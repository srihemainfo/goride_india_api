<script>

// phoneCode()

function AssignValues(data){

    let cleanedDialCode = dial_code_store.replace("+", "");
        let phoneNumber = String(data.phone);
        // Remove prefix if exists
        if (phoneNumber.startsWith(cleanedDialCode)) {
            phoneNumber = phoneNumber.slice(cleanedDialCode.length);
        }

        console.log('phone val:', phoneNumber);

        $('#edit_employee_id').val(data.id);

        $('#edit_user_id').val(data.user_id);

        $('#edit_first_name').val(data.emp_full_name);

        $('#edit_employee_type').val(data.employee_type);

        $('#edit_phone').val(phoneNumber);

        $('#edit_email').val(data.email);

        $('#edit_role_id').val(data.role_id).select2();

    }

    

function edit_employee(id){

            loader.show();

        const url = 'editemployer';

        //   var formDataObject  = {};

        //   formDataObject['token'] = getCookie('d_token');

        //   formDataObject['device_id'] = 0;

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

         

         loader.hide();

      });

    }

    

function showlist(){

    

        var formDataObject  = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        var existingTable = $('#emp-table').DataTable();

            if (existingTable) {

                existingTable.destroy();

            }

        new DataTable('#emp-table', {

    ajax: {

        url: '{{env('API_URL')}}employerlist',

         method: 'POST',

        dataSrc:"data",

        data: formDataObject,

    },

    columns: [

        { 

        data: null,

        render: function(data, type, row, meta) {

          return meta.row + 1;

        }

        },

        { data: 'emp_full_name' },

        { data: 'phone' },

        { data: 'email' },

        { data: 'status' },

        {

            data: null,

            render: function(data, type, row) {

                // Custom rendering logic goes here

                return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;" ><i class="fa fa-lock" style="background: blue; color: #fff; padding: 6px 7px; border-radius: 6px; margin: 0 0 6px 0;" onclick="employeechangepassword(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';

            }

        }

    ],

});

        }

        

function roles(){

    loader.show();

   $.ajax({

        url: "{{env('API_URL')}}roles/role_get",

        type: "POST",

        data: JSON.stringify(formDataObject), // Send the formData object

        contentType: 'application/json',

        dataType: 'json', // Set to false for FormData

        processData: false, // Prevent jQuery from processing the data

        success: function (response) {

            

            if (response.status == true) {

                var select = $('.role_get');

                // Clear any previous options

                select.empty();

                

                // Append a default option

                select.append('<option value="">Select Role</option>');

                

                // Loop through the response data and append each role

                $.each(response.data, function(key, value) {

                    console.log(response.data);

                    select.append('<option value="' + key + '">' + value + '</option>');

                });

                

        

            }

            loader.hide();

        },

        error: function (data) {

            console.log('Error:', data);

        }

    });

    

    

}

        

        

        

$('#emp_search').on('click', function() {

    const url = 'filterepmloyer';

    var formdata = $('#emp_filter').serialize();

    var pairs = formdata.split('&');

    // var formDataObject = {};



    for (var i = 0; i < pairs.length; i++) {

        var pair = pairs[i].split('=');

        var key = decodeURIComponent(pair[0]);

        var value = decodeURIComponent(pair[1]);

        formDataObject[key] = value;

    }

    // formDataObject['token'] = getCookie('d_token');

    // formDataObject['device_id'] = 0;



    // Destroy the existing DataTable before reinitializing

    var existingTable = $('#emp-table').DataTable();

    if (existingTable) {

        existingTable.destroy();

    }



    // Initialize the DataTable

    new DataTable('#emp-table', {

        ajax: {

            url: '{{env('API_URL')}}filterepmloyer',

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

            { data: 'emp_full_name' },

            { data: 'phone' },

            { data: 'email' },

            { data: 'status' },

            {

                data: null,

                render: function(data, type, row) {

                    // Custom rendering logic goes here

                    return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';

                }

            }

        ],

        language: {

        loadingRecords: "Please wait - loading...",

        lengthMenu: "| View _MENU_ records per page",

        zeroRecords: "No Data Found",

        infoEmpty: "No records available",

        infoFiltered: "(filtered from _MAX_ total records)",

        // sProcessing: "<img src='loading.gif'>"

      },

    });

});

    

