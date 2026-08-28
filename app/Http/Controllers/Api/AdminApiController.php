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
use App\Services\FirebaseJobService;
use App\Services\AutomationEventService;



class AdminApiController extends Controller
{

    public $serviceAccountPath;
    public $serviceAccount;

    public function __construct()
    {

        $this->serviceAccountPath = storage_path('app/firebase/firebase-config.json');

        if (!file_exists($this->serviceAccountPath)) {
            response()->json([
                'status' => 'error',
                'message' => 'Firebase config file not found'
            ], 500)->send();
            exit; // stop execution after sending response
        }

        $this->serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
    }

    //new development yokesh 26-05-2026 -start
    public function triggerSelfieVerify(Request $request)
    {
    $expectedToken = 'asdfghjklpoiuytrewqzxcvbnm!@$%^&*()';
    
    if ($request->bearerToken() !== $expectedToken) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $validated = $request->validate([
        'user_id' => 'required',
        'action'  => 'required|string',
        'reason'  => 'nullable|string'
    ]);

    $extra = [
        'action' => $validated['action'],
        'reason' => $validated['reason'] ?? ''
    ];

    $user = DB::table('customer_register')
              ->where('id', $validated['user_id'])
              ->first();

    $hasCarpoolJob = DB::table('cus_job_temp')
                       ->where('user_id', $validated['user_id'])
                       ->where('global_type', 'carpool')
                       ->exists();

    AutomationEventService::trigger('carpool_selfie_verify', $validated['user_id'], $extra);
    
    $eventsTriggered = ['carpool_selfie_verify'];

    if ($validated['action'] === 'reject') {
        AutomationEventService::trigger('carpool_selfie_reject', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_selfie_reject';
    }

    if ($user && $user->doc_verify == 1 && $user->vehicle_verify == 2 && !$hasCarpoolJob) {
        AutomationEventService::trigger('carpool_all_profile_verify', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_all_profile_verify';
    }

    return response()->json([
        'success' => true, 
        'message' => "Push automation triggered successfully.",
        'events_triggered' => $eventsTriggered
    ]);
}
    
    public function triggerDlVerify(Request $request)
    {
    $expectedToken = 'asdfghjklpoiuytrewqzxcvbnm!@$%^&*()';
    
    if ($request->bearerToken() !== $expectedToken) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $validated = $request->validate([
        'user_id' => 'required',
        'action'  => 'required|string',
        'reason'  => 'nullable|string'
    ]);

    $extra = [
        'action' => $validated['action'],
        'reason' => $validated['reason'] ?? ''
    ];

    $user = DB::table('customer_register')
              ->where('id', $validated['user_id'])
              ->first();

    $hasCarpoolJob = DB::table('cus_job_temp')
                       ->where('user_id', $validated['user_id'])
                       ->where('global_type', 'carpool')
                       ->exists();

    $eventsTriggered = [];

    if ($validated['action'] === 'approve') {
        AutomationEventService::trigger('carpool_dl_approved', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_dl_approved';
    } elseif ($validated['action'] === 'reject') {
        AutomationEventService::trigger('carpool_dl_rejected', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_dl_rejected';
    }

    if ($user && $user->doc_verify == 1 && $user->vehicle_verify == 2 && !$hasCarpoolJob) {
        AutomationEventService::trigger('carpool_all_profile_verify', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_all_profile_verify';
    }

    return response()->json([
        'success' => true, 
        'message' => "Push automation triggered successfully.",
        'events_triggered' => $eventsTriggered
    ]);
}
    
    public function triggerVehicleVerify(Request $request)
    {
    $expectedToken = 'asdfghjklpoiuytrewqzxcvbnm!@$%^&*()';
    
    if ($request->bearerToken() !== $expectedToken) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $validated = $request->validate([
        'user_id'      => 'required',
        'action'       => 'required|string',
        'reason'       => 'nullable|string',
        'vehicle_type' => 'nullable|string'
    ]);

    $extra = [
        'action'       => $validated['action'],
        'reason'       => $validated['reason'] ?? '',
        'vehicle_type' => $validated['vehicle_type'] ?? ''
    ];

    $user = DB::table('customer_register')
              ->where('id', $validated['user_id'])
              ->first();

    $hasCarpoolJob = DB::table('cus_job_temp')
                       ->where('user_id', $validated['user_id'])
                       ->where('global_type', 'carpool')
                       ->exists();

    $eventsTriggered = [];

    if ($validated['action'] === 'approve') {
        AutomationEventService::trigger('carpool_vehicle_approved', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_vehicle_approved';
    } elseif ($validated['action'] === 'reject') {
        AutomationEventService::trigger('carpool_vehicle_rejected', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_vehicle_rejected';
    }

    if ($user && $user->doc_verify == 1 && $user->vehicle_verify == 2 && !$hasCarpoolJob) {
        AutomationEventService::trigger('carpool_all_profile_verify', $validated['user_id'], $extra);
        $eventsTriggered[] = 'carpool_all_profile_verify';
    }

    return response()->json([
        'success' => true, 
        'message' => "Push automation triggered successfully.",
        'events_triggered' => $eventsTriggered
    ]);
}
    //new development yokesh 26-05-2026 - end

    public function Admin_get_fare(Request $request)
    {
        try {
            $request->validate([
                'distance'     => 'required|numeric',
                'pickup_date'  => ['required', 'date_format:Y-m-d H:i:s'],
                'dropoff_date' => ['nullable'],
                'way_type'     => 'required|in:oneway,roundtrip',
            ]);

            $pickup = Carbon::parse($request->pickup_date);
            $pickDate = $pickup->format('Y-m-d');
            $dropDate = $request->dropoff_date;
            $journeyType = $request->way_type;
            $userCashPoints = auth()->user()->cash_points ?? 0;

            $distanceKm = ceil($request->distance);

            if ($journeyType == 'roundtrip') {
                $distanceKm *= 2;
            }

            if ($distanceKm < 50) {
                return response()->json([
                    'status'  => false,
                    'data'    => [],
                    'message' => 'The minimum booking distance is 50 km.'
                ]);
            }

            $seaters = ['four_seater', 'seven_seater'];
            $responseData = [];

            foreach ($seaters as $seater) {
                $tariffs = DB::table('tariff_fare_website')
                    ->where('from_km', '<=', $distanceKm)
                    ->where('to_km', '>=', $distanceKm)
                    ->where('status', '0')
                    ->where(function ($query) use ($seater) {
                        $query->where($seater, '>', 0)
                            ->orWhere($seater . '_round', '>', 0);
                    })
                    ->get();

                if ($tariffs->isEmpty()) {
                    continue;
                }

                $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                $roundRow  = $tariffs->firstWhere($seater . '_round', '>', 0);

                $baseFare   = $oneWayRow ? $oneWayRow->{$seater} : 0;
                $baseFare_r = $roundRow ? $roundRow->{$seater . '_round'} : 0;
                $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);

                if ($journeyType == 'roundtrip') {
                    $baseFare = $baseFare_r;
                    $perKm = $perKmRound;
                }

                $responseData[$seater] = $this->applyFareLogic(
                    $baseFare,
                    0,
                    $userCashPoints,
                    $distanceKm,
                    '',
                    $perKm,
                    $journeyType,
                    $pickDate,
                    $dropDate,
                    0,
                    0,
                    0,
                    0
                );
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

    public function sendCustomerPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids'   => 'required|array',
            'user_ids.*' => 'numeric',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'sent_by'    => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // 1. CAPTURE DATA
        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->sent_by ?? auth()->id() ?? 0;
        $userIds = $request->user_ids;
        $uid = $userIds[0];
        $controller = $this;

        // 2. FETCH SINGLE CUSTOMER
        $customer = DB::table('customer_register')
            ->where('id', $uid)
            ->where('deletes', '0')
            ->select('id', 'name', 'fcm_token')
            ->first();

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found or deleted.'], 404);
        }

        $uName = !empty($customer->name) ? $customer->name : 'Customer';

        if (empty($customer->fcm_token)) {
            return response()->json(['status' => false, 'message' => 'FCM Token missing for this customer.'], 400);
        }

        // REPLACE {{Name}} IN TITLE AND BODY
        $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
        $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

        // 3. INSERT INITIAL TRACKING RECORD
        $trackingId = DB::table('push_notifications')->insertGetId([
            'user_id'    => $uid,
            'sent_by'    => $adminId,
            'title'      => $personalizedTitle,
            'body'       => $personalizedBody,
            'status'     => 2, // 2 indicates "Processing"
            'req_json'   => json_encode(['target' => 'Customer', 'user_id' => $uid]),
            'res_json'   => json_encode(['status' => 'Sending...']),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        // 4. SEND NOTIFICATION SYNCHRONOUSLY
        $accessToken = $controller->getAccessToken();
        if (!$accessToken) {
            $resJson = [
                'success_count' => 0,
                'failure_count' => 1,
                'delivered'     => [],
                'not_delivered' => [['id' => $uid, 'name' => 'System', 'error' => 'Firebase Token Error']],
            ];
            DB::table('push_notifications')->where('id', $trackingId)->update(['status' => 0, 'res_json' => json_encode($resJson, JSON_UNESCAPED_UNICODE)]);
            return response()->json(['status' => false, 'message' => 'Firebase Auth Token Error.'], 500);
        }

        try {
            $response = $controller->sendFCM($accessToken, $customer->fcm_token, $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast_specific']);

            if (isset($response['name'])) {
                // SUCCESS - Format the exact delivered structure you requested
                $resJson = [
                    'success_count' => 1,
                    'failure_count' => 0,
                    'delivered'     => [['id' => $uid, 'name' => $uName]],
                    'not_delivered' => [],
                ];

                DB::table('push_notifications')->where('id', $trackingId)->update([
                    'status'     => 1,
                    'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                return response()->json(['status' => true, 'message' => 'Push notification sent successfully!'], 200);
            } else {
                // FCM RESPONSE ERROR - Format the exact not_delivered structure
                $resJson = [
                    'success_count' => 0,
                    'failure_count' => 1,
                    'delivered'     => [],
                    'not_delivered' => [['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error']],
                ];

                DB::table('push_notifications')->where('id', $trackingId)->update([
                    'status'     => 0,
                    'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
                return response()->json(['status' => false, 'message' => 'Failed to send notification via FCM.'], 500);
            }
        } catch (\Throwable $e) {
            // FATAL CATCH ERROR - Format the exact not_delivered structure
            $resJson = [
                'success_count' => 0,
                'failure_count' => 1,
                'delivered'     => [],
                'not_delivered' => [['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()]],
            ];

            DB::table('push_notifications')->where('id', $trackingId)->update([
                'status'     => 0,
                'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function admin_resend_job_push(Request $request)
    {
        try {
            $job_no = $request->job_no;

            if (!$job_no) {
                return response()->json(['status' => false, 'message' => 'Job number is missing.']);
            }

            // 1. FETCH DETAILS (Strictly from cus_job_temp only)
            $jobDetails = DB::table('cus_job_temp')->where('job_no', $job_no)->first();

            if (!$jobDetails) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found in cus_job_temp database.'
                ]);
            }

            $latitude = null;
            $longitude = null;

            // --- Extract pass_count to pass into getNearbyFcm ---
            $passCount = $jobDetails->pass_count ?? null;

            // Strategy A: Check JSON string in from_to_co (Most reliable in your DB)
            if (!empty($jobDetails->from_to_co)) {
                $coords = is_string($jobDetails->from_to_co) ? json_decode($jobDetails->from_to_co, true) : $jobDetails->from_to_co;
                if (isset($coords['from_lat']) && isset($coords['from_lng'])) {
                    $latitude = $coords['from_lat'];
                    $longitude = $coords['from_lng'];
                }
            }

            // Strategy B: Database lookup using place_id
            if (!$latitude || !$longitude) {
                $from_place_id = $jobDetails->from_place_id ?? null;
                if ($from_place_id) {
                    $get_lat = DB::table('outstation_locations')
                        ->where('place_id', $from_place_id)
                        ->select('latitude', 'longitude')
                        ->first();

                    if ($get_lat && $get_lat->latitude && $get_lat->longitude) {
                        $latitude = $get_lat->latitude;
                        $longitude = $get_lat->longitude;
                    }
                }
            }

            // Strategy C: Nominatim Geocoding
            $from_place = $jobDetails->from_place ?? null;
            if ((!$latitude || !$longitude) && !empty($from_place)) {
                try {
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'User-Agent' => 'GoRide-Admin/1.0'
                    ])->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $from_place . ', India',
                        'limit' => 1
                    ]);

                    $data = $response->json();
                    if ($response->successful() && !empty($data) && isset($data[0]['lat'])) {
                        $latitude = $data[0]['lat'];
                        $longitude = $data[0]['lon'];
                    }
                } catch (\Throwable $e) {
                    // Ignore geocoding failure
                }
            }

            if (!$latitude || !$longitude) {
                return response()->json([
                    'status' => false,
                    'message' => "Pickup location coordinates not found for this job."
                ]);
            }

            // Get Fcm Tokens - Handle missing controller safely
            try {
                $customerAppController = app(\App\Http\Controllers\Api\CustomerAppController::class);
                $drivers = $customerAppController->getNearbyFcm(
                    $latitude,
                    $longitude,
                    env('NOTIFICATION_LIMIT', 50),
                    $passCount // Sending pass_count here instead of null
                );
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'Controller Error: ' . $th->getMessage()
                ]);
            }

            // Safely cast to Collection
            $driversCollection = collect($drivers);
            $fcmTokens = $driversCollection->pluck('fcm_token')->filter()->toArray();
            $driverIds = $driversCollection->pluck('id')->filter()->toArray();

            $accessToken = method_exists($this, 'getAccessToken') ? $this->getAccessToken() : '';
            $serviceAcc = property_exists($this, 'serviceAccount') ? $this->serviceAccount : [];
            $pickupDate = $jobDetails->pickup_date ?? date('Y-m-d H:i:s');

            // 2. DISPATCH ASYNCHRONOUSLY
            if (!empty($fcmTokens)) {
                try {
                    dispatch(new \App\Jobs\SendJobNotificationJob(
                        [
                            'id'      => $jobDetails->id,
                            'job_id'  => $jobDetails->id,
                            'type'    => 'new_job_notification',
                            'pickup'  => $pickupDate,
                            'action'  => 'agree_popup'
                        ],
                        $fcmTokens,
                        $serviceAcc,
                        $accessToken ? $accessToken : '',
                        $driverIds
                    ));
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Job Dispatch Error: ' . $th->getMessage()
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Success! Notifications dispatched to ' . count($fcmTokens) . ' nearby drivers.'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'No nearby active drivers found to send notifications to.'
            ]);
        } catch (\Throwable $e) {
            // Using Throwable catches EVERYTHING (even fatal TypeErrors)
            return response()->json([
                'status' => false,
                'message' => 'Fatal Error: ' . $e->getMessage() . ' at line ' . $e->getLine()
            ]);
        }
    }

    public function AdminbookJourney(Request $request)
    {
        try {
            // 🔥 The Fix: Ensure empty strings/nulls are completely stripped from the request 
            // before validation so 'nullable' doesn't trip up on weird frontend payloads.
            if (empty(trim($request->c_email))) {
                $request->request->remove('c_email');
            }

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
                'c_email' => ['sometimes', 'nullable', 'email', 'max:255'], // Added 'email' rule for safety if they DO enter one
                'c_mobile' => ['required', 'string', 'max:255'],
                'isDriver' => ['nullable', 'string', 'max:255'],
                'tax' => ['nullable', 'numeric'],
            ]);

            $data = $request->all();
            $pickup = \Carbon\Carbon::parse($request->pickup_date);
            $now = \Carbon\Carbon::now();

            if ($pickup->isToday() && $pickup->lessThanOrEqualTo($now->copy()->addHour(2))) {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Pickup time must be at least 2 hour after the current time.'
                ]);
            }

            $maxAttempts = 5;
            for ($i = 0; $i < $maxAttempts; $i++) {
                $job_no = 'GRC-' . now()->format('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(7));
                if (!\Illuminate\Support\Facades\DB::table('cus_job_temp')->where('job_no', $job_no)->exists()) {
                    break;
                }
            }

            $cab_seater = [
                'mini_four_seater' => 'mini',
                'four_seater'      => '5',
                'six_seater'       => '7',
                'seven_seater'     => '8'
            ];

            $data['pass_count'] = $cab_seater[$request->cab_type] ?? 4;
            $data['job_no'] = $job_no;
            $data['global_type'] = 'customer';
            $data['user_id'] = 0;

            $data['add_fare_details'] = json_encode($data['add_fare_details'] ?? []);
            $data['pick_address'] = $data['pick_address'] ?? '';
            $data['drop_address'] = $data['drop_address'] ?? '';

            $data['pickup_date'] = date("Y-m-d H:i:s", strtotime($data['pickup_date']));
            $data['dropoff_date'] = $data['dropoff_date'] ?? null;
            $data['created_at'] = now();
            $data['updated_at'] = now();

            if (($data['isDriver'] ?? 'no') == 'no') {
                $column = $request->cab_type;
                $check_data = \Illuminate\Support\Facades\DB::table('location_distance_web')
                    ->where([
                        'from_place_id' => $data['from_place_id'],
                        'to_place_id'   => $data['to_place_id'],
                        'seater' => $column
                    ])->first();

                if ($check_data && !empty($data['pass_count'])) {
                    $toll_fare = $check_data->toll_fare;
                    $distance = $data['job_type'] == 'roundtrip' ? ($check_data->distance * 2) : $check_data->distance;
                    $seater = $request->cab_type;

                    $tariffs = \Illuminate\Support\Facades\DB::table('tariff_fare_website')
                        ->where('from_km', '<=', $distance)
                        ->where('to_km', '>=', $distance)
                        ->where('status', '0')
                        ->where(function ($query) use ($seater) {
                            $query->where($seater, '>', 0)->orWhere($seater . '_round', '>', 0);
                        })->get();

                    if ($tariffs->isEmpty()) {
                        return response()->json(['status' => false, 'data' => [], 'message' => 'Fare not found']);
                    }

                    $oneWayRow = $tariffs->firstWhere($seater, '>', 0);
                    $roundRow  = $tariffs->firstWhere($seater . '_round', '>', 0);

                    $fare       = $oneWayRow ? $oneWayRow->{$seater} : 0;
                    $baseFare_r = $roundRow ? $roundRow->{$seater . '_round'} : 0;
                    $perKm      = $oneWayRow ? $oneWayRow->fare_km : ($roundRow->fare_km ?? 0);
                    $perKmRound = $roundRow ? $roundRow->fare_km : ($oneWayRow->fare_km ?? 0);

                    if ($data['job_type'] == 'roundtrip') {
                        $fare = $baseFare_r;
                        $perKm = $perKmRound;
                    }

                    $driver_bata = 0;
                    $day = null;
                    $p_date = \Carbon\Carbon::parse($data['pickup_date'])->format('Y-m-d');

                    if ($data['job_type'] == 'roundtrip' && $p_date) {
                        $d_date = $data['dropoff_date'];
                        $day = $d_date;

                        if ($day > 1) {
                            $rule = \Illuminate\Support\Facades\DB::table('roundtrip_days')->where('day', $day)->first();
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
                    } else {
                        $toll_fare = (int)($check_data->toll_fare / 2);
                    }

                    $base_fare = $fare;

                    $tax_amount = $request->filled('tax') ? (float)$request->tax : 0;

                    $data['com']         = 0;
                    $data['tax']         = $tax_amount;
                    $data['toll_fare']   = $toll_fare;
                    $data['without_tax'] = round($base_fare);
                    $data['fare']        = round($base_fare + $driver_bata + $toll_fare + $tax_amount);
                    $data['base_fare']   = $base_fare;
                    $data['discount']    = 0;
                    $data['isDiscount']  = 'no';

                    $cabNameMap = [
                        'mini_four_seater' => 'Go Mini',
                        'four_seater'      => 'Go 4Seater',
                        'six_seater'       => 'Go 6Seater',
                        'seven_seater'     => 'Go 7Seater',
                    ];

                    $u_details = [
                        'name'       => $request->c_name,
                        'email'      => $request->c_email ?? '', // Defaults to empty string if omitted
                        'pass_count' => $request->pass_count,
                        'lugg_count' => $request->lugg_count,
                        'cab_type'   => $cabNameMap[$request->cab_type] ?? 'Unknown',
                        'mobile'     => $request->c_mobile,
                        'perKm'      => $perKm
                    ];

                    $data['user_details'] = json_encode($u_details);
                } else {
                    return response()->json(['status' => false, 'data' => [], 'message' => 'Location not found.']);
                }

                $hash = hash_hmac('sha256', $job_no . 'NEW_BOOKING' . $data['c_mobile'], config('app.key'));
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

                $create_job = \Illuminate\Support\Facades\DB::table('cus_job_temp')->insertGetId($data);

                if ($create_job) {

                    $data['id'] = $create_job;
                    $data['poster_name'] = $request->c_name;

                    $mobile     = $u_details['mobile'] ?? $request->c_mobile;
                    $email      = $u_details['email'] ?? '';
                    $cab_type   = $u_details['cab_type'] ?? '';
                    $pass_count = $u_details['pass_count'] ?? '';
                    $lugg_count = $u_details['lugg_count'] ?? '';
                    $name       = $u_details['name'] ?? 'Customer';

                    $pickup_date = \Carbon\Carbon::parse($data['pickup_date'])->format('d M Y h:i A');

                    $dropoff_date = null;
                    if ($data['job_type'] == 'roundtrip') {
                        $dropoff_date = $data['day'] ?? null;
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
                            
                            <tr>
                                <td style="background:#f9bf00; padding:20px; text-align:center; color:#ffffff;">
                                    <h2 style="margin:0;">GoRide – Booking Confirmation</h2>
                                    <p style="margin:5px 0 0;">Job No: <strong>' . $data['job_no'] . '</strong></p>
                                </td>
                            </tr>
        
                            <tr>
                                <td style="padding:25px; color:#333333;">
        
                                    <p><strong>Customer Name:</strong><br>
                                    ' . ($data['poster_name'] ?? $name) . '</p>
        
                                    <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">
        
                                    <h3 style="margin-bottom:10px;">Trip Details</h3>
                                    <table width="100%" cellpadding="6" cellspacing="0">
                                        <tr>
                                            <td><strong>Pickup Location:</strong></td>
                                            <td>' . $data['from_place'] . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Drop Location:</strong></td>
                                            <td>' . $data['to_place'] . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Distance:</strong></td>
                                            <td>' . $data['distance'] . ' kms</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>' . ($data['day'] ?? '') . '</td>
                                        </tr>
                                    </table>
        
                                    <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">
        
                                    <h3 style="margin-bottom:10px;">Schedule</h3>
                                    <p>
                                        <strong>Pickup:</strong> ' . $pickup_date . '<br>'
                        . ($dropoff_date ? '<strong>Drop-off:</strong> ' . $dropoff_date . '<br>' : '') . '
                                    </p>
        
                                    <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">
        
                                    <h3 style="margin-bottom:10px;">Trip Information</h3>
                                    <table width="100%" cellpadding="6" cellspacing="0">
                                        <tr>
                                            <td><strong>Trip Type:</strong></td>
                                            <td>' . ucfirst($data['job_type']) . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Vehicle:</strong></td>
                                            <td>' . $cab_type . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Luggage:</strong></td>
                                            <td>' . $lugg_count . '</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Passengers:</strong></td>
                                            <td>' . $pass_count . '</td>
                                        </tr>
                                    </table>
        
                                    <hr style="border:none; border-top:1px solid #eaeaea; margin:20px 0;">
        
                                    <h3 style="margin-bottom:10px;">Fare Summary</h3>
        
                                    <p style="font-size:15px; margin:6px 0;">
                                        <strong>Base Fare:</strong>
                                        <span style="float:right;">₹' . $base_fare . '</span>
                                    </p>
                                    
                                    <p style="font-size:15px; margin:6px 0;">
                                        <strong>Govt. Levy / Extra:</strong>
                                        <span style="float:right;">₹' . $toll . '</span>
                                    </p>
                                    
                                    <hr style="border:none; border-top:1px solid #e5e7eb; margin:10px 0;">
                                    
                                    <p style="font-size:18px; margin:6px 0;">
                                        <strong>Estimated Fare:</strong>
                                        <span style="color:#f9bf00; float:right;">₹' . $tot_fare . '</span>
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
                            
                                                <a href="' . $previewUrl . '"
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

                    // 1. Send Email
                    try {
                        if (!empty($email)) {
                            Controller::composeEmail($request->ip(), $email, $subject, $message, '', []);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Email failed: " . $e->getMessage());
                    }

                    // 2. Admin System Notification
                    try {
                        $userId = 0;
                        $link = "https://admin.goride.net.in/website-booking/";
                        $title = "New Job Lead from — " . $name;
                        $data_n = ['user_id' => $userId, 'user_name' => $name, 'status' => 'pending', 'changes' => null];
                        \App\Services\NotificationService::create('job.lead', $title, $data_n, $link, $userId);
                    } catch (\Exception $e) {
                        \Log::error("Notif failed: " . $e->getMessage());
                    }

                    // 3. Admin WhatsApp
                    $mobilesss = [
                        env('BOOK_NO_ONE'),
                        env('BOOK_NO_TWO')
                    ];

                    $phone_number_id = env('FB_WHATSAPP_PHONE_NUMBER_ID');
                    $ver   = env('FB_WHATSAPP_VERSION');
                    $token = env('FB_WHATSAPP_TOKEN');

                    foreach ($mobilesss as $adminMobile) {
                        if ($adminMobile) {
                            try {
                                \Illuminate\Support\Facades\Http::withToken($token)->post(
                                    "https://graph.facebook.com/{$ver}/{$phone_number_id}/messages",
                                    [
                                        "messaging_product" => "whatsapp",
                                        "to" => $adminMobile,
                                        "type" => "template",
                                        "template" => [
                                            "name" => env('FB_U_TEMP', 'hello_world'),
                                            "language" => ["code" => "en_US"]
                                        ]
                                    ]
                                );
                            } catch (\Exception $e) {
                                \Log::error("Admin WA failed: " . $e->getMessage());
                            }
                        }
                    }

                    // 4. Customer WhatsApp Message
                    $wh_mess = "Hello {$name} 👋,\n\nThank you for your cab booking request! 🚖\nWe have received your details as below:\n\n📍 *From:* {$data['from_place']}\n📍 *To:* {$data['to_place']}\n🗓 *Date & Time:* {$pickup_date}\n";
                    if ($data['job_type'] != 'oneway') {
                        $wh_mess .= "🗓 No. of Days : {$dropoff_date}\n";
                    }
                    $wh_mess .= "📞 *Mobile:* {$mobile}\n\nOur executive will contact you shortly with the complete booking details and confirmation link.\nBooking Info:- {$previewUrl}\n\n📲 Download our app now and get ₹" . env('CREDIT_POINT', 50) . " FREE credits! 🎉\n\n👉" . env('CUSTOMER_APP') . "\nThank you for choosing GoRide! 😊\n\nCall Customer support : " . env('SUPPORT_MOBILE') . " ";

                    if ($mobile) {
                        try {
                            Controller::sendNotification([
                                'mobile'            => '91' . $mobile,
                                'templateName'      => 'national_draw_verification',
                                'language'          => 'en',
                                'templateBodyParam' => [],
                                'messages'          => $wh_mess,
                                'resend'            => false
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("Cust WA failed: " . $e->getMessage());
                        }
                    }

                    return response()->json([
                        'status' => true,
                        'data' => $job_no,
                        'jd' => $create_job,
                        'preview' => $hash,
                        'message' => 'Job created successfully.'
                    ]);
                }
            } // Closing brace for if($isDriver == 'no')

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'data' => null, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
        }
    }

    public function reject_bid_admin(Request $request)
    {
    try {
        $request->validate([
            'uid'       => ['required', 'max:255'],
            'job_id'    => ['required', 'max:255'],
            'bidder_id' => ['required', 'max:255'],
        ]);

        // 1. Fetch Job
        $get_job = DB::table('cus_job_temp')
            ->where('id', $request->job_id)
            ->where('deletes', '=', '0')
            ->first();

        if (!$get_job) {
            return response()->json(['status' => false, 'message' => 'Job not found.']);
        }

        // 2. Handle Local Database Update for Bids
        $bids = json_decode($get_job->bids_details, true) ?? [];
        
        if (isset($bids[$request->bidder_id])) {
            $bids[$request->bidder_id]['status'] = 'reject';
            
            DB::table('cus_job_temp')
                ->where('id', $request->job_id)
                ->update([
                    'bids_details' => json_encode($bids),
                    'updated_at'   => now()
                ]);
        }

        // 3. Update Firebase Bid Status
        $firebase = new \App\Services\FirebaseJobService(
            $this->serviceAccount['project_id'],
            $this->getAccessToken()
        );

        $firebase->updateBidStatus(
            (string) $get_job->job_no,
            (string) $request->bidder_id,
            'reject'
        );

        // 4. Fetch Bidder & Poster Info for Notifications
        $get_bidder = DB::table('user_register')
            ->where('deletes', '0')
            ->where('id', $request->bidder_id)
            ->first();

        if (!$get_bidder) {
            return response()->json(['status' => false, 'message' => 'Bidder not found.']);
        }

        // Get Poster info
        if ($get_job->global_type == 'customer') {
            $user = DB::table('customer_register')->where(['id' => $get_job->user_id, 'deletes' => '0'])->first();
        } else {
            $user = DB::table('user_register')->where(['id' => $get_job->user_id, 'deletes' => '0'])->first();
        }
        $posterName = $user->name ?? 'Admin';

        // --- WHATSAPP TEMPLATE INTEGRATION ---
        if (Controller::checkWhatsApp(['mobile' => $get_bidder->mobile])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $get_bidder->mobile);
            if (strlen($cleanPhone) === 10) {
                $cleanPhone = '91' . $cleanPhone;
            }

            $formattedDate = \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A');

            $parameters = [
                $get_bidder->name ?? 'Driver', // {{1}}
                $get_job->job_no,              // {{2}}
                $get_job->from_place,          // {{3}}
                $get_job->to_place,            // {{4}}
                $formattedDate                 // {{5}}
            ];

            $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";
            
            $payload = [
                "messaging_product" => "whatsapp",
                "to" => $cleanPhone,
                "type" => "template",
                "template" => [
                    "name" => "reject_bid", 
                    "language" => ["code" => "en_US"],
                    "components" => [
                        ["type" => "body", "parameters" => array_map(fn($p) => ["type" => "text", "text" => (string)$p], $parameters)]
                    ]
                ]
            ];

            \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->post($url, $payload);
        }

        // --- FCM PUSH NOTIFICATION ---
        $fcmTokens = $this->getFcm([$get_bidder->id]);
        if ($fcmTokens) {
            $accessToken = $this->getAccessToken();
            foreach ($fcmTokens as $token) {
                $this->sendFCM(
                    $accessToken,
                    $token,
                    'Bid Rejected',
                    'Job ID ' . $get_job->job_no . ': Your bid has been rejected by Admin',
                    [
                        'caller' => $posterName,
                        'type'   => 'reject_notification',
                        'url'    => env('APP_URL') . 'jobs',
                    ]
                );
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Bidder rejected successfully.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
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
                'auth_key' => ['nullable', 'string', 'max:255'],
                'cancel_reason' => ['nullable', 'string'],
                'cancelled_by' => ['nullable']
            ]);

            $userId = isset($request->user_id) ? $request->user_id : auth()->id();
            $cancelReason = $request->cancel_reason ?? 'Cancelled by Admin';
            $cancelledBy = $request->cancelled_by ?? null;

            if (!empty($request->user_id) && $request->user_id != 0) {
                if ($request->auth_key != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                    return response()->json([
                        'status' => false,
                        'data' => [],
                        'message' => 'Unauthorized.',
                    ], 401);
                }
            }

            $get_job = DB::table('cus_job_temp')
                ->where(function ($query) use ($request) {
                    if ($request->job_no) {
                        $query->where('job_no', $request->job_no);
                    }
                    $query->orWhere('id', $request->job_id);
                })
                ->where('deletes', '0')
                ->first();

            if (!$get_job) {
                \Log::info('Testing...: ' . json_encode($request->job_no));
                return response()->json([
                    'status' => false,
                    'message' => 'Job Not Found'
                ], 404);
            }

            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $jobDoc = $firebase->getJob($get_job->job_no);
            $job = $jobDoc ? $this->parseFirestoreFields($jobDoc) : [];
            $get_bidders_ids = array_keys($job['bids_details'] ?? []);

            $firebase->deleteJob($get_job->job_no);

            DB::table('cus_job_temp')
                ->where('id', $get_job->id)
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0
                ]);

            DB::table('open_jobs')
                ->where('job_no', $get_job->job_no)
                ->orWhere('id', $get_job->id)
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0
                ]);

            DB::table('job_cancellations')->insert([
                'job_id'       => $get_job->id,
                'customer_id'  => $get_job->user_id,
                'cancelled_by' => $cancelledBy,
                'reason'       => $cancelReason,
                'created_at'   => \Carbon\Carbon::now()
            ]);

            $request->job_type = $request->job_type ?? 'open';

            // =========================================================================
            // WHATSAPP SENDING LOGIC 
            // =========================================================================
            $url = "https://graph.facebook.com/" . env('FB_WHATSAPP_VERSION', 'v24.0') . "/" . env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";
            $templateName = 'admin_cancle_jobs';
            $template = DB::table('wamail_templates')->where('name', $templateName)->first();

            if ($get_job->user_id == 0) {
                // WEBSITE JOB: Take from user_details
                $userDetails = json_decode($get_job->user_details, true);
                $customerName = $userDetails['name'] ?? 'Customer';
                $customerPhone = $userDetails['mobile'] ?? '';

                if (!empty($customerPhone)) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    if (\App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $cleanPhone])) {

                        $pickupLoc = $get_job->pick_address ?? $get_job->from_place ?? 'Unknown Location';
                        $dropLoc = $get_job->drop_address ?? $get_job->to_place ?? 'Unknown Location';
                        $rawDate = $get_job->pickup_date ?? $get_job->day ?? $get_job->created_at ?? null;
                        $formattedDate = !empty($rawDate) ? \Carbon\Carbon::parse($rawDate)->format('d-m-Y h:i A') : 'Not Specified';

                        $parameters = [$customerName, $get_job->job_no, $pickupLoc, $dropLoc, $formattedDate];

                        $this->sendCancelWhatsAppMessage($cleanPhone, $templateName, $template, $parameters, $url, $request);
                    }
                }
            } else {
                // APP JOB: Take from customer_register or user_register based on job_type
                $get_u = ($request->job_type == 'customer')
                    ? DB::table('customer_register')->where('id', $get_job->user_id)->where('deletes', 0)->first()
                    : DB::table('user_register')->where('id', $get_job->user_id)->where('deletes', '0')->first();

                if ($get_u) {
                    // Ensure we are taking the mobile number directly from the register table
                    $cleanPhone = preg_replace('/[^0-9]/', '', $get_u->mobile);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    if (\App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $get_u->mobile])) {

                        $formattedDate = \Carbon\Carbon::parse($get_job->pickup_date)->format('d-m-Y h:i A');
                        $parameters = [$get_u->name, $get_job->job_no, $get_job->from_place, $get_job->to_place, $formattedDate];

                        $this->sendCancelWhatsAppMessage($cleanPhone, $templateName, $template, $parameters, $url, $request);
                    }
                }
            }
            // =========================================================================

            $jobData = (array) $get_job;
            $biddersIds = $get_bidders_ids;

            dispatch(function () use ($jobData, $biddersIds) {
                $controller = app(\App\Http\Controllers\Api\OpenJobsController::class);
                $accessToken = $controller->getAccessToken();
                $get_job = (object) $jobData;

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

Unfortunately, your bid for job **{$get_job->job_no}** has been ❌ *cancelled* by the  Admin

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

                if (count($biddersIds) > 0) {
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

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Job cancelled successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::info('Validation Error: ' . json_encode($e->getMessage()));
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cancel Job Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'data' => null,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function sendCancelWhatsAppMessage($cleanPhone, $templateName, $template, $parameters, $url, $request)
    {
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

        if ($template && !empty($template->header_image)) {
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

        if ($template && !empty($template->variables_json)) {
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
                "code" => "en_US" // UPDATED: Changed from "en" to "en_US"
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

        // Logging Request
        \Illuminate\Support\Facades\Log::info('WhatsApp Request Payload:', $payload);

        $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);

        $resTime = now();
        $body = $response->json();

        // Logging Error/Success
        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error('WhatsApp API Error:', ['status' => $response->status(), 'response' => $body]);
        }

        $messageId = $body['messages'][0]['id'] ?? null;
        $isSuccess = $response->successful();

        \Illuminate\Support\Facades\DB::table('smslog')->insert([
            'gateway' => 'fbWhatsapp',
            'subject' => 'Job Cancelled by Admin',
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
    }

    public function cancel_assigned_job(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'job_id'        => ['required'],
                'user_id'       => ['nullable'],
                'job_no'        => ['nullable'],
                'job_type'      => ['nullable'],
                'auth_key'      => ['nullable', 'string', 'max:255'],
                'cancel_reason' => ['nullable', 'string'],
                'cancelled_by'  => ['nullable']
            ]);

            $userId = $request->user_id ?? auth()->id();
            $cancelReason = $request->cancel_reason ?? 'Cancelled by Admin';
            $cancelledBy = $request->cancelled_by ?? null;

            // Auth key check only if a valid user_id is passed (bypassed for admin/website where user_id might be 0)
            if (!empty($userId) && $userId != 0) {
                if ($request->auth_key != 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345') {
                    return response()->json(['status' => false, 'data' => [], 'message' => 'Unauthorized.'], 401);
                }
            }

            // Fetch Job: Conditionally apply user_id check
            $query = DB::table('cus_job_temp')
                ->where(function ($q) use ($request) {
                    $q->where('job_no', $request->job_no)
                        ->orWhere('id', $request->job_id);
                })
                ->where('deletes', '0')
                ->lockForUpdate();

            if (!empty($userId) && $userId != 0 && $request->job_type !== 'website') {
                $query->where('user_id', $userId);
            }

            $get_job = $query->first();

            if (!$get_job) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Job Not Found'], 404);
            }

            // Firebase Sync
            $get_bidders_ids = [];
            try {
                $firebase = new \App\Services\FirebaseJobService(
                    $this->serviceAccount['project_id'],
                    $this->getAccessToken()
                );
                $jobDoc = $firebase->getJob($get_job->job_no);

                if ($jobDoc) {
                    $job = $this->parseFirestoreFields($jobDoc);
                    $get_bidders_ids = array_keys($job['bids_details'] ?? []);
                    $firebase->deleteJob($get_job->job_no);
                }
            } catch (\Throwable $e) {
            }

            if (empty($get_bidders_ids)) {
                if (!empty($get_job->bid_details)) {
                    $bidDetails = json_decode($get_job->bid_details, true);
                    if (is_array($bidDetails)) $get_bidders_ids = array_keys($bidDetails);
                }
                if (empty($get_bidders_ids) && !empty($get_job->fare_breakdown)) {
                    $fareBreakdown = json_decode($get_job->fare_breakdown, true);
                    if (isset($fareBreakdown['bidder_id'])) $get_bidders_ids = [$fareBreakdown['bidder_id']];
                }
            }

            // Time-Based Refund Logic
            $pickupTime = \Carbon\Carbon::parse($get_job->pickup_date);
            $now        = \Carbon\Carbon::now();
            $minutesDifference = $now->diffInMinutes($pickupTime, false);
            $isEligibleForRefund = ($minutesDifference >= 60);

            $ch_payment = DB::table('payment_history')
                ->where('job_no', $get_job->job_no)
                ->where(function ($query) {
                    $query->where('paymentStatus', 'success')
                        ->orWhere('gateway', 'cash');
                })
                ->first();

            $fare_break = json_decode($get_job->fare_breakdown, true);

            if ($isEligibleForRefund) {

                // 1. Customer Wallet Refund (Only if payment exists and is NOT cash)
                if ($ch_payment && $ch_payment->gateway != 'cash') {
                    $alreadyRefundedWallet = DB::table('walletBalance_history')
                        ->where('reference_id', $ch_payment->id)
                        ->where('transaction_type', 'REFUND')
                        ->where('point_type', 'WALLET')
                        ->exists();

                    if (!$alreadyRefundedWallet) {
                        $actualUserId = $get_job->user_id;
                        $customer = DB::table('customer_register')->where('id', $actualUserId)->first();

                        if ($customer) {
                            $openingBalance = $customer->walletBalance ?? 0;
                            $expectedAmount = $ch_payment->grandtotal + $ch_payment->wallet_amt;
                            $closingBalance = $openingBalance + $expectedAmount;

                            DB::table('walletBalance_history')->insert([
                                'userid'           => $customer->id,
                                'uname'            => $customer->name,
                                'umobile'          => $customer->mobile,
                                'opening_balance'  => $openingBalance,
                                'total'            => $expectedAmount,
                                'closeing_balance' => $closingBalance,
                                'point_type'       => 'WALLET',
                                'transaction_type' => 'REFUND',
                                'global_type'      => 'customer',
                                'reference_id'     => $ch_payment->id,
                                'reference_table'  => 'payment_history',
                                'ip'               => $request->ip(),
                                'createdon'        => now(),
                                'updatedon'        => now()
                            ]);

                            DB::table('customer_register')
                                ->where('id', $customer->id)
                                ->update(['walletBalance' => $closingBalance]);
                        }
                    }
                }

                // 2. Customer Cash Points (Discount) Refund (Independent of Payment Gateway)
                if (!empty($fare_break['isDiscount']) && $fare_break['isDiscount'] == 'yes' && !empty($fare_break['discount'])) {

                    $alreadyRefundedCashPoints = DB::table('walletBalance_history')
                        ->where('reference_id', $get_job->id)
                        ->where('reference_table', 'cus_job_temp')
                        ->where('transaction_type', 'REFUND')
                        ->where('point_type', 'CREDIT')
                        ->exists();

                    if (!$alreadyRefundedCashPoints) {
                        $actualUserId = $get_job->user_id;
                        $customer = DB::table('customer_register')->where('id', $actualUserId)->first();

                        if ($customer) {
                            $cashOpening = $customer->cash_points ?? 0;
                            $cashClosing = $cashOpening + $fare_break['discount'];

                            DB::table('walletBalance_history')->insert([
                                'userid'           => $customer->id,
                                'uname'            => $customer->name,
                                'umobile'          => $customer->mobile,
                                'uemail'           => $customer->email ?? null,
                                'opening_balance'  => $cashOpening,
                                'total'            => $fare_break['discount'],
                                'closeing_balance' => $cashClosing,
                                'point_type'       => 'CREDIT',
                                'transaction_type' => 'REFUND',
                                'reward_type'      => 'JOB',
                                'global_type'      => 'customer',
                                'reference_id'     => $get_job->id, // Using Job ID since payment might not exist
                                'reference_table'  => 'cus_job_temp',
                                'ip'               => $request->ip(),
                                'createdon'        => now(),
                                'updatedon'        => now()
                            ]);

                            DB::table('customer_register')
                                ->where('id', $customer->id)
                                ->update(['cash_points' => $cashClosing]);
                        }
                    }
                }
            }

            // Driver Wallet Refund
            $gatewayUsed = $ch_payment->gateway ?? 'none';
            $deductAmt = $get_job->deductAmt ?? 0;

            if ($ch_payment && $ch_payment->gateway == 'cash' && isset($get_job->deductAmt) && $get_job->deductAmt != 0 && $get_job->assigned_to) {
                $expectedAmount = $get_job->deductAmt;

                $driver_info = DB::table('user_register')
                    ->where('id', $get_job->assigned_to)
                    ->where('deletes', '0')->first();

                $checkDeduct = DB::table('walletBalance_history')->where([
                    'userid'           => $get_job->assigned_to,
                    'transaction_type' => 'DEBIT',
                    'reference_id'     => $get_job->id,
                    'reference_table'  => 'cus_job_temp',
                    'total'            => $get_job->deductAmt,
                ])->first();

                if ($driver_info && $checkDeduct) {

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
                        'global_type'      => null,
                        'reference_id'     => $get_job->id,
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
                } else {
                }
            }

            // Update Job Status & Logs
            DB::table('cus_job_temp')->where('id', $get_job->id)->update([
                'job_status'     => 'cancelled',
                'confirm_status' => 0
            ]);

            DB::table('open_jobs')->where(function ($query) use ($get_job) {
                $query->where('job_no', $get_job->job_no)->orWhere('id', $get_job->id);
            })->update([
                'job_status'     => 'cancelled',
                'confirm_status' => 0
            ]);

            DB::table('job_cancellations')->insert([
                'job_id'       => $get_job->id,
                'customer_id'  => $get_job->user_id ?? 0,
                'cancelled_by' => $cancelledBy,
                'reason'       => $cancelReason,
                'created_at'   => now()
            ]);

            // WhatsApp Notification
            $jobType = $request->job_type ?? 'open';
            $actualUserId = $get_job->user_id;

            $get_u = DB::table($jobType == 'customer' ? 'customer_register' : 'user_register')
                ->where('id', $actualUserId)
                ->where('deletes', '0')
                ->first();

            if ($get_u) {
                $existsWhatsApp = \App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $get_u->mobile]);
                if ($existsWhatsApp) {

                    $sendTemplateMessage = function ($mobile, $templateName, $parameters) use ($request) {
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

                    $pickupDateFormatted = $get_job->pickup_date
                        ? date('d M Y, h:i A', strtotime($get_job->pickup_date))
                        : '-';

                    $sendTemplateMessage(
                        $get_u->mobile,
                        'admin_cancle_jobs',
                        [
                            $get_u->name ?? 'Customer',
                            $get_job->job_no ?? '-',
                            $get_job->from_place ?? '-',
                            $get_job->to_place ?? '-',
                            $pickupDateFormatted
                        ]
                    );
                }
            }

            DB::commit();

            // Dispatch Bidders Notifications
            $jobData = (array) $get_job;
            $biddersIds = $get_bidders_ids;

            dispatch(function () use ($jobData, $biddersIds) {
                // Background execution logic
            });

            return response()->json([
                'status'  => true,
                'type'    => 1,
                'data'    => [],
                'message' => 'Job cancelled successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'type'    => 0,
                'data'    => null,
                'message' => 'An error occurred while cancelling the job: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function admin_pushNotify(Request $request)
    {


        $fcmToken = $this->getFcm([$request->user_id]);

        // return $fcmToken;
        if ($fcmToken) {
            $accessToken = $this->getAccessToken();
            if ($accessToken) {
                foreach ($fcmToken as $token) {
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

    public function sendSpecificUserPushNotificationDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids'   => 'required|array',
            'user_ids.*' => 'integer',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // 1. CAPTURE DATA
        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->admin_id ?? auth()->id();
        $userIds = $request->user_ids;
        $controller = $this;

        // 2. INSERT INITIAL RECORD (STATUS 2 = IN PROGRESS)
        $trackingId = DB::table('push_notifications')->insertGetId([
            'user_id'    => '0',
            'sent_by'    => $adminId,
            'title'      => $title,
            'body'       => $body,
            'status'     => 2, // 2 indicates "Processing/In Progress"
            'req_json'   => json_encode(['target' => 'Drivers', 'user_ids' => $userIds]),
            'res_json'   => json_encode(['status' => 'Processing in background...']),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        // =========================================================================
        // 3. BACKGROUND PROCESS: Runs ONLY AFTER the instant response is sent
        // =========================================================================
        app()->terminating(function () use ($controller, $title, $body, $adminId, $userIds, $trackingId) {

            // Prevent PHP from timing out or running out of memory during huge loops
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $usersToNotify = [];

            // Fetch Drivers ONLY
            $drivers = DB::table('user_register')
                ->whereIn('id', $userIds)
                ->where('deletes', '0')
                ->select('id', 'name', 'fcm_token')
                ->get();

            foreach ($drivers as $d) {
                $usersToNotify[$d->id] = ['id' => $d->id, 'name' => $d->name, 'token' => $d->fcm_token];
            }

            $successCount = 0;
            $failureCount = 0;
            $delivered = [];
            $notDelivered = [];

            if (!empty($usersToNotify)) {
                $accessToken = $controller->getAccessToken();
                if ($accessToken) {
                    // Send notifications and handle personalized {{Name}}
                    foreach ($userIds as $uid) {
                        $user = $usersToNotify[$uid] ?? null;

                        // Handle invalid/deleted users
                        if (!$user) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => 'Unknown', 'error' => 'User not found in DB'];
                            continue;
                        }

                        // Fallback to 'Driver' if name is empty
                        $uName = !empty($user['name']) ? $user['name'] : 'Driver';

                        if (empty($user['token'])) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                            continue;
                        }

                        // REPLACE {{Name}} IN TITLE AND BODY
                        $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                        $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                        try {
                            $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast_specific']);
                            if (isset($response['name'])) {
                                $successCount++;
                                $delivered[] = ['id' => $uid, 'name' => $uName];
                            } else {
                                $failureCount++;
                                $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error'];
                            }
                        } catch (\Throwable $e) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                        }
                    }
                } else {
                    $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'Firebase Token Error'];
                }
            } else {
                $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'No active drivers found.'];
            }

            $resJson = [
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'delivered'     => $delivered,
                'not_delivered' => $notDelivered,
            ];

            // 4. UPDATE THE EXISTING TRACKING RECORD TO NORMAL STATUS
            DB::table('push_notifications')->where('id', $trackingId)->update([
                'status'     => ($successCount > 0) ? 1 : 0,
                'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        // =========================================================================
        // 5. INSTANT FRONTEND RESPONSE: This returns immediately!
        // =========================================================================
        return response()->json([
            'status'  => true,
            'message' => 'Notifications sent to queue successfully!'
        ], 200);
    }

    public function sendSpecificDriverPushNotificationJob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids'   => 'required|array',
            'user_ids.*' => 'integer',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // 1. CAPTURE DATA
        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->admin_id ?? auth()->id();
        $userIds = $request->user_ids;
        $controller = $this;

        // 2. FETCH USERS & TOKENS IMMEDIATELY
        $drivers = DB::table('user_register')
            ->whereIn('id', $userIds)
            ->where('deletes', '0')
            ->select('id', 'name', 'fcm_token')
            ->get();

        $customers = DB::table('customer_register')
            ->whereIn('id', $userIds)
            ->where('deletes', '0')
            ->select('id', 'name', 'fcm_token')
            ->get();

        $usersToNotify = [];
        $isCustomer = false;

        foreach ($drivers as $d) {
            $usersToNotify[$d->id] = ['id' => $d->id, 'name' => $d->name, 'token' => $d->fcm_token];
        }
        foreach ($customers as $c) {
            $usersToNotify[$c->id] = ['id' => $c->id, 'name' => $c->name, 'token' => $c->fcm_token];
            $isCustomer = true; // Flags that target is a Customer
        }

        if (empty($usersToNotify)) {
            return response()->json(['status' => false, 'message' => 'User not found in system.'], 200);
        }

        $targetLabel = $isCustomer ? 'Customer' : 'Driver';

        // 3. PROCESS NOTIFICATIONS SYNCHRONOUSLY
        $successCount = 0;
        $failureCount = 0;
        $delivered = [];
        $notDelivered = [];
        $lastError = 'Unknown Error'; // Fallback error message

        $accessToken = $controller->getAccessToken();

        if (!$accessToken) {
            return response()->json(['status' => false, 'message' => 'Firebase Authentication Failed.'], 200);
        }

        foreach ($userIds as $uid) {
            $user = $usersToNotify[$uid] ?? null;

            if (!$user) {
                $failureCount++;
                $notDelivered[] = ['id' => $uid, 'name' => 'Unknown', 'error' => 'User not found'];
                $lastError = 'User not found';
                continue;
            }

            $uName = !empty($user['name']) ? $user['name'] : 'User';

            // Check if FCM Token exists
            if (empty($user['token'])) {
                $failureCount++;
                $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                $lastError = 'FCM Token not found for ' . $uName; // Specific error for toast
                continue;
            }

            // Replace {{name}} dynamically
            $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
            $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

            try {
                $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast_specific']);

                if (isset($response['name'])) { // 'name' indicates a successful message ID returned from Firebase
                    $successCount++;
                    $delivered[] = ['id' => $uid, 'name' => $uName];
                } else {
                    $failureCount++;
                    $errReason = 'FCM Response Error';
                    // Attempt to extract exact Firebase error message if available
                    if (isset($response['error']['message'])) {
                        $errReason = $response['error']['message'];
                    }
                    $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $errReason];
                    $lastError = 'Push failed: ' . $errReason;
                }
            } catch (\Throwable $e) {
                $failureCount++;
                $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                $lastError = 'Error: ' . $e->getMessage();
            }
        }

        $resJson = [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'delivered'     => $delivered,
            'not_delivered' => $notDelivered,
        ];

        // 4. INSERT FINAL STATUS INTO LOGS
        DB::table('push_notifications')->insert([
            'user_id'    => '0',
            'sent_by'    => $adminId,
            'title'      => $title,
            'body'       => $body,
            'status'     => ($successCount > 0) ? 1 : 0, // 1 = Success, 0 = Failed
            'req_json'   => json_encode(['target' => $targetLabel, 'user_ids' => $userIds]),
            'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        // 5. RETURN EXACT RESULT TO FRONTEND TOAST
        if ($successCount > 0) {
            return response()->json([
                'status'  => true,
                'message' => 'Notification sent successfully!'
            ], 200);
        } else {
            return response()->json([
                'status'  => false,
                'message' => $lastError
            ], 200);
        }
    }

    public function adminCancelWebsiteJob(Request $request)
    {
        try {
            // 1. Retrieve POST parameters
            $jobId = $request->input('job_id');
            $jobNo = $request->input('job_no');

            // Retrieve the new cancellation parameters
            $cancelReason = $request->input('cancel_reason', 'Cancelled by Admin');
            $cancelledBy = $request->input('cancelled_by', null);

            if (!$jobId || !$jobNo) {
                return response()->json([
                    'type' => 0,
                    'result' => 'Missing job ID or Number.'
                ]);
            }

            // 2. Fetch Job Details 
            $job = \Illuminate\Support\Facades\DB::table('cus_job_temp')->where('id', $jobId)->first();

            if (!$job) {
                return response()->json([
                    'type' => 0,
                    'result' => 'Job not found.'
                ]);
            }

            // 3. Extract Customer Data from JSON
            $userDetails = json_decode($job->user_details, true);
            $customerName = $userDetails['name'] ?? 'Customer';
            $customerPhone = $userDetails['mobile'] ?? '';

            // Extract Location Data 
            $pickupLoc = $job->pick_address ?? $job->from_place ?? 'Unknown Location';
            $dropLoc = $job->drop_address ?? $job->to_place ?? 'Unknown Location';

            // Extract & Format Date Safely
            $rawDate = $job->pickup_date ?? $job->day ?? $job->created_at ?? null;
            $formattedDate = 'Not Specified';

            if (!empty($rawDate)) {
                try {
                    $formattedDate = \Carbon\Carbon::parse($rawDate)->format('d-m-Y h:i A');
                } catch (\Throwable $e) {
                    $formattedDate = $rawDate;
                }
            }

            // 4. Update the Job Status (No Deletion)
            \Illuminate\Support\Facades\DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0
                ]);

            // =========================================================================
            // NEW LOGIC: Insert into job_cancellations table
            // =========================================================================
            \Illuminate\Support\Facades\DB::table('job_cancellations')->insert([
                'job_id'       => $jobId,
                'customer_id'  => $job->user_id ?? 0,
                'cancelled_by' => $cancelledBy,
                'reason'       => $cancelReason,
                'created_at'   => \Carbon\Carbon::now()
            ]);
            // =========================================================================

            // 5. Construct & Send the WhatsApp Message
            try {
                if (!empty($customerPhone)) {

                    $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    $existsWhatsApp = \App\Http\Controllers\Controller::checkWhatsApp([
                        'mobile' => $cleanPhone
                    ]);

                    if ($existsWhatsApp) {
                        $messageText = "
Hello {$customerName}, 👋  

Your job **{$jobNo}** has been ❌ *cancelled* By Admin.  

📌 Job Details:  
🔹 Pickup: {$pickupLoc}  
🔹 Drop-off: {$dropLoc}  
🔹 PickUp Date: {$formattedDate}  
";

                        $whatsAppArr = [
                            'mobile' => $cleanPhone,
                            'templateName' => 'national_draw_verification',
                            'language' => 'en',
                            'templateBodyParam' => [],
                            'messages' => $messageText,
                            'resend' => false
                        ];

                        \App\Http\Controllers\Controller::sendNotification($whatsAppArr);
                    }
                }
            } catch (\Throwable $waError) {
                \Illuminate\Support\Facades\Log::error('WhatsApp Error on Website Cancel: ' . $waError->getMessage());
            }

            // 6. Return Success Response
            return response()->json([
                'type' => 1,
                'result' => 'Job cancelled successfully.',
                'status' => true
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'type' => 0,
                'result' => $e->getMessage()
            ]);
        }
    }

    public function adminCancelScheduledJob(Request $request)
    {
        try {
            $jobId = $request->input('job_id');
            $jobNo = $request->input('job_no');
            $cancelReason = $request->input('cancel_reason', 'Cancelled by Admin');
            $cancelledBy = $request->input('cancelled_by', null);

            if (!$jobId || !$jobNo) {
                return response()->json([
                    'type' => 0,
                    'status' => false,
                    'message' => 'Missing job ID or Number.'
                ]);
            }

            $job = \Illuminate\Support\Facades\DB::table('cus_job_temp')->where('id', $jobId)->first();

            if (!$job) {
                return response()->json([
                    'type' => 0,
                    'status' => false,
                    'message' => 'Job not found.'
                ]);
            }

            $customer = \Illuminate\Support\Facades\DB::table('customer_register')->where('id', $job->user_id)->first();
            $customerName = trim($customer->name ?? 'Customer');
            $customerPhone = trim($customer->mobile ?? '');

            $pickupLoc = trim($job->pick_address ?? '');
            if (empty($pickupLoc)) $pickupLoc = trim($job->from_place ?? '');
            if (empty($pickupLoc)) $pickupLoc = 'Unknown Location';

            $dropLoc = trim($job->drop_address ?? '');
            if (empty($dropLoc)) $dropLoc = trim($job->to_place ?? '');
            if (empty($dropLoc)) $dropLoc = 'Unknown Location';

            $rawDate = trim($job->pickup_date ?? '');
            if (empty($rawDate)) $rawDate = trim($job->day ?? '');
            if (empty($rawDate)) $rawDate = trim($job->created_at ?? '');
            
            $formattedDate = 'Not Specified';
            if (!empty($rawDate)) {
                try {
                    $formattedDate = \Carbon\Carbon::parse($rawDate)->format('d-m-Y h:i A');
                } catch (\Throwable $e) {
                    $formattedDate = $rawDate;
                }
            }

            \Illuminate\Support\Facades\DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->update([
                    'job_status' => 'cancelled',
                    'confirm_status' => 0
                ]);

            \Illuminate\Support\Facades\DB::table('job_cancellations')->insert([
                'job_id'       => $jobId,
                'customer_id'  => $job->user_id ?? 0,
                'cancelled_by' => $cancelledBy,
                'reason'       => $cancelReason,
                'created_at'   => \Carbon\Carbon::now()
            ]);

            try {
                if (!empty($customerPhone)) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    if (\App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $cleanPhone])) {
                        $templateName = 'scheduled_job_cancelled';
                        $parameters = [$customerName, $jobNo, $pickupLoc, $dropLoc, $formattedDate];
                        $template = \Illuminate\Support\Facades\DB::table('wamail_templates')->where('name', $templateName)->first();

                        if ($template) {
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

                            \Illuminate\Support\Facades\Log::info("WhatsApp Request Payload [{$templateName}]:", $payload);

                            $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                            $body = $response->json();
                            
                            if (!$response->successful()) {
                                \Illuminate\Support\Facades\Log::error("WhatsApp API Error [{$templateName}]:", ['status' => $response->status(), 'response' => $body]);
                            } else {
                                \Illuminate\Support\Facades\Log::info("WhatsApp API Success [{$templateName}]:", $body);
                            }
                        }
                    }
                }
            } catch (\Throwable $waError) {
                \Illuminate\Support\Facades\Log::error('WhatsApp Error on Scheduled Cancel: ' . $waError->getMessage());
            }

            return response()->json([
                'type' => 1,
                'status' => true,
                'message' => 'Scheduled job cancelled successfully.',
                'result' => 'Scheduled job cancelled successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'type' => 0,
                'status' => false,
                'message' => $e->getMessage(),
                'result' => $e->getMessage()
            ]);
        }
    }
     
    public function adminWithdrawTransfer(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader !== 'Bearer asdfghjklpoiuytrewqzxcvbnm!@$%^&*()') {
            return response()->json(['type' => '0', 'result' => 'Unauthorized access.']);
        }

        $reqId      = $request->input('request');
        $mode       = $request->input('mode');
        $transid    = $request->input('transid');
        $date       = $request->input('date');
        $reasontext = $request->input('reasontext');
        $loginID    = auth()->id() ?? 1;

        if (empty($mode) || empty($transid) || empty($date) || empty($reasontext)) {
            return response()->json(['type' => '0', 'result' => 'Please fill all fields.']);
        }

        $fileNEW = '';
        if ($request->hasFile('filesent')) {
            $file = $request->file('filesent');

            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowed)) {
                return response()->json(['type' => '0', 'result' => 'File format not supported']);
            }
            if ($file->getSize() > 2097152) {
                return response()->json(['type' => '0', 'result' => 'File size must be less than 2MB']);
            }

            try {
                $uploadResponse = \Illuminate\Support\Facades\Http::attach(
                    'image',
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                )->post('https://www.goride.run/api/v1-cus/s3-upload-image', [
                    'img_type' => 'withdrawal_receipt',
                    'auth_key' => env('EXPECTED_API_TOKEN', 'ASDFGHJKLqwertyuiopMNBVCXZ!@#$%^&*()0987612345'),
                    'name'     => 'receipt_' . $reqId
                ]);

                $uploadData = $uploadResponse->json();

                if ($uploadResponse->successful() && isset($uploadData['status']) && $uploadData['status'] === true) {
                    $fileNEW = $uploadData['data']['url'];
                } else {
                    return response()->json([
                        'type' => '0',
                        'result' => 'Image upload failed: ' . ($uploadData['message'] ?? 'Unknown Error')
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json(['type' => '0', 'result' => 'API Upload Error: ' . $e->getMessage()]);
            }
        } else {
            return response()->json(['type' => '0', 'result' => 'Please select a file to upload']);
        }

        $withdraw = \Illuminate\Support\Facades\DB::table('withdraw_request')
            ->where('request_id', $reqId)
            ->where('status', '0')
            ->where('deletes', '0')
            ->first();

        if (!$withdraw) {
            return response()->json(['type' => '0', 'result' => 'Request Not Found or Already Processed']);
        }

        $from_id = $withdraw->requested_by;
        $req_mobile_check = $withdraw->mobile ?? '';
        $withdraw_amount = $withdraw->amount;
        $request_id = $withdraw->request_id;
        $withdraw_pk = $withdraw->id; // Fetched the Primary Key for reference_id

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $tableName = (strpos($request_id, 'WRC-') === 0) ? "customer_register" : "user_register";

            $userQuery = \Illuminate\Support\Facades\DB::table($tableName)->where('id', $from_id)->where('deletes', '0');
            if (!empty($req_mobile_check)) {
                $userQuery->orWhere('mobile', $req_mobile_check);
            }
            $userRow = $userQuery->first();

            if (!$userRow) throw new \Exception("User details not found.");

            $current_balance = (float)($userRow->walletBalance ?? 0);
            $req_fullname = trim(($userRow->name ?? '') . ' ' . ($userRow->lname ?? '')) ?: 'Unknown';
            $req_mobile = $userRow->mobile ?? '';

            // Update withdraw_request table status to 1 (Completed)
            \Illuminate\Support\Facades\DB::table('withdraw_request')
                ->where('request_id', $reqId)
                ->where('deletes', '0')
                ->update([
                    'submited_by' => $loginID,
                    'reasontext' => $reasontext,
                    'trans_mode' => $mode,
                    'attachment_url' => $fileNEW,
                    'transaction_id' => $transid,
                    'trans_date' => $date,
                    'status' => '1',
                    'closingBalance' => $current_balance
                ]);

            // UPDATE WINNING BALANCE HISTORY STATUS TO 1 (Completed)
            \Illuminate\Support\Facades\DB::table('winningBalance_history')
                ->where('reference_id', $withdraw_pk)
                ->where('reference_table', 'withdraw_request')
                ->update([
                    'status' => '1',
                    'updatedon' => now()
                ]);

            if (!empty($req_mobile)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $req_mobile);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '91' . $cleanPhone;
                }

                if (\App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $cleanPhone])) {
                    $templateName = 'withdrawal_success';
                    $parameters = [$req_fullname, $request_id, $withdraw_amount, $mode, $transid];
                    $template = \Illuminate\Support\Facades\DB::table('wamail_templates')->where('name', $templateName)->first();

                    if ($template) {
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

                        \Illuminate\Support\Facades\Log::info("WhatsApp Request Payload [{$templateName}]:", $payload);

                        $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                        $body = $response->json();
                        
                        if (!$response->successful()) {
                            \Illuminate\Support\Facades\Log::error("WhatsApp API Error [{$templateName}]:", ['status' => $response->status(), 'response' => $body]);
                        } else {
                            \Illuminate\Support\Facades\Log::info("WhatsApp API Success [{$templateName}]:", $body);
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['type' => '1', 'result' => 'Transfer completed successfully.']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['type' => '0', 'result' => $e->getMessage()]);
        }
    }

    public function adminWithdrawReject(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader !== 'Bearer asdfghjklpoiuytrewqzxcvbnm!@$%^&*()') {
            return response()->json(['type' => 0, 'result' => 'Unauthorized access.']);
        }

        $reqId  = $request->input('request');
        $reason = $request->input('reason');
        $loginID = auth()->id() ?? 0;
        $dubaidate_time = now();

        if (empty($reqId) || empty($reason)) {
            return response()->json(['type' => 0, 'result' => 'Request ID or Reason missing!']);
        }

        $withdraw = \Illuminate\Support\Facades\DB::table('withdraw_request')
            ->where('request_id', $reqId)
            ->where('deletes', '0')
            ->where('status', '0')
            ->first();

        if (!$withdraw) {
            return response()->json(['type' => 0, 'result' => 'Request ID Not Found or Already Processed!']);
        }

        $user_id  = $withdraw->requested_by;
        $amt      = (float)$withdraw->amount;
        $request_id = $withdraw->request_id;
        $withdraw_pk = $withdraw->id;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $userTable = (strpos($request_id, 'WRC-') === 0) ? 'customer_register' : 'user_register';

            $user = \Illuminate\Support\Facades\DB::table($userTable)->where('id', $user_id)->where('deletes', '0')->first();
            if (!$user) throw new \Exception("User not found.");

            $req_fullname = trim(($user->name ?? '') . ' ' . ($user->lname ?? '')) ?: 'Unknown';
            $req_mobile = $user->mobile ?? '';
            $req_email = $user->email ?? '';
            $open_amt   = (float)($user->walletBalance ?? 0);
            $total_amt  = $open_amt + $amt;

            // Update withdraw_request table status to 2 (Rejected)
            \Illuminate\Support\Facades\DB::table('withdraw_request')
                ->where('request_id', $reqId)
                ->where('deletes', '0')
                ->update([
                    'submited_by' => $loginID,
                    'reasontext' => $reason,
                    'trans_date' => $dubaidate_time,
                    'status' => '2'
                ]);

            // UPDATE WINNING BALANCE HISTORY STATUS TO 2 (Rejected)
            \Illuminate\Support\Facades\DB::table('winningBalance_history')
                ->where('reference_id', $withdraw_pk)
                ->where('reference_table', 'withdraw_request')
                ->update([
                    'status' => '2',
                    'updatedon' => now()
                ]);

            \Illuminate\Support\Facades\DB::table($userTable)
                ->where('id', $user_id)
                ->where('deletes', '0')
                ->update(['walletBalance' => $total_amt]);

            \Illuminate\Support\Facades\DB::table('walletBalance_history')->insert([
                'userid' => $user_id,
                'uname' => $req_fullname,
                'umobile' => $req_mobile,
                'uemail' => $req_email,
                'opening_balance' => $open_amt,
                'total' => $amt,
                'closeing_balance' => $total_amt,
                'transaction_type' => 'CREDIT',
                'reward_type' => 'RE_BANKWITHDRAWAL',
                'reference_id' => $withdraw_pk,
                'reference_table' => 'withdraw_request',
                'ip' => $request->ip(),
                'status' => '1',
                'created_by' => $loginID
            ]);

            if (!empty($req_mobile)) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $req_mobile);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '91' . $cleanPhone;
                }

                if (\App\Http\Controllers\Controller::checkWhatsApp(['mobile' => $cleanPhone])) {
                    $templateName = 'withdrawal_rejected';
                    $parameters = [$req_fullname, $request_id, $amt, $reason];
                    $template = \Illuminate\Support\Facades\DB::table('wamail_templates')->where('name', $templateName)->first();

                    if ($template) {
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

                        \Illuminate\Support\Facades\Log::info("WhatsApp Request Payload [{$templateName}]:", $payload);

                        $response = \Illuminate\Support\Facades\Http::withToken(env('FB_WHATSAPP_TOKEN'))->acceptJson()->post($url, $payload);
                        $body = $response->json();
                        
                        if (!$response->successful()) {
                            \Illuminate\Support\Facades\Log::error("WhatsApp API Error [{$templateName}]:", ['status' => $response->status(), 'response' => $body]);
                        } else {
                            \Illuminate\Support\Facades\Log::info("WhatsApp API Success [{$templateName}]:", $body);
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['type' => '1', 'result' => 'Rejected Successfully & Wallet Refunded']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['type' => '0', 'result' => $e->getMessage()]);
        }
    }

    public function adminWithdrawDeclineForm(Request $request)
    {
        // 1. Basic Auth Verification (Inline)
        $authHeader = $request->header('Authorization');
        if ($authHeader !== 'Bearer asdfghjklpoiuytrewqzxcvbnm!@$%^&*()') {
            return response()->json(['type' => 0, 'result' => 'Unauthorized access.']);
        }

        // 2. Retrieve POST parameters
        $reqId = $request->input('request');
        $mobile = $request->input('mobile');
        $from_name = $request->input('from_name');
        $createdon = $request->input('createdon');

        if (!empty($reqId)) {
            $count = DB::table('withdraw_request')
                ->where('request_id', $reqId)
                ->where('deletes', '0')
                ->where('status', '0')
                ->count();

            if ($count > 0) {
                return response()->json([
                    'type' => 1,
                    'rejectbtn' => '<button class="btn btn-primary" onclick="reject_request(\'' . $reqId . '\',\'' . $mobile . '\',\'' . $from_name . '\',\'' . $createdon . '\')"> Send</button>',
                    'result' => '<textarea class="form-control" id="rejectreasontext" placeholder="Enter the reason for rejection"></textarea>'
                ]);
            }
        }

        return response()->json(['type' => 0, 'result' => 'Request ID Not Found!']);
    }

    public function adminWithdrawList(Request $request)
    {
        // 1. Basic Auth Verification
        $authHeader = $request->header('Authorization');
        if ($authHeader !== 'Bearer asdfghjklpoiuytrewqzxcvbnm!@$%^&*()') {
            return response()->json([]);
        }

        $result = [];
        $now = date('Y-m-d');

        // 2. Retrieve Filters
        $formdate   = $request->input('formdate', '');
        $todate     = $request->input('todate', '');
        $status     = $request->input('status', '');
        $trans_mode = $request->input('trans_mode', '');

        // Initialize Query
        $query = DB::table('withdraw_request')->where('deletes', '0');

        // 3. Apply Filters
        if ($status !== '' && $status !== 'all') {
            if ($status === '0') {
                $query->where(function ($q) {
                    $q->where('status', '0')
                        ->orWhereNull('status')
                        ->orWhere('status', '');
                });
            } else {
                $query->where('status', $status);
            }
        }

        if ($trans_mode !== '' && $trans_mode !== 'all') {
            $query->where('trans_mode', $trans_mode);
        }

        if (!empty($formdate)) {
            $fd = date("Y-m-d", strtotime($formdate));
            $td = (!empty($todate)) ? date("Y-m-d", strtotime($todate)) : $now;
            $query->whereBetween('createdon', ["$fd 00:00:00", "$td 23:59:59"]);
        } else if (empty($formdate) && empty($todate) && ($status === '' || $status === 'all') && ($trans_mode === '' || $trans_mode === 'all')) {
            $query->where('createdon', 'LIKE', "$now%");
        }

        // Fetch Data
        $with_his = $query->orderBy('id', 'desc')->get();

        // Admin Role override
        $roll_id = 1;

        // 4. Process Results
        foreach ($with_his as $value) {
            $req_by = $value->requested_by;
            $request_id = $value->request_id;

            // Determine Table
            if (strpos($request_id, 'WRC-') === 0) {
                $tableName = "customer_register";
                $userType = 'Customer';
            } else {
                $tableName = "user_register";
                $userType = null;
            }

            // Fetch User Data
            $userData = DB::table($tableName)->where('id', $req_by)->first();

            if ($tableName === 'user_register') {
                $kycData = DB::table('kyc_details')->where('user_id', $req_by)->where('deletes', '0')->first();
                if ($kycData && !empty($kycData->type)) {
                    $userType = $kycData->type;
                } else {
                    $roll_id_user = $userData->roll_id ?? 0;
                    if ($roll_id_user != 0) {
                        $role = DB::table('role')->where('id', $roll_id_user)->first();
                        $userType = $role ? $role->name : 'Unknown';
                    } else {
                        $userType = 'Unknown';
                    }
                }
            }

            if (!$userType) $userType = 'Unknown';

            // Extract Data Safely
            $from_name = trim(($userData->name ?? '') . ' ' . ($userData->lname ?? ''));
            if ($from_name == '') $from_name = 'Unknown';

            $mobile = $userData->mobile ?? '';
            $address = $userData->address ?? '';
            $city = $userData->city ?? '';
            $t_earning = $userData->walletBalance ?? '0';
            $idProFront = $userData->idProFront ?? '';
            $idProBack = $userData->idProBack ?? '';
            $nationality = $userData->nationality ?? '';
            $residinglocation = $userData->residinglocation ?? '';

            $upiID = $value->upiID ?? '';
            $to_name = '';

            $createdon = $value->createdon;
            $dateOnly = explode(' ', $createdon)[0];

            // ==========================================
            // FIX: HANDLE S3 / ABSOLUTE URLs CORRECTLY
            // ==========================================
            $attachmentimg = $value->attachment_url ?? '';

            $safeAssetUrl = defined('assetURL') ? constant('assetURL') : rtrim(env('APP_URL'), '/');
            $safeBaseUrl = defined('baseurl') ? constant('baseurl') : rtrim(env('APP_URL'), '/');

            if (str_starts_with($attachmentimg, 'http://') || str_starts_with($attachmentimg, 'https://')) {
                // If it's already a full URL (like S3), use it directly
                $path = $attachmentimg;
            } else {
                // Otherwise, it's a local relative path, so prepend the base URL
                $path = (strpos($attachmentimg, "nationaldraw") === 0)
                    ? $safeAssetUrl . '/' . ltrim($attachmentimg, '/')
                    : $safeBaseUrl . '/' . ltrim($attachmentimg, '/');
            }

            $path1 = $safeAssetUrl . '/' . ltrim($idProFront, '/');
            $path2 = $safeAssetUrl . '/' . ltrim($idProBack, '/');

            // Generate Status & Action HTML
            $statusText = '';
            $action = '';

            if ($value->status == 1) {
                $statusText = 'Success';
            } else if ($value->status == 2) {
                $statusText = 'Reject';
            } else {
                $statusText = 'Process';
            }

            // Pending Actions
            if ($value->status == 0 || empty($value->status)) {
                if (in_array($roll_id, [1, 2, 4])) {
                    $action .= '<a class="btn text-danger btn-sm transfer" style="cursor: pointer;" data-bs-original-title="Transfer"><span class="fa fa-money" onclick="withdrawtransfer(\'' . $request_id . '\', \'' . $value->amount . '\', \'' . $upiID . '\')"> Transfer</span></a>';
                    $action .= '<a class="btn text-danger btn-sm" style="cursor: pointer;" data-bs-original-title="Reject"><span class="fa fa-money" onclick="withdrawDecline(\'' . $request_id . '\',\'' . $mobile . '\',\'' . $from_name . '\',\'' . $dateOnly . '\')"> Reject</span></a>';
                    $action .= '<a class="btn text-danger btn-sm transfer" style="cursor: pointer;" data-bs-original-title="ID Proof"><span class="fa fa-picture-o" style="color: #a4d47a;" onclick="user_id_image(\'' . $request_id . '\', \'' . $path1 . '\', \'' . $path2 . '\')"> Id Proof</span></a>';
                }
            }

            // Completed Actions
            if ($value->status == 1 || $value->status == 2) {
                if ($statusText != 'Reject') {
                    $action .= '<a class="btn text-danger btn-sm transfer" style="cursor: pointer;" data-bs-original-title="Edit"><span class="fa fa-edit" style="color: blue;" onclick="editWithDrawDetails(\'' . $request_id . '\')"> Edit</span></a>';
                }
                $action .= '<a class="btn text-danger btn-sm transfer" style="cursor: pointer;" data-bs-original-title="Bank Details"><span class="fa fa-eye" style="color: #ff0000;" onclick="showUpiDetails(\'' . $upiID . '\')"> Bank Details</span></a>';
                $action .= '<a class="btn text-danger btn-sm transfer" style="cursor: pointer;" data-bs-original-title="ID Proof"><span class="fa fa-picture-o" style="color: #a4d47a;" onclick="user_id_image(\'' . $request_id . '\', \'' . $path1 . '\', \'' . $path2 . '\')"> Id Proof </span></a>';

                // FIXED View Button Link
                if (!empty($attachmentimg)) {
                    $action .= ' <a href="' . $path . '" target="_blank"><button class="btn btn-sm"><i style="color: lime;" class="fa fa-eye"></i> View</button></a>';
                }
            }

            // Append to Result Array
            $result[] = [
                "date" => date("d-M-Y g:i a", strtotime($value->createdon)),
                "user_id" => $req_by,
                "fromname" => ucwords($from_name),
                "type" => ucwords($userType),
                "amt" => $value->amount,
                "wbalance" => $t_earning,
                "Request_Type" => strtolower($value->cus_prefer ?? ''),
                "status" => $statusText,
                "reason" => $value->reasontext,
                "trans_mode" => $value->trans_mode,
                "request_id" => $request_id,
                "trans_date" => $value->trans_date,
                "trans_id" => $value->transaction_id,
                "mobile" => $mobile,
                "bank" => $value->bank_name ?? '',
                "acount_num" => $value->acc_no ?? '',
                "upiId" => $upiID,
                "iban" => $value->iban_code ?? '',
                "swift" => $value->swiftcode ?? '',
                "nation" => $nationality,
                "location" => $residinglocation,
                "toname" => ucwords($to_name),
                "address" => $address,
                "region" => $city,
                "action" => $action
            ];
        }

        return response()->json($result);
    }

    private function compressImage($source, $destination, $quality)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            // Handle transparency for PNG
            imageAlphaBlending($image, true);
            imageSaveAlpha($image, true);
        } else {
            return false;
        }
        imagejpeg($image, $destination, $quality); // Save as JPEG for compression
        return true;
    }

    public function resendPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:push_notifications,id',
            'user_ids'        => 'required|array',
            'user_ids.*'      => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $original = DB::table('push_notifications')->find($request->notification_id);
        if (!$original) {
            return response()->json(['status' => false, 'message' => 'Original notification not found.']);
        }

        $title = $original->title ?? '';
        $body  = $original->body ?? '';

        // FIX: Target is stored inside the JSON column, not as a direct property
        $reqJsonRaw = json_decode($original->req_json, true) ?: [];
        $target = $reqJsonRaw['target'] ?? '';

        $imageUrl = $original->image_url ?? '';
        $userIds = $request->user_ids;
        $originalId = $original->id;
        $originalResJsonStr = $original->res_json;

        $controller = $this;

        DB::table('push_notifications')->where('id', $originalId)->update([
            'status'     => 2,
            'updated_at' => now(),
        ]);

        app()->terminating(function () use ($controller, $title, $body, $userIds, $originalId, $originalResJsonStr, $target, $imageUrl) {
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $usersToNotify = [];
            $targetLower = strtolower($target);

            // FIX: Robustly check the target type to query the correct table
            if (strpos($targetLower, 'driver') !== false || strpos($targetLower, 'rm') !== false) {
                $users = DB::table('user_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
            } elseif (strpos($targetLower, 'customer') !== false) {
                $users = DB::table('customer_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
            } else {
                // Fallback for "Both" or if target is purely ambiguous
                $drivers = DB::table('user_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
                $customers = DB::table('customer_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
                $users = $drivers->merge($customers);
            }

            foreach ($users as $u) {
                $usersToNotify[$u->id] = ['id' => $u->id, 'name' => $u->name, 'token' => $u->fcm_token];
            }

            $accessToken = $controller->getAccessToken();
            if (!$accessToken) {
                DB::table('push_notifications')->where('id', $originalId)->update(['status' => 0]);
                return;
            }

            $newDelivered = [];
            $newNotDelivered = [];
            $allAttemptedIds = [];

            // Add the domain URL if an image exists
            $absoluteImageUrl = null;
            if (!empty($imageUrl)) {
                $absoluteImageUrl = 'https://admin.goride.net.in/' . $imageUrl;
            }

            foreach ($userIds as $uid) {
                $user = $usersToNotify[$uid] ?? null;
                $allAttemptedIds[] = $uid;

                if (!$user) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => 'Unknown', 'error' => 'User not found in Database'];
                    continue;
                }

                $uName = !empty($user['name']) ? $user['name'] : 'User';

                if (empty($user['token'])) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                    continue;
                }

                $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                try {
                    $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_resend', 'image' => $absoluteImageUrl]);
                    if (isset($response['name'])) {
                        $newDelivered[] = ['id' => $uid, 'name' => $uName];
                    } else {
                        $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error'];
                    }
                } catch (\Throwable $e) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                }
            }

            $currentResJson = json_decode($originalResJsonStr, true) ?: [];
            $currentDelivered = $currentResJson['delivered'] ?? [];
            $currentNotDelivered = $currentResJson['not_delivered'] ?? [];

            $currentNotDelivered = array_filter($currentNotDelivered, function ($item) use ($allAttemptedIds) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item;
                return !in_array($id, $allAttemptedIds);
            });

            $currentDelivered = array_filter($currentDelivered, function ($item) use ($allAttemptedIds) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item;
                return !in_array($id, $allAttemptedIds);
            });

            $finalDelivered = array_values(array_merge($currentDelivered, $newDelivered));
            $finalNotDelivered = array_values(array_merge($currentNotDelivered, $newNotDelivered));

            $overallStatus = (count($finalDelivered) > 0) ? 1 : 0;

            $updatedResJson = [
                'success_count' => count($finalDelivered),
                'failure_count' => count($finalNotDelivered),
                'delivered'     => $finalDelivered,
                'not_delivered' => $finalNotDelivered,
            ];

            DB::table('push_notifications')->where('id', $originalId)->update([
                'status'     => $overallStatus,
                'res_json'   => json_encode($updatedResJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['status'  => true, 'message' => "Resend Notifications Sent!."]);
    }

    public function sendAdminPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target_user' => 'required_without:user_ids|in:driver,customer,both',
            'user_ids'    => 'sometimes|array',
            'user_ids.*'  => 'integer|exists:user_register,id',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->admin_id ?? auth()->id();
        $targetUser = $request->target_user ?? null;
        $userIds = $request->user_ids ?? [];
        $hasUserIds = $request->has('user_ids');
        $controller = $this;

        // --- FIXED IMAGE UPLOAD DIRECTORY PATH ---
        $imageUrl = null;
        $absoluteImageUrl = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.jpg';

            // Hardcoded absolute path pointing directly to the admin domain's file manager
            $destinationPath = '/home/goridenetincwdin/admin.goride.net.in/uploads/push_notification_images';

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $sourcePath = $image->getPathname();
            $targetPath = $destinationPath . '/' . $imageName;

            // Compress, fallback to direct move if GD library fails
            if (!$this->compressImage($sourcePath, $targetPath, 60)) {
                $image->move($destinationPath, $imageName);
            }

            $imageUrl = 'uploads/push_notification_images/' . $imageName;

            // Absolute URL explicitly pointing to the admin domain for Firebase FCM
            $absoluteImageUrl = 'https://admin.goride.net.in/' . $imageUrl;
        }

        $trackingId = DB::table('push_notifications')->insertGetId([
            'user_id'    => '0',
            'sent_by'    => $adminId,
            'title'      => $title,
            'body'       => $body,
            'image_url'  => $imageUrl,
            'status'     => 2,
            'req_json'   => json_encode(['target' => $targetUser, 'user_ids' => $userIds]),
            'res_json'   => json_encode(['status' => 'Processing in background...']),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        app()->terminating(function () use ($controller, $title, $body, $hasUserIds, $userIds, $targetUser, $trackingId, $absoluteImageUrl) {
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $usersToNotify = [];

            if ($hasUserIds && is_array($userIds)) {
                $drivers = DB::table('user_register')->whereIn('id', $userIds)->where('deletes', '0')->whereNotNull('fcm_token')->where('fcm_token', '!=', '')->select('id', 'name', 'fcm_token')->get();
                $customers = DB::table('customer_register')->whereIn('id', $userIds)->where('deletes', '0')->whereNotNull('fcm_token')->where('fcm_token', '!=', '')->select('id', 'name', 'fcm_token')->get();

                foreach ($drivers as $d) {
                    $usersToNotify[$d->fcm_token] = ['id' => $d->id, 'name' => $d->name, 'token' => $d->fcm_token];
                }
                foreach ($customers as $c) {
                    $usersToNotify[$c->fcm_token] = ['id' => $c->id, 'name' => $c->name, 'token' => $c->fcm_token];
                }
            } else {
                if ($targetUser === 'driver' || $targetUser === 'both') {
                    $drivers = DB::table('user_register')->whereNotNull('fcm_token')->where('fcm_token', '!=', '')->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
                    foreach ($drivers as $driver) {
                        $usersToNotify[$driver->fcm_token] = ['id' => $driver->id, 'name' => $driver->name, 'token' => $driver->fcm_token];
                    }
                }
                if ($targetUser === 'customer' || $targetUser === 'both') {
                    $customers = DB::table('customer_register')->whereNotNull('fcm_token')->where('fcm_token', '!=', '')->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
                    foreach ($customers as $customer) {
                        $usersToNotify[$customer->fcm_token] = ['id' => $customer->id, 'name' => $customer->name, 'token' => $customer->fcm_token];
                    }
                }
            }

            $successCount = 0;
            $failureCount = 0;
            $delivered = [];
            $notDelivered = [];

            if (!empty($usersToNotify)) {
                $accessToken = $controller->getAccessToken();
                if ($accessToken) {
                    foreach ($usersToNotify as $user) {
                        $uName = !empty($user['name']) ? $user['name'] : 'User';
                        $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                        $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                        try {
                            $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast', 'image' => $absoluteImageUrl]);
                            if (isset($response['name'])) {
                                $successCount++;
                                $delivered[] = ['id' => $user['id'], 'name' => $uName];
                            } else {
                                $failureCount++;
                                $notDelivered[] = ['id' => $user['id'], 'name' => $uName, 'error' => 'FCM Response Error'];
                            }
                        } catch (\Throwable $e) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $user['id'], 'name' => $uName, 'error' => $e->getMessage()];
                        }
                    }
                } else {
                    $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'Firebase Token Error'];
                }
            } else {
                $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'No active users found.'];
            }

            $resJson = ['success_count' => $successCount, 'failure_count' => $failureCount, 'delivered' => $delivered, 'not_delivered' => $notDelivered];

            DB::table('push_notifications')->where('id', $trackingId)->update([
                'status'     => ($successCount > 0) ? 1 : 0,
                'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['status' => true, 'message' => 'Notifications Sent Successfully!'], 200);
    }

    public function sendSpecificUserPushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids'   => 'required|array',
            'user_ids.*' => 'integer',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->admin_id ?? auth()->id();
        $userIds = $request->user_ids;
        $controller = $this;

        // --- FIXED IMAGE UPLOAD DIRECTORY PATH ---
        $imageUrl = null;
        $absoluteImageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.jpg';

            // Hardcoded absolute path pointing directly to the admin domain's file manager
            $destinationPath = '/home/goridenetincwdin/admin.goride.net.in/uploads/push_notification_images';

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $sourcePath = $image->getPathname();
            $targetPath = $destinationPath . '/' . $imageName;

            if (!$this->compressImage($sourcePath, $targetPath, 60)) {
                $image->move($destinationPath, $imageName);
            }

            $imageUrl = 'uploads/push_notification_images/' . $imageName;
            $absoluteImageUrl = 'https://admin.goride.net.in/' . $imageUrl;
        }

        $trackingId = DB::table('push_notifications')->insertGetId([
            'user_id'    => '0',
            'sent_by'    => $adminId,
            'title'      => $title,
            'body'       => $body,
            'image_url'  => $imageUrl,
            'status'     => 2,
            'req_json'   => json_encode(['target' => 'RM Users', 'user_ids' => $userIds]),
            'res_json'   => json_encode(['status' => 'Processing in background...']),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        app()->terminating(function () use ($controller, $title, $body, $adminId, $userIds, $trackingId, $absoluteImageUrl) {
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $usersToNotify = [];

            $drivers = DB::table('user_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
            $customers = DB::table('customer_register')->whereIn('id', $userIds)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();

            foreach ($drivers as $d) {
                $usersToNotify[$d->id] = ['id' => $d->id, 'name' => $d->name, 'token' => $d->fcm_token];
            }
            foreach ($customers as $c) {
                $usersToNotify[$c->id] = ['id' => $c->id, 'name' => $c->name, 'token' => $c->fcm_token];
            }

            $successCount = 0;
            $failureCount = 0;
            $delivered = [];
            $notDelivered = [];

            if (!empty($usersToNotify)) {
                $accessToken = $controller->getAccessToken();
                if ($accessToken) {
                    foreach ($userIds as $uid) {
                        $user = $usersToNotify[$uid] ?? null;

                        if (!$user) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => 'Unknown', 'error' => 'User not found in DB'];
                            continue;
                        }

                        $uName = !empty($user['name']) ? $user['name'] : 'User';

                        if (empty($user['token'])) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                            continue;
                        }

                        $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                        $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                        try {
                            $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast_specific', 'image' => $absoluteImageUrl]);
                            if (isset($response['name'])) {
                                $successCount++;
                                $delivered[] = ['id' => $uid, 'name' => $uName];
                            } else {
                                $failureCount++;
                                $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error'];
                            }
                        } catch (\Throwable $e) {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                        }
                    }
                } else {
                    $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'Firebase Token Error'];
                }
            } else {
                $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'No active users found.'];
            }

            $resJson = ['success_count' => $successCount, 'failure_count' => $failureCount, 'delivered' => $delivered, 'not_delivered' => $notDelivered];

            DB::table('push_notifications')->where('id', $trackingId)->update([
                'status'     => ($successCount > 0) ? 1 : 0,
                'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['status'  => true, 'message' => 'Notifications Sent Successfully!'], 200);
    }

    public function resendPushNotificationDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer|exists:push_notifications,id',
            'user_ids'        => 'required|array',
            'user_ids.*'      => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // Fetch original notification
        $original = DB::table('push_notifications')->find($request->notification_id);
        if (!$original) {
            return response()->json(['status' => false, 'message' => 'Original notification not found.']);
        }

        // CAPTURE DATA FOR BACKGROUND PROCESSING
        $title = $original->title ?? '';
        $body  = $original->body ?? '';
        $target = $original->target ?? ''; // Capture target
        $userIds = $request->user_ids;
        $originalId = $original->id;
        $originalResJsonStr = $original->res_json;

        $controller = $this;

        // =========================================================================
        // 1. UPDATE RECORD TO "IN PROGRESS" (STATUS = 2) IMMEDIATELY
        // =========================================================================
        DB::table('push_notifications')->where('id', $originalId)->update([
            'status'     => 2,
            'updated_at' => now(),
        ]);

        // =========================================================================
        // 2. BACKGROUND PROCESS: Runs ONLY AFTER the instant response is sent
        // =========================================================================
        app()->terminating(function () use ($controller, $title, $body, $userIds, $originalId, $originalResJsonStr, $target) {

            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $usersToNotify = [];

            // Select table (Always user_register)
            $users = DB::table('user_register')
                ->whereIn('id', $userIds)
                ->where('deletes', '0')
                ->select('id', 'name', 'fcm_token')
                ->get();

            foreach ($users as $u) {
                $usersToNotify[$u->id] = ['id' => $u->id, 'name' => $u->name, 'token' => $u->fcm_token];
            }

            $accessToken = $controller->getAccessToken();
            if (!$accessToken) {
                // Failsafe: Revert status if Firebase auth completely fails
                DB::table('push_notifications')->where('id', $originalId)->update(['status' => 0]);
                return;
            }

            $newDelivered = [];
            $newNotDelivered = [];
            $allAttemptedIds = [];

            // Send notifications
            foreach ($userIds as $uid) {
                $user = $usersToNotify[$uid] ?? null;
                $allAttemptedIds[] = $uid;

                // Handle invalid/deleted users gracefully
                if (!$user) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => 'Unknown', 'error' => 'User not found in Database'];
                    continue;
                }

                $uName = !empty($user['name']) ? $user['name'] : 'User';

                if (empty($user['token'])) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                    continue;
                }

                $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                try {
                    $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_resend']);
                    if (isset($response['name'])) {
                        $newDelivered[] = ['id' => $uid, 'name' => $uName];
                    } else {
                        $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error'];
                    }
                } catch (\Throwable $e) {
                    $newNotDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                }
            }

            // Update existing notification's res_json (merge)
            $currentResJson = json_decode($originalResJsonStr, true) ?: [];
            $currentDelivered = $currentResJson['delivered'] ?? [];
            $currentNotDelivered = $currentResJson['not_delivered'] ?? [];

            // Remove these users from old arrays to avoid duplicates
            $currentNotDelivered = array_filter($currentNotDelivered, function ($item) use ($allAttemptedIds) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item;
                return !in_array($id, $allAttemptedIds);
            });

