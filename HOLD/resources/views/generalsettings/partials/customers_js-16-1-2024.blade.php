<script>
$(document).ready(function(){
    
  showlist();  
  countrylist();
});    

function showlist() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var existingTable = $('#emp-table').DataTable();
    if (existingTable) {
        existingTable.destroy();
    }
    new DataTable('#emp-table', {
        ajax: {
            url: "{{ env('API_URL') }}generalsetting",
            method: 'POST',
            dataSrc: "data",
            data: formDataObject,
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'company_name' },
            { data: 'contact_number' },
            { data: 'email' },
            { data: 'website_prefix' },
            {
                data: 'logo',
                render: function(data, type, row) {
                    return '<img src="' + data + '" alt="Logo" style="width:50px;height:50px;"/>';
                }
            },
            {
                data: 'favicon',
                render: function(data, type, row) {
                    return '<img src="' + data + '" alt="Favicon" style="width:30px;height:30px;"/>';
                }
            },
            {
                data: 'website_url',
                render: function(data, type, row) {
                    return '<a href="' + data + '" target="_blank">' + data + '</a>' +
                           ' <i class="fa-solid fa-copy" style="cursor: pointer; color: blue; margin-left: 5px;" onclick="copyToClipboard(\'' + data + '\')" title="Copy URL"></i>';
                }
            },
            {
                data: 'id',
                render: function(data, type, row) {
                    // return     '<span style="padding: 8px;" onclick="window.location.href="articleset/'+data+'"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0 0 6px 0;"  ></i>Edit Article</span>' +
                    // '&nbsp;<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0 0 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span>' +
                    //       '&nbsp;<span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';
                    
                    
        //             return  '<button style="padding: 8px;" onclick="window.location.href=\'/articleset/' + data + '\'"><i class="fa-regular fa-pen-to-square" style="background: #182697;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0 0 6px 0;"></i>Edit Article</button>' +
        // '&nbsp;<button style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0 0 6px 0;" onclick="edit_employee(' + row.id + ')"></i></button>' +
        // '&nbsp;<button style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></button>';
        
        
        
        return `
        <a class="btn text-danger btn-sm" style="cursor: pointer; font-size: 16px;" onclick="edit_employee(${row.id})">
                                    <span class="fa fa-pencil-square-o" style="color: #001e1e;">&nbsp;Edit</span>
                                </a>
                                
                                
                                &nbsp;
                                
                                 <a class="btn text-danger btn-sm" style="cursor: pointer; font-size: 16px;" href="articleset/${btoa(encryptData(row.id))}">
    <span class="fa fa-pencil-square-o" style="color: #166969;">&nbsp;Article</span>
</a>

                                
                                
           &nbsp;
        
        <a class="btn text-danger btn-sm" style="cursor: pointer; font-size: 16px;" onclick="delete_employee(${row.id})">
                                    <span class="fa fa-trash" style="color: #df0707;">&nbsp;</span>
                                </a>`;

                }
            }
        ],
    });
}

// Function to copy text to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
       // alert('URL copied to clipboard!');
        Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Copied',
                        text: 'URL copied to clipboard!',
                        showConfirmButton: false,
                        timer: 2000,
                    });
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}



$('#saveBtn').click(function (e) {
    e.preventDefault();
    $('#saveBtn').html(
        `<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
            <span class="visually-hidden">Loading...</span>
        </div>`);
    const url = 'generalstore';
    var formdata = new FormData($('#formSettingsGeneral')[0]); // FormData object

    // Append additional fields
    formdata.append('token', getCookie('d_token'));
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}" + url,
        type: "POST",
        processData: false, // Important for FormData
        contentType: false, // Important for FormData
        dataType: 'json',
        success: function (response) {
            $('#saveBtn').html('<i class="fa fa-save"></i>&nbsp; Save');
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    window.location.reload();
                });
            }

        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

// $('#edit_saveBtn').on('click', function(){
//         const url ='generalupdate';
//         var formdata = $('#edit_employeeForm').serialize();
//          var pairs = formdata.split('&');
//             // var formDataObject  = {};
            
//             for (var i = 0; i < pairs.length; i++) {
//               var pair = pairs[i].split('=');
//               var key = decodeURIComponent(pair[0]);
//               var value = decodeURIComponent(pair[1]);
//               formDataObject[key] = value;
//             }
//             // formDataObject['token'] = getCookie('d_token');
//             // formDataObject['device_id'] = 0;
//         var settings = {
//          "url": "{{env('API_URL')}}"+url,
//          "method": "POST",
//          "timeout": 0,
//          "headers": {
//              "Content-Type": "application/json"
//           },
//          "data": JSON.stringify(formDataObject),
//       };
//       $.ajax(settings).done(function (response) {
//          if(response['status'] == 200){
//              swalalertsuccess(response['message']);
//              location.reload()
//             // Swal.fire({
//             //           position: "top-right",
//             //           icon: "success",
//             //           title: response['message'],
//             //           showConfirmButton: false,
//             //           timer: 1500
//             //       }).then(function() {
//             //         location.reload()
//             //     });
//              }
//          if(response['status'] == 400){
//             errornotify(response)
//          }
//          if(response['status'] == 500){
//             warningClick('Error',response['error'],"danger")
//          }
//          if(response['status'] == 401){
//             unauth()
//          }
//       });
//       })

