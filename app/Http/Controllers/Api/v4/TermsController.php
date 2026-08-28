<?php
namespace App\Http\Controllers\Api\v4;
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


class TermsController extends Controller{
// get terms & conditions details fuinction
    public function terms(Request $request){

        $get_term = DB::table('termsconditions')->select(
            'termsconditions.id',
            'termsconditions.name',
            'termsconditions.orderno',
          )->where('deletes','0')->orderBy('orderno', 'ASC')->get();
        //  dd($get_term);
          if ($get_term) {
      
            $response = [
      
              'status' => 'success',
              'message' => 'Terms & condition deatils show',
              'data' => [
                'terms' => $get_term
              ]
            ];
            return response($response);
          } else {
            $response = [
      
              'status' => 'failure',
              'message' => 'Terms & condition not show !',
              'error' => ''
            ];
            return response($response);

    }

}

}

