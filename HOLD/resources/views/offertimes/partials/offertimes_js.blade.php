<script>



$(document).ready(function(){

  vehicletable();

}); 



function vehicletable(){

    var formDataObject  = {};

    formDataObject['token'] = getCookie('d_token');

    formDataObject['device_id'] = 0;

    

    // Destroy any existing DataTable instance

    if ($.fn.DataTable.isDataTable('#data-table')) {

        $('#data-table').DataTable().destroy();

    }

    

    // Initialize a new DataTable

    $('#data-table').DataTable({

        ajax: {

            url: '{{env('API_URL')}}OffertimesView',

            method: 'POST',

            dataSrc: 'data',

            data: function(d) {

                return $.extend({}, d, formDataObject);

            },

        },

        columns: [

            { 

                data: null,

                render: function(data, type, row, meta) {

                    return meta.row + 1;  // Return row number

                }

            },

            { data: 'cost' },

            {
            data: 'from',
            render: function (data) {
                return formatTimeAMPM(data); // Convert 1100 -> 11:00 AM
            }
        },

        {
            data: 'to',
            render: function (data) {
                return formatTimeAMPM(data); // Convert 2100 -> 9:00 PM
            }
        },

            { data: 'content' },

            {

                data: null,

                render: function(data, type, row) {

                    // Return custom HTML for Edit and Delete buttons

                    return `

                        

                            <!-- Edit Button -->

                            <button type="button" class="btn btn-outline-secondary footable-edit "  data-id="${row.id}"

                        data-bs-toggle="modal" 

                        data-bs-target="#editor-modal" 

                        data-id="123">

                    <span class="fa-solid fa-edit" aria-hidden="true"></span>

                    </button>



                            <!-- Delete Button -->

                            <button id="delete" class="btn btn-outline-danger footable-delete me-3" 

                                data-id="${row.id}">

                                <i class="fa fa-trash"></i>

                            </button>

                       

                    `;

                }

            }

        ],

    });

    function formatTimeAMPM(time) {
    if (!time) return ''; // Handle null/undefined cases
    let timeStr = time.toString().padStart(4, '0'); // Ensure it's 4 digits
    let hours = parseInt(timeStr.slice(0, 2), 10); // Extract hours
    let minutes = timeStr.slice(2); // Extract minutes

    let period = hours >= 12 ? 'PM' : 'AM'; // Determine AM or PM
    hours = hours % 12 || 12; // Convert 24-hour format to 12-hour

    return `${hours}:${minutes} ${period}`;
}

}

$('#primaryBtn').click(function (e) {

    e.preventDefault();



    // Check if form exists before using it

    var form = $('#fleetForm')[0];

    if (!form) {

        console.log('Form not found!');

        return;

    }

    

    var costValue = $('#cost').val();

    if (costValue.length > 8) {

        Swal.fire({

            title: '',

            text: 'The cost length must be up to 8 digits only.',

            icon: 'error'

        });



        return false;

    }

    

    var from = $('#from').val(),to = $('#to').val();

    if (!from || !to) {

        swalalerterror('Both "From" and "To" times are required.');

        return;

    }

    if(from > 23 || to > 23 ){

        swalalerterror('The time cannot be greater than 23');

        return;

    }

    

    if (parseInt(from) >= parseInt(to)) {

        swalalerterror('The "To" time cannot be less than or equal');

        return;

    }

    

    // Initialize FormData after confirming the form exists

    var formdata = new FormData(form);



    // Append additional data to FormData after initialization

    formdata.append('token', getCookie('d_token')); // Assuming getCookie is a function that gets the cookie value

    formdata.append('device_id', 0);

            loader.show();

    $.ajax({

        data: formdata,

        url: "{{env('API_URL')}}OffertimeStore",

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

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Offer Time updated successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        // window.location.reload();

                        form.reset();



                    });

                } else if(response.message =="Data has been inserted successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Created',

                        text: 'Offer Time Created successfully',

                        showConfirmButton: false,

                        timer: 3000,

                    }).then(function () {

                        // window.location.reload();

                            $('#add-modal').modal('hide')

                          form.reset();

                        vehicletable();



                    });

                }

                

                 loader.hide();

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

            

            loader.hide();

        }

    });

});


