<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\user_register;

use App\Http\Controllers\Template\mailController;

use Exception;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Carbon;


class LoginController extends Controller
{
  public function logout(Request $request)
  {
    try {
      $user = auth()->user();

      if ($user) {
        // Get the current access token
        $currentAccessToken = $user->currentAccessToken();

        if ($currentAccessToken) {
          // Delete the current access token
          $fcm_up = DB::table('user_register')
            ->where('id', auth()->id())
            ->update(['fcm_token' => null]);

          $currentAccessToken->delete();
          
          
          $response = ['status' => 'success', 'message' => 'Logout Successfully!', 'data' => 'Logout Successfully'];
          return response($response);
        } else {
          // Handle the case where the current access token is not found.
          $response = ['status' => 'failed',  'message' => 'Current access token not found',  'error' => 'Current access token not found'];
          return response($response, 404); // Return a 404 Not Found status code
        }
      } else {
        // Handle the case where the user is not authenticated.
        $response = ['status' => 'failed', 'message' => 'User not authenticated', 'error' => 'User not authenticated'];
        return response($response, 401); // Return a 401 Unauthorized status code
      }
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Logout failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function logout_message(Request $request)
  {
    try {

      return response()->json(['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'The authentication token has expired. Please log in again.']);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function logError(Request $request)
  {

    try {
      return response()->json(['status' => 'failed', 'message' => 'Log processing failed!', 'error' => 'Please contact the admin team for assistance.']);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function login(Request $request)
  {

    try {
      $type = $request->type;


      if ($type == 'EMAIL') {
        $validator =  Validator::make($request->all(), [
          'email' => 'required',
          'password' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json([
            "error" => 'validation_error',
            "message" => $validator->errors(),
          ], 422);
        }



        $request->email = Controller::BlockSQLInjection($request->email);
        if ($request->email == '' || $request->email == null || $request->email == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
          goto returnFVI;
        }

        $request->type = Controller::BlockSQLInjection($request->type);
        if ($request->type == '' || $request->type == null || $request->type == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
          goto returnFVI;
        }

        // $request->password = Controller::BlockSQLInjection($request->password);
        // if ($request->password == '' || $request->password == null || $request->password == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
        //   goto returnFVI;
        // }




        //check email
        $user = user_register::where(['email' => $request->email, 'pass' => md5($request->password), 'roll_id' => '0', 'status' => '0', 'deletes' => '0'])->first();
        // dd($user);
        // if($user == ""){
        //     return response(['status'=>failed,'message'=>'invalid email or password']);
        // }
        if ($user) {
          //create token
          $token = $user->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;

          $response = [

            'status' => 'success',
            'message' => 'login success',

            'data' => [
              'user_id' => $user->id,
              'name' => $user->name,
              'email' => $user->email,
              'mobile' => $user->mobile,
              'country' => $user->nationality,
              'state' => $user->address,
              'city' => $user->city,

            ],
            'token' => $token,
          ];

          return response($response);
        } else {
          $response = [

            'status' => 'failed',
            'message' => 'Invalid Email or password!',
            'error' => 'login failed!.',

          ];

          return response($response);
        }
      } else {

        $validator =  Validator::make($request->all(), [
          'mobile' => 'required',
          'password' => 'required',
        ]);

        if ($validator->fails()) {
          return response()->json([
            "error" => 'validation_error',
            "message" => $validator->errors(),
          ], 422);
        }


        $request->mobile = Controller::BlockSQLInjection($request->mobile);
        if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
          goto returnFVI;
        }

        $request->type = Controller::BlockSQLInjection($request->type);
        if ($request->type == '' || $request->type == null || $request->type == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
          goto returnFVI;
        }

        // $request->password = Controller::BlockSQLInjection($request->password);
        // if ($request->password == '' || $request->password == null || $request->password == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
        //   goto returnFVI;
        // }

        //check email
        $user = user_register::where(['mobile' => $request->mobile, 'pass' => md5($request->password), 'roll_id' => '0'])->first();

        if ($user == "") {
          $response = [

            'status' => 'failed',
            'message' => 'Invalid Mobile Number or password!', 'error' => 'Login Failed!.'

          ];

          return response($response);
        }


        //create token
        $token = $user->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;

        $response = [

          'status' => 'success',
          'message' => 'login success',
          'data' => [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'country' => $user->nationality,
            'state' => $user->address,
            'city' => $user->city,

          ],
          'token' => $token,
        ];

        return response($response);
      }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function loginWithPassword(Request $request)
  {

    try {

      $request->mobile = Controller::BlockSQLInjection($request->mobile);
      if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
        goto returnFVI;
      }


      if ($request->password == '' || $request->password == null || $request->password == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
        goto returnFVI;
      }
        
    // return $request->all();

      // check email
      $user = user_register::where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
    //   return $user;

      if ($user == "") {
        $response = [
          'status' => 'failed',
          'message' => 'The Phone Number that you`ve entered doesn`t match any account. Sign up for an account!',
          'error' => 'The Phone Number that you`ve entered doesn`t match any account. Sign up for an account!'
        ];
        goto returnFVI;
      }
      
        $check_block = DB::table('blocked_user')
            ->where('user_id', $user->id)
            ->where('expiry_date', '>=', now()) // block still active
            ->where('status', 0)
            ->where('deletes', 0)
            ->first();
        
        if ($check_block) {
            // Calculate activation date (expiry_date + 1 day)
            $activeAt = \Carbon\Carbon::parse($check_block->expiry_date)->addDay()->format('d-m-Y');
        
            $response = [
                'status'  => 'failed',
                'message' => 'Your Account is Blocked. It will be active at ' . $activeAt,
                'error'   => 'Your Account is Blocked. It will be active at ' . $activeAt,
            ];
            goto returnFVI;
        }

        


      if (md5($request->password) != $user->pass) {
        $response = [
          'status' => 'failed',
          'message' => 'Password does not match. Please check your password!',
          'error' => 'Password does not match. Please check your password!'
        ];
        // Log
        //  $log = Controller::error_log_new($request->ip(), 'mobile_login_password_error',  $user->id, '', '', 'mobile login successful',  json_encode($response), __DIR__, basename(__FILE__), __LINE__);
        goto returnFVI;
      }

      // create token
      $token = $user->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;
        
    // return response()->json(['hiii' => $request->all()]);
        
      $dataToUpdate = [
            'lastlogin' => now(),
            'password'  => $request->password,
        ];
        
        
        if ($request->platform_type == 'ios' || $request->platform_type == 'android') {
            $dataToUpdate['fcm_token'] = $request->fcm_token ?? null;
        }else{
            $dataToUpdate['browser_fcm_token'] = $request->browser_fcm_token ?? null;
        }
        // return response()->json(['message' => $dataToUpdate['fcm_token'], 'hiii' => $request->all()]);
        
        $update = DB::table('user_register')
            ->where('id', $user->id)
            ->update($dataToUpdate);

    $utm_source = $request->utm_source??null;
    $utm_campaign = $request->utm_campaign??null;

      $log =   DB::table('login_logs')->insert([
        'method' => __FUNCTION__,
        'userid' => $user->id, // user ID here
        'createdon' => now(),
        'ip' => $request->ip(),
        'utm_campaign'=> $utm_campaign,
        'utm_source'=> $utm_source
        // 'deletes' will be automatically set to '0' as per the default value
      ]);

      $response = [
        'status' => 'success',
        'message' => 'login success',
        'data' => [
          'user_id' => $user->id,
          'name' => $user->name,
          'lname' => $user->lname ?? '',
          'email' => $user->email,
          'mobile' => $user->mobile,
          'dialCode' => $user->dialCode,
          'country' => $user->nationality,
          'state' => $user->address,
          'city' => $user->city,
          'notify' => $user->notify,
        ],
        'token' => $token,
      ];

      // Log
      //   $log = Controller::error_log_new($request->ip(), 'mobile_login_success_',  $user->id, '', '', 'mobile login successful',  json_encode($response), __DIR__, basename(__FILE__), __LINE__);
      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function signin_otp(Request $request)
  {


    if ($request->type == "EMAIL") {
      // print_r($request->all());exit();

      $validator =  Validator::make($request->all(), [
        'type' => 'required',
        'email' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          "error" => 'validation_error',
          "message" => $validator->errors(),
        ], 422);
      }


      $request->email = Controller::BlockSQLInjection($request->email);
      if ($request->email == '' || $request->email == null || $request->email == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }


      $data = DB::table('user_register')->where(['email' => $request->email, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      //   dd($data);
      $subject = "National Draw | OTP to Signin Email - " . date("d-m-Y g:i a");
      if ($data) {
        $randotp = Controller::generateOTP(4);
        $update =  DB::table('user_register')->where(['email' => $request->email, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);
        $requestArr = [
          'name' => $data->name,
          'randotp' => $randotp,
        ];

        session(['otparray' => (intval(session('otparray')) + 1)]);


        $message = mailController::signUPotp($requestArr);
        $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);

        $response = [
          'status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,],
        ];
        return response($response);
      } else {
        $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => 'OTP Not Send!.'];
        return response($response);
      }
    } else {
      $validator =  Validator::make($request->all(), [
        'type' => 'required',
        'mobile' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          "error" => 'validation_error',
          "message" => $validator->errors(),
        ], 422);
      }


      $request->mobile = Controller::BlockSQLInjection($request->mobile);
      if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }




      //  dd($data);
      $data = DB::table('user_register')->where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if ($data) {
        if (substr($request->mobile, 0, 3) == "971") {


          // if($data){
          $subject = "National Draw | OTP to Signin MOBILE - " . date("d-m-Y g:i a");

          $randotp = Controller::generateOTP(4);

          $messages = "Your National Draw verification code is: " . $randotp;

          $sentsms = Controller::sendsms($request->mobile, $messages, '');

          if ($sentsms) {
            $otp_up = DB::table('user_register')->where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

            if ($otp_up) {
              $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,],];
              return response($response);
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'OTP Not Send!', 'error' => 'OTP  Failed!.'];
            return response($response);
          }

          // }
        } else {

          $response = ['status' => 'failed', 'message' => 'OTP Not Send', 'error' => 'OTP  Failed!.'];
          return response($response);
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'OTP Not Send', 'error' => 'OTP  Failed!.'];
        return response($response);
      }
    }

    returnFVI:
    return response()->json($response);
  }

  /////// New Login With OTP /////////
  public function loginOTP(Request $request)
  {
    try {
        
      
      $response = [];

      $request->mobile = Controller::BlockSQLInjection($request->mobile);
      if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
        goto returnFVI;
      }

      $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
      // Using the session helper
      $checkRepeat = DB::table('smslog')
        ->where('mobile', $request->mobile)
        // ->where('details', 'LIKE', '%verification code%')
        ->where('smssendstatus', '=', '1')
        ->where('datetime', '>=', $oneHourAgo)
        ->get();
      $limitVal = (substr($request->mobile, 0, 3) == "971") ? 5 : 3;
      if (isset($checkRepeat) && $checkRepeat->count() >= $limitVal) {
        $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
        goto returnFVI;
      }

      $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();

      $data = DB::table('user_register')->where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      
      
      
      if (isset($data->id) && $data->id != '') {
        // if (substr($data->mobile, 0, 3) == "971") {
        
        $check_block = DB::table('blocked_user')
            ->where('user_id', $data->id)
            ->where('expiry_date', '>=', now()) // block still active
            ->where('status', 0)
            ->where('deletes', 0)
            ->first();
        
        if ($check_block) {
            // Calculate activation date (expiry_date + 1 day)
            $activeAt = \Carbon\Carbon::parse($check_block->expiry_date)->addDay()->format('d-m-Y');
        
            $response = [
                'status'  => 'failed',
                'message' => 'Your Account is Blocked. It will be active at ' . $activeAt,
                'error'   => 'Your Account is Blocked. It will be active at ' . $activeAt,
            ];
            goto returnFVI;
        }

        $randotp = Controller::generateOTP(6);
        // $randotp = '123456';
        // $messages = "Your GoRide Login Code is " . $randotp . ". Please don't share with anyone.";
        $messages = "Your GoRide Verification Code is " . $randotp . ". Please don't share with anyone.";

        $whatsAppArr = [
          'mobile' => $request->mobile,
          'templateName' => 'national_draw_verification',
          'language' => 'en',
          'templateBodyParam' => [
            strval($randotp)
          ],
          'messages' => $messages,
          'resend' => ($request->isResend === "true" ? true : false)
        ];
        
        $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
                                
        if($check_mess && $check_mess->mess_type == 'sms'){
            
            $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
            
        }elseif($check_mess && $check_mess->mess_type == 'whatsapp'){
            
            $sentsms = Controller::sendNotification($whatsAppArr);
        }


        
        // return $sentsms

      
    //   return $sentsms;
        //  dd($sentsms);

        // if (!$sentsms) {
        //   $whatsAppArr['resend'] = true;
        // //   $sentsms = Controller::sendNotification($whatsAppArr);
        //   $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
            
        // }


        // if (substr($data->mobile, 0, 3) != "971") {

        //   if (isset($request->isResend) && $request->isResend === "true") {
        //     // Temporarily SMS services have been stopped NON UAE 18-02-2024
        //     // goto sendSMS;
        //     goto sendWhatsApp;
        //   }

        //   sendWhatsApp:
        //   $sentsms = Controller::sendWhatsApp($whatsAppArr);
        //   if (!$sentsms) {
        //     goto sendSMS;
        //   } else {
        //     goto skipSMS;
        //   }
        // } else {
        //   goto sendSMS;
        // }

        // sendSMS:
        // // $sentsms = Controller::sendWhatsApp($data->mobile, $messages);
        // // Temporarily SMS services have been stopped NON UAE 18-02-2024
        // if (substr($data->mobile, 0, 3) == "971") {
        //   $sentsms = Controller::sendsms($data->mobile, $messages, '');
        // }
        // skipSMS:


        if ($sentsms) {



          $otp_up = DB::table('user_register')->where(['id' => $data->id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp, 'updated_at' => now()]);

          if ($otp_up) {
            $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'OTP Not Send!', 'error' => 'OTP  Failed!.'];
          goto returnFVI;
        }
        // } else {
        //   $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
        //   goto returnFVI;
        // }
      } else {
        $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => 'User Not Found!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
  
  public function driverBindOTP(Request $request)
  {
    try {
        
      
      $response = [];

        $request->mobile = Controller::BlockSQLInjection($request->mobile);
        // return auth()->user();
        if (($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') || $request->mobile == auth()->user()->mobile) {
            $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
            goto returnFVI;
        }
        

        $oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));
        
        $checkRepeat = DB::table('smslog')
            ->where('mobile', $request->mobile)
            ->where('smssendstatus', '=', '1')
            ->where('w_type', 'bind_otp')
            ->where('auth_id', auth()->user()->id)
            ->where('datetime', '>=', $oneDayAgo)
            ->get();

        $limitVal = (substr($request->mobile, 0, 3) == "971") ? 5 : 5;
        
        // return $checkRepeat->count();
        if ($checkRepeat->count() >= $limitVal) {
            $response = [
                'status' => 'failed',
                'message' => 'Try Again After 1 Day',
                'error' => 'You have reached the daily OTP limit'
            ];
            goto returnFVI;
        }
        
        $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();
        
        $data = DB::table('user_register')
                ->select('user_register.id')
                ->join('kyc_details as kd', 'kd.user_id', '=', 'user_register.id')
                ->where([
                    'user_register.mobile' => $request->mobile,
                    'user_register.roll_id' => '0',
                    'user_register.deletes' => '0',
                    'user_register.status' => '0',
                    'kd.type' => 'Owner'
                ])
                ->first();

      if (isset($data->id) && $data->id != '') {
          
        $randotp = Controller::generateOTP(6);
        $messages = "Your GoRide Verification Code is " . $randotp . ". Please don't share with anyone.";

        $whatsAppArr = [
          'mobile' => $request->mobile,
          'templateName' => 'national_draw_verification',
          'language' => 'en',
          'templateBodyParam' => [
            strval($randotp)
          ],
          'messages' => $messages,
          'resend' => ($request->isResend === "true" ? true : false)
        ];
        
        $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
                                
        if($check_mess && $check_mess->mess_type == 'sms'){
            
            // $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
            $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
            
        }elseif($check_mess && $check_mess->mess_type == 'whatsapp'){
            
            // $sentsms = Controller::sendNotification($whatsAppArr);
            $sentsms = Controller::sendNotification($whatsAppArr);
        }



        // if (!$sentsms) {
        //   $whatsAppArr['resend'] = true;
        // //   $sentsms = Controller::sendNotification($whatsAppArr);
            
        // }

        if ($sentsms) {
            
           $update_data = DB::table('smslog')
                        ->where('mobile', $request->mobile)
                        ->where('token_response', 'LIKE', "%{$randotp}%")
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->update([
                            'w_type' => 'bind_otp',
                            'auth_id' => auth()->user()->id
                        ]);

          $otp_up = DB::table('user_register')->where(['id' => auth()->user()->id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp, 'updated_at' => now()]);

          if ($otp_up) {
            $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => auth()->user()->id, 'owner_id' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'OTP Not Send!', 'error' => 'OTP  Failed!.'];
          goto returnFVI;
        }
        // } else {
        //   $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
        //   goto returnFVI;
        // }
      } else {
        $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => 'User Not Found!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
  
  public function driverBindOTPverify(Request $request)
  {

    try {

      $tempID = decrypt($request->enc)['tempID'];

      $expiresAt = decrypt($request->enc)['expiry'];
      
      $owner_id = decrypt($request->enc)['owner_id'];

      $tempID = Controller::BlockSQLInjection($tempID);
      if ($tempID == '' || $tempID == null || $tempID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Data!', 'error' => 'Please use a valid Data!'];
        goto returnFVI;
      }
      
      $owner_id = Controller::BlockSQLInjection($owner_id);
      if ($owner_id == '' || $owner_id == null || $owner_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Data!', 'error' => 'Please use a valid Data!'];
        goto returnFVI;
      }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }

      if (strlen($request->otp) != 6) {
        $response = ['status' => 'failed', 'message' => 'Please use 6 digit only!', 'error' => 'Please use 6 digit only!'];
        goto returnFVI;
      }



      if (!Carbon::now()->lt($expiresAt)) {
        $response = ['status' => 'success', 'message' => "Timeout. Kindly refresh and try again!", 'data' =>  "Timeout. Kindly refresh and try again!"];
        goto returnFVI;
      }



      $data = user_register::where(['id' => $tempID, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if ($data) {

        // Log
        // $log = Controller::error_log_new($request->ip(), 'mobile_loginotp_success',  $data->id, '', '', 'mobile login successful',  json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
        // return $owner_id;

        $user = DB::table('user_register')
            ->where('id', $owner_id)
            ->where('status', '0')
            ->where('deletes', '0')
            ->first();
            
        $data->isOpenjob = 0;
        $data->isBidding = 0;
        $data->notify = 0;
        $data->owner_id = $owner_id;
        $data->save();
        
        if ($user) {
            $drivers = json_decode($user->drivers_ids ?? '[]', true) ?: [];
        
            if (!in_array($owner_id, $drivers)) {
                $drivers[] = $data->id;
        
                DB::table('user_register')
                    ->where('id', $owner_id)
                    ->update([
                        'drivers_ids' => json_encode($drivers),
                    ]);
            }else{
                $response = [
                    'status' => 'failed',
                    'message' => 'Already joined',
                    'data' => null,
                ];
                
                goto returnFVI;
            }
        
            $user = DB::table('user_register')
                ->join('kyc_details as kd', 'kd.user_id', '=', 'user_register.id')
                ->select(
                    'user_register.name',
                    'user_register.email',
                    'user_register.mobile',
                    'user_register.company_name',
                    'kd.selfie_url',
                    'user_register.city',
                    'kd.type as kyc_type'
                )
                ->where('user_register.id', $owner_id)
                ->where('user_register.deletes', '0')
                ->where('user_register.status', '0')
                ->first();

$company = $user->company_name ? $user->company_name . ' agency' : 'agency';

$messages = 'ðŸ‘‹ *New Driver Joined Your agency!* ðŸš—

Hi '.$user->name.', a new driver has just joined under your '.$company.'. ðŸŽ‰  

ðŸ‘¤ *Driver Name:* '.$data->name.'
ðŸš˜ *Mobile No:* '.$data->mobile.'

Their registration is complete, and theyâ€™re now connected with your account.  
You can view and manage their details in your Agent Dashboard. ðŸ’¼  

ðŸš– *GoRide â€” Drive Smart. Earn More. Manage Better.*';

    
            $whatsAppArr = [
              'mobile' => $user->mobile,
              'templateName' => 'national_draw_verification',
              'language' => 'en',
              'templateBodyParam' => [
                ''
              ],
              'messages' => $messages,
              'resend' => ($request->isResend === "true" ? true : false)
            ];
    
    
            $sentsms = Controller::sendNotification($whatsAppArr);
            // return $sentsms;
            if (!$sentsms) {
              $whatsAppArr['resend'] = true;
              $sentsms = Controller::sendNotification($whatsAppArr);
            //   $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
                
            }
        
            $response = [
                'status' => 'success',
                'message' => 'Now you joined to the '. $user->company_name ?? $user->name .' Agency',
                'data' => $user
            ];
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'User not found or inactive',
                'data' => null,
            ];
        }

        goto returnFVI;
      } else {


        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'Invalid OTP!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function resend_otp(Request $request)
  {

    if ($request->type == "EMAIL") {
      //print_r($request->all());exit();

      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }

      $data = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

      $subject = "National Draw | RESEND OTP to Signin Email - " . date("d-m-Y g:i a");

      if ($data) {
        $randotp = Controller::generateOTP(4);
        $update =  DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);
        $requestArr = [
          'name' => $data->name,
          'randotp' => $randotp,
        ];



        $message = mailController::signUPotp($requestArr);
        $sendEmail = Controller::composeEmail($request->ip(), $data->email, $subject, $message);

        $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,],];
        return response($response);
      } else {
        $response = ['status' => 'failed', 'message' => 'Resend OTP Failed!', 'error' => 'Resend OTP Failed'];
        return response($response);
      }
    } else {



      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }


      $data = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if ($data) {
        if (substr($data->mobile, 0, 3) == "971") {


          $subject = "National Draw | RESEND OTP to Signin MOBILE - " . date("d-m-Y g:i a");

          $randotp = Controller::generateOTP(4);

          $messages = "Your National Draw verification code is: " . $randotp;

          $sentsms = Controller::sendsms($data->mobile, $messages, '');

          if ($sentsms) {
            $otp_up = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

            if ($otp_up) {
              $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,]];
              return response($response);
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'Resend Failed!', 'error' => 'Resend Failed!.'];
            return response($response);
          }
        } else {

          $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed!.'];
          return response($response);
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed!.'];
        return response($response);
      }
    }

    returnFVI:
    return response()->json($response);
  }
  
  public function otp_verify(Request $request)
  {
    //   dd($request->all());

    if ($request->type == "EMAIL") {


      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }


      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }


      $data = user_register::where(['id' => $request->user_id, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

      if ($data) {

        $token = $data->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;

        $response = [

          'status' => 'success',
          'message' => 'login success',
          'data' => [
            'user_id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'mobile' => $data->mobile,
            'country' => $data->nationality,
            'state' => $data->address,
            'city' => $data->city,

          ],
          'token' => $token,
        ];

        return response($response);
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'Verify Failed'];
        return response($response);
      }
    } else {



      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }


      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }

      $data = user_register::where(['id' => $request->user_id, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      //   dd($data);
      if ($data) {

        $token = $data->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;

        $response = [

          'status' => 'success',
          'message' => 'login success',
          'data' => [
            'user_id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'mobile' => $data->mobile,
            'country' => $data->nationality,
            'state' => $data->address,
            'city' => $data->city,

          ],
          'token' => $token,
        ];

        return response($response);
      } else {

        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'Verify Failed!.'];
        return response($response);
      }
    }



    returnFVI:
    return response()->json($response);
  }



  //forgot email/mobile otp
  // public function forgot(Request $request)
  // {
  //   if ($request->type == "EMAIL") {

  //     $validator =  Validator::make($request->all(), [
  //       'type' => 'required',
  //       'email' => 'required'
  //     ]);

  //     if ($validator->fails()) {
  //       return response()->json([
  //         "error" => 'validation_error',
  //         "message" => $validator->errors(),
  //       ], 422);
  //     }



  //     $request->email = Controller::BlockSQLInjection($request->email);
  //     if ($request->email == '' || $request->email == null || $request->email == 'null') {
  //       $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
  //       goto returnFVI;
  //     }

  //     $request->type = Controller::BlockSQLInjection($request->type);
  //     if ($request->type == '' || $request->type == null || $request->type == 'null') {
  //       $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
  //       goto returnFVI;
  //     }


  //     $data = DB::table('user_register')->where(['email' => $request->email, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

  //     $subject = "National Draw | Forgot password - " . date("d-m-Y g:i a");
  //     if ($data) {
  //       $randotp = Controller::generateOTP(4);
  //       $update =  DB::table('user_register')->where(['email' => $request->email, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);
  //       $requestArr = [
  //         'name' => $data->name,
  //         'randotp' => $randotp,
  //       ];

  //       session(['otparray' => (intval(session('otparray')) + 1)]);


  //       $message = mailController::forgot($requestArr);
  //       $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);

  //       $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,],];
  //       return response($response);
  //     } else {
  //       $response = ['status' => 'failed', 'message' => 'user not found', 'error' => 'user not found'];
  //       return response($response);
  //     }
  //   } else {
  //     $validator =  Validator::make($request->all(), [
  //       'type' => 'required',
  //       'mobile' => 'required',

  //     ]);

  //     if ($validator->fails()) {
  //       return response()->json([
  //         "error" => 'validation_error',
  //         "message" => $validator->errors(),
  //       ], 422);
  //     }


  //     $request->mobile = Controller::BlockSQLInjection($request->mobile);
  //     if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
  //       $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
  //       goto returnFVI;
  //     }

  //     $request->type = Controller::BlockSQLInjection($request->type);
  //     if ($request->type == '' || $request->type == null || $request->type == 'null') {
  //       $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
  //       goto returnFVI;
  //     }




  //     // dd($request->mobile);
  //     $data = DB::table('user_register')->where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
  //     //   dd($data);
  //     if ($data) {
  //       if (substr($data->mobile, 0, 3) == "971") {


  //         // if($data){
  //         $subject = "National Draw | Forgot password - " . date("d-m-Y g:i a");

  //         $randotp = Controller::generateOTP(4);

  //         $messages = "Hello " . $data->name . ", " . $randotp . " is the One Time Password (OTP) to Forgot Password the National Draw Account.";
  //         $sentsms = Controller::sendsms($data->mobile, $messages, '');

  //         if ($sentsms) {
  //           $otp_up = DB::table('user_register')->where(['id' => $data->id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

  //           if ($otp_up) {
  //             $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id]];
  //             return response($response);
  //           }
  //         } else {
  //           $response = ['status' => 'failed', 'message' => 'user not found', 'error' => 'user not found'];
  //           return response($response);
  //         }

  //         // }
  //       } else {

  //         $response = ['status' => 'failed', 'message' => 'OTP Not Send', 'error' => 'OTP Failed!.'];
  //         return response($response);
  //       }
  //     } else {

  //       $response = ['status' => 'failed', 'message' => 'OTP Not Send', 'error' => 'OTP Failed!.'];
  //       return response($response);
  //     }
  //   }


  //   returnFVI:
  //   return response()->json($response);
  // }


  public function forgot_resendotp(Request $request)
  {
    //   dd($request);

    if ($request->type == "EMAIL") {
      //print_r($request->all());exit();


      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }


      $data = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

      $subject = "National Draw | Forgot password Resend OTP - " . date("d-m-Y g:i a");

      if ($data) {
        $randotp = Controller::generateOTP(4);
        $update =  DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);
        $requestArr = [
          'name' => $data->name,
          'randotp' => $randotp,
        ];



        $message = mailController::forgot($requestArr);
        $sendEmail = Controller::composeEmail($request->ip(), $data->email, $subject, $message);

        $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id,]];
        return response($response);
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid Datas!', 'error' => 'Resend OTP Failed'];
        return response($response);
      }
    } else {

      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }



      $data = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

      if (substr($data->mobile, 0, 3) == "971") {



        $subject = "National Draw |Forgot password Resend OTP - " . date("d-m-Y g:i a");

        $randotp = Controller::generateOTP(4);

        $messages = "Your National Draw verification code is: " . $randotp;

        $sentsms = Controller::sendsms($data->mobile, $messages, '');

        if ($sentsms) {
          $otp_up = DB::table('user_register')->where(['id' => $request->user_id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

          if ($otp_up) {
            $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['user_id' => $data->id],];
            return response($response);
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'OTP Not Send!', 'error' => 'Resend Failed!.'];
          return response($response);
        }
      } else {

        $response = ['status' => 'failed', 'message' => 'OTP Not Send', 'error' => 'Resend Failed!.'];
        return response($response);
      }
    }


    returnFVI:
    return response()->json($response);
  }

  public function forgot_otpverify(Request $request)
  {


    if ($request->type == "EMAIL") {

      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }


      $data = user_register::where(['id' => $request->user_id, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      // dd($data);
      if ($data) {


        $response = [

          'status' => 'success',
          'message' => 'Otp Verified successfully!',

          'data' => ['user_id' => $data->id],

        ];

        return response($response);
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Failed!.'];
        return response($response);
      }
    } else {


      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
        goto returnFVI;
      }

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
        goto returnFVI;
      }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }

      $data = user_register::where(['id' => $request->user_id, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if ($data) {


        $response = [

          'status' => 'success',
          'message' => 'Otp Verified successfully!',
          'data' => ["user_id" => $data->id],

        ];

        return response($response);
      } else {

        $response = ['status' => 'failed', 'message' => 'Invalid OTP', 'error' => 'OTP verify Failed!.'];
        return response($response);
      }
    }


    returnFVI:
    return response()->json($response);
  }

  public function forgot_password_update(Request $request)
  {
    // dd('erfwetrw');
    $validator =  Validator::make($request->all(), [
      'password' => 'required|min:6',
      'confirm_password' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
        "error" => 'validation_error',
        "message" => $validator->errors(),
      ], 422);
    }



    $request->user_id = Controller::BlockSQLInjection($request->user_id);
    if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
      goto returnFVI;
    }

    $request->type = Controller::BlockSQLInjection($request->type);
    if ($request->type == '' || $request->type == null || $request->type == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
      goto returnFVI;
    }

    $request->otp = Controller::BlockSQLInjection($request->otp);
    if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
      goto returnFVI;
    }


