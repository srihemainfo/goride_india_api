<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GoRide</title>

    {{-- <title>GoRide</title> --}}
    <link rel="shortcut icon" href="{{ asset('goride/img/Go-Ride-fav-icon.webp') }}" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&amp;display=swap">
    <link rel="stylesheet" href="{{ asset('goride/css/plugins.css') }}" />
    <link rel="stylesheet" href="{{ asset('goride/css/style.css') }}" />\


    @yield('css')

</head>

<body>
    <div id="app">
        <!-- Navigation -->
        @include('include.header')

        <!-- Main Content -->
        <main class="py-4">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('include.footer')


        @yield('script')
    </div>
</body>

</html>
