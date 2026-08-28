<?php

namespace App\Http\Controllers\Api\v5;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Agora\RtcTokenBuilder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BidPlacedToRedis;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Support\Facades\Redis;
use App\Services\NotificationService;
use App\Services\PusherService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ApiResponse;

class CustomerCallController extends Controller
{
    
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;
    public $razorpay;
    
    public function __construct()
    {
        
        $this->razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
        
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
        
        $this->serviceAccountPath2 = storage_path('app/firebase/firebase-config-customer-schedule.json');
    
        if (!file_exists($this->serviceAccountPath)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }
        
        if (!file_exists($this->serviceAccountPath2)) {
            response()->json([
                'status'  => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }
        
    
        $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        $this->serviceAccount2 = json_decode(file_get_contents($this->serviceAccountPath2), true);
    }
    
    public function encryptJobId($jobId)
    {
        return Crypt::encryptString((string)$jobId);
    }
    
    public function getAccessToken()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));
    
        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];
    
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";
    
        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount['private_key']),
            OPENSSL_ALGO_SHA256
        );
    
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Request access token
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);
    
        $ch = curl_init($this->serviceAccount['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }
    
    public function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        // Ensure all data values are strings
        $stringData = [];
        foreach ($data as $key => $value) {
            $validKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
            $stringData[$validKey] = (string) $value;
        }
        
        $stringData['title'] = $title;
        $stringData['body'] = $body;
        
        $stringData['actions'] = json_encode([
            [
                'id' => 'accept',
                'title' => 'Accept'
            ],
            [
                'id' => 'decline',
                'title' => 'Decline'
            ]
        ]);
    
        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->serviceAccount['project_id'] . '/messages:send';
    
        $payload = [
            'validate_only' => false,
            'message' => [
                'token' => $fcmToken,
                'notification' => [ // 👈 required for mobile push
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '86400s',
                    'notification' => [
                        'channel_id' => 'new_job_channel',
                        'sound' => 'custom_notification',
                        'color' => '#FF6B35',
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'sound' => 'custom_notification.wav',
                            'badge' => 1
                        ]
                    ]
                ],
                'data' => $stringData
                
            ]
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        curl_close($ch);
    
        return json_decode($result, true);
    }
    
    private function getReceiverFcmToken($userId)
    {
        return DB::table('customer_register')
            ->where('id', $userId)
            ->value('fcm_token');
    }
    
    public function startCall(Request $request)
    {
        try {
            
            $request->validate([
                'receiver_id' => 'required|exists:customer_register,id',
                'job_id' => 'required'
            ]);
        
            $callerId = auth()->id();
            $receiverId = $request->receiver_id;
            $job_id = $request->job_id;
            
            $job = DB::table('cus_job_temp')->where(['id' => $request->job_id])->whereNot('job_status', 'cancelled')->first();
            
            if(!$job){
                return ApiResponse::error('Job Not found');
            }
            
            $job_no = $request->job_no;
        
            $channelName = 'call_' . $callerId . '_' . $receiverId . '_' . time();
        
            $call = DB::table('carpool_calls')->insertGetId([
                'channel_name' => $channelName,
                'job_id' => $job_id,
                'job_no' => $job_no,
                'caller_id' => $callerId,
                'receiver_id' => $receiverId,
                'status' => 'calling',
                'created_at' => now(),
            ]);
        
            $tokenData = app(\App\Http\Controllers\Api\v5\AgoraController::class)
                ->generateToken(new Request(['channel_name' => $channelName]))
                ->getData(true);
        
            $receiverToken = $this->getReceiverFcmToken($receiverId);
            
            if (!empty($receiverToken)) {
            
                $accessToken = $this->getAccessToken();
            
                if ($accessToken) {
            
                    $caller = auth()->user();
            
                    $title = "📞 Incoming Call";
                    $body  = "{$caller->name} is calling you";
            
                    $this->sendFCM(
                        $accessToken,
                        $receiverToken,
                        $title,
                        $body,
                        [
                            'type' => 'incoming_call',
                            'call_id' => (string)$call,
                            'channel_name' => $channelName,
                            'caller_id' => (string)$callerId,
                            'caller_name' => (string)$caller->name,
                            'job_id' => (string)$job_id,
                            'job_no' => (string)$job_no,
                            'action' => 'carpool_call'
                        ]
                    );
                }
            }
            
            DB::table('carpool_calls')->where('id', $call)->update([
                'token' =>  $tokenData['data']['token'],
                'app_id' => $tokenData['data']['app_id']
            ]);
            
            $data = [
                'call_id' => $call,
                'channel_name' => $channelName,
                'token' => $tokenData['data']['token'],
                'app_id' => $tokenData['data']['app_id'],
            ];
            
            return ApiResponse::success('Call starts', $data);
        
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function acceptCall(Request $request)
    {
        try{
            
            $request->validate([
                'call_id' => 'required|exists:carpool_calls,id',
            ]);
        
            $call = DB::table('carpool_calls')->where('id', $request->call_id)->first();
        
            if (!$call) {
                // return response()->json(['status' => false], 404);
                return ApiResponse::error('Failed to accept call');
            }
        
            DB::table('carpool_calls')->where('id', $call->id)->update([
                'status' => 'ongoing',
                'started_at' => now(),
            ]);
            
            $data = [
                'call_id' => $request->call_id,
                'channel_name' => $call->channel_name,
                'token' => $call->token,
                'app_id' => $call->app_id
            ];
        
            // return response()->json(['status' => true]);
            
            return ApiResponse::success('Call accepted', $data);
        
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Validation failed.', $e->getMessage());
        }
    }
    
    public function endCall(Request $request)
    {
        try{
                
            $request->validate([
                'call_id' => 'required|exists:carpool_calls,id',
            ]);
        
            $call = DB::table('carpool_calls')->where('id', $request->call_id)->first();
        
            if (!$call) {
                // return response()->json(['status' => false], 404);
                return ApiResponse::error('Failed to end call');
            }
        
            DB::table('carpool_calls')->where('id', $call->id)->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
    
            return ApiResponse::success('Call ended', []);
        
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Validation failed.', $e->getMessage());
        }
    }
    
    public function callHistory()
    {
        try{
            
            $userId = auth()->id();
                
            $calls = DB::table('carpool_calls')
                ->where('caller_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('id', 'desc')
                ->get();
    
            return ApiResponse::success('Call history', $calls);
        
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Validation failed.', $e->getMessage());
        }
        
    }
    
    public function callStatus(Request $request)
    {
        try{
            $request->validate([
                'call_id' => 'required|exists:carpool_calls,id',
            ]);
            
            $userId = auth()->id();
                
            $call = DB::table('carpool_calls')->where('id', $request->call_id)->first();
    
            return ApiResponse::success('Call status', $call);
        
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Validation failed.', $e->getMessage());
        }
    }
    
}