$(function(){

        showlist()

        roles();

    })

    

$('#add_saveBtn').on('click', function(){

            loader.show(); 

            const url = 'createemployer';

            var formdata = $('#add_employeeForm').serialize();

            var pairs = formdata.split('&');

            // var formDataObject  = {};

            for (var i = 0; i < pairs.length; i++) {

              var pair = pairs[i].split('=');

              var key = decodeURIComponent(pair[0]);

              var value = decodeURIComponent(pair[1]);

              formDataObject[key] = value;

            }

            // formDataObject['token'] = getCookie('d_token');

            // formDataObject['device_id'] = 0;

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

              swalalertsuccess(response['message']);

              $('#add_employeeForm')[0].reset();

            // $('#add_cus_form-modal').modal('hide')

            $('#add_cus_form-modal').css('display','none')

                showlist()

            // Swal.fire({

            //           position: "top-right",

            //           icon: "success",

            //           title: response['message'],

            //           showConfirmButton: false,

            //           timer: 1500

            //       }).then(function() {

            //         location.reload()

            //     });

             }

         if(response['status'] == 400){

            errornotify(response)

         }

         if(response['status'] == 500){

            warningClick('Error',response['error'],"danger")

         }

         if(response['status'] == 401){

            unauth()

         }

         loader.hide();

      });

      })


      var dial_code_store = '+'+ @json($myDial);

    function phoneCode() {
        const url = 'phoneCode';
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
            console.log(response);
            if (response['status'] == 200) {
                $('#country_code').text(response.data);
                $('#country_code_edit').text(response.data);
                dial_code_store = response.data;
                // $('#country_code_whatsapp').val(response.data);
                $('#hidden_phoneCode').val(response.data);
                $('#edit_hidden_phoneCode').val(response.data);
                // $('#edit_country_code').val(response.data);
                // $('#edit_country_code_whatsapp').val(response.data);
                // $('#edit_cus_form-modal').modal('show')
            }
            if (response['status'] == 400) {
                warningClick('Error', response['message'], "danger")
            }
            if (response['status'] == 500) {
                warningClick('Error', response['error'], "danger")
            }
            if (response['status'] == 401) {
                unauth()
            }
        });
    }

      

$('#edit_saveBtn').on('click', function(){

        loader.show();

          const url = 'updateemployer';

        var formdata = $('#edit_employeeForm').serialize();

         var pairs = formdata.split('&');

            // var formDataObject  = {};

            

            for (var i = 0; i < pairs.length; i++) {

              var pair = pairs[i].split('=');

              var key = decodeURIComponent(pair[0]);

              var value = decodeURIComponent(pair[1]);

              formDataObject[key] = value;

            }

            // formDataObject['token'] = getCookie('d_token');

            // formDataObject['device_id'] = 0;

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

            swalalertsuccess(response['message']);

            $('#edit_employeeForm')[0].reset();

            showlist()

            $('#edit_cus_form-modal').modal('hide')

            // Swal.fire({

            //           position: "top-right",

            //           icon: "success",

            //           title: response['message'],

            //           showConfirmButton: false,

            //           timer: 1500

            //       }).then(function() {

            //         location.reload()

            //     });

             }

         if(response['status'] == 400){

            errornotify(response)

         }

         if(response['status'] == 500){

            warningClick('Error',response['error'],"danger")

         }

         if(response['status'] == 401){

            unauth()

         }

         loader.hide();

      });

      })

      

function delete_employee(id){

            const url = 'deleteemployer';

        //   var formDataObject  = {};

        //   formDataObject['token'] = getCookie('d_token');

        //   formDataObject['device_id'] = 0;

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

                    //    swalalertsuccess(response['message']);

                        showlist();

                      Swal.fire({ 

                                 position: "center",

                                 icon: "success",

                                 title: "Deleted",

                                 text: 'Deleted Successfully.',

                                 showConfirmButton: false,

                                 timer: 2000

                             });setTimeout(function() {
                            location.reload();
                        }, 4000);

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

            //  else if (result.dismiss === Swal.DismissReason.cancel) {

            //   //Swal.fire('Cancelled', 'Your data is safe.', 'error');

               

            //   swalalerterror('Your data is safe.');

            //  }

         });

        }

        

