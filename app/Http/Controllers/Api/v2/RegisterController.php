<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Template\mailController;
// use App\Http\Controllers\RulesController;
// use Illuminate\Contracts\Validation\Rules;
use App\Rules\Rules;
use App\Rules\CustomRule;
use DB;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\user_register;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{

  public function userRegister($user_id, $otp, $ip)
  {

    try {
      $response = [];
      $getTempUser = DB::table('users_temp')->where([
        ['deletes', '=', '0'],
        ['id', '=', $user_id],
      ])->first();

      if ($getTempUser->id != '') {

        $getTempUser = json_decode(json_encode($getTempUser), true);



        $clientIP = request()->ip();
        $date = date("Y-m-d H:i:s");



        //   dd();

        $clientIP = request()->ip();
        $date = date("Y-m-d H:i:s");

        $user_register_insert = DB::table('user_register')->insert([
          'user' => 'Customer',
          'pass' => $getTempUser['pass'],
          'password' => $getTempUser['password'],
          'roll_id' => $getTempUser['roll_id'],
          'created_by' => 0,
          'referred_by' => $getTempUser['referred_by'],
          'ref_his_id' => $getTempUser['ref_his_id'],
          'dialCode' => $getTempUser['dialCode'],
          'mobile' => $getTempUser['mobile'],
          'name' => $getTempUser['name'],
          'email' => $getTempUser['email'],
          'deletes' => 0,
          'created_at' => $date,

          'building_name' => $getTempUser['building_name'],
          'city' => $getTempUser['city'],
          'address' => $getTempUser['address'],
          'nationality' => $getTempUser['nationality'],
          'ip' => $clientIP,
          'email_verify' => $getTempUser['email_verify'],
          'mobile_verify' => $getTempUser['mobile_verify'],
          'passport' => '',
          'img_url' => '',
          'otp' => '',
          'branch_code' => '',
          'branch_name' => '',
          'IBAN_code' => '',
          'currency_code' => '',
          'my_referral_code' => '',
          'residinglocation' => '',
          'delete_req' => '',
          'ticket_purchased' => '',
          'ticket_count' => '',
          'deviceType' => '',
          'exchangeid' => '',


        ]);


        // dd($user_register_insert); 




        if ($user_register_insert) {

          $last_ins_ID = DB::getPdo()->lastInsertId();

          $user_register_user_check = DB::table('user_register')->where(['id' => $last_ins_ID, 'status' => '0', 'deletes' => '0'])->orderBy('id', 'DESC')->first();


          if ($user_register_user_check) {


            $user_id_s = $user_register_user_check->id;


            // $mobile1=DB::table('user_register')->select('mobile')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
            $mobile = $user_register_user_check->mobile;

            // $name1=DB::table('user_register')->select('name')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
            $name = $user_register_user_check->name;

            // $email1=DB::table('user_register')->select('EMAIL')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
            $email = $user_register_user_check->email;
            //  dd($mobile);

            if (substr($mobile, 0, 3) == "971") {
              // dd('sfdsgf');
              $messages = "Congratulation!!! You have successfully created National Draw account.";
              $templateid = "";
              $randotp = Controller::generateOTP(4);


              // sendsms($this->con, $mobile, $messages, $templateid);
              $sentsms = Controller::sendsms($mobile, $messages, '');
            }




            $subject = 'Congratulation!!! You have successfully created National Draw account';

            $messages = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                                                            <html xmlns="http://www.w3.org/1999/xhtml">

                                                            <head>

                                                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

                                                            <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                                                            <meta name="viewport" content="width=device-width, initial-scale=1.0">

                                                            <title>Ticket Purchase OTP Registered Template</title>

                                                            <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=6uwSMFAkZPgrNvkHDa5A-2G2mC7d8O0zslNZ97rd3ooPL2OKZv2GxCk1VHcTHqeq7-bFOP--dprL0GEc99h-FZL_gJhGqMo1pe1DBAT3R9NjPNbBHiVJIC7CeHsdymQ0" charset="UTF-8"></script><style type="text/css">



@importurl("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

                                                              body {

                                                                margin: 0;

                                                              }

                                                              .wrapper {



                                                                background:#CCC;



                                                                }

                                                              .main {



                                                                background:#FFF;

                                                                max-width:600px;



                                                                }



                                                              table {

                                                                border-spacing: 0;

                                                              }

                                                              td {

                                                                padding: 3px;

                                                              }

                                                              img {

                                                                border: 0;

                                                              }

                                                              .column-one {



                                                                text-align:center;

                                                                margin:0 auto;

                                                                }

                                                              .column-one .column {



                                                                width:100%;

                                                                  margin:0 auto;



                                                                }







                                                            </style>

                                                            </head>

                                                            <body>



                                                              <center class="wrapper">



                                                                        <table class="main" width="100%">

                                                                            <!-- BORDER -->

                                                                            <tr><td class="column-one" style="background: #29377d; height:50px;">





                                                                            </td></tr>
                                                                            <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;">


                                                                            </td></tr>









                                                                  <tr><td class="column-one" >

                                                                  <table class="column"> <tr><td valign="top"  style="padding: 16px 0 12px 0;">

                                                                  <center>

                                                                    <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >



                                                                  </center>



                                                                    </td></tr></table>



                                                                      </td></tr>



                                                                              <tr>


                                                                                  <td class="column-one" >

                                                                                    <table align="center" class="column" style="
                                                                              background: url(' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/new_mantony.png)no-repeat;
                                                                              height:429px;background-position: center;    margin: 0px 0 0 0 !important;"> <tbody><tr><td colspan="3" valign="top" style="padding:10px 0px 0px 10px;">


                                                                          <h3 class="demoname" style="color: #be1e2d;  font-family: Arial Narrow;font-style: italic;font-size: 32px; margin: 0px 0px 0px 24px; text-align: center;">Hi, ' . $name . '


                                                                                              </h3>


                                                                                          </td></tr><tr>
                                                                                            <td>


                                                                                          </td>


                                                                          </tr>


                                                                                    </tbody></table>


                                                                                  </td>




                                                                    </tr>



                                                              <tr>

                                                                                <td class="column-one" >

                                                                      <table align="center" class="column"> <tr>

                                                                        <td valign="top" >

                                                                          <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                                                                    <tbody>



                                                                              <tr>

                                                                        <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                                                                          <strong><p class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 26px; margin: 10px 0px 0px 0px; text-align: center;">Are you excited to <span style="color:#be1e2d;">Participate?</span>

                                                                                  </p></strong>

                                                                          <p style="color: #29377d;  font-size:152%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;margin: 22px 0px 13px 0px;font-weight: 600;">You are one step away<br>from changing your Life</p>

                                                                        </td>

                                                                              </tr>

                                                                              <tr>
                                                                          <td style=" border-radius: 4px 4px 0px 0px; color: #111111; font-size: 24px; line-height: 24px;" align="center" valign="top" bgcolor="#ffffff">
                                                                            <h3 style="color: #ffffff; font-size: 22px; margin: 0px 0px 9px 0px; font-style: italic; font-family: Arial Narrow; padding: 10px 8px 8px 8px; background: #be1e2d; width: 230px; line-height: 1; border-radius: 10px;"><a style=" color:#fff;" href="' . ($request->header('Origin') . '/') . 'play">PARTICIPATE NOW !</a></h3>
                                                                          </td>
                                                                        </tr>

                                                                    </tbody>

                                                                  </table>

                                                                  <br>



                                                                  <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                                                                    <tbody>

                                                                      <tr>

                                                                        <td class="gmail-line" style="box-sizing: border-box; width: 8px;">

                                                                          <img  style="width:489px !important;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/center_img2.png">

                                                                        </td>

                                                                      </tr>

                                                                    </tbody>

                                                                  </table>
                                                                  <br>



                                                                  
                                                                   <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>

                                                                   For Clarification



                                                                          <br>

                                                                   Call 04 33 98880 Whatsapp +971 56 199 1271

                                                                   <br>

                                                                   or email support@nationaldrawuae.com</p>
                                                                        </td></tr></table>



                                                                      </td></tr>


                                                                  </table>
                                                                </center>



                                                              </body>

                                                              </html>';










            $sendEmail = Controller::composeEmail($ip, $email, $subject, $messages);



            return ['status' => 'success', 'message' => 'Verified successfully!', 'data' => "Verified successfully, login to continue"];
          }
        } else {
          return ['status' => 'failed', 'message' => 'User Creation has been failed!', 'error' => 'Insert query has been Failed'];
        }
      } else {
        return ['status' => 'failed', 'message' => 'User account not found!', 'error' => 'The user account not found in the temp.'];
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  // Register Email OTP Send
  public function register(Request $request)
  {

    try {
      // $numberController = new RulesController();
      $response = [];
      $input = $request->all();

      $validator = Validator::make($input, [
        'first_name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/', new CustomRule],

        'mobile' => ['required', 'regex:/^\+?[0-9]+$/'],
        'dialCode' => ['required', 'integer'],
        'email' => ['required', 'email', 'max:70'],
        // 'password' => ['required', Password::min(6)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
        // 'c_password' => ['required', Password::min(6)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'same:password'],
        'password' => ['required', 'min:6'],
        'c_password' => ['required', 'min:6', 'same:password'],
        'deviceType' => ['required', 'in:MOBILE,APP,DESKTOP,BROWSER,TABLET', 'max:10'],
        'building_name' => ['max:50'],
        'country' => ['required', 'integer'],
        'state' => ['required', 'integer'],
        'city' => ['required', 'integer'],
      ]);

      if (!$validator->fails()) {



        $request->email = Controller::BlockSQLInjection($request->email);
        if ($request->email == '' || $request->email == null || $request->email == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
          goto returnFVI;
        }

        $request->first_name = Controller::BlockSQLInjection($request->first_name);
        if ($request->first_name == '' || $request->first_name == null || $request->first_name == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid name!', 'error' => 'Please use a valid name!'];
          goto returnFVI;
        }

        $request->mobile = Controller::BlockSQLInjection($request->mobile);
        if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
          goto returnFVI;
        }

        $request->dialCode = Controller::BlockSQLInjection($request->dialCode);
        if ($request->dialCode == '' || $request->dialCode == null || $request->dialCode == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid dial code!', 'error' => 'Please use a valid dial code!'];
          goto returnFVI;
        }

        // $request->password = Controller::BlockSQLInjection($request->password);
        // if ($request->password == '' || $request->password == null || $request->password == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
        //   goto returnFVI;
        // }

        // $request->c_password = Controller::BlockSQLInjection($request->c_password);
        // if ($request->c_password == '' || $request->c_password == null || $request->c_password == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please use a valid confirm password!', 'error' => 'Please use a valid confirm password!'];
        //   goto returnFVI;
        // }

        $request->deviceType = Controller::BlockSQLInjection($request->deviceType);
        if ($request->deviceType == '' || $request->deviceType == null || $request->deviceType == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid device type!', 'error' => 'Please use a valid device type!'];
          goto returnFVI;
        }

        $request->country = Controller::BlockSQLInjection($request->country);
        if ($request->country == '' || $request->country == null || $request->country == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please select valid country!', 'error' => 'Please select valid country!'];
          goto returnFVI;
        }

        $request->state = Controller::BlockSQLInjection($request->state);
        if ($request->state == '' || $request->state == null || $request->state == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please select valid state!', 'error' => 'Please select valid state!'];
          goto returnFVI;
        }

        $request->city = Controller::BlockSQLInjection($request->city);
        if ($request->city == '' || $request->city == null || $request->city == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please select valid city!', 'error' => 'Please select valid city!'];
          goto returnFVI;
        }


        // dd($request->email);


        $pass = md5($request->password);


        /// Pattern Valiation ///





        $emailCheck = DB::table('user_register')
          ->where('email', $request->email)
          ->where('status', '0')
          ->where('deletes', '0')
          ->get();

        if ($emailCheck->count() > 0) {
          $response = ['status' => 'failed', 'message' => 'Entered "Email" has already been registered', 'error' => 'Entered "Email" has already been registered'];
          goto returnFVI;
        }

        // $d_mob=$dialcode.''.$request->mobile;
        // dd($d_mob);
        $mobileCheck = DB::table('user_register')
          ->where('mobile', $request->mobile)
          ->where('status', '0')
          ->where('deletes', '0')
          ->get();

        if ($mobileCheck->count() > 0) {
          $response = ['status' => 'failed', 'message' => 'Mobile you entered already has a account.', 'error' => 'Mobile you entered already has a account.'];
          goto returnFVI;
        }


        $countryName = trim(DB::table('countries')->where('flag', 1)
          ->where('id', $request->country)
          ->where('name', '!=', '')
          ->orderByDesc('id')->limit(1)
          ->value('name'), "'");

        // dd($countryName);
        $stateName = trim(DB::table('states')->where('flag', 1)
          ->where('id', $request->state)
          ->where('name', '!=', '')
          ->orderByDesc('id')->limit(1)
          ->value('name'), "'");

        $cityName = trim(DB::table('cities')->where('flag', 1)
          ->where('id', $request->city)
          ->where('name', '!=', '')
          ->orderByDesc('id')->limit(1)
          ->value('name'), "'");

        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));

        $checkRepeat = DB::table('users_temp')
          ->where('email', '=', $request->email)
          ->where('status', '=', '0')
          ->where('deletes', '=', '1')
          ->where('created_at', '>=', $oneHourAgo)
          ->get();

        if ($checkRepeat->count() > 4) {
          $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
          goto returnFVI;
        }

        $randotp = Controller::generateOTP(4);

        $arr = [
          'building_name' => ($request->building_name != '' && $request->building_name != 'null') ? $request->building_name : '',
          'city' => $cityName,
          'name' => $request->first_name,
          'email' => $request->email,
          'mobile' => $request->mobile,
          'address' => $stateName,
          'nationality' => $countryName,
          'pass' => $pass,
          'deletes' => '1',
          'dialCode' => $request->dialCode,
          'otp' => $randotp,
          'ip' => $request->ip(),
          'deviceType' => $request->deviceType,
          'roll_id' => '0',
          'created_at' => date('Y-m-d H:i:s'),
          'password' => $request->password,
        ];

        $tempINS = DB::table('users_temp')->insert($arr);
        $insertedId = DB::getPdo()->lastInsertId();
        if ($tempINS) {

          $subject = "National Draw | OTP to Verify Email - " . date("d-m-Y g:i a");
          $requestArr = [
            'name' => $request->first_name,
            'randotp' => $randotp,
          ];

          $message = mailController::signUPotp($requestArr);

          $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);

          if ($sendEmail) {
            $response = ['status' => 'success', 'message' => 'Email OTP Send Successfully!', 'data' => ['tempID' => (int) $insertedId]];
            goto returnFVI;
          } else {
            $response = ['status' => 'failed', 'message' => 'Email OTP Failed!', 'error' => $sendEmail];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => $tempINS];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  // Get Country, City, State, collection API
  public function getWorld()
  {
    try {
      $response = [];
      $countries = DB::table('countries')->select([
        'countries.id as id',
        'countries.name as name',
        DB::raw('COUNT(states.id) as statecount'),
      ])
        ->join('states', 'countries.id', '=', 'states.country_id')
        ->where('countries.flag', 1)
        ->groupBy('countries.id')
        ->havingRaw('statecount > 0')
        ->orderBy('name', 'ASC')
        ->get();

      // $states = DB::table('states')->select([
      //   'states.id as id',
      //   'states.name as name',
      //   'states.country_id as countryID',
      //   DB::raw('COUNT(cities.id) as citycount'),
      // ])
      //   ->join('cities', 'states.id', '=', 'cities.state_id')
      //   ->where('states.flag', 1)
      //   ->groupBy('states.id')
      //   ->havingRaw('citycount > 0')
      //   ->orderBy('name', 'ASC')
      //   ->get();

      // $cities = DB::table('cities')->select([
      //   'id',
      //   'name',
      //   'country_id as countryID',
      //   'country_id as countryID',
      //   'state_id as stateID',
      // ])
      //   ->where('flag', 1)
      //   ->orderBy('name', 'ASC')
      //   ->get();

      $response = ['status' => 'success', 'message' => 'Country has been get successfully.', 'data' => ['countries' => $countries, 'states' => $states]];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  // Get Country, City, State, collection API
  public function getCity(Request $request)
  {
    try {
      $response = [];
      if ($request->state_id == '' || $request->state_id == null || $request->state_id == '') {
        $response = ['status' => 'failed', 'message' => 'Kindly send state id!', 'error' => 'Kindly send state id!'];
        goto returnFVI;
      }

      $request->state_id = Controller::BlockSQLInjection($request->state_id);
      if ($request->state_id == '' || $request->state_id == null || $request->state_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid state id!', 'error' => 'Please use a valid state id!'];
        goto returnFVI;
      }

      // $countries = DB::table('countries')->select([
      //   'countries.id as id',
      //   'countries.name as name',
      //   DB::raw('COUNT(states.id) as statecount'),
      // ])
      //   ->join('states', 'countries.id', '=', 'states.country_id')
      //   ->where('countries.flag', 1)
      //   ->groupBy('countries.id')
      //   ->havingRaw('statecount > 0')
      //   ->orderBy('name', 'ASC')
      //   ->get();

      // $states = DB::table('states')->select([
      //   'states.id as id',
      //   'states.name as name',
      //   'states.country_id as countryID',
      //   DB::raw('COUNT(cities.id) as citycount'),
      // ])
      //   ->join('cities', 'states.id', '=', 'cities.state_id')
      //   ->where('states.flag', 1)
      //   ->groupBy('states.id')
      //   ->havingRaw('citycount > 0')
      //   ->orderBy('name', 'ASC')
      //   ->get();

      $cities = DB::table('cities')->select([
        'id',
        'name',
        'country_id as countryID',
        'country_id as countryID',
        'state_id as stateID',
      ])
        ->where('state_id', $request->state_id)
        ->where('flag', 1)
        ->orderBy('name', 'ASC')
        ->get();

      if ($cities->count() > 0) {
        $response = ['status' => 'success', 'message' => 'City has been get successfully.', 'data' => ['cities' => $cities]];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'City list not found!', 'error' => 'City list not found!'];
        goto returnFVI;
      }




      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  //  The Mobile/Email OTP Verification API
  public function signupOTPVerify(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();

      $validator = Validator::make($input, [
        'tempID' => ['required'],
        'method' => ['required', 'in:EMAIL,MOBILE', 'max:10'],
        'OTP' => ['required', 'max:4'],
      ]);
      if (!$validator->fails()) {




        $request->tempID = Controller::BlockSQLInjection($request->tempID);
        if ($request->tempID == '' || $request->tempID == null || $request->tempID == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid tempID!', 'error' => 'Please use a valid tempID!'];
          goto returnFVI;
        }

        $request->OTP = Controller::BlockSQLInjection($request->OTP);
        if ($request->OTP == '' || $request->OTP == null || $request->OTP == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid OTP!', 'error' => 'Please use a valid OTP!'];
          goto returnFVI;
        }

        // $request->method = Controller::BlockSQLInjection($request->method);
        if (Controller::BlockSQLInjection($request->method) == '' || Controller::BlockSQLInjection($request->method) == null || Controller::BlockSQLInjection($request->method) == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid method!', 'error' => 'Please use a valid method!'];
          goto returnFVI;
        }



        if ($request->method == 'EMAIL') {
          $bresult = DB::table('users_temp')->where('id', $request->tempID)
            ->where('status', 0)
            ->where('otp', $request->OTP)
            ->where('deletes', 1)
            ->orderBy('id', 'DESC')
            ->first();



          if ($bresult) {

            $mobile = $bresult->mobile;


            $email_verify = DB::table('users_temp')->where('id', $request->tempID)->update(['email_verify' => 'YES']);

            if ($email_verify) {


              if (substr($mobile, 0, 3) == "971") {
                $randotp = Controller::generateOTP(4);


                $messages = "Your National Draw verification code is: " . $randotp;

                $sentsms = Controller::sendsms($mobile, $messages, '');



                if ($sentsms) {

                  $otp_up = DB::table('users_temp')->where('id', $request->tempID)->update(['otp' => $randotp]);

                  if ($otp_up) {
                    $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['tempID' => (int) $request->tempID]];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'Kindly Use Correct Mobile. SMS Failed!', 'error' => 'Kindly Use Correct Mobile.'];
                  goto returnFVI;
                }
              } else {

                $delete_update = DB::table('users_temp')->where('id', $request->tempID)->update(['deletes' => '0']);

                if ($delete_update) {

                  $response = RegisterController::userRegister($request->tempID, $request->OTP, $request->ip());

                  goto returnFVI;
                }
              }
            } else {
              $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Verification failed.'];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Verification failed.'];
            goto returnFVI;
          }
        }

        if ($request->method == 'MOBILE') {

          // $bresult = select_query($con, "users_temp", "", "`id`='$email' and `status`='0'  and `otp`='$otp' and `deletes`='1' ORDER BY `id` DESC LIMIT 1", "", "");

          $bresult = DB::table('users_temp')->where('id', $request->tempID)
            ->where('status', '0')
            ->where('otp', $request->OTP)
            ->where('deletes', '1')
            ->orderBy('id', 'DESC')
            ->first();

          if ($bresult) {

            $mobile = $bresult->mobile;
            $email_verify = DB::table('users_temp')->where('id', $request->tempID)->update(['mobile_verify' => 'YES']);
            // $email_verify = mysqli_query($con, "UPDATE `users_temp` SET `mobile_verify` = 'YES' WHERE `users_temp`.`id` = $user_id;");
            if ($email_verify) {

              // $delete_update = mysqli_query($con, "UPDATE `users_temp` SET `deletes` = '0' WHERE `users_temp`.`id` = $user_id;");
              // if ($delete_update) {
              //     $result = $auth->userRgister($user_id, $otp, ($request->header('Origin') . '/'));
              //     goto result12334;
              // }

              $delete_update = DB::table('users_temp')->where('id', $request->tempID)->update(['deletes' => '0']);
              if ($delete_update) {
                $response = RegisterController::userRegister($request->tempID, $request->OTP, $request->ip());
                goto returnFVI;
              }
            } else {
              $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Verification failed.'];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Verification failed.'];
            goto returnFVI;
          }
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  //  The Sign Up OTP resend
  public function singupOTPResend(Request $request)
  {
    try {

      $response = [];
      $input = $request->all();

      $validator = Validator::make($input, [
        'tempID' => ['required'],
        'method' => ['required', 'in:EMAIL,MOBILE', 'max:10']
      ]);
      if (!$validator->fails()) {


        $request->tempID = Controller::BlockSQLInjection($request->tempID);
        if ($request->tempID == '' || $request->tempID == null || $request->tempID == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid tempID!', 'error' => 'Please use a valid tempID!'];
          goto returnFVI;
        }

        $request->method = Controller::BlockSQLInjection($request->method);
        if ($request->method == '' || $request->method == null || $request->method == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid method!', 'error' => 'Please use a valid method!'];
          goto returnFVI;
        }




        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));


        // $checkRepeat1 = select_query($con, "users_temp", "", " `id`='$email_id' and `status`='0' and  `otp`!='' and `deletes`='1'  and `created_at` >= '$oneHourAgo'", "", "");

        $checkRepeat1 = DB::table('users_temp')->where('id', $request->tempID)
          ->where('status', '0')
          ->where('otp', '!=', '')
          ->where('deletes', '1')
          ->where('created_at', '>=', $oneHourAgo)
          ->get();

        $rcount = $checkRepeat1->count();


        if ($rcount > 4 || session('otparray') > 4) {
          $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
          goto returnFVI;
        } else {


          // Log
          // error_log_new($con, getUserIP(), 'sign_up_resend_otp_try', '', $_POST["email"], '', 'User try to resend OTP option', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);
          if ($request->method == 'EMAIL') {



            // $CheckOTP = select_query($con, "users_temp", "", "`id`='$email_id' and `status`='0' and `deletes`='1'  and `otp`!=''  ORDER BY `id` DESC", "", "");

            $CheckOTP =  DB::table('users_temp')->where('id', $request->tempID)
              ->where('status', '0')
              ->where('deletes', '1')
              ->where('otp', '!=', '')
              ->orderBy('id', 'desc')
              ->first();

            if ($CheckOTP) {

              $randotp = $CheckOTP->otp;
              $id = $CheckOTP->id;
              $mobile = $CheckOTP->mobile;
              $email = $CheckOTP->email;
              $name = $CheckOTP->name;



              // $randotp = rand(1000, 9999);
              $randotp = Controller::generateOTP(4);

              session(['otparray' => (intval(session('otparray')) + 1)]);

              $update =  DB::table('users_temp')->where('id', $id)->where('deletes', 1)->update(['otp' => $randotp]);
              if ($update) {
                $subject = "National Draw | OTP to Verify Email - " . date("d-m-Y g:i a");

                $messages1 =

                  //new template 26-12-2022 start       //
                  '
          <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

          <html xmlns="http://www.w3.org/1999/xhtml">

          <head>

          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

          <meta http-equiv="X-UA-Compatible" content="IE=edge" />

          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <title>Ticket Purchase OTP Registered Template</title>

          <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=cOHVXauret47m7vfvvQhf02-NcodCzBsAzoj0F1AHQgPkD9Rj1YHZaoHPpoqjmlFYOj6jJ3T7iZ4ouw5wIdI1iWh-rYN2IwIddwzX0pKgHcRyYHURyLdo5E9133N8cCX" charset="UTF-8"></script><style type="text/css">



@importurl("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

            body {

              margin: 0;

            }

            .wrapper {



              background:#CCC;



              }

            .main {



              background:#FFF;

              max-width:600px;



              }



            table {

              border-spacing: 0;

            }



            img {

              border: 0;

            }

            .column-one {



              text-align:center;

              margin:0 auto;

              }

            .column-one .column {



              width:100%;

                margin:0 auto;



              }







          </style>

          </head>

          <body>



            <center class="wrapper">



              <table class="main" width="100%">

                  <!-- BORDER -->

                  <tr><td class="column-one" style=" background: #29377d; height:54px;">





                  </td></tr>



                  <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:15px;">





                  </td></tr>



                  <!-- BORDER -->



                  <!-- LOGO  -->

                  <tr><td class="column-one" >

                  <table class="column"> <tr><td valign="top" style="padding: 16px 0 37px 0;">

                  <center>

                    <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png"  style="border: 0px;"  >



                  </center>



                    </td></tr></table>



                  </td></tr>

                  <!-- LOGO  -->

                          <tr>

                            <td class="column-one" >

                    <table align="center" class="column" style="
              background: url(' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/new_man.png)no-repeat;
              height: 300px;background-position: center;    margin: -26px 0 0 0 !important;
              "> <tbody><tr><td colspan="3" valign="top" style="padding:10px 0px 0px 10px;">


          <h3 class="demoname" style="color: #be1e2d;  font-family: Arial Narrow;font-style: italic;font-size: 32px; margin: 0px 0px 0px 24px; text-align: center;">Hi, ' . $name . '


                              </h3>


                          </td></tr><tr>
                            <td>


                           </td>


          </tr>


                    </tbody></table>


                  </td></tr>



          <tr>

                            <td class="column-one" >

                  <table align="center" class="column"> <tr>

                    <td valign="top" >

                      <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                <tbody>



                          <tr>

                    <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                      <p style="color: #29377d;  font-size:163%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Please use the below OTP to complete the<br>registration with National Draw</p>

                    </td>

                          </tr>

                          <tr>
                      <td style=" border-radius: 4px 4px 0px 0px; color: #111111; font-size: 24px; line-height: 24px;padding: 10px;" align="center" valign="top" bgcolor="#ffffff">
                        <h3 style="color: #ffffff; font-size: 36px; margin: 0px; font-style: italic; font-family: Arial Narrow;padding: 9px; background: #be1e2d; width: 119px; line-height: 1; border-radius: 11px; border: 3px dashed #ffffff;">' . $randotp . '</h3>
                      </td>
                    </tr>

                </tbody>

              </table>

              <br>

             <!--  -->

              <!-- <br style="color: #000000;  font-size: medium; background-color: #fbfbfb;"> -->

              <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                <tbody>

                  <tr>

                    <td class="gmail-line" style="box-sizing: border-box; width: 8px;">

                      <img  style="width:489px !important;"src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/center_img2.png">

                    </td>

                  </tr>

                </tbody>

              </table>
              <br>


              <p style="color: #29377d !important;  font-size: 22px !important; margin: 0px !important; text-align: center !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Need help?
          +971 433 98880<br>support@nationaldrawuae.com

              </p>

          <br>

             
                <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
                
                For Clarification
                
                 
                
                       <br>
                
                Call 04 33 98880 Whatsapp +971 56 199 1271
                
                <br>
                
                or email support@nationaldrawuae.com</p>
                    </td></tr></table>



                  </td></tr>


              </table> <!-- End Main Class -->



            </center> <!-- End Wrapper -->



          </body>

          </html>';



                // $sendemail = sendemail($con, $email, $subject, $messages1, 'otp');

                $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $messages1);
                if ($sendEmail) {
                  $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['tempID' => (int)$request->tempID]];
                  goto returnFVI;
                } else {
                  $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed'];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed'];
                goto returnFVI;
              }
            }
          }


          if ($request->method == 'MOBILE') {
            // $CheckOTP = select_query($con, "users_temp", "", "`id`='$email_id' and `status`='0' and `deletes`='1'  and `otp`!=''  ORDER BY `id` DESC", "", "");
            $CheckOTP =  DB::table('users_temp')->where('id', $request->tempID)
              ->where('status', '0')
              ->where('deletes', '1')
              ->where('otp', '!=', '')
              ->orderBy('id', 'desc')
              ->first();
            if ($CheckOTP) {

              $randotp =  $CheckOTP->otp;
              $id =  $CheckOTP->id;
              $mobile =  $CheckOTP->mobile;
              $email =  $CheckOTP->email;
              $name =  $CheckOTP->name;

              $randotp = Controller::generateOTP(4);
              session(['otparray' => (intval(session('otparray')) + 1)]);



              $update =  DB::table('users_temp')->where('id', $id)->where('deletes', 1)->update(['otp' => $randotp]);
              if ($update) {
                if (substr($mobile, 0, 3) == "971") {

                  $messages = "Your National Draw verification code is: " . $randotp;

                  $sentsms = Controller::sendsms($mobile, $messages, '');
                  if ($sentsms) {
                    $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['tempID' => (int)$request->tempID]];
                    goto returnFVI;
                  } else {
                    $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed'];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed'];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => 'Resend Failed', 'error' => 'Resend Failed'];
                goto returnFVI;
              }
            }
          }



          $response = ['status' => 'success', 'message' => 'OTP Sent Successfully!', 'data' => ['tempID' => (int)$request->tempID]];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
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
