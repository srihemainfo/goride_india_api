<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;

use DB;
use Exception;
use Illuminate\Http\Request;

use App\Models\user_register;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Api\v2\billingController;

class getThanks extends Controller
{


  // Get User Product Cart Details
  public function getThanks(Request $request)
  {

    try {
      $response = [];
      $data = [];
      $input = $request->all();
      $transaction_id = $request->transaction_id;

      $transaction_id = Controller::BlockSQLInjection($transaction_id);
      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Invalid transaction ID. Please use a valid transaction ID.', 'error' => 'Invalid transaction ID. Please use a valid transaction ID.'];
        goto returnFVI;
      }




      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      // if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Transaction id Required!', 'error' => 'Kindly check the Order Reference!'];
      //   goto returnFVI;
      // }



      $data['errorText'] = '';

      $paymentHistory = DB::table('payment_history')
        ->select('id',  'paymentLogID', 'ticketReferenceID',  'category', 'invoice_no', 'renewalStatus', 'checkout_response', 'status')
        ->where('transaction_id', '=', $transaction_id)
        // ->where('status', '1')
        ->where('user_id', $user_id)
        ->first();


      // dd($paymentHistory);
      if ($paymentHistory) {

        $paymentLogID = $paymentHistory->paymentLogID;
        $ticketReferenceID = $paymentHistory->ticketReferenceID;
        $checkout_response = json_decode($paymentHistory->checkout_response, true);

        // dd();

        $paymentHistoryLog = DB::table('payment_history_log')
          ->select('id', 'response')
          ->where('id', '=', $paymentLogID)
          // ->where('status', '1')
          ->where('user_id', $user_id)
          ->first();

        if ($paymentHistoryLog) {
          $response = json_decode($paymentHistoryLog->response, true);
          if (isset($response['status_message'])) {
            $data['errorText'] = $response['status_message'];
          }
        }

        // dd($paymentHistory->status);

        if ($paymentHistory->status === '0') {

          $data['pageTitle'] = 'Unfortunately, your payment has failed. Please try again later';

          $response = ['status' => 'failed', 'message' => 'Invalid transaction ID. Please use a valid transaction ID.', 'error' => $data];
          goto returnFVI;
        }




        if ($paymentLogID != '' && $ticketReferenceID != '') {


          $ticket = DB::table('ndticket')
            ->select('id', 'purchaseDatetime', 'ticketNo', 'invoiceNo')
            ->where('referenceID', '=', $ticketReferenceID)
            ->where('deletes', '=', '0')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();


          $data['pageTitle'] = $paymentHistory->renewalStatus === 'PICKUPTOSTORE' ? 'Your order for store pickup is confirmed. Thank you for scheduling' : ($paymentHistory->renewalStatus === 'DELIVERYTOCUSTOMER' ? 'Your order for delivered is confirmed. Thank you for scheduling' : ($paymentHistory->renewalStatus === 'RENEWAL' ? 'Thank You for Your Renewal Purchase!' : ($paymentHistory->renewalStatus === 'RECHARGE' ? 'Thank You for Your Top Up!' : 'Thank You for Your Purchase!')));



          if ($ticket->count() > 0) {

            $data['ticketURL'] = ($request->header('Origin') . '/') . 'ticket-view/' . $transaction_id;
            $data['invoiceURL'] = ($request->header('Origin') . '/') . 'invoice/' . $transaction_id;

            $data['type'] = 'ND';
            $data['ticketNo'] = $ticket[0]->ticketNo;
            $data['invoiceNo'] = $paymentHistory->invoice_no;
            $data['renewalStatus'] = $paymentHistory->renewalStatus;

            $data['planSuggestions'] = null;


            if ($paymentHistory->renewalStatus === 'NEW') {


              $planRequest = new Request([
                "referenceID" => $ticketReferenceID
              ]);



              $billing = new billingController();

              $planRes =  $billing->getSuggestion($planRequest);
              $planData = json_decode($planRes->getContent(), true);

              if ($planData['status'] === 'success') {
                $data['planSuggestions']['noOfDraws'] = 0;
                $data['planSuggestions']['prizeInGram']  = 0;

                if ($checkout_response['productID'] === 1 || $checkout_response['productID'] === 2) {
                  if ($planData['data']['monthlyPlan'] != null) {
                    $data['planSuggestions']['referenceID'] = $ticketReferenceID;
                    $data['planSuggestions']['purchaseStatus'] = 'RENEWAL';
                    $data['planSuggestions']['productID'] = $planData['data']['monthlyPlan']['planDetails']['productID'];
                    $data['planSuggestions']['endDate'] = $planData['data']['monthlyPlan']['planList'][0]['endDate'];
                    $data['planSuggestions']['raffleCount'] = $planData['data']['monthlyPlan']['planList'][0]['raffle'];
                    $data['planSuggestions']['planAmt'] = $planData['data']['monthlyPlan']['planList'][0]['amount'];
                  }
                } else  if ($checkout_response['productID'] === 3) {
                  if ($planData['data']['annuallyPlan'] != null) {
                    $data['planSuggestions']['referenceID'] = $ticketReferenceID;
                    $data['planSuggestions']['purchaseStatus'] = 'RENEWAL';
                    $data['planSuggestions']['productID'] = $planData['data']['annuallyPlan']['planDetails']['productID'];

                    $data['planSuggestions']['endDate'] = $planData['data']['annuallyPlan']['planDetails']['endDate'];
                    $data['planSuggestions']['raffleCount'] = $planData['data']['annuallyPlan']['planDetails']['balanceRaffles'];
                    $data['planSuggestions']['planAmt'] = $planData['data']['annuallyPlan']['planDetails']['finalTotal'];
                  }
                }







                if ($data['planSuggestions'] != null) {
                  $renewalRes = new Request([
                    "cartDetails" => [
                      "productID" => $data['planSuggestions']['productID'],
                      "planAmount" => $data['planSuggestions']['planAmt']
                    ],
                    "referenceID" =>  $data['planSuggestions']['referenceID'],
                    "purchaseStatus" => "RENEWAL",
                    "shipping" => "pickUpToStore",
                    "pageTitle" => "productDetails"
                  ]);

                  $renewalRequest = $billing->addToCardProduct($renewalRes);
                  $renewalCartData = json_decode($renewalRequest->getContent(), true);

                  if ($renewalCartData['status'] === 'success') {
                    // dd(($renewalCartData['data']['cartData']['drawCount']['thrillCount'] ?? 0));
                    $data['planSuggestions']['noOfDraws'] = ($renewalCartData['data']['cartData']['drawCount']['thrillCount'] ?? 0) + ($renewalCartData['data']['cartData']['drawCount']['boosterCount'] ?? 0) + ($renewalCartData['data']['cartData']['drawCount']['bumberCount'] ?? 0);

                    $data['planSuggestions']['prizeInGram'] = ($renewalCartData['data']['cartData']['prizeInGram']['thrillPrize'] ?? 0) + ($renewalCartData['data']['cartData']['prizeInGram']['boosterPrize'] ?? 0) + ($renewalCartData['data']['cartData']['prizeInGram']['bumperPrize'] ?? 0);
                  }

                  // dd($renewalCartData);
                }
              }


              // dd($planData);
            }
          }

          if ($paymentHistory->renewalStatus === 'RECHARGE' && $paymentHistory->category === 'WalletDeposit') {
            $data['renewalStatus'] = $paymentHistory->renewalStatus;
            $checkWalletHis = DB::table('wallet_history')->where(
              [
                ['deletes', '=', '0'],
                ['status', '=', '0'],
                ['userid', '=', $user_id],
                ['reference_id', '=', $paymentHistory->id],
                ['reference_table', '=', 'payment_history']
              ]
            )->limit(1)->get();

            if ($checkWalletHis->count() > 0) {

              $data['purchaseDetails'] = $checkWalletHis[0];

              // $response = ['status' => 'failed', 'message' => 'The transaction found!', 'error' => 'The transaction found!'];
              // goto returnFVI;
            }
          }
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid transaction ID. Please use a valid transaction ID.', 'error' => 'Invalid transaction ID. Please use a valid transaction ID.'];
        goto returnFVI;
      }

      // $response = ['status' => 'success', 'message' => 'The Thanks screen Details Collected Successfully', 'data' => $data];

      $response = ['status' => 'success', 'message' => 'The details for the Thank You screen have been collected successfully', 'data' => $data];

      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }





  // Get User Product Cart Details
  public function getPurchaseSEO(Request $request)
  {

    try {
      // ini_set('precision', 2);


      $response = [];
      $input = $request->all();


      $request->transaction_id = Controller::BlockSQLInjection($request->transaction_id);
      if ($request->transaction_id == '' || $request->transaction_id == null || $request->transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid transaction id!', 'error' => 'Please use a valid transaction id!'];
        goto returnFVI;
      }

      $transaction_id = $request->transaction_id;

      $data = [];
      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }




      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction id Required!', 'error' => 'Kindly check the Order Reference!'];
        goto returnFVI;
      }

      $payment_history = DB::table('payment_history')
        ->where('transaction_id', '=', $transaction_id)
        ->whereIn('pay_re_status', ['CAPTURED', 'Success', 'Shipped'])
        ->where('status', '=', '1')
        ->where('category', '=', 'PRODUCT')
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();


      // $payment_history = select_query($con, "payment_history", "", "`transaction_id` = '$transIDs' AND `pay_re_status` IN ( 'CAPTURED' , 'Success' ,'Shipped') AND `status` = '1' AND `category` = 'PRODUCT' ORDER BY `id` DESC LIMIT 1", "", "");

      if ($payment_history->count() > 0) {



        $checkout_response = json_decode($payment_history[0]->checkout_response, true);

        $oticket = DB::table('ticket')
          ->where('transaction_id', '=', $transaction_id)
          ->where('deletes', '=', '0')
          ->orderBy('id', 'desc')
          ->limit(1)
          ->get();


        // $oticket = select_query($con, "ticket", "", "`transaction_id` = '$transIDs' and `deletes`='0' order by `id` DESC LIMIT 1", "", "");

        if ($oticket->count() > 0) {

          $ticketID = $oticket[0]->id;
          $net_total = $oticket[0]->net_total;



          // $onepercen = ($net_total / 105);
          // $total_amount = number_format(($onepercen * 100), 2);
          // $tax_value1 = number_format(($net_total - $total_amount), 2);

          $onepercen = intval($net_total) / 105;
          $total_amount = floatval($onepercen) * 100;

          $tax_value = intval($net_total) - floatval($total_amount);




          // dd($taxValueFloat);

          $data['taxValue'] =  number_format($tax_value, 2, ".", "");
          $data['finalTotal'] = $net_total;

          // $user_idT = $oticket[0]->id; $oticket['result'][0]['user_id'];
          $data['seoURL'] = 'https://trk.convserv.com/tracko/v1/cont/cont.js?of=227&ac=5&af=16&cs=0&cp1=' . (isset($checkout_response['item1']) ? intval($checkout_response['item1']) : 0) . '&cp2=' . (isset($checkout_response['item2']) ? intval($checkout_response['item2']) : 0) . '&cp3=' . (isset($checkout_response['item3']) ? intval($checkout_response['item3']) : 0) . '&cp4=' . (isset($checkout_response['item4']) ? intval($checkout_response['item4']) : 0) . '&cp5=' . number_format(intval($net_total), 2) . '&cp6=' . $user_id . '&cp7=' . $transaction_id . '&ts={timestamp}';

          // $ticket_LInes = mysqli_query($con, "SELECT * FROM
          // (SELECT ticket_id, product_id, GROUP_CONCAT(my3number) AS 'my3number', type, deletes , count(id) as 'total' FROM `ticket_lines` WHERE `ticket_id` = $ticketID AND `type` = 'OT' AND  `deletes` = '0' GROUP BY product_id) as t
          // INNER JOIN product AS p ON p.id = t.product_id;");


          $ticketLines = DB::select("SELECT * FROM
          (SELECT ticket_id, product_id, GROUP_CONCAT(my3number) AS 'my3number', type, deletes , count(id) as 'total' FROM `ticket_lines` WHERE `ticket_id` = $ticketID AND `type` = 'OT' AND  `deletes` = '0' GROUP BY product_id) as t
          INNER JOIN product AS p ON p.id = t.product_id;");

          // dd(ini_get('precision'));

          $i = 0;
          $items = [];
          foreach ($ticketLines as $key => $row) {
            $items[] = [
              'item_id' => $row->product_id,
              'item_name' => $row->name,
              'affiliation' => "National Draw Web",
              'coupon' => "",
              'discount' => 0,
              'index' => $i++,
              'item_brand' => "National Draw",
              'item_category' => "Bottled Water",
              'item_category2' => "",
              'item_category3' => "",
              'item_category4' => "",
              'item_category5' => "",
              'item_list_id' => "related_products",
              'item_list_name' => "Category AED " . intval($row->rate),
              'item_variant' => "",
              'location_id' => $request->ip(),
              'price' => intval($row->total) * intval($row->rate),
              'quantity' => intval($row->total),
            ];
          }
        }
      }

      $data['items'] = $items;

      // Log
      $log = Controller::error_log_new($request->ip(), 'SEO_Thanks_ED', $user_id, '', '', 'Thanks page SEO Tag URL has been developed',  json_encode(['response' =>  $data]), __DIR__, basename(__FILE__), __LINE__);


      // error_log_new($con, getUserIP(), 'SEO_Thanks_ED', $user_idT, '', '', 'Thanks page SEO Tag URL has been developed', json_encode(['SEOURL' => $seoURL]), __DIR__, basename(__FILE__), __LINE__, $dubaidate_time);

      $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  public function getFindKisok(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();

      $arr[] = ['status', '=', '0'];




      if (isset($request->kiosk_id) && $request->kiosk_id != null && $request->kiosk_id != 'null' && $request->kiosk_id != '' && $request->kiosk_id > 0) {


        $request->kiosk_id = Controller::BlockSQLInjection($request->kiosk_id);
        if ($request->kiosk_id == '' || $request->kiosk_id == null || $request->kiosk_id == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid kiosk id!', 'error' => 'Please use a valid kiosk id!'];
          goto returnFVI;
        }

        $arr[] = ['id', '=', $request->kiosk_id];
      }

      // dd($arr);
      $markeMap = DB::table('kiosk_locations')
        ->whereIn('kiosk_id', function ($query) {
          $query->select('kiosk_id')
            ->from('kiosk_machines')
            ->where('is_active', '0')
            ->where('deletes', '0');
        })
        ->where($arr)
        ->get();

      $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => ['Kiosks' => $markeMap]];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function storeUserJourney(Request $request)
  {
    try {

      $response = [];
      $input = $request->all();

      $data = [];



      $request->subid1 = Controller::BlockSQLInjection($request->subid1);
      if ($request->subid1 == '' || $request->subid1 == null || $request->subid1 == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid subid1!', 'error' => 'Please use a valid subid1!'];
        goto returnFVI;
      }


      $request->url = Controller::BlockSQLInjection($request->url);
      if ($request->url == '' || $request->url == null || $request->url == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid url!', 'error' => 'Please use a valid url!'];
        goto returnFVI;
      }

      $request->subid2 = Controller::BlockSQLInjection($request->subid2);
      if ($request->subid2 == '' || $request->subid2 == null || $request->subid2 == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid subid2!', 'error' => 'Please use a valid subid2!'];
        goto returnFVI;
      }

      $request->utm_campaign = Controller::BlockSQLInjection($request->utm_campaign);
      if ($request->utm_campaign == '' || $request->utm_campaign == null || $request->utm_campaign == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid utm_campaign!', 'error' => 'Please use a valid utm_campaign!'];
        goto returnFVI;
      }

      $request->utm_source = Controller::BlockSQLInjection($request->utm_source);
      if ($request->utm_source == '' || $request->utm_source == null || $request->utm_source == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid utm_source!', 'error' => 'Please use a valid utm_source!'];
        goto returnFVI;
      }

      $request->user_id = Controller::BlockSQLInjection($request->user_id);
      if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid user_id!', 'error' => 'Please use a valid user_id!'];
        goto returnFVI;
      }

      $request->utm_medium = Controller::BlockSQLInjection($request->utm_medium);
      if ($request->utm_medium == '' || $request->utm_medium == null || $request->utm_medium == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid utm_medium!', 'error' => 'Please use a valid utm_medium!'];
        goto returnFVI;
      }

      $utm_source = (isset($request->utm_source) && $request->utm_source != '' && $request->utm_source != null) ? $request->utm_source : "";
      $utm_medium = (isset($request->utm_medium) && $request->utm_medium != '' && $request->utm_medium != null) ? $request->utm_medium : "";
      $utm_campaign  = (isset($request->utm_campaign) && $request->utm_campaign != '' && $request->utm_campaign != null) ? $request->utm_campaign : "";
      $user_id = $request->user_id != '' ? $request->user_id : '0';

      // dd($request->subid2);


      $subid2 = (isset($request->subid2) && $request->subid2 != '' && $request->subid2 != null) ? $request->subid2 : "";

      $ip = $request->ip();

      $fd = date('Y-m-d H:i:s', strtotime('-1 day', strtotime(now())));
      $td = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime(now())));



      $check_track = DB::select("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' ORDER BY `id` DESC LIMIT 1;");

      if (count($check_track) > 0) {

        $withdraw_arr = [
          "ip" => $ip,
          "subid1" => $request->subid1,
          "url" =>  $request->url,
          "before_dm_id" => $check_track[0]->id,
          "subid2" => $subid2,
          "utm_campaign" => $utm_campaign,
          "utm_source" =>  $utm_source,
          "uniqueid" => '',
          "user_id" => $request->user_id,
          "status" => '0',
          "deletes" => '0',
          "createdon" => now()
        ];




        $check_status = DB::select("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' AND `subid1` LIKE '$request->subid1' ORDER BY `id` DESC LIMIT 1;");


        // dd("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' AND `subid1` LIKE '$request->subid1' ORDER BY `id` DESC LIMIT 1;");
        if (count($check_status) < 1) {
          $with_draw_ins = DB::table('digital_market')->insertGetId($withdraw_arr);
          $data['track_id'] = $with_draw_ins;

          $response = ['status' => 'success', 'message' => 'Process Done', 'data' => $data];
          goto returnFVI;
        } else {
          $response = ['status' => 'failed', 'message' => 'Process Done', 'error' => 'No Data Found'];
          goto returnFVI;
        }
      } else {

        $withdraw_arr = [
          "ip" => $ip,
          "subid1" => $request->subid1,
          "url" =>  $request->url,
          "before_dm_id" => '0',
          "subid2" => $subid2,
          "utm_campaign" => $utm_campaign,
          "utm_source" =>  $utm_source,
          "uniqueid" => '',
          "user_id" => $request->user_id,
          "status" => '0',
          "deletes" => '0',
          "createdon" => now()
        ];

        $with_draw_ins =  DB::table('digital_market')->insertGetId($withdraw_arr);

        $data['track_id'] = $with_draw_ins;

        $response = ['status' => 'success', 'message' => 'Process Done', 'data' => $data];
        goto returnFVI;
      }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  public function getMobileOTP(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;
      $dialCode = $request->dialCode;
      $mobile = $request->mobile;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($dialCode == '' || $dialCode == null || $dialCode == 'null') {
        $response = ['status' => 'failed', 'message' => 'Country Code Missing!', 'error' => 'Country Code Missing!'];
        goto returnFVI;
      }

      if ($mobile == '' || $mobile == null || $mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Mobile No Missing!', 'error' => "Kindly Enter the Mobile No!"];
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



      $otp = Controller::generateOTP(6);

      if ($otp == '') {
        $response = ['status' => 'failed', 'message' => "Couldn`t not generate OTP!", 'error' => "Couldn`t not generate OTP!"];
        goto returnFVI;
      }

      $user_register = DB::table('user_register')
        ->where('mobile', $mobile)
        ->where('deletes', 0)
        ->orderByDesc('id')
        ->limit(1)
        ->get();

      if ($user_register->count() > 0) {
        $response = ['status' => 'failed', 'message' => "This mobile number already exists", 'error' => "This mobile number already exists"];
        goto returnFVI;
      }

      $name = ucwords(strtolower(auth()->user()->name));

      // session(['newMobile' => $mobile]);
      // session(['dialCode' => $dialCode]);
      $encrypted = encrypt([
        'newMobile' => $mobile,
        'dialCode' => $dialCode
      ]);
      // if (session('newMobile') == '' || session('newMobile') == null || session('newMobile') == 'null' || session('dialCode') == '' || session('dialCode') == null || session('dialCode') == 'null') {
      //   $response = ['status' => 'failed', 'message' => "Couldn`t not generate OTP!", 'error' => "Couldn`t not generate OTP!"];
      //   goto returnFVI;
      // }

      if ($encrypted == '' && $encrypted == null && $encrypted == 'null') {
        $response = ['status' => 'failed', 'message' => "The Data Encryption Failed!", 'error' => "The Data Encryption Failed!"];
        goto returnFVI;
      }
      $data["enc"] =  $encrypted;
      $data["dialCode"] = $dialCode;
      $data["mobile"] = $mobile;
      if (substr($mobile, 0, 3) == "971") {

        $updateQuery =  DB::table('user_register')
          ->where('id', $user_id)
          ->where('deletes', '0')
          ->where('status', '0')
          ->update(['otp' => $otp]);
        // session()->get('newMobile'); 
        // dd($updateQuery);
        // $updateQuery = mysqli_query($con, "UPDATE `user_register` SET `otp` = '$otp' WHERE `user_register`.`id` = $user_id;");



        if ($updateQuery) {



          $messages = "Hello " . $name . ", " . $otp . " is the One Time Password (OTP) to Mobile Number update for the National Draw Account.";


          $sentsms = Controller::sendsms($mobile, $messages, '');
          // $sendSms = sendsms($con, $mobile, $messages, "");


          if ($sentsms) {


            $response = ['status' => 'success', 'message' => "OTP Send Successfully", 'data' => $data];
            goto returnFVI;
          } else {
            $response = ['status' => 'failed', 'message' => "Could not send sms", 'error' => "Could not send sms"];
            goto returnFVI;
          }
        } else {

          $response = ['status' => 'failed', 'message' => "Could not send sms", 'error' => "Could not send sms"];
          goto returnFVI;
        }
      } else {

        $response = ['status' => 'success', 'message' => "OTP Send Successfully", 'data' => $data];
        goto returnFVI;
      }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function mobileUpdate(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;
      $enc = decrypt($request->enc);
      $otp = $request->otp;
      $allowFB = $request->allowFB;

      $dialCode = $enc['dialCode'];
      $mobile =  $enc['newMobile'];

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($enc == '' || $enc == null || $enc == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Encrypted string Missing!', 'error' => 'The Encrypted string Missing!'];
        goto returnFVI;
      }

      if ($dialCode == '' || $dialCode == null || $dialCode == 'null') {
        $response = ['status' => 'failed', 'message' => 'Country Code Missing!', 'error' => 'Country Code Missing!'];
        goto returnFVI;
      }

      if ($mobile == '' || $mobile == null || $mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Mobile No Missing!', 'error' => "Kindly Enter the Mobile No!"];
        goto returnFVI;
      }

      if ($otp == '' || $otp == null || $otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Couldn`t not generate OTP!', 'error' => 'Couldn`t not generate OTP!'];
        goto returnFVI;
      }




      $request->otp = Controller::BlockSQLInjection($request->otp);
      if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid otp!', 'error' => 'Please use a valid otp!'];
        goto returnFVI;
      }




      $eotp = auth()->user()->otp;

      if (($otp == $eotp && $dialCode == 971 && $allowFB == '') || ($dialCode != 971 && $allowFB != '' && $allowFB == true)) {

        $updateQuery =   DB::table('user_register')
          ->where('id', $user_id)
          ->update([
            'dialCode' => $dialCode,
            'mobile' => $mobile,
            'mobile_verify' => 'YES',
            'updated_at' => now(),
            'otp' => ""
          ]);



        if ($updateQuery) {


          $response = ['status' => 'success', 'message' => "Mobile Number Updated Successfully", 'data' => "Mobile Number Updated Successfully"];
          goto returnFVI;
        } else {

          $response = ['status' => 'failed', 'message' => "Update process Failed!", 'error' => "Update process Failed!"];
          goto returnFVI;
        }
      } else {

        $response = ['status' => 'failed', 'message' => "OTP Does not Match. Kindly Check your SMS", 'error' => "OTP Does not Match. Kindly Check your SMS"];
        goto returnFVI;
      }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




  public function getPastDrawVideos(Request $request)
  {
    try {
      $response = [];
      $input = $request->all();
      $data = [];

      $product_list = DB::table('draw')
        ->select('id', 'dailyThrillName', 'dailyDrawNo', 'resultDate')
        ->where('dailyThirllStatus', 'Completed')
        ->where('deletes', '0')
        ->orderBy('id', 'DESC')
        ->get();

      if ($product_list->count() > 0) {
        foreach ($product_list as $key => $value) {

          $video_url = DB::table('past_video_url')
            ->select('youtube_url')
            ->where('draw_id', $value->id)
            ->where('deletes', '0')
            ->orderBy('youtube_url')
            ->first();

          if (empty($video_url)) {
            // Handle the case where no video URL is found
            continue; // Skip to the next iteration
          }

          // Process the draw details
          $drawNameDetail = explode(" ", $value->dailyDrawNo);
          $drawName = "National Draw - ";
          if (isset($drawNameDetail[0])) {
            $drawName .= $drawNameDetail[0] . " ";
          }
          if (isset($drawNameDetail[2])) {
            $drawName .= "(" . date("l", strtotime($value->resultDate)) . ") Draw No " . ltrim($drawNameDetail[2], "#");
          }
          $videoDate = date("dS", strtotime($value->resultDate)) . '-' . strtoupper(date("M", strtotime($value->resultDate))) . '-' . date("Y", strtotime($value->resultDate));

          $image_url = "https://i.ytimg.com/vi/" . trim(str_replace("https://www.youtube.com/embed/", "", $video_url->youtube_url)) . "/hqdefault.jpg";

          $data[] = [
            'dailyDrawNo' => $value->id,
            'dailyThrillName' => $drawName,
            'imageUrl' => $image_url,
            'videoUrl' =>  $video_url->youtube_url
          ];
        }
      }

      $response = ['status' => 'success', 'message' => "The past draw show details collected successfully", 'data' => $data];
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }







  public function dynamicLink(Request $request)
  {

    try {
      $data = [];
      $response = [];
      $input = $request->all();

      // $request->user_id = Controller::BlockSQLInjection($request->user_id);
      // if ($request->user_id == '' || $request->user_id == null || $request->user_id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid user id!', 'error' => 'Please use a valid user id!'];
      //   goto returnFVI;
      // }

      // $request->type = Controller::BlockSQLInjection($request->type);
      // if ($request->type == '' || $request->type == null || $request->type == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
      //   goto returnFVI;
      // }

      // $request->payment_id = Controller::BlockSQLInjection($request->payment_id);
      // if ($request->payment_id == '' || $request->payment_id == null || $request->payment_id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Please use a valid payment id!', 'error' => 'Please use a valid payment id!'];
      //   goto returnFVI;
      // }

      $user_id = Controller::BlockSQLInjection($request->user_id);
      $type = Controller::BlockSQLInjection($request->type);
      $payment_id = Controller::BlockSQLInjection($request->payment_id);




      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }

      if ($type == '' || $type == null || $type == 'null') {
        $response = ['status' => 'failed', 'message' => 'The type missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }

      if ($payment_id == '' || $payment_id == null || $payment_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The payment id missing!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }

      $user = user_register::where(['id' => $user_id, 'roll_id' => '0', 'status' => '0', 'deletes' => '0'])->first();

      if ($user) {

        $data['token'] = $user->createToken('NDpaybylink', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;
      } else {
        $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }

      if ($data['token'] == '' || $data['token'] == null || $data['token'] == 'null') {
        $response = ['status' => 'failed', 'message' => 'User Not Found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }

      $valid_linkcheck = DB::table('payby_link')
        ->select('*')
        ->where('id', $payment_id)
        ->where('type', $type)
        ->where('user_id', $user_id)
        ->where('deletes', '0')
        ->orderByDesc('id')
        ->limit(1)
        ->get();

      // dd($valid_linkcheck);

      if ($valid_linkcheck->count() > 0) {



        $expired_time =  strtotime($valid_linkcheck[0]->expired);
        $current_time = strtotime(now());
        $status = $valid_linkcheck[0]->status;
        if ($expired_time >= $current_time) {
          if ($status != 'Paid') {
            $data['user_id'] = $valid_linkcheck[0]->user_id;


            $payment_idlink  = $valid_linkcheck[0]->payment_id;


            $payment_idcheck = DB::table('payment_history')
              ->select('*')
              ->where('id', $payment_idlink)
              ->orderByDesc('id')
              ->limit(1)
              ->get();

            if ($payment_idcheck->count() > 0) {

              $cardata = (array)json_decode($payment_idcheck[0]->checkout_response);

              $data['payment_id'] = $payment_id;
              $data['cartdata'] = $cardata;

              $updatePayLink = DB::table('payby_link')
                ->where('id', $payment_id)
                ->where(function ($query) {
                  $query->where('status', '!=', 'Paid')
                    ->orWhereNull('status');
                })
                ->where('type', $type)
                ->where('user_id', $user_id)
                ->where('deletes', '0')
                ->update(['status' => 'clicked']);

              // dd($updatePayLink);

              $data['redirectURL'] = ($request->header('Origin') . '/') . 'billing/';
              $response = ['status' => 'success', 'message' => 'Details collected successfully', 'data' => $data];
              goto returnFVI;
            } else {
              $response = ['status' => 'failed', 'message' => 'Track not found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'Track not found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Track not found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Track not found!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
        goto returnFVI;
      }






      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }







  public function doS3upload(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;

      $data = [];
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Token Missing Error'];
        goto returnFVI;
      }


      // $user = DB::table('user_register')
      //   ->where('id', $user_id)
      //   ->where('deletes', '0')
      //   ->where('status', '0')
      //   ->orderBy('id', 'desc')
      //   ->first();

      // if ($user->id == '' || $user->id == null || $user->id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
      //   goto returnFVI;
      // }

      $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
      $maxFileSize = 5120; // Maximum file size in kilobytes (5 MB)


      if ($request->hasFile('image')) {
        $file = $request->file('image');
        $name = $file->getClientOriginalName();

        if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {
          // File is valid, an image, within allowed MIME types, and within size limits



          // dd(phpinfo());

          // Your code to process the uploaded image goes here
          $filePath = 'nationaldraw/' . $user_id . '/' . md5($name . uniqid() . $user_id  . time()) . '.' . $file->getClientOriginalExtension();

          $store = Storage::disk('spaces')->put(
            '/' . $filePath,
            file_get_contents($request->file('image')->getRealPath()),
            'public'
          );
          // dd($store);

          if ($store) {
            // $store = Storage::disk('spaces')->put('/nationaldraw/uploads/test' . $name, file_get_contents($request->file('file')->getRealPath()), 'public');
            // $url = str_replace(env('DO_FULL_URL'), env('DO_REDIRECT_URL'), Storage::disk('spaces')->url($filePath));
            $url = $filePath;
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
          $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
        goto returnFVI;
      }


      // dd($data);
      // Get User ID
      // $user_id = auth()->user()->id;
      // if ($user_id == '' || $user_id == null || $user_id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
      //   goto returnFVI;
      // }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }








  public function BluckdoS3upload(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();
      $user_id = $request->id;

      $data = [];
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Token Missing Error'];
        goto returnFVI;
      }


      // $user = DB::table('user_register')
      //   ->where('id', $user_id)
      //   ->where('deletes', '0')
      //   ->where('status', '0')
      //   ->orderBy('id', 'desc')
      //   ->first();

      // if ($user->id == '' || $user->id == null || $user->id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => ['redirectURL' => ($request->header('Origin') . '/') . 'expired/']];
      //   goto returnFVI;
      // }

      $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp', 'pdf'];
      $maxFileSize = 15360; // Maximum file size in kilobytes (15 MB)


      if ($request->hasFile('image')) {
        $file = $request->file('image');
        $name = $file->getClientOriginalName();

        if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {
          // File is valid, an image, within allowed MIME types, and within size limits

          $fileName = (isset($request->fileName) && $request->fileName != '' && $request->fileName != null && $request->fileName != 'null') ? $request->fileName : md5($name . uniqid() . $user_id  . time());

          // Your code to process the uploaded image goes here
          $filePath = 'nationaldraw/' . $user_id . '/' .  $fileName . '.' . $file->getClientOriginalExtension();

          $store = Storage::disk('spaces')->put(
            '/' . $filePath,
            file_get_contents($request->file('image')->getRealPath()),
            'public'
          );
          // dd($store);

          if ($store) {
            // $store = Storage::disk('spaces')->put('/nationaldraw/uploads/test' . $name, file_get_contents($request->file('file')->getRealPath()), 'public');
            // $url = str_replace(env('DO_FULL_URL'), env('DO_REDIRECT_URL'), Storage::disk('spaces')->url($filePath));
            $url = $filePath;
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
          $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
        goto returnFVI;
      }


      // dd($data);
      // Get User ID
      // $user_id = auth()->user()->id;
      // if ($user_id == '' || $user_id == null || $user_id == 'null') {
      //   $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
      //   goto returnFVI;
      // }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }












  public function getWithDrawDetails(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;

      $data = [];


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

      $data['users'] = auth()->user()->toArray();

      // $data['FRONT_img'] = $FRONT_img;

      // $data['BACK_img'] = $BACK_img;
      // dd($FRONT_img->img_url);
      // $data['FRONT_img']->img_url =  (strpos($FRONT_img->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $FRONT_img->img_url) : $FRONT_img->img_url;

      // $data['BACK_img']->img_url = (strpos($BACK_img->img_url, "nationaldrawuae") === 0) ? (env('DO_REDIRECT_URL') . $BACK_img->img_url) : $BACK_img->img_url;





      $data['country'] = $country;

      $data['lastReqData'] =  $req_check;

      $response = ['status' => 'success', 'message' => "With draw page details collected", 'data' =>  $data];
      goto returnFVI;



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




  public function deleteImage(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;


      $request->image_id = Controller::BlockSQLInjection($request->image_id);
      if ($request->image_id == '' || $request->image_id == null || $request->image_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid image id!', 'error' => 'Please use a valid image id!'];
        goto returnFVI;
      }


      $image_id = $request->image_id;
      $data = [];

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($image_id == '' || $image_id == null || $image_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Image Required!', 'error' => 'Kindly check the image ID!'];
        goto returnFVI;
      }

      $arr = ["deletes" => '1'];

      $update = DB::table('user_images')
        ->where('id', $image_id)
        ->where('user_id', $user_id)
        ->where('deletes', '0')
        ->update($arr);

      if ($update) {
        $response = ['status' => 'success', 'message' => "Image deleted Successfully", 'data' =>  "Successfully Deleted"];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'Delete Process Failed!', 'error' => 'Delete Process Failed!'];
        goto returnFVI;
      }


      // $arr = array("deletes" => '1');

      // $update = update($con, "user_images", "`id` = '$id' and `deletes`='0'", $arr, "", "", "", "");

      // $errors = $update['errors'];

      // if ($errors != "") {

      //   $result["type"] = "0";

      //   $result["result"] = $errors;
      // } else {

      //   $result["type"] = "1";
      // }






      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }





















  public function withdrawRequest(Request $request)
  {

    try {

      $response = [];
      $input = $request->all();
      $user_id = auth()->user()->id;

      $data = [];

      $request->withdrawAmt = Controller::BlockSQLInjection($request->withdrawAmt);
      if ($request->withdrawAmt == '' || $request->withdrawAmt == null || $request->withdrawAmt == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid withdraw amount!', 'error' => 'Please use a valid withdraw amount!'];
        goto returnFVI;
      }

      $amount =  $request->withdrawAmt;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      if (intval($amount) >= 300) {

        RE:
        $request_id = 'WR' . Str::random(15) . now()->format('His');
        $req_check = DB::table('withdraw_request')->where('request_id', $request_id)->count();
        if ($req_check > 0) {
          goto RE;
        }

        $my_t_earn = auth()->user()->t_earning;

        if (floatval($my_t_earn) >= floatval($amount)) {


          $fromclose = intval($my_t_earn) - intval($amount);
          $dob = date('Y-m-d', strtotime($request->dob));

          $request->nationality = Controller::BlockSQLInjection($request->nationality);
          if ($request->nationality == '' || $request->nationality == null || $request->nationality == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid nationality!', 'error' => 'Please use a valid nationality!'];
            goto returnFVI;
          }


          $request->livein = Controller::BlockSQLInjection($request->livein);
          if ($request->livein == '' || $request->livein == null || $request->livein == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid livein!', 'error' => 'Please use a valid live in!'];
            goto returnFVI;
          }



          $nationlaity = DB::table('countries')->where('flag', 1)
            ->where('id', $request->nationality)
            ->where('name', '!=', '')
            ->orderBy('id', 'desc')
            ->value('name');

          $livin = DB::table('countries')->where('flag', 1)
            ->where('id', $request->livein)
            ->where('name', '!=', '')
            ->orderBy('id', 'desc')
            ->value('name');

          $with_draw_ins = '';
          $img_arr = explode(',', $request->totalimg);
          if (isset($request->totalimg) && $request->totalimg !== null && count($img_arr) > 0) {
            foreach ($img_arr as $value) {

              $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
              $maxFileSize = 5120; // Maximum file size in kilobytes (5 MB)

              if ($request->hasFile($value)) {
                $file = $request->file($value);
                $name = $file->getClientOriginalName();

                if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {
                  // File is valid, an image, within allowed MIME types, and within size limits



                  // dd(phpinfo());

                  // Your code to process the uploaded image goes here
                  $filePath = 'nationaldraw/' . $user_id . '/' . md5($user_id . $name . time()) . '.' . $file->getClientOriginalExtension();

                  $store = Storage::disk('spaces')->put(
                    '/' . $filePath,
                    file_get_contents($request->file($value)->getRealPath()),
                    'public'
                  );
                  // dd($store);

                  if ($store) {
                    // $store = Storage::disk('spaces')->put('/nationaldraw/uploads/test' . $name, file_get_contents($request->file('file')->getRealPath()), 'public');
                    // $url = Storage::disk('spaces')->url($filePath);
                    // $url = str_replace(env('DO_FULL_URL'), env('DO_REDIRECT_URL'), Storage::disk('spaces')->url($filePath));
                    $url = $filePath;
                    if ($url != '') {
                      $data = [
                        'digitalURL' => $url
                      ];

                      $nty = '';
                      if ('wemimage1' == $value || 'wemimage3' == $value) {
                        $nty = 'FRONT';
                      } else {
                        $nty = 'BACK';
                      }

                      $withdrawData = [
                        "img_url" => $url,
                        "type" => $nty,
                        "user_id" => $user_id,
                        "status" => '0',
                        "deletes" => '0',
                        "createdon" => now()
                      ];

                      $with_draw_ins =   DB::table('user_images')->insert($withdrawData);


                      // $withdraw_arr = array("img_url" => $fileNEW, "type" => $nty, "user_id" => $user_id, "status" => '0', "deletes" => '0', "createdon" => $dubaidate_time);

                      // $with_draw_ins = insert($con, "user_images", "", $withdraw_arr, "", "", "");

                      // $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
                      // goto returnFVI;
                    } else {
                      $response = ['status' => 'failed', 'message' => "URL generation failed!", 'error' => "URL generation failed!"];
                      goto returnFVI;
                    }
                  } else {
                    $response = ['status' => 'failed', 'message' => "The File Upload Failed!", 'error' => "The File Upload Failed!"];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => "format not supported!", 'error' => "format not supported!"];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
                goto returnFVI;
              }
            }
          }
          // dd($img_arr);

          // File Upload 
          if ((count($img_arr) > 0 && $with_draw_ins != '' && $request->totalimg != '') || ($request->totalimg == '')) {

            $request->type = Controller::BlockSQLInjection($request->type);
            if ($request->type == '' || $request->type == null || $request->type == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
              goto returnFVI;
            }

            $toEmail = env('withDrawToEmail');
            $ccAddress = array_map('trim', explode(',', env('withDrawCC')));
            $toSubject = 'Withdraw Request Notification';

            $htmlTemplate = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
               <head>
                  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Withdraw Request Mail Template</title>
                
                  <style type="text/css">
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
                        <tr>
                           <td class="column-one" style="background: #29377d; height:22px;">
                           </td>
                        </tr>
                        <tr>
                           <td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:6px;">        
                           </td>
                        </tr>
                        <tr>
                           <td class="column-one" >
                              <table class="column">
                                 <tr>
                                    <td valign="top" style="padding: 16px 0 0px 0;">
                                       <center>
                                          <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/b3fb4b313447535147e83d04a9485710.png" style="border: 0px;" width="40%">
                                       </center>
                                    </td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <!-- LOGO  -->
                        <tr>
                           <td class="column-one" >
                              <table align="center" class="column">
                                 <tr>
                                    <td valign="top" >
                                       <div style="margin:0 auto;  max-width:500px; display:block;">
                                         
                                          <div style="">
                                             <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px; margin:10px 0px; text-align: center;font-weight: 500;">Hi Team,</h3>
                                             <br>
                                             <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/e7f67b501b1d1abe8d90c78de09e0d23.png" width="20%">
                                          </div>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <td style="color: #111111;padding: 15px 14px 10px;font-size: 24px;line-height: 3px;" align="center" valign="top" bgcolor="#ffffff">
                                       <h3 style="color: #29377d;font-size: 27px;margin: 0px;font-style: italic;font-family: Arial Narrow;">Withdraw Request</h3>
                                    </td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td class="column-one" >
                              <table align="center" class="column">
            
                                 <tr>
                                    <td valign="top" >
                                       <table style="margin: auto; border-collapse: collapse;border: 1px; width:95%; max-width:500px;" border="1" cellspacing="2" cellpadding="0">
                                          <tbody>
                                             <tr>
                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:28%" align="center" bgcolor="#d0dbe7"><strong>Name</strong></th>
                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:12%" align="center" bgcolor="#d0dbe7"><strong>Amount</strong></th>
                                                <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:18px; width:25%" align="center" bgcolor="#d0dbe7"><strong>Request id</strong></th>
                                             </tr>
                                             <tr>
                                                <td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' . auth()->user()->name  . ' ' . (auth()->user()->lname ?? '') . '</strong></td>
                                                <td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' . $amount . '</strong></td>                                               
                                                <td style=" padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size:19px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 800;">' . $request_id . '<br></strong></td>
                                             </tr>
                                          </tbody>
                                       </table>
                                       <br>
                                      
                                       <table style=" width: 100%;margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                                          <tbody>
                                             
                                             <tr>
                                                <td class="gmail-line" style="box-sizing: border-box; width: 8px;padding:15px 0 0 0;">
                                                   <img  style="width:500px !important;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/f9945404f6bf1a1eaefba91746051a35.png">
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                      
                                       <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
                                          For Clarification
                                          <br>
                                          Call 04 33 98880 Whatsapp +971 56 199 1271
                                          <br>
                                          or email support@nationaldrawuae.com
                                       </p>
                                    </td>
                                 </tr>
                              </table>
                           </td>
                        </tr>
                     </table>
                     <!-- End Main Class -->
                  </center>
                  <!-- End Wrapper -->
               </body>
            </html>';


            if ($request->type == 'BANK') {




              $request->accHolderName = Controller::BlockSQLInjection($request->accHolderName);
              if ($request->accHolderName == '' || $request->accHolderName == null || $request->accHolderName == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid account holder name!', 'error' => 'Please use a valid account holder name!'];
                goto returnFVI;
              }


              $request->accNumber = Controller::BlockSQLInjection($request->accNumber);
              if ($request->accNumber == '' || $request->accNumber == null || $request->accNumber == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid account number!', 'error' => 'Please use a valid account number!'];
                goto returnFVI;
              }


              $request->accType = Controller::BlockSQLInjection($request->accType);
              if ($request->accType == '' || $request->accType == null || $request->accType == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid account type!', 'error' => 'Please use a valid account type!'];
                goto returnFVI;
              }

              $request->bankName = Controller::BlockSQLInjection($request->bankName);
              if ($request->bankName == '' || $request->bankName == null || $request->bankName == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid bank name!', 'error' => 'Please use a valid bank name!'];
                goto returnFVI;
              }

              $request->branchName = Controller::BlockSQLInjection($request->branchName);
              if ($request->branchName == '' || $request->branchName == null || $request->branchName == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid branch name!', 'error' => 'Please use a valid branch name!'];
                goto returnFVI;
              }


              $request->branchCode = Controller::BlockSQLInjection($request->branchCode);
              if ($request->branchCode == '' || $request->branchCode == null || $request->branchCode == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid branch code!', 'error' => 'Please use a valid branch code!'];
                goto returnFVI;
              }


              $request->ibanNo = Controller::BlockSQLInjection($request->ibanNo);
              if ($request->ibanNo == '' || $request->ibanNo == null || $request->ibanNo == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid iban no!', 'error' => 'Please use a valid IBAN no!'];
                goto returnFVI;
              }


              $request->dob = Controller::BlockSQLInjection($request->dob);
              if ($request->dob == '' || $request->dob == null || $request->dob == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid date of birth!', 'error' => 'Please use a valid date of birth!'];
                goto returnFVI;
              }

              $request->currencyCode = Controller::BlockSQLInjection($request->currencyCode);
              if ($request->currencyCode == '' || $request->currencyCode == null || $request->currencyCode == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid currency code!', 'error' => 'Please use a valid currency code!'];
                goto returnFVI;
              }

              $request->passport = Controller::BlockSQLInjection($request->passport);
              if ($request->passport == '' || $request->passport == null || $request->passport == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid passport!', 'error' => 'Please use a valid passport!'];
                goto returnFVI;
              }

              $request->swiftCode = Controller::BlockSQLInjection($request->swiftCode);
              if ($request->swiftCode == '' || $request->swiftCode == null || $request->swiftCode == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid swift code!', 'error' => 'Please use a valid swift code!'];
                goto returnFVI;
              }

              if (
                isset($request->type) && $request->type !== null &&
                isset($request->withdrawAmt) && $request->withdrawAmt !== null &&
                isset($request->accHolderName) && $request->accHolderName !== null &&
                isset($request->accNumber) && $request->accNumber !== null &&
                isset($request->accType) && $request->accType !== null &&
                isset($request->bankName) && $request->bankName !== null &&
                isset($request->branchName) && $request->branchName !== null &&
                isset($request->branchCode) && $request->branchCode !== null &&
                isset($request->ibanNo) && $request->ibanNo !== null &&
                isset($request->dob) && $request->dob !== null &&
                isset($request->currencyCode) && $request->currencyCode !== null &&
                isset($request->passport) && $request->passport !== null &&
                isset($request->nationality) && $request->nationality !== null &&
                isset($request->livein) && $request->livein !== null &&
                isset($request->swiftCode) && $request->swiftCode !== null
              ) {
                // All required fields are set and not null, you can proceed with your logic.

                $withdraw_arr = [
                  "dob" => $dob,
                  "img_url" => '',
                  "currencyccode" => $request->currencyCode,
                  "swiftcode" => $request->swiftCode,
                  "acctype" => $request->accType,
                  "achname" => $request->accHolderName,
                  "cus_prefer" => strtolower($request->type),
                  "iban_code" => $request->ibanNo,
                  "bank_name" => $request->bankName,
                  "branch_name" => $request->branchName,
                  "branch_code" => $request->branchCode,
                  "emirites_passport" => $request->passport,
                  "acc_no" => $request->accNumber,
                  "nationality" => $nationlaity,
                  "residinglocation" => $livin,
                  "request_id" => $request_id,
                  "from_id" => $user_id,
                  "to_id" => '1',
                  "amount" => $amount,
                  "status" => '0',
                  "deletes" => '0',
                  "createdon" => now(),
                  "transaction_id" => "",
                  "exchangeid" => ""
                ];

                $with_draw_ins = DB::table('withdraw_request')->insertGetId($withdraw_arr);


                if ($with_draw_ins != '') {

                  $dataArr['updated_at'] = now();
                  $dataArr['t_earning'] = $fromclose;
                  $dataArr['dob'] = $dob;
                  $dataArr['bank_name'] = $request->bankName;
                  $dataArr['acctype'] = $request->accType;
                  $dataArr['account_no'] = $request->accNumber;
                  $dataArr['IBAN_code'] = $request->ibanNo;
                  $dataArr['swift_code'] = $request->swiftCode;
                  $dataArr['account_name'] = $request->accHolderName;
                  $dataArr['branch_name']  =  $request->branchName;
                  $dataArr['branch_code'] =  $request->branchCode;
                  $dataArr['currency_code'] = $request->currencyCode;
                  $dataArr['passport'] = $request->passport;



                  $result_arr = array_intersect_key(auth()->user()->toArray(), $dataArr);

                  $log_arr = array_diff($result_arr, $dataArr);



                  $user_profile_log_arr['user_id'] = $user_id;

                  $user_profile_log_arr['changed_by'] = $user_id;

                  $user_profile_log_arr['changed_data'] = json_encode($log_arr);

                  $user_profile_log_arr['updated_datetime'] = now();

                  $user_profile_log_arr['ip'] = $request->ip();

                  $user_profile_log_ins =  DB::table('user_profile_activity_log')->insert($user_profile_log_arr);





                  $updateUser = DB::table('user_register')
                    ->where('id', $user_id)
                    ->where('deletes', '0')
                    ->update([
                      'updated_at' => now(),
                      't_earning' => $fromclose,
                      'dob' => $dob,
                      'bank_name' => $request->bankName,
                      'acctype' => $request->accType,
                      'account_no' => $request->accNumber,
                      'IBAN_code' => $request->ibanNo,
                      'swift_code' => $request->swiftCode,
                      'account_name' => $request->accHolderName,
                      'branch_name' => $request->branchName,
                      'branch_code' => $request->branchCode,
                      'currency_code' => $request->currencyCode,
                      'passport' => $request->passport
                    ]);


                  if ($updateUser) {



                    $withdrawData = [
                      "userid" => $user_id,
                      "type" => 'withdraw',
                      "request" => json_encode($request->all()),
                      "status" => '0',
                      "deletes" => '0',
                      "createdon" => now()
                    ];

                    $log = DB::table('bank_change_log')->insert($withdrawData);



                    // Send Mail
                    $emailSend =  Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', $ccAddress);





                    $response = ['status' => 'success', 'message' => "Request has been sent", 'data' =>  "Request has been sent"];
                    goto returnFVI;
                    // }
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'Withdrawal request failed!', 'error' => 'Withdrawal request failed!'];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => 'Please Fill All Fields!', 'error' => 'Please Fill All Fields!'];
                goto returnFVI;
              }
            } else if ($request->type == 'EXCHANGE') {


              $request->exchangeID = Controller::BlockSQLInjection($request->exchangeID);
              if ($request->exchangeID == '' || $request->exchangeID == null || $request->exchangeID == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid exchange ID!', 'error' => 'Please use a valid exchange ID!'];
                goto returnFVI;
              }

              $request->dob = Controller::BlockSQLInjection($request->dob);
              if ($request->dob == '' || $request->dob == null || $request->dob == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid date of birth!', 'error' => 'Please use a valid date of birth!'];
                goto returnFVI;
              }

              $request->passport = Controller::BlockSQLInjection($request->passport);
              if ($request->passport == '' || $request->passport == null || $request->passport == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid passport!', 'error' => 'Please use a valid passport!'];
                goto returnFVI;
              }


              if (

                isset($request->type) && $request->type !== null &&
                isset($request->exchangeID) && $request->exchangeID !== null &&
                isset($request->withdrawAmt) && $request->withdrawAmt !== null &&
                isset($request->passport) && $request->passport !== null &&
                isset($request->nationality) && $request->nationality !== null &&
                isset($request->livein) && $request->livein !== null &&
                isset($request->dob) && $request->dob !== null


              ) {


                $withdrawData = [
                  "dob" => $dob,
                  "img_url" => '',
                  "cus_prefer" => strtolower($request->type),
                  "exchangeid" => $request->exchangeID,
                  "nationality" => $nationlaity,
                  "residinglocation" => $livin,
                  "request_id" => $request_id,
                  "from_id" => $user_id,
                  "to_id" => 1,
                  "amount" => $amount,
                  "status" => '0',
                  "deletes" => '0',
                  "createdon" => now(),
                  "transaction_id" => "",
                  "swiftcode" => "",
                  "acctype" => "",
                  "currencyccode" => "",
                  "achname" => "",
                  "acc_no" => "",
                  "bank_name" => "",
                  "iban_code" => "",
                  "branch_name" => "",
                  "branch_code" => "",
                  "emirites_passport" => $request->passport
                ];

                $with_draw_ins =   DB::table('withdraw_request')->insertGetId($withdrawData);


                if ($with_draw_ins != '') {


                  $exchangeDataArr['updated_at'] = now();

                  $exchangeDataArr['t_earning'] = $fromclose;

                  $exchangeDataArr['dob'] = $dob;

                  $exchangeDataArr['passport'] = $request->passport;

                  $exchangeDataArr['exchangeid'] = $request->exchangeID;



                  $result_arr = array_intersect_key(auth()->user()->toArray(), $exchangeDataArr);

                  $log_arr = array_diff($result_arr, $exchangeDataArr);



                  $user_profile_log_arr['user_id'] = $user_id;

                  $user_profile_log_arr['changed_by'] = $user_id;

                  $user_profile_log_arr['changed_data'] = json_encode($log_arr);

                  $user_profile_log_arr['updated_datetime'] = now();

                  $user_profile_log_arr['ip'] = $request->ip();



                  $user_profile_log_ins =  DB::table('user_profile_activity_log')->insert($user_profile_log_arr);


                  $updateUser =  DB::table('user_register')
                    ->where('id', $user_id)
                    ->where('deletes', '0')
                    ->update([
                      'updated_at' => now(),
                      't_earning' => $fromclose,
                      'dob' => $dob,
                      'passport' => $request->passport,
                      'exchangeid' => $request->exchangeID
                    ]);

                  if ($updateUser) {
                    $withdrawData = [
                      "userid" => $user_id,
                      "type" => 'withdraw',
                      "request" => json_encode($request->all()),
                      "status" => '0',
                      "deletes" => '0',
                      "createdon" => now()
                    ];

                    $log = DB::table('bank_change_log')->insert($withdrawData);

                    // Send Mail
                    $emailSend =  Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', $ccAddress);

                    $response = ['status' => 'success', 'message' => "Request has been sent", 'data' =>  "Request has been sent"];
                    goto returnFVI;
                  }
                }
              } else {
                $response = ['status' => 'failed', 'message' => 'Please Fill All Fields!', 'error' => 'Please Fill All Fields!'];
                goto returnFVI;
              }
            } else {
              $response = ['status' => 'failed', 'message' => 'The type not found!', 'error' => 'The type not found!'];
              goto returnFVI;
            }
          } else {
            if (count($img_arr) > 0 && $with_draw_ins != '') {
              $response = ['status' => 'failed', 'message' => "File upload Field! kindly try again!", 'error' => "File upload Field! kindly try again!"];
              goto returnFVI;
              // $result['type'] = '0';
              // $result['result'] = 'File upload Field! kindly try again!';
            }
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Minimum Withdrawal amount is AED 300.00', 'error' => 'Minimum Withdrawal amount is AED 300.00'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Minimum Withdrawal amount is AED 300.00', 'error' => 'Minimum Withdrawal amount is AED 300.00'];
        goto returnFVI;
      }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }











  public function checkValidity(Request $request)
  {
    try {
      $token = $request->bearerToken();
      $user = auth()->user();
      if ($token) {
        // $token = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        $token = PersonalAccessToken::find($token);
        // dd($token);
      } else {
        $response = ['status' => 'failed', 'message' => 'Invalid or expired token', 'error' => 'Invalid or expired token'];
        goto returnFVI;
      }
      $tokanData = $token->toArray();
      $expiresAt = $tokanData['abilities']['expires_at'];
      // dd($tokanData['abilities']['expires_at']);
      if ($token && Carbon::now()->lt($expiresAt)) {
        $response = ['status' => 'success', 'message' => "The access token is valied!", 'data' =>  "The access token is valied!"];
        goto returnFVI;
      } else {

        if ($user) {
          // Get the current access token
          $currentAccessToken = $user->currentAccessToken();

          if ($currentAccessToken) {
            // Delete the current access token
            $currentAccessToken->delete();

            $response = ['status' => 'failed', 'message' => 'Invalid or expired token', 'error' => 'Invalid or expired token'];
            goto returnFVI;
          } else {
            // Handle the case where the current access token is not found.
            $response = ['status' => 'failed',  'message' => 'Current access token not found',  'error' => 'Current access token not found'];
            return response($response, 404); // Return a 404 Not Found status code
          }
        } else {
          // Handle the case where the user is not authenticated.
          $response = ['status' => 'failed', 'message' => 'User not authenticated', 'error' => 'User not authenticated'];
          return response($response, 401); // Return a 401 Unauthorized status code
        }
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }







  public function updateMyprofile(Request $request)
  {
    try {

      $user_id = auth()->user()->id;


      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $arr = [];





      if ($request->bulidingname != '' && $request->bulidingname != null && $request->bulidingname != 'null') {

        $request->bulidingname = Controller::BlockSQLInjection($request->bulidingname);
        if ($request->bulidingname == '' || $request->bulidingname == null || $request->bulidingname == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid buliding name!', 'error' => 'Please use a valid buliding name!'];
          goto returnFVI;
        }


        $arr["building_name"] = utf8_encode($request->bulidingname);
      }





      if ($request->nationality != '' && $request->nationality != null && $request->nationality != 'null') {

        $request->nationality = Controller::BlockSQLInjection($request->nationality);
        if ($request->nationality == '' || $request->nationality == null || $request->nationality == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid nationality!', 'error' => 'Please use a valid nationality!'];
          goto returnFVI;
        }

        $nation = (int) $request->nationality;
        $inNaCountry = DB::table('countries')
          ->select('name')
          ->where('flag', '=', '1')
          ->where('id', '=', $request->nationality)
          ->where('name', '!=', '')
          ->orderBy('id', 'DESC')
          ->first()
          ->name;


        $arr["nationality"] =  trim(DB::connection()->getPdo()->quote($inNaCountry), "'");
      }







      if ($request->address != '' && $request->address != null && $request->address != 'null') {

        $request->address = Controller::BlockSQLInjection($request->address);
        if ($request->address == '' || $request->address == null || $request->address == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid address!', 'error' => 'Please use a valid address!'];
          goto returnFVI;
        }

        $billadd = (int) $request->address;

        $inStateCountry = DB::table('states')
          ->select('name')
          ->where('flag', '=', '1')
          ->where('id', '=', $request->address)
          ->where('name', '!=', '')
          ->orderBy('id', 'DESC')
          ->first()
          ->name;


        $arr["address"] =  trim(DB::connection()->getPdo()->quote($inStateCountry), "'");
      }





      if ($request->billing_city != '' && $request->billing_city != null && $request->billing_city != 'null') {
        $request->billing_city = Controller::BlockSQLInjection($request->billing_city);
        if ($request->billing_city == '' || $request->billing_city == null || $request->billing_city == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid billing city!', 'error' => 'Please use a valid billing city!'];
          goto returnFVI;
        }
        $billcity = (int) $request->billing_city;


        $inCityCountry = DB::table('cities')
          ->select('name')
          ->where('flag', '=', 1)
          ->where('id', '=', $request->billing_city)
          ->where('name', '!=', '')
          ->orderBy('id', 'DESC')
          ->first()
          ->name;


        $arr["city"] = trim(DB::connection()->getPdo()->quote($inCityCountry), "'");
      }





      if ($request->achname != '' && $request->achname != null && $request->achname != 'null') {

        $request->achname = Controller::BlockSQLInjection($request->achname);
        if ($request->achname == '' || $request->achname == null || $request->achname == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid account holder name!', 'error' => 'Please use a valid account holder name!'];
          goto returnFVI;
        }
        $arr["account_name"] = $request->achname;
      }



      if ($request->account_no != '' && $request->account_no != null && $request->account_no != 'null') {
        $request->account_no = Controller::BlockSQLInjection($request->account_no);
        if ($request->account_no == '' || $request->account_no == null || $request->account_no == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid account no!', 'error' => 'Please use a valid account no!'];
          goto returnFVI;
        }
        $arr["account_no"] = $request->account_no;
      }



      if ($request->account_name != '' && $request->account_name != null && $request->account_name != 'null') {
        $request->account_name = Controller::BlockSQLInjection($request->account_name);
        if ($request->account_name == '' || $request->account_name == null || $request->account_name == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid account name!', 'error' => 'Please use a valid account name!'];
          goto returnFVI;
        }
        $arr["acctype"] = $request->account_name;
      }



      if ($request->bank_name != '' && $request->bank_name != null && $request->bank_name != 'null') {
        $request->bank_name = Controller::BlockSQLInjection($request->bank_name);
        if ($request->bank_name == '' || $request->bank_name == null || $request->bank_name == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid bank name!', 'error' => 'Please use a valid bank name!'];
          goto returnFVI;
        }
        $arr["bank_name"] = $request->bank_name;
      }



      if ($request->branchname != '' && $request->branchname != null && $request->branchname != 'null') {

        $request->branchname = Controller::BlockSQLInjection($request->branchname);
        if ($request->branchname == '' || $request->branchname == null || $request->branchname == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid branch name!', 'error' => 'Please use a valid branch name!'];
          goto returnFVI;
        }
        $arr["branch_name"] = $request->branchname;
      }



      if ($request->branchcode != '' && $request->branchcode != null && $request->branchcode != 'null') {

        $request->branchcode = Controller::BlockSQLInjection($request->branchcode);
        if ($request->branchcode == '' || $request->branchcode == null || $request->branchcode == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid branch code!', 'error' => 'Please use a valid branch code!'];
          goto returnFVI;
        }
        $arr["branch_code"] = $request->branchcode;
      }



      if ($request->iban_code != '' && $request->iban_code != null && $request->iban_code != 'null') {
        $request->iban_code = Controller::BlockSQLInjection($request->iban_code);
        if ($request->iban_code == '' || $request->iban_code == null || $request->iban_code == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid iban code!', 'error' => 'Please use a valid iban code!'];
          goto returnFVI;
        }
        $arr["IBAN_code"] = $request->iban_code;
      }





      if ($request->swift_code != '' && $request->swift_code != null && $request->swift_code != 'null') {
        $request->swift_code = Controller::BlockSQLInjection($request->swift_code);
        if ($request->swift_code == '' || $request->swift_code == null || $request->swift_code == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid swift code!', 'error' => 'Please use a valid swift code!'];
          goto returnFVI;
        }
        $arr["swift_code"] = $request->swift_code;
      }




      if ($request->dob != '' && $request->dob != null && $request->dob != 'null') {
        $request->dob = Controller::BlockSQLInjection($request->dob);
        if ($request->dob == '' || $request->dob == null || $request->dob == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid date of birth!', 'error' => 'Please use a valid date of birth!'];
          goto returnFVI;
        }
        $arr["dob"] = date("Y-m-d", strtotime($request->dob));
      }





      if ($request->currency_code != '' && $request->currency_code != null && $request->currency_code != 'null') {
        $request->currency_code = Controller::BlockSQLInjection($request->currency_code);
        if ($request->currency_code == '' || $request->currency_code == null || $request->currency_code == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid currency code!', 'error' => 'Please use a valid currency code!'];
          goto returnFVI;
        }
        $arr["currency_code"] = $request->currency_code;
      }



      if ($request->passport != '' && $request->passport != null && $request->passport != 'null') {
        $request->passport = Controller::BlockSQLInjection($request->passport);
        if ($request->passport == '' || $request->passport == null || $request->passport == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid passport!', 'error' => 'Please use a valid passport!'];
          goto returnFVI;
        }

        $arr["passport"] = $request->passport;
      }




      if ($request->exchangeid != '' && $request->exchangeid != null && $request->exchangeid != 'null') {
        $request->exchangeid = Controller::BlockSQLInjection($request->exchangeid);
        if ($request->exchangeid == '' || $request->exchangeid == null || $request->exchangeid == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid exchange id!', 'error' => 'Please use a valid exchange id!'];
          goto returnFVI;
        }
        $arr["exchangeid"] = $request->exchangeid;
      }

      $img_arr = explode(',', $request->totalimg);
      if (isset($request->totalimg) && $request->totalimg !== null && count($img_arr) > 0) {
        foreach ($img_arr as $value) {

          $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
          $maxFileSize = 5120; // Maximum file size in kilobytes (5 MB)

          if ($request->hasFile($value)) {
            $file = $request->file($value);
            $name = $file->getClientOriginalName();

            if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {
              // File is valid, an image, within allowed MIME types, and within size limits



              // dd(phpinfo());

              // Your code to process the uploaded image goes here
              $filePath = 'nationaldraw/' . $user_id . '/' . md5($user_id .  $name  . time()) . '.' . $file->getClientOriginalExtension();

              $store = Storage::disk('spaces')->put(
                '/' . $filePath,
                file_get_contents($request->file($value)->getRealPath()),
                'public'
              );
              // dd($store);

              if ($store) {
                // $store = Storage::disk('spaces')->put('/nationaldraw/uploads/test' . $name, file_get_contents($request->file('file')->getRealPath()), 'public');
                // $url = Storage::disk('spaces')->url($filePath);
                // $url = str_replace(env('DO_FULL_URL'), env('DO_REDIRECT_URL'), Storage::disk('spaces')->url($filePath));
                $url = $filePath;

                if ($url != '') {
                  $data = [
                    'digitalURL' => $url
                  ];

                  $nty = '';
                  if ('emimage1' == $value || 'emimage3' == $value) {
                    $nty = 'FRONT';
                  } else {
                    $nty = 'BACK';
                  }

                  $withdrawData = [
                    "img_url" => $url,
                    "type" => $nty,
                    "user_id" => $user_id,
                    "status" => '0',
                    "deletes" => '0',
                    "createdon" => now()
                  ];

                  $with_draw_ins =   DB::table('user_images')->insert($withdrawData);
                } else {
                  $response = ['status' => 'failed', 'message' => "URL generation failed!", 'error' => "URL generation failed!"];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => "The File Upload Failed!", 'error' => "The File Upload Failed!"];
                goto returnFVI;
              }
            } else {
              $response = ['status' => 'failed', 'message' => "format not supported!", 'error' => "format not supported!"];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => "Kindly send Correct files!", 'error' => "Kindly send Correct files!"];
            goto returnFVI;
          }
        }
      }


      $arr['updated_at'] = now();





      $updateUser = DB::table('user_register')
        ->where('id', $user_id)
        ->where('deletes', '0')
        ->update($arr);

      if ($updateUser) {



        $result_arr = array_intersect_key(auth()->user()->toArray(), $arr);


        $log_arr = array_diff($result_arr, $arr);



        $user_profile_log_arr['user_id'] = $user_id;
        $user_profile_log_arr['changed_by'] = $user_id;
        $user_profile_log_arr['changed_data'] = json_encode($log_arr);
        $user_profile_log_arr['updated_datetime'] = now();
        $user_profile_log_arr['ip'] = $request->ip();



        $user_profile_log_ins = DB::table('user_profile_activity_log')->insert($user_profile_log_arr);


        $withdraw_arr = [
          "userid" => $user_id,
          "type" => 'userupdate',
          "request" => json_encode($request->all()),
          "status" => '0',
          "deletes" => '0',
          "createdon" => now()
        ];


        $with_draw_ins = DB::table('bank_change_log')->insert($withdraw_arr);



        $response = ['status' => 'success', 'message' => "Updated Successfully!", 'data' =>  "Updated Successfully!"];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'Update process failed!', 'error' => 'Update process failed!'];
        goto returnFVI;
      }

      // }






      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function tempEmail(Request $request)
  {
    $sendEmail = Controller::composeEmail($request->ip(), 'developer@cwd.co.in', 'test', '<h1>Welcome</h1>');

    return $sendEmail;
  }
}
