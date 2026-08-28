<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GO RIDE - The Heartbeat of Mobility</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
  <link rel="shortcut icon" href="https://www.goride.run/goride/img/Go-Ride-fav-icon.webp" />
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&amp;display=swap">




   <style>
 
body{
        font-family: 'Outfit', sans-serif;
        color:#555;
}


.navbar-brand img {
  transition: all 0.4s ease-in-out;
}

/*.navbar.scrolled .navbar-brand img {*/
/*  transform: scale(0.9); */
/*  opacity: 0.9; */
/*  filter: drop-shadow(0 2px 4px rgba(0,*/
/*    0,*/
/*    0,*/
/*    0.3));*/
/*}*/

.navbar .nav-link {
 
    font-weight: 500;
}

.navbar .nav-link:hover {
    color: #ff9900 !important; 
}

.navbar .nav-link.active {
    color: #!important ; 
    font-weight: 600;
    position: relative;
}

.navbar .nav-link.active::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -5px;
    width: 100%;
    height: 2px;
    background: #ff9900;
    border-radius: 2px;
}

.footer-social li a i {
    height: 45px;
    width: 45px;
    line-height: 45px;
    text-align: center;
    border-radius: 50px;
    background: #111;
    color: white; 
    transition: all 0.3s ease;
}

.scroll-accordion {
  max-height: 400px;
  overflow-y: auto;
  padding-right: 5px; 
}

.scroll-accordion: :-webkit-scrollbar {
  width: 6px;
}

.scroll-accordion: :-webkit-scrollbar-track {
  background: #f1f1f1;
}

.scroll-accordion: :-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 5px;
}

.scroll-accordion: :-webkit-scrollbar-thumb:hover {
  background: #555;
}

.vda-2 .video-content {
    margin-bottom: -250px;
    position: relative;
    z-index: 2;
}
   .terms-section{
           padding-top: 110px;
   }
   
   
   .site-heading ul li,
.privacy-list li {
    list-style-type: none;
    margin-bottom: 10px;
}

 
   @media (max-width: 768px) {
       .terms-section

 {
    padding-top: 82px;
}
       .navbar .nav-link {
           color:black ;
       }
       .navbar-collapse{
          background: white !important;
          padding: 20px !important;
          margin-top: 26px;
    }
       .jobs-hero{
           background-image: url(/goride/img/slider/go_ride_background.png) !important;
       }
       .goride-about{
               height: 428px;
       }
       .view{
           display:flex;
           justify-content:center;
       }
       .vda-2 .video-content{
               margin-bottom: -179px !important;
    }
       
       .video-wrapper {
               height: 259px !important;
    }
       .theme-btn2{
           padding: 6px 17px !important;
    }
       .site-heading h2 {
           font-size: 23px !important;
    }
       .hero-section {
           padding: 0px !important;
    }
       .jobs-hero p {
           text-align:justify !important;
    }
    /*   .jobs-hero{*/
    /*           background: url(/goride/img/slider/go_ride_background.png) center center / cover no-repeat !important;*/
    /*}*/
       .hero-btn{
           display:flex !important;
    }
       
.jobs-hero .theme-btn {
    padding: 6px 8px !important;
    }
    .video-content::before {
       display:none;
    }
     .copyright::before {
       display:none;
    }
    .footer-social{
        justify-content:center !important;
    }
    .footer-social li a i{
      background:white !important;
          color: #ff9900 !important;
    }
}

 ul li
 {
    list-style-type: none;
    margin-bottom: 10px;
}

   /* Basic hamburger bars */
.navbar-toggler .toggler-icon {
  display: block;
  width: 25px;
  height: 3px;
  margin: 5px 0;
  transition: all 0.3s ease;
  background-color: #fff; /* default → white */
  border-radius: 2px;
}

/* After scroll → black */
.navbar.scrolled .navbar-toggler .toggler-icon {
  background-color: #000;
}

   .theme-btn2:hover{
       background: #e0a800;
   }
 #mainNavbar {
background: #222222;
  transition: all 0.3s ease;
      padding-top: 23px;
}


#mainNavbar.scrolled {
  background: #fff !important;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);

}


.goride-logoscroll {
     background: transparent;
  position: relative;
  top: 0;               
  transition: all 0.4s ease;
}

