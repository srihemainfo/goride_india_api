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
                url: "{{ route('locationrange.index') }}",
                data: function (d) {
                    d.name = $('input[name=name_filter]').val();
                    d.type = $('input[name=place_filter]').val();
                    d.from_charge = $('input[name=pickup_filter]').val();
                    d.to_charge = $('input[name=dropoff_filter]').val();
                    d.passing_charge = $('input[name=passing_filter]').val();
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false , className: "text-center"},
                {data: 'id', name: 'id', searchable: false},
                {data: 'name', name: 'name', className: "text-center"},
                {data: 'place', name: 'place', className: "text-center"},
                {data: 'from_charge', name: 'from_charge', className: "text-center"},
                {data: 'to_charge', name: 'to_charge', className: "text-center"},
                {data: 'passing_charge', name: 'passing_charge', className: "text-center"},
                {data: 'status', name: 'status', className: "text-center"},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
            ],
            "order":[[1,'desc']]
        });

        //Search Datatable
        $('#search').on('click', function(e) {
            table.draw();
        });

        //Modal Form Trigger
        $('#addLocationrange').click(function () {
            ResetErrors()
            $('#locationrange_id').val('');
            $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");
            $('#locationrangeForm').trigger("reset");
            $('#map').hide();
            $('#form-modal').modal('show');
        });

        // Ajax for Save and Update
        $('#saveBtn').click(function (e) {
                e.preventDefault();

                $.ajax({
                data: $('#locationrangeForm').serialize(),
                url: "{{ route('locationrange.store') }}",
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
                        $('#locationrangeForm').trigger("reset");
                        $('#form-modal').modal('hide');
                        table.draw();
                        if(response.data.created_at === response.data.updated_at){
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Added',
                                text: 'New Location Range added successfully',
                                showConfirmButton: false,
                                timer: 2000
                            });

                        }else{
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Updated',
                                text: 'Location Range updated successfully',
                                showConfirmButton: false,
                                timer: 2000
                            });

                        }
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });

         //Update fleet status
         $('body').on('change', '.location-range-status', function(){
            let location_range_status = $(this).data('previous');
            let location_range_id = $(this).data("id");
            let isActive = $(this).val();
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
                        url: "{{ route('LocationRangeStatusUpdate') }}",
                        data: {id: location_range_id, isActive: isActive},
                        success: function (response) {
                            if(response.isUpdated){
                                table.draw();
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Updated',
                                    text: 'Location Range status changed successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                })
                            } else {
                                ('[data-id="' + location_range_id + '"]').val(location_range_status)
                                Swal.fire("Error", "Employee status not changed", "error");
                            }
                        },
                        error: function (data) {
                            ('[data-id="' + location_range_id + '"]').val(location_range_status)
                            console.log('Error:', data);
                        }
                    });
                }
                else {
                $('[data-id="' + location_range_id + '"]').val(location_range_status)
                }
            })
        });


        //Edit Form Trigger with Data
        $('body').on('click', '.editLocationRange', function () {
            ResetErrors()
            let locationrange_id = $(this).data('id');
            $('.invalid-name').text('');
            $.get("{{ route('locationrange.index') }}" +'/' + locationrange_id +'/edit', function (response) {
                if(response.data){
                    $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");
                    $('#form-modal').modal('show');
                    $('#map').show();
                    AssignValues(response.data)
                } else{
                    Swal.fire("404", "Location Range not found", "error");
                }
            })
        });

        //Delete Ajax
        $('body').on('click', '.deleteLocationRange', function(){
            let locationrange_id = $(this).data("id");
            Swal.fire({
                title: "Are you sure to delete this location range?",
                text: "It will gone forever.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) =>{
                if(willDelete.isConfirmed){
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('locationrange') }}"+'/'+locationrange_id,
                        data: {id: locationrange_id},
                        success: function (response) {
                            if(response.isDeleted){
                                table.draw();
                                Swal.fire({
                                    position: 'center',
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: 'Location Range deleted successfully',
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                            } else {
                                Swal.fire("Error", "Location Range not deleted", "error");
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            })
        });

        //Change the value of type place filter
        $("#type_filter").change(function(){
            $("#place_filter").val($(this).val());
        });

        //Reset filter values
        $('#reset_filter').click(function(){
            $("#name_filter").val('');
            $("#type_filter").val('');
            $("#place_filter").val('');
            $("#pickup_filter").val('');
            $("#dropoff_filter").val('');
            $("#passing_filter").val('');

            table.draw();
        });

    })

    function ResetErrors(){
        $('.invalid-name, .invalid-place-type, .invalid-pickup-charge, .invalid-dropoff-charge, .invalid-passing_charge').text('');
    }

    function ShowErrors(errors){
        if(errors.name){
            $('.invalid-name').text(errors.name);
        }
        if(errors.type){
            $('.invalid-place-type').text(errors.type);
        }
        if(errors.from_charge){
            $('.invalid-pickup-charge').text(errors.from_charge);
        }
        if(errors.to_charge){
            $('.invalid-dropoff-charge').text(errors.to_charge);
        }
        if(errors.passing_charge){
            $('.invalid-passing-charge').text(errors.passing_charge);
        }
    }

    function AssignValues(data){
        $('#locationrange_id').val(data.id);
        $('#name').val(data.name);
        $('#type').val(data.type);
        $('#from_charge').val(data.from_charge);
        $('#to_charge').val(data.to_charge);
        $('#passing_charge').val(data.passing_charge);

        initMap(JSON.parse( data.coordinates))
    }

    let script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAvamEcF_mpcwyGNek02hZ6N6SBAK8I2As&callback=initMap&libraries=drawing&v=weekly';
    script.async = true;

    window.initMap = function(coordinates) {
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 12,
          center: coordinates[0],
          mapTypeId: "terrain",
        });

        const polyline = new google.maps.Polygon({
          paths: coordinates,
          strokeColor: "#FF0000",
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: "#FF0000",
          fillOpacity: 0.35,
        });

        polyline.setMap(map);
    }

    document.head.appendChild(script);

    @if (session('coordinates_update'))
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('coordinates_update') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('coordinates_update');
        @endphp
    @endif
</script>
