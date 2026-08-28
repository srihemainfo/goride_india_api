@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('goride/css/custom-plan-style.css') }}">
<link rel="stylesheet" href="{{ asset('goride/css/custom-plan-flaticon.css') }}">
<script src="{{ asset('goride/js/custom-plan-main.js') }}"></script>


<style>

.accordion-box .block .acc-btn:before{
    top:14px;
}
.btn-signin{
     font-family: 'Outfit', sans-serif;
}

.accordion-box .block .acc-btn.active{
    padding:22px 16px;
}
.accordion-box .block .acc-btn{
        padding:22px 16px;
}
.accordion-box .block .content{
    padding:16px 16px;
        font-family: 'Outfit', sans-serif;
        line-height:1.8;
}
.about     width: 24px;
    height: 24px;
}
 .item .curv-butn .br-right-bottom {
    position: absolute;
    bottom: -1px;
    right: -24px;
    -webkit-transform: rotate(270deg);
    -ms-transform: rotate(270deg);
    transform: rotate(270deg);
    line-height: 1;
}
.item .curv-butn .br-left-top svg {
    width: 24px;
    height: 24px;
}
.item .curv-butn .br-left-top {
    position: absolute;
    top: -24px;
    left: -1px;
    -webkit-transform: rotate(270deg);
    -ms-transform: rotate(270deg);
    transform: rotate(270deg);
    line-height: 1;
}
 .item img {
    width: 100%;
    transform: scale(1);
    transition: transform 500ms 
ease;
}
.item .curv-butn {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 90px;
    height: 90px;
    line-height: 90px;
    text-align: center;
    border-radius: 0 40px 0 0;
}
.icon-bg {
    background: #fff !important;
}
 .item img {
    width: 100%;
    transform: scale(1);
    transition: transform 500ms 
ease;
}
.item {
    position: relative;
    border-radius: 20px 20px 20px 0;
    overflow: hidden;
    margin-bottom: 15px;
    isolation: isolate;
}