$('#reset_emp_filter').on('click', function(){

          $("#emp_filter")[0].reset();

          showlist()

      })

      

// Ajax for Password change

$('#paswordsaveBtn').click(function (e) {

    e.preventDefault();

   // Validation
var password = $('#change_password').val().trim();
var c_password = $('#change_password_confirmation').val().trim();

// Check if password is empty
if (password === '') {
    warningClick('Required', 'Password is required', 'warning');
    $('#change_password').focus();
    return false;
}

// Check if confirm password is empty
if (c_password === '') {
    warningClick('Required', 'Confirm Password is required', 'warning');
    $('#change_password_confirmation').focus();
    return false;
}

// Check if password has at least 8 and at most 20 characters
if (password.length < 8 || password.length > 20) {
    warningClick('Invalid Length', 'Password must be between 8 to 20 characters', 'warning');
    $('#change_password').focus();
    return false;
}

// Check if confirm password has at least 8 and at most 20 characters
if (c_password.length < 8 || c_password.length > 20) {
    warningClick('Invalid Length', 'Confirm Password must be between 8 to 20 characters', 'warning');
    $('#change_password_confirmation').focus();
    return false;
}

// Check if passwords match
if (password !== c_password) {
    warningClick('Mismatch', 'Password and Confirm Password do not match', 'warning');
    $('#change_password_confirmation').focus();
    return false;
}

    const url = 'passwordchange';

    const form = $('#changePasswordForm')[0]; // Get the form element

    const formData = new FormData(form); // Create a FormData object with the form data

    

      formData.forEach(function(value, key) {

        formDataObject[key] = value;

    });

    console.log(formDataObject);



    //Add additional data to formData

    formData.append('token', getCookie('d_token'));

    formData.append('device_id', 0);



    $.ajax({

        url: "{{env('API_URL')}}" + url,

        type: "POST",

        data: JSON.stringify(formDataObject), // Send the formData object

        contentType: 'application/json',

        dataType: 'json', // Set to false for FormData

        processData: false, // Prevent jQuery from processing the data

        success: function (response) {

            // Clear error messages

            $('.invalid-password').text('');



            // Handle validation errors (status 400)

            if (response.status == 400 && response.errors) {

                if (response.errors.change_password.length == 1) {

                    $('.invalid-password').text(response.errors.change_password[0]);

                } else if (response.errors.password.length > 1) {

                    $('.invalid-password').html(response.errors.change_password[0] + '<br />' + response.errors.change_password[1]);

                }

            }

            // Handle error without validation (status 400)

            if (response.status == 400 && !response.errors) {

               //Swal.fire("Error", "Password not changed", "error");

                swalalerterror('Password not changed')

                

            }

            // Handle success (status 200)

            if (response.status == 200) {

                $('#changePasswordForm').trigger("reset");

                $('#form-modal').modal('hide');

                if (response.isUpdated) {

                    // swalalertsuccess('Password changed successfully');

                    

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Password Updated successfully',

                        showConfirmButton: false,

                        timer: 3000

                    });
                    setTimeout(function() {
                            location.reload();
                        }, 4000);

                }

            }

        },

        error: function (data) {

            console.log('Error:', data);

        }

    });

});



//password modal show

function employeechangepassword(id){

            loader.show(); 

        var url ='passwordShow';

        //   var formDataObject  = {};

        //   formDataObject['token'] = getCookie('d_token');

        //   formDataObject['device_id'] = 0;

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

         if(response['status'] == 200){

            AssignValues1(response['data'])

            $('#form-modal').modal('show')

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

         loader.hide();

      });

    }

        

function AssignValues1(data){

        //console.log(data)

        $('#password_user_id').val(data.user_id);

    }

</script>

