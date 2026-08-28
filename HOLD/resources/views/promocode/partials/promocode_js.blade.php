<script>
$(document).ready(function(){
   
  
  vehicletable();
});    


function vehicletable() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    
    if ($.fn.DataTable.isDataTable('#data-table')) {
        $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
        ajax: {
            url: '{{env('API_URL')}}PromoCodeView',
            method: 'POST',
            dataSrc: 'data',  
            data: function(d) {
                return $.extend({}, d, formDataObject);  
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;  
                }
            },
           { data: 'code' },
            { data: 'minvalue' },
            { data: 'maxvalue' },
            { data: 'fromdate' },
            { data: 'todate' },
            { data: 'type' },
            { data: 'values' },

            {
                data: null,
                render: function(data, type, row) {
                    return `
                       
                            <!-- Edit Button -->
                        <button type="button" class="btn btn-outline-secondary footable-edit "  data-id="${row.id}"
                        data-bs-toggle="modal" 
                        data-bs-target="#editor-modal" 
                        data-id="123">
                    <span class="fa-solid fa-edit" aria-hidden="true"></span>
                </button>


                            <!-- Delete Button -->
                            <button class="btn btn-outline-danger footable-delete" 
                                data-id="${row.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                       
                    `;
                }
            }
        ],
        destroy: true,  
        responsive: true,  
        autoWidth: false   
    });
}

// function vehicletable() {
//     var formDataObject = {};
//     formDataObject['token'] = getCookie('d_token');
//     formDataObject['device_id'] = 0;
    
//     if ($.fn.DataTable.isDataTable('#data-table')) {
//         $('#data-table').DataTable().destroy();
//     }

//     $('#data-table').DataTable({
//         ajax: {
//             url: '{{env('API_URL')}}PromoCodeView',
//             method: 'POST',
//             dataSrc: 'data',
//             data: function(d) {
//                 return $.extend({}, d, formDataObject);  
//             }
//         },
//         columns: [
//             { 
//                 data: null,
//                 render: function(data, type, row, meta) {
//                     return meta.row + 1;  
//                 }
//             },
//             { data: 'code' },
//             { data: 'minvalue' },
//             { data: 'maxvalue' },
//             { data: 'fromdate' },
//             { data: 'todate' },
//             { data: 'type' },
//             { data: 'values' },

//             {
//                 data: null,
//                 render: function(data, type, row) {
//                     return `
//                          <button type="button" class="btn btn-outline-secondary footable-edit me-3"  data-id="${row.id}"
//                         data-bs-toggle="modal" 
//                         data-bs-target="#editor-modal" 
//                         data-id="123">
//                     <span class="fa-solid fa-edit" aria-hidden="true"></span>
//                 </button>


//                             <!-- Delete Button -->
//                             <button class="btn btn-outline-danger footable-delete" 
//                                 data-id="${row.id}">
//                                 <i class="fa fa-trash"></i>
//                             </button>
//                     `;
//                 }
//             }
//         ],
//         destroy: true,  
//         responsive: true,  
//         autoWidth: false,
//         language: {
//             emptyTable: "No records found"  
//         }
//     });
// }



  $('#primaryBtn').click(function (e) {
    e.preventDefault();


    var form = $('#VehicleForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    var formdata = new FormData(form);


    formdata.append('token', getCookie('d_token'));
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}PromoCodeStore",
        type: "POST",
        processData: false,  
        contentType: false,  
        dataType: 'json',
        success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
        else: function (xhr, status, error) {
            // Better error logging
            console.log('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! ' + xhr.responseText,
            });
        }
    });
});


$(document).on('click', '.footable-edit', function() {
    var id = $(this).data('id'); 

    $.ajax({
        url: '{{env('API_URL')}}PromoCodeEdit/edit/' + id, 
        method: 'POST', 
        data: {
            id: id,   
            token: getCookie('d_token'), 
            device_id: 0 
        },
        success: function(response) {
            if (response.status === 200) {
                var data = response.data; 
                console.log('API Data: ', data);
                $('#editcode').val(data.code);
                $('#editmin_value').val(data.minvalue);
                $('#editmax_value').val(data.maxvalue);
                $('#editfrom_date').val(data.fromdate);
                $('#editto_date').val(data.todate);
               $('#edittype').val(data.type).trigger('change').trigger('select');
                $('#editvalues').val(data.values);
            } else {
                alert('Failed to fetch the data for editing.');
            }
        },
        error: function(error) {
            console.error('Error:', error);
            alert('An error occurred while fetching data.');
        }
    });
}); 


$(document).on('click', '.footable-edit', function() {
    editVehicleId = $(this).data('id'); 
    $('#editVehicleId').val(editVehicleId);  
});

 $('#UpdateprimaryBtn').click(function (e) {
    e.preventDefault();
var vehicleId = $('#editVehicleId').val(); 
    var form = $('#EditVehicleForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    var formdata = new FormData(form);
    formdata.append('token', getCookie('d_token')); 
    formdata.append('device_id', 0);


    var url = `{{env('API_URL')}}PromoCodeUpdate/update/` + vehicleId;

    $.ajax({
        data: formdata,
        url: url,
        type: "POST",
        processData: false,  // Important for FormData
        contentType: false,  // Important for FormData
        dataType: 'json',
        success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Data has been updated successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been updated successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
        error: function (xhr, status, error) {
            // Better error logging
            console.log('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! ' + xhr.responseText,
            });
        }
    });
});


$(document).on('click', '.footable-delete', function() {
    var id = $(this).data('id');
    console.log("Delete button clicked with ID:", id);

    // Use Swal for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with deletion
            $.ajax({
                url: '{{env('API_URL')}}PromoCodeDelete/delete/' + id,
                type: 'POST', // Changed to DELETE for deletion
                data: {
                    token: getCookie('d_token'),
                    device_id: 0
                },
                 success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Promo Code deleted successfully!") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been deleted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Promo Code deleted successfully!") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been deleted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText); // Log any error for debugging
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while trying to delete the item.',
                    });
                }
            });
        }
    });
});


    
</script>