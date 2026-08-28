<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Template\mailController;
use DB;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\user_register;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class NewauthController2 extends Controller
{
     public function getData($url, $dataArray)
    {
        $response = Http::get($url, $dataArray);
       

        if ($response->failed()) {
            return 'Request Error: ' . $response->body();
        } else {
            return $response->json();
        }
    }

  public function userRegisters(Request $request)
  {
    //   dd('sfds');
     $firstname=$request->firstname;
     $lastname=$request->lastname;
     $dob=$request->dob;
     $nationality=$request->nationality;
     $country_of_residence=$request->country_of_residence;
     $dialcode=$request->dialcode;
     $mobile=$request->mobile;
     $password=$request->password;
     $email=$request->email;
    
     $validator =  Validator::make($request->all(),[
        'firstname' => 'required',
        'lastname' => 'required',
        'dob' => 'required',
        'nationality' => 'required',
        'residence'=>'required',
        'dialcode' => 'required',
        'mobile' => 'required',
        'email'=>'nullable|email',
        'password' => 'required',
    ]);

    if($validator->fails()){
        return response()->json([
        "error" => 'validation_error',
        "message" => $validator->errors(),
        ], 422);
    }
    
    $min_length = 8; // Minimum password length
    $requires_lowercase = true; // Requires at least one lowercase letter
    $requires_uppercase = true; // Requires at least one uppercase letter
    $requires_digit = true; // Requires at least one digit
    $requires_special = true; // Requires at least one special character (e.g., !@#$%^&*)

    // Check password length
    if (strlen($password) < $min_length) {
       $response = ['status' => 'failed', 'message' => 'Minimum password length', 'error' => 'Minimum password length'];
         return response($response);  
    }

    // Check for lowercase letters
    if ($requires_lowercase && !preg_match('/[a-z]/', $password)) {
         $response = ['status' => 'failed', 'message' => 'Atleast one lower case', 'error' => 'Atleast one lower case'];
         return response($response);  
    }

    // Check for uppercase letters
    if ($requires_uppercase && !preg_match('/[A-Z]/', $password)) {
       $response = ['status' => 'failed', 'message' => 'Requires at least one uppercase letter', 'error' => 'Requires at least one uppercase letter'];
         return response($response);  
    }

    // Check for digits
    if ($requires_digit && !preg_match('/[0-9]/', $password)) {
         $response = ['status' => 'failed', 'message' => ' Requires at least one digit', 'error' => 'Requires at least one digit'];
         return response($response);  
    }

    // Check for special characters
    if ($requires_special && !preg_match('/[!@#\$%^&*()\-_=+{}\[\]:;<>,.?~]/', $password)) {
        $response = ['status' => 'failed', 'message' => 'Requires at least one special character', 'error' => 'Requires at least one special character'];
         return response($response);  
    }
   
   
    $dob=$request->dob;
    $toDate = Carbon::parse($dob);
    $fromDate = \Carbon\Carbon::now()->format('Y-m-d');
    $days = $toDate->diffInDays($fromDate);
    $months = $toDate->diffInMonths($fromDate);
    $years = $toDate->diffInYears($fromDate);
                
    if($years > 18){
                     
    }else{
    $response =['status' => 'failed', 'message' => 'You are not eligible for registration', 'error' => 'you are not eligible for registration'];
                return response($response);  
    }
    
    $emailCheck = DB::table('user_register')
            ->where('email', $request->email)
             ->where('status', '0')
             ->where('deletes', '0')
             ->get();
        
    if ($emailCheck->count() > 0) {
            $response =['status' => 'failed', 'message' => 'Email has already been registered', 'error' => 'Email  has already been registered'];
            return response($response);  
    }
        
    $mobileCheck = DB::table('user_register')
                ->where('mobile', $dialcode.''.$request->mobile)
                ->where('status', '0')
                ->where('deletes', '0')
                ->get();
        
    if ($mobileCheck->count() > 0) {
            $response =['status' => 'failed', 'message' => 'Mobile you entered already has a account', 'error' => 'Mobile you entered already has a account'];
            return response($response); 
    }
                
    if($request->nationality !=""){
        $country=DB::table('countries')->where('id',$request->nationality)->first();
    }else{
        $country="";
    }
                
    if($request->residence !=""){
        $residence=DB::table('countries')->where('id',$request->residence)->first();
    }else{
       $residence=""; 
    }
    
        $name_merge=$firstname.' '.$lastname;
        $timestamp = strtotime($dob);
        $formatted_date = date("Y-m-d", $timestamp);
                
        $urlGoogleCaptcha = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaSecretKey = '6LcuYlUoAAAAAALEQZ1sSG9F4hfHbn7TjG0bDkra';
                
        $verficationResponse = $request->captcha_token;
            $dataArray = [
            'secret' => $recaptchaSecretKey,
            'response' => $verficationResponse,
        ];

        $recaptchaResponse = $this->getData($urlGoogleCaptcha, $dataArray);
        $randotp = Controller::generateOTP(4);
            
               
        // if (is_array($recaptchaResponse)) {
            // if ($recaptchaResponse['success'] == true) {
                            
                    $otp = rand(100000,999999);
                            
                            
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'https://api.green-api.com/waInstance7103862736/sendMessage/73f187e6b1794558b0c00dcb3f3e7cc9884ef9e5b06c4346ac',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS =>'{
                         "chatId" : "'.$dialcode.$mobile.'@c.us",
                        "message": "'.'Your Signup OTP'.$otp.'"
                    }',
                      CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                      ),
                    ));
                    
                    $response = curl_exec($curl);
                    
                    curl_close($curl);
                    // echo $response;
                   
                // dd($otp);
                            
                            
                            
                $insert_user=DB::table('users_temp')->insert(['building_name'=>'','name'=>$name_merge,'dob'=>$formatted_date,'nationality'=>$country->name,'residinglocation'=>$residence->name,'dialCode'=>$dialcode,'email'=>$email,
                'mobile'=>$dialcode.$mobile,'password'=>$password, 'roll_id' => '0','created_at' => date('Y-m-d H:i:s'), 'otp' => $otp,'city'=>'','address'=>'','ip'=>$request->ip(),
                'deviceType' => '']);
                        
                $insert_id=DB::table('users_temp')->orderBy('id','DESC')->first();
     
                if ($insert_user) {
                    
                    
                    $response = ['status' => 'success', 'message' => 'OTP Send Successfully', 'data' => ['user_id'=>$insert_id]];
                    return response($response);  
                }else{
                    $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => $tempINS];
                    return response($response);  
                }
                // }else {
                //     $response = [
                //         'status' => 'failed',
                //         'message' => 'Google recaptcha error',
                //         'data' => 'Google recaptcha error',
                //       ];
                //       return response()->json($response);
                //     }
                //   }
                
            
         
     
    
   
    
    
    
  }
  public function login_new(Request $request){
    //   dd('sdsaa');
    $validator =  Validator::make($request->all(), [
        'dialcode' => 'required',
        'mobile' => 'required',
        'password' => 'required',
      ]);

      if ($validator->fails()) {
        return response()->json([
          "error" => 'validation_error',
          "message" => $validator->errors(),
        ], 422);
      }
      $dialcode_mobile=$request->dialcode.''.$request->mobile;
      $user = user_register::where(['mobile' => $dialcode_mobile, 'pass' => md5($request->password), 'roll_id' => '0', 'status' => '0', 'deletes' => '0'])->first();

    //   dd($user);
    if ($user) {
        //create token
        $token = $user->createToken('LDaccessToken', ['expires_at' => Carbon::now()->addHours(24)])->plainTextToken;

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

  }

  
}
