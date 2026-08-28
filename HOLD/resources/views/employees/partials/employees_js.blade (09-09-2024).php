<script>
  
function AssignValues(data){
        console.log(data)
        $('#edit_employee_id').val(data.id);
        $('#edit_user_id').val(data.user_id);
        $('#edit_first_name').val(data.emp_full_name);
        $('#edit_employee_type').val(data.employee_type);
        $('#edit_phone').val(data.phone);
        $('#edit_email').val(data.email);
        if(data.is_admin == "1"){
            $('#edit_is_admin').prop('checked', true);
        }    
    }
    
function edit_employee(id){
        const url = 'editemployer';
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
        
$('#emp_search').on('click', function() {
    const url = 'filterepmloyer';
    var formdata = $('#emp_filter').serialize();
    var pairs = formdata.split('&');
    var formDataObject = {};

    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        var key = decodeURIComponent(pair[0]);
        var value = decodeURIComponent(pair[1]);
        formDataObject[key] = value;
    }
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

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
        zeroRecords: "Nothing found - sorry",
        infoEmpty: "No records available",
        infoFiltered: "(filtered from _MAX_ total records)",
        // sProcessing: "<img src='loading.gif'>"
      },
    });
});
    
$(function(){
        showlist()
    })
    
$('#add_saveBtn').on('click', function(){
          const url = 'createemployer';
        var formdata = $('#add_employeeForm').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
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
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
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
      });
      })
      
$('#edit_saveBtn').on('click', function(){
          const url = 'updateemployer';
        var formdata = $('#edit_employeeForm').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
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
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
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
      });
      })
      
function delete_employee(id){
            const url = 'deleteemployer';
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
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              location.reload()
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
        
$('#reset_emp_filter').on('click', function(){
          $("#emp_filter")[0].reset();
          showlist()
      })
      
// Ajax for Password change
$('#paswordsaveBtn').click(function (e) {
    e.preventDefault();
    
    const url = 'passwordchange';
    const form = $('#changePasswordForm')[0]; // Get the form element
    const formData = new FormData(form); // Create a FormData object with the form data

    // Add additional data to formData
    formData.append('token', getCookie('d_token'));
    formData.append('device_id', 0);

    $.ajax({
        url: "{{env('API_URL')}}" + url,
        type: "POST",
        data: formData, // Send the formData object
        dataType: 'json',
        contentType: false, // Set to false for FormData
        processData: false, // Prevent jQuery from processing the data
        success: function (response) {
            // Clear error messages
            $('.invalid-password').text('');

            // Handle validation errors (status 400)
            if (response.status == 400 && response.errors) {
                if (response.errors.password.length == 1) {
                    $('.invalid-password').text(response.errors.password[0]);
                } else if (response.errors.password.length > 1) {
                    $('.invalid-password').html(response.errors.password[0] + '<br />' + response.errors.password[1]);
                }
            }
            // Handle error without validation (status 400)
            if (response.status == 400 && !response.errors) {
                Swal.fire("Error", "Password not changed", "error");
            }
            // Handle success (status 200)
            if (response.status == 200) {
                $('#changePasswordForm').trigger("reset");
                $('#form-modal').modal('hide');
                if (response.isUpdated) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Password changed successfully',
                        showConfirmButton: false,
                        timer: 2000
                    });
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
        var url ='passwordShow';
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
      });
    }
        
function AssignValues1(data){
        //console.log(data)
        $('#password_user_id').val(data.user_id);
    }
</script>
