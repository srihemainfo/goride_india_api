<script> 
$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    let table = $('#data-table').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{env('API_URL')}}driver_request",
            type: "post",
            dataType: 'json',
            delay: 400,
            data: function (d) {
                d.role_id = $('#role_id').val();
                d.token = token;
                d.device_id = device_id;
            },
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id', searchable: false, visible: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' ,
                render: function (data, type, row) {
                    
                    return row.drivers.email;
                }    
            
            },
            { data: 'vehicle_type', name: 'vehicle_type' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    
                    return `
                    <div class="d-flex me-5">
                        <button class="btn btn-success accept-btn me-3" data-id="${row.id}" data-driver_id="${row.driver_id}" data-p_key="${row.p_key}" data-email="${row.email ? row.email.trim() : ''}">
                            Accept
                        </button>
                        <button class="btn btn-danger reject-btn me-5" data-id="${row.id}" data-driver_id="${row.driver_id}" data-p_key="${row.p_key}" data-email="${row.email ? row.email.trim() : ''}">
                            Reject
                        </button>
                    </div>`;
                }
            }

        ],
        "order": [[1, 'asc']],
        initComplete: function () {
            bindActionButtons();
        },
        drawCallback: function (settings) {
            bindActionButtons();
        }
    });

    function bindActionButtons() {
        $('.accept-btn, .reject-btn').off('click').on('click', function () {
            let button = $(this);
            let driverId = button.data('driver_id');
            let p_key = button.data('p_key');
            let email = button.data('email');
            let id = button.data('id');
            let action = button.hasClass('accept-btn') ? "Accepted" : "Rejected";

            Swal.fire({
                title: `Are you sure?`,
                text: `You are about to mark this request as ${action}.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: button.hasClass('accept-btn') ? "#28a745" : "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: `Yes, ${action}`,
            }).then((result) => {
                if (result.isConfirmed) {
                    loader.show();

                    $.ajax({
                        url: '{{env('DRIVER_API')}}valuecheck/driver_request',
                        method: 'GET',
                        data: {
                            'driver_id': driverId,
                            'p_key': p_key,
                            'email': email,
                            'id': id,
                            'value': action
                        },
                        success: function (response) {
                            // alert('hiiii');
                            if (response.status) {
                                swalalertsuccess(response.message);
                                table.draw(); // Refresh table
                            } else {
                                swalalerterror(response.message);
                            }
                            loader.hide();
                        },
                        error: function () {
                            Swal.fire("Error", "An unexpected error occurred.", "error");
                            loader.hide();
                        }
                    });
                }
            });
        });
    }
});


</script> 