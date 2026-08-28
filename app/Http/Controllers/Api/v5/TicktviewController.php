<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use App\Models\user_register;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicktviewController extends Controller
{


    public static function eliDrawCount($buildCheckOut)
    {
        try {
            $response = [];
            $eligiableCount = 0;
            $data = [];

            $data['drawCounts']['thrillCount'] = 0;
            $data['drawCounts']['boosterCount'] = 0;
            $data['drawCounts']['bumberCount'] = 0;

            if ($buildCheckOut['eligibleDraw']['is_thrill']) {
                $data['drawCounts']['thrillCount'] = DB::table('draw')
                    ->where([
                        // ['resultDate', '>', date('Y-m-d', strtotime($buildCheckOut['startDate']))],
                        // ['resultDate', '<=', $buildCheckOut['endDate']],
                        ['deletes', '=', '0'],
                        // ['dailyThirllStatus', '=', 'Active']
                    ])
                    ->whereBetween('resultDate', [date('Y-m-d', strtotime($buildCheckOut['startDate'])), date('Y-m-d', strtotime($buildCheckOut['endDate']))])
                    ->whereIn('dailyThirllStatus', ['Active'])
                    ->orderBy('resultDate', 'ASC')
                    ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                    ->count();

                $eligiableCount += $data['drawCounts']['thrillCount'];
            }

            if ($buildCheckOut['eligibleDraw']['is_weekly']) {
                $data['drawCounts']['boosterCount'] = DB::table('draw')
                    ->where([
                        // ['resultDate', '>', date('Y-m-d', strtotime($buildCheckOut['startDate']))],
                        // ['resultDate', '<=', $buildCheckOut['endDate']],
                        ['deletes', '=', '0'],
                        // ['weeklyBoosterStatus', '=', 'Active']
                    ])
                    ->whereBetween('resultDate', [date('Y-m-d', strtotime($buildCheckOut['startDate'])), date('Y-m-d', strtotime($buildCheckOut['endDate']))])
                    ->whereIn('weeklyBoosterStatus', ['Active'])
                    ->orderBy('resultDate', 'ASC')
                    ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                    ->count();

                $eligiableCount +=  $data['drawCounts']['boosterCount'];
            }

            if ($buildCheckOut['eligibleDraw']['is_bumper']) {


                $data['drawCounts']['bumberCount'] = DB::table('draw')
                    ->where([
                        // ['resultDate', '>', date('Y-m-d', strtotime($buildCheckOut['startDate']))],
                        // ['resultDate', '<=', $buildCheckOut['endDate']],
                        ['deletes', '=', '0'],
                        // ['monthlyBumperStatus', '=', 'Active'],
                        // ['monthlyBumperPrice', '<=', $buildCheckOut['maxPrize']]
                    ])
                    ->whereBetween('resultDate', [date('Y-m-d', strtotime($buildCheckOut['startDate'])), date('Y-m-d', strtotime($buildCheckOut['endDate']))])
                    ->whereIn('monthlyBumperStatus', ['Active'])
                    ->orderBy('resultDate', 'ASC')
                    ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                    ->count();
                $eligiableCount += $data['drawCounts']['bumberCount'];
            }
            $data['eligiableCount'] = $eligiableCount;

            // $data['drawCounts']['thrillTotalChances'] = $data['drawCounts']['thrillCount'] * $buildCheckOut['totalRaffles'];
            // $data['drawCounts']['boosterTotalChances'] = $data['drawCounts']['boosterCount'] * $buildCheckOut['totalRaffles'];
            // $data['drawCounts']['bumberTotalChances'] = $data['drawCounts']['bumberCount'] * $buildCheckOut['totalRaffles'];

            $response = ['status' => 'success', 'message' => 'The Draw Count Collected!', 'data' => $data];
            goto returnFVI;

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    public function ticketView(Request $request)
    {
        try {
            $response = [];

            $data = [];

            $transaction_id = $request->transaction_id;
            $transaction_id = Controller::BlockSQLInjection($transaction_id);
            if ($transaction_id == '' || $transaction_id == null || $transaction_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid transaction ID.', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }

            $paymentHistory = DB::table('payment_history')
                ->where('transaction_id', '=', $transaction_id)
                ->orWhere('ticketReferenceID', '=', $transaction_id)
                ->where('status', '1')
                ->whereIn('renewalStatus', ['NEW', 'RENEWAL'])
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();



            // dd($paymentHistory);


            if ($paymentHistory->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid transaction ID.', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }


            $invoiceID = $paymentHistory[0]->invoice_no;
            $ticketReferenceID = $paymentHistory[0]->ticketReferenceID;


            // $cartDetails = $paymentHistory[0]->checkout_response != null ? json_decode($paymentHistory[0]->checkout_response, true) : null;


            // dd($cartDetails);


            $userData = DB::table('user_register')
                ->where([
                    ['id', '=', $paymentHistory[0]->user_id],
                    ['roll_id', '=', '0'],
                    //   ['deletes', '=', '0'],
                    //   ['status', '=', '0']
                ])
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();

            if ($userData->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'The Customer Not Found!', 'error' => 'The Customer Not Found!'];
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
                    'is_bumper',
                    'startDate'
                )
                ->where('referenceID', '=', $ticketReferenceID)
                ->where('deletes', '=', '0')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();

            if ($ticket->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'Ticket not found!', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }


            $invoice = DB::table('invoice')
                ->where('id', '=', $invoiceID)
                ->where('deletes', '=', '0')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get();

            if ($invoiceID > 0 && $invoice->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'Invoice not found!', 'error' => 'Please provide a valid transaction ID.'];
                goto returnFVI;
            }


            $firstDraw = DB::table('draw')
                ->where([
                    // ['saleDate', '>=', $ticketExpriy],
                    ['deletes', '=', '0'],
                    // ['dailyThirllStatus', '=', 'Active']
                ])
                ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                ->orderBy('saleDate', 'ASC')
                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                ->limit(1)
                ->get();

            if ($firstDraw->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'The first draw not found!', 'error' => 'The first draw not found!'];
                goto returnFVI;
            }


            $startDateDraw = DB::table('draw')
                ->where([
                    ['saleDate', '=', $ticket[0]->startDate],
                    ['deletes', '=', '0'],
                    // ['dailyThirllStatus', '=', 'Active']
                ])
                ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                ->orderBy('saleDate', 'ASC')
                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                ->limit(1)
                ->get();

            if ($startDateDraw->count() < 1) {
                $response = ['status' => 'failed', 'message' => 'The first draw not found!', 'error' => 'The first draw not found!'];
                goto returnFVI;
            }


            $data['name'] = ($invoice->count() > 0 ? ($invoice[0]->firstname . ' ' . ($invoice[0]->lastname  ?? '')) : ($userData[0]->name . ' ' . ($userData[0]->lname  ?? '')));
            $data['email'] = ($invoice->count() > 0 ? ($invoice[0]->emailid ?? '') : ($userData[0]->email  ?? ''));
            $data['mobile'] = ($invoice->count() > 0 ? ($invoice[0]->mobile ?? '') : ($userData[0]->mobile  ?? ''));
            $data['startDate'] = ($invoice->count() > 0 && $invoice[0]->renewalStatus == 'NEW') ? ($startDateDraw[0]->resultDate ?? '') : ($paymentHistory[0]->renewalStatus == 'NEW' ? ($startDateDraw[0]->resultDate ?? '') : ($firstDraw[0]->resultDate ?? ''));

            $data['purchaseDate'] = $ticket[0]->purchaseDatetime ?? '';
            $data['ticketNo'] = $ticket[0]->ticketNo ?? '';
            $data['validityPeriod'] = $ticket[0]->validityPeriod ?? '';

            $data['expiryDate'] = $ticket[0]->endDate ?? '';
            $data['netTotal'] = $ticket[0]->netTotal ?? '';
            $data['raffleIds'] = json_decode($ticket[0]->raffleIds, true);
            $data['totalRaffle'] = $ticket[0]->totalRaffle;
            $data['is_thrill'] = $ticket[0]->is_thrill;
            $data['is_weekly'] = $ticket[0]->is_weekly;
            $data['is_bumper'] = $ticket[0]->is_bumper;

            $data['bumperDrawDate'] = null;
            $data['drawCount'] = null;

            // $data['drawCount'] 
            // $cartDetails['drawCount'] = [];

            $countResponse = json_decode($this->eliDrawCount([
                'startDate' =>   $data['startDate'],
                'endDate' => $data['expiryDate'],
                'eligibleDraw' => [
                    'is_thrill' => $data['is_thrill'],
                    'is_weekly' => $data['is_weekly'],
                    'is_bumper' => $data['is_bumper']
                ]
            ])->getContent(), true);



            $cartDetails['drawCount'] = isset($countResponse['data']) ? $countResponse['data']['drawCounts'] : '';

            // dd($cartDetails['drawCount']);

            if ($cartDetails != null) {
                $data['drawCount']['thrillCount'] = isset($cartDetails['drawCount']) && isset($cartDetails['drawCount']['thrillCount']) ? $cartDetails['drawCount']['thrillCount'] : null;
                $data['drawCount']['boosterCount'] = isset($cartDetails['drawCount']) && isset($cartDetails['drawCount']['boosterCount']) ? $cartDetails['drawCount']['boosterCount'] : null;
                $data['drawCount']['bumberCount'] = isset($cartDetails['drawCount']) && isset($cartDetails['drawCount']['bumberCount']) ? $cartDetails['drawCount']['bumberCount'] : null;



                $getTotalDate = DB::table('draw')
                    ->select('resultDate')
                    // ->where('resultDate', '<=', date('Y-m-d', strtotime($data['expiryDate'])))
                    ->whereBetween('resultDate', [date('Y-m-d', strtotime($data['startDate'])), date('Y-m-d', strtotime($data['expiryDate']))])
                    ->where('deletes', '0')
                    ->whereIn('monthlyBumperStatus', ['Active'])
                    ->where('bumperDrawNo', '!=', '0')
                    ->orderBy('resultDate', 'desc')
                    // ->limit($data['drawCount']['bumberCount'])
                    ->pluck('resultDate')
                    ->toArray();

                $data['bumperDrawDate'] = array_reverse($getTotalDate);

                // dd($getTotalDate, array_reverse($getTotalDate));
            }



            $response = ['status' => 'success', 'message' => 'The ticket details were collected successfully.', 'data' => $data];
            goto returnFVI;



            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
}