.rate-feature {
    display: flex
;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
 .rate-item {
      /*width: 320px;*/
      background:white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(6, 22, 58, 0.1);
      overflow: hidden;
      transition: all 0.5s ease-in-out;
      position: relative;
      z-index: 1;
    }

    .rate-item:hover {
      transform: translateY(-10px);
    }

    .rate-header img {
      width: 100%;
      border-radius: 20px 20px 0 0;
      /*height: 250px;*/
    }

    .rate-header-content {
      text-align: center;
      margin: 25px 0 15px 0;
    }

    .rate-header-content h4 {
      font-size: 22px;
      font-weight: 700;
      text-transform: uppercase;
      color: #0f172a;
      margin: 0;
    }

    .rate-header-content p {
      color: #f9bf00;
      font-weight: 600;
      margin-top: 8px;
    }

    .rate-content {
          background: #1c1e22;
    padding: 12px 12px;
    /* border-radius: 20px; */
    position: relative;
    z-index: 1;
    display: flex
;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    }

    .rate-content::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
      background-repeat: repeat;
      opacity: 0.2;
      border-radius: 20px;
      z-index: -1;
    }

   .rate-icon {
    display: flex
;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 36px;
    /* border-radius: 50%; */
    font-size: 56px;
    /* color: white; */
    /* background: #f9bf00; */
    /* margin: -70px auto 0 auto; */
    position: relative;
    /* box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

    .rate-icon::before {
     content: "";
    position: absolute;
    inset: -2px;
    border: 2px dashed #f9bf00;
    /* border-radius: 50%; */
    /* animation: rotate 8s 
linear infinite; */
}

    @keyframes rotate {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

   .rate-icon p {
    font-size: 21px;
    color: wheat;
    font-weight: 600;
}

    /*.rate-feature {*/
    /*  margin-top: 30px;*/
    /*}*/

    .rate-feature ul {
      padding: 0px;
    margin: 3px;
    list-style: none;
    }

    .rate-feature ul li {
      color: #fff;
      margin: 10px 0;
      display: flex;
      /*justify-content: space-between;*/
      align-items: center;
      font-size: 15px;
      font-weight:500;
    }

    .rate-feature ul li i {
      color: #f9bf00 !important;
      margin-right: 8px;
    }

    .theme-btn {
     display: block;
    width: 100%;
    text-align: center;
    padding: 3px 0px;
    background: #f9bf00;
    color: #0f172a;
    text-transform: uppercase;
    border-radius: 50px;
    font-weight: 600;
    font-size:13px;
    border: none;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.4s 
ease-in-out;
    }

    .theme-btn:hover {
      background: white;
      color: #f9bf00;
    }

    .theme-btn i {
      margin-left: 6px;
    }
.why-choose {
  background: url('{{ asset('goride/img/car.webp') }}') center center/cover no-repeat;
  /*padding: 80px 20px;*/
  color: #fff;
  text-align: center;
  position: relative;
}
.overlay {
  background-color: rgba(0, 0, 0, 0.6); /* dark overlay for readability */
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
  max-width: 1000px;
  margin: 0 auto;
}

.feature-box {
    background-color: rgb(255 255 255 / 90%);
    padding: 20px;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.feature-box:hover {
    transform: translateY(-5px);
    background-color: rgb(255 251 255);

}

.feature-box h3 {
  font-size: 1.2rem;
  margin-bottom: 10px;
}
.feature-box p {
  
    font-weight: bold;
    color: #7a3d0c;
}


 .fare-options h3 {
      color: #7a3d0c;
      font-size: 18px;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .price-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 25px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .price-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card-details h4 {
      margin: 0;
      font-size: 16px;
      font-weight: 600;
      color: #222;
    }

    .card-details small {
      color: #777;
    }

    .card-price {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .card-price span {
      font-size: 20px;
      font-weight: 600;
      color: #e66100;
    }
.card-price button {
  background: #f9bf00;
  color: #222;
  border: none;
    padding: 2px 14px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.4s ease-in-out;
  font-weight: 600;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}

.card-price button:hover {
  background: #e66100;
  color: #fff;
  transform: scale(1.08);
  box-shadow: 0 6px 12px rgba(230, 97, 0, 0.4);
}

    .default-animation img {
        width: 21px;
        height: 21px;
    }

    @media only screen and (max-width: 767px) {
        .accordion-box .block .acc-btn{
            line-height:1.8;
        }
        
      .page-header-info h1 {
  font-size: 26px !important;
      }
        .price-card{
            flex-direction: column;
              gap: 15px;
        }
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
@php
    //dd($seoTags);
    $disCripTion = trim(($seoTags['wikiDes'] ?? '') . ' ' . ($seoTags['shortNote'] ?? ''));
    if(isset($seoTags['innerLinks']) && count($seoTags['innerLinks']) > 0){
    
    
    shuffle($seoTags['innerLinks']);
    $disCripTion = array_reduce($seoTags['innerLinks'], function ($description, $linkData) {
        $anchorTag = '<a href="' . $linkData->slug . '" style="color: #467bbe; text-decoration: none;">' . $linkData->name . '</a>';
        return preg_replace('/' . preg_quote($linkData->name, '/') . '/', $anchorTag, $description, 1);
    }, $disCripTion);
    
    }
    
    //dd($disCripTion);
  $seoTags['faqData'] = $seoTags['faqData']??[];

@endphp

@if($seoTags['page_exist'])
    
    <!-- Breadcrumb -->
 <section class="page-header" 
    style="background-image: url('{{ request()->is('car-rental/chennai-to-madurai-outstation-cab-service') ? asset('goride/img/car.webp') : asset('goride/img/breadcrump_banner.webp') }}');">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info main-banner-content mt-5 pt-5">
            <!--<h4>About Us!</h4>-->
            <h1> {{ isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) . ' Taxi/Cab Dispatch Software  with Goride' : "Cab Booking & Dispatch Software" }}
            </h1>
            <!-- <h1></h1> -->
            <p>{{ $seoTags['metaDes'] ?? "Unlock smart mobility by leveraging the power of our next-gen cab booking software with advanced dispatch system" }}
            </p>
            <div class="banner-btn">
                <!--<a href="#" class="default-btn-one">More About Us</a>-->
                <!--<a href="https://www.youtube.com/watch?v=_ysd-zHamjk" class="video-btn popup-youtube">Start a Free Trail<i class="fa-solid fa-arrow-right"></i></a>-->
            </div>
        </div>
    </div>
</section>

<!--<section class="choose-section">-->
<!--    <div class="container">-->
<!--        <div class="row align-items-center mb-2">-->
<!--            <div-->
<!--                class="{{ (isset($seoTags['img']) && $seoTags['img'] != '') ? 'col-lg-6 col-md-12' : 'col-lg-16 col-md-12' }} ">-->
<!--                <div class="choose-content-area">-->

                    <!-- <span>Dedicated CRM</span> -->
<!--                    <h3>{{ $seoTags['metaTitle'] ?? "GoRide Will Help Take Your Cab Business To The Next Level" }}</h3>-->
<!--                    <p>{!! $disCripTion ?? '' !!}</p>-->
                    <!--<p>{{ $seoTags['wikiDes'] ?? '' }}</p>-->
                    <!--<p>{{ $seoTags['shortNote'] ?? '' }}</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            @if (isset($seoTags['img']) && $seoTags['img'] != '')-->
<!--                <div class="col-lg-6 col-md-12">-->
<!--                    <div class="choose-image">-->
<!--                        <img src="{{ asset($seoTags['img']) }}" alt="image" style="width:100%;height:400px;">-->
<!--                    </div>-->
<!--                </div>-->
<!--            @endif-->
<!--        </div>-->
<!--        <div class="row align-items-center">-->
<!--            <div class="col-lg-6 col-md-12">-->
<!--                <div class="choose-content-area">-->
<!--                    <span>Dedicated CRM</span>-->
<!--                    <h3>GoRide Will Help Take Your Cab Business To The Next Level</h3>-->
<!--                    <p>"Customer Relationship Management." It refers to a system or software designed to manage a-->
<!--                        company’s interactions with current and potential customers. You mentioned that you developed-->
<!--                        and customized the CRM's features while ensuring the content was accurate—what were some of the-->
<!--                        key customizations you implemented</p>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Contact Management</h4>-->
<!--                        <p>Store and manage customer and prospect information, such as names, contact details, and-->
<!--                            interaction history</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Sales Management</h4>-->
<!--                        <p>Track sales opportunities, pipelines, and deals in different stages, from lead generation to-->
<!--                            closing.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Lead Management</h4>-->
<!--                        <p>Capture, track, and manage potential customers through the sales funnel.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-btn">-->
                        <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-6 col-md-12">-->
<!--                <div class="choose-image">-->
<!--                    <img src="{{ asset('goride/img/custom-plan/crm.webp') }}" alt="image">-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<!--<section class="choose-section" style="padding:60px 0;">-->
<!--    <div class="container">-->
<!--        <div class="row align-items-center mb-4">-->
<!--            <div class="{{ (isset($seoTags['img']) && $seoTags['img'] != '') ? 'col-lg-6 col-md-12' : 'col-lg-12 col-md-12' }}">-->
<!--                <div class="choose-content-area" style="color:#333;">-->
<!--                    <h3 style="font-size:32px;font-weight:700;margin-bottom:15px;color:#222;">-->
<!--                        {{ $seoTags['metaTitle'] ?? 'Chennai to Madurai Cab Service – Book Affordable & Comfortable Rides' }}-->
<!--                    </h3>-->
<!--                    <p style="font-size:17px;line-height:1.8;margin-bottom:0;">-->
<!--                        {!! $disCripTion ?? 'Looking for a reliable <b>Chennai to Madurai cab service</b>? We offer safe, comfortable, and affordable one-way and round-trip taxi options from Chennai to Madurai. Whether you’re traveling for business, pilgrimage, or a family visit, our experienced drivers and well-maintained cars ensure a hassle-free journey.' !!}-->
<!--                    </p>-->
<!--                </div>-->
<!--            </div>-->
<!--            @if (isset($seoTags['img']) && $seoTags['img'] != '')-->
<!--                <div class="col-lg-6 col-md-12 mt-4 mt-lg-0">-->
<!--                    <div class="choose-image" style="border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">-->
<!--                        <img src="{{ asset($seoTags['img']) }}" alt="Chennai to Madurai Route" style="width:100%;height:400px;object-fit:cover;">-->
<!--                    </div>-->
<!--                </div>-->
<!--            @endif-->
<!--        </div>-->

        <!--<div class="row align-items-start mt-4">-->
        <!--    <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">-->
        <!--        <div class="choose-content-area" style="color:#333;">-->
        <!--            <span style="display:inline-block;background:#ffe1d2;color:#d9480f;padding:6px 14px;border-radius:20px;font-weight:600;font-size:14px;margin-bottom:15px;">Distance & Travel Time</span>-->
        <!--            <h3 style="font-size:26px;font-weight:700;margin-bottom:15px;color:#222;">Chennai to Madurai Route Details</h3>-->
        <!--            <p style="font-size:16px;line-height:1.8;margin-bottom:25px;">-->
        <!--                The distance between <b>Chennai and Madurai</b> is approximately <b>460 km</b>, and the journey takes around <b>7 to 8 hours</b> via NH38. Our professional drivers ensure a smooth and enjoyable journey with regular rest stops for your comfort.-->
        <!--            </p>-->

        <!--            <div style="margin-bottom:25px;">-->
        <!--                <h4 style="font-size:20px;font-weight:700;color:#d9480f;margin-bottom:15px;">Taxi Fare Options</h4>-->
        <!--                <ul style="padding-left:18px;line-height:1.8;list-style:none;margin:0;">-->
        <!--                    <li style="margin-bottom:12px;">-->
        <!--                        <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #f1f1f1;border-radius:10px;padding:12px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">-->
        <!--                            <div>-->
        <!--                                <b>Sedan (Etios, Dzire)</b><br>-->
        <!--                                <small style="color:#666;">Ideal for small families</small>-->
        <!--                            </div>-->
        <!--                            <div style="display:flex;align-items:center;gap:10px;">-->
        <!--                                <span style="background:#d9480f;color:#fff;padding:6px 12px;border-radius:8px;font-weight:600;">₹6,299</span>-->
        <!--                                <a href="/book-now?car=sedan" style="font-size:14px;padding:6px 12px;background:#222;color:#fff;border-radius:6px;text-decoration:none;">Book Now</a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li style="margin-bottom:12px;">-->
        <!--                        <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #f1f1f1;border-radius:10px;padding:12px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">-->
        <!--                            <div>-->
        <!--                                <b>SUV (Innova, Ertiga)</b><br>-->
        <!--                                <small style="color:#666;">Perfect for group travel</small>-->
        <!--                            </div>-->
        <!--                            <div style="display:flex;align-items:center;gap:10px;">-->
        <!--                                <span style="background:#d9480f;color:#fff;padding:6px 12px;border-radius:8px;font-weight:600;">₹8,499</span>-->
        <!--                                <a href="/book-now?car=suv" style="font-size:14px;padding:6px 12px;background:#222;color:#fff;border-radius:6px;text-decoration:none;">Book Now</a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                    <li>-->
        <!--                        <div style="display:flex;justify-content:space-between;align-items:center;background:#fff;border:1px solid #f1f1f1;border-radius:10px;padding:12px 18px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">-->
        <!--                            <div>-->
        <!--                                <b>Premium Cars</b><br>-->
        <!--                                <small style="color:#666;">For luxury and comfort</small>-->
        <!--                            </div>-->
        <!--                            <div style="display:flex;align-items:center;gap:10px;">-->
        <!--                                <span style="background:#d9480f;color:#fff;padding:6px 12px;border-radius:8px;font-weight:600;">₹10,999</span>-->
        <!--                                <a href="/book-now?car=premium" style="font-size:14px;padding:6px 12px;background:#222;color:#fff;border-radius:6px;text-decoration:none;">Book Now</a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </li>-->
        <!--                </ul>-->
        <!--            </div>-->


        <!--            <div style="margin-bottom:25px;">-->
        <!--                <h4 style="font-size:20px;font-weight:700;color:#d9480f;margin-bottom:10px;">Why Visit Madurai?</h4>-->
        <!--                <p style="font-size:16px;line-height:1.8;">-->
        <!--                    Known as the <b>“Temple City”</b>, Madurai is famous for the <b>Meenakshi Amman Temple</b>, one of India’s oldest and most magnificent temples. The city’s heritage, vibrant markets, and South Indian delicacies make it a must-visit destination.-->
        <!--                </p>-->
        <!--            </div>-->

        <!--            <div>-->
        <!--                <h4 style="font-size:20px;font-weight:700;color:#d9480f;margin-bottom:10px;">Why Choose GoRide?</h4>-->
        <!--                <ul style="padding-left:18px;line-height:1.8;">-->
        <!--                    <li>24×7 customer support</li>-->
        <!--                    <li>Experienced and verified drivers</li>-->
        <!--                    <li>Clean, sanitized, and well-maintained vehicles</li>-->
        <!--                    <li>Transparent pricing – no hidden charges</li>-->
        <!--                    <li>Easy online booking & cancellation</li>-->
        <!--                </ul>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->

        <!--    <div class="col-lg-6 col-md-12">-->
        <!--        <div class="choose-image" style="border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">-->
        <!--            <img src="{{ asset('goride/img/custom-plan/chennai-madurai.webp') }}" alt="Chennai to Madurai Taxi Service" style="width:100%;height:600px;object-fit:cover;">-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
<!--    </div>-->
<!--</section>-->

<section class="choose-section  pb-5 ">
    <div class="container">
        <div class="row align-items-center mb-5 gap-5 gap-md-0">
            <div class="col-lg-8 col-md-12 order-1 order-lg-1">
                <div class="choose-content-area">
                    <span>Distance and Travel Time</span>
                    @php
                        if($seoTags['slug'] && $seoTags['slug'] != ''){
                            $explode = explode('-', $seoTags['slug']);
                        }
                    @endphp
                    <h3>{{ ucfirst($explode[0]) }} To {{ ucfirst($explode[2]) }} Route Details</h3>
                      <p >
                        {{$seoTags['shortNote'] ?? 'The approximate distance between the these locations is around '.$seoTags['kms'].' kms. The distance may vary slightly depending on the specific route taken and the mode of travel. Road networks and connectivity play an important role in determining the exact distance. This information helps in planning travel, transport, and logistics effectively between different destinations.'}}
                      </p>
                       <h3>Why Visit {{ ucfirst($explode[2]) }}?</h3>
                     <p>
                        {!!$seoTags['wikiDesHtml'] ?? ''!!}
                     </p>
                      
                       
                   
                </div>
            </div>
            <div class="col-lg-4 col-md-12 order-1 order-lg-2">
               <div class="item aos-init aos-animate" data-aos="fade-down" data-aos-duration="1000"> <img src="{{$seoTags['img']??'https://www.goride.net.in/goride/img/indian-about.jpg'}}" class="img-fluid" alt="about">
          <div class="curv-butn icon-bg">
            <img src="https://www.goride.net.in/goride/img/g.png" alt="Play Button" style="width: 50px; height: 50px;">
            <div class="br-left-top">
              <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                <path d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z" fill="#ffffff"></path>
              </svg>
            </div>
            <div class="br-right-bottom">
              <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                <path d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z" fill="#ffffff"></path>
              </svg>
            </div>
          </div>
        </div>
            </div>
            
        </div>
        <div class="row align-items-center">
            <div class="choose-content-area">
                <h3>Taxi Fare Options</h3>
            </div>
            <!--<div class="col-lg-6">-->
            <!--     <div class="fare-options choose-content-area">-->
            <!--          <h3>Taxi Fare Options</h3>-->
                
            <!--          <div class="price-card">-->
            <!--            <div class="card-details">-->
            <!--              <h4>Sedan (Etios, Dzire)</h4>-->
            <!--              <small>Ideal for small families</small>-->
            <!--            </div>-->
            <!--            <div class="card-price">-->
            <!--              <span>₹6,299</span>-->
            <!--              <button>Book Now</button>-->
            <!--            </div>-->
            <!--          </div>-->
                
            <!--          <div class="price-card">-->
            <!--            <div class="card-details">-->
            <!--              <h4>SUV (Innova, Ertiga)</h4>-->
            <!--              <small>Perfect for group travel</small>-->
            <!--            </div>-->
            <!--            <div class="card-price">-->
            <!--              <span>₹8,499</span>-->
            <!--              <button>Book Now</button>-->
            <!--            </div>-->
            <!--          </div>-->
                
            <!--          <div class="price-card">-->
            <!--            <div class="card-details">-->
            <!--              <h4>Premium Cars</h4>-->
            <!--              <small>For luxury and comfort</small>-->
            <!--            </div>-->
            <!--            <div class="card-price">-->
            <!--              <span>₹10,999</span>-->
            <!--              <button>Book Now</button>-->
            <!--            </div>-->
            <!--          </div>-->
                     
                         
                      <!--    <h3 class="text-white">Why Choose GoRide?</h3>-->
                      <!--<p>✔ 24×7 customer support</p>-->
                      <!--<p>✔ Experienced and verified drivers</p>-->
                      <!--<p>✔ Clean and well-maintained vehicles</p>-->
            <!--          </div>-->
                      
            <!--</div>-->
            
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/swift.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Swift/Etios </p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                               <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['one_four'], true)['Actual'] ?? 'N/A' }} </li>
                          
                            <li><i class="fa-solid fa-check-double"></i>1 to 4 Seater</li>
                       
                          
                          
                          
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div> 
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                <div class="rate-header">
                  <img src="{{ asset('goride/img/xylo.webp') }}" alt="Premium Car">
                </div>
                <div class="rate-content">
                  <div class="rate-icon">
                  <p>Tavera/Xylo/Ertigo </p>
                  </div>
                  <div class="rate-feature">
                    <ul>
                        <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['five_six'], true)['Actual'] ?? 'N/A' }} </li>
                        <li><i class="fa-solid fa-check-double"></i>5 to 6 Seater</li>
                    </ul>
                    <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>   
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/innova.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Innova </p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                            <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['five_seven'], true)['Actual'] ?? 'N/A' }} </li>
                            <li><i class="fa-solid fa-check-double"></i>5 to 7 Seater</li>
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div>   
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                <div class="rate-header">
                  <img src="{{ asset('goride/img/tempo.webp') }}" alt="Premium Car">
                </div>
                <div class="rate-content">
                  <div class="rate-icon">
                  <p>Tempo traveller</p>
                  </div>
                  <div class="rate-feature">
                    <ul>
                        <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['eight_onethree'], true)['Actual'] ?? 'N/A' }} </li>
                        <li><i class="fa-solid fa-check-double"></i>8 to 13 Seater</li>
                    </ul>
                    <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>  
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/tempo.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Tempo traveller</p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                            <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['onefour_oneeight'], true)['Actual'] ?? 'N/A' }} </li>
                            <li><i class="fa-solid fa-check-double"></i>14 to 18 Seater</li>
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div> 
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/mini_bus.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Mini Bus</p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                            <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['onenine_twoone'], true)['Actual'] ?? 'N/A' }} </li>
                            <li><i class="fa-solid fa-check-double"></i>19 to 21 Seater</li>
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div>   
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/mini_bus2.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Mini Bus</p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                            <li><i class="fa-solid fa-check-double"></i>{{ json_decode($seoTags['twotwo_twofive'], true)['Actual'] ?? 'N/A' }} </li>
                            <li><i class="fa-solid fa-check-double"></i>22 to 25 Seater</li>
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="rate-item aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                    <div class="rate-header">
                      <img src="{{ asset('goride/img/bus.webp') }}" alt="Premium Car">
                    </div>
                    <div class="rate-content">
                      <div class="rate-icon">
                      <p>Bus</p>
                      </div>
                      <div class="rate-feature">
                        <ul>
                            <li><i class="fa-solid fa-check-double"></i> {{ json_decode($seoTags['twosix_fivezero'], true)['Actual'] ?? 'N/A' }} </li>
                            <li><i class="fa-solid fa-check-double"></i>26 to 50 Seater</li>
                        </ul>
                        <button class="theme-btn">Book Now <i class="fa-solid fa-arrow-right"></i></button>
                      </div>
                    </div>
                  </div>
            </div>
            
            <!--<div class="col-lg-6">-->
            <!--    <div class="choose-content-area">-->
            <!--               <h3>Why Visit Madurai?</h3>-->
            <!--         <p>Known as the <strong>“Temple City”</strong>, Madurai is famous for the -->
            <!--             <strong>Meenakshi Amman Temple</strong>, one of India’s oldest and most magnificent temples.-->
            <!--             The city’s heritage, vibrant markets, and South Indian delicacies -->
            <!--             make it a must-visit destination.</p>-->
            <!--             </div>-->
            <!--          </div>-->
        </div>
    </div>
