<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GoRide</title>


    <link rel="shortcut icon" href="{{ asset('goride/img/Go-Ride-fav-icon.webp') }}" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('goride/css/plugins.css') }}" />
    <link rel="stylesheet" href="{{ asset('goride/css/style.css') }}" />

    {{-- <link rel="stylesheet" href="{{ asset('goride/vendor/intl-tel-input/intlTelInput.css') }}" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.5.3/css/intlTelInput.css"
        integrity="sha512-Hjsts+5q0OWXb6jem9SwlTyJKJpnAXrTtoKIzKeekwUFG6QesBqmr/5+NBXiimtCEUphi7Os72nrefhPbCYqtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @yield('css')


    <!-- jQuery -->
    <script src="{{ asset('goride/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Common Config File  -->
    <script src="{{ asset('goride/js/common.js') }}"></script>

    <!-- Protect our site  -->
    {{-- <script src="{{ asset('goride/js/protectSite.js') }}"></script> --}}

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
        @include('include.footer')


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
    </div>
</body>

</html>
