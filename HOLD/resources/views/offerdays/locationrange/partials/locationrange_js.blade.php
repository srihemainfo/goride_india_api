<script>
    //Modal Form Trigger

    $('#addLocationrange').click(function() {

        ResetErrors()

        $('#locationrange_id').val('');

        $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");

        $('#locationrangeForm').trigger("reset");

        $('#map').hide();

        $('#form-modal').modal('show');

    });



    function ResetErrors() {

        $('.invalid-name, .invalid-place-type, .invalid-pickup-charge, .invalid-dropoff-charge, .invalid-passing_charge').text('');

    }



    function showlist() {

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        var existingTable = $('#data-table').DataTable();

        if (existingTable) {

            existingTable.destroy();

        }

        new DataTable('#data-table', {

            ajax: {

                url: '{{env('API_URL')}}GetZones',

                method: 'POST',

                dataSrc: "data",

                data: formDataObject,

            },

            columns: [

                {

                    data: null,

                    render: function(data, type, row, meta) {

                        return meta.row + 1;

                    }

                },

                {
                    data: 'text'
                },

                // { data: 'type' },

                {
                    data: 'from_charge'
                },

                {
                    data: 'to_charge'
                },

                // { data: 'passing_charge' },

                // { data: 'status' },

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



    //  filter search   

    $('#search').on('click', function() {

        const url = 'search';

        var formdata = $('#emp_filter').serialize();

        var pairs = formdata.split('&');

        var formDataObject = {};



        for (var i = 0; i < pairs.length; i++) {

            var pair = pairs[i].split('=');

            var key = decodeURIComponent(pair[0]);

            var value = decodeURIComponent(pair[1]);

            formDataObject[key] = value;

        }

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;



        // Destroy the existing DataTable before reinitializing

        var existingTable = $('#data-table').DataTable();

        if (existingTable) {

            existingTable.destroy();

        }



        // Initialize the DataTable

        new DataTable('#data-table', {

            ajax: {

                url: '{{env('API_URL')}}locationrangeFilter',

                method: 'POST',

                dataSrc: function(json) {

                    // Check if no records are returned

                    if (json.data && json.data.length === 0) {

                        return []; // Return an empty array to show the 'zeroRecords' message

                    } else {

                        return json.data; // Normal data return

                    }

                },

                data: formDataObject,

                error: function() {

                    // Handle any errors during the Ajax request

                    alert('An error occurred while loading data.');

                }

            },

            columns: [

                {

                    data: null,

                    render: function(data, type, row, meta) {

                        return meta.row + 1;

                    }

                },

                {
                    data: 'name'
                },

                {
                    data: 'from_charge'
                },

                {
                    data: 'to_charge'
                },

                // { data: 'passing_charge' },

                {

                    data: null,

                    render: function(data, type, row) {

                        // Custom rendering logic goes here

                        return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';

                    }

                }

            ],

            language: {

                loadingRecords: "Please wait - loading...",

                lengthMenu: "| View _MENU_ records per page",

                zeroRecords: "No records found - sorry",

                infoEmpty: "No records available",

                infoFiltered: "(filtered from _MAX_ total records)",

            },

            processing: true, // Show processing indicator while loading

            serverSide: true, // Enable server-side processing

        });

    });





    $(function() {

        showlist()

    })



    //save update



    $('#saveBtn').click(function(e) {

        e.preventDefault();


        //Validation
        var zonename = $('#name').val().trim();
        if (zonename === '') {

            warningClick('Required', 'Zone Name is required', 'warning');
            $('#name').focus();
            return false;
        }

        var from_charge = $('#from_charge').val().trim();
        if (from_charge === '') {
            warningClick('Required', 'Pickup Charge is Required', 'warning');
            $('#from_charge').focus();
            return false;
        }

        var to_charge = $('#to_charge').val().trim();
        if (to_charge === '') {
            warningClick('Required', 'Dropup Charge is Required', 'warning');
            $('#to_charge').focus();
            return false;
        }


        // Create a new FormData object from the form

        var formData = new FormData($('#locationrangeForm')[0]);



        // Add additional data to the FormData object

        formData.append('token', getCookie('d_token'));

        formData.append('device_id', 0);



        // Perform the AJAX request

        $.ajax({

            url: "{{env('API_URL')}}cordinatestore",

            type: "POST",

            data: formData,

            processData: false, // Prevent jQuery from automatically transforming the data into a query string

            contentType: false, // Prevent jQuery from setting the Content-Type header

            dataType: 'json',

            success: function(response) {

                ResetErrors();



                if (response.status == 400 && response.errors) {

                    ShowErrors(response.errors);

                } else if (response.status == 400 && !response.errors) {

                    Swal.fire("Error", "Zone name already exixts", "warning");

                } else if (response.status == 200) {

                    $('#locationrangeForm').trigger("reset");

                    $('#form-modal').modal('hide');

                    // table.draw();



                    if (response.message == "Record inserted successfully.") {

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Added',

                            text: 'Record inserted successfully.',

                            showConfirmButton: false,

                            timer: 2000

                        });

                        location.reload()

                    } else {

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Updated',

                            text: 'Location Range Updated successfully.',

                            showConfirmButton: false,

                            timer: 4000

                        });
                        setTimeout(function() {
                            location.reload();
                        }, 4000);
                    }

                }

            },

            error: function(data) {

                console.log('Error:', data);

            }

        });

    });



    function delete_employee(id) {

        const url = 'destroyfaredetail';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['emp_id'] = id;

        var settings = {

            "url": "{{env('API_URL')}}" + url,

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify(formDataObject),

        };

        Swal.fire({

            title: 'Are you sure?',

            text: 'You won\'t to Delete this!',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, delete it!',

            cancelButtonText: 'No, cancel!',

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax(settings).done(function(response) {

                    if (response['status'] == 200) {

                        Swal.fire({

                            position: 'center',

                            icon: "success",

                            title: 'Deleted',

                            text: 'Deleted successfully.',

                            showConfirmButton: false,

                            timer: 2000

                        }).then(function() {

                            location.reload()

                        });

                    }

                    if (response['status'] == 400) {

                        warningClick('Error', response['message'], "danger")

                    }

                    if (response['status'] == 500) {

                        warningClick('Error', response['error'], "danger")

                    }

                    if (response['status'] == 401) {

                        unauth()

                    }

                });



            }

            //  else if (result.dismiss === Swal.DismissReason.cancel) {

            //   Swal.fire('Cancelled', 'Your data is safe.', 'error');

            //  }

        });

    }



    $('#reset_emp_filter').on('click', function() {

        $("#emp_filter")[0].reset();

        showlist()

    })





    function edit_employee(id) {

        const url = 'fardetailsedit';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['emp_id'] = id;

        var settings = {

            "url": "{{env('API_URL')}}" + url,

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify(formDataObject),

        };

        $.ajax(settings).done(function(response) {

            if (response['status'] == 200) {

                // console.log("Response Data:", response['data']);

                AssignValues(response['data'])

                $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");
                $('#modalTitle').html('Location Range Edit Form');

                $('#form-modal').modal('show');

                $('#map').show();

            }

            if (response['status'] == 400) {

                warningClick('Error', response['message'], "danger")

            }

            if (response['status'] == 500) {

                warningClick('Error', response['error'], "danger")

            }

            if (response['status'] == 401) {

                unauth()

            }

        });

    }





    function AssignValues(data) {



        // $('#locationrange_id').val(data.emp_id);
        $('#locationrange_id').val(data.id);

        $('#name').val(data.name);

        $('#type').val(data.type);

        $('#from_charge').val(data.from_charge);

        $('#to_charge').val(data.to_charge);

        // $('#passing_charge').val(data.passing_charge);

        // $('#passing_charge').val(data.passing_charge);

        // alert(data.coordinates);

        if (data.coordinates != null) {

            initMap(JSON.parse(data.coordinates))

        }



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





    $(document).ready(function() {

        // Manually close the modal on button click

        $(".close").click(function() {

            $('#form-modal').modal('hide'); // This will hide the modal

        });

    });
</script>