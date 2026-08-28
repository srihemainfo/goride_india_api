@extends('dashboard-layout.index')
@section('content')


<style>
 @media (max-width: 768px) {
    .modal-dialog-aside {
        width: 100% !important;
        height: 100% !important;
        margin: 0;
        max-width: none;
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
}
</style>

<!-- Error Message -->
<div class="alert alert-danger" id="no-data-message" style="display: none;"></div>
<!-- Main Data Section -->
<div id="data-section" style="display: none;">
    <!-- Existing Content -->
    <div class="col-sm-12 main-card mb-2 card">
        <div class="x_content-container">
            <div class="col-sm-12 main-card mb-2 card">
                <div class="card-header">
                    <h4 class="card-title">Website Settings</h4>
                    <div class="btn-actions-pane-right">
                        <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->
                        <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add Website </button>
                    </div>
                </div>
                <div class="card-body">
                <div class="table-responsive">
    <table class="table table-bordered text-nowrap border-bottom" id="emp-table" style="width:100%;">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Company Name</th>
                <th>Phone No.</th>
                <th>Email</th>
                <th>Prefix</th>
                <th>Logo</th>
                <th>Favicon</th>
                <th>Customer Url</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Modals -->
    @include('generalsettings.partials.add_customer_modal')
    @include('generalsettings.partials.edit_employee_modal')
    
</div>

@include('generalsettings.partials.codeView')
@endsection
@section('custom_scripts')
<script>
    $(document).ready(function () {
        $.ajax({
            url: '/check-driver-vehicle',
            method: 'GET',
            success: function (response) {
                if (response.status === 'error') {
                    // Show error message and hide content
                    $('#no-data-message').text(response.message).show();
                    $('#data-section').hide();
                } else {
                    // Show content and hide error message
                    $('#no-data-message').hide();
                    $('#data-section').show();
                }
            },
            error: function () {
                alert('An error occurred while checking data.');
            }
        });
    });
</script>
@include('generalsettings.partials.customers_js')
@endsection
