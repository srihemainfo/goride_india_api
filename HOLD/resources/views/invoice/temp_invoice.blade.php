@php
//dd($data);
$data = $data['data'];
$alphaPart = preg_replace('/[^A-Za-z]/', '', $data['job_no']);

$invoiceCode = str_replace($alphaPart, "INV", $data['job_no']);
@endphp

<!DOCTYPE html>
<html lang='en'>
    <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
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
            main{
                margin-top: -30px;
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
            th {
                border: 1px solid black;
                border-collapse: collapse;
                text-align: center;
                padding-top: 10px;
                padding-bottom: 10px;
            }
            td {
                /* border-left: 1px solid black; */
                /* border-right: 1px solid black; */
                /* border-collapse: collapse; */
                text-align: center;
                padding-bottom: 5px;
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
            table tr td:last-child {
                border-right: 1px solid black;
            }
            table tr td:first-child {
                border-left: 1px solid black;
            }
            table tr td{
                border-bottom: 1px solid black;
            }
        </style>
    </head>
    <body>
        <header style='border-bottom: 2px solid #000;'>
            <div class='header-content' style="padding: 0;">
               <h4 style="margin: 0;">{{$data['website']}}</h4>
                  
            </div>
            <div style="width: 100%;">
                <div class="a"><b>Phone :</b>{{ $data['c_mobile'] }}</div>
                <div class="a" style="text-align: right;"><b>Email :</b>{{ $data['c_email'] }}</div>
            </div>
        </header>
        <footer style='font-weight: bold;'>
            <p>{{$data['website']}}. &#169; {{ date('Y') }} </p>
        </footer>

        <main>
            <div style="width: 100%;">
                <h3 style="text-align: center; font-size: 30px;color: green; margin-top: 40px; margin-bottom: 15px;">
                    <b> INVOICE </b>
                </h3>
                <div class="a"><b>Invoice No :</b> {{ $invoiceCode }} <br/><b>Name :</b>{{ $data['fname'] }}<br><b>Address :</b>{{ $data['address1'] }}</div>
                <div class="a" style="text-align: right;">
                    
                    <b>Invoice Date :</b> {{ date('d-M-Y',strtotime($data['updated_at'])) }} <br/>
                    <b>Order Status :</b> {{ $data['order_status'] }} <br/>
                    <b>Payment Method :</b> {{ ucwords(str_replace('_', ' ', $data['type'])) }} <br/>
                    <b>Payment Status :</b> {{ $data['payment_status'] }}
                </div>
            </div>
            <div class='container'>
                <table>
                    <thead>
                        <tr>
                            <th style='font-size: 15px;width: 10%;background-color: Gainsboro;'>Job No</th>
                            <th style='font-size: 15px;width: 20%;background-color: Gainsboro;'>Pickup Date & Time</th>
                            <th style='font-size: 15px;width: 40%;background-color: Gainsboro;'>Description</th>
                            <th style='font-size: 15px;width: 10%;background-color: Gainsboro;'>Cost</th>
                            <th style='font-size: 15px;width: 10%;background-color: Gainsboro;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style='border-bottom: white;'>
                            <td style='text-align:center; font-size: 14px;padding-right:5px;'>{{ $data['job_no'] }}</td>
                            <td style='text-align:center; font-size: 12px;padding-right:5px;'>{{$data['pickup_date']}} {{$data['pickup_time']}}</td>
                            <td style='text-align:left; font-size: 14px;padding-right:5px;'>
                                <b>From </b>: {{$data['from']}} </br>
                                <b>To </b>: {{$data['to']}}</br>
                                <b>Car Type </b>: {{$data['car_type']}}</br>
                                <b>Extra </b>: {{$data['car_park_amount']}} 
                            </td>
                            <td style='text-align:center; font-size: 14px;padding-right:5px;'>{{ $data['total'] }}</td>
                            <td style='text-align:center; font-size: 14px;padding-right:5px;'>{{$data['total'] + $data['car_park_amount']}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class='main-amount' style='page-break-inside: avoid;'>
                    <table>
                        <tr>
                            <th style='padding: 4px 3px; font-size: 17px; text-align:left;color: green;'> Total Amount </th>
                            <td style='padding: 4px 3px; text-align:right;font-size: 15px;border-top: 1px solid black;border-bottom: 1px solid black;'>
                            <b>{{$data['currency']}} {{ number_format($data['total'] + $data['car_park_amount'], 2) }}</b>
                             </td>

                        </tr>
                    </table>
                </div>
            </div>
        </main>

    </body>
</html>
