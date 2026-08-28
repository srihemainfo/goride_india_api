<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

    });

            // Ajax for Save and Update
			$('.updateFare').click(function (e) {
                e.preventDefault();
                let id = $(this).data("id");

                $.ajax({
                    data: $('#car_fare_'+id).serialize(),
                    url: "{{ route('carfare.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        if(response.isUpdated){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Car Fare',
                                text: 'Car fares updated successfully',
                                showConfirmButton: false,
                                timer: 2000,
                            }).then((willUpdate) =>{
                                if(willUpdate.isConfirmed){
                                    location.reload();
                                }
                            })
                        } else {
                            Swal.fire("Error", "Carfare not updated", "error");
                        }				

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
                
            });
</script>