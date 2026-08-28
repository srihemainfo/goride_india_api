@extends('layouts.app')

@section('content')

<style>
 
 
 
 .trial-banner {
/*background: url(/goride/img/offer-bg.jpg) no-repeat center center;*/
/*    background-size: cover;*/
/*    background-repeat: no-repeat;*/
/*    background-position: center;*/
    color: white;
    /*min-height: 400px;*/
    /*height: 70vh;*/
    display: flex
;
    justify-content: center;
    align-items: center;
    background:#f3ba00ba;
/*background: linear-gradient(to right top, #c0c0c0, #3a7bd5);*/
}
.free-plan-img

 {
    max-width: 300px;
    height: 300px;
}

/*.plan-features {*/
/*  background: rgba(0, 0, 0, 0.6);*/
/*  border-left: 4px solid #ffc107;*/
/*  max-width: 400px;*/
/*}*/

.tick-icon {
  color: #00d084;
  font-size: 1.3rem;
}

.trial-btn {
  background-color: #ffc107;
  color: black;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.trial-btn:hover {
  background-color: #ffb300;
  transform: scale(1.05);
}

 
.trial-badge {
  background: #fff;
  color: #2c3e50;
  border-radius: 50%;
  width: 200px;
  height: 200px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  font-family: 'Segoe UI', sans-serif;
}

.trial-days {
  font-size: 1.2rem;
  font-weight: 700;
  color: #e74c3c;
}

.trial-text {
  font-size: 1.6rem;
  color: #2980b9;
}

.trial-subtext {
  font-size: 0.85rem;
  color: #7f8c8d;
}

.brand-name {
  font-weight: 800;
  color: #fff;
}
.trial-line {
     padding: 9px;
    background: linear-gradient(to right, #ffc107, #ffb300);
    border-radius: 20px;
    margin: 0 auto;
    color: black;
    font-weight: 700;
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
.list-unstyled li{
    font-weight:700;
    font-size:23px;
}
.tick-line {
  font-size: 1.25rem; /* Increase this value for larger text */
  font-weight: 500;   /* Optional: for semi-bold text */
}
 .blink {
    animation: blinkColor 1s infinite;
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
    @media (max-width: 768px) {
         .app-ride img{
             width: 88px !important;
         }
    }
</style>

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>GoRide Driver App</h4>
                <h1>Boost Your Productivity <br> with <span>Our Driver App!</span></h1>
                <p>All the features you need to manage rides efficiently and <br> enhance your driving experience!</p>
            </div>
        </div>
    </section>
    
    <section class="about-section padding">
        <div class="container">
         <div class="row align-items-center d-flex justify-content-center ">
  <div class="col-md-9 mb-4 mb-md-0">
    <div class="section-heading mb-40">
      <h4 class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;"> <span></span>GoRide Driver App</h4>
      <h2 class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;">Empowering Drivers with AI Precision</h2>
      <p class="wow fadeIn" data-wow-duration="2s" style="visibility: visible; animation-duration: 2s; animation-name: fadeIn;">
        At GoRide, we believe in empowering drivers with the tools they need to succeed. Our AI-based dispatch software offers a powerful and intuitive Driver App, specifically designed to enhance the efficiency and earnings of taxi drivers. Whether you’re an independent driver or part of a larger fleet, the GoRide Driver App is your key to a seamless driving experience.
      </p>
    </div>
  </div>

  <div class="col-md-3">
    <div class="mb-5 text-center">
      <h3 class="widget-title">Try our Driver App</h3>
      <a href="https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&amp;pcampaignid=web_share" target="_blank">
  <div class="app-ride" >
    <img src="https://www.goride.net.in/goride/img/paly-store-logo.png" alt="Google Play" style="width:160px;">
  </div>
</a>


    </div>
  </div>
</div>
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-heading text-center mb-40">
                        <h2 class="wow fadeIn" data-wow-duration="2s">Why Choose the GoRide Driver App?</h2>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s">
                    <div class="key_features_card">
                        <h4>AI-Powered Dispatching</h4>
                        <p>Our advanced AI algorithms ensure that you receive the most optimized jobs based on your location, availability, and driving patterns. With GoRide, you spend less time waiting and more time earning.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                    <div class="key_features_card">
                        <h4>Real-Time Navigation</h4>
                        <p>Integrated with cutting-edge GPS technology, the GoRide Driver App provides real-time navigation and traffic updates. This means fewer delays and quicker routes, helping you provide a better service to your passengers.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>Flexible Scheduling</h4>
                        <p>With our flexible scheduling feature, you can choose when you want to work. Go online and offline with just a tap, allowing you to manage your time and balance work with your personal life.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s">
                    <div class="key_features_card">
                        <h4>Instant Bookings and Alerts</h4>
                        <p>Receive instant alerts for new ride requests, cancellations, and any changes in your schedule. With GoRide, you are always in the loop, ensuring that you never miss an opportunity.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                    <div class="key_features_card">
                        <h4>Secure and Transparent Payments</h4>
                        <p>Say goodbye to payment hassles. GoRide’s Driver App provides secure, transparent, and quick payment processing. Track your earnings in real time and receive detailed summaries of all your rides.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex my-1 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                    <div class="key_features_card">
                        <h4>Driver Support and Community</h4>
                        <p>At GoRide, we care about our drivers. Our dedicated support team is available 24/7 to assist you with any issues or questions. Plus, connect with other drivers through our app community for tips, advice, and camaraderie.</p>
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
    						<h3>Get Started with GoRide Driver App</h3>
    					</div>
    					<div class="how-app-work-content" id="how-app-work-slider-pager">
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-user-plus"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Sign Up</h4>
        								<p>Download the GoRide Driver App from the App Store or Google Play, and sign up with your details.</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
    						    <div class="single-how-app-work">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-check-circle"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Get Verified</h4>
        								<p>Complete a simple verification process to ensure safety and security for all users.</p>
        							</div>
        						</div>
    						</a>
    						<a href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="1500ms">
    						    <div class="single-how-app-work ">
        							<div class="icon-box">
        								<div class="inner">
        									<i class="fas fa-car"></i>
        								</div>
        							</div>
        							<div class="text-box">
        								<h4>Start Driving</h4>
        								<p>Once verified, you’re ready to hit the road. Go online, accept rides, and start earning!</p>
        							</div>
        						</div>
    						</a>
    					</div>
    					<!-- Links -->
    					<a href="#" class="download-btn wow fadeIn" data-wow-duration="2s">
    						<i class="fab fa-apple"></i>
    						<span class="inner"> <span class="avail">Available on</span> <span class="store-name">App Store</span></span>
    					</a>
    					<a href="https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&pcampaignid=web_share" target="_blank" class="download-btn wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
    						<i class="fab fa-google-play"></i>
    						<span class="inner"><span class="avail">Available on</span> <span class="store-name">Google play</span></span>
    					</a>
    				</div>
    			</div>
    			<div class="col-md-6 how-app-work-slider-content d-flex align-items-center wow fadeIn" data-wow-duration="2s">
    			    <img src="{{ asset('goride/img/driver-app-mockup.webp') }}">
    			</div>
    		</div>
    	</div>
    </section>
    
<section class="trial-banner py-5" >
  <div class="container">
    <div class="row align-items-center">

      <!-- Left Image -->
      <div class="col-12 col-md-5 text-center mb-4 mb-md-0">
        <img src="https://www.goride.net.in/goride/img/free-plan.png" alt="Free Plan" class="img-fluid free-plan-img" style="max-width: 300px;">
      </div>

      <!-- Right Content -->
      <div class="col-12 col-md-7">
    <h2 class="fw-bold text-center mb-2 text-dark">
  Smarter business starts with Go Ride
</h2>
<p class="text-center fw-bold fs-5" style="color:black;">
  Enjoy a <span class="blink fw-bold" style="color: beige;">1-month free trial</span> and take your business to the next level
</p>


    

        <!-- Try Free Button -->
        <div class="text-center mt-4">
          <a href="/pricing" class="text-decoration-none">
            <button class="signupBtn bg-dark text-white" >
              Try Now
              <span class="arrow">
               <svg fill="black" viewBox="0 0 320 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                  <path d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 
                  160c-12.5 12.5-32.8 12.5-45.3 
                  0s-12.5-32.8 0-45.3L210.7 
                  256 73.4 118.6c-12.5-12.5-12.5-32.8 
                  0-45.3s32.8-12.5 45.3 
                  0l160 160z"></path>
                </svg>
              </span>
            </button>
          </a>
        </div>
      </div>

    </div>
  </div>
</section>



    
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