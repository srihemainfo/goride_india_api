@extends('layouts.app')

@section('content')

<style>
    .tabs_wrapper {
	 width: 100%;
	 text-align: center;
	 margin: 0 auto;
	 background: transparent;
}
 ul.tabs {
	 display: inline-block;
	 vertical-align: top;
	 position: relative;
	 z-index: 10; 
	 margin: 2px;
	 padding: 0;
	 width: 17%;
	 min-width: 175px;
	 list-style: none;
	 -ms-transition: all 0.3s ease;
	 -webkit-transition: all 0.3s ease;
	 transition: all 0.3s ease;
}
 ul.tabs li {
    /*box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);*/
	 cursor: pointer;
	 padding: 10px;
	 line-height: 31px;
	 color: white;
	 text-align: left;
	 font-weight: bold;
	 background-color: #666;
	 background: #600026;
	 background: #fff !important;
    border: 1px solid #bdbdbd;
    /*border-radius: 10px;*/
    color:#000;
}
 ul.tabs li:hover {
	 background: #ae0046;
	 background: -moz-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
	 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(174, 0, 70, 1)), color-stop(100%, rgba(251, 15, 86, 1)));
	 background: -webkit-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
	 background: -o-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
	 background: -ms-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
	 background: #606060 !important;
	 filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ae0046', endColorstr='#fb0f56',GradientType=0);
	 color: #fff;
	 -ms-transition: all 0.3s ease;
	 -webkit-transition: all 0.3s ease;
	 transition: all 0.3s ease;
}
 ul.tabs li.active {
	 background: #4c001e;
	 background: -moz-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
	 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(76, 0, 30, 1)), color-stop(100%, rgba(159, 7, 53, 1)));
	 background: -webkit-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
	 background: -o-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
	 background: -ms-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
	 background: linear-gradient(to bottom, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
	 filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4c001e', endColorstr='#9f0735',GradientType=0);
	 color: #ddd;
	 -ms-transition: all 0.3s ease;
	 -webkit-transition: all 0.3s ease;
	 transition: all 0.3s ease;
	 color: #f8be00;
    border-right: 4px solid #f8be00;
}
 .tab_container {
	 display: inline-block;
	 vertical-align: top;
	 position: relative;
	 z-index: 20;
	 left: 0;
	 width: 80%;
	 min-width: 10px;
	 text-align: left;
	 background: white;
	 border-radius: 12px;
}
 .tab_content {
	 padding: 20px;
	 height: 100%;
	 display: none;
	 background:#f2f7fb;
}
 .tab_drawer_heading {
	 display: none;
}
 @media screen and (max-width: 781px) {
	 ul.tabs {
		 display: none;
	}
	 .tab_container {
		 display: block;
		 margin: 0 auto;
		 width: 95%;
		 border-top: none;
		 border-radius: 0;
	}
	 .tab_drawer_heading {
		 background-color: #ccc;
		 background: #600026;
		/* Old browsers */
		 background: -moz-linear-gradient(top, rgba(96, 0, 38, 1) 0%, rgba(198, 9, 67, 1) 100%);
		/* FF3.6+ */
		 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(96, 0, 38, 1)), color-stop(100%, rgba(198, 9, 67, 1)));
		/* Chrome,Safari4+ */
		 background: -webkit-linear-gradient(top, rgba(96, 0, 38, 1) 0%, rgba(198, 9, 67, 1) 100%);
		/* Chrome10+,Safari5.1+ */
		 background: -o-linear-gradient(top, rgba(96, 0, 38, 1) 0%, rgba(198, 9, 67, 1) 100%);
		/* Opera 11.10+ */
		 background: -ms-linear-gradient(top, rgba(96, 0, 38, 1) 0%, rgba(198, 9, 67, 1) 100%);
		/* IE10+ */
		 background: linear-gradient(to bottom, rgba(96, 0, 38, 1) 0%, rgba(198, 9, 67, 1) 100%);
		/* W3C */
		 filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#600026', endColorstr='#c60943',GradientType=0);
		/* IE6-9 */
		 color: #fff;
		 margin: 0;
		 padding: 5px 20px;
		 display: block;
		 cursor: pointer;
		 -webkit-touch-callout: none;
		 -webkit-user-select: none;
		 -khtml-user-select: none;
		 -moz-user-select: none;
		 -ms-user-select: none;
		 user-select: none;
		 text-align: center;
		 box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        background: #fff !important;
        border: 1px solid #bdbdbd;
        border-radius: 10px;
        color: #000;
        font-size:20px;
	}
	 .tab_drawer_heading:hover {
		 background: #ccc;
		 background: #ae0046;
		 background: -moz-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
		 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(174, 0, 70, 1)), color-stop(100%, rgba(251, 15, 86, 1)));
		 background: -webkit-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
		 background: -o-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
		 background: -ms-linear-gradient(top, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
		 background: linear-gradient(to bottom, rgba(174, 0, 70, 1) 0%, rgba(251, 15, 86, 1) 100%);
		 filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ae0046', endColorstr='#fb0f56',GradientType=0);
		 color: #000;
	}
	 .d_active {
		 background: #fff;
		 background: #4c001e;
		 background: -moz-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
		 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(76, 0, 30, 1)), color-stop(100%, rgba(159, 7, 53, 1)));
		 background: -webkit-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
		 background: -o-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
		 background: -ms-linear-gradient(top, rgba(76, 0, 30, 1) 0%, rgba(159, 7, 53, 1) 100%);
		 background: #fff !important;
		 filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4c001e', endColorstr='#9f0735',GradientType=0);
		 color: #000;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        border-radius: 11px;
        border-right: 6px solid #f8be00;
	}
}
 .tab-head{
     font-size: 24px;
    color: grey;
 }
 .sub-headpara{
     color:#f8be00;
     font-size:20px;
 }
 
 