</section>

<section class="why-choose ">
  <div class="overlay">
<h2 class="text-white">Why Choose GoRide?</h2>
    <div class="features">
      <div class="feature-box">
        <h3>✔ 24×7 Customer Support</h3>
        <p>Always available to assist you anytime, anywhere.</p>
      </div>
      <div class="feature-box">
        <h3>✔ Verified Drivers</h3>
        <p>Experienced professionals with background checks.</p>
      </div>
      <div class="feature-box">
        <h3>✔ Clean Vehicles</h3>
        <p>Well-maintained and sanitized for your comfort.</p>
      </div>
      <div class="feature-box">
        <h3>✔ Safe & Reliable</h3>
        <p>Timely rides with trusted service standards.</p>
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

<!--<section class="choose-section">-->
<!--    <div class="container">-->
<!--        <div class="row align-items-center">-->
<!--            <div class="col-lg-6 col-md-12">-->
<!--                <div class="choose-content-area">-->
<!--                    <span>Driver App</span>-->
<!--                    <h3>Streamline Your Driving Experience with Our Advanced Driver App</h3>-->
<!--                    <p>A driver app provides essential tools for drivers, including navigation, ride requests, and-->
<!--                        real-time tracking. It enables efficient route management, helps with fare calculations, and-->
<!--                        supports communication with passengers.</p>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Real-Time Navigation</h4>-->
<!--                        <p>Provides accurate, turn-by-turn directions to help drivers reach their destinations-->
<!--                            efficiently.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Ride Request Management</h4>-->
<!--                        <p>Allows drivers to accept or decline ride requests, view passenger details, and manage their-->
<!--                            schedule</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Real-Time Tracking</h4>-->
<!--                        <p>Enables drivers to track their location and view incoming ride requests on a map for optimal-->
<!--                            route planning</p>-->
<!--                    </div>-->
<!--                    <div class="choose-btn">-->
                        <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-6 col-md-12">-->
