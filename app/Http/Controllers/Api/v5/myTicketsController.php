<?php

namespace App\Http\Controllers\Api\v5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_register;
use App\Models\Agent;
// use Twilio\Rest\Client;
use GuzzleHttp\Client;
use App\Http\Controllers\Template\mailController;

use Auth;
use DateTime;
use Exception;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


// use \App\Mail\OtpMail;

class myTicketsController extends Controller
{


  public function myTicketNew(Request $request)
  {
    try {

      $response = [];
      $input = $request->all();

      $data = [];
      $user_id = auth()->user()->id;
      if ($user_id == '' || $user_id == null || $user_id == 'null') {
        $response = ['status' => 'failed', 'message' => 'Login Required!', 'error' => 'Kindly check the access token!'];
        goto returnFVI;
      }


      $request->year = Controller::BlockSQLInjection($request->year);
      if ($request->year == '' || $request->year == null || $request->year == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid year!', 'error' => 'Please use a valid year!'];
        goto returnFVI;
      }


      $request->ticketType = Controller::BlockSQLInjection($request->ticketType);
      if ($request->ticketType == '' || $request->ticketType == null || $request->ticketType == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid ticket type!', 'error' => 'Please use a valid ticket type!'];
        goto returnFVI;
      }

      $request->orderStatus = Controller::BlockSQLInjection($request->orderStatus);
      if ($request->orderStatus == '' || $request->orderStatus == null || $request->orderStatus == 'null') {
        $response = ['status' => 'failed', 'message' => 'Please use a valid order status!', 'error' => 'Please use a valid order status!'];
        goto returnFVI;
      }


      $year = $request->year;
      $ticket_type =  $request->ticketType;
      $order_status = $request->orderStatus;

      // if ($user_id != '') {

      $ticketCon = $userCon = "`user_id` = '$user_id'";

      if ($year != '') {
        $ticketCon .= "AND createdon  > '" . $year . "-01-01' AND createdon < '" . $year . "-12-31'";
      }

      $query = "SELECT * FROM (";

      if ($ticket_type == 'OT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('OT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'OT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE $userCon  AND `deletes` = '0' AND `type`='OT'   GROUP BY `ticket_id`";
      }

      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }

      if ($ticket_type == 'WT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('WT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'WT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE $userCon  AND `deletes` = '0' AND `type`='WT'   GROUP BY `ticket_id`";
      }

      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }

      if ($ticket_type == 'MT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('MT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'MT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines`WHERE $userCon  AND `deletes` = '0' AND `type`='MT'   GROUP BY `ticket_id`";
      }

      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }
      if ($ticket_type == 'AT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('AT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'AT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE  $userCon AND `deletes` = '0' AND `type`='AT'   GROUP BY `ticket_id`";
      }
      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }
      if ($ticket_type == 'FT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('FT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'FT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines`  WHERE $userCon  AND `deletes` = '0' AND `type`='FT'   GROUP BY `ticket_id`";
      }
      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }
      if ($ticket_type == 'CT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('CT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'CT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE  $userCon AND `deletes` = '0' AND `type`='CT'   GROUP BY `ticket_id`";
      }
      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }
      if ($ticket_type == 'BP' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('BP', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'BP' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE $userCon  AND `deletes` = '0' AND `type`='BP'  GROUP BY `ticket_id`";
      }
      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }
      if ($ticket_type == 'CP' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('CP', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'CP' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE $userCon  AND `deletes` = '0' AND `type`='CP'  GROUP BY `ticket_id`";
      }
      if ($ticket_type == 'all') {
        $query .= "UNION ";
      }

      if ($ticket_type == 'KT' || $ticket_type == 'all') {
        $query .= "SELECT `ticket_id`, GROUP_CONCAT(`my3number`) AS 'my3number', CONCAT('KT', '',`ticket_id`) AS 'ticketno', GROUP_CONCAT(`raffle_id`) AS 'raffle_id', 'KT' as `type` , GROUP_CONCAT(product_id) AS `product_id` FROM `ticket_lines` WHERE $userCon  AND `deletes` = '0' AND `type`='KT'  GROUP BY `ticket_id`";
      }

      $query .= ") AS `t`;";

      // var_dump($query);die;

      $ticketLineArray = DB::select($query);

      // dd($ticketLineArray);
      // $ticketLineArray = mysqli_fetch_all($ticket_lines, MYSQLI_ASSOC);

      $query2 = "SELECT t.*, draw.*, user_register.name AS 'uname' FROM (";
      if ($ticket_type == 'OT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'OT' as `type` FROM `ticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'MT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'MT' as `type` FROM `mticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'AT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'AT' as `type` FROM `aticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'FT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'FT' as `type` FROM `fticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'WT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'WT' as `type` FROM `wticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'CT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'CT' as `type` FROM `cticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'BP' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'BP' as `type` FROM `bpticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'CP' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'CP' as `type` FROM `cpticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'KT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon as 'TKcreated_at', net_total, transaction_id, total_lines,  'KT' as `type` FROM `kticket` WHERE $ticketCon  AND `deletes` = '0'";
      }

      $query2 .= ") AS t INNER JOIN draw ON draw.id = t.draw_id
                     INNER JOIN user_register ON user_register.id = t.user_id where draw.id != ''";

      if ($order_status == 'active') {
        $status_con = "AND `draw`.`status` = 'Active'";
      } else if ($order_status == 'past') {
        $status_con = "AND `draw`.`status` != 'Active'";
      } else {
        $status_con = '';
      }

      $query2 .= $status_con;
      $query2 .= " ORDER BY t.TKcreated_at DESC;";


      $ticketArray  = DB::select($query2);


      $productArr  = DB::select("SELECT * FROM `product` ORDER BY `product`.`id` ASC");




      $grandRaffleArr  = DB::select("SELECT * FROM `raffledraw` WHERE `status` = 'Completed';");

      $superRaffleArr  = DB::select("SELECT * FROM `superraffledraw` WHERE `status` = 'Completed';");

      $data['ticketArray'] = $ticketArray;
      $data['ticketLineArray'] = $ticketLineArray;
      $data['productArr'] = $productArr;
      $data['grandRaffleArr'] = $grandRaffleArr;
      $data['superRaffleArr'] = $superRaffleArr;

      $response = ['status' => 'success', 'message' => 'Ticket Data Collected', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
