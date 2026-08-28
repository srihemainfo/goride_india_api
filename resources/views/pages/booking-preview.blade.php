<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GoRide | Booking Preview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
    
            .fare-onwards {
            display: block;         
            font-size: 13px;
            color: #888;
            font-weight: 500;
            margin-top: 2px;        
        }
        body {
            margin: 0;
            background: #f4f6fb;
            font-family: 'Inter', sans-serif;
            color: #111827;
        }

        .container {
            max-width: 980px;
            margin: 30px auto;
            margin-top: 0;
            padding: 15px;
        }

        .card {
            background: #ffffff;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
        }
        
        .brand img {
            height: 44px;
        }

        .brand h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
        }

        .brand small {
            color: #6b7280;
        }

        .status {
            padding: 6px 16px;
            border-radius: 999px;
            background: #fff7ed;
            color: #c2410c;
            font-size: 32px;
            font-weight: 700;
        }

        .route {
            display: flex;
            gap: 16px;
            margin-top: 18px;
        }

        .route-box {
            flex: 1;
            background: #f9fafb;
            border-radius: 12px;
            padding: 14px;
        }

        .route-box small {
            color: #6b7280;
            font-size: 12px;
        }

        .route-box strong {
            display: block;
            margin-top: 6px;
            font-size: 15px;
        }

        h3 {
            margin: 0 0 14px;
            font-size: 17px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .info {
            background: #f9fafb;
            border-radius: 12px;
            padding: 14px;
        }

        .info small {
            color: #6b7280;
            font-size: 12px;
        }

        .info strong {
            display: block;
            margin-top: 6px;
            font-size: 15px;
        }
 
        .fare-box {
            border: 2px solid #f9bf00;
            border-radius: 14px;
            padding: 22px;
        }

        .fare-total {
            font-size: 34px;
            font-weight: 800;
            /*color: #f9bf00;*/
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 8px 0;
            font-size: 14px;
        }

        .total-row {
            border-top: 1px solid #e5e7eb;
            font-weight: 700;
        }

        .btns {
            display: flex;
            gap: 14px;
            margin-top: 22px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-outline {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            color: #374151;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #6b7280;
        }

        @media(max-width: 640px) {
            .route {
                flex-direction: column;
            }
        }
        @media print {
            .btn,
            .btns {
                display: none !important;
            }
        
            body {
                background: #ffffff !important;
            }
        
            .card {
                box-shadow: none !important;
                border: 1px solid #e5e7eb;
            }
        }
        
        /* Floating Buttons */
        .floating-actions {
            position: fixed;
            bottom: 25px;
            right: 25px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 9999;
        }
        
        @media screen and (max-width: 767px) {
            .floating-actions {
                left: 25px;
                right: unset;
            }
        }
        
        .floating-actions button {
            width: 55px;
            height: 55px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 22px;
            font-weight: bold;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            transition: 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Share Button */
        .btn-share {
            background: #2563eb;
            color: #fff;
        }
        
        /* Print Button */
        .btn-print {
            background: #f9bf00;
            color: #111;
        }
        
        /* Hover Effect */
        .floating-actions button:hover {
            transform: scale(1.12);
        }
        
        .booking-heading {
            text-align: center;
            margin-bottom: 1rem;
        }

        .booking-heading h2 {
            margin: 0;
        }
        
        /* Driver Card */
        .driver-wrapper{
            display:flex;
            flex-wrap:wrap;
            gap:20px;
            align-items:center;
        }
        
        /* Driver profile */
        .driver-profile{
            display:flex;
            align-items:center;
            gap:16px;
            min-width:220px;
        }
        
        /* Avatar */
        .driver-avatar{
            width:70px;
            height:70px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #f9bf00;
            box-shadow:0 6px 15px rgba(0,0,0,0.15);
        }
        
        /* Driver text */
        .driver-role{
            font-size:12px;
            color:#6b7280;
        }
        
        .driver-name{
            margin:2px 0 0;
            font-size:16px;
            font-weight:700;
        }
        
        /* Info grid */
        .driver-info-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:14px;
            flex:1;
        }
        
        .driver-info{
            background:#f9fafb;
            padding:14px;
            border-radius:10px;
        }
        
        .driver-info span{
            display:block;
            font-size:12px;
            color:#6b7280;
        }
        
        .driver-info strong{
            font-size:15px;
        }
        
        /* View images button */
        .view-images-btn{
            background:#2563eb;
            color:#fff;
            border:none;
            padding:6px 12px;
            border-radius:6px;
            font-size:13px;
            cursor:pointer;
        }
        
        .view-images-btn:hover{
            background:#1e40af;
        }
        
        .gallery-modal{
            display:none;
            position:fixed;
            z-index:99999;
            left:0;
            top:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.9);
            align-items:center;
            justify-content:center;
        }
        
        .gallery-image{
            max-width:90%;
            max-height:80%;
            border-radius:10px;
        }
        
        .gallery-close{
            position:absolute;
            top:25px;
            right:40px;
            font-size:40px;
            color:white;
            cursor:pointer;
        }
        
        .gallery-prev,
        .gallery-next{
            position:absolute;
            top:50%;
            transform:translateY(-50%);
            background:rgba(255,255,255,0.2);
            border:none;
            color:white;
            font-size:30px;
            padding:10px 18px;
            cursor:pointer;
        }
        
        /* Premium Fare Card */
        
        .premium-fare{
            border:2px solid #f4b000;
            padding:26px;
        }
        
        /* header */
        .fare-header h3{
            margin:0 0 18px 0;
            font-size:18px;
            font-weight:700;
        }
        
        /* rows */
        
        .fare-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:10px 0;
            font-size:14px;
        }
        
        /* subtle separator */
        
        .fare-divider{
            border-top:1px solid #e5e7eb;
            margin:8px 0;
        }
        
        /* estimated */
        
        .fare-estimate{
            font-weight:700;
            font-size:16px;
        }
        
        .fare-estimate-right{
            text-align:right;
        }
        
        .fare-estimate small{
            display:block;
            font-size:12px;
            color:#6b7280;
        }
        
        /* paid amount */
        
        .paid{
            margin-top:10px;
            padding:12px 14px;
            background:#f0fdf4;
            border-radius:8px;
        }
        
        .paid-value{
            color:#059669;
            font-weight:700;
        }
        
        /* balance */
        
        .balance{
            margin-top:8px;
            padding:12px 14px;
            background:#fff7ed;
            border-radius:8px;
        }
        
        .balance-value{
            color:#c2410c;
            font-weight:700;
        }
        
        /* currency alignment */
        
        .fare-row span:last-child{
            font-weight:600;
        }
        
        /* Payment accordion */

        .payment-accordion{
            margin-top:18px;
            border-top:1px solid #e5e7eb;
        }
        
        .accordion-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            font-weight:600;
            padding:12px 0;
            cursor:pointer;
        }
        
        .accordion-header i{
            transition:0.3s;
        }
        
        .accordion-body{
            display:block;
            padding-top:10px;
        }
        
        /* payment rows */
        
        .payment-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 14px;
            margin-bottom:8px;
            border-radius:10px;
            font-size:14px;
        }
        
        /* left section */
        
        .payment-left{
            display:flex;
            align-items:center;
            gap:10px;
        }
        
        /* icons */
        
        .payment-icon{
            width:32px;
            height:32px;
            border-radius:8px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
        }
        
        /* colors */
        
        .online-icon{
            background:#ecfdf5;
            color:#059669;
        }
        
        .wallet-icon{
            background:#eef2ff;
            color:#4338ca;
        }
        
        .balance-icon{
            background:#fff7ed;
            color:#c2410c;
        }
        
        /* values */
        
        .payment-value{
            font-weight:700;
        }
        
        .online-value{
            color:#059669;
        }
        
        .wallet-value{
            color:#4338ca;
        }
        
        .balance-value{
            color:#c2410c;
        }
        
        /* Credit Bonus */

        .credit-icon{
            background:#fef3c7;
            color:#b45309;
        }
        
        .credit-value{
            color:#b45309;
            font-weight:700;
        }
        
        .gallery-prev{ left:20px; }
        .gallery-next{ right:20px; }
        /* Hide Floating Buttons in Print */
        @media print {
            .floating-actions {
                display: none !important;
            }
        }
        
        
        @media screen and (max-width: 365px) {
            .status {
                font-size: 18px;
            }
        }
        
        .fare-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            font-size: 14px;
        }
        
        /* Left label */
        .fare-row > span:first-child {
            flex: 1;
            color: #6b7280;
        }
        
        /* Right container */
        .fare-row > span:last-child {
            flex: 1;
            text-align: right;
        }
        
        /* Price line */
        .price-line {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            flex-wrap: wrap;
        }
        
        /* Old price */
        .old-price {
            text-decoration: line-through;
            color: #9ca3af;
            font-size: 12px;
        }
        
        /* Saved text */
        .saved-text {
            display: block;
            font-size: 12px;
            color: #059669;
            margin-top: 2px;
        }
        
        /* Mobile fine-tuning */
        @media (max-width: 480px) {
            .fare-row {
                font-size: 13px;
            }
        
            .price-line {
                gap: 4px;
            }
        
            .saved-text {
                font-size: 11px;
            }
        }

    </style>
