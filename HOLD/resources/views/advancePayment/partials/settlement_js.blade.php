<script>

    $(function () {

        

        driverlist()

        showlist()



        //Select2 fields

        $('#index_custom_filter_form').select2();

        $('#driver_name, #driver_name_filter', 'driver_name_create').select2();



        //Change the value of filter

        // $("#driver_name_filter").change(function(){

        //         $("#name_filter").val($(this).val());

        // });

        

        // $('#date_filter').datepicker({

        //     format: "dd-mm-yyyy"

        // }).datepicker()

        

        $('input[name="index_custom_filter"]').daterangepicker({

                autoUpdateInput: false,

                locale: {

                    cancelLabel: 'Clear'

                },

                ranges: {

                    'Today': [moment().startOf('day'), moment().endOf('day')],

                    'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],

                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],

                    'This Month': [moment().startOf('month'), moment().endOf('month')],

                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],

                    'This Year': [moment().startOf('year'), moment().endOf('year')],

                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]

                }

            });

        

        // Ensure input field updates when a date is selected

        $('input[name="index_custom_filter"]').on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));

        });

    

        // Clear the input field when cancel is clicked

        $('input[name="index_custom_filter"]').on('cancel.daterangepicker', function(ev, picker) {

                $(this).val('');

            });



        // $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {

        //     $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));

        // });



        // $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {

        //     $(this).val('');

        // });



        //Date range picker variables

        const start = moment('2015-01-01');

        const end = moment();



        // //Date range picker for pickup dates

        // function pickup_callback(start, end) {

        //     $('#custom_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        // }



        // $('#custom_filter').daterangepicker({}, pickup_callback)

        //     .on('apply.daterangepicker', function(ev, picker) {

        //         $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(

        //             'DD/MM/YYYY'));

        //     })

        // .on('cancel.daterangepicker', function(ev, picker) {

        //     $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        // });



        // pickup_callback(start, end);

        

        // $('#index_custom_filter').daterangepicker({}, pickup_callback)

        //     .on('apply.daterangepicker', function(ev, picker) {

        //         $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(

        //             'DD/MM/YYYY'));

        //     })

        //     .on('cancel.daterangepicker', function(ev, picker) {

        //         $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        //     });



        // pickup_callback(start, end);

        

        

        // $('#settlement_type').change(function(){

            

        //     let set_value = $('#settlement_type').val();

        //     if(set_value != '' && set_value != null && set_value != undefined){

                

        //         // if (set_value === 'Monthly') {

        //         //     $('#month_select').show()

    

        //         //     $('#week_select').hide()

        //         //     $('#day_select').hide()

        //         //     $('#custom_select').hide()

    

        //         //     $('#index_custom_filter').val('').trigger('change')

        //         //     $('#date_filter').val('').trigger('change')

        //         //     $('#custom_filter').val('').trigger('change')

        //         // } else 

        //         if (set_value === 'Weekly') {

        //             $('#week_select').show()

    

        //             $('#month_select').hide()

        //             $('#day_select').hide()

        //             $('#custom_select').hide()

    

        //             $('#month_filter').val('').trigger('change')

        //             $('#date_filter').val('').trigger('change')

        //             $('#custom_filter').val('').trigger('change')

        //         } else if (set_value === 'Daily') {

        //             $('#day_select').show()

    

        //             $('#month_select').hide()

        //             $('#week_select').hide()

        //             $('#custom_select').hide()

    

        //             $('#month_filter').val('').trigger('change')

        //             $('#index_custom_filter').val('').trigger('change')

        //             $('#custom_filter').val('').trigger('change')

        //         } else if (set_value === 'Custom') {

        //             $('#custom_select').show()

    

        //             $('#month_select').hide()

        //             $('#week_select').hide()

        //             $('#day_select').hide()

    

        //             $('#month_filter').val('').trigger('change')

        //             $('#index_custom_filter').val('').trigger('change')

        //             $('#date_filter').val('').trigger('change')

        //         } else {

        //             $('#month_select, #week_select, #day_select, #custom_select').hide()

        //             $('#month_filter, #index_custom_filter, #date_filter, #custom_filter').val('').trigger('change')

        //             $('#generate-link').attr("href", '')

        //             $('#generate-excel').attr("href", '')

        //             $('#generate-btn').hide()

        //         }

        //     }else {

        //         $('#month_select, #week_select, #day_select, #custom_select').hide()

        //         $('#month_filter, #index_custom_filter, #date_filter, #custom_filter').val('').trigger('change')

        //         $('#generate-link').attr("href", '')

        //         $('#generate-excel').attr("href", '')

        //         $('#generate-btn').hide()

        //     }

        // })



        $("#search_filter").click(function () {

            var formDataObject  = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0;

            // let selected_date = $('#index_custom_filter').val()

            // let date_array = selected_date.split(" ")

            // formDataObject['from_date'] = date_array[0];

            // formDataObject['to_date'] = date_array[2];

            

            let selected_date = $('#index_custom_filter').val();

            let date_array = selected_date.split(" - ");

        

            formDataObject['from_date'] = formatDate(date_array[0]);

            formDataObject['to_date'] = formatDate(date_array[1]);

            

            

            formDataObject['driver_id'] = $('#driver_name_filter').val();

            console.log(formDataObject)

            if ($.fn.DataTable.isDataTable('#data-table')) {

                $('#data-table').DataTable().destroy();

            }

            $('#data-table').DataTable({

                processing: true,

                searching: true,

                ajax: {

                    url: '{{env('API_URL')}}advance-filter',

                    method: 'POST',

                    data: formDataObject,

                    dataSrc: function(json) {

                        console.log("Received JSON:", json); // Log the response to inspect

                        if (!json || !json.data) {

                            console.error("No 'data' property in the response.");

                            return [];

                        }

                        return json.data;  // Return the 'data' array from the response

                    },

                    error: function(xhr, error, thrown) {

                        console.error("Error in AJAX request:", xhr.responseText); // Log any errors

                    }

                },

                columns: [

                    {

                        data: null,

                        render: function(data, type, row, meta) {

                            return meta.row + 1;

                        }

                    },

                    

                    // { data: 'id' },

                    { 

                        data: 'null',

                        render: function(data, type, row) {

                            return `${row.driver_no} ${row.name}`;

                        }

                        

                    },

           

                                // <span style="padding: 8px;">

                                //     <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="advanceedit(${row.id})"></i>

                                // </span>

                    { data: 'driver_amt' },

                    { data: 'payment_date' },

                    {

                        data: null,

                        render: function(data, type, row) {

                            return `

                                <span style="padding: 8px;">

                                    <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="advancedelete(${row.id})"></i>

                                </span>`;

                        }

                    }

                ],

                language: {

                    emptyTable: "No records found"

                },

                drawCallback: function(settings) {

                    var api = this.api();

                    var data = api.rows().data();

                    if (data.length === 0) {

                        $('.row-checkbox').hide();

                    }

                },

                responsive: {

                    details: {

                        type: 'column',

                        target: 'tr'

                    }

                }

            });

            // $.ajax({

            //     data: formDataObject,

            //     url: '{{env('API_URL')}}advance-filter",

            //     type: "POST",

            //     dataType: 'json',

            //     success: function (response) {

            //         console.log(response);

            //         if(response['status'] == 200){

            //             $('tbody').html("");

            //             MakeTransactionTable(response.transactions, response.total_week_cost, response.total_week_settlement, response.total_total_settlement, response.from_date, response.to_date)

            //         }

            //         if(response['status'] == 400){

            //           errornotify(response)

            //         }

            //         if(response['status'] == 500){

            //           warningClick('Error',response['error'],"danger")

            //         }

            //         if(response['status'] == 401){

            //           unauth()

            //         }

            //     },

            //     error: function (data) {

            //           console.log('Error:', data);

            //     }

            // });

        });



        //Reset filter values

        $('#reset_filter').click(function(){

            $("#driver_name_filter").val(null).trigger("change");

            $("#name_filter").val(null).trigger("change");

            $("#index_custom_filter").val(null).trigger("change");

            $('tbody').html("");

            showlist()

        });



        //excel report generation

        $('body').on('click', '#settlement_report', function(){

                const base_url = '{{ url('driver-settlement-pdf') }}'

                let driver_id = $('#driver_name_filter').val();

                let week = $('#index_custom_filter').val();



                let link_query = ''

                link_query = base_url + '?driver_id='+driver_id+'&week='+week

                $('#settlement_report').attr("href", link_query)



                //console.log(link_query)

            });



        //Modal Form Trigger

        $('#calcSettlement').click(function (e) {

            e.preventDefault()

            $('#payBtn').html("<i class='fas fa-coins'></i>&nbsp Submit");

            $('#generateSettlementForm').trigger("reset");

            $('#form-modal').modal('show');

        });

        

        function formatDate(dateStr) {

            let separator = dateStr.includes("/") ? "/" : "-"; // Detect separator

            let [day, month, year] = dateStr.split(separator);

            return `${year}-${month}-${day}`;

        }



        // Ajax for Save and Update

		$('#payBtn').click(function (e) {

		  //  $('#advance_id').val(data.id);

		  //  console.log('hii')

	        e.preventDefault();

            var formDataObject  = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0;

            

            

		    let set_value = $('#settlement_type').val();

		    let is_Valid = false;

		    let newdate = $('#date_filter').val();

		    

            if (newdate != '') {

                // var formdate = moment($('#date_filter').data('daterangepicker').startDate).format("YYYY-MM-DD HH:mm:ss");

                // var todate = moment($('#date_filter').data('daterangepicker').endDate).format("YYYY-MM-DD HH:mm:ss");

                

                formDataObject['from_date'] = newdate;

                formDataObject['to_date'] = newdate;

                

                is_Valid = true;

            } else {

                var formdate = '';

                var todate = '';

                is_Valid = false;

            }

            

            if($('#driver_amt').val() == ''){

                is_Valid = false;

            }

            

            if($('#driver_name_create').val() == ''){

                is_Valid = false;

            }

            

            formDataObject['driver_id'] = $('#driver_name_create').val();

            formDataObject['driver_amt'] = $('#driver_amt').val();

            formDataObject['advance_id'] = $('#advance_id').val();

            

            if(is_Valid){

                

                $.ajax({

                    data: formDataObject,

                    url: '{{env('API_URL')}}advance-paymentStore',

                    type: "POST",

                    dataType: 'json',

                    beforeSend: function() {

                        $('#load_animation').show()

                    },

                    success: function (response) {

                        $('#load_animation').hide()

                        

                        if(response.status == 400){

                            Swal.fire({

                                position: 'center',

                                icon: 'warning',

                                title: 'No Data Found',

                                text: response['message'],

                                showConfirmButton: false,

                                timer: 2000

                            });

                            $('#form-modal').modal('hide');

                            showlist()

                        }



                        if(response.status == 200){

                            Swal.fire({

                                position: 'center',

                                icon: 'success',

                                title: 'Success',

                                text: response.message,

                                showConfirmButton: false,

                                timer: 2000

                            });

                           $('#form-modal').modal('hide');

                            showlist()

                            // window.location.reload();



                        }

                        if(response['status'] == 500){

                          warningClick('Error',response['error'],"danger")

                          $('#form-modal').modal('show');

                            showlist()

                        }

                        if(response['status'] == 401){

                          unauth()

                        }

         

                    },

                    error: function (data) {

                        $('#load_animation').hide()

                        console.log('Error:', data);

                    }

                })

                $('#advance_id').val('')

            }else{

                warningClick('Required', 'Fill All Required Fields',"warning")

            }

		    

        })

        //Email Settlement Report

        $(document).on('click', '.email_now', function(){

            let transaction_id = $(this).data('transaction_id');

            let driver_id = $(this).data('driver_id');

            let job_id = $(this).data('job_id');

            // let week = $(this).data('week');

            let week = $('#index_custom_filter').val();



            $.ajax({

                type: "GET",

                url: "{{ route('EmailSettlements') }}",

                data: {

                    transaction_id: transaction_id,

                    driver_id: driver_id,

                    job_id: job_id,

                    week: week

                },

                beforeSend: function() {

                    $('body').append(loading_animation())

                },

                success: function(response) {

                    $('.loading-overlay').remove()



                    if(response.status == 200){

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Success',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 2000,

                        })

                    }else if(response.status == 400){

                        Swal.fire({

                            position: 'center',

                            icon: 'error',

                            title: 'Failed',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 2000,

                        })

                    }

                },

                error: function(data) {

                    $('.loading-overlay').remove()

                    console.log('Error');

                }

            });

        })

    })

    

