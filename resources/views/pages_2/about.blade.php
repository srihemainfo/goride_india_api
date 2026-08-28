@extends('layouts.app')

@section('content')

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>About Us!</h4>
                <h1>Feel your journey <br> with <span>GoRide!</span></h1>
                <p>Everything your taxi business <br>needs is already here! </p>
            </div>
        </div>
    </section>
    
    <section class="about-section padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-sm-6 wow slideInLeft" data-wow-duration="2s">
                    <div class="about-img">
                        <img class="about-img1" src="{{ asset('goride/img/about-1.webp') }}" alt="img">
                        <img class="about-img2" src="{{ asset('goride/img/about-2.webp') }}" alt="img">
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 wow slideInRight" data-wow-duration="2s">
                    <div class="section-heading mb-40">
                        <h4 class="wow fadeIn" data-wow-duration="2s"><span></span>About Us</h4>
                        <h2 class="wow fadeIn" data-wow-duration="2s" data-wow-delay="2000ms">Unifying Global Expertise for Next-Gen Taxi Dispatching</h2>
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="2500ms">Welcome to Go Ride, a groundbreaking venture that combines the expertise of two industry leaders from opposite sides of the globe—Airport Rides Ltd, a premier taxi service provider in Canada, and Goride Run Private Limited, a cutting-edge software company based in India. Together, we are revolutionizing the way taxi services are managed and delivered through our state-of-the-art taxi dispatch software.</p>
                    </div>
                    <!--/.section-heading-->
                    <ul class="about-info wow fadeIn" data-wow-duration="2s" data-wow-delay="3000ms">
                        <li class="justify-content-center">
                            <img src="{{ asset('goride/img//logo-dark.png') }}" alt="logo" style="width: 55%;">
                            <!--<h2><span>Get in Touch</span><a href="tel:9884557004" contenteditable="false" style="cursor: pointer;">+91 98845 57004</a></h2>-->
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <section>
        <div class="container mb-5">
            <div class="row">
                <div class="col-lg-6 wow fadeIn" data-wow-duration="2s">
                    <div class="section-heading mb-40">
                        <h4 class="wow fadeIn" data-wow-duration="2s"><span></span>Our Story</h4>
                        <h2 class="wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">Crafting the Future of Taxi Services Together</h2>
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">The journey of Go Ride began with a shared vision: to create a seamless and efficient taxi dispatch system that addresses the evolving needs of modern transportation. Airport Rides Ltd has a long-standing reputation for providing reliable and customer-centric taxi services across Canada, while Goride Run Private Limited is known for its innovative software solutions that empower businesses worldwide.</p>
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="1500ms">Recognizing the potential to make a significant impact in the transportation industry, these two powerhouses decided to join forces. The result is Go Ride, a unique platform that integrates deep industry knowledge with advanced technology to offer taxi companies a robust, user-friendly dispatch solution.</p>
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center wow fadeIn" data-wow-duration="2s">
                    <figure class="about-feature-img">
                        <img src="{{ asset('goride/img/go_ride_logo.png') }}" alt="">
                        <div class="img-lg-line">
                        </div>
                    </figure>
                </div>
            </div>
        </div>
    </section>
    
    <section>
        <div class="ws-about mt-5">
            <div class="row no-gutters">
                <div class="col-lg-6 wow slideInLeft" data-wow-duration="2s">
                    <div class="ws-img-bg">
                    </div>
                </div>
                <div class="col-lg-6 wow slideInRight" data-wow-duration="2s">
                    <div class="ws-right-layer">
                        <div class="ws-right-content">
                            <!--<h5 class="subtitle-alt">Shaping the Future of Taxi Dispatch</h5>-->
                            <!--<h2>Empowering Global Mobility Through Innovation</h2>-->
                            <!--<p>At Go Rides, we are dedicated to shaping the future of taxi dispatch with innovative solutions that redefine global mobility. Our vision is to lead this transformation by developing smart, scalable, and intuitive software that bridges the gap between service providers and customers.</p>-->
                            <div class="row ws-box-layer-alt">
                                <div class="col-md-6 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                                    <div class="ws-box-alt">
                                        <figure class="ha-icon">
                                            <img src="{{ asset('goride/img/our-vision.png') }}" alt="our-vision">
                                        </figure>
                                        <h4>Our <span>Vision</span></h4>
                                        <p>At Go Ride, our vision is to lead the transformation of the global taxi industry by offering smart, scalable, and intuitive dispatch software.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                                    <div class="ws-box-alt">
                                        <figure class="ha-icon">
                                            <img src="{{ asset('goride/img/our-mission.png') }}" alt="our-mission">
                                        </figure>
                                        <h4>Our <span>Mission</span></h4>
                                        <p>Our mission is to deliver cutting-edge dispatch software that enhances the operational efficiency of taxi companies, improves customer satisfaction, and drives growth.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="testimonial-section bg-grey">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 wow fadeIn" data-wow-duration="2s">
                    <div class="section-heading mb-10">
                        <h4 class="wow fadeIn" data-wow-duration="2s"><span></span>Our Future</h4>
                        <h2 class="wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">Go Ride: Innovation Through Partnership</h2>
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">As we continue to innovate and expand, Go Ride is poised to become the preferred choice for taxi dispatch software worldwide. We are committed to continuous improvement, adapting to the changing needs of the industry, and helping our clients thrive in a competitive market.</p>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-duration="2s">
                    <div class="feature-wrap">
                        <div class="section-heading mb-30 wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <!--<h4 class="white"><span></span>Why Choose Go Ride?</h4>-->
                            <h2 class="white">Why Choose Go Ride?</h2>
                            <p class="white">Where Tradition Meets Innovation in Taxi Dispatch.</p>
                        </div>
                        <ul class="ridek-features wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">
                            <li>
                                <div class="feature-icon">
                                    <i class="fa-light fa-briefcase"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Industry Expertise:</h3>
                                    <p>Benefit from the combined experience of a leading Canadian taxi service and a top Indian software developer.</p>
                                </div>
                            </li>
                            <li>
                                <div class="feature-icon">
                                    <i class="fa-light fa-microchip"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Innovative Technology:</h3>
                                    <p>Our dispatch software is designed with the latest technology to ensure reliability, scalability, and ease of use.</p>
                                </div>
                            </li>
                            <li>
                                <div class="feature-icon">
                                    <i class="fa-light fa-globe"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Global Reach, Local Impact:</h3>
                                    <p>While our roots are in Canada and India, our solutions are designed to meet the needs of taxi companies globally.</p>
                                </div>
                            </li>
                            <li>
                                <div class="feature-icon">
                                    <i class="fa-light fa-user"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Customer-Centric Approach:</h3>
                                    <p>We understand the challenges taxi companies face and are dedicated to providing a solution that enhances both operator efficiency and customer experience.</p>
                                </div>
                            </li>
                        </ul>
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