.accordion-box .block .acc-btn {
    padding: 22px;
}

.accordion-box .block .acc-btn:before {
    top: 13px;
}

@media screen and (max-width: 767px) {
    .faq_section {
        padding: 50px 0;
    }
}


h4 strong {
    font-weight: 500;
}
 
    </style>

    <!-- Breadcrumb -->

    <section class="page-header faq">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>Features</h4>
                <h1>Innovative Features for a Smarter <span>Cab Dispatch System</span></h1>
                <!--<p>Explore our affordable and customizable pricing options designed to fit businesses of all sizes.</p>-->
    <a href="signup" class="cs_btn cs_style_2 mt-4">Get start for free&nbsp;<i class="fa-regular fa-car-side-bolt"></i></a>
            </div>
        </div> 
    </section>
    
<section class="new-features mt-5 mb-5 d-none d-lg-block">
    <div class="container-fluid">
        <div class="tabs_wrapper">
            <ul class="tabs">
              <li class="active" rel="tab1">CRM With Website</li>
              <li rel="tab2">Customizable Features</li>
              <li rel="tab3">Booking Form</li>
              <li rel="tab4">My Passenger App /My Riders App</li>
              <li rel="tab5">Promo Code</li>
              <li rel="tab6">Offer Day</li>
              <li rel="tab7">Offer Time</li>
              <li rel="tab8">Zone Drawing</li>
               <li rel="tab9">Add Location (Range)</li>
            </ul>
            <div class="tab_container">
              <h3 class="d_active tab_drawer_heading" rel="tab1">CRM With Website</h3>
              <div id="tab1" class="tab_content">
              <h2 class="tab-head">Complete CRM & Dispatch Software Integration for Your Business</h2>
                <p>At Your Company Name, we are committed to providing seamless solutions for your 
                business by integrating a powerful CRM with your website and dispatch 
                software system. Our comprehensive approach ensures smooth operations, increased efficiency, and improved customer experience.</p>
                <div class="para">
                    <h2 class="tab-head">Why Choose Our CRM & Dispatch System?</h2>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Unified Platform:</strong></span>Our integrated CRM and dispatch system allows you to manage 
                            customer relationships, track leads, and dispatch vehicles in
                            one centralized platform. This streamlines your operations, reduces 
                            manual tasks, and ensures everyone is on the same page.</p>
                            
                             <p><span class="sub-headpara"><strong>Real-Time Dispatch Management:</strong></span>Stay on top of your fleet with real-time tracking and dispatch updates. Assign jobs quickly, monitor vehicle availability, and get live updates on driver status, all from your CRM dashboard.</p>
                            
                             <p><span class="sub-headpara"><strong>Customer Relationship Management:</strong></span>Enhance your customer interactions with advanced CRM tools. Our system captures customer data, tracks communication history, and automates follow-ups, making it easier to manage your relationships and build long-term loyalty.</p>
                            
                             <p><span class="sub-headpara"><strong>Custom Website Integration:</strong></span>We seamlessly integrate our dispatch and CRM software with your existing website, providing a smooth user experience for both your team and your customers. Online bookings, customer inquiries, and dispatch requests are all handled efficiently.</p>
                            </div>
                </div>
              </div>
              <!-- #tab1 -->
              <h3 class="tab_drawer_heading" rel="tab2">Customizable Features</h3>
              <div id="tab2" class="tab_content">
              <h2>Customizable Features</h2>
                <h2 class="tab-head">Vehicle Selection</h2>
