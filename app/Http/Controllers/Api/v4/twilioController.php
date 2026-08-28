<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;
use Twilio\Exceptions\TwilioException;
use DB;
// use GuzzleHttp\Client;

class twilioController extends Controller
{
    public function twilioSMS(Request $request)
    {
        try {
            // $token = $request->bearerToken();


            if ($request->senderName === 'DRAW') {
                $request->mobileNo = Controller::BlockSQLInjection($request->mobileNo);
                if ($request->mobileNo == '' || $request->mobileNo == null || $request->mobileNo == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please select valid mobile No!', 'error' => 'Please select valid mobile No!'];
                    goto returnFVI;
                }

                $request->message = Controller::BlockSQLInjection($request->message);
                if ($request->message == '' || $request->message == null || $request->message == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please select valid mobile No!', 'error' => 'Please select valid mobile No!'];
                    goto returnFVI;
                }

                $smsLog_arr = [
                    'isResend' => (isset($request->resendStatus) && $request->resendStatus === 'YES' ? $request->resendStatus : 'NO'),
                    'gateway' => '', 'mobile' => $request->mobileNo, 'details' => $request->message, 'ip' => ($request->ip() ?? ''), 'datetime' => date("Y-m-d H:i:s"), 'status' => '', 'site' => 'CUSTOMER', 'REQ_Time' => date("Y-m-d H:i:s"), 'RES_Time' => date("Y-m-d H:i:s"), 'smssendstatus' => '1', 'response' => '', 'token_response' => '', 'subject' => '', 'reference_id' => '', 'smsdetails' => '', 'smsstatus' => ''
                ];
                $smslogID = DB::table('smslog')->insertGetId($smsLog_arr);
                // $smslogID = DB::getPdo()->lastInsertId();






                // $client = new Client();

                // $requestBody = [
                //     'To' => '+' . strval($request->mobileNo),
                //     'From' => env('TWILIO_PHONE_NUMBER'),
                //     'Body' => $request->message,
                //     'StatusCallback' => env('TWILIO_CALLBACK_URL')
                // ];

                // $response = $client->request('POST', 'https://api.twilio.com/2010-04-01/Accounts/' . env('TWILIO_SID') . '/Messages.json', [
                //     'auth' => [env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN')],
                //     'form_params' => $requestBody
                // ]);


                // // Get the response body as a string
                // $responseData = json_decode($response->getBody()->getContents(), true);
                // // $responseData = $response->json();


                // // $resultResponse = $sendSms->toArray();

                // $update = DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($requestBody), 'status' => json_encode($responseData), 'reference_id' => $responseData['sid'] ?? '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'twilio', 'smsstatus' => $responseData['status'] ?? '']);

                // if (isset($responseData['sid']) && $responseData['sid'] != '') {
                //     $response = ['status' => 'success', 'message' => 'SMS Sent Successfully!', 'data' => ['gateWayResponse' => $responseData]];
                //     goto returnFVI;
                // } else {
                //     $response = ['status' => 'failed', 'message' => 'Failed to send SMS!', 'error' => $responseData];
                //     goto returnFVI;
                // }
                // dd($responseData['sid']);


                try {
                    $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
                    $sendSms =  $client->messages->create(
                        // Where to send a text message (your cell phone?)
                        '+' . strval($request->mobileNo),
                        array(
                            'from' => env('TWILIO_PHONE_NUMBER'),
                            'body' => $request->message,
                            'statusCallback' => env('TiwilioCallBack')
                        )
                    );
                } catch (TwilioException $e) {
                    // Handle error
                    // $errorMessage = $e->getMessage();
                    // Log or handle the error as needed

                    $response = ['status' => 'failed', 'message' => 'Proccess Failed', 'error' => $e->getMessage()];
                    DB::table('smslog')->where('id', $smslogID)->update(['token_response' => '', 'status' => json_encode($response), 'reference_id' => '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'twilio', 'smsstatus' => 'Failed']);
                    goto returnFVI;
                }

                $resultResponse = $sendSms->toArray();

                DB::table('smslog')->where('id', $smslogID)->update(['token_response' => '', 'status' => json_encode($resultResponse), 'reference_id' => $resultResponse['sid'] ?? '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'twilio', 'smsstatus' => $resultResponse['status'] ?? '']);

                if (isset($resultResponse['sid']) && $resultResponse['sid'] != '') {
                    $response = ['status' => 'success', 'message' => 'SMS Sent Successfully!', 'data' => ['gateWayResponse' => $resultResponse]];
                    goto returnFVI;
                } else {
                    $response = ['status' => 'failed', 'message' => 'Failed to send SMS!', 'error' => $resultResponse];
                    goto returnFVI;
                }
            } else {
                $response = ['status' => 'failed', 'message' => 'Please use Valid Sender Name!', 'error' => 'Please use Valid Sender Name!'];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
}
