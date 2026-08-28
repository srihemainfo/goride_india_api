@extends('layouts.app')

@section('css')
<style>
    input.warningError {
        border: 2px solid red !important;
    }

    p.by-click-text1.text-center.my-4.under-line {
        cursor: pointer;
        display: inline;
    }

    select.warningError,
    button.btn.dropdown-toggle.btn-light.bs-placeholder.warningError {
        border: 2px solid red !important;
    }

    .pointOFFEvent {
        pointer-events: none;
    }
    
    .iti {
        width: 100%;
    }

    #message {
        display: none;
        background: #fff;
        color: #000;
        padding: 10px 13px;
        position: absolute;
        z-index: 2;
        width: fit-content;
        /* Adjust width as needed */
        border: 2px solid #bcbec0;
    }

    .form-group1 {
        position: relative;
        /* Ensure relative positioning for absolute positioning of #message */
    }


    .pass-error {
        margin-top: 9px;
        /* Adjust margin as needed */
        position: relative;
        /* Ensure relative positioning for absolute positioning of the message box */
    }

    #password-field_err {
        position: relative;
        /*bottom: -25px;*/
        /* Adjust the distance from the input field */
        left: 0;
        color: red;
        font-size: 12px;
    }

    .new-signup [type="date"] {
        background: #fff url('https://cdn1.iconfinder.com/data/icons/cc_mono_icon_set/blacks/16x16/calendar_2.png') 97% 50% no-repeat !important;
        filter: grayscale(100%);
    }

    .new-signup [type="date"]::-webkit-inner-spin-button {
        display: none;
    }

    .new-signup [type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0;
    }

    @media only screen and (max-width: 600px) {


        #message p {
            padding: 0px;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 5px;
        }
    }

    .sug-text {
        font-size: 15px;
        cursor: pointer;
        color: blue;
        text-decoration: underline;
    }

    .sug-text i {
        font-size: 24px;
        color: #f9bf00;
    }

    #message p {
        padding: 0px;
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 5px;

    }

    .valid {
        color: green;
    }

    .invalid {
        color: red;
    }

    .pass-error:after {
        content: "";
        display: block;
        border-width: 10px 19px 0;
        border-style: solid;
        border-color: #f1f1f1 transparent transparent;
        margin-left: -10px;
        position: absolute;
        bottom: -10px;
        left: 10%;
        border-top-color: transparent;
        border-bottom-color: #fff;
        top: -19px;
        bottom: auto;
        border-top-width: 0;
        border-bottom-width: 19px;
    }
    
    @media (max-width: 768px) {
        
        .page-header .page-header-shape, .page-header .container {
            display: none;
        }
        
        .page-header {
            height: 120px !important;
        }
        
    }

    .pass-error:before {
        content: "";
        display: block;
        border-width: 7px 21px 0;
        border-style: solid;
        border-color: #f1f1f1 transparent transparent;
        margin-left: -10px;
        position: absolute;
        bottom: -10px;
        left: 9%;
        border-top-color: transparent;
        border-bottom-color: #bcbec0;
        top: -22px;
        bottom: auto;
        border-top-width: 1px;
        border-bottom-width: 21px;
    }

    .dropdown.bootstrap-select.form-control {
        border: 0;
    }

    .signup-card .form-control,
    .bootstrap-select>.dropdown-toggle {
        background: #fff;
    }

    .page-header {
        height: 530px;
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
        width: 44px;
        height: 44px;
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
{{-- <section class="new-signup" id="formOne">
    <div class="container pb-5 mb-3">
        <div class="inner-hero-section1">
            <div class="container mb-4">
                <h1>REGISTER YOUR ACCOUNT</h1>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="signup-card">
                    <form id="" method="post">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="form-group mt-0">
                                    <label class="lb-text">Name <span class="mand-star">*</span>
                                        <span class="ps-text">(As per Passport/ID)</span>
                                    </label>

                                    <input type="text" class="form-control" value="" name="f_name" id="f_name"
                                        required="" oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '');"
                                        maxlength="70" placeholder="Name">
                                    <span id="f_name_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="form-group mt-0">
                                    <label class="lb-text">LAST NAME <span class="mand-star">*</span> <span
                                            class="ps-text">(As per Passport/ID)</span></label>

                                    <input type="text" class="form-control" value="" name="l_name" id="l_name"
                                        required="" oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '');"
                                        maxlength="70" placeholder="Last Name">
                                    <span id="l_name_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12">
                                <div class="form-group mt-0">
                                    <label class="lb-text">DATE OF BIRTH <span class="mand-star">*</span></label>



                                    <input type="date" name="dataOfBirth" style=""
                                        class="form-control mm-c text-uppercase" id="dataOfBirth"
                                        onchange="age_validate()" min="1900-01-01"
                                        max="<?= date('Y-m-d', strtotime('-3 years')) ?>" maxlength="10" required>
                                    <span id="dataOfBirth_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12">
                                <div class="form-group mt-0">
                                    <label class="lb-text">NATIONALITY <span class="mand-star">*</span></label>

                                    <select class="form-control selectpicker" aria-label="select nationality"
                                        data-live-search="true" placeholder="" id="nationlaity" name="nationlaity"
                                        required>
                                        <option value="">Select Nationality</option>

                                        <?php
                               foreach ($getCountryData['data']['countries'] as $key => $value) :
                               ?>
                                        <!-- <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option> -->
                                        <option value="<?= $value['name'] ?>">
                                            <?= $value['name'] ?>
                                        </option>
                                        <?php
                               endforeach;
                               ?>
                                    </select>
                                    <span id="nationlaity_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12">
                                <div class="form-group mt-0">
                                    <label class="lb-text">COUNTRY OF RESIDENCE <span class="mand-star">*</span></label>
                                    <select class="selectpicker form-control" aria-label="Select Country of Residence"
                                        data-live-search="true" placeholder="" id="livein" name="livein" required>
                                        <option value="">Select Country of Residence</option>

                                        <?php
                               foreach ($getCountryData['data']['countries'] as $key => $value) :
                               ?>
                                        <!-- <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option> -->
                                        <option value="<?= $value['name'] ?>">
                                            <?= $value['name'] ?>
                                        </option>
                                        <?php
                               endforeach;
                               ?>
                                    </select>
                                    <span id="livein_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>


                            <div class="col-12 col-md-12  mt-2">
                                <p class="mand-star12"><sup class="mand-star">*</sup> Required</p>
                            </div>
                            <div class="col-12 col-md-12 text-center  mb-lg-0 mb-3">
                                <button type="button" class="btn-primary1 cs_btn cs_style_2 mt-lg-0" id="button1"
                                    onclick="firstFormSubmit(1)">NEXT</button>
                            </div>
                        </div>
                    </form>
                </div>
                <img src="assets/img/Sign-bottom.png" class="img-fluid my-5">
            </div>
        </div>
    </div>
</section> --}}

