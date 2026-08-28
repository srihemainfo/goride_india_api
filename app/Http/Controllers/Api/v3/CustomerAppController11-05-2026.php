<?php

namespace App\Http\Controllers\Api\v3;

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
use Illuminate\Support\Facades\Cache;


class CustomerAppController extends Controller
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
    
    public function whNoreplyWebhook(Request $request)
    {
        $verifyToken = 'goride-wh-noreply-hook0987)(*&';
        
        $rawBody = $request->getContent(); // full JSON body

        DB::table('error_log')->insert([
            'ip'        => $request->ip(),
            'type'      => $request->method(), // GET or POST
            'userid'    => 0,
            'email'     => '',
            'mobile'    => '',
            'message'   => 'WHATSAPP WEBHOOK HIT',
            'request'   => $rawBody ?: json_encode($request->query()), // GET has no body
            'path'      => $request->fullUrl(),
            'file_name' => 'whatsapp_webhook',
            'line_no'   => 0,
            'createdon' => now(),
        ]);
    
        if ($request->isMethod('get')) {
    
            $mode      = $request->query('hub_mode') ?? $request->query('hub.mode');
            $token     = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
            $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');
            
            // return $token;
    
            if ($token == $verifyToken) {
                
                return response($challenge, 200)
                        ->header('Content-Type', 'text/plain');
            }
    
            return response('Verification token mismatch', 403);
        }
    
        if ($request->isMethod('post')) {
    
            $payload = $request->all();
    
            \Log::info('WhatsApp Webhook', $payload);
    
            $this->sendAutoReply($payload);
    
            return response()->json(['status' => 'received'], 200);
        }
    }
    
    private function sendAutoReply($payload)
    {
        try {
    
            $phone_number_id = env('FB_WHATSAPP_PHONE_NUMBER_ID');
            $ver = env('FB_WHATSAPP_VERSION');
            $token = env('FB_WHATSAPP_TOKEN');
            
            $entry = $payload['entry'][0]['changes'][0]['value'] ?? null;
    
            if (!$entry || !isset($entry['messages'][0]['from'])) {
                return;
            }
    
            $from = $entry['messages'][0]['from'];
    
            Http::withToken($token)->post(
                "https://graph.facebook.com/{$ver}/{$phone_number_id}/messages",
                [
                    "messaging_product" => "whatsapp",
                    "to" => $from,
                    "type" => "text",
                    "text" => [
                        "body" => "Thanks! Your cab is being book 🚕"
                    ]
                ]
            );
    
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
        }
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
    
    
    public function getFcmCopy($id = null, $loc = null, $us_id = null)
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
            
            $id = $us_id??auth()->user()->id;
            
            $get_tokens = DB::table('user_register')
                ->whereNotNull('fcm_token')
                ->where('deletes', '0')
                ->where('notify', 1)
                // ->orWhereNotNull('browser_fcm_token')
                ->where('id', '!=', $id)
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
    
    public function getFcm($id = null, $loc = null, $us_id = null, $cab_seat = null)
    {
        $query = DB::table('user_register')
            ->where('deletes', '0')
            ->where('doc_verify', 1)
            ->where('vehicle_verify', 2)
            ->whereNotNull('fcm_token')
            ->orderByDesc('profile_percentage');

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
    
    public function getNearbyFcm($fromLat, $fromLng, $limit = 50, $cab_seat = null)
    {
        $collected = collect();
    
        $radiusSteps = [env('NOTIFY_FIRST'), env('NOTIFY_SECOND'), env('NOTIFY_THIRD')];
        
        // $blockedDrivers = DB::table('driver_notification_logs')
        //     ->select('user_id')
        //     ->where('created_at', '>=', now()->subHours(2))
        //     ->groupBy('user_id')
        //     ->havingRaw('COUNT(*) >= 3')
        //     ->pluck('user_id')
        //     ->toArray();
            
        if($cab_seat && $cab_seat == 'mini'){
            $cab_seat = 5;
        }
    
        foreach ($radiusSteps as $radius) {
    
            $remaining = $limit - $collected->count();
            if ($remaining <= 0) break;
    
            $latRange = $radius / 111;
            $lngRange = $radius / (111 * cos(deg2rad($fromLat)));
    
            $drivers = DB::table('user_register as ur')
                ->join('drivers_current_location as dcl', 'ur.id', '=', 'dcl.user_id')
    
                ->where('ur.deletes', '0')
                ->where('ur.doc_verify', 1)
                ->where('ur.vehicle_verify', 2)
                ->where('ur.notify', 1)
                ->whereNotNull('ur.fcm_token')
    
                // ->whereNotIn('ur.id', $blockedDrivers)
                ->whereNotIn('ur.id', $collected->pluck('id'))
    
                ->whereNotNull('dcl.lat')
                ->whereNotNull('dcl.lng')
    
                ->whereBetween('dcl.lat', [$fromLat - $latRange, $fromLat + $latRange])
                ->whereBetween('dcl.lng', [$fromLng - $lngRange, $fromLng + $lngRange])
                
                ->when($cab_seat != null, function ($q) use ($cab_seat) {
                    $q->whereRaw("
                        CAST(
                            COALESCE(
                                JSON_UNQUOTE(
                                    JSON_EXTRACT(
                                        ur.vehicle_details,
                                        '$.rc_details.response.vehicle_details.seat_capacity'
                                    )
                                ),
                                '0'
                            ) AS UNSIGNED
                        ) >= ?
                    ", [(int) $cab_seat]);
                })
    
                ->select(
                    'ur.id',
                    'ur.fcm_token',
                    DB::raw("
                        (6371 * acos(
                            cos(radians($fromLat)) *
                            cos(radians(dcl.lat)) *
                            cos(radians(dcl.lng) - radians($fromLng)) +
                            sin(radians($fromLat)) *
                            sin(radians(dcl.lat))
                        )) AS distance
                    ")
                )
    
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'asc')
                ->limit($remaining)
                ->get();
    
            $collected = $collected->merge($drivers);
        }
    
        // if ($collected->count() < $limit) {
    
        //     $remaining = $limit - $collected->count();
    
        //     $extra = DB::table('user_register')
        //         ->where('deletes', '0')
        //         ->where('doc_verify', 1)
        //         ->where('vehicle_verify', 2)
        //         ->whereNotNull('fcm_token')
        //         ->whereNotIn('ur.id', $blockedDrivers)
        //         ->whereNotIn('id', $collected->pluck('id'))
    
        //         ->when($cab_seat !== null, function ($q) use ($cab_seat) {
        //             $q->whereRaw("
        //                 CAST(
        //                     COALESCE(
        //                         JSON_UNQUOTE(JSON_EXTRACT(vehicle_details, '$.type')),
        //                         '0'
        //                     ) AS UNSIGNED
        //                 ) >= ?
        //             ", [(int)$cab_seat]);
        //         })
        //         ->inRandomOrder()
        //         ->limit($remaining)
        //         ->get(['id', 'fcm_token']);
    
        //     $collected = $collected->merge($extra);
        // }
    
        return $collected
            // ->filter()
            // ->unique()
            ->values();
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
    
    public function uploadImage(Request $request)
    {
        // 1️⃣ Validate
        $request->validate([
            'img_type' => 'required',
            'auth_key' => 'required|string',
            'image'    => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'name'     => 'required|string|max:50'
        ]);
    
        // 2️⃣ Auth check
        if ($request->auth_key !== env('EXPECTED_API_TOKEN')) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized'
            ], 401);
        }
    
        DB::beginTransaction();
    
        try {
    
            // 3️⃣ Prepare file
            $file = $request->file('image');
    
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($request->name));
    
            $fileName = uniqid($safeName . '_') . '.' . $file->getClientOriginalExtension();
    
            // 4️⃣ Upload to S3
            $path = $file->storeAs(
                'cus_app/images',
                $fileName,
                's3'
            );
    
            Storage::disk('s3')->setVisibility($path, 'public');
    
            $url = Storage::disk('s3')->url($path);
    
            $imageId = DB::table('s3_images')->insertGetId([
                'img_type'       => $request->img_type,
                'name'       => $safeName,
                's3_path'    => $path,
                's3_url'     => $url,
                'mime_type'  => $file->getClientMimeType(),
                'size'       => $file->getSize(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            DB::commit();
    
            // 6️⃣ Response
            return response()->json([
                'status'  => true,
                'message' => 'Image uploaded successfully',
                'data'    => [
                    'id'   => $imageId,
                    'path' => $path,
                    'url'  => $url,
                ]
            ], 200);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Upload failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    function formatReadableDate($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->format('d M Y, h:i A');
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
    
    private function createFirebaseJob(string $jobNo, array $data)
    {
        $accessToken = $this->getAccessToken();
    
        if (!$accessToken) {
            throw new \Exception('Firebase access token failed');
        }
    
        $fbCol = env('FIREBASE_COLLECTION', 'jobs');
    
        $url = "https://firestore.googleapis.com/v1/projects/{$this->serviceAccount['project_id']}/databases/(default)/documents/{$fbCol}/{$jobNo}";
    
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
    
                'pass_count' => [
                    'stringValue' => (string) $data['pass_count']
                ],
    
    
                'distance' => [
                    'integerValue' => (string) (int) $data['distance']
                ],
    
                'duration' => [
                    'stringValue' => (string) ($data['duration'] ?? '')
                ],
    
                'global_type' => [
                    'stringValue' => 'customer'
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
                'pick_lat' => [
                    'stringValue' => (string) ($data['pick_lat']??'')
                ],
                'day' => [
                    'stringValue' => (string) ($data['day']??'')
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
                            'bata' => ['stringValue' => 'Included'],
                            'toll' => ['stringValue' => 'Included'],
                            'parking' => ['stringValue' => 'Included'],
                        ]
                    ]
                ],
    
                'user_id' => [
                    'integerValue' => (string) (int) $data['user_id']
                ],
                
                'base_fare' => [
                    
                    'stringValue' => (string) $data['base_fare']
                ],
                
                'toll_fare' => [
                    
                    'stringValue' => (string) $data['toll_fare']
                ],
                'com' => [
                    
                    'stringValue' => (string) $data['com']
                ],
                'tax' => [
                    
                    'stringValue' => (string) $data['tax']
                ],
                'discount' => [
                    
                    'stringValue' => (string) $data['discount']
                ],
                'isDiscount' => [
                    
                    'stringValue' => (string) $data['isDiscount']
                ],
                'fare' => [
                    'stringValue' => (string) $data['fare']
                ],
                
                'pick_address' => [
                    
                    'stringValue' => (string) $data['pick_address']
                ],
                'drop_address' => [
                    
                    'stringValue' => (string) $data['drop_address']
                ],
                'user_details' => [
                    
                    'stringValue' => (string) $data['user_details']
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
        
        if (!empty($data['dropoff_date'])) {
            $fields['dropoff_date'] = [
                'stringValue' => date('Y-m-d', strtotime($data['dropoff_date']))
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
    
    private function createFirebaseJob2(string $jobNo, array $data)
    {
        $accessToken = $this->getAccessToken2();
    
        if (!$accessToken) {
            throw new \Exception('Firebase access token failed');
        }
    
        $fbCol = env('FIREBASE_COLLECTION', 'jobs');
    
        $url = "https://firestore.googleapis.com/v1/projects/{$this->serviceAccount2['project_id']}/databases/(default)/documents/{$fbCol}/{$jobNo}";
    
        $fields = [
    
                'id' => ['integerValue' => (string) $data['id']],
                'job_no' => ['stringValue' => (string) $jobNo],
                
                'global_type' => [
                    'stringValue' => 'schedule'
                ],
    
                'job_status' => [
                    'stringValue' => 'created'
                ],
                
                'preview_hash' => [
                    'stringValue' => (string) $data['preview_hash']
                ],
                
                'user_id' => [
                    'integerValue' => (string) (int) $data['user_id']
                ],
                
                'poster_name' => [
                    'stringValue' => (string) $data['poster_name']
                ],
    
                'created_at' => [
                    'timestampValue' => now()->toIso8601String()
                ],
                
                'updated_at' => [
                    'timestampValue' => now()->toIso8601String()
                ]
                
        ];
        
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
    
    function extractDistrict($addressComponents)
    {
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
    
    private function normalizeSearch($text)
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $text));
    }
    
    public function addressAutocomplete(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3|max:20',
            'lat'   => 'required|numeric',
            'lng'   => 'required|numeric',
        ]);
    
        if (!$this->verifyRecaptcha($request->recaptcha_token, 'address_autocomplete')) {
            return response()->json(['status' => false]);
        }
    
        $limit = 8;
        $normalized = $this->normalizeSearch($request->input('query'));
    
        /* ------------------------------------
           1️⃣ FAST DB PREFIX SEARCH
        ------------------------------------ */
        $dbResults = DB::table('location_address')
            ->where('search_key', 'LIKE', $normalized . '%')
            ->where('state', 'Tamil Nadu')
            ->limit($limit)
            ->get(['address','latitude','longitude'])
            ->map(fn($r) => [
                'address' => $r->address,
                'lat' => $r->latitude,
                'lng' => $r->longitude,
            ])
            ->toArray();
    
        if (count($dbResults) >= $limit) {
            return response()->json([
                'status' => true,
                'data' => $dbResults
            ]);
        }
    
        /* ------------------------------------
           2️⃣ GOOGLE FALLBACK (LIMITED)
        ------------------------------------ */
        $google = Http::timeout(3)->get(
            'https://maps.googleapis.com/maps/api/place/autocomplete/json',
            [
                'input'      => $request->input('query'),
                'key'        => env('GOOGLE_KEY'),
                'location'   => "{$request->lat},{$request->lng}",
                'radius'     => 30000,
                'components' => 'country:in',
                // 'strictbounds' => true,
            ]
        )->json();
        
        // return $google;
    
        if (empty($google['predictions'])) {
            return response()->json([
                'status' => true,
                'data' => $dbResults
            ]);
        }
    
        $new = [];
    
        foreach ($google['predictions'] as $p) {
    
            if (count($new) + count($dbResults) >= $limit) break;
    
            $geo = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/geocode/json',
                [
                    'place_id' => $p['place_id'],
                    'key'      => env('GOOGLE_KEY'),
                ]
            )->json();
    
            // dd($geo);
            if (empty($geo['results'][0])) continue;
    
            $res = $geo['results'][0];
            
            /* ---- TN HARD FILTER ---- */
            $isTN = false;
            foreach ($res['address_components'] as $c) {
                if (
                    in_array('administrative_area_level_1', $c['types']) &&
                    strtolower($c['long_name']) == 'tamil nadu'
                ) {
                    $isTN = true;
                    break;
                }
            }
            if (!$isTN) continue;
    
            $lat = $res['geometry']['location']['lat'];
            $lng = $res['geometry']['location']['lng'];
            $address = $res['formatted_address'];
    
            /* ---- INSERT IF NEW ---- */
            DB::table('location_address')->updateOrInsert(
                ['latitude' => $lat, 'longitude' => $lng],
                [
                    'address' => $address,
                    'state' => 'Tamil Nadu',
                    'country' => 'India',
                    'search_key' => $this->normalizeSearch($address),
                    'source' => 'google',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
    
            $new[] = [
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
            ];
        }
    
        return response()->json([
            'status' => true,
            'data' => array_slice(array_merge($dbResults, $new), 0, $limit)
        ]);
    }
    
    public function adminaddressAutocomplete(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3|max:20',
            'lat'   => 'required|numeric',
            'lng'   => 'required|numeric',
        ]);
    
        $limit = 8;
        $normalized = $this->normalizeSearch($request->input('query'));
    
        /* ------------------------------------
           1️⃣ FAST DB PREFIX SEARCH
        ------------------------------------ */
        $dbResults = DB::table('location_address')
            ->where('search_key', 'LIKE', $normalized . '%')
            ->where('state', 'Tamil Nadu')
            ->limit($limit)
            ->get(['address','latitude','longitude'])
            ->map(fn($r) => [
                'address' => $r->address,
                'lat' => $r->latitude,
                'lng' => $r->longitude,
            ])
            ->toArray();
    
        if (count($dbResults) >= $limit) {
            return response()->json([
                'status' => true,
                'data' => $dbResults
            ]);
        }
    
        /* ------------------------------------
           2️⃣ GOOGLE FALLBACK (LIMITED)
        ------------------------------------ */
        $google = Http::timeout(3)->get(
            'https://maps.googleapis.com/maps/api/place/autocomplete/json',
            [
                'input'      => $request->input('query'),
                'key'        => env('GOOGLE_KEY'),
                'location'   => "{$request->lat},{$request->lng}",
                'radius'     => 30000,
                'components' => 'country:in',
                // 'strictbounds' => true,
            ]
        )->json();
        
        // return $google;
    
        if (empty($google['predictions'])) {
            return response()->json([
                'status' => true,
                'data' => $dbResults
            ]);
        }
    
        $new = [];
    
        foreach ($google['predictions'] as $p) {
    
            if (count($new) + count($dbResults) >= $limit) break;
    
            $geo = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/geocode/json',
                [
                    'place_id' => $p['place_id'],
                    'key'      => env('GOOGLE_KEY'),
                ]
            )->json();
    
            // dd($geo);
            if (empty($geo['results'][0])) continue;
    
            $res = $geo['results'][0];
            
            /* ---- TN HARD FILTER ---- */
            $isTN = false;
            foreach ($res['address_components'] as $c) {
                if (
                    in_array('administrative_area_level_1', $c['types']) &&
                    strtolower($c['long_name']) == 'tamil nadu'
                ) {
                    $isTN = true;
                    break;
                }
            }
            if (!$isTN) continue;
    
            $lat = $res['geometry']['location']['lat'];
            $lng = $res['geometry']['location']['lng'];
            $address = $res['formatted_address'];
    
            /* ---- INSERT IF NEW ---- */
            DB::table('location_address')->updateOrInsert(
                ['latitude' => $lat, 'longitude' => $lng],
                [
                    'address' => $address,
                    'state' => 'Tamil Nadu',
                    'country' => 'India',
                    'search_key' => $this->normalizeSearch($address),
                    'source' => 'google',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
    
            $new[] = [
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
            ];
        }
    
        return response()->json([
            'status' => true,
            'data' => array_slice(array_merge($dbResults, $new), 0, $limit)
        ]);
    }
    
    private function applyFareLogic(
        $fare,
        $toll,
        $cashPoints,
        $distance,
        $duration,
        $perKm,
        $jType,
        $p_date,
        $d_date,
        $fromlat,
        $fromlng,
        $tolat,
        $tolng
    ) {
        $driver_bata = 0;
        
        $day = 0;
    
        if ($jType == 'roundtrip' && $p_date && $d_date) {
    
            // $day = Carbon::parse($p_date)->diffInDays(Carbon::parse($d_date)) + 1;
            $day = $d_date;
    
            if ($day > 1) {
    
                $rule = DB::table('roundtrip_days')
                    ->where('day', $day)
                    ->first();
    
                if ($rule) {
    
                    if ($rule->km <= $distance) {
                        $driver_bata = ($day - 1) * 300;
                    } else {
                        $driver_bata = 2100 * ($day - 1);
                    }
    
                } else {
                    $driver_bata = 2100 * ($day - 1);
                }
                
                $day = $day . ' Days';
                
            }else{
                $day = $day.' Day Upto 24 hours';
            }
        }else{
            $toll = (int)($toll/2);
            
            if($distance <= 100){
                $day = 'Upto 5 hours';
            }elseif($distance >= 101 && $distance <= 200){
                $day = 'Upto 8 hours';
            }elseif($distance >= 201 && $distance <= 300){
                $day = 'Upto 12 hours';
            }elseif($distance >= 301 && $distance <= 400){
                $day = 'Upto 15 hours';
            }elseif($distance >= 401){
                $day = 'Upto 24 hours';
            }
        }
    
        // $base = ($fare - $toll) + $commission;
        $base = ($fare + $driver_bata);
    
        // $discount = round($base * 0.05);
        // $isDiscount = $discount <= $cashPoints;
    
        // if ($isDiscount) {
        //     $base -= $discount;
        // }
        $toll = round($toll);
        $commission = round(($base + $toll) * 0.05);
        $tax = round(($base + $commission) * 0.05);
    
        // $tax = round($base * 0.05);
        
        
        $get_fare = DB::table('tariff_fare_website')
            ->where('from_km', '<=', (float) $distance)
            ->where('to_km', '>=', (float) $distance)
            ->where('four_seater', '!=', 0)
            ->where('status', '0')
            ->first();

        // $to_km = $distance;

        if ($get_fare) {
            $distance = $get_fare->to_km;
        }
        
        // $distance = $to_km;
    
        return [
            'distance'      => $distance,
            'duration'      => $duration,
            'fare'          => round($base + $commission),
            'toll_fare'     => round($toll),
            'day' => $day,
            'inc_km' => $distance,
            'tax'           => $tax,
            // 'driver_bata'   => $driver_bata,
            'per_km'        => $perKm,
            'from_lat' => $fromlat,
            'from_lng' => $fromlng,
            'to_lat' => $tolat,
            'to_lng' => $tolng
        ];
    }
    
    private function applyFareLogic_App(
        $fare,
        $toll,
        $cashPoints,
        $distance,
        $duration,
        $perKm,
        $jType,
        $p_date,
        $d_date,
        $fromlat,
        $fromlng,
        $tolat,
        $tolng,
        $seaters,
        $isBelowDis
    ) {
        $driver_bata = 0;
        
        $day = 0;
    
        if ($jType == 'roundtrip' && $p_date && $d_date) {
    
            // $day = Carbon::parse($p_date)->diffInDays(Carbon::parse($d_date)) + 1;
            $day = $d_date;
    
            if ($day > 1) {
    
                $rule = DB::table('roundtrip_days')
                    ->where('day', $day)
                    ->first();
    
                if ($rule) {
    
                    if ($rule->km <= $distance) {
                        $driver_bata = ($day - 1) * 300;
                    } else {
                        $driver_bata = 2100 * ($day - 1);
                    }
    
                } else {
                    $driver_bata = 2100 * ($day - 1);
                }
                
                $day = $day . ' Days';
                
            }else{
                $day = $day.' Day Upto 24 hours';
            }
        }else{
            $toll = (int)($toll/2);
            
            if($distance <= 100){
                $day = 'Upto 5 hours';
            }elseif($distance >= 101 && $distance <= 200){
                $day = 'Upto 8 hours';
            }elseif($distance >= 201 && $distance <= 300){
                $day = 'Upto 12 hours';
            }elseif($distance >= 301 && $distance <= 400){
                $day = 'Upto 15 hours';
            }elseif($distance >= 401){
                $day = 'Upto 24 hours';
            }
            
            if($isBelowDis){
                $fare = $this->calculateSlabFare($distance, $seaters);
            }
        }
    
        $base = ($fare + $driver_bata);
        
        $com = round(($base + $toll) * 0.05);
        
        $toll = round($toll);
        
        $u_cash_point = auth()->user()->cash_points ?? 0;
        
        $discount = round(($base + $com) * 0.05);
        if($discount > 500){
            $discount = 500;
        }
        
        // $discount_amt = $discount;
        
        if($discount <= $u_cash_point && $u_cash_point != 0){
            
            $tax = round( (($base + $com) - $discount) * 0.05);
            
        }elseif($u_cash_point != 0){
         
            $discount = min($u_cash_point, $discount);
            $tax = round( (($base + $com) - $discount) * 0.05);
            
        }else{
            $tax = round(($base + $com) * 0.05);
            $discount = 0;
        }
    
        $isDiscount = ($u_cash_point != 0) ? 'yes' : 'no';
        
        
        $get_fare = DB::table('tariff_fare_website')
            ->where('from_km', '<=', (float) $distance)
            ->where('to_km', '>=', (float) $distance)
            ->where($seaters, '!=', 0)
            // ->where(function ($q) use ($seaters) {
            //     foreach ($seaters as $seater) {
            //         $q->orWhere($seater, '!=', 0);
            //     }
            // })
            ->where('status', '0')
            ->first();

        if ($get_fare && !$isBelowDis) {
            $distance = $get_fare->to_km;
        }
        
    
        return [
            'from_lat'      => $fromlat,
            'from_lng'      => $fromlng,
            'to_lat'        => $tolat,
            'to_lng'        => $tolng,
            'day'           => $day,
            'distance'      => $distance,
            'duration'      => $duration,
            'per_km'        => $perKm,
            'inc_km'        => $distance,
            
            'base_fare'     => $base,
            'toll_fare'     => round($toll),
            'tax'           => $tax,
            'com'           => $com,
            'driver_bata'   => $driver_bata,
            'isDiscount'    => $isDiscount,
            'discount'    => $discount,
            'fare'          => round(($base + $com + $tax + $toll) -$discount)
        ];
    }
    
    private function getRouteData($fromLat, $fromLng, $toLat, $toLng)
    {
        $res = Http::withHeaders([
            'X-Goog-Api-Key' => env('DISTANCE_GOOGLE_KEY'),
            'X-Goog-FieldMask' => 'routes.distanceMeters,routes.duration,routes.travelAdvisory'
        ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
            'origin' => ['location' => ['latLng' => ['latitude' => $fromLat, 'longitude' => $fromLng]]],
            'destination' => ['location' => ['latLng' => ['latitude' => $toLat, 'longitude' => $toLng]]],
            'travelMode' => 'DRIVE',
            'extraComputations' => ['TOLLS']
        ])->json();
    
        if (!isset($res['routes'][0])) return null;
    
        $route = $res['routes'][0];
    
        return [
            'distance' => $route['distanceMeters'],
            'duration' => rtrim($route['duration'], 's') . ' sec',
            'toll'     => $route['travelAdvisory'] ? ($route['travelAdvisory']['tollInfo']['estimatedPrice'][0]['units']??0) : 0,
            'toll_details' => $route['travelAdvisory'] ? json_encode($route['travelAdvisory']['tollInfo']) : null
        ];
    }
    
    private function getLatLngByPlaceId(string $placeId)
    {
        $loc = DB::table('outstation_locations')
            ->where('place_id', $placeId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();
    
        if ($loc) {
            return [
                'lat' => $loc->latitude,
                'lng' => $loc->longitude,
            ];
        }
    
        // Google Geocode (ONLY ONCE PER PLACE)
        $geo = Http::timeout(3)->get(
            'https://maps.googleapis.com/maps/api/geocode/json',
            [
                'place_id' => $placeId,
                'key'      => env('GOOGLE_KEY'),
            ]
        )->json();
    
        if (empty($geo['results'][0])) {
            return null;
        }
    
        $res = $geo['results'][0];
        $lat = $res['geometry']['location']['lat'];
        $lng = $res['geometry']['location']['lng'];
    
        DB::table('outstation_locations')
            ->where('place_id', $placeId)
            ->update([
                'latitude'   => $lat,
                'longitude'  => $lng,
                'updated_at' => now(),
            ]);
    
        return [
            'lat' => $lat,
            'lng' => $lng,
        ];
    }
    
    public function GoogleLocationsOldDB(Request $request)
    {
        try {
            
            $search = trim($request->search);
        
            if (strlen($search) < 2) {
                return response()->json([
                    'status' => 200,
                    'data'   => []
                ]);
            }
            
            $limit     = 3;
            $searchKey = $this->normalizeSearch($search);
            
            $dbResults = DB::table('outstation_locations')
                ->where('search_key', 'LIKE', $searchKey . '%')
                ->orderByRaw("(LENGTH(display_name) - LENGTH(REPLACE(display_name, ',', ''))) ASC")
                // ->when($request->input('d_value'), function ($query, $dValue) {
                //     return $query->where('place_id', '!=', $dValue);
                // })
                ->where('state', 'Tamil Nadu')
                ->limit(10)
                ->get([
                    'display_name as name',
                    'place_id as id',
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
                    'data'   => $dbResults
                ]);
            }
    
            $newResults = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) + count($dbResults) >= $limit) {
                    break;
                }
    
                $description = $p['description']; 
    
                if (stripos($description, 'Tamil Nadu') == false) {
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
                    'id' => $p['place_id'],
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
                'data'   => $merged
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function GoogleLocations(Request $request)
    {
        try {
            $search = trim($request->search);
    
            if (strlen($search) < 2) {
                return response()->json([
                    'status' => 200,
                    'data'   => []
                ]);
            }
    
            $limit     = 3;
            $searchKey = $this->normalizeSearch($search);
            $cacheKey  = "location_search:" . $searchKey;
    
            $cached = Cache::get($cacheKey);
            if (!empty($cached)) {
                return response()->json([
                    'status' => 200,
                    'data'   => $cached,
                    'source' => 'cache'
                ]);
            }
    
            $dbResults = DB::table('outstation_locations')
                ->where('search_key', 'LIKE', '%' . $searchKey . '%')
                ->orderByRaw("CASE 
                    WHEN search_key LIKE ? THEN 1 
                    ELSE 2 END", [$searchKey . '%'])
                ->limit($limit)
                ->get([
                    'display_name as name',
                    'place_id as id',
                    'latitude',
                    'longitude',
                    'state',
                    // 'district'
                ])
                ->toArray();
    
            if (count($dbResults) >= $limit) {
    
                Cache::put($cacheKey, $dbResults, now()->addHours(6));
    
                return response()->json([
                    'status' => 200,
                    'data'   => $dbResults,
                    'source' => 'db'
                ]);
            }
    
            $gKeys = array_values(array_filter([
                env('GOOGLE_KEY_ONE'),
                env('GOOGLE_KEY_TWO'),
                env('GOOGLE_KEY_THREE'),
            ]));
    
            if (empty($gKeys)) {
                return response()->json([
                    'status' => false,
                    'error'  => 'No Google API keys configured'
                ]);
            }
    
            $googleKey = $gKeys[array_rand($gKeys)];
    
            $google = Http::timeout(3)->get(
                'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                [
                    'input'      => $search,
                    'key'        => $googleKey,
                    'components' => 'country:in',
                    'types'      => 'geocode'
                ]
            )->json();
    
            if (($google['status'] ?? '') !== 'OK') {
    
                Cache::put($cacheKey, $dbResults, now()->addMinutes(10));
    
                return response()->json([
                    'status' => 200,
                    'data'   => $dbResults
                ]);
            }
    
            $newResults = [];
            $insertData = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) >= ($limit - count($dbResults))) break;
    
                // $mainText = $p['structured_formatting']['main_text'] ?? null;
                $mainText = $p['description'] ?? null;
                if (!$mainText) continue;
    
                $placeId = $p['place_id'];
                $state = $p['terms'][2]['value'] ?? $p['terms'][1]['value'] ?? $p['terms'][0]['value'];
    
                // $detailsCacheKey = "place_details_" . $placeId;
    
                // $details = Cache::remember($detailsCacheKey, now()->addDays(7), function () use ($placeId, $gKeys) {
    
                //     $googleKey = $gKeys[array_rand($gKeys)];
    
                //     return Http::timeout(3)->get(
                //         'https://maps.googleapis.com/maps/api/place/details/json',
                //         [
                //             'place_id' => $placeId,
                //             'key'      => $googleKey,
                //             'fields'   => 'address_component,geometry'
                //         ]
                //     )->json();
                // });
    
                // if (!isset($details['result'])) continue;
    
                // $components = $details['result']['address_components'] ?? [];
    
                // $state = null;
                $district = null;
                // $lat = $details['result']['geometry']['location']['lat'] ?? null;
                // $lng = $details['result']['geometry']['location']['lng'] ?? null;
                $lat = null;
                $lng = null;
    
                // foreach ($components as $c) {
    
                //     if (in_array('administrative_area_level_1', $c['types'])) {
                //         $state = $c['long_name'];
                //     }
    
                //     if (in_array('administrative_area_level_2', $c['types'])) {
                //         $district = $c['long_name'];
                //     }
                // }
    
                // if (!$state) continue;
    
                // if ($district && strtolower($district) == strtolower($state)) {
                //     $district = null;
                // }
    
                $formattedName = $mainText;
    
                // if ($district) {
                //     $formattedName .= ', ' . $district;
                // }
    
                // $formattedName .= ', ' . $state . ', India';
    
                $lat = $lat !== null ? round($lat, 6) : null;
                $lng = $lng !== null ? round($lng, 6) : null;
    
                $newResults[] = [
                    'name'      => $formattedName,
                    'id'        => $placeId,
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'state'     => $state,
                    'district'  => $district
                ];
    
                $insertData[] = [
                    'place_id'     => $placeId,
                    'name'         => $mainText,
                    'display_name' => $formattedName,
                    'state'        => $state,
                    'country'      => 'India',
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'search_key'   => $this->normalizeSearch($formattedName),
                    'source'       => 'google',
                    'res_json'       => json_encode($p),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
    
            if (!empty($insertData)) {
                DB::table('outstation_locations')->upsert(
                    $insertData,
                    ['place_id'],
                    ['name', 'display_name', 'state', 'latitude', 'longitude', 'updated_at']
                );
            }
    
            $final = collect($dbResults)
                ->merge($newResults)
                ->unique('place_id')
                ->values()
                ->take($limit)
                ->toArray();
    
            if (!empty($final)) {
                Cache::put($cacheKey, $final, now()->addHours(6));
            }
    
            return response()->json([
                'status' => 200,
                'data'   => $final,
                'source' => 'hybrid'
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    // public function GoogleLocations(Request $request)
    // {
    //     try {
    
    //         $search = trim($request->search);
    
    //         if (strlen($search) < 2) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'data'   => []
    //             ]);
    //         }
    
    //         $limit     = 3;
    //         $searchKey = $this->normalizeSearch($search);
    //         $cacheKey  = "location_search:" . $searchKey;
            
    //         if (Cache::has($cacheKey)) {
    //             return response()->json([
    //                 'status' => 200,
    //                 'data'   => Cache::get($cacheKey),
    //                 'source' => 'redis'
    //             ]);
    //         }
    
    //         $dbResults = DB::table('outstation_locations')
    //             ->where('search_key', 'LIKE', $searchKey . '%')
    //             ->where('state', 'Tamil Nadu')
    //             ->orderByRaw("(LENGTH(display_name) - LENGTH(REPLACE(display_name, ',', ''))) ASC")
    //             ->limit(10)
    //             ->get([
    //                 'display_name as name',
    //                 'place_id as id',
    //                 'latitude',
    //                 'longitude'
    //             ])
    //             ->toArray();
    
    //         if (count($dbResults) >= $limit) {
    
    //             Cache::put($cacheKey, $dbResults, now()->addHours(6));
    
    //             return response()->json([
    //                 'status' => 200,
    //                 'data'   => $dbResults,
    //                 'source' => 'db'
    //             ]);
    //         }
    
    //         $google = Http::timeout(3)->get(
    //             'https://maps.googleapis.com/maps/api/place/autocomplete/json',
    //             [
    //                 'input'      => $search,
    //                 'key'        => env('GOOGLE_KEY'),
    //                 'components' => 'country:in',
    //                 'types'      => 'geocode'
    //             ]
    //         )->json();
    
    //         if (($google['status'] ?? '') !== 'OK') {
    
    //             Cache::put($cacheKey, $dbResults, now()->addMinutes(30));
    
    //             return response()->json([
    //                 'status' => true,
    //                 'data'   => $dbResults
    //             ]);
    //         }
    
    //         $newResults = [];
    //         $insertData = [];
    
    //         foreach ($google['predictions'] as $p) {
    
    //             if (count($newResults) + count($dbResults) >= $limit) break;
    
    //             $mainText      = $p['structured_formatting']['main_text'] ?? null;
    //             $secondaryText = $p['structured_formatting']['secondary_text'] ?? '';
    
    //             if (!$mainText) continue;
    
    //             $parts = array_map('trim', explode(',', $secondaryText));
    //             $e_j   = json_encode($p);
    
    //             $district = null;
    //             $state    = null;
    //             $pincode  = null;
    //             $country  = null;
    
    //             // ✅ Step 1: Try extract from autocomplete (FREE)
    //             foreach ($parts as $part) {
    
    //                 if (preg_match('/\b\d{6}\b/', $part)) {
    //                     $pincode = $part;
    //                     continue;
    //                 }
    
    //                 if (stripos($part, 'Tamil Nadu') !== false) {
    //                     $state = 'Tamil Nadu';
    //                     continue;
    //                 }
    
    //                 if (strcasecmp($part, 'India') === 0) {
    //                     $country = 'India';
    //                     continue;
    //                 }
    
    //                 if (!$district &&
    //                     stripos($part, 'Tamil Nadu') === false &&
    //                     strcasecmp($part, 'India') !== 0) {
    //                     $district = $part;
    //                 }
    //             }
    
    //             // 🔥 Step 2: If district missing → call Place Details (ONLY THEN)
    //             if (!$district) {
    
    //                 $details = Http::timeout(2)->get(
    //                     'https://maps.googleapis.com/maps/api/place/details/json',
    //                     [
    //                         'place_id' => $p['place_id'],
    //                         'key'      => env('GOOGLE_KEY'),
    //                         'fields'   => 'address_components'
    //                     ]
    //                 )->json();
    
    //                 foreach ($details['result']['address_components'] ?? [] as $comp) {
    
    //                     if (in_array('administrative_area_level_2', $comp['types'])) {
    //                         $district = $comp['long_name'];
    //                     }
    
    //                     if (in_array('administrative_area_level_1', $comp['types'])) {
    //                         $state = $comp['long_name'];
    //                     }
    
    //                     if (in_array('postal_code', $comp['types'])) {
    //                         $pincode = $comp['long_name'];
    //                     }
    
    //                     if (in_array('country', $comp['types'])) {
    //                         $country = $comp['long_name'];
    //                     }
    //                 }
    //             }
    
    //             // ❌ Mandatory district
    //             if (!$district) {
    //                 continue;
    //             }
    
    //             if (!$state) {
    //                 $state = 'Tamil Nadu';
    //             }
    
    //             $formattedName = $mainText;
    
    //             if ($district && $district !== $state) {
    //                 $formattedName .= ', ' . $district;
    //             }
    
    //             $formattedName .= ', ' . $state;
    
    //             if ($pincode) {
    //                 $formattedName .= ' ' . $pincode;
    //             }
    
    //             if ($country) {
    //                 $formattedName .= ', ' . $country;
    //             }
    
    //             $insertData[] = [
    //                 'place_id'     => $p['place_id'],
    //                 'name'         => $mainText,
    //                 'display_name' => $formattedName,
    //                 'district'     => $district,
    //                 'state'        => $state,
    //                 'country'      => 'India',
    //                 'search_key'   => $this->normalizeSearch($formattedName),
    //                 'source'       => 'google',
    //                 'res_json'     => $e_j,
    //                 'created_at'   => now(),
    //                 'updated_at'   => now(),
    //             ];
    
    //             $newResults[] = [
    //                 'name'      => $formattedName,
    //                 'id'        => $p['place_id'],
    //                 'latitude'  => null,
    //                 'longitude' => null
    //             ];
    //         }
    
    //         if (!empty($insertData)) {
    //             DB::table('outstation_locations')->upsert(
    //                 $insertData,
    //                 ['place_id'],
    //                 ['name', 'display_name', 'district', 'updated_at']
    //             );
    //         }
    
    //         $merged = collect($dbResults)
    //             ->merge($newResults)
    //             ->unique('id')
    //             ->values()
    //             ->take($limit)
    //             ->toArray();
    
    //         Cache::put($cacheKey, $merged, now()->addHours(6));
    
    //         return response()->json([
    //             'status' => 200,
    //             'data'   => $merged,
    //             'source' => 'google'
    //         ]);
    
    //     } catch (\Throwable $e) {
    
    //         return response()->json([
    //             'status' => false,
    //             'error'  => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    private function getFareConfig($seater)
    {
        return [
            'mini_four_seater' => [
                [1, 92],
                [2, 24],
                [2, 33],
                [5, 33],
                [5, 29],
                [5, 27],
                [5, 26],
                [5, 25],
                [5, 24],
                [5, 22],
                [5, 21],
                [INF, 20],
            ],
            'four_seater' => [
                [1, 114],
                [2, 24],
                [2, 32],
                [5, 33],
                [5, 29],
                [5, 27],
                [5, 26],
                [5, 25],
                [5, 24],
                [5, 22],
                [5, 21],
                [INF, 20],
            ],
            'six_seater' => [
                [1, 158],
                [2, 34],
                [2, 36],
                [5, 35],
                [5, 34],
                [5, 33],
                [5, 32],
                [5, 31],
                [5, 30],
                [5, 29],
                [5, 28],
                [INF, 27],
            ],
            'seven_seater' => [
                [1, 188],
                [2, 38],
                [2, 36],
                [5, 35],
                [5, 34],
                [5, 34],
                [5, 32],
                [5, 32],
                [5, 30],
                [5, 29],
                [5, 28],
                [INF, 27],
            ],
        ][$seater] ?? [];
    }
    
    private function calculateSlabFare($distance, $seater)
    {
        $config = $this->getFareConfig($seater);
    
        $remainingDistance = $distance;
        $totalFare = 0;
    
        foreach ($config as [$slabKm, $rate]) {
            if ($remainingDistance <= 0) break;
    
            $applicableKm = min($remainingDistance, $slabKm);
            $totalFare += $applicableKm * $rate;
    
            $remainingDistance -= $applicableKm;
        }
    
        return round($totalFare);
    }
    
    public function DistanceAndDuration(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => 'required|string',
                'to_place_id'   => 'required|string',
                'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date'   => ['nullable'],
                'way_type'      => 'required|in:oneway,roundtrip',
            ]);
            
            // call helper function send data to it.
            
    
            $pickup = Carbon::parse($request->pickup_date);
            $now    = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHours(2))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pickup time must be at least 2 hours from now'
                ]);
            }
            
            if($request->from_place_id == $request->to_place_id){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Please select different pickup and drop-off locations.'
                ]);
            }
            
            $pickDate = Carbon::parse($request->pickup_date)->format('Y-m-d');
            $dropDate = $request->dropoff_date;
    
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
    
            if (!$fromGeo || !$toGeo) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unable to resolve coordinates'
                ], 422);
            }
    
            $journeyType    = $request->way_type;
            $userCashPoints = auth()->user()->cash_points ?? 0;
    
            $seaters = [];
            
            if($request->pass == 'mini'){
                $seaters[] = 'mini_four_seater';
            }else if($request->pass == 4 || $request->pass == 5){
                $seaters[] = 'four_seater';
            }else if($request->pass == 7){
                $seaters[] = 'six_seater';
            }else if($request->pass == 8){
                $seaters[] = 'seven_seater';
            }
    
            $responseData = [];
            
            $isBelowDis = false;
            
            $fromName = DB::table('outstation_locations')
                ->where('place_id', $request->from_place_id)
                ->value('display_name');
        
            $toName = DB::table('outstation_locations')
                ->where('place_id', $request->to_place_id)
                ->value('display_name');
    
            foreach ($seaters as $seater) {
    
                /* ---- Cache Check ---- */
                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $request->from_place_id,
                        'to_place_id'   => $request->to_place_id,
                        'seater'        => $seater,
                    ])->first();
                    
                if ($cached) {
                    
                    userLocationLog::logUserActivity(
                        auth()->id() ?? $request->user_id,
                        'outstation_locations',
                        'location_search_cached',
                        null,
                        [
                            'from_place_id' => $request->from_place_id,
                            'to_place_id'   => $request->to_place_id,
                            'from_name'     => $fromName ?? null,
                            'to_name'       => $toName ?? null,
                            'seater'        => $seater,
                            'distance'      => $cached->distance ?? null,
                            'duration'      => $cached->duration ?? null,
                            'source'        => 'cache'
                        ]
                    );
                    
                    $baseFare = $journeyType == 'roundtrip'
                        ? $cached->return_fare
                        : $cached->oneway_fare;
                        
                        
                    $distance = $journeyType == 'roundtrip'
                        ? ($cached->distance * 2)
                        : $cached->distance;
                        
                    $isBelowDis = ($distance <= 50) ? true : false;
                        
                    $s_arr = [$seater, $seater.'_round'];
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    if ($tariffs->isEmpty()) {
                        continue;
                    }
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($journeyType == 'roundtrip') {
                        $baseFare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
                    
                    // if ($isBelowDis && $journeyType == 'oneway') {
                        
                    // }
    
    
                    $responseData = $this->applyFareLogic_App(
                        $baseFare,
                        $cached->toll_fare,
                        $userCashPoints,
                        $distance,
                        $cached->duration,
                        $perKm,
                        $journeyType,
                        $pickDate,
                        $dropDate,
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng'],
                        $seater,
                        $isBelowDis
                    );
                    
                    $responseData['fare'] = $responseData['base_fare'] + $responseData['com'];
                    
    
                    continue;
                }
    
                $route = $this->getRouteData(
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng']
                );
    
                if (!$route) continue;
    
                $seconds = (int) rtrim($route['duration'], 's');
                $minutes = ceil($seconds / 60);
                $hours   = intdiv($minutes, 60);
                $mins    = $minutes % 60;
    
                if ($hours > 0 && $mins > 0) {
                    $duration = "{$hours} hours {$mins} mins";
                } elseif ($hours > 0) {
                    $duration = "{$hours} hours";
                } else {
                    $duration = "{$mins} mins";
                }
    
                /* ---- Distance ---- */
                $distanceKm = ceil($route['distance'] / 1000);
                
                $dis_km = $distanceKm;
                
                if ($journeyType == 'roundtrip') {
                    $distanceKm *= 2;
                    
                    // $seater = $seater.'_round';
                }
                
                $isBelowDis = ($distanceKm < 50) ? true : false;
                
                $s_arr = [$seater, $seater.'_round'];
    
                // 1. Fetch all matching records (should return 2 rows)
                $tariffs = DB::table('tariff_fare_website')
                    ->where('from_km', '<=', $distanceKm)
                    ->where('to_km', '>=', $distanceKm)
                    ->where('status', '0')
                    ->where(function($query) use ($seater) {
                        $query->where($seater, '>', 0)
                              ->orWhere($seater.'_round', '>', 0);
                    })
                    ->get();
                
                // 2. Check if we actually got results
                if ($tariffs->isEmpty()) {
                    continue;
                }
                
                // 3. Find the specific values across the records
                // We use 'firstWhere' to find the specific row that has a value for that column
                $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                
                // 4. Assign values, ensuring we have fallbacks if one row is missing
                $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                
                $tollFare = (int) ($route['toll'] ?? 0);
                
                /* ---- Cache Result ---- */
                DB::table('location_distance_web')->insert([
                    'from_place_id' => $request->from_place_id,
                    'to_place_id'   => $request->to_place_id,
                    'seater'        => $seater,
                    'distance'      => $dis_km,
                    'duration'      => $duration,
                    'oneway_fare'   => $baseFare,
                    'return_fare'   => $baseFare_r,
                    'toll_fare'     => $tollFare,
                    'toll_details'  => $route['toll_details'] ?? null,
                    'per_km'        => $perKm,
                    'per_km_round'  => $perKmRound,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                
                userLocationLog::logUserActivity(
                    auth()->id() ?? $request->user_id,
                    'outstation_locations',
                    'location_search',
                    null,
                    [
                        'from_place_id' => $request->from_place_id,
                        'to_place_id'   => $request->to_place_id,
                        'from_name'     => $fromName ?? null,
                        'to_name'       => $toName ?? null,
                        'seater'        => $seater,
                        'distance'      => $dis_km ?? null,
                        'duration'      => $duration ?? null,
                        'source'        => 'new'
                    ]
                );
                
                if ($journeyType == 'roundtrip') {
                    $baseFare = $baseFare_r;
                    $perKm = $perKmRound;
                }
                
                
                
                $responseData = $this->applyFareLogic_App(
                    $baseFare,
                    $tollFare,
                    $userCashPoints,
                    $distanceKm,
                    $duration,
                    $perKm,
                    // $perKmRound,
                    $journeyType,
                    $pickDate,
                    $dropDate,
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng'],
                    $seater,
                    $isBelowDis
                );
                $responseData['fare'] = $responseData['base_fare'] + $responseData['com'];
                /* ---- Response ---- */
                
            }
            
            
            // if($isBelowDis){
            //     // \Log::info('Hii bro...: ' . 'Hii da thambi');
            //     return response()->json([
            //         'status'  => false,
            //         'data'    => [],
            //         'message' => 'The minimum booking distance is 50 km.'
            //     ]);
            // }
    
            return response()->json([
                'status'  => true,
                'data'    => $responseData,
                'message' => 'Fare calculated successfully'
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function DistanceAndDurationAll(Request $request)
    {
        try {
    
            // if (!$this->verifyRecaptcha($request->recaptcha_token, 'distance_fetch')) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Bot detected'
            //     ], 403);
            // }
    
            $request->validate([
                'from_place_id' => 'required|string',
                'to_place_id'   => 'required|string',
                'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date'   => ['nullable'],
                'way_type'      => 'required|in:oneway,roundtrip',
            ]);
            
            
    
            $pickup = Carbon::parse($request->pickup_date);
            $now    = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHours(2))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pickup time must be at least 2 hours from now'
                ]);
            }
            
            if($request->from_place_id == $request->to_place_id){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Please select different pickup and drop-off locations.'
                ]);
            }
            
            $pickDate = Carbon::parse($request->pickup_date)->format('Y-m-d');
            $dropDate = $request->dropoff_date;
    
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
    
            if (!$fromGeo || !$toGeo) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unable to resolve coordinates'
                ], 422);
            }
    
            $journeyType    = $request->way_type;
            $userCashPoints = auth()->user()->cash_points ?? 0;
    
            $seaters = [
                'mini_four_seater',
                'four_seater',
                'six_seater',
                'seven_seater'
            ];
    
            $responseData = [];
            
            $isBelowDis = false;
    
            foreach ($seaters as $seater) {
    
                /* ---- Cache Check ---- */
                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $request->from_place_id,
                        'to_place_id'   => $request->to_place_id,
                        'seater'        => $seater,
                    ])->first();
                    
                if ($cached) {
                    
                    $baseFare = $journeyType == 'roundtrip'
                        ? $cached->return_fare
                        : $cached->oneway_fare;
                        
                        
                    $distance = $journeyType == 'roundtrip'
                        ? ($cached->distance * 2)
                        : $cached->distance;
                        
                    $isBelowDis = ($distance < 50) ? true : false;
                        
                    $s_arr = [$seater, $seater.'_round'];
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    if ($tariffs->isEmpty()) {
                        continue;
                    }
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($journeyType == 'roundtrip') {
                        $baseFare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
    
    
                    $responseData[$seater] = $this->applyFareLogic(
                        $baseFare,
                        $cached->toll_fare,
                        $userCashPoints,
                        $distance,
                        $cached->duration,
                        $perKm,
                        $journeyType,
                        $pickDate,
                        $dropDate,
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng']
                    );
    
                    continue;
                }
    
                $route = $this->getRouteData(
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng']
                );
    
                if (!$route) continue;
    
                $seconds = (int) rtrim($route['duration'], 's');
                $minutes = ceil($seconds / 60);
                $hours   = intdiv($minutes, 60);
                $mins    = $minutes % 60;
    
                if ($hours > 0 && $mins > 0) {
                    $duration = "{$hours} hours {$mins} mins";
                } elseif ($hours > 0) {
                    $duration = "{$hours} hours";
                } else {
                    $duration = "{$mins} mins";
                }
    
                /* ---- Distance ---- */
                $distanceKm = ceil($route['distance'] / 1000);
                
                $dis_km = $distanceKm;
                
                if ($journeyType == 'roundtrip') {
                    $distanceKm *= 2;
                    
                    // $seater = $seater.'_round';
                }
                
                $isBelowDis = ($distanceKm < 50) ? true : false;
                
                $s_arr = [$seater, $seater.'_round'];
    
                // 1. Fetch all matching records (should return 2 rows)
                $tariffs = DB::table('tariff_fare_website')
                    ->where('from_km', '<=', $distanceKm)
                    ->where('to_km', '>=', $distanceKm)
                    ->where('status', '0')
                    ->where(function($query) use ($seater) {
                        $query->where($seater, '>', 0)
                              ->orWhere($seater.'_round', '>', 0);
                    })
                    ->get();
                
                // 2. Check if we actually got results
                if ($tariffs->isEmpty()) {
                    continue;
                }
                
                // 3. Find the specific values across the records
                // We use 'firstWhere' to find the specific row that has a value for that column
                $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                
                // 4. Assign values, ensuring we have fallbacks if one row is missing
                $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                
                $tollFare = (int) ($route['toll'] ?? 0);
                
                /* ---- Cache Result ---- */
                DB::table('location_distance_web')->insert([
                    'from_place_id' => $request->from_place_id,
                    'to_place_id'   => $request->to_place_id,
                    'seater'        => $seater,
                    'distance'      => $dis_km,
                    'duration'      => $duration,
                    'oneway_fare'   => $baseFare,
                    'return_fare'   => $baseFare_r,
                    'toll_fare'     => $tollFare,
                    'toll_details'  => $route['toll_details'] ?? null,
                    'per_km'        => $perKm,
                    'per_km_round'  => $perKmRound,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                
                if ($journeyType == 'roundtrip') {
                    $baseFare = $baseFare_r;
                    $perKm = $perKmRound;
                }
                
                
                
                $responseData[$seater] = $this->applyFareLogic(
                    $baseFare,
                    $tollFare,
                    $userCashPoints,
                    $distanceKm,
                    $duration,
                    $perKm,
                    // $perKmRound,
                    $journeyType,
                    $pickDate,
                    $dropDate,
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng']
                );
                /* ---- Response ---- */
                
            }
            
            if($isBelowDis){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'The minimum booking distance is 50 km.'
                ]);
            }
            
            // \Log::error('Testing...: ' . $responseData);
    
            return response()->json([
                'status'  => true,
                'data'    => $responseData,
                'message' => 'Fare calculated successfully'
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function AdminDistanceAndDurationAll(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => 'required|string',
                'to_place_id'   => 'required|string',
                'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date'   => ['nullable'],
                'way_type'      => 'required|in:oneway,roundtrip',
            ]);
    
            $pickup = Carbon::parse($request->pickup_date);
            $now    = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHours(2))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pickup time must be at least 2 hours from now'
                ]);
            }
            
            if($request->from_place_id == $request->to_place_id){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'Please select different pickup and drop-off locations.'
                ]);
            }
            
            $pickDate = Carbon::parse($request->pickup_date)->format('Y-m-d');
            $dropDate = $request->dropoff_date;
    
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
    
            if (!$fromGeo || !$toGeo) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unable to resolve coordinates'
                ], 422);
            }
    
            $journeyType    = $request->way_type;
            $userCashPoints = auth()->user()->cash_points ?? 0;
    
            $seaters = [
                'four_seater',
                'seven_seater'
            ];
    
            $responseData = [];
            
            $isBelowDis = false;
    
            foreach ($seaters as $seater) {
    
                /* ---- Cache Check ---- */
                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $request->from_place_id,
                        'to_place_id'   => $request->to_place_id,
                        'seater'        => $seater,
                    ])->first();
                    
                if ($cached) {
                    
                    $baseFare = $journeyType == 'roundtrip'
                        ? $cached->return_fare
                        : $cached->oneway_fare;
                        
                        
                    $distance = $journeyType == 'roundtrip'
                        ? ($cached->distance * 2)
                        : $cached->distance;
                        
                    $isBelowDis = ($distance < 50) ? true : false;
                        
                    $s_arr = [$seater, $seater.'_round'];
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    if ($tariffs->isEmpty()) {
                        continue;
                    }
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($journeyType == 'roundtrip') {
                        $baseFare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
    
    
                    $responseData[$seater] = $this->applyFareLogic(
                        $baseFare,
                        $cached->toll_fare,
                        $userCashPoints,
                        $distance,
                        $cached->duration,
                        $perKm,
                        $journeyType,
                        $pickDate,
                        $dropDate,
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng']
                    );
    
                    continue;
                }
    
                $route = $this->getRouteData(
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng']
                );
    
                if (!$route) continue;
    
                $seconds = (int) rtrim($route['duration'], 's');
                $minutes = ceil($seconds / 60);
                $hours   = intdiv($minutes, 60);
                $mins    = $minutes % 60;
    
                if ($hours > 0 && $mins > 0) {
                    $duration = "{$hours} hours {$mins} mins";
                } elseif ($hours > 0) {
                    $duration = "{$hours} hours";
                } else {
                    $duration = "{$mins} mins";
                }
    
                /* ---- Distance ---- */
                $distanceKm = ceil($route['distance'] / 1000);
                
                $dis_km = $distanceKm;
                
                if ($journeyType == 'roundtrip') {
                    $distanceKm *= 2;
                    
                    // $seater = $seater.'_round';
                }
                
                $isBelowDis = ($distanceKm < 50) ? true : false;
                
                $s_arr = [$seater, $seater.'_round'];
    
                // 1. Fetch all matching records (should return 2 rows)
                $tariffs = DB::table('tariff_fare_website')
                    ->where('from_km', '<=', $distanceKm)
                    ->where('to_km', '>=', $distanceKm)
                    ->where('status', '0')
                    ->where(function($query) use ($seater) {
                        $query->where($seater, '>', 0)
                              ->orWhere($seater.'_round', '>', 0);
                    })
                    ->get();
                
                // 2. Check if we actually got results
                if ($tariffs->isEmpty()) {
                    continue;
                }
                
                // 3. Find the specific values across the records
                // We use 'firstWhere' to find the specific row that has a value for that column
                $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                
                // 4. Assign values, ensuring we have fallbacks if one row is missing
                $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                
                $tollFare = (int) ($route['toll'] ?? 0);
                
                /* ---- Cache Result ---- */
                DB::table('location_distance_web')->insert([
                    'from_place_id' => $request->from_place_id,
                    'to_place_id'   => $request->to_place_id,
                    'seater'        => $seater,
                    'distance'      => $dis_km,
                    'duration'      => $duration,
                    'oneway_fare'   => $baseFare,
                    'return_fare'   => $baseFare_r,
                    'toll_fare'     => $tollFare,
                    'toll_details'  => $route['toll_details'] ?? null,
                    'per_km'        => $perKm,
                    'per_km_round'  => $perKmRound,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                
                if ($journeyType == 'roundtrip') {
                    $baseFare = $baseFare_r;
                    $perKm = $perKmRound;
                }
                
                
                
                $responseData[$seater] = $this->applyFareLogic(
                    $baseFare,
                    $tollFare,
                    $userCashPoints,
                    $distanceKm,
                    $duration,
                    $perKm,
                    // $perKmRound,
                    $journeyType,
                    $pickDate,
                    $dropDate,
                    $fromGeo['lat'],
                    $fromGeo['lng'],
                    $toGeo['lat'],
                    $toGeo['lng']
                );
                /* ---- Response ---- */
                
            }
            
            if($isBelowDis){
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'The minimum booking distance is 50 km.'
                ]);
            }
    
            return response()->json([
                'status'  => true,
                'data'    => $responseData,
                'message' => 'Fare calculated successfully'
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }
    }
    
    public function bookJourney(Request $request)
    {
        try {
            
            // if (!$this->verifyRecaptcha($request->recaptcha_token, 'booking_create')) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Bot detected'
            //     ], 403);
            // }
    
            $request->validate([
                'job_type' => ['required', 'string', 'max:255'],
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date' => ['nullable'],
                'day' => ['nullable'],
                'pass_count' => ['required', 'string', 'max:255'],
                'fare' => ['required', 'numeric'],
                'distance' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'string', 'max:255'],
    
                'add_fare_details' => ['required', 'array'],
                'add_fare_details.bata' => ['required', 'string', 'max:255'],
                'add_fare_details.parking' => ['required', 'string', 'max:255'],
                'add_fare_details.toll' => ['required', 'string', 'max:255'],
    
                'type' => ['required', 'string', 'max:255'],
                'c_name' => ['required', 'string', 'max:255'],
                'c_email' => ['nullable', 'string', 'max:255'],
                'c_mobile' => ['required', 'string', 'max:255'],
                'isDriver' => ['nullable', 'string', 'max:255'],
            ]);
    
            $data = $request->all();
        
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour(2))) 
            {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be at least 2 hour after the current time.'
                ]);
            }
    
            // $jobCount = DB::table('cus_job_temp')->count() + 1;
            // $job_no = "GRC-" . str_pad($jobCount, 3, '0', STR_PAD_LEFT);
            
            $maxAttempts = 5;

            for ($i = 0; $i < $maxAttempts; $i++) {
            
                // $job_no = 'GRC-' . strtoupper(bin2hex(random_bytes(4)));
                $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            
                if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            
                if ($i == $maxAttempts - 1) {
                    // throw new \Exception('Unable to generate unique job number.');
                    // goto HI;
                }
            }
            
            $cab_seater = [
                'mini_four_seater' => 'mini',
                'four_seater'      => '5',
                'six_seater'       => '7',
                'seven_seater'  => '8'
            ];
            
            // $request->pass_count = $cab_seater[$request->cab_type];
            $data['pass_count'] = $cab_seater[$request->cab_type];
    
            $data['job_no'] = $job_no;
            $data['global_type'] = 'customer';
            $data['user_id'] = 0;
    
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
            $data['pick_address'] = $data['pick_address'];
            $data['drop_address'] = $data['drop_address'];
    
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = $data['dropoff_date'];
    
            $data['created_at'] = now();
            $data['updated_at'] = now();
            
            $data['otp'] = Controller::generateOTP(6);
    
            if ($data['isDriver'] == 'no') 
            {
                
                $column = $request->cab_type;
                
                $check_data = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $data['from_place_id'],
                        'to_place_id'   => $data['to_place_id'],
                        'seater' => $column
                    ])->first();
                
                if ($check_data && !empty($data['pass_count'])) {

                    $toll_fare = $check_data->toll_fare;
                    
                    $distance = $data['job_type'] == 'roundtrip'
                        ? ($check_data->distance * 2)
                        : $check_data->distance;
                    
                    
                    $seater = $request->cab_type;
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    if ($tariffs->isEmpty()) {
                         return response()->json([
                            'status' => false,
                            'data' => [],
                            'message' => 'Fare not found'
                        ]);
                    }
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $fare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($data['job_type'] == 'roundtrip') {
                        $fare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
                    
                    $driver_bata = 0;
                    $day = null;
                    
                    $p_date = Carbon::parse($data['pickup_date'])->format('Y-m-d');
    
                    if ($data['job_type'] == 'roundtrip' && $p_date) {
                        
                        // $d_date = Carbon::parse($data['dropoff_date'])->format('Y-m-d');
                        $d_date = $data['dropoff_date'];
                
                        // $day = Carbon::parse($p_date)->diffInDays(Carbon::parse($d_date)) + 1;
                        $day = $d_date;
                
                        if ($day > 1) {
                
                            $rule = DB::table('roundtrip_days')
                                ->where('day', $day)
                                ->first();
                
                            if ($rule) {
                
                                if ($rule->km <= ($check_data->distance * 2)) {
                                    $driver_bata = ($day - 1) * 300;
                                } else {
                                    $driver_bata = 2100 * ($day - 1);
                                }
                
                            } else {
                                $driver_bata = 2100 * ($day - 1);
                            }
                        }
                    }else{
                        $toll_fare = (int)($check_data->toll_fare/2);
                    }
                    // dd($driver_bata, $distance);
                    
                    $base_fare = $fare + $driver_bata;
                    $com = round(($base_fare + $toll_fare) * 0.05);
                    $tax = round(($base_fare + $com) * 0.05);
                    
                    $data['com'] = $com;
                    $data['tax']         = $tax;
                    $data['toll_fare']   = $toll_fare;
                    $data['without_tax'] = 0;
                    $data['fare']        = round($base_fare + $toll_fare + $com + $tax);
                    $data['base_fare']        = $base_fare;
                    $data['discount']         = 0;
                    $data['isDiscount']         = 'no';
                    
                    $cabNameMap = [
                        'mini_four_seater' => 'Go Mini',
                        'four_seater'      => 'Go 4Seater',
                        'six_seater'       => 'Go 6Seater',
                        'seven_seater'  => 'Go 7Seater',
                    ];
                    
                    $u_details = [
                        'name'       => $request->c_name,
                        'email'      => $request->c_email,
                        'pass_count' => $request->pass_count,
                        'lugg_count' => $request->lugg_count,
                        'cab_type'   => $cabNameMap[$request->cab_type] ?? 'Unknown',
                        'mobile'     => $request->c_mobile,
                        'perKm' => $perKm
                    ];

                
                    $data['user_details'] = json_encode($u_details);
                    
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Location not found.'
                    ]);
                }
                $hash = hash_hmac(
                    'sha256',
                    $job_no . 'NEW_BOOKING' . $data['c_mobile'],
                    config('app.key') // secret
                );
                
                
                do {
                    $shortCode = env('SHORT_SLUG').Str::random(8);
                } while (
                    DB::table('cus_job_temp')
                        ->where('short_hash', $shortCode)
                        ->exists()
                );
                
                $data['preview_hash'] = $hash;
                $data['short_hash'] = $shortCode;
                
                unset(
                    $data['type'],
                    $data['dr_date'],
                    $data['isDriver'],
                    $data['bataRadio'],
                    $data['driver_id'],
                    $data['tollRadio'],
                    $data['driver_sc_id'],
                    $data['cab_type'],
                    $data['dropoff_date'],
                    $data['c_name'],
                    $data['c_email'],
                    $data['c_mobile'],
                    $data['toll'],
                    $data['lugg_count'],
                    $data['recaptcha_token'],
                    $data['from_place_id'],
                    $data['to_place_id'],
                    // $data['otp'],
                    $data['parkingRadio']
                );
                
                // $data['pick_lat'] = $data['pick_lat']?? '';
                // $data['pick_lng'] = $data['pick_lng']?? '';
                // $data['drop_lat'] = $data['drop_lat']?? '';
                // $data['drop_lng'] = $data['drop_lng']?? '';                   
                
                
                $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
                if ($create_job) {
                    
                    $this->jobOtpInsert($data['otp'], $create_job);
                    
                    unset(
                        $data['preview_hash'],
                        $data['otp']
                    );
                    
                    
                    $data['id'] = $create_job;
                    $data['poster_name'] = $request->name;
                    // $this->createFirebaseJob($job_no, $data);
                    
                    
                    $mobile     = $u_details['mobile'] ?? '';
                    $email      = $u_details['email'] ?? '';
                    $cab_type   = $u_details['cab_type'] ?? '';
                    $pass_count = $u_details['pass_count'] ?? '';
                    $lugg_count = $u_details['lugg_count'] ?? '';
                    $name       = $u_details['name'] ?? 'Customer';
                    
                    $pickup_date = Carbon::parse($data['pickup_date'])->format('d M Y h:i A');
                    $created_at = Carbon::parse($data['created_at'])->format('d M Y h:i A');
                    
                    $dropoff_date = null;
                    if ($data['job_type'] == 'roundtrip') {
                        $dropoff_date = $data['day'];
                    }
                    
                    $subject = 'Booking Information ' . $data['job_no'];
                    
                    $toll = $data['toll_fare'];
                    $base_fare = $data['base_fare'];
                    
                    $tot_fare = (int) $data['fare'];
                    
                    // $previewUrl = url('booking-information/' . $shortCode);
                    $previewUrl = env('PREVIEW_ENDPOINT') . $shortCode;
                    
                    $message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GoRide Booking Confirmation</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#f9bf00; padding:20px; text-align:center; color:#ffffff;">
                            <h2 style="margin:0;">GoRide – Booking Confirmation</h2>
                            <p style="margin:5px 0 0;">Job No: <strong>'.$data['job_no'].'</strong></p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:25px; color:#333333;">

                            <p><strong>Customer Name:</strong><br>
                            '.($data['poster_name'] ?? $name).'</p>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Trip Details</h3>
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td><strong>Pickup Location:</strong></td>
                                    <td>'.$data['from_place'].'</td>
                                </tr>
                                <tr>
                                    <td><strong>Drop Location:</strong></td>
                                    <td>'.$data['to_place'].'</td>
                                </tr>
                                <tr>
                                    <td><strong>Distance:</strong></td>
                                    <td>'.$data['distance'].' kms</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>'.$data['day'].'</td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Schedule</h3>
                            <p>
                                <strong>Pickup:</strong> '.$pickup_date.'<br>'
                                .($dropoff_date ? '<strong>Drop-off:</strong> '.$dropoff_date.'<br>' : '').'
                            </p>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Trip Information</h3>
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td><strong>Trip Type:</strong></td>
                                    <td>'.ucfirst($data['job_type']).'</td>
                                </tr>
                                <tr>
                                    <td><strong>Vehicle:</strong></td>
                                    <td>'.$cab_type.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Luggage:</strong></td>
                                    <td>'.$lugg_count.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Passengers:</strong></td>
                                    <td>'.$pass_count.'</td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Fare Summary</h3>

                            <p style="font-size:15px; margin:6px 0;">
                                <strong>Base Fare:</strong>
                                <span style="float:right;">₹'.$base_fare.'</span>
                            </p>
                            
                            <p style="font-size:15px; margin:6px 0;">
                                <strong>Govt. Levy / Extra:</strong>
                                <span style="float:right;">₹'.$toll.'</span>
                            </p>
                            
                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:10px 0;">
                            
                            <p style="font-size:18px; margin:6px 0;">
                                <strong>Estimated Fare:</strong>
                                <span style="color:#f9bf00; float:right;">₹'.$tot_fare.'</span>
                            </p>


                        </td>
                        
                    </tr>
                    
                    <tr>
                        <td style="padding:20px;">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f4f6f8; border-radius:10px; padding:20px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin:0 0 10px; font-size:14px; color:#374151;">
                                            View your complete booking details
                                        </p>
                    
                                        <a href="'. $previewUrl. '"
                                           target="_blank"
                                           style="
                                               display:inline-block;
                                               background:#2563eb;
                                               color:#ffffff;
                                               padding:12px 24px;
                                               border-radius:8px;
                                               font-size:15px;
                                               font-weight:600;
                                               text-decoration:none;">
                                            View Booking Preview
                                        </a>
                    
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>


                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f3f5; padding:15px; text-align:center; font-size:13px; color:#666;">
                            Thank you for choosing <strong>GoRide</strong>.<br>
                            For any assistance, feel free to contact us.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';
                    
                    $sentsms = Controller::composeEmail(
                        $request->ip(),
                        $email,
                        $subject,
                        $message,
                        '',
                        []
                    );
                    
                    $userId = 0;
                    
                    $link = env('ADMIN_ENDPOINT')."website-booking/";
                    $title = "New Job Lead from — ". $name;
                    
                    // data payload
                    $data_n = [
                        'user_id' => $userId,
                        'user_name' => $name,
                        'status' => 'pending',
                        'changes' => null,
                    ];
                    
                    NotificationService::create('job.lead', $title, $data_n, $link, $userId);
                    
$message = "📢 *New Booking Alert!*

Hello GoRide Team,

A new ride request has been received. Please review the details below and assign a driver as soon as possible.

---
🗓 *Booking Details:*
• *Booking Date:* {$created_at}
• *Booking ID:* #{$job_no}
• *Customer Name:* {$name}
• *Pickup Date & Time:* {$pickup_date}

🔗 *Preview Link:* {$previewUrl}

Thank you,
*GoRide System*";

                    $mobilesss = [
                        // '919884557004',
                        // env('BOOK_NO_ONE'),
                        // env('BOOK_NO_TWO')
                        // '919094042940'
                    ];
                    
                    foreach ($mobilesss as $mobile) {
                        Controller::sendNotification([
                            'mobile'            => $mobile,
                            'templateName'      => 'national_draw_verification',
                            'language'          => 'en',
                            'templateBodyParam' => [],
                            'messages'          => $message,
                            'resend'            => false
                        ]);
                    }
                    
                    $shortPreviewUrl = $previewUrl;
                    
$wh_mess = "Hello {$name} 👋,

Thank you for your cab booking request! 🚖
We have received your details as below:

📍 *From:* {$data['from_place']}
📍 *To:* {$data['to_place']}
🗓 *Date & Time:* {$pickup_date}
";

if ($data['job_type'] != 'oneway') {
    $wh_mess .= "🗓 No. of Days : {$dropoff_date}\n";
}

$wh_mess .= "📞 *Mobile:* {$u_details['mobile']}

Our executive will contact you shortly with the complete booking details and confirmation link.
Booking Info:- {$shortPreviewUrl}

📲 Download our app now and get ₹" . env('CREDIT_POINT') . " FREE credits! 🎉

👉".env('CUSTOMER_APP')."
Thank you for choosing GoRide! 😊

Call Customer support : ".env('SUPPORT_MOBILE')." ";

                    if($u_details['mobile']){
                       Controller::sendNotification([
                            'mobile'            => '91'.$u_details['mobile'],
                            'templateName'      => 'national_draw_verification',
                            'language'          => 'en',
                            'templateBodyParam' => [],
                            'messages'          => $wh_mess,
                            'resend'            => false
                        ]); 
                    }

            
                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'jd' => $create_job,
                        'preview' => $previewUrl,
                        'message' => 'Job created successfully.'
                    ]);
                }
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
    
    public function generateTinyUrl($url)
    {
        try {
            $response = Http::get('https://tinyurl.com/api-create.php', [
                'url' => $url
            ]);
    
            return $response->successful() ? $response->body() : $url;
        } catch (\Exception $e) {
            return $url;
        }
    }
    
    public function AdminbookJourney(Request $request)
    {
        try {
    
            $request->validate([
                'job_type' => ['required', 'string', 'max:255'],
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date' => ['nullable'],
                'day' => ['nullable'],
                'pass_count' => ['required', 'string', 'max:255'],
                'fare' => ['required', 'numeric'],
                'distance' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'string', 'max:255'],
    
                'add_fare_details' => ['required', 'array'],
                'add_fare_details.bata' => ['required', 'string', 'max:255'],
                'add_fare_details.parking' => ['required', 'string', 'max:255'],
                'add_fare_details.toll' => ['required', 'string', 'max:255'],
    
                'type' => ['required', 'string', 'max:255'],
                'c_name' => ['required', 'string', 'max:255'],
                'c_email' => ['required', 'string', 'max:255'],
                'c_mobile' => ['required', 'string', 'max:255'],
                'isDriver' => ['nullable', 'string', 'max:255'],
            ]);
    
            $data = $request->all();
        
            $pickup = Carbon::parse($request->pickup_date);
            $now = Carbon::now();
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour(2))) 
            {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be at least 2 hour after the current time.'
                ]);
            }
    
            // $jobCount = DB::table('cus_job_temp')->count() + 1;
            // $job_no = "GRC-" . str_pad($jobCount, 3, '0', STR_PAD_LEFT);
            
            $maxAttempts = 5;

            for ($i = 0; $i < $maxAttempts; $i++) {
            
                // $job_no = 'GRC-' . strtoupper(bin2hex(random_bytes(4)));
                $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            
                if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            
                if ($i == $maxAttempts - 1) {
                    // throw new \Exception('Unable to generate unique job number.');
                    // goto HI;
                }
            }
    
            $data['job_no'] = $job_no;
            $data['global_type'] = 'customer';
            $data['user_id'] = 0;
    
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
            $data['pick_address'] = $data['pick_address'];
            $data['drop_address'] = $data['drop_address'];
    
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = $data['dropoff_date'];
    
            $data['created_at'] = now();
            $data['updated_at'] = now();
    
            if ($data['isDriver'] == 'no') 
            {
                
                $column = $request->cab_type;
                
                $check_data = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $data['from_place_id'],
                        'to_place_id'   => $data['to_place_id'],
                        'seater' => $column
                    ])->first();
                
                if ($check_data && !empty($data['pass_count'])) {

                    // $fare = ($data['job_type'] == 'roundtrip')
                    //     ? (float) $check_data->return_fare
                    //     : (float) $check_data->oneway_fare;
                        
                    $toll_fare = $check_data->toll_fare;
                    
                    // $fare = $data['job_type'] == 'roundtrip'
                    //     ? $check_data->return_fare
                    //     : $check_data->oneway_fare;
                        
                    $distance = $data['job_type'] == 'roundtrip'
                        ? ($check_data->distance * 2)
                        : $check_data->distance;
                        
                    
                    $seater = $request->cab_type;
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    if ($tariffs->isEmpty()) {
                         return response()->json([
                            'status' => false,
                            'data' => [],
                            'message' => 'Fare not found'
                        ]);
                    }
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $fare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($data['job_type'] == 'roundtrip') {
                        $fare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
                    
                    
                    
                    $driver_bata = 0;
                    $day = null;
                    
                    $p_date = Carbon::parse($data['pickup_date'])->format('Y-m-d');
    
                    if ($data['job_type'] == 'roundtrip' && $p_date) {
                        
                        // $d_date = Carbon::parse($data['dropoff_date'])->format('Y-m-d');
                        $d_date = $data['dropoff_date'];
                
                        // $day = Carbon::parse($p_date)->diffInDays(Carbon::parse($d_date)) + 1;
                        $day = $d_date;
                
                        if ($day > 1) {
                
                            $rule = DB::table('roundtrip_days')
                                ->where('day', $day)
                                ->first();
                
                            if ($rule) {
                
                                if ($rule->km <= ($check_data->distance * 2)) {
                                    $driver_bata = ($day - 1) * 300;
                                } else {
                                    $driver_bata = 2100 * ($day - 1);
                                }
                
                            } else {
                                $driver_bata = 2100 * ($day - 1);
                            }
                        }
                    }else{
                        $toll_fare = (int)($check_data->toll_fare/2);
                    }
                    // dd($driver_bata, $distance);
                    
                    $base_fare = $fare;
                
                    
                    $data['com'] = 0;
                    $data['tax']         = 0;
                    $data['toll_fare']   = $toll_fare;
                    $data['without_tax'] = round($base_fare);
                    $data['fare']        = round($base_fare + $driver_bata + $toll_fare);
                    $data['base_fare']        = $base_fare;
                    $data['discount']         = 0;
                    $data['isDiscount']         = 'no';
                    
                    $cabNameMap = [
                        // 'mini_four_seater' => 'Go Mini',
                        'four_seater'      => 'Go Sedan',
                        'seven_seater'       => 'Go SUV',
                        // 'onethree_seater'  => 'Go SUV+',
                    ];
                    
                    $u_details = [
                        'name'       => $request->c_name,
                        'email'      => $request->c_email,
                        'pass_count' => $request->pass_count,
                        'lugg_count' => $request->lugg_count,
                        'cab_type'   => $cabNameMap[$request->cab_type] ?? 'Unknown',
                        'mobile'     => $request->c_mobile,
                        'perKm' => $perKm
                    ];

                
                    $data['user_details'] = json_encode($u_details);
                    
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Location not found.'
                    ]);
                }
                $hash = hash_hmac(
                    'sha256',
                    $job_no . 'NEW_BOOKING' . $data['c_mobile'],
                    config('app.key') // secret
                );
                
                $data['preview_hash'] = $hash;
                
                unset(
                    $data['type'],
                    $data['dr_date'],
                    $data['isDriver'],
                    $data['bataRadio'],
                    $data['driver_id'],
                    $data['tollRadio'],
                    $data['driver_sc_id'],
                    $data['cab_type'],
                    $data['dropoff_date'],
                    $data['c_name'],
                    $data['c_email'],
                    $data['c_mobile'],
                    $data['toll'],
                    $data['lugg_count'],
                    $data['recaptcha_token'],
                    $data['from_place_id'],
                    $data['to_place_id'],
                    $data['parkingRadio']
                );
                
                // $data['pick_lat'] = $data['pick_lat']?? '';
                // $data['pick_lng'] = $data['pick_lng']?? '';
                // $data['drop_lat'] = $data['drop_lat']?? '';
                // $data['drop_lng'] = $data['drop_lng']?? '';
                
                
                $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
                if ($create_job) {
                    
                    unset(
                        $data['preview_hash']
                    );
                    
                    $data['id'] = $create_job;
                    $data['poster_name'] = $request->name;
                    // $this->createFirebaseJob($job_no, $data);
                    
                    
                    $mobile     = $u_details['mobile'] ?? '';
                    $email      = $u_details['email'] ?? '';
                    $cab_type   = $u_details['cab_type'] ?? '';
                    $pass_count = $u_details['pass_count'] ?? '';
                    $lugg_count = $u_details['lugg_count'] ?? '';
                    $name       = $u_details['name'] ?? 'Customer';
                    
                    $pickup_date = Carbon::parse($data['pickup_date'])->format('d M Y h:i A');
                    
                    $dropoff_date = null;
                    if ($data['job_type'] == 'roundtrip') {
                        $dropoff_date = $data['day'];
                    }
                    
                    $subject = 'Booking Information ' . $data['job_no'];
                    
                    $toll = $data['toll_fare'];
                    $base_fare = $data['base_fare'];
                    
                    $tot_fare = (int) $data['fare'];
                    
                    $previewUrl = url('booking-information/' . $hash);
                    
                    $message = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GoRide Booking Confirmation</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background:#f9bf00; padding:20px; text-align:center; color:#ffffff;">
                            <h2 style="margin:0;">GoRide – Booking Confirmation</h2>
                            <p style="margin:5px 0 0;">Job No: <strong>'.$data['job_no'].'</strong></p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:25px; color:#333333;">

                            <p><strong>Customer Name:</strong><br>
                            '.($data['poster_name'] ?? $name).'</p>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Trip Details</h3>
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td><strong>Pickup Location:</strong></td>
                                    <td>'.$data['from_place'].'</td>
                                </tr>
                                <tr>
                                    <td><strong>Drop Location:</strong></td>
                                    <td>'.$data['to_place'].'</td>
                                </tr>
                                <tr>
                                    <td><strong>Distance:</strong></td>
                                    <td>'.$data['distance'].' kms</td>
                                </tr>
                                <tr>
                                    <td><strong>Duration:</strong></td>
                                    <td>'.$data['day'].'</td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Schedule</h3>
                            <p>
                                <strong>Pickup:</strong> '.$pickup_date.'<br>'
                                .($dropoff_date ? '<strong>Drop-off:</strong> '.$dropoff_date.'<br>' : '').'
                            </p>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Trip Information</h3>
                            <table width="100%" cellpadding="6" cellspacing="0">
                                <tr>
                                    <td><strong>Trip Type:</strong></td>
                                    <td>'.ucfirst($data['job_type']).'</td>
                                </tr>
                                <tr>
                                    <td><strong>Vehicle:</strong></td>
                                    <td>'.$cab_type.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Luggage:</strong></td>
                                    <td>'.$lugg_count.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Passengers:</strong></td>
                                    <td>'.$pass_count.'</td>
                                </tr>
                            </table>

                            <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">

                            <h3 style="margin-bottom:10px;">Fare Summary</h3>

                            <p style="font-size:15px; margin:6px 0;">
                                <strong>Base Fare:</strong>
                                <span style="float:right;">₹'.$base_fare.'</span>
                            </p>
                            
                            <p style="font-size:15px; margin:6px 0;">
                                <strong>Govt. Levy / Extra:</strong>
                                <span style="float:right;">₹'.$toll.'</span>
                            </p>
                            
                            <hr style="border:none; border-top:1px solid #e5e7eb; margin:10px 0;">
                            
                            <p style="font-size:18px; margin:6px 0;">
                                <strong>Estimated Fare:</strong>
                                <span style="color:#f9bf00; float:right;">₹'.$tot_fare.'</span>
                            </p>


                        </td>
                        
                    </tr>
                    
                    <tr>
                        <td style="padding:20px;">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f4f6f8; border-radius:10px; padding:20px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin:0 0 10px; font-size:14px; color:#374151;">
                                            View your complete booking details
                                        </p>
                    
                                        <a href="'. $previewUrl. '"
                                           target="_blank"
                                           style="
                                               display:inline-block;
                                               background:#2563eb;
                                               color:#ffffff;
                                               padding:12px 24px;
                                               border-radius:8px;
                                               font-size:15px;
                                               font-weight:600;
                                               text-decoration:none;">
                                            View Booking Preview
                                        </a>
                    
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>


                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f3f5; padding:15px; text-align:center; font-size:13px; color:#666;">
                            Thank you for choosing <strong>GoRide</strong>.<br>
                            For any assistance, feel free to contact us.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';
                    
                    $sentsms = Controller::composeEmail(
                        $request->ip(),
                        $email,
                        $subject,
                        $message,
                        '',
                        []
                    );

            
                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'jd' => $create_job,
                        'preview' => $hash,
                        'message' => 'Job created successfully.'
                    ]);
                }
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
    
    public function postJob(Request $request)
    {
        $validated = $request->validate([
            'job_id'  => ['required'],
            'user_id' => ['required'],
        ]);
    
        // Fetch job
        DB::table('cus_job_temp')->where(['id' => $validated['job_id'], 'user_id' => '0'])->update(['user_id' => $validated['user_id']]);
        
        $jobTemp = DB::table('cus_job_temp')->where('id', $validated['job_id'])->first();
        
        if (!$jobTemp) {
            return response()->json([
                'status' => false,
                'message' => 'Job not found.'
            ], 404);
        }
        
        // return $jobTemp;
    
        // Fetch user
        $user = DB::table('customer_register')->where('id', $validated['user_id'])->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }
    
        $data = (array) $jobTemp;
    
        unset(
            $data['trac_id'],
            $data['confirm_status'],
            $data['mock_amt'],
            $data['wallet_amt'],
            $data['grandtotal'],
            $data['fare_breakdown'],
            $data['pay_amt'],
            $data['bids_details'],
            $data['reject_count'],
            $data['job_remark'],
            $data['liked_users'],
            $data['cron_check_status'],
            $data['before_assign_id'],
            $data['assigned_to'],
            $data['assigned_by'],
            $data['assigned_count'],
            $data['deletes'],
            $data['comments'],
            $data['comments_status'],
            $data['whatsapp_log'],
            $data['wh_notify']
        );
    
        // Add extra data
        $data['poster_name'] = $user->name;
    
        // Firebase job creation
        $this->createFirebaseJob($data['job_no'], $data);
    
        // Get place (safe)
        $placeParts = array_map('trim', explode(',', $data['from_place'] ?? ''));
        $place = count($placeParts) >= 2 ? $placeParts[count($placeParts) - 2] : null;
    
        if ($place) {
            $fcmTokens = $this->getFcm(null, $place, $validated['user_id'], $jobTemp->pass_count);
    
            if (!empty($fcmTokens)) {
                dispatch(new \App\Jobs\SendJobNotificationJob(
                    [
                        'id'     => $data['id'],
                        'type'   => 'new_job_notification',
                        'pickup' => $data['pickup_date'],
                        'action' => 'agree_popup'
                    ],
                    $fcmTokens,
                    $this->serviceAccount,
                    $this->getAccessToken()
                ));
            }
        }
    
        return response()->json([
            'status'  => true,
            'data'    => $data['job_no'],
            'jd'      => $validated['job_id'],
            'message' => 'Job created successfully.'
        ]);
    }
    
    public function bookingPreview(Request $request, $key)
    {
        $currentTime = now();
    
        $job = DB::table('cus_job_temp as ct')
            ->leftJoin('user_register as ur', 'ur.id', '=', 'ct.assigned_to')
            ->leftJoin(DB::raw("
                (SELECT * FROM payment_history 
                 WHERE id IN (
                    SELECT MAX(id) FROM payment_history GROUP BY job_no
                 )
                ) as ph
            "), 'ph.job_no', '=', 'ct.job_no')
            ->leftJoin('kyc_details as kd', 'ur.id', '=', 'kd.user_id')
            ->select(
    
                'ct.*',
    
                DB::raw("
                    CASE
                        WHEN ct.job_status != 'accept' AND ct.isView = 0
                        THEN 'XXX-XXX-XXXX'
    
                        WHEN ct.isView = 0 
                             AND ct.pickup_date > DATE_ADD('{$currentTime}', INTERVAL 1 HOUR)
                        THEN ur.mobile
    
                        ELSE ur.mobile
                    END as mobile
                "),
                DB::raw("
                    COALESCE(ur.profile_img_url, kd.selfie_url) as profile_img_url
                "),
    
                DB::raw("
                    JSON_UNQUOTE(JSON_EXTRACT(ur.vehicle_details,'$.rc_number'))
                    as rc_number
                "),
    
                'ur.name',
                'ph.gateway',
    
                DB::raw("
                    JSON_UNQUOTE(JSON_EXTRACT(ur.vehicle_details,'$.vehicle'))
                    as vehicle_image
                ")
            )
            ->where('ct.preview_hash', $key)
            ->first();
    
        if (!$job) {
            abort(404, 'Booking not found');
        }
        
        // dd($job);
    
        $userDetails   = json_decode($job->user_details ?? '', true) ?: [];
        $fareBreakdown = json_decode($job->fare_breakdown ?? '', true) ?: [];
    
        $isFromCustomerRegister = false;
    
        $name   = $userDetails['name'] ?? '';
        $mobile = $userDetails['mobile'] ?? '';
        $email  = $userDetails['email'] ?? '';
    
        if (strtolower(trim($name)) === 'customer' || strtolower(trim($name)) === 'not provided') {
            $name = '';
        }
    
        if (strtolower(trim($mobile)) === 'not provided') {
            $mobile = '';
        }
    
        if (strtolower(trim($email)) === 'not provided') {
            $email = '';
        }
    
        if (!empty($job->user_id) && $job->user_id != 0) {
    
            if (strpos($job->job_no, 'GRC') === 0) {
    
                $customer = DB::table('customer_register')
                    ->where('id', $job->user_id)
                    ->where('deletes', '0')
                    ->first();
    
                if ($customer) {
    
                    $isFromCustomerRegister = true;
    
                    $name   = !empty($name) ? $name : $customer->name;
                    $mobile = !empty($mobile) ? $mobile : $customer->mobile;
                    $email  = !empty($email) ? $email : $customer->email;
                }
    
            } else {
    
                $userReg = DB::table('user_register')
                    ->where('id', $job->user_id)
                    ->where('deletes', '0')
                    ->first();
    
                if ($userReg) {
    
                    $name   = !empty($name) ? $name : $userReg->name;
                    $mobile = !empty($mobile) ? $mobile : $userReg->mobile;
                    $email  = !empty($email) ? $email : $userReg->email;
                }
            }
        }
    
        $name   = $name ?: 'Customer';
        $mobile = $mobile ?: 'Not Provided';
        $email  = $email ?: 'Not Provided';
    
        $cabType = $userDetails['cab_type'] ?? null;
    
        if (strtolower(trim($cabType)) === 'not provided') {
            $cabType = '';
        }
    
        $passCount = $userDetails['pass_count'] ?? $job->pass_count ?? '';
        $luggCount = $userDetails['lugg_count'] ?? $job->lugg_count ?? '';
        
    
        if (empty($cabType)) {
    
            $pCount = $passCount;
    
            if ($pCount > 0) {
    
                if ($pCount == 'mini') {
                    $cabType = 'Go Mini';
                } elseif ($pCount == 5) {
                    $cabType = 'Go 4Seaters';
                } elseif ($pCount == 6) {
                    $cabType = 'Go 5Seaters';
                } elseif ($pCount == 7) {
                    $cabType = 'Go 6Seaters';
                } elseif ($pCount >= 8) {
                    $cabType = 'Go 7Seaters';
                }
    
            } else {
                $cabType = 'Go 4Seaters';
            }
        }
    
        if ($isFromCustomerRegister) {
            $passCount = '';
            $luggCount = '';
        }
    
        $status = 'Pending';
        $now = now()->timezone('Asia/Kolkata');
        $pickupDate = Carbon::parse($job->pickup_date)->timezone('Asia/Kolkata');
    
        if ($job->job_status == 'accept' && $pickupDate > $now) {
            $status = 'Confirmed';
        }else if($pickupDate < $now && $job->job_status == 'accept'){
            $status = 'Completed';
        }else if($job->job_status == 'created' || $job->job_status == 'bidding'){
            $status = 'Pending';
        }else{
            $status = ucfirst($job->job_status);
        }
    
    
        $pickupDate = null;
    
        if (!empty($job->pickup_date)) {
    
            try {
                $pickupDate = \Carbon\Carbon::parse($job->pickup_date)
                    ->format('d M Y, h:i A');
            } catch (\Exception $e) {
                $pickupDate = $job->pickup_date;
            }
        }
    
        $baseFare   = array_key_exists('base_fare', $fareBreakdown) ? $fareBreakdown['base_fare'] : (int) ($job->base_fare ?? 0);
        $tollFare   = array_key_exists('toll_fare', $fareBreakdown) ? $fareBreakdown['toll_fare'] : (int) ($job->toll_fare ?? 0);
        $tax        = array_key_exists('tax', $fareBreakdown) ? $fareBreakdown['tax'] : (int) ($job->tax ?? 0);
        $commission = array_key_exists('com', $fareBreakdown) ? $fareBreakdown['com'] : (int) ($job->com ?? 0);
        $discount   = array_key_exists('discount', $fareBreakdown) ? $fareBreakdown['discount'] : (int) ($job->discount ?? 0);
        $isDiscount   = array_key_exists('isDiscount', $fareBreakdown) ? $fareBreakdown['isDiscount'] : ($job->isDiscount ?? '');
        
        // dd($isDiscount);
        
        $b_amt = 0;
        $paid_on = 0;
        $totalFare = 0;
        $paid_wallet = 0;
        
        if($job->job_status == 'created' || $job->job_status == 'bidding' || $job->job_status == 'schedule'){
            $totalFare = array_key_exists('total_fare', $fareBreakdown) ? $fareBreakdown['total_fare'] : $job->fare;
        }else{
            $totalFare  = $job->fare??0;
        }
        
        
        $vehicleImages = [];

        if (!empty($job->vehicle_image)) {
        
            $vehicleDetails = json_decode($job->vehicle_image, true);
        
            // if (!empty($vehicleDetails['vehicle'])) {
        
            //     $vehicle = $vehicleDetails['vehicle'];
        
            // }
            foreach ($vehicleDetails as $key => $value) {
    
                if (str_contains($key, '_image_url') && !empty($value)) {
                    $vehicleImages[] = $value;
                }
    
            }
        }
        
        if(array_key_exists('total_fare', $fareBreakdown) && $fareBreakdown['total_fare'] == $job->pay_amt && $job->deductAmt == 0){
            $b_amt = 0;
        }else if($job->deductAmt != 0){
            $b_amt = $fareBreakdown['pay_to_driver'];
            
        }else{
            $b_amt = $baseFare + $tollFare;
        }
        
        $paid_on = $job->fare;
        $credit_bonus = $isDiscount == 'yes' ? $discount : 0;
        
        if($job->gateway && $job->gateway == 'wallet'){
            $paid_on = 0;
            $paid_wallet = $job->pay_amt;
        }else if($job->gateway && $job->gateway != 'wallet'){
            $paid_on = $job->pay_amt;
            $paid_wallet = $job->wallet_amt;
        }
        
        if($job->payment_status == 'pending' && $job->deductAmt == 0){
            $paid_on = 0;
            $paid_wallet = 0;
            $b_amt = 0;
        }
        
        
        if($job->job_status == 'created' || $job->job_status == 'bidding'){
            $b_amt = 0;
            $paid_on = 0;
            $paid_wallet = 0;
            // $credit_bonus = 0;
            $b_amt = 0;
        }
        
        // dd($job);
    
        return view('pages.booking-preview', [
    
            'job_no'       => $job->job_no,
            'from_place'   => $job->from_place,
            'to_place'     => $job->to_place,
            'pickup_date'  => $pickupDate,
            'job_type'     => $job->job_type,
            'day'          => $job->day,
    
            'name'         => $name,
            'mobile'       => $mobile,
            'email'        => $email,
    
            'cab_type'     => $cabType,
            'pass_count'   => $passCount,
            'lugg_count'   => $luggCount,
            'perKm'        => $userDetails['perKm'] ?? '',
            'distance'     => $job->distance,
    
            'pick_address' => $job->pick_address,
            'drop_address' => $job->drop_address,
            'user_details' => $job->user_details,
    
            'job_status'   => $status,
            
            'actual_base' => $baseFare + $commission,
            'base_fare'    => $isDiscount == 'yes' ? ($baseFare + $commission) - $discount : ($baseFare + $commission),
            'govt_levy'    => $tollFare,
            'tax'          => $tax,
            'com'          => $commission,
            'discount'     => $discount,
            'total_fare'   => $totalFare,
            'isPaid'   => $job->payment_status,
            'paid_amt' => $paid_on,
            'wallet_amt' => $paid_wallet,
            'credit_bonus' => $credit_bonus,
            'balance_amt' => $b_amt,
            'isDiscount' => $isDiscount,
            'deductAmt' => $job->deductAmt,
            // driver details for your new card
            'driver_name'   => $job->name ?? '',
            'driver_image'   => $job->profile_img_url ?? '',
            'driver_mobile' => $job->mobile ?? '',
            'vehicle_number'=> $job->rc_number ?? '',
            'vehicle_images' => $vehicleImages ?? [],
            'gateway' => $job->gateway??null,
            'isPayment' => ($job->gateway == 'cash' || $job->payment_status == 'paid') ? true : false
        ]);
    }
    
    private function getDistanceOnly($fromLat, $fromLng, $toLat, $toLng)
    {
        $res = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins'      => "$fromLat,$fromLng",
            'destinations' => "$toLat,$toLng",
            'key'          => env('DISTANCE_GOOGLE_KEY'),
            'mode'         => 'driving',
            'units'        => 'metric'
        ])->json();
    
        if (
            !isset($res['rows'][0]['elements'][0]['distance']['value']) ||
            $res['rows'][0]['elements'][0]['status'] !== 'OK'
        ) {
            return null;
        }
    
        return [
            'distance' => $res['rows'][0]['elements'][0]['distance']['value'], // meters
            'duration' => $res['rows'][0]['elements'][0]['duration']['text']
        ];
    }
    
    private function updateGeo($from, $to)
    {
        $apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6IjVkODk3M2U0YTQ1ZDQyMGFhMjExYTU1YmU2ZGFlZGM3IiwiaCI6Im11cm11cjY0In0=";
    
        $originResponse = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->get('https://api.openrouteservice.org/geocode/search', [
            'api_key' => $apiKey,
            'text' => $from,
        ]);
    
        $destResponse = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->get('https://api.openrouteservice.org/geocode/search', [
            'api_key' => $apiKey,
            'text' => $to,
        ]);
    
        $originCoords = $originResponse->json()['features'][0]['geometry']['coordinates'] ?? null;
        $destCoords   = $destResponse->json()['features'][0]['geometry']['coordinates'] ?? null;
    
        if (!$originCoords || !$destCoords) {
            return null;
        }
    
        return [
            'from_lat' => $originCoords[1],
            'from_lng' => $originCoords[0],
            'to_lat'   => $destCoords[1],
            'to_lng'   => $destCoords[0],
        ];
    }
    
    private function applyFareLogic_cp($fare, $toll, $cashPoints, $distance, $duration, $perKm)
    {
        $commission = round($fare * 0.05);
        $base = ($fare - $toll) + $commission;
    
        $discount = round($base * 0.05);
        $isDiscount = $discount <= $cashPoints;
    
        if ($isDiscount) {
            $base -= $discount;
        }
    
        $tax = round($base * 0.05);
    
        return [
            'distance'     => $distance,
            'duration'     => $duration,
            'fare'         => round($base),
            // 'without_toll_tax'  => round($base - $toll),
            // 'without_tax'  => round($base + $toll),
            'toll_fare'    => round($toll),
            'tax'          => $tax,
            // 'discount'     => $discount,
            // 'isDiscount'   => $isDiscount ? 'yes' : 'no',
            // 'com'   => $commission,
            'per_km' => $perKm
        ];
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
    
    public function getSeaters(Request $request)
    {
        
        $data = DB::table('cabs')->where(['status' => 1, 'deletes' => 0])
                ->select(
                    'id',
                    'name',
                    'image_url as vehicle_image',
                    DB::raw("CONCAT(seats - 1, '+1') as active_seat"),
                    'seats as seater'
                )
                ->get();
        
    
        return response()->json([
            'status'  => 'success',
            'message' => 'Seaters records retrieved successfully',
            'data'    => $data,
        ], 200);
    }
    
    public function cheap_price($fromWords, array $dates)
    {
        return collect($dates)->map(function ($date) use ($fromWords) {
    
            $drivers = $this->getDriversByDate($fromWords, $date);
    
            return [
                'date'           => $date,
                // 'driver_count'   => $drivers->count(),
                'cheapest_price' => $drivers->isNotEmpty()
                    ? (float) $drivers->first()->price
                    : null,
                // 'drivers'        => $drivers,
            ];
        })->values();
    }
    
    public function jobOtpInsert($otp, $jobId)
    {
        try {
    
            if (empty($otp) || empty($jobId)) {
                \Log::info('OTP insert log...: ', [$otp]);
                return false;
            }

            $otpId = DB::table('job_start_otps')->insertGetId([
                'job_id'        => $jobId,
                'otp'           => $otp,
                // 'expires_at'    => Carbon::now()->addMinutes(5),
                'expires_at'    => null,
                'attempts'      => 0,
                'max_attempts'  => 5,
                'verified_at'   => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
    
            return true;
    
        } catch (\Exception $e) {
            
            \Log::info('OTP insert log...: ', [$e->getMessage()]);
    
            return false;
        }
    }

    
    public function create_job(Request $request)
    {
        try {
    
            $request->validate([
                'job_type' => ['required', 'string', 'max:255'],
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date' => ['nullable'],
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
            
            $u_cash_point = $user->cash_points;
    
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
            
            // Validate functions
            $firstJob = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('deletes', '0')
                ->where('job_status', 'created')
                ->orderBy('created_at', 'asc')
                ->first();
                
            // return $firstJob;
            $pickupDate = Carbon::parse($request->pickup_date)->toDateString();
        
            if ($firstJob) {
        
                $firstJobTime = Carbon::parse($firstJob->created_at);
                $windowEnd    = $firstJobTime->copy()->addHour();
                $now          = Carbon::now();
        
                if ($now->lessThan($windowEnd)) {
        
                //     $jobsInWindow = DB::table('cus_job_temp')
                //         ->where('user_id', $userId)
                //         ->where('deletes', '0')
                //         ->where('job_status', 'created')
                //         ->whereBetween('created_at', [$firstJobTime, $windowEnd])
                //         ->count();
        
                //     if ($jobsInWindow >= 2) {
        
                //         $minutesLeft = $now->diffInMinutes($windowEnd);
        
                //         DB::rollBack();
        
                //         return response()->json([
                //             'status' => false,
                //             'data' => [],
                //             'message' => "You already created 2 active jobs. Try again after {$minutesLeft} minutes."
                //         ], 422);
                //     }
                
                    $jobsInWindow = DB::table('cus_job_temp')
                        ->where('user_id', $userId)
                        ->where('deletes', '0')
                        ->where('job_status', 'created')
                        ->whereBetween('created_at', [$firstJobTime, $windowEnd])
                        ->pluck('job_no');   // get job numbers
            
                    if ($jobsInWindow->count() >= 2) {
            
                        $minutesLeft = $now->diffInMinutes($windowEnd);
            
                        DB::rollBack();
            
                        // return response()->json([
                        //     'status' => false,
                        //     'data' => [
                        //         'job_no' => $jobsInWindow->values()
                        //     ],
                        //     'message' => "You already created active jobs. Try again after {$minutesLeft} minutes."
                        // ], 422);
                    }
                }
                
                
            }
        
            /*
            |--------------------------------------------------------------------------
            | RULE 2 : Same Pickup Date Conflict
            |--------------------------------------------------------------------------
            */
        
            // $samePickupExists = DB::table('cus_job_temp')
            //     ->where('user_id', $userId)
            //     ->where('deletes', '0')
            //     ->where('job_status', 'created')
            //     ->whereDate('pickup_date', $pickupDate)
            //     ->exists();
        
            // if ($samePickupExists) {
        
            //     DB::rollBack();
        
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'You already have an active booking for this pickup date. Please choose another date.'
            //     ], 422);
            // }
            
            $samePickupJobs = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('deletes', '0')
                ->where('job_status', 'created')
                ->whereDate('pickup_date', $pickupDate)
                ->pluck('job_no');   // get job numbers
            
            if ($samePickupJobs->isNotEmpty()) {
            
                DB::rollBack();
            
                return response()->json([
                    'status' => false,
                    'data' => [
                        'job_no' => $samePickupJobs->values()
                    ],
                    'message' => 'You already have an active booking for this pickup date. Please choose another date.'
                ], 422);
            }
            
            // HI:
            $maxAttempts = 5;

            for ($i = 0; $i < $maxAttempts; $i++) {
            
                // $job_no = 'GRC-' . strtoupper(bin2hex(random_bytes(4)));
                $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            
                if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            
                if ($i == $maxAttempts - 1) {
                    // throw new \Exception('Unable to generate unique job number.');
                    // goto HI;
                }
            }
    
            $data['job_no'] = $job_no;
            $data['global_type'] = 'customer';
            $data['user_id'] = $userId;
    
            // Convert fare details to JSON
            $data['add_fare_details'] = json_encode($data['add_fare_details']);
    
            // Clean date fields
            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            // $data['dropoff_date'] = !empty($data['dropoff_date']) ? date("Y-m-d", strtotime($data['dropoff_date'])) : null;
            $data['dropoff_date'] = $data['dropoff_date'];
            
            $journeyType = $request->job_type;
    
            $data['created_at'] = now();
            $data['updated_at'] = now();
            
            $otp = Controller::generateOTP(6);
            
            $data['otp'] = $otp;
    
            if ($data['isDriver'] == 'yes') 
            {
                
                if($request->pass_count == 'mini'){
                    $seater = 'mini_four_seater';
                }elseif($request->pass_count == 5 || $request->pass_count == 4){
                    $seater = 'four_seater';
                }else if($request->pass_count == 7){
                    $seater = 'six_seater';
                }else if($request->pass_count == 8){
                    $seater = 'seven_seater';
                }
                
                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $data['from_place_id'],
                        'to_place_id'   => $data['to_place_id'],
                        'seater'        => $seater,
                    ])->first();
                    
                // return $cached;
                    
                if ($cached) {
                    
                    // $baseFare = $request->base_fare;
                    $baseFare = $cached->oneway_fare;
                        
                    $distance = $journeyType == 'roundtrip'
                        // ? ($cached->distance * 2)
                        ? $cached->distance
                        : $cached->distance;
                        
                    $isBelowDis = ($distance < 50) ? true : false;
                        
                    $s_arr = [$seater, $seater.'_round'];
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $baseFare   = $baseFare;
                    $baseFare_r = $baseFare * 2;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    // if ($journeyType == 'roundtrip') {
                    //     $baseFare = $baseFare_r;
                    //     $perKm = $perKmRound;
                    // }
                    
                    $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
                    $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
                    
                    // $seaters[] = $seater;
                    // \Log::info('Testing schedule...: ', [$cached, $baseFare]);
    
                   $fareData = $this->applyFareLogic_App(
                        $baseFare,
                        $cached->toll_fare,
                        0,
                        $distance,
                        $cached->duration,
                        $perKm,
                        $journeyType,
                        $data['pickup_date'],
                        $data['dropoff_date'],
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng'],
                        $seater,
                        $isBelowDis
                    );
                    
                    // \Log::info('Testing schedule...: ', [$fareData, $cached, $baseFare]);
                    
                    // dd('hii');
                
                    $data = array_merge($data, $fareData);
                    
                    $from_to = [
                        'from_lat' => $fromGeo['lat'],
                        'from_lng' => $fromGeo['lng'],
                        'to_lat' => $toGeo['lat'],
                        'to_lng' => $toGeo['lng'],
                    ];
                    
                    $data['from_to_co'] = json_encode($from_to);
    
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Route not found.'
                    ]);
                }
                
                
                
                unset(
                    $data['type'],
                    $data['dr_date'],
                    $data['isDriver'],
                    $data['bataRadio'],
                    $data['driver_id'],
                    $data['tollRadio'],
                    $data['driver_sc_id'],
                    $data['without_tax'],
                    $data['to_lng'],
                    $data['to_lat'],
                    $data['from_lng'],
                    $data['from_lat'],
                    $data['per_km'],
                    $data['inc_km'],
                    
                    $data['parkingRadio']
                );
                
                $data['pick_address']   = '';
                $data['drop_address']   = '';
                $data['dropoff_date']   = null;
                $data['user_details']   = null;
                
                // return $data;
                
                $hash = hash_hmac(
                    'sha256',
                    $job_no . 'NEW_BOOKING' . $user->mobile,
                    config('app.key')
                );
                
                $data['preview_hash'] = $hash;
                
                do {
                    $shortCode = env('SHORT_SLUG').Str::random(8);
                } while (
                    DB::table('cus_job_temp')
                        ->where('short_hash', $shortCode)
                        ->exists()
                );
                
                $data['short_hash'] = $shortCode;
                
                $previewUrl = env('PREVIEW_ENDPOINT') . $shortCode;
                
                // $data['job_status'] = 'schedule';
                $data['job_status'] = 'created';
                $data['global_type'] = 'schedule';

                $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
                if ($create_job) {
                    
                    $this->jobOtpInsert($otp, $create_job);
                    
                    $data['id'] = $create_job;
                    $data['poster_name'] = auth()->user()->name;
                    
                    $this->createFirebaseJob2($job_no, $data);
                    
                    unset(
                        $data['preview_hash'],
                        $data['otp'],
                    );
                    
                    $pickup_date = Carbon::parse($data['pickup_date'])->format('d M Y h:i A');
                    $created_at = Carbon::parse($data['created_at'])->format('d M Y h:i A');
                    $name = $data['poster_name'];
                    
$message = "📢 *New Booking Alert from Customer App - Schedule!*

Hello GoRide Team,

A new ride request has been received. Please review the details below and assign a driver as soon as possible.

---
🗓 *Booking Details:*
• *Booking Date:* {$created_at}
• *Booking ID:* #{$job_no}
• *Customer Name:* {$name}
• *Pickup Date & Time:* {$pickup_date}

🔗 *Preview Link:* {$previewUrl}

Thank you,
*GoRide System*";

                    $mobilesss = [
                        // '919884557004',
                        // env('BOOK_NO_ONE'),
                        // env('BOOK_NO_TWO')
                        // '919094042940'
                    ];
                    
                    foreach ($mobilesss as $mobile) {
                        Controller::sendNotification([
                            'mobile'            => $mobile,
                            'templateName'      => 'national_draw_verification',
                            'language'          => 'en',
                            'templateBodyParam' => [],
                            'messages'          => $message,
                            'resend'            => false
                        ]);
                    }
                    
            
                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'job_id' => $create_job,
                        'message' => 'Job created successfully.'
                    ]);
                }
            }
            
            if ($data['isDriver'] == 'no') 
            {
                
                if($request->pass_count == 'mini'){
                    $seater = 'mini_four_seater';
                }elseif($request->pass_count == 5){
                    $seater = 'four_seater';
                }else if($request->pass_count == 7){
                    $seater = 'six_seater';
                }else if($request->pass_count == 8){
                    $seater = 'seven_seater';
                }
                
                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $data['from_place_id'],
                        'to_place_id'   => $data['to_place_id'],
                        'seater'        => $seater,
                    ])->first();
                    
                
                    
                if ($cached) {
                    
                    $baseFare = $journeyType == 'roundtrip'
                        ? $cached->return_fare
                        : $cached->oneway_fare;
                        
                        
                    $distance = $journeyType == 'roundtrip'
                        ? ($cached->distance * 2)
                        : $cached->distance;
                        
                    $isBelowDis = ($distance < 50) ? true : false;
                        
                    $s_arr = [$seater, $seater.'_round'];
    
                    $tariffs = DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function($query) use ($seater) {
                            $query->where($seater, '>', 0)
                                  ->orWhere($seater.'_round', '>', 0);
                        })
                        ->get();
                    
                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater.'_round', '>', 0);
                    
                    $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater.'_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);
                    
                    if ($journeyType == 'roundtrip') {
                        $baseFare = $baseFare_r;
                        $perKm = $perKmRound;
                    }
                    
                    $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
                    $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
                    
                    // $seaters[] = $seater;
    
                   $fareData = $this->applyFareLogic_App(
                        $baseFare,
                        $cached->toll_fare,
                        0,
                        $distance,
                        $cached->duration,
                        $perKm,
                        $journeyType,
                        $data['pickup_date'],
                        $data['dropoff_date'],
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng'],
                        $seater,
                        $isBelowDis
                    );
                
                    $data = array_merge($data, $fareData);
                    
                    $from_to = [
                        'from_lat' => $fromGeo['lat'],
                        'from_lng' => $fromGeo['lng'],
                        'to_lat' => $toGeo['lat'],
                        'to_lng' => $toGeo['lng'],
                    ];
                    
                    $data['from_to_co'] = json_encode($from_to);
    
                }else{
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Route not found.'
                    ]);
                }
                
                
                
                unset(
                    $data['type'],
                    $data['dr_date'],
                    $data['isDriver'],
                    $data['bataRadio'],
                    $data['driver_id'],
                    $data['tollRadio'],
                    $data['driver_sc_id'],
                    $data['without_tax'],
                    $data['to_lng'],
                    $data['to_lat'],
                    $data['from_lng'],
                    $data['from_lat'],
                    $data['per_km'],
                    $data['inc_km'],
                    // $data['otp'],
                    $data['parkingRadio']
                );
                
                $data['pick_address']   = '';
                $data['drop_address']   = '';
                $data['dropoff_date']   = null;
                $data['user_details']   = null;
                
                // return $data;
                
                $hash = hash_hmac(
                    'sha256',
                    $job_no . 'NEW_BOOKING' . $user->mobile,
                    config('app.key') // secret
                );
                
                $data['preview_hash'] = $hash;
                
                do {
                    $shortCode = env('SHORT_SLUG').Str::random(8);
                } while (
                    DB::table('cus_job_temp')
                        ->where('short_hash', $shortCode)
                        ->exists()
                );
                
                $data['short_hash'] = $shortCode;
                
                $previewUrl = env('PREVIEW_ENDPOINT') . $shortCode;
                
                $get_lat = DB::table('outstation_locations')->where('place_id', $data['from_place_id'])->select('latitude', 'longitude')->first();
                
                $fcmTokens = [];
                
                if($get_lat){
                    $drivers = $this->getNearbyFcm(
                        $get_lat->latitude,
                        $get_lat->longitude,
                        env('NOTIFICATION_LIMIT'),
                        $request->pass_count
                    );
                    
                    $fcmTokens = $drivers->pluck('fcm_token')->toArray();
                    $driverIds = $drivers->pluck('id')->toArray();
                    
                }
                
                if (empty($fcmTokens)) {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'No driver available for ' . $data['from_place'] . ' location.'
                    ]);
                }
                
            
                $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
                if ($create_job) {
                    
                    $this->jobOtpInsert($otp, $create_job);
                    
                    $data['id'] = $create_job;
                    $data['poster_name'] = auth()->user()->name;
                    
                    unset(
                        $data['preview_hash'],
                        $data['otp']
                    );
                    
                    $this->createFirebaseJob($job_no, $data);
            
                    $place = collect(explode(',', $request->from_place))->map('trim')->get(-2);
                    // $fcmTokens = $this->getFcm(null, $place, null, $request->pass);
                    
                    
                    
            
                    if (!empty($fcmTokens)) {
                        dispatch(new \App\Jobs\SendJobNotificationJob(
                            [
                                'id'  => $create_job,
                                'type'    => 'new_job_notification',
                                'pickup'  => $data['pickup_date'],
                                'action'  => 'agree_popup'
                            ],
                            $fcmTokens,
                            $this->serviceAccount,
                            $this->getAccessToken(),
                            $driverIds
                        ));
                    }
                    
                    $pickup_date = Carbon::parse($data['pickup_date'])->format('d M Y h:i A');
                    $created_at = Carbon::parse($data['created_at'])->format('d M Y h:i A');
                    $name = $data['poster_name'];
                    
$message = "📢 *New Booking Alert from Customer App!*

Hello GoRide Team,

A new ride request has been received. Please review the details below and assign a driver as soon as possible.

---
🗓 *Booking Details:*
• *Booking Date:* {$created_at}
• *Booking ID:* #{$job_no}
• *Customer Name:* {$name}
• *Pickup Date & Time:* {$pickup_date}

🔗 *Preview Link:* {$previewUrl}

Thank you,
*GoRide System*";

                    $mobilesss = [
                        // '919884557004',
                        // env('BOOK_NO_ONE'),
                        // env('BOOK_NO_TWO')
                        // '919094042940'
                    ];
                    
                    foreach ($mobilesss as $mobile) {
                        Controller::sendNotification([
                            'mobile'            => $mobile,
                            'templateName'      => 'national_draw_verification',
                            'language'          => 'en',
                            'templateBodyParam' => [],
                            'messages'          => $message,
                            'resend'            => false
                        ]);
                    }
            
                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'message' => 'Job created successfully.'
                    ]);
                }
            }

            
            // Write a code for firebase job create one with same job number $job_no.
    
    
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
    
    public function sendWhatsappBook(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_no' => ['required'],
                // 'job_id' => ['required'],
                'mob'    => ['required'],
            ]);
    
            $ch_book = DB::table('cus_job_temp as ct')
                ->where('user_id', 0)
                // ->where('id', $request->job_id)
                ->where('job_no', $request->job_no)
                ->where('user_details->mobile', $request->mob)
                ->first();
    
            if (!$ch_book) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found.'
                ], 200);
            }
    
            if ($ch_book->wh_notify == 1) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Message already sent.'
                ], 200);
            }
    
            $us_data = $ch_book->user_details
                ? json_decode($ch_book->user_details)
                : null;
    
            if (!$us_data) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User data not found.'
                ], 200);
            }
    
            $mobile = $us_data->mobile ?? '';
            $cab_type = $us_data->cab_type ?? '';
            $pass_count = $us_data->pass_count ?? '';
            $lugg_count = $us_data->lugg_count ?? '';
            $name   = $us_data->name ?? 'Customer';
    
            // ✅ Fix mobile format
            if (strlen($mobile) == 10) {
                $mobile = '91' . $mobile;
            }
    
            // ✅ Date formatting (24-hour)
            $pickup_date = Carbon::parse($ch_book->pickup_date)->format('Y-m-d H:i:s');
    
            $dropoff_date = null;
            if (!empty($ch_book->dropoff_date)) {
                $dropoff_date = Carbon::parse($ch_book->dropoff_date)->format('Y-m-d H:i:s');
            }
            
            $toll = $ch_book->toll_fare;
            
            $tot_fare = (int) $ch_book->fare;

    
            // ✅ Build WhatsApp message safely
            $message  = "*GoRide – Booking Confirmation*\n";
            $message .= "━━━━━━━━━━━━━━━━━━\n\n";
            
            $message .= "Customer Name:\n";
            $message .= "{$name}\n\n";
            
            $message .= "Trip Details:\n";
            $message .= "• Pickup Location: {$ch_book->from_place}\n";
            $message .= "• Drop Location: {$ch_book->to_place}\n";
            $message .= "• Distance: {$ch_book->distance} km\n";
            $message .= "• Duration: {$ch_book->duration}\n\n";
            
            $message .= "Schedule:\n";
            $message .= "• Pickup Date & Time: {$pickup_date}\n";
            
            if ($dropoff_date) {
                $message .= "• Drop-off Date & Time: {$dropoff_date}\n";
            }
            
            $message .= "\nTrip Information:\n";
            $message .= "• Trip Type: " . ucfirst($ch_book->job_type) . "\n";
            $message .= "• Vehicle: " . $cab_type . "\n";
            $message .= "• Luggage: " . $lugg_count . "\n";
            $message .= "• Passenger: " . $pass_count . "\n\n";
            
            $message .= "Addresses:\n";
            $message .= "• Pickup Address: {$ch_book->pick_address}\n";
            $message .= "• Drop Address: {$ch_book->drop_address}\n\n";
            
            $message .= "Fare Summary:\n";
            $message .= "• Total Fare: ₹{$tot_fare}\n\n";
            
            $message .= "Thank you for choosing *GoRide*.\n";
            $message .= "For any assistance, feel free to contact us.";

    
            $whatsAppArr = [
                'mobile'           => $mobile,
                'templateName'     => 'national_draw_verification',
                'language'         => 'en',
                'templateBodyParam'=> [],
                'messages'         => $message,
                'resend'           => false
            ];
    
            $sentsms = Controller::sendNotification($whatsAppArr);
    
            if ($sentsms) {
    
                // mark as sent (recommended)
                DB::table('cus_job_temp')
                    ->where('id', $ch_book->id)
                    ->update(['wh_notify' => 1]);
    
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Booking information sent successfully!'
                ], 200);
            }
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'WhatsApp send failed. Invalid mobile number.'
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
    
    public function getVehicles(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'user_id'   => ['required'],
            ]);
            
            if($request->user_id != ''){
                
                $get_user = DB::table('user_register')->where(['id' => $request->user_id, 'deletes' => '0', 'status' => '0'])->select('vehicle_details')->first();

                // $get_rc = optional(
                //     DB::table('ocr_request')
                //         ->where(['user_id' => $request->user_id, 'deletes' => 0, 'doc_type' => 'RC', 'status' => 'ACTIVE'])
                //         ->select('doc_type', 'seater', 'doc_no', 'status', 'user_id', 'req_response')
                //         ->orderByDesc('id')
                //         ->first(),
                //     function ($rc) {
                //         $rc->req_response = $rc->req_response ? json_decode($rc->req_response, true) : null;
                //         return $rc;
                //     }
                // );
                
                $get_seat = DB::table('ocr_request')
                        ->where(['user_id' => $request->user_id, 'deletes' => 0, 'doc_type' => 'RC'])->orderBy('id', 'DESC')->first();
                
                return response()->json([
                    'status'  => true,
                    'data' => $get_user ? ($get_user->vehicle_details ? json_decode($get_user->vehicle_details) : null) : null,
                    'seater_count' => $get_seat ? $get_seat->seater : 4,
                    'message' => 'Vehicle Retrived'
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
    
    public function getVehiclesBidder(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'oid' => ['required'], // owner / customer id
                'sid' => ['required'], // bidder (driver id)
                'jid' => ['required'], // job id
            ]);
    
            // ✅ Check job exists
            $job = DB::table('cus_job_temp')
                ->where('job_no', $request->jid)
                ->where('user_id', $request->oid)
                ->where('deletes', '0')
                ->whereIn('job_status', ['created', 'bidding'])
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Job'
                ], 404);
            }
    
            // ✅ Decode bids_details safely
            $bids = $job->bids_details ? json_decode($job->bids_details, true) : [];
    
            // ✅ Check bidder exists inside bids
            if (!isset($bids[$request->sid])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized bidder'
                ], 401);
            }
    
            // ✅ Get vehicle
            $get_user = DB::table('user_register')
                ->where('id', $request->sid)
                ->where('deletes', '0')
                ->where('status', '0')
                ->select('vehicle_details')
                ->first();
    
            $get_seat = DB::table('ocr_request')
                ->where('user_id', $request->sid)
                ->where('deletes', 0)
                ->where('doc_type', 'RC')
                ->orderBy('id', 'DESC')
                ->first();
    
            return response()->json([
                'status' => true,
                'data' => $get_user && $get_user->vehicle_details
                    ? json_decode($get_user->vehicle_details, true)
                    : [],
                'seater_count' => $get_seat ? $get_seat->seater : 4,
                'message' => 'Vehicle Retrieved'
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function paymentBreakDown(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_id'  => ['nullable'],
                'job_no'  => ['required'],
                'user_id' => ['required'],
                'date' => ['nullable'],
                'isCredit' => ['nullable', 'in:yes,no'],
                'payType' => ['nullable', 'in:part,full'],
                'isWallet' => ['nullable', 'in:yes,no']
            ]);
    
            $bidderId = (int) $request->user_id;
            
            $user_d = auth()->user();
            
            $isWallet = 'no';
            $walletAmt = 0;
            $walletBal = $user_d->walletBalance;
            
            $alreadyPaid = DB::table('payment_history')
                ->where('job_no', $request->job_no)
                ->where('paymentStatus', 'success')
                ->exists();
                
            $alreadyPaidCus = DB::table('cus_job_temp')
                ->where('job_no', $request->job_no)
                ->where('payment_status', 'paid')
                ->exists();
        
            if ($alreadyPaid || $alreadyPaidCus) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment already completed for this job.',
                    'data'    => []
                ], 200);
            }
            
            if($request->filled('isCredit') || $request->filled('isWallet')){
                
                $get_j = DB::table('cus_job_temp')
                    ->where([
                        'job_no'  => $request->job_no,
                        'user_id' => auth()->id()
                    ])
                    ->select('fare_breakdown')
                    ->first();
                
                if (!$get_j || !$get_j->fare_breakdown) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Fare breakdown not found'
                    ], 404);
                }
                
                $fare_b = json_decode($get_j->fare_breakdown, true);
                
                if (!is_array($fare_b)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid fare data'
                    ], 400);
                }
                
                $totalFare = (int) ($fare_b['actual_total_fare'] ?? 0);
                $part_pay = (int) ($fare_b['actual_part_pay'] ?? 0);
                
                $ch_amt = $request->payType == 'part' ? $part_pay : $totalFare;
                
                if ($request->filled('isCredit')) {
                
                    $discount = (int) ($fare_b['discount'] ?? 0);
                    $dis = $discount;
                
                    if ($request->isCredit == 'yes') {
                        // $totalFare -= $discount;
                        $fare_b['isDiscount'] = 'yes';
                    } else if($request->isCredit == 'no') {
                        $totalFare += $discount;
                        $dis = 0;
                        $fare_b['isDiscount'] = 'no';
                    }
                    
                    $fare_b['tax'] = round(($fare_b['base_fare'] + $fare_b['com'] - $dis) * 0.05);

                    $totalFare = $fare_b['base_fare'] + $fare_b['com'] + $fare_b['toll_fare'] + $fare_b['tax'] - $dis;
                    
                    $part_pay = $totalFare - (
                        (int) ($fare_b['base_fare'] ?? 0) +
                        (int) ($fare_b['toll_fare'] ?? 0)
                    );
                }
                
                // \Log::error('Testing...: ' . $totalFare);
                
                if ($request->filled('isWallet')) {
                
                    $walletAmt = auth()->user()->walletBalance ?? 0;
                
                    if ($request->isWallet == 'yes' && $fare_b['isWallet'] != 'yes') {
                        
                        if($request->payType == 'full'){
                            $walletUse = min($walletAmt, $totalFare);
                            $totalFare -= $walletUse;
                            
                        }else{
                            $walletUse = min($walletAmt, $part_pay);
                            $part_pay -= $walletUse;
                            
                        }
                        
                        
                        $fare_b['wallet'] = $walletUse;
                        $fare_b['walletBalance'] = 0;
                        $fare_b['isWallet'] = 'yes';
                
                    } else if($request->isWallet == 'no' && $fare_b['isWallet'] != 'no') {
                        
                        $walletAmt = auth()->user()->walletBalance ?? 0;
                        
                        if($request->payType == 'full'){
                            // $walletUse = min($walletAmt, $totalFare);
                            // $totalFare -= $walletUse;
                            // $totalFare += (int) ($walletAmt ?? 0);
                            
                        }else{
                            // $walletAmt = min($walletAmt, $fare_b['wallet']);
                            // $walletAmt = auth()->user()->walletBalance ?? 0;
                            // $part_pay_fare -= $walletUse;
                            // $part_pay += (int) ($walletAmt ?? 0);
                            
                        }
                
                        $fare_b['walletBalance'] = $walletAmt;
                        $fare_b['wallet'] = 0;
                        $fare_b['isWallet'] = 'no';
                    }
                }
                
                
                $fare_b['total_fare'] = $totalFare;
                $fare_b['part_pay_fare'] = $part_pay;
                
                DB::table('cus_job_temp')
                    ->where([
                        'job_no'  => $request->job_no,
                        'user_id' => auth()->id()
                    ])
                    ->update([
                        'pay_amt'        => $totalFare,
                        'fare_breakdown' => json_encode($fare_b),
                        'updated_at'     => now()
                    ]);
                
                $fare_b['base_fare'] = $fare_b['base_fare'] + ($fare_b['com'] ?? 0);
                
                return response()->json([
                    'status'  => true,
                    'data'    => $fare_b,
                    'pay_no'  => $request->job_no,
                    'message' => 'Payment break down'
                ], 200);
            }
            
            $sc_id = null;
            
            if (isset($request->date)) {

                $job = DB::table('cus_job_temp')
                    ->where('job_no', $request->job_no)
                    ->first();
            
                if (!$job) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Job not found.'
                    ], 200);
                }
                
                $job = (array) $job;
                
                $sch_id = json_decode($job['sch_status'], true);
                
                $sc_id = $sch_id[$request->date][$request->user_id]['sch_id'];
            
                $get_sch_amount = DB::table('schedule_dates')
                    ->where('user_id', $request->user_id)
                    ->where('id', $sch_id[$request->date][$request->user_id]['sch_id'])
                    ->first();
                
                $bid['amount'] = 0;
                
                if ($get_sch_amount && !empty($get_sch_amount->dates_price)) {
                
                    $datesPrice = json_decode($get_sch_amount->dates_price, true);
                
                    foreach ($datesPrice as $dateTime => $amount) {
                
                        // Extract only the date part
                        $date = substr($dateTime, 0, 10);
                
                        if ($date == $request->date) {
                            $bid['amount'] = $amount;
                            break;
                        }
                    }
                }
                
                $ch_amt = false;
            }else{
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
        
                $firebaseDoc = $firebase->getJob($request->job_no);
        
                $job = $this->parseFirestoreFields($firebaseDoc);
        
                if (
                    empty($job['bids_details']) ||
                    !isset($job['bids_details'][$bidderId])
                ) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Bid not found for this user.'
                    ], 200);
                }
        
                $bid = $job['bids_details'][$bidderId];
                
                $ch_amt = ((int)$job['base_fare'] + (int)$job['toll_fare']) == (int) $bid['amount'];
            }
            
            // return $ch_amt != true;
            $u_cash_point = auth()->user()->cash_points ?? 0;
            
            $v_no = null;
            
            $dri_ex_amt = 0;
    
            if ($ch_amt == true) {
                
                $base_fare  = $job['base_fare'];
                $tax        = $job['tax'];
                $total_fare = $job['fare'];
                $com = $job['com'];
                // $discount = $job['discount'];
                $dri_ex_amt = $base_fare + $job['toll_fare'];
                
                $discount = round(($base_fare + $com) * 0.05);
                $discount = min($u_cash_point, $discount);
                
            } else {
                $dri_ex_amt = $bid['amount'];
                if($job['global_type'] == 'schedule'){
                    $base_fare  = (int) $bid['amount'];
                    
                }else{
                    $base_fare  = (int) $bid['amount'] - (int)$job['toll_fare'];
                    
                }
                
                $com = round(($base_fare + $job['toll_fare']) *0.05);
                
                $discount = round(($base_fare + $com) * 0.05);
                
                if($discount > 500){
                    $discount = 500;
                }
                // $discount_amt = round(($base_fare + $com) * 0.05);
                
                if($discount <= $u_cash_point && $u_cash_point != 0){
            
                    $tax = round( (($base_fare + $com) - $discount) * 0.05);
                    
                }elseif($u_cash_point != 0){
                    // $min_dis = min($u_cash_point, $discount);
                    $discount = min($u_cash_point, $discount);
                    
                    $tax = round( (($base_fare + $com) - $discount) * 0.05);
                    
                }else{
                    $tax = round(($base_fare + $com) * 0.05);
                    $discount = 0;
                }
                
                $job['fare'] = ($base_fare + $tax + $com + $job['toll_fare']) - $discount;
                
                $total_fare = $job['fare'];
                
                $v_n = array_key_exists('b_cab_no', $bid) && $bid['b_cab_no'] != '' && $bid['b_cab_no'] != null ? $bid['b_cab_no'] : null;
                
            }
            
            $part_pay = $total_fare - ($base_fare + $job['toll_fare']);
    
            $passCount = ($job['pass_count'] ?? 'four_seater');
            
            $job['isDiscount'] = $u_cash_point != 0 ? 'yes' : 'no';
            // $job['discount'] = $u_cash_point != 0 ? $job['discount'] : 0;
            // $discount = $u_cash_point != 0 ? ($u_cash_point > $discount? $u_cash_point: $discount) : 0;
            $discount = $u_cash_point != 0 ? (int) min($u_cash_point, $discount) : 0;
            
            
            $job['pass_count'] = $job['pass_count'] == 'mini' ? '4 Mini' : $job['pass_count'] - 1;
            
            
            if($passCount == 'mini'){
                $column = 'mini_four_seater';
            }else if($passCount == 4 || $passCount == 5){
                $column = 'four_seater';
            }else if($passCount == 7){
                $column = 'six_seater';
            }else if($passCount == 8){
                $column = 'seven_seater';
            }
            
            if($job['job_type'] == 'roundtrip'){
                $column = $column.'_round';
            }
    
            $get_fare = DB::table('tariff_fare_website')
                ->where('from_km', '<=', (float) $job['distance'])
                ->where('to_km', '>=', (float) $job['distance'])
                ->where($column, '!=', 0)
                ->where('status', '0')
                ->first();
    
            $ex_km = '14';
            $to_km = $job['distance'];
    
            if ($get_fare) {
                $ex_km = $get_fare->fare_km;
                $to_km = $get_fare->to_km;
            }
            
            $inclu = [
                // "{$ex_km} km included",
                "{$to_km} Upto Km included",
                "Toll, Bata, Parking Included",
                "Driver allowance Included",
                "Driver food and accommodation (stay) charges included",
                "Waiting time up to 30 minutes for pickup included (₹100 per 60 minutes after 30 minutes)",
                "Sightseeing included",
                "Fuel charges included",
                "Toll charges included (based on actual value)",
                "Return trips close by 9:00 PM",
                "Parking charges included",
                "Taxes included"
            ];
    
            $exclu = [
                "₹{$ex_km}/km will apply beyond the included kms",
                "State permit / entry charges",
                "Hill station charges (extra)",
                // "Toll charges & parking charges"
                // Additional kilometers: ₹15 per km
                // Additional hours / days: ₹100 per hour & ₹1200 per day
                // Waiting: First 30 minutes free; additional waiting charges apply
                "Any government taxes or local charges, if applicable"
            ];
            
            $terms = [
                "Your trip has a KM limit. If your usage exceeds this limit, you will be charged for the excess KM used",
                "Your trip includes one pickup in the pickup city and one drop to the destination city. It does not include within-city travel",
                "If your trip has hill climbs, the cab AC may be switched off during such climbs",
                "Cancellation charges may apply as per policy",
                "Refund will be processed within 5-7 business days",
                "Driver will wait for 15 minutes after scheduled pickup time",
                "Additional charges may apply for extra stops",
                "Customer is responsible for toll charges if not included",
                "Vehicle condition should be maintained during the trip",
                "Any damages will be charged separately",
                "GoRide shall not be responsible or liable for any damages, injuries, losses, or disputes arising between the driver and the customer during the ride"
            ];
    
            $data = [
                'id'            => $job['id'] ?? null,
                'bidder_id'     => $bidderId ?? null,
                'sch_id'     => $sc_id ?? null,
                'bidder_date'     => $request->date ?? null,
                'job_no'        => $job['job_no'] ?? null,
                'job_type'      => $job['job_type'] ?? null,
                'pass_count'    => $job['pass_count'] ?? null,
                'from_place'    => $job['from_place'] ?? null,
                'to_place'      => $job['to_place'] ?? null,
                'pickup_date'   => $job['pickup_date'] ?? null,
                'dropoff_date'   => $job['dropoff_date'] ?? null,
                'distance'      => $job['distance'] ?? null,
                'duration'      => $job['duration'] ?? null,
    
                'bata'          => $job['add_fare_details']['bata'] ?? 0,
                'toll'          => $job['add_fare_details']['toll'] ?? 0,
                'parking'       => $job['add_fare_details']['parking'] ?? 0,
                
                'base_fare'     => $base_fare,
                'toll_fare'     => $job['toll_fare'],
    
                'tax'           => $tax,
                'com'           => $com,
                'pay_to_driver' => ((int)$base_fare + (int)$job['toll_fare']),
                'total_fare'    => $total_fare,
                
                'actual_total_fare' => $total_fare,
                'actual_part_pay' => $part_pay,
                
                'part_pay_fare' => $part_pay,
                'credit_pay_fare' => $discount,
                'isDiscount' => $job['isDiscount'],
                'discount' => $discount,
                
                'isWallet' => $isWallet,
                'wallet' => $walletAmt,
                'walletBalance' => $user_d->walletBalance??0,
                'actual_walletBalance' => $user_d->walletBalance??0,
                'user_credit'   => $user_d->cash_points??0,
                'b_cab_no'   => $v_n??null,
                'actual_driver_amt' => $dri_ex_amt,
    
                'inclusion'     => $inclu,
                'exclusion'     => $exclu,
                'terms' => $terms
            ];
    
            DB::table('cus_job_temp')->where(['job_no' => $request->job_no, 'user_id' => auth()->user()->id])->update([
                'pay_amt'  => $total_fare,
                'fare_breakdown'   => json_encode($data),
                'updated_at'=> now()
            ]);
            
            $data['base_fare'] = $data['base_fare'] + $data['com'];
    
            return response()->json([
                'status'  => true,
                'data'    => $data,
                'pay_no'    => $request->job_no,
                'message' => 'Payment break down'
            ], 200);
            
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            \Log::info('Schedule: ', [$e->getMessage()]);
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function webPaymentBreakDown(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_id'  => ['nullable'],
                'job_no'  => ['required'],
                'user_id' => ['required'],
                'date' => ['nullable'],
                'isCredit' => ['nullable', 'in:yes,no'],
                'payType' => ['nullable', 'in:part,full'],
                'isWallet' => ['nullable', 'in:yes,no']
            ]);
    
            $bidderId = (int) $request->user_id;
            
            $user_d = null;
            
            $isWallet = 'no';
            $walletAmt = 0;
            $walletBal = 0;
            
            $alreadyPaid = DB::table('payment_history')
                ->where('job_no', $request->job_no)
                ->where('paymentStatus', 'success')
                ->exists();
                
            $alreadyPaidCus = DB::table('cus_job_temp')
                ->where('job_no', $request->job_no)
                ->where('payment_status', 'paid')
                ->exists();
        
            if ($alreadyPaid || $alreadyPaidCus) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Payment already completed for this job.',
                    'data'    => []
                ], 200);
            }
            
            $sc_id = null;
            
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );
    
            $firebaseDoc = $firebase->getJob($request->job_no);
    
            $job = $this->parseFirestoreFields($firebaseDoc);
    
            if (
                empty($job['bids_details']) ||
                !isset($job['bids_details'][$bidderId])
            ) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bid not found for this user.'
                ], 200);
            }
    
            $bid = $job['bids_details'][$bidderId];
            
            $ch_amt = ((int)$job['base_fare'] + (int)$job['toll_fare']) == (int) $bid['amount'];
            
            // return $ch_amt != true;
            $u_cash_point = 0;
            
            $v_no = null;
            
            $dri_ex_amt = 0;
    
            if ($ch_amt == true) {
                
                $base_fare  = $job['base_fare'];
                $tax        = $job['tax'];
                $total_fare = $job['fare'];
                $com = $job['com'];
                // $discount = $job['discount'];
                $dri_ex_amt = $base_fare + $job['toll_fare'];
                
                $discount = round(($base_fare + $com) * 0.05);
                $discount = min($u_cash_point, $discount);
                
            } else {
                $dri_ex_amt = $bid['amount'];
                if($job['global_type'] == 'schedule'){
                    $base_fare  = (int) $bid['amount'];
                    
                }else{
                    $base_fare  = (int) $bid['amount'] - (int)$job['toll_fare'];
                    
                }
                
                $com = round(($base_fare + $job['toll_fare']) *0.05);
                
                $discount = round(($base_fare + $com) * 0.05);
                
                if($discount > 500){
                    $discount = 500;
                }
                // $discount_amt = round(($base_fare + $com) * 0.05);
                
                if($discount <= $u_cash_point && $u_cash_point != 0){
            
                    $tax = round( (($base_fare + $com) - $discount) * 0.05);
                    
                }elseif($u_cash_point != 0){
                    // $min_dis = min($u_cash_point, $discount);
                    $discount = min($u_cash_point, $discount);
                    
                    $tax = round( (($base_fare + $com) - $discount) * 0.05);
                    
                }else{
                    $tax = round(($base_fare + $com) * 0.05);
                    $discount = 0;
                }
                
                $job['fare'] = ($base_fare + $tax + $com + $job['toll_fare']) - $discount;
                
                $total_fare = $job['fare'];
                
                $v_n = array_key_exists('b_cab_no', $bid) && $bid['b_cab_no'] != '' && $bid['b_cab_no'] != null ? $bid['b_cab_no'] : null;
                
            }
            
            $part_pay = $total_fare - ($base_fare + $job['toll_fare']);
    
            $passCount = ($job['pass_count'] ?? 'four_seater');
            
            $job['isDiscount'] = $u_cash_point != 0 ? 'yes' : 'no';
            $discount = $u_cash_point != 0 ? (int) min($u_cash_point, $discount) : 0;
            
            $job['pass_count'] = $job['pass_count'] == 'mini' ? '4 Mini' : $job['pass_count'] - 1;
            
            
            if($passCount == 'mini'){
                $column = 'mini_four_seater';
            }else if($passCount == 4 || $passCount == 5){
                $column = 'four_seater';
            }else if($passCount == 7){
                $column = 'six_seater';
            }else if($passCount == 8){
                $column = 'seven_seater';
            }
            
            if($job['job_type'] == 'roundtrip'){
                $column = $column.'_round';
            }
    
            $get_fare = DB::table('tariff_fare_website')
                ->where('from_km', '<=', (float) $job['distance'])
                ->where('to_km', '>=', (float) $job['distance'])
                ->where($column, '!=', 0)
                ->where('status', '0')
                ->first();
    
            $ex_km = '14';
            $to_km = $job['distance'];
    
            if ($get_fare) {
                $ex_km = $get_fare->fare_km;
                $to_km = $get_fare->to_km;
            }
            
            $inclu = [
                // "{$ex_km} km included",
                "{$to_km} Upto Km included",
                "Toll, Bata, Parking Included",
                "Driver allowance Included",
                "Driver food and accommodation (stay) charges included",
                "Waiting time up to 30 minutes for pickup included (₹100 per 60 minutes after 30 minutes)",
                "Sightseeing included",
                "Fuel charges included",
                "Toll charges included (based on actual value)",
                "Return trips close by 9:00 PM",
                "Parking charges included",
                "Taxes included"
            ];
    
            $exclu = [
                "₹{$ex_km}/km will apply beyond the included kms",
                "State permit / entry charges",
                "Hill station charges (extra)",
                // "Toll charges & parking charges"
                // Additional kilometers: ₹15 per km
                // Additional hours / days: ₹100 per hour & ₹1200 per day
                // Waiting: First 30 minutes free; additional waiting charges apply
                "Any government taxes or local charges, if applicable"
            ];
            
            $terms = [
                "Your trip has a KM limit. If your usage exceeds this limit, you will be charged for the excess KM used",
                "Your trip includes one pickup in the pickup city and one drop to the destination city. It does not include within-city travel",
                "If your trip has hill climbs, the cab AC may be switched off during such climbs",
                "Cancellation charges may apply as per policy",
                "Refund will be processed within 5-7 business days",
                "Driver will wait for 15 minutes after scheduled pickup time",
                "Additional charges may apply for extra stops",
                "Customer is responsible for toll charges if not included",
                "Vehicle condition should be maintained during the trip",
                "Any damages will be charged separately",
                "GoRide shall not be responsible or liable for any damages, injuries, losses, or disputes arising between the driver and the customer during the ride"
            ];
    
            $data = [
                'id'            => $job['id'] ?? null,
                'bidder_id'     => $bidderId ?? null,
                'sch_id'     => $sc_id ?? null,
                'bidder_date'     => $request->date ?? null,
                'job_no'        => $job['job_no'] ?? null,
                'job_type'      => $job['job_type'] ?? null,
                'pass_count'    => $job['pass_count'] ?? null,
                'from_place'    => $job['from_place'] ?? null,
                'to_place'      => $job['to_place'] ?? null,
                'pickup_date'   => $job['pickup_date'] ?? null,
                'dropoff_date'   => $job['dropoff_date'] ?? null,
                'distance'      => $job['distance'] ?? null,
                'duration'      => $job['duration'] ?? null,
    
                'bata'          => $job['add_fare_details']['bata'] ?? 0,
                'toll'          => $job['add_fare_details']['toll'] ?? 0,
                'parking'       => $job['add_fare_details']['parking'] ?? 0,
                
                'base_fare'     => $base_fare,
                'toll_fare'     => $job['toll_fare'],
    
                'tax'           => $tax,
                'com'           => $com,
                'pay_to_driver' => ((int)$base_fare + (int)$job['toll_fare']),
                'total_fare'    => $total_fare,
                
                'actual_total_fare' => $total_fare,
                'actual_part_pay' => $part_pay,
                
                'part_pay_fare' => $part_pay,
                'credit_pay_fare' => $discount,
                'isDiscount' => $job['isDiscount'],
                'discount' => $discount,
                
                'isWallet' => $isWallet,
                'wallet' => $walletAmt,
                'walletBalance' => 0,
                'actual_walletBalance' => 0,
                'user_credit'   => 0,
                'b_cab_no'   => $v_n??null,
                'actual_driver_amt' => $dri_ex_amt,
    
                'inclusion'     => $inclu,
                'exclusion'     => $exclu,
                'terms' => $terms
            ];
    
            DB::table('cus_job_temp')->where(['job_no' => $request->job_no, 'user_id' => 0])->update([
                'pay_amt'  => $total_fare,
                'fare_breakdown'   => json_encode($data),
                'updated_at'=> now()
            ]);
            
            $data['base_fare'] = $data['base_fare'] + $data['com'];
    
            return response()->json([
                'status'  => true,
                'data'    => $data,
                'pay_no'    => $request->job_no,
                'message' => 'Payment break down'
            ], 200);
            
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            
            \Log::info('Schedule: ', [$e->getMessage()]);
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pay_no'   => 'required',
            'pay_type'   => 'required',
            'wallet_pay' => 'nullable',
            'credit_pay' => 'nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $user = $request->user();
        
        // return $user;
        
        $depositAmt = 0;
        $finalAmt = 0;
        
        if($request->pay_no != ''){
            $get_data = DB::table('cus_job_temp')->where(['user_id' => $user->id, 'job_no' => $request->pay_no])->whereIn('job_status', ['created', 'bidding', 'schedule'])->first();
            
            if($get_data){
                $depositAmt = (int) $get_data->pay_amt;
                $finalAmt = $depositAmt;
            }else{
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Booking not found or cancelled',
                    'error'   => 'Booking not found or cancelled'
                ]);
            }
        }
        
        $fr_details = json_decode($get_data->fare_breakdown, true);
        
        if($get_data->bids_details){
            
            $bid_details = json_decode($get_data->bids_details, true);
            
            $bids = $bid_details[$fr_details['bidder_id']];
            
            if($bids['amount'] != $fr_details['actual_driver_amt']){
                
                return response()->json([
                    'status' => false,
                    'message' => 'The price was changed. Refresh to proceed with payment.'
                ]);
            }
            
        }
        
        $wallet_amt = (float) (auth()->user()->walletBalance ?? 0);
        $wtotalFare    = (float) ($fr_details['total_fare'] ?? 0);
        $credit_amt = 0;
        
        if ($request->pay_type == 'part') {

            $fr_details = json_decode($get_data->fare_breakdown, true);
        
            // $walletPayFare = $fr_details['wallet'] ?? 0;
            $creditPayFare = $fr_details['discount'] ?? 0;
            $partPayFare   = $fr_details['part_pay_fare'] ?? 0;
            $taxFare       = $fr_details['tax'] ?? 0;
        
            // if (($user->cash_points ?? 0) < $creditPayFare && $request->credit_pay == 'yes') {
            //     return response()->json([
            //         'status'  => 'failed',
            //         'message' => 'Low credit points',
            //         'error'   => 'Low credit points'
            //     ], 200);
            // }
        
            $depositAmt = $partPayFare;
            
            if ($fr_details['isDiscount'] == 'yes') {
                $credit_amt = $creditPayFare;
                
            } else {
                $credit_amt = 0;
            }
            
            // return $fr_details;
            $finalAmt = $taxFare;
        }else{
            
            $creditPayFare = $fr_details['discount'] ?? 0;
            $partPayFare   = $fr_details['part_pay_fare'] ?? 0;
            $taxFare       = $fr_details['tax'] ?? 0;
            
            if ($fr_details['isDiscount'] == 'yes') {
                $credit_amt = $creditPayFare;
                
            } else {
                $credit_amt = 0;
            }
        }
        
        $w_amt = 0;
        
        // return $credit_amt;
        

        $alreadyPaid = DB::table('payment_history')
            ->where('job_no', $get_data->job_no)
            ->where('paymentStatus', 'success')
            ->exists();
            
        $alreadyPaidCus = DB::table('cus_job_temp')
            ->where('job_no', $get_data->job_no)
            ->where('payment_status', 'paid')
            ->exists();
    
        if ($alreadyPaid || $alreadyPaidCus) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment already completed for this job.',
                'data'    => []
            ], 200);
        }
        
        $w_amt = $fr_details['wallet'];
        // if ($request->wallet_pay != 'yes') {
            
            // $w_amt = $depositAmt;
        // }
        
        if ($request->wallet_pay == 'yes' && $wallet_amt < $depositAmt) {
            return response()->json([
                'status'  => false,
                'message' => 'Insufficient wallet balance.',
                'data'    => []
            ], 200);
        }
        
        $currency = 'INR';
        
        $amountInPaise = (int) round($depositAmt * 100);
        
        
    
        DB::beginTransaction();
    
        try {
            // Get last ID
            $lastPayment = DB::table('payment_history')
                ->select('id')
                ->orderBy('id', 'desc')
                ->first();
    
            $lastId = $lastPayment->id ?? 0;
    
            $tran_id = 'GRB' . uniqid() . date('Hii') . ($lastId + 1);

            $buildCheckOut = [
                'userID'          => $user->id,
                'depositAmt'      => $depositAmt,
                'existWalletAmt'  => number_format($user->walletBalance, 2),
                'existCash_points'  => number_format($user->cash_points, 2),
                'finalTotal'      => $fr_details['actual_total_fare'],
                'discount'        => 0,
                'shipamount'      => 0,
                'wallet_amt'         => $w_amt,
                'credit_amt'         => $credit_amt,
                'grandtotal'      => $depositAmt,
                'shipping'        => 'pickUpToStore',
            ];
    
            $checkout_arr = [
                'createdon'          => now(),
                'crontime'           => now(),
                'ip'                 => $request->ip() ?? '',
                'user_id'            => $user->id,
                'status'             => '0',
                'transaction_id'     => $tran_id,
                'job_no'     => $request->pay_no,
                'checkout_response'  => json_encode($buildCheckOut),
                'category'           => 'Purchase',
                'gateway'            => $request->wallet_pay == 'yes' ? 'wallet' : 'razorpay',
                'finaltotal'         => $depositAmt,
                'wallet_amt'         => $w_amt,
                'receipt_no'         => '',
                'reference'          => '',
                // 'renewalStatus'      => 'RECHARGE',
                'paymentStatus'      => 'initiated',
                'shipamount'         => 0,
                // 'wallet_amt'         => 0,
                'credit_amt'         => $credit_amt,
                'grandtotal'         => $depositAmt,
            ];
    
            $paymentId = DB::table('payment_history')->insertGetId($checkout_arr);
            
            if ($request->wallet_pay == 'yes') {
                
                $get_log = DB::table('cus_job_temp')
                    ->where('job_no', $get_data->job_no)
                    ->first();
                if (!$get_log) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Job not found.'
                    ]);
                }
                
                $fareBreakdown = json_decode($get_log->fare_breakdown, true);
                $sch_status = json_decode($get_log->sch_status, true);
                
                if($get_log && $get_log->global_type != 'schedule'){
                    
                    $firebase = new \App\Services\FirebaseJobService(
                        $this->serviceAccount['project_id'],
                        $this->getAccessToken()
                    );
                    
                    $firebaseDoc = $firebase->getJob($get_data->job_no);
                    $job = $this->parseFirestoreFields($firebaseDoc);
                    $bidder_id     = $fareBreakdown['bidder_id'] ?? null;
                
                    if (!$bidder_id) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Invalid bidder.'
                        ]);
                    }
                
                    $job['bids_details'][$bidder_id]['status'] = 'accept';
                
                    $firebase->updateBidStatus(
                        $get_log->job_no,
                        $bidder_id,
                        'accept'
                    );
                }else{
                    
                    $sch_status[$fareBreakdown['bidder_date']][$fareBreakdown['bidder_id']]['status'] = 'accept';

                    $pickupParts = explode(' ', $get_data->pickup_date);
                    $pickupTime = $pickupParts[1] ?? '00:00:00';
                    
                    $newDate = $fareBreakdown['bidder_date'];
                    $sch_id = $fareBreakdown['sch_id'];
                    
                    $bidder_id = $fareBreakdown['bidder_id'];
                    
                    $newPickupDateTime = $newDate . ' ' . $pickupTime;
                    
                    DB::table('cus_job_temp')
                        ->where('job_no', $get_data->job_no)
                        ->update([
                            'pickup_date' => $newPickupDateTime,
                            'sch_status' => json_encode($sch_status)
                        ]);
                        
                    
                    
                    $get_sh = DB::table('schedule_dates')
                        ->where('user_id', $fareBreakdown['bidder_id'])
                        ->where('id', $sch_id)
                        ->first();
                    
                    if ($get_sh && $get_sh->dates_price) {
                    
                        $de_code = json_decode($get_sh->dates_price, true);
                    
                        foreach ($de_code as $key => $value) {
                    
                            if (strpos($key, $newDate) === 0) {
                                unset($de_code[$key]);
                            }
                        }
                    
                        DB::table('schedule_dates')
                            ->where('user_id', $fareBreakdown['bidder_id'])
                            ->where('id', $sch_id)
                            ->update([
                                'dates_price' => json_encode($de_code)
                            ]);
                            
                        $firebase = new \App\Services\FirebaseJobService(
                            $this->serviceAccount2['project_id'],
                            $this->getAccessToken2()
                        );
                        
                        $firebase->deleteScheduleJob($get_data->job_no);
                    }
                }
                
            
                $walletBalance  = (float) $wallet_amt;
                $finaltotal     = (float) $depositAmt;
                $closingBalance = $walletBalance - $finaltotal;
            
                $payArr = [
                    "userid"            => $user->id,
                    "uname"             => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                    "umobile"           => auth()->user()->mobile,
                    "uemail"            => auth()->user()->email,
                    'opening_balance'   => $walletBalance,
                    'total'             => $finaltotal,
                    'closeing_balance'  => $closingBalance,
                    'point_type'        => 'WALLET',
                    'transaction_type'  => 'DEBIT',
                    'reward_type'       => 'JOB',
                    'card_no'           => '',
                    'reference_id'      => $lastId ?? null,
                    'reference_table'   => 'payment_history',
                    'ip'                => $request->ip() ?? '',
                    'global_type' => 'customer',
                    'createdon'         => now()
                ];
                
            
                $wallet_history = DB::table('walletBalance_history')
                    ->insertGetId($payArr);
            
                if (!$wallet_history) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Wallet transaction log failed.'
                    ]);
                }
            
                DB::table('customer_register')
                    ->where('id', $user->id)
                    ->update([
                        'walletBalance' => $closingBalance,
                        'updated_at'    => now()
                    ]);
            
                if (($fareBreakdown['isDiscount'] ?? '') == 'yes') {
            
                    $opening        = (float) ($user->cash_points ?? 0);
                    $expectedAmount = (float) ($fareBreakdown['discount'] ?? 0);
                    $closing        = $opening - $expectedAmount;
            
                    DB::table('walletBalance_history')->insert([
                        'userid'            => $user->id,
                        'uname'             => $user->name,
                        'umobile'           => $user->mobile,
                        'uemail'            => $user->email,
                        'opening_balance'   => $opening,
                        'total'             => $expectedAmount,
                        'closeing_balance'  => $closing,
                        'point_type'        => 'CREDIT',
                        'transaction_type'  => 'DEBIT',
                        'reward_type'       => 'JOB',
                        'global_type' => 'customer',
                        'reference_id'      => $lastId ?? null,
                        'reference_table'   => 'payment_history',
                        'ip'                => $request->ip(),
                        'createdon'         => now(),
                        'updatedon'         => now()
                    ]);
            
                    DB::table('customer_register')
                        ->where('id', $user->id)
                        ->update([
                            'cash_points' => $closing
                        ]);
                }
                
                $total_fare = 
                    $fareBreakdown['base_fare'] +
                    $fareBreakdown['com'] +
                    $fareBreakdown['tax'] +
                    $fareBreakdown['toll_fare'] -
                    ($fareBreakdown['isDiscount'] == 'yes' ? $fareBreakdown['discount'] : 0);
            
                DB::table('cus_job_temp')
                    ->where('job_no', $get_log->job_no)
                    ->where('user_id', $user->id)
                    ->update([
                        'com'            => $fareBreakdown['com'] ?? 0,
                        'tax'            => $fareBreakdown['tax'] ?? 0,
                        'base_fare'      => $fareBreakdown['base_fare'] ?? 0,
                        'toll_fare'      => $fareBreakdown['toll_fare'] ?? 0,
                        'discount'       => $fareBreakdown['discount'] ?? 0,
                        'isDiscount'     => $fareBreakdown['isDiscount'] ?? 0,
                        'wallet_amt'     => $fareBreakdown['wallet'] ?? 0,
                        'fare'           => $total_fare ?? 0,
                        'pay_amt'        => $depositAmt,
                        'credit'         => $fareBreakdown['discount'] ?? 0,
                        'job_status'     => 'accept',
                        'payment_status' => 'paid',
                        'assigned_to' => $fareBreakdown['bidder_id'],
                        // 'assigned_cab' => $fareBreakdown['b_cab_no'],
                        'bids_details'   => $get_log->global_type != 'schedule' ? json_encode($job['bids_details']) : null,
                        'fare_breakdown' => $get_log->fare_breakdown,
                        'updated_at'     => now()
                    ]);
                    
                $columns = DB::getSchemaBuilder()->getColumnListing('cus_job_temp');

                /* Remove 'id' column */
                $columns = array_filter($columns, function ($column) {
                    return $column !== 'id';
                });
                
                /* Convert array to comma-separated string */
                $columnList = implode(',', $columns);
                
                DB::statement("
                    INSERT INTO open_jobs ($columnList)
                    SELECT $columnList
                    FROM cus_job_temp
                    WHERE job_no = ? AND user_id = ?
                ", [$get_log->job_no, $user->id]);
            
                DB::table('payment_history')
                    ->where('id', $paymentId)
                    ->update([
                        'paymentStatus' => 'success',
                        'status'        => 1,
                        // 'updated_at'    => now()
                    ]);
                    
                DB::commit();
                
                if ($bidder_id) {
                    dispatch(new \App\Jobs\SendFcmNotificationJob(
                        type: 'accept_notification',
                        userIds: [$bidder_id],
                        title: 'Bid Accepted',
                        body: "Great news! Your bid for Job #{$get_log->job_no} ({$get_log->from_place}) has been accepted.",
                    ));
                }
                    
                return response()->json([
                    'status'  => true,
                    'data'    => [
                        // 'job_id' => $openJobId,
                        'job_no'      => $get_log->job_no,
                        // 'v_details' => $get_v ? $get_v->vehicle_details : null,
                        'paid_amount' => $depositAmt,
                        'ride_info' => $fareBreakdown
                    ],
                    'message' => 'Ride confirmed successfully.'
                ], 200);
                    
            } else{
                
                $orderData = [
                    'receipt'         => $tran_id,
                    'amount'          => $amountInPaise,
                    'currency'        => $currency,
                    'payment_capture' => 1
                ];
        
                $razorpayOrder = $this->razorpay->order->create($orderData);
                
                DB::table('payment_history')
                    ->where('id', $paymentId)
                    ->update(['receipt_no' => $razorpayOrder['id']]);
        
                DB::commit();
        
                return response()->json([
                    'success'      => true,
                    'order_id'     => $razorpayOrder['id'],
                    'amount'       => $depositAmt,
                    'currency'     => $currency,
                    'razorpay_key' => env('RAZAPI_KEY_ID'),
                    'tx_id'        => $tran_id,
                ]);
            }
            
    
        } catch (\Exception $e) {
    
            DB::rollBack();
            \Log::error('Razorpay order creation failed: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => 'Unable to create order, try again later.'
            ], 500);
        }
    }
    
    public function cashOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pay_no'   => 'required',
            'credit_pay' => 'nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $user = $request->user();
        
        $depositAmt = 0;
        $finalAmt = 0;
        
        if($request->pay_no != ''){
            $get_data = DB::table('cus_job_temp')->where(['user_id' => $user->id, 'job_no' => $request->pay_no])->whereIn('job_status', ['created', 'bidding', 'schedule'])->first();
            
            if($get_data){
                $depositAmt = (int) $get_data->pay_amt;
                $finalAmt = $depositAmt;
            }else{
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Booking not found or cancelled',
                    'error'   => 'Booking not found or cancelled'
                ]);
            }
        }
        
        $fr_details = json_decode($get_data->fare_breakdown, true);
        
        if($get_data->bids_details){
            
            $bid_details = json_decode($get_data->bids_details, true);
            
            $bids = $bid_details[$fr_details['bidder_id']];
            
            if($bids['amount'] != $fr_details['actual_driver_amt']){
                
                return response()->json([
                    'status' => false,
                    'message' => 'The price was changed. Refresh to proceed with payment.'
                ]);
            }
            
        }
        
        $wallet_amt = (float) (auth()->user()->walletBalance ?? 0);
        $wtotalFare    = (float) ($fr_details['total_fare'] ?? 0);
        $credit_amt = 0;
        
        $creditPayFare = $fr_details['discount'] ?? 0;
        $partPayFare   = $fr_details['part_pay_fare'] ?? 0;
        $taxFare       = $fr_details['tax'] ?? 0;
        
        if ($fr_details['isDiscount'] == 'yes') {
            $credit_amt = $creditPayFare;
            
        } else {
            $credit_amt = 0;
        }
        $w_amt = 0;
        
        $alreadyPaid = DB::table('payment_history')
            ->where('job_no', $get_data->job_no)
            ->where('gateway', 'cash')
            ->exists();
            
        $alreadyPaidCus = DB::table('cus_job_temp')
            ->where('job_no', $get_data->job_no)
            ->where('payment_status', 'paid')
            ->exists();
    
        if ($alreadyPaid || $alreadyPaidCus) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment already completed for this job.',
                'data'    => []
            ], 200);
        }
        
        $w_amt = $fr_details['wallet'];
        
        $currency = 'INR';
        
        $amountInPaise = (int) round($depositAmt * 100);
        
        DB::beginTransaction();
    
        try {
            $lastPayment = DB::table('payment_history')
                ->select('id')
                ->orderBy('id', 'desc')
                ->first();
    
            $lastId = $lastPayment->id ?? 0;
    
            $tran_id = 'GRB' . uniqid() . date('Hii') . ($lastId + 1);

            $buildCheckOut = [
                'userID'          => $user->id,
                'depositAmt'      => $depositAmt,
                'existWalletAmt'  => number_format($user->walletBalance, 2),
                'existCash_points'  => number_format($user->cash_points, 2),
                'finalTotal'      => $fr_details['actual_total_fare'],
                'discount'        => 0,
                'shipamount'      => 0,
                'wallet_amt'         => $w_amt,
                'credit_amt'         => $credit_amt,
                'grandtotal'      => $depositAmt,
                'shipping'        => 'pickUpToStore',
            ];
    
            $checkout_arr = [
                'createdon'          => now(),
                'crontime'           => now(),
                'ip'                 => $request->ip() ?? '',
                'user_id'            => $user->id,
                'status'             => '0',
                'transaction_id'     => $tran_id,
                'job_no'      => $request->pay_no,
                'checkout_response'  => json_encode($buildCheckOut),
                'category'           => 'Purchase',
                'gateway'            => 'cash',
                'finaltotal'         => $depositAmt,
                'wallet_amt'         => $w_amt,
                'receipt_no'         => '',
                'reference'          => '',
                'paymentStatus'      => 'pending',
                'shipamount'         => 0,
                'credit_amt'         => $credit_amt,
                'grandtotal'         => $depositAmt,
            ];
    
            $paymentId = DB::table('payment_history')->insertGetId($checkout_arr);
            
            $get_log = DB::table('cus_job_temp')
                ->where('job_no', $get_data->job_no)
                ->first();
            if (!$get_log) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found.'
                ]);
            }
            
            $fareBreakdown = json_decode($get_log->fare_breakdown, true);
            $sch_status = json_decode($get_log->sch_status, true);
            
            if($get_log && $get_log->global_type != 'schedule'){
                
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                
                $firebaseDoc = $firebase->getJob($get_data->job_no);
                $job = $this->parseFirestoreFields($firebaseDoc);
                $bidder_id     = $fareBreakdown['bidder_id'] ?? null;
            
                if (!$bidder_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid bidder.'
                    ]);
                }
            
                $job['bids_details'][$bidder_id]['status'] = 'accept';
            
                $firebase->updateBidStatus(
                    $get_log->job_no,
                    $bidder_id,
                    'accept'
                );
                
                $firebase->deleteJob($get_log->job_no);
            }else{
                
                $sch_status[$fareBreakdown['bidder_date']][$fareBreakdown['bidder_id']]['status'] = 'accept';

                $pickupParts = explode(' ', $get_data->pickup_date);
                $pickupTime = $pickupParts[1] ?? '00:00:00';
                
                $newDate = $fareBreakdown['bidder_date'];
                $sch_id = $fareBreakdown['sch_id'];
                
                $bidder_id = $fareBreakdown['bidder_id'];
                
                $newPickupDateTime = $newDate . ' ' . $pickupTime;
                
                DB::table('cus_job_temp')
                    ->where('job_no', $get_data->job_no)
                    ->update([
                        'pickup_date' => $newPickupDateTime,
                        'sch_status' => json_encode($sch_status)
                    ]);
                    
                
                
                $get_sh = DB::table('schedule_dates')
                    ->where('user_id', $fareBreakdown['bidder_id'])
                    ->where('id', $sch_id)
                    ->first();
                
                if ($get_sh && $get_sh->dates_price) {
                
                    $de_code = json_decode($get_sh->dates_price, true);
                
                    foreach ($de_code as $key => $value) {
                
                        if (strpos($key, $newDate) === 0) {
                            unset($de_code[$key]);
                        }
                    }
                
                    DB::table('schedule_dates')
                        ->where('user_id', $fareBreakdown['bidder_id'])
                        ->where('id', $sch_id)
                        ->update([
                            'dates_price' => json_encode($de_code)
                        ]);
                        
                    $firebase = new \App\Services\FirebaseJobService(
                        $this->serviceAccount2['project_id'],
                        $this->getAccessToken2()
                    );
                    
                    $firebase->deleteScheduleJob($get_data->job_no);
                }
            }
            
            if (($fareBreakdown['isDiscount'] ?? '') == 'yes') {
        
                $opening        = (float) ($user->cash_points ?? 0);
                $expectedAmount = (float) ($fareBreakdown['discount'] ?? 0);
                $closing        = $opening - $expectedAmount;
        
                DB::table('walletBalance_history')->insert([
                    'userid'            => $user->id,
                    'uname'             => $user->name,
                    'umobile'           => $user->mobile,
                    'uemail'            => $user->email,
                    'opening_balance'   => $opening,
                    'total'             => $expectedAmount,
                    'closeing_balance'  => $closing,
                    'point_type'        => 'CREDIT',
                    'transaction_type'  => 'DEBIT',
                    'reward_type'       => 'JOB',
                    'global_type' => 'customer',
                    'reference_id'      => $lastId ?? null,
                    'reference_table'   => 'payment_history',
                    'ip'                => $request->ip(),
                    'createdon'         => now(),
                    'updatedon'         => now()
                ]);
        
                DB::table('customer_register')
                    ->where('id', $user->id)
                    ->update([
                        'cash_points' => $closing
                    ]);
            }
            
            $total_fare = $fareBreakdown['total_fare'];
            
            $fareBreakdown['pay_to_driver'] = $total_fare;
            $fareBreakdown['wallet'] = 0;
            $fareBreakdown['total_fare'] = $total_fare;
        
            DB::table('cus_job_temp')
                ->where('job_no', $get_log->job_no)
                ->where('user_id', $user->id)
                ->update([
                    'com'             => $fareBreakdown['com'] ?? 0,
                    'tax'             => $fareBreakdown['tax'] ?? 0,
                    'base_fare'       => $fareBreakdown['base_fare'] ?? 0,
                    'toll_fare'       => $fareBreakdown['toll_fare'] ?? 0,
                    'discount'        => $fareBreakdown['discount'] ?? 0,
                    'isDiscount'      => $fareBreakdown['isDiscount'] ?? 0,
                    'wallet_amt'      => $fareBreakdown['wallet'] ?? 0,
                    'fare'            => $total_fare ?? 0,
                    'deductAmt'       => $partPayFare,
                    'credit'          => $fareBreakdown['discount'] ?? 0,
                    'job_status'      => 'accept',
                    'payment_status'  => 'pending',
                    'assigned_to' => $fareBreakdown['bidder_id'],
                    'bids_details'   => $get_log->global_type != 'schedule' ? json_encode($job['bids_details']) : null,
                    'fare_breakdown' => json_encode($fareBreakdown),
                    'updated_at'     => now()
                ]);
                
            $columns = DB::getSchemaBuilder()->getColumnListing('cus_job_temp');
            $columns = array_filter($columns, function ($column) {
                return $column !== 'id';
            });
            $columnList = implode(',', $columns);
            
            DB::statement("
                INSERT INTO open_jobs ($columnList)
                SELECT $columnList
                FROM cus_job_temp
                WHERE job_no = ? AND user_id = ?
            ", [$get_log->job_no, $user->id]);
                
            DB::commit();
            
            if ($bidder_id) {
                dispatch(new \App\Jobs\SendFcmNotificationJob(
                    type: 'accept_notification',
                    userIds: [$bidder_id],
                    title: 'Bid Accepted',
                    body: "Great news! Your bid for Job #{$get_log->job_no} ({$get_log->from_place}) has been accepted.",
                ));
                
                $dr_details = DB::table('user_register')->where(['id' => $bidder_id, 'deletes' => '0'])->first();
                
                $pickupTime = $get_log->pickup_date 
                    ? date('d M Y, h:i A', strtotime($get_log->pickup_date))
                    : '-';
                    
                $driverName = $dr_details->name ?? 'Driver';
                $driverMobile = $dr_details->mobile ?? '-';
                
                $customerName = $user->name ?? 'Customer';
                $customerMobile = $user->mobile ?? '-';
                
                $tripType = ucwords($get_log->job_type ?? '-');
                $fare = $total_fare ?? 0;
                $noOfDays = !empty($get_log->day) ? $get_log->day : '-';

                $sendTemplateMessage = function($mobile, $templateName, $parameters) use ($request) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    $template = DB::table('wamail_templates')->where('name', $templateName)->first();
                    if (!$template) return;

                    $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";

                    $bodyParameters = [];
                    foreach ($parameters as $param) {
                        $val = ($param !== null && $param !== '') ? (string) $param : '-';
                        $bodyParameters[] = [
                            "type" => "text",
                            "text" => $val
                        ];
                    }

                    $components = [];

                    if (!empty($template->header_image)) {
                        $components[] = [
                            "type" => "header",
                            "parameters" => [
                                [
                                    "type" => "image",
                                    "image" => [
                                        "link" => $template->header_image 
                                    ]
                                ]
                            ]
                        ];
                    }

                    if (!empty($bodyParameters)) {
                        $components[] = [
                            "type" => "body",
                            "parameters" => $bodyParameters
                        ];
                    }

                    if (!empty($template->variables_json)) {
                        $buttonsConfig = json_decode($template->variables_json, true);
                        if (!empty($buttonsConfig['buttons'])) {
                            foreach ($buttonsConfig['buttons'] as $index => $btn) {
                                if ($btn['type'] === 'COPY_CODE') {
                                    $components[] = [
                                        "type" => "button",
                                        "sub_type" => "url",
                                        "index" => (string)$index,
                                        "parameters" => [
                                            [
                                                "type" => "text",
                                                "text" => (string)($parameters[0] ?? '123456')
                                            ]
                                        ]
                                    ] ;
                                }
                                if ($btn['type'] === 'URL' && strpos($btn['url'] ?? '', '{{1}}') !== false) {
                                    $components[] = [
                                        "type" => "button",
                                        "sub_type" => "url",
                                        "index" => (string)$index,
                                        "parameters" => [
                                            [
                                                "type" => "text",
                                                "text" => (string)($parameters[0] ?? '')
                                            ]
                                        ]
                                    ];
                                }
                            }
                        }
                    }

                    $templatePayload = [
                        "name" => $templateName,
                        "language" => [
                            "code" => "en_US"
                        ]
                    ];

                    if (!empty($components)) {
                        $templatePayload["components"] = $components;
                    }

                    $payload = [
                        "messaging_product" => "whatsapp",
                        "to" => $cleanPhone,
                        "type" => "template",
                        "template" => $templatePayload
                    ];

                    $reqTime = now();
                    \Illuminate\Support\Facades\Log::info("WhatsApp Request Payload [{$templateName}]:", $payload);

                    $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                    $resTime = now();
                    $body = $response->json();
                    
                    if (!$response->successful()) {
                        \Illuminate\Support\Facades\Log::error("WhatsApp API Error [{$templateName}]:", ['status' => $response->status(), 'response' => $body]);
                    } else {
                        \Illuminate\Support\Facades\Log::info("WhatsApp API Success [{$templateName}]:", $body);
                    }

                    $messageId = $body['messages'][0]['id'] ?? null;
                    $isSuccess = $response->successful();

                    DB::table('smslog')->insert([
                        'gateway' => 'fbWhatsapp',
                        'subject' => $templateName,
                        'details' => json_encode($parameters),
                        'mobile' => $cleanPhone,
                        'ip' => request()->ip() ?? '',
                        'datetime' => now(),
                        'token_response' => json_encode($body),
                        'status' => $isSuccess ? 'sent' : 'failed',
                        'reference_id' => $messageId ?? '',
                        'site' => 'CUSTOMER',
                        'REQ_Time' => $reqTime,
                        'RES_Time' => $resTime,
                        'smsdetails' => json_encode($payload),
                        'smsstatus' => $isSuccess ? 'Sent' : 'Failed',
                        'smssendstatus' => $isSuccess ? '1' : '0',
                        'response' => $response->body(),
                        'isResend' => 'NO'
                    ]);
                };

                if (!empty($driverMobile)) {
                    $sendTemplateMessage(
                        $driverMobile, 
                        'customer_details_to_driver', 
                        [
                            $driverName,           
                            $customerName,         
                            $customerMobile,       
                            $get_log->from_place,  
                            $get_log->to_place,    
                            $noOfDays,             
                            $pickupTime,           
                            $tripType,             
                            $fare,                 
                            'Cash'                 
                        ]
                    );
                }

                if (!empty($customerMobile)) {
                    $sendTemplateMessage(
                        $customerMobile, 
                        'drivers_details_to_customers_informations_data', 
                        [
                            $customerName,         
                            $driverName,           
                            $driverMobile,         
                            $get_log->from_place,  
                            $get_log->to_place,    
                            $pickupTime,           
                            $tripType,             
                            $fare,                 
                            'Cash'                 
                        ]
                    );
                    
                    $sendTemplateMessage(
                        $customerMobile, 
                        'otp_start_ride', 
                        [$get_log->otp]
                    );
                }
            }
                
            return response()->json([
                'status'  => true,
                'data'    => [
                    // 'job_id' => $openJobId,
                    'job_no'      => $get_log->job_no,
                    // 'v_details' => $get_v ? $get_v->vehicle_details : null,
                    'paid_amount' => $depositAmt,
                    'ride_info' => $fareBreakdown
                ],
                'message' => 'Ride confirmed successfully.'
            ], 200);
            
    
        } catch (\Exception $e) {
    
            DB::rollBack();
            \Log::info('Cash to driver failed ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Unable to pay via cash, try again another payment type.',
                'error' => 'Unable to pay via cash, try again another payment type.'
            ]);
        }
    }
    
    public function webCashOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pay_no'   => 'required',
            'credit_pay' => 'nullable',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $user = null;
        
        $depositAmt = 0;
        $finalAmt = 0;
        
        if($request->pay_no != ''){
            $get_data = DB::table('cus_job_temp')->where(['user_id' => 0, 'job_no' => $request->pay_no])->whereIn('job_status', ['created', 'bidding', 'schedule'])->first();
            
            if($get_data){
                $depositAmt = (int) $get_data->pay_amt;
                $finalAmt = $depositAmt;
            }else{
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Booking not found or cancelled',
                    'error'   => 'Booking not found or cancelled'
                ]);
            }
        }
        
        $fr_details = json_decode($get_data->fare_breakdown, true);
        
        if($get_data->bids_details){
            
            $bid_details = json_decode($get_data->bids_details, true);
            
            $bids = $bid_details[$fr_details['bidder_id']];
            
            if($bids['amount'] != $fr_details['actual_driver_amt']){
                
                return response()->json([
                    'status' => false,
                    'message' => 'The price was changed. Refresh to proceed with payment.'
                ]);
            }
            
        }
        
        $wallet_amt = 0;
        $wtotalFare    = (float) ($fr_details['total_fare'] ?? 0);
        $credit_amt = 0;
        
        $creditPayFare = $fr_details['discount'] ?? 0;
        $partPayFare   = $fr_details['part_pay_fare'] ?? 0;
        $taxFare       = $fr_details['tax'] ?? 0;
        
        if ($fr_details['isDiscount'] == 'yes') {
            $credit_amt = $creditPayFare;
            
        } else {
            $credit_amt = 0;
        }
        $w_amt = 0;
        
        // return $credit_amt;

        $alreadyPaid = DB::table('payment_history')
            ->where('job_no', $get_data->job_no)
            ->where('gateway', 'cash')
            ->exists();
            
        $alreadyPaidCus = DB::table('cus_job_temp')
            ->where('job_no', $get_data->job_no)
            ->where('payment_status', 'paid')
            ->exists();
    
        if ($alreadyPaid || $alreadyPaidCus) {
            return response()->json([
                'status'  => false,
                'message' => 'Payment already completed for this job.',
                'data'    => []
            ], 200);
        }
        
        $w_amt = $fr_details['wallet'];
        
        $currency = 'INR';
        
        $amountInPaise = (int) round($depositAmt * 100);
        
        DB::beginTransaction();
    
        try {
            // Get last ID
            $lastPayment = DB::table('payment_history')
                ->select('id')
                ->orderBy('id', 'desc')
                ->first();
    
            $lastId = $lastPayment->id ?? 0;
    
            $tran_id = 'GRB' . uniqid() . date('Hii') . ($lastId + 1);

            $buildCheckOut = [
                'userID'          => 0,
                'depositAmt'      => $depositAmt,
                'existWalletAmt'  => 0,
                'existCash_points'  => 0,
                'finalTotal'      => $fr_details['actual_total_fare'],
                'discount'        => 0,
                'shipamount'      => 0,
                'wallet_amt'         => $w_amt,
                'credit_amt'         => $credit_amt,
                'grandtotal'      => $depositAmt,
                'shipping'        => 'pickUpToStore',
            ];
    
            $checkout_arr = [
                'createdon'          => now(),
                'crontime'           => now(),
                'ip'                 => $request->ip() ?? '',
                'user_id'            => 0,
                'status'             => '0',
                'transaction_id'     => $tran_id,
                'job_no'     => $request->pay_no,
                'checkout_response'  => json_encode($buildCheckOut),
                'category'           => 'Purchase',
                'gateway'            => 'cash',
                'finaltotal'         => $depositAmt,
                'wallet_amt'         => $w_amt,
                'receipt_no'         => '',
                'reference'          => '',
                // 'renewalStatus'      => 'RECHARGE',
                'paymentStatus'      => 'pending',
                'shipamount'         => 0,
                // 'wallet_amt'         => 0,
                'credit_amt'         => $credit_amt,
                'grandtotal'         => $depositAmt,
            ];
    
            $paymentId = DB::table('payment_history')->insertGetId($checkout_arr);
            
            $get_log = DB::table('cus_job_temp')
                ->where('job_no', $get_data->job_no)
                ->first();
            if (!$get_log) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found.'
                ]);
            }
            
            $fareBreakdown = json_decode($get_log->fare_breakdown, true);
            $sch_status = json_decode($get_log->sch_status, true);
            
            if($get_log && $get_log->global_type != 'schedule'){
                
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                
                $firebaseDoc = $firebase->getJob($get_data->job_no);
                $job = $this->parseFirestoreFields($firebaseDoc);
                $bidder_id     = $fareBreakdown['bidder_id'] ?? null;
                
                if (!$bidder_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid bidder.'
                    ]);
                }
            
                $job['bids_details'][$bidder_id]['status'] = 'accept';
                
                $firebase->updateBidStatus(
                    $get_log->job_no,
                    $bidder_id,
                    'accept'
                );
                
                $firebase->deleteJob($get_log->job_no);
                
            }
            
            $total_fare = $fareBreakdown['total_fare'];
            
            $fareBreakdown['pay_to_driver'] = $total_fare;
            $fareBreakdown['wallet'] = 0;
            $fareBreakdown['total_fare'] = $total_fare;
        
            DB::table('cus_job_temp')
                ->where('job_no', $get_log->job_no)
                ->where('user_id', 0)
                ->update([
                    'com'            => $fareBreakdown['com'] ?? 0,
                    'tax'            => $fareBreakdown['tax'] ?? 0,
                    'base_fare'      => $fareBreakdown['base_fare'] ?? 0,
                    'toll_fare'      => $fareBreakdown['toll_fare'] ?? 0,
                    'discount'       => $fareBreakdown['discount'] ?? 0,
                    'isDiscount'     => $fareBreakdown['isDiscount'] ?? 0,
                    'wallet_amt'     => $fareBreakdown['wallet'] ?? 0,
                    'fare'           => $total_fare ?? 0,
                    // 'pay_amt'        => $depositAmt,
                    'deductAmt'      => $partPayFare,
                    'credit'         => $fareBreakdown['discount'] ?? 0,
                    'job_status'     => 'accept',
                    'payment_status' => 'pending',
                    'assigned_to' => $fareBreakdown['bidder_id'],
                    // 'assigned_cab' => $fareBreakdown['b_cab_no'],
                    'bids_details'   => $get_log->global_type != 'schedule' ? json_encode($job['bids_details']) : null,
                    'fare_breakdown' => json_encode($fareBreakdown),
                    'updated_at'     => now()
                ]);
                
            $columns = DB::getSchemaBuilder()->getColumnListing('cus_job_temp');

            /* Remove 'id' column */
            $columns = array_filter($columns, function ($column) {
                return $column !== 'id';
            });
            
            /* Convert array to comma-separated string */
            $columnList = implode(',', $columns);
            
            DB::statement("
                INSERT INTO open_jobs ($columnList)
                SELECT $columnList
                FROM cus_job_temp
                WHERE job_no = ? AND user_id = ?
            ", [$get_log->job_no, 0]);
                
            DB::commit();
            
            if ($bidder_id) {
                dispatch(new \App\Jobs\SendFcmNotificationJob(
                    type: 'accept_notification',
                    userIds: [$bidder_id],
                    title: 'Bid Accepted',
                    body: "Great news! Your bid for Job #{$get_log->job_no} ({$get_log->from_place}) has been accepted.",
                ));
            }
                
            return response()->json([
                'status'  => true,
                'data'    => [
                    'job_no'      => $get_log->job_no,
                    'paid_amount' => $depositAmt,
                    'ride_info' => $fareBreakdown
                ],
                'message' => 'Ride confirmed successfully.'
            ], 200);
            
    
        } catch (\Exception $e) {
    
            DB::rollBack();
            \Log::info('Cash to driver failed ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Unable to pay via cash, try again another payment type.',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function paymentVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'tx_id'               => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors(), 'data' => []], 200);
        }
    
        $user      = $request->user();
        $paymentId = $request->razorpay_payment_id;
        $orderId   = $request->razorpay_order_id;
        $signature = $request->razorpay_signature;
        $trac_Id   = $request->tx_id;
        $txId      = null;
    
        $tx = DB::table('payment_history')
            ->where('transaction_id', $trac_Id)
            ->where('user_id', $user->id)
            ->first();
    
        if (!$tx) {
            return response()->json(['status' => false, 'message' => 'Transaction not found', 'data' => []], 200);
        }
    
        $txId = $tx->id;
    
        $ch_wab = DB::table('walletBalance_history')
            ->where('reference_id', $txId)
            ->where('userid', $user->id)
            ->where('point_type', 'CREDIT')
            ->first();
    
        if ($ch_wab && $tx->credit_amt != 0) {
            return response()->json(['status' => false, 'message' => 'Credit point already deducted', 'data' => []], 200);
        }
    
        $ch_tx = DB::table('payment_history')
            ->where('id', $txId)
            ->where('user_id', $user->id)
            ->where('paymentStatus', 'success')
            ->first();
    
        $ch_wal = DB::table('cus_job_temp')
            ->where('job_no', $tx->job_no)
            ->where('user_id', $user->id)
            // ->where('payment_status', 'paid')
            ->whereNotIn('job_status', ['created', 'bidding', 'schedule'])
            ->first();
    
        if ($ch_tx || $ch_wal) {
            return response()->json(['status' => false, 'message' => 'Payment already done or Booking cancelled', 'data' => []], 200);
        }
    
        $checkout = json_decode($tx->checkout_response, true);
        $expectedAmount = $checkout['finalTotal'] ?? 0;
    
        $attributes = [
            'razorpay_order_id'   => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature'  => $signature
        ];
    
        // try {
    
        //     $this->razorpay->utility->verifyPaymentSignature($attributes);
    
        // } catch (\Exception $e) {
    
        //     DB::table('payment_history')->where('id', $txId)->update([
        //         'paymentStatus' => 'failed',
        //         'razorpay_payment_id' => $paymentId,
        //         'razorpay_signature' => $signature,
        //     ]);
    
        //     DB::table('payment_history_log')->insert([
        //         'payment_history_id' => $txId,
        //         'transaction_id' => $tx->transaction_id,
        //         'gateway' => 'razorpay',
        //         'user_id' => $user->id,
        //         'paymentStatus' => 'failed',
        //         'pay_response' => $tx->checkout_response,
        //         'response' => $e->getMessage(),
        //         'createdon' => now(),
        //     ]);
    
        //     DB::table('cus_job_temp')
        //         ->where('job_no', $tx->job_no)
        //         ->where('user_id', $user->id)
        //         ->update(['payment_status' => 'failed']);
    
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid payment signature',
        //         'data' => []
        //     ]);
        // }
    
        try {
    
            $payment = $this->razorpay->payment->fetch($paymentId);
    
        } catch (\Exception $e) {
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'failed',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id' => $tx->transaction_id,
                'gateway' => 'razorpay',
                'user_id' => $user->id,
                'paymentStatus' => 'failed',
                'pay_response' => $tx->checkout_response,
                'response' => $e->getMessage(),
                'createdon' => now(),
            ]);
    
            DB::table('cus_job_temp')
                ->where('job_no', $tx->job_no)
                ->where('user_id', $user->id)
                ->update(['payment_status' => 'failed']);
    
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch payment details',
                'data' => []
            ]);
        }
    
        if (!isset($payment['status']) || $payment['status'] != 'captured') {
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'failed'
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id' => $tx->transaction_id,
                'gateway' => 'razorpay',
                'user_id' => $user->id,
                'paymentStatus' => 'failed',
                'pay_response' => $tx->checkout_response,
                'response' => json_encode($payment),
                'createdon' => now(),
            ]);
    
            DB::table('cus_job_temp')
                ->where('job_no', $tx->job_no)
                ->where('user_id', $user->id)
                ->update(['payment_status' => 'failed']);
    
            return response()->json([
                'success' => false,
                'message' => 'Payment not captured',
                'data' => []
            ]);
        }
    
        $paidAmount = $payment['amount'] / 100;
    
        DB::beginTransaction();
    
        try {
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'success',
                'status' => '1',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id' => $tx->transaction_id,
                'gateway' => 'razorpay',
                'user_id' => $user->id,
                'paymentStatus' => 'success',
                'pay_response' => $tx->checkout_response,
                'response' => json_encode($payment),
                'createdon' => now(),
            ]);
    
            $get_log = DB::table('cus_job_temp')
                ->where('job_no', $tx->job_no)
                ->first();
    
            $fareBreakdown = json_decode($get_log->fare_breakdown, true);
            $sch_status = json_decode($get_log->sch_status, true);
    
            $bidder_id = $fareBreakdown['bidder_id'] ?? null;
            $newPickupDateTime = $get_log->pickup_date;
    
            if ($get_log && $get_log->global_type != 'schedule') {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
    
                $firebaseDoc = $firebase->getJob($tx->job_no);
    
                $job = $this->parseFirestoreFields($firebaseDoc);
    
                if (!$firebaseDoc) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Job not found in Firebase.'
                    ], 200);
                }
    
                if (!$bidder_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid bidder.'
                    ]);
                }
    
                $job['bids_details'][$bidder_id]['status'] = 'accept';
    
                $firebase->updateBidStatus(
                    $get_log->job_no,
                    $bidder_id,
                    'accept'
                );
                
                $firebase->deleteJob($get_log->job_no);
    
            } else {
    
                $pickupParts = explode(' ', $get_log->pickup_date);
                $pickupTime = $pickupParts[1] ?? '00:00:00';
    
                $newDate = $fareBreakdown['bidder_date'];
                $sch_id = $fareBreakdown['sch_id'];
    
                $newPickupDateTime = $newDate . ' ' . $pickupTime;
    
                $sch_status[$fareBreakdown['bidder_date']][$fareBreakdown['bidder_id']]['status'] = 'accept';
    
                DB::table('cus_job_temp')
                    ->where('job_no', $get_log->job_no)
                    ->update([
                        'pickup_date' => $newPickupDateTime,
                        'sch_status' => json_encode($sch_status)
                    ]);
                    
                $get_sh = DB::table('schedule_dates')
                        ->where('user_id', $bidder_id)
                        ->where('id', $sch_id)
                        ->first();
                    
                if ($get_sh && $get_sh->dates_price) {
                
                    $de_code = json_decode($get_sh->dates_price, true);
                
                    foreach ($de_code as $key => $value) {
                
                        if (strpos($key, $newDate) === 0) {
                            unset($de_code[$key]);
                        }
                    }
                
                    DB::table('schedule_dates')
                        ->where('user_id', $bidder_id)
                        ->where('id', $sch_id)
                        ->update([
                            'dates_price' => json_encode($de_code)
                        ]);
                }
                    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount2['project_id'],
                    $this->getAccessToken2()
                );
                
                $firebase->deleteScheduleJob($get_log->job_no);
            }
            
            $total_fare = 
                $fareBreakdown['base_fare'] +
                $fareBreakdown['com'] +
                $fareBreakdown['tax'] +
                $fareBreakdown['toll_fare'] -
                ($fareBreakdown['isDiscount'] == 'yes' ? $fareBreakdown['discount'] : 0);
            
            DB::table('cus_job_temp')
            ->where('job_no', $get_log->job_no)
            ->where('user_id', $user->id)
            ->update([
                'com'            => $fareBreakdown['com'] ?? 0,
                'tax'            => $fareBreakdown['tax'] ?? 0,
                'base_fare'      => $fareBreakdown['base_fare'] ?? 0,
                'toll_fare'      => $fareBreakdown['toll_fare'] ?? 0,
                'discount'       => $fareBreakdown['discount'] ?? 0,
                'isDiscount'     => $fareBreakdown['isDiscount'] ?? '',
                'wallet_amt'     => $fareBreakdown['wallet'] ?? 0,
                'fare'           => $total_fare ?? 0,
                'pay_amt'        => $paidAmount,
                'credit'         => $fareBreakdown['discount'] ?? 0,
                // 'assigned_cab'         => $fareBreakdown['b_cab_no'] ?? 0,
                'job_status'     => 'accept',
                'payment_status' => 'paid',
                'assigned_to' => $bidder_id,
                'bids_details'   => $get_log->global_type != 'schedule' ? json_encode($job['bids_details']) : null,
                'fare_breakdown' => $get_log->fare_breakdown,
                'updated_at'     => now()
            ]);
            
            $columns = DB::getSchemaBuilder()->getColumnListing('cus_job_temp');

            $columns = array_filter($columns, function ($column) {
                return $column !== 'id';
            });
            
            $columnList = implode(',', $columns);
            
            DB::statement("
                INSERT INTO open_jobs ($columnList)
                SELECT $columnList
                FROM cus_job_temp
                WHERE job_no = ? AND user_id = ?
            ", [$get_log->job_no, $user->id]);
            
             if($fareBreakdown['isDiscount'] == 'yes'){
                    
                $opening = $user->cash_points??0;
                $expectedAmount = $fareBreakdown['discount'];
                $closing = $opening - $expectedAmount;
        
                DB::table('walletBalance_history')->insert([
                    'userid'            => $user->id,
                    'uname'             => $user->name,
                    'umobile'           => $user->mobile,
                    'uemail'            => $user->email,
                    'opening_balance'   => $opening,
                    'total'             => $expectedAmount,
                    'closeing_balance'  => $closing,
                    'point_type'        => 'CREDIT',
                    'transaction_type'  => 'DEBIT',
                    'reward_type'       => 'JOB',
                    'reference_id'      => $txId,
                    'global_type' => 'customer',
                    'reference_table'   => 'payment_history',
                    'ip'                => $request->ip(),
                    'createdon'         => now(),
                    'updatedon'         => now()
                ]);
                
                DB::table('customer_register')->where('id', $user->id)->update([
                    'cash_points' => $closing
                ]);
            }
            
            if($fareBreakdown['isWallet'] == 'yes'){
                
                
                $walletBalance  = $user->walletBalance??0;
                $finaltotal     = (float) $fareBreakdown['wallet'];
                $closingBalance = $walletBalance - $finaltotal;
            
                $payArr = [
                    "userid"            => $user->id,
                    "uname"             => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                    "umobile"           => auth()->user()->mobile,
                    "uemail"            => auth()->user()->email,
                    'opening_balance'   => $walletBalance,
                    'total'             => $finaltotal,
                    'closeing_balance'  => $closingBalance,
                    'point_type'        => 'WALLET',
                    'transaction_type'  => 'DEBIT',
                    'reward_type'       => 'JOB',
                    'card_no'           => '',
                    'reference_id'      => $txId ?? '',
                    'reference_table'   => 'payment_history',
                    'global_type' => 'customer',
                    'ip'                => $request->ip() ?? '',
                    'createdon'         => now()
                ];
                
            
                $wallet_history = DB::table('walletBalance_history')
                    ->insertGetId($payArr);
                    
                    
                DB::table('customer_register')->where('id', $user->id)->update([
                    'walletBalance' => $closingBalance
                ]);
            }
        
            $firebase->deleteJob($tx->job_no);
            
            // DB::commit();
            
            $get_v = DB::table('user_register')->where(['id' => $bidder_id, 'deletes' => '0'])->first();
            
            $emailDetails = [
                'name'  => auth()->user()->name,
                'email' => auth()->user()->email
            ];
            
            $payDetails = [
                'wallet'  => 0,
                'credit'  => 0,
                'balance' => 0,
                'upi'     => $tx->grandtotal,
            ];
            
            $jobDetails = json_decode($get_log->fare_breakdown, true);
            
            $jobDetails['user_id'] = auth()->id();
            
            if (!empty($emailDetails) && !empty($jobDetails)) {
                GenerateInvoiceJob::dispatch(
                    $jobDetails,
                    $emailDetails,
                    $payDetails
                );
            }
            
    
            DB::commit();
    
            
            if ($bidder_id) {
                dispatch(new \App\Jobs\SendFcmNotificationJob(
                    type: 'accept_notification',
                    userIds: [$bidder_id],
                    title: 'Bid Accepted',
                    body: "Great news! Your bid for Job #{$get_log->job_no} ({$get_log->from_place}) has been accepted.",
                ));
            }
            
            $dr_details = DB::table('user_register')->where(['id' => $bidder_id, 'deletes' => '0'])->first();
                
            // $u_d = json_decode($get_log->user_details, true) ?? [];
            
            $pickupTime = $get_log->pickup_date 
                ? date('d M Y, h:i A', strtotime($get_log->pickup_date))
                : '-';
                
            $driverName = $dr_details->name ?? 'Driver';
            $driverMobile = $dr_details->mobile ?? '-';
            
            $customerName = auth()->user()->name;
            $customerMobile = auth()->user()->mobile;
            
            $tripType = ucwords($get_log->job_type ?? '-');
            $fare = $total_fare ?? 0;

$tripExtra = '';

if ($tripType != 'Oneway') {
    $tripExtra = "🗓 *No. of Days:* {$get_log->day}\n";
}
                
$message = "📢 *New Ride Assigned!*

Hello *{$driverName}*,
You have a new booking. Please find the passenger details below:

👤 *Passenger Name:* {$customerName}
📞 *Contact Number:* {$customerMobile}
📍 *Pickup Location:* {$get_log->from_place}
🏁 *Drop Location:* {$get_log->to_place}
{$tripExtra}
🕒 *Pickup Time:* {$pickupTime}
💼 *Trip Type:* {$tripType}
💰 *Fare Details:* ₹{$fare} (Payment Mode: Online)

Kindly contact the passenger and proceed on time.
Safe driving! 🚘

— *GoRide Run Pvt Ltd*";
                
                if (!empty($driverMobile)) {
                    Controller::sendNotification([
                        'mobile'            => $driverMobile,
                        'templateName'      => 'national_draw_verification',
                        'language'          => 'en',
                        'templateBodyParam' => [],
                        'messages'          => $message,
                        'resend'            => false
                    ]);
                }
                
$messageCus = "🚗 *GoRide Run Pvt Ltd – Driver Assigned*

Hello *{$customerName}*,
Your ride has been confirmed! Here are your driver details:

👨 *Driver Name:* {$driverName}
📞 *Contact Number:* {$driverMobile}
📍 *Pickup Location:* {$get_log->from_place}
🏁 *Drop Location:* {$get_log->to_place}
{$tripExtra}
🕒 *Pickup Time:* {$pickupTime}
💼 *Trip Type:* {$tripType}
💰 *Fare Details:* ₹{$fare} (Payment Mode: Online)

Please share OTP {$get_log->otp} with your driver to start the ride.

📝 Note: Please be ready at the pickup location on time. You can contact the driver directly if needed.
Have a safe and pleasant journey! 😊

— *GoRide Run Pvt Ltd*";
                
                if (!empty($customerMobile)) {
                    Controller::sendNotification([
                        'mobile'            => $customerMobile,
                        'templateName'      => 'national_draw_verification',
                        'language'          => 'en',
                        'templateBodyParam' => [],
                        'messages'          => $messageCus,
                        'resend'            => false
                    ]);
                }
            
            return response()->json([
                'status' => true,
                'data' => [
                    'job_id' => $get_log->id,
                    'job_no' => $tx->job_no,
                    'paid_amount' => $paidAmount,
                    'ride_info' => json_decode($get_log->fare_breakdown)
                ],
                'message' => 'Ride confirmed successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            
            \Log::info('View Booking failed API Error: ' . $e->getMessage());
    
            DB::rollBack();
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'failed'
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id' => $tx->transaction_id,
                'gateway' => 'razorpay',
                'user_id' => $user->id,
                'paymentStatus' => 'failed',
                'pay_response' => $tx->checkout_response,
                'response' => $e->getMessage(),
                'createdon' => now(),
            ]);
    
            DB::table('cus_job_temp')
                ->where('job_no', $tx->job_no)
                ->where('user_id', $user->id)
                ->update(['payment_status' => 'failed']);
    
            return response()->json([
                'success' => false,
                'message' => 'Booking failed',
                'data' => $e->getMessage()
            ]);
        }
    }
    
    public function bookingHistory(Request $request)
    {
        
        $user = auth()->user();
        
        $currentTime = now()->toDateTimeString();
            
        if($request->status && in_array($request->status, ['created', 'accept', 'cancelled'])){
            
            $userId = $user->id;
            
            $userId = $user->id;

            $book_history = DB::table('cus_job_temp as ct')
            
                /* Extract accepted driver id from JSON key */
                // ->leftJoin('user_register as ur', function ($join) {
                //     $join->on('ur.id', '=', DB::raw("
                //         REPLACE(
                //             REPLACE(
                //                 JSON_UNQUOTE(
                //                     JSON_EXTRACT(
                //                         JSON_SEARCH(ct.bids_details,'one','accept',NULL,'$.*.status'),
                //                         '$'
                //                     )
                //                 ),
                //             '$.\"',''),
                //         '\".status','')
                //     "));
                // })
            
                /* Driver KYC */
                ->leftJoin('user_register as ur', 'ur.id', '=', 'ct.assigned_to')
                ->leftJoin('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
            
                ->where('ct.user_id', $userId)
                ->where('ct.job_status', $request->status)
                ->whereIn('ct.global_type', ['customer','mock','open'])
            
                ->select([
                    'ct.id',
                    'ct.job_no',
                    'ct.job_type',
                    'ct.from_place',
                    'ct.to_place',
                    'ct.pickup_date',
                    'ct.dropoff_date',
                    'ct.day',
                    DB::raw("
                        CASE
                            WHEN ct.job_status != 'accept'
                            THEN NULL
                            
                            ELSE ur.id
                        END as driver_id
                    "),
                    'ct.isView',

                    DB::raw("
                        COALESCE(ur.profile_img_url, kd.selfie_url) as profile_img_url
                    "),
                    
                    'ur.name',

                    DB::raw("
                        CASE
                            WHEN ct.job_status != 'accept' AND ct.isView = 0
                            THEN 'XXX-XXX-XXXX'
                        
                            WHEN ct.isView = 0 && ct.job_status = 'accept'
                                 AND ct.pickup_date > DATE_ADD('$currentTime', INTERVAL 1 HOUR)
                            THEN ur.mobile
                        
                            ELSE ur.mobile
                        END as mobile
                    "),
            
                    DB::raw("
                        'Mobile number will appear 1 hour before the pickup time. If you choose to view it earlier, the paid amount and credit points will not be refundable as per the refund policy.' as mobile_text
                    "),
            
                    DB::raw("
                        JSON_UNQUOTE(JSON_EXTRACT(ur.vehicle_details,'$.rc_number'))
                        as rc_number
                    "),
                    
                    DB::raw("
                        JSON_UNQUOTE(JSON_EXTRACT(ur.vehicle_details,'$.vehicle'))
                        as vehicle_image
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ct.pass_count = 'mini'
                            THEN '4 Mini'
                            ELSE ct.pass_count - 1
                        END as pass_count
                    "),
            
                    DB::raw("
                        CONCAT('" . config('app.url') . "booking-information/', ct.preview_hash)
                        as preview
                    "),
            
                    DB::raw("
                        IF(ct.job_status='accept','confirmed',ct.job_status)
                        as job_status
                    "),
            
                    'ct.payment_status as paymentStatus',
                    
                    DB::raw("
                        CASE
                            WHEN ct.job_status = 'accept' 
                                 AND ph.paymentStatus = 'success' 
                                 AND ph.gateway = 'wallet'
                            THEN ct.pay_amt
                    
                            WHEN ct.job_status = 'accept' 
                                 AND ph.paymentStatus = 'success' 
                                 AND ph.gateway = 'razorpay'
                            THEN ph.wallet_amt
                    
                            ELSE 0
                        END as wallet_amt
                    "),
                
                    DB::raw("
                        CASE
                            WHEN ct.job_status = 'accept' AND ph.paymentStatus = 'success' AND ph.gateway = 'razorpay'
                            THEN ct.pay_amt
                            ELSE 0
                        END as paid_amt
                    "),
                
                    DB::raw("
                        CASE
                            WHEN ct.job_status = 'accept' AND ph.paymentStatus = 'success'
                            THEN ct.credit
                            ELSE 0
                        END as credit
                    "),
                    
                    DB::raw("
                        CASE
                            WHEN ct.pay_amt = JSON_UNQUOTE(JSON_EXTRACT(ct.fare_breakdown, '$.total_fare'))
                            THEN 0
                            ELSE JSON_UNQUOTE(JSON_EXTRACT(ct.fare_breakdown, '$.pay_to_driver'))
                        END as driver_amt
                    "),
                    'cf.id as feed_id',
                    'ct.fare as total_fare'
                ])
            
                ->orderByDesc('ct.id')
                ->limit(20)
                ->get();
            
        }else{
            
            $userId = $user->id;
            $now = now();
            
            $book_history = DB::table('cus_job_temp as ct')
                ->leftJoin('payment_history as ph', function ($join) {
                    $join->on('ct.job_no', '=', 'ph.job_no');
                })
                ->leftJoin('user_register as ur', 'ur.id', '=', 'ct.assigned_to')
                ->leftJoin('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
                ->leftJoin('customer_feedback as cf', 'cf.job_id', '=', 'ct.id')
                // ->leftJoin('owner_vehicle_list as vl', 'vl.rc_number', '=', 'ct.assigned_cab')
            
                ->where('ct.user_id', $userId)
                ->whereIn('ct.global_type', ['customer','mock','open','schedule'])
                ->where(function ($q) use ($now) {
                    $q->whereIn('ct.job_status', ['accept','cancelled'])
                      ->orWhere('ct.pickup_date', '>=', $now);
                })
            
                ->select([
                    'ct.id',
                    'ct.job_no',
                    'ct.job_type',
                    'ct.from_place',
                    'ct.to_place',
                    'ct.pickup_date',
                    'ct.dropoff_date',
                    'ct.day',
            
                    // DB::raw("IF(ct.job_status != 'accept', NULL, ur.id) as driver_id"),
                    // AND vl.id IS NULL
                    // WHEN ct.job_status = 'accept' AND vl.id IS NOT NULL
                    // AND ct.pickup_date >= '$now'
                    // THEN vl.vehicle_details 
                    DB::raw("
                        CASE 
                            WHEN ct.job_status = 'accept' 
                            AND ct.pickup_date >= '$now'
                            THEN ur.id 
                            ELSE NULL 
                        END as driver_id
                    "),
                    DB::raw("
                        CASE 
                            WHEN ct.job_status = 'accept' 
                            AND ct.pickup_date >= '$now'
                            THEN ur.vehicle_details
                            ELSE NULL 
                        END as vehicle_details
                    "),
                    // DB::raw("IF(ct.job_status != 'accept', NULL, ur.vehicle_details) as vehicle_details"),
            
                    'ct.isView',
            
                    DB::raw("COALESCE(ur.profile_img_url, kd.selfie_url) as profile_img_url"),
                    'ur.name',
            
                    DB::raw("
                        CASE
                            WHEN ct.job_status != 'accept' AND ct.isView = 0 THEN 'XXX-XXX-XXXX'
                            WHEN ct.isView = 0 AND ct.job_status = 'accept'
                                 AND ct.pickup_date > DATE_ADD('$now', INTERVAL 1 HOUR)
                            THEN ur.mobile
                            ELSE ur.mobile
                        END as mobile
                    "),
            
                    DB::raw("'Mobile number will appear 1 hour before the pickup time. If you choose to view it earlier, the paid amount and credit points will not be refundable as per the refund policy.' as mobile_text"),
                    DB::raw("'Cancellation policy text.' as cancel_text"),
            
                    DB::raw("ur.vehicle_details->>'$.rc_number' as rc_number"),
                    // DB::raw("ur.vehicle_details->>'$.vehicle' as vehicle_image"),
                    
                    //  AND vl.id IS NULL 
                    
                    // WHEN ct.job_status = 'accept' 
                    //      AND vl.id IS NOT NULL 
                    //      AND ct.pickup_date >= '$now'
                    // THEN vl.vehicle_details->>'$.vehicle'
                    DB::raw("
                        CASE 
                            WHEN ct.job_status = 'accept' 
                                 AND ct.pickup_date >= '$now'
                            THEN ur.vehicle_details->>'$.vehicle'
                            ELSE NULL 
                        END as vehicle_image
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ct.pass_count = 'mini' THEN '4 Mini'
                            ELSE ct.pass_count - 1
                        END as pass_count
                    "),
            
                    DB::raw("CONCAT('" . config('app.url') . "booking-information/', ct.preview_hash) as preview"),
            
                    DB::raw("
                        CASE
                            WHEN ct.job_status = 'accept' AND ct.pickup_date >= '$now'
                            THEN 'confirmed'
                    
                            WHEN ct.job_status = 'accept' AND ct.pickup_date < '$now'
                            THEN 'completed'
                    
                            ELSE ct.job_status
                        END AS job_status
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ph.gateway = 'cash'
                            THEN 'cash'
            
                            ELSE ct.payment_status
                        END as paymentStatus
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ph.paymentStatus = 'success' AND ph.gateway = 'wallet'
                            THEN ct.pay_amt
            
                            WHEN ph.paymentStatus = 'success' AND ph.gateway = 'razorpay'
                            THEN ph.wallet_amt
            
                            ELSE 0
                        END as wallet_amt
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ph.paymentStatus = 'success' AND ph.gateway = 'razorpay'
                            THEN ct.pay_amt
                            ELSE 0
                        END as paid_amt
                    "),
            
                    DB::raw("
                        CASE
                            WHEN (ph.paymentStatus = 'success' AND ct.isDiscount = 'yes') OR (ph.gateway = 'cash' AND ct.isDiscount = 'yes')
                            THEN ct.credit
                            ELSE 0
                        END as credit
                    "),
            
                    DB::raw("
                        CASE
                            WHEN ct.pay_amt = ct.fare_breakdown->>'$.total_fare' AND ph.gateway != 'cash'
                            THEN 0
                            WHEN ph.gateway = 'cash'
                            THEN ct.fare_breakdown->>'$.total_fare'
                            ELSE ct.fare_breakdown->>'$.pay_to_driver'
                        END as driver_amt
                    "),
            
                    'cf.id as feed_id',
                    'ct.fare as total_fare',
                    
                    DB::raw("
                        CASE
                            WHEN ct.isDiscount = 'yes' AND ct.credit != 0
                            THEN ct.fare + ct.credit
                            ELSE NULL
                        END as actual_total_fare
                    "),
                    'ct.otp',
                    'ct.otpVerify',
                    DB::raw("'yes' as isOtp")
                    
            
                    // DB::raw("
                    //     IF(
                    //         ct.isDiscount = 'yes',
                    //         ct.fare + ct.discount,
                    //         ct.fare
                    //     ) AS total_fare
                    // ")
                ])
            
                ->orderByDesc('ct.id')
                ->limit(20)
                ->get();
            
        }
        
        return response()->json([
            'status' => true,
            'data'  => $book_history,
            'message' => 'Booking History Retrieved.',
        ], 200);
    }
    
    public function viewContact(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'job_no' => 'required|exists:cus_job_temp,job_no'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                    'data'    => []
                ], 422);
            }
    
            $user = $request->user();
    
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Authentication required.',
                    'data'    => []
                ], 401);
            }
    
            $job = DB::table('cus_job_temp')
                ->where('job_no', $request->job_no)
                ->where('user_id', $user->id)
                ->where('job_status', 'accept')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid job or access denied.',
                    'data'    => []
                ], 200);
            }
            
            $pickupTime = \Carbon\Carbon::parse($job->pickup_date);
            $now        = now();
    
            if ($pickupTime->diffInMinutes($now, false) >= -60) {
                return response()->json([
                    'status'  => false,
                    'message' => 'You are already eligible to view the contact.',
                    'data'    => []
                ], 200);
            }
    
            if ((int) $job->isView == 1) {
                return response()->json([
                    'status'  => true,
                    'message' => 'Contact information already unlocked.',
                    'data'    => [
                        'job_id'   => $job->job_no,
                        'is_view'  => 1
                    ]
                ], 200);
            }
    
            $updated = DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'isView'     => 1,
                    'updated_at' => now()
                ]);
    
            return response()->json([
                'status'  => true,
                'message' => 'Contact information unlocked successfully.',
                'data'    => [
                    'job_id'   => $job->job_no,
                    'is_view'  => 1
                ]
            ], 200);
    
        } catch (\Exception $e) {
    
            \Log::error('View Contact API Error: ' . $e->getMessage());
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again later.',
                'data'    => []
            ], 500);
        }
    }
    
    // public function cancel_job(Request $request)
    // {
    //     try {
    
    //         $request->validate([
    //             'job_id' => ['required'],
    //             'job_no' => ['required'],
    //             'reason' => ['nullable'],
    //             'docs' => ['nullable']
    //         ]);
    
    //         $user   = auth()->user();
    //         $userId = $user->id;
    
    //         $job = DB::table('cus_job_temp')
    //             ->where('id', $request->job_id)
    //             ->where('user_id', $userId)
    //             ->whereNot('job_status', 'cancelled')
    //             ->where('job_no', $request->job_no)
    //             ->where('deletes', '0')
    //             ->first();
    
    //         if (!$job) {
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Job not found or already cancelled.'
    //             ], 200);
    //         }
            
    //         $pickupTime = \Carbon\Carbon::parse($job->pickup_date);
    //         $now        = \Carbon\Carbon::now();
            
    //         $ch_payment = DB::table('payment_history')
    //             ->where([
    //                 'job_no'        => $job->job_no,
    //                 'user_id' => $userId,
    //                 'paymentStatus' => 'success'
    //             ])
    //             ->first();
                
    //         $fare_break = json_decode($job->fare_breakdown, true);
    
    //         if ($ch_payment) {
                
    //             $isTime = ($pickupTime->diffInMinutes($now, false) >= -60);
    
    //             if($job->isView == 0 && !$isTime && !$pickupTime->isPast()){
                    
    //                 $opening        = $user->walletBalance ?? 0;
    //                 $expectedAmount = $ch_payment->grandtotal + $ch_payment->wallet_amt;
    //                 $closing        = $opening + $expectedAmount;
        
    //                 DB::table('walletBalance_history')->insert([
    //                     'userid'           => $user->id,
    //                     'uname'            => $user->name,
    //                     'umobile'          => $user->mobile,
    //                     'uemail'           => $user->email,
    //                     'opening_balance'  => $opening,
    //                     'total'            => $expectedAmount,
    //                     'closeing_balance' => $closing,
    //                     'point_type'       => 'WALLET',
    //                     'transaction_type' => 'REFUND',
    //                     'reward_type'      => 'WalletDeposit',
    //                     'reference_id'     => $ch_payment->id,
    //                     'reference_table'  => 'payment_history',
    //                     'ip'               => $request->ip(),
    //                     'createdon'        => now(),
    //                     'updatedon'        => now()
    //                 ]);
                    
    //                 DB::table('customer_register')
    //                     ->where('id', $user->id)
    //                     ->update([
    //                         'walletBalance' => $closing
    //                     ]);
                        
    //                 if($fare_break['isDiscount'] == 'yes'){
                    
    //                     DB::table('walletBalance_history')->insert([
    //                         'userid'           => $user->id,
    //                         'uname'            => $user->name,
    //                         'umobile'          => $user->mobile,
    //                         'uemail'           => $user->email,
    //                         'opening_balance'  => auth()->user()->cash_points,
    //                         'total'            => $fare_break['discount'],
    //                         'closeing_balance' => ((int) auth()->user()->cash_points + $fare_break['discount']),
    //                         'point_type'       => 'CREDIT',
    //                         'transaction_type' => 'REFUND',
    //                         'reward_type'      => 'WalletDeposit',
    //                         'reference_id'     => $ch_payment->id,
    //                         'reference_table'  => 'payment_history',
    //                         'ip'               => $request->ip(),
    //                         'createdon'        => now(),
    //                         'updatedon'        => now()
    //                     ]);
                        
    //                     DB::table('customer_register')
    //                         ->where('id', $user->id)
    //                         ->update([
    //                             'cash_points' => ((int) auth()->user()->cash_points + $fare_break['discount'])
    //                         ]);
    //                 }
    //             }
    
    //         }
            
    //         if($job->global_type != 'schedule'){
    //             $firebase = new \App\Services\FirebaseJobService(
    //                 $this->serviceAccount['project_id'],
    //                 $this->getAccessToken()
    //             );
        
    //             $firebase->updateJobStatus($job->job_no, 'cancelled');
    //             $bidders   = $firebase->getJobBidders($job->job_no) ?? [];
    //             $bidderIds = !empty($bidders) ? array_keys($bidders) : [];
        
    //             if (!empty($bidderIds)) {
        
    //                 dispatch(new \App\Jobs\SendFcmNotificationJob(
    //                     type: 'job_cancelled',
    //                     userIds: $bidderIds,
    //                     title: 'Job Cancelled',
    //                     body: 'Job ' . $job->job_no . ' has been cancelled by the owner.'
    //                 ));
    //             }
    //             $firebase->deleteJob($job->job_no);
                
    //         }else{
    //             if ($job->assigned_to) {
    //                 dispatch(new \App\Jobs\SendFcmNotificationJob(
    //                     type: 'job_cancelled',
    //                     userIds: [$job->assigned_to],
    //                     title: 'Job Cancelled',
    //                     body: 'Job ' . $job->job_no . ' has been cancelled by the owner.'
    //                 ));
        
    //             }
    //         }
    
    //         DB::table('cus_job_temp')
    //             ->where('id', $job->id)
    //             ->update(['job_status' => 'cancelled']);
                
                
    //         if (!empty($request->reason) || (!empty($request->docs) && is_array($request->docs))) {
                
    //             $cancelId = DB::table('job_cancellations')->insertGetId([
    //                 'job_id'      => $job->id,
    //                 'customer_id' => $userId,
    //                 'reason'      => $request->reason,
    //                 'created_at'  => now()
    //             ]);
        
    //             if (!empty($request->docs) && is_array($request->docs)) {
        
    //                 $uniqueDocs = array_values(array_unique($request->docs));
        
    //                 $insertData = [];
        
    //                 foreach ($uniqueDocs as $doc) {
    //                     $insertData[] = [
    //                         'cancellation_id' => $cancelId,
    //                         'doc_url'         => $doc,
    //                         'created_at'      => now()
    //                     ];
    //                 }
        
    //                 DB::table('job_cancellation_docs')->insert($insertData);
    //             }
    //         }
            
    
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Job cancelled successfully.'
    //         ]);
    
    //     } catch (\Throwable $e) {
    
    //         \Log::error('Cancel Job Failed', [
    //             'error' => $e->getMessage()
    //         ]);
    
    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function cancel_job(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $request->validate([
                'job_id' => ['required'],
                'job_no' => ['required'],
                'reason' => ['nullable'],
                'docs' => ['nullable']
            ]);
    
            $user   = auth()->user();
            $userId = $user->id;
    
            // 🔒 Lock row to prevent parallel execution
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->whereNot('job_status', 'cancelled')
                ->where('job_no', $request->job_no)
                ->where('deletes', '0')
                ->lockForUpdate()
                ->first();
    
            if (!$job) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found or already cancelled.'
                ], 404);
            }
    
            // ✅ Immediately mark as cancelled (prevents second request execution)
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update(['job_status' => 'cancelled']);
    
            $pickupTime = \Carbon\Carbon::parse($job->pickup_date);
            $now        = \Carbon\Carbon::now();
    
            $ch_payment = DB::table('payment_history')
                ->where('job_no', $job->job_no)
                ->where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('paymentStatus', 'success')
                          ->orWhere('gateway', 'cash');
                })
                ->first();
    
            $fare_break = json_decode($job->fare_breakdown, true);
    
            if ($ch_payment) {
    
                // ✅ Prevent duplicate refund
                $alreadyRefunded = DB::table('walletBalance_history')
                    ->where('reference_id', $ch_payment->id)
                    ->where('transaction_type', 'REFUND')
                    ->exists();
    
                $isTime = ($pickupTime->diffInMinutes($now, false) >= -60);
    
                if ($job->isView == 0 && !$isTime && !$pickupTime->isPast() && !$alreadyRefunded) {
                    
                    if($ch_payment->gateway != 'cash'){
                        
                        // 💰 Wallet Refund
                        $opening        = $user->walletBalance ?? 0;
                        $expectedAmount = $ch_payment->grandtotal + $ch_payment->wallet_amt;
                        $closing        = $opening + $expectedAmount;
        
                        DB::table('walletBalance_history')->insert([
                            'userid'           => $user->id,
                            'uname'            => $user->name,
                            'umobile'          => $user->mobile,
                            'uemail'           => $user->email,
                            'opening_balance'  => $opening,
                            'total'            => $expectedAmount,
                            'closeing_balance' => $closing,
                            'point_type'       => 'WALLET',
                            'transaction_type' => 'REFUND',
                            'reward_type'      => 'JOB',
                            'global_type' => 'customer',
                            'reference_id'     => $ch_payment->id,
                            'reference_table'  => 'payment_history',
                            'ip'               => $request->ip(),
                            'createdon'        => now(),
                            'updatedon'        => now()
                        ]);
        
                        DB::table('customer_register')
                            ->where('id', $user->id)
                            ->update([
                                'walletBalance' => $closing
                            ]);
                    }
                    
                    
                    // 🎁 Cashback Refund
                    if (!empty($fare_break['isDiscount']) && $fare_break['isDiscount'] == 'yes') {
    
                        $cashOpening = $user->cash_points ?? 0;
                        $cashClosing = $cashOpening + $fare_break['discount'];
    
                        DB::table('walletBalance_history')->insert([
                            'userid'           => $user->id,
                            'uname'            => $user->name,
                            'umobile'          => $user->mobile,
                            'uemail'           => $user->email,
                            'opening_balance'  => $cashOpening,
                            'total'            => $fare_break['discount'],
                            'closeing_balance' => $cashClosing,
                            'point_type'       => 'CREDIT',
                            'transaction_type' => 'REFUND',
                            'reward_type'      => 'JOB',
                            'global_type' => 'customer',
                            'reference_id'     => $ch_payment->id,
                            'reference_table'  => 'payment_history',
                            'ip'               => $request->ip(),
                            'createdon'        => now(),
                            'updatedon'        => now()
                        ]);
    
                        DB::table('customer_register')
                            ->where('id', $user->id)
                            ->update([
                                'cash_points' => $cashClosing
                            ]);
                    }
                }
                
                if($ch_payment->gateway == 'cash' && $job->deductAmt != 0){
                    
                        $expectedAmount = $job->deductAmt;
                        
                        $driver_info = DB::table('user_register')
                            ->where('id', $job->assigned_to)
                            ->where('deletes', '0')->first();
                            
                        $checkDeduct = DB::table('walletBalance_history')->where([
                                        'userid' => $job->assigned_to,
                                        'transaction_type' => 'DEBIT',
                                        'reference_id'     => $job->id,
                                        'reference_table'  => 'cus_job_temp',
                                        'total' => $job->deductAmt,
                                    ])->first();
                        
                        if($driver_info && $checkDeduct){
                            
                            // 💰 Wallet Refund
                            $opening        = $driver_info->walletBalance ?? 0;
                            $closing        = $opening + $expectedAmount;
                            
            
                            DB::table('walletBalance_history')->insert([
                                'userid'           => $driver_info->id,
                                'uname'            => $driver_info->name,
                                'umobile'          => $driver_info->mobile,
                                'uemail'           => $driver_info->email,
                                'opening_balance'  => $opening,
                                'total'            => $expectedAmount,
                                'closeing_balance' => $closing,
                                'point_type'       => 'WALLET',
                                'transaction_type' => 'REFUND',
                                'reward_type'      => 'JOB',
                                'global_type' =>  null,
                                'reference_id'     => $job->id,
                                'reference_table'  => 'cus_job_temp',
                                'ip'               => $request->ip(),
                                'createdon'        => now(),
                                'updatedon'        => now()
                            ]);
            
                            DB::table('user_register')
                                ->where('id', $driver_info->id)
                                ->update([
                                    'walletBalance' => $closing
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
            if (!empty($request->reason) || (!empty($request->docs) && is_array($request->docs))) {
    
                $cancelId = DB::table('job_cancellations')->insertGetId([
                    'job_id'      => $job->id,
                    'customer_id' => $userId,
                    'reason'      => $request->reason,
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
            }
    
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
    
    public function webCancelJob(Request $request)
    {
        DB::beginTransaction();
    
        try {
    
            $request->validate([
                'job_id' => ['required'],
                'job_no' => ['required'],
                'reason' => ['nullable'],
                'docs' => ['nullable']
            ]);
    
            $user   = null;
            $userId = 0;
    
            // 🔒 Lock row to prevent parallel execution
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', 0)
                ->whereNot('job_status', 'cancelled')
                ->where('job_no', $request->job_no)
                ->where('deletes', '0')
                ->lockForUpdate()
                ->first();
    
            if (!$job) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found or already cancelled.'
                ], 404);
            }
    
            // ✅ Immediately mark as cancelled (prevents second request execution)
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update(['job_status' => 'cancelled']);
    
            $pickupTime = \Carbon\Carbon::parse($job->pickup_date);
            $now        = \Carbon\Carbon::now();
    
            $ch_payment = DB::table('payment_history')
                ->where('job_no', $job->job_no)
                ->where('user_id', 0)
                ->where(function ($query) {
                    $query->where('paymentStatus', 'success')
                          ->orWhere('gateway', 'cash');
                })
                ->first();
    
            $fare_break = json_decode($job->fare_breakdown, true);
            
            if ($ch_payment) {
    
                // ✅ Prevent duplicate refund
                $alreadyRefunded = DB::table('walletBalance_history')
                    ->where('reference_id', $ch_payment->id)
                    ->where('transaction_type', 'REFUND')
                    ->exists();
    
                $isTime = ($pickupTime->diffInMinutes($now, false) >= -60);
                
                if($ch_payment->gateway == 'cash' && $job->deductAmt != 0){
                    
                        $expectedAmount = $job->deductAmt;
                        
                        $driver_info = DB::table('user_register')
                            ->where('id', $job->assigned_to)
                            ->where('deletes', '0')->first();
                            
                        $checkDeduct = DB::table('walletBalance_history')->where([
                                        'userid' => $job->assigned_to,
                                        'transaction_type' => 'DEBIT',
                                        'reference_id'     => $job->id,
                                        'reference_table'  => 'cus_job_temp',
                                        'total' => $job->deductAmt,
                                    ])->first();
                        
                        if($driver_info && $checkDeduct){
                            
                            // 💰 Wallet Refund
                            $opening        = $driver_info->walletBalance ?? 0;
                            $closing        = $opening + $expectedAmount;
                            
            
                            DB::table('walletBalance_history')->insert([
                                'userid'           => $driver_info->id,
                                'uname'            => $driver_info->name,
                                'umobile'          => $driver_info->mobile,
                                'uemail'           => $driver_info->email,
                                'opening_balance'  => $opening,
                                'total'            => $expectedAmount,
                                'closeing_balance' => $closing,
                                'point_type'       => 'WALLET',
                                'transaction_type' => 'REFUND',
                                'reward_type'      => 'JOB',
                                'global_type' =>  null,
                                'reference_id'     => $job->id,
                                'reference_table'  => 'cus_job_temp',
                                'ip'               => $request->ip(),
                                'createdon'        => now(),
                                'updatedon'        => now()
                            ]);
            
                            DB::table('user_register')
                                ->where('id', $driver_info->id)
                                ->update([
                                    'walletBalance' => $closing
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
            if (!empty($request->reason) || (!empty($request->docs) && is_array($request->docs))) {
    
                $cancelId = DB::table('job_cancellations')->insertGetId([
                    'job_id'      => $job->id,
                    'customer_id' => $userId,
                    'reason'      => $request->reason,
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
            }
    
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
    
            if ($check_type && $check_type->type == 'Owner') {
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
    
    public function s_fetch_driver_COPY(Request $request)
    {
        try {
            $validated = $request->validate([
                'from'    => ['required', 'string'],
                'to'      => ['required', 'string'],
                // 'pickup'  => ['required', 'date_format:Y-m-d'],
                'pickup'  => ['required'],
                // 'dropoff' => ['required']
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
    
    public function getPresignedUrl(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            'file_type' => 'required|string',
        ]);

        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $fileName = Str::uuid() . '.' . pathinfo($request->file_name, PATHINFO_EXTENSION);
        $bucket = config('filesystems.disks.s3.bucket');

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key'    => $fileName,
            'ContentType' => $request->file_type,
            // 'ACL'    => 'public-read',
        ]);

        $requestSigned = $s3Client->createPresignedRequest($cmd, '+5 minutes');
        $presignedUrl = (string) $requestSigned->getUri();

        return response()->json([
            'status' => true,
            'upload_url' => $presignedUrl,
            'file_url'   => "https://{$bucket}.s3.amazonaws.com/{$fileName}",
        ]);
    }
    
    public function validateCode(Request $request)
    {
        try {
            $request->validate([
                'referral_code' => 'required|string'
            ]);
    
            $code = strtoupper($request->referral_code);
    
            $cacheKey = "referral_code_" . $code;
    
            $referral = Cache::remember($cacheKey, 300, function () use ($code) {
                return DB::table('referral_codes')
                    ->where('code', $code)
                    ->where('app_name', 'customer')
                    ->first();
            });
    
            if (!$referral) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid referral code'
                ]);
            }
    
            if ($request->user() && $request->user()->id == $referral->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot use your own referral code'
                ]);
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Referral code is valid'
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production if needed
            ], 500);
        }
    }

    
}