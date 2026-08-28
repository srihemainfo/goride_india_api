<header class="header-classic">
    <div class="container">
        <!---fluid-->
        <!-- header top -->
        <div class="header-top">
            <div class="row align-items-center over-car">
                <div class="col-md-5  text-start">
                    <!-- site logo -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '' ? $seoData['partnerWeb']->logo : 'tone/images/demoimage.png') }}"
                            alt="logo" />
                    </a>
                </div>
                <div class="col-md-7 d-md-block">
                    <!-- social icons -->
                    <nav class="navbar navbar-expand-lg">
                        <div class="flex-cont">
                            <div class="d-flex align-items-center">
                                <!-- header buttons -->
                                <div class="header-buttons">
                                    <!-- <button class="search icon-button">
                                        <i class="icon-magnifier"></i>
                                    </button> -->
                                    <button class="burger-menu icon-button ms-2 float-end float-lg-none">
                                        <span class="burger-icon"></span>
                                    </button>
                                </div>
                                <div class="contact-item contact-item1 p-0 rounded d-flex align-items-center">
                                    <div class="details">
                                        <h3 class="text-light">
                                            {{ isset($seoData['partnerWeb']->company_name) && $seoData['partnerWeb']->company_name != '' ? $seoData['partnerWeb']->company_name : 'Go Ride Run' }}
                                        </h3>
                                        <div class="num-flex">
                                            <p class="mb-0">
                                                <i class='fab fa-whatsapp-square'></i>
                                                <a href="https://api.whatsapp.com/send?phone={{ $metaData['whatsapp_number'] ?? '000000000' }}&amp;text=Hello I need car service Please contact me"
                                                    target="_blank">
                                                    {{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? $seoData['partnerWeb']->whatsapp_number : '0000000000' }}
                                                </a>
                                            </p>
                                        </div>
                                     
                                        @if(isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)
                                                                                @php

                                                                                    $topItems = collect($seoData['getAllPages'])->filter(function ($page) {
                                                                                        return $page->position == 'top';
                                                                                    });

                                                                                    $getFirstFive = collect($topItems)->take(5);
                                                                                @endphp
                                                                                <ul class="navbar-nav">
                                                                                    @foreach ($getFirstFive as $page)
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link"
                                                                                                href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
                                                                                        </li>
                                                                                    @endforeach
                                                                                </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
    <style>
        @media (max-width: 1370px) {
            .navbar-brand {
        position: relative;
        float: left;
        margin: 0;
    }
}
.contact-item .details h3{
    font-size: 21px !important;
}
.contact-item .details{
    margin-left: 63px;
}
.navbar-brand img{
    width: 30%;


}
.header-classic .header-top{
        padding-bottom: 10px;
}
@media (max-width: 992px) {
    .over-car{
        display: contents !important;}
    .navbar-brand {
        position: relative;
        float: left;
        left: -21%;
        margin-top: 24px;
        margin: 30px 0 0 0;
    
    }
    .navbar-brand img {
    width: 25%;
}
}
@media only screen and (max-width: 767px) {
    .rounded {
    border-radius: 10px !important;
    position: relative;
}
    .details h3 {
        font-size: 15px !important;
    }}
    .num-flex p{
        display: flex;
        align-items: center;
    }
    .navbar-nav .nav-item a{
        font-size: 18px;
    }
 
    </style>

