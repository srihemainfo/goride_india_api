@php
    $transaction = '';
  //  dd($website_currency);
    foreach ($transactions as $trans):
        $transaction .=
            "<tr>
                <td style='text-align:left; font-size: 15px;padding-left:5px;'>" .
                    $trans['driver_name'] .
                "</td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" . 
                        (isset($trans['total']) && $trans['total'] != 0 ? $website_currency . ' ' . number_format($trans['total'], 2) : '-') .
                    "
            </td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" . 
    (isset($trans['bank']) && $trans['bank'] != 0 ? $website_currency . ' ' . number_format($trans['bank'], 2) : '-') .
"
</td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" .
    (isset($trans['cash']) && $trans['cash'] != 0 ? $website_currency . ' ' . number_format($trans['cash'], 2) : '-') .
"
</td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" .
    (isset($trans['card']) && $trans['card'] != 0 ? $website_currency . ' ' . number_format($trans['card'], 2) : '-') .
"
</td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" .
    (isset($trans['comm']) && $trans['comm'] != 0 ? $website_currency . ' ' . number_format($trans['comm'], 2) : '-') .
"
</td>
                <td style='text-align:right; font-size: 15px;padding-left:5px;'>" .
    (isset($trans['credit']) && $trans['credit'] != 0 ? $website_currency . ' ' . number_format($trans['credit'], 2) : '-') .
"
</td>

            </tr>";

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
       @if(!empty($partner_list) && isset($partner_list[0]['company_logo']) && !empty($partner_list[0]['company_logo']))
    <img src="{{ $partner_list[0]['company_logo'] }}" style='width: 10%'>
@else
    <img src="{{ base_path() }}/public/logo.png" style='width: 10%'>
@endif
            </div>
            <div style="width: 100%;">
                <div class="a"><b>Phone :</b>{{ $partner_list[0]['phone'] }}</div>
                <div class="a" style="text-align: right;"><b>Email :</b> {{ $partner_list[0]['email'] }}</div>
            </div>
        </header>

        <footer style='font-weight: bold;'>
            <p>{{ $partner_list[0]['company_name'] }} &#169; {{ date('Y') }} </p>
        </footer>

        <main>
            <div style="width: 100%;">
                <h3 style="text-align: center; font-size: 20px;">
                    <b> Settlement Report</b>
                </h3>

            </div>
            <div class='container'>
                <table>
                    <thead>
                        <tr>
                            <th style='font-size: 15px;width: 20%;'>Driver No.</th>
                            <th style='font-size: 15px;width: 10%;'>Total</th>
                            <th style='font-size: 15px;width: 10%;'>Bank / Invoice</th>
                            <th style='font-size: 15px;width: 10%;'>Cash</th>
                            <th style='font-size: 15px;width: 10%;'>Card</th>
                            <th style='font-size: 15px;width: 10%;'>Commission</th>
                            <th style='font-size: 15px;width: 10%;'>Weekly credit</th>

                        </tr>

                    </thead>
                    <tbody>

                        {!! $transaction !!}
                    </tbody>
                </table>
                <div class='main-amount' style='page-break-inside: avoid;'>
                    <table>
                        <tr>

                            <td style='padding: 4px 3px; text-align:right;width: 20%;'>Total</td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'><b>{{ isset($transaction_summary['total']) && $transaction_summary['total'] != 0 ? $website_currency . ' ' . number_format($transaction_summary['total'], 2) : '-' }}</b></td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'></td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'></td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'></td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'></td>
                            <td style='padding: 4px 3px; text-align:right;width: 10%;'><b>{{ isset($transaction_summary['credit']) && $transaction_summary['credit'] != 0 ? $website_currency . ' ' . number_format($transaction_summary['credit'], 2) : '-' }}</b></td>


                        </tr>

                    </table>
                </div>
            </div>
        </main>
    </body>
</html>
