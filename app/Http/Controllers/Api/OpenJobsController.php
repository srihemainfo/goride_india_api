<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Jobs\BidPlacedToRedis;
use Illuminate\Support\Facades\Redis;
use App\Services\NotificationService;
use App\Services\PusherService;
use App\Services\FirebaseJobService;
use App\Services\GeohashService;
use Illuminate\Support\Facades\Cache;

class OpenJobsController extends Controller
{
    
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;
    
    public function __construct()
    {
        // Correct path to storage/app/firebase/firebase-config.json
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
    public function admin_confirm_grd_job(Request $request)
    {
        try {
            $request->validate([
                'job_id' => 'required',
                'job_no' => 'required'
            ]);

            $job_no = $request->job_no;

            // 1. UPDATE MYSQL to 1 (Confirmed)
            DB::table('cus_job_temp')
                ->where('job_no', $job_no)
                ->update(['confirm_status' => 1, 'updated_at' => now()]);

            DB::table('open_jobs')
                ->where('job_no', $job_no)
                ->update(['confirm_status' => 1, 'updated_at' => now()]);

            // 2. UPDATE FIREBASE DYNAMICALLY to 1 (Confirmed)
            $accessToken = $this->getAccessToken();
            
            if ($accessToken) {
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $accessToken
                );
                
                // Uses the method we added earlier to set confirm_status to 1
                $firebase->updateConfirmStatus($job_no, 1); 
            }

            return response()->json([
                'status' => true,
                'message' => 'Job Confirmed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function normalizeSearch($text)
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $text));
    }
    
    private function verifyRecaptcha($token, $action)
    {
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => env('BOOKING_RECAPTCHA_SECRET_KEY'),
                'response' => $token,
                'remoteip' => request()->ip(),
            ]
        )->json();
    
        if (
            empty($response['success']) ||
            $response['action'] !== $action ||
            $response['score'] < 0.5
        ) {
            return false;
        }
    
        return true;
    }
    
    function formatReadableDate($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->format('d M Y, h:i A');
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
    
    public function getAccessToken2()
    {
        $header = base64_encode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]));
    
        $now = time();
        $claimSet = [
            'iss'   => $this->serviceAccount2['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud'   => $this->serviceAccount2['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600
        ];
    
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput  = "$header.$claimSetEncoded";
    
        openssl_sign(
            $signatureInput,
            $signature,
            openssl_pkey_get_private($this->serviceAccount2['private_key']),
            OPENSSL_ALGO_SHA256
        );
    
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
        // Request access token
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]);
    
        $ch = curl_init($this->serviceAccount2['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }
    
    public function getFcm($id = null, $loc = null)
    {
        
        if ($id) {
            $get_tokens = DB::table('user_register')
                    ->whereIn('id', $id)
                    ->where('deletes', '0')
                    ->when($loc, function($query) use ($loc) {
                            $query->where(function($q) use ($loc) {
                                $q->whereNull('prefered_location')
                                  ->orWhere('prefered_location->location', '')
                                  ->orWhere('prefered_location->location', 'LIKE', '%' . $loc . '%');
                            });
                        })
                    ->where('notify', 1)->get();
                    
            $tokens = [];
            
            foreach ($get_tokens as $user) {
                if (!empty($user->fcm_token)) {
                    $tokens[] = $user->fcm_token;
                }
                if (!empty($user->browser_fcm_token)) {
                    $tokens[] = $user->browser_fcm_token;
                }
            }
    
            // if ($get_token) {
            //     if (!empty($get_token->fcm_token)) {
            //         $tokens[] = $get_token->fcm_token;
            //     }
            //     if (!empty($get_token->browser_fcm_token)) {
            //         $tokens[] = $get_token->browser_fcm_token;
            //     }
            // }
    
            return $tokens;
    
        } else {
            
            $get_tokens = DB::table('user_register')
                ->whereNotNull('fcm_token')
                ->where('deletes', '0')
                ->where('notify', 1)
                // ->orWhereNotNull('browser_fcm_token')
                ->where('id', '!=', auth()->user()->id)
                ->when($loc, function($query) use ($loc) {
                    $query->where(function($q) use ($loc) {
                        $q->whereNull('prefered_location')
                          ->orWhere('prefered_location->location', '')
                          ->orWhere('prefered_location->location', 'LIKE', '%' . $loc . '%');
                    });
                })
                ->select('fcm_token')
                ->get(); // fetch results
    
            $tokens = [];
            foreach ($get_tokens as $user) {
                if (!empty($user->fcm_token)) {
                    $tokens[] = $user->fcm_token;
                }
                // if (!empty($user->browser_fcm_token)) {
                //     $tokens[] = $user->browser_fcm_token;
                // }
            }
    
            return $tokens;
        }
    }
    
    private function createFirebaseJobOld(string $jobNo, array $data)
    {
        $accessToken = $this->getAccessToken();
    
        if (!$accessToken) {
            throw new \Exception('Firebase access token failed');
        }
    
        $fbCol = env('FIREBASE_COLLECTION', 'jobs');
    
        $url = "https://firestore.googleapis.com/v1/projects/{$this->serviceAccount['project_id']}/databases/(default)/documents/{$fbCol}/{$jobNo}";
    
        $payload = [
            'fields' => [
    
                'id' => ['integerValue' => (string) $data['id']],
                'job_no' => ['stringValue' => (string) $jobNo],
    
                'from_place' => ['stringValue' => (string) $data['from_place']],
                'to_place'   => ['stringValue' => (string) $data['to_place']],
    
                'pickup_date' => [
                    'timestampValue' => date('c', strtotime($data['pickup_date']))
                ],
    
                'pass_count' => [
                    'integerValue' => (string) (int) $data['pass_count']
                ],
    
                'fare' => [
                    'integerValue' => (string) (int) $data['fare']
                ],
    
                'job_type' => [
                    'stringValue' => (string) $data['job_type']
                ],
    
                'distance' => [
                    'integerValue' => (string) (int) $data['distance']
                ],
    
                // ✅ FIX: duration must never be null
                'duration' => [
                    'stringValue' => (string) ($data['duration'] ?? '')
                ],
    
                'global_type' => [
                    'stringValue' => (string) ($data['global_type'] ?? 'customer') 
                ],
    
                'job_remark' => [
                    'stringValue' => (string) ($data['job_remark'] ?? 'No remark')
                ],
    
                'job_status' => [
                    'stringValue' => 'created'
                ],
                'poster_name' => [
                    'stringValue' => (string) $data['poster_name']
                ],
    
                'add_fare_details' => [
                    'mapValue' => [
                        'fields' => [
                            'bata' => ['stringValue' => 'Included'],
                            'toll' => ['stringValue' => 'Included'],
                            'parking' => ['stringValue' => 'Included'],
                        ]
                    ]
                ],
    
                'user_id' => [
                    'integerValue' => (string) (int) $data['user_id']
                ],
    
                'created_at' => [
                    'timestampValue' => now()->toIso8601String()
                ],
    
                'expires_at' => [
                    'timestampValue' => now()->addMinutes(30)->toIso8601String()
                ],
            ]
        ];
    
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->patch($url, $payload);
    
        if ($response->failed()) {
            throw new \Exception(
                'Firebase job creation failed: ' . $response->body()
            );
        }
    
        return $response->json();
    }
    
    private function createFirebaseJob(string $jobNo, array $data)
    {
        $accessToken = $this->getAccessToken();
    
        if (!$accessToken) {
            throw new \Exception('Firebase access token failed');
        }
    
        $fbCol = env('FIREBASE_COLLECTION', 'jobs');
    
        $url = "https://firestore.googleapis.com/v1/projects/{$this->serviceAccount['project_id']}/databases/(default)/documents/{$fbCol}/{$jobNo}";
        
        $add_details = json_decode($data['add_fare_details'], true)??[];
    
        $fields = [
    
                'id' => ['integerValue' => (string) $data['id']],
                'job_no' => ['stringValue' => (string) $jobNo],
                'job_type' => [
                    'stringValue' => (string) $data['job_type']
                ],
    
                'from_place' => ['stringValue' => (string) $data['from_place'] ],
                'from_place_id' => ['stringValue' => ((string) $data['from_place_id']??'') ],
                'to_place'   => ['stringValue' => (string) $data['to_place']],
                'to_place_id'   => ['stringValue' => ((string) $data['to_place_id'] ?? '') ],
    
                'pickup_date' => [
                    'timestampValue' => date('c', strtotime($data['pickup_date']))
                ],
                
                'day' => [
                    'stringValue' => (string) trim(str_ireplace(['days', 'day'], '', $data['dropoff_date'] ?? ''))
                ],
    
                'pass_count' => [
                    'integerValue' => (string) (int) $data['pass_count'] 
                ],
    
    
                'distance' => [
                    'integerValue' => (string) (int) $data['distance']
                ],
    
                'duration' => [
                    'stringValue' => (string) ($data['duration'] ?? '')
                ],
    
                'global_type' => [
                    'stringValue' => (string) ($data['global_type'] ?? 'customer')
                ],
    
                'job_remark' => [
                    'stringValue' => (string) ($data['job_remark'] ?? 'No remark')
                ],
    
                'job_status' => [
                    'stringValue' => 'created'
                ],
                'poster_name' => [
                    'stringValue' => (string) ($data['poster_name'] ?? '')
                ],
                'pick_lat' => [
                    'stringValue' => (string) ($data['pick_lat']??'')
                ],
                'pick_lan' => [
                    'stringValue' => (string) ($data['pick_lan']??'')
                ],
                'drop_lat' => [
                    'stringValue' => (string) ($data['drop_lat']??'')
                ],
                'drop_lan' => [
                    'stringValue' => (string) ($data['drop_lan']??'')
                ],
    
                'add_fare_details' => [
                    'mapValue' => [
                        'fields' => [
                            'bata' => ['stringValue' => $add_details['bata']],
                            'toll' => ['stringValue' => $add_details['toll']],
                            'parking' => ['stringValue' => $add_details['parking']],
                        ]
                    ]
                ],
    
                'user_id' => [
                    'integerValue' => (string) (int) $data['user_id']
                ],
                
                'base_fare' => [
                    
                    'stringValue' => (string) ($data['base_fare'] ?? '')
                ],
                
                'toll_fare' => [
                    
                    'stringValue' => (string) ($data['toll_fare'] ?? '')
                ],
                'com' => [
                    
                    'stringValue' => (string) ($data['com'] ?? '')
                ],
                'tax' => [
                    
                    'stringValue' =>  (string) ($data['tax'] ?? '')
                ],
                'discount' => [
                    
                    'stringValue' =>  (string) ($data['discount'] ?? '')
                ],
                'isDiscount' => [
                    
                    'stringValue' =>  (string) ($data['isDiscount'] ?? '')
                ],
                'fare' => [
                    'stringValue' =>  (string) ($data['fare'] ?? '')
                ],
                
                'pick_address' => [
                    
                    'stringValue' =>  (string) ($data['pick_address'] ?? '')
                ],
                'drop_address' => [
                    
                    'stringValue' =>  (string) ($data['drop_address'] ?? '')
                ],
                'user_details' => [
                    
                    'stringValue' =>  (string) ($data['user_details'] ?? '')
                ],
                'from_to_co' => [
                    
                    'stringValue' => (string) ($data['from_to_co'] ?? '')
                ],
    
                'created_at' => [
                    'timestampValue' => now()->toIso8601String()
                ],
                
    
                'expires_at' => [
                    'timestampValue' => now()->addMinutes(30)->toIso8601String()
                ]
                
                
                
                // 'without_tax' => [
                    
                //     'stringValue' => (string) $data['without_tax']
                // ],
                // 'credit' => [
                    
                //     'stringValue' => (string) $data['credit']
                // ],
        ];
        
        // if (!empty($data['dropoff_date'])) {
        //     $fields['dropoff_date'] = [
        //         'stringValue' => date('Y-m-d', strtotime($data['dropoff_date']))
        //     ];
        // }
        if (!empty($data['dropoff_date'])) {
            $fields['dropoff_date'] = [
                'stringValue' => (string) $data['dropoff_date']
            ];
        }
        
        $payload = [
            'fields' => $fields
        ];
    
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->patch($url, $payload);
    
        if ($response->failed()) {
            throw new \Exception(
                'Firebase job creation failed: ' . $response->body()
            );
        }
    
        return $response->json();
    }
    
    private function normalizeFirebaseJob(array $doc): array
    {
        $f = $doc['fields'];
    
        return [
            'job_no'        => $f['job_no']['stringValue'] ?? null,
            'from_place'    => $f['from_place']['stringValue'] ?? '',
            'to_place'      => $f['to_place']['stringValue'] ?? '',
            'fare'          => (int) ($f['fare']['integerValue'] ?? 0),
            'distance'      => (int) ($f['distance']['integerValue'] ?? 0),
            'pass_count'    => (int) ($f['pass_count']['integerValue'] ?? 0),
            'pickup_date'   => $f['pickup_date']['timestampValue'] ?? null,
            'job_status'    => $f['job_status']['stringValue'] ?? '',
            'source'        => 'firebase'
        ];
    }
    
    private function parseFirestoreFields(array $fields): array
    {
        $result = [];
    
        foreach ($fields as $key => $value) {
    
            if (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            }
            elseif (isset($value['integerValue'])) {
                $result[$key] = (int) $value['integerValue'];
            }
            elseif (isset($value['doubleValue'])) {
                $result[$key] = (float) $value['doubleValue'];
            }
            elseif (isset($value['booleanValue'])) {
                $result[$key] = (bool) $value['booleanValue'];
            }
            elseif (isset($value['timestampValue'])) {
                $result[$key] = \Carbon\Carbon::parse(
                    $value['timestampValue'],
                    'UTC'
                )->setTimezone(config('app.timezone'))->toDateTimeString();
            }

            elseif (isset($value['mapValue']['fields'])) {
                // Recursive parse for maps (like bids)
                $result[$key] = $this->parseFirestoreFields(
                    $value['mapValue']['fields']
                );
            }
            elseif (isset($value['arrayValue']['values'])) {
                $result[$key] = array_map(function ($v) {
                    return $this->parseFirestoreFields([$v])[0] ?? null;
                }, $value['arrayValue']['values']);
            }
            else {
                $result[$key] = null;
            }
        }
    
        return $result;
    }
    
    public function GoogleDistrict_all(Request $request)
    {
        try {

            $countryCode = $request->countryCode??'IN';
            
            $googleData = DB::table('districts')
            ->where('deletes', 0)
            ->select(DB::raw("CONCAT(district_name, ', ', state) as full_name"))
            ->pluck('full_name')
            ->toArray();


            return response()->json(['status' => 200, 'data' => $googleData, 'message' => 'Location Retrieved Successfully']);

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
    
    public function job_action(Request $request)
    {
        try {
    
            // Basic validation
            if (!$request->job_id || !$request->user_id || !$request->action) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid request'
                ], 422);
            }
    
            $userId = $request->user_id;
            $jobId  = $request->job_id;
            $action = $request->action;
            $loc    = null;
    
            DB::beginTransaction();
    
            // DELETE JOB
            if ($action === 'delete') {
    
                DB::table('open_jobs')
                    ->where('id', $jobId)
                    ->update(['delete' => 1]);
    
                DB::commit();
    
                return response()->json([
                    'status'  => true,
                    'message' => 'Job Deleted'
                ]);
            }
    
            // CONFIRM JOB
            DB::table('open_jobs')
                ->where('id', $jobId)
                ->update(['confirm_status' => 1]);
    
            $jb = DB::table('open_jobs')
                ->where('id', $jobId)
                ->where('confirm_status', 1)
                ->first();
    
            if (!$jb) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found'
                ], 404);
            }
    
            // Extract location from from_place
            $parts = array_map('trim', explode(',', $jb->from_place));
            $count = count($parts);
            $loc   = ($count >= 2) ? $parts[$count - 2] : $parts[0];
            
            $loc = null;
    
            // Fetch FCM tokens
            $tokens = DB::table('user_register')
                ->whereNotNull('fcm_token')
                ->where('deletes', '0')
                ->where('notify', 1)
                ->where('id', '!=', $userId)
                ->when($loc, function ($query) use ($loc) {
                    $query->where(function ($q) use ($loc) {
                        $q->whereNull('prefered_location')
                          ->orWhere('prefered_location->location', '')
                          ->orWhere('prefered_location->location', 'LIKE', '%' . $loc . '%');
                    });
                })
                ->pluck('fcm_token')
                ->toArray();
    
            DB::commit();
            
            // return $tokens;
    
            // Send notifications OUTSIDE transaction
            if (!empty($tokens)) {
                $accessToken = $this->getAccessToken();
    
                if ($accessToken) {
                    foreach ($tokens as $token) {
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            'New Job Arrived!',
                            'A new job is available from ' . $jb->from_place . '. Open the app to place your bid.',
                            [
                                'caller' => $userId,
                                'type'   => 'new_job_notification',
                                'id'     => $jobId,
                                'action' => 'agree_popup',
                                'url'    => env('APP_URL') . 'jobs',
                                'pickup' => null,
                            ]
                        );
                    }
                }
            }
    
            return response()->json([
                'status'  => true,
                'message' => 'Job Activated'
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            \Log::error('Job Action Failed', [
                'error' => $e->getMessage(),
                'job_id' => $request->job_id ?? null,
                'user_id' => $request->user_id ?? null,
            ]);
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
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
                    ],
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
    
    public function admin_pushNotify(Request $request){
        
        
        $fcmToken = $this->getFcm([$request->user_id]);

        // return $fcmToken;
        if ($fcmToken) {
            $accessToken = $this->getAccessToken();
            if ($accessToken) {
                foreach($fcmToken as $token){
                    $responses = $this->sendFCM(
                        $accessToken,
                        $token,
                        $request->title,
                        $request->message,
                        [
                            'caller' => $request->user_id,
                            'type'     => $request->action,
                            'action'     => $request->action
                        ]
                        
                    );
                    
                }
                return $responses;
            }
        }
    }
    
    public function send_template(Request $request){
        
        $user_id = $request->user_id;
        $name = $request->name;
        $mobile = $request->mobile;
        
        if($request->method == 'push_notification'){
            
            $fcmToken = $this->getFcm([$user_id]);

            if ($fcmToken) {
                $accessToken = $this->getAccessToken();
                if ($accessToken) {
                    foreach($fcmToken as $token){
                        $responses = $this->sendFCM(
                            $accessToken,
                            $token,
                            $request->title,
                            $request->message_push,
                            [
                                'caller' => $user_id,
                                'type'     => $request->action,
                                'action'     => $request->action
                            ]
                            
                        );
                        
                    }
                    return $responses;
                }
            }
            
        }elseif($request->method == 'sms'){
           
            $messages = $request->message;
            
            $whatsAppArr = [
                'mobile' => $mobile,
                'templateName' => 'national_draw_verification',
                'language' => 'en',
                'templateBodyParam' => [],
                'messages' => $messages,
                'resend' => false
            ];
            $responseeee = Controller::smsNotification($whatsAppArr, 'kyc_verified');
            
            return $responseeee;
        }
    }
    
    function extractDistrict($addressComponents) {
        foreach ($addressComponents as $component) {
            if (in_array('administrative_area_level_3', $component['types'])) {
                return $component['long_name']; // District
            }
        }
        foreach ($addressComponents as $component) {
            if (in_array('sublocality_level_1', $component['types'])) {
                return $component['long_name']; // Fallback to borough/neighborhood
            }
        }
        return null; // If nothing found
    }
    
    public function GoogleLocations(Request $request)
    {
        try {
            $search = trim($request->search);
    
            $cities = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                // ->whereRaw("name NOT LIKE '%,%'")
                ->limit(10)
                ->get();
    
            if ($cities->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'data' => [],
                    'message' => 'No locations found'
                ]);
            }
    
            $results = [];
            $updates = [];
    
            foreach ($cities as $city) {
                $cityName = $city->name;
    
                $new_req = new Request([
                    'search' => $cityName,
                    'countryCode' => 'IN'
                ]);
                if(str_word_count($cityName) == 1 && strpos($cityName, ',') == false){
                    
                    $districtApi = $this->GoogleDistrict($new_req)->getData(true);
        
                    if (!empty($districtApi['data'])) {
                        $districtName = $districtApi['data'][0] ?? null;
                        $stateName = "Tamil Nadu";
        
                        if ($districtName) {
                            if($cityName == $districtName){
                                $newName = "{$districtName}, {$stateName}";    
                                
                            }else{
                                $newName = "{$cityName}, {$districtName}, {$stateName}";
                                
                            }
        
                            $updates[] = [
                                'id' => $city->id,
                                'name' => $newName
                            ];
        
                            $city->name = $newName;
                        }
                    }
                    
                }
    
                $results[] = $city;
            }
            
            usort($results, function ($a, $b) {
                $aCommas = substr_count($a->name, ',');
                $bCommas = substr_count($b->name, ',');
            
                // Fewer commas first
                return $aCommas <=> $bCommas;
            });

    
            // ✅ Bulk DB updates
            foreach ($updates as $update) {
                DB::table('cities')
                    ->where('id', $update['id'])
                    ->update(['name' => $update['name']]);
            }
    
            return response()->json([
                'status' => 200,
                'data' => $results,
                'message' => 'Location Retrieved Successfully'
            ]);
    
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function GoogleLocationsDriverNew(Request $request)
    {
        try {
    
            $search = trim($request->search);
    
            if (strlen($search) < 2) {
                return response()->json([
                    'status' => true,
                    'data'   => []
                ]);
            }
    
            $limit     = 3;
            $searchKey = $this->normalizeSearch($search);
    
            $dbResults = DB::table('outstation_locations')
                ->where('search_key', 'LIKE', $searchKey . '%')
                ->where('state', 'Tamil Nadu')
                ->limit($limit)
                ->get([
                    'display_name as name',
                    'place_id',
                    'latitude',
                    'longitude'
                ])
                ->toArray();
    
            if (count($dbResults) >= $limit) {
                return response()->json([
                    'status' => 200,
                    'data'   => $dbResults
                ]);
            }
    
            $google = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                [
                    'input'      => $search,
                    'key'        => env('GOOGLE_KEY'),
                    'components' => 'country:in',
                    'types'      => 'geocode'
                    //  'types'        => 'geocode',
                     
                ]
            )->json();
    
            if (($google['status'] ?? '') !== 'OK') {
                return response()->json([
                    'status' => true,
                    'data'   => $dbResults,
                    'message' => 'Location Retrieved Successfully'
                ]);
            }
    
            $newResults = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) + count($dbResults) >= $limit) {
                    break;
                }
    
                $description = $p['description']; 
    
                if (stripos($description, 'Tamil Nadu') === false) {
                    continue;
                }
    
                $mainText = $p['structured_formatting']['main_text'] ?? null;
    
                if (!$mainText) continue;
    
                DB::table('outstation_locations')->updateOrInsert(
                    ['place_id' => $p['place_id']],
                    [
                        'name'         => $mainText,
                        'display_name' => $description,
                        'state'        => 'Tamil Nadu',
                        'country'      => 'India',
                        'search_key'   => $this->normalizeSearch($description),
                        'source'       => 'google',
                        'updated_at'   => now(),
                        'created_at'   => now(),
                    ]
                );
    
                $newResults[] = [
                    'name'      => $description,
                    'place_id' => $p['place_id'],
                    'latitude' => null, 
                    'longitude'=> null
                ];
            }
            
            $merged = collect($dbResults)
            ->merge($newResults)
            ->unique('place_id')
            ->values();
    
            return response()->json([
                'status' => 200,
                'data'   => $merged,
                'message' => 'Location Retrieved Successfully'
                
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function GoogleLocationsAll(Request $request)
    {
        try {
    
            $search = trim($request->search);
    
            if (strlen($search) < 1) {
                return response()->json([
                    'status' => true,
                    'data'   => []
                ]);
            }
    
            $limit     = 5;
            $searchKey = $this->normalizeSearch($search);
    
            $query = DB::table('outstation_locations')
                    ->where('search_key', 'LIKE', $searchKey . '%');
                
                // 🔹 Conditional filter
                if ($request->loc_type == 'from') {
                    $query->whereIn('state', ['Tamil Nadu', 'Puducherry']);
                } else {
                    $query->whereIn('state', [
                        'Tamil Nadu',
                        'Puducherry',
                        'Kerala',
                        'Karnataka',
                        'Andhra Pradesh',
                        'Pondicherry'
                    ]);
                }
                
                $dbResults = $query
                    ->whereRaw("(LENGTH(display_name) - LENGTH(REPLACE(display_name, ',', ''))) >= 2")
                    ->orderByRaw("(LENGTH(display_name) - LENGTH(REPLACE(display_name, ',', ''))) ASC")
                    ->limit(10)
                    ->distinct('place_id')
                    ->get([
                        'display_name as name',
                        'place_id',
                        'latitude',
                        'longitude'
                    ])
                    
                    ->toArray();
    
            if (count($dbResults) >= $limit) {
                return response()->json([
                    'status' => true,
                    'data'   => $dbResults
                ]);
            }
    
            $google = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                [
                    'input'      => $search,
                    'key'        => env('GOOGLE_KEY'),
                    'components' => 'country:in',
                    'types'      => 'geocode'
                ]
            )->json();
    
            if (($google['status'] ?? '') !== 'OK') {
                return response()->json([
                    'status' => true,
                    'data'   => $dbResults
                ]);
            }
    
            $newResults = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) + count($dbResults) >= $limit) {
                    break;
                }
    
                $description = $p['description'];
                
                if($request->loc_type == 'from'){
                    if (
                        stripos($description, 'Tamil Nadu') === false &&
                        stripos($description, 'Puducherry') === false &&
                        stripos($description, 'Pondicherry') === false
                    ) {
                        continue;
                    }
                    
                }elseif($request->loc_type == 'to'){
                    
                    if (
                        stripos($description, 'Tamil Nadu') === false &&
                        stripos($description, 'Kerala') === false &&
                        stripos($description, 'Karnataka') === false &&
                        stripos($description, 'Andhra Pradesh') === false &&
                        stripos($description, 'Puducherry') === false &&
                        stripos($description, 'Pondicherry') === false
                    ) {
                        continue;
                    }
                }
                
    
                // Require minimum 2 commas (City, State, Country)
                if (substr_count($description, ',') < 2) {
                    continue;
                }
    
                $mainText = $p['structured_formatting']['main_text'] ?? null;
    
                if (!$mainText) continue;
                
                $state = 
                (stripos($description, 'Puducherry') !== false || stripos($description, 'Pondicherry') !== false)
                    ? 'Puducherry'
                    : (
                        stripos($description, 'Kerala') !== false
                            ? 'Kerala'
                            : (
                                stripos($description, 'Karnataka') !== false
                                    ? 'Karnataka'
                                    : (
                                        stripos($description, 'Andhra Pradesh') !== false
                                            ? 'Andhra Pradesh'
                                            : 'Tamil Nadu'
                                    )
                            )
                    );
    
                DB::table('outstation_locations')->updateOrInsert(
                    ['place_id' => $p['place_id']],
                    [
                        'name'         => $mainText,
                        'display_name' => $description,
                        'state' => $state,
                        'country'      => 'India',
                        'search_key'   => $this->normalizeSearch($description),
                        'source'       => 'google',
                        'updated_at'   => now(),
                        'created_at'   => now(),
                    ]
                );
    
                $newResults[] = [
                    'name'      => $description,
                    'place_id'  => $p['place_id'],
                    'latitude'  => null,
                    'longitude' => null
                ];
            }
    
            $merged = collect($dbResults)
                ->merge($newResults)
                ->unique('place_id')
                ->when($request->input('d_value'), function ($query, $dValue) {
                    return $query->where('place_id', '!=', $dValue);
                })
                ->values();
    
            return response()->json([
                'status' => true,
                'data'   => $merged
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function AdminGoogleLocationsAll(Request $request)
    {
        try {
    
            // if (!$this->verifyRecaptcha($request->recaptcha_token, 'location_fetch')) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'Bot detected'
            //     ], 403);
            // }
    
            $search = trim($request->search);
    
            if (strlen($search) < 2) {
                return response()->json([
                    'status' => true,
                    'data'   => []
                ]);
            }
    
            $limit     = 3;
            $searchKey = $this->normalizeSearch($search);
    
            $dbResults = DB::table('outstation_locations')
                ->where('search_key', 'LIKE', $searchKey . '%')
                ->where('state', 'Tamil Nadu')
                ->limit($limit)
                ->get([
                    'display_name as name',
                    'place_id',
                    'latitude',
                    'longitude'
                ])
                ->toArray();
    
            if (count($dbResults) >= $limit) {
                return response()->json([
                    'status' => true,
                    'data'   => $dbResults
                ]);
            }
    
            $google = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                [
                    'input'      => $search,
                    'key'        => env('GOOGLE_KEY'),
                    'components' => 'country:in',
                    'types'      => 'geocode'
                    //  'types'        => 'geocode',
                     
                ]
            )->json();
    
            if (($google['status'] ?? '') !== 'OK') {
                return response()->json([
                    'status' => true,
                    'data'   => $dbResults
                ]);
            }
    
            $newResults = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) + count($dbResults) >= $limit) {
                    break;
                }
    
                $description = $p['description']; 
    
                if (stripos($description, 'Tamil Nadu') === false) {
                    continue;
                }
    
                $mainText = $p['structured_formatting']['main_text'] ?? null;
    
                if (!$mainText) continue;
    
                DB::table('outstation_locations')->updateOrInsert(
                    ['place_id' => $p['place_id']],
                    [
                        'name'         => $mainText,
                        'display_name' => $description,
                        'state'        => 'Tamil Nadu',
                        'country'      => 'India',
                        'search_key'   => $this->normalizeSearch($description),
                        'source'       => 'google',
                        'updated_at'   => now(),
                        'created_at'   => now(),
                    ]
                );
    
                $newResults[] = [
                    'name'      => $description,
                    'place_id' => $p['place_id'],
                    'latitude' => null, 
                    'longitude'=> null
                ];
            }
            
            $merged = collect($dbResults)
            ->merge($newResults)
            ->unique('place_id')
            ->values();
    
            return response()->json([
                'status' => true,
                'data'   => $merged
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function GoogleLocations_admin(Request $request)
    {
        try {
            $search = trim($request->search);
            
            if($request->auth_token != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345'){
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Unauthorized.',
                ], 401);
            }
            
            $cities = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->limit(10)
                ->get();
                
            
            if ($cities->isEmpty()) {
                return response()->json([
                    'status' => 200,
                    'data' => [],
                    'message' => 'No locations found'
                ]);
            }
    
            $results = [];
            $updates = [];
    
            foreach ($cities as $city) {
                $cityName = $city->name;
    
                $new_req = new Request([
                    'search' => $cityName,
                    'countryCode' => 'IN'
                ]);
                if(str_word_count($cityName) == 1 && strpos($cityName, ',') == false){
                    
                    $districtApi = $this->GoogleDistrict($new_req)->getData(true);
        
                    if (!empty($districtApi['data'])) {
                        $districtName = $districtApi['data'][0] ?? null;
                        $stateName = "Tamil Nadu";
        
                        if ($districtName) {
                            if($cityName == $districtName){
                                $newName = "{$districtName}, {$stateName}";    
                                
                            }else{
                                $newName = "{$cityName}, {$districtName}, {$stateName}";
                                
                            }
        
                            $updates[] = [
                                'id' => $city->id,
                                'name' => $newName
                            ];
        
                            $city->name = $newName;
                        }
                    }
                    
                }
    
                $results[] = $city;
            }
            
            usort($results, function ($a, $b) {
                $aCommas = substr_count($a->name, ',');
                $bCommas = substr_count($b->name, ',');
            
                return $aCommas <=> $bCommas;
            });

            foreach ($updates as $update) {
                DB::table('cities')
                    ->where('id', $update['id'])
                    ->update(['name' => $update['name']]);
            }
    
            return response()->json([
                'status' => 200,
                'data' => $results,
                'message' => 'Location Retrieved Successfully'
            ]);
    
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function GoogleDistrict(Request $request)
    {
        try {

            $terms = str_replace(" ", "+", $request->search);
            // $apiKey = config('app.google_key'); // Your Google API key

            // $countryCode = DB::connection('mysql')->table('countries')->where('name', $country)->value('iso2');
            $countryCode = $request->countryCode??'IN';
            
            $googleData = DB::table('districts')
            ->where('deletes', 0)
            ->where('district_name', 'LIKE', '%' . $terms . '%')
            ->select(DB::raw("CONCAT(district_name, ', ', state) as full_name"))
            ->pluck('full_name')
            ->toArray();


            return response()->json(['status' => 200, 'data' => $googleData, 'message' => 'Location Retrieved Successfully']);

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
    
    public function DistanceAndDuration(Request $request)
    {
        try {
            // $from_lat = $request->from_lat;
            // $from_lng = $request->from_lng;
            
            $cities_form = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->from)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            $cities_to = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->to)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            if($cities_form){
                $from_lat = $cities_form->latitude;
                $from_lng = $cities_form->longitude;
            }
            if($cities_to){
                $to_lat = $cities_to->latitude;
                $to_lng = $cities_to->longitude;
            }
            
            // $to_lat   = $request->to_lat;
            // $to_lng   = $request->to_lng;
    
            $j_type = $request->way_type; // 'roundtrip' or 'oneway'
            $googleKey = env('DISTANCE_GOOGLE_KEY');
            $data = [];
            if ($request->pass && $request->pass <= 4) {
                $column = 'four_seater';
            } elseif ($request->pass && $request->pass <= 6) {
                $column = 'six_seater';
            } elseif ($request->pass && $request->pass <= 7) {
                $column = 'seven_seater';
            }elseif ($request->pass && $request->pass >= 8 && $request->pass <= 13) {
                $column = 'onethree_seater';
            }elseif ($request->pass && $request->pass >= 13 && $request->pass <= 18) {
                $column = 'oneeight_seater';
            }elseif ($request->pass && $request->pass >= 18 && $request->pass <= 21) {
                $column = 'twoone_seater';
            }elseif ($request->pass && $request->pass >= 21 && $request->pass <= 25) {
                $column = 'twofive_seater';
            }elseif ($request->pass && $request->pass >= 25 && $request->pass <= 50) {
                $column = 'fivezero_seater';
            } elseif (empty($request->pass)) {
                $column = 'mini_four_seater';
            } else {
                $column = 'seven_seater';
            }
            
            if($request->pass != ''){
                
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        'seater' => $column
                    ])->first();
                
                // return $check_data;
                if($check_data && $request->pass != ''){
                    $j_val = ($j_type == 'roundtrip') ? $check_data->return_fare : $check_data->oneway_fare;
                    $data['duration'] = $check_data->duration;
                    $data['distance'] = $check_data->distance;
                    $data['fare'] = $j_val;
                    // return $data;
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Distance, Duration and Fare Retrieved Successfully'
                    ]);
                }
            }
                
            KJHGFDS:
    
            // Format coordinates
            $from_area = $from_lat . ',' . $from_lng;
            $to_area   = $to_lat . ',' . $to_lng;
    
            // Distance Matrix API
            $matrix_response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                'origins' => $from_area,
                'destinations' => $to_area,
                'key' => $googleKey,
            ]);
            $obj = $matrix_response->json();
    
            if (isset($obj['rows'][0]['elements'][0]['status']) && $obj['rows'][0]['elements'][0]['status'] == 'OK') {
                
                $j_val = ($j_type == 'roundtrip') ? 2 : 1;
    
                $data['duration'] = $obj['rows'][0]['elements'][0]['duration']['text'] ?? 'N/A';
                $distance = $obj['rows'][0]['elements'][0]['distance']['text'] ?? 'N/A';
    
                $duration_sec = $obj['rows'][0]['elements'][0]['duration']['value'];
                $distance_meter = $obj['rows'][0]['elements'][0]['distance']['value'];
    
                $distance_km = ceil($distance_meter / 1000) * $j_val;
                $duration_min = ceil($duration_sec / 60) * $j_val;
    
                $distance_number = str_replace(['km', ',', 'mi'], '', $distance);
                $unit = 'km'; // can fetch from DB if needed
    
                if ($unit == 'miles') {
                    $data['distance'] = round($distance_number * 0.621371) * $j_val;
                } else {
                    $data['distance'] = round($distance_number) * $j_val;
                }
    
                // Directions API (for transit fare if available)
                $directions_response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
                    'origin' => $from_area,
                    'destination' => $to_area,
                    'mode' => 'transit',
                    'key' => $googleKey,
                ]);
                $directions_data = $directions_response->json();
    
                // $fare = $directions_data['routes'][0]['fare']['text'] ?? null;
                // if ($fare) {
                //     // $fare = 
                //     $data['fare'] = preg_replace('/[^\d.]/', '', $fare) * $j_val;
                    
                //     // $base_fare = 50;
                //     // $per_km_rate = 10;
                //     // $per_minute_rate = 1.5;
    
                //     // $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     // $approx_fare = round($fare);
    
                //     // $data['fare'] = $approx_fare;
                // } else {
                //     // Custom fare calculation
                //     $base_fare = 50;          // ₹50 base
                //     $per_km_rate = 12;        // ₹10 per km
                //     $per_minute_rate = 1.5;   // ₹1.5 per minute
    
                //     $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     $approx_fare = round($fare);
    
                //     $data['fare'] = $approx_fare;
                // }
                
                // Determine which column to use based on passenger count
                
                // Fetch matching fare row
                $get_fare = DB::table('tariff_fare')
                    ->where('from_km', '<=', (float) $data['distance'])
                    ->where('to_km', '>=', (float) $data['distance'])
                    ->where($column, '!=', 0)
                    ->where('status', '0')
                    ->first();
                
                // Assign fare value if found
                if ($get_fare) {
                    $data['fare'] = $get_fare->{$column} ?? 0;
                }
                
                // return $data['fare'];
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        'seater'   => $column
                    ])->exists();
                
                
                if (!$check_data && $request->pass != '') {
                    
                    $a_fare = $data['fare'] / $j_val;
                
                    $insert_data = [
                        'from'         => $request->from,
                        'to'           => $request->to,
                        'seater'       => $column,
                        'distance'     => $data['distance'],
                        'duration'     => $data['duration'],
                        'oneway_fare'  => $a_fare,
                        'return_fare'  => $a_fare * 2,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                    
                    
                
                    DB::table('location_distance')->insertGetId($insert_data);
                }

    
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Distance, Duration and Fare Retrieved Successfully'
                ]);
            } else {
                
                
                
                $apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVkODk3M2U0YTQ1ZDQyMGFhMjExYTU1YmU2ZGFlZGM3IiwiaCI6Im11cm11cjY0In0=";
                
                $originResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->from,
                ]);
    
                $destResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->to,
                ]);
    
                $originData = $originResponse->json();
                $destData = $destResponse->json();
                
                $originCoords = $originData['features'][0]['geometry']['coordinates'] ?? null;
                $destCoords = $destData['features'][0]['geometry']['coordinates'] ?? null;
    
                if (!$originCoords || !$destCoords) {
                    return response()->json(['status' => false, 'message' => 'Coordinates not found'], 400);
                }
                
                $from_lat = $originCoords[1];
                $from_lng = $originCoords[0];
                
                $to_lat = $destCoords[1];
                $to_lng = $destCoords[0];
                
                if($from_lat == '' || $from_lng == '' || $to_lat == '' || $to_lng == ''){
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'Unable to calculate distance/duration for this location.',
                        'data' => []
                    ]);
                    
                }else{
                    goto KJHGFDS;
                }
                
                
            }
    
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
    
    public function DistanceAndDurationAll(Request $request)
    {
        try {
            // $from_lat = $request->from_lat;
            // $from_lng = $request->from_lng;
            
            $cities_form = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->from)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            $cities_to = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->to)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            if($cities_form){
                $from_lat = $cities_form->latitude;
                $from_lng = $cities_form->longitude;
            }
            if($cities_to){
                $to_lat = $cities_to->latitude;
                $to_lng = $cities_to->longitude;
            }
            
            // $to_lat   = $request->to_lat;
            // $to_lng   = $request->to_lng;
    
            $j_type = $request->way_type; // 'roundtrip' or 'oneway'
            $googleKey = env('DISTANCE_GOOGLE_KEY');
            $data = [];
            $column = [];
            
            foreach($request->pass as $gg => $g){
                if($g == 'mini'){
                    $column[] = 'mini_four_seater';
                }elseif($g == '4'){
                    
                    $column[] = 'four_seater';
                }elseif($g == '6'){
                    
                    $column[] = 'six_seater';
                }elseif($g == '7'){
                    
                    $column[] = 'seven_seater';
                }elseif($g == '8'){
                    
                    $column[] = 'onethree_seater';
                }elseif($g == '9'){
                    
                    $column[] = 'onethree_seater';
                }
            }
            
            if(!empty($request->pass)){
                
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        
                    ])->whereIn('seater', $column)->first();
                
                // return $check_data;
                if($check_data && $request->pass){
                    $j_val = ($j_type == 'roundtrip') ? $check_data->return_fare : $check_data->oneway_fare;
                    $data['duration'] = $check_data->duration;
                    $data['distance'] = $check_data->distance;
                    $data['fare'] = $j_val;
                    // return $data;
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Distance, Duration and Fare Retrieved Successfully'
                    ]);
                }
            }
                
            KJHGFDS:
    
            // Format coordinates
            $from_area = $from_lat . ',' . $from_lng;
            $to_area   = $to_lat . ',' . $to_lng;
    
            // Distance Matrix API
            $matrix_response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                'origins' => $from_area,
                'destinations' => $to_area,
                'key' => $googleKey,
            ]);
            $obj = $matrix_response->json();
    
            if (isset($obj['rows'][0]['elements'][0]['status']) && $obj['rows'][0]['elements'][0]['status'] == 'OK') {
                
                $j_val = ($j_type == 'roundtrip') ? 2 : 1;
    
                $data['duration'] = $obj['rows'][0]['elements'][0]['duration']['text'] ?? 'N/A';
                $distance = $obj['rows'][0]['elements'][0]['distance']['text'] ?? 'N/A';
    
                $duration_sec = $obj['rows'][0]['elements'][0]['duration']['value'];
                $distance_meter = $obj['rows'][0]['elements'][0]['distance']['value'];
    
                $distance_km = ceil($distance_meter / 1000) * $j_val;
                $duration_min = ceil($duration_sec / 60) * $j_val;
    
                $distance_number = str_replace(['km', ',', 'mi'], '', $distance);
                $unit = 'km'; // can fetch from DB if needed
    
                if ($unit == 'miles') {
                    $data['distance'] = round($distance_number * 0.621371) * $j_val;
                } else {
                    $data['distance'] = round($distance_number) * $j_val;
                }
    
                // Directions API (for transit fare if available)
                $directions_response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
                    'origin' => $from_area,
                    'destination' => $to_area,
                    'mode' => 'transit',
                    'key' => $googleKey,
                ]);
                $directions_data = $directions_response->json();
    
                // $fare = $directions_data['routes'][0]['fare']['text'] ?? null;
                // if ($fare) {
                //     // $fare = 
                //     $data['fare'] = preg_replace('/[^\d.]/', '', $fare) * $j_val;
                    
                //     // $base_fare = 50;
                //     // $per_km_rate = 10;
                //     // $per_minute_rate = 1.5;
    
                //     // $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     // $approx_fare = round($fare);
    
                //     // $data['fare'] = $approx_fare;
                // } else {
                //     // Custom fare calculation
                //     $base_fare = 50;          // ₹50 base
                //     $per_km_rate = 12;        // ₹10 per km
                //     $per_minute_rate = 1.5;   // ₹1.5 per minute
    
                //     $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     $approx_fare = round($fare);
    
                //     $data['fare'] = $approx_fare;
                // }
                
                // Determine which column to use based on passenger count
                
                // Fetch matching fare row
                $get_fare = DB::table('tariff_fare')
                    ->where('from_km', '<=', (float) $data['distance'])
                    ->where('to_km', '>=', (float) $data['distance'])
                    ->where($column, '!=', 0)
                    ->where('status', '0')
                    ->first();
                
                // Assign fare value if found
                if ($get_fare) {
                    $data['fare'] = $get_fare->{$column} ?? 0;
                }
                
                // return $data['fare'];
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        'seater'   => $column
                    ])->exists();
                
                
                if (!$check_data && $request->pass != '') {
                    
                    $a_fare = $data['fare'] / $j_val;
                
                    $insert_data = [
                        'from'         => $request->from,
                        'to'           => $request->to,
                        'seater'       => $column,
                        'distance'     => $data['distance'],
                        'duration'     => $data['duration'],
                        'oneway_fare'  => $a_fare,
                        'return_fare'  => $a_fare * 2,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                    
                    
                
                    DB::table('location_distance')->insertGetId($insert_data);
                }

    
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Distance, Duration and Fare Retrieved Successfully'
                ]);
            } else {
                
                
                
                $apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVkODk3M2U0YTQ1ZDQyMGFhMjExYTU1YmU2ZGFlZGM3IiwiaCI6Im11cm11cjY0In0=";
                
                $originResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->from,
                ]);
    
                $destResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->to,
                ]);
    
                $originData = $originResponse->json();
                $destData = $destResponse->json();
                
                $originCoords = $originData['features'][0]['geometry']['coordinates'] ?? null;
                $destCoords = $destData['features'][0]['geometry']['coordinates'] ?? null;
    
                if (!$originCoords || !$destCoords) {
                    return response()->json(['status' => false, 'message' => 'Coordinates not found'], 400);
                }
                
                $from_lat = $originCoords[1];
                $from_lng = $originCoords[0];
                
                $to_lat = $destCoords[1];
                $to_lng = $destCoords[0];
                
                if($from_lat == '' || $from_lng == '' || $to_lat == '' || $to_lng == ''){
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'Unable to calculate distance/duration for this location.',
                        'data' => []
                    ]);
                    
                }else{
                    goto KJHGFDS;
                }
                
                
            }
    
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
    
    public function DistanceAndDuration_admin(Request $request)
    {
        try {
            // $from_lat = $request->from_lat;
            // $from_lng = $request->from_lng;
            
            if($request->auth_token != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345'){
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Unauthorized.',
                ], 401);
            }
            
            $cities_form = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->from)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            $cities_to = DB::table('cities')
                ->select('id', 'name', 'latitude', 'longitude')
                ->where('name', $request->to)
                ->where(['country_code' => 'IN', 'state_code' => 'TN'])
                ->first();
                
            if($cities_form){
                $from_lat = $cities_form->latitude;
                $from_lng = $cities_form->longitude;
            }
            if($cities_to){
                $to_lat = $cities_to->latitude;
                $to_lng = $cities_to->longitude;
            }
            
            // $to_lat   = $request->to_lat;
            // $to_lng   = $request->to_lng;
    
            $j_type = $request->way_type; // 'roundtrip' or 'oneway'
            $googleKey = env('DISTANCE_GOOGLE_KEY');
            $data = [];
            if ($request->pass && $request->pass <= 4) {
                $column = 'four_seater';
            } elseif ($request->pass && $request->pass <= 6) {
                $column = 'six_seater';
            } elseif ($request->pass && $request->pass <= 7) {
                $column = 'seven_seater';
            }elseif ($request->pass && $request->pass >= 8 && $request->pass <= 13) {
                $column = 'onethree_seater';
            }elseif ($request->pass && $request->pass >= 13 && $request->pass <= 18) {
                $column = 'oneeight_seater';
            }elseif ($request->pass && $request->pass >= 18 && $request->pass <= 21) {
                $column = 'twoone_seater';
            }elseif ($request->pass && $request->pass >= 21 && $request->pass <= 25) {
                $column = 'twofive_seater';
            }elseif ($request->pass && $request->pass >= 25 && $request->pass <= 50) {
                $column = 'fivezero_seater';
            } elseif (empty($request->pass)) {
                $column = 'mini_four_seater';
            } else {
                $column = 'seven_seater';
            }
            
            if($request->pass != ''){
                
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        'seater' => $column
                    ])->first();
                
                // return $check_data;
                if($check_data && $request->pass != ''){
                    $j_val = ($j_type == 'roundtrip') ? $check_data->return_fare : $check_data->oneway_fare;
                    $data['duration'] = $check_data->duration;
                    $data['distance'] = $check_data->distance;
                    $data['fare'] = $j_val;
                    // return $data;
                    return response()->json([
                        'status' => true,
                        'data' => $data,
                        'message' => 'Distance, Duration and Fare Retrieved Successfully'
                    ]);
                }
            }
                
            KJHGFDS:
    
            // Format coordinates
            $from_area = $from_lat . ',' . $from_lng;
            $to_area   = $to_lat . ',' . $to_lng;
    
            // Distance Matrix API
            $matrix_response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                'origins' => $from_area,
                'destinations' => $to_area,
                'key' => $googleKey,
            ]);
            $obj = $matrix_response->json();
    
            if (isset($obj['rows'][0]['elements'][0]['status']) && $obj['rows'][0]['elements'][0]['status'] == 'OK') {
                
                $j_val = ($j_type == 'roundtrip') ? 2 : 1;
    
                $data['duration'] = $obj['rows'][0]['elements'][0]['duration']['text'] ?? 'N/A';
                $distance = $obj['rows'][0]['elements'][0]['distance']['text'] ?? 'N/A';
    
                $duration_sec = $obj['rows'][0]['elements'][0]['duration']['value'];
                $distance_meter = $obj['rows'][0]['elements'][0]['distance']['value'];
    
                $distance_km = ceil($distance_meter / 1000) * $j_val;
                $duration_min = ceil($duration_sec / 60) * $j_val;
    
                $distance_number = str_replace(['km', ',', 'mi'], '', $distance);
                $unit = 'km'; // can fetch from DB if needed
    
                if ($unit == 'miles') {
                    $data['distance'] = round($distance_number * 0.621371) * $j_val;
                } else {
                    $data['distance'] = round($distance_number) * $j_val;
                }
    
                // Directions API (for transit fare if available)
                $directions_response = Http::get("https://maps.googleapis.com/maps/api/directions/json", [
                    'origin' => $from_area,
                    'destination' => $to_area,
                    'mode' => 'transit',
                    'key' => $googleKey,
                ]);
                $directions_data = $directions_response->json();
    
                // $fare = $directions_data['routes'][0]['fare']['text'] ?? null;
                // if ($fare) {
                //     // $fare = 
                //     $data['fare'] = preg_replace('/[^\d.]/', '', $fare) * $j_val;
                    
                //     // $base_fare = 50;
                //     // $per_km_rate = 10;
                //     // $per_minute_rate = 1.5;
    
                //     // $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     // $approx_fare = round($fare);
    
                //     // $data['fare'] = $approx_fare;
                // } else {
                //     // Custom fare calculation
                //     $base_fare = 50;          // ₹50 base
                //     $per_km_rate = 12;        // ₹10 per km
                //     $per_minute_rate = 1.5;   // ₹1.5 per minute
    
                //     $fare = $base_fare + ($distance_km * $per_km_rate) + ($duration_min * $per_minute_rate);
                //     $approx_fare = round($fare);
    
                //     $data['fare'] = $approx_fare;
                // }
                
                // Determine which column to use based on passenger count
                
                // Fetch matching fare row
                $get_fare = DB::table('tariff_fare')
                    ->where('from_km', '<=', (float) $data['distance'])
                    ->where('to_km', '>=', (float) $data['distance'])
                    ->where($column, '!=', 0)
                    ->where('status', '0')
                    ->first();
                
                // Assign fare value if found
                if ($get_fare) {
                    $data['fare'] = $get_fare->{$column} ?? 0;
                }
                
                // return $data['fare'];
                $check_data = DB::table('location_distance')
                    ->where([
                        'from' => $request->from,
                        'to'   => $request->to,
                        'seater'   => $column
                    ])->exists();
                
                
                if (!$check_data && $request->pass != '') {
                    
                    $a_fare = $data['fare'] / $j_val;
                
                    $insert_data = [
                        'from'         => $request->from,
                        'to'           => $request->to,
                        'seater'       => $column,
                        'distance'     => $data['distance'],
                        'duration'     => $data['duration'],
                        'oneway_fare'  => $a_fare,
                        'return_fare'  => $a_fare * 2,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                    
                    
                
                    DB::table('location_distance')->insertGetId($insert_data);
                }

    
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Distance, Duration and Fare Retrieved Successfully'
                ]);
            } else {
                
                
                
                $apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVkODk3M2U0YTQ1ZDQyMGFhMjExYTU1YmU2ZGFlZGM3IiwiaCI6Im11cm11cjY0In0=";
                
                $originResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->from,
                ]);
    
                $destResponse = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->get('https://api.openrouteservice.org/geocode/search', [
                    'api_key' => $apiKey,
                    'text' => $request->to,
                ]);
    
                $originData = $originResponse->json();
                $destData = $destResponse->json();
                
                $originCoords = $originData['features'][0]['geometry']['coordinates'] ?? null;
                $destCoords = $destData['features'][0]['geometry']['coordinates'] ?? null;
    
                if (!$originCoords || !$destCoords) {
                    return response()->json(['status' => false, 'message' => 'Coordinates not found'], 400);
                }
                
                $from_lat = $originCoords[1];
                $from_lng = $originCoords[0];
                
                $to_lat = $destCoords[1];
                $to_lng = $destCoords[0];
                
                if($from_lat == '' || $from_lng == '' || $to_lat == '' || $to_lng == ''){
                    
                    return response()->json([
                        'status' => false,
                        'message' => 'Unable to calculate distance/duration for this location.',
                        'data' => []
                    ]);
                    
                }else{
                    goto KJHGFDS;
                }
                
                
            }
    
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
    
    public function update_profile(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['required'],
                'isPro' => ['required'],
                'isFront' => ['required'],
                'address' => ['required'],
                'isBack' => ['required'],
                'id_proof_type' => ['required', 'string', 'max:255'],
                'dial_code' => ['nullable', 'max:5'],
                'company_name' => ['nullable', 'string', 'max:255'],
            
                // Conditional aadhar_no validation
                'aadhar_no' => [
                    'null',
                    Rule::when($request->id_proof_type == 'aadhar', 'digits:12'),
                    Rule::when($request->id_proof_type !='aadhar', 'digits:15'),
                ],
            
                // Images
                'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'aadhar_image_front' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'aadhar_image_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                // Custom messages
                'aadhar_no.digits' => $request->id_proof_type == 'aadhar'
                    ? 'Aadhar number must be exactly 12 digits.'
                    : 'License number must be exactly 15 digits.',
            ]);

        
    
            $userId = auth()->user()->id;
            $data = [];
            $get_pre_image = DB::table('user_register')->where('id', $userId)->where('deletes', '0')->first();
            $temp_img = [];
            
            $updateData = [
                'name'         => $request->name,
                'email'        => $request->email,
                'dialcode'     => $request->dial_code,
                'mobile'       => $request->phone,
                'company_name' => $request->company_name??null,
                'aadhar_no'    => $request->aadhar_no,
                'proof_type'    => $request->id_proof_type,
                'address'    => $request->address,
                'updated_at'   => now(),
            ];
            if ($request->hasFile('profile_image') && $request->isPro != false) {
                
                // return $request->hasFile('profile_image');
                if ($get_pre_image->profile_img_url) {
                    // Extract the S3 key from the full URL
                    $parsedUrl = parse_url($get_pre_image->profile_img_url);
                    if (isset($parsedUrl['path'])) {
                        $oldFilePath = ltrim($parsedUrl['path'], '/');
                        if (Storage::disk('s3')->exists($oldFilePath)) {
                            Storage::disk('s3')->delete($oldFilePath);
                        }
                    }
                }
                
                $image = $request->file('profile_image');
                $fileName = $userId . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $f_path = 'goride-driver/' . $fileName;
                
                $profilePath = $request->file('profile_image')->store($f_path, 's3');
                Storage::disk('s3')->setVisibility($profilePath, 'public');
                $data['profile_image_url'] = Storage::disk('s3')->url($profilePath);
                $temp_img[] = $data['profile_image_url'];
                $updateData['profile_img_url'] = $data['profile_image_url'];
            }
            
            // return $request->isPro;
            
            // if ($request->hasFile('licence_image')) {
                
            //     if ($get_pre_image->licence_image) {
            //         // Extract the S3 key from the full URL
            //         $parsedUrl = parse_url($get_pre_image->licence_image);
            //         if (isset($parsedUrl['path'])) {
            //             $oldFilePath = ltrim($parsedUrl['path'], '/');
            //             if (Storage::disk('s3')->exists($oldFilePath)) {
            //                 Storage::disk('s3')->delete($oldFilePath);
            //             }
            //         }
            //     }
                
            //     $image = $request->file('licence_image');
            //     $fileName = $userId . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            //     $f_path = 'goride-driver/' . $fileName;
                
            //     $profilePath = $request->file('licence_image')->store($f_path, 's3');
            //     Storage::disk('s3')->setVisibility($profilePath, 'public');
            //     $data['licence_image_url'] = Storage::disk('s3')->url($profilePath);
                
            //     $temp_img[] = $data['licence_image_url'];
            // }
            
            if ($request->hasFile('aadhar_image_front') && $request->isFront != false) {
                
                if ($get_pre_image->aadhar_image_front) {
                    // Extract the S3 key from the full URL
                    $parsedUrl = parse_url($get_pre_image->aadhar_image_front);
                    if (isset($parsedUrl['path'])) {
                        $oldFilePath = ltrim($parsedUrl['path'], '/');
                        if (Storage::disk('s3')->exists($oldFilePath)) {
                            Storage::disk('s3')->delete($oldFilePath);
                        }
                    }
                }
                
                $image = $request->file('aadhar_image_front');
                $fileName = $userId . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $f_path = 'goride-aadhar/' . $fileName;
                
                $profilePath = $request->file('aadhar_image_front')->store($f_path, 's3');
                Storage::disk('s3')->setVisibility($profilePath, 'public');
                $data['aadhar_image_front'] = Storage::disk('s3')->url($profilePath);
                
                $temp_img[] = $data['aadhar_image_front'];
                $updateData['aadhar_image_front'] = $data['aadhar_image_front'];
            }
            
            if ($request->hasFile('aadhar_image_back') && $request->isBack != false) {
                
                if ($get_pre_image->aadhar_image_back) {
                    // Extract the S3 key from the full URL
                    $parsedUrl = parse_url($get_pre_image->aadhar_image_back);
                    if (isset($parsedUrl['path'])) {
                        $oldFilePath = ltrim($parsedUrl['path'], '/');
                        if (Storage::disk('s3')->exists($oldFilePath)) {
                            Storage::disk('s3')->delete($oldFilePath);
                        }
                    }
                }
                
                $image = $request->file('aadhar_image_back');
                $fileName = $userId . '-' . pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $f_path = 'goride-aadhar/' . $fileName;
                
                $profilePath = $request->file('aadhar_image_back')->store($f_path, 's3');
                Storage::disk('s3')->setVisibility($profilePath, 'public');
                $data['aadhar_image_back'] = Storage::disk('s3')->url($profilePath);
                
                $temp_img[] = $data['aadhar_image_back'];
                $updateData['aadhar_image_back'] = $data['aadhar_image_back'];
            }
            
            
            
            // if ($request->isPro == true && !empty($data['profile_image_url'])) {
            //     $updateData['profile_img_url'] = $get_pre_image->profile_img_url;
            // }
            
            // if ($request->isFront == true && !empty($data['aadhar_image_front'])) {
            //     $updateData['aadhar_image_front'] = $get_pre_image->aadhar_image_front;
            // }
            
            // if ($request->isBack == true && !empty($data['aadhar_image_back'])) {
            //     $updateData['aadhar_image_back'] = $get_pre_image->aadhar_image_back;
            // }
            
            // return $updateData;
            
    
        
            // DB::table('user_register')->where('id', $userId)->update([
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'dialcode' => $request->dial_code,
            //     'mobile' => $request->phone,
            //     'company_name' => $request->company_name,
            //     'aadhar_no' => $request->aadhar_no,
                
                
            //     // 'licence_image' => $data['licence_image_url'],
            //     // 'aadhar_images' => json_encode($data['aadhar_image_urls']),
            //     'updated_at' => now(),
            // ]);
            
            DB::table('user_register')->where('id', $userId)->where('deletes', '0')->update($updateData);
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Profile updated successfully.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            foreach ($temp_img as $path) {
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
            }
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_profile(Request $request)
    {
        
        if(auth()->user()->id != ''){
            
            // $get_user = DB::table('user_register')->leftJoin('kyc_details ON kyc_details.user_id = user_register.id')->where('deletes', '0')->where('kyc_details.deletes', '0')->where('id', auth()->user()->id)->select('id', 'name', 'email', 'mobile', 'profile_img_url ?? kyc_details.selfie_url', 'aadhar_no', 'aadhar_image_front', 'aadhar_image_back', 'licence_image', 'company_name', 'proof_type')->first();
            
            
            $get_user = DB::table('user_register')
                ->leftJoin('kyc_details', 'kyc_details.user_id', '=', 'user_register.id')
                ->where('user_register.deletes', '0')
                // ->where('kyc_details.deletes', '0')
                ->where('user_register.id', auth()->id())
                ->select(
                    'user_register.id',
                    'user_register.name',
                    'user_register.email',
                    'user_register.mobile',
                    DB::raw('COALESCE(user_register.profile_img_url, kyc_details.selfie_url) AS profile_img_url'),
                    'user_register.aadhar_no',
                    'user_register.aadhar_image_front',
                    'user_register.aadhar_image_back',
                    'user_register.licence_image',
                    'user_register.company_name',
                    'user_register.address',
                    'user_register.proof_type'
                )
                ->first();

            
            return response()->json([
                'status' => true,
                'data' => $get_user??null,
                'message' => 'Profile retrieved successfully.'
            ], 200);
        }else{
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Unauthorized'
            ], 401);
            
        }
    }
    
    public function create_job(Request $request)
    {
        try {
            $request->validate([
                'job_type' => ['required', 'string', 'max:255'],
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required'],
                'dropoff_date' => ['nullable'],
                'pass_count' => ['required', 'string', 'max:255'],
                'fare' => ['required', 'numeric'],
                'distance' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'string', 'max:255'],
                // 'add_fare_details' => ['required', 'string', 'max:255'],
                'bataRadio' => ['nullable', 'string', 'max:255'],
                'parkingRadio' => ['nullable', 'string', 'max:255'],
                'tollRadio' => ['nullable', 'string', 'max:255'],
            ]);
        
    
            $userId = auth()->id();
            $data = [];
            
            
            if(auth()->user()->doc_verify == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            if(auth()->user()->vehicle_verify == 0){
                
                $get_type = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
                
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
                if($get_crm && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'url' => 'https://'.strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                    
                }elseif($get_crm == null && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => null,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }
                
            }
            
            // $check_limit = DB::table('cus_job_temp')
            //     ->where('user_id', auth()->id())
            //     ->where('global_type', 'open')
            //     ->where('deletes', '0')
            //     ->whereDate('created_at', Carbon::today())
            //     ->count();
            
            // if ($check_limit >= 3) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'You have reached the maximum job creation limit for today. Please try again tomorrow.'
            //     ], 200);
            // }
            
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
            
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour())) {
            
                
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be at least 1 hour after the current time.'
                ]);
                
            }
            
            HI:
            $maxAttempts = 5;

            for ($i = 0; $i < $maxAttempts; $i++) {
            
                $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            
                if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            
                if ($i == $maxAttempts - 1) {
                    // throw new \Exception('Unable to generate unique job number.');
                    goto HI;
                }
            }
            
            $data = $request->all();
            $data['bataRadio'] = $data['parkingRadio'] = $data['tollRadio'] = 'Included';
            $data['job_no'] = $job_no;
            $data['global_type'] = 'open';
            $data['user_id'] = $userId;
            $add_details = [];
            if($data['bataRadio'] || $data['parkingRadio'] || $data['tollRadio']){
                $add_details = [
                    'bata' =>  $data['bataRadio'],
                    'parking' =>  $data['parkingRadio'],
                    'toll' =>  $data['tollRadio']
                ];
            }
            
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = ($data['dropoff_date'] != '' || $data['dropoff_date'] != null) ? date("Y-m-d H:i:s", strtotime($data['dropoff_date'])) : null;
            
            $data['created_at'] = now();
            $data['updated_at'] = now();
            unset($data['bataRadio'], $data['tollRadio'], $data['parkingRadio']);
            
            return $data;
            
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            if ($create_job) {
                
                // $fcmToken = $this->getFcm(null);
                
                $parts = array_map('trim', explode(',', $request->from_place));
                $count = count($parts);
                
                if ($count >= 2) {
                    $place = $parts[$count - 2];
                } else {
                    $place = $parts[0];
                }

                // $fcmToken = $this->getFcm(null, $place);

                if ($fcmToken) {
                    $accessToken = $this->getAccessToken();
                    if ($accessToken) {
                        foreach($fcmToken as $token){
                            $responses = $this->sendFCM(
                                $accessToken,
                                $token,
                                'New Job Arrived!',
                                'A new job is available from ' . $request->from_place . '. Open the app to place your bid.',
                                [
                                    'caller' => auth()->user()->name,
                                    'type'     => 'new_job_notification',
                                    'id'     => $create_job,
                                    'action'     => 'agree_popup',
                                    'url'   => env('APP_URL') . 'jobs',
                                    'pickup'   => $data['pickup_date'] ?? null,
                                ]
                                
                            );
                            // return $responses;
                        }
                    }
                }
                
                // return $fcmToken;
                
                
                return response()->json([
                    'status' => true,
                    'data' => $job_no,
                    'job_id' => $create_job,
                    'message' => 'Job created successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create job.'
                ], 200);
            }
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function create_job_v2(Request $request)
    {
        try {
            $request->validate([
                'job_type'      => ['required', 'string', 'max:255'],
                'from_place'    => ['required', 'string', 'max:255'],
                'to_place'      => ['required', 'string', 'max:255'],
                'pickup_date'   => ['required'],
                'dropoff_date'  => ['nullable'],
                'pass_count'    => ['required', 'string', 'max:255'],
                'fare'          => ['required', 'numeric'],
                'distance'      => ['required', 'string', 'max:255'],
                'duration'      => ['nullable', 'string', 'max:255'],
                'jb_type'       => ['required', 'string', 'max:255'],
                'mock_amt'      => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::requiredIf($request->jb_type == 'mock'),
                ],
            
                'bataRadio'     => ['nullable', 'string', 'max:255'],
                'parkingRadio'  => ['nullable', 'string', 'max:255'],
                'tollRadio'     => ['nullable', 'string', 'max:255'],
            ]);

        
    
            $userId = auth()->id();
            $data = [];
            
            if (auth()->user()->walletBalance < 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance. Please top up to create job.',
                    'data'    => null,
                ]);
            }
            
            
            if(auth()->user()->doc_verify == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            if(auth()->user()->vehicle_verify == 0){
                
                $get_type = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
                
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
                if($get_crm && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    // return response()->json([
                    //     'status'  => false,
                    //     'data'    => null,
                    //     "user_type" => $get_type->type,
                    //     'url' => 'https://'.strtolower($get_crm->fullDomain),
                    //     'message' => 'CRM vehicle setup Pending.'
                    // ], 200);
                    
                }elseif($get_crm == null && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => null,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }
                
            }
            
            // $check_limit = DB::table('open_jobs')
            //     ->where('user_id', auth()->id())
            //     ->where('deletes', '0')
            //     ->whereDate('created_at', Carbon::today())
            //     ->count();
            
            // if ($check_limit >= 3) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'You have reached the maximum job creation limit for today. Please try again tomorrow.'
            //     ], 200);
            // }
            
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
            
            if (
                ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour(2))) 
                || $pickup->lt($now->startOfDay())
            ) {
                
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be 2 hour ahead or on a future date.'
                ]);
                
            }
            
            // $jobCount = DB::table('cus_job_temp')->count() + 1;
            // $job_no = "GRD-" . str_pad($jobCount, 3, '0', STR_PAD_LEFT);
            
            // HI:
            $maxAttempts = 5;

            for ($i = 0; $i < $maxAttempts; $i++) {
            
                $job_no = 'GRD-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            
                if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            
                if ($i == $maxAttempts - 1) {
                    // throw new \Exception('Unable to generate unique job number.');
                    // goto HI;
                }
            }
    
            $data['job_no'] = $job_no;
            
            $data = $request->all();
            $data['bataRadio'] = $data['parkingRadio'] = $data['tollRadio'] = 'Included';
            $data['job_no'] = $job_no;
            $data['global_type'] = $request->jb_type == 'mock' ? 'mock' : 'open';
            $data['mock_amt'] = $request->mock_amt??0;
            $data['base_fare'] = $request->fare??0;
            $data['user_id'] = $userId;
            $add_details = [];
            if($data['bataRadio'] || $data['parkingRadio'] || $data['tollRadio']){
                $add_details = [
                    'bata' =>  $data['bataRadio'],
                    'parking' =>  $data['parkingRadio'],
                    'toll' =>  $data['tollRadio']
                ];
            }
            
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            // $data['dropoff_date'] = $request->day ?? $request->dropoff_date ?? null;
            $data['dropoff_date'] = $data['dropoff_date'] ?? null;
            // $data['dropoff_date'] = $request->dropoff_date == 1 ? 'Upto 24 Hours' : $request->dropoff_date .' Days';

            $data['created_at'] = now();
            $data['updated_at'] = now();
            
            unset($data['bataRadio'], $data['tollRadio'], $data['parkingRadio'], $data['jb_type']);
            
            if ($data['pass_count'] && $data['pass_count'] <= 4) {
                $column = 'four_seater';
            } elseif ($data['pass_count'] && $data['pass_count'] <= 6) {
                $column = 'six_seater';
            } elseif ($data['pass_count'] && $data['pass_count'] <= 7) {
                $column = 'seven_seater';
            }elseif ($data['pass_count'] && $data['pass_count'] >= 8 && $data['pass_count'] <= 13) {
                $column = 'onethree_seater';
            }elseif ($data['pass_count'] && $data['pass_count'] >= 13 && $data['pass_count'] <= 18) {
                $column = 'oneeight_seater';
            }elseif ($data['pass_count'] && $data['pass_count'] >= 18 && $data['pass_count'] <= 21) {
                $column = 'twoone_seater';
            }elseif ($data['pass_count'] && $data['pass_count'] >= 21 && $data['pass_count'] <= 25) {
                $column = 'twofive_seater';
            }elseif ($data['pass_count'] && $data['pass_count'] >= 25 && $data['pass_count'] <= 50) {
                $column = 'fivezero_seater';
            } elseif (empty($data['pass_count'])) {
                $column = 'mini_four_seater';
            } else {
                $column = 'seven_seater';
            }
            
            $check_data = DB::table('location_distance')
                ->where([
                    'from' => $data['from_place'],
                    'to'   => $data['to_place'],
                    'seater' => $column
                ])->first();
            
            // return $column;
            // if($check_data && $data['pass_count'] != ''){
                // $data['fare'] = ($data['job_type'] == 'roundtrip') ? $check_data->return_fare : $check_data->oneway_fare;
                
                // $data['tax'] = (10 / 100) * $data['fare'];
            
                // $data['without_tax'] = $data['fare'];
                // $data['fare'] = $data['fare'] + $data['tax'];
                
            // }
            
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            if ($create_job) {
                
                $parts = array_map('trim', explode(',', $request->from_place));
                $count = count($parts);
                
                if ($count >= 2) {
                    $place = $parts[$count - 2];
                } else {
                    $place = $parts[0];
                }
                
                $data['id'] = $create_job;
                $data['poster_name'] = auth()->user()->name;
                $data['from_place_id'] = '';
                $data['to_place_id'] = '';
                // return $data;
                $this->createFirebaseJob($job_no, $data);
                
                $place = collect(explode(',', $request->from_place))->map('trim')->get(-2);
                $fcmTokens = $this->getFcm(null, $place);
                
                if (!empty($fcmTokens)) {
                    // dispatch(new \App\Jobs\SendJobNotificationJob(
                    //     [
                    //         'id'  => $job_no,
                    //         'type'    => 'new_job_notification',
                    //         'pickup'  => $data['pickup_date'],
                    //         'action'  => 'agree_popup'
                    //     ],
                    //     $fcmTokens,
                    //     $this->serviceAccount,
                    //     $this->getAccessToken()
                    // ));
                }
                
                return response()->json([
                    'status' => true,
                    'data' => $job_no,
                    'job_id' => $create_job,
                    'message' => 'Job created successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create job.'
                ], 200);
            }
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function create_job_v2_copy(Request $request)
    {
        try {
            $request->validate([
                'job_type'      => ['required', 'string', 'max:255'],
                'from_place'    => ['required', 'string', 'max:255'],
                'to_place'      => ['required', 'string', 'max:255'],
                'pickup_date'   => ['required'],
                'dropoff_date'  => ['nullable'],
                'pass_count'    => ['required', 'string', 'max:255'],
                'fare'          => ['required', 'numeric'],
                'distance'      => ['required', 'string', 'max:255'],
                'duration'      => ['nullable', 'string', 'max:255'],
                'jb_type'       => ['required', 'string', 'max:255'],
                'mock_amt'      => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::requiredIf($request->jb_type == 'mock'),
                ],
            
                'bataRadio'     => ['nullable', 'string', 'max:255'],
                'parkingRadio'  => ['nullable', 'string', 'max:255'],
                'tollRadio'     => ['nullable', 'string', 'max:255'],
            ]);

        
    
            $userId = auth()->id();
            $data = [];
            
            
            if(auth()->user()->doc_verify == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            if(auth()->user()->vehicle_verify == 0){
                
                $get_type = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
                
                // $get_crm = DB::table('crm')
                //     ->select('fullDomain')
                //     ->join('subscriptions as sub', 'sub.id', '=', 'crm.subscription_id')
                //     ->where('crm.userID', auth()->user()->id)
                //     ->where('crm.crmStatus', 'generated')
                //     ->where('crm.deletes', '0')
                //     ->where('sub.planType', 'TRAIL')
                //     ->where('sub.paymentStatus', 'SUCCESS')
                //     ->orderBy('crm.id', 'DESC')
                //     ->first();
                
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
                if($get_crm && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'url' => 'https://'.strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                    
                }elseif($get_crm == null && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => null,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }
                
            }
            
            $check_limit = DB::table('open_jobs')
                ->where('user_id', auth()->id())
                ->where('deletes', '0')
                ->whereDate('created_at', Carbon::today())
                ->count();
            
            // if ($check_limit >= 3) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'You have reached the maximum job creation limit for today. Please try again tomorrow.'
            //     ], 200);
            // }
            
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
            
            // return $pickup->isToday();
            
            if (
                ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour())) 
                || $pickup->lt($now->startOfDay())
            ) {
                
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be 1 hour ahead or on a future date.'
                ]);
                
            }
            
            $jobCount = DB::table('open_jobs')->count() + 1;
            $job_no = "GR-" . str_pad($jobCount, 3, '0', STR_PAD_LEFT);
            
            $data = $request->all();
            $data['bataRadio'] = $data['parkingRadio'] = $data['tollRadio'] = 'Included';
            $data['job_no'] = $job_no;
            $data['global_type'] = $request->jb_type == 'mock' ? 'mock' : 'open';
            $data['mock_amt'] = $request->mock_amt??0;
            $data['user_id'] = $userId;
            $add_details = [];
            if($data['bataRadio'] || $data['parkingRadio'] || $data['tollRadio']){
                $add_details = [
                    'bata' =>  $data['bataRadio'],
                    'parking' =>  $data['parkingRadio'],
                    'toll' =>  $data['tollRadio']
                ];
            }
            
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = isset($data['dropoff_date']) && $data['dropoff_date']
                                ? date('Y-m-d H:i:s', strtotime($data['dropoff_date']))
                                : null;

            
            $data['created_at'] = now();
            $data['updated_at'] = now();
            unset($data['bataRadio'], $data['tollRadio'], $data['parkingRadio'], $data['jb_type']);
            
            
            // return $data;
            $create_job = DB::table('open_jobs')->insertGetId($data);
            
            if ($create_job) {
                
                // $fcmToken = $this->getFcm(null);
                
                $parts = array_map('trim', explode(',', $request->from_place));
                $count = count($parts);
                
                if ($count >= 2) {
                    $place = $parts[$count - 2];
                } else {
                    $place = $parts[0];
                }

                // $fcmToken = $this->getFcm(null, $place);

                // if ($fcmToken) {
                //     $accessToken = $this->getAccessToken();
                //     if ($accessToken) {
                //         foreach($fcmToken as $token){
                //             $responses = $this->sendFCM(
                //                 $accessToken,
                //                 $token,
                //                 'New Job Arrived!',
                //                 'A new job is available from ' . $request->from_place . '. Open the app to place your bid.',
                //                 [
                //                     'caller' => auth()->user()->name,
                //                     'type'     => 'new_job_notification',
                //                     'id'     => $create_job,
                //                     'action'     => 'agree_popup',
                //                     'url'   => env('APP_URL') . 'jobs',
                //                     'pickup'   => $data['pickup_date'] ?? null,
                //                 ]
                                
                //             );
                //             // return $responses;
                //         }
                //     }
                // }
                
                // return $fcmToken;
                
                
                return response()->json([
                    'status' => true,
                    'data' => $job_no,
                    'job_id' => $create_job,
                    'message' => 'Job created successfully.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create job.'
                ], 200);
            }
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function get_all_jobsOLDFun(Request $request)
    {
        try {
    
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            $request->validate([
                'location'      => ['nullable', 'array'],
                'location.*'    => ['nullable', 'string', 'max:255'],
                'distance'      => ['nullable', 'numeric', 'min:0'],
                'min_distance'  => ['nullable', 'numeric', 'min:0'],
                'pass'          => ['nullable', 'numeric', 'min:0'],
                'min_pass'      => ['nullable', 'numeric', 'min:0'],
                'fare'          => ['nullable', 'numeric', 'min:0'],
                'min_fare'      => ['nullable', 'numeric', 'min:0'],
                'fromDate'      => ['nullable', 'date'],
                'toDate'        => ['nullable', 'date'],
            ]);
    
            $userId = auth()->id();
    
            $loc          = $request->location ? array_filter($request->location) : null;
            $distance     = $request->distance ?: null;
            $min_distance = $request->min_distance ?: null;
            $pass         = $request->pass ?: null;
            $min_pass     = $request->min_pass ?: null;
            $fare         = $request->fare ?: null;
            $min_fare     = $request->min_fare ?: null;
            $fromDate     = $request->fromDate ?: null;
            $toDate       = $request->toDate ?: null;
    
            $arr_sat = ['bidding', 'created'];
    
            $page    = (int) $request->input('page', 1);
            $perPage = 3;
            $skip    = ($page - 1) * $perPage;
    
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->first();
    
            if (auth()->user()->isOpenjob == '0' && $check_role) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'jobs' => [],
                        'next_page' => null
                    ],
                    'message' => 'Job Retrieved Successfully',
                ], 200);
            }
    
            $firebaseJobs = [];
            $dbJobs = [];
    
            try {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
    
                $firebaseDocs = $firebase->getOpenJobs();
    
                foreach ($firebaseDocs as $doc) {

                    $job = $this->parseFirestoreFields($doc['fields']);
                    
                    // dd($job, $userId);
                
                    $bids = $job['bids_details'] ?? [];
                
                    $job['user_bid'] = array_key_exists($userId, $bids) ? 'yes' : 'no';
                
                    $job['bids_count'] = is_array($bids) ? count($bids) : 0;
                
                    $likedUsers = $job['liked_users'] ?? [];
                
                    $job['user_like_status'] = (
                        is_array($likedUsers) && in_array($userId, $likedUsers)
                    ) ? '1' : '0';
                
                    $job['source'] = 'firebase';
                    $job['add_fare_details'] = '{\"bata\":\"Included\",\"toll\":\"Included\",\"parking\":\"Included\"}';
                    
                    if ($job['fare'] && $job['base_fare']) {
                        // $job->fare = (string) ((int) $job->fare * 0.95);
                        $job['fare'] = (string) ((int) $job['base_fare'] + ((int) $job['toll_fare']));
                    }
                
                    if (!in_array($job['job_status'] ?? '', ['created', 'bidding'])) {
                        continue;
                    }
                    
                    if ($job['global_type'] != 'customer') {
                        continue;
                    }
                    
                    if($job['job_type'] == 'roundtrip'){
                        $job['dropoff_date'] = null;
                    }
                    
                    if ($job['user_id'] == $userId) {
                        continue;
                    }
                
                    if ($loc) {
                        $match = false;
                        foreach ($loc as $l) {
                            if (stripos($job['from_place'] ?? '', $l) !== false) {
                                $match = true;
                                break;
                            }
                        }
                        if (!$match) continue;
                    }
                
                    if ($distance && ($job['distance'] ?? 0) > $distance) continue;
                    if ($min_distance && ($job['distance'] ?? 0) < $min_distance) continue;
                
                    if ($pass && ($job['pass_count'] ?? 0) > $pass) continue;
                    if ($min_pass && ($job['pass_count'] ?? 0) < $min_pass) continue;
                
                    if ($fare && ($job['fare'] ?? 0) > $fare) continue;
                    if ($min_fare && ($job['fare'] ?? 0) < $min_fare) continue;
                
                    if ($fromDate && $toDate && !empty($job['pickup_date'])) {
                        $pickup = Carbon::parse($job['pickup_date']);
                        if ($pickup->lt($fromDate) || $pickup->gt($toDate)) continue;
                    }
                    
                    $firebaseJobs[] = (object) $job;
                }

    
            } catch (\Exception $e) {
                \Log::warning('Firebase jobs skipped', ['error' => $e->getMessage()]);
            }
    
            /* -----------------------------
               MERGE + PAGINATE
            ------------------------------*/
            $merged = collect($firebaseJobs)
                // ->merge($firebaseJobs)
                ->unique('job_no')
                ->sortByDesc('created_at')
                ->values();
    
            $finalJobs = $merged->slice($skip, $perPage)->values();
            
            // $finalJobs = [];
            
            return response()->json([
                'status' => true,
                'data' => [
                    'jobs' => $finalJobs,
                    'next_page' => $merged->count() > ($skip + $perPage)
                        ? $page + 1
                        : null
                ],
                'message' => 'Job Retrieved Successfully',
            ], 200);
    
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_all_jobsOldNode(Request $request)
    {
        try {
    
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            $request->validate([
                'location'      => ['nullable', 'array'],
                'location.*'    => ['nullable', 'string', 'max:255'],
                'distance'      => ['nullable', 'numeric', 'min:0'],
                'min_distance'  => ['nullable', 'numeric', 'min:0'],
                'pass'          => ['nullable', 'numeric', 'min:0'],
                'min_pass'      => ['nullable', 'numeric', 'min:0'],
                'fare'          => ['nullable', 'numeric', 'min:0'],
                'min_fare'      => ['nullable', 'numeric', 'min:0'],
                'fromDate'      => ['nullable', 'date'],
                'toDate'        => ['nullable', 'date'],
            ]);
    
            $userId = auth()->id();
    
            $loc          = $request->location ? array_filter($request->location) : null;
            $distance     = $request->distance ?: null;
            $min_distance = $request->min_distance ?: null;
            $pass         = $request->pass ?: null;
            $min_pass     = $request->min_pass ?: null;
            $fare         = $request->fare ?: null;
            $min_fare     = $request->min_fare ?: null;
            $fromDate     = $request->fromDate ?: null;
            $toDate       = $request->toDate ?: null;
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
        
                $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                $pass = $rcVehicle['seat_capacity'] ?? null;
                // $body_type = $rcVehicle['body_type'] ?? null;
        
            } else {
    
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $userId)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    
                    $pass = $get_ocr->seater ?? null;
                    if($pass == 'mini' || $pass == 'Mini' || $pass == 'Mini 4'){
                        $pass = 4;
                    }
                    $pass += 1;
                }
            }
    
            $page    = (int) $request->input('page', 1);
            $perPage = 3;
            $skip    = ($page - 1) * $perPage;
    
            // Role check
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->first();
    
            if (auth()->user()->isOpenjob == '0' && $check_role) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'jobs' => [],
                        'next_page' => null
                    ],
                    'message' => 'Job Retrieved Successfully',
                ], 200);
            }
    
            /* ===============================
               FETCH FROM NODE CACHE SERVER
            ================================ */
    
            $nodeUrl = env('NODE_CACHE_URL'). '/get-collection/'.env('FIREBASE_COLLECTION');
    
            $response = Http::withBasicAuth(
                env('NODE_CACHE_USER'),
                env('NODE_CACHE_PASS')
            )
            ->timeout(5)
            ->get($nodeUrl);
    
            if (!$response->successful()) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'jobs' => [],
                        'next_page' => null
                    ],
                    'message' => 'Job Retrieved Successfully',
                ], 200);
            }
    
            $firebaseDocs = $response->json();
    
            $firebaseJobs = [];
    
            foreach ($firebaseDocs as $job) {
    
    
                $bids = $job['bids_details'] ?? [];
                $job['user_bid'] = isset($bids[$userId]) ? 'yes' : 'no';
                $job['bids_count'] = is_array($bids) ? count($bids) : 0;
    
                $likedUsers = $job['liked_users'] ?? [];
                $job['user_like_status'] =
                    (is_array($likedUsers) && in_array($userId, $likedUsers)) ? '1' : '0';
    
                $job['source'] = 'firebase';
                $pass_c = $job['pass_count'];
                $job['pass_count'] = $job['pass_count'] == 'mini' ? 5 : $job['pass_count'];
                $job['add_fare_details'] = '{"bata":"Included","toll":"Included","parking":"Included"}';
    
                if (!in_array($job['job_status'] ?? '', ['created', 'bidding'])) continue;
           
                if (($job['user_id'] ?? null) == $userId) continue;
    
                if (($job['job_type'] ?? '') === 'roundtrip') {
                    $job['dropoff_date'] = null;
                }
                
                $isCustomer = isset($job['global_type']) && $job['global_type'] === 'customer';
                $isConfirmedOther = array_key_exists('confirm_status', $job) && $job['confirm_status'] == 1;
                    if (!$isCustomer && !$isConfirmedOther) {
                        continue;
                }
    
                if (!empty($job['base_fare']) || !empty($job['toll_fare'])) {
                    $job['fare'] = (string) ((int) $job['base_fare'] + (int) $job['toll_fare']);
                }
    
                if ($loc) {
                    $fromPlace = strtolower($job['from_place'] ?? '');
                    $match = collect($loc)->contains(function ($l) use ($fromPlace) {
                        return str_contains($fromPlace, strtolower($l));
                    });
                    if (!$match) continue;
                }
    
                if ($distance && ($job['distance'] ?? 0) > $distance) continue;
                if ($min_distance && ($job['distance'] ?? 0) < $min_distance) continue;
    
                if ($pass && ($job['pass_count'] ?? 0) > $pass) continue;
                if ($min_pass && ($job['pass_count'] ?? 0) < $min_pass) continue;
                
                $job['pass_count'] = $pass_c == 'mini' ? '4 Mini' : $pass_c -1;
    
                if ($fare && ($job['fare'] ?? 0) > $fare) continue;
                if ($min_fare && ($job['fare'] ?? 0) < $min_fare) continue;
    
                $pickup = Carbon::parse($job['pickup_date'])->setTimezone('Asia/Kolkata');
                $now = Carbon::now('Asia/Kolkata');
                
                if ($pickup->lt($now)) {
                    continue;
                }
                
                if ($fromDate && $toDate && !empty($job['pickup_date'])) {
                
                    if ($pickup->lt($fromDate) || $pickup->gt($toDate)) {
                        continue;
                    }
                }
                
                $job['pickup_date'] = $pickup->format('Y-m-d H:i:s');
    
                $firebaseJobs[] = (object) $job;
            }
    
    
            $merged = collect($firebaseJobs)
                ->unique('job_no')
                ->sortByDesc('created_at')
                ->values();
    
            $finalJobs = $merged->slice($skip, $perPage)->values();
    
            return response()->json([
                'status' => true,
                'data' => [
                    'jobs' => $finalJobs,
                    'next_page' => $merged->count() > ($skip + $perPage)
                        ? $page + 1
                        : null
                ],
                'message' => 'Job Retrieved Successfully',
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            Log::error('FCM send error for token: ', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_all_jobsOldDB(Request $request)
    {
        try {
    
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            $request->validate([
                'location'      => ['nullable', 'array'],
                'location.*'    => ['nullable', 'string', 'max:255'],
                'distance'      => ['nullable', 'numeric', 'min:0'],
                'min_distance'  => ['nullable', 'numeric', 'min:0'],
                'pass'          => ['nullable', 'numeric', 'min:0'],
                'min_pass'      => ['nullable', 'numeric', 'min:0'],
                'fare'          => ['nullable', 'numeric', 'min:0'],
                'min_fare'      => ['nullable', 'numeric', 'min:0'],
                'fromDate'      => ['nullable', 'date'],
                'toDate'        => ['nullable', 'date'],
            ]);
    
            $userId = auth()->id();
    
            $loc          = $request->location ? array_filter($request->location) : null;
            $distance     = $request->distance ?: null;
            $min_distance = $request->min_distance ?: null;
            $pass         = $request->pass ?: null;
            $min_pass     = $request->min_pass ?: null;
            $fare         = $request->fare ?: null;
            $min_fare     = $request->min_fare ?: null;
            $fromDate     = $request->fromDate ?: null;
            $toDate       = $request->toDate ?: null;
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
        
                $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                $pass = $rcVehicle['seat_capacity'] ?? null;
                // $body_type = $rcVehicle['body_type'] ?? null;
        
            } else {
    
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $userId)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    
                    $pass = $get_ocr->seater ?? null;
                    if($pass == 'mini' || $pass == 'Mini' || $pass == 'Mini 4'){
                        $pass = 4;
                    }
                    $pass += 1;
                }
            }
    
            $page    = (int) $request->input('page', 1);
            $perPage = 3;
            $skip    = ($page - 1) * $perPage;
    
            // Role check
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->first();
    
            if (auth()->user()->isOpenjob == '0' && $check_role) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'jobs' => [],
                        'next_page' => null
                    ],
                    'message' => 'Job Retrieved Successfully',
                ], 200);
            }
    
            // $userId = $user->id;
            $now = Carbon::now();
            $arr_sat = ['bidding', 'created'];
            
            $firebaseDocs = DB::table('cus_job_temp as oj')
                ->where('oj.user_id', '!=', $userId)
                ->where('oj.deletes', '0')
                ->whereIn('oj.job_status', $arr_sat)
                ->where('oj.created_at', '>=', now()->subDays(10))
                ->where('oj.pickup_date', '>=', now())
                ->where(function ($q) {
                    $q->where('oj.global_type', 'customer')
                      ->orWhere('oj.confirm_status', 1);
                })
                ->orderByDesc('oj.created_at')
                ->get();
            
            $firebaseJobs = [];
            
            foreach ($firebaseDocs as $job) {
            
                $bids = is_array($job->bids_details) ? $job->bids_details : json_decode($job->bids_details, true) ?? [];
                $likedUsers = is_array($job->liked_users) ? $job->liked_users : json_decode($job->liked_users, true) ?? [];
            
                $job->user_bid = isset($bids[$userId]) ? 'yes' : 'no';
                $job->bids_count = count($bids);
                $job->user_like_status = in_array($userId, $likedUsers) ? '1' : '0';
                $job->source = 'firebase';
            
                $originalPass = $job->pass_count;
                $job->pass_count = ($originalPass === 'mini') ? 5 : $originalPass;
            
                if (!empty($job->base_fare) || !empty($job->toll_fare)) {
                    $job->fare = (int)$job->base_fare + (int)$job->toll_fare;
                }
            
                if (!empty($loc)) {
                    $fromPlace = strtolower($job->from_place ?? '');
                    $match = false;
            
                    foreach ($loc as $l) {
                        if (str_contains($fromPlace, strtolower($l))) {
                            $match = true;
                            break;
                        }
                    }
            
                    if (!$match) continue;
                }
            
                if ($distance && ($job->distance ?? 0) > $distance) continue;
                if ($min_distance && ($job->distance ?? 0) < $min_distance) continue;
            
                if ($pass && ($job->pass_count ?? 0) > $pass) continue;
                if ($min_pass && ($job->pass_count ?? 0) < $min_pass) continue;
            
                $job->pass_count = ($originalPass == 'mini') ? '4 Mini' : $originalPass - 1;
            
                if ($fare && ($job->fare ?? 0) > $fare) continue;
                if ($min_fare && ($job->fare ?? 0) < $min_fare) continue;
            
                $pickup = Carbon::parse($job->pickup_date)->timezone('Asia/Kolkata');
            
                if ($pickup->lt($now)) continue;
            
                if ($fromDate && $toDate) {
                    if ($pickup->lt($fromDate) || $pickup->gt($toDate)) continue;
                }
            
                $job->pickup_date = $pickup->format('Y-m-d H:i:s');
            
                if (($job->job_type ?? '') == 'roundtrip') {
                    $job->dropoff_date = null;
                }
            
                $job->add_fare_details = '{"bata":"Included","toll":"Included","parking":"Included"}';
            
                $firebaseJobs[] = $job;
            }
            
            $merged = collect($firebaseJobs)
                ->unique('job_no')
                ->values();
            
            $finalJobs = $merged->slice($skip, $perPage)->values();
            
            return response()->json([
                'status' => true,
                'data' => [
                    'jobs' => $finalJobs,
                    'next_page' => $merged->count() > ($skip + $perPage)
                        ? $page + 1
                        : null
                ],
                'message' => 'Job Retrieved Successfully',
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            Log::error('FCM send error for token: ', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_all_jobs(Request $request)
    {
        try {
    
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Unauthorized',
                ], 401);
            }
    
            $request->validate([
                'location'      => ['nullable', 'array'],
                'location.*'    => ['nullable', 'string', 'max:255'],
                'distance'      => ['nullable', 'numeric', 'min:0'],
                'min_distance'  => ['nullable', 'numeric', 'min:0'],
                'pass'          => ['nullable', 'numeric', 'min:0'],
                'min_pass'      => ['nullable', 'numeric', 'min:0'],
                'fare'          => ['nullable', 'numeric', 'min:0'],
                'min_fare'      => ['nullable', 'numeric', 'min:0'],
                'fromDate'      => ['nullable', 'date'],
                'toDate'        => ['nullable', 'date'],
            ]);
    
            $userId = auth()->id();
    
            $loc          = $request->location ? array_filter($request->location) : null;
            $distance     = $request->distance ?: null;
            $min_distance = $request->min_distance ?: null;
            $pass         = $request->pass ?: null;
            $min_pass     = $request->min_pass ?: null;
            $fare         = $request->fare ?: null;
            $min_fare     = $request->min_fare ?: null;
            $fromDate     = $request->fromDate ?: null;
            $toDate       = $request->toDate ?: null;
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
        
                $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                $pass = $rcVehicle['seat_capacity'] ?? null;
                // $body_type = $rcVehicle['body_type'] ?? null;
        
            } else {
    
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $userId)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    
                    $pass = $get_ocr->seater ?? null;
                    if($pass == 'mini' || $pass == 'Mini' || $pass == 'Mini 4'){
                        $pass = 4;
                    }
                    // $pass += 1;
                }
            }
            
            // dd('hiii');
    
            $page    = (int) $request->input('page', 1);
            $perPage = 3;
            $skip    = ($page - 1) * $perPage;
    
            // Role check
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->first();
    
            if (auth()->user()->isOpenjob == '0' && $check_role) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'jobs' => [],
                        'next_page' => null
                    ],
                    'message' => 'Job Retrieved Successfully',
                ], 200);
            }
            
            $u_state = auth()->user()->state;
    
            // $userId = $user->id;
            $now = Carbon::now();
            $arr_sat = ['bidding', 'created'];
            $firebaseJobs = [];
            
            $firebaseDocs = Cache::remember('jobs_list_global', 5, function () {
                return DB::table('cus_job_temp as oj')
                    ->where('oj.deletes', '0')
                    ->whereIn('oj.job_status', ['bidding', 'created'])
                    ->where('oj.created_at', '>=', now()->subDays(10))
                    ->where('oj.pickup_date', '>=', now())
                    // ->where('oj.user_id', '!=', 0)
                    ->whereNot('oj.global_type', 'carpool')
                    ->where(function ($q) {
                        $q->where('oj.global_type', 'customer')
                          ->orWhere('oj.confirm_status', 1);
                    })
                    ->orderByDesc('oj.created_at')
                    ->get();
            });
            
            $firebaseJobs = collect($firebaseDocs)
                // ✅ remove current user jobs early
                // ->where('user_id', '!=', $userId)
            
                // ✅ transform data
                ->map(function ($job) use ($userId) {
            
                    $bids = is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true) ?? [];
            
                    $likedUsers = is_array($job->liked_users)
                        ? $job->liked_users
                        : json_decode($job->liked_users, true) ?? [];
            
                    $job->user_bid = isset($bids[$userId]) ? 'yes' : 'no';
                    $job->bids_count = count($bids);
                    $job->user_like_status = in_array($userId, $likedUsers) ? '1' : '0';
                    $job->source = 'firebase';
            
                    $originalPass = $job->pass_count;
                    $job->pass_count = ($originalPass === 'mini') ? 5 : $originalPass;
            
                    $job->fare = $job->global_type != 'open' && $job->global_type != 'mock' ? (int)($job->base_fare ?? 0) + (int)($job->toll_fare ?? 0) : $job->fare;
                    
                    // if($job->global_type == 'mock'){
                    //     $job->base_fare = $job->fare;
                    // }
            
                    $job->original_pass = $originalPass;
            
                    return $job;
                })
            
                // ✅ apply filters (FAST exit)
                ->filter(function ($job) use (
                    $loc, $distance, $min_distance,
                    $pass, $min_pass, $fare, $min_fare,
                    $fromDate, $toDate, $now, $u_state
                ) {
            
                    // Location filter
                    if (!empty($loc)) {
                        $fromPlace = strtolower($job->from_place ?? '');
                        $match = false;
            
                        foreach ($loc as $l) {
                            if (str_contains($fromPlace, strtolower($l))) {
                                $match = true;
                                break;
                            }
                        }
            
                        if (!$match) return false;
                    }
                    
                    $locations = array_map('trim', explode(',', $job->from_place));
                    
                    if (!in_array($u_state, $locations)) {
                        return false;
                    }
            
                    if ($distance && ($job->distance ?? 0) > $distance) return false;
                    if ($min_distance && ($job->distance ?? 0) < $min_distance) return false;
            
                    if ($pass && ($job->pass_count ?? 0) > $pass) return false;
                    if ($min_pass && ($job->pass_count ?? 0) < $min_pass) return false;
            
                    if ($fare && ($job->fare ?? 0) > $fare) return false;
                    if ($min_fare && ($job->fare ?? 0) < $min_fare) return false;
            
                    $pickup = Carbon::parse($job->pickup_date)->timezone('Asia/Kolkata');
            
                    if ($pickup->lt($now)) return false;
            
                    if ($fromDate && $toDate) {
                        if ($pickup->lt($fromDate) || $pickup->gt($toDate)) return false;
                    }
            
                    // store processed pickup
                    $job->pickup_date = $pickup->format('Y-m-d H:i:s');
            
                    return true;
                })
            
                // ✅ final formatting
                ->map(function ($job) {
            
                    $job->pass_count = ($job->original_pass == 'mini')
                        ? '4 Mini'
                        : ($job->global_type == 'open' || $job->global_type == 'mock' ? $job->original_pass : $job->original_pass - 1);
            
                    if (($job->job_type ?? '') == 'roundtrip') {
                        $job->dropoff_date = null;
                    }
            
                    $job->add_fare_details = '{"bata":"Included","toll":"Included","parking":"Included"}';
            
                    unset($job->original_pass);
            
                    return $job;
                })
            
                ->values();
            
            $merged = collect($firebaseJobs)
                ->unique('job_no')
                ->values();
            
            $finalJobs = $merged->slice($skip, $perPage)->values();
            
            return response()->json([
                'status' => true,
                'data' => [
                    'jobs' => $finalJobs,
                    'next_page' => $merged->count() > ($skip + $perPage)
                        ? $page + 1
                        : null
                ],
                'message' => 'Job Retrieved Successfully',
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            Log::error('FCM send error for token: ', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function notify_jobs(Request $request)
    {
        
        try{
            
            if(true){
                
                $data = [];
                
                // $userId = auth()->user()->id;
                
                $arr_sat = ['bidding', 'created'];
                
                $get_jobs = DB::table('open_jobs as oj')
                    ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
                    ->where('oj.pickup_date', '>=', Carbon::now())
                    ->where('oj.deletes', '=', '0')
                    ->whereIn('oj.job_status', $arr_sat)
                
                
                    // ->whereRaw("
                    //     (
                    //         JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$userId\"') = 0
                    //         OR (
                    //             JSON_SEARCH(oj.bids_details, 'one', 'accept', NULL, '$.\"$userId\".status') IS NULL
                    //             AND JSON_SEARCH(oj.bids_details, 'one', 'reject', NULL, '$.\"$userId\".status') IS NULL
                    //         )
                    //     )
                    // ")
                
                    ->select(
                        'oj.id',
                        'oj.pickup_date',
                        'oj.dropoff_date',
                        'oj.job_type',
                        DB::raw("SUBSTRING_INDEX(oj.from_place, ',', 1) as from_place"),
                        DB::raw("SUBSTRING_INDEX(oj.to_place, ',', 1) as to_place")
                    )
                    ->orderBy('oj.id', 'DESC')
                    
                    ->limit(4)
                    ->get();
                
                
                    
                $data['jobs'] = $get_jobs;
                // $data['next_page'] = count($get_jobs) == $perPage ? $page + 1 : null;
                    
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Job Retrieved Successfully',
                ], 200);
                    
                
            }else{
                return response()->json([
                    'status' => false,
                    'data' => '',
                    'message' => 'Unautherized',
                ], 401);
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
        
    }
    
    public function my_current_jobsOld(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $userId = auth()->id();
    
        $jobs = DB::table('cus_job_temp as oj')
            ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
            ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
            ->where('oj.pickup_date', '>=', Carbon::now())
            ->where('oj.user_id', $userId)
            ->where('oj.global_type', 'open')
            ->where('oj.deletes', '0')
            ->where('ur.deletes', '0')
            ->whereNotIn('oj.job_status', ['expiried', 'cancelled', 'no_response'])
            ->select(
                'oj.*',
                'ur.name as poster_name',
                'ur.email as poster_email',
                'ur.mobile as poster_mobile'
            )
            ->orderByDesc('oj.id')
            ->limit(20)
            ->get();
    
        if ($jobs->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => ['jobs' => []],
                'message' => 'Job Retrieved Successfully',
            ]);
        }
    
        /*
        |--------------------------------------------------------------------------
        | STEP 1: FIREBASE FETCH
        |--------------------------------------------------------------------------
        */
        $firebase = new \App\Services\FirebaseJobService(
            $this->serviceAccount['project_id'],
            $this->getAccessToken()
        );
    
        $firebaseBids = $firebase->getMyJobs(
            $jobs->pluck('job_no')->toArray()
        );
    
        /*
        |--------------------------------------------------------------------------
        | STEP 2: MERGE FIREBASE + DB BIDS
        |--------------------------------------------------------------------------
        */
        $allBids = [];
    
        foreach ($jobs as $job) {
    
            // ✅ Priority: DB → Firebase
            if (!empty($job->bids_details)) {
    
                $allBids[$job->job_no] = json_decode($job->bids_details, true);
    
            } else {
    
                $allBids[$job->job_no] = $firebaseBids[$job->job_no] ?? [];
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | STEP 3: COLLECT ALL BIDDER IDS
        |--------------------------------------------------------------------------
        */
        $allBidderIds = collect($allBids)
            ->flatMap(fn($bids) => array_keys($bids))
            ->filter()
            ->unique()
            ->values();
    
        $users = DB::table('user_register')
            ->whereIn('id', $allBidderIds)
            ->where('deletes', '0')
            ->get()
            ->keyBy('id');
    
        /*
        |--------------------------------------------------------------------------
        | STEP 4: FORMAT RESPONSE
        |--------------------------------------------------------------------------
        */
        $formattedJobs = $jobs->map(function ($job) use ($allBids, $users) {
    
            $jobBids = $allBids[$job->job_no] ?? [];
            $bidders = [];
    
            foreach ($jobBids as $bidderId => $bid) {
    
                // ❌ Skip rejected
                if (($bid['status'] ?? null) === 'reject') {
                    continue;
                }
    
                $user = $users[$bidderId] ?? null;
                if (!$user) continue;
    
                $bidders[] = [
                    'bidder_id'     => $user->id,
                    'bidder_name'   => $user->name,
                    'bidder_email'  => $user->email,
                    'bidder_mobile' => $user->mobile,
                    'amount'        => $bid['amount'] ?? null,
                    'remark'        => $bid['remark'] ?? null,
                    'status'        => $bid['status'] ?? 'pending',
                ];
            }
    
            return [
                'id'             => $job->id,
                'job_no'         => $job->job_no,
                'from_place'     => $job->from_place,
                'to_place'       => $job->to_place,
                'pickup_date'    => $job->pickup_date,
                'dropoff_date'   => $job->dropoff_date,
                'pass_count'     => $job->pass_count,
                'fare'           => $job->fare,
                'distance'       => $job->distance,
                'duration'       => $job->duration,
                'job_status'     => $job->job_status,
                'payment_status' => $job->payment_status,
                'created_at'     => $job->created_at,
                'poster_name'    => $job->poster_name,
                'poster_email'   => $job->poster_email,
                'poster_mobile'  => $job->poster_mobile,
                'bidders'        => $bidders,
            ];
        });
    
        return response()->json([
            'status' => true,
            'data' => [
                'jobs' => $formattedJobs
            ],
            'message' => 'Job Retrieved Successfully',
        ]);
    }
    
    public function my_current_jobsOldDB(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $userId = auth()->id();
    
        $jobs = DB::table('cus_job_temp as oj')
            ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
            ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
            ->where('oj.pickup_date', '>=', Carbon::now())
            ->where('oj.user_id', $userId)
            ->where('oj.global_type', 'open')
            ->where('oj.deletes', '0')
            ->where('ur.deletes', '0')
            ->whereNotIn('oj.job_status', ['expiried', 'cancelled', 'no_response'])
            ->select(
                'oj.*',
                'ur.name as poster_name',
                'ur.email as poster_email',
                'ur.mobile as poster_mobile'
            )
            ->orderByDesc('oj.id')
            ->limit(20)
            ->get();
    
        if ($jobs->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => ['jobs' => []],
                'message' => 'Job Retrieved Successfully',
            ]);
        }
    
        /*
        |--------------------------------------------------------------------------
        | STEP 1: FIREBASE FETCH
        |--------------------------------------------------------------------------
        */
        // $firebase = new \App\Services\FirebaseJobService(
        //     $this->serviceAccount['project_id'],
        //     $this->getAccessToken()
        // );
    
        // $firebaseBids = $firebase->getMyJobs(
        //     $jobs->pluck('job_no')->toArray()
        // );
        
        $firebaseBids = [];
    
        $allBids = [];
    
        foreach ($jobs as $job) {
    
            if (!empty($job->bids_details)) {
    
                $allBids[$job->job_no] = json_decode($job->bids_details, true);
    
            } else {
    
                $allBids[$job->job_no] = $firebaseBids[$job->job_no] ?? [];
            }
        }
    
        $allBidderIds = collect($allBids)
            ->flatMap(fn($bids) => array_keys($bids))
            ->filter()
            ->unique()
            ->values();
    
        $users = DB::table('user_register')
            ->whereIn('id', $allBidderIds)
            ->where('deletes', '0')
            ->get()
            ->keyBy('id');
    
        $formattedJobs = $jobs->map(function ($job) use ($allBids, $users) {
    
            $jobBids = $allBids[$job->job_no] ?? [];
            $bidders = [];
    
            foreach ($jobBids as $bidderId => $bid) {
    
                if (($bid['status'] ?? null) === 'reject') {
                    continue;
                }
    
                $user = $users[$bidderId] ?? null;
                if (!$user) continue;
    
                $bidders[] = [
                    'bidder_id'     => $user->id,
                    'bidder_name'   => $user->name,
                    'bidder_email'  => $user->email,
                    'bidder_mobile' => $user->mobile,
                    'amount'        => $bid['amount'] ?? null,
                    'remark'        => $bid['remark'] ?? null,
                    'status'        => $bid['status'] ?? 'pending',
                ];
            }
    
            return [
                'id'             => $job->id,
                'job_no'         => $job->job_no,
                'from_place'     => $job->from_place,
                'to_place'       => $job->to_place,
                'pickup_date'    => $job->pickup_date,
                'dropoff_date'   => $job->dropoff_date,
                'pass_count'     => $job->pass_count,
                'fare'           => $job->fare,
                'distance'       => $job->distance,
                'duration'       => $job->duration,
                'job_status'     => $job->job_status,
                'payment_status' => $job->payment_status,
                'created_at'     => $job->created_at,
                'poster_name'    => $job->poster_name,
                'poster_email'   => $job->poster_email,
                'poster_mobile'  => $job->poster_mobile,
                'bidders'        => $bidders,
            ];
        });
    
        return response()->json([
            'status' => true,
            'data' => [
                'jobs' => $formattedJobs
            ],
            'message' => 'Job Retrieved Successfully',
        ]);
    }
    
    public function my_current_jobs(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $userId = auth()->id();
    
        $cacheKey = "current_jobs_{$userId}";

        // ✅ Cache only the main jobs query
        $jobs = Cache::remember($cacheKey, 5, function () use ($userId) {
    
            return DB::table('cus_job_temp as oj')
                ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
                ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
                ->where('oj.pickup_date', '>=', Carbon::now())
                ->where('oj.user_id', $userId)
                ->whereIn('oj.global_type', ['open', 'mock'])
                // ->where('oj.global_type', 'open')
                ->where('oj.deletes', '0')
                ->where('ur.deletes', '0')
                ->whereNotIn('oj.job_status', ['expiried', 'cancelled', 'no_response'])
                ->select(
                    'oj.*',
                    'ur.name as poster_name',
                    'ur.email as poster_email',
                    'ur.mobile as poster_mobile'
                )
                ->orderByDesc('oj.id')
                ->limit(20)
                ->get();
        });
    
        if ($jobs->isEmpty()) {
            return response()->json([
                'status' => true,
                'data' => ['jobs' => []],
                'message' => 'Job Retrieved Successfully',
            ]);
        }
    
        /*
        |--------------------------------------------------------------------------
        | STEP 1: FIREBASE FETCH
        |--------------------------------------------------------------------------
        */
        // $firebase = new \App\Services\FirebaseJobService(
        //     $this->serviceAccount['project_id'],
        //     $this->getAccessToken()
        // );
    
        // $firebaseBids = $firebase->getMyJobs(
        //     $jobs->pluck('job_no')->toArray()
        // );
        
        $firebaseBids = [];
    
        $allBids = [];
    
        foreach ($jobs as $job) {
    
            if (!empty($job->bids_details)) {
                $allBids[$job->job_no] = json_decode($job->bids_details, true);
            } else {
                $allBids[$job->job_no] = $firebaseBids[$job->job_no] ?? [];
            }
        }
    
        // ✅ Collect bidder IDs
        $allBidderIds = collect($allBids)
            ->flatMap(fn($bids) => array_keys($bids))
            ->filter()
            ->unique()
            ->values();
    
        // ❌ Don't cache this (changes frequently per request)
        $users = DB::table('user_register')
            ->whereIn('id', $allBidderIds)
            ->where('deletes', '0')
            ->get()
            ->keyBy('id');
    
        // ✅ Format response
        $formattedJobs = $jobs->map(function ($job) use ($allBids, $users) {
    
            $jobBids = $allBids[$job->job_no] ?? [];
            $bidders = [];
    
            foreach ($jobBids as $bidderId => $bid) {
    
                if (($bid['status'] ?? null) === 'reject') continue;
    
                $user = $users[$bidderId] ?? null;
                if (!$user) continue;
    
                $bidders[] = [
                    'bidder_id'     => $user->id,
                    'bidder_name'   => $user->name,
                    'bidder_email'  => $user->email,
                    'bidder_mobile' => $user->mobile,
                    'amount'        => $bid['amount'] ?? null,
                    'remark'        => $bid['remark'] ?? null,
                    'status'        => $bid['status'] ?? 'pending',
                ];
            }
    
            return [
                'id'             => $job->id,
                'job_no'         => $job->job_no,
                'from_place'     => $job->from_place,
                'to_place'       => $job->to_place,
                'pickup_date'    => $job->pickup_date,
                'dropoff_date'   => $job->dropoff_date,
                'pass_count'     => $job->pass_count,
                'fare'           => $job->fare,
                'distance'       => $job->distance,
                'duration'       => $job->duration,
                'job_status'     => $job->job_status,
                'payment_status' => $job->payment_status,
                'created_at'     => $job->created_at,
                'poster_name'    => $job->poster_name,
                'poster_email'   => $job->poster_email,
                'poster_mobile'  => $job->poster_mobile,
                'bidders'        => $bidders,
            ];
        });
    
        return response()->json([
            'status' => true,
            'data' => [
                'jobs' => $formattedJobs
            ],
            'message' => 'Job Retrieved Successfully',
        ]);
    }
    
    public function my_past_jobs(Request $request)
    {
        if(auth()->user()->id != ''){
            
            $data = [];
            
            $pre_loc = json_decode(auth()->user()->prefered_location);
            
            $arr_sat = ['accept', 'expiried', 'cancelled', 'bidding', 'created'];
            
            $get_jobs = DB::table('cus_job_temp as oj')
                    ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
                    ->where('oj.user_id', auth()->user()->id)
                    ->whereIn('oj.job_status', $arr_sat)
                    ->whereIn('oj.global_type', ['open', 'mock'])
                //   ->when(in_array('cancelled', $arr_sat), function ($query) {
                //         // Case 1: If job_status = cancelled
                //         $query->where('oj.job_status', 'cancelled')
                //               ->where(function ($q) {
                //                   $q->where('oj.pickup_date', '<', now())
                //                     ->orWhere('oj.pickup_date', '>', now());
                //               });
                //     }, function ($query) {
                //         // Case 2: If job_status != cancelled
                //         $query->where('oj.pickup_date', '<', now());
                //     })
                    ->where('oj.pickup_date', '<', now())
                    ->where('oj.deletes', '=', '0')
                    ->where('ur.deletes', '=', '0')
                    ->select(
                        'oj.*',
                        'ur.name as poster_name',
                        'ur.email as poster_email',
                        'ur.mobile as poster_mobile',
                        DB::raw("
                            CASE
                                WHEN oj.job_status = 'accept' THEN 'completed'
                                WHEN oj.job_status = 'cancelled' THEN 'cancelled'
                                WHEN oj.bids_details IS NULL AND oj.pickup_date < NOW() THEN 'expired'
                                WHEN oj.bids_details IS NOT NULL
                                     AND oj.job_status = 'bidding' 
                                     AND oj.pickup_date < NOW() THEN 'no-response'
                            END as final_status
                        ")
                    )
                    ->orderBy('oj.id', 'DESC')
                    ->limit(20)
                    ->get();
                
            $data['jobs'] = $get_jobs;
                
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Job Retrieved Successfully',
            ], 200);
                
            
        }else{
            return response()->json([
                'status' => false,
                'data' => '',
                'message' => 'Unautherized',
            ], 401);
        }
    }
    
    public function create_bidNewVehicle(Request $request)
    {
        try {
    
            $request->validate([
                'job_id'   => ['required', 'string', 'max:255'],
                'amount'   => ['required', 'string', 'max:8'],
                'remark'   => ['nullable', 'string', 'max:255'],
                // 'driver_id'=> ['nullable', 'integer'],
                'vehicle_no'=> ['nullable', 'string'],
            ]);
    
            $userId = auth()->user()->id;
    
            $data = [];
            $new_bid = [
                'amount' => (int) $request->amount,
                'remark' => $request->remark,
                'status' => 'inreview',
            ];
            
            $job_ex = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->whereIn('job_status', ['accept', 'cancelled'])
                ->exists();
            
            if ($job_ex) {
                return response()->json([
                    'status' => true,
                    'message' => 'Job already cancelled or assigned',
                ], 200);
            }
    
            if (auth()->user()->doc_verify == 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            $user = auth()->user();
            $kyc_id = DB::table('kyc_details')->where(['user_id' => $user->id])->first();
    
            $check_u = DB::table('user_register')
                ->join('kyc_details', 'kyc_details.user_id', '=', 'user_register.id')
                ->where([
                    'user_register.id' => $userId,
                    'user_register.deletes' => '0',
                    'kyc_details.deletes' => '0',
                    'kyc_details.type' => 'Owner'
                ])
                ->first();
                
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->where('user_id', '!=', $userId)
                ->first();
    
            // if ($check_u) {
            //     if (!empty(auth()->user()->drivers_ids)) {
            //         return response()->json([
            //             'status' => false,
            //             'data' => null,
            //             'message' => 'Please add drivers before proceeding.'
            //         ]);
            //     }
            // } else {
            //     if (auth()->user()->isBidding == 0) {
            //         return response()->json([
            //             'status' => false,
            //             'data' => null,
            //             'message' => "You don't have permission to place a bid."
            //         ]);
            //     }
            // }
    
            if ($kyc_id->type == 'Owner') {
                
                if($request->vehicle_no){
                    $check_v = DB::table('owner_vehicle_list')->where(['user_id' => $user->id, 'rc_number' => $request->vehicle_no])->first();
                    
                    if($check_v){
                        
                        if($check_v->verification_status != 2){
                            return response()->json([
                                'status' => false,
                                'data' => null,
                                'user_type' => $kyc_id?->type,
                                'message' => 'Your vehicle is under review. An admin will verify it shortly.'
                            ], 200);
                        }
                        
                    }else{
                        return response()->json([
                            'status' => false,
                            'data' => null,
                            'user_type' => $kyc_id?->type,
                            'message' => 'Vehicle add Pending.'
                        ], 200);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'user_type' => $kyc_id?->type,
                        'message' => 'Please select your vehicle.'
                    ], 200);
                }
    
                
                
            }else if(auth()->user()->vehicle_verify < 1){
                
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $kyc_id?->type,
                    'message' => 'Vehicle add Pending.'
                ], 200);
                
            }else if(auth()->user()->vehicle_verify < 2){
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $kyc_id?->type,
                    'message' => 'Your vehicle is under review. An admin will verify it shortly.'
                ], 200);
            }
    
            
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Job not found or Can\'t Bid own Job'
                ]);
            }
    
            if ($job->global_type == 'mock') {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'You cannot place a bid on a mock job.'
                ]);
            }
    
            // $get_type_2 = DB::table('kyc_details')
            //     ->where(['user_id' => $userId, 'deletes' => 0])
            //     ->first();
    
            // if ($get_type_2 && $get_type_2->type == 'Owner') {
    
            //     if (!$request->driver_id) {
            //         return response()->json([
            //             'status' => false,
            //             'message' => 'Select driver to bid job.',
            //             'data' => []
            //         ], 200);
            //     }
    
            //     $checkOwner = DB::table('user_register')
            //         ->where('id', $userId)
            //         ->whereRaw(
            //             "JSON_CONTAINS(drivers_ids, JSON_QUOTE(?))",
            //             [$request->driver_id]
            //         )
            //         ->first();
    
            //     if (!$checkOwner) {
            //         return response()->json([
            //             'status' => false,
            //             'message' => 'Driver not included under your account.',
            //             'data' => []
            //         ], 200);
            //     }
            // }
            

            $seat = null;
            $body_type = null;
            $language = null;
            $luggage = null;
            
            // if ($user->vehicle_details) {
            
            //     $vehicleDetails = json_decode($user->vehicle_details, true);
            
            //     if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
            //         $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
            //         $seat = $rcVehicle['seat_capacity'] ?? null;
            //         $body_type = $rcVehicle['body_type'] ?? null;
            
            //     } else {
        
            //         $get_ocr = DB::table('ocr_request')
            //             ->where('user_id', $user->id)
            //             ->where('doc_type', 'RC')
            //             ->orderByDesc('id')
            //             ->first();
            
            //         if ($get_ocr) {
            //             $seat = $get_ocr->seater ?? null;
            //         }
            //     }
            
            //     if (!empty($vehicleDetails['user_info'])) {
            
            //         $language = $vehicleDetails['user_info']['language'] ?? null;
            //         $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
            //     }
            // }else{
                
            //     $get_ocr = DB::table('ocr_request')
            //         ->where('user_id', $user->id)
            //         ->where('doc_type', 'RC')
            //         ->orderByDesc('id')
            //         ->first();
        
            //     if ($get_ocr) {
            //         $seat = $get_ocr->seater ?? null;
            //     }
                
            // }
            
            if ($user->vehicle_details && $kyc_id->type == 'Driver') {
            
                $vehicleDetails = json_decode($user->vehicle_details, true);
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $seat = $rcVehicle['seat_capacity'] ?? null;
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else if ($kyc_id->type == 'Owner'){
                
                $vehicleDetails = json_decode($check_v->vehicle_details, true);
                $seat = $check_v->seater;
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else{
                
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $user->id)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    $seat = $get_ocr->seater ?? null;
                }
                
            }
            
            $base_fare  = (int) $request->amount;
            
            if(($job->toll_fare + $job->base_fare) == $base_fare){
                
                $com = round(($base_fare) *0.05);
                $show_amount = $job->base_fare + $com;
            }else{
                $base_fare = $base_fare - $job->toll_fare;
                $com = round(($base_fare + $job->toll_fare) *0.05);
                $show_amount = $base_fare + $com;
                
            }
            
            $newBid = [
                'amount'      => (int) $request->amount,
                'show_amount' => (int) $show_amount,
                'b_name'      => auth()->user()->name,
                'kyc_id'      => $kyc_id->id ?? null,
                'b_mobile'    => auth()->user()->mobile,
                'b_image'     => auth()->user()->profile_img_url ?? ($kyc_id->selfie_url ?? null),
                'b_rating'    => auth()->user()->ratings,
                'b_seater'    => $seat,
                'b_cab'       => $body_type,
                'b_luggage'   => $luggage,
                'b_language'  => $language,
                'b_cab_no' => $kyc_id->type == 'Owner' ? $request->vehicle_no : '',
                'remark'      => $request->remark,
            ];
            
            /* -----------------------------
               FIREBASE (fast inline)
            ------------------------------*/
            (new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            ))->placeBid($job->job_no, $userId, $newBid);
            
            if ($job->job_no) {

                $bids = $job->bids_details
                    ? (is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true))
                    : [];
            
                // O(1) update / insert
                $newBid['status'] = 'inreview';
                $bids[$userId] = $newBid;
            
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->update([
                        'bids_details' => json_encode($bids, JSON_UNESCAPED_UNICODE)
                    ]);
            }
    
            // if ($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id) {
            //     $firebase->assignDriver(
            //         $job->job_no,
            //         (int) $request->driver_id,
            //         $userId
            //     );
            // }
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Bid placed successfully.'
            ], 200);
    
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function create_bid(Request $request)
    {
        try {
    
            $request->validate([
                'job_id'   => ['required', 'string', 'max:255'],
                'amount'   => ['required', 'string', 'max:8'],
                'remark'   => ['nullable', 'string', 'max:255'],
                'driver_id'=> ['nullable', 'integer'],
            ]);
    
            $userId = auth()->user()->id;
            
            if (auth()->user()->walletBalance < 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance. Please top up to place a bid.',
                    'data'    => null,
                ]);
            }
            
            $activeJob = DB::table('cus_job_temp')
                ->where('assigned_to', $userId)
                ->whereIn('job_status', ['accept', 'started', 'created', 'bidding'])
                ->whereNot('id', $request->job_id)
                ->latest('id')
                ->first();
            
            if ($activeJob) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Please complete your current ride before accepting a new one.',
                    'data'    => [
                        'job_id'  => $activeJob->id,
                        'job_no'  => $activeJob->job_no ?? null
                    ]
                ], 200);
            }
    
            $data = [];
            $new_bid = [
                'amount' => (int) $request->amount,
                'remark' => $request->remark,
                'status' => 'inreview',
            ];
            
            $job_ex = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->whereIn('job_status', ['accept', 'cancelled'])
                ->exists();
            
            if ($job_ex) {
                return response()->json([
                    'status' => true,
                    'message' => 'Job already cancelled or assigned',
                ], 200);
            }
    
            if (auth()->user()->doc_verify == 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
    
            $check_u = DB::table('user_register')
                ->join('kyc_details', 'kyc_details.user_id', '=', 'user_register.id')
                ->where([
                    'user_register.id' => $userId,
                    'user_register.deletes' => '0',
                    'kyc_details.deletes' => '0',
                    'kyc_details.type' => 'Owner'
                ])
                ->first();
    
            if ($check_u) {
                if (!empty(auth()->user()->drivers_ids)) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'message' => 'Please add drivers before proceeding.'
                    ]);
                }
            } else {
                if (auth()->user()->isBidding == 0) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'message' => "You don't have permission to place a bid."
                    ]);
                }
            }
    
            if (auth()->user()->vehicle_verify == 0) {
    
                $get_type = DB::table('kyc_details')
                    ->where(['user_id' => $userId, 'deletes' => 0])
                    ->first();
    
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', $userId)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
    
                if ($get_crm && $get_type) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'user_type' => $get_type->type,
                        'url' => 'https://' . strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                }
    
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $get_type?->type,
                    'message' => 'Vehicle add Pending.'
                ], 200);
            }
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                // ->where('user_id', '!=', $userId)
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Job not found or Can\'t Bid own Job'
                ]);
            }
            
            if ($job && $job->user_id != 0) {
                // Determine the correct table
                $table = ($job->global_type == 'customer') ? 'customer_register' : 'user_register';
                
                // Fetch only the fcm_token column and assign it directly to the job object
                $job->fcm_token = DB::table($table)
                    ->where('id', $job->user_id)
                    ->value('fcm_token'); 
            }
    
            if ($job->global_type == 'mock') {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'You cannot place a bid on a mock job.'
                ]);
            }
    
            $get_type_2 = DB::table('kyc_details')
                ->where(['user_id' => $userId, 'deletes' => 0])
                ->first();
    
            if ($get_type_2 && $get_type_2->type == 'Owner') {
    
                if (!$request->driver_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Select driver to bid job.',
                        'data' => []
                    ], 200);
                }
    
                $checkOwner = DB::table('user_register')
                    ->where('id', $userId)
                    ->whereRaw(
                        "JSON_CONTAINS(drivers_ids, JSON_QUOTE(?))",
                        [$request->driver_id]
                    )
                    ->first();
    
                if (!$checkOwner) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Driver not included under your account.',
                        'data' => []
                    ], 200);
                }
            }
            
            $user = auth()->user();

            $seat = null;
            $body_type = null;
            $language = null;
            $luggage = null;
            
            if ($user->vehicle_details) {
            
                $vehicleDetails = json_decode($user->vehicle_details, true);
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $seat = $rcVehicle['seat_capacity'] ?? null;
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else{
                
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $user->id)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    $seat = $get_ocr->seater ?? null;
                }
                
            }
            
            $kyc_id = DB::table('kyc_details')->where(['user_id' => $user->id])->first();
    
            /* -----------------------------
               FIREBASE OPERATIONS
            ------------------------------*/
            // $firebase = new FirebaseJobService(
            //     $this->serviceAccount['project_id'],
            //     $this->getAccessToken()
            // );
            
            $base_fare  = (int) $request->amount;
            
            if(($job->toll_fare + $job->base_fare) == $base_fare){
                
                $com = round(($base_fare) *0.05);
                $show_amount = $job->base_fare + $com;
            }else{
                $base_fare = $base_fare - $job->toll_fare;
                $com = round(($base_fare + $job->toll_fare) *0.05);
                $show_amount = $base_fare + $com;
                
            }
             
            // $firebase->placeBid(
            //     $job->job_no,
            //     $userId,
            //     [
            //         'amount' => (int) $request->amount,
            //         'show_amount' => (int) $show_amount,
            //         'b_name' => auth()->user()->name,
            //         // 'b_image' => auth()->user()->profile_img_url,
            //         // 'b_rating' => auth()->user()->ratings,
            //         'kyc_id' => $kyc_id ? $kyc_id->id : null,
            //         'b_mobile' => auth()->user()->mobile,
            //         'b_image' => auth()->user()->profile_img_url ?? $kyc_id->selfie_url,
            //         'b_rating' => auth()->user()->ratings,
            //         'b_seater' => $seat,
            //         'b_cab' => $body_type,
            //         'b_luggage' => $luggage,
            //         'b_language' => $language,
                    
            //         'remark' => $request->remark
            //     ]
            // );
            
            $newBid = [
                'amount'      => (int) $request->amount,
                'show_amount' => (int) $show_amount,
                'b_name'      => auth()->user()->name,
                'kyc_id'      => $kyc_id->id ?? null,
                'b_mobile'    => auth()->user()->mobile,
                'b_image'     => auth()->user()->profile_img_url ?? ($kyc_id->selfie_url ?? null),
                'b_rating'    => auth()->user()->ratings,
                'b_seater'    => $seat,
                'b_cab'       => $body_type,
                'b_luggage'   => $luggage,
                'b_language'  => $language,
                'remark'      => $request->remark,
                'updated_at' => now()
            ];
            
            /* -----------------------------
               FIREBASE (fast inline)
            ------------------------------*/
            (new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            ))->placeBid($job->job_no, $userId, $newBid);
            
            if ($job->job_no) {

                $bids = $job->bids_details
                    ? (is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true))
                    : [];
            
                // O(1) update / insert
                $newBid['status'] = 'inreview';
                $bids[$userId] = $newBid;
            
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->update([
                        'bids_details' => json_encode($bids, JSON_UNESCAPED_UNICODE)
                    ]);
            }
    
            if ($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id) {
                $firebase->assignDriver(
                    $job->job_no,
                    (int) $request->driver_id,
                    $userId
                );
            }
            
            $user = auth()->user();

            $fcmTokens = !empty($job->fcm_token) ? [$job->fcm_token] : [];
            
            if (!empty($fcmTokens)) {
            
                $accessToken = $this->getAccessToken();
            
                if ($accessToken) {
            
                    $title = "🚗 A driver is interested!";
                    $body  = "{$user->name} wants to take your trip from {$job->from_place} to {$job->to_place}. Check the offer now.";
            
                    foreach ($fcmTokens as $token) {
            
                        if (empty($token)) continue; // safety
            
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            $title,
                            $body,
                            [
                                'job_id'     => (string) $job->id,
                                'type'       => 'incoming_bid',
                                'action'     => 'open_job',
                                'bid_user'   => (string) $user->id,
                                'from_place' => (string) $job->from_place,
                                'to_place'   => (string) $job->to_place,
                            ]
                        );
                    }
                }
            }
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Bid placed successfully.'
            ], 200);
    
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function admin_create_bid(Request $request)
    {
        try {
    
            $request->validate([
                'job_id'       => ['required', 'string', 'max:255'],
                'assignedBy_id'=> ['required', 'string', 'min:1'],
                'driver_id'    => ['required', 'string', 'min:1'],
                'amount'       => ['required', 'string', 'max:8'],
                'remark'       => ['nullable', 'string', 'max:255'],
            ]);
    
            $userId = $request->driver_id;
    
            // Fetch Driver Profile
            $check_ver = DB::table('user_register')
                ->where([
                    'id' => $userId,
                    'deletes' => '0'
                ])
                ->first();
                
            if (!$check_ver) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Driver not found.'
                ], 404);
            }

            // Early check for already assigned/cancelled jobs
            $job_ex = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->whereIn('job_status', ['accept', 'cancelled'])
                ->exists();
    
            if ($job_ex) {
                return response()->json([
                    'status' => true,
                    'message' => 'Job already cancelled or assigned',
                ], 200);
            }
    
            // KYC Checks
            if ($check_ver->doc_verify == 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
    
            if ($check_ver->vehicle_verify < 2) {
                $get_type = DB::table('kyc_details')
                    ->where(['user_id' => $userId, 'deletes' => 0])
                    ->first();
    
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $get_type?->type,
                    'message' => 'Vehicle add Pending.'
                ], 200);
            }
    
            // Job Checks
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->where('user_id', '!=', $userId)
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Job not found or Can\'t Bid own Job'
                ]);
            }

            // Fetch FCM Token for Customer Notification
            if ($job && $job->user_id != 0) {
                $table = ($job->global_type == 'customer') ? 'customer_register' : 'user_register';
                
                $job->fcm_token = DB::table($table)
                    ->where('id', $job->user_id)
                    ->value('fcm_token'); 
            }
    
            if ($job->global_type == 'mock') {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'You cannot place a bid on a mock job.'
                ]);
            }

            // -----------------------------------------------------------------
            // VEHICLE DETAILS EXTRACTION
            // -----------------------------------------------------------------
            $seat = null;
            $body_type = null;
            $language = null;
            $luggage = null;
            
            if ($check_ver->vehicle_details) {
            
                $vehicleDetails = json_decode($check_ver->vehicle_details, true);
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
                    $seat = $rcVehicle['seat_capacity'] ?? null;
                    $body_type = $rcVehicle['body_type'] ?? null;
                } else {
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $userId)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            } else {
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $userId)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    $seat = $get_ocr->seater ?? null;
                }
            }
            
            $kyc_id = DB::table('kyc_details')->where(['user_id' => $userId])->first();

            // -----------------------------------------------------------------
            // COMMISSION & FARE MATH
            // (Expects toll to be pre-added from frontend JS)
            // -----------------------------------------------------------------
            $base_fare  = (int) $request->amount;
            
            if (($job->toll_fare + $job->base_fare) == $base_fare) {
                $com = round(($base_fare) * 0.05);
                $show_amount = $job->base_fare + $com;
            } else {
                $base_fare = $base_fare - $job->toll_fare;
                $com = round(($base_fare + $job->toll_fare) * 0.05);
                $show_amount = $base_fare + $com;
            }

            // -----------------------------------------------------------------
            // ASSEMBLE BID PAYLOAD
            // -----------------------------------------------------------------
            $newBid = [
                'amount'      => (int) $request->amount,
                'show_amount' => (int) $show_amount,
                'b_name'      => $check_ver->name,
                'kyc_id'      => $kyc_id->id ?? null,
                'b_mobile'    => $check_ver->mobile,
                'b_image'     => $check_ver->profile_img_url ?? ($kyc_id->selfie_url ?? null),
                'b_rating'    => $check_ver->ratings,
                'b_seater'    => $seat,
                'b_cab'       => $body_type,
                'b_luggage'   => $luggage,
                'b_language'  => $language,
                'remark'      => $request->remark,
                'updated_at'  => now() 
            ];

            // -----------------------------------------------------------------
            // FIREBASE & LOCAL DB UPDATES
            // -----------------------------------------------------------------
            (new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            ))->placeBid($job->job_no, $userId, $newBid);
            
            if ($job->job_no) {

                $bids = $job->bids_details
                    ? (is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true))
                    : [];
            
                // O(1) update / insert
                $newBid['status'] = 'inreview';
                $bids[$userId] = $newBid;
            
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->update([
                        'bids_details' => json_encode($bids, JSON_UNESCAPED_UNICODE)
                    ]);
            }

            // -----------------------------------------------------------------
            // FCM NOTIFICATIONS
            // -----------------------------------------------------------------
            $fcmTokens = !empty($job->fcm_token) ? [$job->fcm_token] : [];
            
            if (!empty($fcmTokens)) {
                $accessToken = $this->getAccessToken();
            
                if ($accessToken) {
                    $title = "🚗 A driver is interested!";
                    $body  = "{$check_ver->name} wants to take your trip from {$job->from_place} to {$job->to_place}. Check the offer now.";
            
                    foreach ($fcmTokens as $token) {
                        if (empty($token)) continue; 
            
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            $title,
                            $body,
                            [
                                'job_id'     => (string) $job->id,
                                'type'       => 'incoming_bid',
                                'action'     => 'open_job',
                                'bid_user'   => (string) $check_ver->id,
                                'from_place' => (string) $job->from_place,
                                'to_place'   => (string) $job->to_place,
                            ]
                        );
                    }
                }
            }
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Bid placed successfully by admin.'
            ], 200);
    
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function create_bid_copy_2(Request $request)
    {
        try {
            $request->validate([
                'job_id' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'string', 'max:8'],
                'remark' => ['nullable', 'string', 'max:255'],
            ]);
        
            $userId = auth()->user()->id;
            $data = [];
            
            $new_bid = [
                'amount' => (int)$request->amount,
                'remark' => $request->remark,
                'status' => 'inreview',
            ];
            
            
            if(auth()->user()->doc_verify == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            $check_u = DB::table('user_register')
                ->join('kyc_details', 'kyc_details.user_id', '=', 'user_register.id')
                ->where([
                    'user_register.id' => auth()->id(),
                    'user_register.deletes' => '0',
                    'kyc_details.deletes' => '0',
                    'kyc_details.type' => 'Owner'
                ])
                ->first();
            
            if ($check_u) {
                if (!empty(auth()->user()->drivers_ids)) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'message' => 'Please add drivers before proceeding.'
                    ]);
                }
            }else{
                if (auth()->user()->isBidding == 0) {
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        'message' => "You don't have permission to place a bid."
                    ]);
                }

            }

            
            if(auth()->user()->vehicle_verify == 0){
                
                $get_type = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
                
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
                if($get_crm && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'url' => 'https://'.strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                    
                }elseif($get_crm == null && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => null,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }
                
            }
            
            // Instead of this check with Firebase DB.
            $job = DB::table('cus_job_temp')->where('id', $request->job_id)->where('deletes', '=', '0')->where('user_id', '!=', $userId)->first();
            
            if (!$job) {
                return response()->json(['status' => false, 'data' => null, 'message' => 'Job not found or Can\'t Bid own Job']);
            }elseif($job->global_type == 'mock'){
                return response()->json(['status' => false, 'data' => null, 'message' => 'You cannot place a bid on a mock job.']);
            }
            
            $get_type_2 = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
            
            if($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id){
                
                $checkOwner = DB::table('user_register')
                    ->where('id', auth()->user()->id)
                    ->where(function ($q) use ($request) {
                        $q->whereRaw("JSON_CONTAINS(drivers_ids, CAST(? AS JSON))", [$request->driver_id])
                          ->orWhereRaw("JSON_CONTAINS(drivers_ids, JSON_QUOTE(CAST(? AS CHAR)))", [$request->driver_id]);
                    })
                    ->first();
    
                    
                // $check_job_2 = DB::table('open_jobs')
                //     ->where('id', $request->job_id)
                //     ->whereNull('assigned_to')->first();
        
                if (!$checkOwner) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Driver not included under your account.',
                        'data'    => [],
                    ], 200);
                }
                
                
                // if($check_job_2 == null){
                //     return response()->json([
                //         'status'  => false,
                //         'message' => 'Driver already assigned',
                //         'data'    => [],
                //     ], 200);
                // }
            }elseif($get_type_2 && $get_type_2->type == 'Owner'){
                if(!isset($request->driver_id)){
                     return response()->json([
                        'status'  => false,
                        'message' => 'Select driver to bid job.',
                        'data'    => [],
                    ], 200);
                }
            }
            
            // Check with firebase column
            if (is_null($job->bids_details)) {
                
                $bids = [
                    $userId => $new_bid
                ];
                
                $new_bid_redish = [
                    'job_id' => $request->job_id,
                    'bidder_name' => auth()->user()->name,
                    'bidder_phone' => auth()->user()->mobile,
                    'bidder_email' => auth()->user()->email,
                    'bidder_id' => $userId
                ];
                
                $final_bid = array_merge($new_bid_redish, $new_bid);
                
                $payload = [
                    'type' => 'bid_placed',
                    'data' => $final_bid,
                    'ts' => now()->toDateTimeString(),
                ];
                
                // update the job record with the firebase
                
                // Redis::rpush("job:{$request->job_id}:history", json_encode($payload));
            
                // Redis::publish("job:{$request->job_id}:events", json_encode($payload));
                
                // Redis::xadd(
                //     "job:{$request->job_id}:stream",
                //     '*',
                //     ['data' => json_encode($payload)]
                // );
                
                // PusherService::trigger(
                //     "job.{$request->job_id}",
                //     "bid.placed",
                //     $payload
                // );
                
                
            } else {
                
                // $bids = json_decode($job->bids_details, true);
                
                // if($bids[$userId]['status'] == 'accept' || $bids[$userId]['status'] == 'reject'){
                //     return response()->json([
                //             'status' => false,
                //             'data' => null,
                //             'message' => 'Your Bid already or Accept or Reject.'
                //         ], 200);
                // }
                
                $bids = json_decode($job->bids_details, true);
                
                

                if (isset($bids[$userId]['status']) && in_array($bids[$userId]['status'], ['accept', 'reject'])) {
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        'message' => 'Your bid has already been accepted or rejected.'
                    ], 200);
                }
                
                if (array_key_exists($userId, $bids)) {
                    
                    $new_bid_redish = [
                        'job_id' => $request->job_id,
                        'bidder_name' => auth()->user()->name,
                        'bidder_phone' => auth()->user()->mobile,
                        'bidder_email' => auth()->user()->email,
                        'bidder_id' => $userId
                    ];
                    
                    $final_bid = array_merge($new_bid_redish, $new_bid);
                    
                    $payload = [
                        'type' => 'bid_place_manage',
                        'data' => $final_bid,
                        'ts' => now()->toDateTimeString()
                    ];
                    
                    // Redis::rpush("job:{$request->job_id}:history-manage", json_encode($payload));
                
                    // Redis::publish("job:{$request->job_id}:events-manage", json_encode($payload));
                    
                    // Redis::xadd(
                    //     "job:{$request->job_id}:stream-manage",
                    //     '*',
                    //     ['data' => json_encode($payload)]
                    // );
                    
                    PusherService::trigger(
                        "job.{$request->job_id}",
                        "bid.placed",
                        $payload
                    );
                    
                }else{
                    $new_bid_redish = [
                        'job_id' => $request->job_id,
                        'bidder_name' => auth()->user()->name,
                        'bidder_phone' => auth()->user()->mobile,
                        'bidder_email' => auth()->user()->email,
                        'bidder_id' => $userId
                    ];
                    
                    $final_bid = array_merge($new_bid_redish, $new_bid);
                    
                    $payload = [
                        'type' => 'bid_placed',
                        'data' => $final_bid,
                        'ts' => now()->toDateTimeString(),
                    ];
                    
                    // Redis::rpush("job:{$request->job_id}:history", json_encode($payload));
                
                    // Redis::publish("job:{$request->job_id}:events", json_encode($payload));
                    
                    // Redis::xadd(
                    //     "job:{$request->job_id}:stream",
                    //     '*',
                    //     ['data' => json_encode($payload)]
                    // );
                    
                    PusherService::trigger(
                        "job.{$request->job_id}",
                        "bid.placed",
                        $payload
                    );
                }
                
                $bids[$userId] = $new_bid;
                
                // $new_bid_redish = [
                //     'job_id' => $request->job_id,
                //     'bidder_name' => auth()->user()->name,
                //     'bidder_phone' => auth()->user()->mobile,
                //     'bidder_email' => auth()->user()->email,
                //     'bidder_id' => $userId
                // ];
                
                // $final_bid = array_merge($new_bid_redish, $new_bid);
                
                // $payload = [
                //     'type' => 'bid_placed',
                //     'data' => $final_bid,
                //     'ts' => now()->toDateTimeString(),
                // ];
                
                // Redis::rpush("job:{$request->job_id}:history-manage", json_encode($payload));
                
                // Redis::publish("job:{$request->job_id}:events-manage", json_encode($payload));
            }
            
            
            // Redis::rpush("job:{$jobId}:events", json_encode([
            //     'type' => 'bid_placed',
            //     'data' => [
            //         'amount' => $request->amount,
            //         'remark' => $request->remark,
            //         'status' => 'inreview',
            //     ],
            //     'ts' => now()->toDateTimeString(),
            // ]));
            
            $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('deletes', '=', '0')
                ->update([
                    'bids_details' => json_encode($bids),
                    'job_status' => 'bidding'
                ]);
                
            if($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id){
                
                $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                // ->where('job_status', 'accept')
                // ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"{$agentId}\".status')) = 'accept'")
                ->update([
                    'assigned_to' => $request->driver_id,
                    'assigned_by' => auth()->user()->id
                ]);
            }
            
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Bid placed successfully.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function create_bid_copy(Request $request)
    {
        try {
            $request->validate([
                'job_id' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'string', 'max:8'],
                'remark' => ['nullable', 'string', 'max:255'],
            ]);
        
            $userId = auth()->user()->id;
            $data = [];
            
            $new_bid = [
                'amount' => (int)$request->amount,
                'remark' => $request->remark,
                'status' => 'inreview',
            ];
            
            
            if(auth()->user()->doc_verify == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            $check_u = DB::table('user_register')
                ->join('kyc_details', 'kyc_details.user_id', '=', 'user_register.id')
                ->where([
                    'user_register.id' => auth()->id(),
                    'user_register.deletes' => '0',
                    'kyc_details.deletes' => '0',
                    'kyc_details.type' => 'Owner'
                ])
                ->first();
            
            if ($check_u) {
                if (!empty(auth()->user()->drivers_ids)) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'message' => 'Please add drivers before proceeding.'
                    ]);
                }
            }else{
                if (auth()->user()->isBidding == 0) {
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        'message' => "You don't have permission to place a bid."
                    ]);
                }

            }

            
            if(auth()->user()->vehicle_verify == 0){
                
                $get_type = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
                
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
                    
                if($get_crm && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'url' => 'https://'.strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                    
                }elseif($get_crm == null && auth()->user()->vehicle_verify == 0 && $get_type){
                    
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => $get_type->type,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }else{
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        "user_type" => null,
                        'message' => 'Vehicle add Pending.'
                    ], 200);
                    
                }
                
            }
            
            $job = DB::table('open_jobs')->where('id', $request->job_id)->where('deletes', '=', '0')->where('user_id', '!=', $userId)->first();
            
            if (!$job) {
                return response()->json(['status' => false, 'data' => null, 'message' => 'Job not found or Can\'t Bid own Job']);
            }elseif($job->global_type == 'mock'){
                return response()->json(['status' => false, 'data' => null, 'message' => 'You cannot place a bid on a mock job.']);
            }
            
            $get_type_2 = DB::table('kyc_details')->where(['user_id' => auth()->user()->id, 'deletes' => 0])->first();
            
            if($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id){
                
                $checkOwner = DB::table('user_register')
                    ->where('id', auth()->user()->id)
                    ->where(function ($q) use ($request) {
                        $q->whereRaw("JSON_CONTAINS(drivers_ids, CAST(? AS JSON))", [$request->driver_id])
                          ->orWhereRaw("JSON_CONTAINS(drivers_ids, JSON_QUOTE(CAST(? AS CHAR)))", [$request->driver_id]);
                    })
                    ->first();
    
                    
                // $check_job_2 = DB::table('open_jobs')
                //     ->where('id', $request->job_id)
                //     ->whereNull('assigned_to')->first();
        
                if (!$checkOwner) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Driver not included under your account.',
                        'data'    => [],
                    ], 200);
                }
                
                
                // if($check_job_2 == null){
                //     return response()->json([
                //         'status'  => false,
                //         'message' => 'Driver already assigned',
                //         'data'    => [],
                //     ], 200);
                // }
            }elseif($get_type_2 && $get_type_2->type == 'Owner'){
                if(!isset($request->driver_id)){
                     return response()->json([
                        'status'  => false,
                        'message' => 'Select driver to bid job.',
                        'data'    => [],
                    ], 200);
                }
            }
            
            if (is_null($job->bids_details)) {
                
                $bids = [
                    $userId => $new_bid
                ];
                
                $new_bid_redish = [
                    'job_id' => $request->job_id,
                    'bidder_name' => auth()->user()->name,
                    'bidder_phone' => auth()->user()->mobile,
                    'bidder_email' => auth()->user()->email,
                    'bidder_id' => $userId
                ];
                
                $final_bid = array_merge($new_bid_redish, $new_bid);
                
                $payload = [
                    'type' => 'bid_placed',
                    'data' => $final_bid,
                    'ts' => now()->toDateTimeString(),
                ];
                
                // Redis::rpush("job:{$request->job_id}:history", json_encode($payload));
            
                // Redis::publish("job:{$request->job_id}:events", json_encode($payload));
                
                // Redis::xadd(
                //     "job:{$request->job_id}:stream",
                //     '*',
                //     ['data' => json_encode($payload)]
                // );
                
                PusherService::trigger(
                    "job.{$request->job_id}",
                    "bid.placed",
                    $payload
                );
                
                
            } else {
                
                // $bids = json_decode($job->bids_details, true);
                
                // if($bids[$userId]['status'] == 'accept' || $bids[$userId]['status'] == 'reject'){
                //     return response()->json([
                //             'status' => false,
                //             'data' => null,
                //             'message' => 'Your Bid already or Accept or Reject.'
                //         ], 200);
                // }
                
                $bids = json_decode($job->bids_details, true);
                
                

                if (isset($bids[$userId]['status']) && in_array($bids[$userId]['status'], ['accept', 'reject'])) {
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        'message' => 'Your bid has already been accepted or rejected.'
                    ], 200);
                }
                
                if (array_key_exists($userId, $bids)) {
                    
                    $new_bid_redish = [
                        'job_id' => $request->job_id,
                        'bidder_name' => auth()->user()->name,
                        'bidder_phone' => auth()->user()->mobile,
                        'bidder_email' => auth()->user()->email,
                        'bidder_id' => $userId
                    ];
                    
                    $final_bid = array_merge($new_bid_redish, $new_bid);
                    
                    $payload = [
                        'type' => 'bid_place_manage',
                        'data' => $final_bid,
                        'ts' => now()->toDateTimeString()
                    ];
                    
                    // Redis::rpush("job:{$request->job_id}:history-manage", json_encode($payload));
                
                    // Redis::publish("job:{$request->job_id}:events-manage", json_encode($payload));
                    
                    // Redis::xadd(
                    //     "job:{$request->job_id}:stream-manage",
                    //     '*',
                    //     ['data' => json_encode($payload)]
                    // );
                    
                    PusherService::trigger(
                        "job.{$request->job_id}",
                        "bid.placed",
                        $payload
                    );
                    
                }else{
                    $new_bid_redish = [
                        'job_id' => $request->job_id,
                        'bidder_name' => auth()->user()->name,
                        'bidder_phone' => auth()->user()->mobile,
                        'bidder_email' => auth()->user()->email,
                        'bidder_id' => $userId
                    ];
                    
                    $final_bid = array_merge($new_bid_redish, $new_bid);
                    
                    $payload = [
                        'type' => 'bid_placed',
                        'data' => $final_bid,
                        'ts' => now()->toDateTimeString(),
                    ];
                    
                    // Redis::rpush("job:{$request->job_id}:history", json_encode($payload));
                
                    // Redis::publish("job:{$request->job_id}:events", json_encode($payload));
                    
                    // Redis::xadd(
                    //     "job:{$request->job_id}:stream",
                    //     '*',
                    //     ['data' => json_encode($payload)]
                    // );
                    
                    PusherService::trigger(
                        "job.{$request->job_id}",
                        "bid.placed",
                        $payload
                    );
                }
                
                $bids[$userId] = $new_bid;
                
                // $new_bid_redish = [
                //     'job_id' => $request->job_id,
                //     'bidder_name' => auth()->user()->name,
                //     'bidder_phone' => auth()->user()->mobile,
                //     'bidder_email' => auth()->user()->email,
                //     'bidder_id' => $userId
                // ];
                
                // $final_bid = array_merge($new_bid_redish, $new_bid);
                
                // $payload = [
                //     'type' => 'bid_placed',
                //     'data' => $final_bid,
                //     'ts' => now()->toDateTimeString(),
                // ];
                
                // Redis::rpush("job:{$request->job_id}:history-manage", json_encode($payload));
                
                // Redis::publish("job:{$request->job_id}:events-manage", json_encode($payload));
            }
            
            
            // Redis::rpush("job:{$jobId}:events", json_encode([
            //     'type' => 'bid_placed',
            //     'data' => [
            //         'amount' => $request->amount,
            //         'remark' => $request->remark,
            //         'status' => 'inreview',
            //     ],
            //     'ts' => now()->toDateTimeString(),
            // ]));
            
            $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('deletes', '=', '0')
                ->update([
                    'bids_details' => json_encode($bids),
                    'job_status' => 'bidding'
                ]);
                
            if($get_type_2 && $get_type_2->type == 'Owner' && $request->driver_id){
                
                $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                // ->where('job_status', 'accept')
                // ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"{$agentId}\".status')) = 'accept'")
                ->update([
                    'assigned_to' => $request->driver_id,
                    'assigned_by' => auth()->user()->id
                ]);
            }
            
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Bid placed successfully.'
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function agree_jobNewVehicle(Request $request)
    {
        try {
    
            $request->validate([
                'job_id'    => ['required', 'string', 'max:255'],
                'mock_type' => ['nullable', 'string', 'in:wallet,cash'],
                'vehicle_no' => ['nullable', 'string']
            ]);
    
            $userId = auth()->user()->id;
    
            if (auth()->user()->doc_verify == 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            $user = auth()->user();
            
            $kyc_id = DB::table('kyc_details')->where(['user_id' => $user->id])->first();
            
            // dd($kyc_id);
    
            if ($kyc_id->type == 'Owner') {
    
                // $get_crm = DB::table('subscriptions as sub')
                //     ->select('crm.fullDomain')
                //     ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                //     ->where('sub.user_id', $userId)
                //     ->where('sub.planType', 'TRAIL')
                //     ->where('sub.paymentStatus', 'SUCCESS')
                //     ->where('crm.crmStatus', 'generated')
                //     ->where('crm.deletes', '0')
                //     ->orderBy('sub.id', 'DESC')
                //     ->first();
    
                // if ($get_crm && $get_type) {
                //     return response()->json([
                //         'status' => false,
                //         'data' => null,
                //         'user_type' => $get_type->type,
                //         'url' => 'https://' . strtolower($get_crm->fullDomain),
                //         'message' => 'CRM vehicle setup Pending.'
                //     ], 200);
                // }
                
                if($request->vehicle_no){
                    $check_v = DB::table('owner_vehicle_list')->where(['user_id' => $user->id, 'rc_number' => $request->vehicle_no])->first();
                    
                    if($check_v){
                        
                        if($check_v->verification_status != 2){
                            return response()->json([
                                'status' => false,
                                'data' => null,
                                'user_type' => $kyc_id?->type,
                                'message' => 'Your vehicle is under review. An admin will verify it shortly.'
                            ], 200);
                        }
                        
                    }else{
                        return response()->json([
                            'status' => false,
                            'data' => 333,
                            'user_type' => $get_type?->type,
                            'message' => 'Vehicle add Pending.'
                        ], 200);
                    }
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'user_type' => $get_type?->type,
                        'message' => 'Please select your vehicle.'
                    ], 200);
                }
    
                
                
            }else if(auth()->user()->vehicle_verify < 1){
                
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $kyc_id?->type,
                    'message' => 'Vehicle add Pending.'
                ], 200);
                
            }else if(auth()->user()->vehicle_verify < 2){
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $kyc_id?->type,
                    'message' => 'Your vehicle is under review. An admin will verify it shortly.'
                ], 200);
            }
            

            $seat = null;
            $body_type = null;
            $language = null;
            $luggage = null;
            
            if ($user->vehicle_details && $kyc_id->type == 'Driver') {
            
                $vehicleDetails = json_decode($user->vehicle_details, true);
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $seat = $rcVehicle['seat_capacity'] ?? null;
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else if ($kyc_id->type == 'Owner'){
                
                $vehicleDetails = json_decode($check_v->vehicle_details, true);
                $seat = $check_v->seater;
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else{
                
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $user->id)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    $seat = $get_ocr->seater ?? null;
                }
                
            }
            

    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->whereIn('job_status', ['created', 'bidding'])
                ->where('user_id', '!=', $userId)
                ->where('deletes', '0')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Job not found or Can\'t Bid own Job'
                ]);
            }
    
            if (
                $request->mock_type === 'wallet' &&
                $job->mock_amt > 0 &&
                $job->mock_amt > auth()->user()->walletBalance
            ) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Your wallet balance is too low. You cannot agree to this job.'
                ]);
            }
    
            /* -----------------------------
               BID DATA (UNCHANGED LOGIC)
            ------------------------------*/
            
            if ($job->fare) {
                // $job->fare = (string) ((int) $job->fare * 0.95);
                if($job->global_type == 'customer'){
                    $job->fare = (string) ($job->base_fare + $job->toll_fare);
                    $job->show_fare = (string) ($job->base_fare + $job->com);
                }else{
                    $job->fare = (string) $job->fare;
                    $job->show_fare = (string) $job->fare;
                }
            }
            
            $newBid = [
                'amount'    => (int) $job->fare,
                'show_amount'    => (int) $job->show_fare,
                'remark'    => '',
                'mock_type' => $request->mock_type,
                'b_name' => auth()->user()->name,
                'kyc_id' => $kyc_id ? $kyc_id->id : null,
                'b_mobile' => auth()->user()->mobile,
                'b_image' => auth()->user()->profile_img_url ?? $kyc_id->selfie_url,
                'b_rating' => auth()->user()->ratings,
                'b_seater' => $seat,
                'b_cab' => $body_type,
                'b_luggage' => $luggage,
                'b_language' => $language,
                'b_cab_no' => $kyc_id->type == 'Owner' ? $request->vehicle_no : '',
                'status'    => 'direct'
            ];
    
            /* -----------------------------
               FIREBASE UPDATE (NEW)
            ------------------------------*/
           (new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            ))->placeBid($job->job_no, $userId, $newBid);
            
            if ($job->job_no) {
            
                $bids = $job->bids_details
                    ? (is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true))
                    : [];
            
                $bids[$userId] = $newBid;
            
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->update([
                        'bids_details' => json_encode($bids, JSON_UNESCAPED_UNICODE)
                    ]);
            }
            
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Job has been agreed successfully. Please wait for the poster\'s response.'
            ], 200);
    
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
                'errors' => $e->getMessage(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function agree_job(Request $request)
    {
        try {
    
            $request->validate([
                'job_id'    => ['required', 'string', 'max:255'],
                'mock_type' => ['nullable', 'string', 'in:wallet,cash']
            ]);
    
            $userId = auth()->user()->id;
    
            if (auth()->user()->doc_verify == 0) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'KYC Pending.'
                ], 200);
            }
            
            if (auth()->user()->walletBalance < 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance. Please top up to place a bid.',
                    'data'    => null,
                ]);
            }
            
            $activeJob = DB::table('cus_job_temp')
                ->where('assigned_to', $userId)
                ->whereIn('job_status', ['accept', 'started', 'created', 'bidding'])
                ->whereNot('id', $request->job_id)
                ->latest('id')
                ->first();
            
            if ($activeJob) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Please complete your current ride before accepting a new one.',
                    'data'    => [
                        'job_id'  => $activeJob->id,
                        'job_no'  => $activeJob->job_no ?? null
                    ]
                ], 200);
            }
            
    
            if (auth()->user()->vehicle_verify == 0) {
    
                $get_type = DB::table('kyc_details')
                    ->where(['user_id' => $userId, 'deletes' => 0])
                    ->first();
    
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', $userId)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
    
                if ($get_crm && $get_type) {
                    return response()->json([
                        'status' => false,
                        'data' => null,
                        'user_type' => $get_type->type,
                        'url' => 'https://' . strtolower($get_crm->fullDomain),
                        'message' => 'CRM vehicle setup Pending.'
                    ], 200);
                }
    
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'user_type' => $get_type?->type,
                    'message' => 'Vehicle add Pending.'
                ], 200);
            }
            
            $user = auth()->user();

            $seat = null;
            $body_type = null;
            $language = null;
            $luggage = null;
            
            if ($user->vehicle_details) {
            
                $vehicleDetails = json_decode($user->vehicle_details, true);
            
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $seat = $rcVehicle['seat_capacity'] ?? null;
                    $body_type = $rcVehicle['body_type'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user->id)
                        ->where('doc_type', 'RC')
                        ->orderByDesc('id')
                        ->first();
            
                    if ($get_ocr) {
                        $seat = $get_ocr->seater ?? null;
                    }
                }
            
                if (!empty($vehicleDetails['user_info'])) {
            
                    $language = $vehicleDetails['user_info']['language'] ?? null;
                    $luggage  = $vehicleDetails['user_info']['luggage'] ?? null;
                }
            }else{
                
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $user->id)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    $seat = $get_ocr->seater ?? null;
                }
                
            }
            
            $kyc_id = DB::table('kyc_details')->where(['user_id' => $user->id])->first();
            
            
    
            // 1. Get the base job first
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->whereIn('job_status', ['created', 'bidding'])
                ->where('user_id', '!=', $userId)
                ->where('deletes', '0')
                ->first();
            
            // 2. If job exists and has a user, fetch only the fcm_token
            if ($job && $job->user_id != 0) {
                // Determine the correct table
                $table = ($job->global_type == 'customer') ? 'customer_register' : 'user_register';
                
                // Fetch only the fcm_token column and assign it directly to the job object
                $job->fcm_token = DB::table($table)
                    ->where('id', $job->user_id)
                    ->value('fcm_token'); 
            }
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Job not found or Can\'t Bid own Job'
                ]);
            }
    
            if (
                $request->mock_type === 'wallet' &&
                $job->mock_amt > 0 &&
                $job->mock_amt > auth()->user()->walletBalance
            ) {
                return response()->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'Your wallet balance is too low. You cannot agree to this job.'
                ]);
            }
    
            /* -----------------------------
               BID DATA (UNCHANGED LOGIC)
            ------------------------------*/
            
            if ($job->fare) {
                // $job->fare = (string) ((int) $job->fare * 0.95);
                if($job->global_type == 'customer'){
                    $job->fare = (string) ($job->base_fare + $job->toll_fare);
                    $job->show_fare = (string) ($job->base_fare + $job->com);
                }else{
                    $job->fare = (string) $job->fare;
                    $job->show_fare = (string) $job->fare;
                }
            }
            
            $newBid = [
                'amount'    => (int) $job->fare,
                'show_amount'    => (int) $job->show_fare,
                'remark'    => '',
                'mock_type' => $request->mock_type,
                'b_name' => auth()->user()->name,
                'kyc_id' => $kyc_id ? $kyc_id->id : null,
                'b_mobile' => auth()->user()->mobile,
                'b_image' => auth()->user()->profile_img_url ?? $kyc_id->selfie_url,
                'b_rating' => auth()->user()->ratings,
                'b_seater' => $seat,
                'b_cab' => $body_type,
                'b_luggage' => $luggage,
                'b_language' => $language,
                'status'    => 'direct',
                'updated_at' => now()
            ];
    
            /* -----------------------------
               FIREBASE UPDATE (NEW)
            ------------------------------*/
           (new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            ))->placeBid($job->job_no, $userId, $newBid);
            
            if ($job->job_no) {
                
                $bids = $job->bids_details
                    ? (is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true))
                    : [];
            
                $bids[$userId] = $newBid;
            
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->update([
                        'bids_details' => json_encode($bids, JSON_UNESCAPED_UNICODE)
                    ]);
            }
            
            $user = auth()->user();

            $fcmTokens = !empty($job->fcm_token) ? [$job->fcm_token] : [];
            
            if (!empty($fcmTokens)) {
            
                $accessToken = $this->getAccessToken();
            
                if ($accessToken) {
            
                    $title = "🚗 A driver is interested!";
                    $body  = "{$user->name} wants to take your trip from {$job->from_place} to {$job->to_place}. Check the offer now.";
            
                    foreach ($fcmTokens as $token) {
            
                        if (empty($token)) continue;
            
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            $title,
                            $body,
                            [
                                'job_id'     => (string) $job->id,
                                'type'       => 'incoming_bid',
                                'action'     => 'open_job',
                                'bid_user'   => (string) $user->id,
                                'from_place' => (string) $job->from_place,
                                'to_place'   => (string) $job->to_place,
                            ]
                        );
                    }
                }
            }
            
    
            return response()->json([
                'status' => true,
                'data' => null,
                'message' => 'Job has been agreed successfully. Please wait for the poster\'s response.'
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            \Log::info('Testing schedule...: ', [$e->getMessage()]);
    
            return response()->json([
                'status' => false,
                'data' => null,
                'errors' => $e->getMessage(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_jobinfo(Request $request)
    {
        try {
            $request->validate([
                'job_id' => ['required', 'string', 'max:255'],
                'method' => ['required', 'string', 'max:255'],
                'bidder_id' => ['nullable', 'string', 'max:255'],
                'sch_id' => ['nullable'],
                'date' => ['nullable', 'string', 'max:255'],
            ]);
            
            // \Log::info('View Booking failed Error: ' . json_encode($request->all()));
            
            $find_j = DB::table('cus_job_temp')->where('id', $request->job_id)->where('deletes' , '0')->first();
            
            switch ($request->method) {
                
                case 'schedule':
                    
                    $user = auth()->user();
                    
                    $job = DB::table('cus_job_temp')
                        ->where('id', $request->job_id)
                        ->where('deletes', '0')
                        ->first();
                    
                    if (!$job) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Job not found.'
                        ], 404);
                    }
                    
                    $statusData = json_decode($job->sch_status, true) ?? [];
                    
                    // if (isset($statusData[$request->date][auth()->id()])) {
                    //     $time = $statusData[$request->date][auth()->id()]['updated_at'];
                    // }
                    
                    // if (now()->diffInSeconds($time) > 30) {
                    //     return response()->json([
                    //         'status' => false,
                    //         'message' => 'This job is no longer available due to time limit expiration.'
                    //     ]);
                    // }
                    
                    $driverId = auth()->id();
                    $date = $request->date;
                    
                    // Check existence
                    if (!isset($statusData[$date][$driverId]['updated_at'])) {
                        return response()->json([
                            'status' => false,
                            'message' => 'No active job found.'
                        ]);
                    }
                    
                    // Parse timestamp
                    $updatedAt = \Carbon\Carbon::parse($statusData[$date][$driverId]['updated_at']);
                    
                    // Expiry check (30 seconds from updated_at)
                    if ($updatedAt->addSeconds(30)->isPast()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'The time limit has expired. This job is no longer available.'
                        ]);
                    }
                    
                    if ($job->job_status == 'accept') {
                        return response()->json([
                            'status' => false,
                            'message' => 'Job already booked.'
                        ]);
                    }
                    
                    // $sch_id = json_decode($request->sch_id, true);
                    $sch_id = $request->sch_id;
                    
                    $get_fare = DB::table('schedule_dates')
                        ->whereIn('id', $sch_id)
                        ->where('user_id', $user->id)
                        ->select('dates_price')
                        ->first();
                    
                    // \Log::error('Testing.....: '. json_encode($get_fare));
                    if ($get_fare) {
                    
                        $base_fare = 0;
                    
                        $dates = json_decode($get_fare->dates_price, true);
                    
                        if (is_array($dates)) {
                            foreach ($dates as $datetime => $price) {
                    
                                if (strncmp($datetime, $request->date, 10) === 0) {
                                    $base_fare = (int) $price;
                                    break;
                                }
                            }
                        }
                        
                    
                        if ($base_fare > 0) {
                    
                            $toll_fare = $job->toll_fare;
                    
                            $fare = $base_fare + $toll_fare;
                            
                            $explode = explode(' ', $job->pickup_date); 
                            
                            $job->pickup_date = $request->date . ' ' . $explode[1];
                    
                            $data = [
                                'id'                   => $job->id,
                                'user_id'              => $job->user_id,
                                'global_type'          => $job->global_type,
                                'job_type'             => $job->job_type,
                                'job_no'               => $job->job_no,
                                'from_place'           => $job->from_place,
                                'to_place'             => $job->to_place,
                                'pickup_date'          => $job->pickup_date,
                                'dropoff_date'         => $job->dropoff_date,
                                'pass_count'           => $job->pass_count == 'mini' ? '4 Mini' : $job->pass_count - 1,
                                'base_fare'            => $base_fare,
                                'toll_fare'            => $job->toll_fare,
                                'day'                  => $job->day,
                                'fare'                 => $fare,
                                'mock_amt'             => $job->mock_amt,
                                'add_fare_details'     => $job->add_fare_details,
                                'reject_count'         => $job->reject_count,
                                'distance'             => $job->distance,
                                'job_remark'           => $job->job_remark,
                                'job_status'           => $job->job_status,
                                'payment_status'       => $job->payment_status,
                                'created_at'           => $job->created_at,
                                'poster_name'          => $user->name,
                                'poster_email'         => $user->email,
                                'poster_mobile'        => $user->mobile,
                                'poster_company'       => '',
                                'poster_ratings'       => '',
                                'poster_complete_jobs' => '',
                                'bidders'              => [],
                            ];
                    
                            return response()->json([
                                'status'  => true,
                                'data'    => $data,
                                'message' => 'Job details got successfully.'
                            ], 200);
                        }
                    }
                    
                case 'manage':
                    $bidder_id = auth()->user()->id;
                    
                    $get_job = DB::table('cus_job_temp')
                        ->where('id', $request->job_id)
                        ->whereIn('job_status', ['accept', 'cancelled'])
                        ->first();
                    
                    // \Log::info('View Booking failed Error: ' . json_encode($get_job));
                    if ($get_job) {
                    // \Log::info('HIIIIIIIIIIIIIIIIIII: ' . json_encode($get_job));
                        return response()->json([
                            'status' => false,
                            'message' => 'This job has already been accepted or cancelled.'
                        ], 200);
                    }
                    
                    break;
            
                case 'bidder':
                    $bidder_id = is_numeric($request->bidder_id) ? $request->bidder_id : null;
                    break;
                    
                case 'bidder_accept':
                    $bidder_id = is_numeric($request->bidder_id) ? $request->bidder_id : null;
                    
                    $get_job = DB::table('cus_job_temp')->where('id', $request->job_id)->whereRaw("JSON_CONTAINS_PATH(bids_details, 'one', '$.\"$bidder_id\"')")->first();
                    
                    if(!$get_job){
                        return response()->json([
                            'status' => false,
                            'message' => 'Bid not found.'
                        ], 200);
                        
                    }
                    
                    break;
                    
                case 'reject':
                    
                    $get_job = DB::table('cus_job_temp')
                        ->where('id', $request->job_id)
                        ->where('deletes', '=', '0')
                        ->where('reject_count', '<', 1)
                        ->first();
                    
                    // return $get_job;
                    
                    if(!$get_job){
                        return response()->json([
                            'status' => false,
                            'message' => 'Your Reject Limit Reached. You can only cancel the Job.'
                        ], 200);
                        
                    }
                    
                    
                    $bidder_id = is_numeric($request->bidder_id) ? $request->bidder_id : null;
                    break;
            
                default:
                    $bidder_id = null;
                    break;
            }
            
            // $arr_sat = ['accept', 'expiried', 'cancelled', 'no_response'];
            
            $joinTable = ($find_j->global_type == 'customer')
                ? 'customer_register as ur'
                : 'user_register as ur';
            
            $get_jobs = DB::table('cus_job_temp as oj')
                ->leftJoin($joinTable . ' as ur', function ($join) {
                    $join->on('ur.id', '=', 'oj.user_id')
                         ->where('ur.deletes', '0'); // ✅ moved here
                })
                ->where('oj.id', $request->job_id)
                ->where('oj.deletes', '0')
                ->whereIn('oj.job_status', ['created', 'bidding'])
                // ->where('ur.deletes', '0')
                ->select(
                    'oj.*',
                    DB::raw('CAST(TRUNCATE(oj.fare, 0) AS CHAR) as fare'),
                    'ur.name as poster_name',
                    'ur.email as poster_email',
                    'ur.mobile as poster_mobile',
                    'ur.company_name as poster_company',
                    'ur.ratings as poster_ratings',
                    'ur.complete_jobs as poster_complete_jobs'
                )
                ->orderByDesc('oj.id')
                ->get();
            
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
            
            $get_jobs = $get_jobs->map(function ($job) use ($bidder_id, $request, $firebase) {
            
                $bidders = [];
            
                $firebaseDoc = $firebase->getJobBidders($job->job_no);
            
                $decoded = $this->parseFirestoreFields($firebaseDoc);
            
                if (!empty($decoded)) {
            
                    if ($request->method == 'manage') {
            
                        if (
                            isset($decoded[$bidder_id]['status']) &&
                            in_array($decoded[$bidder_id]['status'], ['accept', 'reject'])
                        ) {
                            return [
                                'status'  => false,
                                'data'    => null,
                                'message' => 'Your bid has already been accepted or rejected.'
                            ];
                        }
                    }
            
                    foreach ($decoded as $userId => $bid) {
            
                        $user = DB::table('user_register')
                            ->where('id', $userId)
                            ->where('deletes', '0')
                            ->first();
            
                        if (!$user) {
                            continue;
                        }
            
                        $bidData = [
                            'bidder_name'          => $user->name ?? null,
                            'bidder_email'         => $user->email ?? null,
                            'bidder_mobile'        => $user->mobile ?? null,
                            'bidder_company_name'  => $user->company_name ?? null,
                            'bidder_complete_jobs' => $user->complete_jobs ?? null,
                            'bidder_ratings'       => $user->ratings ?? null,
                            'amount'               => (int) ($bid['amount'] ?? 0),
                            'remark'               => $bid['remark'] ?? null,
                        ];
            
                        if ($userId == $bidder_id) {
                         
                            $bidders = $bidData;
                            break;
                        } else {
                            $bidders[] = $bidData;
                        }
                    }
                }
            
                $job->fare = (string) (
                    (int) $job->base_fare + (int) $job->toll_fare
                );
            
                return [
                    'id'                   => $job->id,
                    'user_id'              => $job->user_id,
                    'global_type'          => $job->global_type,
                    'job_type'             => $job->job_type,
                    'job_no'               => $job->job_no,
                    'from_place'           => $job->from_place,
                    'to_place'             => $job->to_place,
                    'pickup_date'          => $job->pickup_date,
                    'dropoff_date'         => $job->dropoff_date,
                    'pass_count'           => $job->pass_count == 'mini' ? '4 Mini' : (string)((int)$job->pass_count - 1),
                    'base_fare'            => $job->base_fare,
                    'toll_fare'            => $job->toll_fare,
                    'day'                  => $job->day,
                    'fare'                 => $job->fare,
                    'mock_amt'             => $job->mock_amt,
                    'add_fare_details'     => $job->add_fare_details,
                    'reject_count'         => $job->reject_count,
                    'distance'             => $job->distance,
                    'job_remark'           => $job->job_remark,
                    'job_status'           => $job->job_status,
                    'payment_status'       => $job->payment_status,
                    'created_at'           => $job->created_at,
                    'poster_name'          => $job->poster_name,
                    'poster_email'         => $job->poster_email,
                    'poster_mobile'        => $job->poster_mobile,
                    'poster_company'       => $job->poster_company,
                    'poster_ratings'       => $job->poster_ratings,
                    'poster_complete_jobs' => $job->poster_complete_jobs,
                    'bidders'              => $bidders,
                ];
            });

            
            $data = count($get_jobs) > 0 ? $get_jobs[0] : [];
            
            if(count($data) == 0){
                return response()->json([
                    'status'  => false,
                    'data'    => $data,
                    'message' => 'This Job cancelled or Bid has been removed.'
                ], 200);
            }
            
            // If "manage" mode returned an error
            if (isset($data['status']) && $data['status'] == false) {
                return response()->json($data, 200);
            }
            
            return response()->json([
                'status'  => true,
                'data'    => $data,
                'message' => 'Job details got successfully.'
            ], 200);

    
        } catch (ValidationException $e) {
            \Log::info('View Booking failed API Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            \Log::info('View Booking failed API Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function accept_job(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'job_id'    => ['required', 'string', 'max:255'],
    //             'bidder_id' => ['required', 'string', 'max:255'],
    //         ]);
    
    //         $get_job = DB::table('cus_job_temp')
    //             ->where('id', $request->job_id)
    //             ->first();
    
    //         if (!$get_job) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Job not found.'
    //             ]);
    //         }
    

    //         if (auth()->user()->doc_verify == 0) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'data'    => null,
    //                 'message' => 'KYC Pending.'
    //             ]);
    //         }
    
    //         if (auth()->user()->vehicle_verify == 0) {
    
    //             $get_type = DB::table('kyc_details')
    //                 ->where(['user_id' => auth()->user()->id, 'deletes' => 0])
    //                 ->first();
    
    //             $get_crm = DB::table('subscriptions as sub')
    //                 ->select('crm.fullDomain')
    //                 ->join('crm', 'crm.subscription_id', '=', 'sub.id')
    //                 ->where('sub.user_id', auth()->user()->id)
    //                 ->where('sub.planType', 'TRAIL')
    //                 ->where('sub.paymentStatus', 'SUCCESS')
    //                 ->where('crm.crmStatus', 'generated')
    //                 ->where('crm.deletes', '0')
    //                 ->orderBy('sub.id', 'DESC')
    //                 ->first();
    
    //             if ($get_crm && $get_type) {
    //                 return response()->json([
    //                     'status'    => false,
    //                     'data'      => null,
    //                     'user_type' => $get_type->type,
    //                     'url'       => 'https://' . strtolower($get_crm->fullDomain),
    //                     'message'   => 'CRM vehicle setup Pending.'
    //                 ]);
    //             }
    
    //             return response()->json([
    //                 'status'    => false,
    //                 'data'      => null,
    //                 'user_type' => $get_type->type ?? null,
    //                 'message'   => 'Vehicle add Pending.'
    //             ]);
    //         }
            
    //         if($get_job->bids_details == null){
                
    //             $firebase = new \App\Services\FirebaseJobService(
    //                 $this->serviceAccount['project_id'],
    //                 $this->getAccessToken()
    //             );
        
    //             $firebaseBids = $firebase->getJobBidders($get_job->job_no);
        
    //             if (empty($firebaseBids) || !isset($firebaseBids[$request->bidder_id])) {
    //                 return response()->json([
    //                     'status' => false,
    //                     'message' => 'Bid not found in Firebase.'
    //                 ]);
    //             }
        
    //             $firebase->updateBidStatus(
    //                 $get_job->job_no,
    //                 $request->bidder_id,
    //                 'accept'
    //             );
                
    //             DB::table('cus_job_temp')
    //                 ->where('id', $get_job->id)
    //                 ->where('deletes', '0')
    //                 ->update([
    //                     'bids_details' => $firebaseBids json_encode,
    //                     'job_status' => 'accept',
    //                 ]);
                    
    //             $firebase->deleteJob($get_job->job_no);
                
    //         }else{
    //             $get_job->bis_details;
                
    //             DB::table('cus_job_temp')
    //                 ->where('id', $get_job->id)
    //                 ->where('deletes', '0')
    //                 ->update([
    //                     'bids_details' => // get particular id is accept,
    //                     'job_status' => 'accept',
    //                 ]);
    //         }
    
    
    //         DB::table('user_register')
    //             ->where('id', $get_job->user_id)
    //             ->where('deletes', '0')
    //             ->increment('complete_jobs', 1);
    
    //         $get_bidder = DB::table('user_register')
    //             ->where('id', $request->bidder_id)
    //             ->where('deletes', '0')
    //             ->first();
    
    //         if (!$get_bidder) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Bidder not found.'
    //             ]);
    //         }
    

    //         $get_bidder_amt = DB::table('cus_job_temp')
    //             ->where('id', $get_job->id)
    //             ->where('deletes', '0')
    //             ->whereRaw("JSON_EXTRACT(bids_details, '$.\"$request->bidder_id\"') IS NOT NULL")
    //             ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"$request->bidder_id\".amount')) as amount"))
    //             ->first();
    
    //         $amount = $get_bidder_amt ? $get_bidder_amt->amount : $get_job->fare;
    
    //         // ✅ WhatsApp check
    //         $existsWhatsApp = Controller::checkWhatsApp([
    //             'mobile' => $get_bidder->mobile
    //         ]);
    
    //         if (!$existsWhatsApp) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'WhatsApp not available for this user.'
    //             ]);
    //         }
    
    //         // ✅ Message
    //         $messages = "
    // Hello {$get_bidder->name}, 👋
    
    // Great news! 🎉  
    // Your bid for job {$get_job->job_no} has been accepted by " . auth()->user()->name . ".
    
    // Pickup: {$get_job->from_place}  
    // Drop: {$get_job->to_place}  
    // Date: " . \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A') . "  
    // Fare: ₹{$amount}  
    
    // Contact: " . auth()->user()->mobile . "
    
    // Be ready for the trip 🚀
    // ";

    //         Controller::sendNotification([
    //             'mobile' => $get_bidder->mobile,
    //             'templateName' => 'national_draw_verification',
    //             'language' => 'en',
    //             'templateBodyParam' => [],
    //             'messages' => $messages,
    //             'resend' => false
    //         ]);

    //         $fcmTokens = $this->getFcm([$get_bidder->id]);
    
    //         if ($fcmTokens) {
    //             $accessToken = $this->getAccessToken();
    
    //             foreach ($fcmTokens as $token) {
    //                 try {
    //                     $this->sendFCM(
    //                         $accessToken,
    //                         $token,
    //                         'Bid Accepted',
    //                         'Your bid for job ' . $get_job->job_no . ' has been accepted.',
    //                         [
    //                             'type' => 'accept_notification',
    //                             'url'  => env('APP_URL') . 'jobs',
    //                         ]
    //                     );
    //                 } catch (\Throwable $e) {
    //                     Log::error('FCM Error', ['error' => $e->getMessage()]);
    //                 }
    //             }
    //         }
    
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Job accepted successfully.'
    //         ]);
    
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed.',
    //             'errors' => $e->errors()
    //         ], 422);
    
    //     } catch (\Exception $e) {
    
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage(),
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function accept_job(Request $request)
    {
        try {
            $request->validate([
                'job_id'    => ['required', 'string'],
                'bidder_id' => ['required', 'string'],
            ]);
    
            $user = auth()->user();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->first();
    
            if (!$job) {
                return response()->json(['status' => false, 'message' => 'Job not found']);
            }
    
            if (auth()->user()->doc_verify == 0) {
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'KYC Pending.'
                ]);
            }
    
            if (auth()->user()->vehicle_verify == 0) {
    
                $get_type = DB::table('kyc_details')
                    ->where(['user_id' => auth()->user()->id, 'deletes' => 0])
                    ->first();
    
                $get_crm = DB::table('subscriptions as sub')
                    ->select('crm.fullDomain')
                    ->join('crm', 'crm.subscription_id', '=', 'sub.id')
                    ->where('sub.user_id', auth()->user()->id)
                    ->where('sub.planType', 'TRAIL')
                    ->where('sub.paymentStatus', 'SUCCESS')
                    ->where('crm.crmStatus', 'generated')
                    ->where('crm.deletes', '0')
                    ->orderBy('sub.id', 'DESC')
                    ->first();
    
                if ($get_crm && $get_type) {
                    return response()->json([
                        'status'    => false,
                        'data'      => null,
                        'user_type' => $get_type->type,
                        'url'       => 'https://' . strtolower($get_crm->fullDomain),
                        'message'   => 'CRM vehicle setup Pending.'
                    ]);
                }
    
                return response()->json([
                    'status'    => false,
                    'data'      => null,
                    'user_type' => $get_type->type ?? null,
                    'message'   => 'Vehicle add Pending.'
                ]);
            }
    
            $bidderId = $request->bidder_id;
            $bids = [];
    
            if (empty($job->bids_details)) {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
    
                $firebaseBidsRaw = $firebase->getJobBidders($job->job_no);
    
                if (empty($firebaseBidsRaw) || !isset($firebaseBidsRaw[$bidderId])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bid not found'
                    ]);
                }
    
                $bids = [];
                foreach ($firebaseBidsRaw as $id => $val) {
                    $bids[$id] = $this->parseFirestoreFields($val);
                }
    
                $firebase->deleteJob($job->job_no);
    
            } else {
           
                $bids = json_decode($job->bids_details, true);
    
                if (!isset($bids[$bidderId])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bid not found in DB'
                    ]);
                }
            }
    
            foreach ($bids as $id => &$bid) {
                $bid['status'] = ($id == $bidderId) ? 'accept' : $bid['status'];
            }
    
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'bids_details' => json_encode($bids),
                    'job_status'   => 'accept',
                    'assigned_to' => $bidderId
                ]);
    
            DB::table('user_register')
                ->where('id', $job->user_id)
                ->increment('complete_jobs');
    
            $bidder = DB::table('user_register')
                ->where('id', $bidderId)
                ->where('deletes', '0')
                ->first();
    
            if (!$bidder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found'
                ]);
            }
    
            $amount = $bids[$bidderId]['amount'] ?? $job->fare;
    
            if (Controller::checkWhatsApp(['mobile' => $bidder->mobile])) {
    
                $message = "Hello {$bidder->name}, Your bid for job {$job->job_no} has been accepted.";
    
                Controller::sendNotification([
                    'mobile' => $bidder->mobile,
                    'templateName' => 'national_draw_verification',
                    'language' => 'en',
                    'templateBodyParam' => [],
                    'messages' => $message,
                    'resend' => false
                ]);
            }
    
            $tokens = $this->getFcm([$bidder->id]);
            
            if ($tokens) {
                $accessToken = $this->getAccessToken();
    
                foreach ($tokens as $token) {
                    $this->sendFCM(
                        $accessToken,
                        $token,
                        'Bid Accepted',
                        "Job {$job->job_no} accepted",
                        ['type' => 'accept_notification']
                    );
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Job accepted successfully'
            ]);
    
        } catch (\Exception $e) {
            \Log::info('View Booking failed API Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
//     public function reject_bid(Request $request)
//     {
//         try {
//             $request->validate([
//                 'job_id' => ['required', 'string', 'max:255'],
//                 'bidder_id' => ['required', 'string', 'max:255'],
//             ]);
            
//             $get_job = DB::table('cus_job_temp')
//                     ->where('id', $request->job_id)
//                     ->where('deletes', '=', '0')
//                     ->where('reject_count', '<', 1)->first();
            
//             if ($get_job) {
//                 // DB::table('cus_job_temp')
//                 //     ->where('id', $get_job->id)
//                 //     ->where('deletes', '=', '0')
//                 //     ->update([
//                 //         'bids_details' => DB::raw("JSON_SET(bids_details, '$.\"{$request->bidder_id}\".status', 'reject')"),
//                 //         'job_status'   => 'bidding',
//                 //         'reject_count'   => '1',
//                 //     ]);
                
//                 $firebase = new \App\Services\FirebaseJobService(
//                     $this->serviceAccount['project_id'],
//                     $this->getAccessToken()
//                 );
                
//                 $firebaseDoc = $firebase->getJob($get_job->job_no);
                
//                 $job = $this->parseFirestoreFields($firebaseDoc);
                
//                 $amount = $job['bids_details'][$request->bidder_id]['amount'];
                
//                 $firebase->updateBidStatus(
//                     $get_job->job_no,
//                     $request->bidder_id,
//                     'reject'
//                 );
                    
//                 DB::table('user_register')
//                     ->where('id', $get_job->user_id)->where('deletes', '0')
//                     ->decrement('complete_jobs', 1);
                
//                 $get_bidder = DB::table('user_register')->where('deletes', '0')
//                     ->where('id', $request->bidder_id)->first();

//                 $existsWhatsApp = Controller::checkWhatsApp([
//                     'mobile' => $get_bidder->mobile
//                 ]);
                
//                 if ($existsWhatsApp) {
                    
                    
// $messages = "
// Hello {$get_bidder->name}, 👋

// We appreciate your interest in bidding for the job *{$get_job->job_no}*.  

// Unfortunately, your bid was not selected by ".auth()->user()->name ." this time.  

// 📌 Job Details:  
// 🔹 Pickup: {$get_job->from_place} 
// 🔹 Drop-off: {$get_job->to_place}  
// 🔹 Date: " . \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A') . "    
// 🔹 Fare:₹ {$amount}
// 🔹 Contact Person: ". auth()->user()->mobile ."

// ❗ Don’t worry — many new jobs are being posted every day.  
// You can explore other opportunities and place your bids again.  

// We value your efforts and wish you success in your upcoming bids 🚀  

// Thanks for using " . env('APP_NAME') . ".  ";

//                     // $randotp = '123456';
    
//                     $whatsAppArr = [
//                         'mobile' => $get_bidder->mobile,
//                         'templateName' => 'national_draw_verification',
//                         'language' => 'en',
//                         'templateBodyParam' => [],
//                         'messages' => $messages,
//                         'resend' => false
//                     ];
    
//                     $sentsms = Controller::sendNotification($whatsAppArr);
                    
//                     $fcmToken = $this->getFcm([$get_bidder->id]);
                    
//                     if ($fcmToken) {
//                         $accessToken = $this->getAccessToken();
//                         if ($accessToken) {
//                             foreach($fcmToken as $token){
                                
//                                 $responses = $this->sendFCM(
//                                     $accessToken,
//                                     $token,
//                                     'Your Bid Has Been Rejected!',
//                                     'Job ID ' . $get_job->job_no . ': Your bid has been rejected by ' . auth()->user()->name,
//                                     [
//                                         'caller' => auth()->user()->name,
//                                         'type'   => 'reject_notification',
//                                         'url'   => env('APP_URL') . 'jobs',
                                        
//                                     ]
//                                 );
                                
//                             }
//                         }
//                     }
                    
//                     // return $responses;
                    
//                 }
                    
//                 return response()->json([
//                     'status' => true,
//                     'data' => [],
//                     'message' => 'Bidder rejected successfully.'
//                 ]);
//             }else{
//                 DB::table('cus_job_temp')
//                     ->where('id', $get_job->id)
//                     ->where('deletes', '=', '0')
//                     ->update([
                        
//                         'job_status'   => 'cancelled'
                        
//                     ]);
                    
//                 $firebase = new \App\Services\FirebaseJobService(
//                     $this->serviceAccount['project_id'],
//                     $this->getAccessToken()
//                 );
                
//                 $firebase->updateBidStatus(
//                     $get_job->job_no,
//                     $request->bidder_id,
//                     'cancelled'
//                 );
                    
//                 // DB::table('user_register')
//                 //     ->where('id', $get_job->user_id)->where('deletes', '0')
//                 //     ->decrement('complete_jobs', 1);
                    
//                 return response()->json([
//                     'status' => true,
//                     'data' => [],
//                     'message' => 'Job cancelled successfully.'
//                 ]);
                
//             }

//             return response()->json([
//                 'status' => false,
//                 'message' => 'Failed to Accept job.'
//             ], 200);
    
    
//         } catch (ValidationException $e) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Validation failed.',
//                 'errors' => $e->errors()
//             ], 422);
//         }catch (\Exception $e) {
    
//             return response()->json([
//                 'status' => false,
//                 'data' => null,
//                 'message' => $e->getMessage(),
//                 'error' => $e->getMessage() 
//             ], 500);
//         }
//     }

    public function reject_bid(Request $request)
    {
        try {
            $request->validate([
                'job_id'    => ['required', 'string'],
                'bidder_id' => ['required', 'string'],
            ]);
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found'
                ]);
            }
    
            if (empty($job->bids_details)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No bids found in DB'
                ]);
            }
    
            $bids = json_decode($job->bids_details, true);
    
            if (!isset($bids[$request->bidder_id])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found'
                ]);
            }
    
            /*
            |--------------------------------------------------------------------------
            | STEP 1: UPDATE STATUS
            |--------------------------------------------------------------------------
            */
            $bids[$request->bidder_id]['status'] = 'reject';
    
            /*
            |--------------------------------------------------------------------------
            | STEP 2: CHECK IF ALL REJECTED → CANCEL JOB
            |--------------------------------------------------------------------------
            */
            $allRejected = collect($bids)->every(fn($b) => ($b['status'] ?? '') === 'reject');
    
            // $newStatus = $allRejected ? 'cancelled' : 'bidding';
            $newStatus = $job->reject_count == 0 ? 'bidding' : 'cancelled';
            $re_count = $job->reject_count == 0 ? 1 : 2;
    
            /*
            |--------------------------------------------------------------------------
            | STEP 3: SAVE
            |--------------------------------------------------------------------------
            */
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'bids_details' => json_encode($bids),
                    'job_status'   => $newStatus,
                    'reject_count'   => $re_count,
                ]);
                
            $firebase = new FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
            
            $firebase->rejectBid(
                $job->job_no,
                $request->bidder_id
            );
            
            if($newStatus == 'cancelled'){
                
                $firebase->deleteJob($job->job_no);
            }
    
            DB::table('user_register')
                ->where('id', $job->user_id)
                ->where('deletes', '0')
                ->decrement('complete_jobs');
    
            $bidder = DB::table('user_register')
                ->where('id', $request->bidder_id)
                ->where('deletes', '0')
                ->first();
    
            if (!$bidder) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bidder not found'
                ]);
            }
    
            $amount = $bids[$request->bidder_id]['amount'] ?? $job->fare;
    
            if (Controller::checkWhatsApp(['mobile' => $bidder->mobile])) {
    
                $message = "
    Hello {$bidder->name}, 👋
    
    Your bid for job {$job->job_no} has been rejected.
    
    Pickup: {$job->from_place}
    Drop: {$job->to_place}
    Date: " . \Carbon\Carbon::parse($job->pickup_date)->format('d-m-Y h:i A') . "
    Fare: ₹{$amount}
    
    Try other jobs 🚀
    ";
    
                Controller::sendNotification([
                    'mobile' => $bidder->mobile,
                    'templateName' => 'national_draw_verification',
                    'language' => 'en',
                    'templateBodyParam' => [],
                    'messages' => $message,
                    'resend' => false
                ]);
            }
    
            // ✅ FCM
            $tokens = $this->getFcm([$bidder->id]);
    
            if ($tokens) {
                $accessToken = $this->getAccessToken();
    
                foreach ($tokens as $token) {
                    $this->sendFCM(
                        $accessToken,
                        $token,
                        'Bid Rejected',
                        "Your bid for job {$job->job_no} was rejected",
                        ['type' => 'reject_notification']
                    );
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => $allRejected == 'cancelled'
                    ? 'All bidders rejected. Job cancelled.'
                    : 'Bidder rejected successfully.'
            ]);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function reject_bid_admin(Request $request)
    {
        try {
            $request->validate([
                'uid' => ['required', 'max:255'],
                'job_id' => ['required', 'max:255'],
                'bidder_id' => ['required', 'max:255'],
            ]);
            
            $get_job = DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->where('deletes', '=', '0')
                    // ->where('reject_count', '<', 1)
                    ->first();
                    
            if($get_job->global_type == 'customer'){
                
                $user = DB::table('customer_register')->where(['id' => $request->uid, 'deletes' => '0'])->first();
            }else{
                $user = DB::table('user_register')->where(['id' => $request->uid, 'deletes' => '0'])->first();
                
            }
                    
            
            if ($get_job) {
                
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                
                $firebaseDoc = $firebase->getJob($get_job->job_no);
                
                $job = $this->parseFirestoreFields($firebaseDoc);
                
                $amount = $job['bids_details'][$request->bidder_id]['amount'];
                
                $firebase->updateBidStatus(
                    (string) $get_job->job_no,
                    (string) $request->bidder_id,
                    'reject'
                );
                    
                // DB::table('user_register')
                //     ->where('id', $get_job->user_id)->where('deletes', '0')
                //     ->decrement('complete_jobs', 1);
                
                $get_bidder = DB::table('user_register')->where('deletes', '0')
                    ->where('id', $request->bidder_id)->first();
                    
                // $get_bidder_amt = DB::table('open_jobs')
                //     ->where('id', $get_job->id)
                //     ->where('deletes', '=', '0')
                //     ->whereRaw("JSON_EXTRACT(bids_details, '$.\"$request->bidder_id\"') IS NOT NULL")
                //     ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"$request->bidder_id\".amount')) as amount"))
                //     ->first();

                // $amount = $get_bidder_amt ? $get_bidder_amt->amount : $get_job->fare;
                    
                    
                $existsWhatsApp = Controller::checkWhatsApp([
                    'mobile' => $get_bidder->mobile
                ]);
                
                if ($existsWhatsApp) {
                    
                    
$messages = "
Hello {$get_bidder->name}, 👋

We appreciate your interest in bidding for the job *{$get_job->job_no}*.  

Unfortunately, your bid was not selected by ".$user->name ." this time.  

📌 Job Details:  
🔹 Pickup: {$get_job->from_place} 
🔹 Drop-off: {$get_job->to_place}  
🔹 Date: " . \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A') . "    
🔹 Fare:₹ {$amount}
🔹 Contact Person: ". $user->mobile ."

❗ Don’t worry — many new jobs are being posted every day.  
You can explore other opportunities and place your bids again.  

We value your efforts and wish you success in your upcoming bids 🚀  

Thanks for using " . env('APP_NAME') . ".  ";

                    // $randotp = '123456';
    
                    $whatsAppArr = [
                        'mobile' => $get_bidder->mobile,
                        'templateName' => 'national_draw_verification',
                        'language' => 'en',
                        'templateBodyParam' => [],
                        'messages' => $messages,
                        'resend' => false
                    ];
    
                    $sentsms = Controller::sendNotification($whatsAppArr);
                    
                    $fcmToken = $this->getFcm([$get_bidder->id]);
                    
                    if ($fcmToken) {
                        $accessToken = $this->getAccessToken();
                        if ($accessToken) {
                            foreach($fcmToken as $token){
                                
                                $responses = $this->sendFCM(
                                    $accessToken,
                                    $token,
                                    'Your Bid Has Been Rejected!',
                                    'Job ID ' . $get_job->job_no . ': Your bid has been rejected by ' . $user->name,
                                    [
                                        'caller' => $user->name,
                                        'type'   => 'reject_notification',
                                        'url'   => env('APP_URL') . 'jobs',
                                        
                                    ]
                                );
                                
                            }
                        }
                    }
                    
                    // return $responses;
                    
                }
                    
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'Bidder rejected successfully.'
                ]);
            }else{
                DB::table('cus_job_temp')
                    ->where('id', $get_job->id)
                    ->where('deletes', '=', '0')
                    ->update([
                        // 'bids_details' => DB::raw("JSON_SET(bids_details, '$.\"{$request->bidder_id}\".status', 'cancelled')"),
                        'job_status'   => 'cancelled'
                        
                    ]);
                    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                
                $firebase->updateBidStatus(
                    $get_job->job_no,
                    $request->bidder_id,
                    'cancelled'
                );
                    
                // DB::table('user_register')
                //     ->where('id', $get_job->user_id)->where('deletes', '0')
                //     ->decrement('complete_jobs', 1);
                    
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'Job cancelled successfully.'
                ]);
                
            }

            return response()->json([
                'status' => false,
                'message' => 'Failed to Accept job.'
            ], 200);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function bidding_statusOld(Request $request)
    {
        try {
    
            $userId = auth()->id();
    
            $inStatus = ['accept', 'reject', 'direct', 'inreview'];
            $jobStatusAllowed = ['accept', 'bidding', 'cancelled'];
    
            /* -------------------------------------------------
               ROLE CHECK (UNCHANGED)
            --------------------------------------------------*/
            $check_role = DB::table('kyc_details')->where([
                'user_id' => $userId,
                'type'    => 'Driver'
            ])->first();
    
            if (auth()->user()->isBidding == 0 && $check_role) {
                return response()->json([
                    'status'  => true,
                    'data'    => [],
                    'message' => 'Job Details got successfully.'
                ]);
            }
    
            /* -------------------------------------------------
               FIREBASE: FETCH RECENT JOBS
            --------------------------------------------------*/
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebaseDocs = $firebase->listJobs([
                'since' => now()->subDays(10)->toIso8601String(),
                'limit' => 50
            ]);
            
            // return $firebaseDocs;
    
            if (empty($firebaseDocs)) {
                return response()->json([
                    'status'  => true,
                    'data'    => [],
                    'message' => 'Job Details got successfully.'
                ]);
            }
    
            $results = [];
            
            // return $firebaseDocs;
    
            /* -------------------------------------------------
               PROCESS FIREBASE JOBS
            --------------------------------------------------*/
            foreach ($firebaseDocs as $doc) {
    
                if (!isset($doc['fields'])) {
                    continue;
                }
    
                $job = $this->parseFirestoreFields($doc['fields']);
    
                if (!in_array($job['job_status'] ?? null, $jobStatusAllowed)) {
                    continue;
                }
    
                if (empty($job['pickup_date']) || Carbon::parse($job['pickup_date'])->lt(Carbon::now())) {
                    continue;
                }
    
                if (($job['user_id'] ?? null) == $userId) {
                    continue;
                }
    
                $bids = $job['bids_details'] ?? [];
    
                if (!isset($bids[$userId])) {
                    continue;
                }
    
                $userBid = $bids[$userId];
    
                if (!in_array($userBid['status'] ?? null, $inStatus)) {
                    continue;
                }
                
                // if ($job['fare']) {
                //     // $job->fare = (string) ((int) $job->fare * 0.95);
                //     $userBid['amount'] = (string) ((float) $job['fare'] * 0.95);
                // }
                // $job['fare'] = $userBid['amount'];
                
                if ($job['fare']) {
                    // $job->fare = (string) ((int) $job->fare * 0.95);
                    $job['fare'] = (string) ((float) $job['base_fare'] + (float) $job['toll_fare']);
                }
    
                $poster = DB::table('customer_register')
                    ->where('id', $job['user_id'])
                    ->where('deletes', '0')
                    ->first();
    
                $assigned_driver = null;
    
                if (!empty($job['assigned_to'])) {
                    $driver = DB::table('user_register')
                        ->where('id', $job['assigned_to'])
                        ->first();
    
                    if ($driver) {
                        $assigned_driver = [
                            'name'   => $driver->name,
                            'email'  => $driver->email,
                            'mobile' => $driver->mobile,
                        ];
                    }
                }
                
                if($job['job_status'] == 'accept' && ($userBid['status'] == 'inreview' || $userBid['status'] == 'direct') ){
                    $userBid['status'] = 'reject';
                }elseif($job['job_status'] == 'cancelled'){
                    
                    $userBid['status'] = 'cancelled';
                }
                
                // return $job;
                
                $s_res = [
                    // 'job_no'               => $job['job_no'] ?? null,
                    // 'job_type'             => $job['job_type'] ?? null,
                    // 'from_place'           => $job['from_place'] ?? null,
                    // 'to_place'             => $job['to_place'] ?? null,
                    // 'pickup_date'          => $job['pickup_date'] ?? null,
                    // 'fare'                 => $job['fare'] ?? null,
                    // 'distance'             => $job['distance'] ?? null,
                    // 'job_status'           => $job['job_status'] ?? null,
                    // 'created_at'           => $job['created_at'] ?? null,
                    // $job,
    
                    'poster_name'          => $poster->name ?? null,
                    'poster_email'         => $poster->email ?? null,
                    'poster_mobile'        => $poster->mobile ?? null,
                    'poster_company'       => $poster->company_name ?? null,
                    'poster_ratings'       => $poster->ratings ?? null,
                    'poster_complete_jobs' => $poster->complete_jobs ?? null,
    
                    'user_bid_status'      => $userBid['status'] ?? null,
                    'user_bid_amount'      => $userBid['amount'] ?? null,
                    'user_bid'             => 'yes',
                    'user_like_status'     => (
                        isset($job['liked_users']) &&
                        in_array($userId, (array) $job['liked_users'])
                    ) ? '1' : '0',
                    'bids_count'           => is_array($bids) ? count($bids) : 0,
                    'assigned_driver'      => $assigned_driver,
                ];
                
                $results[] = array_merge($job, $s_res);
            }
    
            return response()->json([
                'status'  => true,
                'data'    => array_slice($results, 0, 20),
                'message' => 'Job Details got successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status'  => false,
                'data'    => null,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    // public function bidding_status(Request $request)
    // {
    //     try {
    
    //         $userId = auth()->id();
    
    //         $inStatus = ['accept', 'reject', 'direct', 'inreview'];
    //         $jobStatusAllowed = ['accept', 'bidding', 'cancelled'];
    
    //         /* -------------------------------------------------
    //           ROLE CHECK (UNCHANGED)
    //         --------------------------------------------------*/
    //         $check_role = DB::table('kyc_details')->where([
    //             'user_id' => $userId,
    //             'type'    => 'Driver'
    //         ])->first();
    
    //         if (auth()->user()->isBidding == 0 && $check_role) {
    //             return response()->json([
    //                 'status'  => true,
    //                 'data'    => [],
    //                 'message' => 'Job Details got successfully.'
    //             ]);
    //         }
    
    //         /* -------------------------------------------------
    //           FETCH FROM NODE CACHE
    //         --------------------------------------------------*/
    //         $nodeUrl = env('NODE_CACHE_URL') . '/get-collection/'.env('FIREBASE_COLLECTION');
    
    //         $response = Http::withBasicAuth(
    //             env('NODE_CACHE_USER'),
    //             env('NODE_CACHE_PASS')
    //         )
    //         ->timeout(5)
    //         ->get($nodeUrl);
    
    //         if (!$response->successful()) {
    //             return response()->json([
    //                 'status' => true,
    //                 'data' => [],
    //                 'message' => 'Job Details got successfully.',
    //             ], 200);
    //         }
    
    //         $firebaseDocs = $response->json();
    //         $results = [];
    
    //         foreach ($firebaseDocs as $job) {
    
    //             if (!in_array($job['job_status'] ?? null, $jobStatusAllowed)) {
    //                 continue;
    //             }
                
    //             $pickup = Carbon::parse($job['pickup_date'])->setTimezone('Asia/Kolkata');

                
    //             $job['pickup_date'] = $pickup->format('Y-m-d H:i:s');
    
    //             if (empty($job['pickup_date']) || Carbon::parse($job['pickup_date'])->lt(Carbon::now())) {
    //                 continue;
    //             }
    
    //             if (($job['user_id'] ?? null) == $userId) {
    //                 continue;
    //             }
    
    //             $bids = $job['bids_details'] ?? [];
    
    //             if (!isset($bids[$userId])) {
    //                 continue;
    //             }
                
    //             $job['pass_count'] = $job['pass_count'] == 'mini' ? '4 Mini' : $job['pass_count'] - 1;
    
    //             $userBid = $bids[$userId];
    
    //             if (!in_array($userBid['status'] ?? null, $inStatus)) {
    //                 continue;
    //             }
    
    //             if (!empty($job['base_fare']) && !empty($job['toll_fare'])) {
    //                 $job['fare'] = (string)((float)$job['base_fare'] + (float)$job['toll_fare']);
    //             }
    
    //             $poster = DB::table('customer_register')
    //                 ->where('id', $job['user_id'])
    //                 ->where('deletes', '0')
    //                 ->first();
    
    //             $assigned_driver = null;
    
    //             if (!empty($job['assigned_to'])) {
    //                 $driver = DB::table('user_register')
    //                     ->where('id', $job['assigned_to'])
    //                     ->first();
    
    //                 if ($driver) {
    //                     $assigned_driver = [
    //                         'name'   => $driver->name,
    //                         'email'  => $driver->email,
    //                         'mobile' => $driver->mobile,
    //                     ];
    //                 }
    //             }
    
    //             if ($job['job_status'] == 'accept' &&
    //                 ($userBid['status'] == 'inreview' || $userBid['status'] == 'direct')) {
    
    //                 $userBid['status'] = 'reject';
    
    //             } elseif ($job['job_status'] == 'cancelled') {
    
    //                 $userBid['status'] = 'cancelled';
    //             }
    
    //             $s_res = [
    //                 'poster_name'          => $poster->name ?? null,
    //                 'poster_email'         => $poster->email ?? null,
    //                 'poster_mobile'        => $poster->mobile ?? null,
    //                 'poster_company'       => $poster->company_name ?? null,
    //                 'poster_ratings'       => $poster->ratings ?? null,
    //                 'poster_complete_jobs' => $poster->complete_jobs ?? null,
    
    //                 'user_bid_status'      => $userBid['status'] ?? null,
    //                 'user_bid_amount'      => $userBid['amount'] ?? null,
    //                 'user_bid'             => 'yes',
    
    //                 'user_like_status'     => (
    //                     isset($job['liked_users']) &&
    //                     in_array($userId, (array)$job['liked_users'])
    //                 ) ? '1' : '0',
    
    //                 'bids_count'           => is_array($bids) ? count($bids) : 0,
    //                 'assigned_driver'      => $assigned_driver,
    //             ];
    
    //             $results[] = array_merge($job, $s_res);
    //         }
    
    //         return response()->json([
    //             'status'  => true,
    //             'data'    => array_slice($results, 0, 20),
    //             'message' => 'Job Details got successfully.'
    //         ]);
    
    //     } catch (\Exception $e) {
    
    //         return response()->json([
    //             'status'  => false,
    //             'data'    => null,
    //             'message' => $e->getMessage(),
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function bidding_statusOldDB(Request $request)
    {
        try {
    
            $userId = auth()->id();
    
            $inStatus = ['accept', 'reject', 'direct', 'inreview'];
            $jobStatusAllowed = ['accept', 'bidding', 'cancelled', 'created'];
    
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->exists();
    
            if (auth()->user()->isBidding == 0 && $check_role) {
                return response()->json([
                    'status'  => true,
                    'data'    => [],
                    'message' => 'Job Details got successfully.'
                ]);
            }
    
            $firebaseDocs = DB::table('cus_job_temp as oj')

                ->leftJoin('customer_register as cr', function ($join) {
                    $join->on('cr.id', '=', 'oj.user_id')
                         ->where('oj.global_type', 'customer');
                })
                ->leftJoin('user_register as ur', function ($join) use ($userId) {
                    $join->on('ur.id', '=', 'oj.user_id')
                         ->where('oj.id', '!=', $userId) // 🔥 corrected column
                         ->where('oj.global_type', '!=', 'customer');
                })
            
                ->leftJoin('user_register as dr', 'dr.id', '=', 'oj.assigned_to')
            
                // ->where('oj.user_id', '!=', $userId)
                ->where('oj.deletes', '0')
                ->whereIn('oj.job_status', $jobStatusAllowed)
                ->where('oj.created_at', '>=', now()->subDays(10))
                ->where('oj.pickup_date', '>=', now())
            
                ->orderByDesc('oj.created_at')
            
                ->select([
                    'oj.*',
            
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.name
                            ELSE ur.name
                        END as poster_name
                    "),
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.email
                            ELSE ur.email
                        END as poster_email
                    "),
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.mobile
                            ELSE ur.mobile
                        END as poster_mobile
                    "),
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.company_name
                            ELSE NULL
                        END as poster_company
                    "),
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.ratings
                            ELSE NULL
                        END as poster_ratings
                    "),
                    DB::raw("
                        CASE 
                            WHEN oj.global_type = 'customer' THEN cr.complete_jobs
                            ELSE NULL
                        END as poster_complete_jobs
                    "),
            
                    'dr.name as driver_name',
                    'dr.email as driver_email',
                    'dr.mobile as driver_mobile',
                ])
            
                ->get();
    
            $results = [];
            $now = now('Asia/Kolkata');
            
            foreach ($firebaseDocs as $job) {
    
                $pickup = Carbon::parse($job->pickup_date)->timezone('Asia/Kolkata');
    
                if ($pickup->lt($now)) continue;
    
                $job->pickup_date = $pickup->format('Y-m-d H:i:s');
    
                $bids = is_array($job->bids_details)
                    ? $job->bids_details
                    : json_decode($job->bids_details, true) ?? [];
    
                if (!isset($bids[$userId])) continue;
    
                $userBid = $bids[$userId];
    
                if (!in_array($userBid['status'] ?? null, $inStatus)) continue;
    
                $job->pass_count = ($job->pass_count === 'mini')
                    ? '4 Mini'
                    : $job->pass_count - 1;
    
                if (!empty($job->base_fare) || !empty($job->toll_fare)) {
                    $job->fare = (float)$job->base_fare + (float)$job->toll_fare;
                }
    
                $assigned_driver = null;
                if (!empty($job->assigned_to)) {
                    $assigned_driver = [
                        'name'   => $job->driver_name,
                        'email'  => $job->driver_email,
                        'mobile' => $job->driver_mobile,
                    ];
                }
    
                if ($job->job_status == 'accept' &&
                    in_array($userBid['status'], ['inreview', 'direct'])) {
    
                    $userBid['status'] = 'reject';
    
                } elseif ($job->job_status == 'cancelled') {
    
                    $userBid['status'] = 'cancelled';
                }
    
                $likedUsers = is_array($job->liked_users)
                    ? $job->liked_users
                    : json_decode($job->liked_users, true) ?? [];
    
                $results[] = [
                    ... (array) $job,
    
                    'user_bid_status' => $userBid['status'] ?? null,
                    'user_bid_amount' => $userBid['amount'] ?? null,
                    'user_bid'        => 'yes',
    
                    'user_like_status' => in_array($userId, $likedUsers) ? '1' : '0',
                    'bids_count'       => count($bids),
    
                    'assigned_driver'  => $assigned_driver,
                ];
            }
    
            return response()->json([
                'status'  => true,
                'data'    => array_slice($results, 0, 20),
                'message' => 'Job Details got successfully.'
            ]);
    
        } catch (\Exception $e) {
            
            \Log::info('FCM send error', [$e->getMessage()]);
    
            return response()->json([
                'status'  => false,
                'data'    => null,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function bidding_status(Request $request)
    {
        try {
    
            $userId = auth()->id();
    
            $inStatus = ['accept', 'reject', 'direct', 'inreview'];
            $jobStatusAllowed = ['accept', 'bidding', 'cancelled', 'created', 'schedule'];
    
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type'    => 'Driver'
                ])->exists();
    
            if (auth()->user()->isBidding == 0 && $check_role) {
                return response()->json([
                    'status'  => true,
                    'data'    => [],
                    'message' => 'Job Details got successfully.'
                ]);
            }
    
            $firebaseDocs = Cache::remember('bidding_job_global', 5, function () use ($jobStatusAllowed) {

                return DB::table('cus_job_temp as oj')
        
                    ->leftJoin('customer_register as cr', function ($join) {
                        $join->on('cr.id', '=', 'oj.user_id')
                             ->whereIn('oj.global_type', ['customer', 'schedule']);
                    })
        
                    ->leftJoin('user_register as ur', function ($join) {
                        $join->on('ur.id', '=', 'oj.user_id')
                             ->where('oj.global_type', '!=', 'customer');
                    })
        
                    ->leftJoin('user_register as dr', 'dr.id', '=', 'oj.assigned_to')
        
                    ->where('oj.deletes', '0')
                    ->whereIn('oj.job_status', $jobStatusAllowed)
                    ->where('oj.created_at', '>=', now()->subDays(10))
                    ->where('oj.pickup_date', '>=', now())
        
                    ->orderByDesc('oj.created_at')
        
                    ->select([
                        'oj.*',
        
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.name ELSE ur.name END as poster_name"),
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.email ELSE ur.email END as poster_email"),
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.mobile ELSE ur.mobile END as poster_mobile"),
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.company_name ELSE NULL END as poster_company"),
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.ratings ELSE NULL END as poster_ratings"),
                        DB::raw("CASE WHEN oj.global_type = 'customer' THEN cr.complete_jobs ELSE NULL END as poster_complete_jobs"),
        
                        'dr.name as driver_name',
                        'dr.email as driver_email',
                        'dr.mobile as driver_mobile',
                    ])
        
                    ->get();
            });
    
            $results = [];
            $now = now('Asia/Kolkata');
            
            foreach ($firebaseDocs as $job) {

                // ✅ Time filter
                $pickup = Carbon::parse($job->pickup_date)->timezone('Asia/Kolkata');
                if ($pickup->lt($now)) continue;
                
                if($job->global_type == 'schedule'){
                    
                    // dd('hi');
                    
                    if($job->job_status == 'accept'){
                        
                        $bids = is_array($job->sch_status)
                            ? $job->sch_status
                            : json_decode($job->sch_status, true) ?? [];
                            
                        $dateString = $pickup->format('Y-m-d');
                        $bids = $bids[$dateString];
                        
                        // $userBid = $bids[$dateString][$userId];
                    }    
                    
                }else{
                    
                    $bids = is_array($job->bids_details)
                        ? $job->bids_details
                        : json_decode($job->bids_details, true) ?? [];
                }
                
                if (!isset($bids[$userId])) continue;
        
                $userBid = $bids[$userId];
        
                if (!in_array($userBid['status'] ?? null, $inStatus)) continue;
        
                $job->pickup_date = $pickup->format('Y-m-d H:i:s');
        
                $job->pass_count = ($job->pass_count === 'mini')
                    ? '4 Mini'
                    : ($job->global_type == 'open' ? $job->pass_count : $job->pass_count - 1);
        
                // ✅ Fare calculation
                $job->fare = (float)($job->base_fare ?? 0) + (float)($job->toll_fare ?? 0);
        
                // ✅ Assigned driver
                $assigned_driver = null;
                if (!empty($job->assigned_to)) {
                    $assigned_driver = [
                        'name'   => $job->driver_name,
                        'email'  => $job->driver_email,
                        'mobile' => $job->driver_mobile,
                    ];
                }
        
                // ✅ Status adjustments
                if ($job->job_status == 'accept' &&
                    in_array($userBid['status'], ['inreview', 'direct'])) {
        
                    $userBid['status'] = 'reject';
        
                } elseif ($job->job_status == 'cancelled') {
        
                    $userBid['status'] = 'cancelled';
                }elseif(in_array($userBid['status'], ['inreview', 'direct'])){
                    $userBid['status'] = 'inreview';
                    
                }
        
                // ✅ Likes
                $likedUsers = is_array($job->liked_users)
                    ? $job->liked_users
                    : json_decode($job->liked_users, true) ?? [];
        
                // ✅ Final result
                $results[] = [
                    ...(array) $job,
        
                    'user_bid_status' => $userBid['status'] ?? null,
                    'user_bid_amount' => $userBid['amount'] ?? null,
                    'user_bid'        => 'yes',
        
                    'user_like_status' => in_array($userId, $likedUsers) ? '1' : '0',
                    'bids_count'       => count($bids),
        
                    'assigned_driver'  => $assigned_driver,
                ];
            }
    
            return response()->json([
                'status'  => true,
                'data'    => array_slice($results, 0, 20),
                'message' => 'Job Details got successfully.'
            ]);
    
        } catch (\Exception $e) {
            
            \Log::info('FCM send error', [$e->getMessage()]);
    
            return response()->json([
                'status'  => false,
                'data'    => null,
                'message' => $e->getMessage(),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function bidding_statusCopy(Request $request)
    {
        try {
            
            
            $userId = auth()->user()->id;
            
            $inStatus = ['accept', 'reject', 'direct', 'inreview'];
            
            $arr_sat = ['accept', 'bidding', 'cancelled'];
            
            $check_role = DB::table('kyc_details')->where([
                'user_id' => auth()->user()->id,
                'type' => 'Driver'
            ])->first();
            
            if(auth()->user()->isBidding == 0 && $check_role){
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'Job Details got successfully.'
                ]);
            }
            
        
            // Instead of the open job table use firebase job and complete the code
            $get_jobs = DB::table('cus_job_temp as oj')
                    ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
                    ->where('oj.user_id', '!=', $userId)
                    ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
                    ->where('oj.pickup_date', '>=', Carbon::now())
                    ->where('oj.deletes', '=', '0')
                    ->where('ur.deletes', '=', '0')
                    ->whereIn('oj.job_status', $arr_sat)
                    ->whereRaw("
                        JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$userId\"')
                        AND JSON_EXTRACT(oj.bids_details, '$.\"$userId\".status') IN ('accept', 'reject', 'inreview', 'direct')
                    ")
                    ->select(
                        'oj.*',
                        'ur.name as poster_name',
                        'ur.email as poster_email',
                        'ur.mobile as poster_mobile',
                        'ur.company_name as poster_company',
                        'ur.ratings as poster_ratings',
                        'ur.complete_jobs as poster_complete_jobs',
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"$userId\".status')) as user_bid_status"),
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"$userId\".amount')) as user_bid_amount"),
                        DB::raw("IF(JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$userId\"'), 'yes', 'no') as user_bid"),
                        DB::raw("IF(JSON_CONTAINS(oj.liked_users, '[$userId]'), '1', '0') as user_like_status"),
                        DB::raw("JSON_LENGTH(JSON_KEYS(oj.bids_details)) as bids_count"),
                        DB::raw("
                            CASE 
                                WHEN oj.assigned_to IS NULL THEN NULL
                                ELSE (
                                    SELECT JSON_OBJECT(
                                        'name', name,
                                        'email', email,
                                        'mobile', mobile
                                    )
                                    FROM user_register 
                                    WHERE id = oj.assigned_to
                                    LIMIT 1
                                )
                            END AS assigned_driver
                        ")


                    )
                    ->orderBy('oj.id', 'DESC')
                    ->limit(20)
                    ->get();
            
            return response()->json([
                'status' => true,
                'data' => $get_jobs,
                'message' => 'Job Details got successfully.'
            ]);
    
    
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function assigned_job(Request $request)
    {
        try {

            $user = auth()->user();
            $userId = $user->id;
            $ownerId = $user->owner_id;
    
            $arr_sat = ['accept'];
            $inStatus = ['accept', 'reject', 'direct', 'inreview'];
    
            $check_role = DB::table('kyc_details')
                ->where([
                    'user_id' => $userId,
                    'type' => 'Driver'
                ])
                ->first();
    
            $final_jobs = collect();
    
            if ($ownerId != 0 && $ownerId != null && $check_role) {
    
                $owner_jobs = DB::table('open_jobs as oj')
                    ->leftJoin('user_register as ur', 'ur.id', '=', 'oj.assigned_to')
                    ->leftJoin('user_register as ur2', 'ur2.id', '=', DB::raw($ownerId))
                    ->where('oj.assigned_to', '!=', $userId)
                    ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
                    ->where('oj.pickup_date', '>=', Carbon::now())
                    ->where('oj.deletes', '0')
                    ->where('ur.deletes', '0')
                    ->whereIn('oj.job_status', $arr_sat)
                    ->whereRaw("
                        JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$ownerId\"')
                        AND JSON_EXTRACT(oj.bids_details, '$.\"$ownerId\".status') IN ('accept','reject','inreview','direct')
                    ")
                    ->select(
                        'oj.*',
                        'ur.name as poster_name',
                        'ur.email as poster_email',
                        'ur.mobile as poster_mobile',
                        'ur.company_name as poster_company',
                        'ur.ratings as poster_ratings',
                        'ur.complete_jobs as poster_complete_jobs',
                        'ur2.name as owner_name',
                        'ur2.email as owner_email',
                        'ur2.mobile as owner_mobile',
                        'ur2.company_name as owner_company',
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"$ownerId\".status')) as user_bid_status"),
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"$ownerId\".amount')) as user_bid_amount"),
                        DB::raw("IF(JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$ownerId\"'), 'yes', 'no') as user_bid"),
                        DB::raw("JSON_LENGTH(JSON_KEYS(oj.bids_details)) as bids_count"),
                        DB::raw("
                            CASE 
                                WHEN oj.assigned_to IS NULL THEN NULL
                                ELSE (
                                    SELECT JSON_OBJECT(
                                        'name', name,
                                        'email', email,
                                        'mobile', mobile
                                    )
                                    FROM user_register 
                                    WHERE id = oj.assigned_to
                                    LIMIT 1
                                )
                            END AS assigned_driver
                        ")
                    )
                    ->orderBy('oj.id', 'DESC')
                    ->limit(20)
                    ->get();
    
                $final_jobs = $final_jobs->merge($owner_jobs);
            }
    
            return response()->json([
                'status' => true,
                'data' => $final_jobs,
                'message' => 'Job Details fetched successfully.'
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function job_owner(Request $request)
    {
        try {
            $request->validate([
                'user_id' => ['required', 'string', 'max:255'],
                'j_type' => ['required', 'string', 'in:customer,open'],
            ]);

            
            $Id = $request->user_id;
            
            if($request->j_type == 'customer'){
                $tbl = 'customer_register';
            }else{
                $tbl = 'user_register';
            }
            
            $get_profile = DB::table($tbl.' as oj')
                    ->where('id', $Id)->where('deletes', '0')
                    ->select(
                        'name',
                        'email',
                        'mobile',
                        'mobile_verify',
                        'company_name',
                        'ratings',
                        'profile_img_url',
                        'complete_jobs'
                    )
                    ->first();
            
            
            return response()->json([
                'status' => true,
                'data' => $get_profile,
                'message' => 'Profile retrieved successfully.'
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function update_like(Request $request)
    {
        try {
            $request->validate([
                'job_id' => ['required', 'string', 'max:255'],
                'status' => ['required', 'in:on,off']
            ]);
            
            $userId =auth()->user()->id;
            
            // $Id = $request->job_id;
            
            $job = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('deletes', '=', '0')
                ->where('user_id', '!=', $userId)
                ->first();
            
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'data'    => null,
                    'message' => 'Job not found or Can\'t like own Job'
                ]);
            }
            
            $likedUsers = !empty($job->liked_users) ? json_decode($job->liked_users, true) : [];

            if (!is_array($likedUsers)) {
                $likedUsers = [];
            }
            
            if ($request->status === 'on') {
                // add user if not already liked
                if (!in_array($userId, $likedUsers)) {
                    $likedUsers[] = $userId;
                }
            } else {
                if (($key = array_search($userId, $likedUsers)) !== false) {
                    unset($likedUsers[$key]);
                    $likedUsers = array_values($likedUsers);
                }
            }

            
            DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('deletes', '=', '0')
                ->update([
                    'liked_users' => json_encode($likedUsers)
                ]);
            
            
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Job '. ($request->status == 'on' ? 'Liked' : 'Disliked')
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function liked_jobs(Request $request)
    {
        try {
            
            $userId = auth()->id();

            $get_jobs = DB::table('open_jobs as oj')
                ->join('user_register as ur', 'ur.id', '=', 'oj.user_id')
                ->where('oj.user_id', '!=', $userId)
                ->where('oj.created_at', '>=', Carbon::now()->subDays(10))
                ->where('oj.pickup_date', '>=', Carbon::now())
                ->where('oj.deletes', '=', '0')
                ->where('ur.deletes', '=', '0')
                ->whereIn('job_status', ['created', 'bidding'])
                ->whereRaw("JSON_CONTAINS(oj.liked_users, ?)", [json_encode((int) $userId)])
                ->select(
                    'oj.*',
                    'ur.name as poster_name',
                    'ur.email as poster_email',
                    'ur.mobile as poster_mobile',
                    'ur.company_name as poster_company',
                    'ur.ratings as poster_ratings',
                    'ur.complete_jobs as poster_complete_jobs',
                    DB::raw("IF(JSON_CONTAINS(oj.liked_users, '[$userId]'), '1', '0') as user_like_status"),
                    DB::raw("IF(JSON_CONTAINS(JSON_KEYS(oj.bids_details), '\"$userId\"'), 'yes', 'no') as user_bid"),
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"$userId\".status')) as user_bid_status"),
                    DB::raw("IFNULL(JSON_LENGTH(oj.bids_details), 0) as bids_count")
                )
                ->orderByDesc('oj.id')
                ->limit(20)
                ->get();

            
            
            return response()->json([
                'status' => true,
                'data' => $get_jobs,
                'message' => 'Data retrieved successfully.'
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    public function preferedLoc_store(Request $request)
    {
        try {
            
            $request->validate([
                'location' => ['nullable', 'array'],
                'location.*' => ['nullable', 'string', 'max:255'],
                'distance' => ['nullable', 'numeric', 'min:0'],  
                'min_distance' => ['nullable', 'numeric', 'min:0'],  
                'pass'     => ['nullable', 'numeric', 'min:0'],   
                'min_pass'     => ['nullable', 'numeric', 'min:0'],   
                'fare'     => ['nullable', 'numeric', 'min:0'],
                'min_fare'     => ['nullable', 'numeric', 'min:0'],
            ]);
            
            $userId = auth()->id();
            
            $loc = '';

            if ($request->location && !empty($request->location) && is_array($request->location)) {
                $loc = implode('|', $request->location);
            }
            
            $get_jobs = [
                'location' => $loc??'',
                'distance' => $request->distance??'',
                'min_distance' => $request->min_distance??'',
                'pass'     => $request->pass??'',
                'min_pass'     => $request->min_pass??'',
                'fare'     => $request->fare??'',
                'min_fare'     => $request->min_fare??'',
            ];
            
            // If your database column is JSON type, you can directly store array; otherwise encode as string
            $update_pref = DB::table('user_register')
                ->where('id', $userId)->where('deletes', '0')
                ->update([
                    'prefered_location' => json_encode($get_jobs)
                    
                ]);
            
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Filters updated successfully.'
            ]);
            
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function preferedLoc_get(Request $request)
    {
        try {
            
            $pre_loc = auth()->user()->prefered_location;
            
            return response()->json([
                'status' => true,
                'data' => $pre_loc,
                'message' => 'Filters get successfully.'
            ]);
            
    
    
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function cancel_bid(Request $request)
    {
        try {
            
            $request->validate([
                'job_id' => ['required', 'string'],
                // 'bidder_id' => ['required', 'string', 'max:255']
            ]);
            
            $userId = auth()->id();
            
            $job = DB::table('cus_job_temp')
                // ->where('user_id', $userId)
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->whereIn('job_status', ['bidding', 'created', 'accept'])
                ->first();
            
            
            if ($job) {
                
                if (Carbon::parse($job->pickup_date)->lte(Carbon::now()->addHour())) {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Your cancel time has expired.'
                    ]);
                }
                
                $firebase = new FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                
                $firebase->cancelBid(
                    $job->job_no,
                    $userId
                );
                
                DB::table('cus_job_temp')
                    ->where('id', $request->job_id)
                    ->update([
                        'bids_details' => DB::raw("JSON_REMOVE(bids_details, '$.\"$userId\"')")
                    ]);
                
                return response()->json([
                    'status' => true,
                    'data' => [],
                    'message' => 'Your Bid removed successfully.'
                ]);
                
            }else{
                
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Job Not Found.'
                ]);
            }
    
        } catch (ValidationException $e) {
            \Log::info('Hiiiiiiiiiiiiii', [$e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
            \Log::info('Hiiiiiiiiiiiiii', [$e->getMessage()]);
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function cancel_bid_copy(Request $request)
    {
        try {
            
            $request->validate([
                'job_id' => ['required', 'string'],
                // 'bidder_id' => ['required', 'string', 'max:255']
            ]);
            
            $userId = auth()->id();
            
            $job = DB::table('open_jobs')
                // ->where('user_id', $userId)
                ->where('id', $request->job_id)
                ->where('deletes', '0')
                ->whereIn('job_status', ['bidding', 'created', 'accept'])
                ->first(['id', 'bids_details', 'pickup_date']);
            
            
            if ($job) {
                
                if (Carbon::parse($job->pickup_date)->lte(Carbon::now()->addHour())) {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Your cancel time has expired.'
                    ]);
                }
                
                
                $bids = json_decode($job->bids_details, true);
            
                if (!empty($bids) && array_key_exists($userId, $bids)) {
                    
                    $u_d = [];
                    if ($bids[$userId]['status'] == 'accept') {
                        $u_d['job_status'] = 'bidding';
                    }
                    
                    unset($bids[$userId]);
                    
                    $u_d['bids_details'] = json_encode($bids);
            
                    DB::table('open_jobs')
                        ->where('id', $job->id)
                        ->update($u_d);
                        
                    return response()->json([
                        'status' => true,
                        'data' => [],
                        'message' => 'Your Bid removed successfully.'
                    ]);
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Bid Not Found.'
                    ]);
                }
                
            }else{
                
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Job Not Found.'
                ]);
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function cancel_job(Request $request)
    {
        try {
            
            $request->validate([
                'job_id' => ['required'],
                'user_id' => ['nullable'],
                'job_no' => ['nullable'],
                'job_type' => ['nullable'],
                'auth_key' => ['nullable', 'string', 'max:255']
            ]);
            
            $userId = $request->user_id ? $request->user_id : auth()->id();
            
            if (!empty($request->user_id)) {
                if ($request->auth_key != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Unauthorized.',
                    ], 401);
                }
            }
            
            $get_job = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('job_no', $request->job_no)
                ->orWhere('id', $request->job_id)
                ->where('deletes', '0')
                ->first();

            if (!$get_job) {
                \Log::info('Testing...: ' . json_encode($request->job_no));
                return response()->json([
                    'status' => false,
                    'message' => 'Job Not Found'
                ], 404);
            }

            // Fetch Bidders before deleting
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
            
            $jobDoc = $firebase->getJob($get_job->job_no);
            $job = $jobDoc ? $this->parseFirestoreFields($jobDoc) : [];
            $get_bidders_ids = array_keys($job['bids_details'] ?? []);
            
            // =========================================================================
            // 🚀 FAST SYNC: Delete from Firebase IMMEDIATELY
            // =========================================================================
            $firebase->deleteJob($get_job->job_no);

            // =========================================================================
            // 🚀 FAST SYNC: Update MySQL DB IMMEDIATELY
            // =========================================================================
            DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('job_no', $request->job_no)
                ->orWhere('id', $request->job_id)
                ->where('deletes', '0')
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0 // Set confirm_status back to 0
                ]);
                
            DB::table('open_jobs')
                ->where('job_no', $request->job_no)
                ->orWhere('id', $request->job_id)
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0 // Keep tables synced
                ]);
            
            $request->job_type = $request->job_type ??'open';
            
            // =========================================================================
            // 🚀 FAST SYNC: Send WhatsApp to Job Owner IMMEDIATELY
            // =========================================================================
            if($request->job_type == 'customer'){
                $get_u = DB::table('customer_register')
                        ->where('id', $userId)
                        ->where('deletes', 0)->first();
            }else{
                $get_u = DB::table('user_register')
                        ->where('id', $userId)
                        ->where('deletes', '0')->first();
            }
            
            if ($get_u) {
                $existsWhatsApp = \App\Http\Controllers\Controller::checkWhatsApp([
                    'mobile' => $get_u->mobile
                ]);

                if ($existsWhatsApp) {
                    $messages2 = "
Hello {$get_u->name}, 👋  

Your job **{$get_job->job_no}** has been ❌ *cancelled* By Admin.  

📌 Job Details:  
🔹 Pickup: {$get_job->from_place}  
🔹 Drop-off: {$get_job->to_place}  
🔹 PickUp Date: " . \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A') . "  
";

                    $whatsAppArr2 = [
                        'mobile' => $get_u->mobile,
                        'templateName' => 'national_draw_verification',
                        'language' => 'en',
                        'templateBodyParam' => [],
                        'messages' => $messages2,
                        'resend' => false
                    ];

                    \App\Http\Controllers\Controller::sendNotification($whatsAppArr2);
                }
            }

            // =========================================================================
            // ⏳ QUEUE THE HEAVY TASKS (Bidders WhatsApp & FCM) IN BACKGROUND
            // =========================================================================
            
            // Pass values into the queue safely
            $jobData = (array) $get_job;
            $biddersIds = $get_bidders_ids;

            dispatch(function () use ($jobData, $biddersIds) {
                
                // Re-instantiate controller inside queue to get fresh tokens
                $controller = app(\App\Http\Controllers\Api\OpenJobsController::class);
                $accessToken = $controller->getAccessToken();
                
                $get_job = (object) $jobData;
                
                // 1. WhatsApp to Bidders
                if (count($biddersIds) > 0) {
                    $get_bidders = DB::table('user_register')
                        ->whereIn('id', $biddersIds)
                        ->where('deletes', '0')
                        ->select('id', 'name', 'mobile')
                        ->get();

                    foreach ($get_bidders as $bidders) {
                        $existsWhatsApp = \App\Http\Controllers\Controller::checkWhatsApp([
                            'mobile' => $bidders->mobile
                        ]);

                        if ($existsWhatsApp) {
                            $messages = "
Hello {$bidders->name}, 👋  

Unfortunately, your bid for job **{$get_job->job_no}** has been ❌ *cancelled* by the job owner.

📌 Job Details:  
🔹 Pickup: {$get_job->from_place}  
🔹 Drop-off: {$get_job->to_place}  
🔹 PickUp Date: " . \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A') . "  
";

                            $whatsAppArr = [
                                'mobile' => $bidders->mobile,
                                'templateName' => 'national_draw_verification',
                                'language' => 'en',
                                'templateBodyParam' => [],
                                'messages' => $messages,
                                'resend' => false
                            ];

                            \App\Http\Controllers\Controller::sendNotification($whatsAppArr);
                        }
                    }
                }
                
                // 2. FCM Push Notifications to Bidders
                if(count($biddersIds) > 0){
                    $fcmTokens = $controller->getFcm($biddersIds);
                
                    if ($fcmTokens && count($fcmTokens) && $accessToken) {
                        foreach ($fcmTokens as $token) {
                            try {
                                $controller->sendFCM(
                                    $accessToken,
                                    $token,
                                    'Your Bid Has Been Cancelled',
                                    'Job ID ' . $get_job->job_no . ': Unfortunately, the job has been cancelled by job owner.',
                                    [
                                        'caller' => 'Job Owner',
                                        'type'   => 'cancel_notification',
                                        'url'    => env('APP_URL') . 'jobs',
                                    ]
                                );
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::error('FCM send error for token: ' . $token, [
                                    'message' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }

            }); 
            // =========================================================================
            // 🛑 END QUEUE
            // =========================================================================

            // IMMEDIATE SUCCESS RESPONSE
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Job cancelled successfully.'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::info('Testing...: ' . json_encode($e->getMessage()));
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
    
    public function cancelRide(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $request->validate([
                'job_id' => ['required']
            ]);
    
            $user   = auth()->user();
            $userId = $user->id;
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('assigned_to', $userId)
                ->where('job_status', 'accept')
                // ->where('job_no', $request->job_no)
                ->where('deletes', '0')
                ->lockForUpdate()
                ->first();
    
            if (!$job) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'This job is no longer available or cannot be canceled at this time.'
                ]);
            }
            
            $g_type = null;
                
            if($job->global_type == 'customer' || $job->global_type == 'schedule'){
                $tbl = 'customer_register';
                $g_type = 'customer';
            }else{
                $tbl = 'user_register';
            }
                
            $customer = DB::table($tbl)
                ->where([
                    'id'        => $job->user_id,
                    'deletes'       => '0'
                ])
                ->first();
    
            // ✅ Immediately mark as cancelled (prevents second request execution)
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update(['job_status' => 'cancelled']);
    
            $pickupTime = \Carbon\Carbon::parse($job->pickup_date);
            $now        = \Carbon\Carbon::now();
    
            $ch_payment = DB::table('payment_history')
                ->where([
                    'job_no'        => $job->job_no,
                    'user_id'       => $customer->id,
                    'paymentStatus' => 'success'
                ])
                ->first();
            
    
            $fare_break = json_decode($job->fare_breakdown, true);
    
            if ($ch_payment && $customer) {
    
                // ✅ Prevent duplicate refund
                $alreadyRefunded = DB::table('walletBalance_history')
                    ->where('reference_id', $ch_payment->id)
                    ->where('transaction_type', 'REFUND')
                    ->exists();
    
                $isTime = ($pickupTime->diffInMinutes($now, false) >= -60);
    
                // if ($job->isView == 0 && !$isTime && !$pickupTime->isPast() && !$alreadyRefunded) {
                if ($job->isView == 0 && !$alreadyRefunded && $job->deductAmt == 0) {
    
                    // 💰 Wallet Refund
                    $opening        = $customer->walletBalance ?? 0;
                    $expectedAmount = $ch_payment->grandtotal + $ch_payment->wallet_amt;
                    $closing        = $opening + $expectedAmount;
    
                    DB::table('walletBalance_history')->insert([
                        'userid'           => $customer->id,
                        'uname'            => $customer->name,
                        'umobile'          => $customer->mobile,
                        'uemail'           => $customer->email,
                        'opening_balance'  => $opening,
                        'total'            => $expectedAmount,
                        'closeing_balance' => $closing,
                        'point_type'       => 'WALLET',
                        'transaction_type' => 'REFUND',
                        'reward_type'      => 'WalletDeposit',
                        'global_type' => $g_type,
                        'reference_id'     => $ch_payment->id,
                        'reference_table'  => 'payment_history',
                        'ip'               => $request->ip(),
                        'createdon'        => now(),
                        'updatedon'        => now()
                    ]);
    
                    DB::table($tbl)
                        ->where('id', $customer->id)
                        ->update([
                            'walletBalance' => $closing
                        ]);
    
                    // 🎁 Cashback Refund
                    if (!empty($fare_break['isDiscount']) && $fare_break['isDiscount'] == 'yes') {
    
                        $cashOpening = $customer->cash_points ?? 0;
                        $cashClosing = $cashOpening + $fare_break['discount'];
    
                        DB::table('walletBalance_history')->insert([
                            'userid'           => $customer->id,
                            'uname'            => $customer->name,
                            'umobile'          => $customer->mobile,
                            'uemail'           => $customer->email,
                            'opening_balance'  => $cashOpening,
                            'total'            => $fare_break['discount'],
                            'closeing_balance' => $cashClosing,
                            'point_type'       => 'CREDIT',
                            'transaction_type' => 'REFUND',
                            'reward_type'      => 'WalletDeposit',
                            'global_type' => $g_type,
                            'reference_id'     => $ch_payment->id,
                            'reference_table'  => 'payment_history',
                            'ip'               => $request->ip(),
                            'createdon'        => now(),
                            'updatedon'        => now()
                        ]);
    
                        DB::table($tbl)
                            ->where('id', $customer->id)
                            ->update([
                                'cash_points' => $cashClosing
                            ]);
                    }
                }
            }
    
            // 🔔 Notifications & Firebase
            if ($job->global_type != 'schedule' && $job->job_status != 'accept') {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
    
                $firebase->updateJobStatus($job->job_no, 'cancelled');
    
                $bidders   = $firebase->getJobBidders($job->job_no) ?? [];
                $bidderIds = !empty($bidders) ? array_keys($bidders) : [];
    
                if (!empty($bidderIds)) {
                    dispatch(new \App\Jobs\SendFcmNotificationJob(
                        type: 'job_cancelled',
                        userIds: $bidderIds,
                        title: 'Job Cancelled',
                        body: 'Job ' . $job->job_no . ' has been cancelled by the owner.'
                    ));
                }
    
                $firebase->deleteJob($job->job_no);
    
            } else {
    
                if ($job->assigned_to) {
                    dispatch(new \App\Jobs\SendFcmNotificationJob(
                        type: 'job_cancelled',
                        userIds: [$job->assigned_to],
                        title: 'Job Cancelled',
                        body: 'Job ' . $job->job_no . ' has been cancelled by the owner.'
                    ));
                }
            }
    
            // 📝 Cancellation logs
            // if (!empty($request->reason) || (!empty($request->docs) && is_array($request->docs))) {
    
                $cancelId = DB::table('job_cancellations')->insertGetId([
                    'job_id'      => $job->id,
                    'customer_id' => $customer->id,
                    'cancelled_by' => $userId,
                    'reason'      => $request->reason??null,
                    'created_at'  => now()
                ]);
    
                if (!empty($request->docs) && is_array($request->docs)) {
    
                    $uniqueDocs = array_values(array_unique($request->docs));
    
                    $insertData = [];
    
                    foreach ($uniqueDocs as $doc) {
                        $insertData[] = [
                            'cancellation_id' => $cancelId,
                            'doc_url'         => $doc,
                            'created_at'      => now()
                        ];
                    }
    
                    DB::table('job_cancellation_docs')->insert($insertData);
                }
            // }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Job cancelled successfully.'
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            \Log::error('Cancel Job Failed', [
                'error' => $e->getMessage()
            ]);
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function delete_job(Request $request)
    {
        try {
            
            $request->validate([
                'job_id' => ['required'],
                'user_id' => ['nullable'],
                'job_no' => ['nullable'],
                'auth_key' => ['nullable', 'string', 'max:255']
            ]);
            
            $userId = $request->user_id ? $request->user_id : auth()->id();
            
            if (!empty($request->user_id)) {
                
                if ($request->auth_key != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Unauthorized.',
                    ], 401);
                }
                
            }
            
            $get_job = DB::table('cus_job_temp')
            ->where('user_id', $userId)
            ->where('job_no', $request->job_no)
            ->where('deletes', '0')
            ->first();

        
        $firebase = new \App\Services\FirebaseJobService(
            $this->serviceAccount['project_id'],
            $this->getAccessToken()
        );
        if($get_job){
            
            // $firebase->deleteJob(
            //     $get_job->job_no
            // );
            
            $firebase->updateJobStatus(
                $get_job->job_no,
                'cancelled'
            );
            
            DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('id', $request->job_id)
                ->update(['deletes' => '1']);
        }

            
        DB::table('open_jobs')
            ->where('user_id', $userId)
            ->where('id', $request->job_id)
            ->update(['deletes' => '1']);

        return response()->json([
            'status' => true,
            'data' => [],
            'message' => 'Job deleted successfully.'
        ]);
            
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function report_user(Request $request)
    {
        try {
            
            $request->validate([
                'reason' => ['required', 'string'],
                'message' => ['required', 'string'],
                'job_type' => ['required', 'string'],
                'attachments'   => ['nullable','array','max:2'],
                'attachments.*' => ['image','max:5120'],   
                'user_id' => ['required', 'numeric']
            ]);
        
            // if (auth()->user()->doc_verify != 1) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Only document verified users can report.'
            //     ], 403);
            // }
            
            // dd();
    
            DB::beginTransaction();
            // 1. Insert complaint record
            $complaintId = DB::table('complain_user')->insertGetId([
                'user_id'      => $request->user_id,
                'job_type'      => $request->job_type,
                'reporter_id'  => auth()->id(),
                'reason'       => $request->reason,
                'message'      => $request->message,
                'status'       => 'pending',
                'created_at'   => now(),
                'updated_at'   => now()
            ]);
            
            // $complaintId = '';
    
            $s3Urls = [];
    
            // 2. Upload attachments (if any)
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    // dd('hippp');
                    // Save temporarily
                    $tempPath = $file->store('temp_reports');
            
                    // Target path in S3
                    $fileName = basename($tempPath);
                    $s3Path = 'complaints/'.$complaintId.'/'.$fileName;
            
                    // Upload file to S3
                    $uploaded = Storage::disk('s3')->put($s3Path, Storage::get($tempPath));
            
                    if (!$uploaded) {
                        DB::rollBack();
                        throw new \Exception('Complaint submission failed');
                        // return response()->json([
                        //     'status' => false,
                        //     'message' => 'S3 upload failed'
                        // ], 200);
                    }
            
                    // Make file public
                    Storage::disk('s3')->setVisibility($s3Path, 'public');
            
                    // Get public URL
                    $url = Storage::disk('s3')->url($s3Path);
                    $s3Urls[] = $url;
            
                    // Delete local temp
                    Storage::delete($tempPath);
                }
                // dd('heeeei');
            }
    
            // 3. Update record with attachment URLs
            DB::table('complain_user')
                ->where('id', $complaintId)
                ->update(['attachments' => json_encode($s3Urls)]);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Complaint submitted successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to submit complaint: '.$e->getMessage()
            ], 500);
        }
    }
    
    public function notify_status(Request $request)
    {
        try {
            $request->validate([
                'status' => ['required', 'in:0,1']
            ]);
            
            $userId =auth()->user()->id;
            
            DB::table('user_register')
                ->where('id', $userId)
                ->where('deletes', '=', '0')
                ->update([
                    'notify' => $request->status
                ]);
            
            return response()->json([
                'status' => true,
                'data' => $request->status,
                'message' => 'Notification '. ($request->status == '0' ? 'Off' : 'On')
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }
    
    public function pro_update(Request $request)
    {
        try {
            
            $request->validate([
                // 'type' => ['required', 'string'],
                'low_pass_notify' => ['nullable'],
                // 'proof_type' => ['required', 'string'],
                // 'proof_url' => ['required', 'array'],
                // 'proof_url.*' => ['url'],
                // 'aadhar' => ['required', 'array'],
                // 'aadhar.*' => ['url'],
                'vehicle' => ['required', 'array'],
            ]);
            
            // return auth()->user()->currentAccessToken();
            
            if($request->vehicle){
                $v_types = collect($request->vehicle)->pluck('type')->unique()->values()->toArray();
                $seats = json_encode($v_types);
                $request->vehicle = json_encode($request->vehicle);
            }else{
                $seats = null;
                $request->vehicle = null;
            }
            
            // if($request->proof_url){
            //     $proof_url = collect($request->proof_url)->unique()->values()->toArray();
            //     $request->proof_url = json_encode($proof_url);
            // }else{
            //     $request->proof_url = null;
            // }
            
            // if($request->aadhar){
            //     $aadhar = collect($request->aadhar)->unique()->values()->toArray();
            //     $request->aadhar = json_encode($aadhar);
            // }else{
            //     $request->aadhar = null;
            // }
            
            $userId = auth()->user()->id;
            
            DB::table('user_register')
                ->where('id', $userId)
                ->where('deletes', '=', '0')
                ->update([
                    // 'seaters' => $seats,
                    'vehicle_details' => $request->all(),
                    'vehicle_verify' => 1,
                    // 'aadhar_images' => $request->aadhar,
                    'low_pass_notify' => $request->low_passenger_count ?? 0,
                ]);
                
            $get_id = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('id')->first();
                
            $kycId = $get_id->id;
            $userId = auth()->user()->id;
                
            $link = "https://console.goride.run/kyc-verify/verify/{$userId}/{$kycId}";
            $title = "Vehicle Details Updated — ". auth()->user()->name;
            
            // data payload
            $data = [
                'user_id' => $userId,
                'user_name' => auth()->user()->name,
                'kyc_id' => $kycId,
                'status' => 'Inreview',
                'changes' => null,
            ];
            
            NotificationService::create('kyc.updated', $title, $data, $link, $userId);
            
            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Vehicle Uploaded.'
            ]);
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        }catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage() 
            ], 500);
        }
    }

    private function handleBlockUser($complaintId, $userId)
    {
        $complaint = DB::table('complain_user')
            ->where(['id' => $complaintId, 'user_id' => $userId, 'status' => 'pending'])
            ->first();
    
        if (!$complaint) {
            return response()->json([
                'status'  => false,
                'message' => 'Complaint not found.'
            ], 404);
        }
    
        // Cancel all jobs
        $jobs = DB::table('open_jobs')
            ->where('user_id', $userId)
            ->whereIn('job_status', ['created', 'bidding'])
            ->get();
    
        $bidders = $this->extractBidders($jobs);
    
        DB::table('open_jobs')
            ->where('user_id', $userId)
            ->whereIn('job_status', ['created', 'bidding'])
            ->update(['job_status' => 'cancelled']);
            
        $check_block = DB::table('blocked_user')->where(['user_id' => $complaint->user_id, 'status' => 0])->first();
        
        if(!$check_block){
            
            // Insert block entry
            DB::table('blocked_user')->insert([
                'user_id'     => $complaint->user_id,
                'reported_by' => $complaint->reporter_id,
                'block_date'  => now(),
                'expiry_date' => now()->addDays(6),
                'day_count'   => 7,
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
            
        }
        
         DB::table('complain_user')
            ->where(['id' => $complaintId, 'user_id' => $userId])
            ->update(['status' => 'blocked']);
    
        // Notify all bidders
        $this->notifyBidders($bidders);
    
        // Notify reported user
        $this->notifyReportedUser($complaint);
    
        return 'success';
    }
    
    private function extractBidders($jobs)
    {
        $ids = [];
    
        foreach ($jobs as $job) {
            if (!empty($job->bids_details)) {
                $bids = json_decode($job->bids_details, true);
                if (is_array($bids)) {
                    $ids = array_merge($ids, array_map('intval', array_keys($bids)));
                }
            }
        }
    
        return array_unique($ids);
    }
    
    private function notifyBidders($bidderIds)
    {
        if (empty($bidderIds)) {
            return 'failed';
        }
    
        $bidders = DB::table('user_register')
            ->whereIn('id', $bidderIds)
            ->where('deletes', '0')
            ->select('id', 'name', 'mobile')
            ->get();
    
        foreach ($bidders as $b) {
            if (true) {
                $message = "
Hello {$b->name}, 👋  

All of your **bids** on the **job owner’s listings** have been ❌ **cancelled**.  
Don’t worry—you can **explore** and **place new bids** on other available jobs. 🚀  

– GoRide
";
                DB::table('whatsapp_bulk_message')->insert([
                    'details'     => $message,
                    'name'        => $b->name,
                    'status'      => 'pending',
                    'to_whatsapp' => $b->mobile,
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        }
    
        // Send FCM notifications
        $fcmTokens = $this->getFcm($bidderIds);
        if ($fcmTokens && count($fcmTokens)) {
            $accessToken = $this->getAccessToken();
            if ($accessToken) {
                foreach ($fcmTokens as $token) {
                    try {
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            'All Your Bids Have Been Cancelled',
                            'All Your Bids Have Been Cancelled by GoRide.',
                            [
                                'caller' => 'Job Owner',
                                'type'   => 'cancel_notification',
                                'url'    => env('APP_URL') . 'jobs',
                            ]
                        );
                    } catch (\Throwable $e) {
                        Log::error('FCM send error', [
                            'token'   => $token,
                            'message' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }
    
    private function notifyReportedUser($id, $complaint)
    {
        $user = DB::table('user_register')
            ->where('id', $complaint->user_id)
            ->where('deletes', '0')
            ->first();
    
        if (!$user || !Controller::checkWhatsApp(['mobile' => $user->mobile])) {
            return 'failed';
        }
    
        $message = "
Hello {$user->name}, 👋  

You have been **reported** for:  
📝 {$complaint->reason}  

As a result, your account has been ❌ **suspended for 7 days**.  
During this period, all of your **jobs** have been **cancelled**.  


If you have any queries, please contact **GoRide Support** at 📩 support@goride.run or 📞 +91 6369742104.  

– GoRide
";
    
        Controller::sendNotification([
            'mobile'           => $user->mobile,
            'templateName'     => 'national_draw_verification',
            'language'         => 'en',
            'templateBodyParam'=> [],
            'messages'         => $message,
            'resend'           => false
        ]);
        
    }
    
    private function notifyUnBlock($id, $complaint)
    {
        $user = DB::table('user_register')
            ->where('id', $complaint)
            ->where('deletes', '0')
            ->first();
    
        if (!$user || !Controller::checkWhatsApp(['mobile' => $user->mobile])) {
            return 'false';
        }
        
        $comp = DB::table('complain_user')
            ->where(['id' => $id, 'user_id' => $complaint, 'status' => 'blocked'])
            ->update(['status' => 'unblocked']);
            
        $comp2 = DB::table('blocked_user')
            ->where(['user_id' => $complaint, 'status' => 0])
            ->update(['status' => 1]);
    
$message = "
Hello {$user->name}, 👋  

Good news! 🎉 Your account has been ✅ *unblocked*.  
You can now continue using GoRide and start posting or bidding on new *jobs* again. 🚀  

If you have any queries, please contact *GoRide Support* at 📩 support@goride.run or 📞 +91 6369742104.  

– GoRide
";
    
        Controller::sendNotification([
            'mobile'           => $user->mobile,
            'templateName'     => 'national_draw_verification',
            'language'         => 'en',
            'templateBodyParam'=> [],
            'messages'         => $message,
            'resend'           => false
        ]);
        
        
    }

    public function block_user(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'       => ['required'],
                'user_id'  => ['required'],
                'status'   => ['required', 'string'],
                'auth_key' => ['required', 'string']
            ]);
    
            if ($validated['auth_key'] != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }
    
            if ($validated['status'] == 'block') {

                $st_check = $this->handleBlockUser($validated['id'], $validated['user_id']);
                
                if($st_check == 'success'){
                    
                    return response()->json([
                        'status'  => true,
                        'message' => 'User Block successfully.'
                    ], 200);
                    
                }else{
                    
                    return response()->json([
                        'status'  => false,
                        'message' => 'User Block failed.'
                    ], 200);
                }
                
            }elseif($validated['status'] == 'unblock'){
                
                $st_check = $this->notifyUnBlock($validated['id'], $validated['user_id']);
                
                if($st_check == 'success'){
                    
                    return response()->json([
                        'status'  => true,
                        'message' => 'User Unblock successfully.'
                    ], 200);
                    
                }else{
                    
                    return response()->json([
                        'status'  => false,
                        'message' => 'User Unblock failed.'
                    ], 200);
                }
            }
    
            return response()->json([
                'status'  => false,
                'message' => 'Invalid status action.'
            ], 400);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('block_user error', ['error' => $e]);
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function check_appVersion(Request $request)
    {
        try {
            $validated = $request->validate([
                'version'   => ['required', 'string'],
                'type'   => ['required', 'string'],
                'authKey' => ['required', 'string']
            ]);
    
            if ($validated['authKey'] != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }
            
            $check_ver = DB::table('app_versions')->where(['version' => $request->version, 'type' => $request->type])->first();
            $get_state = DB::table('states')->where(['country_code' => 'IN'])->get();
            $get_district = DB::table('districts')->select('district_name', 'state')->where(['deletes' => '0'])->get();
            
            if($check_ver){
                
                return response()->json([
                    'status'  => true,
                    'state_count'  => $get_state->count(),
                    'state'  => $get_state,
                    'district'  => $get_district,
                    'message' => 'Up to Date.'
                ], 200);
            }else{
                // return response()->json([
                //     'status'  => false,
                //     'state_count'  => $get_state->count(),
                //     'state'  => $get_state,
                //     'district'  => $get_district,
                //     'message' => 'New version available.'
                // ], 200);
                return response()->json([
                    'status'  => true,
                    'state_count'  => $get_state->count(),
                    'state'  => $get_state,
                    'district'  => $get_district,
                    'message' => 'Up to Date.'
                ], 200);
                
            }
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function get_vehicles(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'user_id'   => ['required', 'string'],
                'authKey' => ['required', 'string']
            ]);
            
            if ($validated['authKey'] != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }
            
            if($request->user_id != ''){
                
                $get_user = DB::table('user_register')->where(['id' => $request->user_id, 'deletes' => '0', 'status' => '0'])->select('vehicle_details')->first();
                
                $get_id = DB::table('kyc_details')
                    ->where(['user_id' => $request->user_id, 'deletes' => 0])
                    ->select('id', 'type')
                    ->first();
                    
                if (!$get_id) {
                    return response()->json([
                        'status' => false,
                        // 'data' => [],
                        'message' => 'KYC Pending.'
                    ]);
                }
                
                if($get_id->type == 'Owner'){
                    
                    $vehicles = DB::table('owner_vehicle_list')
                        ->where([
                            'user_id' => $request->user_id,
                            'deletes' => 0,
                            'verification_status' => 2
                        ])
                        ->pluck('vehicle_details')
                        ->map(function ($v) {
                            return $v ? json_decode($v) : null;
                        });
                    
                    return response()->json([
                        'status' => true,
                        'data' => $vehicles,
                        'seater_count' => null,
                        'message' => 'Vehicles Retrieved'
                    ], 200);
                    
                }else{
                    $get_rc = optional(
                        DB::table('ocr_request')
                            ->where(['user_id' => $request->user_id, 'deletes' => 0, 'doc_type' => 'RC', 'status' => 'ACTIVE'])
                            ->select('doc_type', 'seater', 'doc_no', 'status', 'user_id', 'req_response')
                            ->orderByDesc('id')
                            ->first(),
                        function ($rc) {
                            $rc->req_response = $rc->req_response ? json_decode($rc->req_response, true) : null;
                            return $rc;
                        }
                    );
                    
                    $get_seat = DB::table('ocr_request')
                            ->where(['user_id' => $request->user_id, 'deletes' => 0, 'doc_type' => 'RC'])->orderBy('id', 'DESC')->first();
                    
                    // $details = [
                    //     'rc_details' => $get_rc,
                    //     'vehicle_details' => $get_user ? ($get_user->vehicle_details ? json_decode($get_user->vehicle_details) : null) : null
                    // ];
                    
                    return response()->json([
                        'status'  => true,
                        // 'data' => $get_user->vehicle_details ? json_decode($get_user->vehicle_details) : null,
                        'data' => $get_user ? ($get_user->vehicle_details ? json_decode($get_user->vehicle_details) : null) : null,
                        'seater_count' => $get_seat ? $get_seat->seater : 4,
                        'message' => 'Vehicle Retrived'
                    ], 200);
                    
                }
                
                // return $get_user;
                
                // if($get_user){
                    
                // }else{
                    
                //     return response()->json([
                //         'status'  => true,
                //         'data' => null,
                //         'message' => 'Vehicle Retrived'
                //     ], 200);
                // }
                
            }
    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function user_vehicle(Request $request)
    {
        try {
            
            // dd($request->all());
            
            $validated = $request->validate([
                'user_id'   => ['required', 'string'],
                'authKey' => ['required', 'string']
            ]);
            
            if ($validated['authKey'] != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }
            
            if (!empty($request->user_id)) {

                $user = DB::table('user_register AS ur')
                    ->join('kyc_details AS kd', 'kd.user_id', '=', 'ur.id')
                    ->where([
                        'ur.id' => $request->user_id,
                        'ur.vehicle_verify' => '2',
                        'ur.deletes' => '0',
                        'ur.status' => '0',
                    ])
                    ->select('kd.type', 'ur.vehicle_details', 'ur.drivers_ids', 'ur.id', 'ur.name', 'ur.email','ur.mobile')
                    ->first();
            
                $get_v = null;
                $v_no  = null;
                $u_deatils  = null;
            
                if (!$user) {
                    return response()->json([
                        'status'  => false,
                        'data'    => null,
                        'message' => 'User not found or vehicle not verified.',
                    ], 200);
                }
            
                switch ($user->type) {
                    case 'Driver':
                        
                        $vehicle = json_decode($user->vehicle_details ?? '', true);
                        if (!empty($vehicle)) {
                            $get_v[$user->id] = $vehicle['vehicle'] ?? null;
                            $v_no[$user->id]  = $vehicle['rc_number'] ?? null;
                            $u_deatils[$user->id]  = [
                                'name' => $user->name,
                                'email' => $user->email,
                                'mobile' => $user->mobile,
                            ];
                        }
                        break;
                
                    case 'Owner':
                        
                        $driver_ids = json_decode($user->drivers_ids ?? '', true);
                
                        if (!empty($driver_ids) && is_array($driver_ids)) {
                            
                            $drivers = DB::table('user_register')
                                ->whereIn('id', $driver_ids)
                                ->where(['deletes' => '0', 'status' => '0'])
                                ->select('vehicle_details', 'id', 'name', 'email','mobile')
                                ->get();
                
                            $get_v = [];
                            $v_no  = [];
                
                            foreach ($drivers as $driver) {
                                $v = json_decode($driver->vehicle_details ?? '', true);
                
                                if (!empty($v['vehicle']) && !empty($v['vehicle']['front_view_image_url'])) {
                                    $get_v[$driver->id] = [
                                        "front_view_image_url" => $v['vehicle']['front_view_image_url'] ?? null
                                    ];
                                    
                                    $v_no[$driver->id]  = $v['rc_number'] ?? null;
                                    $u_deatils[$driver->id]  = [
                                        'name' => $driver->name,
                                        'email' => $driver->email,
                                        'mobile' => $driver->mobile,
                                    ];
                                }
                            }
                        }
                        break;
                }

            
                $res_arr = [
                    'vehicle'   => $get_v,
                    'rc_number' => $v_no,
                    'user_details' => $u_deatils,
                ];
            
                return response()->json([
                    'status'  => true,
                    'data'    => $res_arr,
                    'message' => 'Vehicle retrieved successfully.',
                ], 200);
            }

    
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function ownerPro(Request $request)
    {
        try {

            $userId = auth()->id();
            $owner_id = auth()->user()->owner_id;

            $user = DB::table('user_register as ur')
                ->select(
                    'ur.name',
                    'ur.email',
                    'ur.mobile',
                    'ur.company_name',
                    DB::raw("(
                        SELECT COUNT(*)
                        FROM open_jobs AS oj
                        WHERE JSON_UNQUOTE(JSON_EXTRACT(oj.bids_details, '$.\"{$userId}\".status')) = 'accept' AND assigned_to = $userId AND deletes = '0'
                    ) as assigned_count")
                )
                ->where('ur.id', $owner_id)
                ->where('ur.deletes', '0')
                ->where('ur.status', '0')
                ->first();

            return response()->json([
                'status'  => true,
                'data' => $user,
                'message' => 'Data retrieved.'
            ], 200);

    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function driversList(Request $request)
    {
        try {

            $agentId = auth()->id();
            
            // return $agentId;

            // $drivers = DB::table('user_register as d')
            //     ->join('user_register as a', function ($join) use ($agentId) {
            //         $join->where('a.id', '=', $agentId);
            //         $join->whereRaw("JSON_CONTAINS(a.drivers_ids, JSON_QUOTE(CAST(d.id AS CHAR)))");
            //     })
            //     ->where('d.deletes', '0')
            //     ->where('d.status', '0')
            //     ->groupBy('d.id', 'd.name', 'd.mobile', 'd.isBidding', 'd.isOpenjob', 'd.notify')
            //     ->select(
            //         'd.id',
            //         'd.name',
            //         'd.mobile',
            //         'd.isBidding',
            //         'd.isOpenjob',
            //         'd.notify',
            //         'd.vehicle_details'
            //     )
            //     ->orderBy('d.name', 'asc')
            //     ->get();
            
            // $drivers = DB::table('user_register as d')
            //         ->join('user_register as a', function ($join) use ($agentId) {
            //             $join->on(DB::raw('1'), '=', DB::raw('1'))
            //                  ->where('a.id', '=', $agentId)
            //                  ->whereRaw("JSON_CONTAINS(a.drivers_ids, CAST(d.id AS JSON))");
            //         })
            //         ->where('d.deletes', '0')
            //         ->where('d.status', '0')
            //         ->groupBy('d.id', 'd.name', 'd.mobile', 'd.isBidding', 'd.isOpenjob', 'd.notify', 'd.vehicle_details')
            //         ->select(
            //             'd.id',
            //             'd.name',
            //             'd.mobile',
            //             'd.isBidding',
            //             'd.isOpenjob',
            //             'd.notify',
            //             'd.vehicle_details'
            //         )
            //         ->orderBy('d.name', 'asc')
            //         ->get();

            $drivers = DB::table('user_register as d')
                ->join('user_register as a', function ($join) use ($agentId) {
                    $join->on(DB::raw('1'), '=', DB::raw('1'))
                         ->where('a.id', '=', $agentId)
                         ->whereRaw("
                            JSON_CONTAINS(a.drivers_ids, CAST(d.id AS JSON))
                            OR JSON_CONTAINS(a.drivers_ids, JSON_QUOTE(CAST(d.id AS CHAR)))
                         ");
                })
                ->where('d.deletes', '0')
                ->where('d.status', '0')
                ->groupBy('d.id', 'd.name', 'd.mobile', 'd.isBidding', 'd.isOpenjob', 'd.notify', 'd.vehicle_details')
                ->select(
                    'd.id',
                    'd.name',
                    'd.mobile',
                    'd.isBidding',
                    'd.isOpenjob',
                    'd.notify',
                    'd.vehicle_details'
                )
                ->orderBy('d.name', 'asc')
                ->get();





            return response()->json([
                'status'  => true,
                'data' => $drivers,
                'message' => 'Data retrieved.'
            ], 200);

    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function driverAccessModify(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'driver_id' => ['required'],
                'type'      => ['required'],
                'status'    => ['required', 'in:0,1']
            ]);
    
            $agentId = auth()->id();
    
            $updated = DB::table('user_register')
                ->where([
                    'id'      => $validated['driver_id'],
                    'deletes' => '0'
                ])
                ->update([
                    $validated['type'] => $validated['status']
                ]);
    
            return response()->json([
                'status'  => true,
                'data'    => $updated,
                'message' => $updated ? 'Driver access updated' : 'No changes made'
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
    
        }
    }
    
    public function assignDriver(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'job_id'    => ['required', 'integer'],
                'driver_id' => ['required', 'integer']
            ]);
    
            $agentId = auth()->id();
    
            // $checkOwner = DB::table('user_register')
            //     ->where('id', $agentId)
            //     ->whereRaw("JSON_CONTAINS(drivers_ids, JSON_QUOTE(CAST(? AS CHAR)))", [$request->driver_id])
            //     ->first();
            
            $checkOwner = DB::table('user_register')
                ->where('id', $agentId)
                ->where(function ($q) use ($request) {
                    $q->whereRaw("JSON_CONTAINS(drivers_ids, CAST(? AS JSON))", [$request->driver_id])
                      ->orWhereRaw("JSON_CONTAINS(drivers_ids, JSON_QUOTE(CAST(? AS CHAR)))", [$request->driver_id]);
                })
                ->first();

                
            $check_job = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('job_status', 'accept')->whereNull('assigned_to')->first();
    
            if (!$checkOwner) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Driver not included under your account.',
                    'data'    => [],
                ], 200);
            }
            
            
            if($check_job == null){
                return response()->json([
                    'status'  => false,
                    'message' => 'Driver already assigned',
                    'data'    => [],
                ], 200);
            }
    
            $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('job_status', 'accept')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"{$agentId}\".status')) = 'accept'")
                ->update([
                    'assigned_to' => $request->driver_id,
                    'assigned_by' => $agentId
                ]);

             if($updated){
                 
                $dr_de = DB::table('user_register')
                    ->where('id', $request->driver_id)
                    ->where('deletes', '0')
                    ->where('status', '0')
                    ->first();
                if($dr_de){
                    
                    $company = $checkOwner->company_name ? $checkOwner->company_name . ' agency' : 'agency';
        
                    $fareDetails = json_decode($check_job->add_fare_details ?? '{}', true);
                    
                    $fareText = '';
                    if (!empty($fareDetails)) {
                        foreach ($fareDetails as $key => $value) {
                            $fareText .= '   • ' . ucfirst($key) . ': ' . ucfirst($value) . "\n";
                        }
                    } else {
                        $fareText = '   • No additional fare details available.' . "\n";
                    }
                    
                    $fareDetails = json_decode($check_job->bids_details ?? '{}', true);

                    $amount = $fareDetails[$agentId]['amount'] ?? 0;

    
$messages = '📢 *New Job Assigned!* 🚘

Hi ' . $dr_de->name . ', a new job has been assigned to you by *' . $checkOwner->name . '* from *' . $company . '*.

🧾 *Job Details:*  
• *Job ID:* ' . $check_job->job_no . '  
• *Pickup:* ' . $check_job->from_place . '  
• *Drop:* ' . $check_job->to_place . '  
• *Fare:* ₹' . $amount . '  
• *Additional Fare Details:*  
' . $fareText . '• *Status:* Assigned ✅  

Please check your GoRide app for full details and start your trip on time. 🕒  

🚖 *GoRide — Drive Smart. Earn More. Deliver Better.*';
    
                    $whatsAppArr = [
                      'mobile' => $dr_de->mobile,
                      'templateName' => 'national_draw_verification',
                      'language' => 'en',
                      'templateBodyParam' => [
                        ''
                      ],
                      'messages' => $messages,
                      'resend' => ($request->isResend === "true" ? true : false)
                    ];
            
            
                    $sentsms = Controller::sendNotification($whatsAppArr);
                    // return $sentsms;
                    if (!$sentsms) {
                      $whatsAppArr['resend'] = true;
                      $sentsms = Controller::sendNotification($whatsAppArr);
                    //   $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
                    }
                    
                    $fcmToken = $this->getFcm([$request->driver_id]);
                    
                    if ($fcmToken) {
                        $accessToken = $this->getAccessToken();
                        if ($accessToken) {
                            foreach($fcmToken as $token){
                                
                                try {
                                    $responses = $this->sendFCM(
                                        $accessToken,
                                        $token,
                                        '📦 New Job Assigned',
                                        'Job ' . $get_job->job_no . ' has been assigned to you by ' . $checkOwner->name . ' from ' . $company . '. Please review the details and prepare for pickup.',
                                        [
                                            'caller' => auth()->user()->name,
                                            'type'   => 'new_job_assigned',
                                            'url'   => env('APP_URL') . 'jobs',
                                            'owner'    => $checkOwner->name,
                                            'company'  => $company
                                            
                                        ]
                                    );
                                    
                                    
                                } catch (\Throwable $e) {
                                    // Log the error to storage/logs/laravel.log
                                    Log::error('FCM send error for token: ' . $token, [
                                        'message' => $e->getMessage(),
                                        'file'    => $e->getFile(),
                                        'line'    => $e->getLine(),
                                        'trace'   => $e->getTraceAsString(),
                                    ]);
                                }
                                
                            }
                        }
                    }
                    
                }
        
                return response()->json([
                    'status'  => true,
                    'message' => $updated ? 'Job successfully assigned to driver.' : 'No matching job found or already assigned.',
                    'data'    => $updated
                ], 200);
                 
             }
             
            
            
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function re_assignDriver(Request $request)
    {
        try {
            $validated = $request->validate([
                'job_id'    => ['required', 'integer'],
                'driver_id' => ['required', 'integer'],
            ]);
    
            $agentId = auth()->id();
    
            $checkOwner = DB::table('user_register')
                ->where('id', $agentId)
                ->where(function ($q) use ($request) {
                    $q->whereRaw("JSON_CONTAINS(drivers_ids, CAST(? AS JSON))", [$request->driver_id])
                      ->orWhereRaw("JSON_CONTAINS(drivers_ids, JSON_QUOTE(CAST(? AS CHAR)))", [$request->driver_id]);
                })
                ->first();
    
            $check_job = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('job_status', 'accept')
                ->whereNotNull('assigned_to')
                ->first();
    
            if (!$checkOwner) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Driver not included under your account.',
                    'data'    => [],
                ], 200);
            }
    
            if ($check_job == null) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Driver not assigned or Job already start',
                    'data'    => [],
                ], 200);
            }else{
                if($check_job->assigned_count >= 1){
                    return response()->json([
                        'status'  => false,
                        'message' => 'Re assign limit reached.',
                        'data'    => [],
                    ], 200);
                }
            }
    
            $pre_d = $check_job->assigned_to;
    
            $updated = DB::table('open_jobs')
                ->where('id', $request->job_id)
                ->where('job_status', 'accept')
                ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(bids_details, '$.\"{$agentId}\".status')) = 'accept'")
                ->update([
                    'assigned_to' => $request->driver_id,
                    'assigned_count' => 1,
                    'assigned_by' => $agentId
                ]);
    
            if ($updated) {
                $dr_de = DB::table('user_register')
                    ->where('id', $request->driver_id)
                    ->where('deletes', '0')
                    ->where('status', '0')
                    ->first();
    
                if ($dr_de) {
                    $company = $checkOwner->company_name ? $checkOwner->company_name . ' agency' : 'agency';
    
                    $fareDetails = json_decode($check_job->add_fare_details ?? '{}', true);
    
                    $fareText = '';
                    if (!empty($fareDetails)) {
                        foreach ($fareDetails as $key => $value) {
                            $fareText .= '   • ' . ucfirst($key) . ': ' . ucfirst($value) . "\n";
                        }
                    } else {
                        $fareText = '   • No additional fare details available.' . "\n";
                    }
    
                    $bidsDetails = json_decode($check_job->bids_details ?? '{}', true);
                    $amount = $bidsDetails[$agentId]['amount'] ?? 0;
    
                    $messages = '📢 *New Job Assigned!* 🚘
    
    Hi ' . $dr_de->name . ', a new job has been assigned to you by *' . $checkOwner->name . '* from *' . $company . '*.
    
    🧾 *Job Details:*  
    • *Job ID:* ' . $check_job->job_no . '  
    • *Pickup:* ' . $check_job->from_place . '  
    • *Drop:* ' . $check_job->to_place . '  
    • *Pickup-Date:* ' . $check_job->pickup_date . '  
    • *Fare:* ₹' . $amount . '  
    • *Additional Fare Details:*  
    ' . $fareText . '• *Status:* Assigned ✅  
    
    Please check your GoRide app for full details and start your trip on time. 🕒  
    
    🚖 *GoRide — Drive Smart. Earn More. Deliver Better.*';
    
                    $whatsAppArr = [
                        'mobile' => $dr_de->mobile,
                        'templateName' => 'national_draw_verification',
                        'language' => 'en',
                        'templateBodyParam' => [''],
                        'messages' => $messages,
                        'resend' => ($request->isResend === "true" ? true : false)
                    ];
    
                    $sentsms = Controller::sendNotification($whatsAppArr);
    
                    if (!$sentsms) {
                        $whatsAppArr['resend'] = true;
                        $sentsms = Controller::sendNotification($whatsAppArr);
                    }
    
                    $fcmToken = $this->getFcm([$dr_de->id]);
    
                    if ($fcmToken) {
                        $accessToken = $this->getAccessToken();
                        if ($accessToken) {
                            foreach ($fcmToken as $token) {
                                try {
                                    $responses = $this->sendFCM(
                                        $accessToken,
                                        $token,
                                        '📦 New Job Assigned',
                                        'Job ' . $check_job->job_no . ' has been assigned to you by ' . $checkOwner->name . ' from ' . $company . '. Please review the details and prepare for pickup.',
                                        [
                                            'caller' => auth()->user()->name,
                                            'type'   => 'new_job_assigned',
                                            'url'    => env('APP_URL') . 'jobs',
                                            'owner'  => $checkOwner->name,
                                            'company'=> $company
                                        ]
                                    );
                                } catch (\Throwable $e) {
                                    Log::error('FCM send error for token: ' . $token, [
                                        'message' => $e->getMessage(),
                                        'file'    => $e->getFile(),
                                        'line'    => $e->getLine(),
                                        'trace'   => $e->getTraceAsString(),
                                    ]);
                                }
                            }
                        }
                    }
                }
    
                if ($pre_d) {
                    $pre_dr_de = DB::table('user_register')
                        ->where('id', $pre_d)
                        ->where('deletes', '0')
                        ->where('status', '0')
                        ->first();
    
                    if ($pre_dr_de) {
                        $company = $checkOwner->company_name ? $checkOwner->company_name . ' agency' : 'agency';
    
                        $fareDetails = json_decode($check_job->add_fare_details ?? '{}', true);
    
                        $fareText = '';
                        if (!empty($fareDetails)) {
                            foreach ($fareDetails as $key => $value) {
                                $fareText .= '   • ' . ucfirst($key) . ': ' . ucfirst($value) . "\n";
                            }
                        } else {
                            $fareText = '   • No additional fare details available.' . "\n";
                        }
    
                        $bidsDetails = json_decode($check_job->bids_details ?? '{}', true);
                        $amount = $bidsDetails[$agentId]['amount'] ?? 0;
    
                        $messages = '🔄 *Job Reassigned to Another Driver* 🚘
    
    Hi ' . $pre_dr_de->name . ', the job (ID: *' . $check_job->job_no . '*) that was previously assigned to you has been *reassigned* by *' . $checkOwner->name . '* from *' . $company . '*.
    
    🧾 *Job Summary:*  
    • *Pickup:* ' . $check_job->from_place . '  
    • *Drop:* ' . $check_job->to_place . '  
    • *Pickup-Date:* ' . $check_job->pickup_date . '  
    • *Fare:* ₹' . $amount . '  
    
    You no longer need to take any action for this trip.  
    Thank you for your understanding and professionalism. 🙏  
    
    🚖 *GoRide — Drive Smart. Earn More. Deliver Better.*';
    
                        $whatsAppArrr = [
                            'mobile' => $pre_dr_de->mobile,
                            'templateName' => 'national_draw_verification',
                            'language' => 'en',
                            'templateBodyParam' => [''],
                            'messages' => $messages,
                            'resend' => ($request->isResend === "true" ? true : false)
                        ];
    
                        $sentsms = Controller::sendNotification($whatsAppArrr);
    
                        if (!$sentsms) {
                            $whatsAppArrr['resend'] = true;
                            $sentsms = Controller::sendNotification($whatsAppArrr);
                        }
    
                        $fcmToken = $this->getFcm([$pre_dr_de->id]);
    
                        if ($fcmToken) {
                            $accessToken = $this->getAccessToken();
                            if ($accessToken) {
                                foreach ($fcmToken as $token) {
                                    try {
                                        $responses = $this->sendFCM(
                                            $accessToken,
                                            $token,
                                            '🔄 Job Reassigned',
                                            'Job ' . $check_job->job_no . ' has been reassigned to another driver by ' . $checkOwner->name . ' from ' . $company . '. No further action is required on your end.',
                                            [
                                                'caller' => auth()->user()->name,
                                                'type'   => 'job_reassigned',
                                                'url'    => env('APP_URL') . 'jobs',
                                                'owner'  => $checkOwner->name,
                                                'company'=> $company
                                            ]
                                        );
                                    } catch (\Throwable $e) {
                                        Log::error('FCM send error for token: ' . $token, [
                                            'message' => $e->getMessage(),
                                            'file'    => $e->getFile(),
                                            'line'    => $e->getLine(),
                                            'trace'   => $e->getTraceAsString(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
    
                return response()->json([
                    'status'  => true,
                    'message' => $updated ? 'Job successfully assigned to driver.' : 'No matching job found or already assigned.',
                    'data'    => $updated
                ], 200);
            }
            
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function removeOwner(Request $request)
    {
        try {
            
            // $validated = $request->validate([
            //     'job_id'    => ['required', 'integer'],
            //     'driver_id' => ['required', 'integer']
            // ]);
    
            $dr_id = auth()->id();

            $checkDriver = DB::table('user_register')
                ->where('id', $dr_id)
                ->first();
            
            if ($checkDriver) {
            
                $get_owner = DB::table('user_register')
                    ->where('id', $checkDriver->owner_id)
                    ->first();
            
                if ($get_owner && !empty($get_owner->drivers_ids)) {
            
                    $drivers = json_decode($get_owner->drivers_ids, true) ?? [];
            
                    $drivers = array_filter($drivers, function ($id) use ($checkDriver) {
                        return $id != $checkDriver->id;
                    });
            
                    $drivers = array_values($drivers);
            
                    DB::table('user_register')
                        ->where('id', $get_owner->id)
                        ->update([
                            'drivers_ids' => json_encode($drivers)
                        ]);
                }
            
                $updated = DB::table('user_register')
                    ->where('id', $checkDriver->id)
                    ->update([
                        'owner_id' => null
                    ]);
            
                return response()->json([
                    'status'  => true,
                    'message' => 'Owner removed successfully.',
                    'data'    => $updated
                ], 200);
            }
            
            return response()->json([
                'status'  => false,
                'message' => 'Driver not found.',
            ], 404);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function schedule_create(Request $request)
    {
        $data = $request->validate([
            "driver_id"      => ["nullable"],
            'routes'         => ['required', 'array', 'min:1'],
            'routes.*.from'  => ['required', 'string'],
            'routes.*.to'    => ['required', 'string'],
            'routes.*.dates' => ['required', 'array', 'min:1'],
        ]);
    
        $userId = auth()->id();
        $now    = now();
        $result = [];
        
        // if (auth()->user()->walletBalance < 0) {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Insufficient wallet balance. Please top up to schedule your ride.',
        //         'data'    => null,
        //     ]);
        // }
        
        if(auth()->user()->doc_verify != 1){
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Kyc not verified',
            ]);
        }
        
        $check_own = DB::table('kyc_details')->where(['user_id' => $userId, 'deletes' => 0])->select('type')->first();
        if($check_own && $check_own->type == 'Owner'){
            $userId = $request->driver_id;
        }else if($check_own && $check_own->type == 'Driver'){
            if(auth()->user()->vehicle_verify != 2){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Vehicle not verified',
                ]);
            }
        }
    
        DB::beginTransaction();
    
        try {
    
            foreach ($data['routes'] as $route) {
    
                $finalDates = [];
    
                foreach ($route['dates'] as $date => $price) {
    
                    $blocked = DB::table('open_jobs')
                        ->where([
                            'from_place' => $route['from'],
                            'to_place'   => $route['to'],
                            'job_status' => 'accept',
                        ])
                        ->whereRaw(
                            "DATE_ADD(pickup_date, INTERVAL duration MINUTE) >= ?",
                            [$date . ' 00:00:00']
                        )
                        ->whereRaw(
                            "JSON_EXTRACT(bids_details, '$.\"{$userId}\".status') IS NOT NULL"
                        )
                        ->exists();
    
                    if (!$blocked) {
                        $finalDates[$date] = $price;
                    }
                }
    
                if (!$finalDates) {
                    continue;
                }
    
                $record = DB::table('schedule_dates')
                    ->where([
                        'from_place' => $route['from'],
                        'to_place'   => $route['to'],
                        'user_id'    => $userId,
                        'deletes'    => 0,
                    ])
                    ->first();
    
                if (false) {
                // if ($record) {
                    $dates = array_replace(
                        json_decode($record->dates_price, true) ?? [],
                        $finalDates
                    );
    
                    DB::table('schedule_dates')
                        ->where('id', $record->id)
                        ->update([
                            'dates_price' => json_encode($dates),
                            'updated_at'  => $now,
                        ]);
    
                    $action = 'updated';
                } else {
                    $get_id = DB::table('schedule_dates')->insertGetId([
                        'from_place'  => $route['from'],
                        'to_place'    => $route['to'],
                        'user_id'     => $userId,
                        'dates_price' => json_encode($finalDates),
                        'created_at'  => $now,
                    ]);
    
                    $action = 'created';
                }
    
                $result[] = [
                    'id'   => $get_id,
                    'from'   => $route['from'],
                    'to'     => $route['to'],
                    'action' => $action,
                ];
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'data'    => $result,
                'message' => 'Schedule processed successfully.',
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    public function extend_date(Request $request)
    {
        $data = $request->validate([
            'id'        => ['required'],
            'driver_id' => ['nullable']
        ]);
    
        $userId = auth()->id();
        $now    = now();
        $result = [];
    
        if (auth()->user()->doc_verify != 1) {
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Kyc not verified',
            ]);
        }
    
        $check_own = DB::table('kyc_details')
            ->where(['user_id' => $userId, 'deletes' => 0])
            ->select('type')
            ->first();
    
        if ($check_own && $check_own->type === 'Owner') {
            $userId = $request->driver_id;
        } elseif ($check_own && $check_own->type === 'Driver') {
            if (auth()->user()->vehicle_verify != 2) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Vehicle not verified',
                ]);
            }
        }
    
        DB::beginTransaction();
    
        try {
    
            $record = DB::table('schedule_dates')
                ->where([
                    'id'      => $request->id,
                    'user_id' => $userId,
                    'deletes' => 0,
                ])
                ->first();
    
            if ($record) {
    
                $existingDates = json_decode($record->dates_price, true);
    
                if (is_array($existingDates) && !empty($existingDates)) {
    
                    $lastDate = collect(array_keys($existingDates))
                        ->sort()
                        ->last();
    
                    $startDate = \Carbon\Carbon::parse($lastDate)->addDay();
    
                    $date_arr = [];
    
                    for ($i = 0; $i < 7; $i++) {
                        $date_arr[$startDate->format('Y-m-d H:i:s')] = current($existingDates);
                        $startDate->addDay();
                    }
    
                    DB::table('schedule_dates')->insert([
                        'from_place'  => $record->from_place,
                        'to_place'    => $record->to_place,
                        'user_id'     => $userId,
                        'dates_price' => json_encode($date_arr),
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'data'    => $result,
                'message' => 'Schedule processed successfully.',
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    public function schedule_edit(Request $request)
    {
        $validated = $request->validate([
            'id'               => ['required'],
            'route'            => ['required', 'array'],
            'route.from'       => ['required', 'string'],
            'route.to'         => ['required', 'string'],
            'route.dates'      => ['required', 'array', 'min:1'],
        ]);
    
        $userId = auth()->id();
        $now    = now();
        // dd($validated['id'], $userId);
        DB::beginTransaction();
    
        try {
    
            $record = DB::table('schedule_dates')
                ->where('id', $validated['id'])
                // ->where('user_id', $userId)
                ->where('deletes', 0)
                ->first();
    
            if (!$record) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Schedule not found.',
                ]);
            }
            // else {
            //     $data = (array) $record;

            //     unset($data['id']);

            //     DB::table('schedule_past_dates')->insert($data);
            // }
    
            DB::table('schedule_dates')
                ->where('id', $record->id)
                ->update([
                    'dates_price' => json_encode($validated['route']['dates'], JSON_THROW_ON_ERROR),
                    'updated_at'  => $now,
                ]);
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'data'    => [
                    'from'   => $validated['route']['from'],
                    'to'     => $validated['route']['to'],
                    'action' => 'updated',
                ],
                'message' => 'Schedule updated successfully.',
            ], 200);
    
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    public function schedule_delete(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required'],
        ]);
    
        $userId = auth()->id();
        $now    = now();
    
        DB::beginTransaction();
    
        try {
    
            $affected = DB::table('schedule_dates')
                ->where('id', $validated['id'])
                // ->where('user_id', $userId)
                ->where('deletes', 0)
                ->update([
                    'deletes' => 1,
                    'updated_at' => $now,
                ]);
    
            if ($affected == 0) {
                DB::rollBack();
    
                return response()->json([
                    'status'  => false,
                    'message' => 'Schedule not found or already deleted.',
                ], 200);
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'data'    => [],
                'message' => 'Schedule deleted successfully.',
            ], 200);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    public function schedule_check_dates(Request $request)
    {
        try {
            $validated = $request->validate([
                'from'  => ['required', 'string'],
                'to'    => ['required', 'string'],
                // 'date'  => ['required', 'date_format:Y-m-d']
                'date'  => ['required']
            ]);
    
            $user_id = auth()->id();
    
            $record = DB::table('schedule_dates')
                ->where([
                    'from_place' => $validated['from'],
                    'to_place'   => $validated['to'],
                    'user_id'    => $user_id,
                    'deletes'    => 0
                ])->first();
    
            if (!$record) {
                return response()->json([
                    'status'  => true,
                    'data'    => [],
                    'message' => 'Date is clear.',
                ], 200);
            }
    
            $dates = json_decode($record->dates_price, true) ?? [];

            $dateExists = array_key_exists($validated['date'], $dates);
    
            if ($dateExists) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Date already scheduled.',
                ], 200);
            }
    
            return response()->json([
                'status'  => true,
                'data'    => [],
                'message' => 'Date is clear.',
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function available_dates(Request $request)
    {
        try {
            $user_id = auth()->id();
            $today   = now()->format('Y-m-d');

            $bookedDates = DB::select("
                SELECT jt.date_key AS date
                FROM schedule_dates,
                JSON_TABLE(
                    JSON_KEYS(dates_price),
                    '$[*]'
                    COLUMNS (
                        date_key VARCHAR(20) PATH '$'
                    )
                ) jt
                WHERE user_id = ?
                  AND deletes = 0
                  AND jt.date_key >= ?
            ", [$user_id, $today]);
            
            $booked = array_unique(array_column($bookedDates, 'date'));
            
            $today = now();
            $next20days = [];
            
            for ($i = 0; $i < 20; $i++) {
                $next20days[] = $today->copy()->addDays($i)->format('Y-m-d');
            }
            
            $available = array_values(array_diff($next20days, $booked));
            
            sort($available);
    
            return response()->json([
                'status'  => true,
                'data'    => $available,
                'message' => 'Available dates retrieved.',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function get_dates(Request $request)
    {
        try {
            $authUserId = auth()->id();
    
            $now = Carbon::now(config('app.timezone'));
    
            $check_type = DB::table('kyc_details')
                ->where('user_id', $authUserId)
                ->first();
    
            if ($check_type && $check_type->type === 'Owner') {
                $driverIds = json_decode(auth()->user()->drivers_ids, true);
                $driverIds = is_array($driverIds) ? $driverIds : [];
            } else {
                $driverIds = [$authUserId];
            }
    
            $records = DB::table('schedule_dates')
                ->whereIn('user_id', $driverIds)
                ->where('deletes', 0)
                ->get();
    
            $finalData = [];
    
            foreach ($records as $record) {
    
                $datesPrice = json_decode($record->dates_price, true);
    
                if (!is_array($datesPrice)) {
                    continue;
                }
    
                $futureDates = [];
    
                foreach ($datesPrice as $dateTime => $price) {
    
                    // Parse using exact format + timezone
                    $date = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $dateTime,
                        config('app.timezone')
                    );
    
                    if ($date->greaterThan($now)) {
                        $futureDates[$dateTime] = $price;
                    }
                }
    
                if (!empty($futureDates)) {
                    ksort($futureDates);
                    $record->dates_price = $futureDates;
                    $finalData[] = $record;
                }
            }
    
            return response()->json([
                'status'  => true,
                'data'    => $finalData,
                'message' => 'Future scheduled dates retrieved successfully.',
            ], 200);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    public function get_past_dates(Request $request)
    {
        try {
    
            $userId = auth()->id();
            $now    = Carbon::now();
            
            $check_type = DB::table('kyc_details')
                ->where('user_id', $userId)
                ->first();
            
            if ($check_type && $check_type->type == 'Owner') {
    
                $driverIds = json_decode(auth()->user()->drivers_ids, true);
    
                // Safety fallback
                if (!is_array($driverIds) || empty($driverIds)) {
                    $driverIds = [];
                }
    
            } else {
                $driverIds = [$userId];
            }
    
            $records = DB::table('schedule_dates')
                ->whereIn('user_id', $driverIds)
                ->where('deletes', 0)
                ->get();
    
            $finalData = [];
    
            foreach ($records as $record) {
    
                $datesPrice = json_decode($record->dates_price, true);
    
                if (!is_array($datesPrice)) {
                    continue;
                }
    
                // Filter only past dates
                $pastDates = collect($datesPrice)
                    ->filter(function ($price, $date) use ($now) {
                        return Carbon::parse($date)->lt($now);
                    })
                    ->sortKeysDesc()     // latest past dates first
                    // ->take(7)            // only 7 dates
                    ->sortKeys();        // optional: re-sort ascending
    
                if ($pastDates->isNotEmpty()) {
                    $record->dates_price = $pastDates;
                    $finalData[] = $record;
                }
            }
    
            return response()->json([
                'status'  => true,
                'data'    => $finalData,
                'message' => 'Past scheduled dates retrieved',
            ], 200);
    
        } catch (\Throwable $e) {
    
            \Log::error('Get Past Dates Error', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
    
    public function s_fetch_driver(Request $request)
    {
        try {
            $validated = $request->validate([
                'from'    => ['required', 'string'],
                'to'      => ['required', 'string'],
                // 'pickup'  => ['required', 'date_format:Y-m-d'],
                'pickup'  => ['required'],
                'dropoff' => ['required']
                // 'dropoff' => ['required', 'date_format:Y-m-d']
            ]);
    
            $pickup = $validated['pickup'];
    
            $fromWords = preg_split('/[\s,]+/', strtolower($validated['from']), -1, PREG_SPLIT_NO_EMPTY);
            $toWords   = preg_split('/[\s,]+/', strtolower($validated['to']), -1, PREG_SPLIT_NO_EMPTY);
    
            $datesToCheck = [
                $pickup
                // date('Y-m-d', strtotime($pickup . ' +1 day')),
                // date('Y-m-d', strtotime($pickup . ' +2 day'))
            ];
    
            foreach ($datesToCheck as $checkDate) {
    
                $query = DB::table('schedule_dates as sd')
                    ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
                    ->select(
                        'ur.name',
                        'ur.email',
                        'ur.mobile',
                        'sd.from_place',
                        'sd.to_place',
                        'sd.dates_price',
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(sd.dates_price, '$.\"{$checkDate}\"')) AS price"),
                        'ur.vehicle_details'
                    )
                    ->where('sd.deletes', 0);
    
                $query->where(function ($q) use ($fromWords) {
                    foreach ($fromWords as $word) {
                        $q->orWhere('sd.from_place', 'LIKE', "%$word%");
                    }
                });
    
                // Location match: TO
                // $query->where(function ($q) use ($toWords) {
                //     foreach ($toWords as $word) {
                //         $q->orWhere('sd.to_place', 'LIKE', "%$word%");
                //     }
                // });
    
                $query->whereRaw("JSON_CONTAINS(JSON_KEYS(sd.dates_price), JSON_QUOTE(?))", [$checkDate]);
    
                $drivers = $query->orderBy('price', 'ASC')->get();
    
                if ($drivers->count() > 0) {
    
                    return response()->json([
                        'status'        => true,
                        'matched_date'  => $checkDate,
                        'requested_date'=> $pickup,
                        'data'       => $drivers,
                        'message'       => $checkDate == $pickup
                            ? 'Drivers available for your date.'
                            : "No drivers on your chosen date. Showing nearest available date: $checkDate"
                    ], 200);
                }
            }
    
            // If no drivers found in all 3 dates
            return response()->json([
                'status'  => false,
                'drivers' => [],
                'message' => 'No drivers available for selected or nearby dates.',
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
    
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

     public function c_create_job(Request $request)
    {
        try {
    
            $request->validate([
                'job_type' => ['required', 'string', 'max:255'],
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date' => ['nullable', 'date_format:Y-m-d H:i:s'],
                'pass_count' => ['required', 'string', 'max:255'],
                'fare' => ['required', 'numeric'],
                'distance' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'string', 'max:255'],
    
                'add_fare_details' => ['required', 'array'],
                'add_fare_details.bata' => ['required', 'string', 'max:255'],
                'add_fare_details.parking' => ['required', 'string', 'max:255'],
                'add_fare_details.toll' => ['required', 'string', 'max:255'],
    
                'type' => ['required', 'string', 'max:255'],
                'isDriver' => ['nullable', 'string', 'max:255'],
    
                'driver_id' => ['nullable', 'integer'],
                'driver_sc_id' => ['nullable', 'integer'],
    
                'dr_date' => ['nullable', 'date']
            ]);
    
            $user = auth()->user();
            $userId = $user->id;
            $data = $request->all();
    
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour())) 
            {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be at least 1 hour after the current time.'
                ]);
            }
    
            $jobCount = DB::table('open_jobs_customer')->count() + 1;
            $job_no = "GRC-" . str_pad($jobCount, 3, '0', STR_PAD_LEFT);
    
            $data['job_no'] = $job_no;
            $data['global_type'] = 'customer';
            $data['user_id'] = $userId;
    
            // Convert fare details to JSON
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
    
            // Clean date fields
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = !empty($data['dropoff_date']) ? date("Y-m-d H:i:s", strtotime($data['dropoff_date'])) : null;
    
            $data['created_at'] = now();
            $data['updated_at'] = now();
    
    
            if ($data['isDriver'] == 'yes') 
            {
                $get_driver = DB::table('schedule_dates as sd')
                    ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
                    ->where(['sd.id' => $request->driver_sc_id, 'sd.user_id' => $request->driver_id])
                    ->select('ur.id', 'ur.name', 'ur.email', 'ur.mobile', 'sd.*')
                    ->first();
    
                $date = $this->formatReadableDate($request->dr_date ?? $request->pickup_date);
    
                if ($get_driver) 
                {
                    $check_once = DB::table('open_jobs_customer')->where(['from_place' => $request->from_place, 'to_place' => $request->to_place, 'pickup_date' => $request->pickup_date])->first();
                    
                    if($check_once){
                        return response()->json([
                            'status' => false,
                            'data'   => [],
                            'message'=> 'You have already selected a driver for this trip. Please complete the pending confirmation before creating a new request.'
                        ]);
                    }
                    
                    unset($data['bataRadio'], $data['tollRadio'], $data['parkingRadio'], $data['type'], $data['isDriver'], $data['driver_id'], $data['dr_date'], $data['driver_sc_id']);
    
                    $create_job = DB::table('open_jobs_customer')->insertGetId($data);
    
                    if ($create_job) 
                    {

$message = "Hello *{$get_driver->name}* 👋,

A client has booked you for 📅 *{$date}*.

Kindly check your GoRide app and *accept the job* to confirm your availability.

https://goride.net.in/open-driver-app

– GoRide Team 🚗✨
";
    
                        Controller::sendNotification([
                            'mobile'           => $get_driver->mobile,
                            'templateName'     => 'national_draw_verification',
                            'language'         => 'ta',
                            'templateBodyParam'=> [],
                            'messages'         => $message,
                            'resend'           => false
                        ]);
    
                        $fcmToken = $this->getFcm([$get_driver->id]);
    
                        if ($fcmToken) {
                            $accessToken = $this->getAccessToken();
    
                            if ($accessToken) {
                                foreach ($fcmToken as $token) {
    
                                    try {
                                        $this->sendFCM(
                                            $accessToken,
                                            $token,
                                            '👤 Customer Selected You',
                                            'A customer has chosen you for their trip. Please wait for their final confirmation.',
                                            [
                                                'caller' => $user->name,
                                                'type'   => 'customer_selection_pending',
                                                'url'    => env('APP_URL') . 'jobs',
                                                'date'   => $date,
                                            ]
                                        );
    
                                    } catch (\Throwable $e) {
                                        Log::error("FCM send error (token: $token)", [
                                            'error' => $e->getMessage(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
    
                return response()->json([
                    'status' => true,
                    'data'   => $job_no,
                    'message'=> 'Job created and driver notified.'
                ]);
            }
    
            if ($data['isDriver'] === 'no') 
            {
                unset($data['bataRadio'], $data['tollRadio'], $data['parkingRadio'], $data['type'], $data['isDriver'], $data['driver_id'], $data['dr_date'], $data['driver_sc_id']);
                
                $create_job = DB::table('open_jobs_customer')->insertGetId($data);
    
                if ($create_job) 
                {
                    $parts = array_map('trim', explode(',', $request->from_place));
                    $place = count($parts) >= 2 ? $parts[count($parts)-2] : $parts[0];
    
                    $fcmToken = $this->getFcm(null, $place);
    
                    if ($fcmToken) {
                        $accessToken = $this->getAccessToken();
    
                        if ($accessToken) {
                            foreach ($fcmToken as $token) {
                                $this->sendFCM(
                                    $accessToken,
                                    $token,
                                    'New Job Arrived!',
                                    'A new job is available from ' . $request->from_place . '. Open the app to place your bid.',
                                    [
                                        'caller' => $user->name,
                                        'type'   => 'new_job_notification',
                                        'id'     => $create_job,
                                        'action' => 'agree_popup',
                                        'url'    => env('APP_URL') . 'jobs',
                                        'pickup' => $data['pickup_date'],
                                    ]
                                );
                            }
                        }
                    }
    
                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'message' => 'Job created successfully.'
                    ]);
                }
    
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create job.'
                ], 200);
            }
    
    
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
    
    public function confirmAvailability(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_id' => ['required','integer'],
                'status' => ['required','string'],
                'date'   => ['required','date']
            ]);
    
            $user = auth()->user();
            $date = $validated['date'];
    
            $job = DB::table('cus_job_temp')
                ->where('id', $validated['job_id'])
                ->where('global_type', 'schedule')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'drivers' => [],
                    'message' => 'Job not found or already booked.',
                ], 200);
            }
    
            /* -------------------------------------------------
               Check if driver already has accepted job
            --------------------------------------------------*/
    
            $job_sh = DB::table('cus_job_temp')
                ->where('id', $validated['job_id'])
                ->where('global_type', '!=', 'schedule')
                ->where('job_status', 'accept')
                ->whereDate('pickup_date', $date)
                ->whereRaw("JSON_EXTRACT(bids_details, '$.\"{$user->id}\".status') = 'accept'")
                ->first();
    
            if ($job_sh) {
                return response()->json([
                    'status'  => false,
                    'drivers' => [],
                    'message' => 'You have another job on this date.',
                ], 200);
            }
    
            if (!$job->sch_status) {
                return response()->json([
                    'status'  => false,
                    'drivers' => [],
                    'message' => 'Driver status not initialized.',
                ], 200);
            }
    
            /* -------------------------------------------------
               Decode schedule status once
            --------------------------------------------------*/
    
            $statusData = json_decode($job->sch_status, true) ?? [];
    
            if (
                !isset($statusData[$date]) ||
                !isset($statusData[$date][$user->id])
            ) {
                return response()->json([
                    'status'  => false,
                    'drivers' => [],
                    'message' => 'Not eligible to accept the job.',
                ], 200);
            }
    
            $sch_id = $statusData[$date][$user->id]['sch_id'] ?? null;
    
            if (!$sch_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Schedule not found.'
                ], 200);
            }
    
            $fare = 0;
    
            $get_fare = DB::table('schedule_dates')
                ->where('user_id', $user->id)
                ->where('id', $sch_id)
                ->select('dates_price')
                ->first();
    
            if ($get_fare) {
                $base_fare = 0;
                $dates_data = json_decode($get_fare->dates_price, true);
            
                $searchDate = date('Y-m-d', strtotime($date));
            
                if (is_array($dates_data) && isset($dates_data[$searchDate])) {
                    
                    $drivers = $dates_data[$searchDate];
            
                    // $driverData = reset($drivers); 
                    
                    $base_fare = (int) $drivers;
                }
            
                if ($base_fare > 0) {
                    $fare = $base_fare + ($job->toll_fare ?? 0);
                }
            }

            $now = now()->toISOString();
    
            $statusData[$date][$user->id]['status'] = $validated['status'];
            $statusData[$date][$user->id]['updated_at'] = $now;
            $statusData[$date][$user->id]['amount'] = $fare;
    
            /* -------------------------------------------------
               Update Firebase
            --------------------------------------------------*/
    
            $accessToken = $this->getAccessToken2();
    
            if ($accessToken) {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount2['project_id'],
                    $accessToken
                );
    
                $firebase->updateScheduleStatus($job->job_no, $statusData);
            }
    
            /* -------------------------------------------------
               Update MySQL
            --------------------------------------------------*/
    
            DB::table('cus_job_temp')
                ->where('id', $validated['job_id'])
                ->update([
                    'sch_status' => json_encode($statusData)
                ]);
    
            /* -------------------------------------------------
               Response Message
            --------------------------------------------------*/
    
            $message = match ($validated['status']) {
                'available' => 'Availability confirmed. Please stand by for customer confirmation.',
                'busy'      => 'Status set to busy. We will notify you when the next job opportunity is available.',
                default     => 'Availability updated successfully.'
            };
    
            return response()->json([
                'status'  => true,
                'message' => $message,
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    
    public function otpVerify(Request $request)
    {
        try {
    
            $data = $request->validate([
                'job_id' => 'required',
                'otp'    => 'required|digits:6',
                'lat'    => 'nullable',
                'lng'    => 'nullable'
            ]);
            
            $user = auth()->user();
    
            // Get job + latest OTP in minimal queries
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->whereIn('job_status', ['accept', 'started'])
                ->first();
    
            if (!$job) {
                return response()->json(['status' => false, 'message' => 'Job not found'], 200);
            }
    
            $otp = DB::table('job_start_otps')
                ->where('job_id', $data['job_id'])
                ->whereNull('verified_at')
                ->latest('id')
                ->first();
    
            if (!$otp) {
                return response()->json(['status' => false, 'message' => 'OTP not found'], 200);
            }
    
            // Expired
            // if (now()->gt($otp->expires_at)) {
            //     DB::table('job_start_otps')->where('id', $otp->id)->update(['verified_at' => now()]);
            //     return response()->json(['status' => false, 'message' => 'OTP expired'], 200);
            // }
    
            // Max attempts
            if ($otp->attempts >= $otp->max_attempts) {
                return response()->json(['status' => false, 'message' => 'Max attempts reached'], 200);
            }
    
            // Invalid OTP
            if ($data['otp'] != $otp->otp) {
                DB::table('job_start_otps')->where('id', $otp->id)->increment('attempts');
                return response()->json(['status' => false, 'message' => 'Invalid OTP'], 200);
            }
    
            // ✅ Success (atomic update)
            DB::transaction(function () use ($otp, $data) {
                DB::table('job_start_otps')->where('id', $otp->id)->update([
                    'verified_at' => now(),
                    'verified_by' => $user->id,
                    's_lat'      => $request->lat??null,
                    's_lng'      => $request->lng??null,
                    'updated_at'  => now()
                ]);
    
                DB::table('cus_job_temp')->where('id', $data['job_id'])->update([
                    'otpVerify'       => 1,
                    'job_status' => 'started',
                    'updated_at'       => now()
                ]);
            });
    
            return response()->json([
                'status'  => true,
                'message' => 'OTP verified. Your ride has started.'
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            \Log::error('OTP VERIFY ERROR: '.$e->getMessage());
    
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
            ], 500);
        }
    }
    
    public function completeRide(Request $request)
    {
        try {
    
            $data = $request->validate([
                'job_id' => 'required|integer',
                'lat'    => 'nullable',
                'lng'    => 'nullable'
            ]);
    
            $user = auth()->user();
            $now  = now();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->where('job_status', 'started')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found or not started.',
                ], 200);
            }
    
            $otp = DB::table('job_start_otps')
                ->where('job_id', $data['job_id'])
                ->whereNotNull('verified_at')
                ->latest('id')
                ->first();
    
            if (!$otp) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job cannot be completed without OTP verification.',
                ], 200);
            }
    
            DB::transaction(function () use ($otp, $data, $now, $request) {
    
                if (!empty($request->lat) && !empty($request->lng)) {
                    DB::table('job_start_otps')
                        ->where('id', $otp->id)
                        ->update([
                            'c_lat'      => $request->lat,
                            'c_lng'      => $request->lng,
                            'updated_at' => $now
                        ]);
                }
    
                DB::table('cus_job_temp')
                    ->where('id', $data['job_id'])
                    ->update([
                        'job_status'   => 'completed',
                        'updated_at'   => $now
                    ]);
                    
                
            });
            
            $driverFcm = $user->fcm_token;
            
            // Decide customer table
            $table = in_array($job->global_type, ['customer', 'schedule']) 
                ? 'customer_register' 
                : 'user_register';
            
            // Get customer FCM correctly
            $customerFcm = DB::table($table)
                ->where('id', $job->user_id)
                ->value('fcm_token');
            
            $accessToken = $this->getAccessToken();
            
            $this->sendFCM(
                $accessToken,
                $driverFcm,
                'Ride Completed 🎉',
                'You’ve completed the ride successfully. Keep going!',
                [
                    'type'   => 'ride_completed',
                    'job_id' => $job->id,
                    'action' => 'driver_dashboard',
                ]
            );
            
            if ($customerFcm) {
                $this->sendFCM(
                    $accessToken,
                    $customerFcm,
                    'Trip Completed 🚖',
                    'Hope you had a great ride! Thank you for riding with GoRide.',
                    [
                        'type'   => 'ride_completed',
                        'job_id' => $job->id,
                        'action' => 'ride_summary',
                    ]
                );
            }
    
            return response()->json([
                'status'  => true,
                'message' => 'Ride completed! Great job, We’re ready for your next trip!',
                'data'    => null
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            \Log::error('Complete Ride ERROR: '.$e->getMessage());
    
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
            ], 500);
        }
    }
    
    private function getGeoHashPrefix(
        $lat,
        $lng,
        $precision = 5
    )
    {
        $geoHash = new GeohashService();
    
        return $geoHash->encode(
            $lat,
            $lng,
            $precision
        );
    }
    
    public function updateRouteEta(Request $request)
    {
        try {
    
            $request->validate([
                'job_id' => 'required|integer'
            ]);
    
            $driver = auth()->user();
    
            /*
            |--------------------------------------------------------------------------
            | Active Ride
            |--------------------------------------------------------------------------
            */
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $driver->id)
                ->where('global_type', 'dr_carpool')
                ->where('job_status', 'started')
                ->first();
    
            if (!$job) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Active ride not found.'
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | Driver Latest Location
            |--------------------------------------------------------------------------
            */
    
            $driverLocation = $this->getDriverLocation(
                $driver->id
            );
    
            if (!$driverLocation) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Driver location unavailable.'
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | Decode JSON
            |--------------------------------------------------------------------------
            */
    
            $route = json_decode(
                $job->route_json,
                true
            );
    
            $stops = json_decode(
                $job->stops_json,
                true
            );
    
            if (
                empty($route) ||
                empty($stops)
            ) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Route data not found.'
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | Current Progress
            |--------------------------------------------------------------------------
            */
    
            $progress = $this->getCurrentRouteProgress(
    
                $route['route_id'],
    
                $driverLocation['lat'],
    
                $driverLocation['lng']
            );
    
            if (!$progress) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to determine current route progress.'
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | Current Stop
            |--------------------------------------------------------------------------
            */
    
            $currentIndex = $this->getCurrentStop(
    
                $stops,
    
                $progress['route_distance']
            );
    
            if ($currentIndex === null) {
    
                return response()->json([
                    'status' => true,
                    'message' => 'Journey completed.',
                    'data' => [
                        'stops' => $stops
                    ]
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | ETA Driver -> Current Stop
            |--------------------------------------------------------------------------
            */
    
            $eta = $this->getEtaToCurrentStop(
    
                $driverLocation['lat'],
    
                $driverLocation['lng'],
    
                $stops[$currentIndex]['latitude'],
    
                $stops[$currentIndex]['longitude']
            );
    
            if (!$eta) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to calculate ETA.'
                ], 200);
            }
    
            /*
            |--------------------------------------------------------------------------
            | Update Remaining Stops
            |--------------------------------------------------------------------------
            */
    
            $stops = $this->updateStopsEta(
    
                $stops,
    
                $currentIndex,
    
                $eta['duration_minutes']
            );
    
            /*
            |--------------------------------------------------------------------------
            | Save
            |--------------------------------------------------------------------------
            */
    
            DB::table('cus_job_temp')
    
                ->where('id', $job->id)
    
                ->update([
    
                    'stops_json' => json_encode(
                        $stops,
                        JSON_UNESCAPED_UNICODE
                    ),
    
                    'updated_at' => now()
    
                ]);
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'ETA updated successfully.',
    
                'data' => [
    
                    'current_stop' => $stops[$currentIndex],
    
                    'driver_progress' => $progress,
    
                    'stops' => $stops
    
                ]
    
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Throwable $e) {
    
            Log::error('Update Route ETA Error', [
    
                'job_id' => $request->job_id ?? null,
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
    
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Server error.'
            ], 500);
        }
    }
    
    private function getDriverLocation(int $driverId): ?array
    {
        try {
    
            $redis = app('redis');
    
            $key = "driver:location:{$driverId}";
    
            if (!$redis->exists($key)) {
                return null;
            }
    
            $location = $redis->hGetAll($key);
    
            if (
                empty($location) ||
                !isset($location['lat']) ||
                !isset($location['lng'])
            ) {
                return null;
            }
    
            return [
    
                'lat' => (float) $location['lat'],
    
                'lng' => (float) $location['lng'],
    
                'heading' => isset($location['heading'])
                    ? (float) $location['heading']
                    : 0,
    
                'speed' => isset($location['speed'])
                    ? (float) $location['speed']
                    : 0,
    
                'updated_at' => $location['updated_at']
                    ?? null
    
            ];
    
        } catch (\Throwable $e) {
    
            Log::error(
                'Get Driver Location Error',
                [
                    'driver_id' => $driverId,
                    'message' => $e->getMessage()
                ]
            );
    
            return null;
        }
    }
    
    private function getCurrentRouteProgress(
        int $routeId,
        float $lat,
        float $lng
    ): ?array
    {
        try {
    
            /*
            |--------------------------------------------------------------------------
            | Driver Geohash
            |--------------------------------------------------------------------------
            */
    
            $hash = substr(
                $this->getGeoHashPrefix(
                    $lat,
                    $lng,
                    6
                ),
                0,
                5
            );
    
            /*
            |--------------------------------------------------------------------------
            | Candidate Route Points
            |--------------------------------------------------------------------------
            */
    
            $points = DB::table('route_points')
    
                ->where('route_id', $routeId)
    
                ->where('geohash', 'LIKE', $hash.'%')
    
                ->get();
    
            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */
    
            if ($points->isEmpty()) {
    
                $points = DB::table('route_points')
    
                    ->where('route_id', $routeId)
    
                    ->get();
            }
    
            if ($points->isEmpty()) {
                return null;
            }
    
            /*
            |--------------------------------------------------------------------------
            | Find Nearest Route Point
            |--------------------------------------------------------------------------
            */
    
            $nearest = null;
    
            $nearestDistance = PHP_FLOAT_MAX;
    
            foreach ($points as $point) {
    
                $distance = $this->haversineDistance(
    
                    $lat,
                    $lng,
    
                    $point->latitude,
                    $point->longitude
                );
    
                if ($distance < $nearestDistance) {
    
                    $nearestDistance = $distance;
    
                    $nearest = $point;
                }
            }
    
            if (!$nearest) {
                return null;
            }
    
            return [
    
                'point_order' => $nearest->point_order,
    
                'route_distance' => $nearest->route_distance,
    
                'distance_to_route' => round(
                    $nearestDistance,
                    2
                )
    
            ];
    
        } catch (\Throwable $e) {
    
            Log::error(
                'Current Route Progress Error',
                [
                    'route_id' => $routeId,
                    'message' => $e->getMessage()
                ]
            );
    
            return null;
        }
    }
    
    private function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float
    {
        $earthRadius = 6371000;
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) *
            sin($dLon / 2);
    
        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );
    
        return $earthRadius * $c;
    }
    
    private function getCurrentStop(
        array &$stops,
        float $currentRouteDistance
    ): ?int
    {
        try {
    
            $currentIndex = null;
    
            /*
            |--------------------------------------------------------------------------
            | Completion Buffer
            |--------------------------------------------------------------------------
            |
            | Driver must travel 200 meters after a stop
            | before it becomes completed.
            |
            */
    
            $completionBuffer = 200;
    
            foreach ($stops as $index => &$stop) {
    
                /*
                |--------------------------------------------------------------------------
                | Stop already completed
                |--------------------------------------------------------------------------
                */
    
                if (
                    ($stop['status'] ?? 'pending') == 'completed'
                ) {
                    $stop['progress'] = 'completed';
                    continue;
                }
    
                /*
                |--------------------------------------------------------------------------
                | Driver already crossed this stop
                |--------------------------------------------------------------------------
                */
    
                if (
                    $currentRouteDistance >=
                    ($stop['route_distance'] + $completionBuffer)
                ) {
    
                    $stop['status'] = 'completed';
    
                    $stop['progress'] = 'completed';
    
                    if (empty($stop['actual_arrival_time'])) {
    
                        $stop['actual_arrival_time']
                            = now()->format('Y-m-d H:i:s');
                    }
    
                    continue;
                }
    
                /*
                |--------------------------------------------------------------------------
                | First Pending Stop
                |--------------------------------------------------------------------------
                */
    
                if ($currentIndex === null) {
    
                    $currentIndex = $index;
    
                    $stop['progress'] = 'current';
    
                    continue;
                }
    
                /*
                |--------------------------------------------------------------------------
                | Remaining Stops
                |--------------------------------------------------------------------------
                */
    
                $stop['progress'] = 'upcoming';
            }
    
            unset($stop);
    
            return $currentIndex;
    
        } catch (\Throwable $e) {
    
            Log::error(
                'Current Stop Error',
                [
                    'message' => $e->getMessage()
                ]
            );
    
            return null;
        }
    }
    
    private function getEtaToCurrentStop(
        float $driverLat,
        float $driverLng,
        float $stopLat,
        float $stopLng
    ): ?array
    {
        try {
    
            $url = env('OSRM_ROUTE_URL')
                . "/route/v1/driving/"
                . $driverLng . "," . $driverLat
                . ";"
                . $stopLng . "," . $stopLat;
    
            $response = Http::timeout(15)
                ->get($url, [
                    'overview' => 'false',
                    'steps' => 'false',
                    'annotations' => 'false'
                ]);
    
            if (!$response->successful()) {
    
                Log::error(
                    'OSRM ETA Error',
                    [
                        'response' => $response->body()
                    ]
                );
    
                return null;
            }
    
            $data = $response->json();
    
            if (
                empty($data['routes']) ||
                empty($data['routes'][0])
            ) {
                return null;
            }
    
            $route = $data['routes'][0];
            $arrived = $route['distance'] <= 50;
    
            return [
            
                'distance_meters' => (int)$route['distance'],
            
                'distance_km' => round(
                    $route['distance'] / 1000,
                    2
                ),
            
                'duration_seconds' => (int)$route['duration'],
            
                'duration_minutes' => $arrived
                    ? 0
                    : max(
                        1,
                        round($route['duration'] / 60)
                    ),
            
                'arrived' => $arrived
            ];
    
        } catch (\Throwable $e) {
    
            Log::error(
                'Get ETA Error',
                [
                    'message' => $e->getMessage()
                ]
            );
    
            return null;
        }
    }

}