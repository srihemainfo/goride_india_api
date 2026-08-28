@php
//dd($get_driver);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 0.7rem !important; 
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
            border-collapse: collapse;
        }
        .a {
            width: 49%;
            display: inline-block;
        }
    </style>
</head>
<body>
    <header style="border-bottom: 2px solid #000;">
        <div class="header-content">
            <h4 style="margin: 0;">{{ $get_job_details[0]['website'] }}</h4>
        </div>
        <div style="width: 100%;">
            <div class="a"><b>Phone :</b> {{ $partner_list[0]['phone'] }}</div>
            <div class="a" style="text-align: right;"><b>Email :</b> {{ $partner_list[0]['email'] }}</div>
        </div>
    </header>

    <footer style="font-weight: bold;">
        <p>{{ $get_job_details[0]['website'] }} &#169; {{ date('Y') }}</p>
    </footer>

    <main>
        <div style="width: 100%;">
            <div class="a"><b>Name :</b> {{ $get_driver[0]['name'] }}</div>
            <div class="a"><b>Email :</b> {{ $get_driver[0]['email'] }}</div>
            <div class="a"><b>Phone :</b> {{ $get_driver[0]['phone'] }}</div>
            <h3 style="text-align: center;">
                <b>Settlement Report ({{ date('d-m-Y', strtotime($get_transaction['fromdate'])) }} - {{ date('d-m-Y', strtotime($get_transaction['todate'])) }})</b>
            </h3>
        </div>

        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Job No</th>
                        <th style="width: 10%;">Pickup Date & Time</th>
                        <th style="width: 10%;">From</th>
                        <th style="width: 10%;">To</th>
                        <th style="width: 10%;">Car Type</th>
                        <th style="width: 10%;">Total Amount</th>
                        <th style="width: 10%;">Parking Charges</th>
                        <th style="width: 10%;">Commission</th>
                        <th style="width: 10%;">Final Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $get_job_detail = '';
                        foreach ($get_job_details as $jobs):
                            $driverAmount = $jobs['driver_amount'] ? $website_currency . number_format($jobs['driver_amount'], 2) : '-';
                            $carParkAmount = $jobs['car_park_amount'] ? $website_currency . number_format($jobs['car_park_amount'], 2) : '-';
                            $commissionProfit = $jobs['commision_profit'] ? $website_currency . number_format($jobs['commision_profit'], 2) : '-';
                            $driverFinal = $jobs['driver_final'] ? $website_currency . number_format($jobs['driver_final'], 2) : '-';

                            $get_job_detail .= "
                                <tr>
                                    <td style='text-align:left; padding-left:5px;'>". htmlspecialchars($jobs['job_no']) ."</td>
                                    <td style='text-align:left;'>". date('d-m-Y', strtotime($jobs['pickup_date'])) . " " . date('H:i', strtotime($jobs['pickup_time'])) ."</td>
                                    <td style='text-align:left; padding-left:5px;'>". htmlspecialchars($jobs['from']) ."</td>
                                    <td style='text-align:left; padding-right:5px;'>". htmlspecialchars($jobs['to']) ."</td>
                                    <td style='text-align:center; padding-right:5px;'>". htmlspecialchars($jobs['car_type']) ."</td>
                                    <td style='text-align:right; padding-right:5px;'>". $driverAmount ."</td>
                                    <td style='text-align:right; padding-right:5px;'>". $carParkAmount ."</td>
                                    <td style='text-align:right; padding-right:5px;'>". $commissionProfit ."</td>
                                    <td style='text-align:right; padding-right:5px;'>". $driverFinal ."</td>
                                </tr>";
                        endforeach;
                    @endphp
                    {!! $get_job_detail !!}
                </tbody>
            </table>

            <div class="main-amount" style="page-break-inside: avoid;">
                <table>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;">Total Jobs</th>
                        <td style="padding: 4px 3px; text-align:right;">{{ count($get_job_details) }}</td>
                        <th style="padding: 4px 3px; text-align:left;">Driver Amount</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ $get_bookings_details['driver_amount_total'] == "0" ? "-" : $website_currency . number_format($get_bookings_details['driver_amount_total'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;">Settlement For</th>
                        <td style="padding: 4px 3px; text-align:right;">{{ date('d-m-Y', strtotime($get_transaction['fromdate'])) }}</td>
                        <th style="padding: 4px 3px; text-align:left;">Commission Amount (-)</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ $get_bookings_details['commision_profit_total'] == "0" ? "-" : $website_currency . number_format($get_bookings_details['commision_profit_total'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;">Opening Balance</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ ($get_settle_history['old_balance'] == "0") ? "-" : $website_currency . number_format($get_settle_history['old_balance'], 2) }}
                        </td>
                        <th style="padding: 4px 3px; text-align:left;">Parking Charges</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ $get_bookings_details['car_park_amount_total'] == "0" ? "-" : $website_currency . number_format($get_bookings_details['car_park_amount_total'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;">Closing Balance</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ ($get_settle_history['current_balance'] == 0) ? "-" : $website_currency . number_format($get_settle_history['current_balance'], 2) }}
                        </td>
                        <th style="padding: 4px 3px; text-align:left;">Total Amount</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ $get_bookings_details['driver_final_total'] == "0" ? "-" : $website_currency . number_format($get_bookings_details['driver_final_total'], 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

