@extends($theme. '.layouts.app')

@php
   
    $get_date_cost = ($seoData['bookinfo']['book']->additional_cost_date ?? 0);
    $get_time_cost = ($seoData['bookinfo']['book']->additional_cost_time ?? 0);
  
    
   // $get_date_cost = is_numeric($get_date_cost) && $get_date_cost > 0 ? $get_date_cost : 0;
    //$get_time_cost = is_numeric($get_time_cost) && $get_time_cost > 0 ? $get_time_cost : 0;

    $get_date_cost = is_numeric($get_date_cost) ? $get_date_cost : 0;
    $get_time_cost = is_numeric($get_time_cost) ? $get_time_cost : 0;
    
    $tot_sp_cost = $get_time_cost + $get_date_cost;

@endphp



@section('css') 

<style>

    body {
        font-family: "Poppins", sans-serif;
        font-size: 12px;
    }

    a {
        color: black;
    }
    .table>:not(caption)>*>* {
        padding: .3rem .3rem;
    }

    /*.bp-section .container {*/
    /*    padding: 0 10rem;*/
    /*}*/

    h3, h6 {
        font-weight: 700;
        margin: 20px 0;
    }

    .bottom-line {
        border-bottom: 1px solid #f8c41b;
        padding-bottom: 5px;
    }
    
    
    
    h3, h6 {
        font-weight: 700;
        margin: 10px 0; 
    }
    
    @media screen and (max-width: 576px) {
        
        .bp-section .container {
            padding: 0 1.5rem;
        }
        
    }
    
    /*@media print {*/
    /*    * {*/
    /*        -webkit-print-color-adjust: exact !important;*/
    /*        print-color-adjust: exact !important;*/
    /*    }*/

    /*    @page {*/
    /*        margin: 1cm;*/
    /*    }*/
        
    /*    body {*/
    /*        margin: 1cm;*/
    /*    }*/
    /*}*/
    
    @media print {
      @page {
        margin: 0; /* This adds space on all sides in most browsers */
      }
    
      #print-btn {
        display: none; /* Hide the print button when printing */
      }
    }

    
    .btn.btn-outline-primary {
        width: auto;
        position: absolute;
        right: 10px;
        top: 10px;
    }

</style>

@endsection





@section('content')