function showlist() {

    $('#advance_id').val('')

    var formDataObject = {

        token: getCookie('d_token'),

        device_id: 0

    };



    if ($.fn.DataTable.isDataTable('#data-table')) {

        $('#data-table').DataTable().destroy();

    }



    $('#data-table').DataTable({

        processing: true,

        searching: true,

        ajax: {

            url: '{{env('API_URL')}}advance-paymentIndex',

            method: 'POST',

            data: formDataObject,

            dataSrc: function(json) {

                console.log("Received JSON:", json); // Log the response to inspect

                if (!json || !json.data) {

                    console.error("No 'data' property in the response.");

                    return [];

                }

                return json.data;  // Return the 'data' array from the response

            },

            error: function(xhr, error, thrown) {

                console.error("Error in AJAX request:", xhr.responseText); // Log any errors

            }

        },

        columns: [

            {

                data: null,

                render: function(data, type, row, meta) {

                    return meta.row + 1;

                }

            },

            

            // { data: 'id' },

            { 

                data: 'null',

                render: function(data, type, row) {

                    return `${row.driver_no} ${row.name}`;

                }

                

            },

                        // <span style="padding: 8px;">

                        //     <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="advanceedit(${row.id})"></i>

                        // </span>

   

            { data: 'driver_amt' },

            { data: 'payment_date' },

            {

                data: null,

                render: function(data, type, row) {

                    return `

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="advancedelete(${row.id})"></i>

                        </span>`;

                }

            }

        ],

        language: {

            emptyTable: "No records found"

        },

        drawCallback: function(settings) {

            var api = this.api();

            var data = api.rows().data();

            if (data.length === 0) {

                $('.row-checkbox').hide();

            }

        },

        responsive: {

            details: {

                type: 'column',

                target: 'tr'

            }

        }

    });

}



    

