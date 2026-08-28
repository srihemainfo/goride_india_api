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
            <a class="logo" href="/"> <img src="{{ asset('goride/img/logo-light.png') }}" class="logo-img" alt=""> </a>
        </div>
        <!-- Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
            aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation"> <span
                class="navbar-toggler-icon"><i class="fa-solid fa-bars"></i></span> </button>
        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav ms-auto">
                <!--<li class="nav-item dropdown"> <a class="nav-link active dropdown-toggle" href="#" role="button"-->
                <!--        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Home </a>                        -->
                <!--</li>-->
                <!--<li class="nav-item"><a class="nav-link" href="#features">Features</a></li>-->
                <!--<li class="nav-item dropdown"> <a class="nav-link " href="#" role="button"-->
                <!--    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">Service Offerings <i class="ti-angle-down"></i></a>-->
                <!--    <ul class="dropdown-menu">-->
                <!--        <li><a href="#" class="dropdown-item"><span>CRM with Dispatch System</span></a></li>-->
                <!--        <li><a href="#" class="dropdown-item"><span>Driver App</span></a></li>-->
                <!--        <li><a href="#" class="dropdown-item"><span>Passenger App</span></a></li>                           -->
                <!--    </ul>-->
                <!--</li>-->
                <!--<li class="nav-item"><a class="nav-link" href="#about">About</a></li>-->
                <li class="nav-item"><a class="nav-link" href="crm-with-dispatch">CRM with Dispatch System</a></li>
                <li class="nav-item"><a class="nav-link" href="driver-app">Driver App</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Passenger App</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Sign in/ Sign up</a></li>
                <!--<li class="nav-item"><a class="nav-link" href="#footer">Contact</a></li>-->
            </ul>
            <div class="navbar-right">
                <div class="wrap">
                    <a href="tel:9884557004">
                        <div class="icon"> <i class="flaticon-phone-call"></i> </div>
                    </a>
                    <!--<div class="text">-->
                    <!--    <p>For Pre Booking</p>-->
                    <!--    <h5><a href="tel:9884557004">+91 98845 57004</a></h5>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Preloader

    window.onload = function() {
        document.body.classList.add("loaded");
    }
</script>
