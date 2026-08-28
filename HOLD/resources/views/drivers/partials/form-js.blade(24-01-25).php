<script>

$('#sidebar_menu_opener').click(function(){
   $('#add-drivermodel').toggleClass('driver-modelactive'); 
});

 const current_date = new Date().toISOString().split('T')[0]; 
$(document).ready(function(){

    // Select all date fields with the 'future-date' class
    const futureDateFields = document.querySelectorAll('.future-date');

    // Set the min attribute for all date fields to today's date
    futureDateFields.forEach(field => {
        field.setAttribute('min', current_date);
    });
 
  vehiclelist();
$('#add-drivermodel').modal('show');
});   
//     $.ajaxSetup({
        // 				headers: {
        // 					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        // 				}
        // 			});
        //     function PreviewImage() {
        //         var oFReader = new FileReader();
        //         oFReader.readAsDataURL(document.getElementById("upload_photo").files[0]);

        //         oFReader.onload = function (oFREvent) {
        //             document.getElementById("uploadPreview").src = oFREvent.target.result;
        //         };
        //     };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    function PreviewImage() {
         var fileInput = $('#upload_photo')[0].files[0];  // Get the file selected in the input
        var fileSize = fileInput.files[0].size; // File size in bytes
        var maxSize = 2 * 1024 * 1024; // 2 MB in bytes
        
        if (fileSize > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: 'Error: File size exceeds 2MB. Please choose a smaller file.',
            });
        } else {
            var fileSizeInMB = (fileSize / (1024 * 1024)).toFixed(2);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: `Your action with file size ${fileSizeInMB} MB was successful.`
            });
        
            var oFReader = new FileReader();
            oFReader.readAsDataURL(fileInput.files[0]);
        
            oFReader.onload = function (oFREvent) {
                document.getElementById("uploadPreview").src = oFREvent.target.result;
                $('#uploadPreview').show();
            };
        }
    };



    //Modal Form Trigger
    $('#addFileupload').click(function () {
        ResetErrors();
    $('#message').css('display', 'none');
    let driver_id = $('#driver_id_file').val('');
  
    $('#upload_form').trigger("reset");
    $('#form-modal').modal('show',driver_id);
        
    });

    //File Uploadation
   $("#file_upload_btn").click(function (e) {
    e.preventDefault();
    
    // Create a new FormData object from the form
    var formData = new FormData($('#upload_form')[0]);
    
    // Manually append the file to the FormData (optional, as FormData does this automatically)
    var fileInput = $('#select_file')[0].files[0];  // Get the file selected in the input
        
    if (fileInput) {
        formData.append('select_file', fileInput);  // Append the file to the FormData object
    }

    $.ajax({
        url: "{{ route('FileUpload') }}",  // Adjust this route as needed
        method: "POST",
        data: formData,
        dataType: "JSON",
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            if (data.isUploaded) {
                ResetErrors();
                $('#upload_form').trigger("reset");
                $('#form-modal').modal('hide');
                $('#uploaded_documents_view').html('');
                
                if (data.document_details != '') {
                    MakeDocumentsView(data.document_details);
                }
                
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Uploaded',
                    text: 'Driver document Uploaded successfully',
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                $('#message').css('display', 'block');
                $('#message').html(data.message);
                $('#message').addClass(data.class_name);
            }
        }
    });
});


    $(document).on('click', '.document_delete', function () {
        let document_id = $(this).data("id");
    Swal.fire({
        title: "Are you sure to delete this driver?",
    text: "It will gone forever.",
    icon: "warning",
    buttons: true,
    dangerMode: true,
        }).then((willDelete) =>
    {
            if(willDelete.isConfirmed)
    {
        $.ajax({
            url: "{{ route('FileDelete') }}",
            method: "POST",
            data: { document_id: document_id },
            dataType: "JSON",
            success: function (response) {
                if (response.isDeleted) {
                    $('#uploaded_documents_view').html('');
                    console.log('test1');
                    if(response.document_details!= ''){
                        MakeDocumentsView(response.document_details);
                    }
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Driver document deleted successfully',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } else {
                    Swal.fire("Error", "Driver Document not deleted", "error");
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        })
    }
        })
    
    });

    function MakeDocumentsView(document_details){
        $.each(document_details, function (key, item) {
            $('#uploaded_documents_view').append('<tr>\
                <td>'+ item.description + '</td>\
                <td><a href="/driver-documents/'+ item.driver_id + '/' + item.file_path + ' " target="_blank" id="document_view" class="mb-2 mr-2 btn-sm btn-transition btn " style="color: #266444; border-color:#266444;" title="View Document"> <i class="fa fa-eye" ></i> </a><button type="button" class="mb-2 mr-2 btn-sm btn btn-outline-danger document_delete" data-id="' + item.id + '" title="Delete Document" />  <i class="fa fa-trash"></i> </button></td>\
                </tr>'
            );
        });
    }

    function ResetErrors(){
        $('.invalid-file, .invalid-description').text('');
    }

    function ShowErrors(errors){
        if(errors.select_file){
        $('.invalid-file').text(errors.select_file);
        }
    if(errors.description){
        $('.invalid-description').text(errors.description);
        }
    }

    // $('#driver_sub').on('click',function(){
        //     alert('king');
        //     const url = 'createdriver';
        //     var formdata = $('#driver_form').serialize();
        //    
        //      var pairs = formdata.split('&');
        //         var formDataObject  = {};

        //         for (var i = 0; i < pairs.length; i++) {
        //           var pair = pairs[i].split('=');
        //           var key = decodeURIComponent(pair[0]);
        //           var value = decodeURIComponent(pair[1]);
        //           formDataObject[key] = value;
        //         }
        //         formDataObject['token'] = getCookie('d_token');
        //         formDataObject['device_id'] = 0;
        //     var settings = {
        //      "url": "{{env('API_URL')}}"+url,
        //      "method": "POST",
        //      "timeout": 0,
        //      "headers": {
        //          "Content-Type": "application/json"
        //       },
        //      "data": JSON.stringify(formDataObject),
        //   };
        //   $.ajax(settings).done(function (response) {
        //      if(response['status'] == 200){
        //          setCookie('swal',response['message'],'1')
        //          window.location.href="/driver";
        //          }
        //      if(response['status'] == 400){
        //         errornotify(response)
        //      }
        //      if(response['status'] == 500){
        //         warningClick('Error',response['error'],"danger")
        //      }
        //      if(response['status'] == 401){
        //         unauth()
        //      }
        //   });
        // })
        
        $('#driver_sub').on('click', function () {
            
            var driverNo = $('#driver_no').val().trim();
            var name = $('#name').val().trim();
            var phone = $('#phone').val().trim();
            var email = $('#email').val().trim();
            var address = $('#address').val().trim();
            var dob = $('#dob').val().trim();
            var national_insurance_no = $('#national_insurance_no').val().trim();
            var vehicle_type = $('#vehicle_type').val().trim();
            var vehicle_reg_no = $('#vehicle_reg_no').val().trim();
            var vehicle_color = $('#vehicle_color').val().trim();
            var number_of_seats = $('#number_of_seats').val().trim();
            var vehicle_insurance = $('#vehicle_insurance').val().trim();
            var vehicle_insurance_expiry = $('#vehicle_insurance_expiry').val().trim();
            var vehicle_license = $('#vehicle_license').val().trim();
            var vehicle_license_expiry = $('#vehicle_license_expiry').val().trim();
            var pco_license_no = $('#pco_license_no').val().trim();
            var pco_license_no_expiry = $('#pco_license_no_expiry').val().trim();
            var driver_license_no = $('#driver_license_no').val().trim();
            var driver_license_no_expiry = $('#driver_license_no_expiry').val().trim();
            var mot_no = $('#mot_no').val().trim();
            var mot_no_expiry = $('#mot_no_expiry').val().trim();

            var today = new Date(); // Get today's date
            var driverLicenseExpiry = new Date(driver_license_no_expiry);
            var motNoExpiry = new Date(mot_no_expiry);
            var pcoLicenseExpiry = new Date(pco_license_no_expiry);
            var vehicleLicenseExpiry = new Date(vehicle_license_expiry);
            var vehicleInsuranceExpiry = new Date(vehicle_insurance_expiry);

            const url = 'createdriver';
            var formdata = new FormData($('#driver_form')[0]);
       
            formdata.append('token', getCookie('d_token'));
            formdata.append('device_id', 0);
            
            const futureDateFields = document.querySelectorAll('.future-date'); // Select fields with the class
        
            // Set the min attribute for all fields with the future-date class
            futureDateFields.forEach(field => {
                field.setAttribute('min', current_date);
            });
        
            let val_validate = validateForm();
            console.log(val_validate);
            if(val_validate){
                
                futureDateFields.forEach(field => {
                    const errorSpan = document.getElementById(`${field.id}_Error`);
                    errorSpan.textContent = ''; // Clear previous errors
        
                    const selectedDate = new Date(field.value);
                    const currentDate = new Date(today);
        
                    if (field.value != '') {
                        if (selectedDate < currentDate) {
                            errorSpan.textContent = 'Please select a future date.';
                            val_validate = false;
                        }
                    }
                });
            
                if (val_validate) {
                    $('#driver_sub').html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
                            <span class="visually-hidden">Loading...</span>
                        </div>`)
                var settings = {
                    "url": "{{env('API_URL')}}" + url,
                    "method": "POST",
                    "timeout": 0,
                    "processData": false,
                    "contentType": false,
                    "mimeType": "multipart/form-data",
                    "data": formdata,
                };
    
                $.ajax(settings).done(function (response) {
                    $('#driver_sub').html(`<i class="fa fa-save"></i>&nbsp; Save`)
                    response = JSON.parse(response);
    
                    // alert(response.status);
    
                    if (response.status === 200) {
                        setCookie('swal', response['message'], '1');
                    }
    
    
                    if (response.status === 400 && response.errors) {
                        // alert('welcome');
    
                        for (var key in response.errors) {
    
                            if (response.errors.hasOwnProperty(key)) {
                                var errorMessages = response.errors[key];
                                // alert(errorMessages);
    
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: errorMessages,
                                });
                                // Display error messages however you want, for example:
                                errorMessages.forEach(function (message) {
                                    console.log(key + ': ' + message);
                                });
                            }
                        }
                    }
    
                    if (response.status === 500 ) {
                        // alert('welcom');
                        // warningClick('Error', response['error'], "danger");
                        const response = {status: 500, error: "Undefined variable $image_path"};
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: response.error,
                        });
                                          
                    }
                    if (response['status'] == 401) {
                        unauth();
                    }
                    
                    let curr_url  = window.location.pathname;
                    if(curr_url == '/driver-create'){
                        window.location.href = "/booking/create";
                        
                    }else{
                        window.location.reload()
                        
                    }
    
                });
            }else
            {
                $('#driver_sub').html(`<i class="fa fa-save"></i>&nbsp; Save`)
            }
            }else{
                $('#driver_sub').html(`<i class="fa fa-save"></i>&nbsp; Save`)
            }
            
            

            
        });
        
        function validateForm() {
        let isValid = true;

        // Driver No.
        let driverNo = document.getElementById('driver_no');
        let driverNoError = document.getElementById('DriverNoError');
        if (!driverNo.value.trim()) {
            driverNoError.textContent = "Driver No. is required.";
            isValid = false;
            $('#driver_no').focus();
        } else {
            driverNoError.textContent = "";
        }

        // Name
        let name = document.getElementById('name');
        let nameError = document.getElementById('firstnameError');
        if (!name.value.trim()) {
            nameError.textContent = "Name is required.";
            isValid = false;
            $('#name').focus();
        } else {
            nameError.textContent = "";
        }

        // Phone No.
        let phone = document.getElementById('phone');
        let phoneError = document.getElementById('PhoneNoError');
        if (!phone.value.trim()) {
            phoneError.textContent = "Phone No. is required.";
            isValid = false;
            $('#phone').focus();
        } else if (phone.value.length < 10) {
            phoneError.textContent = "Phone No. should be at least 10 digits.";
            isValid = false;
            $('#phone').focus();
        } else {
            phoneError.textContent = "";
        }

        // Email
        let email = document.getElementById('email');
        let emailError = document.getElementById('EmailError');
        let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!email.value.trim()) {
            emailError.textContent = "Email is required.";
            isValid = false;
            $('#email').focus();
        } else if (!emailPattern.test(email.value)) {
            emailError.textContent = "Invalid email format.";
            isValid = false;
            $('#email').focus();
        } else {
            emailError.textContent = "";
        }

        // Address
        let address = document.getElementById('address');
        let addressError = document.getElementById('AddressError');
        if (address.value.length > 200) {
            addressError.textContent = "Address should not exceed 200 characters.";
            isValid = false;
            $('#address').focus();
        } else {
            addressError.textContent = "";
        }

        // // Date of Birth
        // let dob = document.getElementById('dob');
        // let dobError = document.getElementById('DateofBirthError');
        // if (!dob.value.trim()) {
        //     dobError.textContent = "Date of Birth is required.";
        //     isValid = false;
        // } else {
        //     dobError.textContent = "";
        // }
        
        // Get the input value
        // var dob = $('#dob').val().trim();
        let dob = $('#dob').val().trim();
        let dobNoError = document.getElementById('DateofBirthError');
        if (dob) {
            // var dobDate = new Date(dob);
            var parts = dob.split('-');
            if (parts.length === 3) {
                var dobDate = new Date(parts[0], parts[1] - 1, parts[2]); // Month is zero-based in JS

                // Get the current date
                var today = new Date();
        
                // Calculate the age
                var age = today.getFullYear() - dobDate.getFullYear();
                var monthDiff = today.getMonth() - dobDate.getMonth();
        
                // Adjust the age if the birthday hasn't occurred this year yet
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
        
                // Validate age
                if (age < 18) {
                    dobNoError.textContent = "Age must be above 18.";
                    $('#dob').focus();
                    isValid = false;
                }else{
                    dobNoError.textContent = "";
                    
                }
            }
        } else {
            // console.log('jnfdcbhhd')
            // dobNoError.textContent = "DOB is required";
        }


        // National Insurance No.
        let nationalInsuranceNo = document.getElementById('national_insurance_no');
        let nationalInsuranceNoError = document.getElementById('NationalInsuranceNoError');
        if (nationalInsuranceNo.value.length > 20) {
            nationalInsuranceNoError.textContent = "National Insurance No. should not exceed 20 characters.";
            isValid = false;
            $('#national_insurance_no').focus();
        } else {
            nationalInsuranceNoError.textContent = "";
        }

        // Vehicle Type
        let vehicleType = document.getElementById('vehicle_type');
        let vehicleTypeError = document.getElementById('vehicle_type_Error');
        if (vehicleType.value.trim() == "") {
            vehicleTypeError.textContent = "Vehicle Type is required.";
            isValid = false;
            $('#vehicle_type').focus();
        } else {
            vehicleTypeError.textContent = "";
        }

        // Vehicle Reg No.
        let vehicleRegNo = document.getElementById('vehicle_reg_no');
        let vehicleRegNoError = document.getElementById('vehicle_reg_no_Error');
        if (vehicleRegNo.value.length > 25) {
            vehicleRegNoError.textContent = "Vehicle Reg No. should not exceed 25 characters.";
            isValid = false;
            $('#vehicle_reg_no').focus();
        } else {
            vehicleRegNoError.textContent = "";
        }

        // Vehicle Color
        let vehicleColor = document.getElementById('vehicle_color');
        let vehicleColorError = document.getElementById('vehicle_color_Error');
        if (vehicleColor.value.length > 15) {
            vehicleColorError.textContent = "Vehicle Color should not exceed 15 characters.";
            isValid = false;
            $('#vehicle_color').focus();
        } else {
            vehicleColorError.textContent = "";
        }

        // Number of Seats
        let numberOfSeats = document.getElementById('number_of_seats');
        let numberOfSeatsError = document.getElementById('number_of_seats_Error');
        if (numberOfSeats.value < 0 || numberOfSeats.value > 99) {
            numberOfSeatsError.textContent = "Number of seats should be between 0 and 99.";
            isValid = false;
            $('#number_of_seats').focus();
        } else {
            numberOfSeatsError.textContent = "";
        }

        // // Vehicle Insurance Expiry
        // let vehicleInsuranceExpiry = document.getElementById('vehicle_insurance_expiry');
        // let vehicleInsuranceExpiryError = document.getElementById('vehicle_insurance_expiry_Error');
        // if (!vehicleInsuranceExpiry.value.trim()) {
        //     vehicleInsuranceExpiryError.textContent = "Vehicle Insurance Expiry is required.";
        //     isValid = false;
        // } else {
        //     vehicleInsuranceExpiryError.textContent = "";
        // }

        // // Vehicle License Expiry
        // let vehicleLicenseExpiry = document.getElementById('vehicle_license_expiry');
        // let vehicleLicenseExpiryError = document.getElementById('vehicle_license_expiry_Error');
        // if (!vehicleLicenseExpiry.value.trim()) {
        //     vehicleLicenseExpiryError.textContent = "Vehicle License Expiry is required.";
        //     isValid = false;
        // } else {
        //     vehicleLicenseExpiryError.textContent = "";
        // }

        // // PCO License Expiry
        // let pcoLicenseExpiry = document.getElementById('pco_license_no_expiry');
        // let pcoLicenseExpiryError = document.getElementById('pco_license_no_expiry_Error');
        // if (!pcoLicenseExpiry.value.trim()) {
        //     pcoLicenseExpiryError.textContent = "PCO License Expiry is required.";
        //     isValid = false;
        // } else {
        //     pcoLicenseExpiryError.textContent = "";
        // }

        // // Driver License Expiry
        // let driverLicenseExpiry = document.getElementById('driver_license_no_expiry');
        // let driverLicenseExpiryError = document.getElementById('driver_license_no_expiry_Error');
        // if (!driverLicenseExpiry.value.trim()) {
        //     driverLicenseExpiryError.textContent = "Driver License Expiry is required.";
        //     isValid = false;
        // } else {
        //     driverLicenseExpiryError.textContent = "";
        // }

        // // MOT Expiry
        // let motExpiry = document.getElementById('mot_no_expiry');
        // let motExpiryError = document.getElementById('mot_no_expiry_Error');
        // if (!motExpiry.value.trim()) {
        //     motExpiryError.textContent = "MOT Expiry is required.";
        //     isValid = false;
        // } else {
        //     motExpiryError.textContent = "";
        // }

        // Commission Value
        let driver_bookingValue = document.getElementById('driver_booking_percentage');
        let driver_bookingValueError = document.getElementById('book_per_value_Error');
        if (driver_bookingValue.value < 0 || driver_bookingValue.value > 99) {
            driver_bookingValueError.textContent = "Booking Percentage should be between 0 and 99.";
            isValid = false;
            $('#driver_booking_percentage').focus();
        } else {
            driver_bookingValueError.textContent = "";
        }
        
        
        let commissionValue = document.getElementById('commision_value');
        let commissionValueError = document.getElementById('commision_value_Error');
        if (commissionValue.value < 0 || commissionValue.value > 99) {
            commissionValueError.textContent = "Commission Value should be between 0 and 99.";
            isValid = false;
            $('#commision_value').focus();
        } else {
            commissionValueError.textContent = "";
        }
        
        
        let bookingValue = document.getElementById('booking_email');
        let bookingValueError = document.getElementById('booking_value_Error');
        if (bookingValue.value < 0 || bookingValue.value > 99) {
            bookingValueError.textContent = "Booking Value should be between 0 and 99.";
            isValid = false;
            $('#booking_email').focus();
        } else {
            bookingValueError.textContent = "";
        }
        
        
        let startValue = document.getElementById('start_date');
        let startValueError = document.getElementById('commision_value_Error');
        if (startValue.value < 0 || startValue.value > 99) {
            startValueError.textContent = "Commission Value should be between 0 and 99.";
            isValid = false;
            $('#start_date').focus();
        } else {
            startValueError.textContent = "";
        }
        
        let endValue = document.getElementById('end_date');
        let endValueError = document.getElementById('commision_value_Error');
        if (endValue.value < 0 || endValue.value > 99) {
            endValueError.textContent = "Commission Value should be between 0 and 99.";
            isValid = false;
            $('#end_date').focus();
        } else {
            endValueError.textContent = "";
        }
        
        return isValid;
    }


    // $('#driver_sub_up').on('click', function(){
        //     alert('king');
        //     const url = 'updatedriver';
        //     var formdata = $('#driver_up_form').serialize();
        //      var pairs = formdata.split('&');
        //         var formDataObject  = {};

        //         for (var i = 0; i < pairs.length; i++) {
        //           var pair = pairs[i].split('=');
        //           var key = decodeURIComponent(pair[0]);
        //           var value = decodeURIComponent(pair[1]);
        //           formDataObject[key] = value;
        //         }
        //         formDataObject['token'] = getCookie('d_token');
        //         formDataObject['device_id'] = 0;
        //         // console.log(formDataObject);
        //     var settings = {
        //      "url": "{{env('API_URL')}}"+url,
        //      "method": "POST",
        //      "timeout": 0,
        //      "headers": {
        //          "Content-Type": "application/json"
        //       },
        //      "data": JSON.stringify(formDataObject),
        //   };
        //   $.ajax(settings).done(function (response) {
        //      if(response['status'] == 200){
        //          setCookie('swal',response['message'],'1')
        //          window.location.href="/driver";
        //          }
        //      if(response['status'] == 400){
        //         errornotify(response)
        //      }
        //      if(response['status'] == 500){
        //         warningClick('Error',response['error'],"danger")
        //      }
        //      if(response['status'] == 401){
        //         unauth()
        //      }
        //   });
        // })
$('#driver_sub_up').on('click', function () {
    const url = 'updatedriver';
    var formData = new FormData($('#driver_up_form')[0]);

    // Get the driver_id from the form (assuming it's in an input element)
    var driverId = $('#driver_up_form').find('input[name="driver_id"]').val();

    // Ensure driver_id is an integer and remove any non-numeric values or duplicate
    driverId = parseInt(driverId, 10);

    // Only append the driver_id if it's a valid integer
    if (Number.isInteger(driverId)) {
        formData.append('driver_id', driverId);
    }

    // Append additional data
    formData.append('token', getCookie('d_token'));
    formData.append('device_id', 0);

    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "contentType": false,
        "mimeType": "multipart/form-data",
        "data": formData
    };

    $.ajax(settings).done(function (response) {
        var responseObject = JSON.parse(response);
        var status = responseObject.status;
        var message = responseObject.message;
        console.log(message);

        if (status == 200) {
            setCookie('swal', message, '1');
            window.location.href = "/driver";
        }
        if (status == 400) {
            errornotify(response);
        }
        if (status == 500) {
            warningClick('Error', response['error'], "danger");
        }
        if (status == 401) {
            unauth();
        }
    });
});


    
    function driveredit(id){
          const url = 'editdriver';
    var formDataObject  = { };
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    formDataObject['driver_id'] = id;
    var settings = {
        "url": "{{env('API_URL')}}"+url,
    "method": "POST",
    "timeout": 0,
    "headers": {
        "Content-Type": "application/json"
          },
    "data": JSON.stringify(formDataObject),
      };
    $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
        $('#driver_id').val(response['driver_id'])
         $('#driver_no').val(response['driver'][0].driver_no)
        $('#name').val(response['driver'][0].name)
        $('#phone').val(response['driver'][0].phone)
        $('#address').val(response['driver'][0].address)
        $('#email').val(response['driver'][0].email)
        $('#dob').val(response['driver'][0].dob)
        $('#national_insurance_no').val(response['driver'][0].ni_num)
        $('#driver_booking_percentage').val(response['driver'][0].booking_comm_val)
        $('#commision_value').val(response['driver'][0].commission_val)
        $('#booking_email').val(response['driver'][0].booking_email)
        $('#start_date').val(response['driver'][0].start_date)
        $('#end_date').val(response['driver'][0].end_date)
        // brands(response['driver'][0].make,'vehicle_make')
        // models(response['driver'][0].make,response['driver'][0].model,'vehicle_model')
        veh_types(response['driver'][0].make,response['driver'][0].model,response['driver'][0].vech_type,'vehicle_type')
        $('#vehicle_reg_no').val(response['driver'][0].vech_reg_num)
        $('#vehicle_color').val(response['driver'][0].vech_color)
        $('#number_of_seats').val(response['driver'][0].no_seat)
        $('#vehicle_insurance').val(response['driver'][0].vech_insurance)
        $('#vehicle_insurance_expiry').val(response['driver'][0].vech_insur_expiry_date)
        $('#vehicle_license').val(response['driver'][0].vech_licence_no)
        $('#vehicle_license_expiry').val(response['driver'][0].vech_insur_expiry_date)
        $('#pco_license_no').val(response['driver'][0].pco_licence_no)
        $('#pco_license_no_expiry').val(response['driver'][0].pco_lic_expiry_date)
        $('#driver_license_no').val(response['driver'][0].driver_licence_no)
        $('#driver_license_no_expiry').val(response['driver'][0].driver_lic_expiry_date)
        $('#mot_no').val(response['driver'][0].mot_no)
        $('#mot_no_expiry').val(response['driver'][0].mot_expiry_date)
        $('#refresh_time').val(response['driver'][0].refresh_time)
        $('#before_reminder_time').val(response['driver'][0].reminder_time)
        $('#start_journey_gaptime').val(response['driver'][0].gap_time)
        $('#customer_call').val(response['driver'][0].customer_call)
        $('#show_image_data').val(response['driver'][0].upload_photo)
        var imageurl="https://airportrides-storage.s3.amazonaws.com/";
        var test_imae=response['driver'][0].upload_photo;
        var show_image=imageurl+test_imae;
        console.log(response['driver'][0].upload_photo);
        $('#uploadPreview').attr('src',show_image)
        // $('#uploadPreview').show()
        $('#upload_photo').attr('src',test_imae)
    
             }
    if(response['status'] == 400){
             var key = window.location.href;
    var segments = key.split('/');
    var lastSegment = segments.pop();
    if(lastSegment == 'create'){
      
    }else{
        // warningClick('Error', response['error'], "danger")
    }
         }
    if(response['status'] == 500){
        warningClick('Error', response['error'], "danger")
    }
    if(response['status'] == 401){
        unauth()
    }
      });
      }

    $('#vehicle_make').on('change', function(){
          var id = $('#vehicle_make').val();
    if(id != ''){
        // models(id, '', 'vehicle_model')
    }else{
        $('#vehicle_model').html('<option value="">select</option>')
    }
      })

    $('#vehicle_model').on('change', function(){
          var br_id = $('#vehicle_make').val();
    var md_id = $('#vehicle_model').val();
    if(br_id != '' && md_id != ''){
        veh_types(br_id, md_id, '', 'vehicle_type')
    }else{
        $('#vehicle_type').html('<option value="">select</option>')
    }
          
      })

    $(function(){
        // brands('', 'vehicle_make')
        var key = window.location.href;
    var segments = key.split('/');
    var lastSegment = segments.pop();
        driveredit(lastSegment)
    })
    
    
    
    
    
    
    function vehiclelist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}Vehiclelist',
        method: 'POST', // Consider using GET if you're only fetching data
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                 PopulateSelect(data);
               
                // VehicleValues(data);
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}



function PopulateSelect(data) {
    var select = $('#vehicle_type');
    select.empty(); // Clear existing options
    
    // Add default option
    

    select.append('<option value=" ">Select Vehicle Type</option>');
    if (data.length > 0) {
        data.forEach(function(vehicle) {
            var option = $(`<option value="${vehicle}">${vehicle}</option>`);
            select.append(option);
        });
    } else {
        select.append('<option value="">No vehicles found.</option>');
    }
    
}

function VehicleValues(vehicleName) {
    // Find and select the option with the specified vehicle name
    $('#vehicle_type').val(vehicleName).trigger('change');
}

function validateNumberInput(input) {
        // This will replace anything that is not a digit (0-9) with an empty string
        input.value = input.value.replace(/\D/g, '');
    }

</script>