function driverlist(){

        var formDataObject  = {};

          formDataObject['token'] = getCookie('d_token');

          formDataObject['device_id'] = 0;

          var settings = {

         "url": '{{env('API_URL')}}'+"driverlist",

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

             $('#driver_name_create').html(driver_options)

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

    

function advanceedit(id){

    

    if(id != ''){

        var formDataObject  = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['advance_id'] = id;

        

        $.ajax({

            type: "POST",

            url: '{{env('API_URL')}}advance-paymentEdit',

            data: formDataObject,

            beforeSend: function() {

                $('body').append(loading_animation())

            },

            success: function(response) {

                $('.loading-overlay').remove()

                let data = response.message;

                if(response.status == 200 && data != null){

                    

                    $('#advance_id').val(data.id);

                    $('#date_filter').val(data.payment_date);

                    $('#driver_name_create').val(data.driver_id).trigger('change');

                    $('#driver_amt').val(data.driver_amt);

                    $('#form-modal').modal('show');

                    // showlist()

                    

                }else if(response.status == 400){

                    Swal.fire({

                        position: 'center',

                        icon: 'error',

                        title: 'Failed',

                        text: response.message,

                        showConfirmButton: false,

                        timer: 2000,

                    })

                    $('#form-modal').modal('show');

                    // showlist()

                }

            },

            error: function(data) {

                $('.loading-overlay').remove()

                console.log('Error');

            }

        });

        

    }

}



function advancedelete(id){

    

    if(id != ''){

        var formDataObject  = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['advance_id'] = id;

        

        $.ajax({

            type: "POST",

            url: '{{env('API_URL')}}advance-paymentDelete',

            data: formDataObject,

            beforeSend: function() {

                $('body').append(loading_animation())

            },

            success: function(response) {

                $('.loading-overlay').remove()

                let data = response.message;

                if(response.status == 200){

                    

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Success',

                        text: data,

                        showConfirmButton: false,

                        timer: 2000,

                    })

                    // $('#form-modal').modal('show');

                    showlist()

                    

                }else if(response.status == 400){

                    Swal.fire({

                        position: 'center',

                        icon: 'error',

                        title: 'Failed',

                        text: data,

                        showConfirmButton: false,

                        timer: 2000,

                    })

                    // $('#form-modal').modal('show');

                    showlist()

                }

            },

            error: function(data) {

                $('.loading-overlay').remove()

                console.log('Error');

            }

        });

        

    }

}



