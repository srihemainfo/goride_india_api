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
                searching: false,
				ajax: {
                    url: "{{ route('fixed-price.index') }}",         
                    data: function (d) {
                        d.selected_area_from = $('input[name=selected_area_from]').val() ? $('input[name=selected_area_from]').val() : undefined;
                        d.selected_area_to = $('input[name=selected_area_to]').val() ? $('input[name=selected_area_to]').val() : undefined;
                    },
                },
				columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
				    {data: 'area_from', name: 'area_from'},
                    {data: 'area_to', name: 'area_to'},
                    {data: 'saloon', name: 'saloon', orderable: false, searchable: false},
                    {data: 'estate', name: 'estate', orderable: false, searchable: false},
                    {data: 'mpv', name: 'mpv', orderable: false, searchable: false},
                    {data: 'mpv5', name: 'mpv5', orderable: false, searchable: false},
                  	{data: 'mpv6', name: 'mpv6', orderable: false, searchable: false},
                    {data: 'mpv8', name: 'mpv8', orderable: false, searchable: false},
                    {data: 'executive', name: 'executive', orderable: false, searchable: false},
                    {data: 'mpv_executive', name: 'mpv_executive', orderable: false, searchable: false},
                  	{data: 'action', name: 'action', orderable: false, searchable: false},
				],
                "order":[[1,'desc']]
			});

            //Search Datatable
            $('#search').on('click', function(e) {
                table.draw();
            });

            //Modal Form Trigger
			$('#addPrice').click(function () {
                ResetErrors()
                $('#price_id').val('');
				$('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");
				$('#fixedPriceForm').trigger("reset");
                ResetSelect2();
				$('#form-modal').modal('show');
			});

             // Ajax for Save and Update
			$('#saveBtn').click(function (e) {
                    e.preventDefault();

                    $.ajax({
                    data: $('#fixedPriceForm').serialize(),
                    url: "{{ route('fixed-price.store') }}",
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
                            $('#fixedPriceForm').trigger("reset");
                            ResetSelect2()
                            $('#form-modal').modal('hide');
                            table.draw();
                            if(response.data.created_at === response.data.updated_at){
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Added',
                                    text: 'New fixed price added successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                })
                                
                            }else{
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Updated',
                                    text: 'Fixed price updated successfully',
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
            $('body').on('click', '.editPrice', function () {
                ResetErrors()

                let id = $(this).data('id');
                
                $.get("{{ route('fixed-price.index') }}" +'/' + id +'/edit', function (response) {
                    if(response.data){                        
                        $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");
                        $('#form-modal').modal('show');
                        AssignValues(response.data)
                    } else{
                        Swal.fire("404", "Price details not found", "error");
                    }
                })
            });

            //Delete Ajax
            $('body').on('click', '.deletePrice', function(){
                let id = $(this).data("id");

                Swal.fire({
                    title: "Are you sure to delete this price?",
                    text: "It will gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) =>{
                    if(willDelete.isConfirmed){
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('fixed-price') }}"+'/'+id,
                            data: {id: id},
                            success: function (response) {
                                if(response.isDeleted){
                                    table.draw();
                                    Swal.fire({
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Deleted',
                                        text: 'Fixed price deleted successfully',
                                        showConfirmButton: false,
                                        timer: 2000,
                                    })
                                    
                                } else {
                                    Swal.fire("Error", "Fixed price not deleted", "error");
                                }
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                })
            });

            // Ajax for Update Price Row
			$('body').on('click', '.updatePrice', function(e){
                e.preventDefault();

                let price_id = $(this).data("id");

                let saloon = $(this).closest('tr').find('input[name=saloon]').val()
                let estate = $(this).closest('tr').find('input[name=estate]').val()
                let mpv = $(this).closest('tr').find('input[name=mpv]').val()
                let mpv6 = $(this).closest('tr').find('input[name=mpv6]').val()
                let mpv8 = $(this).closest('tr').find('input[name=mpv8]').val()
                let executive = $(this).closest('tr').find('input[name=executive]').val()
                let mpv5 = $(this).closest('tr').find('input[name=mpv5]').val()
                let mpv_executive = $(this).closest('tr').find('input[name=mpv_executive]').val()

                $.ajax({
                    type: "POST",
                    url: "{{ route('FixedPriceUpdate') }}",
                    data: {
                        price_id: price_id, 
                        saloon: saloon,
                        estate: estate,
                        mpv: mpv,
                        mpv6: mpv6,
                        mpv8: mpv8,
                        executive: executive,
                        mpv5: mpv5,
                        mpv_executive: mpv_executive
                    },
                    success: function (response) {
                        console.log(response.isUpdated)

                        if(response.isUpdated){
                            table.draw();
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Updated',
                                text: 'Fixed price updated successfully',
                                showConfirmButton: false,
                                timer: 2000,
                            })
                            
                        } else {
                            Swal.fire("Error", "Fixed price not changed", "error");
                        }
                        
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            })

            // Ajax for area selection based on place
            $('#place_from').change(function(){
                let place_id = $('#place_from').val();
                let price_id = $('#price_id').val() ? $('#price_id').val() : null ;


                if(place_id){
                    $.get("{{ route('GetAreaForPlace') }}" +'/' + place_id, function (response) {
                        MakeAreaDropdown(response.data, 'from')
                        
                        if(price_id){
                            $('#area_from').val($('#area_from_name').val()).trigger("change")
                        }
                    })
                }
            })
            $('#place_to').change(function(){
                let place_id = $('#place_to').val();
                let price_id = $('#price_id').val() ? $('#price_id').val() : null ;

                if(place_id){
                    $.get("{{ route('GetAreaForPlace') }}" +'/' + place_id, function (response) {
                        MakeAreaDropdown(response.data, 'to')  
                                                
                        if(price_id){
                            $('#area_to').val($('#area_to_name').val()).trigger("change")
                        }
                    })
                }
            })

            //Change the value of from and to areas in filter
            $("#area_from_filter").change(function(){
                $("#selected_area_from").val($(this).val());
            });
            $("#area_to_filter").change(function(){
                $("#selected_area_to").val($(this).val());
            });

            //Reset filter values
            $('#reset_filter').click(function(){
                $("#area_from_filter").val(null).trigger("change");
                $("#area_to_filter").val(null).trigger("change");
                table.draw();
            });

    })

    function ResetErrors(){
        $('.invalid-place_from, .invalid-place_to, .invalid-area_from, .invalid-area_to').text('');
    }

    function ShowErrors(errors){
        if(errors.place_from){
            $('.invalid-place_from').text(errors.place_from);
        }
        if(errors.place_to){
            $('.invalid-place_to').text(errors.place_to);
        }
        if(errors.area_from){
            $('.invalid-area_from').text(errors.area_from);
        }
        if(errors.area_to){
            $('.invalid-area_to').text(errors.area_to);
        }
    }

    /*Note: Problem - Unvailable places exist. Because it allowed in old app.*/
    function AssignValues(data){
        console.log(data)

        $('#price_id').val(data.id);
        $('#place_from').val(data.place_from).trigger("change");
        $('#place_to').val(data.place_to).trigger("change");

        $('#area_from_name').val(data.area_from);
        $('#area_to_name').val(data.area_to);

        $('#saloon').val(data.saloon)
        $('#executive').val(data.executive)
        $('#estate').val(data.estate)
        $('#mpv').val(data.mpv)
        $('#mpv5').val(data.mpv5)
        $('#mpv6').val(data.mpv6)
        $('#mpv8').val(data.mpv8)
        $('#mpv_executive').val(data.mpv_executive)
    }

    function ResetSelect2(){
        $("#place_from").val(null).trigger("change")
        $("#place_to").val(null).trigger("change")

        $('#area_from').empty()
        $( "#area_from" ).prop("disabled", true)

        $('#area_to').empty()
        $( "#area_to" ).prop("disabled", true)
    }

    function MakeAreaDropdown(data, type){
        ResetErrors()

        if(!data){
            if(type === 'from'){
                $('#area_from').empty()
                $( "#area_from" ).prop("disabled", true)
                $('.invalid-area_from').text('No area found for selected place.');
                return ;
            } else if(type === 'to') {
                $('#area_to').empty()
                $( "#area_to" ).prop("disabled", true)
                $('.invalid-area_to').text('No area found for selected place.');
                return ;
            }
        } else {

            let option = ''

            data.forEach(createOption);

            function createOption(item) {
                option += `<option value='${item.area}'>${item.area}</option>`;
            }
            
            if(type === 'from'){
                $('#area_from').prop("disabled", false)
                $('#area_from').empty()
                $('#area_from').append(option)
            }else if(type === 'to'){
                $('#area_to').prop("disabled", false)
                $('#area_to').empty()
                $('#area_to').append(option)
            }
        }
    }
</script>