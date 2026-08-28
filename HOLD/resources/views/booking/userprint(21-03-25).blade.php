@if(isset($sid) && !empty($sid))

@php



//var_dump(session);die;

//dd(session);

//dd($db_check[0]->db_key);

//dd($sid);



$data = \App\Models\Booking::where('id', $sid)->first();

//dd($data);



$currentConnection = DB::connection()->getName();

//dd($currentConnection);



//dd($data);

@endphp

@endif















<!doctype html>

<html lang="en-US">



<head>

    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

    <title>Go Ride - CRM Admin Dashboard</title>

    <meta name="description" content="airportrides">

    <!--<link href="https://airportrides.co/assets/images/fav-d.png" sizes="128x128" rel="shortcut icon" type="image/x-icon">-->

</head>

<style>

    /* @import url('https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@1,700&display=swap'); */



    h1,

    table {

        letter-spacing: 1px;

        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";

        font-size: 20px;

    }



    td {

        border: none !important;

    }



    a:hover {

        text-decoration: underline !important;

    }
     td span{
        color: #787774;
        font-weight: 700
     }

  

</style>



<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">

    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="font-family: 'Open Sans', sans-serif;">



        <tr>

            <td>

                <table style="background-color: white; max-width:900px; margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
{{-- 
                    <tr>

                        <td style="height:60px;">&nbsp;</td>

                    </tr> --}}

                    <!-- Logo -->

                    <tr>

                        <td style="text-align:center;">

                            <!--<a href="https://airportrides.co/" title="airport rides" target="_blank">-->

                                <!--<img width="60" src="https://i.ibb.co/hL4XZp2/android-chrome-192x192.png" title="logo" alt="logo">-->

                                <!--<div class="mobile_menu_main_logo"><img class="nav_logo_img img-fluid" src="{{ asset('assets/images/logo-black-new.png') }}" alt="images/header-logo2.png"></div>-->

                            </a>

                        </td>

                    </tr>
{{-- 
                    <tr>

                        <td style="height:20px;">&nbsp;</td>

                    </tr> --}}

                    <!-- Email Content -->

                    <tr>

                        <td>

                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:823px; border: 2px solid black; background-color: white !important;">

                                {{-- <tr>

                                    <td>&nbsp;</td>

                                </tr> --}}

                                <!-- Title -->

                                <tr>

                                    <td style="text-align:center;">
                                         
                                        <h4 style="font-weight:1000; font-size:28px;" id="web_name">Goride.Run</h4>

                                        <h1 style="color:#f3ba00; font-weight:1000; font-size:21px;">Booking Information</h1>

                                        <span style="display:inline-block; vertical-align:middle; border-bottom:1px solid black; 

                                        width:150px;"></span>

                                    </td>

                                </tr>

                                <!-- Details Table -->

                                <tr>

                                    <td>

                                        <table cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid #ededed">

                                            <tbody>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Booking ID :</td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="job_number" id="job_number"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Booking Status : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booking_Status" id="Booking_Status"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Booked On : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_On" id="Booked_On"></span>

                                                    </td>

                                                </tr>

                                                <!--your route-->

                                                <tr>

                                                    <th scope="col" colspan="2" style="background: rgb(51, 51, 51);color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Your Route</th>

                                                </tr>

                                                <!---->

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Journey Type : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_Journey_Type" id="Booked_Journey_Type"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Date of Journey : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span type="text" id="one_way_pickup_date" name="one_way_pickup_date"></span>

                                                    </td>

                                                </tr>

                                                <!--<tr>-->

                                                <!--    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">-->

                                                <!--        Flight Number : </td>-->

                                                <!--    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">-->

                                                <!--        <span name="one_way_Flight_Landing_Time" id="one_way_Flight_Landing_Time"></span>-->

                                                <!--    </td>-->

                                                <!--</tr>-->

                                                <tr>

                                                    <td style="padding: 10px; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Preferred Vehicle : </td>

                                                    <td style="padding: 10px; color: #fff;">
                                                        
                                                        <span name="Booked_OPreferred_Vehicle" id="Booked_OPreferred_Vehicle"></span>    
                                                        
                                                     </td>

                                                    

                                                </tr>



                                                <!--passenger and lagguage-->

                                                <tr>

                                                    <th scope="col" colspan="2" style="background: rgb(51, 51, 51);color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Passengers & Luggage</th>

                                                </tr>

                                                <!---->

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Booster Seat : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_Booster_Seat_On" id="Booked_Booster_Seat_On"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Passengers : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_Passengers_On" id="Booked_Passengers_On"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Luggages : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Booked_Luggages_On" id="Booked_Luggages_On"></span>

                                                    </td>

                                                </tr>

                                                

                                                

                                                 <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Hand Luggages : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Hand_Luggages" id="Hand_Luggages"></span>

                                                    </td>

                                                </tr>



                                                <!--Your Details-->

                                                <tr>

                                                    <th scope="col" colspan="2" style="background:  rgb(51, 51, 51);color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Passengers Details</th>

                                                </tr>

                                                <!---->

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Your Name : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color:  #787774;  font-weight: 700">

                                                    <samp id="client_name" name="client_name" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">
                                                    </samp>
                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Contact Number : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_Contact_Number" id="Booked_Contact_Number"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        E-mail Address : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Booked_E_mail_Address" id="Booked_E_mail_Address"></span>

                                                    </td>

                                                </tr>



                                                <!--Journey Details-->

                                                <tr>

                                                    <th scope="col" colspan="2" style="background:  rgb(51, 51, 51);color: white;font-size: 20px;font-weight: 600;     padding: 3px 0;">Journey Details </th>

                                                </tr>

                                                <!---->

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        From : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_From" id="Booked_From"></span>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td

                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        		Flight Number : </td>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Flight_Number_Booked" id="Flight_Number_Booked"></span>

                                                         </td>	

                                                </tr>

                                                <tr>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                       		Fight Arriving From : </td>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Fight_Arriving_From_Booked" id="Fight_Arriving_From_Booked"></span>

                                                        	 </td>

                                                </tr>
                                                
                                                
                                                <tr>

                                                    <td

                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        		Ship Name : </td>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Ship_Number_Booked" id="Ship_Number_Booked"></span>

                                                         </td>	

                                                </tr>

                                                <tr>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                       		Ship From : </td>

                                                    <td

                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Ship_Arriving_From_Booked" id="Ship_Arriving_From_Booked"></span>

                                                        	 </td>

                                                </tr>



                                                <tr>

                                                    <td style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Pickup Address : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Booked_Pickup_Address" id="Booked_Pickup_Address"></span>

                                                    </td>

                                                </tr>

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Pickup Postcode : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Booked_Pickup_Postcode" id="Booked_Pickup_Postcode"></span>

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        To : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="My_Booked_to" id="My_Booked_to"></span>

                                                    </td>

                                                </tr>



                                                <!--Payment Details-->

                                                <tr>

                                                    <th scope="col" colspan="2" style="background:  rgb(51, 51, 51);color: white;font-size: 20px;font-weight: 600;     padding: 3px 0;">Payment Details </th>

                                                </tr>

                                                <!---->

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                        Payment Status : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                        <span name="Payment_Status_of_Booked" id="Payment_Status_of_Booked"></span>

                                                    </td>

                                                </tr>

                                                

                                                

                                                 <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color: black">

                                                       Journey Cost : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">

                                                      <span name="symbol" id="Currency_Symbol"></span>

                                                      <span name="Payment_On" id="Payment_On"></span>

                                                    </td>

                                                </tr>

                                                

                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Payment Type : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Journey_Cost" id="Journey_Cost"></span>

                                                        

                                                    </td>

                                                </tr>



                                                <tr>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color: black">

                                                        Message : </td>

                                                    <td style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">

                                                        <span name="Message_no_pr" id="Message_no_pr"></span>

                                                    </td>

                                                </tr>





                                            </tbody>

                                        </table>

                                    </td>

                                </tr>

                                <tr>

                                    <td style="height:40px;">&nbsp;</td>

                                </tr>



                            </table>

                        </td>

                    </tr>

                    <tr>

                        <td style="height:20px;">&nbsp;</td>

                    </tr>

                    <tr>

                        <td style="text-align:center;">

                            <!--<p style="font-size:14px; color:#455056bd; line-height:18px; margin:0 0 0;">&copy; <strong></strong></p>-->

                        </td>

                    </tr>

                    <tr>

                        <!--<td style="height:60px;">&nbsp;</td>-->

                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    

    <script>

        var currentUrl = window.location.href;

        

        var bookId = currentUrl.match(/\/(\d+)\?/)[1];

        var dToken  = currentUrl.match(/d_token=([^&]+)/)[1];

       

            var formDataObject = {};

            formDataObject['token'] = dToken;

            formDataObject['device_id'] = 0;

            formDataObject['book_id'] = bookId;

           

            var settings = {

                "url": "{{env('API_URL')}}previewbooking",

                "method": "POST",

                "timeout": 0,

                "headers": {

                    "Content-Type": "application/json"

                },

                "data": JSON.stringify(formDataObject),

            };

            // alert('data');

            $.ajax(settings).done(function(response) {

                if (response['status'] == 200) {

                    var userName = response['booking_details']['fname'] || 'NIL';

                    document.getElementById('client_name').innerText = userName;
                    
                    var cweburl = response['booking_details']['cweburl'] != '' ? response['booking_details']['cweburl'] : (response['booking_details']['website'] ?? 'N/A');

                    document.getElementById('web_name').innerText = cweburl;

                    var useremail = response['booking_details']['email'] || 'NIL';

                    document.getElementById('Booked_E_mail_Address').innerText = useremail;
                    
                    var car_type = response['booking_details']['car_type'] || 'NIL';

                    document.getElementById('Booked_OPreferred_Vehicle').innerText = car_type;

                    var userfrom = response['booking_details']['from'] || 'NIL';

                    document.getElementById('Booked_From').innerText = userfrom;

                    var userNameto = response['booking_details']['to'] || 'NIL';

                    document.getElementById('My_Booked_to').innerText = userNameto;

                    var Booked_Type = response['booking_details']['way'] || 'NIL';

                    document.getElementById('Booked_Journey_Type').innerText = Booked_Type;

                    var amount_total = response['booking_details']['total'] || 'NIL';

                    document.getElementById('Payment_On').innerText = amount_total;

                    var pickupDate = response['booking_details']['pickup_date'];
                    var pickupTime = response['booking_details']['pickup_time'] || 'NIL';

                    var user_pickup_date = pickupDate + ' ' + pickupTime;
                    var formattedDate = new Date(pickupDate);
                    var options = { day: 'numeric', month: 'short', year: 'numeric' };
                    var formattedDateString = formattedDate.toLocaleDateString('en-GB', options).toUpperCase();

                    document.getElementById('one_way_pickup_date').innerHTML = formattedDateString + ' &nbsp;&nbsp;&nbsp;' + pickupTime;


                    // var pickup_flight_num = response['booking_details']['pickup_flight_num'] || 'NIL';

                    // document.getElementById('one_way_Flight_Landing_Time').innerText = pickup_flight_num;

                    var user_job_number = response['booking_details']['job_no'] || 'NIL';

                    document.getElementById('job_number').innerText = user_job_number;

                    

                    var user_order_status = response['booking_details']['order_status'] || 'NIL';

                    document.getElementById('Booking_Status').innerText = user_order_status;

                    
                //  ----------------booked on---------------
                    var user_booking_date = response['booking_details']['booking_date'] || 'NIL';

                    
                    if (user_booking_date && user_booking_date.includes('00:00:00')) {
                  user_booking_date = user_booking_date.split(' ')[0];  
                 }

                    if (user_booking_date !== 'NIL') {
                    var date = new Date(user_booking_date);
                    var options = { year: 'numeric', month: 'short', day: 'numeric' };
                    user_booking_date = date.toLocaleDateString('en-GB', options); 

                   }

                   user_booking_date = user_booking_date.toUpperCase();
                    document.getElementById('Booked_On').innerText = user_booking_date;


                //  --------------------------------------------------------------
                    

                    var payment_status = response['booking_details']['payment_status'] || 'NIL';

                    document.getElementById('Payment_Status_of_Booked').innerText = payment_status;

                    

                    var user_passengers = response['booking_details']['passengers'] || 'NIL';

                    document.getElementById('Booked_Passengers_On').innerText = user_passengers;

                    

                    var user_baggagess = response['booking_details']['baggages'] || 'NIL';

                    document.getElementById('Booked_Luggages_On').innerText = user_baggagess;

                    

                    var user_hand_luggages = response['booking_details']['hand_luggages'] || 'NIL';

                    document.getElementById('Hand_Luggages').innerText = user_hand_luggages;

                    

                    var user_booster = response['booking_details']['booster'] || 'NIL';

                    document.getElementById('Booked_Booster_Seat_On').innerText = user_booster;

                    

                    var user_mobile = response['booking_details']['mobile'] || 'NIL';

                    document.getElementById('Booked_Contact_Number').innerText = user_mobile;

                    

                    var user_message = response['booking_details']['message'] || 'NIL';

                    document.getElementById('Message_no_pr').innerText = user_message;

                    

                    var amount_type = response['booking_details']['type'] || 'NIL';
                    amount_type = amount_type.replace(/_/g, ' ').toUpperCase()
                    document.getElementById('Journey_Cost').innerText = amount_type;

                    

                    var user_pickup_address = response['booking_details']['pickup_address'] || 'NIL';

                    document.getElementById('Booked_Pickup_Address').innerText = user_pickup_address;

                    

                    var user_pickup_postcode = response['booking_details']['pickup_postcode'] || 'NIL';

                    document.getElementById('Booked_Pickup_Postcode').innerText = user_pickup_postcode;



                    var currency_symbol = response['booking_details']['currency_symbol'] || 'NIL';

                    document.getElementById('Currency_Symbol').innerText = currency_symbol;

                    

                     if (response['booking_details']['order_status'] == 'Confirmed') {

                        document.getElementById('Message_no_pr').innerText = response['booking_details']['message'] || 'NIL';

                        document.querySelector('#Message_no_pr').closest('tr').style.display = 'table-row';

                    } else {

                        document.querySelector('#Message_no_pr').closest('tr').style.display = 'none';

                    }

                    

                    var userfrom = response['booking_details']['from'] || 'NIL';

                   

                    var ee= userfrom.toLowerCase();

                  //  alert(ee);

                    var userfrom = response['booking_details']['from'] || 'NIL';

                    if (userfrom && userfrom.toLowerCase().includes('airport')) {

                       // alert('hi');

                        document.getElementById('Flight_Number_Booked').innerText = response['booking_details']['pickup_flight_num'] || 'NIL';

                        document.getElementById('Fight_Arriving_From_Booked').innerText = response['booking_details']['pickup_flight_from'] || 'NIL';

                        document.querySelector('#Flight_Number_Booked').closest('tr').style.display = 'table-row';

                        document.querySelector('#Fight_Arriving_From_Booked').closest('tr').style.display = 'table-row';

                        document.querySelector('#Booked_Pickup_Address').closest('tr').style.display = 'none';

                        document.querySelector('#Booked_Pickup_Postcode').closest('tr').style.display = 'none';
                        
                        document.querySelector('#Ship_Number_Booked').closest('tr').style.display = 'none';

                        document.querySelector('#Ship_Arriving_From_Booked').closest('tr').style.display = 'none';

                    }else if (userfrom && userfrom.toLowerCase().includes('port') && !userfrom.toLowerCase().includes('airport')){
                        
                        document.getElementById('Ship_Number_Booked').innerText = response['booking_details']['pick_shipname'] || 'NIL';

                        document.getElementById('Ship_Arriving_From_Booked').innerText = response['booking_details']['pick_shipfrom'] || 'NIL';

                        document.querySelector('#Ship_Number_Booked').closest('tr').style.display = 'table-row';

                        document.querySelector('#Ship_Arriving_From_Booked').closest('tr').style.display = 'table-row';

                        document.querySelector('#Booked_Pickup_Address').closest('tr').style.display = 'none';

                        document.querySelector('#Booked_Pickup_Postcode').closest('tr').style.display = 'none';
                        
                        document.querySelector('#Flight_Number_Booked').closest('tr').style.display = 'none';

                        document.querySelector('#Fight_Arriving_From_Booked').closest('tr').style.display = 'none';
                    }else {

                        document.querySelector('#Flight_Number_Booked').closest('tr').style.display = 'none';

                        document.querySelector('#Fight_Arriving_From_Booked').closest('tr').style.display = 'none';
                        
                        document.querySelector('#Ship_Number_Booked').closest('tr').style.display = 'none';

                        document.querySelector('#Ship_Arriving_From_Booked').closest('tr').style.display = 'none';

                    }

                    

                }

                if (response['status'] == 400) {

                    warningClick('Error', response['error'], "danger")

                }

                if (response['status'] == 500) {

                    warningClick('Error', response['error'], "danger")

                }

                if (response['status'] == 401) {

                    unauth()

                }

            }).fail(function(jqXHR, textStatus, errorThrown) {

                alert('AJAX request failed');

            });



        

        console.log("Book ID:", bookId);

        console.log("d_token:", dToken);





    

    

    </script>