.goride-logoscroll.scrolled {
  top: 25px;           

  border-radius: 35px;
  padding: 10px;
}
   }

.video-content {
    background-image: url(goride/img/zero.png);
    position: relative;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border-radius: 20px;
    /* width: 554px; */
}

.video-content::before {
    content: "";
    position: absolute;
    background: rgba(3,
    2,
    7, .2);
    border-radius: 20px;
    width: 49%;
    height: 100%;
    left: 284px;
    top: 0;
}

.video-wrapper {
    position: relative;
    display: flex
;
    justify-content: center;
    height: 435px;
    z-index: 100;
    /* width: 333px; */
}

.video-area .play-btn,
.play-btn {
    display: inline-block;
    height: 75px;
    width: 75px;
    line-height: 75px;
    font-size: 20px;
    text-align: center;
    background: #ff9900; /* straight orange */
    color: #ffffff !important;
    position: absolute;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%,
    -50%);
    z-index: 1;
}

.play-btn i::after {
    content: "";
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    z-index: -1;
    background-color: #ff9900; /* straight orange */
    border-radius: 50%;
    animation: ripple-wave 1s linear infinite;
    transform: scale(1);
    transition: all 0.5s ease-in-out;
}
@keyframes ripple-wave {
    0% {
        transform: scale(1);
        opacity: 0.6;
    }
    100% {
        transform: scale(1.8);
        opacity: 0;
    }
}
.footer-area {
    background: #111; /* dark background */
    position: relative;
    z-index: 1;
    padding-top: 35px;
    color: #fff;
}

.footer-widget {
    position: relative;
    z-index: 1;
}

.footer-widget-box {
    margin-bottom: 20px;
}

.footer-logo img {
    width: 210px;
    margin-bottom: 20px;
}

.footer-widget-box p {
    color: #fff;
    margin-bottom: 20px;
}

.footer-contact li {
    position: relative;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    color: #ccc;
    font-size: 16px;
    margin-bottom: 10px;
}

.footer-contact li a {
    color: #ccc;
    text-decoration: none;
    transition: 0.3s;
}

.footer-contact li a:hover {
    color: #ff9900; /* orange on hover */
}

.footer-list li a i {
    margin-right: 5px;
    color: #ff9900;
}
.footer-contact li i {
    width: 35px;
    height: 35px;
    line-height: 35px;
    font-size: 16px;
    margin-right: 15px;
    border-radius: 50%;
    background: #ff9900; /* orange background for icons */
    text-align: center;
    color: #fff;
    transition: 0.3s;
}

.footer-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-list li {
    margin-bottom: 10px;
}

.footer-list li a {
    color: #ccc;
    text-decoration: none;
    transition: 0.3s;
}

.footer-list li a:hover {
    color: #ff9900; /* hover color */
}

.footer-newsletter p {
    margin-bottom: 10px;
}

.subscribe-form {
    display: flex;
    gap: 10px;
}

.subscribe-form input {
    flex: 1;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
}

.subscribe-form button {
    padding: 8px 15px;
    border: none;
    background: #ff9900; /* orange button */
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    transition: 0.3s;
}

.subscribe-form button:hover {
    opacity: 0.8;
}


.footer-social {
    display: flex;
    list-style: none;
    justify-content: flex-end;
    gap: 15px;
    padding: 0;
    margin: 0;
}

.footer-social li a {
    color: #aaa;
    font-size: 16px;
    transition: 0.3s;
}

.footer-social li a:hover {
    color: #ff9900; /* hover orange */
}

.copyright {
position: relative;
    padding: 20px 0;
    background: var(--footer-bg2);
border-bottom: 5px solid #f9c724;

    z-index: 1;
}

.copyright::before {
    content: "";
    position: absolute;
    right: 0;
    top: -10px;
    bottom: -1px;
    background: #f9c724;
    width: 50%;
    clip-path: polygon(8% 0%,
    100% 0,
    100% 100%,
    0% 100%);
    z-index: -1;
}