<!--                <div class="choose-image">-->
<!--                    <img src="{{ asset('goride/img/custom-plan/driver-app-mockup.webp') }}" alt="image">-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<!--<section class="choose-section">-->
<!--    <div class="container">-->
<!--        <div class="row align-items-center">-->
<!--            <div class="col-lg-6 col-md-12 order-2 order-lg-1">-->
<!--                <div class="choose-image">-->
<!--                    <img src="{{ asset('goride/img/custom-plan/passenger-app-mockup.webp') }}" alt="image">-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-6 col-md-12 order-1 order-lg-2">-->
<!--                <div class="choose-content-area">-->
<!--                    <span>Passenger App</span>-->
<!--                    <h3>Enhance Your Experience with Our User-Friendly Passenger App</h3>-->
<!--                    <p>A Passenger App simplifies the booking process with intuitive scheduling, real-time tracking, and-->
<!--                        secure payment options. It enhances user experience by providing seamless access to services and-->
<!--                        up-to-date information at their fingertips.</p>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Easy Booking and Scheduling</h4>-->
<!--                        <p>Allows customers to book services or schedule rides with a few taps, view available options,-->
<!--                            and receive instant confirmations.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Real-Time Tracking</h4>-->
<!--                        <p>Provides live tracking of the service provider or vehicle, so customers can monitor their-->
<!--                            arrival and plan accordingly.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-text">-->
<!--                        <i class="fa-regular fa-check"></i>-->
<!--                        <h4>Seamless Payment Integration</h4>-->
<!--                        <p>Supports secure and convenient payment options, including credit cards and digital wallets,-->
<!--                            with options to view and manage transaction history.</p>-->
<!--                    </div>-->
<!--                    <div class="choose-btn">-->
                        <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<!-- Start Services Section -->
