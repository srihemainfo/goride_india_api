<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>










function AssignValues(response) {
    console.log(response);
    // Assign other fields
    $('#fleet_id').val(response['data'].id);
    $('#name').val(response['data'].name);
    $('#passenger').val(response['data'].passenger);
    $('#no_of_seats').val(response['data'].no_of_seats);
    $('#min').val(response['data'].min);
    $('#max').val(response['data'].max);
    $('#luggage').val(response['data'].luggage);
    $('#hand_luggage').val(response['data'].hand_luggage);
    $('#child').val(response['data'].child);
    $('#booster').val(response['data'].booster);
    $('#order').val(response['data'].order);

    var imageUrl = 'https://airportrides-storage.s3.amazonaws.com/' + response.data.upload_photo;
    $('#edit_imagePreview').attr('src', imageUrl);
    
    const imagePath = response.data.upload_photo;
    const imageName = imagePath.split('/').pop(); // This will give "croppedFleetImage_1735824365246.jpeg"
    
    // Set the image name in the hidden input
    const hiddenFileNameInput = document.getElementById('hidden_imageName'); // Get the hidden input field
    if (hiddenFileNameInput) {
        hiddenFileNameInput.value = imageName; // Set the value to the image file name
    }

//  const imageElement = $('#image');

// if (response['data'].upload_photo) {
//     const imagePath = response['data'].upload_photo; 
//     const baseUrl = '{{env('API_URL')}}';
//     const imageUrl = baseUrl + imagePath;

//     console.log('Constructed Image URL:', imageUrl);
//     console.log('Image Path from Response:', imagePath);

//     // Attempt to set the image source
//     imageElement.attr('src', imageUrl);
// } else {
//     console.log('No upload_photo found in response.');
//     imageElement.attr('src', 'path/to/default-image.png'); // Optional default image
// }



}





        
        function deletefleet(id){
        const url = 'deletevehichle';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
        Swal.fire({
             title: 'Are you sure?',
             text: 'You won\'t be able to revert this!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes, delete it!',
             cancelButtonText: 'No, cancel!',
           }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax(settings).done(function (response) {
                   if(response['status'] == 200){
                       $("#fleet_create_form")[0].reset();
                       $('.modal-title').html('Add Fleet')
                       $('#flfrm_dis').click()
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                  text: 'Fleet has been removed successfully',
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              showlist()
                          });
                       }
                   if(response['status'] == 400){
                       warningClick('Error',response['message'],"danger")
                   }
                   if(response['status'] == 500){
                      warningClick('Error',response['error'],"danger")
                   }
                   if(response['status'] == 401){
                      unauth()
                   }
                  });
               
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
             }
         });
        }
        
        function editvehichle(id){
            const url = 'editvehichle';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
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
             $('.modal-title').html('Edit Fleet')
              AssignValues(response)
              $('#fleet_create_sub').html("Update");
              $('#addFleet').click()
             }
         if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
        }
        
    function showlist() {
    const url = 'vehichlelist';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(formDataObject),
    };
    
    $.ajax(settings).done(function(response) {
        if (response['status'] == 200) {
            var list = '';
            for (var i = 0; i < response['data'].length; i++) {
                file(response['data'][i], i, function(imageData, index) {
                    if (response['data'][index].status == 'Active') {
                    var sts = 'checked';
                } else {
                    var sts = '';
                }
                    list += '<div class="col-md-12 col-lg-6 car-clmn mb-3"><div class="who-dr"><div class="as-sec"><label class="switch"><input type="checkbox" ' + sts + ' id="flstatus' + response['data'][index].id + '" onclick="Consequatur deleniti(' + response['data'][index].id + ')"><div class="slider"></div><div class="slider-card"><div class="slider-card-face slider-card-front"></div><div class="slider-card-face slider-card-back"></div></div></label></div><div class="img-sec">' + imageData + '</div><div class="detail-sec"><h3 style="font-size: 16px;font-weight: 600;color: #003757;">' + response['data'][index].name + ' Car</h3><ul class="mb-0 " style="display: grid;padding: 0;"><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-user me-2"></i>People - ' + response['data'][index].passenger + '</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-bag-shopping me-2"></i>Hand Bag - ' + response['data'][index].hand_luggage + '</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-suitcase-rolling me-2"></i>Suitcase - ' + response['data'][index].luggage + '</a></li></ul><ul style="display: flex;margin: 0; padding: 0;"><li style="list-style-type: none;margin: 0 9px 1px 3px;"><button style="list-style-type: none; margin: 3px;padding: 7px 8px 7px 8px;background: #254bd9;color: #fff; border-radius: 6px;border: none;" onclick="editvehichle(' + response['data'][index].id + ')"><i class="fa-solid fa-pen-to-square"></i></button></li><li style="list-style-type: none;"><button style="list-style-type: none;margin: 3px;padding: 7px 8px 7px 8px;background: #d92550;color: #fff;border-radius: 6px;border: none;" onclick="deletefleet(' + response['data'][index].id + ')"><i class="fa-solid fa-trash"></i></button></li></ul></div></div></div>';
                    $('.listoffleets').html(list);
                });
            }
        } else if (response['status'] == 400) {
            let curr_url  = window.location.pathname;
                        
            if(curr_url == '/fleet'){
                errornotify(response);
                // warningClick('Error', response['error'], "danger");
            }
        } else if (response['status'] == 500) {
            let curr_url  = window.location.pathname;
                        
            if(curr_url == '/fleet'){
                warningClick('Error', response['error'], "danger");
            }
        } else if (response['status'] == 401) {
            unauth();
        }
    });
}