<!-- Breadcrumb -->

<section class="page-header">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info">
            <h1>Start Your Journey with <span>GoRide!</span></h1>
            <p>Sign up today and take the first step towards a seamless ride experience.</p>
        </div>
    </div>
</section>

<section class="new-signup" id="formTwo">
    <div class="container pb-5 mb-3">
        <div class="inner-hero-section1">
            <div class="container page-header-info text-center">
                <h2 class="text-dark my-2">REGISTER YOUR <span>ACCOUNT</span></h2>
            </div>
        </div>
        <div class="row justify-content-center gap-4 gap-md-0">
            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center order-2">
                <img class="register_img" src="{{ asset('goride/img/rigister_png.png') }}">
            </div>
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center order-1">
                <div class="signup-card px-4 py-2 py-md-4">
                    <form method="post">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mt-0">
                                    <label class="lb-text text-uppercase">Name <span class="mand-star">*</span>
                                        {{-- <span class="ps-text">(As per Passport/ID)</span> --}}
                                    </label>
                                    <!-- <input type="text" class="form-control" value="" name="f_name" required=""> -->
                                    <!-- <input type="text" class="form-control" value="" name="f_name" id="f_name" required="" maxlength="70"> -->
                                    <input type="text" class="form-control" value="" name="f_name" id="f_name"
                                        required="" oninput="this.value = this.value.replace(/[^A-Za-z ]/g, '');"
                                        maxlength="70" placeholder="Name">
                                    <span id="f_name_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group mt-0">
                                    <label class="lb-text">WHATSAPP NUMBER<span class="mand-star">*</span></label><br>
                                    <input type="tel" id="phone" class="form-control w-100" placeholder="Mobile Number"
                                        oninput="mobile_number_validation($(this).val(), inputPhone, 'phone')"
                                        maxlength="15">
                                    <span id="phone_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-12">
                                <div class="form-group">
                                    <label class="lb-text">EMAIL<span class="mand-star">*</span></label>

                                    <input type="email" class="form-control" value="" name="email" id="email"
                                        required="" oninput="email_validation($(this).val(), 'email')" maxlength="70"
                                        autocomplete="off">
                                    <span id="email_err" class="spanClass" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-none">
                                <div class="form-group form-group1">
                                    <label class="lb-text">PASSWORD<span class="mand-star">*</span></label>
                                    <!-- <input id="password-field" type="password" class="form-control" name="password"> -->
                                    <div class="input-group">
                                        <input id="password-field" type="password" class="form-control"
                                            name="password-field" maxlength="15" autocomplete="off">
                                        <span class="input-group-text">
                                            <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                        </span>
                                    </div>
                                    <!--<input id="password-field" type="password" class="form-control"-->
                                    <!--    name="password-field" maxlength="15">-->
                                    <!--<i toggle="#password-field" class="bi bi-eye field-icon toggle-password bi"></i>-->
                                    <span id="password-field_err" class="spanClass" style="color: red;"></span>
                                    <p class="mand-star12 str m-0"><sup class="mand-star">*</sup> Required</p>

                                    <div class="pass-error" id="message">
                                        <p id="letter" class="invalid">-At least one lowercase letter</p>
                                        <p id="capital" class="invalid">-At least one uppercase letter</p>
                                        <p id="number" class="invalid">-At least one digit</p>
                                        <p id="special" class="invalid">-At least 1 special character
                                            (non-alphanumeric)</p>
                                        <p id="length" class="invalid">-At least 8 characters length</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 d-none">
                                <div class="form-group mt-lg-2">
                                    <label class="lb-text d-lg-block d-none">&nbsp;<span
                                            class="mand-star"></span></label>
                                    <span class="sug-text my-0 my-1" onclick="suggestPassword()">Suggest Password</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-12 text-center">
                                <button type="button" class=" btn-primary1 mt-2 cs_btn cs_style_2 mt-lg-0"
                                    id="firstBtnSub" onclick="firstFormSubmit(2, true, 0)">NEXT</button>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <p class="by-click-text-new">By clicking 'Next' You accept Go Ride Terms & Privacy
                                    Policy</p>
                            </div>
                            <div class="col-lg-11 col-md-12 my-1 mb-lg-0 mb-4 text-center">
                                <span class="by-click-text ">Already have an account? <a
                                        class="by-click-text under-line text-danger " href="login"
                                        contenteditable="false" style="cursor: pointer;"> Sign In
                                    </a>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <!--<img src="assets/img/Sign-bottom.png" class="img-fluid my-5">-->
            </div>
        </div>
    </div>
