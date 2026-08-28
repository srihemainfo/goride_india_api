<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WebsiteBookingController extends Controller
{
    public $serviceAccountPath;
    public $serviceAccount;
    public $serviceAccountPath2;
    public $serviceAccount2;

    public function __construct()
    {
        $this->serviceAccountPath = storage_path('app/firebase/firebase-config-customer.json');
        $this->serviceAccountPath2 = storage_path('app/firebase/firebase-config-customer-schedule.json');

        if (file_exists($this->serviceAccountPath)) {
            $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        }
        if (file_exists($this->serviceAccountPath2)) {
            $this->serviceAccount2 = json_decode(file_get_contents($this->serviceAccountPath2), true);
        }
    }

    public function getAccessToken()
    {
        if (!$this->serviceAccount) return null;
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $claimSet = [
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $this->serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600
        ];
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput = "$header.$claimSetEncoded";
        openssl_sign($signatureInput, $signature, openssl_pkey_get_private($this->serviceAccount['private_key']), OPENSSL_ALGO_SHA256);
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $postFields = http_build_query(['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $jwt]);
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
        if (!$this->serviceAccount2) return null;
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $claimSet = [
            'iss' => $this->serviceAccount2['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $this->serviceAccount2['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600
        ];
        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput = "$header.$claimSetEncoded";
        openssl_sign($signatureInput, $signature, openssl_pkey_get_private($this->serviceAccount2['private_key']), OPENSSL_ALGO_SHA256);
        $jwt = "$signatureInput." . str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $postFields = http_build_query(['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $jwt]);
        $ch = curl_init($this->serviceAccount2['token_uri']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        return $responseData['access_token'] ?? null;
    }

    private function normalizeSearch($text)
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $text));
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
        $ignore = ['tamil', 'nadu', 'india'];
        $normalized = $this->normalizeLocation($input);
        $words = explode(' ', $normalized);
        foreach ($words as $word) {
            if (in_array($word, $ignore)) continue;
            foreach ($aliases as $aliasList) {
                if (in_array($word, $aliasList)) return $aliasList;
            }
            return [$word];
        }
        return [];
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
            if (!empty($ids)) $query->whereIn('id', $ids);
        } else {
            $excludeId = $us_id;
            if (!empty($excludeId)) $query->where('id', '!=', $excludeId);
        }

        if (!empty($loc)) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(prefered_location, '$.location')) LIKE ?", ["%{$loc}%"]);
        }

        if ($cab_seat !== null) {
            $query->whereRaw("CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(vehicle_details, '$.type')), '0') AS UNSIGNED) >= ?", [(int) $cab_seat]);
        }

        return $query->pluck('fcm_token')->filter()->unique()->values()->toArray();
    }

    public function sendFCM($accessToken, $fcmToken, $title, $body, $data = [])
    {
        if (!$accessToken || !$fcmToken) return false;
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
                'notification' => ['title' => $title, 'body' => $body],
                'android' => [
                    'priority' => 'high',
                    'ttl' => '86400s',
                    'notification' => ['channel_id' => 'new_job_channel', 'sound' => 'custom_notification', 'color' => '#FF6B35']
                ],
                'apns' => [
                    'headers' => ['apns-priority' => '10', 'apns-push-type' => 'alert'],
                    'payload' => ['aps' => ['alert' => ['title' => $title, 'body' => $body], 'sound' => 'custom_notification.wav', 'badge' => 1]]
                ],
                'data' => $stringData
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken, 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public function GoogleLocations(Request $request)
    {
        try {
            $search = trim($request->search);

            if (empty($search)) {
                return response()->json([
                    'status'  => 200,
                    'data'    => [],
                    'message' => 'Search keyword is required.'
                ]);
            }

            $results = DB::table('districts')
                ->where('name', 'LIKE', "%{$search}%")
                ->select('id', 'name', 'latitude', 'longitude', 'place_id')
                ->get();

            return response()->json([
                'status'  => 200,
                'data'    => $results,
                'message' => $results->isEmpty() ? 'No locations found.' : 'Location retrieved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error'  => $e->getMessage()
            ]);
        }
    }

    public function get_all_jobs(Request $request)
    {
        $cityAliases = [
            'trichy' => ['trichy', 'tiruchirappalli', 'thiruchirapalli'],
            'madurai' => ['madurai'],
            'rameswaram' => ['rameswaram', 'rameshwaram'],
            'salem' => ['salem']
        ];
        $fromWords = $this->extractCityAliases($request->from, $cityAliases);
        $toWords = $this->extractCityAliases($request->to, $cityAliases);
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $now = Carbon::now();

        $pickupTime = $request->pickup_time ?? '00:00';
        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $pickupTime, $matches)) {
            $hour = (int)$matches[1];
            $minute = $matches[2];
            $ampm = strtoupper($matches[3]);
            if ($ampm == 'PM' && $hour != 12) $hour += 12;
            if ($ampm == 'AM' && $hour == 12) $hour = 0;
            $pickupTime24 = sprintf('%02d:%02d', $hour, $minute);
        } else {
            $pickupTime24 = $pickupTime;
        }
        $requestedDateTime = Carbon::parse($date . ' ' . $pickupTime24);

        $drCarpoolQuery = DB::table('cus_job_temp as j')
            ->join('user_register as ur', 'j.user_id', '=', 'ur.id')
            ->leftJoin('kyc_details as kyc', 'j.user_id', '=', 'kyc.user_id')
            ->where('j.global_type', 'dr_carpool')
            ->where('j.isLock', 0)
            ->where('j.confirm_status', 1)
            ->where('j.deletes', '0')
            ->whereNotIn('j.job_status', ['completed', 'cancelled', 'started'])
            ->where('j.pickup_date', '>=', $now);

        if (!empty($fromWords)) {
            $drCarpoolQuery->where(function ($q) use ($fromWords) {
                foreach ($fromWords as $word) {
                    $q->orWhereRaw('LOWER(j.from_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        if (!empty($toWords)) {
            $drCarpoolQuery->where(function ($q) use ($toWords) {
                foreach ($toWords as $word) {
                    $q->orWhereRaw('LOWER(j.to_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        $drCarpoolJobs = $drCarpoolQuery->select('j.*', 'ur.name', 'ur.profile_img_url', 'ur.profile_percentage as rating', 'ur.vehicle_details as embedded_vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

        $scheduleQuery = DB::table('schedule_dates as sd')
            ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
            ->leftJoin('kyc_details as kyc', 'sd.user_id', '=', 'kyc.user_id')
            ->where('sd.deletes', 0)
            ->whereNotNull('sd.dates_price');

        if (!empty($fromWords)) {
            $scheduleQuery->where(function ($q) use ($fromWords) {
                foreach ($fromWords as $word) {
                    $q->orWhereRaw('LOWER(sd.from_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        if (!empty($toWords)) {
            $scheduleQuery->where(function ($q) use ($toWords) {
                foreach ($toWords as $word) {
                    $q->orWhereRaw('LOWER(sd.to_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        $scheduleJobs = $scheduleQuery->select('sd.*', 'ur.name', 'ur.profile_percentage as rating', 'ur.vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

        $jobs = [];
        $faresByDate = [];
        $startDate = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            $faresByDate[$startDate->copy()->addDays($i)->format('Y-m-d')] = 999999;
        }

        foreach ($drCarpoolJobs as $cj) {
            $t = Carbon::parse($cj->pickup_date);
            if ($t->isPast()) {
                continue;
            }
            $dStr = $t->format('Y-m-d');
            if ($dStr === $date && $t->lt($requestedDateTime)) {
                continue;
            }
            $fare = (float)$cj->fare;
            if (isset($faresByDate[$dStr]) && $fare < $faresByDate[$dStr]) {
                $faresByDate[$dStr] = $fare;
            }

            if ($dStr === $date) {
                $parsedVeh = null;
                $carName = 'Vehicle';
                if (!empty($cj->embedded_vehicle_details)) {
                    $parsedVeh = json_decode($cj->embedded_vehicle_details, true);
                    $carName = $parsedVeh['type'] ?? ($parsedVeh['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
                }
                $offeredSeats = (is_numeric($cj->pass_count) && (int)$cj->pass_count > 0) ? (int)$cj->pass_count : 4;
                $alreadyFilled = (isset($cj->filled_seat) && is_numeric($cj->filled_seat)) ? (int)$cj->filled_seat : 0;
                $remainingSeats = max(0, $offeredSeats - $alreadyFilled);

                $jobs[] = [
                    'id' => 'cp_' . $cj->id,
                    'db_id' => $cj->id,
                    'job_no' => null,
                    'carName' => $carName,
                    'name' => $cj->name ?? 'User',
                    'rating' => $cj->rating ? number_format($cj->rating / 20, 1) : '4.5',
                    'rides' => rand(10, 50),
                    'exp' => $cj->exp,
                    'selfie_url' => $cj->selfie_url,
                    'dep' => $t->format('H:i'),
                    'arr' => $t->copy()->addHours(3)->format('H:i'),
                    'dur' => '3h 0m',
                    'fareAdj' => 0,
                    'perSeatFare' => $fare,
                    'nextDay' => false,
                    'rideType' => 'carpool',
                    'maxSeats' => $remainingSeats > 0 ? $remainingSeats : 1,
                    'seats' => 1,
                    'full' => $remainingSeats <= 0,
                    'distance' => '200 km',
                    'cpDriverStart' => $cj->from_place,
                    'cpDriverEnd' => $cj->to_place,
                    'cpFrom' => $cj->from_place,
                    'cpTo' => $cj->to_place,
                    'vehicle_meta' => $parsedVeh,
                    'req_status' => null
                ];
            }
        }

        foreach ($scheduleJobs as $sj) {
            $dp = json_decode($sj->dates_price, true);
            $vDetails = json_decode($sj->vehicle_details, true);
            $carName = $vDetails['type'] ?? ($vDetails['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
            $seatCap = $vDetails['rc_details']['response']['vehicle_details']['seat_capacity'] ?? 4;

            if (is_array($dp)) {
                foreach ($dp as $dt => $price) {
                    $jobDateTime = Carbon::parse($dt);
                    if ($jobDateTime->isPast()) {
                        continue;
                    }
                    $dStr = $jobDateTime->format('Y-m-d');
                    if ($dStr === $date && $jobDateTime->lt($requestedDateTime)) {
                        continue;
                    }
                    $fPrice = (float)$price;
                    if (isset($faresByDate[$dStr]) && $fPrice < $faresByDate[$dStr]) {
                        $faresByDate[$dStr] = $fPrice;
                    }

                    if ($dStr === $date) {
                        $jobs[] = [
                            'id' => 'sc_' . $sj->id,
                            'db_id' => $sj->id,
                            'job_no' => null,
                            'carName' => $carName,
                            'name' => $sj->name ?? 'Driver',
                            'rating' => $sj->rating ? number_format($sj->rating / 20, 1) : '4.8',
                            'rides' => rand(50, 200),
                            'exp' => $sj->exp,
                            'selfie_url' => $sj->selfie_url,
                            'dep' => substr($dt, 11, 5) ?: '10:00',
                            'arr' => 'TBD',
                            'dur' => '4h 0m',
                            'fareAdj' => $fPrice,
                            'perSeatFare' => $fPrice,
                            'nextDay' => false,
                            'rideType' => 'private',
                            'maxSeats' => $seatCap,
                            'seats' => 1,
                            'full' => false,
                            'distance' => '200 km',
                            'cpDriverStart' => $sj->from_place,
                            'cpDriverEnd' => $sj->to_place,
                            'cpFrom' => $sj->from_place,
                            'cpTo' => $sj->to_place,
                            'vehicle_meta' => $vDetails,
                            'req_status' => null
                        ];
                    }
                }
            }
        }

        $dateStrip = [];
        foreach ($faresByDate as $k => $v) {
            if ($v != 999999) {
                $dateStrip[$k] = $v;
            }
        }

        return response()->json(['status' => true, 'data' => ['jobs' => $jobs, 'dates' => $dateStrip]]);
    }

    // public function get_all_jobs_after_login(Request $request)
    // {
    //     try {
    //         $user = auth('sanctum')->user();
    //         $checkHeaderToken = request()->bearerToken();

    //         if (!$user && $checkHeaderToken) {
    //             $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($checkHeaderToken);
    //             if ($tokenModel) {
    //                 $user = DB::table('customer_register')->where('id', $tokenModel->tokenable_id)->first();
    //             }
    //         }

    //         if (!$user) {
    //             return response()->json(['status' => false, 'message' => 'Authentication Required'], 401);
    //         }

    //         $cityAliases = [
    //             'trichy' => ['trichy', 'tiruchirappalli', 'thiruchirapalli'],
    //             'madurai' => ['madurai'],
    //             'rameswaram' => ['rameswaram', 'rameshwaram'],
    //             'salem' => ['salem']
    //         ];
            
    //         $fromInput = $request->input('from', '');
    //         $toInput = $request->input('to', '');
    //         $dateInput = $request->input('date', now()->format('Y-m-d'));
            
    //         $fromWords = $this->extractCityAliases($fromInput, $cityAliases);
    //         $toWords = $this->extractCityAliases($toInput, $cityAliases);
    //         $date = Carbon::parse($dateInput)->format('Y-m-d');
    //         $now = Carbon::now();

    //         $pickupTime = $request->input('pickup_time', '00:00');
    //         if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $pickupTime, $matches)) {
    //             $hour = (int)$matches[1];
    //             $minute = $matches[2];
    //             $ampm = strtoupper($matches[3]);
    //             if ($ampm == 'PM' && $hour != 12) $hour += 12;
    //             if ($ampm == 'AM' && $hour == 12) $hour = 0;
    //             $pickupTime24 = sprintf('%02d:%02d', $hour, $minute);
    //         } else {
    //             $pickupTime24 = $pickupTime;
    //         }
    //         $requestedDateTime = Carbon::parse($date . ' ' . $pickupTime24);

    //         $drCarpoolQuery = DB::table('cus_job_temp as j')
    //             ->join('user_register as ur', 'j.user_id', '=', 'ur.id')
    //             ->leftJoin('kyc_details as kyc', 'j.user_id', '=', 'kyc.user_id')
    //             ->where('j.global_type', 'dr_carpool')
    //             ->where('j.isLock', 0)
    //             ->where('j.confirm_status', 1)
    //             ->where('j.deletes', '0')
    //             ->whereNotIn('j.job_status', ['completed', 'cancelled', 'started'])
    //             ->where('j.pickup_date', '>=', $now);

    //         if (!empty($fromWords)) {
    //             $drCarpoolQuery->where(function ($q) use ($fromWords) {
    //                 foreach ($fromWords as $word) {
    //                     $q->orWhereRaw('LOWER(j.from_place) LIKE ?', ["%{$word}%"]);
    //                 }
    //             });
    //         }
    //         if (!empty($toWords)) {
    //             $drCarpoolQuery->where(function ($q) use ($toWords) {
    //                 foreach ($toWords as $word) {
    //                     $q->orWhereRaw('LOWER(j.to_place) LIKE ?', ["%{$word}%"]);
    //                 }
    //             });
    //         }
    //         $drCarpoolJobs = $drCarpoolQuery->select('j.*', 'ur.name', 'ur.profile_img_url', 'ur.profile_percentage as rating', 'ur.vehicle_details as embedded_vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

    //         $scheduleQuery = DB::table('schedule_dates as sd')
    //             ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
    //             ->leftJoin('kyc_details as kyc', 'sd.user_id', '=', 'kyc.user_id')
    //             ->where('sd.deletes', 0)
    //             ->whereNotNull('sd.dates_price');

    //         if (!empty($fromWords)) {
    //             $scheduleQuery->where(function ($q) use ($fromWords) {
    //                 foreach ($fromWords as $word) {
    //                     $q->orWhereRaw('LOWER(sd.from_place) LIKE ?', ["%{$word}%"]);
    //                 }
    //             });
    //         }
    //         if (!empty($toWords)) {
    //             $scheduleQuery->where(function ($q) use ($toWords) {
    //                 foreach ($toWords as $word) {
    //                     $q->orWhereRaw('LOWER(sd.to_place) LIKE ?', ["%{$word}%"]);
    //                 }
    //             });
    //         }
    //         $scheduleJobs = $scheduleQuery->select('sd.*', 'ur.name', 'ur.profile_percentage as rating', 'ur.vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

    //         $userInvitations = DB::table('invitations')
    //             ->where('inviter_id', $user->id)
    //             ->where('type', 'join')
    //             ->where('created_at', '>=', now()->subDays(2))
    //             ->get()
    //             ->keyBy('job_id');

    //         $userPrivateReqs = DB::table('cus_job_temp')
    //             ->where('user_id', $user->id)
    //             ->where('global_type', 'schedule')
    //             ->where('created_at', '>=', now()->subDays(2))
    //             ->whereIn('job_status', ['created', 'pending', 'inreview', 'accepted', 'rejected'])
    //             ->get();

    //         $jobs = [];
    //         $faresByDate = [];
    //         $startDate = Carbon::today();
    //         for ($i = 0; $i < 7; $i++) {
    //             $faresByDate[$startDate->copy()->addDays($i)->format('Y-m-d')] = 999999;
    //         }

    //         foreach ($drCarpoolJobs as $cj) {
    //             $t = Carbon::parse($cj->pickup_date);
    //             if ($t->isPast()) {
    //                 continue;
    //             }
    //             $dStr = $t->format('Y-m-d');
    //             if ($dStr === $date && $t->lt($requestedDateTime)) {
    //                 continue;
    //             }
    //             $fare = (float)$cj->fare;
    //             if (isset($faresByDate[$dStr]) && $fare < $faresByDate[$dStr]) {
    //                 $faresByDate[$dStr] = $fare;
    //             }

    //             if ($dStr === $date) {
    //                 $parsedVeh = null;
    //                 $carName = 'Vehicle';
    //                 if (!empty($cj->embedded_vehicle_details)) {
    //                     $parsedVeh = json_decode($cj->embedded_vehicle_details, true);
    //                     if (is_array($parsedVeh)) {
    //                         $carName = $parsedVeh['type'] ?? ($parsedVeh['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
    //                     }
    //                 }
    //                 $offeredSeats = (is_numeric($cj->pass_count) && (int)$cj->pass_count > 0) ? (int)$cj->pass_count : 4;
    //                 $alreadyFilled = (isset($cj->filled_seat) && is_numeric($cj->filled_seat)) ? (int)$cj->filled_seat : 0;
    //                 $remainingSeats = max(0, $offeredSeats - $alreadyFilled);

    //                 $req_status = null;
    //                 if ($userInvitations->has($cj->id)) {
    //                     $invite = $userInvitations->get($cj->id);
    //                     if ($invite->status === 'accept' || $invite->status === 'accepted') {
    //                         $req_status = 'accepted';
    //                     } elseif ($invite->status === 'reject' || $invite->status === 'rejected') {
    //                         $req_status = 'rejected';
    //                     } else {
    //                         $req_status = 'requested';
    //                     }
    //                 }

    //                 $jobs[] = [
    //                     'id' => 'cp_' . $cj->id,
    //                     'db_id' => $cj->id,
    //                     'job_no' => null,
    //                     'carName' => $carName,
    //                     'name' => $cj->name ?? 'User',
    //                     'rating' => $cj->rating ? number_format($cj->rating / 20, 1) : '4.5',
    //                     'rides' => rand(10, 50),
    //                     'exp' => $cj->exp,
    //                     'selfie_url' => $cj->selfie_url,
    //                     'dep' => $t->format('H:i'),
    //                     'arr' => $t->copy()->addHours(3)->format('H:i'),
    //                     'dur' => '3h 0m',
    //                     'fareAdj' => 0,
    //                     'perSeatFare' => $fare,
    //                     'nextDay' => false,
    //                     'rideType' => 'carpool',
    //                     'maxSeats' => $remainingSeats > 0 ? $remainingSeats : 1,
    //                     'seats' => 1,
    //                     'full' => $remainingSeats <= 0,
    //                     'distance' => '200 km',
    //                     'cpDriverStart' => $cj->from_place,
    //                     'cpDriverEnd' => $cj->to_place,
    //                     'cpFrom' => $cj->from_place,
    //                     'cpTo' => $cj->to_place,
    //                     'vehicle_meta' => $parsedVeh,
    //                     'req_status' => $req_status
    //                 ];
    //             }
    //         }

    //         foreach ($scheduleJobs as $sj) {
    //             $dp = json_decode($sj->dates_price, true);
    //             $vDetails = json_decode($sj->vehicle_details, true);
                
    //             $carName = 'Vehicle';
    //             $seatCap = 4;
    //             if (is_array($vDetails)) {
    //                 $carName = $vDetails['type'] ?? ($vDetails['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
    //                 $seatCap = $vDetails['rc_details']['response']['vehicle_details']['seat_capacity'] ?? 4;
    //             }

    //             if (is_array($dp)) {
    //                 foreach ($dp as $dt => $price) {
    //                     $jobDateTime = Carbon::parse($dt);
    //                     if ($jobDateTime->isPast()) {
    //                         continue;
    //                     }
    //                     $dStr = $jobDateTime->format('Y-m-d');
    //                     if ($dStr === $date && $jobDateTime->lt($requestedDateTime)) {
    //                         continue;
    //                     }
    //                     $fPrice = (float)$price;
    //                     if (isset($faresByDate[$dStr]) && $fPrice < $faresByDate[$dStr]) {
    //                         $faresByDate[$dStr] = $fPrice;
    //                     }

    //                     if ($dStr === $date) {
    //                         $req_status = null;
    //                         $job_no = null;

    //                         foreach ($userPrivateReqs as $upr) {
    //                             $schStatusStr = (string) ($upr->sch_status ?? '');
                                
    //                             if (str_contains($schStatusStr, '"sch_id":' . $sj->id) || str_contains($schStatusStr, '"sch_id":"' . $sj->id . '"')) {
    //                                 $job_no = $upr->job_no;
    //                                 $schJson = json_decode($upr->sch_status, true);
    //                                 if (is_array($schJson)) {
    //                                     foreach ($schJson as $dateKey => $drivers) {
    //                                         foreach ($drivers as $d_id => $meta) {
    //                                             if (isset($meta['sch_id']) && $meta['sch_id'] == $sj->id) {
    //                                                 $req_status = $meta['status'] ?? null;
    //                                                 break 3;
    //                                             }
    //                                         }
    //                                     }
    //                                 }
    //                                 if (!$req_status) {
    //                                     $req_status = in_array($upr->job_status, ['created', 'pending', 'inreview']) ? 'requested' : $upr->job_status;
    //                                 }
    //                                 break;
    //                             }
    //                         }

    //                         $jobs[] = [
    //                             'id' => 'sc_' . $sj->id,
    //                             'db_id' => $sj->id,
    //                             'job_no' => $job_no,
    //                             'carName' => $carName,
    //                             'name' => $sj->name ?? 'Driver',
    //                             'rating' => $sj->rating ? number_format($sj->rating / 20, 1) : '4.8',
    //                             'rides' => rand(50, 200),
    //                             'exp' => $sj->exp,
    //                             'selfie_url' => $sj->selfie_url,
    //                             'dep' => substr($dt, 11, 5) ?: '10:00',
    //                             'arr' => 'TBD',
    //                             'dur' => '4h 0m',
    //                             'fareAdj' => $fPrice,
    //                             'perSeatFare' => $fPrice,
    //                             'nextDay' => false,
    //                             'rideType' => 'private',
    //                             'maxSeats' => $seatCap,
    //                             'seats' => 1,
    //                             'full' => false,
    //                             'distance' => '200 km',
    //                             'cpDriverStart' => $sj->from_place,
    //                             'cpDriverEnd' => $sj->to_place,
    //                             'cpFrom' => $sj->from_place,
    //                             'cpTo' => $sj->to_place,
    //                             'vehicle_meta' => is_array($vDetails) ? $vDetails : null,
    //                             'req_status' => $req_status
    //                         ];
    //                     }
    //                 }
    //             }
    //         }

    //         $dateStrip = [];
    //         foreach ($faresByDate as $k => $v) {
    //             if ($v != 999999) {
    //                 $dateStrip[$k] = $v;
    //             }
    //         }

    //         return response()->json(['status' => true, 'data' => ['jobs' => $jobs, 'dates' => $dateStrip]]);
            
    //     } catch (\Throwable $e) {
    //         // Write the error directly to storage/logs/laravel.log
    //         \Illuminate\Support\Facades\Log::error('GoRide API Error in get_all_jobs_after_login: ' . $e->getMessage(), [
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         // Still return it to the frontend so you can see it in DevTools
    //         return response()->json([
    //             'status' => false, 
    //             'message' => 'Line ' . $e->getLine() . ': ' . $e->getMessage(),
    //             'file' => basename($e->getFile())
    //         ], 500);
    //     }
    // }
    
    public function get_all_jobs_after_login(Request $request)
{
    try {
        $user = auth('sanctum')->user();
        $checkHeaderToken = request()->bearerToken();

        if (!$user && $checkHeaderToken) {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($checkHeaderToken);
            if ($tokenModel) {
                $user = DB::table('customer_register')->where('id', $tokenModel->tokenable_id)->first();
            }
        }

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Authentication Required'], 401);
        }

        $cityAliases = [
            'trichy' => ['trichy', 'tiruchirappalli', 'thiruchirapalli'],
            'madurai' => ['madurai'],
            'rameswaram' => ['rameswaram', 'rameshwaram'],
            'salem' => ['salem']
        ];
        
        $fromInput = $request->input('from', '');
        $toInput = $request->input('to', '');
        $dateInput = $request->input('date', now()->format('Y-m-d'));
        
        $fromWords = $this->extractCityAliases($fromInput, $cityAliases);
        $toWords = $this->extractCityAliases($toInput, $cityAliases);
        $date = Carbon::parse($dateInput)->format('Y-m-d');
        $now = Carbon::now();

        $pickupTime = $request->input('pickup_time', '00:00');
        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $pickupTime, $matches)) {
            $hour = (int)$matches[1];
            $minute = $matches[2];
            $ampm = strtoupper($matches[3]);
            if ($ampm == 'PM' && $hour != 12) $hour += 12;
            if ($ampm == 'AM' && $hour == 12) $hour = 0;
            $pickupTime24 = sprintf('%02d:%02d', $hour, $minute);
        } else {
            $pickupTime24 = $pickupTime;
        }
        $requestedDateTime = Carbon::parse($date . ' ' . $pickupTime24);

        $drCarpoolQuery = DB::table('cus_job_temp as j')
            ->join('user_register as ur', 'j.user_id', '=', 'ur.id')
            ->leftJoin('kyc_details as kyc', 'j.user_id', '=', 'kyc.user_id')
            ->where('j.global_type', 'dr_carpool')
            ->where('j.isLock', 0)
            ->where('j.confirm_status', 1)
            ->where('j.deletes', '0')
            ->whereNotIn('j.job_status', ['completed', 'cancelled', 'started'])
            ->where('j.pickup_date', '>=', $now);

        if (!empty($fromWords)) {
            $drCarpoolQuery->where(function ($q) use ($fromWords) {
                foreach ($fromWords as $word) {
                    $q->orWhereRaw('LOWER(j.from_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        if (!empty($toWords)) {
            $drCarpoolQuery->where(function ($q) use ($toWords) {
                foreach ($toWords as $word) {
                    $q->orWhereRaw('LOWER(j.to_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        $drCarpoolJobs = $drCarpoolQuery->select('j.*', 'ur.name', 'ur.profile_img_url', 'ur.profile_percentage as rating', 'ur.vehicle_details as embedded_vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

        $scheduleQuery = DB::table('schedule_dates as sd')
            ->join('user_register as ur', 'ur.id', '=', 'sd.user_id')
            ->leftJoin('kyc_details as kyc', 'sd.user_id', '=', 'kyc.user_id')
            ->where('sd.deletes', 0)
            ->whereNotNull('sd.dates_price');

        if (!empty($fromWords)) {
            $scheduleQuery->where(function ($q) use ($fromWords) {
                foreach ($fromWords as $word) {
                    $q->orWhereRaw('LOWER(sd.from_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        if (!empty($toWords)) {
            $scheduleQuery->where(function ($q) use ($toWords) {
                foreach ($toWords as $word) {
                    $q->orWhereRaw('LOWER(sd.to_place) LIKE ?', ["%{$word}%"]);
                }
            });
        }
        $scheduleJobs = $scheduleQuery->select('sd.*', 'ur.name', 'ur.profile_percentage as rating', 'ur.vehicle_details', 'kyc.selfie_url', 'kyc.exp')->get();

        $userInvitations = DB::table('invitations')
            ->where('inviter_id', $user->id)
            ->where('type', 'join')
            ->where('created_at', '>=', now()->subDays(2))
            ->get()
            ->keyBy('job_id');

        $userPrivateReqs = DB::table('cus_job_temp')
            ->where('user_id', $user->id)
            ->where('global_type', 'schedule')
            ->where('created_at', '>=', now()->subDays(2))
            ->whereIn('job_status', ['created', 'pending', 'inreview', 'accepted', 'rejected'])
            ->get();

        $scheduleReqStatusMap = [];
        foreach ($userPrivateReqs as $upr) {
            $schRaw = $upr->sch_status;
            if (empty($schRaw)) continue;

            $schJson = json_decode($schRaw, true);
            if (!is_array($schJson)) continue;

            foreach ($schJson as $dateKey => $drivers) {
                if (!is_array($drivers)) continue;
                foreach ($drivers as $driverId => $meta) {
                    if (!is_array($meta) || !isset($meta['sch_id'])) continue;
                    $metaSchId = (string) $meta['sch_id'];
                    $status = $meta['status'] ?? $upr->job_status;
                    $compositeKey = $dateKey . '|' . $metaSchId;
                    if (!isset($scheduleReqStatusMap[$compositeKey])) {
                        $scheduleReqStatusMap[$compositeKey] = [
                            'status' => $status,
                            'job_no' => $upr->job_no,
                        ];
                    }
                }
            }
        }

        $jobs = [];
        $faresByDate = [];
        $startDate = Carbon::today();
        for ($i = 0; $i < 7; $i++) {
            $faresByDate[$startDate->copy()->addDays($i)->format('Y-m-d')] = 999999;
        }

        foreach ($drCarpoolJobs as $cj) {
            $t = Carbon::parse($cj->pickup_date);
            if ($t->isPast()) {
                continue;
            }
            $dStr = $t->format('Y-m-d');
            if ($dStr === $date && $t->lt($requestedDateTime)) {
                continue;
            }
            $fare = (float)$cj->fare;
            if (isset($faresByDate[$dStr]) && $fare < $faresByDate[$dStr]) {
                $faresByDate[$dStr] = $fare;
            }

            if ($dStr === $date) {
                $parsedVeh = null;
                $carName = 'Vehicle';
                if (!empty($cj->embedded_vehicle_details)) {
                    $parsedVeh = json_decode($cj->embedded_vehicle_details, true);
                    if (is_array($parsedVeh)) {
                        $carName = $parsedVeh['type'] ?? ($parsedVeh['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
                    }
                }
                $offeredSeats = (is_numeric($cj->pass_count) && (int)$cj->pass_count > 0) ? (int)$cj->pass_count : 4;
                $alreadyFilled = (isset($cj->filled_seat) && is_numeric($cj->filled_seat)) ? (int)$cj->filled_seat : 0;
                $remainingSeats = max(0, $offeredSeats - $alreadyFilled);

                $req_status = null;
                if ($userInvitations->has($cj->id)) {
                    $invite = $userInvitations->get($cj->id);
                    if ($invite->status === 'accept' || $invite->status === 'accepted') {
                        $req_status = 'accepted';
                    } elseif ($invite->status === 'reject' || $invite->status === 'rejected') {
                        $req_status = 'rejected';
                    } else {
                        $req_status = 'requested';
                    }
                }

                $jobs[] = [
                    'id' => 'cp_' . $cj->id,
                    'db_id' => $cj->id,
                    'job_no' => null,
                    'carName' => $carName,
                    'name' => $cj->name ?? 'User',
                    'rating' => $cj->rating ? number_format($cj->rating / 20, 1) : '4.5',
                    'rides' => rand(10, 50),
                    'exp' => $cj->exp,
                    'selfie_url' => $cj->selfie_url,
                    'dep' => $t->format('H:i'),
                    'arr' => $t->copy()->addHours(3)->format('H:i'),
                    'dur' => '3h 0m',
                    'fareAdj' => 0,
                    'perSeatFare' => $fare,
                    'nextDay' => false,
                    'rideType' => 'carpool',
                    'maxSeats' => $remainingSeats > 0 ? $remainingSeats : 1,
                    'seats' => 1,
                    'full' => $remainingSeats <= 0,
                    'distance' => '200 km',
                    'cpDriverStart' => $cj->from_place,
                    'cpDriverEnd' => $cj->to_place,
                    'cpFrom' => $cj->from_place,
                    'cpTo' => $cj->to_place,
                    'vehicle_meta' => $parsedVeh,
                    'req_status' => $req_status
                ];
            }
        }

        foreach ($scheduleJobs as $sj) {
            $dp = json_decode($sj->dates_price, true);
            $vDetails = json_decode($sj->vehicle_details, true);
            
            $carName = 'Vehicle';
            $seatCap = 4;
            if (is_array($vDetails)) {
                $carName = $vDetails['type'] ?? ($vDetails['rc_details']['response']['vehicle_details']['body_type'] ?? 'Vehicle');
                $seatCap = $vDetails['rc_details']['response']['vehicle_details']['seat_capacity'] ?? 4;
            }

            if (is_array($dp)) {
                foreach ($dp as $dt => $price) {
                    $jobDateTime = Carbon::parse($dt);
                    if ($jobDateTime->isPast()) {
                        continue;
                    }
                    $dStr = $jobDateTime->format('Y-m-d');
                    if ($dStr === $date && $jobDateTime->lt($requestedDateTime)) {
                        continue;
                    }
                    $fPrice = (float)$price;
                    if (isset($faresByDate[$dStr]) && $fPrice < $faresByDate[$dStr]) {
                        $faresByDate[$dStr] = $fPrice;
                    }

                    if ($dStr === $date) {
                        $schIdKey = (string) $sj->id;
                        $compositeKey = $dStr . '|' . $schIdKey;
                        $req_status = null;
                        $job_no = null;

                        if (isset($scheduleReqStatusMap[$compositeKey])) {
                            $req_status = $scheduleReqStatusMap[$compositeKey]['status'];
                            $job_no = $scheduleReqStatusMap[$compositeKey]['job_no'];
                            if (in_array($req_status, ['created', 'pending', 'inreview'])) {
                                $req_status = 'requested';
                            }
                        }

                        $jobs[] = [
                            'id' => 'sc_' . $sj->id,
                            'db_id' => $sj->id,
                            'job_no' => $job_no,
                            'carName' => $carName,
                            'name' => $sj->name ?? 'Driver',
                            'rating' => $sj->rating ? number_format($sj->rating / 20, 1) : '4.8',
                            'rides' => rand(50, 200),
                            'exp' => $sj->exp,
                            'selfie_url' => $sj->selfie_url,
                            'dep' => substr($dt, 11, 5) ?: '10:00',
                            'arr' => 'TBD',
                            'dur' => '4h 0m',
                            'fareAdj' => $fPrice,
                            'perSeatFare' => $fPrice,
                            'nextDay' => false,
                            'rideType' => 'private',
                            'maxSeats' => $seatCap,
                            'seats' => 1,
                            'full' => false,
                            'distance' => '200 km',
                            'cpDriverStart' => $sj->from_place,
                            'cpDriverEnd' => $sj->to_place,
                            'cpFrom' => $sj->from_place,
                            'cpTo' => $sj->to_place,
                            'vehicle_meta' => is_array($vDetails) ? $vDetails : null,
                            'req_status' => $req_status
                        ];
                    }
                }
            }
        }

        $dateStrip = [];
        foreach ($faresByDate as $k => $v) {
            if ($v != 999999) {
                $dateStrip[$k] = $v;
            }
        }

        return response()->json(['status' => true, 'data' => ['jobs' => $jobs, 'dates' => $dateStrip]]);
        
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('GoRide API Error in get_all_jobs_after_login: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => false, 
            'message' => 'Line ' . $e->getLine() . ': ' . $e->getMessage(),
            'file' => basename($e->getFile())
        ], 500);
    }
}

    public function carpoolStatus(Request $request)
    {
        $user = auth('sanctum')->user();
        $checkHeaderToken = request()->bearerToken();

        if (!$user && $checkHeaderToken) {
            $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($checkHeaderToken);
            if ($tokenModel) {
                $user = DB::table('customer_register')->where('id', $tokenModel->tokenable_id)->first();
            }
        }

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Authentication Required'], 401);
        }

        $invitations = DB::table('invitations')
            ->where('inviter_id', $user->id)
            ->where('type', 'join')
            ->whereIn('status', ['accepted', 'accept', 'rejected', 'reject', 'pending'])
            ->get();

        $data = [];
        foreach ($invitations as $inv) {
            $statusVal = $inv->status;
            if ($statusVal == 'accept') $statusVal = 'accepted';
            if ($statusVal == 'reject') $statusVal = 'rejected';
            if ($statusVal == 'pending') $statusVal = 'requested';

            $data[] = [
                'job_id' => $inv->job_id,
                'status' => $statusVal
            ];
        }
        return response()->json(['status' => true, 'data' => $data]);
    }

    private function sendOtp(string $mobile, string $otp, string $message, bool $resend = false): bool
    {
        $payload = [
            'mobile'            => $mobile,
            'templateName'      => 'national_draw_verification',
            'language'          => 'en',
            'templateBodyParam' => [(string) $otp],
            'messages'          => $message,
            'resend'            => $resend
        ];

        $messType = DB::table('settings')->value('mess_type');

        if ($messType === 'sms') {
            return Controller::smsNotification($payload, 'verify');
        }

        return Controller::sendNotification($payload);
    }

    public function sendWebOtp(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'mobile'     => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode'   => ['nullable', 'integer'],
                'deviceType' => 'nullable|string|in:MOBILE,APP,DESKTOP,BROWSER,TABLET|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }

            $mobile     = Controller::BlockSQLInjection($request->mobile);
            $dialCode   = Controller::BlockSQLInjection($request->dialCode ?? '91');
            $deviceType = Controller::BlockSQLInjection($request->deviceType ?? 'BROWSER');

            if (!$mobile) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid mobile',
                    'error'   => 'Invalid input'
                ], 400);
            }

            $otp        = Controller::generateOTP(4);
            $message    = "Your GoRide Verification Code is {$otp}. Please don't share with anyone.";
            $expiryTime = \Carbon\Carbon::now()->addMinutes(10)->toDateTimeString();
            $oneHourAgo = \Carbon\Carbon::now()->subHour();

            $customer = DB::table('customer_register')
                ->where([
                    'mobile'  => $mobile,
                    'status'  => '0',
                    'deletes' => '0'
                ])
                ->first();

            if ($mobile == '916383800627') {
                $otp = 111111;
                if ($customer) {
                    DB::table('customer_register')
                        ->where('id', $customer->id)
                        ->update([
                            'otp'        => $otp,
                            'updated_at' => now()
                        ]);
                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Mobile OTP Sent Successfully!',
                        'data'    => [
                            'enc' => encrypt([
                                'tempID' => $customer->id,
                                'mobile' => $customer->mobile,
                                'expiry' => $expiryTime
                            ])
                        ]
                    ]);
                }
            }

            if ($customer) {
                $limitVal = 3;
                $smsCount = DB::table('smslog')
                    ->where('mobile', $mobile)
                    ->where('smssendstatus', '1')
                    ->where('datetime', '>=', $oneHourAgo)
                    ->count();

                if ($smsCount >= $limitVal) {
                    return response()->json([
                        'status'  => 'failed',
                        'message' => 'Try Again After 1 Hour',
                        'error'   => 'Rate limit exceeded'
                    ], 429);
                }

                if (!$this->sendOtp($mobile, $otp, $message, $request->isResend ?? false)) {
                    return response()->json([
                        'status'  => 'failed',
                        'message' => 'OTP Not Sent',
                        'error'   => 'Notification failed'
                    ], 500);
                }

                DB::table('customer_register')
                    ->where('id', $customer->id)
                    ->update([
                        'otp'        => $otp,
                        'updated_at' => now()
                    ]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Mobile OTP Sent Successfully!',
                    'data'    => [
                        'enc' => encrypt([
                            'tempID' => $customer->id,
                            'mobile' => $customer->mobile,
                            'expiry' => $expiryTime
                        ])
                    ]
                ]);
            }

            $tempCount = DB::table('users_temp')
                ->where('mobile', $mobile)
                ->where('created_at', '>=', $oneHourAgo)
                ->count();

            if ($tempCount >= 3) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Try Again After 1 Hour',
                    'error'   => 'Rate limit exceeded'
                ], 429);
            }

            $tempId = DB::table('users_temp')->insertGetId([
                'building_name' => '',
                'city'          => '',
                'name'          => '',
                'email'         => '',
                'address'       => '',
                'state'         => '',
                'pass'          => '',
                'password'      => '',
                'lname'         => '',
                'mobile'        => $mobile,
                'dialCode'      => $dialCode,
                'otp'           => $otp,
                'ip'            => $request->ip(),
                'deviceType'    => $deviceType,
                'deletes'       => '1',
                'roll_id'       => '0',
                'utm_source'    => $_COOKIE['utm_source'] ?? null,
                'utm_campaign'  => $_COOKIE['utm_campaign'] ?? null,
                'created_at'    => now()
            ]);

            if (!$tempId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Insert Failed!',
                    'error'   => 'Temporary user creation failed'
                ], 500);
            }

            if (!$this->sendOtp($mobile, $otp, $message)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'OTP Not Sent',
                    'error'   => 'Notification failed'
                ], 500);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Mobile OTP Sent Successfully!',
                'data'    => [
                    'enc' => encrypt([
                        'tempID' => $tempId,
                        'mobile' => $mobile,
                        'expiry' => $expiryTime
                    ])
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in Web OTP: ', ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;
        $name = $request->name ?? '';
        $fcm_token = $request->fcm_token ?? '';

        $cached = Cache::get('otp_' . $phone);
        $dbCustomer = DB::table('customer_register')->where('mobile', $phone)->where('otp', $otp)->first();
        $dbTemp = DB::table('users_temp')->where('mobile', $phone)->where('otp', $otp)->first();

        if (($cached && (string) $cached === (string) $otp) || $dbCustomer || $dbTemp) {
            DB::table('customer_register')->updateOrInsert(
                ['mobile' => $phone],
                [
                    'name'       => $name,
                    'fcm_token'  => $fcm_token,
                    'status'     => '0',
                    'deletes'    => '0',
                    'updated_at' => now()
                ]
            );

            if ($cached) {
                Cache::forget('otp_' . $phone);
            }

            if ($dbCustomer) {
                DB::table('customer_register')->where('mobile', $phone)->update(['otp' => '']);
            }

            if ($dbTemp) {
                DB::table('users_temp')->where('mobile', $phone)->update(['otp' => '']);
            }

            $user = DB::table('customer_register')->where('mobile', $phone)->first();
            $customerModel = \App\Models\customer_register::find($user->id);
            $token = $customerModel->createToken('NDaccessToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'token'  => $token,
                'user'   => $user
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Invalid or Expired OTP'
        ], 400);
    }

    public function jobOtpInsert($otp, $jobId)
    {
        try {
            if (empty($otp) || empty($jobId)) {
                return false;
            }

            $otpId = DB::table('job_start_otps')->insertGetId([
                'job_id'        => $jobId,
                'otp'           => $otp,
                'expires_at'    => null,
                'attempts'      => 0,
                'max_attempts'  => 5,
                'verified_at'   => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
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
            } else {
                $day = $day . ' Day Upto 24 hours';
            }
        } else {
            $toll = (int)($toll / 2);

            if ($distance <= 100) {
                $day = 'Upto 5 hours';
            } elseif ($distance >= 101 && $distance <= 200) {
                $day = 'Upto 8 hours';
            } elseif ($distance >= 201 && $distance <= 300) {
                $day = 'Upto 12 hours';
            } elseif ($distance >= 301 && $distance <= 400) {
                $day = 'Upto 15 hours';
            } elseif ($distance >= 401) {
                $day = 'Upto 24 hours';
            }

            if ($isBelowDis) {
                $fare = $this->calculateSlabFare($distance, $seaters);
            }
        }

        $base = ($fare + $driver_bata);

        $settings = DB::table('booking_settings')->where('status', 0)->first();

        if ($settings && $settings->default_discount > 0) {
            $discountAmount = ($base * $settings->default_discount) / 100;
            $base -= $discountAmount;
        }

        $com = round(($base + $toll) * 0.05);
        $toll = round($toll);
        $u_cash_point = auth()->user()->cash_points ?? 0;
        $discount = round(($base + $com) * 0.05);
        if ($discount > 500) {
            $discount = 500;
        }

        if ($discount <= $u_cash_point && $u_cash_point != 0) {
            $tax = round((($base + $com) - $discount) * 0.05);
        } elseif ($u_cash_point != 0) {
            $discount = min($u_cash_point, $discount);
            $tax = round((($base + $com) - $discount) * 0.05);
        } else {
            $tax = round(($base + $com) * 0.05);
            $discount = 0;
        }

        $isDiscount = ($u_cash_point != 0) ? 'yes' : 'no';

        $get_fare = DB::table('tariff_fare_website')
            ->where('from_km', '<=', (float) $distance)
            ->where('to_km', '>=', (float) $distance)
            ->where($seaters, '!=', 0)
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
            'discount'      => $discount,
            'fare'          => round(($base + $com + $tax + $toll) - $discount)
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

    public function requestBook(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $checkHeaderToken = request()->bearerToken();

            if (!$user && $checkHeaderToken) {
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($checkHeaderToken);
                if ($tokenModel) {
                    $user = DB::table('customer_register')->where('id', $tokenModel->tokenable_id)->first();
                }
            }

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Authentication Required'], 401);
            }

            $validated = $request->validate([
                'id' => 'required|string',
                'db_id' => 'required|integer',
                'type' => 'required|string|in:carpool,private',
                'seats' => 'required|string',
                'fare' => 'required|numeric',
                'from_place' => 'required|string',
                'to_place' => 'required|string',
                'pickup_date' => 'required|string',
                'from_place_id' => 'nullable|string',
                'to_place_id' => 'nullable|string'
            ]);

            if ($validated['type'] === 'carpool') {
                $job = DB::table('cus_job_temp')->where('id', $validated['db_id'])->where('deletes', '0')->first();
                if (!$job) {
                    return response()->json(['status' => false, 'message' => 'Ride session definition missing.'], 404);
                }

                $existingInvite = DB::table('invitations')
                    ->where('job_id', $job->id)
                    ->where('inviter_id', $user->id)
                    ->where('type', 'join')
                    ->first();

                if ($existingInvite) {
                    if ($existingInvite->status === 'accept' || $existingInvite->status === 'accepted') {
                        return response()->json(['status' => true, 'is_accepted' => true, 'message' => 'Join request accepted by host.']);
                    }
                    return response()->json(['status' => false, 'message' => 'You have already requested to join this ride.'], 422);
                }

                $offeredSeats = (int)$job->pass_count;
                $alreadyFilled = (int)$job->filled_seat;
                if (($alreadyFilled + (int)$validated['seats']) > $offeredSeats) {
                    return response()->json(['status' => false, 'message' => 'Selected seat configuration exceeds vacancy limits.'], 422);
                }

                $totFare = $validated['fare'];
                $com = round($totFare * 0.1);
                $dedAmt = $com;
                $ncoll = $totFare;
                $ndri = $totFare - $com;
                $fare_breakdown = [
                    "com" => $com,
                    "tax" => 0,
                    "base_fare" => $totFare - $com,
                    "total_fare" => $totFare
                ];

                $inviteToken = Str::random(40);
                $otp = rand(1000, 9999);
                $inviteId = DB::table('invitations')->insertGetId([
                    'global_type' => $job->global_type,
                    'inviter_id' => $user->id,
                    'invitee_user_id' => $job->user_id,
                    'type' => 'join',
                    'job_id' => $job->id,
                    'from_place' => $validated['from_place'],
                    'to_place' => $validated['to_place'],
                    'from_place_id' => $validated['from_place_id'] ?? '',
                    'to_place_id' => $validated['to_place_id'] ?? '',
                    'invite_token' => $inviteToken,
                    'source' => 'job',
                    'collectAmt' => $ncoll,
                    'deductAmt' => $dedAmt,
                    'fare_breakdown' => json_encode($fare_breakdown),
                    'otp' => $otp,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $driver = DB::table('user_register')->where('id', $job->user_id)->first();
                if ($driver && $driver->fcm_token) {
                    $accessToken = $this->getAccessToken();
                    $this->sendFCM(
                        $accessToken,
                        $driver->fcm_token,
                        "🚗 New Request for {$job->from_place}",
                        "{$user->name} wants to join your ride! Tap to view and accept.",
                        [
                            'job_id' => (string)$job->id,
                            'pickup' => $validated['from_place'],
                            'dropoff' => $validated['to_place'],
                            'date' => $job->pickup_date,
                            'image' => $user->profile_img_url ?? '',
                            'name' => $user->name,
                            'type' => 'join_request',
                            'action' => 'join_request',
                            'screen' => '/my-posts',
                            'collectAmt' => (string)$ncoll,
                            'driverAmt' => (string)$ndri,
                            'invite_token' => $inviteToken,
                            'invite_id' => (string)$inviteId
                        ]
                    );
                }
                return response()->json(['status' => true, 'mode' => 'carpool', 'message' => 'Join request sent successfully to host.']);

            } else {
                $scheduleDate = DB::table('schedule_dates')->where('id', $validated['db_id'])->first();
                
                if (!$scheduleDate) {
                    return response()->json(['status' => false, 'message' => 'Driver timeline schedule block not resolved.'], 404);
                }

                $driverId = $scheduleDate->user_id;

                $rawDate = $validated['pickup_date'];
                $cleanDate = preg_replace('/(AM|PM):\d{2}/i', '$1', $rawDate);
                
                $carbonDate = \Carbon\Carbon::parse($cleanDate);
                $parsedDate = $carbonDate->format('Y-m-d H:i:s');
                $dateOnly = $carbonDate->format('Y-m-d');

                $attemptCount = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->where('global_type', 'schedule')
                    ->whereDate('pickup_date', $dateOnly)
                    ->where(function ($query) use ($validated) {
                        $query->where('sch_status', 'like', '%"sch_id":' . $validated['db_id'] . '%')
                              ->orWhere('sch_status', 'like', '%"sch_id":"' . $validated['db_id'] . '"%');
                    })
                    ->count();

                if ($attemptCount >= 2) {
                    return response()->json(['status' => false, 'message' => 'Driver request limit exceeded for today. Try another ride.'], 422);
                }
                $existingJob = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->where('global_type', 'schedule')
                    ->whereDate('pickup_date', $dateOnly)
                    ->where(function ($query) use ($validated) {
                        $db_id = $validated['db_id'];
                        $query->where('sch_status', 'like', '%"sch_id":"' . $db_id . '"%')   
                              ->orWhere('sch_status', 'like', '%"sch_id": "' . $db_id . '"%') 
                              ->orWhere('sch_status', 'like', '%"sch_id":' . $db_id . '%')    
                              ->orWhere('sch_status', 'like', '%"sch_id": ' . $db_id . '%');  
                    })
                    ->orderBy('id', 'desc')
                    ->first();

                
                if ($existingJob) {
                    if ($existingJob->payment_status === 'paid') {
                        Log::info('Result: Blocked duplicate request. Job is already paid.');
                        return response()->json([
                            'status' => false, 
                            'message' => 'You have already booked and paid for this schedule.Check WhatsApp For Details'
                        ], 422);
                    }
                    if (in_array($existingJob->job_status, ['accept', 'accepted'])) {
                        return response()->json([
                            'status' => true, 
                            'is_accepted' => true, 
                            'mode' => 'private', 
                            'job_no' => $existingJob->job_no,
                            'message' => 'Booking is already accepted by the driver.'
                        ]);
                    }
                    
                    if (in_array($existingJob->job_status, ['created', 'pending', 'inreview'])) {
                        Log::info('Result: Blocked duplicate request. Job is currently ' . $existingJob->job_status);
                        return response()->json([
                            'status' => false, 
                            'message' => 'This request is already ' . $existingJob->job_status . '. Please wait for the driver.'
                        ], 422);
                    }
                } else {
                    // Log::warning('NO MATCH FOUND -> Creating a new job request in database for sch_id: ' . $validated['db_id']);
                }

                $statusData = [
                    $dateOnly => [
                        $driverId => [
                            'status' => 'inreview',
                            'sch_id' => $validated['db_id'],
                            'updated_at' => now()->toISOString()
                        ]
                    ]
                ];
                $schStatusJson = json_encode($statusData);

                $maxAttempts = 5;
                for ($i = 0; $i < $maxAttempts; $i++) {
                    $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(Str::random(7));
                    if (!DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                        break;
                    }
                }

                $otp = Controller::generateOTP(6);

                $hash = hash_hmac(
                    'sha256',
                    $job_no . 'NEW_BOOKING' . ($user->mobile ?? ''),
                    config('app.key')
                );

                do {
                    $shortCode = env('SHORT_SLUG') . Str::random(8);
                } while (
                    DB::table('cus_job_temp')->where('short_hash', $shortCode)->exists()
                );

                $previewUrl = env('PREVIEW_ENDPOINT') . $shortCode;

                $seater = 'four_seater';
                if (isset($validated['seats'])) {
                    if ($validated['seats'] === 'mini') {
                        $seater = 'mini_four_seater';
                    } elseif ($validated['seats'] <= 5) {
                        $seater = 'four_seater';
                    } elseif ($validated['seats'] <= 7) {
                        $seater = 'six_seater';
                    } elseif ($validated['seats'] >= 8) {
                        $seater = 'seven_seater';
                    }
                }

                $fromId = $validated['from_place_id'] ?? '';
                $toId = $validated['to_place_id'] ?? '';

                $cached = DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $fromId,
                        'to_place_id' => $toId
                    ])->first();

                if (!$cached) {
                    $apiKey = env('GOOGLE_MAPS_API_KEY');

                    if (empty($fromId) || empty($toId)) {
                        return response()->json([
                            'status' => false,
                            'data' => [],
                            'message' => 'Missing from_place_id or to_place_id. Cannot calculate route.'
                        ]);
                    }

                    if ($apiKey) {
                        $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                            'origins' => "place_id:{$fromId}",
                            'destinations' => "place_id:{$toId}",
                            'key' => $apiKey
                        ]);

                        $data = $response->json();

                        if (isset($data['rows'][0]['elements'][0]['status']) && $data['rows'][0]['elements'][0]['status'] === 'OK') {
                            $distanceMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                            $durationSeconds = $data['rows'][0]['elements'][0]['duration']['value'];

                            $distanceKm = round($distanceMeters / 1000, 2);
                            $durationMins = round($durationSeconds / 60);

                            DB::table('location_distance_web')->insert([
                                'from_place_id' => $fromId,
                                'to_place_id' => $toId,
                                'distance' => $distanceKm,
                                'duration' => $durationMins,
                                'toll_fare' => 0,
                                'created_at' => now(),
                            ]);

                            $cached = (object)[
                                'distance' => $distanceKm,
                                'duration' => $durationMins,
                                'toll_fare' => 0
                            ];
                        }
                    }

                    // if (!$cached) {
                    //     return response()->json([
                    //         'status' => false,
                    //         'data' => [],
                    //         'message' => 'Route not found and could not be calculated. Check laravel logs for API details.'
                    //     ]);
                    // }
                }

                $journeyType = 'oneway';
                $cachedDistance = $cached->distance ?? 0;
                $distance = $journeyType === 'roundtrip' ? ($cachedDistance * 2) : $cachedDistance;
                $isBelowDis = $distance < 50;

                $tariffs = DB::table('tariff_fare_website')
                    ->where('from_km', '<=', $distance)
                    ->where('to_km', '>=', $distance)
                    ->where('status', '0')
                    ->where(function ($query) use ($seater) {
                        $query->where($seater, '>', 0)
                            ->orWhere($seater . '_round', '>', 0);
                    })
                    ->get();

                $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                $roundRow  = $tariffs->firstWhere($seater . '_round', '>', 0);
                $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                $baseFare_r = $roundRow ? $roundRow->{$seater . '_round'} : 0;
                $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow ? $roundRow->fare_km : 0);
                $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow ? $oneWayRow->fare_km : 0);

                if ($journeyType === 'roundtrip') {
                    $baseFare = $baseFare_r;
                    $perKm = $perKmRound;
                }

                $fromGeo = $this->getLatLngByPlaceId($fromId);
                $toGeo   = $this->getLatLngByPlaceId($toId);

                $fareData = $this->applyFareLogic_App(
                    $baseFare,
                    $cached->toll_fare ?? 0,
                    0,
                    $distance,
                    $cached->duration ?? 0,
                    $perKm,
                    $journeyType,
                    $parsedDate,
                    null,
                    $fromGeo['lat'] ?? null,
                    $fromGeo['lng'] ?? null,
                    $toGeo['lat'] ?? null,
                    $toGeo['lng'] ?? null,
                    $seater,
                    $isBelowDis
                );

                $from_to_co = json_encode([
                    'from_lat' => $fromGeo['lat'] ?? null,
                    'from_lng' => $fromGeo['lng'] ?? null,
                    'to_lat' => $toGeo['lat'] ?? null,
                    'to_lng' => $toGeo['lng'] ?? null,
                ]);

                $addFareDetails = json_encode([
                    'bata' => 'Included',
                    'parking' => 'Included',
                    'toll' => 'Included'
                ]);

                $jobId = DB::table('cus_job_temp')->insertGetId([
                    'job_no' => $job_no,
                    'user_id' => $user->id,
                    'global_type' => 'schedule',
                    'job_type' => $journeyType,
                    'from_place' => $validated['from_place'] ?? '',
                    'to_place' => $validated['to_place'] ?? '',
                    'from_place_id' => $fromId,
                    'to_place_id' => $toId,
                    'pickup_date' => $parsedDate,
                    'dropoff_date' => null,
                    'day' => $fareData['day'] ?? null,
                    'pass_count' => (string)($validated['seats'] ?? ''),
                    'fare' => $validated['fare'] ?? 0,
                    'distance' => (string)$distance,
                    'duration' => (string)($cached->duration ?? 0),
                    'add_fare_details' => $addFareDetails,
                    'from_to_co' => $from_to_co,
                    'pick_address' => '',
                    'drop_address' => '',
                    'user_details' => null,
                    'otp' => $otp,
                    'short_hash' => $shortCode,
                    'preview_hash' => $hash,
                    'job_status' => 'created',
                    'sch_status' => $schStatusJson,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'base_fare' => $fareData['base_fare'] ?? 0,
                    'toll_fare' => $fareData['toll_fare'] ?? 0,
                    'tax' => $fareData['tax'] ?? 0,
                    'com' => $fareData['com'] ?? 0,
                    'driver_bata' => $fareData['driver_bata'] ?? 0,
                    'isDiscount' => $fareData['isDiscount'] ?? 'no',
                    'discount' => $fareData['discount'] ?? 0,
                    'without_tax' => $fareData['without_tax'] ?? 0,
                ]);

                if ($jobId) {
                    $this->jobOtpInsert($otp, $jobId);

                    $firebaseData = [
                        'id' => $jobId,
                        'job_no' => $job_no,
                        'user_id' => $user->id,
                        'global_type' => 'schedule',
                        'job_type' => $journeyType,
                        'from_place' => $validated['from_place'] ?? '',
                        'to_place' => $validated['to_place'] ?? '',
                        'from_place_id' => $fromId,
                        'to_place_id' => $toId,
                        'pickup_date' => $parsedDate,
                        'dropoff_date' => null,
                        'pass_count' => (string)($validated['seats'] ?? ''),
                        'fare' => (float)$validated['fare'] ?? 0,
                        'distance' => (string)$distance,
                        'duration' => (string)($cached->duration ?? 0),
                        'add_fare_details' => $addFareDetails,
                        'from_to_co' => $from_to_co,
                        'pick_address' => '',
                        'drop_address' => '',
                        'user_details' => null,
                        'driver_id' => $driverId,
                        'driver_sc_id' => $validated['db_id'],
                        'job_status' => 'created',
                        'poster_name' => $user->name ?? '',
                        'preview_hash' => $hash,
                        'short_hash' => $shortCode,
                        'sch_status' => $schStatusJson,
                        'total_notifications' => 1,
                        'success_count' => 0,
                        'failure_count' => 0,
                        'base_fare' => $fareData['base_fare'] ?? 0,
                        'toll_fare' => $fareData['toll_fare'] ?? 0,
                        'tax' => $fareData['tax'] ?? 0,
                        'com' => $fareData['com'] ?? 0,
                        'driver_bata' => $fareData['driver_bata'] ?? 0,
                        'isDiscount' => $fareData['isDiscount'] ?? 'no',
                        'discount' => $fareData['discount'] ?? 0,
                        'per_km' => $fareData['per_km'] ?? 0,
                        'inc_km' => $fareData['inc_km'] ?? 0,
                        'without_tax' => $fareData['without_tax'] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $this->createFirebaseJob2($job_no, $firebaseData);

                    $pickup_formatted = \Carbon\Carbon::parse($parsedDate)->format('d M Y h:i A');
                    $created_formatted = now()->format('d M Y h:i A');
                    $name = $user->name ?? 'Customer';

                    $message = "📢 *New Booking Alert from Customer App - Schedule!*\n\nHello GoRide Team,\n\nA new ride request has been received. Please review the details below and assign a driver as soon as possible.\n\n---\n🗓 *Booking Details:*\n• *Booking Date:* {$created_formatted}\n• *Booking ID:* #{$job_no}\n• *Customer Name:* {$name}\n• *Pickup Date & Time:* {$pickup_formatted}\n\n🔗 *Preview Link:* {$previewUrl}\n\nThank you,\n*GoRide System*";

                    $mobilesss = [];
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
                }

                $driver = DB::table('user_register')->where('id', $driverId)->first();
                if ($driver && $driver->fcm_token) {
                    $accessToken = $this->getAccessToken();
                    $this->sendFCM(
                        $accessToken,
                        $driver->fcm_token,
                        '🎯 Job Waiting for You',
                        'Hurry up! Please confirm your availability within 30 seconds to take this job. 🚖',
                        [
                            'job_no' => $job_no,
                            'job_id' => $jobId,
                            'type' => 'new_job_notification',
                            'pickup' => (string)$dateOnly,
                            'sch_id' => json_encode((int)[$validated['db_id']]),
                            'action' => 'schedule_popup'
                        ]
                    );
                }

                $accessToken2 = $this->getAccessToken2();
                if ($accessToken2 && isset($this->serviceAccount2['project_id'])) {
                    try {
                        $firebaseUrl = 'https://' . $this->serviceAccount2['project_id'] . '.firebaseio.com/schedule_jobs/' . $job_no . '.json?access_token=' . $accessToken2;
                        Http::put($firebaseUrl, ['sch_status' => $statusData, 'updated_at' => now()->toISOString()]);
                    } catch (\Throwable $ex) {
                        \Illuminate\Support\Facades\Log::error("Firebase Realtime write failure: " . $ex->getMessage());
                    }
                }

                return response()->json(['status' => true, 'mode' => 'private', 'job_no' => $job_no, 'message' => 'Availability lock broadcast sent to driver.']);
            }
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getJobDetails(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $checkHeaderToken = request()->bearerToken();
            if (!$user && $checkHeaderToken) {
                $tokenModel = \Laravel\Sanctum\PersonalAccessToken::findToken($checkHeaderToken);
                if ($tokenModel) {
                    $user = DB::table('customer_register')->where('id', $tokenModel->tokenable_id)->first();
                }
            }
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'Authentication Required'], 401);
            }
            $request->validate([
                'job_id' => 'required',
                'type' => 'required|string|in:carpool,private'
            ]);
            $dbId = $request->job_id;
            $type = $request->type;
            if ($type === 'carpool') {
                $job = DB::table('cus_job_temp as j')
                    ->leftJoin('user_register as u', 'j.user_id', '=', 'u.id')
                    ->select('j.*', 'u.name as driver_name', 'u.mobile as driver_mobile')
                    ->where('j.id', $dbId)
                    ->first();
                if (!$job) {
                    return response()->json(['status' => false, 'message' => 'Job not found'], 404);
                }
                $invite = DB::table('invitations')
                    ->where('job_id', $job->id)
                    ->where('inviter_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->first();
                $pickupDate = !empty($job->pickup_date) ? date('d M Y h:i A', strtotime($job->pickup_date)) : 'N/A';
                return response()->json([
                    'status' => true,
                    'data' => [
                        'job_no' => $job->job_no ?? 'N/A',
                        'from_place' => $job->from_place ?? 'N/A',
                        'to_place' => $job->to_place ?? 'N/A',
                        'pickup_date' => $pickupDate,
                        'distance' => $job->distance ?? 'N/A',
                        'fare' => $invite ? $invite->collectAmt : ($job->fare ?? '0'),
                        'driver_name' => $job->driver_name ?? 'N/A',
                        'driver_mobile' => $job->driver_mobile ?? 'N/A',
                        'otp' => $invite ? $invite->otp : null
                    ]
                ]);
            }
            if ($type === 'private') {
                $schedule = DB::table('schedule_dates as s')
                    ->leftJoin('user_register as u', 's.user_id', '=', 'u.id')
                    ->select('s.*', 'u.name as driver_name', 'u.mobile as driver_mobile')
                    ->where('s.id', $dbId)
                    ->first();
                $job = DB::table('cus_job_temp')
                    ->where('global_type', 'schedule')
                    ->where(function($q) {
                        $q->where('job_status', '!=', 'cancelled')
                          ->orWhereNull('job_status');
                    })
                    ->where(function ($q) use ($dbId) {
                        $q->whereRaw("REPLACE(REPLACE(REPLACE(sch_status, ' ', ''), '\r', ''), '\n', '') LIKE ?", ['%"sch_id":"' . $dbId . '"%'])
                          ->orWhereRaw("REPLACE(REPLACE(REPLACE(sch_status, ' ', ''), '\r', ''), '\n', '') LIKE ?", ['%"sch_id":' . $dbId . '%']);
                    })
                    ->orderBy('id', 'desc')
                    ->first();
                if (!$job) {
                    return response()->json([
                        'status' => true,
                        'data' => [
                            'job_no' => 'N/A',
                            'from_place' => $schedule->from_place ?? 'N/A',
                            'to_place' => $schedule->to_place ?? 'N/A',
                            'pickup_date' => 'Flexible',
                            'distance' => 'N/A', 
                            'fare' => 'N/A',
                            'driver_name' => $schedule->driver_name ?? 'N/A',
                            'driver_mobile' => $schedule->driver_mobile ?? 'N/A',
                            'otp' => null
                        ]
                    ]);
                }
                $pickupDate = !empty($job->pickup_date) ? date('d M Y h:i A', strtotime($job->pickup_date)) : 'Flexible';
                return response()->json([
                    'status' => true,
                    'data' => [
                        'job_no' => $job->job_no ?? 'N/A',
                        'from_place' => $job->from_place ?? $schedule->from_place ?? 'N/A',
                        'to_place' => $job->to_place ?? $schedule->to_place ?? 'N/A',
                        'pickup_date' => $pickupDate,
                        'distance' => $job->distance ?? 'N/A',
                        'fare' => $job->fare ?? '0',
                        'driver_name' => $schedule->driver_name ?? 'N/A',
                        'driver_mobile' => $schedule->driver_mobile ?? 'N/A',
                        'otp' => $job->otp ?? null
                    ]
                ]);
            }
            return response()->json(['status' => false, 'message' => 'Invalid job type requested'], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching job details.',
                'error' => $e->getMessage() 
            ], 500);
        }
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
            'id' => ['integerValue' => (string) ($data['id'] ?? 0)],
            'job_no' => ['stringValue' => (string) $jobNo],
            'global_type' => ['stringValue' => (string) ($data['global_type'] ?? 'schedule')],
            'job_type' => ['stringValue' => (string) ($data['job_type'] ?? 'oneway')],
            'job_status' => ['stringValue' => (string) ($data['job_status'] ?? 'created')],
            'preview_hash' => ['stringValue' => (string) ($data['preview_hash'] ?? '')],
            'short_hash' => ['stringValue' => (string) ($data['short_hash'] ?? '')],
            'user_id' => ['integerValue' => (string) (int) ($data['user_id'] ?? 0)],
            'poster_name' => ['stringValue' => (string) ($data['poster_name'] ?? '')],
            'from_place' => ['stringValue' => (string) ($data['from_place'] ?? '')],
            'to_place' => ['stringValue' => (string) ($data['to_place'] ?? '')],
            'from_place_id' => ['stringValue' => (string) ($data['from_place_id'] ?? '')],
            'to_place_id' => ['stringValue' => (string) ($data['to_place_id'] ?? '')],
            'pickup_date' => ['stringValue' => (string) ($data['pickup_date'] ?? '')],
            'dropoff_date' => ['stringValue' => (string) ($data['dropoff_date'] ?? '')],
            'pass_count' => ['stringValue' => (string) ($data['pass_count'] ?? '')],
            'fare' => ['doubleValue' => (float) ($data['fare'] ?? 0)],
            'distance' => ['stringValue' => (string) ($data['distance'] ?? '0')],
            'duration' => ['stringValue' => (string) ($data['duration'] ?? '0')],
            'add_fare_details' => ['stringValue' => (string) ($data['add_fare_details'] ?? '{}')],
            'from_to_co' => ['stringValue' => (string) ($data['from_to_co'] ?? '{}')],
            'pick_address' => ['stringValue' => (string) ($data['pick_address'] ?? '')],
            'drop_address' => ['stringValue' => (string) ($data['drop_address'] ?? '')],
            'user_details' => ['stringValue' => (string) ($data['user_details'] ?? '')],
            'driver_id' => ['integerValue' => (string) (int) ($data['driver_id'] ?? 0)],
            'driver_sc_id' => ['integerValue' => (string) (int) ($data['driver_sc_id'] ?? 0)],
            'base_fare' => ['doubleValue' => (float) ($data['base_fare'] ?? 0)],
            'toll_fare' => ['doubleValue' => (float) ($data['toll_fare'] ?? 0)],
            'tax' => ['doubleValue' => (float) ($data['tax'] ?? 0)],
            'com' => ['doubleValue' => (float) ($data['com'] ?? 0)],
            'driver_bata' => ['doubleValue' => (float) ($data['driver_bata'] ?? 0)],
            'isDiscount' => ['stringValue' => (string) ($data['isDiscount'] ?? 'no')],
            'discount' => ['doubleValue' => (float) ($data['discount'] ?? 0)],
            'per_km' => ['doubleValue' => (float) ($data['per_km'] ?? 0)],
            'fare_km' => ['doubleValue' => (float) ($data['fare_km'] ?? 0)],
            'inc_km' => ['doubleValue' => (float) ($data['inc_km'] ?? 0)],
            'without_tax' => ['doubleValue' => (float) ($data['without_tax'] ?? 0)],
            'sch_status' => ['stringValue' => (string) ($data['sch_status'] ?? '{}')],
            'total_notifications' => ['integerValue' => (string) (int) ($data['total_notifications'] ?? 1)],
            'success_count' => ['integerValue' => (string) (int) ($data['success_count'] ?? 0)],
            'failure_count' => ['integerValue' => (string) (int) ($data['failure_count'] ?? 0)],
            'created_at' => ['timestampValue' => ($data['created_at'] ?? now())->toIso8601String()],
            'updated_at' => ['timestampValue' => ($data['updated_at'] ?? now())->toIso8601String()]
        ];

        $payload = ['fields' => $fields];

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

    public function pollStatus(Request $request)
    {
    $request->validate(['job_no' => 'required|string']);
    $job_no = $request->job_no;

    // 1. Fetch the local job record to track the time elapsed
    $localJob = DB::table('cus_job_temp')->where('job_no', $job_no)->first();
    $isTimeout = false;

    // Check if the job exists and 30 seconds have exceeded since creation
    if ($localJob && isset($localJob->created_at)) {
        if (Carbon::parse($localJob->created_at)->addSeconds(30)->isPast()) {
            $isTimeout = true;
        }
    }

    $accessToken2 = $this->getAccessToken2();
    if (!$accessToken2) {
        return response()->json(['status' => false, 'message' => 'Firebase authentication failure.'], 500);
    }

    $projectId = $this->serviceAccount2['project_id'];
    $firestoreUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/dev_jobs/{$job_no}";

    try {
        $response = Http::withToken($accessToken2)->get($firestoreUrl);
        $fbData = $response->json();

        if (isset($fbData['error'])) {
            return response()->json(['status' => false, 'message' => $fbData['error']['message'] ?? 'Not Found']);
        }

        // 2. Evaluate Firebase response for driver status
        if (isset($fbData['fields']['sch_status']['mapValue']['fields'])) {
            $schStatusFields = $fbData['fields']['sch_status']['mapValue']['fields'];

            foreach ($schStatusFields as $dateKey => $dateData) {
                $drivers = $dateData['mapValue']['fields'] ?? [];

                foreach ($drivers as $driverId => $meta) {
                    $driverFields = $meta['mapValue']['fields'] ?? [];
                    $status = $driverFields['status']['stringValue'] ?? '';

                    // If a driver is available, return immediately (ignore timeout)
                    if ($status === 'available') {
                        $amount = $driverFields['amount']['integerValue'] ?? $driverFields['amount']['stringValue'] ?? $driverFields['amount']['doubleValue'] ?? 0;

                        return response()->json([
                            'status' => true,
                            'state' => 'available',
                            'driver_id' => $driverId,
                            'amount' => $amount
                        ]);
                    }

                    // If a driver is busy, return immediately
                    if ($status === 'busy') {
                        return response()->json(['status' => true, 'state' => 'busy']);
                    }
                }
            }
        }

        // 3. If no driver has responded, handle the timeout cancellation
        if ($isTimeout) {
            DB::table('cus_job_temp')
                ->where('job_no', $job_no)
                ->update(['job_status' => 'cancelled']);

            return response()->json(['status' => true, 'state' => 'cancelled']);
        }

        // 4. Default return if within 30 seconds and no response yet
        return response()->json(['status' => true, 'state' => 'inreview']);

    } catch (\Throwable $e) {
        // Fallback timeout check in case Firebase request fails entirely
        if ($isTimeout) {
            DB::table('cus_job_temp')
                ->where('job_no', $job_no)
                ->update(['job_status' => 'cancelled']);
                
            return response()->json(['status' => true, 'state' => 'cancelled']);
        }

        return response()->json(['status' => true, 'state' => 'inreview']);
    }
}

    public function cancelRequest(Request $request)
    {
        $request->validate([
            'job_no' => 'required|string',
            'db_id' => 'required|integer'
        ]);

        $user = auth('sanctum')->user();
        if ($user) {
            Cache::put('req_timeout_' . $user->id . '_' . $request->db_id, true, 86400);
        }

        $accessToken2 = $this->getAccessToken2();
        if ($accessToken2 && isset($this->serviceAccount2['project_id'])) {
            $firebaseUrl = 'https://' . $this->serviceAccount2['project_id'] . '.firebaseio.com/schedule_jobs/' . $request->job_no . '.json?access_token=' . $accessToken2;

            $res = Http::get($firebaseUrl);
            $data = $res->json();

            if (isset($data['sch_status'])) {
                $statusData = $data['sch_status'];
                foreach ($statusData as $date => &$drivers) {
                    foreach ($drivers as $driverId => &$meta) {
                        $meta['status'] = 'cancelled';
                        $meta['updated_at'] = now()->toISOString();
                    }
                }
                Http::put($firebaseUrl, ['sch_status' => $statusData, 'updated_at' => now()->toISOString()]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Request revoked and safely updated.']);
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

    $get_data = DB::table('cus_job_temp')->where('job_no', $request->pay_no)->whereIn('job_status', ['created', 'bidding', 'schedule'])->first();

    if (!$get_data) {
        return response()->json([
            'status'  => 'failed',
            'message' => 'Booking not found or cancelled',
            'error'   => 'Booking not found or cancelled'
        ]);
    }

    $fare = (float) ($get_data->fare ?? 0);
    $depositAmt = (int) $fare;
    $finalAmt = $depositAmt;

    if ($get_data->fare_breakdown) {
        $fr_details = json_decode($get_data->fare_breakdown, true);
    } else {
        $fr_details = [
            'com'                => (float) ($get_data->com ?? 0),
            'tax'                => (float) ($get_data->tax ?? 0),
            'base_fare'          => (float) ($get_data->base_fare ?? $fare),
            'total_fare'         => $fare,
            'actual_total_fare'  => $fare,
            'discount'           => (float) ($get_data->discount ?? 0),
            'isDiscount'         => $get_data->isDiscount ?? 'no',
            'wallet'             => 0,
            'part_pay_fare'      => 0,
            'pay_to_driver'      => $fare,
            'bidder_id'          => null,
            'actual_driver_amt'  => 0,
        ];
    }

    if ($get_data->bids_details) {
        $bid_details = json_decode($get_data->bids_details, true);
        $bidder_id = $fr_details['bidder_id'] ?? null;

        if (!$bidder_id || !isset($bid_details[$bidder_id])) {
            return response()->json([
                'status' => false,
                'message' => 'Selected bid is no longer available. Please refresh.'
            ]);
        }

        $bids = $bid_details[$bidder_id];

        if (!isset($bids['amount'])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid bid data. Please refresh.'
            ]);
        }

        if ((float)$bids['amount'] !== (float)$fr_details['actual_driver_amt']) {
            return response()->json([
                'status' => false,
                'message' => 'The price was changed. Refresh to proceed with payment.'
            ]);
        }
    }

    $wallet_amt = 0;
    $wtotalFare = (float) ($fr_details['total_fare'] ?? 0);
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

    $w_amt = $fr_details['wallet'] ?? 0;
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
            'userID'           => $get_data->user_id ?? 0,
            'depositAmt'       => $depositAmt,
            'existWalletAmt'   => 0,
            'existCash_points' => 0,
            'finalTotal'       => $fr_details['actual_total_fare'] ?? $depositAmt,
            'discount'         => 0,
            'shipamount'       => 0,
            'wallet_amt'       => $w_amt,
            'credit_amt'       => $credit_amt,
            'grandtotal'       => $depositAmt,
            'shipping'         => 'pickUpToStore',
        ];

        $checkout_arr = [
            'createdon'          => now(),
            'crontime'           => now(),
            'ip'                 => $request->ip() ?? '',
            'user_id'            => $get_data->user_id ?? 0,
            'status'             => '0',
            'transaction_id'     => $tran_id,
            'job_no'             => $request->pay_no,
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

        $fareBreakdown = $get_log->fare_breakdown ? json_decode($get_log->fare_breakdown, true) : $fr_details;
        $sch_status = json_decode($get_log->sch_status, true) ?? [];

        if ($get_log && $get_log->global_type != 'schedule') {
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $firebaseDoc = $firebase->getJob($get_data->job_no);
            $job = $this->parseFirestoreFields($firebaseDoc);
            $bidder_id = $fareBreakdown['bidder_id'] ?? null;

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

        $assigned_to = $fareBreakdown['bidder_id'] ?? null;
        if (!$assigned_to && !empty($sch_status)) {
            foreach ($sch_status as $date => $drivers) {
                foreach ($drivers as $driverId => $info) {
                    if (isset($info['status']) && $info['status'] !== 'rejected') {
                        $assigned_to = $driverId;
                        break 2;
                    }
                }
            }
        }

        $total_fare = $fareBreakdown['total_fare'] ?? $depositAmt;
        $fareBreakdown['pay_to_driver'] = $total_fare;
        $fareBreakdown['wallet'] = 0;
        $fareBreakdown['total_fare'] = $total_fare;

        DB::table('cus_job_temp')
            ->where('job_no', $get_log->job_no)
            ->update([
                'com'              => $fareBreakdown['com'] ?? 0,
                'tax'              => $fareBreakdown['tax'] ?? 0,
                'base_fare'        => $fareBreakdown['base_fare'] ?? 0,
                'toll_fare'        => $fareBreakdown['toll_fare'] ?? 0,
                'discount'         => $fareBreakdown['discount'] ?? 0,
                'isDiscount'       => $fareBreakdown['isDiscount'] ?? 0,
                'wallet_amt'       => $fareBreakdown['wallet'] ?? 0,
                'fare'             => $total_fare,
                'deductAmt'        => $partPayFare,
                'credit'           => $fareBreakdown['discount'] ?? 0,
                'job_status'       => 'accepted',
                'payment_status'   => 'paid',
                'assigned_to'      => $assigned_to,
                'bids_details'     => ($get_log->global_type != 'schedule') ? json_encode($job['bids_details'] ?? []) : null,
                'fare_breakdown'   => json_encode($fareBreakdown),
                'updated_at'       => now()
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
        ", [$get_log->job_no, $get_log->user_id ?? 0]);

        DB::commit();

        if ($assigned_to) {
            dispatch(new \App\Jobs\SendFcmNotificationJob(
                type: 'accept_notification',
                userIds: [$assigned_to],
                title: 'Schedule Confirmed',
                body: "You are scheduled for this ride.",
            ));

            $dr_details = DB::table('user_register')->where(['id' => $assigned_to, 'deletes' => '0'])->first();
            $cus_details = DB::table('customer_register')->where('id', $get_log->user_id)->first();

            $pickupTime = $get_log->pickup_date
                ? date('d M Y, h:i A', strtotime($get_log->pickup_date))
                : '-';

            $driverName = $dr_details->name ?? 'Driver';
            $driverMobile = $dr_details->mobile ?? '-';

            $customerName = $cus_details->name ?? 'Customer';
            $customerMobile = $cus_details->mobile ?? '-';

            $tripType = ucwords($get_log->job_type ?? '-');
            $fare = $total_fare ?? 0;
            $noOfDays = !empty($get_log->day) ? $get_log->day : 'N/A';

            $sendTemplateMessage = function($mobile, $templateName, $parameters) use ($request) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '91' . $cleanPhone;
                }

                if (empty($cleanPhone) || strlen($cleanPhone) < 10) return;

                $template = DB::table('wamail_templates')->where('name', $templateName)->first();
                if (!$template) return;

                $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";

                $bodyParameters = [];
                foreach ($parameters as $param) {
                    if ($param !== null && $param !== '') {
                        $bodyParameters[] = [
                            "type" => "text",
                            "text" => (string) $param
                        ];
                    }
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
                                ];
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
                $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                $resTime = now();
                $body = $response->json();

                $messageId = $body['messages'][0]['id'] ?? null;
                $isSuccess = $response->successful();

                DB::table('smslog')->insert([
                    'gateway' => 'fbWhatsapp',
                    'subject' => $templateName,
                    'details' => json_encode($parameters),
                    'mobile' => $cleanPhone,
                    'ip' => $request->ip() ?? '',
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

            if (!empty($driverMobile) && $driverMobile !== '-') {
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

            if (!empty($customerMobile) && $customerMobile !== '-') {
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
                'job_no'      => $get_log->job_no,
                'paid_amount' => $depositAmt,
                'ride_info'   => $fareBreakdown
            ],
            'message' => 'Ride confirmed successfully.'
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Unable to pay via cash, try again another payment type.',
            'error' => $e->getMessage()
        ]);
    }
}
}