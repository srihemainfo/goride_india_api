<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;

class InvitationController extends Controller
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
    
    public function getFcm($id = null, $loc = null, $us_id = null, $cab_seat = null)
    {
        $query = DB::table('customer_register')
            ->where('deletes', '0')
            ->whereNotNull('fcm_token');

        if (!empty($id)) {
    
            $ids = is_array($id) ? array_filter($id) : [$id];
    
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
    
        } else {
    
            $excludeId = $us_id ?? optional(auth()->user())->id;
    
            if (!empty($excludeId)) {
                $query->where('id', '!=', $excludeId);
            }
        }
    
        if (!empty($loc)) {
            $query->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(prefered_location, '$.location')) LIKE ?",
                ["%{$loc}%"]
            );
        }
    
        if ($cab_seat !== null) {
            $query->whereRaw(
                "
                CAST(
                    COALESCE(
                        JSON_UNQUOTE(JSON_EXTRACT(vehicle_details, '$.type')),
                        '0'
                    ) AS UNSIGNED
                ) >= ?
                ",
                [(int) $cab_seat]
            );
        }
    
        return $query->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
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
        
        if($data['invite_token'] == 'job_invitation'){
            
            $stringData['actions'] = json_encode([
                [
                    'id' => 'accept',
                    'title' => 'Accept'
                ],
                [
                    'id' => 'reject',
                    'title' => 'Not Interested'
                ]
            ]);
            
        }
        
        
        $stringData['invite_token'] = $data['invite_token'] ?? '';
    
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
    
    public function sync(Request $request)
    {
        try {
            $request->validate([
                'contacts' => 'required|array'
            ]);

            $userId = auth()->id();
            $data = [];

            foreach ($request->contacts as $contact) {
                $phone = preg_replace('/\D/', '', $contact['phone']);
                $hash = hash('sha256', $phone);

                $data[] = [
                    'user_id' => $userId,
                    'contact_name' => $contact['name'] ?? null,
                    'phone' => $phone,
                    'phone_hash' => $hash,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            DB::table('user_contacts')->insertOrIgnore($data);

            // Match existing users
            DB::statement("
                UPDATE user_contacts uc
                JOIN customer_register u ON uc.phone_hash = SHA2(u.mobile, 256)
                SET uc.is_app_user = 1,
                    uc.app_user_id = u.id
                WHERE uc.user_id = ?
            ", [$userId]);

            return ApiResponse::success('Contacts synced');

        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function appUsers()
    {
        try {
            $userId = auth()->id();

            $users = DB::table('user_contacts as uc')
                ->join('customer_register as u', 'uc.app_user_id', '=', 'u.id')
                ->where('uc.user_id', $userId)
                ->select('u.id', 'u.name', 'u.mobile')
                ->get();

            return ApiResponse::success('App users fetched', $users);

        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function send(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $userId = auth()->id();
    
            $request->validate([
                'type' => 'required|in:app,job',
                'invitee_user_id' => 'nullable|exists:customer_register,id',
                'phone' => 'nullable',
                'job_id' => 'required_if:type,job'
            ]);
    
            $phoneHash = null;
            if ($request->phone) {
                $phone = preg_replace('/\D/', '', $request->phone);
                $phoneHash = hash('sha256', $phone);
            }
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->where('deletes', '0')
                ->first();
    
            if (!$job) {
                return ApiResponse::error('Invalid job or unauthorized access', 403);
            }
    
            $lastInvite = DB::table('invitations')
                ->where('inviter_id', $userId)
                ->where('job_id', $request->job_id)
                ->where(function ($q) use ($request, $phoneHash) {
                    $q->where('invitee_user_id', $request->invitee_user_id)
                      ->orWhere('invitee_phone_hash', $phoneHash);
                })
                ->where('status', 'pending')
                ->latest()
                ->first();
    
            if ($lastInvite && $lastInvite->created_at >= now()->subSeconds(30)) {
                $secondsLeft = 30 - now()->diffInSeconds($lastInvite->created_at);
    
                return ApiResponse::error("Please wait {$secondsLeft} seconds before sending again", 200);
            }
            
            $inviteToken = Str::random(40);
            
            $checkExists = DB::table('invitations')
                ->where('job_id', $request->job_id)
                ->where('invitee_user_id', $request->invitee_user_id)
                ->where('status', 'accepted')->exists();
                
            if($checkExists){
                return ApiResponse::error('Already accepted for the ride');
            }
    
            $inviteId = DB::table('invitations')->insertGetId([
                'inviter_id' => $userId,
                'invitee_user_id' => $request->invitee_user_id,
                'invitee_phone_hash' => $phoneHash,
                'type' => $request->type,
                'job_id' => $request->job_id,
                'invite_token' => $inviteToken,
                'created_at' => now(),
                'updated_at' => now()
            ]);
    
            if ($request->invitee_user_id) {
    
                $customer = DB::table('customer_register')
                    ->where('id', $request->invitee_user_id)
                    ->first();
    
                $inviter = DB::table('customer_register')
                    ->where('id', $userId)
                    ->first();
    
                $cusIds = [$request->invitee_user_id];
    
                $fcmTokens = $this->getFcm($cusIds, null, null, null);
    
                if (!empty($fcmTokens)) {
    
                    $accessToken = $this->getAccessToken();
    
                    if ($accessToken) {
    
                        $title = "📍 Ride to {$job->from_place}";
                        $body = "{$inviter->name} invited you to join a ride to {$job->from_place}";
    
                        foreach ($fcmTokens as $token) {
    
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                $title,
                                $body,
                                [
                                    'job_id' => (string)$job->id,
                                    'type'   => 'job_invitation',
                                    'action' => 'job_invitation',
                                    'invite_token' => $inviteToken
                                ]
                            );
                        }
                    }
                }
            }
    
            DB::commit();
    
            return ApiResponse::success('Invitation sent successfully', [
                'invite_id' => $inviteId
            ]);
    
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function accept(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'invite_token' => 'required'
            ]);

            $userId = auth()->id();

            $invite = DB::table('invitations')
                ->where('invite_token', $request->invite_token)
                ->where('invitee_user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$invite || $invite->status != 'pending') {
                DB::commit();
                return ApiResponse::error('Invalid invite');
            }
            
            $job = DB::table('cus_job_temp')
                ->where('id', $invite->job_id)
                ->where('global_type', 'carpool')
                ->lockForUpdate() 
                ->first();
                
            if (!$job) {
                DB::commit();
                return ApiResponse::error('Job not found');
            }
                
            if ($job->filled_seat < $job->pass_count) {
                
                DB::table('cus_job_temp')
                    ->where('id', $invite->job_id)
                    ->increment('filled_seat', 1);
                    
            }else{
                DB::commit();
                return ApiResponse::error('All seats are occupied. Please try another job.');
            }

            DB::table('invitations')
                ->where('id', $invite->id)
                // ->where('invitee_user_id', $userId)
                ->update([
                    'status' => 'accepted',
                    'accepted_at' => now()
                ]);

            // Add friends (bidirectional)
            DB::table('friends')->insertOrIgnore([
                [
                    'user_id' => $invite->inviter_id,
                    'friend_id' => $invite->invitee_user_id,
                    'source' => 'invite',
                    'invitation_id' => $invite->id,
                    'created_at' => now()
                ],
                [
                    'user_id' => $invite->invitee_user_id,
                    'friend_id' => $invite->inviter_id,
                    'source' => 'invite',
                    'invitation_id' => $invite->id,
                    'created_at' => now()
                ]
            ]);

            DB::commit();

            return ApiResponse::success('Invitation accepted');

        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function inviteList()
    {
        try {
            $userId = auth()->id();

            $invites = DB::table('invitations')
                ->where('invitee_user_id', $userId)
                ->where('status', 'pending')
                ->get();

            return ApiResponse::success('Invitations list', $invites);

        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function friendsList()
    {
        try {
            $userId = auth()->id();

            $friends = DB::table('friends as f')
                ->join('customer_register as u', 'f.friend_id', '=', 'u.id')
                ->where('f.user_id', $userId)
                ->select('u.id', 'u.name', 'u.mobile')
                ->get();

            return ApiResponse::success('Friends list', $friends);

        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}