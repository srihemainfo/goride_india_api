<?php
namespace App\Http\Controllers\Api;
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


class FaqControllers extends Controller{
    // get Faq details fuinction

    public function faq(){

        $get_faq = DB::table('faq')->select(
            'faq.id',
            'faq.ques',
            'faq.ans',
            'faq.orderno',
          )->where('deletes','0')->orderBy('orderno', 'ASC')->get();
        //  dd($get_faq);
          if ($get_faq) {
      
            $response = [
      
              'status' => 'success',
              'message' => 'Faq deatils show',
              'data' => [
                'faq' => $get_faq
              ]
            ];
            return response($response);
          } else {
            $response = [
      
              'status' => 'failure',
              'message' => 'Faq deatils not show !',
              'error' => ''
            ];
            return response($response);

    }

}
}