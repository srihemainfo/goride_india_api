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

class TicketshowController extends Controller
{

  public function myticket(Request $request)
  {

    $user_id = auth()->user()->id;

    $year = $request->year;
    $ticket_type = $request->ticket_type;
    $order_status = $request->order_status;
    if ($user_id != '') {

      if ($ticket_type != '' && $order_status == 'past' || $order_status == 'active' || $order_status == 'all') {
        // dd('hgg');



        // ticket line show
        $ticketQuery = DB::table('ticket_lines')
          ->where('user_id', $user_id)
          ->where('ticket_lines.deletes', 0)
          ->leftjoin('draw', 'draw.id', '=', 'ticket_lines.draw_id')
          ->where('draw.id', '!=', '')->when($order_status == 'active', function ($query) {
            return $query->where('draw.status', 'Active');
          })
          ->when($order_status == 'past', function ($query) {
            return $query->where('draw.status', '!=', 'Active');
          });

        if ($year != '') {
          $ticketQuery->whereYear('ticket_lines.createdon', $year);
        }

        if ($ticket_type != 'all') {

          $ticketQuery->where('type', $ticket_type);
        }

        $ticketLineArray = $ticketQuery
          ->groupBy('ticket_id')
          ->select(
            'ticket_id',
            DB::raw('GROUP_CONCAT(my3number) as my3number'),
            DB::raw("CONCAT('$ticket_type', '', ticket_id) as ticketno"),
            DB::raw('GROUP_CONCAT(raffle_id) as raffle_id'),
            DB::raw('"' . $ticket_type . '" as type'),
            DB::raw('GROUP_CONCAT(product_id) as product_id')
          )
          ->get();
        // $ticketLineArray="";
      } else {
        // $ticketLineArray = "";
      }
      //  raffel ticket show
      $ticketTypes = ['ticket', 'aticket', 'bpticket', 'cpticket', 'cticket', 'fticket', 'kticket', 'mticket', 'wticket'];

      $ticketArray = [];

      if ($ticket_type == 'all') {
        foreach ($ticketTypes as $type) {
          $ticketQuery2 = DB::table($type)->select('ticket_lines.type')
            ->where($type . '.user_id', $user_id)
            ->where($type . '.deletes', '0')
            ->leftjoin('ticket_lines', $type . '.id', '=', 'ticket_lines.ticket_id');

          if ($year != '') {
            $ticketQuery2->whereYear($type . '.createdon', $year);
          }

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $result = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', $type . '.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', $type . '.user_id')
            ->where('draw.id', '!=', '')

            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy($type . '.createdon', 'DESC')
            ->select(
              $type . '.id as ticketid',
              $type . '.draw_id as draw_id', // Corrected here
              $type . '.user_id',
              $type . '.ticket_no',
              $type . '.invoice_no',
              $type . '.createdon',
              $type . '.net_total',
              $type . '.transaction_id',
              $type . '.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get()->toArray(); // Convert the collection to an array

          // Merge the results into $ticketArray
          $ticketArray = array_merge($ticketArray, $result);
        }
      } else {
        if ($ticket_type == 'OT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('ticket')->select('ticket_lines.type')
            ->where('ticket.user_id', $user_id)
            ->where('ticket.deletes', '0')
            ->leftjoin('ticket_lines', 'ticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('ticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'ticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'ticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('ticket.createdon', 'DESC')
            ->select(
              'ticket.id as ticketid',
              'draw.id as draw_id',
              'ticket.user_id',
              'ticket.ticket_no',
              'ticket.invoice_no',
              'ticket.createdon',
              'ticket.net_total',
              'ticket.transaction_id',
              'ticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();

          // dd($ticketArray);

        } elseif ($ticket_type == 'MT' || $ticket_type == 'all') {

          $ticketQuery2 = DB::table('mticket')->select('ticket_lines.type')
            ->where('mticket.user_id', $user_id)
            ->where('mticket.deletes', '0')
            ->leftjoin('ticket_lines', 'mticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('mticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'mticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'mticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('mticket.createdon', 'DESC')
            ->select(
              'mticket.id as ticketid',
              'draw.id as draw_id',
              'mticket.user_id',
              'mticket.ticket_no',
              'mticket.invoice_no',
              'mticket.createdon',
              'mticket.net_total',
              'mticket.transaction_id',
              'mticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'AT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('aticket')->select('ticket_lines.type')
            ->where('aticket.user_id', $user_id)
            ->where('aticket.deletes', '0')
            ->leftjoin('ticket_lines', 'aticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('aticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'aticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'aticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('aticket.createdon', 'DESC')
            ->select(
              'aticket.id as ticketid',
              'draw.id as draw_id',
              'aticket.user_id',
              'aticket.ticket_no',
              'aticket.invoice_no',
              'aticket.createdon',
              'aticket.net_total',
              'aticket.transaction_id',
              'aticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'FT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('fticket')->select('ticket_lines.type')
            ->where('fticket.user_id', $user_id)
            ->where('fticket.deletes', '0')
            ->leftjoin('ticket_lines', 'fticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('fticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'fticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'fticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('fticket.createdon', 'DESC')
            ->select(
              'fticket.id as ticketid',
              'draw.id as draw_id',
              'fticket.user_id',
              'fticket.ticket_no',
              'fticket.invoice_no',
              'fticket.createdon',
              'fticket.net_total',
              'fticket.transaction_id',
              'fticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'WT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('wticket')->select('ticket_lines.type')
            ->where('wticket.user_id', $user_id)
            ->where('wticket.deletes', '0')
            ->leftjoin('ticket_lines', 'wticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('wticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'wticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'wticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('wticket.createdon', 'DESC')
            ->select(
              'wticket.id as ticketid',
              'draw.id as draw_id',
              'wticket.user_id',
              'wticket.ticket_no',
              'wticket.invoice_no',
              'wticket.createdon',
              'wticket.net_total',
              'wticket.transaction_id',
              'wticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'CT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('cticket')->select('ticket_lines.type')
            ->where('cticket.user_id', $user_id)
            ->where('cticket.deletes', '0')
            ->leftjoin('ticket_lines', 'cticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('cticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'cticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'cticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('cticket.createdon', 'DESC')
            ->select(
              'cticket.id as ticketid',
              'draw.id as draw_id',
              'cticket.user_id',
              'cticket.ticket_no',
              'cticket.invoice_no',
              'cticket.createdon',
              'cticket.net_total',
              'cticket.transaction_id',
              'cticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'BP' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('bpticket')->select('ticket_lines.type')
            ->where('bpticket.user_id', $user_id)
            ->where('bpticket.deletes', '0')
            ->leftjoin('ticket_lines', 'bpticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('bpticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'bpticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'bpticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('bpticket.createdon', 'DESC')
            ->select(
              'bpticket.id as ticketid',
              'draw.id as draw_id',
              'bpticket.user_id',
              'bpticket.ticket_no',
              'bpticket.invoice_no',
              'bpticket.createdon',
              'bpticket.net_total',
              'bpticket.transaction_id',
              'bpticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'CP' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('cpticket')->select('ticket_lines.type')
            ->where('cpticket.user_id', $user_id)
            ->where('cpticket.deletes', '0')
            ->leftjoin('ticket_lines', 'cpticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('cpticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'cpticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'cpticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('cpticket.createdon', 'DESC')
            ->select(
              'cpticket.id as ticketid',
              'draw.id as draw_id',
              'cpticket.user_id',
              'cpticket.ticket_no',
              'cpticket.invoice_no',
              'cpticket.createdon',
              'cpticket.net_total',
              'cpticket.transaction_id',
              'cpticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        } elseif ($ticket_type == 'KT' || $ticket_type == 'all') {
          $ticketQuery2 = DB::table('kticket')->select('ticket_lines.type')
            ->where('kticket.user_id', $user_id)
            ->where('kticket.deletes', '0')
            ->leftjoin('ticket_lines', 'kticket.id', '=', 'ticket_lines.ticket_id');


          if ($year != '') {
            $ticketQuery2->whereYear('kticket.createdon', $year);
          }
          // $ticketQuery2 = DB::table('ticket_lines')->where('ticket_lines.deletes', '0');

          if ($ticket_type != 'all') {
            $ticketQuery2->where('ticket_lines.type', $ticket_type);
          }

          $ticketArray = $ticketQuery2
            ->leftjoin('draw', 'draw.id', '=', 'kticket.draw_id')
            ->leftjoin('user_register', 'user_register.id', '=', 'kticket.user_id')
            ->where('draw.id', '!=', '')
            ->when($order_status == 'active', function ($query) {
              return $query->where('draw.status', 'Active');
            })
            ->when($order_status == 'past', function ($query) {
              return $query->where('draw.status', '!=', 'Active');
            })
            ->orderBy('kticket.createdon', 'DESC')
            ->select(
              'kticket.id as ticketid',
              'draw.id as draw_id',
              'kticket.user_id',
              'kticket.ticket_no',
              'kticket.invoice_no',
              'kticket.createdon',
              'kticket.net_total',
              'kticket.transaction_id',
              'kticket.total_lines',
              DB::raw('"' . $ticket_type . '" as type'),
              'draw.status as draw_status',
              'draw.name as draw_name',
              'draw.result_datetime as draw_date',
              'user_register.name as uname'
            )
            ->groupBy('ticketid')->get();
        }
      }
      $productArr = DB::table('product')->orderBy('id', 'ASC')->get();


      $grandRaffleArr = DB::table('raffledraw')->where('status', 'Completed')->get();

      $response = [

        'status' => 'success',
        'message' => 'Ticket filder details show',
        'data' => [
          'ticketArray' => $ticketArray,
          'ticketLineArray' => $ticketLineArray,
          'productArr' => $productArr,
          'grandRaffleArr' => $grandRaffleArr
        ]
      ];
      return response($response);
    }
  }

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
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'OT' as `type` FROM `ticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'MT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'MT' as `type` FROM `mticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'AT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'AT' as `type` FROM `aticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'FT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'FT' as `type` FROM `fticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'WT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'WT' as `type` FROM `wticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'CT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'CT' as `type` FROM `cticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'BP' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'BP' as `type` FROM `bpticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'CP' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'CP' as `type` FROM `cpticket` WHERE $ticketCon  AND `deletes` = '0'";
      }
      if ($ticket_type == 'all') {
        $query2 .= "UNION ";
      }
      if ($ticket_type == 'KT' || $ticket_type == 'all') {
        $query2 .= "SELECT id as ticketid, draw_id, user_id, ticket_no, invoice_no, createdon , net_total, transaction_id, total_lines,  'KT' as `type` FROM `kticket` WHERE $ticketCon  AND `deletes` = '0'";
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
      $query2 .= " ORDER BY t.createdon DESC;";

      // var_dump($query2);die;
      // $tickets = mysqli_query($con, $query2);
      // $ticketArray = mysqli_fetch_all($tickets, MYSQLI_ASSOC);

      $ticketArray  = DB::select($query2);
      // dd($ticketArray);

      // $query3 = mysqli_query($con, "SELECT * FROM `product` ORDER BY `product`.`id` ASC");
      // $productArr = mysqli_fetch_all($query3, MYSQLI_ASSOC);

      $productArr  = DB::select("SELECT * FROM `product` ORDER BY `product`.`id` ASC");


      // $query4 = mysqli_query($con, "SELECT * FROM `raffledraw` WHERE `status` = 'Completed';");
      // $grandRaffleArr = mysqli_fetch_all($query4, MYSQLI_ASSOC);

      $grandRaffleArr  = DB::select("SELECT * FROM `raffledraw` WHERE `status` = 'Completed';");

      // dd($grandRaffleArr);

      $data['ticketArray'] = $ticketArray;
      $data['ticketLineArray'] = $ticketLineArray;
      $data['productArr'] = $productArr;
      $data['grandRaffleArr'] = $grandRaffleArr;

      // $utm_source = (isset($request->utm_source) && $request->utm_source != '' && $request->utm_source != null) ? $request->utm_source : "";
      // $utm_medium = (isset($request->utm_medium) && $request->utm_medium != '' && $request->utm_medium != null) ? $request->utm_medium : "";
      // $utm_campaign  = (isset($request->utm_campaign) && $request->utm_campaign != '' && $request->utm_campaign != null) ? $request->utm_campaign : "";
      // $user_id = $request->user_id != '' ? $request->user_id : '0';

      // // dd($request->subid2);


      // $subid2 = (isset($request->subid2) && $request->subid2 != '' && $request->subid2 != null) ? $request->subid2 : "";

      // $ip = $request->ip();

      // $fd = date('Y-m-d H:i:s', strtotime('-1 day', strtotime(now())));
      // $td = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime(now())));



      // $check_track = DB::select("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' ORDER BY `id` DESC LIMIT 1;");

      // if (count($check_track) > 0) {

      //   $withdraw_arr = [
      //     "ip" => $ip,
      //     "subid1" => $request->subid1,
      //     "url" =>  $request->url,
      //     "before_dm_id" => $check_track[0]->id,
      //     "subid2" => $subid2,
      //     "utm_campaign" => $utm_campaign,
      //     "utm_source" =>  $utm_source,
      //     "uniqueid" => '',
      //     "user_id" => $request->user_id,
      //     "status" => '0',
      //     "deletes" => '0',
      //     "createdon" => now()
      //   ];

      //   // $check_status = DB::table('digital_market')
      //   //   ->where('ip', $ip)
      //   //   ->where('subid1', $request->subid1)
      //   //   ->where('status', '0')
      //   //   ->whereBetween('createdon', [$fd, $td])
      //   //   ->where('deletes', '0')
      //   //   ->orderByDesc('id')
      //   //   ->limit(1)
      //   //   ->get();


      //   $check_status = DB::select("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' AND `subid1` LIKE '$request->subid1' ORDER BY `id` DESC LIMIT 1;");


      //   // dd("SELECT *  FROM `digital_market` WHERE `ip` LIKE '$ip' AND `createdon` BETWEEN '$fd' AND '$td' AND `deletes` = '0' AND `status` = '0' AND `subid1` LIKE '$request->subid1' ORDER BY `id` DESC LIMIT 1;");
      //   if (count($check_status) < 1) {
      //     $with_draw_ins = DB::table('digital_market')->insertGetId($withdraw_arr);
      //     $data['track_id'] = $with_draw_ins;

      //     $response = ['status' => 'success', 'message' => 'Process Done', 'data' => $data];
      //     goto returnFVI;
      //   } else {
      //     $response = ['status' => 'failed', 'message' => 'Process Done', 'error' => 'No Data Found'];
      //     goto returnFVI;
      //   }
      // } else {

      //   $withdraw_arr = [
      //     "ip" => $ip,
      //     "subid1" => $request->subid1,
      //     "url" =>  $request->url,
      //     "before_dm_id" => '0',
      //     "subid2" => $subid2,
      //     "utm_campaign" => $utm_campaign,
      //     "utm_source" =>  $utm_source,
      //     "uniqueid" => '',
      //     "user_id" => $request->user_id,
      //     "status" => '0',
      //     "deletes" => '0',
      //     "createdon" => now()
      //   ];

      //   $with_draw_ins =  DB::table('digital_market')->insertGetId($withdraw_arr);

      //   $data['track_id'] = $with_draw_ins;

      //   $response = ['status' => 'success', 'message' => 'Process Done', 'data' => $data];
      //   goto returnFVI;
      // }


      $response = ['status' => 'success', 'message' => 'Ticket Data Collected', 'data' => $data];
      goto returnFVI;

      returnFVI:
      return response()->json($response);
    } catch (Exception $e) {

      $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
      return response()->json($response);
    }
  }
}