</head>
<body>
    
@php
    if($user_details == null){
        $base_fare = $base_fare;
    }

@endphp

<div class="container">
    
    <div class="booking-heading">
        <h2>Booking Information</h2>
        <small>Booking ID: {{ $job_no }}</small>
    </div>
    <div id="vehicleGallery" class="gallery-modal">
        <span class="gallery-close" onclick="closeVehicleGallery()">&times;</span>
    
        <img id="galleryImage" class="gallery-image">
    
        <button class="gallery-prev" onclick="changeImage(-1)">❮</button>
        <button class="gallery-next" onclick="changeImage(1)">❯</button>
    </div>

    <div class="card">
        <div class="header">
            <div class="brand">
                <img src="{{ asset('goride/img/logo-light.png') }}" alt="GoRide">
                <div>
                    <span class="status">{{$job_status}}</span>
                </div>
            </div>
        </div>

        <div class="route">
            <div class="route-box">
                <small>Pickup Location</small>
                <strong>{{ $from_place }}</strong>
            </div>
            <div class="route-box">
                <small>Drop Location</small>
                <strong>{{ $to_place }}</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Trip Details</h3>
        <div class="grid">
            <div class="info">
                <small>Pickup Date & Time</small>
                <strong>{{ $pickup_date }}</strong>
            </div>
            @if($day)
                <div class="info">
                    <small>Duration</small>
                    <strong>{{ $day }}</strong>
                </div>
            @endif
            <div class="info">
                <small>Trip Type</small>
                <strong>{{ ucfirst($job_type) }}</strong>
            </div>
            <div class="info">
                <small>Vehicle</small>
                <strong>{{ $cab_type }}</strong>
            </div>
            @if($pass_count !== '')
            <div class="info">
                <small>Passengers Count</small>
                <strong>{{ $pass_count }}</strong>
            </div>
            @endif
            @if($lugg_count !== '')
            <div class="info">
                <small>Luggage</small>
                <strong>{{ $lugg_count }}</strong>
            </div>
            @endif
            <div class="info">
                <small>Distance Upto</small>
                <strong>{{ $distance }} kms</strong>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Passengers Details</h3>
        <div class="grid">
            <div class="info">
                <small>Name</small>
                <strong>{{ $name }}</strong>
            </div>
            <div class="info">
                <small>Mobile</small>
                <strong>{{ $mobile }}</strong>
            </div>
            <div class="info" style="display:none;">
                <small>Email</small>
                <strong>{{ $email }}</strong>
            </div>
        </div>
    </div>

    
    <div class="card fare-box premium-fare">

        <div class="fare-header">
            <h3>Fare Breakdown</h3>
        </div>
    
        <div class="fare-row">
            <span>Base Fare</span>
        
            @if($isDiscount == 'yes')
                <span>
                    <span>₹{{ $base_fare }}</span>
                    <span class="old-price">₹{{ $actual_base }}</span>
                    <span class="saved-text">
                        Saved ₹{{ $credit_bonus ?? 0 }} (From GoRide Credit Bonus)
                    </span>
                </span>
            @else
                <span>₹{{ $base_fare }}</span>
            @endif
        </div>
    
        <div class="fare-row">
            <span>Toll (Govt. Levy / Extra)</span>
            <span>₹{{ $govt_levy }}</span>
        </div>
    
        <div class="fare-row">
            <span>Tax (GST 5%)</span>
            <span>₹{{ $tax }}</span>
        </div>
    
        <div class="fare-divider"></div>
    
        <div class="fare-row fare-estimate">
            <span>Estimated Fare</span>
            <div class="fare-estimate-right">
                ₹{{ $total_fare }}
                <small>(Onwards)</small>
            </div>
        </div>
        
        @if($isPayment)
            <div class="payment-accordion">

            <div class="accordion-header" onclick="togglePaymentAccordion()">
                <span>Payment Details</span>
                <i id="paymentArrow" class="fa-solid fa-chevron-down"></i>
            </div>
        
            <div id="paymentAccordionBody" class="accordion-body" style="display: none;">
        
                <!-- Online Payment -->
                @if($deductAmt == 0)
                    <div class="payment-row online">
                    <div class="payment-left">
                        <span class="payment-icon online-icon">
                            <i class="fa-solid fa-credit-card"></i>
                        </span>
                        <span>Paid via Online (UPI / Card)</span>
                    </div>
        
                    <span class="payment-value online-value">
                        ₹{{ $paid_amt ?? 0 }}
                    </span>
                </div>
                @endif
        
                <!-- Wallet Payment -->
                @if($deductAmt == 0 && ($wallet_amt != 0 || $wallet_amt != null))
                    <div class="payment-row wallet">
                        <div class="payment-left">
                            <span class="payment-icon wallet-icon">
                                <i class="fa-solid fa-wallet"></i>
                            </span>
                            <span>Paid via Wallet</span>
                        </div>
            
                        <span class="payment-value wallet-value">
                            ₹{{ $wallet_amt ?? 0 }}
                        </span>
                    </div>
                @endif
                
                <!-- Balance -->
                <div class="payment-row balance ">
                    <div class="payment-left">
                        <span class="payment-icon balance-icon">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </span>
                        <span> {{ $gateway == 'cash' ? 'Cash To Driver' : 'Balance Pay to Driver' }} </span>
                    </div>
        
                    <span class="payment-value balance-value">
                        ₹{{ $balance_amt ?? 0 }}
                    </span>
                </div>
        
            </div>
        
        </div>
        @endif
    
    </div>
    
    @if(!empty($driver_name) && $job_status == 'Confirmed')
            <div class="card">
        <h3>Driver & Vehicle Details</h3>
    
        <div class="driver-wrapper">
    
            <div class="driver-profile">
                <img 
                    src="{{ !empty($driver_image) ? $driver_image : asset('goride/img/user-placeholder.png') }}" 
                    class="driver-avatar"
                    onerror="this.src='{{ asset('goride/img/user-placeholder.png') }}'"
                >
    
                <div class="driver-meta">
                    <span class="driver-role">Driver</span>
                    <h4 class="driver-name">{{ $driver_name }}</h4>
                </div>
            </div>
    
            <div class="driver-info-grid">
    
                <div class="driver-info">
                    <span>Mobile Number</span>
                    <strong>
                        <a href="tel:{{ $driver_mobile }}">
                            {{ $driver_mobile }}
                        </a>
                    </strong>
                </div>
    
                <div class="driver-info">
                    <span>Vehicle Number</span>
                    <strong>{{ $vehicle_number }}</strong>
                </div>
    
                <div class="driver-info">
                    <span>Vehicle Photos</span>
                    <strong>
                        <button class="view-images-btn" onclick="openVehicleGallery()">
                            View Images
                        </button>
                    </strong>
                </div>
    
            </div>
    
        </div>
    </div>
    @endif

    <div class="card">
        <h3 style="margin-bottom:10px;">Terms and Conditions</h3>
        
        <div>
            <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.6;">
                <li>Your trip includes a fixed kilometer (KM) limit. Any distance traveled beyond this limit will be charged at the applicable rate per extra KM.</li>
                <li>The trip covers one pick-up from the pick-up city and one drop at the destination city only. Travel within the city limits is not included.</li>
                <li>For trips involving hill climbs, the air conditioning (AC) may be switched off during uphill drives for safety and vehicle performance.</li>
                <li>GoRide shall not be responsible or liable for any damages, injuries, losses, or disputes arising between the driver and the customer during the ride.</li>
            </ul>
        </div>
    </div>
    
    <div class="card">
        <h3 style="margin-bottom:10px;">Inclusions & Exclusions</h3>

        <div style="display:flex; gap:20px; flex-wrap:wrap;">
        
            <div style="flex:1; min-width:260px;">
                <p style="font-size:15px; font-weight:600; margin-bottom:6px;">
                    Included
                </p>
                <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.6;">
                    <li>{{ $distance }} km included</li>
                    <li>Driver allowance included</li>
                    <li>Driver food and accommodation (stay) charges included</li>
                    <li>Waiting time up to 30 minutes for pickup included (₹100 per 60 minutes after 30 minutes)</li>
                    <li>Sightseeing included</li>
                    <li>Fuel charges included</li>
                    <li>Govt. levy extra included (based on actual value)</li>
                    <li>Parking charges included</li>
                    </ul>
            </div>
         
            <div style="flex:1; min-width:260px;">
                <p style="font-size:15px; font-weight:600; margin-bottom:6px;">
                    Excluded
                </p>
                <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.6;">
                    <li>State permit / entry charges</li>
                    <li>Hill station charges (extra)</li>
                    <li>Govt. levy extra & parking charges</li>
                    <li>Additional kilometers: ₹{{ $perKm }} per km</li>
                    <li>Additional hours / days: ₹100 per hour & ₹1200 per day</li>
                    <li>Waiting: First 30 minutes free; additional waiting charges apply</li>
                    <li>Any government taxes or local charges, if applicable</li>
                </ul>
            </div>
        
        </div>

    </div>
    
    <div class="card">
        <h3 style="margin-bottom:10px;">Safety Guidelines</h3>

        <div style="display:flex; gap:20px; flex-wrap:wrap;">
        
            <div style="flex:1; min-width:260px;">
                <p style="font-size:15px; font-weight:600; margin-bottom:6px;">
                    Before Starting the Ride
                </p>
                <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.6;">
                    <li>Verify the driver’s photo and name</li>
                    <li>Check vehicle details (number plate & model)</li>
                    <li>Cross-check ride charges shown in the app</li>
                    <li>Take odometer photo before trip starts</li>
                    <li>Share trip details with trusted contact</li>
                </ul>
            </div>
         
            <div style="flex:1; min-width:260px;">
                <p style="font-size:15px; font-weight:600; margin-bottom:6px;">
                    After Completing the Ride
                </p>
                <ul style="margin:0; padding-left:18px; font-size:14px; line-height:1.6;">
                    <li>Take final odometer photo</li>
                    <li>Cross-check Govt. levy with receipts</li>
                    <li>Collect all your belongings</li>
                    <li>Confirm payment after verifying charges</li>
                </ul>
            </div>
        
        </div>

    </div>
    <div class="card">
    <h3 style="margin-bottom:10px;">Support & Assistance</h3>

    <div style="font-size:14px; line-height:1.4; ">
        
