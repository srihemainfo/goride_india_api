<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\user_register;
// use Twilio\Rest\Client;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use \App\Mail\OtpMail;

class WithdrawController extends Controller
{
    public function withdraw_data(Request $request)
    {
        $auth = auth()->user()->id;

        // $auth=51484;
        $base_url = ($request->header('Origin') . '/');

        $data = DB::table('withdraw_request')->select(
            'withdraw_request.*',
            'user_register.id as uid',
            'user_register.name',
            'user_register.t_point',
            'user_register.f_points',
            'user_register.t_earning',
            'user_register.cash_points',
            'user_register.bonus_points'
        )
            ->leftjoin('user_register', 'user_register.id', '=', 'withdraw_request.from_id')
            ->where(['withdraw_request.from_id' => $auth, 'withdraw_request.deletes' => '0'])->orderBy('withdraw_request.id', "DESC")->get();
        // dd($data);
        if (count($data) > 0) {

            $detailsArray = [];
            foreach ($data as $datas) {
                $to_data = DB::table('user_register')->where('id', $datas->to_id)->first();

                //   dd($to_data);
                if ($datas->status == "0") {
                    $status = "Inprogress";
                } else {
                    $status = "Success";
                }
                if ($datas->img_url != "") {
                    $img_url = $base_url . '' . $datas->img_url;
                } else {
                    $img_url = "";
                }

                if ($datas->attachment_url != "") {
                    // dd($base_url);
                    $attachment_url = (strpos($datas->attachment_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL')  . $datas->attachment_url : $base_url . '' . $datas->attachment_url;
                } else {
                    $attachment_url = "";
                }

                // if ($datas->attachment_url) {
                //     $attachment = $datas->attachment_url;
                // } else {
                //     $attachment = "";
                // }
                $details = [

                    'id' => $datas->id,
                    'createdon' => date("d-M-Y g:i a", strtotime($datas->createdon)),
                    'to' => $to_data->user,
                    'amount' => $datas->amount,
                    'status' => $datas->status == 0 ? 'Inprogress' : ($datas->status == 2 ? 'Rejected' : 'Success'),
                    'request_id' => $datas->request_id,
                    'attachment' => $attachment_url,
                    'winning_balance' => $datas->t_earning,


                ];
                $detailsArray[] = $details;
            }
            $response = [

                'status' => 'success',
                'message' => 'Withdraw datas',
                'data' => [
                    'winning_balance' => $datas->t_earning,
                    'withdraw' => $detailsArray,
                ],

            ];

            return response($response);
        } else {

            $response = [

                'status' => 'failed',
                'message' => 'No data available',
                'data' => 'no data available',

            ];

            return response($response);

            // if($data){

            // }

            // dd($data);
        }
    }
    //  withdraw details usde this function
    public function withdraw_details()
    {
        $auth = auth()->user()->id;

        //  withdraw user query
        $data = DB::table('user_register')->select(
            'user_register.id',
            'user_register.user',
            'user_register.name',
            'user_register.dob',
            'user_register.passport',
            'user_register.t_earning',
            'user_register.account_name',
            'user_register.acctype',
            'user_register.bank_name',
            'user_register.bank_address',
            'user_register.branch_code',
            'user_register.swift_code',
            'user_register.currency_code',
            'user_register.IBAN_code',
            'user_register.nationality',
            'user_register.residinglocation',
            'user_register.exchangeid'
        )
            ->where(['user_register.id' => $auth, 'deletes' => 0, 'roll_id' => 0, 'status' => 0])->first();

        //  withdraw details query2

        $data2 = DB::table('withdraw_request')->select(
            'withdraw_request.request_id',
            'withdraw_request.amount',
            'withdraw_request.trans_mode',
            'withdraw_request.cus_prefer',
            'withdraw_request.exchangeid',
            'withdraw_request.swiftcode',
            'withdraw_request.acctype',
            'withdraw_request.currencyccode',
            'withdraw_request.achname',
            'withdraw_request.acc_no',
            'withdraw_request.bank_name',
            'withdraw_request.iban_code',
            'withdraw_request.branch_name',
            'withdraw_request.branch_code',
            'withdraw_request.nationality',
            'withdraw_request.residinglocation',
            'withdraw_request.emirites_passport',
            'withdraw_request.dob',
            'withdraw_request.trans_date',
        )
            ->where(['withdraw_request.from_id' => $auth])->first();
        //  withdraw user images detaisl get query
        $data3 = DB::table('user_images')->select('user_images.img_url', 'user_images.type', 'user_images.user_id')
            ->where(['user_images.user_id' => $auth, 'deletes' => '0'])->orderBy('id', 'DESC')->take(2)->get();
        if ($data) {

            $data->from_id = $data2->from_id ?? '';
            $data->achname = $data2->achname ?? '';
            $data->amount = $data2->amount ?? '';
            $data->acctype1 = $data2->acctype ?? '';
            $data->bank_name1 = $data2->bank_name ?? '';
            $data->branch_code1 = $data2->branch_code ?? '';
            $data->iban_code = $data2->iban_code ?? '';
            $data->swiftcode = $data2->swiftcode ?? '';
            $data->dob1 = $data2->dob ?? '';
            $data->currencyccode1 = $data2->currencyccode ?? '';
            $data->emirites_passport = $data2->emirites_passport ?? '';
            $data->nationality1 = $data2->nationality ?? '';
            $data->residinglocation1 = $data2->residinglocation ?? '';
            $data->createdon = $data2->createdon ?? '';

            if ($data3) {
                foreach ($data3 as $datas) {
                    $data->user_id = $datas->user_id ?? '';
                    if ($datas->type == 'FRONT') {
                        $data->front_img = ($request->header('Origin') . '/') . $datas->img_url ?? '';
                    }
                    if ($datas->type == 'BACK') {
                        $data->back_img = ($request->header('Origin') . '/') . $datas->img_url ?? '';
                    }
                }
            }
        }
        $response = [

            'status' => 'success',
            'message' => 'Withdraw details show',
            'data' => [
                'data' => $data,
            ],
        ];
        return response($response);
    }
    // get nationality
    public function drop_nation()
    {
        $counteries = DB::table('countries')->select(
            'countries.id',
            'countries.name',
        )->get();

        $bank_list = [

            'First Abu Dhabi Bank (FAB)',

            'Emirates NBD',

            'Abu Dhabi Commercial Bank',

            'Dubai Islamic Bank',

            'MashreqBank',

            'Abu Dhabi Islamic Bank (ADIB)',

            'HSBC Bank Middle East - UAE Operations',

            'Union National Bank',

            'Commercial Bank of Dubai (CBD)',

            'Emirates Islamic Bank',

            'National Bank of Ras Al Khaimah (RAKBANK)',

            'Al Hilal Bank',

            'Noor Bank',

            'Sharjah Islamic Bank',

            'National Bank of Fujairah',

            'Others',

        ];

        $acc_type = ['savings', 'current'];

        if ($counteries) {

            $response = [

                'status' => 'success',
                'message' => 'Country details show',
                'data' => [
                    'bank_list' => $bank_list,
                    'account_type' => $acc_type,
                    'nationality' => $counteries,

                ],
            ];
            return response($response);
        } else {
            $response = [

                'status' => 'failure',
                'message' => 'Country not select',
                'error' => '',
            ];
            return response($response);
        }
    }
    //  get Live in
    public function drop_live()
    {

        $resdinglogastion = DB::table('countries')->select(
            'countries.id',
            'countries.name',
        )->get();

        if ($resdinglogastion) {

            $response = [

                'status' => 'success',
                'message' => 'Live in details show',
                'data' => [
                    'resdinglogastion' => $resdinglogastion,
                ],
            ];
            return response($response);
        } else {
            $response = [

                'status' => 'failure',
                'message' => 'Live in not select',
                'error' => '',
            ];
            return response($response);
        }
    }
}
