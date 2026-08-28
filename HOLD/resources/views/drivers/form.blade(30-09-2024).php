@php
    // dd($errors->all());
@endphp

<div class="main-card row">
    <div class="col-sm-8" style="padding-right: 5px;padding-bottom: 5px;">
        <div class="col-sm-12 main-card mb-3 card" style="margin-bottom: 5px !important;">
            @include('drivers.partials.create_drivers_form.personal_details')
            @include('drivers.partials.create_drivers_form.business_details')
            @include('drivers.partials.create_drivers_form.vehicle_details')
            @include('drivers.partials.create_drivers_form.mobileapp_setup')
        </div>
    </div>
    <div class="col-sm-4" style="padding-left: 5px;">
        <div class="col-sm-12 main-card mb-3 card">
            @include('drivers.partials.create_drivers_form.image_upload')
        </div>
            <div class="col-sm-12 main-card mb-3 card" id="file_upload">
                @include('drivers.partials.create_drivers_form.file_upload')
            </div>
    </div>
</div>
