@extends('layouts.app')

@section('css')
<style>
    .blink-hard {
        animation: blinker 1s step-end infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }

    @media (min-width: 320px) {
        .try-content {
            margin: -1px -3px !important;

        }

        /* .arrow-img{ width: 70% !important;
                                margin: 0 -2px;} */
    }

    @media (min-width: 768px) {
        .try-content {
            margin: -1px 20px !important;
        }
    }

    .page-header {
        height: 390px;
    }

    .signup-card {
        box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
        border-radius: 10px;
        background: #fff;
        border: 2px solid;
    }

    input[type="password"],
    input[type="email"],
    input[type="text"],
    input[type="file"],
    textarea {
        border: 1px solid #ced4da;
        margin-bottom: 0;
    }

    .cs_btn.cs_style_2 {
        padding: 0px 24px;
    }

    .register_img,
    .enter_otp_img {
        width: 80%;
    }

    body.swal2-toast-shown .swal2-container.swal2-top-end,
    body.swal2-toast-shown .swal2-container.swal2-top-right {
        z-index: 99999999999;
    }

    .otp.ap-otp-input {
        width: 60px;
        height: 55px;
        text-align: center;
        border-radius: 20px;
    }

    .page-header-info h2 {
        font-size: 38px;
    }

    section {
        background: #fafafa;
    }
</style>
@endsection

@section('content')
@php
    $cartData = '';
    //dd(session()->getOldInput()['productID'],session()->getOldInput() );
    if (isset(session()->getOldInput()['quantity']) && session()->getOldInput()['quantity'] > 0) {
        $cartData = json_encode(session()->getOldInput());
    }
@endphp

<section class="page-header">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info">
            <!--<h4>Join the GoRide Community!</h4>-->
            <h1>Start Your Journey with <span>GoRide!</span></h1>
            <p>Sign up today and take the first step towards a seamless ride experience.</p>
        </div>
    </div>
</section>


<section class="new-signup">

    <div class="container pb-5 mb-3">

        <div class="inner-hero-section1">

            <div class="container page-header-info text-center py-4">
                <h2 class="text-dark">SIGN <span>IN</span></h2>
            </div>

        </div>

        <div class="row justify-content-center">

            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                <img class="register_img" src="{{ asset('goride/img/signin_img_png.png') }}">
            </div>

            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">

                <div class="signup-card p-4">

                    <form id="form_submit" method="post">

                        <input type="hidden" id="subid1" value="login">

                        <input type="hidden" id="subid2" value="">

                        <div class="row">

                            <div class="col-12 col-md-12">

                                <div class="form-group mt-2">

                                    <label class="lb-text">MOBILE NUMBER <span class="mand-star">*</span></label><br>

                                    <input type="tel" id="phone" class="form-control w-100" placeholder="Mobile Number"
                                        oninput="mobile_number_validation($(this).val(), inputPhone, 'phone')"
                                        maxlength="15">

                                    <div>
                                        <span id="phone_err" class="spanClass" style="color: red;"></span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class=" col-md-12">

                                <div class="form-group mt-2">

                                    <label class="lb-text">PASSWORD <span class="mand-star">*</span></label>

                                    <div class="input-group">
                                        <input id="password-field" type="password" class="form-control"
                                            name="password-field" maxlength="15">
                                        <span class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                        </span>
                                    </div>

                                    <!--<input id="password-field" type="password" class="form-control"-->
                                    <!--    name="password-field" maxlength="15">-->

                                    <!--<i toggle="#password-field" class="bi bi-eye field-icon toggle-password bi"></i>-->

                                    <span id="password-field_err" class="spanClass" style="color: red;"></span>

                                </div>

                            </div>

                        </div>

                        <div class="row sec-h">

                            <div class="col-6 text-left pr-0">

                                <a href="forgot" class="link" contenteditable="false" style="cursor: pointer;">Forgot
                                    Password?</a>

                            </div>

                            <div class="col-6 text-end pl-0">

                                <a href="loginwithotp" class="link" contenteditable="false"
                                    style="cursor: pointer;">Sign in with OTP?</a>
                                <div class="text-start ps-5" id="signup-link" style="display: none;position: absolute;">
                                    <img src="{{ asset('goride/img/arroww.png') }}" alt="" class=" blink-hard arrow-img"
                                        style="       margin: 0 20px; width: 25%;">
                                    <p class="blink-hard try-content pe-3 pe"
                                        style="    margin: 0 29px;font-weight: 500;color: #b2100e;">Try This </p>

                                </div>

                            </div>



                        </div>

                        <div class="row">

                            <div class="col-12 col-md-12  text-center">

                                <button type="button" class="cs_btn cs_style_2 btn-primary1 mt-3" id="loginBTN"
                                    onclick="loginWithPassword()">LOGIN</button>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-12 text-center my-3">

                                <label class="Forgot-text">Not Registered yet? </label> <a href="signup"
                                    class="Forgot-text pl-1 under-line text-danger" contenteditable="false"
                                    style="cursor: pointer;"> Sign Up</a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection

@section('script')
<script id="rendered-js">


    $(document).ready(function () {
        setCookie('newFormCartData', @json($cartData), 1);
    });

    document.getElementById("togglePassword").addEventListener("click", function () {
        var passwordField = document.getElementById("password-field");
        var icon = this;

        // Toggle the type attribute between password and text
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });


    (() => {

    })();

    const inputMobile = document.querySelector("#phone");

    // Generate Mobile Flag

    const inputPhone = inttelInput(inputMobile);



    const handleChange = () => {

        $('#phone').val('');

        //  $("#phone").removeAttr("maxlength");

    };



    // listen to "keyup", but also "change" to update when the user selects a country

    inputMobile.addEventListener('countrychange', handleChange);





    $(".toggle-password").click(function () {

        $(this).toggleClass("bi-eye  bi-eye-slash");

        var input = $($(this).attr("toggle"));

        if (input.attr("type") == "password") {

            input.attr("type", "text");

        } else {

            input.attr("type", "password");

        }

    });







    const mobile_number_validation = (e, iti, id) => {

        try {

            var t = /[^0-9 ]/g;

            if ($('#phone_err').html(''),

                $('#phone').css('border', '1px solid #18204F'), "" != e) {



                if (0 != parseInt(e.charAt(0))) {



                    if (t.test(e)) e.charAt(e.length - 1), $('#' + id).val(e.replace(t, "")),



                        $('#phone_err').html('The Characters are Not Allowed'),

                        // $('#phone').addClass('warningError');

                        // border: 2px solid red;

                        $('#phone').css('border', '2px solid red');

                    // notifyToast('The Characters are Not Allowed', "warn", "#" + id, "bottom");

                    else {

                        //  var r = country_Mobile_count(parseInt(iti.getSelectedCountryData().dialCode));

                        //  "" != r ? e.length < r || (parseInt(e.length), $("#" + id).attr("maxlength", r)) : $("#" + id).removeAttr("maxlength")

                    }



                } else $('#' + id).val(""),

                    $('#phone_err').html('Mobile Number Should Not Start With Zero'),

                    // $('#phone').addClass('warningError')

                    $('#phone').css('border', '2px solid red')

                // notifyToast('Mobile Number Should Not Start With Zero', "warn", "#" + id, "bottom")



            } else $('#phone_err').html('Mobile Number field is required'),

                // $('#phone').addClass('warningError')

                $('#phone').css('border', '2px solid red')



            // notifyToast('Field Required - Enter Mobile', "warn", "#" + id, "bottom")

        } catch (e) {

            console.log('Error: ' + e.message);

        }

    }



    const country_Mobile_count = (e) => {

        try {

            var t = "";



            return 971 == e || 61 == e || 966 == e || 33 == e || 61 == e || 31 == e ? 9 : 91 == e || 63 == e || 1 ==
                e || 44 ==



                e || 49 == e || 81 == e || 60 == e ? 10 : 973 == e || 65 == e || 852 == e || 965 == e || 974 == e ||
                    968 == e ||



                    45 == e ? 8 : ""

        } catch (e) {

            console.log('Error: ' + e.message);

        }

    }





    const loginWithPassword = () => {

        try {



            let mobileNo = getCountryCodeAndNumber(inputPhone);

            let password = $('#password-field').val();

            const btn = $('#loginBTN');



            $('#phone_err,#password-field_err').html('');

            $('#phone,#password-field').removeClass('warningError');



            let err = 0;



            if (mobileNo == '') {


                $('#phone_err').html('Mobile Number is required');


                $('#phone').css('border', '2px solid red');

                err++;

            }





            if (password == '') {


                $('#password-field_err').html('Password is required');


                $('#phone').css('border', '2px solid red');

                err++;

            }



            if (err > 0) {

                return false;

            }


            if (!inputPhone.isValidNumber() || !inputPhone.isValidNumberPrecise()) {
                $('#phone_err').html('Invaild Mobile No!');


                $('#phone').css('border', '2px solid red');

                return false;
            }




            var h = new FormData();

            h.append('mobile', mobileNo);

            let utm_s = getCookie('utm_source')??null;
            let utm_c = getCookie('utm_campaign')??null;
            
            h.append('utm_campaign', utm_c)
            h.append('utm_source', utm_s)

            h.append('password', password);
            localStorage.setItem('mobile', getCountryCodeAndNumber(inputPhone));
            $.ajax({



                url: origin + '/api/loginWithPassword',

                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,

                beforeSend: function () {

                    // Button Loading

                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);

                },

                success: function (response) {



                    if (response != "") {

                        if (response.status == 'success') {



                            let redirectURL = origin;




                            setCookie("sessionToken", response.token, 3);
                            localStorage.setItem('authToken', response.token);

                            setCookie("cusid", response.data.user_id, 3);

                            setCookie("name", response.data.name, 3);

                            //  deleteCookie('billingDetails');
                            deleteCookie('transaction_id');
                            deleteCookie('finaltotal');

                            if (getCookie("sessionToken") && getCookie("sessionToken") != undefined &&
                                getCookie("sessionToken") != null && getCookie("cusid") != undefined &&
                                getCookie("cusid") != null && getCookie("cusid") != '') {

                                // gtag('event', 'login_with_password_success', {
                                //     'event_category': 'User Engagement',
                                //     'event_label': 'Successful Login with Password',
                                //     'user_id': getCookie("cusid")
                                // });

                                let cartData =  getCookie('newFormCartData') != undefined &&  getCookie('newFormCartData') != null &&  getCookie('newFormCartData') != '' ? JSON.parse(getCookie('newFormCartData')) : {};
                                let quantity = cartData.quantity || 0;

                                localStorage.setItem('userData', JSON.stringify(response.data));

                                showToast('success', response.message, 5000);

                                window.location.replace(redirectURL + (quantity > 0 ? '/cart' : '/dashboard'));

                            } else {

                                showToast('warning', 'Login Failed!', 5000);

                            }

                        } else {



                            // Loading Off 

                            btn.html(`LOGIN`).prop('disabled', false);

                            showToast('error', response.message, 5000);

                            //  document.querySelector('.col-6.text-end.pl-0 a[href="loginwithotp"]').classList.add('blink-hard');

                            document.getElementById('signup-link').style.display = 'block';

                        }

                    }



                    // Loading Off 

                    btn.html(`LOGIN`).prop('disabled', false);

                },

                processData: false,

                contentType: false,

                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    btn.html(`LOGIN`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                }



            });

        } catch (e) {

            console.log('Error: ' + e.message);

        }

    }
</script>
@endsection