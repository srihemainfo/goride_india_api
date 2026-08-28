<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1z2h1mua7cxSWUyOckY_tdOhMZ8GA_jk&libraries=drawing&v=beta" async></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Select2 AJAX search for Drivers
        $('#driver_name').select2({
            ajax: {
                url: "{{route('GetMovingDrivers')}}",
                type: "post",
                dataType: 'json',
                delay: 400,
                data: function (params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }
        })


        $('#driver_name').on('change', function(){
            animateMarker(lat = "51.5072", lng = "0.1276", false)
        })

        const live_channel = pusher.subscribe('tracking-notification');

        live_channel.bind('live-event', function(data) {
            let current_driver = parseInt($('#driver_name').val()) ? parseInt($('#driver_name').val()) : 0
            let new_lat = parseFloat(data.lat);
            let new_lng = parseFloat(data.lng);
            let new_driver = parseInt(data.driver_id);

                if(current_driver === new_driver){
                    animateMarker(new_lat, new_lng, true);

                    $('#driver_select').notify(`Live location updated.`, {
                        autoHide: true,
                        autoHideDelay: 5000,
                        className: 'success',
                        elementPosition: 'right bottom',
                    });
                }
                console.log('Pusher: ',new_lat, new_lng)
            })



        function animateMarker(lat, lng, icon_visibility) {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 18,
                center: new google.maps.LatLng(lat, lng)
            });

            let icon = new google.maps.MarkerImage("{{ asset('car_icon.png') }}", null, null, null, new google.maps.Size(24, 24));

            let marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lng),
                map: map
            });

            if(icon_visibility){
                marker.setIcon(icon)
            } else {
                marker.setMap(null)
            }

            let newLatLng = new google.maps.LatLng(lat, lng);

            marker.setPosition(newLatLng);

        }

            animateMarker(lat = "51.5072", lng = "0.1276", false);
    })

</script>
