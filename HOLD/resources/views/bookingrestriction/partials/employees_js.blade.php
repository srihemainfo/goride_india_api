<script>
  
    function AssignValues(data){
        console.log(data)
        $('#edit_employee_id').val(data.id);
        $('#edit_caption').val(data.caption);
        $('#edit_recurring').val(data.recurring);
        $('#edit_from').val(data.from);
        $('#edit_to').val(data.to);
    }
    
    function edit_employee(id){
        const url = 'bookingrestrictionedit';
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
        url: '{{env('API_URL')}}bookingrestriction',
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
        { data: 'caption' },
        { data: 'recurring' },
        { data: 'from' },
        { data: 'to' },
        {
            data: null,
            render: function(data, type, row) {
                // Custom rendering logic goes here
                return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';
            }
        }
    ],
});
        }
        
    $('#emp_search').on('click', function() {
    const url = 'bookingrestrictionfilter';
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
            url: '{{env('API_URL')}}bookingrestrictionfilter',
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
            { data: 'caption' },
            { data: 'recurring' },
            { data: 'from' },
            { data: 'to' },
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
          const url = 'bookingrestrictionstore';
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
          const url = 'bookingrestrictionupdate';
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
          const url = 'bookingrestrictiondelete';
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
      
    
    
  // Get the current date and time in the required format (YYYY-MM-DDTHH:MM)
  let currentDate = new Date();
  let year = currentDate.getFullYear();
  let month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Add leading 0
  let day = String(currentDate.getDate()).padStart(2, '0');
  let hours = String(currentDate.getHours()).padStart(2, '0');
  let minutes = String(currentDate.getMinutes()).padStart(2, '0');

  // Combine into the datetime-local format (YYYY-MM-DDTHH:MM)
  let defaultDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

  // Set the default value and minimum date/time for the input field
  let inputField = document.getElementById('txtDateTo');
  let inputField1 = document.getElementById('txtDateFrom');
  inputField.value = defaultDateTime;   
  inputField1.value = defaultDateTime;   
  inputField1.min = defaultDateTime;   
  inputField.min = defaultDateTime;   

    
</script>
