<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZAPI_KEY_ID'), env('RAZAPI_KEY_SECRET'));
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'required|numeric|min:1',
            'currency' => 'nullable|string|in:INR,USD,EUR'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $user = $request->user();
        $depositAmt = (float) $request->input('amount');
        $currency   = $request->input('currency', 'INR');
    
        // Convert to paise (Razorpay requirement)
        $amountInPaise = (int) round($depositAmt * 100);
    
        DB::beginTransaction();
    
        try {
            // Get last ID
            $lastPayment = DB::table('payment_history')
                ->select('id')
                ->orderBy('id', 'desc')
                ->first();
    
            $lastId = $lastPayment->id ?? 0;
    
            // Unique Transaction ID
            $tran_id = 'WD' . uniqid() . date('Hii') . ($lastId + 1);
    
            // Additional validation
            if ($depositAmt < 100) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Minimum top-up amount is 100 INR.',
                    'error'   => 'Minimum top-up amount is 100 INR.'
                ], 422);
            }
    
            if ($depositAmt > 10000) {
                return response()->json([
                    'status'  => 'failed',
                    'message' => 'Maximum top-up amount is 10,000 INR.',
                    'error'   => 'Maximum top-up amount is 10,000 INR.'
                ], 422);
            }
    
            $buildCheckOut = [
                'userID'          => $user->id,
                'depositAmt'      => $depositAmt,
                'existWalletAmt'  => number_format($user->walletBalance, 2),
                'finalTotal'      => $depositAmt,
                'discount'        => 0,
                'shipamount'      => 0,
                'grandtotal'      => $depositAmt,
                'shipping'        => 'pickUpToStore',
            ];
    
            $checkout_arr = [
                'createdon'          => now(),
                'crontime'           => now(),
                'ip'                 => $request->ip() ?? '',
                'user_id'            => $user->id,
                'status'             => '0',
                'transaction_id'     => $tran_id,
                'checkout_response'  => json_encode($buildCheckOut),
                'category'           => 'WalletDeposit',
                'gateway'            => 'razorpay',
                'finaltotal'         => $depositAmt,
                'receipt_no'         => '',
                'reference'          => '',
                // 'renewalStatus'      => 'RECHARGE',
                'paymentStatus'      => 'initiated',
                'shipamount'         => 0,
                'grandtotal'         => $depositAmt,
            ];
    
            $paymentId = DB::table('payment_history')->insertGetId($checkout_arr);
    
            $orderData = [
                'receipt'         => $tran_id,
                'amount'          => $amountInPaise,
                'currency'        => $currency,
                'payment_capture' => 1
            ];
    
            $razorpayOrder = $this->razorpay->order->create($orderData);
    
            // Update payment record with Razorpay order ID
            DB::table('payment_history')
                ->where('id', $paymentId)
                ->update(['receipt_no' => $razorpayOrder['id']]);
    
            DB::commit();
    
            return response()->json([
                'success'      => true,
                'order_id'     => $razorpayOrder['id'],
                'amount'       => $depositAmt,
                'currency'     => $currency,
                'razorpay_key' => env('RAZAPI_KEY_ID'),
                'tx_id'        => $tran_id
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
            \Log::error('Razorpay order creation failed: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => 'Unable to create order, try again later.',
                'error' => 'Unable to create order, try again later.'
            ], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'tx_id'               => 'required'
        ]);
    
        if ($validator->fails()) {
            
            return response()->json(['status' => false, 'message' => $validator->errors(), 'data' => []], 200);
        }
    
        $user      = $request->user();
        $paymentId = $request->razorpay_payment_id;
        $orderId   = $request->razorpay_order_id;
        $signature = $request->razorpay_signature;
        $trac_Id      = $request->tx_id;
        $txId      = null;
    
        // Fetch payment history row
        $tx = DB::table('payment_history')->where('transaction_id', $trac_Id)
            ->where('user_id', $user->id)
            ->first();
    
        if (!$tx) {
            
            return response()->json(['status' => false, 'message' => 'Transaction not found', 'data' => []], 200);
        }else{
            $txId = $tx->id;
        }
        
        $ch_tx = DB::table('payment_history')->where('id', $txId)
            ->where('user_id', $user->id)
            ->where('paymentStatus', 'success')
            ->first();
            
        $ch_wal = DB::table('walletBalance_history')->where('reference_id', $txId)
            ->where('userid', $user->id)
            ->first();
            
        if($ch_tx || $ch_wal){
            
            return response()->json(['status' => false, 'message' => 'Amount already credited', 'data' => []], 200);
        }
    
        // Decode checkout JSON
        $checkout = json_decode($tx->checkout_response, true);
        $expectedAmount = $checkout['finalTotal'] ?? 0;
    
        // Signature attributes
        $attributes = [
            'razorpay_order_id'   => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature'  => $signature
        ];
    
        // Verify razorpay signature
        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
    
            // Mark transaction failed
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus'        => 'failed',
                'razorpay_payment_id'  => $paymentId,
                'razorpay_signature'   => $signature,
                // 'updatedon'            => now()
            ]);
    
            // Log
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'failed',
                'pay_response'       => $tx->checkout_response,
                'response'           => $e->getMessage(),
                'createdon'         => now(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment signature',
                'data'    => []
            ]);
        }
    
        // Fetch payment details from Razorpay
        try {
            $payment = $this->razorpay->payment->fetch($paymentId);
        } catch (\Exception $e) {
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus'        => 'failed',
                'razorpay_payment_id'  => $paymentId,
                'razorpay_signature'   => $signature
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'failed',
                'pay_response'       => $tx->checkout_response,
                'response'           => $e->getMessage(),
                'createdon'         => now(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch payment details',
                'data'    => []
            ]);
        }
    
        // Validate Razorpay payment
        if (!isset($payment['status']) || $payment['status'] != 'captured') {
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus'      => 'failed',
                // 'meta'               => json_encode($payment),
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'failed',
                'pay_response'       => $tx->checkout_response,
                'response'           => json_encode($payment),
                'createdon'         => now(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Payment not captured',
                'data'    => []
            ]);
        }
    
        // Amount validation
        $paidAmount = $payment['amount'] / 100;
        
        // return $paidAmount - $expectedAmount;
    
        if (abs($paidAmount - $expectedAmount) > 0.01) {
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'failed'
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'failed',
                'pay_response'       => $tx->checkout_response,
                'response'           => json_encode($payment),
                'createdon' => now(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Paid amount mismatch',
                'data'    => []
            ]);
        }
    
        // CREDIT WALLET
        DB::beginTransaction();
        try {
    
            // Update transaction success
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus'       => 'success',
                'status'       => '1',
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
                // 'meta'                => json_encode($payment),
                // 'updatedon'           => now()
            ]);
            
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'success',
                'pay_response'       => $tx->checkout_response,
                'response'           => json_encode($payment),
                'createdon' => now(),
            ]);
    
            // Add wallet history
            $opening = $user->walletBalance;
            $closing = $opening + $expectedAmount;
    
            DB::table('walletBalance_history')->insert([
                'userid'            => $user->id,
                'uname'             => $user->name,
                'umobile'           => $user->mobile,
                'uemail'            => $user->email,
                'opening_balance'   => $opening,
                'total'             => $expectedAmount,
                'closeing_balance'  => $closing,
                'point_type'        => 'WALLET',
                'transaction_type'  => 'CREDIT',
                'reward_type'       => 'WalletDeposit',
                'reference_id'      => $txId,
                'reference_table'   => 'payment_history',
                'ip'                => $request->ip(),
                'createdon'         => now(),
                'updatedon'         => now()
            ]);
    
            // Update user wallet
            DB::table('user_register')->where('id', $user->id)->update([
                'walletBalance' => $closing
            ]);
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Wallet credited successfully',
                'data'    => ['walletBalance' => $closing]
            ]);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            DB::table('payment_history')->where('id', $txId)->update([
                'paymentStatus' => 'failed'
            ]);
    
            DB::table('payment_history_log')->insert([
                'payment_history_id' => $txId,
                'transaction_id'     => $tx->transaction_id,
                'gateway'            => 'razorpay',
                'user_id'            => $user->id,
                'paymentStatus'      => 'failed',
                'pay_response'       => $tx->checkout_response,
                'response'           => $e->getMessage(),
                'createdon'         => now(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to credit wallet',
                'data'    => []
            ]);
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

            if (intval($amount) >= 1000) {

                RE:
                $request_id = 'WR' . Str::random(15) . now()->format('His');
                $req_check = DB::table('withdraw_request')->where('request_id', $request_id)->count();
                if ($req_check > 0) {
                    goto RE;
                }

                $my_t_earn = auth()->user()->winningBalance;
                
                // return auth()->user();

                if (floatval($my_t_earn) >= floatval($amount)) {

                    $fromclose = floatval($my_t_earn) - floatval($amount);
                    $dob = auth()->user()->dob != NULL ? date('Y-m-d', strtotime(auth()->user()->dob)) : date('Y-m-d');



                    // $request->nationality = Controller::BlockSQLInjection(auth()->user()->nationality);
                    // if ($request->nationality == '' || $request->nationality == null || $request->nationality == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please use a valid nationality!', 'error' => 'Please use a valid nationality!'];
                    //     goto returnFVI;
                    // }




                    // $request->livein = Controller::BlockSQLInjection(auth()->user()->residinglocation);
                    // if ($request->livein == '' || $request->livein == null || $request->livein == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please use a valid livein!', 'error' => 'Please use a valid live in!'];
                    //     goto returnFVI;
                    // }



                    $nationlaity = null;
                    // $nationlaity = $request->nationality;

                    $livin = null;
                    // $livin = $request->livein;





                    $request->type = Controller::BlockSQLInjection($request->type);
                    if ($request->type == '' || $request->type == null || $request->type == 'null') {
                        $response = ['status' => 'failed', 'message' => 'Please use a valid type!', 'error' => 'Please use a valid type!'];
                        goto returnFVI;
                    }
                    
                    // return env('withDrawToEmail');

                    $toEmail = env('withDrawToEmail');
                    $ccAddress = array_map('trim', explode(',', env('withDrawCC')));
                    $toSubject = 'Withdraw Request Notification';

                    $htmlTemplate = '<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
   <meta charset="UTF-8">
   <meta content="width=device-width, initial-scale=1" name="viewport">
   <meta name="x-apple-disable-message-reformatting">
   <meta content="IE=edge" http-equiv="X-UA-Compatible">
   <meta content="telephone=no" name="format-detection">
   <title>Empty template</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Russo+One&display=swap"
      rel="stylesheet">
   <style type="text/css">
      .rollover:hover .rollover-first {
         max-height: 0px !important;
         display: none !important;
      }

      .rollover:hover .rollover-second {
         max-height: none !important;
         display: block !important;
      }

      .rollover span {
         font-size: 0px;
      }

      u+.body img~div div {
         display: none;
      }

      #outlook a {
         padding: 0;
      }

      span.MsoHyperlink,
      span.MsoHyperlinkFollowed {
         color: inherit;
         mso-style-priority: 99;
      }

      a.es-button {
         mso-style-priority: 100 !important;
         text-decoration: none !important;
      }

      a[x-apple-data-detectors],
      #MessageViewBody a {
         color: inherit !important;
         text-decoration: none !important;
         font-size: inherit !important;
         font-family: inherit !important;
         font-weight: inherit !important;
         line-height: inherit !important;
      }

      .es-desk-hidden {
         display: none;
         float: left;
         overflow: hidden;
         width: 0;
         max-height: 0;
         line-height: 0;
         mso-hide: all;
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
      <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none"
         style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
         <tr>
            <td valign="top" style="padding:0;Margin:0">
               <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none"
                  style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                  <tr>
                     <td align="center" style="padding:0;Margin:0">
                        <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body"
                           role="none"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table width="100%" cellpadding="0" cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr>
                                       <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                          <table cellspacing="0" width="100%" cellpadding="0" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0px"><a
                                                      href="' . ($request->header('Origin') . '/') . '" target="_blank"
                                                      style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img
                                                         alt=""
                                                         src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png"
                                                         width="405" class="adapt-img"
                                                         style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
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
               <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none"
                  style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                  <tr>
                     <td align="center" style="padding:0;Margin:0">
                        <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body"
                           role="none"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                          <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0;font-size:0px;"><img
                                                      width="560" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/withrwan_request_img.png"
                                                      class="adapt-img"
                                                      style="display:block;font-size:14px;border:0;outline:none;text-decoration:none; width: 50%;">
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0">
                                                   <table cellspacing="0" height="100%" width="100%" border="0"
                                                      cellpadding="0" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tr>
                                                         <td
                                                            style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                                         </td>
                                                      </tr>
                                                   </table>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:10px 0;Margin:0">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:28px">
                                                      Hi Team</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:28px">
                                                      Withdrawal Request</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0">
                                                   <table cellspacing="0" height="100%" width="100%" border="0"
                                                      cellpadding="0" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tbody>
                                                         <tr>
                                                            <td
                                                               style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td class="esd-structure es-p20" align="left" style="padding:20px;">
                                                   <table style="border: 1px solid #d0dae7;width: 100%;">
                                                      <tbody>
                                                         <tr style="background-color: #d0dae7;">
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Name</th>
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Amount</th>
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Request ID</th>
                                                         </tr>
                                                         <tr>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               ' . ucwords(strtolower(auth()->user()->name . ' ' . (auth()->user()->lname ?? ''))) . '</td>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               INR ' . $amount . '</td>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               ' . $request_id . '</td>
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
                              <td align="center" style="padding:20px;Margin:0;font-size:0">
                                 <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0"
                                    role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tbody>
                                       <tr>
                                          <td
                                             style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                 <table width="100%" cellpadding="0" cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                          <table bgcolor="#002d72" cellpadding="0" cellspacing="0" width="100%"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#002d72"
                                             role="presentation">
                                             <tr>
                                                <td align="center" bgcolor="#002d72"
                                                   style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">
                                                      Need Help? Visit us at www.goride.run</p>
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">
                                                      call or whatsapp on +91 6369742104, email at support@goride.run</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center"
                                                   style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                   <table cellpadding="0" cellspacing="0" dir="ltr"
                                                      class="es-table-not-adapt es-social" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tr>
                                                         <td align="center" valign="top"
                                                            style="padding:0;Margin:0;padding-right:10px"><img
                                                               width="32" alt="Fb" height="32"
                                                               src="nationaldraw/1/emailFB.png"
                                                               title="Facebook"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
                                                         </td>
                                                         <td align="center" valign="top"
                                                            style="padding:0;Margin:0;padding-right:10px"><img
                                                               title="YouTube" width="32" alt="Yt" height="32"
                                                               src="nationaldraw/1/emailYT.png"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
                                                         </td>
                                                         <td align="center" valign="top" style="padding:0;Margin:0"><img
                                                               title="Instagram" width="32" alt="Ig" height="32"
                                                               src="nationaldraw/1/emailIG.png"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
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
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                          <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">
                                                      Note: This is a system auto-generated email. Please do not reply
                                                      to this mail.</p>
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
                    

                    $idProFront = null;
                    // $idProFront = Controller::BlockSQLInjection(auth()->user()->idProFront);
                    // if ($idProFront == '' || $idProFront == null || $idProFront == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please upload the front image of your ID proof.', 'error' => 'Please upload the front image of your ID proof.'];
                    //     goto returnFVI;
                    // }

                    $idProBack = null;
                    // $idProBack = Controller::BlockSQLInjection(auth()->user()->idProBack);
                    // if ($idProBack == '' || $idProBack == null || $idProBack == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please upload the back image of your ID proof.', 'error' => 'Please upload the back image of your ID proof.'];
                    //     goto returnFVI;
                    // }

                    if ($request->type == 'BANK') {

                        $request->accHolderName = Controller::BlockSQLInjection(auth()->user()->account_name);
                        if ($request->accHolderName == '' || $request->accHolderName == null || $request->accHolderName == 'null') {
                            $response = ['status' => 'failed', 'message' => 'Please use a valid account holder name!', 'error' => 'Please use a valid account holder name!'];
                            goto returnFVI;
                        }

                        $request->upi = Controller::BlockSQLInjection(auth()->user()->upiID);
                        if ($request->upi == '' || $request->upi == null || $request->upi == 'null') {
                            $response = ['status' => 'failed', 'message' => 'Please use a valid upi ID!', 'error' => 'Please use a valid upi ID!'];
                            goto returnFVI;
                        }

                        // $request->accType = Controller::BlockSQLInjection(auth()->user()->acctype);
                        // if ($request->accType == '' || $request->accType == null || $request->accType == 'null') {
                        //     $response = ['status' => 'failed', 'message' => 'Please use a valid account type!', 'error' => 'Please use a valid account type!'];
                        //     goto returnFVI;
                        // }

                        // // $request->bankName = Controller::BlockSQLInjection(auth()->user()->bank_name);
                        // if ($request->bankName == '' || $request->bankName == null || $request->bankName == 'null') {
                        //     $response = ['status' => 'failed', 'message' => 'Please use a valid bank name!', 'error' => 'Please use a valid bank name!'];
                        //     goto returnFVI;
                        // }

                        // // $request->branchName = Controller::BlockSQLInjection(auth()->user()->branch_name);
                        // if ($request->branchName == '' || $request->branchName == null || $request->branchName == 'null') {
                        //     $response = ['status' => 'failed', 'message' => 'Please use a valid branch name!', 'error' => 'Please use a valid branch name!'];
                        //     goto returnFVI;
                        // }

                        // if ($request->branchCode != '') {
                        //     // $request->branchCode = Controller::BlockSQLInjection(auth()->user()->branch_code);
                        //     if ($request->branchCode == '' || $request->branchCode == null || $request->branchCode == 'null') {
                        //         $response = ['status' => 'failed', 'message' => 'Please use a valid branch code!', 'error' => 'Please use a valid branch code!'];
                        //         goto returnFVI;
                        //     }
                        // } else {
                        //     $request->branchCode = '';
                        // }

                        $request->currencyCode = 'INR';

                        if (
                            isset($request->type) && $request->type !== null &&
                            isset($request->withdrawAmt) && $request->withdrawAmt !== null &&
                            isset($request->accHolderName) && $request->accHolderName !== null &&
                            isset($request->currencyCode) && $request->currencyCode !== null
                        ) {
                            

                            $withdraw_arr = [
                                "dob" => null,
                                "img_url" => '',
                                "currencyccode" => $request->currencyCode??null,
                                "swiftcode" => null,
                                "acctype" => null,
                                "achname" => $request->accHolderName??null,
                                "cus_prefer" => strtolower($request->type),
                                "iban_code" => "",
                                "bank_name" => null,
                                "upiID" => $request->upi,
                                "branch_name" => null,
                                "branch_code" => null,
                                "emirites_passport" => null,
                                "acc_no" => null,
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
                                
                                $payArr = [
                                    "userid" => $user_id,
                                    "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                                    "umobile" => auth()->user()->mobile,
                                    "uemail" => auth()->user()->email,
                                    'opening_balance' => $my_t_earn,
                                    'total' => $amount,
                                    'closeing_balance' => $fromclose,
                                    'point_type' => 'WINNING',
                                    'transaction_type' => 'DEBIT',
                                    'upiID' => $request->upi,
                                    'card_no' => '',
                                    'reference_id' => $with_draw_ins,
                                    'reference_table' => 'withdraw_request',
                                    'ip' => ($request->ip() ?? ''),
                                    'reward_type' => 'BANKWITHDRAWAL'
                                ];

                                $wallet_history = DB::table('winningBalance_history')->insert($payArr);

                                if ($wallet_history) {
                                    $updateUser = DB::table('user_register')
                                        ->where('id', $user_id)
                                        ->where('deletes', '0')
                                        ->update([
                                            'updated_at' => now(),
                                            'winningBalance' => $fromclose,
                                            'lastlogin' => now()
                                        ]);

                                    if ($updateUser) {
                        
                                        $emailSend = Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', []);

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

                                $payArr = [
                                    "userid" => $user_id,
                                    "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                                    "umobile" => auth()->user()->mobile,
                                    "uemail" => auth()->user()->email,
                                    'opening_balance' => $my_t_earn,
                                    'total' => $amount,
                                    'closeing_balance' => $fromclose,
                                    'point_type' => 'WINNING',
                                    'transaction_type' => 'DEBIT',
                                    'card_no' => '',
                                    'reference_id' => $with_draw_ins,
                                    'reference_table' => 'withdraw_request',
                                    'ip' => ($request->ip() ?? ''),
                                    'reward_type' => 'EXCHANGEWITHDRAWAL'
                                ];

                                $wallet_history = DB::table('winningBalance_history')->insert($payArr);

                                if ($wallet_history) {
                                    $updateUser = DB::table('user_register')
                                        ->where('id', $user_id)
                                        ->where('deletes', '0')
                                        ->update([
                                            'updated_at' => now(),
                                            'winningBalance' => $fromclose,
                                            'lastlogin' => now()
                                        ]);

                                    if ($updateUser) {
                                        
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

                    $response = ['status' => 'failed', 'message' => 'Maximum Withdrawal amount is INR ' . floatval($my_t_earn) . '.', 'error' => 'Maximum Withdrawal amount is INR ' . floatval($my_t_earn) . '.'];
                    goto returnFVI;
                }
            } else {
                // dd('Test');
                $response = ['status' => 'failed', 'message' => 'Minimum Withdrawal amount is INR 1000.00', 'error' => 'Minimum Withdrawal amount is INR 1000.00'];
                goto returnFVI;
            }



            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function withdrawRequestCustomer(Request $request)
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
            
            // $max_amt = auth()->user()->walletBalance;
            
            // if((intval($amount) > 500)){
            //      $response = ['status' => 'failed', 'message' => 'Maximum Withdrawal amount is INR 500.00', 'error' => 'Maximum Withdrawal amount is INR 500.00'];
            //     goto returnFVI;
            // }
            
            $walletBalance = auth()->user()->walletBalance;
            
            if ($amount > $walletBalance) {
                $response = [
                    'status'  => 'failed',
                    'message' => 'Withdrawal amount exceeds available wallet balance.',
                    'error'   => 'Withdrawal amount exceeds available wallet balance.'
                ];
                goto returnFVI;
            }
            

            if (intval($amount) >= 500) {

                RE:
                $request_id = 'WRC-' . Str::random(15) . now()->format('His');
                $req_check = DB::table('withdraw_request')->where('request_id', $request_id)->count();
                if ($req_check > 0) {
                    goto RE;
                }

                $my_t_earn = auth()->user()->walletBalance;

                if (floatval($my_t_earn) >= floatval($amount)) {

                    $fromclose = floatval($my_t_earn) - floatval($amount);
                    $dob = auth()->user()->dob != NULL ? date('Y-m-d', strtotime(auth()->user()->dob)) : date('Y-m-d');

                    $nationlaity = null;

                    $livin = null;

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
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
   <meta charset="UTF-8">
   <meta content="width=device-width, initial-scale=1" name="viewport">
   <meta name="x-apple-disable-message-reformatting">
   <meta content="IE=edge" http-equiv="X-UA-Compatible">
   <meta content="telephone=no" name="format-detection">
   <title>Empty template</title>
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Russo+One&display=swap"
      rel="stylesheet">
   <style type="text/css">
      .rollover:hover .rollover-first {
         max-height: 0px !important;
         display: none !important;
      }

      .rollover:hover .rollover-second {
         max-height: none !important;
         display: block !important;
      }

      .rollover span {
         font-size: 0px;
      }

      u+.body img~div div {
         display: none;
      }

      #outlook a {
         padding: 0;
      }

      span.MsoHyperlink,
      span.MsoHyperlinkFollowed {
         color: inherit;
         mso-style-priority: 99;
      }

      a.es-button {
         mso-style-priority: 100 !important;
         text-decoration: none !important;
      }

      a[x-apple-data-detectors],
      #MessageViewBody a {
         color: inherit !important;
         text-decoration: none !important;
         font-size: inherit !important;
         font-family: inherit !important;
         font-weight: inherit !important;
         line-height: inherit !important;
      }

      .es-desk-hidden {
         display: none;
         float: left;
         overflow: hidden;
         width: 0;
         max-height: 0;
         line-height: 0;
         mso-hide: all;
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
      <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none"
         style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
         <tr>
            <td valign="top" style="padding:0;Margin:0">
               <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none"
                  style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                  <tr>
                     <td align="center" style="padding:0;Margin:0">
                        <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body"
                           role="none"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table width="100%" cellpadding="0" cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                    <tr>
                                       <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                          <table cellspacing="0" width="100%" cellpadding="0" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0px"><a
                                                      href="' . ($request->header('Origin') . '/') . '" target="_blank"
                                                      style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img
                                                         alt=""
                                                         src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png"
                                                         width="405" class="adapt-img"
                                                         style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a>
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
               <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none"
                  style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                  <tr>
                     <td align="center" style="padding:0;Margin:0">
                        <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body"
                           role="none"
                           style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                          <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0;font-size:0px;"><img
                                                      width="560" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/withrwan_request_img.png"
                                                      class="adapt-img"
                                                      style="display:block;font-size:14px;border:0;outline:none;text-decoration:none; width: 50%;">
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0">
                                                   <table cellspacing="0" height="100%" width="100%" border="0"
                                                      cellpadding="0" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tr>
                                                         <td
                                                            style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                                         </td>
                                                      </tr>
                                                   </table>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:10px 0;Margin:0">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:28px">
                                                      Hi Team</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:28px">
                                                      Withdrawal Request</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center" style="padding:20px;Margin:0;font-size:0">
                                                   <table cellspacing="0" height="100%" width="100%" border="0"
                                                      cellpadding="0" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tbody>
                                                         <tr>
                                                            <td
                                                               style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                                            </td>
                                                         </tr>
                                                      </tbody>
                                                   </table>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td class="esd-structure es-p20" align="left" style="padding:20px;">
                                                   <table style="border: 1px solid #d0dae7;width: 100%;">
                                                      <tbody>
                                                         <tr style="background-color: #d0dae7;">
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Name</th>
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Amount</th>
                                                            <th
                                                               style="padding: 10px; text-align: center; font-weight: bold; font-family: Arial, sans-serif; color: #171f4f;">
                                                               Request ID</th>
                                                         </tr>
                                                         <tr>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               ' . ucwords(strtolower(auth()->user()->name . ' ' . (auth()->user()->lname ?? ''))) . '</td>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               INR ' . $amount . '</td>
                                                            <td
                                                               style="padding: 10px; text-align: center; font-family: Arial, sans-serif; color: #171f4f;">
                                                               ' . $request_id . '</td>
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
                              <td align="center" style="padding:20px;Margin:0;font-size:0">
                                 <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0"
                                    role="presentation"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tbody>
                                       <tr>
                                          <td
                                             style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset">
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                 <table width="100%" cellpadding="0" cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                          <table bgcolor="#002d72" cellpadding="0" cellspacing="0" width="100%"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#002d72"
                                             role="presentation">
                                             <tr>
                                                <td align="center" bgcolor="#002d72"
                                                   style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">
                                                      Need Help? Visit us at www.goride.run</p>
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">
                                                      call or whatsapp on +91 6369742104, email at support@goride.run</p>
                                                </td>
                                             </tr>
                                             <tr>
                                                <td align="center"
                                                   style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                   <table cellpadding="0" cellspacing="0" dir="ltr"
                                                      class="es-table-not-adapt es-social" role="presentation"
                                                      style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                      <tr>
                                                         <td align="center" valign="top"
                                                            style="padding:0;Margin:0;padding-right:10px"><img
                                                               width="32" alt="Fb" height="32"
                                                               src="nationaldraw/1/emailFB.png"
                                                               title="Facebook"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
                                                         </td>
                                                         <td align="center" valign="top"
                                                            style="padding:0;Margin:0;padding-right:10px"><img
                                                               title="YouTube" width="32" alt="Yt" height="32"
                                                               src="nationaldraw/1/emailYT.png"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
                                                         </td>
                                                         <td align="center" valign="top" style="padding:0;Margin:0"><img
                                                               title="Instagram" width="32" alt="Ig" height="32"
                                                               src="nationaldraw/1/emailIG.png"
                                                               style="display:block;font-size:14px;border:0;outline:none;text-decoration:none">
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
                           <tr>
                              <td align="left"
                                 style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                 <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                    <tr>
                                       <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                          <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                             style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                             <tr>
                                                <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                   <p
                                                      style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">
                                                      Note: This is a system auto-generated email. Please do not reply
                                                      to this mail.</p>
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
                    

                    $idProFront = null;
                    // $idProFront = Controller::BlockSQLInjection(auth()->user()->idProFront);
                    // if ($idProFront == '' || $idProFront == null || $idProFront == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please upload the front image of your ID proof.', 'error' => 'Please upload the front image of your ID proof.'];
                    //     goto returnFVI;
                    // }

                    $idProBack = null;
                    // $idProBack = Controller::BlockSQLInjection(auth()->user()->idProBack);
                    // if ($idProBack == '' || $idProBack == null || $idProBack == 'null') {
                    //     $response = ['status' => 'failed', 'message' => 'Please upload the back image of your ID proof.', 'error' => 'Please upload the back image of your ID proof.'];
                    //     goto returnFVI;
                    // }

                    if ($request->type == 'BANK' || $request->type == 'UPI') {

                        // $request->accHolderName = Controller::BlockSQLInjection(auth()->user()->account_name);
                        if ($request->accHolderName == '' || $request->accHolderName == null || $request->accHolderName == 'null') {
                            $response = ['status' => 'failed', 'message' => 'Please use a valid account holder name!', 'error' => 'Please use a valid account holder name!'];
                            goto returnFVI;
                        }

                        $request->currencyCode = 'INR';

                        if (
                            isset($request->type) && $request->type != null &&
                            isset($request->upi) && $request->upi != null &&
                            isset($request->withdrawAmt) && $request->withdrawAmt != null &&
                            isset($request->accHolderName) && $request->accHolderName != null &&
                            isset($request->currencyCode) && $request->currencyCode != null
                        ) {
                            

                            $withdraw_arr = [
                                "dob" => null,
                                "img_url" => '',
                                "currencyccode" => $request->currencyCode??null,
                                "swiftcode" => null,
                                "acctype" => null,
                                "achname" => $request->accHolderName??null,
                                "cus_prefer" => strtolower($request->type),
                                "iban_code" => "",
                                "bank_name" => null,
                                "upiID" => $request->upi,
                                "branch_name" => null,
                                "branch_code" => null,
                                "emirites_passport" => null,
                                "acc_no" => null,
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
                                
                                $payArr = [
                                    "userid" => $user_id,
                                    "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                                    "umobile" => auth()->user()->mobile,
                                    "uemail" => auth()->user()->email,
                                    'opening_balance' => $my_t_earn,
                                    'total' => $amount,
                                    'global_type' => 'customer',
                                    'closeing_balance' => $fromclose,
                                    'point_type' => 'WINNING',
                                    'transaction_type' => 'DEBIT',
                                    'upiID' => $request->upi,
                                    'card_no' => '',
                                    'reference_id' => $with_draw_ins,
                                    'reference_table' => 'withdraw_request',
                                    'ip' => ($request->ip() ?? ''),
                                    'reward_type' => 'BANKWITHDRAWAL'
                                ];

                                $wallet_history = DB::table('winningBalance_history')->insert($payArr);

                                if ($wallet_history) {
                                    // $updateUser = DB::table('customer_register')
                                    //     ->where('id', $user_id)
                                    //     ->where('deletes', '0')
                                    //     ->update([
                                    //         'updated_at' => now(),
                                    //         'winningBalance' => $amount,
                                    //         'walletBalance' => DB::raw("walletBalance - {$amount}"),
                                    //         'lastlogin' => now()
                                    //     ]);
                                        
                                    DB::table('customer_register')
                                        ->where('id', $user_id)
                                        ->where('deletes', '0')
                                        ->decrement('walletBalance', $amount);
                                    
                                    $updateUser = DB::table('customer_register')
                                        ->where('id', $user_id)
                                        ->where('deletes', '0')
                                        ->update([
                                            'updated_at' => now(),
                                            'winningBalance' => $amount,
                                            'lastlogin' => now()
                                        ]);

                                    if ($updateUser) {
                        
                                        $emailSend = Controller::composeEmail($request->ip(), $toEmail, $toSubject, $htmlTemplate, '', []);

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

                                $payArr = [
                                    "userid" => $user_id,
                                    "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                                    "umobile" => auth()->user()->mobile,
                                    "uemail" => auth()->user()->email,
                                    'opening_balance' => $my_t_earn,
                                    'total' => $amount,
                                    'closeing_balance' => $fromclose,
                                    'point_type' => 'WINNING',
                                    'transaction_type' => 'DEBIT',
                                    'card_no' => '',
                                    'reference_id' => $with_draw_ins,
                                    'reference_table' => 'withdraw_request',
                                    'ip' => ($request->ip() ?? ''),
                                    'reward_type' => 'EXCHANGEWITHDRAWAL'
                                ];

                                $wallet_history = DB::table('winningBalance_history')->insert($payArr);

                                if ($wallet_history) {
                                    $updateUser = DB::table('user_register')
                                        ->where('id', $user_id)
                                        ->where('deletes', '0')
                                        ->update([
                                            'updated_at' => now(),
                                            'winningBalance' => $fromclose,
                                            'lastlogin' => now()
                                        ]);

                                    if ($updateUser) {
                                        
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
                    
                } else {

                    $response = ['status' => 'failed', 'message' => 'Maximum Withdrawal amount is INR ' . floatval($my_t_earn) . '.', 'error' => 'Maximum Withdrawal amount is INR ' . floatval($my_t_earn) . '.'];
                    goto returnFVI;
                }
            } else {
                // dd('Test');
                $response = ['status' => 'failed', 'message' => 'Minimum Withdrawal amount is INR 500.00', 'error' => 'Minimum Withdrawal amount is INR 500.00'];
                goto returnFVI;
            }



            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
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



            $winningAmt = intval($request->winningAmt);
            if ($winningAmt == '' || $winningAmt == null || $winningAmt == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid withdraw amount!', 'error' => 'Please use a valid withdraw amount!'];
                // $response = ['status' => 'failed', 'message' => 'Please provide a valid winning ID.', 'error' => 'Please provide a valid winning ID.'];
                goto returnFVI;
            }

            if (intval($winningAmt) < 1) {
                $response = [
                    'status' => 'failed',
                    'message' => "The minimum transfer amount is INR 1.",
                    'error' => "The minimum transfer amount is INR 1."
                ];
                goto returnFVI;
            }

            $winningBalance = auth()->user()->winningBalance;
            $finaltotalWinning = $winningBalance - $winningAmt;



            if (floatval($winningBalance) >= floatval($winningAmt)) {
                // dd($finaltotalWinning);

                $payArr = [
                    "userid" => $user_id,
                    "uname" => auth()->user()->name . ' ' . (auth()->user()->lname ?? ''),
                    "umobile" => auth()->user()->mobile,
                    "uemail" => auth()->user()->email,
                    'opening_balance' => $winningBalance,
                    'total' => $winningAmt,
                    'closeing_balance' => $finaltotalWinning,
                    'point_type' => 'WINNING',
                    'transaction_type' => 'DEBIT',
                    'card_no' => '',
                    'reference_id' => 0,
                    'reference_table' => 'walletBalance_history',
                    'ip' => ($request->ip() ?? ''),
                    'reward_type' => 'WALLETTRANSFER'
                ];

                $winningHistory = DB::table('winningBalance_history')->insertGetId($payArr);

                if ($winningHistory && $winningHistory != '') {
                    
                    $walletBalance = auth()->user()->walletBalance;
                    $totalAmt = $winningAmt;
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
                        'reference_id' => $winningHistory,
                        'reference_table' => 'winningBalance_history',
                        'ip' => ($request->ip() ?? ''),
                        'reward_type' => (isset($request->requestType) ? $request->requestType : 'WALLETTRANSFER'),
                        'createdon' => now(),
                        'updatedon' => now()
                    ];

                    $wallet_history = DB::table('walletBalance_history')->insertGetId($payArr);

                    if ($wallet_history) {
                        
                        $updateUser = DB::table('user_register')
                            ->where('id', auth()->user()->id)
                            ->where('deletes', '0')
                            ->where('roll_id', '0')
                            ->where('status', '0')
                            ->update([
                                'walletBalance' => $finaltotal,
                                'winningBalance' => $finaltotalWinning,
                                'lastlogin' => now()
                            ]);


                        $updateWinningHistory = DB::table('winningBalance_history')
                            ->where('id', $winningHistory)
                            ->where('userid', auth()->user()->id)
                            ->where('deletes', '0')
                            // ->where('roll_id', '0')
                            // ->where('status', '0')
                            ->update([
                                'reference_id' => $wallet_history
                                // 'walletBalance' => $finaltotal,
                                // 'winningBalance' => $finaltotalWinning,
                                // 'lastlogin' => now()
                            ]);


                        if ($updateUser && $updateWinningHistory) {


                            $response = [
                                'status' => 'success',
                                'message' => 'The amount transferred successfully.',
                                'data' => 'The amount transferred successfully.'
                            ];
                            goto returnFVI;
                            
                        } else {
                            $response = ['status' => 'failed', 'message' => 'Wallet update process Failed.', 'error' => 'Wallet update process Failed.'];
                            goto returnFVI;
                        }
                    } else {
                        $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
                        goto returnFVI;
                    }
                } else {
                    $response = ['status' => 'failed', 'message' => 'Wallet Transaction log Failed.', 'error' => 'Wallet Transaction log Failed.'];
                    goto returnFVI;
                }
            } else {
                $response = ['status' => 'failed', 'message' => 'Maximum transfer amount is INR ' . floatval($winningBalance) . '.', 'error' => 'Maximum Withdrawal amount is INR ' . floatval($winningBalance) . '.'];
                goto returnFVI;
            }



            // $winners = DB::table('winnerlist as w')
            //     ->select(
            //         'w.id as winningID',
            //         'w.draw_id',
            //         'w.userid',
            //         'w.drawType',
            //         'w.winningDrawName',
            //         'w.winRaffleId',
            //         'w.email',
            //         'w.mobile',
            //         'w.ticketID',
            //         'w.ticketReferenceID',
            //         'w.prize',
            //         // DB::raw('CAST(w.prize_amt AS CHAR) AS prize_amt'),
            //         'w.prize_amt',
            //         'w.country',
            //         'w.residingcountry',
            //         'w.createdon as winningTime',
            //         'd.resultDate',
            //         'nd.ticketNo',
            //         'nd.netTotal',
            //         DB::raw("CASE WHEN nd.netTotal >= 360 THEN 'true' ELSE 'false' END AS fullyPaid"),
            //         'w.requestType',
            //         DB::raw("(360 - nd.netTotal) AS balanceRenewalAmt")
            //     )
            //     ->leftJoin('ndticket as nd', 'nd.id', '=', 'w.ticketID')
            //     ->leftJoin('draw as d', 'd.id', '=', 'w.draw_id')
            //     ->where('w.deletes', '0')
            //     ->where('w.id', $winningID)
            //     ->where('w.userid', $user_id)
            //     ->where('w.requestType', null)
            //     ->orderByDesc('winningTime')
            //     ->get();

            // if ($winners->count() < 1) {
            //     $response = ['status' => 'failed', 'message' => 'The Track not found.', 'error' => 'The Track not found.'];
            //     goto returnFVI;
            // }





            // $winners = $winners[0];

            // $checkWalletHis = DB::table('wallet_history')->where(
            //     [
            //         ['deletes', '=', '0'],
            //         ['status', '=', '0'],
            //         ['reference_id', '=', $winners->winningID],
            //         ['reference_table', '=', 'winnerlist']
            //     ]
            // )->limit(1)->get();

            // if ($checkWalletHis->count() > 0) {
            //     $response = ['status' => 'failed', 'message' => 'The transaction found!', 'error' => 'The transaction found!'];
            //     goto returnFVI;
            // }

            // dd($winners->fullyPaid);
            // die;

            // if (isset($request->fullyPaid) && $request->fullyPaid) {
            //   $winners->fullyPaid === 'true';
            // }

            // if (!isset($request->fullyPaid) && $winners->fullyPaid === 'false') {
            //     $response = [
            //         'status' => 'failed',
            //         'message' => 'Please renew your ticket before trying again.',
            //         'error' => 'Please renew your ticket before trying again.'
            //     ];

            //     goto returnFVI;
            // }


            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    // public function walletTransaction(Request $request)
    // {
    //     try {

    //         $response = [];
    //         $data = [];

    //         // Get User ID
    //         $user_id = auth()->user()->id;
    //         if ($user_id == '' || $user_id == null || $user_id == 'null') {
    //             $response = ['status' => 'failed', 'message' => 'Authentication Required', 'error' => 'Please provide a valid access token.'];
    //             goto returnFVI;
    //         }
            
    //         $history = DB::table(function ($query) use ($user_id) {

    //             $query->select(
    //                     'ph.id as ref_id',
    //                     'ph.transaction_id',
    //                     'ph.razorpay_payment_id',
    //                     'ph.grandtotal as amount',
    //                     'ph.category',
    //                     'ph.paymentStatus',
    //                     'ph.createdon',
    //                     DB::raw("'PAYMENT' as source")
    //                 )
    //                 ->from('payment_history as ph')
    //                 // ->join('payment_history_log as phl', 'phl.payment_history_id', '=', 'ph.id')
    //                 ->join('user_register as ur', 'ur.id', '=', 'ph.user_id')
    //                 ->where('ph.user_id', $user_id)
    //                 ->where('ph.gateway', 'razorpay')
    //                 ->where('ur.deletes', '0')
            
    //             ->unionAll(
    //                 DB::table('winningBalance_history as wh')
    //                     ->select(
    //                         'wh.id as ref_id',
    //                         DB::raw("NULL as transaction_id"),
    //                         DB::raw("NULL as razorpay_payment_id"),
    //                         'wh.total as amount',
    //                         'wh.reward_type as category',
    //                         DB::raw("'Success' as paymentStatus"),
    //                         'wh.createdon',
    //                         DB::raw("'WALLET' as source")
    //                     )
    //                     ->join('user_register as ur', 'ur.id', '=', 'wh.userid')
    //                     ->where('wh.userid', $user_id)
    //                     ->where('ur.deletes', '0')
    //                     ->whereIn('wh.reward_type', ['BANKWITHDRAWAL', 'WALLETTRANSFER'])
    //             );
            
    //         })
    //         ->orderBy('createdon', 'desc')
    //         ->get();
            
    //         $result = [
    //             'status' => true,
    //             'data' => [
    //                 'wallet_balance' => auth()->user()->walletBalance,
    //                 'earning_balance' => auth()->user()->winningBalance,
    //                 'transactions' => $history
    //             ],
    //             'message' => 'Transaction received'
    //         ];
            
    //         returnFVI:
    //         return response()->json($result);
    //     } catch (Exception $e) {

    //         $response = ['status' => 'failed','data' => [], 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
    //         return response()->json($response);
    //     }
    // }
    
    public function walletTransaction(Request $request)
    {
        try {
    
            $user = auth()->user();
    
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'message' => 'Authentication Required',
                    'error' => 'Please provide a valid access token.'
                ], 401);
            }
    
            $user_id = $user->id;
    
            // Wallet History
            $walletQuery = DB::table('walletBalance_history as wb')
                ->select(
                    'wb.id as ref_id',
                    DB::raw("NULL as transaction_id"),
                    'wb.reference_id',
                    DB::raw("
                        CASE 
                            WHEN wb.reward_type = 'JOB' 
                                 AND wb.transaction_type = 'DEBIT'
                            THEN CONCAT('-', wb.total)
                            ELSE wb.total
                        END as amount
                    "),
                    
                    DB::raw("
                        CASE 
                            WHEN wb.reward_type = 'JOB' 
                                 AND wb.transaction_type = 'DEBIT'
                            THEN 'WalletDeduct'
                            
                            WHEN wb.reward_type = 'JOB' 
                                 AND wb.transaction_type = 'REFUND'
                            THEN 'REFUND'
                            
                            ELSE wb.reward_type
                        END as category
                    "),
                    DB::raw("'Success' as paymentStatus"),
                    'wb.createdon',
                    DB::raw("'PAYMENT' as source")
                )
                ->join('user_register as ur', 'ur.id', '=', 'wb.userid')
                ->where('wb.userid', $user_id)
                ->whereNull('wb.global_type')
                ->where('ur.deletes', '0');
    
            // Winning Balance History
            $winningQuery = DB::table('winningBalance_history as wh')
                ->select(
                    'wh.id as ref_id',
                    DB::raw("NULL as transaction_id"),
                    DB::raw("NULL as reference_id"),
                    'wh.total as amount',
                    'wh.reward_type as category',
                    DB::raw("'Success' as paymentStatus"),
                    'wh.createdon',
                    DB::raw("'WALLET' as source")
                )
                ->join('user_register as ur', 'ur.id', '=', 'wh.userid')
                ->where('wh.userid', $user_id)
                ->where('ur.deletes', '0')
                ->whereIn('wh.reward_type', [
                    'BANKWITHDRAWAL',
                    'WALLETTRANSFER'
                ]);
    
            // Merge Queries
            $history = $walletQuery
                ->unionAll($winningQuery);
    
            // Final Result
            $transactions = DB::query()
                ->fromSub($history, 'history')
                ->orderBy('createdon', 'desc')
                ->get();
    
            return response()->json([
                'status' => true,
                'data' => [
                    'wallet_balance' => $user->walletBalance,
                    'earning_balance' => $user->winningBalance,
                    'transactions' => $transactions
                ],
                'message' => 'Transaction received'
            ]);
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => 'Exception occurred',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            ], 500);
        }
    }
    
    public function walletTransactionCustomer(Request $request)
    {
        try {
    
            $user = auth()->user();
    
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Authentication Required',
                    'error'   => 'Please provide a valid access token.'
                ], 401);
            }
    
            // Optional: Prevent deleted users from accessing
            if (isset($user->deletes) && $user->deletes != '0') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Account not active'
                ], 403);
            }
    
            $history = DB::table('withdraw_request as wh')
                ->select(
                    'wh.request_id as ref_id',
                    'wh.achname as name',
                    'wh.amount',
                    'wh.cus_prefer as category',
                    'wh.status',
                    'wh.createdon'
                )
                ->where('wh.requested_by', $user->id)
                ->where(function ($query) {
                    $query->where('wh.request_id', 'like', 'WRC-%');
                })
                ->orderByDesc('wh.createdon')
                ->paginate(20);
    
            return response()->json([
                'status'  => true,
                'data'    => [
                    'wallet_balance' => $user->walletBalance ?? 0,
                    'credit_point'   => $user->cash_points ?? 0,
                    'transactions'   => $history
                ],
                'message' => 'Transaction received'
            ]);
    
        } catch (\Throwable $e) {
    
            \Log::error('Wallet Transaction Error', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage()
            ]);
    
            return response()->json([
                'status'  => false,
                'data'    => [],
                'message' => 'Something went wrong',
                'error'   => 'Internal Server Error'
            ], 500);
        }
    }

}