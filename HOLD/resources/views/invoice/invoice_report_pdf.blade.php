@php
    $booking_detail = '';
    $book_status = $booking_details[0]['payment_status']??'NA';
    $company_name = '';
    $book_total = 0;
    foreach ($booking_details as $booking):
        $company_name = $booking['website'];
        $booking_detail .=
            "<tr style='border-bottom: white;'>
        <td style='text-align:left; font-size: 13px;padding-left:5px;'><b>" .
            $booking['job_no'] .
            "</b></td>
        <td style='font-size: 13px;'>" .
            date('d-m-Y',strtotime($booking['pickup_date'])) ." ".
            date('h:i',strtotime($booking['pickup_time'])) .
            "</td>
        <td style='text-align:left; font-size: 12px;padding-left:5px;'>
            <b>Passenger Name :</b> " .$booking['fname'] ."<br>
            <b>From :</b> ". $booking['from'] ."<br>
            <b>To :</b> ".$booking['to'] . "<br>
            <b>Car :</b> ".$booking['car_type']. "<br>
            <b>Extra :</b> ".$booking['car_park_amount'].
            "</td>
        <td style='text-align:right; font-size: 14px;padding-right:5px;'> " .
            ($booking['total'] ? $website_currency . number_format($booking['total'],2) : '-').
            "</td>

        <td style='text-align:right; font-size: 14px;padding-right:5px;'> " .
            ($booking['total'] ? $website_currency . number_format($booking['total'],2) : '-') .
            "</td>
    </tr>";
        $book_total += $booking['total'] + $booking['car_park_amount'];
    endforeach;

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
            .header-content .table {
                width: 100%;
                vertical-align: middle;
            }
            .header-content .table,
            .table th {
                border: 1px solid black;
                border-collapse: collapse;
                text-align: center;
                padding-top: 10px;
                padding-bottom: 10px;
            }
            .table td {
                /* border-left: 1px solid black; */
                /* border-right: 1px solid black; */
                /* border-collapse: collapse; */
                text-align: center;
                padding-bottom: 5px;
            }
            .main-amount .table {
                margin-top: 1em;
                width: 100%;
                text-align: center;
            }
            .container .table {
                width: 100%;
                text-align: center;
            }
            .container .table,
            th,
            td {
                font-size: 12px;
                border-collapse: collapse;
            }
            .a {
                width: 49%;
                display: inline-block;
            }
            .table tr td:last-child {
                border-right: 1px solid black;
            }
            .table tr td:first-child {
                border-left: 1px solid black;
            }
            .table tr td{
                border-bottom: 1px solid black;
            }
        </style>
    </head>
    <body>
        <header style='border-bottom: 2px solid #000;'>
            <div class='header-content' style="padding: 0;">
               <h4 style="margin: 0;">{{$company_name}}</h4>
                  <!--@if(!empty($partner_list) && isset($partner_list[0]['company_logo']) && !empty($partner_list[0]['company_logo']))-->
                  <!--      <img src="{{ $partner_list[0]['company_logo'] }}" style='width: 10%'>-->
                  <!--  @else-->
                  <!--      <img src="{{ base_path() }}/public/logo.png" style='width: 10%'>-->
                  <!--  @endif-->
  
                
            </div>
            <div style="width: 100%;">
                <div class="a"><b>Phone :</b>{{ $partner_list[0]['phone'] }}</div>
                <div class="a" style="text-align: right;"><b>Email :</b>{{ $partner_list[0]['email'] }}</div>
            </div>
        </header>
        <footer style='font-weight: bold;'>
            <p>{{ $company_name }}. &#169; {{ date('Y') }} </p>
        </footer>

        <main>
            <div style="width: 100%;">
                <h3 style="text-align: center; font-size: 30px;color: green; margin-top: 40px; margin-bottom: 15px;">
                    <b> INVOICE </b>
                </h3>
                <table width="100%" style="border-collapse: collapse;" border="0">
                    <tr>
                        <td style="width: 50%;">
                            <table>
                                <tr>
                                    <td><b>Invoice No</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ $invoice_totals['invoiceno'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Name</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ $invoice_totals['clientname'] }}</td>
                                </tr>
                                <tr>
                                    <td><b>Address</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ $invoice_totals['clientaddress'] }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%;text-align: left;">
                            <table style="float: right;">
                                <tr>
                                    <td><b>Invoice Date</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ date('d-M-Y', strtotime($invoice_totals['invdate'])) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Payment Method</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $invoice_totals['pay_type'])) }}</td>
                                </tr>
                                <tr>
                                    <td><b>Payment Status</b></td>
                                    <td style="padding-left: 10px;">:</td>
                                    <td>{{ $book_status }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='container' style="margin-top:20px;">
                <table class="table">
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
                        {!! $booking_detail !!}
                    </tbody>
                </table>
                <div class='main-amount' style='page-break-inside: avoid;'>
                    <table class="table">
                        <tr>
                            <th style='padding: 4px 3px; font-size: 17px; text-align:left;color: green;'> Total Amount </th>
                            <td style='padding: 4px 3px; text-align:right;font-size: 15px;border-top: 1px solid black;border-bottom: 1px solid black;'>
                            <b>{{ $website_currency }}{{ number_format($book_total, 2) }}</b>
                             </td>

                        </tr>
                    </table>
                </div>
            </div>
        </main>

    </body>
</html>