<p>At Your Company Name we understand the importance of providing flexibility and choice to your customers. Our CRM and dispatch system comes with a fully customizable Vehicle Selection feature that allows users to choose from a range of available vehicles that best suit their needs. Whether your customers need a luxury car, a standard ride, or a larger vehicle for group travel, our system lets them:</p>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Browse Vehicle Categories:</strong></span>Customers can easily filter by vehicle type, size, or special features.</p>
                            
                             <p><span class="sub-headpara"><strong>Real-Time Availability:</strong></span>The system updates in real-time, showing which vehicles are available at the moment of booking.</p>            
                             <p><span class="sub-headpara"><strong>Detailed Vehicle Information:</strong></span>Each vehicle comes with a description, images, and details such as passenger capacity, luggage space, and amenities</p>
                            
                             <p><span class="sub-headpara"><strong>Favorite Vehicles:</strong></span>Customers can save their preferred vehicles for quick selection on future bookings.</p>
                            </div>


                            <h2 class="tab-head">Choice of Payment Options</h2>
<p>We believe in offering convenience and flexibility when it comes to payments. Our system provides customers with multiple Payment Options at checkout, ensuring a hassle-free experience. Your business can customize these options based on what works best for you and your customers</p>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Credit/Debit Cards:</strong></span>Securely accept all major credit and debit cards through an integrated payment gateway.</p>
                             <p><span class="sub-headpara"><strong>PayPal & Digital Wallets:</strong></span>Offer customers the ease of using PayPal or other digital wallet services like Apple Pay and Google Pay.</p>            
                             <p><span class="sub-headpara"><strong>Cash on Delivery:</strong></span>For customers who prefer to pay in person, our system can allow cash or card payments upon arrival.</p>
                            
                             <p><span class="sub-headpara"><strong>Custom Payment Plans:</strong></span>Enable installment plans, corporate billing, or pre-paid packages for regular clients.</p>
                            </div>
              </div>
              <!-- #tab2 -->
              <h3 class="tab_drawer_heading" rel="tab3">Booking Form</h3>
              <div id="tab3" class="tab_content">
                  <div class="para">
                    <h2 class="tab-head">Booking Form</h2>
                    <div class="choose-text">
                        <p>The <span class="sub-headpara"><strong>Booking Form</strong></span> is a feature that allows the admin to create bookings manually and also enables automatic tracking of the customer journey and all other details related to the booking. It includes fields for necessary information such as pickup and drop-off locations, date and time, and ride preferences (e.g., type of vehicle). The form is user-friendly, designed to ensure smooth booking by collecting all the essential details from customers and providing a seamless booking experience.</p>
                    </div>
                </div>
                </div>
              <!-- #tab3 -->
              <h3 class="tab_drawer_heading" rel="tab4">Driver App / Passenger App</h3>
              <div id="tab4" class="tab_content">
              <h2>My Riders App /My Passenger App</h2>
                 <h2 class="tab-head">Driver App: Drive Smarter, Earn More</h2>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Instant Job Alerts:</strong></span>Get real-time ride notifications and never miss a request. </p>
                             <p><span class="sub-headpara"><strong>In-App Navigation:</strong></span>Find the quickest routes with integrated GPS.</p>            
                             <p><span class="sub-headpara"><strong>Track Earnings:</strong></span> View completed rides and daily earnings on the go. </p>
                             <p><span class="sub-headpara"><strong>Seamless Communication:</strong></span>Chat easily with passengers and dispatchers. </p>
                             <p><span class="sub-headpara"><strong>Driver Safety:</strong></span>Emergency contacts and route tracking for peace of mind.  </p>
                             <p><span class="sub-headpara"><strong>Flexible Schedule:</strong></span> Set your hours, drive when it suits you.</p>
                             <p>Drive with ease. Earn with confidence.</p>
                            </div>
                            
                             <h2 class="tab-head">Passenger App: Your Ride, Your Way</h2>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Quick Booking: </strong></span>Book a ride in seconds, anytime, anywhere. </p>
                             <p><span class="sub-headpara"><strong>Real-Time Tracking</strong></span>Track your driver’s location in real-time. </p>            
                             <p><span class="sub-headpara"><strong>Transparent Pricing</strong></span> Get an instant fare estimate before you ride. </p>
                             <p><span class="sub-headpara"><strong>Ride History:</strong></span> Access past rides and receipts effortlessly.</p>
                             <p><span class="sub-headpara"><strong>Safety First:</strong></span> Share ride details with loved ones for extra security.</p>
                             <p>Safe, simple, and seamless rides, whenever you need them. </p>
                            </div>
                            </div>
              <!-- #tab4 --> 
                <h3 class="tab_drawer_heading" rel="tab5">Promocode</h3>
              <div id="tab5" class="tab_content">
              <h2>Promocode</h2>
                <p>Unlock the full potential of your cab or car dispatch business with our cutting-edge software! For a limited time, use PROMO20 at checkout to get 20% off your first month. Simplify dispatching, enhance real-time tracking, and boost customer satisfaction today </p>
              </div>
              
               <h3 class="tab_drawer_heading" rel="tab6">Offer Day</h3>
              <div id="tab6" class="tab_content">
                  <h2>Offer Day: Create Special Promotions for Any Day </h2>
                <p>With our CRM, you can easily create Offer Days—special promotions tied to a specific day or date. Whether it's a holiday, anniversary, or any other special occasion, you can set up an offer that’s only available for that day. </p>
                <h4><strong>How It Works:</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Choose a Day:</strong> Select the exact day you want your offer to run.</li>
                    <li><strong class="sub-headpara">Automatic Activation:</strong> The offer will automatically become available on the chosen day and expire when the day ends. </li>
                    <li><strong class="sub-headpara">Limited-Time Offers:</strong> Create a sense of urgency and excitement by offering exclusive deals for one day only. </li>
                </ul>
                <h4><strong>Why Use Offer Day? </strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Boost Sales for Special Days:</strong> Drive more sales by offering discounts or deals on important dates. </li>
                    <li><strong class="sub-headpara">Easy Setup:</strong> Simply select the day, and our CRM takes care of the rest. </li>
                    <li><strong class="sub-headpara">Create Excitement:</strong> Limited offers for a specific day keep customers coming back for more. </li>
                </ul>
                <p>With Offer Day, you can make any day special with a time-limited promotion! </p>
              </div>
              
              
               <h3 class="tab_drawer_heading" rel="tab7">Offer Time</h3>
              <div id="tab7" class="tab_content">
                <h2>Offer Time: Manage Your Promotions with Ease </h2>
                <p>With our CRM, you can easily create limited-time offers to boost your sales. The Offer Time feature lets you set specific start and end dates for any promotion. </p>
                <h4><strong>How It Works:</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Set Time Periods:</strong> Choose exactly when your offer starts and ends. </li>
                    <li><strong class="sub-headpara">Automatic Updates:</strong> The system automatically makes the offer available during the set time, and it expires when the time is up. </li>
                    <li><strong class="sub-headpara">Create Urgency:</strong> Limited-time offers encourage customers to act quickly before the deal expires. </li>
                </ul>
                <h4><strong>Why Use Offer Time?</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Increase Sales:</strong> Time-limited promotions drive more purchases. </li>
                    <li><strong class="sub-headpara">Easy Management:</strong> The system handles everything automatically. </li>
                    <li><strong class="sub-headpara">Track Results:</strong> See how well your offer performs during its active period. 

