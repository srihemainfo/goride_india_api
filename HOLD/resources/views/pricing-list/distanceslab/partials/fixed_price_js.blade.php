<script>
$(document).ready(function(){
   
  showlist();  
  vehiclelist();
  Editvehiclelist();
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
            url: '{{env('API_URL')}}distanceview',
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
            { data: 'start_dis' },
            { data: 'end_dis' },
            { data: 'saloon' },
            { data: 'executive' },
            { data: 'mpv' },
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







function showlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}PricingShow',
        method: 'POST', // Consider using GET if you're only fetching data
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                console.log(response);
                AssignValues(data);
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
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
        url: "{{env('API_URL')}}DistanceStore",
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
        url: '{{env('API_URL')}}distanceslab/edit/' + id, 
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
                $('#editstarDis').val(data.start_dis);
                $('#editendDis').val(data.end_dis);
                $('#editsaloon').val(data.saloon);
                $('#editexecutive').val(data.executive);
                $('#editmpv').val(data.mpv);
                $('#editseater').val(data.seater);
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

   var url = `{{env('API_URL')}}distanceslab/update/` + vehicleId;



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


//   $(document).on('click', '.footable-delete', function() {
//     var id = $(this).data('id');
//     console.log("Delete button clicked with ID:", id);

//     if (confirm('Are you sure you want to delete this item?')) {
//         $.ajax({
//             url: '{{env('API_URL')}}distanceslab/delete/' + id,
//             type: 'POST', 
//             data: {
//                 token: getCookie('d_token'),
//                 device_id: 0
//             },
//             success: function(response) {
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
                url: '{{env('API_URL')}}distanceslab/delete/' + id,
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