<section class="bp-section">
    
    <div class="container position-relative">
        <button class="btn btn-outline-primary" id="print-btn">
            Print 
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
            </svg>
        </button>
    </div>
    <div class="container" style="padding: 0;" id="main-content">
        
        <div class="text-center my-3 bottom-line mv-div">

            <h3 style="color:#f8c41b">{{ ($seoData['partnerWeb']->company_name ?? 'NA') }} Booking Information</h3>

            <h6 style="color:#000; margin-bottom:0;">

                Email: <a href="mailto:{{ ($seoData['partnerWeb']->email ?? 'NA') }}">{{ ($seoData['partnerWeb']->email ?? 'NA') }}</a>

                Phone: <a href="tel:{{ ($seoData['partnerWeb']->contact_number ?? 'NA') }}">{{ ($seoData['partnerWeb']->contact_number ?? 'NA') }}</a>

            </h6>

            <h6 style="color:#000; margin:0">

                Thank You for using our service to complete your journey. Please check your journey details.

            </h6>

        </div>

        <div style="display: flex;">
            
            <table class="table table-bordered" style="color:#000;">
    
                <tr>
    
                    <th colspan="2" style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">Booking Information</th>
    
                </tr>
    
    
    
    
    
                <tr>
    
                    <td style="width:50%;">Booking ID</td>
    
                    <td>{{ $seoData['bookinfo']['book']->job_no ?? 'NA' }}</td>
    
                </tr>
    
    
    
                <tr>
    
                    <td>Booked On</td>
    
                    <td>{{ date('d-m-Y', strtotime($seoData['bookinfo']['book']->booking_date)) }}
    
                    </td>
    
                </tr>
    
    
    
    
    
                <tr>
    
                    <td>Booking Status</td>
    
                    <td>{{ $seoData['bookinfo']['book']->order_status ?? 'NA' }}</td>
    
                </tr>
    
            </table>
    
    
    
            <table class="table table-bordered ms-2" style="color:#000;">
    
                <tr>
    
                    <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Your Route</th>
    
                </tr>
    
                <tr>
    
                    <td style="width:50%">Journey Type</td>
    
                    <!--<td>{{ $seoData['bookinfo']['book']->way ?? 'NA' }}</td>-->
                    <td>
                        {{
                            isset($seoData['bookinfo']['book']->way) ?
                                ($seoData['bookinfo']['book']->way == 'roundtrip' ? 'RoundTrip' :
                                ($seoData['bookinfo']['book']->way == 'tariff_oneway' ? 'Tariff OneWay' :
                                $seoData['bookinfo']['book']->way))
                            : 'N/A'
                        }}
                    </td>
    
                </tr>
    
    
    
    
    
    
    
                <tr>
    
                    <td>Date of Journey</td>
    
                    <td> {{ date('d-m-Y', strtotime($seoData['bookinfo']['book']->pickup_date)) }} </td>
    
                </tr>
    
                <tr>
    
                    <td>Pick Up Time</td>
    
                    <td>{{ substr($seoData['bookinfo']['book']->pickup_time , 0, -3) }}</td>
    
                </tr>
    
    
    
    
    
                <tr>
    
                    <td>Preferred Vehicle</td>
    
                    <td>{{ $seoData['bookinfo']['book']->car_type ?? 'NA' }} </td>
    
                </tr>
    
                <!--<tr>-->
    
                <!--    <td>Message</td>-->
    
                <!--    <td>{{ $seoData['bookinfo']['book']->message ?? 'NA' }} </td>-->
    
                <!--</tr>-->
    
            </table>
            
        </div>








        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Passengers & Luggage</th>

            </tr>

            <tr class="text-center">

                <!--<td style="width:50%;">Passengers / Baby seat / Luggage / Hand Luggage</td>-->

                <td colspan="2"> Passengers: {{ $seoData['bookinfo']['book']->passengers ?? 'NA' }} / Baby seat: {{ $seoData['bookinfo']['book']->child_seat ?? 'NA' }} / Luggage: {{ $seoData['bookinfo']['book']->baggages ?? 'NA' }}  / Hand Luggage: {{ $seoData['bookinfo']['book']->hand_luggages ?? 'NA' }}</td>

            </tr>

            <!--<tr>-->

            <!--    <td>Baby seat</td>-->

            <!--    <td>{{ $seoData['bookinfo']['book']->child_seat ?? 'NA' }} </td>-->

            <!--</tr>-->

            <!--<tr>-->

            <!--    <td>Luggage</td>-->

            <!--    <td>{{ $seoData['bookinfo']['book']->baggages ?? 'NA' }} </td>-->

            <!--</tr>-->

            <!--<tr>-->

            <!--    <td>Hand Luggage</td>-->

            <!--    <td>{{ $seoData['bookinfo']['book']->hand_luggages ?? 'NA' }} </td>-->

            <!--</tr>-->

        </table>







        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Your Details</th>

            </tr>

            <tr>

                <td style="width:50%">Your Name</td>

                <td>{{ $seoData['bookinfo']['book']->fname ?? 'NA'  }}</td>

            </tr>

            <tr>

                <td>Contact Number</td>

                <td>{{ $seoData['bookinfo']['book']->mobile ?? 'NA'  }}</td>

            </tr>

            <tr>

                <td>E-mail Address</td>

                <td>{{ $seoData['bookinfo']['book']->email ?? 'NA'  }}</td>

            </tr>

        </table>







        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Journey Details</th>

            </tr>

            <tr>

                <td style="width:50%">From</td>

                <td>{{ $seoData['bookinfo']['book']->from ?? 'NA'  }}</td>

            </tr>



            @if( $seoData['bookinfo']['book']->way == 'One way')

            @foreach([1, 2, 3] as $i)

            @if(!empty($seoData['bookinfo']['book']->{'viapoint' . $i}))

            <tr>

                <td>Via Point:</td>

                <td>{{ $seoData['bookinfo']['book']->{'viapoint' . $i} }}</td>

            </tr>

            @endif

            @endforeach

            @elseif( $seoData['bookinfo']['book']->way == 'Return')

            @foreach([1, 2, 3] as $i)

            @if(!empty($seoData['bookinfo']['book']->{'viapoint' . $i}))

            <tr>

                <td>Return Via Point:</td>

                <td>{{ $seoData['bookinfo']['book']->{'viapoint' . $i} }}</td>

            </tr>

            @endif

            @endforeach

            @endif











            @if($seoData['bookinfo']['book']->pickup_flight_num && $seoData['bookinfo']['book']->pickup_flight_num != '')

            <tr>

                <td>Flight Number</td>

                <td>{{ $seoData['bookinfo']['book']->pickup_flight_num ?? 'NA'  }}</td>

            </tr>

            <tr>

                <td>Flight From</td>

                <td>{{ $seoData['bookinfo']['book']->pickup_flight_from ?? 'NA' }}</td>

            </tr>

            <tr>

                <td>Meet and Greet Service</td>

                <td>{{ $seoData['bookinfo']['book']->meet_greet ?? 'NA'  }}</td>

            </tr>

            @elseif($seoData['bookinfo']['book']->pick_shipname && $seoData['bookinfo']['book']->pick_shipname != '')
            <tr>

                <td>Ship Name</td>

                <td>{{ $seoData['bookinfo']['book']->pick_shipname ?? 'NA'   }}</td>

            </tr>

            <tr>

                <td>Ship From</td>

                <td>{{ $seoData['bookinfo']['book']->pick_shipfrom ?? 'NA' }}</td>

            </tr>

            <tr>

                <td>Meet and Greet Service</td>

                <td>{{ $seoData['bookinfo']['book']->meet_greet ?? 'NA'   }}</td>

            </tr>

            @else

            <tr>

                <td>Pickup Address</td>

                <td>{{ $seoData['bookinfo']['book']->pickup_address ?? 'NA'    }}</td>

            </tr>

            <tr>

                <td>Pickup Postcode</td>

                <td>{{ $seoData['bookinfo']['book']->pickup_postcode ?? 'NA'    }}</td>

            </tr>

            @endif







            <tr>

                <td>To</td>

                <td>{{ $seoData['bookinfo']['book']->to ?? 'NA'  }}</td>

            </tr>



            @if( $seoData['bookinfo']['book']->place_to == 4)

            <!--<tr>-->

            <!--    <td>Dropoff Address</td>-->

            <!--    <td>{{ $seoData['bookinfo']['book']->dest_address ?? 'NA'  }}</td>-->

            <!--</tr>-->

            <!--<tr>-->

            <!--    <td>Dropoff Postcode</td>-->

            <!--    <td>{{ $seoData['bookinfo']['book']->dest_postcode ?? 'NA'  }}</td>-->

            <!--</tr>-->

            @endif

        </table>


        <table class="table table-bordered mt-3" style="color:#000;">

            <tr>

                <th colspan="2" style="background-color: #535048;text-align: center;font-size: 18px;color: #fff;font-weight: 600;">Payment Details</th>

            </tr>

            <tr class="text-center">

                <!--<td style="width:50%">Payment Status</td>-->

                <td colspan="2">
                    Payment Status: {{ $seoData['bookinfo']['book']->payment_status ?? 'NA' }} /
                    <span class="fw-bold">
                        Journey Cost: {{ $seoData['bookinfo']['currencySymbol'] ?? '₹' }}{{ $seoData['bookinfo']['book']->total ?? 'NA' }}
                        (Special Offers: {{ ($seoData['bookinfo']['currencySymbol'] ?? '₹') . ($tot_sp_cost ?? '0') }})  
                    </span> /
                    Payment: {{ isset($seoData['bookinfo']['book']->type) ? str_replace('_', ' ', strtoupper($seoData['bookinfo']['book']->type)) : 'NA' }}
                </td>

            </tr>
            <tr class="text-center">

                <!--<td style="width:50%">Payment Status</td>-->

                <td colspan="2">Message: {{ $seoData['bookinfo']['book']->message ?? 'NA' }}</td>

            </tr>

            <tr class="text-start">

                <!--<td style="width:50%">Payment Status</td>-->

                <td colspan="1">Special Offer Time : {{ $seoData['bookinfo']['currencySymbol'] ?? '₹' }}{{ $get_time_cost ? $get_time_cost :'0' }}</td>
                <td colspan="1">Special Offer Date : {{ $seoData['bookinfo']['currencySymbol'] ?? '₹' }}{{ $get_date_cost ? $get_date_cost :'0' }}</td>

            </tr>

            @if($seoData['bookinfo']['pickupTimeContent'] )

            <tr>

                <td>Time Charges Message</td>

                <td>{{ $seoData['bookinfo']['pickupTimeContent'] ?? 'NA' }}</td>

            </tr>

            @endif

            @if($seoData['bookinfo']['pickupDateContent'])

            <!--<tr>-->

            <!--    <td>Date Charges Message</td>-->

            <!--    <td>{{ $seoData['bookinfo']['pickupDateContent'] ?? 'NA' }}</td>-->

            <!--</tr>-->

            @endif

            <!--<tr>-->

            <!--    <td>Journey Cost</td>-->

            <!--    <td>{{ $seoData['bookinfo']['currencySymbol']  }}{{ $seoData['bookinfo']['book']->total ?? 'NA'  }}</td>-->

            <!--</tr>-->

            <!--<tr>-->

            <!--    <td>Payment</td>-->

            <!--    <td>{{ str_replace('_', ' ', strtoupper($seoData['bookinfo']['book']->type)) ?? 'NA'    }}</td>-->

            <!--</tr>-->

        </table>
        
        <table class="table table-bordered mt-3 {{ in_array($seoData['bookinfo']['book']->order_status, ['Dispatched', 'Moving']) ? '' : 'd-none' }}" style="color:#000;" id="driver_details">

                    <tbody>
                        <tr>
                            <th colspan="2"
                                style="background-color:#535048; text-align:center; font-size:18px; color:#fff; font-weight:600;">
                                Driver Details</th>
                        </tr>
                        <tr>
                            <td style="width:50%;">Driver Name</td>
                            <td id="driver_name">{{$seoData['bookinfo']['book']->driver_name}}</td>
                        </tr>
                        <tr>
                            <td>Driver Image</td>
                            <td id="driver_img"><img src="../../{{$seoData['bookinfo']['book']->driver_upload_photo ?? 'dummy_driver_img.png'}}" width="100px" height="100px"></td>
                        </tr>
                        <tr>
                            <td>Driver Phone</td>
                            <td id="driver_phone">{{$seoData['bookinfo']['book']->driver_name ?? ''}}</td>
                        </tr>
                        <tr>
                            <td>Vehicle No</td>
                            <td id="vehicle_no">{{$seoData['bookinfo']['book']->vech_reg_num??''}}</td>
                        </tr>
                    </tbody>
                </table>



        <!--<div class="col-lg-12 mt-4 col-sm-12 mv-div">-->

        <!--    <p style="color:#000;text-align:center;font-size:large">-->

        <!--        If you have a difficulty to find a driver please call us on {{ ($seoData['partnerWeb']->contact_number ?? 'NA') }}-->

        <!--    </p>-->

        <!--    <p style="text-align:center;font-size:large">-->

        <!--        Hope to see you again in your future. Have a nice journey.-->

        <!--    </p>-->

        <!--    <p style="color:#000;text-align:center;font-size:large">Best Regards</p>-->

        <!--    <p style="color:#000;text-align:center;font-size:large">{{ ($seoData['partnerWeb']->company_name ?? 'NA') }}</p>-->

        <!--</div>-->

    </div>


</section>

<script>
$(document).ready(function () {
    $('#print-btn').click(function () {
        window.print(); // Let CSS control what shows in print
    });
});


    window.onload = function() {
        
        // $('#print-btn').click(function () {
        //     var printContent = $('#main-content').html();
        
        //     var originalContent = $('body').html();
        
        //     $('body').empty().append(printContent);
        //     window.print();
        //     $('body').html(originalContent);
        // });


        const text_val = @json($getText);

        if (text_val === 'print') {
            setTimeout(function() {
                window.print();
            }, 500);
        }

    };
</script>

@endsection







@section('script')

<script>





</script>

@endsection