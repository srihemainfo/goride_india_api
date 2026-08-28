<script>



    $(function () {
        showlist()

        $('#fromarea').select2({
            width: '100%',
            placeholder: "Enter Airport, Seaport, Postcode",
            allowClear: true,
            dropdownParent: $('#fromarea').parent()
        });
    
        $('#fromarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 100);
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
                                id: item.id,
                                text: item.text
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            //To

            $('#toarea').select2({
            width: '100%',
            placeholder: "Enter Airport, Seaport, Postcode",
            allowClear: true,
            dropdownParent: $('#toarea').parent()
        });
    
        $('#toarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 100);
        });

            $('#toarea').select2({
                width: '100%',
                placeholder: "Enter Airport, Seaport, Postcode",
                allowClear: true,
                dropdownParent: $('#toarea').parent(),
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
                                id: item.id,
                                text: item.text
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            //edit from place 

             $('#editfromarea').select2({
            width: '100%',
            placeholder: "Enter Airport, Seaport, Postcode",
            allowClear: true,
            dropdownParent: $('#editfromarea').parent()
        });
    
        $('#editfromarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 100);
        });

            $('#editfromarea').select2({
                width: '100%',
                placeholder: "Enter Airport, Seaport, Postcode",
                allowClear: true,
                dropdownParent: $('#editfromarea').parent(),
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
                                id: item.id,
                                text: item.text
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            //edit To place

              $('#edittoarea').select2({
            width: '100%',
            placeholder: "Enter Airport, Seaport, Postcode",
            allowClear: true,
            dropdownParent: $('#edittoarea').parent()
        });
    
        $('#edittoarea').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-container--open .select2-search__field')?.focus();
            }, 100);
        });

            $('#edittoarea').select2({
                width: '100%',
                placeholder: "Enter Airport, Seaport, Postcode",
                allowClear: true,
                dropdownParent: $('#edittoarea').parent(),
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
                                id: item.id,
                                text: item.text
                            }))
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });



        //Modal Form Trigger

        $(document).ready(function () {

            $('#add_area').click(function () {

                // alert('king');

                $('#fixedprice-modal').modal('show'); // Ensure that #form-modal1 exists

            });

        });


 


        //Reset filter values

        $('#reset_filter').on('click', function () {

            $('#cus_filter_form')[0].reset()

            showlist()

        })

    })

    function handleFareSetting(select) {
    const url = select.value;
    if (url) {
        window.location.href = url; // Redirect to the selected page
    }
    }

    function showlist() {

        var formDataObject = {

            token: getCookie('d_token'),

            device_id: 0

        };



        if ($.fn.DataTable.isDataTable('#cus-table')) {

            $('#cus-table').DataTable().destroy();

        }



        $('#fare-table').DataTable({

            processing: true,

            searching: true,

            ajax: {

                url: '{{ env('API_URL')}}faresetting',

                method: 'POST',

                data: formDataObject,

                dataSrc: "data"

            },

            columns: [

                {

                    data: null,

                    render: function (data, type, row, meta) {

                        return meta.row + 1;

                    }

                },

                { data: 'pickup' },

                { data: 'dropoff' },

                { data: 'price' },

                {

                    data: null,

                    render: function (data, type, row) {

                        return `

                        <span style="padding: 8px;">

                            <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="fixedpriceedit(${row.id})"></i>

                        </span>

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

                        </span>`;

                    }

                },

            ],

            responsive: {

                details: {

                    type: 'column',

                    target: 'tr'

                }

            }

        });

    }



    function AssignValues(data) {
        console.log('value',data)

        $('#edit_id').val(data.id);

        // $('#editfromarea').val(data.pickup);
        $('#editfromarea').empty().append(
                    `<option value="${data.pickup}">${data.pickup}</option>`
                );
        // $('#editfromarea').val(data.pickup).trigger('change');

        // $('#edittoarea').val(data.dropoff);
        $('#edittoarea').empty().append(
                    `<option value="${data.dropoff}">${data.dropoff}</option>`
                );

        $('#editfixed_price').val(data.price);

        // $('#pickup_extra').val(data.p_extra);

        // $('#drop_extra').val(data.d_extra);

        // $('#area_status').val(data.status);

        // $('#id').val(data.id);

    }



    function fixedpriceedit(id) {

        const url = 'farepriceedit';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['fareprice_id'] = id;

        var settings = {

            "url": "{{ env('API_URL')}}" + url,

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify(formDataObject),

        };

        $.ajax(settings).done(function (response) {

            if (response['status'] == 200) {

                // $('#addsaveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");

                $('#fixedprice-editmodal').modal('show');

                AssignValues(response['data'])

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



    function cus_del(id) {

        const url = 'fareprice_delete';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['area_id'] = id;

        var settings = {

            "url": "{{ env('API_URL')}}" + url,

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify(formDataObject),

        };

        Swal.fire({

            title: 'Are you sure?',

            text: 'You won\'t be able to revert this!',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, delete it!',

            cancelButtonText: 'No, cancel!',

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax(settings).done(function (response) {

                    if (response['status'] == 200) {

                        Swal.fire({

                            position: "center",

                            icon: "success",

                            title: response['message'],

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function () {
                            location.reload();
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



            } else if (result.dismiss === Swal.DismissReason.cancel) {

                Swal.fire('Cancelled', 'Your data is safe.', 'error');

            }

        });

    }



    $('#addsaveBtn').click(function (e) {

        e.preventDefault(); 



        const url = 'fareprice_store';
        var fixed_price_val = $('#fixed_price').val();
        if (fixed_price_val == 0) {
            warningClick('Required', 'Price field is required.', "warning");
            $('#fixed_price').focus();
            return false;
        }

        // var addpickExtra = $('#add_pickup_extra').val().trim();
        // if (addpickExtra === '') {
        //     // alert('Price Extra Amount is required.');
        //     warningClick('Required', 'Pickup Extra Amount is required', 'warning');
        //     $('#add_pickup_extra').focus();
        //     return false; // Stop form submission
        // }

        var formdata = new FormData($('#fixedPriceForm')[0]); // FormData object



        // Append additional fields

        formdata.append('token', getCookie('d_token'));

        formdata.append('device_id', 0);



        $.ajax({

            data: formdata,

            url: "{{ env('API_URL')}}" + url,

            type: "POST",

            processData: false, // Important for FormData

            contentType: false, // Important for FormData

            dataType: 'json',

            success: function (response) {

                if (response.status == 400) {

                    errornotify(response);

                } else if (response.status == 500) {

                    warningClick('Error', response['error'], "danger");

                } else if (response.status == 401) {

                    unauth();

                } else if (response.status == 200) {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Created',

                        text: 'Area has been created successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });



                }

            },

            error: function (data) {

                console.log('Error:', data);

            }

        });

    });


    
    $('#editsaveBtn').click(function (e) {

        e.preventDefault();



        const url = 'fixedpriceupdate';
        var fixed_price_editval = $('#editfixed_price').val();
        if (fixed_price_editval == 0) {
            warningClick('Required', 'Price field is required.', "warning");
            $('#editfixed_price').focus();
            return false;
        }
        // var pickup_extra = $('#pickup_extra').val().trim();
        // if (pickup_extra === '') {
        //     // alert('Drop Extra Amount is required.');
        //     warningClick('Required', 'Pickup Extra Amount is required.', "warning");
        //     $('#pickup_extra').focus();
        //     return false; // Stop form submission
        // }
        // var drop_extra = $('#drop_extra').val().trim();
        // if (drop_extra === '') {
        //     // alert('Drop Extra Amount is required.');
        //     warningClick('Required', 'Drop Extra Amount is required.', "warning");
        //     $('#drop_extra').focus();
        //     return false; // Stop form submission
        // }

        var formdata = new FormData($('#fixedPriceeditForm')[0]); // FormData object

        // console.log("Jana VAlue",formdata);
        //     for (let [key, value] of formdata.entries()) {
        //     console.log("Jana VAlue",key, value);
        // }


        // Append additional fields

        formdata.append('token', getCookie('d_token'));

        formdata.append('device_id', 0);


        $.ajax({

            data: formdata,

            url: "{{ env('API_URL')}}" + url,

            type: "POST",

            processData: false, // Important for FormData

            contentType: false, // Important for FormData

            dataType: 'json',

            success: function (response) {

                if (response.status == 400) {

                    errornotify(response);

                } else if (response.status == 500) {

                    warningClick('Error', response['error'], "danger");

                } else if (response.status == 401) {

                    unauth();

                } else if (response.status == 200) {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Area has been update successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });



                }

            },

            error: function (data) {

                console.log('Error:', data);

            }

        });

    });

    $('#search').on('click', function () {



        const url = 'filterarea';

        var formdata = $('#cus_filter_form').serialize();

        var pairs = formdata.split('&');

        var formDataObject = {};



        // Split and decode form data into an object

        for (var i = 0; i < pairs.length; i++) {

            var pair = pairs[i].split('=');

            var key = decodeURIComponent(pair[0]);

            var value = decodeURIComponent(pair[1]);

            formDataObject[key] = value;

        }



        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        if ($.fn.DataTable.isDataTable('#cus-table')) {

            $('#cus-table').DataTable().destroy();

        }

        $('#cus-table').DataTable({

            processing: true,

            searching: true,

            ajax: {

                url: '{{ env('API_URL')}}' + url,

                method: 'POST',

                data: function (d) {

                    // Append formDataObject to the request

                    return $.extend({}, d, formDataObject);

                },

                dataSrc: "data"  // Assuming the response has a 'data' field

            },

            columns: [

                {

                    data: null,

                    render: function (data, type, row, meta) {

                        return meta.row + 1; // Serial number

                    }

                },

                { data: 'area' },

                { data: 'pincode' },

                { data: 'p_extra' },

                { data: 'd_extra' },

                { data: 'status' },

                {

                    data: null,

                    render: function (data, type, row) {

                        return `

                        <span style="padding: 8px;">

                            <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(${row.id})"></i>

                        </span>

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

                        </span>`;

                    }

                }

            ],

            responsive: {

                details: {

                    type: 'column',

                    target: 'tr'

                }

            }

        });

    });















    $(document).ready(function () {

        $('#add_area_name').select2({

            dropdownParent: $('#form-modal1'),

            placeholder: 'Enter the Area',

            minimumInputLength: 1,

            ajax: {

                url: "{{ env('API_URL')}}getarealocation",

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

                    console.log('Response:', response);

                    const data = response || [];

                    if (!Array.isArray(data) || data.length === 0) {

                        console.log('No results found');

                        return { results: [] };

                    }

                    const formattedData = data.map(item => ({

                        id: item.text,

                        text: item.text || item.label

                    }));



                    console.log('Formatted Data:', formattedData);

                    return { results: formattedData };

                },

                cache: true,

                error: function (xhr, status, error) {

                    console.error('Error fetching data:', error);

                }

            }

        });

        $('#add_area_name').on('focus', function () {

            $(this).select2('open');

        });

    });





    //  function validateNumericInput(input) {

    //         // Allow only numbers and one decimal point

    //         const regex = /^[0-9]*\.?[0-9]*$/;

    //         if (!regex.test(input.value)) {

    //             input.value = input.value.replace(/[^0-9.]/g, ''); // Remove invalid characters

    //         }

    //     }



    //     // Attach the event listener for both fields

    //     document.getElementById('add_pickup_extra').addEventListener('input', function() {

    //         validateNumericInput(this);

    //     });



    //     document.getElementById('add_drop_extra').addEventListener('input', function() {

    //         validateNumericInput(this);

    //     });



    //validation 






    // function validateNumericInput(input) {

    //         // Allow only numbers and one decimal point

    //         const regex = /^[0-9]*\.?[0-9]*$/;

    //         if (!regex.test(input.value)) {

    //             input.value = input.value.replace(/[^0-9.]/g, ''); // Remove invalid characters

    //         }

    //     }



    //     // Attach the event listener for both fields

    //     document.getElementById('pickup_extra').addEventListener('input', function() {

    //         validateNumericInput(this);

    //     });



    //     document.getElementById('drop_extra').addEventListener('input', function() {

    //         validateNumericInput(this);

    //     });

</script>