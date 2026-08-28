@extends('layouts.app')

@section('content')

<style>
.job-search{
    max-width:526px;
}
.see-jobs-btn{
        background: #f9bf00;
    color: black;
    font-weight: 500;
    border-radius: 14px;
}
.badge{
    background: #f9bf00;
    color: black;#f2f2f2
}
.phone-mockup {
    width: 100%;
    max-width: 471px;
  height: 483px;
  background: #f2f2f2;
  border-radius: 40px;
  position: relative;
  overflow: hidden;
  padding: 20px;
}

.notify-card {
  display: flex;
  align-items: center;
  gap: 15px;
  color: #fff;
  padding: 10px;
  border-radius: 12px;
  margin: 25px auto;
  width: 90%;
  box-shadow: 0px 4px 12px rgba(0,0,0,0.3);
}

.notify-card i {
  font-size: 20px;
}

.notify-card.blue {
    border-right: 8px solid #f9bf00;
    background:white;
}

.notify-card h6 {
    color: #5c5861;
    font-size: 14px;
    margin-bottom: 4px;
}
.notify-card p {
  font-size: 12px;
}



.modal-header{
    background:#fff !important;
    margin: 12px 0 0 0;
}
.modal-body{
   background:#fff !important; 
}
.frm-sec {
    padding: 21px;
}
input#exampleInputEmail1 {
    border: 1px solid #bbbbbb;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
}
.close1 {
    background: none;
    position: absolute;
    right: 20px;
    font-size: 26px;
}
.close1:hover{
    color:orange;
}
.modal-content {
    background-color: #fefefe;
    margin: 83px 0 0 0 ;
    padding: 5px;
    border: 1px solid #888;
    width: 100%;
    max-width: 700px;
    position: relative;
}
    .modal-body h1 {
	 font-weight: 900;
	 font-size: 2.3em;
	 text-transform: uppercase;
}
 .modal-body a.pre-order-btn {
	 color: #000;
	 background-color: gold;
	 border-radius: 1em;
	 padding: 1em;
	 display: block;
	 margin: 2em auto;
	 width: 50%;
	 font-size: 1.25em;
	 font-weight: 6600;
}
 .modal-body a.pre-order-btn:hover {
	 background-color: #000;
	 text-decoration: none;
	 color: gold;
}
  @media (max-width: 768px) {
      
      #about{
          padding:0px !important;
      }
      .cs_cta.cs_style_1 .cs_section_title {
                font-size: 24px;
  }
  section .how{
      padding:0px !important;
  }
 
  .job-box .title-box{
      width:100% !important;
  }
  .job-search{
    max-width:265px !important;

}
.phone-mockup{
    height:600px;
}
}
  @media only screen 
  and (device-width: 390px) 
  and (device-height: 844px) 
  and (-webkit-device-pixel-ratio: 3) {
    .i_phone{
        margin-top:-100px !important;
        
    }  
 
</style>
    <!-- Slider -->
    <header class="header slider-fade">
        <div class="item bg-img" data-overlay-dark="1">
            <div class="v-middle caption">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mb-30">
                            <div class="v-middle text-center ">
                                <h2><span class="changing-word">TAXI</span> </h2>
                                <h2>SOFTWARE SOLUTION</h2>
                                <p>Automate and modernize your taxi fleet operations</p>
                                <p>with a white label cab app solution powered by</p>
                                <p>cutting-edge dispatch technology</p>
                                <div class="d-none d-md-block">
                                @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                                    <a href="pricing" class="cs_btn cs_style_2 mt-4 me-3">Get start for free&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
                                    <a style="cursor: pointer;" onclick="triggerCalendly();" class="cs_btn cs_style_2 mt-4">Book a demo&nbsp;<i class="fa-regular fa-headset"></i></a>
                                @else
                                    <a href="signup" class="cs_btn cs_style_2  mt-4">Get start for free&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
                                @endif
                                </div>
                            </div>
                            <div class="v-middle text-center two  ">
                                <img src="{{ asset('goride/img/slider/mobile_mockup_two.webp') }}" alt="mobile_mockup">
                               <div class="d-block d-md-none">
                                @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                                    <a href="pricing" class="cs_btn cs_style_2 mt-4 me-3">Get start for free&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
                                    <a style="cursor: pointer;" onclick="triggerCalendly();" class="cs_btn cs_style_2 mt-4">Book a demo&nbsp;<i class="fa-regular fa-headset"></i></a>
                                @else
                                    <a href="signup" class="cs_btn cs_style_2  mt-4">Get start for free&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- About -->
    <section class="about section-padding pt-5" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12  i_phone mb-md-30">
                    <div class="content" data-aos="fade-down" data-aos-duration="1000">
                        <div class="text-center text-md-start"><h1 class="section-subtitle">The Heartbeat of Mobility</h1>
                        </div>
                        <div class="section-title text-center text-md-start">Revolutionizing <span>Dispatch with AI-Powered Solutions</span></div>
                       <p class="mb-30 fw-normal" style="text-align: justify;">Welcome to GoRide, the leading AI-based dispatch system designed to optimize
                            your transportation business. Our innovative platform leverages artificial intelligence to
                            deliver efficient and reliable dispatch solutions tailored for taxis, ride-sharing services,
                            and fleet management. With GoRide, you can streamline operations, reduce costs, and enhance
                            customer satisfaction.</p>
                                    <div class="text-center text-md-start">
                        <a href="about" class=" button-4 mb-3 mb-md-0">Learn More About GoRide's Services <span class="ti-arrow-top-right"></span></a></div>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-1 col-md-12">
                    <div class="item " data-aos="fade-down" data-aos-duration="1000">  <img src="{{ asset('goride/img/about.webp') }}" class="img-fluid" alt="about">
                        <div class="curv-butn icon-bg">
                                       <img src="{{ asset('goride/img/g.png') }}" alt="Play Button" style="width: 50px; height: 50px;">
                            <div class="br-left-top">
                                <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="w-11 h-11">
                                    <path
                                        d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                        fill="#ffffff"></path>
                                </svg>
                            </div>
                            <div class="br-right-bottom">
                                <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                    class="w-11 h-11">
                                    <path
                                        d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                        fill="#ffffff"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
<section class="jobs mt-5">
  <div class="container">
    <div class="row">

      <!-- LEFT SIDE (Notification Cards) -->
    <div class="col-md-6 col-12 d-flex justify-content-center align-items-center">
        <div class="phone-mockup position-relative">

          <div class="notify-card blue aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
          <img src="https://www.goride.net.in/goride/img/bell.gif" style="
    height: 42px;
    width: 42px;
">
<div>
              <h6>Tambaram → Egmore <span class="badge  ms-3">ROUND TRIP</span></h6>
    <div class=" d-block d-md-flex gap-3">
              <p class="m-0 text-dark fw-bold"><strong class="text-danger fw-bold me-1">Pickup:</strong> 28 Aug 06:11 PM</p>
              <p class="m-0 text-dark fw-bold"><strong class="text-success fw-bold me-1">Return:</strong> 30 Aug 01:11 PM</p>
    </div>
            </div>
          </div>

          <div class="notify-card blue aos-init" data-aos="fade-left" data-aos-delay="100" data-aos-duration="1000">
         <img src="https://www.goride.net.in/goride/img/bell.gif" style="
    height: 42px;
    width: 42px;
">
            <div>
              <h6>Perambur → Thoothukudi <span class="badge  ms-3">ONE WAY</span></h6>
              <p class="m-0 text-dark fw-bold"><strong class="text-danger fw-bold me-1">Pickup:</strong> 29 Aug 11:09 AM</p>
            </div>
          </div>

          <div class="notify-card blue aos-init" data-aos="fade-right" data-aos-delay="200" data-aos-duration="1000">
           <img src="https://www.goride.net.in/goride/img/bell.gif" style="
    height: 42px;
    width: 42px;
">
         <div>
              <h6>Tambaram → Egmore <span class="badge  ms-3">ROUND TRIP</span></h6>
    <div class=" d-block d-md-flex gap-3">
              <p class="m-0 text-dark fw-bold"><strong class="text-danger fw-bold  me-1">Pickup:</strong> 28 Aug 06:11 PM</p>
              <p class="m-0 text-dark fw-bold"><strong class="text-success fw-bold me-1">Return:</strong> 30 Aug 01:11 PM</p>
    </div>
            </div>
          </div>
    
           <div class="notify-card blue aos-init" data-aos="fade-left" data-aos-delay="300" data-aos-duration="1000">
         <img src="https://www.goride.net.in/goride/img/bell.gif" style="
    height: 42px;
    width: 42px;
">
            <div>
              <h6>Perambur → Thoothukudi <span class="badge  ms-3">ONE WAY</span></h6>
              <p class="m-0 text-dark fw-bold"><strong class="text-danger fw-bold me-1">Pickup:</strong> 29 Aug 11:09 AM</p>
            </div>
          </div>
          <div class="d-flex justify-content-center align-items-center">
              <a href="https://www.goride.net.in/jobs" class="see-jobs-btn px-3 ">View Jobs</a>
            </div>
          
      
        </div>
      </div>

      <!-- RIGHT SIDE (Content + Button) -->
<div class="col-md-6 col-12 text-center d-flex flex-column justify-content-start align-items-center mt-4">
        <div class="section-title nimate__animated animate__fadeInDown"> <span class="my-0">One Platform for Rides &amp; Jobs</span>
</div>
        <p class="fw-semibold my-0">On our platform, passengers or verified drivers can post trip details, bid their price, compare fares, check ratings, and book rides instantly.</p>
        
        <div class="row d-flex justify-content-center align-items-center">
          <!-- Left Image -->
          <div class="col-md-6 col-12 text-center">
            <img src="https://www.goride.net.in/goride/img/mobile-app.jpg" alt="Job Search" class="img-fluid job-search" style="
    max-width: 526px;
    /* height: 366px; */
">
          </div>
             

          <!-- Right Text -->
          
        </div>

      </div>

    </div>
  </div>
</section>



    
    <!-- divider line -->
    <section class="features section-padding mt-5" id="features">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title"> <span>GoRide the Best AI Management Software</span></div>
                    <p class="text-center">Explore the Powerful Capabilities of GoRide</p>

                </div>
            </div>
            <div class="row justify-content-around">

                <div class="col-md-2 text-center mb-3">
                    <div class="mycard wallet" data-aos ="fade-up" data-aos-duration="1000">
                        <img src="{{ asset('goride/img/settings.png') }}" style="width:100px;" alt="settings">
                        <div class="overlay"></div>
                        <h3 class="big-para">AI-Powered Dispatching</h3>
                        <p class="text-center">Our intelligent dispatch system uses advanced algorithms to match drivers
                            with passengers in real time.</p>
                    </div>
                </div>

                <div class="col-md-2 text-center mb-3">
                    <div class="mycard wallet"  data-aos ="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <img src="{{ asset('goride/img/scalability.png') }}" style="width:100px;" alt="scalability">
                        <div class="overlay"></div>
                        <h3 class="big-para">Real-Time Fleet Tracking</h3>
                        <p class="text-center">Keep track of your vehicles and drivers with our live tracking feature.
                            Monitor your fleet's location, speed, and powerful </p>
                    </div>
                </div>

                <div class="col-md-2 text-center mb-3">
                    <div class="mycard wallet"  data-aos ="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        <img src="{{ asset('goride/img/globe (1).png') }}" style="width:100px;" alt="globe">
                        <div class="overlay"></div>
                        <h3 class="big-para">Dynamic Pricing & Demand Forecasting</h3>
                        <p class="text-center">Maximize your profits with GoRide's dynamic pricing and demand forecasting
                            tools. Our AI analyzes historical. </p>
                    </div>
                </div>

                <div class="col-md-2 text-center mb-3">
                    <div class="mycard wallet"  data-aos ="fade-up" data-aos-duration="1000" data-aos-delay="300">
                        <img src="{{ asset('goride/img/productivity.png') }}" style="width:100px;" alt="productivity">
                        <div class="overlay"></div>
                        <h3 class="big-para">Automated Reporting & Analytics</h3>
                        <p class="text-center">Gain valuable insights into your fleet's performance with GoRide's
                            comprehensive reporting and analytics tools. </p>
                    </div>
                </div>

                <div class="col-md-2 text-center mb-3">
                    <div class="mycard wallet"  data-aos ="fade-up" data-aos-duration="1000" data-aos-delay="400">
                        <img src="{{ asset('goride/img/trade.png') }}" style="width:100px;" alt="trade">
                        <div class="overlay"></div>
                        <h3 class="big-para">Customizable Solutions</h3>
                        <p class="text-center">Every business is unique, and so is our approach. GoRide offers customizable
                            solutions tailored to your specific needs,</p>
                    </div>
                </div>
                <!--<div class="col-md-12 text-center">-->
                <!--     <a href="#" class="button-4">Read More <span class="ti-arrow-top-right"></span></a>-->
                <!--</div>-->
            </div>
        </div>

    </section>


    <section class="tax-brand section-padding mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title text-center">Benefits of&nbsp;<span> GoRide</span></div>
                    <p class="text-center">Go Ride has been successfully serving taxi businesses and logistics
                        startups across the globe <br>to bring efficiency, automation, and scale to their operations.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 text-center text-md-start"  data-aos="fade-up" data-aos-duration="1000" >
                    <img src="{{ asset('goride/img/mobile-mockup.webp') }}" class="img-fluid" style="width:60%;" alt="mobile-mockup">
                </div>
                <div class="col-md-6" style="align-self: center;">
                    <ul>
                        <li class="demo-li fw-normal"  data-aos="fade-up" data-aos-duration="1000"><i class="fa-solid fa-check-double"></i><strong>Enhanced Efficiency:</strong>
                            "Reduce idle
                            time and fuel consumption with smart dispatching and routing."
                        </li>
                        <li class="demo-li fw-normal"  data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100" ><i class="fa-solid fa-check-double"></i><strong>Increased Revenue:</strong>
                            "Boost your earnings with dynamic pricing and optimized
                            fleet utilization."
                        </li>
                        <li class="demo-li fw-normal"  data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200"><i class="fa-solid fa-check-double"></i><strong>Improved Customer Experience:
                            </strong>"Deliver faster, more reliable
                            service with AI-powered dispatching."</li>
                        <li class="demo-li fw-normal"  data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300"><i class="fa-solid fa-check-double"></i><strong>Scalable Solutions:
                            </strong>"Grow your business with a platform that scales with
                            you, from a few vehicles to hundreds."</li>
                        <li class="demo-li fw-normal"  data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400"><i class="fa-solid fa-check-double"></i><strong>Actionable
                                Insights:</strong>"Make informed decisions with detailed analytics and
                            reporting."</li>
                    </ul>
                </div>
            </div>
        </div>

    </section>

    <section class="how">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title text-center">How it&nbsp;<span> Works</span></div>
                </div>
            </div>
            <div class="timeline">
                <div class="roadmap row">
                    <div class="step col-md-3 "  data-aos="fade-up" data-aos-duration="2s">
                        <div class="content yellow">
                            <h2>Step 1</h2>
                            <h3>Easy Setup</h3>
                            <p>Getting started with GoRide is simple. Our team will guide you through the setup process and
                                customize the platform to fit your needs.</p>
                        </div>
                    </div>
                    <div class="step col-md-3 "   >
                        <div class="content orange " data-aos="fade-up" data-aos-duration="2s" data-aos-delay="100">
                            <h2>Step 2</h2>
                            <h3>Smart Dispatch</h3>
                            <p>Our AI-driven algorithms assign the best driver for every ride, optimizing routes for time
                                and cost.</p>
                        </div>
                    </div>
                    <div class="step col-md-3 " data-aos="fade-up" data-aos-duration="2s" data-aos-delay="200" >
                        <div class="content red">
                            <h2>Step 3</h2>
                            <h3>Real-Time Monitoring</h3>
                            <p>Track your fleet in real time, ensuring efficient operations and quick response to any
                                issues.</p>
                        </div>
                    </div>
                    <div class="step col-md-3 " data-aos="fade-up" data-aos-duration="2s" data-aos-delay="300" >
                        <div class="content blue">
                            <h2>Step 4</h2>
                            <h3>Data-Driven Decisions</h3>
                            <p>Use our analytics tools to monitor performance, identify trends, and make informed decisions
                                to enhance your operations.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="running-taxi">
            <div class="taxi"></div>
            <div class="taxi-2"></div>
            <div class="taxi-3"></div>
        </div>
    </section>

    <!--<section class="how">-->
    <!--    <div class="container">-->
    <!--        <div class="row">-->
    <!--            <div class="col-md-12">-->
    <!--                <div class="section-title text-center">How it&nbsp;<span> Works</span></div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <div class="timeline">-->
    <!--            <div class="roadmap row">-->
    <!--                <div class="step col-md-3">-->
    <!--                    <div class="content yellow">-->
    <!--                        <h2>Step 1</h2>-->
    <!--                        <h3>Easy Setup</h3>-->
    <!--                        <p>Getting started with GoRide is simple. Our team will guide you through the setup process and customize the platform to fit your needs.</p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="step col-md-3">-->
    <!--                    <div class="content orange">-->
    <!--                        <h2>Step 2</h2>-->
    <!--                        <h3>Smart Dispatch</h3>-->
    <!--                        <p>Our AI-driven algorithms assign the best driver for every ride, optimizing routes for time and cost.</p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="step col-md-3">-->
    <!--                    <div class="content red">-->
    <!--                        <h2>Step 3</h2>-->
    <!--                        <h3>Real-Time Monitoring</h3>-->
    <!--                        <p>Track your fleet in real time, ensuring efficient operations and quick response to any issues.</p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="step col-md-3">-->
    <!--                    <div class="content blue">-->
    <!--                        <h2>Step 4</h2>-->
    <!--                        <h3>Data-Driven Decisions</h3>-->
    <!--                        <p>Use our analytics tools to monitor performance, identify trends, and make informed decisions to enhance your operations.</p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <div class="running-taxi">-->
    <!--        <div class="taxi"></div>-->
    <!--        <div class="taxi-2"></div>-->
    <!--        <div class="taxi-3"></div>-->
    <!--    </div>-->
    <!--</section>-->

    <!-- Our Clients -->
    <section class="myClients section-padding mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center mb-30">
                    <!--<div class="section-subtitle">Testimonials</div>-->
                    <div class="section-title">Trusted by&nbsp;<span> 1000+&nbsp;</span>Businesses</div>
                </div>
                <div class="col-md-12">
                    <div class="owl-carousel owl-theme">
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                                <img src="{{ asset('goride/img/client/airport_uk.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                                <img src="{{ asset('goride/img/client/epsom.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/essex.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/london_airport.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/prestiage.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/prestige_airport.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/skylimo.png')}}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/sutton_airports.png')}}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/tequilimos.png')}}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/travel_bird.png') }}" alt="client">
                            </a>
                        </div>
                        <div class="item bg-transparent">
                            <a href="/our-clients" target="_blank">
                            <img src="{{ asset('goride/img/client/airport_rides.png') }}" alt="client">
                            </a>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-12 row">-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_1.png') }}" alt="client">-->
                <!--    </div>-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_2.png') }}" alt="client">-->
                <!--    </div>-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_3.png') }}" alt="client">-->
                <!--    </div>-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_4.png') }}" alt="client">-->
                <!--    </div>-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_5.png') }}" alt="client">-->
                <!--    </div>-->
                <!--    <div class="col-md-2">-->
                <!--        <img src="{{ asset('goride/img/client/client_6.png') }}" alt="client">-->
                <!--    </div>-->
                <!--</div>-->
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials section-padding mt-15">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center mb-30">
                    <div class="section-subtitle fw-sm-bold fs-sm-5">Testimonials</div>
                    <div class="section-title">What Clients Say</div>
                </div>
                <div class="col-md-12">
                    <div class="owl-carousel owl-theme">
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                               
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text ">
                                <p class="fw-normal">Sri Hema Infotech's incredible customer support made integrating the Dispatch Taxi System seamless, ensuring efficiency and reliability from day one, boosting both our operations and customer satisfaction.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/testimonial-01.webp') }}" alt="team">
                                    </div>
                                   
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Kandasamy Sivabalan</h6>
                                    <p>CEO</p>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                               
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text">
                                <p class="fw-normal">The Dispatch Taxi System simplifies operations, offering an intuitive platform, real-time updates, and improved efficiency, making it indispensable for any taxi service aiming for reliability and enhanced experiences.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/testimonial-02.webp') }}" alt="team">
                                    </div>
                                 
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Kantha</h6>
                                    <p>Founder</p>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                              
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text">
                                <p class="fw-normal">Using Sri Hema Infotech's Dispatch Taxi System, our operations transformed with real-time GPS tracking & dispatching, resulting in streamlined services and higher efficiency for both drivers and passengers.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/som.png') }}" alt="team">
                                    </div>
                                
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Ravi Sellathurrai</h6>
                                    <p>CEO</p>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                               
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text">
                                <p class="fw-normal">The system provides easy setup, real-time tracking, and automatic dispatch for faster ride allocation. Though efficient, adding missing features would enhance its value, making it even better for taxi services.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/testimonial-04.webp') }}" alt="team">
                                    </div>
                                   
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Aj Arul</h6>
                                    <p>Managing Director</p>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                               
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text">
                                <p class="fw-normal">Go Ride simplifies operations with an easy-to-use platform, reducing wait times and boosting efficiency. It's a game-changing solution for taxi services focused on improving customer satisfaction and streamlining processes.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/testimonial-05.webp') }}" alt="team">
                                    </div>
                                    
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Thinesh</h6>
                                    <p>Executive Director</p>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="stars"> <span class="rate">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </span>
                               
                                <div class="shap-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div> <i class="fa-solid fa-quote-left"></i>
                            <div class="text">
                                <p class="fw-normal">Sri Hema Infotech’s system brought significant improvements to Go Ride, offering real-time tracking and effortless bookings, improving experiences for passengers and drivers alike while boosting operational efficiency.</p>
                            </div>
                            <div class="info mt-30">
                                <div class="img-curv">
                                    <div class="img"> <img src="{{ asset('goride/img/testimonial-06.webp') }}" alt="team">
                                    </div>
                                   
                                    <div class="shap-right-bottom">
                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"
                                            class="w-11 h-11">
                                            <path
                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                                fill="#ffffff"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-30">
                                    <h6>Naeem</h6>
                                    <p>Founder</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- divider line -->

    <section class="upgrade mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="cs_cta cs_style_1 text-center position-relative"  data-aos="fade-up" data-aos-duration="1000">
                        <h2 class="cs_section_title">Ready to Revolutionize Your Fleet?
                        </h2>
                        <p class="cs_section_subtitle mb-4">Join hundreds of satisfied customers who trust GoRide for their
                            dispatch needs. <br>Contact us today to learn how our AI-powered solutions can take your
                            business to the next level.
                        </p>
                        <a href="signup" class="cs_btn cs_style_2">Get start&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="api_inter_section">
        <div class="container">
            <div class="mcb-wrap-inner">
                <div class="product-like-reputed-image service-image gape">
                    <img src="{{ asset('goride/img/api_intergation_images.webp') }}" alt="api_intergration_image">
                </div>
                <div class="product-like-reputed-content"  data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <h4>100+ API Integrations For More Efficient Business Operations</h4>
                    <p class="bottom-gap">GoRide offers 100+ API integrations to help you connect multiple stakeholders and enhance your business efficiency</p>
                    <div style="
    display: flex;
    justify-content: center;
