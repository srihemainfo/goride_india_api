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
use Illuminate\Support\Facades\Cache;
use App\Helpers\ApiResponse;

class CarPoolJobController extends Controller
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
        
        $blockedDrivers = DB::table('driver_notification_logs')
            ->select('user_id')
            ->where('created_at', '>=', now()->subHours(2))
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('user_id')
            ->toArray();
            
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
    
                ->whereNotIn('ur.id', $blockedDrivers)
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
    
    private function createFirebaseJob(string $jobNo, array $data)
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
                    'stringValue' => $data['global_type']
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
    
    public function DistanceAndDuration(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => 'required|string',
                'to_place_id'   => 'required|string',
                'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s']
            ]);
            
            $pickup = Carbon::parse($request->pickup_date);
            $now    = Carbon::now();
            
            if($pickup->isPast()){
                return ApiResponse::error('Pickup date & time must be future date & time');
            }
    
            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHours(2))) {
                
                return ApiResponse::error('Pickup time must be at least 2 hours from now');
            }
            
            if($request->from_place_id == $request->to_place_id){
                return ApiResponse::error('Please select different pickup and drop-off locations.');
            }
            
            $pickDate = Carbon::parse($request->pickup_date)->format('Y-m-d');
       
    
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
    
            if (!$fromGeo || !$toGeo) {
                return ApiResponse::error('Unable to resolve coordinates');
            }
    
            $journeyType    = 'oneway';
            $userCashPoints = 0;
            
            $seaters[] = 'four_seater';
            
            $responseData = [];
            
            $fromName = DB::table('outstation_locations')
                ->where('place_id', $request->from_place_id)
                ->value('display_name');
        
            $toName = DB::table('outstation_locations')
                ->where('place_id', $request->to_place_id)
                ->value('display_name');
    
            foreach ($seaters as $seater) {
    
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
                    
                    $responseData = [
                        'distance' =>  $distance,
                        'duration' => $cached->duration
                    ];
                    
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
                
                $responseData = [
                    'distance' =>  $distanceKm,
                    'duration' => $duration
                ];
                
            }
            
            return response()->json([
                'status'  => true,
                'data'    => $responseData,
                'message' => 'Fare calculated successfully'
            ]);
            
            return ApiResponse::success('Fare calculated successfully', $responseData);
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function createJob(Request $request)
    {
        try {
    
            $request->validate([
                
                'from_place' => ['required', 'string', 'max:255'],
                'to_place' => ['required', 'string', 'max:255'],
                'from_place_id' => ['required', 'string', 'max:255'],
                'to_place_id' => ['required', 'string', 'max:255'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'seat_fare' => ['required', 'string', 'max:255'],
                'seat' => ['required', 'numeric'],
                'distance' => ['required', 'string', 'max:255'],
                'duration' => ['nullable', 'string', 'max:255']
            ]);
    
            $user = auth()->user();
            $userId = $user->id;
            
            $activeJobsCount = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->whereNotIn('job_status', ['completed', 'cancelled'])
                ->count();
            
            if ($activeJobsCount >= 2) {
                return ApiResponse::error('You can only have 2 active jobs at a time');
            }
            
            do {
                $job_no = 'GRP-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
            } while (
                DB::table('cus_job_temp')->where('job_no', $job_no)->exists()
            );
            
            $hash = hash_hmac(
                'sha256',
                $job_no . 'NEW_BOOKING' . $user->mobile,
                config('app.key')
            );
            
            
            do {
                $shortCode = env('SHORT_SLUG').Str::random(8);
            } while (
                DB::table('cus_job_temp')
                    ->where('short_hash', $shortCode)
                    ->exists()
            );
            
            
            $data = [
                'global_type'   => 'carpool',
                'job_type'      => 'oneway',
                'user_id'       => $userId,
                'from_place'    => $request->from_place,
                'to_place'      => $request->to_place,
                'from_place_id' => $request->from_place_id,
                'to_place_id'   => $request->to_place_id,
                'pickup_date'   => $request->pickup_date,
                'fare'          => $request->seat_fare,
                'pass_count'    => $request->seat,
                'distance'      => $request->distance,
                'duration'      => $request->duration,
                'created_at'    => now(),
                'updated_at'    => now()
            ];
            
            $data['short_hash'] = $shortCode;
            $data['preview_hash'] = $hash;
            $data['job_no'] = $job_no;
            $data['otp'] = Controller::generateOTP(6);
            
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            if ($accessToken) {
                
                $data['poster_name'] = $user->name;
                $data['id'] = $create_job;
                
        
                $this->createFirebaseJob($job_no, $data);
            }
            
            return ApiResponse::success('Job Created', $create_job);
    
        } catch (ValidationException $e) {
            
            return ApiResponse::error('Validation failed.', [], 422);
    
        } catch (\Exception $e) {
            
            return ApiResponse::error($e->getMessage());
            
        }
    }
    
    public function myJobs()
    {
        try {
            $userId = auth()->id();
    
            // 🔹 1. Get user jobs
            $jobs = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->whereNotIn('job_status', ['completed', 'cancelled'])
                ->orderByDesc('id')
                ->get();
    
            if ($jobs->isEmpty()) {
                return ApiResponse::success('No jobs found', []);
            }
    
            $jobIds = $jobs->pluck('id')->toArray();
            
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->whereNotNull('invitee_user_id')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id', 'invitee_user_id')
                ->pluck('id');
                
            $invites = DB::table('invitations as i')
                ->leftJoin('customer_register as c', 'i.invitee_user_id', '=', 'c.id')
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'i.status',
                    'c.id as user_id',
                    'c.name',
                    'c.profile_img_url'
                )
                ->get();
    
            // 🔹 2. Get all invitations for these jobs
            // $invites = DB::table('invitations as i')
            //     ->leftJoin('customer_register as c', 'i.invitee_user_id', '=', 'c.id')
            //     ->whereIn('i.job_id', $jobIds)
            //     ->select(
            //         'i.job_id',
            //         'i.status',
            //         'c.id as user_id',
            //         'c.name',
            //         'c.profile_img_url'
            //     )
            //     ->get();
            
    
            // 🔹 3. Group by job_id
            $groupedInvites = $invites->groupBy('job_id');
    
            // 🔹 4. Build response
            $result = [];
    
            foreach ($jobs as $job) {
    
                $jobInvites = $groupedInvites[$job->id] ?? collect();
    
                $accepted = $jobInvites
                    ->where('status', 'accepted')
                    ->map(function ($item) {
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'profile_img_url' => $item->profile_img_url
                        ];
                    })
                    ->values();
    
                $pending = $jobInvites
                    ->where('status', 'pending')
                    ->map(function ($item) {
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name
                        ];
                    })
                    ->values();
    
                $result[] = [
                    'job_id' => $job->id,
                    'job_no' => $job->job_no,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'pickup_date' => $job->pickup_date,
                    'seat' => $job->pass_count,
                    'accepted_friends' => $accepted,
                    'pending_invitations' => $pending
                ];
            }
    
            return ApiResponse::success('My jobs fetched', $result);
    
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function postOpen(Request $request)
    {
        try {
    
            $request->validate([
                'job_id' => ['required', 'exists:cus_job_temp,id'],
                'status' => ['required']
            ]);
    
            $userId = auth()->id();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->where('global_type', 'carpool')
                ->first();
    
            if (!$job) {
                return ApiResponse::error('Job not found or unauthorized', 404);
            }
    
            $updated = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->update([
                    'confirm_status' => $request->status,
                    'updated_at' => now()
                ]);
    
            if (!$updated) {
                return ApiResponse::error('Failed to update job');
            }
    
            return ApiResponse::success('Job status updated');
    
        } catch (ValidationException $e) {
    
            return ApiResponse::error('Validation failed.', $e->errors(), 422);
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error($e->getMessage());
        }
    }
    
}