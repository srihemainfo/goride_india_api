@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Payment List Filter</h4>
    </div>
    <div class="card-body" id="invoiceForm">
        <div class="row">
            <div class="col-sm-12 row mb-2">
                <div class="col-sm-3">
                    <label for="client_name_filter">Customer Name</label>
                    <div class="input-group">
                        <select class="form-control select2" style="width: 100%;" tabindex="-1" id="client_name_filter" name="client_name_filter"  data-placeholder="Search Clients">
                            
                        </select>
                        <input type="hidden" name="name_filter" id="name_filter" value="">
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="booking_between_filter">Date Range</label>
                    <input type="text" class="form-control" style="width: 100%;" id="booking_between_filter" placeholder="Select date range" name="booking_between_filter" value="">
                </div>
                <div class="col-sm-6">
                    <label for="job_no_filter">Job No</label>
                    <div class="input-group">
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="job_no_filter" name="job_no_filter[]" data-control="select2" data-placeholder="Search Jobs" data-hide-search="true" multiple="multiple">
                           
                        </select>
                        <input type="hidden" name="job_filter" id="job_filter" value="">
                    </div>
                </div>
            </div>
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-3">
                    <input type="hidden" name="filter_from_date" id="filter_from_date" value="">
                    <input type="hidden" name="filter_to_date" id="filter_to_date" value="">
                    <button type="button" class="btn btn-primary" value="" id="payment_search"><i class="fa fa-search"></i>&nbsp; Search</button>
                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-12 main-card mb-2 card"  id="booking_table" style="display:none">
    <div class="card-header">
        <h4 class="card-title">Payment List</h4>
        <div class="btn-actions-pane-right">
            <button type="button" class="btn btn-success" id="generateInvoice"><i class="fas fa-plus"></i> Add Invoice </button>
        </div>
    </div>
    <div class="card-body" >
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>Add Invoice</th>
                        <th>Job No</th>
                        <th>Member ID</th>
                        <th>Pickup Date</th> 
                        <th>Client Name</th>
                        <th>Payment Method</th>
                        <th>Payment status</th>
                        <th>Order Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="booking_view">
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-sm-12 main-card mb-2 card"  id="invoice_view" style="display:none">
    <div class="card-header">
        <h4 class="card-title">Invoice View</h4>
        <div class="btn-actions-pane-right">
            <button type="button" class="btn btn-success" id="generatedInvoice"><i class="fas fa-plus"></i> Generate Invoice </button>
        </div>
    </div>
   
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12 row mb-4">
                <div class="col-sm-6">
                    <label for="name">Name: <span class="required">*</span></label>
                    <input type="text" class="form-control" id="driver_name" name="driver_name" value="">
                    <input type="hidden" class="form-control" id="driver_id" name="driver_id" value="">
                    <p class="text-danger invalid-name"></p>
                </div>
                <div class="col-sm-6">
                    <label for="invoice_no">Invoice No.: <span class="required">*</span></label>
                    <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="">
                    <p class="text-danger invalid-no"></p>
                </div>
            </div>
            <div class="col-sm-12 row mb-4">
                <div class="col-sm-6">
                    <label for="driver_address">Address : <span class="required">*</span></label>
                    <textarea type="text" class="form-control" id="driver_address" name="driver_address" value=""></textarea>
                    <p class="text-danger invalid-driver-address"></p>
                </div>
                <div class="col-sm-6">
                    <label for="invoice_date">Invoice Date: <span class="required">*</span></label>
                    <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="">
                    <p class="text-danger invalid-invoice-date"></p>
                </div>
            </div>
            <div class="col-sm-12 row mb-4">
                <div class="col-sm-6">
                    <label for="payment_type">Payment Type: <span class="required">*</span></label>
                    <select type="text" class="form-control" id="payment_type" name="payment_type" value="">
                        <option value=""> --Select-- </option>
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
                    </select>
                    <p class="text-danger invalid-payment-type"></p>
                </div>
                <div class="col-sm-6">
                    <label for="status">Status: <span class="required">*</span></label>
                    <select type="text" class="form-control" id="status" name="status" value="">
                        <option value=""> --Select-- </option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                    <p class="text-danger invalid-status"></p>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" >
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Job No</th>
                        <th>Pickup Date & Time</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="inv_view">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('invoice.partials.invoices_js')
@endsection