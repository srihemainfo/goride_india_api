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
    
<section class="privacy_policy_section ">
  <div class="container">
    <div class="section-heading my-4 text-center">
      <h1 class="wow fadeIn" data-wow-duration="2s">
        Terms & <span>Conditions</span>
      </h1>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs justify-content-center" id="policyTab" role="tablist">
      <li class="nav-item me-3" role="presentation">
        <button class="nav-link active" id="term1-tab" data-bs-toggle="tab" data-bs-target="#term1" type="button" role="tab" aria-controls="term1" aria-selected="true">
          GoRide General
        </button>
      </li>
      <li class="nav-item me-3" role="presentation">
        <button class="nav-link" id="term2-tab" data-bs-toggle="tab" data-bs-target="#term2" type="button" role="tab" aria-controls="term2" aria-selected="false">
         Jobs
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="policyTabContent">
      <!-- TERM 1 (Full Content) -->
      <div class="tab-pane fade show active" id="term1" role="tabpanel" aria-labelledby="term1-tab">
        <div class="privacy-content">
          <p>Welcome to Go Ride!</p>
          <ul>
            <li>
              <p>By accessing and using this website, you agree to comply with these Terms and Conditions. If you do not agree to these terms, you should not use this website. </p>
              <p>The following terms are used throughout these Terms and Conditions, Privacy Policy, Disclaimer, and all associated agreements:  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> "Client," "You," and "Your" refer to you, the individual using or accessing this website. </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> "The Company," "We," "Us," "Our" refer to Go Ride.</p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> "Party" or "Parties" refers to both you (the Client) and us (Go Ride). </p>
              <p>Any use of the terms in singular, plural, capitalized, or other variations (e.g., "he/she" or "they") is interpreted as referring to the same entity. </p>
            </li>
          </ul>
          <ul>
            <li>
              <h4>1. Cookies</h4>
              <p>We use cookies to enhance your experience on our website. By continuing to use Go Ride, you consent to the use of cookies as described in our Privacy Policy. Cookies help us retrieve user details during each visit, enabling us to provide personalized features and improve functionality. Some third-party affiliates and advertisers may also use cookies. </p>
            </li>

            <li>
              <h4>2. License and Ownership</h4>
              <p>Unless otherwise stated, The platform Go Ride is owned and operated by GORIDE RUN PRIVATE LIMITED owns all intellectual property rights for the content on this website. You may access and view the content solely for personal, non-commercial purposes, subject to the following restrictions:  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> You may not republish, sell, rent, or sublicense any material from Go Ride.  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> You may not reproduce, duplicate, or copy content from the website. </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> You may not redistribute any content from Go Ride without permission. </p>
              <p>Certain areas of the website allow users to post comments or share opinions. Please note that Go Ride does not monitor, filter, or edit user-generated content before it appears. The opinions expressed in the comments reflect those of the individual poster and do not necessarily reflect the views of Go Ride. We do not assume responsibility for the content of user comments, and by posting comments, you agree to indemnify us from any claims arising from the comments you post. </p>
            </li>

            <li>
              <h4>3. You agree not to post any content that: </h4>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Violates any intellectual property rights (e.g., copyright, trademark, patent); </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Contains defamatory, libelous, offensive, or unlawful material; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Invades privacy or infringes upon any legal rights of third parties; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Promotes unlawful activities, business solicitation, or spam. </p>
              <p>You grant Go Ride a non-exclusive, royalty-free license to use, edit, reproduce, and distribute your comments in any medium or format, including any future modifications. </p>
            </li>

            <li>
              <h4>4. Hyperlinks to Our Content </h4>
              <p>Certain organizations may link to our website without prior written approval, including: </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Government agencies; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Search engines;  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> News organizations; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Online directory distributors;  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Accredited businesses (excluding non-profits or charity groups). </p>
              <p>These organizations may link to our home page or specific content, as long as the link: </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Is not misleading or deceptive;  </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Does not imply false sponsorship or endorsement of the linking party’s products/services; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Fits within the context of the linking party’s website content. </p>
              <p>We may consider other link requests from specific organizations, including business information sources, professional associations, educational institutions, and trade groups, provided: </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> The link will not harm our reputation or brand; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> The organization has no negative history with us; </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> The link provides a clear benefit to us in terms of visibility and reputation. </p>
              <p>Links should not use our logo or any proprietary artwork unless a formal trademark license agreement is in place. </p>
            </li>

            <li>
              <h4>5. iFrames </h4>
              <p>You may not create any frames around our webpages that alter the visual appearance or presentation of our website without prior written consent from Go Ride. </p>
            </li>

            <li>
              <h4>6. Order Confirmation & Payment </h4>
              <p><strong>Order Confirmation: </strong>Once you complete your order and payment is successfully processed, you will receive a confirmation email acknowledging your purchase. This email will contain important details about your order and how to proceed with setting up your taxi dispatch software. </p>
              <p><strong>Payment Processing: </strong>All payments for software services and website creation are processed securely. Once payment is confirmed, you will receive access to the necessary tools to begin setting up your website and dispatch software. </p>
            </li>

            <li>
              <h4>7. Shipping policy (Website Creation & Domain Setup)</h4>
              <p><strong>Custom Website Creation: </strong>After your payment is successfully processed, you will be able to create a custom taxi dispatch website based on your preferences. You can choose the features, layout, and branding of your website with the options available in the software platform. </p>
              <p><strong>Domain Setup: </strong>You will be provided with a unique domain that corresponds to your taxi dispatch service. This domain is provided at no additional cost and will be registered to your business. </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Example: [yourcompany].goride.run or a custom domain of your choosing (if applicable). </p>
            </li>

            <li>
              <h4>8. No Cancellation or Refund Policy </h4>
              <p>All sales are final. Once a transaction is completed, no cancellations, exchanges, or refunds will be provided, unless otherwise required by applicable law. </p>
              <p>This policy applies to all products, services, and bookings made through our website, including but not limited to subscriptions, purchases, and event bookings. We encourage you to carefully review your order and ensure that all details are correct before completing your purchase. </p>
            </li>

            <li>
              <h4>9. Exceptions to the Policy </h4>
              <p>While we generally do not offer cancellations or refunds, there may be certain circumstances where a refund or cancellation could be considered on a case-by-case basis, such as: </p>
              <p class="ps-3">Technical Issues: If a technical error or problem with our website or payment processing system prevents you from completing your transaction. </p>
              <p class="ps-3">Exceptional Circumstances: In some rare cases, we may consider a refund if a valid issue or dispute arises regarding the service or product you purchased. Refunds in such cases are entirely at our discretion, and the refund process will take 7 to 10 days to complete. </p>
            </li>

            <li>
              <h4>10. No Refunds for Services Rendered or Products Delivered </h4>
              <p>Once services have been rendered or products have been delivered, we cannot offer a refund. This includes, but is not limited to, any digital products, memberships, subscriptions. </p>
            </li>

            <li>
              <h4>11. Content Liability </h4>
              <p>GORIDE RUN PRIVATE LIMITED is not responsible for any illegal forms, misuse of payments, or duplicate forms related to Go Ride. You agree to indemnify and defend us against all claims, damages, or expenses arising from any such issues on your website or platform. </p>
              <p>Links to websites containing defamatory, illegal, or harmful content are strictly prohibited. You must not post any links that could be construed as harmful or in violation of third-party rights. </p>
            </li>
            <li>
              <h4>12. User Consent for Communication </h4>
              <p>By using the Go Rides platform and providing your contact details (such as email or mobile number), you authorize Go Rides to contact you via SMS, email, or call for service notifications, updates, or support purposes.</p>
              <p>You may opt out of such communications at any time by using the unsubscribe link in our messages or by emailing support@goride.run.  </p>
              <p>Go Rides will not misuse, sell, or disclose your contact details to unauthorized third parties. All user information is handled in accordance with our <b>Privacy Policy </b> and applicable Indian data protection laws, including the <b> Digital Personal Data Protection Act, 2023 (DPDP Act).</b></p>
            </li>
             <li>
              <h4>13. Data Sharing Between Users and Drivers </h4>
              <p>By using the Go Rides platform, you acknowledge and agree that:</p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Go Rides acts <b>only as an information-sharing platform </b> to connect users and drivers. </p>
               <p class="ps-3"><i class="fa-regular fa-chevron-right"></i>Your contact details (such as mobile number or email) may be shared only with the respective user or driver <b>for the purpose of communication related to ride coordination, pricing, or service inquiries. </b> </p>
              <p class="ps-3"><i class="fa-regular fa-chevron-right"></i> Go Rides does <b> not participate in or monitor</b> communications between users and drivers and bears <b> no responsibility</b> for any misuse of shared contact details beyond the intended purpose. </p>
              <p>You may request removal of your data or withdrawal of consent by contacting support@goride.run</p>
              <p>All use of data shall comply with our <b>Privacy Policy</b> and the <b>Digital Personal Data Protection Act, 2023.</b></p>

            </li>
             <li>
              <h4>14. Disclaimer </h4>
              <p>Our platform serves solely as an information and connection medium between drivers and owners. We do not employ, verify, or control the users registered on the platform. Therefore, we are <b>not responsible or liable for any kind of accidents, physical harm, property damage, loss, misconduct, or harassment</b> that may occur during or after any service, communication, or meeting between users. </p>
              <p>All users are advised to exercise caution, verify details independently, and ensure personal safety while engaging in any service or interaction through our platform. </p>
            </li>
          </ul>
        </div>
      </div>

      <!-- TERM 2 -->
      <div class="tab-pane fade" id="term2" role="tabpanel" aria-labelledby="term2-tab">
        <section class="terms-section ">
  <div class="container">
    <p  data-aos="fade-up" data-aos-delay="100">
      Welcome to GoRide Platform.</p>
        <p  data-aos="fade-up" data-aos-delay="100"> Using GoRide, User agree to the following Terms and Conditions.</p>
    </p>

    <!-- 1. Platform Nature -->
    <div class="mb-4" data-aos="fade-up">
       <div class="site-heading mb-3">
              <h4>  1.Platform Nature</h4>
           
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>GoRide is a global free jobs collaboration platform that connects Cab Owners/Companies and Drivers 	  for job postings and bidding.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>We only provide a medium for job listings, bidding, and communication.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>We are not an employer, recruiter, transport operator, or contractor.</li>
      </ul>
    </div>

    <!-- 2. User Responsibilities -->
    <div class="mb-4" data-aos="fade-up">
        <div class="site-heading mb-3">   <h4>  2.User responsiblities </h4>
           
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Users must provide accurate and truthful details when registering or posting jobs.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Job Posters are responsible for accepting bids and directly contacting the selected Driver/Cab Owner.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Drivers and Cab Owners are solely responsible for verifying each other’s identity, accepting bids and         	  directly contacting the selected Driver/Cab Owners.
</li>
      </ul>
    </div>

    <!-- 3. Commission Policy -->
    <div class="mb-4" data-aos="fade-up">
      <div class="site-heading mb-3">
              <h4>3.Commission Policy</h4>

            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>As of now, GoRide is a zero-commission platform.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>However, GoRide reserves the right to introduce, revise, or modify commission structures or fees in the future with prior notification to users.
