<script>
let currencyval = null;

    $(function() {
        var segments = window.location.href.split('/');
        var lastSegment = segments.pop();
        console.log('sdfg',lastSegment + segments);
        // console.log('sdfdhdtgfg',lastSegment);
    
        editBooking(lastSegment);
    
        var formDataObject = {
            token: getCookie('d_token'),
            device_id: 0
        };
    
        $('#pick_up, #one_way_drop_off').select2({
            ajax: {
                url: "{{env('API_URL')}}getlocation",
                type: "post",
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return {
                        search: params.term,
                        token: formDataObject.token,
                        device_id: formDataObject.device_id
                    };
                },
                processResults: function(response) {
                    const data = response.data;
                    if (data.length === 0) return { results: [] };
                    const formattedData = data.map(item => ({ id: item.id, text: item.text }));
                    return { results: formattedData };
                },
                cache: true
            }
        });
    
       $('#car_type, #edithourlytime').change(function () {
            const formDataObject = {
                token: getCookie('d_token'),
                device_id: 0
            };
    
            const car_type = $('#car_type').val();
            const hourly_time = $('#edithourlytime').val();
    
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
    
        $('#pick_up').change(function() {
            let data = $('#pick_up').select2('data')[0] || {};
            let address = data.area_address || $('#pick_up').val();
            $('#one_way_pickup_address').val(address);
            $('#from_place_id').val(data.place_id || '');
        });
    
        $('#one_way_drop_off').change(function() {
            let data = $('#one_way_drop_off').select2('data')[0] || {};
            let address = data.area_address || $('#one_way_drop_off').val();
            $('#one_way_drop_off_place_type').val(data.place_type || '');
            $('#one_way_dropoff_address').val(address);
            $('#to_place_id').val(data.place_id || '');
        });
    
        $("#pickup_points").click(function() {
            $("#pickup_point_container").toggle(this.checked);
        });
    
        $('.pick_up_point_select').select2({
            ajax: {
                url: "{{env('API_URL')}}getlocation",
                type: "post",
                dataType: 'json',
                delay: 400,
                data: function(params) {
                    return {
                        search: params.term,
                        token: formDataObject.token,
                        device_id: formDataObject.device_id
                    };
                },
                processResults: function(response) {
                    const data = response.data;
                    if (data.length === 0) return { results: [] };
                    const formattedData = data.map(item => ({ id: item.id, text: item.text }));
                    return { results: formattedData };
                },
                cache: true
            }
        });
    
        $('#pickup_date').datepicker("setDate");
    
        ArrivalDetailsContainer('', 'one_way');
        DepartureDetailsContainer('', 'one_way');
    });
    
    $('#update_book').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var spinner = button.find('.spinner-border');
        var buttonText = button.find('.button-text');
    
        button.attr('disabled', true);
    
        var segments = window.location.href.split('/');
        var lastSegment = segments.pop();
    
        const url = 'hourlyupdatebooking';
        var formdata = $('#editbookingForm').serialize();
        var formDataObject = {};
        formdata.split('&').forEach(pair => {
            var [key, value] = pair.split('=');
            formDataObject[decodeURIComponent(key)] = decodeURIComponent(value);
        });
    
        let total_cost = Number($('#total_cost').val()) || 0;
        let driver_amount = Number($('#driver_amount').val()) || 0;
        let isValid = true;
    
        if (driver_amount > total_cost) {
            isValid = false;
            $('#driver_amount').val('');
            $('.invalid_driver_amount').text('Driver Amount should not be more than actual total cost.');
        } else {
            $('.invalid_driver_amount').text('');
        }
    
        formDataObject.token = getCookie('d_token');
        formDataObject.device_id = 0;
        formDataObject.book_id = lastSegment;
    
        if (isValid) {
            var settings = {
                url: "{{env('API_URL')}}" + url,
                method: "POST",
                timeout: 0,
                headers: { "Content-Type": "application/json" },
                data: JSON.stringify(formDataObject),
                beforeSend: function() {
                    spinner.show();
                    buttonText.hide();
                    button.attr('disabled', true);
                },
                complete: function() {
                    spinner.hide();
                    buttonText.show();
                    button.attr('disabled', false);
                }
            };
    
            $.ajax(settings).done(function(response) {
                if (response.status === 200) {
                    alert('success');
                    setCookie('swal', response.message, '1');
                    window.location.href = '/dashboard';
                } else if (response.status === 400) {
                    errornotify(response);
                } else if (response.status === 500) {
                    warningClick('Error', response.error, "danger");
                } else if (response.status === 401) {
                    unauth();
                }
            }).fail(function(jqXHR, textStatus) {
                console.log('Request failed:', textStatus);
            });
        }
    });
    
    $('#car_type').change(function() {
        CarCapacityMaker($(this).val(), '');
    });
    
    $(document).ready(function() {
        var formDataObject = {
            token: getCookie('d_token'),
            device_id: 0
        };
    
        $.ajax({
            url: "{{env('API_URL')}}get-distance-unit",
            type: "POST",
            dataType: 'json',
            data: formDataObject,
            success: function(response) {
                if (response.status === 200) {
                    var unit = response.data.distance_unit;
                    $('.getcurrencycode').text('(' + response.data.currency + ')');
                    currencyval = response.data.currency;
    
                    const symbols = { INR: '₹', USD: '$', GBP: '£', KWD: 'KD', CAD: 'C$' };
    
                    $('.getcurrencycodeshow').text(symbols[currencyval] || '');
                    $('.distance_unit').append('(In ' + unit + ')');
                } else {
                    console.log('Unable to retrieve distance unit');
                }
            },
            error: function(data) {
                console.log('Error fetching distance unit:', data);
            }
        });
    });
    
    function CarCapacityMaker(car_type, data) {
        $('#passenger_count, #luggage_count, #hand_luggage_count').empty();
        var formDataObject = {
            token: getCookie('d_token'),
            device_id: 0,
            fleet_id: car_type
        };
    
        $.ajax({
            url: "{{env('API_URL')}}getfleet",
            type: "POST",
            dataType: 'json',
            data: formDataObject,
            success: function(response) {
                if (response.status === 200) {
                    let passenger_dd = '', luggage_dd = '', hand_luggage_dd = '', child_dd = '';
    
                    for (let i = 1; i <= response.data.passenger; i++)
                        passenger_dd += `<option value="${i}" ${i == data.passengers ? 'selected' : ''}>${i}</option>`;
    
                    for (let i = 0; i <= response.data.luggage; i++)
                        luggage_dd += `<option value="${i}" ${i == data.baggages ? 'selected' : ''}>${i}</option>`;
    
                    for (let i = 0; i <= response.data.hand_luggage; i++)
                        hand_luggage_dd += `<option value="${i}" ${i == data.hand_luggages ? 'selected' : ''}>${i}</option>`;
    
                    for (let i = 0; i <= response.data.child; i++)
                        child_dd += `<option value="${i}" ${i == data.child_seat ? 'selected' : ''}>${i}</option>`;
    
                    $('#passenger_count').html(passenger_dd);
                    $('#luggage_count').html(luggage_dd);
                    $('#hand_luggage_count').html(hand_luggage_dd);
                    $('#child_seat_count').html(child_dd);
    
                    if (data.child_seat < 1) {
                        $('#child_seat_container').empty();
                    } else {
                        let child_seat_dropdown = '';
                        for (let i = 1; i <= data.child_seat; i++) {
                            child_seat_dropdown += `
                            <div class="col-sm-3">
                                <label for="baby_seat_${i}">Child Seat ${i}</label>
                                <select class="form-control" id="baby_seat_${i}" name="baby_seat_${i}">
                                    <option value="Rear Facing" ${i == 1 ? data.firstbaby == 'Rear Facing' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Rear Facing' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Rear Facing' ? 'selected' : '' : ''}>Rear Facing</option>
                                    <option value="Forward Facing" ${i == 1 ? data.firstbaby == 'Forward Facing' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Forward Facing' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Forward Facing' ? 'selected' : '' : ''}>Forward Facing</option>
                                    <option value="Booster" ${i == 1 ? data.firstbaby == 'Booster' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Booster' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Booster' ? 'selected' : '' : ''}>Booster</option>
                                </select>
                                <p class="text-danger invalid-baby-seat-${i}"></p>
                            </div>`;
                        }
                        $('#child_seat_container').html(child_seat_dropdown);
                    }
                } else if (response.status === 400 || response.status === 500) {
                    warningClick('Error', response.message || response.error, "danger");
                } else if (response.status === 401) {
                    unauth();
                }
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }
    
    function ShowClientInfo(data) {
        $('#client_info').html(`
            <div class="col-sm-4">
                <label for="client_name" class="col-form-label">Client Name <span class="required">*</span></label>
                <input type="text" id="client_name" name="client_name" class="form-control" value="${data.fname}" placeholder="Enter client name" readonly>
                <p class="text-danger invalid-client-name"></p>
            </div>
            <div class="col-sm-4">
                <label for="client_email" class="col-form-label">Email</label>
                <input type="text" id="client_email" name="client_email" class="form-control" value="${data.email}" placeholder="Enter client email" readonly>
                <p class="text-danger invalid-client-email"></p>
            </div>
            <div class="col-sm-4">
                <label for="client_mobile" class="col-form-label">Mobile <span class="required">*</span></label>
                <input type="text" id="client_mobile" name="client_mobile" class="form-control" value="${data.mobile}" placeholder="Enter client mobile" readonly>
                <p class="text-danger invalid-client-mobile"></p>
            </div>
        `);
    }
    
    function journeyDetails(data) {
        var inputDate = new Date(data.created_at);
        var day = String(inputDate.getDate()).padStart(2, '0');
        var month = String(inputDate.getMonth() + 1).padStart(2, '0');
        var year = inputDate.getFullYear();
        var formattedDate = `${day}/${month}/${year}`;
        console.log('dsfghj',formattedDate);
    
        var sel = `
        <div class="card-header">
            <h4 class="card-title">Journey Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <label>Job No.</label>
                    <input class="form-control" type="text" value="${data.job_no}" readonly>
                </div>
                <div class="col-sm-3">
                    <label>Booking Date <span class="required">*</span></label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="booking_date" value="${formattedDate}" readonly>
                        <p class="text-danger invalid-booking-date"></p>
                    </div>
                </div>
            </div>
        </div>`;
        $('#journey_container').html(sel);
    }
    
    function editBooking(id) {
        const url = 'hourlyeditbooking';
        var formDataObject = {
            token: getCookie('d_token'),
            device_id: 0,
            book_id: id
        };
        $.ajax({
            url: "{{env('API_URL')}}" + url,
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(formDataObject),
            success: function(response) {
                // console.log(response);
                if (response.status === 200 && response.isEditable) {
                    ShowClientInfo(response.booking_details);
                    journeyDetails(response.booking_details);
                    veh_types('', '', response.booking_details.car_type, 'car_type');
                    CarCapacityMaker(response.booking_details.car_type, response.booking_details);
                    $('#pick_up').empty().append(`<option value="${response.booking_details.from}">${response.booking_details.from}</option>`);
    
                
    
                    var inputDate = new Date(response.booking_details.pickup_date);
                    var formatDate = `${inputDate.getDate()}-${inputDate.getMonth() + 1}-${inputDate.getFullYear()}`;
                    $('#pickup_date').val(formatDate);
                    $('#pickup_time').val(response.booking_details.pickup_time);
                    $('#payment_status').val(response.booking_details.payment_status);
    
                    let orderStatus = response.booking_details.order_status.toLowerCase();
                    let options = '';
    
                    switch (orderStatus) {
                        case 'pending':
                        case 'confirmed':
                        case 'canceled':
                            options = `
                                <option value="Pending" ${orderStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="Confirmed" ${orderStatus === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                                <option value="Canceled" ${orderStatus === 'canceled' ? 'selected' : ''}>Cancelled</option>`;
                            break;
                        case 'assigned':
                            options = `
                                <option value="Assigned" selected>Assigned</option>
                                <option value="Dispatched">Dispatched</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Canceled">Cancelled</option>`;
                            break;
                        case 'dispatched':
                            options = `
                                <option value="Dispatched" selected>Dispatched</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Cancelled</option>`;
                            break;
                        case 'completed':
                            options = `<option value="Completed" selected>Completed</option>`;
                            break;
                        case 'settled':
                            options = `<option value="Settled" selected>Settled</option>`;
                            break;
                        default:
                            options = `
                                <option value="Confirmed">Confirmed</option>
                                <option value="Pending">Pending</option>`;
                    }
                    $('#order_status').html(options);
    
                    $('#edithourlytime').val(response.booking_details.hourly_time);
                    console.log(response.booking_details.hourly_time);
                    if (response.booking_details.hourly_time > 12) {
                        let drivercharge = parseFloat(response.booking_details.driver_amount);
                        $('#driver_amount').val(drivercharge);
                        $('#driver_amount_container').show();
                    } else {
                        $('#driver_amount').val('');
                        $('#driver_amount_container').hide();
                    }
                    
                    $('#total_cost').val(parseInt(response.booking_details.total) +  parseFloat(response.booking_details.driver_amount || 0));
                    $('#driver_amount').val(response.booking_details.driver_amount);
                    $('#payment_method').val(response.booking_details.type);
                    $('#client_id').val(response.booking_details.user_id);
                } else if (response.status === 400 || response.status === 500) {
                    warningClick('Error', response.error || response.message, "danger");
                } else if (response.status === 401) {
                    unauth();
                }
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }
    
    function AssignValues(data) {
        $('#edithourlytime').val(data.hourly_time);
        let drivercharge = 0;
    
        if (data.hourly_time > 12) {
            drivercharge = parseFloat(data.driver_charge);
            $('#driver_amount').val(drivercharge);
            $('#driver_amount_container').show();
        } else {
            $('#driver_amount').val('');
            $('#driver_amount_container').hide();
        }
    
        let total = parseFloat(data.total_hourly) + drivercharge;
        $('#total_cost').val(total);
    }
    
    </script>
