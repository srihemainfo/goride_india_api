
<header class="header-classic">
    <div class="container">
        <!---fluid-->
        <!-- header top -->
        <div class="header-top">
    <div class="row align-items-center over-car">
        <div class="col-md-5 col-sm-3 text-start">
            <!-- Site logo -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '' ? $seoData['partnerWeb']->logo : 'tone/images/demoimage.png') }}"
                    alt="logo" />
            </a>
        </div>
        <div class="col-md-7  col-sm-9">
            <!-- Social icons and navigation -->
            
                    <div class="flex-cont">
                        <div class="d-flex align-items-center w-100">
                            <div class="contact-item contact-item1 p-0 rounded d-flex align-items-center">
                                <div class="details">
                                    <h3 class="text-light">
                                        {{ isset($seoData['partnerWeb']->company_name) && $seoData['partnerWeb']->company_name != '' ? $seoData['partnerWeb']->company_name : 'Go Ride Run' }}
                                    </h3>
                                    <div class="num-flex">
                                        <p class="mb-0">
                                            <i class="fab fa-whatsapp-square"></i>
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
                </div>
            </nav>
        </div>
    </div>
</div>

    </div>
</header>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (for toggle functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .navbar-expand-lg{
            right: 19px;
            position: relative;
        }
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
@media only screen and (max-width: 767px) {
    .num-flex {
        justify-content: start;
    }
}
.header-classic .header-top{
        padding-bottom: 10px;
}
@media (max-width: 992px) {
    .navbar-nav .nav-item a{
        margin-top: 0 !important;
    }
    .num-flex{
    margin-bottom: 10px;}
    .num-flex a{
        font-size: 12px;
    }
    .contact-item .details h3 {
    font-size: 15px !important;
}
    .navbar-nav .nav-item a {
    font-size: 12px !important;
}
    .navbar-expand-lg {
    right: 13px;
    position: relative;
}
    .contact-item .details {
    margin-left: 0px;
    top: 8px;
    position: relative;
}
    .header-classic{
        box-shadow: none!important;
    }
    .over-car{
        display: contents !important;}
    .navbar-brand {
        position: relative;
        float: left;
        left: 0%;
        margin-top: 24px;
        margin: 0px 0 0 0;
    
    }
    .navbar-brand img {
    width: 19%;
    margin-top: 3px;
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
        margin-left: 0 !important;
        align-items: center;
    }
    .navbar-nav .nav-item a{
        font-size: 16px;
        margin-top: 7px;
    }
    .navbar-toggler .bg-light{
        right: 43px;
        position: relative;
    }
    @media (min-width: 320px) and (max-width: 411px) {
    .contact-item .details {
        margin-left: -11px;
        top: 8px;
        position: relative;
    }
    .navbar-nav .nav-item a {
        font-size: 10px !important;
    }

}
@media (min-width: 320px) and (max-width: 359px) {
.contact-item .details {
        margin-left: -32px;
        top: 8px;
        position: relative;
    }
}
    </style>