    // $request->password = Controller::BlockSQLInjection($request->password);
    // if ($request->password == '' || $request->password == null || $request->password == 'null') {
    //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
    //   goto returnFVI;
    // }

    // $request->confirm_password = Controller::BlockSQLInjection($request->confirm_password);
    // if ($request->confirm_password == '' || $request->confirm_password == null || $request->confirm_password == 'null') {
    //   $response = ['status' => 'failed', 'message' => 'Please use a valid confirm password!', 'error' => 'Please use a valid confirm password!'];
    //   goto returnFVI;
    // }



    $password = $request->password;
    $confirm_password = $request->confirm_password;

    $datas = DB::table('user_register')->where(['id' => $request->user_id, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
    // dd($datas);
    if ($password == $confirm_password) {
      if ($datas) {

        $pass = md5($request->password);
        $update = DB::table('user_register')->where('id', $request->user_id)->update(['password' => $request->password, 'pass' => $pass, 'otp' => ""]);

        $response = [

          'status' => 'success',
          'message' => 'Password Updated successfully!',
          'data' => ["user_id" => $datas->id],

        ];
        return response($response);
      } else {
        $response = [

          'status' => 'failed',
          'message' => 'Password  Not updated',
          'error' => 'Update Failed!.',

        ];
        return response($response);
      }
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'Password  Not Matched',
        'error' => 'Password  Not Matched!.',

      ];
      return response($response);
    }



    returnFVI:
    return response()->json($response);
  }

