<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Agent;
use App\Models\user_register;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;
use App\Http\Controllers\Template\mailController;

use Auth;
use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\File;
use \App\Mail\OtpMail;

class MyprofileController extends Controller
{

  public function myprofile(Request $request)
  {
    try {
      $user_id = auth()->user()->id;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $req_check = DB::table('withdraw_request')
        ->where('from_id', '=', $user_id)
        ->where('deletes', '=', '0')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get();

      $country = DB::select("SELECT `countries`.`id` AS `id`, `countries`.`name` AS `name`, COUNT(states.id) AS `statecount` FROM `countries` INNER JOIN `states` ON `countries`.`id` = `states`.`country_id` WHERE `countries`.`flag` = '1' GROUP BY countries.id HAVING `statecount` > 0 ORDER BY name ASC;");

      $FRONT_img = DB::table('user_images')
        ->select(
          "id",
          "user_id",
          "img_url",
          "type"
        )
        ->where('user_id', $user_id)
        ->where('img_url', '!=', '')
        ->where('type', 'FRONT')
        ->where('status', '0')
        ->where('deletes', '0')
        ->orderBy('id', 'desc')
        ->first();

      $BACK_img = DB::table('user_images')
        ->select(
          "id",
          "user_id",
          "img_url",
          "type"
        )
        ->where('user_id', $user_id)
        ->where('img_url', '!=', '')
        ->where('type', 'BACK')
        ->where('status', '0')
        ->where('deletes', '0')
        ->orderBy('id', 'desc')
        ->first();



      $data['users'] = auth()->user()->toArray();




      if (isset($FRONT_img->id)) {
        $data['FRONT_img'] = $FRONT_img;
        $data['FRONT_img']->img_url =  (strpos($FRONT_img->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $FRONT_img->img_url) : $FRONT_img->img_url;
      } else {
        $data['FRONT_img']['id'] = '';
        $data['FRONT_img']['user_id'] = '';
        $data['FRONT_img']['img_url'] = '';
        $data['FRONT_img']['type'] = '';
      }

      if (isset($BACK_img->id)) {
        $data['BACK_img'] = $BACK_img;
        $data['BACK_img']->img_url = (strpos($BACK_img->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $BACK_img->img_url) : $BACK_img->img_url;
      } else {
        $data['BACK_img']['id'] = '';
        $data['BACK_img']['user_id'] = '';
        $data['BACK_img']['img_url'] = '';
        $data['BACK_img']['type'] = '';
      }










      $data['country'] = $country;

      // $data['lastReqData'] =  $req_check;

      $response = ['status' => 'success', 'message' => "With draw page details collected", 'data' =>  $data];
      goto returnFVI;


      // $data = DB::table('user_register')->select(
      //   'user_register.id',
      //   'user_register.name',
      //   'user_register.dialCode',
      //   'user_register.mobile',
      //   'user_register.email',
      //   'user_register.building_name',
      //   'user_register.nationality',
      //   'user_register.address',
      //   'user_register.city',
      //   'user_register.account_name',
      //   'user_register.account_no',
      //   'user_register.acctype',
      //   'user_register.bank_name',
      //   'user_register.branch_name',
      //   'user_register.branch_code',
      //   'user_register.IBAN_code',
      //   'user_register.swift_code',
      //   'user_register.dob',
      //   'user_register.currency_code',
      //   'user_register.passport',
      //   'user_register.exchangeid',
      //   'user_register.t_earning'
      //   // ,'user_images.user_id as uid','user_images.img_url','user_images.type','user_images.deletes as del'
      // )
      //   // ->leftJoin('user_images','user_images.user_id','=','user_register.id')
      //   ->where(['user_register.id' => $auth, 'user_register.deletes' => 0, 'user_register.roll_id' => 0, 'user_register.status' => 0])
      //   ->get();
      // //  dd($data);


      // if ($data) {


      //   $detailsArray = [];
      //   foreach ($data as $datas) {
      //     $image = DB::table('user_images')->select('img_url', 'type', 'id')->where('user_id', $auth)->where('deletes', '0')->where('status', '0')->where('type', 'FRONT')->limit(1)->get();
      //     $image2 = DB::table('user_images')->select('img_url', 'type', 'id')->where('user_id', $auth)->where('deletes', '0')->where('status', '0')->where('type', 'BACK')->limit(1)->get();





      //     $base_url = ($request->header('Origin') . '/');

      //     $imageUrl1 = '';
      //     if (isset($image) && count($image) > 0) {
      //       $imageUrl1 = (strpos($image[0]->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $image[0]->img_url) : $image[0]->img_url;
      //     }

      //     $imageUrl2 = '';
      //     if (isset($image2) && count($image2) > 0) {
      //       $imageUrl2 = (strpos($image2[0]->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $image2[0]->img_url) : $image2[0]->img_url;
      //     }

      //     $details = [
      //       'id' => $datas->id ?: '',
      //       'name' => $datas->name ?: '',
      //       'dialCode' => $datas->dialCode ?: '',
      //       'mobile' => $datas->mobile ?: '',
      //       'email' => $datas->email ?: '',
      //       'building_name' => $datas->building_name ?: '',
      //       'nationality' => $datas->nationality ?: '',
      //       'address' => $datas->address ?: '',
      //       'city' => $datas->city ?: '',
      //       'account_name' => $datas->account_name ?: '',
      //       'account_no' => $datas->account_no ?: '',
      //       'acctype' => $datas->acctype ?: '',
      //       'bank_name' => $datas->bank_name ?: '',
      //       'branch_name' => $datas->branch_name ?: '',
      //       'branch_code' => $datas->branch_code ?: '',
      //       'IBAN_code' => $datas->IBAN_code ?: '',
      //       'swift_code' => $datas->swift_code ?: '',
      //       'dob' => $datas->dob ?: '',
      //       'currency_code' => $datas->currency_code ?: '',
      //       'passport' => $datas->passport ?: '',
      //       'exchangeid' => $datas->exchangeid ?: '',
      //       //  'img_url' => $img
      //       'front_img' => $imageUrl1,
      //       'back_img' => $imageUrl2,
      //       't_earning' => $datas->t_earning,
      //       'frontID' => isset($image[0]) ? $image[0]->id : '',
      //       'backID' => isset($image2[0]) ? $image2[0]->id : ''
      //     ];
      //     $detailsArray[] = $details;
      //   }


      //   $response = [

      //     'status' => 'success',
      //     'message' => 'Customer Dashboard!',
      //     'data' => [
      //       'myprofile' => $detailsArray,
      //       // 'img'=>$image,

      //     ]
      //   ];
      //   return response($response);
      // } else {
      //   $response = [
      //     'status' => 'failed',
      //     'message' => 'No Data Available!',
      //     'error' => 'Failed!.',

      //   ];


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }





    public function emailupdate_otp(Request $request)
  {
    $auth = auth()->user()->id;


    $request->email = Controller::BlockSQLInjection($request->email);
    if ($request->email == '' || $request->email == null || $request->email == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
      goto returnFVI;
    }


    if ($request->email != "") {

       $data = DB::table('user_register')
    ->where('email', $request->email)
    ->where('deletes', 0)
    ->where('roll_id', 0)
    ->where('status', 0)
    ->where('id', '!=', $auth)
    ->first();

      if ($data) {
        $response = ['status' => 'failed', 'message' => 'Email already registered', 'error' => 'email already registered'];
        return response($response);
      } else {


        $data = DB::table('user_register')->where(['id' => $auth, 'deletes' => 0, 'roll_id' => 0, 'status' => 0])->first();

        $randotp = Controller::generateOTP(6);
        $update =  DB::table('user_register')->where(['id' => $auth, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

        $requestArr = [
          'name' => $data->name,
          'randotp' => $randotp,
        ];
        $message = mailController::Email_update($requestArr);

        $subject = "National Draw |  OTP to  Email Update - " . date("d-m-Y g:i a");

        $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);
        // dd( $sendEmail);
        $response = ['status' => 'success', 'message' => 'OTP sent successfully', 'data' => ['user_id' => $data->id,],];
        return response($response);
      }
    } else {
      $response = ['status' => 'failed', 'message' => 'Please enter the email', 'error' => 'please enter the email'];
      return response($response);
    }


    returnFVI:
    return response()->json($response);
  }
  public function emailupdate_resendotp(Request $request)
  {
    $auth = auth()->user()->id;


    $request->user_id = Controller::BlockSQLInjection($request->user_id);
    if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
      goto returnFVI;
    }


    $request->email = Controller::BlockSQLInjection($request->email);
    if ($request->email == '' || $request->email == null || $request->email == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
      goto returnFVI;
    }


    if ($request->user_id != "") {

      if ($request->email != "") {

        if ($auth == $request->user_id) {



            $data = DB::table('user_register')
            ->where('email', $request->email)
            ->where('deletes', 0)
            ->where('roll_id', 0)
            ->where('status', 0)
            ->where('id', '!=', $auth)
            ->first();

          if ($data) {
            $response = ['status' => 'failed', 'message' => 'Email already registered', 'error' => 'email already registered'];
            return response($response);
          } else {


            $data = DB::table('user_register')->where(['id' => $auth, 'deletes' => 0, 'roll_id' => 0, 'status' => 0])->first();

            $randotp = Controller::generateOTP(6);
            $update =  DB::table('user_register')->where(['id' => $auth, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->update(['otp' => $randotp]);

            $requestArr = [
              'name' => $data->name,
              'randotp' => $randotp,
            ];
            $message = mailController::Email_update($requestArr);
            $subject = "National Draw | RESEND OTP to  Email Update - " . date("d-m-Y g:i a");

            $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);


            $response = ['status' => 'success', 'message' => 'OTP sent successfully!', 'data' => ['user_id' => $data->id,],];
            return response($response);
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Invalid user ', 'error' => 'Invalid user'];
          return response($response);
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Kindly enter the email ', 'error' => 'Invalid email'];
        return response($response);
      }
    } else {
      $response = ['status' => 'failed', 'message' => 'Kindly enter the Userid ', 'error' => 'Invalid userid'];
      return response($response);
    }

    returnFVI:
    return response()->json($response);
  }
  public function emailupdate(Request $request)
{
    $auth = auth()->user()->id;

    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $data = user_register::where(['id' => $auth, 'otp' => $request->otp, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

    if (!$data) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Incorrect otp',
            'error' => 'email updated failed'
        ]);
    }

    $update = DB::table('user_register')
        ->where(['id' => $auth, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])
        ->update(['email' => $request->email, 'otp' => '', 'email_verify' => 'YES']);

    if ($update) {
        return response()->json([
            'status' => 'success',
            'message' => 'Email updated successfully',
            'data' => [
                'user_id' => $data->id
            ]
        ]);
    } else {
        return response()->json([
            'status' => 'failed',
            'message' => 'Failed to update email',
            'error' => 'Failed to update email'
        ]);
    }
}



//   public function update_myprofile(Request $request)
//   {
//     $auth = auth()->user()->id;

//       dd($request);

//     //     if($request->building_name !=""){
//     //             if($request->country !=""){
//     //                 if($request->state !=""){
//     //                     if($request->city !=""){


//     if ($request->passport) {
//       $passport = $request->passport;
//     } else {
//       $passport = "";
//     }
//     if ($request->exchangeid) {
//       $exchangeid = $request->exchangeid;
//     } else {
//       $exchangeid = "";
//     }
//     // dd('$data');

//     $data = user_register::where(['id' => $auth, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();

//     if ($data) {

//       if ($request->country != "") {
//         $country = DB::table('countries')->select('id', 'name')->where('id', $request->country)->first();
//         $countries = $country->name;
//       } else {
//         $country = DB::table('user_register')->select('id', 'name as nationality')->where('id', $auth)->first();
//         $countries = $country->name;
//       }

//       if ($request->state != "") {
//         $state = DB::table('states')->select('id', 'name')->where('id', $request->state)->first();
//         $states = $state->name;
//       } else {
//         $state = DB::table('user_register')->select('id', 'address as name')->where('id', $auth)->first();
//         $states = $state->name;
//       }
//       // dd($state->name);
//       if ($request->city != "") {
//         $city = DB::table('cities')->select('id', 'name')->where('id', $request->city)->first();

//         $cities = $city->name;
//       } else {
//         $city = DB::table('user_register')->select('id', 'city as name')->where('id', $auth)->first();
//         $cities = $city->name;
//       }
//       // dd($cities);
//       $update =  DB::table('user_register')->where(['id' => $auth])
//         ->update([
//           'building_name' => $request->building_name,
//           'nationality' => $countries,
//           'address' => $states,
//           'city' => $cities,
//           'account_name' => $request->account_holder,
//           'account_no' => $request->account_no,
//           'acctype' => $request->acctype,
//           'bank_name' => $request->bank_name,
//           'branch_code' => $request->branch_code,
//           'branch_name' => $request->branch_name,
//           'IBAN_code' => $request->IBAN_code,
//           'currency_code' => $request->currency_code,
//           'swift_code' => $request->swift_code,
//           'dob' => $request->dob,
//           'passport' => $passport,
//           'exchangeid' => $exchangeid
//         ]);
//       $response = [

//         'status' => 'success',
//         'message' => 'Profile updated sucessfully',
//         'data' => [
//           'user_id' => $data->id,

//         ],
//       ];

//       return response($response);
//     } else {
//       $response = [

//         'status' => 'failed',
//         'message' => 'Profile updated failed!',
//         'error' => 'profile updated failed',
//       ];

//       return response($response);
//     }
//     //                 }else{
//     //      $response = [

//     //           'status' => 'failed',
//     //           'message' => 'Please enter the city name',
//     //           'error'=>'please enter the city name',
//     //         ];

//     //         return response($response);
//     //           }     
//     //                 }else{
//     //      $response = [

//     //           'status' => 'failed',
//     //           'message' => 'Please enter the state name',
//     //           'error'=>'please enter the state name',
//     //         ];

//     //         return response($response);
//     //           }          

//     //             }else{
//     //      $response = [

//     //           'status' => 'failed',
//     //           'message' => 'Please enter the country name',
//     //           'error'=>'please enter the country name',
//     //         ];

//     //         return response($response);
//     // }       
//     //   }else{
//     //      $response = [

//     //           'status' => 'failed',
//     //           'message' => 'Please enter the building name',
//     //           'error'=>'please enter the building name',
//     //         ];

//     //         return response($response);
//     // }


//   }




public function MyprofileUpdate(Request $request)
{
    
    $auth = auth()->user()->id;
    $user = user_register::find($auth);
    
    $validator = Validator::make($request->all(), [
      
        'gender' => 'required',
        'dob' => 'required|date',
        'nationality' => 'required|string',
        'residinglocation' => 'required|string',
       
          
    ]);

    if ($validator->fails()) {
        $response = [
            'status' => 'failed',
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ];

        return response()->json($response, 422);
    }
    
    $user->gender = $request->gender;
    $user->dob = $request->dob;
    $user->nationality = $request->nationality;
    $user->residinglocation = $request->residinglocation; 
   
    try {
        $user->save();
    } catch (\Exception $e) {
        return response()->json(['status' => 'failed', 'message' => 'Failed to update profile', 'error' => $e->getMessage()], 500);
    }

    $response = [
        'status' => 'success',
        'message' => 'Profile updated successfully'
    ];

    return response()->json($response);
}


public function changePassword(Request $request)
{
    $auth = auth()->user()->id;
    $validator = Validator::make($request->all(), [
        'new_password' => 'required|string|min:8',
        'confirm_password' => 'required|string|min:8|same:new_password',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    try {
        $user = user_register::where(['id' => $auth, 'roll_id' => '0', 'deletes' => '0', 'status' => '0'])->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->pass = md5($request->new_password); // Use MD5 hashing and assign to pass column
        $user->save();
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

    $response = [
        'status' => 'success',
        'message' => 'Password updated successfully'
    ];

    return response()->json($response);
}









  public function country(Request $request)
  {
    //   $countries=DB::table('countries')->select('countries.id','countries.name')->where('countries.flag','1')
    //   ->join('states','states.country_id','countries.id')
    //   ->join('cities','states.cities','countries.id')
    //   ->get();

    $countries = DB::table('countries')->select('countries.id', 'countries.name')
      ->selectRaw('COUNT(states.id) as statecount')
      ->join('states', 'countries.id', '=', 'states.country_id')
      ->where('countries.flag', 1)
      ->groupBy('countries.id', 'countries.name')
      ->havingRaw('COUNT(states.id) > 0')
      ->orderBy('countries.name', 'ASC')
      ->get();

    if ($countries) {
      $response = [

        'status' => 'sucess',
        'message' => 'Country list',
        'data' => [
          'user_id' => $countries

        ],
      ];

      return response($response);
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'No data available',
        'error' => 'no data available',
      ];

      return response($response);
    }

    // dd('awdwaf');
  }
  public function state(Request $request)
  {


    $request->country_id = Controller::BlockSQLInjection($request->country_id);
    if ($request->country_id == '' || $request->country_id == null || $request->country_id == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid country id!', 'error' => 'Please use a valid country id!'];
      goto returnFVI;
    }

    if (!empty($request->country_id)) {
      //  $states=DB::table('states')->select('id','name','country_id')->where('country_id',$request->country_id)->where('flag','1')->get();

      $states = DB::table('states')->select('states.id', 'states.name')
        ->selectRaw('COUNT(cities.id) as citycount')
        ->join('cities', 'states.id', '=', 'cities.state_id')
        ->where('states.flag', 1)
        ->where('states.country_id', $request->country_id) // Replace $id with the actual country ID you want to filter by
        ->groupBy('states.id', 'states.name')
        ->havingRaw('COUNT(cities.id) > 0')
        ->orderBy('states.name', 'ASC')
        ->get();




      if (count($states) > 0) {
        $response = [

          'status' => 'sucess',
          'message' => 'State list',
          'data' => [
            'states' => $states

          ],
        ];

        return response($response);
      } else {
        $response = [

          'status' => 'failed',
          'message' => 'No state available',
          'error' => 'no state available',
        ];

        return response($response);
      }
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'Please enter the country',
        'error' => 'Please enter the country',
      ];

      return response($response);
    }


    returnFVI:
    return response()->json($response);
  }
  public function city(Request $request)
  {
    $request->state_id = Controller::BlockSQLInjection($request->state_id);
    if ($request->state_id == '' || $request->state_id == null || $request->state_id == 'null') {
      $response = ['status' => 'failed', 'message' => 'Please use a valid state id!', 'error' => 'Please use a valid state id!'];
      goto returnFVI;
    }



    if (!empty($request->state_id)) {
      // $cities=DB::table('cities')->select('id','name','state_id')->where('state_id',$request->state_id)->where('flag','1')->get();
      // dd($cities);
      $cities = DB::table('cities')->select('id', 'name', 'state_id')->where('state_id', $request->state_id)
        ->where('flag', 1)
        ->orderBy('name', 'ASC')
        ->get();
      if (count($cities) > 0) {
        $response = [

          'status' => 'sucess',
          'message' => 'City list',
          'data' => [
            'cities' => $cities

          ],
        ];

        return response($response);
      } else {
        $response = [

          'status' => 'failed',
          'message' => 'No city available',
          'error' => 'no city available',
        ];

        return response($response);
      }
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'Please enter the state',
        'error' => 'Please enter the state',
      ];

      return response($response);
    }
    returnFVI:
    return response()->json($response);
  }
  public function password_change(Request $request)
  {

    try {
      $user_id = auth()->user()->id;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      $request->password = Controller::BlockSQLInjection($request->password);
      if ($request->password == '' || $request->password == null || $request->password == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
        goto returnFVI;
      }

      $request->new_password = Controller::BlockSQLInjection($request->new_password);
      if ($request->new_password == '' || $request->new_password == null || $request->new_password == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid new password!', 'error' => 'Please use a valid new password!'];
        goto returnFVI;
      }


      $password = $request->password;
      $confirm_password = $request->new_password;


      $pattern = "/^(?=.*[a-zA-Z])(?=.*\d{6,16})/";
      // dd(preg_match($pattern, $request->password));
      if (!preg_match($pattern, trim($request->password))) {
        $response = ['status' => 'failed', 'message' => 'At least one letter is required, with a minimum of 6 digits and a maximum of 16 digits.', 'error' => 'At least one letter is required, with a minimum of 6 digits and a maximum of 16 digits.'];
        goto returnFVI;
      }

      if (!empty($request->password)) {

        if (!empty($request->new_password)) {

          if ($password == $confirm_password) {

            $user = DB::table('user_register')->where('id', $user_id)
              ->where('deletes', '0')
              ->update(['pass' => md5($request->new_password), 'password' => $request->new_password,  'updated_at' => now()]);
            if ($user) {
              $response = [
                'status' => 'success',
                'message' => 'Password updated successfully',
                'data' => 'Password updated successfully',
              ];
              goto returnFVI;
            } else {


              $response = [

                'status' => 'success',
                'message' => 'Password updated successfully',
                'error' => 'Password updated successfully',
              ];

              goto returnFVI;
            }
          } else {
            $response = [

              'status' => 'failed',
              'message' => 'Password did not match',
              'error' => 'password did not match',
            ];

            goto returnFVI;
          }
        } else {
          $response = [

            'status' => 'failed',
            'message' => 'Please enter the confirm password',
            'error' => 'please enter the confirm password',
          ];

          goto returnFVI;
        }
      } else {
        $response = [

          'status' => 'failed',
          'message' => 'Please enter the password',
          'error' => 'please enter the password',
        ];

        goto returnFVI;
      }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
  public function delete_image(Request $request)
  {
    $auth = auth()->user()->id;
    //  dd($request);
    $update = DB::table('user_images')->where('user_id', $auth)->update(['deletes' => '1', 'type' => 'FRONT']);

    if ($update) {

      $response = [
        'status' => 'success',
        'message' => 'Image deleted successfully',
        'error' => 'image deleted successfully',
      ];

      return response($response);
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'Image deleted failed',
        'error' => 'image deleted failed',
      ];

      return response($response);
    }
  }
  public function img_delete(Request $request)
  {
    $auth = auth()->user()->id;
    //  dd($request);
    $update = DB::table('user_images')->where('user_id', $auth)->update(['deletes' => '1', 'type' => 'BACK']);

    if ($update) {

      $response = [
        'status' => 'success',
        'message' => 'Image deleted successfully',
        'error' => 'image deleted successfully',
      ];

      return response($response);
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'Image deleted failed',
        'error' => 'image deleted failed',
      ];

      return response($response);
    }
  }
  public function customer(Request $request)
  {
    $auth = auth()->user()->id;
    //  dd($auth

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
    )
      ->where(['user_register.id' => $auth, 'user_register.deletes' => 0, 'user_register.roll_id' => 0, 'user_register.status' => 0])
      ->get();



    if ($data) {

      $response = [

        'status' => 'success',
        'message' => 'Customer dashboard!',
        'data' => [
          'user_id' => $data
        ]


      ];
      return response($response);
    } else {
      $response = [
        'status' => 'failed',
        'message' => 'No data available',
        'error' => 'no data available',

      ];
      return response($response);
    }
  }
  
   public function user_image_upload1(Request $request)
  {
    //   return('deva prathap');
    $auth = auth()->user()->id;
    
    $response = [];
      $input = $request->all();
    //   $user_id =  $request->id;
    $user_id = $auth;

      $data = [];
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Token Missing Error'];
        goto returnFVI;
      }
      
      $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
      $maxFileSize = 15360; // Maximum file size in kilobytes (15 MB)
      
    //   dd($request->hasFile('image'));
      
       if ($request->hasFile('image')) {
           
          
        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        
       
         

        if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {
         

          $fileName = (isset($request->fileName) && $request->fileName != '' && $request->fileName != null && $request->fileName != 'null') ? $request->fileName : md5($name . uniqid() . $user_id  . time());

         
          $filePath = 'nationaldraw/' . $user_id . '/' .  $fileName . '.' . $file->getClientOriginalExtension();
          

          $store = Storage::disk('spaces')->put(
            '/' . $filePath,
            file_get_contents($request->file('image')->getRealPath()),
            'public'
          );
         
          // dd($store);

          if ($store) {
           
            $url = $filePath;
            //   return($url);
            
            $update = DB::table('user_register')->where('id', $auth)->update(['img_url' => $url,'lastlogin' => now()]);
            
            // return($update);
            
            if ($update) {

              $response = [
                'status' => 'success',
                'message' => 'Image upload successfully',
                'error' => 'image upload successfully',
              ];
               goto returnFVI;
        
             
            } else {
              $response = [
        
                'status' => 'failed',
                'message' => 'Image upload failed',
                'error' => 'image upload failed',
              ];
               goto returnFVI;
        
             
            }
            
            
            if ($url != '') {
              $data = [
                'digitalURL' => $url
              ];

              $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
              goto returnFVI;
            } else {
              $response = ['status' => 'failed', 'message' => "URL generation failed!", 'error' => "URL generation failed!"];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => "The File Upload Failed!", 'error' => "The File Upload Failed!"];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => "Kindly select Correct files!", 'error' => "Kindly select Correct files!"];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => "Kindly select the  Correct files.!", 'error' => "Kindly select the  Correct files.!"];
        goto returnFVI;
      }
    
    
    
    returnFVI:
      return response()->json($response);

  
  }
  
  
  
}
