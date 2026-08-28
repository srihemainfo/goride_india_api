<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Crypt;


class PlayController extends Controller
{


  public function generatePlanData($plan, $priceKey, $validityKey, $bookingsKey)
  {
      
      
 $validityDays =  ($validityKey === 'yearlyDays' ? Controller::getDaysInMonthOrYear('year', $plan->$validityKey)  :  Controller::getDaysInMonthOrYear('month', $plan->$validityKey));
      
     
    return [
      "id" => $plan->id,
      "name" => $plan->name,
      "no_of_bookings" => $plan->$bookingsKey,
      "no_of_Vehicle" => $plan->no_of_Vehicle,
      "image" => $plan->image,
      "trailsDays" => ($validityKey === 'renewalDays') ? 0 : $plan->trailsDays,
      "no_of_website" => $plan->no_of_website,
      "setupFees" => $plan->setupFees,
      "qty" => $plan->qty,
      "currency" => $plan->currency,
      "price" => $plan->$priceKey,
      "validityMorY" => $plan->$validityKey,
      "validityDays" =>  $validityDays ,
      "productType" => $plan->productType,
      "description" => $plan->description,
      "standardFeatures" => $plan->standardFeatures,
      "perDay" => ($plan->$priceKey > 0) ? number_format(($plan->$priceKey / $validityDays), 2) : 0
    ];
  }

  public function sortPlansByPrice($plans, $priceKey)
  {
    return $plans->sortBy($priceKey)->values();
  }


