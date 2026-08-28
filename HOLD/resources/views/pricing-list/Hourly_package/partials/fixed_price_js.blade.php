
<script>
$(document).ready(function() {
    
    showlist();

});

function showlist() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

    if ($.fn.DataTable.isDataTable('#data-table')) {
        $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
        ajax: {
            url: '{{env('API_URL')}}hourlypackage',
            method: 'POST',
            dataSrc: function (json) {
                // Log the full response for debugging
                console.log('Server Response:', json);

                // Access the data using the 'car_fares' key
                if (json && Array.isArray(json.car_fares)) {
                    return json.car_fares;
                } else {
                    console.error('Error: The `car_fares` key is missing or is not an array.');
                    return []; // Return an empty array to prevent DataTables from breaking
                }
            },
            data: function(d) {
                return $.extend({}, d, formDataObject);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        },
        columns: [
            { 
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;  
                }
            },
            { data: 'Distance' },
            { data: 'Hours' },
            { data: 'Saloon' },
            { data: 'Executive' },
            { data: 'MPV' },
            { data: 'seater' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
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







//     function showlist() {
//         var formDataObject = {
//             token: getCookie('d_token'),
//             device_id: 0
//         };

//         // Make an AJAX request to fetch the list
//         $.ajax({
//             url: '{{env('API_URL')}}hourlypackage',
//             method: 'POST', // Consider using GET if you're only fetching data
//             data: formDataObject,
//             success: function(response) {
                
//                 if (response.status === 200) {
//                     var listCarFares = response.car_fares;
//                     console.log(listCarFares);

//                     // Clear existing rows
//                     $('table tbody').empty();

//                     // Iterate through each item in the list
//                     listCarFares.forEach(function(item) {
//                         let row = `
//                             <tr data-id="${item.id}">
//                                 <td>${item.id}</td>
//                                 <td>${item.Distance}</td>
//                                 <td>${item.Hours}</td>
//                                 <td>${item.Saloon}</td>
//                                 <td>${item.Executive}</td>
//                                 <td>${item.MPV}</td>
//                                 <td>${item.seater}</td>
//                                                                 <td>
//                      <td class="footable-editing footable-last-visible">
//     <div class="btn-group btn-group-sm" role="group">
//         <!-- Edit Button -->
//         <button type="button" class="btn btn-outline-secondary footable-edit" 
//             data-bs-toggle="modal" 
//             data-bs-target="#editor-modal" 
//             data-id="${item.id}">
//             <span class="fa-solid fa-edit" aria-hidden="true"></span>
//         </button>

//         <!-- Delete Button (Adjusted for consistent sizing) -->
//         <button id="delete" class="btn btn-outline-danger footable-delete" 
//             data-id="${item.id}">
//             <i class="fa fa-trash"></i>
//         </button>
//     </div>
// </td>
//                             </tr>
//                         `;

//                         // Append the row to the table body
//                         $('table tbody').append(row);
//                     });

//                     // Handle permissions
//                     if (!response.permissions.IS_CREATABLE) {
//                         $('.create-button').hide();
//                     }
//                     if (!response.permissions.IS_UPDATABLE) {
//                         $('.editFleet').hide();
//                     }
//                     if (!response.permissions.IS_DELETABLE) {
//                         $('.deleteFleet').hide();
//                     }

                   
//                 } else {
//                     console.error('Unexpected response status:', response.status);
//                 }
//             },
//             error: function(error) {
//                 console.error('Error fetching data', error);
//             }
//         });
//     }



// store
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
        url: "{{env('API_URL')}}hourlypackagestore",
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

                if (response.message =="Package added successfully!") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Package added successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Package added successfully!") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Package added successfully!',
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
        url: '{{env('API_URL')}}hourlypackagedit/' + id, 
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
                $('#editdistancePackage').val(data.Distance );
                $('#edithoursPackage').val(data.Hours);
                $('#editvehicle1').val(data.Saloon);
                $('#editvehicle2').val(data.Executive);
                $('#editvehicle3').val(data.MPV);
                $('#editvehicle4').val(data.seater);
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

    var form = $('#EditVehicleForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    var formdata = new FormData(form);
    formdata.append('token', getCookie('d_token')); 
    formdata.append('device_id', 0);


    var vehicleId = $('#editVehicleId').val(); 

    if (!vehicleId) {
        console.log('Vehicle ID not found!');
        return;
    }

   var url = `{{env('API_URL')}}HourlyPackageUpdate/` + vehicleId;



    $.ajax({
        data: formdata,
        url: url,
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
                } else if(response.message =="Data has been updated successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been updated successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
        error: function (xhr, status, error) {
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

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => { // Corrected: this is where the block should start
        if (result.isConfirmed) {
            $.ajax({
                url: '{{env('API_URL')}}hourlypackagedelete/' + id,
                type: 'POST', 
                data: {
                    token: getCookie('d_token'),
                    device_id: 0
                },
                success: function(response) {
                    if (response.status === 200) {
                        $('tr[data-id="' + id + '"]').remove();
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Item deleted successfully.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        window.location.reload(); // Correct place for page reload
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete item.',
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText); 
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

//  $(document).on('click', '.footable-delete', function() {
//     var id = $(this).data('id');
//     console.log("Delete button clicked with ID:", id);

//     if (confirm('Are you sure you want to delete this item?')) {
//         $.ajax({
//             url: '{{env('API_URL')}}hourlypackagedelete/' + id,
//             type: 'POST', 
//             data: {
//                 token: getCookie('d_token'),
//                 device_id: 0
//             },
//           success: function(response) {
//                 if (response.status === 200) {
//                     $('tr[data-id="' + id + '"]').remove();
//                     Swal.fire({
//                         position: 'top-end',
//                         icon: 'success',
//                         title: 'Deleted',
//                         text: 'Item deleted successfully.',
//                         showConfirmButton: false,
//                         timer: 1500
//                     });
//                 } else {
//                     Swal.fire({
//                         icon: 'error',
//                         title: 'Error',
//                         text: 'Failed to delete item.',
//                     });
//                 }
//             },
//             error: function(xhr, status, error) {
//                 console.error('Error:', xhr.responseText); 
//                 Swal.fire({
//                     icon: 'error',
//                     title: 'Error',
//                     text: 'An error occurred while trying to delete the item.',
//                 });
//             }
//         });
//     }
// });
</script>