"><a style="margin-left: 0px;" href="#" class="cs_btn cs_style_2">
                        <span class="button_label">Explore App Marketplace</span>
                    </a></div>
                </div>
            </div>
        </div>
    </section>
    
    <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close1" data-bs-dismiss="modal"><i class="fa fa-close" style="width: 30px; height: 30px;border-radius: 50%; border: 1px solid #000;"></i></button>
        <!--         <h4 class="modal-title">Modal Header</h4> -->
      </div>
      <div class="modal-body text-center mt-4">
        <img src="https://goride.run/goride/img/logo-dark.png" class="logo-img" alt="" style="width: 200px;">
       <form class="frm-sec">
  <div class="mb-3">
    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Login with Mobile Number">

  </div>
 <button type="submit" class="btn btn-primary mb-3" style="width: 100%;">Get OTP</button>
<span class="by-click-text ">Already have an account? <a class="by-click-text under-line text-danger " href="login" contenteditable="false" style="cursor: pointer;"> Sign In
                                        </a>
                                    </span>
</form>
      </div>
      <div class="modal-footer">
        <!--         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
      </div>
    </div>

  </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js" integrity="sha512-Ysw1DcK1P+uYLqprEAzNQJP+J4hTx4t/3X2nbVwszao8wD+9afLjBQYjz7Uk4ADP+Er++mJoScI42ueGtQOzEA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.css" integrity="sha512-rd0qOHVMOcez6pLWPVFIv7EfSdGKLt+eafXh4RO/12Fgr41hDQxfGvoi1Vy55QIVcQEujUE1LQrATCLl2Fs+ag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
<script>


  var swiper = new Swiper(".myCardSwiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  autoplay: {
    delay: 3000,
  },
});


const swiperContainer = document.querySelector('.myCardSwiper');

swiperContainer.addEventListener('mouseenter', () => verticalSwiper.autoplay.stop());
swiperContainer.addEventListener('mouseleave', () => verticalSwiper.autoplay.start());


triggerCalendly = () => {
    sessionStorage.setItem('triggerCalendlyClick', 'true');
    window.location.href = '/dashboard';
}

//     $(document).ready(function(){       
//   $('#myModal').modal('show');
//     }); 
</script>



    {{-- <div class="container">
        <h1>Welcome to Our Website</h1>
        <p>This is the home page of our Laravel application. Here you can add content that is specific to the home page.</p>

        <div class="row">
            <div class="col-md-6">
                <h2>About Us</h2>
                <p>Learn more about our mission, values, and team.</p>
            </div>
            <div class="col-md-6">
                <h2>Contact Us</h2>
                <p>Get in touch with us for any inquiries or support.</p>
            </div>
        </div>
    </div> --}}
@endsection

