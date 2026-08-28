<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
use App\Models\user_register;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;

class pastDraw extends Controller
{


  // Get User Product Cart Details
  // public function pastDrawList(Request $request)
  // {
  //   try {
  //     $response = [];
  //     $input = $request->all();
  //     $data = [];

  //     $query  =  DB::select("SELECT g.*, rf.raffleIds FROM (
  //                             SELECT d.id AS 'drawID', d.name AS 'drawName', d.raffle_status AS 'raffleDraw', d.first AS 'strightNo' FROM `draw` AS d
  //                             WHERE d.`status` = 'Completed' AND d.`deletes` = '0'   ORDER BY `id` DESC
  //                           ) AS g
  //                           LEFT JOIN (SELECT draw_id, REPLACE(GROUP_CONCAT(`raffle_id`) , ',', ' | ') AS 'raffleIds' FROM (
  //                             SELECT `id`, `draw_id`, `raffle_id`, `prize` FROM `winnerlist` WHERE  `raffle_id` IS NOT null AND `my3number` IS null ORDER BY `prize` ASC
  //                           ) AS f GROUP BY f.`draw_id`) AS  rf ON rf.draw_id = g.drawID ORDER BY g.drawID DESC;");

  //     if (count($query) > 0) {
  //       $data = $query;
  //     }

  //     $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
  //     goto returnFVI;

  //     returnFVI:
  //     return response()->json($response);
  //   } catch (Exception $e) {
  //     $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
  //     return response()->json($response);
  //   }
  // }


