@if(isset($sid) && !empty($sid))



@php







//var_dump(session);die;



//dd(session('check'));

$check = session('check');



//dd($db_check[0]->db_key);



//dd($sid);







$data = \App\Models\Booking::where('id', $sid)->first();



//dd($data);







//$currentConnection = DB::connection('mysql1')->table('partnerlists')->where('db_key' , $check->db_key)->first();



//dd($currentConnection);







//dd($data);



@endphp



@endif



<!DOCTYPE html>

<html lang=en style>

<meta charset=utf-8>

<meta name=viewport content="width=device-width, initial-scale=1.0">

<title>

    Go Ride CRM Booking Preview

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"

    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&family=Great+Vibes&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<meta name=referrer content=no-referrer>

<link rel="shortcut icon" type=image/x-icon href=data:,>

</head>



<style>



    body {

        font-family: "Poppins", sans-serif;

        font-size: 12px;

    }



    a {

        color: black;

    }



    .bp-section .container {

        padding: 0 10rem;

    }



    h3, h6 {

        font-weight: 700;

        margin: 20px 0;

    }



    .bottom-line {

        border-bottom: 1px solid #f8c41b;

        padding-bottom: 5px;

    }

    

    .table>:not(caption)>*>* {

        padding: .3rem .3rem;

    }

    

    h3, h6 {

        font-weight: 700;

        margin: 10px 0; 

    }

    

    @media screen and (max-width: 576px) {

        

        .bp-section .container {

            padding: 0 1.5rem;

        }

        

    }

    

    @media print {

        * {

            -webkit-print-color-adjust: exact !important;

            print-color-adjust: exact !important;

        }



        @page {

            margin: 0;

        }

        

        body {

            margin: 1cm;

        }

    }

    

    .btn.btn-outline-primary {

        position: absolute;

        right: 10px;

        top: 10px;

    }



</style>



