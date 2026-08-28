<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SendSMSController extends Controller
{
    //
public function sendsms(){

    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/AC45798a074b60327b7f19b229e89881a3/Messages.json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => 'From=+14753488744&Body=test body&To=+91 9345553521',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Basic QUM0NTc5OGEwNzRiNjAzMjdiN2YxOWIyMjllODk4ODFhMzpkNDEyZDZlNTYxYjFiZTg2MDBjMTU0YWNlMDhlNDcxZQ==',
    'Content-Type: application/x-www-form-urlencoded'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
}
    

}