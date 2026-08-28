<?php

namespace App\Http\Controllers\Api\v5;

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
use App\Services\AutomationEventService;
use App\Services\GeoHashService;
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
    
    public function getOSRMDistanceNew($fromLat, $fromLng, $toLat, $toLng)
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
                        'distance'  => round($meters),
                        // 'distance_km'      => round($meters / 1000, 2),
                        'duration' => round($route['duration']),
                        // 'duration_text'    => gmdate("H:i:s", $route['duration']),
                        'summary'          => $route['summary'] ?? 'Main Road'
                    ];
                }
            }
    
            return ['status' => false, 'message' => 'Route not found'];
    
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
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
    
    public function encryptJobId($id, $key = 12345)
    {
        $xored = $id ^ $key;
        
        $stringValue = (string) $xored;
        
        $base64 = base64_encode($stringValue);
        
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
    
    public function createFirebaseJob(string $jobNo, array $data)
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
    
            if ($pickup->isToday() && $pickup->lessThan($now->copy()->addMinutes(30))) {
                return ApiResponse::error('Pickup time must be at least 30 minutes from now');
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
    
                $route = $this->getOSRMDistanceNew(
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
                'isOpen' => ['nullable'],
                'frequency_type' => ['nullable']
            ]);
    
            $user = auth()->user();
            $userId = $user->id;
            
            if(auth()->user()->doc_verify != 1 && auth()->user()->vehicle_verify != 2){
                
                return ApiResponse::error('KYC verification pending.');
            }
            
            $getDuration = DB::table('location_distance_web')
                ->where([
                    'from_place_id' => $request->from_place_id, 
                    'to_place_id' => $request->to_place_id
                ])
                ->first();
            
            $totalDurationMinutes = 0; 
            
            if ($getDuration) {
                $dur = $getDuration->duration;
                
                $hours = 0;
                $minutes = 0;
            
                if (preg_match('/(\d+)\s*hour/', $dur, $matches)) {
                    $hours = (int)$matches[1];
                }
                if (preg_match('/(\d+)\s*min/', $dur, $matches)) {
                    $minutes = (int)$matches[1];
                }
            
                $totalDurationMinutes = ($hours * 60) + $minutes;
            }
            
            // FIX: Convert the incoming string into a Carbon instance safely
            $newJobStart = Carbon::parse($request->pickup_date);
            
            // FIX: Clone the start time so adding minutes doesn't overwrite $newJobStart
            $newJobEnd = $newJobStart->clone()->addMinutes($totalDurationMinutes);
            
            $conflictingJob = DB::table('cus_job_temp')
                ->where('user_id', $userId)
                ->where('global_type', 'carpool')
                ->whereNotIn('job_status', ['completed', 'cancelled'])
                ->where(function ($query) use ($newJobStart, $newJobEnd) {
                    $query->where('pickup_date', '<', $newJobEnd)
                          ->where(DB::raw("DATE_ADD(pickup_date, INTERVAL 4 HOUR)"), '>', $newJobStart);
                })
                ->select(['pickup_date']) // Select the start time of the problem job
                ->first();
            
            if ($conflictingJob) {
                $existingJobStart = Carbon::parse($conflictingJob->pickup_date);
                // Calculate the end time using the same 4-hour interval logic from your query
                $existingJobEnd = $existingJobStart->clone()->addHours(4); 
            
                // Format times cleanly for readability (e.g., "2026-05-21 02:30 PM")
                $startTimeFormatted = $existingJobStart->format('Y-m-d h:i A');
                $endTimeFormatted = $existingJobEnd->format('h:i A');
            
                return ApiResponse::error(
                    // "You already have a job booked during this time window (Busy from {$startTimeFormatted} to {$endTimeFormatted})."
                    "You already have a ride scheduled during this time. Please choose a different pickup time."
                );
            }
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            // $c_seat = 0;
            $c_seat = null;
            
            // if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            if (!empty($vehicleDetails['choosed_vehicle'])) {
        
                // $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                // $c_seat = $rcVehicle['seat_capacity'] ?? null;
                // $body_type = $rcVehicle['body_type'] ?? null;
                
                $c_seat = $vehicleDetails['choosed_vehicle'];
        
            } else {
    
                // $get_ocr = DB::connection('mysql_log')->table('ocr_request')
                //     ->where('user_id', $userId)
                //     ->where('doc_type', 'RC')
                //     ->orderByDesc('id')
                //     ->first();
        
                // if ($get_ocr) {
                    
                //     $c_seat = $get_ocr->seater ?? null;
                //     if($c_seat == 'mini' || $c_seat == 'Mini' || $c_seat == 'Mini 4'){
                //         $c_seat = 5;
                //     }
                // }
            }
            
            // if ($request->seat > ($c_seat - 1)) {
            if ($c_seat && ($c_seat == 'BIKE' && $request->seat > 1)) {
                return ApiResponse::error("Seat count exceeds your vehicle's maximum capacity.");
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
                'route_id'      => $request->route_id,
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
            
            $todayDay = date('l'); 
            $freDays = $request->frequency_type ?? [];
            
            $freDaysLookUp = array_flip($freDays);
            $is_empty = empty($freDays);
            $fromId = $data['from_place_id'] ?? null;
            $toId   = $data['to_place_id'] ?? null;
            
            if(!$is_empty){
                $userId   = auth()->id();
                
                $existingJobs = DB::table('frequency_job')
                    ->where('user_id', $userId)
                    ->where('global_type', 'carpool')
                    ->where('status', 0)
                    ->get();
            
                foreach ($existingJobs as $job) {
                    $savedData = json_decode($job->job_data, true);
                    
                    if (($savedData['from_place_id'] ?? null) == $fromId && 
                        ($savedData['to_place_id'] ?? null) == $toId) {
                        
                        $savedDays = explode(',', $job->frequency_type);
                        $intersect = array_intersect($freDays, $savedDays);
                        
                        if (!empty($intersect)) {
                            return ApiResponse::error("You already have a schedule for " . implode(', ', $intersect) . " on this route.", 200);
                        }
                    }
                }
                
            }
            
            $com = 0;
            $ovFare = (int)$data['fare'] + $com;
            $tax = 0;
            
            $pay_amount = $ovFare + $tax;
            
            $farebreak = ['total_fare' => $pay_amount, 'com' => $com, 'tax' => $tax, 'base_fare' => (int)$data['fare']];
            
            $data['fare_breakdown'] = json_encode($farebreak);
            
            // 1. Insert into local temp job table
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            // 2. Prepare data payload for Firebase
            $data['poster_name'] = auth()->user()->name ?? 'System';
            $data['id'] = $create_job;
            unset($data['confirm_status'], $data['route_id']);
            
            // 3. Push to Firebase
            // $this->createFirebaseJob($job_no, $data);
            
            $getPoly = DB::table('route_options')->where('id', $request->route_id)->first();
            
            if($getPoly){
                $this->ensureRoutePoints(
                    $getPoly->id,
                    $getPoly->polyline
                );
            }
            
            if ($is_empty) {
                // Today matches the frequency. Execute immediately using a transaction for data safety.
                // $res = DB::transaction(function () use ($todayDay, $freDays, $data, $job_no, $is_empty, $fromId, $toId, $create_job) {
                    
                //     if(!$is_empty){
                        
                //         $remainingDays = array_diff($freDays, [$todayDay]);
                //         $frequencyText = implode(',', $remainingDays);
                        
                //         $data['confirm_status'] = 1;
                        
                //         $frId = DB::table('frequency_job')->insertGetId([
                //             'global_type'    => 'carpool',
                //             'user_id'        => auth()->id(),
                //             'from_place_id'        => $fromId,
                //             'to_place_id'        => $toId,
                //             'frequency_type' => $frequencyText,
                //             'job_data'       => json_encode($data),
                //             // 'status'         => 'pending',
                //             'created_at'     => now(),
                //             'updated_at'     => now()
                //         ]);
                //     }
                    
                //     return [
                //         'job_id' => $create_job,
                //         'job_no' => $job_no
                //     ];
                    
                // });
                
                $res = [
                    'job_id'  => $create_job,
                    'job_no'  => $job_no
                ];
                
                
                
                $chCount = DB::table('cus_job_temp')->where('user_id', auth()->user()->id)->where('global_type', 'carpool')->count();
                
                if($chCount == 1){
                    AutomationEventService::trigger(
                        'carpool_post_first_mess',
                        auth()->user()->id
                    );
                }else{
                    AutomationEventService::trigger(
                        'carpool_after_post',
                        auth()->user()->id
                    );
                }
                
                AutomationEventService::trigger(
                    'carpool_pick_hour_before_host',
                    auth()->user()->id,
                    [
                        'ride_id' => $create_job
                    ]
                );
                
                // $jobId = $create_job; // Your existing job id
                // $userId = auth()->id();
                
                app(\App\Services\SocietyGroupService::class)->shareJobToMyGroups($create_job, $userId);
                
                return ApiResponse::success('Job Created', $res);
            
            } else if (!$is_empty) {
                
                $frequencyText = implode(',', $freDays);
                
                $frId = DB::table('frequency_job')->insertGetId([
                    'global_type'    => 'carpool',
                    'user_id'        => $userId,
                    'from_place_id'  => $fromId,
                    'to_place_id'    => $toId,
                    'frequency_type' => $frequencyText,
                    'job_data'       => json_encode($data),
                    // 'status'         => 'pending',
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]);
                
                $res = [
                    'job_id'  => $create_job,
                    'job_no'  => $job_no,
                    'message' => "Your jobs have been scheduled for: " . implode(', ', $freDays) . "."
                ];
                
                return ApiResponse::success('Job Created', $res);
            }
            
            
    
        } catch (ValidationException $e) {
            
            return ApiResponse::error('Validation failed.', [], 422);
    
        } catch (\Exception $e) {
            
            return ApiResponse::error($e->getMessage());
            
        }
    }
    
    // private function ensureRoutePoints(
    //     $routeId,
    //     $polyline
    // )
    // {
    //     try {
    
    //         $exists = DB::table('route_points')
    //             ->where('route_id', $routeId)
    //             ->exists();
    
    //         if ($exists) {
    //             return true;
    //         }
    
    //         $this->storeRoutePoints(
    //             $routeId,
    //             $polyline
    //         );
    
    //         return true;
    
    //     } catch (\Throwable $e) {
    
    //         \Log::error(
    //             'Ensure Route Points Error',
    //             [
    //                 'message' => $e->getMessage()
    //             ]
    //         );
    
    //         return false;
    //     }
    // }
    
    private function storeRoutePoints($routeId, $encodedPolyline)
    {
        try {
            if (!$encodedPolyline) {
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | Decode Polyline (Returns flat array: [lat1, lng1, lat2, lng2...])
            |--------------------------------------------------------------------------
            */
            $points = \Polyline::decode($encodedPolyline);
    
            if (empty($points)) {
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | Delete Existing
            |--------------------------------------------------------------------------
            */
            DB::table('route_points')
                ->where('route_id', $routeId)
                ->delete();
    
            $insertData = [];
            $order = 1;
            $geoHash = new GeohashService();
            $totalValues = count($points);
    
            /*
            |--------------------------------------------------------------------------
            | Sample Every 20th Point (Step of 40 in a flat array)
            |--------------------------------------------------------------------------
            */
            for ($i = 0; $i < $totalValues; $i += 40) {
                
                $lat = $points[$i] ?? null;
                $lng = $points[$i + 1] ?? null;
    
                if ($lat !== null && $lng !== null) {
                    // Generate Geohash using your new Service
                    $hash = $geoHash->encode($lat, $lng, 6);
    
                    $insertData[] = [
                        'route_id'    => $routeId,
                        'latitude'    => $lat,
                        'longitude'   => $lng,
                        'geohash'     => $hash,
                        'point_order' => $order,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                    $order++;
                }
            }
    
            /*
            |--------------------------------------------------------------------------
            | Ensure the Last Point of the route is always included
            |--------------------------------------------------------------------------
            */
            if ($totalValues >= 2 && ($totalValues - 2) % 40 !== 0) {
                $lastLat = $points[$totalValues - 2];
                $lastLng = $points[$totalValues - 1];
                
                $insertData[] = [
                    'route_id'    => $routeId,
                    'latitude'    => $lastLat,
                    'longitude'   => $lastLng,
                    'geohash'     => $geoHash->encode($lastLat, $lastLng, 6),
                    'point_order' => $order,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Bulk Insert
            |--------------------------------------------------------------------------
            */
            if (!empty($insertData)) {
                foreach (array_chunk($insertData, 1000) as $chunk) {
                    DB::table('route_points')->insert($chunk);
                }
            }
    
        } catch (\Throwable $e) {
            \Log::error('Store Route Points Error', [
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function myJobs()
    {
        try {
            $userId = auth()->id();
    
            $jobs = DB::table('cus_job_temp as job')
            
                ->leftJoin('customer_register as cust', function ($join) {
                    $join->on('cust.id', '=', 'job.user_id')
                         ->where('job.global_type', '=', 'carpool');
                })
                // Join User table for non-carpool
                ->leftJoin('user_register as user', function ($join) {
                    $join->on('user.id', '=', 'job.user_id')
                         ->where('job.global_type', '!=', 'carpool');
                })
                ->where('job.user_id', $userId)
                ->whereIn('job.global_type', ['carpool', 'dr_carpool'])
                ->whereNotIn('job.job_status', ['completed', 'cancelled'])
                ->whereDate('job.pickup_date', '>=', now()->toDateString())
                ->orderByDesc('job.id')
                ->select(
                    'job.*',
                    DB::raw('COALESCE(cust.name, user.name) as name'),
                    DB::raw('COALESCE(cust.fcm_token, user.fcm_token) as fcm_token'),
                    DB::raw('COALESCE(cust.profile_img_url, user.profile_img_url) as profile_img_url')
                )
                ->get();
    
            if ($jobs->isEmpty()) {
                return ApiResponse::success('No jobs found', []);
            }
    
            $jobIds = $jobs->pluck('id')->toArray();
            
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
           
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
            
                ->pluck('id');
                

            $invites = DB::table('invitations as i')
      
                ->leftJoin('customer_register as c', function ($join) use ($userId) {
                    $join->on('c.id', '=', DB::raw("CASE 
                        WHEN i.global_type = 'carpool' AND i.invitee_user_id = " . (int)$userId . " AND i.type = 'join' THEN i.inviter_id 
                        WHEN i.global_type = 'carpool' THEN i.invitee_user_id 
                        ELSE NULL 
                    END"));
                })
                
                ->leftJoin('user_register as u', function ($join) use ($userId) {
                    $join->on('u.id', '=', DB::raw("CASE 
                        WHEN i.global_type = 'dr_carpool' AND i.invitee_user_id = " . (int)$userId . " AND i.type = 'join' THEN i.inviter_id 
                        WHEN i.global_type = 'dr_carpool' THEN i.invitee_user_id 
                        ELSE NULL 
                    END"));
                })
                
                ->leftJoin('kyc_carpool as kc', function ($join) {
                    $join->on('kc.user_id', '=', 'c.id');
                })
                
                ->leftJoin('kyc_details as kd', function ($join) {
                    $join->on('kd.user_id', '=', 'u.id');
                })
                
                ->whereIn('i.id', $latestInviteIds)
                
                ->select(
                    'i.job_id',
                    'i.status',
                    'i.invite_token',
                    'i.type as jb_type',
                    'i.otp',
                    DB::raw('COALESCE(c.id, u.id) as user_id'),
                    DB::raw('COALESCE(c.name, u.name) as name'),
                    DB::raw("
                        CASE
                            WHEN i.global_type = 'carpool'
                            THEN COALESCE(c.profile_img_url, kc.selfie_url)
                            ELSE COALESCE(u.profile_img_url, kd.selfie_url)
                        END AS profile_img_url
                    "),
                    DB::raw('COALESCE(c.fcm_token, u.fcm_token) as fcm_token')
                )
                
                ->get();

            $groupedInvites = $invites->groupBy('job_id');
    
            $result = [];
            
            $deep = env('DEEPLINK_CUSTOMER');
            
            foreach ($jobs as $job) {
    
                $jobInvites = $groupedInvites[$job->id] ?? collect();
                
                $accepted = $jobInvites
                    ->where('status', 'accepted')
                    ->map(function ($item) {
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'jb_type' => $item->jb_type,
                            'profile_img_url' => $item->profile_img_url,
                            'fcm_token' => $item->fcm_token,
                            'otp' => $item->otp,
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
                            'otp' => $item->otp,
                            'invite_token' => $item->invite_token
                        ];
                    })
                    ->values();
                    
                $encryptedId = $this->encryptJobId($job->id);
                
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
                ->leftJoin('customer_register as cr', function ($join) {
                    $join->on('cr.id', '=', DB::raw("CASE 
                        WHEN i.type = 'join' THEN i.inviter_id 
                        ELSE i.invitee_user_id 
                    END"))
                    ->where('i.global_type', '=', 'carpool');
                })
                ->whereIn('i.id', $latestInviteIds)
                ->select([
                    'cr.id',  
                    'cr.name',   
                    'cr.profile_img_url',
                    'cr.fcm_token'
                ])
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
            
            if($request->invite_token){
                $get_fare = DB::table('invitations')->where(['invite_token' => $request->invite_token, 'job_id' => $request->job_id])->first();
                
                if($get_fare){
                    $job->fare = $get_fare->collectAmt;
                }
            }
    
            return ApiResponse::success('Job retrieved', $job);
    
        } catch (ValidationException $e) {
            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function carPoolSearchOld(Request $request)
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
                ->leftJoin('customer_register as cr', 'cr.id', '=', 'j.user_id')
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
                    cr.name,
                    cr.profile_img_url,
                    cr.fcm_token,
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(cr.vehicle_details, '$.choosed_vehicle')), '') as choosed_vehicle,
    
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
                ->where('j.user_id', '!=', 0)
                // ->whereBetween('j.pickup_date', [$startTime, $endTime])
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
                // ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                // Group by the "other" person regardless of whether they are inviter or invitee
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->where('status', 'accepted')
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
                    'i.status',
                    'c.fcm_token'
                )
                ->get()
                ->groupBy('job_id');
    
            $result = [];
            
            
            $deep = env('DEEPLINK_CUSTOMER');
    
            foreach ($jobs as $job) {
    
                $bookedSeats = $participants[$job->id]->booked_seats ?? 0;
                $availableSeats = $job->total_seats - $bookedSeats;
    
                if ($availableSeats < $request->seat) {
                    continue;
                }
    
                $passengers = $passengerList[$job->id] ?? collect();
                
                $encryptedId = $this->encryptJobId($job->id);
                
                // if ($passengers->contains('id', $userId)) {
                //     continue;
                // }
                
                if ($passengers->contains(function ($passenger) use ($userId) {
                    return $passenger->id === $userId && 
                           in_array($passenger->status, ['accepted', 'pending']);
                })) {
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
                    'choosed_vehicle' => $job->choosed_vehicle,
                    'name' => $job->name,
                    'pro_image' => $job->profile_img_url,
                    'fcm_token' => $job->fcm_token,
    
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
    
    public function carPoolSearchOld2(Request $request)
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
                ->leftJoin('customer_register as cr', function ($join) {
                    $join->on('cr.id', '=', 'j.user_id')
                         ->where('j.global_type', '=', 'carpool');
                })
                ->leftJoin('user_register as ur', function ($join) {
                    $join->on('ur.id', '=', 'j.user_id')
                         ->where('j.global_type', '!=', 'carpool');
                })
                ->selectRaw("
                    j.id,
                    j.global_type,
                    j.job_no,
                    j.from_place,
                    j.to_place,
                    j.isLock,
                    j.pickup_date,
                    j.pass_count as total_seats,
                    j.fare,
                    j.user_id,
                    
                    -- Conditional User Metadata Fields
                    CASE WHEN j.global_type = 'carpool' THEN cr.name ELSE ur.name END as name,
                    CASE WHEN j.global_type = 'carpool' THEN cr.profile_img_url ELSE ur.profile_img_url END as profile_img_url,
                    CASE WHEN j.global_type = 'carpool' THEN cr.fcm_token ELSE ur.fcm_token END as fcm_token,
                    
                    IFNULL(JSON_UNQUOTE(JSON_EXTRACT(cr.vehicle_details, '$.choosed_vehicle')), '') as choosed_vehicle,
            
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
            
                ->whereIn('j.global_type', ['carpool', 'dr_carpool'])
                ->where('j.isLock', 0)
                ->where('j.confirm_status', 1)
                ->whereNotIn('j.job_status', ['completed', 'cancelled'])
                ->where('j.user_id', '!=', $userId)
                ->where('j.user_id', '!=', 0)
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
                // ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                // Group by the "other" person regardless of whether they are inviter or invitee
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->where('status', 'accepted')
                ->select('job_id', DB::raw('COUNT(*) as booked_seats'))
                ->groupBy('job_id')
                ->get()
                ->keyBy('job_id');
                
            // return $userId;
    
            $passengerList = DB::table('invitations as i')
        
                ->leftJoin('customer_register as cr', function ($join) use ($userId) {
                    $join->on('cr.id', '=', DB::raw("CASE 
                        WHEN i.global_type = 'carpool' AND (i.inviter_id = " . (int)$userId . " OR i.type = 'join') THEN i.inviter_id 
                        WHEN i.global_type = 'carpool' THEN i.invitee_user_id 
                        ELSE NULL 
                    END"));
                })
                
                ->leftJoin('user_register as ur', function ($join) use ($userId) {
                    $join->on('ur.id', '=', DB::raw("CASE 
                        WHEN i.global_type = 'dr_carpool' AND (i.inviter_id = " . (int)$userId . " OR i.type = 'join') THEN i.inviter_id 
                        WHEN i.global_type = 'dr_carpool' THEN i.invitee_user_id 
                        ELSE NULL 
                    END"));
                })
                
           
                ->whereIn('i.id', $latestInviteIds)
                
            
                ->select(
                    'i.job_id',
                    'i.status',
                    DB::raw('COALESCE(cr.id, ur.id) as id'),
                    DB::raw('COALESCE(cr.name, ur.name) as name'),
                    DB::raw('COALESCE(cr.profile_img_url, ur.profile_img_url) as profile_img_url'),
                    DB::raw('COALESCE(cr.fcm_token, ur.fcm_token) as fcm_token')
                )
                
      
                ->get()
                ->groupBy('job_id');
    
            $result = [];
            
            
            $deep = env('DEEPLINK_CUSTOMER');
    
            foreach ($jobs as $job) {
    
                $bookedSeats = $participants[$job->id]->booked_seats ?? 0;
                $availableSeats = $job->total_seats - $bookedSeats;
    
                if ($availableSeats < $request->seat) {
                    continue;
                }
    
                $passengers = $passengerList[$job->id] ?? collect();
                
                $encryptedId = $this->encryptJobId($job->id);
                
                // if ($passengers->contains('id', $userId)) {
                //     continue;
                // }
                
                if ($passengers->contains(function ($passenger) use ($userId) {
                    return $passenger->id === $userId && 
                           in_array($passenger->status, ['accepted', 'pending']);
                })) {
                    continue;
                }
    
                $result[] = [
                    'job_id' => $job->id,
                    'global_type' => $job->global_type,
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
                    'choosed_vehicle' => $job->choosed_vehicle,
                    'name' => $job->name,
                    'pro_image' => $job->profile_img_url,
                    'fcm_token' => $job->fcm_token,
    
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
    
    public function carPoolSearchNew1(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => ['required', 'string'],
                'to_place_id' => ['required', 'string'],
                'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
                'seat' => ['required', 'numeric', 'min:1']
            ]);
    
            $userId = auth()->id();
    
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
    
            $pickupDate = Carbon::parse(
                $request->pickup_date
            )->toDateString();
    
            /*
            |--------------------------------------------------------------------------
            | Radius
            |--------------------------------------------------------------------------
            */
    
            $radius = 5;
    
            /*
            |--------------------------------------------------------------------------
            | DRIVER CARPOOL SEARCH
            |--------------------------------------------------------------------------
            |
            | Only dr_carpool uses route/journey_stops logic
            |
            */
    
            $pickupStops = DB::table('journey_stops')
    
                ->select(
                    'journey_stops.*',
    
                    DB::raw("
                        (
                            6371 * acos(
                                cos(radians(".$fromLat.")) *
                                cos(radians(latitude)) *
                                cos(
                                    radians(longitude) -
                                    radians(".$fromLng.")
                                ) +
                                sin(radians(".$fromLat.")) *
                                sin(radians(latitude))
                            )
                        ) AS distance
                    ")
                )
    
                ->having('distance', '<=', $radius)
    
                ->get();
    
            $dropStops = DB::table('journey_stops')
    
                ->select(
                    'journey_stops.*',
    
                    DB::raw("
                        (
                            6371 * acos(
                                cos(radians(".$toLat.")) *
                                cos(radians(latitude)) *
                                cos(
                                    radians(longitude) -
                                    radians(".$toLng.")
                                ) +
                                sin(radians(".$toLat.")) *
                                sin(radians(latitude))
                            )
                        ) AS distance
                    ")
                )
    
                ->having('distance', '<=', $radius)
    
                ->get();
    
            $matchedDriverJobIds = [];
    
            if (
                $pickupStops->isNotEmpty() &&
                $dropStops->isNotEmpty()
            ) {
    
                $pickupGrouped = $pickupStops
                    ->groupBy('job_id');
    
                $dropGrouped = $dropStops
                    ->groupBy('job_id');
    
                foreach ($pickupGrouped as $jobId => $pickupItems) {
    
                    if (!isset($dropGrouped[$jobId])) {
                        continue;
                    }
    
                    $pickupOrder = $pickupItems
                        ->min('stop_order');
    
                    $dropOrder = $dropGrouped[$jobId]
                        ->max('stop_order');
    
                    /*
                    |--------------------------------------------------------------------------
                    | Correct Direction
                    |--------------------------------------------------------------------------
                    */
    
                    if ($pickupOrder < $dropOrder) {
    
                        $matchedDriverJobIds[] = $jobId;
                    }
                }
            }
    
            /*
            |--------------------------------------------------------------------------
            | MAIN SEARCH QUERY
            |--------------------------------------------------------------------------
            |
            | customer carpool -> old logic
            | driver carpool   -> route stop logic
            |
            */
    
            $jobs = DB::table('cus_job_temp as j')
    
                ->leftJoin('customer_register as cr', function ($join) {
    
                    $join->on('cr.id', '=', 'j.user_id')
                        ->where(
                            'j.global_type',
                            '=',
                            'carpool'
                        );
                })
    
                ->leftJoin('user_register as ur', function ($join) {
    
                    $join->on('ur.id', '=', 'j.user_id')
                        ->where(
                            'j.global_type',
                            '!=',
                            'carpool'
                        );
                })
    
                ->leftJoin(
                    'route_options as ro',
                    'ro.id',
                    '=',
                    'j.route_id'
                )
    
                ->selectRaw("
                    j.id,
                    j.global_type,
                    j.job_no,
                    j.from_place,
                    j.to_place,
                    j.isLock,
                    j.pickup_date,
                    j.pass_count as total_seats,
                    j.fare,
                    j.user_id,
    
                    ro.summary,
                    ro.polyline,
                    ro.distance_meters,
                    ro.duration_seconds,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.name
                        ELSE ur.name
                    END as name,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.profile_img_url
                        ELSE ur.profile_img_url
                    END as profile_img_url,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.fcm_token
                        ELSE ur.fcm_token
                    END as fcm_token,
    
                    IFNULL(
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                cr.vehicle_details,
                                '$.choosed_vehicle'
                            )
                        ),
                        ''
                    ) as choosed_vehicle,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lat'
                                        )
                                    )
                                )
                            ) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lng'
                                        )
                                    )
                                ) - radians(?)
                            ) +
                            sin(radians(?)) *
                            sin(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lat'
                                        )
                                    )
                                )
                            )
                        )
                    ) AS pickup_distance,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lat'
                                        )
                                    )
                                )
                            ) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lng'
                                        )
                                    )
                                ) - radians(?)
                            ) +
                            sin(radians(?)) *
                            sin(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lat'
                                        )
                                    )
                                )
                            )
                        )
                    ) AS drop_distance
                ", [
                    $fromLat,
                    $fromLng,
                    $fromLat,
    
                    $toLat,
                    $toLng,
                    $toLat
                ])
    
                ->where(function ($query) use (
                    $matchedDriverJobIds,
                    $radius
                ) {
    
                    /*
                    |--------------------------------------------------------------------------
                    | Existing Customer Carpool Logic
                    |--------------------------------------------------------------------------
                    */
    
                    $query->where(function ($q) use ($radius) {
    
                        $q->where(
                            'j.global_type',
                            'carpool'
                        )
    
                        ->having(
                            'pickup_distance',
                            '<=',
                            $radius
                        )
    
                        ->having(
                            'drop_distance',
                            '<=',
                            $radius
                        );
                    });
    
                    /*
                    |--------------------------------------------------------------------------
                    | Driver Carpool Logic
                    |--------------------------------------------------------------------------
                    */
    
                    if (!empty($matchedDriverJobIds)) {
    
                        $query->orWhere(function ($q) use (
                            $matchedDriverJobIds
                        ) {
    
                            $q->where(
                                'j.global_type',
                                'dr_carpool'
                            )
    
                            ->whereIn(
                                'j.id',
                                $matchedDriverJobIds
                            );
                        });
                    }
                })
    
                ->whereIn(
                    'j.global_type',
                    ['carpool', 'dr_carpool']
                )
    
                ->where('j.isLock', 0)
    
                ->where('j.confirm_status', 1)
    
                ->whereNotIn(
                    'j.job_status',
                    ['completed', 'cancelled']
                )
    
                ->where('j.user_id', '!=', $userId)
    
                ->where('j.user_id', '!=', 0)
    
                ->whereDate(
                    'j.pickup_date',
                    $pickupDate
                )
    
                ->orderByRaw(
                    '(pickup_distance + drop_distance) ASC'
                )
    
                ->limit(50)
    
                ->get();
    
            if ($jobs->isEmpty()) {
    
                return ApiResponse::success(
                    'No rides found',
                    []
                );
            }
    
            /*
            |--------------------------------------------------------------------------
            | Existing Invitation Logic
            |--------------------------------------------------------------------------
            */
    
            $jobIds = $jobs->pluck('id')->toArray();
    
            $pendingInvites = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->where('inviter_id', $userId)
                ->pluck('job_id')
                ->toArray();
    
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                ->groupBy(DB::raw("
                    CASE
                        WHEN type = 'join'
                        THEN inviter_id
                        ELSE invitee_user_id
                    END
                "))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->where('status', 'accepted')
                ->select(
                    'job_id',
                    DB::raw('COUNT(*) as booked_seats')
                )
                ->groupBy('job_id')
                ->get()
                ->keyBy('job_id');
    
            /*
            |--------------------------------------------------------------------------
            | Passenger List
            |--------------------------------------------------------------------------
            */
    
            $passengerList = DB::table('invitations as i')
    
                ->leftJoin(
                    'customer_register as cr',
                    function ($join) use ($userId) {
    
                        $join->on(
                            'cr.id',
                            '=',
                            DB::raw("
                                CASE
                                    WHEN i.global_type = 'carpool'
                                    AND (
                                        i.inviter_id = ".(int)$userId."
                                        OR i.type = 'join'
                                    )
                                    THEN i.inviter_id
    
                                    WHEN i.global_type = 'carpool'
                                    THEN i.invitee_user_id
    
                                    ELSE NULL
                                END
                            ")
                        );
                    }
                )
    
                ->leftJoin(
                    'user_register as ur',
                    function ($join) use ($userId) {
    
                        $join->on(
                            'ur.id',
                            '=',
                            DB::raw("
                                CASE
                                    WHEN i.global_type = 'dr_carpool'
                                    AND (
                                        i.inviter_id = ".(int)$userId."
                                        OR i.type = 'join'
                                    )
                                    THEN i.inviter_id
    
                                    WHEN i.global_type = 'dr_carpool'
                                    THEN i.invitee_user_id
    
                                    ELSE NULL
                                END
                            ")
                        );
                    }
                )
    
                ->whereIn('i.id', $latestInviteIds)
    
                ->select(
                    'i.job_id',
                    'i.status',
    
                    DB::raw(
                        'COALESCE(cr.id, ur.id) as id'
                    ),
    
                    DB::raw(
                        'COALESCE(cr.name, ur.name) as name'
                    ),
    
                    DB::raw(
                        'COALESCE(
                            cr.profile_img_url,
                            ur.profile_img_url
                        ) as profile_img_url'
                    ),
    
                    DB::raw(
                        'COALESCE(
                            cr.fcm_token,
                            ur.fcm_token
                        ) as fcm_token'
                    )
                )
    
                ->get()
    
                ->groupBy('job_id');
    
            /*
            |--------------------------------------------------------------------------
            | Final Response
            |--------------------------------------------------------------------------
            */
    
            $result = [];
    
            $deep = env('DEEPLINK_CUSTOMER');
    
            foreach ($jobs as $job) {
    
                $bookedSeats =
                    $participants[$job->id]
                        ->booked_seats ?? 0;
    
                $availableSeats =
                    $job->total_seats - $bookedSeats;
    
                if ($availableSeats < $request->seat) {
                    continue;
                }
    
                $passengers =
                    $passengerList[$job->id]
                        ?? collect();
    
                $encryptedId =
                    $this->encryptJobId($job->id);
    
                if (
                    $passengers->contains(
                        function ($passenger) use ($userId) {
    
                            return
                                $passenger->id === $userId &&
                                in_array(
                                    $passenger->status,
                                    ['accepted', 'pending']
                                );
                        }
                    )
                ) {
                    continue;
                }
    
                $result[] = [
    
                    'job_id' => $job->id,
    
                    'global_type' => $job->global_type,
    
                    'job_no' => $job->job_no,
    
                    'user_id' => $job->user_id,
    
                    'from_place' => $job->from_place,
    
                    'to_place' => $job->to_place,
    
                    'pickup_date' => $job->pickup_date,
    
                    'is_requested' => in_array(
                        $job->id,
                        $pendingInvites
                    ),
    
                    'fare' => $job->fare,
    
                    'total_seats' => $job->total_seats,
    
                    'available_seats' => $availableSeats,
    
                    'isLock' => $job->isLock == 0
                        ? 'Unlocked'
                        : 'Locked',
    
                    /*
                    |--------------------------------------------------------------------------
                    | KEEP EXISTING RESPONSE KEYS
                    |--------------------------------------------------------------------------
                    */
    
                    'pickup_distance_km' =>
                        (int) round(
                            $job->pickup_distance ?? 0
                        ),
    
                    'drop_distance_km' =>
                        (int) round(
                            $job->drop_distance ?? 0
                        ),
    
                    'choosed_vehicle' =>
                        $job->choosed_vehicle,
    
                    'name' => $job->name,
    
                    'pro_image' =>
                        $job->profile_img_url,
    
                    'fcm_token' =>
                        $job->fcm_token,
    
                    'passenger_count' =>
                        $passengers->count(),
    
                    'passengers' => $passengers
    
                        ->map(function ($p) {
    
                            return [
    
                                'id' => $p->id,
    
                                'name' => $p->name,
    
                                'profile_img_url' =>
                                    $p->profile_img_url,
    
                                'fcm_token' =>
                                    $p->fcm_token
                            ];
                        })
    
                        ->values(),
    
                    /*
                    |--------------------------------------------------------------------------
                    | NEW OPTIONAL ROUTE DATA
                    |--------------------------------------------------------------------------
                    */
    
                    'route_summary' =>
                        $job->summary,
    
                    'route_polyline' =>
                        $job->polyline,
    
                    'route_distance_km' =>
                        $job->distance_meters
                            ? round(
                                $job->distance_meters / 1000,
                                2
                            )
                            : 0,
    
                    'route_duration_minutes' =>
                        $job->duration_seconds
                            ? round(
                                $job->duration_seconds / 60
                            )
                            : 0,
    
                    'deepLink' =>
                        $deep.'/carpool?jid='.$encryptedId
                ];
            }
    
            return ApiResponse::success(
                'Rides fetched',
                array_values($result)
            );
    
        } catch (ValidationException $e) {
    
            return ApiResponse::error(
                'Validation failed.',
                $e->errors(),
                422
            );
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error(
                $e->getMessage()
            );
        }
    }
    
    // public function carPoolSearch(Request $request)
    // {
    //     try {
    
    //         $request->validate([
    //             'from_place_id' => ['required', 'string'],
    //             'to_place_id'   => ['required', 'string'],
    //             'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
    //             'seat'          => ['required', 'numeric', 'min:1']
    //         ]);
    
    //         $userId = auth()->id();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Get Coordinates
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $fromGeo = $this->getLatLngByPlaceId(
    //             $request->from_place_id
    //         );
    
    //         $toGeo = $this->getLatLngByPlaceId(
    //             $request->to_place_id
    //         );
    
    //         if (!$fromGeo || !$toGeo) {
    
    //             return ApiResponse::error(
    //                 'Invalid location'
    //             );
    //         }
    
    //         $fromLat = $fromGeo['lat'];
    //         $fromLng = $fromGeo['lng'];
    
    //         $toLat = $toGeo['lat'];
    //         $toLng = $toGeo['lng'];
            
    //         $res = $this->getOSRMDistance($fromLat, $fromLng, $toLat, $toLng);
            
    //         $dis = 0;
            
    //         if ($res['status']) {
    //             $dis = $res['distance_km'];
    //             // echo "Travel Time: " . $res['duration_text'];
    //         }
    
    //         $pickupDate = Carbon::parse(
    //             $request->pickup_date
    //         )->toDateString();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Existing Radius
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $radius = 5;
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | DRIVER CARPOOL MATCHING
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $matchedDriverJobIds = [];
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | STEP 1 - STOP MATCHING
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $pickupStops = DB::table('journey_stops')
    
    //             ->select(
    //                 'journey_stops.*',
    
    //                 DB::raw("
    //                     (
    //                         6371 * acos(
    //                             cos(radians(".$fromLat.")) *
    //                             cos(radians(latitude)) *
    //                             cos(
    //                                 radians(longitude) -
    //                                 radians(".$fromLng.")
    //                             ) +
    //                             sin(radians(".$fromLat.")) *
    //                             sin(radians(latitude))
    //                         )
    //                     ) AS distance
    //                 ")
    //             )
    
    //             ->having('distance', '<=', $radius)
    
    //             ->get();
    
    //         $dropStops = DB::table('journey_stops')
    
    //             ->select(
    //                 'journey_stops.*',
    
    //                 DB::raw("
    //                     (
    //                         6371 * acos(
    //                             cos(radians(".$toLat.")) *
    //                             cos(radians(latitude)) *
    //                             cos(
    //                                 radians(longitude) -
    //                                 radians(".$toLng.")
    //                             ) +
    //                             sin(radians(".$toLat.")) *
    //                             sin(radians(latitude))
    //                         )
    //                     ) AS distance
    //                 ")
    //             )
    
    //             ->having('distance', '<=', $radius)
    
    //             ->get();
    
    //         if (
    //             $pickupStops->isNotEmpty() &&
    //             $dropStops->isNotEmpty()
    //         ) {
    
    //             $pickupGrouped = $pickupStops
    //                 ->groupBy('job_id');
    
    //             $dropGrouped = $dropStops
    //                 ->groupBy('job_id');
    
    //             foreach ($pickupGrouped as $jobId => $pickupItems) {
    
    //                 if (!isset($dropGrouped[$jobId])) {
    //                     continue;
    //                 }
    
    //                 $pickupOrder = $pickupItems
    //                     ->min('stop_order');
    
    //                 $dropOrder = $dropGrouped[$jobId]
    //                     ->max('stop_order');
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Correct Direction
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 if ($pickupOrder < $dropOrder) {
    
    //                     $matchedDriverJobIds[] = $jobId;
    //                 }
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | STEP 2 - PHASE 3 GEOHASH MATCHING
    //         |--------------------------------------------------------------------------
    //         */
            
    //         // return $matchedDriverJobIds;
            
    
    //         if (empty($matchedDriverJobIds)) {
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Generate Passenger Geohashes
    //             |--------------------------------------------------------------------------
    //             */
    //             // Precision
    //             // 6	~1.2 KM
    //             // 5	~4.9 KM
    //             // 4	~39 KM
    
    //             $pickupHash = substr(
    //                 $this->getGeoHashPrefix(
    //                     $fromLat,
    //                     $fromLng,
    //                     5
    //                 ),
    //                 0,
    //                 4
    //             );
                
    //             // dd($pickupHash);
    
    //             $dropHash = substr(
    //                 $this->getGeoHashPrefix(
    //                     $toLat,
    //                     $toLng,
    //                     5
    //                 ),
    //                 0,
    //                 4
    //             );
    
    //             // dd($dropHash);
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Pickup Route Match
    //             |--------------------------------------------------------------------------
    //             */
    
    //             $pickupRouteIds = DB::table('route_points')
    
    //                 ->where(
    //                     'geohash',
    //                     'LIKE',
    //                     $pickupHash.'%'
    //                 )
    
    //                 ->pluck('route_id')
    
    //                 ->unique()
    
    //                 ->toArray();
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Drop Route Match
    //             |--------------------------------------------------------------------------
    //             */
    
    //             $dropRouteIds = DB::table('route_points')
    
    //                 ->where(
    //                     'geohash',
    //                     'LIKE',
    //                     $dropHash.'%'
    //                 )
    
    //                 ->pluck('route_id')
    
    //                 ->unique()
    
    //                 ->toArray();
    
    //             // dd($pickupRouteIds);
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Common Route IDs
    //             |--------------------------------------------------------------------------
    //             */
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Common Route IDs
    //             |--------------------------------------------------------------------------
    //             */
                
    //             $matchedRouteIds = array_values(
    //                 array_intersect(
    //                     $pickupRouteIds,
    //                     $dropRouteIds
    //                 )
    //             );
                
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Direction Validation
    //             |--------------------------------------------------------------------------
    //             */
                
    //             $matchedDriverJobIds = [];
                
    //             foreach ($matchedRouteIds as $routeId) {
                
    //                 $pickupPoint = DB::table('route_points')
                
    //                     ->where('route_id', $routeId)
                
    //                     ->where(
    //                         'geohash',
    //                         'LIKE',
    //                         $pickupHash.'%'
    //                     )
                
    //                     ->orderBy('point_order')
                
    //                     ->first();
                
    //                 $dropPoint = DB::table('route_points')
                
    //                     ->where('route_id', $routeId)
                
    //                     ->where(
    //                         'geohash',
    //                         'LIKE',
    //                         $dropHash.'%'
    //                     )
                
    //                     ->orderBy('point_order')
                
    //                     ->first();
                
    //                 if (
    //                     !$pickupPoint ||
    //                     !$dropPoint
    //                 ) {
    //                     continue;
    //                 }
                
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Avoid Reverse Direction
    //                 |--------------------------------------------------------------------------
    //                 */
                
    //                 if (
    //                     $pickupPoint->point_order >=
    //                     $dropPoint->point_order
    //                 ) {
    //                     continue;
    //                 }
                
    //                 $jobIds = DB::table('cus_job_temp')
                
    //                     ->where(
    //                         'global_type',
    //                         'dr_carpool'
    //                     )
                
    //                     ->where(
    //                         'route_id',
    //                         $routeId
    //                     )
                
    //                     ->pluck('id')
                
    //                     ->toArray();
                
    //                 $matchedDriverJobIds = array_merge(
    //                     $matchedDriverJobIds,
    //                     $jobIds
    //                 );
    //             }
                
    //             $matchedDriverJobIds = array_unique(
    //                 $matchedDriverJobIds
    //             );
    //         }
            
    //         // return $matchedDriverJobIds;
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | MAIN SEARCH QUERY
    //         |--------------------------------------------------------------------------
    //         */
            
    //         // CASE 
    //         //             WHEN j.global_type = 'carpool' THEN CAST(j.fare AS CHAR)
    //         //             ELSE IFNULL(j.fare_breakdown->>'$.total_fare', '0') 
    //         //         END as fare,
    
    //         $jobs = DB::table('cus_job_temp as j')
    
    //             ->leftJoin('customer_register as cr', function ($join) {
    
    //                 $join->on('cr.id', '=', 'j.user_id')
    //                     ->where(
    //                         'j.global_type',
    //                         '=',
    //                         'carpool'
    //                     );
    //             })
    
    //             ->leftJoin('user_register as ur', function ($join) {
    
    //                 $join->on('ur.id', '=', 'j.user_id')
    //                     ->where(
    //                         'j.global_type',
    //                         '!=',
    //                         'carpool'
    //                     );
    //             })
    
    //             ->leftJoin(
    //                 'route_options as ro',
    //                 'ro.id',
    //                 '=',
    //                 'j.route_id'
    //             )
    
    //             ->selectRaw("
    //                 j.id,
    //                 j.global_type,
    //                 j.job_no,
    //                 j.from_place,
    //                 j.from_place_id,
    //                 j.to_place,
    //                 j.to_place_id,
    //                 j.isLock,
    //                 j.pickup_date,
    //                 j.pass_count as total_seats,
    //                 j.fare,
                    
    //                 CASE 
    //                     WHEN j.global_type = 'carpool' THEN CAST(j.fare AS CHAR)
    //                     ELSE IFNULL(j.fare_breakdown->>'$.total_fare', '0') 
    //                 END as total_fare,
                    
    //                 j.user_id,
    
    //                 ro.summary,
    //                 ro.polyline,
    //                 ro.distance_meters,
    //                 ro.duration_seconds,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.name
    //                     ELSE ur.name
    //                 END as name,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.profile_img_url
    //                     ELSE ur.profile_img_url
    //                 END as profile_img_url,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.fcm_token
    //                     ELSE ur.fcm_token
    //                 END as fcm_token,
    
    //                 IFNULL(
    //                     JSON_UNQUOTE(
    //                         JSON_EXTRACT(
    //                             cr.vehicle_details,
    //                             '$.choosed_vehicle'
    //                         )
    //                     ),
    //                     ''
    //                 ) as choosed_vehicle,
    
    //                 (
    //                     6371 * acos(
    //                         cos(radians(?)) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lat'
    //                                     )
    //                                 )
    //                             )
    //                         ) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lng'
    //                                     )
    //                                 )
    //                             ) - radians(?)
    //                         ) +
    //                         sin(radians(?)) *
    //                         sin(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lat'
    //                                     )
    //                                 )
    //                             )
    //                         )
    //                     )
    //                 ) AS pickup_distance,
    
    //                 (
    //                     6371 * acos(
    //                         cos(radians(?)) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lat'
    //                                     )
    //                                 )
    //                             )
    //                         ) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lng'
    //                                     )
    //                                 )
    //                             ) - radians(?)
    //                         ) +
    //                         sin(radians(?)) *
    //                         sin(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lat'
    //                                     )
    //                                 )
    //                             )
    //                         )
    //                     )
    //                 ) AS drop_distance
    //             ", [
    //                 $fromLat,
    //                 $fromLng,
    //                 $fromLat,
    
    //                 $toLat,
    //                 $toLng,
    //                 $toLat
    //             ])
    
    //             ->where(function ($query) use (
    //                 $matchedDriverJobIds,
    //                 $radius
    //             ) {
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Existing Customer Carpool Logic
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 $query->where(function ($q) use ($radius) {
    
    //                     $q->where(
    //                         'j.global_type',
    //                         'carpool'
    //                     )
    
    //                     ->having(
    //                         'pickup_distance',
    //                         '<=',
    //                         $radius
    //                     )
    
    //                     ->having(
    //                         'drop_distance',
    //                         '<=',
    //                         $radius
    //                     );
    //                 });
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Driver Route Logic
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 if (!empty($matchedDriverJobIds)) {
    
    //                     $query->orWhere(function ($q) use (
    //                         $matchedDriverJobIds
    //                     ) {
    
    //                         $q->where(
    //                             'j.global_type',
    //                             'dr_carpool'
    //                         )
    
    //                         ->whereIn(
    //                             'j.id',
    //                             $matchedDriverJobIds
    //                         );
    //                     });
    //                 }
    //             })
    
    //             ->whereIn(
    //                 'j.global_type',
    //                 ['carpool', 'dr_carpool']
    //             )
    
    //             ->where('j.isLock', 0)
    
    //             ->where('j.confirm_status', 1)
    
    //             ->whereNotIn(
    //                 'j.job_status',
    //                 ['completed', 'cancelled']
    //             )
    
    //             ->where('j.user_id', '!=', $userId)
    
    //             ->where('j.user_id', '!=', 0)
    
    //             ->whereDate(
    //                 'j.pickup_date',
    //                 $pickupDate
    //             )
    
    //             ->orderByRaw(
    //                 '(pickup_distance + drop_distance) ASC'
    //             )
    
    //             ->limit(50)
    
    //             ->get();
    
    //         if ($jobs->isEmpty()) {
    
    //             return ApiResponse::success(
    //                 'No rides found',
    //                 []
    //             );
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Existing Invitation Logic
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $jobIds = $jobs->pluck('id')->toArray();
    
    //         $pendingInvites = DB::table('invitations')
    //             ->whereIn('job_id', $jobIds)
    //             ->where('status', 'pending')
    //             ->where('inviter_id', $userId)
    //             ->pluck('job_id')
    //             ->toArray();
    
    //         $latestInviteIds = DB::table('invitations')
    //             ->whereIn('job_id', $jobIds)
    //             ->select(DB::raw('MAX(id) as id'))
    //             ->groupBy('job_id')
    //             ->groupBy(DB::raw("
    //                 CASE
    //                     WHEN type = 'join'
    //                     THEN inviter_id
    //                     ELSE invitee_user_id
    //                 END
    //             "))
    //             ->pluck('id');
    
    //         $participants = DB::table('invitations')
    //             ->whereIn('id', $latestInviteIds)
    //             ->where('status', 'accepted')
    //             ->select(
    //                 'job_id',
    //                 DB::raw('COUNT(*) as booked_seats')
    //             )
    //             ->groupBy('job_id')
    //             ->get()
    //             ->keyBy('job_id');
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Passenger List
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $passengerList = DB::table('invitations as i')
    
    //             ->leftJoin(
    //                 'customer_register as cr',
    //                 function ($join) use ($userId) {
    
    //                     $join->on(
    //                         'cr.id',
    //                         '=',
    //                         DB::raw("
    //                             CASE
    //                                 WHEN i.global_type = 'carpool'
    //                                 AND (
    //                                     i.inviter_id = ".(int)$userId."
    //                                     OR i.type = 'join'
    //                                 )
    //                                 THEN i.inviter_id
    
    //                                 WHEN i.global_type = 'carpool'
    //                                 THEN i.invitee_user_id
    
    //                                 ELSE NULL
    //                             END
    //                         ")
    //                     );
    //                 }
    //             )
    
    //             ->leftJoin(
    //                 'customer_register as ur',
    //                 function ($join) use ($userId) {
    
    //                     $join->on(
    //                         'ur.id',
    //                         '=',
    //                         DB::raw("
    //                             CASE
    //                                 WHEN i.global_type = 'dr_carpool'
    //                                 AND (
    //                                     i.inviter_id = ".(int)$userId."
    //                                     OR i.type = 'join'
    //                                 )
    //                                 THEN i.inviter_id
    
    //                                 WHEN i.global_type = 'dr_carpool'
    //                                 THEN i.invitee_user_id
    
    //                                 ELSE NULL
    //                             END
    //                         ")
    //                     );
    //                 }
    //             )
    
    //             ->whereIn('i.id', $latestInviteIds)
    
    //             ->select(
    //                 'i.job_id',
    //                 'i.status',
    
    //                 DB::raw(
    //                     'COALESCE(cr.id, ur.id) as id'
    //                 ),
    
    //                 DB::raw(
    //                     'COALESCE(cr.name, ur.name) as name'
    //                 ),
    
    //                 DB::raw(
    //                     'COALESCE(
    //                         cr.profile_img_url,
    //                         ur.profile_img_url
    //                     ) as profile_img_url'
    //                 ),
    
    //                 DB::raw(
    //                     'COALESCE(
    //                         cr.fcm_token,
    //                         ur.fcm_token
    //                     ) as fcm_token'
    //                 )
    //             )
    
    //             ->get()
    
    //             ->groupBy('job_id');
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Final Response
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $result = [];
    
    //         $deep = env('DEEPLINK_CUSTOMER');
    
    //         foreach ($jobs as $job) {
    
    //             $bookedSeats =
    //                 $participants[$job->id]
    //                     ->booked_seats ?? 0;
    
    //             $availableSeats =
    //                 $job->total_seats - $bookedSeats;
    
    //             if ($availableSeats < $request->seat) {
    //                 continue;
    //             }
    
    //             $passengers =
    //                 $passengerList[$job->id]
    //                     ?? collect();
    
    //             $encryptedId =
    //                 $this->encryptJobId($job->id);
    
    //             if (
    //                 $passengers->contains(
    //                     function ($passenger) use ($userId) {
    
    //                         return
    //                             $passenger->id === $userId &&
    //                             in_array(
    //                                 $passenger->status,
    //                                 ['accepted', 'pending']
    //                             );
    //                     }
    //                 )
    //             ) {
    //                 continue;
    //             }
                
    //             $isVal = false;
    //             $perKms = 5;
                
    //             if(($request->from_place_id != $job->from_place_id) || ($request->to_place_id != $job->to_place_id)){
    //                 $isVal = true;
    //             }
                
    //             if ($job->global_type == 'dr_carpool') {
    //                 $perKm = DB::table('user_register')
    //                     ->where('id', $job->user_id)
    //                     ->value('per_km');
                    
    //                 $perKm = $perKm == 0 ? $perKms : $perKm;
                
    //                 if ($dis != 0 && $isVal) {
    //                     $fare = $dis * $perKm;
                        
    //                     $com = $fare * 0.05;
    //                     $tax = ($fare + $com) * 0.05;
                        
    //                     $job->fare = round($fare + $com + $tax);
    //                 }else{
    //                     $job->fare = $job->total_fare;
                        
    //                 }
    //             }
                
    
    //             $result[] = [
    
    //                 'job_id' => $job->id,
    
    //                 'global_type' => $job->global_type,
    
    //                 'job_no' => $job->job_no,
    
    //                 'user_id' => $job->user_id,
    
    //                 'from_place' => $job->from_place,
    
    //                 'to_place' => $job->to_place,
    
    //                 'pickup_date' => $job->pickup_date,
    
    //                 'is_requested' => in_array(
    //                     $job->id,
    //                     $pendingInvites
    //                 ),
    
    //                 'fare' => $job->fare,
    
    //                 'total_seats' => $job->total_seats,
    
    //                 'available_seats' => $availableSeats,
    
    //                 'isLock' => $job->isLock == 0
    //                     ? 'Unlocked'
    //                     : 'Locked',
    
    //                 'pickup_distance_km' =>
    //                     (int) round(
    //                         $job->pickup_distance ?? 0
    //                     ),
    
    //                 'drop_distance_km' =>
    //                     (int) round(
    //                         $job->drop_distance ?? 0
    //                     ),
    
    //                 'choosed_vehicle' =>
    //                     $job->choosed_vehicle,
    
    //                 'name' => $job->name,
    
    //                 'pro_image' =>
    //                     $job->profile_img_url,
    
    //                 'fcm_token' =>
    //                     $job->fcm_token,
    
    //                 'passenger_count' =>
    //                     $passengers->count(),
    
    //                 'passengers' => $passengers
    
    //                     ->map(function ($p) {
    
    //                         return [
    
    //                             'id' => $p->id,
    
    //                             'name' => $p->name,
    
    //                             'profile_img_url' =>
    //                                 $p->profile_img_url,
    
    //                             'fcm_token' =>
    //                                 $p->fcm_token
    //                         ];
    //                     })
    
    //                     ->values(),
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Optional Route Data
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 'route_summary' =>
    //                     $job->summary,
    
    //                 'route_polyline' =>
    //                     $job->polyline,
    
    //                 'route_distance_km' =>
    //                     $job->distance_meters
    //                         ? round(
    //                             $job->distance_meters / 1000,
    //                             2
    //                         )
    //                         : 0,
    
    //                 'route_duration_minutes' =>
    //                     $job->duration_seconds
    //                         ? round(
    //                             $job->duration_seconds / 60
    //                         )
    //                         : 0,
    
    //                 'deepLink' =>
    //                     $deep.'/carpool?jid='.$encryptedId
    //             ];
    //         }
    
    //         return ApiResponse::success(
    //             'Rides fetched',
    //             array_values($result)
    //         );
    
    //     } catch (ValidationException $e) {
    
    //         return ApiResponse::error(
    //             'Validation failed.',
    //             $e->errors(),
    //             422
    //         );
    
    //     } catch (\Throwable $e) {
    
    //         return ApiResponse::error(
    //             $e->getMessage()
    //         );
    //     }
    // }
    
    // public function carPoolSearch(Request $request)
    // {
    //     try {
    
    //         $request->validate([
    //             'from_place_id' => ['required', 'string'],
    //             'to_place_id'   => ['required', 'string'],
    //             'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
    //             'seat'          => ['required', 'numeric', 'min:1']
    //         ]);
    
    //         $userId = auth()->id();
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Get Coordinates
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $fromGeo = $this->getLatLngByPlaceId(
    //             $request->from_place_id
    //         );
    
    //         $toGeo = $this->getLatLngByPlaceId(
    //             $request->to_place_id
    //         );
    
    //         if (!$fromGeo || !$toGeo) {
    
    //             return ApiResponse::error(
    //                 'Invalid location'
    //             );
    //         }
    
    //         $fromLat = $fromGeo['lat'];
    //         $fromLng = $fromGeo['lng'];
    
    //         $toLat = $toGeo['lat'];
    //         $toLng = $toGeo['lng'];
            
    //         $res = $this->getOSRMDistance($fromLat, $fromLng, $toLat, $toLng);
            
    //         $dis = 0;
            
    //         if ($res['status']) {
    //             $dis = $res['distance_km'];
    //             // echo "Travel Time: " . $res['duration_text'];
    //         }
    
    //         $pickupDate = Carbon::parse(
    //             $request->pickup_date
    //         )->toDateString();
            
    //         // $pickupDateTime = Carbon::parse($request->pickup_date);
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Existing Radius
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $radius = 5;
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | DRIVER CARPOOL MATCHING
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $matchedDriverJobIds = [];
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | STEP 1 - STOP MATCHING
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $pickupStops = DB::table('journey_stops')
    
    //             ->select(
    //                 'journey_stops.*',
    
    //                 DB::raw("
    //                     (
    //                         6371 * acos(
    //                             cos(radians(".$fromLat.")) *
    //                             cos(radians(latitude)) *
    //                             cos(
    //                                 radians(longitude) -
    //                                 radians(".$fromLng.")
    //                             ) +
    //                             sin(radians(".$fromLat.")) *
    //                             sin(radians(latitude))
    //                         )
    //                     ) AS distance
    //                 ")
    //             )
    
    //             ->having('distance', '<=', $radius)
    
    //             ->get();
    
    //         $dropStops = DB::table('journey_stops')
    
    //             ->select(
    //                 'journey_stops.*',
    
    //                 DB::raw("
    //                     (
    //                         6371 * acos(
    //                             cos(radians(".$toLat.")) *
    //                             cos(radians(latitude)) *
    //                             cos(
    //                                 radians(longitude) -
    //                                 radians(".$toLng.")
    //                             ) +
    //                             sin(radians(".$toLat.")) *
    //                             sin(radians(latitude))
    //                         )
    //                     ) AS distance
    //                 ")
    //             )
    
    //             ->having('distance', '<=', $radius)
    
    //             ->get();
    
    //         if (
    //             $pickupStops->isNotEmpty() &&
    //             $dropStops->isNotEmpty()
    //         ) {
    
    //             $pickupGrouped = $pickupStops
    //                 ->groupBy('job_id');
    
    //             $dropGrouped = $dropStops
    //                 ->groupBy('job_id');
    
    //             foreach ($pickupGrouped as $jobId => $pickupItems) {
    
    //                 if (!isset($dropGrouped[$jobId])) {
    //                     continue;
    //                 }
    
    //                 $pickupOrder = $pickupItems
    //                     ->min('stop_order');
    
    //                 $dropOrder = $dropGrouped[$jobId]
    //                     ->max('stop_order');
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Correct Direction
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 if ($pickupOrder < $dropOrder) {
    
    //                     $matchedDriverJobIds[] = $jobId;
    //                 }
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | STEP 2 - PHASE 3 GEOHASH MATCHING
    //         |--------------------------------------------------------------------------
    //         */
            
    //         // return $matchedDriverJobIds;
            
    
    //         if (empty($matchedDriverJobIds)) {
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Generate Passenger Geohashes
    //             |--------------------------------------------------------------------------
    //             */
    //             // Precision
    //             // 6	~1.2 KM
    //             // 5	~4.9 KM
    //             // 4	~39 KM
    
    //             $pickupHash = substr(
    //                 $this->getGeoHashPrefix(
    //                     $fromLat,
    //                     $fromLng,
    //                     5
    //                 ),
    //                 0,
    //                 4
    //             );
                
    //             // dd($pickupHash);
    
    //             $dropHash = substr(
    //                 $this->getGeoHashPrefix(
    //                     $toLat,
    //                     $toLng,
    //                     5
    //                 ),
    //                 0,
    //                 4
    //             );
    
    //             $pickupRouteIds = DB::table('route_points')
    
    //                 ->where(
    //                     'geohash',
    //                     'LIKE',
    //                     $pickupHash.'%'
    //                 )
    
    //                 ->pluck('route_id')
    
    //                 ->unique()
    
    //                 ->toArray();
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Drop Route Match
    //             |--------------------------------------------------------------------------
    //             */
    
    //             $dropRouteIds = DB::table('route_points')
    
    //                 ->where(
    //                     'geohash',
    //                     'LIKE',
    //                     $dropHash.'%'
    //                 )
    
    //                 ->pluck('route_id')
    
    //                 ->unique()
    
    //                 ->toArray();
    
    //             // dd($pickupRouteIds);
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Common Route IDs
    //             |--------------------------------------------------------------------------
    //             */
    
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Common Route IDs
    //             |--------------------------------------------------------------------------
    //             */
                
    //             $matchedRouteIds = array_values(
    //                 array_intersect(
    //                     $pickupRouteIds,
    //                     $dropRouteIds
    //                 )
    //             );
                
    //             /*
    //             |--------------------------------------------------------------------------
    //             | Direction Validation
    //             |--------------------------------------------------------------------------
    //             */
                
    //             $matchedDriverJobIds = [];
                
    //             foreach ($matchedRouteIds as $routeId) {
                
    //                 $pickupPoint = DB::table('route_points')
                
    //                     ->where('route_id', $routeId)
                
    //                     ->where(
    //                         'geohash',
    //                         'LIKE',
    //                         $pickupHash.'%'
    //                     )
                
    //                     ->orderBy('point_order')
                
    //                     ->first();
                
    //                 $dropPoint = DB::table('route_points')
                
    //                     ->where('route_id', $routeId)
                
    //                     ->where(
    //                         'geohash',
    //                         'LIKE',
    //                         $dropHash.'%'
    //                     )
                
    //                     ->orderBy('point_order')
                
    //                     ->first();
                
    //                 if (
    //                     !$pickupPoint ||
    //                     !$dropPoint
    //                 ) {
    //                     continue;
    //                 }
                
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Avoid Reverse Direction
    //                 |--------------------------------------------------------------------------
    //                 */
                
    //                 if (
    //                     $pickupPoint->point_order >=
    //                     $dropPoint->point_order
    //                 ) {
    //                     continue;
    //                 }
                
    //                 $jobIds = DB::table('cus_job_temp')
                
    //                     ->where(
    //                         'global_type',
    //                         'dr_carpool'
    //                     )
                
    //                     ->where(
    //                         'route_id',
    //                         $routeId
    //                     )
                
    //                     ->pluck('id')
                
    //                     ->toArray();
                
    //                 $matchedDriverJobIds = array_merge(
    //                     $matchedDriverJobIds,
    //                     $jobIds
    //                 );
    //             }
                
    //             $matchedDriverJobIds = array_unique(
    //                 $matchedDriverJobIds
    //             );
    //         }
            
    //         // return $matchedDriverJobIds;
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | MAIN SEARCH QUERY
    //         |--------------------------------------------------------------------------
    //         */
            
    //         // CASE 
    //         //             WHEN j.global_type = 'carpool' THEN CAST(j.fare AS CHAR)
    //         //             ELSE IFNULL(j.fare_breakdown->>'$.total_fare', '0') 
    //         //         END as fare,
    
    //         $jobs = DB::table('cus_job_temp as j')
    
    //             ->leftJoin('customer_register as cr', function ($join) {
    
    //                 $join->on('cr.id', '=', 'j.user_id')
    //                     ->where(
    //                         'j.global_type',
    //                         '=',
    //                         'carpool'
    //                     );
    //             })
    
    //             ->leftJoin('user_register as ur', function ($join) {
    
    //                 $join->on('ur.id', '=', 'j.user_id')
    //                     ->where(
    //                         'j.global_type',
    //                         '!=',
    //                         'carpool'
    //                     );
    //             })
    
    //             ->leftJoin(
    //                 'route_options as ro',
    //                 'ro.id',
    //                 '=',
    //                 'j.route_id'
    //             )
    
    //             ->selectRaw("
    //                 j.id,
    //                 j.global_type,
    //                 j.job_no,
    //                 j.from_place,
    //                 j.from_place_id,
    //                 j.to_place,
    //                 j.to_place_id,
    //                 j.isLock,
    //                 j.pickup_date,
    //                 j.pass_count as total_seats,
    //                 j.fare,
    //                 j.route_id,
                    
    //                 CASE 
    //                     WHEN j.global_type = 'carpool' THEN CAST(j.fare AS CHAR)
    //                     ELSE IFNULL(j.fare_breakdown->>'$.total_fare', '0') 
    //                 END as total_fare,
                    
    //                 j.user_id,
    
    //                 ro.summary,
    //                 ro.polyline,
    //                 ro.distance_meters,
    //                 ro.duration_seconds,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.name
    //                     ELSE ur.name
    //                 END as name,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.profile_img_url
    //                     ELSE ur.profile_img_url
    //                 END as profile_img_url,
    
    //                 CASE
    //                     WHEN j.global_type = 'carpool'
    //                     THEN cr.fcm_token
    //                     ELSE ur.fcm_token
    //                 END as fcm_token,
    
    //                 IFNULL(
    //                     JSON_UNQUOTE(
    //                         JSON_EXTRACT(
    //                             cr.vehicle_details,
    //                             '$.choosed_vehicle'
    //                         )
    //                     ),
    //                     ''
    //                 ) as choosed_vehicle,
    
    //                 (
    //                     6371 * acos(
    //                         cos(radians(?)) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lat'
    //                                     )
    //                                 )
    //                             )
    //                         ) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lng'
    //                                     )
    //                                 )
    //                             ) - radians(?)
    //                         ) +
    //                         sin(radians(?)) *
    //                         sin(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.from_lat'
    //                                     )
    //                                 )
    //                             )
    //                         )
    //                     )
    //                 ) AS pickup_distance,
    
    //                 (
    //                     6371 * acos(
    //                         cos(radians(?)) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lat'
    //                                     )
    //                                 )
    //                             )
    //                         ) *
    //                         cos(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lng'
    //                                     )
    //                                 )
    //                             ) - radians(?)
    //                         ) +
    //                         sin(radians(?)) *
    //                         sin(
    //                             radians(
    //                                 JSON_UNQUOTE(
    //                                     JSON_EXTRACT(
    //                                         j.from_to_co,
    //                                         '$.to_lat'
    //                                     )
    //                                 )
    //                             )
    //                         )
    //                     )
    //                 ) AS drop_distance
    //             ", [
    //                 $fromLat,
    //                 $fromLng,
    //                 $fromLat,
    
    //                 $toLat,
    //                 $toLng,
    //                 $toLat
    //             ])
    
    //             ->where(function ($query) use (
    //                 $matchedDriverJobIds,
    //                 $radius
    //             ) {
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Existing Customer Carpool Logic
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 $query->where(function ($q) use ($radius) {
    
    //                     $q->where(
    //                         'j.global_type',
    //                         'carpool'
    //                     )
                        
    //                     ->having(
    //                         'drop_distance',
    //                         '<=',
    //                         $radius
    //                     )
    
    //                     ->having(
    //                         'pickup_distance',
    //                         '<=',
    //                         $radius
    //                     );
    
    //                 });
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Driver Route Logic
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 if (!empty($matchedDriverJobIds)) {
    
    //                     $query->orWhere(function ($q) use (
    //                         $matchedDriverJobIds
    //                     ) {
    
    //                         $q->where(
    //                             'j.global_type',
    //                             'dr_carpool'
    //                         )
    
    //                         ->whereIn(
    //                             'j.id',
    //                             $matchedDriverJobIds
    //                         );
    //                     });
    //                 }
    //             })
    
    //             ->whereIn(
    //                 'j.global_type',
    //                 ['carpool', 'dr_carpool']
    //             )
    
    //             ->where('j.isLock', 0)
    
    //             ->where('j.confirm_status', 1)
    
    //             ->whereNotIn(
    //                 'j.job_status',
    //                 ['completed', 'cancelled']
    //             )
    
    //             ->where('j.user_id', '!=', $userId)
    
    //             ->where('j.user_id', '!=', 0)
    
    //             ->whereDate('j.pickup_date', $pickupDate)
    //             ->whereTime('j.pickup_date', '>', now()->toTimeString())
                
    //             ->orderByRaw(
    //                 '(pickup_distance + drop_distance) ASC'
    //             )
                
    //             ->limit(50)
    
    //             ->get();
    
    //         if ($jobs->isEmpty()) {
    
    //             return ApiResponse::success(
    //                 'No rides found',
    //                 []
    //             );
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Existing Invitation Logic
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $jobIds = $jobs->pluck('id')->toArray();
    
    //         $pendingInvites = DB::table('invitations')
    //             ->whereIn('job_id', $jobIds)
    //             ->where('status', 'pending')
    //             ->where('inviter_id', $userId)
    //             ->pluck('job_id')
    //             ->toArray();
    
    //         $latestInviteIds = DB::table('invitations')
    //             ->whereIn('job_id', $jobIds)
    //             ->select(DB::raw('MAX(id) as id'))
    //             ->groupBy('job_id')
    //             ->groupBy(DB::raw("
    //                 CASE
    //                     WHEN type = 'join'
    //                     THEN inviter_id
    //                     ELSE invitee_user_id
    //                 END
    //             "))
    //             ->pluck('id');
    
    //         $participants = DB::table('invitations')
    //             ->whereIn('id', $latestInviteIds)
    //             ->where('status', 'accepted')
    //             ->select(
    //                 'job_id',
    //                 DB::raw('COUNT(*) as booked_seats')
    //             )
    //             ->groupBy('job_id')
    //             ->get()
    //             ->keyBy('job_id');
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Passenger List
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $passengerList = DB::table('invitations as i')
    
    //             ->leftJoin(
    //                 'customer_register as cr',
    //                 function ($join) use ($userId) {
    
    //                     $join->on(
    //                         'cr.id',
    //                         '=',
    //                         DB::raw("
    //                             CASE
    //                                 WHEN i.global_type = 'carpool'
    //                                 AND (
    //                                     i.inviter_id = ".(int)$userId."
    //                                     OR i.type = 'join'
    //                                 )
    //                                 THEN i.inviter_id
    
    //                                 WHEN i.global_type = 'carpool'
    //                                 THEN i.invitee_user_id
    
    //                                 ELSE NULL
    //                             END
    //                         ")
    //                     );
    //                 }
    //             )
    
    //             ->leftJoin(
    //                 'customer_register as ur',
    //                 function ($join) use ($userId) {
    
    //                     $join->on(
    //                         'ur.id',
    //                         '=',
    //                         DB::raw("
    //                             CASE
    //                                 WHEN i.global_type = 'dr_carpool'
    //                                 AND (
    //                                     i.inviter_id = ".(int)$userId."
    //                                     OR i.type = 'join'
    //                                 )
    //                                 THEN i.inviter_id
    
    //                                 WHEN i.global_type = 'dr_carpool'
    //                                 THEN i.invitee_user_id
    
    //                                 ELSE NULL
    //                             END
    //                         ")
    //                     );
    //                 }
    //             )
    
    //             ->whereIn('i.id', $latestInviteIds)
                
    //             ->select(
    //                 'i.job_id',
    //                 'i.status',
                    
    //                 DB::raw(
    //                     'COALESCE(cr.id, ur.id) as id'
    //                 ),
                    
    //                 DB::raw(
    //                     'COALESCE(cr.name, ur.name) as name'
    //                 ),
                    
    //                 DB::raw(
    //                     'COALESCE(
    //                         cr.profile_img_url,
    //                         ur.profile_img_url
    //                     ) as profile_img_url'
    //                 ),
    
    //                 DB::raw(
    //                     'COALESCE(
    //                         cr.fcm_token,
    //                         ur.fcm_token
    //                     ) as fcm_token'
    //                 )
    //             )
    
    //             ->get()
    
    //             ->groupBy('job_id');
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Final Response
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $result = [];
    
    //         $deep = env('DEEPLINK_CUSTOMER');
    
    //         foreach ($jobs as $job) {
    
    //             $bookedSeats =
    //                 $participants[$job->id]
    //                     ->booked_seats ?? 0;
    
    //             $availableSeats =
    //                 $job->total_seats - $bookedSeats;
    
    //             if ($availableSeats < $request->seat) {
    //                 continue;
    //             }
    
    //             $passengers =
    //                 $passengerList[$job->id]
    //                     ?? collect();
    
    //             $encryptedId =
    //                 $this->encryptJobId($job->id);
    
    //             if (
    //                 $passengers->contains(
    //                     function ($passenger) use ($userId) {
    
    //                         return
    //                             $passenger->id === $userId &&
    //                             in_array(
    //                                 $passenger->status,
    //                                 ['accepted', 'pending']
    //                             );
    //                     }
    //                 )
    //             ) {
    //                 continue;
    //             }
                
    //             $isVal = false;
    //             $perKms = 5;
                
    //             if(($request->from_place_id != $job->from_place_id) || ($request->to_place_id != $job->to_place_id)){
    //                 $isVal = true;
    //             }
                
    //             if ($job->global_type == 'dr_carpool') {
                
    //                 $perKm = DB::table('user_register')
    //                     ->where('id', $job->user_id)
    //                     ->value('per_km');
                
    //                 $perKm = $perKm == 0
    //                     ? $perKms
    //                     : $perKm;
                
    //                 if ($dis != 0 && $isVal) {
                
    //                     $pickupOffset =
    //                         2 * $this->getNearestRouteDistanceKm(
    //                             $job->route_id,
    //                             $fromLat,
    //                             $fromLng
    //                         );
                
    //                     $dropOffset =
    //                         2 * $this->getNearestRouteDistanceKm(
    //                             $job->route_id,
    //                             $toLat,
    //                             $toLng
    //                         );
                            
                            
    //                     // \Log::error('Fetch Job Pickup' . $job->job_no, [
    //                     //     'error' => $pickupOffset
    //                     // ]);
    //                     // \Log::error('Fetch Job Drop' . $job->job_no, [
    //                     //     'error' => $dropOffset
    //                     // ]);
                
    //                     $billableDistance =
    //                         round($dis +
    //                         $pickupOffset +
    //                         $dropOffset);
                
    //                     // \Log::error('Fetch Job Actual Distance' . $job->job_no, [
    //                     //     'error' => $billableDistance . ' - ' .$dis
    //                     // ]);
                        
    //                     $fare =
    //                         $billableDistance *
    //                         $perKm;
                
    //                     // \Log::error('Fetch Job Actual Fare' . $job->job_no, [
    //                     //     'error' => $fare
    //                     // ]);
                        
    //                     $com = round($fare * 0.1);
                
    //                     // $tax = ($fare + $com) * 0.05;
    //                     $tax = 0;
                
    //                     $job->fare = round(
    //                         $fare + $com + $tax
    //                     );
                
    //                     $job->billable_distance =
    //                         round(
    //                             $billableDistance
    //                         );
                            
                            
    //                     // \Log::error('Fetch Job' . $job->job_no, [
    //                     //     'error' => $job->billable_distance
    //                     // ]);
                
    //                 } else {
                
    //                     $job->fare =
    //                         $job->total_fare;
    //                 }
    //             }
                
    
    //             $result[] = [
    
    //                 'job_id' => $job->id,
    
    //                 'global_type' => $job->global_type,
    
    //                 'job_no' => $job->job_no,
    
    //                 'user_id' => $job->user_id,
    
    //                 'from_place' => $job->from_place,
                    
    //                 'from_place_pass' => $request->from_place,
    
    //                 'to_place' => $job->to_place,
                    
    //                 'to_place_pass' => $request->to_place,
    
    //                 'pickup_date' => $job->pickup_date,
    
    //                 'is_requested' => in_array(
    //                     $job->id,
    //                     $pendingInvites
    //                 ),
    
    //                 'fare' => $job->fare,
    
    //                 'total_seats' => $job->total_seats,
    
    //                 'available_seats' => $availableSeats,
    
    //                 'isLock' => $job->isLock == 0
    //                     ? 'Unlocked'
    //                     : 'Locked',
    
    //                 'pickup_distance_km' =>
    //                     (int) round(
    //                         $job->pickup_distance ?? 0
    //                     ),
    
    //                 'drop_distance_km' =>
    //                     (int) round(
    //                         $job->billable_distance ?? 0
    //                     ),
    
    //                 'choosed_vehicle' => $job->global_type = 'dr_carpool' ? 'Car' : $job->choosed_vehicle,
    
    //                 'name' => $job->name,
    
    //                 'pro_image' =>
    //                     $job->profile_img_url,
    
    //                 'fcm_token' =>
    //                     $job->fcm_token,
    
    //                 'passenger_count' =>
    //                     $passengers->count(),
    
    //                 'passengers' => $passengers
    
    //                     ->map(function ($p) {
    
    //                         return [
    
    //                             'id' => $p->id,
    
    //                             'name' => $p->name,
    
    //                             'profile_img_url' =>
    //                                 $p->profile_img_url,
    
    //                             'fcm_token' =>
    //                                 $p->fcm_token
    //                         ];
    //                     })
    
    //                     ->values(),
    
    //                 /*
    //                 |--------------------------------------------------------------------------
    //                 | Optional Route Data
    //                 |--------------------------------------------------------------------------
    //                 */
    
    //                 'route_summary' =>
    //                     $job->summary,
    
    //                 'route_polyline' =>
    //                     $job->polyline,
    
    //                 'route_distance_km' =>
    //                     $job->distance_meters
    //                         ? round(
    //                             $job->distance_meters / 1000,
    //                             2
    //                         )
    //                         : 0,
    
    //                 'route_duration_minutes' =>
    //                     $job->duration_seconds
    //                         ? round(
    //                             $job->duration_seconds / 60
    //                         )
    //                         : 0,
    
    //                 'deepLink' =>
    //                     $deep.'/carpool?jid='.$encryptedId
    //             ];
    //         }
    
    //         return ApiResponse::success(
    //             'Rides fetched',
    //             array_values($result)
    //         );
    
    //     } catch (ValidationException $e) {
    
    //         return ApiResponse::error(
    //             'Validation failed.',
    //             $e->errors(),
    //             422
    //         );
    
    //     } catch (\Throwable $e) {
    
    //         return ApiResponse::error(
    //             $e->getMessage()
    //         );
    //     }
    // }
    
    private function haversineDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
    
        $earthRadius = 6371;
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
    
        $a =
            sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLng / 2) *
            sin($dLng / 2);
    
        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );
    
        return $earthRadius * $c;
    }
    
    private function getMatchedStopsByTime(
        array $stops,
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng,
        Carbon\Carbon $searchTime,
        int $timeWindow = 20
    ): ?array
    {
        $pickupCandidates = [];
        $dropCandidates = [];
    
        foreach ($stops as $stop) {
    
            $pickupDistance = $this->haversineDistance(
                $fromLat,
                $fromLng,
                $stop['latitude'],
                $stop['longitude']
            );
    
            if ($pickupDistance <= 5) {
    
                $stop['distance'] = $pickupDistance;
    
                $pickupCandidates[] = $stop;
            }
    
            $dropDistance = $this->haversineDistance(
                $toLat,
                $toLng,
                $stop['latitude'],
                $stop['longitude']
            );
    
            if ($dropDistance <= 5) {
    
                $stop['distance'] = $dropDistance;
    
                $dropCandidates[] = $stop;
            }
        }
    
        if (
            empty($pickupCandidates) ||
            empty($dropCandidates)
        ) {
            return null;
        }
    
        usort($pickupCandidates, fn($a, $b) =>
            $a['distance'] <=> $b['distance']
        );
    
        usort($dropCandidates, fn($a, $b) =>
            $a['distance'] <=> $b['distance']
        );
    
        foreach ($pickupCandidates as $pickup) {
    
            foreach ($dropCandidates as $drop) {
    
                if (
                    $pickup['stop_order'] >=
                    $drop['stop_order']
                ) {
                    continue;
                }
    
                $estimatedPickup = Carbon\Carbon::parse(
                    $pickup['estimated_time']
                );
    
                $difference = abs(
                    $estimatedPickup->diffInMinutes(
                        $searchTime,
                        false
                    )
                );
    
                if ($difference > $timeWindow) {
                    continue;
                }
    
                return [
    
                    'pickup_stop' => $pickup,
    
                    'drop_stop' => $drop,
    
                    'pickup_time' => $estimatedPickup,
    
                    'difference' => $difference
                ];
            }
        }
    
        return null;
    }
    
    public function carPoolSearch(Request $request)
    {
        try {
    
            $request->validate([
                'from_place_id' => ['required', 'string'],
                'to_place_id'   => ['required', 'string'],
                'pickup_date'   => ['required', 'date_format:Y-m-d H:i:s'],
                'seat'          => ['required', 'numeric', 'min:1']
            ]);
    
            $userId = auth()->id();
    
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
      
            }
    
            $pickupDate = Carbon::parse(
                $request->pickup_date
            )->toDateString();
            
            $radius = 5;
    
            $matchedDriverJobIds = [];
    
            $pickupStops = DB::table('journey_stops')
                ->select(
                    'journey_stops.*',
                    DB::raw("
                        (
                            6371 * acos(
                                cos(radians(".$fromLat.")) *
                                cos(radians(latitude)) *
                                cos(
                                    radians(longitude) -
                                    radians(".$fromLng.")
                                ) +
                                sin(radians(".$fromLat.")) *
                                sin(radians(latitude))
                            )
                        ) AS distance
                    ")
                )
                ->having('distance', '<=', $radius)
                ->get();
    
            $dropStops = DB::table('journey_stops')
                ->select(
                    'journey_stops.*',
                    DB::raw("
                        (
                            6371 * acos(
                                cos(radians(".$toLat.")) *
                                cos(radians(latitude)) *
                                cos(
                                    radians(longitude) -
                                    radians(".$toLng.")
                                ) +
                                sin(radians(".$toLat.")) *
                                sin(radians(latitude))
                            )
                        ) AS distance
                    ")
                )
                ->having('distance', '<=', $radius)
    
                ->get();
    
            if (
                $pickupStops->isNotEmpty() &&
                $dropStops->isNotEmpty()
            ) {
    
                $pickupGrouped = $pickupStops
                    ->groupBy('job_id');
    
                $dropGrouped = $dropStops
                    ->groupBy('job_id');
    
                foreach ($pickupGrouped as $jobId => $pickupItems) {
    
                    if (!isset($dropGrouped[$jobId])) {
                        continue;
                    }
    
                    $pickupOrder = $pickupItems
                        ->min('stop_order');
    
                    $dropOrder = $dropGrouped[$jobId]
                        ->max('stop_order');
    
                    if ($pickupOrder < $dropOrder) {
    
                        $matchedDriverJobIds[] = $jobId;
                    }
                }
            }
            
    
            if (empty($matchedDriverJobIds)) {
    
                /*
                |--------------------------------------------------------------------------
                | Generate Passenger Geohashes
                |--------------------------------------------------------------------------
                */
                
                // dd('hi');
                
                // Precision
                // 6	~1.2 KM
                // 5	~4.9 KM
                // 4	~39 KM
    
                $pickupHash = substr(
                    $this->getGeoHashPrefix(
                        $fromLat,
                        $fromLng,
                        4
                    ),
                    0,
                    4
                );
                
                $dropHash = substr(
                    $this->getGeoHashPrefix(
                        $toLat,
                        $toLng,
                        4
                    ),
                    0,
                    4
                );
    
                $pickupRouteIds = DB::table('route_points')
    
                    ->where(
                        'geohash',
                        'LIKE',
                        $pickupHash.'%'
                    )
    
                    ->pluck('route_id')
    
                    ->unique()
    
                    ->toArray();
    
                $dropRouteIds = DB::table('route_points')
    
                    ->where(
                        'geohash',
                        'LIKE',
                        $dropHash.'%'
                    )
    
                    ->pluck('route_id')
    
                    ->unique()
    
                    ->toArray();
    
                $matchedRouteIds = array_values(
                    array_intersect(
                        $pickupRouteIds,
                        $dropRouteIds
                    )
                );
                
                $matchedDriverJobIds = [];
                
                foreach ($matchedRouteIds as $routeId) {
                
                    $pickupPoint = DB::table('route_points')
                
                        ->where('route_id', $routeId)
                
                        ->where(
                            'geohash',
                            'LIKE',
                            $pickupHash.'%'
                        )
                
                        ->orderBy('point_order')
                
                        ->first();
                
                    $dropPoint = DB::table('route_points')
                
                        ->where('route_id', $routeId)
                
                        ->where(
                            'geohash',
                            'LIKE',
                            $dropHash.'%'
                        )
                
                        ->orderBy('point_order')
                
                        ->first();
                
                    if (
                        !$pickupPoint ||
                        !$dropPoint
                    ) {
                        continue;
                    }
                
                    if (
                        $pickupPoint->point_order >=
                        $dropPoint->point_order
                    ) {
                        continue;
                    }
                
                    $jobIds = DB::table('cus_job_temp')
                        
                        ->whereNotIn('job_status', [
                            'completed',
                            'cancelled'
                        ])
                        
                        ->where(
                            'route_id',
                            $routeId
                        )
                
                        ->pluck('id')
                
                        ->toArray();
                
                    $matchedDriverJobIds = array_merge(
                        $matchedDriverJobIds,
                        $jobIds
                    );
                }
                
                $matchedDriverJobIds = array_unique(
                    $matchedDriverJobIds
                );
                
            }
            
            
            $jobs = DB::table('cus_job_temp as j')
        
                ->leftJoin('customer_register as cr', function ($join) {
                    $join->on('cr.id', '=', 'j.user_id')
                         ->where('j.global_type', '=', 'carpool');
                })
            
                ->leftJoin('user_register as ur', function ($join) {
                    $join->on('ur.id', '=', 'j.user_id')
                         ->where('j.global_type', '!=', 'carpool');
                })
                
                ->leftJoin('kyc_carpool as kc', function ($join) {
                    $join->on('kc.user_id', '=', 'cr.id')
                         ->where('j.global_type', '=', 'carpool');
                })
            
                ->leftJoin('kyc_details as kd', function ($join) {
                    $join->on('kd.user_id', '=', 'ur.id')
                         ->where('j.global_type', '!=', 'carpool');
                })
    
                ->leftJoin(
                    'route_options as ro',
                    'ro.id',
                    '=',
                    'j.route_id'
                )
    
                ->selectRaw("
                    j.id,
                    j.global_type,
                    j.job_no,
                    j.from_place,
                    j.from_place_id,
                    j.to_place,
                    j.to_place_id,
                    j.isLock,
                    j.pickup_date,
                    j.pass_count as total_seats,
                    j.fare,
                    j.route_id,
                    j.stops_json,
                    
                    CASE 
                        WHEN j.global_type = 'carpool' THEN CAST(j.fare AS CHAR)
                        ELSE IFNULL(j.fare_breakdown->>'$.total_fare', '0') 
                    END as total_fare,
                    
                    j.user_id,
    
                    ro.summary,
                    ro.polyline,
                    ro.distance_meters,
                    ro.duration_seconds,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.name
                        ELSE ur.name
                    END as name,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN COALESCE(cr.profile_img_url, kc.selfie_url)
                        ELSE COALESCE(ur.profile_img_url, kd.selfie_url)
                    END AS profile_img_url,
    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.fcm_token
                        ELSE ur.fcm_token
                    END as fcm_token,
                    
                    CASE
                        WHEN j.global_type = 'carpool'
                        THEN cr.vehicle_details
                        ELSE ur.vehicle_details
                    END as vehicle_details,
    
                    IFNULL(
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                cr.vehicle_details,
                                '$.choosed_vehicle'
                            )
                        ),
                        ''
                    ) as choosed_vehicle,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lat'
                                        )
                                    )
                                )
                            ) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lng'
                                        )
                                    )
                                ) - radians(?)
                            ) +
                            sin(radians(?)) *
                            sin(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.from_lat'
                                        )
                                    )
                                )
                            )
                        )
                    ) AS pickup_distance,
    
                    (
                        6371 * acos(
                            cos(radians(?)) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lat'
                                        )
                                    )
                                )
                            ) *
                            cos(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lng'
                                        )
                                    )
                                ) - radians(?)
                            ) +
                            sin(radians(?)) *
                            sin(
                                radians(
                                    JSON_UNQUOTE(
                                        JSON_EXTRACT(
                                            j.from_to_co,
                                            '$.to_lat'
                                        )
                                    )
                                )
                            )
                        )
                    ) AS drop_distance
                ", [
                    $fromLat,
                    $fromLng,
                    $fromLat,
    
                    $toLat,
                    $toLng,
                    $toLat
                ])
    
                ->where(function ($query) use (
                    $matchedDriverJobIds,
                    $radius
                ) {
    
                    $query->whereIn(
                        'j.id',
                        $matchedDriverJobIds
                    );
                })
    
                ->whereIn(
                    'j.global_type',
                    ['carpool', 'dr_carpool']
                )
    
                ->where('j.isLock', 0)
    
                ->where('j.confirm_status', 1)
    
                ->whereNotIn(
                    'j.job_status',
                    ['completed', 'cancelled']
                )
    
                ->where('j.user_id', '!=', $userId)
    
                ->where('j.user_id', '!=', 0)
    
                ->whereDate('j.pickup_date', $pickupDate)
                ->whereTime('j.pickup_date', '>', now()->toTimeString())
                
                ->orderByRaw(
                    '(pickup_distance + drop_distance) ASC'
                )
                
                ->limit(50)
    
                ->get();
                
                $searchTime = Carbon::parse($request->pickup_date);
                
                // \Log::info('Search Job Time', [
                //     'searchTime' => $searchTime
                // ]);
                
                $timeWindow = 90; // minutes
                
                // $jobs = $jobs->filter(function ($job) use (
                //     $searchTime,
                //     $timeWindow,
                //     $fromLat,
                //     $fromLng,
                //     $toLat,
                //     $toLng
                // ) {
                
                //     if (empty($job->stops_json)) {
                //         return false;
                //     }
                
                //     $stops = json_decode($job->stops_json, true);
                
                //     if (empty($stops)) {
                //         return false;
                //     }
                
                //     $pickupCandidates = [];
                //     $dropCandidates = [];
                
                //     /*
                //     |--------------------------------------------------------------------------
                //     | Collect Nearby Stops
                //     |--------------------------------------------------------------------------
                //     */
                
                //     foreach ($stops as $stop) {
                
                //         $pickupDistance = $this->haversineDistance(
                //             $fromLat,
                //             $fromLng,
                //             $stop['latitude'],
                //             $stop['longitude']
                //         );
                
                //         if ($pickupDistance <= 30) {
                
                //             $stop['distance'] = $pickupDistance;
                
                //             $pickupCandidates[] = $stop;
                //         }
                
                //         $dropDistance = $this->haversineDistance(
                //             $toLat,
                //             $toLng,
                //             $stop['latitude'],
                //             $stop['longitude']
                //         );
                
                //         if ($dropDistance <= 30) {
                
                //             $stop['distance'] = $dropDistance;
                
                //             $dropCandidates[] = $stop;
                //         }
                //     }
                    
                //     \Log::info('Search Job stop', [
                //         'stop1' => $pickupCandidates,
                //         'stop2' => $dropCandidates
                //     ]);
                
                //     if (
                //         empty($pickupCandidates) ||
                //         empty($dropCandidates)
                //     ) {
                //         return false;
                //     }
                
                //     /*
                //     |--------------------------------------------------------------------------
                //     | Sort by nearest stop
                //     |--------------------------------------------------------------------------
                //     */
                
                //     usort($pickupCandidates, function ($a, $b) {
                
                //         return $a['distance'] <=> $b['distance'];
                
                //     });
                
                //     usort($dropCandidates, function ($a, $b) {
                
                //         return $a['distance'] <=> $b['distance'];
                
                //     });
                
                //     $matchedPickup = null;
                //     $matchedDrop = null;
                
                //     /*
                //     |--------------------------------------------------------------------------
                //     | Direction Validation
                //     |--------------------------------------------------------------------------
                //     */
                
                //     foreach ($pickupCandidates as $pickup) {
                
                //         foreach ($dropCandidates as $drop) {
                
                //             if (
                //                 $pickup['stop_order'] <
                //                 $drop['stop_order']
                //             ) {
                
                //                 $matchedPickup = $pickup;
                
                //                 $matchedDrop = $drop;
                
                //                 break 2;
                //             }
                //         }
                //     }
                    
                //     \Log::info('Search Job stop match', [
                //         'mstop1' => $matchedPickup,
                //         'mstop2' => $matchedPickup
                //     ]);
                
                //     if (
                //         !$matchedPickup ||
                //         !$matchedDrop
                //     ) {
                //         return false;
                //     }
                
                //     /*
                //     |--------------------------------------------------------------------------
                //     | Pickup Time Validation
                //     |--------------------------------------------------------------------------
                //     */
                
                //     $estimatedPickup = Carbon::parse(
                //         $matchedPickup['estimated_time']
                //     );
                
                //     $difference = abs(
                //         $estimatedPickup->diffInMinutes(
                //             $searchTime,
                //             false
                //         )
                //     );
                
                //     if ($difference > $timeWindow) {
                //         return false;
                //     }
                
                //     /*
                //     |--------------------------------------------------------------------------
                //     | Store Temporary Values
                //     |--------------------------------------------------------------------------
                //     */
                
                //     $job->matched_pickup_stop =
                //         $matchedPickup['stop_name'];
                
                //     $job->matched_drop_stop =
                //         $matchedDrop['stop_name'];
                
                //     $job->matched_pickup_time =
                //         $estimatedPickup->format(
                //             'Y-m-d H:i:s'
                //         );
                
                //     $job->pickup_time_difference =
                //         $difference;
                        
                //     \Log::info('Search Job', [
                //         'job' => $job
                //     ]);
                
                //     return true;
                
                // })->values();
    
            if ($jobs->isEmpty()) {
    
                return ApiResponse::success(
                    'No rides found',
                    []
                );
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
                ->groupBy(DB::raw("
                    CASE
                        WHEN type = 'join'
                        THEN inviter_id
                        ELSE invitee_user_id
                    END
                "))
                ->pluck('id');
    
            $participants = DB::table('invitations')
                ->whereIn('id', $latestInviteIds)
                ->where('status', 'accepted')
                ->select(
                    'job_id',
                    DB::raw('COUNT(*) as booked_seats')
                )
                ->groupBy('job_id')
                ->get()
                ->keyBy('job_id');
    
            $passengerList = DB::table('invitations as i')
    
                ->leftJoin(
                    'customer_register as cr',
                    function ($join) use ($userId) {
    
                        $join->on(
                            'cr.id',
                            '=',
                            DB::raw("
                                CASE
                                    WHEN i.global_type = 'carpool'
                                    AND (
                                        i.inviter_id = ".(int)$userId."
                                        OR i.type = 'join'
                                    )
                                    THEN i.inviter_id
    
                                    WHEN i.global_type = 'carpool'
                                    THEN i.invitee_user_id
    
                                    ELSE NULL
                                END
                            ")
                        );
                    }
                )
    
                ->leftJoin(
                    'customer_register as ur',
                    function ($join) use ($userId) {
    
                        $join->on(
                            'ur.id',
                            '=',
                            DB::raw("
                                CASE
                                    WHEN i.global_type = 'dr_carpool'
                                    AND (
                                        i.inviter_id = ".(int)$userId."
                                        OR i.type = 'join'
                                    )
                                    THEN i.inviter_id
    
                                    WHEN i.global_type = 'dr_carpool'
                                    THEN i.invitee_user_id
    
                                    ELSE NULL
                                END
                            ")
                        );
                    }
                )
    
                ->whereIn('i.id', $latestInviteIds)
                
                ->select(
                    'i.job_id',
                    'i.status',
                    
                    DB::raw(
                        'COALESCE(cr.id, ur.id) as id'
                    ),
                    
                    DB::raw(
                        'COALESCE(cr.name, ur.name) as name'
                    ),
                    
                    DB::raw(
                        'COALESCE(
                            cr.profile_img_url,
                            ur.profile_img_url
                        ) as profile_img_url'
                    ),
    
                    DB::raw(
                        'COALESCE(
                            cr.fcm_token,
                            ur.fcm_token
                        ) as fcm_token'
                    )
                )
    
                ->get()
    
                ->groupBy('job_id');
    
            $result = [];
    
            $deep = env('DEEPLINK_CUSTOMER');
            
            $get_set = DB::table('booking_settings')->where('status', 0)->first();
    
            foreach ($jobs as $job) {
    
                $bookedSeats =
                    $participants[$job->id]
                        ->booked_seats ?? 0;
    
                $availableSeats =
                    $job->total_seats - $bookedSeats;
    
                if ($availableSeats < $request->seat) {
                    continue;
                }
    
                $passengers =
                    $passengerList[$job->id]
                        ?? collect();
    
                $encryptedId =
                    $this->encryptJobId($job->id);
    
                if (
                    $passengers->contains(
                        function ($passenger) use ($userId) {
    
                            return
                                $passenger->id === $userId &&
                                in_array(
                                    $passenger->status,
                                    ['accepted', 'pending']
                                );
                        }
                    )
                ) {
                    continue;
                }
                
                $isVal = false;
                $perKms = 5;
                
                if(($request->from_place_id != $job->from_place_id) || ($request->to_place_id != $job->to_place_id)){
                    $isVal = true;
                }
                
                if ($job->global_type) {
                    
                    if($job->global_type == 'dr_carpool'){
                        $perKm = DB::table('user_register')
                            ->where('id', $job->user_id)
                            ->value('cp_per_km');
                    }else{
                        $perKm = $job->choosed_vehicle == 'CAR' ? $get_set->carpool_car_price_km : $get_set->carpool_bike_price_km;
                    }
                    // dd('hi');
                    
                
                    $perKm = $perKm == 0
                        ? $perKms
                        : $perKm;
                
                    if ($dis != 0 && $isVal) {
                
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
                        
                
                        $billableDistance =
                            round($dis +
                            $pickupOffset +
                            $dropOffset);
                
                        $fare =
                            $billableDistance *
                            $perKm;
                        if($job->global_type == 'dr_carpool'){
                            $com = round($fare * 0.1);
                        }else{
                            $com = 0;
                        }
                
                        // $tax = ($fare + $com) * 0.05;
                        $tax = 0;
                
                        $job->fare = round(
                            $fare + $com + $tax
                        );
                
                        $job->billable_distance =
                            round(
                                $billableDistance
                            );
                
                    } else {
                
                        $job->fare =
                            $job->total_fare;
                    }
                }
    
                $result[] = [
    
                    'job_id' => $job->id,
    
                    'global_type' => $job->global_type,
    
                    'job_no' => $job->job_no,
    
                    'user_id' => $job->user_id,
    
                    'from_place' => $job->from_place,
                    
                    'from_place_pass' => $request->from_place,
    
                    'to_place' => $job->to_place,
                    
                    'to_place_pass' => $request->to_place,
    
                    'pickup_date' => $job->pickup_date,
                    
                    'matched_pickup_stop' =>
                        $job->matched_pickup_stop ?? null,
                    
                    'matched_drop_stop' =>
                        $job->matched_drop_stop ?? null,
                    
                    'matched_pickup_time' =>
                        $job->matched_pickup_time ?? null,
                    
                    'pickup_time_difference' =>
                        $job->pickup_time_difference ?? null,
    
                    'is_requested' => in_array(
                        $job->id,
                        $pendingInvites
                    ),
    
                    'fare' => $job->fare,
                    
                    'vehicle_details' => $job->vehicle_details,
    
                    'total_seats' => $job->total_seats,
    
                    'available_seats' => $availableSeats,
    
                    'isLock' => $job->isLock == 0
                        ? 'Unlocked'
                        : 'Locked',
    
                    'pickup_distance_km' =>
                        (int) round(
                            $job->pickup_distance ?? 0
                        ),
    
                    'drop_distance_km' =>
                        (int) round(
                            $job->billable_distance ?? 0
                        ),
    
                    'choosed_vehicle' => $job->global_type = 'dr_carpool' ? 'Car' : $job->choosed_vehicle,
    
                    'name' => $job->name,
    
                    'pro_image' =>
                        $job->profile_img_url,
    
                    'fcm_token' =>
                        $job->fcm_token,
    
                    'passenger_count' =>
                        $passengers->count(),
    
                    'passengers' => $passengers
    
                        ->map(function ($p) {
    
                            return [
    
                                'id' => $p->id,
    
                                'name' => $p->name,
    
                                'profile_img_url' =>
                                    $p->profile_img_url,
    
                                'fcm_token' =>
                                    $p->fcm_token
                            ];
                        })
    
                        ->values(),
    
                    /*
                    |--------------------------------------------------------------------------
                    | Optional Route Data
                    |--------------------------------------------------------------------------
                    */
    
                    'route_summary' =>
                        $job->summary,
    
                    'route_polyline' =>
                        $job->polyline,
    
                    'route_distance_km' =>
                        $job->distance_meters
                            ? round(
                                $job->distance_meters / 1000,
                                2
                            )
                            : 0,
    
                    'route_duration_minutes' =>
                        $job->duration_seconds
                            ? round(
                                $job->duration_seconds / 60
                            )
                            : 0,
    
                    'deepLink' =>
                        $deep.'/carpool?jid='.$encryptedId
                ];
            }
    
            return ApiResponse::success(
                'Rides fetched',
                array_values($result)
            );
    
        } catch (ValidationException $e) {
    
            return ApiResponse::error(
                'Validation failed.',
                $e->errors(),
                422
            );
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error(
                $e->getMessage()
            );
        }
    }
    
    public function homeJobs(Request $request)
    {
        try {
            
            $userId = auth()->id();
    
            $startDate = now()->toDateString();
            $endDate   = now()->addDays(2)->toDateString();
            
            // dd(auth()->user());
    
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
                ->whereIn('otpVerify', [0, 1])
                ->pluck('job_id');
            
            $latestInviteIds = DB::table('invitations')
                ->whereIn('job_id', $jobIds)
                ->select(DB::raw('MAX(id) as id'))
                ->whereIn('otpVerify', [0, 1])
                ->groupBy('job_id')
                ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                ->pluck('id');
                
            $passengerList = DB::table('invitations as i')
               
                ->leftJoin('customer_register as ur', function ($join) use ($userId) {
                    $join->on('ur.id', '=', DB::raw("CASE 
                                WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                                ELSE i.invitee_user_id 
                            END"))
                         ->where('i.global_type', '=', 'dr_carpool');
                })
              
                ->leftJoin('customer_register as cr', function ($join) use ($userId) {
                    $join->on('cr.id', '=', DB::raw("CASE 
                                WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                                ELSE i.invitee_user_id 
                            END"))
                         ->where('i.global_type', '!=', 'dr_carpool');
                })
                ->leftJoin('kyc_details as kd', function ($join) {
                    $join->on('kd.user_id', '=', 'ur.id');
                })
                
                ->leftJoin('kyc_carpool as kc', function ($join) {
                    $join->on('kc.user_id', '=', 'cr.id');
                })
                ->whereIn('i.id', $latestInviteIds)
                ->whereIn('i.otpVerify', [0, 1])
                ->select([
                    'i.job_id',
                    'i.type',
                    'i.status',
                    'i.invite_token',
                    'i.from_place',
                    'i.otpVerify',
                    'i.to_place',
                    'i.otp',
                    'i.collectAmt',
                   
                    DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.id ELSE cr.id END as id"),
                    DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.name ELSE cr.name END as name"),
                    // DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.profile_img_url ELSE cr.profile_img_url END as profile_img_url"),
                    DB::raw("
                        CASE
                            WHEN i.global_type = 'dr_carpool'
                            THEN COALESCE(ur.profile_img_url, kd.selfie_url)
                            ELSE COALESCE(cr.profile_img_url, kc.selfie_url)
                        END AS profile_img_url
                    "),
                    DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.fcm_token ELSE cr.fcm_token END as fcm_token"),
                ])
                ->get()
                ->groupBy('job_id');
                
            $joinedJobsQuery = DB::table('cus_job_temp as job')
                ->leftJoin('user_register as ur_host', function ($join) {
                    $join->on('ur_host.id', '=', 'job.user_id')
                         ->where('job.global_type', '=', 'dr_carpool');
                })
                ->leftJoin('customer_register as cr_host', function ($join) {
                    $join->on('cr_host.id', '=', 'job.user_id')
                         ->where('job.global_type', '!=', 'dr_carpool');
                })
                ->leftJoin('kyc_details as kd', function ($join) {
                    $join->on('kd.user_id', '=', 'ur_host.id');
                })
                
                ->leftJoin('kyc_carpool as kc', function ($join) {
                    $join->on('kc.user_id', '=', 'cr_host.id');
                })
                ->whereIn('job.id', $jobIds)
                ->whereIn('job.global_type', ['carpool', 'dr_carpool'])
                ->whereDate('job.pickup_date', '>=', now()->toDateString())
                ->whereNotIn('job.job_status', ['cancelled', 'completed'])
                ->where('job.deletes', '0');
            
            $joinedJobs = $joinedJobsQuery
                ->select([
                    'job.id',
                    'job.global_type',
                    'job.job_no',
                    'job.user_id',
                    'job.from_place',
                    'job.to_place',
                    'job.job_status',
                    'job.from_to_co',
                    'job.fare',
                    'job.otp',
                    'job.isLock',
                    'job.pickup_date',
                    'job.pass_count as total_seats',
                    'job.filled_seat',
                    DB::raw("'join' as job_type"),
                    
                    DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.name ELSE cr_host.name END as name"),
                    
                    // DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.profile_img_url ELSE cr_host.profile_img_url END as profile_img_url"),
                    
                    DB::raw("
                        CASE
                            WHEN job.global_type = 'dr_carpool'
                            THEN COALESCE(ur_host.profile_img_url, kd.selfie_url)
                            ELSE COALESCE(cr_host.profile_img_url, kc.selfie_url)
                        END AS profile_img_url
                    "),
                    
                    DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.fcm_token ELSE cr_host.fcm_token END as fcm_token"),
                    
                    DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.vehicle_details ELSE cr_host.vehicle_details END as vehicle_details"),
                    
                    DB::raw("CASE 
                        WHEN job.global_type = 'dr_carpool' THEN IFNULL(JSON_UNQUOTE(JSON_EXTRACT(ur_host.vehicle_details, '$.choosed_vehicle')), '')
                        ELSE IFNULL(JSON_UNQUOTE(JSON_EXTRACT(cr_host.vehicle_details, '$.choosed_vehicle')), '')
                    END as choosed_vehicle")
                ])
                ->latest('job.id')
                ->get();
                
            $deep = env('DEEPLINK_CUSTOMER');
            
            $jobs = $joinedJobs
                
                // ->filter(function ($job) {
                //     return $job->job_status != 'cancelled';
                // })
                
                ->map(function ($job) use ($passengerList, $deep) {

                $passengers = $passengerList[$job->id] ?? collect();
                
                $encryptedId = $this->encryptJobId($job->id);
                
                $jobType = 'Requested';
                
                $userPassenger = $passengers->first(function ($p) {
                
                    return $p->id == auth()->id();
                
                });
                
                if ($userPassenger) {
                    
                    $job->from_place = $userPassenger->from_place;
                    $job->to_place = $userPassenger->to_place;
                    $job->fare = $userPassenger->collectAmt;
                    $job->otp = $userPassenger->otp;
                
                    if ($userPassenger->status == 'accepted') {
                        if($job->job_status == 'started'){
                            $jobType = 'Started';
                        }else if($job->job_status == 'completed'){
                            
                            $jobType = 'Completed';
                        }else{
                            
                            $jobType = 'Confirmed';
                        }
                
                    } elseif ($userPassenger->status == 'rejected') {
                
                        $jobType = 'Rejected';
                
                    } elseif ($userPassenger->status == 'removed') {
                
                        $jobType = 'Removed';
                
                    } elseif ($userPassenger->status == 'exit') {
                
                        $jobType = 'Removed';
                
                    }
                }else{
                    return [];
                }
    
                return [
                    'id' => $job->id,
                    'job_no' => $job->job_no,
                    'global_type' => $job->global_type,
                    'user_id' => $job->user_id,
                    'name' => $job->name,
                    'pro_image' => $job->profile_img_url,
                    'fcm_token' => $job->fcm_token,
                    'from_place' => $job->from_place,
                    'to_place' => $job->to_place,
                    'fare' => $job->fare,
                    'pickup_date' => $job->pickup_date,
                    'total_seats' => $job->total_seats,
                    'filled_seat' => $job->filled_seat,
                    'vehicle_details' => $job->vehicle_details,
                    'from_to_co' => $job->from_to_co,
                    'otp' => $jobType == 'Confirmed' || $jobType == 'Started' ? $job->otp : null,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                    'choosed_vehicle' => $job->global_type == 'carpool' ? $job->choosed_vehicle : 'CAR',
                    'job_type' => $jobType,
                    // 'job_type' => $job->job_type,
    
                    'passenger_count' => $passengers
                        ->filter(function ($p) {
                            return $p->status == 'accepted';
                        })->count(),
    
                    'passengers' => $passengers
                        ->filter(function ($p) {
                            return $p->status == 'accepted';
                        })
                        ->map(function ($p) {
        
                            return [
                                'id' => $p->id,
                                'name' => $p->name,
                                'profile_img_url' => $p->profile_img_url,
                                'type' => $p->type,
                                'status' => $p->status,
                                'otpVerify' => $p->otpVerify,
                                'invite_token' => $p->invite_token,
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
        
        \Log::error('Cancel Job Failed', [
            'error' => 'hiiii bro start'
        ]);
    
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
            
            AutomationEventService::trigger(
                'carpool_cancel_host',
                $userId
            );
            
            \Log::error('Cancel Job Failed', [
                'error' => 'hiiii bro'
            ]);
    
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
    
    public function scheduleList(Request $request)
    {
        try {
    
            $userId = auth()->id();
            $page = $request->page ?? 1;
            $limit = $request->limit ?? 10;
            $offset = ($page - 1) * $limit;
    
            $query = DB::table('frequency_job as fj')
                ->leftJoin(
                    'frequency_job_logs as fjl',
                    'fjl.frequency_job_id',
                    '=',
                    'fj.id'
                )
                ->where('fj.user_id', $userId)
                ->where('fj.status', 0)
                ->select(
                    'fj.id',
                    'fj.global_type',
                    'fj.frequency_type',
                    'fj.job_data',
                    'fj.status',
                    'fj.created_at',
                    'fj.updated_at',
                    'fj.last_generated_at',
                    'fj.last_generated_temp_job_id',
                    'fj.last_generated_job_no',
                    DB::raw('COUNT(fjl.id) as total_generated_jobs')
                )
                ->groupBy(
                    'fj.id',
                    'fj.global_type',
                    'fj.frequency_type',
                    'fj.job_data',
                    'fj.status',
                    'fj.created_at',
                    'fj.updated_at',
                    'fj.last_generated_at',
                    'fj.last_generated_temp_job_id',
                    'fj.last_generated_job_no'
                );
    
            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where(
                            'fj.frequency_type',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'fj.last_generated_job_no',
                            'LIKE',
                            "%{$search}%"
                        );
                });
            }
    
            if ($request->filled('status')) {
    
                $query->where(
                    'fj.status',
                    $request->status
                );
            }
    
            $total = (clone $query)->count();
    
            $scheduleList = $query
                ->orderBy('fj.id', 'DESC')
                ->offset($offset)
                ->limit($limit)
                ->get();
    
            $scheduleList->transform(function ($item) {
    
                $jobData = json_decode(
                    $item->job_data,
                    true
                );
    
                unset($item->job_data);
    
                if (!empty($jobData)) {
    
                    foreach ($jobData as $key => $value) {
                        if($key != 'id' && $key != 'user_id' && $key != 'created_at' && $key != 'updated_at'){
                            
                            $item->$key = $value;
                        }
                    }
                }
    
                return $item;
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Schedule list fetched successfully',
                'pagination' => [
                    'current_page' => (int) $page,
                    'limit' => (int) $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                ],
                'data' => $scheduleList,
            ]);
    
        } catch (\Exception $e) {
    
            \Log::error('Schedule List API Failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteSchedule(Request $request)
    {
        try {
    
            $request->validate([
                'sch_id' => 'required|integer'
            ]);
    
            $userId = auth()->id();
    
            $schedule = DB::table('frequency_job')
                ->where('id', $request->sch_id)
                ->where('user_id', $userId)
                ->first();
    
            if (!$schedule) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }
    
            DB::beginTransaction();
    
            $futureJobIds = DB::table('frequency_job_logs as fjl')
                ->join(
                    'cus_job_temp as cjt',
                    'cjt.id',
                    '=',
                    'fjl.temp_job_id'
                )
                ->where('fjl.frequency_job_id', $schedule->id)
                ->where('cjt.pickup_date', '>=', now())
                ->pluck('cjt.id');
    
            if ($futureJobIds->isNotEmpty()) {
    
                DB::table('cus_job_temp')
                    ->whereIn('id', $futureJobIds)
                    ->delete();
            }
    
            DB::table('frequency_job')
                ->where('id', $schedule->id)
                ->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Schedule deleted successfully'
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            \Log::error('Delete Schedule Failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
    
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function pastJob(Request $request)
    {
        
        try {
            
            $user = auth()->user();
            $userId = $user->id;
            $deep = env('DEEPLINK_CUSTOMER');
    
            if ($request->type == 'passenger') {
                
                $jobIds = DB::table('invitations')
                    ->where(function ($query) use ($userId) {
                        $query->where('inviter_id', $userId)
                              ->where('type', 'join');
                    })
                    ->orWhere(function ($query) use ($userId) {
                        $query->where('invitee_user_id', $userId)
                              ->where('type', 'job');
                    })
                    // ->where(function ($query) {
                    //     $query->whereIn('status', ['cancelled', 'rejected', 'exit'])
                    //           ->orWhere('otpVerify', 2);
                    // })
                    
                    ->pluck('job_id');
    
                if ($jobIds->isEmpty()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Job history fetched successfully',
                        'data' => ['jobs' => []]
                    ], 200);
                }
    
                $latestInviteIds = DB::table('invitations')
                    ->whereIn('job_id', $jobIds)
                    ->select(DB::raw('MAX(id) as id'))
                    ->groupBy('job_id')
                    ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
                    ->pluck('id');
                    
                $passengerList = DB::table('invitations as i')
                    ->leftJoin('customer_register as ur', function ($join) use ($userId) {
                        $join->on('ur.id', '=', DB::raw("CASE 
                            WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                            ELSE i.invitee_user_id 
                        END"))
                        ->where('i.global_type', '=', 'dr_carpool');
                    })
                    ->leftJoin('customer_register as cr', function ($join) use ($userId) {
                        $join->on('cr.id', '=', DB::raw("CASE 
                            WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
                            ELSE i.invitee_user_id 
                        END"))
                        ->where('i.global_type', '=', 'carpool');
                    })
                    ->where(function ($query) {
                        $query->where('i.otpVerify', 2)
                              ->orWhereIn('i.status', ['cancelled', 'rejected', 'exit', 'pending']);
                    })
                    ->whereIn('i.id', $latestInviteIds)
                    ->select([
                        'i.job_id',
                        'i.type',
                        'i.status',
                        'i.invite_token',
                        'i.from_place',
                        'i.to_place',
                        'i.collectAmt',
                        DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.id ELSE cr.id END as id"),
                        DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.name ELSE cr.name END as name"),
                        DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.profile_img_url ELSE cr.profile_img_url END as profile_img_url"),
                        DB::raw("CASE WHEN i.global_type = 'dr_carpool' THEN ur.fcm_token ELSE cr.fcm_token END as fcm_token"),
                    ])
                    ->get()
                    ->groupBy('job_id');
                    
                $joinedJobs = DB::table('cus_job_temp as job')
                    ->leftJoin('user_register as ur_host', function ($join) {
                        $join->on('ur_host.id', '=', 'job.user_id')
                             ->where('job.global_type', '=', 'dr_carpool');
                    })
                    ->leftJoin('customer_register as cr_host', function ($join) {
                        $join->on('cr_host.id', '=', 'job.user_id')
                             ->where('job.global_type', '!=', 'dr_carpool');
                    })
                    ->whereIn('job.id', $jobIds)
                    ->whereIn('job.global_type', ['carpool', 'dr_carpool'])
                    // ->whereDate('job.pickup_date', '<', now()->toDateString())
                    // ->whereIn('job.job_status', ['cancelled', 'completed'])
                    ->where('job.deletes', '0')
                    ->select([
                        'job.id',
                        'job.global_type',
                        'job.job_no',
                        'job.user_id',
                        'job.from_place',
                        'job.to_place',
                        'job.job_status',
                        'job.fare',
                        'job.isLock',
                        'job.pickup_date',
                        'job.pass_count as total_seats',
                        'job.filled_seat',
                        DB::raw("'join' as job_type"),
                        DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.name ELSE cr_host.name END as name"),
                        DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.profile_img_url ELSE cr_host.profile_img_url END as profile_img_url"),
                        DB::raw("CASE WHEN job.global_type = 'dr_carpool' THEN ur_host.fcm_token ELSE cr_host.fcm_token END as fcm_token"),
                        DB::raw("CASE 
                            WHEN job.global_type = 'dr_carpool' THEN IFNULL(JSON_UNQUOTE(JSON_EXTRACT(ur_host.vehicle_details, '$.choosed_vehicle')), '')
                            ELSE IFNULL(JSON_UNQUOTE(JSON_EXTRACT(cr_host.vehicle_details, '$.choosed_vehicle')), '')
                        END as choosed_vehicle")
                    ])
                    ->latest('job.id')
                    ->get();
                    
                $jobs = $joinedJobs->map(function ($job) use ($passengerList, $deep, $userId) {
                    $passengers = $passengerList[$job->id] ?? collect();
                    $encryptedId = $this->encryptJobId($job->id);
                    $jobType = 'Expiry';
                    
                    $userPassenger = $passengers->first(fn($p) => $p->id == $userId);
                    
                    if ($userPassenger) {
                        $job->from_place = $userPassenger->from_place;
                        $job->to_place = $userPassenger->to_place;
                        $job->fare = $userPassenger->collectAmt;
                    
                        if ($userPassenger->status == 'accepted') {
                            $jobType = 'Confirmed';
                        }else if($job->job_status == 'completed'){
                            $jobType = 'Completed';
                        }else if($job->job_status == 'cancelled'){
                            $jobType = 'Cancelled';
                        } elseif (in_array($userPassenger->status, ['rejected', 'removed', 'exit'])) {
                            $jobType = ($userPassenger->status == 'rejected') ? 'Rejected' : 'Removed';
                        }else{
                            $jobType = 'Expiried';
                        }
                    }
                
                    $acceptedPassengers = $passengers->filter(fn($p) => $p->status == 'accepted');
    
                    return [
                        'id' => $job->id,
                        'job_no' => $job->job_no,
                        'global_type' => $job->global_type,
                        'user_id' => $job->user_id,
                        'name' => $job->name,
                        'profile_img_url' => $job->profile_img_url,
                        'fcm_token' => $job->fcm_token,
                        'from_place' => $job->from_place,
                        'to_place' => $job->to_place,
                        'fare' => $job->fare,
                        'pickup_date' => $job->pickup_date,
                        'total_seats' => $job->total_seats,
                        'filled_seat' => $job->filled_seat,
                        'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                        'choosed_vehicle' => $job->choosed_vehicle,
                        'job_type' => $jobType,
                        'passenger_count' => $acceptedPassengers->count(),
                        'passengers' => $acceptedPassengers->map(fn($p) => [
                            'id' => $p->id,
                            'name' => $p->name,
                            'profile_img_url' => $p->profile_img_url,
                            'type' => $p->type,
                            'status' => $p->status,
                            'invite_token' => $p->invite_token,
                            'fcm_token' => $p->fcm_token
                        ])->values(),
                        'deepLink' => $deep . '/carpool?jid=' . $encryptedId
                    ];
                });
                
                return response()->json([
                    'status' => true,
                    'message' => 'Past jobs fetched successfully.',
                    'data' => $jobs
                ], 200);
                
            } else {
                // "Else" context handling (Driver / Host perspective)
                $jobs = DB::table('cus_job_temp as job')
                    ->leftJoin('route_options as ro', 'ro.id', '=', 'job.route_id')
                    ->leftJoin('customer_register as ur', 'ur.id', '=', 'job.user_id')
                    ->select([
                        'job.*',
                        'ro.summary as route_summary',
                        'ro.distance_meters',
                        'ro.duration_seconds',
                        'ro.toll_amount',
                        'ro.polyline',
                        'ur.name',
                        'ur.profile_img_url',
                        'ur.fcm_token'
                    ])
                    ->where('job.user_id', $userId)
                    ->where('job.global_type', 'carpool')
                    ->where(function ($query) {
                        $query->whereIn('job.job_status', ['cancelled', 'completed'])
                              ->orWhere(function ($q) {
                                  $q->where('job.job_status', 'created')
                                    ->where('job.pickup_date', '<', now());
                              });
                    })
                    ->orderByDesc('job.id')
                    ->get();
    
                if ($jobs->isEmpty()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Past jobs fetched successfully.',
                        'data' => []
                    ], 200);
                }
    
                // Fixed the broken join syntax bug here by replacing structural 'if' statement with DB::raw
                $allPassengers = DB::table('invitations as i')
                    ->leftJoin('customer_register as c', 'c.id', '=', DB::raw("CASE WHEN i.type = 'join' THEN i.inviter_id ELSE i.invitee_user_id END"))
                    ->whereIn('i.job_id', $jobs->pluck('id'))
                    ->select([
                        'i.job_id',
                        'i.status',
                        'i.collectAmt',
                        'c.id',
                        'c.name',
                        'c.profile_img_url',
                        DB::raw('(i.fare_breakdown->>"$.total_fare" - (i.fare_breakdown->>"$.com" + i.fare_breakdown->>"$.tax")) as driver_fare')
                    ])
                    ->get()
                    ->groupBy('job_id');
    
                $result = [];
                foreach ($jobs as $job) {
                    $jobPassengers = $allPassengers->get($job->id, collect());
                    
                    // Get accepted and count pending collections out of memory to completely skip loop-based extra DB queries
                    $accepted = $jobPassengers->filter(fn($p) => $p->status === 'accepted')->values();
                    // $pending = $jobPassengers->filter(fn($p) => $p->status === 'pending')->count(); // kept if needed later
    
                    $encryptedId = $this->encryptJobId($job->id);
        
                    $result[] = [
                        'job_id' => $job->id,
                        'global_type' => $job->global_type,
                        'job_no' => $job->job_no,
                        'user_id' => $job->user_id,
                        'name' => $job->name,
                        'pro_image' => $job->profile_img_url,
                        'fcm_token' => $job->fcm_token,
                        'from_place' => $job->from_place,
                        'to_place' => $job->to_place,
                        'pickup_date' => $job->pickup_date,
                        'seat' => $job->pass_count,
                        'filled_seat' => $job->filled_seat,
                        'fare' => $job->fare,
                        'job_status' => $job->job_status == 'created' ? 'expired' : $job->job_status,
                        'jb_type' => $job->confirm_status ?? null,
                        'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                        'accepted_friends' => $accepted,
                        'deepLink' => $deep . '/carpool?jid=' . $encryptedId,
                        'selected_route' => [
                            'route_id' => $job->route_id,
                            'summary' => $job->route_summary,
                            'distance_km' => $job->distance_meters ? round($job->distance_meters / 1000, 2) : 0,
                            'duration_minutes' => $job->duration_seconds ? round($job->duration_seconds / 60) : 0,
                            'toll_amount' => $job->toll_amount ?? 0,
                            'polyline' => $job->polyline
                        ],
                    ];
                }
        
                return response()->json([
                    'status' => true,
                    'message' => 'Past jobs fetched successfully.',
                    'data' => $result
                ], 200);
            }
    
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Log::error('Past Jobs Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => 'Server error',
            ], 500);
        }
    }
    
    private function getAlternativeRoutes($fromLat, $fromLng, $toLat, $toLng, $f_place_id, $t_place_id)
    {
        try {
          
            $coordinates = "{$fromLng},{$fromLat};{$toLng},{$toLat}";
            
            $response = Http::get("https://gomaps.g-ride.in/route/v1/driving/{$coordinates}", [
                'alternatives' => 'true',
                'overview'     => 'full',
                'geometries'   => 'polyline',
                'steps' => 'true' // Summary is available without full steps
            ]);
    
            $res = $response->json();
    
            if (!isset($res['code']) || $res['code'] !== 'Ok' || empty($res['routes'])) {
                return [];
            }
    
            $routes = [];
    
            foreach ($res['routes'] as $index => $route) {
                
                $distanceMeters = round($route['distance'] ?? 0, 2);
                $durationSeconds = (int) ($route['duration'] ?? 0);
    
                // $roadSummary = $route['summary'] ?? '';
                
                // if (empty($roadSummary) && !empty($route['legs'])) {
                //     $roadSummary = $route['legs'][0]['summary'] ?? '';
                // }
    
                // if (empty($roadSummary)) {
                // }
                // $roadSummary = ($index == 0) ? "Route 1" : "Route " . $index + 1;
                $roadSummary = "Route " . $index + 1;
    
                $tollAmount = 0;
                $tollInfo = null;
    
                $routeData = [
                    'from_place_id'   => $f_place_id,
                    'to_place_id'     => $t_place_id,
                    'from_lat'        => $fromLat,
                    'from_lng'        => $fromLng,
                    'to_lat'          => $toLat,
                    'to_lng'          => $toLng,
                    'route_index'     => $index,
                    'summary'         => $roadSummary, 
                    'distance_meters' => $distanceMeters,
                    'duration_seconds'=> $durationSeconds,
                    'toll_amount'     => $tollAmount,
                    'polyline'        => $route['geometry'] ?? null,
                    'route_labels'    => json_encode([]),
                    'raw_response'    => json_encode($route),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
    
                // Insert into DB
                $routeId = DB::table('route_options')->insertGetId($routeData);
                
                $this->storeRouteStops(
                    $routeId,
                    $route,
                    $fromLat,
                    $fromLng,
                    $toLat,
                    $toLng
                );
    
                $routes[] = [
                    'id'               => $routeId,
                    'route_index'      => $index,
                    'summary'          => $roadSummary,
                    'distance_meters'  => $distanceMeters,
                    'distance_km'      => round($distanceMeters / 1000, 2),
                    'duration_seconds' => $durationSeconds,
                    'duration_text'    => gmdate('H:i:s', $durationSeconds),
                    'toll_amount'      => $tollAmount,
                    'toll_details'     => $tollInfo,
                    'route_labels'     => [],
                    'polyline'         => $routeData['polyline'],
                ];
            }
    
            return $routes;
    
        } catch (\Exception $e) {
            Log::error('OSRM Route Error', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }
    
    private function storeRouteStops(
        int $routeId,
        array $route,
        float $fromLat,
        float $fromLng,
        float $toLat,
        float $toLng
    ): void
    {
        try {
    
            DB::table('route_stops')
                ->where('route_id', $routeId)
                ->delete();
    
            $stops = [];
    
            $order = 1;
    
            /*
            |--------------------------------------------------------------------------
            | Source Stop
            |--------------------------------------------------------------------------
            */
    
            $source = $this->reverseGeocode(
                $fromLat,
                $fromLng
            );
    
            if ($source) {
    
                $stops[] = [
                    'route_id'   => $routeId,
                    'stop_name'  => $source,
                    'latitude'   => $fromLat,
                    'longitude'  => $fromLng,
                    'stop_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Intermediate Stops
            |--------------------------------------------------------------------------
            */
    
            $distanceCovered = 0;
    
            foreach ($route['legs'][0]['steps'] ?? [] as $step) {
    
                $distanceCovered += $step['distance'];
    
                // Every 25 KM
                if ($distanceCovered < 25000) {
                    continue;
                }
    
                $distanceCovered = 0;
    
                $location =
                    $step['maneuver']['location']
                    ?? null;
    
                if (!$location) {
                    continue;
                }
    
                $lng = $location[0];
                $lat = $location[1];
    
                $place = $this->reverseGeocode(
                    $lat,
                    $lng
                );
    
                if (!$place) {
                    continue;
                }
    
                $lastStop = end($stops);
    
                if (
                    $lastStop &&
                    strtolower($lastStop['stop_name'])
                    === strtolower($place)
                ) {
                    continue;
                }
    
                $stops[] = [
                    'route_id'   => $routeId,
                    'stop_name'  => $place,
                    'latitude'   => $lat,
                    'longitude'  => $lng,
                    'stop_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Destination Stop
            |--------------------------------------------------------------------------
            */
    
            $destination = $this->reverseGeocode(
                $toLat,
                $toLng
            );
    
            if (
                $destination &&
                (
                    empty($stops) ||
                    strtolower(end($stops)['stop_name'])
                    !== strtolower($destination)
                )
            ) {
    
                $stops[] = [
                    'route_id'   => $routeId,
                    'stop_name'  => $destination,
                    'latitude'   => $toLat,
                    'longitude'  => $toLng,
                    'stop_order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
    
            if (!empty($stops)) {
    
                DB::table('route_stops')
                    ->insert($stops);
            }
    
        } catch (\Throwable $e) {
    
            Log::error('Store Route Stops Error', [
                'route_id' => $routeId,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function reverseGeocode(
        float $lat,
        float $lng
    ): ?string
    {
        try {
    
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'GoRide'
                ])
                ->get('https://geo.g-ride.in/reverse.php', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'format' => 'jsonv2',
                    'addressdetails' => 1
                ]);
    
            if (!$response->successful()) {
                return null;
            }
    
            $address = $response->json('address', []);
    
            return trim(
                $address['city']
                ?? $address['town']
                ?? $address['municipality']
                ?? $address['village']
                ?? $address['county']
                ?? $address['state_district']
                ?? ''
            ) ?: null;
    
        } catch (\Throwable $e) {
    
            Log::error('Reverse Geocode Error', [
                'lat' => $lat,
                'lng' => $lng,
                'message' => $e->getMessage()
            ]);
    
            return null;
        }
    }
    
    public function fetchRoute(Request $request)
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
    
                $getRoute = DB::table('route_options')
                        ->where([
                            'from_place_id' => $request->from_place_id,
                            'to_place_id'   => $request->to_place_id
                        ])
                        ->orderBy('route_index')
                        ->get();
                    
                $routes = [];
                
                $user_id = auth()->user()->id;
                
                $perKms = 4;
                $perKm = 4;
                
                // $perKm = DB::table('user_register')
                //     ->where('id', $user_id)
                //     ->value('per_km');
                
                // $perKm = $perKm == 0 ? $perKms : $perKm;
                
                $pass = 0;
                
                $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
                
                if (!empty($vehicleDetails['choosed_vehicle'])) {
                    
                    $rcVehicle = $vehicleDetails['choosed_vehicle'];
                    
                    $perKms = $rcVehicle == 'CAR' ? 4 : 2;
                    $perKm = $rcVehicle == 'CAR' ? 4 : 2;
                    // $pass = $rcVehicle['seat_capacity'] ?? null;
                }
                // else {
        
                //     $get_ocr = DB::connection('mysql_log')->table('ocr_request')
                //         ->where('user_id', $user_id)
                //         ->where('doc_type', 'RC')
                //         ->orderByDesc('id')
                //         ->first();
                        
            
                //     if ($get_ocr) {
                        
                //         $pass = $get_ocr->seater ?? null;
                //         if($pass == 'mini' || $pass == 'Mini' || $pass == 'Mini 4'){
                //             $pass = 4;
                //         }
                //         $pass += 1;
                //     }
                // }
                    
                if ($getRoute->count() > 0) {
                    
                        $routes = $getRoute->map(function ($route) use ($perKms, $pass) {
                            
                            $dis =round($route->distance_meters / 1000, 2);
                            
                            $totalFare = 0;
                            
                            if ($dis != 0) {
                                $fare = $dis * $perKms;
                                
                                $com = $fare * 0.1;
                                // $tax = ($fare + $com) * 0.05;
                                $tax = 0;
                                
                                $totalFare = round($fare + $com + $tax);
                            }
                            
                            $pass = 1;
                            
                            $totalFare = (int) ( $totalFare / $pass );
                            
                            return [
                                'id' => $route->id,
                                'route_index' => $route->route_index,
                                'summary' => $route->summary,
                                'distance_meters' => (int) $route->distance_meters,
                                'distance_km' => round(
                                    $route->distance_meters / 1000,
                                    2
                                ),
                                'duration_seconds' => (int) $route->duration_seconds,
                                'duration_text' => gmdate(
                                    'H:i:s',
                                    $route->duration_seconds
                                ),
                                'toll_amount' => (float) $route->toll_amount,
                                'per_swat' => (float) $totalFare,
                                'route_labels' => $route->route_labels
                                    ? json_decode($route->route_labels, true)
                                    : [],
                                'polyline' => $route->polyline,
                            ];
                    
                        })->toArray();
                    
                } else {
                    
                    $routes = $this->getAlternativeRoutes(
                        $fromGeo['lat'],
                        $fromGeo['lng'],
                        $toGeo['lat'],
                        $toGeo['lng'],
                        $request->from_place_id,
                        $request->to_place_id
                    );
                    
                    $passengerCount = (int) ($pass ?? 1); 
                    if ($passengerCount <= 0) {
                        $passengerCount = 1; 
                    }
                    
                    $routes = collect($routes)->map(function ($route) use ($perKms, $passengerCount) {
                        
                        $distanceKm = round($route['distance_km']);
                        $totalFare = 0;
                        
                        if ($distanceKm != 0) {
                            $fare = $distanceKm * $perKms;
                            $com = $fare * 0.1;
                            $tax = 0;
                            
                            $totalFare = round($fare + $com + $tax);
                        }
                        
                        return [
                            'id'               => $route['id'] ?? null,
                            'route_index'      => $route['route_index'] ?? null,
                            'summary'          => $route['summary'] ?? null,
                            'distance_meters'  => (int) ($route['distance_meters'] ?? 0),
                            'distance_km'      => $distanceKm,
                            'duration_seconds' => (int) ($route['duration_seconds'] ?? 0),
                            'duration_text'    => gmdate('H:i:s', $route['duration_seconds'] ?? 0),
                            'toll_amount'      => (float) ($route['toll_amount'] ?? 0),
                            'per_swat'         => (int) $totalFare,
                            'route_labels'     => !empty($route['route_labels']) ? json_decode($route['route_labels'], true) : [],
                            'polyline'         => $route['polyline'] ?? null,
                        ];
                    
                    })->toArray();
                    
                }
                    
                
                
            }
            
            return response()->json([
                'status'  => true,
                'data'    => [],
                'route' => $routes,
                'message' => 'Fare calculated successfully'
            ]);
            
            return ApiResponse::success('Fare calculated successfully', $responseData);
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function getRouteStops($routeId)
    {
        try {
    
            $route = DB::table('route_options')
                ->where('id', $routeId)
                ->first();
    
            if (!$route) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Route not found'
                ]);
            }
    
            $stops = DB::table('route_stops')
                ->where('route_option_id', $routeId)
                ->orderBy('stop_order')
                ->get();
    
            if ($stops->count() == 0) {
    
                $generatedStops = $this->generateRouteStops(
                    $route
                );
                
                // dd($generatedStops);
    
                if (!empty($generatedStops)) {
    
                    DB::table('route_stops')
                        ->insert($generatedStops);
                }
    
                $stops = DB::table('route_stops')
                    ->where('route_option_id', $routeId)
                    ->orderBy('stop_order')
                    ->get();
            }
    
            return response()->json([
    
                'status' => true,
    
                'route' => [
    
                    'id' => $route->id,
    
                    'summary' => $route->summary,
    
                    'distance_km' => round(
                        $route->distance_meters / 1000,
                        2
                    ),
    
                    'duration_seconds' => $route->duration_seconds,
    
                    'polyline' => $route->polyline,
                ],
    
                'stops' => $stops
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
    
                'status' => false,
    
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function generateRouteStops($route)
    {
        try {
            $polyline = $route->polyline;
    
            if (!$polyline) {
                return [];
            }
    
            $points = Polyline::decode($polyline);
            
            if (empty($points)) {
                return [];
            }
    
            $insertData = [];
            $order = 1;
            $totalValues = count($points);
    
            for ($i = 0; $i < $totalValues; $i += 100) {
                
                $lat = $points[$i] ?? null;
                $lng = $points[$i + 1] ?? null;
    
                if ($lat !== null && $lng !== null) {
                    $insertData[] = [
                        'route_option_id' => $route->id,
                        'stop_name'       => 'Stop ' . $order,
                        'latitude'        => $lat,
                        'longitude'       => $lng,
                        'stop_order'      => $order,
                        'is_popular'      => 1,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                    $order++;
                }
            }
    
            $lastLatIndex = $totalValues - 2;
            $lastLngIndex = $totalValues - 1;
            
            if ($totalValues >= 2 && ($totalValues - 2) % 100 !== 0) {
                $insertData[] = [
                    'route_option_id' => $route->id,
                    'stop_name'       => 'Stop ' . $order,
                    'latitude'        => $points[$lastLatIndex],
                    'longitude'       => $points[$lastLngIndex],
                    'stop_order'      => $order,
                    'is_popular'      => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
    
            return $insertData;
    
        } catch (\Exception $e) {
            // Log the actual error to your laravel.log file for debugging
            \Log::error("Polyline generation failed: " . $e->getMessage());
            return [];
        }
    }
    
    private function ensureRoutePoints(
        $routeId,
        $polyline
    )
    {
        try {
    
            $exists = DB::table('route_points')
                ->where('route_id', $routeId)
                ->exists();
    
            if ($exists) {
                return true;
            }
    
            $this->storeRoutePoints(
                $routeId,
                $polyline
            );
    
            return true;
    
        } catch (\Throwable $e) {
    
            \Log::error(
                'Ensure Route Points Error',
                [
                    'message' => $e->getMessage()
                ]
            );
    
            return false;
        }
    }
    
    // private function storeRoutePoints($routeId, $encodedPolyline)
    // {
    //     try {
    //         if (!$encodedPolyline) {
    //             return;
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Decode Polyline (Returns flat array: [lat1, lng1, lat2, lng2...])
    //         |--------------------------------------------------------------------------
    //         */
    //         $points = \Polyline::decode($encodedPolyline);
    
    //         if (empty($points)) {
    //             return;
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Delete Existing
    //         |--------------------------------------------------------------------------
    //         */
    //         DB::table('route_points')
    //             ->where('route_id', $routeId)
    //             ->delete();
    
    //         $insertData = [];
    //         $order = 1;
    //         $geoHash = new GeohashService();
    //         $totalValues = count($points);
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Sample Every 20th Point (Step of 40 in a flat array)
    //         |--------------------------------------------------------------------------
    //         */
    //         for ($i = 0; $i < $totalValues; $i += 40) {
                
    //             $lat = $points[$i] ?? null;
    //             $lng = $points[$i + 1] ?? null;
    
    //             if ($lat !== null && $lng !== null) {
    //                 // Generate Geohash using your new Service
    //                 $hash = $geoHash->encode($lat, $lng, 6);
    
    //                 $insertData[] = [
    //                     'route_id'    => $routeId,
    //                     'latitude'    => $lat,
    //                     'longitude'   => $lng,
    //                     'geohash'     => $hash,
    //                     'point_order' => $order,
    //                     'created_at'  => now(),
    //                     'updated_at'  => now(),
    //                 ];
    //                 $order++;
    //             }
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Ensure the Last Point of the route is always included
    //         |--------------------------------------------------------------------------
    //         */
    //         if ($totalValues >= 2 && ($totalValues - 2) % 40 !== 0) {
    //             $lastLat = $points[$totalValues - 2];
    //             $lastLng = $points[$totalValues - 1];
                
    //             $insertData[] = [
    //                 'route_id'    => $routeId,
    //                 'latitude'    => $lastLat,
    //                 'longitude'   => $lastLng,
    //                 'geohash'     => $geoHash->encode($lastLat, $lastLng, 6),
    //                 'point_order' => $order,
    //                 'created_at'  => now(),
    //                 'updated_at'  => now(),
    //             ];
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Bulk Insert
    //         |--------------------------------------------------------------------------
    //         */
    //         if (!empty($insertData)) {
    //             foreach (array_chunk($insertData, 1000) as $chunk) {
    //                 DB::table('route_points')->insert($chunk);
    //             }
    //         }
    
    //     } catch (\Throwable $e) {
    //         \Log::error('Store Route Points Error', [
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    
    // private function getGeoHashPrefix(
    //     $lat,
    //     $lng,
    //     $precision = 5
    // )
    // {
    //     $geoHash = new GeohashService();
    
    //     return $geoHash->encode(
    //         $lat,
    //         $lng,
    //         $precision
    //     );
    // }
    
    public function shareLocation(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_id'       => 'required|exists:cus_job_temp,id',
                'invite_token' => 'required|string',
                'lat'          => 'required|numeric|between:-90,90',
                'lng'          => 'required|numeric|between:-180,180',
            ]);
    
            $userId = auth()->id();
    
            if (!$userId) {
                return ApiResponse::error('Unauthorized.', 401);
            }
    
            DB::transaction(function () use ($validated, $userId) {
    
                $updated = DB::table('invitations')
                    ->where([
                        'invite_token' => $validated['invite_token'],
                        'job_id' => $validated['job_id']
                    ])
                    ->update([
                        'location_share' => 1
                    ]);
    
                // if (!$updated) {
                //     throw new \Exception('Invalid invitation token.');
                // }
    
                DB::table('customer_register')
                    ->where('id', $userId)
                    ->update([
                        'lat' => $validated['lat'],
                        'lng' => $validated['lng']
                    ]);
            });
    
            return ApiResponse::success('Location shared successfully.');
    
        } catch (ValidationException $e) {
    
            return ApiResponse::error(
                'Validation failed.',
                $e->errors(),
                422
            );
    
        } catch (\Throwable $e) {
    
            return ApiResponse::error($e->getMessage(), 500);
    
        }
    }
    
}