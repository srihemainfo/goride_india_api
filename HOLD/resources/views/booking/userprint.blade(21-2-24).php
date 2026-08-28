@if(isset($sid) && !empty($sid))
    @php
    
    
    
         //dd($db_check[0]->db_key);
         //dd($sid);
         
        $data = \App\Models\Booking::where('id', $sid)->first();
        //dd($data);
        
      $currentConnection = DB::connection()->getName();
//dd($currentConnection);  
        
        //dd($data);
    @endphp
@endif
<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>airportrides</title>
    <meta name="description" content="airportrides">
    <link href="https://airportrides.co/assets/images/fav-d.png" sizes="128x128" rel="shortcut icon" type="image/x-icon">
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@1,700&display=swap');

h1 , table{
    letter-spacing: 1px;
    font-family: 'Sofia Sans Extra Condensed', sans-serif;
    font-size: 20px;
}

td {
    border: none !important;
}
    a:hover {text-decoration: underline !important;}
</style>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
        style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px; margin:0 auto;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height:60px;">&nbsp;</td>
                    </tr>
                    <!-- Logo -->
                    <tr>
                        <td style="text-align:center;">
                          <a href="https://airportrides.co/" title="airport rides" target="_blank">
                            <!--<img width="60" src="https://i.ibb.co/hL4XZp2/android-chrome-192x192.png" title="logo" alt="logo">-->
                             <div class="mobile_menu_main_logo"><img class="nav_logo_img img-fluid" src="{{ asset('assets/images/logo-black-new.png') }}" alt="images/header-logo2.png"></div>
                          </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <!-- Email Content -->
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px; background:#1A3760; border-radius:3px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);padding:0 40px; border-radius: 39px !important;
             background: #1A3760;
             box-shadow: 0px -2px 11px #132845, 3px -1px 5px #21467b !important;">
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                                <!-- Title -->
                                <tr>
                                    <td style="padding:0 15px; text-align:center;">
                                        <h1 style="color:#f3ba00; font-weight:400; margin:0;font-size:32px;">Booking Information</h1>
                                        <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; 
                                        width:100px;"></span>
                                    </td>
                                </tr>
                                <!-- Details Table -->
                                <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0"
                                            style="width: 100%; border: 1px solid #ededed">
                                            <tbody>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Booking ID:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->job_no }}             </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Booking Status:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->order_status }} </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Booked On:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        	{{ $data->created_at }}                </td>
                                                </tr>
                                                <!--your route-->
                                                <tr>
                                                 <th scope="col" colspan="2" style="background: #f3ba00;color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Your Route</th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Journey Type:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->way }} </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        Date of Journey:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->pickup_date }} </td>	
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        Flight Landing Time:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        {{ $data->pickup_time }}</td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        Preferred Vehicle:</td>
                                                    <td style="padding: 10px; color: #fff;">	{{ $data->car_type }} </td>
                                                </tr>
                                                
                                                  <!--passenger and lagguage-->
                                                <tr>
                                                 <th scope="col" colspan="2" style="background: #f3ba00;color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Passengers & Luggage</th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Booster Seat:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                       {{ $data->booster }} </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        	Passengers: </td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->passengers }} </td>	
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       	Luggages:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        {{ $data->baggages }} </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        Hand Luggages:</td>
                                                    <td style="padding: 10px; color: #fff;">{{ $data->hand_luggages }} </td>
                                                </tr>
                                                
                                                   <!--Your Details-->
                                                <tr>
                                                 <th scope="col" colspan="2" style="background: #f3ba00;color: white;font-size: 20px;font-weight: 600;    padding: 3px 0;">Your Details</th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                        Your Name:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                       {{ $data->fname }} </td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        	Contact Number: </td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->mobile }} </td>	
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       		E-mail Address:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        {{ $data->email }} </td>
                                                </tr>
                                                
                                                  <!--Journey Details-->
                                                <tr>
                                                 <th scope="col" colspan="2" style="background: #f3ba00;color: white;font-size: 20px;font-weight: 600;     padding: 3px 0;">Journey Details </th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                       From:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                       {{ $data->from }} </td>
                                                </tr>
                                    @if (str_contains(strtolower($data->from), 'airport') || str_contains(strtolower($data->from), 'terminal'))          
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        		Flight Number: </td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->pickup_flight_num }} </td>	
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       		Fight Arriving From:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        	{{ $data->pickup_flight_from }} </td>
                                                </tr>
                                                @else
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        		Pickup Address: </td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        {{ $data->pickup_address }} </td>	
                                                </tr>
                                                 <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       		Pickup Postcode:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        	{{ $data->pickup_postcode }} </td>
                                                </tr>
                                                @endif
                                                 <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       		To:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        	{{ $data->to }}                </td>
                                                </tr>
                                                
                                                 <!--Payment Details-->
                                                <tr>
                                                 <th scope="col" colspan="2" style="background: #f3ba00;color: white;font-size: 20px;font-weight: 600;     padding: 3px 0;">Payment Details </th>
                                                </tr>
                                                <!---->
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed;border-right: 1px solid #ededed; width: 35%; font-weight:600; color:rgb(243 186 0)">
                                                       	Payment Status:</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                       	{{ $data->payment_status }}</td>
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px;  border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                        		Journey Cost: </td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff;">
                                                        £ {{ $data->net_total }} </td>	
                                                </tr>
                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       			Payment :</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        		{{ $data->payment_type }} </td>
                                                </tr>
                                                
                                                                                                <tr>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; border-right: 1px solid #ededed; width: 35%;font-weight:600; color:rgb(243 186 0)">
                                                       			Message :</td>
                                                    <td
                                                        style="padding: 10px; border-bottom: 1px solid #ededed; color: #fff; ">
                                                        		{{ $data->message }} </td>
                                                </tr>
                                                
                                                
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:40px;">&nbsp;</td>
                                </tr>
                          
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                                <!--<p style="font-size:14px; color:#455056bd; line-height:18px; margin:0 0 0;">&copy; <strong></strong></p>-->
                        </td>
                    </tr>
                    <tr>
                        <!--<td style="height:60px;">&nbsp;</td>-->
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

