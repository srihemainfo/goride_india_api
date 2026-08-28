@php

    $get_job_detail = '';
    //dd($get_job_details);
    foreach ($get_job_details as $jobs):
        $get_job_detail .=
            "<tr>
        <td style='text-align:left; font-size: 15px;padding-left:5px;'>" .
            $jobs->job_no .
            "</td>
        <td style='font-size: 15px;'>" .
            date('d-M-Y',strtotime($jobs->pickup_date)) ." ".
            $jobs->pickup_time .
            "</td>
        <td style='text-align:left; font-size: 15px;padding-left:5px;'>" .
            $jobs->from.
            "</td>
        <td style='text-align:left; font-size: 15px;padding-right:5px;'>" .
            $jobs->to.
            "</td>
        <td style='text-align:center; font-size: 15px;padding-right:5px;'>" .
            $jobs->car_type.
            "</td>
        <td style='text-align:right; font-size: 15px;padding-right:5px;'>" .
            number_format($jobs->driver_amount,2).
            "</td>
        <td style='text-align:right; font-size: 15px;padding-right:5px;'>" .
            number_format($jobs->car_park_amount,2).
            "</td>
        <td style='text-align:right; font-size: 15px;padding-right:5px;'>" .
            number_format($jobs->commision_profit,2).
            "</td>
        <td style='text-align:right; font-size: 15px;padding-right:5px;'>" .
            number_format($jobs->driver_final,2).
            "</td>
    </tr>";

    endforeach;

@endphp

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <style>
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
            .a {
                width: 49%;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <header style='border-bottom: 2px solid #000;'>
            <div class='header-content'>
                <img src='{{ base_path() }}/public/logo.png' style='width: 20%'>
            </div>
            <div style="width: 100%;">
                <div class="a"><b>Phone :</b> +44 (0) 208 111 1104</div>
                <div class="a" style="text-align: right;"><b>Email :</b> info@ecminibus.co.uk</div>
            </div>
        </header>

        <footer style='font-weight: bold;'>
            <p> EC Mini Bus. &#169; {{ date('Y') }} </p>
        </footer>

        <main>
            <div style="width: 100%;">
                <h3 style="text-align: center; font-size: 20px;">
                    <b> Weekly Settlement Report</b>
                </h3>

            </div>
            <div class='container'>
                <table>
                    <thead>
                        <tr>
                            <th style='font-size: 15px;width: 10%;'>Job No</th>
                            <th style='font-size: 15px;width: 10%;'>Pickup Date & Time</th>
                            <th style='font-size: 15px;width: 10%;'>From</th>
                            <th style='font-size: 15px;width: 10%;'>To</th>
                            <th style='font-size: 15px;width: 10%;'>Car Type</th>
                            <th style='font-size: 15px;width: 10%;'>Total Amount</th>
                            <th style='font-size: 15px;width: 10%;'>Parking Charges</th>
                            <th style='font-size: 15px;width: 10%;'>Commission</th>
                            <th style='font-size: 15px;width: 10%;'>Final Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        {!! $get_job_detail !!}
                    </tbody>
                </table>
                <div class='main-amount' style='page-break-inside: avoid;'>
                    <table>
                        <tr>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Total Jobs </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ count($get_job_details) }}</td>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Driver Amount </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($driver_amount_total,2) }}</td>
                        </tr>
                        <tr>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Settlement For </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ $get_transaction->fromdate }}</td>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Commission Amount (-) </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($commision_profit_total,2) }}</td>
                        </tr>
                        <tr>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Opening Balance </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($get_settle_history->old_balance,2) }}</td>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Parking Charges </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($car_park_amount_total,2) }}</td>
                        </tr>
                        <tr>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Closing Balance </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($get_settle_history->current_balance,2) }}</td>
                            <th style='padding: 4px 3px; font-size: 14px; text-align:left;'> Total Amount </th>
                            <td style='padding: 4px 3px; text-align:right;'>{{ number_format($driver_final_total,2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </main>
    </body>
</html>
