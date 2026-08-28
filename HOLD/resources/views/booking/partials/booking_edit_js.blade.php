<script>

let currencyval = null;
let tariff_type = null;



$(function() {
    //Trigger client info fields 

    var key = window.location.href;
    var segments = key.split('/');
    var lastSegment = segments.pop();

    editBooking(lastSegment)

    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

    $('#one_way_pick_up, #one_way_drop_off').select2({
        ajax: {
            url: "{{env('API_URL')}}getlocation",
            type: "post",
            dataType: 'json',
            delay: 400,
            data: function(params) {

                return {
                    search: params.term, // search term
                    token: formDataObject.token,
                    device_id: formDataObject.device_id
                };
            },
            processResults: function(response) {
                const data = response.data;
                console.log(data.length);
                if (data.length == 0) {
                    return {
                        results: []
                    };
                }
                if (data.length > 0) {
                    console.log(data.length);
                    const formattedData = data.map(item => ({
                        id: item.id,
                        text: item.text
                    }));
                    return {
                        results: formattedData
                    };
                }
            },
            cache: true
        }
    })

    $('#car_type, #one_way_pick_up, #one_way_drop_off, #one_way_pickup_date, #one_way_pickup_time, #return_pickup_date').change(function() {
        let from_area = $('#one_way_pick_up').val()
        let to_area = $('#one_way_drop_off').val()
        let car_type = $('#car_type').val();
        let tariff_return_date;
        if(tariff_type == 'tariff_oneway'){
            tariff_return_date = '';
        }else{
            tariff_return_date = $('#return_pickup_date').val();
        }
        let edit_date = $('#one_way_pickup_date').val();
        let edit_time = $('#one_way_pickup_time').val();
        let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

        if (car_type && from_area && to_area) {
            console.log('success',tariff_return_date)
            //show pickpoint checkbox
            $("#pickup_points_container").show()
            var formDataObject = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['from'] = from_area;
            formDataObject['to'] = to_area;
            formDataObject['car_type'] = car_type;
            formDataObject['edit_date'] = edit_date;
            formDataObject['edit_time'] = edit_time;
            formDataObject['tariff_type'] = tariff_type;
            formDataObject['tariff_return_date'] = tariff_return_date;
            formDataObject['journey_type'] = journeytype;

            $.ajax({
                data: formDataObject,
                url: "{{env('API_URL')}}distance",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    
                    if (response.fare_type && response.fare_type.length > 0) {
                        let fareTypeValue = response.fare_type[0].fare_type; 
                        let tariff_type1 = response.tariff_type; 
                        let tariff_results = response.tariff_results;
                        if (fareTypeValue == "2") {
                            // alert('come');
                            AssignValueFore_tariff(response,tariff_results,tariff_type1);
                        } else {
                            AssignValues(response, 'one_way', currencyval);
                        }
                    } else {
                        AssignValues(response, 'one_way', currencyval);
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        } else {
            console.log('One way request not fulfill.')
        }
    });

    $('#one_way_pick_up').change(function() {
        let place = $('#one_way_pick_up').select2('data')[0].place_type ?
            $('#one_way_pick_up').select2('data')[0].place_type : ''

        let place_id = $('#one_way_pick_up').select2('data')[0].place_id ?
            $('#one_way_pick_up').select2('data')[0].place_id : ''

        let area = $('#one_way_pick_up').val()

        let address = $('#one_way_pick_up').select2('data')[0].area_address ?
            $('#one_way_pick_up').select2('data')[0].area_address : area

        let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

        $('#one_way_pickup_address').val(address)
        $('#from_place_id').val(place_id)

    })

    $('#one_way_drop_off').change(function() {
        let place = $('#one_way_drop_off').select2('data')[0].place_type ?
            $('#one_way_drop_off').select2('data')[0].place_type : ''

        let place_id = $('#one_way_drop_off').select2('data')[0].place_id ?
            $('#one_way_drop_off').select2('data')[0].place_id : ''

        let area = $('#one_way_drop_off').val()

        let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

        let address = $('#one_way_drop_off').select2('data')[0].area_address ?
            $('#one_way_drop_off').select2('data')[0].area_address : area

        $('#one_way_drop_off_place_type').val(place)

        $('#one_way_dropoff_address').val(address)
        $('#to_place_id').val(place_id)

    })

    $("#pickup_points").click(function() {
        if ($(this).is(":checked")) {
            $("#pickup_point_container").show();
        } else {
            $("#pickup_point_container").hide();
        }
    })

    $('.pick_up_point_select').select2({
        ajax: {
            url: "{{env('API_URL')}}getlocation",
            type: "post",
            dataType: 'json',
            delay: 400,
            data: function(params) {

                return {
                    search: params.term, // search term
                    token: formDataObject.token,
                    device_id: formDataObject.device_id
                };
            },
            processResults: function(response) {
                const data = response.data;
                console.log(data.length);
                if (data.length == 0) {
                    return {
                        results: []
                    };
                }
                if (data.length > 0) {
                    console.log(data.length);
                    const formattedData = data.map(item => ({
                        id: item.id,
                        text: item.text
                    }));
                    return {
                        results: formattedData
                    };
                }
            },
            cache: true
        }
    })

    $(document).on('click', '.add_pickup_point', function() {
        let location = $('.pick_up_point_select').val() ? $('.pick_up_point_select').val() : null;
        let location_array = $('input[name="pickup_location[]"]').map(function() {
            return $(this).val();
        }).get();

        let new_location_field = `<div class="col-sm-4 mb-2 d-flex justify-content-between location_row">
                                            <input type="text" name="pickup_location[]" id="pickup_location" class="form-control from_location mr-1" value="${location}">
                                            <button type="button" class="btn btn-danger remove_booking" title="Remove Location">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>`

        if (location && !location_array.includes(location)) {
            $('.pick_up_point_select').val('').trigger('change');
            $('#points_values').append(new_location_field);
        } else {
            console.log('No Values.');
        }
    })

    $(document).on('click', '.remove_booking', function() {
        $(this).closest('.location_row').remove()
    })

    // $('#one_way_pickup_date').datepicker("setDate", );
    // $('#return_pickup_date').datepicker("setDate", );

    //Show flight details container, if from place is airport
    ArrivalDetailsContainer('', 'one_way')
    DepartureDetailsContainer('', 'one_way')

    //Select after landing pickup time
    $('#one_way_flight_pickup_time').val('').trigger('change')

    //Select payment status
    $('#one_way_payment_status').val('').trigger('change')

    //Select payment method
    $('#one_way_payment_method').val('').trigger('change')

    //Select order status
    $('#one_way_order_status').val('').trigger('change')

    //Check pickup point checkbox, if it selected
    // $('#pickup_points').prop('checked', true);

})

$('#update_book').on('click', function(e) {
    e.preventDefault();
    var button = $(this);
    var spinner = button.find('.spinner-border');
    var buttonText = button.find('.button-text');

   
    $('#update_book').attr('disabled', true);

    var key1 = window.location.href;
    var segments1 = key1.split('/');
    var lastSegment1 = segments1.pop();

    const url = 'updatebooking';
    var formdata = $('#editbookingForm').serialize();
    var pairs = formdata.split('&');
    var formDataObject = {};

    let total_cost = Number($('#one_way_total_cost').val()) || 0;
    let driver_amount = Number($(this).val()) || 0;
    let isVaild = true;
    if (driver_amount > total_cost) {
        isVaild = false;
        $('#one_way_driver_amount').val('')
        $('.invalid_one_way_driver_amount').text('Driver Amount should not be more than actual total cost.');
    } else {
        isVaild = true;
        $('.invalid_one_way_driver_amount').text('');
    }

    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        var key = decodeURIComponent(pair[0]);
        var value = decodeURIComponent(pair[1]);
        formDataObject[key] = value;
    }
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    formDataObject['book_id'] = lastSegment1;
    delete formDataObject['pickup_location[]'];
    var inputValues = [];
    $("input[id='pickup_location']").each(function() {
        inputValues.push($(this).val());
    });
    formDataObject['pickup_location'] = inputValues;

    
    if (isVaild) {
        var settings = {
            "url": "{{env('API_URL')}}" + url,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify(formDataObject),
        };
        spinner.show();
        buttonText.hide();
        $('#update_book').attr('disabled', true);

        $.ajax(settings).done(function(response) {
            // console.log('Jana',response);
            $('#update_book').attr('disabled', false);
            if (response['status'] == 200) {
                setCookie('swal', response['message'], '1')
                // location.reload()
                window.location.href = '/dashboard';
            }
            if (response['status'] == 400) {
                errornotify(response)
            }
            if (response['status'] == 500) {
                warningClick('Error', response['error'], "danger")
            }
            if (response['status'] == 401) {
                unauth()
            }
            spinner.hide();
            buttonText.show();
            
        });
    }
})

$('#one_way_driver_amount').on('keyup', function() {
    let total_cost = Number($('#one_way_total_cost').val()) || 0;
    let driver_amount = Number($(this).val()) || 0;

    if (driver_amount > total_cost) {
        $('#one_way_driver_amount').val('')
        $('.invalid_one_way_driver_amount').text('Driver Amount should not be more than actual total cost.');
    } else {
        $('.invalid_one_way_driver_amount').text(''); // Clear the error message if valid
    }
});


$('#car_type').change(function() {
    CarCapacityMaker($('#car_type').val(), '')
})

$(document).ready(function() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    $.ajax({
        data: formDataObject,
        url: "{{env('API_URL')}}get-distance-unit",
        type: "POST",
        dataType: 'json',
        success: function(response) {
            
            if (response.status === 200) {
                // console.log('Error fetching distance unit:', response.data.distance_unit);
                var unit = response.data.distance_unit;
                $('.getcurrencycode').text('(' + response.data.currency + ')');
                currencyval = response.data.currency;

                const symbols = {
                    INR: '₹',
                    USD: '$',
                    GBP: '£',
                    KWD: 'KD',
                    CAD: 'C$'
                };


                if (symbols[currencyval]) {
                    $('.getcurrencycodeshow').text(symbols[currencyval]);
                } else {
                    $('.getcurrencycodeshow').text(''); // Empty if not found
                }
                
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
    // console.log(data, 'hiiiiiiiiiiiiiiiiii')
    $('#passenger_count').empty()
    $('#luggage_count').empty()
    $('#hand_luggage_count').empty()
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    formDataObject['fleet_id'] = car_type;

    $.ajax({
        data: formDataObject,
        url: "{{env('API_URL')}}getfleet",
        type: "POST",
        dataType: 'json',
        success: function(response) {
            if (response['status'] == 200) {
                //dd means dropdown
                let passenger_dd = ''
                let luggage_dd = ''
                let hand_luggage_dd = ''
                let child_dd = ''
                // console.log(response['data'].passenger);
                // console.log(response['data'].luggage);
                for (let i = 1; i <= response['data'].passenger; i++) {
                    passenger_dd +=
                        `<option value="${i}" ${i == data.passengers ? 'selected' : ''}>${i}</option>`;
                }

                for (let i = 0; i <= response['data'].luggage; i++) {
                    luggage_dd +=
                        `<option value="${i}" ${i == data.baggages ? 'selected' : ''}>${i}</option>`;
                }

                for (let i = 0; i <= response['data'].hand_luggage; i++) {
                    hand_luggage_dd +=
                        `<option value="${i}" ${i == data.hand_luggages ? 'selected' : ''}>${i}</option>`;
                }

                for (let i = 0; i <= response['data'].child; i++) {
                    child_dd +=
                        `<option value="${i}" ${i == data.child_seat ? 'selected' : ''}>${i}</option>`;
                }
                // console.log('janma',passenger_dd);
                // console.log('janma',luggage_dd);
                // console.log('janma',hand_luggage_dd);
                $('#passenger_count').html(passenger_dd)
                $('#luggage_count').html(luggage_dd)
                $('#hand_luggage_count').html(hand_luggage_dd)
                $('#child_seat_count').html(child_dd)



                let child_seat_dropdown = ''
                for (let i = 1; i <= data.child_seat; i++) {
                    child_seat_dropdown += `<div class="col-sm-3">
                               <label for="baby_seat_${i}">Child Seat ${i}</label>
                               <select class="form-control" id="baby_seat_${i}" name="baby_seat_${i}">
                                   <option value="Rear Facing" ${i == 1 ? data.firstbaby == 'Rear Facing' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Rear Facing' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Rear Facing' ? 'selected' : '' : ''}>Rear Facing</option>
                                   <option value="Forward Facing" ${i == 1 ? data.firstbaby == 'Forward Facing' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Forward Facing' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Forward Facing' ? 'selected' : '' : ''}>Forward Facing</option>
                                   <option value="Booster" ${i == 1 ? data.firstbaby == 'Booster' ? 'selected' : '' : i == 2 ? data.secondbaby == 'Booster' ? 'selected' : '' : i == 3 ? data.thirdbaby == 'Booster' ? 'selected' : '' : ''}>Booster</option>
                               </select>
                               <p class="text-danger invalid-baby-seat-${i}"></p>
                           </div>`
                }

                if (data.child_seat < 1) {
                    $('#child_seat_container').empty()
                } else {
                    $('#child_seat_container').html(child_seat_dropdown)
                }

                //   $('#passenger_count').trigger('change')
                //     $('#luggage_count').htrigger('change')
                //     $('#hand_luggage_count').trigger('change')
                //     $('#child_seat_count').trigger('change')

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

        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
}

function ShowClientInfo(data) {
    $('#client_info').empty()

    $('#client_info').html(
        `<div class="col-sm-4">
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
            </div>`
    )
}

let journeytype = null;
function journeyDetails(data) {
    var inputDate = new Date(data.booking_date);
    var formattedDate = `${inputDate.getDate()}-${inputDate.getMonth() + 1}-${inputDate.getFullYear()}`;
    journeytype = data.way;
    console.log('y',journeytype);
    if(journeytype == 'roundtrip'){
        $('#return_pickup_date_show').removeClass('d-none').show();
        $('#return_pickup_date').val(data.tariff_return_date);
    }
    
    var sel = `<div class="card-header">
              <h4 class="card-title">Journey Details</h4>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-sm-3">
                      <label>Job No.</label>
                      <input class="form-control" type="text" value="${data.job_no}" readonly>
                  </div>
                  <div class="col-sm-3">
                      <label>Journey Type <span class="required">*</span></label>
                      <input class="form-control" type="text" id="journey_type" name="journey_type" value="${data.way}" readonly>
                      <p class="text-danger invalid-journey-type"></p>
                  </div>
                  <div class="col-sm-3">
                      <label>Booking Date <span class="required">*</span></label>
                      <div class="input-group">
                          <input class="form-control" type="text" name="booking_date" value="${formattedDate}" readonly>
                          <button type="button" class="btn btn-outline-secondary"><i class="fa fa-calendar"></i></button>
                          <p class="text-danger invalid-booking-date"></p>
                      </div>
                  </div>
              </div>
          </div>`;
    $('#journey_container').html(sel);
}

function editBooking(id) {

    const url = 'editbooking';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    formDataObject['book_id'] = id;
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
        //   console.log('janaaaa',response['booking_details']['additional_cost_date']);
        if (response['status'] == 200) {
            if (response['isEditable'] == true) {
                
                let edit_off_date = parseInt(response['booking_details']['additional_cost_date']);
                let edit_off_time = parseInt(response['booking_details']['additional_cost_time']);
                let tariff_way = response['booking_details']['way'];
                
                let total_off_date_time = edit_off_date + edit_off_time;
                let total_off_date_time_total = edit_off_date + edit_off_time + response['booking_details'].total;

                if(total_off_date_time){
                    $('.edit_offershowhide').show()
                    
                }else{
                    $('.edit_offershowhide').hide()

                }
                
                // Tariff
                if (tariff_way == 'roundtrip' || tariff_way == 'tariff_oneway') {
                    $('#edit_overall_total').val(response['booking_details'].total);
                } else {
                    $('#edit_overall_total').val(total_off_date_time_total);
                }
                
                
                $('.edit_special_off_total').text(total_off_date_time)
                $('#edit_offer_time_price').val(edit_off_time)
                $('#edit_offer_date_price').val(edit_off_date)


                ShowClientInfo(response['booking_details'])
                journeyDetails(response['booking_details'])
                tariff_type = response['booking_details']['way'];
                veh_types('', '', response['booking_details']['car_type'], 'car_type')
                CarCapacityMaker(response['booking_details']['car_type'], response['booking_details'])
                $('#one_way_pick_up').empty().append(
                    `<option value="${response.booking_details.from}">${response.booking_details.from}</option>`
                );
                // $('#one_way_pick_up').trigger('change');


                // Populate drop-off address
                $('#one_way_drop_off').empty().append(
                    `<option value="${response.booking_details.to}">${response.booking_details.to}</option>`
                );
                let from_areaaa = $('#one_way_pick_up').val()
                let to_areaaa = $('#one_way_drop_off').val()
                if ((from_areaaa.includes('Airport') || from_areaaa.includes('terminal')) && !from_areaaa
                    .includes('bus')) {
                    $('#is_airport_or_ship_one_way').val(1)
                    $('.one_way_arrival_flight_ship_details').show();
                } else {
                    $('#is_airport_or_ship_one_way').val(0)
                    $('.one_way_arrival_flight_ship_details').hide();
                }


                $('#one_way_pickup_address').val(response.booking_details.pickup_address)
                $('#one_way_dropoff_address').val(response.booking_details.dest_address)
                $('#one_way_flight_pickup_time').val(response.booking_details.after_landing_time)
                $('#one_way_payment_method').val(response.booking_details.type);
                // one_way_flight_pickup_time
                //  $('#one_way_pick_up').html(`<option value="${response['booking_details'].pickup_address}">${response['booking_details'].pickup_address}</option>`)
                //  $('#one_way_drop_off').html(`<option value="${response['booking_details'].dest_address}">${response['booking_details'].dest_address}</option>`)
                $('#one_way_ref_no').val(response['booking_details'].reference_no)
                if (response['pick_up_points'].length > 0) {
                    $('#pickup_points').click()
                    let new_location_field = ``;
                    for (i = 0; i < response['pick_up_points'].length; i++) {
                        new_location_field += `<div class="col-sm-4 mb-2 d-flex justify-content-between location_row">
                                            <input type="text" name="pickup_location[]" id="pickup_location" class="form-control from_location mr-1" value="${response['pick_up_points'][i].location_name}">
                                            <button type="button" class="btn btn-danger remove_booking" title="Remove Location">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>`;
                    }
                    $('#points_values').html(new_location_field);
                }
                var pickupDate = new Date(response['booking_details'].pickup_date);
                var pickupYear  = pickupDate.getFullYear();
                var pickupMonth = String(pickupDate.getMonth() + 1).padStart(2, '0');
                var pickupDay   = String(pickupDate.getDate()).padStart(2, '0');
                var pickupFormat = `${pickupYear}-${pickupMonth}-${pickupDay}`;
                
                // Return date
                var returnDate = new Date(response['booking_details'].tariff_return_date);
                var returnYear  = returnDate.getFullYear();
                var returnMonth = String(returnDate.getMonth() + 1).padStart(2, '0');
                var returnDay   = String(returnDate.getDate()).padStart(2, '0');
                var returnFormat = `${returnYear}-${returnMonth}-${returnDay}`;
                
                // console.log('Pickup Date:', pickupFormat);
                // console.log('Return Date:', returnFormat);
                
                // Set values
                $('#one_way_pickup_date').val(pickupFormat);
                $('#return_pickup_date').val(returnFormat);
                $('#one_way_pickup_time').val(response['booking_details'].pickup_time)
                $('#one_way_pickup_address').val(response['booking_details'].pickup_address)
                $('#one_way_dropoff_address').val(response['booking_details'].dest_address)
                $('#one_way_flight_number').val(response['booking_details'].pickup_flight_num)
                $('#one_way_flight_pickup_time').val(response['booking_details'].after_landing_time)
                $('#one_way_payment_method').val(response['booking_details'].type)
                //  console.log(response['booking_details'].type);
                $('#one_way_payment_status').val(response['booking_details'].payment_status)
                let orderStatus = response['booking_details'].order_status.toLowerCase();
                let options = '';

                switch (orderStatus) {
                    case 'pending':
                    case 'confirmed':
                    case 'canceled':
                        options = `
                                <option value="Pending" ${orderStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="Confirmed" ${orderStatus === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                                <option value="Canceled" ${orderStatus === 'canceled' ? 'selected' : ''}>Cancelled</option>
                            `;
                        break;

                    case 'assigned':
                        options = `
                                <option value="Assigned" selected>Assigned</option>
                                <option value="Dispatched">Dispatched</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Canceled">Cancelled</option>
                            `;
                        break;

                    case 'dispatched':
                        options = `
                                <option value="Dispatched" selected>Dispatched</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Cancelled</option>
                            `;
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
                                <option value="Pending">Pending</option>
                            `;
                }

                let orderstatus = `${options}`;

                $('#one_way_order_status').html(orderstatus)
                $('#one_way_total_cost').val(parseInt(response['booking_details'].actual_amount));
                $('#net_total').val(response['booking_details'].total)
                $('#one_way_extra_cost').val(response['booking_details'].car_park_amount)
                $('#one_way_distance').val(response['booking_details'].distance)
                console.log('kjhgf',response['booking_details'].duration);
                //Tarff
                if (tariff_way == 'roundtrip') {
                    $('#one_way_travel_time').val(response['booking_details'].duration);
                }else if(tariff_way == 'tariff_oneway'){
                    $('#one_way_travel_time').val(response['booking_details'].duration);
                }else{
                    $('#one_way_travel_time').val(response['booking_details'].duration)
                }
                
                $('#one_way_driver_amount').val(response['booking_details'].driver_amount)
                // console.log(response['booking_details'].distance);
                // console.log(response['booking_details'].duration);
                $('#one_way_message').html(response['booking_details'].message)
                $('#one_way_remarks').html(response['booking_details'].remarks)
                $('#one_way_payment_message').html(response['booking_details'].payment_message)
                $('#one_way_from_lati').val(response['booking_details'].from_lat)
                $('#one_way_from_longi').val(response['booking_details'].from_long)
                $('#one_way_to_lati').val(response['booking_details'].to_lat)
                $('#one_way_to_longi').val(response['booking_details'].to_long)
                $('#one_way_actual_amount').val(response['booking_details'].actual_amount)
                $('#actual_amount_show').val(response['booking_details'].actual_amount)
                $('#one_way_special_day_percentage').val(response['booking_details'].special_day_percentage)
                $('#client_id').val(response['booking_details'].user_id)
                //  $('#is_airport_or_ship_one_way').val('1')
            }
        }
        if (response['status'] == 400) {
            warningClick('Error', response['error'], "danger")
        }
        if (response['status'] == 500) {
            warningClick('Error', response['error'], "danger")
        }
        if (response['status'] == 401) {
            unauth()
        }
    });

}


$('#child_seat_count').change(function() {
    ChildSeatMaker($('#child_seat_count').val())
})

function ChildSeatMaker(seat_count) {
    let child_seat_dropdown = ''

    for (let i = 1; i <= seat_count; i++) {
        child_seat_dropdown += `<div class="col-sm-3">
                        <label for="baby_seat_${i}">Child Seat ${i}</label>
                        <select class="form-control" id="baby_seat_${i}" name="baby_seat_${i}">
                            <option value="Rear Facing" selected>Rear Facing</option>
                            <option value="Forward Facing" >Forward Facing</option>
                            <option value="Booster" >Booster</option>
                        </select>
                        <p class="text-danger invalid-baby-seat-${i}"></p>
                    </div>`
    }

    if (seat_count < 1) {
        $('#child_seat_container').empty()
    } else {
        $('#child_seat_container').html(child_seat_dropdown)
    }
}

//Tariff Assign Values
function AssignValueFore_tariff(data, amount, journey_type) {
    console.log('Normal Total:', amount.normal_total); 
    
    let tariff_nrml_total    = amount.normal_total;
    let tariff_out_total     = amount.outstation_total; 
    let tariff_distance      = data.distance_unit;
    let tariff_out_above_km  = data.outstation_above_km.km;   
    let tariff_out_above_hr  = data.outstation_above_km.hr; 
    let tariff_out_above_day = data.outstation_above_km.day; 
    let tariff_type          = data.tariff_type;
    
    let tariff_final_total = 
        ((tariff_type === "roundtrip") || (tariff_distance > tariff_out_above_km))
            ? tariff_out_total
            : tariff_nrml_total;
            
    let tariff_final_time = 
        ((tariff_type === "roundtrip") || (tariff_distance > tariff_out_above_km))
            ? (tariff_out_above_day + ' Day')
            : (tariff_out_above_hr + ' Hour');
    
    // console.log('Distance Total:', tariff_distance); 
    // console.log('Above KM:', tariff_out_above_km); 
    // console.log('Above HR:', tariff_out_above_hr); 
    // console.log('Above Day:', tariff_out_above_day); 
    // console.log('Final Total:', tariff_final_total); 
    
    $('#one_way_total_cost').val(tariff_final_total); 
    $('#edit_overall_total').val(tariff_final_total); 
    $('#one_way_travel_time').val(tariff_final_time);
    if(tariff_type == "roundtrip"){
        $('#one_way_distance').val(tariff_distance * 2);
    }else{
        $('#one_way_distance').val(tariff_distance);
    }
    $('#actual_amount_show').val(tariff_final_total);
    $('#one_way_actual_amount').val(tariff_final_total);
    $('#net_total').val(tariff_final_total);
}


function AssignValues(data, journey_type, currencyval) {

    // $('.edit_special_off_total').text('( ' + currencySymbol + ' )')
    let onchange_edit_value = parseInt(data.one_day_price) + parseInt(data.o_time_price_only);
    let oneway_all_total = data.total_fare + data.o_time_price_only + data.one_day_price;

    if(onchange_edit_value){
        $('.edit_offershowhide').show();
        // console.log('true', onchange_edit_value);
    }else{
        $('.edit_offershowhide').hide();
    }
    $('#net_total_show').val(oneway_all_total);
    $('#net_total').val(oneway_all_total);
    $('#edit_offer_time_price').val(data.o_time_price_only);
    $('#edit_offer_date_price').val(data.one_day_price);
    $('#edit_overall_total').val(data.o_total_fare);
    $('.edit_special_off_total').text(onchange_edit_value);
    
    
    // console.log('false', data.total_fare);
    $('#one_way_total_cost').val(parseInt(data.total_fare));
    $('#actual_amount_show').val(parseInt(data.total_fare));
    
    $('#one_way_travel_time').val(data.duration)
    $('#one_way_distance').val(data.distance_unit)
    // $('#one_way_actual_amount').val(data.total_fare)
    //these values are stored in hidden input fields
    $('#one_way_from_lati').val(data.from_lati)
    $('#one_way_from_longi').val(data.from_longi)
    $('#one_way_to_lati').val(data.to_lati)
    $('#one_way_to_longi').val(data.to_longi)

    //calculate total cost
    CalculateAmount('one_way')
}

function CalculateAmount(journey_type) {
    let total_cost = parseFloat(0)
    let isEditable = 'true'

    let one_way_extra_percentage = parseFloat($('#one_way_special_day_percentage').val())
    let one_way_actual_amount = parseFloat($('#one_way_actual_amount').val())
    let one_way_caculated_amount = one_way_extra_percentage > 0 ? one_way_actual_amount + (one_way_actual_amount * (
        one_way_extra_percentage / 100)) : one_way_actual_amount

    let return_extra_percentage = parseFloat($('#return_special_day_percentage').val())
    let return_actual_amount = parseFloat($('#return_actual_amount').val())
    let return_caculated_amount = return_extra_percentage > 0 ? return_actual_amount + (return_actual_amount * (
        return_extra_percentage / 100)) : return_actual_amount

    if (journey_type === 'one_way') {
        // $('#one_way_total_cost').val(Math.ceil(isEditable && total_cost !== 0 ? total_cost : one_way_caculated_amount)
        //     .toFixed(2))
        // $('#net_total').val(Math.ceil(isEditable && total_cost !== 0 ? total_cost : one_way_caculated_amount).toFixed(
        //     2))
    } else if (journey_type === 'return') {
        $('#return_total_cost').val(Math.ceil(return_caculated_amount).toFixed(2))
        // $('#net_total').val(Math.ceil(return_caculated_amount).toFixed(2))
    }
}

function ArrivalDetailsContainer(place_type, journey_type) {
    const extra_details_places = ['Airports', 'Seaports']

    let place = place_type ? place_type.trim() : '';
    let journey = journey_type ? journey_type.trim() : '';

    if (place === '' && journey === '') {
        $('.one_way_arrival_flight_ship_details').hide()
        $('.return_flight_ship_details').hide()
    }

    if (journey === 'one_way') {
        // $('.one_way_arrival_flight_ship_details').show()
        $('.invalid_one_way_flight_number').text('')
        $('.invalid_one_way_flight_from').text('')

        // $('#is_airport_or_ship_one_way').val(1)

        $("label[for|='one_way_flight_number']").html('Flight Number <span class="required">*</span>')
        $("label[for|='one_way_flight_from']").html('Flight From <span class="required">*</span>')

        $('#one_way_flight_number').attr('placeholder', 'Flight Number');
        $('#one_way_flight_from').attr('placeholder', 'Flight From');

        $('#pickup_time_container').show()
    }

    if (journey === 'return') {
        $('.return_flight_ship_details').show()
        $('.invalid_return_flight_number').text('')
        $('.invalid_return_flight_from').text('')
        $('#is_airport_or_ship_return').val(1)
        $('#return_transport_name').text('Flight Information')

        $("label[for|='return_flight_number']").html('Flight Number <span class="required">*</span>')
        $("label[for|='return_flight_from']").html('Flight From <span class="required">*</span>')

        $('#return_flight_number').attr('placeholder', 'Flight Number');
        $('#return_flight_from').attr('placeholder', 'Flight From');
    }
}

function DepartureDetailsContainer(place_type, journey_type) {
    let place = place_type ? place_type.trim() : '';
    let journey = journey_type ? journey_type.trim() : '';

    if (place === '' || journey === '') {
        $('.one_way_departure_flight_ship_details').hide()
    }
    $('.one_way_departure_flight_ship_details').hide()
}

function extraOneway() {

    let extra = parseFloat($('#one_way_extra_cost').val()) || 0;
    let actual = parseFloat($('#one_way_total_cost').val()) || 0;

    // $('#net_total').val(actual + extra);
}
</script>