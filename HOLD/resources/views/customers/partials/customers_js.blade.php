<script>
    var isvalid = true;

    $(document).ready(function () {


        // phoneCode() 


        TemplateList(); 



    });

    $(function () {



        showlist()



        $('#cus_search').on('click', function () {

            const url = 'filterepmloyer';

            var formdata = $('#cus_filter_form').serialize();

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



            console.log(formDataObject);



            // Destroy the existing DataTable before reinitializing

            var existingTable = $('#cus-table').DataTable();

            if (existingTable) {

                existingTable.destroy();

            }



            // Initialize the DataTable

            new DataTable('#cus-table', {

                ajax: {

                    url: '{{env('API_URL')}}' + 'filteremployer',

                    method: 'POST',

                    dataSrc: "data",

                    data: formDataObject,

                },

                columns: [

                    {

                        data: null,

                        render: function (data, type, row, meta) {

                            return meta.row + 1; // Row number

                        }

                    },

                    {

                        data: null,

                        orderable: false,

                        searchable: false,

                        render: function (data, type, row) {

                            if (row && row.id) {

                                return `<input type="checkbox" class="row-checkbox" data-id="${row.id}">`;

                            } else {

                                return '';

                            }

                        }

                    },



                    { data: 'id' },

                    { data: 'f_name' },

                    // { data: 'company_name' },

                    { data: 'phone' },

                    { data: 'email' },

                    {

                        data: null,

                        render: function (data, type, row) {

                            return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="cus_del(' + row.id + ')"></i></span>';

                        }

                    }

                ],

                responsive: {

                    details: {

                        type: 'column',

                        target: 'tr'

                    }

                }

            });

        });





        //Modal Form Trigger

        $('#addCustomer').click(function () {

            $('#customer_id').val('');

            $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Save");

            $('#customerForm').trigger("reset");

            $('#form-modal').modal('show');

        });



        // Ajax for Save and Update

        $('#saveBtn').click(function (e) {

            e.preventDefault();



            const url = 'createcustomer'; 

            var formdata = $('#customerForm').serialize();

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



            $.ajax({

                data: formDataObject,

                url: "{{env('API_URL')}}" + url,

                type: "POST",

                dataType: 'json',

                success: function (response) {

                    if (response['status'] == 400) {

                        errornotify(response)

                    }

                    if (response['status'] == 500) {

                        warningClick('Error', response['error'], "danger")

                    }

                    if (response['status'] == 401) {

                        unauth()

                    }



                    if (response.status == 200) {

                        $('#customerForm').trigger("reset");

                        $('#form-modal').modal('hide');

                        // table.draw();

                        if ($('#saveBtn').html() == '<i class="fa fa-save"></i>&nbsp; Save') {

                            Swal.fire({

                                position: 'top-end',

                                icon: 'success',

                                title: 'Added',

                                text: 'New Customer added successfully',

                                showConfirmButton: false,

                                timer: 2000,

                            }).then(function () {

                                showlist()

                            })



                        } else {

                            Swal.fire({

                                position: 'top-end',

                                icon: 'success',

                                title: 'Updated',

                                text: 'Customer updated successfully',

                                showConfirmButton: false,

                                timer: 2000,

                            }).then(function () {

                                showlist()

                            })



                        }

                    }

                },

                error: function (data) {

                    console.log('Error:', data);

                }

            });

        });



        // $('body').on('click', '#generate-excel', function(){

        //     const base_url = '{{ url('customer-ecxel-export') }}'

        //     let name_filter = $('#name_filter').val();

        //     let email_filter = $('#email_filter').val();

        //     let phone_filter = $('#phone_filter').val();

        //     let link_query = ''

        //     link_query = base_url + '?name=' + name_filter + '&email=' + email_filter + '&phone_no=' + phone_filter

        //     $('#generate-excel').attr("href", link_query)

        //     $('#generate-link').attr("href", '')

        //     console.log(link_query)

        // });



        //Reset filter values

        $('#reset_filter').on('click', function () {

            $('#cus_filter_form')[0].reset()

            showlist()

        })

    })



    // function showlist(){

    //              var formDataObject  = {};

    //              formDataObject['token'] = getCookie('d_token');

    //              formDataObject['device_id'] = 0;

    //              var existingTable = $('#cus-table').DataTable();

    //              if (existingTable) {

    //                  existingTable.destroy();

    //              }

    //              new DataTable('#cus-table', {

    //                  processing: true,

    //                  searching: false,

    //               ajax: {

    //                   url: '{{env('API_URL')}}customerlist',

    //                   method: 'POST',

    //                   dataSrc:"data",

    //                   data: formDataObject,

    //               },

    //               columns: [

    //                   { 

    //                   data: null,

    //                   render: function(data, type, row, meta) {

    //                     return meta.row + 1;

    //                   }

    //                   },

    //                   {

    //             data: 'checkbox', // Add the checkbox column

    //             orderable: false,

    //             searchable: false,

    //             render: function(data, type, row) {

    //                 return data; // Render the checkbox HTML from the backend

    //             }

    //         },

    //                   { data: 'id' },

    //                   { data: 'f_name' },

    //                   { data: 'company_name' },

    //                   { data: 'phone' },

    //                   { data: 'email' },

    //                   {

    //                       data: null,

    //                       render: function(data, type, row) {

    //                           // Custom rendering logic goes here

    //                           return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="cus_del(' + row.id + ')"></i></span>';

    //                       }

    //                   }

    //               ],

    //               responsive: {

    //               details: {

    //                   type: 'column',

    //                   target: 'tr'

    //               }

    //           }

    //           });

    //      }

    //     function showlist() {

    //     var formDataObject = {};

    //     formDataObject['token'] = getCookie('d_token');

    //     formDataObject['device_id'] = 0;



    //     var existingTable = $('#cus-table').DataTable();

    //     if (existingTable) {

    //         existingTable.destroy();

    //     }



    //     new DataTable('#cus-table', {

    //         processing: true,

    //         searching: false,

    //         ajax: {

    //             url: '{{env('API_URL')}}customerlist',

    //             method: 'POST',

    //             dataSrc: "data",

    //             data: formDataObject,

    //         },

    //         columns: [

    //             {

    //                 data: null,

    //                 render: function(data, type, row, meta) {

    //                     return meta.row + 1;

    //                 }

    //             },

    // { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },





    //             { data: 'id' },

    //             { data: 'f_name' },

    //             { data: 'company_name' },

    //             { data: 'phone' },

    //             { data: 'email' },

    //             {

    //                 data: null,

    //                 render: function(data, type, row) {

    //                     return '<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(' + row.id + ')"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="cus_del(' + row.id + ')"></i></span>';

    //                 }

    //             }

    //         ],

    //         responsive: {

    //             details: {

    //                 type: 'column',

    //                 target: 'tr'

    //             }

    //         }

    //     });

    // }

    function showlist() {

        var formDataObject = {

            token: getCookie('d_token'),

            device_id: 0

        };



        if ($.fn.DataTable.isDataTable('#cus-table')) {

            $('#cus-table').DataTable().destroy();

        }



        $.ajax({

            url: '{{ env("API_URL") }}customerlist',

            method: 'POST',

            data: formDataObject,

            success: function (json) {

                // Reload both DataTables with the same data

                $('#cus-table').DataTable().clear().rows.add(json.data).draw();

                let otherTable = $('#customer_list').empty();

                $.each(json.data, function (index, value) {

                    otherTable.append(`

                    <tr>

                        <td><input type="checkbox" class="customer-checkbox" value="${value.id}" name="customers[]"></td>

                        <td>${value.f_name}</td>

                        <td>${value.email}</td>

                    </tr>

                `);

                });

            },

            error: function (xhr) {

                console.error("Failed to load customers:", xhr.responseText);

            }

        });



        $('#cus-table').DataTable({

            data: [],

            columns: [

                {

                    data: null,

                    render: function (data, type, row, meta) {

                        return meta.row + 1;

                    }

                },

                {

                    data: null,

                    orderable: false,

                    searchable: false,

                    render: function (data, type, row) {

                        if (row && row.id) {

                            return `<input type="checkbox" class="row-checkbox" data-id="${row.id}">`;

                        } else {

                            return '';

                        }

                    }

                },

                { data: 'id' },

                { data: 'f_name' },



                { data: 'phone' },

                { data: 'email' },

                {

                    data: null,

                    render: function (data, type, row) {

                        return `

                        <span style="padding: 8px;">

                            <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(${row.id})"></i>

                        </span>

                        <span style="padding: 8px;">

                            <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

                        </span>`;

                    }

                }

            ],

        });



        // $('#second-table').DataTable({

        //     data: [],

        //     columns: [/* your columns */]

        // });







        // $('#cus-table').DataTable({

        //     processing: true,

        //     searching: true,

        //     ajax: {

        //         url: '{{env('API_URL')}}customerlist',

        //         method: 'POST',

        //         data: formDataObject,

        //         dataSrc: function(json) {

        //             console.log("Received JSON:", json);

        //             if (!json || !json.data) {

        //                 console.error("No 'data' property in the response.");

        //                 return [];

        //             }



        //             // Populate second (non-DataTable) table

        //             let otherTable = $('#customer_list').empty();

        //             $.each(json.data, function(index, value) {

        //                 otherTable.append(`

        //                     <tr>

        //                         <td><input type="checkbox" class="customer-checkbox" value="${value.id}"></td>

        //                         <td>${value.name}</td>

        //                         <td>${value.email}</td>

        //                     </tr>

        //                 `);

        //             });



        //             return json.data; // Return for the DataTable

        //         },



        //         error: function(xhr, error, thrown) {

        //             console.error("Error in AJAX request:", xhr.responseText); // Log any errors

        //         }

        //     },

        //     columns: [

        //         {

        //             data: null,

        //             render: function(data, type, row, meta) {

        //                 return meta.row + 1;

        //             }

        //         },

        //         {

        //             data: null,

        //             orderable: false,

        //             searchable: false,

        //             render: function(data, type, row) {

        //                 if (row && row.id) {

        //                     return `<input type="checkbox" class="row-checkbox" data-id="${row.id}">`;

        //                 } else {

        //                     return '';

        //                 }

        //             }

        //         },

        //         { data: 'id' },

        //         { data: 'f_name' },



        //         { data: 'phone' },

        //         { data: 'email' },

        //         {

        //             data: null,

        //             render: function(data, type, row) {

        //                 return `

        //                     <span style="padding: 8px;">

        //                         <i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="customeredit(${row.id})"></i>

        //                     </span>

        //                     <span style="padding: 8px;">

        //                         <i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px;border-radius: 6px;" onclick="cus_del(${row.id})"></i>

        //                     </span>`;

        //             }

        //         }

        //     ],

        //     language: {

        //         emptyTable: "No records found"

        //     },

        //     drawCallback: function(settings) {

        //         var api = this.api();

        //         var data = api.rows().data();

        //         if (data.length === 0) {

        //             $('.row-checkbox').hide();

        //         }

        //     },

        //     responsive: {

        //         details: {

        //             type: 'column',

        //             target: 'tr'

        //         }

        //     }

        // });

    }







    function AssignValues(data) {

        // dial_code_store   = remove(+)dial_code_store
        //    phone_no  = dial_code_store val(data.phone);

        let cleanedDialCode = dial_code_store.replace("+", "");
        let phoneNumber = String(data.phone);
        // Remove prefix if exists
        if (phoneNumber.startsWith(cleanedDialCode)) {
            phoneNumber = phoneNumber.slice(cleanedDialCode.length);
        }

        console.log('phone val:', phoneNumber);// Output: 6543123456

        $('#customer_id').val(data.id);

        $('#first_name').val(data.f_name);

        $('#cmpny_name').val(data.company_name);

        $('#phone').val(phoneNumber);

        $('#email').val(data.email);

        $('#address1').val(data.address1);

        $('#remarks').val(data.remark);

    }



    function customeredit(id) {

        const url = 'editcustomer';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['customer_id'] = id;

        var settings = {

            "url": "{{ env('API_URL') }}" + url,

            "method": "POST",

            "timeout": 0,

            "headers": {

                "Content-Type": "application/json"

            },

            "data": JSON.stringify(formDataObject),

        };

        $.ajax(settings).done(function (response) {

            if (response['status'] == 200) {

                $('#saveBtn').html("<i class=\"fa fa-save\"></i>&nbsp; Update");

                $('#form-modal').modal('show');

                AssignValues(response['data'])

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



    function cus_del(id) {

        const url = 'deletecustomer';

        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');

        formDataObject['device_id'] = 0;

        formDataObject['customer_id'] = id;

        var settings = {

            "url": "{{env('API_URL')}}" + url,

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

                    if (response['status'] == 200) {

                        Swal.fire({

                            position: "top-right",

                            icon: "success",

                            title: response['message'],

                            showConfirmButton: false,

                            timer: 1500

                        }).then(function () {

                            showlist()

                        });

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

            //  else if (result.dismiss === Swal.DismissReason.cancel) {

            //   Swal.fire('Cancelled', 'Your data is safe.', 'error');

            //  }

        });

    }





    //         $('body').on('click', '#Emailsend', function() {





    //     $('#partner_driver_data').html('');

    //  formDataObject['token'] = getCookie('d_token');

    //           formDataObject['device_id'] = 0;

    //     var selectedIds = [];

    //     $('.ispublish:checked').each(function(){

    //         selectedIds.push($(this).data('id'));

    //     });



    //     if (selectedIds.length > 0) {

    //         $.ajax({

    //             type: "POST",

    //             url: '{{env('API_URL')}}get-emails',  

    //             data: {

    //                 id: selectedIds,

    //             },

    //             success: function(response) {

    //                 if (response.emails) {

    //                     console.log(response.emails);                   

    //                   // $('#customer_emails').val(Object.values(response.emails).join(', '));

    // 					 $('#customer_emails').val(Object.values(response.emails.map(email => email.email)).join(', '));



    // 					   let customerNames = response.emails.map(email => email.f_name).join(', ');

    // 					console.log(customerNames);

    //         $('#customernames').text(customerNames);

    // 					tinymce.get("customer_email_send").setContent($('#customer_email_send').html());

    // 					  $('#Sendemail').modal('show');  





    //                 } else {

    //                     alert("No emails found"); 

    //                 }

    //             },

    //             error: function(xhr, status, error) {

    //                 console.error('AJAX Error:', status, error);  

    //                 alert("An error occurred"); 

    //             }

    //         });

    //     } else {

    //         alert("At least choose one job");  

    //     }

    // });



    // $('body').on('click', '#Emailsend', function() {

    //     $('#partner_driver_data').html('');



    //     // Initialize formDataObject

    //     let formDataObject = {};

    //     formDataObject['token'] = getCookie('d_token');

    //     formDataObject['device_id'] = 0;



    //     var selectedIds = [];

    //     $('.ispublish:checked').each(function() {

    //         selectedIds.push($(this).data('id'));

    //     });



    //     if (selectedIds.length > 0) {

    //         // Add selectedIds to formDataObject

    //         formDataObject['id'] = selectedIds;



    //         $.ajax({

    //             type: "POST",

    //             url: '{{env('API_URL')}}get-emails',

    //             data: formDataObject,  // Send formDataObject directly

    //             success: function(response) {

    //                 if (response.emails) {

    //                     console.log(response.emails);

    //                     $('#customer_emails').val(Object.values(response.emails.map(email => email.email)).join(', '));



    //                     let customerNames = response.emails.map(email => email.f_name).join(', ');

    //                     console.log(customerNames);

    //                     $('#customernames').text(customerNames);

    //                     tinymce.get("customer_email_send").setContent($('#customer_email_send').html());

    //                     $('#Sendemail').modal('show');  

    //                 } else {

    //                     alert("No emails found"); 

    //                 }

    //             },

    //             error: function(xhr, status, error) {

    //                 console.error('AJAX Error:', status, error);  

    //                 alert("An error occurred"); 

    //             }

    //         });

    //     } else {

    //         alert("At least choose one job");  

    //     }

    // });





    $('#groupForm').on('submit', function (e) {

        e.preventDefault();



        let groupName = $('#groupName').val();

        let selectedCustomers = [];





        $('input[name="customers[]"]:checked').each(function () {

            selectedCustomers.push($(this).val());

        });







        if (!groupName || selectedCustomers.length === 0) {

            // alert('');

            warningClick('Error', 'Please enter group name and select at least one customer.', "danger")

            return;

        }





        $.ajax({

            url: '{{env('API_URL')}}create-groups',

            type: 'POST',

            data: {

                group_name: groupName,

                token: getCookie('d_token'),

                customers: selectedCustomers

            },

            success: function (response) {

                // alert('Group created successfully!');

                if (response.status) {

                    warningClick('Success', response.message, "success")

                } else {

                    warningClick('Error', response.message, "danger")

                }



                let grp_sel = $('#groupSelect').empty();



                grp_sel.append(`<option selected disabled>Choose...</option>`);



                $.each(response.data, function (index, value) {



                    let row = `<option value="${value.id}">${value.group_name}</option>`;

                    grp_sel.append(row);



                });

                //     $.each(response.data, function(index, value) {
                //     let row = `
                //         <li class="list-group-item d-flex justify-content-between align-items-center">
                //             ${value.group_name}
                //             <button class="btn btn-sm btn-danger" onclick="deleteGroup(${value.id})">
                //                 <i class="bi bi-trash"></i>
                //             </button>
                //         </li>
                //     `;
                //     $('#groupList').append(row);
                // });





                $('#winningno').modal('hide');

                $('#groupModal').modal('hide');

                $('#groupForm')[0].reset();

            },

            error: function (xhr) {

                // alert('Something went wrong!');

                // console.log(xhr.responseText);

            }

        });

    });


var dial_code_store = '+'+@json($myDial);

    function phoneCode() {
        const url = 'phoneCode';
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
        $.ajax(settings).done(function(response) {
            console.log(response);
            if (response['status'] == 200) {
                $('#country_code').text(response.data);
                dial_code_store = response.data;
                // $('#country_code_whatsapp').val(response.data);
                $('#hidden_phoneCode').val(response.data);
                // $('#edit_country_code').val(response.data);
                // $('#edit_country_code_whatsapp').val(response.data);
                // $('#edit_cus_form-modal').modal('show')
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




    $('body').on('click', '#Emailsend', function () {   

        // console.log('hhhhhhhhohhhhhhhhhhiii')

        $('#partner_driver_data').html(''); // Clear previous data

        let formDataObject = {

            token: getCookie('d_token'),

            device_id: 0

        };



        // Collect selected IDs

        let selectedIds = [];

        $('.row-checkbox:checked').each(function () {

            selectedIds.push($(this).data('id'));

        });

        // console.log('count vals',selectedIds);

        if (Array.isArray(selectedIds) || selectedIds.length > 0) {

            $('#loading-spinner').show();

            $('#Sendemail').modal('show');



            $.ajax({

                type: "POST",

                url: '{{env('API_URL')}}get-emails',

                data: {

                    id: selectedIds,

                    ...formDataObject

                },

                success: function (response) {

                    $('#loading-spinner').hide(); // Hide loading spinner



                    if (response.emails && response.emails.length > 0) {



                        $('#customer_emails').val(response.emails.map(email => email.email).join(', '));





                        let customerNames = response.emails.map(email => email.f_name).join(', ');

                        $('#customernames').val(customerNames);



                        let editor = tinymce.get("customer_email_send");

                        if (editor) {

                            editor.setContent('');

                        } else {

                            console.warn("TinyMCE editor not initialized for #customer_email_send");

                        }





                        $('#Sendemail').modal('show');

                    } else {

                        // alert("No emails found");

                    }


                    // console.log('grp vals',response.group);


                    if (response.group) {

                        let grp_sel = $('#groupSelect').empty(); // Clear existing options



                        // Add a default option back

                        grp_sel.append(`<option selected disabled>Choose...</option>`);



                        $.each(response.group, function (index, value) {

                            let row = `<option value="${value.id}">${value.group_name}</option>`;

                            grp_sel.append(row); // Correct way to append HTML string

                        });

                    }



                },

                error: function (xhr, status, error) {

                    $('#loading-spinner').hide(); // Hide loading spinner

                    console.error('AJAX Error:', status, error);

                    alert("An error occurred");

                }

            });

        } else {

            // Swal.fire({

            //     icon: 'warning',

            //     title: 'Warning',

            //     text: 'Please select atleast one customer.',

            // });

            $('#Sendemail').modal('show');

        }

    });



    function openDeleteModal() {
        // Call your 'index' function here
        customer_list_delete();  // This is where the 'index' function will be called

        // Show the modal
        $('#groupModalDelete').modal('show');
    }

    function customer_list_delete() {

        // console.log("Index function called!");

        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;

        $.ajax({
            method: 'POST',
            url: '{{ env("API_URL") }}customer_list_show',
            data: formDataObject,
            success: function (response) {
                // Assuming 'response' is an array of customer data
                $.each(response.data, function (index, item) {
                    // Ensure it is an array if 'customer_ids' exists

                    var serialNumber = index + 1;

                    var row = '<tr>';
                    row += '<td>' + serialNumber + '</td>'; // Display S.No
                    row += '<td>' + item.group_name + '</td>';
                    row += '<td><button class="btn btn-danger" onclick="deleteCustomer_group(event,' + item.id + ')">Delete</button></td>';
                    row += '</tr>';

                    // Append the new row to the table body
                    $('#customer_list_delete').append(row);
                });
            },
            error: function (xhr, status, error) {
                // Handle error here
            }
        });


    }
    function deleteCustomer_group(e, id) {
        e.preventDefault();
        const url = 'delete_group_list';
        var formDataObject = {};

        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
        formDataObject['customer_id'] = id;

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {   // ✅ Only if user clicks 'Yes, delete it!'
                $.ajax({
                    url: "{{ env('API_URL') }}" + url,
                    method: "POST",
                    data: formDataObject,
                    success: function (response) {
                        if (response.status == 200) {
                            warningClick('Deleted', 'Group has been deleted successfully', 'success');

                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else if (response.status == 400) {
                            warningClick('Error', 'Error deleting the group', 'danger');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        alert('Error deleting!');
                    }
                });
            }
        });
    }





    function TemplateList() {

        var formDataObject = {

            token: getCookie('d_token'),

            device_id: 0

        };



        // Make an AJAX request to fetch the templates

        $.ajax({

            url: '{{env('API_URL')}}' + 'Template',

            method: 'POST', // Use POST or GET depending on the API requirement

            data: formDataObject,

            success: function (response) {

                if (response.status === 200) {

                    var templates = response.data;

                    PopulateSelect(templates); // Populate the dropdown with templates

                    console.log(response);

                } else {

                    console.error('Error:', response.message);

                }

            },

            error: function (error) {

                console.error('Error fetching data:', error);

            }

        });

    }



    function changeField(a) {



        if (a == 'single') {

            $('.hideSingle').show();

            $('.hideGroup').hide();

        } else if (a == 'group') {

            $('.hideGroup').show();

            $('.hideSingle').hide();



        }



    }



    function PopulateSelect(templates) {

        var select = $('#templateSelect');

        select.empty(); // Clear existing options



        // Add default option

        select.append('<option value="">Select Template</option>');



        if (templates.length > 0) {

            templates.forEach(function (template) {

                var option = $('<option></option>');

                option.val(template.description); // Set value to template description (email body)

                option.text(template.template_name); // Set text to template name

                select.append(option);

            });

        } else {

            select.append('<option value="">No templates found.</option>');

        }

    }



    // Handle template selection and update the email body

    $('#templateSelect').on('change', function () {

        var selectedTemplateDescription = $(this).val(); // Get the selected template description

        if (selectedTemplateDescription) {

            $('#customer_email_send').html(selectedTemplateDescription); // Update the email body with the selected template description

        } else {

            $('#customer_email_send').html(''); // Clear the email body if no template is selected

        }

    });







    $('#primaryBtn').click(function (e) {

        e.preventDefault();


        var button = $(this);
        var spinner = button.find('.spinner-border');
        var buttonText = button.find('.button-text');
        
        var form = $('#emailForm')[0];

        if (!form) {

            return;

        }

        var formdata = new FormData(form);

        formdata.append('token', getCookie('d_token'));

        formdata.append('device_id', 0);

        var emailBody = $('#customer_email_send').html().trim();

        formdata.append('description', emailBody);

        var selectedTemplateValue = $('#templateSelect').val();

        var selectedTemplateName = $('#templateSelect').find('option:selected').text();

        formdata.append('template_name', selectedTemplateName);
    
        formdata.forEach(function(value, key){
            // console.log(key + ": " + value);
        });
        
        
        //validation 
        var messageType = formdata.get('message_type');
        var email_sub = $('#email_subject').val();
        var customer_name = $('#customernames').val();
        var customer_mail = $('#customer_emails').val();
        var group_name_show = $('#groupSelect').val();
        // console.log('current value',group_name_show);

        if(messageType == 'single'){
           if (email_sub == '') {
            warningClick('Required', 'Email Subject is Required', 'danger');
            isvalid = false;
            }else if (customer_name == '') {
                warningClick('Required', 'Customer Name is Required', 'danger');
                isvalid = false;
            }else if (customer_mail == '') {
                warningClick('Required', 'Email is Required', 'danger');
                isvalid = false;
            }
            // else if (emailBody == '') {
            //     warningClick('Required', 'Email Description is Required', 'danger');
            //     $('#customer_email_send').html('');
            //     isvalid = false;
            // }
             else {
                isvalid = true;
            }

        }
         else if(messageType == 'group'){
            if (email_sub == '') {
            warningClick('Required', 'Email Subject is Required', 'danger');
            isvalid = false;
            }else if (group_name_show === null) {
                warningClick('Required', 'Group Name is Required', 'danger');
                isvalid = false;
            }
            // else if (emailBody == '') {
            //     warningClick('Required', 'Email Description is Required', 'danger');
            //     $('#customer_email_send').html('');
            //     isvalid = false;
            // }
             else {
                isvalid = true;
            }
        }

        spinner.show();
        buttonText.hide();
        $('#primaryBtn').attr('disabled', true);
        console.log('ajax',isvalid);
        if (isvalid) {
            $.ajax({

                url: '{{env('API_URL')}}' + 'CustomerEmailBooking',

                type: "POST",

                data: formdata,

                processData: false,

                contentType: false,

                dataType: 'json',

                success: function (response) {

                    console.log(response);
                    $('#primaryBtn').attr('disabled', false);

                    if (response.status === 200) {
                        // $('#customer_email_send').html('');
                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Email Sent Successfully',

                            showConfirmButton: false,

                            timer: 2000,

                        }).then(function () {

                            // window.location.reload();

                        });
                        spinner.hide();
                        buttonText.show();

                    } else {
                        // $('#customer_email_send').html('');
                        Swal.fire({

                            icon: 'warning',

                            title: 'Warning',

                            text: response.message || 'Unexpected response received',

                        });
                        spinner.hide();
                        buttonText.show();
                        $('#primaryBtn').attr('disabled', false);

                    }

                },

                error: function (xhr, status, error) {

                    console.log('Error:', error);

                    Swal.fire({

                        icon: 'error',

                        title: 'Oops...',

                        text: 'Something went wrong! ' + xhr.responseText,

                    });
                    spinner.hide();
                    buttonText.show();
                    $('#primaryBtn').attr('disabled', false);

                }

            });
        }else {
            // $('#customer_email_send').html('');
            spinner.hide();
            buttonText.show();
            $('#primaryBtn').attr('disabled', false);

        }

    });











    $(document).ready(function () {

        $('#preview_email').click(function () {

            var name = $('#customernames').val();

            var emailBody = $('#customer_email_send').html();

            var recipientEmail = $('#customer_emails').val();





            // Prepare the preview content

            var previewContent = `

         <p>Dear ${name}</p>

            <h6>To: ${recipientEmail}</h6>

            

            <hr>

            <div>${emailBody}</div>

        `;



            $('#email_preview_content').html(previewContent);



            // Show the modal

            $('#previewModal').modal('show');

        });





        $('#customerSearch').on('keyup', function () {

            var search = $(this).val().toLowerCase();

            $('#customer_list .customer-item').each(function () {

                var rowText = $(this).text().toLowerCase();

                $(this).toggle(rowText.includes(search));

            });

        });

    });



</script>