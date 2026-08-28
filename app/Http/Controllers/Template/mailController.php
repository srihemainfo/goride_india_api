<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class mailController extends Controller
{
 public static function Email_update(array $request)
  {
    $html = '<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registration-Welcome</title>
    <style type="text/css">
        @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");


        body {
            margin: 0;
        }

        .wrapper {
            background: #CCC;
        }

        .main {
            background: #FFF;
            max-width: 600px;
        }

        table {
            border-spacing: 0;
        }

        td {
            padding: 3px;
        }

        img {
            border: 0;
        }

        .column-one {
            text-align: center;
            margin: 0 auto;
        }

        .column-one .column {
            width: 100%;
            margin: 0 auto;
        }

        .im {
            color: #01104e;
        }

        .column-one h3 {
            color: #01104e;
            font-family: Verdana, sans-serif !important;
            font-size: 28px;
            font-weight: 600;
            margin: 14px 0 0 0;
        }

        .column-one p {
            color: #01104e;
            font-family: Verdana, sans-serif !important;
            font-size: 19px;
            font-weight: 500;
            margin: 4px 0;
        }
        ul li {
    display: inline;
    border: 2px solid #171f4f;
    color: #171f4f;
    border-radius: 24%;
    padding: 4px 10px;
    font-size: 24px;
    font-weight: 600;
    margin: 0 1px;
}  </style>
</head>

<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- BORDER -->
            <tr>
                <td style="background-color: #171f4f; height: 45px;"></td>
            </tr>
            <tr>
                <td class="column-one" style="background: #088b42;height:10px;">
                </td>
            </tr>
            <!-- <tr>
               <td style="background-color: #339a46; height: 45px;"></td>
               </tr> -->
            <tr>
                <td class="column-one">
                    <table class="column">
                        <tr>
                            <td valign="top" style="padding: 0;">
                                <center>
                                    <br>
                                    <img src="'.   env('DO_REDIRECT_URL') . 'nationaldraw/1/logo123.png" style="border: 0px; margin:10px 0;  " width="50%">
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" style="padding: 0;">
                                <center>
                                    <!-- <br> -->
                                    <img src="'. env('DO_REDIRECT_URL') . 'nationaldraw/1/otp-verify-123.png" style="border-radius: 19px;" width="43%">
                                    <br>
                                </center>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- LOGO  -->
            <tr>
                <td class="column-one c-f">
                    <!-- <br> -->
                    <!-- <p style="font-weight: 600!important;">Hi, First Name Last Name</p> -->

                    <p
                        style="font-size:15px; font-weight: 500!important; font-family: Verdana, sans-serif !important; margin:14px 0;">
                        Please use this OTP to complete the <br>process of changing your email address.
                        <!-- Please complete your email change<br> using the OTP below. -->
                       
                        <!-- Please complete your registration
                        <br>using the OTP below. -->
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <ul
                        style="color: #01104e;font-family: Verdana, sans-serif !important;font-size: 15px;font-weight: 500; list-style: none; text-align: center; padding: 0; margin:0 ; line-height: 1.5;">
                   
                        <li>' . $request['randotp'][0] . '</li> 
                        <li>' . $request['randotp'][1] . '</li> 
                        <li>'. $request['randotp'][2] .  '</li> 
                        <li>' . $request['randotp'][3] . '</li> 
                        <li>'. $request['randotp'][4] .  '</li> 
                        <li>' . $request['randotp'][5] . '</li>

                    </ul>
                </td>
            </tr>
            <!-- <tr>
                <td class="column-one ">
                    <p
                        style="font-size:13px; font-weight: 500!important; font-family: Verdana, sans-serif !important; margin:14px 0;">
                        Note: Your OTP will be valid for only XXX minutes.
                    </p>
                </td>
            </tr> -->
            <tr>
                <td class="column-one">
                    <br>
              <img style="width: !important;margin-top: 10px;" src="'. env('DO_REDIRECT_URL')  . 'nationaldraw/1/ndFooter.png" width="84%">
                </td>
            </tr>
            <tr>
                <td>
                    <p
                        style="color: #171f4f !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                        Note: This is a system auto-generated email. Please do not reply to this mail.
                    </p>
                </td>
            </tr>
            <tr>
                <td class="column-one" style="background: #171f4f; height:10px;">
                </td>
            </tr>
        </table>
        <!-- End Main Class -->
    </center>
    <!-- End Wrapper -->
</body>

</html>';

    return $html;
  }
  public static function forgot(array $request)
  {
    //   dd($request['name']);

    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

                    <html xmlns="http://www.w3.org/1999/xhtml">
                    
                    <head>
                    
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    
                    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
                    
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    
                    <title>Ticket Purchase OTP Mail Template</title>
                    
                    <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=VYoSGN8lSeyo4fOYSOiX1gKMyBI3TuPA184x9Eo1x1M7fkxGa1UMBzpe8PLsCGK5fm67p-Jpeb3bvtc9WKdFNU9bW7lCEol91zPUNoQFZ20" charset="UTF-8"></script><style type="text/css">
                    
                    
                    
                      @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
                    
                      body {
                    
                        margin: 0;
                    
                      }
                    
                      .wrapper {
                    
                        
                    
                        background:#CCC;
                    
                    
                        }
                    
                      .main {
                    
                    
                        background:#FFF;
                    
                        max-width:600px;
                    
                    
                        } 
                    
                    
                      table {
                    
                        border-spacing: 0;
                    
                      }
                    
                      td {
                    
                        padding: 3px;
                        
                    
                    
                      }
                    
                      img {
                    
                        border: 0;
                    
                      }
                    
                      .column-one {
                    
                    
                    
                        text-align:center;
                    
                        margin:0 auto;
                    
                        }
                    
                      .column-one .column {
                    
                        
                    
                        width:100%;
                    
                          margin:0 auto;
                    
                      
                    
                        }
                    
                        
                    
                    
                    
                    
                    
                    </style>
                    
                    </head>
                    
                    <body>
                   
                      <center class="wrapper">
                    
                    
                    
                        <table class="main" width="100%" style="background-color: #fff;">
                    
                            <!-- BORDER -->
                    
                            <tr><td class="column-one" style="background: #29377d; height:50px;">
                    
                     
                    
                            
                    
                            </td></tr>
                    
                            
                    
                            <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;">
                    
                     
                    
                            
                    
                            </td></tr>
                    
                            
                    
                            <!-- BORDER -->
                    
                            
                    
                            <!-- LOGO  -->
                    
                            <tr><td class="column-one" >
                    
                            <table class="column"> <tr><td valign="top" style="padding: 16px 0 0px 0;">  
                    
                            <center>
                    
                              <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >
                    
                            
                    
                            </center>
                    
                            
                    
                              </td></tr></table>
                    
                            
                    
                            </td></tr>
                    
                            <!-- LOGO  -->
                    
                                    <tr>
                    
                                      <td class="column-one" >
                    
                            <table align="center" class="column"> <tr><td valign="top" >  
                    
                     <div style="margin:0 auto;  max-width:500px; display:block; ">
                    
                             <div style="width:100px; float:left; ">      <img style="border: 0px;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/char.png" ></div>
                    
                             <div>
                    
                    <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 30px; margin: 0px; text-align: center;font-weight: 500;">Hi, ' . $request['name'] . ' 
                    
                                          <br>
                    
                                        </h3>
                    
                                       
                    
                                      <p style="color: #29377d;font-weight: 500; font-family: Arial Narrow;font-style: italic; font-size:165%;  margin: 13px 8px 13px 8px; text-align: center;">Are you&nbsp;<span style="color:#be1e2d;">excited</span> to<br>
                                       <span style="color:#be1e2d;">Play</span>&nbsp;With GO RIDE</p>
                    
                                       <h3 style="color: #29377d; font-family: Arial Narrow;  font-style: italic;font-size:196%; margin: 0px; text-align: center;">Forgot Your Password?
                    
                                          <br>
                    
                                        </h3></div>
                    
                       
                    
                            </div>
                    
                              </td></tr></table>
                    
                            
                    
                            </td></tr>
                    
                    
                    
                    <tr>
                    
                                      <td class="column-one" >
                    
                            <table align="center" class="column"> <tr>
                    
                              <td valign="top" >  
                    
                                <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                    
                          <tbody>
                    
                            <tr>
                    
                              <td style="color: #29377d; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">
                    
                                <p style="color: #29377d;  font-size:145%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Please use the below OTP reset your password<br>which will be valid for 15 minutes</p>
                    
                              </td>
                    
                                    </tr>
                    
                                    <tr>
                                <td style=" border-radius: 4px 4px 0px 0px; color: #111111; font-size: 24px; line-height: 24px;" align="center" valign="top" >
                                  <h3 style="color: #ffffff; font-size: 36px; margin: 0px; font-style: italic; font-family: Arial Narrow;padding: 9px; background: #be1e2d; width: 119px; line-height: 1; border-radius: 11px; border: 3px dashed #ffffff;">' . $request['randotp'] . '</h3>
                                </td>
                              </tr>
                    
                          </tbody>
                    
                        </table>
                    
                        <!-- <br>   -->
                    
                        <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                    
                          <tbody>
                    
                            <tr>
                    
                              <td style="color: #29377d; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">
                    
                                <p style="color: #29377d;  font-size:147%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">If you did not forgot your password you<br>can safely ignore this email</p>
                    
                              </td>
                    
                                    </tr>
                          </tbody>
                    
                        </table>
                    
                        <!-- <br style="color: #000000;  font-size: medium; background-color: #fbfbfb;"> -->
                    
                        <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                    
                          <tbody>
                    
                            <tr>
                    
                              <td class="gmail-line" style="box-sizing: border-box; width: 8px;">
                    
                                <img  style="width:489px !important;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/center_img2.png">
                    
                              </td>
                    
                            </tr>
                    
                          </tbody>
                    
                        </table>
                        <br>
                    
                      
                        <p style="color: #29377d !important;  font-size: 150% !important; margin: 0px !important; text-align: center !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Need help?
                    +971 433 98880<br>support@nationaldrawuae.com
                    
                        </p>
                    
                    <br>
                    
                       
                    <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>

                    For Clarification
                    
                    
                           <br>
                    
                    Call 04 33 98880 Whatsapp +971 56 199 1271
                    
                    <br>
                    
                    or email support@nationaldrawuae.com</p>
                              </td></tr></table>
                    
                            
                    
                            </td></tr>
                    
                    
                        </table> <!-- End Main Class -->
                    
                      </center>
                    
                    
                    
                      </center> <!-- End Wrapper -->
                    
                    
                    
                    </body>
                    
                    </html>';
    return $html;
  }



  public static function signUPotp(array $request)
  {
    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ticket Purchase OTP Registered Template</title>

    <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=cOHVXauret47m7vfvvQhf02-NcodCzBsAzoj0F1AHQgPkD9Rj1YHZaoHPpoqjmlFYOj6jJ3T7iZ4ouw5wIdI1iWh-rYN2IwIddwzX0pKgHcRyYHURyLdo5E9133N8cCX" charset="UTF-8"></script><style type="text/css">



      @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

      body {

        margin: 0;

      }

      .wrapper {



        background:#CCC;



        }

      .main {



        background:#FFF;

        max-width:600px;



        }



      table {

        border-spacing: 0;

      }



      img {

        border: 0;

      }

      .column-one {



        text-align:center;

        margin:0 auto;

        }

      .column-one .column {



        width:100%;

          margin:0 auto;



        }







    </style>

    </head>

    <body>



      <center class="wrapper">



        <table class="main" width="100%">

            

            <tr><td class="column-one" style=" background: #29377d; height:54px;">





            </td></tr>



            <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:15px;">





            </td></tr>




            <tr><td class="column-one" >

            <table class="column"> <tr><td valign="top" style="padding: 16px 0 37px 0;">

            <center>

              <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png"  style="border: 0px;"  >



            </center>



              </td></tr></table>



            </td></tr>

            <!-- LOGO  -->

                    <tr>

                      <td class="column-one" >

              <table align="center" class="column" style="
        background: url(' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/new_man.png)no-repeat;
        height: 300px;background-position: center;    margin: -26px 0 0 0 !important;
        "> <tbody><tr><td colspan="3" valign="top" style="padding:10px 0px 0px 10px;">


    <h3 class="demoname" style="color: #be1e2d;  font-family: Arial Narrow;font-style: italic;font-size: 32px; margin: 0px 0px 0px 24px; text-align: center;">Hi, ' . $request['name'] . '


                        </h3>


                    </td></tr><tr>
                      <td>


                     </td>


    </tr>


              </tbody></table>


            </td></tr>



    <tr>

                      <td class="column-one" >

            <table align="center" class="column"> <tr>

              <td valign="top" >

                <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

          <tbody>



                    <tr>

              <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                <p style="color: #29377d;  font-size:163%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Please use the below OTP to complete the<br>registration with GO RIDE</p>

              </td>

                    </tr>

                    <tr>
                <td style=" border-radius: 4px 4px 0px 0px; color: #111111; font-size: 24px; line-height: 24px;padding: 10px;" align="center" valign="top" bgcolor="#ffffff">
                  <h3 style="color: #ffffff; font-size: 36px; margin: 0px; font-style: italic; font-family: Arial Narrow;padding: 9px; background: #be1e2d; width: 119px; line-height: 1; border-radius: 11px; border: 3px dashed #ffffff;">' . $request['randotp'] . '</h3>
                </td>
              </tr>

          </tbody>

        </table>

        <br>





        <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

          <tbody>

            <tr>

              <td class="gmail-line" style="box-sizing: border-box; width: 8px;">

                <img  style="width:489px !important;"src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/center_img2.png">

              </td>

            </tr>

          </tbody>

        </table>
        <br>


        <p style="color: #29377d !important;  font-size: 22px !important; margin: 0px !important; text-align: center !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Need help?
    +971 433 98880<br>support@nationaldrawuae.com

        </p>

    <br>

    
        <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
        
        For Clarification
        
         
        
               <br>
        
        Call 04 33 98880 Whatsapp +971 56 199 1271
        
        <br>
        
        or email support@nationaldrawuae.com</p>
              </td></tr></table>



            </td></tr>


        </table>



      </center>



    </body>

    </html>';

    return $html;
  }
  public static function otpverify(array $request)
  {
    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ticket Purchase OTP Registered Template</title>

    <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=cOHVXauret47m7vfvvQhf02-NcodCzBsAzoj0F1AHQgPkD9Rj1YHZaoHPpoqjmlFYOj6jJ3T7iZ4ouw5wIdI1iWh-rYN2IwIddwzX0pKgHcRyYHURyLdo5E9133N8cCX" charset="UTF-8"></script><style type="text/css">



      @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

      body {

        margin: 0;

      }

      .wrapper {



        background:#CCC;



        }

      .main {



        background:#FFF;

        max-width:600px;



        }



      table {

        border-spacing: 0;

      }



      img {

        border: 0;

      }

      .column-one {



        text-align:center;

        margin:0 auto;

        }

      .column-one .column {



        width:100%;

          margin:0 auto;



        }







    </style>

    </head>

    <body>



      <center class="wrapper">



        <table class="main" width="100%">

            

            <tr><td class="column-one" style=" background: #29377d; height:54px;">





            </td></tr>



            <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:15px;">





            </td></tr>




            <tr><td class="column-one" >

            <table class="column"> <tr><td valign="top" style="padding: 16px 0 37px 0;">

            <center>

              <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png"  style="border: 0px;"  >



            </center>



              </td></tr></table>



            </td></tr>

            <!-- LOGO  -->

                    <tr>

                      <td class="column-one" >

              <table align="center" class="column" style="
        background: url(' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/new_man.png)no-repeat;
        height: 300px;background-position: center;    margin: -26px 0 0 0 !important;
        "> <tbody><tr><td colspan="3" valign="top" style="padding:10px 0px 0px 10px;">


    <h3 class="demoname" style="color: #be1e2d;  font-family: Arial Narrow;font-style: italic;font-size: 32px; margin: 0px 0px 0px 24px; text-align: center;">Hi, ' . $request['name'] . '


                        </h3>


                    </td></tr><tr>
                      <td>


                     </td>


    </tr>


              </tbody></table>


            </td></tr>



    <tr>

                      <td class="column-one" >

            <table align="center" class="column"> <tr>

              <td valign="top" >

                <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

          <tbody>



                    <tr>

              <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">

                <p style="color: #29377d;  font-size:163%; text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;">Please use the below OTP to complete the<br>registration with GO RIDE</p>

              </td>

                    </tr>

                    <tr>
                <td style=" border-radius: 4px 4px 0px 0px; color: #111111; font-size: 24px; line-height: 24px;padding: 10px;" align="center" valign="top" bgcolor="#ffffff">
                  <h3 style="color: #ffffff; font-size: 36px; margin: 0px; font-style: italic; font-family: Arial Narrow;padding: 9px; background: #be1e2d; width: 119px; line-height: 1; border-radius: 11px; border: 3px dashed #ffffff;">' . $request['randotp'] . '</h3>
                </td>
              </tr>

          </tbody>

        </table>

        <br>





        <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">

          <tbody>

            <tr>

              <td class="gmail-line" style="box-sizing: border-box; width: 8px;">

                <img  style="width:489px !important;"src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/center_img2.png">

              </td>

            </tr>

          </tbody>

        </table>
        <br>


        <p style="color: #29377d !important;  font-size: 22px !important; margin: 0px !important; text-align: center !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Need help?
    +971 433 98880<br>support@nationaldrawuae.com

        </p>

    <br>

  
        <p style="color: #29377d !important;font-size: 15px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
        
        For Clarification
        
         
        
               <br>
        
        Call 04 33 98880 Whatsapp +971 56 199 1271
        
        <br>
        
        or email support@nationaldrawuae.com</p>
              </td></tr></table>



            </td></tr>


        </table>



      </center>



    </body>

    </html>';

    return $html;
  }

  //   delete user template

  public static function DeleteUsers(array $request)
  {
    //   dd($email);

    // $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    // <html xmlns="http://www.w3.org/1999/xhtml">

    // <head>

    //  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    //  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    //  <meta name="viewport" content="width=device-width, initial-scale=1.0">

    //  <title>Account Deletion  Mail Template</title>

    //  <script type="text/javascript" src="https://gc.kis.v2.scr.kaspersky-labs.com/FD126C42-EBFA-4E12-B309-BB3FDD723AC1/main.js?attr=yuNx6KMmuSX7G888_pK_iflKVisJPyCIZHaiZKJ6_xjmtUHZZy7e-WelDWLIWGC1JW_2FnJUtJeJjI_QFlJ4CJHiU7vyWEwFPWr7-hwzWgO0pRYSCwjaA-G2CSKIavTp" charset="UTF-8"></script><style type="text/css">



    //    @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");

    //    body {

    //      margin: 0;

    //    }

    //    .wrapper {



    //      background:#CCC;



    //      }

    //    .main {



    //      background:#FFF;

    //      max-width:600px;



    //      } 



    //    table {

    //      border-spacing: 0;

    //    }

    //    td {

    //      padding: 3px;

    //    }

    //    img {

    //      border: 0;

    //    }

    //    .column-one {



    //      text-align:center;

    //      margin:0 auto;

    //      }

    //    .column-one .column {



    //      width:100%;

    //        margin:0 auto;



    //      }


    //  </style>

    //  </head>

    //  <body>


    //    <center class="wrapper">


    //      <table class="main" width="100%">

    //          <!-- BORDER -->

    //          <tr><td class="column-one" style="background: #29377d; height:50px;">


    //          </td></tr>

    //                  <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;">        

    //          </td></tr>        

    //          <tr><td class="column-one" >

    //          <table class="column"> <tr>
    //            <td valign="top" style="padding: 16px 0 0px 0;">  

    //          <center>

    //            <img src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >

    //          </center>

    //            </td></tr></table>



    //          </td></tr>

    //          <!-- LOGO  -->

    //                  <tr>

    //                    <td class="column-one" >

    //          <table align="center" class="column"> <tr><td valign="top" >  

    //   <div style="margin:0 auto;  max-width:500px; display:block;">

    //           <div style="width:110px; float:left; margin: 0 0 0 0;">      <img style="border: 0px; " src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/LD-02.png" >
    //             <img style="border: 0px; " src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/Account-text.png" >
    //         </div>

    //           <!-- <div style="width:110px; float:left; margin: 0 0 0 0;">      <img style="border: 0px; " src="' .  ($request->header('Origin') . '/') . 'assets/images/mailtemplate/Account-text.png" ></div> -->


    //           <div  style="">

    //  <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px;text-align: center;font-weight: 600; margin: 0px 0 18px;"> Hi,'  . $request['name'] .  '

    //                        <br>

    //                      </h3>

    //                      <p style="color: #29377d;font-family: Arial Narrow;font-style: italic;font-size: 147%;margin: 0px 8px 13px 8px;text-align: center;font-weight: 500; width: 354px;
    //                      float: right;">We have received your request for your account deletion and is currently under process.</p>

    //                        <p style="color: #29377d;font-family: Arial Narrow;font-style: italic;font-size: 140%;margin: 11px 0;text-align: center;font-weight: 500; width: 354px;
    //                        float: right;">
    //                       Your personal information and data will be permanently deleted from our systems as per your request
    //                       <strong style="
    //                       font-size: 111%;">within 30 days.</strong> 
    //                        </p>

    //                      </h3></div>

    //          </div>

    //            </td></tr></table>
    //        </td></tr>

    //  <tr>

    //                    <td class="column-one" >
    //  <hr style="border-top:1px solid #d6dbf4; width: 392px;">

    //  <p style="font-family: Arial Narrow; color: #be1e2d; width: 389px;margin: auto;font-weight: 500;font-size: 120%;"><strong style="font-weight: 700; ">Please Note:</strong> Avoid login to your account 
    // during this period to maintain deletion request.</p>
    // <hr style="border-top:1px solid #d6dbf4;font-size: 112%; width: 392px;">
    //          <table align="center"  class="column"> <tr>

    //            <td valign="top" >  











    //     <br>
    //     <p style="color: #29377d !important;font-size: 17px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
    //       For Clarification

    //              <br>
    //       Call 04 33 98880 Whatsapp +971 56 199 1271
    //       <br>
    //       or email support@nationaldrawuae.com</p>

    //            </td></tr></table>

    //          </td></tr>

    //      </table> <!-- End Main Class -->



    //    </center> <!-- End Wrapper -->



    //  </body>

    // </html>';



    $html = '<!DOCTYPE html
       PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
       <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <meta http-equiv="X-UA-Compatible" content="IE=edge" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Account-deletion</title>
          <style type="text/css">
             @import url("https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap");
             @import url("https://fonts.cdnfonts.com/css/verdana");
             body {
             margin: 0;
             }
             .wrapper {
             background: #CCC;
             }
             .main {
             background: #FFF;
             max-width: 600px;
             }
             table {
             border-spacing: 0;
             }
             td {
             padding: 3px;
             }
             img {
             border: 0;
             }
             .column-one {
             text-align: center;
             margin: 0 auto;
             }
             .column-one .column {
             width: 100%;
             margin: 0 auto;
             }
             .im {
             color: #01104e;
             }
             .column-one h3 {
             color: #01104e;
             font-family: Verdana, sans-serif !important;
             font-size: 28px;
             font-weight: 600;
             margin: 14px 0 0 0;
             }
             .column-one p {
             color: #01104e;
             font-family: Verdana, sans-serif !important;
             font-size: 19px;
             font-weight: 500;
             margin: 4px 0;
             }
             td.column-two p {
             text-align: justify;
             color: #01104e;
             font-family: Verdana, sans-serif !important;
             font-size: 17px;
             font-weight: 500 !important;
             padding: 0 10%;
             margin: auto;
             }
             .c-f {
             padding: 8px 0;
             }
             .d-red{
                margin: 0 7.5%!important;
                line-height: 1.4;
             }
             @media only screen and (max-width: 600px){
                .d-red{
                margin: 0 4.5%!important;
             }
             }
          </style>
       </head>
       <body>
          <center class="wrapper">
             <table class="main" width="100%">
                <!-- BORDER -->
                <!-- <tr>
                   <td class="column-one f" style="background: #01104e; height:50px;">
                   </td>
                   </tr> -->
                <tr>
                   <td class="column-one">
                      <table class="column">
                         <tr>
                            <td valign="top" style="padding: 0;">
                               <center>
                                  <br>
                                  <img src="https://assets.nationaldrawuae.com/nationaldrawuae/1/nationaldrawuaeLogo.png"
                                     style="border: 0px;" width="35%">
                               </center>
                            </td>
                         </tr>
                         <tr>
                            <td valign="top" style="padding: 0;">
                               <center>
                                  <br>
                                  <img src="https://assets.nationaldrawuae.com/nationaldrawuae/1/willMissYouEmailTemplate.png" style="border: 0px;" width="84%">
                                  <br>
                               </center>
                            </td>
                         </tr>
                      </table>
                   </td>
                </tr>
                <!-- LOGO  -->
                <tr>
                   <td class="column-one c-f">
                      <br>
                      <p style="font-weight: 600!important;">Hi '  . $request['name'] .  ',</p>
                      <p style="font-size: 14px; font-weight: 400!important; ">We have received your request to delete your account.<br> Your request is under process.
                      </p>
                      <p
                         style="font-size:14px; font-weight: 400!important; font-family: Verdana, sans-serif !important; margin:4px 0; padding: 0 5%;">
                         Your personal information and data will be permanently deleted
                           from our systems on ' . date("jS F Y", strtotime($request['accountExpiry'])) . '.
                      </p>
                   </td>
                </tr>
                <tr>
                   <td class="column-one c-f">
                      <p class="d-red"  style="color:#BE1E2D; font-size:14px; font-weight: 400!important; font-family: Verdana, sans-serif !important; padding: 6px 0; border-top:1px solid #01104e; border-bottom: 1px solid #01104e; ">Note: If you login into your account before ' . date("jS F Y", strtotime($request['accountExpiry'])) . ',<br>Your account deletion request will be cancelled.</p>
                      </td>
                      </tr>
              
                <tr>
                   <td class="column-one">
                      <img style=" margin-top: 15px;" src="https://assets.nationaldrawuae.com/nationaldrawuae/1/EmailTemplateFooter.png" width="84%">
                   </td>
                </tr>
                <tr>
                   <td>
                      <p
                         style="color: #01104e !important;font-size: 11px !important;margin: 7px 0px !important;text-align: center !important;font-weight: 500 !important;font-family: Verdana, sans-serif !important;">
                         Note: This is a system auto-generated email. Please do not reply to this mail.
                      </p>
                   </td>
                </tr>
                <td class="column-one" style="background: #e6e6e6; height:15px;">
                </td>
                </tr>
             </table>
             <!-- End Main Class -->
          </center>
          <!-- End Wrapper -->
       </body>
    </html>';

    return $html;
  }



  public static function addCreditPurchase(array $request)
  {


    $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        
        <html xmlns="http://www.w3.org/1999/xhtml">
        
        <head>
         
         <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
         
         <meta http-equiv="X-UA-Compatible" content="IE=edge" />
         
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         
         <title>Ticket Purchase OTP Mail Template</title>
         
         <style type="text/css">
         
         
         @import url(https://fonts.googleapis.com/css2?family=Barlow+Condensed&display=swap);body{margin:0}.wrapper{background:#ccc}.main{background:#fff;max-width:600px}table{border-spacing:0}td{padding:3px}img{border:0}.column-one{text-align:center;margin:0 auto}.column-one .column{width:100%;margin:0 auto}
         
         
         </style>
         
         </head>
         
         <body>
         
         
           <center class="wrapper">
         
         
             <table class="main" width="100%">
         
                 <!-- BORDER -->
         
                 <tr><td class="column-one" style="background: #29377d; height:50px;">
                 
         
                 </td></tr>
         
                         <tr><td class="column-one" style="background: radial-gradient(circle,#fcef48 0%,#fdd206 100%); height:11px;">        
         
                 </td></tr>        
         
                 <tr><td class="column-one" >
         
                 <table class="column"> <tr>
                   <td valign="top" style="padding: 16px 0 0px 0;">  
         
                 <center>
         
                   <img src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/logo1.png" style="border: 0px;"  >
                 
                 </center>
         
                   </td></tr></table>
         
                 
         
                 </td></tr>
         
                 <!-- LOGO  -->
         
                         <tr>
         
                           <td class="column-one" >
         
                 <table align="center" class="column"> <tr><td valign="top" >  
         
          <div style="margin:0 auto;  max-width:500px; display:block;">
         
                  <div style="width:110px; float:left; margin: -39px 0 0 0;">      <img style="border: 0px;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/char21.png" ></div>
         
         
                  <div  style="">
         
         <h3 class="demoname"style="color: #29377d;  font-family: Arial Narrow;font-style: italic;font-size: 28px;text-align: center;font-weight: 600;"> Hi, ' . $request['name'] . '</h3>
                             <br>
                             <p style="color: #29377d;font-family: Arial Narrow;font-style: italic;font-size: 173%;margin: 0px 8px 13px 8px;text-align: center;font-weight: 600;">Thank you for your Order <br>
                              and the Points  are
                              <br>
                              Credited Your Wallet</p>
                             
         
                             </h3></div>
        
                 </div>
         
                   </td></tr></table>
               </td></tr>
        
         <tr>
         
                           <td class="column-one" >
         
                 <table align="center" class="column"> <tr>
         
                   <td valign="top" >  
         
        
                     <br>
         
                     
         
                     <table style="margin: auto;color: #000000;font-size: medium;background-color: #fbfbfb;border-collapse: collapse;/* width: 93%; */width: 95%;max-width: 500px;" border="0" cellspacing="0" cellpadding="0">
         
                      <tbody>
                
                        <tr>
                
                         <td style="color: #111111;padding: 15px 14px 23px;/* border-radius: 4px 4px 0px 0px; */font-size: 24px;line-height: 24px;border: 1px solid #29377d;/* width: 465px; */border-bottom: none;" align="center" valign="top" bgcolor="#ffffff">
                
                           <h3 style="color: #29377d;font-size: 30px;margin: 0px;/* font-style: italic; */font-family: Arial Narrow;">CREDITED POINTS DETAILS
               
                              
               
                             <br>
               
                           </h3>
               
                         </td>
                
                        </tr>
                
                      </tbody>
                
                    </table>
             <table style="margin: auto;border-collapse: collapse;border: 1px;width:95%;max-width:500px;" border="1" cellspacing="2" cellpadding="0">
         
              <tbody>
        
                <tr>
        
                  <td style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size: 24px;width: 49%;border-right: none!important;" align="center" bgcolor="#d0dbe7"><strong>Cash Points</strong></td>
        
                  <th style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size: 24px;width: 45%;border-left: none!important;" align="center" bgcolor="#d0dbe7"><strong>Aed ' . number_format($request['cash_point'], 2) . '</strong></th>
        
                  
        
                  
        
                </tr>
                <tr>
        
                  <td style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size: 26px;border-right: none!important;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 800;">Bonus Points</strong></td>
        
                  <td style="padding: 12px 5px;color: #354169;font-style: italic;font-family: Arial Narrow;font-size: 26px;border-left: none!important;" align="center" bgcolor="#d0dbe7"><strong style="font-weight: 800;">Aed ' . number_format($request['bonus_point'], 2) . '</strong></td>
        
                </tr>
                <tr>
        
                  <td style="padding: 12px 5px;color: #ffffff;font-style: italic;font-family: Arial Narrow;font-size: 26px;border-right: none;" align="center" bgcolor="#354169"><strong style="font-weight: 800;">Total Points</strong></td>
        
                  <td style="padding: 12px 5px;color: #ffffff;font-style: italic;font-family: Arial Narrow;font-size: 26px;border-left: none !important;" align="center" bgcolor="#354169"><strong style="font-weight: 800;">Aed ' . number_format($request['total'], 2) . '</strong></td>
        
                </tr>
        
              </tbody>
        
            </table>
             <br>
         
            <table style="margin: auto; color: #000000;  font-size: medium; background-color: #fbfbfb;  border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
        
              <tbody>
        
                
        
             
                <tr>
        
                  <td style="padding: 0px 0px 0px 30px;border-radius: 4px 4px 0px 0px;font-size: 24px;line-height: 24px;margin: 11px 0 0 0;" align="center" valign="top" bgcolor="#ffffff">
        
                    <h3 style="color: #ffffff;font-size: 26px;margin: 19px 0 0 0;padding: 8px 31px 10px 34px;background: #be1e2d;color: #e5462f;line-height: 1;border-radius: 5px;border: 1px solid;font-weight: 700;">
        
                      <a href="' . ($request->header('Origin') . '/') . 'play' . '" style="color: #ffffff;text-decoration-line: none;font-style: italic;font-family: Arial Narrow;font-weight: 600 !important;">CLICK HERE TO PARTICIPATE</a>
        
                    </h3>
        
                  </td>
        
                </tr>
        
              </tbody>
        
            </table>
         
             <table style="margin: auto; color: #000000;  font-size: medium; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
         
               <tbody>
         
                 <tr>
         
                   <td style="color: #666666; background: none; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-origin: initial; background-clip: initial; font-size: 15px; line-height: 25px;" align="center" bgcolor="#e4dcf1">
         
                    <p style="color: #29377d;font-size: 158%;text-align: center;font-style: italic;font-family: Arial Narrow;line-height:30px;font-weight: 700;">Watch Just3 ' . (isset($request['drawfreq']) && $request['drawfreq'] === 4 ? 'Daily Draw results <br> every Monday to Friday ' : 'Tri-Daily Draw results every (Monday,Wednesday,Friday) ')  . $request['resultDatetime'] . ' UAE Time ' . (isset($request['SuperresultDatetime']) ? ', Super Raffle Draw ' . date("dS F Y", strtotime($request['SuperresultDatetime'])) . ' ' : ' ')  . $request['raffleDrawDate'] . '</p>
         
                   </td>
               
                 </tr>
         
                
                   <tr>
         
                   <td class="gmail-line" style="box-sizing: border-box; width: 8px;padding: 0;">
         
                     <img  style="width:500px !important;" src="' . ($request->header('Origin') . '/') . 'assets/images/mailtemplate/final_img.png">
         
                   </td>
         
                 </tr>
         
               </tbody>
         
             </table> 
            
           
             
            <br>
            <p style="color: #29377d !important;font-size: 21px !important;margin: 0px !important;text-align: center !important;font-weight: 500 !important;font-style: italic !important;font-family: Arial Narrow !important;margin: 8px 0px 0px 0px !important;">Note: This is a system auto generated email. Please do not reply to this mail.<br>
              For Clarification
               
                     <br>
              Call 04 33 98880 Whatsapp +971 56 199 1271
              <br>
              or email support@nationaldrawuae.com</p>
         
                   </td></tr></table>
         
                 </td></tr>
         
             </table> 
         
         
         
           </center> 
         
         
         
         </body>
        
        </html>';
    return $html;
  }


  public static function contactus(array $request)
  {
    //   dd($email);

    $html = '<html>
<head>
<style>
table {
font-family: arial, sans-serif;
border-collapse: collapse;
width: 50%;
}

td, th {
border: 1px solid #dddddd;
text-align: left;
padding: 8px;
}

tr:nth-child(even) {
background-color: #dddddd;
}
</style>
</head>
<body>

<center><h2>GO RIDE</h2></center>

<table align="center">
<tr>
<th colspan="2" style="text-align:center;" ><strong>Customer Details</strong></th>


</tr>
<tr>
<td>Name</td>
<td> <span>' . $request['name'] . '</span></td>

</tr>
<tr>
<td>Email</td>
<td>  <span>' . $request['email'] . '</span></td>

</tr>
<tr>
<td>Phone </td>
<td>   <span>' . $request['mobile'] . '</span></td>

</tr>
<tr>
<td>Subject</td>
<td>  <span>' . $request['subject'] . '</span></td>

</tr>
<tr>
<td>Message</td>
<td> <span>' . $request['message'] . '</span></td>

</tr>

</table>

</body>
</html>';

    return $html;
  }
}
