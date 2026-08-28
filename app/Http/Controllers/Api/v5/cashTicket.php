<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\drawsController;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class cashTicket extends Controller
{

  public function getCusSiteTotal($checkoutResponse)
  {
    try {
      $post = json_decode($checkoutResponse, true);

      $prices = [
        'item1' => 10,
        'item2' => 20,
        'item3' => 50,
        'item4' => 100,
      ];

      $finalTotal = 0;

      foreach ($prices as $itemName => $price) {
        if (isset($post[$itemName]) && $post[$itemName] > 0) {
          $finalTotal += $post[$itemName] * $price;
        }
      }

      return $finalTotal;
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }















  public function cashTicketCheck(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();

      $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
      if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => 'Please use a valid transaction id!'];
        goto returnFVI;
      }

      $transaction_id = $request->transaction_id;
      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
        goto returnFVI;
      }

      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
        goto returnFVI;
      }

      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
        goto returnFVI;
      }
      $payment_history = DB::table('payment_history')
        ->select('id', 'checkout_response', 'transaction_id', 'verify_status')
        ->where('transaction_id', $transaction_id)
        ->where('status', '0')
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();
      // dd($payment_history[0]->checkout_response);
      if ($payment_history->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'Transaction Track not found!', 'error' => 'Transaction Track not found!'];
        goto returnFVI;
      }

      if ($payment_history[0]->verify_status == 'NO') {
        $response = ['status' => 'failed', 'message' => 'The verification failed!', 'error' => 'The verification failed!'];
        goto returnFVI;
      }

      $cpticket = DB::table('cpticket')
        ->where('transaction_id', $transaction_id)
        ->get();

      $bpticket = DB::table('bpticket')
        ->where('transaction_id', $transaction_id)
        ->get();


      $data['cash_points'] = auth()->user()->cash_points;
      $data['bonus_points'] = auth()->user()->bonus_points;
      $data['cartTotal'] = $this->getCusSiteTotal($payment_history[0]->checkout_response);
      $data['cpticket'] = $cpticket->count();
      $data['bpticket'] = $bpticket->count();

      $response = ['status' => 'success', 'message' => 'User data collected successfully', 'data' => $data];
      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
















  public function cashTicketGenerate(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();



      $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
      if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => 'Please use a valid transaction id!'];
        goto returnFVI;
      }


      $transaction_id = $request->transaction_id;
      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Login Required!'];
        goto returnFVI;
      }

      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => 'Transaction ID Required!'];
        goto returnFVI;
      }

      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }

      $payment_history = DB::table('payment_history')
        ->where('transaction_id', $transaction_id)
        ->where('status', '0')
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();

      if ($payment_history->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Missing!', 'error' => 'Transaction ID Missing!'];
        goto returnFVI;
      }





      // $payment_history = select_query($con, "payment_history", "", "`transaction_id` = '$transaction_id' AND `status` = '0' ORDER BY `id` DESC LIMIT 1", "", "");
      if ($payment_history->count() > 0) {
        // if ($draw_id != '' && $draw_id != 0) {


        // $oticket = select_query($con, "cpticket", "", "`transaction_id` = '$transaction_id' and `deletes`='0' order by `id` DESC", "", "");


        $oticket = DB::table('cpticket')
          ->where('transaction_id', $transaction_id)
          ->where('deletes', '0')
          ->orderBy('id', 'desc')
          ->get();
        if ($oticket->count() < 1) {




          $ottype = 'CP';


          // // Log
          $log = Controller::error_log_new($request->ip(), 'strat_ticket_generate', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);


          // Log
          // error_log_new($con, getUserIP(), 'strat_ticket_generate', $_SESSION['cusid'], '', '', 'Start Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);




          $response = json_decode($payment_history[0]->response, true);
          $user_id = $payment_history[0]->user_id;
          $checkout_response = json_decode($payment_history[0]->checkout_response, true);

          $orderReference = $payment_history[0]->reference;
          $transaction_id = $payment_history[0]->transaction_id;



          // Log
          // error_log_new($con, getUserIP(), 'strated', $_SESSION['cusid'], '', '', 'Draw ID:' . $draw_id, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

          // // Log
          $log = Controller::error_log_new($request->ip(), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($response), __DIR__, basename(__FILE__), __LINE__);



          // $user_register_get = select_query($con, "user_register", "", "`id`='$user_id' and `id`!='' and `deletes`='0' and `status`='0' and `roll_id`='0' order by `id` DESC LIMIT 1", "", "");

          $name = auth()->user()->name;
          $mobile = auth()->user()->mobile;
          $email = auth()->user()->email;
          $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
          $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
          $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->nationality), "'");
          $cash_points = (int) auth()->user()->cash_points;






          $post = $checkout_response;

          // if ($post['item1'] > 0) {
          //   $tentotal = $post['item1'] * 10;
          // } else {
          //   $tentotal = "0";
          // }

          // if ($post['item2'] > 0) {
          //   $twentytotal = $post['item2'] * 20;
          // } else {
          //   $twentytotal = "0";
          // }

          // if ($post['item3'] > 0) {
          //   $thirtytotal = $post['item3'] * 50;
          // } else {
          //   $thirtytotal = "0";
          // }

          // if ($post['item4'] > 0) {
          //   $fourtytotal = $post['item4'] * 100;
          // } else {
          //   $fourtytotal = "0";
          // }

          // $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;
          // $totline = $post['item1'] + $post['item2'] + $post['item3'] + $post['item4'];
          // $count = intval($post['item1']) + intval($post['item2']) + intval($post['item3']) + intval($post['item4']);
          $tentotal = isset($post['item1']) && $post['item1'] > 0 ? $post['item1'] * 10 : 0;
          $twentytotal = isset($post['item2']) && $post['item2'] > 0 ? $post['item2'] * 20 : 0;
          $thirtytotal = isset($post['item3']) && $post['item3'] > 0 ? $post['item3'] * 50 : 0;
          $fourtytotal = isset($post['item4']) && $post['item4'] > 0 ? $post['item4'] * 100 : 0;

          $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;
          $totline = ($post['item1'] ?? 0) + ($post['item2'] ?? 0) + ($post['item3'] ?? 0) + ($post['item4'] ?? 0);
          $count = intval($post['item1'] ?? 0) + intval($post['item2'] ?? 0) + intval($post['item3'] ?? 0) + intval($post['item4'] ?? 0);


          if ($cash_points >= $finaltotal) {

            $nettotal_new = $cash_points - $finaltotal;




            // $upArff = ["cash_points" => $nettotal_new];
            // $ins_updathh = setupdate($con, "user_register", "`id`='$user_id'", $upArff, "", "", "");

            $upArff = ["cash_points" => $nettotal_new];

            $ins_updathh = DB::table('user_register')
              ->where('id', $user_id)
              ->update($upArff);



            $onepercen = intval($finaltotal) / 105;
            $total_amount = floatval($onepercen) * 100;
            $tax_value = intval($finaltotal) - floatval($total_amount);

            $Arr1 = [
              "draw_id" => $draw_id,
              "user_id" => $user_id,
              "total_lines" => $totline,
              "sale_from" => 1,
              "purchase_datetime" => now(),
              "net_total" => $finaltotal,
              "tax_percentage" => 5,
              "tax_value" => number_format($tax_value, 2, ".", ""),
              "total_amount" => number_format($total_amount, 2, ".", ""),
              "payment_by" => "CASH",
              "transaction_id" => $transaction_id,
              "createdon" => now(),
              "agent_id" => 0,
              "ticket_no" => '',
              "invoice_no" => 0,
              "status" => 0,
              "payment_transaction_id" => '',
              "referral_id" => 0,
              "deletes" => 0,
              "delete_reason" => ''
            ];

            // $ticket = insert($con, "cpticket", "", $Arr1, "", "", "");
            // $ticketnumber = $ticket['id'];
            $ticketnumber = DB::table('cpticket')->insertGetId($Arr1);



            $ticketid = $ottype . sprintf("%04d", $ticketnumber);






            // $Arr1 = ["ticket_id" => $ticketnumber, "type" => $ottype, "firstname" => $name, "lastname" => '', "emailid" => $email, "address" => $address, "city" => $city, "country" => $nationality, "response" => 'CASHPOINT'];
            // $invoice = insert($con, "invoice", "", $Arr1, "", "", "");
            // $invoiceid = $invoice['id'];



            $Arr1 = [
              "ticket_id" => $ticketnumber,
              "type" => $ottype,
              "firstname" => $name,
              "lastname" => '',
              "emailid" => $email,
              "address" => $address,
              "city" => $city,
              "country" => $nationality,
              "response" => 'CASHPOINT'
            ];

            $invoiceid = DB::table('invoice')->insertGetId($Arr1);

            /// LD Bank && Point Transaction not needed


            /// CB Transaction  needed




            // $Arr13 = [
            //   'userid' => $user_id,
            //   'uname' => $name,
            //   'umobile' => $mobile,
            //   'uemail' => $email,
            //   'opening_balance' => $cash_points,
            //   'total' => $finaltotal,
            //   'closeing_balance' => $nettotal_new,
            //   'point_type' => 'CASH',
            //   'transaction_type' => 'DEBIT',
            //   'reward_type' => 'PURCHASE',
            //   'card_no' => '',
            //   'reference_id' => $ticketnumber,
            //   'reference_table' => 'cpticket',
            //   'ip' => $request->ip(),
            //   'device' => '',
            //   'deletes' => '0',
            //   'status' => '0',
            //   'createdon' => now(),
            //   'updatedon' => now()
            // ];
            // $cb_trans_ins = insert($con, "cb_transactions", "", $Arr13, "", "", "");

            $Arr13 = [
              'userid' => $user_id,
              'uname' => $name,
              'umobile' => $mobile,
              'uemail' => $email,
              'opening_balance' => $cash_points,
              'total' => $finaltotal,
              'closeing_balance' => $nettotal_new,
              'point_type' => 'CASH',
              'transaction_type' => 'DEBIT',
              'reward_type' => 'PURCHASE',
              'card_no' => '',
              'reference_id' => $ticketnumber,
              'reference_table' => 'cpticket',
              'ip' => request()->ip(),
              'device' => '',
              'deletes' => '0',
              'status' => '0',
              'createdon' => now(),
              'updatedon' => now()
            ];

            $cb_trans_ins = DB::table('cb_transactions')->insert($Arr13);









            // $Arr2 = ["ticket_no" => $ticketid, "invoice_no" => $invoiceid];
            // $ins_update = setupdate($con, "cpticket", "`id`='$ticket[id]'", $Arr2, "", "", "");

            $Arr2 = [
              "ticket_no" => $ticketid,
              "invoice_no" => $invoiceid
            ];

            $ins_update = DB::table('cpticket')
              ->where('id', $ticketnumber) // Assuming you have the 'id' value in the $ticket array
              ->update($Arr2);


            $pt = "4";
            $ot = 1;
            $k = "";
            for ($x = 1; $x <= $pt; $x++) {
              $tval = "item" . $x;
              if (isset($post[$tval]) && $post[$tval] > 0) {
                $k++;
                // $product = select_query($con, "product", "", "`id`='$x'  ", "", "");
                // foreach ($product['result'] as $key => $productinfolist) {
                // }

                $product = DB::table('product')
                  ->where('id', $x)
                  ->first();

                if ($x == "1") {
                  $rowvalue = "a";
                } else if ($x == "2") {
                  $rowvalue = "b";
                } else if ($x == "3") {
                  $rowvalue = "c";
                } else if ($x == "4") {
                  $rowvalue = "d";
                } else {
                  $rowvalue = "";
                }

                $numlines = $post[$tval];
                for ($g = 1; $g <= $numlines; $g++) {
                  $checkline = "itemid_row" . $rowvalue . "_" . $g;
                  if ($post[$checkline] != "") {
                    $my3number = "";
                    $linedate = "";
                    $my3number = $post[$checkline];
                    $raffle_id = $ottype . $ticketnumber . str_pad($ot, 2, "0", STR_PAD_LEFT);


                    // $linedate = ["my3number" => $my3number, "user_id" => $user_id, "ticket_id" => $ticketnumber, "draw_id" => $draw_id, "agent_id" => 0, "product_id" => $x, "orders" => $ticketid, "orders" => $ticketnumber, "raffle_id" => $raffle_id, "type" => $ottype, "invoice_no" => $invoiceid, "createdon" => now()];
                    // $lines = insert($con, "ticket_lines", "", $linedate, "", "", "");


                    $linedate = [
                      "my3number" => $my3number,
                      "user_id" => $user_id,
                      "ticket_id" => $ticketnumber,
                      "draw_id" => $draw_id,
                      "agent_id" => 0,
                      "product_id" => $x,
                      "orders" => $ticketnumber,
                      "raffle_id" => $raffle_id,
                      "type" => $ottype,
                      "invoice_no" => $invoiceid,
                      "createdon" => now(),
                      'deletes' => '0'
                    ];

                    $lines = DB::table('ticket_lines')->insert($linedate);

                    $ot++;
                  }
                }
              }
            }




            // $draw_arr = ["status" => '1', "pay_re_status" => 'CASH', "gateway" => "CASHPOINT", "draw_id" => $draw_id];
            // $Inv_update = update($con, "payment_history", "`transaction_id` = '$transaction_id' and `status` = '0'", $draw_arr, "", "", "", "");


            $draw_arr = [
              "status" => '1',
              "pay_re_status" => 'CASH',
              "gateway" => "CASHPOINT",
              "draw_id" => $draw_id,
            ];

            $Inv_update = DB::table('payment_history')
              ->where('id', $payment_history[0]->id)
              ->where('transaction_id', $transaction_id)
              ->where('status', '0')
              ->update($draw_arr);

            // $errors = $Inv_update['errors'];

            // if ($errors != "") {
            //   $result["type"] = "0";
            //   $result["result"] = $errors;
            // } else {
            if ($Inv_update) {

              // dd($Inv_update);


              // $printurlf = ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id;
              // $printinvoice = ($request->header('Origin') . '/') . "invoice/" . $transaction_id;

              // $printurl = get_tiny_url($printurlf);
              // $invoiceurl = get_tiny_url($printinvoice);

              // if (substr($mobile, 0, 3) == "971") {

              //   $messages1 = 'Ticket ID #' . $ticketid . '.';

              //   $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `type` = '$ottype' and `ticket_id`!='' and `deletes` = '0' group by `product_id` order by `product_id` ASC  ", "", "");

              //   foreach ($query['result'] as $key => $valuelist) {

              //     $p_id = $valuelist['product_id'];

              //     $t_id = $valuelist['orders'];

              //     $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");

              //     $messages1 .= 'CAT-AED ' . round($product['result'][0]['rate']) . '. ';

              //     $rcount = count($mynumber['result']);

              //     $io = 1;

              //     $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and `type` = '$ottype' and `deletes` = '0'", "", "");

              //     foreach ($mynumber['result'] as $key => $mynumber1) {
              //       $messages1 .= $mynumber1['my3number'];
              //       if ($io == $rcount) {
              //         $messages1 .= '. ';
              //       } else {
              //         $messages1 .= ', ';
              //       }

              //       $io++;
              //     }
              //   }

              //   $messages1 .= 'for an Amounts of AED ' . number_format((float) $finaltotal, 2, '.', '') . ' for more info. ( ' . $printurl . ' ),. TC apply. Good Luck!!!';

              //   $templateid = "";

              //   sendsms($con, $mobile, $messages1, $templateid);
              // }

              $subject = "Purchase Confirmation-" . date("d-m-Y g:i a");

              $messages = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                              <html xmlns="http://www.w3.org/1999/xhtml">

                              <head>

                              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

                              <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                              <meta name="viewport" content="width=device-width, initial-scale=1.0">

                              <title>Ticket Purchase OTP Mail Template</title>

                              <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=iAigOX6GVJu4ROvuy0LxQnLmSNev6g4m9B77vlBijml2y1CBtzTQKdnxq9XYMjJF06TLForU786USAwOkS_k1Pkh65Ehn0aBhdND7VvcwC8" charset="UTF-8"></script><style type="text/css">



                                @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

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



                                      <tr><td class="column-one" style="background: #29377d; height:50px;">


                                      </td></tr>

                                              <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;">

                                      </td></tr>

                                      <tr><td class="column-one" >

                                      <table class="column"> <tr>
                                        <td valign="top" style="padding: 16px 0 0px 0;">

                                      <center>

                                        <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >

                                      </center>

                                        </td></tr></table>



                                      </td></tr>



                                              <tr>

                                                <td class="column-one" >

                                      <table align="center" class="column"> <tr><td valign="top" >

                                <div style="margin:0 auto;  max-width:500px; display:block;">

                                        <div style="width:110px; float:left; ">      <img style="border: 0px;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/char21.png" ></div>


                                        <div  style="">

                              <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px; margin: 0px; text-align: center;font-weight: 500;"> Hi,' . ($name . ' ' . auth()->user()->lname ?? '') . '

                                                    <br>

                                                  </h3>

                                                  <p style="color: #29377d;   font-family: Arial Narrow;font-style: italic; font-size:165%;  margin: 13px 8px 13px 8px; text-align: center;">Thank you for your purchase <br>
                                                  and donations </p>

                                                  <h3 style="color: #29377d; font-family: Arial Narrow;  font-style: italic;font-size: 195%; margin: 0px; text-align: center;">Ticket ID #' . $ottype . '' . $ticketnumber . '

                                                    <br>

                                                  </h3></div>

                                      </div>

                                        </td></tr></table>
                                    </td></tr>

                              <tr>

                                                <td class="column-one" >

                                      <table align="center" class="column"> <tr>

                                        <td valign="top" >

                                <table style="margin: auto; border-collapse: collapse;border: 1px; width:95%; max-width:500px;" border="1" cellspacing="2" cellpadding="0">

                                            <tbody>

                                              <tr>

                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:28%" align="center" bgcolor="#d0dbe7"><strong>Products</strong></th>

                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:12%" align="center" bgcolor="#d0dbe7"><strong>Lines</strong></th>

                                                <th style="padding: 12px 5px;color: #354169;font-size:20px;font-family: Arial Narrow; width:35%" align="center" bgcolor="#d0dbe7"><strong>My

                                                  <span><img align="center" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/three.png"></span>Numbers </strong></th>

                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:25%" align="center" bgcolor="#d0dbe7"><strong>Raffle ID</strong></th>

                                              </tr>';


              // $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber'  and `type` = '$ottype' and `ticket_id`!='' and `deletes` = '0' group by `product_id` order by `product_id` ASC  ", "", "");

              // $query = DB::table('ticket_lines')
              //   ->where('ticket_id', $ticketnumber)
              //   ->where('type', $ottype)
              //   ->where('ticket_id', '!=', '')
              //   ->where('deletes', '0')
              //   ->orderBy('product_id', 'ASC')
              //   ->get();

              $query = DB::table('ticket_lines')
                ->select('product_id')
                ->where('deletes', '0')
                ->where('type', $ottype)
                ->where('ticket_id', $ticketnumber)
                ->groupBy('product_id')
                ->orderBy('product_id', 'asc')
                ->get();


              foreach ($query as $key => $valuelist) {

                $p_id = $valuelist->product_id;

                // $t_id = $valuelist->orders;

                // $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");

                // foreach ($product['result'] as $key => $productinfo) {
                // }
                $product = DB::table('product')
                  ->where('id', $valuelist->product_id)
                  ->first();

                // $pcountlist = select_query_count($con, "ticket_lines", "id", "`product_id`='$valuelist[product_id]' and  `type` = '$ottype' and  `orders`='$valuelist[orders]' and `deletes` = '0'", "", "");

                // $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and  `type` = '$ottype' and `deletes` = '0'", "", "");

                $mynumber = DB::table('ticket_lines')
                  ->where('ticket_id', $ticketnumber)
                  ->where('product_id', $p_id)
                  ->where('type', $ottype)
                  ->where('deletes', '0')
                  ->get();



                $messages .= '<tr><td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">AED ' . number_format((float) $product->rate, 2, '.', '') . '</strong></td>';

                $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' . $mynumber->count() . '</strong></td>';

                $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';

                foreach ($mynumber as $key => $mynumber1) {

                  $messages .= $mynumber1->my3number . "<br>";
                }

                $messages .= '</strong></td>';

                $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';

                foreach ($mynumber as $key => $mynumber1) {

                  $messages .= $mynumber1->raffle_id . "<br>";
                }

                $messages .= '</strong></td>';

                $messages .= '</tr>';
              }

              $messages .= ' </tbody>

                                          </table>
                                          <br>



                                            <table style="margin: auto; color: #000000; font-size: medium; background-color: #fbfbfb;  border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                                    <tbody>

                                      <tr>

                                        <td style="color: #111111; padding: 15px 14px 23px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;" align="center" valign="top" bgcolor="#ffffff">

                                          <h3 style="color: #29377d;  font-size: 30px; margin: 0px;font-style: italic;font-family: Arial Narrow;">Total Amount:

                                            <span class="gmail-otp-bg" style="color: #be1e2d;font-style: italic;font-family: Arial Narrow;">AED ' . number_format((float) $finaltotal, 2, '.', '') . '</span>

                                            <br>

                                          </h3>

                                        </td>

                                      </tr>

                                    </tbody>

                                  </table>
                                  <br>

                                  <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb;  border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                                    <tbody>

                                      <tr>

                                        <td style="padding: 0px 10px 0px 0px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px; width:175px;" align="center" valign="top" bgcolor="#ffffff">

                                          <h3 style="color: #ffffff;  font-size: 22px; margin: 0px; padding: 8px 13px 10px 14px; background: #29377d;; line-height: 1; border-radius: 5px;">

                                            <a href="' . ($request->header('Origin') . '/') . 'ticket-view/' . $transaction_id . '" style="color: #ffffff; text-decoration-line: none;font-style: italic;font-family: Arial Narrow;">View Ticket</a>

                                          </h3>

                                        </td>

                                        <td style="padding: 0px 0px 0px 30px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:175px;" align="center" valign="top" bgcolor="#ffffff">

                                          <h3 style="color: #ffffff; font-size: 22px; margin: 0px;  padding: 8px 13px 10px 14px; background: #ffffff;color: #29377d;;  line-height: 1; border-radius: 5px;border: 1px solid;">

                                            <a href="' . ($request->header('Origin') . '/') . 'invoice/' . $transaction_id . '" style="color: #29377d; text-decoration-line: none;font-style: italic;font-family: Arial Narrow;">View Invoice</a>

                                          </h3>

                                        </td>

                                      </tr>

                                    </tbody>

                                  </table>

                                  <table style=" width: 100%;margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                                    <tbody>

                                      <tr>

                                      <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                                      <p style="color: #29377d !important;  font-size:147%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Watch Just3 ' . (isset($drawData->data->active->drawfreq) && $drawData->data->active->drawfreq === 4 ? 'Daily Draw results <br> every Monday to Friday ' : 'Tri-Daily Draw results every (Monday,Wednesday,Friday) ')  . $drawData->data->active->resultDatetime . ' UAE Time ' . ((isset($drawData->data->activeSuperRaffle) && $drawData->data->activeSuperRaffle != '' && isset($drawData->data->activeSuperRaffle->drawResultDate)) ? ', Super Raffle Draw on ' . date("dS F Y", strtotime($drawData->data->activeSuperRaffle->drawResultDate)) . ' ' : ' ')   . (!Controller::checkGrandRaffleEligible($drawData->data->active->draw_no) ?  ('& <br>Grand Raffle Draw result on ' . Controller::raffleDrawDate('dS F Y')) : '') . '</p>

                                    </td>

                                      </tr>


                                        <tr>

                                        <td class="gmail-line" style="box-sizing: border-box; width: 8px;padding: 0;">

                                          <img  style="width:500px !important;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/final_img.png">

                                        </td>

                                      </tr>

                                    </tbody>

                                  </table>


                                 
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

              $emailchack = explode('@', $email);
              if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                // $emailsend = sendemail($con, $email, $subject, $messages, 'tickets');

                $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $messages);
              }



              // Log
              $log = Controller::error_log_new($request->ip(), 'Ticket_genereated', $user_id, '', '', 'Ticket Genereted. ID: ' . $ticketnumber, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);




              $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
              $response = ['status' => 'success', 'message' => 'Cash Ticket Generated Successfully', 'data' => $data];
              goto returnFVI;
            } else {

              // Log
              $log = Controller::error_log_new($request->ip(), 'update_failed', $user_id, '', '', 'Start Wallet Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
              $response = ['status' => 'failed', 'message' => 'The Update Process Failed!', 'error' => 'The Update Process Failed!'];
              goto returnFVI;

              // Log
              // error_log_new($con, getUserIP(), 'wallet_balance_low', $checkwallet['result'][0]['id'], '', '', 'Start Wallet Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__)?, __LINE__, $dubaidate_time);
            }
          } else {

            // Log
            $log = Controller::error_log_new($request->ip(), 'wallet_balance_low', $user_id, '', '', 'Start Wallet Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
            $response = ['status' => 'failed', 'message' => 'Your Wallet Balance is low!', 'error' => 'Your Wallet Balance is low!'];
            goto returnFVI;

            // Log
            // error_log_new($con, getUserIP(), 'wallet_balance_low', $checkwallet['result'][0]['id'], '', '', 'Start Wallet Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__)?, __LINE__, $dubaidate_time);
          }
        } else {

          // // Log
          $log = Controller::error_log_new($request->ip(), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);



          $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
          $response = ['status' => 'success', 'message' => 'Cash Ticket Generated Successfully', 'data' => $data];
          goto returnFVI;
        }
      }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }










  public function bonusTicketGenerate(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();

      $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
      if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => 'Please use a valid transaction id!'];
        goto returnFVI;
      }

      $transaction_id = $request->transaction_id;
      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Login Required!'];
        goto returnFVI;
      }

      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => 'Transaction ID Required!'];
        goto returnFVI;
      }

      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }

      $payment_history = DB::table('payment_history')
        ->where('transaction_id', $transaction_id)
        ->where('status', '0')
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();


      if ($payment_history->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Missing!', 'error' => 'Transaction ID Missing!'];
        goto returnFVI;
      }

      if ($payment_history[0]->verify_status == 'NO') {
        $response = ['status' => 'failed', 'message' => 'The verification failed!', 'error' => 'The verification failed!'];
        goto returnFVI;
      }

      // Log
      $log = Controller::error_log_new($request->ip(), 'start_wallet_ticket', $user_id, '', '', 'Start Wallet Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);








      // Log
      // error_log_new($con, getUserIP(), 'start_wallet_ticket', $_SESSION['cusid'], '', '', 'Start Wallet Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

      // if ($_SESSION['transaction_id'] == $_POST['wtran_id'] && $_SESSION['cusid'] != '') {
      // $transaction_id = $_REQUEST['wtran_id'];
      // $payment_his_id1 = select_query($con, "payment_history", "", "`transaction_id` = '$transaction_id' and `status` = '0'", "", "");
      // if ($payment_his_id1['nr'] > 0) {


      // $oticket = select_query($con, "bpticket", "", "`transaction_id` = '$transaction_id' and `deletes`='0' order by `id` DESC", "", "");

      $oticket = DB::table('bpticket')
        ->where('transaction_id', $transaction_id)
        ->where('deletes', '0')
        ->orderBy('id', 'DESC')
        ->get();

      if ($oticket->count() < 1) {
        $toid = $transaction_id;
        // if ($toid == $transaction_id) {
        $post = json_decode($payment_history[0]->checkout_response, true);

        // $draw_id = $payment_his_id1['result'][0]['draw_id'];

        // if ($draw_id != '') {

        // if ($post['item1'] > 0) {
        //   $tentotal = $post['item1'] * 10;
        // } else {
        //   $tentotal = "0";
        // }

        // if ($post['item2'] > 0) {
        //   $twentytotal = $post['item2'] * 20;
        // } else {
        //   $twentytotal = "0";
        // }

        // if ($post['item3'] > 0) {
        //   $thirtytotal = $post['item3'] * 50;
        // } else {
        //   $thirtytotal = "0";
        // }

        // if ($post['item4'] > 0) {
        //   $fourtytotal = $post['item4'] * 100;
        // } else {
        //   $fourtytotal = "0";
        // }

        // $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;
        // $totline = $post['item1'] + $post['item2'] + $post['item3'] + $post['item4'];

        $tentotal = isset($post['item1']) && $post['item1'] > 0 ? $post['item1'] * 10 : 0;
        $twentytotal = isset($post['item2']) && $post['item2'] > 0 ? $post['item2'] * 20 : 0;
        $thirtytotal = isset($post['item3']) && $post['item3'] > 0 ? $post['item3'] * 50 : 0;
        $fourtytotal = isset($post['item4']) && $post['item4'] > 0 ? $post['item4'] * 100 : 0;

        $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;
        $totline = ($post['item1'] ?? 0) + ($post['item2'] ?? 0) + ($post['item3'] ?? 0) + ($post['item4'] ?? 0);




        // $user_id = $_SESSION['cusid'];


        // $checkwallet = select_query($con, "user_register", "", "`id`='$user_id' and `id`!='' and `deletes`='0' and `status`='0' and `roll_id`='0' order by `id` DESC  LIMIT 1", "", "");

        // if ($checkwallet['nr'] > 0) {
        $mobile = auth()->user()->mobile;
        $name = auth()->user()->name;
        $email = auth()->user()->email;
        $bonus_points = auth()->user()->bonus_points;
        // $bfirstname =  auth()->user()->name;
        // $blastname = '';
        // $bemailid =  auth()->user()->email;





        // $baddress = trim(DB::connection()->getPdo()->quote(auth()->user()->address, "'"));
        // $bcity = trim(DB::connection()->getPdo()->quote(auth()->user()->residinglocation, "'"));
        // $bcountry = trim(DB::connection()->getPdo()->quote(auth()->user()->nationality, "'"));
        // }



        if (round($bonus_points) >= $finaltotal) {

          $nettotal = $bonus_points - $finaltotal;



          // $upArff = ["bonus_points" => $nettotal];
          // $ins_updathh = setupdate($con, "user_register", "`id`='$user_id'", $upArff, "", "", "");


          $upArff = ["bonus_points" => $nettotal];

          $ins_updathh = DB::table('user_register')
            ->where('id', $user_id)
            ->update($upArff);





          // $onepercen = ($finaltotal / 105);
          // $total_amount = number_format(($onepercen * 100), 2);
          // $tax_value = number_format(($finaltotal - $total_amount), 2);



          $onepercen = intval($finaltotal) / 105;

          $total_amount = (float) $onepercen * 100;

          $tax_value = intval($finaltotal) - floatval($total_amount);


          $Arr = [
            "draw_id" => $draw_id,
            "user_id" => $user_id,
            "total_lines" => $totline,
            "sale_from" => 1,
            "purchase_datetime" => now(),
            "net_total" => $finaltotal,
            "tax_percentage" => 5,
            "tax_value" => number_format($tax_value, 2, ".", ""),
            "total_amount" => number_format($total_amount, 2, ".", ""),
            "payment_by" => "BONUS",
            "transaction_id" => $transaction_id,
            "createdon" => now(),
            "agent_id" => 0,
            "ticket_no" => '',
            "invoice_no" => 0,
            "status" => 0,
            "payment_transaction_id" => '',
            "referral_id" => 0,
            "deletes" => 0,
            "delete_reason" => ''
          ];

          // $ticket = insert($con, "bpticket", "", $Arr, "", "", "");
          $ticket = DB::table('bpticket')->insertGetId($Arr);



          // Log
          $log = Controller::error_log_new($request->ip(), 'ticket_created_wticket', $user_id, '', '', 'Ticket Insert Finsiehd', json_encode($ticket), __DIR__, basename(__FILE__), __LINE__);


          // Log
          // error_log_new($con, getUserIP(), 'ticket_created_wticket', $_SESSION['cusid'], '', '', 'Ticket Insert Finsiehd', json_encode($ticket), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

          $ottype = "BP";
          $ticketnumber = $ticket;
          $ticketid = $ottype . sprintf("%04d", $ticket);




          $invoiceid = '';


          // Log
          $log = Controller::error_log_new($request->ip(), 'invoice_created_wticket', $user_id, '', '', 'Invoice Generated', json_encode($invoiceid), __DIR__, basename(__FILE__), __LINE__);




          // Log
          // error_log_new($con, getUserIP(), 'invoice_created_wticket', $_SESSION['cusid'], '', '', 'Invoice Generated', json_encode($invoiceid), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

          ///////////////////////////////  POINT TRANSACTION ////////////////////////

          $tobalancepoint = $bonus_points + $finaltotal;
          $topoints = $bonus_points - $finaltotal;



          /// Insert from the bonus transaction track on cb_transaction

          $Arr1 = [
            'userid' => $user_id,
            'uname' => $name,
            'umobile' => $mobile,
            'uemail' => $email,
            'opening_balance' => $bonus_points,
            'total' => $finaltotal,
            'closeing_balance' => $topoints,
            'point_type' => 'BONUS',
            'transaction_type' => 'DEBIT',
            'reward_type' => 'PURCHASE',
            'card_no' => '',
            'reference_id' => $ticket,
            'reference_table' => 'bpticket',
            'ip' => request()->ip(),
            'device' => '',
            'deletes' => '0',
            'status' => '0',
            'createdon' => now(),
            'updatedon' => now()
          ];
          // $cb_trans_ins = insert($con, "cb_transactions", "", $Arr1, "", "", "");


          $cb_trans_ins = DB::table('cb_transactions')->insert($Arr1);



          ///////////////////////////////  POINT TRANSACTION ////////////////////////

          $Arr2 = ["ticket_no" => $ticketid, "invoice_no" => $invoiceid];
          // $ins_update = setupdate($con, "bpticket", "`id`='$ticket[id]'", $Arr2, "", "", "");
          $ins_update = DB::table('bpticket')
            ->where('id', $ticketnumber) // Assuming you have the 'id' value in the $ticket array
            ->update($Arr2);



          $pt = "4";
          $ot = 1;
          $k = "";
          for ($x = 1; $x <= $pt; $x++) {
            $tval = "item" . $x;
            // if ($post[$tval] > 0) {
            if (isset($post[$tval]) && $post[$tval] > 0) {
              $k++;
              // $product = select_query($con, "product", "", "`id`='$x'  ", "", "");
              // foreach ($product['result'] as $key => $productinfolist) {
              // }
              $product = DB::table('product')
                ->where('id', $x)
                ->first();
              if ($x == "1") {
                $rowvalue = "a";
              } else if ($x == "2") {
                $rowvalue = "b";
              } else if ($x == "3") {
                $rowvalue = "c";
              } else if ($x == "4") {
                $rowvalue = "d";
              } else {
                $rowvalue = "";
              }

              $numlines = $post[$tval];
              for ($g = 1; $g <= $numlines; $g++) {
                $checkline = "itemid_row" . $rowvalue . "_" . $g;
                if ($post[$checkline] != "") {
                  $my3number = "";
                  $linedate = "";
                  $my3number = $post[$checkline];
                  $raffle_id = $ottype . $ticketnumber . str_pad($ot, 2, "0", STR_PAD_LEFT);




                  // $linedate = ["my3number" => $my3number, "user_id" => $user_id, "ticket_id" => $ticket['id'], "draw_id" => $draw_id, "agent_id" => 0, "product_id" => $x, "orders" => $ticketid, "orders" => $ticket['id'], "raffle_id" => $raffle_id, "type" => $ottype, "invoice_no" => $invoiceid, "createdon" => $dubaidate_time];
                  // $lines = insert($con, "ticket_lines", "", $linedate, "", "", "");


                  $linedate = [
                    "my3number" => $my3number,
                    "user_id" => $user_id,
                    "ticket_id" => $ticketnumber,
                    "draw_id" => $draw_id,
                    "agent_id" => 0,
                    "product_id" => $x,
                    "orders" => $ticketnumber,
                    "raffle_id" => $raffle_id,
                    "type" => $ottype,
                    "invoice_no" => $invoiceid,
                    "createdon" => now(),
                    'deletes' => '0'
                  ];

                  $lines =  DB::table('ticket_lines')->insert($linedate);

                  $ot++;
                }
              }
            }
          }




          $draw_arr = ["status" => '1', "pay_re_status" => 'BONUS', "gateway" => "BONUSPOINT", "draw_id" => $draw_id];

          // $Inv_update = update($con, "payment_history", "`transaction_id` = '$transaction_id' and `status` = '0'", $draw_arr, "", "", "", "");
          // $errors = $Inv_update['errors'];
          // if ($errors != "") {
          //   $result["type"] = "0";
          //   $result["result"] = $errors;
          // } else {

          $Inv_update =   DB::table('payment_history')
            ->where('id', $payment_history[0]->id)
            ->where('transaction_id', $transaction_id)
            ->where('status', '0')
            ->update($draw_arr);
          if ($Inv_update) {


            // $printurlf = ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id;
            // $printinvoice = ($request->header('Origin') . '/') . "invoice/" . $transaction_id;
            // $printurl = get_tiny_url($printurlf);
            // $invoiceurl = get_tiny_url($printinvoice);



            // // Log
            // error_log_new($con, getUserIP(), 'Lines_insert_finished_wt', $_SESSION['cusid'], '', '', 'Ticket Lines Generated', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

            // $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticket[id]' and `ticket_id`!='' and `deletes` = '0' group by `product_id` order by `product_id` ASC  ", "", "");

            // foreach ($query['result'] as $key => $valuelist) {

            //   $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");

            //   foreach ($product['result'] as $key => $productinfo) {
            //   }

            //   $pcountlist = select_query_count($con, "ticket_lines", "id", "`product_id`='$valuelist[product_id]' and `ticket_id`='$valuelist[ticket_id]' and `deletes` = '0'", "", "");

            //   $lines = "";

            //   $mynumber = select_query($con, "ticket_lines", "", "`product_id`='$valuelist[product_id]' and `ticket_id`='$valuelist[ticket_id]' and `deletes` = '0'", "", "");

            //   foreach ($mynumber['result'] as $key => $mynumber1) {

            //     $lines .= $mynumber1['my3number'] . ",";
            //   }

            //   $sms1 = " CAT-AED " . round($productinfo['rate']) . " Selected No: " . $lines;

            //   $finalsms .= $sms1;
            // }

            // if (substr($mobile, 0, 3) == "971") {

            //   $messages1 = 'Ticket ID #' . $ticketid . '.';

            //   $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `type` = '$ottype' and `ticket_id`!='' and `deletes` = '0' group by `product_id` order by `product_id` ASC  ", "", "");

            //   foreach ($query['result'] as $key => $valuelist) {

            //     $p_id = $valuelist['product_id'];

            //     $t_id = $valuelist['orders'];

            //     $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");

            //     $messages1 .= 'CAT-AED ' . round($product['result'][0]['rate']) . '. ';

            //     $rcount = count($mynumber['result']);

            //     $io = 1;

            //     $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and `type` = '$ottype' and `deletes` = '0'", "", "");

            //     foreach ($mynumber['result'] as $key => $mynumber1) {

            //       $messages1 .= $mynumber1['my3number'];

            //       if ($io == $rcount) {

            //         $messages1 .= '. ';
            //       } else {

            //         $messages1 .= ', ';
            //       }

            //       $io++;
            //     }
            //   }

            //   $messages1 .= 'for an Amounts of AED ' . number_format((float) $finaltotal, 2, '.', '') . ' for more info. ( ' . $printurl . ' ),. TC apply. Good Luck!!!';

            //   $templateid = "";

            //   // sendsms($con, $mobile, $messages1, $templateid);
            // }

            // New Sms Format End

            // New SMS Format Start
            // dd($Inv_update);
            $subject = "Purchase Confirmation -" . date("d-m-Y g:i a");

            $messages = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

             <html xmlns="http://www.w3.org/1999/xhtml">

             <head>

              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

              <meta http-equiv="X-UA-Compatible" content="IE=edge" />

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <title>Ticket Purchase OTP Mail Template</title>

              <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=5O6l1hEmHZsgGLYImPLML-rBMyI4Q8GN8YjYdFo4c0XmPYvIzOvx5moqnWZT0UiZJxpymRSsqokCDJ_gUYroCvpzhEaDf4RK6OHPRsBDHUualz5RipR0SHuUO-Mmu6nT" charset="UTF-8"></script><style type="text/css">



                @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

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

                      <table class="column"> <tr>
                        <td valign="top" style="padding: 16px 0 0px 0;">

                      <center>

                        <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >

                      </center>

                        </td></tr></table>



                      </td></tr>

                      <!-- LOGO  -->

                              <tr>

                                <td class="column-one" >

                      <table align="center" class="column"> <tr><td valign="top" >

               <div style="margin:0 auto;  max-width:500px; display:block;">

                       <div style="width:110px; float:left; ">      <img style="border: 0px;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/mantoy1.png" ></div>


                       <div  style="">

              <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px; margin: 0px; text-align: center;font-weight: 500;">Hi, ' . ($name . ' ' . auth()->user()->lname ?? '') . '

                                    <br>

                                  </h3>



                                  <strong><p style="color: #29377d;   font-family: Arial Narrow;font-style: italic; font-size:165%;  margin: 13px 8px 13px 8px; text-align: center;">Your <span style="color: #be1e2d;">Bonus Ticket</span> Details <br>

                                  are Below</p>
                                </strong>
                                  <h3 style="color: #29377d; font-family: Arial Narrow;  font-style: italic;font-size: 195%; margin: 0px; text-align: center;">Ticket ID #' . $ottype . '' . $ticketnumber . '

                                    <br>

                                  </h3></div>



                      </div>

                        </td></tr></table>



                      </td></tr>



              <tr>

                                <td class="column-one" >

                      <table align="center" class="column"> <tr>

                        <td valign="top" >

               <table style="margin: auto; border-collapse: collapse;border: 1px; width:95%; max-width:500px;" border="1" cellspacing="2" cellpadding="0">

                            <tbody>

                              <tr>

                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:28%" align="center" bgcolor="#d0dbe7"><strong>Products</strong></th>

                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:12%" align="center" bgcolor="#d0dbe7"><strong>Lines</strong></th>

                                <th style="padding: 12px 5px;color: #354169;font-size:20px;font-family: Arial Narrow; width:35%" align="center" bgcolor="#d0dbe7"><strong>My

                                  <span><img align="center" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/three.png"></span>Numbers </strong></th>

                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:25%" align="center" bgcolor="#d0dbe7"><strong>Raffle ID</strong></th>

                              </tr>

                              <tr>';



            $query = DB::table('ticket_lines')
              ->select('product_id')
              ->where('deletes', '0')
              ->where('type', $ottype)
              ->where('ticket_id', $ticketnumber)
              ->groupBy('product_id')
              ->orderBy('product_id', 'asc')
              ->get();

            // foreach ($query['result'] as $key => $valuelist) {
            foreach ($query as $key => $valuelist) {
              // $p_id = $valuelist['product_id'];
              // $t_id = $valuelist['orders'];

              $p_id = $valuelist->product_id;
              // $t_id = $valuelist->orders;



              $product = DB::table('product')
                ->where('id',  $valuelist->product_id)
                ->first();

              // $pcountlist = select_query_count($con, "ticket_lines", "id", "`product_id`='$valuelist[product_id]' and  `type` = '$ottype' and  `orders`='$valuelist[orders]' and `deletes` = '0'", "", "");
              // $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and  `type` = '$ottype' and `deletes` = '0'", "", "");


              $mynumber = DB::table('ticket_lines')
                ->where('ticket_id', $ticketnumber)
                ->where('product_id', $p_id)
                ->where('type', $ottype)
                ->where('deletes', '0')
                ->get();



              $messages .= '<tr><td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">AED ' . number_format((float) $product->rate, 2, '.', '') . '</strong></td>';

              $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' . $mynumber->count() . '</strong></td>';

              $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';

              // foreach ($mynumber['result'] as $key => $mynumber1) {
              foreach ($mynumber as $key => $mynumber1) {
                $messages .=  $mynumber1->my3number   . "<br>";
              }

              $messages .= '</strong></td>';
              $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';

              // foreach ($mynumber['result'] as $key => $mynumber1) {
              foreach ($mynumber as $key => $mynumber1) {
                $messages .= $mynumber1->raffle_id . "<br>";
              }

              $messages .= '</strong></td>';
              $messages .= '</tr>';
            }

            $messages .= '</tbody>

                          </table>
                          <br>



                           <table style="margin: auto; color: #000000; font-size: medium; background-color: #fbfbfb;  border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                    <tbody>

                      <tr>

                        <td style="color: #111111; padding: 15px 14px 23px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;" align="center" valign="top" bgcolor="#ffffff">

                          <h3 style="color: #29377d;  font-size: 30px; margin: 0px;font-style: italic;font-family: Arial Narrow;">Total Amount:

                            <span class="gmail-otp-bg" style="color: #be1e2d;font-style: italic;font-family: Arial Narrow;">AED ' . number_format((float) $finaltotal, 2, '.', '') . '</span>

                            <br>

                          </h3>

                        </td>

                      </tr>

                    </tbody>

                  </table>
                  <br>

                  <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb;  border-collapse: collapse;" border="0"  cellspacing="0" cellpadding="0">

                    <tbody>

                      <tr>

                        <td style="padding: 0px 10px 0px 0px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px; width:175px;" align="center" valign="top" bgcolor="#ffffff">

                          <h3 style="color: #ffffff;  font-size: 22px; margin: 0px; padding: 8px 13px 10px 14px; background: #29377d; line-height: 1; border-radius: 5px;">

                            <a href="' . ($request->header('Origin') . '/') . 'ticket-view/' . $transaction_id . '" style="color: #ffffff; text-decoration-line: none;font-style: italic;font-family: Arial Narrow;">View Ticket</a>

                          </h3>

                        </td>


                      </tr>

                    </tbody>

                  </table>

                  <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

                    <tbody>

                      <tr>

                        <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                         <p style="color: #29377d;  font-size:147%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Watch Just3 ' . (isset($drawData->data->active->drawfreq) && $drawData->data->active->drawfreq === 4 ? 'Daily Draw results <br> every Monday to Friday ' : 'Tri-Daily Draw results every (Monday,Wednesday,Friday) ')  . $drawData->data->active->resultDatetime . ' UAE Time ' . ((isset($drawData->data->activeSuperRaffle) && $drawData->data->activeSuperRaffle != '' && isset($drawData->data->activeSuperRaffle->drawResultDate)) ? ', Super Raffle Draw on ' . date("dS F Y", strtotime($drawData->data->activeSuperRaffle->drawResultDate)) . ' ' : ' ')   . (!Controller::checkGrandRaffleEligible($drawData->data->active->draw_no) ?  ('& <br>Grand Raffle Draw result on ' . Controller::raffleDrawDate('dS F Y')) : '') . '</p>

                        </td>

                      </tr>


                        <tr>

                        <td class="gmail-line" style="box-sizing: border-box; width: 8px;padding: 0;">

                          <img  style="width:500px !important;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/final_img.png">

                        </td>

                      </tr>

                    </tbody>

                  </table>


                 

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

            $emailchack = explode('@', $email);

            if (strtolower($emailchack[1]) != "nationaldrawuae.com") {

              // $emailsend = sendemail($con, $email, $subject, $messages, 'tickets');

              $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $messages);
            }


            $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
            $response = ['status' => 'success', 'message' => 'Bonus Ticket Generated Successfully', 'data' => $data];
            goto returnFVI;
            // unset($_SESSION['merchantOrderReference']);
            // unset($_SESSION['makepayemntre']);
            // unset($_SESSION['MakepaymentState']);
            // unset($_SESSION['transaction_id']);
          } else {
            $response = ['status' => 'failed', 'message' => 'The Update Process Failed!', 'error' => 'The Update Process Failed!'];
            goto returnFVI;
          }

          // New SMS Format End

        } else {
          // Log
          // error_log_new($con, getUserIP(), 'wallet_balance_low', $checkwallet['result'][0]['id'], '', '', 'Start Wallet Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

          // Log
          $log = Controller::error_log_new($request->ip(), 'wallet_balance_low', $user_id, '', '', 'Start Wallet Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
          $response = ['status' => 'failed', 'message' => 'Your Wallet Balance is low!', 'error' => 'Your Wallet Balance is low!'];
          goto returnFVI;
        }

        // Log
        // error_log_new($con, getUserIP(), 'ticket_creation_Finished', $_SESSION['cusid'], '', '', 'Wallet Ticket Generated Successfully', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);


        // Log
        $log = Controller::error_log_new($request->ip(), 'ticket_creation_Finished', $user_id, '', '', 'Wallet Ticket Generated Successfully', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
        // $result['type'] = '1';
        // $result['result'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;

        $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
        $response = ['status' => 'success', 'message' => 'Bonus Ticket Generated Successfully', 'data' => $data];
        goto returnFVI;
        // }
        // }
      } else {

        // $result['type'] = '1';
        // $result['result'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;

        $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
        $response = ['status' => 'success', 'message' => 'Bonus Ticket Generated Successfully', 'data' => $data];
        goto returnFVI;
      }
      // }
      // } else {
      //   // unset($_SESSION['transaction_id']);
      //   // divert(($request->header('Origin') . '/') . 'billing');
      //   $result['type'] = '1';
      //   $result['result'] = ($request->header('Origin') . '/') . 'billing';
      //   goto returnFVI;
      // }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
