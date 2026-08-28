<script>

let isValid = true;
let currencyval = null;
$('.oneway_offershowhide').hide();
$('.return_offershowhide').hide();

    $(function() {
        phoneCode()
        veh_types('','','','car_type','Active')
        
        //Allowed places for autofill address
        const auto_fill_places = ['Airports', 'Seaports', 'Hotels', 'Southampton Hotels',
            'Heathrow Airport Hotels', 'Train stations'
        ]

        $('#one_way_pickup_date, #return_pickup_date, #one_way_flight_date, #return_flight_date').datepicker({
            format: "dd-mm-yyyy",
            weekStart: 1
        }).datepicker("setDate", "0")

        //Hide return container on pageload
        ReturnContainerVisibility(false)

        //Hide flight details container on pageload
        ArrivalDetailsContainer('', '')
        DepartureDetailsContainer('', '')

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
                token: getCookie('d_token'), // Get token from cookies
                device_id: 0
            }),
            success: function(response) {
                if(response['status'] == 400){
                    errornotify(response)
                    // window.location.href = '/dashboard';
                }
                if(response['status'] == 500){
                    Swal.fire({
                        title: "Warning!",
                        text: response['error'],
                        icon: "warning",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "OK"
                    });

                    // window.location.href = '/dashboard';
                }
                if(response['status'] == 401){
                    // unauth()
                    // window.location.href = '/dashboard';
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });


        //Select2 AJAX search for clients
        // $('#search_clients').select2({
        //       ajax: {
        //           url: "{{env('API_URL')}}customerlist",
        //           type: "post",
        //           dataType: 'json',
        //           delay: 400,
        //           data: function(params) {
        //               // Merge your formDataObject with the Select2 request parameters
        //               return {
        //                   search: params.term, // search term
        //                   token: formDataObject.token,
        //                   device_id: formDataObject.device_id
        //               };
        //           },
        //           processResults: function(response) {
        //                  const data = response.data;
        //                 //  // console.log(data.length);
        //                 if(data.length == 0){
        //                      return {
        //                      results: []
        //                  };
        //                 }
        //                  if(data.length > 0){
        //                  const formattedData = data.map(item => ({
        //                      id: item.id,
        //                      text: item.f_name
        //                  }));
        //                  return {
        //                      results: formattedData
        //                  };
        //                  }
        //           },
        //           cache: true
        //       }
        //   });

        
        
        

        
        $('#search_clients').select2({
            ajax: {
                url: "{{env('API_URL')}}get-clients",
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
                    // console.log(response); // Log the response to see its structure
                    const data = response;
                
                    if (data.length === 0) {
                        return {
                            results: []
                        };
                    }
                
                    const formattedData = data.map(item => ({
                        id: item.id,
                        text: item.text
                    }));
                
                    return {
                        results: formattedData
                    };
            },

                cache: true
            }
        });


        //Load data for selected client
        $('#search_clients').change(function() {
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['customer_id'] = $('#search_clients').val();

            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}editcustomer",
                data: formDataObject,
                success: function(response) {
                    // // console.log(response[0].email)
                    ShowClientInfo(response.data)
                },
                error: function(data) {
                    // console.log('Error:', data);
                }
            });
        })

        //Client Modal Form Trigger
        $('#addCustomer').click(function() {
            ClientModal_ResetErrors()
            $('#customer_id').val('');
            $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");
            $('#customerForm').trigger("reset");
            $('#form-modal').modal('show');
        });

        // Ajax for Save New Client
        	$('#saveBtn').click(function (e) {
                    e.preventDefault();
                    
                    const url = 'createcustomer';
               var formdata = $('#customerForm').serialize();
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

                    $.ajax({
                    data: formDataObject,
                    url: "{{env('API_URL')}}"+url,
                    type: "POST",
                    dataType: 'json',
                    success: function (response) {
                          if(response['status'] == 400){
                             errornotify(response)
                          }
                          if(response['status'] == 500){
                             warningClick('Error',response['error'],"danger")
                          }
                          if(response['status'] == 401){
                             unauth()
                          }

                        if(response.status == 200){
                            $('#customerForm').trigger("reset");
                            $('#form-modal').modal('hide');
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Added',
                                    text: 'New Customer added successfully',
                                    showConfirmButton: false,
                                    timer: 2000,
                                }).then(function(){
                                     location.reload()
                                })						
                        }						
                    },
                    error: function (data) {
                        // console.log('Error:', data);
                    }
                });
            });

        //Calculate distance, duration, coordinates and fare for one way trip
        $('#car_type, #one_way_pick_up, #one_way_drop_off, #date, #date1, #one_way_pickup_time , #return_pickup_time').change(function() {
            let from_area = $('#one_way_pick_up').val();
            let to_area = $('#one_way_drop_off').val();
            $('.one_way_arrival_flight_ship_details').hide();
            if (from_area && (from_area.toLowerCase().includes('airport') || from_area.toLowerCase().includes('terminal')) && !from_area.toLowerCase().includes('bus')) {
                $('#is_airport_or_ship_one_way').val(1);
                $('.one_way_arrival_flight_ship_details').show();
            } else {
                $('#is_airport_or_ship_one_way').val(0);
                $('.one_way_arrival_flight_ship_details').hide();
            }

            let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false
            
            let car_type = $('#car_type').val();
            let date = $('#date').val();
            let date1 = $('#date1').val();
            let one_way_pickup_time = $('#one_way_pickup_time').val();
            let return_pickup_time = $('#return_pickup_time').val();
            
            if (car_type && from_area && to_area) {
                console.log('succes')
                //show pickpoint checkbox
                $("#pickup_points_container").show()
                var formDataObject  = {};
                formDataObject['token'] = getCookie('d_token');
                formDataObject['from'] = from_area;
                formDataObject['to'] = to_area;
                formDataObject['car_type'] = car_type;
                formDataObject['return_pickup_time'] = return_pickup_time;
                formDataObject['one_way_pickup_time'] = one_way_pickup_time;
                formDataObject['date1'] = date1;
                formDataObject['date'] = date;
                
                $.ajax({
                    data: formDataObject,
                    url: "{{env('API_URL')}}distance",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        AssignValues(response, 'one_way', currencyval)
                        AssignValues(response, 'return', currencyval)
                        // console.log('Jana total',response.total_oneway_date_time);
                        // if (journey_type) {
                        //     ReturnTripInitialAutoCalculation(journey_type)
                        // }
                    },
                    error: function(data) {
                        // console.log('Error:', data);
                    }
                });
            } else {
                // console.log('One way request not fulfill.')
            }
        });

        //Calculate distance, duration, coordinates and fare for return trip
        $('#car_type, #return_pick_up, #return_drop_off').change(function() {
            let car_type = $('#car_type').val()
            let from_area = $('#return_pick_up').val()
            let to_area = $('#return_drop_off').val()
            let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

            if (car_type && from_area && to_area) {
                $.ajax({
                    data: {
                        car_type: car_type,
                        from_area: from_area,
                        to_area: to_area
                    },
                    url: "{{ route('GetQuote') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        AssignValues(response, 'return')
                    },
                    error: function(data) {
                        // console.log('Error:', data);
                    }
                });
            } else {
                // console.log('Return request not fulfill.')
            }
        });

        var dial_code_store;

    function phoneCode() {
        const url = 'phoneCode';
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
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
            console.log(response);
            if (response['status'] == 200) {
                $('#country_code').text(response.data);
                dial_code_store = response.data;
                // $('#country_code_whatsapp').val(response.data);
                $('#hidden_phoneCode').val(response.data);
                // $('#edit_country_code').val(response.data);
                // $('#edit_country_code_whatsapp').val(response.data);
                // $('#edit_cus_form-modal').modal('show')
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


        // Ajax for Save and Update
        $('#book_now').click(function(e) {
            e.preventDefault();
            var button = $(this);
            var spinner = button.find('.spinner-border');
            var buttonText = button.find('.button-text');
            const url = 'createbooking';
            var formdata = $('#bookingForm').serialize();
            var pairs = formdata.split('&');
            var formDataObject  = {};

            var one_date = $('#date').val();
            var return_date = $('#date1').val();
            var one_time = $('#one_way_pickup_time').val();
            var return_time = $('#return_pickup_time').val();
            // console.log('one time', one_time);


            var return_date12 = $('[name="journey_type"]:checked').val();

            if (return_date12 === 'Return') { 

                if (one_date == return_date) {
                    var oneTimeParts = one_time.split(':');
                    var returnTimeParts = return_time.split(':');

                    var oneTotalMinutes = parseInt(oneTimeParts[0]) * 60 + parseInt(oneTimeParts[1]);
                    var returnTotalMinutes = parseInt(returnTimeParts[0]) * 60 + parseInt(returnTimeParts[1]);

                    var diffMinutes = returnTotalMinutes - oneTotalMinutes;

                    if (diffMinutes < 60 && diffMinutes > 0) {
                        warningClick('Warning', 'Oneway and Return Time should not be the same', 'warning');
                        $('#return_pickup_time').focus();
                        isValid = false;
                    }else{
                        isValid = true;
                    }
                } else {
                    isValid = true;
                }

            } else {
                isValid = true;
            }

            // console.log('Jana', return_date12);
            // console.log('Janareturn', valuesaa12);

            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            delete formDataObject['pickup_location[]'];
            var inputValues = [];
            $("input[id='pickup_location']").each(function() {
            inputValues.push($(this).val());
            });
            formDataObject['pickup_location'] = inputValues;

            // console.log('Janareturn', return_time);
            
            spinner.show();
            buttonText.hide();
            $('#book_now').attr('disabled', true);

            if(isValid)
            {
                $.ajax({
                data: formDataObject,
                url: "{{env('API_URL')}}"+url,
                type: 'post',
                dataType: 'json',
                success: function(response) {
                    $('#book_now').attr('disabled', false)
                    if(response['status'] == 200){
                         setCookie('swal',response['message'],'1')
                         window.location.href="/booking/list/All";
                         }
                     if(response['status'] == 400){
                        errornotify(response)
                     }
                     if(response['status'] == 500){
                        warningClick('Alert',response['error'],"danger")
                     }
                     if(response['status'] == 401){
                        unauth()
                     }
                    spinner.hide();
                    buttonText.show();
                },
                error: function(data) {
                    $('#book_now').attr('disabled', false);
                    // console.log('Error:', data);
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

            
        })

        //Load car type details on pageload
        // CarCapacityMaker($('#car_type').val())

        //Load car type details on change
        $('#child_seat_count').change(function() {
            ChildSeatMaker($('#child_seat_count').val())
        })

        //Load car type details on change
        $('#car_type').change(function() {
            CarCapacityMaker($('#car_type').val())
        })

        //Special date extra charges check for one way pickup date
        $('#one_way_pickup_date').change(function() {
            CheckSpecialDay($('#one_way_pickup_date').val(), 'one_way')
        })

        //Special date extra charges check for return pickup date
        $('#return_pickup_date').change(function() {
            CheckSpecialDay($('#return_pickup_date').val(), 'return')
        })

        //One way auto address fill for pickup
        $('#one_way_pick_up').change(function() {
            let place = $('#one_way_pick_up').select2('data')[0].place_type ?
                $('#one_way_pick_up').select2('data')[0].place_type : ''
                
                let place_id = $('#one_way_pick_up').select2('data')[0].place_id ?
                $('#one_way_pick_up').select2('data')[0].place_id : ''

            let area = $('#one_way_pick_up').val()

            let address = $('#one_way_pick_up').select2('data')[0].area_address ?
                $('#one_way_pick_up').select2('data')[0].area_address : area

            let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false

                // $('#one_way_pickup_address').val(address)
                $('#from_place_id').val(place_id)

            //Flight details container visibility for one way
            ArrivalDetailsContainer(place, 'one_way')

            if (journey_type) {
                AutoSelectReturnPickup()
                AutoSelectReturnDrop()
            }
        })

        //One way auto address fill for dropoff
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

                // $('#one_way_dropoff_address').val(address)
                $('#to_place_id').val(place_id)

            //Flight details container visibility for one way
            DepartureDetailsContainer(place, 'one_way')

            if (journey_type) {
                AutoSelectReturnPickup()
                AutoSelectReturnDrop()
                //Flight details container visibility for return
                ArrivalDetailsContainer(place, 'return')
            }
        })

        $("#pickup_points").click(function() {
                if ($(this).is(":checked")) {
                    $("#pickup_point_container").show();
                } else {
                    $("#pickup_point_container").hide();
                }
            })

            $('.pick_up_point_select').select2({
                
            //   var countryWebsites =   $('#country_websites').val();
                // if(countryWebsites != ''){
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
                        if(data.length == 0){
                             return {
                             results: []
                         };
                        }
                         if(data.length > 0){
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
                    
                // }else{
                //     swalalerterror('please choose the country');
                // }
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
                    // console.log('No Values.');
                }
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
                        // console.log('Error fetching distance unit:', response.data.currency);
                        if (response.status === 200) {
                         var unit = response.data.distance_unit; 
                         $('.getcurrencycode').text('(' + response.data.currency + ')');
                         currencyval = response.data.currency;
                      $('.distance_unit').append('(In ' + unit + ')');
                        } else {
                            // console.log('Unable to retrieve distance unit');
                        }
                    },
                    error: function(data) {
                        // console.log('Error fetching distance unit:', data);
                    }
                });
            });

            $(document).on('click', '.calc_new_amount', function() {
                let car_type = $('#car_type').val()
                let from_location = $('#one_way_pick_up').val()
                let to_location = $('#one_way_drop_off').val()
                let location_array = $('input[name="pickup_location[]"]').map(function() {
                    return $(this).val();
                }).get();

                location_array.unshift(from_location)
                location_array.push(to_location)

                // console.log(from_location, to_location, location_array)

                $.ajax({
                    data: {
                        car_type: car_type,
                        pick_up_points: location_array,
                    },
                    url: "{{env('API_URL')}}recalculate",
                    type: "POST",
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 200) {
                            $('#one_way_distance').val(response.total_distance)
                            $('#one_way_total_cost').val(response.total_fare)
                            // $('#net_total').val(response.total_fare)
                            $('#one_way_travel_time').val(response.total_duration)
                            $('#one_way_actual_amount').val(response.total_fare)

                            //calculate total cost
                            CalculateAmount('one_way')

                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                text: 'Recalculation Done',
                                showConfirmButton: false,
                                timer: 2000,
                            })
                        } else {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Error',
                                text: 'unable to fetch new calculated details.',
                                showConfirmButton: false,
                                timer: 2000,
                            })
                        }
                    }
                })


            })

            $(document).on('click', '.remove_booking', function() {
                $(this).closest('.location_row').remove()
            })

            //Select2 AJAX search for locations
            $('#one_way_pick_up, #one_way_drop_off, #return_pick_up, #return_drop_off').select2({
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
                         // console.log(data.length);
                        if(data.length == 0){
                             return {
                             results: []
                         };
                        }
                         if(data.length > 0){
                             // console.log(data.length);
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
            
             //Select2 AJAX search for locations
            $('#country_websites').select2({
                ajax: {
                    url: "{{env('API_URL')}}country_websites",
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
                         // console.log(data.length);
                        if(data.length == 0){
                             return {
                             results: [{ id: 'CRM', text: 'From CRM (CR)' }]
                         };
                        }
                         if(data.length > 0){
                             // console.log(data.length);
                        //  const formattedData = data.map(item => ({
                        //      id: item.id,
                        //      text: item.text
                        //  }));
                        
                        const formattedData = [
                            { id: 'CRM', text: 'From CRM (CR)' }, 
                            ...data.map(item => ({
                                id: item.id,
                                text: item.text
                            }))
                        ];
                         return {
                             results: formattedData
                         };
                         }
                    },
                    cache: true
                }
            })

            //Special date extra charges check for on pageload
            CheckSpecialDay($('#one_way_pickup_date').val(), 'one_way')

            //Journey type selection for return container visibility
            $('[name="journey_type"]').click(function() {
                let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false
                let place = $('#one_way_drop_off_place_type').val()

                ReturnContainerVisibility(journey_type)
                AutoSelectReturnPickup()
                AutoSelectReturnDrop()
                ReturnTripInitialAutoCalculation(journey_type)
                //Flight details container visibility for return
                ArrivalDetailsContainer(place, 'return')
            })

            //Return auto address fill for pickup
            $('#return_pick_up').change(function() {
                let place = $('#return_pick_up').select2('data')[0].place_type ?
                    $('#return_pick_up').select2('data')[0].place_type : ''

                let area = $('#return_pick_up').val()

                let address = $('#return_pick_up').select2('data')[0].area_address ?
                    $('#return_pick_up').select2('data')[0].area_address : area

                    $('#return_pickup_address').val(address)

                //Flight details container visibility for return
                ArrivalDetailsContainer(place, 'return')
            })

            //Return auto address fill for dropoff
            $('#return_drop_off').change(function() {
                let place = $('#return_drop_off').select2('data')[0].place_type ?
                    $('#return_drop_off').select2('data')[0].place_type : ''

                let area = $('#return_drop_off').val()

                let address = $('#return_drop_off').select2('data')[0].area_address ?
                    $('#return_drop_off').select2('data')[0].area_address : area

                    $('#return_dropoff_address').val(address)
            })
    })

    function ShowClientInfo(data) {
        $('#client_info').empty()

        $('#client_info').html(
            `<div class="col-sm-4">
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
            </div>`
        )
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

    function ReturnContainerVisibility(visibility) {
        if (visibility) {
            $('#return_container').show()
            CheckSpecialDay($('#return_pickup_date').val(), 'return')
        } else {
            $('#return_container').hide()
        }
    }

    function CarCapacityMaker(car_type) {
        $('#passenger_count').empty()
        $('#luggage_count').empty()
        $('#hand_luggage_count').empty()
        var formDataObject  = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
        formDataObject['fleet_id'] = car_type;

        $.ajax({
            data: formDataObject,
            url: "{{env('API_URL')}}getfleet",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                if(response['status'] == 200){
              //dd means dropdown
                let passenger_dd = ''
                let luggage_dd = ''
                let hand_luggage_dd = ''
                let child_dd = ''
                
                // console.log('jana values',response['data'].luggage);
                
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



                $('#passenger_count').html(passenger_dd)
                $('#luggage_count').html(luggage_dd)
                $('#hand_luggage_count').html(hand_luggage_dd)
                $('#child_seat_count').html(child_dd)
               
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
                // console.log('Error:', data);
            }
        });
    }

    function ChildSeatMaker(seat_count){
        let child_seat_dropdown = ''

            for(let i = 1; i <= seat_count; i++){
                child_seat_dropdown += `<div class="col-sm-3">
                        <label for="baby_seat_${i}">Child Seat ${i}</label>
                        <select class="form-control" id="baby_seat_${i}" name="baby_seat_${i}">
                            <option value="Rear Facing" selected>Rear Facing</option>
                            <option value="Forward Facing">Forward Facing</option>
                            <option value="Booster">Booster</option>
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

        // if (journey === 'return') {
        //     $('.return_flight_ship_details').show()
        //     $('.invalid_return_flight_number').text('')
        //     $('.invalid_return_flight_from').text('')
        //         $('#is_airport_or_ship_return').val(1)
        //         $('#return_transport_name').text('Flight Information')

        //         $("label[for|='return_flight_number']").html('Flight Number <span class="required">*</span>')
        //         $("label[for|='return_flight_from']").html('Flight From <span class="required">*</span>')

        //         $('#return_flight_number').attr('placeholder', 'Flight Number');
        //         $('#return_flight_from').attr('placeholder', 'Flight From');
        //     } 
    }

    function DepartureDetailsContainer(place_type, journey_type) {
        let place = place_type ? place_type.trim() : '';
        let journey = journey_type ? journey_type.trim() : '';

        if (place === '' || journey === '') {
            $('.one_way_departure_flight_ship_details').hide()
        }
            $('.one_way_departure_flight_ship_details').hide()
    }

    function AssignValues(data, journey_type, currencyval) {
      
        let offerhide = data.total_oneway_date_time
        let oneway_all_total = data.total_fare + data.o_time_price_only + data.one_day_price
        let return_all_total = data.r_total_fare + data.r_time_price_only + data.r_day_price

        $('#oneway_special_off_total123').val(data.o_time_price_only);
        $('#oneway_date_offerprice').val(data.one_day_price);
        $('#return_offer_time_price').val(data.r_time_price_only);
        $('#return_offer_date_price').val(data.r_day_price);
        $('#one_way_all_total').val(oneway_all_total);
        $('#return_overall_total').val(return_all_total);
        $('#net_total').val(oneway_all_total);
        $('#return_net_total').val(return_all_total);
        
        let returnofferhide = data.total_return_date_time
        const symbols = {
            INR: '₹',
            USD: '$',
            GBP: '£',
            KWD: 'KD',
            IQD: 'IQD',
            CAD: 'C$'
        };
        
        let currencySymbol = symbols[currencyval] || currencyval;
        
            // console.log('off value', currencySymbol + ' ' + data.total_oneway_date_time);

            if(returnofferhide){
                $('.return_offershowhide').show();
            }else{
                $('.return_offershowhide').hide();
            }
        if (journey_type === 'one_way') {
            
            $('#one_way_travel_time').val(data.duration)
            $('#one_way_distance').val(data.distance_unit)
            $('#one_way_actual_amount').val(data.total_fare)

            //these values are stored in hidden input fields
            $('#one_way_from_lati').val(data.from_lati)
            $('#one_way_from_longi').val(data.from_longi)
            $('#one_way_to_lati').val(data.to_lati)
            $('#one_way_to_longi').val(data.to_longi)
            $('.oneway_special_off_total').text('( ' + currencySymbol + data.total_oneway_date_time + ' )')
            $('.return_special_off_total').text('( ' + currencySymbol + data.total_return_date_time + ' )')
            // console.log('off vale',data);
            if(offerhide){
                $('.oneway_offershowhide').show();
            }else{
                $('.oneway_offershowhide').hide();
            }
            
            //calculate total cost
            CalculateAmount('one_way')
        } else if (journey_type === 'return') {
            
            $('#return_travel_time').val(data.duration)
            $('#return_distance').val(data.distance_unit)
            $('#return_actual_amount').val(data.r_total_fare)
            
            //these values are stored in hidden input fields
            $('#return_from_lati').val(data.from_lati)
            $('#return_from_longi').val(data.from_longi)
            $('#return_to_lati').val(data.to_lati)
            $('#return_to_longi').val(data.to_longi)
            
            // if(returnofferhide){
            //     $('.return_offershowhide').show();
            // }else{
            //     $('.return_offershowhide').hide();
            // }
            // console.log('valyyy',returnofferhide);
            
            //calculate total cost
            CalculateAmount('return')
        }
    }

    function ShowTripErrors(errors) {
        if ($('#search_clients').val() == '') {
            ShowClientInfo({
                f_name: '',
                email: '',
                phone: ''
            })
        }

        if (errors.client_id) {
            $('.invalid_client_id').text(errors.client_id)
        } else {
            $('.invalid_client_id').text('')
        }

        if (errors.client_name) {
            $('.invalid-client-name').text(errors.client_name)
        } else {
            $('.invalid-client-name').text('')
        }

        if (errors.client_email) {
            $('.invalid-client-email').text(errors.client_email)
        } else {
            $('.invalid-client-email').text('')
        }

        if (errors.client_mobile) {
            $('.invalid-client-mobile').text(errors.client_mobile)
        } else {
            $('.invalid-client-mobile').text('')
        }

        if (errors.journey_type) {
            $('.invalid-journey-type').text(errors.journey_type)
        } else {
            $('.invalid-journey-type').text('')
        }

        if (errors.booking_date) {
            $('.invalid-booking-date').text(errors.booking_date)
        } else {
            $('.invalid-booking-date').text('')
        }

        if (errors.car_type) {
            $('.invalid-car-type').text(errors.car_type)
        } else {
            $('.invalid-car-type').text('')
        }

        if (errors.passenger_count) {
            $('.invalid-passenger-count').text(errors.passenger_count)
        } else {
            $('.invalid-passenger-count').text('')
        }

        if (errors.child_seat_count) {
            $('.invalid-child-seat-count').text(errors.child_seat_count)
        } else {
            $('.invalid-child-seat-count').text('')
        }

        if (errors.luggage_count) {
            $('.invalid-luggage-count').text(errors.luggage_count)
        } else {
            $('.invalid-luggage-count').text('')
        }

        if (errors.hand_luggage_count) {
            $('.invalid-hand-luggage-count').text(errors.hand_luggage_count)
        } else {
            $('.invalid-hand-luggage-count').text('')
        }

        if (errors.baby_seat_1) {
            $('.invalid-baby-seat-1').text(errors.baby_seat_1)
        } else {
            $('.invalid-baby-seat-1').text('')
        }

        if (errors.baby_seat_2) {
            $('.invalid-baby-seat-2').text(errors.baby_seat_2)
        } else {
            $('.invalid-baby-seat-2').text('')
        }

    }

    function ShowOneWayErrors(errors) {
        if (errors.one_way_pick_up) {
            $('.invalid_one_way_pick_up').text(errors.one_way_pick_up)
        } else {
            $('.invalid_one_way_pick_up').text('')
        }

        if (errors.one_way_drop_off) {
            $('.invalid_one_way_drop_off').text(errors.one_way_drop_off)
        } else {
            $('.invalid_one_way_drop_off').text('')
        }

        if (errors.one_way_pickup_date) {
            $('.invalid_one_way_pickup_date').text(errors.one_way_pickup_date)
        } else {
            $('.invalid_one_way_pickup_date').text('')
        }

        if (errors.one_way_pickup_time) {
            $('.invalid_one_way_pickup_time').text(errors.one_way_pickup_time)
        } else {
            $('.invalid_one_way_pickup_time').text('')
        }

        if (errors.one_way_pickup_address) {
            $('.invalid_one_way_pickup_address').text(errors.one_way_pickup_address)
        } else {
            $('.invalid_one_way_pickup_address').text('')
        }

        if (errors.one_way_dropoff_address) {
            $('.invalid_one_way_dropoff_address').text(errors.one_way_dropoff_address)
        } else {
            $('.invalid_one_way_dropoff_address').text('')
        }

        if (errors.one_way_flight_date) {
            $('.invalid_one_way_flight_date').text(errors.one_way_flight_date)
        } else {
            $('.invalid_one_way_flight_date').text('')
        }

        if (errors.one_way_flight_time) {
            $('.invalid_one_way_flight_time').text(errors.one_way_flight_time)
        } else {
            $('.invalid_one_way_flight_time').text('')
        }

        if (errors.one_way_flight_pickup_time) {
            $('.invalid_one_way_flight_pickup_time').text(errors.one_way_flight_pickup_time)
        } else {
            $('.invalid_one_way_flight_pickup_time').text('')
        }

        if (errors.one_way_flight_number) {
            $('.invalid_one_way_flight_number').text(errors.one_way_flight_number)
        } else {
            $('.invalid_one_way_flight_number').text('')
        }

        if (errors.one_way_flight_from) {
            $('.invalid_one_way_flight_from').text(errors.one_way_flight_from)
        } else {
            $('.invalid_one_way_flight_from').text('')
        }

        if (errors.one_way_payment_status) {
            $('.invalid_one_way_payment_status').text(errors.one_way_payment_status)
        } else {
            $('.invalid_one_way_payment_status').text('')
        }

        if (errors.one_way_payment_method) {
            $('.invalid_one_way_payment_method').text(errors.one_way_payment_method)
        } else {
            $('.invalid_one_way_payment_method').text('')
        }

        if (errors.one_way_order_status) {
            $('.invalid_one_way_order_status').text(errors.one_way_order_status)
        } else {
            $('.invalid_one_way_order_status').text('')
        }

        if (errors.one_way_total_cost) {
            $('.invalid_one_way_total_cost').text(errors.one_way_total_cost)
        } else {
            $('.invalid_one_way_total_cost').text('')
        }

        if (errors.one_way_extra_cost) {
            $('.invalid_one_way_extra_cost').text(errors.one_way_extra_cost)
        } else {
            $('.invalid_one_way_extra_cost').text('')
        }

        if (errors.one_way_distance) {
            $('.invalid_one_way_distance').text(errors.one_way_distance)
        } else {
            $('.invalid_one_way_distance').text('')
        }

        if (errors.one_way_travel_time) {
            $('.invalid_one_way_travel_time').text(errors.one_way_travel_time)
        } else {
            $('.invalid_one_way_travel_time').text('')
        }

        if (errors.one_way_dest_ship_name) {
            $('.invalid_one_way_dest_ship_name').text(errors.one_way_dest_ship_name)
        } else {
            $('.invalid_one_way_dest_ship_name').text('')
        }

    }

    function ShowReturnErrors(errors) {

        if (errors.return_pick_up) {
            $('.invalid_return_pick_up').text(errors.return_pick_up)
        } else {
            $('.invalid_return_pick_up').text('')
        }

        if (errors.return_drop_off) {
            $('.invalid_return_drop_off').text(errors.return_drop_off)
        } else {
            $('.invalid_return_drop_off').text('')
        }

        if (errors.return_pickup_date) {
            $('.invalid_return_pickup_date').text(errors.return_pickup_date)
        } else {
            $('.invalid_return_pickup_date').text('')
        }

        if (errors.return_pickup_time) {
            $('.invalid_return_pickup_time').text(errors.return_pickup_time)
        } else {
            $('.invalid_return_pickup_time').text('')
        }

        if (errors.return_pickup_address) {
            $('.invalid_return_pickup_address').text(errors.return_pickup_address)
        } else {
            $('.invalid_return_pickup_address').text('')
        }

        if (errors.return_dropoff_address) {
            $('.invalid_return_dropoff_address').text(errors.return_dropoff_address)
        } else {
            $('.invalid_return_dropoff_address').text('')
        }

        if (errors.return_flight_date) {
            $('.invalid_return_flight_date').text(errors.return_flight_date)
        } else {
            $('.invalid_return_flight_date').text('')
        }

        if (errors.return_flight_time) {
            $('.invalid_return_flight_time').text(errors.return_flight_time)
        } else {
            $('.invalid_return_flight_time').text('')
        }

        if (errors.return_flight_pickup_time) {
            $('.invalid_return_flight_pickup_time').text(errors.return_flight_pickup_time)
        } else {
            $('.invalid_return_flight_pickup_time').text('')
        }

        if (errors.return_flight_number) {
            $('.invalid_return_flight_number').text(errors.return_flight_number)
        } else {
            $('.invalid_return_flight_number').text('')
        }

        if (errors.return_flight_from) {
            $('.invalid_return_flight_from').text(errors.return_flight_from)
        } else {
            $('.invalid_return_flight_from').text('')
        }

        if (errors.return_payment_status) {
            $('.invalid_return_payment_status').text(errors.return_payment_status)
        } else {
            $('.invalid_return_payment_status').text('')
        }

        if (errors.return_payment_method) {
            $('.invalid_return_payment_method').text(errors.return_payment_method)
        } else {
            $('.invalid_return_payment_method').text('')
        }

        if (errors.return_order_status) {
            $('.invalid_return_order_status').text(errors.return_order_status)
        } else {
            $('.invalid_return_order_status').text('')
        }

        if (errors.return_total_cost) {
            $('.invalid_return_total_cost').text(errors.return_total_cost)
        } else {
            $('.invalid_return_total_cost').text('')
        }

        if (errors.return_extra_cost) {
            $('.invalid_return_extra_cost').text(errors.return_extra_cost)
        } else {
            $('.invalid_return_extra_cost').text('')
        }

        if (errors.return_distance) {
            $('.invalid_return_distance').text(errors.return_distance)
        } else {
            $('.invalid_return_distance').text('')
        }

        if (errors.return_travel_time) {
            $('.invalid_return_travel_time').text(errors.return_travel_time)
        } else {
            $('.invalid_return_travel_time').text('')
        }

        if(errors.one_way_driver_amount){
            $('.invalid_one_way_driver_amount').text(errors.one_way_driver_amount)
        } else {
            $('.invalid_one_way_driver_amount').text('')
        }
    }

    function CheckSpecialDay(special_date, journey_type) {
        // console.log(special_date)

        $.ajax({
            data: {
                special_date: special_date,
            },
            url: "{{ route('CheckSpecialDay') }}",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                if (response.length > 0) {
                    if (journey_type === 'one_way') {
                        $('#one_way_special_day_percentage').val(response[0].cost)

                        //calculate total cost
                        CalculateAmount('one_way')

                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'For outward trip: Extra ' + response[0].cost + '% applicable.',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    } else if (journey_type === 'return') {
                        $('#return_special_day_percentage').val(response[0].cost)

                        //calculate total cost
                        CalculateAmount('return')

                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'For return trip: Extra ' + response[0].cost + '% applicable.',
                            showConfirmButton: false,
                            timer: 5000
                        })
                    }
                } else {
                    if (journey_type === 'one_way') {
                        $('#one_way_special_day_percentage').val(0)
                        CalculateAmount('one_way')
                    } else if (journey_type === 'return') {
                        $('#return_special_day_percentage').val(0)
                        CalculateAmount('return')
                    }
                }
            },
            error: function(data) {
                // console.log('Error:', data);
            }
        });
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
            $('#one_way_total_cost').val(Math.ceil(isEditable && total_cost !== 0 ? total_cost : one_way_caculated_amount))
            // $('#net_total').val(Math.ceil(isEditable && total_cost !== 0 ? total_cost : one_way_caculated_amount))
        } else if (journey_type === 'return') {
            $('#return_total_cost').val(Math.ceil(return_caculated_amount))
            // $('#return_net_total').val(Math.ceil(return_caculated_amount))
        }
    }

    function AutoSelectReturnPickup() {
        let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false
        let area = $('#one_way_drop_off').val()
        let address = $('#one_way_dropoff_address').val() ? $('#one_way_dropoff_address').val() : ''
        // console.log(area)
        // console.log(address)
        // console.log('hiiiiiiiiiiiii')
        if (journey_type && area) {
            $('#return_pick_up').append(`<option value="${area}" selected>${area}</option>`)
            $('#return_pickup_address').val(address)
            
            if (area && (area.toLowerCase().includes('airport') || area.toLowerCase().includes('terminal')) && !area.toLowerCase().includes('bus')) {
                $('.return_flight_ship_details').show()
                $('.invalid_return_flight_number').text('')
                $('.invalid_return_flight_from').text('')
                $('#is_airport_or_ship_return').val(1)
                $('#return_transport_name').text('Flight Information')
    
                $("label[for|='return_flight_number']").html('Flight Number')
                $("label[for|='return_flight_from']").html('Flight From')
    
                $('#return_flight_number').attr('placeholder', 'Flight Number');
                $('#return_flight_from').attr('placeholder', 'Flight From');
                
                // $('.return_flight').show();
            } else {
                $('.return_flight_ship_details').hide()
            }
        }
    }

    function AutoSelectReturnDrop() {
        let journey_type = $('[name="journey_type"]:checked').val() === 'Return' ? true : false
        let area = $('#one_way_pick_up').val()
        let address = $('#one_way_pickup_address').val() ? $('#one_way_pickup_address').val() : ''

        if (journey_type && area) {
            $('#return_drop_off').append(`<option value="${area}" selected>${area}</option>`)
            $('#return_dropoff_address').val(address)
        }
    }

    function ReturnTripInitialAutoCalculation(journey_type) {
        if (journey_type) {
            $('#return_from_lati').val($('#one_way_to_lati').val())
            $('#return_from_longi').val($('#one_way_to_longi').val())

            $('#return_to_lati').val($('#one_way_from_lati').val())
            $('#return_to_longi').val($('#one_way_from_longi').val())

            // $('#return_total_cost').val(Number($('#one_way_actual_amount').val()))
            // $('#return_net_total').val(Number($('#one_way_actual_amount').val()))
            // $('#return_actual_amount').val(Number($('#one_way_actual_amount').val()))

            $('#return_distance').val(Number($('#one_way_distance').val()))
            $('#return_travel_time').val($('#one_way_travel_time').val())
        } else {
            $('#return_from_lati').val('')
            $('#return_from_longi').val('')
            $('#return_to_lati').val('')
            $('#return_to_longi').val('')
            $('#return_total_cost').val('')
            // $('#return_net_total').val('')
            $('#return_actual_amount').val()
            $('#return_distance').val('')
            $('#return_travel_time').val('')
        }
    }
    
    function extraOneway(){
            
        let extra = parseFloat($('#one_way_extra_cost').val()) || 0;
        let actual = parseFloat($('#one_way_total_cost').val()) || 0;
    
        // $('#net_total').val(actual + extra);
    }
    
    function extraReturn(){
        let extra = parseFloat($('#return_extra_cost').val()) || 0;
        let actual = parseFloat($('#return_total_cost').val()) || 0;
    
        // $('#return_net_total').val(actual + extra);
        
    }
    
    
    
</script>
