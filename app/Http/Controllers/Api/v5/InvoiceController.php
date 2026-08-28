<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use App\Http\Controllers\NumberController;
// use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{

    public function invoiceView(Request $request)
    {
        try {
            $numberController = new NumberController();
            $response = [];

            $data = [];

            $transaction_id = $request->transaction_id;
            $transaction_id = Controller::BlockSQLInjection($transaction_id);
            if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid transaction ID.', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }

            $invoice = DB::table('invoice')
                ->where('payment_transaction_id', '=', $transaction_id)
                ->orWhere('ticketReferenceID', '=', $transaction_id)
                ->where('deletes', '=', '0')
                ->orderBy('id', 'DESC')
                // ->limit(1)
                ->get();

            if ($invoice->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'Invoice not found!', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }


            foreach ($invoice as $key => $value) {

                $paymentHistory = DB::table('payment_history')
                    ->where('id', '=', $value->payment_history_id)
                    ->where('status', '1')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get();

                if ($paymentHistory->count() < 1) {
                    $response = ['status' => 'failed', 'message' => 'The payment track missing', 'error' => 'Please provide a valid transaction ID.'];
                    goto returnFVI;
                }

                // dd($value->product_id);

                $product = DB::table('product')
                    ->where('id', '=', $value->product_id)
                    ->where('deletes', '0')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get();

                if ($product->count() < 1) {
                    $response = ['status' => 'failed', 'message' => 'The product track missing', 'error' => 'Please provide a valid transaction ID.'];
                    goto returnFVI;
                }





                $ticket = DB::table('ndticket')
                    ->select(
                        DB::raw('CAST(raffleIds AS JSON) AS raffleIds'),
                        'purchaseDatetime',
                        'ticketNo',
                        'validityPeriod',
                        'endDate',
                        'netTotal',
                        'totalRaffle',
                        'is_thrill',
                        'is_weekly',
                        'is_bumper'
                    )
                    ->where('referenceID', '=', $value->ticketReferenceID)
                    ->where('deletes', '=', '0')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get();

                if ($ticket->count() < 1) {
                    $response = ['status' => 'failed', 'message' => 'Ticket not found!', 'error' => 'Please provide a valid transaction ID.'];
                    goto returnFVI;
                }



                $data[] = [
                    'ticketReferenceID'  => $value->ticketReferenceID,
                    'transactionID' => $value->payment_transaction_id,
                    'name' => $value->firstname . ' ' . ($value->lastname ?? ''),
                    'mobile' => $value->mobile,
                    'email' => $value->emailid,
                    'address' =>  $value->address,
                    'city' => $value->city,
                    'country' => $value->country,
                    'discount' => strval($value->discount),
                    'totalAmt' => strval($value->totalAmt),
                    'taxPercentage' => strval($value->taxPercentage),
                    'taxValue' => strval($value->taxValue),
                    'netTotal' => strval($value->netTotal),

                    'shipamount' => strval($value->shipamount),

                    'grandtotal' => strval($value->grandtotal),
                    'paymentType' => $value->paymentType,
                    'invoiceNo' => $value->id,
                    'paymentStatus' => $paymentHistory[0]->paymentStatus,
                    'invoiceDate' => $value->createdon,
                    'cartDetails' => json_decode($paymentHistory[0]->checkout_response, true),
                    'productName' =>  $product[0]->name,
                    'amountWords' => $numberController->convert(number_format((float)$value->grandtotal, 2, '.', ''), 'AED'),
                    'cart' => json_decode($value->cart, true)
                ];
            }





            // dd($data);



            // $paymentHistory = DB::table('payment_history')
            //     ->where('transaction_id', '=', $transaction_id)
            //     ->orWhere('ticketReferenceID', '=', $transaction_id)
            //     ->where('status', '1')
            //     ->orderBy('id', 'DESC')
            //     ->limit(1)
            //     ->get();




            // if ($paymentHistory->count() < 1) {
            //     $response = ['status' => 'failed', 'message' => 'Please provide a valid transaction ID.', 'error' => 'Please provide a valid transaction ID.'];
            //     goto returnFVI;
            // }


            // $invoiceID = $paymentHistory[0]->invoice_no;
            // $ticketReferenceID = $paymentHistory[0]->ticketReferenceID;

            // $ticket = DB::table('ndticket')
            //     ->select(
            //         DB::raw('CAST(raffleIds AS JSON) AS raffleIds'),
            //         'purchaseDatetime',
            //         'ticketNo',
            //         'validityPeriod',
            //         'endDate',
            //         'netTotal',
            //         'totalRaffle',
            //         'is_thrill',
            //         'is_weekly',
            //         'is_bumper'
            //     )
            //     ->where('referenceID', '=', $ticketReferenceID)
            //     ->where('deletes', '=', '0')
            //     ->orderBy('id', 'DESC')
            //     ->limit(1)
            //     ->get();

            // if ($ticket->count() < 1) {
            //     $response = ['status' => 'failed', 'message' => 'Ticket not found!', 'error' => 'Please provide a valid transaction ID.'];
            //     goto returnFVI;
            // }


            // $invoice = DB::table('invoice')
            //     ->where('id', '=', $invoiceID)
            //     ->where('deletes', '=', '0')
            //     ->orderBy('id', 'DESC')
            //     ->limit(1)
            //     ->get();

            // if ($invoice->count() < 1) {
            //     $response = ['status' => 'failed', 'message' => 'Invoice not found!', 'error' => 'Please provide a valid transaction ID.'];
            //     goto returnFVI;
            // }

            // $data['name'] = $invoice[0]->firstname . ' ' . ($invoice[0]->lastname  ?? '');
            // $data['email'] = $invoice[0]->emailid ?? '';
            // $data['mobile'] = $invoice[0]->mobile ?? '';
            // $data['purchaseDate'] = $ticket[0]->purchaseDatetime ?? '';
            // $data['ticketNo'] = $ticket[0]->ticketNo ?? '';
            // $data['validityPeriod'] = $ticket[0]->validityPeriod ?? '';
            // $data['expiryDate'] = $ticket[0]->endDate ?? '';
            // $data['netTotal'] = $ticket[0]->netTotal ?? '';
            // $data['raffleIds'] = json_decode($ticket[0]->raffleIds, true);
            // $data['totalRaffle'] = $ticket[0]->totalRaffle;
            // $data['is_thrill'] = $ticket[0]->is_thrill;
            // $data['is_weekly'] = $ticket[0]->is_weekly;
            // $data['is_bumper'] = $ticket[0]->is_bumper;

            $response = ['status' => 'success', 'message' => 'The Invoice details were collected successfully.', 'data' => $data];
            goto returnFVI;



            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
}
