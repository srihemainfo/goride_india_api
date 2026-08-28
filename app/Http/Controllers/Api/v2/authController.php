<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Template\mailController;
use Illuminate\Support\Carbon;
use App\Rules\CustomRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Http\Request;
use App\Models\user_register;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Helpers\referralCode;


class authController extends Controller
{

    public function userRegister($user_id, $otp, $ip, $request)
    {

        try {
            $response = [];
            $getTempUser = DB::table('users_temp')->where([
                ['deletes', '=', '0'],
                ['id', '=', $user_id],
            ])->first();

            if ($getTempUser->id != '') {

                $getTempUser = json_decode(json_encode($getTempUser), true);



                $clientIP = request()->ip();
                $date = date("Y-m-d H:i:s");

                //   dd();

                $clientIP = request()->ip();
                $date = date("Y-m-d H:i:s");


                $mobileCheck = DB::table('user_register')
                    ->where('mobile', $getTempUser['mobile'])
                    ->where('status', '0')
                    ->where('deletes', '0')
                    ->where('roll_id', '0')
                    ->get();

                if ($mobileCheck->count() > 0) {
                    return ['status' => 'failed', 'message' => 'The mobile number you entered already has an account.', 'error' => 'The mobile number you entered already has an account.'];
                    // goto returnFVI;
                }
                
                $utm_source = $_COOKIE['utm_source']??null;
                $utm_campaign = $_COOKIE['utm_campaign']??null;
                
                $dataToUpdate = [];

                $dataToUpdate['fcm_token'] = $request->fcm_token ?? null;
                if ($request->platform_type == 'ios' || $request->platform_type == 'android') {
                } else {
                    $dataToUpdate['browser_fcm_token'] = $request->browser_fcm_token ?? null;
                }
                
                $userData = [
                    'user' => 'Customer',
                    'pass' => $getTempUser['pass'],
                    'password' => $getTempUser['password'],
                    'roll_id' => $getTempUser['roll_id'],
                    'created_by' => 0,
                    'dialCode' => $getTempUser['dialCode'],
                    'mobile' => $getTempUser['mobile'],
                    'name' => $getTempUser['name'],
                    'email' => $getTempUser['email'],
                    'deletes' => '0',
                    'created_at' => now(),
                    'dob' => $getTempUser['dob'],
                    'lname' => $getTempUser['lname'],
                    'building_name' => $getTempUser['building_name'],
                    'city' => $getTempUser['city'],
                    'address' => $getTempUser['address'],
                    'nationality' => $getTempUser['nationality'],
                    'state' => $getTempUser['state'],
                    'ip' => $clientIP,
                    'email_verify' => $getTempUser['email_verify'],
                    'mobile_verify' => $getTempUser['mobile_verify'],
                    'otp' => '',
                    'my_referral_code' => '',
                    'residinglocation' => $getTempUser['residinglocation'],
                    'deviceType' => $getTempUser['deviceType'],
                    'utm_source' => $utm_source,
                    'utm_campaign' => $utm_campaign,
                ];
                
                $finalData = array_merge($userData, $dataToUpdate);
                
                $user_register_insert = DB::table('user_register')->insert($finalData);


                // dd($user_register_insert);




                if ($user_register_insert) {

                    $last_ins_ID = DB::getPdo()->lastInsertId();

                    $user_register_user_check = DB::table('user_register')->where(['id' => $last_ins_ID, 'status' => '0', 'deletes' => '0'])->orderBy('id', 'DESC')->first();


                    if ($user_register_user_check) {


                        $user_id_s = $user_register_user_check->id;


                        // $mobile1=DB::table('user_register')->select('mobile')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
                        $mobile = $user_register_user_check->mobile;

                        // $name1=DB::table('user_register')->select('name')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
                        $name = $user_register_user_check->name;

                        // $email1=DB::table('user_register')->select('EMAIL')->where(['id'=>$user_id_s,'status'=>'0','deletes'=>'0','roll_id'=>'0'])->orderBy('id','DESC')->first();
                        $email = $user_register_user_check->email;
                        //  dd($mobile);

                        // if (substr($mobile, 0, 3) == "971") {
                        // dd('sfdsgf');
                        // $messages = "Congratulation!!! You have successfully created Go Ride account.";
                        // $templateid = "";
                        // $randotp = Controller::generateOTP(4);


                        // sendsms($this->con, $mobile, $messages, $templateid);
                        // SMS Stopped 
                        // $sentsms = Controller::sendsms($mobile, $messages, '');
                        // }




                        $subject = 'Congratulation!!! You have successfully created Go Ride account';

                        $messages = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">
   <head>
      <meta charset="UTF-8">
      <meta content="width=device-width, initial-scale=1" name="viewport">
      <meta name="x-apple-disable-message-reformatting">
      <meta content="IE=edge" http-equiv="X-UA-Compatible">
      <meta content="telephone=no" name="format-detection">
      <title>Welcome Email</title>
      <style type="text/css">
         .rollover:hover .rollover-first {
         max-height:0px!important;
         display:none!important;
         }
         .rollover:hover .rollover-second {
         max-height:none!important;
         display:block!important;
         }
         .rollover span {
         font-size:0px;
         }
         u + .body img ~ div div {
         display:none;
         }
         #outlook a {
         padding:0;
         }
         span.MsoHyperlink,
         span.MsoHyperlinkFollowed {
         color:inherit;
         mso-style-priority:99;
         }
         a.es-button {
         mso-style-priority:100!important;
         text-decoration:none!important;
         }
         a[x-apple-data-detectors],
         #MessageViewBody a {
         color:inherit!important;
         text-decoration:none!important;
         font-size:inherit!important;
         font-family:inherit!important;
         font-weight:inherit!important;
         line-height:inherit!important;
         }
         .es-desk-hidden {
         display:none;
         float:left;
         overflow:hidden;
         width:0;
         max-height:0;
         line-height:0;
         mso-hide:all;
         }
         .es-button-border {
          mso-style-priority: 100 !important;
          text-decoration: none !important;
          mso-line-height-rule: exactly;
          color: #fff !important;
          font-size: 24px;
          padding: 10px 20px 10px 20px;
          display: inline-block;
          background: #002d72;
          border-radius: 10px;
          font-family: "Poppins", sans-serif;
          font-weight: bold;
          font-style: normal;
          line-height: 29px;
          width: auto;
          text-align: center;
          letter-spacing: 0;
          mso-padding-alt: 0;
          mso-border-alt: 10px solid #fff;
          border: 2px solid;
         }
      </style>
   </head>
   <body class="body" style="width:100%;height:100%;padding:0;Margin:0">
      <div dir="ltr" class="es-wrapper-color" lang="en" style="background-color:#F6F6F6">
         <table cellpadding="0" cellspacing="0" width="100%" class="es-wrapper" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#F6F6F6">
            <tr>
               <td valign="top" style="padding:0;Margin:0">
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                     <tr>
                        <td align="center" style="padding:0;Margin:0">
                           <table cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                              <tr>
                                 <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                       <tr>
                                          <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                             <table cellspacing="0" width="100%" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                   <td align="center" style="padding:20px;Margin:0;font-size:0px"><a href="' . ($request->header('Origin') . '/') . '" target="_blank" style="mso-line-height-rule:exactly;text-decoration:underline;color:#2CB543;font-size:14px"><img alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/go_ride_logo.png" width="350" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                </tr>
                                             </table>
                                          </td>
                                       </tr>
                                    </table>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <table align="center" cellpadding="0" cellspacing="0" class="es-content" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:100%;table-layout:fixed !important">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table cellspacing="0" align="center" bgcolor="#ffffff" cellpadding="0" class="es-content-body" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;font-size:0px"><img width="400" alt="" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/welcome_email_banner.png" class="adapt-img" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                               <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:36px;letter-spacing:0;color:#002d72;font-size:24px"><strong>Hi, ' . ucfirst(strtolower($user_register_user_check->name)) . ' ' . (ucfirst(strtolower($user_register_user_check->lname)) ?? '') . '</strong></p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Welcome to Go Ride!</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">We`re exicited to have you on board.</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:7px;Margin:0;font-size:0">
                                                                <table cellspacing="0" height="100%" width="100%" border="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                                    <tbody><tr>
                                                                        <td style="padding:0;Margin:0;height:1px;width:100%;margin:0px;border-bottom:0px solid #cccccc;background:unset"></td>
                                                                    </tr>
                                                                </tbody></table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Ready to get started?</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">Go Ride is your all-in-one platform to post ride requirements,</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">bid on jobs, and manage taxi and cab bookings efficiently</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="padding:0;Margin:0">
                                                                <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:27px;letter-spacing:0;color:#002d72;font-size:18px">— all in one place.</p>
                                                            </td>
                                                        </tr>
                                                        
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-right:20px;padding-left:20px">
                                            <table width="100%" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                                    <table bgcolor="#ffa417" cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;" role="presentation">
                                                        <tr>
                                                       <td align="center" bgcolor="#ffa417" style="padding:0;Margin:0;padding-top:20px;padding-bottom:10px">
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">Need Help? Visit us at <a href="' . (env('APP_URL') . '/') . '" style="color: white;">www.goride.run</a></p>
                                                                                                                                    <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#ffffff;font-size:14px">call or whatsapp on <a href="tel:'. env('SUPPORT_MOBILE') .'">'. env('SUPPORT_MOBILE') .'</a>, email at <a href="mailto:'. env('SUPPORT_EMAIL') .'">'. env('SUPPORT_EMAIL') .'</a></p>
                                                                                                                                </td>
                                                        </tr>
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0">
                                                            <table cellpadding="0" cellspacing="0" dir="ltr" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                              <tr>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_FB') .'"><img width="32" alt="Fb" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/facebook-logo-white.png" title="Facebook" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a href="'. env('SUPPORT_YOUTUBE') .'"><img title="YouTube" width="32" alt="Yt" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/youtube-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                            <td align="center" valign="top" style="padding:0;Margin:0"><a href="'. env('SUPPORT_INSTA') .'"><img title="Instagram" width="32" alt="Ig" height="32" src="' . env('DO_REDIRECT_URL') . 'goride/img/email/instagram-logo-white.png" style="display:block;font-size:14px;border:0;outline:none;text-decoration:none"></a></td>
                                                                                                                                        </tr>
                                                            </table>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-right:20px;padding-left:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                            <tr>
                                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr>
                                                        <td align="center" style="padding:0;Margin:0;padding-bottom:10px">
                                                            <p style="Margin:0;mso-line-height-rule:exactly;font-family:verdana, geneva, sans-serif;line-height:21px;letter-spacing:0;color:#01104e;font-size:14px">Note: This is a system auto-generated email. Please do not reply to this mail.</p>
                                                        </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                  </table>
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>';



                        // $messages = '
                        // <!DOCTYPE html
                        //    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        // <html xmlns="http://www.w3.org/1999/xhtml">
                        //    <head>
                        //       <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        //       <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                        //       <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        //       <title> Registration-Welcome</title>
                        //       <style type="text/css">
                        //          @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                        //          @import url("https://fonts.cdnfonts.com/css/verdana");
                        //          body {
                        //          margin: 0;
                        //          }
                        //          .wrapper {
                        //          background: #CCC;
                        //          }
                        //          .main {
                        //          background: #FFF;
                        //          max-width: 600px;
                        //          }
                        //          table {
                        //          border-spacing: 0;
                        //          }
                        //          td {
                        //          padding: 3px;
                        //          }
                        //          img {
                        //          border: 0;
                        //          }
                        //          .column-one {
                        //          text-align: center;
                        //          margin: 0 auto;
                        //          }
                        //          .column-one .column {
                        //          width: 100%;
                        //          margin: 0 auto;
                        //          }
                        //          .im {
                        //          color: #01104e;
                        //          }
                        //          .column-one h3 {
                        //          color: #01104e;
                        //          font-family: Verdana, sans-serif !important;
                        //          font-size: 28px;
                        //          font-weight: 600;
                        //          margin: 14px 0 0 0;
                        //          }
                        //          .column-one p {
                        //          color: #01104e;
                        //          font-family: Verdana, sans-serif !important;
                        //          font-size: 19px;
                        //          font-weight: 500;
                        //          margin: 4px 0;
                        //          }
                        //       </style>
                        //    </head>
                        //    <body>
                        //       <center class="wrapper">
                        //          <table class="main" width="100%">
                        //             <!-- BORDER -->
                        //             <tr>
                        //                <td style="background-color: #171f4f; height: 45px;"></td>
                        //             </tr>
                        //             <tr>
                        //                <td class="column-one" style="background: #088b42;height:10px;">
                        //                </td>
                        //             </tr>
                        //             <!-- <tr>
                        //                <td style="background-color: #339a46; height: 45px;"></td>
                        //                </tr> -->
                        //             <tr>
                        //                <td class="column-one">
                        //                   <table class="column">
                        //                      <tr>
                        //                         <td valign="top" style="padding: 0;">
                        //                            <center>
                        //                               <br>
                        //                               <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndLogo.png" style="border: 0px;"
                        //                                  width="50%">
                        //                            </center>
                        //                         </td>
                        //                      </tr>
                        //                      <tr>
                        //                         <td valign="top" style="padding: 0;">
                        //                            <center>
                        //                               <br>
                        //                               <img src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndWelcome.png" style="border-radius: 19px;" width="84%">
                        //                               <br>
                        //                            </center>
                        //                         </td>
                        //                      </tr>
                        //                   </table>
                        //                </td>
                        //             </tr>
                        //             <!-- LOGO  -->
                        //             <tr>
                        //                <td class="column-one c-f">
                        //                   <br>
                        //                   <p style="font-weight: 600!important;">Hi, ' . ucfirst(strtolower($user_register_user_check->name)) . ' ' . (ucfirst(strtolower($user_register_user_check->lname)) ?? '') . '</p>
                        //                   <!-- <p style="font-size: 15px; font-weight: 500!important; line-height: 1.5;">Congratulations on taking
                        //                      your<br>
                        //                      first little step towards your BIG DREAMS!
                        //                      </p> -->
                        //                   <!-- <p style="font-size: 15px; font-weight: 500!important; line-height: 1.5;">Congratulations on playing smart and winning <br>big for the first time!</p> -->
                        //                   <p
                        //                      style="font-size:15px; font-weight: 500!important; font-family: Verdana, sans-serif !important; margin:14px 0;">
                        //                      Start your journey by purchasing your first product <br> and get a chance to participate in draws.
                        //                      <!-- <br>Ticket gives you the chance to enter 3 draws: -->
                        //                   </p>
                        //                </td>
                        //             </tr>
                        //             <tr>
                        //                <td>
                        //                   <ul
                        //                      style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                        //                      <li>â€¢ Thrill Draw win up to 24 Grams of Gold</li>
                        //                      <li>â€¢ Booster Draw win up to 100 Grams of Gold </li>
                        //                      <li>â€¢ Bumper Draw win up to 1000 Grams of  Gold</li>
                        //                   </ul>
                        //                </td>
                        //             </tr>
                        //             <tr>
                        //                <td style="border-radius: 4px 4px 0px 0px;color: #111111;padding:24px 10px;" align="center" valign="top"
                        //                   bgcolor="#ffffff">
                        //                   <a href="' . ($request->header('Origin') . '/') . '"  style="color: #ffffff;margin: 0px;padding:6px  9px;background: #088b42;width: fit-content;font-size: 16px;border-radius: 7px; font-family: Poppins, sans-serif !important; font-weight: 600; text-decoration: none;">PARTICIPATE
                        //                   NOW</a>
                        //                </td>
                        //             </tr>
                        //             <!-- <tr>
                        //                 <td style="background: #171f4f;" align="center" valign="top">
                        //                     <p style="color: #fff !important;font-size: 11px !important; font-family: Verdana, sans-serif !important;">Need Help? Visit us at www.nationaldrawuae.com,<br> call on  +971 48839177, or email at care@nationaldrawuae.com</p>
                        //                 </td>
                        //             </tr> -->
                        //             <tr>
                        //                 <td class="column-one">
                        //                    <img style="width: !important;margin-top: 10px;" src="' . env('DO_REDIRECT_URL') . 'nationaldraw/1/ndFooter.png" width="84%">
                        //                 </td>
                        //              </tr>
                        //             <tr>
                        //                <td>
                        //                   <p
                        //                      style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                        //                      Note: This is a system auto-generated email. Please do not reply to this mail.
                        //                   </p>
                        //                </td>
                        //             </tr>
                        //             <tr>
                        //             <td class="column-one" style="background: #171f4f; height:10px;">
                        //             </td>
                        //             </tr>
                        //          </table>
                        //          <!-- End Main Class -->
                        //       </center>
                        //       <!-- End Wrapper -->
                        //    </body>
                        // </html>';




                        if (isset($email) && $email != '') {
                            $emailchack = explode('@', $email);
                            if (strtolower($emailchack[1]) != "goride.run") {
                                $sendEmail = Controller::composeEmail($ip, $email, $subject, $messages);
                            }
                        }


                        // Last login ID
                        $log = DB::table('login_logs')->insert([
                            'method' => __FUNCTION__,
                            'userid' => $last_ins_ID, // user ID here
                            'createdon' => now(),
                            'ip' => $request->ip(),
                            'utm_campaign'=> $utm_campaign,
                            'utm_source'=> $utm_source
                            // 'deletes' will be automatically set to '0' as per the default value
                        ]);


                        $user = user_register::where(['id' => $last_ins_ID, 'roll_id' => '0', 'status' => '0', 'deletes' => '0'])->first();

                        $token = $user->createToken('NDaccessToken', ['expires_at' => Carbon::now()->addHours(72)])->plainTextToken;

                        $response = [

                            'status' => 'success',
                            'message' => 'Verified successfully!',

                            'data' => [
                                'user_id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'mobile' => $user->mobile,
                                'country' => $user->nationality,
                                'state' => $user->address,
                                'city' => $user->city,
                                'notify' => $user->notify,

                            ],
                            'token' => $token,
                        ];
                        
                        $refferal_code = referralCode::generateReferralCode();

                        $existing = DB::table('referral_codes')
                            ->where('user_id', $user->id)
                            ->where('app_name', 'partner')
                            ->first();
                        
                        if (!$existing) {
                        
                            $referral_code = referralCode::generateReferralCode();
                        
                            DB::table('referral_codes')->insert([
                                'user_id'    => $user->id,
                                'app_name'   => 'partner',
                                'code'       => $referral_code,
                                'created_at' => now()
                            ]);
                        
                        }



                        // return ['status' => 'success', 'message' => 'Verified successfully!', 'data' => "Verified successfully, login to continue"];
                        return $response;
                    }
                } else {
                    return ['status' => 'failed', 'message' => 'User Creation has been failed!', 'error' => 'Insert query has been Failed'];
                }
            } else {
                return ['status' => 'failed', 'message' => 'User account not found!', 'error' => 'The user account not found in the temp.'];
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function update_bank(Request $request)
    {
        try {
    
            $validated = $request->validate([
                'ac_name' => ['required', 'string'],
                'upiID'   => ['required', 'string']
            ]);
    
            $user = DB::table('user_register')
                ->where('id', auth()->id())
                ->where('deletes', '0')
                ->first();
    
            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'User not found.'
                ], 404);
            }
    
