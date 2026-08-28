@extends('dashboard-layout.index')

@section('content')
    <form id="bookingForm" name="bookingForm">
        @include('booking.partials.create_booking_form.client_search')

        @include('booking.partials.create_booking_form.journey_details')

        @include('booking.partials.create_booking_form.car_details')

        @include('booking.partials.create_booking_form.outward_details')

        @include('booking.partials.create_booking_form.return_details')
        

        <div class="col-sm-12 main-card mb-3 card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <input type="hidden" name="one_way_from_lati" id="one_way_from_lati"
                            value="{{ $isEditable && $booking_details->from_lat ? $booking_details->from_lat : '' }}">
                        <input type="hidden" name="one_way_from_longi" id="one_way_from_longi"
                            value="{{ $isEditable && $booking_details->from_long ? $booking_details->from_long : '' }}">

                        <input type="hidden" name="one_way_to_lati" id="one_way_to_lati"
                            value="{{ $isEditable && $booking_details->to_lat ? $booking_details->to_lat : '' }}">
                        <input type="hidden" name="one_way_to_longi" id="one_way_to_longi"
                            value="{{ $isEditable && $booking_details->to_long ? $booking_details->to_long : '' }}">

                        <input type="hidden" name="one_way_actual_amount" id="one_way_actual_amount"
                            value="{{ $isEditable && $booking_details->actual_amount ? $booking_details->actual_amount : 0 }}">
                        <input type="hidden" name="one_way_special_day_percentage" id="one_way_special_day_percentage"
                            value="{{ $isEditable && $booking_details->special_day_percentage ? $booking_details->special_day_percentage : 0 }}">

                        @if (!$isEditable)
                            <input type="hidden" name="return_from_lati" id="return_from_lati" value="">
                            <input type="hidden" name="return_from_longi" id="return_from_longi" value="">
                            <input type="hidden" name="return_to_lati" id="return_to_lati" value="">
                            <input type="hidden" name="return_to_longi" id="return_to_longi" value="">
                            <input type="hidden" name="return_actual_amount" id="return_actual_amount" value="0">
                            <input type="hidden" name="return_special_day_percentage" id="return_special_day_percentage"
                                value="0">

                            {{-- below hidden field used to open flight and cruise info box in return - nothing gose to db from this field. --}}
                            <input type="hidden" name="one_way_drop_off_place_type" id="one_way_drop_off_place_type" value="">
                        @endif

                        {{-- @if ($isEditable)
                        <input type="hidden" name="trip_id" id="trip_id" value="{{ $booking_details->id }}">
                    @endif --}}

                        <button class="btn btn-success"
                            id="book_now">
                            <span class="button-text">{{ $isEditable ? 'Update Booking' : 'Book Now' }}</span>
                            <span class="spinner-border spinner-border-sm text-light" style="display: none;" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('customers.partials.add_customer_modal')
@endsection

@section('custom_scripts')
    @include('booking.partials.booking_js')
@endsection
