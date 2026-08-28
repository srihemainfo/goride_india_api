<script>
    $(function () {
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
                        if(data.length == 0){
                             return {
                             results: []
                         };
                        }
                         if(data.length > 0){
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
            
            $('#car_type, #one_way_pick_up, #one_way_drop_off').change(function() {
            let from_area = $('#one_way_pick_up').val()
            let to_area = $('#one_way_drop_off').val()
            let car_type = $('#car_type').val()
            let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

            if (car_type && from_area && to_area) {
                console.log('succes')
                //show pickpoint checkbox
                $("#pickup_points_container").show()
                var formDataObject  = {};
                formDataObject['token'] = getCookie('d_token');
                formDataObject['from'] = from_area;
                formDataObject['to'] = to_area;

                $.ajax({
                    data: formDataObject,
                    url: "{{env('API_URL')}}distance",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        AssignValues(response, 'one_way')
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
                        if(data.length == 0){
                             return {
                             results: []
                         };
                        }
                         if(data.length > 0){
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

                if(location && !location_array.includes(location)){
                    $('.pick_up_point_select').val('').trigger('change');
                    $('#points_values').append(new_location_field);
                }else{
                    console.log('No Values.');
                }
            })
            
            $(document).on('click', '.remove_booking', function() {
                $(this).closest('.location_row').remove()
            })


        //Setting date for pickup date
        $('#one_way_pickup_date').datepicker("setDate", )

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
    
    $('#update_book').on('click', function(){
        
           var key1 = window.location.href;
           var segments1 = key1.split('/');
           var lastSegment1 = segments1.pop();
            
            const url = 'updatebooking';
            var formdata = $('#editbookingForm').serialize();
            var pairs = formdata.split('&');
            var formDataObject  = {};
            
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
            
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
    //   console.log(formDataObject)
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             setCookie('swal',response['message'],'1')
             location.reload()
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
    })
    
    $('#car_type').change(function() {
            CarCapacityMaker($('#car_type').val(),'')
    })
    
    function CarCapacityMaker(car_type,data) {
        $('#passenger_count').empty()
        $('#luggage_count').empty()
        $('#hand_luggage_count').empty()
        var formDataObject  = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
        formDataObject['fleet_id'] = car_type;

        $.ajax({
            data: formDataObject,
            url: "{{env('API_URL')}}editvehichle",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                if(response['status'] == 200){
              //dd means dropdown
                let passenger_dd = ''
                let luggage_dd = ''
                let hand_luggage_dd = ''
                let child_dd = ''
                
                    for (let i = 1; i <= response['data'].passenger; i++) {
                        passenger_dd += `<option value="${i}" ${data.passengers != null ? data.passengers == i ? 'selected' : '' : ''}>${i}</option>`
                    }
                    for (let i = 0; i <= response['data'].luggage; i++) {
                        luggage_dd += `<option value="${i}" ${data.baggages != null ? data.baggages == i ? 'selected' : '' : ''}>${i}</option>`
                    }
                    for (let i = 0; i <= response['data'].hand_luggage; i++) {
                        hand_luggage_dd += `<option value="${i}" ${data.hand_luggages != null ? data.hand_luggages == i ? 'selected' : '' : ''}>${i}</option>`
                    }
                    for(let i = 0; i <= response['data'].child; i++){
                        child_dd += `<option value="${i}" ${data.child_seat != null ? data.child_seat == i ? 'selected' : '' : ''}>${i}</option>`
                    }

                $('#passenger_count').html(passenger_dd)
                $('#luggage_count').html(luggage_dd)
                $('#hand_luggage_count').html(hand_luggage_dd)
                $('#child_seat_count').html(child_dd)
                    
                    let child_seat_dropdown = ''

                   for(let i = 1; i <= data.child_seat; i++){
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

                  if(data.child_seat < 1){
                      $('#child_seat_container').empty()
                  }else{
                      $('#child_seat_container').html(child_seat_dropdown)
                  }
               
             }
             if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
              }
              if(response['status'] == 500){
                 warningClick('Error',response['error'],"danger")
              }
              if(response['status'] == 401){
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
    
        function journeyDetails(data) {
          var inputDate = new Date(data.booking_date);
          var formattedDate = `${inputDate.getDate()}-${inputDate.getMonth() + 1}-${inputDate.getFullYear()}`;
      
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
    
    function editBooking(id){
        
        const url = 'editbooking';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['book_id'] = id;
          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
          console.log(response);
         if(response['status'] == 200){
             if(response['isEditable'] == true){
                 ShowClientInfo(response['booking_details'])
                 journeyDetails(response['booking_details'])
                 veh_types('','',response['booking_details']['car_type'],'car_type')
                 CarCapacityMaker(response['booking_details']['car_type'],response['booking_details'])
                 $('#one_way_pick_up').empty().append(
                    `<option value="${response.booking_details.from}">${response.booking_details.from}</option>`
                );

                // Populate drop-off address
                $('#one_way_drop_off').empty().append(
                    `<option value="${response.booking_details.to}">${response.booking_details.to}</option>`
                );
                $('#one_way_pickup_address').val(response.booking_details.pickup_address)
                $('#one_way_dropoff_address').val(response.booking_details.dest_address)
                $('#one_way_flight_pickup_time').val(response.booking_details.after_landing_time)
                $('#one_way_payment_method').val(response.booking_details.type);
                // one_way_flight_pickup_time
                //  $('#one_way_pick_up').html(`<option value="${response['booking_details'].pickup_address}">${response['booking_details'].pickup_address}</option>`)
                //  $('#one_way_drop_off').html(`<option value="${response['booking_details'].dest_address}">${response['booking_details'].dest_address}</option>`)
                 $('#one_way_ref_no').val(response['booking_details'].reference_no)
                 if(response['pick_up_points'].length > 0){
                     $('#pickup_points').click()
                     let new_location_field = ``;
                     for(i=0; i < response['pick_up_points'].length; i++){
                      new_location_field += `<div class="col-sm-4 mb-2 d-flex justify-content-between location_row">
                                            <input type="text" name="pickup_location[]" id="pickup_location" class="form-control from_location mr-1" value="${response['pick_up_points'][i].location_name}">
                                            <button type="button" class="btn btn-danger remove_booking" title="Remove Location">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>`;
                     }
                    $('#points_values').html(new_location_field);
                 }
                 var inputDate = new Date(response['booking_details'].pickup_date);
                 var formatDate = `${inputDate.getDate()}-${inputDate.getMonth() + 1}-${inputDate.getFullYear()}`;
                 $('#one_way_pickup_date').val(formatDate)
                 $('#one_way_pickup_time').val(response['booking_details'].pickup_time)
                 $('#one_way_pickup_address').val(response['booking_details'].pickup_address)
                 $('#one_way_dropoff_address').val(response['booking_details'].dest_address)
                 $('#one_way_flight_number').val(response['booking_details'].pickup_flight_num)
                 $('#one_way_flight_pickup_time').val(response['booking_details'].after_landing_time)
                 $('#one_way_payment_method').val(response['booking_details'].type)
                 console.log(response['booking_details'].type);
                 $('#one_way_payment_status').val(response['booking_details'].payment_status)
                 let orderstatus = `${response['booking_details'].order_status.toLowerCase() === 'pending' ? `<option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Canceled">Cancelled</option>` : response['booking_details'].order_status.toLowerCase() === 'confirmed' ? `<option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Canceled">Cancelled</option>` : response['booking_details'].order_status.toLowerCase() === 'assigned' ? `<option value="Assigned">Assigned</option>
                            <option value="Dispatched">Dispatched</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Canceled">Cancelled</option>` : response['booking_details'].order_status.toLowerCase() === 'dispatched' ? `<option value="Dispatched">Dispatched</option>
                            <option value="Completed">Completed</option>
                            <option value="Canceled">Cancelled</option>` : response['booking_details'].order_status.toLowerCase() === 'completed' ? `<option value="Completed">Completed</option>` : 
                            response['booking_details'].order_status.toLowerCase() === 'settled' ? `<option value="settled">Settled</option>` : response['booking_details'].order_status.toLowerCase() === 'canceled' ? `<option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Canceled">Cancelled</option>` : `<option value="Confirmed">Confirmed</option>
                        <option value="Pending">Pending</option>`
                 }`;
                 $('#one_way_order_status').html(orderstatus)
                 $('#one_way_total_cost').val(response['booking_details'].total)
                 $('#one_way_extra_cost').val(response['booking_details'].extracharges)
                 $('#one_way_distance').val(response['booking_details'].distance)
                 $('#one_way_travel_time').val(response['booking_details'].duration)
                 $('#one_way_driver_amount').val(response['booking_details'].driver_amount)
                 $('#one_way_message').html(response['booking_details'].message)
                 $('#one_way_remarks').html(response['booking_details'].remarks)
                 $('#one_way_payment_message').html(response['booking_details'].payment_message)
                 $('#one_way_from_lati').val(response['booking_details'].from_lat)
                 $('#one_way_from_longi').val(response['booking_details'].from_long)
                 $('#one_way_to_lati').val(response['booking_details'].to_lat)
                 $('#one_way_to_longi').val(response['booking_details'].to_long)
                 $('#one_way_actual_amount').val(response['booking_details'].net_total)
                 $('#one_way_special_day_percentage').val(response['booking_details'].special_day_percentage)
                 $('#client_id').val(response['booking_details'].user_id)
                 $('#is_airport_or_ship_one_way').val('1')
             }
         }
         if(response['status'] == 400){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
        
    }
    
    $('#child_seat_count').change(function() {
            ChildSeatMaker($('#child_seat_count').val())
        })
        
        function ChildSeatMaker(seat_count){
        let child_seat_dropdown = ''

            for(let i = 1; i <= seat_count; i++){
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

        if(seat_count < 1){
            $('#child_seat_container').empty()
        }else{
            $('#child_seat_container').html(child_seat_dropdown)
        }
    }
    
    function AssignValues(data, journey_type) {
            $('#one_way_travel_time').val(data.duration)
            $('#one_way_distance').val(data.miles)
            $('#one_way_actual_amount').val(data.total_fare)

            //these values are stored in hidden input fields
            $('#one_way_from_lati').val(data.from_lati)
            $('#one_way_from_longi').val(data.from_longi)
            $('#one_way_to_lati').val(data.to_lati)
            $('#one_way_to_longi').val(data.to_longi)

            //calculate total cost
            CalculateAmount('one_way')
    }
    
</script>
