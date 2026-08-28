@php
   // dd($pickup_points);
@endphp


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Driver Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        .prompt-content{
            height:660px;
           overflow: auto;
        }
    </style>
</head>
<body>

</body>
</html>

<img src="{{ asset('logo.png') }}" class="rounded mx-auto d-block" alt="logo">
<div class="col-sm-6 main-card mb-2 card mx-auto prompt-content">
    <!--<div class="card-header">-->
    <!--    <h5 class="card-title">Driver Name: {{ $driver_details->name }}</h5>-->
    <!--</div>-->
    <!--<div class="card-body">-->
    <!--    <div class="col-sm-12">-->
    <!--        <img src="{{ asset($driver_details->photo) }}" class="rounded mx-auto d-block img-fluid" alt="profile image">-->
    <!--    </div>-->
    <!--</div>-->
    
    
    
    <div class="bootprompt-body">
        <div class="prompt">
            <br>
        <p>Dear, {{ $driver_details->fname }}.<br><br>
        <strong>The Vehicle Details</strong>
        <br>Reg No: {{ $driver_details->fname }}
        <br>V Model: {{ $driver_details->model }}
        <br>V Make: {{ $driver_details->make }}
        <br>V Color: {{ $driver_details->vech_color }}
        <br>V Type: {{ $vehicle[0]->name }}
        <br>No. L: {{ $vehicle[0]->luggage }}
        <br>No. HL: {{ $vehicle[0]->hand_luggage }}
        <br>No Seat: {{ $vehicle[0]->no_of_seats }}
        <br>Child Seat: {{ $vehicle[0]->child }}
        <br>
        <br><strong>The Driver Details</strong>
        <br><img style="width: 150px; height=175px; margin: 0px 5px 0px 0px;" src="https://ecminibus.info{{ $driver_details->photo }}"> <br>
        <br>Driver Name: {{ $driver_details->name }}
        <br>Driver PCO License No: {{ $driver_details->vech_licence_no }}
        <br>Driver Phone No: {{ $driver_details->phone }}
        <br>Pickup Date: {{ $driver_details->pickup_date }}
        <br>Pickup Time: {{ $driver_details->pickup_time }}
        <br>
        <br><strong>The Job Details</strong>
        <br>V Type: {{ $vehicle[0]->name }}
        <br>Driver Name: {{ $driver_details->name }}
        <br>Pickup Date: {{ $driver_details->pickup_date }}
        <br>Pickup Time: {{ $driver_details->pickup_time }}
        <br>Pickup From: {{ $driver_details->from }}
        <br>Via Points: @foreach($pickup_points as $pick_points)
                       {{ $pick_points->location_name }} | 
                       @endforeach
        <br>Drop To: {{ $driver_details->to }}
        </p>
        <h4>Contact us</h4>
        <p>1. If you can not locate your driver, Please Call us immediately at 0208 611 2965 (To dial from outside the UK is 0044 208 611 2965) or FREE Number 0800 612 8914 "UK".<br>2. EC Minibus is not responsible for lost or damaged luggage or any other items left in the vehicle during the time of service. Please check the vehicle before Exiting. Items left in the vehicle may require extra charges to be returned. <br>3. Please note that the prices do not include gratuity and are up to the client's discretion to tip the Driver, preference in cash, or any currency. </p>
        <h4>Pickup Instruction</h4>
        <ul>
        <li><strong>Airport Pickup,</strong> The driver will monitor the flight, only go into the terminal 45 minutes after the plane lands, and will meet you with your name on the Board Sign at the arrivals point, located immediately after the customs exit. (Please do not leave the airport terminal we are not allowed to pick up from outside of the terminal)</li>
        <li><strong>Cruise Terminal Pickup,</strong> The driver will meet you with your name on a Board Sign outside or inside the Cruise terminal at the specified pickup time. (Please do not wait for your driver at the vehicle pickup and drop-off area)</li>
        <li><strong>Hotel Pickup,</strong> Please wait at the hotel lobby for collection and just let the concierge or reception desk that you are waiting for a private transfer our driver will aim to arrive 10 minutes early at the hotel and make contact with the concierge or reception desk.</li>
        <li><strong>Private Address,</strong> The driver will make contact by ringing the doorbell and will be waiting as close as possible to the front door at the set pickup time.</li>
        <li><strong>Meet &amp; Greet Service,</strong> Includes 90 minutes of free waiting time from the flight arrival time, an additional charge will apply £8 for every 10 minutes plus any additional car park charges. The train station and cruise terminals are allowed a free waiting time of 30 minutes from the booking time afterward £8 for every 10 minutes plus any additional car park charges. Payable to the driver in cash at the end of the service.</li>
        <li><strong>Our standard cancellation policy,</strong> To make a cancellation for a booking up to 12 hours before the journey pickup time no refund, 24 hours before the journey would be a 50% refund, and 48 hours before the journey 100% refund. For the 16-55 Seater bus, we require a minimum of 14 days notice before the date for a 100% refund, 10 days before the journey would be a 50% refund, and 7 days before the journey pick-up day no refund.</li>
        </ul>
        <p>Best Regards
        <br><strong>EC Minibus</strong> 
        <br>International House, 6 South Molton St, London, W1K 5QF, 
        <br>Phone: UK 0044 208 611 2965 
        <br>info@ecminibus.co.uk 
        <br>www.ecminibus.co.uk</p>
        </div>
        </div>
    
  </div>