function file(data, index, callback) {
    // console.log(data);
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
       
        var imageData = `<img class="img-flx" src="data:image/png;base64,${response}" alt="Displayed Image" style="width: 220px;height: 200px;padding: 0px 24px 0px 10px;border-right: 1px solid #cdc3c3;">`;
        if (callback && typeof callback === "function") {
            callback(imageData, index);
        }
    });
}



        
        $(function(){
            showlist()
           
        })
        
        $('#brand_id').on('change', function(){
          var id = $('#brand_id').val();
          if(id != ''){
              models(id,'','model_id')
          }else{
              $('#model_id').html('<option value="">select</option>')
          }
      })
      
      function reset(){
           $("#fleet_create_form")[0].reset();
          brands('','brand_id')
          $('#fleet_id').val('')
          $('#edit_imagePreview').attr('src', '');
          $('#model_id').html('<option value="">Select</option>')
          $('.modal-title').html('Add Fleet')
          $('#fleet_create_sub').html(`Save`);
      }
      
// $(document).ready(function () {
//     const fileInput = document.getElementById('fileInput');
//     let cropper;
//     let isImageCropped = false; 
//     let imageType = 'image/png'; 

//     fileInput.addEventListener('change', function (event) {
//         const file = event.target.files[0];
//         const reader = new FileReader();
//         imageType = file.type || 'image/png';

//         reader.onload = function (e) {
//             image.src = e.target.result;
//             image.style.display = 'block';
//             if (cropper) {
//                 cropper.destroy(); 
//             }
//             cropper = new Cropper(image, {
//                 aspectRatio: 1.5,
//                 viewMode: 1,
//             });
//             isImageCropped = true; 
//         };
//         reader.readAsDataURL(file);
//     });

//     $('#fleet_create_sub').on('click', function (e) {
//         e.preventDefault(); 

//         const formDataObject = new FormData($('#fleet_create_form')[0]);
//         formDataObject.append('_token', $('input[name="_token"]').val());
//         formDataObject.append('token', getCookie('d_token'));
//         formDataObject.append('device_id', 0);

//         if (isImageCropped && cropper) {
//             const croppedCanvas = cropper.getCroppedCanvas();
//             const uniqueFileName = `croppedFleetImage_${Date.now()}.${imageType.split('/')[1]}`;

//             croppedCanvas.toBlob(function (blob) {
//                 formDataObject.append('file', blob, uniqueFileName); 
//                 submitForm(formDataObject); 
//             }, imageType);
//         } else {
//             submitForm(formDataObject); 
//         }
//     });

