<script>

    $(function () {


        const today = new Date();
        const formattedDate = today.toLocaleDateString('en-GB').split('/').join('-');

       
        driverlist()

        showlist()



        //Select2 fields

        $('#index_custom_filter_form').select2();

        $('#driver_name, #driver_name_filter', 'driver_name_create').select2();



        //Change the value of filter

        // $("#driver_name_filter").change(function(){

        //         $("#name_filter").val($(this).val());

        // });



        $('#date_filter').datepicker({

            format: "dd-mm-yyyy"

        }).datepicker()



        //Date range picker variables

        const start = moment('2015-01-01');

        const end = moment();



        //Date range picker for pickup dates

        function pickup_callback(start, end) {

            $('#custom_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        }



        $('#custom_filter').daterangepicker({}, pickup_callback)

            .on('apply.daterangepicker', function (ev, picker) {

                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(

                    'DD/MM/YYYY'));

            })

            .on('cancel.daterangepicker', function (ev, picker) {

                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

            });



        pickup_callback(start, end);



        // $('#index_custom_filter').daterangepicker({}, pickup_callback)

        //     .on('apply.daterangepicker', function(ev, picker) {

        //         $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(

        //             'DD/MM/YYYY'));

        //     })

        //     .on('cancel.daterangepicker', function(ev, picker) {

        //         $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        //     });



        // pickup_callback(start, end);



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

        $('input[name="index_custom_filter"]').on('apply.daterangepicker', function (ev, picker) {

            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));

        });



        // Clear the input field when cancel is clicked

        $('input[name="index_custom_filter"]').on('cancel.daterangepicker', function (ev, picker) {

            $(this).val('');

        });





        $('#settlement_type').change(function () {



            let set_value = $('#settlement_type').val();

            if (set_value != '' && set_value != null && set_value != undefined) {



                // if (set_value === 'Monthly') {

                //     $('#month_select').show()



                //     $('#week_select').hide()

                //     $('#day_select').hide()

                //     $('#custom_select').hide()



                //     $('#index_custom_filter').val('').trigger('change')

                //     $('#date_filter').val('').trigger('change')

                //     $('#custom_filter').val('').trigger('change')

                // } else 

                if (set_value === 'Weekly') {

                    $('#week_select').show()



                    $('#month_select').hide()

                    $('#day_select').hide()

                    $('#custom_select').hide()



                    $('#month_filter').val('').trigger('change')

                    $('#date_filter').val('').trigger('change')

                    $('#custom_filter').val('').trigger('change')

                } else if (set_value === 'Daily') {

                    $('#day_select').show()

                    $('#date_filter').val(formattedDate);

                    $('#month_select').hide()

                    $('#week_select').hide()

                    $('#custom_select').hide()



                    $('#month_filter').val('').trigger('change')

                    $('#index_custom_filter').val('').trigger('change')

                    $('#custom_filter').val('').trigger('change')

                } else if (set_value === 'Custom') {

                    $('#custom_select').show()



                    $('#month_select').hide()

                    $('#week_select').hide()

                    $('#day_select').hide()



                    $('#month_filter').val('').trigger('change')

                    $('#index_custom_filter').val('').trigger('change')

                    $('#date_filter').val('').trigger('change')

                } else {

                    $('#month_select, #week_select, #day_select, #custom_select').hide()

                    $('#month_filter, #index_custom_filter, #date_filter, #custom_filter').val('').trigger('change')

                    $('#generate-link').attr("href", '')

                    $('#generate-excel').attr("href", '')

                    $('#generate-btn').hide()

                }

            } else {

                $('#month_select, #week_select, #day_select, #custom_select').hide()

                $('#month_filter, #index_custom_filter, #date_filter, #custom_filter').val('').trigger('change')

                $('#generate-link').attr("href", '')

                $('#generate-excel').attr("href", '')

                $('#generate-btn').hide()

            }

        })



        $("#search_filter").click(function () {

            var formDataObject = {};

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

            // //console.log(formDataObject)

            $.ajax({

                data: formDataObject,

                url: "{{env('API_URL')}}transfilter",

                type: "POST",

                dataType: 'json',

                success: function (response) {

                    //console.log(response);

                    if (response['status'] == 200) {

                        $('tbody').html("");

                        MakeTransactionTable(response.transactions, response.total_week_cost, response.total_week_settlement, response.total_total_settlement, response.from_date, response.to_date)

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

                },

                error: function (data) {

                    //console.log('Error:', data);

                }

            });

        });



        //Reset filter values

        $('#reset_filter').click(function () {

            $("#driver_name_filter").val(null).trigger("change");

            $("#name_filter").val(null).trigger("change");

            $("#index_custom_filter").val(null).trigger("change");

            $('tbody').html("");

        });



        //excel report generation

        $('body').on('click', '#settlement_report', function () {

            const base_url = '{{ url('driver-settlement-pdf') }}'

            let driver_id = $('#driver_name_filter').val();

            let week = $('#index_custom_filter').val();



            let link_query = ''

            link_query = base_url + '?driver_id=' + driver_id + '&week=' + week

            $('#settlement_report').attr("href", link_query)



            ////console.log(link_query)

        });



        //Modal Form Trigger

        $('#calcSettlement').click(function (e) {

            e.preventDefault()

            $('#saveBtn').html("<i class=\"fa fa-calculator\"></i>&nbsp; Calculate");

            $('#generateSettlementForm').trigger("reset");

            $('#form-modal').modal('show');

            $('#date_filter').val(formattedDate);



        });



        function formatDate(dateStr) {
            if (!dateStr) return ''; // Prevent error if dateStr is undefined/null/empty

            let separator = dateStr.includes("/") ? "/" : "-"; // Detect separator
            let [day, month, year] = dateStr.split(separator);

            return `${year}-${month}-${day}`;
        }


        // Ajax for Save and Update

        $('#saveBtn').click(function (e) {



            e.preventDefault();

            //         var button = $(this);
            //         var spinner = button.find('.spinner-border');

            // // Disable the button and show the spinner
            // // button.prop('disabled', true);
            //         spinner.show();




            var formDataObject = {};

            formDataObject['token'] = getCookie('d_token');

            formDataObject['device_id'] = 0;





            let set_value = $('#settlement_type').val();
            let is_Valid = false;

            if (set_value == '') {
                warningClick('Error', 'Please select Settlement Type', 'danger');
                is_Valid = false;
            }

            if (set_value != '' && set_value != null && set_value != undefined) {



                // if (set_value === 'Monthly') {



                //     let selected_month = $('#month_filter').val(); // Get "March-2025"

                //     let [monthName, year] = selected_month.split("-"); // Split into month and year



                //     // Convert month name to number

                //     let monthNumber = new Date(`${monthName} 1, ${year}`).getMonth() + 1;

                //     monthNumber = monthNumber.toString().padStart(2, "0"); // Ensure 2-digit format



                //     // Get last day of the month

                //     let lastDay = new Date(year, monthNumber, 0).getDate();



                //     // Format as MM/DD/YYYY - MM/DD/YYYY

                //     let from_date = `01/${monthNumber}/${year}`;

                //     let to_date = `${lastDay}/${monthNumber}/${year}`;



                //     formDataObject['from_date'] = from_date;

                //     formDataObject['to_date'] = to_date;

                //     is_Valid = true;





                // } else 

                if (set_value === 'Weekly') {

                    let selected_date = $('#week_filter_form').val(); // Example: "04/03/2025 - 10/03/2025"

                    let date_array = selected_date.split(" "); // ["04/03/2025", "-", "10/03/2025"]



                    formDataObject['from_date'] = formatDate(date_array[0]); // Convert to Y-m-d

                    formDataObject['to_date'] = formatDate(date_array[2]);   // Convert to Y-m-d

                    is_Valid = true;

                } else if (set_value === 'Daily') {

                    // const today = new Date().toISOString().split('T')[0]; // Format: yyyy-mm-dd
                    // $('#date_filter').val(today);

                    let selected_date = $('#date_filter').val();


                    formDataObject['from_date'] = formatDate(selected_date, "-");

                    formDataObject['to_date'] = formatDate(selected_date, "-");



                    if (selected_date == '') {
                        warningClick('Error', 'Date Field is Required', 'danger');
                        is_Valid = false;
                    } else {
                        is_Valid = true;
                    }

                } else if (set_value === 'Custom') {

                    let selected_date = $('#custom_filter').val(); // Example: "04/03/2025 - 10/03/2025"

                    let date_array = selected_date.split(" - "); // ["04/03/2025", "10/03/2025"]



                    formDataObject['from_date'] = formatDate(date_array[0]); // Convert to Y-m-d

                    formDataObject['to_date'] = formatDate(date_array[1]);

                    if (selected_date == '') {
                        warningClick('Error', 'Custom Date is Required', 'danger');
                        is_Valid = false;
                    } else {
                        is_Valid = true;
                    }



                }







                formDataObject['driver_id'] = $('#driver_name_create').val();

                // console.log('jana', formDataObject['driver_id']);

                if (formDataObject['driver_id'] == '') {
                    warningClick('Error', 'Please select driver name', "danger");
                    is_Valid = false;
                }

                if (is_Valid) {



                    $.ajax({

                        data: formDataObject,

                        url: '{{env('API_URL')}}settlement',

                        type: "POST",

                        dataType: 'json',

                        beforeSend: function () {

                            var button = $('#saveBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);


                        },

                        success: function (response) {

                            // $('#load_animation').hide()



                            if (response.status == 400) {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'warning',
                                    title: 'No Commission Rate is Found',
                                    text: response['message'],
                                    showConfirmButton: false,
                                    timer: 4000
                                }).then(() => {
                                    $('#form-modal').modal('hide');
                                    window.location.reload();
                                });
                            }



                            if (response.status == 200) {

                                Swal.fire({

                                    position: 'center',

                                    icon: 'success',

                                    title: 'Settlement Done',

                                    text: response.message,

                                    showConfirmButton: false,

                                    timer: 3000

                                });



                                window.location.reload();



                            }

                            if (response['status'] == 500) {

                                warningClick('Error', response['error'], "danger")

                            }

                            if (response['status'] == 401) {

                                unauth()

                            }



                        },

                        error: function (data) {

                            $('#load_animation').hide()

                            //console.log('Error:', data);

                        },
                        // complete: function () {

                        //     var button = $('#saveBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', false);
                        // }

                    })

                }



            }



        })

        //Email Settlement Report

        $(document).on('click', '.email_now', function () {

            let transaction_id = $(this).data('transaction_id');

            let driver_id = $(this).data('driver_id');

            let job_id = $(this).data('job_id');

            let week = $(this).data('week');

            // let week = $('#index_custom_filter').val();



            $.ajax({

                type: "GET",

                url: "{{ route('EmailSettlements') }}",

                data: {

                    transaction_id: transaction_id,

                    driver_id: driver_id,

                    job_id: job_id,

                    week: week

                },

                beforeSend: function () {

                    $('body').append(loading_animation())

                },

                success: function (response) {

                    $('.loading-overlay').remove()



                    if (response.status == 200) {

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Success',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 3000,

                        })

                    } else if (response.status == 400) {

                        Swal.fire({

                            position: 'center',

                            icon: 'error',

                            title: 'Failed',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 3000,

                        })

                    }

                },

                error: function (data) {

                    $('.loading-overlay').remove()

                    //console.log('Error');

                }

            });

        })

    })



    function showlist() {

        const url = 'transactions';

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



        $.ajax(settings).done(function (response) {

            //console.log(response);

            if (response['status'] == 200) {

                var list = `<tr><td colspan="4">Date: ${response['last_week']}</td></tr>`;

                if (response['transactions'].length > 0) {

                    for (let i = 0; i < response['transactions'].length; i++) {



                        let dateRange = response['transactions'][i].fromdate + ' to ' + response['transactions'][i].todate;



                        list += `<tr>

                                <td>${response['transactions'][i].driver_no + '-' + response['transactions'][i].name}</td>

                                <td style="text-align: right;">${response['transactions'][i].driver_amt ? response['transactions'][i].driver_amt.toFixed(2) : '0.00'}</td>

                                <td style="text-align: right;">${response['transactions'][i].comm ? response['transactions'][i].comm.toFixed(2) : '0.00'}</td>

                                <td>

                                    <a href="{{ route('WeeklyDriverSettlementPdf') }}?transaction_id=${response['transactions'][i].id}&driver_id=${response['transactions'][i].driver_id}&job_id=${response['transactions'][i].jobid}"

                                        target="_blank" data-id="${response['transactions'][i].id}&week=${response['last_week']}" title="Preview Transaction"

                                        class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-info previewTransaction">

                                        <i class="fa fa-print"></i>

                                    </a>

                                    <a href="#" data-id="${response['transactions'][i].id}" data-id="${response['transactions'][i].id}" data-week="${dateRange}" data-transaction_id="${response['transactions'][i].id}" data-driver_id="${response['transactions'][i].driver_id}" data-job_id="${response['transactions'][i].jobid}" title="Send Email"

                                        class=" mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark email_now">

                                        <i class="fa fa-envelope"></i>

                                    </a>

                                </td>

                            </tr>`;

                    }

                } else {

                    list += `<tr>

                            <td colspan="4" style="color: #f00; text-align:center; font-weight: bold;">No data found for last week.

                            </td>

                        </tr>`;

                }

                list += `<tr>

                        <td><b>Total</b></td>

                        <td style="text-align: right;"><b>${response['transaction_summary'] && response['transaction_summary'].total ? response['transaction_summary'].total.toFixed(2) : '0.00'}</b></td>

                        <td colspan="2"></td>

                    </tr>`;



                $('#settle-table').html(list);

            }



            if (response['status'] == 400) {

                errornotify(response);

            }

            if (response['status'] == 500) {

                warningClick('Error', response['error'], "danger");

            }

            if (response['status'] == 401) {

                unauth();

            }

        });

    }





    function driverlist() {

        var formDataObject = {};

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

            if (response['status'] == 200) {

                let driver_options = '<option value="">-- select driver --</option>'

                response['data'].forEach(function (item) {

                    driver_options +=

                        `<option value="${item.id}">${item.name}</option>`

                })

                $('#driver_name_filter').html(driver_options)

                $('#driver_name_create').html(driver_options)

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



    function MakeTransactionTable(transactions, total_week_cost, total_week_settlement, total_total_settlement, from_date, to_date) {

        // Append the date range header

        $('#settlement_view').append(`

        <tr>

            <td colspan="10">Date: ${from_date} to ${to_date}</td>

        </tr>

    `);



        // Check if there are transactions

        if (transactions.length > 0) {

            $.each(transactions, function (key, item) {

                var regex = /(\d{4}-\d{2}-\d{2}\s+to\s+\d{4}-\d{2}-\d{2})/;

                var matches = item.note.match(regex);

                // //console.log(matches);

                //     if (matches && matches[1]) {

                //         var dateRange = matches[1];

                //     }

                var dateRange = item.fromdate + 'to' + item.todate;



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

                        

                        <a href="#" data-id="${item.id}" data-week="${dateRange}" data-transaction_id="${item.id}" data-driver_id="${item.driver_id}" data-job_id="${item.jobid}" title="Send Email" class="email_now mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark sendEmail">

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





    function loading_animation() {

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