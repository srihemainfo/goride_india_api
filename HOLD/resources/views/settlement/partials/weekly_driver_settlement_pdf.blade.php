@php
    use Illuminate\Support\Str;


    $advance_amt = 0;
    //dd($get_advance);
    $get_avg_advance = $get_advance ? number_format($get_advance['average_amount'], 2) : 0.00;
    $get_total_advance = $get_advance ? number_format($get_advance['total_amount'], 2) : 0.00;
    $get_bookings_details['driver_final_total'] = str_replace(',', '', $get_bookings_details['driver_final_total']);
    $get_total_advance = str_replace(',', '', $get_total_advance);
    
    $balance = (float)$get_bookings_details['driver_final_total'] - (float)$get_total_advance;
    $balance = number_format($balance, 2);
    $textColor = $balance < 0 ? 'red' : 'black';
    
    //dd($get_bookings_details['commision_profit_total']);
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
                        <!--<th style="width: 10%;">Parking Charges</th>-->
                        <th style="width: 10%;">Commission</th>
                        <!--<th style="width: 10%;">Advance</th>-->
                        <th style="width: 10%;">Final Amount</th>
                    </tr>
                </thead>
                <tbody>
                   @php
                        $get_job_detail = '';
                    
                        foreach ($get_job_details as $k => $jobs) {
                            $driverAmount = isset($jobs['driver_amount']) ? $website_currency . number_format((float)$jobs['driver_amount'], 2) : '-';
                            $carParkAmount = isset($jobs['car_park_amount']) ? $website_currency . number_format((float)$jobs['car_park_amount'], 2) : '-';
                            $commissionProfitValue = isset($jobs['commision_profit']) ? (float)$jobs['commision_profit'] : 0;
                            $commissionProfit = $website_currency . number_format($commissionProfitValue, 2);
                    
                            $driverFinalValue = isset($jobs['driver_final']) ? (float)$jobs['driver_final'] : 0;
                            $driverFinal = $website_currency . number_format($driverFinalValue, 2);
                    
                            // Determine color
                            $textColor = $driverFinalValue < 0 ? 'red' : 'black';
                    
                            $get_job_detail .= "
                                <tr>=
                                    <td style='text-align:left; padding-left:5px;'>" . htmlspecialchars($jobs['job_no']) . "</td>
                                    <td style='text-align:left;'>" . (!empty($jobs['pickup_date']) ? date('d-m-Y', strtotime($jobs['pickup_date'])) : '-') . " " . (!empty($jobs['pickup_time']) ? date('H:i', strtotime($jobs['pickup_time'])) : '') . "</td>
                                    <td style='text-align:left; padding-left:5px;'>" . htmlspecialchars($jobs['from']) . "</td>
                                    <td style='text-align:left; padding-right:5px;'>" . htmlspecialchars($jobs['to']) . "</td>
                                    <td style='text-align:center; padding-right:5px;'>" . htmlspecialchars($jobs['car_type']) . "</td>
                                    <td style='text-align:right; padding-right:5px;'>" . $driverAmount . "</td>
                                    <!--<td style='text-align:right; padding-right:5px;'>" . $carParkAmount . "</td>-->
                                    <!--<td style='text-align:right; padding-right:5px;'>" . $commissionProfit . "</td>-->
                                    <td style='text-align:right; padding-right:5px;'>" . $commissionProfit . "</td>
                                    <!--<td style='text-align:right; padding-right:5px;'>" . $website_currency . $get_avg_advance . "</td>-->
                                    <td style='text-align:right; padding-right:5px; color: $textColor;'>" . $driverFinal . "</td>
                                </tr>";
                        }
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
                            {{ $get_bookings_details['driver_amount_total'] == "0" ? "0" : $website_currency . $get_bookings_details['driver_amount_total'] }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;">Settlement Date</th>
                        <td style="padding: 4px 3px; text-align:right;">{{ date('d-m-Y', strtotime($get_transaction['settle_date'])) }}</td>
                        <th style="padding: 4px 3px; text-align:left;">Commission Amount (-)</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{ $get_bookings_details['commision_profit_total'] == "0" ? "0" : $website_currency . $get_bookings_details['commision_profit_total'] }}
                        </td>
                    </tr>
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;"></th>
                        <td style="padding: 4px 3px; text-align:right;"></td>
                        <th style="padding: 4px 3px; text-align:left;">Advance Payment (-)</th>
                        <td style="padding: 4px 3px; text-align:right;">
                            {{  $website_currency . $get_total_advance }}
                        </td>
                    </tr>
                    <!--<tr>-->
                    <!--    <th style="padding: 4px 3px; text-align:left;"></th>-->
                    <!--    <td style="padding: 4px 3px; text-align:right;">-->
                    <!--        {{ ($get_settle_history['old_balance'] == "0") ? "-" : $website_currency . number_format((float)$get_settle_history['old_balance'], 2) }}-->
                    <!--    </td>-->
                    <!--    <th style="padding: 4px 3px; text-align:left;"></th>-->
                    <!--    <td style="padding: 4px 3px; text-align:right;">-->
                    <!--        {{ $get_bookings_details['car_park_amount_total'] == "0" ? "-" : $website_currency . number_format((float)$get_bookings_details['car_park_amount_total'], 2) }}-->
                    <!--    </td>-->
                    <!--</tr>-->
                    <tr>
                        <th style="padding: 4px 3px; text-align:left;"></th>
                        <td style="padding: 4px 3px; text-align:right;">
                            <!--{{ ($get_settle_history['current_balance'] == 0) ? "-" : $website_currency . number_format((float)$get_settle_history['current_balance'], 2) }}-->
                        </td>
                        <th style="padding: 4px 3px; text-align:left;">Total Amount</th>
                        <td style='padding: 4px 3px; text-align:right; color: {{ $textColor }}; font-weight: bold;'>
                            {{ $balance == "0" ? "0" : $website_currency . $balance }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>
</body>
</html>