.copyright .copyright-text {
    color: #aaa; /* straight gray text color */
    margin-bottom: 0px;
    font-size: 16px;
}
/* Ensure carousel images are consistent */
.owl-carousel .item img {
    max-width: 150px; /* Adjust width as needed */
    max-height: 80px; /* Adjust height as needed */
    width: auto; /* Maintain aspect ratio */
    height: auto; /* Maintain aspect ratio */
    display: block;
    margin: 0 auto; /* Center the image */
    object-fit: contain; /* Prevent stretching, scale down to fit */
}
  .faq-area .accordion-button:not(.collapsed) {
  color: #ffb400; /* theme color */
  background: transparent;
  box-shadow: inset 0 -1px 0 rgba(0,
    0,
    0,
    0.13);
}

.accordion-button:not(.collapsed) {
  border-bottom: 1px solid #ffb400; /* theme color */
}

  .faq-area .accordion-item {
  border: none;
  margin-bottom: 30px;
  background: #ffffff;
  border-radius: 12px !important;
  box-shadow: 0 4px 15px rgba(0,
    0,
    0,
    0.08);
}

.accordion-button {
  border-radius: 0px !important;
  background: transparent;
  font-weight: 700;
  font-size: 20px;
  color: #222222;
  box-shadow: none !important;
}

.faq-area .accordion-item span {
  width: 45px;
  height: 45px;
  margin-right: 15px;
}

.faq-area .accordion-item i {
  width: 45px;
  height: 45px;
  line-height: 45px;
  border-radius: 50px;
  background: #ffb400; /* theme color */
  text-align: center;
  color: #ffffff;
  font-size: 18px;
}

.faq-img img {
    border-radius: 15px;
    width: 100%;
    height: 300px;
    object-fit: contain;
}

.py-120 {
  padding: 120px 0;
}

.site-heading .site-title-tagline {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: #ffb400;
}

.site-heading h2 {
  font-size: 32px;
  font-weight: 700;
  color: #222;
}

.site-heading h2 span {
  color: #ffb400;
}

   .download-area {
  position: relative;
}

.download-wrapper {
  position: relative;
  background-image: url('goride/img/shape-7.png');
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  border-radius: 20px;
  padding: 40px;
  overflow: hidden;
  z-index: 1;
}

.site-heading {
  margin-bottom: 40px;
  position: relative;
  z-index: 1;
}

.site-title-tagline {
  position: relative;
  text-transform: uppercase;
  letter-spacing: 4px;
  font-size: 18px;
  font-weight: 700;
  color: #f9b208; /* yellow */
}
/*.site-title-tagline::before {*/
/*  content: "";*/
/*  position: absolute;*/
/*  height: 10px;*/
/*  width: 100%;*/
/*  background: #f9b208;*/
/*  opacity: 0.2;*/
/*  left: -2px;*/
/*  bottom: 0;*/
/*}*/

.site-title {
  font-weight: 700;
  text-transform: capitalize;
  font-size: 45px;
  color: #222; /* dark */
  margin-bottom: 0;
}

.site-title span {
  color: #f9b208; /* yellow highlight */
}

.site-heading p {
  margin-top: 15px;
  color: #555;
  font-size: 16px;
  line-height: 1.6;
}

.download-btn {
  display: flex;
  gap: 15px;
  justify-content:center;
}

.download-btn a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 20px;
  background: #f9b208;
  color: #fff;
  border-radius: 50px;
  box-shadow: 0 4px 20px rgba(0,
    0,
    0,
    0.1);
  text-decoration: none;
  transition: all 0.3s ease;
}

