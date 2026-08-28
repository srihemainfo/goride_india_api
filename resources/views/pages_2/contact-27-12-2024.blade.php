@extends('layouts.app')

@section('css')
<style>
    .iti {
        /* position: relative; */
        display: block;
    }
</style>
@endsection


@section('content')

<!-- Breadcrumb -->

<section class="page-header">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info">
            <h4>Contact Us</h4>
            <h1>We’re Here to Help! <br> Reach out to <span>GoRide!</span></h1>
            <p>Have any questions or need assistance? <br>Our team is ready to support your business!</p>
        </div>
    </div>
</section>

<!--<section class="map-wrapper">-->
<!--    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d686.9119105268126!2d80.23319907125429!3d13.114349382565724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a5265b2a14f8f63%3A0xc2c5b68a8e7e4985!2sSri%20Hema%20Infotech!5e0!3m2!1sen!2sin!4v1725512101127!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>-->
<!--</section>-->

<section class="contact-section bd-bottom padding">
    <div class="map"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="contact-details-wrap">
                    <div class="contact-title">
                        <h2>We're Here to Help <span>You Succeed</span></h2>
                        <p>Have questions about our AI dispatch software? Want to learn more about how GoRide can
                            transform your transportation business? Contact us today, and our team of experts will be
                            happy to assist you.</p>
                    </div>
                    <ul class="contact-details">
                        <!--<li><a href="tel:+16473661867" title="For Canada Only"><i class="fa-brands fa-canadian-maple-leaf"></i></a>+1 647 3661867</li>-->
                        <li><a href="tel:+919884557004" title="International"><i
                                    class="fa-solid fa-earth-asia"></i>+919884557004</a></li>
                        <li><a href="mailto:support@goride.run"><i class="fas fa-envelope"></i>support@goride.run</a>
                        </li>

                        <li><a href="javascript:void(0);">

                                <i class="fas fa-address-book"></i>
                            </a>Sri Hema Infotech </br>
                            No: 1A,2nd Floor,
                            Paper Mills Road, Gopal Colony,</br>
                            Perambur, Chennai - 600 082.</br>
                            Tamilnadu, India.</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-form">


                    <form class="form-horizontal">
                        <div class="contact-title">
                            <h2>Contact With Us! <span></span></h2>
                        </div>
                        <div class="contact-form-group">


                            <div class="form-field">
                                <div>
                                    <input type="text" id="name" name="user_name" placeholder="Full Name"
                                        class="form-control m-0">
                                </div>

                                <span id="f_name_err" class="spanClass" style="color: red;"></span>
                            </div>



                            <div class="form-field">
                                <!-- <label for="email" class="form-label"><span>Email <span
                                            class="required-star">*</span></span></label> -->
                                <div>
                                    <input type="email" class="form-control m-0" value="" name="email" id="email"
                                        required="" oninput="email_validation($(this).val(), 'email')" maxlength="70"
                                        autocomplete="off" placeholder="Email">
                                </div>
                                <span id="email_err" class="spanClass" style="color: red;"></span>
                            </div>
                            <div class="form-field">
                                <!-- <label for="phone" class="form-label"><span>Mobile <span
                                            class="required-star">*</span></span></label> -->
                                <div>
                                    <input type="tel" id="phone" class="form-control m-0 w-100" placeholder="Phone Number"
                                        oninput="mobile_number_validation($(this).val(), inputPhone, 'phone')"
                                        maxlength="15">
                                </div>
                                <span id="phone_err" class="spanClass" style="color: red;"></span>
                            </div>
                            <div class="form-field">
                                <!-- <label for="sub1" class="form-label"><span>Subject <span
                                            class="required-star">*</span></span></label> -->
                                <div>
                                    <input type="text" id="sub1" name="sub1" class="form-control m-0" placeholder="Subject">
                                </div>
                                <span id="sub_err" class="spanClass" style="color: red;"></span>
                            </div>
                            

                        </div>
                        <div class="form-field">
                                <input type="hidden" id="googleResponse" name="google_response">
                                <!-- <label for="message" class="form-label"><span>Message <span
                                            class="required-star">*</span></span></label> -->
                                <!-- <input type="text" id="message" name="message" class="form-control"> -->
                                <div>
                                    <textarea id="message" placeholder="Message" style="
                                        height: auto;
    resize: unset;" class="form-control m-0" cols="30" rows="4"></textarea>
                                </div>
                                <span id="message_err" class="spanClass" style="color: red;"></span>
                            </div>
                            <div class="form-field">
                                <button type="button" class="g-recaptcha cs_btn cs_style_2" id="capcha"
                                    data-sitekey="{{env('RECAPTCHA_SITEKEY')}}" data-callback='onSubmit'
                                    data-action='submit' onclick="firstFormSubmit()">Send Message</button>
                            </div>
                    </form>

                    <!-- <form action="#" method="post" class="form-horizontal">
                        <div class="contact-title">
                            <h2>Contact With Us! <span></span></h2>
                        </div>
                        <div class="contact-form-group">
                            <div class="form-field">
                                <input type="text" id="firstname" name="firstname" class="form-control"
                                    placeholder="First Name" required="">
                            </div>
                            <div class="form-field">
                                <input type="text" id="lastname" name="lastname" class="form-control"
                                    placeholder="Last Name" required="">
                            </div>
                            <div class="form-field">
                                <input type="email" id="email" name="email" class="form-control" placeholder="Email"
                                    required="">
                            </div>
                            <div class="form-field">
                                <input type="text" id="phone" name="phone" class="form-control"
                                    placeholder="Phone Number" required="">
                            </div>
                            <div class="form-field message">
                                <textarea id="message" name="message" cols="30" rows="4" class="form-control"
                                    placeholder="Message" required=""></textarea>
                            </div>
                            <div class="form-field">
                                <button id="submit" class="cs_btn cs_style_2" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form> -->
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
    integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://www.google.com/recaptcha/api.js?render={{env('RECAPTCHA_SITEKEY')}}"></script>