</li>
      </ul>
    </div>

    <!-- 4. Bidding & Acceptance -->
    <div class="mb-4" data-aos="fade-up">
     <div class="site-heading mb-3">
              <h4>4.Bidding & Acceptance</h4>
       
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Any user may post jobs and receive bids.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> The Job Poster reserves the right to accept or reject any bid.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Once accepted, further communication is handled independently between the parties.</li>
      </ul>
    </div>

    <!-- 5. Platform Rights -->
    <div class="mb-4" data-aos="fade-up">
      <div class="site-heading mb-3">
              <h4>5.Platform Rights</h4>
         
            </div>
      <ul>
           <p  class="mb-3"> GoRide reserves the right to:</p>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Change, suspend, or discontinue the platform or its features at any time.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Modify business models, fee structures, or policies as determined by management.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Call off or terminate the service with or without prior notice.</li>
      </ul>
    </div>

    <!-- 6. Liability Disclaimer -->
    <div class="mb-4" data-aos="fade-up">
     <div class="site-heading mb-3">
              <h4>6.Liability Disclaimer<h4>
 
            </div>
      <ul>   <p  class="mb-3">  GoRide is not responsible for:</p>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Accuracy of job postings or bids.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Payments, contracts, or disputes between users.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Vehicle condition, driver behavior, or service quality.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> All risks arising from using the platform are solely borne by the users.</li>
      </ul>
    </div>

    <!-- 7. Prohibited Activities -->
    <div class="mb-4" data-aos="fade-up">
  <div class="site-heading mb-3">
              <h4>7.Prohibited Activities</h4>
         
            </div>
      <ul>
           <p  class="mb-3">  Users must not: </p>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>   Users must not:
Post false, misleading, or illegal jobs.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Share fraudulent or unauthorized documents.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Engage in harassment, abusive language, or discrimination.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Use the platform for activities that violate applicable laws</li>
      </ul>
    </div>

    <!-- 8. Termination -->
    <div class="mb-4" data-aos="fade-up">
  <div class="site-heading mb-3">
              <h4>8.Termination</h4>
         
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> GoRide reserves the right to suspend, restrict, or remove users who violate these Terms.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>  Fraudulent or repeated misuse may result in a permanent ban.</li>
      </ul>
    </div>

    <!-- 9. Data & Privacy -->
    <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <h4>9.Data & Privacy</h4>
         
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Basic user information may be collected for account, security, and communication purposes.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> GoRide will not sell or misuse user information.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> For detailed practices, please refer to our Privacy Policy.
</li>
      </ul>
    </div>

    <!-- 10. Governing Law -->
    <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <h4>10.Governing Law</h4>
         
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> These Terms shall be governed by and interpreted in accordance with the laws of India, but may also adapt to global jurisdictions where users operate.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Any disputes shall be subject to the exclusive jurisdiction of Chennai, Tamil Nadu, India, unless otherwise required by applicable international law.
</li>
      </ul>
    </div>
     <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <h4>11.User Consent for Communication</h4>
         
            </div>
      <p>By using the Go Rides platform and providing your contact details (such as email or mobile number), you authorize Go Rides to contact you via SMS, email, or call for service notifications, updates, or support purposes.</p>
              <p>You may opt out of such communications at any time by using the unsubscribe link in our messages or by emailing support@goride.run.  </p>
              <p>Go Rides will not misuse, sell, or disclose your contact details to unauthorized third parties. All user information is handled in accordance with our <b>Privacy Policy </b> and applicable Indian data protection laws, including the <b> Digital Personal Data Protection Act, 2023 (DPDP Act).</b></p>
    </div>
     <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <h4>12.Data Sharing Between Users and Drivers</h4>
         
            </div>
     <p>By using the Go Rides platform, you acknowledge and agree that:</p>
              <p class="ps-3"><i class="fas fa-check-circle me-2 text-secondary"></i> Go Rides acts <b>only as an information-sharing platform </b> to connect users and drivers. </p>
               <p class="ps-3"><i class="fas fa-check-circle me-2 text-secondary"></i>Your contact details (such as mobile number or email) may be shared only with the respective user or driver <b>for the purpose of communication related to ride coordination, pricing, or service inquiries. </b> </p>
              <p class="ps-3"><i class="fas fa-check-circle me-2 text-secondary"></i> Go Rides does <b> not participate in or monitor</b> communications between users and drivers and bears <b> no responsibility</b> for any misuse of shared contact details beyond the intended purpose. </p>
              <p>You may request removal of your data or withdrawal of consent by contacting support@goride.run</p>
              <p>All use of data shall comply with our <b>Privacy Policy</b> and the <b>Digital Personal Data Protection Act, 2023.</b></p>
    </div> <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <h4>13. Disclaimer </h4>
         
            </div>
    <p>Our platform serves solely as an information and connection medium between drivers and owners. We do not employ, verify, or control the users registered on the platform. Therefore, we are <b>not responsible or liable for any kind of accidents, physical harm, property damage, loss, misconduct, or harassment</b> that may occur during or after any service, communication, or meeting between users. </p>
              <p>All users are advised to exercise caution, verify details independently, and ensure personal safety while engaging in any service or interaction through our platform. </p>
    </div>

    <p class="mt-4 fw-bold text-center" data-aos="zoom-in">
      By using GoRide, you confirm that you have read, understood, and agreed to these Terms & Conditions.
    </p>
  </div>
</section>
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