.download-btn a:first-child {
  background: linear-gradient(135deg, #f9b208, #ff8000); /* gradient */
}

.download-btn a:last-child {
    background: #000000;
}

.download-btn a:hover {
  transform: translateY(-3px);
}

.download-btn a i {
  font-size: 32px;
}

.download-btn-content {
  display: flex;
  flex-direction: column;
}

.download-btn-content span {
  font-weight: 500;
  font-size: 14px;
  line-height: 1.2;
}

.download-btn-content strong {
  font-size: 16px;
}

.download-img {
  position: absolute;
  right: 0;
  bottom: -30px;
  width: 55%;
  z-index: 2;
  animation: floatY 3s ease-in-out infinite;
}
/* Floating animation for the image */
@keyframes floatY {
    0%,
    100% {
    transform: translateY(0);
    }
  50% {
    transform: translateY(-15px);
    }
}

    #how-it-works .nav-pills .nav-link {
     border-radius: 30px;
    padding: 6px 20px;
    transition: all 0.3s ease-in-out;
    font-weight: 600;
        color: #ca9c07;
}
  #how-it-works .nav-pills .nav-link.active {
      background: linear-gradient(135deg, #f9dc7a, #ca9e20);
    color: black;
   
    /* height: 32px; */
    display: flex
;
    align-items: center;
}
  #how-it-works .screenshot-tab {
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}
  #how-it-works .screenshot-tab:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0,
    0,
    0,
    0.2);
}
  #how-it-works h3 {
    font-weight: 700;
    margin-bottom: 15px;
}
/* Section background */
section.bg-dark {
  position: relative;
  overflow: hidden;
}
section.bg-dark::before {
  content: "";
  position: absolute;
  top: -50px;
  left: -100px;
  width: 300px;
  height: 300px;
  background: #f9b208;
  opacity: 0.05;
  border-radius: 50%;
  transform: rotate(25deg);
  opacity: 1;
}

section.bg-dark::after {
  content: "";
  position: absolute;
  bottom: -50px;
  right: -100px;
  width: 400px;
  height: 400px;
  background: #ffffff;
  opacity: 0.03;
  border-radius: 50%;
  transform: rotate(-15deg);
    opacity: 0.1;
}
/* Heading underline effect */
.section-title {
  position: relative;
  display: inline-block;
  padding-left: 50px;
  font-weight: 700;
}

.section-title::before {
  content: "";
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 35px;
  height: 4px;
  background: #f9b208; /* yellow highlight */
  border-radius: 2px;
}
/* Circle icons */
.rounded-circle {
  font-size: 20px;
  box-shadow: 0 4px 12px rgba(0,
    0,
    0,
    0.1);
}
/* Numbering highlight */
.fw-bold span {
  font-size: 32px;
  font-weight: 700;
  margin-left: 8px;
}

   .jobs-hero {
  position: relative;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}
