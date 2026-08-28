
<header class="header-classic">
    <div class="container">
        <!---fluid-->
        <!-- header top -->
        <div class="header-top">
        <nav >
            <div class="row align-items-center">
                <div class="col-6 col-md-4 text-start">
                    <!-- Site logo -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <!-- <img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '' ? $seoData['partnerWeb']->logo : 'tone/images/demoimage.png') }}"
                    alt="logo" /> -->
                        @if(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '')
                        <img src="{{ asset($seoData['partnerWeb']->logo) }}" alt="logo" />
                        @endif
                    </a>
                </div>
          
                <div class="col-6 col-md-8 text-center">
                    <!-- Social icons and navigation -->

                    <div class="flex-cont">
                        <div class="d-flex align-items-center w-100  justify-content-center">
                            <div class="contact-item contact-item1 p-0 rounded d-flex align-items-center">
                                <div class="details">
                                    <h3 class="text-light">
                                        {{ isset($seoData['partnerWeb']->company_name) && $seoData['partnerWeb']->company_name != '' ? $seoData['partnerWeb']->company_name : 'Go Ride Run' }}
                                    </h3>
                                    <!-- <div class="num-flex">
                                        <p class="mb-0">
                                            <i class="fab fa-whatsapp-square"></i>
                                            <a href="https://api.whatsapp.com/send?phone={{ $seoData['partnerWeb']->contact_number ?? '000000000' }}&amp;text=Hello I need car service Please contact me"
                                                target="_blank">
                                                {{ isset($seoData['partnerWeb']->contact_number) && $seoData['partnerWeb']->contact_number != '' ? $seoData['partnerWeb']->contact_number : '0000000000' }}
                                            </a>
                                        </p>
                                    </div> -->

                                    @if(isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)
                                    @php
                                    $topItems = collect($seoData['getAllPages'])->filter(function ($page) {
                                    return $page->position == 'top';
                                    });
                                    $getFirstFive = collect($topItems)->take(5);
                                    @endphp

                               <ul class="navbar-nav d-none d-lg-flex">
    @foreach ($getFirstFive as $page)
    <li class="nav-item">
        <a class="nav-link" style="font-size: 16px !important;" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
    </li>
    @endforeach
</ul>
                                    <!-- Mobile Offcanvas -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav">
            @foreach ($getFirstFive as $page)
            <li class="nav-item">
                <a class="nav-link text-black py-2 ps-3 text-start" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
            </li>
            @endforeach
        </ul>
    </div>
</div>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mobile-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
    ☰
</button>
             
            </div>
             </nav>
        </div>
    </div>
<a href="https://api.whatsapp.com/send?phone={{ $seoData['partnerWeb']->contact_number ?? '0000000000' }}&amp;text=Hello I need car service Please contact me"
   class="whatsapp-button"
   target="_blank">
   <img src="https://img.icons8.com/color/48/000000/whatsapp--v1.png" alt="WhatsApp">
</a>

    </div>
</header>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS (for toggle functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
  $('.mobile-btn').css({
    'background': 'none',
    'color': 'white',
    'outline': 'none',
    'box-shadow': 'none',
    'border': 'none'
  });
});

</script>
<style>
    .navbar-expand-lg {
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



    .contact-item .details h3 {
        font-size: 25px !important;
    }

    .contact-item .details {
        margin-left: 63px;
    }

    .whatsapp-button {
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 999;
  display: inline-block;
  cursor: pointer;
}

.whatsapp-button img {
  width: 60px;
  height: 60px;
 
  transition: transform 0.2s ease;
}

.whatsapp-button img:hover {
  transform: scale(1.1);
}


    .navbar-brand img {
        width: 50%;


    }
    .mobile-btn{
          width: 100% !important;
    background: none;
    color: white;
    border: none;
    font-size: 30px;
    text-align: end;
    }
    .mobile-btn:hover,
.mobile-btn:focus,
.mobile-btn:active,
.mobile-btn:focus-visible {
    background: none !important;
    color: white !important;
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
}

  

    @media only screen and (max-width: 767px) {
        .num-flex {
            justify-content: start;
        }
    }

    .header-classic .header-top {
        padding-bottom: 10px;
    }

    @media (max-width: 992px) {
        .navbar-nav .nav-item a {
            margin-top: 0 !important;
        }

        .num-flex {
            margin-bottom: 10px;
        }

        .num-flex a {
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

        .header-classic {
            box-shadow: none !important;
        }

        .over-car {
            display: contents !important;
        }

        .navbar-brand {
            position: relative;
            float: left;
            left: 0%;
            margin-top: 24px;
            margin: 0px 0 0 0;

        }

        .navbar-brand img {
            width: 100%;
            /* margin-top: 3px; */
        }
    }

    @media only screen and (max-width: 767px) {
        .rounded {
            border-radius: 10px !important;
            position: relative;
        }

        .details h3 {
            font-size: 15px !important;
        }
    }

    .num-flex p {
        display: flex;
        margin-left: 0 !important;
        align-items: center;
    }

    .navbar-nav .nav-item a {
        font-size: 16px;
        margin-top: 7px;
    }

    .navbar-toggler .bg-light {
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