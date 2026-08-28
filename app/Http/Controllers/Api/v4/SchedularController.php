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
use App\Services\PusherService;
use Razorpay\Api\Api;

class SchedularController extends Controller
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
    
    public function getDateWiseCheapestCopy(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'from'           => ['required', 'string'],
                'to'             => ['required', 'string'],
                'from_place_id'  => ['required', 'string'],
                'to_place_id'    => ['required', 'string'],
                'pickup'         => ['required', 'date'],
                'seat'           => ['required'],
            ]);
    
            $pickup     = \Carbon\Carbon::parse($validated['pickup'])->startOfDay();
            $endDate    = $pickup->copy()->addDays(6)->endOfDay();
            $seatInput  = $validated['seat'];
    
            // -------------------------
            // Seat Mapping (Optimized)
            // -------------------------
            $seaterMap = [
                'mini' => 'mini_four_seater',
                4      => 'four_seater',
                5      => 'four_seater',
                6      => 'six_seater',
                7      => 'seven_seater',
            ];
    
            $seater = $seaterMap[$seatInput] ?? 'four_seater';
    
            // -------------------------
            // Cached Distance Fetch
            // -------------------------
            $cached = DB::table('location_distance_web')
                ->where([
                    'from_place_id' => $validated['from_place_id'],
                    'to_place_id'   => $validated['to_place_id'],
                    'seater'        => $seater,
                ])
                ->select('toll_fare')
                ->first();
    
            $toll_fare = 0;
    
            if ($cached) {
                $toll_fare = $request->way_type != 'oneway'
                    ? $cached->toll_fare
                    : round($cached->toll_fare / 2);
            }
    
            $driver_bata = 0;
            $u_cash_point = auth()->user()->cash_points ?? 0;
    
            $fromWords = preg_split('/[\s,]+/', strtolower(trim($validated['from'])));
            $toWords   = preg_split('/[\s,]+/', strtolower(trim($validated['to'])));
    
         
            $rows = DB::table('schedule_dates as sd')
                ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
                ->where('sd.deletes', 0)
                ->whereNotNull('sd.dates_price')
                ->where('sd.dates_price', '!=', '')
                ->whereRaw("
                    CAST(
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                ur.vehicle_details,
                                '$.rc_details.response.vehicle_details.seat_capacity'
                            )
                        ) AS UNSIGNED
                    ) >= ?
                ", [$seatInput])
                ->when($fromWords, function ($q) use ($fromWords) {
                    foreach ($fromWords as $word) {
                        $q->whereRaw('LOWER(sd.from_place) LIKE ?', ["%{$word}%"]);
                    }
                })
                ->when($toWords, function ($q) use ($toWords) {
                    foreach ($toWords as $word) {
                        $q->whereRaw('LOWER(sd.to_place) LIKE ?', ["%{$word}%"]);
                    }
                })
                ->pluck('sd.dates_price');
    
            $cheapestByDate = [];
    
            foreach ($rows as $datesPrice) {
    
                $dates = json_decode($datesPrice, true);
                if (!$dates) continue;
    
                foreach ($dates as $datetime => $price) {
    
                    $date = substr($datetime, 0, 10);
    
                    if ($date < $pickup->toDateString() || $date > $endDate->toDateString()) {
                        continue;
                    }
    
                    if (!isset($cheapestByDate[$date]) || $price < $cheapestByDate[$date]) {
                        $cheapestByDate[$date] = (float) $price;
                    }
                }
            }
            
            // return $rows;
    
            ksort($cheapestByDate);
    
            $data = [];
    
            foreach ($cheapestByDate as $date => $fare) {
    
                $toll = round($toll_fare);
    
                $base = ($fare + $driver_bata);
    
                $com = round(($base + $toll) * 0.05);
    
                $discount = round(($base + $com) * 0.05);
                if($discount > 500){
                    $discount = 500;
                }
    
                if ($discount <= $u_cash_point && $u_cash_point != 0) {
    
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                } elseif ($u_cash_point != 0) {
    
                    $discount = ($u_cash_point - $discount);
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                } else {
    
                    $discount = 0;
                    $tax = round(($base + $com) * 0.05);
                }
    
                $total = round(($base + $com + $toll + $tax) - $discount);
                
                $isDiscount = ($u_cash_point != 0) ? 'yes' : 'no';
    
                $data[] = [
                    'date'         => $date,
                    'base_fare'    => $fare,
                    'driver_bata'  => $driver_bata,
                    'toll'         => $toll,
                    'com'   => $com,
                    'discount'     => $discount,
                    'isDiscount'     => $isDiscount,
                    'tax'          => $tax,
                    'total_fare' => $total,
                ];
            }
    
            return response()->json([
                'status' => true,
                'data'   => $data
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
    
    private function normalizeLocation($text)
    {
        $text = strtolower($text);
        $text = str_replace([',', '.', '-'], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    private function extractCityAliases($input, $aliases)
    {
        $ignore = ['tamil','nadu','india'];
    
        $normalized = $this->normalizeLocation($input);
        $words = explode(' ', $normalized);
    
        foreach ($words as $word) {
    
            if (in_array($word,$ignore)) {
                continue;
            }
    
            foreach ($aliases as $aliasList) {
    
                if (in_array($word,$aliasList)) {
                    return $aliasList;
                }
    
            }
    
            return [$word];
        }
    
        return [];
    }
    
    public function getDateWiseCheapest(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'from' => ['required','string'],
                'to' => ['required','string'],
                'from_place_id' => ['required','string'],
                'to_place_id' => ['required','string'],
                'pickup' => ['required','date'],
                'seat' => ['required'],
            ]);
    
            $pickup = \Carbon\Carbon::parse($validated['pickup'])->startOfDay();
            $endDate = $pickup->copy()->addDays(6)->endOfDay();
            $seatInput = $validated['seat'];
    
            $seaterMap = [
                'mini'=>'mini_four_seater',
                4=>'four_seater',
                5=>'four_seater',
                7=>'six_seater',
                8=>'seven_seater',
            ];
    
            $seater = $seaterMap[$seatInput] ?? 'four_seater';
    
            $cached = DB::table('location_distance_web')
            ->where([
                'from_place_id'=>$validated['from_place_id'],
                'to_place_id'=>$validated['to_place_id'],
                'seater'=>$seater
            ])
            ->select('toll_fare')
            ->first();
    
            $toll_fare = 0;
    
            if($cached){
                $toll_fare = $request->way_type != 'oneway'
                    ? $cached->toll_fare
                    : round($cached->toll_fare / 2);
            }
    
            $driver_bata = 0;
            $u_cash_point = auth()->user()->cash_points ?? 0;
    
            $cityAliases = [
                'trichy'=>['trichy','tiruchirappalli','thiruchirapalli'],
                'madurai'=>['madurai'],
                'rameswaram'=>['rameswaram','rameshwaram'],
                'salem'=>['salem'],
            ];
    
            $fromWords = $this->extractCityAliases($request->from,$cityAliases);
            $toWords = $this->extractCityAliases($request->to,$cityAliases);
    
            $rows = DB::table('schedule_dates as sd')
            ->join('user_register as ur','ur.id','=','sd.user_id')
            ->where('sd.deletes', 0)
            ->whereNotNull('sd.dates_price')
            ->where('sd.dates_price','!=','')
    
            ->whereRaw("
                CAST(
                    JSON_UNQUOTE(
                        JSON_EXTRACT(
                            ur.vehicle_details,
                            '$.rc_details.response.vehicle_details.seat_capacity'
                        )
                    ) AS UNSIGNED
                ) >= ?
            ",[$seatInput])
    
            ->where(function($query) use ($fromWords){
                foreach($fromWords as $word){
                    $query->orWhereRaw('LOWER(sd.from_place) LIKE ?',["%{$word}%"]);
                }
            })
    
            ->where(function($query) use ($toWords){
                foreach($toWords as $word){
                    $query->orWhereRaw('LOWER(sd.to_place) LIKE ?',["%{$word}%"]);
                }
            })
    
            ->pluck('sd.dates_price');
    
            $cheapestByDate = [];
            
            // return $rows;
    
            foreach($rows as $datesPrice){
    
                $dates = json_decode($datesPrice,true);
                if(!$dates) continue;
    
                foreach($dates as $datetime=>$price){
    
                    $date = substr($datetime,0,10);
    
                    if($date < $pickup->toDateString() || $date > $endDate->toDateString()){
                        continue;
                    }
                    
                    // if($date != $pickup->toDateString()){
                    //     continue;
                    // }
    
                    if(!isset($cheapestByDate[$date]) || $price < $cheapestByDate[$date]){
                        $cheapestByDate[$date] = (float)$price;
                    }
    
                }
    
            }
    
            ksort($cheapestByDate);
    
            $data = [];
            
            // return $cheapestByDate;
    
            foreach($cheapestByDate as $date=>$fare){
    
                $toll = round($toll_fare);
                $base = ($fare + $driver_bata);
                $com = round(($base + $toll) * 0.05);
                $discount = round(($base + $com) * 0.05);
                if($discount > 500){
                    $discount = 500;
                }
    
                if($discount <= $u_cash_point && $u_cash_point != 0){
    
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                }elseif($u_cash_point != 0){
    
                    $discount = ($u_cash_point - $discount);
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                }else{
    
                    $discount = 0;
                    $tax = round(($base + $com) * 0.05);
    
                }
    
                $total = round(($base + $com + $toll + $tax) - $discount);
    
                $isDiscount = ($u_cash_point != 0) ? 'yes':'no';
    
                $data[] = [
                    'date'=>$date,
                    'base_fare'=>$fare,
                    'driver_bata'=>$driver_bata,
                    'toll'=>$toll,
                    'com'=>$com,
                    'discount'=>$discount,
                    'isDiscount'=>$isDiscount,
                    'tax'=>$tax,
                    'total_fare'=>$fare + $com
                ];
    
            }
    
            return response()->json([
                'status'=>true,
                'data'=>$data
            ]);
    
        }
        catch(\Throwable $e){
    
            return response()->json([
                'status'=>false,
                'message'=>'Something went wrong'
            ],500);
    
        }
    }
    
    public function getDriversByDate(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'from'          => ['required','string'],
                'to'            => ['required','string'],
                'date'          => ['required','date'],
                'from_place_id' => ['required','string'],
                'to_place_id'   => ['required','string'],
                'way_type'      => ['required','string'],
                'seat'          => ['required','integer','min:1'],
                'day'           => ['nullable','integer','min:1']
            ]);
    
            $selectedDate = $validated['date'];
            $seatRequired = $validated['seat'];
            $wayType      = $validated['way_type'];
    
            $seaterMap = [
                'mini'=>'mini_four_seater',
                4=>'four_seater',
                5=>'four_seater',
                7=>'six_seater',
                8=>'seven_seater'
            ];
    
            $seater = $seaterMap[$seatRequired] ?? 'four_seater';
    
            $cached = DB::table('location_distance_web')
                ->where([
                    'from_place_id'=>$validated['from_place_id'],
                    'to_place_id'=>$validated['to_place_id'],
                    'seater'=>$seater
                ])
                ->select('toll_fare','distance')
                ->first();
    
            $toll_fare = 0;
            $distance = 0;
    
            if ($cached) {
    
                $toll_fare = $wayType != 'oneway'
                    ? $cached->toll_fare
                    : round($cached->toll_fare / 2);
    
                $distance = $wayType == 'roundtrip'
                    ? ($cached->distance * 2)
                    : $cached->distance;
            }
    
            $driver_bata = 0;
    
            if ($wayType == 'roundtrip') {
    
                $day = $validated['day'] ?? 0;
    
                if ($day > 1) {
    
                    $rule = DB::table('roundtrip_days')
                        ->where('day',$day)
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
                }
            }
    
            $cityAliases = [
                'trichy'=>['trichy','tiruchirappalli','thiruchirapalli'],
                'madurai'=>['madurai'],
                'rameswaram'=>['rameswaram','rameshwaram'],
                'salem'=>['salem']
            ];
    
            $fromWords = $this->extractCityAliases($validated['from'],$cityAliases);
            $toWords   = $this->extractCityAliases($validated['to'],$cityAliases);
    
            $seatInput = $request->seat ?? 0;
    
            $query = DB::table('schedule_dates as sd')
                ->join('user_register as ur','ur.id','=','sd.user_id')
                ->select(
                    'sd.id as sch_id',
                    'ur.id as driver_id',
                    'ur.name',
                    'ur.mobile',
                    DB::raw("ur.vehicle_details->'$.rc_details' as vehicle_details"),
                    DB::raw("ur.vehicle_details->'$.vehicle' as vehicle_images"),
                    'sd.from_place',
                    'sd.to_place',
                    'sd.dates_price'
                )
                ->where('sd.deletes',0)
                ->whereNotNull('sd.dates_price')
                ->where('sd.dates_price','LIKE',"%{$selectedDate}%")
    
                ->whereRaw("
                    CAST(
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                ur.vehicle_details,
                                '$.rc_details.response.vehicle_details.seat_capacity'
                            )
                        ) AS UNSIGNED
                    ) >= ?
                ",[$seatInput])
    
                ->where(function ($query) use ($fromWords) {
                    foreach ($fromWords as $word) {
                        $query->orWhereRaw('LOWER(sd.from_place) LIKE ?',["%{$word}%"]);
                    }
                })
    
                ->where(function ($query) use ($toWords) {
                    foreach ($toWords as $word) {
                        $query->orWhereRaw('LOWER(sd.to_place) LIKE ?',["%{$word}%"]);
                    }
                });
    
            $drivers = $query->paginate(10);
    
            $u_cash_point = auth()->user()->cash_points ?? 0;
            $tollRounded = round($toll_fare);
    
            $drivers->getCollection()->transform(function ($driver) use ($selectedDate,$driver_bata,$u_cash_point,$tollRounded) {
    
                $fare = null;
    
                if (!empty($driver->dates_price)) {
    
                    $dates = json_decode($driver->dates_price,true);
    
                    if (is_array($dates)) {
                        foreach ($dates as $datetime=>$p) {
                            if (strncmp($datetime,$selectedDate,10)==0) {
                                $fare = (float)$p;
                                break;
                            }
                        }
                    }
                }
    
                if ($fare == null) {
                    unset($driver->dates_price);
                    return $driver;
                }
    
                $toll = $tollRounded;
    
                $base = ($fare + $driver_bata);
    
                $com = round(($base + $toll) * 0.05);
    
                $discount = round(($base + $com) * 0.05);
                if($discount > 500){
                    $discount = 500;
                }
    
                if ($discount <= $u_cash_point && $u_cash_point != 0) {
    
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                } elseif ($u_cash_point != 0) {
    
                    $discount = ($u_cash_point - $discount);
                    $tax = round((($base + $com) - $discount) * 0.05);
    
                } else {
    
                    $discount = 0;
                    $tax = round(($base + $com) * 0.05);
                }
    
                $total = round(($base + $com + $toll + $tax) - $discount);
    
                $driver->base_fare = $fare;
                $driver->driver_bata = $driver_bata;
                $driver->toll = $toll;
                $driver->commission = $com;
                $driver->discount = $discount;
                $driver->tax = $tax;
                $driver->total_amount = $fare + $com;
    
                $driver->vehicle_details = json_decode($driver->vehicle_details);
                $driver->vehicle_images = json_decode($driver->vehicle_images);
    
                unset($driver->dates_price);
    
                return $driver;
    
            });
    
            $drivers->setCollection(
                $drivers->getCollection()->sortBy('total_amount')->values()
            );
    
            return response()->json($drivers);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'=>false,
                'message'=>'Something went wrong'
            ],500);
        }
    }
    
    public function checkAvailability(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'job_no' => ['required','string'],
                's_ids'  => ['required','array'],
                'date'   => ['required','date']
            ]);
    
            $jobNo = $validated['job_no'];
            $sIds  = $validated['s_ids'];
            $date  = $validated['date'];
    
            $job = DB::table('cus_job_temp')
                ->where('job_no', $jobNo)
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found'
                ], 404);
            }
            
            $requiredSeats = $job->pass_count == 'mini' ? 4 : (int) $job->pass_count;
    
            $drivers = DB::table('schedule_dates as sd')
                ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
                ->select(
                    'ur.id',
                    'sd.id as sch_id',
    
                    DB::raw("
                        CAST(
                            JSON_UNQUOTE(
                                JSON_EXTRACT(
                                    ur.vehicle_details,
                                    '$.rc_details.response.vehicle_details.seat_capacity'
                                )
                            ) AS UNSIGNED
                        ) as seat_capacity
                    "),
    
                    DB::raw("
                        JSON_UNQUOTE(
                            JSON_EXTRACT(
                                ur.vehicle_details,
                                '$.vehicle_details.type'
                            )
                        ) as vehicle_type
                    ")
                )
                ->whereIn('sd.id', $sIds)
                ->get();
    
            if ($drivers->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No drivers found'
                ], 404);
            }
    
            $eligibleDrivers = $drivers->filter(function ($driver) use ($requiredSeats) {
    
                $seat_cap = 0;
    
                if ($driver->seat_capacity) {
                    $seat_cap = (int) $driver->seat_capacity;
                } elseif ($driver->vehicle_type) {
                    $seat_cap = $driver->vehicle_type == 'Mini'
                        ? 4
                        : (int) $driver->vehicle_type;
                }
    
                return $seat_cap >= $requiredSeats;
    
            })->values();
    
            if ($eligibleDrivers->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No eligible drivers for this ride'
                ], 200);
            }
    

            $statusData = json_decode($job->sch_status, true) ?? [];
    
            if (!isset($statusData[$date])) {
                $statusData[$date] = [];
            }
    
            $now = now()->toISOString();
            
            $driverIds = [];
            
            // \Log::info('Testing schedule...: ', [$eligibleDrivers]);
    
            foreach ($eligibleDrivers as $driver) {
                
                if (isset($statusData[$date][$driver->id])) {
                    // unset($eligibleDrivers[$key]);
                    continue;
                }
                $driverIds[] = $driver->id;
    
                $statusData[$date][$driver->id] = [
                    'status' => 'inreview',
                    'sch_id' => $driver->sch_id,
                    'updated_at' => $now
                ];
            }
            
            // $driverIds = $eligibleDrivers->pluck('id')->toArray();
            // \Log::info('Testing schedule...: ', [$driverIds]);
            
            $fcmTokens = count($driverIds) > 0 ? $this->getFcm($driverIds, null, null, null) : null;
    
            if (!empty($fcmTokens)) {
    
                $accessToken = $this->getAccessToken();
    
                if ($accessToken) {
    
                    foreach ($fcmTokens as $token) {
    
                        $this->sendFCM(
                            $accessToken,
                            $token,
                            '🎯 Job Waiting for You',
                            'Hurry up! Please confirm your availability within 30 seconds to take this job. 🚕',
                            [
                                'job_no' => $jobNo,
                                'job_id' => $job->id,
                                'type'   => 'new_job_notification',
                                'pickup' => $date,
                                'sch_id' => json_encode($sIds),
                                'action' => 'schedule_popup'
                            ]
                        );
                    }
                }
            }
            
            $accessToken = $this->getAccessToken2();
            
            if ($accessToken) {
    
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount2['project_id'],
                    $accessToken
                );
    
                $firebase->updateScheduleStatus($jobNo, $statusData);
            }
            
            DB::table('cus_job_temp')
                ->where('job_no', $jobNo)
                ->update([
                    'sch_status' => json_encode($statusData)
                ]);
    
            return response()->json([
                'status' => true,
                'message' => 'Waiting for driver availability'
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
    
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}