            $currentDelivered = array_filter($currentDelivered, function ($item) use ($allAttemptedIds) {
                $id = is_array($item) ? ($item['id'] ?? null) : $item;
                return !in_array($id, $allAttemptedIds);
            });

            // Merge the new attempts in and re-index arrays
            $finalDelivered = array_values(array_merge($currentDelivered, $newDelivered));
            $finalNotDelivered = array_values(array_merge($currentNotDelivered, $newNotDelivered));

            $overallStatus = (count($finalDelivered) > 0) ? 1 : 0;

            $updatedResJson = [
                'success_count' => count($finalDelivered),
                'failure_count' => count($finalNotDelivered),
                'delivered'     => $finalDelivered,
                'not_delivered' => $finalNotDelivered,
            ];

            // 3. FINAL DB UPDATE (REMOVES "IN PROGRESS" STATUS)
            DB::table('push_notifications')->where('id', $originalId)->update([
                'status'     => $overallStatus,
                'res_json'   => json_encode($updatedResJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        // =========================================================================
        // 4. INSTANT FRONTEND RESPONSE
        // =========================================================================
        return response()->json([
            'status'  => true,
            'message' => "Resend Notifications Sent!."
        ]);
    }

    function formatReadableDate($datetime)
    {
        return \Carbon\Carbon::parse($datetime)->format('d M Y, h:i A');
    }

    public function deleteFacebookTemplate(Request $request)
    {
        $request->validate([
            'template_id' => 'required|numeric'
        ]);

        $template = DB::table('wamail_templates')->where('id', $request->template_id)->first();

        if (!$template) {
            return response()->json(['status' => false, 'message' => 'Template not found in local database.'], 404);
        }

        // 1. Delete from Facebook if it was sent there
        if (!empty($template->whatsapp_template_id)) {
            $fbTemplateName = strtolower(str_replace(' ', '_', $template->name));

            $wabaId = env('FB_WHATSAPP_BUSINESS_ACCOUNT_ID');
            $version = env('FB_WHATSAPP_VERSION', 'v24.0');
            $token = env('FB_WHATSAPP_TOKEN');

            $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates?name={$fbTemplateName}";

            try {
                $response = Http::withToken($token)->delete($url);
                $body = $response->json();

                // If Facebook blocks the deletion (e.g., template is in use or already deleted),
                // we catch the error but STILL proceed to delete it from your local database below.
                if (!$response->successful() && isset($body['error'])) {
                    \Log::warning("FB Delete Warning: " . $body['error']['message']);
                }
            } catch (\Exception $e) {
                // Log network errors but don't crash
                \Log::error('FB Delete Exception: ' . $e->getMessage());
            }
        }

        // 2. FIX: Hard delete the template from your database completely
        DB::table('wamail_templates')->where('id', $template->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Template successfully deleted from Whatsapp.'
        ]);
    }

    private function getMetaFileHandleFromUrl($imageUrl)
    {
        try {
            // Using .env with direct fallbacks based on your configuration
            $appId = env('FB_APP_ID', '2107858870045418');
            $version = env('FB_WHATSAPP_VERSION', 'v24.0');
            $token = env('FB_WHATSAPP_TOKEN');

            // 1. Download the image using cURL (Bypasses allow_url_fopen=0 server restrictions)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Prevents local SSL handshake errors
            $fileData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // If the cURL download fails, log it and abort
            if (!$fileData || $httpCode !== 200) {
                \Log::error('Meta Upload: Failed to fetch image via cURL. HTTP Code: ' . $httpCode . ' URL: ' . $imageUrl);
                return null;
            }

            $fileSize = strlen($fileData);

            // Determine mime type from URL extension
            $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            $mimeType = 'image/jpeg'; // Default
            if ($extension === 'png') $mimeType = 'image/png';
            if ($extension === 'webp') $mimeType = 'image/webp';

            // 2. Step 1: Start Upload Session
            $sessionUrl = "https://graph.facebook.com/{$version}/{$appId}/uploads";

            $sessionResponse = Http::post($sessionUrl, [
                'file_name' => 'header_image.' . $extension,
                'file_length' => $fileSize,
                'file_type' => $mimeType,
                'access_token' => $token
            ]);

            $sessionId = $sessionResponse->json('id');
            if (!$sessionId) {
                \Log::error('Meta Upload Session Failed: ' . $sessionResponse->body());
                return null;
            }

            // 3. Step 2: Upload the actual binary data using cURL for robust transfer
            $uploadUrl = "https://graph.facebook.com/{$version}/{$sessionId}";

            $chUpload = curl_init();
            curl_setopt($chUpload, CURLOPT_URL, $uploadUrl);
            curl_setopt($chUpload, CURLOPT_POST, 1);
            curl_setopt($chUpload, CURLOPT_POSTFIELDS, $fileData);
            curl_setopt($chUpload, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($chUpload, CURLOPT_HTTPHEADER, [
                'Authorization: OAuth ' . $token,
                'file_offset: 0'
            ]);
            $uploadResult = curl_exec($chUpload);
            curl_close($chUpload);

            $uploadResponse = json_decode($uploadResult, true);
            return $uploadResponse['h'] ?? null;
        } catch (\Exception $e) {
            \Log::error('Meta Resumable Upload Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function requestTemplateApproval(Request $request)
    {
        $request->validate([
            'template_id' => 'required|numeric'
        ]);

        $template = DB::table('wamail_templates')->where('id', $request->template_id)->first();

        if (!$template) {
            return response()->json(['status' => false, 'message' => 'Template not found'], 404);
        }

        $fbTemplateName = strtolower(str_replace(' ', '_', $template->name));

        // .env with direct fallbacks
        $wabaId = env('FB_WHATSAPP_BUSINESS_ACCOUNT_ID', '1293805358722420');
        $version = env('FB_WHATSAPP_VERSION', 'v24.0');
        $token = env('FB_WHATSAPP_TOKEN');

        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates";

        // 1. Clean body text using advanced formatting logic
        $cleanBody = $template->body;
        $cleanBody = preg_replace('/<\/p>|<\/div>/i', "\n\n", $cleanBody);
        $cleanBody = preg_replace('/<br\s*\/?>/i', "\n", $cleanBody);
        $cleanBody = strip_tags($cleanBody);
        $cleanBody = html_entity_decode($cleanBody, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cleanBody = preg_replace("/[ \t]+/", " ", $cleanBody);
        $cleanBody = preg_replace("/\n{3,}/", "\n\n", $cleanBody);
        $cleanBody = trim($cleanBody);
        $cleanBody = html_entity_decode($cleanBody, ENT_QUOTES, 'UTF-8');

        $bodyComponent = [
            "type" => "BODY",
            "text" => $cleanBody
        ];

        // 2. Handle Dynamic Variables in BODY
        if ($template->m_type === 'dynamic' && $template->var_count > 0) {
            $exampleArray = [];
            for ($i = 1; $i <= $template->var_count; $i++) {
                $oldVar = "{{VAR{$i}}}";
                $newVar = "{{{$i}}}";
                $bodyComponent['text'] = str_replace($oldVar, $newVar, $bodyComponent['text']);
                $exampleArray[] = "SampleText" . $i;
            }
            $bodyComponent['example'] = [
                "body_text" => [$exampleArray]
            ];
        }

        $components = [$bodyComponent];

        // 3. --- DYNAMIC BUTTONS PARSING LOGIC ---
        if (!empty($template->variables_json)) {
            $buttonsConfig = json_decode($template->variables_json, true);

            if (isset($buttonsConfig['type']) && $buttonsConfig['type'] !== 'none' && !empty($buttonsConfig['buttons'])) {
                $fbButtonsArray = [];

                foreach ($buttonsConfig['buttons'] as $btn) {
                    if ($btn['type'] === 'QUICK_REPLY') {
                        $fbButtonsArray[] = [
                            "type" => "QUICK_REPLY",
                            "text" => mb_substr($btn['text'], 0, 25)
                        ];
                    } elseif ($btn['type'] === 'URL') {
                        $fbButtonsArray[] = [
                            "type" => "URL",
                            "text" => mb_substr($btn['text'], 0, 25),
                            "url" => $btn['url']
                        ];
                    } elseif ($btn['type'] === 'PHONE_NUMBER') {
                        $fbButtonsArray[] = [
                            "type" => "PHONE_NUMBER",
                            "text" => mb_substr($btn['text'], 0, 25),
                            "phone_number" => $btn['phone_number']
                        ];
                    }
                }

                if (count($fbButtonsArray) > 0) {
                    $components[] = [
                        "type" => "BUTTONS",
                        "buttons" => $fbButtonsArray
                    ];
                }
            }
        }
        // --- END BUTTONS LOGIC ---

        // 4. --- IMAGE HEADER LOGIC ---
        if (!empty($template->header_image)) {

            // Generate the Meta Handle by uploading our local image to Facebook
            $metaHandle = $this->getMetaFileHandleFromUrl($template->header_image);

            if ($metaHandle) {
                $headerComponent = [
                    "type" => "HEADER",
                    "format" => "IMAGE",
                    "example" => [
                        "header_handle" => [
                            $metaHandle // This is the h:: string Facebook requires
                        ]
                    ]
                ];
                // Meta prefers the HEADER to be the first element in the components array
                array_unshift($components, $headerComponent);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to generate Meta Handle. Please check the Application Logs for exact Meta API response.'
                ], 400);
            }
        }
        // --- END IMAGE HEADER LOGIC ---

        // 5. Final Payload mapped for Meta
        $payload = [
            "name" => $fbTemplateName,
            "category" => strtoupper($template->channel ?? 'UTILITY'),
            "language" => "en_US",
            "components" => $components
        ];

        // 6. Send to Facebook
        try {
            $response = Http::withToken($token)->acceptJson()->post($url, $payload);
            $body = $response->json();

            if ($response->successful() && isset($body['id'])) {
                $fbStatus = isset($body['status']) ? strtolower($body['status']) : 'in review';

                DB::table('wamail_templates')->where('id', $template->id)->update([
                    'whatsapp_template_id' => $body['id'],
                    'approval_status' => $fbStatus
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Template submitted! Status: ' . strtoupper($fbStatus)
                ]);
            } else {
                $errorMsg = $body['error']['message'] ?? 'Unknown Error';
                $errorDetails = $body['error']['error_user_msg'] ?? '';

                return response()->json([
                    'status' => false,
                    'message' => 'Facebook Error: ' . $errorMsg . ' ' . $errorDetails
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function syncTemplateStatus(Request $request)
    {
        $request->validate([
            'template_id' => 'required|numeric'
        ]);

        $template = DB::table('wamail_templates')->where('id', $request->template_id)->first();

        if (!$template || !$template->whatsapp_template_id) {
            return response()->json(['status' => false, 'message' => 'Template not found or not submitted to FB yet.'], 404);
        }

        $fbTemplateName = strtolower(str_replace(' ', '_', $template->name));

        $wabaId = env('FB_WHATSAPP_BUSINESS_ACCOUNT_ID');
        $version = env('FB_WHATSAPP_VERSION', 'v24.0');
        $token = env('FB_WHATSAPP_TOKEN');

        // Endpoint to GET template status
        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates?name={$fbTemplateName}";

        try {
            $response = Http::withToken($token)->get($url);
            $body = $response->json();

            if ($response->successful() && isset($body['data'][0])) {
                $fbData = $body['data'][0];

                $status = strtolower($fbData['status']); // pending, approved, or rejected
                $reason = $fbData['rejected_reason'] ?? null;

                // Extract the category (type) from the Facebook response
                $category = $fbData['category'] ?? null;

                // Prepare the array for database update
                $updatePayload = [
                    'approval_status' => $status,
                    'rejection_reason' => $reason
                ];

                // If Facebook returns a category, include it to update the 'channel' column
                if ($category) {
                    $updatePayload['channel'] = $category; // e.g., 'MARKETING', 'UTILITY'
                }

                DB::table('wamail_templates')->where('id', $template->id)->update($updatePayload);

                return response()->json([
                    'status' => true,
                    'approval_status' => $status,
                    'channel' => $category, // Included in response for frontend visibility
                    'message' => 'Template status is now: ' . strtoupper($status) . ($reason ? " (Reason: $reason)" : '')
                ]);
            }

            return response()->json(['status' => false, 'message' => 'Template not found on Facebook'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function sampleMess(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'template_name' => 'required',
            'message_body' => 'required'
        ]);

        $mobiles = array_filter(array_map('trim', explode(',', $request->mobile)));

        if (empty($mobiles)) {
            return response()->json(['status' => false, 'message' => 'No valid mobile numbers provided.']);
        }

        $url = "https://graph.facebook.com/" .
            env('FB_WHATSAPP_VERSION', 'v24.0') . "/" .
            env('FB_WHATSAPP_PHONE_NUMBER_ID') . "/messages";

        $template = DB::table('wamail_templates')->where('name', $request->template_name)->first();

        $bodyParameters = [];
        if ($request->has('parameters') && is_array($request->parameters)) {
            foreach ($request->parameters as $param) {
                if ($param !== null && $param !== '') {
                    $bodyParameters[] = [
                        "type" => "text",
                        "text" => (string) $param
                    ];
                }
            }
        }

        $components = [];

        if ($template && !empty($template->header_image)) {
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

        // FIX: Inject BUTTON parameters into payload specifically for OTP/Copy Code
        if ($template && !empty($template->variables_json)) {
            $buttonsConfig = json_decode($template->variables_json, true);
            if (!empty($buttonsConfig['buttons'])) {
                foreach ($buttonsConfig['buttons'] as $index => $btn) {

                    // OTP Copy Code button strict requirement
                    if ($btn['type'] === 'COPY_CODE') {
                        $otpCode = $request->parameters[0] ?? current($request->parameters) ?? '123456';
                        $components[] = [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => (string)$index, // Meta requires the index as a string
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string)$otpCode
                                ]
                            ]
                        ];
                    }

                    // Dynamic URL button strict requirement
                    if ($btn['type'] === 'URL' && strpos($btn['url'] ?? '', '{{1}}') !== false) {
                        $dynamicUrlVal = $request->parameters[0] ?? current($request->parameters) ?? '';
                        $components[] = [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => (string)$index,
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string)$dynamicUrlVal
                                ]
                            ]
                        ];
                    }
                }
            }
        }

        $templatePayload = [
            "name" => $request->template_name,
            "language" => [
                "code" => $request->template_language ?? "en"
            ]
        ];

        if (!empty($components)) {
            $templatePayload["components"] = $components;
        }

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($mobiles as $mob) {
            $cleanMobile = preg_replace('/[^0-9]/', '', $mob);

            if (empty($cleanMobile)) continue;

            $payload = [
                "messaging_product" => "whatsapp",
                "to" => $cleanMobile,
                "type" => "template",
                "template" => $templatePayload
            ];

            $reqTime = now();

            $response = Http::withToken(env('FB_WHATSAPP_TOKEN'))
                ->acceptJson()
                ->post($url, $payload);

            $resTime = now();
            $body = $response->json();
            $messageId = $body['messages'][0]['id'] ?? null;

            $isSuccess = $response->successful();

            if ($isSuccess) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = $cleanMobile . ': ' . ($body['error']['message'] ?? 'Unknown Error') . ' - ' . ($body['error']['error_user_msg'] ?? '');
            }

            DB::table('smslog')->insert([
                'gateway' => 'fbWhatsapp',
                'subject' => substr($request->message_body, 0, 200),
                'details' => $request->message_body,
                'mobile' => $cleanMobile,
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
        }

        $summaryMessage = "Sent successfully to $successCount numbers.";
        if ($failCount > 0) {
            $summaryMessage .= " Failed for $failCount numbers.";
        }

        return response()->json([
            'status' => $successCount > 0 || $failCount === 0,
            'message' => $summaryMessage,
            'errors' => $errors
        ]);
    }

    public function fetchMissingTemplates(Request $request)
    {
        $wabaId = env('FB_WHATSAPP_BUSINESS_ACCOUNT_ID');
        $version = env('FB_WHATSAPP_VERSION', 'v24.0');
        $token = env('FB_WHATSAPP_TOKEN');

        // Fetch all templates from Meta (limit 1000 ensures we get most of them)
        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates?limit=1000";

        try {
            $response = Http::withToken($token)->get($url);
            if (!$response->successful()) {
                return response()->json(['status' => false, 'message' => 'Failed to fetch from Meta: ' . $response->body()]);
            }

            $fbTemplates = $response->json()['data'] ?? [];

            // Get all local template names (lowercase for safe comparison)
            $localTemplates = DB::table('wamail_templates')->pluck('name')->map(function ($name) {
                return strtolower(trim($name));
            })->toArray();

            $missingTemplates = [];

            foreach ($fbTemplates as $tpl) {
                $fbName = strtolower(trim($tpl['name']));
                // If the template from Facebook does not exist in our local DB, add it to the missing list
                if (!in_array($fbName, $localTemplates)) {
                    $missingTemplates[] = $tpl;
                }
            }

            return response()->json([
                'status' => true,
                'templates' => $missingTemplates
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function syncSelectedTemplates(Request $request)
    {
        $templates = $request->input('templates');

        if (empty($templates)) {
            return response()->json(['status' => false, 'message' => 'No templates provided for syncing.']);
        }

        $insertedCount = 0;

        try {
            foreach ($templates as $tpl) {
                $name = $tpl['name'];
                $channel = $tpl['category'] ?? 'UTILITY';
                $status = strtolower($tpl['status'] ?? 'approved');
                $fbId = $tpl['id'];

                $bodyText = '';
                $buttonsJson = '';
                $headerImage = '';

                // Parse Meta's component array back into our DB format
                if (isset($tpl['components'])) {
                    foreach ($tpl['components'] as $comp) {
                        if ($comp['type'] === 'BODY') {
                            $bodyText = $comp['text'] ?? '';
                        }

                        if ($comp['type'] === 'HEADER' && isset($comp['format']) && $comp['format'] === 'IMAGE') {
                            if (isset($comp['example']) && is_array($comp['example'])) {
                                $headerImage = $comp['example']['header_url'][0] ?? ($comp['example']['header_handle'][0] ?? '');
                            }
                        }

                        // FIX: Added robust catch for COPY_CODE and unknown buttons
                        if ($comp['type'] === 'BUTTONS' && isset($comp['buttons'])) {
                            $btnType = 'none';
                            $btns = [];
                            foreach ($comp['buttons'] as $b) {
                                if ($b['type'] === 'QUICK_REPLY') {
                                    $btnType = 'QUICK_REPLY';
                                    $btns[] = ['type' => 'QUICK_REPLY', 'text' => $b['text']];
                                } elseif ($b['type'] === 'URL') {
                                    $btnType = 'CALL_TO_ACTION';
                                    $btns[] = ['type' => 'URL', 'text' => $b['text'] ?? '', 'url' => $b['url'] ?? ''];
                                } elseif ($b['type'] === 'PHONE_NUMBER') {
                                    $btnType = 'CALL_TO_ACTION';
                                    $btns[] = ['type' => 'PHONE_NUMBER', 'text' => $b['text'] ?? '', 'phone_number' => $b['phone_number'] ?? ''];
                                } elseif ($b['type'] === 'COPY_CODE') {
                                    $btnType = 'COPY_CODE';
                                    $btns[] = ['type' => 'COPY_CODE', 'text' => $b['text'] ?? 'Copy code', 'example' => $b['example'] ?? ''];
                                } else {
                                    $btnType = $b['type'];
                                    $btns[] = ['type' => $b['type'], 'text' => $b['text'] ?? 'Button'];
                                }
                            }
                            if (count($btns) > 0) {
                                $buttonsJson = json_encode(['type' => $btnType, 'buttons' => $btns], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            }
                        }
                    }
                }

                if (empty($buttonsJson)) {
                    $buttonsJson = json_encode(['type' => 'none', 'buttons' => []]);
                }

                $formattedBody = '';
                if (!empty($bodyText)) {
                    $formattedBody = '<p>' . nl2br(htmlspecialchars($bodyText)) . '</p>';
                }

                preg_match_all('/\{\{\d+\}\}/', $bodyText, $matches);
                $varCount = count($matches[0]);
                $mType = $varCount > 0 ? 'dynamic' : 'static';

                $exists = DB::table('wamail_templates')->where('name', $name)->exists();

                if (!$exists) {
                    DB::table('wamail_templates')->insert([
                        'name' => $name,
                        'channel' => $channel,
                        'body' => $formattedBody,
                        'variables_json' => $buttonsJson,
                        'header_image' => $headerImage,
                        'media_url' => '',
                        'whatsapp_template_id' => $fbId,
                        'approval_status' => $status,
                        'is_active' => 1,
                        'created_by' => 0,
                        'm_type' => $mType,
                        'var_count' => $varCount,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $insertedCount++;
                }
            }

            return response()->json(['status' => true, 'message' => "Successfully synced $insertedCount templates."]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'DB Error: ' . $e->getMessage()], 500);
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
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => $this->serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $claimSetEncoded = base64_encode(json_encode($claimSet));
        $signatureInput = "$header.$claimSetEncoded";

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
            'assertion' => $jwt
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

    public function getFcm($id = null, $loc = null)
    {

        if ($id) {
            $get_tokens = DB::table('user_register')
                ->whereIn('id', $id)
                ->where('deletes', '0')
                // ->when($loc, function($query) use ($loc) {
                //         $query->where(function($q) use ($loc) {
                //             $q->whereNull('prefered_location')
                //               ->orWhere('prefered_location->location', '')
                //               ->orWhere('prefered_location->location', 'LIKE', '%' . $loc . '%');
                //         });
                //     })
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
                ->where('id', '!=', $request->u_id)
                ->when($loc, function ($query) use ($loc) {
                    $query->where(function ($q) use ($loc) {
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
                'notification' => [ // Required for mobile push
                    'title' => $title,
                    'body' => $body,
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
                                'body' => $body,
                            ],
                            'sound' => 'custom_notification.wav',
                            'badge' => 1
                        ]
                    ]
                ],
                'data' => $stringData
            ]
        ];

        // --- THE FIX: DYNAMICALLY ATTACH THE IMAGE IF IT EXISTS ---
        if (isset($data['image']) && !empty($data['image'])) {
            // 1. Standard notification image
            $payload['message']['notification']['image'] = $data['image'];

            // 2. Android specific rich notification image
            $payload['message']['android']['notification']['image'] = $data['image'];

            // 3. iOS (APNs) specific rich notification setup
            $payload['message']['apns']['payload']['aps']['mutable-content'] = 1; // Tells iOS to download the image
            $payload['message']['apns']['fcm_options']['image'] = $data['image'];
        }

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

    private function parseFirestoreFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $key => $value) {

            if (isset($value['stringValue'])) {
                $result[$key] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $result[$key] = (int) $value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $result[$key] = (float) $value['doubleValue'];
            } elseif (isset($value['booleanValue'])) {
                $result[$key] = (bool) $value['booleanValue'];
            } elseif (isset($value['timestampValue'])) {
                $result[$key] = \Carbon\Carbon::parse(
                    $value['timestampValue'],
                    'UTC'
                )->setTimezone(config('app.timezone'))->toDateTimeString();
            } elseif (isset($value['mapValue']['fields'])) {
                // Recursive parse for maps (like bids)
                $result[$key] = $this->parseFirestoreFields(
                    $value['mapValue']['fields']
                );
            } elseif (isset($value['arrayValue']['values'])) {
                $result[$key] = array_map(function ($v) {
                    return $this->parseFirestoreFields([$v])[0] ?? null;
                }, $value['arrayValue']['values']);
            } else {
                $result[$key] = null;
            }
        }

        return $result;
    }

    public function sendMobilePushNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_numbers' => 'required|string',
            'user_type'      => 'required|in:driver,customer,both',
            'title'          => 'required|string|max:255',
            'body'           => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $title = $request->title;
        $body  = $request->body;
        $adminId = $request->admin_id ?? auth()->id();
        $userType = $request->user_type;
        $controller = $this;

        // Clean up mobile numbers (split by comma, remove spaces/empty values)
        $mobilesRaw = explode(',', $request->mobile_numbers);
        $mobiles = array_filter(array_map('trim', $mobilesRaw));

        // --- IMAGE UPLOAD LOGIC ---
        $imageUrl = null;
        $absoluteImageUrl = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.jpg';

            $destinationPath = '/home/goridenetincwdin/admin.goride.net.in/uploads/push_notification_images';

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $sourcePath = $image->getPathname();
            $targetPath = $destinationPath . '/' . $imageName;

            if (!$this->compressImage($sourcePath, $targetPath, 60)) {
                $image->move($destinationPath, $imageName);
            }

            $imageUrl = 'uploads/push_notification_images/' . $imageName;
            $absoluteImageUrl = 'https://admin.goride.net.in/' . $imageUrl;
        }

        // --- FETCH USERS BASED ON MOBILE NUMBERS ---
        $userIds = [];
        $usersToNotify = [];
        $targetDesc = 'Specific Users (' . ucfirst($userType) . 's)';

        if ($userType === 'driver' || $userType === 'both') {
            $drivers = DB::table('user_register')->whereIn('mobile', $mobiles)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
            foreach ($drivers as $d) {
                $usersToNotify[$d->id] = ['id' => $d->id, 'name' => $d->name, 'token' => $d->fcm_token];
                $userIds[] = $d->id;
            }
        }

        if ($userType === 'customer' || $userType === 'both') {
            $customers = DB::table('customer_register')->whereIn('mobile', $mobiles)->where('deletes', '0')->select('id', 'name', 'fcm_token')->get();
            foreach ($customers as $c) {
                $usersToNotify[$c->id] = ['id' => $c->id, 'name' => $c->name, 'token' => $c->fcm_token];
                $userIds[] = $c->id;
            }
        }

        if (empty($usersToNotify)) {
            return response()->json(['status' => false, 'message' => 'No active users found with the provided mobile numbers.']);
        }

        $trackingId = DB::table('push_notifications')->insertGetId([
            'user_id'    => '0',
            'sent_by'    => $adminId,
            'title'      => $title,
            'body'       => $body,
            'image_url'  => $imageUrl,
            'status'     => 2,
            'req_json'   => json_encode(['target' => $targetDesc, 'user_ids' => $userIds]),
            'res_json'   => json_encode(['status' => 'Processing in background...']),
            'created_at' => now(),
            'updated_at' => now(),
            'deletes'    => 0,
        ]);

        app()->terminating(function () use ($controller, $title, $body, $adminId, $userIds, $usersToNotify, $trackingId, $absoluteImageUrl) {
            set_time_limit(0);
            ini_set('memory_limit', '512M');

            $successCount = 0;
            $failureCount = 0;
            $delivered = [];
            $notDelivered = [];

            $accessToken = $controller->getAccessToken();
            if ($accessToken) {
                foreach ($userIds as $uid) {
                    $user = $usersToNotify[$uid] ?? null;
                    if (!$user) continue;

                    $uName = !empty($user['name']) ? $user['name'] : 'User';

                    if (empty($user['token'])) {
                        $failureCount++;
                        $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Token Missing'];
                        continue;
                    }

                    $personalizedTitle = str_ireplace('{{name}}', $uName, $title);
                    $personalizedBody  = str_ireplace('{{name}}', $uName, $body);

                    try {
                        $response = $controller->sendFCM($accessToken, $user['token'], $personalizedTitle, $personalizedBody, ['type' => 'admin_broadcast_specific', 'image' => $absoluteImageUrl]);
                        if (isset($response['name'])) {
                            $successCount++;
                            $delivered[] = ['id' => $uid, 'name' => $uName];
                        } else {
                            $failureCount++;
                            $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => 'FCM Response Error'];
                        }
                    } catch (\Throwable $e) {
                        $failureCount++;
                        $notDelivered[] = ['id' => $uid, 'name' => $uName, 'error' => $e->getMessage()];
                    }
                }
            } else {
                $notDelivered[] = ['id' => 0, 'name' => 'System', 'error' => 'Firebase Token Error'];
            }

            $resJson = ['success_count' => $successCount, 'failure_count' => $failureCount, 'delivered' => $delivered, 'not_delivered' => $notDelivered];

            DB::table('push_notifications')->where('id', $trackingId)->update([
                'status'     => ($successCount > 0) ? 1 : 0,
                'res_json'   => json_encode($resJson, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        });

        return response()->json(['status'  => true, 'message' => 'Notifications Sent Successfully!'], 200);
    }

    public function adminUpdateJob(Request $request)
    {
        try {
            $request->validate([
                'job_id'      => 'required',
                'job_no'      => 'required',
                'c_name'      => 'required|string',
                'c_mobile'    => 'required',
                'from_place'  => 'required',
                'to_place'    => 'required',
                'pickup_date' => 'required',
                'fare'        => 'required|numeric',
                'tax'         => 'nullable|numeric',
            ]);

            $jobId = $request->job_id;
            $jobNo = $request->job_no;

            $oldJob = \Illuminate\Support\Facades\DB::table('cus_job_temp')
                ->where('id', $jobId)
                ->where('deletes', '0')
                ->first();

            if (!$oldJob) {
                return response()->json(['status' => false, 'message' => 'Job not found or deleted.'], 404);
            }

            $taxAmount = (float) $request->input('tax', 0);
            $baseFare  = (float) $request->input('base_fare', 0);
            $tollFare  = (float) $request->input('toll_fare', 0);
            $totalFare = (float) $request->input('fare', 0);
            $passCount = (int) $request->input('pass_count', 1);

            $jobType = $request->job_type === 'round_trip' ? 'roundtrip' : 'oneway';

            $cabNameMap = [
                'mini'         => 'Go Mini',
                'four_seater'  => 'Go Sedan',
                'six_seater'   => 'Go 6Seaters',
                'seven_seater' => 'Go 7Seaters',
            ];
            $mappedCabType = $cabNameMap[$request->cab_type] ?? $request->cab_type;

            $userDetails = [
                'name'       => $request->c_name,
                'email'      => $request->c_email ?? '',
                'mobile'     => $request->c_mobile,
                'pass_count' => $passCount,
                'lugg_count' => (int)($request->lugg_count ?? 0),
                'cab_type'   => $mappedCabType,
            ];

            $changes = [];
            $fieldsToCheck = [
                'from_place'  => $request->from_place,
                'to_place'    => $request->to_place,
                'pickup_date' => date('Y-m-d H:i:s', strtotime($request->pickup_date)),
                'distance'    => (int)$request->distance,
                'duration'    => $request->duration,
                'base_fare'   => $baseFare,
                'toll_fare'   => $tollFare,
                'tax'         => $taxAmount,
                'without_tax' => $baseFare,
                'fare'        => $totalFare,
                'job_type'    => $jobType,
                'pass_count'  => $passCount,
            ];

            foreach ($fieldsToCheck as $field => $newValue) {
                $oldValue = $oldJob->$field ?? '';
                $oldStr = trim((string)$oldValue);
                $newStr = trim((string)$newValue);

                if (empty($oldStr) && empty($newStr)) continue;
                if ($field === 'pickup_date' && date('Y-m-d H:i', strtotime($oldStr)) === date('Y-m-d H:i', strtotime($newStr))) continue;
                if (in_array($field, ['base_fare', 'toll_fare', 'fare', 'distance', 'tax', 'without_tax', 'pass_count']) && floatval($oldStr) === floatval($newStr)) continue;

                if ($oldStr !== $newStr) {
                    $changes[$field] = ['old' => $oldStr, 'new' => $newStr];
                }
            }

            $oldUserDetails = json_decode($oldJob->user_details ?? '{}', true);
            $newUserFields = [
                'name'       => $request->c_name,
                'mobile'     => $request->c_mobile,
                'cab_type'   => $mappedCabType,
                'pass_count' => $passCount,
                'lugg_count' => (int)($request->lugg_count ?? 0),
            ];

            foreach ($newUserFields as $field => $newValue) {
                $oldValue = $oldUserDetails[$field] ?? '';
                $oldStr = trim((string)$oldValue);
                $newStr = trim((string)$newValue);
                if (empty($oldStr) && empty($newStr)) continue;
                if ($oldStr !== $newStr) {
                    $changes["customer_$field"] = ['old' => $oldStr, 'new' => $newStr];
                }
            }

            $updateData = [
                'job_type'       => $jobType,
                'day'            => $request->filled('day') ? $request->day : '',
                'dropoff_date'   => $request->filled('dropoff_date') ? $request->dropoff_date : '',
                'from_place'     => $request->from_place,
                'to_place'       => $request->to_place,
                'pickup_date'    => date('Y-m-d H:i:s', strtotime($request->pickup_date)),
                'distance'       => $request->filled('distance') ? (int)$request->distance : 0,
                'duration'       => $request->filled('duration') ? $request->duration : null,
                'base_fare'      => $baseFare,
                'toll_fare'      => $tollFare,
                'tax'            => $taxAmount,
                'without_tax'    => $baseFare,
                'fare'           => $totalFare,
                'pass_count'     => $passCount,
                'updated_at'     => now(),
            ];

            if (!empty($oldJob->fare_breakdown) && $oldJob->fare_breakdown !== 'null' && $oldJob->fare_breakdown !== '{}') {
                $fareBreakdown = json_decode($oldJob->fare_breakdown, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($fareBreakdown)) {
                    $fareBreakdown['tax']        = $taxAmount;
                    $fareBreakdown['base_fare']  = $baseFare;
                    $fareBreakdown['toll_fare']  = $tollFare;
                    $fareBreakdown['total_fare'] = $totalFare;
                    $fareBreakdown['pass_count'] = $passCount;
                    $updateData['fare_breakdown'] = json_encode($fareBreakdown);
                }
            }

            if (!empty($oldJob->user_details) && $oldJob->user_details !== 'null' && $oldJob->user_details !== '{}') {
                $updateData['user_details'] = json_encode($userDetails);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($jobId, $jobNo, $updateData, $changes, $oldJob, $request) {
                \Illuminate\Support\Facades\DB::table('cus_job_temp')->where('id', $jobId)->update($updateData);

                if (!empty($changes)) {
                    \Illuminate\Support\Facades\DB::table('job_edit_logs')->insert([
                        'job_id'       => $jobId,
                        'job_no'       => $jobNo,
                        'edited_by'    => $request->admin_id ?? auth()->id() ?? 1,
                        'edit_details' => json_encode($changes),
                        'created_at'   => now()
                    ]);
                }

                if ((empty($oldJob->user_details) || $oldJob->user_details === 'null' || $oldJob->user_details === '{}') && $oldJob->user_id > 0) {
                    $userDataToUpdate = [
                        'name'       => $request->c_name,
                        'email'      => $request->c_email ?? '',
                        'mobile'     => $request->c_mobile,
                        'updated_at' => now()
                    ];

                    if (strpos($jobNo, 'GRD') === 0) {
                        \Illuminate\Support\Facades\DB::table('user_register')->where('id', $oldJob->user_id)->update($userDataToUpdate);
                    } else {
                        \Illuminate\Support\Facades\DB::table('customer_register')->where('id', $oldJob->user_id)->update($userDataToUpdate);
                    }
                }
            });

            $projectId = $this->serviceAccount['project_id'] ?? env('FIREBASE_PROJECT_ID', 'goride-947ed');
            $accessToken = $this->getAccessToken();

            $firebaseUpdateData = [
                'job_type'       => ['stringValue'  => (string)$jobType],
                'day'            => ['stringValue'  => (string)($request->day ?? '')],
                'dropoff_date'   => ['stringValue'  => (string)($request->dropoff_date ?? '')],
                'from_place'     => ['stringValue'  => (string)$request->from_place],
                'to_place'       => ['stringValue'  => (string)$request->to_place],
                'fare'           => ['stringValue'  => (string)$totalFare],
                'base_fare'      => ['stringValue'  => (string)$baseFare],
                'toll_fare'      => ['stringValue'  => (string)$tollFare],
                'tax'            => ['stringValue'  => (string)$taxAmount],
                'without_tax'    => ['stringValue'  => (string)$baseFare],
                'distance'       => ['integerValue' => (string)(int)($request->filled('distance') ? $request->distance : 0)],
                'duration'       => ['stringValue'  => (string)($request->duration ?? '')],
                'poster_name'    => ['stringValue'  => (string)$request->c_name],
                'pickup_date'    => ['timestampValue' => date('Y-m-d\TH:i:s\Z', strtotime($request->pickup_date))],
                'u_mobile'       => ['stringValue'  => (string)$request->c_mobile],
                'mobile'         => ['stringValue'  => (string)$request->c_mobile],
                'cab_type'       => ['stringValue'  => (string)$mappedCabType],
                'pass_count'     => ['integerValue' => (string)$passCount],
                'luggage'        => ['integerValue' => (string)(int)($request->lugg_count ?? 0)],
                'lugg_count'     => ['integerValue' => (string)(int)($request->lugg_count ?? 0)],
                'user_details'   => ['stringValue'  => json_encode($userDetails)],
            ];

            if (isset($updateData['fare_breakdown'])) {
                $firebaseUpdateData['fare_breakdown'] = ['stringValue' => $updateData['fare_breakdown']];
            }

            $updateMask = [];
            foreach (array_keys($firebaseUpdateData) as $field) {
                $updateMask[] = "updateMask.fieldPaths=" . $field;
            }
            $queryString = implode('&', $updateMask);
            $payload = json_encode(['fields' => (object)$firebaseUpdateData]);

            $collectionsToUpdate = ['jobs', 'open_jobs', 'open_jobs_customer'];

            $multiHandle = curl_multi_init();
            $curlHandles = [];

            foreach ($collectionsToUpdate as $collection) {
                $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}/{$jobNo}?{$queryString}";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]);

                curl_multi_add_handle($multiHandle, $ch);
                $curlHandles[] = $ch;
            }

            $isRunning = null;
            do {
                curl_multi_exec($multiHandle, $isRunning);
            } while ($isRunning);

            foreach ($curlHandles as $ch) {
                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
            }
            curl_multi_close($multiHandle);

            return response()->json(['status' => true, 'message' => 'Job updated successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
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
        $tolng) 
    {
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

    public function getExpiredJobs(Request $request)
    {
        try {
            $startDate = $request->input('startDate');
            $endDate   = $request->input('endDate');
            $filterType = $request->input('filterType');

            $col_jb = ($filterType == 'pickup') ? 'pickup_date' : 'created_at';
            $currentTime = \Carbon\Carbon::now('Asia/Kolkata');

            // 1. Base Query for Expired Jobs
            $query = \Illuminate\Support\Facades\DB::table('cus_job_temp')
                ->where('deletes', '0')
                ->whereNotIn('job_status', ['accept', 'accepted', 'completed', 'cancelled','started'])
                ->where('pickup_date', '<', $currentTime)
                // Add the grouped OR condition here
                ->where(function($q) {
                    $q->where('job_no', 'not like', 'GRP-%')
                      ->orWhere('global_type', '!=', 'carpool');
                });

            // Apply Date Range
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween($col_jb, ["$startDate 00:00:00", "$endDate 23:59:59"]);
            }

            $rawJobs = $query->orderByDesc('created_at')->get();

            if ($rawJobs->isEmpty()) {
                return response()->json([
                    'type'   => 0,
                    'result' => [],
                    'data'   => []
                ]);
            }

            // 2. Collect IDs to prevent ALL N+1 queries
            $driverIds = [];
            $customerOwnerIds = [];
            $userOwnerIds = [];
            $jobIds = []; // 🚀 OPTIMIZATION: Collect Job IDs for bulk push notification fetch

            foreach ($rawJobs as $item) {
                $job = (array) $item;

                if (!empty($job['id'])) {
                    $jobIds[] = (int) $job['id'];
                }

                // Collect owner IDs
                $userId = (int)($job['user_id'] ?? 0);
                if ($userId > 0) {
                    $gType = strtolower($job['global_type'] ?? '');
                    if (in_array($gType, ['customer', 'schedule'])) {
                        $customerOwnerIds[] = $userId;
                    } else {
                        $userOwnerIds[] = $userId;
                    }
                }

                // Collect Bidder IDs
                if (!empty($job['bids_details']) && is_string($job['bids_details'])) {
                    $bids = json_decode($job['bids_details'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($bids)) {
                        $driverIds = array_merge($driverIds, array_keys($bids));
                    }
                }

                // Collect Schedule Request Driver IDs
                if (!empty($job['sch_status']) && is_string($job['sch_status'])) {
                    $schData = json_decode($job['sch_status'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($schData)) {
                        foreach ($schData as $date => $bidders) {
                            if (is_array($bidders)) {
                                $driverIds = array_merge($driverIds, array_keys($bidders));
                            }
                        }
                    }
                }
            }

            // Bulk Fetch Users & Customers
            $driverNames = [];
            $driverMobiles = [];
            $allUserRegisterIds = array_unique(array_merge($driverIds, $userOwnerIds));

            if (!empty($allUserRegisterIds)) {
                $users = \Illuminate\Support\Facades\DB::table('user_register')
                    ->whereIn('id', $allUserRegisterIds)
                    ->select('id', 'name', 'mobile')
                    ->get();
                foreach ($users as $u) {
                    $driverNames[$u->id] = $u->name;
                    $driverMobiles[$u->id] = $u->mobile;
                }
            }

            $customerDetails = [];
            if (!empty($customerOwnerIds)) {
                $customerDetails = \Illuminate\Support\Facades\DB::table('customer_register')
                    ->whereIn('id', array_unique($customerOwnerIds))
                    ->select('id', 'name', 'mobile')
                    ->get()
                    ->keyBy('id')
                    ->toArray();
            }

            // 🚀 OPTIMIZATION: Bulk Fetch Push Notifications
            $pushCounts = [];
            if (!empty($jobIds)) {
                $chunks = array_chunk(array_unique($jobIds), 50); // Chunk to keep query size safe
                foreach ($chunks as $chunk) {
                    $pushQuery = \Illuminate\Support\Facades\DB::table('push_notifications');

                    $pushQuery->where(function ($q) use ($chunk) {
                        foreach ($chunk as $jid) {
                            $q->orWhere('req_json', 'LIKE', '%"job_id": ' . $jid . '%')
                                ->orWhere('req_json', 'LIKE', '%"job_id":' . $jid . '%');
                        }
                    });

                    // Ordering by ascending overwrites our array with the newest record automatically
                    $records = $pushQuery->orderBy('id', 'asc')->get(['req_json', 'res_json']);

                    foreach ($records as $record) {
                        foreach ($chunk as $jid) {
                            if (strpos($record->req_json, '"job_id": ' . $jid) !== false || strpos($record->req_json, '"job_id":' . $jid) !== false) {
                                $resData = !empty($record->res_json) ? json_decode($record->res_json, true) : [];
                                if (isset($resData['success_count'])) {
                                    $pushCounts[$jid] = (int)$resData['success_count'];
                                }
                            }
                        }
                    }
                }
            }

            // 3. Map and Transform Data
            $jobs = $rawJobs->map(function ($item) use ($driverNames, $driverMobiles, $customerDetails, $pushCounts) {
                $job = (array) $item;
                $gType = strtolower($job['global_type'] ?? '');

                // --- DECODE USER DETAILS ---
                $originalUserDetails = $job['user_details'] ?? '';
                $details = [];

                if (!empty($originalUserDetails) && is_string($originalUserDetails) && $originalUserDetails !== 'null') {
                    $details = json_decode($originalUserDetails, true) ?: [];
                }

                // Resolve Owner Name & Mobile Fallbacks
                $userId = (int)($job['user_id'] ?? 0);
                $dbName = '';
                $dbMobile = '';

                if ($userId > 0) {
                    if (in_array($gType, ['customer', 'schedule']) && isset($customerDetails[$userId])) {
                        $dbName = $customerDetails[$userId]->name;
                        $dbMobile = $customerDetails[$userId]->mobile;
                    } elseif (isset($driverNames[$userId])) {
                        $dbName = $driverNames[$userId];
                        $dbMobile = $driverMobiles[$userId];
                    }
                }

                $finalName = !empty($dbName) ? $dbName : (!empty($details['name']) ? $details['name'] : (!empty($job['name']) ? $job['name'] : 'Customer'));
                $finalMobile = !empty($dbMobile) ? $dbMobile : (!empty($details['mobile']) ? $details['mobile'] : (!empty($job['mobile']) ? $job['mobile'] : ''));
                $cleanMobile = preg_replace('/[^0-9]/', '', $finalMobile);

                $job['lugg_count'] = $job['lugg_count'] ?? $details['lugg_count'] ?? 0;
                $job['pass_count'] = $job['pass_count'] ?? $details['pass_count'] ?? 1;
                $job['cab_type'] = $job['cab_type'] ?? $details['cab_type'] ?? '';
                $job['name'] = $finalName;
                $job['poster_name'] = $finalName;
                $job['u_name'] = $finalName;
                $job['mobile'] = $cleanMobile;
                $job['phone_no'] = $cleanMobile;
                $job['u_mobile'] = $cleanMobile;

                if (empty($details)) {
                    $job['user_details'] = '';
                } else {
                    $job['user_details'] = $originalUserDetails;
                }

                // --- DECODE FARE BREAKDOWN ---
                if (!empty($job['fare_breakdown']) && is_string($job['fare_breakdown'])) {
                    $fb = json_decode($job['fare_breakdown'], true);
                    if (is_array($fb) && !empty($fb['total_fare'])) {
                        $job['fare'] = (float) $fb['total_fare']; // Strict casting
                    }
                }

                $baseVal = !empty($job['base_fare']) ? (float) $job['base_fare'] : 0;
                $tollVal = !empty($job['toll_fare']) ? (float) $job['toll_fare'] : 0;

                $job['base_fare'] = ($baseVal > 0) ? $baseVal : "Included";
                $job['toll_fare'] = ($tollVal > 0) ? $tollVal : "Included";

                // --- DECODE BIDS DETAILS ---
                if (!empty($job['bids_details']) && is_string($job['bids_details'])) {
                    $bids = json_decode($job['bids_details'], true);
                    if (is_array($bids)) {
                        foreach ($bids as $driverId => &$bidInfo) {
                            $bidInfo['b_name'] = $driverNames[$driverId] ?? 'Unknown Driver';
                        }
                        $job['bids_details'] = $bids;
                    } else {
                        $job['bids_details'] = [];
                    }
                } else {
                    $job['bids_details'] = [];
                }

                // --- DECODE SCHEDULE STATUS ---
                $job['sch_amount'] = 0;
                $job['sch_status_data'] = [];

                if (!empty($job['sch_status']) && is_string($job['sch_status'])) {
                    $schData = json_decode($job['sch_status'], true);
                    if (is_array($schData)) {
                        foreach ($schData as $date => $bidders) {
                            if (is_array($bidders)) {
                                foreach ($bidders as $bidderId => $bData) {
                                    if (!empty($bData['amount']) && (float)$bData['amount'] > 0) {
                                        $job['sch_amount'] += (float) $bData['amount'];
                                    }
                                    $job['sch_status_data'][] = [
                                        'date' => $date,
                                        'driver_id' => $bidderId,
                                        'driver_name' => $driverNames[$bidderId] ?? 'Unknown Driver',
                                        'driver_mobile' => $driverMobiles[$bidderId] ?? '',
                                        'status' => $bData['status'] ?? 'pending'
                                    ];
                                }
                            }
                        }
                    }
                }

                if ($gType === 'schedule') {
                    if ($job['sch_amount'] > 0) {
                        $job['fare'] = $job['sch_amount'];
                    }
                    $job['base_fare'] = null;
                    $job['toll_fare'] = null;
                    $job['tax'] = null;
                    $job['driver_bata'] = null;
                }

                // 🚀 OPTIMIZATION: Instant O(1) Push Notification Lookup
                $job['count'] = 0;
                $searchJobId = (int)($job['id'] ?? 0);
                if ($searchJobId > 0 && isset($pushCounts[$searchJobId])) {
                    $job['count'] = $pushCounts[$searchJobId];
                }

                return $job;
            });

            return response()->json([
                'type'   => $jobs->isNotEmpty() ? 1 : 0,
                'result' => $jobs->values()->all(),
                'data'   => []
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'type'   => 0,
                'result' => $e->getMessage(),
                'data'   => []
            ], 500);
        }
    }

    private function applyFilters(array $job, array $filters): bool
    {
        /* ===============================
           1. JOB NO FILTER
        =============================== */
        if (
            !empty($filters['job_no']) &&
            ($job['job_no'] ?? null) !== $filters['job_no']
        ) {
            return false;
        }

        /* ===============================
           2. JOB TYPE FILTER
        =============================== */
        if (
            !empty($filters['jobType']) &&
            ($job['job_type'] ?? null) !== $filters['jobType']
        ) {
            return false;
        }

        // if ($job['global_type'] != 'customer') {
        //     continue;
        // }

        /* ===============================
           3. JOB SOURCE FILTER
        =============================== */
        if (!empty($filters['jobFrom'])) {

            $source = strtolower($job['global_type'] ?? '');

            if ($filters['jobFrom'] === 'customer' && $source !== 'customer') {
                return false;
            }

            if ($filters['jobFrom'] !== 'customer' && $source === 'customer') {
                return false;
            }
        }

        $filterType = $filters['filterType'] ?? 'booked';

        $jobDateRaw = $filterType == 'pickup'
            ? ($job['pickup_date'] ?? null)
            : ($job['created_at'] ?? null);

        // if (!$jobDateRaw) {
        //     return false;
        // }

        $jobDate = date('Y-m-d', strtotime($jobDateRaw));
        $today   = date('Y-m-d');

        if (empty($filters['startDate']) && empty($filters['endDate'])) {

            // Always apply on pickup date
            $pickupDateRaw = $job['pickup_date'] ?? null;
            if (!$pickupDateRaw) {
                return false;
            }

            $pickupDate = date('Y-m-d', strtotime($pickupDateRaw));

            if ($pickupDate < $today) {
                return false;
            }
        } else {

            $start = $filters['startDate'] ?? $today;
            $end   = $filters['endDate'] ?? $today;

            if ($jobDate < $start || $jobDate > $end) {
                return false;
            }
        }

        if (!empty($filters['jobStatus'])) {
            return $this->matchStatus($job, $filters['jobStatus']);
        }

        return true;
    }

    protected function matchStatus(array $job, string $status): bool
    {
        $current = $job['job_status'] ?? '';
        $pickup = strtotime($job['pickup_date'] ?? '');
        $deleted = $job['deletes'] ?? '0';
        $now = time();

        return match ($status) {
            'not_complete' =>
            in_array($current, ['created', 'bidding'], true),

            'bidding' => $current === 'bidding',
            'accepted' => $current === 'accept',
            'cancelled' => $current === 'cancelled',

            'expired' =>
            $pickup < $now &&
                in_array($current, ['created', 'bidding'], true),

            'deleted' =>
            $deleted === '1',

            default => true
        };
    }

    protected function attachUsers(array $jobs): array
    {
        $collection = collect($jobs);

        $customerIds = $collection
            ->where('global_type', 'customer')
            ->pluck('user_id')
            ->filter(fn($id) => is_numeric($id) && $id > 0)
            ->unique()
            ->values();

        $userIds = $collection
            ->where('global_type', '!=', 'customer')
            ->pluck('user_id')
            ->filter(fn($id) => is_numeric($id) && $id > 0)
            ->unique()
            ->values();

        $customers = DB::table('customer_register as cr')
            ->whereIn('cr.id', $customerIds->isNotEmpty() ? $customerIds : [0])
            ->select('cr.id', 'cr.name', 'cr.mobile', DB::raw("'Customer' as type"))
            ->get()
            ->keyBy('id');

        $users = DB::table('user_register as ur')
            ->leftJoin('kyc_details as kr', 'ur.id', '=', 'kr.user_id')
            ->whereIn('ur.id', $userIds->isNotEmpty() ? $userIds : [0])
            ->select(
                'ur.id',
                'ur.name',
                'ur.mobile',
                'kr.type as type',
                'kr.id as kd_id'
            )
            ->get()
            ->keyBy('id');

        $jobNos = $collection->pluck('job_no')->filter()->unique()->values();

        $previewHashes = DB::table('cus_job_temp')
            ->whereIn('job_no', $jobNos->isNotEmpty() ? $jobNos : [''])
            ->select('job_no', 'preview_hash')
            ->get()
            ->keyBy('job_no');

        $missingHashes = [];

        foreach ($jobs as &$job) {

            $uid = $job['user_id'] ?? 0;
            $type = $job['global_type'] ?? '';
            $jobNo = $job['job_no'] ?? null;

            $job['bid_count'] = (isset($job['bids_details']) && is_array($job['bids_details']))
                ? count($job['bids_details'])
                : 0;

            $job['preview_hash'] = $previewHashes[$jobNo]->preview_hash ?? null;

            $userData = $type === 'customer'
                ? ($customers[$uid] ?? null)
                : ($users[$uid] ?? null);

            if ($userData) {

                $job += (array) $userData;

                // generate missing hash safely
                if (!$job['preview_hash']) {

                    $mobile = $userData->mobile ?? '';

                    $hash = hash_hmac(
                        'sha256',
                        $jobNo . 'NEW_BOOKING' . $mobile,
                        config('app.key')
                    );

                    $job['preview_hash'] = $hash;
                    $missingHashes[$jobNo] = $hash;
                }
            } else {
                $job += [
                    'name' => '',
                    'mobile' => '',
                    'type' => '',
                    'kd_id' => null
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Batch DB write (ONE QUERY ONLY)
        |--------------------------------------------------------------------------
        */
        if (!empty($missingHashes)) {

            $cases = [];
            $ids = [];

            foreach ($missingHashes as $jobNo => $hash) {
                $jobNo = addslashes($jobNo);
                $hash = addslashes($hash);

                $cases[] = "WHEN '{$jobNo}' THEN '{$hash}'";
                $ids[] = "'{$jobNo}'";
            }

            $idsList = implode(',', $ids);
            $caseSql = implode(' ', $cases);

            $sql = "
                UPDATE cus_job_temp
                SET preview_hash = CASE job_no
                    {$caseSql}
                END
                WHERE job_no IN ({$idsList})
            ";

            DB::statement($sql);
        }


        return $jobs;
    }

    protected function emptyResponse()
    {
        return response()->json([
            'type' => 0,
            'result' => [],
        ]);
    }

    public function admin_accept_job(Request $request)
    {
        $jobNo = $request->job_no ?? 'UNKNOWN';

        try {
            \Log::info("AdminAcceptJob [JobNo: {$jobNo}]: Step 1 - Method triggered. Validating request...");

            /* ================= VALIDATION ================= */
            $validated = $request->validate([
                'job_id'  => ['required'],
                'job_no'  => ['required'],
                'u_id'    => ['required'],
                'user_id' => ['required']
            ]);

            $bidderId = (int) $request->user_id; // Driver ID
            $user_d   = (int) $request->u_id;    // Customer ID
            $jobNo    = $request->job_no;

            /* ================= FETCH CUS_JOB_TEMP EARLY ================= */
            $get_log = \Illuminate\Support\Facades\DB::table('cus_job_temp')->where('job_no', $jobNo)->first();

            if (!$get_log) {
                return response()->json(['status' => false, 'message' => 'Job not found in database.'], 200);
            }

            /* 🔒 INTEGRITY CHECKS */
            if ($get_log->user_id != $user_d) {
                return response()->json(['status' => false, 'message' => 'Unauthorized action on this job.'], 403);
            }

            if (in_array($get_log->job_status, ['accept', 'completed', 'cancelled'])) {
                return response()->json(['status' => false, 'message' => 'This job has already been processed or cancelled.'], 200);
            }

            $alreadyAssigned = \Illuminate\Support\Facades\DB::table('open_jobs')->where('job_no', $jobNo)->exists();
            if ($alreadyAssigned) {
                return response()->json(['status' => false, 'message' => 'Job has already been assigned to a driver.'], 200);
            }

            /* ================= FETCH USER ================= */
            $us_d = json_decode($get_log->user_details ?? '', true) ?? [];
            $tableName = (strpos($jobNo, 'GRC') === 0) ? 'customer_register' : 'user_register';
            $user = \Illuminate\Support\Facades\DB::table($tableName)->where(['id' => $user_d, 'deletes' => '0'])->first();

            if (!$user && $user_d != 0) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 200);
            }

            /* ================= FIREBASE ================= */
            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $firebaseDoc = $firebase->getJob($jobNo);

            if (!$firebaseDoc) {
                return response()->json(['status' => false, 'message' => 'Job not found in Firebase.'], 200);
            }

            $job = $this->parseFirestoreFields($firebaseDoc);

            if (empty($job['bids_details']) || !isset($job['bids_details'][$bidderId])) {
                return response()->json(['status' => false, 'message' => 'Bid not found for this user.'], 200);
            }

            $bid = $job['bids_details'][$bidderId];
            $job['bids_details'][$bidderId]['status'] = 'accept';

            /* ================= RESTORED: FLAWLESS FARE MATHEMATICS ================= */
            // We MUST calculate these so the UI shows the breakdown properly instead of 0
            $fr_details = json_decode($get_log->fare_breakdown ?? '', true) ?? [];

            $toll_fare  = (int) ($fr_details['toll_fare'] ?? $job['toll_fare'] ?? 0);
            $bid_amount = (int) ($bid['amount'] ?? 0);

            if (($job['global_type'] ?? '') == 'schedule') {
                $base_fare = $bid_amount;
            } else {
                $base_fare = $bid_amount - $toll_fare;
            }

            if ($base_fare < 0) $base_fare = 0;

            $com = (int) round(($base_fare + $toll_fare) * 0.05);
            $tax = (int) round(($base_fare + $com) * 0.05);
            $total_fare = $base_fare + $tax + $com + $toll_fare;

            /* ================= TARIFF & CAB SEATING ================= */
            $passCount = $us_d['pass_count'] ?? $job['pass_count'] ?? null;
            $luggCount = $us_d['lugg_count'] ?? $job['lugg_count'] ?? 0;
            $cabType   = $us_d['cab_type'] ?? $job['cab_type'] ?? '';

            if (!empty($us_d)) {
                if ($cabType == 'Go Sedan') {
                    $column = 'seven_seater';
                    if (($job['job_type'] ?? '') == 'roundtrip') $column .= '_round';
                } else {
                    $column = 'four_seater';
                    if (($job['job_type'] ?? '') == 'roundtrip') $column .= '_round';
                }
            } else {
                if ($passCount <= 4) $column = 'four_seater';
                elseif ($passCount <= 6) $column = 'six_seater';
                elseif ($passCount <= 7) $column = 'seven_seater';
                elseif ($passCount <= 13) $column = 'onethree_seater';
                elseif ($passCount <= 18) $column = 'oneeight_seater';
                elseif ($passCount <= 21) $column = 'twoone_seater';
                elseif ($passCount <= 25) $column = 'twofive_seater';
                elseif ($passCount <= 50) $column = 'fivezero_seater';
                elseif ($passCount === 0) $column = 'mini_four_seater';
                else $column = 'seven_seater';
            }

            $get_fare = \Illuminate\Support\Facades\DB::table('tariff_fare_website')
                ->where('from_km', '<=', (float) ($job['distance'] ?? 0))
                ->where('to_km', '>=', (float) ($job['distance'] ?? 0))
                ->where($column, '!=', 0)
                ->where('status', '0')
                ->first();

            $ex_km = $get_fare ? $get_fare->fare_km : '14';
            $to_km = $get_fare ? $get_fare->to_km : ($job['distance'] ?? 0);

            $inclu = [
                "{$to_km} Upto Km included",
                "Toll, Bata, Parking Included",
                "Driver allowance Included",
                "Waiting time up to 30 minutes for pickup included",
                "Fuel charges included",
                "Toll charges included (based on actual value)",
                "Return trips close by 9:00 PM",
                "Taxes included"
            ];

            $exclu = [
                "₹{$ex_km}/km will apply beyond the included kms",
                "State permit / entry charges",
                "Hill station charges (extra)",
                "Any government taxes or local charges, if applicable"
            ];

            /* ================= RESTORED: JSON DATA BREAKDOWN ================= */
            $data = [
                'id'              => $job['id'] ?? null,
                'bidder_id'       => $bidderId,
                'job_no'          => $jobNo,
                'job_type'        => $job['job_type'] ?? '',
                'pass_count'      => $passCount,
                'lugg_count'      => $luggCount,
                'cab_type'        => $cabType,
                'from_place'      => $job['from_place'] ?? '',
                'to_place'        => $job['to_place'] ?? '',
                'pickup_date'     => $job['pickup_date'] ?? '',
                'dropoff_date'    => $job['dropoff_date'] ?? null,
                'distance'        => $job['distance'] ?? 0,
                'duration'        => $job['duration'] ?? '',
                'bata'            => $job['add_fare_details']['bata'] ?? $fr_details['bata'] ?? 'Included',
                'toll'            => $job['add_fare_details']['toll'] ?? $fr_details['toll'] ?? 'Included',
                'parking'         => $job['add_fare_details']['parking'] ?? $fr_details['parking'] ?? 'Included',

                // Explicit values ensure the UI shows the breakdown properly
                'base_fare'       => $base_fare,
                'toll_fare'       => $toll_fare,
                'tax'             => $tax,
                'com'             => $com,

                // 🔥 THE FIX: Cash To Driver magic variables
                'pay_to_driver'   => $total_fare, // Forces UI to read full amount
                'total_fare'      => $total_fare,

                'part_pay_fare'   => $com,
                'credit_pay_fare' => 0,
                'isDiscount'      => 'no',
                'user_credit'     => 0,
                'wallet'          => 0,
                'inclusion'       => $inclu,
                'exclusion'       => $exclu
            ];

            // Ensure deductAmt is never 0, otherwise bookingPreview ignores 'pay_to_driver'
            $safe_deductAmt = $com > 0 ? $com : 1;

            $depositAmt = 0; // Cash deposit is 0
            $tran_id = 'GRB' . time() . rand(1000, 9999);

            /* ================= DATABASE TRANSACTION ================= */
            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                // 1. Update Temporary Jobs
                \Illuminate\Support\Facades\DB::table('cus_job_temp')
                    ->where('job_no', $jobNo)
                    ->update([
                        'job_status'     => 'accept',
                        'assigned_to'    => $bidderId,
                        'payment_status' => 'pending',
                        'fare'           => $total_fare,
                        'pay_amt'        => $total_fare,
                        'com'            => $com,
                        'tax'            => $tax,
                        'base_fare'      => $base_fare,
                        'toll_fare'      => $toll_fare,
                        'discount'       => 0,
                        'isDiscount'     => 'no',
                        'deductAmt'      => $safe_deductAmt, // 🔥 Triggers Cash To Driver logic
                        'credit'         => 0,
                        'wallet_amt'     => 0,
                        'fare_breakdown' => json_encode($data), // The fully populated JSON breakdown
                        "bids_details"   => json_encode($job['bids_details']),
                        'updated_at'     => now()
                    ]);

                $buildCheckOut = [
                    'userID'           => $user_d,
                    'depositAmt'       => $depositAmt,
                    'existWalletAmt'   => 0,
                    'existCash_points' => 0,
                    'finalTotal'       => $total_fare,
                    'discount'         => 0,
                    'shipamount'       => 0,
                    'wallet_amt'       => 0,
                    'credit_amt'       => 0,
                    'grandtotal'       => $depositAmt,
                    'shipping'         => 'pickUpToStore',
                ];

                // 2. Insert Payment History
                $paymentId = \Illuminate\Support\Facades\DB::table('payment_history')->insertGetId([
                    'createdon'         => now(),
                    'crontime'          => now(),
                    'ip'                => $request->ip() ?? '',
                    'user_id'           => $user_d,
                    'status'            => '0',
                    'transaction_id'    => $tran_id,
                    'job_no'            => $jobNo,
                    'checkout_response' => json_encode($buildCheckOut),
                    'category'          => 'Purchase',
                    'gateway'           => 'cash',
                    'finaltotal'        => $depositAmt,
                    'receipt_no'        => '',
                    'reference'         => '',
                    'paymentStatus'     => 'pending',
                    'shipamount'        => 0,
                    'wallet_amt'        => 0,
                    'credit_amt'        => 0,
                    'grandtotal'        => $depositAmt,
                ]);

                // 3. Insert Payment Log
                \Illuminate\Support\Facades\DB::table('payment_history_log')->insert([
                    'payment_history_id' => $paymentId,
                    'transaction_id'     => $tran_id,
                    'gateway'            => 'cash',
                    'user_id'            => $user_d,
                    'paymentStatus'      => 'pending',
                    'pay_response'       => json_encode($buildCheckOut),
                    'response'           => null,
                    'createdon'          => now(),
                ]);

                // 4. Migrate to Open Jobs Dynamically
                $columns = \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing('cus_job_temp');
                $columns = array_filter($columns, function ($column) {
                    return $column !== 'id';
                });
                $columnList = implode(',', $columns);

                \Illuminate\Support\Facades\DB::statement("
                INSERT INTO open_jobs ($columnList)
                SELECT $columnList
                FROM cus_job_temp
                WHERE job_no = ? AND user_id = ?
            ", [$jobNo, $user_d]);

                \Illuminate\Support\Facades\DB::commit();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ], 500);
            }

            /* ================= FIREBASE SYNC & NOTIFICATIONS ================= */
            try {
                $firebase->updateBidStatus($jobNo, $bidderId, 'accept');
                $firebase->updateJobStatus($jobNo, 'accept');

                if ($user) {
                    \App\Jobs\GenerateInvoiceJob::dispatch(
                        $data + ['user_id' => $user->id],
                        ['name' => $user->name, 'email' => $user->email],
                        ['wallet' => 0, 'credit' => 0, 'balance' => 0, 'upi' => $total_fare]
                    );
                }

                $fcmToken = $this->getFcm([$bidderId]);
                if ($fcmToken) {
                    $accessToken = $this->getAccessToken();
                    if ($accessToken) {
                        $callerName = $user ? $user->name : 'Customer';
                        foreach ($fcmToken as $token) {
                            try {
                                $this->sendFCM(
                                    $accessToken,
                                    $token,
                                    'Your Bid Has Been Accepted!',
                                    'Job ID ' . $jobNo . ': Your bid has been accepted by ' . $callerName . '. Get ready for the ride.',
                                    [
                                        'caller' => $callerName,
                                        'type'   => 'accept_notification',
                                        'url'    => env('APP_URL') . 'jobs',
                                    ]
                                );
                            } catch (\Throwable $e) {
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
            }

            // --- WHATSAPP TEMPLATE INTEGRATION FROM CASHORDER ---
            try {
                $dr_details = \Illuminate\Support\Facades\DB::table('user_register')->where(['id' => $bidderId, 'deletes' => '0'])->first();

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

                $sendTemplateMessage = function ($mobile, $templateName, $parameters) use ($request) {
                    $cleanPhone = preg_replace('/[^0-9]/', '', $mobile);
                    if (strlen($cleanPhone) === 10) {
                        $cleanPhone = '91' . $cleanPhone;
                    }

                    $template = \Illuminate\Support\Facades\DB::table('wamail_templates')->where('name', $templateName)->first();
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

                    \Illuminate\Support\Facades\DB::table('smslog')->insert([
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
                        [$get_log->otp ?? '1234']
                    );
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("AdminAcceptJob WhatsApp Error: " . $e->getMessage());
            }
            // --- END WHATSAPP TEMPLATE INTEGRATION ---

            $firebase->deleteJob($jobNo);

            return response()->json([
                'status'  => true,
                'data'    => [
                    'job_no'      => $jobNo,
                    'v_details'   => null,
                    'paid_amount' => $depositAmt,
                    'ride_info'   => $data
                ],
                'message' => 'Ride assigned successfully.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An unexpected error occurred.'
            ], 500);
        }
    }

    public function adminJobList(Request $request)
    {
        try {
            $filters = $request->only([
                'startDate',
                'endDate',
                'dateFilter',
                'jobType',
                'jobStatus',
                'job_no',
                'jobFrom',
                'filterType'
            ]);

            // Fetch data from local MySQL
            $rawJobs = \Illuminate\Support\Facades\DB::table('cus_job_temp')
                ->where('deletes', '0')
                ->get();

            $jobs = [];
            foreach ($rawJobs as $rawJob) {
                $jobArray = (array) $rawJob;

                // Skip jobs where job_no starts with GRP-
                if (isset($jobArray['job_no']) && strpos($jobArray['job_no'], 'GRP-') === 0) {
                    continue;
                }

                // Decode bids_details
                if (!empty($jobArray['bids_details']) && is_string($jobArray['bids_details'])) {
                    $jobArray['bids_details'] = json_decode($jobArray['bids_details'], true);
                } elseif (empty($jobArray['bids_details'])) {
                    $jobArray['bids_details'] = [];
                }

                // Ensure global_type consistency
                $jobNo = $jobArray['job_no'] ?? '';
                if (strpos($jobNo, 'GRC') === 0) {
                    $jobArray['global_type'] = 'customer';
                } elseif (strpos($jobNo, 'GRD') === 0) {
                    $jobArray['global_type'] = 'driver';
                } elseif (empty($jobArray['global_type'])) {
                    $jobArray['global_type'] = 'website';
                }

                $jobs[] = $jobArray;
            }

            if (!$jobs) {
                return $this->emptyResponse();
            }

            // ====================================================================
            // EXISTING FILTER LOGIC
            // ====================================================================
            $currentTime = time();
            $baseFilters = $filters;
            $reqStatus = $filters['jobStatus'] ?? '';

            if (in_array($reqStatus, ['expired', 'not_complete', 'accepted'])) {
                unset($baseFilters['jobStatus']);
            }

            $jobs = array_values(array_filter($jobs, function ($job) use ($filters, $baseFilters, $currentTime, $reqStatus) {

                // 🛑 MAJOR FIX: Removed the $userId <= 0 block entirely!
                // Website jobs have user_id = 0. By removing the block, they pass through to the frontend.

                if (method_exists($this, 'applyFilters') && !$this->applyFilters($job, $baseFilters)) {
                    return false;
                }

                $dbStatus = strtolower($job['job_status'] ?? '');
                $pickupDate = $job['pickup_date'] ?? '';
                $isExpired = (!empty($pickupDate) && strtotime($pickupDate) < $currentTime);

                if ($reqStatus === 'expired') {
                    return ($dbStatus !== 'accept' && $dbStatus !== 'completed' && $dbStatus !== 'cancelled' && $isExpired);
                }
                if ($reqStatus === 'not_complete') {
                    return ($dbStatus !== 'accept' && $dbStatus !== 'completed' && $dbStatus !== 'cancelled' && $dbStatus !== 'started' && !$isExpired);
                }
                if ($reqStatus === 'accepted') {
                    return ($dbStatus === 'accept' || $dbStatus === 'accepted');
                }
                if (!empty($reqStatus) && !in_array($reqStatus, ['expired', 'not_complete', 'accepted'])) {
                    if ($dbStatus !== strtolower($reqStatus)) {
                        return false;
                    }
                }
                return true;
            }));

            if (!$jobs) {
                return $this->emptyResponse();
            }

            if (method_exists($this, 'attachUsers')) {
                $jobs = $this->attachUsers($jobs);
            }

            // Fetch IDs for relation lookups
            $bidDriverIds = [];
            $userOwnerIds = [];
            $customerOwnerIds = [];

            foreach ($jobs as $job) {
                if (isset($job['bids_details']) && is_array($job['bids_details'])) {
                    $bidDriverIds = array_merge($bidDriverIds, array_keys($job['bids_details']));
                }

                $userId = (int)($job['user_id'] ?? 0);
                if ($userId > 0) {
                    $gType = strtolower($job['global_type'] ?? '');
                    if ($gType === 'driver' || $gType === 'agent') {
                        $userOwnerIds[] = $userId;
                    } else {
                        $customerOwnerIds[] = $userId;
                    }
                }
            }

            // Bulk Fetch Drivers & Customers
            $allUserRegisterIds = array_unique(array_merge($bidDriverIds, $userOwnerIds));
            $userRegisterDetails = !empty($allUserRegisterIds) ? \Illuminate\Support\Facades\DB::table('user_register')->whereIn('id', $allUserRegisterIds)->select('id', 'name', 'mobile', 'roll_id')->get()->keyBy('id')->toArray() : [];
            $customerIds = array_unique($customerOwnerIds);
            $customerDetails = !empty($customerIds) ? \Illuminate\Support\Facades\DB::table('customer_register')->whereIn('id', $customerIds)->select('id', 'name', 'mobile')->get()->keyBy('id')->toArray() : [];

            // ====================================================================
            // DATA FORMATTING & UI KEY BINDING
            // ====================================================================
            foreach ($jobs as &$job) {

                // --- START: SYNCHRONIZED FARE CALCULATION LOGIC ---
                $fareBreakdown = [];
                if (!empty($job['fare_breakdown']) && is_string($job['fare_breakdown'])) {
                    $fareData = json_decode($job['fare_breakdown'], true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($fareData)) {
                        $fareBreakdown = $fareData;
                        $job = array_merge($job, $fareData);
                    }
                }

                $baseFare   = array_key_exists('base_fare', $fareBreakdown) ? $fareBreakdown['base_fare'] : (int) ($job['base_fare'] ?? 0);
                $tollFare   = array_key_exists('toll_fare', $fareBreakdown) ? $fareBreakdown['toll_fare'] : (int) ($job['toll_fare'] ?? 0);
                $tax        = array_key_exists('tax', $fareBreakdown) ? $fareBreakdown['tax'] : (int) ($job['tax'] ?? 0);
                $commission = array_key_exists('com', $fareBreakdown) ? $fareBreakdown['com'] : (int) ($job['com'] ?? 0);
                $discount   = array_key_exists('discount', $fareBreakdown) ? $fareBreakdown['discount'] : (int) ($job['discount'] ?? 0);
                $isDiscount = array_key_exists('isDiscount', $fareBreakdown) ? $fareBreakdown['isDiscount'] : ($job['isDiscount'] ?? '');

                $jobStatus = strtolower($job['job_status'] ?? '');

                // Total fare conditional logic
                if ($jobStatus == 'created' || $jobStatus == 'bidding' || $jobStatus == 'schedule') {
                    $totalFare = array_key_exists('total_fare', $fareBreakdown) ? $fareBreakdown['total_fare'] : ($job['fare'] ?? 0);
                } else {
                    $totalFare  = $job['fare'] ?? 0;
                }

                $b_amt = 0;
                $paid_on = 0;
                $paid_wallet = 0;
                $pay_amt = $job['pay_amt'] ?? 0;
                $deductAmt = $job['deductAmt'] ?? null;
                $gateway = $job['gateway'] ?? '';

                if (array_key_exists('total_fare', $fareBreakdown) && $fareBreakdown['total_fare'] == $pay_amt && $deductAmt == null) {
                    $b_amt = 0;
                } else if ($deductAmt != 0 && array_key_exists('pay_to_driver', $fareBreakdown)) {
                    $b_amt = $fareBreakdown['pay_to_driver'];
                } else {
                    $b_amt = $baseFare + $tollFare;
                }

                $paid_on = $job['fare'] ?? 0;
                $credit_bonus = $isDiscount == 'yes' ? $discount : 0;

                if ($gateway && $gateway == 'wallet') {
                    $paid_on = 0;
                    $paid_wallet = $pay_amt;
                } else if ($gateway && $gateway != 'wallet') {
                    $paid_on = $pay_amt;
                    $paid_wallet = $job['wallet_amt'] ?? 0;
                }

                if (($job['payment_status'] ?? '') == 'pending' && $deductAmt == null) {
                    $paid_on = 0;
                    $paid_wallet = 0;
                    $b_amt = 0;
                }

                if ($jobStatus == 'created' || $jobStatus == 'bidding') {
                    $b_amt = 0;
                    $paid_on = 0;
                    $paid_wallet = 0;
                }

                // Bind values exactly how bookingPreview exports them
                $job['actual_base']  = $baseFare + $commission;
                $job['base_fare']    = ($isDiscount == 'yes' && $discount > 0) ? ($baseFare + $commission) - $discount : ($baseFare + $commission);
                $job['govt_levy']    = $tollFare;
                $job['tax']          = $tax;
                $job['com']          = $commission;
                $job['discount']     = $discount;
                $job['total_fare']   = $totalFare;
                $job['fare']         = $totalFare;
                $job['isDiscount']   = $isDiscount;
                $job['paid_amt']     = $paid_on;
                $job['wallet_amt']   = $paid_wallet;
                $job['credit_bonus'] = $credit_bonus;
                $job['balance_amt']  = $b_amt;
                // --- END: SYNCHRONIZED FARE CALCULATION LOGIC ---

                $job['distance'] = $job['distance'] ?? ($job['c_distance'] ?? '');
                $job['duration'] = $job['duration'] ?? ($job['c_duration'] ?? '');

                $details = [];
                if (!empty($job['user_details'])) {
                    $details = is_string($job['user_details']) ? json_decode($job['user_details'], true) : $job['user_details'];
                }
                if (!is_array($details)) $details = [];

                $userId = (int)($job['user_id'] ?? 0);
                $gType  = strtolower($job['global_type'] ?? '');
                $job['bid_count'] = isset($job['bids_details']) && is_array($job['bids_details']) ? count($job['bids_details']) : 0;

                $job['name'] = "";
                $job['mobile'] = "";
                $job['type'] = "";
                $job['kd_id'] = null;

                if ($userId > 0) {
                    if ($gType === 'driver' || $gType === 'agent') {
                        if (isset($userRegisterDetails[$userId])) {
                            $job['name']   = $userRegisterDetails[$userId]->name ?? '';
                            $job['mobile'] = $userRegisterDetails[$userId]->mobile ?? '';
                            $job['kd_id']  = $userRegisterDetails[$userId]->id ?? null;
                            $roll_id = $userRegisterDetails[$userId]->roll_id ?? 0;
                            $job['type']   = ($gType === 'agent' || $roll_id == 3) ? "Agent" : "Driver";
                        }
                    } else {
                        if (isset($customerDetails[$userId])) {
                            $job['name']   = $customerDetails[$userId]->name ?? '';
                            $job['mobile'] = $customerDetails[$userId]->mobile ?? '';
                            $job['type']   = "Customer";
                        }
                    }
                }

                // --- CORRECT WEBSITE JOB HANDLING ---
                if (empty($job['name']) && !empty($details)) {
                    $job['name']   = $details['name'] ?? 'Unknown User';
                    $job['mobile'] = $details['mobile'] ?? '';
                    if (empty($job['type'])) $job['type'] = "Website";
                }

                $job['u_name']      = !empty($job['name']) ? $job['name'] : 'Unknown User';
                $job['poster_name'] = $job['u_name'];
                $job['u_mobile']    = $job['mobile'];

                $job['lugg_count'] = $job['lugg_count'] ?? $job['luggage'] ?? ($details['lugg_count'] ?? 0);
                $job['pass_count'] = $job['pass_count'] ?? ($details['pass_count'] ?? 1);
                $job['cab_type']   = $job['cab_type'] ?? $job['car_type'] ?? ($details['cab_type'] ?? '');

                // Push Notification Counts Logic
                $job['count'] = 0;
                $currentStatus = strtolower($job['job_status'] ?? '');
                $searchJobId = (int)($job['id'] ?? 0);

                if ($currentStatus === 'accept' || $currentStatus === 'accepted') {
                    $openJob = \Illuminate\Support\Facades\DB::table('open_jobs')->where('id', $searchJobId)->first(['job_no']);
                    if ($openJob && !empty($openJob->job_no)) {
                        $cusJobTemp = \Illuminate\Support\Facades\DB::table('cus_job_temp')->where('job_no', $openJob->job_no)->first(['id']);
                        if ($cusJobTemp) $searchJobId = $cusJobTemp->id;
                    }
                }

                if ($searchJobId > 0) {
                    $pushRecord = \Illuminate\Support\Facades\DB::table('push_notifications')
                        ->where('req_json', 'LIKE', '%"job_id": ' . $searchJobId . '%')
                        ->orWhere('req_json', 'LIKE', '%"job_id":' . $searchJobId . '%')
                        ->orderBy('id', 'desc')->first(['res_json']);

                    if ($pushRecord && !empty($pushRecord->res_json)) {
                        $resData = json_decode($pushRecord->res_json, true);
                        if (isset($resData['success_count'])) $job['count'] = (int)$resData['success_count'];
                    }
                }

                // Format Bidder Names
                if (isset($job['bids_details']) && is_array($job['bids_details'])) {
                    foreach ($job['bids_details'] as $driverId => &$bidInfo) {
                        if (is_array($bidInfo)) {
                            $bidInfo['b_name'] = isset($userRegisterDetails[$driverId]) ? $userRegisterDetails[$driverId]->name : 'Unknown Driver';
                        }
                    }
                }
            }
            unset($job, $bidInfo);

            return response()->json([
                'type' => 1,
                'result' => array_values($jobs),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'type' => 0,
                'result' => $e->getMessage(),
            ]);
        }
    }

    public function adminScheduledJobList(Request $request)
    {
        try {
            $query = \Illuminate\Support\Facades\DB::table('cus_job_temp as cjt')
                ->leftJoin('customer_register as cr', 'cjt.user_id', '=', 'cr.id')
                ->where('cjt.global_type', 'schedule')
                ->where('cjt.job_status', 'created')
                ->select(
                    'cjt.*',
                    'cr.name as u_name',
                    'cr.mobile as u_mobile',
                    'cr.dialCode as dialCode'
                );

            // 🔥 FIX: Get the exact current date AND time
            $currentDateTime = date('Y-m-d H:i:s');

            // 🔥 FIX: Always exclude jobs where the pickup_date/time has already passed
            $query->where('cjt.pickup_date', '>=', $currentDateTime);

            $today = date('Y-m-d');
            $start = $request->input('startDate');
            $end = $request->input('endDate');

            // If start/end dates are provided, apply them on top of the current time restriction
            if (!empty($start) || !empty($end)) {
                $start = $start ?: $today;
                $end = $end ?: $today;
                $query->whereDate('cjt.pickup_date', '>=', $start)
                    ->whereDate('cjt.pickup_date', '<=', $end);
            }

            $jobs = $query->get()->map(function ($item) {
                $job = (array) $item;

                // 1. Decode bids_details JSON
                if (!empty($job['bids_details']) && is_string($job['bids_details'])) {
                    $job['bids_details'] = json_decode($job['bids_details'], true);
                }

                // 2. Safely read user_details if it exists
                $details = [];
                if (!empty($job['user_details']) && is_string($job['user_details'])) {
                    $details = json_decode($job['user_details'], true) ?: [];
                }

                $job['lugg_count'] = $job['lugg_count'] ?? $details['lugg_count'] ?? 0;
                $job['pass_count'] = $job['pass_count'] ?? $details['pass_count'] ?? 1;
                $job['cab_type'] = $job['cab_type'] ?? $details['cab_type'] ?? '';
                $finalName = !empty($job['u_name']) ? $job['u_name'] : ($details['name'] ?? 'Customer');

                if (empty($job['user_details'])) {
                    $job['user_details'] = json_encode([
                        'name' => $finalName,
                        'mobile' => $job['u_mobile'] ?? '',
                        'pass_count' => $job['pass_count'],
                        'cab_type' => $job['cab_type']
                    ]);
                }

                // 3. Extract the real fare from 'fare_breakdown' JSON
                if (!empty($job['fare_breakdown']) && is_string($job['fare_breakdown'])) {
                    $fb = json_decode($job['fare_breakdown'], true);
                    if (is_array($fb) && !empty($fb['total_fare'])) {
                        $job['fare'] = $fb['total_fare'];
                    }
                }

                // 4. Extract amount from 'sch_status' JSON (Overrides if > 0)
                $job['sch_amount'] = 0;
                $job['sch_status_data'] = [];
                $driverIds = [];

                if (!empty($job['sch_status']) && is_string($job['sch_status'])) {
                    $schData = json_decode($job['sch_status'], true);
                    if (is_array($schData)) {
                        foreach ($schData as $date => $bidders) {
                            if (is_array($bidders)) {
                                foreach ($bidders as $bidderId => $bData) {
                                    if (!empty($bData['amount']) && $bData['amount'] > 0) {
                                        $job['sch_amount'] += (float) $bData['amount'];
                                    }
                                    $job['sch_status_data'][] = [
                                        'date' => $date,
                                        'driver_id' => $bidderId,
                                        'status' => $bData['status'] ?? 'pending'
                                    ];
                                    $driverIds[] = $bidderId;
                                }
                            }
                        }
                    }
                }

                if (!empty($driverIds)) {
                    // Fetch both name and mobile from user_register
                    $driversData = \Illuminate\Support\Facades\DB::table('user_register')
                        ->whereIn('id', $driverIds)
                        ->select('id', 'name', 'mobile')
                        ->get()
                        ->keyBy('id');

                    foreach ($job['sch_status_data'] as &$req) {
                        $driver = $driversData->get($req['driver_id']);
                        $req['driver_name'] = $driver ? $driver->name : 'Unknown Driver';
                        $req['driver_mobile'] = $driver ? $driver->mobile : '';
                    }
                }

                // Set the final fare
                if ($job['sch_amount'] > 0) {
                    $job['fare'] = $job['sch_amount'];
                }

                // 🔥 FIX: Force all breakdown fields to null so the UI completely hides them
                $job['base_fare'] = null;
                $job['toll_fare'] = null;
                $job['tax'] = null;
                $job['driver_bata'] = null;

                // Ensure all name keys exist
                $job['poster_name'] = $finalName;
                $job['name'] = $finalName;
                $job['u_name'] = $finalName;

                return $job;
            })->toArray();

            return response()->json([
                'type' => 1,
                'result' => array_values($jobs),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'type' => 0,
                'result' => $e->getMessage(),
            ]);
        }
    }

    public function adminJobEdit(Request $request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [

            'job_id' => 'required|integer',
            'job_no' => 'required|string',

            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'mobile' => 'required|string|max:15',

            'job_type' => 'required|in:oneway,roundtrip',
            'car_type' => 'required|string|max:50',

            'distance' => 'required|numeric|min:0',
            'passenger_count' => 'required|integer|min:1',
            'luggage_count' => 'required|integer|min:0',

            'pickup_date' => 'required|date',
            'drop_day' => 'nullable|string|max:50',

            'base_fare' => 'required|numeric|min:0',
            'toll_fare' => 'nullable|numeric|min:0',
            'total_fare' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'data' => []
            ], 200);
        }


        try {

            DB::beginTransaction();

            /* =========================
               FIREBASE UPDATE
            ========================== */

            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $firebase->editJob($request->job_no, [
                'job_type' => $request->job_type,
                'distance' => $request->distance,
                'pick_address' => $request->pickup_address,
                'drop_address' => $request->drop_address,
                'pickup_date' => $request->pickup_date,
                'pass_count' => $request->passenger_count,
                'base_fare' => $request->base_fare,
                'toll_fare' => $request->toll_fare,
                'fare' => $request->total_fare,
            ]);

            $firebase->updateUserDetailsKeys($request->job_no, [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'cab_type' => $request->car_type,
                'pass_count' => $request->passenger_count,
                'lugg_count' => $request->luggage_count,
            ]);

            $jsonSet = sprintf(
                "JSON_SET(
                    user_details,
                    '$.name', '%s',
                    '$.email', '%s',
                    '$.mobile', '%s',
                    '$.cab_type', '%s',
                    '$.pass_count', %d,
                    '$.lugg_count', %d
                )",
                addslashes($request->name),
                addslashes($request->email),
                addslashes($request->mobile),
                addslashes($request->car_type),
                (int) $request->passenger_count,
                (int) $request->luggage_count
            );

            DB::table('cus_job_temp')
                ->where('job_no', $request->job_no)
                ->update([
                    'job_type' => $request->job_type,
                    // 'car_type'        => $request->car_type,
                    'distance' => $request->distance,
                    'pickup_date' => $request->pickup_date,
                    'pass_count' => $request->passenger_count,
                    // 'luggage_count'   => $request->luggage_count,
                    'day' => $request->duration,
                    'base_fare' => $request->base_fare,
                    'toll_fare' => $request->toll_fare,
                    'fare' => $request->total_fare,
                    'user_details' => DB::raw($jsonSet),
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Job updated successfully.',
                'data' => []
            ], 200);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Job update failed',
                'data' => $e->getMessage()
            ], 500);
        }
    }

    public function adminGetPassengerDetails(Request $request)
    {
        $passenger_id = $request->passenger_id;
        $jobtype = $request->jobtype;

        if ($jobtype == 'customer' || $jobtype == 'schedule') {

            $passenger = DB::table('customer_register')
                ->where('id', $passenger_id)
                ->first();
        } else {
            $passenger = DB::table('user_register')
                ->where('id', $passenger_id)
                ->first();
        }

        if (!$passenger) {
            return response()->json([
                'status' => false,
                'message' => 'Passenger not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $passenger
        ]);
    }

    public function adminGetDriverDetails(Request $request)
    {
        $driver_id = (int) $request->driver_id;

        if (!$driver_id) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid driver id'
            ], 422);
        }

        // 1. Fetch ALL driver info from user_register, kyc_details, and ocr_request in ONE query
        $driver = DB::table('user_register as ur')
            ->leftJoin('kyc_details as kd', function ($join) {
                $join->on('kd.user_id', '=', 'ur.id')
                    ->whereRaw('kd.id = (
                     SELECT MAX(id)
                     FROM kyc_details
                     WHERE user_id = ur.id AND deletes = 0
                 )');
            })
            ->leftJoin('ocr_request as ocr', function ($join) {
                // FIX: Correctly structure the join to avoid SQL syntax errors
                $join->on('ocr.user_id', '=', 'ur.id')
                    ->whereRaw('ocr.id = (
                     SELECT MAX(id) 
                     FROM ocr_request 
                     WHERE user_id = ur.id 
                     AND doc_type = "DRIVING_LICENSE"
                 )');
            })
            ->leftJoin('districts as dist', 'ur.districts_id', '=', 'dist.id')
            ->where('ur.id', $driver_id)
            ->select(
                'ur.id as user_id',
                'ur.profile_img_url',
                'ur.address',
                'ur.name',
                'ur.mobile',
                'ur.profile_percentage',
                'ur.age',
                'ur.email',
                'ur.state',
                'ur.city',
                'ur.dob',
                'ur.districts_id',
                'dist.district_name',
                'kd.exp',
                'kd.dl_no',
                'kd.dl_expiry',
                'kd.selfie_url',
                // Get Aadhaar details from the same KYC join
                'kd.front_image as aadhar_image_front',
                'kd.back_image as aadhar_image_back',
                'kd.proof_type',
                'kd.proof_status',
                'ur.vehicle_details',
                'ur.per_km',
                'ur.per_hour',
                'ur.per_day',
                'ur.extra_per_km',
                'ur.upiID',
                'ur.reviews',
                'ur.remarks',
                'ocr.front as license_front_image',
                'ocr.back as license_back_image',
                // Safely check both NT and TR for the license type, just like the PHP logic does
                DB::raw("COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(ocr.req_response, '$.NT')), ''), JSON_UNQUOTE(JSON_EXTRACT(ocr.req_response, '$.TR'))) as license_type")
            )
            ->first();

        if (!$driver) {
            return response()->json([
                'status' => false,
                'message' => 'Driver not found'
            ], 404);
        }

        // 2. Decode vehicle_details
        $driver->vehicle_details = !empty($driver->vehicle_details)
            ? json_decode($driver->vehicle_details, true)
            : [];

        // 3. Ensure the proof_type is actually Aadhaar (fallback if the row is something else)
        $validAadhaarTypes = ['AADHAAR', 'AADHAR_DIGILOCKER'];
        if (!in_array($driver->proof_type, $validAadhaarTypes)) {
            $driver->aadhar_image_front = null;
            $driver->aadhar_image_back  = null;
            $driver->proof_type         = null;
            $driver->proof_status       = null;
        }

        // 4. Set doc_verify based on Aadhaar approval
        $driver->doc_verify = ($driver->proof_status === 'approved') ? 1 : 0;

        return response()->json([
            'status' => true,
            'data' => ['driver' => $driver]
        ]);
    }

    public function adminGetDriverList(Request $request)
    {
        $drivers = DB::table('user_register as ur')
            ->join('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
            ->leftJoin('drivers_current_location as drl', 'ur.id', '=', 'drl.user_id')
            ->where('ur.deletes', '0')
            ->where('ur.doc_verify', '1')
            ->where('ur.vehicle_verify', '2')
            ->where('kd.type', 'Driver')
            ->where('kd.o_status', 3)
            ->whereNotNull('ur.vehicle_details')
            ->select(
                'ur.*',
                'kd.*',
                'drl.lat as s_lat',
                'drl.lng as s_lang'
            )
            ->orderBy('ur.id', 'desc')
            ->get();

        if ($drivers->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No drivers found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $drivers
        ]);
    }

    public function searchMapDrivers(Request $request)
    {
        $search = $request->input('search', '');

        $query = DB::table('user_register as ur')
            ->join('kyc_details as kd', 'kd.user_id', '=', 'ur.id')
            ->where('ur.deletes', '0')
            ->where('ur.doc_verify', '1')
            ->where('ur.vehicle_verify', '2')
            ->where('kd.type', 'Driver');

        // Search by name or mobile if input exists
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ur.name', 'LIKE', "%{$search}%")
                    ->orWhere('ur.mobile', 'LIKE', "%{$search}%");
            });
        }

        // Limit to 20 for maximum UI speed
        $drivers = $query->select('ur.id', 'ur.name', 'ur.mobile')
            ->get();

        if ($drivers->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No drivers found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $drivers
        ]);
    }

    public function getDriverCurrentLocation(Request $request)
    {
        $driverId = $request->input('driver_id');

        $location = DB::table('drivers_current_location')
            ->where('user_id', $driverId)
            ->orderBy('updated_at', 'desc')
            ->select('lat', 'lng', 'updated_at')
            ->first();

        if ($location) {
            // Format the date nicely for the tooltip
            $location->updated_at = date('d M Y, h:i A', strtotime($location->updated_at));

            return response()->json([
                'status' => true,
                'data' => $location
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'No location data found.'
        ]);
    }

    public function rmReqeuestList(Request $request)
    {
        try {

            $request->validate([
                'id' => ['required', 'string'],
            ]);

            $firebase = new \App\Services\FirebaseJobService(
                $this->serviceAccount['project_id'],
                $this->getAccessToken()
            );

            $firebaseData = $firebase->getDevRmRequestById($request->id);

            if (!$firebaseData) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            // return $firebaseData;

            $district = $firebaseData['districts'] ?? null;

            $districtFormatted = $district === "All"
                ? "All"
                : (is_array($district) ? array_values($district) : []);

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $request->id,
                    'district' => $districtFormatted,
                    'user_id' => $firebaseData['user_id'] ?? null,
                    'created_at' => $firebaseData['created_at'] ?? null,
                ]
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {

            \Log::error('RM Request API Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
    public function getRoutePolyline(Request $request)
    {
        $startLng = $request->input('start_lng');
        $startLat = $request->input('start_lat');
        $endLng = $request->input('end_lng');
        $endLat = $request->input('end_lat');

        $osrmUrl = "https://ukgeo.goride.run/route/v1/driving/{$startLng},{$startLat};{$endLng},{$endLat}?overview=full";

        try {
            $response = Http::timeout(30)->get($osrmUrl);
            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The routing API returned an error.',
                    'status'  => $response->status(),
                    'body'    => $response->body()
                ], $response->status());
            }

            return response()->json([
                'success' => true,
                'data'    => $response->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ], 500);
        }
    }
}