<body>

    

    <div class="site-wrapper">

        <div class="main-overlay"></div>

        <section class="bp-section " >

            <div class="container position-relative">

                <button class="btn btn-outline-primary" id="print-btn">

                    Print 

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">

                      <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>

                      <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>

                    </svg>

                </button>

            </div>

            <div class="container" style="padding: 0;" id="main-content">

                <div class="text-center my-3 mt-1 bottom-line mv-div">

                    

                    <h3 style="color:#f8c41b;" id="web_name"></h3>

                    <h6 style="color:#000; margin-bottom:15px;">

                        Email: <a href="" id="partner_company"></a>

                        Phone: <a href="" id="partner_phone"></a>

                    </h6>

                    <h6 style="color:#000; margin:0;">

                        Thank you for using our services to

                        complete your journey. Please check your journey details.

                    </h6>

                </div>

                

                <div style="display: flex;">

                    

                    <table class="table table-bordered me-2" style="color:#000;">

                        <tbody>

                            <tr>

                                <th colspan="2"

                                    style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                    Booking Information</th>

                            </tr>

                            <tr>

                                <td style="width:50%;">Booking ID</td>

                                <td id="job_number"></td>

                            </tr>

                            <tr>

                                <td>Booked On</td>

                                <td id="Booked_On"></td>

                            </tr>

                            <tr>

                                <td>Booking Status</td>

                                <td id="Booking_Status"></td>

                            </tr>

                        </tbody>

                    </table>

    

                    <table class="table table-bordered" style="color:#000;">

                        <tbody>

                            <tr>

                                <th colspan="2"

                                    style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                    Your Route</th>

                            </tr>

                            <tr>

                                <td style="width:50%;">Journey Type</td>

                                <td id="Booked_Journey_Type"></td>

                            </tr>

                            <tr>

                                <td>Date of Journey</td>

                                <td id="one_way_pickup_date"></td>

                            </tr>

                            <!--<tr>-->

                            <!--    <td>Pick Up Time</td>-->

                            <!--    <td></td>-->

                            <!--</tr>-->

                            <tr>

                                <td>Preferred Vehicle</td>

                                <td id="Booked_OPreferred_Vehicle"></td>

                            </tr>

                            <!--<tr>-->

                            <!--    <td>Message</td>-->

                            <!--    <td></td>-->

                            <!--</tr>-->

                        </tbody>

                    </table>

                </div>





                <table class="table table-bordered mt-3" style="color:#000;">

                    <tbody>

                        <tr>

                            <th colspan="2"

                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                Passengers & Luggage</th>

                        </tr>

                        <tr>

                            <td colspan='2' align='center' style="width:50%;" id="Booked_Passengers_On"></td>

                            <!--<td id="Booked_Passengers_On">0</td>-->

                        </tr>

                        <!--<tr>-->

                        <!--    <td>Baby seat</td>-->

                        <!--    <td id="Booked_Booster_Seat_On">0</td>-->

                        <!--</tr>-->

                        <!--<tr>-->

                        <!--    <td>Luggage</td>-->

                        <!--    <td id="Booked_Luggages_On">0</td>-->

                        <!--</tr>-->

                        <!--<tr>-->

                        <!--    <td>Hand Luggage</td>-->

                        <!--    <td id="Hand_Luggages">0</td>-->

                        <!--</tr>-->

                    </tbody>

                </table>



                <table class="table table-bordered mt-3" style="color:#000;">

                    <tbody>

                        <tr>

                            <th colspan="2"

                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                Your Details</th>

                        </tr>

                        <tr>

                            <td style="width:50%;">Your Name</td>

                            <td id="client_name"></td>

                        </tr>

                        <tr>

                            <td>Contact Number</td>

                            <td id="Booked_Contact_Number"></td>

                        </tr>

                        <tr>

                            <td>E-mail Address</td>

                            <td id="Booked_E_mail_Address"</td>

                        </tr>

                    </tbody>

                </table>



                <table class="table table-bordered mt-3" style="color:#000;">

                    <tbody>

                        <tr>

                            <th colspan="2"

                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                Journey Details</th>

                        </tr>

                        <tr>

                            <td style="width:50%;">From</td>

                            <td id="Booked_From"></td>

                        </tr>

                        <tr>

                            <td style="width:50%;">Flight Number</td>

                            <td id="Flight_Number_Booked"></td>

                        </tr>

                        <tr>

                            <td style="width:50%;">Fight Arriving From</td>

                            <td id="Fight_Arriving_From_Booked"></td>

                        </tr>

                        <tr>

                            <td style="width:50%;">Ship Name</td>

                            <td id="Ship_Number_Booked"></td>

                        </tr>

                        <tr>

                            <td style="width:50%;">Ship From</td>

                            <td id="Ship_Arriving_From_Booked"></td>

                        </tr>

                        <tr>

                            <td>Pickup Address</td>

                            <td id="Booked_Pickup_Address"></td>

                        </tr>

                        <tr>

                            <td>Pickup Postcode</td>

                            <td id="Booked_Pickup_Postcode"></td>

                        </tr>

                        <tr>

                            <td>To</td>

                            <td id="My_Booked_to"></td>

                        </tr>

                        

                    </tbody>

                </table>



                <table class="table table-bordered mt-3" style="color:#000;">

                    <tbody>

                        <tr>

                            <th colspan="2"

                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                Payment Details</th>

                        </tr>

                        <tr>

                            <td colspan="2" align='center' style="width:50%;" id="Payment_Status_of_Booked"></td>

                        </tr>

                        <tr>

                            <td colspan="2" align='center' style="width:50%;" id="client_message"></td>

                        </tr>

                        <tr>

                            <td colspan="1" align='start' style="width:50%;" id="specialoffer_show"></td>
                            <td colspan="1" align='start' style="width:50%;" id="specialoffer_show_date"></td>

                        </tr>


                        <!--<tr>-->

                        <!--    <td>Journey Cost</td>-->

                        <!--    <td ><span name="symbol" id="Currency_Symbol"></span> <span name="symbol" id="Payment_On"></span></td>-->

                        <!--</tr>-->

                        <!--<tr>-->

                        <!--    <td>Payment</td>-->

                        <!--    <td id="Journey_Cost"></td>-->

                        <!--</tr>-->

                        <!--<tr>-->

                        <!--    <td>Message</td>-->

                        <!--    <td id="Message_no_pr"></td>-->

                        <!--</tr>-->

                    </tbody>

                </table>

                

                <table class="table table-bordered mt-3" style="color:#000;" id="driver_details">

                    <tbody>

                        <tr>

                            <th colspan="2"

                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">

                                Driver Details</th>

                        </tr>

                        <tr>

                            <td style="width:50%;">Driver Name</td>

                            <td id="driver_name"></td>

                        </tr>

                        <tr>

                            <td>Driver Image</td>

                            <td id="driver_img"></td>

                        </tr>

                        <tr>

                            <td>Driver Phone</td>

                            <td id="driver_phone"></td>

                        </tr>

                        <tr>

                            <td>Vehicle No</td>

                            <td id="vehicle_no"></td>

                        </tr>

                    </tbody>

                </table>

                

                <!--<table class="table table-bordered mt-3" style="color:#000;">-->

                <!--    <tbody>-->

                <!--        <tr>-->

                <!--            <th colspan="2"-->

                <!--                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">-->

                <!--                Client Message</th>-->

                <!--        </tr>-->

                <!--        <tr>-->

                <!--            <td colspan="2" align='center' style="width:50%;" id="client_message">-->

                <!--                Despite the overwhelming odds stacked against her, Amelia, armed with nothing but an unshakable determination, a weathered notebook filled with half-baked ideas, and an old, sputtering typewriter that clacked with every keystroke, embarked on a journey through the sleepy coastal town where the sea whispered secrets to those who dared to listen,-->

                <!--            </td>-->

                <!--        </tr>-->

                <!--    </tbody>-->

                <!--</table>-->



                <!--<div class="col-lg-12 mt-4 col-sm-12 mv-div text-center">-->

                <!--    <p style="color:#000;" id="foot_text">-->

                <!--        If you have difficulty finding a driver, please call us on .-->

                <!--    </p>-->

                <!--    <p style="">-->

                <!--        Hope to see you again in the future. Have a nice journey.-->

                <!--    </p>-->

                    <!--<p style="color:#000; font-size:large;">Best Regards</p>-->

                <!--    <p style="color:#000;" id="foot_c_name"></p>-->

                <!--</div>-->

            </div>

        </section>

    </div>



