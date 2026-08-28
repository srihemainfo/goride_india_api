<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        <a href="/" contenteditable="false" style="cursor: pointer;">
                <img src="https://hulk.goride.run/logo.png">            </a>
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <!-- Session Status -->
        
        <!-- Validation Errors -->
        
        <form id="log_form">

            <div>
                <label class="block font-medium text-sm text-gray-700" for="username">
    Username
</label>

                <input class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full" id="email" type="email" name="username" autofocus="autofocus">
            </div>

            <div class="mt-4">
                <label class="block font-medium text-sm text-gray-700" for="password">
    Password
</label>

                <input class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full" id="password" type="password" name="password" autocomplete="current-password">
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="https://hulk.goride.run/forgot-password" contenteditable="false" style="cursor: pointer;">
                        Forgot your password?
                    </a>
                
                
            </div>
        </form>
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 ml-3" id="log_sub">
    Log in
</button>
    </div>
</div>
<x-guest-layout>
    <div class="modal-overlay"></div>

    <!-- Modal -->
    <div class="modal fade show" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="img-box">
                        <img src="dashboard-assets/assets/images/rb_25496.png">
                    </div>
                  
                    <a href="/" style="display:flex;justify-content:center;"class="logo-box">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    </a>
        
                    <div class="alert alert-danger mt-3" role="alert">
                        @php
                            $get_status = $_COOKIE['crm_active'] ?? null;
                            $message = 'Your subscription has expired or your plan is canceled. For assistance, please contact support.';
                    
                            if ($get_status !== null && strtolower($get_status) == strtolower(request()->getHost())) {
                                $message = 'Your subscription is inactive. <a style="color=black" href="https://www.goride.run/dashboard" target="_blank">Go To GoRide Dashboard</a> then activate your CRM. For assistance, please contact support.';
                            }
                        @endphp
                        {!! $message !!}
                    </div>

                    <div class="text-center d-flex justify-content-center">
                    <a href="https://www.goride.run/contact">
                        <button class="btn btn-info text-center">Contact Us</button>
                    </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-guest-layout>

<style>

.logo-box{
    margin-top:60px;
    display: flex;
    justify-content: center;
}
.img-box{
           display: flex !important
;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    justify-content: center !important;
    width: 135px;
    align-items: center !important;
    position: absolute;
    left: 178px;
    top: -66px;
    background-color: white;
    border-radius: 50%;

}
.modal-content {
    top: 140px;
}
.btn-primary {
    color: #fff;
    background-color: #0b5ed7;
    border-color: #0a58ca;
}
.btn-secondary {
    color: #fff;
    background-color: #5c636a;
    border-color: #565e64;
}
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: black;
    opacity: 0.5;
    z-index: 1040; /* Below the modal */
    display: block;
}
.modal-content {
    border-radius: 20px; !important}
@media only screen and (min-width: 320px)to (max-width:411px) {
    .img-box {
              left: 139px !important;
        top: -64px!important;
    width: 113px!important;
    }
}

@media only screen and (max-width: 768px) {
    .img-box {
   left: 124px;
        top: -60px;
        width: 113px;
    
    }
}

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-notify.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalElement = new bootstrap.Modal(document.getElementById('staticBackdrop'));
        modalElement.show();
    });
</script>
