<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

            // Datatable
			let table = $('#data-table').DataTable({
                pageLength: 100,
				processing: true,
				serverSide: true,
                searching: false,
				ajax: '{{ route('module-permissions.index') }}',
				columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data:'id',name:'id', searchable: false, visible: false},
					{data: 'module_name', name: 'module_name'},
                    {data: 'is_readable', name: 'is_readable', orderable: false, className: "text-center align-top"},
                    {data: 'is_creatable', name: 'is_creatable', orderable: false, className: "text-center align-top"},
                    {data: 'is_updatable', name: 'is_updatable', orderable: false, className: "text-center align-top"},
                    {data: 'is_deletable', name: 'is_deletable', orderable: false, className: "text-center align-top"},
					{data: 'action', name: 'action', orderable: false, searchable: false},
				],
                "order":[[1,'asc']]
			});

             //Edit Form Trigger with Data
             $('body').on('click', '.updateRights', function () {
                 let id = $(this).data('id')
                 let is_readable = $(this).closest('tr').find('.is_readable').is(':checked') ? '1' : '0'
                 let is_creatable = $(this).closest('tr').find('.is_creatable').is(':checked') ? '1' : '0'
                 let is_updatable = $(this).closest('tr').find('.is_updatable').is(':checked') ? '1' : '0'
                 let is_deletable = $(this).closest('tr').find('.is_deletable').is(':checked') ? '1' : '0'

                 //if read not checked all other values not going to checked
                 let is_read_checked = $(this).closest('tr').find('.is_readable').is(':checked')
                 let permissions = {
                    id: id,
                    is_readable: is_readable,
                    is_creatable: is_read_checked ? is_creatable : '0',
                    is_updatable: is_read_checked ? is_updatable : '0',
                    is_deletable: is_read_checked ? is_deletable : '0'
                 }


                 $.ajax({
                    data: permissions,
                    url: "{{ route('UpdatePermissions') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 400 && !response.errors){
                            Swal.fire("Error", "Update failed", "error");
                        }

                        if(response.status == 200){
                            table.draw();

                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Added',
                                    text: 'Employee permissions updated successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                })
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
             })
        }

    )
</script>
