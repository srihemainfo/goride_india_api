<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

            // Datatable
			let table = $('#data-table').DataTable({
                pageLength: 50,
                // retrieve: true,
				// processing: true,
				serverSide: true,
                searching: false,
				 ajax: {
                        url: "{{env('API_URL')}}module-permissions",
                        type: "post",
                        data: function(d) {
                            d.role_id = $('#role_id').val();
                            d.token = token;
                            d.device_id = device_id;
                        },
                    },
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
                 
                 let is_readable = $(this).closest('tr').find('.is_readable').is(':checked') ? '1' : '0'
                 let is_creatable = $(this).closest('tr').find('.is_creatable').is(':checked') ? '1' : '0'
                 let is_updatable = $(this).closest('tr').find('.is_updatable').is(':checked') ? '1' : '0'
                 let is_deletable = $(this).closest('tr').find('.is_deletable').is(':checked') ? '1' : '0'
                 let id = $(this).data('id')

                 //if read not checked all other values not going to checked
                 let is_read_checked = $(this).closest('tr').find('.is_readable').is(':checked')
                 formDataObject['id'] = id;
                 formDataObject['is_readable'] = is_read_checked ? is_readable : 0;
                 formDataObject['is_creatable'] = is_read_checked ? is_creatable : 0;
                 formDataObject['is_updatable'] = is_read_checked ? is_updatable: 0;
                 formDataObject['is_deletable'] = is_read_checked ? is_deletable : 0;
                //  let permissions = {
                //     id: id,
                //     is_readable: is_readable,
                //     is_creatable: is_read_checked ? is_creatable : '0',
                //     is_updatable: is_read_checked ? is_updatable : '0',
                //     is_deletable: is_read_checked ? is_deletable : '0'
                //  }


                 $.ajax({
                    data: formDataObject,
                    url: "{{env('API_URL')}}UpdatePermissions",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        if(response.status == 400 && !response.errors){
                            swalalerterror(response.message || "Update failed")
                            // Swal.fire("Error", "Update failed", "error");
                        }

                        if(response.status == 200){
                             swalalertsuccess(response.message || 'Employee permissions updated successfully' )
                            table.draw();

                                // Swal.fire({
                                //     position: 'top-end',
                                //     icon: 'success',
                                //     title: 'Added',
                                //     text: 'Employee permissions updated successfully',
                                //     showConfirmButton: false,
                                //     timer: 2000,
                                // })
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
             })
		        $(document).on('change','#role_id',function(){
                   table.draw();
            
             })
             
             $('#update-all-btn').on('click', function() {
    let updates = [];

    // Iterate over each row in the table
    $('#data-table tr').each(function() {

        let row = $(this);
        let id = row.find('.updateRights').data('id');
        let data = { id: id }; 


        row.find('input.editable-input').each(function() {
            let column = $(this).data('column'); 
            let value = $(this).is(':checked') ? '1' : '0'; 

            if (value !== '') {
                data[column] = value;
            }
        });
        updates.push(data);
        formDataObject['data'] = updates;
     
    });

        // Make AJAX call to update all data
        $.ajax({
            url: '{{env('API_URL')}}updateAllPermissions', // Replace with your actual update endpoint
            method: 'POST',
            data: formDataObject,
            success: function(response) {
                
                var message = response.message;
                var status = response.status;
                if (status == 200) {
                    swalalertsuccess(message);
                    table.draw();
                } else {
                    swalalerterror(message);
                    // table.draw();
                }
                
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error updating data:', error);
                alert('Failed to update data.');
                 table.draw();
            }
        });
    });

        })
</script>
