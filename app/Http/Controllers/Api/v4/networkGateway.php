<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
// use App\Http\Controllers\drawsController;
use DB;
use Exception;
use Illuminate\Http\Request;

class networkGateway extends Controller
{


  public function invokeApiRequest($type, $url, $headers, $post)
  {
    try {
      $curl = curl_init();

      curl_setopt_array(
        $curl,
        array(

          CURLOPT_URL => $url,

          CURLOPT_RETURNTRANSFER => true,

          CURLOPT_ENCODING => '',

          CURLOPT_MAXREDIRS => 10,

          CURLOPT_TIMEOUT => 0,

          CURLOPT_FOLLOWLOCATION => true,

          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

          CURLOPT_CUSTOMREQUEST => $type,

          CURLOPT_POSTFIELDS => $post,

          CURLOPT_HTTPHEADER => $headers,

        )
      );

      $response = curl_exec($curl);

      curl_close($curl);

      return json_decode($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function identify($apikey, $idUrl)
  {

    try {

      $idHead = array("Authorization: Basic " . $apikey, "Content-Type: application/vnd.ni-identity.v1+json");
      $idOutput = $this->invokeApiRequest("POST", $idUrl, $idHead, '');

      return $idOutput;
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function pay($payUrlRe, $token, $ammt, $tran_id, $firstname, $address, $emailid)
  {
    try {
      $ord = [
        "action" => "SALE",
        "billingAddress" => [
          "firstName" => $firstname,
          "address1" => $address,
        ],
        "emailAddress" => $emailid,
        "merchantOrderReference" => $tran_id,
        "language" => "en",
        "merchantAttributes" => [
          "redirectUrl" => ($request->header('Origin') . '/') . 'network/websdk_response.php',
          "skipConfirmationPage" => true,
          "cancelUrl" => ($request->header('Origin') . '/') . 'network/redirect.php',
          "cancelText" => 'Return to Play',
        ],
        "amount" => [
          "currencyCode" => "AED",
          "value" => $ammt,
        ],
      ];

      $payHead = array("Authorization: Bearer " . $token, "Content-Type: application/vnd.ni-payment.v2+json");
      $payPost = json_encode($ord);

      $payOutput = $this->invokeApiRequest("POST", $payUrlRe, $payHead, $payPost);
      return $payOutput;
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  // Get User Product Cart Details
  public function networkInitiate(Request $request)
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
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Transaction ID Required!', 'error' => 'Kindly check the transaction ID!'];
        goto returnFVI;
      }

      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }

      $firstName = str_replace("'", "`", auth()->user()->name);
      $email = (auth()->user()->email != '') ? auth()->user()->email : auth()->user()->mobile . '@nationaldrawuae.com';
      $address = auth()->user()->address;


      $startTime = now()->subHours(6)->toDateTimeString();

      $resultPay = DB::table('payment_history')
        ->select('finaltotal', 'id', 'transaction_id', 'verify_status', 'category')
        ->where('transaction_id', '=', $transaction_id)
        ->where('createdon', '>', $startTime)
        ->orderBy('id', 'desc')
        ->limit(1)
        ->get();
      if ($resultPay->count() < 1) {
        $response = ['status' => 'failed', 'message' => 'The Transaction ID Not Found!', 'error' => 'The Transaction ID Not Found!'];
        goto returnFVI;
      }

      // dd($resultPay[0]);
      if ($resultPay[0]->verify_status == 'NO' && $resultPay[0]->category == 'PRODUCT') {
        $response = ['status' => 'failed', 'message' => 'The verification failed!', 'error' => 'The verification failed!'];
        goto returnFVI;
      }

      // $ammt = (int) DB::table('payment_history')->where('transaction_id', $transaction_id)->value('finaltotal');
      $ammt = (int) $resultPay[0]->finaltotal;

      // dd($result);
      if ($ammt > 0 && $ammt != 0) {

        $idData = $this->identify(env('networkAPIKEY'), env('networkAccessUrl'));

        if (isset($idData->access_token)) {

          $token = $idData->access_token;
          $amt = $ammt * 100;
          $payData = $this->pay(env('networkCommonUrl') . env('networkOutlet') . "/orders", $token, $amt, $transaction_id, $firstName, $address, $email);
          $arrayResponse = (array) $payData;
          $merchantOrderReference = $payData->merchantOrderReference;
          $order_reference = $payData->reference;
          $order_paypage_url = $payData->_links->payment->href;
          // dd($payData);
          if ($order_reference != '' && $order_paypage_url != '' && $merchantOrderReference == $transaction_id) {

            $updateData = [
              'pay_response' => json_encode($payData),
              'gateway' => 'network',
              'draw_id' => $draw_id,
              'reference' => $order_reference,
              'request_url' => $order_paypage_url,
            ];
            // dd($updateData);
            $paymentHistory = DB::table('payment_history')
              ->where('id', '=', $resultPay[0]->id)
              // ->where('transaction_id', '=', $transaction_id)
              ->where('status', '0')
              ->where('createdon', '>', $startTime)
              ->orderBy('id', 'desc')
              ->update($updateData);


            if ($paymentHistory) {
              $data["pay_url"] = $order_paypage_url;
              $response = ['status' => 'success', 'message' => 'Network payment initiated successfully', 'data' => $data];
              goto returnFVI;
            } else {
              $response = ['status' => 'failed', 'message' => 'Payment Gateway not Responding!', 'error' => 'Network payment initiated successfully!'];
              goto returnFVI;
            }
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'Payment Gateway not Responding!', 'error' => 'Token getting API failed!'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'The total amount missing!', 'error' => 'Kindly Contact Admin!'];
        goto returnFVI;
      }

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  // Get User Product Cart Details
  public function networkSuccess(Request $request)
  {

    try {
      $response = [];
      $input = $request->all();

      $request->orderReference = Controller::BlockSQLInjection($request->orderReference);
      if ($request->orderReference == '' || $request->orderReference == null || $request->orderReference == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid order Reference!', 'error' => 'Please use a valid order Reference!'];
        goto returnFVI;
      }


      $orderReference = $request->orderReference;
      $draw = Controller::getActiveDrawData()->content();
      $drawData = json_decode($draw);
      $draw_id = $drawData->data->active->draw_id ?? '';
      $data = [];
      // Get User ID
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }

      if ($orderReference == '' || $orderReference == null || $orderReference == 'null') {
        $response = ['status' => 'failed', 'message' => 'Order Reference Required!', 'error' => 'Kindly check the Order Reference!'];
        goto returnFVI;
      }

      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'The Active Draw Not Found!', 'error' => 'The Active Draw Not Found!'];
        goto returnFVI;
      }


      // Log
      $log = Controller::error_log_new($request->ip(), 'network_customer_return', $user_id, '', '', 'Customer Enter to National Draw Site', json_encode($request), __DIR__, basename(__FILE__), __LINE__);

      $residServiceURL = env('networkCommonUrl') . env('networkOutlet') . "/orders/" . $orderReference;
      $tokenHeaders = array("Authorization: Basic " . env('networkAPIKEY'), "Content-Type: application/vnd.ni-identity.v1+json");
      $tokenResponse = $this->identify(env('networkAPIKEY'), env('networkAccessUrl'));
      if (isset($tokenResponse->access_token)) {

        // Log
        $log = Controller::error_log_new($request->ip(), 'get_network_token', $user_id, '', '', 'Try To Get network Token', json_encode($request), __DIR__, basename(__FILE__), __LINE__);

        $responseHeaders = array("Authorization: Bearer " . $tokenResponse->access_token, "Content-Type: application/vnd.ni-payment.v2+json", "Accept: application/vnd.ni-payment.v2+json");
        $orderResponse = $this->invokeApiRequest("GET", $residServiceURL, $responseHeaders, '');

        $data['gatewayResponse'] = (array) $orderResponse;


        // Log
        $log = Controller::error_log_new($request->ip(), 'get_network_payment_status', $user_id, '', '', 'Try To Get payment status from to network', json_encode($data['gatewayResponse']), __DIR__, basename(__FILE__), __LINE__);

        $data['MakepaymentState'] = isset($orderResponse->_embedded->payment[0]->state) ? $orderResponse->_embedded->payment[0]->state : '';
        $data['makepayemntre'] = isset($orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]->state) ? $orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]->state : '';

        $tran_id = $orderResponse->merchantOrderReference;
        $data['merchantOrderReference'] = $orderResponse->merchantOrderReference;


        $trid = DB::table('payment_history')
          ->select('id', 'transaction_id', 'category')
          ->where('transaction_id', 'LIKE', $tran_id)
          ->where('status', '0')
          ->where('gateway', 'network')
          ->orderBy('id', 'desc')
          ->limit(1)
          ->get();




        // $trid = select_query($con, "payment_history", "", "`transaction_id` = '$tran_id' and `status` = '0'", "", "");

        if ($trid->count() > 0) {


          $draw_arr = [
            'pay_re_status' => $data['MakepaymentState'],
            'response' => json_encode($orderResponse),
          ];
          $Inv_update = DB::table('payment_history')
            ->where('id', '=', $trid[0]->id)
            ->where('transaction_id', 'LIKE', $tran_id)
            ->where('status', '=', '0')
            ->update($draw_arr);
          // dd($Inv_update);
          if ($Inv_update) {

            // if (isset($orderResponse->_embedded) && $orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]->state == "SUCCESS" && $orderResponse->_embedded->payment[0]->state != 'FAILED' && $orderResponse->_embedded->payment[0]->state == 'CAPTURED') 
            if (
              isset($orderResponse->_embedded) && is_object($orderResponse->_embedded) &&
              isset($orderResponse->_embedded->payment[0]) && is_object($orderResponse->_embedded->payment[0]) &&
              isset($orderResponse->_embedded->payment[0]->_embedded) && is_object($orderResponse->_embedded->payment[0]->_embedded) &&
              isset($orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]) && is_object($orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]) &&
              isset($orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]->state) &&
              $orderResponse->_embedded->payment[0]->_embedded->{'cnp:capture'}[0]->state == "SUCCESS" &&
              isset($orderResponse->_embedded->payment[0]->state) &&
              $orderResponse->_embedded->payment[0]->state != 'FAILED' &&
              $orderResponse->_embedded->payment[0]->state == 'CAPTURED'
            ) {





              // Log
              $log = Controller::error_log_new($request->ip(), 'redirect_to_success', $user_id, '', '', 'Success redirect. ID: ' . $data["merchantOrderReference"], json_encode($request), __DIR__, basename(__FILE__), __LINE__);

              if ($trid[0]->category == 'CARTON') {
                $data['redirectURL'] = ($request->header('Origin') . '/') . "index.php?makepayment=success&category=carton";
              } else {
                $data['redirectURL'] = ($request->header('Origin') . '/') . "index.php?makepayment=success";
              }


              // $data['redirectURL'] = ($request->header('Origin') . '/') . 'failed/';
              $response = ['status' => 'success', 'message' => 'Network payment success', 'data' => $data];


              // Log
              $log = Controller::error_log_new($request->ip(), 'network_success_final', $user_id, '', '', 'Customer Enter to National Draw Site', json_encode($response), __DIR__, basename(__FILE__), __LINE__);

              goto returnFVI;
            } else {


              // Log
              $log = Controller::error_log_new($request->ip(), 'redirect_to_failed', $user_id, '', '', 'Failed redirect ID: ' . $data["merchantOrderReference"], json_encode($request), __DIR__, basename(__FILE__), __LINE__);
              $data['redirectURL'] = ($request->header('Origin') . '/') . 'failed/';
              $response = ['status' => 'failed', 'message' => 'Network payment failed', 'error' => $data];
              goto returnFVI;
              // divert($baseurl . 'failed/');
            }
          } else {
            $response = ['status' => 'failed', 'message' => 'The transaction table update failed!', 'error' => 'The transaction table update failed!'];
            goto returnFVI;
          }
        } else {
          $response = ['status' => 'failed', 'message' => 'The transaction track not found!', 'error' => 'The transaction track not found!'];
          goto returnFVI;
        }
      } else {
        $response = ['status' => 'failed', 'message' => 'Payment Gateway not Responding!', 'error' => 'Token getting API failed!'];
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
