@extends('layouts.app')

@section('css')

<style>
    
.section-heading h2 span, .section-heading h1 span {
    color: #f8be00;
}

.privacy_policy_section ul li {
    margin-bottom: 15px;
}

.text-theme {
    color: #f8be00 !important;
}

.page-header {
    height: 390px;
}

.privacy_policy_section {
    padding-top: 80px;
}

.navbar .navbar-nav .nav-link, .flaticon-phone-call {
    color: #000 !important;    
}
    
</style>

@endsection

@section('content')

    <!-- Breadcrumb -->

    <!--<section class="page-header">-->
    <!--    <div class="page-header-shape"></div>-->
    <!--    <div class="container">-->
    <!--        <div class="page-header-info">-->
                <!--<h4>Join the GoRide Community!</h4>-->
    <!--            <h2>Start Your Journey <br> with <span>GoRide!</span></h2>-->
    <!--            <p>Sign up today and take the first step towards a seamless ride experience.</p>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    
 <section class="privacy_policy_section">
  <div class="container">
    <div class="row">
      <div class="col-md-12">

        <!-- Section Heading -->
        <div class="section-heading my-4 text-center">
          <h1 class="wow fadeIn" data-wow-duration="2s">Privacy <span>Policy</span></h1>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs justify-content-center" id="policyTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active me-3" id="policy1-tab" data-bs-toggle="tab" data-bs-target="#policy1"
              type="button" role="tab" aria-controls="policy1" aria-selected="true">GoRide General</button>
          </li>
          <li class="nav-item me-3" role="presentation">
            <button class="nav-link" id="policy2-tab" data-bs-toggle="tab" data-bs-target="#policy2"
              type="button" role="tab" aria-controls="policy2" aria-selected="false">Jobs</button>
          </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content mt-4" id="policyTabsContent">

          <!-- Policy 1 -->
          <div class="tab-pane fade show active" id="policy1" role="tabpanel" aria-labelledby="policy1-tab">
            <div class="privacy-content">
              <p>Go Ride ("we", "us", "our") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you visit or use our website <a href="https://goride.run/">https://goride.run/</a></p>
              <p>By accessing or using the Site, you consent to the collection and use of your information in accordance with this Privacy Policy. If you do not agree with the practices described in this Privacy Policy, please do not use the Site. </p>
              <ul>
                <li>
                  <h4>1. Information We Collect </h4>
                  <p>We collect different types of information from or about you in the following ways: </p>
                  <p class="ps-3">A. Personal Information </p>
                  <p class="ps-3">Personal information is data that can be used to identify you or contact you, such as:  </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Name</p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Email address </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Phone number </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Billing and shipping addresses </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Payment information (when applicable) </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Account username and password (if you create an account) </p>
                  <p class="ps-3">We collect personal information when you register for an account, subscribe to our newsletter, make a purchase, contact customer support, or interact with our Site in other ways. </p>
                  <p>B. Non-Personal Information </p>
                  <p class="ps-3">Non-personal information is data that does not directly identify you. This may include: </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> IP address </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Browser type  </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Device type  </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Operating system  </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Referring URLs  </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Pages visited   </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Session duration   </p>
                  <p class="ps-5"><i class="fa-regular fa-chevron-right"></i> Cookies     </p>
                </li>
              
                <li>
                  <h4>2. How We Use Your Information </h4>
                  <p>We use the information we collect for various purposes, including but not limited to: </p>
                  <p class="ps-3">Providing and improving our services: To provide you with the products and services you request, process transactions, and improve the functionality of our Site. </p>
                  <p class="ps-3"><strong>Communication: </strong>To communicate with you about your account, orders, and any inquiries or requests. This includes sending order confirmations, updates, and promotional offers (if you’ve opted into such communications). </p>
                  <p class="ps-3"><strong>Personalization: </strong>To personalize your experience on the Site based on your preferences and browsing behavior. </p>
                  <p class="ps-3"><strong>Legal obligations: </strong>To comply with applicable laws, regulations, and legal processes, and protect our rights and the rights of others. </p>
                  <p class="ps-3"><strong>Marketing: </strong> To send you marketing materials if you have opted in to receive them. You can opt out of these communications at any time. </p>
                </li>
              
                <li>
                  <h4>3. How We Share Your Information </h4>
                  <p>We do not sell, rent, or trade your personal information to third parties. However, we may share your information in the following circumstances: </p>
                  <p class="ps-3"><b>Service Providers: </b>We may share your information with third-party service providers who assist us in running our business, including payment processors, shipping companies, email service providers, and analytics providers. These providers are obligated to handle your information securely and use it only for the purposes for which it was provided. </p>
                  <p class="ps-3"><b>Legal Compliance:</b> We may disclose your information to comply with legal obligations, enforce our policies, protect the rights, property, or safety of Go Ride, our users, or others, or respond to lawful requests by public authorities. </p>
                </li>
              
                <li>
                  <h4>4. Cookies and Tracking Technologies </h4>
                  <p>We use cookies and other tracking technologies to enhance your experience on our website. Cookies are small files that are stored on your device and allow us to collect and store certain information about your preferences and activity on our Site. </p>
                  <p>We use cookies for the following purposes: </p>
                  <p class="ps-3"><strong>Session management: </strong>To remember your login status and preferences during your visit. </p>
                  <p class="ps-3"><b>Analytics: </b>To analyze how visitors use our website and improve its functionality. </p>
                  <p class="ps-3"><b>Marketing:</b> To track user behavior and deliver personalized advertisements based on your interests. </p>
                  <p>You can control cookie settings through your browser. However, if you choose to disable cookies, certain features of the Site may not function as intended. </p>
                </li>
              
                <li>
                  <h4>5. Data Security </h4>
                  <p>We take reasonable precautions to protect the security of your personal information. We use industry-standard encryption and security technologies to safeguard your data during transmission and storage. However, no method of electronic transmission or storage is completely secure, and we cannot guarantee the absolute security of your data. </p>
                </li>
              
                <li>
                  <h4>6. Your Rights and Choices</h4>
                  <p>Depending on your location, you may have certain rights regarding your personal data. These rights may include the right to: </p>
                  <p class="ps-3"><b>Access:</b> Request access to the personal data we hold about you. </p>
                  <p class="ps-3"><b>Correction:</b> Request corrections to inaccurate or incomplete data.</p>
                  <p class="ps-3"><b>Deletion:</b> Request the deletion of your personal data, subject to certain conditions.</p>
                  <p class="ps-3"><b>Opt-Out:</b> Opt out of receiving marketing communications at any time by following the instructions in the email or contacting us directly.</p>
                  <p class="ps-3"><b>Data Portability:</b> Request a copy of your personal data in a commonly used, machine-readable format. </p>
                  <p class="ps-3">To exercise any of these rights, please contact us at <a href="tel:+916369742104" style=" text-decoration: underline;"> +91 63697 42104</a>. We will respond to your request in accordance with applicable law. </p>
                </li>
              
                <li>
                  <h4>7. Third-Party Links </h4>
                  <p>Our website may contain links to third-party websites, products, or services. We are not responsible for the privacy practices or the content of these third-party websites. We encourage you to review the privacy policies of any third-party websites you visit. </p>
                </li>
              
                <li>
                  <h4>8. Children’s Privacy </h4>
                  <p>Our services are not intended for individuals under the age of 13, and we do not knowingly collect personal information from children under 13. If we learn that we have inadvertently collected personal information from a child under 13, we will take steps to delete such information from our records. </p>
                </li>
              
                <li>
                  <h4>9. Changes to This Privacy Policy </h4>
                  <p>We reserve the right to update or change this Privacy Policy at any time. If we make changes, we will update the Effective Date at the top of this page. We encourage you to review this Privacy Policy periodically to stay informed about how we are protecting your information. </p>
                </li>
              
                <li>
                  <h4>10. Contact Us</h4>
                  <p>If you have any questions or concerns about this Privacy Policy, or if you would like to exercise any of your rights, please contact us: <a href="mailto:support@goride.run">support@goride.run</a> </p>
                </li>
                  <li>
                  <h4>11. User Data Consent & Sharing</h4>
                  <p class="ps-3">Go Rides <b>does not sell, rent, or commercially exploit</b> user data. </p>
                  <p class="ps-3">The contact information you provide (such as phone number or email) is <b>publicly visible only to other registered users </b> of the Go Rides platform <b>for communication related to employment, vehicle hire, or related services. </b></p>
                  <p class="ps-3">Go Rides acts <b>solely as an information-sharing Platform</b> and<b> is not involved</b> in any agreements, payments, or communication between users.</p>
                  <p>By submitting your information, you consent to:</p>
                  <p class="ps-3">Displaying your details on the Go Rides website for discovery by other users</p>
                  <p class="ps-3">Communication from other users regarding listings or job opportunities</p> 
                   <p class="ps-3">Our storage and handling of your information in accordance with this Privacy Policy</p>
                   <p>Users may request deletion or modification of their listings or personal data by contacting support@goride.run</p>
                  <p><b>Data Protection</b></p>
                  <p>All data is stored securely. Access is restricted to authorized personnel.</p>
                  <p>We comply with the <b>Digital Personal Data Protection Act, 2023 (India)</b> in handling and protecting user information.</p>
                </li>
              </ul>
            </div>
          </div>

          <!-- Policy 2 (empty for now, you can duplicate if needed) -->
          <div class="tab-pane fade" id="policy2" role="tabpanel" aria-labelledby="policy2-tab">
            <div class="privacy-content">
              <!-- 🔹 Policy 2 content (the one you pasted) -->
              <section class="terms-section">
                <div class="container">
                
                  <p >
                    GoRide Platform respects your privacy.</p>
                    <p>This Privacy Policy explains how we collect, use, and protect your information when you use our platform.</p>
                  </p>

                  <!-- Sections -->
                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4> 1.Information We Collect</h4>
                    </div>
                    <ul>
                      <p class="mb-3">When you use GoRide, we may collect:</p>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Personal Information: Name, phone number, email address, profile details.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Business/Vehicle Details: Cab company name, license number, vehicle information, job postings.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Usage Data: Device details, IP address, browser type, location data, and activity logs</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Communication Records: Messages, bids, and interactions made through the platform.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>2.How We Use Your Information</h4>
                    </div>
                    <ul>
                      <p class="mb-3">We use collected information to:</p>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Provide and operate the GoRide platform.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Enable job postings, bidding, and communication between Drivers and Cab Owners/Companies.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Improve security, verify user identities, and prevent fraud.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Send important notifications (updates, changes in terms, commission policy, or services).</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Improve user experience, analytics, and platform performance.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>3.Data Sharing & Disclosure</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>We do not sell or trade your personal information.</li>
                      <p class="mb-3">We may share limited information with:</p>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Other users (to facilitate job postings and contact).</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Service providers (for hosting, analytics, or support).</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Legal authorities (if required by law, fraud, or misuse cases).</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>4.Data Security</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>We implement reasonable technical and organizational measures to protect your data.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>However, no method of transmission over the internet is 100% secure; users share information at their own risk.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>5.Cookies & Tracking</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>We may use cookies and similar technologies for analytics, personalization, and security.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Users can control cookies through browser settings, but some features may not function properly if disabled.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>6.Retention of Data</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>We retain your data as long as necessary for providing services and complying with legal obligations.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Inactive accounts may be deleted after a certain period.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>7.Platform Rights</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>GoRide reserves the right to update, modify, or discontinue services at any time.</li>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>Changes to this Privacy Policy will be notified via the platform or email.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>8.Governing Law</h4>
                    </div>
                    <ul>
                      <li><i class="fas fa-check-circle me-2 text-secondary"></i>This Privacy Policy shall be governed by the laws of India, but applies to our global users.</li>
                    </ul>
                  </div>

                  <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>9.Contact Us</h4>
                    </div>
                    <ul>
                      <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                        For privacy-related concerns or requests, contact us at:
                        <a href="mailto:support@goride.run"><i class="fas fa-envelope ms-1"></i> support@goride.run</a>
                      </li>
                    </ul>
                  </div>
                   <div class="mb-4">
                    <div class="site-heading mb-3">
                      <h4>10.User Data Consent & Sharing</h4>
                    </div>
                    <ul>
                      <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                          Go Rides <b>does not sell, rent, or commercially exploit</b> user data. 
                      </li>
                       <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                         The contact information you provide (such as phone number or email) is <b>publicly visible only to other registered users </b> of the Go Rides platform <b>for communication related to employment, vehicle hire, or related services. </b>
                      </li> <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                          Go Rides acts <b>solely as an information-sharing Platform</b> and<b> is not involved</b> in any agreements, payments, or communication between users.
                      </li>
                    </ul>
                     <p>By submitting your information, you consent to:</p>
                     <ul>
                      <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                          Displaying your details on the Go Rides website for discovery by other users 
                      </li>
                       <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                       Communication from other users regarding listings or job opportunities </b>
                      </li> <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                           Our storage and handling of your information in accordance with this Privacy Policy
                      </li>
                    </ul>
                    <p>Users may request deletion or modification of their listings or personal data by contacting support@goride.run</p>
                     <p>Data Protection</p>
                       <ul>
                      <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                         All data is stored securely. Access is restricted to authorized personnel.
                      </li>
                       <li>
                        <i class="fas fa-check-circle me-2 text-secondary"></i>
                        We comply with the <b>Digital Personal Data Protection Act, 2023 (India)</b> in handling and protecting user information. </b>
                      </li> 
                    </ul>
                  </div>

                  <p class="mt-4 fw-bold text-center">
                    By using GoRide, you confirm that you have read, understood, and agreed to these Privacy & Policy.
                  </p>
                </div>
              </section>
            </div>
          </div>


        </div><!-- End tab-content -->

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