With  <strong>Offer Time,</strong> creating and managing promotions has never been easier!  </li>
                </ul>
              </div>
              
               <h3 class="tab_drawer_heading" rel="tab8">Zone Management Overview</h3>
              <div id="tab8" class="tab_content">
              <h2>Zone Management Overview</h2>
                <p>Our dispatch software offers a powerful Zone Drawing feature, allowing you to visually define service areas for optimal fleet management and faster dispatching. Whether you’re handling multiple cities, regions, or custom service areas, our software helps you stay in control.</p>
              </div>
              
               <h3 class="tab_drawer_heading" rel="tab9">Add Location Range</h3>
              <div id="tab9" class="tab_content">
              <h2>Add Location Range</h2>
                <p>The Add Location (Range) feature allows users or administrators to set specific locations and define the service range for the taxi dispatch system. This can be used to specify areas where the service operates and the distance limits for a ride. This feature helps optimize ride dispatch and ensures services are available in predefined zones.</p>
              <h3>Key Features:</h3>
              <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Customizable Zones:</strong></span>Draw, edit, and manage zones directly on a map.</p>
                             <p><span class="sub-headpara"><strong>Automated Dispatching::</strong></span>Assign drivers based on proximity within defined zones.</p>            
                             <p><span class="sub-headpara"><strong>Real-time Zone Tracking:</strong></span> Track vehicles and dispatch operations in real-time within your service areas.</p>
                             <p><span class="sub-headpara"><strong>Custom Payment Plans:</strong></span>Dynamic Zone Adjustments</p>
                            </div>
              </div>
            
            </div>
        </div>
    </div>
