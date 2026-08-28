<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Models\user_register;
// use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawController extends Controller
{
    public function previous_draw(Request $request)
    {
        //  $auth= auth()->user()->id;

        $data = DB::table('draw')->select('id', 'draw_no', 'name')->where(['status' => 'Completed', 'deletes' => '0'])->orderBy('id', 'DESC')->get();

        if ($data) {

            $response = [

                'status' => 'success',
                'message' => 'Previous draw',
                'data' => [
                    'draw' => $data,

                ],

            ];
            return response($response);
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'No data available',
                'error' => 'no data available',

            ];
            return response($response);
        }
    }

    public function previous_drawview(Request $request)
    {


        $request->draw_id = Controller::BlockSQLInjection($request->draw_id);
        if ($request->draw_id == '' || $request->draw_id == null || $request->draw_id == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
            goto returnFVI;
        }


        $drawID = $request->draw_id;
        $responseData = [];

        if (isset($request->pageTitle) && $request->pageTitle != null && $request->pageTitle != '' && $request->pageTitle == 'Grand') {
            $grandRaffle = DB::select("SELECT r.*, p.rate FROM `raffledraw` AS `r` LEFT JOIN ticket_lines AS t ON r.win_ticket_line_id = t.id LEFT JOIN product AS p ON p.id = r.product_id WHERE r.`id` = '$draw_id' AND r.`status` = 'Completed';");

            $data['grandRaffle']['winnersList'] =  $grandRaffle;
            $data['grandRaffle']['drawNo'] =  $grandRaffle[0]->draw_no;
            $data['grandRaffle']['drawName'] =  'GRAND RAFFLE DRAW #' . str_pad($grandRaffle[0]->draw_no, 3, "0", STR_PAD_LEFT);
            goto returnFVI;
        }

        if ($drawID != '') {
            $draw = DB::table('draw')
                ->select('id', 'name', 'first', 'second', 'third_one', 'third_two', 'third_three', 'third_four', 'hprize1', 'hprize2', 'hprize3', 'prizeRatio', 'result_datetime', 'draw_no')
                ->where('id', $drawID)
                ->where('deletes', 0)
                ->orderBy('id', 'desc')
                ->limit(1)
                ->first();

            if ($draw) {
                $drawid = $draw->id;
                $draw_name = $draw->name;
                $first = $draw->first;
                $second = $draw->second;
                $third_one = $draw->third_one;
                $third_two = $draw->third_two;
                $third_three = $draw->third_three;
                $third_four = $draw->third_four;

                $hprize1 = $draw->hprize1;
                $hprize2 = $draw->hprize2;
                $hprize3 = $draw->hprize3;
                $prizeRatio = (int) $draw->prizeRatio;

                $result_datetime = date('d/m/y', strtotime($draw->result_datetime));
                $draw_no = str_pad($draw->draw_no, 3, "0", STR_PAD_LEFT);

                $products = DB::table('product')
                    ->select('id', 'rate')
                    ->where('deletes', 0)
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($products as $product) {
                    $pid = $product->id;
                    $t_count = '';
                    $t_amt = '';

                    $first_z_amt = 'XXX';
                    $first_z_count = 'XXX';
                    if ($first != '') {
                        $first_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) as amt'), DB::raw('COUNT(id) as count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $first)
                            ->first();
                        // dd($first_z);

                        if ($first_z->count > 0) {
                            $first_z_count = str_pad($first_z->count, 2, "0", STR_PAD_LEFT);
                            $first_z_amt = 'AED ' . number_format($first_z->amt) . '/-';
                            $t_count .= intval($first_z->count);
                            $t_amt .= intval($first_z->amt);
                        }
                    }
                    $second_z_amt = 'XXX';
                    $second_z_count = 'XXX';

                    if ($second != '') {
                        $second_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) AS amt'), DB::raw('COUNT(id) AS count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $second)
                            ->first();

                        if ($second_z->count > 0) {
                            $second_z_count = str_pad($second_z->count, 2, "0", STR_PAD_LEFT);
                            $second_z_amt = 'AED ' . number_format($second_z->amt) . '/-';
                            $t_count .= intval($second_z->count);
                            $t_amt .= intval($second_z->amt);
                        }
                    }
                    $third_one_z_amt = 'XXX';
                    $third_one_z_count = 'XXX';

                    if ($third_one != '') {
                        $third_one_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) AS amt'), DB::raw('COUNT(id) AS count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $third_one)
                            ->first();

                        if ($third_one_z->count > 0) {
                            $third_one_z_count = str_pad($third_one_z->count, 2, "0", STR_PAD_LEFT);
                            $third_one_z_amt = 'AED ' . number_format($third_one_z->amt) . '/-';
                            $t_count .= intval($third_one_z->count);
                            $t_amt .= intval($third_one_z->amt);
                        }
                    }
                    $third_two_z_amt = 'XXX';
                    $third_two_z_count = 'XXX';

                    if ($third_two != '') {
                        $third_two_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) AS amt'), DB::raw('COUNT(id) AS count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $third_two)
                            ->first();

                        if ($third_two_z->count > 0) {
                            $third_two_z_count = str_pad($third_two_z->count, 2, "0", STR_PAD_LEFT);
                            $third_two_z_amt = 'AED ' . number_format($third_two_z->amt) . '/-';
                            $t_count .= intval($third_two_z->count);
                            $t_amt .= intval($third_two_z->amt);
                        }
                    }
                    $third_three_z_amt = 'XXX';
                    $third_three_z_count = 'XXX';

                    if ($third_three != '') {
                        $third_three_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) AS amt'), DB::raw('COUNT(id) AS count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $third_three)
                            ->first();

                        if (intval($third_three_z->count) > 0) {
                            $third_three_z_count = str_pad($third_three_z->count, 2, "0", STR_PAD_LEFT);
                            $third_three_z_amt = 'AED ' . number_format($third_three_z->amt) . '/-';
                            $t_count .= intval($third_three_z->count);
                            $t_amt .= intval($third_three_z->amt);
                        }
                    }
                    $third_four_z_amt = 'XXX';
                    $third_four_z_count = 'XXX';

                    if ($third_four != '') {
                        $third_four_z = DB::table('winnerlist')
                            ->select(DB::raw('SUM(prize_amt) AS amt'), DB::raw('COUNT(id) AS count'))
                            ->where('p_id', $pid)
                            ->where('draw_id', $drawid)
                            ->where('my3number', $third_four)
                            ->first();

                        if (intval($third_four_z->count) > 0) {
                            $third_four_z_count = str_pad($third_four_z->count, 2, "0", STR_PAD_LEFT);
                            $third_four_z_amt = 'AED ' . number_format($third_four_z->amt) . '/-';
                            $t_count .= intval($third_four_z->count);
                            $t_amt .= intval($third_four_z->amt);
                        }
                    }
                    $tota_amt = $third_four_z->amt + $third_three_z->amt + $third_two_z->amt + $third_one_z->amt + $second_z->amt + $first_z->amt;
                    $total_count = $third_four_z->count + $third_three_z->count + $third_two_z->count + $third_one_z->count + $second_z->count + $first_z->count;
                    // dd($tota_amt);

                    $responseData[] = [
                        'pid' => $pid,
                        'rate' => $product->rate,
                        't_count' => $total_count,
                        't_amt' => $tota_amt,
                        'first_z_count' => $first_z_count,
                        'first_z_amt' => $first_z_amt,
                        'second_z_count' => $second_z_count,
                        'second_z_amt' => $second_z_amt,
                        'third_one_z_count' => $third_one_z_count,
                        'third_one_z_amt' => $third_one_z_amt,
                        'third_two_z_count' => $third_two_z_count,
                        'third_two_z_amt' => $third_two_z_amt,
                        'third_three_z_count' => $third_three_z_count,
                        'third_three_z_amt' => $third_three_z_amt,
                        'third_four_z_count' => $third_four_z_count,
                        'third_four_z_amt' => $third_four_z_amt,
                        // Add other data here for each product.

                    ];
                }
                $response = [
                    'status' => 'success',
                    'message' => 'Draw result  details',
                    'data' => [
                        'draw_id' => $drawid,
                        'draw_name' => $draw_name,
                        'first' => $first,
                        'second' => $second,
                        'th_one' => $third_one,
                        'th_two' => $third_two,
                        'th_three' => $third_three,
                        'th_four' => $third_four,
                        'hprize1' => $hprize1,
                        'hprize2' => $hprize2,
                        'hprize3' => $hprize3,
                        'prizeratio' => $prizeRatio,
                        'result_datetime' => $result_datetime,
                        'draw_no' => $draw_no,
                        'total_value' => $responseData,

                    ],
                ];
                return response($response);
            } else {
                $response = [
                    'status' => 'failed',
                    'message' => 'No data available',
                    'error' => 'no data available',
                ];
                return response($response);
            }
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'Transaction id missing',
                'error' => 'Transaction id missing',
            ];
            return response($response);
        }



        returnFVI:
        return response()->json($response);
    }

    public function previous_youtubevideo(Request $request)
    {

        $data = DB::table('draw')->select('draw.id', 'draw.name', 'draw.draw_no', 'past_video_url.youtube_url')
            ->leftjoin('past_video_url', 'past_video_url.draw_id', '=', 'draw.id')
            ->where(['draw.status' => 'Completed', 'draw.deletes' => '0'])
            ->orderBy('draw.id', 'DESC')->get();

        if ($data) {

            $response = [

                'status' => 'success',
                'message' => 'Past video list',
                'data' => [
                    'youtube_video' => $data,

                ],

            ];
            return response($response);
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'No data available',
                'error' => 'no data available',
            ];
            return response($response);
        }
    }

    public function winner_pastdraw(Request $request)
    {

        // $data=DB::table('draw')->where(['status'=>'Completed','deletes'=>'0'])->get();
        // $data=DB::table('draw')->where(['status'=>'Completed','deletes'=>'0'])->first();

        $data = DB::table('draw')->select(
            'draw.id',
            'draw.draw_no',
            'draw.name'

        )
            ->where(['draw.status' => 'Completed', 'draw.deletes' => '0'])
            // ->leftjoin('winnerlist','winnerlist.draw_id','=','draw.id')
            ->orderBy('draw.id', 'DESC')
            ->get();

        if ($data) {

            $detailsArray = [];
            foreach ($data as $datas) {
                $count = DB::table('winnerlist')->where('draw_id', $datas->id)->count();

                $details = [
                    'id' => $datas->id,
                    'draw_no' => $datas->draw_no,
                    'name' => $datas->name,
                    'count' => $count,

                ];
                $detailsArray[] = $details;
            }

            $response = [

                'status' => 'success',
                'message' => 'Winner past draw',
                'data' => [
                    'draw' => $detailsArray,

                ],

            ];
            return response($response);
        } else {
            $response = [
                'status' => 'failed',
                'message' => 'No data available',
                'error' => 'no data available',

            ];
            return response($response);
        }
    }
    public function winner_pastdrawview(Request $request)
    {
        // $response = [];
        // dd($request->draw_id);
        $request->draw_id = Controller::BlockSQLInjection($request->draw_id);
        if ($request->draw_id == '' || $request->draw_id == null || $request->draw_id == 'null') {
            $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
            goto returnFVI;
        }


        if (isset($request->pageTitle) && $request->pageTitle != null && $request->pageTitle != '' && $request->pageTitle == 'Grand') {


            $grandRaffle = DB::select("SELECT r.*, p.rate FROM `raffledraw` AS `r` LEFT JOIN ticket_lines AS t ON r.win_ticket_line_id = t.id LEFT JOIN product AS p ON p.id = r.product_id WHERE r.`id` = '$request->draw_id' AND r.`status` = 'Completed';");

            if (count($grandRaffle) > 0) {

                $gdata[] = [
                    "id" => $grandRaffle[0]->id,
                    "draw_no" => $grandRaffle[0]->draw_no,
                    "name" => $grandRaffle[0]->winner_name,
                    "my3number" => "",
                    "category" => intval($grandRaffle[0]->rate),
                    "prize_amt" => $grandRaffle[0]->wonprize,
                    "prize" => "",
                    "country" => $grandRaffle[0]->country,
                    "residingcountry" => $grandRaffle[0]->residingcountry,
                    "raffle_id" => $grandRaffle[0]->win_raffle_no,
                    "image_url" => (isset($grandRaffle[0]->image_url) && strpos($grandRaffle[0]->image_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL') . $grandRaffle[0]->image_url : (env('DO_REDIRECT_URL') . 'nationaldrawuae/1/d6ecb1f107a9c8a9f067df15ff4da83c.jpg')
                ];

                // dd( $grandRaffle);
                $response = [

                    'status' => 'success',
                    'message' => 'Winner draw view',
                    'data' => [
                        'draw' => $grandRaffle[0]->id,
                        'draw_name' => $grandRaffle[0]->name,
                        'date' => date("d M Y", strtotime($grandRaffle[0]->result_datetime)),
                        'lucky3number' => '',
                        'raffle_winners' => [],
                        'my3number_winners' => [],
                        'my3number_morewinners' => [],
                        'grandRaffleWinners' => $gdata

                    ],

                ];
                goto returnFVI;
            } else {
                $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
                goto returnFVI;
            }

            goto returnFVI;
        }


        if (isset($request->pageTitle) && $request->pageTitle != null && $request->pageTitle != '' && $request->pageTitle == 'Super') {


            $grandRaffle = DB::select("SELECT r.*, p.rate FROM `superraffledraw` AS `r` LEFT JOIN ticket_lines AS t ON r.win_ticket_line_id = t.id LEFT JOIN product AS p ON p.id = r.product_id WHERE r.`id` = '$request->draw_id' AND r.`status` = 'Completed';");

            if (count($grandRaffle) > 0) {

                $gdata[] = [
                    "id" => $grandRaffle[0]->id,
                    "draw_no" => $grandRaffle[0]->draw_no,
                    "name" => $grandRaffle[0]->winner_name,
                    "my3number" => "",
                    "category" => intval($grandRaffle[0]->rate),
                    "prize_amt" => $grandRaffle[0]->wonprize,
                    "prize" => "",
                    "country" => $grandRaffle[0]->country,
                    "residingcountry" => $grandRaffle[0]->residingcountry,
                    "raffle_id" => $grandRaffle[0]->win_raffle_no,
                    "image_url" => (isset($grandRaffle[0]->image_url) && strpos($grandRaffle[0]->image_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL') . $grandRaffle[0]->image_url : (env('DO_REDIRECT_URL') . 'nationaldrawuae/1/d6ecb1f107a9c8a9f067df15ff4da83c.jpg')
                ];

                // dd( $grandRaffle);
                $response = [

                    'status' => 'success',
                    'message' => 'Winner draw view',
                    'data' => [
                        'draw' => $grandRaffle[0]->id,
                        'draw_name' => $grandRaffle[0]->name,
                        'date' => date("d M Y", strtotime($grandRaffle[0]->result_datetime)),
                        'lucky3number' => '',
                        'raffle_winners' => [],
                        'my3number_winners' => [],
                        'my3number_morewinners' => [],
                        'grandRaffleWinners' => [],
                        'superRaffleWinners' => $gdata

                    ],

                ];
                goto returnFVI;
            } else {
                $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
                goto returnFVI;
            }

            goto returnFVI;
        }


        if ($request->draw_id != "") {
            $data = DB::table('winnerlist')->select(
                'winnerlist.id',
                'winnerlist.draw_id',
                'winnerlist.name as winner_name',
                'winnerlist.p_id',
                'winnerlist.my3number',
                'winnerlist.prize_amt',
                'winnerlist.prize',
                'winnerlist.country',
                'winnerlist.residingcountry',
                'winnerlist.raffle_id',
                'winnerlist.image_url'
            )
                //   ->leftjoin('draw')
                ->where('winnerlist.draw_id', '=', $request->draw_id)
                ->where('winnerlist.raffle_id', '=', null)
                // ->where('winnerlist.image_url', '!=', "")
                ->orderByDesc('prize_amt')
                ->orderBy('name')
                ->get();




            $data2 = DB::table('winnerlist')->select(
                'winnerlist.id',
                'winnerlist.draw_id',
                'winnerlist.name as winner_name',
                'winnerlist.p_id',
                'winnerlist.my3number',
                'winnerlist.prize_amt',
                'winnerlist.prize',
                'winnerlist.country',
                'winnerlist.residingcountry',
                'winnerlist.raffle_id',
                'winnerlist.image_url'
            )
                ->where('winnerlist.draw_id', '=', $request->draw_id)
                ->where('winnerlist.raffle_id', '!=', "")
                ->orderBy('winnerlist.id', 'ASC')
                ->get();

            // dd($data2);

            // if (count($data) > 0) {
            // dd($data);

            $lucky_drawno = DB::table('draw')->where('id', $request->draw_id)->first();
            $detailsArray = [];
            $detailsArray2 = [];
            $detailsArray3 = [];

            if (count($data) > 0) {
                foreach ($data as $datas) {

                    if ($datas->prize == "1") {
                        $matched_order = "Straignt";
                    } elseif ($datas->prize == "2") {
                        $matched_order = "Mixed";
                    } elseif ($datas->prize == "3") {
                        $matched_order = "Reverse";
                    }

                    if ($datas->p_id == "1") {
                        $product = "10";
                    } elseif ($datas->p_id == "2") {
                        $product = "20";
                    } elseif ($datas->p_id == "3") {
                        $product = "50";
                    } elseif ($datas->p_id == "4") {
                        $product = "100";
                    }

                    $base_url = ($request->header('Origin') . '/');
                    if ($datas->image_url) {
                        $image_url = (strpos($datas->image_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL') . $datas->image_url : $base_url . '' . $datas->image_url;
                    } else {
                        $image_url = "";
                    }

                    if (isset($datas->image_url) && $image_url != '') {


                        $details = [
                            'id' => $datas->id,
                            'draw_no' => $datas->draw_id ?: '',
                            'name' => $datas->winner_name ?: '',
                            'my3number' => $datas->my3number ?: '',
                            "matched_order" => $matched_order,
                            "category" => $datas->p_id ?: '',
                            'prize_amt' => $datas->prize_amt ?: '',
                            'prize' => $datas->prize ?: '',
                            'country' => $datas->country ?: '',
                            'residingcountry' => $datas->residingcountry ?: '',
                            'image_url' => $image_url,

                        ];
                        $detailsArray[] = $details;
                    }
                }
            }

            if ($detailsArray != []) {
                //   dd('asfdas');
                //  $detailsArray2[] = $details2;
                $my3num = $detailsArray;
            } else {
                $my3num = "";
            }
            /////////////////////////////////////////////////////////////

            if (count($data2) > 0) {
                if ($data2) {

                    $detailsArray2 = [];
                    foreach ($data2 as $datas2) {

                        if ($datas2->p_id == "1") {
                            $product = "10";
                        } elseif ($datas2->p_id == "2") {
                            $product = "20";
                        } elseif ($datas2->p_id == "3") {
                            $product = "50";
                        } elseif ($datas2->p_id == "4") {
                            $product = "100";
                        }
                        $base_url = ($request->header('Origin') . '/');
                        if ($datas2->image_url) {
                            $image_url = (strpos($datas2->image_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL') . $datas2->image_url : $base_url . '' . $datas2->image_url;
                            // $base_url . '' . $datas2->image_url;
                        } else {
                            $image_url = "https://www.nationaldrawuae.com/assets/images/Male156520230622020600.jpg";
                        }
                        $details2 = [
                            'id' => $datas2->id,
                            'draw_no' => $datas2->draw_id,
                            'name' => $datas2->winner_name,
                            'my3number' => $datas2->my3number,
                            'category' => $datas2->p_id,
                            'prize_amt' => $datas2->prize_amt,
                            'prize' => $datas2->prize,
                            'country' => $datas2->country,
                            'residingcountry' => $datas2->residingcountry,
                            'raffle_id' => $datas2->raffle_id ?: '',
                            'image_url' => $image_url,
                        ];
                        $detailsArray2[] = $details2;
                    }
                }
            }
          
            if ($detailsArray2 != []) {
                //   dd('asfdas');
                //  $detailsArray2[] = $details2;
                $raffle = $detailsArray2;
            } else {
                $raffle = "";
            }

            $data3 = DB::table('winnerlist')->select(
                'winnerlist.id',
                'winnerlist.draw_id',
                'winnerlist.name as winner_name',
                'winnerlist.p_id',
                'winnerlist.my3number',
                'winnerlist.prize_amt',
                'winnerlist.prize',
                'winnerlist.country',
                'winnerlist.residingcountry',
                'winnerlist.raffle_id',
                'winnerlist.image_url'
            )
                ->where('winnerlist.draw_id', '=', $request->draw_id)
                ->where('winnerlist.raffle_id', '=', null)
                ->where('winnerlist.image_url', '=', "")
                ->orderByDesc('prize_amt')
                ->orderBy('name')
                ->get();

    

            if (count($data3) > 0) {
                foreach ($data3 as $datas3) {

                    $base_url = ($request->header('Origin') . '/');
                    if ($data3) {

                        if ($datas3->image_url) {
                            $image_url = (strpos($datas3->image_url, "nationaldrawuae") === 0) ? env('DO_REDIRECT_URL') . $datas3->image_url : $base_url . '' . $datas3->image_url;
                            // $base_url . '' . $datas3->image_url;
                        } else {
                            $image_url = "";
                        }

                        $details3 = [
                            'id' => $datas3->id,
                            'draw_no' => $datas3->draw_id,
                            'name' => $datas3->winner_name,
                            'my3number' => $datas3->my3number,
                            'category' => $datas3->p_id,
                            'prize_amt' => $datas3->prize_amt,
                            'prize' => $datas3->prize,
                            'country' => $datas3->country,
                            'residingcountry' => $datas3->residingcountry,
                            'raffle_id' => $datas3->raffle_id ?: '',
                            'image_url' => $image_url,
                        ];
                        $detailsArray3[] = $details3;
                    }
                }
            }

            if ($detailsArray3 != []) {
                //   dd('asfdas');
                //  $detailsArray2[] = $details2;
                $my3winners = $detailsArray3;
            } else {
                $my3winners = "";
            }
            //  dd($detailsArray2);

            $response = [

                'status' => 'success',
                'message' => 'Winner draw view',
                'data' => [
                    'draw' =>  $request->draw_id,
                    'draw_name' => $lucky_drawno->name,
                    'drawfreq' => $lucky_drawno->drawfreq,
                    'date' => date("d M Y", strtotime($lucky_drawno->result_datetime)),
                    'lucky3number' => $lucky_drawno->first,
                    'raffle_winners' => $raffle,
                    'my3number_winners' => $my3num,
                    'my3number_morewinners' => $my3winners,

                ],

            ];
            return response($response);
            // } else {
            //     $response = [
            //         'status' => 'failed',
            //         'message' => 'No data available',
            //         'error' => 'no data available',

            //     ];
            //     return response($response);
            // }
        } else {
            $response = [

                'status' => 'failed',
                'message' => 'Kindly enter the draw',
                'error' => 'kindly enter the transfer draw',
            ];
            return response($response);
        }

        returnFVI:
        return response()->json($response);
    }
}
