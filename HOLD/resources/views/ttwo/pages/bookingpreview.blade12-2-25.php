@extends($theme. '.layouts.app')



@section('css')
    <style>

.button-oneNew{
  display: inline-block;
  transition: all 0.2s ease-in;
  position: relative;
  overflow: hidden;
  z-index: 1;
  color: #090909;
  padding: 0.7em 1.7em;
  cursor: pointer;
  font-size: 18px;
  text-decoration: none;
  border-radius: 0.5em;
  background: #e8e8e8;
  border: 1px solid #e8e8e8;
  box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff;
}

.button-oneNew:active {
  color: #666;
  box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;
}

.button-oneNew:before {
  content: "";
  position: absolute;
  left: 50%;
  transform: translateX(-50%) scaleY(1) scaleX(1.25);
  top: 100%;
  width: 140%;
  height: 180%;
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 50%;
  display: block;
  transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
  z-index: -1;
}

.button-oneNew:after {
  content: "";
  position: absolute;
  left: 55%;
  transform: translateX(-50%) scaleY(1) scaleX(1.45);
  top: 180%;
  width: 160%;
  height: 190%;
  background-color: #009087;
  border-radius: 50%;
  display: block;
  transition: all 0.5s 0.1s cubic-bezier(0.55, 0, 0.1, 1);
  z-index: -1;
}

.button-oneNew:hover {
  color: #ffffff;
  border: 1px solid #009087;
}

.button-oneNew:hover:before {
  top: -35%;
  background-color: #009087;
  transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
}

.button-oneNew:hover:after {
  top: -45%;
  background-color: #009087;
  transform: translateX(-50%) scaleY(1.3) scaleX(0.8);
}

.booking-preview-btn {
    display: inline-block;
    transition: all 0.2s ease-in;
    position: relative;
    overflow: hidden;
    z-index: 1;
    color: #090909;
    padding: 0.7em 1.7em;
    font-size: 18px;
    text-decoration: none;
    border-radius: 0.5em;
    background: transparent;
    border: 3px solid #e8e8e8;
    cursor: default !important;
}
    
    
</style>
@endsection


@section('content')

<section class="bp-section">
    <div class="container">
        <div class="text-center my-3 bottom-line mv-div">
            <h3 style="color:#f8c41b">{{ ($seoData['partnerWeb']->company_name ?? '') }} Booking Information</h3>
            <h6 style="color:#000000; margin-bottom:0;">
                Email: <a href="mailto:{{ ($seoData['partnerWeb']->email ?? '') }}">{{ ($seoData['partnerWeb']->email ?? '') }}</a>
                Cell: <a href="{{ ($seoData['partnerWeb']->contact_number ?? '') }}">{{ ($seoData['partnerWeb']->contact_number ?? '') }}</a>
                Phone: <a href="tel:{{ ($seoData['partnerWeb']->contact_number ?? '') }}">{{ ($seoData['partnerWeb']->contact_number ?? '') }}</a>
            </h6>
            <h6 style="color:#000000; margin:0">
                Thank You for using <a href="http://{{ request()->getHost() }}">{{ request()->getHost() }}</a> to complete your journey. Please check your journey details.
            </h6>
        </div>

        <table class="table table-bordered" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center;color: #fff;">Booking Information</th>
            </tr>
            
           
            <tr>
                <td style="width:50%">Booking ID</td>
                <td>{{  $seoData['bookinfo']['book']->job_no ?? '' }}</td>
            </tr>
           
            <tr>
                <td>Booked On</td>
                <td>{{ date('d-m-Y H:i:s', strtotime($seoData['bookinfo']['book']->booking_date)) }}