<p>
    If you experience any difficulty in finding a driver or require assistance during your trip,
    please feel free to contact us via 
    <strong>
        <a href="tel:+916369742104" style="color:#c89f17; text-decoration:none;">
            Call
        </a>
    </strong>
    or
    <strong>
        <a href="https://api.whatsapp.com/send/?phone=916369742104&text=Hello%2C%20I%20require%20assistance%20with%20my%20cab%20booking.%20Kindly%20connect%20with%20me.%20Thank%20you.&type=phone_number&app_absent=0"
           target="_blank"
           style="color:#c89f17; text-decoration:none;">
            WhatsApp
        </a>
    </strong>
    at
    <strong style="color:#c89f17;">+91 63697 42104</strong>,
    or email us at
    <strong>
        <a href="mailto:support@goride.run" style="color:#c89f17; text-decoration:none;">
            support@goride.run
        </a>
    </strong>.
</p>


        <p>
            We hope to see you again for your future outstation transport requirements.  
            <strong>Have a safe and pleasant journey.</strong>
        </p>

        <p style="margin-top:14px;">
            <strong>Best Regards,</strong><br>
            GoRide Team
        </p>

        <p style="margin-top:12px;">
            <a href="/terms" target="_blank" style="color:#c89f17; font-weight:600;">
                Terms &amp; Conditions
            </a>
        </p>

    </div>
