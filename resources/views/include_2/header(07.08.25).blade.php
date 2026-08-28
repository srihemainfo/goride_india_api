<style>

  .btn-signin {
font-size: 13px;
    font-weight: bold;
    padding: 4px 6px;
    border: none;
    transition: all 0.4s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #FFC107, #FF9800);
    color: #222;
    border-radius: 50px;
    width: 92px;
      
  }
    

  .btn-signin:hover {
     font-weight: bold;
   padding: 4px 6px;
    border: none;
    transition: all 0.4s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #FFB300, #FF5722);
    color: #fff;
    transform: translateY(-2px);
  }
  .gradient-globe {
  background: linear-gradient(to right, #ffc107, #ff4081); /* Yellow to pink */
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 18px;
}
.skiptranslate{
    display:none;
}

body {
    position:static !important;
}

</style>


{{-- <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>GoRide</title>
    <link rel="shortcut icon" href="{{ asset('goride/img/Go-Ride-fav-icon.webp') }}" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('goride/css/plugins.css') }}" />
    <link rel="stylesheet" href="{{ asset('goride/css/style.css') }}" />
</head> --}}

<!-- Preloader -->
<div id="preloader-wrap">
    <div class="car">
        <div class="strike"></div>
        <div class="strike strike2"></div>
        <div class="strike strike3"></div>
        <div class="strike strike4"></div>
        <div class="strike strike5"></div>
        <div class="car-detail spoiler"></div>
        <div class="car-detail back"></div>
        <div class="car-detail center"></div>
        <div class="car-detail center1"></div>
        <div class="car-detail front"></div>
        <div class="car-detail wheel"></div>
        <div class="car-detail wheel wheel2"></div>
    </div>
</div>

<!-- Custom Cursor -->
<div class="custom-cursor"></div>

<!-- Progress scroll totop -->
<div class="progress-wrap cursor-pointer">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
</div>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg" id="home_header_top">
   <div class="container mt-3">
   
        <!-- Logo -->

        <div class="logo-wrapper">
            <a class="logo" href="/"> <img src="https://www.goride.net.in/goride/img/logo-light.png" class="logo-img" alt=""> </a>
        </div>
        <!-- Button -->
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"><i class="fa-solid fa-bars"></i></span> </button>
        <div class="row">
 <div class="col-12 d-flex justify-content-end mb-3 align-items-center gap-2">
     
     @if ($userDetails != null && isset($userDetails['userID']) && $userDetails['userID'] != null)
            <a href="/dashboard" class="btn btn-signin">
            Dashboard
          </a>
          <a onclick="logoutFUN()" class="btn btn-signin">
            Logout
          </a>
     
     @else
     
          <a href="/login" class="btn btn-signin">
            Log in
          </a>
          <a href="/signup" class="btn btn-signin">
            Sign Up
          </a>
     
     @endif
     

  <!-- Language Dropdown with Yellow Globe -->
 <!-- Language Dropdown with Gradient Globe -->
<div class="dropdown">
  <button class="btn dropdown-toggle d-flex align-items-center text-warning" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="fas fa-globe me-2 gradient-globe"></i> <!-- Gradient Globe -->
    English
  </button>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
    <li><a class="dropdown-item" href="#" onclick="translatePage('en')">English</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('hi')">Hindi</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('ta')">Tamil</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('te')">Telugu</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('kn')">Kannada</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('ml')">Malayalam</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('bn')">Bengali</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('gu')">Gujarati</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('mr')">Marathi</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('ur')">Urdu</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('pa')">Punjabi</a></li>
    <li><a class="dropdown-item" href="#" onclick="translatePage('or')">Odia</a></li>
  </ul>
</div>
<div id="google_translate_element" style="display: none;"></div>

