@extends('layouts.app')

@section('css')
<style>
    #otp_err {
        color: red;
    }

    .warningError {
        border: 2px solid red !important;
    }

    p.by-click-text1.text-center.my-4.under-line {
        cursor: pointer;
    }

    .pointOFFEvent {
        pointer-events: none;
    }

    .page-header {
        height: 390px;
    }

    .signup-card {
        box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
        border-radius: 10px;
        background: #fff;
        width: 95%;
    }
    
    @media (max-width: 768px) {
        .page-header .page-header-shape, .page-header .container {
            display: none;
        }
        
        .page-header {
            height: 120px !important;
        }
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

    .register_img {
        width: 70%;
    }

    .enter_otp_img {
        width: 80%;
    }

    .reset_password_img {
        width: 65%;
    }

    body.swal2-toast-shown .swal2-container.swal2-top-end,
    body.swal2-toast-shown .swal2-container.swal2-top-right {
        z-index: 99999999999;
    }

    .otp.ap-otp-input {
        width: 50px;
        height: 50px;
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
<!-- Breadcrumb -->

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

<section class="new-signup pb-5" id="formOne">
    <div class="container pb-lg-5">
        <div class="inner-hero-section1">
            <div class="container page-header-info text-center py-4">
                <h2 class="text-dark">SIGN IN <span>WITH OTP</span></h2>
            </div>
        </div>
        <div class="row justify-content-center gap-4 gap-md-0">
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center order-2 order-md-1">
                <img class="register_img" src="{{ asset('goride/img/signin_img_png.png') }}">
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-center align-items-center order-1 order-md-2">
                <div class="signup-card p-4">
                    <form method="post">
                        <div class="row">
                            <div class="col-12 col-md-12 text-center">
                                <div class="form-group mt-2 text-start">
                                    
                                    <label class="lb-text">MOBILE NUMBER<span class="mand-star">*</span></label><br>
                                    <input type="tel" id="phone" class="form-control w-100" placeholder="Mobile Number"
                                        oninput="mobile_number_validation($(this).val(), inputPhone, 'phone')"
                                        maxlength="15">
                                    <!-- <input type="text" id="phone" class="form-control w-100" placeholder="Mobile Number" name="phone"> -->
                                    <span id="phone_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12  my-3 text-center">
                                <button type="button" class="cs_btn cs_style_2 btn-primary1 px-3" id="firstBtnSub"
                                    onclick="firstFormSubmit()">SEND OTP</button>
                            </div>

                            <div class="col-md-12 text-center my-2">
                                <label class="Forgot-text mem-t">Remember your password? </label> <a href="login"
                                    class="Forgot-text mem-t under-line" contenteditable="false"
                                    style="cursor: pointer;"> Sign In</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="new-signup pb-5" id="formTwo">
    <div class="container pb-lg-5">
        <div class="inner-hero-section1">
            <div class="container page-header-info text-center py-4">
                <h2 class="text-dark">SIGN IN <span>WITH OTP</span></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                <div class="signup-card text-center p-3">
                    <form id="form_submit" method="post">
                        <h5>ENTER OTP</h5>
                        <input type="hidden" id="fcm_token" value="">
                        <input type="hidden" id="browser_fcm_token" value="">
                        <input type="hidden" id="platform_type" value="">
                        <div class="col-12 col-md-12 text-center my-3 text-center otp-text">
                            <p id="formThreeTxt">
                                OTP SENT TO YOUR REGISTERED MOBILE NUMBER<br>
                                TO +971 50123 4567</p>
                            <!-- <p><img src="assets/img/whatsapp-resend.png" width="29px">&nbsp;WhatsApp / SMS</p> -->
                            <p><i class="fa-brands fa-whatsapp"></i>&nbsp;WhatsApp / SMS</p>
                        </div>
                        <div
                            class="col-md-10 text-center my-2 otp-box justify-content-around m-auto text-center d-flex ">

                            <!-- <input class="otp ap-otp-input" type="tel" id="otp1" data-index="0" maxlength="1" autocomplete="off">
                                          <input class="otp ap-otp-input" type="tel" id="otp2" data-index="0" maxlength="1" autocomplete="off">
                                          <input class="otp ap-otp-input" type="tel" id="otp3" data-index="0" maxlength="1" autocomplete="off">
                                          <input class="otp ap-otp-input" type="tel" id="otp4" data-index="0" maxlength="1" autocomplete="off">
                                          <input class="otp ap-otp-input" type="tel" id="otp5" data-index="0" maxlength="1" autocomplete="off">
                                          <input class="otp ap-otp-input" type="tel" id="otp6" data-index="0" maxlength="1" autocomplete="off"> -->
                            <input class="otp ap-otp-input" type="tel" id="otp1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">
                            <input class="otp ap-otp-input" type="tel" id="otp2"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">
                            <input class="otp ap-otp-input" type="tel" id="otp3"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">
                            <input class="otp ap-otp-input" type="tel" id="otp4"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">
                            <input class="otp ap-otp-input" type="tel" id="otp5"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">
                            <input class="otp ap-otp-input" type="tel" id="otp6"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0" maxlength="1"
                                autocomplete="off">


                        </div>

                        <div class="col-12 col-md-12 text-center">
                            <span id="otp_err" class="spanClass"></span>
                        </div>
                        <div class="col-12 col-md-12 text-center my-2"><button type="button"
                                class="btn-primary1 n-btn mt-lg-0 cs_btn cs_style_2" id="verifyBTN">VERIFY
                                OTP</button></div>
                        <div class="col-12 col-md-12 text-center  mb-3" id="resendBTN"
                            onclick="$(this).toggleClass('pointOFFEvent');">
                            <p class="by-click-text1 text-center my-1 under-line" onclick="firstFormSubmit(true)">
                                Resend OTP</p>
                        </div>
                        <!-- <div class=" col-md-12  mt-3 text-center">
                                          <p class="by-click-text2 text-center">If you haven't received the OTP via SMS, <br>please tap here</p>
                                       </div>
                                       <div class="col-12 col-md-12 text-center my-2">
                                          <button type="button" class=" btn-primary12   mt-lg-0"><img src="assets/img/whatsapp-resend.png" width="27px"> SEND OTP</button>
                                       </div>
                                       <div class=" col-md-12  mt-3 text-center">
                                          <p class="by-click-text2 text-center mb-3 mb-lg-0"> to receive it through WhatsApp</p>
                                       </div> -->
                    </form>
                </div>
            </div>
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                <img class="enter_otp_img" src="{{ asset('goride/img/enter_otp_svg.svg') }}">
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script id="rendered-js">
    const inputMobile = document.querySelector("#phone");
    const $inp1 = $(".ap-otp-input");
    let enc = '';

    // Generate Mobile Flag
    const inputPhone = inttelInput(inputMobile);
    const handleChange = () => {
        $('#phone').val('');
    };
    // listen to "keyup", but also "change" to update when the user selects a country
    inputMobile.addEventListener('countrychange', handleChange);




    (function () {

        $('#formTwo').hide();



        $("input, select").on("input change", function () {
            let txt = $(`#${$(this).attr('id')}_err`).text();
            var otp = $('#otp1').val().trim() + $('#otp2').val().trim() + $('#otp3').val().trim() + $(
                '#otp4').val().trim() + $('#otp5').val().trim() + $('#otp6').val().trim();

            $(this).removeClass('warningError');
            $(`#${$(this).attr('id')}_err`).html('');
            $(`#otp_err`).html('');

            if ($(this).val() == '') {
                $(this).addClass('warningError');
                $(`#${$(this).attr('id')}_err`).html(txt);
            }

            if (otp != undefined && otp != null && otp.trim().length === 6) {
                $(`#verifyBTN`).trigger('click');
            }


        });
    })();



    // const mobile_number_validation = (e, iti, id) => {

    //     try {

    //         var t = /[^0-9 ]/g;

    //         if ($('#phone_err').html(''),

    //             $('#phone').css('border', '1px solid #18204F'), "" != e) {



    //             if (0 != parseInt(e.charAt(0))) {



    //                 if (t.test(e)) e.charAt(e.length - 1), $('#' + id).val(e.replace(t, "")),



    //                     $('#phone_err').html('The Characters are Not Allowed'),

    //                     // $('#phone').addClass('warningError');

    //                     // border: 2px solid red;

    //                     $('#phone').css('border', '2px solid red');

    //                 // notifyToast('The Characters are Not Allowed', "warn", "#" + id, "bottom");

    //                 else {

    //                     //  var r = country_Mobile_count(parseInt(iti.getSelectedCountryData().dialCode));

    //                     //  "" != r ? e.length < r || (parseInt(e.length), $("#" + id).attr("maxlength", r)) : $("#" + id).removeAttr("maxlength")

    //                 }



    //             } else $('#' + id).val(""),

    //                 $('#phone_err').html('Mobile Number Should Not Start With Zero'),

    //                 // $('#phone').addClass('warningError')

    //                 $('#phone').css('border', '2px solid red')

    //             // notifyToast('Mobile Number Should Not Start With Zero', "warn", "#" + id, "bottom")



    //         } else $('#phone_err').html('Mobile Number field is required'),

    //             // $('#phone').addClass('warningError')

    //             $('#phone').css('border', '2px solid red')



    //         // notifyToast('Field Required - Enter Mobile', "warn", "#" + id, "bottom")

    //     } catch (e) {

    //         console.log('Error: ' + e.message);

    //     }

    // }
    
    const mobile_number_validation = (e, iti, id) => {
    try {
        var t = /[^0-9()\-\s]/g;

        $('#phone_err').html('');
        $('#phone').css('border', '1px solid #18204F');

        if (e != "") {
            if (parseInt(e.charAt(0)) !== 0) {

                if (t.test(e)) {
                    $('#' + id).val(e.replace(t, ""));
                    $('#phone_err').html('Only numbers, spaces, (), and - are allowed');
                    $('#phone').css('border', '2px solid red');
                } else {
                    // Additional validation can go here
                }

            } else {
                $('#' + id).val("");
                $('#phone_err').html('Mobile Number Should Not Start With Zero');
                $('#phone').css('border', '2px solid red');
            }

        } else {
            $('#phone_err').html('Mobile Number field is required');
            $('#phone').css('border', '2px solid red');
        }
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

    //  Mobile Number Validation End



    // OTP box validation Start

    $inp1.on({
        paste(t) {
            let e = t.originalEvent.clipboardData.getData("text").trim();
            if (!/\d{6}/.test(e)) return t.preventDefault();
            let i = [...e];
            $inp1.val(t => i[t]).eq(5).focus();
        },
        input(t) {
            let e = $inp1.index(this);
            if (this.value) $inp1.eq(e + 1).focus();
        },
        keydown(t) {
            let e = $inp1.index(this);
            if (!this.value && t.key === "Backspace" && e) $inp1.eq(e - 1).focus();
        }
    });





    /// first Form Submit Start
    const firstFormSubmit = (resend = false) => {
        try {
            let error = 0;

            $('#phone_err').html('');
            //  $('#mobile_code').removeClass('warningError');
            $('#phone').css('border', '1px solid #18204F');




            let mobileNumber = getCountryCodeAndNumber(inputPhone);
            let dialCode = inputPhone.getSelectedCountryData().dialCode;





            if (mobileNumber.length < 1 || mobileNumber == null || mobileNumber == undefined || mobileNumber ==
                'null' || mobileNumber.trim() == '' || $("#phone").val() == '') {
                // notifyToast('Kindly enter mobile no', "error", "#mobile_code", "bottom");
                $('#phone_err').html('Mobile Number is required');
                $('#phone').css('border', '2px solid red');
                error++;
            }



            if (error > 0) {
                return false;
            }

            if (0 == $("#phone").val().charAt(0)) {
                // notifyToast('Remove "Zero" at beginning', "error", "#mobile_code", "bottom");
                $('#phone_err').html('Remove "Zero" at beginning');
                //  $('#mobile_code').addClass('warningError');
                $('#phone').css('border', '2px solid red');
                return false;
            }



            if (!inputPhone.isValidNumber() || !inputPhone.isValidNumberPrecise()) {
                $('#phone_err').html('Invaild Mobile No!');


                $('#phone').css('border', '2px solid red');

                return false;
            }





            var h = new FormData();
            

            h.append('mobile', mobileNumber);
            h.append('isResend', resend)
            
            let utm_s = getCookie('utm_source')??null;
            let utm_c = getCookie('utm_campaign')??null;
            h.append('utm_campaign', utm_c)
            h.append('utm_source', utm_s)


            var btn = $('#firstBtnSub');


            if (resend) {
                $(`#resendPbtn`).attr('disabled', 'disabled');
            }

            $.ajax({
                // url: url,
                url: origin + '/api/loginOTP',
                type: 'POST',
                data: h,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {

                    // Button Loading

                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);

                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 'success') {
                            enc = response.data.enc.toString();

                            // if (dialCode == 971) {

                            //  $('#firstBtnSub').html(btn);
                            $('#formOne').hide();
                            $('#formThreeTxt').text(
                                `Enter the OTP code sent to your mobile number +${mobileNumber}`);
                            $('#verifyBTN').attr(`onclick`,
                                `verifyUPdate(${mobileNumber}, ${dialCode})`);
                            $('#formTwo').show();
                            trim_seconds();
                            showToast('success', response.message, 5000);


                        } else {
                            //  $('#firstBtnSub').html(btn);
                            // errorThrow("mobileerror", response.message, "phone");
                            $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 

                    btn.html(`SEND OTP`).prop('disabled', false);

                },

                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    btn.html(`SEND OTP`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },


                processData: false,
                contentType: false
            });
            // }


        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }



    const verifyUPdate = (mobile, dialCode) => {
        try {
            var user = '';
            $('#otpvererr').html('');
            // var mobile = mobile;
            // var dialCode = dialCode;
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() +
                $('#otp6').val();
            $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').removeClass('warningError');
            $('#otp_err').html('');
            // if (mobile == '') {
            //    // $('#otpvererr').html('<div class="alert alert-danger" role="alert">Kindly Enter the Mobile Number</div>');
            //    return false;
            // }

            if (otp == '') {
                // $('#otpvererr').html('<div class="alert alert-danger" role="alert">Kindly Enter the OTP</div>');
                // toast('error', 'Kindly Enter the OTP');
                $('#otp_err').html('Kindly Enter the OTP');
                $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').addClass('warningError');
                return false;
            }

            if (otp.length < 6) {
                // $('#otpvererr').html('<div class="alert alert-danger" role="alert">Kindly Enter the OTP</div>');
                // toast('error', 'Kindly Enter the OTP');
                $('#otp_err').html('Kindly Enter the OTP');
                $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').addClass('warningError');
                return false;
            }

            if (enc == '' || enc == undefined) {
                // $('#otpvererr').html('<div class="alert alert-danger" role="alert">Kindly Refresh and Try Again</div>');
                // toast('error', 'Kindly Refresh and Try Again');
                $('#otp_err').html('Kindly Refresh and Try Again');
                $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').addClass('warningError');
                return false;
            }


            updateMobile(user, mobile, dialCode, otp);
            // }
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }


    const updateMobile = (user, mobile, dialCode, otp) => {
        try {
            var btn = $('#verifyBTN');
            //  $('#verifyBTN').html(`<button class="btn btn-primary1 n-btn mt-lg-0" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>`);

            var h = new FormData();
            // h.append('allowFB', user);
            h.append('enc', enc);
            h.append('otp', otp);
            
            let utm_s = getCookie('utm_source')??null;
            let utm_c = getCookie('utm_campaign')??null;
            h.append('utm_campaign', utm_c)
            h.append('utm_source', utm_s)
            h.append('fcm_token', $('#fcm_token').val()??null)
            h.append('platform_type', $('#platform_type').val()??null)
            h.append('browser_fcm_token', $('#browser_fcm_token').val()??null)

            $.ajax({
                url: origin + '/api/loginOTPverify',
                type: 'POST',

                data: h,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 'success') {

                            setCookie("sessionToken", response.token, 3);
                            localStorage.setItem('authToken', response.token);
                            setCookie("cusid", response.data.user_id, 3);
                            setCookie("name", response.data.name, 3);
                            deleteCookie('transaction_id');
                            deleteCookie('finaltotal');
                            if (getCookie("sessionToken") && getCookie("sessionToken") != undefined &&
                                getCookie("sessionToken") != null && getCookie("cusid") != undefined &&
                                getCookie("cusid") != null && getCookie("cusid") != '') {
                                localStorage.setItem('userData', JSON.stringify(response.data));
                                showToast('success', 'Login Successfully', 5000);

                                // gtag('event', 'login_with_OTP_success', {
                                //     'event_category': 'User Engagement',
                                //     'event_label': 'Successful Login with OTP',
                                //     'user_id': getCookie("cusid")
                                // });


                                // if (getCookie('newFormCartData') && getCookie("newFormCartData") !=
                                //     undefined && getCookie("newFormCartData") != null) {
                                //     window.location.replace(origin + '/billing');
                                // } else {
                                //     window.location.replace(origin + '/#PARTICIPATE');
                                // }

                                let cartData =  getCookie('newFormCartData') != undefined &&  getCookie('newFormCartData') != null &&  getCookie('newFormCartData') != '' ? JSON.parse(getCookie('newFormCartData')) : {};
                                let quantity = cartData.quantity || 0;

                                window.location.replace(origin + (quantity > 0 ? '/cart' : '/dashboard'));
                                // window.location.replace(origin + "/dashboard");
                            } else {
                                showToast('warning', 'Login Failed!', 5000);
                            }
                        } else {
                            //  $('#verifyBTN').html(btn);
                            // $('#otpvererr').html(`<div class="alert alert-danger" role="alert">${response.message}</div>`);
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 
                    btn.html(`VERIFY OTP`).prop('disabled', false);
                },
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`VERIFY OTP`).prop('disabled', false);
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




    const trim_seconds = () => {
        try {
            var resentBTN = $(`#resendBTN`).html();
            var now_1 = new Date().getTime();

            x = setInterval(function () {

                // Get today's date and time

                var now = new Date().getTime();

                // Find the distance between now and the count down date

                var distance = (now_1 + 60000) - now;

                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                $(`#resendBTN`).html(
                    `<p class="by-click-text1 text-center my-4 under-line">${seconds}s</p>`);
                //  document.getElementById("resendBTN").innerHTML = `<p class="by-click-text1 text-center my-4 under-line">${seconds}s</p>`;

                if (distance < 0) {

                    clearInterval(x);

                    $(`#resendBTN`).html(resentBTN);
                    //  $(`#resendBTN`).toggleClass('pointOFFEvent');
                    if ($(`#resendBTN`).hasClass('pointOFFEvent')) {
                        $(`#resendBTN`).removeClass('pointOFFEvent');
                    }

                    // document.getElementById("resendBTN").innerHTML = resentBTN;

                    $(`#resendPbtn`).removeAttr('disabled', 'disabled');

                }

            }, 1000);
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
</script>
@endsection