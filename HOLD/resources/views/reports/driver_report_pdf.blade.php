@php
//dd($job_details);
    $job_detail = '';

    foreach ($job_details as $job):
        $job_detail .=
            "<tr>
    <td>" .
            $job->job_no .
            "</td>
    <td>" .
            date('d-m-Y', strtotime($job->pickup_date)) .
            ' (' .
            substr($job->pickup_time, 0, 5) .
            ")</td>
    <td>" .
            $job->from .
            "</td>
    <td>" .
            $job->to .
            "</td>
    <td>" .
            $job->car_type .
            "</td>
    <td>" .
            $job->driver_amount .
            "</td>
    <td>" .
            $job->car_park_amount .
            "</td>
    <td>" .
            $job->commision_profit .
            "</td>
    <td>" .
            $job->driver_final .
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
            margin: 7.1em .75em 1.75em;
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
            top: -115px;
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
    </style>
</head>

<body>
    <header style='border-bottom: 2px solid #000;'>
        <div class='header-content'>
            <!--@if(!empty($partner_lists) && isset($partner_lists[0]->company_logo) && !empty($partner_lists[0]->company_logo))-->
            <!--    <img src="{{ $partner_lists[0]->company_logo }}" style='width: 10%'>-->
            <!--@else-->
            <!--    <img src="{{ base_path() }}/public/logo.png" style='width: 10%'>-->
            <!--@endif-->
            <h3 style='font-size: 14px; text-align:center;'>{{ $job_details[0]->website }}</h3>
        </div>
        <h3 style='font-size: 14px; text-align:center;'>{{ $report_type }} Settlement Report</h3>
    </header>

    <footer style='font-weight: bold;'>
        <p> {{ $job_details[0]->website }} &#169; {{ date('Y') }}</p>
    </footer>

    <main>
        <div class='container'>
            <h3 style='font-size: 16px; text-align:center; margin-top: -1em;'>Driver Name:
                {{ $job_details[0]->name }}
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Job No.</th>
                        <th>Pickup Date/Time</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Car Type</th>
                        <th>Total Amount</th>
                        <th>Parking Charges</th>
                        <th>Commission</th>
                        <th>Final Amount</th>
                    </tr>
                </thead>
                <tbody>
                    {!! $job_detail !!}
                </tbody>
            </table>

            <div class='main-amount' style='page-break-inside: avoid;'>
                <table>
                    <tr>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Total Jobs</th>
                        <td style='padding: 4px 0px;'>{{ $amount_details->total_raids }}</td>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Driver Amount </th>
                        <td style='padding: 4px 0px;text-align: right;padding: 3px;'>
                            {{ $amount_details->Driver_Amount }}</td>
                    </tr>

                    <tr>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Settlement For</th>
                        <td style='padding: 4px 0px;'>{{ date('d-m-Y', strtotime($from)) }} to
                            {{ date('d-m-Y', strtotime($to)) }} </td>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Commission Amount (-) </th>
                        <td style='padding: 4px 0px;text-align: right;padding: 3px;'>
                            {{ $amount_details->Commission_Amount }}</td>
                    </tr>

                    <tr>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Opening Balance </th>
                        <td style='padding: 4px 0px;'>{{ $settle_history->old_balance }}</td>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Parking Charges (+) </th>
                        <td style='padding: 4px 0px;text-align: right;padding: 3px;'>
                            {{ $amount_details->Parking_Charges }}</td>
                    </tr>

                    <tr>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Closing Balance </th>
                        <td style='padding: 4px 0px;'>{{ $settle_history->current_balance }}</td>
                        <th style='padding: 4px 0px;text-align: left;padding: 3px;'> Total Amount</th>
                        <td style='padding: 4px 0px;text-align: right;padding: 3px;'>
                            {{ $amount_details->Driver_Amount }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </main>


</body>

</html>
