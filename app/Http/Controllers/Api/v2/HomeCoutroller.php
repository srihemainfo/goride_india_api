<?php

namespace App\Http\Controllers\Api\v2;

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


class HomeCoutroller extends Controller
{
    // get home  details function

    public function home(Request $request)
    {



        // $data = DB::select(DB::raw("SELECT `id`, `draw_no` ,`first`,`result_datetime` FROM (SELECT * FROM `draw` WHERE `status`='Completed' AND `deletes`='0' ORDER BY `id` DESC LIMIT 3) AS `test` ORDER BY `id` ASC"));


        // $total = DB::select(DB::raw('
        // SELECT (
        //     SELECT COUNT(*) FROM winnerlist) + (SELECT COUNT(*) FROM raffledraw WHERE status = "Completed" AND deletes = 0) AS total'));
        // $trow = $total[0];

        // if ($data) {



        //     $data2 = DB::table('testimonials')
        //         ->select('testimonials.id', 'testimonials.thumbnail', 'testimonials.name', 'testimonials.prizeAMT')
        //         ->where(['states' => '0', 'deletes' => '0'])
        //         ->orderBy('prizeAMT', 'DESC')
        //         ->get();

        //     $testimonials = [];

        //     if ($data2->count() > 0) {
        //         foreach ($data2 as $testimonial) {
        //             $id = $testimonial->id;
        //             $image = ($request->header('Origin') . '/') . $testimonial->thumbnail;
        //             $name = $testimonial->name;
        //             $prize = $testimonial->prizeAMT;

        //             $testimonials[] = [
        //                 'id' => $id,
        //                 'image' => $image,
        //                 'name' => $name,
        //                 'prize' => $prize,
        //             ];
        //         }
        //     }

        //     $response = [
        //         'status' => 'success',
        //         'message' => 'Home page details show',
        //         'data' => [
        //             'lastdraw' => $data,
        //             'total_winners' => $trow->total,
        //             'testimonials' => $testimonials,
        //         ]

        //     ];

        //     return response()->json($response);



        //     foreach ($data as $testimonial) {
        //         $id = $testimonial->id;
        //         $image = ($request->header('Origin') . '/') . $testimonial->thumbnail;
        //         $name = $testimonial->name;
        //         $prize = $testimonial->prizeAMT;

        //         // Add each testimonial to the array
        //         $testimonials[] = [
        //             'id' => $id,
        //             'image' => $image,
        //             'name' => $name,
        //             'prize' => $prize,
        //         ];
        //     }

        //     // if ($data) {

        //     //     $response = [

        //     //         'status' => 'success',
        //     //         'message' => 'Last draw deatils show',
        //     //         'data' => [
        //     //             'last_draw' => $data,

        //     //         ]
        //     //     ];
        //     //     return response($response);

        //     // } else {
        //     //     $response = [

        //     //         'status' => 'failure',
        //     //         'message' => 'data not available',
        //     //         'error' => 'no data available'
        //     //     ];
        //     //     return response($response);
        // }
        // } else {
        //     $response = [

        //         'status' => 'failed',
        //         'message' => 'No data available',
        //         'data' => 'no data available',

        //     ];

        //     return response($response);
        // }
    }
}
