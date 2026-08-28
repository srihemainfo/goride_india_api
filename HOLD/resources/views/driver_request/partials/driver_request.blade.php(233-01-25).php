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
            data: function(d) {
                d.role_id = $('#role_id').val();
                d.token = token;
                d.device_id = device_id;
            },
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'id', name: 'id', searchable: false, visible: false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'vehicle_type', name: 'vehicle_type'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "order": [[1, 'asc']],
        initComplete: function() {
            bindSelectChangeEvent();
        },
        drawCallback: function(settings) {
            bindSelectChangeEvent();
        }
    });
    function bindSelectChangeEvent() {
        const previousValues = {};

        $('.request_status').each(function() {
            const id = $(this).data('id');
            previousValues[id] = $(this).val();
        });

        $('.request_status').off('change').on('change', function() {
            const selectedValue = $(this).val();
            const driverId = $(this).data('driver_id');
            const partnerId = $(this).data('partner_id');
            const id = $(this).data('id');

            if (selectedValue !== 'Select Yes/No') {
                
                loader.show();
                
                $.ajax({
                    url: 'https://driver.airportrides.co/api/valuecheck/driver_request',
                    method: 'GET',
                    data:{ 'driver_id' : driverId,
                            'partner_id' :partnerId,
                            'id': id,
                            'value':selectedValue
                        
                    },
                    success: function(response) {
                        if (response.status) {
                            swalalertsuccess(response.message);
                            previousValues[id] = selectedValue; // Update previous value
                            table.draw();
                        } else {
                            swalalerterror(response.message);
                            $(`.request-status[data-id="${id}"]`).val(previousValues[id]); // Revert on failure
                        }
                        loader.hide();
                    },
                    error: function() {
                        Swal.fire("Error", "An unexpected error occurred.", "error");
                        $(`.request-status[data-id="${id}"]`).val(previousValues[id]); // Revert on error
                        loader.hide();
                    }
                });
            } else {
                $(this).val(previousValues[id]); 
            }
        });
    }
});


</script> 