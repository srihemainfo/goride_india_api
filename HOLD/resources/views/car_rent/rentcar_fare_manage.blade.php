@extends('dashboard-layout.index')
@section('content')



<!-- New Fare Management Table -->
<div class="col-sm-9 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-4">Fare Management</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table_new" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Car Name</th>
                            <th>Hourly</th>
                            <th>Rate</th>
                            <th>Driver Charge (Night)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data rows will be dynamically inserted here -->
                    </tbody>
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success mb-3'>Update</button>
            </div>
        </div>
    </div>
</div>

<!-- Side Navigation -->
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
        <a class="nav-link active text-dark" id="vert-tabs-right-offer-times-tab" href="/rentcar-fare-manage" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare Management
        </a>
    </div>
</div>

@endsection

@section('custom_scripts')
@include('car_rent.partials.rentcar_faremanage_js')
@endsection
