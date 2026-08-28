<script>
// SHOW Wallet settlement histry
$(document).ready(function() {
          const url = 'transactions';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
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
    if (response && response.transaction_summary && response.transaction_summary.total) {
        var totalValue = response.transaction_summary.total;
        if (response.status === 200) {
            document.getElementById("totalValue").innerHTML = totalValue;
        } else if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }
    } else {
        console.error('Invalid response structure:', response);
    }
});
});
// booking overal ammount
$(document).ready(function() {

          const url = 'indexbookingsammount';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
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
    var bookingsvalue=response.transactions[0].totalNetTotal;
        if (response.status === 200) {
            document.getElementById("totalbookingValue").innerHTML = bookingsvalue;
        } else if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }
});
});

// driver ammount 
$(document).ready(function() {

          const url = 'indexdriverammount';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
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
    var bookingsvalues=response.transactions[0].totalNetTotal;
        if (response.status === 200) {
            document.getElementById("totaldrivervalue").innerHTML = bookingsvalues;
        } else if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }
});
});

//auto slider on dashboard with airport data
$(document).ready(function() {

          const url = 'rangefareindexunicairportget';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
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
    var airportname=response.data[0].from_airport;
    var airportname1=response.data[1].from_airport;
    var country1 = extractCountry(airportname);
    var country2 = extractCountry(airportname1);
    if (response.status === 200) {
    document.getElementById("totalairportvalue").innerHTML = airportname;
    document.getElementById("country1").innerHTML = country1;
    document.getElementById("country2").innerHTML = country2;
    document.getElementById("totalairportvalue1").innerHTML = airportname1;

        } else if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }
});
});
//get country 
function extractCountry(airportName) {

    var parts = airportName.split(',');

    var country = parts[parts.length - 1].trim();

    return country;
}


    $(function() {
        
        showlist('all')
        bookingsCount()
        driverlist()
        FleetList()
        $('#driver_name, #driver_name_filter').select2()

        //Date range picker variables
        const start = moment('2015-01-01');
        const end = moment();

        //Date range picker for pickup dates
        function pickup_callback(start, end) {
            $('#pickup_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#pickup_between_filter').daterangepicker({
            locale: {
      firstDay: 1
    }
        }, pickup_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        pickup_callback(start, end);

        //Date range picker for booking dates
        function booking_callback(start, end) {
            $('#booking_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#booking_between_filter').daterangepicker({
            locale: {
      firstDay: 1
    }
        }, booking_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        booking_callback(start, end);

        $("#pickup_between_filter").change(function() {
            let selected_date = $(this).val()
            let date_array = selected_date.split(" ")

            let from_date = moment(date_array[0]).format('YYYY-MM-DD');
            let to_date = moment(date_array[2]).format('YYYY-MM-DD');

            $('#pickup_date_from').val(from_date)
            $('#pickup_date_to').val(to_date)
        });

        //Change the value of date range filter for booking
        $("#booking_between_filter").change(function() {
            let selected_date = $(this).val()
            let date_array = selected_date.split(" ")

            let from_date = moment(date_array[0]).format('YYYY-MM-DD');
            let to_date = moment(date_array[2]).format('YYYY-MM-DD');

            $('#booking_date_from').val(from_date)
            $('#booking_date_to').val(to_date)
        });
        
        $('#dash_book_reset').on('click', function(){
            $('#book_filter_form')[0].reset()
            $('#pickup_date_from').val('')
            $('#pickup_date_to').val('')
            $('#booking_date_from').val('')
            $('#booking_date_to').val('')
            showlist('all')
        })
        
        $('#dash_book_search').on('click', function(){
            const url = 'bookingfilter';
            var key = window.location.href;
            var segments = key.split('/');
            var lastSegment = segments.pop();
                 var formdata = $('#book_filter_form').serialize();
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
                 formDataObject['order_status'] = '';
                 delete formDataObject['pickup_between_filter'];
                 delete formDataObject['booking_between_filter'];
                 // Destroy the existing DataTable before reinitializing
                var existingTable = $('#dash-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#dash-table', {
                processing: true,
                searching: false,
             ajax: {
                 url: '{{env('API_URL')}}bookingfilter',
                 method: 'POST',
                 dataSrc:"data",
                 data: formDataObject,
             },
             createdRow: function (row, data, dataIndex) {
                 // Add a class to the <tr> element based on the o  rder_status value
                 $(row).addClass('col-md-6 mb-2 card db-standard');
             },
             columns: [
                 { data: null,
                 className: 'dt-table-1',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-arrow-up-9-1" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.job_no}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-2',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-plane-arrival" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_flight_num}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-3',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_date+' / '+row.pickup_time}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-4',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.booking_date}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.vehichle_name != null ? row.vehichle_name : '-'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.pickup_address}`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-14',
                 render: function(data,type,row){
                     return `<p class="border-dashed"></p>`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-8',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.dest_address}`;
                 }
                 },
                 {  data: null,
                 className: 'dt-table-9',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-file-invoice" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.payment_status != null ? row.payment_status : '-'}`;
                 }
                 },
                 { data: 'type',
                 className: 'dt-table-10',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-building-columns" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.type}`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-11',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : '-'}`;
                 }
                 },
                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<select class="form-control booking-status" onchange="changeBookStatus(${row.id})" id="book_status${row.id}" name="status">
                        <option value="Pending" ${row.order_status == 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Confirmed" ${row.order_status == 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                        <option value="Assigned" ${row.order_status == 'Assigned' ? 'selected' : ''}>Assigned</option>
                        <option value="Dispatched" ${row.order_status == 'Dispatched' ? 'selected' : ''}>Dispatched</option>
                        <option value="Completed" ${row.order_status == 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="Settled" ${row.order_status == 'Settled' ? 'selected' : ''}>Settled</option>
                        <option value="Canceled" ${row.order_status == 'Canceled' ? 'selected' : ''}>Cancelled</option>
                    </select>`;
                     }
                 },
                 {
                     data: null,
                     className: 'dt-table-13',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<ul>
                         <li  style="list-style-type: none;"><a href="/booking/edit/${row.id}" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a></li>
                         <li  style="list-style-type: none;"><a href="/booking-status-pdf/${row.id}" target="_blank" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a></li>
                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" title="Assign Driver" onclick="assigndriver(${row.id},'${row.job_no}','${row.total}')" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a><a href="javascript:void(0)" onclick="sendConfirmationEmail(${row.id})" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="removedriver(${row.id})" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a></li>
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendSMS"><i class="fa fa-paper-plane"></i></a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendMail(${row.id})" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         </ul>`;
                     }
                 }
             ],
            //  responsive: {
            //       details: {
            //           type: 'column',
            //           target: 'tr'
            //       }
            //   }
            });
        })

        //Assign driver
        $('#saveBtn').click(function(e) {
            e.preventDefault();
            var formdata = $('#assignDriverForm').serialize();
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
                url: "{{env('API_URL')}}assigndriver",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    if(response['status'] == 200){
                        $('#form-modal').modal('hide');
                     Swal.fire({
                                position: "top-right",
                                icon: "success",
                                title: response['message'],
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                             showlist('all')
                         });
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
        });
        
        $('#fleet_create_sub').on('click', function(){
          const url = 'createvehichle';
        var formdata = $('#fleet_create_form').serialize();
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
         if(response['status'] == 200){
             $("#fleet_create_form")[0].reset();
             $('.modal-title').html('Add Fleet')
             $('#vehichle_form-modal').modal('hide')
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    FleetList()
                });
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
      
      $('#veh_brand_id').on('change', function(){
          var id = $('#veh_brand_id').val();
          if(id != ''){
              models(id,'','veh_model_id')
          }else{
              $('#model_id').html('<option value="">select</option>')
          }
      })


        $('body').on('click', '#preview_email', function() {
            bootprompt.dialog({
                title: 'Mail Preview',
                message: tinymce.get("customer_email_body").getContent(),
                size: 'large'
            })
        })

        $('#email_send_btn').click(function() {
            let message = tinymce.get("customer_email_body").getContent()
            let email = $('#customer_email').val()
            let current_url = '{{ url('') }}'
            let formatted_message = message.replace("../..", current_url)
            
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['message'] = formatted_message;
            formDataObject['email'] = email;
            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}bookdetail-mail",
                data: formDataObject,
                beforeSend: function() {
                    $('#load_animation_email').show()
                },
                success: function(response) {
                    $('#load_animation_email').hide()

                    if(response.status == 200){
                        $('#email-modal').modal('hide')
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                    }
                    if(response.status == 400){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                    }
                    if(response['status'] == 500){
                       warningClick('Error',response['error'],"danger")
                    }
                    if(response['status'] == 401){
                       unauth()
                    }
                },
                error: function(data) {
                    $('#load_animation_email').hide()
                    console.log('Error');
                }
            });
        })

        $('#sms_send_btn').click(function() {
            let customer_number = $('#customer_no').val()
            let driver_number = $('#driver_no').val()
            let customer_message = $('#customer_message').val()
            let driver_message = $('#driver_message').val()
            let sms_customer = $('#customer_sms:checked').val() ? true : false
            let sms_driver = $('#driver_sms:checked').val() ? true : false

            if(sms_customer || sms_driver){
                $.ajax({
                    type: "POST",
                    url: "{{ route('SMSBookingDetails') }}",
                    data: {
                        customer_number: customer_number,
                        driver_number: driver_number,
                        customer_message: customer_message,
                        driver_message: driver_message,
                        sms_customer: sms_customer,
                        sms_driver: sms_driver
                    },
                    beforeSend: function() {
                        $('#load_animation_sms').show()
                    },
                    success: function(response) {
                        $('#load_animation_sms').hide()

                        if(response.status == 200){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'SMS Status',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 5000,
                            })
                        }
                    },
                    error: function(data) {
                        $('#load_animation_sms').hide()
                        console.log('Error');
                    }
                });
            } else {
                Swal.fire({
                    position: 'bottom-start',
                    icon: 'warning',
                    title: 'Recipient not selected',
                    text: 'Please select either driver or customer.',
                    showConfirmButton: false,
                    timer: 3000,
                })
            }
        })

        tinymce.init({
            selector: '#customer_email_body',
            branding: false,
            height: '1000',
            menu: {
                file: {
                    title: '',
                    items: ''
                },
                view: {
                    title: '',
                    items: ''
                },
            },
            relative_urls: false,
            remove_script_host: false
        });

        $('#reset').click(function(){
            $("#driver_name_filter").val(null).trigger("change");;
            $("#customer_name_filter").val('');
            $("#job_no_filter").val('');
            $("#ref_no_filter").val('');
            $("#pickup_between_filter").val('');
            $("#booking_between_filter").val('');
            $("#selected_driver").val(null).trigger("change");
            $("#filter_pickup_from_date").val('');
            $("#filter_pickup_to_date").val('');
            $("#filter_booking_from_date").val('');
            $("#filter_booking_to_date").val('');

            table.draw();
        });

    })
    
    function sendMail(is){
        const url = 'emailtemplate-details';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['book_id'] = is;
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
         if(response['status'] == 200){
             let formatted_customer_email = 'Dear, ' + response['data'][0].fname + '.<br><br>' +
                '\n\n<b>The Vehicle Details</b><br>' +
                '\nReg No: ' + response['data'][0].d_vehreg + '<br/>' +
                '\nV Model: ' + response['data'][0].model + '<br/>' +
                '\nV Make: ' + response['data'][0].brand + '<br/>' +
                '\nV Color: ' + response['data'][0].d_vehcol + '<br/>' +
                '\nV Type: ' + response['data'][0].v_name + '<br/><br/>' +
                '\n\n<b>The Driver Details</b><br>' +
                '\n\n\n\n<img src="" style="width:150px; height=175px; margin: 0px 5px 0px 0px;"> <br><br>' +
                '\n\nDriver Name: ' + response['data'][0].d_name + '<br/>' +
                '\nDriver PCO License No: ' + response['data'][0].d_pco_no + '<br/>' +
                '\nDriver Phone No: ' + response['data'][0].d_phone + '<br/>' +
                '\nPickup Date: ' + response['data'][0].pickup_date + '<br/>' +
                '\n\nPickup Time: ' + response['data'][0].pickup_time.substring(0, 5) + '<br/>' +
                '\n\n<h4>Contact us</h4>' +
                '\n2. Airport Rides is not responsible for lost or damaged luggage or any other items left in the vehicle during the time of service. Please check the vehicle before Exiting. Items left in the vehicle may require extra charges to be returned. <br>' +
                '\n3. Please note that the prices do not include gratuity and are up to the client\'s discretion to tip the Driver, preference in cash, or any currency. <br>' +
                '\n\n<h4>Pickup Instruction</h4>' +
                '<ul>'+
                    '\n<li><strong>Airport Pickup,</strong> The driver will monitor the flight, only go into the terminal 45 minutes after the plane lands, and will meet you with your name on the Board Sign at the arrivals point, located immediately after the customs exit.</li>'+
                    '\n<li><strong>Hotel Pickup,</strong> Please wait at the hotel lobby for collection and just let the concierge or reception desk that you are waiting for a private transfer our driver will aim to arrive 10 minutes early at the hotel and make contact with the concierge or reception desk. </li>'+
                    '\n<li><strong>Private Address,</strong> The driver will make contact by ringing the doorbell and will be waiting as close as possible to the front door at the set pickup time.</li>'+
                    '\n<li><strong>Meet & Greet Service,</strong> Includes 90 minutes of free waiting time from the flight arrival time, an additional charge will apply £8 for every 10 minutes plus any additional car park charges. The train station and cruise terminals are allowed a free waiting time of 30 minutes from the booking time afterward £8 for every 10 minutes plus any additional car park charges. Payable to the driver in cash at the end of the service.</li>'+
                    '\n<li><strong>Our standard cancellation policy,</strong> To make a cancellation for a booking up to 12 hours before the journey pickup time no refund, 24 hours before the journey would be a 50% refund, and 48 hours before the journey 100% refund. For the 16-55 Seater bus, we require a minimum of 14 days notice before the date for a 100% refund, 10 days before the journey would be a 50% refund, and 7 days before the journey pick-up day no refund.</li>'+
                '</ul>'+
                '\n\nBest Regards<br>' +
                '\n<b>Airport Rides</b> <br>';
            $('#emailForm').trigger("reset")
            $('#email-modal').modal('show')

            $('#customer_email').val(response['data'][0].email)
            $('#customer').val(response['data'][0].fname)
            tinymce.get("customer_email_body").setContent(formatted_customer_email)
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
    }
    
    function sendConfirmationEmail(is){
        Swal.fire({
                title: "Confirmation Email",
                text: "Are you sure want to send the confirmation email to customer?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willUpdate) => {
                if (willUpdate.isConfirmed) {
                    var formDataObject  = {};
                    formDataObject['token'] = getCookie('d_token');
                    formDataObject['device_id'] = 0;
                    formDataObject['book_id'] = is;
                    $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}bookconfirm-mail",
                        data: formDataObject,
                        beforeSend: function() {
                            $('body').append(loading_animation())
                        },
                        success: function(response) {
                            $('.loading-overlay').remove()
                            if (response.status == 200) {
                                showlist('all')
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    text: 'Email sent successfully.',
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                            } 
                            if(response.status == 400){
                                $('.loading-overlay').remove()
                                Swal.fire("Error",
                                    "Unable to send email now.",
                                    "error");
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
            })
    }
    
    function sendSMS(is){
        const url = 'template-details';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['book_id'] = is;
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
         if(response['status'] == 200){
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
    }
    
    function showlist(status){
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['order_status'] = status;
            var existingTable = $('#dash-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#dash-table', {
                processing: true,
                searching: false,
             ajax: {
                 url: '{{env('API_URL')}}bookinglist',
                 method: 'POST',
                 dataSrc:"data",
                 data: formDataObject,
             },
             createdRow: function (row, data, dataIndex) {
                 // Add a class to the <tr> element based on the o  rder_status value
                 $(row).addClass('col-md-6 mb-2 card db-standard');
             },
             columns: [
                 { data: null,
                 className: 'dt-table-1',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-arrow-up-9-1" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.job_no}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-2',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-plane-arrival" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_flight_num}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-3',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_date+' / '+row.pickup_time}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-4',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.booking_date}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.vehichle_name != null ? row.vehichle_name : '-'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.pickup_address}`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-14',
                 render: function(data,type,row){
                     return `<p class="border-dashed"></p>`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-8',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.dest_address}`;
                 }
                 },
                 {  data: null,
                 className: 'dt-table-9',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-file-invoice" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.payment_status != null ? row.payment_status : '-'}`;
                 }
                 },
                 { data: 'type',
                 className: 'dt-table-10',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-building-columns" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.type}`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-11',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : '-'}`;
                 }
                 },
                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<select class="form-control booking-status" onchange="changeBookStatus(${row.id})" id="book_status${row.id}" name="status">
                        <option value="Pending" ${row.order_status == 'Pending' ? 'selected' : ''}>Pending</option>
                        <option value="Confirmed" ${row.order_status == 'Confirmed' ? 'selected' : ''}>Confirmed</option>
                        <option value="Assigned" ${row.order_status == 'Assigned' ? 'selected' : ''}>Assigned</option>
                        <option value="Dispatched" ${row.order_status == 'Dispatched' ? 'selected' : ''}>Dispatched</option>
                        <option value="Completed" ${row.order_status == 'Completed' ? 'selected' : ''}>Completed</option>
                        <option value="Settled" ${row.order_status == 'Settled' ? 'selected' : ''}>Settled</option>
                        <option value="Canceled" ${row.order_status == 'Canceled' ? 'selected' : ''}>Cancelled</option>
                    </select>`;
                     }
                 },
                 {
                     data: null,
                     className: 'dt-table-13',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<ul>
                         <li  style="list-style-type: none;"><a href="/booking/edit/${row.id}" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a></li>
                         <li  style="list-style-type: none;"><a href="/booking-status-pdf/${row.id}" target="_blank" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a></li>
                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" title="Assign Driver" onclick="assigndriver(${row.id},'${row.job_no}','${row.total}')" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a><a href="javascript:void(0)" onclick="sendConfirmationEmail(${row.id})" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="removedriver(${row.id})" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a></li>
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendSMS"><i class="fa fa-paper-plane"></i></a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendMail(${row.id})" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         </ul>`;
                     }
                 }
             ],
            //  responsive: {
            //       details: {
            //           type: 'column',
            //           target: 'tr'
            //       }
            //   }
            });
         }
         
         function assigndriver(id,job,ttl){
            $('#assignDriverForm').trigger("reset");
            $("#driver_name").val('').trigger('change');
            $('#driver_name').empty()
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          var settings = {
         "url": "{{env('API_URL')}}driverlist",
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             let driver_options = '<option value="">-- select driver --</option>'
             response['data'].forEach(function(item) {
                 driver_options +=
                     `<option value="${item.id}">${item.name}</option>`
             })
             $('#driver_name').html(driver_options)
             $('#booking_id').val(id);
             $('#job_no').val(job);
             $('#total').val(ttl);
             $('#form-modal').modal('show');
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
      });
        }
        
        function removedriver(id){
            Swal.fire({
                title: "Driver Removal",
                text: "Are you sure want to remove the driver?.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willUpdate) => {
                if (willUpdate.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}assigndriver",
                        data: {
                            booking_id: id,
                            status: 'Confirmed',
                            token: getCookie('d_token'),
                            device_id: 0
                        },
                        success: function(response) {
                            if(response['status'] == 200){
                                Swal.fire({
                                  position: "top-right",
                                  icon: "success",
                                  title: response['message'],
                                  showConfirmButton: false,
                                  timer: 1500
                                }).then(function() {
                                 showlist('all')
                                });
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
            })
        }
        
        function changeBookStatus(id){
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to change status!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectstat = $('#book_status' + id).val();
                    var optionToRemoveSelected = $('#book_status' + id+' option[value="'+selectstat+'"]');
                    optionToRemoveSelected.removeAttr('selected');
                    var formDataObject  = {};
                     formDataObject['token'] = getCookie('d_token');
                     formDataObject['device_id'] = 0;
                     formDataObject['booking_id'] = id;
                     formDataObject['status'] = $('#book_status' + id).val();
                    $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}bookingstatus",
                        data: formDataObject,
                        success: function(response) {
                            if(response['status'] == 200){
                              Swal.fire({
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: true,
                                 timer: 2500
                                }).then(function() {
                                    showlist('all')
                                });
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
                        },
                        error: function(data) {
                            ('[data-id="' + booking_id + '"]').val(order_status)
                            console.log('Error:', data);
                        }
                    });
                } else {
                    $('[data-id="' + booking_id + '"]').val(order_status)
                }
            })
        }
    
    function bookingsCount(){
            const url = 'bookinglist';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['order_status'] = "all"
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
         if(response['status'] == 200){
             var dataArray = response['data'];
             var pendingOrders = dataArray.filter(function(item) {
                    return item.order_status === "Pending";
                 });
             var confirmedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Confirmed";
                 });
             var assignedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Assigned";
                 });
             var cancelledOrders = dataArray.filter(function(item) {
                    return item.order_status === "Canceled";
                 });
             var dispatchedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Dispatched";
                 });
             $('#ttl_book').html(animateCount(response['data'].length,'ttl_book'))
             $('#pending_book').html(animateCount(pendingOrders.length,'pending_book'))
             $('#confirmed_book').html(animateCount(confirmedOrders.length,'confirmed_book'))
             $('#assigned_book').html(animateCount(assignedOrders.length,'assigned_book'))
             $('#dispatched_book').html(animateCount(dispatchedOrders.length,'dispatched_book'))
             $('#cancelled_book').html(animateCount(cancelledOrders.length,'cancelled_book'))
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
        }
        
    //     function FleetList(){
    //     const url = 'vehichlelist';
    //         var formDataObject  = {};
    //         formDataObject['token'] = getCookie('d_token');
    //         formDataObject['device_id'] = 0;
    //     var settings = {
    //      "url": "{{env('API_URL')}}"+url,
    //      "method": "POST",
    //      "timeout": 0,
    //      "headers": {
    //          "Content-Type": "application/json"
    //       },
    //      "data": JSON.stringify(formDataObject),
    //   };
    //   $.ajax(settings).done(function (response) {
    //      if(response['status'] == 200){
    //          var list = `<h3 class="hd-name">Car Fleets</h3>
    //                  <div class="owl-carousel owl-theme" id="fleet_corousel">`;
    //          for(i=0; i < response['data'].length; i++){
    //              file(response['data'][i], i, function(imageData, index) {
    //              list += `<div class="item card">
    //                     <div class="tent">
    //                     <ul class="ed-ul">
    //                         <button class="ed-icon" onclick="editvehichle(${response['data'][index].id})">
    //                           <i class="fa-solid fa-pen-to-square" style="color: #fff;"></i> 
    //                         </button>
    //                 <button class="del-icon" onclick="deletefleet(${response['data'][index].id})"><i class="fa-solid fa-trash" style="color: #fff;"></i></button>
    //                     </ul>
    //                 </div>
    //                      <div class="img-cd">
    //                         ${imageData}
    //                         <h3 class="car-name text-center">${response['data'][index]['name'].toUpperCase()}</h3>
    //                      </div>
    //                     </div>`;
            
    //          list += `</div>`;
    //         //  console.log(list);
    //         $('#fleet_container').html(list)
    //              });
                  
    //          }
    //          var owl = $(".owl-carousel");
    //               owl.owlCarousel({
    //                 items: 3,
    //                 margin: 10,
    //                 loop: true,
    //                 nav: true,
    //                 dots: true,
    //                 autoplay:true,
    //                 responsive: {
    //                     0: {
    //                       items: 1
    //                     },
    //                     748: {
    //                       items: 3
    //                     },
    //                     1280: {
    //                       items: 3
    //                     }
    //                   }
    //               });
    //               $('.owl-dots').css('display','block')
             
            
    //      }
    //      if(response['status'] == 400){
    //         errornotify(response)
    //      }
    //      if(response['status'] == 500){
    //         warningClick('Error',response['error'],"danger")
    //      }
    //      if(response['status'] == 401){
    //         unauth()
    //      }
    //   });
    //     }
    function FleetList(){
    const url = 'vehichlelist';
    var formDataObject  = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
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
        if(response['status'] == 200){
            var list = `<h3 class="hd-name">Car Fleets</h3>
                    <div class="owl-carousel owl-theme" id="fleet_corousel">`;

            // Array to store all promises
            var promises = [];

            for(let i=0; i < response['data'].length; i++){
                let promise = new Promise((resolve, reject) => {
                    file(response['data'][i], i, function(imageData, index) {
                        let item = `<div class="item card">
                                <div class="tent">
                                    <ul class="ed-ul">
                                        <button class="ed-icon" onclick="editvehichle(${response['data'][index].id})">
                                            <i class="fa-solid fa-pen-to-square" style="color: #fff;"></i> 
                                        </button>
                                        <button class="del-icon" onclick="deletefleet(${response['data'][index].id})">
                                            <i class="fa-solid fa-trash" style="color: #fff;"></i>
                                        </button>
                                    </ul>
                                </div>
                                <div class="img-cd">
                                    ${imageData}
                                    <h3 class="car-name text-center">${response['data'][index]['name'].toUpperCase()}</h3>
                                </div>
                            </div>`;

                        resolve(item);
                    });
                });
                promises.push(promise);
            }

            Promise.all(promises).then((values) => {
                list += values.join('');

                list += `</div>`;
                $('#fleet_container').html(list);

                var owl = $(".owl-carousel");
                owl.owlCarousel({
                    items: 3,
                    margin: 10,
                    loop: true,
                    nav: true,
                    dots: true,
                    autoplay:true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        748: {
                            items: 3
                        },
                        1280: {
                            items: 3
                        }
                    }
                });
                $('.owl-dots').css('display','block');
            });
        }
        if(response['status'] == 400){
            errornotify(response);
        }
        if(response['status'] == 500){
            warningClick('Error',response['error'],"danger");
        }
        if(response['status'] == 401){
            unauth();
        }
    });
}

    
    function driverlist(){
        var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          var settings = {
         "url": "{{env('API_URL')}}driverlist",
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             let driver_options = '<option value="">-- select driver --</option>'
             response['data'].forEach(function(item) {
                 driver_options +=
                     `<option value="${item.id}">${item.name}</option>`
             })
             $('#driver_name_filter').html(driver_options)
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
      });
    }
    
    function editvehichle(id){
            const url = 'editvehichle';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
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
         if(response['status'] == 200){
             $('.modal-title').html('Edit Fleet')
              AssignValues(response)
              $('#vehichle_form-modal').modal('show')
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
      });
        }
        
        function deletefleet(id){
            const url = 'deletevehichle';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
          var settings = {
         "url": "{{env('API_URL')}}"+url,
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
                   if(response['status'] == 200){
                       $("#fleet_create_form")[0].reset();
                       $('.modal-title').html('Add Fleet')
                       $('#flfrm_dis').click()
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              FleetList()
                          });
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
                  });
               
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
             }
         });
        }
        
        function AssignValues(response){
              $('#veh_fleet_id').val(response['data'].id);
              brands(response['data'].brand_id,'veh_brand_id')
              models(response['data'].brand_id,response['data'].model_id,'veh_model_id')
              $('#veh_name').val(response['data'].name);
              $('#veh_passenger').val(response['data'].passenger);
              $('#veh_no_of_seats').val(response['data'].no_of_seats);
              $('#veh_min').val(response['data'].min);
              $('#veh_max').val(response['data'].max);
              $('#veh_luggage').val(response['data'].luggage);
              $('#veh_hand_luggage').val(response['data'].hand_luggage);
              $('#veh_child').val(response['data'].child);
              $('#veh_booster').val(response['data'].booster);
              $('#veh_order').val(response['data'].order);
        }
        
        function reset(){
           $("#fleet_create_form")[0].reset();
           $('#vehichle_form-modal').modal('hide')
          brands('','brand_id')
          $('#fleet_id').val('')
          $('#model_id').html('<option value="">Select</option>')
          $('.modal-title').html('Add Fleet')
      }
    
    function animateCount(targetCount,ref_id) {
    var currentCount = 0;
    var countDisplay = $("#"+ref_id);

    function updateCount() {
        countDisplay.text(currentCount);
    }

    $({ count: currentCount }).animate({ count: targetCount }, {
        duration: 1000, // Animation duration in milliseconds
        step: function() {
            currentCount = Math.ceil(this.count);
            updateCount();
        },
        complete: function() {
            currentCount = targetCount;
            updateCount();
        }
    });
}

