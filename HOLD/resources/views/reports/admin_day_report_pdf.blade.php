@php
    //dd($job_details, $summary_details);
    $partner_list=$summary_details->partner_list;
    $job_detail = '';
        
    foreach ($job_details->job_details as $job) {
        // Now you can access properties as they are objects
        $driver_name = $job->driver_name;
        $job_no = $job->job_no;
        $pickup_date = $job->pickup_date;
        $pickup_time = $job->pickup_time;
        $pickup_location = $job->pickup_location;
        $dropoff_location = $job->dropoff_location;
        $car_type = $job->car_type;
        $total = $job->total;
        $order_status = $job->order_status;

        // Build the table row
        $job_detail .= "
            <tr>
                <td style='text-align: left; font-size:11px;'>" . strtoupper($driver_name) . "</td>
                <td>" . $job_no . "</td>
                <td>" . date('d-m-Y', strtotime($pickup_date)) . ' (' . substr($pickup_time, 0, 5) . ")</td>
                <td style='text-align: left; font-size:11px;'>" . $pickup_location . "</td>
                <td style='text-align: left; font-size:11px;'>" . $dropoff_location . "</td>
                <td style='text-align: center; font-size:11px;'>" . $car_type . "</td>
                <td style='text-align: right;'>" . $total . "</td>
                <td style='text-align: right;'>" . $order_status . "</td>
            </tr>";
    }
@endphp


<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <style>
       @page {
            margin: 10.1em .75em 1.75em;
           
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
            margin-bottom: 40px;
            width: 100%;
            position: fixed;
            top: -115px;
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
            text-align: center;
        }

        .main-amount table {
            margin-top: 1em;
            width: 100%;
            text-align: center !important;
        }
        .main-amount table,
        th,
        td{
              text-align: center !important;
        }

        .container table {
            width: 100%;
            text-align: center;
        }

        .container table,
        th,
        td {
            font-size: 12px;
            border-collapse: collapse;
        }
    </style>
</head>
@php

//dd($summary_details->summary_details[0]->total_raids);
//$summaryDetails = $summary_details->summary_details;
$totalRaids = $summary_details->summary_details[0]->total_raids;
@endphp
<body>
    <style>
   
   body{
        border:1px solid gray !important;
        
   }
  

        .header-content img {
            width: 40%;
            max-width: 200px; /* Set a max-width for the logo */
        }

        h3 {
            font-size: 14px;
            text-align: center;
        }

        footer {
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .container {
            margin: 20px;
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 15px;
            text-align: left;
            font-size: 14px;
        }

        th {
            font-weight: bold;
            background:#f7f7f7;
        }

        .main-amount {
            page-break-inside: avoid;
        }

        /* Add responsive styles if needed */
        @media screen and (max-width: 600px) {
            .header-content img {
                width: 80%; /* Adjust the logo width for smaller screens */
            }
        }
    </style>
    <header style=''>
        <div class='header-content'>
          <!--@if(!empty($partner_list) && isset($partner_list[0]->company_logo) && !empty($partner_list[0]->company_logo))-->
          <!--      <img src="{{ $partner_list[0]->company_logo }}" style='width: 10%'>-->
          <!--  @else-->
          <!--      <img src="{{ base_path() }}/public/logo.png" style='width: 10%'>-->
          <!--  @endif-->
            <h3 style='font-size: 14px; text-align:center;'>{{ $job_details->job_details[0]->website }}</h3>
        </div>
        <h3 style='font-size: 14px; text-align:center;'>Admin Daily Report</h3>
    </header>

    <footer style='font-weight: bold;'>
        <p> {{ $job_details->job_details[0]->website }} &#169; {{ date('Y') }} </p>
    </footer>

    <main>
        <div class='container'>
            <table>
                <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th>Job No.</th>
                        <th>Pickup Date/Time</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Car Type</th>
                        <th>Total Amount</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    {!! $job_detail !!}
                </tbody>
            </table>

            <div class='main-amount' style='page-break-inside: avoid;'>
                <table>
                    <tr>
                        <th style='padding: 4px 0px;'> Date </th>
                        <th style='padding: 4px 0px;'> Number of Jobs </th>
                    </tr>
                    <tr>
                        <td style='padding: 4px 0px;'>{{ date('d-m-Y') }}</td>
                        <td style='padding: 4px 0px;'>{{ $totalRaids }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
