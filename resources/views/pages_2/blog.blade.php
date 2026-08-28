@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('goride/css/custom-plan-style.css') }}">
<link rel="stylesheet" href="{{ asset('goride/css/custom-plan-flaticon.css') }}">
<script src="{{ asset('goride/js/custom-plan-main.js') }}"></script>
<style>
    .default-animation img {
        width: 21px;
        height: 21px;
    }
    .breadcrumb{
        font-weight: 700;
    font-size: 18px;
}
  .pricing-section {
        padding: 50px 0;
    }

    a.thm-btn.borderd {
        cursor: pointer;
    }

    .sec-title h2 {
        font-size: 32px;
        color: #170B35;
        font-weight: 600;
    }

    .sec-title p {
        font-size: 20px;
        line-height: 26px;
        color: #656565;
        margin-top: 20px;
    }

    .sec-title {
        margin-bottom: 100px;
    }

    .pricing-section ul.switch-toggler-list {
        margin-bottom: 40px;
    }

    .list-inline li {
        display: inline-block;
    }

    .pricing-section ul.switch-toggler-list li a {
        color: #989898;
    }

    .pricing-section ul.switch-toggler-list li.active a {
        color: #000;
    }

    .pricing-section ul.switch-toggler-list li a {
        font-size: 18px;
        font-weight: 600;
        color: #989898;
        padding-left: 10px;
        padding-right: 10px;
        display: block;
    }

    .pricing-section .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        vertical-align: middle;
    }

    .pricing-section .switch.on .slider {
        background: #d43396;
        background: -webkit-gradient(left top, right top, color-stop(0%, #d43396), color-stop(100%, #6541c1));
        background: -webkit-gradient(linear, left top, right top, from(#d43396), to(#6541c1));
        background: linear-gradient(to right, #d43396 0%, #6541c1 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#d43396', endColorstr='#6541c1', GradientType=1);
    }

    .pricing-section .slider.round {
        border-radius: 34px;
    }

    .pricing-section .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .slider.round:before {
        border-radius: 50%;
    }

    .pricing-section .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .tabed-content #year {
        display: none;
    }

    .pricing-section .pricing-row {
        padding-top: 20px;
    }

    .pricing-section .single-pricing {
        position: relative;
        background: #E8E6E6;
        border-radius: 15px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing .inner {
        position: relative;
        padding-bottom: 45px;
        padding-top: 45px;
    }

    .pricing-section .single-pricing h3.title {
        font-size: 24px;
        color: #170B35;
        font-weight: 600;
    }

    .pricing-section .single-pricing h3,
    .pricing-section .single-pricing p,
    .pricing-section .single-pricing ul,
    .pricing-section .single-pricing li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pricing-section .single-pricing p.price {
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 53px;
        font-weight: 200;
        line-height: 1em;
        margin-bottom: 15px;
        margin-top: 15px;
    }

    .pricing-section .single-pricing p.price-label {
        font-size: 18px;
        font-weight: 600;
        color: #656565;
    }

    .pricing-section .single-pricing ul.list-item {
        margin-top: 45px;
    }

    .pricing-section .single-pricing ul.list-item li {
        font-size: 20px;
        color: #170B35;
        font-weight: 400;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-check {
        color: #12CE32;
    }

    .pricing-section .single-pricing ul.list-item li i {
        vertical-align: middle;
        margin-right: 5px;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-times {
        color: #FF0302;
    }

    .pricing-section .single-pricing a.thm-btn {
        padding: 15px 57px;
        margin-top: 35px;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .thm-btn>span {
        position: relative;
    }

    .pricing-section .single-pricing.popular {
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        margin-top: -10px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing.popular .inner {
        padding-top: 45px;
        padding-bottom: 30px;
    }

    .pricing-section .single-pricing.popular .thm-btn {
        color: #fff;
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .pricing-section .single-pricing a.thm-btn {
        padding: 15px 57px;
        margin-top: 35px;
    }

    .thm-btn {
        display: inline-block;
        border: none;
        outline: none;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 600;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
        border-radius: 28px;
        padding: 15px 29px;
        position: relative;
    }

    .pricing-section .single-pricing.popular .thm-btn:before {
        opacity: 0;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .pricing-section .switch.off .slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .thm-btn.borderd {
        color: #190A32;
    }

    .pricing-section .single-pricing a.thm-btn:hover {
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .thm-btn.borderd:hover:before {
        opacity: 0;
    }

    .thm-btn.borderd:hover {
        color: #fff;
    }

    @media (max-width: 736px) {
        .pricing-section .single-pricing.popular {
            top: 0;
            margin-top: 50px;
        }

        .pricing-section .single-pricing {
            max-width: 370px;
            margin-top: 50px;
            margin-left: auto;
            margin-right: auto;
        }

        .pricing-section ul.switch-toggler-list {
            margin-bottom: 0;
        }
    }

    .single-pricing .tag {
        position: absolute;
        text-transform: uppercase;
        font: 600 14px "Poppins", sans-serif;
        color: #fff;
        padding: 5px 20px;
        top: 5px;
        left: 6px;
        border-radius: 15px;
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
    }

    @media only screen and (max-width: 767px) {
        .boosting-list-tab .tabs li a {
            font-size: 15px;
            padding-right: 10px;
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 10px;
        }

        .tab-section {
            padding-bottom: 50px;
        }

        .boosting-list-tab .tabs li {
            flex: 0 0 48%;
            max-width: 46%;
            padding-top: 10px;
        }

        .boosting-list-tab .tabs li a {
            font-size: 15px;
            padding-right: 10px;
            padding-top: 12px;
            padding-bottom: 12px;
            padding-left: 10px;
        }

        .boosting-list-tab .tabs li a span {
            display: block;
            margin-top: 4px;
            font-size: 12px;
        }

        .boosting-list-tab .tabs li a i {
            font-size: 30px;
        }

        .boosting-list-tab .tabs li.bg-eff7e9 {
            background: unset;
        }

        .boosting-list-tab .tabs li.bg-fff8f0 {
            background: unset;
        }

        .boosting-list-tab .tabs li.bg-ecfaf7 {
            background: unset;
        }

        .boosting-list-tab .tabs li.bg-f2f0fb {
            background: unset;
        }

        .boosting-list-tab .tabs li.bg-c5ebf9 {
            background: unset;
        }

        .boosting-list-tab .tab_content .tabs_item .content h2 {
            margin: 30px 0 10px 0;
        }

        .boosting-list-tab .tab_content .tabs_item .tab-text-content {
            margin-top: 25px;
            padding-left: 45px;
        }

        .boosting-list-tab .tab_content .tabs_item .tab-text-content i::before {
            font-size: 30px;
        }

        .boosting-list-tab .tab_content .tabs_item .tab-shape {
            width: 110px;
        }

        .boosting-list-tab .tab_content .tabs_item .tab-btn {
            margin-top: 20px;
        }
        .my-position-element {
            right: 9px !important;
    bottom: calc(var(--breadscrump-height) + -11px)!important;
        }
        .breadcrumb{
            font-size:15px;
        }
        .main-banner-content p{
            font-size:16px;
        }
        .page-header-shape:before {

                width: 139%;
    height: 74%;
        }

    }
    
    .choose-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }
    
    .choose-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .choose-content-area p {
        text-align:justify;
    }
    
:root {
  --breadscrump-height: 60px;
}

.page-header-shape {
    height: var(--breadscrump-height);
}

.my-position-element{
    position: absolute;
    right: 40px;
    bottom: calc( var(--breadscrump-height)  + 0px);
}
   
   
</style>
@endsection
@section('content')
@php
    $disCripTion = trim(($seoTags['wikiDes'] ?? '') . ' ' . ($seoTags['shortNote'] ?? ''));
    if(isset($seoTags['innerLinks']) && count($seoTags['innerLinks']) > 0){
    
    
    shuffle($seoTags['innerLinks']);
    $disCripTion = array_reduce($seoTags['innerLinks'], function ($description, $linkData) {
        $anchorTag = '<a href="' . $linkData->slug . '" style="color: #467bbe; text-decoration: none;">' . $linkData->name . '</a>';
        return preg_replace('/' . preg_quote($linkData->name, '/') . '/', $anchorTag, $description, 1);
    }, $disCripTion);
    
    }
    
   //dd($seoTags['faqData']);

@endphp
<!-- Breadcrumb -->
<section class="page-header position-relative">
    <div class="page-header-shape"></div>
    <div class="container">
      <div class="page-header-info main-banner-content">
            <!--<h4>About Us!</h4>-->
            <h1>AI Meets Taxis: Top, Cheap and Best 10 Smart Dispatch Software’s Dominating 2025</h1>

            <p>
                {{ $seoTags['metaDes'] ?? "Unlock smart mobility by leveraging the power of our next-gen cab booking software with advanced dispatch system" }}
            </p>

        
            <div class="row align-items-center justify-content-between mt-3">
                <!-- Left: Updated Time and Read -->
                <div class="col-md-auto">
                    <div class="blog-meta ">
                        <p class="me-4"><strong>Updated on</strong> May 26, 2025</p>
                        <p><i class="fa fa-clock me-1"></i>13 min read</p>
                    </div>
                </div>

            </div>

            <div class="banner-btn">
                <!--<a href="#" class="default-btn-one">More About Us</a>-->
                <!--<a href="https://www.youtube.com/watch?v=_ysd-zHamjk" class="video-btn popup-youtube">Start a Free Trail<i class="fa-solid fa-arrow-right"></i></a>-->
            </div>
        </div>
    </div>
    
   <div class="row align-items-end justify-content-end my-position-element">
     

        <!-- Right: Breadcrumb -->
        <div class="col-md-auto">
            <div class="breadcrumb text-white small gap-2">
                <a href="/" class="text-white text-decoration-none">Home</a> /
                <a href="/blog" class="text-white text-decoration-none">Blog</a> /
                <span>Top 10 Best Taxi Dispatch software</span>
            </div>
        </div>
    </div>
</section>
<section class="choose-section">
    <div class="container">
        
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="choose-content-area">
                    
                    <h3>Dispatch Smarter, Drive Faster: Best 10 Cab Business Management Software’s (2025)</h3>
                    <p>Choosing the right taxi dispatch system is one of the most critical decisions for any taxi or fleet business. With a wide range of dispatch solutions available in today’s market, finding the perfect fit for your operations can be overwhelming.</p>

                    <p>Running a successful taxi service is no small feat — from managing drivers and tracking vehicles to ensuring timely pickups and drop-offs, every process must be seamless and efficient. This is where modern taxi dispatch software plays a transformative role. It streamlines your workflow, automates core operations, reduces management overhead, and improves service reliability — all while cutting down on time and operational costs.</p>
                    <p>To make your decision easier, we’ve handpicked and analyzed the top 10 taxi dispatch software platforms based on key performance factors, including ease of use, features, scalability, pricing, and customer support. Whether you're just starting or scaling up, this guide will help you find the most suitable dispatch system tailored to your business needs.</p>
                     
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="intersting-post container my-4">
    <div class="bg-warning py-3 px-2">
        <h5 class="text-center fw-bold text-dark mb-0">Other Interesting Posts</h5>
    </div>
    <div class=" p-4" style="background:#ffe9ad;">
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-users text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">The Role of Customer Experience in Taxi App Success</a>
        </p>
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-car-side text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">How to Start a Taxi Business with an On-Demand Booking App</a>
        </p>
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-chart-bar text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">Taxi Dispatch Software vs Traditional Booking Methods: A Complete Comparison</a>
        </p>
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-bolt text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">5 Ways to Increase Taxi Business Revenue Using Automation</a>
        </p>
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-map text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">A Step-by-Step Guide to Launching Your Own Ride-Hailing App</a>
        </p>
        <p class="mb-3 d-flex align-items-start">
            <i class="fas fa-sync text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">Why Real-Time Tracking is a Game-Changer for Taxi Companies</a>
        </p>
        <p class="mb-0 d-flex align-items-start">
            <i class="fas fa-code text-dark me-2 mt-1"></i>
            <a href="#" class="text-dark text-decoration-underline">Choosing the Best Taxi App Development Company: What to Look For</a>
        </p>
    </div>
</div>
            </div>
        </div>
    </div>
</section>
<!--<section class="choose-section">-->
<!--    <div class="container">-->
<!--        <div class="row align-items-center">-->
<!--            <div class="col-lg-6 col-md-12 order-2 order-lg-1">-->
<!--                <div class="choose-image">-->
<!--                    <img src="{{ asset('goride/img/custom-plan/dashboard.png') }}" alt="image">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-6 col-md-12 order-1 order-lg-2">-->
<!--                <div class="choose-content-area">-->
<!--                    <span>Dispatch Software</span>-->
<!--                    <h3>Design your website exactly the way you want with our fully customizable solutions</h3>-->
<!--                    <p>We develop and design websites with 100% customization tailored to your vision and requirements.-->
<!--                        Our team is highly skilled, and we take pride in delivering fully customized solutions that-->
<!--                        ensure complete client satisfaction</p>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Customization</h4>-->
<!--                        <p>Website design customization gives you full control over the look, functionality, and user-->
<!--                            experience to perfectly match your brand.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Design</h4>-->
<!--                        <p>Design your website with 100% satisfaction, fully tailored to your vision and needs</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Implementation</h4>-->
<!--                        <p>Implement your website design with precision, ensuring every detail aligns with your vision-->
<!--                            and functions flawlessly.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-btn">-->
                        <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<section class="choose-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12">
                <div class="choose-content-area">
                    <span>ADVANTAGES</span>
                    <h3>Perks of Using a Smart Taxi Dispatch System</h3>
                    <p>Modern taxi dispatch software is more than just a digital map with moving vehicles — it’s a complete automation system built to streamline operations, boost efficiency, and enhance customer satisfaction. Here are the key benefits it offers to taxi transfer businesses:</p>
                    <div class="choose-text">
                        <i class="fa-regular fa-check"></i>
                        <h4> Real-Time Fleet Tracking</h4>
                        <p>Monitor all your vehicles live on the map. Know exactly where each driver is, estimate arrival times, and respond quickly to delays or changes.</p>
                    </div>
                    <div class="choose-text">
                        <i class="fa-regular fa-check"></i>
                        <h4>Automated Dispatching</h4>
                        <p>Assign rides automatically based on proximity, availability, or driver ratings — no more manual coordination or phone calls required.</p>
                    </div>
                    <div class="choose-text">
                        <i class="fa-regular fa-check"></i>
                        <h4>Improved Customer Experience</h4>
                        <p>Passengers can book instantly through apps or websites, receive real-time ETAs, and track their rides live — just like Uber and Lyft.</p>
                    </div><div class="choose-text">
                        <i class="fa-regular fa-check"></i>
                        <h4>Driver & Trip Management</h4>
                        <p>Manage driver profiles, documents, trip history, and performance from a central dashboard. Get instant access to reports and analytics.</p>
                    </div><div class="choose-text">
                        <i class="fa-regular fa-check"></i>
                        <h4>Reduced Operational Costs</h4>
                        <p>Automating dispatch and route optimization saves fuel, time, and manual labor, reducing daily operational expenses significantly.</p>
                    </div>
                    <div class="choose-btn">
                        <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
           
               <!--     <div class="row">-->
               <!-- <div class="choose-image">-->
               <!--    <img src="{{ asset('goride/img/custom-plan/dashboard.png') }}" alt="image">-->
               <!--</div>-->
               <!--</div>-->
               <div class="row">
                <div class="choose-image1">
                   <img src="{{ asset('goride/img/benifites.png
') }}" alt="image">
               </div>
               </div>
               
            </div>
        </div>
    </div>
</section>

        <section class="about-section padding">
        <div class="container">
            <div class="choose-content-area">
                    <span>Top 10 taxi dispatch software to<br> automate bookings and grow your<br> revenue</span>
                    <h3>1.	GoRide</h3>
                    </div>
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
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="2500ms">Welcome to Go Ride, a groundbreaking venture that combines the expertise of two industry leaders from opposite sides of the globe—Airport Rides Ltd, a premier taxi service provider in Canada, and Sri Hema Infotech, a cutting-edge software company based in India. Together, we are revolutionizing the way taxi services are managed and delivered through our state-of-the-art taxi dispatch software.</p>
                    </div>
                    <!--/.section-heading-->
                    <ul class="about-info wow fadeIn" data-wow-duration="2s" data-wow-delay="3000ms">
                        <li class="justify-content-center">
                            <img src="{{ asset('goride/img/colabrate.png') }}" alt="colabrate" style="width: 55%;">
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
                        <p class="wow fadeIn" data-wow-duration="2s" data-wow-delay="1000ms">The journey of Go Ride began with a shared vision: to create a seamless and efficient taxi dispatch system that addresses the evolving needs of modern transportation. Airport Rides Ltd has a long-standing reputation for providing reliable and customer-centric taxi services across Canada, while Sri Hema Infotech is known for its innovative software solutions that empower businesses worldwide.</p>
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
    
    <div class="row pricing-row justify-content-center pricing-section">
                                            <div class="col-md-4 col-sm-6 col-lg-3 col-9">
                            <div class="single-pricing text-center ">
                                <div class="tag d-none"><span><i class="fas fa-star"></i> Popular</span></div>
                                <form action="cart" method="post">
                                    <input type="hidden" name="_token" value="uKHdDTOczFeUmREtWg02rjgQolNDoOWr5WpvjZ5x">                                    <input type="hidden" name="productID" value="1">
                                    <input type="hidden" name="planType" value="MONTHLY">
                                    <input type="hidden" name="purchaseType" value="NEW">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="subscriptions" value="false">
                                    <div class="inner">
                                                                                <h3 class="title">Free Plan</h3>
                                        <p class="price">
                                            <!-- FREE -->
                                            ₹0
                                        </p>
                                        <p class="price-label">Full access</p>
                                        <ul class="list-item">
                                            <li><i class="fa fa-check"></i>

                                            Upto 1 driver




                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 5 bookings
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 1 website
                                            </li>
                                            
                                                <!-- <li><i class="fa fa-check"></i>
                                                0 Day FREE Trial
                                                </li> -->
                                                
                                                <li>
                                                    <i class="fa fa-check"></i>
                                                    1 Month
                                                </li>
                                                
                                        </ul>



                                        
                                            <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();" class="thm-btn borderd" href="javascript:void(0);"><span>Go with free</span></a>


                                            <!--  -->


                                                                            </div>
                                </form>
                            </div>
                        </div>
                                            <div class="col-md-4 col-sm-6 col-lg-3 col-9">
                            <div class="single-pricing text-center ">
                                <div class="tag d-none"><span><i class="fas fa-star"></i> Popular</span></div>
                                <form action="cart" method="post">
                                    <input type="hidden" name="_token" value="uKHdDTOczFeUmREtWg02rjgQolNDoOWr5WpvjZ5x">                                    <input type="hidden" name="productID" value="2">
                                    <input type="hidden" name="planType" value="MONTHLY">
                                    <input type="hidden" name="purchaseType" value="NEW">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="subscriptions" value="false">
                                    <div class="inner">
                                                                                    <div class="plan-tag">
                                                <p class="anu">
                                                    ₹16.1<span>Per
                                                        Day</span>
                                                </p>
                                            </div>
                                                                                <h3 class="title">Professional</h3>
                                        <p class="price">
                                            <!-- ₹499 -->
                                            ₹499
                                        </p>
                                        <p class="price-label">Full access</p>
                                        <ul class="list-item">
                                            <li><i class="fa fa-check"></i>

                                            Upto 3 drivers




                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 50 bookings
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 1 website
                                            </li>
                                            
                                                <!-- <li><i class="fa fa-check"></i>
                                                0 Day FREE Trial
                                                </li> -->
                                                
                                                <li>
                                                    <i class="fa fa-check"></i>
                                                    1 Month
                                                </li>
                                                
                                        </ul>



                                                                                    <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();" class="thm-btn borderd" href="javascript:void(0);"><span>Subscribe</span></a>

                                                                            </div>
                                </form>
                            </div>
                        </div>
                                            <div class="col-md-4 col-sm-6 col-lg-3 col-9">
                            <div class="single-pricing text-center popular">
                                <div class="tag "><span><i class="fas fa-star"></i> Popular</span></div>
                                <form action="cart" method="post">
                                    <input type="hidden" name="_token" value="uKHdDTOczFeUmREtWg02rjgQolNDoOWr5WpvjZ5x">                                    <input type="hidden" name="productID" value="3">
                                    <input type="hidden" name="planType" value="MONTHLY">
                                    <input type="hidden" name="purchaseType" value="NEW">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="subscriptions" value="false">
                                    <div class="inner">
                                                                                    <div class="plan-tag">
                                                <p class="anu">
                                                    ₹32.2<span>Per
                                                        Day</span>
                                                </p>
                                            </div>
                                                                                <h3 class="title">Enterprise</h3>
                                        <p class="price">
                                            <!-- ₹999 -->
                                            ₹999
                                        </p>
                                        <p class="price-label">Full access</p>
                                        <ul class="list-item">
                                            <li><i class="fa fa-check"></i>

                                            Upto 10 drivers




                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 250 bookings
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                Upto 3 websites
                                            </li>
                                            
                                                <!-- <li><i class="fa fa-check"></i>
                                                0 Day FREE Trial
                                                </li> -->
                                                
                                                <li>
                                                    <i class="fa fa-check"></i>
                                                    1 Month
                                                </li>
                                                
                                        </ul>



                                                                                    <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();" class="thm-btn borderd" href="javascript:void(0);"><span>Subscribe</span></a>

                                                                            </div>
                                </form>
                            </div>
                        </div>
                                    </div>
           <section class="testimonials section-padding mt-15">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center mb-30">
                            <div class="section-subtitle">Testimonials</div>
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
                                    <div class="text">
                                        <p>Sri Hema Infotech's incredible customer support made integrating the Dispatch Taxi System seamless, ensuring efficiency and reliability from day one, boosting both our operations and customer satisfaction.</p>
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
                                        <p>The Dispatch Taxi System simplifies operations, offering an intuitive platform, real-time updates, and improved efficiency, making it indispensable for any taxi service aiming for reliability and enhanced experiences.</p>
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
                                        <p>Using Sri Hema Infotech's Dispatch Taxi System, our operations transformed with real-time GPS tracking & dispatching, resulting in streamlined services and higher efficiency for both drivers and passengers.</p>
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
                                        <p>The system provides easy setup, real-time tracking, and automatic dispatch for faster ride allocation. Though efficient, adding missing features would enhance its value, making it even better for taxi services.</p>
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
                                        <p>Go Ride simplifies operations with an easy-to-use platform, reducing wait times and boosting efficiency. It's a game-changing solution for taxi services focused on improving customer satisfaction and streamlining processes.</p>
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
                                        <p>Sri Hema Infotech’s system brought significant improvements to Go Ride, offering real-time tracking and effortless bookings, improving experiences for passengers and drivers alike while boosting operational efficiency.</p>
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
    <div class="container">
        <div class="mb-5">
     <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>2.	Better Suite</h3>
                    
                    <p>Founded in 2018 by a developer and a designer, GoRide began with a vision to simplify on-demand mobility. Today, it powers over 100 businesses worldwide with its smart, scalable taxi dispatch software. Focused on innovation and user experience, GoRide helps companies launch and manage their own branded ride services—fast, reliable, and effortless.</p>
                     
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight"  data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/bettersuite-og.png
') }}" alt="image">
                        </div>
        </div>
        </div>
        </div>
        <div class="mb-5">
     <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft"  data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>3.	TaxiCaller</h3>
                    
                    <p>Since 2011, TaxiCaller has empowered taxi, limo, private hire, and car service companies across 70+ countries with cloud-based dispatching solutions. Developed by Swedish engineers with expertise in telecom and transport, the platform delivers reliable, innovative software tailored for the evolving mobility industry.</p>
                     
                     <p>With their software, operators can offer customers multiple booking channels—like online reservations and e-hailing through branded apps—keeping them competitive in today’s dynamic transport landscape.</p>
                <p>More than just an app, TaxiCaller provides a complete end-to-end system with invoicing, business analytics, reporting, and more—making it easy to manage operations from a single platform.</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/taxicaller.webp

') }}" alt="image">
                        </div>
        </div>
        </div>
           </div>
        <div class="mb-5">
       <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>4.	UnicoTaxi</h3>
                    
                    <p>UnicoTaxi offers innovative, scalable solutions for the transportation and logistics industry worldwide. Their services include taxi dispatch software, delivery and logistics platforms, Uber clone app development, and custom ride-hailing solutions—designed to meet the evolving demands of modern mobility businesses.</p>
                     
                    
                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/unicotaxi-logo.png


') }}" alt="image">
                        </div>
        </div>
        </div>  
           </div>
           
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>5.	Envoy Taxi Dispatch Software</h3>
                    
                <p>Envoy Taxi Dispatch Software is a UK-based, cloud-powered solution tailored for private hire, chauffeur, and airport transfer businesses. Launched in 2014, Envoy delivers a complete system encompassing booking, dispatching, invoicing, fleet and driver management, mobile apps, and more—all in one easy-to-use platform.</p>
                  <p>Known for its reliability, affordability, and minimal training requirements, Envoy supports companies of all sizes, from small taxi firms to large executive fleets. With no setup fees or long-term contracts, it offers operators the freedom to grow confidently. Its flexibility and customer-focused support have made Envoy one of the most trusted solutions in the UK’s private hire industry.</p>                  
                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1  wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/envoy.png



') }}" alt="image">
                        </div>
        </div>
        </div> 
           </div>
           
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>6.	Sherlock Taxi Dispatch Software </h3>
                    
                <p>Sherlock Taxi Dispatch Software is backed by a team of industry veterans with deep roots in taxi operations. Since 2011, Sherlock has supported fleets across the UK, Europe, Africa, Asia, and Australia—offering a modern, scalable alternative to legacy systems like Shamrock.</p>
                  <p>Now part of Haulmont Technology, a 600-employee global company, Sherlock benefits from robust technical expertise and long-standing staff with extensive experience in global dispatch operations. Its longevity, stability, and industry-driven approach make it a trusted dispatch partner for fleets worldwide.</p>                  
                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1  wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/sherlock.jpg




') }}" alt="image">
                        </div>
        </div>
        </div>  
           </div>
           
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>7.	EasyTaxiOffice  </h3>
                    
                <p>EasyTaxiOffice offers one of the most efficient, affordable, and user-friendly taxi dispatch solutions on the market. With a web-based booking and dispatch system enhanced by dedicated driver and customer apps, it helps companies streamline operations and boost revenue.</p>
                  <p>Designed for taxi businesses of all sizes, EasyTaxiOffice combines high-quality software with fair pricing—providing an accessible path to digital transformation and long-term growth.</p>                  
                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/easytaxioffice.png





') }}" alt="image">
                        </div>
        </div>
        </div> 
         </div>
         
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>8.	YelowSoft</h3>
                    
                <p>YelowSoft is a leading global provider of taxi dispatch software, trusted by over 500 taxi businesses across 40+ countries. The platform enables seamless automation and centralized management of fleets, drivers, bookings, invoices, and more.</p>
                  <p>By streamlining operations and enhancing service quality, YelowSoft empowers taxi companies to boost efficiency and stay competitive in today’s evolving transport landscape.</p>                  
                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/yelowsoft_logo.jpg





') }}" alt="image">
                        </div>
        </div>
        </div>
         </div>
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12  wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>9.	Infinite Cab</h3>
                    
                <p>Infinite Cab offers a GPS-based Taxi Dispatch Software designed to enhance customer service and simplify taxi operations—from booking and fare calculation to invoicing and payment management. This intelligent cab management system streamlines fleet operations while improving service quality.</p>
                  <p>Built for modern taxi businesses, the software integrates driver, passenger, and admin modules into a unified platform. With real-time GPS tracking and mobile/tablet support, operators gain better visibility and control of their fleet.</p>                  
                <p>Ideal for startups and growing services alike, Infinite Cab helps reduce dispatch time, increase bookings, and scale operations efficiently. It also includes live demos for Android and iOS apps, along with a WordPress taxi booking plugin for seamless website integration.</p>
                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight"  data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/infibitecab.jpg






') }}" alt="image">
                        </div>
        </div>
        </div> 
         </div>
        <div class="mb-5">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 wow slideInLeft" data-wow-duration="2s">
                <div class="choose-content-area">
                    
                    <h3>10.Minster</h3>
                    
                                <p>Build and Launch Your Own Taxi Booking App
                Tap into the booming online taxi industry with a custom-built app that boosts your revenue. As a leading taxi app development company, Mindster delivers reliable, feature-rich solutions like Uber and Ola.
                </p>
                                  <p>Advanced Taxi Dispatch Software
                Our system combines simplicity and sophistication, offering real-time tracking, seamless booking, and user-friendly designs. Every interface is tailored for smooth operation and enhanced customer experience.
                </p>                  
                                <p>Driven by Innovation
                At Mindster, we deliver high-end IT solutions with a focus on clarity, creativity, and quality. We transform your ideas into powerful, easy-to-use platforms that fuel business growth.
                </p>
                                                </div>
            </div>
            <div class="col-lg-6 col-md-12 order-2 order-lg-1 wow slideInRight" data-wow-duration="2s">
                        <div class="choose-image">
                            <img src="{{ asset('goride/img/mindster.jpg







') }}" alt="image">
                        </div>
        </div>
        </div>   
       </div> 
         </div>
          

@if (count($seoTags['faqData']) > 0)

    <section class="tab-section ptb-100">
        <div class="container" id="faq">
            <div class="section-title">

                <h3>FAQ</h3>
            </div>
            <div class="tab boosting-list-tab">
                <div class="row justify-content-center">
                    <div class="col-10">
                        <ul class="accordion-box clearfix">

                            @foreach ($seoTags['faqData'] as $key)
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">{{ $loop->iteration }}.</span> {{ $key['question'] ?? ''}}</div>
                                    <div class="acc-content" style="display: none;">
                                        <div class="content">
                                            <div class="text">{{$key['answer'] ?? ''}} </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach


                            <!-- <li class="accordion block">
                                    <div class="acc-btn"><span class="count">2.</span> How Does Taxi Dispatch Software Work?
                                    </div>
                                    <div class="acc-content" style="display: none;">
                                        <div class="content">
                                            <div class="text">Taxi dispatch software works by allowing passengers to request
                                                rides via an app or phone call. In
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                for instance, the software automatically identifies the passenger's location and
                                                assigns the nearest available driver based on GPS. The dispatcher monitors the
                                                ride progress and communicates with the driver through the system.
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">3.</span> Is Taxi Dispatch Software compatible with
                                        different devices?</div>
                                    <div class="acc-content">
                                        <div class="content">
                                            <div class="text">Yes, taxi dispatch software is compatible with various devices. In
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                for example, drivers use mobile apps to receive ride requests, while dispatchers
                                                use desktop interfaces to manage the fleet.
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">4.</span> Can passengers track their rides in
                                        real-time?
                                    </div>
                                    <div class="acc-content">
                                        <div class="content">
                                            <div class="text">Yes, passengers can track their ride in real-time. In
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                passengers can see their driver's location, estimated time of arrival, and route
                                                via a mobile app, ensuring transparency and reducing uncertainty.
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">5.</span> How does Taxi Dispatch Software handle
                                        payment?</div>
                                    <div class="acc-content">
                                        <div class="content">
                                            <div class="text">Taxi dispatch software supports various payment methods, including
                                                credit cards, digital wallets, and cash. For instance, in
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                passengers can pay seamlessly through the app, while drivers receive automatic
                                                payment processing at the end of each trip.
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">6.</span> Can I offer discounts or promotions
                                        through the software? </div>
                                    <div class="acc-content">
                                        <div class="content">
                                            <div class="text">Yes, many taxi dispatch software platforms allow you to set up and
                                                offer promotional codes or discounts. For example, in
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                businesses can create special promotions for customers during holidays or
                                                events, enhancing customer retention and driving more bookings.
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="accordion block">
                                    <div class="acc-btn"><span class="count">7.</span> Can passengers schedule rides in advance?
                                    </div>
                                    <div class="acc-content">
                                        <div class="content">
                                            <div class="text">Yes, passengers can schedule rides in advance using taxi dispatch
                                                software. In
                                                {{(isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '')}},
                                                for example, passengers can book rides for specific times and dates, ensuring
                                                that they have a taxi ready when they need it, such as for airport pickups or
                                                appointments.
                                            </div>
                                        </div>
                                    </div>
                                </li> -->
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endif



@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script>
    $(document).ready(function () {
        @if (count($seoTags['faqData']) > 0)
            $('ul.accordion-box.clearfix li').first().find('.acc-btn').click();
        @endif
    });
    
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