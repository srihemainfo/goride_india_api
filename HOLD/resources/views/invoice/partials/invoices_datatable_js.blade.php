<script>

    $(function () {

     var formDataObject = {};

    formDataObject['token'] = getCookie('d_token');

    formDataObject['device_id'] = 0; 

      console.log(formDataObject);  

        $.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        });



        //Date range picker

        const start = moment('2015-01-01');

        const end = moment();



        function cb(start, end) {

            $('#invoice_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        }



        $('#invoice_between_filter').daterangepicker({}, cb)

        .on('apply.daterangepicker', function(ev, picker) {

            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));

        })

        .on('cancel.daterangepicker', function(ev, picker) {

            $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

        });



        cb(start, end);



        // Datatable

        let table = $('#data-table').DataTable({

            processing: true,

            serverSide: true,

            searching: true,

            ajax: {

                url: "{{ route('invoice.index') }}",

                headers: {

                    "Authorization": "Bearer " + formDataObject['token'],

                    "DeviceId": formDataObject['device_id']

                },

                 method: 'GET',

                data: function (d) {

                    d.invoiceno = $('input[name=invoice_no_filter]').val();

                    d.filter_from_date = $('input[name=filter_from_date]').val() ? $('input[name=filter_from_date]').val() : undefined;

                    d.filter_to_date = $('input[name=filter_to_date]').val() ? $('input[name=filter_to_date]').val() : undefined;

                },

            },

            columns: [

                {data:'id', name:'id', width: "5%", searchable: false },

                {data: 'invoiceno', name: 'invoiceno', width: "10%"},

                {data: 'invdate', name: 'invdate', className: "text-center", width: "10%"},

                {data: 'clientname', name: 'clientname', orderable: false, width: "17%", searchable: false},

                {data: 'jobid', name: 'jobid', orderable: false, className: "text-center", width: "15%", searchable: false},

                {data: 'total', name: 'total', className: "text-center", searchable: false, width: "10%"},

                {data: 'status', name: 'status', orderable: false, className: "text-center", searchable: false, width: "15%"},

                {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center",  width: "20%"},



            ],

            order:[[0,'desc']]

        });



        //Change the value of date range filter

        $("#invoice_between_filter").change(function(){

            let selected_date = $(this).val()

            let date_array = selected_date.split(" ")



            let from_date = moment(date_array[0]).format('YYYY-MM-DD');

            let to_date = moment(date_array[2]).format('YYYY-MM-DD');



            $('#filter_from_date').val(from_date)

            $('#filter_to_date').val(to_date)

        });



        //Search Datatable

        $('#search').on('click', function(e) {

            table.draw();

        });

        // $('#search').on('click', function(){

        //     var formdataobject = {};
        //     formDataObject['token'] = getCookie('d_token');
        //     formDataObject['device_id'] = 0;
        //     // let filterdate = $('#invoice_between_filter').val();
        //     // let date_array = filterdate.split(" - ");
        //     formDataObject['invoice_id'] = $('#invoice_no_filter').val();

        //     $('#data-table').DataTable({
        //         processing: true,
        //         searchable:true,
        //         responcesive: true,
        //         ajax:{
        //             url: '{{env('API_URL')}}invoice_filter',
        //             method: 'POST',
        //             data: formDataObject,
        //             dataSrc: function(json){
        //                 console.log("Received JSON:", json);

        //             }


        //         }
        //     })

        // });



        //Delete Ajax

        $('body').on('click', '.deleteInvoice', function(){

            let invoiceno = $(this).data("id");

            console.log(invoiceno)

            Swal.fire({

                title: "Are you sure to delete this Invoice?",

                text: "It will gone forever.",

                icon: "warning",

                buttons: true,

                dangerMode: true,

            }).then((willDelete) =>{

                if(willDelete.isConfirmed){

                    $.ajax({

                        type: "POST",

                        url: '{{ route('CancelInvoice') }}',

                        data: {invoiceno: invoiceno},

                        dataType: 'json',

                        success: function (response) {

                            if(response.data){

                                table.draw();

                                Swal.fire({

                                    position: 'top-end',

                                    icon: 'success',

                                    title: 'Deleted',

                                    text: 'Invoice deleted successfully',

                                    showConfirmButton: false,

                                    timer: 2000

                                });



                            } else {

                                Swal.fire("Error", "Invoice not deleted", "error");

                            }

                        },

                        error: function (data) {

                            console.log('Error:', data);

                        }

                    });

                }

            })

        });



        //Update invoice status

        $('body').on('change', '.invoice-status', function(){

            let invoice_status = $(this).data('previous');

            let invoice_id = $(this).data("id");

            let status = $(this).val();

            console.log(invoice_status);

            Swal.fire({

                title: "Status Update",

                text: "Are you sure want change the status?.",

                icon: "warning",

                buttons: true,

                dangerMode: true,

            }).then((willUpdate) =>{

                if(willUpdate.isConfirmed){

                    $.ajax({

                        type: "POST",

                        url: "{{ route('InvoiceStatusUpdate') }}",

                        data: {id: invoice_id, status: status},

                        success: function (response) {

                            if(response.isUpdated){

                                table.draw();

                                Swal.fire({

                                    position: 'top-end',

                                    icon: 'success',

                                    title: 'Updated',

                                    text: 'Invoice status changed successfully',

                                    showConfirmButton: false,

                                    timer: 2000

                                });

                            } else {

                                ('[data-id="' + invoice_id + '"]').val(invoice_status)

                                Swal.fire("Error", "Driver status not changed", "error");

                            }

                        },

                        error: function (data) {

                            ('[data-id="' + invoice_id + '"]').val(invoice_status)

                            console.log('Error:', data);

                        }

                    });

                }

                else {

                $('[data-id="' + invoice_id + '"]').val(invoice_status)

                }

            })

        });



        //Modal Form Trigger

        $('#addInvoice').click(function () {

            ResetErrors()

            $('#invoice_id').val('');

            $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");

            $('#invoiceForm').trigger("reset");

            $('#form-modal').modal('show');

        });



        //Reset filter values

        $('#reset_filter').click(function(){ 

            $("#invoice_no_filter").val('');

            $("#invoice_between_filter").val('');

            $("#filter_from_date").val('');

            // $("#filter_to_date").val('');

            $('tbody').html("");

            // cb(start, end);

            table.draw();

        });



        //Email Settlement Report

        $(document).on('click', '.email_now', function(){

            let invoice_no = $(this).data('id')



            $.ajax({

                type: "POST",

                url: "{{ route('EmailInvoice') }}",

                data: {

                    invoice_no: invoice_no

                },

                beforeSend: function() {

                    $('body').append(loading_animation())

                },

                success: function(response) {

                    $('.loading-overlay').remove()



                    if(response.message == 'Email sent successfully.'){

                        Swal.fire({

                            position: 'top-end',

                            icon: 'success',

                            title: 'Success',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 3000,

                        })

                    }else if(response.status == 400){

                        Swal.fire({

                            position: 'top-end',

                            icon: 'error',

                            title: 'Failed',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 3000,

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



    @if (session('invoice_save'))

        Swal.fire({

            position: 'top-end',

            icon: 'success',

            title: 'Generated',

            text: '{{ session('invoice_save') }}',

            showConfirmButton: false,

            timer: 2000,

        })



        @php

            Illuminate\Support\Facades\Session::forget('invoice_save');

        @endphp

    @endif



</script>

