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
    .button-oneNew {

        display: inline-block;

        transition: all 0.2s ease-in;

        position: relative;

        overflow: hidden;

        z-index: 1;

        color: #090909;

        padding: 0.7em 1.7em;

        cursor: pointer;

        font-size: 18px;

        text-decoration: none;

        border-radius: 0.5em;

        background: #e8e8e8;

        border: 1px solid #e8e8e8;

        box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff;

    }



    .button-oneNew:active {

        color: #666;

        box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;

    }



    .button-oneNew:before {

        content: "";

        position: absolute;

        left: 50%;

        transform: translateX(-50%) scaleY(1) scaleX(1.25);

        top: 100%;

        width: 140%;

        height: 180%;

        background-color: rgba(0, 0, 0, 0.05);

        border-radius: 50%;

        display: block;

        transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);

        z-index: -1;

    }



    .button-oneNew:after {

        content: "";

        position: absolute;

        left: 55%;

        transform: translateX(-50%) scaleY(1) scaleX(1.45);

        top: 180%;

        width: 160%;

        height: 190%;

        background-color: #009087;

        border-radius: 50%;

        display: block;

        transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);

        z-index: -1;

    }



    .button-oneNew:hover {

        color: #ffffff;

        border: 1px solid #009087;

    }



    .button-oneNew:hover:before {

        top: -35%;

        background-color: #009087;

        transform: translateX(-50%) scaleY(1.3) scaleX(0.8);

    }



    .button-oneNew:hover:after {

        top: -45%;

        background-color: #009087;

        transform: translateX(-50%) scaleY(1.3) scaleX(0.8);

    }



    .booking-preview-btn {

        display: inline-block;

        transition: all 0.2s ease-in;

        position: relative;

        overflow: hidden;

        z-index: 1;

        color: #090909;

        padding: 0.7em 1.7em;

        font-size: 18px;

        text-decoration: none;

        border-radius: 0.5em;

        background: transparent;

        border: 3px solid #e8e8e8;

        cursor: default !important;

    }
</style>



<body >

    <section class="bp-section">

    <div class="container">

        <div class="text-center my-3 mt-5 bottom-line mv-div">

            <h3 style="color:#f8c41b"> Booking Information</h3>

            <h6 style="color:#000; margin-bottom:0;">

                Email: <a href="mailto:"></a>

                Cell: <a href=""></a>

                Phone: <a href="tel:"></a>

            </h6>

            <h6 style="color:#000; margin:0" id="web_name">

            </h6>

        </div>



        <table class="table table-bordered" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Booking Information</th>

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

        </table>



        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Your Route</th>

            </tr>

            <tr>

                <td style="width:50%">Journey Type</td>

                <td id="Booked_Journey_Type"></td>

            </tr>







            <tr>

                <td>Date of Journey</td>

                <td id="one_way_pickup_date"> </td>

            </tr>

            <tr>

                <td>Pick Up Time</td>

                <td></td>

            </tr>





            <tr>

                <td>Preferred Vehicle</td>

                <td id="Booked_OPreferred_Vehicle"></td>

            </tr>

            <tr>

                <td>Message</td>

                <td></td>

            </tr>

        </table>







        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Passengers & Luggage</th>

            </tr>

            <tr>

                <td style="width:50%">Passengers</td>

                <td id="Booked_Passengers_On"></td>

            </tr>

            <tr>

                <td>Baby seat</td>

                <td id-"Booked_Booster_Seat_On"></td>

            </tr>

            <tr>

                <td>Luggage</td>

                <td id="Booked_Luggages_On"></td>

            </tr>

            <tr>

                <td>Hand Luggage</td>

                <td id="Hand_Luggages"> </td>

            </tr>

        </table>







        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Your Details</th>

            </tr>

            <tr>

                <td style="width:50%">Your Name</td>

                <td id="client_name"></td>

            </tr>

            <tr>

                <td>Contact Number</td>

                <td id="Booked_Contact_Number"></td>

            </tr>

            <tr>

                <td>E-mail Address</td>

                <td id="Booked_E_mail_Address"></td>

            </tr>

        </table>







        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Journey Details</th>

            </tr>

            <tr>

                <td style="width:50%">From</td>

                <td id="Booked_Pickup_Address"></td>

            </tr>

    
            <tr>

                <td>Via Point:</td>

                <td></td>

            </tr>


            <tr>

                <td>Return Via Point:</td>

                <td></td>

            </tr>

            <tr>

                <td>Flight Number</td>

                <td></td>

            </tr>

            <tr>

                <td>Flight From</td>

                <td></td>

            </tr>

            <tr>

                <td>Meet and Greet Service</td>

                <td></td>

            </tr>

            <tr>

                <td>Ship Name</td>

                <td></td>

            </tr>

            <tr>

                <td>Ship From</td>

                <td></td>

            </tr>

            <tr>

                <td>Meet and Greet Service</td>

                <td></td>

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

                <td></td>

            </tr>

            <tr>

                <td>Dropoff Address</td>

                <td></td>

            </tr>

            <tr>

                <td>Dropoff Postcode</td>

                <td></td>

            </tr>

        </table>

        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Payment Details</th>

            </tr>

            <tr>

                <td style="width:50%">Payment Status</td>

                <td id="Payment_Status_of_Booked"></td>

            </tr>

            <tr>

                <td>Time Charges Message</td>

                <td></td>

            </tr>

            <tr>

                <td>Date Charges Message</td>

                <td></td>

            </tr>

            <tr>

                <td>Journey Cost</td>

                <td id="Journey_Cost"></td>

            </tr>

            <tr>

                <td>Payment</td>

                <td id="Payment_On" ></td>

            </tr>

        </table>



        <div class="col-lg-12 mt-4 col-sm-12 mv-div">

            <p style="color:#000;text-align:center;font-size:large">

                If you have a difficulty to find a driver please call us on

            </p>

            <p style="text-align:center;font-size:large">

                Hope to see you again in your future. Have a nice journey.

            </p>

            <p style="color:#000;text-align:center;font-size:large">Best Regards</p>

            <p style="color:#000;text-align:center;font-size:large"></p>

        </div>

    </div>

</section>

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


    </script>
</body>

</html>

