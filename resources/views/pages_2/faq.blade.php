@extends('layouts.app')

@section('content')

    <style>
        .page-header.faq {
            height:370px !important;
        }
        .faq_section {
            padding: 100px 0;
        }
        
        @media screen and (max-width: 576px) {
            
            .faq_section {
                padding: 50px 0;
            }
            
        }
        
    </style>

    <!-- Breadcrumb -->

    <section class="page-header faq">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>FAQ</h4>
                <h1>Frequently Asked <span>Questions</span></h1>
                <!--<p>Explore our affordable and customizable pricing options designed to fit businesses of all sizes.</p>-->
            </div>
        </div>
    </section>
    
    <section class="faq_section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10">
                    <ul class="accordion-box clearfix">
                        <li class="accordion block active-block">
                            <div class="acc-btn active"><span class="count">1.</span> What is Go Ride?</div>
                            <div class="acc-content" style="display: block;">
                                <div class="content">
                                    <div class="text">Go Ride is a platform that offers customizable taxi dispatch software and website creation services. It enables businesses to build their own taxi booking websites with integrated dispatch systems, offering features like domain setup, branding customization, and a seamless user experience. 
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">2.</span> How do I sign up for Go Ride? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">To get started with Go Ride, simply visit our website and follow the registration process: <a href="/signup"> Go Ride Signup. </a> </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">3.</span> How does the payment process work?</div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">Payments are processed securely through our website. After completing your order and payment, you will receive a confirmation email with all the necessary details to set up your services. 
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">4.</span> Can I cancel my order or get a refund? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">Unfortunately, we do not offer cancellations or refunds once an order is processed, as stated in our Terms and Conditions. All sales are final. However, refunds may be considered in exceptional cases, such as technical issues with payment processing or service errors, at our discretion. </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">5.</span> Can I customize the taxi dispatch website? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">Yes! After completing your payment, you can customize your dispatch website according to your preferences. You will have control over the layout, design, and features, allowing you to create a website that suits your brand and business needs. </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">6.</span> Will Go Ride provide a domain for my website? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">Yes, upon successful payment, you will receive a uniquely custom domain for your taxi dispatch website. e.g., [yourcompany].goride.run)  </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">7.</span> How do I access my website and dispatch software after purchase?</div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">Once your payment is confirmed, you will receive an email with payment confirmation details. In your dashboard, you will find instructions on how to access your website and dispatch software tools, allowing you to begin the setup process. </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">8.</span>  Can I change my domain name after purchase? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">The domain name is provided once your order is processed. If you wish to change it, please contact our support team at <a href='mailto:support@goride.run'> support@goride.run</a> Changes may be subject to availability and additional charges. </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">9.</span> What happens if there is an issue with my website or software? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">If you encounter any technical issues, please contact our support team immediately at <a href='mailto:support@goride.run'>support@goride.run</a> We will assist you in resolving any problems related to your website or dispatch software. </div>
                                </div>
                            </div>
                        </li>
                        <li class="accordion block">
                            <div class="acc-btn"><span class="count">10.</span> How can I contact Go Ride for support? </div>
                            <div class="acc-content">
                                <div class="content">
                                    <div class="text">If you need assistance or have any questions, you can contact our support team via the Contact Us page on our website or by email at <a href="mailto:support@goride.run"> support@goride.run</a> We strive to respond to all inquiries as quickly as possible. </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
        integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>

        var wow = new WOW({
            boxClass: 'wow', // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset: 0, // distance to the element when triggering the animation (default is 0)
            mobile: true, // trigger animations on mobile devices (default is true)
            live: true, // act on asynchronously loaded content (default is true)
            callback: function(box) {
                // the callback is fired every time an animation is started
                // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null // optional scroll container selector, otherwise use window
        });
        wow.init();
    </script>
@endsection
