<script>
    let isValid = true;
    let currencyval = null;
    $('.oneway_offershowhide').hide();
    $('.return_offershowhide').hide();
    
    $(function () {
        phoneCode();
        veh_types('', '', '', 'car_type', 'Active');
    
        $('#pickup_date, #return_pickup_date, #one_way_flight_date, #return_flight_date').datepicker({
            format: "dd-mm-yyyy",
            weekStart: 1
        }).datepicker("setDate", "0");
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
    
        $.ajax({
            url: "{{env('API_URL')}}checkSub-limits",
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            data: JSON.stringify({
                token: getCookie('d_token'),
                device_id: 0
            }),
            success: function (response) {
                if (response['status'] == 400) {
                    errornotify(response);
                }
                if (response['status'] == 500) {
                    Swal.fire({
                        title: "Warning!",
                        text: response['error'],
                        icon: "warning",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    
        $('#search_clients').select2({
            ajax: {
                url: "{{env('API_URL')}}get-clients",
                type: "post",
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
                    const data = response;
                    if (data.length === 0) {
                        return { results: [] };
                    }
                    const formattedData = data.map(item => ({
                        id: item.id,
                        text: item.text
                    }));
                    return { results: formattedData };
                },
                cache: true
            }
        });
    
        $('#search_clients').change(function () {
            var formDataObject = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['customer_id'] = $('#search_clients').val();
    
            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}editcustomer",
                data: formDataObject,
                success: function (response) {
                    ShowClientInfo(response.data);
                }
            });
        });
    
        $('#addCustomer').click(function () {
            ClientModal_ResetErrors();
            $('#customer_id').val('');
            $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");
            $('#customerForm').trigger("reset");
            $('#form-modal').modal('show');
        });
    
        $('#saveBtn').click(function (e) {
            e.preventDefault();
    
            const url = 'createcustomer';
            var formdata = $('#customerForm').serialize();
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
    
            $.ajax({
                data: formDataObject,
                url: "{{env('API_URL')}}"+url,
                type: "POST",
                dataType: 'json',
                success: function (response) {
                    if (response['status'] == 400) {
                        errornotify(response);
                    }
                    if (response['status'] == 500) {
                        warningClick('Error', response['error'], "danger");
                    }
                    if (response['status'] == 401) {
                        unauth();
                    }
                    if (response.status == 200) {
                        $('#customerForm').trigger("reset");
                        $('#form-modal').modal('hide');
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Added',
                            text: 'New Customer added successfully',
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(function () {
                            location.reload();
                        });
                    }
                }
            });
        });
    
        $('#car_type, #pick_up, #one_way_drop_off, #date, #date1, #pickup_time , #return_pickup_time').change(function () {
            let from_area = $('#pick_up').val();
            let to_area = $('#one_way_drop_off').val();
            $('.one_way_arrival_flight_ship_details').hide();
    
            if (from_area && (from_area.toLowerCase().includes('airport') || from_area.toLowerCase().includes('terminal')) && !from_area.toLowerCase().includes('bus')) {
                $('#is_airport_or_ship_one_way').val(1);
                $('.one_way_arrival_flight_ship_details').show();
            } else {
                $('#is_airport_or_ship_one_way').val(0);
                $('.one_way_arrival_flight_ship_details').hide();
            }
    
            let car_type = $('#car_type').val();
            let date = $('#date').val();
            let date1 = $('#date1').val();
            let return_pickup_time = $('#return_pickup_time').val();
    
            if (car_type && from_area && to_area) {
                $("#pickup_points_container").show();
                var formDataObject = {
                    token: getCookie('d_token'),
                    from: from_area,
                    to: to_area,
                    car_type: car_type,
                    return_pickup_time: return_pickup_time,
                    date1: date1,
                    date: date
                };
    
                $.ajax({
                    data: formDataObject,
                    url: "{{env('API_URL')}}distance",
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                        AssignValues(response, 'one_way', currencyval);
                        AssignValues(response, 'return', currencyval);
                    }
                });
            }
        });
    
        $('#car_type, #hourly_time').change(function () {
            const formDataObject = {
                token: getCookie('d_token'),
                device_id: 0
            };
    
            const car_type = $('#car_type').val();
            const hourly_time = $('#hourly_time').val();
    
            if (car_type && hourly_time) {
                $.ajax({
                    url: "{{ env('API_URL') }}gethourlyfare",
                    type: "POST",
                    dataType: 'json',
                    data: {
                        ...formDataObject,
                        car_type: car_type,
                        hourly_time: hourly_time
                    },
                    success: function (response) {
                        AssignValues(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching hourly fare:', error);
                    }
                });
            }
        });
    
        function phoneCode() {
            const url = 'phoneCode';
            var formDataObject = {
                token: getCookie('d_token'),
                device_id: 0
            };
    
            $.ajax({
                url: "{{env('API_URL')}}" + url,
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                data: JSON.stringify(formDataObject),
                success: function (response) {
                    if (response['status'] == 200) {
                        $('#country_code').text(response.data);
                        dial_code_store = response.data;
                        $('#hidden_phoneCode').val(response.data);
                    }
                    if (response['status'] == 400) {
                        warningClick('Error', response['message'], "danger");
                    }
                    if (response['status'] == 500) {
                        warningClick('Error', response['error'], "danger");
                    }
                    if (response['status'] == 401) {
                        unauth();
                    }
                }
            });
        }
    
        $('#book_now').click(function (e) {
            e.preventDefault();
    
            var button = $(this);
            var spinner = button.find('.spinner-border');
            var buttonText = button.find('.button-text');
            const url = 'hourly_store';
    
            let formdata = $('#bookingForm').serialize();
            let pairs = formdata.split('&');
            let formDataObject = {};
    
            for (let i = 0; i < pairs.length; i++) {
                let pair = pairs[i].split('=');
                let key = decodeURIComponent(pair[0]);
                let value = decodeURIComponent(pair[1]);
                formDataObject[key] = value;
            }
    
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
    
            spinner.show();
            buttonText.hide();
            $('#book_now').attr('disabled', true);
    
            if (isValid) {
                $.ajax({
                    data: formDataObject,
                    url: "{{ env('API_URL') }}" + url,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        $('#book_now').attr('disabled', false);
                    
                        if (response.status == 200) {
                            // setCookie('swal', response.message, '1');
                    
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Created',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 3000,
                            }).then(function () {
                                window.location.href = "/hourlydashboard";
                            });
                    
                        } else if (response.status == 400) {
                            errornotify(response);
                        } else if (response.status == 500) {
                            warningClick('Alert', response.error, "danger");
                        } else if (response.status == 401) {
                            unauth();
                        }
                    
                        spinner.hide();
                        buttonText.show();
                    },

                    error: function () {
                        $('#book_now').attr('disabled', false);
                        Swal.fire("Booking Error", "Booking not saved.", "error");
                        spinner.hide();
                        buttonText.show();
                    }
                });
            } else {
                $('#book_now').attr('disabled', false);
                spinner.hide();
                buttonText.show();
            }
        });
    
        $('#pick_up, #one_way_drop_off, #return_pick_up, #return_drop_off').select2({
            ajax: {
                url: "{{env('API_URL')}}getlocation",
                type: "post",
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
                    const data = response.data;
                    if (data.length === 0) {
                        return { results: [] };
                    }
    
                    const formattedData = data.map(item => ({
                        id: item.id,
                        text: item.text
                    }));
    
                    return { results: formattedData };
                },
                cache: true
            }
        });
    
        $('#country_websites').select2({
            ajax: {
                url: "{{env('API_URL')}}country_websites",
                type: "post",
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
                    const data = response.data;
                    if (data.length === 0) {
                        return { results: [{ id: 'CRM', text: 'From CRM (CR)' }] };
                    }
    
                    const formattedData = [
                        { id: 'CRM', text: 'From CRM (CR)' },
                        ...data.map(item => ({ id: item.id, text: item.text }))
                    ];
    
                    return { results: formattedData };
                },
                cache: true
            }
        });
    });
    
    function ShowClientInfo(data) {
        $('#client_info').empty();
        $('#client_info').html(`
            <div class="col-sm-4">
                <label for="client_name" class="col-form-label">Client Name <span class="required">*</span></label>
                <input type="text" id="client_name" name="client_name" class="form-control" value="${data.f_name}" placeholder="Enter client name" readonly>
                <p class="text-danger invalid-client-name"></p>
            </div>
            <div class="col-sm-4">
                <label for="client_email" class="col-form-label">Email</label>
                <input type="text" id="client_email" name="client_email" class="form-control" value="${data.email}" placeholder="Enter client email" readonly>
                <p class="text-danger invalid-client-email"></p>
            </div>
            <div class="col-sm-4">
                <label for="client_mobile" class="col-form-label">Mobile <span class="required">*</span></label>
                <input type="text" id="client_mobile" name="client_mobile" class="form-control" value="${data.phone}" placeholder="Enter client mobile" readonly>
                <p class="text-danger invalid-client-mobile"></p>
            </div>`);
    }
    
    function ClientModal_ResetErrors() {
        $('.invalid-first-name, .invalid-phone-no, .invalid-email').text('');
    }
    
    function ClientModal_ShowErrors(errors) {
        if (errors.first_name) {
            $('.invalid-first-name').text(errors.first_name);
        }
        if (errors.phone) {
            $('.invalid-phone-no').text(errors.phone);
        }
        if (errors.email) {
            $('.invalid-email').text(errors.email);
        }
    }
    
    function CarCapacityMaker(car_type) {
        $('#passenger_count').empty();
        $('#luggage_count').empty();
        $('#hand_luggage_count').empty();
    
        var formDataObject = {
            token: getCookie('d_token'),
            device_id: 0,
            fleet_id: car_type
        };
    
        $.ajax({
            data: formDataObject,
            url: "{{env('API_URL')}}getfleet",
            type: "POST",
            dataType: 'json',
            success: function (response) {
                if (response['status'] == 200) {
                    let passenger_dd = '', luggage_dd = '', hand_luggage_dd = '', child_dd = '';
    
                    for (let i = 1; i <= response['data'].passenger; i++) {
                        passenger_dd += `<option value="${i}" ${response['data'].passenger == i ? 'selected' : ''}>${i}</option>`;
                    }
    
                    for (let i = 0; i <= response['data'].luggage; i++) {
                        luggage_dd += `<option value="${i}" ${response['data'].luggage == i ? 'selected' : ''}>${i}</option>`;
                    }
    
                    for (let i = 0; i <= response['data'].hand_luggage; i++) {
                        hand_luggage_dd += `<option value="${i}" ${response['data'].hand_luggage == i ? 'selected' : ''}>${i}</option>`;
                    }
    
                    for (let i = 0; i <= response['data'].child; i++) {
                        child_dd += `<option value="${i}" ${response['data'].child == i ? 'selected' : ''}>${i}</option>`;
                    }
    
                    $('#passenger_count').html(passenger_dd);
                    $('#luggage_count').html(luggage_dd);
                    $('#hand_luggage_count').html(hand_luggage_dd);
                    $('#child_seat_count').html(child_dd);
                }
    
                if (response['status'] == 400) {
                    warningClick('Error', response['message'], "danger");
                }
    
                if (response['status'] == 500) {
                    warningClick('Error', response['error'], "danger");
                }
    
                if (response['status'] == 401) {
                    unauth();
                }
            }
        });
    }
    
    function AssignValues(data) {
        let drivercharge = 0;
    
        if (data.hourly_time > 12) {
            drivercharge = parseFloat(data.driver_charge);
            $('#driver_charges').val(drivercharge);
            $('#driver_charge_section').show();
        } else {
            $('#driver_charges').val('');
            $('#driver_charge_section').hide();
        }
    
        let total = parseFloat(data.total_hourly) + drivercharge;
        $('#hourly_total_cost').val(total);
    }
</script>
