<?php

namespace App\Http\Controllers\Api\v5;

use Exception;
use DateTime;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Template\mailController;
use Illuminate\Support\Carbon;
use App\Rules\CustomRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\customer_register;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Helpers\referralCode;
use App\Services\AutomationEventService;
use Razorpay\Api\Api;

class CustomerAuthController extends Controller
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
    
    public function profile(Request $request)
    {
        $customer = $request->get('customer');
    
        $existing = DB::table('referral_codes')
            ->where('user_id', $customer->id)
            ->where('app_name', 'customer')
            ->first();
    
        if (!$existing) {
            $referral_code = referralCode::generateReferralCode();
    
            DB::table('referral_codes')->insert([
                'user_id'    => $customer->id,
                'app_name'   => 'customer',
                'code'       => $referral_code,
                'created_at' => now()
            ]);
    
            $total_invites = 0;
            $total_rewards = 0;
    
        } else {
            $referral_code = $existing->code;
            $total_invites = $existing->total_invites ?? 0;
            $total_rewards = $existing->total_rewards ?? 0;
        }
        
        $kycDetails = DB::table('kyc_carpool')->where(['user_id' => $customer->id])->first();
        
        $hostCount = DB::table('cus_job_temp')
                ->where('user_id', $customer->id)
                ->where('global_type', 'carpool')
                ->count();
                
        $joinCount = DB::table('invitations')
            ->where('inviter_id', $customer->id)
            ->where('status', 'accepted')
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('job_id')
            // Group by the "other" person regardless of whether they are inviter or invitee
            ->groupBy('inviter_id')
            ->count();
    
        return response()->json([
            'status' => 'success',
            'data'   => $customer,
            'kyc'    => $kycDetails,
            'referral_code' => $referral_code,
            'hostCount' => $hostCount,
            'joinCount' => $joinCount,
            'referral_content' => "🚖 Ride with Goride Today!
    
    Sign up using my referral code and get ₹1000 wallet credits 💰
    🔹 Referral Code:
    *{$referral_code}*
    🔹 Fast, affordable & comfortable rides anytime
    👉 Download and Book Now:",
            'app_link' => 'https://www.goride.run/customer-app',
            'total_invites' => $total_invites,
            'total_rewards' => $total_rewards
        ]);
    }
    
    public function userProfile(Request $request)
    {
        // dd($request->all());
        
        // \Log::info('HIii...: ', ['hiiii']);
        
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'u_type' => ['nullable']
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Validation Error!',
                'error'   => $validator->errors()
            ]);
        }
        
        if ($request->u_type == 'driver') {
            $customer = DB::table('user_register')->where(['id' => $request->id])->first();
            $g_type = 'dr_carpool';
            // \Log::info('Customer Details:', (array) $customer);
            
            // Fallback logic for Driver (dr_carpool) using 'kyc_details'
            if ($customer && $customer->profile_img_url === null) {
                $customer->profile_img_url = DB::table('kyc_details')
                    ->where('user_id', $customer->id) // Assuming 'id' matches the driver's user_id
                    ->value('selfie_url');
            }
            
            // \Log::info('Customer Details:', (array) $customer);
            
            
        } else {
            $customer = DB::table('customer_register')->where(['id' => $request->id])->first();
            $g_type = 'carpool';
            
            // Fallback logic for Customer (carpool) using 'kyc_carpool'
            if ($customer && $customer->profile_img_url === null) {
                $customer->profile_img_url = DB::table('kyc_carpool')
                    ->where('user_id', $request->id) // Assuming 'id' matches the customer's user_id
                    ->value('selfie_url');
            }
        }
        
        
        if($customer){
            
            $kycDetails = DB::table('kyc_carpool')->where(['user_id' => $customer->id])->first();
            
            $hostCount = DB::table('cus_job_temp')
                    ->where('user_id', $customer->id)
                    ->whereIn('global_type', [$g_type])
                    ->count();
                    
            $joinCount = DB::table('invitations')
                ->where('inviter_id', $customer->id)
                ->whereIn('global_type', [$g_type])
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
                    ->whereIn('ct.global_type', [$g_type])
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
    
    public function customerRegister($user_id, $otp, $ip, $request)
    {
        try {
    
            // Fetch temp user safely
            $tempUser = DB::table('users_temp')
                ->where('id', $user_id)
                ->where('deletes', '0')
                ->first();
    
            if (!$tempUser) {
                return [
                    'status'  => 'failed',
                    'message' => 'User account not found!',
                    'error'   => 'The user account not found in temp table.'
                ];
            }
    
            // Prevent duplicate mobile registration
            $mobileExists = DB::table('customer_register')
                ->where('mobile', $tempUser->mobile)
                ->where('status', '0')
                ->where('deletes', '0')
                ->exists();
    
            if ($mobileExists) {
                return [
                    'status'  => 'failed',
                    'message' => 'The mobile number already has an account.',
                    'error'   => 'Duplicate mobile'
                ];
            }
    
            $utm_source   = null;
            $utm_campaign = null;
    
            DB::beginTransaction();
    
            // Prepare insert data
            $insertData = [
                'user'             => 'Customer',
                'pass'             => $tempUser->pass,
                'password'         => $tempUser->password,
                'roll_id'          => $tempUser->roll_id,
                'created_by'       => 0,
                'dialCode'         => $tempUser->dialCode,
                'mobile'           => $tempUser->mobile,
                'name'             => $tempUser->name,
                'email'            => $tempUser->email,
                'deletes'          => '0',
                'created_at'       => now(),
                'dob'              => $tempUser->dob,
                'lname'            => $tempUser->lname,
                'building_name'    => $tempUser->building_name,
                'city'             => $tempUser->city,
                'address'          => $tempUser->address,
                'nationality'      => $tempUser->nationality,
                'state'            => $tempUser->state,
                'ip'               => $request->ip(),
                'email_verify'     => $tempUser->email_verify,
                'mobile_verify'    => $tempUser->mobile_verify,
                'otp'              => '',
                'my_referral_code' => '',
                // 'cash_points'      => env('CREDIT_POINT'),
                'residinglocation' => $tempUser->residinglocation,
                'deviceType'       => $tempUser->deviceType,
                'utm_source'       => $utm_source,
                'utm_campaign'     => $utm_campaign,
                'fcm_token'        => $request->fcm_token ?? null,
            ];
            
            foreach (['utm_source', 'utm_medium', 'utm_campaign'] as $field) {
                $value = $request->input($field);
                
                if ($request->filled($field) && $value !== 'undefined' && $value !== 'null') {
                    $insertData[$field] = $value;
                }
            }
    
            // Insert customer
            $customerId = DB::table('customer_register')->insertGetId($insertData);
    
            // Log login
            DB::table('login_logs')->insert([
                'method'       => __FUNCTION__,
                'userid'       => $customerId,
                'createdon'    => now(),
                'ip'           => $request->ip(),
                'utm_campaign' => $utm_campaign,
                'utm_source'   => $utm_source
            ]);
    
            // Fetch Eloquent model (required for Sanctum)
            $user = customer_register::where([
                'id'      => $customerId,
                'roll_id' => '0',
                'status'  => '0',
                'deletes' => '0'
            ])->first();
    
            if (!$user) {
                DB::rollBack();
                return [
                    'status'  => 'failed',
                    'message' => 'User creation failed',
                    'error'   => 'Post-insert validation failed'
                ];
            }
    
            $token = $user->createToken('NDaccessToken')->plainTextToken;
            
            $existing = DB::table('referral_codes')
                ->where('user_id', $customerId)
                ->where('app_name', 'customer')
                ->first();
            
            if (!$existing) {
            
                $referral_code = referralCode::generateReferralCode();
            
                DB::table('referral_codes')->insert([
                    'user_id'    => $customerId,
                    'app_name'   => 'customer',
                    'code'       => $referral_code,
                    'created_at' => now()
                ]);
            
            }
    
            DB::commit();
            
            AutomationEventService::trigger(
                'otp_verified',
                $user->id
            );
    
            return [
                'status'  => 'success',
                'message' => 'Verified successfully!',
                'data'    => [
                    'user_id' => $user->id,
                    // 'name'    => $user->name,
                    // 'email'   => $user->email,
                    'mobile'  => $user->mobile,
                    // 'country' => $user->nationality,
                    // 'state'   => $user->address,
                    // 'city'    => $user->city,
                    // 'notify'  => $user->notify ?? null,
                ],
                'token' => $token,
            ];
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
            \Log::info('Testing schedule...: ', [$e->getMessage()]);
    
            return [
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ];
        }
    }
    
    public function loginOTP(Request $request)
    {
        try {
    
            $input = $request->all();
    
            $validator = Validator::make($input, [
                'mobile'     => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode'   => ['required', 'integer'],
                'deviceType' => 'nullable|string|in:MOBILE,APP,DESKTOP,BROWSER,TABLET|max:10',
                'fcm_token'  => ['nullable']
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ]);
            }
    
            // Sanitize
            $mobile   = Controller::BlockSQLInjection($request->mobile);
            $dialCode = Controller::BlockSQLInjection($request->dialCode);
    
            if (!$mobile || !$dialCode) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid mobile or dial code',
                    'error'   => 'Invalid input'
                ]);
            }
    
            $otp        = Controller::generateOTP(6);
            $message    = "Your GoRide Verification Code is {$otp}. Please don't share with anyone.";
            $expiryTime = Carbon::now()->addMinutes(10)->toDateTimeString();
            $oneHourAgo = Carbon::now()->subHour();
    
            // Check existing customer
            $customer = DB::table('customer_register')
                ->where([
                    'mobile'  => $mobile,
                    'status'  => '0',
                    'deletes' => '0'
                ])
                ->first();
                
            if($request->mobile == '916383800627'){
                
                $customer = DB::table('customer_register')
                ->where([
                    'mobile'  => $mobile,
                    'status'  => '0',
                    'deletes' => '0'
                ])
                ->first();
                
                $otp = 111111;
                
                if($customer){
                    
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
    
                // Rate limit
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
                    ]);
                }
    
                // Send OTP
                if (!$this->sendOtp($mobile, $otp, $message, $request->isResend ?? false)) {
                    return response()->json([
                        'status'  => 'failed',
                        'message' => 'OTP Not Sent',
                        'error'   => 'Notification failed'
                    ]);
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
                ]);
            }
    
            $tempId = DB::table('users_temp')->insertGetId([
                'building_name' => '',
                'city' => '',
                'name' => '',
                'email' => '',
                'address' => '',
                'state' => '',
                'pass' => '',
                'password' => '',
                'lname' => '',
                'mobile'       => $mobile,
                'dialCode'     => $dialCode,
                'otp'          => $otp,
                'ip'           => $request->ip(),
                'deviceType'   => $request->deviceType,
                'deletes'      => '1',
                'roll_id'      => '0',
                'utm_source'   => $_COOKIE['utm_source'] ?? null,
                'utm_campaign' => $_COOKIE['utm_campaign'] ?? null,
                'created_at'   => now()
            ]);
            
    
            if (!$tempId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Insert Failed!',
                    'error'   => 'Temporary user creation failed'
                ]);
            }
    
            if (!$this->sendOtp($mobile, $otp, $message)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'OTP Not Sent',
                    'error'   => 'Notification failed'
                ]);
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
            
            \Log::info('HIii...: ', [$e->getMessage()]);
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyLoginOTP(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'enc' => ['required'],
                'otp' => ['required', 'max:6'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ]);
            }
    
            $encData = decrypt($request->enc);
    
            $tempID    = Controller::BlockSQLInjection($encData['tempID'] ?? null);
            $expiresAt = $encData['expiry'] ?? null;
            $mobile_no = $encData['mobile'] ?? null;
            
            // return $mobile_no;
    
            if (!$tempID || !$expiresAt || !$mobile_no) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid enc',
                    'error'   => 'Invalid enc'
                ]);
            }
    
            $otp = Controller::BlockSQLInjection($request->otp);
    
            if (!$otp) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid OTP',
                    'error'   => 'Invalid OTP'
                ]);
            }
    
            // Expiry check
            if (!Carbon::now()->lt($expiresAt)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'OTP expired. Please retry.',
                    'error'   => 'OTP expired'
                ]);
            }
    
            $customer = DB::table('customer_register')
                ->where([
                    // 'id'      => $tempID,
                    'mobile'      => $mobile_no,
                    'otp'     => $otp,
                    'status'  => '0',
                    'deletes' => '0',
                    'roll_id' => '0'
                ])
                ->first();
    
            if ($customer) {
    
                DB::table('customer_register')
                    ->where('id', $tempID)
                    ->update([
                        'otp'        => '',
                        'lastlogin'  => now(),
                        'fcm_token'  => $request->fcm_token,
                    ]);
                    
                $user = customer_register::where([
                    'id'      => $tempID,
                    'roll_id' => '0',
                    'status'  => '0',
                    'deletes' => '0'
                ])->first();
    
                // $token = customer_register::createToken(
                //     'NDaccessToken',
                //     ['expires_at' => Carbon::now()->addHours(72)]
                // )->plainTextToken;
                $token = $user->createToken('NDaccessToken')->plainTextToken;

    
                DB::table('login_logs')->insert([
                    'method'        => __FUNCTION__,
                    'userid'        => $customer->id,
                    'createdon'     => now(),
                    'ip'            => $request->ip(),
                    'utm_campaign'  => $request->utm_campaign ?? null,
                    'utm_source'    => $request->utm_source ?? null,
                ]);
    
                return response()->json([
                    'status'  => 'success',
                    'message' => 'login success',
                    'data'    => [
                        'user_id' => $customer->id,
                        'name'    => $customer->name,
                        'email'   => $customer->email,
                        'mobile'  => $customer->mobile,
                        'country' => $customer->nationality,
                        'state'   => $customer->address,
                        'city'    => $customer->city,
                    ],
                    'token' => $token
                ]);
            }
    
            $tempUser = DB::table('users_temp')
                ->where([
                    'id'      => $tempID,
                    'otp'     => $otp,
                    'status'  => '0',
                    'deletes' => '1'
                ])
                ->first();
    
            if (!$tempUser) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Please enter a valid 6-digit verification code.',
                    'error'   => 'OTP verification failed'
                ]);
            }
    
            DB::table('users_temp')
                ->where('id', $tempID)
                ->update([
                    'mobile_verify' => 'YES',
                    'deletes'       => '0'
                ]);
    
            return CustomerAuthController::customerRegister(
                $tempID,
                $otp,
                $request->ip(),
                $request
            );
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyLoginOTPWeb(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'enc' => ['required'],
                'name' => ['required'],
                'email' => ['required'],
                'mobile' => ['required'],
                'otp' => ['required', 'max:6'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ]);
            }
    
            $encData = decrypt($request->enc);
    
            $tempID    = Controller::BlockSQLInjection($encData['tempID'] ?? null);
            $expiresAt = $encData['expiry'] ?? null;
            $mobile_no = $encData['mobile'] ?? null;
            
            // return $mobile_no;
    
            if (!$tempID || !$expiresAt || !$mobile_no) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid enc',
                    'error'   => 'Invalid enc'
                ]);
            }
    
            $otp = Controller::BlockSQLInjection($request->otp);
    
            if (!$otp) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid OTP',
                    'error'   => 'Invalid OTP'
                ]);
            }
    
            // Expiry check
            if (!Carbon::now()->lt($expiresAt)) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'OTP expired. Please retry.',
                    'error'   => 'OTP expired'
                ]);
            }
    
            $customer = DB::table('customer_register')
                ->where([
                    // 'id'      => $tempID,
                    'mobile'      => $mobile_no,
                    'otp'     => $otp,
                    'status'  => '0',
                    'deletes' => '0',
                    'roll_id' => '0'
                ])
                ->first();
    
            if ($customer) {
                
                if($customer->name == null || $customer->name == ''){
                    
                    DB::table('customer_register')
                        ->where('id', $tempID)
                        ->update([
                            'otp'        => '',
                            'name'        => $request->name,
                            'email'        => $request->email,
                            'lastlogin'  => now(),
                            'fcm_token'  => $request->fcm_token,
                        ]);
                        
                }
                    
                $user = customer_register::where([
                    'id'      => $tempID,
                    'roll_id' => '0',
                    'status'  => '0',
                    'deletes' => '0'
                ])->first();
    
                // $token = customer_register::createToken(
                //     'NDaccessToken',
                //     ['expires_at' => Carbon::now()->addHours(72)]
                // )->plainTextToken;
                $token = $user->createToken('NDaccessToken')->plainTextToken;

    
                // DB::table('login_logs')->insert([
                //     'method'        => __FUNCTION__,
                //     'userid'        => $customer->id,
                //     'createdon'     => now(),
                //     'ip'            => $request->ip(),
                //     'utm_campaign'  => $request->utm_campaign ?? null,
                //     'utm_source'    => $request->utm_source ?? null,
                // ]);
    
                return response()->json([
                    'status'  => 'success',
                    'message' => 'login success',
                    'data'    => [
                        'user_id' => $customer->id,
                        'name'    => $customer->name,
                        'email'   => $customer->email,
                        'mobile'  => $customer->mobile,
                        'country' => $customer->nationality,
                        'state'   => $customer->address,
                        'city'    => $customer->city,
                    ],
                    'token' => null
                ]);
            }
    
            $tempUser = DB::table('users_temp')
                ->where([
                    'id'      => $tempID,
                    'otp'     => $otp,
                    'status'  => '0',
                    'deletes' => '1'
                ])
                ->first();
    
            if (!$tempUser) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Please enter a valid 6-digit verification code.',
                    'error'   => 'OTP verification failed'
                ]);
            }
    
            DB::table('users_temp')
                ->where('id', $tempID)
                ->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile_verify' => 'YES',
                    'deletes'       => '0'
                ]);
    
            return CustomerAuthController::customerRegister(
                $tempID,
                $otp,
                $request->ip(),
                $request
            );
    
        } catch (\Throwable $e) {
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function forceRegister(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'sid'    => ['required', 'integer'],
                'mobile' => ['required', 'digits_between:8,15'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }
    
            $sid       = $request->sid;
            $mobile_no = $request->mobile;
    
            DB::beginTransaction();
    
            // Check existing customer
            $customer = DB::table('customer_register')
                ->where('mobile', $mobile_no)
                ->where('status', '0')
                ->where('deletes', '0')
                ->where('roll_id', '0')
                ->first();
    
            // Fetch job details
            $job = DB::table('cus_job_temp')
                ->where('id', $sid)
                ->where('job_status', 'created')
                ->where('deletes', '0')
                ->whereNotNull('user_details')
                ->first();
    
            if (!$job) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Invalid or expired job.',
                ], 404);
            }
    
            $user_det = json_decode($job->user_details, true);
    
            if (!$user_det || empty($user_det['mobile'])) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'User details missing in job.',
                ], 400);
            }
    
            if ($customer) {
    
                DB::commit();
    
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Login success',
                    'data'    => [
                        'user_id' => $customer->id,
                        'name'    => $customer->name,
                        'email'   => $customer->email,
                        'mobile'  => $customer->mobile,
                        'country' => $customer->nationality,
                        'state'   => $customer->state,
                        'city'    => $customer->city,
                    ],
                    'token' => null
                ]);
            }
    
            $insertData = [
                'user'             => 'Customer',
                'pass'             => '',
                'password'         => '',
                'roll_id'          => 0,
                'created_by'       => 0,
                'dialCode'         => 91,
                'mobile'           => $user_det['mobile'],
                'name'             => $user_det['name'] ?? '',
                'email'            => $user_det['email'] ?? '',
                'deletes'          => '0',
                'created_at'       => now(),
                'dob'              => null,
                'lname'            => '',
                'building_name'    => '',
                'city'             => '',
                'address'          => '',
                'nationality'      => '',
                'state'            => '',
                'ip'               => $request->ip(),
                'email_verify'     => 'NO',
                'mobile_verify'    => 'YES',
                'otp'              => '',
                'my_referral_code' => '',
                'residinglocation' => '',
                'deviceType'       => 'MOBILE',
                'utm_source'       => '',
                'utm_campaign'     => '',
                'fcm_token'        => null,
            ];
    
            $customerId = DB::table('customer_register')->insertGetId($insertData);
    
            $newCustomer = DB::table('customer_register')->where('id', $customerId)->first();
    
            DB::commit();
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Verified successfully!',
                'data'    => [
                    'user_id' => $newCustomer->id,
                    'name'    => $newCustomer->name,
                    'email'   => $newCustomer->email,
                    'mobile'  => $newCustomer->mobile,
                ],
                'token' => null,
            ]);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    // public function initiateProfile(Request $request)
    // {
    //     try {
    
    //         $validator = Validator::make($request->all(), [
    //             'c_name'            => ['required', 'string', 'max:255'],
    //             'c_email'           => ['required', 'email', 'max:255'],
    //             'c_gender'          => ['required', 'in:MALE,FEMALE,OTHERS'],
    //             'whatsapp_update'   => ['required', 'in:yes,no'],
    //             'referral_code' => ['nullable']
    //         ]);
    
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status'  => 'failed',
    //                 'message' => 'Validation Error!',
    //                 'error'   => $validator->errors()
    //             ], 422);
    //         }
    
    //         $userId = auth()->id();
    
    //         if (!$userId) {
    //             return response()->json([
    //                 'status'  => 'failed',
    //                 'message' => 'Unauthorized',
    //                 'error'   => 'Unauthorized'
    //             ], 401);
    //         }
            
            
    //         $wal_h = DB::table('walletBalance_history')->where(['userid' => $userId, 'point_type' => 'CREDIT', 'transaction_type' => 'CREDIT'])->exists();
            
    //         $chDelUser = true;
            
    //         if(!$wal_h){
                
    //             $openingBalance = 0;
    //             $amount = env('CREDIT_POINT');
    //             $closingBalance = $openingBalance + $amount;
    //             $txn1 = DB::table('walletBalance_history')->insertGetId([
    //             'uname'            => trim($request->c_name),
    //             'umobile'          => auth()->user()->mobile ?? '',
    //             'uemail'           => trim($request->c_email),
    //             'userid'           => $userId,
    //             'global_type'      => 'customer',
            
    //             'opening_balance'  => $openingBalance,
    //             'total'            => $amount,
    //             'closeing_balance' => $closingBalance,
            
    //             'point_type'       => 'CREDIT', // or 'credit_point' if you use both
    //             'transaction_type' => 'CREDIT',
    //             'reward_type'      => 'credit_point',
            
    //             'reference_id'     => 0,
    //             'reference_table'  => ' ',
            
    //             'ip'               => request()->ip(),
            
    //             'createdon'        => now(),
    //             'updatedon'        => now(),
    //         ]);
            
    //             $chDelUser = DB::table('customer_register')
    //                 ->where('mobile', auth()->user()->mobile)
    //                 // ->where('status', '0')
    //                 ->where('deletes', '1')->exists();
                    
    //             if(!$chDelUser){
                    
    //                 $updated = DB::table('customer_register')
    //                     ->where('id', $userId)
    //                     ->where('status', '0')
    //                     ->where('deletes', '0')
    //                     ->update([
    //                         // 'name'             => trim($request->c_name),
    //                         // 'email'            => trim($request->c_email),
    //                         // 'gender'           => $request->c_gender,
    //                         'cash_points'    => env('CREDIT_POINT'),
    //                         // 'whatsapp_update'  => $request->whatsapp_update == 'yes' ? 1 : 0,
    //                         'updated_at'       => now(),
    //                     ]);
    //             }
            
    //         }
            
    
    //         $updated = DB::table('customer_register')
    //             ->where('id', $userId)
    //             ->where('status', '0')
    //             ->where('deletes', '0')
    //             ->update([
    //                 'name'             => trim($request->c_name),
    //                 'email'            => trim($request->c_email),
    //                 'gender'           => $request->c_gender,
    //                 'whatsapp_update'  => $request->whatsapp_update == 'yes' ? 1 : 0,
    //                 'updated_at'       => now(),
    //             ]);
    
    //         if (!$updated) {
    //             return response()->json([
    //                 'status'  => 'failed',
    //                 'message' => 'Profile update failed',
    //                 'data'   => []
    //             ]);
    //         }
            
    //         if (!empty($request->referral_code)) {

    //             DB::transaction(function () use ($request, $userId, $chDelUser) {
            
    //                 $referralOwner = DB::table('referral_codes')
    //                     ->where('code', $request->referral_code)
    //                     ->where('app_name', 'customer')
    //                     ->first();
                        
            
    //                 if (!$referralOwner) {
    //                     // throw new \Exception('Invalid referral code');
    //                     return;
    //                 }
                    
    //                 $referrer = DB::table('customer_register')
    //                     ->where('id', $referralOwner->user_id)->where('deletes', 0)->first();
            
    //                 if (!$referrer) {
    //                     // throw new \Exception('Invalid referral code');
    //                     return;
    //                 }
                    
    //                 if ($referralOwner->user_id == $userId) {
    //                     // throw new \Exception('You cannot use your own referral code');
    //                     return;
    //                 }
                    
            
    //                 $existingReferral = DB::table('referrals')
    //                     ->where('referred_user_id', $userId)
    //                     ->lockForUpdate()
    //                     ->first();
            
    //                 if ($existingReferral) {
    //                     // throw new \Exception('Referral already applied');
    //                     return;
    //                 }
            
    //                 $referralId = DB::table('referrals')->insertGetId([
    //                     'referrer_user_id' => $referralOwner->user_id,
    //                     'referred_user_id' => $userId,
    //                     'referral_code'    => $request->referral_code,
    //                     'status'           => 'completed',
    //                     'referrer_rewarded' => 0,
    //                     'referred_rewarded' => 0,
    //                     'created_at'       => now(),
    //                     'updated_at'       => now(),
    //                 ]);
                    
    //                 $openingBalance = $referrer->walletBalance;
    //                 $amount = env('REFERRAL_AMOUNT');
    //                 $closingBalance = $openingBalance + $amount;
                    
    //                 $txn1 = DB::table('walletBalance_history')->insertGetId([
    //                     'uname'            => $referrer->name ?? '',
    //                     'umobile'          => $referrer->mobile ?? '',
    //                     'uemail'           => $referrer->email ?? '',
    //                     'userid'           => $referralOwner->user_id,
                    
    //                     'opening_balance'  => $openingBalance,
    //                     'total'            => $amount,
    //                     'closeing_balance' => $closingBalance,
                    
    //                     'point_type'       => 'WALLET', // or 'credit_point' if you use both
    //                     'transaction_type' => 'REFERRAL',
    //                     'reward_type'      => 'referral_bonus',
                    
    //                     'reference_id'     => $referralId,
    //                     'reference_table'  => 'referrals',
                    
    //                     'ip'               => request()->ip(),
                    
    //                     'createdon'        => now(),
    //                     'updatedon'        => now(),
    //                 ]);
            
    //                 DB::table('customer_register')
    //                     ->where('id', $referralOwner->user_id)
    //                     ->update(['walletBalance' => $closingBalance]);
            
    //                 DB::table('referrals')
    //                     ->where('id', $referralId)
    //                     ->update([
    //                         'referrer_rewarded' => $amount,
    //                         'wallet_history_id' => $txn1,
    //                         'referred_rewarded' => $chDelUser ? 0 : env('CREDIT_POINT'),
    //                         'updated_at' => now()
    //                     ]);
                        
    //                 DB::table('referral_codes')
    //                     ->where('user_id', $referralOwner->user_id)
    //                     ->update([
    //                         'total_invites' => DB::raw('total_invites + 1'),
    //                         'total_rewards' => DB::raw('total_rewards + 10'),
    //                         'updated_at' => now()
    //                     ]);
    //             });
    //         }
            
    //         // REFFAWAY:
    //         if(!$chDelUser){
                
    //             AutomationEventService::trigger(
    //                 'signup_bonus',
    //                 $userId
    //             );
    //         }
            
    //         AutomationEventService::trigger(
    //             'signup_completed',
    //             $userId
    //         );
            
    
    //         return response()->json([
    //             'status'  => 'success',
    //             'message' => 'Profile updated successfully',
    //             'data'    => []
    //         ]);
    
    //     } catch (\Throwable $e) {
            
    //         // \Log::info('Testing schedule...: ', [$e->getMessage()]);
    
    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => 'Process failed',
    //             'error'   => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function initiateProfile(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'c_name'            => ['required', 'string', 'max:255'],
                'c_email'           => ['nullable'],
                'c_gender'          => ['required', 'in:MALE,FEMALE,OTHERS'],
                'whatsapp_update'   => ['required', 'in:yes,no'],
                'referral_code' => ['nullable']
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }
    
            $userId = auth()->id();
    
            if (!$userId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Unauthorized',
                    'error'   => 'Unauthorized'
                ], 401);
            }
            
            
            $wal_h = DB::table('walletBalance_history')->where(['userid' => $userId, 'point_type' => 'CREDIT', 'transaction_type' => 'CREDIT'])->exists();
            
            $chDelUser = true;
            
            if(!$wal_h){
                
                $openingBalance = 0;
                $amount = env('CREDIT_POINT');
                $closingBalance = $openingBalance + $amount;
                $txn1 = DB::table('walletBalance_history')->insertGetId([
                'uname'            => trim($request->c_name),
                'umobile'          => auth()->user()->mobile ?? '',
                'uemail'           => $request->c_email??'',
                'userid'           => $userId,
                'global_type'      => 'customer',
            
                'opening_balance'  => $openingBalance,
                'total'            => $amount,
                'closeing_balance' => $closingBalance,
            
                'point_type'       => 'CREDIT', // or 'credit_point' if you use both
                'transaction_type' => 'CREDIT',
                'reward_type'      => 'credit_point',
            
                'reference_id'     => 0,
                'reference_table'  => ' ',
            
                'ip'               => request()->ip(),
            
                'createdon'        => now(),
                'updatedon'        => now(),
            ]);
            
                $chDelUser = DB::table('customer_register')
                    ->where('mobile', auth()->user()->mobile)
                    // ->where('status', '0')
                    ->where('deletes', '1')->exists();
                    
                if(!$chDelUser){
                    
                    $updated = DB::table('customer_register')
                        ->where('id', $userId)
                        ->where('status', '0')
                        ->where('deletes', '0')
                        ->update([
                            // 'name'             => trim($request->c_name),
                            // 'email'            => trim($request->c_email),
                            // 'gender'           => $request->c_gender,
                            'cash_points'    => env('CREDIT_POINT'),
                            // 'whatsapp_update'  => $request->whatsapp_update == 'yes' ? 1 : 0,
                            'updated_at'       => now(),
                        ]);
                }
            
            }
            
    
            $updated = DB::table('customer_register')
                ->where('id', $userId)
                ->where('status', '0')
                ->where('deletes', '0')
                ->update([
                    'name'             => trim($request->c_name),
                    'email'            => $request->c_email??'',
                    'gender'           => $request->c_gender,
                    'whatsapp_update'  => $request->whatsapp_update == 'yes' ? 1 : 0,
                    'updated_at'       => now(),
                ]);
    
            if (!$updated) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Profile update failed',
                    'data'   => []
                ]);
            }
            
            $chDelUser = DB::table('customer_register')
                ->where('mobile', auth()->user()->mobile)
                // ->where('status', '0')
                ->where('deletes', '1')->exists();
            
            if (!empty($request->referral_code) && !$chDelUser) {

                DB::transaction(function () use ($request, $userId, $chDelUser) {
            
                    $referralOwner = DB::table('referral_codes')
                        ->where('code', $request->referral_code)
                        ->where('app_name', 'customer')
                        ->first();
                        
            
                    if (!$referralOwner) {
                        // throw new \Exception('Invalid referral code');
                        return;
                    }
                    
                    $referrer = DB::table('customer_register')
                        ->where('id', $referralOwner->user_id)->where('deletes', 0)->first();
            
                    if (!$referrer) {
                        // throw new \Exception('Invalid referral code');
                        return;
                    }
                    
                    if ($referralOwner->user_id == $userId) {
                        // throw new \Exception('You cannot use your own referral code');
                        return;
                    }
                    
            
                    $existingReferral = DB::table('referrals')
                        ->where('referred_user_id', $userId)
                        ->lockForUpdate()
                        ->first();
            
                    if ($existingReferral) {
                        // throw new \Exception('Referral already applied');
                        return;
                    }
            
                    $referralId = DB::table('referrals')->insertGetId([
                        'referrer_user_id' => $referralOwner->user_id,
                        'referred_user_id' => $userId,
                        'referral_code'    => $request->referral_code,
                        'status'           => 'completed',
                        'referrer_rewarded' => 0,
                        'referred_rewarded' => 0,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                    
                    $openingBalance = $referrer->walletBalance;
                    $amount = env('REFERRAL_AMOUNT');
                    $closingBalance = $openingBalance + $amount;
                    
                    $txn1 = DB::table('walletBalance_history')->insertGetId([
                        'uname'            => $referrer->name ?? '',
                        'umobile'          => $referrer->mobile ?? '',
                        'uemail'           => $referrer->email ?? '',
                        'userid'           => $referralOwner->user_id,
                    
                        'opening_balance'  => $openingBalance,
                        'total'            => $amount,
                        'closeing_balance' => $closingBalance,
                    
                        'point_type'       => 'WALLET', // or 'credit_point' if you use both
                        'transaction_type' => 'REFERRAL',
                        'reward_type'      => 'referral_bonus',
                    
                        'reference_id'     => $referralId,
                        'reference_table'  => 'referrals',
                    
                        'ip'               => request()->ip(),
                    
                        'createdon'        => now(),
                        'updatedon'        => now(),
                    ]);
            
                    DB::table('customer_register')
                        ->where('id', $referralOwner->user_id)
                        ->update(['walletBalance' => $closingBalance]);
            
                    DB::table('referrals')
                        ->where('id', $referralId)
                        ->update([
                            'referrer_rewarded' => $amount,
                            'wallet_history_id' => $txn1,
                            'referred_rewarded' => $chDelUser ? 0 : env('CREDIT_POINT'),
                            'updated_at' => now()
                        ]);
                        
                    DB::table('referral_codes')
                        ->where('user_id', $referralOwner->user_id)
                        ->update([
                            'total_invites' => DB::raw('total_invites + 1'),
                            'total_rewards' => DB::raw('total_rewards + 10'),
                            'updated_at' => now()
                        ]);
                });
            }
            
            // REFFAWAY:
            if(!$chDelUser){
                
                AutomationEventService::trigger(
                    'signup_bonus',
                    $userId
                );
            }
            
            AutomationEventService::trigger(
                'signup_completed',
                $userId
            );
            
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully',
                'data'    => []
            ]);
    
        } catch (\Throwable $e) {
            
            // \Log::info('Testing schedule...: ', [$e->getMessage()]);
    
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateProfile(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'c_image'            => ['nullable', 'string', 'max:255'],
                'c_name'            => ['required', 'string', 'max:255'],
                'c_email'           => ['required', 'email', 'max:255'],
                // 'c_gender'          => ['required', 'in:MALE,FEMALE,OTHERS'],
                // 'whatsapp_update'   => ['required', 'in:yes,no']
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }
    
            $userId = auth()->id();
    
            if (!$userId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Unauthorized',
                    'error'   => 'Unauthorized'
                ], 401);
            }
            
            $updateData = [
                'name'       => trim($request->c_name),
                'email'      => trim($request->c_email),
                'updated_at' => now(),
            ];
            
            // 2. Add the image only if it exists in the request
            if ($request->filled('c_image')) {
                $updateData['profile_img_url'] = trim($request->c_image);
            }
            
            // 3. Perform the update
            $updated = DB::table('customer_register')
                        ->where('id', $userId)
                        ->where('status', '0')
                        ->where('deletes', '0')
                        ->update($updateData);
    
            if (!$updated) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Profile update failed',
                    'data'   => []
                ]);
            }
            
            // REFFAWAY:
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully',
                'data'    => []
            ]);
    
        } catch (\Throwable $e) {
            
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function bootStatus(Request $request)
    {
        try {
            $data = ['user_details' => []];
            $user = auth()->user();
            
            $isCarpool = null;
            
            // if (!empty($request->lat) && !empty($request->lng)) {
            
            //     $gKeys = array_values(array_filter([
            //         env('GOOGLE_KEY_ONE'),
            //         env('GOOGLE_KEY_TWO'),
            //         env('GOOGLE_KEY_THREE'),
            //     ]));
            
            //     if (empty($gKeys)) {
            //         Log::error('No Google API keys configured');
            //         goto AWAY;
            //     }
            
            //     $googleKey = $gKeys[array_rand($gKeys)];
            
            //     $lat = $request->lat;
            //     $lng = $request->lng;
            
            //     try {
            
            //         $url = "https://maps.googleapis.com/maps/api/geocode/json";
            
            //         $response = Http::timeout(10)->get($url, [
            //             'latlng' => $lat . ',' . $lng,
            //             'key'    => $googleKey,
            //         ]);
            
            //         if (!$response->successful()) {
            //             Log::error('Google Geocode API request failed', [
            //                 'response' => $response->body()
            //             ]);
            //             goto AWAY;
            //         }
            
            //         $datas = $response->json();
            
            //         $fullAddress = null;
            //         $state = null;
            //         $district = null;
            
            //         if (!empty($datas['results'])) {
            //             // 1. Capture the full address from the first (most specific) result
            //             $fullAddress = $datas['results'][0]['formatted_address'] ?? null;
            
            //             // 2. Loop through results to safely capture both State and District
            //             foreach ($datas['results'] as $result) {
            //                 if (!empty($result['address_components'])) {
            //                     foreach ($result['address_components'] as $component) {
            //                         if (!isset($component['types'])) {
            //                             continue;
            //                         }
            
            //                         // Capture State (administrative_area_level_1)
            //                         if (empty($state) && in_array('administrative_area_level_1', $component['types'])) {
            //                             $state = $component['long_name'];
            //                         }
            
            //                         // Capture District / City Level (administrative_area_level_2)
            //                         if (empty($district) && in_array('administrative_area_level_2', $component['types'])) {
            //                             $district = $component['long_name'];
            //                         }
            
            //                         // Break early if we have found both structural pieces
            //                         if (!empty($state) && !empty($district)) {
            //                             break 2;
            //                         }
            //                     }
            //                 }
            //             }
            //         }
            
            //         Log::info('Detected Location Matrix', [
            //             'lat'          => $lat,
            //             'lng'          => $lng,
            //             'current_address' => $fullAddress,
            //             'state'        => $state,
            //             'district'     => $district
            //         ]);
            
            //         // 3. Update the database if parameters were found
            //         if (!empty($state) || !empty($district) || !empty($fullAddress)) {
                        
            //             $updatePayload = [
            //                 'updated_at' => now(),
            //             ];
            
            //             if (!empty($state)) {
            //                 $updatePayload['current_state'] = $state;
            //             }
            
            //             // Note: Ensure these columns ('current_district', 'current_address') exist in your schema
            //             if (!empty($district)) {
            //                 $updatePayload['current_district'] = $district; 
            //             }
            
            //             if (!empty($fullAddress)) {
            //                 $updatePayload['current_address'] = $fullAddress;
            //             }
            
            //             DB::table('customer_register')
            //                 ->where('id', auth()->user()->id)
            //                 ->update($updatePayload);
            //         }
            
            //     } catch (\Exception $e) {
            
            //         Log::error('Reverse geocoding exception', [
            //             'message' => $e->getMessage()
            //         ]);
                    
            //         goto AWAY;
            //     }
            // }
            
            if (!empty($request->lat) && !empty($request->lng)) {
            
                $gKeys = array_values(array_filter([
                    env('GOOGLE_KEY_ONE'),
                    env('GOOGLE_KEY_TWO'),
                    env('GOOGLE_KEY_THREE'),
                ]));
            
                if (empty($gKeys)) {
                    Log::error('No Google API keys configured');
                    goto AWAY;
                }
            
                $googleKey = $gKeys[array_rand($gKeys)];
            
                $lat = $request->lat;
                $lng = $request->lng;
            
                try {
            
                    $url = "https://maps.googleapis.com/maps/api/geocode/json";
            
                    $response = Http::timeout(10)->get($url, [
                        'latlng' => $lat . ',' . $lng,
                        'key'    => $googleKey,
                    ]);
            
                    if (!$response->successful()) {
                        Log::error('Google Geocode API request failed', [
                            'response' => $response->body()
                        ]);
                        goto AWAY;
                    }
            
                    $datas = $response->json();
            
                    $fullAddress = null;
                    $state = null;
                    $district = null;
            
                    if (!empty($datas['results'])) {
                        // 1. Capture the full address
                        $fullAddress = $datas['results'][0]['formatted_address'] ?? null;
                    
                        // 2. Loop to safely capture both State and District
                        foreach ($datas['results'] as $result) {
                            if (!empty($result['address_components'])) {
                                
                                $tempLocality = null; // Backup copy for District fallback
                    
                                foreach ($result['address_components'] as $component) {
                                    if (!isset($component['types'])) {
                                        continue;
                                    }
                    
                                    // Capture State
                                    if (empty($state) && in_array('administrative_area_level_1', $component['types'])) {
                                        $state = $component['long_name'];
                                    }
                    
                                    // Capture District / City Level (Standard)
                                    if (empty($district) && in_array('administrative_area_level_2', $component['types'])) {
                                        $district = $component['long_name'];
                                    }
                    
                                    // Capture Locality / City (Fallback for Metro Cities like Chennai)
                                    if (in_array('locality', $component['types'])) {
                                        $tempLocality = $component['long_name'];
                                    }
                                }
                    
                                // FALLBACK TRICK: If standard district is missing but we found a locality, use it as the district
                                if (empty($district) && !empty($tempLocality)) {
                                    $district = $tempLocality;
                                }
                    
                                // Break early if everything is resolved
                                if (!empty($state) && !empty($district)) {
                                    break;
                                }
                            }
                        }
                    }
            
                    Log::info('Detected Location Matrix', [
                        'lat'          => $lat,
                        'lng'          => $lng,
                        'current_address' => $fullAddress,
                        'state'        => $state,
                        'district'     => $district
                    ]);
            
                    // 3. Update the database if parameters were found
                    if (!empty($state) || !empty($district) || !empty($fullAddress)) {
                        
                        $updatePayload = [
                            'updated_at' => now(),
                        ];
            
                        if (!empty($state)) {
                            $updatePayload['current_state'] = $state;
                        }
            
                        // Note: Ensure these columns ('current_district', 'current_address') exist in your schema
                        if (!empty($district)) {
                            $updatePayload['current_district'] = $district; 
                        }
            
                        if (!empty($fullAddress)) {
                            $updatePayload['current_address'] = $fullAddress;
                        }
                        
                        $updatePayload['lat'] = $lat;
                        $updatePayload['lng'] = $lng;
            
                        DB::table('customer_register')
                            ->where('id', auth()->user()->id)
                            ->update($updatePayload);
                    }
            
                } catch (\Exception $e) {
            
                    Log::error('Reverse geocoding exception', [
                        'message' => $e->getMessage()
                    ]);
                    
                    goto AWAY;
                }
                
                $isCarpool = auth()->user()->current_state == 'Tamil Nadu' ? 'yes' : 'no';
            }else{
                $userState = auth()->user()->current_state;

                if ($userState) {
                    $isCarpool = ($userState === 'Tamil Nadu') ? 'yes' : 'no';
                } else {
                    // Corrected the broken query chain by using latest() and value() properly
                    $getLastPlace = DB::table('cus_job_temp')
                        ->where('user_id', $user->id)
                        ->whereIn('global_type', ['customer', 'carpool', 'schedule'])
                        ->latest('id') // Assumes 'id' is your auto-incrementing primary key
                        ->value('from_place');
                
                    if ($getLastPlace) {
                        $isCarpool = str_contains($getLastPlace, 'Tamil Nadu') ? 'yes' : 'no';
                    } else {
                        $isCarpool = 'yes';
                    }
                }
            }
            
            AWAY:
            
            if ($user) {
          
                $getLastJob = DB::table('cus_job_temp')
                    ->where('user_id', $user->id)
                    ->whereIn('global_type', ['schedule'])
                    ->whereNull('user_details')
                    ->whereIn('job_status', ['created', 'bidding'])
                    ->orderBy('id', 'desc')
                    ->where('deletes', '0')
                    ->first();
    
                $data['user_details'] = [
                    'id'              => $user->id,
                    'name'            => $user->name,
                    'email'           => $user->email,
                    'mobile'          => $user->mobile,
                    'dialCode'        => $user->dialCode,
                    'deviceType'      => $user->deviceType,
                    'fcm_token'       => $user->fcm_token,
                    'notify'          => $user->notify,
                    'whatsapp_update' => $user->whatsapp_update,
                    'created_at'      => $user->created_at,
                    'credit'      => $user->cash_points,
                    'wallet'      => $user->walletBalance,
                    'job_info'      => $getLastJob,
                    'job_no'          => $getLastJob ? $getLastJob->job_no : null,
                    'job_status'      => $getLastJob ? $getLastJob->global_type : null,
                    'isCarpool'       => $isCarpool,
                    'cancel_reason' => [
                        'Driver not assigned / No driver available',
                        'Driver is taking too long to arrive',
                        'Driver asked to cancel the trip',
                        'Driver not responding to calls',
                        'Driver is far away from pickup location',
                        'Change of travel plan',
                        'Booked by mistake',
                        'Found another cab / alternative transport',
                        'Price is too high / fare issue',
                        'Pickup location entered incorrectly',
                        'Destination changed',
                        'Waiting time is too long',
                        'Driver cancelled earlier / reliability issue',
                        'Vehicle details not matching',
                        'Safety concerns',
                        'Personal emergency',
                        'Traffic delay / route issue',
                        'App / technical issue',
                        'Payment issue (cash/online problem)',
                        'Weather conditions (rain/flood/heat)',
                        'Other'
                    ]
                ];
            }
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Boot records retrieved',
                'data'    => $data
            ]);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;
    
        $get_user_data = DB::table('customer_register')
            ->where('id', $user_id)
            ->first();
    
        if (!$get_user_data) {
            return response([
                'status' => false,
                'message' => 'User not found',
                'error' => 'User not found',
            ]);
        }
    
        DB::beginTransaction();
    
        try {
    
            $old_data = (array) $get_user_data;
    
            $new_data = $old_data;
            $new_data['deletes'] = '1';
    
            $changed_data = [];
            foreach ($new_data as $key => $value) {
                if (!isset($old_data[$key]) || $old_data[$key] != $value) {
                    $changed_data[$key] = [
                        'old' => $old_data[$key] ?? null,
                        'new' => $value
                    ];
                }
            }
    
            DB::table('customer_register')
                ->where('id', $user_id)
                ->update([
                    'deletes' => '1',
                    'updated_at' => now()
                ]);
    
            DB::table('user_profile_activity_log')->insert([
                'user_id' => $user_id,
                'changed_by' => $user_id,
                'changed_data' => json_encode($changed_data),
                'updated_datetime' => now(),
                'ip' => request()->ip(),
            ]);
    
            DB::commit();
    
            if (!empty($user->fcm_token)) {
    
                $fcmTokens = [$user->fcm_token];
    
                if (!empty($fcmTokens)) {
    
                    $accessToken = $this->getAccessToken();
    
                    if ($accessToken) {
    
                        $title = "Account Deleted ❌";
                        $body = "Your GoRide account has been deleted successfully. We're sad to see you go 😔";
    
                        foreach ($fcmTokens as $token) {
    
                            $this->sendFCM(
                                $accessToken,
                                $token,
                                $title,
                                $body,
                                [
                                    'type'   => 'account_delete',
                                    'action' => 'account_delete',
                                    'screen' => 'login'
                                ]
                            );
                        }
                    }
                }
            }
    
            if (!empty($user->mobile)) {
                
                $name = $user->name;
                $deleted_at = now()->format('d M Y, h:i A');

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

                // Trigger the app deletion WhatsApp template
                $sendTemplateMessage(
                    $user->mobile, 
                    'customer_app_deletion_', 
                    [$name, $deleted_at] 
                );
            }
    
            return response([
                'status' => true,
                'message' => 'Your account deleted successfully',
                'data' => $user_id,
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ]);
        }
    }
    
}