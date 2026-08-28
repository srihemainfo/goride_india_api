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
            url: '{{env('API_URL')}}EmailTemplateView',
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
            { data: 'template_name' },
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
}







 $('#primaryBtn').click(function (e) {
    e.preventDefault();

    // Check if form exists before using it
    var form = $('#fleetForm')[0];
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
        url: "{{env('API_URL')}}EmailTemplateStore",
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
                        title: 'Added',
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




$(document).on('click', '.footable-edit', function() {
    var id = $(this).data('id'); 

    $.ajax({
        url: '{{env('API_URL')}}EmailTemplateEdit/edit/' + id, 
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
        $('#edittemplate_name').val(data.template_name);
        $('#editemail').val(data.email);
        $('#editdiscription').summernote('code', data.description); // Use Summernote's API
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

    // Get the content from the Summernote editor
    var editDescription = $('#editdiscription').summernote('code');
    formdata.append('editdiscription', editDescription); // Add this line

    var url = `{{env('API_URL')}}EmailTemplateUpdate/update/` + vehicleId;

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
                if (response.message == "Data has been updated successfully") {
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
                } else if (response.message == "Data has been inserted successfully") {
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
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{env('API_URL')}}EmailTemplateDelete/delete/' + id,
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
                        location.reload()
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
$(document).ready(function() {
    $('#description').summernote({
        placeholder: 'Enter description',
        tabsize: 2,
        height: 200,  // Set the height of the editor
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
$(document).ready(function() {
    $('#editdiscription').summernote({
        tabsize: 2,
        height: 200,  // Set the height of the editor
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});

</script>