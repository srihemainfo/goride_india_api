@extends('layouts.app')

@section('content')

<style>
  .trial-banner {

    color: white;

    display: flex
;
    justify-content: center;
    align-items: center;
    background:#f3ba00ba;

}
 @media (max-width: 768px) {
         .app-ride img{
             width: 88px !important;
         }
    }
    

.free-plan-img

 {
    max-width: 300px;
    height: 300px;
}
 .blink {
    animation: blinkColor 1s infinite;
  }
.signupBtn {
  background: #f3ba00;
    width: 134px;
    height: 48px;
    border: none;
    display: flex
;
    align-items: center;
    justify-content: flex-start;
    padding-left: 29px;
    border-radius: 23px;
    gap: 9px;
    color: white;
    /* background: linear-gradient(to right, #a173f9, #7c51d7, #ba6eff); */
    color: black;
    position: relative;
    cursor: pointer;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.212);
    font-weight: 600;
}

.arrow {
position: absolute;
    right: 7.5px;
    background-color:#f3ba00;
    width: 30px;
    height: 30px;
    display: flex
;
    align-items: center;
    justify-content: center;
    border-radius: 30px;
    transition: all .5s ease;}


.signupBtn:hover .arrow {
  width: calc(120px - (7.5px)*2);
}
@keyframes blinkColor {
    0%   { color: red; }
    50%  { color: green; }
    100% { color: red; }
  }
  .app-ride{
      background-color: #000;
      padding: 10px;
      display: inline-block; 
      border-radius: 8px;
  }
   
</style>

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>GoRide My Passenger App</h4>
                <h1>Experience Convenient Rides <br> with <span>Our My Passenger App!</span></h1>
                <p>All the tools you need to book rides effortlessly and <br> enjoy a smooth travel experience!</p>
            </div>
        </div>
    </section>
    
    <section class="about-section mt-5">
        <div class="container">
          <div class="row align-items-center d-flex justify-content-center ">
  <div class="col-md-12 mb-4 mb-md-0">
    <div class="section-heading mb-40">
      <h4 class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;"> <span></span>GoRide Driver App</h4>
      <h2 class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;">Empowering Drivers with AI Precision</h2>
      <p class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;">
        At GoRide, we believe in empowering drivers with the tools they need to succeed. Our AI-based dispatch software offers a powerful and intuitive Driver App, specifically designed to enhance the efficiency and earnings of taxi drivers. Whether you’re an independent driver or part of a larger fleet, the GoRide Driver App is your key to a seamless driving experience.
      </p>
    </div>
  </div>

<!--  <div class="col-md-3">-->
<!--    <div class="mb-5 text-center">-->
<!--      <h3 class="widget-title">Try our Driver App</h3>-->
<!--      <a href="https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&amp;pcampaignid=web_share" target="_blank">-->
<!--  <div style="background-color: #000; padding: 10px; display: inline-block; border-radius: 8px;">-->
<!--    <img src="https://www.goride.net.in/goride/img/paly-store-logo.png" alt="Google Play" style="width:160px;">-->
<!--  </div>-->
<!--</a>-->


