
<style>
 @media (max-width:768px) {
    .company_info{
    margin-top: 10px;
    font-size:12px;
  }  
  .company_info h3{
    font-size: 19px !important;
  }
  .navbar-brand img {
    margin-top: 10px;
            width: 100% !important;
        }
}
.header__main-wrap{
    padding:  0 !important;
}
.navbar-brand img {
    width: 50%;
}

 
</style>
<header class="header">

<div class="container-fluid py-md-3 py-sm-0" style="background-color: #E3EDF6;">
    <div class="row d-flex align-items-center">
        <!-- Logo on the Left -->
        <div class="col-6 col-md-4 text-start ">
            <a href="home" class="navbar-brand">
                <img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '' 
                    ? $seoData['partnerWeb']->logo 
                    : 'tone/images/demoimage.png') }}"
                    alt="logo" class="img-fluid ms-2">
            </a>
        </div>

        <!-- Centered Content -->
        <div class="col-6 col-md-8 company_info text-center">
            <h3 class="mb-1">
                {{ isset($seoData['partnerWeb']->company_name) && $seoData['partnerWeb']->company_name != '' 
                    ? $seoData['partnerWeb']->company_name 
                    : 'Go Ride Run' }}
            </h3>
            <div>
                <i class="far fa-phone"><a class="text-dark text-decoration-none" href="tel:{{ isset($seoData['partnerWeb']->contact_number) && $seoData['partnerWeb']->contact_number != '' 
                    ? $seoData['partnerWeb']->contact_number 
                    : '0000000000' }}">
                    {{ isset($seoData['partnerWeb']->contact_number) && $seoData['partnerWeb']->contact_number != '' 
                    ? $seoData['partnerWeb']->contact_number 
                    : '0000000000' }}
                </a></i>
                <br>
                <i class="far fa-envelope"> <a class="text-dark text-decoration-none" href="mailto:{{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' 
                    ? $seoData['partnerWeb']->email 
                    : 'support@goride.run' }}">
                    {{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' 
                    ? $seoData['partnerWeb']->email 
                    : 'support@goride.run' }}
                </a></i>
               
            </div>
        </div>
    </div>
</div>



    <div class="header__main-wrap desk-hd p-0" data-uk-sticky="top: 250; animation: uk-animation-slide-top;">

        <div class="container-fluid">

            <nav class="navbar navbar-expand-lg navbar-light">

                <div class="container-fluid">

                    <div class="header__main ul_li_between">

                        <div class="header__main-left ul_li flex-1">

                            <div class="header__bar hamburger_menu">

                                <a href="javacript:void(0);"><i class="fa fa-bars"></i></a>

                            </div>

                        </div>

                        <div class="main-menu navbar navbar-expand-lg">

                            <nav class="main-menu__nav collapse navbar-collapse">








                                @if (isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)
                                @php

                                $topItems = collect($seoData['getAllPages'])->filter(function ($page) {
                                return $page->position == 'top';
                                });

                                $getFirstFive = collect($topItems)->take(5);
                                @endphp
                                <ul class="navbar-nav">
                                    @foreach ($getFirstFive as $page)
                                    <li class="">
                                        <a class="" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
                                    </li>
                                    @endforeach
                                </ul>
                                @endif

                                <!--<ul>-->

                                <!--	<li><a href="Demo Themeusine-service-toronto">Demo Themeusine-service</a></li>-->

                                <!--	<li><a href="airport-Demo Theme-services">Airport Transfers</a></li>-->

                                <!--	<li><a href="fleet">Our Fleet</a></li>-->

                                <!--	<li><a href="contact">Contact us</a></li>-->

                                <!--</ul>-->

                            </nav>

                        </div>

                        <div class="header__main-right ul_li">

                        </div>

                    </div>

                </div>

            </nav>

        </div>

    </div>

</header>