</section>

<section class="faq_section d-block d-lg-none">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <ul class="accordion-box clearfix">
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> CRM With Website</div>
                        <div class="acc-content p-2" style="display: none;">
                            <h2 class="tab-head">Complete CRM & Dispatch Software Integration for Your Business</h2>
                            <p>At Your Company Name, we are committed to providing seamless solutions for your 
                            business by integrating a powerful CRM with your website and dispatch 
                            software system. Our comprehensive approach ensures smooth operations, increased efficiency, and improved customer experience.</p>
                            <div class="para">
                                <h2 class="tab-head">Why Choose Our CRM & Dispatch System?</h2>
                                <div class="choose-text">
                                    <p><span class="sub-headpara"><strong>Unified Platform:</strong></span>Our integrated CRM and dispatch system allows you to manage 
                                    customer relationships, track leads, and dispatch vehicles in
                                    one centralized platform. This streamlines your operations, reduces 
                                    manual tasks, and ensures everyone is on the same page.</p>
                                    
                                     <p><span class="sub-headpara"><strong>Real-Time Dispatch Management:</strong></span>Stay on top of your fleet with real-time tracking and dispatch updates. Assign jobs quickly, monitor vehicle availability, and get live updates on driver status, all from your CRM dashboard.</p>
                                    
                                     <p><span class="sub-headpara"><strong>Customer Relationship Management:</strong></span>Enhance your customer interactions with advanced CRM tools. Our system captures customer data, tracks communication history, and automates follow-ups, making it easier to manage your relationships and build long-term loyalty.</p>
                                    
                                     <p><span class="sub-headpara"><strong>Custom Website Integration:</strong></span>We seamlessly integrate our dispatch and CRM software with your existing website, providing a smooth user experience for both your team and your customers. Online bookings, customer inquiries, and dispatch requests are all handled efficiently.</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Customizable Features</div>
                        <div class="acc-content p-2">
                            <h2>Customizable Features</h2>
                <h2 class="tab-head">Vehicle Selection</h2>
<p>At Your Company Name we understand the importance of providing flexibility and choice to your customers. Our CRM and dispatch system comes with a fully customizable Vehicle Selection feature that allows users to choose from a range of available vehicles that best suit their needs. Whether your customers need a luxury car, a standard ride, or a larger vehicle for group travel, our system lets them:</p>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Browse Vehicle Categories:</strong></span>Customers can easily filter by vehicle type, size, or special features.</p>
                            
                             <p><span class="sub-headpara"><strong>Real-Time Availability:</strong></span>The system updates in real-time, showing which vehicles are available at the moment of booking.</p>            
                             <p><span class="sub-headpara"><strong>Detailed Vehicle Information:</strong></span>Each vehicle comes with a description, images, and details such as passenger capacity, luggage space, and amenities</p>
                            
                             <p><span class="sub-headpara"><strong>Favorite Vehicles:</strong></span>Customers can save their preferred vehicles for quick selection on future bookings.</p>
                            </div>


                            <h2 class="tab-head">Choice of Payment Options</h2>
