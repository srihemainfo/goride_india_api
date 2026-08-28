@extends('layouts.app')

@section('content')

<style>
/* Mobile Responsive Additions */
@media (max-width: 768px) {
    
    .page-header-info{
        display: flex
;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    }
    .page-header {
        height: auto !important;
        padding: 20px 0;
    }
    
    .page-header .container .row {
        flex-direction: column;
    }
    
    .page-header .col-8,
    .page-header .col-4 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    
    .page-header .col-8 {
        margin-bottom: 30px;
    }
    
    .choose-content-area h3 {
        font-size: 24px;
        padding-bottom: 15px;
    }
    
    .choose-content-area .choose-text {
        padding-left: 30px;
        margin-top: 15px;
    }
    
    .agency_card {
        margin: 10px 0;
        padding: 20px 15px;
    }
    
    .features {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .feature-box {
        padding: 15px;
    }
    
    .how-app-work-content {
        flex-direction: column;
    }
    
    .single-how-app-work {
        margin-bottom: 20px;
        text-align: center;
    }
    
    .services-section .row {
        margin: 0 -10px;
    }
    
    .single-services-box {
        margin-bottom: 20px;
        height: auto;
        padding: 20px 15px;
    }
    
    .section-title h3 {
        font-size: 28px;
    }
    
    .why-choose h2 {
        font-size: 2rem;
        margin-bottom: 30px;
    }
    
    .enquiry-form {
        padding: 20px 15px;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        top: -25px;
    }
    
    .icon-circle i {
        font-size: 24px;
    }
    
    .agency_card h4 {
        font-size: 18px;
    }
    
    .agency_card p {
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .choose-content-area h3 {
        font-size: 22px;
    }
    
    .section-title h3 {
        font-size: 24px;
    }
    
    .why-choose h2 {
        font-size: 1.8rem;
    }
    
    .feature-box h3 {
        font-size: 1.1rem;
    }
    
    .single-services-box h3 {
        font-size: 18px;
    }
    
    .single-services-box p {
        font-size: 13px;
    }
    
    .page-header h1 {
        font-size: 28px !important;
        text-align: center;
    }
     
    .page-header p {
        text-align: center;
    }
    
    .btn-submit {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .choose-content-area h3 {
        font-size: 20px;
    }
    
    .section-title h3 {
        font-size: 22px;
    }
    
    .why-choose h2 {
        font-size: 1.6rem;
    }
    
    .agency_card {
        padding: 15px 10px;
    }
    
    .enquiry-form h3 {
        font-size: 20px;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
}


.btn-agent-super {
   position: relative;
    display: inline-block;
    padding: 9px 12px;
    font-size: 15px;
    font-weight: bold;
    color: #000;
    background: #f9bf00;
    border-radius: 15px;
    text-transform: uppercase;
    text-decoration: none;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(249, 191, 0, 0.5), 0 0 40px rgba(249, 191, 0, 0.3) inset;
    transition: all 0.3s 
ease;
    animation: bounce 2s infinite;
}

/* Shimmer effect */
.btn-agent-super::before {
    content: "";
    position: absolute;
    top: 0;
    left: -75%;
    width: 50%;
    height: 100%;
    background: rgba(255,255,255,0.4);
    transform: skewX(-25deg);
    transition: all 0.7s ease;
}
.btn-agent-super:hover::before {
    left: 125%;
}

/* Hover glow and scale */
.btn-agent-super:hover {
    transform: scale(1.15);
    box-shadow: 0 0 30px rgba(249,191,0,1), 0 0 60px rgba(249,191,0,0.7) inset;
}



/* Keyframes */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

@keyframes sparks {
    0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
    50% { opacity: 1; transform: translate(calc(-50% + 12px), calc(-50% - 24px)) scale(1); }
    100% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
}

@keyframes gradientBG {
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
.agent-button {
      position: relative;
    padding: 5px 16px;
    font-size: 15px;
    font-weight: 700;
    color: #000;
    background: #f9bf00;
    border: none;
    border-radius: 60px;
    cursor: pointer;
    overflow: hidden;
    z-index: 1;
    transition: transform 0.3s 
ease, box-shadow 0.3s 
ease;
    box-shadow: 0 0 0 rgba(0, 0, 0, 0);
    animation: enterPop 0.8s 
ease-out forwards, pulseGlow 2.5s infinite;
    }

    .agent-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, transparent, rgba(255,255,255,0.6), transparent);
      transform: skewX(-20deg);
      animation: shimmer 3s infinite;
      z-index: 2;
    }

    .agent-button::after {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 60px;
      background: radial-gradient(circle at center, rgba(255,255,255,0.2), transparent 70%);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: 0;
    }

    .agent-button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px #f9bf00;
      color:black;
    }

    .agent-button:hover::after {
      opacity: 1;
    }

    @keyframes shimmer {
      0% { left: -100%; }
      100% { left: 100%; }
    }

    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 4px #f9bf00; }
      50% { box-shadow: 0 0 12px #f9bf00; }
    }

    @keyframes enterPop {
      0% {
        transform: scale(0.5);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
.enquiry-form .form-control:focus,
.enquiry-form .form-select:focus {
  border-color: #f9bf00 !important;
  box-shadow: 0 0 5px #f9bf00 !important;
  outline: none;
}
.circle-number {
  display: inline-flex
;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: #f9bf00;
    color: white;
    font-weight: 700;
    border-radius: 50%;
    margin-right: 10px;
    font-size: 18px;
     /*box-shadow: 0 0 10px rgba(249, 191, 0, 0.5);*/
}
.inner p{
    font-weight: 600;
    color: white;
    padding: 5px;
}

.choose-content-area h3::before {
    position: absolute;
    content: "";
    height: 3px;
    width: 80px;
    background-color: #fac012;
    bottom: 0;
    left: 0;
}
.choose-content-area h3::after {
    position: absolute;
    content: "";
    height: 3px;
    width: 32px;
    background-color: #fac012;
    bottom: 0;
    margin: 0 auto 0;
    left: 95px;
}
.choose-section {
    line-height: 1;
}
.choose-content-area h3 {
    font-size: 30px;
    color: #202647;
    font-weight: bold;
    margin: 10px 0 26px 0;
    position: relative;
    padding-bottom: 20px;
    max-width: 600px;
}
.choose-content-area p {
    font-size: 14px;
    color: #6a6c72;
    font-weight: 400;
    margin: 0 0 0 0;
}
.choose-content-area .choose-text {
    margin-top: 20px;
    position: relative;
    padding-left: 35px;
}
.choose-content-area .choose-text i {
    position: absolute;
    display: inline-block;
    height: 22px;
    width: 22px;
    line-height: 22px;
    background-color: #ffffff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
    text-align: center;
    color: #f0b800;
    border-radius: 50px;
    top: 4px;
    left: 0;
    transition: 0.5s;
}
.choose-content-area .choose-text h4 {
    font-size: 18px;
    color: #202647;
    font-weight: 500;
    margin: 0 0 8px 0;
}
.choose-content-area .choose-text p {
    margin: 0 0 0 0;
    font-size: 14px;
    color: #6a6c72;
    font-weight: 400;
}
.section-title {
    text-align: center;
    margin-bottom: 50px;
}
.section-title h3 {
    font-size: 38px;
    color: #202647;
    font-weight: bold;
    margin: 10px 0 34px 0;
    position: relative;
    padding-bottom: 20px;
}
.section-title h3::before {
    position: absolute;
    content: "";
    height: 3px;
    width: 80px;
    background-color: #f9bf00;
    bottom: 0;
    left: 0;
    right: 35px;
    margin: auto;
}
.single-services-box:hover {
    transform: translateY(-10px);
    box-shadow: 0 2px 48px 0 rgba(0, 0, 0, 0.08);
    background-color: #b9b9b9;
}
.single-services-box p {
    font-size: 14px;
    color: #6a6c72;
    font-weight: 400;
    margin: 0 0 0 0;
    transition: 0.5s;
}
.single-services-box h3 {
    font-size: 20px;
    margin-top: 18px;
    margin-bottom: 10px;
    color: #202647;
    font-weight: 600;
    transition: 0.5s;
    text-transform: capitalize;
}
.single-services-box {
  position: relative;
    background: #ffffff;
    border-radius: 2px;
    transition: 0.5s;
    text-align: center;
    padding: 30px 3px;
    margin-bottom: 30px;
    height: 284px;
}
.single-services-box .icon {
    text-align: center;
    width: 65px;
    height: 65px;
    line-height: 65px;
    border-radius: 50%;
    transition: 0.5s;
    display: inline-block;
}
.services-section {
    background-color: #f6f5fb;
    position: relative;
    z-index: 1;
    overflow: hidden;
}
.how-app-work-section .how-app-work-content:before{
        left: 32px;
}
.how-app-work-section .single-how-app-work .icon-box .inner{
    width: 44px;
    height: 44px;
}

.why-choose {
  background: url('{{ asset('goride/img/car.webp') }}') center center/cover no-repeat;
  color: #fff;
  text-align: center;
  position: relative;
}
.overlay {
  background-color: rgba(0, 0, 0, 0.6);
  padding: 60px 20px;
  border-radius: 10px;
}

.why-choose h2 {
  font-size: 2.5rem;
  margin-bottom: 40px;
  font-weight: bold;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
  margin: 0 auto;
}

.feature-box {
    background-color: rgb(255 255 255 / 90%);
    padding: 20px;
    border-radius: 8px;
    transition: transform 0.3s ease;
    height:225px;
}

.feature-box:hover {
    transform: translateY(-5px);
    background-color: rgb(255 251 255);
}

.feature-box h3 {
 font-size: 18px;
    margin-bottom: 10px;
}
.feature-box p {
   font-weight: bold;
    color: #7a3d0c;
    font-size: 15px;
}

.agency_card {
    position: relative;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 16px;
    text-align: center;
    transition: all 0.5s ease;
    color: #fff;
    cursor: pointer;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.agency_card:hover {
    transform: scale(1.04);
    border-color: rgba(249, 191, 0, 0.8);
    box-shadow: 0 0 25px rgba(249, 191, 0, 0.25);
    background: rgba(0, 0, 0, 0.7);
}

.agency_card h4 {
    font-size: 20px;
    color: #f9bf00;
    font-weight: 700;
    position: relative;
    z-index: 1;
    letter-spacing: 0.5px;
}

.agency_card p {
    font-size: 15px;
    color: #eaeaea;
    line-height: 1.6;
    margin-top: 10px;
    position: relative;
    z-index: 1;
}

.icon-circle {
    width: 70px;
    height: 70px;
    background: #f9bf00;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
    z-index: 1;
    animation: rotate-left-right 3s ease-in-out infinite;
    top:-31px;
}

.icon-circle i {
    font-size: 28px;
    color: #000;
}

@keyframes rotate-left-right {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(-15deg); }
    50% { transform: rotate(0deg); }
    75% { transform: rotate(15deg); }
    100% { transform: rotate(0deg); }
}

.agency_card:hover .icon-circle {
    background: #fff;
    color: #f9bf00;
    box-shadow: 0 0 20px rgba(249, 191, 0, 0.7);
}

.enquiry-form {
    background: rgba(255, 255, 255, 0.95);
    padding: 12px 12px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.enquiry-form h3 {
    color: black;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
}

.enquiry-form .form-control,
.enquiry-form .form-select {
    border-radius: 6px;
    padding: 6px;
    border: 1px solid #ddd;
}

.btn-submit {
    width: 50%;
    background: #f9bf00;
    border: none;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-submit:hover,
.btn-submit:focus {
  background: #e0aa00 !important; /* slightly darker yellow on hover */
  color: #fff !important;
  /*box-shadow: 0 0 10px #f9bf00;*/
  transform: translateY(-2px); /* subtle lift effect */
  outline: none;
}

.trial-banner {
    background: #f3ba00ba;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}

.free-plan-img {
    max-width: 300px;
    height: 300px;
}

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
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding-left: 29px;
    border-radius: 23px;
    gap: 9px;
    color: black;
    position: relative;
    cursor: pointer;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.212);
    font-weight: 600;
}

.arrow {
    position: absolute;
    right: 7.5px;
    background-color: #f3ba00;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 30px;
    transition: all .5s ease;
}

.signupBtn:hover .arrow {
    width: calc(120px - (7.5px)*2);
}

.list-unstyled li {
    font-weight: 700;
    font-size: 23px;
}

.tick-line {
    font-size: 1.25rem;
    font-weight: 500;
}

.blink {
    animation: blinkColor 1s infinite;
}

@keyframes blinkColor {
    0% {
        color: red;
    }
    50% {
        color: green;
    }
    100% {
        color: red;
    }
}

.app-ride {
    background-color: #000;
    padding: 10px;
    display: inline-block;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .app-ride img {
        width: 88px !important;
    }
}
</style>


<!-- Breadcrumb -->

<section class="page-header"
    style="background-image: url('{{ request()->is('agency') ? asset('goride/img/handshake.webp') : asset('goride/img/breadcrump_banner.webp') }}'); height: 550px; background-size: cover; background-position: center center;">

    <div class="container">
        <div class="row mt-5 pt-5 d-flex justify-content-center align-items-center">
            <div class="col-8">
                <div class="page-header-info">
                
                    <h1>Become a <span>GoRide Agency</span></h1>
                    <p>Build India's most trusted mobility platform in your zone with exclusive territorial rights. Create community impact while building a sustainable business with our proven agency model.
                    </p>
                    <button class="agent-button mt-3">BECOME AN AGENT</button>
                </div>
            </div>
            <div class="col-4">

                <div class="enquiry-form" data-aos="fade-left">
                    <h3>Enquiry Form</h3>
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control" placeholder="Phone Number" required>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" required>
                                <option value="">Select District</option>
                                <option>Chennai</option>
                                <option>Coimbatore</option>
                                <option>Madurai</option>
                                <option>Salem</option>
                                <option>Trichy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Location" required>
                        </div>
                        <div class="d-flex justify-content-center align-items-center">
                        <button type="submit" class="btn-submit">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="about-section pt-5 pb-5">
    <div class="container">
     
        <div class="row align-items-center">
            <div class="container">
                  <div class="row d-flex justify-content-center align-items-center">
      <div class="col-md-12">
        <div class="choose-content-area  mb-40">
          <h3 class="wow fadeIn" data-wow-duration="2s">
            Why Become a GoRide Agency?
          </h3>
        </div>
      </div>

      <div class="col-md-4 d-flex my-4 wow fadeIn" data-wow-duration="2s">
        <div class="agency_card">
          <div class="icon-circle"><i class="fas fa-handshake"></i></div>
          <h4>Exclusive Zone Rights</h4>
          <p>
            Secure territorial exclusivity for GoRide operations in your area, ensuring
            you're the sole authorized partner managing all local activities.
          </p>
        </div>
      </div>

      <div class="col-md-4 d-flex my-4 wow fadeIn" data-wow-duration="2s" data-wow-delay="300ms">
        <div class="agency_card">
          <div class="icon-circle"><i class="fas fa-coins"></i></div>
          <h4>Multiple Income Streams</h4>
          <p>
            Benefit from diverse revenue sources including driver subscriptions, company
            onboarding fees, escrow transaction shares, and performance bonuses.
          </p>
        </div>
      </div>

      <div class="col-md-4 d-flex my-4 wow fadeIn" data-wow-duration="2s" data-wow-delay="600ms">
        <div class="agency_card">
          <div class="icon-circle"><i class="fas fa-chart-line"></i></div>
          <h4>Substantial Earning Potential</h4>
          <p>
            Generate recurring monthly income ranging from ₹50,000 to ₹2,00,000+ based on your
            zone's activity and growth performance.
          </p>
        </div>
      </div>

      <div class="col-md-4 d-flex my-4 wow fadeIn" data-wow-duration="2s" data-wow-delay="900ms">
        <div class="agency_card">
          <div class="icon-circle"><i class="fas fa-headset"></i></div>
          <h4>Comprehensive Support</h4>
          <p>
            Receive full brand, marketing, training, and backend support from GoRide
            headquarters, ensuring your success every step of the way.
          </p>
        </div>
      </div>

      <div class="col-md-4 d-flex my-4 wow fadeIn" data-wow-duration="2s" data-wow-delay="1200ms">
        <div class="agency_card">
          <div class="icon-circle"><i class="fas fa-rocket"></i></div>
          <h4>Growth Pathway</h4>
          <p>
            High-performing agencies can upgrade to become GoRide Sharing Partners,
            unlocking even greater business opportunities and revenue potential.
          </p>
        </div>
      </div>
    </div>
            </div>
        </div>
    </div>
</section>


<section class="choose-section pb-5 pb-md-0">
    <div class="container">
        <div class="row align-items-start">
               <div class="choose-content-area">
             <h3>GoRide Support's Benefits</h3></div>
           <div class="col-lg-4 col-md-12">
  <div class="choose-content-area">
    <!-- <span>GoRide Support s Benefits</span> -->
    <!-- <p>Manage your entire district operation through our intuitive customer relationship management system with real-time analytics and reporting capabilities</p> -->

    <div class="choose-text" data-aos="fade-up" data-aos-delay="100">
      <i class="fa-regular fa-check"></i>
      <h4>Advanced CRM's Dashboard Access</h4>
      <p>
        Manage your entire zone operation through our intuitive customer relationship management system with real-time analytics and reporting capabilities.
      </p>
    </div>

    <div class="choose-text" data-aos="fade-up" data-aos-delay="200">
      <i class="fa-regular fa-check"></i>
      <h4>Branding's Co-Marketing Support</h4>
      <p>
        Leverage GoRide's brand reputation with professional marketing materials, co-branded campaigns, and digital marketing support to attract drivers and companies.
      </p>
    </div>

    <div class="choose-text" data-aos="fade-up" data-aos-delay="300">
      <i class="fa-regular fa-check"></i>
      <h4>Comprehensive Training Programs</h4>
      <p>
        Receive both online and offline training for you and your staff, covering operations, compliance, customer service, and platform management.
      </p>
    </div>
  </div>
</div>

            <div class="col-lg-4 col-md-12">
                <div class="choose-image">
                    <img src="{{ asset('goride/img/benefits.webp') }}">
                </div>
            </div>
          <div class="col-lg-4 col-md-12">
  <div class="choose-content-area">

    <div class="choose-text" data-aos="fade-up" data-aos-delay="100">
      <i class="fa-regular fa-check"></i>
      <h4>Lead Generation Support</h4>
      <p>
        Benefit from centrally-run marketing campaigns that generate qualified leads directly to your zone, helping you grow faster.
      </p>
    </div>

    <div class="choose-text" data-aos="fade-up" data-aos-delay="200">
      <i class="fa-regular fa-check"></i>
      <h4>Transparent Commission Dashboard</h4>
      <p>
        Track your earnings in real-time with detailed monthly reports showing all revenue streams, bonuses, and performance metrics.
      </p>
    </div>

    <div class="choose-text" data-aos="fade-up" data-aos-delay="300">
      <i class="fa-regular fa-check"></i>
      <h4>Recognition's Rewards Program</h4>
      <p>
        Top-performing zones receive special recognition, additional bonuses, exclusive incentives, and opportunities for expansion.
      </p>
    </div>

    <div class="choose-btn" data-aos="fade-up" data-aos-delay="400">
      <!-- <a href="#" class="default-btn-one">Discover More</a> -->
    </div>

  </div>
</div>

            
        </div>
    </div>
</section>

<section class="why-choose">
     <div class="overlay text-center">
  <div class="container">
      
    <div class="row">
     
        <h2 class="text-white">Agency Responsibilities</h2>
        <p class="text-white">
          As a GoRide zone Agency, you'll play a crucial role in building and maintaining a quality mobility network in your territory.
        </p>

        <div class="features">
          <div class="row d-flex justify-content-center align-items-center">
  <div class="col-lg-4 col-md-6 col-12 mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="feature-box">
      <h3><span class="circle-number">01</span> Driver's Operator Onboarding</h3>
      <p>
        Recruit, verify, and onboard local drivers and travel operators. Ensure all documentation meets GoRide's verification standards for safety and compliance.
      </p>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-12 mb-4" data-aos="fade-up" data-aos-delay="200">
    <div class="feature-box">
      <h3><span class="circle-number">02</span> Fee Collection's KYC Management</h3>
      <p>
        Assist in subscription fee collection and complete Know Your Customer (KYC) verification processes for all network participants.
      </p>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-12 mb-4" data-aos="fade-up" data-aos-delay="300">
    <div class="feature-box">
      <h3><span class="circle-number">03</span> Relationship Management</h3>
      <p>
        Build and maintain strong relationships with local drivers, travel companies, and customers to ensure high service quality and satisfaction.
      </p>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-12 mb-4" data-aos="fade-up" data-aos-delay="400">
    <div class="feature-box">
      <h3><span class="circle-number">04</span> Compliance's Quality Control</h3>
      <p>
        Ensure all operations comply with GoRide's policies, local transport regulations, and safety standards. Monitor service quality consistently.
      </p>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-12 mb-4" data-aos="fade-up" data-aos-delay="500">
    <div class="feature-box">
      <h3><span class="circle-number">05</span> Marketing's Performance Reviews</h3>
      <p>
        Actively participate in local marketing campaigns and attend monthly performance reviews to optimize operations and maximize growth.
      </p>
    </div>
  </div>
</div>

        </div> <!-- features end -->
      </div>
    </div>
  </div>
</section>


<section class="how-app-work-section" id="how-it-works">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="how-app-work-content-wrap">
                    <div class="title wow fadeIn" data-wow-duration="2s">
                        <h3>How to Apply</h3>
                    </div>
                    <div class="how-app-work-content" id="how-app-work-slider-pager">
                        <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work ">
                                <div class="icon-box">
                                    <div class="inner">
                                        <p >1</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    ">Express Interest
</h4>
                                    <p class="m-0
    ">Visit goride.run/agency or contact GoRide headquarters directly to begin your application process.
</p>
                                </div>
                            </div>
                        </div>
                        <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work">
                                <div class="icon-box">
                                    <div class="inner">
                                            <p >2</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    ">Submit Application</h4>
                                    <p lass="m-0
    ">Provide your business background, experience, and zone preference details through our partner application form.</p>
                                </div>
                            </div>
                        </div>
                        <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work ">
                                <div class="icon-box">
                                    <div class="inner">
                                           <p >3</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    ">Interview Process</h4>
                                    <p class="m-0
    ">Participate in our shortlisting and interview process where we assess fit, commitment, and zone potential.</p>
                                </div>
                            </div>
                        </div>
                      <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work ">
                                <div class="icon-box">
                                    <div class="inner">
                                           <p >4</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    ">Agreement's Payment</h4>
                                    <p class="m-0
    ">Pay the security deposit and activation fee, then sign the official GoRide zone Agency Agreement.</p>
                                </div>
                            </div>
                        </div>
                        <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work ">
                                <div class="icon-box">
                                    <div class="inner">
                                           <p >5</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    ">Complete Training</h4>
                                    <p class="m-0
    ">Undergo comprehensive training and receive full access to the CRM dashboard and operational tools.
</p>
                                </div>
                            </div>
                        </div>
                        <div href="#" class="pager-item active wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">
                            <div class="single-how-app-work ">
                                <div class="icon-box">
                                    <div class="inner">
                                           <p >6</p>
                                    </div>
                                </div>
                                <div class="text-box">
                                    <h4 class="m-0
    "> Launch Operations</h4>
                                    <p class="m-0
    ">Officially launch GoRide operations in your zone and start building your mobility network and revenue streams!</p>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                    <!-- Links -->
                    <!--<a href="#" class="download-btn wow fadeIn" data-wow-duration="2s">-->
                    <!--	<i class="fab fa-apple"></i>-->
                    <!--	<span class="inner"> <span class="avail">Available on</span> <span class="store-name">App Store</span></span>-->
                    <!--</a>-->
                    <!--<a href="https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&pcampaignid=web_share"-->
                    <!--    target="_blank" class="download-btn wow fadeIn" data-wow-duration="2s" data-wow-delay="500ms">-->
                    <!--    <i class="fab fa-google-play"></i>-->
                    <!--    <span class="inner"><span class="avail">Available on</span> <span class="store-name">Google-->
                    <!--            play</span></span>-->
                    <!--</a>-->
         <a href="#" class="btn-agent-super">
     BECOME AN AGENT<i class="fas fa-hand-point-up ms-2"></i>
</a>
                </div>
            </div>
            <!--<div class="col-md-6 how-app-work-slider-content d-flex align-items-center wow fadeIn"-->
            <!--    data-wow-duration="2s">-->
            <!--    <img src="{{ asset('goride/img/driver-app-mockup.webp') }}">-->
            <!--</div>-->
        </div>
    </div>
</section>


<section class="services-section pt-3 pb-3">
    <div class="container">
        <div class="section-title">
           
            <h3>Infrastructure Requirements
</h3>
<p>To operate as an authorized GoRide zone Agency, ensure you have the following essential infrastructure in plac</p>
        </div>
      <div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
        <div class="single-services-box">
            <div class="icon">
               <img src="https://www.goride.net.in/goride/img/req1.png">
            </div>
            <h3>Office Space</h3>
            <p>100 to 300 sq.ft easily accessible city center (home office or commercial space acceptable). Should be presentable for driver meetings and company onboarding sessions.</p>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
        <div class="single-services-box">
            <div class="icon">
                <img src="https://www.goride.net.in/goride/img/req6.png">
            </div>
            <h3>Team Members</h3>
            <p>Minimum 2 to 3 members including Admin/Manager, Field Executive for driver recruitment, and Customer Support representative to handle inquiries.</p>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="300">
        <div class="single-services-box">
            <div class="icon">
                <img src="https://www.goride.net.in/goride/img/req2.png">
            </div>
            <h3>Technology Equipment</h3>
            <p>Laptop/Desktop with reliable high-speed Internet (4G/5G), Printer/Scanner for documentation, and backup power supply recommended.</p>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="400">
        <div class="single-services-box">
            <div class="icon">
                <img src="https://www.goride.net.in/goride/img/req3.png">
            </div>
            <h3>Communication Tools</h3>
            <p>Smartphone with WhatsApp Business for network communication, driver coordination, and customer support channels.</p>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="500">
        <div class="single-services-box">
            <div class="icon">
                <img src="https://www.goride.net.in/goride/img/req4.png">
            </div>
            <h3>Local Knowledge</h3>
            <p>Deep understanding of local driver networks, travel company landscape, transport routes, and community connections in your zone.</p>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="600">
        <div class="single-services-box">
            <div class="icon">
                <img src="https://www.goride.net.in/goride/img/req5.png">
            </div>
            <h3>Branding Materials</h3>
            <p>GoRide Signboard for office visibility, promotional stickers, posters, and marketing collateral (provided by GoRide headquarters).</p>
        </div>
    </div>
</div>

    </div>
    
</section>

<!--<section class="trial-banner py-5" >-->
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