  public function planList(Request $request)
  {

    try {

      $token = $request->bearerToken();
      $tokenRecord = PersonalAccessToken::findToken($token);
      $user_id = null;

      if ($tokenRecord) {
        $user = $tokenRecord->tokenable; 
        if(!isset($user) || $user == null || $user == ''){
              unset($_COOKIE['sessionToken']);
        }
        // dd($user);
        $user_id = $user->id ?? null;
      }

      // $user_id = auth()->user()->id ?? '' ; 

      // dd($user_id);

      $planWhere = [
        ['deletes', '=', '0'],

      ];

      if (!isset($request->productID)) {
        $planWhere[] = [
          'currency',
          '=',
          ((isset($request->countryCode) && $request->countryCode !== null && $request->countryCode !== '' && $request->countryCode === 'IN')
            ? 'INR'
            : 'USD')
        ];
      }

      // dd($planWhere);

      $plans = DB::table('plans')

        ->where($planWhere)
        ->orderBy('id', 'desc')
        ->get();


      if ($plans->count() < 1) {
        $response = [
          'status' => 'failed',
          'message' => 'No plans found!',
          'error' => 'Please refresh and try again.'
        ];
        goto returnFVI;
      }

      $data = [
        'monthly' => null,
        'yearly' => null,
        'renewal' => null,
      ];


      $plans->each(function ($plan) use (&$data, $user_id) {

        $checkTrail = DB::table('invoice')
          ->where('user_id', $user_id)
          ->where('deletes', '0')
          // ->where('planType', 'TRAIL')
          ->orderBy('id', 'DESC')
          ->get();

        // if ($checkTrail->count() < 1 && $user_id == null) {
        //   // $planType = $product->productType;
        // }

        if (($plan->productType === 'PLAN') || ($plan->productType === 'TRAIL' && $user_id == null) || (($checkTrail->count() < 1 && $user_id != null && $plan->productType === 'TRAIL'))) {
          $data['monthly'][] = $this->generatePlanData($plan, 'monthlyPrice', 'monthlyDays', 'monthlyNoOfBookings');
        }

        if ($plan->productType === 'PLAN') {
          $data['yearly'][] = $this->generatePlanData($plan, 'yearlyPrice', 'yearlyDays', 'yearlyNoOfBookings');
          // }

          // if ($plan->productType === 'PLAN') {

          $planD = $this->generatePlanData($plan, 'renewalPrice', 'renewalDays', 'renewalNoOfBookings');
          // dd($planD);
          if (intval($planD['price']) > 0) {
            $data['renewal'][] = $planD;
          }
        }
        
        
      });


      $data['monthly'] = $this->sortPlansByPrice(collect($data['monthly']), 'price');
      $data['yearly'] = $this->sortPlansByPrice(collect($data['yearly']), 'price');
      $data['renewal'] = $this->sortPlansByPrice(collect($data['renewal']), 'price');



      $response = ['status' => 'success', 'message' => 'The plan details have been collected successfully', 'data' => ['planList' => $data]];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function dashboard(Request $request)
  {

    try {


      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $data['userDetails'] = [
        'userID' => auth()->user()->id,
        // 'user' => auth()->user()->user,
        // 'pass' => auth()->user()->pass,
        // 'password' => auth()->user()->password,
        // 'roll_id' => auth()->user()->roll_id,
        // 'users_refid' => auth()->user()->users_refid,
        // 'created_by' => auth()->user()->created_by,
        // 'agentIds' => auth()->user()->agentIds,
        'name' => auth()->user()->name,
        'lname' => auth()->user()->lname,
        'gender' => auth()->user()->gender,
        'dialCode' => auth()->user()->dialCode,
        'mobile' => auth()->user()->mobile,
        'mobile_verify' => auth()->user()->mobile_verify,
        'email' => auth()->user()->email,
        'email_verify' => auth()->user()->email_verify,
        'dob' => auth()->user()->dob,
        'passport' => auth()->user()->passport,
        'passport_expiry' => auth()->user()->passport_expiry,
        // 'img_url' => auth()->user()->img_url,
        // 'img_url' => auth()->user()->img_url != 'NULL' && auth()->user()->img_url != '' ? env('DO_REDIRECT_URL') . auth()->user()->img_url : '',
        // 'profile_img_url' => auth()->user()->profile_img_url,
        // 'profile_img_name' => auth()->user()->profile_img_name,
        // 'profile_img_verfiy' => auth()->user()->profile_img_verfiy,
        // 'profile_img_reject_id' => auth()->user()->profile_img_reject_id,
        // 'deletes' => auth()->user()->deletes,
        // 'status' => auth()->user()->status,
        // 'otp' => auth()->user()->otp,
        // 'created_at' => auth()->user()->created_at,
        // 't_point' => auth()->user()->t_point,
        // 'f_points' => auth()->user()->f_points,
        // 'walletBalance' => auth()->user()->walletBalance,
        // 'cash_points' => auth()->user()->cash_points,
        // 'bonus_points' => auth()->user()->bonus_points,
        // 'account_name' => auth()->user()->account_name,
        // 'acctype' => auth()->user()->acctype,
        // 'account_no' => auth()->user()->account_no,
        // 'bank_name' => auth()->user()->bank_name,
        // 'bank_address' => auth()->user()->bank_address,
        // 'branch_code' => auth()->user()->branch_code,
        // 'branch_name' => auth()->user()->branch_name,
        // 'swift_code' => auth()->user()->swift_code,
        // 'IBAN_code' => auth()->user()->IBAN_code,
        // 'currency_code' => auth()->user()->currency_code,
        // 'my_referral_code' => auth()->user()->my_referral_code,
        // 'building_name' => auth()->user()->building_name,
        // 'city' => auth()->user()->city,
        // 'address' => auth()->user()->address,
        // 'nationality' => auth()->user()->nationality,
        // 'residinglocation' => auth()->user()->residinglocation,
        // 'updated_at' => auth()->user()->updated_at,
        // 'ip' => auth()->user()->ip,
        // 'delete_req' => auth()->user()->delete_req,
        // 'delete_request' => auth()->user()->delete_request,
        // 'ticket_purchased' => auth()->user()->ticket_purchased,
        // 'ticket_count' => auth()->user()->ticket_count,
        // 'lastlogin' => auth()->user()->lastlogin,
        // 'deviceType' => auth()->user()->deviceType,
        // 'exchangeid' => auth()->user()->exchangeid,
        // 'api_token' => auth()->user()->api_token,
        // 'idProFront' => auth()->user()->idProFront != 'NULL' && auth()->user()->idProFront != '' ? env('DO_REDIRECT_URL') . auth()->user()->idProFront : '',
        // 'idProBack' => auth()->user()->idProBack != 'NULL' && auth()->user()->idProBack != '' ? env('DO_REDIRECT_URL') . auth()->user()->idProBack : ''

      ];



      $crmList = DB::table('crm as c')
      ->leftJoin('subscriptions as s', 's.id', '=', 'c.subscription_id')
    ->select('c.*', 's.expiryDate', 's.checkout_response',  's.subscription_id as subscrID', 's.sub_status as subStatus')
        ->where('c.userID', auth()->user()->id)
        ->where('c.deletes', '0')
        ->orderBy('c.id', 'desc')
        ->get()
        ->map(function ($item) {

          $item->transactionID = json_decode($item->transactionID, true);
          $item->invoiceID = json_decode($item->invoiceID, true);
          $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);

 $item->checkout_response = json_decode($item->checkout_response, true);
 
             $item->isActive =  Carbon::parse( $item->expiryDate)->isAfter(Carbon::now());
 

          $item->crmReq = isset($item->crmReq) ? json_decode($item->crmReq, true) : $item->crmReq;

          $item->crmRes = isset($item->crmRes) ? json_decode($item->crmRes, true) : $item->crmRes;


          if (isset($item->crmRes) && isset($item->crmRes['data']['domain_name'])) {
            $item->crmReq['fullDomain'] = $item->crmRes['data']['domain_name'];
          }

          return $item;
        });

      $data['userDetails']['crmList'] = null;


      if ($crmList->count() > 0) {
        $data['userDetails']['crmList'] = $crmList;
      }



      // $result = DB::table('ndticket')
      //   ->selectRaw('COUNT(`id`) AS totalTickets, SUM(`totalRaffle`) AS totalRaffle')
      //   ->where('userId', $user_id)
      //   ->where('deletes', '0')
      //   ->first();

      // $data['ticketData']['totalTickets'] = $result->totalTickets ?? 0;
      // $data['ticketData']['totalRaffle'] = $result->totalRaffle ?? 0;

      // $data['userDetails']['shipingAddres']  = null;
      // $checkAddressBook =  DB::table('address_book')
      //   ->where('user_id', $user_id)
      //   ->where('deletes', '0')
      //   ->select('id AS addressBookID', 'name', 'dialCode', 'mobile', 'email', 'doorno', 'street', 'city', 'state', 'country', 'landmark', 'postal_code')
      //   ->orderBy('id', 'DESC')
      //   ->limit(1)
      //   ->get();


      // if ($checkAddressBook->count() > 0) {
      //   $data['userDetails']['shipingAddres'] = $checkAddressBook[0];
      // }

      // $data['userDetails']['balanceWinningGold'] = 0;
      // $totalGold = DB::table('winnerlist')
      //   ->select(DB::raw('COALESCE(SUM(prize), 0) AS totalGold'))
      //   ->where('userid', $user_id)
      //   ->where('deletes', '0')
      //   ->whereNull('requestType')
      //   ->first()->totalGold;

      // if ($totalGold) {
      //   $data['userDetails']['balanceWinningGold'] = $totalGold;
      // }


      // $data['userDetails']['bankDetails'] = false;
      // $data['userDetails']['exchangeDetails'] = false;

      // if (
      //   // isset(auth()->user()->type) && auth()->user()->type !== null &&
      //   // isset(auth()->user()->withdrawAmt) && auth()->user()->withdrawAmt !== null &&
      //   isset(auth()->user()->account_name) && auth()->user()->account_name !== null &&
      //   isset(auth()->user()->account_no) && auth()->user()->account_no !== null &&
      //   isset(auth()->user()->acctype) && auth()->user()->acctype !== null &&
      //   isset(auth()->user()->bank_name) && auth()->user()->bank_name !== null &&
      //   isset(auth()->user()->branch_name) && auth()->user()->branch_name !== null &&
      //   // isset(auth()->user()->branch_code) && auth()->user()->branch_code !== null &&
      //   // isset(auth()->user()->IBAN_code) && auth()->user()->IBAN_code !== null &&
      //   isset(auth()->user()->dob) && auth()->user()->dob !== null &&
      //   isset(auth()->user()->currency_code) && auth()->user()->currency_code !== null &&
      //   isset(auth()->user()->passport) && auth()->user()->passport !== null &&
      //   isset(auth()->user()->nationality) && auth()->user()->nationality !== null &&
      //   isset(auth()->user()->residinglocation) && auth()->user()->residinglocation !== null &&
      //   isset(auth()->user()->swift_code) && auth()->user()->swift_code !== null &&
      //   isset(auth()->user()->idProFront) && auth()->user()->idProFront !== null &&
      //   isset(auth()->user()->idProBack) && auth()->user()->idProBack !== null
      // ) {
      //   $data['userDetails']['bankDetails'] = true;
      // }

      // // dd(auth()->user()->exchangeid);
      // if (

      //   // isset(auth()->user()->type) && $request->type !== null &&
      //   isset(auth()->user()->exchangeid) && auth()->user()->exchangeid != '' && auth()->user()->exchangeid !== null &&
      //   // isset(auth()->user()->withdrawAmt) && $request->withdrawAmt !== null &&
      //   isset(auth()->user()->passport) && auth()->user()->passport !== null &&
      //   isset(auth()->user()->nationality) && auth()->user()->nationality !== null &&
      //   isset(auth()->user()->residinglocation) && auth()->user()->residinglocation !== null &&
      //   isset(auth()->user()->dob) && auth()->user()->dob !== null


      // ) {
      //   $data['userDetails']['exchangeDetails'] = true;
      // }

      $response = ['status' => 'success', 'message' => 'The customer details have been collected successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




public function checkDomainStatus(Request $request)
  {

    try {
        
        $mainDomain = $request->mainDomain ?? null;



//  $user_id = auth()->user()->id;

      if ($mainDomain == '' || $mainDomain == null || $mainDomain == 'null') {
        $response = ['status' => 'failed', 'message' => 'Domain Required!', 'error' => 'Kindly provide valid Domain Required!'];
        goto returnFVI;
      }
      
      
    $checkDomain  = DB::table('crm')
    ->where('fullDomain', 'testshi.goride.run')
    ->where('deletes', '0')
    ->where('crmStatus', 'generated')
    ->where('userID', '!=', '0')
    ->where('subscription_id', '!=', '0')
    ->orderBy('id', 'desc')
    ->limit(1)
    ->get() ->map(function ($item) {  
        
        
        if(!empty( $item->invoiceID)){
              $item->invoiceID = json_decode($item->invoiceID, true);
        }
          
          
          if(!empty( $item->crmReq)){
             $item->crmReq = json_decode($item->crmReq, true);
        }
          
                if(!empty( $item->crmRes)){
             $item->crmRes = json_decode($item->crmRes, true);
        }
            
                if(!empty( $item->currentPlanBenefits)){
             $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);
        }
        
        
        
        return $item;
        
        });
    
    
      if ($checkDomain->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'Domain Not Found!', 'error' => 'Kindly provide valid Domain!'];
        goto returnFVI;
      }
      
      $checkDomain = $checkDomain[0];
  $user_id=  $checkDomain->userID;
    
    
    
      
    $getSubsCrbtion =   DB::table('subscriptions as p')
    ->leftJoin(
        DB::raw('(SELECT * FROM invoice i WHERE i.user_id = '.$user_id.' 
            AND i.deletes = "0" 
            AND i.subscription_id IS NOT NULL
            AND i.startDate <= CURDATE()
            AND i.endDate >= CURDATE()
            ORDER BY i.id DESC) as i'),
        'i.subscription_id', '=', 'p.id'
    )
    ->select(
        'p.id',
        'p.createdon',
        'p.planType',
        'p.paymentStatus',
        'p.grandtotal',
        'p.subscription_id as transaction_id',
        'p.invoiceID as invoice_no',
        'p.gateway',
        'p.purchaseType',
        'p.currency',
        'i.cart',
        'i.startDate',
        'i.endDate',
        'p.crmID',
        'i.id as invID',
        DB::raw("CASE 
                    WHEN p.paymentStatus IN ('COMPLETED', 'paid', 'SUCCESS') THEN 'Active'
                    WHEN p.paymentStatus IN ('CREATED', 'created') THEN 'Started'
                    ELSE LOWER(REPLACE(p.paymentStatus, '_', ' '))
                END AS pStatus")
    )
    ->whereNotNull('p.paymentStatus')
    ->where('p.user_id', '=', $user_id)
    ->orderByDesc('p.createdon')
    ->get()->map(function ($item) use ($user_id) {
        
        
         if(empty($item->endDate)) {
            $invoice = DB::table('invoice')
         ->whereDate('startDate', '<=', now())
    ->where('subscription_id', $item->id)
    ->where('deletes', '0')
    ->where('user_id', $user_id)
    ->orderBy('id', 'desc')
    ->limit(1)
    ->get();

if( $invoice->count() > 0){
    $invoice = $invoice[0];
    
    
    $item->cart = $invoice-> cart;
    
        $item->startDate = $invoice-> startDate;
        
            $item->endDate = $invoice-> endDate;
            
                $item->invID = $invoice-> id;
    
    
}
        }
        
        
        

        if (!empty($item->cart)) {
          $item->cart = json_decode($item->cart, true);
        }
        if (!empty($item->createdon)) {
          $item->createdon = Carbon::parse($item->createdon)->format('d.m.Y / h:i A');
        }
        
        
           if (!empty($item->crmID) && $item->crmID > 0) {
          $item->crmID =  Crypt::encrypt($item->crmID); 
        }
        
            if (!empty($item->endDate) && Carbon::parse($item->endDate)->isBefore(Carbon::today())) {
                     $item->pStatus = 'Expired';
                }
        
        
       
        
        
        return $item;
      });

      dd($getSubsCrbtion);

 


    //   $crmList = DB::table('crm as c')
    //   ->leftJoin('subscriptions as s', 's.id', '=', 'c.subscription_id')
    // ->select('c.*', 's.expiryDate', 's.checkout_response',  's.subscription_id as subscrID', 's.sub_status as subStatus')
    //     ->where('c.userID', $checkDomain->userID)
    //     ->where('c.deletes', '0')
    //     ->orderBy('c.id', 'desc')
    //     ->get()
    //     ->map(function ($item) {

    //       $item->transactionID = json_decode($item->transactionID, true);
    //       $item->invoiceID = json_decode($item->invoiceID, true);
    //       $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);

    //              $item->checkout_response = json_decode($item->checkout_response, true);
 
    //          $item->isActive =  Carbon::parse( $item->expiryDate)->isAfter(Carbon::now());
 

    //       $item->crmReq = isset($item->crmReq) ? json_decode($item->crmReq, true) : $item->crmReq;

    //       $item->crmRes = isset($item->crmRes) ? json_decode($item->crmRes, true) : $item->crmRes;


    //       if (isset($item->crmRes) && isset($item->crmRes['data']['domain_name'])) {
    //         $item->crmReq['fullDomain'] = $item->crmRes['data']['domain_name'];
    //       }

    //       return $item;
    //     });











      $data['userDetails']['crmList'] = null;


      if ($crmList->count() > 0) {
        $data['userDetails']['crmList'] = $crmList;
      }



    

      $response = ['status' => 'success', 'message' => 'The customer details have been collected successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  public function generateCRM(Request $request)
  {

    try {

      $data = [];

      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $crmID = $request->crmID;
      $domainPrefix = $request->domainPrefix;
      $userName = $request->userName;
      $passWord = $request->passWord;

      if ($crmID == '' || $crmID == null || $crmID == 'null') {
        $response = ['status' => 'failed', 'message' => 'CRM ID is required!', 'error' => 'CRM ID is required!'];
        goto returnFVI;
      }


      if ($domainPrefix == '' || $domainPrefix == null || $domainPrefix == 'null') {
        $response = ['status' => 'failed', 'message' => 'Domain Prefix is required!', 'error' => 'Domain Prefix is required!'];
        goto returnFVI;
      }


      if ($userName == '' || $userName == null || $userName == 'null') {
        $response = ['status' => 'failed', 'message' => 'Username is required!', 'error' => 'Username is required!'];
        goto returnFVI;
      }

      if ($passWord == '' || $passWord == null || $passWord == 'null') {
        $response = ['status' => 'failed', 'message' => 'Password is required!', 'error' => 'Password is required!'];
        goto returnFVI;
      }




      $crmData = DB::table('crm')
        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
        // ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
        ->where('id', $crmID)
        ->where('userID', $user_id)
        ->where('deletes', '0')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->map(function ($item) {
          $item->transactionID = json_decode($item->transactionID, true);
          $item->invoiceID = json_decode($item->invoiceID, true);
          $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);
          return $item;
        });

      if ($crmData->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'Please provide valid crm ID!', 'error' => 'Please provide valid crm ID!'];
        goto returnFVI;
      }

      $crmData = $crmData[0];

      if ($crmData->crmStatus === 'generated') {
        $response = [
          'status' => 'failed',
          'message' => 'Already generated, please purchase a new plan!',
          'error' => 'Already generated, please purchase a new plan!'
        ];
        goto returnFVI;
      }




      $crmNameCheck = DB::table('crm')
        // ->whereRaw("JSON_CONTAINS(transactionID, '\"$transaction_id\"', '$')")
        // ->whereRaw("JSON_CONTAINS(transactionID, '{$payment_history[0]->id}', '$')")
        // ->where('id', $crmID)
        ->where('subDomainName', $domainPrefix)
        // ->where('deletes', '0')
        ->orderBy('id', 'DESC')
        ->limit(1)
        ->get()
        ->map(function ($item) {
          $item->transactionID = json_decode($item->transactionID, true);
          $item->invoiceID = json_decode($item->invoiceID, true);
          $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true);
          return $item;
        });


      if ($crmNameCheck->count() > 0) {
        $response = ['status' => 'failed', 'message' => 'Kindly try different prefix!', 'error' => 'Kindly try different prefix!'];
        goto returnFVI;
      }


      $ticketUpdateArr = [
        // "raffleIds" => json_encode($raffleIDs),
        // "invoiceID" => json_encode($invoiceD),
        // "expiryDate" => $checkout_response['expiryDate'],
        // "currentPlanBenefits" => json_encode($checkout_response),
        "updatedon" => now(),
        "crmReq" => json_encode([
          'crmID' => $crmID,
          'domainPrefix' => $domainPrefix,
          'userName' => $userName,
          'passWord' => $passWord,
          'ip' => $request->ip()
        ]),
        // "subDomainName" => $domainPrefix,
      ];

      $ticketUpdate = DB::table('crm')->where('id', $crmID)
        ->where('userID', $user_id)->where("deletes", '0')->update($ticketUpdateArr);
      if ($ticketUpdate) {



        $reqArr['cartData'] = $crmData->currentPlanBenefits;

        $reqArr['domain_name'] = $domainPrefix;
        $reqArr['username'] = $userName;
        $reqArr['password'] = $passWord;
        $reqArr['crmID'] = $crmData->id;
        $reqArr['crmRefernce'] = $crmData->crmRefernce;

        $reqArr['name'] = auth()->user()->name;
        $reqArr['phone'] = strval(auth()->user()->mobile);
        $reqArr['email'] = auth()->user()->email;
        $reqArr['dialCode'] = auth()->user()->dialCode;


        $createCRM = Http::post(env('SHICreateCRM'), $reqArr);

        $crmRes = $createCRM->json();


        // dd($crmRes);


        if ($createCRM->successful() && isset($crmRes['status']) && $crmRes['status'] === 'success') {


          $crmArr = [
            "crmRes" => json_encode($crmRes),
            "partnerID" => $crmRes['data']['partner_id'],
            "updatedon" => now(),
            "crmStatus" => 'generated',
            "subDomainName" => $domainPrefix,
            "fullDomain" => $domainPrefix . '.' . preg_replace('/^www\./', '', $request->getHost())
          ];

          $crmUpdate = DB::table('crm')->where('id', $crmID)
            ->where('userID', $user_id)->where("deletes", '0')->update($crmArr);
          if ($crmUpdate) {

            $response = ['status' => 'success', 'message' => 'The CRM - Partner account created', 'data' => $crmRes];
            goto returnFVI;
          } else {
            $response = ['status' => 'failed', 'message' => 'The CRM Update process failed!', 'error' => 'The CRM Update process failed!'];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'The CRM generate process failed!', 'error' => 'The CRM generate process failed!', 'resErr' => $crmRes];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'The CRM Update process failed!', 'error' => 'The CRM Update process failed!'];
        goto returnFVI;
      }



      // $data['userDetails'] = [
      //   'userID' => auth()->user()->id,
      //   // 'user' => auth()->user()->user,
      //   // 'pass' => auth()->user()->pass,
      //   // 'password' => auth()->user()->password,
      //   // 'roll_id' => auth()->user()->roll_id,
      //   // 'users_refid' => auth()->user()->users_refid,
      //   // 'created_by' => auth()->user()->created_by,
      //   // 'agentIds' => auth()->user()->agentIds,
      //   'name' => auth()->user()->name,
      //   'lname' => auth()->user()->lname,
      //   'gender' => auth()->user()->gender,
      //   'dialCode' => auth()->user()->dialCode,
      //   'mobile' => auth()->user()->mobile,
      //   'mobile_verify' => auth()->user()->mobile_verify,
      //   'email' => auth()->user()->email,
      //   'email_verify' => auth()->user()->email_verify,
      //   'dob' => auth()->user()->dob,
      //   'passport' => auth()->user()->passport,
      //   'passport_expiry' => auth()->user()->passport_expiry,
      //   // 'img_url' => auth()->user()->img_url,
      //   // 'img_url' => auth()->user()->img_url != 'NULL' && auth()->user()->img_url != '' ? env('DO_REDIRECT_URL') . auth()->user()->img_url : '',
      //   // 'profile_img_url' => auth()->user()->profile_img_url,
      //   // 'profile_img_name' => auth()->user()->profile_img_name,
      //   // 'profile_img_verfiy' => auth()->user()->profile_img_verfiy,
      //   // 'profile_img_reject_id' => auth()->user()->profile_img_reject_id,
      //   // 'deletes' => auth()->user()->deletes,
      //   // 'status' => auth()->user()->status,
      //   // 'otp' => auth()->user()->otp,
      //   // 'created_at' => auth()->user()->created_at,
      //   // 't_point' => auth()->user()->t_point,
      //   // 'f_points' => auth()->user()->f_points,
      //   // 'walletBalance' => auth()->user()->walletBalance,
      //   // 'cash_points' => auth()->user()->cash_points,
      //   // 'bonus_points' => auth()->user()->bonus_points,
      //   // 'account_name' => auth()->user()->account_name,
      //   // 'acctype' => auth()->user()->acctype,
      //   // 'account_no' => auth()->user()->account_no,
      //   // 'bank_name' => auth()->user()->bank_name,
      //   // 'bank_address' => auth()->user()->bank_address,
      //   // 'branch_code' => auth()->user()->branch_code,
      //   // 'branch_name' => auth()->user()->branch_name,
      //   // 'swift_code' => auth()->user()->swift_code,
      //   // 'IBAN_code' => auth()->user()->IBAN_code,
      //   // 'currency_code' => auth()->user()->currency_code,
      //   // 'my_referral_code' => auth()->user()->my_referral_code,
      //   // 'building_name' => auth()->user()->building_name,
      //   // 'city' => auth()->user()->city,
      //   // 'address' => auth()->user()->address,
      //   // 'nationality' => auth()->user()->nationality,
      //   // 'residinglocation' => auth()->user()->residinglocation,
      //   // 'updated_at' => auth()->user()->updated_at,
      //   // 'ip' => auth()->user()->ip,
      //   // 'delete_req' => auth()->user()->delete_req,
      //   // 'delete_request' => auth()->user()->delete_request,
      //   // 'ticket_purchased' => auth()->user()->ticket_purchased,
      //   // 'ticket_count' => auth()->user()->ticket_count,
      //   // 'lastlogin' => auth()->user()->lastlogin,
      //   // 'deviceType' => auth()->user()->deviceType,
      //   // 'exchangeid' => auth()->user()->exchangeid,
      //   // 'api_token' => auth()->user()->api_token,
      //   // 'idProFront' => auth()->user()->idProFront != 'NULL' && auth()->user()->idProFront != '' ? env('DO_REDIRECT_URL') . auth()->user()->idProFront : '',
      //   // 'idProBack' => auth()->user()->idProBack != 'NULL' && auth()->user()->idProBack != '' ? env('DO_REDIRECT_URL') . auth()->user()->idProBack : ''

      // ];



      // $crmList = DB::table('crm')
      //   ->where('userID', auth()->user()->id)
      //   ->where('deletes', '0')
      //   ->orderBy('id', 'desc')
      //   ->get()
      //   ->map(function ($item) {
      //     // Assuming 'transactionID', 'invoiceID', and 'currentPlanBenefits' are JSON strings
      //     $item->transactionID = json_decode($item->transactionID, true); // Convert JSON string to array
      //     $item->invoiceID = json_decode($item->invoiceID, true);       // Convert JSON string to array
      //     $item->currentPlanBenefits = json_decode($item->currentPlanBenefits, true); // Convert JSON string to array

      //     return $item;
      //   });

      // $data['userDetails']['crmList'] = null;


      // if ($crmList->count() > 0) {
      //   $data['userDetails']['crmList'] = $crmList;
      // }



      // $result = DB::table('ndticket')
      //   ->selectRaw('COUNT(`id`) AS totalTickets, SUM(`totalRaffle`) AS totalRaffle')
      //   ->where('userId', $user_id)
      //   ->where('deletes', '0')
      //   ->first();

      // $data['ticketData']['totalTickets'] = $result->totalTickets ?? 0;
      // $data['ticketData']['totalRaffle'] = $result->totalRaffle ?? 0;

      // $data['userDetails']['shipingAddres']  = null;
      // $checkAddressBook =  DB::table('address_book')
      //   ->where('user_id', $user_id)
      //   ->where('deletes', '0')
      //   ->select('id AS addressBookID', 'name', 'dialCode', 'mobile', 'email', 'doorno', 'street', 'city', 'state', 'country', 'landmark', 'postal_code')
      //   ->orderBy('id', 'DESC')
      //   ->limit(1)
      //   ->get();


      // if ($checkAddressBook->count() > 0) {
      //   $data['userDetails']['shipingAddres'] = $checkAddressBook[0];
      // }

      // $data['userDetails']['balanceWinningGold'] = 0;
      // $totalGold = DB::table('winnerlist')
      //   ->select(DB::raw('COALESCE(SUM(prize), 0) AS totalGold'))
      //   ->where('userid', $user_id)
      //   ->where('deletes', '0')
      //   ->whereNull('requestType')
      //   ->first()->totalGold;

      // if ($totalGold) {
      //   $data['userDetails']['balanceWinningGold'] = $totalGold;
      // }


      // $data['userDetails']['bankDetails'] = false;
      // $data['userDetails']['exchangeDetails'] = false;

      // if (
      //   // isset(auth()->user()->type) && auth()->user()->type !== null &&
      //   // isset(auth()->user()->withdrawAmt) && auth()->user()->withdrawAmt !== null &&
      //   isset(auth()->user()->account_name) && auth()->user()->account_name !== null &&
      //   isset(auth()->user()->account_no) && auth()->user()->account_no !== null &&
      //   isset(auth()->user()->acctype) && auth()->user()->acctype !== null &&
      //   isset(auth()->user()->bank_name) && auth()->user()->bank_name !== null &&
      //   isset(auth()->user()->branch_name) && auth()->user()->branch_name !== null &&
      //   // isset(auth()->user()->branch_code) && auth()->user()->branch_code !== null &&
      //   // isset(auth()->user()->IBAN_code) && auth()->user()->IBAN_code !== null &&
      //   isset(auth()->user()->dob) && auth()->user()->dob !== null &&
      //   isset(auth()->user()->currency_code) && auth()->user()->currency_code !== null &&
      //   isset(auth()->user()->passport) && auth()->user()->passport !== null &&
      //   isset(auth()->user()->nationality) && auth()->user()->nationality !== null &&
      //   isset(auth()->user()->residinglocation) && auth()->user()->residinglocation !== null &&
      //   isset(auth()->user()->swift_code) && auth()->user()->swift_code !== null &&
      //   isset(auth()->user()->idProFront) && auth()->user()->idProFront !== null &&
      //   isset(auth()->user()->idProBack) && auth()->user()->idProBack !== null
      // ) {
      //   $data['userDetails']['bankDetails'] = true;
      // }

      // // dd(auth()->user()->exchangeid);
      // if (

      //   // isset(auth()->user()->type) && $request->type !== null &&
      //   isset(auth()->user()->exchangeid) && auth()->user()->exchangeid != '' && auth()->user()->exchangeid !== null &&
      //   // isset(auth()->user()->withdrawAmt) && $request->withdrawAmt !== null &&
      //   isset(auth()->user()->passport) && auth()->user()->passport !== null &&
      //   isset(auth()->user()->nationality) && auth()->user()->nationality !== null &&
      //   isset(auth()->user()->residinglocation) && auth()->user()->residinglocation !== null &&
      //   isset(auth()->user()->dob) && auth()->user()->dob !== null


      // ) {
      //   $data['userDetails']['exchangeDetails'] = true;
      // }



      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }



  public function deleteRequest(Request $request)
  {

    try {
      // $data = [];

      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      $updateUser = DB::table('user_register')
        ->where([
          ['id', '=', $user_id],
          ['deletes', '=', '0'],
          ['status', '=', '0'],
          ['roll_id', '=', '0']
        ])
        ->update([
          'delete_request' => '1',
          'lastlogin' => Carbon::now()->toDateTimeString(),
          'updated_at' => Carbon::now()->toDateTimeString()
        ]);

      if ($updateUser) {


        // Check if a delete request record already exists
        $usercheck = DB::table('user_delete_request')
          ->where([
            ['requestID', '=', $user_id],
            ['status', '=', '0'],
          ])
          ->first();

        $deleteArr = [
          'requestID' => $user_id,
          // 'deleted_by' => $user_id,
          // 'createdon' => now(),
          'status' => '0',
          'account_expire_date' => now()->addDays(60),
        ];
        if (isset($usercheck) && $usercheck->id != '') {
          $deleteReq = DB::table('user_delete_request')
            ->where([
              ['id', '=', $usercheck->id],
              ['requestID', '=', $user_id],
            ])
            ->update($deleteArr);
        } else {
          $deleteArr['createdon'] = Carbon::now()->toDateTimeString();
          $deleteReq = DB::table('user_delete_request')->insert($deleteArr);
        }

        if ($deleteReq) {
          $response = ['status' => 'success', 'message' => 'Account deletion request submitted successfully', 'data' => 'Account deletion request submitted successfully'];
          goto returnFVI;
        } else {
          $response = ['status' => 'failed', 'message' => 'The delete request process has failed.', 'error' => 'The delete request process has failed.'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'The user update process has failed.', 'error' => 'The user update process has failed.'];
        goto returnFVI;
      }

      $response = ['status' => 'success', 'message' => 'The customer details have been collected successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function deleteIdProof(Request $request)
  {

    try {
      // $data = [];
      $updateArr = [];
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      // $key = ['idProFront', 'idProBack'];



      if (isset($request->idProFront) && $request->idProFront === 'true') {
        $updateArr['idProFront'] = null;
      }

      if (isset($request->idProBack) && $request->idProBack === 'true') {
        $updateArr['idProBack'] = null;
      }

      if (count($updateArr) < 1) {
        $response = ['status' => 'failed', 'message' => 'Please provide valid key!', 'error' => 'Please provide valid key!'];
        goto returnFVI;
      }


      $updateArr['lastlogin'] = Carbon::now()->toDateTimeString();


      $updateArr['updated_at'] = Carbon::now()->toDateTimeString();

      // dd($updateArr);

      $updateUser = DB::table('user_register')
        ->where([
          ['id', '=', $user_id],
          ['deletes', '=', '0'],
          ['status', '=', '0'],
          ['roll_id', '=', '0']
        ])
        ->update($updateArr);

      if ($updateUser) {
        $response = ['status' => 'success', 'message' => 'The Image deleted successfully', 'data' => 'Account deletion request submitted successfully'];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'The image delete request process has failed.', 'error' => 'The delete request process has failed.'];
        goto returnFVI;
      }

      // $response = ['status' => 'success', 'message' => 'The customer details have been collected successfully', 'data' => $data];
      // goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }


  public function updateDetails(Request $request)
  {
    try {
      $updateArr = [];
      $imgTagArr = [];
      $user_id = auth()->user()->id;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login required!', 'error' => 'Please ensure your access token is correct.'];
        goto returnFVI;
      }

      $request->nationality = Controller::BlockSQLInjection($request->nationality);
      if ($request->nationality == '' || $request->nationality == null || $request->nationality == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please select a nationality!', 'error' => 'Please use a valid nationality.'];
        goto returnFVI;
      }

      $request->livein = Controller::BlockSQLInjection($request->livein);
      if ($request->livein == '' || $request->livein == null || $request->livein == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid "live-in" address.', 'error' => 'Please provide a valid "live-in" address.'];
        goto returnFVI;
      }

      $request->emOrpassport = Controller::BlockSQLInjection($request->emOrpassport);
      if ($request->emOrpassport == '' || $request->emOrpassport == null || $request->emOrpassport == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid Emirates ID or passport!', 'error' => 'Please provide a valid Emirates ID or passport.'];
        goto returnFVI;
      }

      if ($request->hasFile('idProFront')) {
        $imgTagArr[] = 'idProFront';
      }

      if ($request->hasFile('idProBack')) {
        $imgTagArr[] = 'idProBack';
      }

      if (count($imgTagArr) > 0) {
        foreach ($imgTagArr as $value) {

          $allowedMimeTypes = ['jpeg', 'png', 'jpg', 'gif', 'webp'];
          $maxFileSize = 5120; // Maximum file size in kilobytes (5 MB)

          if ($request->hasFile($value)) {
            $file = $request->file($value);
            $name = $file->getClientOriginalName();

            if ($file->isValid() && in_array($file->getClientOriginalExtension(), $allowedMimeTypes) && $file->getSize() <= $maxFileSize * 1024) {

              // Your code to process the uploaded image goes here
              $filePath = 'nationaldraw/' . $user_id . '/' . md5($user_id . $name . time()) . '.' . $file->getClientOriginalExtension();

              $store = Storage::disk('spaces')->put(
                '/' . $filePath,
                file_get_contents($request->file($value)->getRealPath()),
                'public'
              );

              if ($store) {
                $url = $filePath;
                if ($url != '') {
                  $updateUser = DB::table('user_register')
                    ->where([
                      ['id', '=', $user_id],
                      ['deletes', '=', '0'],
                      ['status', '=', '0'],
                      ['roll_id', '=', '0']
                    ])
                    ->update([
                      $value => $url,
                      'updated_at' => Carbon::now()->toDateTimeString()
                    ]);

                  if (!$updateUser) {
                    $response = ['status' => 'failed', 'message' => 'The user update process has failed.', 'error' => 'The user update process has failed.'];
                    goto returnFVI;
                  }
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

      $request->type = Controller::BlockSQLInjection($request->type);
      if ($request->type == '' || $request->type == null || $request->type == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid type!', 'error' => 'Please provide a valid type!'];
        goto returnFVI;
      }

      if ($request->type === 'exchange') {

        $request->exchangeID = Controller::BlockSQLInjection($request->exchangeID);
        if ($request->exchangeID == '' || $request->exchangeID == null || $request->exchangeID == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please use a valid Exchange ID!', 'error' => 'Please use a valid Exchange ID!'];
          goto returnFVI;
        }

        $updateArr = [
          'passport' => $request->emOrpassport,
          'nationality' => $request->nationality,
          'residinglocation' => $request->livein,
          'exchangeid' => $request->exchangeID,
          'updated_at' => Carbon::now()->toDateTimeString(),
          'lastlogin' => Carbon::now()->toDateTimeString(),
        ];
      } else if ($request->type === 'bank') {

        $request->accName = Controller::BlockSQLInjection($request->accName);
        if ($request->accName == '' || $request->accName == null || $request->accName == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid account holder name!', 'error' => 'Please provide a valid account holder name!'];
          goto returnFVI;
        }

        $request->accNo = Controller::BlockSQLInjection($request->accNo);
        if ($request->accNo == '' || $request->accNo == null || $request->accNo == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid account no!', 'error' => 'Please provide a valid account no!'];
          goto returnFVI;
        }

        $request->accType = Controller::BlockSQLInjection($request->accType);
        if ($request->accType == '' || $request->accType == null || $request->accType == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid account type!', 'error' => 'Please provide a valid account type!'];
          goto returnFVI;
        }

        $request->bankName = Controller::BlockSQLInjection($request->bankName);
        if ($request->bankName == '' || $request->bankName == null || $request->bankName == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid bank name!', 'error' => 'Please provide a valid bank name!'];
          goto returnFVI;
        }

        $request->branchName = Controller::BlockSQLInjection($request->branchName);
        if ($request->branchName == '' || $request->branchName == null || $request->branchName == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid branch name!', 'error' => 'Please provide a valid branch name!'];
          goto returnFVI;
        }

        // $request->branchCode = Controller::BlockSQLInjection($request->branchCode);
        // if ($request->branchCode == '' || $request->branchCode == null || $request->branchCode == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please provide a valid branch code!', 'error' => 'Please provide a valid branch code!'];
        //   goto returnFVI;
        // }

        // $request->ibanNo = Controller::BlockSQLInjection($request->ibanNo);
        // if ($request->ibanNo == '' || $request->ibanNo == null || $request->ibanNo == 'null') {
        //   $response = ['status' => 'failed', 'message' => 'Please provide a valid iban code!', 'error' => 'Please provide a valid iban code!'];
        //   goto returnFVI;
        // }

        $request->swiftCode = Controller::BlockSQLInjection($request->swiftCode);
        if ($request->swiftCode == '' || $request->swiftCode == null || $request->swiftCode == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid swift code!', 'error' => 'Please provide a valid swift code!'];
          goto returnFVI;
        }

        $request->currencyCode = Controller::BlockSQLInjection($request->currencyCode);
        if ($request->currencyCode == '' || $request->currencyCode == null || $request->currencyCode == 'null') {
          $response = ['status' => 'failed', 'message' => 'Please provide a valid currency code!', 'error' => 'Please provide a valid currency code!'];
          goto returnFVI;
        }

        $updateArr = [
          'passport' => $request->emOrpassport,
          'nationality' => $request->nationality,
          'residinglocation' => $request->livein,
          'updated_at' => Carbon::now()->toDateTimeString(),
          'lastlogin' => Carbon::now()->toDateTimeString(),
          'currency_code' => $request->currencyCode,
          'swift_code' => $request->swiftCode,
          //   'IBAN_code' => $request->ibanNo,
          'branch_code' => $request->branchCode,
          'branch_name' => $request->branchName,
          'bank_name' => $request->bankName,
          'acctype' => $request->accType,
          'account_no' => $request->accNo,
          'account_name' => $request->accName
        ];
      } else {
        $response = ['status' => 'failed', 'message' => 'Method not found!', 'error' => 'Please provide a valid method.'];
        goto returnFVI;
      }

      $updateUser = DB::table('user_register')
        ->where([
          ['id', '=', $user_id],
          ['deletes', '=', '0'],
          ['status', '=', '0'],
          ['roll_id', '=', '0']
        ])->limit(1)
        ->update($updateArr);
      // dd($updateArr);

      if (!$updateUser) {
        $response = ['status' => 'failed', 'message' => 'The user update process has failed.', 'error' => 'The user update process has failed.'];
        goto returnFVI;
      }

      $response = ['status' => 'success', 'message' => 'The details were updated successfully.', 'data' => 'The details were updated successfully.'];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }




  public function updateAddressBook(Request $request)
  {

    try {
      // $data = [];
      // $updateArr = [];
      $response = [];

      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $request->name = Controller::BlockSQLInjection($request->name);
      if ($request->name == '' || $request->name == null || $request->name == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid name.', 'error' => 'Please provide a valid name.'];
        goto returnFVI;
      }


      $request->dialCode = Controller::BlockSQLInjection($request->dialCode);
      if ($request->dialCode == '' || $request->dialCode == null || $request->dialCode == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid dialCode.', 'error' => 'Please provide a valid dialCode.'];
        goto returnFVI;
      }


      $request->mobile = Controller::BlockSQLInjection($request->mobile);
      if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid mobile no.', 'error' => 'Please provide a valid mobile no.'];
        goto returnFVI;
      }

      $request->doorNo = Controller::BlockSQLInjection($request->doorNo);
      if ($request->doorNo == '' || $request->doorNo == null || $request->doorNo == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid door no.', 'error' => 'Please provide a valid door no.'];
        goto returnFVI;
      }

      $request->street = Controller::BlockSQLInjection($request->street);
      if ($request->street == '' || $request->street == null || $request->street == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid street.', 'error' => 'Please provide a valid street.'];
        goto returnFVI;
      }


      $request->landmark = Controller::BlockSQLInjection($request->landmark);
      if ($request->landmark == '' || $request->landmark == null || $request->landmark == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid landmark.', 'error' => 'Please provide a valid landmark.'];
        goto returnFVI;
      }


      $request->pincode = Controller::BlockSQLInjection($request->pincode);
      if ($request->pincode == '' || $request->pincode == null || $request->pincode == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid pincode.', 'error' => 'Please provide a valid pincode.'];
        goto returnFVI;
      }


      $request->country = Controller::BlockSQLInjection($request->country);
      if ($request->country == '' || $request->country == null || $request->country == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid country.', 'error' => 'Please provide a valid country.'];
        goto returnFVI;
      }

      $request->state = Controller::BlockSQLInjection($request->state);
      if ($request->state == '' || $request->state == null || $request->state == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid state.', 'error' => 'Please provide a valid state.'];
        goto returnFVI;
      }

      $request->city = Controller::BlockSQLInjection($request->city);
      if ($request->city == '' || $request->city == null || $request->city == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please provide a valid city.', 'error' => 'Please provide a valid city.'];
        goto returnFVI;
      }

      $addressBookI = [
        'name' => $request->name,
        'dialCode' => $request->dialCode,
        'mobile' => $request->mobile,
        'doorno' => $request->doorNo,
        'street' => $request->street,
        'city' => $request->city,
        'state' => $request->state,
        'country' => $request->country,
        'postal_code' => $request->pincode,
        'landmark' => $request->landmark,
        'updated_at' => Carbon::now()->toDateTimeString()
      ];


      $request->email = Controller::BlockSQLInjection($request->email);
      if ($request->email != '' && $request->email != null && $request->email != 'null') {
        $addressBookI['email'] = $request->email;
      }

      $checkAddressBook = DB::table('address_book')
        ->where([
          ['user_id', '=', $user_id],
          ['deletes', '=', '0'],
          // ['status', '=', '0'],
          // ['roll_id', '=', '0']
        ])->orderby('id', 'DESC')->limit(1)->get();

      if ($checkAddressBook->count() < 1) {
        $addressBookI['created_at'] = Carbon::now()->toDateTimeString();
        $addressBookI['user_id'] = $user_id;

        $addressBook = DB::table('address_book')->insert($addressBookI);
      } else {
        $addressBook = DB::table('address_book')->where([
          ['id', '=', $checkAddressBook[0]->id],
          ['user_id', '=', $user_id],
          ['deletes', '=', '0'],
          // ['status', '=', '0'],
          // ['roll_id', '=', '0']
        ])->limit(1)->update($addressBookI);
      }


      // dd($addressBook);
      if ($addressBook) {
        $response = ['status' => 'success', 'message' => 'The address book was updated successfully', 'data' => 'The address book was updated successfully'];
        goto returnFVI;
      } else {
        $response = ['status' => 'failed', 'message' => 'The address book update process has failed.', 'error' => 'The address book update process has failed.'];
        goto returnFVI;
      }


      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }






  public function showWinners(Request $request)
  {
    try {
      $offset = $request->input('date_range');
      $startDraw = $offset;
      $endDraw = $offset + 6;
      $winnersQuery = DB::table('winnerlist')
        ->select(
          'userid',
          'draw_id',
          'drawType',
          'winningDrawName',
          'winRaffleId',
          'fullName',
          'winnerlist.email',
          'winnerlist.mobile',
          'ticketID',
          'ticketReferenceID',
          DB::raw('IFNULL(NULLIF(winnerlist.residingcountry, ""), user_register.residinglocation) AS residingcountry'),
          DB::raw('IFNULL(NULLIF(winnerlist.country, ""), user_register.nationality) AS country'),
          'prize',
          'image_url'
        )
        ->join('draw', 'draw.id', '=', 'winnerlist.draw_id')
        ->leftJoin('user_register', 'user_register.id', '=', 'winnerlist.userid')
        ->whereBetween('draw.dailyDrawNo', [$startDraw, $endDraw])
        ->orderBy('draw.id');

      $defaultImageUrl = "assets/img/pro-d1.png";

      $winners = $winnersQuery->get()->map(function ($winner) use ($defaultImageUrl) {
        if (!empty($winner->image_url)) {
          $winner->image_url = env('DO_REDIRECT_URL') . $winner->image_url;
        } else {
          $winner->image_url = $defaultImageUrl;
        }
        return $winner;
      });

      return response()->json([
        'status' => 'success',
        'message' => 'The winners list for the offset ' . $offset . ' was retrieved successfully',
        'data' => $winners
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to retrieve winners list',
        'error' => $e->getMessage()
      ]);
    }
  }





  public function showTransaction(Request $request)
  {
    try {
      $user_id = auth()->user()->id;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login required!', 'error' => 'Please ensure your access token is correct.'];
        goto returnFVI;
      }

      $transactions = DB::table('payment_history')
        ->select('id', 'createdon', 'renewalStatus', 'paymentStatus', 'grandtotal', 'transaction_id', 'invoice_no', 'ticketReferenceID', 'gateway')
        ->where('user_id', '=', $user_id)
        ->whereNotNull('paymentStatus')
        ->orderBydesc('createdon')
        ->get();


      $transactions = $transactions->map(function ($transaction) {
        $description = 'N/A';
        if (in_array($transaction->paymentStatus, ['Success', 'SUCCESS'])) {
          // $description = 'Transaction ID: ' . $transaction->transaction_id . '</br> Invoice No: <a href="invoice/' . $transaction->ticketReferenceID . '" class="view-btn" target="_blank">#' . $transaction->invoice_no . '</a>';

          $description = 'Transaction ID: ' . $transaction->transaction_id;
        } elseif ($transaction->paymentStatus === '' || $transaction->paymentStatus === null) {
          $description = 'Transaction ID: ' . $transaction->transaction_id . $transaction->id;
        }



        $transaction->renewalStatus = ($transaction->renewalStatus == 'DELIVERYTOCUSTOMER' ? 'Delivery' : ($transaction->renewalStatus == 'PICKUPTOSTORE' ? 'Pickup At Store' : ($transaction->renewalStatus == 'RECHARGE' ? 'Wallet Top Up' : ucfirst(strtolower($transaction->renewalStatus)))));

        $transaction->description = $description;
        return $transaction;
      });

      return response()->json([
        'status' => 'success',
        'message' => 'The transaction history was retrieved successfully',
        'data' => $transactions
      ]);





      returnFVI:
      return response()->json($response);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to retrieve transaction history',
        'error' => $e->getMessage()
      ]);
    }
  }



  public function packageHistory(Request $request)
  {
    try {
      $user_id = auth()->user()->id;

      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login required!', 'error' => 'Please ensure your access token is correct.'];
        goto returnFVI;
      }




      // $transactions = DB::table('payment_history as p')
      //   ->leftJoin('invoice as i', 'i.id', '=', 'p.invoice_no')
      //   ->select(
      //     'p.id',
      //     'p.createdon',
      //     'p.planType',
      //     'p.paymentStatus',
      //     'p.grandtotal',
      //     'p.transaction_id',
      //     'p.invoice_no',
      //     'p.gateway',
      //     'p.purchaseType',
      //     'p.currency',
      //     'i.cart',
      //     'i.startDate',
      //     'i.endDate',
      //     DB::raw("CASE 
      //               WHEN p.paymentStatus IN ('COMPLETED', 'paid', 'SUCCESS') THEN 'Success'
      //               WHEN p.paymentStatus IN ('CREATED', 'created') THEN 'Started'
      //               ELSE p.paymentStatus
      //           END AS pStatus")
      //   )
      //   ->whereNotNull('p.paymentStatus')
      //   ->where('p.user_id', '=', $user_id)
      //   ->orderByDesc('p.createdon')
      //   ->get()->map(function ($item) {

      //     if (!empty($item->cart)) {
      //       $item->cart = json_decode($item->cart, true);
      //     }
      //     if (!empty($item->createdon)) {
      //       $item->createdon = Carbon::parse($item->createdon)->format('d.m.Y / h:i A');
      //     }
      //     return $item;
      //   });


        
      $transactions =
      
    //   DB::table('subscriptions as p')
    //   ->leftJoin('invoice as i', 'i.id', '=', 'p.invoiceID')
    //   ->select(
    //     'p.id',
    //     'p.createdon',
    //     'p.planType',
    //     'p.paymentStatus',
    //     'p.grandtotal',
    //     'p.subscription_id as transaction_id' ,
    //     'p.invoiceID as invoice_no',
    //     'p.gateway',
    //     'p.purchaseType',
    //     'p.currency',
    //     'i.cart',
    //     'i.startDate',
    //     'i.endDate',
    //     'p.crmID',
    //     DB::raw("CASE 
    //           --    WHEN p.paymentStatus IN ('COMPLETED', 'paid', 'SUCCESS') THEN 'Success'
    //               WHEN p.paymentStatus IN ('COMPLETED', 'paid', 'SUCCESS') THEN 'Active'
    //               WHEN p.paymentStatus IN ('CREATED', 'created') THEN 'Started'
    //               ELSE LOWER(REPLACE(p.paymentStatus, '_', ' '))
    //           END AS pStatus")
    //   )
    //   ->whereNotNull('p.paymentStatus')
    //   ->where('p.user_id', '=', $user_id)
    //   ->orderByDesc('p.createdon')
    //   ->get()
      
      
      
      
      DB::table('subscriptions as p')
    ->leftJoin(
        DB::raw('(SELECT * FROM invoice i WHERE i.user_id = '.$user_id.' 
            AND i.deletes = "0" 
            AND i.subscription_id IS NOT NULL
            AND i.startDate <= CURDATE()
            AND i.endDate >= CURDATE()
            ORDER BY i.id DESC) as i'),
        'i.subscription_id', '=', 'p.id'
    )
    ->select(
        'p.id',
        'p.createdon',
        'p.planType',
        'p.paymentStatus',
        'p.grandtotal',
        'p.subscription_id as transaction_id',
        'p.invoiceID as invoice_no',
        'p.gateway',
        'p.purchaseType',
        'p.currency',
        'i.cart',
        'i.startDate',
        'i.endDate',
        'p.crmID',
        'i.id as invID',
        DB::raw("CASE 
                    WHEN p.paymentStatus IN ('COMPLETED', 'paid', 'SUCCESS') THEN 'Active'
                    WHEN p.paymentStatus IN ('CREATED', 'created') THEN 'Started'
                    ELSE LOWER(REPLACE(p.paymentStatus, '_', ' '))
                END AS pStatus")
    )
    ->whereNotNull('p.paymentStatus')
    ->where('p.user_id', '=', $user_id)
    ->orderByDesc('p.createdon')
    ->get()->map(function ($item) use ($user_id) {
        
        
         if(empty($item->endDate)) {
            $invoice = DB::table('invoice')
         ->whereDate('startDate', '<=', now())
    ->where('subscription_id', $item->id)
    ->where('deletes', '0')
    ->where('user_id', $user_id)
    ->orderBy('id', 'desc')
    ->limit(1)
    ->get();

if( $invoice->count() > 0){
    $invoice = $invoice[0];
    
    
    $item->cart = $invoice-> cart;
    
        $item->startDate = $invoice-> startDate;
        
            $item->endDate = $invoice-> endDate;
            
                $item->invID = $invoice-> id;
    
    
}
        }
        
        
        

        if (!empty($item->cart)) {
          $item->cart = json_decode($item->cart, true);
        }
        if (!empty($item->createdon)) {
          $item->createdon = Carbon::parse($item->createdon)->format('d.m.Y / h:i A');
        }
        
        
           if (!empty($item->crmID) && $item->crmID > 0) {
          $item->crmID =  Crypt::encrypt($item->crmID); 
        }
        
            if (!empty($item->endDate) && Carbon::parse($item->endDate)->isBefore(Carbon::today())) {
                     $item->pStatus = 'Expired';
                }
        
        
       
        
        
        return $item;
      });


      return response()->json([
        'status' => 'success',
        'message' => 'The transaction history was retrieved successfully',
        'data' => [
          'packageHis' => $transactions
        ]
      ]);


      returnFVI:
      return response()->json($response);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to retrieve transaction history',
        'error' => $e->getMessage()
      ]);
    }
  }

  // public function Withdrawalhistory(Request $request)
  // {
  //   try {
  //     $auth = auth()->user()->id;
  //     $withdrawalhistory = DB::table('withdraw_request')
  //       ->select(
  //         'request_id',
  //         'createdon',
  //         'amount',
  //         DB::raw('CASE 
  //                           WHEN status = 0 THEN "Pending"
  //                           WHEN status = 1 THEN "Completed"
  //                           WHEN status = 2 THEN "Rejected"
  //                        END AS status_description'),
  //         'attachment_url'
  //       )
  //       ->get();

  //     $withdrawalhistory->transform(function ($item, $key) {
  //       $item->attachment_url = $item->attachment_url ? env('DO_REDIRECT_URL') . $item->attachment_url : null;
  //       return $item;
  //     });


  //     return response()->json([
  //       'status' => 'success',
  //       'message' => 'The Withdrawal history was retrieved successfully',
  //       'data' => $withdrawalhistory
  //     ]);
  //   } catch (\Exception $e) {
  //     return response()->json([
  //       'status' => 'error',
  //       'message' => 'Failed to retrieve Withdrawal history',
  //       'error' => $e->getMessage()
  //     ]);
  //   }
  // }




  public function Withdrawalhistory(Request $request)
  {
    try {

      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $withdrawalhistory = DB::table('withdraw_request')
        ->select(
          'request_id',
          'createdon',
          'amount',
          'status',
          'attachment_url',
          'openingBalance',
          'closingBalance'
        )->where('requested_by', '=', $user_id)
        ->where('deletes', '=', '0')
        ->get();

      $withdrawalhistory = $withdrawalhistory->map(function ($item, $key) {
        if ($item->status == 0) {
          $item->status_description = 'Pending';
        } elseif ($item->status == 1) {
          $item->status_description = 'Completed';
        } elseif ($item->status == 2) {
          $item->status_description = 'Rejected';
        } else {
          $item->status_description = 'Unknown';
        }
        return $item;
      });


      $withdrawalhistory->transform(function ($item, $key) {
        if ($item->attachment_url) {
          $item->attachment_url = env('DO_REDIRECT_URL') . $item->attachment_url;
        }

        return $item;
      });


      return response()->json([
        'status' => 'success',
        'message' => 'The Withdrawal history was retrieved successfully',
        'data' => $withdrawalhistory
      ]);


      returnFVI:
      return response()->json($response);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to retrieve Withdrawal history',
        'error' => $e->getMessage()
      ]);
    }
  }

  public function Wallethistory(Request $request)
  {
    try {
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      $withdrawalhistory = DB::table('wallet_history')
        ->select(
          'userid',
          'createdon',
          'opening_balance',
          'closeing_balance',
          DB::raw("CASE WHEN reward_type = 'WalletDeposit' THEN 'Wallet Top Up' ELSE reward_type END AS reward_type"),
          'total'
        )
        ->where('deletes', '=', '0')
        ->where('userid', $user_id)
        ->whereIn('reward_type', ['ADWALLETTRANSFER', 'WALLETTRANSFER', 'WalletDeposit'])
        ->get();

      return response()->json([
        'status' => 'success',
        'message' => 'The Wallet history was retrieved successfully',
        'data' => $withdrawalhistory
      ]);

      returnFVI:
      return response()->json($response);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Failed to retrieve Wallet history',
        'error' => $e->getMessage()
      ]);
    }
  }
}
