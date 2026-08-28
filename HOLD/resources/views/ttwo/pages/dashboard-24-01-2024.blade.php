@extends($theme. '.layouts.app')

@section('css')

@endsection


@section('content')
	<main>
		<div class="hero__two"
			style="background-image:linear-gradient(rgba(227, 237, 246, 0.5),rgba(115, 93, 255, 0)), url({{ asset('ttwo/images/banner6.webp') }});background-position: center top;background-size: cover;">
			<div class="header__main-wrap desk-vw" data-uk-sticky="top: 250; animation: uk-animation-slide-top;">
				<div class="container">
					<div class="header__main ul_li_between">
						<div class="header__main-left ul_li flex-1">
							<div class="header__bar hamburger_menu">
								<a href="home"><img src="{{ asset('ttwo/images/bar-2.svg') }}" alt></a>
							</div>
							<div class="header__logo ml-50">
								<a href="home">
									<!--<img src="{{ asset('ttwo/images/logo-white1.png') }}" alt="logo">-->
									<h1><img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != ''  ? $seoData['partnerWeb']->logo : 'tone/images/demoimage.png') }}" alt="logo" /></h1>
								</a>
							</div>
						</div>
						<div class="main-menu navbar navbar-expand-lg">
							<nav class="main-menu__nav collapse navbar-collapse">
							    
							  
							    			      @if(isset($seoData['getAllPages']) && count($seoData['getAllPages']) > 0)
    @php
        
       
        $getFirstFive = $seoData['getAllPages']->take(5);
        
        
    @endphp
    <ul class="navbar-nav">
        @foreach ($getFirstFive as $page)
            <li >
                  <a href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
            </li>
        @endforeach
    </ul>