<tbody>

	   <tr>
                        <td style="text-align:center;">
                                <!--<p style="font-size:14px; color:#455056bd; line-height:18px; margin:0 0 0;">&copy; <strong></strong></p>-->
                        </td>
                    </tr>
    <tr>
		<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%;
			padding-top: 25px;" class="social-icons"><table width="256" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse: collapse; border-spacing: 0; padding: 0;">
			<tbody><tr>

				<!-- ICON 1 -->
				<td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://www.facebook.com/profile.php?id=61551495456254" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="F" title="Facebook" width="44" height="44" src="{{ asset('assets/images/mail-icons/facebook.png') }}"></a></td>

				<!-- ICON 2 -->
				<td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://twitter.com/AirportridesCo" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="T" title="Twitter" width="44" height="44" src="{{ asset('assets/images/mail-icons/twitter.png') }}"></a></td>				

				<!-- ICON 3 -->
				<td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="G" title="Instagram" width="44" height="44" src="{{ asset('assets/images/mail-icons/instagram.png') }}"></a></td>		

				<!-- ICON 4 -->
				<td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="I" title="Contact" width="44" height="44" src="{{ asset('assets/images/mail-icons/call.png') }}"></a></td>
					
				<td align="center" valign="middle" style="margin: 0; padding: 0; padding-left: 10px; padding-right: 10px; border-collapse: collapse; border-spacing: 0;"><a target="_blank" href="https://airportrides.co/" style="text-decoration: none;"><img border="0" vspace="0" hspace="0" style="padding: 0; margin: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; border: none; display: inline-block;
					color: #000000;" alt="I" title="Website" width="44" height="44" src="{{ asset('assets/images/mail-icons/mail.png') }}"></a></td>

			</tr>
			 <tr>
                        <td style="height:60px;">&nbsp;</td>
                    </tr>
			</tbody></table>
		</td>
	</tr>
</tbody>

</html>