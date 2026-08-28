@php
    // dd($job_details, $summary_details);

    $job_detail = '';

    foreach ($job_details as $job):
        $job_detail .=
            "<tr>
        <td style='text-align: left; font-size:11px;'>" .
            strtoupper($job->driver_name) .
            "</td>
        <td>" .
            $job->job_no .
            "</td>
        <td>" .
            date('d-m-Y', strtotime($job->pickup_date)) .
            ' (' .
            substr($job->pickup_time, 0, 5) .
            ")</td>
        <td style='text-align: left; font-size:11px;'>" .
            $job->from .
            "</td>
        <td style='text-align: left; font-size:11px;'>" .
            $job->to .
            "</td>
        <td style='text-align: center; font-size:11px;'>" .
            $job->car_type .
            "</td>
        <td style='text-align: right;'>" .
            $job->total .
            "</td>
        <td style='text-align: right;'>" .
            $job->order_status .
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
@php
dd($summary_details);

@endphp
<body>
    <header style='border-bottom: 2px solid #000;'>
        <div class='header-content'>
            <img src='{{ base_path() }}/public/logo.png' style='width: 40%'>
        </div>
        <h3 style='font-size: 14px; text-align:center;'>Admin Daily Report</h3>
    </header>

    <footer style='font-weight: bold;'>
        <p> Airportrides.co. &#169; {{ date('Y') }} </p>
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
                        <td style='padding: 4px 0px;'>{{ date('d-m-Y', strtotime($date)) }}</td>
                        <td style='padding: 4px 0px;'>". $summary_details->total_raids ."</td>
                    </tr>
                </table>
            </div>
        </div>
    </main>


</body>

</html>