<!--<section class="services-section">-->
<!--    <div class="container">-->
<!--        <div class="section-title">-->
<!--            <span>Services</span>-->
<!--            <h3>How We Can Help?</h3>-->
<!--        </div>-->
<!--        <div class="row">-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/calender.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>Real-Time Booking & Dispatch</h3>-->
<!--                    <p>Fast & easy booking process with advance booking option & smart algorithm to match available-->
<!--                        nearest drivers with riders' requirements</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/driver.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>Driver Management Tools</h3>-->
<!--                    <p>Advanced driver management features, including managing driver profiles, vehicle details,-->
<!--                        performance analytics, & operating & monitoring.</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/map.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>GPS Tracking and Navigation</h3>-->
<!--                    <p>AI-based GPS tracking to ensure accurate real-time location of the vehicle with integrated-->
<!--                        navigation tools to help drivers find the most efficient routes</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/file.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>Automated Fare</h3>-->
<!--                    <p>Automated accurate fare calculation based on distance, time, & additional charges ensuring-->
<!--                        transparency in pricing & allowing the riders.</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/gateway.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>Multiple Payment Options</h3>-->
<!--                    <p>Secure and flexible payment options integrating multiple payment methods, such as credit/debit-->
<!--                        cards, digital wallets, or many other forms.</p>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-4 col-md-6 col-sm-6">-->
<!--                <div class="single-services-box">-->
<!--                    <div class="icon">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/alarm.png') }}" alt="icon">-->
<!--                    </div>-->
<!--                    <h3>Automated Alerts/Notifications</h3>-->
<!--                    <p>Automated alerts about the latest status of the ride, including driver acceptance, estimated-->
<!--                        arrival time, and trip completionFre</p>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="default-animation">-->
<!--        <div class="shape-img1"><img src="{{ asset('goride/img/custom-plan/12.svg') }}" alt="image"></div>-->
<!--        <div class="shape-img2"><img src="{{ asset('goride/img/custom-plan/13.svg') }}" alt="image"></div>-->
<!--        <div class="shape-img3"><img src="{{ asset('goride/img/custom-plan/14.png') }}" alt="image"></div>-->
<!--        <div class="shape-img4"><img src="{{ asset('goride/img/custom-plan/15.png') }}" alt="image"></div>-->
<!--        <div class="shape-img5"><img src="{{ asset('goride/img/custom-plan/2.png') }}" alt="image"></div>-->
<!--    </div>-->
<!--</section>-->
<!-- End Services Section -->
<!-- Start Tab Section -->
<!--<section class="tab-section ptb-100">-->
<!--    <div class="container">-->
<!--        <div class="section-title">-->
<!--            <span>Boosting</span>-->
<!--            <h3>Outstanding Digital Experience</h3>-->
<!--        </div>-->
<!--        <div class="tab boosting-list-tab">-->
<!--            <ul class="tabs">-->
<!--                <li class="current">-->
<!--                    <a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/assignment.png') }}" alt="icon" style="width: 50%;">-->
<!--                        <span>Assignment</span>-->
<!--                    </a>-->
<!--                </li>-->
<!--                <li class="bg-ecfaf7 std1"><a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/tracking.png') }}" alt="icon" style="width: 50%;">-->
<!--                        <span> GPS Tracking</span>-->
<!--                    </a></li>-->
<!--                <li class="bg-ecfaf7 std2"><a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/route.png') }}" alt="icon" style="width: 50%;">-->
<!--                        <span>Route Optimization</span>-->
<!--                    </a></li>-->
<!--                <li class="bg-ecfaf7 std3"><a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/chat.png') }}" alt="icon" style="width: 50%;">-->
<!--                        <span> Communication</span>-->
<!--                    </a></li>-->
<!--                <li class="bg-ecfaf7 std4"><a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/analytics.png') }}" alt="icon" style="width: 50%;">-->
<!--                        <span>Analytics-->
<!--                        </span>-->
<!--                    </a></li>-->
<!--                <li class="bg-ecfaf7 st5"><a href="#">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/real-time-strategy.png') }}" alt="icon"-->
<!--                            style="width: 50%;">-->
<!--                        <span>Real-Time Updates</span>-->
<!--                    </a></li>-->
<!--            </ul>-->
<!--            <div class="tab_content">-->
<!--                <div class="tabs_item" style="display: block;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/1_1.jpg') }}" alt="icon" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Automated Job Assignment and Scheduling</h2>-->
<!--                                <p>Dispatch software uses AI algorithms to automatically assign jobs to drivers based on-->
<!--                                    factors like location, availability, vehicle capacity, and job priority. This-->
<!--                                    minimizes manual intervention and ensures that resources are used efficiently.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>The software automatically assigns jobs to the best-suited drivers based on-->
<!--                                            factors like proximity, availability, vehicle capacity, and driver skills.-->
<!--                                            This ensures that the right driver is assigned to the right task every time.-->
<!--                                        </p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Scheduling is adjusted in real-time based on job priority, driver-->
<!--                                            availability, and traffic conditions. Urgent jobs are prioritized, and any-->
<!--                                            last-minute changes are seamlessly incorporated into the schedule.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tabs_item" id="standard" style="display: none;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/2_1.jpg') }}" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Real-Time GPS Tracking</h2>-->
<!--                                <p>Provides live tracking of drivers and vehicles, allowing dispatchers to monitor-->
<!--                                    routes, track job progress, and adjust operations dynamically based on real-time-->
<!--                                    data. This enhances operational control and customer communication.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Increased Safety: Monitor the location of vehicles and drivers to enhance-->
<!--                                            safety and provide timely assistance in emergencies.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Improved Efficiency: Businesses can streamline their operations, optimize-->
<!--                                            routes, and reduce fuel consumption by tracking vehicles and assets.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tabs_item" style="display: none;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/3_1.jpg') }}" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Route Optimization</h2>-->
<!--                                <p>The software optimizes routes based on real-time traffic data, distance, and time-->
<!--                                    constraints, reducing fuel costs, improving delivery times, and maximizing driver-->
<!--                                    efficiency. Dynamic re-routing is also supported in case of unexpected delays.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Increased Productivity: Optimizing routes means drivers spend less time on-->
<!--                                            the road and can complete more deliveries or service calls in a day.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Improved Customer Satisfaction: Faster, on-time deliveries result in happier-->
<!--                                            customers, leading to higher retention rates and positive reviews.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tabs_item" style="display: none;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/4_2.jpg') }}" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Seamless Communication</h2>-->
<!--                                <p>In-app messaging and push notifications enable direct communication between-->
<!--                                    dispatchers, drivers, and customers. Dispatchers can send job details, updates, and-->
<!--                                    alerts, while drivers can report issues or update job statuses on the go.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Improved Productivity: Employees can focus on their tasks without needing to-->
<!--                                            juggle multiple communication tools, leading to faster decision-making and-->
<!--                                            collaboration.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Enhanced Customer Experience: Clients receive consistent and timely-->
<!--                                            responses, no matter the channel they use to reach out, resulting in better-->
<!--                                            customer service and satisfaction</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tabs_item" style="display: none;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/5_1.jpg') }}" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Analytics and Reporting</h2>-->
<!--                                <p>Provides detailed analytics on job performance, driver efficiency, vehicle usage, and-->
<!--                                    customer satisfaction. These reports help businesses identify trends, make-->
<!--                                    data-driven decisions, and improve future operations.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Data Collection: Gathering relevant data from various sources, such as-->
<!--                                            databases, surveys, and sensors.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Data Processing: Cleaning, organizing, and preparing data for analysis to-->
<!--                                            ensure accuracy and relevance.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="tabs_item" style="display: none;">-->
<!--                    <div class="row align-items-center">-->
<!--                        <div class="col-lg-5">-->
<!--                            <div class="tab-image">-->
<!--                                <img src="{{ asset('goride/img/custom-plan/new.png') }}" alt="image">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-7">-->
<!--                            <div class="content">-->
<!--                                <h2>Customer Integration and Real-Time Updates</h2>-->
<!--                                <p>The software integrates with customer-facing platforms to allow bookings, track-->
<!--                                    deliveries, and send real-time notifications. This improves customer experience by-->
<!--                                    providing accurate ETAs, delivery confirmations, and transparency throughout the-->
<!--                                    service.</p>-->
<!--                            </div>-->
<!--                            <div class="row">-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Immediate Awareness: Users receive the latest information instantly, keeping-->
<!--                                            them informed of the most current developments and changes.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="col-lg-6 col-md-6 col-sm-6">-->
<!--                                    <div class="tab-text-content">-->
<!--                                        <i class="flaticon-analysis-2"></i>-->
<!--                                        <p>Quick Decision-Making: With up-to-date information, individuals and-->
<!--                                            organizations can make informed decisions and take timely actions based on-->
<!--                                            the latest data.</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="tab-btn">-->
                                <!-- <a href="#" class="default-btn-one">Discover More</a> -->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="tab-shape">-->