</td>
            </tr>
            
            
            <tr>
                <td>Booking Status</td>
                <td>{{ $seoData['bookinfo']['book']->order_status ?? '' }}</td>
            </tr>
        </table>

        <table class="table table-bordered mt-3" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center; color: #fff;">Your Route</th>
            </tr>
            <tr>
                <td style="width:50%">Journey Type</td>
                <td>{{ $seoData['bookinfo']['book']->way ?? '' }}</td>
            </tr>
            
            
         
            <tr>
                <td>Date of Journey</td>
                <td> {{ date('d-m-Y', strtotime($seoData['bookinfo']['book']->pickup_date)) }}  </td>
            </tr>
            <tr>
                <td>Pick Up Time</td>
                <td>{{ substr($seoData['bookinfo']['book']->pickup_time , 0, -3) }}</td>
            </tr>
            
                
            <tr>
                <td>Preferred Vehicle</td>
                <td>{{ $seoData['bookinfo']['book']->car_type ?? '' }} </td>
            </tr>
            <tr>
                <td>Message</td>
                <td>{{ $seoData['bookinfo']['book']->message ?? '' }}  </td>
            </tr>
        </table>
        
        

        <table class="table table-bordered mt-3" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center; color: #fff;">Passengers & Luggage</th>
            </tr>
            <tr>
                <td style="width:50%">Passengers</td>
                <td>{{ $seoData['bookinfo']['book']->passengers ?? '' }}  </td>
            </tr>
            <tr>
                <td>Baby seat</td>
                <td>{{ $seoData['bookinfo']['book']->child_seat ?? '' }}  </td>
            </tr>
            <tr>
                <td>Luggage</td>
                <td>{{ $seoData['bookinfo']['book']->baggages ?? '' }} </td>
            </tr>
            <tr>
                <td>Hand Luggage</td>
                <td>{{ $seoData['bookinfo']['book']->hand_luggages ?? '' }}  </td>
            </tr>
        </table>


 
        <table class="table table-bordered mt-3" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center; color: #fff;">Your Details</th>
            </tr>
            <tr>
                <td style="width:50%">Your Name</td>
                <td>{{ $seoData['bookinfo']['book']->fname ?? ''  }}</td>
            </tr>
            <tr>
                <td>Contact Number</td>
                <td>{{ $seoData['bookinfo']['book']->mobile ?? ''  }}</td>
            </tr>
            <tr>
                <td>E-mail Address</td>
                <td>{{ $seoData['bookinfo']['book']->email ?? ''  }}</td>
            </tr>
        </table>
        
        
 
        <table class="table table-bordered mt-3" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center; color: #fff;">Journey Details</th>
            </tr>
            <tr>
                <td style="width:50%">From</td>
                <td>{{ $seoData['bookinfo']['book']->from ?? ''  }}</td>
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
            
            



            @if($seoData['bookinfo']['book']->place_from  == 1)
                <tr>
                    <td>Flight Number</td>
                    <td>{{ $seoData['bookinfo']['book']->pickup_flight_num ?? ''  }}</td>
                </tr>
                <tr>
                    <td>Flight From</td>
                    <td>{{  $seoData['bookinfo']['book']->pickup_flight_from ?? '' }}</td>
                </tr>
                <tr>
                    <td>Meet and Greet Service</td>
                    <td>{{   $seoData['bookinfo']['book']->meet_greet ?? ''  }}</td>
                </tr>
            @elseif($seoData['bookinfo']['book']->place_from  == 2)
                <tr>
                    <td>Ship Name</td>
                    <td>{{  $seoData['bookinfo']['book']->pick_shipname ?? ''   }}</td>
                </tr>
                <tr>
                    <td>Ship From</td>
                    <td>{{  $seoData['bookinfo']['book']->pick_shipname ?? '' }}</td>
                </tr>
                <tr>
                    <td>Meet and Greet Service</td>
                    <td>{{  $seoData['bookinfo']['book']->meet_greet ?? ''   }}</td>
                </tr>
            @else
                <tr>
                    <td>Pickup Address</td>
                    <td>{{  $seoData['bookinfo']['book']->pickup_address ?? ''    }}</td>
                </tr>
                <tr>
                    <td>Pickup Postcode</td>
                    <td>{{  $seoData['bookinfo']['book']->pickup_postcode ?? ''    }}</td>
                </tr>
            @endif
            
            
   
            <tr>
                <td>To</td>
                <td>{{  $seoData['bookinfo']['book']->to ?? ''  }}</td>
            </tr>

            @if( $seoData['bookinfo']['book']->place_to  == 4)
                <tr>
                    <td>Dropoff Address</td>
                    <td>{{  $seoData['bookinfo']['book']->dest_address ?? ''  }}</td>
                </tr>
                <tr>
                    <td>Dropoff Postcode</td>
                    <td>{{ $seoData['bookinfo']['book']->dest_postcode ?? ''  }}</td>
                </tr>
            @endif
        </table>
        
        

 

        <table class="table table-bordered mt-3" style="color:#000000;">
            <tr>
                <th colspan="2" style="background-color:#535048; text-align:center; color: #fff;">Payment Details</th>
            </tr>
            <tr>
                <td style="width:50%">Payment Status</td>
                <td>{{  $seoData['bookinfo']['book']->payment_status ?? ''    }}</td>
            </tr>
            @if($seoData['bookinfo']['pickupTimeContent'] )
                <tr>
                    <td>Time Charges Message</td>
                    <td>{{ $seoData['bookinfo']['pickupTimeContent'] ?? '' }}</td>
                </tr>
            @endif
            @if($seoData['bookinfo']['pickupDateContent'])
                <tr>
                    <td>Date Charges Message</td>
                    <td>{{ $seoData['bookinfo']['pickupDateContent'] ?? '' }}</td>
                </tr>
            @endif
            <tr>
                <td>Journey Cost</td>
                <td>{{  $seoData['bookinfo']['currencySymbol']  }}{{  $seoData['bookinfo']['book']->total ?? ''  }}</td>
            </tr>
            <tr>
                <td>Payment</td>
                <td>{{  str_replace('_', ' ', strtoupper($seoData['bookinfo']['book']->type))?? ''    }}</td>
            </tr>
        </table>

        <div class="col-lg-12 mt-4 col-sm-12 mv-div">
            <p style="color:#000000;text-align:center;font-size:large">
                If you have a difficulty to find a driver please call us on {{ ($seoData['partnerWeb']->contact_number ?? '') }}
            </p>
            <p style="color:#000000;text-align:center;font-size:large">
                Hope to see you again in your future. Have a nice journey.
            </p>
            <p style="color:#000000;text-align:center;font-size:large">Best Regards</p>
            <p style="color:#000000;text-align:center;font-size:large">{{ ($seoData['partnerWeb']->company_name ?? '') }}</p>
        </div>
    </div>
</section>
<script>
   window.onload = function() {
        const text_val = @json($getText);
        if (text_val === 'print') {
            window.print();
        }
    };
   
</script>
@endsection



@section('script')

@endsection