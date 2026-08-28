<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <style>
            @font-face {
                font-family: 'DejaVu Sans';
                font-style: normal;
                font-weight: normal;
                src: url("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/fonts/dejavu/DejaVuSans.ttf") format("truetype");
            }
    
            body {
                font-family: 'DejaVu Sans', sans-serif;
                font-size: 0.8rem;
            }
            @page {
                margin: 5.1em .75em 1.75em;
            }
            footer {
                width: 100%;
                text-align: center;
                position: fixed;
                bottom: -40px;
            }
            footer p {
                border-top: 1px solid black;
            }
            header {
                width: 100%;
                position: fixed;
                top: -75px;
            }
            .header-border {
                border-border: 3px solid #000;
            }
            .header-content {
                display: flex;
                justify-content: center;
                text-align: center;
            }
            .header-content table {
                width: 100%;
                vertical-align: middle;
            }
            .header-content table,
            th,
            td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            main{
                margin-top: 4em;
                width: 100%;
            }
            .container table {
                width: 100%;
            }
            .container table,
            th,
            td {
                font-size: 12px;
                border-collapse: collapse;
            }
            .a {
                width: 49%;
                display: inline-block;
            }
            .extra-details{
                margin-top: 1em;
                width: 100%;
            }
        </style>
        
    </head>
    <body>
        <header style='border-bottom: 2px solid #000;'>
            <div class='header-content'>
                <img src='{{ base_path() }}/public/logo.png' style='width: 25%'>
            </div>
            <div style="width: 100%;">
                <p style="text-align: center;">
                    <strong>Address:</strong> No: 1A, 2nd Floor, Paper Mills Road, Perambur, Chennai, Tamil Nadu 600082
                    <strong>Phone:</strong> +91 98845 57004, <br>
                    <strong>Website:</strong> www.goride.run,
                    <strong>Email:</strong> support@goride.run.
                </p>
            </div>
        </header>

        <footer style='font-weight: bold;'>
            <p>Go Ride. &#169; {{ date('Y') }} </p>
        </footer>

        <main>
            <div class='container'>
                <div class="main-details" style="page-break-inside: avoid;">
                    <h3 style="text-align: center; font-size: 20px; margin-bottom:-12px;">
                        <b><u> Booking Status </u></b>
                    </h3>
                    <div style="width: 100%">
                        <div style="display:inline-block;width: 31%;vertical-align: top;">
                            <h4><u>Booking Information</u></h4>
                            <p>
                                <strong>Ref No: </strong>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $mergedData['booking_details']['job_no'] }}
                            </p>
                            <p>
                                <strong>Status: </strong>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                {{ $mergedData['booking_details']['order_status'] }}
                            </p>
                            <p>
                                <strong>Pick-up date: </strong>
                                {{ date('d-m-Y', strtotime($mergedData['booking_details']['pickup_date'])) }}
                            </p>
                            <p>
                                <strong>Pick-up Time: </strong>
                                {{ date('H:i', strtotime($mergedData['booking_details']['pickup_time'])) }}
                            </p>
                            </div>
                            <div style="display:inline-block;width: 40%;vertical-align: top;">
                                <h4><u>Traveller Information</u></h4>
                                <p>
                                    <strong>Passenger: </strong>
                                    {{ $mergedData['booking_details']['fname'] }}
                                </p>
                                <p>
                                    <strong>Email: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['email'] }}
                                </p>
                                <p><strong>Phone: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['mobile'] }}
                                </p>
                            </div>
                            <div style="display:inline-block;width: 27%;vertical-align: top;">
                                <h4><u>Carrier Details</u></h4>
                                <p>
                                    <strong>Passengers: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['passengers'] }}
                                </p>
                                <p>
                                    <strong>Check-in Luggage: </strong>
                                    {{ $mergedData['booking_details']['baggages'] }}
                                </p>
                                <p>
                                    <strong>Hand Luggage: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['hand_luggages'] }}
                                </p>
                                <p>
                                    <strong>Child Seat: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['child_seat'] }}
                                </p>
                                <p>
                                    <strong>Vehicle type: </strong>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ $mergedData['booking_details']['car_type'] }}
                                </p>
                            </div>
                            </div>
                            <hr/>
                            <div style="width: 100%;">
                                <div style="display:inline-block;width: 49%;vertical-align: top;">
                                    <h4><u>Pick-up Information</u></h4>
                                    <p>
                                        <strong>From: </strong>
                                        {{ $mergedData['booking_details']['from'] }}
                                    </p>
                                </div>
                                <div style="display:inline-block;width: 49%;vertical-align: top;">
                                    <h4><u>Drop-off Information</u></h4>
                                    <p>
                                        <strong>To: </strong>
                                        {{ $mergedData['booking_details']['to'] }}
                                    </p>
                                </div>
                            </div>
                            <hr/>
                            <div style="width: 100%;">
                                <div style="display:inline-block;width: 100%;vertical-align: top;">
                                    <h4><u>Fare Details</u></h4>
                                    <p>
                                        <strong>Price override: </strong>
                                            {{$mergedData['booking_details']['currency_symbol']}}
                                            {{ $mergedData['booking_details']['total'] }}
                                    </p>
                                    <p>
                                        <strong>Grand Total: </strong>
                                        &nbsp;&nbsp;
                                            {{$mergedData['booking_details']['currency_symbol']}}
                                            {{ $mergedData['booking_details']['total'] }}
                                    </p>
                                    <p>
                                        <strong>Payment Method: </strong>
                                        &nbsp;&nbsp;
                                        {{ !empty($mergedData['booking_details']['payment_message']) ? $mergedData['booking_details']['payment_message'] : '' }}
                                    </p>

                        </div>
                    </div>
                </div>

                {{--<div class="extra-details" style="page-break-inside: avoid;">
                    <div style="width: 100%;height:70px;"></div>
                    <div style="width: 100%;">
                        <p style="text-align: center;"><strong style="color:green;"><u>Thank you and have a pleasant journey !</u></strong> <br/><br/>
                            Could you please check whether the details are correct and let us know if you have any changes to the journey.
                            Orders are subject to our terms & conditions. We welcome all comments on the services we provide.
                        </p>
                    </div>
                    <hr/>
                    <div style="width: 100%;">
                        <h4><u>Contact us</u></h4>
                        <ul>
                            <li>If you can not locate your driver, Please Call us immediately at 0208 611 2965 (To dial from outside the UK is 0044 208
                                611 2965) or FREE Number 0800 612 8914 'UK'.
                            </li>
                            <li>EC Minibus is not responsible for lost or damaged luggage or any other items left in the vehicle during the time of service.
                                Please check the vehicle before Exiting.
                            </li>
                            <li>Items left in the vehicle may require extra charges to be returned.
                                Please note that the prices do not include gratuity and are up to the client's discretion to tip the Driver, preference in
                                cash, or any currency.
                            </li>
                        </ul>
                    </div>
                    <hr/>
                    <div style="width: 100%;">
                        <h4><u>Pickup Instructions</u></h4>
                        <ul>
                            <li><strong>Airport Pickup,</strong> The driver will monitor the flight, only go into the terminal 45 minutes after the plane lands, and will meet
                                you with your name on the Board Sign at the arrivals point, located immediately after the customs exit. (Please do not
                                leave the airport terminal we are not allowed to pick up from outside of the terminal).
                            </li>
                            <li><strong>Cruise Terminal Pickup,</strong> The driver will meet you with your name on a Board Sign outside or inside the Cruise terminal at the
                                specified pickup time. (Please do not wait for your driver at the vehicle pickup and drop-off area).
                            </li>
                            <li><strong>Hotel Pickup,</strong> Please wait at the hotel lobby for collection and just let the concierge or reception desk that you are waiting for a
                                private transfer our driver will aim to arrive 10 minutes early at the hotel and make contact with the concierge or reception
                                desk.
                            </li>
                            <li><strong>Private Address,</strong> The driver will make contact by ringing the doorbell and will be waiting as close as possible to the front door
                                at the set pickup time.
                            </li>
                            <li><strong>Meet & Greet Service,</strong> includes 90 minutes of free waiting time from the flight arrival time, an additional charge will
                                apply £8 for every 10 minutes plus any additional car park charges. The train station and cruise terminals are
                                allowed a free waiting time of 30 minutes from the booking time afterward £8 for every 10 minutes plus any
                                additional car park charges. Payable to the driver in cash at the end of the service.
                            </li>
                            <li><strong>Our standard cancellation policy,</strong> To make a cancellation for a booking up to 12 hours before the journey pickup
                                time no refund, 24 hours before the journey would be a 50% refund, and 48 hours before the journey 100%
                                refund. For the 16-55 Seater bus, we require a minimum of 14 days' notice before the date for a 100% refund,
                                10 days before the journey would be a 50% refund, and 7 days before the journey pick-up day no refund.
                            </li>
                        </ul>
                    </div>
                </div>--}}
            </div>
        </main>
    </body>
</html>
