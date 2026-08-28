@extends('dashboard-layout.index')



@section('content')


    <style>
        #Sendemail.modal .modal-dialog-aside {

            width: 600px !important;

        }

        @media (max-width: 768px) {
            .modal-dialog-aside {
                width: 100% !important;
                height: 100% !important;
                margin: 0;
                max-width: none;
                left: 12px;
            }

            .modal-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .customer-name {
                max-width: 100% !important;
                padding-left: 6px !important;
            }
        }
    </style>

    <div class="col-sm-12 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Customer List</h4>

            <div class="btn-actions-pane-right">

            </div>

        </div>

        <div class="card-body">

            <div class="row">
                
                <div class="col-4">
                    
                    <div class="customer-details" id="customer-details">
                        <p>Name: <span id="customer-name"></span></p>
                        <p>Email: <span id="customer-email"></span></p>
                        <p>Phone No.: <span id="customer-phone"></span></p>
                    </div>

                    
                </div>
                
                <div class="col-8">
                    
                    <div class="table-responsive" >

                        <table class="table" width="100%" id="customer-booking">
        
                            <thead class="table-light">
        
                                <tr>

                                    <th style="width:7%;">#</th>
    
                                    <th style="width:10%;">From</th>
        
                                    <th>To</th>
        
                                    <th>Total</th>
        
                                    <th style="width:10%;">Action</th>
        
                                </tr>
        
                            </thead>
        
                            <tbody></tbody>
        
                        </table>
        
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection 

@section('custom_scripts')
<script>
    let customerDetails = localStorage.getItem("customer_details");

    if (customerDetails) {
        let data = JSON.parse(customerDetails);

        // Populate the customer details
        document.getElementById("customer-name").textContent = data[0].f_name || "Not available";
        document.getElementById("customer-email").textContent = data[0].email || "Not available";
        document.getElementById("customer-phone").textContent = data[0].phone || "Not available";
        
        // Check if the DataTable exists and destroy it
        if ($.fn.dataTable.isDataTable('#customer-booking')) {
            $('#customer-booking').DataTable().destroy();
        }
        
        // Initialize the DataTable
        $('#customer-booking').DataTable({
            data: data,  // Pass the 'data' directly to the DataTable
            columns: [
                { 
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // Row number
                    }
                },
                { data: 'bc_from' },
                { data: 'bc_to' },
                { data: 'bc_total' },
                {
                    data: null,
                    render: function(data, type, row) {
                        // Extract domainName from cookies using a helper function
                        var domainName = getCookie("domainName");
                
                        // Return the view icon with the window.open() function
                        return '<span style="padding: 8px;" title="Preview Booking Details">' +
                            '<i class="fa-solid fa-eye" style="background: blue;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;cursor: pointer;" ' +
                            'onclick="window.open(\'/booking/Preview/'+ row.bc_id +'?d_token=' + domainName + '\', \'_blank\');"></i>' +
                            '</span>';
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

        
        console.log(data); // Output the decoded data to the console
    } else {
        console.log("No customer details found in localStorage.");
    }
</script>
@endsection