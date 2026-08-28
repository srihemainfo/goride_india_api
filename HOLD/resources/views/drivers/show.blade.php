


@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-body">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('driver.index') }}">List Drivers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Show Driver</li>
            </ol>
        </nav>

        <div class="card-header">
            <h4 class="card-title">Driver Details</h4>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-4">
                        <label for="driver_no">Driver No. <span class="required">*</span></label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="driver_no" name="driver_no" value="{{ old('driver_no', optional($driver ?? null)->driver_no) }}">

                    </div>
                    <div class="col-sm-4">
                        <label for="name">Name <span class="required">*</span></label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($driver ?? null)->name) }}">

                    </div>
                    <div class="col-sm-4">
                        <label for="phone_no">Phone No. <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">+44 (0)</span>
                            <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="phone_no" name="phone_no" value="{{ old('phone_no', optional($driver ?? null)->phone) }}"  aria-describedby="basic-addon1">
                        </div>

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-4">
                        <label for="email">Email <span class="required">*</span></label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="email" name="email" value="{{ old('email', optional($driver ?? null)->email) }}">

                    </div>

                    <div class="col-sm-4">
                        <label for="address">Address</label>
                        <textarea {{  $show ? "disabled" : "" }} class="form-control" id="address" name="address" rows="3">{{ old('address', optional($driver ?? null)->address) }}</textarea>

                    </div>

                    <div class="col-sm-3">
                        <img src="{{ optional($driver ?? null)->photo }}" id="uploadPreview" alt="your image" onerror="this.src='https://www.ecminibus.co.uk/crm/images/empty_profile.png'" style="width: 128px; height: 128px;">
                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-4">
                        <label for="commision_value">Commision Value</label>
                        <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="commision_value" name="commision_value" value="{{ old('commision_value', optional($driver ?? null)->commission_val) }}">

                    </div>
                    <div class="col-sm-4">
                        <label for="driver_booking_percentage">Driver Booking Percentage</label>
                        <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="driver_booking_percentage" name="driver_booking_percentage" value="{{ old('driver_booking_percentage', optional($driver ?? null)->booking_comm_val) }}">

                    </div>
                    <div class="col-sm-4">
                        <label for="booking_email">Booking Email</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="booking_email" name="booking_email" value="{{ old('booking_email', optional($driver ?? null)->booking_email) }}">

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-4">
                        <label for="national_insurance_no">National Insurance No.</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="national_insurance_no" name="national_insurance_no" value="{{ old('national_insurance_no', optional($driver ?? null)->ni_num) }}">

                    </div>
                    <div class="col-sm-4">
                        <label for="vehicle_type">Vehicle Type <span class="required">*</span></label>
                        <select {{  $show ? "disabled" : "" }} class="form-control" id="vehicle_type" name="vehicle_type">
                            <option value="">-- select vehicle type --</option>
                            <option value="saloon" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "saloon" ? "selected" : "" }}>Saloon</option>
                            <option value="estate" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "estate" ? "selected" : "" }}>Estate</option>
                            <option value="mpv_executive" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "mpv_executive" ? "selected" : "" }}>Executive 8 Seater</option>
                            <option value="mpv8" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "mpv8" ? "selected" : "" }}>8 Seater</option>
                            <option value="executive" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "executive" ? "selected" : "" }}>Executive Saloon</option>
                            <option value="mpv" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "mpv" ? "selected" : "" }}>MPV</option>
                            <option value="mpv5" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "mpv5" ? "selected" : "" }}>Executive MPV</option>
                            <option value="mpv6" {{ old('vehicle_type', optional($driver ?? null)->vech_type) === "mpv6" ? "selected" : "" }}>MPV+</option>
                        </select>

                    </div>
                    <div class="col-sm-4">
                        <label for="vehicle_reg_no">Vehicle Reg No.</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_reg_no" name="vehicle_reg_no" value="{{ old('vehicle_reg_no', optional($driver ?? null)->vech_reg_num) }}">

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-3">
                        <label for="vehicle_color">Vehicle Color</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_color" name="vehicle_color" value="{{ old('vehicle_color', optional($driver ?? null)->vech_color) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="vehicle_make">Vehicle Make</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_make" name="vehicle_make" value="{{ old('vehicle_make', optional($driver ?? null)->make) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="vehicle_model">Vehicle Model</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model', optional($driver ?? null)->model) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="number_of_seats">Number of Seats</label>
                        <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="number_of_seats" name="number_of_seats" value="{{ old('number_of_seats', optional($driver ?? null)->no_seat) }}">

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-3">
                        <label for="vehicle_insurance">Vehicle Insurance</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_insurance" name="vehicle_insurance" value="{{ old('vehicle_insurance', optional($driver ?? null)->vech_insurance) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="vehicle_insurance_expiry">Insurance Expiry on</label>
                        <input {{  $show ? "disabled" : "" }} type="date" class="form-control" id="vehicle_insurance_expiry"  name="vehicle_insurance_expiry" value="{{ old('vehicle_insurance_expiry', optional($driver ?? null)->vech_insur_expiry_date) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="vehicle_license">Vehicle Licence</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="vehicle_license" name="vehicle_license" value="{{ old('vehicle_license', optional($driver ?? null)->vech_licence_no) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="vehicle_license_expiry">Licence Expiry on</label>
                        <input {{  $show ? "disabled" : "" }} type="date" class="form-control" id="vehicle_license_expiry"  name="vehicle_license_expiry" value="{{ old('vehicle_license_expiry', optional($driver ?? null)->vech_lic_expiry_date) }}">

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-3">
                        <label for="pco_license_no">PCO License No.</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="pco_license_no" name="pco_license_no" value="{{ old('pco_license_no', optional($driver ?? null)->pco_licence_no) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="pco_license_no_expiry">PCO Expiry on</label>
                        <input {{  $show ? "disabled" : "" }} type="date" class="form-control" id="pco_license_no_expiry"  name="pco_license_no_expiry" value="{{ old('pco_license_no_expiry', optional($driver ?? null)->pco_lic_expiry_date) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="driver_license_no">Driver Licence No.</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="driver_license_no" name="driver_license_no" value="{{ old('driver_license_no', optional($driver ?? null)->driver_licence_no) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="driver_license_no_expiry">Driver Licence Expiry on</label>
                        <input {{  $show ? "disabled" : "" }} type="date" class="form-control" id="driver_license_no_expiry"  name="driver_license_no_expiry" value="{{ old('driver_license_no_expiry', optional($driver ?? null)->driver_lic_expiry_date) }}">

                    </div>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-3">
                        <label for="mot_no">MOT No.</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="mot_no" name="mot_no" value="{{ old('mot_no', optional($driver ?? null)->mot_no) }}">

                    </div>
                    <div class="col-sm-3">
                        <label for="mot_no_expiry">MOT Expiry on</label>
                        <input {{  $show ? "disabled" : "" }} type="date" class="form-control" id="mot_no_expiry"  name="mot_no_expiry" value="{{ old('mot_no_expiry', optional($driver ?? null)->mot_expiry_date) }}">

                    </div>


                </div>

                <div class="row ml-2">
                    <h4 class="card-title">Mobile App Setup</h4>
                </div>

                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-3">
                        <label for="refresh_time">Refresh Time</label>
                        <div class="input-group mb-3">
                            <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="refresh_time" name="refresh_time" value="{{ old('refresh_time', optional($driver ?? null)->refresh_time) }}">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">Mins</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="before_reminder_time">Before Reminder Time</label>
                        <div class="input-group mb-3">
                            <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="before_reminder_time" name="before_reminder_time" value="{{ old('before_reminder_time', optional($driver ?? null)->reminder_time) }}">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">Mins</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="start_journey_gaptime">Start Journey Gaptime</label>
                        <div class="input-group mb-3">
                            <input {{  $show ? "disabled" : "" }} type="number" class="form-control" id="start_journey_gaptime" name="start_journey_gaptime" value="{{ old('start_journey_gaptime', optional($driver ?? null)->gap_time) }}">
                            <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2">Hrs</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-3">
                        <label for="customer_call">Customer Call</label>
                        <input {{  $show ? "disabled" : "" }} type="text" class="form-control" id="customer_call" name="customer_call" value="{{ old('customer_call', optional($driver ?? null)->customer_call) }}">

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('drivers.partials.form-js')
@endsection
