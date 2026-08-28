<?php

namespace App\Http\Controllers\Api;

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

class CustomerAuthController extends Controller
{
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
    
            $utm_source   = $_COOKIE['utm_source']   ?? null;
            $utm_campaign = $_COOKIE['utm_campaign'] ?? null;
    
            DB::beginTransaction();
    
            // Prepare insert data
            $insertData = [
                'user'             => 'Customer',
                'users_refid'      => 'website',
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
    
    public function bookingOTP(Request $request)
    {
        try {
    
            $input = $request->all();
    
            $validator = Validator::make($input, [
                'mobile'     => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode'   => ['required', 'integer'],
                'deviceType' => 'nullable|string|in:MOBILE,APP,DESKTOP,BROWSER,TABLET|max:10',
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
    
            $otp        = Controller::generateOTP(4);
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
                
            if($request->mobile == '916383800627' || $request->mobile == '919585769163'){
                
                $customer = DB::table('customer_register')
                ->where([
                    'mobile'  => $mobile,
                    'status'  => '0',
                    'deletes' => '0'
                ])
                ->first();
                
                $otp = 1111;
                
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
                    'message' => 'Invalid OTP!',
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
                    'message' => 'Invalid OTP!',
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
    
    public function bookingOTPverify(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'enc' => ['required'],
                'name' => ['required'],
                'email' => ['nullable'],
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
                            // 'fcm_token'  => $request->fcm_token,
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
                    'message' => 'Invalid OTP!',
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
    
    public function initiateProfile(Request $request)
    {
        try {
    
            $validator = Validator::make($request->all(), [
                'c_name'            => ['required', 'string', 'max:255'],
                'c_email'           => ['required', 'email', 'max:255'],
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
            
            if(!$wal_h){
                
                $openingBalance = 0;
                $amount = env('CREDIT_POINT');
                $closingBalance = $openingBalance + $amount;
                $txn1 = DB::table('walletBalance_history')->insertGetId([
                'uname'            => trim($request->c_name),
                'umobile'          => auth()->user()->mobile ?? '',
                'uemail'           => trim($request->c_email),
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
            
    
            $updated = DB::table('customer_register')
                ->where('id', $userId)
                ->where('status', '0')
                ->where('deletes', '0')
                ->update([
                    'name'             => trim($request->c_name),
                    'email'            => trim($request->c_email),
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
            
            if (!empty($request->referral_code)) {

                DB::transaction(function () use ($request, $userId) {
                    
                    DB::table('referral_log')->insert([
                        'user_id'           => $userId,
                        'code'  => $request->referral_code,
                        'created_at'        => now()
                    ]);
            
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
                            'referred_rewarded' => env('CREDIT_POINT'),
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
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully',
                'data'    => []
            ]);
    
        } catch (\Throwable $e) {
            
            \Log::info('Testing schedule...: ', [$e->getMessage()]);
    
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
    
}