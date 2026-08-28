<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class onlineTicket extends Controller
{




    // ND Ticket Generation 
    public function onlineTicketGeneration(Request $request)
    {

        try {
            $response = [];
            $input = $request->all();
            $data = [];
            $transaction_id = $request->transaction_id;




            $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
            if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }




            // Get User ID
            $user_id = auth()->user()->id;
            if ($user_id == '' || $user_id == null || $user_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }








            // $type = "ND";
            // User Details
            $name = auth()->user()->name;
            $mobile = auth()->user()->mobile;
            $email = auth()->user()->email;
            $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
            $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
            $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->residinglocation), "'");



            // dd($transaction_id);


            $payment_history = DB::table('payment_history')
                ->select('*')
                ->where('transaction_id', 'LIKE', $transaction_id)
                ->whereIn('paymentStatus', ['paid', 'COMPLETED'])
                ->where('user_id', $user_id)
                ->where('status', '0')
                ->whereIn('purchaseType', ['NEW', 'RENEWAL', 'UPGRADE'])

                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();



            // dd($payment_history->count());

            if ($payment_history->count() > 0) {

                $paymentLog = DB::table('payment_history_log')
                    ->select('*')
                    ->where('payment_history_id', $payment_history[0]->id)
                    ->where('transaction_id', $transaction_id)
                    ->whereIn('paymentStatus', ['paid', 'COMPLETED'])
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get();

                // dd($paymentLog->count());

                if ($paymentLog->count() < 1) {

                    // Log
                    $log = Controller::error_log_new(($request->ip() ?? ''), 'OnlineTicketGenerate_API', $user_id, '', '', 'Payment Log', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                    $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                    goto returnFVI;
                }

                if ($payment_history[0]->purchaseType === 'NEW') {



                    $oticket = DB::table('crm')
                        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                        ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
                        ->where('userID', $user_id)
                        ->where('deletes', '0')
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();

                    $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();



                    if ($oticket->count() < 1 && $invoiceRecheck->count() < 1) {


                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'ticketGenerationStarted', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        $response = json_decode($paymentLog[0]->response, true);
                        $user_id = $payment_history[0]->user_id;
                        $checkout_response = json_decode($payment_history[0]->checkout_response, true);




                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        $crmRefernce = 'CRM' . md5($payment_history[0]->id . ' ' . time() . ' ' . $transaction_id);





                        $finaltotal = $payment_history[0]->finaltotal;

                        $onepercen = floatval($finaltotal) / 105;
                        $total_amount = floatval($onepercen) * 100;
                        $tax_value = floatval($finaltotal) - floatval($total_amount);

                        // dd($checkout_response);
                        // die;

                        $Arr = [
                            "userID" => $user_id,
                            "transactionID" => json_encode([intval($payment_history[0]->id)]),
                            "crmRefernce" => $crmRefernce,
                            // "sale_from" => 1,
                            // "purchaseDatetime" => $payment_history[0]->createdon,
                            // "totalAmt" => number_format($total_amount, 2, ".", ""),
                            // "taxPercentage" => 5,
                            // "taxValue" => number_format($tax_value, 2, ".", ""),
                            // "netTotal" => $finaltotal,
                            // "transactionIds" => json_encode(["$transaction_id"]),
                            // "totalRaffle" => $checkout_response['totalRaffles'],
                            // "paymentHistoryIds" => json_encode([$payment_history[0]->id]),
                            // "validityPeriod" => $checkout_response['noOfDays'],
                            // "startDate" => $checkout_response['startDate'],
                            // "is_thrill" => $checkout_response['eligibleDraw']['is_thrill'] ? 'YES' : 'NO',
                            // "is_weekly" => $checkout_response['eligibleDraw']['is_weekly'] ? 'YES' : 'NO',
                            // "is_bumper" => $checkout_response['eligibleDraw']['is_bumper'] ? 'YES' : 'NO',
                            // "endDate" => $checkout_response['endDate'],
                            // "oldTicketID" => 0,
                            "deletes" => '0',
                            "createdon" => now(),
                            "updatedon" => now(),
                            "expiryDate" => $checkout_response['expiryDate'],
                            "currentPlanBenefits" => json_encode($checkout_response)
                            // "agentId" => 0,
                            // 'discount' => number_format(floatval($checkout_response['discount']), 2, ".", ""),
                            // 'shipamount' => $payment_history[0]->shipamount,
                            // 'grandtotal' => $payment_history[0]->grandtotal
                        ];



                        $ticketId = DB::table('crm')->insertGetId($Arr);




                        $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                        if ($ticket && $ticket->id != null && $ticket->id != '') {



                            $invoiceArr = [
                                "crmID" => $ticket->id,
                                // "crmID" => $ticketId,
                                "payment_history_id" => $payment_history[0]->id,
                                // "draw_id" => $draw_id,
                                "user_id" => $user_id,
                                "product_id" => $checkout_response['productID'],
                                "totalAmt" => number_format($finaltotal, 2, ".", ""),
                                "taxPercentage" => 0,
                                "taxValue" => 0,
                                "netTotal" => $finaltotal,
                                "firstname" => $name,
                                "lastname" => auth()->user()->lname ?? '',
                                "emailid" => $email,
                                "address" => $address,
                                "city" => $city,
                                "country" => $nationality,
                                "startDate" => $checkout_response['startDate'],
                                'endDate' => $checkout_response['expiryDate'],
                                'deletes' => '0',
                                'createdon' => now(),
                                'planType' => $payment_history[0]->planType,
                                'purchaseType' => $payment_history[0]->purchaseType,
                                'payment_transaction_id' => $transaction_id,
                                'paymentType' => 'Card',
                                'mobile' => $mobile,
                                'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                                'discountID' => 0,
                                'cart' => json_encode($checkout_response),
                                'shipamount' => $payment_history[0]->shipamount,
                                'grandtotal' => $payment_history[0]->grandtotal,
                                // 'delivery_status' => ($checkout_response['shipping'] === 'deliveryToMe' ? 'requested'  : null),
                                // 'deliveryType' => $checkout_response['shipping'],
                                // 'delivery_status' => null,

                            ];



                            $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                            $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                            if ($invoice->id == null || $invoice->id == '') {
                                $response = ['status' => 'failed', 'message' => 'The invoice generation failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                                goto returnFVI;
                            }

                            // dd($invoiceid);




                            // dd($raffleIDs);

                            $ticketUpdateArr = [
                                // "raffleIds" => json_encode($raffleIDs),
                                "invoiceID" => json_encode([$invoice->id]),
                            ];

                            $ticketUpdate = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->update($ticketUpdateArr);


                            $paymentArr = [
                                "status" => '1',
                                "crmID" => $ticket->id,
                                // "draw_id" => $draw_id,
                                // "ticketReferenceID" => $ticket->referenceID,
                                "invoice_no" => $invoiceid
                            ];


                            $payUpdate = DB::table('payment_history')
                                ->where('id', '=', $payment_history[0]->id)
                                ->where('status', '0')
                                ->update($paymentArr);








                            if ($payUpdate && $ticketUpdate) {

                                // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                                // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);

                                // $printurl = $printurlf->getBody()->getContents();
                                // $invoiceurl = $printinvoice->getBody()->getContents();

                                $subject = "Purchase Confirmation - " . strtoupper(date("d-m-Y g:i a"));


                                $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Purchase Confirm Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/purchase_confirm_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Thank you for choosing <b>Go Ride!</b></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Your plan has been successfully purchased, </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">and you`re all set to take your business to the next level.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Log in to your <a href="' . ($request->header('Origin') . '/') . '" style="color:#002d72;">dashboard</a> to <br> start managing your rides efficiently.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Explore all the features that <br> come with your Go Ride plan.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="esd-structure es-p20" align="left" style="padding:20px 0;">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" style="padding:0;Margin:0">
                                                                                                <a href="' . ($request->header('Origin') . '/') . '" class="msohide es-button-border">Log In to Dashboard</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                        <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . ($request->header('Origin') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on +91 98845 57004, email at support@goride.run</p>
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                <tr>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';



                                // $messages = '
                                //   <!DOCTYPE html
                                //      PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                                //   <html xmlns="http://www.w3.org/1999/xhtml">
                                //      <head>
                                //         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                                //         <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                                //         <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                //         <title> Order Confirmation</title>
                                //         <style type="text/css">
                                //            @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                                //            @import url("https://fonts.cdnfonts.com/css/verdana");
                                //            body {
                                //            margin: 0;
                                //            }
                                //            .wrapper {
                                //            background: #CCC;
                                //            }
                                //            .main {
                                //            background: #FFF;
                                //            max-width: 600px;
                                //            }
                                //            table {
                                //            border-spacing: 0;
                                //            }
                                //            td {
                                //            padding: 3px;
                                //            }
                                //            img {
                                //            border: 0;
                                //            }
                                //            .column-one {
                                //            text-align: center;
                                //            margin: 0 auto;
                                //            }
                                //            .column-one .column {
                                //            width: 100%;
                                //            margin: 0 auto;
                                //            }
                                //            .im {
                                //            color: #01104e;
                                //            }
                                //            .column-one h3 {
                                //            color: #01104e;
                                //            font-family: Verdana, sans-serif !important;
                                //            font-size: 28px;
                                //            font-weight: 600;
                                //            margin: 14px 0 0 0;
                                //            }
                                //            .column-one p {
                                //            color: #01104e;
                                //            font-family: Verdana, sans-serif !important;
                                //            font-size: 19px;
                                //            font-weight: 500;
                                //            margin: 4px 0;
                                //            }
                                //         </style>
                                //      </head>
                                //      <body>
                                //         <center class="wrapper">
                                //            <table class="main" width="100%">
                                //               <!-- BORDER -->
                                //               <tr>
                                //                  <td style="background-color: #171f4f; height: 45px;"></td>
                                //               </tr>
                                //               <tr>
                                //                  <td class="column-one" style="background: #088b42;height:10px;">
                                //                  </td>
                                //               </tr>
                                //               <!-- <tr>
                                //                  <td style="background-color: #339a46; height: 45px;"></td>
                                //                  </tr> -->
                                //               <tr>
                                //                  <td class="column-one">
                                //                     <table class="column">
                                //                        <tr>
                                //                           <td valign="top" style="padding: 0;">
                                //                              <center>
                                //                                 <br>
                                //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                                //                                    width="50%">
                                //                              </center>
                                //                           </td>
                                //                        </tr>
                                //                        <tr>
                                //                           <td valign="top" style="padding: 0;">
                                //                              <center>
                                //                                 <br>
                                //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndThanks.png" style="border-radius: 19px;" width="70%">
                                //                                 <br>
                                //                              </center>
                                //                           </td>
                                //                        </tr>
                                //                     </table>
                                //                  </td>
                                //               </tr>
                                //               <!-- LOGO  -->
                                //               <tr>
                                //                   <td class="column-one c-f">
                                //                     <p style="font-weight: 500!important;">Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</p>
                                //                     <p style="font-size: 16px; font-weight: 400!important;">Thank you for your purchase.</p>
                                //                     <p style="font-size:21px;font-weight: 600!important;font-family: Verdana, sans-serif !important;margin:14px 0;border: 2px dotted green;width: fit-content;margin: 15px auto;padding: 6px;border-radius: 8px;">
                                //                       Ticket ID #' . $ticket->ticketNo . '
                                //                     </p>
                                //                   </td>
                                //                 </tr>
                                //                 <tr>
                                //                   <td>
                                //                       <table style="margin: auto;border-collapse: collapse;border: 1px solid #088b42;width:90%;max-width:480px;" border="1" cellspacing="2" cellpadding="0">
                                //                           <tbody>
                                //                             <tr>

                                //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width: 13%;background: #171f4f;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Valid Up To</strong></th>
                                //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Raffle ID</strong></th>

                                //                             </tr>
                                //                             <tr>
                                //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . ($checkout_response['noOfDays'] > 1 ? $checkout_response['noOfDays'] . ' Days' : $checkout_response['noOfDays'] . ' Day') . '<br>
                                //                                   <small>' . date('d F Y', strtotime($checkout_response['endDate'])) . '</small></strong></td>
                                //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . implode('<br>', $raffleIDs) . '</strong></td>
                                //                             </tr>
                                //                           </tbody>
                                //                         </table>
                                //                   </td>
                                //                 </tr>
                                //                 <tr>
                                //                   <td style="color: #111111; padding: 20px 14px; " align="center" valign="top" bgcolor="#ffffff">
                                //                     <h3 style="color: #01104e;  font-size: 20px; margin: 0px;font-family: Verdana, sans-serif !important;">
                                //                       Total Amount:
                                //                       <span class="gmail-otp-bg" style="color: #088b42;font-family: Verdana, sans-serif !important;">AED
                                //                         ' . number_format($finaltotal, 2) . '</span>
                                //                       <br>
                                //                     </h3>
                                //                   </td>
                                //                 </tr>
                                //           <tr>
                                //                   <td style="margin: auto !important;">
                                //                     <table style="margin:auto!important;" border="0" cellspacing="0" cellpadding="0">
                                //                       <tbody>
                                //                         <tr>
                                //                           <td style="padding: 0px 0px 0px 2px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:40%;" align="center" valign="top" bgcolor="#ffffff">
                                //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                                //                               <a href="' . $printurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Ticket</a>
                                //                             </h3>
                                //                           </td>
                                //                       <!-- <td style="padding: 0px 0px 0px 30px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:44%;" align="center" valign="top" bgcolor="#ffffff">
                                //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                                //                               <a href="' . $invoiceurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Invoice</a>
                                //                             </h3>
                                //                           </td> -->
                                //                         </tr>
                                //                         <br>
                                //                       </tbody>
                                //                     </table>
                                //                     <br>
                                //                   </td>
                                //                 </tr>
                                //               <tr>
                                //                  <td>
                                //                     <ul
                                //                        style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                                //                        <li>• Thrill Draw win up to 24 Grams of Gold</li>
                                //                        <li>• Booster Draw win up to 100 Grams of Gold </li>
                                //                        <li>• Bumper Draw win up to 1000 Grams of  Gold</li>
                                //                     </ul>
                                //                  </td>
                                //               </tr>

                                //               <tr>
                                //                   <td class="column-one">
                                //                      <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                                //                   </td>
                                //                </tr>
                                //               <tr>
                                //                  <td>
                                //                     <p
                                //                        style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                                //                        Note: This is a system auto-generated email. Please do not reply to this mail.
                                //                     </p>
                                //                  </td>
                                //               </tr>
                                //               <tr>
                                //               <td class="column-one" style="background: #171f4f; height:10px;">
                                //               </td>
                                //               </tr>
                                //            </table>
                                //            <!-- End Main Class -->
                                //         </center>
                                //         <!-- End Wrapper -->
                                //      </body>
                                //   </html>';

                                if (isset($email) && $email != '') {
                                    $emailchack = explode('@', $email);
                                    if (strtolower($emailchack[1]) != "goride.run") {
                                        $sendEmail = Controller::composeEmail(($request->ip() ?? ''), $email, $subject, $messages);
                                    }
                                }




                                // if ($mobile != '' && $mobile != null) {


                                // $startDateDraw = DB::table('draw')
                                //   ->where([
                                //     ['saleDate', '=', date('Y-m-d', strtotime($checkout_response['startDate']))],
                                //     ['deletes', '=', '0'],
                                //     // ['dailyThirllStatus', '=', 'Active']
                                //   ])
                                //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                                //   ->orderBy('saleDate', 'ASC')
                                //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                                //   ->limit(1)
                                //   ->get();


                                // if ($startDateDraw->count() > 0) {
                                //   $checkout_response['startDate'] = $startDateDraw[0]->resultDate;
                                // }

                                // $whatsAppArr = [
                                //   'mobile' => $mobile,
                                //   // 'templateName' => 'final_shipping_order_confirm_v3',
                                //   'language' => 'en',

                                //   'templateBodyParam' => [
                                //     ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? ''),
                                //     date('d M Y', strtotime($payment_history[0]->createdon)),
                                //     $checkout_response['noOfDays'] . " Day" . ($checkout_response['noOfDays'] > 1 ? 's' : ''),
                                //     date('d M Y', strtotime($checkout_response['startDate'])),
                                //     date('d M Y', strtotime($checkout_response['endDate']))
                                //   ],
                                //   // 'messages' => $messages,
                                //   'buttons' => [
                                //     [
                                //       'type' => 'URL',
                                //       'parameter' => $transaction_id
                                //     ]
                                //   ],
                                //   'templateName' => 'ticket_purchase_customer_template_v3'
                                // ];


                                // $whatsAppArr['messages'] = 'Hi, ' . $whatsAppArr['templateBodyParam'][0] . ' 🛍️Thank you for your Purchase on ' . $whatsAppArr['templateBodyParam'][1] . '! Your order has been confirmed. Draw valid for ' . $whatsAppArr['templateBodyParam'][2] . ' from ' . $whatsAppArr['templateBodyParam'][3] . ' to ' . $whatsAppArr['templateBodyParam'][4] . '. Wish you a Best of Luck☘️! Click below to view your ticket!👇' . $printurl;

                                // $sendWhatsapp = Controller::sendNotification($whatsAppArr);

                                // $sendWhatsapp = Controller::sendWhatsApp($whatsAppArr);
                                // }

                                // if ($checkout_response['payByLinkID'] != null) {
                                //   $updatePayLink = DB::table('payby_link')
                                //     ->where('id', $checkout_response['payByLinkID'])
                                //     // ->where('type', $type)
                                //     // ->where('status', '!=', 'Paid')
                                //     ->where(function ($query) {
                                //       $query->where('status', '!=', 'Paid')
                                //         ->orWhereNull('status');
                                //     })
                                //     ->where('user_id', $user_id)
                                //     ->where('Newpayment_id', $payment_history[0]->id)
                                //     ->where('deletes', '0')
                                //     ->update(['status' => 'Paid']);
                                // }



                                $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                                $response = ['status' => 'success', 'message' => 'The CRM Id Generated Successfully', 'data' => $data];
                                goto returnFVI;
                            } else {

                                $response = ['status' => 'failed', 'message' => 'The update process failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                                goto returnFVI;
                            }
                        } else {

                            $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                            goto returnFVI;
                        }
                    } else {

                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                        $response = ['status' => 'failed', 'message' => 'This crm has been already generated', 'error' => $data];
                        goto returnFVI;
                    }
                } else if ($payment_history[0]->purchaseType === 'RENEWAL' || $payment_history[0]->purchaseType === 'UPGRADE') {



                    $oticket = DB::table('crm')
                        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                        // ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
                        ->where('id', $payment_history[0]->crmID)
                        ->where('userID', $user_id)
                        ->where('deletes', '0')
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();

                    $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();




                    if ($oticket->count() > 0 && $invoiceRecheck->count() < 1) {


                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'ticketGenerationStarted', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        $response = json_decode($paymentLog[0]->response, true);
                        $user_id = $payment_history[0]->user_id;
                        $checkout_response = json_decode($payment_history[0]->checkout_response, true);



                        // dd($oticket);

                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $crmRefernce = 'CRM' . md5($payment_history[0]->id . ' ' . time() . ' ' . $transaction_id);





                        $finaltotal = $payment_history[0]->finaltotal;

                        $onepercen = floatval($finaltotal) / 105;
                        $total_amount = floatval($onepercen) * 100;
                        $tax_value = floatval($finaltotal) - floatval($total_amount);

                        // dd($checkout_response);
                        // die;

                        // $Arr = [
                        //   "userID" => $user_id,
                        //   "transactionID" => json_encode([intval($payment_history[0]->id)]),
                        //   "crmRefernce" => $crmRefernce,
                        //   // "sale_from" => 1,
                        //   // "purchaseDatetime" => $payment_history[0]->createdon,
                        //   // "totalAmt" => number_format($total_amount, 2, ".", ""),
                        //   // "taxPercentage" => 5,
                        //   // "taxValue" => number_format($tax_value, 2, ".", ""),
                        //   // "netTotal" => $finaltotal,
                        //   // "transactionIds" => json_encode(["$transaction_id"]),
                        //   // "totalRaffle" => $checkout_response['totalRaffles'],
                        //   // "paymentHistoryIds" => json_encode([$payment_history[0]->id]),
                        //   // "validityPeriod" => $checkout_response['noOfDays'],
                        //   // "startDate" => $checkout_response['startDate'],
                        //   // "is_thrill" => $checkout_response['eligibleDraw']['is_thrill'] ? 'YES' : 'NO',
                        //   // "is_weekly" => $checkout_response['eligibleDraw']['is_weekly'] ? 'YES' : 'NO',
                        //   // "is_bumper" => $checkout_response['eligibleDraw']['is_bumper'] ? 'YES' : 'NO',
                        //   // "endDate" => $checkout_response['endDate'],
                        //   // "oldTicketID" => 0,
                        //   "deletes" => '0',
                        //   "createdon" => now(),
                        //   "updatedon" => now(),
                        //   "expiryDate" => $checkout_response['expiryDate'],
                        //   "currentPlanBenefits" => json_encode($checkout_response)
                        //   // "agentId" => 0,
                        //   // 'discount' => number_format(floatval($checkout_response['discount']), 2, ".", ""),
                        //   // 'shipamount' => $payment_history[0]->shipamount,
                        //   // 'grandtotal' => $payment_history[0]->grandtotal
                        // ];



                        // $ticketId = DB::table('crm')->insertGetId($Arr);




                        // $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                        // if ($ticket && $ticket->id != null && $ticket->id != '') {


                        $insCart = $checkout_response;
                        unset($insCart['pastPlanHis']);
                        unset($insCart['crmDetails']);

                        $invoiceArr = [
                            "crmID" => $oticket[0]->id,
                            // "crmID" => $ticketId,
                            "payment_history_id" => $payment_history[0]->id,
                            // "draw_id" => $draw_id,
                            "user_id" => $user_id,
                            "product_id" => $checkout_response['productID'],
                            "totalAmt" => number_format($finaltotal, 2, ".", ""),
                            "taxPercentage" => 0,
                            "taxValue" => 0,
                            "netTotal" => $finaltotal,
                            "firstname" => $name,
                            "lastname" => auth()->user()->lname ?? '',
                            "emailid" => $email,
                            "address" => $address,
                            "city" => $city,
                            "country" => $nationality,
                            "startDate" => $checkout_response['startDate'],
                            'endDate' => $checkout_response['expiryDate'],
                            'deletes' => '0',
                            'createdon' => now(),
                            'planType' => $payment_history[0]->planType,
                            'purchaseType' => $payment_history[0]->purchaseType,
                            'payment_transaction_id' => $transaction_id,
                            'paymentType' => 'Card',
                            'mobile' => $mobile,
                            'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                            'discountID' => 0,
                            'cart' => json_encode($insCart),
                            'shipamount' => $payment_history[0]->shipamount,
                            'grandtotal' => $payment_history[0]->grandtotal,
                            // 'delivery_status' => ($checkout_response['shipping'] === 'deliveryToMe' ? 'requested'  : null),
                            // 'deliveryType' => $checkout_response['shipping'],
                            // 'delivery_status' => null,

                        ];



                        $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                        $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                        if ($invoice->id == null || $invoice->id == '') {
                            $response = ['status' => 'failed', 'message' => 'The invoice generation failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                            goto returnFVI;
                        }

                        // dd($invoiceid);


                        $invoiceD = json_decode($oticket[0]->invoiceID, true);

                        $transaD = json_decode($oticket[0]->transactionID, true);


                        array_push($invoiceD, $invoice->id);

                        array_push($transaD, $payment_history[0]->id);

                        // dd($transaD);


                        unset($checkout_response['crmDetails']);
                        $ticketUpdateArr = [
                            // "raffleIds" => json_encode($raffleIDs),
                            "invoiceID" => json_encode($invoiceD),
                            "expiryDate" => $checkout_response['expiryDate'],
                            "currentPlanBenefits" => json_encode($checkout_response),
                            "transactionID" => json_encode($transaD),
                        ];

                        $ticketUpdate = DB::table('crm')->where('id', $oticket[0]->id)->where("deletes", '0')->update($ticketUpdateArr);


                        $paymentArr = [
                            "status" => '1',
                            "crmID" => $oticket[0]->id,
                            // "draw_id" => $draw_id,
                            // "ticketReferenceID" => $ticket->referenceID,
                            "invoice_no" => $invoiceid
                        ];


                        $payUpdate = DB::table('payment_history')
                            ->where('id', '=', $payment_history[0]->id)
                            ->where('status', '0')
                            ->update($paymentArr);








                        if ($payUpdate && $ticketUpdate) {

                            // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                            // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);

                            // $printurl = $printurlf->getBody()->getContents();
                            // $invoiceurl = $printinvoice->getBody()->getContents();
                            if ($payment_history[0]->purchaseType === 'UPGRADE') {
                                $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Purchase Confirm Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/upgrade_confirmation_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:20px 0 0 0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Great news!</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Your <b>Go Ride</b> plan has been successfully upgraded.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">You now have access to even more powerful tools and <br> features to drive your business forward.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Ready to explore your upgraded plan?</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="esd-structure es-p20" align="left" style="padding:20px 0;">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" style="padding:0;Margin:0">
                                                                                                <a href="' . ($request->header('Origin') . '/') . '" class="msohide es-button-border">Go to Dashboard</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                        <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . ($request->header('Origin') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on +91 98845 57004, email at support@goride.run</p>
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                <tr>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';
                            } else {
                                $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Renewal Confirm Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/renewal_confirmation_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:20px 0 0 0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">We’re excited to let you know that your</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px"><b>Go Ride</b> plan has been successfully renewed!</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Your uninterrupted access to all the tools <br> and features you rely on to grow <br> your business continues seamlessly.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Keep managing your rides efficiently with <b>Go Ride.</b></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="esd-structure es-p20" align="left" style="padding:20px 0;">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" style="padding:0;Margin:0">
                                                                                                <a href="' . ($request->header('Origin') . '/') . '" class="msohide es-button-border">Go to Dashboard</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                        <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . ($request->header('Origin') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on +91 98845 57004, email at support@goride.run</p>
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                <tr>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                    <td align="center" valign="top" style="padding:0;Margin:0"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';
                            }




                            $subject = ucfirst(strtolower($payment_history[0]->purchaseType)) . " Confirmation - " . strtoupper(date("d-m-Y g:i a"));



                            // $messages = '
                            //   <!DOCTYPE html
                            //      PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            //   <html xmlns="http://www.w3.org/1999/xhtml">
                            //      <head>
                            //         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                            //         <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                            //         <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            //         <title> Order Confirmation</title>
                            //         <style type="text/css">
                            //            @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                            //            @import url("https://fonts.cdnfonts.com/css/verdana");
                            //            body {
                            //            margin: 0;
                            //            }
                            //            .wrapper {
                            //            background: #CCC;
                            //            }
                            //            .main {
                            //            background: #FFF;
                            //            max-width: 600px;
                            //            }
                            //            table {
                            //            border-spacing: 0;
                            //            }
                            //            td {
                            //            padding: 3px;
                            //            }
                            //            img {
                            //            border: 0;
                            //            }
                            //            .column-one {
                            //            text-align: center;
                            //            margin: 0 auto;
                            //            }
                            //            .column-one .column {
                            //            width: 100%;
                            //            margin: 0 auto;
                            //            }
                            //            .im {
                            //            color: #01104e;
                            //            }
                            //            .column-one h3 {
                            //            color: #01104e;
                            //            font-family: Verdana, sans-serif !important;
                            //            font-size: 28px;
                            //            font-weight: 600;
                            //            margin: 14px 0 0 0;
                            //            }
                            //            .column-one p {
                            //            color: #01104e;
                            //            font-family: Verdana, sans-serif !important;
                            //            font-size: 19px;
                            //            font-weight: 500;
                            //            margin: 4px 0;
                            //            }
                            //         </style>
                            //      </head>
                            //      <body>
                            //         <center class="wrapper">
                            //            <table class="main" width="100%">
                            //               <!-- BORDER -->
                            //               <tr>
                            //                  <td style="background-color: #171f4f; height: 45px;"></td>
                            //               </tr>
                            //               <tr>
                            //                  <td class="column-one" style="background: #088b42;height:10px;">
                            //                  </td>
                            //               </tr>
                            //               <!-- <tr>
                            //                  <td style="background-color: #339a46; height: 45px;"></td>
                            //                  </tr> -->
                            //               <tr>
                            //                  <td class="column-one">
                            //                     <table class="column">
                            //                        <tr>
                            //                           <td valign="top" style="padding: 0;">
                            //                              <center>
                            //                                 <br>
                            //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                            //                                    width="50%">
                            //                              </center>
                            //                           </td>
                            //                        </tr>
                            //                        <tr>
                            //                           <td valign="top" style="padding: 0;">
                            //                              <center>
                            //                                 <br>
                            //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndThanks.png" style="border-radius: 19px;" width="70%">
                            //                                 <br>
                            //                              </center>
                            //                           </td>
                            //                        </tr>
                            //                     </table>
                            //                  </td>
                            //               </tr>
                            //               <!-- LOGO  -->
                            //               <tr>
                            //                   <td class="column-one c-f">
                            //                     <p style="font-weight: 500!important;">Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</p>
                            //                     <p style="font-size: 16px; font-weight: 400!important;">Thank you for your purchase.</p>
                            //                     <p style="font-size:21px;font-weight: 600!important;font-family: Verdana, sans-serif !important;margin:14px 0;border: 2px dotted green;width: fit-content;margin: 15px auto;padding: 6px;border-radius: 8px;">
                            //                       Ticket ID #' . $ticket->ticketNo . '
                            //                     </p>
                            //                   </td>
                            //                 </tr>
                            //                 <tr>
                            //                   <td>
                            //                       <table style="margin: auto;border-collapse: collapse;border: 1px solid #088b42;width:90%;max-width:480px;" border="1" cellspacing="2" cellpadding="0">
                            //                           <tbody>
                            //                             <tr>

                            //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width: 13%;background: #171f4f;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Valid Up To</strong></th>
                            //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Raffle ID</strong></th>

                            //                             </tr>
                            //                             <tr>
                            //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . ($checkout_response['noOfDays'] > 1 ? $checkout_response['noOfDays'] . ' Days' : $checkout_response['noOfDays'] . ' Day') . '<br>
                            //                                   <small>' . date('d F Y', strtotime($checkout_response['endDate'])) . '</small></strong></td>
                            //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . implode('<br>', $raffleIDs) . '</strong></td>
                            //                             </tr>
                            //                           </tbody>
                            //                         </table>
                            //                   </td>
                            //                 </tr>
                            //                 <tr>
                            //                   <td style="color: #111111; padding: 20px 14px; " align="center" valign="top" bgcolor="#ffffff">
                            //                     <h3 style="color: #01104e;  font-size: 20px; margin: 0px;font-family: Verdana, sans-serif !important;">
                            //                       Total Amount:
                            //                       <span class="gmail-otp-bg" style="color: #088b42;font-family: Verdana, sans-serif !important;">AED
                            //                         ' . number_format($finaltotal, 2) . '</span>
                            //                       <br>
                            //                     </h3>
                            //                   </td>
                            //                 </tr>
                            //           <tr>
                            //                   <td style="margin: auto !important;">
                            //                     <table style="margin:auto!important;" border="0" cellspacing="0" cellpadding="0">
                            //                       <tbody>
                            //                         <tr>
                            //                           <td style="padding: 0px 0px 0px 2px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:40%;" align="center" valign="top" bgcolor="#ffffff">
                            //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                            //                               <a href="' . $printurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Ticket</a>
                            //                             </h3>
                            //                           </td>
                            //                       <!-- <td style="padding: 0px 0px 0px 30px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:44%;" align="center" valign="top" bgcolor="#ffffff">
                            //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                            //                               <a href="' . $invoiceurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Invoice</a>
                            //                             </h3>
                            //                           </td> -->
                            //                         </tr>
                            //                         <br>
                            //                       </tbody>
                            //                     </table>
                            //                     <br>
                            //                   </td>
                            //                 </tr>
                            //               <tr>
                            //                  <td>
                            //                     <ul
                            //                        style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                            //                        <li>• Thrill Draw win up to 24 Grams of Gold</li>
                            //                        <li>• Booster Draw win up to 100 Grams of Gold </li>
                            //                        <li>• Bumper Draw win up to 1000 Grams of  Gold</li>
                            //                     </ul>
                            //                  </td>
                            //               </tr>

                            //               <tr>
                            //                   <td class="column-one">
                            //                      <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                            //                   </td>
                            //                </tr>
                            //               <tr>
                            //                  <td>
                            //                     <p
                            //                        style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                            //                        Note: This is a system auto-generated email. Please do not reply to this mail.
                            //                     </p>
                            //                  </td>
                            //               </tr>
                            //               <tr>
                            //               <td class="column-one" style="background: #171f4f; height:10px;">
                            //               </td>
                            //               </tr>
                            //            </table>
                            //            <!-- End Main Class -->
                            //         </center>
                            //         <!-- End Wrapper -->
                            //      </body>
                            //   </html>';

                            if (isset($email) && $email != '') {
                                $emailchack = explode('@', $email);
                                if (strtolower($emailchack[1]) != "goride.run") {
                                    $sendEmail = Controller::composeEmail(($request->ip() ?? ''), $email, $subject, $messages);
                                }
                            }




                            // if ($mobile != '' && $mobile != null) {


                            // $startDateDraw = DB::table('draw')
                            //   ->where([
                            //     ['saleDate', '=', date('Y-m-d', strtotime($checkout_response['startDate']))],
                            //     ['deletes', '=', '0'],
                            //     // ['dailyThirllStatus', '=', 'Active']
                            //   ])
                            //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                            //   ->orderBy('saleDate', 'ASC')
                            //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                            //   ->limit(1)
                            //   ->get();


                            // if ($startDateDraw->count() > 0) {
                            //   $checkout_response['startDate'] = $startDateDraw[0]->resultDate;
                            // }

                            // $whatsAppArr = [
                            //   'mobile' => $mobile,
                            //   // 'templateName' => 'final_shipping_order_confirm_v3',
                            //   'language' => 'en',

                            //   'templateBodyParam' => [
                            //     ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? ''),
                            //     date('d M Y', strtotime($payment_history[0]->createdon)),
                            //     $checkout_response['noOfDays'] . " Day" . ($checkout_response['noOfDays'] > 1 ? 's' : ''),
                            //     date('d M Y', strtotime($checkout_response['startDate'])),
                            //     date('d M Y', strtotime($checkout_response['endDate']))
                            //   ],
                            //   // 'messages' => $messages,
                            //   'buttons' => [
                            //     [
                            //       'type' => 'URL',
                            //       'parameter' => $transaction_id
                            //     ]
                            //   ],
                            //   'templateName' => 'ticket_purchase_customer_template_v3'
                            // ];


                            // $whatsAppArr['messages'] = 'Hi, ' . $whatsAppArr['templateBodyParam'][0] . ' 🛍️Thank you for your Purchase on ' . $whatsAppArr['templateBodyParam'][1] . '! Your order has been confirmed. Draw valid for ' . $whatsAppArr['templateBodyParam'][2] . ' from ' . $whatsAppArr['templateBodyParam'][3] . ' to ' . $whatsAppArr['templateBodyParam'][4] . '. Wish you a Best of Luck☘️! Click below to view your ticket!👇' . $printurl;

                            // $sendWhatsapp = Controller::sendNotification($whatsAppArr);

                            // $sendWhatsapp = Controller::sendWhatsApp($whatsAppArr);
                            // }

                            // if ($checkout_response['payByLinkID'] != null) {
                            //   $updatePayLink = DB::table('payby_link')
                            //     ->where('id', $checkout_response['payByLinkID'])
                            //     // ->where('type', $type)
                            //     // ->where('status', '!=', 'Paid')
                            //     ->where(function ($query) {
                            //       $query->where('status', '!=', 'Paid')
                            //         ->orWhereNull('status');
                            //     })
                            //     ->where('user_id', $user_id)
                            //     ->where('Newpayment_id', $payment_history[0]->id)
                            //     ->where('deletes', '0')
                            //     ->update(['status' => 'Paid']);
                            // }



                            $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                            $response = ['status' => 'success', 'message' => 'The CRM Plan updated successfully', 'data' => $data];
                            goto returnFVI;
                        } else {

                            $response = ['status' => 'failed', 'message' => 'The update process failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                            goto returnFVI;
                        }
                        // } 

                        // else {

                        //   $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                        //   goto returnFVI;
                        // }
                    } else {

                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                        $response = ['status' => 'failed', 'message' => 'This crm has been already generated', 'error' => $data];
                        goto returnFVI;
                    }
                } else {
                    dd('hii');
                    $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                    goto returnFVI;
                }
            } else {

                // Log
                $log = Controller::error_log_new(($request->ip() ?? ''), 'OnlineTicketGenerate_API', $user_id, '', '', 'Query Result', json_encode($payment_history), __DIR__, basename(__FILE__), __LINE__);



                $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }


            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }



    public function buyTrailCRM(Request $request)
    {

        try {
            $response = [];
            $input = $request->all();
            $data = [];
            $transaction_id = $request->transaction_id;

            $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
            if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            $user_id = auth()->user()->id;
            if ($user_id == '' || $user_id == null || $user_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            $name = auth()->user()->name;
            $mobile = auth()->user()->mobile;
            $email = auth()->user()->email;
            $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
            $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
            $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->residinglocation), "'");



            $checkTrail = DB::table('invoice')
                ->where('user_id', $user_id)
                ->where('deletes', '0')
                ->where('planType', 'TRAIL')
                ->orderBy('id', 'DESC')
                ->get();

            if ($checkTrail->count() > 0) {

                $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }




            $payment_history = DB::table('subscriptions')
                ->select('*')
                ->where('subscription_id', 'LIKE', $transaction_id)
                // ->whereIn('paymentStatus', ['paid'])
                ->where('user_id', $user_id)
                ->where('planType', 'TRAIL')
                ->where('status', '0')
                ->whereIn('purchaseType', ['NEW', 'RENEWAL'])

                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();

            // dd($payment_history);

            // dd($payment_history->count());

            if ($payment_history->count() > 0) {

                // $paymentLog = DB::table('payment_history_log')
                //   ->select('*')
                //   ->where('payment_history_id', $payment_history[0]->id)
                //   ->where('transaction_id', $transaction_id)
                //   ->whereIn('paymentStatus', ['paid'])
                //   ->orderBy('id', 'DESC')
                //   ->limit(1)
                //   ->get();

                // // dd($paymentLog->count());

                // if ($paymentLog->count() < 1) {

                //   // Log
                //   $log = Controller::error_log_new(($request->ip() ?? ''), 'OnlineTicketGenerate_API', $user_id, '', '', 'Payment Log', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                //   $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                //   goto returnFVI;
                // }

                if ($payment_history[0]->purchaseType === 'NEW') {



                    $oticket = DB::table('crm')
                        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                        ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
                        ->where('userID', $user_id)
                        ->where('deletes', '0')
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();

                    $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();



                    if ($oticket->count() < 1 && $invoiceRecheck->count() < 1) {


                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'ticketGenerationStarted', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $response = json_decode($paymentLog[0]->response, true);
                        $user_id = $payment_history[0]->user_id;
                        $checkout_response = json_decode($payment_history[0]->checkout_response, true);




                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        $crmRefernce = 'CRM' . md5($payment_history[0]->id . ' ' . time() . ' ' . $transaction_id);





                        $finaltotal = $payment_history[0]->finaltotal;

                        $onepercen = floatval($finaltotal) / 105;
                        $total_amount = floatval($onepercen) * 100;
                        $tax_value = floatval($finaltotal) - floatval($total_amount);

                        // dd($checkout_response);
                        // die;

                        $Arr = [
                            "userID" => $user_id,
                            "subscription_id" => $payment_history[0]->id,
                            // "transactionID" => json_encode([intval($payment_history[0]->id)]),
                            "crmRefernce" => $crmRefernce,
                            // "sale_from" => 1,
                            // "purchaseDatetime" => $payment_history[0]->createdon,
                            // "totalAmt" => number_format($total_amount, 2, ".", ""),
                            // "taxPercentage" => 5,
                            // "taxValue" => number_format($tax_value, 2, ".", ""),
                            // "netTotal" => $finaltotal,
                            // "transactionIds" => json_encode(["$transaction_id"]),
                            // "totalRaffle" => $checkout_response['totalRaffles'],
                            // "paymentHistoryIds" => json_encode([$payment_history[0]->id]),
                            // "validityPeriod" => $checkout_response['noOfDays'],
                            // "startDate" => $checkout_response['startDate'],
                            // "is_thrill" => $checkout_response['eligibleDraw']['is_thrill'] ? 'YES' : 'NO',
                            // "is_weekly" => $checkout_response['eligibleDraw']['is_weekly'] ? 'YES' : 'NO',
                            // "is_bumper" => $checkout_response['eligibleDraw']['is_bumper'] ? 'YES' : 'NO',
                            // "endDate" => $checkout_response['endDate'],
                            // "oldTicketID" => 0,
                            "deletes" => '0',
                            "createdon" => now(),
                            "updatedon" => now(),
                            "expiryDate" => $checkout_response['expiryDate'],
                            "currentPlanBenefits" => json_encode($checkout_response),
                             "sub_status" => 'active'
                            // "agentId" => 0,
                            // 'discount' => number_format(floatval($checkout_response['discount']), 2, ".", ""),
                            // 'shipamount' => $payment_history[0]->shipamount,
                            // 'grandtotal' => $payment_history[0]->grandtotal
                        ];



                        $ticketId = DB::table('crm')->insertGetId($Arr);




                        $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                        if ($ticket && $ticket->id != null && $ticket->id != '') {



                            $invoiceArr = [
                                "crmID" => $ticket->id,
                                 "subscription_id" => $payment_history[0]->id,
                                // "crmID" => $ticketId,
                                // "payment_history_id" => $payment_history[0]->id,
                                // "draw_id" => $draw_id,
                                "user_id" => $user_id,
                                "product_id" => $checkout_response['productID'],
                                "totalAmt" => number_format($finaltotal, 2, ".", ""),
                                "taxPercentage" => 0,
                                "taxValue" => 0,
                                "netTotal" => $finaltotal,
                                "firstname" => $name,
                                "lastname" => auth()->user()->lname ?? '',
                                "emailid" => $email,
                                "address" => $address,
                                "city" => $city,
                                "country" => $nationality,
                                "startDate" => $checkout_response['startDate'],
                                'endDate' => $checkout_response['expiryDate'],
                                'deletes' => '0',
                                'createdon' => now(),
                                'planType' => $payment_history[0]->planType,
                                'purchaseType' => $payment_history[0]->purchaseType,
                                'payment_transaction_id' => $transaction_id,
                                'paymentType' => 'Card',
                                'mobile' => $mobile,
                                'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                                'discountID' => 0,
                                'cart' => json_encode($checkout_response),
                                'shipamount' => $payment_history[0]->shipamount,
                                'grandtotal' => $payment_history[0]->grandtotal,
                                // "subscrID" => $payment_history[0]->id,
                                "sub_status" => 'active',
                                // 'delivery_status' => ($checkout_response['shipping'] === 'deliveryToMe' ? 'requested'  : null),
                                // 'deliveryType' => $checkout_response['shipping'],
                                // 'delivery_status' => null,
  "currency" => $checkout_response['currency'] ?? null
                            ];



                            $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                            $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                            if ($invoice->id == null || $invoice->id == '') {
                                $response = ['status' => 'failed', 'message' => 'The invoice generation failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                                goto returnFVI;
                            }

                            // dd($invoiceid);




                            // dd($raffleIDs);

                            $ticketUpdateArr = [
                                // "raffleIds" => json_encode($raffleIDs),
                                "invoiceID" => json_encode([$invoice->id]),
                            ];

                            $ticketUpdate = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->update($ticketUpdateArr);


                            $paymentArr = [
                                "status" => '1',
                                "crmID" => $ticket->id,
                                // "draw_id" => $draw_id,
                                // "ticketReferenceID" => $ticket->referenceID,
                                "invoiceID" => $invoiceid,
                                'paymentStatus' => "SUCCESS",
                                  "sub_status" => 'active',
                                  "gateway" => "goride",
                                  "cycles_paid" => 1
                            ];


                            $payUpdate = DB::table('subscriptions')
                                ->where('id', '=', $payment_history[0]->id)
                                ->where('status', '0')
                                ->update($paymentArr);








                            if ($payUpdate && $ticketUpdate) {

                                // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                                // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);

                                // $printurl = $printurlf->getBody()->getContents();
                                // $invoiceurl = $printinvoice->getBody()->getContents();

                                // $subject = "Purchase Confirmation - " . strtoupper(date("d-m-Y g:i a"));
                                
                                    $subject = "Purchase Confirmation";

                                $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Purchase Confirm Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff !important;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/purchase_confirm_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Thank you for choosing <b>Go Ride!</b></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Your plan has been successfully purchased, </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">and you`re all set to take your business to the next level.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Log in to your <a href="' . ($request->header('Origin') . '/') . '" style="color:#002d72;">dashboard</a> to <br> start managing your rides efficiently.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Explore all the features that <br> come with your Go Ride plan.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="esd-structure es-p20" align="left" style="padding:20px 0;">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" style="padding:0;Margin:0">
                                                                                                <a href="' . ($request->header('Origin') . '/') . '" class="msohide es-button-border">Log In to Dashboard</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                         <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . (env('APP_URL') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on <a href="tel:'. env('SUPPORT_MOBILE') .'">'. env('SUPPORT_MOBILE') .'</a>, email at <a href="mailto:'. env('SUPPORT_EMAIL') .'">'. env('SUPPORT_EMAIL') .'</a></p>
                                                                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                              <tr>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_FB') .'"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_YOUTUBE') .'"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0"><a href="'. env('SUPPORT_INSTA') .'"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                        </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';

                                
                                if (isset($email) && $email != '') {
                                    $emailchack = explode('@', $email);
                                    if (strtolower($emailchack[1]) != "goride.run") {
                                        $sendEmail = Controller::composeEmail(($request->ip() ?? ''), $email, $subject, $messages);
                                    }
                                }




                                // if ($mobile != '' && $mobile != null) {


                                // $startDateDraw = DB::table('draw')
                                //   ->where([
                                //     ['saleDate', '=', date('Y-m-d', strtotime($checkout_response['startDate']))],
                                //     ['deletes', '=', '0'],
                                //     // ['dailyThirllStatus', '=', 'Active']
                                //   ])
                                //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                                //   ->orderBy('saleDate', 'ASC')
                                //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                                //   ->limit(1)
                                //   ->get();


                                // if ($startDateDraw->count() > 0) {
                                //   $checkout_response['startDate'] = $startDateDraw[0]->resultDate;
                                // }

                                // $whatsAppArr = [
                                //   'mobile' => $mobile,
                                //   // 'templateName' => 'final_shipping_order_confirm_v3',
                                //   'language' => 'en',

                                //   'templateBodyParam' => [
                                //     ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? ''),
                                //     date('d M Y', strtotime($payment_history[0]->createdon)),
                                //     $checkout_response['noOfDays'] . " Day" . ($checkout_response['noOfDays'] > 1 ? 's' : ''),
                                //     date('d M Y', strtotime($checkout_response['startDate'])),
                                //     date('d M Y', strtotime($checkout_response['endDate']))
                                //   ],
                                //   // 'messages' => $messages,
                                //   'buttons' => [
                                //     [
                                //       'type' => 'URL',
                                //       'parameter' => $transaction_id
                                //     ]
                                //   ],
                                //   'templateName' => 'ticket_purchase_customer_template_v3'
                                // ];


                                // $whatsAppArr['messages'] = 'Hi, ' . $whatsAppArr['templateBodyParam'][0] . ' 🛍️Thank you for your Purchase on ' . $whatsAppArr['templateBodyParam'][1] . '! Your order has been confirmed. Draw valid for ' . $whatsAppArr['templateBodyParam'][2] . ' from ' . $whatsAppArr['templateBodyParam'][3] . ' to ' . $whatsAppArr['templateBodyParam'][4] . '. Wish you a Best of Luck☘️! Click below to view your ticket!👇' . $printurl;

                                // $sendWhatsapp = Controller::sendNotification($whatsAppArr);

                                // $sendWhatsapp = Controller::sendWhatsApp($whatsAppArr);
                                // }

                                // if ($checkout_response['payByLinkID'] != null) {
                                //   $updatePayLink = DB::table('payby_link')
                                //     ->where('id', $checkout_response['payByLinkID'])
                                //     // ->where('type', $type)
                                //     // ->where('status', '!=', 'Paid')
                                //     ->where(function ($query) {
                                //       $query->where('status', '!=', 'Paid')
                                //         ->orWhereNull('status');
                                //     })
                                //     ->where('user_id', $user_id)
                                //     ->where('Newpayment_id', $payment_history[0]->id)
                                //     ->where('deletes', '0')
                                //     ->update(['status' => 'Paid']);
                                // }



                                $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                                $data['crmIDs'] = $ticket->id;
                                $response = ['status' => 'success', 'message' => 'Your CRM has been successfully generated. Continue with the setup.', 'data' => $data];
                                goto returnFVI;
                            } else {

                                $response = ['status' => 'failed', 'message' => 'The update process failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                                goto returnFVI;
                            }
                        } else {

                            $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                            goto returnFVI;
                        }
                    } else {

                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                        $response = ['status' => 'failed', 'message' => 'Your crm has been already generated', 'error' => $data];
                        goto returnFVI;
                    }
                }

                // else if ($payment_history[0]->purchaseType === 'RENEWAL') {



                //   $oticket = DB::table('crm')
                //     // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                //     // ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
                //     ->where('id', $payment_history[0]->crmID)
                //     ->where('userID', $user_id)
                //     ->where('deletes', '0')
                //     ->orderBy('id', 'DESC')
                //     ->limit(1)
                //     ->get();

                //   $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                //     ->limit(1)
                //     ->get();




                //   if ($oticket->count() > 0 && $invoiceRecheck->count() < 1) {


                //     // Log
                //     // $log = Controller::error_log_new(($request->ip() ?? ''), 'ticketGenerationStarted', $user_id, '', '', 'Start Ticket Generation', json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                //     $response = json_decode($paymentLog[0]->response, true);
                //     $user_id = $payment_history[0]->user_id;
                //     $checkout_response = json_decode($payment_history[0]->checkout_response, true);



                //     // dd($oticket);

                //     // Log
                //     // $log = Controller::error_log_new(($request->ip() ?? ''), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                //     // $crmRefernce = 'CRM' . md5($payment_history[0]->id . ' ' . time() . ' ' . $transaction_id);





                //     $finaltotal = $payment_history[0]->finaltotal;

                //     $onepercen = floatval($finaltotal) / 105;
                //     $total_amount = floatval($onepercen) * 100;
                //     $tax_value = floatval($finaltotal) - floatval($total_amount);

                //     // dd($checkout_response);
                //     // die;

                //     // $Arr = [
                //     //   "userID" => $user_id,
                //     //   "transactionID" => json_encode([intval($payment_history[0]->id)]),
                //     //   "crmRefernce" => $crmRefernce,
                //     //   // "sale_from" => 1,
                //     //   // "purchaseDatetime" => $payment_history[0]->createdon,
                //     //   // "totalAmt" => number_format($total_amount, 2, ".", ""),
                //     //   // "taxPercentage" => 5,
                //     //   // "taxValue" => number_format($tax_value, 2, ".", ""),
                //     //   // "netTotal" => $finaltotal,
                //     //   // "transactionIds" => json_encode(["$transaction_id"]),
                //     //   // "totalRaffle" => $checkout_response['totalRaffles'],
                //     //   // "paymentHistoryIds" => json_encode([$payment_history[0]->id]),
                //     //   // "validityPeriod" => $checkout_response['noOfDays'],
                //     //   // "startDate" => $checkout_response['startDate'],
                //     //   // "is_thrill" => $checkout_response['eligibleDraw']['is_thrill'] ? 'YES' : 'NO',
                //     //   // "is_weekly" => $checkout_response['eligibleDraw']['is_weekly'] ? 'YES' : 'NO',
                //     //   // "is_bumper" => $checkout_response['eligibleDraw']['is_bumper'] ? 'YES' : 'NO',
                //     //   // "endDate" => $checkout_response['endDate'],
                //     //   // "oldTicketID" => 0,
                //     //   "deletes" => '0',
                //     //   "createdon" => now(),
                //     //   "updatedon" => now(),
                //     //   "expiryDate" => $checkout_response['expiryDate'],
                //     //   "currentPlanBenefits" => json_encode($checkout_response)
                //     //   // "agentId" => 0,
                //     //   // 'discount' => number_format(floatval($checkout_response['discount']), 2, ".", ""),
                //     //   // 'shipamount' => $payment_history[0]->shipamount,
                //     //   // 'grandtotal' => $payment_history[0]->grandtotal
                //     // ];



                //     // $ticketId = DB::table('crm')->insertGetId($Arr);




                //     // $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                //     // if ($ticket && $ticket->id != null && $ticket->id != '') {



                //     $invoiceArr = [
                //       "crmID" => $oticket[0]->id,
                //       // "crmID" => $ticketId,
                //       "payment_history_id" => $payment_history[0]->id,
                //       // "draw_id" => $draw_id,
                //       "user_id" => $user_id,
                //       "product_id" => $checkout_response['productID'],
                //       "totalAmt" => number_format($finaltotal, 2, ".", ""),
                //       "taxPercentage" => 0,
                //       "taxValue" => 0,
                //       "netTotal" => $finaltotal,
                //       "firstname" => $name,
                //       "lastname" => auth()->user()->lname ?? '',
                //       "emailid" => $email,
                //       "address" => $address,
                //       "city" => $city,
                //       "country" => $nationality,
                //       "startDate" => $checkout_response['startDate'],
                //       'endDate' => $checkout_response['expiryDate'],
                //       'deletes' => '0',
                //       'createdon' => now(),
                //       'planType' => $payment_history[0]->planType,
                //       'purchaseType' => $payment_history[0]->purchaseType,
                //       'payment_transaction_id' => $transaction_id,
                //       'paymentType' => 'Card',
                //       'mobile' => $mobile,
                //       'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                //       'discountID' => 0,
                //       'cart' => json_encode($checkout_response),
                //       'shipamount' => $payment_history[0]->shipamount,
                //       'grandtotal' => $payment_history[0]->grandtotal,
                //       // 'delivery_status' => ($checkout_response['shipping'] === 'deliveryToMe' ? 'requested'  : null),
                //       // 'deliveryType' => $checkout_response['shipping'],
                //       // 'delivery_status' => null,

                //     ];



                //     $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                //     $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                //     if ($invoice->id == null || $invoice->id == '') {
                //       $response = ['status' => 'failed', 'message' => 'The invoice generation failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                //       goto returnFVI;
                //     }

                //     // dd($invoiceid);


                //     $invoiceD = json_decode($oticket[0]->invoiceID, true);

                //     $transaD = json_decode($oticket[0]->transactionID, true);


                //     array_push($invoiceD, $invoice->id);

                //     array_push($transaD, $payment_history[0]->id);

                //     // dd($transaD);

                //     $ticketUpdateArr = [
                //       // "raffleIds" => json_encode($raffleIDs),
                //       "invoiceID" => json_encode($invoiceD),
                //       "expiryDate" => $checkout_response['expiryDate'],
                //       "currentPlanBenefits" => json_encode($checkout_response),
                //       "transactionID" => json_encode($transaD),
                //     ];

                //     $ticketUpdate = DB::table('crm')->where('id', $oticket[0]->id)->where("deletes", '0')->update($ticketUpdateArr);


                //     $paymentArr = [
                //       "status" => '1',
                //       "crmID" => $oticket[0]->id,
                //       // "draw_id" => $draw_id,
                //       // "ticketReferenceID" => $ticket->referenceID,
                //       "invoice_no" => $invoiceid
                //     ];


                //     $payUpdate = DB::table('payment_history')
                //       ->where('id', '=', $payment_history[0]->id)
                //       ->where('status', '0')
                //       ->update($paymentArr);








                //     if ($payUpdate && $ticketUpdate) {

                //       // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                //       // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);

                //       // $printurl = $printurlf->getBody()->getContents();
                //       // $invoiceurl = $printinvoice->getBody()->getContents();

                //       // $subject = "Purchase Confirmation - " . strtoupper(date("d-m-Y g:i a"));

                //       // $messages = '
                //       //   <!DOCTYPE html
                //       //      PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                //       //   <html xmlns="http://www.w3.org/1999/xhtml">
                //       //      <head>
                //       //         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                //       //         <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                //       //         <meta name="viewport" content="width=device-width, initial-scale=1.0">
                //       //         <title> Order Confirmation</title>
                //       //         <style type="text/css">
                //       //            @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                //       //            @import url("https://fonts.cdnfonts.com/css/verdana");
                //       //            body {
                //       //            margin: 0;
                //       //            }
                //       //            .wrapper {
                //       //            background: #CCC;
                //       //            }
                //       //            .main {
                //       //            background: #FFF;
                //       //            max-width: 600px;
                //       //            }
                //       //            table {
                //       //            border-spacing: 0;
                //       //            }
                //       //            td {
                //       //            padding: 3px;
                //       //            }
                //       //            img {
                //       //            border: 0;
                //       //            }
                //       //            .column-one {
                //       //            text-align: center;
                //       //            margin: 0 auto;
                //       //            }
                //       //            .column-one .column {
                //       //            width: 100%;
                //       //            margin: 0 auto;
                //       //            }
                //       //            .im {
                //       //            color: #01104e;
                //       //            }
                //       //            .column-one h3 {
                //       //            color: #01104e;
                //       //            font-family: Verdana, sans-serif !important;
                //       //            font-size: 28px;
                //       //            font-weight: 600;
                //       //            margin: 14px 0 0 0;
                //       //            }
                //       //            .column-one p {
                //       //            color: #01104e;
                //       //            font-family: Verdana, sans-serif !important;
                //       //            font-size: 19px;
                //       //            font-weight: 500;
                //       //            margin: 4px 0;
                //       //            }
                //       //         </style>
                //       //      </head>
                //       //      <body>
                //       //         <center class="wrapper">
                //       //            <table class="main" width="100%">
                //       //               <!-- BORDER -->
                //       //               <tr>
                //       //                  <td style="background-color: #171f4f; height: 45px;"></td>
                //       //               </tr>
                //       //               <tr>
                //       //                  <td class="column-one" style="background: #088b42;height:10px;">
                //       //                  </td>
                //       //               </tr>
                //       //               <!-- <tr>
                //       //                  <td style="background-color: #339a46; height: 45px;"></td>
                //       //                  </tr> -->
                //       //               <tr>
                //       //                  <td class="column-one">
                //       //                     <table class="column">
                //       //                        <tr>
                //       //                           <td valign="top" style="padding: 0;">
                //       //                              <center>
                //       //                                 <br>
                //       //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                //       //                                    width="50%">
                //       //                              </center>
                //       //                           </td>
                //       //                        </tr>
                //       //                        <tr>
                //       //                           <td valign="top" style="padding: 0;">
                //       //                              <center>
                //       //                                 <br>
                //       //                                 <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndThanks.png" style="border-radius: 19px;" width="70%">
                //       //                                 <br>
                //       //                              </center>
                //       //                           </td>
                //       //                        </tr>
                //       //                     </table>
                //       //                  </td>
                //       //               </tr>
                //       //               <!-- LOGO  -->
                //       //               <tr>
                //       //                   <td class="column-one c-f">
                //       //                     <p style="font-weight: 500!important;">Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</p>
                //       //                     <p style="font-size: 16px; font-weight: 400!important;">Thank you for your purchase.</p>
                //       //                     <p style="font-size:21px;font-weight: 600!important;font-family: Verdana, sans-serif !important;margin:14px 0;border: 2px dotted green;width: fit-content;margin: 15px auto;padding: 6px;border-radius: 8px;">
                //       //                       Ticket ID #' . $ticket->ticketNo . '
                //       //                     </p>
                //       //                   </td>
                //       //                 </tr>
                //       //                 <tr>
                //       //                   <td>
                //       //                       <table style="margin: auto;border-collapse: collapse;border: 1px solid #088b42;width:90%;max-width:480px;" border="1" cellspacing="2" cellpadding="0">
                //       //                           <tbody>
                //       //                             <tr>

                //       //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width: 13%;background: #171f4f;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Valid Up To</strong></th>
                //       //                               <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Raffle ID</strong></th>

                //       //                             </tr>
                //       //                             <tr>
                //       //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . ($checkout_response['noOfDays'] > 1 ? $checkout_response['noOfDays'] . ' Days' : $checkout_response['noOfDays'] . ' Day') . '<br>
                //       //                                   <small>' . date('d F Y', strtotime($checkout_response['endDate'])) . '</small></strong></td>
                //       //                               <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . implode('<br>', $raffleIDs) . '</strong></td>
                //       //                             </tr>
                //       //                           </tbody>
                //       //                         </table>
                //       //                   </td>
                //       //                 </tr>
                //       //                 <tr>
                //       //                   <td style="color: #111111; padding: 20px 14px; " align="center" valign="top" bgcolor="#ffffff">
                //       //                     <h3 style="color: #01104e;  font-size: 20px; margin: 0px;font-family: Verdana, sans-serif !important;">
                //       //                       Total Amount:
                //       //                       <span class="gmail-otp-bg" style="color: #088b42;font-family: Verdana, sans-serif !important;">AED
                //       //                         ' . number_format($finaltotal, 2) . '</span>
                //       //                       <br>
                //       //                     </h3>
                //       //                   </td>
                //       //                 </tr>
                //       //           <tr>
                //       //                   <td style="margin: auto !important;">
                //       //                     <table style="margin:auto!important;" border="0" cellspacing="0" cellpadding="0">
                //       //                       <tbody>
                //       //                         <tr>
                //       //                           <td style="padding: 0px 0px 0px 2px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:40%;" align="center" valign="top" bgcolor="#ffffff">
                //       //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                //       //                               <a href="' . $printurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Ticket</a>
                //       //                             </h3>
                //       //                           </td>
                //       //                       <!-- <td style="padding: 0px 0px 0px 30px; border-radius: 4px 4px 0px 0px; font-size: 24px; line-height: 24px;width:44%;" align="center" valign="top" bgcolor="#ffffff">
                //       //                             <h3 style="color: #ffffff; font-size: 17px; margin: 0px;  padding: 8px 5px 10px 5px; background: #ffffff;color: #01104e;;  line-height: 1; border-radius: 5px;border: 2px solid #088b42;">
                //       //                               <a href="' . $invoiceurl . '" style="text-transform: uppercase; color: #01104e; text-decoration-line: none; font-family: Poppins, sans-serif !important; cursor: pointer;" contenteditable="false">View Invoice</a>
                //       //                             </h3>
                //       //                           </td> -->
                //       //                         </tr>
                //       //                         <br>
                //       //                       </tbody>
                //       //                     </table>
                //       //                     <br>
                //       //                   </td>
                //       //                 </tr>
                //       //               <tr>
                //       //                  <td>
                //       //                     <ul
                //       //                        style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                //       //                        <li>• Thrill Draw win up to 24 Grams of Gold</li>
                //       //                        <li>• Booster Draw win up to 100 Grams of Gold </li>
                //       //                        <li>• Bumper Draw win up to 1000 Grams of  Gold</li>
                //       //                     </ul>
                //       //                  </td>
                //       //               </tr>

                //       //               <tr>
                //       //                   <td class="column-one">
                //       //                      <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                //       //                   </td>
                //       //                </tr>
                //       //               <tr>
                //       //                  <td>
                //       //                     <p
                //       //                        style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                //       //                        Note: This is a system auto-generated email. Please do not reply to this mail.
                //       //                     </p>
                //       //                  </td>
                //       //               </tr>
                //       //               <tr>
                //       //               <td class="column-one" style="background: #171f4f; height:10px;">
                //       //               </td>
                //       //               </tr>
                //       //            </table>
                //       //            <!-- End Main Class -->
                //       //         </center>
                //       //         <!-- End Wrapper -->
                //       //      </body>
                //       //   </html>';

                //       // if (isset($email) && $email != '') {
                //       //   $emailchack = explode('@', $email);
                //       //   if (strtolower($emailchack[1]) != "nationaldrawuae.com") {
                //       //     $sendEmail = Controller::composeEmail(($request->ip() ?? ''), $email, $subject, $messages);
                //       //   }
                //       // }




                //       // if ($mobile != '' && $mobile != null) {


                //       // $startDateDraw = DB::table('draw')
                //       //   ->where([
                //       //     ['saleDate', '=', date('Y-m-d', strtotime($checkout_response['startDate']))],
                //       //     ['deletes', '=', '0'],
                //       //     // ['dailyThirllStatus', '=', 'Active']
                //       //   ])
                //       //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                //       //   ->orderBy('saleDate', 'ASC')
                //       //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                //       //   ->limit(1)
                //       //   ->get();


                //       // if ($startDateDraw->count() > 0) {
                //       //   $checkout_response['startDate'] = $startDateDraw[0]->resultDate;
                //       // }

                //       // $whatsAppArr = [
                //       //   'mobile' => $mobile,
                //       //   // 'templateName' => 'final_shipping_order_confirm_v3',
                //       //   'language' => 'en',

                //       //   'templateBodyParam' => [
                //       //     ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? ''),
                //       //     date('d M Y', strtotime($payment_history[0]->createdon)),
                //       //     $checkout_response['noOfDays'] . " Day" . ($checkout_response['noOfDays'] > 1 ? 's' : ''),
                //       //     date('d M Y', strtotime($checkout_response['startDate'])),
                //       //     date('d M Y', strtotime($checkout_response['endDate']))
                //       //   ],
                //       //   // 'messages' => $messages,
                //       //   'buttons' => [
                //       //     [
                //       //       'type' => 'URL',
                //       //       'parameter' => $transaction_id
                //       //     ]
                //       //   ],
                //       //   'templateName' => 'ticket_purchase_customer_template_v3'
                //       // ];


                //       // $whatsAppArr['messages'] = 'Hi, ' . $whatsAppArr['templateBodyParam'][0] . ' 🛍️Thank you for your Purchase on ' . $whatsAppArr['templateBodyParam'][1] . '! Your order has been confirmed. Draw valid for ' . $whatsAppArr['templateBodyParam'][2] . ' from ' . $whatsAppArr['templateBodyParam'][3] . ' to ' . $whatsAppArr['templateBodyParam'][4] . '. Wish you a Best of Luck☘️! Click below to view your ticket!👇' . $printurl;

                //       // $sendWhatsapp = Controller::sendNotification($whatsAppArr);

                //       // $sendWhatsapp = Controller::sendWhatsApp($whatsAppArr);
                //       // }

                //       // if ($checkout_response['payByLinkID'] != null) {
                //       //   $updatePayLink = DB::table('payby_link')
                //       //     ->where('id', $checkout_response['payByLinkID'])
                //       //     // ->where('type', $type)
                //       //     // ->where('status', '!=', 'Paid')
                //       //     ->where(function ($query) {
                //       //       $query->where('status', '!=', 'Paid')
                //       //         ->orWhereNull('status');
                //       //     })
                //       //     ->where('user_id', $user_id)
                //       //     ->where('Newpayment_id', $payment_history[0]->id)
                //       //     ->where('deletes', '0')
                //       //     ->update(['status' => 'Paid']);
                //       // }



                //       $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                //       $response = ['status' => 'success', 'message' => 'The CRM Plan updated successfully', 'data' => $data];
                //       goto returnFVI;
                //     } else {

                //       $response = ['status' => 'failed', 'message' => 'The update process failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                //       goto returnFVI;
                //     }
                //     // } 

                //     // else {

                //     //   $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                //     //   goto returnFVI;
                //     // }
                //   } else {

                //     // Log
                //     // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                //     // $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                //     $response = ['status' => 'failed', 'message' => 'This crm has been already generated', 'error' => $data];
                //     goto returnFVI;
                //   }
                // } 
                else {
                    $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                    goto returnFVI;
                }
            } else {

                // Log
                $log = Controller::error_log_new(($request->ip() ?? ''), 'OnlineTicketGenerate_API', $user_id, '', '', 'Query Result', json_encode($payment_history), __DIR__, basename(__FILE__), __LINE__);



                $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }


            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function buyTrailCRM_mobile(Request $request)
    {

        try {
            $response = [];
            $input = $request->all();
            $data = [];
            $transaction_id = $request->transaction_id;

            $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
            if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            $user_id = auth()->user()->id;
            if ($user_id == '' || $user_id == null || $user_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }
            
            $check_user = DB::table('kyc_details')->where(['user_id' => $user_id, 'type' => 'Driver', 'deletes' => 0])->exists();
            if($check_user){
                $response = ['status' => 'failed', 'message' => 'Service not available for your account.', 'error' => 'Service not available for your account.'];
                goto returnFVI;
            }

            if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }

            $name = auth()->user()->name;
            $mobile = auth()->user()->mobile;
            $email = auth()->user()->email;
            $address = trim(DB::connection()->getPdo()->quote(auth()->user()->address), "'");
            $city = trim(DB::connection()->getPdo()->quote(auth()->user()->city), "'");
            $nationality = trim(DB::connection()->getPdo()->quote(auth()->user()->residinglocation), "'");



            $checkTrail = DB::table('invoice')
                ->where('user_id', $user_id)
                ->where('deletes', '0')
                ->where('planType', 'TRAIL')
                ->orderBy('id', 'DESC')
                ->get();

            if ($checkTrail->count() > 0) {

                $response = ['status' => 'failed', 'message' => 'The transaction track dd missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                goto returnFVI;
            }




            $payment_history = DB::table('subscriptions')
                ->select('*')
                ->where('subscription_id', 'LIKE', $transaction_id)
                // ->whereIn('paymentStatus', ['paid'])
                ->where('user_id', $user_id)
                ->where('planType', 'TRAIL')
                ->where('status', '0')
                ->whereIn('purchaseType', ['NEW', 'RENEWAL'])

                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();

            // dd($payment_history);

            // dd($payment_history->count());

            if ($payment_history->count() > 0) {

                if ($payment_history[0]->purchaseType === 'NEW') {

                    $oticket = DB::table('crm')
                        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                        ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
                        ->where('userID', $user_id)
                        ->where('deletes', '0')
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();

                    $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get();



                    if ($oticket->count() < 1 && $invoiceRecheck->count() < 1) {

                        $user_id = $payment_history[0]->user_id;
                        $checkout_response = json_decode($payment_history[0]->checkout_response, true);

                        $crmRefernce = 'CRM' . md5($payment_history[0]->id . ' ' . time() . ' ' . $transaction_id);

                        $finaltotal = $payment_history[0]->finaltotal;

                        $onepercen = floatval($finaltotal) / 105;
                        $total_amount = floatval($onepercen) * 100;
                        $tax_value = floatval($finaltotal) - floatval($total_amount);

                        $Arr = [
                            "userID" => $user_id,
                            "subscription_id" => $payment_history[0]->id,
                            "crmRefernce" => $crmRefernce,
                            "deletes" => '0',
                            "createdon" => now(),
                            "updatedon" => now(),
                            "expiryDate" => $checkout_response['expiryDate'],
                            "currentPlanBenefits" => json_encode($checkout_response),
                             "sub_status" => 'active'
                        ];

                        $ticketId = DB::table('crm')->insertGetId($Arr);

                        $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                        if ($ticket && $ticket->id != null && $ticket->id != '') {

                            $invoiceArr = [
                                "crmID" => $ticket->id,
                                 "subscription_id" => $payment_history[0]->id,
                                "user_id" => $user_id,
                                "product_id" => $checkout_response['productID'],
                                "totalAmt" => number_format($finaltotal, 2, ".", ""),
                                "taxPercentage" => 0,
                                "taxValue" => 0,
                                "netTotal" => $finaltotal,
                                "firstname" => $name,
                                "lastname" => auth()->user()->lname ?? '',
                                "emailid" => $email,
                                "address" => $address,
                                "city" => $city,
                                "country" => $nationality,
                                "startDate" => $checkout_response['startDate'],
                                'endDate' => $checkout_response['expiryDate'],
                                'deletes' => '0',
                                'createdon' => now(),
                                'planType' => $payment_history[0]->planType,
                                'purchaseType' => $payment_history[0]->purchaseType,
                                'payment_transaction_id' => $transaction_id,
                                'paymentType' => 'Card',
                                'mobile' => $mobile,
                                'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                                'discountID' => 0,
                                'cart' => json_encode($checkout_response),
                                'shipamount' => $payment_history[0]->shipamount,
                                'grandtotal' => $payment_history[0]->grandtotal,
                                "sub_status" => 'active',
                                "currency" => $checkout_response['currency'] ?? null
                            ];

                            $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                            $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                            if ($invoice->id == null || $invoice->id == '') {
                                $response = ['status' => 'failed', 'message' => 'The invoice generation failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                                goto returnFVI;
                            }

                            // dd($invoiceid);




                            // dd($raffleIDs);

                            $ticketUpdateArr = [
                                // "raffleIds" => json_encode($raffleIDs),
                                "invoiceID" => json_encode([$invoice->id]),
                            ];

                            $ticketUpdate = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->update($ticketUpdateArr);


                            $paymentArr = [
                                "status" => '1',
                                "crmID" => $ticket->id,
                                // "draw_id" => $draw_id,
                                // "ticketReferenceID" => $ticket->referenceID,
                                "invoiceID" => $invoiceid,
                                'paymentStatus' => "SUCCESS",
                                  "sub_status" => 'active',
                                  "gateway" => "goride",
                                  "cycles_paid" => 1
                            ];


                            $payUpdate = DB::table('subscriptions')
                                ->where('id', '=', $payment_history[0]->id)
                                ->where('status', '0')
                                ->update($paymentArr);








                            if ($payUpdate && $ticketUpdate) {

                                // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "ticket-view/" . $transaction_id);
                                // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . ($request->header('Origin') . '/') . "invoice/" . $transaction_id);

                                // $printurl = $printurlf->getBody()->getContents();
                                // $invoiceurl = $printinvoice->getBody()->getContents();

                                // $subject = "Purchase Confirmation - " . strtoupper(date("d-m-Y g:i a"));
                                
                                    $subject = "Purchase Confirmation";

                                $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Purchase Confirm Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff !important;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/purchase_confirm_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower(auth()->user()->name)) . ' ' . (ucfirst(strtolower(auth()->user()->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Thank you for choosing <b>Go Ride!</b></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Your plan has been successfully purchased, </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">and you`re all set to take your business to the next level.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Log in to your <a href="' . ($request->header('Origin') . '/') . '" style="color:#002d72;">dashboard</a> to <br> start managing your rides efficiently.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Explore all the features that <br> come with your Go Ride plan.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="esd-structure es-p20" align="left" style="padding:20px 0;">
                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="560" class="esd-container-frame" align="center" valign="top">
                                                                                <table cellpadding="0" cellspacing="0" width="100%">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td align="center" style="padding:0;Margin:0">
                                                                                                <a href="' . ($request->header('Origin') . '/') . '" class="msohide es-button-border">Log In to Dashboard</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                         <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . (env('APP_URL') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on <a href="tel:'. env('SUPPORT_MOBILE') .'">'. env('SUPPORT_MOBILE') .'</a>, email at <a href="mailto:'. env('SUPPORT_EMAIL') .'">'. env('SUPPORT_EMAIL') .'</a></p>
                                                                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                              <tr>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_FB') .'"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_YOUTUBE') .'"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0"><a href="'. env('SUPPORT_INSTA') .'"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                        </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';


                                if (isset($email) && $email != '') {
                                    $emailchack = explode('@', $email);
                                    if (strtolower($emailchack[1]) != "goride.run") {
                                        $sendEmail = Controller::composeEmail(($request->ip() ?? ''), $email, $subject, $messages);
                                    }
                                }
                                
                                $data['crmIDs'] = $ticket->id;
                                $data['crmIDs'] = $ticket->id;
                                
                                $dom_pre = 'DS' . str_pad(mt_rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT) . date('d');
                                $data['redirectURL'] =  'https://'.$dom_pre.'.goride.net.in';
                                
                                $new_req = [
                                    'crmID' => $ticket->id,
                                    'domainPrefix' => $dom_pre,
                                    // 'userName' => auth()->user()->mobile,
                                    'userName' => (substr(auth()->user()->mobile, 0, 2) == '91') ? substr(auth()->user()->mobile, 2) : auth()->user()->mobile,
                                    'passWord' => auth()->user()->password
                                ];
                                
                                $authHeader = $request->header('Authorization');
                                
                                $response = Http::withHeaders([
                                                'Authorization' => $authHeader
                                            ])->post(env('APP_API') . 'generateCRM-mobile', $new_req);
                                            
                                $resData = $response->json();
                                
                                // dd($resData);
                                
                                if ($response->successful() && isset($resData['status']) && $resData['status'] === 'success') {
                                    // Handle success
                                    $response = ['status' => 'success', 'message' => 'Your CRM will be generated shortly. Please wait a few minutes, then log in using your registered mobile number and password.', 'data' => $data];
                                    goto returnFVI;
                                    
                                } else {
                                    $response = ['status' => 'failed', 'message' => 'CRM generation process failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $ticket->id]];
                                    goto returnFVI;
                                    // Log or return error
                                }


                            } else {

                            }
                        } else {

                            $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                            goto returnFVI;
                        }
                    } else {

                        // Log
                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                        // $data['redirectURL'] = ($request->header('Origin') . '/') . 'thanks/' . $transaction_id;
                        $response = ['status' => 'failed', 'message' => 'Your crm has been already generated', 'error' => $data];
                        goto returnFVI;
                    }
                }
                else {
                    $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
                    goto returnFVI;
                }
            } else {

                // Log
                $log = Controller::error_log_new(($request->ip() ?? ''), 'OnlineTicketGenerate_API', $user_id, '', '', 'Query Result', json_encode($payment_history), __DIR__, basename(__FILE__), __LINE__);



                $response = ['status' => 'failed', 'message' => 'The transaction track missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'failed/' . $transaction_id]];
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
