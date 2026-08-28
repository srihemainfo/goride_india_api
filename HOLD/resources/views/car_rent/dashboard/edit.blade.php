@extends('dashboard-layout.index')

@section('content')  
<form id="editbookingForm" name="editbookingForm">
    <div class="col-sm-12 main-card mb-3 card">
        <div class="card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hourlydashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hourlydashboard') }}">List Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hourly Booking Edit</li>
                </ol>
            </nav>
            <div class="card-header">
                <h4 class="card-title">Booking Form</h4>
            </div>
            <div class="card-body">
                <div class="row" id="client_info"></div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 main-card mb-3 card" id="journey_container"></div>

    <div class="col-sm-12 main-card mb-3 card" id="car_container">
        <div class="card-header">
            <h4 class="card-title">Car Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <label for="car_type">Car Type <span class="required">*</span></label>
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="car_type" name="car_type" data-control="select2" data-placeholder="Select an option" data-hide-search="true">
                        <option value="">Select</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label for="passenger_count">No of Passengers <span class="required">*</span></label>
                    <select class="form-control" id="passenger_count" name="passenger_count"></select>
                </div>
                <div class="col-sm-2">
                    <label for="luggage_count">Luggage</label>
                    <select class="form-control" id="luggage_count" name="luggage_count"></select>
                </div>
                <div class="col-sm-2">
                    <label for="hand_luggage_count">Hand Luggage</label>
                    <select class="form-control" id="hand_luggage_count" name="hand_luggage_count"></select>
                </div>
            </div>
            <div class="row mt-2" id="child_seat_container"></div>
        </div>
    </div>

    <div class="col-sm-12 main-card mb-3 card" id="outward_container">
        <div class="card-header">
            <h4 class="card-title">Pickup Details</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-4">
                    <label for="pick_up">Pick-up <span class="required">*</span></label>
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="pick_up" name="pick_up" data-control="select2" data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                        <option value=""></option>
                    </select>
                    <p class="text-danger invalid_pick_up"></p>
                </div>
                <div class="col-sm-2">
                    <label for="pickup_date">Pickup Date <span class="required">*</span></label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="pickup_date" name="pickup_date">
                        <button type="button" class="btn btn-outline-secondary" onclick="(function(){$('#pickup_date').datepicker('show')})()"><i class="fa fa-calendar"></i></button>
                    </div>
                    <p class="text-danger invalid_pickup_date"></p>
                </div>
                <div class="col-sm-2">
                    <label for="pickup_time">Pickup Time <span class="required">*</span></label>
                    <input class="form-control" type="time" id="pickup_time" name="pickup_time">
                    <p class="text-danger invalid_pickup_time"></p>
                </div>
            </div>
            <div class="row border-bottom mb-1">
                <div class="col-sm-4">
                    <h6>Payment & Order Details</h6>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-2">
                    <label for="payment_method">Payment Method <span class="required">*</span></label>
                    <select class="form-control" name="payment_method" id="payment_method">
                        <option value="Bank">Bank</option>
                        <option value="Card">Card</option>
                        <option value="pay_by_cash">Cash</option>
                        <option value="Invoiced">Invoiced</option>
                    </select>
                    <p class="text-danger invalid_payment_method"></p>
                </div>
                <div class="col-sm-2">
                    <label for="payment_status">Payment Status <span class="required">*</span></label>
                    <select class="form-control" name="payment_status" id="payment_status">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Invoiced">Invoiced</option>
                    </select>
                    <p class="text-danger invalid_payment_status"></p>
                </div>
                <div class="col-sm-2">
                    <label for="order_status">Booking Status <span class="required">*</span></label>
                    <select class="form-control" name="order_status" id="order_status"></select>
                    <p class="text-danger invalid_order_status"></p>
                </div>
                <div class="col-sm-3">
                    <label for="one_way_travel_time" style="font-weight: bold;">Hourly Time <span class="getcurrencycode"></span></label>
                    <input class="form-control text-success font-weight-bold" type="text" id="edithourlytime" name="edithourlytime" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);" style="font-weight: bold;" value="0">
                </div>
                <div class="col-sm-2" id="driver_amount_container" style="display:none;">
                    <label for="driver_amount">Driver Amount</label>
                    <input class="form-control text-success font-weight-bold" type="text" name="driver_amount" id="driver_amount" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);">
                    <p class="text-danger invalid_driver_amount"></p>
                </div>

                <div class="col-sm-3">
                    <label for="total_cost">Total Cost <span class="getcurrencycode"></span> <span class="required">*</span> <a class="" href="/carfares" target="_blank">Check Car Fare</a></label>
                    <input class="form-control text-success font-weight-bold" type="text" name="total_cost" readonly id="total_cost" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5); extraOneway();">
                    <p class="text-danger invalid_total_cost"></p>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="client_id" id="client_id">
</form>

<div class="col-sm-12 main-card mb-3 card"> 
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center">
                <button class="btn btn-success" id="update_book">
                    <span class="button-text">Update Booking</span>
                    <span class="spinner-border spinner-border-sm text-light" style="display: none;" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('car_rent.dashboard.partials.booking_edit_js')
@endsection
