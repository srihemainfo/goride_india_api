@extends('dashboard-layout.index')

@section('content')
    <div class="col-sm-12 main-card mb-3 card">
        <div class="card-body">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('booking/list/Confirmed') }}">List Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Multi Booking
                    </li>
                </ol>
            </nav>

            <div id="clone_job">
                <div class="card-header">
                    <h4 class="card-title">Clone Job</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                    <label for="search_job">Search Job (Booking ID) <span class="required">*</span></label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                                        tabindex="-1" aria-hidden="true" id="search_job" name="search_job"
                                        data-control="select2" data-placeholder="Enter Job No." data-hide-search="true">
                                    </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="book_info_section">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="client_name" class="col-form-label">Client Name</label>
                                <input type="text" id="client_name" name="client_name" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="client_email" class="col-form-label">Email</label>
                                <input type="text" id="client_email" name="client_email" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="client_mobile" class="col-form-label">Mobile</label>
                                <input type="text" id="client_mobile" name="client_mobile" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="car_type" class="col-form-label">Car Type</label>
                                <input type="text" id="car_type" name="car_type" class="form-control" value=""
                                    disabled>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="payment_status" class="col-form-label">Payment Status</label>
                                <input type="text" id="payment_status" name="payment_status" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="order_status" class="col-form-label">Order Status</label>
                                <input type="text" id="order_status" name="order_status" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="total_cost" class="col-form-label">Total Cost</label>
                                <input type="text" id="total_cost" name="total_cost" class="form-control" value=""
                                    disabled>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="extra_cost" class="col-form-label">Extra Cost</label>
                                <input type="text" id="extra_cost" name="extra_cost" class="form-control"
                                    value="" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="job_details">
                <div class="card-header" id="job_detail_header" style="display: none;">
                    <h4 class="card-title">Multi Booking Form</h4>
                </div>

                <div class="card-body">
                    <form id="multiBookingForm" name="multiBookingForm">
                        <input type="hidden" name="job_id" id="job_id">
                        <input type="hidden" name="pickup_time" id="pickup_time">
                        <input type="hidden" name="valid_from" id="valid_from">
                        <input type="hidden" name="valid_to" id="valid_to">
                        <input type="hidden" name="valid_place_from" id="valid_place_from">
                        <input type="hidden" name="valid_place_to" id="valid_place_to">
                        <div id="job_detail_container" style="display: none;">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="pickup_date_0" class="col-form-label">Pickup Date<span class="required">*</span></label>
                                        <div class="input-group">
                                            <input type="text" name="pickup_date[]" class="form-control"
                                                value="" id="pickup_date_0" placeholder="dd-mm-yyyy">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="(function(){$('#pickup_date_0').datepicker({ format: 'dd-mm-yyyy' }).datepicker('show')})()">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </div>
                                        <p class="text-danger invalid_pickup_date"></p>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="pickup_time_0" class="col-form-label">Pickup Time<span class="required">*</span></label>
                                        <input type="time" name="pickup_time[]" class="form-control" value=""
                                            id="pickup_time_0">
                                    </div>
                                    <p class="text-danger invalid_pickup_time"></p>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="pickup_location_0" class="col-form-label">Pick up</label>
                                        <input type="text" name="pickup_location[]" id="pickup_location_0"
                                            class="form-control from_location" value="" readonly>
                                    </div>
                                    <p class="text-danger invalid_pickup_location"></p>
                                </div>
                                <div class="col-sm-1 locate">
                                    <button type="button" class="btn btn-primary shift_location" title="Shift Locations"
                                        style="position: absolute; top: 36px; left: 30px;">
                                        <i class="fa fa-retweet" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="dropoff_location_0" class="col-form-label">Drop off</label>
                                        <input type="text" name="dropoff_location[]" id="dropoff_location_0"
                                            class="form-control to_location" value="" readonly>
                                    </div>
                                    <p class="text-danger invalid_dropoff_location"></p>
                                </div>
                                <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="website_0" class="col-form-label">Website Prefix<span class="required">*</span></label>
                                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1"
                        aria-hidden="true" id="country_websites" name="website[]" data-control="select2"
                        data-placeholder="Choose the website Prefix" data-hide-search="true">
                        <option value=""></option>
                    </select>
                                    </div>
                                    <p class="text-danger invalid_dropoff_location"></p>
                                </div>
                                <div class="col-sm-1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-footer" id="job_detail_footer" style="display: none;">
                    <div class="mr-2">
                        <button type="button" class="btn btn-primary add_booking" title="Add New Row">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Itinerary
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success save_booking">
                            <i class="fa fa-save" aria-hidden="true"></i> Save Bookings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    
$(document).ready(function(){
    $('.close-sidebar-btn').click(function(){
        $('.locate').toggleClass('small_screen');
    });
});
    
</script>
<style>
  .locate.small_screen{
        right: 8px;
    }
</style>
@endsection
@section('custom_scripts')
    @include('booking.partials.booking_js')
@endsection
@section('custom_scripts')
    @include('booking.partials.multi_booking_js')
@endsection