.jobs-hero::before {
    content: "";
    position: absolute;
    width: 100%;
    height: 100%;
    left: -0.5px;
    top: 0;
    background: var(--hero-overlay-color);
    opacity: 0.7;
    z-index: -1;
}
.jobs-hero .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
.jobs-hero-content {
  position: relative;
  z-index: 2;
}
.jobs-hero .hero-sub-title {
  font-size: 1.2rem;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #FFC107; /* golden highlight */
}
.jobs-hero .hero-title {
  font-size: 3rem;
  font-weight: 700;
      max-width: 443px;
}
.jobs-hero .hero-title span {
  color: #FFC107;
}
.jobs-hero p {
  margin-top: 15px;
  font-size: 16px;
  line-height: 1.6;
    max-width: 381px;
}
.jobs-hero .theme-btn {
  background: #FFC107;
    color: #000;
  padding: 12px 28px;
  border-radius: 7px;
  font-weight: 600;
  margin: 10px;
  display: inline-block;
  text-decoration: none;
  transition: 0.3s;
     
}
.jobs-hero .theme-btn:hover {
  background: #e0a800;
}
.jobs-hero .theme-btn2 {
  background: #28A745;
  color: #fff;
}
/*.jobs-hero .theme-btn2:hover {*/
/*  background: #218838;*/
/*}*/
 .theme-btn2 {
background: #FFC107;
    color: #000;
    padding: 12px 28px;
    border-radius: 7px;
    font-weight: 600;
    margin: 10px;
    display: inline-block;
    text-decoration: none;
    transition: 0.3s;
}



    * {
        scroll-behavior: smooth;
}

    body {
        overflow-x: hidden;
}

    .navbar {
        transition: all 0.3s ease;
    
        background:transparent;
}


    .navbar-brand img {
     height: 64px;
    transition: transform 0.3s ease;
    position: absolute;
    width: 211px;
    /* top: 33px; */
    /*background: #fff;*/
    border-radius: 40px;
    transition: 0s;
}
}

    .navbar-brand:hover img {
        transform: scale(1.05);
}

    .hero-section {
        /*background: white;*/
        /*padding: 77px 0;*/
        color: #2C3E50;
        position: relative;
        overflow: hidden;
}



    .hero-content {
        position: relative;
        z-index: 2;
}

    .floating-animation {
        animation: float 6s ease-in-out infinite;
}

    @keyframes float {
    0%,
    100% { transform: translateY(0px);
    }
        50% { transform: translateY(-20px);
    }
}

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #FFC107, #FFD54F);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        color: #2C3E50;
        transition: all 0.4s ease;
        box-shadow: 0 10px 30px rgba(255,
    193,
    7,
    0.3);
}

    .feature-icon:hover {
        transform: translateY(-10px) scale(1.1);
        box-shadow: 0 20px 40px rgba(255,
    193,
    7,
    0.4);
}

    .screenshot-tab {
        border: 3px solid #FFC107;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 10px 30px rgba(0,
    0,
    0,
    0.1);
}

    .screenshot-tab:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0,
    0,
    0,
    0.15);
}

    .section-title {
        color: #2C3E50;
        font-weight: bold;
        margin-bottom: 3rem;
        position: relative;
}

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #FFC107, #28A745);
        border-radius: 2px;
}

    .btn-primary-custom {
        background: linear-gradient(135deg, #FFC107, #FFD54F);
        border: none;
        color: #2C3E50;
        font-weight: bold;
        padding: 12px 30px;
        border-radius: 25px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(255,
    193,
    7,
    0.3);
}

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #E0A800, #FFC107);
        color: #2C3E50;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,
    193,
    7,
    0.4);
}

    .footer {
        background: linear-gradient(135deg, #2C3E50, #34495e);
        color: white;
        padding: 50px 0 20px;
}

    .mobile-mockup {
        max-width: 300px;
        margin: 0 auto;
}

    .stats-section {
        background: linear-gradient(135deg, #28A745, #34ce57);
        color: white;
        padding: 60px 0;
}

    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        animation: countUp 2s ease-out;
}

    @keyframes countUp {
        from { opacity: 0; transform: translateY(20px);
    }
        to { opacity: 1; transform: translateY(0);
    }
}
/* FAQ section styles */
    .faq-section {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 80px 0;
}

    .faq-item {
        background: white;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(0,
    0,
    0,
    0.08);
        transition: all 0.3s ease;
        overflow: hidden;
}

    .faq-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,
    0,
    0,
    0.12);
}

    .faq-question {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 25px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2C3E50;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
}

    .faq-question:hover {
        background: linear-gradient(135deg, #FFC107, #FFD54F);
        color: #2C3E50;
}

    .faq-question .icon {
        transition: transform 0.3s ease;
        font-size: 1.2rem;
}

    .faq-question[aria-expanded="true"
] .icon {
        transform: rotate(180deg);
}

    .faq-answer {
        padding: 0 30px 25px;
        color: #666;
        line-height: 1.6;
}
/* Pulse animation for CTA buttons */
    .pulse-animation {
        animation: pulse 2s infinite;
}

    @keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255,
        193,
        7,
        0.7);
    }
        70% { box-shadow: 0 0 0 10px rgba(255,
        193,
        7,
        0);
    }
        100% { box-shadow: 0 0 0 0 rgba(255,
        193,
        7,
        0);
    }
}
/* Gradient text effect */
    .gradient-text {
        background: linear-gradient(135deg, #FFC107, #28A745);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
}
/* Card hover effects */
    .feature-card {
        transition: all 0.4s ease;
        border-radius: 20px;
        padding: 30px;
        background: white;
        box-shadow: 0 5px 20px rgba(0,
    0,
    0,
    0.08);
}

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,
    0,
    0,
    0.15);
}
/* Loading animation */
    .loading-dots {
        display: inline-block;
}

    .loading-dots::after {
        content: '';
        animation: dots 1.5s steps(5, end) infinite;
}

    @keyframes dots {
    0%,
    20% { content: '';
    }
        40% { content: '.';
    }
        60% { content: '..';
    }
        80%,
    100% { content: '...';
    }
}
</style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top p-3" id="mainNavbar">
  <div class="container">
    <!-- Brand / Logo -->
    <div class="goride-logoscroll">
