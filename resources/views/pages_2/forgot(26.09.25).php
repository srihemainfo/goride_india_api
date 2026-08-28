@extends('layouts.app')

@section('css')
    <style>
    
      .whatsapp-label {
      font-size: 16px;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
    
    .whatsapp-icon {
      position: relative;
      display: inline-block;
    }

    .whatsapp-icon i {
      background: #25D366;    
      color: white;
      border-radius: 50%;
      font-size: 16px;
      padding: 8px;
    }
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
        }
        
        @media (max-width: 768px) {
            .page-header .page-header-shape, .page-header .container {
                display: none;
            }
                #formThreeTxt {
            font-size: 15px;
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
            width: 60%;
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
                    <h2 class="text-dark">FORGET <span>PASSWORD</span></h2>
                </div>
            </div>
            <div class="row justify-content-center gap-4 gap-md-0">
                <div class="col-12 col-md-4 d-flex justify-content-center align-items-center">
                    <div class="signup-card p-4">
                        <form method="post">
                            <div class="row">


                                <div class="col-12 col-md-12 text-start">
                                    <div class="form-group mt-2">
                                        <label class="lb-text">MOBILE NUMBER<span class="mand-star">*</span></label><br>

                                        <!-- <input type="text" id="phone" class="form-control w-100" placeholder="Mobile Number" name="name"> -->
                                        <input type="tel" id="phone" class="form-control w-100"
                                            placeholder="Mobile Number"
                                            oninput="mobile_number_validation($(this).val(), inputPhone, 'phone')"
                                            maxlength="15">
                                        <span id="phone_err" class="spanClass" style="color: red;"></span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-12  text-center my-4">
                                    <button type="button" class="cs_btn cs_style_2 btn-primary1" id="firstBtnSub"
                                        onclick="firstFormSubmit()">NEXT</button>
                                </div>


                                <div class="col-md-12 text-center my-2">
                                    <label class="Forgot-text mem-t">Remember your password? </label> <a href="login"
                                        class="Forgot-text mem-t under-line" contenteditable="false"
                                        style="cursor: pointer;">Sign In</a>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                    <img class="register_img" src="{{ asset('goride/img/forget_png.png') }}">
                </div>
            </div>
        </div>
    </section>
    <section class="new-signup pb-5" id="formTwo">
        <div class="container pb-lg-5">
            <div class="inner-hero-section1">
                <div class="container page-header-info text-center pt-5">
                    <h2 class="text-dark">VERIFY YOUR <span>ACCOUNT</span></h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                    <div class="signup-card text-center p-4">
                        <form id="secondForm" method="post">
                            <h5>ENTER OTP</h5>
                            <div class="col-12 col-md-12 text-center my-3 text-center otp-text">
                                <p id="formThreeTxt">
                                     Enter the 6-digit code from your WhatsApp +919976505486</p>
                                 <p class="whatsapp-label">
                              <span class="whatsapp-icon">
                                  <i class="fa-brands fa-whatsapp"></i>  
                            </span>
                            &nbsp;WhatsApp </p>
                                <!-- <p><img src="assets/img/whatsapp-resend.png" width="29px">&nbsp;WhatsApp / SMS</p> -->
                            </div>
                            <div
                                class=" col-md-10 text-center my-2 otp-box justify-content-around m-auto text-center d-flex ">


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
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0"
                                    maxlength="1" autocomplete="off">
                                <input class="otp ap-otp-input" type="tel" id="otp6"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" data-index="0"
                                    maxlength="1" autocomplete="off">


                            </div>

                            <div class="col-12 col-md-12 text-center">
                                <span id="otp_err" class="spanClass"></span>
                            </div>
                            <div class="col-12 col-md-12 text-center my-4">
                                <button type="button" id="verifyBTN"
                                    class="btn-primary1 cs_btn cs_style_2 n-btn mt-lg-0">VERIFY
                                    OTP</button>
                            </div>
                            <div class="col-12 col-md-12 text-center  mb-3" id="resendBTN"
                                onclick="$(this).toggleClass('pointOFFEvent');">
                                <p class="by-click-text1 text-center my-4 under-line" id="resendPbtn"
                                    onclick="firstFormSubmit(true)">Resend OTP</p>
                            </div>
                            <div style="text-align: center; margin-top: 2px;">
                          <a href="https://wa.me/916369742104" target="_blank" 
                             style="text-decoration: underline; color: #25D366; font-size: 16px; font-weight: 600;">
                            <i class="fa-brands fa-whatsapp me-1"></i> Open WhatsApp
                          </a>
                        </div>

                        </form>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                    <img class="enter_otp_img" src="{{ asset('goride/img/enter_otp_svg.svg') }}">
                </div>
            </div>
        </div>
    </section>
    <section class="new-signup" id="formThree">
        <div class="container pb-5 mb-3">
            <div class="inner-hero-section1">
                <div class="container page-header-info text-center pt-5">
                    <h2 class="text-dark">RESET <span>PASSWORD</span></h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                    <div class="signup-card p-4">
                        <form id="form_submit" method="post">
                            <div class="row">
                                <div class=" col-md-12">
                                    <div class="form-group mt-1 pb-3">
                                        <label class="lb-text">NEW PASSWORD<span class="mand-star">*</span></label>
                                        <div class="input-group">
                                            <input id="new-password" type="password" class="form-control" name="password"
                                            maxlength="15">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="new-password-toggle" style="cursor: pointer;"></i>
                                            </span>
                                        </div>
                                        <span id="new-password_err" class="spanClass" style="color: red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class=" col-md-12">
                                    <div class="form-group mt-1 pb-3">
                                        <label class="lb-text">CONFIRM PASSWORD<span class="mand-star">*</span></label>
                                        <div class="input-group">
                                            <input id="con-password" type="password" class="form-control" name="password"
                                            maxlength="15">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye" id="con-password-toggle" style="cursor: pointer;"></i>
                                            </span>
                                        </div>
                                        <span id="con-password_err" class="spanClass" style="color: red;"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-12  mt-3 text-center">
                                    <button type="button" class="cs_btn cs_style_2 btn-primary1 mt-2 px-3  mt-lg-0"
                                        id="UpdatePasswordBTN" onclick="UpdatePassword()" style="font-weight:600;font-size: 16px;">UPDATE
                                        PASSWORD</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                    <img class="reset_password_img" src="{{ asset('goride/img/reset_password_png.png') }}">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script id="rendered-js">
    
    
    document.getElementById("new-password-toggle").addEventListener("click", function () {
        var passwordField = document.getElementById("new-password");
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
    
    document.getElementById("con-password-toggle").addEventListener("click", function () {
        var passwordField = document.getElementById("con-password");
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
    
    
        //  let signUPmobile;
        //  var coderesult; // Declare the coderesult variable
        let enc = '';
        let verifyOTP = '';
        const $inp1 = $(".ap-otp-input");
        const inputMobile = document.querySelector("#phone");

        // Generate Mobile Flag
        const inputPhone = inttelInput(inputMobile);



        const handleChange = () => {

            $('#phone').val('');

            //  $("#phone").removeAttr("maxlength");

        };



        // listen to "keyup", but also "change" to update when the user selects a country

        inputMobile.addEventListener('countrychange', handleChange);




        (function() {

            $(`#formTwo,#formThree`).hide();

            $("input, select").on("input change", function() {
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







        // Password Validation Start
        const validatePassword = (password, id) => {
            try {
                // Minimum length of 6 characters
                if (password.length < 6) {
                    // notifyToast('Minimum length of 6 characters', "warn", "#" + id, "bottom");
                    $(`#${id}`).addClass('warningError');
                    $(`#${id}_err`).html('Minimum length of 6 characters');
                    return false;
                }

                // At least 1 uppercase letter
                if (!/[A-Z]/.test(password)) {
                    // notifyToast('At least 1 uppercase letter', "warn", "#" + id, "bottom");
                    $(`#${id}`).addClass('warningError');
                    $(`#${id}_err`).html('At least 1 uppercase letter');
                    return false;
                }

                // At least 1 digit
                if (!/[0-9]/.test(password)) {
                    // notifyToast('At least 1 digit', "warn", "#" + id, "bottom");
                    $(`#${id}`).addClass('warningError');
                    $(`#${id}_err`).html('At least 1 digit');
                    return false;
                }

                // At least 1 special character (non-alphanumeric)
                if (!/[!@#$%^&*()\-_=+{}[\]|;:'",<.>/?]/.test(password)) {
                    // notifyToast('At least 1 special character (non-alphanumeric)', "warn", "#" + id, "bottom");
                    $(`#${id}`).addClass('warningError');
                    $(`#${id}_err`).html('At least 1 special character (non-alphanumeric)');
                    return false;
                }

                // All criteria met
                return true;
            } catch (e) {
                console.log('Error: ' + e.message);
            }
        }
        // Password Validation End

        // Password generator Start
        const generatePassword = (length = 12) => {
            try {
                const uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                const lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
                const digitChars = '0123456789';
                const specialChars = '!@#$%^&*()_-+=[]{}|;:,.<>?';

                const allChars = uppercaseChars + lowercaseChars + digitChars + specialChars;

                let password = '';
                password += uppercaseChars[Math.floor(Math.random() * uppercaseChars.length)];
                password += lowercaseChars[Math.floor(Math.random() * lowercaseChars.length)];
                password += digitChars[Math.floor(Math.random() * digitChars.length)];
                password += specialChars[Math.floor(Math.random() * specialChars.length)];

                for (let i = 4; i < length; i++) {
                    password += allChars[Math.floor(Math.random() * allChars.length)];
                }

                return password;
            } catch (e) {
                console.log('Error: ' + e.message);
            }
        }

        // Password generator End


        // SUGGEST password Start 
        const suggestPassword = () => {
            try {
                $('#password-field').val(generatePassword(6));
            } catch (e) {
                console.log('Error: ' + e.message);
            }
        }
        // SUGGEST password End



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



        // OTP box validation End



        $(".toggle-password").click(function() {
            $(this).toggleClass("bi-eye  bi-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });



        /// first Form Submit Start
        const firstFormSubmit = (resend = false) => {
            try {
                let error = 0;

                $('#phone_err').html('');

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
                h.append('isResend', resend);


                var btn = $('#firstBtnSub');
                // $('#firstBtnSub').html(`<button class="btn btn-primary1 mt-2  mt-lg-0" type="button" disabled>
            //                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            //                            Loading...
            //                            </button>`);

                if (resend) {
                    $(`#resendPbtn`).attr('disabled', 'disabled');
                }

                $.ajax({
                    // url: url,
                    url: origin + '/api/forgotRequest',
                    type: 'POST',
                    data: h,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {

                        // Button Loading

                        btn.html(
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                        ).prop('disabled', true);

                    },
                    success: function(response) {
                        // var response = JSON.parse(data);
                        if (response != "") {
                            if (response.status == 'success') {
                                enc = response.data.enc.toString();

                                // if (dialCode == 971) { 

                                // $('#firstBtnSub').html(btn);
                                $('#formOne').hide();
                                $('#formThreeTxt').text(
                                    ` Enter the 6-digit code from your WhatsApp +${mobileNumber}`);
                                // $('#verifyBTN').html(`<button type="button" class="btn btn-primary1 n-btn mt-lg-0" onclick="verifyUPdate(${mobileNumber}, ${dialCode})">VERIFY OTP</button>`);
                                $('#verifyBTN').attr(`onclick`,
                                    `verifyUPdate(${mobileNumber}, ${dialCode})`);

                                $('#formTwo').show();
                                trim_seconds();
                                showToast('success', response.message, 5000);


                            } else {
                                // $('#firstBtnSub').html(btn);
                                // errorThrow("mobileerror", response.message, "phone");
                                $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                                showToast('error', response.message, 5000);
                            }
                        }

                        // Loading Off 

                        btn.html(`NEXT`).prop('disabled', false);

                    },
                    error: function(xhr, status, error) {

                        showToast("error", "Request failed", 5000);

                        btn.html(`NEXT`).prop('disabled', false);

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
            var user = '';
            $('#otpvererr').html('');
            // var mobile = mobile;
            // var dialCode = dialCode;
            var otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() + $(
                '#otp6').val();
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

        }


        const updateMobile = (user, mobile, dialCode, otp) => {

            var btn = $('#verifyBTN');
            // $('#verifyBTN').html(`<button class="btn btn-primary1 n-btn mt-lg-0" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>`);

            var h = new FormData();
            // h.append('allowFB', user);
            h.append('enc', enc);
            h.append('otp', otp);

            $.ajax({
                url: origin + '/api/forgotOTPverify',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // data: {
                //     method: "verify_TOP_UPDATE",
                //     mobile: mobile,
                //     dialCode: dialCode,
                //     otp: otp,
                //     user: user
                // },
                data: h,
                // headers: {
                //    "Authorization": "Bearer " + getCookie('sessionToken')
                // },
                beforeSend: function() {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function(response) {
                    // var response = JSON.parse(data);

                    if (response != "") {
                        if (response.status == 'success') {
                            enc = response.data.enc.toString();
                            $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').val('');
                            verifyOTP = otp;
                            // $('#verifyBTN').html(btn);
                            $('#formTwo').hide();
                            $('#formThree').show();
                            // toast('success', response.message);
                        } else {
                            // $('#verifyBTN').html(btn);
                            // $('#otpvererr').html(`<div class="alert alert-danger" role="alert">${response.message}</div>`);
                            // toast('error', response.message);
                            $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').addClass('warningError');
                            $('#otp_err').html(response.message);
                        }
                    }


                    // Loading Off 
                    btn.html(`VERIFY OTP`).prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`VERIFY OTP`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                },
                processData: false,
                contentType: false
            });

        }


        const UpdatePassword = () => {
            try {
                let error = 0;
                let npass = $('#new-password').val();
                let cpass = $('#con-password').val();

                $('#new-password,#con-password').removeClass('warningError');
                $('#new-password_err,#con-password_err').html('');

                var otp = verifyOTP;

                if (npass.length < 1 || npass == null || npass == undefined || npass == 'null' || npass.trim() == '') {

                    $('#new-password').addClass('warningError');
                    $('#new-password_err').html('New password is required');

                    // notifyToast('Kindly enter password', "error", "#new-password", "bottom");
                    error++;
                }

                if (cpass.length < 1 || cpass == null || cpass == undefined || cpass == 'null' || cpass.trim() == '') {
                    $('#con-password').addClass('warningError');
                    $('#con-password_err').html('Confirm password is required');
                    // notifyToast('Kindly enter password', "error", "#con-password", "bottom");
                    error++;
                }

                if (error > 0) {
                    return false;
                }




                if (!validatePassword(npass, 'new-password')) {
                    return false;
                }

                if (!validatePassword(cpass, 'con-password')) {
                    return false;
                }

                if (npass != cpass) {
                    // toast('error', 'The new password and the confirmed password do not match.');
                    $('#con-password').addClass('warningError');
                    $('#con-password_err').html('The new password and the confirmed password do not match.');
                    return false;
                }

                if (enc == '' || enc == undefined) {
                    // toast('error', 'Kindly Refresh and Try Again');
                    $('#con-password').addClass('warningError');
                    $('#con-password_err').html('Kindly Refresh and Try Again.');
                    return false;
                }

                var h = new FormData();
                h.append('enc', enc);
                h.append('otp', otp);
                h.append('password', npass);
                h.append('confirm_password', cpass);


                var btn = $(`#UpdatePasswordBTN`);
                // $('#UpdatePasswordBTN').html(`<button class="btn btn-primary1 n-btn mt-lg-0" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...</button>`);

                $.ajax({
                    url: origin + '/api/forgotPasswordUpdate',
                    type: 'POST',
                    data: h,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        // Button Loading
                        btn.html(
                            `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                        ).prop('disabled', true);
                    },

                    success: function(response) {
                        // var response = JSON.parse(data);
                        // enc = response.data.enc.toString();
                        if (response != "") {
                            if (response.status == 'success') {
                                // $('#verifyBTN').html(btn);
                                // $('#formTwo').hide();
                                // $('#formThree').show();

                                // gtag('event', 'forgot_password_success', {
                                //     'event_category': 'User Engagement',
                                //     'event_label': 'Successful Password Reset',
                                //     'user_id': response.data.user_id
                                // });


                                showToast('success', response.message, 5000);


                                window.location.href = origin + '/login';

                            } else {
                                $('#UpdatePasswordBTN').html(btn);
                                // $('#otpvererr').html(`<div class="alert alert-danger" role="alert">${response.message}</div>`);
                                showToast('error', response.message, 5000);
                            }
                        }

                        // Loading Off 
                        btn.html(`UPDATE PASSWORD`).prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        showToast("error", "Request failed", 5000);
                        btn.html(`UPDATE PASSWORD`).prop('disabled', false);
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

                x = setInterval(function() {

                    // Get today's date and time

                    var now = new Date().getTime();

                    // Find the distance between now and the count down date

                    var distance = (now_1 + 60000) - now;

                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    $(`#resendBTN`).html(
                        `<p class="by-click-text1 text-center my-4 under-line">Resend OTP in ${seconds} Seconds</p>`
                    );
                    // document.getElementById("resendBTN").innerHTML = `<p class="by-click-text1 text-center my-4 under-line">Resend OTP in ${seconds} Seconds</p>`;

                    if (distance < 0) {

                        clearInterval(x);
                        $(`#resendBTN`).html(resentBTN);

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
