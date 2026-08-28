<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\HtmlString;
use Carbon\Carbon;
use App\Http\Controllers\Api\twilioController;
use App\Http\Controllers\Api\AdminApiController;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    
    //  public function redirectToPage($url)
    // {
    //     return response()->make('<script>window.location = "' . $url . '";</script>');
    // }
    
    
    
public function getDaysInMonthOrYear($type, $value = 1, $startDate = null)
{
   
    $startDate = $startDate ?? Carbon::now();

    
    if ($type == 'month') {
        
        return $startDate->diffInDays($startDate->copy()->addMonths($value));
    } 
  
    elseif ($type == 'year') {
        
        if ($value >= 1) {
            $futureYear = $startDate->copy()->addYears($value)->year;
            return Carbon::create($futureYear, 1, 1)->daysInYear;
        } else {
          
            return 0;
        }
    } 
  
    else {
        return 0;
    }
}


// public function getDaysInMonthOrYear($type, $value = 1)
// {
//     if ($type == 'month') {
//           return Carbon::now()->diffInDays(Carbon::now()->addMonths($value));
//     } elseif ($type == 'year') {
//         if ($value >= 1) {
//             $futureYear = Carbon::now()->addYears($value)->year; 
//             return Carbon::create($futureYear, 1, 1)->daysInYear;
//         } else {
//             return 0;
//         }
//     } else {
//         return 0;
//     }
// }
    
    // OTP
    public function generateOTP($len)
    {
        try {
            $generator = "135792468";
            $result = "";
            for ($i = 1; $i <= $len; $i++) {
                $result .= substr($generator, (rand() % (strlen($generator))), 1);
            }
            return $result;
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    


    // Error Log
    function error_log_new($ip, $type, $userid, $email, $mobile, $message, $request, $path, $file_name, $line_no)
    {
        try {
            // Direct Insert Start
            $insertData = [
                'ip' => $ip,
                'type' => $type,
                'userid' => $userid,
                'email' => $email,
                'mobile' => $mobile,
                'message' => $message,
                'request' => $request,
                'path' => $path,
                'file_name' => $file_name,
                'line_no' => $line_no,
                'createdon' => now(),
            ];

            $inserted = DB::table('error_log')->insert($insertData);
            return $inserted;
            // Direct Insert End

            // Log API Start
            // $body = [
            //     'method' => 'errorLog',
            //     'ip' => $ip,
            //     'type' => $type,
            //     'userid' => $userid,
            //     'email' => $email,
            //     'mobile' => $mobile,
            //     'message' => $message,
            //     'request' => json_decode($request, true),
            //     'path' => $path,
            //     'file_name' => $file_name,
            //     'line_no' => $line_no

            // ];

            // $headers =  [
            //     'Content-Type' => 'application/json'
            // ];


            // $response = Http::withHeaders($headers)->post(env('Log_API'), $body);
            // return true;
            // Log API End

        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    // Email
    public function composeEmail($user_ip, $email, $subject, $message, $frmID = '', $ccAddress = [])
    {


        try {
            // return true;

            require base_path("vendor/autoload.php");
            $mail = new PHPMailer(true);     // Passing `true` enables exceptions

            // dd($email);
            $i = 0;
            $datetime = date("Y-m-d H:i:s");
            $htmlString = new HtmlString($message);



            // Send Email Via CRON
            // $insertlog =  DB::table('emaillog')->insert([
            //     'details' =>  $htmlString->toHtml(),
            //     'subject' => $subject,
            //     'email' => $email,
            //     'ip' => $user_ip,
            //     'datetime' => $datetime,
            //     'status' => 0,
            //     'fromemail' => ''
            // ]);
            // if ($insertlog) {
            //     return true;
            // } else {
            //     return false;
            // }

            $insertlog = DB::table('emaillog')->insert([
                'details' => $htmlString->toHtml(),
                'subject' => $subject,
                'email' => $email,
                'ip' => $user_ip,
                'datetime' => $datetime,
                'sendstatus' => 'PENDING',
                'status' => 1,
                'fromemail' => ''
            ]);
            $insert_id = DB::getPdo()->lastInsertId();
            $mail->isSMTP();

            recheckMail:
            $fromMail = '';
            if ($frmID != '') {
                $conT = "`emailkey` = '$frmID' ORDER BY `id` DESC LIMIT 1";
            } else {
                $conT = "`emailkey` = 'all' ORDER BY `id` DESC LIMIT 1";
            }


            $email_config = DB::table('email_config')->where('deletes', '0')
                ->where('status', '0')
                ->whereRaw($conT)->get();

            if ($email_config->count() > 0) {

                $mail->SMTPDebug = false;
                // $row = mysqli_fetch_assoc($get_Email);
                $fromMail = $email_config[0]->setFrom;
                $smtpAu = boolval($email_config[0]->SMTPAuth) ? true : false;
                $mail->Host = $email_config[0]->Host;
                $mail->SMTPAuth = $smtpAu;
                $mail->Username = $email_config[0]->Username;
                $mail->Password = $email_config[0]->Password;
                $mail->SMTPSecure = $email_config[0]->SMTPSecure;
                $mail->Port = $email_config[0]->Port;
                $mail->setFrom($fromMail, $email_config[0]->fromname);
                $mail->AddReplyTo($email_config[0]->AddReplyTo, $email_config[0]->fromname);
                if ($email_config[0]->char_set != '') {
                    $mail->CharSet = $email_config[0]->char_set;
                }

                if ($email_config[0]->Encoding != '') {
                    $mail->Encoding = $email_config[0]->Encoding;
                }
            } else {
                $frmID = '';
                if ($i == 0) {
                    $i++;
                    goto recheckMail;
                }
            }


            $mail->addAddress($email);

            if (count($ccAddress) > 0) {
                foreach ($ccAddress as $ccAddress) {
                    $mail->addCC($ccAddress);
                }
            }


            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            if (!$mail->send()) {
                $er = json_encode($mail->ErrorInfo);

                $update = DB::table('emaillog')->where('id', $insert_id)
                    ->update([
                        'sendstatus' => 'FAILED',
                        'error_info' => $er,
                        'fromemail' => $fromMail,
                    ]);

                return false;
            } else {
                $er = json_encode($mail->ErrorInfo);
                $update = DB::table('emaillog')->where('id', $insert_id)
                    ->update([
                        'sendstatus' => 'SUCCESS',
                        'error_info' => $er,
                        'fromemail' => $fromMail,
                    ]);

                return true;
            }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    // General Request
    function invokeApiRequest($type, $url, $headers, $post)
    {







        $curl = curl_init();







        curl_setopt_array($curl, array(







            CURLOPT_URL => $url,







            CURLOPT_RETURNTRANSFER => true,







            CURLOPT_ENCODING => '',







            CURLOPT_MAXREDIRS => 10,







            CURLOPT_TIMEOUT => 0,







            CURLOPT_FOLLOWLOCATION => true,







            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,







            CURLOPT_CUSTOMREQUEST => $type,







            CURLOPT_POSTFIELDS => $post,







            CURLOPT_HTTPHEADER => $headers,







        ));







        $response = curl_exec($curl);







        curl_close($curl);







        return json_decode($response);
    }

    // General Request
    function requestAPI($type, $URL, $headers = [], $post = [])
    {
        $curl = curl_init();

        $data = [
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_SSL_VERIFYPEER => false
        ];

        if (!empty($headers) && is_array($headers)) {
            $data[CURLOPT_HTTPHEADER] = $headers;
        }

        if (!empty($post)) {
            if (is_array($post)) {
                $data[CURLOPT_POSTFIELDS] = http_build_query($post);
            } else {
                $data[CURLOPT_POSTFIELDS] = $post;
            }
        }

        curl_setopt_array($curl, $data);

        $response = curl_exec($curl);

        if ($response === false) {
            // Handle the error, e.g., return an error message
            return json_encode(['error' => curl_error($curl)]);
        }

        curl_close($curl);

        return $response;
    }

    // WhatsApp
    function sendWhatsApp($request)
    {
        try {



            // $getSettings = DB::table('whatsapp_switch')->where('site', 'customersite')->where('deletes', '0')->get();
            // if ($getSettings->count() > 0) {

            //     if ($getSettings[0]->gateway == 'greenapi') {


            //         // Green API Start
            //         $key = "26bb7fbec9a340d8b5d162000068af3ef0fd632014bc45e5ae";
            //         $Instance = "7103926844";
            //         $checkURL = "https://api.green-api.com/waInstance" . $Instance . "/checkWhatsapp/" . $key;
            //         $headers = [
            //             'Content-Type: application/json'
            //         ];
            //         $body = [
            //             'phoneNumber' => strval($request['mobile'])
            //         ];
            //         $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));


            //         $result = json_decode($response);

            //         if (isset($result->existsWhatsapp) && $result->existsWhatsapp) {

            //             $smslogID = DB::table('smslog')->insertGetId([
            //                 'gateway' => 'greenapi',
            //                 'mobile' => $request['mobile'],
            //                 'details' => addslashes($request['messages']),
            //                 'ip' => '',
            //                 'datetime' => date("Y-m-d H:i:s"),
            //                 'status' => '',
            //                 'site' => 'CUSTOMER',
            //                 'REQ_Time' => date("Y-m-d H:i:s"),
            //                 'RES_Time' => date("Y-m-d H:i:s"),
            //                 'smssendstatus' => '1',
            //                 'response' => '',
            //                 'token_response' => '',
            //                 'subject' => '',
            //                 'reference_id' => '',
            //                 'smsdetails' => '',
            //                 'smsstatus' => ''
            //             ]);


            //             $body = [
            //                 'chatId' => strval($request['mobile']) . '@c.us',
            //                 'message' => $request['messages']
            //             ];
            //             $sendURL = 'https://api.green-api.com/waInstance' . $Instance . '/sendMessage/' . $key;
            //             $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));



            //             $result = json_decode($response);
            //             DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result), 'reference_id' => $result->idMessage ?? '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'greenapi', 'smsstatus' => '']);
            //             if (isset($result->idMessage) && $result->idMessage != '') {
            //                 return true;
            //             } else {
            //                 return false;
            //             }
            //         } else {
            //             return false;
            //         }
            //         // Green API End
            //     } else if ($getSettings[0]->gateway == 'doubletick') {


            // Double Tick Whatsapp
            $headers = [
                'Content-Type: application/json'
            ];
            $body = [
                'senderName' => "DRAW",
                'mobileNo' => strval($request['mobile']),
                // 'message' => trim($request['message']),
                'templateName' => $request['templateName'],
                'language' => $request['language'],
                'templateBodyParam' => $request['templateBodyParam'],
                'buttons' => isset($request['buttons']) ? $request['buttons'] : []
            ];

            $response = $this->requestAPI('POST', env('APP_URL') . 'api/dtSendTemplate', $headers, json_encode($body));
            // dd($response);
            $result = json_decode($response);

            if ($result->status == 'success') {
                return true;
            } else {
                return false;
            }
            //     } else {
            //         return false;
            //     }
            // } else {
            //     return false;
            // }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    // SMS
    function sendsms($mobile, $messages, $templateid)
    {
        try {
            // return true;
            $senderName = 'DRAW';


            if (substr(strval($mobile), 0, 3) == "971") {

                // Send Whatsapp For ALL Numbers
                // $sendWhatsApp = $this->sendWhatsApp($mobile, $messages);

                $getSettings = DB::table('sms_switch')->where('site', 'customersite')->where('deletes', '0')->get();
                if ($getSettings->count() > 0) {

                    // if ($getSettings[0]->gateway == 'twilio') {
                    //     $response = $this->requestAPI('POST', env('APP_URL') . 'api/twilioSMS', $headers, json_encode($body));
                    //     // dd($response);
                    //     $result = json_decode($response);

                    //     if ($result->status == 'success') {
                    //         return true;
                    //     } else {
                    //         return false;
                    //     }
                    // } else {

                    $smsLog_arr = ['gateway' => '', 'mobile' => $mobile, 'details' => $messages, 'ip' => $templateid, 'datetime' => date("Y-m-d H:i:s"), 'status' => '', 'site' => 'CUSTOMER', 'REQ_Time' => date("Y-m-d H:i:s"), 'RES_Time' => date("Y-m-d H:i:s"), 'smssendstatus' => '1', 'response' => '', 'token_response' => '', 'subject' => '', 'reference_id' => '', 'smsdetails' => '', 'smsstatus' => ''];
                    $insertData = DB::table('smslog')->insert($smsLog_arr);
                    $smslogID = DB::getPdo()->lastInsertId();

                    // if ($getSettings[0]->gateway == 'cequens') {



                    //     $API_KEY = '3c6cafa2-73e6-4f30-ae27-0ccddb6e3511';



                    //     $USER_NAME = 'Draw';



                    //     $TOKEN_REQUEST_URL = 'https://apis.cequens.com/auth/v1/tokens/';



                    //     $SEND_SMS_URL = 'https://apis.cequens.com/sms/v1/messages';



                    //     $idHead = [



                    //         'Accept: application/json',



                    //         'Content-Type: application/json',



                    //     ];



                    //     $TokenRequetBody = [];



                    //     $TokenRequetBody['apiKey'] = $API_KEY;



                    //     $TokenRequetBody['userName'] = $USER_NAME;



                    //     $getToken = $this->invokeApiRequest("POST", $TOKEN_REQUEST_URL, $idHead, json_encode($TokenRequetBody));



                    //     $tokenreponse = json_encode($getToken);



                    //     // Log

                    //     DB::table('smslog')->where('id', $smslogID)->update(['gateway' => 'cequens', 'token_response' => $tokenreponse]);



                    //     $replyCode = (int) $getToken->replyCode;



                    //     $token = $getToken->data->access_token;







                    //     if ($token != '' && $replyCode === 0) {







                    //         $idHead = [



                    //             'Accept: application/json',



                    //             'Content-Type: application/json',



                    //             'Authorization: bearer ' . $token,



                    //         ];



                    //         $SendMessageBody = [];



                    //         $SendMessageBody['senderName'] = "National Draw";



                    //         $SendMessageBody['messageType'] = "text";



                    //         // $SendMessageBody->acknowledgement = 0;



                    //         // $SendMessageBody->flashing = 0;



                    //         $SendMessageBody['messageText'] = $messages;



                    //         $SendMessageBody['recipients'] = strval($mobile);

                    //         $SendMessageBody['shortURL'] = false;


                    //         $sendSMS = $this->invokeApiRequest("POST", $SEND_SMS_URL, $idHead, json_encode($SendMessageBody));


                    //         // dd($sendSMS);

                    //         $smsReplyCode = (int) $sendSMS->replyCode;



                    //         $response[] = array();



                    //         if ($smsReplyCode === 0) {





                    //             // Log

                    //             DB::table('smslog')->where('id', $smslogID)->update(['status' => json_encode($sendSMS), 'reference_id' => $sendSMS->data->SentSMSIDs[0]->SMSId, 'RES_Time' => date("Y-m-d H:i:s")]);

                    //             $response['status'] = "success";
                    //         } else {



                    //             // Log

                    //             DB::table('smslog')->where('id', $smslogID)->update(['status' => json_encode($sendSMS), 'reference_id' => $sendSMS->requestId, 'RES_Time' => date("Y-m-d H:i:s")]);

                    //             $response['status'] = "failed";
                    //         }
                    //     } else {



                    //         $response['status'] = "failed";
                    //     }



                    //     $response = json_encode($response);



                    //     return json_decode($response);
                    // } else if ($getSettings[0]->gateway == 'dataslice') {







                    //     $url = 'http://smsplus.radiosms.ca/send_sms.php?';



                    //     $ch = curl_init($url);



                    //     curl_setopt($ch, CURLOPT_POST, true);



                    //     curl_setopt($ch, CURLOPT_POSTFIELDS, 'from=' . "National Draw" . '&to=' . $mobile . '&msg=' . $messages . '&user=nationaldrawuae&password=draw@234&camp=test_bulk&dr=1');



                    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



                    //     $response = curl_exec($ch);



                    //     curl_close($ch);



                    //     $result = json_decode($response);

                    //     $reference_id = $result->received;

                    //     // Log

                    //     DB::table('smslog')->where('id', $smslogID)->update(['status' => $response, 'reference_id' => $reference_id, 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'dataslice']);

                    //     return json_decode($response);
                    // } else if ($getSettings[0]->gateway  == 'expresso') {
                    //     return false;
                    //     $sender = 'National Draw';
                    //     // $data = "username=nationaldrawuae&apiId=H5ZtneJG&json=True&destination=" . $mobile . "&source=" . $sender . "&text=" . $messages;
                    //     // $ch = curl_init('https://sms.montymobile.com/API/SendSMS?');
                    //     // curl_setopt($ch, CURLOPT_POST, true);
                    //     // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    //     // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //     // $response = curl_exec($ch);
                    //     // curl_close($ch);
                    //     // $reference_id = json_decode($response);
                    //     // dd($response);
                    //     // Log



                    //     $response = Http::post('https://sms.montymobile.com/API/SendSMS?', [
                    //         'username' => 'nationaldrawuae',
                    //         'apiId' => 'H5ZtneJG',
                    //         'json' => 'True',
                    //         'destination' => $mobile,
                    //         'source' => $sender,
                    //         'text' => $messages
                    //     ]);

                    //     // dd($response);

                    //     DB::table('smslog')->where('id', $smslogID)->update(['status' => $response, 'reference_id' => $reference_id, 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'expresso']);
                    // } else if ($getSettings[0]->gateway  == 'precise') {
                    //     // return false;
                    //     $sender = 'National Draw';
                    //     $userName = 'nationaldrawuaeOTP';
                    //     $passWord = 'nationaldrawuae$2024';
                    //     $Authorization = base64_encode($userName . ':' . $passWord);


                    //     $headers = [
                    //         'Content-Type: application/json',
                    //     ];
                    //     $body = [
                    //         'Authorization' => $Authorization,
                    //         'MobileNumbers' => [
                    //             strval($mobile)
                    //         ],
                    //         'Message' => $messages,
                    //         'SenderName' => $sender,
                    //         'ReportRequired' => true
                    //     ];



                    //     $response = $this->requestAPI('POST', 'https://restapi.tobeprecisesms.com/api/SendSMS/SingleSMS?Username=' . $userName . '&Password=' . $passWord, $headers, json_encode($body));

                    //     $result = json_decode($response);

                    //     // dd($result);



                    //     DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result), 'reference_id' => $result->data[0]->msgId ?? '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'precise', 'smsstatus' => $result->data[0]->details ?? '']);
                    //     if ($result->status == 'OK') {
                    //         return true;
                    //     } else {
                    //         return false;
                    //     }
                    // }


                    if ($getSettings[0]->gateway == 'brandmaster') {

                        $headers = [
                            'Content-Type: application/json',
                            'Authorization: Basic bmF0aW9uYWxkcmF3OkFudU9YZ05C'
                        ];

                        $body = [
                            'source' => "NATL DRAW",
                            'destination' => [
                                strval($mobile)
                            ],
                            'text' => $messages
                            // 'SenderName' => $sender,
                            // 'ReportRequired' => true
                        ];



                        $response = $this->requestAPI('POST', 'https://portal.smshub.live/API/SendBulkSMS', $headers, json_encode($body));

                        $result = json_decode($response);

                        // dd($result);



                        DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result[0]), 'reference_id' => $result[0]->Id ?? '', 'RES_Time' => date("Y-m-d H:i:s"), 'gateway' => 'brandmaster', 'smsstatus' => $result[0]->Description ?? '']);
                        if ($result[0]->Description == 'Success') {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    // }
                } else {
                    return false;
                }
            } else {

                $body = [
                    'senderName' => $senderName,
                    'mobileNo' => strval($mobile),
                    'message' => trim($messages),
                    // 'ip' => $request->ip()
                ];

                $twilioController = new twilioController();
                $requestData = new Request($body);
                $response = $twilioController->twilioSMS($requestData);
                $responseData = json_decode($response->getContent(), true);


                // dd($responseData);

                if ($responseData['status'] === 'success') {
                    return true;
                } else {
                    return false;
                }

                // dd($responseData);
                // return false;

                // $response = $this->requestAPI('POST', env('APP_URL') . 'api/twilioSMS', $headers, json_encode($body));
                // $result = json_decode($response);
                // if ($result->status == 'success') {
                //     return true;
                // } else {
                //     return false;
                // }

            }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    // Active Draw Data
    public function getActiveDrawData()
    {

        try {
            $response = [];
            $datas = [];

            $activeDraw = DB::table('draw')
                ->where([
                    ['saleDate', '>=', date('Y-m-d')],
                    ['deletes', '=', '0'],
                    ['dailyThirllStatus', '=', 'Active']
                ])
                ->orderBy('saleDate', 'ASC')
                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'startDate', 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                ->limit(1)
                ->get();

            $displayDrawDate = DB::table('draw')
                ->where([
                    ['resultDate', '>=', ((date('G') >= 19) ? date('Y-m-d', strtotime('+1 day')) : date('Y-m-d'))],
                    ['deletes', '=', '0'],
                    ['dailyThirllStatus', '=', 'Active']
                ])
                ->orderBy('saleDate', 'ASC')
                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'startDate', 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                ->limit(1)
                ->get();
            $datas['displayDraw'] = [];
            if ($displayDrawDate->count() > 0) {
                $datas['displayDraw'] = $displayDrawDate[0];
                $datas['displayDraw']->salesStrategyFormula = json_decode($displayDrawDate[0]->salesStrategyFormula, true);
            }

            $lastDraw = DB::table('draw')
                ->where([
                    ['deletes', '=', '0'],
                    // ['dailyThirllStatus', 'IN', 'Active', 'Completed']
                ])
                ->whereIn('dailyThirllStatus', ['Active', 'Completed'])
                ->orderBy('saleDate', 'DESC')
                ->select('id', 'startDate', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'salesStrategyFormula', 'todayGoldPrize', 'previewData', 'deletes')
                ->limit(1)
                ->get();

            $datas['activeDraw'] = [];

            $productListCollect = DB::table('product')
                ->where([
                    ['deletes', '=', '0'],
                    ['type', '=', 'PRODUCT']
                ])
                ->whereNotIn('id', [1, 2])
                ->orderBy('id', 'ASC')
                ->select(DB::raw('CAST(eligibleDraw AS JSON) AS eligibleDraw'), 'qty', 'id', 'name', 'raffleQuantity', DB::raw("CONCAT('" . env('DO_REDIRECT_URL') . "', image) AS image"), 'rate', 'validityDays', 'type', 'description', 'chances', 'maxPrize', 'eligibleDrawCount')
                ->get();


            if ($activeDraw->count() > 0 && $lastDraw->count() > 0) {


                // Ticket Selling Concept 
                $datas['activeDraw'] = $activeDraw[0];
                $datas['activeDraw']->salesStrategyFormula = json_decode($activeDraw[0]->salesStrategyFormula);

                // Last Draw
                $datas['lastDraw'] = $lastDraw[0];
                $datas['lastDraw']->salesStrategyFormula = json_decode($lastDraw[0]->salesStrategyFormula);


                // Product List
                $productList = [];

                if ($productListCollect && $productListCollect->count() > 0) {

                    // NEW
                    $datas['productList'] = collect($productListCollect)->map(function ($item) use ($lastDraw, $activeDraw) {
                        $item->eligibleDraw = json_decode($item->eligibleDraw, true);
                        $item->name = $item->qty . ' ' . $item->name;

                        if (in_array($item->id, [4])) {
                            $specificDate = Carbon::createFromDate(
                                Carbon::parse($lastDraw[0]->saleDate)->format('Y'),
                                Carbon::parse($lastDraw[0]->saleDate)->format('m'),
                                Carbon::parse($lastDraw[0]->saleDate)->format('d')
                            );

                            $daysDifference = Carbon::parse($activeDraw[0]->saleDate)->diffInDays(Carbon::parse($specificDate)) + 1;
                            $endDate = Carbon::parse(date('Y-m-d', strtotime($activeDraw[0]->saleDate)))->addDays($daysDifference)->format('Y-m-d');
                        } else {
                            $endDate = Carbon::parse($activeDraw[0]->saleDate)->addDays($item->validityDays)->format('Y-m-d');
                        }

                        $noOfDraw = 0;
                        if ($item->eligibleDraw['is_thrill']) {
                            $thirllDraw = DB::table('draw')
                                ->where([
                                    ['resultDate', '>', date('Y-m-d')],
                                    ['resultDate', '<=', $endDate],
                                    ['deletes', '=', '0'],
                                    ['dailyThirllStatus', '=', 'Active']
                                ])
                                ->orderBy('resultDate', 'ASC')
                                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                                ->count();
                            if (in_array($item->id, [3, 4])) {
                                $item->validityDays = $thirllDraw;
                            }

                            $noOfDraw += $thirllDraw;
                        }
                        if ($item->eligibleDraw['is_weekly']) {
                            $noOfDraw += DB::table('draw')
                                ->where([
                                    ['resultDate', '>', date('Y-m-d')],
                                    ['resultDate', '<=', $endDate],
                                    ['deletes', '=', '0'],
                                    ['weeklyBoosterStatus', '=', 'Active']
                                ])
                                ->orderBy('resultDate', 'ASC')
                                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                                ->count();
                        }
                        if ($item->eligibleDraw['is_bumper']) {
                            $noOfDraw += DB::table('draw')
                                ->where([
                                    ['resultDate', '>', date('Y-m-d')],
                                    ['resultDate', '<=', $endDate],
                                    ['deletes', '=', '0'],
                                    ['monthlyBumperStatus', '=', 'Active'],
                                    ['monthlyBumperPrice', '<=', $item->maxPrize]
                                ])
                                ->orderBy('resultDate', 'ASC')
                                ->select(DB::raw('CAST(salesStrategyFormula AS JSON) AS salesStrategyFormula'), 'id', 'sid', 'saleDate', 'resultDate', 'dailyDrawNo', 'dailyThrillName', 'dailyThirllPrice', 'winThirllRaffleIds', 'dailyPermitNo', 'dailyThirllStatus', 'weeklyDrawNo', 'weeklyBoosterName', 'weeklyBoosterPrice', 'winweeklyBoosterIds', 'weeklyPermitNo', 'weeklyBoosterStatus', 'bumperDrawNo', 'monthlyBumperName', 'monthlyBumperPrice', 'winBumperRaffleIds', 'bumperPermitNo', 'monthlyBumperStatus', 'todayGoldPrize', 'previewData', 'deletes', 'createdon')
                                ->count();
                        }
                        $item->eligibleDrawCount = $noOfDraw;
                        $item->chances = $item->eligibleDrawCount * $item->raffleQuantity;



                        /// New 31 5 2024
                        $item->discountAmt = 0;
                        $item->discountID = null;

                        $txTime = date('Y-m-d H:i:s');
                        $discountLike = DB::table('discount_periods')
                            ->where('type', '=', 'general')
                            ->where('start_date', '<', $txTime)
                            ->where('end_date', '>', $txTime)
                            ->where('deletes', '0')
                            ->where('discount_amount', '>', 0)
                            ->where('product_id', '=', $item->id)
                            ->limit(1)
                            ->get();

                        if ($discountLike->count() > 0) {
                            $item->discountAmt = floatval($discountLike[0]->discount_amount);

                            $item->discountID = $discountLike[0]->id;
                            $item->discountRate = floatval($item->rate - $discountLike[0]->discount_amount);
                        }

                        $item->stackStatus = ($item->id === 1 || $item->id === 2) ? false : true;

                        // Temp
                        $item->rate = $item->id == 3 ? 15 : 75;

                        $item->image = $item->id == 4 ? 'https://nationalasset.blr1.digitaloceanspaces.com/nationaldraw/1/PROLL4.png' : $item->image;


                        return $item;
                    })->all();
                }

                // $datas['productList'] = $productList;
            } else {
                if ($productListCollect && $productListCollect->count() > 0) {
                    $datas['productList'] = collect($productListCollect)->map(function ($item) use ($lastDraw, $activeDraw) {
                        $item->eligibleDraw = json_decode($item->eligibleDraw, true);
                        $item->name = $item->qty . ' ' . $item->name;



                        /// New 31 5 2024
                        $item->discountAmt = 0;
                        $item->discountID = null;

                        $txTime = date('Y-m-d H:i:s');
                        $discountLike = DB::table('discount_periods')
                            ->where('type', '=', 'general')
                            ->where('start_date', '<', $txTime)
                            ->where('end_date', '>', $txTime)
                            ->where('deletes', '0')
                            ->where('discount_amount', '>', 0)
                            ->where('product_id', '=', $item->id)
                            ->orderBy('id', 'DESC')
                            ->limit(1)
                            ->get();

                        if ($discountLike->count() > 0) {
                            $item->discountAmt = floatval($discountLike[0]->discount_amount);

                            $item->discountID = $discountLike[0]->id;
                            $item->discountRate = floatval($item->rate - $discountLike[0]->discount_amount);
                        }

                        $item->stackStatus = ($item->id === 1 || $item->id === 2) ? false : true;

                        return $item;
                    })->all();
                }
            }

            $response = ['status' => 'success', 'message' => 'Active Draw Details Collected!', 'data' => $datas];
            goto returnFVI;

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process Failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    // function raffleDrawDate($format)
    // {
    //     try {
    //         // $purchaseDatetime = now(); // Replace with your purchase_datetime value

    //         $drawQuery1 = DB::table('raffledraw')->where('ticket_start_datetime', '<', now())
    //             ->where('ticket_end_datetime', '>', now())
    //             ->where('deletes', '0')
    //             ->orderBy('result_datetime', 'ASC')
    //             ->limit(1)
    //             ->first();

    //         if ($drawQuery1) {
    //             return date($format, strtotime($drawQuery1->result_datetime));
    //         }
    //     } catch (Exception $e) {

    //         $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
    //         return response()->json($response);
    //     }
    // }

    public function BlockSQLInjection($query, $specialChar = true)
    {
        try {
            $defaultInjection = [
                "null",
                "*",
                "create",
                "drop",
                "truncate",
                "1,1",
                "https",
                "http",
                "top 0 ",
                "top 1 ",
                "benchmark",
                "union",
                "root",
                "delay",
                "true",
                "false",
                "getRequestString",
                "schema",
                "syscolumns",
                "sysobjects",
                "dump",
                "sleep",
                "ascii",
                "extractvalue",
                "database",
                "null",
                "version",
                "shutdown",
                "declare",
                "begin",
                "not in",
                "not exist",
                "isnull",
                "load",
                "convert",
                "pytW",
                " 1 ",
                "%",
                "||",
                " 0 ",
                "injectx"
            ];

            // Rearrange the query to remove potentially malicious characters
            $query = preg_replace('/[\r\n]+/', ' ', $query); // Remove line breaks and multiple spaces
            $query = trim($query);
            // $query = str_replace(array('=', '-', '_', '&', '^', '*', '|', '?', '#', '"', "'", ';'), ' ', $query);
            if ($specialChar) {
                // Network gateway used the "-" so its removed.
                $query = str_replace(array('=', '_', '&', '^', '*', '|', '?', '#', '"', "'", ';'), ' ', $query);
            }

            // Check if the query contains any default injection keywords
            foreach ($defaultInjection as $injection) {
                if (stripos($query, $injection) !== false) {
                    // var_dump($injection);die;
                    return ""; // Query contains a banned keyword, return an empty string
                }
            }

            return $query; // Query is considered safe
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    public function sendNotificationCopy($request)
    {
        try {

            $senderName = 'DRAW';
            $mobile = $request['mobile'];
            $gateway = '';
            $resendStatus = 'NO';
            $country = (substr(strval($mobile), 0, 3) == "971") ? 'UAE' : 'NONUAE';
            $messages = $request['messages'];

            // $getSettings = DB::table('settings')->where('id', '1')->limit(1)->get();
            // if ($getSettings->count() > 0) {

            // $res = json_decode($getSettings[0]->customerSiteNotfication, true);
            // $request['resend'] = true;


            // if (isset($request['resend']) && $request['resend']) {
            //     $resendStatus = 'YES';
            //     $gateway = $res['RESEND' . $country];
            // } else {
            //     $gateway = $res[$country];
            // }

            // if ($gateway == '') {
            //     return false;
            // }


            // if ((isset($gateway['brandmaster']) && $gateway['brandmaster']) || (isset($gateway['greenapi']) && $gateway['greenapi'])) {
            //     $smsLog_arr = ['isResend' => $resendStatus, 'gateway' => '', 'mobile' => $mobile, 'details' => $messages, 'ip' => '', 'datetime' => date("Y-m-d H:i:s"), 'status' => '', 'site' => 'CUSTOMER', 'REQ_Time' => date("Y-m-d H:i:s"), 'RES_Time' => date("Y-m-d H:i:s"), 'smssendstatus' => '1', 'response' => '', 'token_response' => '', 'subject' => '', 'reference_id' => '', 'smsdetails' => '', 'smsstatus' => ''];
            //     $smslogID = DB::table('smslog')->insertGetId($smsLog_arr);
            // }

            // if (isset($gateway['brandmaster']) && $gateway['brandmaster']) {

            //     $headers = [
            //         'Content-Type: application/json',
            //         'Authorization: Basic bmF0aW9uYWxkcmF3OkFudU9YZ05C'
            //     ];

            //     $body = [
            //         'source' => "NATL DRAW",
            //         'destination' => [
            //             strval($mobile)
            //         ],
            //         'text' => $messages
            //         // 'SenderName' => $sender,
            //         // 'ReportRequired' => true
            //     ];



            //     $response = $this->requestAPI('POST', 'https://portal.smshub.live/API/SendBulkSMS', $headers, json_encode($body));

            //     $result = json_decode($response);





            //     DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result[0]), 'reference_id' => $result[0]->Id ?? '', 'RES_Time' => date("Y-m-d H:i:s"), 'gateway' => 'brandmaster', 'smsstatus' => $result[0]->Description ?? '']);
            //     if ($result[0]->Description == 'Success') {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } else if (isset($gateway['twilio']) && $gateway['twilio']) {

            //     $body = [
            //         'senderName' => $senderName,
            //         'mobileNo' => strval($mobile),
            //         'message' => trim($messages),
            //         // 'ip' => $request->ip()
            //         'resendStatus' => $resendStatus
            //     ];

            //     $twilioController = new twilioController();
            //     $requestData = new Request($body);
            //     $response = $twilioController->twilioSMS($requestData);
            //     $responseData = json_decode($response->getContent(), true);

            //     // dd($responseData);

            //     // dd($responseData);

            //     if ($responseData['status'] === 'success') {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } else if (isset($gateway['greenapi']) && $gateway['greenapi']) {


            // Green API Start
            $key = env('SHIWhatsAppAPIKey');
            $Instance = env('SHIWhatsAppInstance');

            $checkURL = env('SHIWhatsAppEndPoint') . "client/isRegisteredUser/" . $Instance;
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'x-api-key: ' . $key
            ];

            $body = [
                'number' => strval($request['mobile'])
            ];

            $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));


            $result = json_decode($response);

            if (isset($result->success) && $result->success && $result->result) {

                $smslogID = DB::table('smslog')->insertGetId([
                    'gateway' => 'shiwhatsapp',
                    'mobile' => $request['mobile'],
                    'details' => addslashes($request['messages']),
                    'ip' => '',
                    'datetime' => date("Y-m-d H:i:s"),
                    'status' => '',
                    'site' => 'CUSTOMER',
                    'REQ_Time' => date("Y-m-d H:i:s"),
                    'RES_Time' => date("Y-m-d H:i:s"),
                    'smssendstatus' => '1',
                    'response' => '',
                    'token_response' => '',
                    'subject' => '',
                    'reference_id' => '',
                    'smsdetails' => '',
                    'smsstatus' => ''
                ]);

                // dd($smslogID );

                $sendURL = env('SHIWhatsAppEndPoint') . "client/sendMessage/" . $Instance;
                if (
                    isset($request['urlFile']) && $request['urlFile'] != null && $request['urlFile'] != ''
                    && isset($request['fileName']) && $request['fileName'] != null && $request['fileName'] != ''
                ) {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        'urlFile' => $request['urlFile'],
                        'fileName' => $request['fileName'],
                        'caption' => $messages
                    ];

                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                } else {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        'contentType' => 'string',
                        'content' => $request['messages']
                    ];
                    // $sendURL = env('SHIWhatsAppEndPoint') . $Instance . '/sendMessage/' . $key;
                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                }





                $result = json_decode($response);


                // dd($result->data->_data->id->id);


                DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result), 'reference_id' => $result->data->_data->id->id ?? '', 'RES_Time' => date("Y-m-d H:i:s"), 'gateway' => 'shiwhatsapp', 'smsstatus' => '']);
                if (isset($result->success) && $result->success != '') {
                    return true;
                } else {
                    return false;
                }
                // } else {
                //     return false;
                // }
                // Green API End
            }


            // else if (isset($gateway['doubletick']) && $gateway['doubletick']) {


            //     // Double Tick Whatsapp
            //     $headers = [
            //         'Content-Type: application/json'
            //     ];
            //     $body = [
            //         'senderName' => "DRAW",
            //         'mobileNo' => strval($request['mobile']),
            //         // 'message' => trim($request['message']),
            //         'templateName' => $request['templateName'],
            //         'language' => $request['language'],
            //         'templateBodyParam' => $request['templateBodyParam'],
            //         'resendStatus' => $resendStatus,
            //         'buttons' => isset($request['buttons']) ? $request['buttons'] : []
            //     ];

            //     $response = $this->requestAPI('POST', env('APP_URL') . 'api/dtSendTemplate', $headers, json_encode($body));
            //     // dd($response);
            //     $result = json_decode($response);

            //     if ($result->status == 'success') {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } 
            else {
                return false;
            }

            // dd($mobile);
            // } else {
            //     return false;
            // }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return false;
            return response()->json($response);
        }
    }
    
    public function sendNotification($request)
    {
        try {

            $senderName = 'DRAW';
            $mobile = $request['mobile'];
            $gateway = '';
            $resendStatus = 'NO';
            $country = (substr(strval($mobile), 0, 3) == "971") ? 'UAE' : 'NONUAE';
            $messages = $request['messages'];


            // Green API Start
            $key = env('SHIWhatsAppAPIKey');
            $Instance = env('SHIWhatsAppInstance');

            $checkURL = env('SHIWhatsAppEndPoint') . "waInstance" . $Instance . '/checkWhatsapp/'.$key;
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                // 'x-api-key: ' . $key
            ];

            $body = [
                'phoneNumber' => strval($request['mobile'])
            ];

            $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));


            $result = json_decode($response);

            if (isset($result->existsWhatsapp) && $result->existsWhatsapp) {

                $smslogID = DB::table('smslog')->insertGetId([
                    'gateway' => 'greenwhatsapp',
                    'mobile' => $request['mobile'],
                    'details' => addslashes($request['messages']),
                    'ip' => '',
                    'datetime' => date("Y-m-d H:i:s"),
                    'status' => '',
                    'site' => 'CUSTOMER',
                    'REQ_Time' => date("Y-m-d H:i:s"),
                    'RES_Time' => date("Y-m-d H:i:s"),
                    'smssendstatus' => '1',
                    'response' => '',
                    'token_response' => '',
                    'subject' => '',
                    'reference_id' => '',
                    'smsdetails' => '',
                    'smsstatus' => ''
                ]);

                // dd($smslogID );

                $sendURL = env('SHIWhatsAppEndPoint') . "waInstance" . $Instance . '/sendMessage/'.$key;
                if (
                    isset($request['urlFile']) && $request['urlFile'] != null && $request['urlFile'] != ''
                    && isset($request['fileName']) && $request['fileName'] != null && $request['fileName'] != ''
                ) {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        'urlFile' => $request['urlFile'],
                        'fileName' => $request['fileName'],
                        'caption' => $messages,
                        "customPreview" => ['title' => 'TEXT_MESSAGE']
                    ];

                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                } else {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        // 'contentType' => 'string',
                        'message' => $request['messages'],
                        "customPreview" => ['title' => 'TEXT_MESSAGE']
                    ];
                    // $sendURL = env('SHIWhatsAppEndPoint') . $Instance . '/sendMessage/' . $key;
                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                }





                $result = json_decode($response);


                // dd($result->data->_data->id->id);


                DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result), 'reference_id' => $result->data->_data->id->id ?? '', 'RES_Time' => date("Y-m-d H:i:s"), 'gateway' => 'greenwhatsapp', 'smsstatus' => '']);
                if (isset($result->idMessage) && $result->idMessage != '') {
                    return true;
                } else {
                    return false;
                }
                // } else {
                //     return false;
                // }
                // Green API End
            }


            // else if (isset($gateway['doubletick']) && $gateway['doubletick']) {


            //     // Double Tick Whatsapp
            //     $headers = [
            //         'Content-Type: application/json'
            //     ];
            //     $body = [
            //         'senderName' => "DRAW",
            //         'mobileNo' => strval($request['mobile']),
            //         // 'message' => trim($request['message']),
            //         'templateName' => $request['templateName'],
            //         'language' => $request['language'],
            //         'templateBodyParam' => $request['templateBodyParam'],
            //         'resendStatus' => $resendStatus,
            //         'buttons' => isset($request['buttons']) ? $request['buttons'] : []
            //     ];

            //     $response = $this->requestAPI('POST', env('APP_URL') . 'api/dtSendTemplate', $headers, json_encode($body));
            //     // dd($response);
            //     $result = json_decode($response);

            //     if ($result->status == 'success') {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } 
            else {
                return false;
            }

            // dd($mobile);
            // } else {
            //     return false;
            // }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return false;
            return response()->json($response);
        }
    }
    
    public function sendNotificationUK($request)
    {
        try {

            $senderName = 'DRAW';
            $mobile = $request['mobile'];
            $gateway = '';
            $resendStatus = 'NO';
            $country = (substr(strval($mobile), 0, 3) == "971") ? 'UAE' : 'NONUAE';
            $messages = $request['messages'];


            // Green API Start
            $key = env('SHIWhatsAppAPIKey');
            $Instance = env('SHIWhatsAppInstance');

            $checkURL = env('SHIWhatsAppEndPoint') . "waInstance" . $Instance . '/checkWhatsapp/'.$key;
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                // 'x-api-key: ' . $key
            ];

            $body = [
                'phoneNumber' => strval($request['mobile'])
            ];

            $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));


            $result = json_decode($response);

            if (isset($result->existsWhatsapp) && $result->existsWhatsapp) {

                $smslogID = DB::table('smslog')->insertGetId([
                    'gateway' => 'greenwhatsapp',
                    'mobile' => $request['mobile'],
                    'details' => addslashes($request['messages']),
                    'ip' => '',
                    'datetime' => date("Y-m-d H:i:s"),
                    'status' => '',
                    'site' => 'CUSTOMER',
                    'REQ_Time' => date("Y-m-d H:i:s"),
                    'RES_Time' => date("Y-m-d H:i:s"),
                    'smssendstatus' => '1',
                    'response' => '',
                    'token_response' => '',
                    'subject' => '',
                    'reference_id' => '',
                    'smsdetails' => '',
                    'smsstatus' => ''
                ]);

                // dd($smslogID );

                $sendURL = env('SHIWhatsAppEndPoint') . "waInstance" . $Instance . '/sendMessage/'.$key;
                if (
                    isset($request['urlFile']) && $request['urlFile'] != null && $request['urlFile'] != ''
                    && isset($request['fileName']) && $request['fileName'] != null && $request['fileName'] != ''
                ) {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        'urlFile' => $request['urlFile'],
                        'fileName' => $request['fileName'],
                        'caption' => $messages,
                        "customPreview" => ['title' => 'TEXT_MESSAGE']
                    ];

                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                } else {
                    $body = [
                        'chatId' => strval($request['mobile']) . '@c.us',
                        // 'contentType' => 'string',
                        'message' => $request['messages'],
                        "customPreview" => ['title' => 'TEXT_MESSAGE']
                    ];
                    // $sendURL = env('SHIWhatsAppEndPoint') . $Instance . '/sendMessage/' . $key;
                    $response = $this->requestAPI('POST', $sendURL, $headers, json_encode($body));
                }





                $result = json_decode($response);


                // dd($result->data->_data->id->id);


                DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($body), 'status' => json_encode($result), 'reference_id' => $result->data->_data->id->id ?? '', 'RES_Time' => date("Y-m-d H:i:s"), 'gateway' => 'greenwhatsapp', 'smsstatus' => '']);
                if (isset($result->idMessage) && $result->idMessage != '') {
                    return true;
                } else {
                    return false;
                }
                // } else {
                //     return false;
                // }
                // Green API End
            }


            // else if (isset($gateway['doubletick']) && $gateway['doubletick']) {


            //     // Double Tick Whatsapp
            //     $headers = [
            //         'Content-Type: application/json'
            //     ];
            //     $body = [
            //         'senderName' => "DRAW",
            //         'mobileNo' => strval($request['mobile']),
            //         // 'message' => trim($request['message']),
            //         'templateName' => $request['templateName'],
            //         'language' => $request['language'],
            //         'templateBodyParam' => $request['templateBodyParam'],
            //         'resendStatus' => $resendStatus,
            //         'buttons' => isset($request['buttons']) ? $request['buttons'] : []
            //     ];

            //     $response = $this->requestAPI('POST', env('APP_URL') . 'api/dtSendTemplate', $headers, json_encode($body));
            //     // dd($response);
            //     $result = json_decode($response);

            //     if ($result->status == 'success') {
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } 
            else {
                return false;
            }

            // dd($mobile);
            // } else {
            //     return false;
            // }
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return false;
            return response()->json($response);
        }
    }
    
    // public function smsNotification($request, $txt)
    // {
    //     try {
            
            
    //         $smslogID = DB::table('smslog')->insertGetId([
    //             'gateway' => 'smsportal',
    //             'mobile' => $request['mobile'],
    //             'details' => addslashes($request['messages']),
    //             'ip' => '',
    //             'datetime' => date("Y-m-d H:i:s"),
    //             'status' => '',
    //             'site' => 'CUSTOMER',
    //             'REQ_Time' => date("Y-m-d H:i:s"),
    //             'RES_Time' => date("Y-m-d H:i:s"),
    //             'smssendstatus' => '0',
    //             'response' => '',
    //             'token_response' => '',
    //             'subject' => '',
    //             'reference_id' => '',
    //             'smsdetails' => '',
    //             'smsstatus' => '',
    //         ]);
            
    //         $get_type = '';
            
    //         if($txt == 'verify'){
    //             $get_type = env('SMS_VERIFY_ID');
    //         }elseif($txt == 'forgot'){
    //             $get_type = env('SMS_FORGOT_ID');
    //         }elseif($txt == 'account_details'){
    //             $get_type = env('SMS_ACCOUNT_DETAIL_ID');
    //         }
        
    //         $checkURL = env('SMS_ENDPOINT') .
    //             "smsapi?key=" . env('SMS_KEY') .
    //             "&route=" . env('SMS_ROUTE') .
    //             "&sender=" . env('SMS_SENDER') .
    //             "&number=" . $request['mobile'] .
    //             "&sms=" . urlencode($request['messages']) .
    //             "&templateid=" . $get_type;
        
    //         $headers = [];
    //         $body = [];
        
    //         $response = $this->requestAPI('GET', $checkURL, $headers, json_encode($body));
    //         $response = trim($response);
        
    //         $errorMessages = [
    //             '101' => 'Invalid user',
    //             '102' => 'Invalid sender ID',
    //             '103' => 'Invalid contact(s)',
    //             '104' => 'Invalid route',
    //             '105' => 'Invalid message',
    //             '106' => 'Spam blocked',
    //             '107' => 'Promotional block',
    //             '108' => 'Low credits in the specified route',
    //             '109' => 'Promotional route active only from 9am to 8:45pm',
    //             '110' => 'Invalid DLT Template ID',
    //         ];
        
    //         if (is_numeric($response)) {
                
    //             if (array_key_exists($response, $errorMessages)) {
    //                 DB::table('smslog')->where('id', $smslogID)->update([
    //                     'RES_Time' => now(),
    //                     'response' => $response,
    //                     'smsstatus' => 'FAILED',
    //                     'smsdetails' => $errorMessages[$response],
    //                     'smssendstatus' => '0',
    //                 ]);
                    
    //                 return false;
        
    //                 // return response()->json([
    //                 //     'status' => 'failed',
    //                 //     'code' => $response,
    //                 //     'error' => $errorMessages[$response],
    //                 // ]);
    //             }
        
    //             $messageId = $response;
        
    //             $dlrURL = env('SMS_ENDPOINT') .
    //                 "dlrapi?key=" . env('SMS_KEY') .
    //                 "&messageid=" . $messageId;
        
    //             $dlrResponse = $this->requestAPI('GET', $dlrURL, $headers, json_encode($body));
    //             $dlrResponse = trim($dlrResponse);
        
    //             $dlrData = json_decode($dlrResponse, true);
                
    //             $deliveryStatus = strtolower($dlrData[0]['status']);
    //             $deliveryTime = $dlrData[0]['time'] ?? now();
    
    //             DB::table('smslog')->where('id', $smslogID)->update([
    //                 'RES_Time' => now(),
    //                 'response' => $dlrResponse,
    //                 'reference_id' => $messageId,
    //                 'smsstatus' => $dlrData[0]['status'],
    //                 'smsdetails' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent')
    //                     ? 'SMS delivered successfully'
    //                     : "SMS not yet delivered ({$deliveryStatus})",
    //                 'smssendstatus' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') ? '1' : '0',
    //             ]);
    
    //             if ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') {
    //                 // return response()->json([
    //                 //     'status' => 'success',
    //                 //     'message' => 'SMS delivered successfully',
    //                 //     'message_id' => $messageId,
    //                 //     'delivery_time' => $deliveryTime,
    //                 // ]);
                    
    //                 return true;
    //             } else {
    //                 // return response()->json([
    //                 //     'status' => 'pending',
    //                 //     'message' => "SMS not yet delivered (Current Status: {$deliveryStatus})",
    //                 //     'message_id' => $messageId,
    //                 //     'delivery_time' => $deliveryTime,
    //                 // ]);
                    
    //                 return false;
    //             }
        
    //             // if (json_last_error() === JSON_ERROR_NONE && empty($dlrData) && isset($dlrData[0]['status'])) {
                    
    //             // } else {
    //             //     DB::table('smslog')->where('id', $smslogID)->update([
    //             //         'RES_Time' => now(),
    //             //         'response' => $dlrResponse,
    //             //         'smsstatus' => 'UNKNOWN',
    //             //         'smsdetails' => 'Unable to fetch DLR response',
    //             //     ]);
        
    //             //     // return response()->json([
    //             //     //     'status' => 'unknown',
    //             //     //     'message' => 'Unable to fetch delivery report',
    //             //     //     'message_id' => $messageId,
    //             //     //     'raw_response' => $dlrResponse,
    //             //     // ]);
    //             //     return true;
    //             // }
    //         }
        
    //         DB::table('smslog')->where('id', $smslogID)->update([
    //             'RES_Time' => now(),
    //             'response' => $response,
    //             'smsstatus' => 'INVALID',
    //             'smsdetails' => 'Invalid or non-numeric response',
    //         ]);
        
    //         // return response()->json([
    //         //     'status' => 'unknown',
    //         //     'response' => $response,
    //         // ]);
            
    //         return false;
    //     } catch (Exception $e) {
    //         $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
    //         return false;
    //         return response()->json($response);
    //     }
    // }
    
     public function smsNotification($request, $txt)
    {
        try {
            
            
            $smslogID = DB::table('smslog')->insertGetId([
                'gateway' => 'smsportal',
                'mobile' => $request['mobile'],
                'details' => addslashes($request['messages']),
                'ip' => '',
                'datetime' => date("Y-m-d H:i:s"),
                'status' => '',
                'site' => 'CUSTOMER',
                'REQ_Time' => date("Y-m-d H:i:s"),
                'RES_Time' => date("Y-m-d H:i:s"),
                'smssendstatus' => '0',
                'response' => '',
                'token_response' => '',
                'subject' => '',
                'reference_id' => '',
                'smsdetails' => '',
                'smsstatus' => '',
            ]);
            
            $get_type = '';
            
            $sms_via = DB::table('settings')->whereNotNull('mess_config')->first();
            
            if($txt == 'verify'){
                $get_type = env('SMS_VERIFY_ID');
            }elseif($txt == 'forgot'){
                $get_type = env('SMS_FORGOT_ID');
            }elseif($txt == 'account_details'){
                $get_type = env('SMS_ACCOUNT_DETAIL_ID');
            }elseif($txt == 'kyc_verified'){
                $get_type = env('SMS_KYC_VERIFY_ID');
            }
            
            if ($sms_via && $sms_via->mess_config == 'smsresell' && in_array($txt, ['verify', 'forgot'], true) ) {
            
                $endpoint = env('SMS_RESELL_ENDPOINT');
                $user     = env('SMS_RESELL_USER');
                $pass     = env('SMS_RESELL_PASS');
                $sender   = env('SMS_RESELL_SENDER');
                $priority = env('SMS_RESELL_PRIORITY');
                $type     = env('SMS_RESELL_TYPE');
            
                $mobile = preg_replace('/^91/', '', $request['mobile']);
            
                $sendURL = $endpoint . 'sendmsg.php?' . http_build_query([
                    'user'     => $user,
                    'pass'     => $pass,
                    'sender'   => $sender,
                    'phone'    => $mobile,
                    'text'     => $request['messages'],
                    'priority' => $priority,
                    'stype'    => $type,
                ]);
            
                $messageId = trim((string) $this->requestAPI('GET', $sendURL));
            
                if ($messageId == '' || !preg_match('/^S\.\d+$/', $messageId)) {
            
                    DB::table('api_error_log')->insert([
                        'url'        => $sendURL,
                        'method'     => 'GET',
                        'payload'    => null,
                        'message'    => 'SMS send failed. Invalid response: ' . ($messageId ?: 'EMPTY'),
                        'status'     => 200,
                        'file'       => __FILE__,
                        'line'       => __LINE__,
                        'trace'      => null,
                        'ip'         => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'headers'    => json_encode(request()->headers->all()),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    return false;
                }
            
                $dlrURL = $endpoint . 'recdlr.php?' . http_build_query([
                    'user'    => $user,
                    'msgid'   => $messageId,
                    'phone'   => $mobile,
                    'msgtype' => $priority,
                ]);
            
                $dlrResponse = trim((string) $this->requestAPI('GET', $dlrURL));
            
                if ($dlrResponse === '') {
            
                    DB::table('api_error_log')->insert([
                        'url'        => $dlrURL,
                        'method'     => 'GET',
                        'payload'    => null,
                        'message'    => 'DLR check failed. Empty response',
                        'status'     => 200,
                        'file'       => __FILE__,
                        'line'       => __LINE__,
                        'trace'      => null,
                        'ip'         => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'headers'    => json_encode(request()->headers->all()),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    return false;
                }
            
                $status = strtolower($dlrResponse);
            
                DB::table('smslog')
                    ->where('id', $smslogID)
                    ->update([
                        'RES_Time'      => now(),
                        'response'      => $dlrResponse,
                        'reference_id'  => $messageId,
                        'smsstatus'     => $dlrResponse,
                        'smsdetails'    => in_array($status, ['sent', 'delivered'], true)
                            ? 'SMS delivered successfully'
                            : "SMS not delivered ({$status})",
                        'smssendstatus' => in_array($status, ['sent', 'delivered'], true) ? '1' : '0',
                    ]);
            
                return in_array($status, ['sent', 'delivered'], true);
                
            } else {
                
                $checkURL = env('SMS_ENDPOINT') .
                    "smsapi?key=" . env('SMS_KEY') .
                    "&route=" . env('SMS_ROUTE') .
                    "&sender=" . env('SMS_SENDER') .
                    "&number=" . $request['mobile'] .
                    "&sms=" . urlencode($request['messages']) .
                    "&templateid=" . $get_type;
            
                $headers = [];
                $body = [];
            
                $response = $this->requestAPI('GET', $checkURL, $headers, json_encode($body));
                $response = trim($response);
            
                $errorMessages = [
                    '101' => 'Invalid user',
                    '102' => 'Invalid sender ID',
                    '103' => 'Invalid contact(s)',
                    '104' => 'Invalid route',
                    '105' => 'Invalid message',
                    '106' => 'Spam blocked',
                    '107' => 'Promotional block',
                    '108' => 'Low credits in the specified route',
                    '109' => 'Promotional route active only from 9am to 8:45pm',
                    '110' => 'Invalid DLT Template ID',
                ];
            
                if (is_numeric($response)) {
                    
                    if (array_key_exists($response, $errorMessages)) {
                        DB::table('smslog')->where('id', $smslogID)->update([
                            'RES_Time' => now(),
                            'response' => $response,
                            'smsstatus' => 'FAILED',
                            'smsdetails' => $errorMessages[$response],
                            'smssendstatus' => '0',
                        ]);
                        
                        return false;
                    }
            
                    $messageId = $response;
            
                    $dlrURL = env('SMS_ENDPOINT') .
                        "dlrapi?key=" . env('SMS_KEY') .
                        "&messageid=" . $messageId;
            
                    $dlrResponse = $this->requestAPI('GET', $dlrURL, $headers, json_encode($body));
                    $dlrResponse = trim($dlrResponse);
            
                    $dlrData = json_decode($dlrResponse, true);
                    
                    $deliveryStatus = strtolower($dlrData[0]['status']);
                    $deliveryTime = $dlrData[0]['time'] ?? now();
        
                    DB::table('smslog')->where('id', $smslogID)->update([
                        'RES_Time' => now(),
                        'response' => $dlrResponse,
                        'reference_id' => $messageId,
                        'smsstatus' => $dlrData[0]['status'],
                        'smsdetails' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent')
                            ? 'SMS delivered successfully'
                            : "SMS not yet delivered ({$deliveryStatus})",
                        'smssendstatus' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') ? '1' : '0',
                    ]);
        
                    if ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') {
                        
                        return true;
                    } else {
                        
                        return false;
                    }
            
                }
            }
        
        
            DB::table('smslog')->where('id', $smslogID)->update([
                'RES_Time' => now(),
                'response' => $response,
                'smsstatus' => 'INVALID',
                'smsdetails' => 'Invalid or non-numeric response',
            ]);
        
            // return response()->json([
            //     'status' => 'unknown',
            //     'response' => $response,
            // ]);
            
            return false;
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return false;
            return response()->json($response);
        }
    }
    
     public function smsNotificationUK($request, $txt)
    {
        try {
            
            
            $smslogID = DB::table('smslog')->insertGetId([
                'gateway' => 'smsportal',
                'mobile' => $request['mobile'],
                'details' => addslashes($request['messages']),
                'ip' => '',
                'datetime' => date("Y-m-d H:i:s"),
                'status' => '',
                'site' => 'CUSTOMER',
                'REQ_Time' => date("Y-m-d H:i:s"),
                'RES_Time' => date("Y-m-d H:i:s"),
                'smssendstatus' => '0',
                'response' => '',
                'token_response' => '',
                'subject' => '',
                'reference_id' => '',
                'smsdetails' => '',
                'smsstatus' => '',
            ]);
            
            $get_type = '';
            
            $sms_via = DB::table('settings')->whereNotNull('mess_config')->first();
            
            if($txt == 'verify'){
                $get_type = env('SMS_VERIFY_ID');
            }elseif($txt == 'forgot'){
                $get_type = env('SMS_FORGOT_ID');
            }elseif($txt == 'account_details'){
                $get_type = env('SMS_ACCOUNT_DETAIL_ID');
            }elseif($txt == 'kyc_verified'){
                $get_type = env('SMS_KYC_VERIFY_ID');
            }
            
            if ($sms_via && $sms_via->mess_config == 'smsresell' && in_array($txt, ['verify', 'forgot'], true) ) {
            
                $endpoint = env('SMS_RESELL_ENDPOINT');
                $user     = env('SMS_RESELL_USER');
                $pass     = env('SMS_RESELL_PASS');
                $sender   = env('SMS_RESELL_SENDER');
                $priority = env('SMS_RESELL_PRIORITY');
                $type     = env('SMS_RESELL_TYPE');
            
                $mobile = preg_replace('/^91/', '', $request['mobile']);
            
                $sendURL = $endpoint . 'sendmsg.php?' . http_build_query([
                    'user'     => $user,
                    'pass'     => $pass,
                    'sender'   => $sender,
                    'phone'    => $mobile,
                    'text'     => $request['messages'],
                    'priority' => $priority,
                    'stype'    => $type,
                ]);
            
                $messageId = trim((string) $this->requestAPI('GET', $sendURL));
            
                if ($messageId == '' || !preg_match('/^S\.\d+$/', $messageId)) {
            
                    DB::table('api_error_log')->insert([
                        'url'        => $sendURL,
                        'method'     => 'GET',
                        'payload'    => null,
                        'message'    => 'SMS send failed. Invalid response: ' . ($messageId ?: 'EMPTY'),
                        'status'     => 200,
                        'file'       => __FILE__,
                        'line'       => __LINE__,
                        'trace'      => null,
                        'ip'         => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'headers'    => json_encode(request()->headers->all()),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    return false;
                }
            
                $dlrURL = $endpoint . 'recdlr.php?' . http_build_query([
                    'user'    => $user,
                    'msgid'   => $messageId,
                    'phone'   => $mobile,
                    'msgtype' => $priority,
                ]);
            
                $dlrResponse = trim((string) $this->requestAPI('GET', $dlrURL));
            
                if ($dlrResponse === '') {
            
                    DB::table('api_error_log')->insert([
                        'url'        => $dlrURL,
                        'method'     => 'GET',
                        'payload'    => null,
                        'message'    => 'DLR check failed. Empty response',
                        'status'     => 200,
                        'file'       => __FILE__,
                        'line'       => __LINE__,
                        'trace'      => null,
                        'ip'         => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'headers'    => json_encode(request()->headers->all()),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            
                    return false;
                }
            
                $status = strtolower($dlrResponse);
            
                DB::table('smslog')
                    ->where('id', $smslogID)
                    ->update([
                        'RES_Time'      => now(),
                        'response'      => $dlrResponse,
                        'reference_id'  => $messageId,
                        'smsstatus'     => $dlrResponse,
                        'smsdetails'    => in_array($status, ['sent', 'delivered'], true)
                            ? 'SMS delivered successfully'
                            : "SMS not delivered ({$status})",
                        'smssendstatus' => in_array($status, ['sent', 'delivered'], true) ? '1' : '0',
                    ]);
            
                return in_array($status, ['sent', 'delivered'], true);
                
            } else {
                
                $checkURL = env('SMS_ENDPOINT') .
                    "smsapi?key=" . env('SMS_KEY') .
                    "&route=" . env('SMS_ROUTE') .
                    "&sender=" . env('SMS_SENDER') .
                    "&number=" . $request['mobile'] .
                    "&sms=" . urlencode($request['messages']) .
                    "&templateid=" . $get_type;
            
                $headers = [];
                $body = [];
            
                $response = $this->requestAPI('GET', $checkURL, $headers, json_encode($body));
                $response = trim($response);
            
                $errorMessages = [
                    '101' => 'Invalid user',
                    '102' => 'Invalid sender ID',
                    '103' => 'Invalid contact(s)',
                    '104' => 'Invalid route',
                    '105' => 'Invalid message',
                    '106' => 'Spam blocked',
                    '107' => 'Promotional block',
                    '108' => 'Low credits in the specified route',
                    '109' => 'Promotional route active only from 9am to 8:45pm',
                    '110' => 'Invalid DLT Template ID',
                ];
            
                if (is_numeric($response)) {
                    
                    if (array_key_exists($response, $errorMessages)) {
                        DB::table('smslog')->where('id', $smslogID)->update([
                            'RES_Time' => now(),
                            'response' => $response,
                            'smsstatus' => 'FAILED',
                            'smsdetails' => $errorMessages[$response],
                            'smssendstatus' => '0',
                        ]);
                        
                        return false;
                    }
            
                    $messageId = $response;
            
                    $dlrURL = env('SMS_ENDPOINT') .
                        "dlrapi?key=" . env('SMS_KEY') .
                        "&messageid=" . $messageId;
            
                    $dlrResponse = $this->requestAPI('GET', $dlrURL, $headers, json_encode($body));
                    $dlrResponse = trim($dlrResponse);
            
                    $dlrData = json_decode($dlrResponse, true);
                    
                    $deliveryStatus = strtolower($dlrData[0]['status']);
                    $deliveryTime = $dlrData[0]['time'] ?? now();
        
                    DB::table('smslog')->where('id', $smslogID)->update([
                        'RES_Time' => now(),
                        'response' => $dlrResponse,
                        'reference_id' => $messageId,
                        'smsstatus' => $dlrData[0]['status'],
                        'smsdetails' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent')
                            ? 'SMS delivered successfully'
                            : "SMS not yet delivered ({$deliveryStatus})",
                        'smssendstatus' => ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') ? '1' : '0',
                    ]);
        
                    if ($deliveryStatus == 'delivered' || $deliveryStatus == 'sent') {
                        
                        return true;
                    } else {
                        
                        return false;
                    }
            
                }
            }
        
        
            DB::table('smslog')->where('id', $smslogID)->update([
                'RES_Time' => now(),
                'response' => $response,
                'smsstatus' => 'INVALID',
                'smsdetails' => 'Invalid or non-numeric response',
            ]);
        
            // return response()->json([
            //     'status' => 'unknown',
            //     'response' => $response,
            // ]);
            
            return false;
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return false;
            return response()->json($response);
        }
    }



    public function checkWhatsAppCopy($request)
    {
        try {

        


            $key = env('SHIWhatsAppAPIKey');
            $Instance = env('SHIWhatsAppInstance');

            $checkURL = env('SHIWhatsAppEndPoint') . "client/isRegisteredUser/" . $Instance;
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                'x-api-key: ' . $key
            ];

            $body = [
                'number' => strval($request['mobile'])
            ];

            $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));
            
            // return $response;

            $result = json_decode($response);

            if (isset($result->success) && $result->success && $result->result) {

                return true;
            } else {

                return false;
            }

         
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function checkWhatsApp($request)
    {
        try {

        


            $key = env('SHIWhatsAppAPIKey');
            $Instance = env('SHIWhatsAppInstance');

            $checkURL = env('SHIWhatsAppEndPoint') . "waInstance" . $Instance . '/checkWhatsapp/'.$key;
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json',
                // 'x-api-key: ' . $key
            ];

            $body = [
                'phoneNumber' => strval($request['mobile'])
            ];

            $response = $this->requestAPI('POST', $checkURL, $headers, json_encode($body));
            
            // return $response;

            $result = json_decode($response);

            if (isset($result->existsWhatsapp) && $result->existsWhatsapp && $result->existsWhatsapp) {

                return true;
            } else {

                return false;
            }

         
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Throw in Catch Section', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function fbwTemplateSend(Request $request)
    {
        try {
    
            $request->validate([
                'mobile' => 'required',
                'type'   => 'required'
            ]);
    
            try {
                $templateName = unserialize(Crypt::decryptString($request->type));
            } catch (\Exception $e) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid template token'
                ]);
            }
            
            // return $templateName;
    
            $template = DB::table('wamail_templates')
                ->where('name', $templateName)
                ->where('approval_status', 'approved')
                ->where('is_active', 1)
                ->first();
    
            if (!$template) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Template not found'
                ]);
            }
    
            $lastSent = DB::table('wb_template_sent')
                ->where('mobile', $request->mobile)
                ->where('temp_name', $template->name)
                ->where('deletes', 0)
                ->where('created_at', '>=', now()->subHours(2))
                ->orderByDesc('id')
                ->first();
            
            if ($lastSent) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Message already sent recently. Please try again after 2 hours.'
                ]);
            }
            
            $parameters = [];
    
            if (preg_match_all('/\{\{(\d+)\}\}/', $template->body, $matches)) {
                foreach ($matches[1] as $param) {
                    $parameters[] = '';
                }
            }
            
             $logId = DB::table('wb_template_sent')->insertGetId([
                'temp_id'   => $template->id ?? null,
                'temp_name' => $template->name,
                'mobile'    => $request->mobile,
                'body'      => $template->body,
                'status'    => 0,
                'created_at'=> now(),
                'updated_at'=> now()
            ]);
    
            $newRequest = new Request([
                'mobile'        => $request->mobile,
                'template_name' => $template->name,
                'message_body'  => $template->body,
                'parameters'    => $parameters
            ]);
    
            $controller = app(AdminApiController::class);

            $response = $controller->sampleMess($newRequest);

            $resData = $response->getData(true);
            
            DB::table('wb_template_sent')
                ->where('id', $logId)
                ->update([
                    'status'     => $resData['status'] ? 1 : 2,
                    'updated_at' => now()
                ]);
    
            return $response;
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status' => 'failed',
                'message' => 'Throw in Catch Section',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'string' => $e->__toString()
                ]
            ]);
        }
    }
}