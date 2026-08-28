<script src="https://cdn.jsdelivr.net/npm/dottie@2.0.2/dottie.js">
    //Dottie JS is used to remove dot in JS object name.
</script>

<script>
    $(function() {

        //Total booking row counts
        let row_count = 0
    
        //trigger booking id, if request come from booking page.
            //Select2 AJAX search for jobs
            var formDataObject = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            
            $('#search_job').select2({
                ajax: {
                    url: "{{env('API_URL')}}bookinglist",
                    type: "post",
                    dataType: 'json',
                    delay: 400,
                    data: function(params) {
                        return {
                            search: params.term, // search term
                            token: formDataObject.token,
                            device_id: formDataObject.device_id,
                            order_status: 'All'
                        };
                    },
                    processResults: function(response) {
                        const data = response.data;
                        //  console.log(data.length);
                        if(data.length == 0){
                             return {
                             results: []
                         };
                        }
                         if(data.length > 0){
                         const formattedData = data.map(item => ({
                             id: item.id,
                             text: item.job_no
                         }));
                         return {
                             results: formattedData
                         };
                         }
                    },
                    cache: true
                }
            })
            //Load data for selected client
            $('#search_job').change(function() {
                let job_id = $('#search_job').val()
                OnChangeJob()

                $.ajax({
                    type: "POST",
                    url: "{{env('API_URL')}}editbooking",
                    data: {
                        book_id: job_id,
                        token: formDataObject.token,
                        device_id: formDataObject.device_id,
                    },
                    success: function(response) {
                        ShowJobDetails(response['booking_details'])
                        ShowMultiPickup(response['booking_details'])
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            })
            // AutoJobSelection()



        $(document).on('click', '.add_booking', function() {
            row_count = row_count + 1
            let pickup_time = $('#pickup_time').val()
            let from_location = $('#valid_from').val()
            let to_location = $('#valid_to').val()
             let placefrom_id = $('#valid_place_from').val()
              let placeto_id = $('#valid_place_to').val()

            let new_row = `<div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="pickup_date_${row_count}" class="col-form-label">Pickup Date</label>
                                        <div class="input-group">
                                            <input type="text" name="pickup_date[]" class="form-control pickup_date"
                                                value="" id="pickup_date_${row_count}"  placeholder="dd-mm-yyyy">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="(function(){$('#pickup_date_${row_count}').datepicker({ format: 'dd-mm-yyyy' }).datepicker('show')})()">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </div>
                                        <p class="text-danger invalid_pickup_date"></p>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="pickup_time_${row_count}" class="col-form-label">Pickup Time</label>
                                        <input type="time" name="pickup_time[]" class="form-control pickup_time" value="${pickup_time}"
                                            id="pickup_time_${row_count}" >
                                    </div>
                                    <p class="text-danger invalid_pickup_time"></p>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="pickup_location_${row_count}" class="col-form-label">Pick up</label>
                                        <input type="text" name="pickup_location[]" id="pickup_location_${row_count}"
                                            class="form-control from_location" value="${from_location}" readonly>
                                    </div>
                                    <p class="text-danger invalid_pickup_location"></p>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-primary shift_location"
                                        title="Shift Locations" style="position: absolute; top: 36px; left: 30px;">
                                        <i class="fa fa-retweet" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="dropoff_location_${row_count}" class="col-form-label">Drop off</label>
                                        <input type="text" name="dropoff_location[]" id="dropoff_location_${row_count}"
                                            class="form-control to_location" value="${to_location}" readonly>
                                    </div>
                                    <p class="text-danger invalid_dropoff_location"></p>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-danger remove_booking" title="Add New Row"
                                        style="position: absolute; top: 36px;">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>`
            $('#job_detail_container').append(new_row)
        })

        $(document).on('click', '.shift_location', function() {
            let from_location = $(this).closest('.row').find('.from_location').val()
            let to_location = $(this).closest('.row').find('.to_location').val()
            
            let from_place_id = $(this).closest('.row').find('.from_place_id').val()
            let to_place_id = $(this).closest('.row').find('.to_place_id').val()

            $(this).closest('.row').find('.from_location').val(to_location)
            $(this).closest('.row').find('.to_location').val(from_location)
            
             $(this).closest('.row').find('.from_place_id').val(to_place_id)
             $(this).closest('.row').find('.to_place_id').val(from_place_id)
        })

        $(document).on('click', '.remove_booking', function() {
            $(this).closest('.row').remove()
        })

        $(document).on('click', '.save_booking', function() {
            var formdata = $('#multiBookingForm').serialize();
            var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            delete formDataObject['pickup_date[]'];
            var inputValues = [];
            $("input[name='pickup_date[]").each(function() {
            inputValues.push($(this).val());
            });
            formDataObject['pickup_date'] = inputValues;
            delete formDataObject['pickup_time[]'];
            var inputValues1 = [];
            $("input[name='pickup_time[]").each(function() {
            inputValues1.push($(this).val());
            });
            formDataObject['pickup_time'] = inputValues1;
            delete formDataObject['pickup_location[]'];
            var inputValues2 = [];
            $("input[name='pickup_location[]").each(function() {
            inputValues2.push($(this).val());
            });
            formDataObject['pickup_location'] = inputValues2;
            delete formDataObject['pickup_location[]'];
            var inputValues2 = [];
            $("input[name='pickup_location[]").each(function() {
            inputValues2.push($(this).val());
            });
            formDataObject['pickup_location'] = inputValues2;
            delete formDataObject['dropoff_location[]'];
            var inputValues3 = [];
            $("input[name='dropoff_location[]").each(function() {
            inputValues3.push($(this).val());
            });
            formDataObject['dropoff_location'] = inputValues3;
            delete formDataObject['valid_place_from'];
            delete formDataObject['valid_place_to'];
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;

            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}multibooking",
                data: formDataObject,
                success: function(response) {
                    if(response['status'] == 200){
                         setCookie('swal',response['message'],'1')
                         window.location.href="/booking/list/All";
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
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        })
    })
    function errornotify(response){
       var title = "Required";
        const obj = response['errors'];
        const arrayOfObjects = [];
        for (const key in obj) {
              if (obj.hasOwnProperty(key)) {
                warningClick(title,response['errors'][key][0],"danger")
             }
       }
       if(response['message']){
           warningClick("Error",response['message'],"warning")
       }
    }
    
    function warningClick(ttl,msg,c_type){
            $.notify({
    	// options
    	title: '<strong>'+ttl+'</strong>',
    	message: "<br>"+msg+"",
      icon: 'glyphicon glyphicon-warning-sign',
    },{
    	// settings
    	element: 'body',
    	position: null,
    	type: c_type,
    	allow_dismiss: true,
    	newest_on_top: false,
    	showProgressbar: false,
    	placement: {
    		from: "top",
    		align: "right"
    	},
    	offset: 20,
    	spacing: 10,
    	z_index: 1031,
    	delay: 3300,
    	timer: 1000,
    	url_target: '_blank',
    	mouse_over: null,
    	animate: {
    		enter: 'animated bounceIn',
    		exit: 'animated bounceOut'
    	},
    	onShow: null,
    	onShown: null,
    	onClose: null,
    	onClosed: null,
    	icon_type: 'class',
      });
    }
    function ShowJobDetails(data) {
        $('#client_name').val(data.fname)
        $('#client_email').val(data.email)
        $('#client_mobile').val(data.mobile)
        $('#car_type').val(data.car_type.toUpperCase())
        $('#no_of_passengers').val(data.passengers)
        $('#payment_status').val(data.payment_status)
        $('#order_status').val(data.order_status)
        $('#total_cost').val(data.total)
        $('#extra_cost').val(data.car_park_amount)
    }

    function ShowMultiPickup(data) {
        $('#job_id').val(data.id)
        $('#valid_from').val(data.from)
        $('#valid_to').val(data.to)
        $('#valid_place_from').val(data.place_from)
        $('#valid_place_to').val(data.place_to)
        $('#pickup_time').val(data.pickup_time)

    

        if (data.id && data.from && data.to) {
            $('#job_detail_header').show()
            $('#job_detail_container').show()
            $('#job_detail_footer').show()
            $('#pickup_location_0').val(data.from)
            $('#dropoff_location_0').val(data.to)
            $('#multi_fplace_id_0').val(data.place_from)
            $('#multi_toplace_id_0').val(data.place_to)
            $('#pickup_time_0').val(data.pickup_time)
        } else {
            $('#job_detail_header').hide()
            $('#job_detail_container').hide()
            $('#job_detail_footer').hide()
        }
    }

    function OnChangeJob() {
        $("#job_detail_container").children().not(':first').remove()
    }

    function ResetErrors() {
        $(".invalid_pickup_date, .invalid_pickup_time, .invalid_pickup_location, .invalid_dropoff_location").text('')
    }

    function ShowErrors(pickup_date_errors, pickup_time_errors,
        pickup_location_errors, dropoff_location_errors) {

        if (pickup_date_errors) {
            for (let i = 0; i < pickup_date_errors.length; i++) {
                $(".invalid_pickup_date:eq(" + parseInt(pickup_date_errors[i]) + ")").text(
                    'The pickup date is required.')
            }
        }

        if (pickup_time_errors) {
            for (let i = 0; i < pickup_time_errors.length; i++) {
                $(".invalid_pickup_time:eq(" + parseInt(pickup_time_errors[i]) + ")").text(
                    'The pickup time is required.')
            }
        }

        if (pickup_location_errors) {
            for (let i = 0; i < pickup_location_errors.length; i++) {
                $(".invalid_pickup_location:eq(" + parseInt(pickup_location_errors[i]) + ")").text(
                    'The pickup location is required.')
            }
        }

        if (dropoff_location_errors) {
            for (let i = 0; i < dropoff_location_errors.length; i++) {
                $(".invalid_dropoff_location:eq(" + parseInt(dropoff_location_errors[i]) + ")").text(
                    'The dropoff location is required.')
            }
        }
    }

    function AutoJobSelection() {
        let job_id = $('#job_no').val()

        $.ajax({
            type: "POST",
            url: "{{ route('GetJobDetails') }}",
            data: {
                id: job_id
            },
            success: function(response) {
                console.log(response)
                ShowJobDetails(response[0])
                ShowMultiPickup(response[0])
            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
    }

    @if (session('booking_status_save') && session('multi_booking_id'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Added',
            text: '{{ session('booking_status_save') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('booking_status_save');
            Illuminate\Support\Facades\Session::forget('multi_booking_id');
        @endphp

        console.log('Second: ' +
            '{{ session('multi_booking_id') ? session('multi_booking_id') : 'Session Empty...' }}')
    @endif
</script>
