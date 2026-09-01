<?php

namespace App\Http\Controllers\Api;

use Razorpay\Api\Api;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
use App\Helpers\userLocationLog;
use Illuminate\Support\Facades\Cache;
use App\Models\UserRegister;
use Illuminate\Http\JsonResponse;

class GlobalAuthController extends Controller
{
    public function checkUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'login' => 'required|in:mobile,email',
                'value' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }
    
            $type = $request->login;
            $value = $request->value;
            $connections = ['global_auth', 'mysql'];
            
            $user = null;
            $foundInConnection = null;
    
            // Loop through connections and stop as soon as a record is found
            foreach ($connections as $connection) {
                $column = ($type === 'email') ? 'email' : 'mobile';
    
                $user = DB::connection($connection)
                    ->table('user_register')
                    ->where($column, $value)
                    ->first();
    
                if ($user) {
                    $foundInConnection = $connection;
                    break;
                }
            }
    
            return response()->json([
                'status' => true,
                'exists' => !empty($user),
                'avail' => $foundInConnection,
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->first_name,
                    'email' => $user->email,
                    'mobile' => $user->mobile
                ] : null
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function sendOTP(Request $request)
    {
        try {
            // 1. Validate payload
            $validator = Validator::make($request->all(), [
                'mobile'   => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode' => ['required', 'integer'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }
    
            $mobile   = trim($request->mobile);
            $dialCode = trim($request->dialCode);
    
            // 2. Check if user already exists in main registration
            $mobileCheck = DB::connection('global_auth')
                ->table('user_register')
                ->where('mobile', $mobile)
                ->where('status', '0')
                // ->where('deletes', '0')
                // ->where('roll_id', '0')
                ->exists();
    
            if ($mobileCheck) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'The mobile number you entered already has an account.',
                    'error'   => 'The mobile number you entered already has an account.'
                ], 400);
            }
    
            // 3. Rate limiting check (Max 3 OTP attempts per hour)
            $oneHourAgo = Carbon::now()->subHour()->toDateTimeString();
    
            $checkRepeat = DB::connection('global_auth')
                ->table('users_temp')
                ->where('mobile', $mobile)
                ->where('status', '0')
                ->where('deletes', '1')
                ->where('created_at', '>=', $oneHourAgo)
                ->count();
    
            if ($checkRepeat >= 3) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Try Again After 1 Hour',
                    'error'   => 'Too many OTP requests. Try after some time.'
                ], 429);
            }
    
            // 4. Generate OTP & payload
            $randotp  = Controller::generateOTP(6);
            $messages = "Your GoRide Verification Code is " . $randotp . ". Please don't share with anyone.";
    
            $whatsAppArr = [
                'mobile'            => $mobile,
                'templateName'      => 'national_draw_verification',
                'language'          => 'en',
                'templateBodyParam' => [(string) $randotp],
                'messages'          => $messages,
                'resend'            => filter_var($request->isResend, FILTER_VALIDATE_BOOLEAN),
            ];
    
            $utm_source   = $_COOKIE['utm_source'] ?? null;
            $utm_campaign = $_COOKIE['utm_campaign'] ?? null;
            $rawPassword  = $request->password ?? '123456';
    
            $insertData = [
                'building_name' => '',
                'city'          => '',
                'name'          => '',
                'email'         => '',
                'mobile'        => $mobile,
                'address'       => '',
                'state'         => '',
                'pass'          => '',
                'password'      => '',
                'deletes'       => '1',
                'dialCode'      => $dialCode,
                'otp'           => $randotp,
                'ip'            => $request->ip(),
                'deviceType'    => $request->deviceType ?? 'web',
                'roll_id'       => '0',
                'created_at'    => now(),
                'lname'         => '',
                'utm_source'    => $utm_source,
                'utm_campaign'  => $utm_campaign
            ];
    
            // 5. Insert temp record inside database transaction
            $tempINS = DB::connection('global_auth')
                ->table('users_temp')
                ->insertGetId($insertData);
    
            if (!$tempINS) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Insert Failed!',
                    'error'   => 'Could not record OTP session.'
                ], 500);
            }
    
            // 6. Message Dispatching (SMS / WhatsApp)
            $sentsms    = false;
            $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
    
            if ($check_mess && $check_mess->mess_type === 'sms') {
                $sentsms = Controller::smsNotificationUK($whatsAppArr, 'verify');
            } elseif ($check_mess && $check_mess->mess_type === 'whatsapp') {
                $sentsms = Controller::sendNotificationUK($whatsAppArr);
            }
    
            // Fallback for development/testing environments if no service configured
            if (!$check_mess) {
                $sentsms = true;
            }
    
            if (!$sentsms) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Please use a valid mobile number. Verification notification failed.',
                    'error'   => 'Notification delivery failed.'
                ], 400);
            }
    
            // 7. Success response
            $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Mobile OTP Sent Successfully!',
                'data'    => [
                    'enc' => encrypt([
                        'tempID' => $tempINS,
                        'expiry' => $timeAfterTenMinutes
                    ])
                ]
            ], 200);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                // 'message' => 'Process failed',
                'message' => $e->getMessage(),
                'error'   => [
                    'message' => $e->getMessage(),
                    'code'    => $e->getCode()
                ]
            ], 500);
        }
    }
    
    // public function verifyOtp(Request $request)
    // {
    //     DB::connection('global_auth')->beginTransaction();

    //     try {
            
    //         $validator = Validator::make(
    //             $request->all(),
    //             [
    //                 'mobile' => 'required',
    //                 'firebase_token' => 'required'
    //             ]
    //         );
    
    //         if ($validator->fails()) {
    
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $validator->errors()->first()
    //             ]);
    //         }
    
    //         $response = Http::post(
    
    //             'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key='
    //             . env('FIREBASE_API_KEY'),
    
    //             [
    //                 'idToken' => $request->firebase_token
    //             ]
    //         );
    
    //         if (!$response->successful()) {
    
    //             throw new \Exception(
    //                 'Invalid firebase token.'
    //             );
    //         }
    
    //         $firebaseData = $response->json();
    
    //         if (
    //             empty($firebaseData['users'][0])
    //         ) {
    //             throw new \Exception(
    //                 'User verification failed.'
    //             );
    //         }
    
    //         $firebaseUser =
    //             $firebaseData['users'][0];
    
    //         $firebaseMobile =
    //             $firebaseUser['phoneNumber']
    //             ?? null;
    
    //         if (
    //             $firebaseMobile !=
    //             $request->mobile
    //         ) {
    //             throw new \Exception(
    //                 'Mobile verification failed.'
    //             );
    //         }
    
    //         $user = DB::connection('global_auth')
    //                     ->table('user_register')
    //                     ->where(
    //                         'mobile',
    //                         $request->mobile
    //                     )
    //                     ->orWhere('email', $request->email)
    //                     ->first();
            
    //         $isNewUser = false;
    
    //         if (!$user) {
    
    //             $isNewUser = true;
    
    //             $userId = DB::connection('global_auth')
    //                     ->table('user_register')
    //                     ->insertGetId([
    //                         'uuid' => Str::uuid(),
    //                         'first_name' =>
    //                             $request->name,
    //                         'email' =>
    //                             $request->email,
    //                         'mobile' =>
    //                             $request->mobile,
    //                         'mobile_verified' => 1,
    //                         'firebase_uid' =>
    //                             $firebaseUser['localId']
    //                             ?? null,
    //                         'login_provider' =>
    //                             'mobile',
    //                         'created_at' =>
    //                             now(),
    //                         'updated_at' =>
    //                             now()
    //                     ]);
    
    //             $user = DB::connection('global_auth')
    //                         ->table('user_register')
    //                         ->where(
    //                             'id',
    //                             $userId
    //                         )
    //                         ->first();
    //         }
    //         else {
    
    //             DB::connection('global_auth')
    //                 ->table('user_register')
    //                 ->where(
    //                     'uuid',
    //                     $user->uuid
    //                 )
    //                 ->update([
    
    //                     'mobile_verified' => 1,
                        
    //                     'mobile' => $request->mobile,
    
    //                     'last_login_at' =>
    //                         now(),
    
    //                     'updated_at' =>
    //                         now()
    
    //                 ]);
    
    //             $user = DB::connection('global_auth')
    //                         ->table('user_register')
    //                         ->where(
    //                             'uuid',
    //                             $user->uuid
    //                         )
    //                         ->first();
    //         }
    
    //         /*
    //         |--------------------------------------------------------------------------
    //         | Sync UK Database
    //         |--------------------------------------------------------------------------
    //         */
    //         DB::connection('global_auth')
    //             ->commit();
    
    //         $authUser =
    //             UserRegister::find(
    //                 $user->id
    //             );
    
    //         $token =
    //             $authUser
    //                 ->createToken(
    //                     'customer'
    //                 )
    //                 ->plainTextToken;
    
    //         return response()->json([
    
    //             'status' => true,
    
    //             'message' =>
    //                 $isNewUser
    //                 ? 'Registration successful.'
    //                 : 'Login successful.',
    
    //             'is_new_user' =>
    //                 $isNewUser,
    
    //             'token' =>
    //                 $token,
    
    //             'user' =>
    //                 $authUser
    
    //         ]);
    
    //     }
    //     catch (\Exception $e) {
    
    //         DB::connection('global_auth')
    //             ->rollBack();
    
    //         return response()->json([
    
    //             'status' => false,
    
    //             'message' =>
    //                 $e->getMessage()
    
    //         ]);
    //     }
    // }
    
    public function verifyOtp(Request $request)
    {
        DB::connection('global_auth')->beginTransaction();
    
        try {
            // 1. Determine if the request is for an Indian phone number
            // Indian numbers start with +91 or 91, or dialCode is passed as 91
            $dialCode = (string) ($request->dialCode ?? '');
            $mobileClean = preg_replace('/[^0-9]/', '', $request->mobile);
            
            $isIndia = ($dialCode === '91' || str_starts_with($mobileClean, '91') || strlen($mobileClean) === 10);
    
            // 2. Conditional Validation Rules
            $rules = [
                'mobile' => 'required',
            ];
    
            if ($isIndia) {
                $rules['enc'] = 'required';
                $rules['otp'] = 'required';
            } else {
                $rules['firebase_token'] = 'required';
            }
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
    
            $firebaseUid = null;
    
            // 3. OTP Verification Routing
            if ($isIndia) {
                // --- INDIA OTP LOGIC (users_temp) ---
                try {
                    $decryptedData = decrypt($request->enc);
                    $tempID    = $decryptedData['tempID'] ?? null;
                    $expiresAt = $decryptedData['expiry'] ?? null;
                } catch (\Exception $e) {
                    throw new \Exception('Please use a valid enc payload!');
                }
    
                if (empty($tempID)) {
                    throw new \Exception('Please use a valid enc!');
                }
    
                if (empty($request->otp)) {
                    throw new \Exception('Please use a valid OTP!');
                }
    
                // Check expiration
                if (!Carbon::now()->lt(Carbon::parse($expiresAt))) {
                    throw new \Exception('Timeout. Kindly refresh and try again!');
                }
    
                // Verify temp OTP record
                $tempUser = DB::connection('global_auth')
                    ->table('users_temp')
                    ->where('id', $tempID)
                    ->where('status', '0')
                    ->where('otp', trim($request->otp))
                    ->where('deletes', '1')
                    ->orderBy('id', 'DESC')
                    ->first();
    
                if (!$tempUser) {
                    throw new \Exception('Invalid OTP or session expired.');
                }
    
                // Mark temp record as processed
                DB::connection('global_auth')
                    ->table('users_temp')
                    ->where('id', $tempID)
                    ->update(['deletes' => '0']);
    
            } else {
                // --- INTERNATIONAL OTP LOGIC (Firebase) ---
                $response = Http::post(
                    'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . env('FIREBASE_API_KEY'),
                    ['idToken' => $request->firebase_token]
                );
    
                if (!$response->successful()) {
                    throw new \Exception('Invalid firebase token.');
                }
    
                $firebaseData = $response->json();
    
                if (empty($firebaseData['users'][0])) {
                    throw new \Exception('User verification failed.');
                }
    
                $firebaseUser   = $firebaseData['users'][0];
                $firebaseMobile = $firebaseUser['phoneNumber'] ?? null;
                $firebaseUid    = $firebaseUser['localId'] ?? null;
    
                // Normalize mobile comparison
                if ($firebaseMobile && preg_replace('/[^0-9]/', '', $firebaseMobile) !== $mobileClean) {
                    throw new \Exception('Mobile verification failed.');
                }
            }
    
            // 4. Common Flow: Find existing user or register new user
            $user = DB::connection('global_auth')
                ->table('user_register')
                ->where('mobile', $request->mobile)
                ->when($request->filled('email'), function ($query) use ($request) {
                    return $query->orWhere('email', $request->email);
                })
                ->first();
    
            $isNewUser = false;
    
            if (!$user) {
                $isNewUser = true;
    
                $userId = DB::connection('global_auth')
                    ->table('user_register')
                    ->insertGetId([
                        'uuid'            => (string) Str::uuid(),
                        'first_name'      => $request->name ?? ($tempUser->name ?? ''),
                        'email'           => $request->email ?? ($tempUser->email ?? null),
                        'mobile'          => $request->mobile,
                        'mobile_verified' => 1,
                        'firebase_uid'    => $firebaseUid,
                        'login_provider'  => $isIndia ? 'mobile' : 'firebase_mobile',
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ]);
    
                $user = DB::connection('global_auth')
                    ->table('user_register')
                    ->where('id', $userId)
                    ->first();
            } else {
                DB::connection('global_auth')
                    ->table('user_register')
                    ->where('uuid', $user->uuid)
                    ->update([
                        'mobile_verified' => 1,
                        'mobile'          => $request->mobile,
                        'last_login_at'   => now(),
                        'updated_at'      => now()
                    ]);
    
                $user = DB::connection('global_auth')
                    ->table('user_register')
                    ->where('uuid', $user->uuid)
                    ->first();
            }
    
            DB::connection('global_auth')->commit();
    
            // 5. Generate Access Token via Sanctum
            $authUser = UserRegister::find($user->id);
            $token    = $authUser->createToken('customer')->plainTextToken;
    
            return response()->json([
                'status'      => true,
                'message'     => $isNewUser ? 'Registration successful.' : 'Login successful.',
                'is_new_user' => $isNewUser,
                'token'       => $token,
                'user'        => $authUser
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error(
                'Authenticate Error : ' .
                $e->getMessage()
            );
            DB::connection('global_auth')->rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function googleAuth(Request $request)
    {
        DB::connection('global_auth')->beginTransaction();
        
        try {
            // Access the array data passed via the HTTP request
            $googleUser = $request->input('google');
            
            if (empty($googleUser) || empty($googleUser['email'])) {
                throw new \Exception('Invalid Google user data received.');
            }
            
            $user = DB::connection('global_auth')
                        ->table('user_register')
                        ->where('email', $googleUser['email'])
                        ->first();
    
            if (!$user) {
                $userId = DB::connection('global_auth')
                    ->table('user_register')
                    ->insertGetId([
                        'uuid'          => Str::uuid(),
                        'first_name'    => $googleUser['given_name'] ?? null,
                        'last_name'     => $googleUser['family_name'] ?? null,
                        'email'         => $googleUser['email'],
                        'google_id'     => $googleUser['id'] ?? null,
                        'profile_image' => $googleUser['avatar'] ?? null,
                        'email_verified'=> 1,
                        'login_type'    => 'google',
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
    
                $user = DB::connection('global_auth')
                    ->table('user_register')
                    ->where('id', $userId)
                    ->first();
            }
            
            DB::connection('global_auth')->commit();
            
            // Fetch instance using Eloquent from the correct connection to generate token
            $authUser = UserRegister::on('global_auth')->find($user->id);
            $token = $authUser->createToken('customer')->plainTextToken;
            
            // Return the clean JSON response payload
            return response()->json([
                'status' => true,
                'token'  => $token,
                'user'   => $authUser
            ]);
            
        } catch (\Exception $e) {
            DB::connection('global_auth')->rollBack();
            
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'c_image' => ['nullable', 'string', 'max:255'],
                'c_name'  => ['required', 'string', 'max:255'],
                'c_email' => ['required', 'email', 'max:255'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Validation Error!',
                    'error'   => $validator->errors()
                ], 422);
            }
    
            $userId = auth()->id() ?? $request->user_id;
    
            if (!$userId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Unauthorized',
                    'error'   => 'User ID missing or unauthorized'
                ], 401);
            }
    
            // Fetch user model using global_auth connection
            $authUser = UserRegister::on('global_auth')
                ->where('id', $userId)
                // ->where('status', '0')
                // ->where('deletes', '0')
                ->first();
    
            if (!$authUser) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'User not found or account inactive',
                    'data'    => []
                ], 404);
            }
    
            // Prepare updates
            $authUser->first_name  = trim($request->c_name);
            $authUser->email = trim($request->c_email);
    
            if ($request->filled('c_image')) {
                $authUser->profile_image = trim($request->c_image);
            }
    
            // Perform save transactionally
            DB::connection('global_auth')->transaction(function () use ($authUser) {
                $authUser->save();
            });
    
            return response()->json([
                'status'  => 'success',
                'message' => 'Profile updated successfully',
                'data'    => $authUser->fresh()
            ]);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function me(Request $request)
    {
        try {
            
            $userId = auth()->id();
    
            if (!$userId) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Unauthorized',
                    'error'   => 'Invalid or expired access token.'
                ], 401);
            }
            
            $user = UserRegister::on('global_auth')
                ->where('id', $userId)
                // ->where('status', '0')
                // ->where('deletes', '0')
                ->first();
    
            if (!$user) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'User account not found or deactivated.',
                    'data'    => null
                ], 404);
            }
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Profile data fetched successfully',
                'data'    => [
                    'id'            => $user->id,
                    'name'          => $user->first_name,
                    'email'         => $user->email,
                    'mobile'        => $user->mobile,
                    'dialCode'      => $user->dialCode ?? null,
                    'profile_image' => $user->profile_image ?? null,
                    'address'       => $user->address ?? null,
                    'city'          => $user->city ?? null,
                    'nationality'   => $user->nationality ?? null,
                    'created_at'    => $user->created_at,
                ]
            ], 200);
    
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Process failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    
    public function verifyToken(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'user'   => [
                'id'      => $user->id,
                'uuid'    => $user->uuid,
                'name'    => $user->first_name,
                'email'   => $user->email,
                'mobile'  => $user->mobile,
                'roll_id' => $user->roll_id,
                // Add any other user attributes your app needs
            ]
        ], 200);
    }
}