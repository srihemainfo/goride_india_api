<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Select2 fields
        $('#driver_name_filter, #month_filter, #week_filter').select2()

        $('#date_filter').datepicker({
            format: "dd-mm-yyyy"
        }).datepicker("setDate", "-1d")

        //Date range picker variables
        const start = moment('2015-01-01');
        const end = moment();

        //Date range picker for pickup dates
        function pickup_callback(start, end) {
            $('#custom_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#custom_filter').daterangepicker({}, pickup_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        pickup_callback(start, end);

        //report type select show
        $('#report_type').change(function() {
            let report_type = $('#report_type').val()

            if (report_type === 'Monthly') {
                $('#month_select').show()

                $('#week_select').hide()
                $('#day_select').hide()
                $('#custom_select').hide()

                $('#week_filter').val('').trigger('change')
                $('#date_filter').val('').trigger('change')
                $('#custom_filter').val('').trigger('change')
            } else if (report_type === 'Weekly') {
                $('#week_select').show()

                $('#month_select').hide()
                $('#day_select').hide()
                $('#custom_select').hide()

                $('#month_filter').val('').trigger('change')
                $('#date_filter').val('').trigger('change')
                $('#custom_filter').val('').trigger('change')
            } else if (report_type === 'Daily') {
                $('#day_select').show()

                $('#month_select').hide()
                $('#week_select').hide()
                $('#custom_select').hide()

                $('#month_filter').val('').trigger('change')
                $('#week_filter').val('').trigger('change')
                $('#custom_filter').val('').trigger('change')
            } else if (report_type === 'Custom') {
                $('#custom_select').show()

                $('#month_select').hide()
                $('#week_select').hide()
                $('#day_select').hide()

                $('#month_filter').val('').trigger('change')
                $('#week_filter').val('').trigger('change')
                $('#date_filter').val('').trigger('change')
            } else {
                $('#month_select, #week_select, #day_select, #custom_select').hide()
                $('#month_filter, #week_filter, #date_filter, #custom_filter').val('').trigger('change')
                $('#generate-link').attr("href", '')
                $('#generate-excel').attr("href", '')
                $('#generate-btn').hide()
            }
        })

        //report range select to show generate button
        // $('#driver_name_filter, #month_filter, #week_filter, #date_filter, #custom_filter').change(function() {
        //         let formDataObject  = {};
        //     formDataObject['token'] = getCookie('d_token');
        //     formDataObject['device_id'] = 0;
        //     const base_url = "{{ url('driver-generate-report') }}"
        //     let report_type = $('#report_type').val()
        //     let driver_id = $('#driver_name_filter').val()
        //     let month = $('#month_filter').val();
        //     let week = $('#week_filter').val();
        //     let date = $('#date_filter').val();
        //     let custom_range = $('#custom_filter').val();
        //     let link_query = ''

        //     if (driver_id && report_type === 'Monthly') {
        //         link_query = base_url + '?report_type=' + report_type + '&month_filter=' + month +
        //             '&driver_id=' + driver_id

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (month != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (driver_id && report_type === 'Weekly') {
        //         modified_week_string = week.replaceAll(' ', '+')

        //         link_query = base_url + '?report_type=' + report_type + '&week_filter=' +
        //             modified_week_string + '&driver_id=' + driver_id

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (week != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (report_type === 'Daily') {
        //         link_query = base_url + '?report_type=' + report_type + '&date_filter=' + date +
        //             '&driver_id=' + driver_id

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (date != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (report_type === 'Custom') {

        //         link_query = base_url + '?report_type=' + report_type + '&custom_filter=' + custom_range

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (custom_range != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else {
        //         $('#generate-link').attr("href", '')
        //         $('#generate-excel').attr("href", '')
        //         $('#generate-btn').hide()
        //     }
        // })
 $('#driver_name_filter, #month_filter, #week_filter, #date_filter, #custom_filter').change(function() {
    let formDataObject = {
        'token': getCookie('d_token'),
        'device_id': 0,
        'd_token': '', // Add this line to initialize d_token
    };
    
    formDataObject['d_token'] = formDataObject['token'];

    const base_url = "{{ url('driver-generate-report') }}"
    let report_type = $('#report_type').val()
    let driver_id = $('#driver_name_filter').val()
    let month = $('#month_filter').val();
    let week = $('#week_filter').val();
    let date = $('#date_filter').val();
    let custom_range = $('#custom_filter').val();
    let link_query = '';

    if (driver_id && report_type === 'Monthly') {
        link_query = base_url + '?report_type=' + report_type + '&month_filter=' + month +
            '&driver_id=' + driver_id + '&d_token=' + formDataObject['d_token'] +
            '&device_id=' + formDataObject['device_id'];
        // rest of the code...
    } else if (driver_id && report_type === 'Weekly') {
        modified_week_string = week.replaceAll(' ', '+')
        link_query = base_url + '?report_type=' + report_type + '&week_filter=' +
            modified_week_string + '&driver_id=' + driver_id + '&d_token=' + formDataObject['d_token'] +
            '&device_id=' + formDataObject['device_id'];
        // rest of the code...
    } else if (report_type === 'Daily') {
        link_query = base_url + '?report_type=' + report_type + '&date_filter=' + date +
            '&driver_id=' + driver_id + '&d_token=' + formDataObject['d_token'] +
            '&device_id=' + formDataObject['device_id'];
        // rest of the code...
    } else if (report_type === 'Custom') {
        link_query = base_url + '?report_type=' + report_type + '&custom_filter=' + custom_range +
            '&d_token=' + formDataObject['d_token'] + '&device_id=' + formDataObject['device_id']+'&driver_id=' + driver_id ;
        // rest of the code...
    } else {
        link_query = base_url; // Default case without any parameters
        // rest of the code...
    }

    // Set href attributes for links
    $('#generate-link').attr("href", link_query + '&excel=0')
    $('#generate-excel').attr("href", link_query + '&excel=1')

    // Show/hide generate button based on the condition
    if ((month && report_type === 'Monthly') || (week && report_type === 'Weekly') || (date && report_type === 'Daily') || (custom_range && report_type === 'Custom')) {
        $('#generate-btn').show();
    } else {
        $('#generate-btn').hide();
    }
});



    })
</script>
