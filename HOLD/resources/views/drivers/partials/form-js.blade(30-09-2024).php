<script>
$(document).ready(function(){
   
 
  vehiclelist();

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
        var fileInput = document.getElementById("upload_photo");
        
        // alert('this image upload checking');
        var fileSize = fileInput.files[0].size; // File size in bytes
        var maxSize = 50 * 1024; // 50 KB in bytes
        if (fileSize > maxSize) {
            // alert("File size exceeds 50KB. Please choose a smaller file.");
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: 'Error: File size exceeds 50KB. Please choose a smaller file.',
            });
        } else {
            // alert("File size is " + (fileSize / 1024).toFixed(2) + " KB. Proceed with the file.");
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your action with file size ' + (fileSize / 1024).toFixed(2) + ' KB was successful.',
            });
            var oFReader = new FileReader();
            oFReader.readAsDataURL(fileInput.files[0]);
        
            oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        }
    
        
        };
    };



    //Modal Form Trigger
    $('#addFileupload').click(function () {
        ResetErrors();
    $('#message').css('display', 'none');
    let driver_id = $('#driver_id_file').val('');
    //console.log(driver_id);
    $('#upload_form').trigger("reset");
    $('#form-modal').modal('show',driver_id);
        
    });

    //File Uploadation
    $("#file_upload_btn").click(function (e) {
        e.preventDefault();


    $.ajax({
        url : "{{ route('FileUpload') }}",
    method : "POST",
    data : new FormData($('#upload_form')[0]),
    dataType : "JSON",
    contentType : false,
    cache: false,
    processData: false,
    success:function(data)
    {
                if(data.isUploaded){
        ResetErrors();
    // $('#message').css('display', 'block');
    // $('#message').html(data.message);
    // $('#message').addClass(data.class_name);
    $('#upload_form').trigger("reset");
    $('#form-modal').modal('hide');
    $('#uploaded_documents_view').html('');
    MakeDocumentsView(data.document_details);
    Swal.fire({
        position: 'top-end',
    icon: 'success',
    title: 'Uploaded',
    text: 'Driver document Uploaded successfully',
    showConfirmButton: false,
    timer: 2000
                    });
                }else {
    $('#message').css('display', 'block');
    $('#message').html(data.message);
    $('#message').addClass(data.class_name);
                }
                
            }
        })
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
                    MakeDocumentsView(response.document_details);
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
        //     console.log(formdata);
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
            //console.log(formdata);
            formdata.append('token', getCookie('d_token'));
            formdata.append('device_id', 0);

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
                response = JSON.parse(response);

                // alert(response.status);

                if (response.status === 200) {
                    setCookie('swal', response['message'], '1');
                    window.location.href = "/driver";
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

            });
        });


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
                    errornotify(response)
                }
                if (status == 500) {
                    warningClick('Error', response['error'], "danger")
                }
                if (status == 401) {
                    unauth()
                }
            });
        })

    
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
             }
    if(response['status'] == 400){
             var key = window.location.href;
    var segments = key.split('/');
    var lastSegment = segments.pop();
    if(lastSegment == 'create'){
      
    }else{
        warningClick('Error', response['error'], "danger")
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
                console.log(response);
                VehicleValues(data);
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
    select.append('<option value=""></option>');

    if (data.length > 0) {
        data.forEach(function(vehicle) {
            var option = $('<option></option>');
            option.val(vehicle); // Set value to vehicle name
            option.text(vehicle); // Set text to vehicle name
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

</script>