$('#edit_saveBtn').on('click', function() {
    $('#edit_saveBtn').html(
        `<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
            <span class="visually-hidden">Loading...</span>
        </div>`);
    const url = 'generalupdate';
    var formdata = new FormData($('#edit_employeeForm')[0]);
    formdata.append('token', getCookie('d_token'));
    formdata.append('device_id', 0);

    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "processData": false, // Don't process the data
        "contentType": false, // Let jQuery set the content type
        "data": formdata, // Use the FormData object directly
    };
    $.ajax(settings).done(function(response) {
        console.log(response);
        $('#edit_saveBtn').html(`<i class="fa fa-save"></i>&nbsp; Save`);
        if (response['status'] == 200) {
            swalalertsuccess(response['message']);
            location.reload();
        }
        if (response['status'] == 400) {
            errornotify(response);
        }
        if (response['status'] == 500) {
            warningClick('Error', response['error'], "danger");
        }
        if (response['status'] == 401) {
            unauth();
        }
    });
});

      
function edit_employee(id){
    $('#saveBtn').html(
        `<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
            <span class="visually-hidden">Loading...</span>
        </div>`);
        const url = 'generalsettingedit';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['emp_id'] = id;
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
          $('#saveBtn').html(`<i class="fa fa-save"></i>&nbsp; Save`);
         
         if(response['status'] == 200){
            AssignValues(response['data'])
            $('#edit_cus_form-modal').modal('show')
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
function countrylist(){
        const url = 'countrylists';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
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
          console.log(response);
         if(response['status'] == 200){
            AssignValues1(response['data'])
            // $('#edit_cus_form-modal').modal('show')
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
    
function AssignValues(data){
     //   alert(data);
        $('#edit_google_translate').val(data.google_translate);
        $('#edit_cookieConsent').val(data.cookie_consent);
        $('#edit_generalid').val(data.id);
        $('#edit_contact_number').val(data.contact_number);
        $('#edit_email').val(data.email);
        $('#edit_company_address').val(data.company_address);
        $('#edit_license_number').val(data.license_number);
        $('#edit_company_name').val(data.company_name);
        $('#edit_trading_name').val(data.trading_name);
        $('#edit_google_api_key').val(data.google_api_key);
        $('#edit_licencenumber').val(data.license_number);
        $('#edit_lincenceedby').val(data.licensed_by);
        $('#edit_licencenumber_refrence').val(data.license_referrer_link);
        $('#edit_bgColorTopFooter').val(data.topbar_footer_bgcolor);
        $('#edit_textColorTopFooter').val(data.topbar_footer_text_color);
        $('#edit_bgColorMenu').val(data.menu_background_color);
        $('#edit_textColorMenu').val(data.menu_text_color);
        $('#edit_site_currencies').val(data.site_currency);
        $('#edit_imagePreview').attr('src', data.logo);
        $('#edit_imagePreviewFavicon').attr('src', data.favicon);
        $('#edit_domain_name').val(data.domain_name);
        $('#edit_model_id').val(data.id);
        $('#edit_whatsapp_number').val(data.whatsapp_number);
        $('#edit_website_prefix').val(data.website_prefix);
        $('#edit_site_country').val(data.country);
        
        
    }    
function AssignValues1(data){
    // console.log(data);
     var countrySelect1 = $('#add_site_country');
    countrySelect1.empty(); // Clear any existing options

    // Clear and populate the second select field (e.g., 'edit_site_country')
    var countrySelect2 = $('#edit_site_country');
    countrySelect2.empty(); // Clear any existing options

    // Loop through data and append options to both select fields
    $.each(data, function(index, country) {
        var option = $('<option>', {
            value: country.sortname, // Use country sortname as value
            text: country.name       // Display country name
        });

        // Append the same option to both selects
        countrySelect1.append(option.clone()); // Clone the option to avoid reference issues
        countrySelect2.append(option);
    });
    }       

function validateContactNumber(input) {
    let value = input.value;
    // Allow only numbers and a single leading '+'
    input.value = value.replace(/(?!^\+)[^\d]/g, ''); 
}

// delete employe
function delete_employee(id){
        const url = 'generaldelete';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['emp_id'] = id;
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
                       swalalertsuccess('Website Deleted Successfully.');
                    //   Swal.fire({ 
                    //              position: "top-right",
                    //              icon: "success",
                    //              title: response['message'],
                    //              showConfirmButton: false,
                    //              timer: 1500
                    //          }).then(function() {
                    //           location.reload()
                    //       });
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
               //Swal.fire('Cancelled', 'Your data is safe.', 'error');
               
               swalalerterror('Your data is safe.');
             }
             location.reload()
         });
        }

</script>