<script>



$(function () {

        

        showlist()

         

            //Modal Form Trigger

$(document).ready(function() {

        $('#addCustomer').click(function() {

            // alert('king');

            $('#form-modal1').modal('show'); // Ensure that #form-modal1 exists

        });

    });



             // Ajax for Save and Update





            // });

            

             //Reset filter values

             $('#reset_filter').on('click', function(){

                 $('#cus_filter_form')[0].reset()

                 showlist()

             })

    })

    

function showlist() {

    var formDataObject = {

        token: getCookie('d_token'),

        device_id: 0

    };



    if ($.fn.DataTable.isDataTable('#cus-table')) {

        $('#cus-table').DataTable().destroy();

    }



    $('#cus-table').DataTable({

        processing: true,

        searching: true,

        ajax: {

            url: '{{ env('API_URL')}}arealist',

            method: 'POST',

            data: formDataObject,

            dataSrc: "data"

        },

        columns: [

            {

                data: null,

                render: function(data, type, row, meta) {

                    return meta.row + 1;

                }

            },

            { data: 'area' },

            { data: 'pincode' },

            { data: 'p_extra' },

            { data: 'd_extra' },

            // {

            //     data: function(row) {

            //         return `

            //             <div class="input-group">

            //                 <input type="text" class="form-control form-control-30 p_extra" placeholder="" value="${row.p_extra}">

            //                 <div class="input-group-append">

            //                     <button title="Update pickup extra" data-id="${row.id}" data-name="pickup" class="btn btn-sm btn-success btn_icon update_p_extra" type="button">

            //                         <i class="fa fa-check"></i>

            //                     </button>

            //                 </div>

            //             </div>`;

            //     }

            // },

            // {

            //     data: function(row) {

            //         return `

            //             <div class="input-group">

            //                 <input type="text" class="form-control form-control-30 p_extra" placeholder="" value="${row.d_extra}">

            //                 <div class="input-group-append">

            //                     <button title="Update pickup extra" data-id="${row.id}" data-name="pickup" class="btn btn-sm btn-success btn_icon update_p_extra" type="button">

            //                         <i class="fa fa-check"></i>

            //                     </button>

            //                 </div>

            //             </div>`;

            //     }

            // },

            { data: 'status' },

            {

                data: null,

                render: function(data, type, row) {

                    return `

                        <span style="padding: 8px;">

                            <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(${row.id})"></i>

                        </span>

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

                        </span>`;

                }

            },

        ],

        responsive: {

            details: {

                type: 'column',

                target: 'tr'

            }

        }

    });

}



function AssignValues(data){

        $('#customer_id').val(data.id);

        $('#area_name').val(data.area);

        $('#post_code').val(data.pincode);

        $('#pickup_extra').val(data.p_extra);

        $('#drop_extra').val(data.d_extra);

        $('#area_status').val(data.status);

        $('#id').val(data.id);

    }

    