<!--                        <img src="{{ asset('goride/img/custom-plan/shape.png') }}" alt="image">-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

@if (count($seoTags['faqData']) > 0)

    <section class="tab-section pt-5 pb-5">
        <div class="container" id="faq">
            <div class="section-title">

                <h3>FAQ</h3>
            </div>
            <div class="tab boosting-list-tab">
                <div class="row justify-content-center">
                   <div class="col-12 col-lg-10">
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

@else
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; text-align: center; padding: 40px; font-family: 'Arial', sans-serif; background-color: #f9f9f9;margin-top:8%;">
        <img src="{{ asset('goride/img/logo-light.png') }}" alt="Page Not Found" style="max-width: 350px; width: 100%; margin-bottom: 30px; animation: float 3s ease-in-out infinite;">
        
        <h1 style="font-size: 48px; font-weight: 700; color: #333; margin-bottom: 15px;">404</h1>
        <h2 style="font-size: 28px; font-weight: 500; color: #555; margin-bottom: 20px;">Oops! Page Not Found</h2>
        <p style="font-size: 18px; color: #777; max-width: 500px; margin-bottom: 30px;">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
    
        <a href="{{ url('/') }}" style="display: inline-block; padding: 12px 30px; background: linear-gradient(90deg, #007bff, #00c6ff); color: #fff; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
            Go Back Home
        </a>
    </div>
    
    <style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    body {
        background: #000000;
    }
    </style>
@endif





@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script>
    $(document).ready(function () {
        @if (count($seoTags['faqData']) > 0)
            $('ul.accordion-box.clearfix li').first().find('.acc-btn').click();
        @endif
        
        $('.theme-btn').on('click', function() {
            window.open('/jobs', '_blank'); // opens in new tab
        });
    });
</script>
@endsection