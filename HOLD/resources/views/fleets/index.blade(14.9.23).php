
@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    
    <div class="card-header">
        <h4 class="card-title">Fleet List</h4>
        <div class="btn-actions-pane-right">
            @if($IS_CREATABLE)
                <button type="button" class="btn btn-success" id="addFleet"><i class="fas fa-plus"></i> Add Fleet </button>
            @endif  
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Passengers</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Luggage</th>
                        <th>Hand Luggage</th>
                        <th>No. of Seats</th>
                        <th>Child Seats</th>
                        <th>Status</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

    @include('fleets.partials.add_fleet_modal')
@endsection

@section('custom_scripts')
    @include('fleets.partials.fleet_js')
@endsection