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
use App\Services\AutomationEventService;
use App\Services\GeoHashService;
use Razorpay\Api\Api;
use Aws\S3\S3Client;
use App\Helpers\userLocationLog;
use Illuminate\Support\Facades\Cache;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Crypt;
use Polyline;

class DriverCarPoolJobController extends Controller
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
        $query = DB::table('customer_register')
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
    
    private function normalizeSearch($text)
    {
        
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $text));
        
    }
    
    private function getAlternativeRoutes($fromLat, $fromLng, $toLat, $toLng, $f_place_id, $t_place_id)
    {
        try {
          
            $coordinates = "{$fromLng},{$fromLat};{$toLng},{$toLat}";
            
            $response = Http::get("https://maps.g-ride.in/route/v1/driving/{$coordinates}", [
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
    
                $roadSummary = $route['summary'] ?? '';
                
                // if (empty($roadSummary) && !empty($route['legs'])) {
                //     $roadSummary = $route['legs'][0]['summary'] ?? '';
                // }
    
                // if (empty($roadSummary)) {
                // }
                // $roadSummary = ($index == 0) ? "Main Route" : "Alternative Route " . $index;
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
    
    // private function storeRouteStops(
    //     int $routeId,
    //     array $route,
    //     float $fromLat,
    //     float $fromLng,
    //     float $toLat,
    //     float $toLng
    // ): void
    // {
    //     try {
    
    //         DB::table('route_stops')
    //             ->where('route_id', $routeId)
    //             ->delete();
    
    //         $stops = [];
    
    //         $order = 1;
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Source Stop
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $source = $this->reverseGeocode(
    //             $fromLat,
    //             $fromLng
    //         );
    
    //         if ($source) {
    
    //             $stops[] = [
    //                 'route_id'   => $routeId,
    //                 'stop_name'  => $source,
    //                 'latitude'   => $fromLat,
    //                 'longitude'  => $fromLng,
    //                 'stop_order' => $order++,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Intermediate Stops
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $distanceCovered = 0;
    //         $durationCovered = 0;
    
    //         foreach ($route['legs'][0]['steps'] ?? [] as $step) {
    
    //             // $distanceCovered += $step['distance'];
    //             $distanceCovered += ($step['distance'] ?? 0);

    //             // $durationCovered += ($step['duration'] ?? 0);
                
    //             $durationCovered += (int) rtrim($step['duration'], 's');
    
    //             // Every 25 KM
    //             if ($distanceCovered < 25000) {
    //                 continue;
    //             }
    
    //             $distanceCovered = 0;
    
    //             $location =
    //                 $step['maneuver']['location']
    //                 ?? null;
    
    //             if (!$location) {
    //                 continue;
    //             }
    
    //             $lng = $location[0];
    //             $lat = $location[1];
    
    //             $place = $this->reverseGeocode(
    //                 $lat,
    //                 $lng
    //             );
    
    //             if (!$place) {
    //                 continue;
    //             }
    
    //             $lastStop = end($stops);
    
    //             if (
    //                 $lastStop &&
    //                 strtolower($lastStop['stop_name'])
    //                 === strtolower($place)
    //             ) {
    //                 continue;
    //             }
    
    //             $stops[] = [
    //                 'route_id'   => $routeId,
    //                 'stop_name'  => $place,
    //                 'latitude'   => $lat,
    //                 'longitude'  => $lng,
    //                 'stop_order' => $order++,
    //                 'travel_time' => round($durationCovered / 60),
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Destination Stop
    //         |--------------------------------------------------------------------------
    //         */
    
    //         $destination = $this->reverseGeocode(
    //             $toLat,
    //             $toLng
    //         );
    
    //         if (
    //             $destination &&
    //             (
    //                 empty($stops) ||
    //                 strtolower(end($stops)['stop_name'])
    //                 !== strtolower($destination)
    //             )
    //         ) {
    
    //             $stops[] = [
    //                 'route_id'   => $routeId,
    //                 'stop_name'  => $destination,
    //                 'latitude'   => $toLat,
    //                 'longitude'  => $toLng,
    //                 'stop_order' => $order++,
                    
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    
    //         if (!empty($stops)) {
    
    //             DB::table('route_stops')
    //                 ->insert($stops);
    //         }
    
    //     } catch (\Throwable $e) {
    
    //         Log::error('Store Route Stops Error', [
    //             'route_id' => $routeId,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    
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
                    'route_id'     => $routeId,
                    'stop_name'    => $source,
                    'latitude'     => $fromLat,
                    'longitude'    => $fromLng,
                    'stop_order'   => $order++,
                    'travel_time'  => 0,
                    'route_distance'  => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | Intermediate Stops
            |--------------------------------------------------------------------------
            */
    
            $distanceCovered = 0;
            $durationCovered = 0; // Seconds from source
            $cumulativeDistance = 0;
    
            foreach ($route['legs'][0]['steps'] ?? [] as $step) {
    
                $distanceCovered += ($step['distance'] ?? 0);
                $cumulativeDistance += ($step['distance'] ?? 0);
                /*
                |--------------------------------------------------------------------------
                | OSRM duration is in seconds
                |--------------------------------------------------------------------------
                */
    
                $durationCovered += ($step['duration'] ?? 0);
    
                // Every 25 KM
    
                if ($distanceCovered < 25000) {
                    continue;
                }
    
                $distanceCovered = 0;
    
                $location = $step['maneuver']['location'] ?? null;
    
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
                    strtolower($lastStop['stop_name']) ==
                    strtolower($place)
                ) {
                    continue;
                }
    
                $stops[] = [
                    'route_id'     => $routeId,
                    'stop_name'    => $place,
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'stop_order'   => $order++,
                    'travel_time'  => round($durationCovered / 60),
                    'route_distance' => $cumulativeDistance,
                    'created_at'   => now(),
                    'updated_at'   => now(),
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
                    strtolower(end($stops)['stop_name']) != strtolower($destination)
                )
            ) {
    
                /*
                |--------------------------------------------------------------------------
                | Total Route Duration
                |--------------------------------------------------------------------------
                */
    
                $totalDuration = 0;
    
                foreach ($route['legs'] ?? [] as $leg) {
    
                    $totalDuration += ($leg['duration'] ?? 0);
                }
    
                $stops[] = [
                    'route_id'     => $routeId,
                    'stop_name'    => $destination,
                    'latitude'     => $toLat,
                    'longitude'    => $toLng,
                    'stop_order'   => $order++,
                    'travel_time'  => round($totalDuration / 60),
                    'route_distance' => $cumulativeDistance,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
    
            if (!empty($stops)) {
    
                DB::table('route_stops')
                    ->insert($stops);
            }
    
        } catch (\Throwable $e) {
    
            Log::error('Store Route Stops Error', [
                'route_id' => $routeId,
                'message'  => $e->getMessage()
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
    
    public function GoogleLocations(Request $request)
    {
        try {
    
            $search = trim($request->search);
            $method = trim(strtolower($request->method ?? 'outstation'));
    
            if (strlen($search) < 2) {
    
                return response()->json([
                    'status' => 200,
                    'data'   => []
                ]);
            }
    
            $table = $method == 'carpool'
                // ? 'carpool_locations'
                ? 'outstation_locations'
                : 'outstation_locations';
    
            $limit = 3;
    
            $searchKey = $this->normalizeSearch($search);
    
            $cacheKey = "location_search:" . $method . ":" . $searchKey;
    
            $cached = Cache::get($cacheKey);
    
            if (!empty($cached)) {
    
                return response()->json([
                    'status' => 200,
                    'data'   => $cached,
                    'source' => 'cache',
                    'method' => $method
                ]);
            }
    
            $dbResults = DB::table($table)
                ->where('search_key', 'LIKE', '%' . $searchKey . '%')
                ->orderByRaw("
                    CASE
                        WHEN search_key LIKE ? THEN 1
                        ELSE 2
                    END
                ", [$searchKey . '%'])
                ->limit(10)
                ->get([
                    'display_name as name',
                    'place_id as id', // The map key needs to match this alias or the original column
                    'latitude',
                    'longitude',
                    'state',
                    'district'
                ])
                ->unique('id') // <-- Filters out duplicates by the 'id' alias
                ->map(function ($item) {
                    return (array) $item;
                })
                ->values() // <-- Resets array keys after filtering
                ->toArray();
    
            if (count($dbResults) >= $limit) {
    
                Cache::put($cacheKey, $dbResults, now()->addHours(6));
    
                return response()->json([
                    'status' => 200,
                    'data'   => $dbResults,
                    'source' => 'db',
                    'method' => $method
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
    
            if ($method == 'carpool') {
            
                $google = Http::timeout(3)->get(
                    'https://maps.googleapis.com/maps/api/place/queryautocomplete/json',
                    [
                        'input'      => $search,
                        'key'        => $googleKey,
                        'components' => 'country:in',
                    ]
                )->json();
    
            } else {
    
                $google = Http::timeout(3)->get(
                    'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                    [
                        'input'      => $search,
                        'key'        => $googleKey,
                        'components' => 'country:in',
                        'types'      => 'geocode'
                    ]
                )->json();
            }
    
            if (($google['status'] ?? '') !== 'OK') {
    
                Cache::put($cacheKey, $dbResults, now()->addMinutes(10));
    
                return response()->json([
                    'status' => 200,
                    'data'   => $dbResults,
                    'source' => 'db',
                    'method' => $method
                ]);
            }
    
            $newResults = [];
            $insertData = [];
    
            foreach ($google['predictions'] as $p) {
    
                if (count($newResults) >= ($limit - count($dbResults))) {
                    break;
                }
    
                $mainText = trim($p['description'] ?? '');
    
                if (!$mainText) {
                    continue;
                }
    
                $placeId = trim($p['place_id'] ?? '');
    
                if (!$placeId) {
                    continue;
                }
    
                $types = $p['types'] ?? [];
    
                if ($method == 'outstation') {
    
                    $blockedTypes = [
                        'establishment',
                        'point_of_interest',
                        'shopping_mall',
                        'hospital',
                        'airport',
                        'restaurant',
                        'lodging',
                        'store',
                        'school',
                        'gym',
                        'cafe',
                        'bank',
                        'premise',
                        'movie_theater',
                        'supermarket'
                    ];
    
                    $hasBlockedType = collect($types)
                        ->intersect($blockedTypes)
                        ->isNotEmpty();
    
                    if ($hasBlockedType) {
                        continue;
                    }
                }
    
                /*
                |--------------------------------------------------------------------------
                | CARPOOL FILTER
                |--------------------------------------------------------------------------
                */
    
                if ($method == 'carpool') {
    
                    $allowedTypes = [
                        'establishment',
                        'point_of_interest',
                        'shopping_mall',
                        'hospital',
                        'airport',
                        'train_station',
                        'bus_station',
                        'transit_station'
                    ];
    
                    $hasAllowedType = collect($types)
                        ->intersect($allowedTypes)
                        ->isNotEmpty();
    
                    if (!$hasAllowedType) {
                        continue;
                    }
                }
    
                /*
                |--------------------------------------------------------------------------
                | TERMS
                |--------------------------------------------------------------------------
                */
    
                $terms = $p['terms'] ?? [];
    
                $state = null;
                $district = null;
    
                if (!empty($terms)) {
    
                    $termsCount = count($terms);
    
                    if ($termsCount >= 2) {
                        $state = $terms[$termsCount - 2]['value'] ?? null;
                    }
    
                    if ($termsCount >= 3) {
                        $district = $terms[$termsCount - 3]['value'] ?? null;
                    }
                }
    
                /*
                |--------------------------------------------------------------------------
                | FORMATTED NAME
                |--------------------------------------------------------------------------
                */
    
                $formattedName = $mainText;
    
                /*
                |--------------------------------------------------------------------------
                | PREVENT DUPLICATES
                |--------------------------------------------------------------------------
                */
    
                $exists = collect($newResults)
                    ->merge($dbResults)
                    ->contains(function ($x) use ($placeId, $formattedName) {
    
                        $id = strtolower(trim($x['id'] ?? ''));
                        $name = strtolower(trim($x['name'] ?? ''));
    
                        return
                            $id == strtolower($placeId)
                            ||
                            $name == strtolower($formattedName);
                    });
    
                if ($exists) {
                    continue;
                }
    
                /*
                |--------------------------------------------------------------------------
                | LAT LNG
                |--------------------------------------------------------------------------
                */
    
                $lat = null;
                $lng = null;
    
                /*
                |--------------------------------------------------------------------------
                | RESPONSE ARRAY
                |--------------------------------------------------------------------------
                */
    
                $newResults[] = [
                    'name'      => $formattedName,
                    'id'        => $placeId,
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'state'     => $state,
                    'district'  => $district,
                    'method'    => $method,
                    'types'     => $types
                ];
    
                /*
                |--------------------------------------------------------------------------
                | INSERT ARRAY
                |--------------------------------------------------------------------------
                */
    
                $insertData[] = [
                    'place_id'     => $placeId,
                    'name'         => $mainText,
                    'display_name' => $formattedName,
                    'state'        => $state,
                    'district'     => $district,
                    'country'      => 'India',
                    'latitude'     => $lat,
                    'longitude'    => $lng,
                    'search_key'   => $this->normalizeSearch($formattedName),
                    'source'       => 'google',
                    'res_json'     => json_encode($p),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
    
            /*
            |--------------------------------------------------------------------------
            | UPSERT
            |--------------------------------------------------------------------------
            */
    
            if (!empty($insertData)) {
    
                DB::table($table)->upsert(
                    $insertData,
                    ['place_id'],
                    [
                        'name',
                        'display_name',
                        'state',
                        'district',
                        'latitude',
                        'longitude',
                        'updated_at'
                    ]
                );
            }
    
            /*
            |--------------------------------------------------------------------------
            | FINAL MERGE
            |--------------------------------------------------------------------------
            */
    
            $final = collect(
                    array_merge($dbResults, $newResults)
                )
                ->unique(function ($item) {
    
                    return strtolower(
                        trim(
                            ($item['id'] ?? '') . '|' . ($item['name'] ?? '')
                        )
                    );
                })
                ->values()
                ->take($limit)
                ->toArray();
    
            /*
            |--------------------------------------------------------------------------
            | CACHE STORE
            |--------------------------------------------------------------------------
            */
    
            if (!empty($final)) {
    
                Cache::put($cacheKey, $final, now()->addHours(6));
            }
    
            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */
    
            return response()->json([
                'status' => 200,
                'data'   => $final,
                'source' => 'hybrid',
                'method' => $method
            ]);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
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
                
                $perKms = 5;
                
                $perKm = DB::table('user_register')
                    ->where('id', $user_id)
                    ->value('per_km');
                
                $perKm = $perKm == 0 ? $perKms : $perKm;
                
                $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
                
                if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            
                    $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
            
                    $pass = $rcVehicle['seat_capacity'] ?? null;
            
                } else {
        
                    $get_ocr = DB::table('ocr_request')
                        ->where('user_id', $user_id)
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
                    
                if ($getRoute->count() > 0) {
                    
                        $routes = $getRoute->map(function ($route) use ($perKm, $pass) {
                            
                            $dis =round($route->distance_meters / 1000, 2);
                            
                            $totalFare = 0;
                            
                            if ($dis != 0) {
                                $fare = $dis * $perKm;
                                
                                $com = $fare * 0.1;
                                // $tax = ($fare + $com) * 0.05;
                                $tax = 0;
                                
                                $totalFare = round($fare + $com + $tax);
                            }
                            
                            $pass -= $pass - 1;
                            
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
                    
                    $routes = collect($routes)->map(function ($route) use ($perKm, $passengerCount) {
                        
                        $distanceKm = round($route['distance_km']);
                        $totalFare = 0;
                        
                        if ($distanceKm != 0) {
                            $fare = $distanceKm * $perKm;
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
    
    private function storeJobRouteJson(
        int $jobId,
        int $routeId,
        string $pickupDate
    ): void
    {
        try {
    
            $route = DB::table('route_options')
                ->where('id', $routeId)
                ->first();
    
            if (!$route) {
                return;
            }
    
            $stops = DB::table('route_stops')
                ->where('route_id', $routeId)
                ->orderBy('stop_order')
                ->get();
    
            $routeJson = [
                'route_id' => $route->id,
                'summary' => $route->summary,
                'distance_meters' => $route->distance_meters,
                'duration_seconds' => $route->duration_seconds,
                'toll_amount' => $route->toll_amount,
                'polyline' => $route->polyline,
            ];
    
            $stopJson = [];
    
            $pickupTime = Carbon::parse($pickupDate);
    
            foreach ($stops as $stop) {
    
                $estimatedTime = $pickupTime
                    ->copy()
                    ->addMinutes($stop->travel_time);
    
                $stopJson[] = [
                    'stop_order' => $stop->stop_order,
                    'stop_name' => $stop->stop_name,
                    'latitude' => $stop->latitude,
                    'longitude' => $stop->longitude,
                    'travel_time' => $stop->travel_time,
                    'route_distance' => $stop->route_distance,
                    'estimated_time' => $estimatedTime
                        ->format('Y-m-d H:i:s'),
                    'actual_arrival_time' => null,
                    'actual_departure_time' => null,
                    'status' => 'pending'
                ];
            }
    
            DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->update([
                    'route_json' => json_encode(
                        $routeJson,
                        JSON_UNESCAPED_UNICODE
                    ),
                    'stops_json' => json_encode(
                        $stopJson,
                        JSON_UNESCAPED_UNICODE
                    )
                ]);
    
        } catch (\Throwable $e) {
    
            Log::error(
                'Store Job Route JSON Error',
                [
                    'job_id' => $jobId,
                    'route_id' => $routeId,
                    'message' => $e->getMessage()
                ]
            );
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
            
            if (auth()->user()->walletBalance < 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance. Please top up to create job.',
                    'data'    => null,
                ]);
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
                ->where('global_type', 'dr_carpool')
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
                    "You already have a job booked during this time window (Busy from {$startTimeFormatted} to {$endTimeFormatted})."
                );
            }
            
            $vehicleDetails = json_decode(auth()->user()->vehicle_details, true);
            
            // $c_seat = 0;
            $c_seat = null;
            
            if (!empty($vehicleDetails['rc_details']['response']['vehicle_details'])) {
            // if (!empty($vehicleDetails['choosed_vehicle'])) {
        
                $rcVehicle = $vehicleDetails['rc_details']['response']['vehicle_details'];
        
                $c_seat = $rcVehicle['seat_capacity'] ?? null;
                $body_type = $rcVehicle['body_type'] ?? null;
                
                // $c_seat = $vehicleDetails['choosed_vehicle'];
        
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
            
            if ($request->seat > ($c_seat - 1)) {
            // if ($c_seat && ($c_seat == 'BIKE' && $request->seat > 1)) {
                return ApiResponse::error("Seat count exceeds your vehicle's maximum capacity.");
            }
            
            // dd();
            
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
                'global_type'   => 'dr_carpool',
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
            
            // if($request->isOpen && $request->isOpen == 1){
            // }
            $data['confirm_status'] = 1;
            
            $data['otp'] = Controller::generateOTP(4);
            
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
                    ->where('global_type', 'dr_carpool')
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
            
            $com = round((int)$data['fare'] * 0.05);
            $ovFare = (int)$data['fare'] + $com;
            $tax = round($ovFare * 0.05);
            
            $pay_amount = $ovFare + $tax;
            
            $farebreak = ['total_fare' => $pay_amount, 'com' => $com, 'tax' => $tax, 'base_fare' => (int)$data['fare']];
            
            $data['fare_breakdown'] = json_encode($farebreak);
            
            // 1. Insert into local temp job table
            $create_job = DB::table('cus_job_temp')->insertGetId($data);
            
            
            // 2. Prepare data payload for Firebase
            $data['poster_name'] = auth()->user()->name ?? 'System';
            $data['id'] = $create_job;
            
            $rt_id = $data['route_id'];
            unset($data['confirm_status']);
            unset($data['route_id']);
            
            $this->storeJobRouteJson(
                $create_job,
                $request->route_id,
                $request->pickup_date
            );
            
            // 3. Push to Firebase
            // $this->createFirebaseJob($job_no, $data);
            
            if (!empty($request->selected_stops)) {
    
                $stops = DB::table('route_stops')
                    ->whereIn('id', $request->selected_stops)
                    ->get();
    
                foreach ($stops as $index => $stop) {
    
                    DB::table('journey_stops')
                        ->insert([
    
                            'journey_id' => $journeyId,
    
                            'stop_name' => $stop->stop_name,
    
                            'latitude' => $stop->latitude,
    
                            'longitude' => $stop->longitude,
    
                            'stop_order' => $index + 1,
    
                            'is_custom' => 0,
    
                            'created_at' => now(),
    
                            'updated_at' => now(),
                        ]);
                }
            }
            
            if (!empty($request->custom_stops)) {

                foreach ($request->custom_stops as $customIndex => $custom) {
    
                    DB::table('journey_stops')
                        ->insert([
                            'journey_id' => $journeyId,
                            'stop_name' => $custom['name'],
                            'latitude' => $custom['lat'],
                            'longitude' => $custom['lng'],
                            'stop_order' => 100 + $customIndex,
                            'is_custom' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
            }
            
            $getPoly = DB::table('route_options')->where('id', $request->route_id)->first();
            
            // dd($getPoly);
            
            if($getPoly){
                $this->ensureRoutePoints(
                    $getPoly->id,
                    $getPoly->polyline
                );
            }
            
            if ($is_empty) {
                
                $res = [
                    'job_id'  => $create_job,
                    'job_no'  => $job_no
                ];
                
                // AutomationEventService::trigger(
                //     'before_one_pick_indicate',
                //     auth()->user()->id,
                //     [
                //         'ride_id' => $res['job_id']
                //     ]
                // );
                
                return ApiResponse::success('Job Created', $res);
            
            } else if (!$is_empty) {
                
                $frequencyText = implode(',', $freDays);
                $data['route_id'] = $rt_id;
                
                $frId = DB::table('frequency_job')->insertGetId([
                    'global_type'    => 'dr_carpool',
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
    
    public function myJobs()
    {
        try {
            
            $userId = auth()->id();
            
            // \Illuminate\Support\Facades\Log::info("Bearer token: " . request()->bearerToken());
    
            $jobs = DB::table('cus_job_temp as job')
            
                ->leftJoin('customer_register as cust', function ($join) {
                    $join->on('cust.id', '=', 'job.user_id')
                         ->where('job.global_type', '=', 'dr_carpool');
                })
                ->leftJoin(
                    'route_options as ro',
                    'ro.id',
                    '=',
                    'job.route_id'
                )
                ->where('job.user_id', $userId)
                ->whereIn('job.global_type', ['dr_carpool'])
                ->whereNotIn('job.job_status', ['completed', 'cancelled'])
                ->whereDate('job.pickup_date', '>=', now()->toDateString())
                ->orderBy('job.pickup_date')
                ->select(
                    'job.*',
                    DB::raw('cust.name as name'),
                    DB::raw('cust.fcm_token as fcm_token'),
                    DB::raw('cust.profile_img_url as profile_img_url'),
                    'ro.id as route_id',
                    'ro.summary as route_summary',
                    'ro.distance_meters',
                    'ro.duration_seconds',
                    'ro.toll_amount',
                    'ro.polyline'
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
                        WHEN i.global_type = 'dr_carpool' AND i.invitee_user_id = " . (int)$userId . " AND i.type = 'join' THEN i.inviter_id 
                        WHEN i.global_type = 'dr_carpool' THEN i.invitee_user_id 
                        ELSE NULL 
                    END"));
                })
                
                ->whereIn('i.id', $latestInviteIds)
                ->select(
                    'i.job_id',
                    'i.status',
                    'i.invite_token',
                    'i.from_place',
                    'i.from_place_id',
                    'i.to_place',
                    'i.to_place_id',
                    'i.type as jb_type',
                    'i.collectAmt',
                    'c.lat',
                    'c.lng',
                    // 1. Extract Total Fare
                    // DB::raw('i.fare_breakdown->>"$.total_fare" as total_fare'),
                    // 2. Calculate Net Fare (Total - Com - Tax)
                    DB::raw('(i.fare_breakdown->>"$.total_fare" - (i.fare_breakdown->>"$.com" + i.fare_breakdown->>"$.tax")) as driver_fare'),
                    'i.otpVerify',
                    'c.id as user_id',
                    'c.name as name',
                    'c.mobile as mobile',
                    'c.profile_img_url as profile_img_url',
                    'c.fcm_token as fcm_token'
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
                        
                        $fromGeo = $this->getLatLngByPlaceId(
                            $item->from_place_id
                        );
                
                        $toGeo = $this->getLatLngByPlaceId(
                            $item->to_place_id
                        );
                
                        if (!$fromGeo || !$toGeo) {
                
                            $fromLat = '';
                            $fromLng = '';
                            
                            $toLat = '';
                            $toLng = '';
                            
                        }else{
                            $fromLat = $fromGeo['lat'];
                            $fromLng = $fromGeo['lng'];
                    
                            $toLat = $toGeo['lat'];
                            $toLng = $toGeo['lng'];
                        }
                
                        
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'jb_type' => $item->jb_type,
                            'from_place' => $item->from_place,
                            'to_place' => $item->to_place,
                            'otpVerify' => $item->otpVerify,
                            'profile_img_url' => $item->profile_img_url,
                            'mobile' => $item->mobile,
                            'fcm_token' => $item->fcm_token,
                            'from_lat' => $fromLat,
                            'from_lng' => $fromLng,
                            'to_lat' => $toLat,
                            'to_lng' => $toLng,
                            'u_lat' => $item->lat,
                            'u_lng' => $item->lng,
                            'collectAmt' => $item->collectAmt,
                            'driver_fare' => $item->driver_fare,
                            'invite_token' => $item->invite_token
                        ];
                    })
                    ->values();
    
                $pending = $jobInvites
                    ->where('status', 'pending')
                    ->map(function ($item) {
                        
                        $fromGeo = $this->getLatLngByPlaceId(
                            $item->from_place_id
                        );
                
                        $toGeo = $this->getLatLngByPlaceId(
                            $item->to_place_id
                        );
                
                        if (!$fromGeo || !$toGeo) {
                
                            $fromLat = '';
                            $fromLng = '';
                            
                            $toLat = '';
                            $toLng = '';
                            
                        }else{
                            $fromLat = $fromGeo['lat'];
                            $fromLng = $fromGeo['lng'];
                    
                            $toLat = $toGeo['lat'];
                            $toLng = $toGeo['lng'];
                        }
                        
                        return [
                            'id' => $item->user_id,
                            'name' => $item->name,
                            'from_place' => $item->from_place,
                            'to_place' => $item->to_place,
                            'jb_type' => $item->jb_type,
                            'otpVerify' => $item->otpVerify,
                            'profile_img_url' => $item->profile_img_url,
                            'mobile' => $item->mobile,
                            'fcm_token' => $item->fcm_token,
                            'from_lat' => $fromLat,
                            'from_lng' => $fromLng,
                            'to_lat' => $toLat,
                            'to_lng' => $toLng,
                            'u_lat' => $item->lat,
                            'u_lng' => $item->lng,
                            'collectAmt' => $item->collectAmt,
                            'driver_fare' => $item->driver_fare,
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
                    'filled_seat' => $job->filled_seat,
                    'fare' => $job->fare,
                    'job_status' => $job->job_status,
                    'jb_type' => $job->confirm_status,
                    'live_tracking' => $job->live_tracking,
                    'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
                    'accepted_friends' => $accepted,
                    'pending_invitations' => $pending,
                    'deepLink' => $deep.'/carpool?jid='.$encryptedId,
                    'selected_route' => [
                        'route_id' => $job->route_id,
                        'summary' => $job->route_summary,
                        'distance_km' => $job->distance_meters
                            ? round($job->distance_meters / 1000, 2)
                            : 0,
                        'duration_minutes' => $job->duration_seconds
                            ? round($job->duration_seconds / 60)
                            : 0,
                        'toll_amount' => $job->toll_amount ?? 0,
                        'polyline' => $job->polyline
                    ],
                ];
            }
            
            usort($result, function ($a, $b) {
                return strtotime($a['pickup_date']) <=> strtotime($b['pickup_date']);
            });
    
            return ApiResponse::success('My jobs fetched', $result);
    
        } catch (\Throwable $e) {
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
                ->whereNotIn('status', ['accepted', 'rejected'])
                ->lockForUpdate()
                ->first();

            if (!$invite) {
                DB::commit();
                return ApiResponse::error('Request was Processed! Refresh to Continue');
            }
            
            $job = DB::table('cus_job_temp')    
                ->where('id', $invite->job_id)
                ->where('user_id', $userId)
                ->where('global_type', 'dr_carpool')
                ->lockForUpdate() 
                ->first();
                
            if (!$job) {
                DB::commit();
                return ApiResponse::error('Job not found');
            }
            
            $action_status = $request->type;
            
            $otp = Controller::generateOTP(4);
                
            if ( $job->filled_seat < $job->pass_count || $action_status != 'accept' ) {
                
                if($action_status == 'accept'){
                    DB::table('cus_job_temp')
                        ->where('id', $invite->job_id)
                        ->increment('filled_seat', 1);
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
                    'otp' => $otp,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            $res = 'Invitation '. ($action_status == 'accept' ? 'accepted' : 'rejected');
            
            $cusD = DB::table('customer_register')->where(['id' => $invite->inviter_id, 'status' => '0', 'deletes' => '0'])->first();
            
            if ($cusD) {
                $inviter = [$cusD->id];
                // $fcmTokens = $this->getFcm($inviter, null, null, null);
                $fcmTokens = [$cusD->fcm_token];
            
                if (!empty($fcmTokens)) {
                    $accessToken = $this->getAccessToken();
            
                    if ($accessToken) {
                        // Check the status of the invite (Assuming $invite->status holds the value)
                        if ($action_status == 'accept') {
                            $title = "✅ Request Accepted!";
                            $body  = "Great news! {$user->name} has accepted your invitation for the ride to {$invite->from_place}.";
                            $type  = 'invite_accepted';
                        } else {
                            $title = "❌ Request Declined";
                            $body  = "Unfortunately, {$user->name} declined the invitation for the ride to {$invite->from_place}.";
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
            
            if($action_status == 'accept'){
                
                // $sendTemplateMessage = function($mobile, $templateName, $parameters) use ($request) {
                //         $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);
                //         if (strlen($cleanPhone) === 10) {
                //             $cleanPhone = '91' . $cleanPhone;
                //         }
    
                //         $template = DB::table('wamail_templates')->where('name', $templateName)->first();
                //         if (!$template) return;
    
                //         $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";
    
                //         $bodyParameters = [];
                //         foreach ($parameters as $param) {
                //             $val = ($param !== null && $param !== '') ? (string) $param : '-';
                //             $bodyParameters[] = [
                //                 "type" => "text",
                //                 "text" => $val
                //             ];
                //         }
    
                //         $components = [];
    
                //         if (!empty($template->header_image)) {
                //             $components[] = [
                //                 "type" => "header",
                //                 "parameters" => [
                //                     [
                //                         "type" => "image",
                //                         "image" => [
                //                             "link" => $template->header_image 
                //                         ]
                //                     ]
                //                 ]
                //             ];
                //         }
    
                //         if (!empty($bodyParameters)) {
                //             $components[] = [
                //                 "type" => "body",
                //                 "parameters" => $bodyParameters
                //             ];
                //         }
    
                //         if (!empty($template->variables_json)) {
                //             $buttonsConfig = json_decode($template->variables_json, true);
                //             if (!empty($buttonsConfig['buttons'])) {
                //                 foreach ($buttonsConfig['buttons'] as $index => $btn) {
                //                     if ($btn['type'] === 'COPY_CODE') {
                //                         $components[] = [
                //                             "type" => "button",
                //                             "sub_type" => "url",
                //                             "index" => (string)$index,
                //                             "parameters" => [
                //                                 [
                //                                     "type" => "text",
                //                                     "text" => (string)($parameters[0] ?? '123456')
                //                                 ]
                //                             ]
                //                         ] ;
                //                     }
                //                     if ($btn['type'] === 'URL' && strpos($btn['url'] ?? '', '{{1}}') !== false) {
                //                         $components[] = [
                //                             "type" => "button",
                //                             "sub_type" => "url",
                //                             "index" => (string)$index,
                //                             "parameters" => [
                //                                 [
                //                                     "type" => "text",
                //                                     "text" => (string)($parameters[0] ?? '')
                //                                 ]
                //                             ]
                //                         ];
                //                     }
                //                 }
                //             }
                //         }
    
                //         $templatePayload = [
                //             "name" => $templateName,
                //             "language" => [
                //                 "code" => "en_US"
                //             ]
                //         ];
    
                //         if (!empty($components)) {
                //             $templatePayload["components"] = $components;
                //         }
    
                //         $payload = [
                //             "messaging_product" => "whatsapp",
                //             "to" => $cleanPhone,
                //             "type" => "template",
                //             "template" => $templatePayload
                //         ];
    
                //         $reqTime = now();
                //         \Illuminate\Support\Facades\Log::info("WhatsApp Request Payload [{$templateName}]:", $payload);
    
                //         $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                //         $resTime = now();
                //         $body = $response->json();
                        
                //         if (!$response->successful()) {
                //             \Illuminate\Support\Facades\Log::error("WhatsApp API Error [{$templateName}]:", ['status' => $response->status(), 'response' => $body]);
                //         } else {
                //             \Illuminate\Support\Facades\Log::info("WhatsApp API Success [{$templateName}]:", $body);
                //         }
    
                //         $messageId = $body['messages'][0]['id'] ?? null;
                //         $isSuccess = $response->successful();
    
                //         DB::table('smslog')->insert([
                //             'gateway' => 'fbWhatsapp',
                //             'subject' => $templateName,
                //             'details' => json_encode($parameters),
                //             'mobile' => $cleanPhone,
                //             'ip' => request()->ip() ?? '',
                //             'datetime' => now(),
                //             'token_response' => json_encode($body),
                //             'status' => $isSuccess ? 'sent' : 'failed',
                //             'reference_id' => $messageId ?? '',
                //             'site' => 'CUSTOMER',
                //             'REQ_Time' => $reqTime,
                //             'RES_Time' => $resTime,
                //             'smsdetails' => json_encode($payload),
                //             'smsstatus' => $isSuccess ? 'Sent' : 'Failed',
                //             'smssendstatus' => $isSuccess ? '1' : '0',
                //             'response' => $response->body(),
                //             'isResend' => 'NO'
                //         ]);
                //     };
                    
                // $sendTemplateMessage(
                //     $cusD->mobile, 
                //     'otp_start_ride', 
                //     [$otp]
                // );
                
                $this->jobOtpInsert($otp, $job->id);
            }
            

            return ApiResponse::success($res);

        } catch (\Throwable $e) {
            DB::rollBack();
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
                'job_id' => ['required', 'exists:cus_job_temp,id'],
                'invite_token' => ['required']
            ]);
    
            $userId = auth()->id();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->where('user_id', $userId)
                ->where('global_type', 'dr_carpool')
                ->first();
    
            if (!$job) {
                return ApiResponse::error('Job not found');
            }
    
            $inv = DB::table('invitations')
                ->where('job_id', $job->id)
                ->where('invite_token', $request->invite_token)
                ->where('status', 'pending')->exists();
                
            if($inv){
                // return 'hi';
                return ApiResponse::success('Job retrieved', []);
            }else{
                // return 'hello';
                return ApiResponse::error('Request was Processed! Please check your carpool list.');
            }
    
    
        } catch (ValidationException $e) {
            // return response()->json('Hii', 200);
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
    
    // public function jobHistory(Request $request)
    // {
    //     try {
    //         $userId = auth()->id();
    
    //         $ownJobs = [];
            
    //         $jobIds = DB::table('invitations')
    //             ->where(function ($query) use ($userId) {
    //                 $query->where('invitee_user_id', $userId)
    //                       ->where('type', 'join');
    //             })
    //             ->orWhere(function ($query) use ($userId) {
    //                 $query->where('invitee_user_id', $userId)
    //                       ->where('type', 'job');
    //             })
    //             ->pluck('job_id');
            
    //         $latestInviteIds = DB::table('invitations')
    //             ->whereIn('job_id', $jobIds)
            
    //             ->select(DB::raw('MAX(id) as id'))
    //             ->groupBy('job_id')
    //             ->groupBy(DB::raw("CASE WHEN type = 'join' THEN inviter_id ELSE invitee_user_id END"))
    //             ->pluck('id');
                
    //         $passengerList = DB::table('invitations as i')
               
    //             // ->leftJoin('user_register as ur', function ($join) use ($userId) {
    //             //     $join->on('ur.id', '=', DB::raw("CASE 
    //             //                 WHEN i.inviter_id = $userId OR i.type = 'join' THEN i.inviter_id 
    //             //                 ELSE i.invitee_user_id 
    //             //             END"))
    //             //          ->where('i.global_type', '=', 'dr_carpool');
    //             // })
              
    //             ->leftJoin('customer_register as cr', function ($join) use ($userId) {
    //                 $join->on('cr.id', '=', DB::raw("CASE 
    //                             WHEN i.invitee_user_id = $userId OR i.type = 'join' THEN i.invitee_user_id 
    //                             ELSE i.invitee_user_id 
    //                         END"))
    //                      ->where('i.global_type', '=', 'dr_carpool');
    //             })
    //             ->whereIn('i.id', $latestInviteIds)
    //             ->select([
    //                 'i.job_id',
    //                 'i.type',
    //                 'i.status',
    //                 'i.invite_token',
                   
    //                 DB::raw("cr.id as id"),
    //                 DB::raw("cr.name as name"),
    //                 DB::raw("cr.profile_img_url as profile_img_url"),
    //                 DB::raw("cr.fcm_token as fcm_token")
    //             ])
    //             ->get()
    //             ->groupBy('job_id');
                
    //         $joinedJobsQuery = DB::table('cus_job_temp as job')
    //             ->leftJoin('user_register as ur_host', function ($join) {
    //                 $join->on('ur_host.id', '=', 'job.user_id')
    //                      ->where('job.global_type', '=', 'dr_carpool');
    //             })
    //             // ->leftJoin('customer_register as cr_host', function ($join) {
    //             //     $join->on('cr_host.id', '=', 'job.user_id')
    //             //          ->where('job.global_type', '!=', 'dr_carpool');
    //             // })
    //             ->whereIn('job.id', $jobIds)
    //             ->whereIn('job.global_type', ['carpool', 'dr_carpool'])
    //             ->whereDate('job.pickup_date', '>=', now()->toDateString())
    //             ->where('job.job_status', ['cancelled', 'completed'])
    //             ->where('job.deletes', '0');
            
    //         $joinedJobs = $joinedJobsQuery
    //             ->select([
    //                 'job.id',
    //                 'job.global_type',
    //                 'job.job_no',
    //                 'job.user_id',
    //                 'job.from_place',
    //                 'job.to_place',
    //                 'job.job_status',
    //                 'job.fare',
    //                 'job.isLock',
    //                 'job.pickup_date',
    //                 'job.pass_count as total_seats',
    //                 'job.filled_seat',
    //                 DB::raw("'join' as job_type"),
                    
    //                 DB::raw("ur_host.name as name"),
                    
    //                 DB::raw("ur_host.profile_img_url as profile_img_url"),
                    
    //                 DB::raw("ur_host.fcm_token as fcm_token"),
                    
    //                 DB::raw("CASE 
    //                     WHEN job.global_type = 'dr_carpool' THEN IFNULL(JSON_UNQUOTE(JSON_EXTRACT(ur_host.vehicle_details, '$.choosed_vehicle')), '')
    //                     ELSE IFNULL(JSON_UNQUOTE(JSON_EXTRACT(cr_host.vehicle_details, '$.choosed_vehicle')), '')
    //                 END as choosed_vehicle")
    //             ])
    //             ->latest('job.id')
    //             ->get();
                
    //         $deep = env('DEEPLINK_CUSTOMER');
            
    //         $jobs = $joinedJobs
                
    //             // ->filter(function ($job) {
    //             //     return $job->job_status != 'cancelled';
    //             // })
                
    //             ->map(function ($job) use ($passengerList, $deep) {

    //             $passengers = $passengerList[$job->id] ?? collect();
                
    //             $encryptedId = $this->encryptJobId($job->id);
                
    //             $jobType = 'Requested';
                
    //             $userPassenger = $passengers->first(function ($p) {
                
    //                 return $p->id == auth()->id();
                
    //             });
                
    //             if ($userPassenger) {
                
    //                 if ($userPassenger->status == 'accepted') {
                
    //                     $jobType = 'Confirmed';
                
    //                 } elseif ($userPassenger->status == 'rejected') {
                
    //                     $jobType = 'Rejected';
                
    //                 } elseif ($userPassenger->status == 'removed') {
                
    //                     $jobType = 'Removed';
                
    //                 }
    //             }
    
    //             return [
    //                 'id' => $job->id,
    //                 'job_no' => $job->job_no,
    //                 'global_type' => $job->global_type,
    //                 'user_id' => $job->user_id,
    //                 'name' => $job->name,
    //                 'profile_img_url' => $job->profile_img_url,
    //                 'fcm_token' => $job->fcm_token,
    //                 'from_place' => $job->from_place,
    //                 'to_place' => $job->to_place,
    //                 'fare' => $job->fare,
    //                 'pickup_date' => $job->pickup_date,
    //                 'total_seats' => $job->total_seats,
    //                 'filled_seat' => $job->filled_seat,
    //                 'isLock' => $job->isLock == 0 ? 'Unlocked' : 'Locked',
    //                 'choosed_vehicle' => $job->choosed_vehicle,
    //                 'job_type' => $jobType,
    //                 // 'job_type' => $job->job_type,
    
    //                 'passenger_count' => $passengers
    //                     ->filter(function ($p) {
    //                         return $p->status == 'accepted';
    //                     })->count(),
    
    //                 'passengers' => $passengers
    //                     ->filter(function ($p) {
    //                         return $p->status == 'accepted';
    //                     })
    //                     ->map(function ($p) {
        
    //                         return [
    //                             'id' => $p->id,
    //                             'name' => $p->name,
    //                             'profile_img_url' => $p->profile_img_url,
    //                             'type' => $p->type,
    //                             'status' => $p->status,
    //                             'invite_token' => $p->invite_token,
    //                             'fcm_token' => $p->fcm_token
    //                         ];
        
    //                     })->values(),
                    
    //                 'deepLink' => $deep.'/carpool?jid='.$encryptedId
    //             ];
    //         });
            
    
    //         return ApiResponse::success('Job history fetched successfully', [
    //             'jobs' => $jobs
    //         ]);
    
    //     } catch (\Throwable $e) {
    //         return ApiResponse::error($e->getMessage());
    //     }
    // }
    
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
                ->where('global_type', 'dr_carpool')
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
                ->where('global_type', 'dr_carpool')
                ->where('job_no', $request->job_no)
                // ->where('job_status', 'created')
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
                ->where('fj.global_type', 'dr_carpool')
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
                ->where('global_type', 'dr_carpool')
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
                ->where('global_type', 'dr_carpool')
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
    
    //         $points = Polyline::decode($encodedPolyline);
    
    //         if (empty($points)) {
    //             return;
    //         }
    
    //         $insertData = [];
    //         $order = 1;
    //         $totalValues = count($points);
    
    //         for ($i = 0; $i < $totalValues; $i += 40) {
                
    //             $lat = $points[$i] ?? null;
    //             $lng = $points[$i + 1] ?? null;
    
    //             if ($lat !== null && $lng !== null) {
    //                 $insertData[] = [
    //                     'route_id'    => $routeId,
    //                     'latitude'    => $lat,
    //                     'longitude'   => $lng,
    //                     'point_order' => $order,
    //                     'created_at'  => now(),
    //                     'updated_at'  => now(),
    //                 ];
    //                 $order++;
    //             }
    //         }
    
    //         $lastLatIndex = $totalValues - 2;
    //         $lastLngIndex = $totalValues - 1;
    
    //         if ($totalValues >= 2 && ($totalValues - 2) % 40 !== 0) {
    //             $insertData[] = [
    //                 'route_id'    => $routeId,
    //                 'latitude'    => $points[$lastLatIndex],
    //                 'longitude'   => $points[$lastLngIndex],
    //                 'point_order' => $order,
    //                 'created_at'  => now(),
    //                 'updated_at'  => now()
    //             ];
    //         }
    
    //         if (!empty($insertData)) {
    //             foreach (array_chunk($insertData, 1000) as $chunk) {
    //                 DB::table('route_points')->insert($chunk);
    //             }
    //         }
    
    //     } catch (\Throwable $e) {
    //         \Log::error('Route Point Store Error', [
    //             'message' => $e->getMessage(),
    //             'trace'   => $e->getTraceAsString()
    //         ]);
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
    
    public function otpVerify(Request $request)
    {
        try {
            $data = $request->validate([
                'job_id'       => 'required',
                'otp'          => 'required',
                'invite_token' => 'required',
                'lat'          => 'nullable',
                'lng'          => 'nullable'
            ]);
            
            $user = auth()->user();
    
            // 1. Fetch Job
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->where('global_type', 'dr_carpool')
                ->where('job_status', '!=', 'cancelled')
                ->first();
    
            if (!$job) {
                return response()->json(['status' => false, 'message' => 'Job not found'], 200);
            }
    
            // 2. Validate OTP Record
            $otpRecord = DB::table('job_start_otps')
                ->where('job_id', $data['job_id'])
                ->where('otp', $data['otp'])
                ->whereNull('verified_at')
                ->latest('id')
                ->first();
    
            if (!$otpRecord) {
                DB::table('job_start_otps')
                    ->where('job_id', $data['job_id'])
                    ->whereNull('verified_at')
                    ->latest('id')
                    ->limit(1)
                    ->increment('attempts');
    
                return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 200);
            }
    
            if ($otpRecord->attempts >= $otpRecord->max_attempts) {
                return response()->json(['status' => false, 'message' => 'Max attempts reached'], 200);
            }
    
            // 3. Process Transaction
            DB::transaction(function () use ($otpRecord, $data, $user, $request, $job) {
                
                // Mark OTP as verified
                DB::table('job_start_otps')->where('id', $otpRecord->id)->update([
                    'verified_at' => now(),
                    'verified_by' => $user->id,
                    's_lat'       => $request->lat,
                    's_lng'       => $request->lng,
                    'updated_at'  => now()
                ]);
                
                $inv = DB::table('invitations')->where('invite_token', $data['invite_token'])->first();
                
                if ($inv) {
                    // Calculate Fare
                    $fare = json_decode($inv->fare_breakdown, true);
                    $dedAmt = (float) ($fare['com'] ?? 0) + (float) ($fare['tax'] ?? 0);
                    
                    $walletBalance  = (float) $user->walletBalance;
                    $closingBalance = $walletBalance - $dedAmt;
    
                    // Update User Wallet
                    DB::table('user_register')->where('id', $user->id)->update([
                        'walletBalance' => $closingBalance
                    ]);
    
                    // Insert History
                    DB::table('walletBalance_history')->insert([
                        "userid"           => $user->id,
                        "uname"            => $user->name ?? '',
                        "umobile"          => $user->mobile ?? '',
                        "uemail"           => $user->email ?? '',
                        'opening_balance'  => $walletBalance,
                        'total'            => $dedAmt,
                        'closeing_balance' => $closingBalance,
                        'point_type'       => 'WALLET',
                        'transaction_type' => 'DEBIT',
                        'reward_type'      => 'JOB',
                        'reference_id'     => $inv->id,
                        'reference_table'  => 'invitations',
                        'ip'               => $request->ip(),
                        'createdon'        => now()
                    ]);
    
                    // Update Invitation
                    DB::table('invitations')->where('id', $inv->id)->update(['isDeduct' => 1, 'otpVerify' => 1]);
                    
                    // Send Notification
                    if (!empty($user->fcm_token)) {
                        $accessToken = $this->getAccessToken();
                        $title = "Wallet Amount Deducted";
                        $body = "₹{$dedAmt} has been deducted from your wallet for the assigned trip. Balance: ₹{$closingBalance}.";
                    
                        $this->sendFCM(
                            $accessToken,
                            $user->fcm_token,
                            $title,
                            $body,
                            [
                                'type'           => 'wallet_deduction',
                                'job_id'         => $job->id,
                                'deduct_amount'  => $dedAmt,
                                'wallet_balance' => $closingBalance,
                            ]
                        );
                    }
                    
                    // Update Job Status if not already verified
                    if ($job->otpVerify == 0) {
                        DB::table('cus_job_temp')->where('id', $job->id)->update([
                            'otpVerify'  => 1,
                            'live_tracking' => 1,
                            'job_status' => 'started',
                            'updated_at' => now()
                        ]);
                    }
                }
            });
            
            return response()->json([
                'status'  => true,
                'message' => 'OTP verified. Passenger onboard.'
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            \Log::error('OTP VERIFY ERROR: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Server error'], 500);
        }
    }
    
    public function dropPassenger(Request $request)
    {
        try {
    
            $data = $request->validate([
                'job_id'       => 'required|integer',
                'invite_token' => 'required|string'
            ]);
    
            $driver = auth()->user();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                // ->where('user_id', $driver->id)
                ->where('global_type', 'dr_carpool')
                // ->whereNotIn('job_status', ['cancelled', 'completed'])
                ->first();
    
            if (!$job) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Ride not found or access denied.'
                ], 200);
            }
    
            $invitation = DB::table('invitations')
                ->where('job_id', $job->id)
                ->where('invite_token', $data['invite_token'])
                ->where('status', 'accepted')
                ->first();
    
            if (!$invitation) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid passenger invitation.'
                ], 200);
            }
    
            if ($invitation->otpVerify == 2) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Passenger already dropped.'
                ], 200);
            }
    
            DB::beginTransaction();
    
            DB::table('invitations')
                ->where('id', $invitation->id)
                ->update([
                    'otpVerify' => 2,
                    'updated_at' => now()
                ]);
    
            $passenger = DB::table('customer_register')
                ->where('id', $invitation->inviter_id)
                ->select(
                    'id',
                    'name',
                    'fcm_token'
                )
                ->first();
    
            DB::commit();
    
            if (
                $passenger &&
                !empty($passenger->fcm_token)
            ) {
    
                try {
    
                    $accessToken = $this->getAccessToken();
    
                    $this->sendFCM(
                        $accessToken,
                        $passenger->fcm_token,
                        'Destination Reached 📍',
                        'You have reached your destination successfully.',
                        [
                            'type' => 'passenger_dropped',
                            'job_id' => (string) $job->id,
                            'invite_token' => $invitation->invite_token,
                            'screen' => 'my-posts',
                            'action' => 'trip_completed'
                        ]
                    );
    
                } catch (\Throwable $e) {
    
                    \Log::warning(
                        'Drop Passenger Notification Failed',
                        [
                            'job_id' => $job->id,
                            'passenger_id' => $passenger->id,
                            'error' => $e->getMessage()
                        ]
                    );
                }
            }
    
            $remainingPassengers = DB::table('invitations')
                ->where('job_id', $job->id)
                ->where('status', 'accepted')
                ->where('otpVerify', '!=', 2)
                ->count();
    
            return response()->json([
                'status' => true,
                'message' => 'Passenger dropped successfully.',
                'data' => [
                    'remaining_passengers' => $remainingPassengers,
                    'all_passengers_dropped' => $remainingPassengers == 0
                ]
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            \Log::error(
                'DROP PASSENGER ERROR: '.$e->getMessage(),
                [
                    'line' => $e->getLine()
                ]
            );
    
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
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
            
            if($invite->otpVerify){
                DB::rollBack();
                return ApiResponse::error('Cannot remove passenger because the OTP has already been verified.');
                
            }
    
            $job = DB::table('cus_job_temp')
                ->where('id', $invite->job_id)
                // ->where('user_id', $userId)
                ->whereIn('global_type', ['dr_carpool'])
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
    
            return ApiResponse::success('Passenger removed.');
    
        } catch (\Throwable $e) {
            DB::rollBack();
            return ApiResponse::error('Something went wrong.');
        }
    }
    
    public function userProfile(Request $request)
    {
        // dd($request->all());
        
        $validator = Validator::make($request->all(), [
            'id' => ['required']
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Validation Error!',
                'error'   => $validator->errors()
            ]);
        }
        
        $customer = DB::table('customer_register')->where(['id' => $request->id])->first();

        if($customer){
            
            $kycDetails = DB::table('kyc_carpool')->where(['user_id' => $customer->id])->first();
            
            $hostCount = DB::table('cus_job_temp')
                    ->where('user_id', $customer->id)
                    ->whereIn('global_type', ['carpool', 'dr_carpool'])
                    ->count();
                    
            $joinCount = DB::table('invitations')
                ->where('inviter_id', $customer->id)
                ->whereIn('global_type', ['carpool', 'dr_carpool'])
                ->where('status', 'accepted')
                ->select(DB::raw('MAX(id) as id'))
                ->groupBy('job_id')
                // Group by the "other" person regardless of whether they are inviter or invitee
                ->groupBy('inviter_id')
                ->count();
            
            $upcomingJob = [];
            
            $uid = auth()->user()->id;
            $uuid = $customer->id;
            
            // dd($uuid);
            
            if ($hostCount > 0) {
                
                $upcomingJob = DB::table('cus_job_temp as ct')
                    ->where('ct.user_id', $uuid)
                    ->whereIn('ct.global_type', ['carpool', 'dr_carpool'])
                    ->where('ct.pickup_date', '>', now())
                    ->whereNotExists(function ($query) use ($uid) {
                        $query->select(DB::raw(1))
                            ->from('invitations as in')
                            ->whereColumn('in.job_id', 'ct.id')
                            ->where(function ($sub) use ($uid) {
                                $sub->where(function ($q) use ($uid) {
                                    $q->where('in.inviter_id', $uid)
                                      ->where('in.type', 'join');
                                })
                                ->orWhere(function ($q) use ($uid) {
                                    $q->where('in.invitee_user_id', $uid)
                                      ->where('in.type', 'job');
                                });
                            })
                            ->whereIn('in.status', ['pending', 'accepted', 'removed']);
                    })
                    ->select('ct.*')
                    ->get()
                    ->toArray();
                    
            }
        
            return response()->json([
                'status' => 'success',
                'data'   => $customer,
                'kyc'    => $kycDetails,
                'hostCount' => $hostCount,
                'joinCount' => $joinCount,
                'upcomingJob' => $upcomingJob
                
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
                'data'   => [],
                'kyc'    => null,
                'hostCount' => 0,
                'joinCount' => 0,
                'upcomingJob' => []
                
            ]);
        }
    }
    
    public function startRide(Request $request)
    {
        try {
    
            $data = $request->validate([
                'job_id' => 'required',
                'lat'    => 'nullable',
                'lng'    => 'nullable'
            ]);
    
            $user = auth()->user();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->first();
    
            if (!$job) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found.'
                ]);
            }
    
            if ($job->user_id != $user->id) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access.'
                ]);
            }
    
            $activeJob = DB::table('cus_job_temp')
                ->where('user_id', $user->id)
                ->where('global_type', 'dr_carpool')
                ->where('job_status', 'started')
                ->where('pickup_date', '<', now())
                ->where('id', '!=', $job->id)
                ->first();
    
            if ($activeJob) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Please complete previous ride before starting another ride.'
                ]);
            }
    
            if ($job->job_status == 'started') {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Ride already started.'
                ]);
            }
    
            DB::table('cus_job_temp')
                ->where('id', $job->id)
                ->update([
                    'job_status' => 'started',
                    'live_tracking' => 1,
                    'updated_at' => now()
                ]);
    
            $acceptedPassengers = DB::table('invitations as i')
                ->join(
                    'customer_register as c',
                    'c.id',
                    '=',
                    'i.invitee_user_id'
                )
                ->where('i.job_id', $job->id)
                ->where('i.global_type', 'dr_carpool')
                ->where('i.status', 'accepted')
                ->select(
                    'c.id',
                    'c.name',
                    'c.fcm_token'
                )
                ->get();
                
            $accessToken = $this->getAccessToken();
    
            if (!empty($user->fcm_token)) {
    
                $this->sendFCM(
                    $accessToken,
                    $user->fcm_token,
                    'Ride Started 🚗',
                    'Your ride has started successfully.',
                    [
                        'type'   => 'ride_started',
                        'job_id' => $job->id,
                        'action' => 'driver_ride'
                    ]
                );
            }
            
            foreach ($acceptedPassengers as $passenger) {
                
                if (empty($passenger->fcm_token)) {
                    continue;
                }
    
                $this->sendFCM(
                    $accessToken,
                    $passenger->fcm_token,
                    'Ride Started 🚗',
                    'Your driver has started the trip.',
                    [
                        'type'   => 'ride_started',
                        'job_id' => $job->id,
                        'action' => 'trip_tracking'
                    ]
                );
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Ride started successfully.',
                'data' => null
            ]);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            \Log::error(
                'Start Ride Error: '.$e->getMessage()
            );
    
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
    
    public function settingUpdate(Request $request)
    {
        try {
    
            $data = $request->validate([
                'km_amt' => 'required'
            ]);
    
            $user = auth()->user();
    
            DB::table('user_register')
                ->where('id', $user->id)
                ->update([
                    'per_km' => $request->km_amt,
                    'updated_at' => now()
                ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Km price setup completed.',
                'data' => null
            ]);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            \Log::error(
                'Start Ride Error: '.$e->getMessage()
            );
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
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
                // ->where('job_status', 'started')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found or not started.',
                ], 200);
            }
            
            // if (Carbon::now()->lt(Carbon::parse($job->pickup_date))) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'You cannot complete the ride before its scheduled pickup time.',
            //     ], 200);
            // }
    
            // $otp = DB::table('job_start_otps')
            //     ->where('job_id', $data['job_id'])
            //     ->whereNotNull('verified_at')
            //     ->latest('id')
            //     ->first();
    
            // if (!$otp) {
            //     return response()->json([
            //         'status'  => false,
            //         'message' => 'Job cannot be completed without OTP verification.',
            //     ], 200);
            // }
    
            DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->update([
                    'job_status'   => 'completed',
                    'updated_at'   => $now
                ]);
                
            $driverFcm = $user->fcm_token;
            
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
    
    public function pastJob(Request $request)
    {
        try {
    
            $user = auth()->user();
    
            $jobs = DB::table('cus_job_temp as job')
                ->leftJoin(
                    'route_options as ro',
                    'ro.id',
                    '=',
                    'job.route_id'
                )
                ->leftJoin(
                    'user_register as ur',
                    'ur.id',
                    '=',
                    'job.user_id'
                )
                ->select(
                    'job.*',
    
                    'ro.summary as route_summary',
                    'ro.distance_meters',
                    'ro.duration_seconds',
                    'ro.toll_amount',
                    'ro.polyline',
    
                    'ur.name',
                    'ur.profile_img_url',
                    'ur.fcm_token'
                )
                ->where('job.user_id', $user->id)
                ->orderBy('job.pickup_date')
                ->where(function ($query) {

                    $query->whereIn(
                        'job.job_status',
                        ['cancelled', 'completed']
                    )
                
                    ->orWhere(function ($q) {
                
                        $q->where('job.job_status', 'created')
                
                          ->where(
                              'job.pickup_date',
                              '<',
                              now()
                          );
                    });
                })
                ->orderByDesc('job.id')
                ->get();
    
            $deep = env('DEEPLINK_CUSTOMER');
    
            $result = [];
    
            foreach ($jobs as $job) {
    
                $accepted = DB::table('invitations as i')
    
                    ->leftJoin(
                        'customer_register as c',
                        'c.id',
                        '=',
                        'i.inviter_id'
                    )
    
                    ->where('i.job_id', $job->id)
    
                    ->where('i.status', 'accepted')
    
                    ->select(
                        'c.id',
                        'c.name',
                        'c.profile_img_url',
                        'i.collectAmt',
                        DB::raw('(i.fare_breakdown->>"$.total_fare" - (i.fare_breakdown->>"$.com" + i.fare_breakdown->>"$.tax")) as driver_fare'),
                    )
    
                    ->get();
    
                $pending = DB::table('invitations')
    
                    ->where('job_id', $job->id)
    
                    ->where('status', 'pending')
    
                    ->count();
    
                $encryptedId = $this->encryptJobId(
                    $job->id
                );
    
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
    
                    'filled_seat' => $job->filled_seat,
    
                    'fare' => $job->fare,
    
                    'job_status' => $job->job_status == 'created' ? 'expired': $job->job_status,
    
                    'jb_type' => $job->confirm_status,
    
                    'isLock' => $job->isLock == 0
                        ? 'Unlocked'
                        : 'Locked',
    
                    'accepted_friends' => $accepted,
    
                    // 'pending_invitations' => $pending,
    
                    'deepLink' =>
                        $deep.'/carpool?jid='.$encryptedId,
    
                    'selected_route' => [
    
                        'route_id' => $job->route_id,
    
                        'summary' => $job->route_summary,
    
                        'distance_km' =>
                            $job->distance_meters
                                ? round(
                                    $job->distance_meters / 1000,
                                    2
                                )
                                : 0,
    
                        'duration_minutes' =>
                            $job->duration_seconds
                                ? round(
                                    $job->duration_seconds / 60
                                )
                                : 0,
    
                        'toll_amount' =>
                            $job->toll_amount ?? 0,
    
                        'polyline' =>
                            $job->polyline
                    ],
                ];
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Past jobs fetched successfully.',
                'data' => $result
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            \Log::error(
                'Past Jobs Error: '.$e->getMessage()
            );
    
            return response()->json([
                'status' => false,
                'message' => 'Server error',
            ], 500);
        }
    }
    
    public function updateTracking(Request $request)
    {
        try {
    
            $data = $request->validate([
                'job_id' => 'required',
                'status' => 'required|in:0,1'
            ]);
    
            $user = auth()->user();
            $now  = now();
    
            $job = DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                // ->where('user_id', $user->id)
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Job not found.',
                ], 404); 
            }
            
            $trackingStatus = ($data['status'] == 1) ? 'on' : 'off';
    
            DB::table('cus_job_temp')
                ->where('id', $data['job_id'])
                ->update([
                    'live_tracking' => $data['status'], 
                    'updated_at'    => $now
                ]);
                
            return response()->json([
                'status'  => true,
                'message' => "Tracking turned {$trackingStatus} successfully.",
                'data'    => null
            ], 200);
    
        } catch (ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}