</body>



<!--<tbody>-->



<!--    <tr>-->

<!--        <td style="text-align:center;">-->

            <!--<p style="font-size:14px; color:#455056bd; line-height:18px; margin:0 0 0;">&copy; <strong></strong></p>-->

<!--        </td>-->

<!--    </tr>-->

<!--    <tr>-->

<!--        <td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;-->

<!--			padding-top: 25px;" class="social-icons">-->

<!--            <table width="256" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse: collapse; border-spacing: 0; padding: 0;">-->

<!--                <tbody>-->

<!--                    <tr>-->



                        <!-- ICON 1 -->

<!--                        <td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://www.facebook.com/profile.php?id=61551495456254" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;-->

<!--					color: #000000;" alt="F" title="Facebook" width="44" height="44" src="{{ asset('assets/images/mail-icons/facebook.png') }}"></a></td>-->



                        <!-- ICON 2 -->

<!--                        <td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://twitter.com/AirportridesCo" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;-->

<!--					color: #000000;" alt="T" title="Twitter" width="44" height="44" src="{{ asset('assets/images/mail-icons/twitter.png') }}"></a></td>-->



                        <!-- ICON 3 -->

<!--                        <td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;-->

<!--					color: #000000;" alt="G" title="Instagram" width="44" height="44" src="{{ asset('assets/images/mail-icons/instagram.png') }}"></a></td>-->



                        <!-- ICON 4 -->

<!--                        <td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;-->

<!--					color: #000000;" alt="I" title="Contact" width="44" height="44" src="{{ asset('assets/images/mail-icons/call.png') }}"></a></td>-->



<!--                        <td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://airportrides.co/" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;-->

<!--					color: #000000;" alt="I" title="Website" width="44" height="44" src="{{ asset('assets/images/mail-icons/mail.png') }}"></a></td>-->



<!--                    </tr>-->

<!--                    <tr>-->

<!--                        <td style="height:60px;">&nbsp;</td>-->

<!--                    </tr>-->

<!--                </tbody>-->

<!--            </table>-->

<!--        </td>-->

<!--    </tr>-->

    

    

<!--</tbody>-->



</html>