function customeredit(id){

        const url = 'editarea';

          var formDataObject  = {};

          formDataObject['token'] = getCookie('d_token');

          formDataObject['device_id'] = 0;

          formDataObject['customer_id'] = id;

          var settings = {

         "url": "{{ env('API_URL')}}"+url,

         "method": "POST",

         "timeout": 0,

         "headers": {

             "Content-Type": "application/json"

          },

         "data": JSON.stringify(formDataObject),

      };

      $.ajax(settings).done(function (response) {

         if(response['status'] == 200){

              $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");

             $('#form-modal').modal('show');

            AssignValues(response['data'])

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

    

function cus_del(id){

        const url = 'deletearea';

          var formDataObject  = {};

          formDataObject['token'] = getCookie('d_token');

          formDataObject['device_id'] = 0;

          formDataObject['customer_id'] = id;

          var settings = {

         "url": "{{ env('API_URL')}}"+url,

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

        

$('#addsaveBtn').click(function (e) {

    e.preventDefault();



    const url = 'areastore';
    var addDropExtra = $('#add_drop_extra').val().trim();
    if (addDropExtra === '') {
        // alert('Drop Extra Amount is required.');
        warningClick('Required','Drop Extra Amount is required.',"warning");
        $('#add_drop_extra').focus();
        return false; // Stop form submission
    }

    var addpickExtra = $('#add_pickup_extra').val().trim();
    if (addpickExtra === '') {
        // alert('Price Extra Amount is required.');
        warningClick('Required','Pickup Extra Amount is required','warning');
        $('#add_pickup_extra').focus();
        return false; // Stop form submission
    }

    var formdata = new FormData($('#customerForm')[0]); // FormData object



    // Append additional fields

    formdata.append('token', getCookie('d_token'));

    formdata.append('device_id', 0);



    $.ajax({

        data: formdata,

        url: "{{ env('API_URL')}}" + url,

        type: "POST",

        processData: false, // Important for FormData

        contentType: false, // Important for FormData

        dataType: 'json',

        success: function (response) {

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                warningClick('Error', response['error'], "danger");

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {

                Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Created',

                        text: 'Area has been created successfully',

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



$('#saveBtn').click(function (e) {

    e.preventDefault();



    const url = 'areaupdate';
    var pickup_extra = $('#pickup_extra').val().trim();
    if (pickup_extra === '') {
        // alert('Drop Extra Amount is required.');
        warningClick('Required','Pickup Extra Amount is required.',"warning");
        $('#pickup_extra').focus();
        return false; // Stop form submission
    }
    var drop_extra = $('#drop_extra').val().trim();
    if (drop_extra === '') {
        // alert('Drop Extra Amount is required.');
        warningClick('Required','Drop Extra Amount is required.',"warning");
        $('#drop_extra').focus();
        return false; // Stop form submission
    }

    var formdata = new FormData($('#customerFormtest')[0]); // FormData object

    // console.log("Jana VAlue",formdata);
//     for (let [key, value] of formdata.entries()) {
//     console.log("Jana VAlue",key, value);
// }


    // Append additional fields

    formdata.append('token', getCookie('d_token'));

    formdata.append('device_id', 0);


    $.ajax({

        data: formdata,

        url: "{{ env('API_URL')}}" + url,

        type: "POST",

        processData: false, // Important for FormData

        contentType: false, // Important for FormData

        dataType: 'json',

        success: function (response) {

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                warningClick('Error', response['error'], "danger");

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {

                Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Area has been update successfully',

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

$('#search').on('click', function() {



    const url = 'filterarea';

    var formdata = $('#cus_filter_form').serialize();

    var pairs = formdata.split('&');

    var formDataObject = {};



    // Split and decode form data into an object

    for (var i = 0; i < pairs.length; i++) {

        var pair = pairs[i].split('=');

        var key = decodeURIComponent(pair[0]);

        var value = decodeURIComponent(pair[1]);

        formDataObject[key] = value;

    }



    formDataObject['token'] = getCookie('d_token');

    formDataObject['device_id'] = 0;

    if ($.fn.DataTable.isDataTable('#cus-table')) {

        $('#cus-table').DataTable().destroy();

    }

    $('#cus-table').DataTable({

        processing: true,

        searching: true,

        ajax: {

            url: '{{ env('API_URL')}}' + url,

            method: 'POST',

            data: function(d) {

                // Append formDataObject to the request

                return $.extend({}, d, formDataObject);

            },

            dataSrc: "data"  // Assuming the response has a 'data' field

        },

        columns: [

            {

                data: null,

                render: function(data, type, row, meta) {

                    return meta.row + 1; // Serial number

                }

            },

            { data: 'area' },

            { data: 'pincode' },

            { data: 'p_extra' },

            { data: 'd_extra' },

            { data: 'status' },

            {

                data: null,

                render: function(data, type, row) {

                    return `

                        <span style="padding: 8px;">

                            <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(${row.id})"></i>

                        </span>

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

                        </span>`;

                }

            }

        ],

        responsive: {

            details: {

                type: 'column',

                target: 'tr'

            }

        }

    });

});















$(document).ready(function() {

    $('#add_area_name').select2({

        dropdownParent: $('#form-modal1'), 

        placeholder: 'Enter the Area',

        minimumInputLength: 1,

        ajax: {

            url: "{{ env('API_URL')}}getarealocation",

            type: "POST",

            dataType: 'json',

            delay: 400,

            data: function(params) {

                return {

                    search: params.term, 

                    token: formDataObject.token, 

                    device_id: formDataObject.device_id

                };

            },

            processResults: function(response) {

                console.log('Response:', response); 

                const data = response || [];

                if (!Array.isArray(data) || data.length === 0) {

                    console.log('No results found'); 

                    return { results: [] };

                }

                const formattedData = data.map(item => ({

                    id: item.text,

                    text: item.text || item.label  

                }));



                console.log('Formatted Data:', formattedData); 

                return { results: formattedData };

            },

            cache: true,

            error: function(xhr, status, error) {

                console.error('Error fetching data:', error); 

            }

        }

    });

    $('#add_area_name').on('focus', function() {

        $(this).select2('open');

    });

});





//  function validateNumericInput(input) {

//         // Allow only numbers and one decimal point

//         const regex = /^[0-9]*\.?[0-9]*$/;

//         if (!regex.test(input.value)) {

//             input.value = input.value.replace(/[^0-9.]/g, ''); // Remove invalid characters

//         }

//     }



//     // Attach the event listener for both fields

//     document.getElementById('add_pickup_extra').addEventListener('input', function() {

//         validateNumericInput(this);

//     });



//     document.getElementById('add_drop_extra').addEventListener('input', function() {

//         validateNumericInput(this);

//     });



//validation 






// function validateNumericInput(input) {

//         // Allow only numbers and one decimal point

//         const regex = /^[0-9]*\.?[0-9]*$/;

//         if (!regex.test(input.value)) {

//             input.value = input.value.replace(/[^0-9.]/g, ''); // Remove invalid characters

//         }

//     }



//     // Attach the event listener for both fields

//     document.getElementById('pickup_extra').addEventListener('input', function() {

//         validateNumericInput(this);

//     });



//     document.getElementById('drop_extra').addEventListener('input', function() {

//         validateNumericInput(this);

//     });

</script>