@php
    // Initialize the job detail string
    $partner_list=$summary_details->partner_list;
   // dd($partner_list[0]->company_logo);
    $job_detail = '';
    //dd($job_details);
    // Loop through the job details
    foreach ($job_details->job_details as $job):
        // Use object notation directly as job_details is an array of objects
        $driverName = isset($job->driver_name) ? ucwords(strtolower($job->driver_name)) : 'N/A';
        $totalRaids = isset($job->total_raids) ? $job->total_raids : 0;
        $parkingCharge = isset($job->parking_charge) ? $job->parking_charge : '0.00';
        $profitDeduct = isset($job->profit_deduct) ? $job->profit_deduct : '0.00';
        $totalCommission = isset($job->total_commission) ? $job->total_commission : '0.00';
        $totalFinalAmount = isset($job->total_final_amount) ? $job->total_final_amount : '0.00';
        $driverAmount = isset($job->driver_amount) ? $job->driver_amount : '0.00';
        $totalAmount = isset($job->total_amount) ? $job->total_amount : '0.00';

        // Build the table row
        $job_detail .= "
            <tr>
                <td style='text-align:left; font-size: 15px;'>$driverName</td>
                <td style='font-size: 15px;'>$totalRaids</td>
                <td style='text-align:right; font-size: 15px;'>$parkingCharge</td>
                <td style='text-align:right; font-size: 15px;'>$profitDeduct</td>
                <td style='text-align:right; font-size: 15px;'>$totalCommission</td>
                <td style='text-align:right; font-size: 15px;'>$totalFinalAmount</td>
                <td style='text-align:right; font-size: 15px;'>$driverAmount</td>
                <td style='text-align:right; font-size: 15px;'>$totalAmount</td>
            </tr>";
    endforeach;
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

        .header-border {
            border-border: 1px solid #000;
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
            text-align: center;
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

//dd($summary_details);
$summaryDetails = $summary_details->summary_details;
$totalRaids =$summaryDetails[0]->total_raids;
$profit_deduct =$summaryDetails[0]->profit_deduct;
$parking_charges =$summaryDetails[0]->parking_charges;
$total_commission =$summaryDetails[0]->total_commission;
$total_final_amount =$summaryDetails[0]->total_final_amount;
$driver_amount = $summaryDetails[0]->driver_amount;
$total_amount =$summaryDetails[0]->total_amount;
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
</head>
<body>
    <header>
        <div class='header-content'>
            <!--@if(!empty($partner_list) && isset($partner_list[0]->company_logo) && !empty($partner_list[0]->company_logo))-->
            <!--    <img src="{{ $partner_list[0]->company_logo }}" style='width: 10%'>-->
            <!--@else-->
            <!--    <img src="{{ base_path() }}/public/logo.png" style='width: 10%'>-->
            <!--@endif-->
            <h3>
                {{ $job_details->job_details[0]->website }}
            </h3>
        </div>
        <h3>
            Admin {{ $report_type }} Settlement Report Summary ({{ date('d-m-Y', strtotime($from)) }} to {{ date('d-m-Y', strtotime($to)) }})
        </h3>
    </header>

    <main>
        <div class='container'>
            <table>
                <thead>
                    <tr>
                        <th>Driver Name</th>
                        <th>Jobs</th>
                        <th>Parking Charge</th>
                        <th>Deductions</th>
                        <th>Commission</th>
                        <th>Final Amount</th>
                        <th>Driver Amount</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    {!! $job_detail !!}
                </tbody>
            </table>

            <div class='main-amount'>
                <table>
                    <tr>
                        <th>Total Jobs</th>
                        <td>{{ $totalRaids }}</td>
                        <th>Profit Deduction</th>
                        <td>{{ $profit_deduct }}</td>
                    </tr>
                    <tr>
                        <th>Parking Charges</th>
                        <td>{{ $parking_charges }}</td>
                        <th>Commission Amount</th>
                        <td>{{ $total_commission }}</td>
                    </tr>
                    <tr>
                        <th>Final Amount (Company Paid)</th>
                        <td>{{ $total_final_amount }}</td>
                        <th>Driver Amount</th>
                        <td>{{ $driver_amount }}</td>
                    </tr>
                    <tr>
                        <td colspan='2'></td>
                        <th>Total Amount</th>
                        <td>{{ $total_amount }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </main>

    <footer>
        <p>{{ $job_details->job_details[0]->website }} &copy; {{ date('Y') }} </p>
    </footer>

</body>

</html>
