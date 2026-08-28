@extends('dashboard-layout.index')

@section('content')

<style>
    .nav-tabs {

        border: none;

    }

    textarea {
        resize: unset;
    }

    .dropdown-list {

        max-height: 200px;

        overflow-y: auto;

        border: 1px solid #ccc;

        display: none;

        position: absolute;

        background-color: white;

        width: 93%;

        z-index: 1000;

    }

    .dropdown-list.active {

        display: block;

    }

    .dropdown-item {

        padding: 8px;

        cursor: pointer;

    }

    .dropdown-item:hover {

        background-color: #f0f0f0;

    }

    [id="dropdown"] {

        width: 94% !important;

    }

    .form-select {



        color: #000;

    }

    .arrow-none {

        background-image: none !important;

    }

    .form-control {

        color: #000;

    }



    .nav-tabs .nav-link:hover {

        background-color: #747474 !important;

        color: white !important;

    }

    .nav-link.active {

        background-color: #fff !important;

        color: #343a40 !important;

    }



    .nav-link:hover {

        background-color: #6c757d !important;

    }

    .card {
        border-radius: 10px;
    }
</style>


<link rel="stylesheet" href="{{ asset('assets/css/rte_theme_default.css') }}" />



<div class="col-12">

    <div class="right_col" role="main">
        <div class="card">
            <div class="card-head">
                <h3 class="p-2">Create New Article</h3>
            </div>
            <div class="card-body">
                <!-- <form action="#" method="POST"> -->
                <!-- URL Field -->
                <div class="form-group">
                    <label for="url">Article URL</label><span style="color:red;">&nbsp;*</span>
                    <input type="url" class="form-control" id="url" name="url" placeholder="Enter article URL"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_ ]/g, '');" maxlength="70" required>
                </div>

                <!-- Meta Title Field -->
                <div class="form-group">
                    <label for="meta-title">Meta Title</label><span style="color:red;">&nbsp;*</span>
                    <input type="text" class="form-control" id="meta-title" name="meta-title"
                        placeholder="Enter meta title"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="150" required>
                </div>

                <!-- Meta Description Field -->
                <div class="form-group">
                    <label for="meta-description">Meta Description</label><span style="color:red;">&nbsp;*</span>
                    <textarea class="form-control" id="meta-description" name="meta-description" rows="4"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="500"
                        placeholder="Enter meta description" required></textarea>
                </div>


                <div class="form-group">
                    <label for="meta-keyword">Meta Keyword</label><span style="color:red;">&nbsp;*</span>
                    <textarea class="form-control" id="meta-keyword" name="meta-keyword" rows="4"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="500"
                        placeholder="Enter meta keyword" required></textarea>
                </div>


                <!-- Content Summary Field -->
                <div class="form-group">
                    <label for="content-summary">Content Summary</label><span style="color:red;">&nbsp;*</span>
                    <!-- <textarea class="form-control" id="content-summary" name="content-summary" rows="5"
                            placeholder="Enter content summary" required></textarea> -->


                    <div id="content-summary">

                    </div>

                </div>

                <!-- Submit Button -->
                <button type="submit" id="firstBtnSub" class="btn btn-primary" onclick="saveContent()">Save
                    Content</button>
                <!-- <button type="submit" class="btn btn-primary">Create Article</button>
                      -->
                <!-- </form> -->
            </div>
        </div>



        <!-- <div class="x_panel">

            <div class="x_title">

                <h2>Article Page</h2>

            </div>







            <div class="x_content">

                <div class="x_content-container">

                    <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>

                        <div class="row mb-3">

                            <label for="" class="col-form-label col-md-5 col-sm-5">Operating country:</label>

                            <div class="col-md-7 col-sm-7">

                                <input class="form-control " type="text" name="country" id="country"
                                    placeholder="Enter Country (India)" autocomplete="off">

                                <div id="dropdown" class="dropdown-list form-select arrow-none"></div>

                            </div>

                        </div>







                        <div class="row mb-3">

                            <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>

                            <div class="col-md-7 col-sm-7">

                                <input class="form-control " type="text" name="currency" id="currency"
                                    placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off">

                                <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>

                            </div>

                        </div>



                        <div class="row mb-3">

                            <label class="col-form-label col-md-5 col-sm-5">Distance unit</label>

                            <div class="col-md-7 col-sm-7">

                                <select name="distance_unit" id="distance_unit" class="form-select" required>

                                    <option value="miles">Miles</option>

                                    <option value="kms">Kms</option>

                                </select>

                            </div>

                        </div>



                        ?>



                        <div class="row">

                            <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum</label>

                            <div class="col-md-4 col-sm-4 col-6 mb-3">

                                <select name="advance_booking_minium_type" id="advance_booking_minium_type"
                                    class="form-select" required>

                                    <option value="minutes">Minutes</option>

                                    <option value="hours">Hours</option>

                                </select>

                            </div>

                            <div class="col-md-3 col-sm-3 col-6 mb-3">

                                <select name="advance_booking_minium" id="advance_booking_minium" class="form-select"
                                    required>

                                </select>



                            </div>

                        </div>

                        <div class="row ">

                            <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum</label>

                            <div class="col-md-4 col-sm-4 col-6 mb-3">

                                <select name="advance_booking_maximum_type" id="advance_booking_maximum_type"
                                    class="form-select" required>

                                    <option value="days">Days</option>

                                    <option value="months">Months</option>

                                    <option value="years">Years</option>

                                </select>

                            </div>

                            <div class="col-md-3 col-sm-3 col-6 mb-3">

                                <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select"
                                    required>

                                </select>



                                <input type="hidden" id="bokingsettingid" name="bokingsettingid">

                            </div>

                        </div>



                        <div class="text-center">

                            <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">UPDATE</button>

                        </div>

                    </form>

                </div>

            </div>

        </div> -->

    </div>

