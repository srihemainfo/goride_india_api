<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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


use \App\Mail\OtpMail;

class AddcreditController extends Controller
{
  public function card_details(Request $request)
  {
    $auth = auth()->user()->id;

    // $base_url = ($request->header('Origin') . '/');

    $data = DB::table('carton')->select('id', 'size', 'image', 'bottle_count', 'cartons', 'total', 'cash_point', 'bonus_point')->where('carton.deletes', '=', '0')->get();

    if ($data) {

      $detailsArray = [];
      foreach ($data as $datas) {
        $details = [
          'id' => $datas->id,
          'image' => ($request->header('Origin') . '/') . '' . $datas->image,
          'bottle_count' => $datas->bottle_count,
          'cartons' => $datas->cartons,
          'total' => $datas->total,
          'cash_point' => $datas->cash_point,
          'bonus_points' => $datas->bonus_point,
        ];
        $detailsArray[] = $details;
      }
      $response = [

        'status' => 'success',
        'message' => 'Carton data List',
        'data' => [
          'carton_list' => $detailsArray,
        ]
      ];
      return response($response);
    } else {
      $response = [
        'status' => 'failed',
        'message' => 'Your cart is empty',
        'error' => 'your cart is empty',

      ];
      return response($response);
    }
  }

  public function cartons_stored(Request $request)
  {

    $auth = auth()->user()->id;
    $dubaidate_time = date('Y-m-d H:i:s');
    $draw_id = $request->draw_id;
    $user_ip = $request->ip();
    $status = '0';
    $tran_id = 'CN' . uniqid(15) . time();
    $category = 'CARTON';

    $item = $request->item;
    // dd
    $array = explode(",", $item);
    //  dd($array);
    $total_num_count = 0;

    $empty = [];

    foreach ($array as  $productinfo) {

      $datas = DB::table('carton')->select('total')->where('id', $productinfo)->first();

      $details = [
        $datas,
      ];
      $empty[] = $details;
      // dd($empty);
    }
    $currentTime = now();
    $latest_draw =  DB::table('draw')
      ->where('status', 'Active')
      ->where('deletes', '0')
      ->orderBy('id', 'ASC')
      ->first();

    //   dd($latest_draw->id);



    //   $checkout_arr = array('createdon' => $dubaidate_time, 'crontime' => $dubaidate_time, 'draw_id' => $draw_id, 'ip' => getUserIP(), "user_id" => $USER_ID, "status" => '0',
    //   "transaction_id" => $tran_id, "checkout_response" => json_encode($_COOKIE['cartondata']), "category" => 'CARTON');

    $insert = DB::table('payment_history')->insert([
      'createdon' => $dubaidate_time, 'crontime' => $dubaidate_time, 'ip' => $user_ip, 'user_id' => $auth, 'status' => '0', 'transaction_id' => $tran_id,
      'finaltotal' => $empty[0][0]->total,
      'checkout_response' => '', 'category' => 'CARTON', 'gateway' => '', 'draw_id' => $latest_draw->id, 'receipt_no' => '', 'nenc_response' => '', 'pay_response' => '',
      'response' => '', 'request_url' => '', 're_response' => '', 're_response' => '', 'settle_response' => ''
    ]);

    if ($insert) {
      $response = [

        'status' => 'scuess',
        'message' => 'User selected cartons to stored',
        'data' => [
          'user_id' => $auth,

        ],

      ];

      return response($response);
    } else {
      $response = [

        'status' => 'failed',
        'message' => 'User selected cartons not store',
        'data' => 'failed'

      ];

      return response($response);
    }
  }
  public function ccavenue(Request $request)
  {
    $auth = auth()->user()->id;

    $ccRequest = [
      "merchant_id" => (int) env('merchantId'),
      "order_id" => $transaction_id,
      "amount" => (int) $payment_history[0]->finaltotal,
      "language" => "EN",
      "saveCard" => $saveCard,
      "billing_name" => $firstname,
      "billing_address" => $address,
      "billing_city" => substr($city, 0, 29),
      "billing_state" => "",
      "billing_zip" => "",
      "billing_country" => $nationality,
      "billing_tel" => $mobile,
      "billing_email" => $emailid,
      "redirect_url" => ($request->header('Origin') . '/') . 'ccavenue/ccavResponseHandler.php',
      "merchant_param1" => $user_id,
      "cancel_url" => ($request->header('Origin') . '/') . "billing",
      "tid" => $transaction_id,
      "merchant_param2" => $merchant_param2
    ];
  }
  public function payment_status(Request $request)
  {
    $auth = auth()->user()->id;
    if (!empty($request->transaction_id)) {

      $check_status = DB::table('payment_history')->select('transaction_id')->where(['transaction_id' => $request->transaction_id, 'status' => '1'])->first();
      //   dd($check_status);
      if ($check_status) {
        $response = [
          'status' => 'sucess',
          'message' => 'payment sucess',
          'data' => ['transaction_id' => $request->transaction_id],
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
    } else {
      $response = [
        'status' => 'failed',
        'message' => 'Please enter transactionid',
        'error' => 'please enter transactionid',
      ];
      return response($response);
    }
  }
  public function thanks_screen(Request $request)
  {
    $auth = auth()->user()->id;

    if ($check_status) {
      $check_status = DB::table('payment_history')->select('transaction_id')->where(['transaction_id' => $request->transaction_id, 'status' => '1'])->first();
      if ($check_status) {
        $response = [
          'status' => 'sucess',
          'message' => 'payment sucess',
          'data' => ['transaction_id' => $request->transaction_id],
        ];
        return response($response);
      } else {
        $response = [
          'status' => 'failed',
          'message' => 'Payment failed',
          'error' => 'payment failed',
        ];
        return response($response);
      }
    } else {
      $response = [
        'status' => 'failed',
        'message' => 'Please enter transactionid',
        'error' => 'please enter transactionid',
      ];
      return response($response);
    }
  }
}
