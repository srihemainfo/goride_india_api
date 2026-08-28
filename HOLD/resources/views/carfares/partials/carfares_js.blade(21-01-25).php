<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

    });

            // Ajax for Save and Updatecarfares_js
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
    function validateInput(input) {
        input.value = input.value.replace(/[^0-9.]/g, ''); 
        if ((input.value.match(/\./g) || []).length > 1) {
            input.value = input.value.replace(/\.(?=.*\.)/g, '');
        }
    }

</script>