  public function customer_view(Request $request)
  {
    $auth = auth()->user()->id;
    $base_url = ($request->header('Origin') . '/');

    $data = DB::table('user_register')->select(
      'user_register.id',
      'user_register.name',
      'user_register.dialCode',
      'user_register.mobile',
      'user_register.email',
      'user_register.building_name',
      'user_register.nationality',
      'user_register.address',
      'user_register.city',
      'user_register.account_name',
      'user_register.account_no',
      'user_register.acctype',
      'user_register.bank_name',
      'user_register.branch_name',
      'user_register.branch_code',
      'user_register.IBAN_code',
      'user_register.swift_code',
      'user_register.dob',
      'user_register.currency_code',
      'user_register.passport',
      'user_register.exchangeid',
      'user_register.t_earning'
      // ,'user_images.user_id as uid','user_images.img_url','user_images.type','user_images.deletes as del'
    )
      // ->leftJoin('user_images','user_images.user_id','=','user_register.id')
      ->where(['user_register.id' => $auth, 'user_register.deletes' => 0, 'user_register.roll_id' => 0, 'user_register.status' => 0])
      ->get();

    if ($data) {


      $detailsArray = [];
      foreach ($data as $datas) {
        $image = DB::table('user_images')->select('img_url', 'type')->where('user_id', $datas->id)->where('deletes', '0')->where('status', '0')->get();
        $base_url = ($request->header('Origin') . '/');
        // dd($image[0]->img_url);
        if (count($image) > 0) {

          $image_front = $base_url . '' . $image[0]->img_url;
          $image_back = $base_url . '' . $image[1]->img_url;
        } else {
          $image_front = '';
          $image_back = '';
        }


        $details = [
          'id' => $datas->id ?: '',
          'name' => $datas->name ?: '',
          'dialCode' => $datas->dialCode ?: '',
          'mobile' => $datas->mobile ?: '',
          'email' => $datas->email ?: '',
          'building_name' => $datas->building_name ?: '',
          'nationality' => $datas->nationality ?: '',
          'address' => $datas->address ?: '',
          'city' => $datas->city ?: '',

          'account_name' => $datas->account_name ?: '',
          'account_no' => $datas->account_no ?: '',
          'acctype' => $datas->acctype ?: '',
          'bank_name' => $datas->bank_name ?: '',
          'branch_name' => $datas->branch_name ?: '',
          'branch_code' => $datas->branch_code ?: '',
          'IBAN_code' => $datas->IBAN_code ?: '',
          'swift_code' => $datas->swift_code ?: '',
          'dob' => $datas->dob ?: '',
          'currency_code' => $datas->currency_code ?: '',
          'passport' => $datas->passport ?: '',
          'exchangeid' => $datas->exchangeid ?: '',
          //  'img_url' => $img
          'front_img' => $image_front,
          'back_img' => $image_front,
          't_earning' => $datas->t_earning,


        ];
        $detailsArray[] = $details;
      }


      $response = [

        'status' => 'success',
        'message' => 'Customer Dashboard!',
        'data' => [
          'myprofile' => $detailsArray,
          // 'img'=>$image,

        ]
      ];
      return response($response);
    } else {
      $response = [
        'status' => 'failed',
        'message' => 'No Data Available!',
        'error' => 'Failed!.',

      ];
      return response($response);
    }
  }





