  public function pastDrawList(Request $request)
  {
    try {
      $response = [];
      // $input = $request->all();
      $data = [];

      $query = DB::select("SELECT g.*, rf.raffleIds FROM (
          SELECT d.id AS 'drawID', d.name AS 'drawName', d.raffle_status AS 'raffleDraw', d.first AS 'strightNo', d.result_datetime AS 'DateTime' FROM `draw` AS d
          WHERE d.`status` = 'Completed' AND d.`deletes` = '0' ORDER BY `id` DESC
      ) AS g
      LEFT JOIN (
          SELECT draw_id, REPLACE(GROUP_CONCAT(`raffle_id`), ',', ' | ') AS 'raffleIds' FROM (
              SELECT `id`, `draw_id`, `raffle_id`, `prize` FROM `winnerlist` WHERE  `raffle_id` IS NOT null AND `my3number` IS null ORDER BY `prize` ASC
          ) AS f GROUP BY f.`draw_id`
      ) AS rf ON rf.draw_id = g.drawID ORDER BY g.drawID DESC;");

      // Developed By Surya 23/12/2023
      $raffledraw = DB::table('raffledraw')
        // ->select('draw_no AS drawID', 'name AS drawName', 'status AS raffleDraw', 'result_datetime AS DateTime', DB::raw('null as strightNo'), DB::raw('null as raffleIds'))
        ->select(
          'draw_no AS drawID',
          'name AS drawName',
          DB::raw("CASE WHEN status = 'Completed' THEN 'Grand' ELSE null END AS raffleDraw"),
          'result_datetime AS DateTime',
          DB::raw('null as strightNo'),
          DB::raw('null as raffleIds')
        )
        ->where('status', 'Completed')
        ->orderBy('result_datetime', 'DESC')
        ->get()
        ->toArray();

        $superraffledraw = DB::table('superraffledraw')
        // ->select('draw_no AS drawID', 'name AS drawName', 'status AS raffleDraw', 'result_datetime AS DateTime', DB::raw('null as strightNo'), DB::raw('null as raffleIds'))
        ->select(
          'draw_no AS drawID',
          'name AS drawName',
          DB::raw("CASE WHEN status = 'Completed' THEN 'Super' ELSE null END AS raffleDraw"),
          'result_datetime AS DateTime',
          DB::raw('null as strightNo'),
          DB::raw('null as raffleIds')
        )
        ->where('status', 'Completed')
        ->orderBy('result_datetime', 'DESC')
        ->get()
        ->toArray();

      $data = array_merge($query, $raffledraw);

      $data = array_merge($data, $superraffledraw);


      usort($data, function ($a, $b) {
        return strtotime($b->DateTime) - strtotime($a->DateTime);
      });

      $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }

  public function pastDrawDetails(Request $request)
  {
    try {
      $response = [];
      $input = $request->all();
      $data = [];

      $just3Draw = [];

      $draw_id = Controller::BlockSQLInjection($request->draw_id);
      if ($draw_id == '' || $draw_id == null || $draw_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
        goto returnFVI;
      }

      // dd($request->pageTitle);

      if (isset($request->pageTitle) && $request->pageTitle != null && $request->pageTitle != '' && $request->pageTitle == 'Grand') {
        $grandRaffle = DB::select("SELECT r.*, p.rate FROM `raffledraw` AS `r` LEFT JOIN ticket_lines AS t ON r.win_ticket_line_id = t.id LEFT JOIN product AS p ON p.id = r.product_id WHERE r.`id` = '$draw_id' AND r.`status` = 'Completed';");


        if (count($grandRaffle) > 0) {
          $data['grandRaffle']['winnersList'] =  $grandRaffle;
          $data['grandRaffle']['drawNo'] =  $grandRaffle[0]->draw_no;
          $data['grandRaffle']['drawName'] =  'GRAND RAFFLE DRAW #' . str_pad($grandRaffle[0]->draw_no, 3, "0", STR_PAD_LEFT);
          goto resultGO;
        } else {
          $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
          goto returnFVI;
        }
      }

      if (isset($request->pageTitle) && $request->pageTitle != null && $request->pageTitle != '' && $request->pageTitle == 'Super') {
        $grandRaffle = DB::select("SELECT r.*, p.rate FROM `superraffledraw` AS `r` LEFT JOIN ticket_lines AS t ON r.win_ticket_line_id = t.id LEFT JOIN product AS p ON p.id = r.product_id WHERE r.`id` = '$draw_id' AND r.`status` = 'Completed';");


        if (count($grandRaffle) > 0) {
          $data['superRaffle']['winnersList'] =  $grandRaffle;
          $data['superRaffle']['drawNo'] =  $grandRaffle[0]->draw_no;
          $data['superRaffle']['drawName'] =  'SUPER RAFFLE DRAW #' . str_pad($grandRaffle[0]->draw_no, 3, "0", STR_PAD_LEFT);
          goto resultGO;
        } else {
          $response = ['status' => 'failed', 'message' => 'Please use a valid draw id!', 'error' => 'Please use a valid draw id!'];
          goto returnFVI;
        }
      }


      $draw = DB::select("SELECT * FROM `draw` WHERE id = '$draw_id' AND `deletes` = '0' ORDER BY `id` DESC LIMIT 1;");
      if (count($draw) < 1) {
        $response = ['status' => 'failed', 'message' => 'Draw Not Found!', 'error' => 'Draw Not Found!'];
        goto returnFVI;
      }

      $just3Draw['drawName'] =  $draw[0]->name;
      $just3Draw['drawNo'] =  $draw[0]->draw_no;
      $just3Draw['drawid'] =  $draw[0]->id;
      $just3Draw['first'] = ($draw[0]->first != '-') ? $draw[0]->first : '';
      $just3Draw['second'] = ($draw[0]->second != '-') ? $draw[0]->second : '';
      $just3Draw['third_one'] = ($draw[0]->third_one != '-') ? $draw[0]->third_one : '';
      $just3Draw['third_two'] = ($draw[0]->third_two != '-') ? $draw[0]->third_two : '';
      $just3Draw['third_three'] = ($draw[0]->third_three != '-') ? $draw[0]->third_three : '';
      $just3Draw['third_four'] = ($draw[0]->third_four != '-') ? $draw[0]->third_four : '';
      $just3Draw['hprize1'] = $draw[0]->hprize1;
      $just3Draw['hprize2'] = $draw[0]->hprize2;
      $just3Draw['hprize3'] = $draw[0]->hprize3;
      $just3Draw['prizeRatio'] = (int)$draw[0]->prizeRatio;
      $just3Draw['resultDatetime'] = $draw[0]->result_datetime;
      $just3Draw['drawNo'] = str_pad($draw[0]->draw_no, 3, "0", STR_PAD_LEFT);

      $product = DB::select("SELECT * FROM `product` WHERE `deletes` = '0' ORDER BY `id` ASC;");
      // dd('Inprogress');
      $category = [];

      if (count($product) > 0) {
        $just3Draw['productList']  = $product;
        foreach ($product as $key => $value) {
          $pid = $value->id;
          $t_count = 0;
          $t_amt = 0;

          $first = $just3Draw['first'];

          $first_z_amt = 'XXX';
          $first_z_count = 'XXX';
          if ($first != '' && $first != '-') {
            // $first_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $first;");
            $first_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $first;");

            $first_z_row = $first_z[0];
            if (intval($first_z_row->count) > 0) {
              $first_z_count =  str_pad(intval($first_z_row->count), 2, "0", STR_PAD_LEFT);
              $first_z_amt = intval($first_z_row->amt);
              $t_count += intval($first_z_row->count);
              $t_amt += intval($first_z_row->amt);
            }
          }

          $second =  $just3Draw['second'];

          $second_z_amt = 'XXX';
          $second_z_count = 'XXX';
          if ($second != '' && $second != '-') {
            // $second_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $second;");
            $second_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $second;");

            $second_z_row = $second_z[0];
            if (intval($second_z_row->count) > 0) {
              $second_z_count =  str_pad(intval($second_z_row->count), 2, "0", STR_PAD_LEFT);
              $second_z_amt = intval($second_z_row->amt);
              $t_count += intval($second_z_row->count);
              $t_amt += intval($second_z_row->amt);
            }
          }

          $third_one =  $just3Draw['third_one'];

          $third_one_z_amt = 'XXX';
          $third_one_z_count = 'XXX';
          if ($third_one != '' && $third_one != '-') {
            // $third_one_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_one");
            $third_one_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_one;");
            $third_one_z_row = $third_one_z[0];
            if (intval($third_one_z_row->count) > 0) {
              $third_one_z_count =  str_pad(intval($third_one_z_row->count), 2, "0", STR_PAD_LEFT);
              $third_one_z_amt = intval($third_one_z_row->amt);
              $t_count += intval($third_one_z_row->count);
              $t_amt += intval($third_one_z_row->amt);
            }
          }

          $third_two =  $just3Draw['third_two'];

          $third_two_z_amt = 'XXX';
          $third_two_z_count = 'XXX';
          if ($third_two != '' && $third_two != '-') {
            // $third_two_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_two");
            $third_two_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_two;");
            $third_two_z_row = $third_two_z[0];
            if (intval($third_two_z_row->count) > 0) {
              $third_two_z_count =  str_pad(intval($third_two_z_row->count), 2, "0", STR_PAD_LEFT);
              $third_two_z_amt = intval($third_two_z_row->amt);
              $t_count += intval($third_two_z_row->count);
              $t_amt += intval($third_two_z_row->amt);
            }
          }

          $third_three = $just3Draw['third_three'];

          $third_three_z_amt = 'XXX';
          $third_three_z_count = 'XXX';
          if ($third_three != '' && $third_three != '-') {
            // $third_three_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_three");
            $third_three_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_three;");
            $third_three_z_row = $third_three_z[0];

            if (intval($third_three_z_row->count) > 0) {
              $third_three_z_count =  str_pad(intval($third_three_z_row->count), 2, "0", STR_PAD_LEFT);
              $third_three_z_amt = intval($third_three_z_row->amt);
              $t_count += intval($third_three_z_row->count);
              $t_amt += intval($third_three_z_row->amt);
            }
          }

          $third_four = $just3Draw['third_four'];

          $third_four_z_amt = 'XXX';
          $third_four_z_count = 'XXX';
          if ($third_four != '' && $third_four != '-') {
            // $third_four_z = mysqli_query($con, "SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_four");
            $third_four_z = DB::select("SELECT SUM(prize_amt) AS 'amt', COUNT(id) AS 'count' FROM `winnerlist` WHERE `p_id` = $pid AND `draw_id` = $draw_id AND `my3number` = $third_four;");
            $third_four_z_row = $third_four_z[0];
            if (intval($third_four_z_row->count) > 0) {
              $third_four_z_count =  str_pad(intval($third_four_z_row->count), 2, "0", STR_PAD_LEFT);
              $third_four_z_amt = intval($third_four_z_row->amt);
              $t_count += intval($third_four_z_row->count);
              $t_amt += intval($third_four_z_row->amt);
            }
          }
          $proArray = [];

          $proArray['prize-box1'] = intval($value->rate) * $just3Draw['prizeRatio'];

          $prizeRatio2 = $just3Draw['prizeRatio'] / 10;
          $proArray['prize-box2'] = intval($value->rate) * $prizeRatio2;
          if ($just3Draw['prizeRatio'] === 250) {
            $prizeRatio3 = $prizeRatio2 / 10;
          } else if ($just3Draw['prizeRatio'] === 300) {
            $prizeRatio3 = $prizeRatio2 / 3;
          }

          $proArray['prize-box3'] = intval($value->rate) * $prizeRatio3;
          $proArray['prize-box4'] = intval($value->rate) * $prizeRatio3;
          $proArray['prize-box5'] = intval($value->rate) * $prizeRatio3;
          $proArray['prize-box6'] = intval($value->rate) * $prizeRatio3;
          $proArray["winners-box1"] = $first_z_count;
          $proArray["winners-box2"] = $second_z_count;
          $proArray["winners-box3"] = $third_one_z_count;
          $proArray["winners-box4"] = $third_two_z_count;
          $proArray["winners-box5"] = $third_three_z_count;
          $proArray["winners-box6"] = $third_four_z_count;
          $proArray["total-box1"] = $first_z_amt;
          $proArray["total-box2"] = $second_z_amt;
          $proArray["total-box3"] = $third_one_z_amt;
          $proArray["total-box4"] = $third_two_z_amt;
          $proArray["total-box5"] = $third_three_z_amt;
          $proArray["total-box6"] = $third_four_z_amt;
          $proArray["totalWinners"] = ($t_count < 1) ? '00' : $t_count;
          $proArray["totalPrizes"] = ($t_amt < 1) ?  '000' :  $t_amt;

          $just3Draw['caterogyList']['AED' . $value->rate] = $proArray;
        }
      }

      $data['3BallWinners'] =  $just3Draw;

      // Raffle Winners 
      $raffleWinners = DB::select("SELECT g.userid AS 'userID',
                                  g.name AS 'Name',
                                  g.raffle_id AS 'raffleID',
                                  g.ticket_lines_id AS 'ticketLinesID',
                                  g.prize AS 'prize',
                                  g.p_id AS 'productID',
                                  g.prize_amt AS 'prizeAmt',
                                  p.rate AS 'productPrize' FROM (SELECT userid, name, raffle_id, ticket_lines_id, prize, p_id, prize_amt FROM `winnerlist`  WHERE `draw_id` = '$draw_id' AND raffle_id IS 	NOT null AND my3number IS null ORDER BY prize ASC LIMIT 3) AS g 
                                  LEFT JOIN product AS p ON g.p_id = p.id;");

      $data['RaffleWinners']['winnersList'] =  $raffleWinners;
      $data['RaffleWinners']['drawNo'] =  $draw[0]->draw_no;
      $data['RaffleWinners']['drawName'] =  'JUST3 RAFFLE DRAW #' . str_pad($draw[0]->draw_no, 3, "0", STR_PAD_LEFT);




      // dd($raffleWinners);
      resultGO:
      $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {
      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
