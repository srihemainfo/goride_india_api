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
}    
</style>

@endsection

@section('content')

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info main-banner-content">
                <!--<h4>About Us!</h4>-->
                <h1>Cab Booking & Dispatch Software</h1>
                <p>Unlock smart mobility by leveraging the power of our next-gen cab booking software with advanced dispatch system</p>
                <div class="banner-btn">
                    <!--<a href="#" class="default-btn-one">More About Us</a>-->
                    <!--<a href="https://www.youtube.com/watch?v=_ysd-zHamjk" class="video-btn popup-youtube">Start a Free Trail<i class="fa-solid fa-arrow-right"></i></a>-->
                </div>
            </div>
        </div>
    </section>
    
    <section class="choose-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="choose-content-area">
                        <span>Dedicated CRM</span>
                        <h3>GoRide Will Help Take Your Cab Business To The Next Level</h3>
                        <p>"Customer Relationship Management." It refers to a system or software designed to manage a company’s interactions with current and potential customers. You mentioned that you developed and customized the CRM's features while ensuring the content was accurate—what were some of the key customizations you implemented</p>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Contact Management</h4>
                            <p>Store and manage customer and prospect information, such as names, contact details, and interaction history</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Sales Management</h4>
                            <p>Track sales opportunities, pipelines, and deals in different stages, from lead generation to closing.</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Lead Management</h4>
                            <p>Capture, track, and manage potential customers through the sales funnel.</p>
                        </div>

                        <div class="choose-btn">
                            <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="choose-image">
                        <img src="{{ asset('goride/img/custom-plan/crm.webp') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="choose-section">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6 col-md-12 order-2 order-lg-1">
                    <div class="choose-image">
                        <img src="{{ asset('goride/img/custom-plan/dashboard.png') }}" alt="image">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 order-1 order-lg-2">
                    <div class="choose-content-area">
                        <span>Dispatch Software</span>
                        <h3>Design your website exactly the way you want with our fully customizable solutions</h3>
                        <p>We develop and design websites with 100% customization tailored to your vision and requirements. Our team is highly skilled, and we take pride in delivering fully customized solutions that ensure complete client satisfaction</p>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Customization</h4>
                            <p>Website design customization gives you full control over the look, functionality, and user experience to perfectly match your brand.</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Design</h4>
                            <p>Design your website with 100% satisfaction, fully tailored to your vision and needs</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Implementation</h4>
                            <p>Implement your website design with precision, ensuring every detail aligns with your vision and functions flawlessly.</p>
                        </div>

                        <div class="choose-btn">
                            <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                        </div>
                    </div>
                </div>

           
            </div>
        </div>
    </section>

    <section class="choose-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="choose-content-area">
                        <span>Driver App</span>
                        <h3>Streamline Your Driving Experience with Our Advanced Driver App</h3>
                        <p>A driver app provides essential tools for drivers, including navigation, ride requests, and real-time tracking. It enables efficient route management, helps with fare calculations, and supports communication with passengers.</p>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Real-Time Navigation</h4>
                            <p>Provides accurate, turn-by-turn directions to help drivers reach their destinations efficiently.</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Ride Request Management</h4>
                            <p>Allows drivers to accept or decline ride requests, view passenger details, and manage their schedule</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Real-Time Tracking</h4>
                            <p>Enables drivers to track their location and view incoming ride requests on a map for optimal route planning</p>
                        </div>

                        <div class="choose-btn">
                            <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="choose-image">
                        <img src="{{ asset('goride/img/custom-plan/driver-app-mockup.webp') }}" alt="image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="choose-section">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-6 col-md-12 order-2 order-lg-1">
                    <div class="choose-image">
                        <img src="{{ asset('goride/img/custom-plan/passenger-app-mockup.webp') }}" alt="image">
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 order-1 order-lg-2">
                    <div class="choose-content-area">
                        <span>Passenger App</span>
                        <h3>Enhance Your Experience with Our User-Friendly Passenger App</h3>
                        <p>A Passenger App simplifies the booking process with intuitive scheduling, real-time tracking, and secure payment options. It enhances user experience by providing seamless access to services and up-to-date information at their fingertips.</p>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Easy Booking and Scheduling</h4>
                            <p>Allows customers to book services or schedule rides with a few taps, view available options, and receive instant confirmations.</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Real-Time Tracking</h4>
                            <p>Provides live tracking of the service provider or vehicle, so customers can monitor their arrival and plan accordingly.</p>
                        </div>

                        <div class="choose-text">
                            <i class="fa-regular fa-check"></i>
                            <h4>Seamless Payment Integration</h4>
                            <p>Supports secure and convenient payment options, including credit cards and digital wallets, with options to view and manage transaction history.</p>
                        </div>

                        <div class="choose-btn">
                            <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                        </div>
                    </div>
                </div>

           
            </div>
        </div>
    </section>

    <!-- Start Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-title">
                <span>Services</span>
                <h3>How We Can Help?</h3>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/calender.png') }}" alt="icon">
                                </div>
                        <h3>Real-Time Booking & Dispatch</h3>
                        <p>Fast & easy booking process with advance booking option & smart algorithm to match available nearest drivers with riders' requirements</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/driver.png') }}" alt="icon">
                        </div>

                        <h3>Driver Management  Tools</h3>
                        <p>Advanced driver management features, including managing driver profiles, vehicle details, performance analytics, & operating & monitoring.</p>        </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/map.png') }}" alt="icon">
                        </div>

                        <h3>GPS Tracking and Navigation</h3>
                        <p>AI-based GPS tracking to ensure accurate real-time location of the vehicle with integrated navigation tools to help drivers find the most efficient routes</p>         </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/file.png') }}" alt="icon">
                        </div>

                        <h3>Automated Fare</h3>
                       <p>Automated accurate fare calculation based on distance, time, & additional charges ensuring transparency in pricing & allowing the riders.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/gateway.png') }}" alt="icon">
                        </div>

                        <h3>Multiple Payment Options</h3>
                        <p>Secure and flexible payment options integrating multiple payment methods, such as credit/debit cards, digital wallets, or many other forms.</p>            </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="single-services-box">
                        <div class="icon">
                            <img src="{{ asset('goride/img/custom-plan/alarm.png') }}" alt="icon">   
                        </div>

                        <h3>Automated Alerts/Notifications</h3>
                        <p>Automated alerts about the latest status of the ride, including driver acceptance, estimated arrival time, and trip completionFre</p>           </div>
                </div>
            </div>
        </div>  

        <div class="default-animation">
            <div class="shape-img1"><img src="{{ asset('goride/img/custom-plan/12.svg') }}" alt="image"></div>
            <div class="shape-img2"><img src="{{ asset('goride/img/custom-plan/13.svg') }}" alt="image"></div>
            <div class="shape-img3"><img src="{{ asset('goride/img/custom-plan/14.png') }}" alt="image"></div>
            <div class="shape-img4"><img src="{{ asset('goride/img/custom-plan/15.png') }}" alt="image"></div>
            <div class="shape-img5"><img src="{{ asset('goride/img/custom-plan/2.png') }}" alt="image"></div>
        </div>  
    </section>
    <!-- End Services Section -->

    <!-- Start Tab Section -->
    <section class="tab-section ptb-100">
        <div class="container">
            <div class="section-title">
                <span>Boosting</span>
                <h3>Outstanding Digital Experience</h3>
            </div>

            <div class="tab boosting-list-tab">
                <ul class="tabs">
                    <li class="current">
                        <a href="#">
                            <img src="{{ asset('goride/img/custom-plan/assignment.png') }}" alt="icon" style="width: 50%;">
                            <span>Assignment</span>
                        </a>
                    </li>
                    
                    <li class="bg-ecfaf7 std1" ><a href="#">
                        <img src="{{ asset('goride/img/custom-plan/tracking.png') }}" alt="icon" style="width: 50%;">
                        <span> GPS Tracking</span>
                    </a></li>
                    
                    <li class="bg-ecfaf7 std2"><a href="#">
                        <img src="{{ asset('goride/img/custom-plan/route.png') }}" alt="icon" style="width: 50%;">
                        <span>Route Optimization</span>
                    </a></li>
                    
                    <li class="bg-ecfaf7 std3"><a href="#">
                        <img src="{{ asset('goride/img/custom-plan/chat.png') }}" alt="icon" style="width: 50%;">
                        <span> Communication</span>
                    </a></li>
                    
                    <li class="bg-ecfaf7 std4"><a href="#">
                        <img src="{{ asset('goride/img/custom-plan/analytics.png') }}" alt="icon" style="width: 50%;">
                        <span>Analytics 
                        </span>
                    </a></li>

                    <li class="bg-ecfaf7 st5"><a href="#">
                        <img src="{{ asset('goride/img/custom-plan/real-time-strategy.png') }}" alt="icon" style="width: 50%;">
                        <span>Real-Time Updates</span>
                    </a></li>
                </ul>

                <div class="tab_content">
                    <div class="tabs_item" style="display: block;">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/1_1.jpg') }}" alt="icon" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Automated Job Assignment and Scheduling</h2>
                                    <p>Dispatch software uses AI algorithms to automatically assign jobs to drivers based on factors like location, availability, vehicle capacity, and job priority. This minimizes manual intervention and ensures that resources are used efficiently.</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>The software automatically assigns jobs to the best-suited drivers based on factors like proximity, availability, vehicle capacity, and driver skills. This ensures that the right driver is assigned to the right task every time.</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Scheduling is adjusted in real-time based on job priority, driver availability, and traffic conditions. Urgent jobs are prioritized, and any last-minute changes are seamlessly incorporated into the schedule.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>

                    <div class="tabs_item" id="standard" style="display: none;" >
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/2_1.jpg') }}" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Real-Time GPS Tracking</h2>
                                    <p>Provides live tracking of drivers and vehicles, allowing dispatchers to monitor routes, track job progress, and adjust operations dynamically based on real-time data. This enhances operational control and customer communication.</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Increased Safety: Monitor the location of vehicles and drivers to enhance safety and provide timely assistance in emergencies.</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Improved Efficiency: Businesses can streamline their operations, optimize routes, and reduce fuel consumption by tracking vehicles and assets.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>

                    <div class="tabs_item" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/3_1.jpg') }}" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Route Optimization</h2>
                                    <p>The software optimizes routes based on real-time traffic data, distance, and time constraints, reducing fuel costs, improving delivery times, and maximizing driver efficiency. Dynamic re-routing is also supported in case of unexpected delays.</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Increased Productivity: Optimizing routes means drivers spend less time on the road and can complete more deliveries or service calls in a day.</p>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Improved Customer Satisfaction: Faster, on-time deliveries result in happier customers, leading to higher retention rates and positive reviews.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>

                    <div class="tabs_item" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/4_2.jpg') }}" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Seamless Communication</h2>
                                    <p>In-app messaging and push notifications enable direct communication between dispatchers, drivers, and customers. Dispatchers can send job details, updates, and alerts, while drivers can report issues or update job statuses on the go.</p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Improved Productivity: Employees can focus on their tasks without needing to juggle multiple communication tools, leading to faster decision-making and collaboration.</p> </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Enhanced Customer Experience: Clients receive consistent and timely responses, no matter the channel they use to reach out, resulting in better customer service and satisfaction</p></div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>

                    <div class="tabs_item" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/5_1.jpg') }}" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Analytics and Reporting</h2>
                                    <p>Provides detailed analytics on job performance, driver efficiency, vehicle usage, and customer satisfaction. These reports help businesses identify trends, make data-driven decisions, and improve future operations.</p></div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Data Collection: Gathering relevant data from various sources, such as databases, surveys, and sensors.</p> </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                           <p>Data Processing: Cleaning, organizing, and preparing data for analysis to ensure accuracy and relevance.</p></div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>

                    <div class="tabs_item" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-lg-5">
                                <div class="tab-image">
                                    <img src="{{ asset('goride/img/custom-plan/new.png') }}" alt="image">
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="content">
                                    <h2>Customer Integration and Real-Time Updates</h2>
                                    <p>The software integrates with customer-facing platforms to allow bookings, track deliveries, and send real-time notifications. This improves customer experience by providing accurate ETAs, delivery confirmations, and transparency throughout the service.</p></div>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                           <p>Immediate Awareness: Users receive the latest information instantly, keeping them informed of the most current developments and changes.</p> </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="tab-text-content">
                                            <i class="flaticon-analysis-2"></i>
                                            <p>Quick Decision-Making: With up-to-date information, individuals and organizations can make informed decisions and take timely actions based on the latest data.</p> </div>
                                    </div>
                                </div>

                                <div class="tab-btn">
                                    <!-- <a href="#" class="default-btn-one">Discover More</a> -->
                                </div>
                            </div>
                        </div>

                        <div class="tab-shape">
                            <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection

@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" ></script>

   <script>
    // Get all tab links
    const tabLinks = document.querySelectorAll('.tabs li');

    // Get all tab content items
    const tabContent = document.querySelectorAll('.tabs_item');

    // Add click event listener to each tab link
    tabLinks.forEach((tab, index) => {
        tab.addEventListener('click', function(event) {
            event.preventDefault();

            // Remove 'current' class from all tabs and hide all tab content
            tabLinks.forEach(link => link.classList.remove('current'));
            tabContent.forEach(content => content.style.display = 'none');

            // Add 'current' class to the clicked tab and show the corresponding tab content
            tab.classList.add('current');
            tabContent[index].style.display = 'block';
        });
    });

    // Optional: Set the first tab to be active by default on page load
    document.querySelector('.tabs li').click();
</script>

    
@endsection