<p>We believe in offering convenience and flexibility when it comes to payments. Our system provides customers with multiple Payment Options at checkout, ensuring a hassle-free experience. Your business can customize these options based on what works best for you and your customers</p>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Credit/Debit Cards:</strong></span>Securely accept all major credit and debit cards through an integrated payment gateway.</p>
                             <p><span class="sub-headpara"><strong>PayPal & Digital Wallets:</strong></span>Offer customers the ease of using PayPal or other digital wallet services like Apple Pay and Google Pay.</p>            
                             <p><span class="sub-headpara"><strong>Cash on Delivery:</strong></span>For customers who prefer to pay in person, our system can allow cash or card payments upon arrival.</p>
                            
                             <p><span class="sub-headpara"><strong>Custom Payment Plans:</strong></span>Enable installment plans, corporate billing, or pre-paid packages for regular clients.</p>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Booking Form</div>
                        <div class="acc-content p-2">
                            <div class="para">
                                <h2 class="tab-head">Booking Form</h2>
                                <div class="choose-text">
                                    <p>The <span class="sub-headpara"><strong>Booking Form</strong></span> is a feature that allows the admin to create bookings manually and also enables automatic tracking of the customer journey and all other details related to the booking. It includes fields for necessary information such as pickup and drop-off locations, date and time, and ride preferences (e.g., type of vehicle). The form is user-friendly, designed to ensure smooth booking by collecting all the essential details from customers and providing a seamless booking experience.</p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Driver App / Passenger App</div>
                        <div class="acc-content p-2">
                            <h2>Driver App / Passenger App</h2>
                 <h2 class="tab-head">Driver App: Drive Smarter, Earn More</h2>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Instant Job Alerts:</strong></span>Get real-time ride notifications and never miss a request. </p>
                             <p><span class="sub-headpara"><strong>In-App Navigation:</strong></span>Find the quickest routes with integrated GPS.</p>            
                             <p><span class="sub-headpara"><strong>Track Earnings:</strong></span> View completed rides and daily earnings on the go. </p>
                             <p><span class="sub-headpara"><strong>Seamless Communication:</strong></span>Chat easily with passengers and dispatchers. </p>
                             <p><span class="sub-headpara"><strong>Driver Safety:</strong></span>Emergency contacts and route tracking for peace of mind.  </p>
                             <p><span class="sub-headpara"><strong>Flexible Schedule:</strong></span> Set your hours, drive when it suits you.</p>
                             <p>Drive with ease. Earn with confidence.</p>
                            </div>
                            
                             <h2 class="tab-head">Passenger App: Your Ride, Your Way</h2>
                    <div class="choose-text">
                            <p><span class="sub-headpara"><strong>Quick Booking: </strong></span>Book a ride in seconds, anytime, anywhere. </p>
                             <p><span class="sub-headpara"><strong>Real-Time Tracking</strong></span>Track your driver’s location in real-time. </p>            
                             <p><span class="sub-headpara"><strong>Transparent Pricing</strong></span> Get an instant fare estimate before you ride. </p>
                             <p><span class="sub-headpara"><strong>Ride History:</strong></span> Access past rides and receipts effortlessly.</p>
                             <p><span class="sub-headpara"><strong>Safety First:</strong></span> Share ride details with loved ones for extra security.</p>
                             <p>Safe, simple, and seamless rides, whenever you need them. </p>
                            </div>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Promo Code</div>
                        <div class="acc-content p-2">
                            <h2>Promocode</h2>
                            <p>Unlock the full potential of your cab or car dispatch business with our cutting-edge software! For a limited time, use PROMO20 at checkout to get 20% off your first month. Simplify dispatching, enhance real-time tracking, and boost customer satisfaction today </p>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Offer Day</div>
                        <div class="acc-content p-2">
                            <h2>Offer Day: Create Special Promotions for Any Day </h2>
                <p>With our CRM, you can easily create Offer Days—special promotions tied to a specific day or date. Whether it's a holiday, anniversary, or any other special occasion, you can set up an offer that’s only available for that day. </p>
                <h4><strong>How It Works:</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Choose a Day:</strong> Select the exact day you want your offer to run.</li>
                    <li><strong class="sub-headpara">Automatic Activation:</strong> The offer will automatically become available on the chosen day and expire when the day ends. </li>
                    <li><strong class="sub-headpara">Limited-Time Offers:</strong> Create a sense of urgency and excitement by offering exclusive deals for one day only. </li>
                </ul>
                <h4><strong>Why Use Offer Day? </strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Boost Sales for Special Days:</strong> Drive more sales by offering discounts or deals on important dates. </li>
                    <li><strong class="sub-headpara">Easy Setup:</strong> Simply select the day, and our CRM takes care of the rest. </li>
                    <li><strong class="sub-headpara">Create Excitement:</strong> Limited offers for a specific day keep customers coming back for more. </li>
                </ul>
                <p>With Offer Day, you can make any day special with a time-limited promotion! </p>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Offer Time</div>
                        <div class="acc-content p-2">
                            <h2>Offer Time: Manage Your Promotions with Ease </h2>
                <p>With our CRM, you can easily create limited-time offers to boost your sales. The Offer Time feature lets you set specific start and end dates for any promotion. </p>
                <h4><strong>How It Works:</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Set Time Periods:</strong> Choose exactly when your offer starts and ends. </li>
                    <li><strong class="sub-headpara">Automatic Updates:</strong> The system automatically makes the offer available during the set time, and it expires when the time is up. </li>
                    <li><strong class="sub-headpara">Create Urgency:</strong> Limited-time offers encourage customers to act quickly before the deal expires. </li>
                </ul>
                <h4><strong>Why Use Offer Time?</strong></h4>
                <ul>
                    <li><strong class="sub-headpara">Increase Sales:</strong> Time-limited promotions drive more purchases. </li>
                    <li><strong class="sub-headpara">Easy Management:</strong> The system handles everything automatically. </li>
                    <li><strong class="sub-headpara">Track Results:</strong> See how well your offer performs during its active period. 