//prasanth show file

    function file(data, index, callback) {
    var settings = {
  "url": "{{env('API_URL')}}showfile",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json"
  },
  "data": JSON.stringify({
    "image": data.upload_photo
  }),
};

    $.ajax(settings).done(function(response) {
        var imageData = `<img class="img-flx" src="data:image/png;base64,${response}" alt="Displayed Image" style="width: 177px; height:201px;">`;
        if (callback && typeof callback === "function") {
            callback(imageData, index);
        }
    });
}

    function AssignModal_ShowErrors(errors) {
        if (errors.charges) {
            $('.invalid-charges').text(errors.charges);
        }
        if (errors.driver_amount) {
            $('.invalid-driver_amount').text(errors.driver_amount);
        }
        if (errors.driver_id) {
            $('.invalid-driver_name').text(errors.driver_id);
        }
    }

    function AssignModal_ResetErrors() {
        $('.invalid-charges, .invalid-driver_name, .invalid-driver_amount').text('')
    }

    function loading_animation(){
        return `<div class="loading-overlay d-flex align-items-center justify-content-center">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-success" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-info" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>`
    }


    @if (session('booking_details_update'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('booking_details_update') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('booking_details_update');
        @endphp
    @endif

    @if (session('booking_status_update'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('booking_status_update') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('booking_status_update');
        @endphp
    @endif
</script>
