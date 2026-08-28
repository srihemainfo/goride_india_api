<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvamEcF_mpcwyGNek02hZ6N6SBAK8I2As&callback=initMap&libraries=drawing&v=weekly" async></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Select2 AJAX search for Drivers
        $('#zone_name').select2({
            ajax: {
                url: "{{route('GetZones')}}",
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

        $("#zone_name").change(function(){
            if($("#zone_name").val() !== ''){
                $('#map').show()
            }
        });
    })

    window.initMap = function() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 51.5072, lng: 0.1276 },
            zoom: 8,
        });

        const drawingManager = new google.maps.drawing.DrawingManager({
            drawingControl: true,
            drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON
            ],
            },
            markerOptions: {
                icon:
                    "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
                },
                circleOptions: {
                    fillColor: "#ffff00",
                    fillOpacity: 1,
                    strokeWeight: 5,
                    clickable: false,
                    editable: true,
                    zIndex: 1,
                },
        });

        google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
            let path = polygon.getPath()
            let coordinates = []
            let zone_id = $('#zone_name').val()

            for(let i = 0 ; i < path.length ; i++) {
                coordinates.push({
                lat: path.getAt(i).lat(),
                lng: path.getAt(i).lng()
                });
            }

            coordinates_string = JSON.stringify(coordinates);

            UpdateCoordinates(coordinates_string, zone_id)
        });

        drawingManager.setMap(map);
    }

    function UpdateCoordinates(coordinates_string, zone_id){
        $.ajax({
            data: {zone_id: zone_id, coordinates: coordinates_string},
            url: "{{ route('UpdateCoordinates') }}",
            type: "POST",
            dataType: 'json',
            success: function (response) {
                if(response.status == 400){
                    Swal.fire("Update Error", "Coordinates are not updated.", "error");
                }

                if(response.status == 200){
                    window.location = '{{ url('/locationrange') }}'
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        })
    }

</script>
