@php
    // dd(htmlentities(stripslashes(utf8_encode(json_encode($list_drivers))), ENT_QUOTES));
    $driver_id = $queryed_driver_id === 'undefined' || empty($queryed_driver_id) ? null : $queryed_driver_id;
@endphp

@extends('dashboard-layout.index')

@section('content')
        @include('booking.partials.filter')


    <div class="col-sm-12 main-card mb-2 card">
        <div class="card-header">
            <h4 class="card-title" id="card_title"></h4>
            <div class="btn-actions-pane-right">
                    <a href="{{ route('driver.index') }}">
                        <button type="button" class="btn btn-primary" id="addBooking"><i class="fas fa-list"></i> Back to
                            Drivers </button>
                    </a>
                        <a href="{{ route('booking.create') }}">
                            <button type="button" class="btn btn-success" id="addBooking"><i class="fas fa-plus"></i> Add
                                Booking </button>
                        </a>
                        <a href="{{ route('MultiBooking') }}">
                            <button type="button" class="btn btn-primary" id="MultiBooking"><i class="fas fa-plus"></i> Multi
                                Booking </button>
                        </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="book-table" class="table row" width="100%">
                    <thead class="table-light"  style="display:none;">
                        <tr>
                        <th>Job No.</th>
                        <th>Flight</th>
                        <th>Pickup D/T</th>
                        <th>Booking Date</th>
                        <th>No. Pax</th>
                        <th>Vehicle</th>
                        <th>Pickup</th>
                        <th></th>
                        <th>Dropoff</th>
                        <th>Pay Status</th>
                        <th>Pay Type</th>
                        <th>Driver</th>
                        <th>status</th>
                        <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="row justify-content-center"></tbody>
                </table>
            </div>
        </div>
    </div>

        @include('booking.partials.assign_driver_modal')

        @include('booking.partials.sms_modal')
        @include('booking.partials.email_modal')
@endsection

@section('custom_scripts')
    @include('booking.partials.booking_datatable_js')
@endsection
