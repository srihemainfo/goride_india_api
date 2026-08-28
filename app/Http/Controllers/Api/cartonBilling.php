<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Template\mailController;

class cartonBilling extends Controller
{








  /// Add to Cart
  public function addToCardCarton(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();

      $data = [];
      $buildCheckOut = $request->all();

      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';
      $finalTotal = 0;
      $quen = 0;
      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }

      $carton = DB::table('carton')
        ->where('deletes', '0')
        ->orderBy('id', 'ASC')
        ->get();

      $err = 0;
      foreach ($carton as $key => $value) {

        $request['item' . $value->id] = Controller::BlockSQLInjection($request['item' . $value->id]);
        if (!isset($request['item' . $value->id]) || $request['item' . $value->id] == '' || $request['item' . $value->id] == null || $request['item' . $value->id] == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid carton details!', 'error' => 'Please use a valid carton details!'];
          goto returnFVI;
        }

        if (isset($request['item' . $value->id]) && $request['item' . $value->id]  != 1 && $request['item' . $value->id]  != 0) {
          $err++;
        }

        if ($request['item' . $value->id]  == 1) {
          $finalTotal += intval($request['item' . $value->id]) * $value->cash_point;
          $quen += $request['item' . $value->id];
        }
      }

      if ($err > 0) {
        $response = ['status' => 'failed', 'message' => 'The Carton Missing!', 'error' => 'The Carton Missing!'];
        goto returnFVI;
      }

      $buildCheckOut['quantity'] = $quen;
      $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();

      $tran_id = 'CN' . uniqid(8) . date('Hi') . ($trid->id + 1);
      $checkout_arr = [
        'createdon' => now(),
        'crontime' =>  now(),
        'draw_id' => $draw_id,
        'ip' => $request->ip(),
        'user_id' => $user_id,
        'status' => '0',
        'transaction_id' => $tran_id,
        'checkout_response' => json_encode($buildCheckOut),
        'category' => 'CARTON',
        'gateway' => '',
        'finaltotal' => $finalTotal,
        'receipt_no' => '',
        'nenc_response' => '',
        'reference' => '',
        'pay_response' => '',
        'response' => '',
        'request_url' => '',
        're_response' => '',
        'settle_response' => '',
      ];

      $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

      if ($lastInsertId != '') {
        $data['transaction_id'] = $tran_id;
        $data['finalTotal'] = $finalTotal;
        $data['userCart'] = $buildCheckOut;
        $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }






  public function cartonPurchase(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();

      $data = [];
      // $buildCheckOut = $request->all();

      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';
      $finalTotal = 0;
      $quen = 0;


      $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
      if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => 'Please use a valid transaction id!'];
        goto returnFVI;
      }

      $transaction_id = $request->transaction_id;
      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }



      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
        goto returnFVI;
      }

      $payment_history = DB::table('payment_history')
        ->where('transaction_id',  $transaction_id)
        ->whereIn('pay_re_status', ['CAPTURED', 'Success', 'Shipped'])
        ->where('status', '0')
        ->where('category', 'CARTON')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get();




      // function cartonPurchase($con, $transaction_id, $dubaidate_time, $baseurl, $mailTemplate)
      // {
      // $payment_history = select_query($con, "payment_history", "", "`transaction_id` = '$transaction_id' and (`pay_re_status` = 'CAPTURED' OR `pay_re_status` = 'Success' OR `pay_re_status` = 'Shipped') and `status` = '0' AND `category` = 'CARTON' ORDER BY `id` DESC LIMIT 1", "", "");
      if ($payment_history->count() > 0) {

        $carton_order = DB::table('carton_order')
          ->where('transaction_id', '=', $transaction_id)
          ->where('deletes', '=', '0')
          ->orderBy('id', 'desc')
          ->limit(1)
          ->get();


        // $carton_order = select_query($con, "carton_order", "", "`transaction_id` = '$transaction_id' and `deletes`='0' order by `id` DESC", "", "");
        if ($carton_order->count() < 1) {



          // $user_id = $payment_history['result'][0]['user_id'];
          $checkout_response = json_decode($payment_history[0]->checkout_response, true);



          // $user_register_get = select_query($con, "user_register", "", "`id`='$user_id' and `id`!='' and `deletes`='0' and `status`='0' and `roll_id`='0' order by `id` DESC LIMIT 1", "", "");
          $name = auth()->user()->name;
          $mobile = auth()->user()->mobile;
          $email = auth()->user()->email;
          $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
          $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
          $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->nationality), "'");
          $obonus_points = auth()->user()->bonus_points;
          $getuserIP = $request->ip();
          $ocash_points = auth()->user()->cash_points;
          /// ORDER Insert

          $carton = DB::table('carton')
            ->where('deletes', '0')
            ->orderBy('id', 'ASC')
            ->get();




          // $carton = select_query($con, "carton", "", "`deletes`='0' order by `id` ASC ", "", ""); 

          $finalTotal = 0;
          $bonus_point = 0;
          $total = 0;
          $count = 0;
          $bottle = 0;
          foreach ($carton as $key => $productinfo) {


            $finalTotal +=  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $productinfo->id]) * intval($productinfo->cash_point)  : 0;
            $bonus_point +=  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $productinfo->id]) * intval($productinfo->bonus_point)  : 0;
            $total +=  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $productinfo->id]) * intval($productinfo->total)  : 0;
            $count += isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $productinfo->id]) * intval($productinfo->cartons)  : 0;
            $bottle += isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $productinfo->id]) * intval($productinfo->bottle_count)  : 0;
          }




          $orderArr = [
            "userid" => $user_id,
            "name" => $name,
            "email" => $email,
            "c_count" => $count,
            "deletes" => '0',
            "total_point" => $total,
            "total_cash" => $finalTotal,
            "total_bonus" => $bonus_point,
            "transaction_id" => $transaction_id,
            "bottle" => $bottle,
            "ip" => $getuserIP,
            "mobile" => $mobile,
            "createdon" => now()
          ];
          // $order_ins = insert($con, "carton_order", "", $orderArr, "", "", "");


          // $orderID = $order_ins['id'];

          $orderID = DB::table('carton_order')->insertGetId($orderArr);

          if ($orderID != '') {
            // Order Details Insert
            // foreach ($carton['result'] as $key => $productinfo) {
            foreach ($carton as $key => $productinfo) {
              $carton_id  = $productinfo->id;
              $bottle_count = $productinfo->bottle_count;
              $lop = isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $carton_id]) : 0;
              $totaln =  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $carton_id]) * intval($productinfo->total) : 0;
              $finalTotaln =  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $carton_id]) * intval($productinfo->cash_point) : 0;
              $bonus_pointn =  isset($checkout_response['item' . $productinfo->id]) ? intval($checkout_response['item' . $carton_id]) * intval($productinfo->bonus_point) : 0;
              for ($i = 0; $i < $lop; $i++) {
                $orderdArr = [
                  "userid" => $user_id,
                  "carton_id" => $carton_id,
                  "carton_order_id" => $orderID,
                  "bottle_count" => $bottle_count,
                  "deletes" => '0',
                  "total" => $totaln,
                  "cash" => $finalTotaln,
                  "bonus" => $bonus_pointn,
                  // "count" => $bottle_count,
                  "createdon" => now()
                ];

                // $order_details_ins = insert($con, "carton_details", "", $orderdArr, "", "", "");


                $order_details_ins = DB::table('carton_details')->insertGetId($orderdArr);
              }
            }




            $ctotalbonus = (int)$obonus_points + (int)$bonus_point;
            // Bonus Transaction Insert
            $bonusA = [
              'userid' => $user_id,
              'uname' => $name,
              'umobile' => $mobile,
              'uemail' => $email,
              'opening_balance' => $obonus_points,
              'total' => $bonus_point,
              'closeing_balance' => $ctotalbonus,
              'point_type' => 'BONUS',
              'transaction_type' => 'CREDIT',
              'reward_type' => 'CARTON',
              'card_no' => '',
              'reference_id' => $orderID,
              'reference_table' => 'carton_order',
              'ip' => $getuserIP,
              'device' => '',
              'deletes' => '0',
              'status' => '0',
              'createdon' => now(),
              'updatedon' => now()
            ];
            // $bonusA_ins = insert($con, "cb_transactions", "", $bonusA, "", "", "");

            $bonusA_ins = DB::table('cb_transactions')->insertGetId($bonusA);




            if ($bonusA_ins != '') {

              $ctotalcash = (int)$ocash_points + (int)$finalTotal;
              $Arr = [
                'userid' => $user_id,
                'uname' => $name,
                'umobile' => $mobile,
                'uemail' => $email,
                'opening_balance' => $ocash_points,
                'total' => $finalTotal,
                'closeing_balance' => $ctotalcash,
                'point_type' => 'CASH',
                'transaction_type' => 'CREDIT',
                'reward_type' => 'CARTON',
                'card_no' => '',
                'reference_id' => $orderID,
                'reference_table' => 'carton_order',
                'ip' => $getuserIP,
                'device' => '',
                'deletes' => '0',
                'status' => '0',
                'createdon' => now(),
                'updatedon' =>  now()
              ];
              // $cash_ins = insert($con, "cb_transactions", "", $Arr, "", "", "");

              $cash_ins = DB::table('cb_transactions')->insertGetId($Arr);





              if ($cash_ins != '') {

                $userArr = [
                  'cash_points' => $ctotalcash,
                  'bonus_points'  =>  $ctotalbonus
                ];


                $userUpdate = DB::table('user_register')
                  ->where('id', '=', $user_id)
                  // ->where('id', '!=', '')
                  ->where('deletes', '=', '0')
                  ->where('status', '=', '0')
                  ->where('roll_id', '=', '0')
                  ->update($userArr);


                // $userUpdate = update($con, "user_register", "`id`='$user_id' and `id` != '' and `deletes`='0' and `status`='0' and `roll_id`='0'", $userArr, "", "", "", "");
                // $errors = $userUpdate['errors'];
                // if ($errors != "") {
                //   $result["type"] = "0";
                //   $result["result"] = $errors;
                // } else {
                if ($userUpdate) {
                  // Payment History Update

                  $draw_arr = ["status" => '1'];

                  // dd($cash_ins);

                  $Inv_update = DB::table('payment_history')
                    ->where('transaction_id', '=', $transaction_id)
                    ->where('status', '=', '0')
                    ->update($draw_arr);



                  // $Inv_update = update($con, "payment_history", "`transaction_id` = '$transaction_id' and `status` = '0'", $draw_arr, "", "", "", "");
                  // $errors = $Inv_update['errors'];

                  // if ($errors != "") {
                  //   $result["type"] = "0";
                  //   $result["result"] = $errors;
                  // } else {

                  if ($Inv_update) {

                    $subject = "Order Confirmation-" . date("d-m-Y g:i a");

                    $request = [
                      'name' => ($name . ' ' . auth()->user()->lname ?? ''),
                      'total' => $total,
                      'bonus_point' => $bonus_point,
                      'cash_point' => $finalTotal,
                      'resultDatetime' => $drawData->data->active->resultDatetime,
                      'draw_no'  => $drawData->data->active->draw_no,
                      'drawfreq' => $drawData->data->active->drawfreq,
                      'raffleDrawDate' => (!Controller::checkGrandRaffleEligible($drawData->data->active->draw_no) ?  ('& <br>Grand Raffle Draw result on ' . Controller::raffleDrawDate('dS F Y')) : ''),
                      'SuperresultDatetime' =>  isset($drawData->data->activeSuperRaffle->drawResultDate) ? $drawData->data->activeSuperRaffle->drawResultDate : ''
                    ];



                    $messages  = mailController::addCreditPurchase($request);
                    if (isset($email) && $email != '') {
                      $emailchack = explode('@', $email);
                      if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                        // $emailsend = sendemail($con, $email, $subject, $messages, 'tickets');

                        $sendEmail = Controller::composeEmail($getuserIP, $email, $subject, $messages);
                      }
                    }


                    // unset($_SESSION['cartondata']);
                    // unset($_SESSION['merchantOrderReference']);
                    // unset($_SESSION['makepayemntre']);
                    // unset($_SESSION['MakepaymentState']);

                    // Log
                    // error_log_new($con, getUserIP(), 'Ticket_genereated',  $_SESSION['cusid'], '', '', 'Ticket Genereted. ID: ' . $ticketnumber, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

                    // Log
                    $log = Controller::error_log_new($getuserIP, 'Carton_genereated_API', $user_id, '', '', 'Carton generated ID: ' . $orderID, json_encode($input), __DIR__, basename(__FILE__), __LINE__);





                    // divert($baseurl . 'thanks/' . $transaction_id);

                    $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                    $response = ['status' => 'success', 'message' => 'Carton purchased success', 'data' => $data];
                    goto returnFVI;
                  } else {
                    $response = ['status' => 'failed', 'message' => 'The update process failed!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'The update process failed!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
                  goto returnFVI;
                }
              }
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'The insert process failed!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
            goto returnFVI;
          }
        } else {
          $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
          $response = ['status' => 'success', 'message' => 'Network payment success', 'data' => $data];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
        goto returnFVI;
      }
      // }















      // $err = 0;
      // foreach ($carton as $key => $value) {
      //   if (isset($request['item' . $value->id]) && $request['item' . $value->id]  != 1 && $request['item' . $value->id]  != 0) {
      //     $err++;
      //   }

      //   if ($request['item' . $value->id]  == 1) {
      //     $finalTotal += intval($request['item' . $value->id]) * $value->cash_point;
      //     $quen += $request['item' . $value->id];
      //   }
      // }

      // if ($err > 0) {
      //   $response = ['status' => 'failed', 'message' => 'The Carton Missing!', 'error' => 'The Carton Missing!'];
      //   goto returnFVI;
      // }

      // $buildCheckOut['quantity'] = $quen;
      // $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();

      // $tran_id = 'CN' . uniqid(8) . date('Hi') . ($trid->id + 1);
      // $checkout_arr = [
      //   'createdon' => now(),
      //   'crontime' =>  now(),
      //   'draw_id' => $draw_id,
      //   'ip' => $request->ip(),
      //   'user_id' => $user_id,
      //   'status' => '0',
      //   'transaction_id' => $tran_id,
      //   'checkout_response' => json_encode($buildCheckOut),
      //   'category' => 'CARTON',
      //   'gateway' => '',
      //   'finaltotal' => $finalTotal,
      //   'receipt_no' => '',
      //   'nenc_response' => '',
      //   'reference' => '',
      //   'pay_response' => '',
      //   'response' => '',
      //   'request_url' => '',
      //   're_response' => '',
      //   'settle_response' => '',
      // ];

      // $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

      // if ($lastInsertId != '') {
      //   $data['transaction_id'] = $tran_id;
      //   $data['finalTotal'] = $finalTotal;
      //   $data['userCart'] = $buildCheckOut;
      //   $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
      //   goto returnFVI;
      // } else {
      //   $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
      //   goto returnFVI;
      // }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  // Get User Product Cart Details
  public function getUserProductCart(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();
      $transaction_id = $request->transaction_id;
      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => 'Kindly check the transaction ID!'];
        goto returnFVI;
      }

      $collectCartData = DB::table('payment_history')->where('transaction_id', $transaction_id)->where('user_id', $user_id)->orderBy('id', 'desc')->limit(1)->get();
      if ($collectCartData->count() > 0) {
        $data['cartData'] = json_decode($collectCartData[0]->checkout_response, true);
        $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Not Found!', 'error' => 'Kindly check the transaction ID!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function VerifyDetails(Request $request)
  {
    try {
      $result = [];
      $input = $request->all();
      $transaction_id = $request->transaction_id;

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $data['allow_id'] = '';
      $count = 0;

      if (!empty($transaction_id)) {

        $paymentHistory = DB::table('payment_history')
          ->where('transaction_id', $transaction_id)
          ->where('user_id', $user_id)
          ->where('status', '0')
          ->orderBy('id', 'desc')
          ->limit(1)
          ->get();

        if ($paymentHistory->count() > 0) {
          $post = json_decode($paymentHistory[0]->checkout_response, true);
          $draw_id = $paymentHistory[0]->draw_id;

          if (empty($draw_id) || $draw_id == '0' || $draw_id < 1 || is_null($draw_id)) {
            $response = ['status' => 'failed', 'message' => 'Draw Not Found!', 'error' => 'Active Draws Not Found!'];
            goto returnFVI;
          }

          $pt = 4;
          $ot = 1;
          $k = "";

          for ($x = 1; $x <= $pt; $x++) {
            $tval = "item" . $x;

            $post[$tval] = $post[$tval] ?? '';
            // dump($post[$tval]);
            if (isset($post[$tval]) && $post[$tval] != '' && $post[$tval] != null && $post[$tval] != 'null' &&  $post[$tval] > 0) {
              $k++;

              switch ($x) {
                case 1:
                  $rowvalue = "a";
                  break;
                case 2:
                  $rowvalue = "b";
                  break;
                case 3:
                  $rowvalue = "c";
                  break;
                case 4:
                  $rowvalue = "d";
                  break;
                default:
                  $rowvalue = "";
                  break;
              }

              $numlines = $post[$tval];

              for ($g = 1; $g <= $numlines; $g++) {
                $checkline = "itemid_row" . $rowvalue . "_" . $g;

                if (!empty($post[$checkline])) {

                  $my3number = $post[$checkline];
                  $checkBlockList = DB::table('blocked_my3number')
                    ->where('drawid', $draw_id)
                    ->where('deletes', '0')
                    ->where(function ($query) use ($my3number) {
                      $query->where('first', 'LIKE', $my3number)
                        ->orWhere('second', 'LIKE', $my3number)
                        ->orWhere('third_one', 'LIKE', $my3number)
                        ->orWhere('third_two', 'LIKE', $my3number)
                        ->orWhere('third_three', 'LIKE', $my3number)
                        ->orWhere('third_four', 'LIKE', $my3number);
                    })
                    ->whereIn('site', ['BOTH', 'CUSTOMER'])
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->get();

                  if ($checkBlockList->count() > 0) {
                    $product_id = $x;
                    $userid = $paymentHistory[0]->user_id;
                    $row = $checkBlockList[0];
                    $mobile_no = auth()->user()->mobile;

                    // $mobile_no = DB::table('user_register')
                    //     ->where('id', $userid)
                    //     ->where('deletes', '0')
                    //     ->value('mobile');

                    $allow_to_purchase = DB::table('allow_to_purchase')
                      ->where('drawid', $draw_id)
                      ->where('my3number', $my3number)
                      ->where('product_id', $product_id)
                      ->where('deletes', '0')
                      ->where('status', 'UNPAID')
                      ->where('user_id', $user_id)
                      ->orderBy('id', 'desc')
                      ->limit(1)
                      ->get();

                    if ($allow_to_purchase->count() > 0) {
                      if ($data['allow_id'] == '') {
                        $data['allow_id'] = $allow_to_purchase[0]->id;
                      }
                      $count++;
                    } else {
                      // $cartdata = json_encode(Session::get('cartdata'));

                      $cartdata = json_encode($post);
                      $get_block_user = DB::table('blocked_my3number_user')
                        ->where('checkout', 'LIKE', '%' . $cartdata . '%')
                        ->where('userid', $user_id)
                        ->where('drawid', $draw_id)
                        ->where('productid', $product_id)
                        ->orderBy('id', 'desc')
                        ->limit(1)
                        ->get();

                      if ($get_block_user->count() < 1) {

                        $blockArr = [
                          "userid" => $user_id,
                          "productid" => $product_id,
                          "agentid" => '0',
                          "my3number" => $my3number,
                          "blockid" => $row->id,
                          "payment_his_id" => $paymentHistory[0]->id,
                          "mobile_no" => $mobile_no,
                          "createdon" => now(),
                          "checkout" => $cartdata,
                          "drawid" => $draw_id,
                          "tried_to" => 1,
                        ];

                        $blocked_my3number_user = DB::table('blocked_my3number_user')->insert($blockArr);
                        $idn = DB::getPdo()->lastInsertId();
                      } else {
                        $idn = $get_block_user[0]->id;
                        $tried_to = $get_block_user[0]->tried_to + 1;
                        DB::table('blocked_my3number_user')
                          ->where('id', $idn)
                          ->update(['tried_to' => $tried_to]);
                      }

                      $get_block_user_es = DB::table('blocked_my3number_user')->where(
                        [
                          ['userid', '=', $user_id],
                          ['drawid', '=', $draw_id],
                          ['productid', '=', $product_id],
                          ['notify_status', '=', '1'],
                        ]
                      )->orderBy('id', 'desc')->limit(1)->get();


                      if ($get_block_user_es->count() < 1) {
                        if (!empty($idn)) {


                          $set_notify = DB::table('set_notify')
                            ->whereIn('id', ['1', '2'])
                            ->where('type', 'Block_My3Number')
                            ->where('deletes', '0')
                            ->orderBy('id', 'asc')
                            ->get();

                          $cus_name = ucwords(strtolower(auth()->user()->name));


                          // $tURL = get_tiny_url(env('Admin_URL') . 'blockmy3numbers/list/' . $user_id);
                          // Tiny URL not Working
                          $response = Http::get('http://tinyurl.com/api-create.php?url=' . env('Admin_URL') . 'blockmy3numbers/list/' . $user_id);
                          $tURL = $response->getBody()->getContents();


                          $cat_txt = '';

                          if ($row->first == $my3number) {
                            $cat_txt = 'Straight';
                          } elseif ($row->second == $my3number) {
                            $cat_txt = 'Reverse';
                          } elseif (in_array($my3number, [$row->third_one, $row->third_two, $row->third_three, $row->third_four])) {
                            $cat_txt = 'Mix';
                          }


                          if ($set_notify->count() > 0) {
                            foreach ($set_notify as $value) {

                              if ($value->notify_to == 'EMAIL') {
                                $emailArr = json_decode($value->notify);
                                foreach ($emailArr as $email) {
                                  $subject = 'Tried to Purchase the Blocked My 3 Number';
                                  $messages = '<div style="font-family: Helvetica, Arial, sans-serif;min-width: 100%;overflow: auto;line-height: 2;">
                                                                    <div style="margin: 50px auto; width: 70%; padding: 20px 0">
                                                                        <div style="border-bottom: 1px solid #eee">
                                                                            <a href="" style="font-size: 1.4em;color: #00466a;text-decoration: none;font-weight: 600;">National Draw</a>
                                                                        </div>
                                                                        <p style="font-size: 1.1em">Hi Team,</p>
                                                                        <p>
                                                                            The following Customer - ' . $cus_name . ' has tried to purchase the blocked ' . $cat_txt . ' My3Number ' . $my3number . '. Please use the below link to review the Customer\'s activity.
                                                                        </p>
                                                                        <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
                                                                            <a href="' . $tURL . '" style="color: white;font-size: 14px;text-decoration: none;cursor: pointer;">View Details</a>
                                                                        </h2>
                                                                        <p style="font-size: 0.9em">Regards,<br />National Draw</p>
                                                                        <hr style="border: none; border-top: 1px solid #eee" />
                                                                        <div style="float: right;padding: 8px 0;color: #aaa;font-size: 0.8em;line-height: 1;font-weight: 300;"></div>
                                                                    </div>
                                                                </div>';

                                  if (!empty($email)) {
                                    $emailchack = explode('@', $email);
                                    if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                                      $email_json = [
                                        "senderName" => "Draw",
                                        "method" => "sendmail",
                                        "emailid" => $email,
                                        "subject" => $subject,
                                        "emailtemplated" => $messages,
                                        "log" => "1",
                                      ];
                                      // $email_sent = invokeApiRequest('POST', $adminurl . 'api/invokeemail.php', '', json_encode($email_json));
                                      $email_sent = Http::post(env('Admin_URL') . 'api/invokeemail.php', $email_json);
                                    }
                                  }
                                }
                              }

                              if ($value->notify_to == 'SMS') {
                                $mobileArr = json_decode($value->notify);
                                foreach ($mobileArr as $mobile) {
                                  if (substr($mobile, 0, 3) == "971") {
                                    $messages = 'The following Customer - ' . $cus_name . ' has tried to purchase - ' . $cat_txt . ' My3Number ' . $my3number . '.';
                                    // sendsms($con, $mobile, $messages, "");
                                    $sentsms = Controller::sendsms($mobile, $messages, '');
                                    // dd($sentsms);
                                  }
                                }
                              }
                            }

                            DB::table('blocked_my3number_user')
                              ->where('id', $idn)
                              ->update(['notify_status' => '1']);
                          }
                        }
                      }

                      goto failed;
                    }

                    if ($count == 1) {
                      goto success;
                    }

                    failed:
                    $response = ['status' => 'failed', 'message' => 'Sorry, the server is busy. Please try again later! Error 502', 'error' => 'Sorry, the server is busy. Please try again later! Error 502'];
                    goto returnFVI;
                    success:
                  }
                }

                $ot++;
              }
            } else {
            }
          }

          $data["seoURL"] = 'https://trk.convserv.com/tracko/v1/cont/cont.js?of=226&ac=5&af=16&cs=0&cp1=' . intval($post['item1']) . '&cp2=' . intval($post['item2']) . '&cp3=' . intval($post['item3']) . '&cp4=' . intval($post['item4']) . '&cp5=' . number_format(intval($paymentHistory[0]->finaltotal), 2) . '&cp6=' . $user_id . '&ts={timestamp}';
          $response = ['status' => 'success', 'message' => 'verification success', 'data' => $data];
          goto returnFVI;
        } else {
          $response = ['status' => 'failed', 'message' => 'Transaction Not Found!', 'error' => 'Kindly refresh and try again!'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Not Found!', 'error' => 'Kindly check the transaction ID!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
