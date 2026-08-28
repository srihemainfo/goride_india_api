
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    
    <title>
        {{ $seoData['partnerWeb']->title ?? "Experience Premium Travel with GoRide |The World's Leading Limo & Cab Dispatch Software Solution" }}
    </title>
    <meta name="description"
        content="{{ $seoData['partnerWeb']->meta_desp ?? "Discover GoRide, the world's top-rated limo and cab dispatch software. Streamline operations, enhance customer experiences, and drive efficiency with our cutting-edge, world-class platform" }}">
    <meta name="keywords"
        content="{{ $seoData['partnerWeb']->meta_keyword ?? "Cab booking software, Limo dispatch system, Ride-hailing platform, Taxi dispatch software, Fleet management software, Online booking system for taxis, Ride-sharing app, Taxi fleet software, Limo reservation system, Taxi booking system, Cab management software, Transportation dispatch software, Automated ride dispatch, On-demand taxi service, Best cab booking app, Ride-hailing dispatch system, Taxi fleet management, Limo fleet software, Real-time cab booking, Cab booking platform, Best limo software, Vehicle dispatch software, Taxi app software, On-demand transportation software, Ride-hailing solutions, Limo booking software, Fleet dispatch solutions" }}">
    <link rel="canonical" href="{{ request()->url() }}">
    <!--<link rel="shortcut icon" href="{{ asset('goride/img/Go-Ride-fav-icon.webp') }}" />-->
  
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($seoData['partnerWeb']->favicon ?? 'tone/images/demoicon.png') }}">

    
<!-- STYLES -->
<link rel="stylesheet" href="{{ asset('tone/css/sweetalert2.css') }}">
<link rel="stylesheet" href="{{ asset('tone/css/bootstrap.min.css') }}" type="text/css" media="all">
<!--<link rel="stylesheet" href="{{ asset('tone/css/all.min.css') }}" type="text/css" media="all">-->

 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 
<link rel="stylesheet" href="{{ asset('tone/css/slick.css') }}" type="text/css" media="all">
<link rel="stylesheet" href="{{ asset('tone/css/simple-line-icons.css') }}" type="text/css" media="all">
<link rel="stylesheet" href="{{ asset('tone/css/style.css') }}" type="text/css" media="all">
<link rel="stylesheet" href="{{ asset('tone/css/form.css') }}" type="text/css" media="all">
<link rel="stylesheet" href="{{ asset('tone/css/easy-autocomplete.min.css') }}">
<link rel="stylesheet" href="{{ asset('tone/css/animate.css') }}">
<link rel="stylesheet" href="{{ asset('tone/css/easy-autocomplete.themes.min.css') }}">
<link rel="stylesheet" href="{{ asset('tone/css/all.min.css') }}" />

<!-- Correct SweetAlert2 CSS -->
<link rel="stylesheet" href="{{ asset('tone/css/sweetalert2.min.css') }}">

<!-- Correct SweetAlert2 JS -->
<script src="{{ asset('tone/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('tone/js/jquery.min.js') }}"></script>
<script src="{{ asset('tone/js/jquery.easy-autocomplete.min.js') }}"></script>

<script src="{{ asset('tone/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('tone/js/smooth-scroll.js') }}"></script>
<script src="{{ asset('tone/js/sweetalert2.min.js') }}"></script>


    
    
    @yield('css')
  
</head>

<body>

	<div class="site-wrapper">

		<div class="main-overlay"></div>

		<!-- header -->
		
		@if (Route::currentRouteName() != 'bookingpreview')
    @include($theme .'.include.header')
@endif
	 


            @yield('content')

	

		<!-- footer -->
 

		@if (Route::currentRouteName() != 'bookingpreview')
    @include($theme . '.include.footer')
@endif
	 


        
        @yield('script')

	</div>
	





</body>

</html>