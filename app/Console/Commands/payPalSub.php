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


class payPalSub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:paypal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle PayPal subscription renewals';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Logic for PayPal subscriptions
        // \Log::info('PayPal subscription command executed');
        $data = [];
        // PayPal API credentials
        $clientId = config('paypal.client_id');
        $clientSecret = config('paypal.client_secret');

        $razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
        $controller = new Controller();


        $subscriptions = DB::table('subscriptions as s')
            ->join(
                DB::raw('(SELECT subscription_id, COUNT(subscription_id) as totalInv 
                  FROM invoice i 
                  WHERE i.deletes = "0" 
                    AND i.subscription_id IS NOT NULL 
                  GROUP BY subscription_id) as i'),
                'i.subscription_id',
                '=',
                's.id'
            )
            ->where('s.cycles_paid', '>', 0)
            ->where('s.sub_status', '!=', 'cancelled')
            ->whereNotNull('s.sub_status')
            ->whereNotNull('s.subID')
            ->where('s.cycles_paid', '>', DB::raw('i.totalInv'))
            ->select('s.*', 'i.totalInv')
            ->get()->map(function ($item) {
                $item->checkout_response = json_decode($item->checkout_response, true);
                $item->subReq = json_decode($item->subReq, true);
                $item->subRes = json_decode($item->subRes, true);

                return $item;
            });


        if ($subscriptions->count() > 0) {


            foreach ($subscriptions as $key => $value) {

                $subID = $value->subID;

                if (isset($subID) && $subID != null) {
                    $this->line('Subscription ID: ' . $value->id);


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

                        if ($user) {



                            json_encode($user);



                            $name = $user->name;
                            $mobile = $user->mobile;
                            $email = $user->email;
                            $address = trim(DB::connection()->getPdo()->quote($user->address), "'");
                            $city = trim(DB::connection()->getPdo()->quote($user->city), "'");
                            $nationality = trim(DB::connection()->getPdo()->quote($user->residinglocation), "'");

                            $cycles_paid = ($value->gateway === 'razorpay') ? ($getSubscription->paid_count) : (intval($value->total_cycles) - (intval($getSubscription['billing_info']['cycle_executions'][0]['cycles_remaining'] ?? intval($value->total_cycles))));



                            $getINV = DB::table('invoice')
                                ->where('subscription_id', $value->id)
                                ->where('deletes', '0')
                                ->where('user_id', $user_id)
                                ->orderBy('endDate', 'desc')
                                ->limit(1)
                                ->get()->map(function ($item) {
                                    $item->cart = json_decode($item->cart, true);
                                    
                                    // $item->subReq = json_decode($item->subReq, true);
                                    // $item->subRes = json_decode($item->subRes, true);
    
                                    return $item;
                                });

                            if ($getINV->count() > 0) {
                                $getINV = $getINV[0];


                                $ticket = DB::table('crm')->where('id', $getINV->crmID)->where("deletes", '0')->first();

                                if ($ticket && $ticket->id != null && $ticket->id != '') {

                                    $checkout_response = $getINV->cart;

                                    $endDate = $checkout_response['expiryDate'];


                                    // $startDate = Carbon::parse($getINV->endDate)->addDay();
                                    //$getINV->endDate;
 $startDate = Carbon::parse($getINV->endDate);

     $findDays =   ($value->planType === 'YEARLY' ? $controller->getDaysInMonthOrYear('year', $checkout_response['productDetails']['validityMorY'], $startDate)  :  $controller->getDaysInMonthOrYear('month', $checkout_response['productDetails']['validityMorY'], $startDate));
                                                
                                            

                                    $endDate = Carbon::parse($startDate)->addDays($findDays)->format('Y-m-d');






                                    $invoiceArr = [
                                        "crmID" => $getINV->crmID,
                                        // "crmID" => $ticketId,
                                        // "payment_history_id" => $value->id,
                                        // "draw_id" => $draw_id,
                                        "user_id" => $user_id,
                                        "product_id" => $getINV->product_id,
                                        "totalAmt" => $getINV->totalAmt,
                                        "taxPercentage" => $getINV->taxPercentage,
                                        "taxValue" => $getINV->taxValue,
                                        "netTotal" => $getINV->netTotal,
                                        "firstname" => $name,
                                        "lastname" => $user->lname ?? '',
                                        "emailid" => $email,
                                        "address" => $address,
                                        "city" => $city,
                                        "country" => $nationality,
                                        "startDate" => $startDate->format('Y-m-d'),
                                        'endDate' => $endDate,
                                        'deletes' => '0',
                                        'createdon' => now(),
                                        'planType' => $value->planType,
                                        'purchaseType' => $value->purchaseType,
                                        'payment_transaction_id' => $getINV->payment_transaction_id,
                                        'paymentType' => 'Card',
                                        'mobile' => $mobile,
                                        'discount' => number_format(floatval($getINV->discount), 2, ".", ""),
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

                                    //       $this->info(json_encode($invoiceArr));
                                    // return;



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


                                    $invoiceIDs = isset($ticket->invoiceID) && $ticket->invoiceID != null
                                        ? json_decode($ticket->invoiceID, true)
                                        : [];
                                    array_push($invoiceIDs, $invoice->id);




                                    $ticketUpdateArr = [
                                        'updatedon' => now(),
                                        // "raffleIds" => json_encode($raffleIDs),
                                        "invoiceID" => json_encode($invoiceIDs),
                                    ];
                                    
                                    
                                    //   $this->info(json_encode($invoiceid));
                                    //     return;

                                    $ticketUpdate = DB::table('crm')->where('id', $ticket->id)->where("deletes", '0')->update($ticketUpdateArr);


                                    $subInvoiceIDs = isset($value->invoiceIDs) && $value->invoiceIDs != null
                                        ? json_decode($value->invoiceIDs, true)
                                        : [];

                                    array_push($subInvoiceIDs, $invoice->id);


                                    $paymentArr = [
                                        // "status" => '1',
                                        // "crmID" => $ticket->id,
                                        'cycles_paid' => $cycles_paid,
                                        'paymentStatus' => $razStatus,
                                        'sub_status' => $razStatus,
                                        'crontime' => now(),
                                        'updatedon' => now(),
                                        // 'invoiceID' => $invoice->id,
                                        "invoiceIDs" => json_encode($subInvoiceIDs),
                                    ];


                                    $payUpdate = DB::table('subscriptions')
                                        ->where('id', '=', $value->id)
                                        // ->where('status', '0')
                                        ->update($paymentArr);







                                    if ($payUpdate && $ticketUpdate) {

 $this->line('The Invoice Created Successfully! Subscription ID: ' . $value->id);

                                        // $this->info(json_encode($invoiceid));
                                        // return;

                                    } else {
                                        $paymentArr = [

                                            'crontime' => Carbon::now(),

                                        ];
                                        $payUpdate = DB::table('subscriptions')
                                            ->where('id', '=', $value->id)
                                            // ->where('status', '0')
                                            // ->whereNotIn('sub_status', ['cancelled'])->orWhereNull('sub_status')
                                            ->update($paymentArr);
                                        $this->line('The Update process failed! Subscription ID: ' . $value->id);
                                    }







                                    /////

                                } else {
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

            }
        }




    }
}
