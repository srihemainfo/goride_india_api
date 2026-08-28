<?php

namespace App\Http\Controllers\Api\v2;

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
use Illuminate\Support\Facades\Crypt;

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
    
    // public function encryptJobId($jobId)
    // {
    //     return Crypt::encryptString((string)$jobId);
    // }
    
    // public function decryptJobId($encrypted)
    // {
    //     return Crypt::decryptString($encrypted);
    // }
    
    // public function encryptJobId($id, $key = 12345) {
    //     $xored = $id ^ $key;
    //     return rtrim(strtr(base64_encode($xored), '+/', '-_'), '=');
    // }
    
    public function encryptJobId($id, $key = 12345)
    {
        // Step 1: XOR
        $xored = $id ^ $key;
    
        // Step 2: Convert to string (important!)
        $stringValue = (string) $xored;
    
        // Step 3: Base64 encode
        $base64 = base64_encode($stringValue);
    
        // Step 4: Make URL safe
        return rtrim(strtr($base64, '+/', '-_'), '=');
    }
    
    public function decryptJobId($encoded, $key = 12345) {
        $decoded = base64_decode(strtr($encoded, '-_', '+/'));
        return $decoded ^ $key;
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
                'duration' => ['nullable', 'string', 'max:255'],
                'isOpen' => ['nullable']
            ]);
    
            $user = auth()->user();
            $userId = $user->id;
            
            if(auth()->user()->doc_verify != 1 && auth()->user()->vehicle_verify != 2){
                
                return ApiResponse::error('KYC verification pending.');
            }
            
            $activeJobsCount = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('global_type', 'carpool')
                ->whereNotIn('job_status', ['completed', 'cancelled'])
                ->count();
            
            // if ($activeJobsCount >= 2) {
            //     return ApiResponse::error('You can only have 2 active jobs at a time');
            // }
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            $c_seat = 0;
            
            if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
        
                $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                $c_seat = $rcVehicle['seat_capacity'] ?? null;
                // $body_type = $rcVehicle['body_type'] ?? null;
        
            } else {
    
                $get_ocr = DB::table('ocr_request')
                    ->where('user_id', $userId)
                    ->where('doc_type', 'RC')
                    ->orderByDesc('id')
                    ->first();
        
                if ($get_ocr) {
                    
                    $c_seat = $get_ocr->seater ?? null;
                    if($c_seat == 'mini' || $c_seat == 'Mini' || $c_seat == 'Mini 4'){
                        $c_seat = 5;
                    }
                }
            }
            
            // if ($request->seat > ($c_seat - 1)) {
            //     return ApiResponse::error("Seat count exceeds your vehicle's maximum capacity.");
            // }
            
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
            
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
            
            $from_to = [
                'from_lat' => $fromGeo['lat'],
                'from_lng' => $fromGeo['lng'],
                'to_lat' => $toGeo['lat'],
                'to_lng' => $toGeo['lng'],
            ];
            
            $data['from_to_co'] = json_encode($from_to);
            
            $data['short_hash'] = $shortCode;
            $data['preview_hash'] = $hash;
            $data['job_no'] = $job_no;
            
            if($request->isOpen && $request->isOpen == 1){
                $data['confirm_status'] = 1;
            }
            
            $data['otp'] = Controller::generateOTP(6);
            
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            $data['poster_name'] = $user->name;
            $data['id'] = $create_job;
            
            unset(
                $data['confirm_status']
            );
            
    
            $this->createFirebaseJob($job_no, $data);
            
            $res = [
                'job_id' => $create_job,
                'job_no' => $job_no
            ];
            
            return ApiResponse::success('Job Created', $res);
    
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
    
            $jobs = DB::table('cus_job_temp as job')
                ->join('customer_register as host', 'host.id', '=', 'job.user_id')
                ->where('job.user_id', $userId) // Specified 'job.' to avoid ambiguity
                ->where('job.global_type', 'carpool')
                ->whereNotIn('job.job_status', ['completed', 'cancelled'])
                ->whereDate('job.pickup_date', '>=', now()->toDateString()) // Filters out past dates
                ->orderByDesc('job.id')
                ->select('job.*', 'host.name', 'host.fcm_token', 'host.profile_img_url')
                ->get();
    
            if ($jobs->isEmpty()) {
                return ApiResponse::success('No jobs found', []);
            }
    
            $jobIds = $jobs->pluck('id')->toArray();
            
            // $latestInviteIds = DB::table('invitations')
            //     ->whereIn('job_id', $jobIds)
            //     ->whereNotNull('invitee_user_id')
            //     ->select(DB::raw('MAX(id) as id'))
            //     ->groupBy('job_id', 'invitee_user_id')
            //     ->pluck('id');
                
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                // ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                // ->groupBy(DB::raw("CASE WHEN type = 'join' THEN invitee_user_id ELSE inviter_id END"))
                ->pluck('id');
                
            // return $latestInviteIds;
                
            $invites = DB::table('invitations as i')
                ->join('customer_register as c', function ($join) use ($userId) {
                    $join->on('c.id', '=', DB::raw("CASE 
                        WHEN i.invitee_user_id = $userId AND i.type = 'join' THEN i.inviter_id 
                        ELSE i.invitee_user_id 
                    END"));
                })
                
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    // 'i.confirm_status as jb_type',
                    'i.status',
                    'i.invite_token',
                    'i.type as jb_type',
                    'c.id as user_id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
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
            
            $deep = env('DEEPLINK_CUSTOMER');
            
            // dd($deep);
    
            foreach ($jobs as $job) {
    
                $jobInvites = $groupedInvites[$job->id] ?? collect();
                
                // dd($jobInvites);
    
                $accepted = $jobInvites
                    ->where('status', 'accepted')
                    ->map(function ($item) {
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'jb_type' => $item->jb_type,
                            'profile_img_url' => $item->profile_img_url,
                            'fcm_token' => $item->fcm_token,
                            'invite_token' => $item->invite_token
                        ];
                    })
                    ->values();
    
                $pending = $jobInvites
                    ->where('status', 'pending')
                    ->map(function ($item) {
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'jb_type' => $item->jb_type,
                            'profile_img_url' => $item->profile_img_url,
                            'fcm_token' => $item->fcm_token,
                            'invite_token' => $item->invite_token
                        ];
                    })
                    ->values();
                    
                $encryptedId = $this->encryptJobId($job->id);
                
                // return $deep.'/carpool?jid='.$encryptedId;
    
                $result[] = [
                    'job_id' => $job->id,
                    'job_no' => $job->job_no,
                    'user_id' => $job->user_id,
                    'name' => $job->name,
                    'pro_image' => $job->profile_img_url,
                    'fcm_token' => $job->fcm_token,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'pickup_date' => $job->pickup_date,
                    'seat' => $job->pass_count,
                    'fare' => $job->fare,
                    'jb_type' => $job->confirm_status,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                    'accepted_friends' => $accepted,
                    'pending_invitations' => $pending,
                    'deepLink' => $deep.'/carpool?jid='.$encryptedId
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
    
    public function jobInfo(Request $request)
    {
        try {
    
            $request->validate([
                'job_id' => ['required', 'exists:cus_job_temp,id']
            ]);
    
            $userId = auth()->id();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('global_type', 'carpool')
                ->first();
    
            if (!$job) {
                return ApiResponse::error('Job not found', 404);
            }
    
            $latestInviteIds = DB::table('invitations')
                ->where('job_id', $job->id)
                ->where('status', 'accepted')
                ->selectRaw('MAX(id) as id')
                ->groupBy('invitee_user_id')
                ->pluck('id');
    
            $passengers = DB::table('invitations as i')
                ->join('customer_register as c', 'c.id', '=', 'i.invitee_user_id')
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'c.id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
                )
                ->get();
    
            $job->passengers = $passengers->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'profile_img_url' => $p->profile_img_url,
                    'fcm_token' => $p->fcm_token
                ];
            })->values();
            
            $deep = env('DEEPLINK_CUSTOMER');
            $encryptedId = $this->encryptJobId($job->id);
            $job->deepLink = $deep.'/carpool?jid='.$encryptedId;
            
            $job->isLock = $job->isLock == 0 ? 'Unlocked' : 'Locked';
    
            return ApiResponse::success('Job retrieved', $job);
    
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function carPoolSearch(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => ['required', 'string'],
                'to_place_id' => ['required', 'string'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'seat' => ['required', 'numeric', 'min:1']
            ]);
    
            $userId = auth()->id();
    
            $fromGeo = $this->getLatLngByPlaceId($request->from_place_id);
            $toGeo   = $this->getLatLngByPlaceId($request->to_place_id);
            
            $now = now()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s');
    
            if (!$fromGeo || !$toGeo) {
                return ApiResponse::error('Invalid location');
            }
    
            $fromLat = $fromGeo['lat'];
            $fromLng = $fromGeo['lng'];
            $toLat   = $toGeo['lat'];
            $toLng   = $toGeo['lng'];
    
            // $pickupDate = Carbon::parse($request->pickup_date);
            $pickupDate = Carbon::parse($request->pickup_date)->toDateString();
            // $startTime = $pickupDate->copy()->subHours(2);
            // $endTime   = $pickupDate->copy()->addHours(2);
    
            $radius = 150;
    
            $jobs = DB::table('cus_job_temp as j')
                ->selectRaw("
                    j.id,
                    j.job_no,
                    j.from_place,
                    j.to_place,
                    j.isLock,
                    j.pickup_date,
                    j.pass_count as total_seats,
                    j.fare,
                    j.user_id,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.from_lat')))) *
                            cos(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.from_lng'))) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.from_lat'))))
                        )
                    ) AS pickup_distance,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.to_lat')))) *
                            cos(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.to_lng'))) - radians(?)) +
                            sin(radians(?)) *
                            sin(radians(JSON_UNQUOTE(JSON_EXTRACT(j.from_to_co, '$.to_lat'))))
                        )
                    ) AS drop_distance
                ", [
                    $fromLat, $fromLng, $fromLat,
                    $toLat, $toLng, $toLat
                ])
    
                ->where('j.global_type', 'carpool')
                ->where('j.isLock', 0)
                ->where('j.confirm_status', 1)
                ->whereNotIn('j.job_status', ['completed', 'cancelled'])
                ->where('j.user_id', '!=', $userId)
                // ->whereBetween('j.pickup_date', [$startTime, $endTime])
                ->where('j.pickup_date', '>=', $now)
                ->whereDate('j.pickup_date', $pickupDate)
    
                ->having('pickup_distance', '<=', $radius)
                ->having('drop_distance', '<=', $radius)
    
                ->orderByRaw('(pickup_distance + drop_distance) ASC')
                ->limit(50)
                ->get();
    
            if ($jobs->isEmpty()) {
                return ApiResponse::success('No rides found', []);
            }
    
            $jobIds = $jobs->pluck('id')->toArray();
            
            $pendingInvites = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->where('inviter_id', $userId)
                ->pluck('job_id')
                ->toArray();
    
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                // Group by the "other" person regardless of whether they are inviter or invitee
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->select('job_id', DB::raw('COUNT(*) as booked_seats'))
                ->groupBy('job_id')
                ->get()
                ->keyBy('job_id');
                
            // return $userId;
    
            $passengerList = DB::table('invitations as i')
                ->join('customer_register as c', function ($join) use ($userId) {
                    $join->on('c.id', '=', DB::raw("CASE 
                        WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                        ELSE i.invitee_user_id 
                    END"));
                })
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'c.id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
                )
                ->get()
                ->groupBy('job_id');
    
            $result = [];
            
            // dd($jobs);
            
            $deep = env('DEEPLINK_CUSTOMER');
    
            foreach ($jobs as $job) {
    
                $bookedSeats = $participants[$job->id]->booked_seats ?? 0;
                $availableSeats = $job->total_seats - $bookedSeats;
    
                if ($availableSeats < $request->seat) {
                    continue;
                }
    
                $passengers = $passengerList[$job->id] ?? collect();
                
                $encryptedId = $this->encryptJobId($job->id);
                
                if ($passengers->contains('id', $userId)) {
                    continue;
                }
    
                $result[] = [
                    'job_id' => $job->id,
                    'job_no' => $job->job_no,
                    'user_id' => $job->user_id,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'pickup_date' => $job->pickup_date,
                    'is_requested' => in_array($job->id, $pendingInvites),
                    'fare' => $job->fare,
                    'total_seats' => $job->total_seats,
                    'available_seats' => $availableSeats,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                    'pickup_distance_km' => (int) round($job->pickup_distance),
                    'drop_distance_km'   => (int) round($job->drop_distance),
    
                    'passenger_count' => $passengers->count(),
                    'passengers' => $passengers->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'profile_img_url' => $p->profile_img_url,
                            'fcm_token' => $p->fcm_token
                        ];
                    })->values(),
                    
                    'deepLink' => $deep.'/carpool?jid='.$encryptedId
                ];
            }
    
            return ApiResponse::success('Rides fetched', array_values($result));
    
        } catch (ValidationException $e) {
    
            return ApiResponse::error('Validation failed.', $e->errors(), 422);
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function homeJobs(Request $request)
    {
        try {
            
            $userId = auth()->id();
    
            $startDate = now()->toDateString();
            $endDate   = now()->addDays(2)->toDateString();
            
            // dd(auth()->user());
            $now = now()->timezone('Asia/Kolkata')->format('Y-m-d H:i:s');
    
            $jobs = DB::table('cus_job_temp as j')
                ->join('customer_register as c', 'c.id', 'j.user_id')
                ->select(
                    'j.id',
                    'j.job_no',
                    'j.from_place',
                    'j.to_place',
                    'j.isLock',
                    'j.pickup_date',
                    'j.pass_count as total_seats',
                    'j.fare',
                    'j.user_id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
                )
                ->where('j.global_type', 'carpool')
                ->where('j.isLock', 0)
                ->where('j.confirm_status', 1)
                ->whereNotIn('j.job_status', ['completed', 'cancelled'])
                ->where('j.user_id', '!=', $userId)
                ->where('j.pickup_date', '>=', $now)
                // ->whereBetween('j.pickup_date', [$startDate, $endDate])
                ->limit(50)
                ->get();
    
            if ($jobs->isEmpty()) {
                return ApiResponse::success('No rides found', []);
            }
    
            $jobIds = $jobs->pluck('id');
            
            $pendingInvites = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->where('inviter_id', $userId)
                ->pluck('job_id')
                ->toArray();
    
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'accepted')
                ->selectRaw('MAX(id) as id')
                ->groupBy('job_id', DB::raw("
                    CASE 
                        WHEN type = 'join' THEN inviter_id 
                        ELSE invitee_user_id 
                    END
                "))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->select('job_id', DB::raw('COUNT(*) as booked_seats'))
                ->groupBy('job_id')
                ->pluck('booked_seats', 'job_id');
    
            // ✅ STEP 4: Passenger profiles
            $passengerList = DB::table('invitations as i')
                ->join('customer_register as c', function ($join) {
                    $join->on('c.id', '=', DB::raw("
                        CASE 
                            WHEN i.type = 'join' THEN i.inviter_id 
                            ELSE i.invitee_user_id 
                        END
                    "));
                })
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'c.id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token'
                )
                ->get()
                ->groupBy('job_id');
                
            $deep = env('DEEPLINK_CUSTOMER');
    
            // ✅ STEP 5: Transform result
            $result = $jobs->map(function ($job) use ($participants, $passengerList, $request, $pendingInvites, $deep, $userId) {
    
                $bookedSeats   = $participants[$job->id] ?? 0;
                $availableSeats = max(0, $job->total_seats - $bookedSeats);
    
                // ✅ Seat filter
                if ($availableSeats < $request->seat) {
                    return null;
                }
    
                $passengers = $passengerList[$job->id] ?? collect();
                
                if ($passengers->contains('id', $userId)) {
                    return null;
                }
                
                $encryptedId = $this->encryptJobId($job->id);
    
                return [
                    'name' => $job->name,
                    'pro_image' => $job->profile_img_url,
                    'fcm_token' => $job->fcm_token,
                    'job_id' => $job->id,
                    'job_no' => $job->job_no,
                    'user_id' => $job->user_id,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'pickup_date' => $job->pickup_date,
                    'fare' => $job->fare,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
    
                    'total_seats' => (int) $job->total_seats,
                    'available_seats' => (int) $availableSeats,
                    
                    'is_requested' => in_array($job->id, $pendingInvites),
    
                    // Placeholder (until geo distance added)
                    'pickup_distance_km' => 0,
                    'drop_distance_km'   => 0,
    
                    'passenger_count' => $passengers->count(),
    
                    'passengers' => $passengers->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name,
                        'profile_img_url' => $p->profile_img_url,
                        'fcm_token' => $p->fcm_token
                    ])->values(),
                    
                    'deepLink' => $deep.'/carpool?jid='.$encryptedId
                ];
            })
            ->filter()
            ->values();
    
            return ApiResponse::success('Rides fetched', $result);
    
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function jobHistory(Request $request)
    {
        try {
            $userId = auth()->id();
    
            $ownJobs = [];
            
            $jobIds = DB::table('invitations')
                ->where(function ($query) use ($userId) {
                    $query->where('inviter_id', $userId)
                          ->where('type', 'join');
                })
                ->orWhere(function ($query) use ($userId) {
                    $query->where('invitee_user_id', $userId)
                          ->where('type', 'job');
                })
                ->pluck('job_id');
            
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                // ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                // Group by the "other" person regardless of whether they are inviter or invitee
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                ->pluck('id');
                
            // return $latestInviteIds;
                
            $passengerList = DB::table('invitations as i')
                ->join('customer_register as c', function ($join) use ($userId) {
                    $join->on('c.id', '=', DB::raw("CASE 
                        WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                        ELSE i.invitee_user_id 
                    END"));
                })
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'c.id',
                    'c.name',
                    'c.profile_img_url',
                    'c.fcm_token',
                    'i.type'
                )
                ->get()
                ->groupBy('job_id');
                
            // return $passengerList;
    
            // $joinedJobsQuery = DB::table('invitations as inv')
            $joinedJobsQuery = DB::table('cus_job_temp as job')
                ->join('customer_register as host', 'host.id', '=', 'job.user_id')
                ->whereIn('job.id', $jobIds)
                ->whereNotIn('job.job_status', ['completed', 'cancelled'])
                ->whereDate('job.pickup_date', '>=', now()->toDateString())
                // ->whereIn('inv.status', 'accepted'])
                // ->where('inv.type', 'job')
                ->where('job.deletes', '0');
            
            $joinedJobs = $joinedJobsQuery
                ->select(
                    'job.id',
                    'job.job_no',
                    'job.user_id',
                    'host.name',
                    'job.from_place',
                    'job.to_place',
                    'job.fare',
                    'job.isLock',
                    'job.pickup_date',
                    'job.pass_count as total_seats',
                    'job.filled_seat',
                    DB::raw("'join' as job_type")
                )
                ->latest('job.id')
                ->get();
                
            $deep = env('DEEPLINK_CUSTOMER');
            
            // return $joinedJobs;
    
            $jobs = $joinedJobs->map(function ($job) use ($passengerList, $deep) {

                $passengers = $passengerList[$job->id] ?? collect();
                
                $encryptedId = $this->encryptJobId($job->id);
    
                return [
                    'id' => $job->id,
                    'job_no' => $job->job_no,
                    'user_id' => $job->user_id,
                    'name' => $job->name,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'fare' => $job->fare,
                    'pickup_date' => $job->pickup_date,
                    'total_seats' => $job->total_seats,
                    'filled_seat' => $job->filled_seat,
                    'job_type' => $job->job_type,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
    
                    'passenger_count' => $passengers->count(),
    
                    'passengers' => $passengers->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'profile_img_url' => $p->profile_img_url,
                            'type' => $p->type,
                            'fcm_token' => $p->fcm_token
                        ];
                    })->values(),
                    
                    'deepLink' => $deep.'/carpool?jid='.$encryptedId
                ];
            });
            
    
            return ApiResponse::success('Job history fetched successfully', [
                'jobs' => $jobs
            ]);
    
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function cancelJob(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $request->validate([
                'job_id' => ['required'],
                'job_no' => ['required'],
                'reason' => ['nullable'],
                'docs'   => ['nullable']
            ]);
    
            $user   = auth()->user();
            $userId = $user->id;
    
            // 🔒 Lock job row
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->where('job_no', $request->job_no)
                ->where('job_status', '!=', 'cancelled')
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
    
            // ✅ Update status
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'job_status' => 'cancelled',
                    'updated_at' => now()
                ]);
    
            // ✅ Get related user IDs (optimized)
            $ids1 = DB::table('invitations')
                ->where('type', 'job')
                ->where('inviter_id', $userId)
                ->where('job_id',$job->id)
                ->where('status', 'accepted')
                ->pluck('invitee_user_id');
    
            $ids2 = DB::table('invitations')
                ->where('type', 'join')
                ->where('invitee_user_id', $userId)
                ->where('job_id', $job->id)
                ->where('status', 'accepted')
                ->pluck('inviter_id');
    
            $IDs = $ids1->merge($ids2)->unique()->values();
    
            // 🔥 Firebase delete
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount2['project_id'],
                $this->getAccessToken2()
            );
    
            $firebase->deleteJob($job->job_no);
    
            // 🔔 Send notification (only if users exist)
            if ($IDs->isNotEmpty()) {
                dispatch(new \App\Jobs\SendFcmNotificationJob(
                    type: 'job_cancelled',
                    userIds: $IDs->toArray(),
                    title: 'Job Cancelled',
                    body: 'Job ' . $job->job_no . ' has been cancelled by the owner.'
                ));
            }
    
            // 📝 Cancellation logs
            if ($request->filled('reason') || $request->filled('docs')) {
    
                $cancelId = DB::table('job_cancellations')->insertGetId([
                    'job_id'      => $job->id,
                    'customer_id' => $userId,
                    'reason'      => $request->reason,
                    'created_at'  => now()
                ]);
    
                if (!empty($request->docs)) {
    
                    $uniqueDocs = collect($request->docs)
                        ->filter()
                        ->unique()
                        ->values();
    
                    if ($uniqueDocs->isNotEmpty()) {
    
                        $insertData = $uniqueDocs->map(function ($doc) use ($cancelId) {
                            return [
                                'cancellation_id' => $cancelId,
                                'doc_url'         => $doc,
                                'created_at'      => now()
                            ];
                        })->toArray();
    
                        DB::table('job_cancellation_docs')->insert($insertData);
                    }
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
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'job_id' => $request->job_id ?? null
            ]);
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    
    public function jobLock(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $request->validate([
                'job_id' => ['required'],
                'job_no' => ['required']
            ]);
    
            $user   = auth()->user();
            $userId = $user->id;
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->where('job_no', $request->job_no)
                ->where('job_status', 'created')
                ->where('deletes', '0')
                ->lockForUpdate()
                ->first();
    
            if (!$job) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found.'
                ], 404);
            }
    
            $status = $job->isLock == 0 ? 1 : 0;
    
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'isLock'     => $status,
                    'updated_at' => now()
                ]);
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Job ' . ($status ? 'Locked' : 'Unlocked')
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
    
}