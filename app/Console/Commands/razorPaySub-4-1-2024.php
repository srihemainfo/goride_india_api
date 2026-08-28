<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Exception;
use Razorpay\Api\Api;
use SebastianBergmann\Type\FalseType;
use App\Models\user_register;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class razorPaySub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:razorpay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle RazorPay subscription renewals';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Logic for RazorPay subscriptions
        // \Log::info('RazorPay subscription command executed');



        try {
            $data = [];
            // PayPal API credentials
            $clientId = config('paypal.client_id');
            $clientSecret = config('paypal.client_secret');

            $razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
            $controller = new Controller();



            $subscriptions = DB::table('subscriptions')
                ->whereNotNull('paymentStatus')
                // ->where('purchaseType', 'NEW')

                // ->where('gateway', 'razorpay')
                ->where('gateway', '!=', '')
                ->whereNotNull('subID')
                
                
                //   ->where('planType',  '!=', 'TRAIL')
                
                 ->whereIn('gateway', ['razorpay', 'paypal'])
                   
                   
                // ->where('currency', 'INR')


                // ->where('crontime', '<', DB::raw('NOW() - INTERVAL 30 MINUTES'))
                // ->where('crontime', '>=', DB::raw('NOW() - INTERVAL 1 DAY')) 
                // ->whereDate('crontime', '<', DB::raw('CURDATE()'))

                ->whereRaw('DATE(crontime) < CURDATE()')
                // ->whereNotIn('sub_status', ['cancelled'])
                // ->orWhereNull('sub_status')
                ->where(function ($query) {
                    $query->whereNotIn('sub_status', ['cancelled'])
                        ->orWhereNull('sub_status');
                })

                ->orderBy('crontime', 'asc')
                ->limit(7)
                ->get()
                ->map(function ($item) {

                    $item->checkout_response = json_decode($item->checkout_response, true);
                    $item->subReq = json_decode($item->subReq, true);
                    $item->subRes = json_decode($item->subRes, true);

                    return $item;
                });

            // $this->info(json_encode($subscriptions));
            // return;

            if ($subscriptions->count() > 0) {

                foreach ($subscriptions as $key => $value) {
                    // $this->info(json_encode($value));
                    // return;

                    $subID = $value->subID;

                    $this->line('Subscription ID: ' . $value->id);


                    $purchaseType = $value->purchaseType;



                    if (isset($subID) && $subID != null) {

                        $getSubscription = null;



                        if ($value->gateway === 'razorpay') {
                            $getSubscription = $razorpay->subscription->fetch($subID);
                        } else {

                           
                            $response1 = Http::withHeaders([
                                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                                'Content-Type' => 'application/x-www-form-urlencoded'
                            ])
                                ->asForm()
                                ->post(config('paypal.base_url') . '/v1/oauth2/token', [
                                    'grant_type' => 'client_credentials',
                                    'ignoreCache' => 'true',
                                    'return_authn_schemes' => 'true',
                                    'return_client_metadata' => 'true',
                                    'return_unconsented_scopes' => 'true',
                                ]);



                        
                            if ($response1->successful()) {
                                $data1 = $response1->json();
                                $accessToken = $data1['access_token'];

                                if (!isset($accessToken) || $accessToken == '') {
                                    $response = [
                                        'status' => 'failed',
                                        'message' => 'Please kindly contact the support.',
                                        'error' => 'Failed to get access token'
                                    ];
                                    
                                }




                                $getPaymentData = Http::withHeaders([
                                    'Authorization' => 'Bearer ' . $accessToken,

                                    'Content-Type' => 'application/json',

                                ])->get(config('paypal.base_url') . '/v1/billing/subscriptions/' . $subID, null);




                                if ($getPaymentData->successful()) {
                                    $getSubscription = $getPaymentData->json();
                                
                                }

                            }


                        }




                        if (isset($getSubscription) && (isset($getSubscription->id) || isset($getSubscription['id']))) {
                            $razStatus = $getSubscription->status ?? $getSubscription['status'] ?? '';
                            // if (isset($razStatus) && $razStatus === 'active') {
                            
                            //  $this->info($razStatus);
                            // return;
                            
                            
                            $crmID = $value->crmID;
                            $user_id = $value->user_id;
                            $user = user_register::find($user_id);
                            
                            if($user){
                                
                           
                            
                            json_encode($user);

  

                            $name = $user->name;
                            $mobile = $user->mobile;
                            $email = $user->email;
                            $address = trim(DB::connection()->getPdo()->quote($user->address), "'");
                            $city = trim(DB::connection()->getPdo()->quote($user->city), "'");
                            $nationality = trim(DB::connection()->getPdo()->quote($user->residinglocation), "'");
                            
                       $cycles_paid =     ($value->gateway === 'razorpay') ?( intval($value->total_cycles) - intval($getSubscription->remaining_count)) : (intval($value->total_cycles) - (intval($getSubscription['billing_info']['cycle_executions'][0]['cycles_remaining'] ?? intval($value->total_cycles))));

                            $paymentArr = [
                                'cycles_paid' => $cycles_paid,
                                'sub_status' => $razStatus,
                                'paymentStatus' => $razStatus,
                                'crontime' => now(),
                                'updatedon' => now(),
                            ];


                            $transaction_id = $value->subscription_id;
                            if (isset($crmID) && $crmID != 0 && $crmID > 0) {



                                $payUpdate = DB::table('subscriptions')
                                    ->where('id', '=', $value->id)
                                    // ->where('status', '0')
                                    ->update($paymentArr);

                                $ticketUpdateArr = [
                                    "updatedon" => now(),
                                    "sub_status" => $razStatus,
                                ];

                                $ticketUpdate = DB::table('crm')->where('id', $crmID)->where("deletes", '0')->update($ticketUpdateArr);

                                if ($payUpdate && $ticketUpdate) {
                                    $this->line('The Status Successfully updated!');
                                } else {
                                    $this->line('The Status Update Process Failed!');
                                }


                            } else {




                                if ($purchaseType === 'NEW' && isset($razStatus) && strtolower($razStatus) === 'active') {



                                    $oticket = DB::table('crm')
                                        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
                                        // ->whereRaw("JSON_CONTAINS(transactionID, '{$value->id}', '$')")
                                        
                                        ->where('subscription_id', $value->id)
                                        ->where('userID', $user_id)
                                        ->where('deletes', '0')
                                        ->orderBy('id', 'DESC')
                                        ->limit(1)
                                        ->get();

                                    $invoiceRecheck = DB::table('invoice')->where('payment_transaction_id', $transaction_id)->where('deletes', '0')->orderBy('id', 'DESC')
                                        ->limit(1)
                                        ->get();

                                    if ($oticket->count() < 1 && $invoiceRecheck->count() < 1) {


                                      //  Log
                                        $log = $controller->error_log_new('', 'Subcription_crm_generate', $user_id, '', '', 'CRM generation process started!', json_encode([
                                            'subscription_id' => $value->id,
                                            
                                            ]), __DIR__, basename(__FILE__), __LINE__);

                                        
                                        $checkout_response = $value->checkout_response;
$crmID = $checkout_response['crmID'] ?? null;

    // $this->info($crmID); 
    // return;

                                        // Log
                                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'strated', $user_id, '', '', 'Draw ID:' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                                        $crmRefernce = 'CRM' . md5($value->id . ' ' . time() . ' ' . $transaction_id);

                                        $finaltotal = $value->finaltotal;
                                        
                                        $taxPercentage = 0;
$total_amount = $finaltotal;
$tax_value = 0;

                                    
                                        
                                       if(isset($checkout_response['currency']) && $checkout_response['currency'] === 'INR'){
                                           $taxPercentage = 18;
                                               $onepercen = floatval($finaltotal) / (100 + $taxPercentage);
                                               $total_amount = floatval($onepercen) * 100;
                                               $tax_value = floatval($finaltotal) - floatval($total_amount);
                                       }
                                        
                                        

                                        // dd($checkout_response);
                                        // die;

                                        $Arr = [
                                            "userID" => $user_id,
                                            // "transactionID" => json_encode([intval($value->id)]),
                                            "crmRefernce" => $crmRefernce,
                                            "subscription_id" => $value->id,
                                            "sub_status" => $razStatus,
                                            // "sale_from" => 1,
                                            // "purchaseDatetime" => $subscriptions->createdon,
                                            // "totalAmt" => number_format($total_amount, 2, ".", ""),
                                            // "taxPercentage" => 5,
                                            // "taxValue" => number_format($tax_value, 2, ".", ""),
                                            // "netTotal" => $finaltotal,
                                            // "transactionIds" => json_encode(["$transaction_id"]),
                                            // "totalRaffle" => $checkout_response['totalRaffles'],
                                            // "paymentHistoryIds" => json_encode([$subscriptions->id]),
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
                                            // 'shipamount' => $subscriptions->shipamount,
                                            // 'grandtotal' => $subscriptions->grandtotal
                                        ];

if(isset($crmID) && $crmID != null && $crmID != '') {
    
    
    unset($Arr['crmRefernce']);
     unset($Arr['createdon']);

    
     $updateCRM = DB::table('crm')
    ->where('id', $crmID)
    ->where('deletes', '0')
    ->update($Arr);
    
    if(!$updateCRM) {
         $response = ['status' => 'failed', 'message' => 'The CRM Update process failed!', "error" => []];
                                         
         $this->info('Error: ' . json_encode($response));
         return;
    }
    
    $ticketId = $crmID;
} else {
     $ticketId = DB::table('crm')->insertGetId($Arr);
}


                                       






                                        $ticket = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->first();

                                        if ($ticket && $ticket->id != null && $ticket->id != '') {

                                            $invoiceArr = [
                                                "crmID" => $ticket->id,
                                                // "crmID" => $ticketId,
                                                // "payment_history_id" => $value->id,
                                                // "draw_id" => $draw_id,
                                                "user_id" => $user_id,
                                                "product_id" => $checkout_response['productID'],
                                                "totalAmt" => number_format($total_amount, 2, ".", ""),
                                                "taxPercentage" => $taxPercentage,
                                                "taxValue" => number_format($tax_value, 2, ".", ""),
                                                "netTotal" => $finaltotal,
                                                "firstname" => $name,
                                                "lastname" => $user->lname ?? '',
                                                "emailid" => $email,
                                                "address" => $address,
                                                "city" => $city,
                                                "country" => $nationality,
                                                "startDate" => $checkout_response['startDate'],
                                                'endDate' => $checkout_response['expiryDate'],
                                                'deletes' => '0',
                                                'createdon' => now(),
                                                'planType' => $value->planType,
                                                'purchaseType' => $value->purchaseType,
                                                'payment_transaction_id' => $transaction_id,
                                                'paymentType' => 'Card',
                                                'mobile' => $mobile,
                                                'discount' => number_format(floatval($checkout_response['discountAmt']), 2, ".", ""),
                                                'discountID' => 0,
                                                'cart' => json_encode($checkout_response),
                                                'shipamount' => $value->shipamount,
                                                'grandtotal' => $value->grandtotal,
                                                "subscription_id" => $value->id,
                                                "subscrID" => $value->subID,
                                                 "sub_status" => $razStatus,
                                                 "currency" => $checkout_response['currency'] ?? null
                                                
                                                // 'delivery_status' => ($checkout_response['shipping'] === 'deliveryToMe' ? 'requested'  : null),
                                                // 'deliveryType' => $checkout_response['shipping'],
                                                // 'delivery_status' => null,

                                            ];



                                            $invoiceid = DB::table('invoice')->insertGetId($invoiceArr);


                                            $invoice = DB::table('invoice')->where('id', $invoiceid)->where('deletes', '0')->first();

                                            if ($invoice->id == null || $invoice->id == '') {
                                                $response = [
                                                    'status' => 'failed',
                                                    'message' => 'The invoice generation failed!',
                                                    "error" => [

                                                        // 'redirectURL' => (env('APP_URL') . '/') . 'failed/' . $transaction_id
                                                    ]

                                                ];
                                                // goto returnFVI;

                                                $this->info('Error: ' . json_encode($response));
                                                // return; 
                                            }




                                            // dd($invoiceid);


$invoiceIDs = isset($ticket->invoiceID) && $ticket->invoiceID != null 
    ? json_decode($ticket->invoiceID, true) 
    : [];

array_push($invoiceIDs, $invoice->id);


                                            // dd($raffleIDs);

                                            $ticketUpdateArr = [
                                                // "raffleIds" => json_encode($raffleIDs),
                                                "invoiceID" => json_encode($invoiceIDs),
                                            ];

                                            $ticketUpdate = DB::table('crm')->where('id', $ticketId)->where("deletes", '0')->update($ticketUpdateArr);


                                            $paymentArr = [
                                                "status" => '1',
                                                "crmID" => $ticket->id,

                                          'cycles_paid' => $cycles_paid,
                                                'paymentStatus' => $razStatus,
                                                'sub_status' => $razStatus,
                                                'crontime' => now(),

                                                'updatedon' => now(),
                                               'invoiceID' => $invoice->id
                                                // "invoiceIDs" => json_encode([$invoice->id]),
                                            ];


                                            $payUpdate = DB::table('subscriptions')
                                                ->where('id', '=', $value->id)
                                                ->where('status', '0')
                                                ->update($paymentArr);







                                            if ($payUpdate && $ticketUpdate) {

                                                // $printurlf = Http::get('http://tinyurl.com/api-create.php?url=' . (env('APP_URL') . '/') . "ticket-view/" . $transaction_id);
                                                // $printinvoice = Http::get('http://tinyurl.com/api-create.php?url=' . (env('APP_URL') . '/') . "invoice/" . $transaction_id);

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
                                                                                                                            <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . (env('APP_URL') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
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
                                                                                                                                        <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower($user->name)) . ' ' . (ucfirst(strtolower($user->lname)) ?? '') . '</strong></p>
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
                                                                                                                                        <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Log in to your <a href="' . (env('APP_URL') . '/') . '" style="color:#002d72;">dashboard</a> to <br> start managing your rides efficiently.</p>
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
                                                                                                                                                                        <a href="' . (env('APP_URL') . '/') . '" class="msohide es-button-border">Log In to Dashboard</a>
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




                                                if (isset($email) && $email != '') {
                                                    $emailchack = explode('@', $email);
                                                    if (strtolower($emailchack[1]) != "goride.run") {
                                                        $sendEmail = $controller->composeEmail('', $email, $subject, $messages);
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
                                                //     ucfirst(strtolower($user->name)) . ' ' . (ucfirst(strtolower($user->lname)) ?? ''),
                                                //     date('d M Y', strtotime($subscriptions->createdon)),
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
                                                //     ->where('Newpayment_id', $subscriptions->id)
                                                //     ->where('deletes', '0')
                                                //     ->update(['status' => 'Paid']);
                                                // }



                                                // $data['redirectURL'] = (env('APP_URL') . '/') . 'thanks/' . $transaction_id;
                                                $response = ['status' => 'success', 'message' => 'The CRM ID Generated Successfully', 'data' => $data];
                                                // goto returnFVI;
                                                // return false;
                                                $this->info('Error: ' . json_encode($response));
                                                // return;
                                            } else {

                                                $response = ['status' => 'failed', 'message' => 'The update process failed!', "error" => []];
                                                // goto returnFVI;
                                                // return false;
                                                $this->info('Error: ' . json_encode($response));
                                                // return;
                                            }
                                        } else {

                                            $response = ['status' => 'failed', 'message' => 'The ticket insert failed!', "error" => []];
                                            // goto returnFVI;
                                            $this->info('Error: ' . json_encode($response));
                                            // return;
                                        }
                                    } else {

                                        // Log
                                        // $log = Controller::error_log_new(($request->ip() ?? ''), 'Ticket_already_genereated', $user_id, '', '', 'Ticket Already Generated' . $draw_id, json_encode($request->all()), __DIR__, basename(__FILE__), __LINE__);

                                        // $data['redirectURL'] = (env('APP_URL') . '/') . 'thanks/' . $transaction_id;
                                        $response = ['status' => 'failed', 'message' => 'This crm has been already generated', 'error' => $data];
                                        // goto returnFVI;
                                        // return false;
                                        $this->info('Error: ' . json_encode($response));
                                        // return;
                                    }
                                } else {


                          


                                    $payUpdate = DB::table('subscriptions')
                                        ->where('id', '=', $value->id)
                                        // ->where('status', '0')
                                        ->update($paymentArr);

                                    // $ticketUpdateArr = [
                                    //     "updatedon" => now(),
                                    //     "sub_status" => $razStatus,
                                    // ];

                                    // $ticketUpdate = DB::table('crm')->where('id', $crmID)->where("deletes", '0')->update($ticketUpdateArr);

                                    if ($payUpdate) {
                                        $this->line('The Status Successfully updated!');
                                    } else {
                                        $this->line('The Status Update Process Failed!');
                                    }

                                    $this->line("The status: $razStatus!");
                                }

                            }



 }else {

                            $paymentArr = [
                               
                                'crontime' => Carbon::now(),
                               
                            ];
                            $payUpdate = DB::table('subscriptions')
                                ->where('id', '=', $value->id)
                                // ->where('status', '0')
                                // ->whereNotIn('sub_status', ['cancelled'])->orWhereNull('sub_status')
                                ->update($paymentArr);
                            $this->line('The User Not found! Subscription ID: ' . $value->id);
                        }


                          
                        } else {

                            $paymentArr = [
                               
                                'crontime' => Carbon::now(),
                               
                            ];
                            $payUpdate = DB::table('subscriptions')
                                ->where('id', '=', $value->id)
                                // ->where('status', '0')
                                // ->whereNotIn('sub_status', ['cancelled'])->orWhereNull('sub_status')
                                ->update($paymentArr);
                            $this->line('The Razor Pay not yet working!');
                        }





                    } else {

                        $paymentArr = [
                          
                            'crontime' => Carbon::now(),
                           
                        ];
                        $payUpdate = DB::table('subscriptions')
                            ->where('id', '=', $value->id)
                            // ->where('status', '0')
                            // ->whereNotIn('sub_status', ['cancelled'])->orWhereNull('sub_status')
                            ->update($paymentArr);

                    }
                    // } else {
                    //     $this->line('The Purchase Type not yet found!');
                    // }

                    // return false;
                }
            } else {


                $paymentArr = [
                   
                    'crontime' => Carbon::now()->subDay(),
                  
                ];
                $payUpdate = DB::table('subscriptions')
                    // ->where('id', '=', $value->id)
                    // ->where('status', '0')
                    // ->whereNotIn('sub_status', ['cancelled'])->orWhereNull('sub_status')
                    ->where(function ($query) {
                        $query->whereNotIn('sub_status', ['cancelled'])
                            ->orWhereNull('sub_status');
                    })
                    ->update($paymentArr);


                $this->line('No subscriptions track found');
            }



        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            // return response()->json($response);

            $this->error('Failed to process RazorPay subscription renewal');
            $this->line('Error: ' . json_encode($response));
            // $this->line('Error code: ' . $e->getCode());
        }
    }
}