  public function loginOTPverify(Request $request)
  {

    try {

      $tempID = decrypt($request->enc)['tempID'];

      $expiresAt = decrypt($request->enc)['expiry'];

      $tempID = Controller::BlockSQLInjection($tempID);
      if ($tempID == '' || $tempID == null || $tempID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Data!', 'error' => 'Please use a valid Data!'];
        goto returnFVI;
      }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }

      if (strlen($request->otp) != 6) {
        $response = ['status' => 'failed', 'message' => 'Please use 6 digit only!', 'error' => 'Please use 6 digit only!'];
        goto returnFVI;
      }



      if (!Carbon::now()->lt($expiresAt)) {
        $response = ['status' => 'success', 'message' => "Timeout. Kindly refresh and try again!", 'data' =>  "Timeout. Kindly refresh and try again!"];
        goto returnFVI;
      }



      $data = user_register::where(['id' => $tempID, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if ($data) {

        // Log
        // $log = Controller::error_log_new($request->ip(), 'mobile_loginotp_success',  $data->id, '', '', 'mobile login successful',  json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);


        $query = DB::table('user_register')
            ->where('id', $tempID)
            ->where('status', '0')
            ->where('deletes', '0');
        
        $dataToUpdate = [
            'otp'       => '',
            'lastlogin' => now(),
        ];
        
        if ($request->platform_type === 'ios' || $request->platform_type === 'android') {
            $dataToUpdate['fcm_token'] = $request->fcm_token;
        }else{
            
            $dataToUpdate['browser_fcm_token'] = $request->browser_fcm_token;
        }
        
        $OTPUpdate = $query->update($dataToUpdate);


        if (!$OTPUpdate) {
          $response = ['status' => 'failed', 'message' => 'Process Failed!', 'error' => 'Process Failed!'];
          goto returnFVI;
        }

        $token = $data->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;
        
        $utm_source = $request->utm_source??null;
        $utm_campaign = $request->utm_campaign??null;

        $log =   DB::table('login_logs')->insert([
          'method' => __FUNCTION__,
          'userid' => $data->id, // user ID here
          'createdon' => now(),
          'ip' => $request->ip(),
          'utm_campaign'=> $utm_campaign,
        'utm_source'=> $utm_source
          // 'deletes' will be automatically set to '0' as per the default value
        ]);



        $response = [

          'status' => 'success',
          'message' => 'login success',
          'data' => [
            'user_id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'mobile' => $data->mobile,
            'country' => $data->nationality,
            'state' => $data->address,
            'city' => $data->city,

          ],
          'token' => $token,
        ];

        goto returnFVI;
      } else {


        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'Invalid OTP!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




























  public function forgotOTPverify(Request $request)
  {



    try {

      $tempID = decrypt($request->enc)['tempID'];
      $expiresAt = decrypt($request->enc)['expiry'];

      $tempID = Controller::BlockSQLInjection($tempID);
      if ($tempID == '' || $tempID == null || $tempID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Data!', 'error' => 'Please use a valid Data!'];
        goto returnFVI;
      }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }
      if (strlen($request->otp) != 6) {
        $response = ['status' => 'failed', 'message' => 'Please use 6 digit only!', 'error' => 'Please use 6 digit only!'];
        goto returnFVI;
      }
      if (!Carbon::now()->lt($expiresAt)) {
        $response = ['status' => 'success', 'message' => "Timeout. Kindly refresh and try again!", 'data' =>  "Timeout. Kindly refresh and try again!"];
        goto returnFVI;
      }

      // $fireBaseCheck = DB::table('user_register')->where('id', $tempID)
      //   ->where('status', '0')
      //   // ->where('otp', $request->otp)
      //   ->where('deletes', '0')
      //   ->orderBy('id', 'DESC')
      //   ->first();


      // if (isset($fireBaseCheck->mobile) && $fireBaseCheck->mobile != '') {
      //   $mobileCheck = DB::table('user_register')
      //     ->where('mobile', $fireBaseCheck->mobile)
      //     ->where('status', '0')
      //     ->where('deletes', '0')
      //     ->get();

      //   if ($mobileCheck->count() < 1) {
      //     $response = ['status' => 'failed', 'message' => 'User not found.', 'error' => 'User not found.'];
      //     goto returnFVI;
      //   }
      // }



      // if (isset($fireBaseCheck) && $fireBaseCheck->id) {
      //   if (substr($fireBaseCheck->mobile, 0, 3) != "971") {

      //     $fbMessage = 'Your National Draw verification code is: ' . $request->otp;
      //     $sixHoursAgo = Carbon::now()->subHours(6);

      //     $results = DB::table('smslog')
      //       ->where('details', 'LIKE', $fbMessage)
      //       ->where('mobile', 'LIKE', $fireBaseCheck->mobile)
      //       ->where('datetime', '>=', $sixHoursAgo)
      //       ->orderBy('id', 'DESC')
      //       ->get();

      //     if ($results->count() < 1) {
      //       $smsLogins =     DB::table('smslog')->insert([
      //         'gateway' => 'firebase',
      //         'subject' => '',
      //         'details' => $fbMessage,
      //         'mobile' => $fireBaseCheck->mobile,
      //         'ip' => '',
      //         'datetime' => now(),
      //         'token_response' => '',
      //         'status' => '',
      //         'reference_id' => '',
      //         'site' => 'CUSTOMER',
      //         'REQ_Time' => now(),
      //         'RES_Time' => now(),
      //         'smsdetails' => '',
      //         'smssendstatus' => '1',
      //         'SentDate' => now(),
      //         'response' => '',
      //         'smsstatus' => 'Delivered',
      //         'ip' => $request->ip()
      //       ]);
      //       if ($smsLogins) {

      //         $OTPUpdate = DB::table('user_register')
      //           ->where('id', $tempID)
      //           ->where('status', '0')
      //           ->where('deletes', '0')
      //           ->update(['otp' => $request->otp]);

      //         if (!$OTPUpdate) {
      //           $response = ['status' => 'failed', 'message' => 'Process Failed!', 'error' => 'Process Failed!'];
      //           goto returnFVI;
      //         }
      //         // dd($fireBaseCheck->mobile);
      //       } else {
      //         $response = ['status' => 'failed', 'message' => 'Process Failed!', 'error' => 'Process Failed!'];
      //         goto returnFVI;
      //       }
      //     } else {
      //       $response = ['status' => 'failed', 'message' => 'OTP Failed!', 'error' => 'OTP Failed!'];
      //       goto returnFVI;
      //     }
      //   }
      // } else {
      //   $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => 'User Not Found!'];
      //   goto returnFVI;
      // }

      $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();

      $data = user_register::where(['id' => $tempID, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      if (isset($data->id) && $data->id != '') {
        $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid OTP', 'error' => 'OTP verify Failed!.'];
        goto returnFVI;
      }
      // }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




  public function forgotRequest(Request $request)
  {


    try {
      $response = [];


      $request->mobile = Controller::BlockSQLInjection($request->mobile);
      if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
        goto returnFVI;
      }



      $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));

      $checkRepeat = DB::table('smslog')
        ->where('mobile', $request->mobile)
        // ->where('details', 'LIKE', '%verification code%')
        ->where('smssendstatus', '=', '1')
        ->where('datetime', '>=', $oneHourAgo)
        ->get();

      $limitVal = (substr($request->mobile, 0, 3) == "971") ? 5 : 3;
      if (isset($checkRepeat) && $checkRepeat->count() >= $limitVal) {
        $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
        goto returnFVI;
      }


      $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();


      // dd($request->mobile);
      $data = DB::table('user_register')->where(['mobile' => $request->mobile, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      //   dd($data);
      if (isset($data->id) && $data->id != '') {




        $subject = "National Draw | Forgot password - " . date("d-m-Y g:i a");

        $randotp = Controller::generateOTP(6);

        // $randotp = '123456';
        // $messages = "Hello " . $data->name . ", " . $randotp . " is the One Time Password (OTP) to Forgot Password the Go Ride Account.";
        $messages = "Hello " . $data->name . ", use " . $randotp . " as your GoRide forgot password OTP. Do not share it with anyone.";
        

        $whatsAppArr = [
          'mobile' => $request->mobile,
          'templateName' => 'national_draw_verification',
          'language' => 'en',
          'templateBodyParam' => [
            strval($randotp)
          ],
          'messages' => $messages,
          'resend' => ($request->isResend === "true" ? true : false)
        ];
        
        $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
                                
        if($check_mess && $check_mess->mess_type == 'sms'){
            
            // $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
            $sentsms = Controller::smsNotification($whatsAppArr, 'forgot');
            
        }elseif($check_mess && $check_mess->mess_type == 'whatsapp'){
            
            // $sentsms = Controller::sendNotification($whatsAppArr);
            $sentsms = Controller::sendNotification($whatsAppArr);
        }


        // if (substr($data->mobile, 0, 3) != "971") {

        // if (!$sentsms) {
        //   $whatsAppArr['resend'] = true;
        //   $sentsms = Controller::smsNotification($whatsAppArr, 'forgot');
        // }

        //   sendWhatsApp:
        //   $sentsms = Controller::sendWhatsApp($whatsAppArr);
        //   if (!$sentsms) {
        //     goto sendSMS;
        //   } else {
        //     goto skipSMS;
        //   }
        // } else {
        //   goto sendSMS;
        // }

        // sendSMS:
        // // $sentsms = Controller::sendWhatsApp($data->mobile, $messages);
        // // Temporarily SMS services have been stopped NON UAE 18-02-2024
        // if (substr($data->mobile, 0, 3) == "971") {
        //   $sentsms = Controller::sendsms($data->mobile, $messages, '');
        // }
        // skipSMS:


        if ($sentsms) {
          $otp_up = DB::table('user_register')->where(['id' => $data->id, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp, 'updated_at' => now()]);

          if ($otp_up) {
            $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'OTP failed', 'error' => 'OTP failed'];
          return response($response);
        }

        // }
        // } else {

        //   $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $data->id, 'expiry' => $timeAfterTenMinutes])]];
        //   goto returnFVI;
        // }
      } else {

        $response = ['status' => 'failed', 'message' => 'User Not Found', 'error' => 'User Not Found!'];
        return response($response);
      }
      // }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  function validatePassword($password)
  {
    try {
      // Minimum length of 6 characters
      if (strlen($password) < 6) {

        return ['status' => false, "message" => 'Minimum length of 6 characters'];
      }

      // At least 1 uppercase letter
      if (!preg_match('/[A-Z]/', $password)) {


        return ['status' => false, "message" => 'At least 1 uppercase letter'];
      }

      // At least 1 digit
      if (!preg_match('/[0-9]/', $password)) {


        return ['status' => false, "message" => 'At least 1 digit'];
      }

      // At least 1 special character (non-alphanumeric)
      if (!preg_match('/[!@#$%^&*()\-_=+{}[\]|;:\'",<.>\/?]/', $password)) {


        return ['status' => false, "message" => 'At least 1 special character (non-alphanumeric)'];
      }



      return ['status' => true, "message" => 'success'];
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function forgotPasswordUpdate(Request $request)
  {


    try {


      // dd('erfwetrw');
      // $validator =  Validator::make($request->all(), [
      //   'password' => 'required|min:6',
      //   'confirm_password' => 'required',
      // ]);

      // if ($validator->fails()) {
      //   return response()->json([
      //     "error" => 'validation_error',
      //     "message" => $validator->errors(),
      //   ], 422);
      // }

      $tempID = decrypt($request->enc)['tempID'];

      $expiresAt = decrypt($request->enc)['expiry'];


      $tempID = Controller::BlockSQLInjection($tempID);
      if ($tempID == '' || $tempID == null || $tempID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Data!', 'error' => 'Please use a valid Data!'];
        goto returnFVI;
      }

      // $request->type = Controller::BlockSQLInjection($request->type);
      // if ($request->type == '' || $request->type == null || $request->type == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
      //   goto returnFVI;
      // }

      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }

      if (strlen($request->otp) != 6) {
        $response = ['status' => 'failed', 'message' => 'Please use 6 digit only!', 'error' => 'Please use 6 digit only!'];
        goto returnFVI;
      }

      // $request->password = Controller::BlockSQLInjection($request->password);
      // if ($request->password == '' || $request->password == null || $request->password == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
      //   goto returnFVI;
      // }

      // $request->confirm_password = Controller::BlockSQLInjection($request->confirm_password);
      // if ($request->confirm_password == '' || $request->confirm_password == null || $request->confirm_password == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid confirm password!', 'error' => 'Please use a valid confirm password!'];
      //   goto returnFVI;
      // }

      if (!Carbon::now()->lt($expiresAt)) {
        $response = ['status' => 'success', 'message' => "Timeout. Kindly refresh and try again!", 'data' =>  "Timeout. Kindly refresh and try again!"];
        goto returnFVI;
      }


      if (!$this->validatePassword($request->password)['status']) {
        $response = ['status' => 'failed', 'message' => $this->validatePassword($request->password)['message'], 'error' => $this->validatePassword($request->password)['message']];
        goto returnFVI;
      }

      $password = $request->password;
      $confirm_password = $request->confirm_password;

      $datas = DB::table('user_register')->where(['id' => $tempID, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
      // dd($datas);
      if ($password == $confirm_password) {
        if ($datas) {

          $pass = md5($request->password);
          $update = DB::table('user_register')->where('id', $tempID)->update(['password' => $request->password, 'pass' => $pass, 'otp' => ""]);

          $response = [
            'status' => 'success',
            'message' => 'Password Updated successfully!',
            'data' => ["user_id" => $datas->id],
          ];
          goto returnFVI;
        } else {
          $response = [
            'status' => 'failed',
            'message' => 'Password updated Failed!',
            'error' => 'Password updated Failed!.',
          ];
          goto returnFVI;
        }
      } else {
        $response = [

          'status' => 'failed',
          'message' => 'Password  Not Matched',
          'error' => 'Password  Not Matched!.',

        ];
        goto returnFVI;
      }





      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}