<script>
    var wow = new WOW({
        boxClass: 'wow',      // animated element css class (default is wow)
        animateClass: 'animated', // animation css class (default is animated)
        offset: 0,          // distance to the element when triggering the animation (default is 0)
        mobile: true,       // trigger animations on mobile devices (default is true)
        live: true,       // act on asynchronously loaded content (default is true)
        callback: function (box) {
            // the callback is fired every time an animation is started
            // the argument that is passed in is the DOM node being animated
        },
        scrollContainer: null // optional scroll container selector, otherwise use window
    }
    );
    wow.init();


    //////////////////// NEW /////////////////////////
    function onSubmit(token) {
        document.getElementById("firstFormSubmit").submit();
        console.log('Captcha response:', token);
    }
    //   document.addEventListener("DOMContentLoaded", function() {
    //   const input = document.querySelector("#phone");
    //   window.intlTelInput(input, {
    //      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.5/build/js/utils.js",
    //      initialCountry: "ae" // Replace "us" with the ISO 3166-1 alpha-2 country code of your desired default country
    //   });
    //   });
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
    const mobile_number_validation = (e, iti, id) => {
        try {
            var t = /[^0-9 ]/g;
            if ($('#phone_err').html(''),
                $('#phone').css('border', '1px solid #18204F'), "" != e) {
                if (0 != parseInt(e.charAt(0))) {
                    if (t.test(e)) e.charAt(e.length - 1), $('#' + id).val(e.replace(t, "")),
                        $('#phone_err').html('The Characters are Not Allowed'),
                        $('#phone').css('border', '2px solid red');
                    else {
                    }
                } else $('#' + id).val(""),
                    $('#phone_err').html('Mobile Number Should Not Start With Zero'),
                    $('#phone').css('border', '2px solid red')
            } else $('#phone_err').html('Mobile Number field is required'),
                $('#phone').css('border', '2px solid red')
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    const email_validation = (e, id) => {
        try {
            let t = e.charAt(0);
            if (/[^A-Za-z0-9]/g.test(t)) return $('#email_err').html('The Email Could not Start with Special Characters'),
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
    const sub_validation = (e, id) => {
        try {
            let t = e.charAt(0);
            var r = /[^A-Za-z0-9._\s"'-]/g;
            if ("" != e) {
                if (!r.test(e)) {
                    // Replace multiple spaces with a single space and trim the result
                    let cleanedValue = e.replace(/\s+/g, ' ').trim();
                    $('#' + id).val(cleanedValue);
                    return true;
                } else {
                    var o = e.charAt(e.length - 1);
                    $('#' + id).val(e.replace(r, ""));
                    $('#sub_err').html("The Special Characters are Not Allowed " + o);
                    $('#sub1').addClass('warningError');
                    return false;
                }
            }
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    const message_validation = (e, id) => {
        try {
            let t = e.charAt(0);
            var r = /[^A-Za-z0-9._\s"'-]/g;
            if ("" != e) {
                if (!r.test(e)) return $('#' + id).val(e.replace(/  +/g, " ").trim()), !0;
                var o = e.charAt(e.length - 1);
                return $('#' + id).val(e.replace(r, "")),
                    // notifyToast("The Special Characters are Not Allowed " + o, "warn", "#" + id, "bottom")
                    $('#message_err').html("The Special Characters are Not Allowed " + o),
                    $('#message').addClass('warningError'), !1
            }
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    const firstFormSubmit = () => {
        try {
            // event.preventDefault();
            let error = 0;
            $('#f_name_err,#phone_err,#email_err,#sub_err,#message_err').html('');
            $('#f_name, #mobile_code,#email,#sub1,#message').removeClass('warningError');
            $('#name').css('border', '1px solid #18204F');
            $('#email').css('border', '1px solid #18204F');
            $('#message').css('border', '1px solid #18204F');
            $('#sub1').css('border', '1px solid #18204F');
            let f_name = $('#name').val();
            let sub1 = $('#sub1').val();
            let message = $('#message').val();
            let mobileNumber = getCountryCodeAndNumber(inputPhone);
            let dialCode = inputPhone.getSelectedCountryData().dialCode;
            let email = $("#email").val();
            let password = $('#password-field').val();
            const btn = $('#capcha');
            if (f_name.length < 1 || f_name == null || f_name == undefined || f_name == 'null' || f_name.trim() == '') {
                $('#f_name_err').html('Name is required');
                $('#f_name').addClass('warningError');
                $('#name').css('border', '2px solid red');
                error++;
            }
            if (email.length < 1 || email == null || email == undefined || email == 'null' || email.trim() == '') {
                $('#email_err').html('Email is required');
                $('#email').addClass('warningError');
                $('#email').css('border', '2px solid red');
                error++;
            }
            if (email != '' && !validateEmail(email)) {
                $('#email_err').html('Invalid Email ID');
                $('#email').addClass('warningError');
                $('#email').css('border', '2px solid red');
                return false;
            }
            if (sub1.length < 1 || sub1 == null || sub1 == undefined || sub1 == 'null' || sub1.trim() == '') {
                $('#sub_err').html('Subject is required');
                $('#sub1').addClass('warningError');
                $('#sub1').css('border', '2px solid red');
                error++;
            }
            if (message.length < 1 || message == null || message == undefined || message == 'null' || message.trim() == '') {
                $('#message_err').html('Message is required');
                $('#message').addClass('warningError');
                $('#message').css('border', '2px solid red');
                error++;
            }
            if (mobileNumber.length < 1 || mobileNumber == null || mobileNumber == undefined || mobileNumber == 'null' || mobileNumber.trim() == '' || $("#phone").val() == '') {
                $('#phone_err').html('Mobile Number is required');
                $('#phone').css('border', '2px solid red');
                error++;
            }
            if (!inputPhone.isValidNumber() || !inputPhone.isValidNumberPrecise()) {
                $('#phone_err').html('Invalid Mobile Number!');
                $('#phone').css('border', '2px solid red');
                return false;
            }
            if (error > 0) {
                return false;
            }
            // $('form').submit(function (event) {
            // event.preventDefault(); 
            grecaptcha.ready(function () {
                grecaptcha.execute($(`#capcha`).data(`sitekey`), { action: 'submit' }).then(function (token) {
                    //                console.log(token);
                    // return false;

                    var formData = new FormData();
                    formData.append('name', $('#name').val());
                    formData.append('email', $('#email').val());
                    formData.append('mobile', $('#phone').val());
                    formData.append('subject', $('#sub1').val());
                    formData.append('message', $('#message').val());
                    formData.append('google_response', token);

                    // var formData = {
                    //     name: $('#name').val(),
                    //     email: $('#email').val(),
                    //     mobile: $('#phone').val(),
                    //     subject: $('#sub1').val(),
                    //     message: $('#message').val(),
                    //     google_response: token
                    // };
                    // Send AJAX request
                    $.ajax({
                        type: 'POST',
                        // url: API_DOMAIN + 'contact',
                        url: origin + '/api/contact',
                        data: formData,
                        headers: {

                            // "Accept": "application/json; charset=utf-8",

                            // "Content-Type": "application/json; charset=utf-8",

                            "Authorization": 'Bearer ' + getCookie("sessionToken"),
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function () {
                            // Button Loading
                            btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);
                        },
                        success: function (response) {
                            if (response.error && response.error === 'validation_error') {
                                let errorMessage = '';
                                Object.keys(response.message).forEach(key => {
                                    errorMessage += `${key}: ${response.message[key].join(', ')}\n`;
                                });
                                // swal.fire({
                                //    title: 'Validation Error',
                                //    text: errorMessage,
                                //    icon: 'error',
                                // });
                                showToast("error", errorMessage, 5000);
                            } else if (response.status === 'success') {
                                // swal.fire({
                                //    title: 'Success',
                                //    text: 'Email send successful',
                                //    icon: 'success',
                                //    timer: 3000, // 3 seconds
                                //    showConfirmButton: false
                                // });
                                showToast("success", "Email send successfully", 5000, refresh);
                                // window.location.reload();
                            } else {
                                // swal.fire({
                                //    title: 'Error',
                                //    text: 'Failed to send email',
                                //    icon: 'error',
                                // });
                                showToast("error", "Failed to send email", 5000, refresh);
                                // location.reload();
                            }
                            // window.location.reload();
                            // Loading Off 
                            btn.html(`Send Message`).prop('disabled', false);
                        },
                        processData: false,
                        contentType: false,
                        error: function (xhr, status, error) {
                            showToast("error", "Request failed", 5000);
                            btn.html(`Send Message`).prop('disabled', false);
                            console.error('Request failed');
                            console.error(xhr, status, error);
                        }
                    });
                });
            });
            // });
            // grecaptcha.ready(function () {
            //    grecaptcha.execute('6LfcVpYqAAAAAP3YO5Mk1wfJzwLEFzvsCb6GXzT8', { action: 'submit' }).then(function (token) {
            //       // Get form data
            //       var formData = {
            //          name: $('#name').val(),
            //          email: $('#email').val(),
            //          mobile: mobileNumber,
            //          subject: $('#sub1').val(),
            //          message: $('#message').val(),
            //          google_response: token
            //       };
            //       console.log(formData);
            //       // Send AJAX request
            //       $.ajax({
            //          type: 'POST',
            //          url: API_DOMAIN + 'contact',
            //          data: formData,
            //          success: function (response) {
            //             if (response.error && response.error === 'validation_error') {
            //                let errorMessage = '';
            //                Object.keys(response.message).forEach(key => {
            //                   errorMessage += `${key}: ${response.message[key].join(', ')}\n`;
            //                });
            //                swal.fire({
            //                   title: 'Validation Error',
            //                   text: errorMessage,
            //                   icon: 'error',
            //                });
            //             } else if (response.status === 'success') {
            //                showToast('success', 'Email sent successfully', 2000);
            //                window.location.reload();
            //             } else {
            //                swal.fire({
            //                   title: 'Error',
            //                   text: 'Failed to send email',
            //                   icon: 'error',
            //                });
            //             }
            //          },
            //          error: function (xhr, status, error) {
            //             swal.fire({
            //                title: 'Error',
            //                text: 'Failed to send email',
            //                icon: 'error',
            //             });
            //          }
            //       });
            //    });
            // });
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    const refresh = () => {
        location.reload();
    }
    // $(document).ready(function () {
    //    $('form').submit(function (event) {
    //       event.preventDefault();
    //       var formData = {
    //          name: $('#name').val(),
    //          email: $('#email').val(),
    //          mobile: $('#phone').val(),
    //          subject: $('#sub1').val(),
    //          message: $('#message').val(),
    //          google_response: $('#googleResponse').val()
    //       };
    //       // Send AJAX request
    //       $.ajax({
    //          type: 'POST',
    //          url: API_DOMAIN + 'contact',
    //          data: formData,
    //          headers: {
    //             "Accept": "application/json",
    //             "Content-Type": "application/json",
    //             "Authorization": 'Bearer ' + token
    //          },
    //          success: function (response) {
    //             if (response.error && response.error === 'validation_error') {
    //                let errorMessage = '';
    //                Object.keys(response.message).forEach(key => {
    //                   errorMessage += `${key}: ${response.message[key].join(', ')}\n`;
    //                });
    //                swal.fire({
    //                   title: 'Validation Error',
    //                   text: errorMessage,
    //                   icon: 'error',
    //                });
    //             } else if (response.status === 'success') {
    //                swal.fire({
    //                   title: 'Success',
    //                   text: 'Email send successful',
    //                   icon: 'success',
    //                   timer: 3000, // 3 seconds
    //                   showConfirmButton: false
    //                });
    //                window.location.reload();
    //             } else {
    //                swal.fire({
    //                   title: 'Error',
    //                   text: 'Failed to send email',
    //                   icon: 'error',
    //                });
    //             }
    //             window.location.reload();
    //          },
    //          error: function (xhr, status, error) {
    //             swal.fire({
    //                title: 'Error',
    //                text: 'Failed to send email',
    //                icon: 'error',
    //             });
    //          }
    //       });
    //    });
    // });
</script>
@endsection