<a class="navbar-brand d-flex align-items-center" href="#home">
  <img 
    id="navbar-logo" 
    src="{{ asset('/goride/img/logo-light.png') }}" 
    data-light="{{ asset('/goride/img/logo-light.png') }}"
    data-dark="{{ asset('/goride/img/logo-dark.png') }}"
    alt="goride-logo" 
    height="40">
</a>

    </div>

    <!-- Hamburger Button -->
 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
        data-bs-target="#navbarNav" aria-controls="navbarNav" 
        aria-expanded="false" aria-label="Toggle navigation">
  <span class="toggler-icon"></span>
  <span class="toggler-icon"></span>
  <span class="toggler-icon"></span>
</button>


    <!-- Collapsible Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#how-it-works">How It Works</a></li>
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#faq">FAQ</a></li>
        <li class="nav-item"><a class="nav-link" href="/goride-jobs#app">App</a></li>
      </ul>
    </div>
  </div>
</nav>



<section class="terms-section ">
  <div class="container">
    <div class="site-heading mb-3">
             
              <h2 class="site-title text-center my-3">
               Privacy  <span>Policy</span>
              </h2>
            </div>
    <p class="text-center mb-5" data-aos="fade-up" data-aos-delay="100">
      GoRide Platform respects your privacy. This Privacy Policy explains how we collect, use, and protect your information when you use our platform.

    </p>

    <!-- 1. Platform Nature -->
    <div class="mb-4" data-aos="fade-up">
       <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start"> Information We Collect</span>
           
            </div>
      <ul>
        <p  class="mb-3">   When you use GoRide, we may collect:</p>

        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Personal Information: Name, phone number, email address, profile details.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Business/Vehicle Details: Cab company name, license number, vehicle information, job postings.

</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Usage Data: Device details, IP address, browser type, location data, and activity logs</li>
         <li><i class="fas fa-check-circle me-2 text-secondary"></i>Communication Records: Messages, bids, and interactions made through the platform.</li>
      </ul>
    </div>

    <!-- 2. User Responsibilities -->
    <div class="mb-4" data-aos="fade-up">
        <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">How We Use Your Information</span>
           
            </div>
      <ul>
         <p  class="mb-3">  We use collected information to:</p>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Provide and operate the GoRide platform.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Enable job postings, bidding, and communication between Drivers and Cab Owners/Companies.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Improve security, verify user identities, and prevent fraud.</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Send important notifications (updates, changes in terms, commission policy, or services).
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Improve user experience, analytics, and platform performance.
.</li>
      </ul>
    </div>

    <!-- 3. Commission Policy -->
    <div class="mb-4" data-aos="fade-up">
      <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">Data Sharing & Disclosure</span>

            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>We do not sell or trade your personal information.</li>
        <p  class="mb-3"> We may share limited information with:</p>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Other users (to facilitate job postings and contact).

</li>
<li><i class="fas fa-check-circle me-2 text-secondary"></i>Service providers (for hosting, analytics, or support).


</li><li><i class="fas fa-check-circle me-2 text-secondary"></i>Legal authorities (if required by law, fraud, or misuse cases).


</li>
      </ul>
    </div>

    <!-- 4. Bidding & Acceptance -->
    <!--<div class="mb-4" data-aos="fade-up">-->
    <!-- <div class="site-heading mb-3">-->
    <!--          <span class="site-title-tagline justify-content-start">BCommission & Business Model Changes</span>-->
       
    <!--        </div>-->
    <!--  <ul>-->
    <!--    <li><i class="fas fa-check-circle me-2 text-secondary"></i> GoRide lets its users know that the platform is being run without commissions at present, but the business model may change in the future. If any fees, commissions, or new features are introduced</li>-->
        
    <!--  </ul>-->
    <!--</div>-->

    <!-- 5. Platform Rights -->
    <div class="mb-4" data-aos="fade-up">
      <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">Data Security</span>
         
            </div>
      <ul>
          
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>We implement reasonable technical and organizational measures to protect your data.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> However, no method of transmission over the internet is 100% secure; users share information at their own risk.
</li>
      
      </ul>
    </div>

    <!-- 6. Liability Disclaimer -->
    <div class="mb-4" data-aos="fade-up">
     <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">Cookies & Tracking</span>
 
            </div>
      <ul>  
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>We may use cookies and similar technologies for analytics, personalization, and security.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Users can control cookies through browser settings, but some features may not function properly if disabled.</li>
        
      </ul>
    </div>

    <!-- 7. Prohibited Activities -->
    <div class="mb-4" data-aos="fade-up">
  <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start"> Retention of Data</span>
         
            </div>
      <ul>
     <li><i class="fas fa-check-circle me-2 text-secondary"></i> We retain your data as long as necessary for providing services and complying with legal obligations.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>Inactive accounts may be deleted after a certain period.</li>
        
      </ul>
    </div>

    <!-- 8. Termination -->
    <div class="mb-4" data-aos="fade-up">
  <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">Platform Rights</span>
         
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>GoRide reserves the right to update, modify, or discontinue services at any time.
</li>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i> Changes to this Privacy Policy will be notified via the platform or email.</li>
      </ul>
    </div>

    <!-- 9. Data & Privacy -->
    <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start"> Governing Law</span>
         
            </div>
      <ul>
        <li><i class="fas fa-check-circle me-2 text-secondary"></i>	This Privacy Policy shall be governed by the laws of India, but applies to our global users.
