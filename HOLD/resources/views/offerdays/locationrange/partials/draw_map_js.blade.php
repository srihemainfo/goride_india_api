<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvamEcF_mpcwyGNek02hZ6N6SBAK8I2As&callback=initMap&libraries=drawing&v=weekly" async></script><script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        //Select2 AJAX search for Drivers
        $('#zone_name').select2({
         ajax: {
        url: "{{env('API_URL')}}GetZones",
        type: "post",
        dataType: 'json',
        delay: 400,
        data: function (params) {
            var formData = {
                search: params.term, 
                token: getCookie('d_token'), 
                device_id: 0 
            };

            return formData; 
        },
        processResults: function (response) {
            return {
                results: response.data
            };
        },
        cache: true
    }
        });

  $("#zone_name").change(function(){
            if($("#zone_name").val() !== ''){
                $('#map').show()
            }
        });
    })
        
        

function showlist(){
        var formDataObject  = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
        var existingTable = $('#emp-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
        new DataTable('#emp-table', {
    ajax: {
        url: '{{env('API_URL')}}GetZones',
         method: 'POST',
        dataSrc:"data",
        data: formDataObject,
    },
    columns: [
        { 
        data: null,
        render: function(data, type, row, meta) {
          return meta.row + 1;
        }
        },
        { data: 'emp_full_name' },
        { data: 'phone' },
        { data: 'email' },
        { data: 'status' },
        {
            data: null,
            render: function(data, type, row) {
                // Custom rendering logic goes here
                return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';
            }
        }
    ],
});
        }

    window.initMap = function() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 20.5937, lng: 78.9629 },
            zoom: 5,
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

    function UpdateCoordinates(coordinates_string, zone_id) {
    // Add token and device_id to formDataObject
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0; 

    // alert('update');

    // Prepare the data to be sent
    var requestData = $.extend({}, formDataObject, {
        zone_id: zone_id,
        coordinates: coordinates_string
    });

    $.ajax({
        data: requestData, // Send merged data (formDataObject + zone_id + coordinates)
        url: "{{env('API_URL')}}UpdateCoordinates",
        type: "POST",
        dataType: 'json',
        success: function (response) {
            if(response.status == 400) {
                Swal.fire("Update Error", "Coordinates are not updated.", "error");
            }

            if(response.status == 200) {
                Swal.fire("Update Success", "Coordinates are updated.", "success");
                window.location.reload();
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
}


</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvamEcF_mpcwyGNek02hZ6N6SBAK8I2As&callback=initMap&libraries=drawing&v=weekly" async></script>
