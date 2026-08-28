<script>
    $(function() {

        driverlist()
        filterReset()
        
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
        
        $('#book_reset').on('click', function(){
            filterReset()
        })
        
        function filterReset(){
            $('#book_filter_form')[0].reset()
            $('#pickup_date_from').val('')
            $('#pickup_date_to').val('')
            $('#booking_date_from').val('')
            $('#booking_date_to').val('')
            showlist()
        }
        
        $('#book_search').on('click', function(){
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
            //  console.log(formDataObject)
                 // Destroy the existing DataTable before reinitializing
                var existingTable = $('#book-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#book-table', {
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
                 {
                    data: null,
                    className: 'dt-table-3',
                    render: function(data, type, row) {
                        // Convert pickup_date to a Date object
                        let date = new Date(row.pickup_date);
                        
                        // Extract year, month (short format), and day
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
                
                        // Format the date as "YYYY-MMM-DD"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate} / ${row.pickup_time}`;
                    }
                },
                 {
                    data: null,
                    className: 'dt-table-4',
                    render: function(data, type, row) {
                        // Convert booking_date to a Date object
                        let date = new Date(row.booking_date);
                
                        // Extract day, month (short format), and year
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = date.getDate(); // Get day as a number
                
                        // Format as "DD-MMM-YYYY"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate}`;
                    }
                },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-person-walking-luggage" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.car_type != null ? row.car_type : '-'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.from}`;
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
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.to}`;
                 }
                 },
                 {
  data: null,
  className: 'dt-table-9',
  render: function(data,type,row){
    return `
      <div>
        <i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-file-invoice" style="font-weight: 00;margin-left: 7px;color: #114462;"></i> ${row.payment_status != null ? row.payment_status : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-building-columns" style="font-weight: 00;margin-left: 5px;color: #114462;"></i> ${row.type ? row.type : 'N/A'}
      </div>
    `;
  }
},
                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                         let orderStatus = row.order_status;
                        if (orderStatus == null || orderStatus == 0 || orderStatus == undefined) {
                            orderStatus = 'Cancelled';  
                        }
                         if (orderStatus == 'Pending') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Confirmed" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Pending</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Confirmed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Assigned" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Confirmed</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Assigned') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Dispatched" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Assigned</button>`;
                            
                         }  
                        if (orderStatus == 'Completed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}"  name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Completed</button>`;
                            
                         }  
                        if (orderStatus == 'Dispatched') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Completed" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Dispatched</button>`;
                            
                         } 
                         if (orderStatus == 'Cancelled') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Cancelled" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }
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
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS"` : ``}
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

        //Select2 driver
        $('#driver_name, #driver_name_filter').select2()

        //Date range picker variables
        const start = moment('2015-01-01');
        const end = moment();

        //Date range picker for pickup dates
        function pickup_callback(start, end) {
            $('#pickup_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#pickup_between_filter').daterangepicker({}, pickup_callback)
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

        $('#booking_between_filter').daterangepicker({}, booking_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        booking_callback(start, end);

        //Assign driver
        // $('#saveBtn').click(function(e) {
        //     e.preventDefault();
        //     var formdata = $('#assignDriverForm').serialize();
        //     var pairs = formdata.split('&');
        //     var formDataObject  = {};
            
        //     for (var i = 0; i < pairs.length; i++) {
        //       var pair = pairs[i].split('=');
        //       var key = decodeURIComponent(pair[0]);
        //       var value = decodeURIComponent(pair[1]);
        //       formDataObject[key] = value;
        //     }
        //     formDataObject['token'] = getCookie('d_token');
        //     formDataObject['device_id'] = 0;
        //     console.log(formDataObject)
        //     $.ajax({
        //         data: formDataObject,
        //         url: "{{env('API_URL')}}assigndriver",
        //         type: "POST",
        //         dataType: 'json',
        //         success: function(response) {
        //             if(response['status'] == 200){
        //                 $('#form-modal').modal('hide');
        //              Swal.fire({
        //                         position: "top-right",
        //                         icon: "success",
        //                         title: response['message'],
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     }).then(function() {
        //                      showlist()
        //                  });
        //               }
        //           if(response['status'] == 400){
        //              errornotify(response)
        //           }
        //           if(response['status'] == 500){
        //              warningClick('Error',response['error'],"danger")
        //           }
        //           if(response['status'] == 401){
        //              unauth()
        //           }
        //         },
        //         error: function(data) {
        //             console.log('Error:', data);
        //         }
        //     });
        // });
        
        $('#saveBtn').click(function(e) {
            e.preventDefault();
            var bookingTotalAmount = $('#total').val();
            var driverAmount = $('#driver_amount').val();
            if (parseFloat(bookingTotalAmount) >= parseFloat(driverAmount)) {
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
            console.log(formDataObject)
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
                             showlist()
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
            }else{
                
                 // Show error if driver amount is not valid
             Swal.fire({
                    position: "top-right",
                    icon: "error",
                    title: "Driver amount must be equal to or less than the actual amount.",
                    showConfirmButton: false,
                    timer: 1500
                });
            }  
            
        });

        //SMS Modal
        $('body').on('click', '.sendSMS', function() {
            let booking_id = $(this).data('id')
            let form_data = $('#' + booking_id).serializeArray()
            let data_obj = {}
            let seat_name_1 = ''
            let seat_name_2 = ''
            let seat_name_3 = ''
            let job_no = $(this).data('jobid')
            let base_url = '{{ url('') }}'

            console.log(base_url)

            form_data.forEach(array_make)

            function array_make(item, index) {
                data_obj[item.name] = data_obj[item.name] || [];
                data_obj[item.name] = item.value;
            }

            if (data_obj.b_seat1 === 'Forward Facing') {
                seat_name_1 = 'FF'
            } else if (data_obj.b_seat1 === 'Rear Facing') {
                seat_name_1 = 'RF'
            } else if (data_obj.b_seat1 === 'Booster') {
                seat_name_1 = 'Booster'
            } else {
                seat_name_1 = ''
            }

            if (data_obj.b_seat2 === 'Forward Facing') {
                seat_name_2 = 'FF'
            } else if (data_obj.b_seat2 === 'Rear Facing') {
                seat_name_2 = 'RF'
            } else if (data_obj.b_seat2 === 'Booster') {
                seat_name_2 = 'Booster'
            } else {
                seat_name_2 = ''
            }

            if (data_obj.b_seat3 === 'Forward Facing') {
                seat_name_3 = 'FF'
            } else if (data_obj.b_seat3 === 'Rear Facing') {
                seat_name_3 = 'RF'
            } else if (data_obj.b_seat3 === 'Booster') {
                seat_name_3 = 'Booster'
            } else {
                seat_name_3 = ''
            }

            let baby_seat_text = seat_name_1 ? seat_name_1 : ''
            baby_seat_text += seat_name_2 ? ', ' + seat_name_2 : ''
            baby_seat_text += seat_name_3 ? ', ' + seat_name_3 : ''


            let formatted_customer_message = 'Hi, ' + data_obj.c_name +
                '\nyour driver\'s details, your driver is on its way.' +
                '\nReg No: ' + data_obj.v_reg_no +
                '\nV Model: ' + data_obj.v_model +
                '\nV Make: ' + data_obj.v_make +
                '\nV Color: ' + data_obj.v_color +
                '\nV Type: ' + data_obj.v_type +
                '\nD Name: ' + data_obj.d_name +
                '\nD License No: ' + data_obj.d_lic_no +
                '\nD Phone: ' + data_obj.d_phone +
                '\nP up Date: ' + data_obj.b_date +
                '\nP up Time: ' + data_obj.b_time.substring(0, 5) +
                '\nD Photo: ' + base_url + '/my-driver/' + job_no +
                '\nAirport Rides.'

            // let formatted_driver_message = 'Name: ' + data_obj.c_name + ', ' + data_obj.c_phone +
            //     baby_seat_text +
            //     '\n\nD/T: ' + data_obj.b_date + ', ' + data_obj.b_time.substring(0, 5) +
            //     '\n\nFrom: ' + data_obj.b_from +
            //     '\nTo: ' + data_obj.b_to +
            //     fligt_details +
            //     '\n\nVehicle: ' + data_obj.v_type +
            //     '\nPayment: ' + data_obj.b_amount + ' ' + data_obj.b_pay_type

            let formatted_driver_message = data_obj.b_date + ', ' + data_obj.b_time.substring(0, 5) + ', '
            + data_obj.c_phone + '.' +
            '\nFrom: ' + data_obj.b_from +
            '\nA F/C: ' + data_obj.b_fc_from +
            '\nVia: ' + data_obj.b_via_points +
            '\nTo: ' + data_obj.b_to +
            '\nD F/C: ' + data_obj.b_fc_to +
            '\nNo. P: ' + data_obj.b_passengers +
            '\nNo. L: ' + data_obj.b_luggage +
            '\nNo. HL: ' + data_obj.b_hand_luggage +
            '\nCC: ' + baby_seat_text +
            '\nV Type: ' + data_obj.v_type +
            '\nPayment: ' + data_obj.b_amount
            '\nDR: ' + data_obj.d_remarks

            $('#smsForm').trigger("reset")
            $('#sms-modal').modal('show')

            $('#customer_no').val(data_obj.c_phone)
            $('#driver_no').val(data_obj.d_phone)
            $('#customer_message').val(formatted_customer_message)
            $('#driver_message').val(formatted_driver_message)
        });


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
            console.log(formDataObject);
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
                                showlist()
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
    
    
        function changeBookStatus(id, element){
            if ($(element).data('btn_name') == "Confirmed") {
                window.open('/booking/edit/' + id, '_blank');
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to change status!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formDataObject  = {};
                     formDataObject['token'] = getCookie('d_token');
                     formDataObject['device_id'] = 0;
                     formDataObject['booking_id'] = id;
                    //  formDataObject['status'] = $('#book_status'+id).val();
                    formDataObject['status'] = $(element).data('btn_name');
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
                                    showlist()
                                });
                            }
                            if(response['status'] == 400){
                               warningClick('Error',response['error'],"danger")
                            }
                            if(response['status'] == 500){
                               warningClick('Error',response['error'],"danger")
                            }
                            if(response['status'] == 401){
                               unauth();
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
    
        function showlist(status){
            var key = window.location.href;
            var segments = key.split('/');
            var lastSegment = segments.pop();
            $('#card_title').html(lastSegment+' Booking List')
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['order_status'] = status || 'All';
            var existingTable = $('#book-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#book-table', {
                processing: true,
                searching: false,
             ajax: {
                 url: '{{env('API_URL')}}bookinglist',
                 method: 'POST',
                 dataSrc:"data",
                 data: formDataObject,
             },
             createdRow: function (row, data, dataIndex) {
                 // Add a class to the <tr> element based on the order_status value
                 $(row).addClass( `col-md-6 mb-2 card db-standard ${data.order_status}`);
             },
             columns: [
                 { data: null,
                 className: 'dt-table-1',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-arrow-up-9-1" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.job_no ? row.job_no : 'N/A'}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-2',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-plane" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_flight_num ? row.pickup_flight_num : 'N/A'}`
                 }
                 },
                 {
                    data: null,
                    className: 'dt-table-3',
                    render: function(data, type, row) {
                        // Convert pickup_date to a Date object
                        let date = new Date(row.pickup_date);
                        
                        // Extract year, month (short format), and day
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
                
                        // Format the date as "YYYY-MMM-DD"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate} / ${row.pickup_time}`;
                    }
                },
                 {
                    data: null,
                    className: 'dt-table-4',
                    render: function(data, type, row) {
                        // Convert booking_date to a Date object
                        let date = new Date(row.booking_date);
                
                        // Extract day, month (short format), and year
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = date.getDate(); // Get day as a number
                
                        // Format as "DD-MMM-YYYY"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate}`;
                    }
                },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-person-walking-luggage" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.car_type != null ? row.car_type : '-'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.from}`;
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
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.to}`;
                 }
                 },
                 {
  data: null,
  className: 'dt-table-9',
  render: function(data,type,row){
    return `
      <div>
        <i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-file-invoice" style="font-weight: 00;margin-left: 7px;color: #114462;"></i> ${row.payment_status != null ? row.payment_status : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-building-columns" style="font-weight: 00;margin-left: 5px;color: #114462;"></i> ${row.type ? row.type : 'N/A'}
      </div>
    `;
  }
},
                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                         let orderStatus = row.order_status;
                        if (orderStatus == null || orderStatus == 0 || orderStatus == undefined) {
                            orderStatus = 'Cancelled';  
                        }
                         if (orderStatus == 'Pending') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Confirmed" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Pending</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Confirmed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Assigned" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Confirmed</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Assigned') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Dispatched" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Assigned</button>`;
                            
                         }  
                        if (orderStatus == 'Completed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}"  name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Completed</button>`;
                            
                         }  
                        if (orderStatus == 'Dispatched') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Completed" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Dispatched</button>`;
                            
                         } 
                         if (orderStatus == 'Cancelled') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Cancelled" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }
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
                         
                         <li style="list-style-type: none;"><a href="/booking/preview/${row.id}?d_token=${getCookie('d_token')}" target="_blank" title="Preview Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success previewItem"><i class="fa fa-eye"></i></a></li>
                         
                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" title="Assign Driver" onclick="assigndriver(${row.id},'${row.job_no}','${row.total}')" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a></li>` : ``}
                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendConfirmationEmail(${row.id})" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}

                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="removedriver(${row.id})" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a></li>
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS"</a></li>` : ``}
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
    
    function driverlist(){
        var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['status'] = 'Active';
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
                                 showlist()
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




</script>
