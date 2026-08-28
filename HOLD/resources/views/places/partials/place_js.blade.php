<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

            // Datatable
			let table = $('#data-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: "{{ route('place.index') }}",
				columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
					{data: 'id', name: 'id', searchable: false, visible: false},
                    {data: 'place', name: 'place'},
                    {data: 'discount', name: 'discount'},
                    {data: 'discount_type', name: 'discount_type', orderable: false, searchable: false},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
					{data: 'action', name: 'action', orderable: false, searchable: false},
				],
                "order":[[1,'desc']]
			});

            //Modal Form Trigger
			$('#addPlace').click(function () {
                ResetErrors()
                $('#place_id').val('');
				$('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");
				$('#placeForm').trigger("reset");
				$('#form-modal').modal('show');
			});

            // Ajax for Save and Update
			$('#saveBtn').click(function (e) {
                    e.preventDefault();
                    console.log("Ok Works")
                    $.ajax({
                    data: $('#placeForm').serialize(),
                    url: "{{ route('place.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        ResetErrors()

                        if(response.status == 400 && response.errors){
                            ShowErrors(response.errors)
                        }

                        if(response.status == 400 && !response.errors){
                            Swal.fire("Error", "Add or Update failed", "error");
                        }

                        if(response.status == 200){
                            $('#placeForm').trigger("reset");
                            $('#form-modal').modal('hide');
                            table.draw();
                            if(response.data.created_at === response.data.updated_at){
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Added',
                                    text: 'New Place added successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                })
                            }else{
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Updated',
                                    text: 'Place updated successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                })
                            }						
                        }				
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });

            //Edit Form Trigger with Data
            $('body').on('click', '.editPlace', function () {
                let place_id = $(this).data('id');
                ResetErrors()
                $.get("{{ route('place.index') }}" +'/' + place_id +'/edit', function (response) {
                    if(response.data){                        
                        $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");
                        $('#form-modal').modal('show');
                        AssignValues(response.data)
                    } else{
                        Swal.fire("404", "Place not found", "error");
                    }
                })
            });

            //Delete Ajax
            $('body').on('click', '.deletePlace', function(){
                let place_id = $(this).data("id");
                console.log(place_id)
                Swal.fire({
                    title: "Are you sure to delete this place?",
                    text: "It will gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) =>{
                    if(willDelete.isConfirmed){
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('place') }}"+'/'+place_id,
                            data: {id: place_id},
                            success: function (response) {
                                if(response.isDeleted){
                                    table.draw();
                                    Swal.fire({
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: 'Place deleted successfully',
                                        showConfirmButton: false,
                                        timer: 2000,
                                    })
                                } else {
                                    Swal.fire("Error", "Place not deleted", "error");
                                }
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            });

            //Update place status
            $('body').on('change', '.place-status', function(){
                let place_status = $(this).data('previous');
                let place_id = $(this).data("id");
                let isActive = $(this).val();
                console.log($(this).val());
                Swal.fire({
                    title: "Status Update",
                    text: "Are you sure want change the status?.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) =>{
                    if(willDelete.isConfirmed){
                        $.ajax({
                            type: "POST",
                            url: "{{ route('PlaceStatusUpdate') }}",
                            data: {id: place_id, isActive: isActive},
                            success: function (response) {
                                if(response.isUpdated){
                                    table.draw();
                                    Swal.fire({
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Updated',
                                        text: 'Place status changed successfully',
                                        showConfirmButton: false,
                                        timer: 2000,
                                    })
                                    
                                } else {
                                    ('[data-id="' + place_id + '"]').val(place_status)
                                    Swal.fire("Error", "Place status not changed", "error");
                                }
                            },
                            error: function (data) {
                                ('[data-id="' + place_id + '"]').val(place_status)
                                console.log('Error:', data);
                            }
                        });
                    }
                    else {
                    $('[data-id="' + place_id + '"]').val(place_status)
                    }
                })
            });
    })

    function ResetErrors(){
        $('.invalid-place-name, .invalid-discount, .invalid-discount_type').text('');
    }

    function ShowErrors(errors){
        if(errors.place){
            $('.invalid-place-name').text(errors.place);
        }
        if(errors.discount){
            $('.invalid-discount').text(errors.discount);
        }
        if(errors.discount_type){
            $('.invalid-discount-type').text(errors.discount_type);
        }
    }

    function AssignValues(data){
        $('#place_id').val(data.id);
        $('#place').val(data.place);
        $('#discount').val(data.discount);
        if(data.discount_type){
            $('#discount_type').val(data.discount_type);
        }
    }
</script>