</div>


<footer>
    © {{ date('Y') }} GoRide • Safe • Reliable • Affordable
</footer>

<div class="floating-actions">
    
    <button class="btn-share" onclick="shareBooking()" title="Share Booking">
        <i class="fa-solid fa-share-nodes"></i>
    </button>

    <button class="btn-print" onclick="window.print()" title="Print Booking">
        🖨️
    </button>

</div>

</div>

</body>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "GoRide",
  "image": "https://www.goride.run/goride/img/logo-dark-2.png",
  "@id": "https://www.goride.run/",
  "url": "https://www.goride.run/",
  "telephone": "+916369742104",
  "priceRange": "0-9999",
  "address": {
    "@type": "PostalAddress",
    "addressCountry": "IN"
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "00:00",
    "closes": "23:59"
  },
  "sameAs": [
    "https://www.facebook.com/goride25",
    "https://twitter.com/go_rides8499",
    "https://www.instagram.com/goride.run/",
    "https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow",
    "https://www.linkedin.com/company/goride-run/posts/?feedView=all",
    "https://www.goride.run/"
  ] 
}
</script>
<script>

    function shareBooking() {
    
        let shareData = {
            title: "GoRide Booking Preview",
            text: "Here is my GoRide booking details.",
            url: window.location.href
        };
    
        if (navigator.share) {
            navigator.share(shareData)
            .then(() => console.log("Shared successfully"))
            .catch((err) => console.log("Share failed:", err));
        } else {
            // Fallback: Copy URL
            navigator.clipboard.writeText(window.location.href);
            alert("Link copied! You can share it manually.");
        }
    }
    
    let vehicleImages = [
        @foreach($vehicle_images as $img)
            "{{ $img }}",
        @endforeach
    ];
    
    let currentImageIndex = 0;
    
    function openVehicleGallery(){
        document.getElementById("vehicleGallery").style.display = "flex";
        showImage();
    }
    
    function closeVehicleGallery(){
        document.getElementById("vehicleGallery").style.display = "none";
    }
    
    function changeImage(step){
        currentImageIndex += step;
    
        if(currentImageIndex >= vehicleImages.length){
            currentImageIndex = 0;
        }
    
        if(currentImageIndex < 0){
            currentImageIndex = vehicleImages.length - 1;
        }
    
        showImage();
    }
    
    function showImage(){
        document.getElementById("galleryImage").src = vehicleImages[currentImageIndex];
    }
    
    function togglePaymentAccordion(){

        let body = document.getElementById("paymentAccordionBody");
        let arrow = document.getElementById("paymentArrow");
    
        if(body.style.display === "block"){
            body.style.display = "none";
            arrow.style.transform = "rotate(0deg)";
        }else{
            body.style.display = "block";
            arrow.style.transform = "rotate(180deg)";
        }
    
    }

    // window.addEventListener('load', function () {
    //     setTimeout(function () {
    //         window.print();
    //     }, 500);
    // });
</script>

</html>