</div>





<!-- 

<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

    <div class="nav flex-column nav-tabs nav-tabs-right" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">







        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab"
            aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

        </a>



        <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

        </a>



        <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-plus" style="margin-right: 8px;"></i> Email Template

        </a>



        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

        </a>

        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date

        </a>

        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

            <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

        </a>

        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-star" style="margin-right: 8px;"></i> Review

        </a>

    </div>

</div> -->



<script type="text/javascript" src="{{ asset('assets/js/rte.js') }}"></script>
<!-- <script>RTE_DefaultConfig.url_base = 'richtexteditor'</script> -->
<script type="text/javascript" src='{{ asset('assets/js/all_plugins.js') }}'></script>



<!-- .js -->
<script>
    var editor1 = new RichTextEditor('#content-summary'

        // , 
        // {

        //     fileUploadUrl: '/upload',
        //     onFileUpload: function (file) {

        //         var formData = new FormData();
        //         formData.append('file', file);

        //         fetch('/upload', {
        //             method: 'POST',
        //             body: formData
        //         }).then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {

        //                     editor1.insertImage(data.fileUrl);
        //                 } else {
        //                     alert("Upload failed!");
        //                 }
        //             }).catch(err => {
        //                 console.error('Error during upload:', err);
        //                 alert('File upload failed.');
        //             });
        //     }
        // }


    );


    // Save content to the server
    const saveContent = () => {
        try {

            var url = $(`#url`).val();
            var title = $(`#meta-title`).val();
            var description = $(`#meta-description`).val();
            var keyword = $(`#meta-keyword`).val();
            var content = editor1.getHTMLCode();
            var btn = $('#firstBtnSub');

            if (url.length < 1 || url == null || url == undefined || url == 'null' || url.trim() == '') {
                showToast('error', 'URL is required!', 5000);
                return false;
            }

            if (title.length < 1 || title == null || title == undefined || title == 'null' || title.trim() == '') {
                showToast('error', 'Meta Title is required!', 5000);
                return false;
            }



            if (description.length < 1 || description == null || description == undefined || description == 'null' || description.trim() == '') {
                showToast('error', 'Meta description is required!', 5000);
                return false;
            }



            if (keyword.length < 1 || keyword == null || keyword == undefined || keyword == 'null' || keyword.trim() == '') {
                showToast('error', 'Meta keyword is required!', 5000);
                return false;
            }




            if (content.length < 1 || content == null || content == undefined || content == 'null' || content.trim() == '') {
                showToast('error', 'Content is required!', 5000);
                return false;
            }






            // var keyword = $(`#meta-keyword`).val();


            // console.log(content);
            var h = new FormData();
            // var h = formDataObject;

            // Append the data to the FormData object
            // h.append('_token', '{{ csrf_token() }}');
            h.append('content_summary', content);
            h.append('url', url);
            h.append('title', title);
            h.append('description', description);
            h.append('keyword', keyword);
            h.append('token', getCookie('d_token'));
            // h.append('device_id', device_id ?? 0);
            // formDataObject[''] = token;
            // formDataObject['device_id'] = device_id;



            $.ajax({
                // url: url,
                url: '/save-article',
                type: 'POST',
                data: h,
                beforeSend: function () {

                    // Button Loading

                    btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);

                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 'success') {
                            // enc = response.data.enc.toString();


                            // if (firebaseEnable != null && firebaseEnable != '' && firebaseEnable === 'YES') {

                            //     // console.log(mobileNumber);
                            //     firebase.auth().signInWithPhoneNumber('+' + mobileNumber, window.recaptchaVerifier).then(
                            //         function (confirmationResult) {
                            //             window.confirmationResult = confirmationResult;
                            //             coderesult = confirmationResult;





                            //             $('#formTwo').hide();
                            //             $('#formThreeTxt').text(`Enter the OTP code sent to your mobile number +${mobileNumber}`);
                            //             $('#verifyBTN').attr(`onclick`, `setTimeout(() => { verifyUPdate(${mobileNumber}, ${dialCode}); }, 100);`);
                            //             $('#formThree').show();

                            //             trim_seconds();

                            //             showToast('success', `OTP send successfully!`, 5000);

                            //         }).catch(function (error) {

                            //             showToast('error', 'Couldn`t send OTP', 5000);

                            //             $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            //             // showToast('error', response.message, 5000);
                            //         });
                            // } else {
                            //     $('#formTwo').hide();
                            //     $('#formThreeTxt').text(`Enter the OTP code sent to your mobile number +${mobileNumber}`);
                            //     $('#verifyBTN').attr(`onclick`, `setTimeout(() => { verifyUPdate(${mobileNumber}, ${dialCode}); }, 100);`);
                            //     $('#formThree').show();


                            //     trim_seconds();



                            //     showToast('success', response.message, 5000);
                            // }

                        } else {

                            // $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 

                    btn.html(`Save Content`).prop('disabled', false);
                },
                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    btn.html(`Save Content`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },

                processData: false,
                contentType: false
            });
            // }


            //     $.ajax({
            //     url: '/save-article',
            //     method: 'POST',
            //     data: {
            //         _token: '{{ csrf_token() }}',
            //         content_summary: content,
            //         url: url,
            //         title: title,
            //         description: description,
            //         keyword: keyword
            //     },
            //     success: function (response) {
            //         alert('Content saved successfully!');
            //     },
            //     error: function (err) {
            //         console.error(err);
            //         alert('Error saving content');
            //     }
            // });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }



</script>

@include('bookingsetting.partials.add_customer_modal')

@endsection

@section('custom_scripts')

@include('bookingsetting.partials.customers_js')

@endsection



<script type="text/javascript" src='{{ asset('assets/js/common.js') }}'></script>