</section>

<section class="new-signup" id="formThree">
    <div class="container pb-5 mb-3">
        <div class="inner-hero-section1">
            <div class="container page-header-info text-center pt-5">
                <h2 class="text-dark">VERIFY YOUR <span>ACCOUNT</span></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                <div class="signup-card text-center p-4">
                    <form id="form_submit" method="post">
                        <h5>VERIFY YOUR MOBILE NUMBER</h5>
                        <div class="col-12 col-md-12 text-center my-3 text-center otp-text">
                            <p id="formThreeTxt">WE HAVE SENT YOU A CODE <br>TO +971 50 123 4567</p>
                            <p><i class="fa-brands fa-whatsapp"></i>&nbsp;WhatsApp</p>
                            <input type="hidden" id="fcm_token" value="">
                            <input type="hidden" id="platform" value="">
                        </div>
                        <div>
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
                            <span id="otp_err" class="spanClass" style="color: red;"></span>
                        </div>
                        <div class="col-12 col-md-12 text-center my-2">
                            <button type="button" id="verifyBTN"
                                class="btn-primary1 cs_btn cs_style_2 n-btn mt-lg-0">VERIFY OTP</button>
                        </div>
                        <div class="col-12 col-md-12 text-center mt-2" id="resendBTN"
                            onclick="$(this).toggleClass('pointOFFEvent');">
                            <p class="by-click-text1 text-center my-2 under-line" onclick="firstFormSubmit(2, true)">
                                Resend OTP</p>
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