</li>
        
      </ul>
    </div>

    <!-- 10. Governing Law -->
    <div class="mb-4" data-aos="fade-up">
    <div class="site-heading mb-3">
              <span class="site-title-tagline justify-content-start">Contact Us</span>
         
            </div>
      <ul>
     <li>
 <i class="fas fa-check-circle me-2 text-secondary"></i>	
  For privacy-related concerns or requests, contact us at:
  <a href="mailto:support@goride.run">
    <i class="fas fa-envelope ms-1"></i> support@goride.run
  </a>
</li>

    
      </ul>
    </div>

    <p class="mt-4 fw-bold text-center" data-aos="zoom-in">
      By using GoRide, you confirm that you have read, understood, and agreed to these Privacy &  Policy .
    </p>
  </div>
</section>





    <!-- Footer -->
<footer class="footer-area">
    <div class="footer-widget">
        <div class="container">
            <div class="row footer-widget-wrapper pt-120 pb-70 d-flex justify-content-between">
                <!-- About Us -->
                <div class="col-md-6 col-lg-4">
                    <div class="footer-widget-box about-us">
                        <a href="#" class="footer-logo">
                          <img src="{{ asset('goride/img/logo-light.png') }}" alt="goride-logo" >
                        </a>
                        <p>Connecting Drivers, Cab Owners & Companies – Worldwide Jobs Platform</p>
                         <ul class="footer-contact list-unstyled p-0 m-0 d-flex d-md-block flex-wrap gap-2">
  <li class="d-inline-flex align-items-center mb-md-2">
    <a href="tel:+916369742104" class="d-inline-flex align-items-center">
      <i class="fas fa-phone me-2"></i> +91 63697 42104
    </a>
  </li>
  <li class="d-inline-flex align-items-center">
    <a href="mailto:support@goride.run" class="d-inline-flex align-items-center">
      <i class="fas fa-envelope me-2"></i> support@goride.run
    </a>
  </li>
</ul>
                    </div>
                </div>

              <!-- Resources -->
<div class="col-6 col-md-6 col-lg-2">
  <div class="footer-widget-box list">
    <h4 class="footer-widget-title">Resources</h4>
    <ul class="footer-list">
      <li><a href="#about"><i class="fas fa-caret-right"></i> About </a></li>
      <li><a href="#features"><i class="fas fa-caret-right"></i> Features</a></li>
      <li><a href="#how-it-works"><i class="fas fa-caret-right"></i> How It Works</a></li>
      <li><a href="#faq"><i class="fas fa-caret-right"></i> Faq</a></li>
      <li><a href="#app"><i class="fas fa-caret-right"></i> App</a></li>
    </ul>
  </div>
</div>

<!-- Quick Links -->
<div class="col-6 col-md-6 col-lg-3">
  <div class="footer-widget-box list">
    <h4 class="footer-widget-title">Quick Links</h4>
    <ul class="footer-list">
      <li><a href="/terms-jobs"><i class="fas fa-caret-right"></i> Terms & Conditions</a></li>
      <li><a href="/policy-jobs"><i class="fas fa-caret-right"></i> Privacy Policy</a></li>
    </ul>
  </div>