//     function submitForm(formData) {
//         $.ajax({
//             url: "{{env('API_URL')}}createvehichle",
//             method: "POST",
//             processData: false,
//             contentType: false,
//             data: formData,
//             success: function (response) {
//                 if (response['status'] == 200) {
//                     $("#fleet_create_form")[0].reset(); 
//                     $('.modal-title').html('Add Fleet');
//                     $('#flfrm_dis').click(); 
//                     Swal.fire({
//                         position: "top-right",
//                         icon: "success",
//                         title: response['message'],
//                         showConfirmButton: false,
//                         timer: 1500
//                     }).then(function () {
//                         showlist(); 
//                     });
//                 } else if (response['status'] == 400) {
//                     errornotify(response); 
//                 } else if (response['status'] == 500) {
//                     warningClick('Error', response['error'], "danger"); 
//                 } else if (response['status'] == 401) {
//                     unauth(); 
//                 }
//             },
//             error: function (jqXHR, textStatus, errorThrown) {
//                 console.error(textStatus, errorThrown);
//             }
//         });
//     }
// });


$(document).ready(function () {
    const fileInput = document.getElementById('fileInput');
    const image = document.getElementById('edit_imagePreview'); // Make sure this element exists in your HTML
    let cropper;
    let isImageCropped = false; 
    let imageType = 'image/png'; 

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        imageType = file.type || 'image/png';

        reader.onload = function (e) {
            image.src = e.target.result;
            image.style.display = 'block'; // Ensure the image is visible
            if (cropper) {
                cropper.destroy(); 
            }
            cropper = new Cropper(image, {
                aspectRatio: 1.5,
                viewMode: 1,
                autoCropArea: 1, // Ensure full area is used
            });
            isImageCropped = true; 
        };
        reader.readAsDataURL(file);
    });

    $('#fleet_create_sub').on('click', function (e) {
        e.preventDefault(); 
        console.log('hii')
        const formDataObject = new FormData($('#fleet_create_form')[0]);
        formDataObject.append('_token', $('input[name="_token"]').val());
        formDataObject.append('token', getCookie('d_token'));
        formDataObject.append('device_id', 0);
        formDataObject.append('file', $('#hidden_imageName').val());
        if (isImageCropped && cropper) {
            const croppedCanvas = cropper.getCroppedCanvas();
            const uniqueFileName = `croppedFleetImage_${Date.now()}.${imageType.split('/')[1]}`;

            croppedCanvas.toBlob(function (blob) {
                formDataObject.append('file', blob, uniqueFileName); 
                submitForm(formDataObject, 0); 
            }, imageType);
        } else {
            submitForm(formDataObject, 0); 
        }
    });
    // $('#sbtUpdate').on('click', function (e) {
    //     e.preventDefault();
    //     console.log('hii')
    //     const formDataObject = new FormData($('#fleet_create_form')[0]);
    //     formDataObject.append('_token', $('input[name="_token"]').val());
    //     formDataObject.append('token', getCookie('d_token'));
    //     formDataObject.append('device_id', 0);
    //     formDataObject.append('file', $('#hidden_imageName').val());
    //     if (isImageCropped && cropper) {
    //         const croppedCanvas = cropper.getCroppedCanvas();
    //         const uniqueFileName = `croppedFleetImage_${Date.now()}.${imageType.split('/')[1]}`;

    //         croppedCanvas.toBlob(function (blob) {
    //             formDataObject.append('file', blob, uniqueFileName); 
    //             submitForm(formDataObject, 1); 
    //         }, imageType);
    //     } else {
    //         submitForm(formDataObject, 1); 
    //     }
    // });

    function submitForm(formData, forNumber) {
        let text_btn = $('#fleet_create_sub').html();
        if(forNumber){
            $('#sbtUpdate').html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
                        <span class="visually-hidden">Loading...</span>
                    </div>`);
        }else
            $('#fleet_create_sub').html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
                        <span class="visually-hidden">Loading...</span>
                    </div>`);
        }
        $('#fleet_create_sub').html(
        `<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
            <span class="visually-hidden">Loading...</span>
        </div>`);
        
        document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
        const fleetName = document.getElementById('name').value.trim();
        let isValid = true;
        if (fleetName === '') {
            document.querySelector('.invalid-fleet-name').textContent = 'Fleet Name is required.';
            isValid = false;
        } else if (fleetName.length > 30) {
            document.querySelector('.invalid-fleet-name').textContent = 'Fleet Name cannot exceed 30 characters.';
            isValid = false;
        }
    
        // Passengers validation
        const passengers = document.getElementById('passenger').value.trim();
        if (passengers === '') {
            document.querySelector('.invalid-passenger').textContent = 'Passengers count is required.';
            isValid = false;
        } else if (isNaN(passengers) || passengers < 1 || passengers > 99) {
            document.querySelector('.invalid-passenger').textContent = 'Passengers count must be between 1 and 99.';
            isValid = false;
        }
    
        // Luggage validation
        const luggage = document.getElementById('luggage').value.trim();
        if (luggage === '') {
            document.querySelector('.invalid-luggage').textContent = 'Luggage count is required.';
            isValid = false;
        }
    
        // Hand Luggage validation
        const handLuggage = document.getElementById('hand_luggage').value.trim();
        if (handLuggage === '') {
            document.querySelector('.invalid-hand-luggage').textContent = 'Hand Luggage count is required.';
            isValid = false;
        }
    
        // Child Seats validation
        const childSeats = document.getElementById('child').value.trim();
        if (childSeats === '') {
            document.querySelector('.invalid-child').textContent = 'Child Seats count is required.';
            isValid = false;
        }
    
        // Booster validation (optional)
        const booster = document.getElementById('booster').value.trim();
        if (booster !== '' && (isNaN(booster) || booster < 0)) {
            document.querySelector('.invalid-booster').textContent = 'Booster count must be a valid number.';
            isValid = false;
        }
    
        // Order validation (optional)
        const order = document.getElementById('order').value.trim();
        if (order !== '' && (isNaN(order) || order < 0)) {
            document.querySelector('.invalid-order').textContent = 'Order must be a valid number.';
            isValid = false;
        }
          const fileInputs = document.getElementById('fileInput');
         const hiddenImageName = document.getElementById('hidden_imageName');
        if(hiddenImageName == ''){
            if (fileInputs.files.length == 0 ) {
            document.querySelector('.invalid-image').textContent = 'Fleet Image is required.';
            isValid = false;
        } else {
            const file = fileInputs.files[0];
            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
            if (!allowedExtensions.exec(file.name)) {
                document.querySelector('.invalid-order').textContent = 'Invalid file type. Allowed types: jpg, jpeg, png, gif.';
                isValid = false;
            }
        }
        }
        // Fleet Image validation

    
        // If all fields are valid, submit the form
        if (isValid) {
            $.ajax({
            url: "{{env('API_URL')}}createvehichle",
            method: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (response) {
                
                if (response['status'] == 200) {
                    $("#fleet_create_form")[0].reset();
                    $('.modal-title').html('Add Fleet');
                    $('#flfrm_dis').click();
                    if(text_btn == "Save"){
                        Swal.fire({
                            position: "top-right",
                            icon: "success",
                            title: response['message'],
                            showConfirmButton: false,
                            text: 'Fleet has been created successfully',
                            timer: 1500
                        }).then(function () {
                            showlist(); 
                        });
                    }else{
                        Swal.fire({
                            position: "top-right",
                            icon: "success",
                            title: response['message'],
                            showConfirmButton: false,
                            text: 'Fleet has been updated successfully',
                            timer: 1500
                        }).then(function () {
                            showlist(); 
                        });
                    }
                    
                } else if (response['status'] == 400) {
                    errornotify(response); 
                } else if (response['status'] == 500) {
                    warningClick('Error', response['error'], "danger"); 
                } else if (response['status'] == 401) {
                    unauth(); 
                }
                if(forNumber){
                    $('#sbtUpdate').html("Save and Previous");
                    window.location.href = '/bookingSetting';
                }else
                    $('#fleet_create_sub').html("Save");
                    window.location.href = '/fleet';
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus, errorThrown);
            }
        });
        }else{
            $('#fleet_create_sub').html("Save");
        }
        
        
    }
});



      
function changefleetstatus(id){
          const url = 'vehichlestatus';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
          if ($('#flstatus'+id).prop('checked')) {
                formDataObject['isActive'] = 'Active';
            } else {
                formDataObject['isActive'] = 'Inactive';
            }
        //   console.log(formDataObject);
          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      Swal.fire({
             title: 'Are you sure?',
             text: 'You want to change the status!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes',
             cancelButtonText: 'No, cancel!',
           }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax(settings).done(function (response) {
                   if(response['status'] == 200){
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              showlist()
                          });
                       }
                   if(response['status'] == 400){
                       warningClick('Error',response['message'],"danger")
                   }
                   if(response['status'] == 500){
                      warningClick('Error',response['error'],"danger")
                   }
                   if(response['status'] == 401){
                      unauth()
                   }
                  });
               
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
                showlist()
             }
         });
      }

</script>