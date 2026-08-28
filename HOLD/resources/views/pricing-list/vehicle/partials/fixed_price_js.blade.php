<script>
$(document).ready(function(){
   
 
  vehiclelist();
  Editvehiclelist();
  vehicletable();
});    


//  function vehicletable() {
//         var formDataObject = {
//             token: getCookie('d_token'),
//             device_id: 0
//         };

//         $.ajax({
//             url: '{{env('API_URL')}}VehiclePricingView',
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
//                                 <td>${item.priority}</td>
//                                 <td>${item.vehicle_name}</td>
//                                 <td>${item.passengers}</td>
//                                 <td>${item.small_lugg}</td>
//                                 <td>${item.large_lugg}</td>
//                                 <td>${item.child_seat}</td>
//                                 <td>${item.price}</td>
//                                  <td class="footable-editing footable-last-visible">
//                                     <div class="btn-group btn-group-sm" role="group">
//                                         <!-- Edit Button -->
//                                         <button type="button" class="btn btn-outline-secondary footable-edit" 
//                                             data-bs-toggle="modal" 
//                                             data-bs-target="#editor-modal" 
//                                             data-id="${item.id}">
//                                             <span class="fa-solid fa-edit" aria-hidden="true"></span>
//                                         </button>
                                
//                                         <!-- Delete Button (Adjusted for consistent sizing) -->
//                                         <button id="delete" class="btn btn-outline-danger footable-delete" 
//                                             data-id="${item.id}">
//                                             <i class="fa fa-trash"></i>
//                                         </button>
//                                     </div>
//                                 </td>

//                         `;

//                         // Append the row to the table body
//                         $('table tbody').append(row);
//                     });

//                     // Handle permissions
//                     if (!response.permissions.IS_CREATABLE) {
//                         $('.create-button').hide();
//                     }
//                     if (!response.permissions.IS_UPDATABLE) {
//                         $('.footable-edit').hide();
//                     }
//                     if (!response.permissions.IS_DELETABLE) {
//                         $('.footable-delete').hide();
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



function vehicletable() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

    if ($.fn.DataTable.isDataTable('#data-table')) {
        $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
        ajax: {
            url: '{{env('API_URL')}}VehiclePricingView',
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
            { data: 'priority' },
            { data: 'vehicle_name' },
            { data: 'passengers' },
            { data: 'small_lugg' },
            { data: 'large_lugg' },
            { data: 'child_seat' },
            { data: 'price' },
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










function AssignValues(data) {
   $('#pricingid').val(data.id);
    $('#priceDecimal').val(data.price_decimal);
    $('#DropOffMinimumPrice').val(data.minprice_dropoff);
    $('#ChildSeatPrice').val(data.childseat_price);
    $('#selCardPaymentPriceType').val(data.cardpayment_pricetype).trigger('change').trigger('select');
    $('#CardPaymentPrice').val(data.cardpayment_percentage);
}




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
    var select = $('#selectVehicle');
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
    $('#selectVehicle').val(vehicleName).trigger('change');
}



function Editvehiclelist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}EditVehiclelist',
        method: 'POST', // Consider using GET if you're only fetching data
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                // Call the function to populate the select element
                editPopulateSelect(data);
                // Call the function to set the selected vehicle value
                EditVehicleValues('Saloon'); // Change 'Saloon' to the actual vehicle name if necessary
                console.log(response);
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

function editPopulateSelect(data) {
    var select = $('#editselectVehicle');
    select.empty(); // Clear existing options

    // Add default option
    select.append('<option value="">Select Vehicle</option>');

    if (Array.isArray(data) && data.length > 0) {
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

function EditVehicleValues(vehicleName) {
    // Find and select the option with the specified vehicle name
    $('#editselectVehicle').val(vehicleName).trigger('change');
}

  
  $('#primaryBtn').click(function (e) {
    e.preventDefault();

    // Check if form exists before using it
    var form = $('#VehicleForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    // Initialize FormData after confirming the form exists
    var formdata = new FormData(form);

    // Append additional data to FormData after initialization
    formdata.append('token', getCookie('d_token')); // Assuming getCookie is a function that gets the cookie value
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}VehicleStore",
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
        url: '{{env('API_URL')}}vehiclepricing/edit/' + id, 
        method: 'POST',
        data: {
            id: id,  
            token: getCookie('d_token'), 
            device_id: 0 
        },
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                console.log('API Response Data: ', data);
                $('#editPriority').val(data.priority);  
                $('#editselectVehicle').val(data.vehicle_name);  
                $('#editVehicleImage').val(''); 
                $('#editPassengerCapacity').val(data.passengers);  
                $('#editSmallLuggageCapacity').val(data.small_lugg); 
                $('#editLargeLuggageCapacity').val(data.large_lugg); 
                $('#editChildSeatCapacity').val(data.child_seat); 
                $('#editselPriceType').val(data.price_type).trigger('change'); 
                $('#editPrice').val(data.price); 

            } else {
                alert('Error fetching data');
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


    var url = `{{env('API_URL')}}vehiclepricing/update/` + vehicleId;

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
                url: '{{env('API_URL')}}vehiclepricing/delete/' + id,
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



        
</script>