</div>


              
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <p class="copyright-text">
                        ©<span id="date">2025</span> Copyright  <a href="#">GoRide</a> All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6  col-12 pt-4 pt-md-0 align-self-center">
                    <ul class="footer-social">
                        <li><a href="https://www.facebook.com/profile.php?id=61564856917550"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://x.com/go_rides8499"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/in/go-rides"><i class="fab fa-linkedin-in"></i></a></li>
                        <li><a href="https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow"><i class="fab fa-youtube"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
     <script>
    
$(document).ready(function () {
    
    document.querySelectorAll('#navbarNav .nav-link').forEach(link => {
  link.addEventListener('click', function () {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.getElementById('navbarNav');

    // Close only if menu is open (mobile view)
    if (window.getComputedStyle(navbarToggler).display !== "none") {
      new bootstrap.Collapse(navbarCollapse).hide();
    }
  });
});

window.addEventListener("scroll", function () {
  const navbar = document.getElementById("mainNavbar");
  const logo = document.getElementById("navbar-logo");
  const navLinks = document.querySelectorAll("#mainNavbar .nav-link");

  const lightLogo = logo.getAttribute("data-light");
  const darkLogo = logo.getAttribute("data-dark");

  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");

    // ✅ Change to dark logo
    logo.src = darkLogo;
    logo.style.background = "#fff";     
    logo.style.borderRadius = "40px";  
    logo.style.padding = "5px";  
    logo.style.width = "204px"; 
    logo.style.height= "70px"; 
    logo.style.top= "-27px"; 
 

    // ✅ Nav links black
    navLinks.forEach(link => {
      link.style.color = "#000";
      link.addEventListener("mouseenter", () => link.style.color = "#ff9900"); // hover
      link.addEventListener("mouseleave", () => link.style.color = "#000");    // back to black
    });
  } else {
    navbar.classList.remove("scrolled");

    // ✅ Change back to light logo
    logo.src = lightLogo;
    logo.style.background = "transparent";
    logo.style.borderRadius = "0"; 
    logo.style.padding = "0";
    logo.style.width = "auto";  

    // ✅ Nav links white
    navLinks.forEach(link => {
      link.style.color = "#fff";
      link.addEventListener("mouseenter", () => link.style.color = "#ff9900"); // hover
      link.addEventListener("mouseleave", () => link.style.color = "#fff");    // back to white
    });
  }
});


    
 window.addEventListener("scroll", function () {
    const nav = document.getElementById("mainNavbar");
    const logoBox = document.querySelector(".goride-logoscroll");

    if (window.scrollY > 50) {
      nav.classList.add("scrolled");
      logoBox.classList.add("scrolled");
    } else {
      nav.classList.remove("scrolled");
      logoBox.classList.remove("scrolled");
    }
  });
 $(".roadmap-mobile").owlCarousel({
  loop: true,
  margin: 30,
  autoplay: true,
  autoplayTimeout: 4000, // 4 seconds between slides
  autoplaySpeed: 1500,   // smooth slide transition
  smartSpeed: 1500,      // transition animation speed
  autoplayHoverPause: false,
  dots: false,
  nav: false,
  responsive: {
    0: { items: 2 },
    576: { items: 3 },
    768: { items: 4 },
    992: { items: 5 },
    1200: { items: 6 }
  }
});

  $('.popup-youtube').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });
   // Initialize AOS (Animate On Scroll)
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Counter animation for stats
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[^\d]/g, ''));
                const suffix = counter.textContent.replace(/[\d]/g, '');
                let current = 0;
                const increment = target / 100;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target + suffix;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current) + suffix;
                    }
                }, 20);
            });
        }

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.stats-section');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });
        observer.observe(statsSection);

        // Add loading effect to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.href && this.href.includes('#')) {
                    return; // Skip for anchor links
                }
                
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading<span class="loading-dots"></span>';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });

        // Add parallax effect to hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section');
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        });

        // Add hover effect to feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.background = 'linear-gradient(135deg, #fff, #f8f9fa)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.background = 'white';
            });
        });
});

       
        
    </script>
</body>
</html>