With  <strong>Offer Time,</strong> creating and managing promotions has never been easier!  </li>
                </ul>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Zone Drawing</div>
                        <div class="acc-content p-2">
                            <h2>Zone Management Overview</h2>
                <p>Our dispatch software offers a powerful Zone Drawing feature, allowing you to visually define service areas for optimal fleet management and faster dispatching. Whether you’re handling multiple cities, regions, or custom service areas, our software helps you stay in control.</p>
                        </div>
                    </li>
                    <li class="accordion block">
                        <div class="acc-btn"><span class="count"></span> Add Location (Range)</div>
                        <div class="acc-content p-2">
                            <h2>Add Location Range</h2>
                                <p>The Add Location (Range) feature allows users or administrators to set specific locations and define the service range for the taxi dispatch system. This can be used to specify areas where the service operates and the distance limits for a ride. This feature helps optimize ride dispatch and ensures services are available in predefined zones.</p>
                              <h3>Key Features:</h3>
                            <div class="choose-text">
                                <p><span class="sub-headpara"><strong>Customizable Zones:</strong></span>Draw, edit, and manage zones directly on a map.</p>
                                 <p><span class="sub-headpara"><strong>Automated Dispatching:</strong></span>Assign drivers based on proximity within defined zones.</p>            
                                 <p><span class="sub-headpara"><strong>Real-time Zone Tracking:</strong></span> Track vehicles and dispatch operations in real-time within your service areas.</p>
                                 <p><span class="sub-headpara"><strong>Custom Payment Plans:</strong></span>Dynamic Zone Adjustments</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
   <script src="https:cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <script>
    $(".tab_content").hide();
    $(".tab_content:first").show();

  /* if in tab mode */
    $("ul.tabs li").click(function() {
		
      $(".tab_content").hide();
      var activeTab = $(this).attr("rel"); 
      $("#"+activeTab).fadeIn();		
		
      $("ul.tabs li").removeClass("active");
      $(this).addClass("active");

	  $(".tab_drawer_heading").removeClass("d_active");
	  $(".tab_drawer_heading[rel^='"+activeTab+"']").addClass("d_active");
	  
    /*$(".tabs").css("margin-top", function(){ 
       return ($(".tab_container").outerHeight() - $(".tabs").outerHeight() ) / 2;
    });*/
    });
    $(".tab_container").css("min-height", function(){ 
      return $(".tabs").outerHeight() + 50;
    });
	/* if in drawer mode */
	$(".tab_drawer_heading").click(function() {
      
      $(".tab_content").hide();
      var d_activeTab = $(this).attr("rel"); 
      $("#"+d_activeTab).fadeIn();
	  
	  $(".tab_drawer_heading").removeClass("d_active");
      $(this).addClass("d_active");
	  
	  $("ul.tabs li").removeClass("active");
	  $("ul.tabs li[rel^='"+d_activeTab+"']").addClass("active");
    });
	
	
</script>
@endsection