$(document).on('click', '.footable-edit', function() {

    var id = $(this).data('id'); 



    $.ajax({

        url: '{{env('API_URL')}}OfferTimeEdit/edit/' + id, 

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

                $('#editcost').val(data.cost);

                $('#editfrom').val(formatTime(data.from));

                // alert("Formatted Time: " + formatTime(data.from)); 
                // alert("Formatted Time: " + formatTime(data.to)); 

                $('#editto').val(formatTime(data.to));

                $('#editcontent').val(data.content);

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

function formatTime(time) {
    if (!time) return ''; // Handle null values
    let timeStr = time.toString().padStart(4, '0'); // Ensure it's always 4 digits
    let hours = timeStr.slice(0, 2); // Extract hours (01, 09, 22, etc.)
    let minutes = timeStr.slice(2, 4); // Extract minutes (00, 30, etc.)

    return `${hours}:${minutes}`; // Return formatted time (24-hour format)
}

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

    

    var from = $('#editfrom').val(),to = $('#editto').val();

    if (!from || !to) {

        swalalerterror('Both "From" and "To" times are required.');

        return;

    }

    if(from > 23 || to > 23 ){

        swalalerterror('The time cannot be greater than 23');

        return;

    }

    if (parseInt(from) >= parseInt(to)) {

        swalalerterror('The "To" time cannot be less than or equal');

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



   var url = `{{env('API_URL')}}OfferTimeUpdate/update/` + vehicleId;



        loader.show();



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

                    // swalalertsuccess('Data has been updated successfully').then(function(){

                    //     $('#editor-modal').modal('hide')

                    //     form.reset();

                    //     vehicletable();

                        

                    // }) ;

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Offer Time updated successfully',

                        showConfirmButton: false,

                        timer: 3000,

                    }).then(function () {

                        // window.location.reload();
                        $('#editor-modal').modal('hide')
                        form.reset();
                        vehicletable();


                    });

                } else if(response.message =="Data has been inserted successfully") {

                     swalalertsuccess('Data has been inserted successfully').then(function () {

                        // window.location.reload();

                        $('#editor-modal').modal('hide')

                        vehicletable()

                        form.reset();



                    })

                    // Swal.fire({

                    //     position: 'center',

                    //     icon: 'success',

                    //     title: 'Added',

                    //     text: 'Data has been inserted successfully',

                    //     showConfirmButton: false,

                    //     timer: 2000,

                    // }).then(function () {

                    //     // window.location.reload();

                    //     $('#editor-modal').modal('hide')

                    //     vehicletable()

                    //     form.reset();



                    // });

                }

                

                 loader.hide();

            }

        },

        error: function (xhr, status, error) {

            console.log('Error:', error);

             swalalerterror('Something went wrong! ' + xhr.responseText)

            // Swal.fire({

            //     icon: 'error',

            //     title: 'Oops...',

            //     text: 'Something went wrong! ' + xhr.responseText,

            // });

            

             loader.hide();

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

    }).then((result) => {

        if (result.isConfirmed) {

             loader.show();

            $.ajax({

                url: '{{env('API_URL')}}OfferTimedelete/delete/' + id,

                type: 'POST', 

                data: {

                    token: getCookie('d_token'),

                    device_id: 0

                },

                success: function(response) {

                    if (response.status === 200) {

                        $('tr[data-id="' + id + '"]').remove();

                        // swalalertsuccess('Item deleted successfully.')

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Deleted',

                            text: 'Offer Time deleted successfully.',

                            showConfirmButton: false,

                            timer: 1500

                        });

                        vehicletable();

                        // location.reload()

                    } else {

                        // swalalerterror('Failed to delete item.')

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: 'Failed to delete item.',

                        });

                    }

                     loader.hide();

                },

                error: function(xhr, status, error) {

                    console.error('Error:', xhr.responseText); 

                     swalalerterror('An error occurred while trying to delete the item.')

                    // Swal.fire({

                    //     icon: 'error',

                    //     title: 'Error',

                    //     text: 'An error occurred while trying to delete the item.',

                    // });

                    

                     loader.hide();

                }

            });

        }

    });

});



</script>