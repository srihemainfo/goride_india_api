<script>
const c_symbol = @json($mySymbol);
let invoice_data = '';

    $(function () {
        // let savedJobNos = [];
        // invoice_data = getCookie('invoice_booking') != '' ? JSON.parse(getCookie('invoice_booking')) : '';
        
        $.ajaxSetup({

            headers: { 

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });



        //Date range picker

        const start = moment('2015-01-01');

        const end = moment();



        function cb(start, end) {

            $('#booking_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        }



        $('#booking_between_filter').daterangepicker({}, cb)

        .on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

        })

        .on('cancel.daterangepicker', function(ev, picker) {

            $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        });



        cb(start, end);

        function getCurrencySymbol(currencyCode) {
        const currencySymbols = {
            "INR": "₹", // Indian Rupee
            "USD": "$", // US Dollar
            "GBP": "£", // British Pound
            "EUR": "€", // Euro
            "JPY": "¥", // Japanese Yen
            "AUD": "A$", // Australian Dollar
            "CAD": "C$", // Canadian Dollar
            "CHF": "Fr", // Swiss Franc
            "CNY": "¥", // Chinese Yuan
            "RUB": "₽", // Russian Ruble
            // Add more currencies as needed
        };

        // Convert the currencyCode to uppercase to make it case-insensitive
        const upperCurrencyCode = currencyCode.toUpperCase();

        // Return the symbol, or return the currency code if the symbol is not found
        return currencySymbols[upperCurrencyCode] || upperCurrencyCode;
    }

        //Change the value of date range filter

        $("#booking_between_filter").change(function(){

            let selected_date = $(this).val()

            let date_array = selected_date.split(" ")



            let from_date = moment(date_array[0]).format('YYYY-MM-DD');

            let to_date = moment(date_array[2]).format('YYYY-MM-DD');



            $('#filter_from_date').val(from_date)

            $('#filter_to_date').val(to_date)

        });

        

        $("#payment_search").click(function () {

            // alert('king');

            var formDataObject = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0;

            let token =formDataObject['token'];

            let device_id =formDataObject['device_id'];

            let customer_id = $('#client_name_filter').val();

            // console.log(customer_id);

            formDataObject['cus_id'] = customer_id;

            let job_no = $('#job_no_filter').val();

            // console.log(job_no);

            formDataObject['job_no'] = job_no;

            let from_date = $('#filter_from_date').val();

            let to_date = $('#filter_to_date').val();

            const url = 'GetBookingForInvoice';

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

        //   console.log(response.booking_details);
        //   console.log("Jana",response.currency);
          

         if(response.booking_details != ''){

            //  alert('success');

            $('#booking_table').show();

            $('tbody').html("");

            let symbol = getCurrencySymbol(c_symbol);

            MakeInvoiceTable(response.booking_details,symbol)

      }

      });

        });



        //generate invoice

        // $("#generateInvoice").click(function (e) {

        //     e.preventDefault();

        //     const select_booking = [];



        //     $.each($("#data-table input[type=checkbox]:checked"), function(){

        //         select_booking.push($(this).val());

        //     });

            

        //     // console.log("working...", select_booking);

        //     $.ajax({

        //         url: "{{env('API_URL')}}GenerateInvoice",

        //         type: 'POST',

        //         data: {

        //             selected_booking: select_booking

        //         },

        //         dataType: 'json',

        //         success:function(response) {

        //             console.log(response);

        //             if(response){

        //                 $('#booking_table').hide();

        //                 $('#invoice_view').show();

        //                 $('tbody').html("");

        //                 MakeInvoiceView(response.invoice_details,response.invoice_totals,response.new_invoice_no,response.driver_name,response.user_id)

        //             }

        //             else{

        //                 //$('#booking_table').hide();

        //             }

        //         },

        //         error: function (data) {

        //               console.log('Error:', data);

        //         }

            

        //      });

        // });

        

        $("#generateInvoice").click(function (e) {

            e.preventDefault();

            const select_booking = [];



            $.each($("#data-table input[type=checkbox]:checked"), function(){

                select_booking.push($(this).val());

            });

            var formDataObject = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0; 

            formDataObject['selected_booking'] = select_booking;

            const url = 'GenerateInvoice';

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
            //   alert(response.currency); 

                if(response){

                    $('#booking_table').hide();

                    $('#invoice_view').show();

                    $('tbody').html("");

                    let symbol = getCurrencySymbol(response.currency);
                    // alert(symbol);

                    MakeInvoiceView(response.invoice_details,response.invoice_totals,response.new_invoice_no,response.driver_name,response.user_id, response.driver_address, symbol)

                }

            });

        });



        //generated invoice

        // $("#generatedInvoice").click(function (e) {

            

        //     e.preventDefault();

            

        //     let driver_name = $('#driver_name').val();

        //     let driver_id = $('#driver_id').val();

        //     let invoice_no = $('#invoice_no').val();

        //     let driver_address = $('#driver_address').val();

        //     let invoice_date = $('#invoice_date').val();

        //     let payment_type = $('#payment_type').val();

        //     let status = $('#status').val();

        //     let pickup_date = $('#pickup_date').val();

        //     let pickup_time = $('#pickup_time').val();

        //     let total = $('#total').val();

        //     let selected_jobs = [];

        //     $.each($("input[name='selected_jobs[]']"), function(){

        //         selected_jobs.push($(this).val());

        //     });

        //     selected_jobs_str = selected_jobs.toString();



        //     let tax_per = 20.00;

        //     let tax_amt = (20/100)*total;

        //     let net = parseFloat(tax_amt) + parseFloat(total);

        //     let date_time = pickup_date + " " + pickup_time;

        //     //alert(date_time);

            

        //     $.ajax({

        //         url: "{{ route('StoreInvoice') }}",

        //         type: 'POST',

        //         data: {

        //             driver_name: driver_name,

        //             driver_id: driver_id,

        //             invoice_no: invoice_no,

        //             driver_address: driver_address,

        //             invoice_date: invoice_date,

        //             payment_type: payment_type,

        //             status: status,

        //             selected_jobs_str: selected_jobs_str,

        //             selected_jobs: selected_jobs,

        //             total: total,

        //             tax_per: tax_per,

        //             tax_amt: tax_amt,

        //             net: net,

        //             date_time: date_time,

                    

        //         },

        //         dataType: 'json',

        //         success:function(response) {

        //             ResetErrors();

        //             if(response.status == 400 && response.errors){

        //                 console.log('in error');

        //                 ShowErrors(response.errors)

        //             }



        //             if(response.status == 400 && !response.errors){

                        

        //                 Swal.fire("Error", "Add or Update failed", "error");

        //             }



        //             if(response.status == 200){

        //                 window.location = response.redirect_url

        //             }

                    	

        //         },

        //         error: function (data) {

        //             console.log('Error:', data);

        //         }

            

        //      });

        // });

        

        $("#generatedInvoice").click(function (e) { 

            e.preventDefault();

            

            var formDataObject = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0; 

            formDataObject['driver_name'] = $('#driver_name').val();

            formDataObject['driver_id'] = $('#driver_id').val();

            formDataObject['invoice_no'] = $('#invoice_no').val();

            formDataObject['driver_address'] = $('#driver_address').val();

            formDataObject['invoice_date'] = $('#invoice_date').val();

            formDataObject['payment_type'] = $('#payment_type').val();

            formDataObject['status'] = $('#status').val();

            let pickup_date = $('#pickup_date').val();

            formDataObject['pickup_date'] = pickup_date;

            let pickup_time = $('#pickup_time').val();

            formDataObject['pickup_time'] = pickup_time;

            let total = $('#total').val();

            formDataObject['total'] = total;

            let selected_jobs = [];

            $.each($("input[name='selected_jobs[]']"), function(){

                selected_jobs.push($(this).val());

            });

            selected_jobs_str = selected_jobs.toString();

            formDataObject['selected_jobs_str'] = selected_jobs_str;

            formDataObject['tax_per'] = 20.00;

            let tax_amt = (20/100)*total;

            formDataObject['tax_amt'] = tax_amt

            formDataObject['net'] = parseFloat(tax_amt) + parseFloat(total);

            formDataObject['date_time'] = pickup_date + " " + pickup_time;

            

            const url = 'StoreInvoice';

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

                if(response){

                   ResetErrors();

                    if(response.status == 400 && response.errors){

                        console.log('in error');

                        ShowErrors(response.errors)

                    }



                    if(response.status == 400 && !response.errors){

                        

                        Swal.fire("Error", "Add or Update failed", "error");

                    }



                    if(response.status == 200){

                        // alert(response);

                        window.location = response.redirect_url

                    }

                }

            });

        });



        //Change the value of filter

        $("#client_name_filter").change(function(){

                $("#name_filter").val($(this).val());

        });



        $("#job_no_filter").change(function(){

                $("#job_filter").val($(this).val());

        });

        

         //Select2 AJAX search for Drivers

        //  $('#client_name_filter').select2({

        //      var formDataObject = {};

        //     formDataObject['token'] = getCookie('d_token');

        //     formDataObject['device_id'] = 0;

        //     console.log(formDataObject);

        //      const url = 'GetClientNames';

        //      var settings = {

        //          "url": "{{env('API_URL')}}"+url,

        //          "method": "POST",

        //          "timeout": 0,

        //          "headers": {

        //              "Content-Type": "application/json"

        //           },

        //         data: function (params) {

        //             return {

        //             search: params.term // search term

        //             };

        //         },

        //       }; 

        //      $.ajax(settings).done(function (response) {

        //          console.log(response);

        //      if(response['status'] == 200){

        //          $('.modal-title').html('Edit Fleet')

        //           AssignValues(response)

        //           $('#addFleet').click()

        //          }

        //      if(response['status'] == 400){

        //          warningClick('Error',response['message'],"danger")

        //      }

        //      if(response['status'] == 500){

        //         warningClick('Error',response['error'],"danger")

        //      }

        //      if(response['status'] == 401){

        //         unauth()

        //      }

        //     });

        //     // ajax: { 

        //     //     url: "{{route('GetClientNames')}}",

        //     //     type: "post",

        //     //     dataType: 'json',

        //     //     delay: 400,

        //     //     data: function (params) {

        //     //         return {

        //     //         search: params.term // search term

        //     //         };

        //     //     },

        //     //     processResults: function (response) {

        //     //         return {

        //     //         results: response

        //     //         };

        //     //     },

        //     //     cache: true

        //     // }

        // })

        

        $('#client_name_filter').select2({

          ajax: {

            "url": "{{env('API_URL')}}GetClientNames",

            method: 'POST',

            headers: {

              'Content-Type': 'application/json'

            },

            data: function (params) {

              return JSON.stringify({

                search: params.term,

                token: getCookie('d_token'),

                device_id: 0

              });

            },

            processResults: function (data) {

                console.log(data);

              // Process the data returned from the server

              // and return it in the format expected by Select2

              return {

                results: data

              };

            }

          }

        });

        



        //Select2 AJAX search for Jobs

        // $('#job_no_filter').select2({

        //     ajax: { 

        //         url: "{{route('GetJobNos')}}",

        //         type: "post",

        //         dataType: 'json',

        //         delay: 400,

        //         data: function (params) {

        //             return {

        //             search: params.term // search term

        //             };

        //         },

        //         processResults: function (response) {

        //             return {

        //             results: response

        //             };

        //         },

        //         cache: true

        //     }

        // })

        

        

        $('#job_no_filter').select2({

          ajax: {

            "url": "{{env('API_URL')}}GetJobNos",

            method: 'POST',

            headers: {

              'Content-Type': 'application/json'

            },

            data: function (params) {

              return JSON.stringify({

                search: params.term,

                token: getCookie('d_token'),

                device_id: 0

              });

            },

            processResults: function (data) {

              return {

                results: data

              };

            }

          }

        });
        
        // if(invoice_data){
            
        //     $('#job_no_filter').trigger('click');

        //     savedJobNos = [invoice_data.job_no];
            
        //     if (savedJobNos.length > 0) {
        //         savedJobNos.forEach(jobNo => {
        //             let optionExists = $('#job_no_filter option[value="' + jobNo + '"]').length > 0;
                    
        //             if (!optionExists) {
                        
        //                 $("#job_no_filter").append('<option value="' + jobNo + '">' + jobNo + '</option>');
        //                 $('#job_no_filter').val(savedJobNos).trigger('change');
        //             }
                    
        //         });
        //     }
        //     $('#payment_search').trigger('click');
        //     deleteCookie('invoice_booking');
            
        // }



        //Reset filter values

        $('#reset_filter').click(function(){

            $("#client_name_filter").val(null).trigger("change");

            $("#name_filter").val(null).trigger("change");

            $("#filter_from_date").val('');

            $("#filter_to_date").val('');

            cb(start, end);

            $("#job_no_filter").val(null).trigger("change");

            $("#job_filter").val(null).trigger("change");

            $('#booking_table').hide();

            $('#invoice_view').hide();

        });

    })



    function MakeInvoiceTable(booking_details,currency) {
        // alert(currency);
        $.each(booking_details, function (key, item) {
            $('#booking_view').append(`
                <tr>
                    <td><input type="checkbox" name="select_booking[]" value="${item.job_no}"></td>
                    <td>${item.job_no}</td>
                    <td>${item.user_id}</td>
                    <td>${item.pickup_date}</td>
                    <td>${item.fname}</td>
                    <td>${item.type}</td>
                    <td>${item.payment_status}</td>
                    <td>${item.order_status}</td>
                    <td>${currency} ${item.total}</td>
                </tr>
            `);
        });
    }






   function MakeInvoiceView(invoice_details, invoice_totals, new_invoice_no, driver_name, user_id, driver_address, currency) {
        $.each(invoice_details, function (key, item) {
            let tax = item.taxpercentvalue ? item.taxpercentvalue : 0;
    
            $('#inv_view').append(`
                <tr>
                    <td></td>
                    <td id="job_list">
                        <input type="hidden" name="selected_jobs[]" value="${item.job_no}">
                        ${item.job_no}
                    </td>
                    <td>
                        <input type="hidden" name="pickup_date" id="pickup_date" value="${item.pickup_date}">
                        ${item.pickup_date}
                        <input type="hidden" name="pickup_time" id="pickup_time" value="${item.pickup_time}">
                        ${item.pickup_time}
                    </td>
                    <td>
                        <b>Passenger Name:</b> ${item.fname}<br>
                        <b>From </b>${item.from} <br><b>To </b> ${item.to}<br>
                        <b>Car-</b> ${item.car_type}<br>
                        <b>Extra:</b> ${item.extracontent??0}
                    </td>
                    <td class="text-right">${currency} ${item.total}</td>
                    <td class="text-right">${currency} ${item.total}</td>
                </tr>
            `);
        });
    
        $('#inv_view').append(`
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Total</b></td>
                <td class="text-right">
                    <input type="hidden" name="total" id="total" value="${invoice_totals}">
                    ${invoice_totals}
                </td>
            </tr>
        `);
        
        let today = new Date().toISOString().split('T')[0];
        document.getElementById("invoice_date").value = today;
        
        $('#driver_name').val(driver_name);
        $('#driver_address').val(driver_address);
        $('#driver_id').val(user_id);
        $('#invoice_no').val(new_invoice_no);
    }


    

    function ResetErrors(){

        $('.invalid-name, .invalid-no, .invalid-driver-address, .invalid-invoice-date, .invalid-payment-type, .invalid-status').text('');

    }



    function ShowErrors(errors){

        if(errors.name){

            $('.invalid-name').text(errors.name);

        }

        if(errors.invoice_no){

            $('.invalid-no').text(errors.invoice_no);

        }

        if(errors.driver_address){

            $('.invalid-driver-address').text(errors.driver_address);

        }

        if(errors.invoice_date){

            $('.invalid-invoice-date').text(errors.invoice_date);

        }

        if(errors.payment_type){

            $('.invalid-payment-type').text(errors.payment_type);

        }

        if(errors.status){

            $('.invalid-status').text(errors.status);

        }

    }

       

</script>