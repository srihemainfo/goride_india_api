<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Datatable
        let table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: "{{ route('notifications.index') }}",
                data: function(d) {},
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id',
                    searchable: false,
                    visible: false
                },
                {
                    data: 'driver_name',
                    name: 'driver_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'booking_id',
                    name: 'booking_id',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-right",
                    orderable: false,
                    searchable: false,
                    width: '15%'
                },
            ],
            "order": [
                [1, 'desc']
            ]
        });

        //Table change during new notification
        $('#notification_count').on('DOMSubtreeModified', function() {
            table.draw();
        });

        //Update notification status
        $('body').on('click', '.markNotification', function() {
            let notification_id = $(this).data("id");

            $.ajax({
                type: "POST",
                url: "{{ route('NotificationStatusUpdate') }}",
                data: {
                    id: notification_id
                },
                success: function(response) {
                    if (response.isUpdated) {
                        $('#notification_count').text(response.notification_count)
                        Swal.fire({
                            position: 'bottom-start',
                            icon: 'success',
                            title: 'Updated',
                            text: 'Notification status changed successfully',
                            showConfirmButton: false,
                            timer: 2000,
                        })
                    } else {
                        Swal.fire("Error",
                            "Notification status not changed",
                            "error");
                    }
                },
                error: function(data) {
                    console.log('Error: ' + data)
                }
            });

        });
    })
</script>
