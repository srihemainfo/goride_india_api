<script>
$(document).ready(function(){
   
  showlist(); 
  fleetlist();
});    

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


function fleetlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    $.ajax({
        url: '{{env('API_URL')}}Fleetlist',
        method: 'POST',
        data: formDataObject,
        success: function(response) {
            // Log the entire response to check its structure
            console.log('API Response:', response);
            
            if (response.status === 200) {
                // Use the correct key 'data' to access the list of car fares
                var listCarFares = response.data;

                if (Array.isArray(listCarFares)) {
                    $('table tbody').empty(); // Clear previous entries

                    listCarFares.forEach(function(item) {
                        let row = `
                            <tr data-id="${item.id}">
                                <td>${item.order}</td>
                                <td>${item.name}</td>
                                <td>${item.passenger}</td>
                                <td>${item.min}</td>
                                <td>${item.max}</td>
                                <td>${item.luggage}</td>
                                <td>${item.hand_luggage}</td>
                                <td>${item.booster}</td>
                                <td>${item.child}</td>
                                <td>${item.status}</td>
                                <td class="footable-editing footable-last-visible">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-outline-secondary footable-edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editor-modal" 
                                            data-id="${item.id}">
                                            <span class="fa-solid fa-edit" aria-hidden="true"></span>
                                        </button>
                                        <!-- Delete Button -->
                                        <button id="delete" class="btn btn-outline-danger footable-delete" 
                                            data-id="${item.id}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('table tbody').append(row);
                    });

                    // Handle permissions (optional logic based on the API response)
                    if (response.permissions) {
                        if (!response.permissions.IS_CREATABLE) {
                            $('.create-button').hide();
                        }
                        if (!response.permissions.IS_UPDATABLE) {
                            $('.footable-edit').hide();
                        }
                        if (!response.permissions.IS_DELETABLE) {
                            $('.footable-delete').hide();
                        }
                    }

                } else {
                    console.error('data is not an array:', listCarFares);
                }

            } else {
                console.error('Unexpected response status:', response.status);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

  
  $('#primaryBtn').click(function (e) {
    e.preventDefault();
    var form = $('#fleetForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }
    var formdata = new FormData(form);
    formdata.append('token', getCookie('d_token')); 
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}FleetStore",
        type: "POST",
        processData: false,  
        contentType: false,  
        dataType: 'json',
        success: function (response) {
            if (response.data) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Added',
                    text: 'Fleet Created Successfully',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    showlist(); 
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Updated',
                    text: 'Fleet Created Successfully',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    showlist(); 
                });
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






$(document).on('click', '.footable-edit', function() {
    var id = $(this).data('id'); 

    $.ajax({
        url: '{{env('API_URL')}}FleetEdit/edit/' + id, 
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
                $('#editname').val(data.name);
                $('#editpassenger').val(data.passenger);
                $('#editbooster').val(data.booster);
                $('#editmin').val(data.min);
                $('#editmax').val(data.max);
                $('#editluggage').val(data.luggage);
                $('#editthand_luggage').val(data.hand_luggage);
                $('#editchild').val(data.child);
                $('#editorder').val(data.order);
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

   var url = `{{env('API_URL')}}fleet/update/` + vehicleId;



    $.ajax({
        data: formdata,
        url: url,
        type: "POST",
        processData: false, 
        contentType: false,  
        dataType: 'json',
        success: function (response) {
            if (response.status === 200) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Updated',
                    text: 'Distance Pricing Updated Successfully',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    showlist(); 
                });
            } else {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to update distance pricing',
                    showConfirmButton: false,
                    timer: 2000,
                });
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
</script>