<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Api\v5\onlineTicket;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\v5\CCAvenueGateway;
use App\Http\Controllers\Api\v5\PlayController;
use Razorpay\Api\Api;

class billingController extends Controller
{

  protected $monthlyCycle;
  protected $yearlyCycle;

  // Constructor to initialize the property
  public function __construct()
  {
    $this->monthlyCycle = 36;
    $this->yearlyCycle = 3;
  }

  public function addToCardProduct(Request $request)
  {
    try {
      $response = [];
      $data = [];
      $buildCheckOut = [];
      $finalTotal = 0;
      $couponErr = [];


      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $purchaseType = $request->purchaseType ?? '';
      $planType = $request->cartDetails['planType'] ?? '';
      
    //   return $request->all();

      if ($purchaseType == '' || $purchaseType == null || $purchaseType == 'null') {
        $response = ['status' => 'failed', 'message' => 'Purchase type is required.', 'error' => 'Purchase type is required.'];
        goto returnFVI;
      }

      if ($planType == '' || $planType == null || $planType == 'null') {
        $response = ['status' => 'failed', 'message' => 'plan type is required.', 'error' => 'plan type is required.'];
        goto returnFVI;
      }




      if ($purchaseType === 'NEW') {

        // $couponCode = $request->couponCode ?? '';
        $productID = $request->cartDetails['productID'] ?? '';

        $quantity = $request->cartDetails['quantity'] ?? '';

        $crmID = $request->cartDetails['crmID'] ?? '';

        $crmDetails = null;
        if ($productID == '' || $productID == null || $productID == 'null' || $productID < 1) {
          $response = ['status' => 'failed', 'message' => 'Product ID is Required!', 'error' => 'Product ID is Required!'];
          goto returnFVI;
        }

        if ($quantity == '' || $quantity == null || $quantity == 'null' || $quantity < 1) {
          $response = ['status' => 'failed', 'message' => 'Quantity is Required!', 'error' => 'Quantity is Required!'];
          goto returnFVI;
        }


        if (isset($crmID) && $crmID != '' && $crmID != null && $crmID > 0) {
          $crmDetails = DB::table('crm')
            ->select('id', 'crmRefernce', 'subDomainName', 'userID', 'currentPlanBenefits', 'expiryDate', 'partnerID', 'crmStatus')
            ->where('id', $crmID)
            ->where('userID', auth()->user()->id)
            ->where('deletes', '0')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
              $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);
              return $item;
            });

          if (count($crmDetails) < 1) {
            $response = ['status' => 'failed', 'message' => 'The CRM details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
            goto returnFVI;
          }

          $crmDetails = $crmDetails[0];
          $crmID = $crmDetails->id;

          unset($crmDetails->currentPlanBenefits);
          // dd($crmID);
        }



        $play = new PlayController();
        $playReq = new Request(["productID" => $productID]);
        $triggerPlan = $play->planList($playReq);
        $planList = json_decode($triggerPlan->content());

        if ($planList->status != 'success') {
          $response = [
            'status' => 'failed',
            'message' => 'The Plan List missing!',
            'error' => 'The Plan List missing!'
          ];
          goto returnFVI;
        }

        $plans = $planList->data->planList->{strtolower($planType)};

        if (count($plans) < 1) {
          $response = ['status' => 'failed', 'message' => 'Product details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
          goto returnFVI;
        }

        $product = collect($plans)->firstWhere('id', $productID);

        // dd( $product, $productID, $plans, $planList);

        if ($product == '' || $product == null || $product == 'null') {
          $response = ['status' => 'failed', 'message' => 'Product details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
          goto returnFVI;
        }


        if ($product->productType === 'TRAIL') {
          $checkTrail = DB::table('invoice')
            ->where('user_id', $user_id)
            ->where('deletes', '0')
            ->where('planType', 'TRAIL')
            ->orderBy('id', 'DESC')
            ->get();

          if ($checkTrail->count() < 1) {
            $planType = $product->productType;
          }

          // dd($checkTrail, $planType);
        }

        if ($planType != 'TRAIL') {
          if (intval($product->price) < 1 || $product->price == null) {
            $response = [
              'status' => 'failed',
              'message' => 'Product prize is missing.',
              'error' => 'Please try again later.'
            ];
            goto returnFVI;
          }
        }

        if ((isset($request->subscriptions) && $request->subscriptions)) {

          $quantity = $planType === 'TRAIL' ? $quantity : ($planType === 'MONTHLY' ? $quantity * $this->monthlyCycle : $quantity * $this->yearlyCycle);

          $buildCheckOut['subscriptionDetails'] = [
            'total_count' => $quantity,
          ];
        }

        $buildCheckOut['quantity'] = $quantity;
        $buildCheckOut['productID'] = $productID;

        $buildCheckOut['discountAmt'] = 0;
        $buildCheckOut['finalTotal'] = floatval($product->price);
        $buildCheckOut['crmID'] = ($crmID != '' && $crmID != null && isset($crmID) ? $crmID : null);
        $buildCheckOut['productDetails'] = $product;
        $buildCheckOut['startDate'] = date('Y-m-d');
        $buildCheckOut['planType'] = $planType;
        $buildCheckOut['purchaseType'] = $purchaseType;
        $buildCheckOut['purchaseDate'] = date('Y-m-d H:i:s');

        $buildCheckOut['trailsDays'] = 0;
        $buildCheckOut['trailsDays'] = $product->trailsDays;

        // dd($product->validityMorY * $quantity);


        // $validityDays = ($planType === 'MONTHLY' ? Controller::getDaysInMonthOrYear('month', $quantity) : ($planType === 'YEARLY' ? Controller::getDaysInMonthOrYear('year', $quantity) : $quantity));


        $validityDays = $planType === 'YEARLY' ? Controller::getDaysInMonthOrYear('year', $quantity) : Controller::getDaysInMonthOrYear('month', $quantity);


        // dd( $validityDays, Controller::getDaysInMonthOrYear('month', $quantity) );

        $buildCheckOut['noOfDays'] =  $validityDays;


        $buildCheckOut['totalDays'] = ($buildCheckOut['trailsDays'] * $quantity) + $buildCheckOut['noOfDays'];


        $buildCheckOut['crmDetails'] = $crmDetails;

        $buildCheckOut['expiryDate'] = Carbon::parse($buildCheckOut['startDate'])->addDays($buildCheckOut['totalDays'])->format('Y-m-d');


        $buildCheckOut['ip'] = $request->ip() ?? '';
        $buildCheckOut['timestamp'] = now();

        $buildCheckOut['userID'] = $user_id;
        $buildCheckOut['shipamount'] = 0;


        $buildCheckOut['grandtotal'] = $buildCheckOut['finalTotal'] + $buildCheckOut['shipamount'];

        if ($buildCheckOut['discountAmt'] > 0) {
          $buildCheckOut['grandtotal'] = ($buildCheckOut['grandtotal'] - $buildCheckOut['discountAmt']);
        }

        $buildCheckOut['currency'] = $product->currency;

        $buildCheckOut['taxType'] = $product->currency === 'INR' ? 'GST' : null;



        $trid = DB::table(((isset($request->subscriptions) && $request->subscriptions) ? 'subscriptions' : 'payment_history'))->select("id")->orderby('id', 'desc')->limit(1)->first();
        $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;



        $tran_id = ((isset($request->subscriptions) && $request->subscriptions) ? 'S' : '') . 'GR' . uniqid(8) . date('Hi') . ($inTrans + 1);

        $buildCheckOut['subscriptions'] = ((isset($request->subscriptions) && $request->subscriptions) ? true : false);

        $buildCheckOut['transaction_id'] = $tran_id;
        
        $utm_source = $request->utm_source??null;
        $utm_campaign = $request->utm_campaign??null;

        $checkout_arr = [
          'createdon' => now(),
          'crontime' => now(),
          // 'crmID' => ($crmID != '' && $crmID != null && isset($crmID) ? $crmID : 0),
          // 'draw_id' => $drawData->data->activeDraw->id,
          'ip' => ($request->ip() ?? ''),
          'user_id' => $user_id,
          'status' => '0',
          'transaction_id' => $tran_id,
          'checkout_response' => json_encode($buildCheckOut),
        //   'subID' => 'PRODUCT',
          'gateway' => '',
          'finaltotal' => $buildCheckOut['finalTotal'],
          'receipt_no' => '',
          'reference' => '',
          'purchaseType' => $purchaseType,
          'planType' => $planType,
          'shipamount' => $buildCheckOut['shipamount'],
          'grandtotal' => $buildCheckOut['grandtotal'],
          'currency' => $buildCheckOut['currency'],
          'utm_source' => $utm_source,
          'utm_campaign' => $utm_campaign
        ];


        if ((isset($request->subscriptions) && $request->subscriptions)) {
          $checkout_arr['subscription_id'] = $checkout_arr['transaction_id'];
          $checkout_arr['start_date'] = $buildCheckOut['startDate'];
          $checkout_arr['expiryDate'] = $buildCheckOut['expiryDate'];
          $checkout_arr['sub_status'] = null;
          $checkout_arr['total_cycles'] = $buildCheckOut['subscriptionDetails']['total_count'];
          $checkout_arr['updatedon'] = now();
          $checkout_arr['product_id'] = $productID;
        //   $checkout_arr['utm_source'] = $utm_source;
        //   $checkout_arr['utm_campaign'] = $utm_campaign;
          unset($checkout_arr['transaction_id']);
        }

        $lastInsertId = DB::table(((isset($request->subscriptions) && $request->subscriptions) ? 'subscriptions' : 'payment_history'))->insertGetId($checkout_arr);

        if ($lastInsertId != '') {

          // if ($buildCheckOut['payByLinkID'] != null) {
          //   $updatePayLink = DB::table('payby_link')
          //     ->where('id', $buildCheckOut['payByLinkID'])
          //     // ->where('type', $type)
          //     ->where(function ($query) {
          //       $query->where('status', '!=', 'Paid')
          //         ->orWhereNull('status');
          //     })
          //     // ->where('status', '!=', 'Paid')
          //     ->where('user_id', $user_id)
          //     ->where('deletes', '0')
          //     ->update(['status' => 'Initiated', 'Newpayment_id' => $lastInsertId]);
          // }

          $data['cartData'] = $buildCheckOut;
          $data['transaction_id'] = $tran_id;

          $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
          goto returnFVI;
        } else {
          $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
          goto returnFVI;
        }
      } else if ($purchaseType === 'RENEWAL' || $purchaseType === 'UPGRADE') {

        $crmID = $request->cartDetails['crmID'] ?? '';

        $productID = $request->cartDetails['productID'] ?? '';

        $quantity = $request->cartDetails['quantity'] ?? '';

        if ($productID == '' || $productID == null || $productID == 'null' || $productID < 1) {
          $response = ['status' => 'failed', 'message' => 'Product ID is Required!', 'error' => 'Product ID is Required!'];
          goto returnFVI;
        }

        if ($quantity == '' || $quantity == null || $quantity == 'null' || $quantity < 1) {
          $response = ['status' => 'failed', 'message' => 'Quantity is Required!', 'error' => 'Quantity is Required!'];
          goto returnFVI;
        }


        if ($crmID == '' || $crmID == null || $crmID == 'null' || $crmID < 1) {
          $response = ['status' => 'failed', 'message' => 'CRM ID is Required!', 'error' => 'CRM ID is Required!'];
          goto returnFVI;
        }




        $play = new PlayController();
        $playReq = new Request(["productID" => $productID]);

        $triggerPlan = $play->planList($playReq);
        $planList = json_decode($triggerPlan->content());

        if ($planList->status != 'success') {
          $response = [
            'status' => 'failed',
            'message' => 'The Plan List missing!',
            'error' => 'The Plan List missing!'
          ];
          goto returnFVI;
        }


        // $plans = $planList->data->planList->renewal;
        $plans = $planList->data->planList->{strtolower($planType)};




        if (count($plans) < 1) {
          $response = ['status' => 'failed', 'message' => 'Product details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
          goto returnFVI;
        }

        $product = collect($plans)->firstWhere('id', $productID);
        // dd($product);


        if ($product == '' || $product == null || $product == 'null') {
          $response = ['status' => 'failed', 'message' => 'Product details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
          goto returnFVI;
        }

        if (intval($product->price) < 1 || $product->price == null) {
          $response = [
            'status' => 'failed',
            'message' => 'Product prize is missing.',
            'error' => 'Please try again later.'
          ];
          goto returnFVI;
        }


        // $crmDetails = DB::table('crm')
        //   ->where('id', $crmID)
        //   ->where('userID', $user_id)
        //   ->where('deletes', '0')
        //   ->orderBy('id', 'DESC')
        //   ->limit(1)
        //   ->get();

        $crmDetails = DB::table('crm')
          ->select('id', 'crmRefernce', 'subDomainName', 'userID', 'currentPlanBenefits', 'expiryDate', 'partnerID', 'crmStatus')
          ->where('id', $crmID)
          ->where('userID', auth()->user()->id)
          ->where('deletes', '0')
          ->orderBy('id', 'desc')
          ->get()
          ->map(function ($item) {
            // Assuming 'transactionID', 'invoiceID', and 'currentPlanBenefits' are JSON strings
            // $item->transactionID = json_decode($item->transactionID, true); // Convert JSON string to array
            // $item->invoiceID = json_decode($item->invoiceID, true);       // Convert JSON string to array
            $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true); // Convert JSON string to array
  
            return $item;
          });

        if (count($crmDetails) < 1) {
          $response = ['status' => 'failed', 'message' => 'The CRM details are missing or incomplete. Please try again later.', 'error' => 'try after some time!'];
          goto returnFVI;
        }

        $crmDetails = $crmDetails[0];

        // $crmDetails->currentPlanBenefits = json_decode($crmDetails->currentPlanBenefits, true);

        $expiryDate = $crmDetails->expiryDate;

        // $buildCheckOut['pastPlanDetails'] = $crmDetails->currentPlanBenefits;

        $buildCheckOut['discountAmt'] = 0;

        if (Carbon::parse($expiryDate)->isPast()) {
          $buildCheckOut['startDate'] = date('Y-m-d');
          // dd("Expired", $crmDetails->expiryDate);
        } else {
          // $buildCheckOut['startDate'] = Carbon::parse($expiryDate)->addDay()->format('Y-m-d');


          if ($purchaseType === 'UPGRADE') {

            $buildCheckOut['startDate'] = date('Y-m-d');

            $inBetweenDays = Carbon::parse($expiryDate)->diffInDays(Carbon::now());


            $pGTtotal = (float) $crmDetails->currentPlanBenefits['grandtotal'];

            $fgTtotalDays = $crmDetails->currentPlanBenefits['totalDays'] - $crmDetails->currentPlanBenefits['trailsDays'];

            if ($inBetweenDays > 0) {

              $perDayPrize = $pGTtotal / $fgTtotalDays;



              $buildCheckOut['discountAmt'] = round($perDayPrize * $inBetweenDays);
            }
          } else {
            $buildCheckOut['startDate'] = Carbon::parse($expiryDate)->addDay()->format('Y-m-d');
          }



          // dd("Not expired", $crmDetails->expiryDate);
        }




        // dd($buildCheckOut);

        $buildCheckOut['quantity'] = $quantity;
        $buildCheckOut['productID'] = $productID;


        $buildCheckOut['finalTotal'] = floatval($product->price) * $buildCheckOut['quantity'];
        $buildCheckOut['crmID'] = $crmID;



        $buildCheckOut['pastPlanHis'] = [];

        $planHis = $crmDetails->currentPlanBenefits['pastPlanHis'] ?? [];

        unset($crmDetails->currentPlanBenefits['pastPlanHis']);

        array_push($planHis, $crmDetails->currentPlanBenefits);

        $buildCheckOut['pastPlanHis'] = $planHis;


        unset($crmDetails->currentPlanBenefits);

        // crmDetails

        $buildCheckOut['crmDetails'] = $crmDetails;

        // $buildCheckOut['crmDetails'] = $crmDetails->currentPlanBenefits;

        $buildCheckOut['productDetails'] = $product;

        $buildCheckOut['planType'] = $planType;
        $buildCheckOut['purchaseType'] = $purchaseType;
        $buildCheckOut['purchaseDate'] = date('Y-m-d H:i:s');
        $buildCheckOut['trailsDays'] = 0;
        $buildCheckOut['trailsDays'] = $product->trailsDays;

        $buildCheckOut['noOfDays'] = $product->validityDays;
        $buildCheckOut['currency'] = $product->currency;

        $buildCheckOut['totalDays'] = $buildCheckOut['trailsDays'] + $buildCheckOut['noOfDays'];

        $buildCheckOut['expiryDate'] = Carbon::parse($buildCheckOut['startDate'])->addDays($buildCheckOut['totalDays'])->format('Y-m-d');

        $buildCheckOut['validityDays'] = Carbon::parse($buildCheckOut['startDate'])->diffInDays(Carbon::parse($buildCheckOut['expiryDate'])) + 1;



        $buildCheckOut['ip'] = $request->ip() ?? '';
        $buildCheckOut['timestamp'] = now();

        $buildCheckOut['userID'] = $user_id;
        $buildCheckOut['shipamount'] = 0;


        $buildCheckOut['grandtotal'] = $buildCheckOut['finalTotal'] + $buildCheckOut['shipamount'];

        if ($buildCheckOut['discountAmt'] > 0) {
          $buildCheckOut['grandtotal'] = ($buildCheckOut['grandtotal'] - $buildCheckOut['discountAmt']);
        }

        if (intval($buildCheckOut['grandtotal'] ?? 0) < 1) {
          $response = [
            'status' => 'failed',
            'message' => 'The minimum purchase is INR 1.',
            'error' => 'The minimum purchase is INR 1.',
          ];
          goto returnFVI;
        }



        $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();
        $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;

        $tran_id = ($purchaseType === 'UPGRADE' ? 'UP' : 'RE') . uniqid(8) . date('Hi') . ($inTrans + 1);


        $buildCheckOut['transaction_id'] = $tran_id;

        $checkout_arr = [
          'createdon' => now(),
          'crontime' => now(),
          'crmID' => $crmID,
          // 'draw_id' => $drawData->data->activeDraw->id,
          'ip' => ($request->ip() ?? ''),
          'user_id' => $user_id,
          'status' => '0',
          'transaction_id' => $tran_id,
          'checkout_response' => json_encode($buildCheckOut),
          // 'category' => 'PRODUCT',
          'gateway' => '',
          'finaltotal' => $buildCheckOut['finalTotal'],
          'receipt_no' => '',
          'reference' => '',
          'purchaseType' => $purchaseType,
          'planType' => $planType,
          'shipamount' => $buildCheckOut['shipamount'],
          'grandtotal' => $buildCheckOut['grandtotal']
        ];

        $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

        if ($lastInsertId != '') {

          // if ($buildCheckOut['payByLinkID'] != null) {
          //   $updatePayLink = DB::table('payby_link')
          //     ->where('id', $buildCheckOut['payByLinkID'])
          //     // ->where('type', $type)
          //     ->where(function ($query) {
          //       $query->where('status', '!=', 'Paid')
          //         ->orWhereNull('status');
          //     })
          //     // ->where('status', '!=', 'Paid')
          //     ->where('user_id', $user_id)
          //     ->where('deletes', '0')
          //     ->update(['status' => 'Initiated', 'Newpayment_id' => $lastInsertId]);
          // }

          $data['cartData'] = $buildCheckOut;
          $data['transaction_id'] = $tran_id;

          $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
          goto returnFVI;
        } else {
          $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
          goto returnFVI;
        }

        // $product_id = $request->cartDetails['productID'] ?? '';

        // if ($product_id == '' || $product_id == null || $product_id == 'null' || $product_id < 1) {
        //   $response = ['status' => 'failed', 'message' => 'Product ID Required!', 'error' => 'Kindly send the product ID!'];
        //   goto returnFVI;
        // }

        // $planAmount = $request->cartDetails['planAmount'];

        // if ($request->referenceID == '' || $request->referenceID == null || $request->referenceID == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'The renewal reference ID is missing!', 'error' => 'The renewal reference ID is missing!'];
        //   goto returnFVI;
        // }

        // if ($planAmount == '' || $planAmount == null || $planAmount == 'null' || $planAmount < 1 || $planAmount > 360) {
        //   $response = ['status' => 'failed', 'message' => 'The amount Required!', 'error' => 'The amount Required!'];
        //   goto returnFVI;
        // }



        // $ndticket = DB::table('ndticket')
        //   // ->whereRaw("JSON_CONTAINS(transactionIds, '\"$transaction_id\"', '$')")
        //   // ->whereRaw("JSON_CONTAINS(paymentHistoryIds, '{$payment_history[0]->id}', '$')")
        //   ->where([
        //     ['referenceID', 'LIKE', $request->referenceID],
        //     ['netTotal', '<', 360],
        //     ['deletes', '=', '0'],
        //     ['userId', '=', $user_id]
        //   ])
        //   ->orderBy('id', 'DESC')
        //   ->limit(1)
        //   ->get();
        // // dd($ndticket);
        // if ($ndticket->count() < 1) {
        //   $response = ['status' => 'failed', 'message' => 'The ticket not found!', 'error' => 'The ticket not found!'];
        //   goto returnFVI;
        // }

        // $ndticket = $ndticket[0];

        // $product = DB::table('product')
        //   ->where([
        //     ['deletes', '=', '0'],
        //     ['type', '=', 'RENEWAL'],
        //     ['id', '=', $product_id],
        //   ])
        //   ->orderBy('id', 'ASC')
        //   ->select(DB::raw('CAST(eligibleDraw AS JSON) AS eligibleDraw'), 'id', 'name', 'raffleQuantity', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', image) AS image"), 'rate', 'validityDays', 'type', 'description', 'chances', 'maxPrize', 'eligibleDrawCount', 'subscription_frequency')
        //   ->first();

        // if ($product == null || $product->id == '' || $product->id == null) {
        //   $response = ['status' => 'failed', 'message' => 'The product not found!', 'error' => 'The product not found!'];
        //   goto returnFVI;
        // }

        // $product->eligibleDraw = json_decode($product->eligibleDraw);

        // // This is Start of Ticket Renewal
        // $ticketExpriy = $ndticket->endDate;
        // if (date("Y-m-d") > $ndticket->endDate) {
        //   $ticketExpriy = date("Y-m-d");
        // }
        // // dd($ticketExpriy);
        // $firstDraw = DB::table('draw')
        //   ->where([
        //     // ['saleDate', '>=', $ticketExpriy],
        //     ['deletes', '=', '0'],
        //     // ['dailyThirllStatus', '=', 'Active']
        //   ])
        //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
        //   ->orderBy('saleDate', 'ASC')
        //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
        //   ->limit(1)
        //   ->get();

        // if ($firstDraw->count() < 1) {
        //   $response = ['status' => 'failed', 'message' => 'The first draw not found!', 'error' => 'The first draw not found!'];
        //   goto returnFVI;
        // }

        // $firstDraw = $firstDraw[0];
        // $firstDraw->salesStrategyFormula = json_decode($firstDraw->salesStrategyFormula);

        // $activeDraw = DB::table('draw')
        //   ->where([
        //     ['saleDate', '>=', $ticketExpriy],
        //     ['deletes', '=', '0'],
        //     ['dailyThirllStatus', '=', 'Active']
        //   ])
        //   ->orderBy('saleDate', 'ASC')
        //   ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
        //   ->limit(1)
        //   ->get();

        // // dd($ticketExpriy);

        // if ($activeDraw->count() < 1) {
        //   $response = ['status' => 'failed', 'message' => 'The active draw was not found.', 'error' => 'The active draw could not be located.'];

        //   goto returnFVI;
        // }

        // // Ticket Selling Concept
        // $activeDraw = $activeDraw[0];
        // $activeDraw->salesStrategyFormula = json_decode($activeDraw->salesStrategyFormula);
        // $salesStrategyFormula = $activeDraw->salesStrategyFormula;

        // $lastDraw = DB::table('draw')
        //   ->where([
        //     ['deletes', '=', '0'],
        //     // ['dailyThirllStatus', 'IN', 'Active', 'Completed']
        //   ])
        //   ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
        //   ->orderBy('saleDate', 'DESC')
        //   ->select('id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'salesStrategyFormula', 'todayGoldPrize', 'previewData', 'deletes')
        //   ->limit(1)
        //   ->get();


        // if ($lastDraw->count() < 1) {
        //   $response = ['status' => 'failed', 'message' => 'The last draw not found!', 'error' => 'The last draw not found!'];
        //   goto returnFVI;
        // }

        // // Ticket Selling Concept
        // $lastDraw = $lastDraw[0];
        // $lastDraw->salesStrategyFormula = json_decode($lastDraw->salesStrategyFormula);


        // if ($product->rate < 1 || $product->rate == null) {
        //   $response = [
        //     'status' => 'failed',
        //     'message' => 'Product prize is missing.',
        //     'error' => 'Please try again later.'
        //   ];
        //   goto returnFVI;
        // }

        // /// Plan based raffle Calculation
        // $specificDate = Carbon::createFromDate(
        //   Carbon::parse($lastDraw->saleDate)->format('Y'),
        //   Carbon::parse($lastDraw->saleDate)->format('m'),
        //   Carbon::parse($lastDraw->saleDate)->format('d')
        // );

        // $daysDifference = Carbon::parse($activeDraw->saleDate)->diffInDays(Carbon::parse($specificDate)) + 1;

        // if (in_array($product_id, [6])) {
        //   $product->validityDays = $daysDifference < 0 ? $product->validityDays : $daysDifference;
        //   $planAmount = $product->rate;
        // } else if (in_array($product_id, [5])) {
        //   $product->raffleQuantity = round((intval($ndticket->netTotal) + $planAmount) / 30);

        //   // Balance Amount Calculation
        //   $specificDates = Carbon::createFromDate(
        //     Carbon::parse($ticketExpriy)->format('Y'),
        //     Carbon::parse($ticketExpriy)->format('m'),
        //     Carbon::parse($ticketExpriy)->format('d')
        //   );

        //   $balanceDaysDifference = Carbon::parse($firstDraw->saleDate)->diffInDays(Carbon::parse($specificDates));

        //   $product->validityDays = $planAmount - ($balanceDaysDifference - $ndticket->validityPeriod);


        //   if ($planAmount > round((360 - intval($ndticket->netTotal)))) {
        //     $planAmount = round(360 - intval($ndticket->netTotal));
        //   }
        // } else {
        //   $response = ['status' => 'failed', 'message' => 'The product not found!', 'error' => 'The product not found!'];
        //   goto returnFVI;
        // }


        // if ($product->validityDays < 1) {
        //   $response = [
        //     'status' => 'failed',
        //     'message' => 'The validity date cannot be zero.',
        //     'error' => 'The validity date cannot be zero.'
        //   ];
        //   goto returnFVI;
        // }


        // if ($product->raffleQuantity < 1 || $product->raffleQuantity > 12) {
        //   $response = [
        //     'status' => 'failed',
        //     'message' => 'The raffle quantity already exists. Please provide a valid plan amount.',
        //     'error' => 'The raffle quantity already exists. Please provide a valid plan amount.'
        //   ];
        //   goto returnFVI;
        // }
        // $buildCheckOut['payByLinkID'] = $request->payByLinkID ?? null;
        // // Total Quantity
        // $buildCheckOut['planAmount'] = $planAmount;
        // $buildCheckOut['paidSoFar'] = $ndticket->netTotal;
        // // Total Raffle IDs
        // $buildCheckOut['totalRaffles'] = $product->raffleQuantity;
        // $buildCheckOut['productID'] = $product_id;
        // $finalTotal = in_array($product_id, [5]) ? $planAmount : ($planAmount - $ndticket->netTotal);

        // if ($finalTotal < 1) {
        //   $response = ['status' => 'failed', 'message' => 'The minimum payable amount must be greater than 0.', 'error' => 'The minimum payable amount must be greater than 0.'];
        //   goto returnFVI;
        // }

        // $buildCheckOut['finalTotal'] = $finalTotal;
        // $buildCheckOut['ticketID'] = $ndticket->id;
        // $buildCheckOut['productRate'] = $product->rate;
        // $buildCheckOut['productName'] = intval($product->rate) . ' ' . $product->name;
        // $buildCheckOut['startDate'] = date('Y-m-d', strtotime($activeDraw->saleDate));

        // $buildCheckOut['discount'] = 0;
        // $buildCheckOut['discountID'] = null;

        // $buildCheckOut['eligibleDraw'] = $product->eligibleDraw;
        // $buildCheckOut['eligibleDrawCount'] = 0;
        // $buildCheckOut['purchaseType'] = $request->purchaseStatus;
        // $buildCheckOut['subscription'] = $product->subscription_frequency;
        // $buildCheckOut['ticketNo'] = $ndticket->ticketNo;

        // $buildCheckOut['noOfDays'] = $product->validityDays;
        // $buildCheckOut['endDate'] = Carbon::parse($buildCheckOut['startDate'])->addDays($buildCheckOut['noOfDays'])->format('Y-m-d');
        // // dd($buildCheckOut);

        // $buildCheckOut['noOfDaysRenewal'] = $finalTotal;

        // $countArr = [
        //   'eligibleDraw' => $buildCheckOut['eligibleDraw'],
        //   'startDate' => $ticketExpriy,
        //   'endDate' => $buildCheckOut['endDate'],
        //   'maxPrize' => $product->maxPrize,
        //   'totalRaffles' => $buildCheckOut['totalRaffles']
        // ];
        // $countResponse = json_decode($this->eliDrawCount($countArr)->getContent(), true);
        // $buildCheckOut['eligibleDrawCount'] = $countResponse['data']['eligiableCount'];

        // $buildCheckOut['drawCount'] = isset($countResponse['data']) ? $countResponse['data']['drawCounts'] : '';
        // $buildCheckOut['prizeInGram'] = isset($countResponse['data']) ? $countResponse['data']['prizeInGram'] : '';


        // if (date("Y-m-d") < $ndticket->endDate) {
        //   $countArr = [
        //     'eligibleDraw' => $buildCheckOut['eligibleDraw'],
        //     'startDate' => date('Y-m-d'),
        //     'endDate' => $buildCheckOut['startDate'],
        //     'maxPrize' => $product->maxPrize
        //   ];
        //   $countResponse = json_decode($this->eliDrawCount($countArr)->getContent(), true);

        //   $buildCheckOut['special']['drawCount'] = isset($countResponse['data']) ? $countResponse['data']['drawCounts'] : '';

        //   $buildCheckOut['special']['eligibleDrawCount'] = isset($countResponse['data']) ? $countResponse['data']['eligiableCount'] : 0;
        //   $buildCheckOut['special']['totalChances'] = $buildCheckOut['special']['eligibleDrawCount'] * $buildCheckOut['totalRaffles'];

        //   $buildCheckOut['eligibleDrawCount'] += $buildCheckOut['special']['eligibleDrawCount'];
        //   // dd($countArr);
        // }

        // if ($buildCheckOut['eligibleDrawCount'] < 1) {
        //   $response = [
        //     'status' => 'failed',
        //     'message' => 'The Eligible draw count is zero.',
        //     'error' => 'The Eligible draw count is zero.'
        //   ];
        //   goto returnFVI;
        // }

        // $buildCheckOut['totalChances'] = $buildCheckOut['eligibleDrawCount'] * $buildCheckOut['totalRaffles'];
        // $buildCheckOut['balanceRaffles'] = $buildCheckOut['totalRaffles'] - $ndticket->totalRaffle;

        // $buildCheckOut['ticketReferenceID'] = $request->referenceID;


        // $buildCheckOut['quantity'] = in_array($product_id, [5]) ? ($buildCheckOut['balanceRaffles'] > 0 ? $buildCheckOut['balanceRaffles'] : 1) : 1;

        // if (isset($request->pageTitle) && $request->pageTitle === 'productDetails') {
        //   $data['cartData'] = $buildCheckOut;
        //   $response = ['status' => 'success', 'message' => 'Product details collected successfully!', 'data' => $data];
        //   goto returnFVI;
        // }
        // // dd($buildCheckOut);
        // $buildCheckOut['shipping'] = $request->shipping;
        // $buildCheckOut['shipamount'] = 0;
        // $buildCheckOut['grandtotal'] = $finalTotal + $buildCheckOut['shipamount'];
        // $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();
        // $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;

        // $tran_id = 'ND' . uniqid(8) . date('Hi') . ($inTrans + 1);
        // $checkout_arr = [
        //   'createdon' => now(),
        //   'crontime' => now(),
        //   'draw_id' => $activeDraw->id,
        //   'ip' => ($request->ip() ?? ''),
        //   'user_id' => $user_id,
        //   'status' => '0',
        //   'transaction_id' => $tran_id,
        //   'checkout_response' => json_encode($buildCheckOut),
        //   'category' => 'PRODUCT',
        //   'gateway' => '',
        //   'finaltotal' => $buildCheckOut['finalTotal'],
        //   'receipt_no' => '',
        //   'reference' => '',
        //   'renewalStatus' => $request->purchaseStatus,
        //   'shipamount' => $buildCheckOut['shipamount'],
        //   'grandtotal' => $buildCheckOut['grandtotal']
        // ];

        // $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

        // if ($lastInsertId != '') {
        //   $data['cartData'] = $buildCheckOut;
        //   $data['transaction_id'] = $tran_id;

        //   $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
        //   goto returnFVI;
        // } else {
        //   $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
        //   goto returnFVI;
        // }
      }



      // else if ($request->purchaseStatus === 'PICKUPTOSTORE' || $request->purchaseStatus === 'DELIVERYTOCUSTOMER') {


      //   $activeDraw = Controller::getActiveDrawData();
      //   $drawData = json_decode($activeDraw->content());

      //   if ($drawData->status != 'success') {
      //     $response = [
      //       'status' => 'failed',
      //       'message' => 'Active Draw Details Not Found!',
      //       'error' => 'Active Draw Details Not Found'
      //     ];
      //     goto returnFVI;
      //   }


      //   $invoiceID = $request->invoiceID;

      //   if ($invoiceID == '' || $invoiceID == null || $invoiceID == 'null' || $invoiceID < 1) {
      //     $response = ['status' => 'failed', 'message' => 'Invoice ID Required!', 'error' => 'Kindly send the Invoice ID!'];
      //     goto returnFVI;
      //   }

      //   // dd(date('w', strtotime($request->pickupDate)));

      //   if (date('w', strtotime($request->pickupDate)) == 0 || date('w', strtotime($request->pickupDate)) == 6) {
      //     $response = ['status' => 'failed', 'message' => 'Selected date is a ' . date('l', strtotime($request->pickupDate)) . '. Please choose a different date!', 'error' => 'Selected date is a ' . date('l', strtotime($request->pickupDate)) . '. Please choose a different date!'];
      //     goto returnFVI;
      //   }

      //   $shippingRecheck = DB::table('shipping_request')
      //     ->where('parent_invoice_id', $invoiceID)
      //     ->where('deletes', '0')
      //     ->limit(1)
      //     ->get();

      //   if ($shippingRecheck->count() > 0) {
      //     $response = ['status' => 'failed', 'message' => 'The shipping request for this invoice has already been completed!', 'error' => 'The shipping request for this invoice has already been completed!'];
      //     goto returnFVI;
      //   }




      //   $invoicesRecheck = DB::table('invoice')
      //     // ->where('ticketReferenceID', 'LIKE',   $ndticket[0]->referenceID)
      //     // ->where('id', '=',   $invoiceID)
      //     // ->whereIn('renewalStatus', ['NEW', 'RENEWAL'])
      //     ->where('user_id', $user_id)
      //     ->where('parentInvoiceID', $invoiceID)

      //     ->where('deletes', '0')->orderByDesc('id')
      //     // ->limit(1)
      //     ->get();




      //   if ($invoicesRecheck->count() > 0) {
      //     $response = ['status' => 'failed', 'message' => 'The shipping request for this invoice has already been completed!', 'error' => 'The shipping request for this invoice has already been completed!'];
      //     goto returnFVI;
      //   }





      //   // TODO
      //   $invoices = DB::table('invoice')
      //     // ->where('ticketReferenceID', 'LIKE',   $ndticket[0]->referenceID)
      //     ->where('id', '=', $invoiceID)
      //     ->whereIn('renewalStatus', ['NEW', 'RENEWAL'])
      //     ->where('user_id', $user_id)
      //     ->where('parentInvoiceID', '0')
      //     ->orderByDesc('id')
      //     ->limit(1)
      //     ->get();




      //   if ($invoices->count() < 1) {
      //     $response = ['status' => 'failed', 'message' => 'The invoice not found!', 'error' => 'The invoice not found!'];
      //     goto returnFVI;
      //   }

      //   $invoices = $invoices[0];

      //   $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours', strtotime($invoices->createdon)));
      //   if (Carbon::now()->lt($expiresAt) && $invoices->delivery_status === null) {



      //     $ndticket = DB::table('ndticket')
      //       // ->whereRaw("JSON_CONTAINS(transactionIds, '\"$transaction_id\"', '$')")
      //       // ->whereRaw("JSON_CONTAINS(paymentHistoryIds, '{$payment_history[0]->id}', '$')")
      //       ->where([
      //         ['referenceID', 'LIKE', $invoices->ticketReferenceID],
      //         // ['netTotal', '<', 360],
      //         ['deletes', '=', '0'],
      //         ['userId', '=', $user_id]
      //       ])
      //       ->orderBy('id', 'DESC')
      //       ->limit(1)
      //       ->get();
      //     // dd($ndticket);
      //     if ($ndticket->count() < 1) {
      //       $response = ['status' => 'failed', 'message' => 'The ticket not found!', 'error' => 'The ticket not found!'];
      //       goto returnFVI;
      //     }

      //     $ndticket = $ndticket[0];





      //     $invoices->cart = json_decode($invoices->cart);

      //     $buildCheckOut['parentInvoiceDetails'] = $invoices;

      //     $ndticket->transactionIds = json_decode($ndticket->transactionIds);
      //     $ndticket->paymentHistoryIds = json_decode($ndticket->paymentHistoryIds);

      //     $ndticket->raffleIds = json_decode($ndticket->raffleIds);

      //     $buildCheckOut['ticketDetails'] = $ndticket;

      //     $buildCheckOut['discount'] = 0;
      //     $buildCheckOut['discountID'] = null;
      //     $buildCheckOut['payByLinkID'] = $request->payByLinkID ?? null;
      //     $buildCheckOut['purchaseType'] = $request->purchaseStatus;
      //     $buildCheckOut['pickupDate'] = $request->pickupDate;
      //     $buildCheckOut['totalPens'] = intval($invoices->grandtotal) + intval($invoices->discount);
      //     $buildCheckOut['productCost'] = floatval($invoices->grandtotal) + intval($invoices->discount);
      //     $buildCheckOut['finalTotal'] = $buildCheckOut['productCost'];



      //     $buildCheckOut['packingCost'] = floatval($buildCheckOut['productCost']) * 0.4445;
      //     $buildCheckOut['handlingCost'] = floatval($buildCheckOut['productCost']) * 0.5555;

      //     if ($request->purchaseStatus === 'DELIVERYTOCUSTOMER') {
      //       $buildCheckOut['packingCost'] = round(floatval($buildCheckOut['productCost']) * 0.4444);
      //       $buildCheckOut['handlingCost'] = round(floatval($buildCheckOut['productCost']) * 0.5555);
      //     }


      //     $buildCheckOut['deliveryCost'] = ($request->purchaseStatus === 'PICKUPTOSTORE' ? 0 : (($request->purchaseStatus === 'DELIVERYTOCUSTOMER' ? 80 : 0)));




      //     $buildCheckOut['shipping'] = $invoices->deliveryType;

      //     $buildCheckOut['shipamount'] = floatval($buildCheckOut['deliveryCost'] + $buildCheckOut['packingCost'] + $buildCheckOut['handlingCost']);

      //     $buildCheckOut['subTotal'] = floatval($buildCheckOut['finalTotal'] + $buildCheckOut['shipamount']);

      //     $buildCheckOut['paidSoFor'] = $buildCheckOut['finalTotal'];
      //     // $buildCheckOut['grandtotal'] =  floatval($buildCheckOut['finalTotal'] +  $buildCheckOut['shipamount']);

      //     $buildCheckOut['grandtotal'] = floatval($buildCheckOut['subTotal'] - $buildCheckOut['paidSoFor']);


      //     if ($buildCheckOut['grandtotal'] < 1) {
      //       $response = ['status' => 'failed', 'message' => 'The minimum payable amount must be greater than 0.', 'error' => 'The minimum payable amount must be greater than 0.'];
      //       goto returnFVI;
      //     }

      //     // dd( $buildCheckOut['shipamount']);

      //     // dd($buildCheckOut['parentInvoiceDetails']->cart->shipingAddress);


      //     $buildCheckOut['shipingAddress'] = ($request->purchaseStatus === 'PICKUPTOSTORE' ? null : (($request->purchaseStatus === 'DELIVERYTOCUSTOMER' ? $buildCheckOut['parentInvoiceDetails']->cart->shipingAddress : null)));

      //     if ($request->purchaseStatus === 'DELIVERYTOCUSTOMER' && $buildCheckOut['grandtotal'] >= 360) {
      //       $checkAddressBook = DB::table('address_book')
      //         ->where('user_id', $user_id)
      //         ->where('deletes', '0')
      //         ->select('id AS addressBookID', 'name', 'dialCode', 'mobile', 'email', 'doorno', 'street', 'city', 'state', 'country', 'landmark', 'postal_code')
      //         ->orderBy('id', 'DESC')
      //         ->limit(1)
      //         ->get();


      //       if ($checkAddressBook->count() < 1) {
      //         $response = [
      //           'status' => 'failed',
      //           'message' => 'Shipping address not found.',
      //           'error' => 'Shipping address not found.'
      //         ];
      //         goto returnFVI;
      //       }


      //       // $countryName = $checkAddressBook[0]->country;

      //       // $buildCheckOut['shipamount'] = strtolower($countryName) === strtolower('United Arab Emirates') ? 200 : 500;
      //       // $buildCheckOut['shipamount'] = 0;
      //       $buildCheckOut['shipingAddress'] = $checkAddressBook[0];
      //     }



      //     $buildCheckOut['pickupAddress'] = ($request->purchaseStatus === 'PICKUPTOSTORE' ? 'Al Ttay, Al Khawaneej Dubai-UAE' : (($request->purchaseStatus === 'DELIVERYTOCUSTOMER' ? null : 'Al Ttay, Al Khawaneej Dubai-UAE')));

      //     // dd(strval($buildCheckOut['parentInvoiceDetails']->cart->productRate));
      //     $buildCheckOut['productName'] = trim(str_replace(strval($buildCheckOut['parentInvoiceDetails']->cart->productRate), '', $buildCheckOut['parentInvoiceDetails']->cart->productName));

      //     if ($request->purchaseStatus === 'PICKUPTOSTORE') {
      //       $buildCheckOut['pickUpStartTime'] = $buildCheckOut['pickupDate'] . ' 11:00 AM';
      //       $buildCheckOut['pickUpEndTime'] = $buildCheckOut['pickupDate'] . ' 03:00 PM';
      //     }

      //     $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();
      //     $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;

      //     $tran_id = ($request->purchaseStatus === 'PICKUPTOSTORE' ? 'PR' : (($request->purchaseStatus === 'DELIVERYTOCUSTOMER' ? 'DR' : 'SP'))) . uniqid(8) . date('Hi') . ($inTrans + 1);
      //     $checkout_arr = [
      //       'createdon' => now(),
      //       'crontime' => now(),
      //       'draw_id' => $drawData->data->activeDraw->id,
      //       'ip' => ($request->ip() ?? ''),
      //       'user_id' => $user_id,
      //       'status' => '0',
      //       'transaction_id' => $tran_id,
      //       'checkout_response' => json_encode($buildCheckOut),
      //       'category' => 'PICKUP',
      //       'gateway' => '',
      //       'finaltotal' => $buildCheckOut['finalTotal'],
      //       'receipt_no' => '',
      //       'reference' => '',
      //       'renewalStatus' => $request->purchaseStatus,
      //       'shipamount' => $buildCheckOut['shipamount'],
      //       'grandtotal' => $buildCheckOut['grandtotal'],
      //       'ticketReferenceID' => $ndticket->referenceID
      //     ];

      //     $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

      //     if ($lastInsertId != '') {
      //       $data['cartData'] = $buildCheckOut;
      //       $data['transaction_id'] = $tran_id;

      //       $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
      //       goto returnFVI;
      //     } else {
      //       $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
      //       goto returnFVI;
      //     }
      //   } else {
      //     $response = ['status' => 'failed', 'message' => 'The Invoice not eligiable for the Pick up.', 'error' => 'The Invoice not eligiable for the Pick up.'];
      //     goto returnFVI;
      //   }
      // } else if ($request->purchaseStatus === 'WALLETDEPOSIT') {

      //   $depositAmt = intval($request->depositAmt);
      //   if ($depositAmt == '' || $depositAmt == null || $depositAmt == 'null' || $depositAmt < 1) {
      //     $response = [
      //       'status' => 'failed',
      //       'message' => 'Kindly send a valid deposit amount greater than zero!',
      //       'error' => 'Kindly send a valid deposit amount greater than zero!'
      //     ];
      //     goto returnFVI;
      //   }

      //   if ($depositAmt < 5) {
      //     $response = [
      //       'status' => 'failed',
      //       'message' => 'Minimum top-up amount is 5 AED.',
      //       'error' => 'Minimum top-up amount is 5 AED.'
      //     ];
      //     goto returnFVI;
      //   }

      //   if ($depositAmt > 50000) {
      //     $response = [
      //       'status' => 'failed',
      //       'message' => 'Maximum top-up amount is 50,000 AED.',
      //       'error' => 'Maximum top-up amount is 50,000 AED.'
      //     ];
      //     goto returnFVI;
      //   }

      //   $buildCheckOut['userID'] = auth()->user()->id;
      //   $buildCheckOut['depositAmt'] = (float) $depositAmt;
      //   $buildCheckOut['existWalletAmt'] = number_format(auth()->user()->walletBalance, 2);
      //   $buildCheckOut['finalTotal'] = $buildCheckOut['depositAmt'];
      //   $buildCheckOut['discount'] = 0;
      //   $buildCheckOut['shipamount'] = 0;
      //   $buildCheckOut['grandtotal'] = $buildCheckOut['finalTotal'];
      //   $buildCheckOut['shipping'] = 'pickUpToStore';

      //   // dd($buildCheckOut);
      //   // dd($buildCheckOut);
      //   $trid = DB::table('payment_history')->select("id")->orderby('id', 'desc')->limit(1)->first();
      //   $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;

      //   $tran_id = 'WD' . uniqid(8) . date('Hi') . ($inTrans + 1);
      //   $checkout_arr = [
      //     'createdon' => now(),
      //     'crontime' => now(),
      //     'draw_id' => 0,
      //     'ip' => ($request->ip() ?? ''),
      //     'user_id' => $user_id,
      //     'status' => '0',
      //     'transaction_id' => $tran_id,
      //     'checkout_response' => json_encode($buildCheckOut),
      //     'category' => 'WalletDeposit',
      //     'gateway' => '',
      //     'finaltotal' => (float) $buildCheckOut['finalTotal'],
      //     'receipt_no' => '',
      //     'reference' => '',
      //     'renewalStatus' => 'RECHARGE',
      //     'shipamount' => (float) $buildCheckOut['shipamount'],
      //     'grandtotal' => (float) $buildCheckOut['grandtotal']
      //   ];

      //   // dd($checkout_arr);
      //   $lastInsertId = DB::table('payment_history')->insertGetId($checkout_arr);

      //   if ($lastInsertId != '') {

      //     $data['cartData'] = $buildCheckOut;
      //     $data['transaction_id'] = $tran_id;


      //     $ccAnenue = new CCAvenueGateway();

      //     $paymentInitiate = new Request([
      //       "transaction_id" => $data['transaction_id']
      //     ]);

      //     $paymentInitiate->headers->set('Origin', rtrim($request->header('Origin'), '/'));
      //     $paymentInitiate->headers->set('Authorization', $request->header('Authorization'));

      //     $ccAnenueInit = $ccAnenue->ccavenueInitiate($paymentInitiate);

      //     $ccAvenueRes = json_decode($ccAnenueInit->getContent(), true);
      //     // dd($ndticket);

      //     // $data['annuallyPlan'] = null;
      //     if ($ccAvenueRes['status'] === 'success') {
      //       $data['ccAnenueInit'] = $ccAvenueRes['data'];
      //     } else {
      //       $response = ['status' => 'failed', 'message' => 'Payment initiate process failed!', 'error' => 'Payment initiate process failed!'];
      //       goto returnFVI;
      //     }
      //     // dd($ccAnenueInit);
      //     // dd($data);

      //     $response = ['status' => 'success', 'message' => 'Transaction ID generated successfully!', 'data' => $data];
      //     goto returnFVI;
      //   } else {
      //     $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Transaction ID generation Process failed!'];
      //     goto returnFVI;
      //   }
      // }
      else {
        $response = ['status' => 'failed', 'message' => 'The purchase status is not available.', 'error' => 'The purchase status is not available.'];
        goto returnFVI;
      }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function getSuggestion(Request $request)
  {

    try {
      $response = [];
      $data = [];
      $buildCheckOut = [];
      $finalTotal = 0;

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $activeDraw = Controller::getActiveDrawData();
      $drawData = json_decode($activeDraw->content());


      if ($drawData->status != 'success') {
        $response = [
          'status' => 'failed',
          'message' => 'Active Draw Details Not Found!',
          'error' => 'Active Draw Details Not Found'
        ];
        goto returnFVI;
      }

      // $salesStrategyFormula = $drawData->data->activeDraw->salesStrategyFormula;
      // $productList = $drawData->data->productList;
      $productList = DB::table('product')
        ->where([
          ['deletes', '=', '0'],
          ['type', '=', 'RENEWAL'],
          // ['id', '=', $product_id],
        ])
        ->orderBy('id', 'ASC')
        ->select('id', DB::raw('concat(ROUND(qty), \' \', name) AS name'), 'qty', 'raffleQuantity', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', image) AS image"), 'rate', 'validityDays', 'type', 'description', 'chances', 'maxPrize', 'eligibleDrawCount', 'subscription_frequency')
        ->get();

      if ($productList->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The product not found!', 'error' => 'The product not found!'];
        goto returnFVI;
      }

      // $product = collect($productList)->firstWhere('id', $product_id);
      $ndWhereArr = [
        // ['referenceID', 'LIKE', 'NDc8c831fe086a29bbfb7a47934a86ab99'], // Only Testing
        ['validityPeriod', '<', 360],
        ['netTotal', '<', 360],
        ['deletes', '=', '0'],
        ['userId', '=', $user_id],
        ['totalRaffle', '<', 12]
      ];


      if (isset($request->referenceID) && $request->referenceID != '' && $request->referenceID != null) {
        $ndWhereArr[] = ['referenceID', 'LIKE', $request->referenceID];
      }

      // dd($ndWhereArr);

      $ndticket = DB::table('ndticket')
        // ->whereRaw("JSON_CONTAINS(transactionIds, '\"$transaction_id\"', '$')")
        // ->whereRaw("JSON_CONTAINS(paymentHistoryIds, '{$payment_history[0]->id}', '$')")
        ->where($ndWhereArr)
        ->orderBy('netTotal', 'DESC')
        ->limit(1)
        ->get();

      // dd($ndticket);
      if ($ndticket->count() < 1) {
        // $response = ['status' => 'failed', 'message' => 'The ticket not found!', 'error' => 'The ticket not found!'];
        // goto returnFVI;

        $response = ['status' => 'failed', 'message' => 'No ticket has been purchased by this customer', 'data' => 'No ticket has been purchased by this customer'];
        goto returnFVI;
      }

      $ndticket = $ndticket[0];

      // New 1-6-2024 for discount
      // dd($ndticket->netTotal);
      // $ndticket->netTotal = ($ndticket->discount > 0 ? ($ndticket->netTotal + $ndticket->discount) : $ndticket->netTotal);

      $lastDraw = DB::table('draw')
        ->where([
          ['deletes', '=', '0'],
          // ['dailyThirllStatus', 'IN', 'Active', 'Completed']
        ])
        ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
        ->orderBy('saleDate', 'DESC')
        ->select('id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'salesStrategyFormula', 'todayGoldPrize', 'previewData', 'deletes')
        ->limit(1)
        ->get();


      if ($lastDraw->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The last draw not found!', 'error' => 'The last draw not found!'];
        goto returnFVI;
      }

      $lastDraw = $lastDraw[0];
      // $lastDraw->salesStrategyFormula = json_decode($lastDraw->salesStrategyFormula);


      $aunnalReq = new Request([
        "cartDetails" => [
          "productID" => 6,
          "planAmount" => intval(360 - $ndticket->netTotal)
        ],
        "referenceID" => $ndticket->referenceID,
        "purchaseStatus" => "RENEWAL",
        "pageTitle" => "productDetails"
      ]);

      $aunnalRes = $this->addToCardProduct($aunnalReq);
      $anuData = json_decode($aunnalRes->getContent(), true);

      // dd($ndticket);

      $data['annuallyPlan'] = null;
      if ($anuData['status'] === 'success') {
        $data['annuallyPlan'] = [
          'planDetails' => $anuData['data']['cartData'],
          'productDetail' => collect($productList)->firstWhere('id', 6)
        ];
      }


      /// Monthly Plan Start
      $data['monthlyPlan'] = null;


      $BalanceRaffle = 12 - $ndticket->totalRaffle;
      $balanceAmt = 360 - $ndticket->netTotal;
      $count = intval($balanceAmt / 30);
      $remainder = $balanceAmt % 30;

      if ($remainder < 1) {
        $remainder = 30;
      } else {
        $BalanceRaffle++;
      }

      $planList = [];
      $raffleCount = 0;



      for ($i = 0; $i < $BalanceRaffle; $i++) {
        $amount = $remainder;

        if ($amount >= 30) {
          $raffleCount++;
        }

        $remainder += 30;

        $endDate = Carbon::parse($lastDraw->resultDate)->subDays($amount)->format('Y-m-d');

        $planList[] = [
          'raffle' => $raffleCount,
          'amount' => $amount,
          'endDate' => $endDate,
          'dropDownText' => $amount . ($raffleCount < 1 ? ' (Monthly Bumper)' : ' (' . $raffleCount . ' more Raffle IDs)'),
        ];
      }



      // dd($planList);

      $endDatesColumn = array_reverse(array_column($planList, 'endDate'));

      if (count($endDatesColumn) < 1) {
        if ($data['annuallyPlan'] != null) {
          $response = [
            'status' => 'success',
            'message' => 'The Yearly Plan was collected successfully!',
            'data' => $data
          ];
          goto returnFVI;
        }


        $response = [
          'status' => 'failed',
          'message' => 'The Monthly plan is not available.',
          'error' => [
            'ticketData' => [
              'reference' => $ndticket->referenceID
            ]
          ]
        ];
        goto returnFVI;
      }

      $firstDate = $endDatesColumn[0];
      // dd($planList);
      $planList = collect($planList)->map(function ($item, $index) use ($firstDate, $request, $ndticket) {
        $endDate = Carbon::parse($firstDate)->addDays($item['amount'])->format('Y-m-d');
        $item['endDate'] = $endDate;

        $renewalRes = new Request([
          "cartDetails" => [
            "productID" => 5,
            "planAmount" => $item['amount']
          ],
          "referenceID" => $ndticket->referenceID,
          "purchaseStatus" => "RENEWAL",
          "shipping" => "pickUpToStore",
          "pageTitle" => "productDetails"
        ]);

        $renewalRequest = $this->addToCardProduct($renewalRes);
        $renewalCartData = json_decode($renewalRequest->getContent(), true);

        if ($renewalCartData['status'] === 'success') {
          if (date("Y-m-d") < $item['endDate']) {
            return $item;
          }
        }
        // dd($renewalCartData);
      })->all();

      $planList = array_filter($planList, function ($value) {
        return $value !== null;
      });



      // array_multisort($endDates, SORT_DESC, $planList);

      if (count($planList) < 1) {
        if ($data['annuallyPlan'] != null) {
          $response = [
            'status' => 'success',
            'message' => 'The Yearly Plan was collected successfully!',
            'data' => $data
          ];
          goto returnFVI;
        }

        $response = [
          'status' => 'failed',
          'message' => 'The Monthly plan is not available.',
          // 'error' => 'The Monthly plan is not available.'
          'error' => [
            'ticketData' => [
              'reference' => $ndticket->referenceID
            ]
          ]
        ];
        goto returnFVI;
      }







      $data['monthlyPlan'] = [
        'planList' => array_values($planList),
        'productDetail' => collect($productList)->firstWhere('id', 5),
        'planDetails' => [
          "ticketNo" => $ndticket->ticketNo,
          "ticketReferenceID" => $ndticket->referenceID,
          "ticketID" => $ndticket->id,
          "productID" => 5,
        ]
      ];


      $data['monthlyPlan'] = null;

      $response = [
        'status' => 'success',
        'message' => 'The renewal plan details were collected successfully.',
        'data' => $data
      ];

      goto returnFVI;



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function myTicket(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }



      $ndticket = DB::table('ndticket')
        // ->whereRaw("JSON_CONTAINS(transactionIds, '\"$transaction_id\"', '$')")
        // ->whereRaw("JSON_CONTAINS(paymentHistoryIds, '{$payment_history[0]->id}', '$')")
        ->where([
          // ['referenceID', 'LIKE', 'NDc8c831fe086a29bbfb7a47934a86ab99'], // Only Testing
          // ['validityPeriod', '<', 360],
          // ['netTotal', '<', 360],
          ['deletes', '=', '0'],
          ['userId', '=', $user_id]
        ])
        ->orderBy('createdon', 'DESC')
        // ->limit(1)
        ->get();

      // dd($ndticket);
      if ($ndticket->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'No ticket has been purchased by this customer', 'data' => 'No ticket has been purchased by this customer'];
        goto returnFVI;
      }



      $ndticket = collect($ndticket)->map(function ($item) {
        $item->transactionIds = json_decode($item->transactionIds, true);
        $item->raffleIds = json_decode($item->raffleIds, true);
        $item->paymentHistoryIds = json_decode($item->paymentHistoryIds, true);
        $item->invoiceNo = json_decode($item->invoiceNo, true);

        // $item->name = $item->rate . ' ' . $item->name;
        $subscribtion = [];
        if (intval($item->netTotal) < 360) {

          $planRequest = new Request([
            "referenceID" => $item->referenceID
          ]);

          $planRes = $this->getSuggestion($planRequest);
          $planData = json_decode($planRes->getContent(), true);

          // dd($planData['status']);

          $subscribtion = ($planData['status'] === 'success') ? $planData['data'] : [];
        }
        $item->subscribtion = $subscribtion;

        return $item;
      })->all();


      // dd($ndticket);

      $response = [
        'status' => 'success',
        'message' => 'The tickets purchased by this customer have been successfully collected.',
        'data' => $ndticket
      ];

      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function myWinnings(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $winners = DB::table('winnerlist as w')
        ->select(
          'w.id as winningID',
          'w.draw_id',
          'w.userid',
          'w.drawType',
          'w.winningDrawName',
          'w.winRaffleId',
          'w.email',
          'w.mobile',
          'w.ticketID',
          'w.ticketReferenceID',
          'w.prize',
          DB::raw('CAST(w.prize_amt AS CHAR) AS prize_amt'),
          'w.country',
          'w.residingcountry',
          'w.createdon as winningTime',
          'd.resultDate',
          'nd.ticketNo',
          DB::raw("((nd.grandtotal + nd.discount) - nd.shipamount) AS netTotal"),
          DB::raw("CASE WHEN ((nd.grandtotal + nd.discount) - nd.shipamount) >= 360 THEN 'true' ELSE 'false' END AS fullyPaid"),
          'w.requestType',
          DB::raw("(360 - ((nd.grandtotal + nd.discount) - nd.shipamount)) AS balanceRenewalAmt"),
          // 'gr.id as goldRequestID',
          // 'gr.delivery_status as goldRequestStatus'
        )
        ->leftJoin('ndticket as nd', 'nd.id', '=', 'w.ticketID')
        ->leftJoin('draw as d', 'd.id', '=', 'w.draw_id')
        // ->leftJoin('gold_request as gr', 'gr.winnerlistId', '=', 'w.id')
        // ->where('gr.deletes', '0')

        ->where('w.deletes', '0')
        ->where('w.userid', $user_id)
        ->orderByDesc('winningTime')
        ->get();

      if ($winners->count() < 1) {
        $winners = [];
      }

      // dd($winners);
      $winners = collect($winners)->map(function ($item) {


        $item->goldRequestDetails = null;


        $childInvoices = DB::table('gold_request')
          ->where('winnerlistId', $item->winningID)
          // ->whereIn('renewalStatus', ['PICKUPTOSTORE', 'DELIVERYTOCUSTOMER'])
          ->where('userID', $item->userid)
          ->where('delivery_status', '!=', 'cancelled')
          // ->where('shipping_requestID', '<>', 0)
          ->where('deletes', '0')
          ->orderByDesc('id')
          ->limit(1)
          ->get();


        // dd($childInvoices);

        $statusDescriptions = [
          'requested' => 'Requested',
          'confirmed' => 'Confirmed',
          'out_for_delivery' => 'Out for Delivery',
          'delivered' => 'Delivered',
          'cancelled' => 'Cancelled',
          'collected' => 'Collected'
        ];




        if ($childInvoices->count() > 0) {



          $childInvoices[0]->delivery_status = $statusDescriptions[$childInvoices[0]->delivery_status];




          $item->goldRequestDetails = $childInvoices[0];
        }





        return $item;
      })->all();



      $response = [
        'status' => 'success',
        'message' => 'The winning track has been successfully collected.',
        'data' => [
          'winningData' => $winners
        ]
      ];
      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function transferToWallet(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $winningID = $request->winningID;
      if ($winningID == '' || $winningID == null || $winningID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid winning ID.', 'error' => 'Please provide a valid winning ID.'];
        goto returnFVI;
      }

      $winners = DB::table('winnerlist as w')
        ->select(
          'w.id as winningID',
          'w.draw_id',
          'w.userid',
          'w.drawType',
          'w.winningDrawName',
          'w.winRaffleId',
          'w.email',
          'w.mobile',
          'w.ticketID',
          'w.ticketReferenceID',
          'w.prize',
          // DB::raw('CAST(w.prize_amt AS CHAR) AS prize_amt'),
          'w.prize_amt',
          'w.country',
          'w.residingcountry',
          'w.createdon as winningTime',
          'd.resultDate',
          'nd.ticketNo',
          'nd.netTotal',
          DB::raw("CASE WHEN nd.netTotal >= 360 THEN 'true' ELSE 'false' END AS fullyPaid"),
          'w.requestType',
          DB::raw("(360 - nd.netTotal) AS balanceRenewalAmt")
        )
        ->leftJoin('ndticket as nd', 'nd.id', '=', 'w.ticketID')
        ->leftJoin('draw as d', 'd.id', '=', 'w.draw_id')
        ->where('w.deletes', '0')
        ->where('w.id', $winningID)
        ->where('w.userid', $user_id)
        ->where('w.requestType', null)
        ->orderByDesc('winningTime')
        ->get();

      if ($winners->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The Track not found.', 'error' => 'The Track not found.'];
        goto returnFVI;
      }





      $winners = $winners[0];

      $checkWalletHis = DB::table('wallet_history')->where(
        [
          ['deletes', '=', '0'],
          ['status', '=', '0'],
          ['reference_id', '=', $winners->winningID],
          ['reference_table', '=', 'winnerlist']
        ]
      )->limit(1)->get();

      if ($checkWalletHis->count() > 0) {
        $response = ['status' => 'failed', 'message' => 'The transaction found!', 'error' => 'The transaction found!'];
        goto returnFVI;
      }

      // dd($winners->fullyPaid);
      // die;

      // if (isset($request->fullyPaid) && $request->fullyPaid) {
      //   $winners->fullyPaid === 'true';
      // }

      if (!isset($request->fullyPaid) && $winners->fullyPaid === 'false') {
        $response = [
          'status' => 'failed',
          'message' => 'Please renew your ticket before trying again.',
          'error' => 'Please renew your ticket before trying again.'
        ];

        goto returnFVI;
      }

      $walletBalance = auth()->user()->walletBalance;
      $totalAmt = $winners->prize_amt;
      $finaltotal = $walletBalance + $totalAmt;

      $payArr = [
        "userid" => auth()->user()->id,
        "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
        "umobile" => auth()->user()->mobile,
        "uemail" => auth()->user()->email,
        'opening_balance' => $walletBalance,
        'total' => $totalAmt,
        'closeing_balance' => $finaltotal,
        'point_type' => 'WALLET',
        'transaction_type' => 'CREDIT',
        'card_no' => '',
        'reference_id' => $winners->winningID,
        'reference_table' => 'winnerlist',
        'ip' => ($request->ip() ?? ''),
        'reward_type' => (isset($request->requestType) ? $request->requestType : 'WALLETTRANSFER'),
        'createdon' => now(),
        'updatedon' => now()
      ];

      $wallet_history = DB::table('wallet_history')->insertGetId($payArr);

      if ($wallet_history) {



        // UPDATE `user_register` SET `walletBalance` = '10001.2' WHERE `id` = 158100 AND `deletes` = '0' AND roll_id = '0' AND status = '0';
        $updateUser = DB::table('user_register')
          ->where('id', auth()->user()->id)
          ->where('deletes', '0')
          ->where('roll_id', '0')
          ->where('status', '0')
          ->update([
            'walletBalance' => $finaltotal,
            'lastlogin' => now()
          ]);


        if ($updateUser) {



          $updateWinner = DB::table('winnerlist')
            ->where('id', $winners->winningID)
            ->where('deletes', '0')
            ->update([
              'requestType' => (isset($request->requestType) ? $request->requestType : 'WALLETTRANSFER'),
              'requestTime' => now()
            ]);

          if ($updateWinner) {
            $response = [
              'status' => 'success',
              'message' => 'The amount transferred successfully.',
              'data' => 'The amount transferred successfully.'
            ];
            goto returnFVI;
          } else {
            $response = ['status' => 'failed', 'message' => 'Winner track update process Failed.', 'error' => 'Winner track update process Failed.'];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Wallet update process Failed.', 'error' => 'Wallet update process Failed.'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function AdTransferToWallet(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $winningID = $request->winningID;
      if ($winningID == '' || $winningID == null || $winningID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid winning ID.', 'error' => 'Please provide a valid winning ID.'];
        goto returnFVI;
      }

      $winners = DB::table('winnerlist as w')
        ->select(
          'w.id as winningID',
          'w.draw_id',
          'w.userid',
          'w.drawType',
          'w.winningDrawName',
          'w.winRaffleId',
          'w.email',
          'w.mobile',
          'w.ticketID',
          'w.ticketReferenceID',
          'w.prize',
          // DB::raw('CAST(w.prize_amt AS CHAR) AS prize_amt'),
          'w.prize_amt',
          'w.country',
          'w.residingcountry',
          'w.createdon as winningTime',
          'd.resultDate',
          'nd.ticketNo',
          'nd.netTotal',
          DB::raw("CASE WHEN nd.netTotal >= 360 THEN 'true' ELSE 'false' END AS fullyPaid"),
          'w.requestType',
          DB::raw("(360 - nd.netTotal) AS balanceRenewalAmt")
        )
        ->leftJoin('ndticket as nd', 'nd.id', '=', 'w.ticketID')
        ->leftJoin('draw as d', 'd.id', '=', 'w.draw_id')
        ->where('w.deletes', '0')
        ->where('w.id', $winningID)
        ->where('w.userid', $user_id)
        ->where('w.requestType', null)
        ->orderByDesc('winningTime')
        ->get();

      if ($winners->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The Track not found.', 'error' => 'The Track not found.'];
        goto returnFVI;
      }

      $winners = $winners[0];

      $checkWalletHis = DB::table('wallet_history')->where(
        [
          ['deletes', '=', '0'],
          ['status', '=', '0'],
          ['reference_id', '=', $winners->winningID],
          ['reference_table', '=', 'winnerlist']
        ]
      )->limit(1)->get();

      if ($checkWalletHis->count() > 0) {
        $response = ['status' => 'failed', 'message' => 'The transaction found!', 'error' => 'The transaction found!'];
        goto returnFVI;
      }

      if ($winners->prize_amt < $winners->balanceRenewalAmt) {
        $response = ['status' => 'failed', 'message' => 'Insufficient amount for renewal.', 'error' => 'Insufficient amount for renewal.'];
        goto returnFVI;
      }


      $renewalRes = new Request([
        "cartDetails" => [
          "productID" => 6,
          "planAmount" => $winners->balanceRenewalAmt
        ],
        "referenceID" => $winners->ticketReferenceID,
        "purchaseStatus" => "RENEWAL",
        "shipping" => "pickUpToStore"
      ]);

      $renewalRequest = $this->addToCardProduct($renewalRes);
      $renewalCartData = json_decode($renewalRequest->getContent(), true);

      if ($renewalCartData['status'] === 'success') {
        $transaction_id = $renewalCartData['data']['transaction_id'];

        if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
          $response = ['status' => 'failed', 'message' => 'Failed to generate transaction ID.', 'error' => 'Failed to generate transaction ID.'];
          goto returnFVI;
        }




        /// Start ///
        $TransferRes = new Request([
          "winningID" => $winners->winningID,
          "fullyPaid" => true,
          "requestType" => 'ADWALLETTRANSFER'
        ]);

        $transFirst = $this->transferToWallet($TransferRes);
        $transferData = json_decode($transFirst->getContent(), true);

        if ($transferData['status'] === 'success') {



          // dd($transferData);

          $onlineTicket = new onlineTicket();
          $walletTicketReq = new Request([
            "transaction_id" => $transaction_id,
          ]);

          $walletTicketGenerate = $onlineTicket->onlineTicketGeneration($walletTicketReq);
          $walletTicketData = json_decode($walletTicketGenerate->getContent(), true);

          // dd($walletTicketData);
          if ($walletTicketData['status'] === 'success') {
            $response = [
              'status' => 'success',
              'message' => 'Adjustment with transfer completed successfully.',
              'data' => $walletTicketData['data']
            ];
            goto returnFVI;
          } else {
            $response = [
              'status' => 'failed',
              'message' => 'Failed to process the renewal.',
              'error' => 'Failed to process the renewal.'
            ];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Failed to Wallet Transfer.', 'error' => 'Failed to Wallet Transfer.'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Failed to generate transaction ID.', 'error' => 'Failed to generate transaction ID.'];
        goto returnFVI;
      }

      // dd($winners->fullyPaid);
      // die;
      // if ($winners->fullyPaid === 'false') {
      //   $response = [
      //     'status' => 'failed',
      //     'message' => 'Please renew your ticket before trying again.',
      //     'error' => 'Please renew your ticket before trying again.'
      //   ];

      //   goto returnFVI;
      // }

      // $walletBalance = auth()->user()->walletBalance;
      // $totalAmt = $winners->prize_amt;
      // $finaltotal =  $walletBalance + $totalAmt;

      // $payArr = [
      //   "userid" =>  auth()->user()->id,
      //   "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
      //   "umobile" => auth()->user()->mobile,
      //   "uemail" => auth()->user()->email,
      //   'opening_balance' => $walletBalance,
      //   'total' => $totalAmt,
      //   'closeing_balance' =>  $finaltotal,
      //   'point_type' => 'WALLET',
      //   'transaction_type' => 'CREDIT',
      //   'card_no' => '',
      //   'reference_id' => $winners->winningID,
      //   'reference_table' => 'winnerlist',
      //   'ip' => $request->ip(),
      //   'reward_type' => 'WALLETTRANSFER',
      //   'createdon' => now(),
      //   'updatedon' => now()
      // ];

      // $wallet_history = DB::table('wallet_history')->insertGetId($payArr);

      // if ($wallet_history) {



      //   // UPDATE `user_register` SET `walletBalance` = '10001.2' WHERE `id` = 158100 AND `deletes` = '0' AND roll_id = '0' AND status = '0';
      //   $updateUser = DB::table('user_register')
      //     ->where('id', auth()->user()->id)
      //     ->where('deletes', 0)
      //     ->where('roll_id', 0)
      //     ->where('status', 0)
      //     ->update([
      //       'walletBalance' => $finaltotal,
      //       'lastlogin' => now()
      //     ]);


      //   if ($updateUser) {



      //     $updateWinner = DB::table('winnerlist')
      //       ->where('id', $winners->winningID)
      //       ->where('deletes', '0')
      //       ->update([
      //         'requestType' => 'WALLETTRANSFER',
      //         'requestTime' => now()
      //       ]);

      //     if ($updateWinner) {
      //       $response = [
      //         'status' => 'success',
      //         'message' => 'The amount transferred successfully.',
      //         'data' => 'The amount transferred successfully.'
      //       ];
      //       goto returnFVI;
      //     } else {
      //       $response = ['status' => 'failed', 'message' => 'Winner track update process Failed.', 'error' => 'Winner track update process Failed.'];
      //       goto returnFVI;
      //     }
      //   } else {
      //     $response = ['status' => 'failed', 'message' => 'Wallet update process Failed.', 'error' => 'Wallet update process Failed.'];
      //     goto returnFVI;
      //   }
      // } else {
      //   $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
      //   goto returnFVI;
      // }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  // With Drawal Request
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





      $requestExistCheck = DB::table('withdraw_request')
        ->where('requested_by', $user_id)
        ->where('status', '0')
        ->where('deletes', '0')
        ->orderByDesc('id')
        ->get();

      if ($requestExistCheck->count() > 0) {
        $response = ['status' => 'failed', 'message' => 'You have already requested a withdrawal.', 'error' => 'You have already requested a withdrawal.'];
        goto returnFVI;
      }




      $amount = $request->withdrawAmt;



      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }






      if (intval($amount) >= 3) {

        RE:
        $request_id = 'WR' . Str::random(15) . now()->format('His');
        $req_check = DB::table('withdraw_request')->where('request_id', $request_id)->count();
        if ($req_check > 0) {
          goto RE;
        }

        $my_t_earn = auth()->user()->walletBalance;





        if (floatval($my_t_earn) >= floatval($amount)) {




          $fromclose = floatval($my_t_earn) - floatval($amount);
          $dob = auth()->user()->dob != NULL ? date('Y-m-d', strtotime(auth()->user()->dob)) : date('Y-m-d');



          $request->nationality = Controller::BlockSQLInjection(auth()->user()->nationality);
          if ($request->nationality == '' || $request->nationality == null || $request->nationality == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid nationality!', 'error' => 'Please use a valid nationality!'];
            goto returnFVI;
          }




          $request->livein = Controller::BlockSQLInjection(auth()->user()->residinglocation);
          if ($request->livein == '' || $request->livein == null || $request->livein == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid livein!', 'error' => 'Please use a valid live in!'];
            goto returnFVI;
          }



          $nationlaity = $request->nationality;

          $livin = $request->livein;





          $request->type = Controller::BlockSQLInjection($request->type);
          if ($request->type == '' || $request->type == null || $request->type == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
            goto returnFVI;
          }

          $toEmail = env('withDrawToEmail');
          $ccAddress = array_map('trim', explode(',', env('withDrawCC')));
          $toSubject = 'Withdraw Request Notification';


          $htmlTemplate = '<!DOCTYPE html
             PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
          <html xmlns="http://www.w3.org/1999/xhtml">
             <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title> Withdraw Request</title>
                <style type="text/css">
                   @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                   @import url("https://fonts.cdnfonts.com/css/verdana");
                   body {
                   margin: 0;
                   }
                   .wrapper {
                   background: #CCC;
                   }
                   .main {
                   background: #FFF;
                   max-width: 600px;
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
                   text-align: center;
                   margin: 0 auto;
                   }
                   .column-one .column {
                   width: 100%;
                   margin: 0 auto;
                   }
                   .im {
                   color: #01104e;
                   }
                   .column-one h3 {
                   color: #01104e;
                   font-family: Verdana, sans-serif !important;
                   font-size: 28px;
                   font-weight: 600;
                   margin: 14px 0 0 0;
                   }
                   .column-one p {
                   color: #01104e;
                   font-family: Verdana, sans-serif !important;
                   font-size: 19px;
                   font-weight: 500;
                   margin: 4px 0;
                   }
                </style>
             </head>
             <body>
                <center class="wrapper">
                   <table class="main" width="100%">
                      <!-- BORDER -->
                      <tr>
                         <td style="background-color: #171f4f; height: 45px;"></td>
                      </tr>
                      <tr>
                         <td class="column-one" style="background: #088b42;height:10px;">
                         </td>
                      </tr>
                      <!-- <tr>
                         <td style="background-color: #339a46; height: 45px;"></td>
                         </tr> -->
                      <tr>
                         <td class="column-one">
                            <table class="column">
                               <tr>
                                  <td valign="top" style="padding: 0;">
                                     <center>
                                        <br>
                                        <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                                           width="50%">
                                           <br>
                                     </center>
                                  </td>
                               </tr>

                            </table>
                         </td>
                      </tr>
                      <!-- LOGO  -->

                      <tr>

                          <td class="column-one c-f">
                            <p style="font-weight: 600!important; margin-top:18px;">Hi Team,</p>


                          </td>
                        </tr>
                      <tr>
                          <td valign="top" style="padding: 0;">
                             <center>
                                <br>
                                <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/cash-withdrawal1.png" style="border-radius: 19px;" width="30%">
                                <br>
                             </center>
                          </td>
                       </tr>
                      <tr>
                          <td class="column-one c-f">

                            <p style="font-size:21px;font-weight: 600!important;font-family: Verdana, sans-serif !important;margin: 3px auto;padding: 6px;border-radius: 8px;">
                              Withdrawal Request
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td>
                              <table style="margin: auto;border-collapse: collapse;border: 1px solid #088b42;width:90%;max-width:480px;" border="1" cellspacing="2" cellpadding="0">
                                  <tbody>
                                    <tr>
                                      <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width: 13%;background: #171f4f;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Name</strong></th>
                                      <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Amount</strong></th>
                                      <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Request id</strong></th>
                                    </tr>
                                    <tr>
                                      <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . ucwords(strtolower(auth()->user()->name . ' ' . (auth()->user()->lname ?? ''))) . '<br></strong></td>
                                      <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">AED ' . $amount . '</strong></td>
                                      <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . $request_id . '</strong></td>
                                  </tr>
                                  </tbody>
                                </table>
                          </td>
                        </tr>


                      <tr>
                         <td>
                          <br>
                            <ul
                               style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                               <li>• Thrill Draw win up to 24 Grams of Gold</li>
                               <li>• Booster Draw win up to 100 Grams of Gold </li>
                               <li>• Bumper Draw win up to 1000 Grams of  Gold</li>
                            </ul>
                         </td>
                      </tr>

                      <tr>
                          <td class="column-one">
                             <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                          </td>
                       </tr>
                      <tr>
                         <td>
                            <p
                               style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                               Note: This is a system auto-generated email. Please do not reply to this mail.
                            </p>
                         </td>
                      </tr>
                      <tr>
                      <td class="column-one" style="background: #171f4f; height:10px;">
                      </td>
                      </tr>
                   </table>
                   <!-- End Main Class -->
                </center>
                <!-- End Wrapper -->
             </body>
          </html>';

          // idProBack

          $idProFront = Controller::BlockSQLInjection(auth()->user()->idProFront);
          if ($idProFront == '' || $idProFront == null || $idProFront == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please upload the front image of your ID proof.', 'error' => 'Please upload the front image of your ID proof.'];
            goto returnFVI;
          }

          $idProBack = Controller::BlockSQLInjection(auth()->user()->idProBack);
          if ($idProBack == '' || $idProBack == null || $idProBack == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please upload the back image of your ID proof.', 'error' => 'Please upload the back image of your ID proof.'];
            goto returnFVI;
          }

          if ($request->type == 'BANK') {




            $request->accHolderName = Controller::BlockSQLInjection(auth()->user()->account_name);
            if ($request->accHolderName == '' || $request->accHolderName == null || $request->accHolderName == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid account holder name!', 'error' => 'Please use a valid account holder name!'];
              goto returnFVI;
            }

            $request->accNumber = Controller::BlockSQLInjection(auth()->user()->account_no);
            if ($request->accNumber == '' || $request->accNumber == null || $request->accNumber == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid account number!', 'error' => 'Please use a valid account number!'];
              goto returnFVI;
            }

            $request->accType = Controller::BlockSQLInjection(auth()->user()->acctype);
            if ($request->accType == '' || $request->accType == null || $request->accType == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid account type!', 'error' => 'Please use a valid account type!'];
              goto returnFVI;
            }

            $request->bankName = Controller::BlockSQLInjection(auth()->user()->bank_name);
            if ($request->bankName == '' || $request->bankName == null || $request->bankName == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid bank name!', 'error' => 'Please use a valid bank name!'];
              goto returnFVI;
            }

            $request->branchName = Controller::BlockSQLInjection(auth()->user()->branch_name);
            if ($request->branchName == '' || $request->branchName == null || $request->branchName == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid branch name!', 'error' => 'Please use a valid branch name!'];
              goto returnFVI;
            }

            if ($request->branchCode != '') {
              $request->branchCode = Controller::BlockSQLInjection(auth()->user()->branch_code);
              if ($request->branchCode == '' || $request->branchCode == null || $request->branchCode == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid branch code!', 'error' => 'Please use a valid branch code!'];
                goto returnFVI;
              }
            } else {
              $request->branchCode = '';
            }

            // $request->ibanNo = Controller::BlockSQLInjection(auth()->user()->IBAN_code);
            // if ($request->ibanNo == '' || $request->ibanNo == null || $request->ibanNo == 'null') {
            //   $response = ['status' => 'failed', 'message' => 'Please use a valid iban no!', 'error' => 'Please use a valid IBAN no!'];
            //   goto returnFVI;
            // }

            $request->dob = Controller::BlockSQLInjection(auth()->user()->dob);
            if ($request->dob == '' || $request->dob == null || $request->dob == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid date of birth!', 'error' => 'Please use a valid date of birth!'];
              goto returnFVI;
            }

            $request->currencyCode = Controller::BlockSQLInjection(auth()->user()->currency_code);
            if ($request->currencyCode == '' || $request->currencyCode == null || $request->currencyCode == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid currency code!', 'error' => 'Please use a valid currency code!'];
              goto returnFVI;
            }


            $request->passport = Controller::BlockSQLInjection(auth()->user()->passport);
            if ($request->passport == '' || $request->passport == null || $request->passport == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid passport!', 'error' => 'Please use a valid passport!'];
              goto returnFVI;
            }



            $request->swiftCode = Controller::BlockSQLInjection(auth()->user()->swift_code);
            if ($request->swiftCode == '' || $request->swiftCode == null || $request->swiftCode == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid swift code!', 'error' => 'Please use a valid swift code!'];
              goto returnFVI;
            }

            // dd($request->swiftCode);


            if (
              isset($request->type) && $request->type !== null &&
              isset($request->withdrawAmt) && $request->withdrawAmt !== null &&
              isset($request->accHolderName) && $request->accHolderName !== null &&
              isset($request->accNumber) && $request->accNumber !== null &&
              isset($request->accType) && $request->accType !== null &&
              isset($request->bankName) && $request->bankName !== null &&
              isset($request->branchName) && $request->branchName !== null &&
              isset($request->branchCode) && $request->branchCode !== null &&
              //   isset($request->ibanNo) && $request->ibanNo !== null &&
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
                "iban_code" => $request->ibanNo ?? "",
                "bank_name" => $request->bankName,
                "branch_name" => $request->branchName,
                "branch_code" => $request->branchCode,
                "emirites_passport" => $request->passport,
                "acc_no" => $request->accNumber,
                "nationality" => $nationlaity,
                "residinglocation" => $livin,
                "request_id" => $request_id,
                "requested_by" => $user_id,
                // "to_id" => '1',
                "amount" => $amount,
                "status" => '0',
                "deletes" => '0',
                "createdon" => now(),
                "transaction_id" => "",
                "exchangeid" => "",
                "idProFront" => $idProFront,
                "idProBack" => $idProBack,
                "openingBalance" => $my_t_earn,
                "closingBalance" => $fromclose
              ];

              $with_draw_ins = DB::table('withdraw_request')->insertGetId($withdraw_arr);


              if ($with_draw_ins != '') {

                // $dataArr['updated_at'] = now();
                // $dataArr['walletBalance'] = $fromclose;
                // $dataArr['dob'] = $dob;
                // $dataArr['bank_name'] = $request->bankName;
                // $dataArr['acctype'] = $request->accType;
                // $dataArr['account_no'] = $request->accNumber;
                // $dataArr['IBAN_code'] = $request->ibanNo;
                // $dataArr['swift_code'] = $request->swiftCode;
                // $dataArr['account_name'] = $request->accHolderName;
                // $dataArr['branch_name']  =  $request->branchName;
                // $dataArr['branch_code'] =  $request->branchCode;
                // $dataArr['currency_code'] = $request->currencyCode;
                // $dataArr['passport'] = $request->passport;



                // $result_arr = array_intersect_key(auth()->user()->toArray(), $dataArr);

                // $log_arr = array_diff($result_arr, $dataArr);



                // $user_profile_log_arr['user_id'] = $user_id;

                // $user_profile_log_arr['changed_by'] = $user_id;

                // $user_profile_log_arr['changed_data'] = json_encode($log_arr);

                // $user_profile_log_arr['updated_datetime'] = now();

                // $user_profile_log_arr['ip'] = $request->ip();

                // $user_profile_log_ins =  DB::table('user_profile_activity_log')->insert($user_profile_log_arr);


                $payArr = [
                  "userid" => $user_id,
                  "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                  "umobile" => auth()->user()->mobile,
                  "uemail" => auth()->user()->email,
                  'opening_balance' => $my_t_earn,
                  'total' => $amount,
                  'closeing_balance' => $fromclose,
                  'point_type' => 'WALLET',
                  'transaction_type' => 'DEBIT',
                  'card_no' => '',
                  'reference_id' => $with_draw_ins,
                  'reference_table' => 'withdraw_request',
                  'ip' => ($request->ip() ?? ''),
                  'reward_type' => 'BANKWITHDRAWAL'
                ];

                $wallet_history = DB::table('wallet_history')->insert($payArr);

                if ($wallet_history) {
                  $updateUser = DB::table('user_register')
                    ->where('id', $user_id)
                    ->where('deletes', '0')
                    ->update([
                      'updated_at' => now(),
                      'walletBalance' => $fromclose,
                      'lastlogin' => now()
                      // 'dob' => $dob,
                      // 'bank_name' => $request->bankName,
                      // 'acctype' => $request->accType,
                      // 'account_no' => $request->accNumber,
                      // 'IBAN_code' => $request->ibanNo,
                      // 'swift_code' => $request->swiftCode,
                      // 'account_name' => $request->accHolderName,
                      // 'branch_name' => $request->branchName,
                      // 'branch_code' => $request->branchCode,
                      // 'currency_code' => $request->currencyCode,
                      // 'passport' => $request->passport
                    ]);

                  if ($updateUser) {

                    // $withdrawData = [
                    //   "userid" => $user_id,
                    //   "type" => 'withdraw',
                    //   "request" => json_encode($request->all()),
                    //   "status" => '0',
                    //   "deletes" => '0',
                    //   "createdon" => now()
                    // ];

                    // $log = DB::table('bank_change_log')->insert($withdrawData);

                    // Send Mail
                    $emailSend = Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', $ccAddress);

                    $response = ['status' => 'success', 'message' => "Request has been sent", 'data' => "Request has been sent"];
                    goto returnFVI;
                    // }
                  } else {
                    $response = ['status' => 'failed', 'message' => 'User update process failed!', 'error' => 'User update process failed!'];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
                  goto returnFVI;
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


            $request->exchangeID = Controller::BlockSQLInjection(auth()->user()->exchangeid);
            if ($request->exchangeID == '' || $request->exchangeID == null || $request->exchangeID == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid exchange ID!', 'error' => 'Please use a valid exchange ID!'];
              goto returnFVI;
            }

            $request->dob = Controller::BlockSQLInjection(auth()->user()->dob);
            if ($request->dob == '' || $request->dob == null || $request->dob == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid date of birth!', 'error' => 'Please use a valid date of birth!'];
              goto returnFVI;
            }

            $request->passport = Controller::BlockSQLInjection(auth()->user()->passport);
            if ($request->passport == '' || $request->passport == null || $request->passport == 'null') {
              $response = ['status' => 'failed', 'message' => 'Please use a valid passport!', 'error' => 'Please use a valid passport!'];
              goto returnFVI;
            }


            // dd($request->passport);

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
                "requested_by" => $user_id,
                // "to_id" => 1,
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
                "emirites_passport" => $request->passport,
                "idProFront" => $idProFront,
                "idProBack" => $idProBack,
                "openingBalance" => $my_t_earn,
                "closingBalance" => $fromclose
              ];

              $with_draw_ins = DB::table('withdraw_request')->insertGetId($withdrawData);


              if ($with_draw_ins != '') {


                // $exchangeDataArr['updated_at'] = now();

                // $exchangeDataArr['walletBalance'] = $fromclose;

                // $exchangeDataArr['dob'] = $dob;

                // $exchangeDataArr['passport'] = $request->passport;

                // $exchangeDataArr['exchangeid'] = $request->exchangeID;



                // $result_arr = array_intersect_key(auth()->user()->toArray(), $exchangeDataArr);

                // $log_arr = array_diff($result_arr, $exchangeDataArr);



                // $user_profile_log_arr['user_id'] = $user_id;

                // $user_profile_log_arr['changed_by'] = $user_id;

                // $user_profile_log_arr['changed_data'] = json_encode($log_arr);

                // $user_profile_log_arr['updated_datetime'] = now();

                // $user_profile_log_arr['ip'] = $request->ip();



                // $user_profile_log_ins =  DB::table('user_profile_activity_log')->insert($user_profile_log_arr);
                $payArr = [
                  "userid" => $user_id,
                  "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                  "umobile" => auth()->user()->mobile,
                  "uemail" => auth()->user()->email,
                  'opening_balance' => $my_t_earn,
                  'total' => $amount,
                  'closeing_balance' => $fromclose,
                  'point_type' => 'WALLET',
                  'transaction_type' => 'DEBIT',
                  'card_no' => '',
                  'reference_id' => $with_draw_ins,
                  'reference_table' => 'withdraw_request',
                  'ip' => ($request->ip() ?? ''),
                  'reward_type' => 'EXCHANGEWITHDRAWAL'
                ];

                $wallet_history = DB::table('wallet_history')->insert($payArr);

                if ($wallet_history) {
                  $updateUser = DB::table('user_register')
                    ->where('id', $user_id)
                    ->where('deletes', '0')
                    ->update([
                      'updated_at' => now(),
                      'walletBalance' => $fromclose,
                      'lastlogin' => now()
                      // 'dob' => $dob,
                      // 'passport' => $request->passport,
                      // 'exchangeid' => $request->exchangeID
                    ]);

                  if ($updateUser) {
                    // $withdrawData = [
                    //   "userid" => $user_id,
                    //   "type" => 'withdraw',
                    //   "request" => json_encode($request->all()),
                    //   "status" => '0',
                    //   "deletes" => '0',
                    //   "createdon" => now()
                    // ];

                    // $log = DB::table('bank_change_log')->insert($withdrawData);

                    // Send Mail
                    $emailSend = Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', $ccAddress);

                    $response = ['status' => 'success', 'message' => "Request has been sent", 'data' => "Request has been sent"];
                    goto returnFVI;
                  } else {
                    $response = ['status' => 'failed', 'message' => 'User update process failed!', 'error' => 'User update process failed!'];
                    goto returnFVI;
                  }
                } else {
                  $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
                  goto returnFVI;
                }
              } else {
                $response = ['status' => 'failed', 'message' => 'Withdrawal request failed!', 'error' => 'Withdrawal request failed!'];
                goto returnFVI;
              }
            } else {
              $response = ['status' => 'failed', 'message' => 'Please Fill All Fields!', 'error' => 'Please Fill All Fields!'];
              goto returnFVI;
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'The type not found!', 'error' => 'The type not found!'];
            goto returnFVI;
          }
          // } else {
          //   if (count($img_arr) > 0 && $with_draw_ins != '') {
          //     $response = ['status' => 'failed', 'message' => "File upload Field! kindly try again!", 'error' => "File upload Field! kindly try again!"];
          //     goto returnFVI;
          //     // $result['type'] = '0';
          //     // $result['result'] = 'File upload Field! kindly try again!';
          //   }
          // }
        } else {

          $response = ['status' => 'failed', 'message' => 'Maximum Withdrawal amount is AED ' . floatval($my_t_earn) . '.', 'error' => 'Maximum Withdrawal amount is AED ' . floatval($my_t_earn) . '.'];
          goto returnFVI;
        }
      } else {
        // dd('Test');
        $response = ['status' => 'failed', 'message' => 'Minimum Withdrawal amount is AED 3.00', 'error' => 'Minimum Withdrawal amount is AED 3.00'];
        goto returnFVI;
      }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function getInvoiceList(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }

      $ticketReferenceNo = $request->ticketReferenceNo;
      if ($ticketReferenceNo == '' || $ticketReferenceNo == null || $ticketReferenceNo == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid Ticket Reference No.', 'error' => 'Please provide a valid Ticket Reference No.'];
        goto returnFVI;
      }



      $ndticket = DB::table('ndticket')
        // ->whereRaw("JSON_CONTAINS(transactionIds, '\"$transaction_id\"', '$')")
        // ->whereRaw("JSON_CONTAINS(paymentHistoryIds, '{$payment_history[0]->id}', '$')")
        ->where([
          ['referenceID', 'LIKE', $ticketReferenceNo],
          // ['validityPeriod', '<', 360],
          // ['netTotal', '<', 360],
          ['deletes', '=', '0'],
          ['userId', '=', $user_id]
        ])
        ->orderBy('createdon', 'DESC')
        ->limit(1)
        ->get();

      // dd($ndticket);
      if ($ndticket->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'No ticket has been purchased by this customer', 'data' => 'No ticket has been purchased by this customer'];
        goto returnFVI;
      }

      $ndticket[0]->transactionIds = json_decode($ndticket[0]->transactionIds);
      $ndticket[0]->raffleIds = json_decode($ndticket[0]->raffleIds);
      $ndticket[0]->invoiceNo = json_decode($ndticket[0]->invoiceNo);

      $invoices = DB::table('invoice')
        ->where('ticketReferenceID', 'LIKE', $ndticket[0]->referenceID)
        ->whereIn('renewalStatus', ['NEW', 'RENEWAL'])
        ->where('user_id', $user_id)
        ->where('parentInvoiceID', '0')
        ->where('deletes', '0')
        ->orderByDesc('id')
        ->get();

      // dd($invoices);

      $invoices = collect($invoices)->map(function ($item) {

        $item->cart = json_decode($item->cart, true);




        // Calculate the difference in hours between now and $createdon
        $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours', strtotime($item->createdon)));

        $item->pickUpStartTo = date('Y-m-d H:i:s', strtotime('+15 days', strtotime($item->createdon)));

        // dd($item);

        // Check if the difference is less than 48 hours
        $item->eligiblePickUp = (Carbon::now()->lt($expiresAt) && $item->delivery_status === null ? true : false);

        $item->shippingInvoice = null;


        $childInvoices = DB::table('invoice')
          ->where('ticketReferenceID', 'LIKE', $item->ticketReferenceID)
          ->whereIn('renewalStatus', ['PICKUPTOSTORE', 'DELIVERYTOCUSTOMER'])
          ->where('user_id', $item->user_id)
          ->where('parentInvoiceID', $item->id)
          ->where('shipping_requestID', '<>', 0)
          ->where('deletes', '0')
          ->orderByDesc('id')
          ->limit(1)
          ->get();

        $statusDescriptions = [
          'requested' => 'Requested',
          'confirmed' => 'Confirmed',
          'out_for_delivery' => 'Out for Delivery',
          'delivered' => 'Delivered',
          'cancelled' => 'Cancelled',
          'collected' => 'Collected'
        ];

        // dd($childInvoices);


        if ($childInvoices->count() > 0) {
          $childInvoices = collect($childInvoices)->map(function ($item) use ($statusDescriptions) {

            $item->cart = json_decode($item->cart, true);


            $item->delivery_status = $statusDescriptions[$item->delivery_status];


            return $item;
          })->all();

          $item->shippingInvoice = $childInvoices[0];
        }

        // Verify the result
        // dd(Carbon::now()->lt($expiresAt), $expiresAt, $item->createdon );




        return $item;
      })->all();

      // $ndticket = collect($ndticket)->map(function ($item) {
      //   $item->transactionIds =  json_decode($item->transactionIds, true);
      //   $item->raffleIds = json_decode($item->raffleIds, true);
      //   $item->paymentHistoryIds = json_decode($item->paymentHistoryIds, true);
      //   $item->invoiceNo = json_decode($item->invoiceNo, true);

      //   // $item->name = $item->rate . ' ' . $item->name;
      //   $subscribtion = [];
      //   if (intval($item->netTotal) < 360) {

      //     $planRequest = new Request([
      //       "referenceID" => $item->referenceID
      //     ]);

      //     $planRes = $this->getSuggestion($planRequest);
      //     $planData = json_decode($planRes->getContent(), true);

      //     // dd($planData['status']);

      //     $subscribtion = ($planData['status'] === 'success') ?  $planData['data'] : [];
      //   }
      //   $item->subscribtion = $subscribtion;

      //   return $item;
      // })->all();


      // dd($ndticket);

      $response = [
        'status' => 'success',
        'message' => 'The tickets purchased by this customer have been successfully collected.',
        'data' => ['invoiceList' => $invoices, 'ticketDetails' => $ndticket[0]]
      ];

      goto returnFVI;


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function requestGold(Request $request)
  {
    try {

      $response = [];
      $data = [];

      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
        goto returnFVI;
      }




      $winningID = $request->winningID;
      if ($winningID == '' || $winningID == null || $winningID == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid Winning No.', 'error' => 'Please provide a valid Winning No.'];
        goto returnFVI;
      }
      // $winningID = $winningID[0];


      $winnerlist = DB::table('winnerlist')
        ->where([
          ['id', '=', $winningID],
          ['deletes', '=', '0'],
          ['userid', '=', $user_id],
          ['requestType', '=', null] // Added comma here
        ])
        ->orderBy('createdon', 'DESC')
        ->limit(1)
        ->get();



      // dd($ndticket);
      if ($winnerlist->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'No winning track found!', 'data' => 'No winning track found!'];
        goto returnFVI;
      }

      $winnerlist = $winnerlist[0];

      $gold_requestRecheck = DB::table('gold_request')
        ->where('winnerlistId', $winningID)
        ->where('deletes', '0')
        ->where('delivery_status', '!=', 'cancelled')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get();

      if ($gold_requestRecheck->count() > 0) {
        $response = ['status' => 'failed', 'message' => 'Already requested!', 'data' => 'Already requested!'];
        goto returnFVI;
      }


      $reqInsert = DB::table('gold_request')->insert([
        'winnerlistId' => $winnerlist->id,
        'grams' => $winnerlist->prize,
        'ticketReferenceID' => $winnerlist->ticketReferenceID,
        'userID' => $winnerlist->userid,
        'goldValue' => $winnerlist->prize_amt,
        'createdon' => now(),
        'updatedon' => now(),
        'deletes' => '0',
        'delivery_status' => 'requested'
      ]);


      if ($reqInsert) {

        $updateWinner = DB::table('winnerlist')
          ->where('id', $winnerlist->id)
          ->update(['requestType' => 'GOLD', 'requestTime' => now()]);
        if ($reqInsert) {




          $toEmail = env('shippingToEmail');
          $ccAddress = array_map('trim', explode(',', env('shippingCC')));
          $toSubject = 'Gold Request Notification';


          $htmlTemplate = '<!DOCTYPE html
          PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
          <html xmlns="http://www.w3.org/1999/xhtml">
       <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title> Gold Request</title>
      <style type="text/css">
         @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
         @import url("https://fonts.cdnfonts.com/css/verdana");
         body {
         margin: 0;
         }
         .wrapper {
         background: #CCC;
         }
         .main {
         background: #FFF;
         max-width: 600px;
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
         text-align: center;
         margin: 0 auto;
         }
         .column-one .column {
         width: 100%;
         margin: 0 auto;
         }
         .im {
         color: #01104e;
         }
         .column-one h3 {
         color: #01104e;
         font-family: Verdana, sans-serif !important;
         font-size: 28px;
         font-weight: 600;
         margin: 14px 0 0 0;
         }
         .column-one p {
         color: #01104e;
         font-family: Verdana, sans-serif !important;
         font-size: 19px;
         font-weight: 500;
         margin: 4px 0;
         }
      </style>
        </head>
         <body>
      <center class="wrapper">
         <table class="main" width="100%">
            <!-- BORDER -->
            <tr>
               <td style="background-color: #171f4f; height: 45px;"></td>
            </tr>
            <tr>
               <td class="column-one" style="background: #088b42;height:10px;">
               </td>
            </tr>
            <!-- <tr>
               <td style="background-color: #339a46; height: 45px;"></td>
               </tr> -->
            <tr>
               <td class="column-one">
                  <table class="column">
                     <tr>
                        <td valign="top" style="padding: 0;">
                           <center>
                              <br>
                              <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                                 width="50%">
                                 <br>
                           </center>
                        </td>
                     </tr>

                  </table>
               </td>
            </tr>
            <!-- LOGO  -->

            <tr>

                <td class="column-one c-f">
                  <p style="font-weight: 600!important; margin-top:18px;">Hello Team</p>


                </td>
              </tr>
            <tr>
                <td valign="top" style="padding: 0;">
                   <center>
                      <br>
                      <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/proGold.png" style="border-radius: 19px;" width="20%">
                      <br>
                   </center>
                </td>
             </tr>
            <tr>
                <td class="column-one c-f">

                  <p style="font-size:21px;font-weight: 600!important;font-family: Verdana, sans-serif !important;margin: 3px auto;padding: 6px;border-radius: 8px;">
                    Gold Request
                  </p>
                </td>
              </tr>
              <tr>
                <td>
                    <table style="margin: auto;border-collapse: collapse;border: 1px solid #088b42;width:90%;max-width:480px;" border="1" cellspacing="2" cellpadding="0">
                        <tbody>
                          <tr>

                            <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width: 13%;background: #171f4f;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Name</strong></th>
                            <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Raffle Id</strong></th>
                            <th style="padding: 12px 0px;color: #ffffff;font-family: Verdana, sans-serif !important;font-size:17px;width:12%;background: #01104e;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 500;">Winning Gold</strong></th>

                          </tr>
                          <tr>
                            <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . $winnerlist->fullName . '<br>
                               </strong></td>
                            <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . $winnerlist->winRaffleId . '</strong></td>
                            <td style=" padding: 12px 0px;color: #01104e;font-family: Verdana, sans-serif !important;font-size:17px;" align="center" bgcolor="#ffffff"><strong style="font-weight: 500;">' . $winnerlist->prize . ' GRAMS </strong></td>

                        </tr>
                        </tbody>
                      </table>
                </td>
              </tr>


            <tr>
               <td>
                <br>
                  <ul
                     style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                     <li>• Thrill Draw win up to 24 Grams of Gold</li>
                     <li>• Booster Draw win up to 100 Grams of Gold </li>
                     <li>• Bumper Draw win up to 1000 Grams of  Gold</li>
                  </ul>
               </td>
            </tr>

            <tr>
                <td class="column-one">
                   <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                </td>
             </tr>
            <tr>
               <td>
                  <p
                     style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                     Note: This is a system auto-generated email. Please do not reply to this mail.
                  </p>
               </td>
            </tr>
            <tr>
            <td class="column-one" style="background: #171f4f; height:10px;">
            </td>
            </tr>
         </table>
         <!-- End Main Class -->
      </center>
      <!-- End Wrapper -->
        </body>
        </html>';

          // Send Mail
          $emailSend = Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', $ccAddress);



          $response = [
            'status' => 'success',
            'message' => 'Your gold request has been successfully submitted. Thank you!',
            'data' => 'Your gold request has been successfully submitted. Thank you!'
          ];

          goto returnFVI;
        } else {
          $response = [
            'status' => 'failed',
            'message' => 'Your gold request process faild!',
            'data' => 'Your gold request process faild!'
          ];
          goto returnFVI;
        }
      } else {
        $response = [
          'status' => 'failed',
          'message' => 'Your gold request process faild!',
          'data' => 'Your gold request process faild!'
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
  
  public function changeSubscription(Request $request){
      
        if(isset($request->subscription_id)){
            $get_sub = DB::table('subscriptions')->where('id', $request->subscription_id)->first();
            $get_inv = DB::table('invoice')->where('subscription_id', $request->subscription_id)->first();
            
            if($get_sub){
                $old_checkout = json_decode($get_sub->checkout_response);
                
                $subID = $get_sub->subID;
                
                $old_checkout->purchaseDate = date('Y-m-d H:i:s');
                $old_checkout->startDate = date('Y-m-d');
                
                
                $validityDays = $old_checkout->planType === 'YEARLY' ? Controller::getDaysInMonthOrYear('year', $old_checkout->quantity) : Controller::getDaysInMonthOrYear('month', $old_checkout->quantity);
                
                $old_checkout->noOfDays =  $validityDays;
                
                $old_checkout->timestamp =  now();


                $old_checkout->totalDays = ($old_checkout->trailsDays * $old_checkout->quantity) + $old_checkout->noOfDays;
                
                $old_checkout->expiryDate = Carbon::parse($old_checkout->startDate)->addDays($old_checkout->totalDays)->format('Y-m-d');
                
                $trid = DB::table(((isset($old_checkout->subscriptions) && $old_checkout->subscriptions) ? 'subscriptions' : 'payment_history'))->select("id")->orderby('id', 'desc')->limit(1)->first();
                $inTrans = (isset($trid) && $trid->id != null && $trid->id != '') ? $trid->id : 0;
                
                $tran_id = ((isset($old_checkout->subscriptions) && $old_checkout->subscriptions) ? 'S' : '') . 'GR' . uniqid(8) . date('Hi') . ($inTrans + 1);
                
                // Convert stdClass to array
                $data = (array) $get_sub;
                $data_inv = (array) $get_inv;
            
                // Remove ID to avoid duplicate key error
                unset($data['id']);
                unset($data_inv['id']);
            
                // Insert and get the new ID
                $newId = DB::table('subscriptions')->insertGetId($data);
                $newId_inv = DB::table('invoice')->insertGetId($data_inv);
            
                if ($newId) {
                    
                    $arr_update = [
                        'subscription_id' => $tran_id,
                        'subID' => null,
                        'gateway' => null,
                        'paymentStatus' => null,
                        'status' => '0',
                        'cron_check_status' => '0',
                        'sub_status' => '0',
                        'cycles_paid' => '0',
                        'plan_id' => null,
                        'subReq' => null,
                        'subRes' => null,
                        'updatedon' => now(),
                        'crontime' => now(),
                        'createdon' => now(),
                        'subslogID' => 0,
                        'invoiceID' => 0,
                        'start_date' => $old_checkout->startDate,
                        'expiryDate' => $old_checkout->expiryDate,
                        'checkout_response' => json_encode($old_checkout),
                        'invoiceIDs' => null
                    ];
                    
                    DB::table('subscriptions')->where('id', $newId)->update($arr_update);
                    DB::table('crm')->where('subscription_id', $get_sub->id)->update(['subscription_id' => $newId, 'manual_sub_access' => '0']);
                    
                    $get_new_inv = DB::table('invoice')->where('id', $newId_inv)->first();
                    
                    if($get_new_inv){
                        $new_inv_cart = json_decode($get_new_inv->cart);
                        
                        $new_inv_cart->transaction_id = $tran_id;
                    }
                    
                    
                    $new_request = new Request([
                        'transaction_id' => $tran_id,
                    ]);
                    
                    // Call the method
                    $CCAvenueGatewaycontroller = new CCAvenueGateway();
                    $create_subscription = $CCAvenueGatewaycontroller->razorpaySubInitiate($new_request);
                    
                    
                    
                    $dataOnly = json_decode($create_subscription->getContent(), true);
                    $razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
                    $getSubscription = $razorpay->subscription->fetch($subID);

                    // dd($getSubscription);
                    if (isset($getSubscription) && isset($getSubscription->id) && $getSubscription->status != 'cancelled') {
                        
                        $cancelSubscription = $razorpay->subscription->fetch($subID)->cancel([
                            "cancel_at_cycle_end" => 0
                        ]);
                        
                        if($cancelSubscription){
                            DB::table('subscriptions')
                                ->where('id', $request->subscription_id)
                                ->update([
                                    'paymentStatus' => 'cancelled',
                                    'sub_status' => 'cancelled'
                                ]);

                            $new_s_ID = DB::table('subscriptions')->where('id', $newId)->first();
                            
                            $update_new_inv = [
                                'subscription_id' => $newId,
                                'payment_transaction_id' => $tran_id,
                                'createdon' => now(),
                                'subscrID' => $new_s_ID->subID,
                                'sub_status' => 'created',
                                'cart' => json_encode($new_inv_cart)
                            ];
                            
                            DB::table('invoice')->where('id', $newId_inv)->update($update_new_inv);
                            DB::table('invoice')->where('subscription_id', $request->subscription_id)->update(['sub_status' => 'cancelled']);
                        }
                      
                    }
                    
                    
                }
                
                $response = [
                  'status' => 'success',
                  'message' => 'Your New Subscription Initiated Successfully. Proceed to Payment.',
                  'data' => $dataOnly
                ];
                
            }
        }
      
      return response()->json($response);
  }
}
