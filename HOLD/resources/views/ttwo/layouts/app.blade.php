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
  
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($seoData['partnerWeb']->favicon ?? 'ttwo/images/demoicon.png') }}">
	
	
	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	
	<link rel="stylesheet" href="{{ asset('ttwo/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/fontawesome-all.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/flaticon-2.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/animate.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/video.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/slick.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/slick-theme.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/metisMenu.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/uikit.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/twenty.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/reset.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/easy-autocomplete.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/easy-autocomplete.themes.min.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/sweetalert.css') }}">
	<link rel="stylesheet" href="{{ asset('ttwo/css/jquery.timepicker.min.css') }}">

		
		
		
	<script async src="{{ asset('ttwo/js/gtm.js') }}"></script>
	<script src="{{ asset('ttwo/js/jquery.js') }}"></script>
	<script src="{{ asset('ttwo/js/jquery-3.6.4.min.js') }}"></script>
	<script src="{{ asset('ttwo/js/jquery.timepicker.min.js') }}"></script>
	
</head>

<body>
	<div class="progress-wrap">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
		</svg>
	</div>

@if (Route::currentRouteName() != 'bookingpreview')
    @include($theme .'.include.header')
@endif

	<aside class="slide-bar">
		<div class="close-mobile-menu">
			<a href="javascript:void(0);"><i class="fal fa-times"></i></a>
		</div>
		<nav class="side-mobile-menu">
			<ul id="mobile-menu-active">
				<li><a href="Demo Themeusine-service-toronto">Demo Themeusine-service</a></li>
				<li><a href="airport-Demo Theme-services">Airport Transfers</a></li>
				<li><a href="fleet">Our Fleet</a></li>
				<li><a href="contact">Contact us</a></li>
			</ul>
		</nav>
		<div class="sidebar-social mt-20">
			<a href="javacript:void(0);"><i class="fab fa-facebook-f"></i></a>
			<a href="javacript:void(0);"><i class="fab fa-twitter"></i></a>
			<a href="javacript:void(0);"><i class="fab fa-instagram"></i></a>
			<a href="javacript:void(0);"><i class="fab fa-linkedin-in"></i></a>
		</div>
	</aside>
	<div class="body-overlay"></div>

	
	    @yield('content')

	
		@if (Route::currentRouteName() != 'bookingpreview')
    @include($theme . '.include.footer')
@endif
	
	<script data-cfasync="false" src="{{ asset('ttwo/js/email-decode.min.js') }}"></script>
	<script src="{{ asset('ttwo/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('ttwo/js/popper.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/jquery.magnific-popup.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/appear.js') }} "></script>
	<script src="{{ asset('ttwo/js/slick.js') }} "></script>
	<script src="{{ asset('ttwo/js/jquery.counterup.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/waypoints.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/imagesloaded.pkgd.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/jquery.filterizr.js') }} "></script>
	<script src="{{ asset('ttwo/js/backToTop.js') }}"></script>
	<script src="{{ asset('ttwo/js/uikit.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/metisMenu.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/twenty.js') }} "></script>
	<script src="{{ asset('ttwo/js/wow.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/gmap3.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/scripts-2.js') }} "></script>
	<script src="{{ asset('ttwo/js/script.js') }} "></script>
	<script src="{{ asset('ttwo/js/jquery.easy-autocomplete.min.js') }} "></script>
	<script src="{{ asset('ttwo/js/sweetalert.js') }} "></script>
	<script src="{{ asset('ttwo/js/jquery.timepicker.min_1.js') }} "></script>

</body>

</html>