function MakeTransactionTable(transactions, total_week_cost, total_week_settlement, total_total_settlement, from_date, to_date) {

    // Append the date range header

    $('#settlement_view').append(`

        <tr>

            <td colspan="10">Date: ${from_date} to ${to_date}</td>

        </tr>

    `);



    // Check if there are transactions

    if (transactions.length > 0) {

        $.each(transactions, function(key, item) {

        var regex = /(\d{4}-\d{2}-\d{2}\s+to\s+\d{4}-\d{2}-\d{2})/;

        var matches = item.note.match(regex);



            if (matches && matches[1]) {

                var dateRange = matches[1];

            }

            $('#settlement_view').append(`

                <tr>

                    <td>${item.driver_no}-${item.name}</td>

                    <td class="text-right">${(item.total ? item.total.toFixed(2) : '0.00')}</td>

                    <!-- 

                    <td class="text-right">${(item.bank ? item.bank.toFixed(2) : '0.00')}</td>

                    <td class="text-right">${(item.cash ? item.cash.toFixed(2) : '0.00')}</td>

                    <td class="text-right">${(item.card ? item.card.toFixed(2) : '0.00')}</td>

                    -->

                    <td class="text-right">${(item.comm ? item.comm.toFixed(2) : '0.00')}</td>

                    <!-- 

                    <td class="text-right">${(item.credit ? item.credit.toFixed(2) : '0.00')}</td>

                    -->

                    <td>

                        <a href="/weekly-driver-settlement-pdf?transaction_id=${item.id}&driver_id=${item.driver_id}&job_id=${item.jobid}" target="_blank" data-id="${item.id}" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-info previewTransaction" title="Preview Transaction"> 

                            <i class="fa fa-print"></i> 

                        </a>

                        

                        <a href="#"

                                        data-id="${item.id}" data-week="${dateRange}" data-transaction_id="${item.id}" data-driver_id="${item.driver_id}" data-job_id="${item.jobid}" title="Send Email" class="email_now mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendEmail">

                            <i class="fa fa-envelope"></i>

                        </a>

                    </td>

                </tr>

            `);

        });

    } else {

        // Append no data found message

        $('#settlement_view').append(`

            <tr>

                <td colspan="4" style="color: #f00; text-align:center; font-weight: bold;">No data found for last week.</td>

            </tr>

        `);

    }



    // Append the total row

    $('#settlement_view').append(`

        <tr>

            <td><b>Total</b></td>

            <td class="text-right"><b>${(total_week_cost ? total_week_cost.toFixed(2) : '0.00')}</b></td>

            <td colspan="2"></td>

        </tr>

    `);

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



</script>

