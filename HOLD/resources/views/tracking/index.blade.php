@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 alert alert-danger">
    <strong>Note: </strong> After selecting the driver, wait for 20 seconds max to get drivers location. If the location is updated, We will notify every time (next to driver name).
</div>
<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Driver Live Location</h4>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4" id="driver_select">
                <label for="driver_name" class="col-form-label">On Duty Drivers<span class="required">&nbsp;*</span></label>
                <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="driver_name" name="driver_id" data-control="select2" data-placeholder="Select driver for booking" data-hide-search="true">
                        <option value="">-- select driver --</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="map" style="height: 400px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('tracking.partials.live_tracking_js')
@endsection
