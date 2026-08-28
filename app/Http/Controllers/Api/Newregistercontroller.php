<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\drawsController;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Newregistercontroller extends Controller
{

  public function testee($transaction_id)
  {
    dd('frtg');
    try {
      dd('frtg');
      $paymentHistory = DB::table('payment_history')->where('transaction_id', $transaction_id)
        ->whereIn('pay_re_status', ['CAPTURED', 'Success', 'Shipped'])
        ->where('status', '0')
        ->orderBy('id', 'desc')
        ->first();

      if ($paymentHistory) {
        $gateway = $paymentHistory->gateway;
        $checkoutResponse = json_decode($paymentHistory->checkout_response, true);
        $finalTotalSs = (int)$paymentHistory->finaltotal;
        $post = $checkoutResponse;

        $tentotal = optional($post)['item1'] > 0 ? $post['item1'] * 10 : 0;
        $twentytotal = optional($post)['item2'] > 0 ? $post['item2'] * 20 : 0;
        $thirtytotal = optional($post)['item3'] > 0 ? $post['item3'] * 50 : 0;
        $fourtytotal = optional($post)['item4'] > 0 ? $post['item4'] * 100 : 0;

        $finalTotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;

        $response = json_decode($paymentHistory->response, true);

        if ($gateway == 'ccavenue') {
          $amount = (int)$response['amount'];

          if ($finalTotal === $amount && $finalTotalSs === $amount) {
            return true;
          }
        } else {
          $amount = (int)$response['amount']['value'] / 100;

          if ($finalTotal === $amount && $finalTotalSs === $amount) {
            return true;
          }
        }
      }

      return false;
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
















  public function onlineTicketGeneration(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();
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



      // Log
      $log = Controller::error_log_new($request->ip(), 'OnlineTicketGenerate_API', $user_id, '', '', 'Start Ticket Generation',  json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

      // dd($log);



      if ($this->checkAmount($transaction_id)) {


        // $payment_history = select_query($con, "payment_history", "", "`transaction_id` = '$transaction_id' and (`pay_re_status` = 'CAPTURED' OR `pay_re_status` = 'Success' OR `pay_re_status` = 'Shipped') and `status` = '0' AND `category` = 'PRODUCT' ORDER BY `id` DESC LIMIT 1", "", "");
        $payment_history = DB::table('payment_history')
          ->select('*')
          ->where('transaction_id', $transaction_id)
          ->whereIn('pay_re_status', ['CAPTURED', 'Success', 'Shipped'])
          ->where('status', '0')
          ->where('category', 'PRODUCT')
          ->orderBy('id', 'DESC')
          ->limit(1)
          ->get();



        if ($payment_history->count() > 0) {



          if ($draw_id != '' && $draw_id != 0) {






            // $oticket = select_query($con, "ticket", "", "`transaction_id` = '$transaction_id' and `deletes`='0' order by `id` DESC", "", "");
            $oticket = DB::table('ticket')
              ->where('transaction_id', $transaction_id)
              ->where('deletes', '0')
              ->orderBy('id', 'DESC')
              ->limit(1)
              ->get();

            // dd($oticket);

            if ($oticket->count() < 1) {

              // Log
              // error_log_new($con, getUserIP(), 'strat_ticket_generate',  $_SESSION['cusid'], '', '', 'Start Ticket Generation', json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

              // Log
              $log = Controller::error_log_new($request->ip(), 'strat_ticket_generate', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);



              $response = json_decode($payment_history[0]->response, true);
              $user_id = $payment_history[0]->user_id;
              $checkout_response = json_decode($payment_history[0]->checkout_response, true);

              $orderReference = $payment_history[0]->reference;
              $transaction_id = $payment_history[0]->transaction_id;


              // Log
              // error_log_new($con, getUserIP(), 'strated',  $_SESSION['cusid'], '', '', 'Draw ID:' . $draw_id, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

              // Log
              $log = Controller::error_log_new($request->ip(), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id,  json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);


              // $user_register_get = select_query($con, "user_register", "", "`id`='$user_id' and `id`!='' and `deletes`='0' and `status`='0' and `roll_id`='0' order by `id` DESC LIMIT 1", "", "");


              $name = auth()->user()->name;
              $mobile = auth()->user()->mobile;
              $email = auth()->user()->email;
              $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
              $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
              $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->nationality), "'");

              // dd($address);


              // $date = date("Y-m-d H:i:s");
              // $count = intval($checkout_response['item1']) + intval($checkout_response['item2']) + intval($checkout_response['item3']) + intval($checkout_response['item4']);
              $count = intval($checkout_response['item1'] ?? 0)
                + intval($checkout_response['item2'] ?? 0)
                + intval($checkout_response['item3'] ?? 0)
                + intval($checkout_response['item4'] ?? 0);


              $post = $checkout_response;


              // if (isset($post['item1']) && $post['item1'] > 0) {
              //   $tentotal = $post['item1'] * 10;
              // } else {
              //   $tentotal = "0";
              // }

              // if (isset($post['item2']) && $post['item2'] > 0) {
              //   $twentytotal = $post['item2'] * 20;
              // } else {
              //   $twentytotal = "0";
              // }

              // if (isset($post['item3']) && $post['item3'] > 0) {
              //   $thirtytotal = $post['item3'] * 50;
              // } else {
              //   $thirtytotal = "0";
              // }

              // if (isset($post['item4']) && $post['item4'] > 0) {
              //   $fourtytotal = $post['item4'] * 100;
              // } else {
              //   $fourtytotal = "0";
              // }

              // $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;
              // $totline = $post['item1'] + $post['item2'] + $post['item3'] + $post['item4'];

              $tentotal = optional($post)['item1'] > 0 ? $post['item1'] * 10 : 0;
              $twentytotal = optional($post)['item2'] > 0 ? $post['item2'] * 20 : 0;
              $thirtytotal = optional($post)['item3'] > 0 ? $post['item3'] * 50 : 0;
              $fourtytotal = optional($post)['item4'] > 0 ? $post['item4'] * 100 : 0;

              $finaltotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;

              $totline = optional($post)['item1'] ?? 0 +
                optional($post)['item2'] ?? 0 +
                optional($post)['item3'] ?? 0 +
                optional($post)['item4'] ?? 0;


              $onepercen = intval($finaltotal) / 105;
              $total_amount = floatval($onepercen) * 100;
              $tax_value = intval($finaltotal) - floatval($total_amount);

              // dd($tax_value);
              // $datetime = date("Y-m-d H:i:s");

              // $Arr = [
              //   "draw_id" => $draw_id,
              //   "user_id" => $user_id,
              //   "total_lines" => $totline,
              //   "sale_from" => 1,
              //   "purchase_datetime" => date("Y-m-d H:i:s"),
              //   "net_total" => $finaltotal,
              //   "tax_percentage" => 5,
              //   "	tax_value" => $tax_value,
              //   "total_amount" => $total_amount,
              //   "payment_by" => "Card",
              //   "transaction_id" => $transaction_id,
              //   "createdon" => date("Y-m-d H:i:s")
              // ];

              // $ticket = insert($con, "ticket", "", $Arr, "", "", "");

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
                "payment_by" => "Card",
                "transaction_id" => $transaction_id,
                "createdon" => now(),
                "agent_id" => 0,
                "ticket_no" => '',
                "invoice_no" => 0,
                "status" => 0,
                "payment_transaction_id" => '',
                "referral_id" => 0,
                "deletes" => 0
              ];

              $ticket = DB::table('ticket')->insertGetId($Arr);

              if ($ticket != ''  && $ticket > 0) {






                $ottype = "OT";
                $ticketnumber = $ticket;
                $ticketid = "OT" . sprintf("%04d", $ticketnumber);

                // $Arr1 = array("ticket_id" => $ticket['id'], "type" => "OT", "firstname" => $name, "lastname" => '', "emailid" => $email, "address" => $address, "city" =>  $city, "country" => $nationality, "response" =>  $_SESSION['billing']['response']);
                // $invoice = insert($con, "invoice", "", $Arr1, "", "", "");

                $Arr1 = [
                  "ticket_id" => $ticket,
                  "type" => "OT",
                  "firstname" => $name,
                  "lastname" => '',
                  "emailid" => $email,
                  "address" => $address,
                  "city" => $city,
                  "country" => $nationality,
                  "response" => json_encode($response),
                ];

                $invoiceid =   DB::table('invoice')->insertGetId($Arr1);

                // $invoiceid = $invoice['id'];



                ///////////////////////////////  POINT TRANSACTION ////////////////////////

                // $nowpoints = select_top_name($con, "ldbank", "points", "`deletes`='0' and `id`='9999999'  ", "points", "");
                $nowpoints = DB::table('ldbank')
                  ->where('deletes', '0')
                  ->where('id', 9999999)
                  ->value('points');

                $balancepoint = $nowpoints - $finaltotal;

                // $topoints = select_top_name($con, "user_register", "t_point", "`deletes`='0' and `status`='0'  and `id`!='' and `id`='$user_id'  ", "t_point", "");
                $topoints = DB::table('user_register')
                  ->where('deletes', '0')
                  ->where('status', '0')
                  ->where('id', '!=', '')
                  ->where('id', $user_id)
                  ->value('t_point');


                // NEW //
                // $t_earning = select_top_name($con, "user_register", "t_earning", "`deletes`='0' and `status`='0'  and `id`!='' and `id`='$user_id'  ", "t_earning", "");
                $t_earning = DB::table('user_register')
                  ->where('deletes', '0')
                  ->where('status', '0')
                  ->where('id', '!=', '')
                  ->where('id', $user_id)
                  ->value('t_earning');



                $topoints = $topoints + $t_earning;

                $tobalancepoint = $topoints + $finaltotal;

                // $Arrd = array("from_id" => "9999999", "type" => "credit", "points" => $finaltotal, "from_opening" => $nowpoints, "from_closing" => $balancepoint, "to_id" => $user_id, "to_opening" => $topoints, "to_closing" => $tobalancepoint, "invoice_id" => $invoiceid, "createdon" => $dubaidate_time);
                // $invoice = insert($con, "points_transaction", "", $Arrd, "", "", "");


                $Arrd = [
                  "from_id" => 9999999,
                  "type" => "credit",
                  "points" => $finaltotal,
                  "from_opening" => $nowpoints,
                  "from_closing" => $balancepoint,
                  "to_id" => $user_id,
                  "to_opening" => $topoints,
                  "to_closing" => $tobalancepoint,
                  "invoice_id" => $invoiceid,
                  "createdon" => now(),
                ];

                $invoice =   DB::table('points_transaction')->insert($Arrd);



                // $Arrd = array("from_id" => "$user_id", "type" => "order", "points" => $finaltotal, "from_opening" => $tobalancepoint, "from_closing" => $topoints, "to_id" => "9999999", "to_opening" => $balancepoint, "to_closing" => $nowpoints, "invoice_id" => $ticket['id'], "createdon" => $dubaidate_time);
                // $invoice = insert($con, "points_transaction", "", $Arrd, "", "", "");

                $Arrd = [
                  "from_id" => $user_id,
                  "type" => "order",
                  "points" => $finaltotal,
                  "from_opening" => $tobalancepoint,
                  "from_closing" => $topoints,
                  "to_id" => 9999999,
                  "to_opening" => $balancepoint,
                  "to_closing" => $nowpoints,
                  "invoice_id" =>  $ticket,
                  "createdon" => now(),
                ];

                $invoice = DB::table('points_transaction')->insert($Arrd);


                ///////////////////////////////  POINT TRANSACTION ////////////////////////

                // $Arr2 = array("ticket_no" => $ticketid, "invoice_no" => $invoiceid);
                // $ins_update = setupdate($con, "ticket", "`id`='$ticket[id]'", $Arr2, "", "", "");
                $Arr2 = [
                  "ticket_no" => $ticketid,
                  "invoice_no" => $invoiceid,
                ];

                $ins_update = DB::table('ticket')
                  ->where('id', $ticket)
                  ->update($Arr2);





                $pt = "4";

                $ot = 1;

                $k = "";

                for ($x = 1; $x <= $pt; $x++) {



                  $tval = "item" . $x;

                  $post[$tval] = $post[$tval] ?? '';

                  // if ($post[$tval] > 0) {
                  if (isset($post[$tval]) && $post[$tval] != '' && $post[$tval] != null && $post[$tval] != 'null' &&  $post[$tval] > 0) {

                    $k++;

                    // $product = select_query($con, "product", "", "`id`='$x'  ", "", "");

                    // foreach ($product['result'] as $key => $productinfolist) {
                    // }
                    $product = DB::table('product')
                      ->where('id', $x)
                      ->first();

                    // if ($x == "1") {

                    //   $rowvalue = "a";
                    // } else if ($x == "2") {

                    //   $rowvalue = "b";
                    // } else if ($x == "3") {

                    //   $rowvalue = "c";
                    // } else if ($x == "4") {

                    //   $rowvalue = "d";
                    // } else {

                    //   $rowvalue = "";
                    // }

                    // Define a mapping of values for $x to $rowvalue
                    $rowvalueMap = [
                      1 => 'a',
                      2 => 'b',
                      3 => 'c',
                      4 => 'd',
                    ];

                    // Set a default value for $rowvalue
                    $rowvalue = "";

                    // Check if $x is in the mapping
                    if (isset($rowvalueMap[$x])) {
                      $rowvalue = $rowvalueMap[$x];
                    }




                    $numlines = $post[$tval];

                    for ($g = 1; $g <= $numlines; $g++) {

                      $checkline = "itemid_row" . $rowvalue . "_" . $g;



                      if ($post[$checkline] != "") {



                        $my3number = "";

                        $linedate = "";

                        $my3number = $post[$checkline];



                        $raffle_id = 'OT' . $ticketnumber . str_pad($ot, 2, "0", STR_PAD_LEFT);



                        // $linedate = array("my3number" => $my3number, "user_id" => $user_id, "ticket_id" => $ticket['id'], "draw_id" => $draw_id, "agent_id" => 0, "product_id" => $x, "orders" => $ticketid, "orders" => $ticket['id'], "raffle_id" => $raffle_id, "type" => "OT", "invoice_no" => $invoiceid, "createdon" => $dubaidate_time);

                        // $lines = insert($con, "ticket_lines", "", $linedate, "", "", "");
                        $linedate = [
                          "my3number" => $my3number,
                          "user_id" => $user_id,
                          "ticket_id" => $ticket,
                          "draw_id" => $draw_id,
                          "agent_id" => 0,
                          "product_id" => $product->id,
                          "orders" => $ticket,
                          "raffle_id" => $raffle_id,
                          "type" => "OT",
                          "invoice_no" => $invoiceid,
                          "createdon" => now(),
                          'deletes' => '0'
                        ];

                        // Insert data into the "ticket_lines" table
                        $lines = DB::table('ticket_lines')->insert($linedate);

                        $ot++;
                      }
                    }
                  }
                }

                // dd($ins_update);

                // $draw_arr = array("status" => '1', "draw_id" => $draw_id);
                // $Inv_update = update($con, "payment_history", "`transaction_id` = '$transaction_id' and `status` = '0'", $draw_arr, "", "", "", "");

                $draw_arr = [
                  "status" => '1',
                  "draw_id" => $draw_id,
                ];

                // Update records in the "payment_history" table
                $Inv_update = DB::table('payment_history')
                  ->where('id', '=', $payment_history[0]->id)
                  ->where('status', '0')
                  ->update($draw_arr);

                // $errors = $Inv_update['errors'];

                if ($Inv_update) {
                  //   $result["type"] = "0";
                  //   $result["result"] = $errors;
                  // } else {


                  $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                  $printinvoice =  Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);



                  $printurl = $printurlf->getBody()->getContents();
                  $invoiceurl = $printinvoice->getBody()->getContents();




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
                              
                                        <div style="width:110px; float:left; ">      <img style="border: 0px;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/char21.png" ></div>
                              
                              
                                        <div  style="">
                              
                              <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px; margin: 0px; text-align: center;font-weight: 500;"> Hi,' . $name . ' 
                              
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

                  $query = DB::table('ticket_lines')
                    // ->selectRaw('*, MAX(id) as max_id') 
                    ->where('ticket_id', $ticket)
                    ->where('type', $ottype)
                    ->where('ticket_id', '!=', '')
                    ->where('deletes', '0')
                    // ->groupBy('product_id')
                    ->orderBy('product_id', 'ASC')
                    ->get();

                  foreach ($query as $key => $valuelist) {

                    $p_id = $valuelist->product_id;

                    $t_id = $valuelist->orders;

                    // $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");

                    $product = DB::table('product')
                      ->where('id',  $valuelist->product_id)
                      ->first();

                    // foreach ($product['result'] as $key => $productinfo) {
                    // }

                    // $pcountlist = select_query_count($con, "ticket_lines", "id", "`product_id`='$valuelist[product_id]' and  `type` = '$ottype' and  `orders`='$valuelist[orders]' and `deletes` = '0'", "", "");



                    // $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and  `type` = '$ottype' and `deletes` = '0'", "", "");
                    $mynumber = DB::table('ticket_lines')
                      ->where('ticket_id', $ticketnumber)
                      ->where('product_id', $p_id)
                      ->where('type', $ottype)
                      ->where('deletes', '0')
                      ->get();

                    $messages .= '<tr><td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">AED ' . number_format((float) $product->rate, 2, '.', '') . '</strong></td>';

                    $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' .  $mynumber->count() . '</strong></td>';

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



                  $messages .=   ' </tbody>
                              
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
                              
                                  </table> <!-- End Main Class -->
                              
                              
                              
                                </center> <!-- End Wrapper -->
                              
                              
                              
                              </body>
  
                              </html>';


                  $emailchack = explode('@', $email);
                  if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                    // $emailsend = sendemail($con, $email, $subject, $messages, 'tickets');

                    $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $messages, 'tickets');
                  }




                  $blid = $request->allow_id ?? '';

                  if ($blid != '') {
                    $paynid = $payment_history[0]->id;

                    $update_s = DB::table('allow_to_purchase')
                      ->where('id', $blid)
                      ->where('deletes', '0')
                      ->where('status', 'UNPAID')
                      ->update([
                        'status' => 'PAID',
                        'newpaymentid' => $payment_history[0]->id
                      ]);

                    // $update_s = mysqli_query($con, "UPDATE `allow_to_purchase` SET `status` = 'PAID', `newpaymentid` = '$paynid'  WHERE `allow_to_purchase`.`id` = $blid  AND `allow_to_purchase`.`deletes` = '0' AND `allow_to_purchase`.`status` = 'UNPAID';");
                    // unset($_SESSION['blid']);
                  }

                  //new paybylink status update




                  // $payupdate = select_query($con, "payby_link", "", "`Newpayment_id`='$status_update_id' AND `user_id` ='$_SESSION[cusid]' ORDER BY `id` DESC LIMIT 1 ", "", "");
                  $payupdate = DB::table('payby_link')
                    ->where('Newpayment_id', $payment_history[0]->id)
                    ->where('user_id', $user_id)
                    ->orderBy('id', 'desc')
                    ->limit(1)
                    ->get();

                  if ($payupdate->count() > 0) {

                    // $newpaybylink_update = array('status' => 'Paid');
                    // $newpaybylinkup = update($con, "payby_link", "`Newpayment_id` = '$payment_newid'", $newpaybylink_update, "", "", "", "");

                    $newpaybylinkupdate = DB::table('payby_link')
                      ->where('Newpayment_id', $payupdate[0]->Newpayment_id)
                      ->update(['status' => 'Paid']);
                  }


                  // unset($_SESSION['merchantOrderReference']);
                  // unset($_SESSION['makepayemntre']);
                  // unset($_SESSION['MakepaymentState']);

                  // Log
                  // error_log_new($con, getUserIP(), 'Ticket_genereated',  $_SESSION['cusid'], '', '', 'Ticket Genereted. ID: ' . $ticketnumber, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

                  // Log
                  $log = Controller::error_log_new($request->ip(), 'Ticket_genereated', $user_id, '', '', 'Ticket Genereted. ID: ' . $ticketnumber, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);



                  // divert($baseurl . 'thanks/' . $transaction_id);
                  $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                  $response = ['status' => 'success', 'message' => 'Network payment success', 'data' => $data];
                  goto returnFVI;
                } else {

                  $response = ['status' => 'failed', 'message' => 'The payment history update failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
                  goto returnFVI;
                }
              } else {

                $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
                goto returnFVI;
              }
            } else {
              // Log
              // error_log_new($con, getUserIP(), 'Ticket_already_genereated',  $_SESSION['cusid'], '', '', 'Ticket Already Generated' . $draw_id, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

              // divert($baseurl . 'thanks/' . $transaction_id);

              // Log
              $log = Controller::error_log_new($request->ip(), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

              $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
              $response = ['status' => 'failed', 'message' => 'This ticket has been generated', 'error' => $data];
              goto returnFVI;
            }
          } else {
            // Log
            // error_log_new($con, getUserIP(), 'draw_id_not_found',  $_SESSION['cusid'], '', '', 'Draw id Not found' . $draw_id, json_encode($_POST), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

            // Log
            $log = Controller::error_log_new($request->ip(), 'draw_id_not_found', $user_id, '', '', 'Draw id Not found' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'The amount validation failed!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/']];
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
