<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAvamEcF_mpcwyGNek02hZ6N6SBAK8I2As&callback=initMap&libraries=drawing&v=weekly"
    async></script>
<script>
    $(document).ready(function () {

        $('#fromarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 50);
        });

            $('#fromarea').select2({
                width: '100%',
                placeholder: "Enter Airport, Seaport, Postcode",
                allowClear: true,
                dropdownParent: $('#fromarea').parent(),
                ajax: {
                url: "{{env('API_URL')}}getlocation",
                    type: "POST",
                    dataType: 'json',
                    delay: 400,
                    data: function (params) {
                        return {
                            search: params.term,
                            token: formDataObject.token,
                            device_id: formDataObject.device_id
                        };
                    },
                    processResults: function (response) {
                        const data = response.data || [];
                        return {
                            results: data.map(item => ({
                                id: item.id,      // used as text displayed
                                text: item.text,
                                lat: item.lat,
                                lng: item.lng,
                                fullData: item    // store full object for later use
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            //To

        //     $('#zone1fromarea').select2({
        //     width: '100%',
        //     placeholder: "Enter Airport, Seaport, Postcode",
        //     allowClear: true,
        //     dropdownParent: $('#toarea').parent()
        // });
    
        $('#zone1fromarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 50);
        });

            $('#zone1fromarea').select2({
                width: '100%',
                placeholder: "Enter Airport, Seaport, Postcode",
                allowClear: true,
                dropdownParent: $('#zone1fromarea').parent(),
                ajax: {
                url: "{{env('API_URL')}}getlocation",
                    type: "POST",
                    dataType: 'json',
                    delay: 400,
                    data: function (params) {
                        return {
                            search: params.term,
                            token: formDataObject.token,
                            device_id: formDataObject.device_id
                        };
                    },
                    processResults: function (response) {
                        const data = response.data || [];
                        return {
                            results: data.map(item => ({
                                id: item.id,      // used as text displayed
                                text: item.text,
                                lat: item.lat,
                                lng: item.lng,
                                fullData: item    // store full object for later use
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });


        function showlist() {
            var formDataObject = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;

            var existingTable = $('#zone-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }

            new DataTable('#zone-table', {
                ajax: {
                    url: '{{env('API_URL')}}mapzonelist',
                    method: 'POST',
                    dataSrc: "data",
                    data: formDataObject,
                },
                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'zone_name'
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <span style="padding: 8px;">
                                    <i class="fa-solid fa-eye" style="background: #007bff; color: #fff; padding: 6px 7px; border-radius: 6px; margin-right: 6px; cursor: pointer;" onclick="view_zone(${row.id})"></i>
                                    <i class="fa-solid fa-trash" style="background: red; color: #fff; padding: 6px 7px; border-radius: 6px; cursor: pointer;" onclick="delete_zone(${row.id})"></i>
                                </span>
                            `;
                        }
                    }
                ]
            });
        }
        showlist();


       function maplisttable() {
            const formDataObject = {
                token: getCookie('d_token'),
                device_id: 0
            };

            // Destroy existing DataTable if initialized
            if ($.fn.DataTable.isDataTable('#maplist-table')) {
                $('#maplist-table').DataTable().destroy();
            }

            // Initialize DataTable
            $('#maplist-table').DataTable({ 
                ajax: {
                    url: '{{env('API_URL')}}datatabelsetmaplist',
                    method: 'POST',
                    contentType: 'application/json',
                    data: function (d) {
                        return JSON.stringify(formDataObject);
                    },
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return meta.row + 1; // Serial number
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) { 
                            return data.zone1_name ? data.zone1_name : '-';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return row.zone2_name ? row.zone2_name : '-';
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return data.price; // Serial number
                        }
                    },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <span style="padding: 8px;">
                                    <i class="fa-solid fa-trash"
                                    style="background: red; color: #fff; padding: 6px 7px; border-radius: 6px; cursor: pointer;"
                                    onclick="delete_setzone(${row.id})"></i>
                                </span>
                            `;
                        }
                    }
                ]
            });
        }


        // Call showlist on ready
        maplisttable();

    });

    function Warningclick(title, message, type) {
        Swal.fire({
            title: title,
            text: message,
            icon: type
        });
    }
//     let polygonCoordinates = [];   // Zone 1
//     let polygonCoordinates2 = [];

//    function initMap(mapId) {
//     const map = new google.maps.Map(document.getElementById(mapId), {
//         center: { lat: 20.5937, lng: 78.9629 },
//         zoom: 5,
//     });

//     const drawingManager = new google.maps.drawing.DrawingManager({
//         drawingControl: true,
//         drawingControlOptions: {
//             position: google.maps.ControlPosition.TOP_CENTER,
//             drawingModes: [google.maps.drawing.OverlayType.POLYGON],
//         },
//         circleOptions: {
//             fillColor: "#ffff00",
//             fillOpacity: 1,
//             strokeWeight: 5,
//             clickable: false,
//             editable: true,
//             zIndex: 1,
//         },
//     });

//     drawingManager.setMap(map);

//     google.maps.event.addListener(drawingManager, 'polygoncomplete', function (polygon) {
//         let path = polygon.getPath();
//         let coords = [];

//         for (let i = 0; i < path.length; i++) {
//             coords.push({
//                 lat: path.getAt(i).lat(),
//                 lng: path.getAt(i).lng()
//             });
//         }

//         if (mapId === 'map') {
//             polygonCoordinates = coords;
//         } else if (mapId === 'map1') {
//             polygonCoordinates2 = coords;
//         }

//         console.log("Coordinates for", mapId, coords);
//     });
// }

let polygonCoordinates = [], polygonCenter = {};
let polygonCoordinates2 = [], polygonCenter2 = {};
let map1, map2;
let currentPolygon1, currentPolygon2;
let map1Initialized = false;
let map2Initialized = false;


// Initialize Map 1
function initMap1(mapId) {
    map1 = new google.maps.Map(document.getElementById(mapId), {
        center: { lat: 13.0827, lng: 80.2707 },
        zoom: 13
    });
}

// Initialize Map 2
function initMap2(mapId) {
    map2 = new google.maps.Map(document.getElementById(mapId), {
        center: { lat: 13.0827, lng: 80.2707 },
        zoom: 13
    });
}


function getPolygonLatLngs(polygon) {
    const path = polygon.getPath();
    const coords = [];
    for (let i = 0; i < path.getLength(); i++) {
        const latLng = path.getAt(i);
        coords.push({
            lat: latLng.lat(),
            lng: latLng.lng()
        });
    }
    return coords;
}


// Draw editable polygon
function drawEditableCirclePolygon(center, radius, map) {
    const segments = 4;
    const path = [];

    for (let i = 0; i < segments; i++) {
        const angle = (i * 360 / segments) * Math.PI / 180;
        const lat = center.lat() + (radius / 111320) * Math.cos(angle);
        const lng = center.lng() + (radius / (111320 * Math.cos(center.lat() * Math.PI / 180))) * Math.sin(angle);
        path.push({ lat, lng });
    }

    const polygon = new google.maps.Polygon({
        paths: path,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        editable: true,
        map: map
    });

    polygon.originalCoordinates = [...path];
    polygon.editedCoordinates = [...path];
    polygon.center = {
        lat: center.lat(),
        lng: center.lng()
    };

    const updateEditedCoords = () => {
        polygon.editedCoordinates = getPolygonLatLngs(polygon);
    };

    const polygonPath = polygon.getPath();
    polygonPath.addListener('set_at', updateEditedCoords);
    polygonPath.addListener('insert_at', updateEditedCoords);
    polygonPath.addListener('remove_at', updateEditedCoords);

    return polygon;
}




 


// Zone 1 selection
$('#fromarea').on('select2:select', function (e) {
    const selected = e.params.data;
    const location = new google.maps.LatLng(selected.lat, selected.lng);

    map1.setCenter(location);
    map1.setZoom(12);

    if (currentPolygon1) currentPolygon1.setMap(null);
    currentPolygon1 = drawEditableCirclePolygon(location, 1000, map1);

    polygonCoordinates = getPolygonLatLngs(currentPolygon1);
    polygonCenter = location;
});

// Zone 2 - Draw on selection
$('#zone1fromarea').on('select2:select', function (e) {
    const selected = e.params.data;
    const location = new google.maps.LatLng(selected.lat, selected.lng);

    map2.setCenter(location);
    map2.setZoom(12);

    if (currentPolygon2) currentPolygon2.setMap(null);
    currentPolygon2 = drawEditableCirclePolygon(location, 1000, map2);

    polygonCoordinates2 = getPolygonLatLngs(currentPolygon2);
    polygonCenter2 = location;
});


    function delete_zone(id) { 
    let token = getCookie('d_token');
    let device_id = 0;

    $.ajax({
        url: '{{env('API_URL')}}mapzonedelete',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ 
            token: token,
            id: id,
            device_id: device_id, 
        }),
        success: function (response) {
            if (response.status == 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'The zone has been deleted successfully.',
                    timer: 2000,
                    showConfirmButton: false, 
                });

                setTimeout(function () {
                    location.reload();
                }, 2500); // Reload after 4 seconds
            } else if (response.status == 500) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        error: function (xhr) {
            alert(response.error);
        }
    }); 
}

function showPolygonOnMap(coordinates) {
    const map = new google.maps.Map(document.getElementById("showmap"), {
        zoom: 12,
        center: coordinates[0], // Center at the first coordinate
    });

    const polygon = new google.maps.Polygon({
        paths: coordinates,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
    });

    polygon.setMap(map);
}


   function view_zone(id) {
    let token = getCookie('d_token');
    let device_id = 0;

    $.ajax({
        url: '{{env('API_URL')}}mapzoneshow',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ 
            token: token,
            id: id,
            device_id: device_id
        }),
        success: function (response) {
            if (response.status == 200) {
                let zone = response.showdata;

                // Set zone name
                $('#showname').val(zone.zone_name);

                // Show modal
                $('#show-modal').modal('show');

                // Show map and draw polygon
                setTimeout(function () {
                    $('#showmap').show();
                    showPolygonOnMap(JSON.parse(zone.coordinates));
                }, 300);
            }
        },
        error: function (xhr) {
            alert('Something went wrong while loading the zone.');
        }
    });
}


 function delete_setzone(id) { 
    let token = getCookie('d_token');
    let device_id = 0;

    $.ajax({
        url: '{{env('API_URL')}}setzonedelete',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ 
            token: token,
            id: id,
            device_id: device_id, 
        }),
        success: function (response) {
            if (response.status == 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: 'The zone has been deleted successfully.',
                    timer: 2000,
                    showConfirmButton: false, 
                });

                setTimeout(function () {
                    location.reload();
                }, 2500); // Reload after 4 seconds
            } else if (response.status == 500) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        },
        error: function (xhr) {
            alert(response.error);
        }
    }); 
}

// Map 1 (Zone 1)
$('#addLocationrange').on('click', function () {
    $('#form-modal').modal('show');
});

// Only bind once
$('#form-modal').on('shown.bs.modal', function () {
    $('#map1').show();

    if (!map1Initialized) {
        initMap1('map1');
        map1Initialized = true;
    } else {
        google.maps.event.trigger(map1, 'resize');
    }
});

// Map 2 (Zone 2)
$('#addZoneBtn').on('click', function () {
    const icon = $(this).find('i');
    const zone2 = $('#zone2Section');

    if (zone2.is(':visible')) {
        zone2.slideUp();
        icon.removeClass('fa-minus').addClass('fa-plus');
    } else {
        zone2.slideDown(() => {
            $('#map2').show();

            if (!map2Initialized) {
                initMap2('map2');
                map2Initialized = true;
            } else {
                google.maps.event.trigger(map2, 'resize');
            }
        });

        icon.removeClass('fa-plus').addClass('fa-minus');
    }
});
// Toggle Zone 2 section and map
// $('#addZoneBtn').on('click', function () {
//     const icon = $(this).find('i');
//     const zone2 = $('#zone2Section');

//     if (zone2.is(':visible')) {
//         zone2.hide();
//         icon.removeClass('fa-minus').addClass('fa-plus');
//     } else {
//         zone2.show();
//         $('#map1').show();

//         if (!map1Initialized) {
//             google.maps.event.trigger(document.getElementById("map1"), 'resize');
//             initMap('map1'); // initialize Zone 2 map
//             map1Initialized = true;
//         }

//         icon.removeClass('fa-plus').addClass('fa-minus');
//     }
// });

$('#mapzonesubmit').on('click', function () {
    const zone1Name = $('#name').val().trim();
    const zone2Visible = $('#zone2Section').is(':visible');
    const zone2Name = $('#name1').val().trim();

    let token = getCookie('d_token');
    let device_id = 0;

    if (!zone1Name) {
        Warningclick('Required', 'Zone 1 name is required', 'warning');
        return;
    }

    if (!currentPolygon1 || getPolygonLatLngs(currentPolygon1).length === 0) {
        Warningclick('Required', 'Please draw Zone 1 map', 'warning');
        return;
    }

    const zone1Coords = getPolygonLatLngs(currentPolygon1);

    let payload = {
        name: zone1Name,
        token: token,
        device_id: device_id,
        coordinates: zone1Coords,
        center_lat: polygonCenter.lat(),
        center_lng: polygonCenter.lng()
    };

    if (zone2Visible) {
        if (!zone2Name) {
            Warningclick('Required', 'Zone 2 name is required', 'warning');
            return;
        }

        if (!currentPolygon2 || getPolygonLatLngs(currentPolygon2).length === 0) {
            Warningclick('Required', 'Please draw Zone 2 map', 'warning');
            return;
        }

        const zone2Coords = getPolygonLatLngs(currentPolygon2);

        payload.name1 = zone2Name;
        payload.coordinates1 = zone2Coords;
        payload.center_lat1 = polygonCenter2.lat();
        payload.center_lng1 = polygonCenter2.lng();
    }

    // Submit
    $.ajax({
        url: '{{env('API_URL')}}mapzonestore',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        success: function (response) {
            if (response.status === 200) {
                $('#form-modal').modal('hide');
                $('#mapzoneForm')[0].reset();
                polygonCoordinates = [];
                polygonCoordinates2 = [];

                Swal.fire({
                    icon: 'success',
                    title: 'Zone Saved!',
                    text: 'The zone has been saved successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else if (response.status === 500) {
                alert(response.error);
            }
        },
        error: function (xhr) {
            alert("Error saving zone: " + xhr.responseText);
        }
    });
});



   $('#setmapsubmit').on('click', function () {
    const zoneprice = $('#price').val();
    const firstzone = $('#zone1').val();
    const secondzone = $('#zone2').val();
    let token = getCookie('d_token');
    let device_id = 0;

    if (!zoneprice || parseFloat(zoneprice) <= 0) {
        Warningclick('Required', 'Zone Price is required', 'warning'); 
        return;
    }
    if (!firstzone) {
        Warningclick('Required', 'Please Select Zone 1', 'warning');
        return;
    }
    if (!secondzone) {
        Warningclick('Required', 'Please Select Zone 2', 'warning');
        return;
    }
    if (secondzone == firstzone) {
        Warningclick('Warning', 'Duplicate zones selected. Zone 1 and Zone 2 must be different', 'warning');
        return;
    }

    $.ajax({
        url: '{{env('API_URL')}}setmapstore',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            token: token,
            device_id: device_id,
            price: zoneprice,
            zone1: firstzone,
            zone2: secondzone  
        }),
        success: function (response) {
            if (response.status == 200) {
                $('#form-modal').modal('hide');
                $('#mapzoneForm')[0].reset();
                polygonCoordinates = [];

                Swal.fire({
                    icon: 'success',
                    title: 'Zone Saved!',
                    text: 'The zone has been saved successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                });

                setTimeout(function () {
                    location.reload();
                }, 2000);
            } else if (response.status == 500) {
                alert(response.error);
            }
        },
        error: function (xhr) {
            alert("Error saving zone: " + xhr.responseText);
        }
    });
});


     $('#setmap').on('click', function () {
        $('#map-modal').modal('show'); 
        let token = getCookie('d_token');
        let device_id = 0; 

        $.ajax({
            url: '{{env('API_URL')}}setmaplist',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ 
                token: token,
                device_id: device_id,
            }),
            success: function (response) {
                    // console.log('val:',response.data);

                
                
                if (response.status == 200) {
                    let zoneSelect1 = $('#zone1');
                    let zoneSelect2 = $('#zone2');

                    zoneSelect1.empty().append('<option value="">-- Select Zone 1 --</option>');
                    zoneSelect2.empty().append('<option value="">-- Select Zone 2 --</option>');

                    response.data.forEach(function (item) {
                        let option = `<option value="${item.id}">${item.zone_name}</option>`;
                        zoneSelect1.append(option);
                        zoneSelect2.append(option);
                    });
                } else if (response.status === 500) {
                    alert(response.error);
                }

            },
            error: function (xhr) {
                alert("Error saving zone: " + xhr.responseText);
            }
        });


        
    });

</script>