            DB::beginTransaction();
    
            $updated = DB::table('user_register')
                ->where('id', $user->id)
                ->update([
                    'account_name' => $request->ac_name,
                    'upiID'        => $request->upiID
                ]);
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => $updated ? 'Bank details updated successfully.' : 'No changes made.',
                'data'    => []
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // Register Email OTP Send
    public function register(Request $request)
    {

        try {
            // $numberController = new RulesController();
            $response = [];
            $input = $request->all();

            $validator = Validator::make($input, [
                'first_name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/', new CustomRule],

                'mobile' => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode' => ['required', 'integer'],
                'email' => ['required', 'email', 'max:70'],
                // 'password' => ['required', Password::min(6)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
                // 'c_password' => ['required', Password::min(6)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'same:password'],
                'password' => ['required', 'min:6'],
                'c_password' => ['required', 'min:6', 'same:password'],
                'deviceType' => ['required', 'in:MOBILE,APP,DESKTOP,BROWSER,TABLET', 'max:10'],
                'building_name' => ['max:50'],
                'country' => ['required', 'integer'],
                'state' => ['required', 'integer'],
                'city' => ['required', 'integer'],
            ]);

            if (!$validator->fails()) {



                $request->email = Controller::BlockSQLInjection($request->email);
                if ($request->email == '' || $request->email == null || $request->email == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
                    goto returnFVI;
                }

                $request->first_name = Controller::BlockSQLInjection($request->first_name);
                if ($request->first_name == '' || $request->first_name == null || $request->first_name == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid name!', 'error' => 'Please use a valid name!'];
                    goto returnFVI;
                }

                $request->mobile = Controller::BlockSQLInjection($request->mobile);
                if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
                    goto returnFVI;
                }

                $request->dialCode = Controller::BlockSQLInjection($request->dialCode);
                if ($request->dialCode == '' || $request->dialCode == null || $request->dialCode == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid dial code!', 'error' => 'Please use a valid dial code!'];
                    goto returnFVI;
                }

                // $request->password = Controller::BlockSQLInjection($request->password);
                // if ($request->password == '' || $request->password == null || $request->password == 'null') {
                //   $response = ['status' => 'failed', 'message' => 'Please use a valid password!', 'error' => 'Please use a valid password!'];
                //   goto returnFVI;
                // }

                // $request->c_password = Controller::BlockSQLInjection($request->c_password);
                // if ($request->c_password == '' || $request->c_password == null || $request->c_password == 'null') {
                //   $response = ['status' => 'failed', 'message' => 'Please use a valid confirm password!', 'error' => 'Please use a valid confirm password!'];
                //   goto returnFVI;
                // }

                $request->deviceType = Controller::BlockSQLInjection($request->deviceType);
                if ($request->deviceType == '' || $request->deviceType == null || $request->deviceType == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid device type!', 'error' => 'Please use a valid device type!'];
                    goto returnFVI;
                }

                $request->country = Controller::BlockSQLInjection($request->country);
                if ($request->country == '' || $request->country == null || $request->country == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please select valid country!', 'error' => 'Please select valid country!'];
                    goto returnFVI;
                }

                $request->state = Controller::BlockSQLInjection($request->state);
                if ($request->state == '' || $request->state == null || $request->state == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please select valid state!', 'error' => 'Please select valid state!'];
                    goto returnFVI;
                }

                $request->city = Controller::BlockSQLInjection($request->city);
                if ($request->city == '' || $request->city == null || $request->city == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please select valid city!', 'error' => 'Please select valid city!'];
                    goto returnFVI;
                }


                // dd($request->email);


                $pass = md5($request->password);


                /// Pattern Valiation ///





                $emailCheck = DB::table('user_register')
                    ->where('email', $request->email)
                    ->where('status', '0')
                    ->where('deletes', '0')
                    ->get();

                if ($emailCheck->count() > 0) {
                    $response = ['status' => 'failed', 'message' => 'Entered "Email" has already been registered', 'error' => 'Entered "Email" has already been registered'];
                    goto returnFVI;
                }

                // $d_mob=$dialcode.''.$request->mobile;
                // dd($d_mob);
                $mobileCheck = DB::table('user_register')
                    ->where('mobile', $request->mobile)
                    ->where('status', '0')
                    ->where('deletes', '0')
                    ->where('roll_id', '0')
                    ->get();

                if ($mobileCheck->count() > 0) {
                    $response = ['status' => 'failed', 'message' => 'The mobile number you entered already has an account.', 'error' => 'The mobile number you entered already has an account.'];
                    goto returnFVI;
                }


                $countryName = trim(DB::table('countries')->where('flag', 1)
                    ->where('id', $request->country)
                    ->where('name', '!=', '')
                    ->orderByDesc('id')->limit(1)
                    ->value('name'), "'");

                // dd($countryName);
                $stateName = trim(DB::table('states')->where('flag', 1)
                    ->where('id', $request->state)
                    ->where('name', '!=', '')
                    ->orderByDesc('id')->limit(1)
                    ->value('name'), "'");

                $cityName = trim(DB::table('cities')->where('flag', 1)
                    ->where('id', $request->city)
                    ->where('name', '!=', '')
                    ->orderByDesc('id')->limit(1)
                    ->value('name'), "'");

                $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));

                $checkRepeat = DB::table('users_temp')
                    ->where('email', '=', $request->email)
                    ->where('status', '=', '0')
                    ->where('deletes', '=', '1')
                    ->where('created_at', '>=', $oneHourAgo)
                    ->get();

                if ($checkRepeat->count() > 4) {
                    $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
                    goto returnFVI;
                }

                $randotp = Controller::generateOTP(4);
                
                $utm_source = $_COOKIE['utm_source']??null;
                $utm_campaign = $_COOKIE['utm_campaign']??null;

                $arr = [
                    'building_name' => ($request->building_name != '' && $request->building_name != 'null') ? $request->building_name : '',
                    'city' => $cityName,
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'address' => $stateName,
                    'nationality' => $countryName,
                    'pass' => $pass,
                    'deletes' => '1',
                    'dialCode' => $request->dialCode,
                    'otp' => $randotp,
                    'ip' => $request->ip(),
                    'deviceType' => $request->deviceType,
                    'roll_id' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'password' => $request->password,
                    'utm_source' => $utm_source,
                    'utm_campaign' => $utm_campaign
                ];

                $tempINS = DB::table('users_temp')->insert($arr);
                $insertedId = DB::getPdo()->lastInsertId();
                if ($tempINS) {

                    $subject = "Go Ride | OTP to Verify Email - " . date("d-m-Y g:i a");
                    $requestArr = [
                        'name' => $request->first_name,
                        'randotp' => $randotp,
                    ];

                    $message = mailController::signUPotp($requestArr);

                    $sendEmail = Controller::composeEmail($request->ip(), $request->email, $subject, $message);

                    if ($sendEmail) {
                        $response = ['status' => 'success', 'message' => 'Email OTP Send Successfully!', 'data' => ['tempID' => (int) $insertedId]];
                        goto returnFVI;
                    } else {
                        $response = ['status' => 'failed', 'message' => 'Email OTP Failed!', 'error' => $sendEmail];
                        goto returnFVI;
                    }
                } else {
                    $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => $tempINS];
                    goto returnFVI;
                }
            } else {
                $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    // Get Country, City, State, collection API
    public function getWorld()
    {
        
        try {
            $response = [];
            $countries = DB::table('countries')->select([
                'countries.id as id',
                'countries.name as name',
                DB::raw('COUNT(states.id) as statecount'),
            ])
                ->join('states', 'countries.id', '=', 'states.country_id')
                ->where('countries.flag', 1)
                ->groupBy('countries.id')
                ->havingRaw('statecount > 0')
                ->orderBy('name', 'ASC')
                ->get();

            $states = DB::table('states')->select([
                'states.id as id',
                'states.name as name',
                'states.country_id as countryID',
                DB::raw('COUNT(cities.id) as citycount'),
            ])
                ->join('cities', 'states.id', '=', 'cities.state_id')
                ->where('states.flag', 1)
                ->groupBy('states.id')
                ->havingRaw('citycount > 0')
                ->orderBy('name', 'ASC')
                ->get();

            // $cities = DB::table('cities')->select([
            //   'id',
            //   'name',
            //   'country_id as countryID',
            //   'country_id as countryID',
            //   'state_id as stateID',
            // ])
            //   ->where('flag', 1)
            //   ->orderBy('name', 'ASC')
            //   ->get();

            $response = ['status' => 'success', 'message' => 'Country, State and City has been get successfully.', 'data' => ['countries' => $countries, 'states' => $states]];
            goto returnFVI;

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }



    // Get Country, City, State, collection API
    public function getCity(Request $request)
    {
        try {
            $response = [];
            if ($request->state_id == '' || $request->state_id == null || $request->state_id == '') {
                $response = ['status' => 'failed', 'message' => 'Kindly send state id!', 'error' => 'Kindly send state id!'];
                goto returnFVI;
            }

            $request->state_id = Controller::BlockSQLInjection($request->state_id);
            if ($request->state_id == '' || $request->state_id == null || $request->state_id == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please use a valid state id!', 'error' => 'Please use a valid state id!'];
                goto returnFVI;
            }

            // $countries = DB::table('countries')->select([
            //   'countries.id as id',
            //   'countries.name as name',
            //   DB::raw('COUNT(states.id) as statecount'),
            // ])
            //   ->join('states', 'countries.id', '=', 'states.country_id')
            //   ->where('countries.flag', 1)
            //   ->groupBy('countries.id')
            //   ->havingRaw('statecount > 0')
            //   ->orderBy('name', 'ASC')
            //   ->get();

            // $states = DB::table('states')->select([
            //   'states.id as id',
            //   'states.name as name',
            //   'states.country_id as countryID',
            //   DB::raw('COUNT(cities.id) as citycount'),
            // ])
            //   ->join('cities', 'states.id', '=', 'cities.state_id')
            //   ->where('states.flag', 1)
            //   ->groupBy('states.id')
            //   ->havingRaw('citycount > 0')
            //   ->orderBy('name', 'ASC')
            //   ->get();

            $cities = DB::table('cities')->select([
                'id',
                'name',
                'country_id as countryID',
                'country_id as countryID',
                'state_id as stateID',
            ])
                ->where('state_id', $request->state_id)
                ->where('flag', 1)
                ->orderBy('name', 'ASC')
                ->get();

            if ($cities->count() > 0) {
                $response = ['status' => 'success', 'message' => 'City has been get successfully.', 'data' => ['cities' => $cities]];
                goto returnFVI;
            } else {
                $response = ['status' => 'failed', 'message' => 'City list not found!', 'error' => 'City list not found!'];
                goto returnFVI;
            }




            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }










    ////////////////////////// NEW ////////////////////////////////
    public function getCountry()
    {
        try {
            $response = [];
            $countries = DB::table('countries')->select([
                'countries.id as id',
                'countries.name as name',
                DB::raw('COUNT(states.id) as statecount'),
            ])
                ->join('states', 'countries.id', '=', 'states.country_id')
                ->where('countries.flag', 1)
                ->groupBy('countries.id')
                ->havingRaw('statecount > 0')
                ->orderBy('name', 'ASC')
                ->get();



            $response = ['status' => 'success', 'message' => 'Country list has been get successfully.', 'data' => ['countries' => $countries]];
            goto returnFVI;

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }


    function validatePassword($password)
    {
        try {
            // Minimum length of 6 characters
            if (strlen($password) < 6) {

                return ['status' => false, "message" => 'Minimum length of 6 characters'];
            }

            // At least 1 uppercase letter
            if (!preg_match('/[A-Z]/', $password)) {


                return ['status' => false, "message" => 'At least 1 uppercase letter'];
            }

            // At least 1 digit
            if (!preg_match('/[0-9]/', $password)) {


                return ['status' => false, "message" => 'At least 1 digit'];
            }

            // At least 1 special character (non-alphanumeric)
            if (!preg_match('/[!@#$%^&*()\-_=+{}[\]|;:\'",<.>\/?]/', $password)) {


                return ['status' => false, "message" => 'At least 1 special character (non-alphanumeric)'];
            }



            return ['status' => true, "message" => 'success'];
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    function calAge($date)
    {
        try {
            $dob = new DateTime($date);
            $now = new DateTime();
            $interval = $now->diff($dob);
            return $interval->format('%y');
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    // public function generatePass()
    // {
    //     // Generate a random 16-character strong password
    //     $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    //     $password = substr(str_shuffle(str_repeat($characters, 10)), 0, 10);

    //     return $password;
    // }
    
    public function generatePass($first_name, $mobile)
    {
        // Get first 3 letters of first_name (capitalize first letter of each)
        $firstPart = substr($first_name, 0, 3);
    
        // Generate 3 random lowercase letters
        $letters = 'abcdefghijklmnopqrstuvwxyz';
        $randomLetters = '';
        for ($i = 0; $i < 3; $i++) {
            $randomLetters .= $letters[random_int(0, strlen($letters) - 1)];
        }
    
        // Get last 3 digits of mobile number
        $lastDigits = substr(preg_replace('/\D/', '', $mobile), -3);
    
        // Combine into final password
        return $firstPart . '@' . $randomLetters . $lastDigits;
    }


    public function signup(Request $request)
    {

        try {
            // $numberController = new RulesController();
            $response = [];
            $input = $request->all();

            $validator = Validator::make($input, [
                'first_name' => ['required', 'max:70'],
                // 'last_name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/', new CustomRule],
                'mobile' => ['required', 'regex:/^\+?[0-9]+$/'],
                'dialCode' => ['required', 'integer'],
                // 'email' => ['required', 'email', 'max:70'],
                'password' => ['nullable'],
                // 'c_password' => ['required', Password::min(6)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'same:password'],
                'password' => ['nullable', 'min:6'],
                // 'c_password' => ['required', 'min:6', 'same:password'],
                'deviceType' => 'nullable|string|in:MOBILE,APP,DESKTOP,BROWSER,TABLET|max:10',
                // 'building_name' => ['max:50'],
                'state' => ['nullable'],
                // 'livein' => ['required'],
                // 'dataOfBirth' => 'required|date_format:Y-m-d'
                // 'city' => ['required', 'integer'],
            ]);
            
            // return $input;

            if (!$validator->fails()) {



                // $request->first_name = Controller::BlockSQLInjection($request->first_name);
                if ($request->first_name == '' || $request->first_name == null || $request->first_name == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid first name!', 'error' => 'Please use a valid name!'];
                    goto returnFVI;
                }

                // $request->last_name = Controller::BlockSQLInjection($request->last_name);
                // if ($request->last_name == '' || $request->last_name == null || $request->last_name == 'null') {
                //     $response = ['status' => 'failed', 'message' => 'Please use a valid last name!', 'error' => 'Please use a valid name!'];
                //     goto returnFVI;
                // }

 

                $request->mobile = Controller::BlockSQLInjection($request->mobile);
                if ($request->mobile == '' || $request->mobile == null || $request->mobile == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid mobile!', 'error' => 'Please use a valid mobile!'];
                    goto returnFVI;
                }

                // $existsWhatsApp = Controller::checkWhatsApp([
                //     'mobile' => $request->mobile
                // ]);
                // // return $existsWhatsApp;
                // if (!$existsWhatsApp) {
                //     $response = [
                //         'status' => 'failed',
                //         'message' => 'Please provide a valid WhatsApp number.',
                //         'error' => 'The provided WhatsApp number is invalid. Please check and try again.'
                //     ];

                //     goto returnFVI;
                // }

                $request->dialCode = Controller::BlockSQLInjection($request->dialCode);
                if ($request->dialCode == '' || $request->dialCode == null || $request->dialCode == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid dial code!', 'error' => 'Please use a valid dial code!'];
                    goto returnFVI;
                }


                if ($request->email != '') {
                    $request->email = Controller::BlockSQLInjection($request->email, false);
                    if ($request->email == '' || $request->email == null || $request->email == 'null') {
                        $response = ['status' => 'failed', 'message' => 'Please use a valid email!', 'error' => 'Please use a valid email!'];
                        goto returnFVI;
                    }

                    $emailCheck = DB::table('user_register')
                        ->where('email', $request->email)
                        ->where('status', '0')
                        ->where('deletes', '0')
                        ->get();

                    if ($emailCheck->count() > 0) {
                        $response = ['status' => 'failed', 'message' => 'Entered "Email" has already been registered', 'error' => 'Entered "Email" has already been registered'];
                        goto returnFVI;
                    }
                }



                // $request->deviceType = Controller::BlockSQLInjection($request->deviceType);
                // if ($request->deviceType == '' || $request->deviceType == null || $request->deviceType == 'null') {
                //     $response = ['status' => 'failed', 'message' => 'Please use a valid device type!', 'error' => 'Please use a valid device type!'];
                //     goto returnFVI;
                // }

                // $request->state = Controller::BlockSQLInjection($request->state);
                // if ($request->state == '' || $request->state == null || $request->state == 'null') {
                //     $response = ['status' => 'failed', 'message' => 'Please select valid state!', 'error' => 'Please select valid state!'];
                //     goto returnFVI;
                // }

                // $request->livein = Controller::BlockSQLInjection($request->livein);
                // if ($request->livein == '' || $request->livein == null || $request->livein == 'null') {
                //     $response = ['status' => 'failed', 'message' => 'Please select valid livein!', 'error' => 'Please select valid livein!'];
                //     goto returnFVI;
                // }
                
                $request->password = $this->generatePass($request->first_name, $request->mobile)??'OH*&!@#G*&$B$*';


                // if (!$this->validatePassword($request->password)['status']) {
                //     $response = ['status' => 'failed', 'message' => $this->validatePassword($request->password)['message'], 'error' => $this->validatePassword($request->password)['message']];
                //     goto returnFVI;
                // }

                // dd($this->calAge($request->dataOfBirth));
                // if ($this->calAge($request->dataOfBirth) < 3) {

                //     $response = ['status' => 'failed', 'message' => 'You age under 3 not qualified to participate to try your luck.', 'error' => 'You age under 3 not qualified to participate to try your luck.'];
                //     goto returnFVI;
                // }

                $pass = md5($request->password);


                /// Pattern Valiation ///



                // $d_mob=$dialcode.''.$request->mobile;
                // dd($d_mob);
                $mobileCheck = DB::table('user_register')
                    ->where('mobile', $request->mobile)
                    ->where('status', '0')
                    ->where('deletes', '0')
                    ->where('roll_id', '0')
                    ->get();

                if ($mobileCheck->count() > 0) {
                    $response = ['status' => 'failed', 'message' => 'The mobile number you entered already has an account.', 'error' => 'The mobile number you entered already has an account.'];
                    goto returnFVI;
                }



                $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));

                $checkRepeat = DB::table('users_temp')
                    ->where('mobile', $request->mobile)
                    ->where('status', '=', '0')
                    ->where('deletes', '=', '1')
                    ->where('created_at', '>=', $oneHourAgo)
                    ->get();

                if ($checkRepeat->count() >= 3) {
                    $response = ['status' => 'failed', 'message' => 'Try Again After 1 Hour', 'error' => 'Try after some Time'];
                    goto returnFVI;
                }

                $randotp = Controller::generateOTP(6);
                // $messages = "Your Go Ride code is " . $randotp . ". Please don't share with anyone.";
                $messages = "Your GoRide Verification Code is " . $randotp . ". Please don't share with anyone.";

                // $randotp = '123456';

                $whatsAppArr = [
                    'mobile' => $request->mobile,
                    'templateName' => 'national_draw_verification',
                    'language' => 'en',
                    'templateBodyParam' => [
                        strval($randotp)
                    ],
                    'messages' => $messages,
                    'resend' => ($request->isResend === "true" ? true : false)
                ];
                
                $utm_source = $_COOKIE['utm_source']??null;
                $utm_campaign = $_COOKIE['utm_campaign']??null;

                $arr = [
                    'building_name' => ($request->building_name != '' && $request->building_name != 'null') ? $request->building_name : '',
                    'city' => '',
                    'name' => $request->first_name,
                    'email' => ($request->email != '' && $request->email != 'null') ? $request->email : $request->mobile . '@goride.run',
                    'mobile' => $request->mobile,
                    'address' => '',
                    'state' => $request->state,
                    'pass' => $pass,
                    'deletes' => '1',
                    'dialCode' => $request->dialCode,
                    'otp' => $randotp,
                    'ip' => $request->ip(),
                    'deviceType' => $request->deviceType,
                    'roll_id' => '0',
                    'created_at' => now(),
                    'password' => $request->password,
                    // 'residinglocation' => $request->livein,
                    'lname' => '',
                    // 'dob' => $request->dataOfBirth
                    'utm_source' => $utm_source,
                    'utm_campaign' => $utm_campaign
                ];

                $tempINS = DB::table('users_temp')->insertGetId($arr);

                if ($tempINS != '' && isset($tempINS)) {


                    $timeAfterTenMinutes = Carbon::now()->addMinutes(10)->toDateTimeString();
                    
                    $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
                                
                    if($check_mess && $check_mess->mess_type == 'sms'){
                        
                        $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
                        
                    }elseif($check_mess && $check_mess->mess_type == 'whatsapp'){
                        
                        $sentsms = Controller::sendNotification($whatsAppArr);
                    }


                    // $sentsms = Controller::sendNotification($whatsAppArr);
                    // if (!$sentsms) {
                    //     $whatsAppArr['resend'] = true;
                    //     $sentsms = Controller::smsNotification($whatsAppArr, 'verify');
                    // }
                    // if (substr($request->mobile, 0, 3) != "971") {

                    //     if (isset($request->isResend) && $request->isResend === "true") {
                    //         // Temporarily SMS services have been stopped NON UAE 18-02-2024
                    //         // goto sendSMS;
                    //         goto sendWhatsApp;
                    //     }
                    //     sendWhatsApp:
                    //     $sentsms = Controller::sendWhatsApp($whatsAppArr);
                    //     if (!$sentsms) {

                    //         goto sendSMS;
                    //     } else {
                    //         goto skipSMS;
                    //     }
                    // } else {
                    //     goto sendSMS;
                    // }

                    // sendSMS:
                    // // $sentsms = Controller::sendWhatsApp($request->mobile, $messages);
                    // // Temporarily SMS services have been stopped NON UAE 18-02-2024
                    // if (substr($request->mobile, 0, 3) == "971") {
                    //     $sentsms = Controller::sendsms($request->mobile, $messages, '');

                    //     if (!$sentsms) {
                    //         $sentsms = Controller::sendWhatsApp($whatsAppArr);
                    //     }
                    // }
                    // skipSMS:




                    if ($sentsms) {

                        $response = ['status' => 'success', 'message' => 'Mobile OTP Send Successfully!', 'data' => ['enc' => encrypt(['tempID' => $tempINS, 'expiry' => $timeAfterTenMinutes])]];
                        goto returnFVI;
                    } else {
                        // $response = ['status' => 'failed', 'message' => 'Kindly Use Correct Mobile. WhatsApp Failed!', 'error' => 'Kindly Use Correct Mobile.'];
                        $response = [
                            'status' => 'failed',
                            'message' => 'Please use a valid mobile number. WhatsApp verification failed.',
                            'error' => 'Please use a valid mobile number.'
                        ];
                        goto returnFVI;
                    }
                } else {
                    $response = ['status' => 'failed', 'message' => 'Insert Failed!', 'error' => 'Insert Failed!'];
                    goto returnFVI;
                }
            } else {
                $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => $e->getMessage(), 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }



    public function verifyOTPsign(Request $request)
    {

        try {

            $response = [];
            $input = $request->all();

            $validator = Validator::make($input, [
                'enc' => ['required'],
                // 'method' => ['required', 'in:EMAIL,MOBILE', 'max:10'],
                'otp' => ['required', 'max:6'],
            ]);
            if (!$validator->fails()) {


                $tempID = decrypt($request->enc)['tempID'];

                $expiresAt = decrypt($request->enc)['expiry'];

                // dd($tempID);

                $tempID = Controller::BlockSQLInjection($tempID);
                if ($tempID == '' || $tempID == null || $tempID == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid enc!', 'error' => 'Please use a valid enc!'];
                    goto returnFVI;
                }

                $request->otp = Controller::BlockSQLInjection($request->otp);
                if ($request->otp == '' || $request->otp == null || $request->otp == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please use a valid OTP!', 'error' => 'Please use a valid OTP!'];
                    goto returnFVI;
                }

                if (!Carbon::now()->lt($expiresAt)) {
                    $response = ['status' => 'success', 'message' => "Timeout. Kindly refresh and try again!", 'data' => "Timeout. Kindly refresh and try again!"];
                    goto returnFVI;
                }
                

                $bresult = DB::table('users_temp')->where('id', $tempID)
                    ->where('status', '0')
                    ->where('otp', $request->otp)
                    ->where('deletes', '1')
                    ->orderBy('id', 'DESC')
                    ->first();

                if (isset($bresult) && $bresult->id) {

                    $mobile = $bresult->mobile;
                    $u_password = $bresult->password;
                    $u_name = $bresult->name;
                    
                    $email_verify = DB::table('users_temp')->where('id', $tempID)->update(['mobile_verify' => 'YES']);

                    // dd($email_verify);
                    // $email_verify = mysqli_query($con, "UPDATE `users_temp` SET `mobile_verify` = 'YES' WHERE `users_temp`.`id` = $user_id;");
                    if ($email_verify) {


                        $delete_update = DB::table('users_temp')->where('id', $tempID)->update(['deletes' => '0']);
                        if ($delete_update) {


                            $response = authController::userRegister($tempID, $request->otp, $request->ip(), $request);
                            
                            // $existsWhatsApp = Controller::checkWhatsApp([
                            //     'mobile' => $mobile
                            // ]);
                            
                            if (true) {

                            if($request->deviceType != 'DESKTOP'){
// $messages = "
// 🎉 Welcome to GoRide! 🎉
// Your account is ready ✅

// 🔑 Password: {$u_password}

// Login anytime:
// 🔗 https://www.goride.run/partner-app

// 🚖 Happy Riding!";

$messages = "Hello {$u_name}, Your Goride account is activated. Your password is {$u_password} Click here www.goride.run/partner-app";
                            }else{
                                
// $messages = "
// 🎉 Welcome to GoRide! 🎉
// Your account is ready ✅

// 🔑 Password: {$u_password}

// Login anytime:
// 🔗 https://www.goride.run/login

// App link :
// 🔗 https://www.goride.run/partner-app

// 🚖 Happy Riding!";

$messages = "Hello {$u_name}, Your Goride account is activated. Your password is {$u_password} Click here www.goride.run/partner-app";
                            }
                            
                                
                            

                                $whatsAppArr = [
                                    'mobile' => $mobile,
                                    'templateName' => 'national_draw_verification',
                                    'language' => 'en',
                                    'templateBodyParam' => [],
                                    'messages' => $messages,
                                    'resend' => false
                                ];
                                
                                $check_mess = DB::table('settings')->select('mess_type')->whereNotNull('mess_type')->first();
                                
                                if($check_mess->mess_type == 'sms'){
                                    
                                    $responseeee = Controller::smsNotification($whatsAppArr, 'account_details');
                                    
                                }elseif($check_mess->mess_type == 'whatsapp'){
                                    
                                    $existsWhatsApp = Controller::checkWhatsApp([
                                        'mobile' => $mobile
                                    ]);
                                    if($existsWhatsApp){
                                        
                                        $responseeee = Controller::sendNotification($whatsAppArr);
                                    }
                                
                                    
                                }
                                
                                
                            }

                            // dd($response);
                            goto returnFVI;
                        }
                    } else {
                        $response = ['status' => 'failed', 'message' => 'Invalid OTP!', 'error' => 'OTP Verification failed.'];
                        goto returnFVI;
                    }
                } else {
                    $response = ['status' => 'failed', 'message' => 'Invalids OTP!', 'error' => 'OTP Verification failed.'];
                    goto returnFVI;
                }
                // }
            } else {
                $response = ['status' => 'failed', 'message' => 'Validation Error!', 'error' => [$validator->errors()]];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    public function Upcoming_Prizes(Request $request)
    {
        try {
            $response = [];
            $data = [];
            $today = now()->toDateString();

            $Daily_Thrill = DB::table('draw')
                ->select('dailyThirllPrice', 'dailyThirllStatus', 'resultDate')
                // ->where('saleDate', $today)
                ->where('dailyThirllStatus', 'Active')
                ->where('deletes', '0')
                ->orderBy('id', 'ASC')
                ->limit(1)
                ->get();

            $Weekly_Booster = DB::table('draw as d')
                ->select(
                    'd.weeklyBoosterPrice',
                    'd.dailyThirllPrice',
                    'd.dailyThirllStatus',
                    'd.resultDate',
                    'd.weeklyDrawNo',
                    'd.id',
                    DB::raw('(
                        SELECT SUM(d2.dailyThirllPrice)
                        FROM draw d2
                        WHERE d2.id <= d.id
                        AND d2.id > d.id - 7
                    ) AS previousDailyThirllPriceSum')
                )
                ->where('d.weeklyBoosterStatus', 'Active')
                ->where('d.deletes', '0')
                ->orderBy('d.id', 'ASC')
                ->first();

            if ($Weekly_Booster) {
                $sum = $Weekly_Booster->weeklyBoosterPrice + $Weekly_Booster->previousDailyThirllPriceSum;
                $Weekly_Booster->totalPrice = $sum;
            }

            $Monthly_Bumper = DB::table('draw as d')
                ->select(
                    'd.monthlyBumperPrice',
                    'd.dailyThirllPrice',
                    'd.monthlyBumperStatus',
                    'd.resultDate',
                    'd.bumperDrawNo',
                    'd.id',
                    DB::raw('(
                        SELECT SUM(d2.dailyThirllPrice)
                        FROM draw d2
                        WHERE d2.id <= d.id
                        AND d2.id > d.id - 30
                    ) AS previousDailyThirllPriceSum'),

                    DB::raw('(
                        SELECT SUM(d2.weeklyBoosterPrice)
                        FROM draw d2
                        WHERE d2.id <= d.id
                        AND d2.id > d.id - 30
                    ) AS previousweeklyBoosterPriceSum')


                )
                ->where('d.monthlyBumperStatus', 'Active')
                ->where('d.deletes', '0')
                ->orderBy('d.id', 'ASC')
                ->first();

            if ($Monthly_Bumper) {
                $sum = $Monthly_Bumper->monthlyBumperPrice + $Monthly_Bumper->previousDailyThirllPriceSum + $Monthly_Bumper->previousweeklyBoosterPriceSum;
                $Monthly_Bumper->totalPrice = $sum;
            }
            // ===========================================
            $Year_result = DB::table('draw')
                ->select(
                    DB::raw('(SELECT SUM(d2.dailyThirllPrice) FROM draw d2 WHERE d2.dailyThirllStatus = "Active" AND d2.resultDate > CURDATE()) AS DailyThirllPriceSum'),
                    DB::raw('(SELECT SUM(d2.weeklyBoosterPrice) FROM draw d2 WHERE d2.dailyThirllStatus = "Active" AND d2.resultDate > CURDATE()) AS weeklyBoosterPriceSum'),
                    DB::raw('(SELECT SUM(d2.monthlyBumperPrice) FROM draw d2 WHERE d2.dailyThirllStatus = "Active" AND d2.resultDate > CURDATE()) AS monthlyBumperPriceSum')
                )
                ->where('dailyThirllStatus', 'Active')
                ->where('resultDate', '>=', DB::raw('CURDATE()'))
                ->first(); // Use first() instead of get() to retrieve a single row

            if ($Year_result) {
                $sum = $Year_result->DailyThirllPriceSum + $Year_result->weeklyBoosterPriceSum + $Year_result->monthlyBumperPriceSum;
                $Year_result->totalPrice = $sum;
            }






            $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => ['Daily_Thrill' => $Daily_Thrill, 'Weekly_Booster' => $Weekly_Booster, 'Monthly_Bumper' => $Monthly_Bumper, 'Year_Result' => $Year_result]];
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }

    public function Download_pdf_whatsapp(Request $request)
    {
        try {
            $userID = auth()->user()->id;
            // return($userID);

            $input = $request->all();

            $validator = Validator::make($input, [
                'userID' => ['required', 'regex:/^\+?[0-9]+$/'],
            ]);

            if (!$validator->fails()) {
                // $userID = Controller::BlockSQLInjection($request->userID);

                $userDetails = DB::table('user_register')
                    ->select('name', 'lname', 'mobile', 'email')
                    ->where('id', $userID)
                    ->where('status', '0')
                    ->where('roll_id', '0')
                    ->where('deletes', '0')
                    ->first();

                if ($userDetails) {
                    $whatsAppArr = [
                        'mobile' => $userDetails->mobile,
                        'templateName' => 'nd_prize_details_v4',
                        'language' => 'en',
                        'templateBodyParam' => [],
                        'messages' => 'Thanks for participating in the Go Ride!! To know more about the Go Ride Prize Details, Click on the image to Download the catalogueðŸ‘†',
                        'urlFile' => env('DO_REDIRECT_URL') . 'nationaldraw/1/National%20Draw%20-%20Details.pdf',
                        'fileName' => 'Go Ride - Details.pdf'
                    ];

                    // $sentsms = Controller::sendWhatsApp($whatsAppArr);
                    $sentsms = Controller::sendNotification($whatsAppArr);
                    // dd($sentsms);
                    if ($sentsms) {

                        return response()->json(['status' => 'success', 'message' => 'The WhatsApp PDF send successfully.', 'data' => $userDetails]);
                    }
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'The user does not exist.']);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Validation failed.']);
            }








            $response = ['status' => 'success', 'message' => 'Details Collected Successfully', 'data' => ['Daily_Thrill' => $Daily_Thrill, 'Weekly_Booster' => $Weekly_Booster, 'Monthly_Bumper' => $Monthly_Bumper, 'Year_Result' => $Year_result]];
            return response()->json($response);
        } catch (Exception $e) {
            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
    
    public function bot_chat(Request $request){
        
$system_prompt = <<<EOD
You are GoRide’s intelligent support assistant Your Name is GoBot. GoRide is an AI-driven dispatch and CRM platform for taxi, limo, chauffeur, and ride-hailing businesses.

Respond only in English, regardless of the user's input language.

On the Second Or Third Message you could ask their Name and phone number. 

Only answer queries related to GoRide. If unrelated, respond with:
“I’m GoRide’s assistant and can help only with GoRide software. For other questions, please email support@goride.run or call +91 63697 42104.”

Key Features:
- Real-time AI dispatch
- Driver & Passenger Apps (Android/iOS)
- CRM dashboard with analytics, bookings, zones
- White-label portal for websites & apps
- GPS tracking, route optimization
- Flexible payments: cards, wallets, cash
- Reporting & analytics
- Pricing:
  • Free: 1 driver, 5 bookings
  • Pro: $19/mo, 3 drivers, 50 bookings
  • Enterprise: $39/mo, 10 drivers, 250 bookings
- 100+ integrations (Twilio, Stripe, AWS, etc.)
- Supports multi-language, multi-currency
- Vision: Leading global taxi tech
- Mission: Enhance taxi efficiency and growth
- Support: support@goride.run | +91 63697 42104
- Steps to Sign Up:- https://www.goride.net.in/signup - Go to this page, Enter your name, WhatsApp Number, Email, PAssword, And Click Next, 
OTP will be sent to your entered number and enter it and verify your account. Then Login with your Account using the Created Phone and Password. 
- Sign In Via OTP is also available. https://www.goride.net.in/loginwithotp
- Forgot password also is available. https://www.goride.net.in/forgot
-GoRide offers three pricing plans. The Free Plan, priced at ₹0, provides full access for up to 1 driver, 5 bookings, and 1 website for 1 month. The Professional Plan, at ₹499/month (₹16.1 per day), includes full access for up to 3 drivers, 50 bookings, and 1 website. The Enterprise Plan, priced at ₹999/month (₹32.2 per day), offers full access for up to 10 drivers, 250 bookings, and up to 3 websites. All plans are valid for 1 month.
- GoRide also offers annual plans for better value. The Professional Annual Plan is ₹4,999 for 12 months (₹27.4 per day), offering full access for up to 3 drivers, 600 bookings, and 1 website. The Enterprise Annual Plan costs ₹9,999 for 12 months, allowing full access for up to 10 drivers, 3,000 bookings, and up to 3 websites. Both plans provide complete feature access and are ideal for growing fleets.
-GoRide is more than just a CRM — it’s a powerful AI-based dispatch platform built to optimize every aspect of your taxi or ride-hailing business. It combines intelligent dispatch automation, comprehensive customer relationship management, real-time GPS tracking, analytics, and seamless in-app communication. GoRide matches the right driver to each ride using AI, reducing wait times and improving service reliability. Its CRM tools help build stronger customer relationships through ride history, preferences, and feedback tracking. Fleet operators benefit from real-time driver tracking, traffic insights, and analytics for better decision-making. GoRide also supports flexible payment methods (cash, cards, wallets) and ensures direct communication between dispatchers, drivers, and passengers. The platform is fully customizable and scalable—ideal for both small operators and large fleets—helping businesses increase efficiency, enhance customer satisfaction, improve driver experience, and grow with confidence.
- Thanks! Welcome Back. 
- Services Contries, India, UK, Canada and All over the World we provide the Dispatched software. 
- No Cab Bookings in the GoRide. 
- Goride Provide Software for the Cab Management, where you can create a job for your company. 
- We provide a Free Website for your company, Its fully customizable. 
Go + Customize
Looking for a tailored solution? Contact our sales team to get the most affordable, customized pricing for your business needs. 
- Do not reply for any bad words.
support@goride.run | phone number or mobile number or call number +91 63697 42104
- You can set the price as per your wish; Pricing by Area, Pricing by Time, Pricing By Zone, Zone to Zone, Area to
Area, Fixed Prices and Kms Based or Miles based pricing all are available. 
Extra charges, Night time charges, Meet and Greet is also available in your CRM you should set up it and config it. 
EOD;

    $userInput = trim($request->input('message', ''));
    $userInput = strip_tags($userInput);
   

    if (!$userInput || strlen($userInput) > 500) {
        return response("Invalid input.", 400);
    }
    
    $userId = $request->user() ? $request->user()->id : null;
    $ip = $request->ip();

    
    $useSession = $userId != null;
    $sessionKey = null;
    $cookieKey = 'chatbot_state_cookie';
    
    $defaultState = [
        'question_count' => 0,
        'info_step' => 1,
        'user_collected' => false,
        'temp_name' => null,
        'temp_phone' => null,
    ];
    
    // Step 1: Retrieve state
    if ($useSession) {
        $sessionKey = 'chatbot_state';
        $state = Session::get($sessionKey, $defaultState);
    } else {
        $cookieData = $_COOKIE[$cookieKey] ?? null; // 👈 native $_COOKIE
        $state = $cookieData ? json_decode($cookieData, true) : $defaultState;
    }
    
    // Step 2: Sanitize input
    // $userInput = preg_replace('/[^\p{L}\p{N}\s\?\!\.\,\-]/u', '', $userInput);
    // $userInput = mb_convert_encoding($userInput, 'UTF-8', 'auto');
    
    // Step 3: Increment question count
    $state['question_count']++;
    
    // Step 4: Handle info collection flow
    if ($state['question_count'] >= 3 && $state['question_count'] < 7 && !$state['user_collected']) {
        $infoStep = $state['info_step'];
    
        switch ($infoStep) {
            case 1:
                $state['info_step'] = 2;
                return $this->saveAndRespond(
                    "Before we continue, may I know your **name** to assist you better?",
                    $useSession,
                    $state,
                    $sessionKey ?? $cookieKey
                );
    
            case 2:
                $state['temp_name'] = $userInput;
                $state['info_step'] = 3;
                return $this->saveAndRespond(
                    "Thanks, " . e($userInput) . "! Could you please share your **phone number**?",
                    $useSession,
                    $state,
                    $sessionKey ?? $cookieKey
                );
    
            case 3:
                if (!preg_match('/^\+?[0-9]{7,15}$/', $userInput)) {
                    return response("That doesn't look like a valid phone number. Please enter a number with 7 to 15 digits.");
                }
    
                $state['temp_phone'] = $userInput;
                $state['info_step'] = 4;
    
                // 🔁 Switch cookie key to phone-based
                // if (!$useSession) {
                //     $cookieKey = 'chatbot_state_' . preg_replace('/\D/', '', $userInput);
                //     setcookie($cookieKey, json_encode($state), time() + 1800, "/");
                //     $_COOKIE[$cookieKey] = json_encode($state); // 👈 simulate for current request
                // }
    
                return $this->saveAndRespond(
                    "Perfect! Lastly, may I have your **email address**?",
                    $useSession,
                    $state,
                    $sessionKey ?? $cookieKey
                );
    
            case 4:
                if (!preg_match('/^[\w\.\-]+@([\w\-]+\.)+[a-zA-Z]{2,7}$/', $userInput)) {
                    return response("Hmm, that doesn't look like a valid email. Please try again.");
                }

    
                // Save to DB
                // DB::table('chatbot_users')->insert([
                //     'name'       => $state['temp_name'],
                //     'phone'      => $state['temp_phone'],
                //     'email'      => $userInput,
                //     'ip_address' => $ip,
                //     'user_id'    => $userId,
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);
                DB::table('chatbot_users')->updateOrInsert(
                    ['phone' => $state['temp_phone']], // condition
                    [
                        'name'       => $state['temp_name'],
                        'email'      => $userInput,
                        'ip_address' => $ip,
                        'user_id'    => $userId,
                        'updated_at' => now(),
                        'created_at' => now(), // optional: will be ignored if updating
                    ]
                );

    
                $state['user_collected'] = true;
    
                return $this->saveAndRespond(
                    "Welcome, " . e($state['temp_name']) . "! How can I assist you further with GoRide?",
                    $useSession,
                    $state,
                    $sessionKey ?? $cookieKey
                );
        }
    } else {
        // Step 5: Save updated state
        if ($useSession) {
            Session::put($sessionKey, $state);
            Session::save();
        } else {
            setcookie($cookieKey, json_encode($state), time() + 1800, "/");
            $_COOKIE[$cookieKey] = json_encode($state); // 👈 simulate for same request
        }
    }
    

    // Step 2: Load FAQ
    $faqPath = public_path('goride/faq/faq.json');
    if (!file_exists($faqPath)) {
        return response("FAQ not found.", 404);
    }

    $faqData = json_decode(file_get_contents($faqPath), true);
    $matchedAnswer = null;

    foreach ($faqData as $faq) {
        similar_text(mb_strtolower($userInput), mb_strtolower($faq['question']), $percent);
        if ($percent > 85) {
            $matchedAnswer = $faq['answer'];
            break;
        }
    }

    if ($matchedAnswer) {
        return response($matchedAnswer);
    }

    // Step 3: Call OpenAI
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => $system_prompt],
            ["role" => "user", "content" => $userInput]
        ],
        "temperature" => 0.7,
        "max_tokens" => 300
    ];

    // Ensure everything is UTF-8
        $data = $this->utf8ize($data);
    
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPEN_AI_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', $data);
    
        
        if (!file_exists($faqPath)) {
            file_put_contents($faqPath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        
        $faqData = json_decode(file_get_contents($faqPath), true);
        $isDuplicate = false;
        foreach ($faqData as $faq) {
            if (strtolower(trim($faq['question'])) == strtolower(trim($userInput))) {
                $isDuplicate = true;
                break;
            }
        }
        
        if (!$isDuplicate) {
            $faqData[] = [
                "question" => $userInput,
                "answer" => $response->json('choices.0.message.content')
            ];
            file_put_contents($faqPath, json_encode($faqData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    
        if ($response->successful()) {
            $reply = $response->json('choices.0.message.content');
            return response($reply);
        } else {
            return response("Sorry, I couldn't process that.", 500);
        }
        
    }
    
    public function getChat_message(Request $request){
        
        $userId = auth()->user()? auth()->user()->id : null;
        $ip = $request->ip();
        
        
        if($userId){
            $t_col = 'user_id';
            
            
        }else{
            
            $t_col = 'ip_address';
        }
        
        $get_data = DB::table('chatbot_users')->where($t_col, $userId??$ip)->get();
        
        if($get_data){
            
            
            
        }else{
            
            
            
        }
        
        return response()->json($get_data);
    }
    
    private function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
        }
        return $mixed;
    }
    
    private function saveAndRespond($message, $useSession, $state, $key)
    {
        if ($useSession) {
            Session::put($key, $state);
        } else {
            setcookie($key, json_encode($state), time() + 1800, "/");
            $_COOKIE[$key] = json_encode($state); 
        }
    
        return response($message);
    }


}