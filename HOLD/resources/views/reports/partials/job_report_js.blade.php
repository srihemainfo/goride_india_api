<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>


<script>
    // function AssignValues(data) {

    //     $('#edit_employee_id').val(data.id);
    //     $('#edit_user_id').val(data.user_id);
    //     $('#edit_first_name').val(data.emp_full_name);
    //     $('#edit_employee_type').val(data.employee_type);
    //     $('#edit_phone').val(data.phone);
    //     $('#edit_email').val(data.email);
    //     $('#edit_role_id').val(data.role_id).select2();
    // }

    // function edit_employee(id)  {
    //     loader.show();
    //     const url = 'editemployer';
    //     //   var formDataObject  = {};
    //     //   formDataObject['token'] = getCookie('d_token');
    //     //   formDataObject['device_id'] = 0;
    //     formDataObject['emp_id'] = id;
    //     var settings = {
    //         "url": "{{env('API_URL')}}" + url,
    //         "method": "POST",
    //         "timeout": 0,
    //         "headers": {
    //             "Content-Type": "application/json"
    //         },
    //         "data": JSON.stringify(formDataObject),
    //     };
    //     $.ajax(settings).done(function(response) {
    //         if (response['status'] == 200) {
    //             AssignValues(response['data'])
    //             $('#edit_cus_form-modal').modal('show')
    //         }
    //         if (response['status'] == 400) {
    //             warningClick('Error', response['message'], "danger")
    //         }
    //         if (response['status'] == 500) {
    //             warningClick('Error', response['error'], "danger")
    //         }
    //         if (response['status'] == 401) {
    //             unauth()
    //         }

    //         loader.hide();
    //     });
    // }

    $(function() {
        $('#job_date_filter').daterangepicker({
            autoUpdateInput: true, // Ensure input updates when date is selected
            showClear: false, // Disable the clear button
            format: 'DD/MM/YYYY'
        }).on('apply.daterangepicker', function(ev, picker) {
            let startDate = picker.startDate.format('YYYY-MM-DD');
            let endDate = picker.endDate.format('YYYY-MM-DD');
            $(this).val(startDate + ' - ' + endDate); // Set the correct format
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val(''); // Set value to empty string when canceled
            $(this).trigger('change'); // Optionally trigger change event to ensure any updates are processed
        });
        showlist()
        roles();
    })

    function showlist() {
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;

        var existingTable = $('#job-table').DataTable();
        if (existingTable) {
            existingTable.destroy();
        }

        new DataTable('#job-table', {
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Job Report',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Export to PDF',
                    titleAttr: 'PDF',
                    extension: ".pdf",
                    filename: "Job Details",
                    title: '',
                    className: 'btn btn-success',
                    orientation: 'portrait',
                    customize: function(doc) {
                        doc.pageSize = 'A4';
                        doc.pageMargins = [20, 20, 20, 10];



                        // Ensure table header rows are recognized
                        doc.content[0].table.headerRows = 1;

                        // Set column widths
                        doc.content[0].table.widths = [
                            '4%', '5%', '7%', '7%', '9%', '10%', '11%', '11%', '6%', '8%', '8%', '7%', '7%'
                        ];

                        // Apply styles to the header row
                        doc.content[0].table.body[0].forEach(cell => {
                            cell.fontSize = 7; // Set header font size
                            cell.bold = true;
                            cell.fillColor = 'white'; // Set header background color to white
                            cell.color = 'black'; // Set header text color to black
                            cell.alignment = 'center'; // Center align text
                            cell.margin = [4, 4, 4, 4];
                        });

                        // Apply styles to the body content
                        doc.content[0].table.body.forEach((row, index) => {
                            if (index !== 0) { // Skip header row
                                row.forEach(cell => {
                                    cell.fontSize = 5; // Set font size for body
                                    cell.alignment = 'center';
                                    cell.margin = [4, 4, 4, 4];
                                    // Even padding: [top, left, bottom, right]
                                });
                            }
                        });

                        // Define table layout for border styles
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) {
                            return 0.8;
                        };
                        objLayout['vLineWidth'] = function(i) {
                            return 0.5;
                        };
                        objLayout['hLineColor'] = function(i) {
                            return '#aaa';
                        };
                        objLayout['vLineColor'] = function(i) {
                            return '#aaa';
                        };
                        objLayout['paddingLeft'] = function(i) {
                            return 2; // Left padding 2px
                        };
                        objLayout['paddingRight'] = function(i) {
                            return 2; // Right padding 2px
                        };

                        doc.content[0].layout = objLayout;
                    },
                    exportOptions: {
                        columns: ':visible'
                    }
                }

            ],

            "lengthMenu": [10, 20, 50, 100], // Dropdown options for rows per page
            "pageLength": 10,
            ajax: {
                url: '{{env('API_URL')}}admin-job-report',
                method: 'POST',
                dataSrc: "data",
                data: formDataObject,
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div style="text-align: center;">${meta.row + 1}</div>`;
                    }
                },
                {
                    data: null,
                    defaultContent: 'Admin'
                },
                {
                    data: 'booking_date',
                    render: function(data, type, row) {
                        return data.split(' ')[0];
                    }
                },
                {
                    data: 'pickup_date'
                },
                {
                    data: 'Customer_name'
                },
                {
                    data: 'mobile'
                },
                {
                    data: 'pickup_address'
                },
                {
                    data: 'dropoff_location'
                },
                {
                    data: 'total_amount',
                    render: function(data, type, row) {
                        // Define currency symbols
                        let currencySymbols = {
                            'USD': '$',
                            'CAD': 'C$',
                            'INR': '₹',
                            'KWD': 'KD',
                            'GBP': '£'
                        };

                        // Get the corresponding symbol or use the currency code if not found
                        let symbol = currencySymbols[row.currency] || row.currency;

                        // Return formatted amount with currency symbol
                        return `<div style="text-align: center;">${symbol} ${data}</div>`;
                    }
                },
                {
                    data: 'driver_name',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (!row.driver_name || !row.driver_licence_no) {
                            return '-';
                        }
                        return row.driver_name + " (" + row.driver_licence_no + ")";
                    }
                },
                {
                    data: 'vech_reg_num',
                    render: function(data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'order_status',
                    render: function(data, type, row) {
                        if (data === 'Completed') {
                            return `<div style="text-align: center;"> Completed </div>`;
                        } else if (data === 'Canceled') {
                            return `<div style="text-align: center;"> Canceled </div>`;
                        } else if (['Settled', 'Assigned', 'Dispatched', 'Moving'].includes(data)) {
                            return `<div style="text-align: center;"> - </div>`;
                        }
                        return '';
                    },
                    searchable: false
                },
            ],
        });
    }



    $('#reset_job_filter').on('click', function() {

        $('#job_detail_filter')[0].reset()

        showlist()

    });

    $(function() {
        showlist();

        $('#job_search').on('click', function() {
            var formdata = $('#job_detail_filter').serialize();
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

            console.log("Form Data:", formDataObject); // Debugging

            // Destroy existing DataTable before reinitializing
            if ($.fn.DataTable.isDataTable('#job-table')) {
                $('#job-table').DataTable().destroy();
            }

            // Initialize Date Range Picker


            // Initialize DataTable
            $('#job-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{env('API_URL')}}admin-job-filter',
                    method: 'POST',
                    dataSrc: "data",
                    data: formDataObject,
                    error: function(xhr, error, thrown) {
                        console.error("Error loading data:", xhr.responseText);
                    }
                },
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        title: 'Job Report',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'Export to PDF',
                        titleAttr: 'PDF',
                        extension: ".pdf",
                        filename: "Job Details",
                        title: '',
                        className: 'btn btn-success',
                        orientation: 'portrait',
                        customize: function(doc) {
                            doc.pageSize = 'A4';
                            doc.pageMargins = [20, 20, 20, 10];



                            // Ensure table header rows are recognized
                            doc.content[0].table.headerRows = 1;

                            // Set column widths
                            doc.content[0].table.widths = [
                                '4%', '5%', '7%', '7%', '9%', '10%', '11%', '11%', '6%', '8%', '8%', '7%', '7%'
                            ];

                            // Apply styles to the header row
                            doc.content[0].table.body[0].forEach(cell => {
                                cell.fontSize = 7; // Set header font size
                                cell.bold = true;
                                cell.fillColor = 'white'; // Set header background color to white
                                cell.color = 'black'; // Set header text color to black
                                cell.alignment = 'center'; // Center align text
                                cell.margin = [4, 4, 4, 4];
                            });

                            // Apply styles to the body content
                            doc.content[0].table.body.forEach((row, index) => {
                                if (index !== 0) { // Skip header row
                                    row.forEach(cell => {
                                        cell.fontSize = 5; // Set font size for body
                                        cell.alignment = 'center';
                                        cell.margin = [4, 4, 4, 4];
                                        // Even padding: [top, left, bottom, right]
                                    });
                                }
                            });

                            // Define table layout for border styles
                            var objLayout = {};
                            objLayout['hLineWidth'] = function(i) {
                                return 0.8;
                            };
                            objLayout['vLineWidth'] = function(i) {
                                return 0.5;
                            };
                            objLayout['hLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['vLineColor'] = function(i) {
                                return '#aaa';
                            };
                            objLayout['paddingLeft'] = function(i) {
                                return 2; // Left padding 2px
                            };
                            objLayout['paddingRight'] = function(i) {
                                return 2; // Right padding 2px
                            };

                            doc.content[0].layout = objLayout;
                        },
                        exportOptions: {
                            columns: ':visible'
                        }
                    }

                ],
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return `<div style="text-align: center;">${meta.row + 1}</div>`;
                        }
                    },
                    {
                        data: null,
                        defaultContent: 'Admin'
                    },
                    {
                        data: 'booking_date',
                        render: function(data, type, row) {
                            return data.split(' ')[0];
                        }
                    },
                    {
                        data: 'pickup_date'
                    },
                    {
                        data: 'Customer_name'
                    },
                    {
                        data: 'mobile'
                    },
                    {
                        data: 'pickup_address'
                    },
                    {
                        data: 'dropoff_location'
                    },
                    {
                        data: 'total_amount',
                        render: function(data, type, row) {
                            return `<div style="text-align: center;">${data}</div>`;
                        }
                    },
                    {
                        data: 'driver_name',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (!row.driver_name || !row.driver_licence_no) {
                                return '-';
                            }
                            return row.driver_name + " (" + row.driver_licence_no + ")";
                        }
                    },
                    {
                        data: 'vech_reg_num',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'order_status',
                        render: function(data, type, row) {
                            if (data === 'Completed') {
                                return `<div style="text-align: center;"> Completed </div>`;
                            } else if (data === 'Canceled') {
                                return `<div style="text-align: center;"> Canceled </div>`;
                            } else if (['Settled', 'Assigned', 'Dispatched', 'Moving'].includes(data)) {
                                return `<div style="text-align: center;"> - </div>`;
                            }
                            return '';
                        },
                        searchable: false
                    },
                ],
            });
        });
    });





    // function roles() {
    //     loader.show();
    //     $.ajax({
    //         url: "{{env('API_URL')}}roles/role_get",
    //         type: "POST",
    //         data: JSON.stringify(formDataObject), // Send the formData object
    //         contentType: 'application/json',
    //         dataType: 'json', // Set to false for FormData
    //         processData: false, // Prevent jQuery from processing the data
    //         success: function(response) {

    //             if (response.status == true) {
    //                 var select = $('.role_get');
    //                 // Clear any previous options
    //                 select.empty();

    //                 // Append a default option
    //                 select.append('<option value="">Select Role</option>');

    //                 // Loop through the response data and append each role
    //                 $.each(response.data, function(key, value) {
    //                     console.log(response.data);
    //                     select.append('<option value="' + key + '">' + value + '</option>');
    //                 });


    //             }
    //             loader.hide();
    //         },
    //         error: function(data) {
    //             console.log('Error:', data);
    //         }
    //     });


    // }



    // $('#emp_search').on('click', function() {
    //     const url = 'filterepmloyer';
    //     var formdata = $('#emp_filter').serialize();
    //     var pairs = formdata.split('&');
    //     // var formDataObject = {};

    //     for (var i = 0; i < pairs.length; i++) {
    //         var pair = pairs[i].split('=');
    //         var key = decodeURIComponent(pair[0]);
    //         var value = decodeURIComponent(pair[1]);
    //         formDataObject[key] = value;
    //     }
    //     // formDataObject['token'] = getCookie('d_token');
    //     // formDataObject['device_id'] = 0;

    //     // Destroy the existing DataTable before reinitializing
    //     var existingTable = $('#emp-table').DataTable();
    //     if (existingTable) {
    //         existingTable.destroy();
    //     }

    //     // Initialize the DataTable
    //     new DataTable('#emp-table', {
    //         ajax: {
    //             url: '{{env('API_URL')}}filterepmloyer',
    //             method: 'POST',
    //             dataSrc: "data",
    //             data: formDataObject,
    //         },
    //         columns: [{
    //                 data: null,
    //                 render: function(data, type, row, meta) {
    //                     return meta.row + 1;
    //                 }
    //             },
    //             {
    //                 data: 'emp_full_name'
    //             },
    //             {
    //                 data: 'phone'
    //             },
    //             {
    //                 data: 'email'
    //             },
    //             {
    //                 data: 'status'
    //             },
    //             {
    //                 data: null,
    //                 render: function(data, type, row) {
    //                     // Custom rendering logic goes here
    //                     return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(' + row.id + ')"></i></span>';
    //                 }
    //             }
    //         ],
    //         language: {
    //             loadingRecords: "Please wait - loading...",
    //             lengthMenu: "| View _MENU_ records per page",
    //             zeroRecords: "No Data Found",
    //             infoEmpty: "No records available",
    //             infoFiltered: "(filtered from _MAX_ total records)",
    //             // sProcessing: "<img src='loading.gif'>"
    //         },
    //     });
    // });



    // $('#add_saveBtn').on('click', function() {
    //     loader.show();
    //     const url = 'createemployer';
    //     var formdata = $('#add_employeeForm').serialize();
    //     var pairs = formdata.split('&');
    //     // var formDataObject  = {};
    //     for (var i = 0; i < pairs.length; i++) {
    //         var pair = pairs[i].split('=');
    //         var key = decodeURIComponent(pair[0]);
    //         var value = decodeURIComponent(pair[1]);
    //         formDataObject[key] = value;
    //     }
    //     // formDataObject['token'] = getCookie('d_token');
    //     // formDataObject['device_id'] = 0;
    //     var settings = {
    //         "url": "{{env('API_URL')}}" + url,
    //         "method": "POST",
    //         "timeout": 0,
    //         "headers": {
    //             "Content-Type": "application/json"
    //         },
    //         "data": JSON.stringify(formDataObject),
    //     };
    //     $.ajax(settings).done(function(response) {
    //         if (response['status'] == 200) {
    //             swalalertsuccess(response['message']);
    //             $('#add_employeeForm')[0].reset();
    //             // $('#add_cus_form-modal').modal('hide')
    //             $('#add_cus_form-modal').css('display', 'none')
    //             showlist()
    //             // Swal.fire({
    //             //           position: "top-right",
    //             //           icon: "success",
    //             //           title: response['message'],
    //             //           showConfirmButton: false,
    //             //           timer: 1500
    //             //       }).then(function() {
    //             //         location.reload()
    //             //     });
    //         }
    //         if (response['status'] == 400) {
    //             errornotify(response)
    //         }
    //         if (response['status'] == 500) {
    //             warningClick('Error', response['error'], "danger")
    //         }
    //         if (response['status'] == 401) {
    //             unauth()
    //         }
    //         loader.hide();
    //     });
    // })

    // $('#edit_saveBtn').on('click', function() {
    //     loader.show();
    //     const url = 'updateemployer';
    //     var formdata = $('#edit_employeeForm').serialize();
    //     var pairs = formdata.split('&');
    //     // var formDataObject  = {};

    //     for (var i = 0; i < pairs.length; i++) {
    //         var pair = pairs[i].split('=');
    //         var key = decodeURIComponent(pair[0]);
    //         var value = decodeURIComponent(pair[1]);
    //         formDataObject[key] = value;
    //     }
    //     // formDataObject['token'] = getCookie('d_token');
    //     // formDataObject['device_id'] = 0;
    //     var settings = {
    //         "url": "{{env('API_URL')}}" + url,
    //         "method": "POST",
    //         "timeout": 0,
    //         "headers": {
    //             "Content-Type": "application/json"
    //         },
    //         "data": JSON.stringify(formDataObject),
    //     };
    //     $.ajax(settings).done(function(response) {
    //         if (response['status'] == 200) {
    //             swalalertsuccess(response['message']);
    //             $('#edit_employeeForm')[0].reset();
    //             showlist()
    //             $('#edit_cus_form-modal').modal('hide')
    //             // Swal.fire({
    //             //           position: "top-right",
    //             //           icon: "success",
    //             //           title: response['message'],
    //             //           showConfirmButton: false,
    //             //           timer: 1500
    //             //       }).then(function() {
    //             //         location.reload()
    //             //     });
    //         }
    //         if (response['status'] == 400) {
    //             errornotify(response)
    //         }
    //         if (response['status'] == 500) {
    //             warningClick('Error', response['error'], "danger")
    //         }
    //         if (response['status'] == 401) {
    //             unauth()
    //         }
    //         loader.hide();
    //     });
    // })

    // function delete_employee(id) {
    //     const url = 'deleteemployer';
    //     //   var formDataObject  = {};
    //     //   formDataObject['token'] = getCookie('d_token');
    //     //   formDataObject['device_id'] = 0;
    //     formDataObject['emp_id'] = id;
    //     var settings = {
    //         "url": "{{env('API_URL')}}" + url,
    //         "method": "POST",
    //         "timeout": 0,
    //         "headers": {
    //             "Content-Type": "application/json"
    //         },
    //         "data": JSON.stringify(formDataObject),
    //     };
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: 'You won\'t be able to revert this!',
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonText: 'Yes, delete it!',
    //         cancelButtonText: 'No, cancel!',
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             $.ajax(settings).done(function(response) {
    //                 if (response['status'] == 200) {
    //                     swalalertsuccess(response['message']);
    //                     showlist();
    //                     //   Swal.fire({ 
    //                     //              position: "top-right",
    //                     //              icon: "success",
    //                     //              title: response['message'],
    //                     //              showConfirmButton: false,
    //                     //              timer: 1500
    //                     //          }).then(function() {
    //                     //           location.reload()
    //                     //       });
    //                 }
    //                 if (response['status'] == 400) {
    //                     warningClick('Error', response['message'], "danger")
    //                 }
    //                 if (response['status'] == 500) {
    //                     warningClick('Error', response['error'], "danger")
    //                 }
    //                 if (response['status'] == 401) {
    //                     unauth()
    //                 }
    //             });

    //         } else if (result.dismiss === Swal.DismissReason.cancel) {
    //             //Swal.fire('Cancelled', 'Your data is safe.', 'error');

    //             swalalerterror('Your data is safe.');
    //         }
    //     });
    // }

    // $('#reset_emp_filter').on('click', function() {
    //     $("#emp_filter")[0].reset();
    //     showlist()
    // })

    // // Ajax for Password change
    // $('#paswordsaveBtn').click(function(e) {
    //     e.preventDefault();

    //     const url = 'passwordchange';
    //     const form = $('#changePasswordForm')[0]; // Get the form element
    //     const formData = new FormData(form); // Create a FormData object with the form data

    //     formData.forEach(function(value, key) {
    //         formDataObject[key] = value;
    //     });
    //     // console.log(formDataObject);

    //     // Add additional data to formData
    //     // formData.append('token', getCookie('d_token'));
    //     // formData.append('device_id', 0);

    //     $.ajax({
    //         url: "{{env('API_URL')}}" + url,
    //         type: "POST",
    //         data: JSON.stringify(formDataObject), // Send the formData object
    //         contentType: 'application/json',
    //         dataType: 'json', // Set to false for FormData
    //         processData: false, // Prevent jQuery from processing the data
    //         success: function(response) {
    //             // Clear error messages
    //             $('.invalid-password').text('');

    //             // Handle validation errors (status 400)
    //             if (response.status == 400 && response.errors) {
    //                 if (response.errors.change_password.length == 1) {
    //                     $('.invalid-password').text(response.errors.change_password[0]);
    //                 } else if (response.errors.password.length > 1) {
    //                     $('.invalid-password').html(response.errors.change_password[0] + '<br />' + response.errors.change_password[1]);
    //                 }
    //             }
    //             // Handle error without validation (status 400)
    //             if (response.status == 400 && !response.errors) {
    //                 //Swal.fire("Error", "Password not changed", "error");
    //                 swalalerterror('Password not changed')

    //             }
    //             // Handle success (status 200)
    //             if (response.status == 200) {
    //                 $('#changePasswordForm').trigger("reset");
    //                 $('#form-modal').modal('hide');
    //                 if (response.isUpdated) {
    //                     swalalertsuccess('Password changed successfully');

    //                     // Swal.fire({
    //                     //     position: 'top-end',
    //                     //     icon: 'success',
    //                     //     title: 'Updated',
    //                     //     text: 'Password changed successfully',
    //                     //     showConfirmButton: false,
    //                     //     timer: 2000
    //                     // });
    //                 }
    //             }
    //         },
    //         error: function(data) {
    //             console.log('Error:', data);
    //         }
    //     });
    // });

    // //password modal show
    // function employeechangepassword(id) {
    //     loader.show();
    //     var url = 'passwordShow';
    //     //   var formDataObject  = {};
    //     //   formDataObject['token'] = getCookie('d_token');
    //     //   formDataObject['device_id'] = 0;
    //     formDataObject['emp_id'] = id;
    //     var settings = {
    //         "url": "{{env('API_URL')}}" + url,
    //         "method": "POST",
    //         "timeout": 0,
    //         "headers": {
    //             "Content-Type": "application/json"
    //         },
    //         "data": JSON.stringify(formDataObject),
    //     };
    //     $.ajax(settings).done(function(response) {
    //         if (response['status'] == 200) {
    //             AssignValues1(response['data'])
    //             $('#form-modal').modal('show')
    //         }
    //         if (response['status'] == 400) {
    //             warningClick('Error', response['message'], "danger")
    //         }
    //         if (response['status'] == 500) {
    //             warningClick('Error', response['error'], "danger")
    //         }
    //         if (response['status'] == 401) {
    //             unauth()
    //         }
    //         loader.hide();
    //     });
    // }

    // function AssignValues1(data) {
    //     //console.log(data)
    //     $('#password_user_id').val(data.user_id);
    // }
</script>