</div>

  <div class="col-12">
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav ms-auto">
                <!--<li class="nav-item dropdown"> <a class="nav-link active dropdown-toggle" href="#" role="button"-->
                <!-- data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Home </a> -->
                <!--</li>-->
                <!--<li class="nav-item"><a class="nav-link" href="#features">Features</a></li>-->
                <!--<li class="nav-item"><a class="nav-link" href="#about">About</a></li>-->
                <li class="nav-item"><a class="nav-link" href="/features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="/crm-with-dispatch">CRM with Dispatch System</a></li>
                <li class="nav-item"><a class="nav-link" href="/driver-app">Driver App</a></li>
                <li class="nav-item"><a class="nav-link" href="/passenger-app">Passenger App</a></li>
                <li class="nav-item"><a class="nav-link" href="/pricing">Pricing</a></li>

                                    <!--<li class="nav-item"><a class="nav-link" href="signup">Sign in/ Sign up</a></li>-->
                    <!--<li class="nav-item dropdown"> -->
                    <!--    <a class="nav-link " href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Account <i class="ti-angle-down"></i></a>-->
                    <!--    <ul class="dropdown-menu">-->
                    <!--        <li><a href="/login" class="dropdown-item"><span>Sign Inc</span></a></li>-->
                    <!--        <li><a href="/signup" class="dropdown-item"><span>Sign Up</span></a></li>                    -->
                    <!--    </ul>-->
                    <!--</li>-->
                




                <!--<li class="nav-item"><a class="nav-link" href="#footer">Contact</a></li>-->
            </ul>
            <div class="navbar-right">
                <div class="wrap">
                    <!--<a href="tel:9884557004">-->
                    <!--    <div class="icon"> <i class="flaticon-phone-call"></i> </div>-->
                    <!--</a>-->
                    <!--<div class="text">-->
                    <!--    <p>For Pre Booking</p>-->
                    <!--    <h5><a href="tel:9884557004">+91 98845 57004</a></h5>-->
                    <!--</div>-->
                    <div class="phone-icon-wrapper">
                        <a href="js" class="phone-icon">
                            <div class="icon"> <i class="flaticon-phone-call"></i> </div>
                        </a>
                        <div class="menu-icons">
                            <a href="tel:+16473661867" title="For Canada Only">
                                <div class="icon"> <i class="fa-brands fa-canadian-maple-leaf"></i> </div>
                            </a>
                            <a href="tel:+917299888886" title="International">
                                <div class="icon"> <i class="fa-solid fa-earth-asia"></i> </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
     </div>
    </div>
</nav>

<!-- Google Translate script loader -->
<script type="text/javascript">
  function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
  }

  function translatePage(lang) {
    const interval = setInterval(() => {
      const select = document.querySelector(".goog-te-combo");
      if (select) {
        select.value = lang;
        select.dispatchEvent(new Event("change"));
        clearInterval(interval);
      }
    }, 100);
  }
</script>

<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script>

    // Preloader

    window.onload = function() {
        document.body.classList.add("loaded");
    }

    $(document).ready(function() {
        $('.phone-icon').on('click', function(event) {
            event.preventDefault();
            $(this).closest('.phone-icon-wrapper').toggleClass('active');
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('.phone-icon-wrapper').length) {
                $('.phone-icon-wrapper').removeClass('active');
            }
        });
    });
    
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#navbar, .navbar-toggler').length) {
            $('#navbar').collapse('hide');
        }
    });

    const logoutFUN = () => {

        try {

            $.ajax({

                url: origin + '/api/logout',

                type: 'POST',

                headers: {

                    "Accept": "application/json; charset=utf-8",

                    "Content-Type": "application/json; charset=utf-8",

                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {

                    if (response.status === 'success') {

                        deleteCookie('sessionToken');
                        deleteCookie('cusid');
                        deleteCookie('name');
                        deleteCookie('transaction_id');
                        deleteCookie('finaltotal');
                        deleteCookie('newFormCartData');
                        deleteCookie('cartdata');
                        deleteCookie('cartondata');
                        deleteCookie('merchantOrderReference');
                        deleteCookie('makepayemntre');
                        deleteCookie('MakepaymentState');
                        //  deleteCookie('allow_id');
                        deleteCookie('newFormCartData');
                        deleteCookie('billingDetails');
                        deleteCookie('shipping');
                        //  deleteCookie('couponCode');
                        deleteCookie('payment_method');
                        deleteCookie('userDetails');
                        //  deleteCookie('payment_id');

                        localStorage.clear();

                        //  if (isWebView()) {
                        //    Android.logout();
                        //  }

                        window.location.href = origin;

                    } else {
                        window.location.href = origin;
                        console.log('Error: ' + response);

                    }

                },

                error: function(xhr, status, error) {

                    window.location.href = origin;

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },

                processData: false,

                contentType: false

            });

        } catch (e) {

            console.log('Error: ' + e.message);

        }

    }
</script>