@endif

								<!--<ul>-->
								<!--	<li><a href="Demo Themeusine-service-toronto">Demo Themeusine-service</a></li>-->
								<!--	<li><a href="airport-Demo Theme-services">Airport Transfers</a></li>-->
								<!--	<li><a href="fleet">Our Fleet</a></li>-->
								<!--	<li><a href="contact">Contact us</a></li>-->
								<!--</ul>-->
							</nav>
						</div>
						<div class="header__main-right ul_li">
						</div>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="row align-items-center mt-none-30 banner-contents">
					<div class="col-lg-6 mt-100">
						<div class="hero__content hero__content-two">
							<span class="wow fadeInUp mobile-banner-content1" data-wow-delay="0s"
								data-wow-duration="1500ms">Welcome to Toronto Pearson Airport
								Cars</span>
							<h1 style="color: #fff;" class="wow fadeInUp mobile-banner-content" data-wow-delay=".3s"
								data-wow-duration="1500ms">Toronto Airport Demo Theme
								Transfers</h1>
						</div>
					</div>
					<div class="col-lg-6">
					    
					    
					     <iframe src="{{ env('iframeURL') . request()->getHost() }}" width="100%" height="500" frameborder="0"></iframe>
						<!--<div class="banner-form-wrapper headline wow fadeInRight" data-wow-delay="800ms"-->
						<!--	data-wow-duration="1500ms">-->
						<!--	<h3>Get an instant quote</h3>-->
						<!--	<form action="car-select" method="post">-->
						<!--		<label for class="pick-drop">Pick-up</label>-->
						<!--		<input type="text" class="inp1" id="provider-remote1" name="from"-->
						<!--			placeholder="Enter Pick up Address" onchange="pearsonairportcheck_pop()" required>-->
						<!--		<div class="extra">-->
						<!--			<input type="checkbox" class="form-check-input" id="accept1"-->
						<!--				name="airport_pickup_checkbox" value="Toronto Pearson International Airport"-->
						<!--				onclick="PearsonAirportCheck()" aria-required="true">-->
						<!--			<label class="form-check-label" for="accept1" style="color:#fff;">-->
						<!--				Pick-up Location : Toronto Pearson International Airport</label>-->
						<!--		</div>-->
						<!--		<label for class="pick-drop">Drop-off</label>-->
						<!--		<input type="text" class="inp1" id="provider-remote2" name="to"-->
						<!--			placeholder="Enter Drop off Address" required>-->
						<!--		<div class="extra">-->
						<!--			<input type="checkbox" class="form-check-input" id="accept12"-->
						<!--				name="airport_dropoff_checkbox" value="yes" onclick="PearsonAirportCheckdrop()"-->
						<!--				aria-required="true">-->
						<!--			<label class="form-check-label" for="accept12" style="color:#fff;">-->
						<!--				Drop-off Location : Toronto Pearson International Airport</label>-->
						<!--		</div>-->
						<!--		<input type="checkbox" class="form-check-input" id="accept_tc1" name="check_return"-->
						<!--			value="Yes" aria-required="true">-->
						<!--		<label class="form-check-label" for="accept_tc1" style="color:#fff;">-->
						<!--			Return Journey?</label>-->
						<!--		<div class="returns" id="return-hidden" style="display:none;">-->
						<!--			<label for class="pick-drop">Pick-up</label>-->
						<!--			<input type="text" class="inp1" id="provider-remote3" name="return_from"-->
						<!--				placeholder="Enter Pick up Address" onchange="pearsonairportcheck_pop_drop()">-->
						<!--			<div class="extra">-->
						<!--				<input type="checkbox" class="form-check-input" id="accept13"-->
						<!--					name="airport_pickup_checkbox_return" value="yes"-->
						<!--					onclick="PearsonAirportCheck_return()" aria-required="true">-->
						<!--				<label class="form-check-label" for="accept13" style="color:#fff;">-->
						<!--					Pick-up Location : Toronto Pearson International Airport</label>-->
						<!--			</div>-->
						<!--			<label for class="pick-drop">Drop-off</label>-->
						<!--			<input type="text" class="inp1" id="provider-remote4" name="return_to"-->
						<!--				placeholder="Enter Drop off Address">-->
						<!--			<div class="extra">-->
						<!--				<input type="checkbox" class="form-check-input" id="accept14"-->
						<!--					name="airport_dropoff_checkbox_return"-->
						<!--					onclick="PearsonAirportCheckdrop_return()" value="yes" aria-required="true">-->
						<!--				<label class="form-check-label" for="accept14" style="color:#fff;">-->
						<!--					Drop-off Location : Toronto Pearson International Airport</label>-->
						<!--			</div>-->
						<!--		</div>-->
						<!--		<button name="submit" type="submit" onclick="form_submit_function()"-->
						<!--			class="book-btn">GET QUOTE</button>-->
						<!--	</form>-->
						<!--</div>-->
						
						
						
					</div>
				</div>
			</div>
		</div>
		<div class="clemfox__bg">
			<div class="service pt-80 pb-80">
				<div class="container">
					<div class="row">
						<div class="col-lg-6">
							<div class="sec-title sec-title__two mb-55">
								<h2 class="title wow fadeInUp" data-wow-delay=".3s" data-wow-duration="1500ms">
									Our Fleets
								</h2>
							</div>
						</div>
					</div>
					<div class="service__slide">
						<div class="service__item">
							<div class="service__img">
								<img src="{{ asset('ttwo/images/seedan.png') }}" alt="fleet cars">
							</div>
							<h3 class="service__title"><a href="javacript:void(0);">Sedan</a></h3>
							<ul class="service__list list-unstyled">
								<li>3 Peoples</li>
								<li>3 Luggages</li>
							</ul>
						</div>
						<div class="service__item">
							<div class="service__img">
								<img src="{{ asset('ttwo/images/suv.png') }}" alt="fleet cars">
							</div>
							<h3 class="service__title"><a href="javacript:void(0);">SUV</a></h3>
							<ul class="service__list list-unstyled">
								<li>6 Peoples</li>
								<li>6 Luggages</li>
							</ul>
						</div>
						<div class="service__item">
							<div class="service__img">
								<img src="{{ asset('ttwo/images/premium-suv.png') }}" alt="fleet cars">
							</div>
							<h3 class="service__title"><a href="javacript:void(0);">Premium SUV</a></h3>
							<ul class="service__list list-unstyled">
								<li>5 Peoples</li>
								<li>5 Luggages</li>
							</ul>
						</div>
						<div class="service__item">
							<div class="service__img">
								<img src="{{ asset('ttwo/images/sprinter.png') }}" alt="fleet cars">
							</div>
							<h3 class="service__title"><a href="javacript:void(0);">Sprinter</a></h3>
							<ul class="service__list list-unstyled">
								<li>12 Peoples</li>
								<li>12 Luggages</li>
							</ul>
						</div>
						<div class="service__item">
							<div class="service__img">
								<img src="{{ asset('ttwo/images/book-executive.png') }}">
							</div>
							<h3 class="service__title"><a href="javacript:void(0);">First Class</a></h3>
							<ul class="service__list list-unstyled">
								<li>2 Peoples</li>
								<li>2 Luggages</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<section id="clinox-service-feature" class="clinox-service-feature-section">
			<div class="container">
				<div class="clinox-service-feature-content">
					<div class="row">
						<div class="col-lg-6">
							<div
								class="clinox-service-feature-items serve1 headline pera-content ul-li-block position-relative">
								<div class="background_overlay"></div>
								<div class="clinox-service-feature-text position-relative">
									<h2 style="color:#fff">Airport Transfer Services</h2>
									<p>Experience top-tier airport transfer services with luxurious
										vehicles, expert chauffeurs, and round-the-clock availability.
										Whether for business or leisure, enjoy smooth, punctual, and
										comfortable rides designed to meet your specific needs. Our services
										extend to all major airports, including Toronto Pearson International
										Airport (YYZ), ensuring you arrive at your destination effortlessly
										and in style.</p>
									<ul>
										<li>On-time pickup at the terminal</li>
										<li>Pre-book or upon arrival options</li>
										<li>Professional chauffeur</li>
										<li>Luggage assistance</li>
										<li>Discounted flat rates</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div
								class="clinox-service-feature-items serve2 headline pera-content ul-li-block position-relative">
								<div class="background_overlay"></div>
								<div class="clinox-service-feature-text position-relative">
									<h2 style="color:#fff">Demo Theme Transfers Services</h2>
									<p>Travel in elegance with our Demo Theme transfer services. professional
										drivers, and luxurious vehicles, we ensure timely, travel in style
										for airport transfers, special events, or corporate needs
										effortlessly, comfortable, and stress-free transportation for every
										journey you take.</p>
									<ul>
										<li>Spacious, comfortable and stylish vehicle options including
											Premium SUV, Sprinter etc..</li>
										<li>Professionally dressed chauffeurs in suit and tie </li>
										<li>Accommodate 4 - 5 passengers plus luggage (depending on vehicle
											choice)</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<section id="clinox-service-offer" class="clinox-service-offer-section">
			<div class="container">
				<div class="clinox-section-title text-center headline pera-content">
					<span class="sub-title">Services Offer</span>
				</div>
				<div class="clinox-service-offer-content">
					<div class="row">
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									01
								</div>
								<div class="inner-text headline pera-content">
									<h3>Online Booking</h3>
									<p>Navigate to our user-friendly website at www.Demo Themepro.ca Our
										intuitive design ensures a hassle-free booking experience.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									02
								</div>
								<div class="inner-text headline pera-content">
									<h3>City Transport</h3>
									<p>Reliable city transport services in Ajax, Burlington, and
										Kitchener. Experience safe, punctual, and comfortable rides tailored
										to your travel needs, ensuring seamless connectivity across these
										vibrant cities.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									03
								</div>
								<div class="inner-text headline pera-content">
									<h3>Airport Transport</h3>
									<p>Our experienced drivers are not just skilled behind the wheel; they
										are also committed to providing a courteous and professional service
										moment you arrive.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									04
								</div>
								<div class="inner-text headline pera-content">
									<h3>Business Transport</h3>
									<p>Experience seamless Corporate Airport Demo Themeusine services for luxury
										business travel. Our professional chauffeurs ensure timely,
										comfortable, and stylish transportation, elevating your corporate
										journey to new heights.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									05
								</div>
								<div class="inner-text headline pera-content">
									<h3>Regular Transport</h3>
									<p>Set up your regular commuting routes by entering your daily pick-up
										and drop-off locations, preferred timings, and any specific
										preferences you may have.</p>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="clinox-service-offer-item d-flex">
								<div class="inner-serial d-flex align-items-center justify-content-center">
									06
								</div>
								<div class="inner-text headline pera-content">
									<h3>Tour Transport</h3>
									<p>Provide details about your tour, including the destination, date,
										and any specific stops or attractions you plan to visit perfect
										vehicle selection.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<script data-cfasync="false" src="js/email-decode.min.js"></script>
		<script src="js/platform.js" async></script>
		<div class="elfsight-app-b916b172-9c2c-4afe-a032-a85746e5d1bb" data-elfsight-app-lazy></div>
	</main>
	
	
@endsection
@section('script')
<script>

   
</script>
@endsection