<section class="new-signup" id="formFour">
    <div class="container pb-5 mb-3">
        <div class="inner-hero-section1">
            <div class="container page-header-info text-center pt-5">
                <h2 class="text-dark">Registration <span>Successful</span></h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 pb-4 d-flex justify-content-center align-items-center">
                <img class="enter_otp_img" src="{{ asset('goride/img/congats_banner.png') }}">
            </div>
            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                <div class="signup-card text-center p-4">
                    <form id="form_submit" method="post">
                        <h3>Congratulations!</h3>
                        <p>Thank you for joining us! <br> We're excited to have you on board.</p>
                        <div class="col-12 col-md-12 text-center my-2">
                            <a href="/pricing" class="btn-primary1 cs_btn cs_style_2 n-btn mt-lg-0">BUY NOW</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>

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

    let signUPmobile;
    var coderesult; // Declare the coderesult variable
    let enc = '';
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



    (function () {

        $(` #formThree, #formFour`).hide();



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



    // Email Validation Start 
    const email_validation = (e, id) => {
        try {
            let t = e.charAt(0);

            if (/[^A-Za-z0-9]/g.test(t)) return $('#email_err').html(
                'The Email Could not Start with Special Characters'),
                $('#email').addClass('warningError')
                // notifyToast('The Email Could not Start with Special Characters', "warn", "#" + id, "bottom")
                , !1;



            var r = /[^A-Za-z0-9@._-]/g;

            if ("" != e) {

                if (!r.test(e)) return $('#' + id).val(e.replace(/  +/g, " ").trim()), !0;

                var o = e.charAt(e.length - 1);

                return $('#' + id).val(e.replace(r, "")),
                    // notifyToast("The Special Characters are Not Allowed " + o, "warn", "#" + id, "bottom")
                    $('#email_err').html("The Special Characters are Not Allowed " + o),
                    $('#email').addClass('warningError'), !1

            }
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }

    const validateEmail = (e) => {
        try {
            return /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(e)
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }

    // Email Validation End 





    // Password Validation Start
    const validatePassword = (password, id) => {
        try {

            $('#password-field_err').html('');
            $('#password-field').removeClass('warningError');

            // Minimum length of 6 characters
            if (password.length < 8) {
                $('#password-field_err').html('Minimum length of 8 characters');
                $('#password-field').addClass('warningError');
                // notifyToast('Minimum length of 6 characters', "warn", "#" + id, "bottom");
                return false;
            }

            // At least 1 uppercase letter
            if (!/[A-Z]/.test(password)) {

                $('#password-field_err').html('At least 1 uppercase letter');
                $('#password-field').addClass('warningError');

                // notifyToast('At least 1 uppercase letter', "warn", "#" + id, "bottom");
                return false;
            }

            // At least 1 digit
            if (!/[0-9]/.test(password)) {
                // notifyToast('At least 1 digit', "warn", "#" + id, "bottom");
                $('#password-field_err').html('At least 1 digit');
                $('#password-field').addClass('warningError');
                return false;
            }

            // At least 1 special character (non-alphanumeric)
            if (!/[!@#$%^&*()\-_=+{}[\]|;:'",<.>/?]/.test(password)) {
                $('#password-field_err').html('At least 1 special character (non-alphanumeric)');
                $('#password-field').addClass('warningError');
                // notifyToast('At least 1 special character (non-alphanumeric)', "warn", "#" + id, "bottom");
                return false;
            }
            $('#message').hide();
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
            $('#password-field').val(generatePassword(8));
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    // SUGGEST password End


    // Get Full Mobile Number Start
    // function getphoneNumber(iti) {
    //    try {
    //       return iti.getNumber().replace("+", "").trim();
    //    } catch (e) {
    //       console.log('Error: ' + e.message);
    //    }
    // }
    // Get Full Mobile Number End

    // notify Toast Js Start
    // function notifyToast(message, style = "success", idClass = "", position = "right") {
    //    try {
    //       if (idClass == "") {
    //          $.notify(message, style);
    //       } else {
    //          $(idClass).notify(message, {
    //             position: position,
    //             autoHideDelay: 5000,
    //             className: style,
    //          });
    //       }
    //    } catch (e) {
    //       console.log('Error: ' + e.message);
    //    }
    // }

    const getDeviceType = () => {
        try {
            let e = "";

            return navigator.userAgent.toLowerCase().match(

                /(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/

            ) ? "TABLET" : navigator.userAgent.toLowerCase().match(

                /(mobi|ipod|phone|blackberry|opera mini|fennec|minimo|symbian|psp|nintendo ds|archos|skyfire|puffin|blazer|bolt|gobrowser|iris|maemo|semc|teashark|uzard)/

            ) ? "MOBILE" : "DESKTOP"
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }

    // notify Toast Js End



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



    const age_validate = () => {
        try {
            // $("#expirydataOfBirth_error").html("");



            let chooshedYear = parseInt(moment($("#dataOfBirth").val(), "YYYY").format('Y'));

            if (chooshedYear < 1900) {

                // errorThrow("expirydataOfBirth_error", "Invalid Year", "");
                // notifyToast('Invalid Year', "warn", "#dataOfBirth", "bottom");
                $('#dataOfBirth_err').html('Invalid Year');
                $('#dataOfBirth').addClass('warningError');

                return false;

            } else if (chooshedYear > 2200) {

                // errorThrow("expirydataOfBirth_error", "Invalid Year", "");
                // notifyToast('Invalid Year', "warn", "#dataOfBirth", "bottom");
                $('#dataOfBirth_err').html('Invalid Year');
                $('#dataOfBirth').addClass('warningError');

                return false;

            } else {

                if (calAge('dataOfBirth') < 3) {




                    $('#dataOfBirth_err').html('Participants under 3 are not allowed to register or purchase.');
                    $('#dataOfBirth').addClass('warningError');

                    return false;

                }





                return true;

            }



        } catch (e) {
            console.log('Error: ' + e.message);
        }

    }






    const calAge = (id) => {
        try {
            var date = document.getElementById(id).value;
            var diff = moment().diff(moment(date), 'years');
            return diff;
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }


    $(".toggle-password").click(function () {
        $(this).toggleClass("bi-eye  bi-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    /// first Form Submit Start
    const firstFormSubmit = (formID, resend = false, firstTime = 1) => {
        try {
            let error = 0;


            $('#f_name_err,#l_name_err,#dataOfBirth_err,#nationlaity_err,#livein_err,#phone_err,#email_err,#password-field_err')
                .html('');
            $('#f_name,#l_name,#dataOfBirth,#nationlaity, button[data-id="nationlaity"],#livein, button[data-id="livein"], #mobile_code,#email,#password-field')
                .removeClass('warningError');

            let f_name = $('#f_name').val();
            // let l_name = $('#l_name').val();
            // let dataOfBirth = $('#dataOfBirth').val();
            // let nationlaity = $('#nationlaity').val();
            // let livein = $('#livein').val();

            if (f_name.length < 1 || f_name == null || f_name == undefined || f_name == 'null' || f_name.trim() ==
                '') {
                // notifyToast('Kindly enter Name', "error", "#f_name", "bottom");
                $('#f_name_err').html('Name is required');
                $('#f_name').addClass('warningError');
                error++;
            }

            // if (l_name.length < 1 || l_name == null || l_name == undefined || l_name == 'null' || l_name.trim() ==
            //     '') {
            //     // notifyToast('Kindly enter last name', "error", "#l_name", "bottom");
            //     $('#l_name_err').html('Last Name is required');
            //     $('#l_name').addClass('warningError');
            //     error++;
            // }

            // if (dataOfBirth == null || dataOfBirth == undefined || dataOfBirth == 'null' || dataOfBirth.trim() ==
            //     '') {
            //     // notifyToast('Kindly select data Of Birth', "error", "#dataOfBirth", "bottom");
            //     $('#dataOfBirth_err').html('Date Of Birth is required');
            //     $('#dataOfBirth').addClass('warningError');
            //     error++;
            // }

            // if (nationlaity.length < 1 || nationlaity == null || nationlaity == undefined || nationlaity ==
            //     'null' || nationlaity.trim() == '') {
            //     // notifyToast('Kindly select nationlaity', "error", "#nationlaity", "bottom");
            //     $('#nationlaity_err').html('Nationality is required');
            //     $('#nationlaity, button[data-id="nationlaity"]').addClass('warningError');
            //     error++;
            // }

            // if (livein.length < 1 || livein == null || livein == undefined || livein == 'null' || livein.trim() ==
            //     '') {
            //     // notifyToast('Kindly select live in', "error", "#livein", "bottom");
            //     $('#livein_err').html('Country of residence is required');
            //     $('#livein, button[data-id="livein"]').addClass('warningError');
            //     error++;
            // }

            if (error > 0) {
                return false;
            }

            // if (calAge('dataOfBirth') < 3) {

            //     $('#dataOfBirth_err').html('Participants under 3 are not allowed to register or purchase.');
            //     $('#dataOfBirth').addClass('warningError');
            //     return false;
            // }

            // if (!age_validate()) {

            //     $('#dataOfBirth_err').html('Participants under 3 are not allowed to register or purchase.');
            //     $('#dataOfBirth').addClass('warningError');
            //     return false;
            // }

            if (formID === 1) {
                $('#formOne').hide();
                $('#formTwo').show();

            } else if (formID === 2) {
                let mobileNumber = getCountryCodeAndNumber(inputPhone);
                let dialCode = inputPhone.getSelectedCountryData().dialCode;
                let email = $("#email").val();
                // let password = $('#password-field').val();
                let password = '';


                if (mobileNumber.length < 1 || mobileNumber == null || mobileNumber == undefined || mobileNumber ==
                    'null' || mobileNumber.trim() == '' || $("#phone").val() == '') {
                    // notifyToast('Kindly enter mobile no', "error", "#mobile_code", "bottom");
                    $('#phone_err').html('Mobile Number is required');
                    $('#phone').css('border', '2px solid red');
                    error++;
                }

                if (email.length < 1 || email == null || email == undefined || email == 'null' || email.trim() == '') {
                    $('#email_err').html('Email is required');
                    $('#email').addClass('warningError');
                    error++;
                }

                // if (password.length < 1 || password == null || password == undefined || password == 'null' ||
                //     password.trim() == '') {
                //     // notifyToast('Kindly enter password', "error", "#password-field", "bottom");
                //     $('#password-field_err').html('Password is required');
                //     $('#password-field').addClass('warningError');
                //     error++;
                // }

                if (error > 0) {
                    return false;
                }

                if (0 == $("#phone").val().charAt(0)) {
                    // notifyToast('Remove "Zero" at beginning', "error", "#mobile_code", "bottom");
                    $('#phone_err').html('Remove "Zero" at beginning');
                    $('#phone').addClass('warningError');
                    return false;
                }

                if (!inputPhone.isValidNumber() || !inputPhone.isValidNumberPrecise()) {
                    $('#phone_err').html('Incorrect Mobile Number');
                    $('#phone').addClass('warningError');
                    // notifyToast('Incorrect Mobile Number', "error", "#mobile_code", "bottom");
                    return false;
                }


                if (!validateEmail(email)) {
                    // notifyToast('Invaild Email ID', "error", "#email", "bottom");
                    $('#email_err').html('Invaild Email ID');
                    $('#email').addClass('warningError');
                    return false;
                }


                // if (!validatePassword(password)) {

                //     $('#password-field_err').html('Kindly enter strong password');
                //     $('#password-field').addClass('warningError');

                //     // notifyToast('Kindly enter strong password', "error", "#password-field", "bottom");
                //     return false;
                // }

                var h = new FormData();
                h.append('first_name', f_name);
                // h.append('last_name', l_name);
                h.append('mobile', mobileNumber);
                // h.append('dataOfBirth', dataOfBirth)
                h.append('dialCode', dialCode);
                h.append('email', email);
                h.append('password', password);
                h.append('deviceType', getDeviceType());
                // h.append('nationality', nationlaity);
                // h.append('livein', livein);
                h.append('isResend', (firstTime === 0) ? false : resend);

                var btn = $('#firstBtnSub');
                // $('#firstBtnSub').html(`<button class="btn btn-primary1 mt-2  mt-lg-0" type="button" disabled>
                //                         <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                //                         Loading...
                //                         </button>`);


                if (resend) {
                    $(`#resendPbtn`).attr('disabled', 'disabled');
                }


                $.ajax({
                    // url: url,
                    url: origin + '/api/signup',
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

                                // $('#firstBtnSub').html(btn);
                                $('#formTwo').hide();
                                $('#formThreeTxt').text(
                                    `Enter the OTP code sent to your mobile number +${mobileNumber}`
                                );
                                // $('#verifyBTN').html(`<button type="button" class="btn btn-primary1 n-btn mt-lg-0" onclick="verifyUPdate(${mobileNumber}, ${dialCode})">REGISTER MY ACCOUNT</button>`);
                                $('#verifyBTN').attr(`onclick`,
                                    `verifyUPdate(${mobileNumber}, ${dialCode})`);
                                $('#formThree').show();

                                // if (trim) {
                                trim_seconds();
                                // }


                                showToast('success', response.message, 5000);


                            } else {
                                // $('#firstBtnSub').html(btn);
                                // errorThrow("phone_error", response.message, "phone");
                                $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                                showToast('error', response.message, 5000);
                            }
                        }

                        // Loading Off 

                        btn.html(`NEXT`).prop('disabled', false);
                    },
                    error: function (xhr, status, error) {

                        showToast("error", "Request failed", 5000);

                        btn.html(`NEXT`).prop('disabled', false);

                        console.error('Request failed');

                        console.error(xhr, status, error);

                    },

                    processData: false,
                    contentType: false
                });
            }


        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }



    const verifyUPdate = (mobile, dialCode) => {
        var user = '';
        $('#otp_err').html('');
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
        h.append('fcm_token', $('#fcm_token').val()??null)
        h.append('platform_type', $('#platform').val()??null)
        h.append('browser_fcm_token', $('#browser_fcm_token').val()??null)
        
        const countryCODE = getCookie('countryCode');

        $.ajax({
            url: origin + '/api/verifyOTPsign',
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
                        $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').val('');

                        showToast('success', response.message, 5000);

                        setCookie("sessionToken", response.token, 3);
                        localStorage.setItem('authToken', response.token);

                        setCookie("cusid", response.data.user_id, 3);
                        setCookie("name", response.data.name, 3);
                        deleteCookie('transaction_id');
                        deleteCookie('finaltotal');
                        if (getCookie("sessionToken") && getCookie("sessionToken") != undefined &&
                            getCookie("sessionToken") != null && getCookie("cusid") != undefined &&
                            getCookie("cusid") != null && getCookie("cusid") != '') {

                            // gtag('event', 'sign_up_success', {
                            //     'event_category': 'User Engagement',
                            //     'event_label': 'Successful Sign Up',
                            //     'user_id': getCookie("cusid")
                            // });
                            
                            gtag('event', 'conversion', {'send_to': 'AW-1007637710/aeKQCNf5_oQYEM6pveAD'});

                            if (getCookie('newFormCartData') && getCookie("newFormCartData") !=
                                undefined && getCookie("newFormCartData") != null) {
                                window.location.replace(origin + '/cart');
                            }

                            $('#formThree').hide();
                            
                            if (countryCODE == 'IN') {
                                window.location = '/jobs';
                            } else {
                                $('#formFour').show();
                            }

                        } else {
                            showToast('warning', 'Login Failed!', 5000);
                        }
                    } else {
                        // $('#verifyBTN').html(btn);
                        $('#otp1,#otp2,#otp3,#otp4,#otp5,#otp6').addClass('warningError');
                        // $('#otpvererr').html(`<div class="alert alert-danger" role="alert">${response.message}</div>`);
                        // toast('error', response.message);
                        $('#otp_err').html(response.message);
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
                // document.getElementById("resendBTN").innerHTML = `<p class="by-click-text1 text-center my-4 under-line">${seconds}s</p>`;

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




    var myInput = document.getElementById("password-field");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");
    var special = document.getElementById("special");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function () {
        document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function () {
        document.getElementById("message").style.display = "none";
    }

    // When the user starts to type something inside the password field
    myInput.oninput = function () {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
        }

        // Validate numbers
        // Validate special characters
        var specialc = /[!@#$%^&*()\-_=+{}[\]|;:'",<.>/?]/g;
        if (myInput.value.match(specialc)) {
            special.classList.remove("invalid");
            special.classList.add("valid");
        } else {
            special.classList.remove("valid");
            special.classList.add("invalid");
        }


        // Validate length
        if (myInput.value.length >= 8) {
            length.classList.remove("invalid");
            length.classList.add("valid");
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
        }
        
        
        if (
            letter.classList.contains("valid") &&
            capital.classList.contains("valid") &&
            special.classList.contains("valid") &&
            length.classList.contains("valid") &&
            number.classList.contains("valid")
        ) {
            document.getElementById("message").style.display = "none";
        } else {
            document.getElementById("message").style.display = "block";
        }
        
    }
</script>
@endsection