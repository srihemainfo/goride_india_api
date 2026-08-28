<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\drawsController;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class couponCode extends Controller
{






  public function checkAmount($transaction_id)
  {
    try {
      $paymentHistory = DB::table('payment_history')->where('transaction_id', $transaction_id)
        ->whereIn('pay_re_status', ['CAPTURED', 'Success', 'Shipped'])
        ->where('status', '0')
        ->orderBy('id', 'desc')
        ->first();

      if ($paymentHistory) {


        // if ($paymentHistory->verify_status == 'NO') {
        //   $response = ['status' => 'failed', 'message' => 'The verification failed!', 'error' => 'The verification failed!'];
        //   goto returnFVI;
        // }


        $gateway = $paymentHistory->gateway;
        $checkoutResponse = json_decode($paymentHistory->checkout_response, true);
        $finalTotalSs = (int) $paymentHistory->finaltotal;
        $post = $checkoutResponse;

        $tentotal = optional($post)['item1'] > 0 ? $post['item1'] * 10 : 0;
        $twentytotal = optional($post)['item2'] > 0 ? $post['item2'] * 20 : 0;
        $thirtytotal = optional($post)['item3'] > 0 ? $post['item3'] * 50 : 0;
        $fourtytotal = optional($post)['item4'] > 0 ? $post['item4'] * 100 : 0;

        $finalTotal = $tentotal + $twentytotal + $thirtytotal + $fourtytotal;

        $response = json_decode($paymentHistory->response, true);

        if ($gateway == 'ccavenue') {
          $amount = (int) $response['amount'];

          if ($finalTotal === $amount && $finalTotalSs === $amount) {
            return true;
          }
        } else {
          $amount = (int) $response['amount']['value'] / 100;

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












  public function couponCode(Request $request)
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
      $coupon = $request->coupon_code;

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


      if ($coupon == '' || $coupon == null || $coupon == 'null') {
        $response = ['status' => 'failed', 'message' => 'Kindly Enter the coupon code!', 'error' => 'Kindly Enter the coupon code!'];
        goto returnFVI;
      }

      $couponcode = DB::table('couponcode')
        ->where('c_code', '=', $coupon)
        ->where('deletes', '=', '0')
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();


      if ($couponcode->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The entered Coupon code is not valid', 'error' => 'The entered Coupon code is not valid'];
        goto returnFVI;
      }

      $status = (int)$couponcode[0]->c_type;
      if ($status != 0) {
        $response = ['status' => 'failed', 'message' => 'The Coupon code has expired, please proceed with another payment option!', 'error' => 'The Coupon code has expired, please proceed with another payment option!'];
        goto returnFVI;
      }

      $c_started_at = $couponcode[0]->c_started_at;
      if (strtotime($c_started_at) > strtotime(now())) {
        $response = ['status' => 'failed', 'message' => 'The entered Coupon code is not yet started, please try again the code after some time!', 'error' => 'The entered Coupon code is not yet started, please try again the code after some time!'];
        goto returnFVI;
      }

      $c_ended_at =  $couponcode[0]->c_ended_at;
      if (strtotime($c_ended_at) < strtotime(now())) {
        $response = ['status' => 'failed', 'message' => 'The Coupon code has expired, please proceed with another payment option!', 'error' => 'The Coupon code has expired, please proceed with another payment option!'];
        goto returnFVI;
      }

      if ($couponcode[0]->occurrence_type == 'single') {
        /// Event Based Validations
        $Ec_name = $couponcode[0]->c_name;
        $Namecouponcode = DB::table('couponcode')
          ->where('c_name', 'LIKE', $Ec_name)
          ->whereIn('user_id', [$user_id])
          ->where('deletes', '=', '0')
          ->where('occurrence_type', '=', 'single')
          ->orderBy('id', 'desc')
          ->limit(1)
          ->get();

        if ($Namecouponcode->count() > 0) {
          $response = ['status' => 'failed', 'message' => 'You have already participated in this event!', 'error' => 'You have already participated in this event!'];
          goto returnFVI;
        }
      }

      $c_type = $couponcode[0]->c_type;
      if ($c_type == 'Multiple') {



        $c_limit = (int) $couponcode[0]->c_limit;
        $c_use_count = (int)$couponcode[0]->c_use_count;




        if ($c_limit == $c_use_count) {
          $updateStatus = DB::table('couponcode')
            ->where('id', $couponcode[0]->id)
            ->where('status', '0')
            ->where('deletes', '0')
            ->update(['status' => '1']);

          // $update_status =  mysqli_query($con, "UPDATE `couponcode` SET `status` = '1' WHERE `id` = '$co_id' AND `status` = '0' AND `deletes` = '0';");

          $response = ['status' => 'failed', 'message' => 'The Coupon code has expired, please proceed with another payment option!', 'error' => 'The Coupon code has expired, please proceed with another payment option!'];
          goto returnFVI;
        }

        if ($c_use_count < $c_limit) {

          $productid = (int)$couponcode[0]->productid;
          $c_couponvalue = (int)$couponcode[0]->c_couponvalue;



          // $payment_history = select_query($con, "payment_history", "", "`transaction_id` = '$tran_id' and `status` = '0' ORDER BY `id` DESC LIMIT 1", "", "");
          $payment_history = DB::table('payment_history')
            ->where('transaction_id', $transaction_id)
            ->where('status', '0')
            ->orderBy('id', 'desc')
            ->limit(1)
            ->get();

          if ($payment_history->count() < 1) {
            $response = ['status' => 'failed', 'message' => 'Transaction Track not found!', 'error' => 'Transaction Track not found!'];
            goto returnFVI;
          }

          if ($payment_history[0]->verify_status == 'NO') {
            $response = ['status' => 'failed', 'message' => 'The verification failed!', 'error' => 'The verification failed!'];
            goto returnFVI;
          }


          $ticketAmt = $this->getCusSiteTotal($payment_history[0]->checkout_response);
          if ($ticketAmt > $c_couponvalue) {
            $response = ['status' => 'failed', 'message' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number", 'error' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number"];
            goto returnFVI;
          }

          if ($ticketAmt != $c_couponvalue) {


            $response = ['status' => 'failed', 'message' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number", 'error' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number"];
            goto returnFVI;
          }


          $post = json_decode($payment_history[0]->checkout_response, true);


          $coount_a = 0;

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

              $product = DB::table('product')->where('id', $x)->first();

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
                if (isset($post[$checkline]) &&  $post[$checkline] != "") {

                  $product_id = $x;
                  $my3number = "";
                  $linedate = "";
                  if ($product_id == $productid && $c_couponvalue == $product->rate) {
                    $coount_a++;
                  } else {
                    $response = ['status' => 'failed', 'message' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number", 'error' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number"];
                    goto returnFVI;
                  }

                  $ot++;
                }
              }
            }
          }


          if ($coount_a > 1) {
            $response = ['status' => 'failed', 'message' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number", 'error' => "The entered Coupon code is valid for the category of AED " . $c_couponvalue . " with single line of My3Number"];
            goto returnFVI;
          }



          $c_used_by = $couponcode[0]->c_used_by;
          if ($c_used_by == 'Purchased') {
            // $ticket_lines = select_query($con, "ticket_lines", "", "`user_id` = '$user_id' and `deletes` = '0' ORDER BY `id` DESC", "", "");
            $ticket_lines = DB::table('ticket_lines')
              ->where('user_id', $user_id)
              ->where('deletes', '0')
              ->orderBy('id', 'desc')
              ->limit(1)
              ->get();
            if ($ticket_lines->count() < 1) {
              $response = ['status' => 'failed', 'message' => "The Coupon Code that you have entered is not applicable for your account,  please proceed with another payment option!", 'error' => "The Coupon Code that you have entered is not applicable for your account,  please proceed with another payment option!"];
              goto returnFVI;
            }
          }

          if ($c_used_by == 'Non-Purchased') {
            // $ticket_lines = select_query($con, "ticket_lines", "", "`user_id` = '$user_id' and `deletes` = '0' ORDER BY `id` DESC", "", "");
            $ticket_lines = DB::table('ticket_lines')
              ->where('user_id', $user_id)
              ->where('deletes', '0')
              ->orderBy('id', 'desc')
              ->limit(1)
              ->get();
            if ($ticket_lines->count() > 0) {
              $response = ['status' => 'failed', 'message' => "The Coupon Code that you have entered is not applicable for your account,  please proceed with another payment option!", 'error' => "The Coupon Code that you have entered is not applicable for your account,  please proceed with another payment option!"];
              goto returnFVI;
            }
          }




          $couponID = $couponcode[0]->id;
          // $couponcode_history = select_query($con, "couponcode_history", "", "`couponcodeID` = '$couponID' and `userid` = '$user_id' and `deletes` = '0' ORDER BY `id` DESC", "", "");

          $couponcode_history = DB::table('couponcode_history')
            ->where('couponcodeID', $couponID)
            ->where('userid', $user_id)
            ->where('deletes', '0')
            ->orderBy('id', 'desc')
            ->get();

          if ($couponcode_history->count() > 0) {
            $response = ['status' => 'failed', 'message' => "Coupon Already Used!", 'error' => "Coupon Already Used!"];
            goto returnFVI;
          }



          // if ($method == 'GereateTicket') {


          $omaftype = 'CT';
          // $fticketArr = array("payment_transaction_id" => '', 'createdon' => $dubaidate_time, "transaction_id" => $tran_id, "deletes" => '0');
          // $fticket = insert($con, "cticket", "", $fticketArr, "", "", "");

          $fticketArr = [
            "payment_transaction_id" => null,
            'createdon' => now(),
            "transaction_id" => $transaction_id,
            "deletes" => '0',
            "payment_transaction_id" => '',
            "draw_id" => $draw_id,
            "agent_id" => 0,
            "user_id" => $user_id,
            "ticket_no" => '',
            "invoice_no" => '',
            "sale_from" => '1',
            "purchase_datetime" => now(),
            "total_amount" => 0,
            "tax_percentage" => 0,
            "tax_value" => 0,
            "net_total" => 0,
            "status" => 1,
            "payment_by" => 'COUPON',
            "total_lines" => 0,
            "delete_reason" => ''
          ];

          $fticket =  DB::table('cticket')->insertGetId($fticketArr);


          if (isset($fticket) && $fticket != '' && $fticket > 0) {
            $ticketnumber = $fticket;



            $ticket_no = $omaftype . $ticketnumber;




            // $ticket = select_query($con, "cticket", "", "`ticket_no`= '$ticket_no' AND `deletes`='0' ", "", "");

            $ticket = DB::table('cticket')
              ->where('ticket_no', $ticket_no)
              ->where('deletes', '0')
              ->orderBy('id', 'desc')
              ->limit(1)
              ->get();

            if ($ticket->count() == 0) {


              $username = auth()->user()->name;
              $mobile = auth()->user()->mobile;
              $email = auth()->user()->email;
              $topoints = floatval(auth()->user()->t_earning);




              $totalAmount = 0;





              $item1Total = isset($post['item1']) && $post['item1'] > 0 ? $post['item1'] * 10 : 0;
              $item2Total = isset($post['item2']) && $post['item2'] > 0 ? $post['item2'] * 20 : 0;
              $item3Total = isset($post['item3']) && $post['item3'] > 0 ? $post['item3'] * 50 : 0;
              $item4Total = isset($post['item4']) && $post['item4'] > 0 ? $post['item4'] * 100 : 0;

              $finalTotal = $item1Total + $item2Total + $item3Total + $item4Total;
              $totline = (isset($post['item1']) ? $post['item1'] : 0) + (isset($post['item2']) ? $post['item2'] : 0) + (isset($post['item3']) ? $post['item3'] : 0) + (isset($post['item4']) ? $post['item4'] : 0);


              $pt = "4";
              $ot = 1;
              $k = "";
              for ($x = 1; $x <= $pt; $x++) {
                $tval = "item" . $x;
                if (isset($post[$tval]) &&  $post[$tval] > 0) {
                  $k++;

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
                      $raffle_id = $omaftype . $ticketnumber . str_pad($ot, 2, "0", STR_PAD_LEFT);
                      $totalAmount += floatval($product->rate);



                      $linedate = [
                        "my3number" => $my3number,
                        "user_id" => $user_id,
                        "ticket_id" => $ticketnumber,
                        "draw_id" => $draw_id,
                        "agent_id" => 0,
                        "product_id" => $x,
                        "orders" => $ticketnumber,
                        "raffle_id" => $raffle_id,
                        "type" => $omaftype,
                        "invoice_no" => 0,
                        "createdon" => now(),
                        "deletes" => '0'
                      ];

                      $lines =   DB::table('ticket_lines')->insert($linedate);


                      $ot++;
                    }
                  }
                }
              }








              $onepercen = intval($finalTotal) / 105;
              $total_amount = floatval($onepercen) * 100;
              $tax_value = intval($finalTotal) - floatval($total_amount);




              $aticArr = [
                "ticket_no" => $ticket_no,
                "total_amount" => number_format($total_amount, 2, ".", ""),
                "tax_percentage" => "5.00",
                "tax_value" => number_format($tax_value, 2, ".", ""),
                "net_total" => $finalTotal,
                "total_lines" => $totline,
                "updatedon" => now()
              ];

              $fticket_update = DB::table('cticket')
                ->where('id', $ticketnumber)
                ->where('deletes', '0')
                ->update($aticArr);







              if ($fticket_update) {



                $searchFticket = DB::table('cticket')
                  ->where('id', $ticketnumber)
                  ->where('user_id', $user_id)
                  ->where('deletes', '0')
                  ->orderBy('id', 'desc')
                  ->limit(1)
                  ->get();


                if ($searchFticket->count() > 0) {


                  $subject = "HERE IS YOUR COUPON TICKET FROM National Draw";


                  $messages = '';


                  $messages .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                                                        <html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=edge" /><meta name="viewport" content="width=device-width, initial-scale=1.0">
                                                        
                                                        <title>Ticket Purchase OTP Mail Template</title>
                                                        
                                                        <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=fCDSKa1AxFnt4nqXyfsLgrXnjtJPIUkU0_xBrXin60bTuonbQ5BLuX_w5bXss4x4Sy-chDgELU6IhDI4FKWtklKAtsnxvw-xRk5dCUNv5Od6UTe7L00J56bOXXd3Hac-" charset="UTF-8"></script><link rel="stylesheet" crossorigin="anonymous" href="https://gc.kis.v2.scr.kaspersky-labs.com/E3E8934C-235A-4B0E-825A-35A08381A191/abn/main.css?attr=aHR0cHM6Ly9zZW50aGlsLmluLm5ldC9saXR0bGUtZHJhdy9tYWlsLXRlbXBsYXRlL2NvdXBvbl9jb2RlLmh0bWw"/><style type="text/css">
                                             
                                                        @import url(https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap);body{margin:0}.wrapper{background:#ccc}.main{background:#fff;max-width:600px}table{border-spacing:0}td{padding:3px}img{border:0}.column-one{text-align:center;margin:0 auto}.column-one .column{width:100%;margin:0 auto}
                                                        
                                                        
                                                        </style>
                                                        
                                                        </head>
                                                        
                                                        <body>
                                                        
                                                        
                                                          <center class="wrapper">
                                                        
                                                        
                                                            <table class="main" width="100%">
                                                        
                                                           
                                                        
                                                                <tr><td class="column-one" style="background: #29377d; height:50px;"></td></tr><tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;"></td></tr>
                                                                <tr>
                                                                <td class="column-one" >
                                                        
                                                                <table class="column"> <tr>
                                                                  <td valign="top" style="padding: 16px 0 0px 0;">  
                                                        
                                                                <center>
                                                        
                                                                  <img src="' .  ($request->header('Origin') . '/') . 'assets/images/mailnew/logo.png" style="border: 0px;"  >
                                                                
                                                                </center>
                                                        
                                                                  </td></tr></table>
                                                        
                                                                
                                                        
                                                                </td></tr>
                                                        
                                                          
                                                        
                                                                        <tr>
                                                        
                                                                          <td class="column-one" >
                                                        
                                                                <table align="center" class="column"> <tr><td valign="top" >  
                                                        
                                                          <div style="margin:0 auto;  max-width:500px; display:block;">
                                                        
                                                                  <div style="width:110px; float:left; ">      <img style="border: 0px;" src="' .  ($request->header('Origin') . '/') . 'assets/images/mailnew/mantoy1.png"  ></div>
                                                        
                                                        
                                                                  <div  style="">
                                                        
                                                        <h3 class="demoname" style="color: #29377d;font-family: Arial Narrow;font-style: italic;font-size: 32px;margin: 0px;text-align: center;font-weight: 700;"> ' . ($username . ' ' . auth()->user()->lname ?? '') . ' 
                                                        
                                                                            
                                                        
                                                                            </h3>
                                                        
                                                                            
                                                        
                                                                            <strong><p style="color: #29377d;   font-family: Arial Narrow;font-style: italic; font-size:200%;  margin: 0px 8px 0px 8px; text-align: center;">Your <span style="color: #be1e2d;">Coupon Ticket</span>  <br>
                                                        
                                                                            Details are Below</p>
                                                                          </strong>
                                                                            <h3 style="color: #29377d;font-family: Arial Narrow;font-style: italic;font-size: 206%;margin: 0px;text-align: center;">Ticket ID #' . $omaftype . '' . $ticketnumber . '
                                                        
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
                                                        
                                                                          <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px; width:28%" align="center" bgcolor="#d0dbe7"><strong>Products</strong></th>
                                                        
                                                                          <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px; width:12%" align="center" bgcolor="#d0dbe7"><strong>Lines</strong></th>
                                                        
                                                                          <th style="padding: 12px 5px;color: #354169;font-style: italic;font-size:24px;font-family: Arial Narrow; width:35%" align="center" bgcolor="#d0dbe7"><strong>My 
                                                        
                                                                            <span><img align="center" src="' .  ($request->header('Origin') . '/') . 'assets/images/mailnew/three.png"></span>Numbers </strong></th>
                                                        
                                                                          <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px; width:25%" align="center" bgcolor="#d0dbe7"><strong>Raffle ID</strong></th>
                                                        
                                                                        </tr>';




                  // $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and  `type` = '$omaftype' and `ticket_id`!='' and `deletes`='0' group by `product_id` order by `product_id` ASC  ", "", "");


                  // $query = DB::table('ticket_lines')

                  //   ->where('ticket_id', $ticketnumber)
                  //   ->where('type', $omaftype)
                  //   ->where('ticket_id', '!=', '')
                  //   ->where('deletes', '0')

                  //   ->orderBy('product_id', 'ASC')
                  //   ->get();

                  $query = DB::table('ticket_lines')
                    ->select('product_id')
                    ->where('deletes', '0')
                    ->where('type', $omaftype)
                    ->where('ticket_id', $ticketnumber)
                    ->groupBy('product_id')
                    ->orderBy('product_id', 'asc')
                    ->get();


                  foreach ($query as $key => $valuelist) {

                    $p_id = $valuelist->product_id;
                    // $t_id = $valuelist->orders;



                    $product = DB::table('product')
                      ->where('id', $valuelist->product_id)
                      ->first();



                    // $pcountlist = select_query_count($con, "ticket_lines", "id", " `ticket_id`='$ticketnumber' and `product_id`='$valuelist[product_id]' and `type` = '$omaftype' and `deletes`='0' and `orders`='$valuelist[orders]'", "", "");
                    // $mynumber = select_query($con, "ticket_lines", "", " `ticket_id`='$ticketnumber' and `product_id`='$p_id' and `type` = '$omaftype' and `orders`='$t_id' and `deletes`='0'", "", "");
                    $mynumber = DB::table('ticket_lines')
                      ->where('ticket_id', $ticketnumber)
                      ->where('product_id', $p_id)
                      ->where('type', $omaftype)
                      ->where('deletes', '0')
                      ->get();

                    $messages .= '<tr><td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">AED ' . number_format((float) $product->rate, 2, '.', '') . '</strong></td>';
                    $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' .  $mynumber->count() . '</strong></td>';
                    $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';

                    foreach ($mynumber as $key => $mynumber1) {
                      $messages .= $mynumber1->my3number  . "<br>";
                    }

                    $messages .= '</strong></td>';
                    $messages .= '<td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:24px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">';



                    foreach ($mynumber as $key => $mynumber1) {
                      $messages .=  $mynumber1->raffle_id  . "<br>";
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
                                                        
                                                                    <h3 style="color: #29377d;font-size: 38px;margin: 0px;font-style: italic;font-family: Arial Narrow;">Total Amount: 
                                                        
                                                                      <span class="gmail-otp-bg" style="color: #be1e2d;font-style: italic;font-family: Arial Narrow;">AED ' . number_format((float) $finalTotal, 2, '.', '') . '</span> 
                                                        
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
                                                        
                                                                  <h3 style="color: #ffffff;font-size: 26px;margin: 0px;padding: 8px 13px 10px 14px;background: #29377d;line-height: 1;border-radius: 5px;">
                                                        
                                                                  <a href="' .  ($request->header('Origin') . '/') . 'ticket-view/' . $transaction_id . '" style="color: #ffffff; text-decoration-line: none;font-style: italic;font-family: Arial Narrow;">View Ticket</a>
                                                                    
                                                        
                                                                    </h3>
                                                        
                                                                  </td>
                                                        
                                                        
                                                                </tr>
                                                        
                                                              </tbody>
                                                        
                                                            </table>
                                                        
                                                            <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                                                        
                                                              <tbody>
                                                        
                                                                <tr>
                                                        
                                                                  <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">
                                                        
                                                                    <p style="color: #29377d;font-size: 152%;text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;font-weight: 700;">Watch Just3 ' . (isset($drawData->data->active->drawfreq) && $drawData->data->active->drawfreq === 4 ? 'Daily Draw results <br> every Monday to Friday ' : 'Tri-Daily Draw results every (Monday,Wednesday,Friday) ')  . $drawData->data->active->resultDatetime . ' UAE Time ' . ((isset($drawData->data->activeSuperRaffle) && $drawData->data->activeSuperRaffle != '' && isset($drawData->data->activeSuperRaffle->drawResultDate)) ? ', Super Raffle Draw on ' . date("dS F Y", strtotime($drawData->data->activeSuperRaffle->drawResultDate)) . ' ' : ' ')   . (!Controller::checkGrandRaffleEligible($drawData->data->active->draw_no) ?  ('& <br>Grand Raffle Draw result on ' . Controller::raffleDrawDate('dS F Y')) : '') . '</p>
                                                        
                                                                  </td>
                                                              
                                                                </tr>
                                                        
                                                                
                                                                  <tr>
                                                        
                                                                  <td class="gmail-line" style="box-sizing: border-box; width: 8px;padding: 0;">
                                                        
                                                                    <img  style="width:500px !important;" src="' .  ($request->header('Origin') . '/') . 'assets/images/mailnew/final_img.png">
                                                        
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
                                                        
                                          
                                                        </html>
                                            ';




                  if ($email != "") {
                    $emailchack = explode('@', $email);
                    if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                      // $emailsend = sendemail($con, $email, $subject, $messages, 'tickets');
                      $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $messages);
                    }
                  }

                  // if (substr($mobile, 0, 3) == "971") {
                  //   $messages1 = 'Ticket ID #' . $omaftype . '' . $ticketnumber . '.';
                  //   $query = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `type` = '$omaftype' and `ticket_id`!='' group by `product_id` order by `product_id` ASC  ", "", "");

                  //   foreach ($query['result'] as $key => $valuelist) {
                  //     $p_id = $valuelist['product_id'];
                  //     $t_id = $valuelist['orders'];
                  //     $product = select_query($con, "product", "", "`id`='$valuelist[product_id]'  ", "", "");
                  //     $messages1 .= 'CAT-AED ' . round($product['result'][0]['rate']) . '. ';
                  //     $rcount = count($mynumber['result']);
                  //     $io = 1;
                  //     $mynumber = select_query($con, "ticket_lines", "", "`ticket_id`='$ticketnumber' and `product_id`='$p_id' and `type` = '$omaftype' and `orders`='$t_id'", "", "");
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

                  //   $printurlf =  ($request->header('Origin') . '/') . 'ticket-view/' . $tran_id;
                  //   $printurl = $cc->get_tiny_url($printurlf);
                  //   $messages1 .= 'for a Total Amounts of AED ' . number_format((float) $totalAmount, 2, '.', '') . ' Check your free Ticket here. ( ' . $printurl . ' ),. TC apply.';
                  //   $templateid = "";
                  //   // sendsms($con, $mobile, $messages1, $templateid);
                  // }




                  // $nowpoints = select_top_name($con, "ldbank", "points", "`deletes`='0' and `id`='9999999'", "points", "");
                  $nowpoints = DB::table('ldbank')
                    ->where('deletes', '0')
                    ->where('id', '9999999')
                    ->value('points');


                  $balancepoint = intval($nowpoints) - intval($finalTotal);

                  $tobalancepoint = $topoints + $finalTotal;



                  // $Arrd = array("from_id" => "9999999", "type" => "COUPON", "points" => $totalAmount, "from_opening" => $nowpoints, "from_closing" => $balancepoint, "to_id" => $user_id, "to_opening" => $tobalancepoint, "to_closing" => $topoints, "invoice_id" => $ticketnumber, "createdon" => $dubaidate_time);
                  // $invoice = insert($con, "points_transaction", "", $Arrd, "", "", "");


                  $Arrd = [
                    "from_id" => "9999999",
                    "type" => "COUPON",
                    "points" => $finalTotal,
                    "from_opening" => $nowpoints,
                    "from_closing" => $balancepoint,
                    "to_id" => $user_id,
                    "to_opening" => $tobalancepoint,
                    "to_closing" => $topoints,
                    "invoice_id" => $ticketnumber,
                    "createdon" => now()
                  ];

                  $invoice =  DB::table('points_transaction')->insertGetId($Arrd);


                  if (isset($invoice) && $invoice != '' && $invoice > 0) {



                    $ldArr = [
                      "points" => $balancepoint
                    ];
                    $amt_update =   DB::table('ldbank')
                      ->where('id', '9999999')
                      ->where('deletes', '0')
                      ->update($ldArr);

                    // $ld_arr = array("points" => $balancepoint);
                    // $amt_update = update($con, "ldbank", "`id`='9999999' AND `deletes`='0'", $ld_arr, "", "", "", "");
                    // $errors = $amt_update['errors'];
                    // if ($errors != "") {
                    //   $result["type"] = "0";
                    //   $result["result"] = $errors;
                    // } else {
                    if ($amt_update) {


                      $c_user_id = $user_id . ', ' . $couponcode[0]->user_id;

                      $to_c = $c_use_count + 1;


                      $couponArr = [
                        "updatedon" => now(),
                        "c_use_count" => $to_c,
                        "user_id" => rtrim($c_user_id, ', ')
                      ];

                      $coupon_update = DB::table('couponcode')
                        ->where('id', $couponID)
                        ->where('status', '0')
                        ->where('deletes', '0')
                        ->update($couponArr);

                      // $coupon_arr = ["updatedon" => $dubaidate_time, "c_use_count" => $to_c, "user_id" => rtrim($c_user_id, ', ')];
                      // $coupon_update = update($con, "couponcode", "`id` = '$couponID' AND `status` = '0' AND `deletes` = '0'", $coupon_arr, "", "", "", "");

                      // $errors = $coupon_update['errors'];
                      // if ($errors != "") {
                      //   $result["type"] = "0";
                      //   $result["result"] = $errors;
                      // } else {

                      if ($coupon_update) {


                        $pay_id = $payment_history[0]->id;

                        $payArr = [
                          'gateway' => "COUPON",
                          'pay_re_status' => "COUPON",
                          'status' => '1'
                        ];
                        $pay_update =  DB::table('payment_history')
                          ->where('id', $pay_id)
                          ->where('transaction_id', $transaction_id)
                          ->where('status', '0')
                          ->orderBy('id', 'DESC')
                          ->limit(1)
                          ->update($payArr);

                        // $pay_arr = ['gateway' => "COUPON", 'pay_re_status' => "COUPON", 'status' => '1'];
                        // $pay_update = update($con, "payment_history", "`id` = '$pay_id' AND `transaction_id` = '$tran_id' and `status` = '0' ORDER BY `id` DESC LIMIT 1", $pay_arr, "", "", "", "");

                        // $errors = $pay_update['errors'];
                        // if ($errors != "") {
                        //   $result["type"] = "0";
                        //   $result["result"] = $errors;
                        // } else {
                        if ($pay_update) {

                          // dd($pay_update);


                          // $c_ar = array("couponcodeID" => $couponID, 'createdon' => $dubaidate_time, "deletes" => '0', "userid" => $user_id, 'amt' => $totalAmount, 'ticket_id' => $ticketnumber);
                          // $c_ins = insert($con, "couponcode_history", "", $c_ar, "", "", "");

                          $cAr = [
                            "couponcodeID" => $couponID,
                            'createdon' => now(),
                            "deletes" => '0',
                            "userid" => $user_id,
                            'amt' => $finalTotal,
                            'ticket_id' => $ticketnumber
                          ];

                          $c_ins =  DB::table('couponcode_history')->insertGetId($cAr);

                          if (isset($c_ins) && $c_ins != '' && $c_ins > 0) {
                            // unset($_SESSION['cartdata']);
                            // $result["type"] = "1";
                            // $result["result"] = "Ticket Generated!";
                            // $result["thanks_url"] =  'thanks/' . $tran_id;

                            $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                            $response = ['status' => 'success', 'message' => "Ticket Generated!", 'data' => $data];
                            goto returnFVI;
                          }
                        }
                      }
                    }
                  }
                }
              } else {
                $response = ['status' => 'failed', 'message' => "The ticket generation process failed!", 'error' => "The ticket generation process failed!"];
                goto returnFVI;
              }
            } else {
              $response = ['status' => 'failed', 'message' => "Ticket Found!", 'error' => "Ticket Found!"];
              goto returnFVI;

              // $response = ['status' => 'failed', 'message' => "User Not Found!", 'error' => "User Not Found!"];
              // goto returnFVI;
            }
          }
          // }
        } else {

          $response = ['status' => 'failed', 'message' => "The Coupon code has expired, please proceed with another payment option", 'error' => "The Coupon code has expired, please proceed with another payment option"];
          goto returnFVI;
        }
      }

      // Log
      $log = Controller::error_log_new($request->ip(), 'couponCodeGenerate_API', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);




      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