<!--    </div>-->
<!--  </div>-->
</div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-heading text-center mb-40">
                        <h2 class="wow fadeIn" data-wow-duration="2s">Why Choose the GoRide My Passenger App?</h2>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s">
                    <div class="key_features_card">
                        <h4>Quick and Easy Bookings</h4>
                        <p>Request a ride in seconds! Enter your destination, choose your ride type, and let our AI-based system match you with the nearest available driver.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                    <div class="key_features_card">
                        <h4>Real-Time Tracking</h4>
                        <p>Stay updated on your ride’s progress. Track your driver in real-time, view their estimated time of arrival, and receive instant notifications about your ride status.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>AI-Optimized Dispatching</h4>
                        <p>Our intelligent dispatch software utilizes advanced algorithms to ensure that the closest driver is assigned to you, minimizing wait times and enhancing efficiency.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s">
                    <div class="key_features_card">
                        <h4>Multiple Payment Options</h4>
                        <p>Pay your way! Choose from a variety of payment methods, including credit/debit cards, mobile wallets, or cash. Secure, convenient, and flexible.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                    <div class="key_features_card">
                        <h4>Ride History and Receipts</h4>
                        <p>Keep track of all your rides in one place. Access past trip details and receipts directly from the app for easy reference and expense management.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>Safety First</h4>
                        <p>Your safety is our priority. Share your trip details with family and friends, view driver profiles and ratings, and access 24/7 support for any assistance.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>Fare Estimates</h4>
                        <p>Know your fare before you ride. Get an upfront fare estimate based on your destination and ride type, so you can travel with peace of mind.</p>
                    </div>
                </div>
                <div class="col-md-3 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>Scheduled Rides</h4>
                        <p>Plan your rides in advance with our scheduling feature. Set your pickup time and location, and we’ll ensure a driver is ready when you are.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="how-app-work-section" id="how-it-works">
    	<div class="container">
    		<div class="row">
    			<div class="col-md-6">
    				<div class="how-app-work-content-wrap">
    					<div class="title wow fadeIn" data-wow-duration="2s">
    						<h3>Get Started with GoRide My Passenger App</h3>
    					</div>
    					<div class="how-app-work-content" id="how-app-work-slider-pager">
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-download"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Download the GoRide My Passenger App</h4>
        								<p>Available on iOS and Android, our app is designed to be intuitive and easy to use.</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
    						    <div class="single-how-app-work">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-user-plus"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Sign Up and Set Up Your Profile</h4>
        								<p>Create an account, add your payment details, and you’re ready to go!</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-taxi"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Request Your Ride</h4>
        								<p>Open the app, enter your destination, and choose your ride type.</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-map-marker-alt"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Track Your Driver</h4>
        								<p>Get real-time updates on your driver’s location and estimated time of arrival.</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-smile"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Enjoy Your Ride</h4>
        								<p>Sit back, relax, and enjoy the journey. Rate your driver and leave feedback to help us improve.</p>
        							</div>
        						</div>
    						</a>
    					</div>
    					<!-- Links -->
    					<!--<a href="https://apps.apple.com/in/app/my-passenger/id6744351099" target="_blank" class="download-btn wow fadeIn" data-wow-duration="2s">-->
    					<!--	<i class="fab fa-apple"></i>-->
    					<!--	<span class="inner"> <span class="avail">Available on</span> <span class="store-name">App Store</span></span>-->
    					<!--</a>-->
    					<a href="https://play.google.com/store/apps/details?id=com.shi.myPassenger" target="_blank" class="download-btn wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
    						<i class="fab fa-google-play"></i>
    						<span class="inner"><span class="avail">Available on</span> <span class="store-name">Google play</span></span>
    					</a>
    				</div>
    			</div>
    			<div class="col-md-6 how-app-work-slider-content d-flex align-items-center wow fadeIn" data-wow-duration="2s">
    			    <img src="{{ asset('goride/img/passenger-app-mockup.webp') }}">
    			</div>
    		</div>
    	</div>
    </section>
    
<!--     <section class="trial-banner py-5" >-->
<!--  <div class="container">-->
<!--    <div class="row align-items-center">-->

      <!-- Left Image -->
<!--      <div class="col-12 col-md-5 text-center mb-4 mb-md-0">-->
<!--        <img src="https://www.goride.net.in/goride/img/free-plan.png" alt="Free Plan" class="img-fluid free-plan-img" style="max-width: 300px;">-->
<!--      </div>-->

      <!-- Right Content -->
<!--      <div class="col-12 col-md-7">-->
<!--    <h2 class="fw-bold text-center mb-2 text-dark">-->
<!--  Smarter business starts with Go Ride-->
<!--</h2>-->
<!--<p class="text-center fw-bold fs-5" style="color:black;">-->
<!--  Enjoy a <span class="blink fw-bold" style="color: beige;">1-month free trial</span> and take your business to the next level-->
<!--</p>-->


    

        <!-- Try Free Button -->
<!--        <div class="text-center mt-4">-->
<!--          <a href="/pricing" class="text-decoration-none">-->
<!--            <button class="signupBtn bg-dark text-white" >-->
<!--              Try Now-->
<!--              <span class="arrow">-->
<!--               <svg fill="black" viewBox="0 0 320 512" height="1em" xmlns="http://www.w3.org/2000/svg">-->
<!--                  <path d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 -->
<!--                  160c-12.5 12.5-32.8 12.5-45.3 -->
<!--                  0s-12.5-32.8 0-45.3L210.7 -->
<!--                  256 73.4 118.6c-12.5-12.5-12.5-32.8 -->
<!--                  0-45.3s32.8-12.5 45.3 -->
<!--                  0l160 160z"></path>-->
<!--                </svg>-->
<!--              </span>-->
<!--            </button>-->
<!--          </a>-->
<!--        </div>-->
<!--      </div>-->

<!--    </div>-->
<!--  </div>-->
<!--</section>-->
    
@endsection

@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        var wow = new WOW( {
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       0,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
            callback:     function(box) {
              // the callback is fired every time an animation is started
              // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null // optional scroll container selector, otherwise use window
          }
        );
        wow.init();
    </script>
    
@endsection