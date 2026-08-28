@php

$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/index.php') === 0) {
    // Remove only the first occurrence of "/index.php"
    $cleanUri = preg_replace('#^/index\.php#', '', $requestUri);
    $cleanUri = $cleanUri == ""? '/': $cleanUri;
    header("Location: $cleanUri", true, 301);
    exit;
}

    $userToken = $_COOKIE['sessionToken'] ?? '';
    $userDetails = null;
    if (isset($userToken) && $userToken != null) {
        $apiEndpoint = url('/api/dashboard');
        $response = Http::withToken($userToken)->post($apiEndpoint);
        if ($response->successful()) {
            // Decode JSON response
            $authUser = $response->json();
            // dd($authUser['data'], 's');
            if (isset($authUser['status']) && $authUser['status'] === 'success') {
                $userDetails = $authUser['data']['userDetails'] ?? null;
            }
        }
    }
    // dd($userDetails);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    @if (str_contains(request()->url(), 'car-rental'))
        @php
            $from = ucfirst($explode[0] ?? '');
            $to = ucfirst($explode[2] ?? '');
            $price = number_format($seoTags['mini_four_seater'] ?? 0);
        
            $titles = [
                "Book Cabs from $from to $to at ₹$price | Tolls, Driver Bata & Taxes Included – GoRide",
                "Book $from to $to Cab at ₹$price | All Charges Included – GoRide",
                "$from to $to Taxi ₹$price | Tolls & Bata Included – GoRide",
                "Low-Cost $from to $to Cab ₹$price | No Hidden Fees – GoRide",
                "Affordable $from to $to Car Rental starting at ₹$price | GoRide",
                "Book $from to $to Outstation Taxi from ₹$price | GoRide"
            ];
            
            $randomTitle = $titles[array_rand($titles)];
        @endphp
        
        <title>{{ $randomTitle }}</title>
    
        <meta name="description"
              content="Book online {{ ucfirst($explode[0]) }} to {{ ucfirst($explode[2]) }} cabs starting at ₹{{ number_format($seoTags['mini_four_seater'] ?? 0) }} with GoRide. Choose Sedan, SUV, or Tempo Traveller—AC or Non-AC, one-way or round-trip. Transparent pricing, tolls,bata,parking and taxes included, driven by experienced professionals">
    
        <meta name="keywords"
              content="{{ ucfirst($explode[0]) }} to {{ ucfirst($explode[2]) }}, {{ ucfirst($explode[0]) }} to {{ ucfirst($explode[2]) }} Taxi, {{ ucfirst($explode[0]) }} to {{ ucfirst($explode[2]) }} Cab, Low Cost Taxi {{ ucfirst($explode[0]) }}, Cheap Fare Cabs {{ ucfirst($explode[0]) }}, Economy Cab Charges, Affordable Cab Service, GoRide Cab Service, Outstation Taxi {{ ucfirst($explode[0]) }}, One Way Cab {{ ucfirst($explode[0]) }}, Round Trip Taxi {{ ucfirst($explode[2]) }}, Inter-District Travel India, GoRide Fleet Management">
    @else
        <title>
            {{ $seoTags['metaTitle'] ?? "Experience Premium Travel with GoRide | The World's Leading Limo & Cab Dispatch Software Solution" }}
        </title>
    
        <meta name="description"
              content="{{ $seoTags['metaDes'] ?? 'Discover GoRide, the world’s top-rated limo and cab dispatch software. Streamline operations, enhance customer experiences, and drive efficiency with our cutting-edge, world-class platform.' }}">
    
        <meta name="keywords"
              content="{{ $seoTags['metaKeyword'] ?? 'Cab booking software, Limo dispatch system, Ride-hailing platform, Taxi dispatch software, Fleet management software, Online booking system for taxis, Ride-sharing app, Taxi fleet software, Limo reservation system, Taxi booking system, Cab management software, Transportation dispatch software, Automated ride dispatch, On-demand taxi service, Best cab booking app, Ride-hailing dispatch system, Taxi fleet management, Limo fleet software, Real-time cab booking, Cab booking platform, Best limo software, Vehicle dispatch software, Taxi app software, On-demand transportation software, Ride-hailing solutions, Limo booking software, Fleet dispatch solutions' }}">
    @endif


    
   <script type="text/javascript">
    //     (function(c,l,a,r,i,t,y){
    //         c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
    //         t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    //         y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    //     })(window, document, "clarity", "script", "qyc35jnl40");
    </script>
    
    
    
    

    <link rel="canonical" href="{{ request()->url() }}">
    <link rel="shortcut icon" href="{{ asset('goride/img/Go-Ride-fav-icon.webp') }}" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('goride/css/plugins.css') }}" />
    <link rel="stylesheet" href="{{ asset('goride/css/style.css') }}" />
    {{--
    <link rel="stylesheet" href="{{ asset('goride/vendor/intl-tel-input/intlTelInput.css') }}" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.5.3/css/intlTelInput.css"
        integrity="sha512-Hjsts+5q0OWXb6jem9SwlTyJKJpnAXrTtoKIzKeekwUFG6QesBqmr/5+NBXiimtCEUphi7Os72nrefhPbCYqtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.min.css" rel="stylesheet">

    @yield('css')
    <!-- jQuery -->
    <script src="{{ asset('goride/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Common Config File  -->
    <script src="{{ asset('goride/js/common.js') }}"></script>
     
      <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        
    <!-- Protect our site  -->
    {{--
    <script src="{{ asset('goride/js/protectSite.js') }}"></script> --}}
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
    
        @media screen and (max-width: 767px) {
        
            .iti--container {
                z-index: 99999999 !important;
            }
            
            .form-control, .select2-container .select2-selection--single .select2-selection__rendered, .select2-results__option {
                color: #000 !important;
            }
            
            .select2-container .select2-selection--single .select2-selection__rendered {
                font-size: 1.2rem !important;
            }
            
            .select2-container {
                padding: 5px !important;
            }
            
        }
        
    </style>

    <script>
        $.ajax({
            url: "https://api.country.is/",
            type: "GET",
            dataType: "json",
            success: function (response) {
                // console.log(response.country);
                var countryCode = response.country;
                if (countryCode) {
                    setCookie("countryCode", countryCode, 3);
                    // if (!getCookie('allowAuth') && (!allowKey.includes(getCookie('allowAuth')) || !allowKey.includes(''))) {
                    //     // console.log(allowKey.includes(''));
                    //     if (allowKey.includes(subid20) || allowKey.includes('') || allowKey.includes(subid20)) {
                    //         window.location.href = origin;
                    //     } else {
                    //         checkAndRedirectRestrictedCountry(countryCode.toUpperCase(), restrictedCountryCodes, resRedirectUrl);
                    //     }
                    // }
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7PNZ2KFE7W"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-7PNZ2KFE7W');
    </script>
    
    
</head>

<body>
    <div id="app">
        <!-- Navigation -->
        @include('include.header')
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
        <!-- Footer -->
        
        @if(str_contains(Request::path(), 'car-rental') && $seoTags['page_exist'])
            @include('include.custom-footer')
        @else
            @include('include.footer')
        @endif

        




        <script src="{{ asset('goride/js/jquery-migrate-3.4.1.min.js') }}"></script>
        <script src="{{ asset('goride/js/jquery.isotope.v3.0.2.js') }}"></script>
        <script src="{{ asset('goride/js/popper.min.js') }}"></script>
        <script src="{{ asset('goride/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('goride/js/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('goride/js/smooth-scroll.js') }}"></script>
        <script src="{{ asset('goride/js/custom.js') }}"></script>
        <script src="{{ asset('goride/vendor/moment/moment.min.js') }}"></script>
        <script src="{{ asset('goride/vendor/moment/luxon.min.js') }}"></script>
        <script src="{{ asset('goride/vendor/intl-tel-input/intlTelInput-jquery.min.js') }}"></script>
        <script src="{{ asset('goride/vendor/intl-tel-input/intlTelInput.min.js') }}"></script>
        <script src="{{ asset('goride/vendor/alert/sweetalert2@11.js') }}"></script>
        
    
        
        @yield('script')
        <script type="module">
            import { initializeApp } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-app.js";
            import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging.js";
            
            const firebaseConfig = {
              apiKey: "AIzaSyBLWKuZGTE4C0LvFob800avF-jgIYxnsyw",
              authDomain: "goride-947ed.firebaseapp.com",
              projectId: "goride-947ed",
              storageBucket: "goride-947ed.firebasestorage.app",
              messagingSenderId: "1068992532063",
              appId: "1:1068992532063:web:a4bfecdf589c73b5ff55ea",
              measurementId: "G-HS1ZXQDYSS"
            };
            
            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);
            
            async function initFirebaseMessagingRegistration() {
              try {
                const permission = await Notification.requestPermission();
                if (permission === "granted") {
                
                  const registration = await navigator.serviceWorker.register("/firebase-messaging-sw.js");
            
                  const token = await getToken(messaging, {
                    vapidKey: "BHRPkvR_Iuvwb2Cm7VVu2QGUr2ziIkErXWv1qPFacPGuM7RvXZayh8DFVdpmTezzmNnMUCrHHXgLN8no27N_39k",
                    serviceWorkerRegistration: registration
                  });
                
                    $('#browser_fcm_token').val(token);
                  
                } else {
                }
              } catch (err) {
              }
            }
            
            initFirebaseMessagingRegistration();
            
            // Foreground message listener
           onMessage(messaging, (payload) => {
              console.log("Foreground message:", payload);
            
              const title = payload.data?.title || 'Notification';
              const options = {
                body: payload.data?.body || '',
                data: { url: payload.data?.url || '/jobs' }
              };
            
              // delegate to service worker for consistent click handling
              if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({
                  action: 'show-notification',
                  title,
                  options
                });
              }
            });

                  
            </script>

        <script>
        
            

            function getQueryParam(param) {
                
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
                
            }
    
            const utm_source = getQueryParam('utm_source');
            const utm_campaign = getQueryParam('utm_campaign');
            
            console.log(utm_source, utm_campaign);
            if(utm_source)setCookie('utm_source', utm_source, 1);
            if(utm_campaign)setCookie('utm_campaign', utm_campaign, 1);
            
        </script>
        
        <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.min.js"></script>
    <script>
      AOS.init();
    </script>

    </div>

    @include('include.seoScheme')

   
</body>

</html>