</body>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    

    <script>

    

    $(document).ready(function () {

        $('#print-btn').click(function () {

            var printContent = $('#main-content').html();

            var originalContent = $('body').html();

            

            $('body').html(printContent);

            window.print();

            location.reload();

        });

    });



        var currentUrl = window.location.href;

        

        var token = currentUrl.split("booking/Preview/")[1];

        

        // var bookIdMatch = currentUrl.match(/\/(\d+)(?:\/|\?|$)/);

        

        // var bookId = bookIdMatch ? bookIdMatch[1] : null;



        // var dToken  = currentUrl.match(/d_token=([^&]+)/)[1]??window.location.host;

        

        // var dTokenMatch = currentUrl.match(/d_token=([^&]+)/);

        

        // var url = dTokenMatch ? 'previewbooking' : 'previewBooking';

        var url = 'previewBooking';

        

        // var dToken = dTokenMatch ? dTokenMatch[1] : window.location.host;







            var formDataObject = {};



            formDataObject['token'] = token;



            formDataObject['device_id'] = 0;



            // formDataObject['book_id'] = bookId;



           



            var settings = {



                "url": "{{env('API_URL')}}"+url,



                "method": "POST",



                "timeout": 0,



                "headers": {



                    "Content-Type": "application/json"



                },



                "data": JSON.stringify(formDataObject),



            };





            $.ajax(settings).done(function(response) {



                if (response['status'] == 200) {

                    

                    if (response['partner']) {

                        $('#partner_company').text(response['partner']['email']);

                        $('#partner_company').attr('href', 'mailto:' + response['partner']['email']);

                        // $('#foot_c_name').text(response['partner']['company_name']);

                        $('#partner_phone').text(response['partner']['phone']);

                        $('#partner_phone').attr('href', 'tel:' + response['partner']['phone']);

                    

                        // $('#partner_domain').text(response['partner']['domain_name']);

                        

                        $('#foot_text').text('If you have difficulty finding a driver, please call us on ' + response['partner']['phone'] + '.');

                    

                        let domain = response['partner']['domain_name'];

                        let url = domain.startsWith('http') ? domain : 'http://' + domain;

                    

                        // $('#partner_domain').attr('href', url);

                    }



                    var userName = response['booking_details']['fname'] || 'NA';



                    document.getElementById('client_name').innerText = userName;

                    

                    var cweburl = response['booking_details']['cweburl'] != '' ? response['booking_details']['cweburl'] : (response['booking_details']['website'] ?? 'N/A');



                    document.getElementById('web_name').innerText = response['partner']['company_name'] + ' Booking Information';



                    var useremail = response['booking_details']['email'] || 'NA';



                    document.getElementById('Booked_E_mail_Address').innerText = useremail;

                    

                    var car_type = response['booking_details']['car_type'] || 'NA';



                    document.getElementById('Booked_OPreferred_Vehicle').innerText = car_type;



                    var userfrom = response['booking_details']['from'] || 'NA';



                    document.getElementById('Booked_From').innerText = userfrom;



                    var userNameto = response['booking_details']['to'] || 'NA';



                    document.getElementById('My_Booked_to').innerText = userNameto;



                    var Booked_Type = response['booking_details']['way'] || 'NA';
                    
                    if(Booked_Type == 'tariff_oneway'){
                        Booked_Type = 'Tariff OneWay';
                    }else if(Booked_Type == 'roundtrip'){
                        Booked_Type = 'RoundTrip';
                    }



                    document.getElementById('Booked_Journey_Type').innerText = Booked_Type;



                    var amount_total = Math.round(response['booking_details']['total'] || 0);

                    var special_time = response['booking_details']['additional_cost_time'] ? response['booking_details']['additional_cost_time'] : '0';
                    var special_date = response['booking_details']['additional_cost_date'] ? response['booking_details']['additional_cost_date'] : '0';

                    var total_special_date_time = Math.round(Number(special_time) + Number(special_date));

                    // console.log('jana',total_special_date_time);

                    // document.getElementById('Payment_On').innerText = amount_total;



                    var pickupDate = response['booking_details']['pickup_date'];

                    var pickupTime = response['booking_details']['pickup_time'] || 'NA';



                    var user_pickup_date = pickupDate + ' ' + pickupTime;

                    var formattedDate = new Date(pickupDate);

                    var options = { day: 'numeric', month: 'short', year: 'numeric' };

                    var formattedDateString = formattedDate.toLocaleDateString('en-GB', options).toUpperCase();



                    document.getElementById('one_way_pickup_date').innerHTML = formattedDateString + ' &nbsp;&nbsp;&nbsp;' + pickupTime;





                    // var pickup_flight_num = response['booking_details']['pickup_flight_num'] || 'NA';



                    // document.getElementById('one_way_Flight_Landing_Time').innerText = pickup_flight_num;



                    var user_job_number = response['booking_details']['job_no'] || 'NA';



                    document.getElementById('job_number').innerText = user_job_number;



                    



                    var user_order_status = response['booking_details']['order_status'] || 'NA';



                    document.getElementById('Booking_Status').innerText = user_order_status;

                    

                    

                    if(user_order_status == 'Dispatched' || user_order_status == 'Moving'){

                        

                        $('#driver_details').show();

                        

                        $('#driver_name').text(`${response['booking_details']['driver_name']}`);

                        

                        $('#driver_phone').text(`${response['booking_details']['driver_phone']}`);

                        

                        $('#vehicle_no').text(`${response['booking_details']['vech_reg_num']??'NA'}`);

                        $('#driver_img').html(
    response['booking_details']['upload_photo']
        ? `<img src="../../${response['booking_details']['upload_photo']}" width="100px" height="100px">`
        : `<img src="{{ asset('dashboard-assets/assets/images/cars-fleets/driverimg.png') }}" width="100px" height="100px">`
);

                        // $('#driver_img').html(

                        //     `<img src="../../${response['booking_details']['upload_photo'] ?? 'car1.png'}" width="100px" height="100px">`

                        // );
                        // $('#driver_img').html(

                        //     `<img src="{{ asset('dashboard-assets/assets/images/cars-fleets/driverimg.png') }}" width="100px" height="100px">`

                        // );

                    }else{

                        $('#driver_details').hide();

                    }



                    

                //  ----------------booked on---------------

                    var user_booking_date = response['booking_details']['booking_date'] || 'NA';



                    

                    if (user_booking_date && user_booking_date.includes('00:00:00')) {

                  user_booking_date = user_booking_date.split(' ')[0];  

                 }



                    if (user_booking_date !== 'NA') {

                    var date = new Date(user_booking_date);

                    var options = { year: 'numeric', month: 'short', day: 'numeric' };

                    user_booking_date = date.toLocaleDateString('en-GB', options); 



                   }



                   user_booking_date = user_booking_date.toUpperCase();

                    document.getElementById('Booked_On').innerText = user_booking_date;





                //  --------------------------------------------------------------

                    



                    var payment_status = response['booking_details']['payment_status'] || 'NA';



                    // document.getElementById('Payment_Status_of_Booked').innerText = payment_status;

                    

                   



                    // console.log(response['booking_details'], 'hiiiiiiiiiiiii');

                    



                    var user_passengers = response['booking_details']['passengers'] || 'NA';

                    var user_baggagess = response['booking_details']['baggages'] || 'NA';

                    var user_hand_luggages = response['booking_details']['hand_luggages'] || 'NA';

                    var child_seat = response['booking_details']['child_seat'] || 'NA';

                    var firstbaby = response['booking_details']['firstbaby'] || '-';

                    var secondbaby = response['booking_details']['secondbaby'] || '-';

                    var thirdbaby = response['booking_details']['thirdbaby'] || '-';

                    

                    let seats = '';

                    

                    if (child_seat > 0) {

                        let babies = [firstbaby, secondbaby, thirdbaby].slice(0, child_seat);

                        seats = `( ${babies.join(' , ')} )`;

                    }

                    

                    const nbsp = '\u00A0';



                    // Construct the string with non-breaking spaces around slashes

                    const displayText = 'Passengers: ' + user_passengers +

                        nbsp + nbsp + nbsp + '/' + nbsp + nbsp + nbsp +

                        ' Baby seat: ' + child_seat + ' ' +seats +

                        nbsp + nbsp + nbsp + '/' + nbsp + nbsp + nbsp +

                        ' Luggage: ' + user_baggagess +

                        nbsp + nbsp + nbsp + '/' + nbsp + nbsp + nbsp +

                        ' Hand Luggage: ' + user_hand_luggages;

                    

                    document.getElementById('Booked_Passengers_On').innerText = displayText;

                    // document.getElementById('Booked_Luggages_On').innerText = user_baggagess;

                    // document.getElementById('Booked_Passengers_On').innerText = 'Passengers : '+ user_passengers + ' / Baby seat: '+ user_booster + ' &nbsp; / Luggage : ' + user_baggagess + ' / Hand Luggage: '+ user_hand_luggages;



                    





                    

                    // document.getElementById('Hand_Luggages').innerText = user_hand_luggages;

                    // document.getElementById('Booked_Booster_Seat_On').innerText = user_booster;



                    







                    







                    



                    var user_mobile = response['booking_details']['mobile'] || 'NA';



                    document.getElementById('Booked_Contact_Number').innerText = user_mobile;



                    



                    var user_message = response['booking_details']['message'] || 'NA';



                    // document.getElementById('Message_no_pr').innerText = user_message;



                    



                    var amount_type = response['booking_details']['type'] || 'NA';

                    amount_type = amount_type.replace(/_/g, ' ').toUpperCase()

                    // document.getElementById('Journey_Cost').innerText = amount_type;



                    



                    var user_pickup_address = response['booking_details']['pickup_address'] || 'NA';



                    document.getElementById('Booked_Pickup_Address').innerText = user_pickup_address;



                    



                    var user_pickup_postcode = response['booking_details']['pickup_postcode'] || 'NA';



                    document.getElementById('Booked_Pickup_Postcode').innerText = user_pickup_postcode;







                    var currency_code = response['booking_details']['currency_symbol'] || 'NA';
                    // console.log('currency',currency_code);

                    var currencySymbols = {
                        "INR": "₹",
                        "USD": "$",
                        "GBP": "£",
                        "EUR": "€",
                        "JPY": "¥",
                        "AUD": "A$",
                        "CAD": "C$",
                        "CHF": "Fr",
                        "CNY": "¥",
                        "RUB": "₽"
                    };

                    var currency_symbol = currencySymbols[currency_code] || currency_code;



                    // document.getElementById('Currency_Symbol').innerText = currency_symbol;



                    



                     if (response['booking_details']['order_status'] == 'Confirmed') {



                        // document.getElementById('Message_no_pr').innerText = response['booking_details']['message'] || 'NA';



                        // document.querySelector('#Message_no_pr').closest('tr').style.display = 'table-row';



                    } else {



                        // document.querySelector('#Message_no_pr').closest('tr').style.display = 'none';



                    }

                    

                    // const nbsp = '\u00A0';

                    // Construct the string with non-breaking spaces around slashes

                    const disText = 'Payment Status : ' + payment_status +

                        nbsp + nbsp + nbsp + '/ ' + nbsp + nbsp + nbsp +

                        '<b>Journey Cost : ' + currency_symbol + amount_total + nbsp + '(Special Offers:' + currency_symbol + total_special_date_time + ')</b>' + nbsp + nbsp + nbsp + ' / ' + nbsp + nbsp + nbsp +

                        'Payment : ' + amount_type;

                    

                    // Set the constructed string to the element's innerText

                    document.getElementById('Payment_Status_of_Booked').innerHTML = disText;

                    document.getElementById('client_message').innerText = 'Message : ' + user_message;

                    document.getElementById('specialoffer_show').innerText = 'Special Offer Time : ' + currency_symbol + special_time;

                    document.getElementById('specialoffer_show_date').innerText = 'Special Offer Date : ' + currency_symbol + special_date;



                    

                    // document.getElementById('Payment_Status_of_Booked').innerText = payment_status + ' / ' + currency_symbol + ' / ' + amount_type + ' / ' + user_message;

                    

                    var userfrom = response['booking_details']['from'] || 'NA';



                   



                    var ee= userfrom.toLowerCase();



                    // alert(ee);



                    var userfrom = response['booking_details']['from'] || 'NA';



                    if (userfrom && userfrom.toLowerCase().includes('airport')) {



                        // alert('hi');



                        document.getElementById('Flight_Number_Booked').innerText = response['booking_details']['pickup_flight_num'] || 'NA';



                        document.getElementById('Fight_Arriving_From_Booked').innerText = response['booking_details']['pickup_flight_from'] || 'NA';



                        document.querySelector('#Flight_Number_Booked').closest('tr').style.display = 'table-row';



                        document.querySelector('#Fight_Arriving_From_Booked').closest('tr').style.display = 'table-row';



                        document.querySelector('#Booked_Pickup_Address').closest('tr').style.display = 'none';



                        document.querySelector('#Booked_Pickup_Postcode').closest('tr').style.display = 'none';

                        

                        document.querySelector('#Ship_Number_Booked').closest('tr').style.display = 'none';



                        document.querySelector('#Ship_Arriving_From_Booked').closest('tr').style.display = 'none';



                    }else if (userfrom && userfrom.toLowerCase().includes('port') && !userfrom.toLowerCase().includes('airport')){

                        

                        document.getElementById('Ship_Number_Booked').innerText = response['booking_details']['pick_shipname'] || 'NA';



                        document.getElementById('Ship_Arriving_From_Booked').innerText = response['booking_details']['pick_shipfrom'] || 'NA';



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







        



        // console.log("Book ID:", bookId);



        // console.log("d_token:", dToken);











    



    



    </script>





</html>