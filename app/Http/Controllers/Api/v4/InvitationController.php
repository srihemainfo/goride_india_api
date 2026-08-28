<?php

namespace App\Http\Controllers\Api\v4;

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
use App\Services\AutomationEventService;
use App\Services\PusherService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

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
    
    private function getNearestRouteDistanceKm(
        $routeId,
        $lat,
        $lng
    ) {
    
        $nearest = DB::table('route_points')
    
            ->selectRaw("
                MIN(
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(latitude))
                        * cos(
                            radians(longitude)
                            - radians(?)
                        )
                        +
                        sin(radians(?))
                        * sin(radians(latitude))
                    )
                ) as distance
            ", [
                $lat,
                $lng,
                $lat
            ])
    
            ->where('route_id', $routeId)
    
            ->value('distance');
    
        return round(
            $nearest
        );
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
    
    public function getOSRMDistance($fromLat, $fromLng, $toLat, $toLng)
    {
        try {
            // OSRM Request Format: lon,lat;lon,lat
            $coordinates = "{$fromLng},{$fromLat};{$toLng},{$toLat}";
            $url = "https://gomaps.g-ride.in/route/v1/driving/{$coordinates}";
    
            $response = Http::get($url, [
                'overview'   => 'false', // We only need distance, not the full map path
                'alternatives' => 'false'
            ]);
    
            if ($response->successful()) {
                $data = $response->json();
    
                if (isset($data['routes'][0])) {
                    $route = $data['routes'][0];
                    
                    $meters = $route['distance'];
                    
                    return [
                        'status'           => true,
                        'distance_meters'  => round($meters),
                        'distance_km'      => round($meters / 1000, 2),
                        'duration_seconds' => round($route['duration']),
                        'duration_text'    => gmdate("H:i:s", $route['duration']),
                        'summary'          => $route['summary'] ?? 'Main Road'
                    ];
                }
            }
    
            return ['status' => false, 'message' => 'Route not found'];
    
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
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
    
    public function getFcm($id = null, $loc = null, $us_id = null, $cab_seat = null)
    {
        if($loc == 'dr_carpool'){
            
            $query = DB::table('user_register')
                ->where('deletes', '0')
                // ->where('id', $id)
                ->where('status', 0)
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
            
        }else{
            
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
        }

    
        // if (!empty($loc)) {
        //     $query->whereRaw(
        //         "JSON_UNQUOTE(JSON_EXTRACT(prefered_location, '$.location')) LIKE ?",
        //         ["%{$loc}%"]
        //     );
        // }
    
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
                            'sound' => 'custom_notification.mp3',
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
    
    // public function sync(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'contacts' => 'required|array'
    //         ]);

    //         $userId = auth()->id();
    //         $data = [];

    //         foreach ($request->contacts as $contact) {
    //             $phone = preg_replace('/\D/', '', $contact['phone']);
    //             $hash = hash('sha256', $phone);

    //             $data[] = [
    //                 'user_id' => $userId,
    //                 'contact_name' => $contact['name'] ?? null,
    //                 'phone' => $phone,
    //                 'phone_hash' => $hash,
    //                 'created_at' => now(),
    //                 'updated_at' => now()
    //             ];
    //         }
            
    //         // dd($data);
    //         // return 'hiii ' . count($data);

    //         DB::table('user_contacts')->insertOrIgnore($data);

    //         DB::statement("
    //             UPDATE user_contacts uc
    //             JOIN customer_register u 
    //                 ON (
    //                     CASE 
    //                         WHEN CHAR_LENGTH(uc.phone) = 10 
    //                         THEN CONCAT('91', uc.phone)
    //                         ELSE uc.phone
    //                     END
    //                 ) = u.mobile
    //             SET 
    //                 uc.is_app_user = 1,
    //                 uc.app_user_id = u.id
    //             WHERE uc.user_id = ?
    //         ", [$userId]);

    //         return ApiResponse::success('Contacts synced');

    //     } catch (\Throwable $e) {
    //         return ApiResponse::error($e->getMessage());
    //     }
    // }
    
    public function sync(Request $request)
    {
        try {
            $request->validate(['contacts' => 'required|array']);
    
            $userId = auth()->id();
    
            $data = collect($request->contacts)->map(function ($c) use ($userId) {
                $phone = preg_replace('/\D/', '', $c['phone']);
                $phone = ltrim($phone, '0');
    
                if (strlen($phone) == 10) $phone = '91' . $phone;
                if (strlen($phone) != 12) return null;
    
                return [
                    'user_id' => $userId,
                    'contact_name' => $c['name'] ?? null,
                    'phone' => $phone,
                    'phone_hash' => hash('sha256', $phone),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->filter()->unique('phone')->values()->toArray();
    
            if (!$data) return ApiResponse::success('No valid contacts');
    
            // ✅ Upsert = insert + update (no duplicates, single query)
            DB::table('user_contacts')->upsert(
                $data,
                ['user_id', 'phone']
            );
    
            // ✅ Map app users (fast join)
            DB::statement("
                UPDATE user_contacts uc
                JOIN customer_register u ON uc.phone = u.mobile
                SET uc.is_app_user = 1, uc.app_user_id = u.id
                WHERE uc.user_id = ?
            ", [$userId]);
    
            return ApiResponse::success('Contacts synced');
    
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // public function appUsers()
    // {
    //     try {
    //         $userId = auth()->id();

    //         $users = DB::table('user_contacts as uc')
    //             ->join('customer_register as u', 'uc.app_user_id', '=', 'u.id')
    //             ->where('uc.user_id', $userId)
    //             ->select('u.id', 'u.name', 'u.mobile')
    //             ->get();

    //         return ApiResponse::success('App users fetched', $users);

    //     } catch (\Throwable $e) {
    //         return ApiResponse::error($e->getMessage());
    //     }
    // }


    // public function appUsers(Request $request)
    // {
    //     try {
    //         $userId = auth()->id();

    //         $chId = $request->chId ?? null;
    
    //         $invSub = DB::table('invitations')
    //             ->selectRaw("
    //                 CASE
    //                     WHEN type = 'job' THEN invitee_user_id
    //                     WHEN type = 'join' THEN inviter_id
    //                 END as friend_target_id,
    //                 COUNT(*) as invite_count
    //             ")
    //             ->where(function ($q) use ($userId) {
    //                 $q->where(function ($q) use ($userId) {
    //                     $q->where('inviter_id', $userId)
    //                       ->where('type', 'job');
    //                 })
    //                 ->orWhere(function ($q) use ($userId) {
    //                     $q->where('invitee_user_id', $userId)
    //                       ->where('type', 'join');
    //                 });
    //             })
    //             ->where('job_id', $chId)
    //             ->groupByRaw("
    //                 CASE
    //                     WHEN type = 'job' THEN invitee_user_id
    //                     WHEN type = 'join' THEN inviter_id
    //                 END
    //             ");
    
    //         // 2. Main query joining user_contacts with customer_register and the subquery
    //         $users = DB::table('user_contacts as uc')
    //             ->join('customer_register as u', 'uc.app_user_id', '=', 'u.id')
    //             ->leftJoinSub($invSub, 'inv', function ($join) {
    //                 $join->on('u.id', '=', 'inv.friend_target_id');
    //             })
    //             ->where('uc.user_id', $userId)
    //             ->select(
    //                 'u.id', 
    //                 'u.name', 
    //                 'u.mobile',
    //                 'u.profile_img_url', // Added in case you need it like friendsList
    //                 DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Host' ELSE 'Passenger' END as user_role"),
    //                 DB::raw("
    //                     CASE
    //                         WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
    //                         WHEN inv.invite_count = 1 THEN 'reinvite'
    //                         ELSE 'already_sent'
    //                     END as invitation_status
    //                 ")
    //             )
    //             ->get();
    
    //         return ApiResponse::success('App users fetched successfully', $users);
    
    //     } catch (\Throwable $e) {
    //         Log::error("App Users List Error: " . $e->getMessage());
    //         return ApiResponse::error('Failed to load app users. ' . $e->getMessage());
    //     }
    // }
    
    public function appUsers(Request $request)
    {
        try {
            $userId = auth()->id();
            $chId = $request->chId ?? null;
    
            // 1. Build the subquery for 'invitations'
            // We add global_type to the aggregation so we can map it to the respective table later
            $invSub = DB::table('invitations')
                ->selectRaw("
                    global_type,
                    CASE
                        WHEN type = 'job' THEN invitee_user_id
                        WHEN type = 'join' THEN inviter_id
                    END as friend_target_id,
                    COUNT(*) as invite_count
                ")
                ->where(function ($q) use ($userId) {
                    $q->where(function ($q) use ($userId) {
                        $q->where('inviter_id', $userId)
                          ->where('type', 'job');
                    })
                    ->orWhere(function ($q) use ($userId) {
                        $q->where('invitee_user_id', $userId)
                          ->where('type', 'join');
                    });
                })
                ->where('job_id', $chId)
                ->groupByRaw("
                    global_type,
                    CASE
                        WHEN type = 'job' THEN invitee_user_id
                        WHEN type = 'join' THEN inviter_id
                    END
                ");
    
            // 2. Query for Customer Registers ('carpool')
            $customerUsers = DB::table('user_contacts as uc')
                ->join('customer_register as u', 'uc.app_user_id', '=', 'u.id')
                ->leftJoinSub($invSub, 'inv', function ($join) {
                    $join->on('u.id', '=', 'inv.friend_target_id')
                         ->where('inv.global_type', '=', 'carpool');
                })
                ->where('uc.user_id', $userId)
                ->select(
                    'u.id', 
                    'u.name', 
                    'u.mobile',
                    'u.profile_img_url',
                    DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Host' ELSE 'Passenger' END as user_role"),
                    DB::raw("
                        CASE
                            WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
                            WHEN inv.invite_count = 1 THEN 'reinvite'
                            ELSE 'already_sent'
                        END as invitation_status
                    ")
                );
    
            // 3. Query for User Registers ('dr_carpool')
            // We union this query with the customer one.
            $users = DB::table('user_contacts as uc')
                ->join('user_register as u', 'uc.app_user_id', '=', 'u.id')
                ->leftJoinSub($invSub, 'inv', function ($join) {
                    $join->on('u.id', '=', 'inv.friend_target_id')
                         ->where('inv.global_type', '=', 'dr_carpool');
                })
                ->where('uc.user_id', $userId)
                ->select(
                    'u.id', 
                    'u.name', 
                    'u.mobile',
                    'u.profile_img_url',
                    // Adjusting 'user_role' condition if user_register table structural rules differ
                    DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Driver' ELSE 'Passenger' END as user_role"),
                    DB::raw("
                        CASE
                            WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
                            WHEN inv.invite_count = 1 THEN 'reinvite'
                            ELSE 'already_sent'
                        END as invitation_status
                    ")
                )
                // 4. Combine both chunks together cleanly
                ->unionAll($customerUsers)
                ->get();
    
            return ApiResponse::success('App users fetched successfully', $users);
    
        } catch (\Throwable $e) {
            Log::error("App Users List Error: " . $e->getMessage());
            return ApiResponse::error('Failed to load app users. ' . $e->getMessage());
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
            
            // $customer = DB::table('customer_register')
            //     ->where('id', $request->invitee_user_id)
            //     ->first();
                
            // if($customer && $customer->doc_verify != 1){
            //     return ApiResponse::error('KYC Pending', 403);
            // }
    
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
    
                // return ApiResponse::error("Please wait {$secondsLeft} seconds before sending again", 200);
            }
            
            $inviteToken = Str::random(40);
            
            $checkExists = DB::table('invitations as i')
                ->where('i.job_id', $request->job_id)
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('i.invitee_user_id', $request->invitee_user_id)
                          ->where('i.type', 'job');
                    })
                    ->orWhere(function ($q) use ($request) {
                        $q->where('i.inviter_id', $request->invitee_user_id)
                          ->where('i.type', 'join');
                    });
                })
                ->where('i.status', 'pending')
                ->count();
            
            if ($checkExists == 2) {
                return ApiResponse::error('Already invitation sent for the ride');
            }
            
            $checkExists = DB::table('invitations as i')
                ->where('i.job_id', $request->job_id)
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('i.invitee_user_id', $request->invitee_user_id)
                          ->where('i.type', 'job');
                    })
                    ->orWhere(function ($q) use ($request) {
                        $q->where('i.inviter_id', $request->invitee_user_id)
                          ->where('i.type', 'join');
                    });
                })
                ->where('i.status', 'accepted')
                ->exists();
            
            if ($checkExists) {
                return ApiResponse::error('Already accepted for the ride');
            }
    
            $inviteId = DB::table('invitations')->insertGetId([
                'inviter_id' => $userId,
                'invitee_user_id' => $request->invitee_user_id,
                'invitee_phone_hash' => $phoneHash,
                'type' => $request->type,
                'job_id' => $request->job_id,
                'invite_token' => $inviteToken,
                'from_place' => $request->from_place??null,
                'to_place' => $request->to_place??null,
                'from_place_id' => $request->from_place_id??null,
                'to_place_id' => $request->to_place_id??null,
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
    
                        $title = "📍 Ride from {$job->from_place}";
                        $body = "{$inviter->name} invited you to join a ride from {$job->from_place}";
    
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
                                    'screen' => '/my-posts',
                                    'sound' => 'custom_notification',
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
    
    public function joinRequest(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $userId = auth()->id();
            $user = auth()->user();
    
            $request->validate([
                'type' => 'required|in:join',
                // 'invitee_user_id' => 'nullable|exists:customer_register,id',
                'phone' => 'nullable',
                'job_id' => 'required_if:type,join'
            ]);
            
            if($user && $user->doc_verify != 1){
                return ApiResponse::error('KYC Pending', 403);
            }
    
            $phoneHash = null;
            if ($request->phone) {
                $phone = preg_replace('/\D/', '', $request->phone);
                $phoneHash = hash('sha256', $phone);
            }
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                // ->where('user_id', $request->invitee_user_id)
                ->where('deletes', '0')
                ->first();
    
            if (!$job) {
                return ApiResponse::error('Invalid job or unauthorized access', 403);
            }
            
            $request->invitee_user_id = $job->user_id;
            
            $gtype = $job->global_type;
    
            $lastInvite = DB::table('invitations')
                ->where('inviter_id', $userId)
                ->where('global_type', $gtype)
                ->where('job_id', $request->job_id)
                ->where(function ($q) use ($request, $phoneHash) {
                    $q->where('invitee_user_id', $request->invitee_user_id)
                      ->orWhere('invitee_phone_hash', $phoneHash);
                })
                ->where('type', 'join')
                ->where('status', 'pending')
                ->latest()
                ->first();
    
            if ($lastInvite && $lastInvite->created_at >= now()->subSeconds(30)) {
                $secondsLeft = 30 - now()->diffInSeconds($lastInvite->created_at);
    
                // return ApiResponse::error("Please wait {$secondsLeft} seconds before sending again", 200);
            }
            
            $inviteToken = Str::random(40);
            
            $checkExists = DB::table('invitations')
                ->where('job_id', $request->job_id)
                ->where('type', 'join')
                ->where('global_type', $gtype)
                ->where('inviter_id', $userId)
                ->where('status', 'accepted')->exists();
                
            if($checkExists){
                return ApiResponse::error('Already accepted your request for the ride');
            }
            
            $fromGeo = $this->getLatLngByPlaceId(
                $request->from_place_id
            );
    
            $toGeo = $this->getLatLngByPlaceId(
                $request->to_place_id
            );
    
            if (!$fromGeo || !$toGeo) {
    
                return ApiResponse::error(
                    'Invalid location'
                );
            }
    
            $fromLat = $fromGeo['lat'];
            $fromLng = $fromGeo['lng'];
    
            $toLat = $toGeo['lat'];
            $toLng = $toGeo['lng'];
            
            $res = $this->getOSRMDistance($fromLat, $fromLng, $toLat, $toLng);
            
            $dis = 0;
            
            if ($res['status']) {
                $dis = $res['distance_km'];
                // echo "Travel Time: " . $res['duration_text'];
            }
            
            
            $getJob = DB::table('cus_job_temp')->where('id', $request->job_id)->first();
            $getDr = DB::table('user_register')->where('id', $request->invitee_user_id)->first();
            
            // Ensure fare_breakdown is an object if it's stored as a JSON string
            $existingBreakdown = json_decode($getJob->fare_breakdown);
            
            $isVal = false;
            
            // Check if location changed
            if ($request->from_place_id != $getJob->from_place_id || $request->to_place_id != $getJob->to_place_id) {
                $isVal = true;
            }
            
            $totFare = 0;
            $dedAmt = 0;
            $fare_breakdown = null;
            $distance = $dis ?? 0;
            
            $ndri = 0;
            $ncoll = 0;
            $perKms = 5;
            
            if ($getJob->global_type == 'dr_carpool') {
                
                // Use the invitee driver's rate
                $perKm = $getDr->per_km == 0 ? $perKms : $getDr->per_km;
                // $perKm = $perKm == 0 ? $perKms : $perKm;
                
                $pickupOffset =
                    2 * $this->getNearestRouteDistanceKm(
                        $job->route_id,
                        $fromLat,
                        $fromLng
                    );
        
                $dropOffset =
                    2 * $this->getNearestRouteDistanceKm(
                        $job->route_id,
                        $toLat,
                        $toLng
                    );
                
                if ($dis != 0 && $isVal) {
                    
                    $billableDistance = round($dis + $pickupOffset + $dropOffset);
                    
                    $fare = $billableDistance * $perKm;
                    
                    // $fare = $distance * $perKm;
                    
                    $com = round($fare * 0.1);
                    // $tax = ($fare + $com) * 0.05;
                    $tax = 0;
                    $total = $fare + $com + $tax;
            
                    $totFare = round($total);
            
                    $fare_breakdown = [
                        "com"        => round($com),
                        "tax"        => round($tax),
                        "base_fare"  => round($fare),
                        "total_fare" => $totFare
                    ];
                    
                    $dedAmt = $com + $tax;
                    $ncoll = $totFare;
                    $ndri = $totFare - ($com + $tax);
                    
                } else {
                    // Fallback to existing job fare
                    $totFare = $existingBreakdown->total_fare ?? 0;
                    
                    $dedAmt = $existingBreakdown->com + $existingBreakdown->tax;
                    $ndri = $totFare - ($existingBreakdown->com + $existingBreakdown->tax);
                    $ncoll = $totFare;
                    
                    $fare_breakdown = $existingBreakdown;
                }
            }
    
            $inviteId = DB::table('invitations')->insertGetId([
                'global_type' => $gtype,
                'inviter_id' => $userId,
                'invitee_user_id' => $request->invitee_user_id,
                'invitee_phone_hash' => $phoneHash,
                'type' => $request->type,
                'job_id' => $request->job_id,
                'from_place' => $request->from_place,
                'to_place' => $request->to_place,
                'from_place_id' => $request->from_place_id,
                'to_place_id' => $request->to_place_id,
                'invite_token' => $inviteToken,
                'source' => 'job',
                'collectAmt' => $totFare,
                'deductAmt' => $dedAmt,
                'fare_breakdown' => json_encode($fare_breakdown),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
    
            if ($request->invitee_user_id) {
    
                $inviter = [$request->invitee_user_id];
    
                $fcmTokens = $this->getFcm($inviter, $gtype, null, null);
    
                if (!empty($fcmTokens)) {
    
                    $accessToken = $this->getAccessToken();
    
                    if ($accessToken) {
    
                        // $title = "📍 Ride to {$job->from_place}";
                        // $body = "{$user->name} send a joined request to the job {$job->from_place}";
                        $title = "🚗 New Request for {$job->from_place}";
                        $body = "{$user->name} wants to join your ride! Tap to view and accept.";
    
                        foreach ($fcmTokens as $token) {
    
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                $title,
                                $body,
                                [
                                    'job_id' => (string)$job->id,
                                    'pickup' => $request->from_place,
                                    'dropoff' => $request->to_place,
                                    'date' => $job->pickup_date,
                                    'image' => $user->profile_img_url,
                                    'name' => $user->name,
                                    'type'   => 'join_request',
                                    'action' => 'join_request',
                                    'screen' => '/my-posts',
                                    'collectAmt' => $ncoll,
                                    'driverAmt' => $ndri,
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
            
            if($job->isLock){
                DB::commit();
                return ApiResponse::error('Job owner locked the job.');
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
            
            AutomationEventService::trigger(
                'carpool_pick_hour_before_pass',
                $userId,
                [
                    'ride_id' => $invite->job_id,
                ]
            );

            return ApiResponse::success('Invitation accepted');

        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function acceptReject(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'invite_token' => 'required',
                'type' => 'required'
            ]);

            $userId = auth()->id();
            
            $user = auth()->user();

            $invite = DB::table('invitations')
                ->where('invite_token', $request->invite_token)
                ->where('invitee_user_id', $userId)
                ->lockForUpdate()
                ->first();

            if (!$invite) {
                DB::commit();
                return ApiResponse::error('Invalid invite');
            }
            
            $job = DB::table('cus_job_temp')    
                ->where('id', $invite->job_id)
                ->where('user_id', $userId)
                ->where('global_type', 'carpool')
                ->lockForUpdate() 
                ->first();
                
            if (!$job) {
                DB::commit();
                return ApiResponse::error('Job not found');
            }
            
            $action_status = $request->type;
                
            if ( $job->filled_seat < $job->pass_count || $action_status != 'accept' ) {
                
                if($action_status == 'accept'){
                    DB::table('cus_job_temp')
                        ->where('id', $invite->job_id)
                        ->increment('filled_seat', 1);
                    
                    AutomationEventService::trigger(
                        'carpool_pick_hour_before_pass',
                        $invite->inviter_id,
                        [
                            'ride_id' => $invite->job_id,
                        ]
                    );
                    
                }
                
                if($invite->status == 'accepted' && $action_status == 'reject'){
                    DB::table('cus_job_temp')
                        ->where('id', $invite->job_id)
                        ->decrement('filled_seat', 1);
                    
                }
                    
            }else{
                DB::commit();
                return ApiResponse::error('All seats are occupied. Please try another job.');
            }

            DB::table('invitations')    
                ->where('id', $invite->id)
                // ->where('invitee_user_id', $userId)
                ->update([
                    'status' => $action_status == 'accept' ? 'accepted' : 'rejected',
                    'updated_at' => now()
                ]);

            DB::commit();
            
            $res = 'Invitation '. ($action_status == 'accept' ? 'accepted' : 'rejected');
            
            if ($invite->inviter_id) {
                $inviter = [$invite->inviter_id];
                $fcmTokens = $this->getFcm($inviter, null, null, null);
            
                if (!empty($fcmTokens)) {
                    $accessToken = $this->getAccessToken();
            
                    if ($accessToken) {
                        // Check the status of the invite (Assuming $invite->status holds the value)
                        if ($action_status == 'accept') {
                            $title = "✅ Request Accepted!";
                            $body  = "Great news! {$user->name} has accepted your invitation for the ride from {$job->from_place}.";
                            $type  = 'invite_accepted';
                        } else {
                            $title = "❌ Request Declined";
                            $body  = "Unfortunately, {$user->name} declined the invitation for the ride from {$job->from_place}.";
                            $type  = 'invite_rejected';
                        }
            
                        foreach ($fcmTokens as $token) {
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                $title,
                                $body,
                                [
                                    'job_id'       => (string)$job->id,
                                    'type'         => $type,
                                    'action'       => $type,
                                    'screen' => '/my-posts',
                                    'invite_token' => $request->invite_token
                                ]
                            );
                        }
                    }
                }
            }

            return ApiResponse::success($res);

        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function inviteList()
    {
        try {
            $userId = auth()->id();
    
            $invites = DB::table('invitations as i')
                ->where(function ($query) use ($userId) {
                    $query->where(function ($q) use ($userId) {
                            $q->where('i.invitee_user_id', $userId)
                              ->where('i.type', 'job');
                        })
                        ->orWhere(function ($q) use ($userId) {
                            $q->where('i.invitee_user_id', $userId)
                              ->where('i.type', 'join');
                        });
                })
                ->where('i.status', 'pending')
                ->where('i.created_at', '>=', now()->subDays(5))
                ->whereIn('i.id', function ($sub) use ($userId) {
                    $sub->selectRaw('MAX(id)')
                        ->from('invitations as s')
                        ->where(function ($query) use ($userId) {
                            $query->where(function ($q) use ($userId) {
                                    $q->where('s.invitee_user_id', $userId)
                                      ->where('s.type', 'job');
                                })
                                ->orWhere(function ($q) use ($userId) {
                                    $q->where('s.invitee_user_id', $userId)
                                      ->where('s.type', 'join');
                                });
                        })
                        ->where('s.status', 'pending')
                        ->groupBy('s.job_id');
                })
                ->whereNotExists(function ($q) use ($userId) {
                    $q->select(DB::raw(1))
                      ->from('invitations as x')
                      ->whereColumn('x.job_id', 'i.job_id')
                      ->where('x.inviter_id', $userId)
                      ->whereIn('x.status', ['accepted', 'rejected', 'removed']);
                })
                ->get();
    
            return ApiResponse::success('Invitations list', $invites);
    
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    // public function friendsList()
    // {
    //     try {
    //         $userId = auth()->id();

    //         $friends = DB::table('friends as f')
    //             ->join('customer_register as u', 'f.friend_id', '=', 'u.id')
    //             ->where('f.user_id', $userId)
    //             ->select(
    //                 'u.id', 
    //                 'u.name', 
    //                 'u.mobile',
    //                 'u.profile_img_url',
    //                 DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Host' ELSE 'Passenger' END as user_role")
    //             )
    //             ->get();

    //         return ApiResponse::success('Friends list', $friends);

    //     } catch (\Throwable $e) {
    //         return ApiResponse::error($e->getMessage());
    //     }
    // }
    
    // public function friendsList(Request $request)
    // {
    //     try {
    //         $userId = auth()->id();
    //         $chId = $request->chId ?? null;
            
    //         $invSub = DB::table('invitations')
    //             ->selectRaw("
    //                 CASE
    //                     WHEN type = 'job' THEN invitee_user_id
    //                     WHEN type = 'join' THEN inviter_id
    //                 END as friend_target_id,
    //                 COUNT(*) as invite_count
    //             ")
    //             ->where(function ($q) use ($userId) {
    //                 $q->where(function ($q) use ($userId) {
    //                     $q->where('inviter_id', $userId)
    //                       ->where('type', 'job');
    //                 })
    //                 ->orWhere(function ($q) use ($userId) {
    //                     $q->where('invitee_user_id', $userId)
    //                       ->where('type', 'join');
    //                 });
    //             })
    //             ->where('job_id', $chId)
    //             ->groupByRaw("
    //                 CASE
    //                     WHEN type = 'job' THEN invitee_user_id
    //                     WHEN type = 'join' THEN inviter_id
    //                 END
    //             ");
                
    //         $friends = DB::table('friends as f')
    //             ->join('customer_register as u', 'f.friend_id', '=', 'u.id')
    //             ->leftJoinSub($invSub, 'inv', function ($join) {
    //                 $join->on('u.id', '=', 'inv.friend_target_id');
    //             })
    //             ->where('f.user_id', $userId)
    //             ->select(
    //                 'u.id',
    //                 'u.name',
    //                 'u.mobile',
    //                 'u.profile_img_url',
    //                 DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Host' ELSE 'Passenger' END as user_role"),
    //                 DB::raw("
    //                     CASE
    //                         WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
    //                         WHEN inv.invite_count = 1 THEN 'reinvite'
    //                         ELSE 'already_sent'
    //                     END as invitation_status
    //                 ")
    //             )
    //             ->get();
            
    //         return ApiResponse::success('Friends list fetched successfully', $friends);
    
    //     } catch (\Throwable $e) {
     
    //         Log::error("Friends List Error: " . $e->getMessage());
    //         return ApiResponse::error('Failed to load friends list. ' . $e->getMessage());
    //     }
    // }
    
    public function friendsList(Request $request)
    {
        try {
            $userId = auth()->id();
            $chId = $request->chId ?? null;
            
            // 1. Build the subquery for 'invitations' including global_type
            $invSub = DB::table('invitations')
                ->selectRaw("
                    global_type,
                    CASE
                        WHEN type = 'job' THEN invitee_user_id
                        WHEN type = 'join' THEN inviter_id
                    END as friend_target_id,
                    COUNT(*) as invite_count
                ")
                ->where(function ($q) use ($userId) {
                    $q->where(function ($q) use ($userId) {
                        $q->where('inviter_id', $userId)
                          ->where('type', 'job');
                    })
                    ->orWhere(function ($q) use ($userId) {
                        $q->where('invitee_user_id', $userId)
                          ->where('type', 'join');
                    });
                })
                ->where('job_id', $chId)
                ->groupByRaw("
                    global_type,
                    CASE
                        WHEN type = 'job' THEN invitee_user_id
                        WHEN type = 'join' THEN inviter_id
                    END
                ");
                
            // 2. Query for Friends in Customer Registers ('carpool')
            $customerFriends = DB::table('friends as f')
                ->join('customer_register as u', 'f.friend_id', '=', 'u.id')
                ->leftJoinSub($invSub, 'inv', function ($join) {
                    $join->on('u.id', '=', 'inv.friend_target_id')
                         ->where('inv.global_type', '=', 'carpool');
                })
                ->where('f.user_id', $userId)
                ->where('f.source', 'invite')
                ->select(
                    'u.id',
                    'u.name',
                    'u.mobile',
                    'u.profile_img_url',
                    DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Host' ELSE 'Passenger' END as user_role"),
                    DB::raw("
                        CASE
                            WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
                            WHEN inv.invite_count = 1 THEN 'reinvite'
                            ELSE 'already_sent'
                        END as invitation_status
                    ")
                );
    
            // 3. Query for Friends in User Registers ('dr_carpool')
            $friends = DB::table('friends as f')
                ->join('user_register as u', 'f.friend_id', '=', 'u.id')
                ->leftJoinSub($invSub, 'inv', function ($join) {
                    $join->on('u.id', '=', 'inv.friend_target_id')
                         ->where('inv.global_type', '=', 'dr_carpool');
                })
                ->where('f.user_id', $userId)
                ->where('f.source', 'dr_invite')
                ->select(
                    'u.id',
                    'u.name',
                    'u.mobile',
                    'u.profile_img_url',
                    DB::raw("CASE WHEN u.vehicle_details IS NOT NULL THEN 'Driver' ELSE 'Passenger' END as user_role"),
                    DB::raw("
                        CASE
                            WHEN inv.invite_count IS NULL OR inv.invite_count = 0 THEN 'invite'
                            WHEN inv.invite_count = 1 THEN 'reinvite'
                            ELSE 'already_sent'
                        END as invitation_status
                    ")
                )
                // 4. Combine both friend categories together
                ->unionAll($customerFriends)
                ->get();
            
            return ApiResponse::success('Friends list fetched successfully', $friends);
    
        } catch (\Throwable $e) {
            Log::error("Friends List Error: " . $e->getMessage());
            return ApiResponse::error('Failed to load friends list. ' . $e->getMessage());
        }
    }
    
    public function removeInvite(Request $request)
    {
        DB::beginTransaction();
    
        try {
            
            $request->validate([
                'invite_token' => ['required'],
                'job_id'       => ['required'],
                'u_type'       => ['nullable'],
                'pass_id'      => ['required']
            ]);
    
            $userId = auth()->id();
            $user = auth()->user();
    
            $invite = DB::table('invitations')
                ->where('invite_token', $request->invite_token)
                ->where('job_id', $request->job_id)
                ->whereIn('status', ['pending', 'accepted'])
                ->lockForUpdate()
                ->first();
    
            if (!$invite) {
                DB::rollBack();
                return ApiResponse::error('Invalid or already removed invite');
            }
    
            $job = DB::table('cus_job_temp')
                ->where('id', $invite->job_id)
                // ->where('user_id', $userId)
                ->whereIn('global_type', ['dr_carpool', 'carpool'])
                ->lockForUpdate()
                ->first();
    
            if (!$job) {
                DB::rollBack();
                return ApiResponse::error('Unauthorized or job not found');
            }
    
            if ($job->filled_seat > 0) {
                DB::table('cus_job_temp')
                    ->where('id', $job->id)
                    ->decrement('filled_seat', 1);
            }
            
            if($request->u_type == 'passenger'){
                $u_st = 'exit';
            }else{
                $u_st = 'removed';
            }
            
    
            DB::table('invitations')
                ->where('id', $invite->id)
                ->update([
                    'status'     => $u_st,
                    'updated_at' => now()
                ]);
    
            DB::commit();
            
            $cusIds = [$request->pass_id];
    
            $fcmTokens = $this->getFcm($cusIds, null, null, null);
    
            if (!empty($fcmTokens)) {
    
                    $accessToken = $this->getAccessToken();
    
                    if ($accessToken) {
    
                        $title = "🚗 Ride Update";
                        $body = "You have been removed from the carpool to {$job->from_place} by {$user->name}.";
    
                        foreach ($fcmTokens as $token) {
    
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                $title,
                                $body,
                                [
                                    'job_id' => (string)$job->id,
                                    'type'   => 'job_invitation_remove',
                                    'action' => 'job_invitation_remove',
                                    'screen' => '/my-posts',
                                    'invite_token' => $request->invite_token
                                ]
                            );
                        }
                    }
                }
                
            $table = ($job->global_type == 'dr_carpool') ? 'user_register' : 'customer_register';
            $drFcm = DB::table($table)->where('id', $job->user_id)->value('fcm_token');
            
            // 3. Send Notification if Token exists
            if (!empty($drFcm) && $accessToken) {
                // Note: Use a temporary variable for the title to keep $u_st lowercase for the body
                $displayStatus = ucwords($u_st);
                
                $driverTitle = "🚗 Passenger {$displayStatus}";
                $driverBody  = "{$user->name} has {$u_st} from your carpool to {$job->from_place}.";
            
                $this->sendFCM(
                    $accessToken,
                    $drFcm, // Fixed variable name mismatch
                    $driverTitle,
                    $driverBody,
                    [
                        'job_id' => (string)$job->id,
                        'type'   => 'driver_passenger_removed', 
                        'action' => 'driver_passenger_removed',
                        'screen' => 'my-posts',
                        'invite_token' => ''
                    ]
                );
            }
    
            return ApiResponse::success('Passenger removed.');
    
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
    
    // public function notificationList(Request $request)
    // {
    //     try {
    
    //         $userId = auth()->id();
    //         $page = $request->page ?? 1;
    //         $limit = $request->limit ?? 20;
    //         $offset = ($page - 1) * $limit;
            
    //         $notifications = DB::table('push_notification_logs as pnl')
    
    //             ->leftJoin(
    //                 'push_automation_rules as par',
    //                 'par.id',
    //                 '=',
    //                 'pnl.rule_id'
    //             )
    //             ->where('pnl.user_id', $userId)
    //             ->select(
    //                 'pnl.id',
    //                 'pnl.event',
    //                 'pnl.status',
    //                 'pnl.sent_at',
    //                 'par.title',
    //                 'par.message',
    //                 'par.redirect'
    //             )
    //             ->orderByDesc('pnl.id')
    //             ->offset($offset)
    //             ->limit($limit)
    //             ->get();
    
    //         $total = DB::table('push_notification_logs')
    //             ->where('user_id', $userId)
    //             ->count();
    
    //         $data = [];
    
    //         foreach ($notifications as $item) {
    
    //             $data[] = [
    //                 'id' => $item->id,
    //                 'title' => $item->title,
    //                 'message' => $item->message,
    //                 'event' => $item->event,
    //                 'status' => $item->status,
    //                 'redirect' => $item->redirect,
    //                 'sent_at' => $item->sent_at,
    //                 'time_ago' => \Carbon\Carbon::parse(
    //                     $item->sent_at
    //                 )->diffForHumans()
    //             ];
    //         }
    
    //         return response()->json([
    
    //             'status' => true,
    //             'message' => 'Notifications fetched successfully',
    //             'total' => $total,
    //             'page' => (int)$page,
    //             'limit' => (int)$limit,
    //             'data' => $data
    //         ]);
    
    //     } catch (\Throwable $e) {
    
    //         \Log::error('Notification list error', [
    //             'message' => $e->getMessage(),
    //             'line' => $e->getLine()
    //         ]);
    
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to fetch notifications',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function notificationList(Request $request)
    {
        try {
    
            $userId = auth()->id();
    
            $page = $request->page ?? 1;
    
            $limit = $request->limit ?? 20;
    
            $offset = ($page - 1) * $limit;
    
            $notifications = DB::table('push_notification_logs as pnl')
    
                ->leftJoin(
                    'push_automation_rules as par',
                    'par.id',
                    '=',
                    'pnl.rule_id'
                )
                
                ->where(
                    'pnl.created_at',
                    '>=',
                    now()->subHour()
                )
    
                ->where('pnl.user_id', $userId)
    
                ->select(
    
                    'pnl.id',
    
                    'pnl.event',
    
                    'pnl.status',
    
                    'pnl.sent_at',
    
                    'par.title',
    
                    'par.message',
    
                    'par.redirect',
    
                    DB::raw("'push' as type")
                )
    
                ->get();
    
            /*
            |--------------------------------------------------------------------------
            | Invitation Notifications
            |--------------------------------------------------------------------------
            */
    
            $invites = DB::table('invitations as i')
    
                ->where(function ($query) use ($userId) {
    
                    $query->where(function ($q) use ($userId) {
    
                            $q->where(
                                    'i.invitee_user_id',
                                    $userId
                                )
                                ->where('i.type', 'job');
    
                        })
    
                        ->orWhere(function ($q) use ($userId) {
    
                            $q->where(
                                    'i.invitee_user_id',
                                    $userId
                                )
                                ->where('i.type', 'join');
    
                        });
                })
    
                ->where('i.status', 'pending')
    
                ->where(
                    'i.created_at',
                    '>=',
                    now()->subHour()
                )
    
                ->whereIn('i.id', function ($sub)
                    use ($userId) {
    
                    $sub->selectRaw('MAX(id)')
    
                        ->from('invitations as s')
    
                        ->where(function ($query)
                            use ($userId) {
    
                            $query->where(function ($q)
                                use ($userId) {
    
                                    $q->where(
                                            's.invitee_user_id',
                                            $userId
                                        )
                                        ->where(
                                            's.type',
                                            'job'
                                        );
    
                                })
    
                                ->orWhere(function ($q)
                                    use ($userId) {
    
                                    $q->where(
                                            's.invitee_user_id',
                                            $userId
                                        )
                                        ->where(
                                            's.type',
                                            'join'
                                        );
                                });
                        })
    
                        ->where('s.status', 'pending')
    
                        ->groupBy('s.job_id');
                })
    
                ->whereNotExists(function ($q)
                    use ($userId) {
    
                    $q->select(DB::raw(1))
    
                        ->from('invitations as x')
    
                        ->whereColumn(
                            'x.job_id',
                            'i.job_id'
                        )
    
                        ->where(
                            'x.inviter_id',
                            $userId
                        )
    
                        ->whereIn('x.status', [
                            'accepted',
                            'rejected',
                            'removed'
                        ]);
                })
    
                ->select(
    
                    'i.id',
    
                    'i.type as event',
    
                    'i.created_at as sent_at',
    
                    DB::raw("'pending' as status"),
    
                    DB::raw("
                        CASE
                            WHEN i.type = 'job'
                            THEN '🚖 New Ride Invitation'
                            ELSE '👥 Join Request'
                        END as title
                    "),
    
                    DB::raw("
                        CASE
                            WHEN i.type = 'job'
                            THEN 'You received a new ride invitation. Check and respond now 🚖'
                            ELSE 'Someone invited you to join. Check it now 👥'
                        END as message
                    "),
    
                    DB::raw("
                        CASE
                            WHEN i.type = 'job'
                            THEN 'my-posts'
                            ELSE 'my-posts'
                        END as redirect
                    "),
    
                    DB::raw("'invite' as type")
                )
    
                ->get();
    
            /*
            |--------------------------------------------------------------------------
            | Merge Notifications
            |--------------------------------------------------------------------------
            */
    
            $merged = $notifications
    
                ->concat($invites)
    
                ->sortByDesc('sent_at')
    
                ->values();
    
            /*
            |--------------------------------------------------------------------------
            | Total Count
            |--------------------------------------------------------------------------
            */
    
            $total = $merged->count();
    
            /*
            |--------------------------------------------------------------------------
            | Pagination After Merge
            |--------------------------------------------------------------------------
            */
    
            $paginated = $merged
    
                ->slice($offset, $limit)
    
                ->values();
    
            /*
            |--------------------------------------------------------------------------
            | Format Response
            |--------------------------------------------------------------------------
            */
    
            $data = [];
    
            foreach ($paginated as $item) {
    
                $data[] = [
    
                    'id' => $item->id,
    
                    'type' => $item->type,
    
                    'title' => $item->title,
    
                    'message' => $item->message,
    
                    'event' => $item->event,
    
                    'status' => $item->status,
    
                    'redirect' => $item->redirect,
    
                    'sent_at' => $item->sent_at,
    
                    'time_ago' => \Carbon\Carbon::parse(
                        $item->sent_at
                    )->diffForHumans()
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */
    
            return response()->json([
    
                'status' => true,
    
                'message' => 'Notifications fetched successfully',
    
                'total' => $total,
    
                'page' => (int)$page,
    
                'limit' => (int)$limit,
    
                'data' => $data
            ]);
    
        } catch (\Throwable $e) {
    
            \Log::error('Notification list error', [
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
            ]);
    
            return response()->json([
    
                'status' => false,
    
                'message' => 'Failed to fetch notifications',
    
                'error' => $e->getMessage()
    
            ], 500);
        }
    }
    
}