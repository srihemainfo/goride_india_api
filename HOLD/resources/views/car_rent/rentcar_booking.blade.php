@extends('dashboard-layout.index')

@section('content')

<form id="bookingForm">
    {{-- Booking Info --}}
    <div class="col-sm-12 main-card mb-3 card">
        <div class="card-body">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">List Booking Rent Car</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Booking</li>
                </ol>
            </nav>

            <div class="card-header">
                <h4 class="card-title">Booking Form</h4>
            </div>

            <div class="row mt-3">
                <div class="col-sm-4">
                    <label for="search_clients">Search Clients <span class="required">*</span></label>
                    <div class="input-group">
                        <select class="form-control select2" style="width: 80%;" id="search_clients" name="client_id" data-placeholder="Search Clients">
                            <option value=""></option>
                        </select>
                        <button type="button" class="btn btn-success" title="Add Client" id="addCustomer"><i class="fas fa-plus"></i></button>
                    </div>
                    <p class="invalid_client_id text-danger"></p>
                </div>
            </div>
            <div class="row mt-2" id="client_info"></div>
        </div>
    </div>

    {{-- Car Details --}}
    <div class="col-sm-12 main-card mb-3 card" id="car_container">
        <div class="card-header">
            <h4 class="card-title">Car Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <label for="car_type">Car Type <span class="required">*</span></label>
                    <select class="form-control select2" id="car_type" name="car_type" data-placeholder="Select an option">
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="passenger_count">Passengers Count <span class="required">*</span></label>
                    <select class="form-control" id="passenger_count" name="passenger_count" readonly></select>
                </div>
            </div>
            <div class="row mt-2" id="child_seat_container"></div>
        </div>
    </div>

    {{-- Pickup Details --}}
    <div class="col-sm-12 main-card mb-3 card" id="outward_container">
        <div class="card-header">
            <h4 class="card-title">Pickup Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-3">
                    <label for="country_websites">Website <span class="required">*</span></label>
                    <select class="form-control select2" id="country_websites" name="country_websites" data-placeholder="Choose the website">
                        <option value=""></option>
                    </select>
                </div>

                <div class="col-sm-3">
                    <label for="pick_up">Pick-up Location <span class="required">*</span></label>
                    <select class="form-control select2" id="pick_up" name="pick_up" data-placeholder="Enter Pickup Address">
                        <option value=""></option>
                    </select>
                    <p class="text-danger invalid_pick_up"></p>
                </div>

                <div class="col-sm-3">
                    <label for="pickup_date">Pickup Date <span class="required">*</span></label>
                    <input class="form-control" type="date" id="date" name="pickup_date" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-sm-3">
                    <label for="pickup_time">Pickup Time <span class="required">*</span></label>
                    <input class="form-control" type="time" id="pickup_time" name="pickup_time">
                    <p class="text-danger invalid_pickup_time"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment & Booking Details --}}
    <div class="col-sm-12 main-card mb-3 card">
        <div class="card-header">
            <h4 class="card-title">Payment & Booking Details</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-sm-2">
                    <label for="payment_method">Payment Method <span class="required">*</span></label>
                    <select class="form-control" name="payment_method" id="payment_method">
                        <option value="pay_by_cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Bank">Bank</option>
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
                    <select class="form-control" name="order_status" id="order_status">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending">Pending</option>
                    </select>
                    <p class="text-danger invalid_order_status"></p>
                </div>

                <div class="col-sm-2">
                    <label for="hourly_time">Hourly Time <span class="required">*</span></label>
                    <input class="form-control text-success font-weight-bold" type="text" name="hourly_time" id="hourly_time" value="">
                    <p class="text-danger invalid_one_way_travel_time"></p>
                </div>

                <div class="col-sm-2" id="driver_charge_section" style="display: none;">
                    <label for="one_way_travel_time">Driver Charges <span class="required">*</span></label>
                    <input class="form-control text-success font-weight-bold" type="text" name="driver_charges" id="driver_charges" value="">
                </div>

                <div class="col-sm-3">
                    <label for="hourly_total_cost" style="font-weight: bold;">Total <span class="getcurrencycode"></span></label>
                    <input class="form-control text-success font-weight-bold" type="text" name="hourly_total_cost" id="hourly_total_cost" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);">
                </div>
            </div>

            <button id="book_now" type="submit" class="btn btn-primary">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                <span class="button-text">Book Now</span>
            </button>
        </div>
    </div>
</form>

@include('customers.partials.add_customer_modal')

@endsection

@section('custom_scripts')
    @include('car_rent.partials.hourly_booking_js')
@endsection
