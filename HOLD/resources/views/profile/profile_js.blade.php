{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    function showlist() {

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;



        $.ajax({

            url: '{{env('API_URL')}}myprofile',

            type: 'POST',

            data: formDataObject,

            success: function(data) {

                let response = data;

                console.log(response.data.currency);

                console.log(response.data);

                if (response.data != null) {

                    $('#profile_name').html(response.data.name);

                    $('#companyname').html(response.data.cmpny_name);

                    $('#email').html(response.data.email);

                    $('#phone').html(response.data.phone);

                    // $('#country').html(response.data.country);

                    $('#currency').html(response.data.currency);

                    $('#profileimage').attr('src', response.data.cmpny_logo);

                    $('#profileimage1').attr('src', response.data.cmpny_logo);



                    $('#edit_profile_name').val(response.data.name);

                    $('#edit_companyname').val(response.data.cmpny_name);

                    $('#edit_email').val(response.data.email);

                    $('#editemailpass').val(response.data.email);

                    $('#edit_phone').val(response.data.phone);

                    $('#edit_currency').val(response.data.currency);

                } else {

                    alert("please update the profile")

                }

                $("#edit_profile_name_span").text("");

                $("#edit_currency_span").text("");

                $("#edit_companyname_span").text("");

                $("#edit_phone_span").text("");

            },

            error: function(xhr, status, error) {

                console.error(error);

            }

        });









    }



    $(document).ready(function() {

        showlist();

        // console.log('hiii');

    });





    function file(data, index, callback) {

        var settings = {

            "url": "{{env('API_URL')}}showfile",

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify({

                "image": data.upload_photo

            }),

        };



        $.ajax(settings).done(function(response) {

            if (callback && typeof callback === "function") {

                callback(response, index);

            }

        });

    }



    $('#edit_profile').on('click', function() {

        console.log('hiiiiiiiiiiii')

        const url = 'createprofile';

        var formData = new FormData(document.getElementById('edit_profileForm'));

        formData.append('company_logo', document.getElementById('formFile').files[0]);

        var serializedData = $('#edit_profileForm').serializeArray();

        $.each(serializedData, function(key, input) {

            formData.append(input.name, input.value);

        });



        formData.append('token', getCookie('d_token'));

        formData.append('device_id', 0);



        let edit_profile_name = $("#edit_profile_name").val();

        let edit_currency = $("#edit_currency").val();

        let edit_companyname = $("#edit_companyname").val();

        let edit_phone = $("#edit_phone").val();



        let Validateverify = true;



        // console.log(edit_currency);

        if (edit_profile_name == '') {

            $("#edit_profile_name_span").text("This Field Is Required");

            Validateverify = false;

        } else if (edit_profile_name.length > 30) {

            $("#edit_profile_name_span").text("Max Length 30 Characters");

            Validateverify = false;

        } else {

            $("#edit_profile_name_span").text("");

        }



        if (edit_currency == '' || edit_currency == null) {

            $("#edit_currency_span").text("This Field Is Required");

            Validateverify = false;

        } else {

            $("#edit_currency_span").text("");

        }



        if (edit_companyname == '') {

            $("#edit_companyname_span").text("This Field Is Required");

            Validateverify = false;

        } else if (edit_companyname.length > 30) {

            $("#edit_companyname_span").text("Max Length 30 Characters");

            Validateverify = false;

        } else {

            $("#edit_companyname_span").text("");

        }

        if (edit_phone == '') {

            $("#edit_phone_span").text("This Field Is Required");

            Validateverify = false;

        } else {

            if (isNaN(edit_phone)) {

                $("#edit_phone_span").text("This Field Is Required");

                Validateverify = false;

            } else {

                $("#edit_phone_span").text("");

                // Validateverify = true;

            }

        }

        // console.log(Validateverify)

        if (Validateverify) {

            $('#edit_profile').html('<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;"><span class="visually-hidden">Loading...</span></div>');

            $.ajax({

                "url": "{{env('API_URL')}}" + url,

                "method": "POST",

                "timeout": 0,

                "processData": false,

                "contentType": false,

                "mimeType": "multipart/form-data",

                "data": formData,

                "success": function(response) {

                    console.log(response);

                    var jsonResponse = JSON.parse(response);

                    var status = jsonResponse.status;

                    var message = jsonResponse.message;

                    console.log(status, 'hiii');

                    if (status == 200) {

                        Swal.fire({

                            position: "top-right",

                            icon: "success",

                            title: 'Profile Updated Successfully',

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function() {

                            location.reload();

                        });

                    }

                    if (status == 400) {

                        Swal.fire({

                            position: "top-right",

                            icon: "danger",

                            title: 'No Changes Can Be Made.',

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function() {

                            location.reload();

                        });

                    }

                    if (status == 500) {

                        warningClick('Error', response['error'], "danger");

                    }

                    if (status == 401) {

                        unauth();

                    }

                    $('#edit_profile').html(`<i class="fa fa-save"></i> Update`);

                }

            })





        } else {

            $('#edit_profile').html(`<i class="fa fa-save"></i> Update`);



        }





    });


    //profile
    $(document).ready(function() {

        // Check if an image is already stored in localStorage
        if (localStorage.getItem('profilePic')) {
            $('.profile-pic').attr('src', localStorage.getItem('profilePic'));
        }

        var readURL = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    // Update the preview image
                    $('.profile-pic').attr('src', e.target.result);
                    // Store the uploaded image URL in localStorage
                    localStorage.setItem('profilePic', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Trigger file input on button click
        $(".upload-button").on('click', function() {
            $(".file-upload").click();
        });

        // Handle file upload change
        $(".file-upload").on('change', function() {
            readURL(this);
        });
    });



    //change password 



    $('#password_change_butn').on('click', function() {

        const url = 'profilepasswordchange';

        var serializedData = $('#password_profileForm').serializeArray();
        // serializedData.push({ name: "device_id", value: "2" });
        console.log("Serialized Data:", serializedData);
        // return false;



        var formData = new FormData();



        $.each(serializedData, function(key, input) {
            console.log("Appending:", input.name, input.value);
            formData.append(input.name, input.value);

        });



        formData.append('token', getCookie('d_token'));

        console.log("Token:", getCookie('d_token'));

        formData.append('device_id', 0);

        console.log("Device ID:", 0);


        var settings = {



        };

        $.ajax({

            "url": "{{env('API_URL')}}" + url,

            "method": "POST",

            "timeout": 0,

            "processData": false,

            "contentType": false,

            "mimeType": "multipart/form-data",

            "data": formData,

            "success": function(response) {
                console.log(response);

                var jsonResponse = JSON.parse(response);

                var status = jsonResponse.status;

                var message = jsonResponse.message;

                if (message == 'Mail send successfully') {

                    $("#otpshow").show();

                    $("#otp").on("input", function() {
                        let otpValue = $(this).val();
                        if (otpValue.length === 4) {
                            $("#password_change_butn").prop("disabled", false);
                        } else {
                            $("#password_change_butn").prop("disabled", true);
                        }
                    });

                    $("#password_change_butn").text(`Submit`);

                    
                        Swal.fire({
                            toast: true,
                            position: 'top-end', 
                            icon: 'warning',
                            text: 'Kindly check your email and enter the OTP',
                            showConfirmButton: false,
                            timer: 3000, 
                            timerProgressBar: true
                        });


                   

                } else if (message == 'Enter your Password') {
                    $("#otp").attr("readonly", true);
                    $("#passwordshow").show();

                    $("#conformpasswordshow").show();

                    $("#password, #con_password").on("input", function() {
                        let password = $("#password").val();
                        let confirmPassword = $("#con_password").val();
                        // console.log(password);
                        // console.log(confirmPassword);
                        if (password.length >= 8 && password.length <= 20 && confirmPassword.length >= 8 && confirmPassword.length <= 20) {
                            $("#password_change_butn").prop("disabled", false);
                        } else {
                            $("#password_change_butn").prop("disabled", true);
                        }
                    });

                    toastr.warning('Please fill Password');

                } else if (message == 'Password Mismatch') {

                    $('#password').val('');

                    $('#con_password').val('');

                    toastr.warning('Password And Confirm Password Not Same');

                } else if (message == "Invalid Otp") {

                    $('#otp').val('');

                    toastr.warning('Please fill Valid otp');

                }

                if (message == "Password Changed successfully") {

                    Swal.fire({

                        // position: "top-right",

                        icon: "success",

                        title: message,

                        showConfirmButton: false,

                        timer: 1500

                    }).then(function() {

                        location.reload();

                    });





                }

                if (status == 400) {

                    Swal.fire({

                        // position: "top-right",

                        icon: "danger",

                        title: message,

                        showConfirmButton: false,

                        timer: 1500

                    }).then(function() {

                        location.reload();

                    });

                }

                if (status == 500) {

                    warningClick('Error', response['error'], "danger");

                }

                if (status == 401) {

                    unauth();

                }

            }

        })



    });
</script>