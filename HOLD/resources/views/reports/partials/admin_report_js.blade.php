<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Select2 fields
        $('#month_filter, #week_filter').select2()

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
            $('#job_select').show()
            
            $('#month_select').hide()
            $('#week_select').hide()
            $('#day_select').hide()
            $('#custom_select').hide()
            $('#generate-btn').show()
            $('#job_type').trigger('change')
            $('#job_filter').val('')
        })
        
        $('#job_select').change(function(){
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
        // $('#month_filter, #week_filter, #date_filter, #custom_filter').change(function() {
        //     const base_url = "{{ url('admin-generate-report') }}"
        //     // const base_url = '{{env('API_URL')}}admin-generate-report'
        //      let formDataObject  = {};
        //     formDataObject['token'] = getCookie('d_token');
        //     formDataObject['device_id'] = 0;
            
        //     let report_type = $('#report_type').val()
        //     let job_type = $('#job_type').val()
        //     let month = $('#month_filter').val();
        //     let week = $('#week_filter').val();
        //     let date = $('#date_filter').val();
        //     let custom_range = $('#custom_filter').val();
        //     let link_query = ''

        //     if (report_type === 'Monthly') {
        //         link_query = base_url + '?job_type=' + job_type + '&report_type=' + report_type + '&month_filter=' + month

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (month != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (report_type === 'Weekly') {
        //         modified_week_string = week.replaceAll(' ', '+')
        //         link_query = base_url + '?job_type=' + job_type + '&report_type=' + report_type + '&week_filter=' +
        //             modified_week_string

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (week != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (report_type === 'Daily') {
        //         link_query = base_url + '?job_type=' + job_type + '&report_type=' + report_type + '&date_filter=' + date

        //         $('#generate-link').attr("href", link_query + '&excel=0')
        //         $('#generate-excel').attr("href", link_query + '&excel=1')

        //         if (date != '') {
        //             $('#generate-btn').show()
        //         } else {
        //             $('#generate-btn').hide()
        //         }
        //     } else if (report_type === 'Custom') {

        //         link_query = base_url + '?job_type=' + job_type + '&report_type=' + report_type + '&custom_filter=' + custom_range

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
        
        
        $('#month_filter, #week_filter, #date_filter, #custom_filter').change(function() {
    const base_url = "{{ url('admin-generate-report') }}"
    // const base_url = '{{env('API_URL')}}admin-generate-report'
    let formDataObject  = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

    let report_type = $('#report_type').val()
    let job_type = $('#job_type').val()
    let month = $('#month_filter').val();
    let week = $('#week_filter').val();
    let date = $('#date_filter').val();
    let custom_range = $('#custom_filter').val();
    let link_query = `?job_type=${job_type}&report_type=${report_type}&device_id=${formDataObject.device_id}&token=${formDataObject.token}`;

    if (report_type === 'Monthly') {
        link_query += `&month_filter=${month}`;
    } else if (report_type === 'Weekly') {
        modified_week_string = week.replaceAll(' ', '+');
        link_query += `&week_filter=${modified_week_string}`;
    } else if (report_type === 'Daily') {
        link_query += `&date_filter=${date}`;
    } else if (report_type === 'Custom') {
        link_query += `&custom_filter=${custom_range}`;
    }

    $('#generate-link').attr("href", base_url + link_query + '&excel=0');
    $('#generate-excel').attr("href", base_url + link_query + '&excel=1');

    if ((report_type === 'Monthly' && month !== '') ||
        (report_type === 'Weekly' && week !== '') ||
        (report_type === 'Daily' && date !== '') ||
        (report_type === 'Custom' && custom_range !== '')) {
        $('#generate-btn').show();
    } else {
        $('#generate-btn').hide();
    }
});


    })
</script>
