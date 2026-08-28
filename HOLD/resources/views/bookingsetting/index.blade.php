@extends('dashboard-layout.index')
@section('content')
<style>
    .x_panel {
        background-color: white;
        padding: 30px;
        border-radius: 10px;
    }
    @media (max-width:776px){
        .x_panel{
            padding: 13px !important;
        }
        .x_title h2{
            font-size: x-large !important;
        }
    }

    .nav-tabs {
        border: none;
    }

    .dropdown-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        display: none;
        position: absolute;
        background-color: white;
        width: 93%;
        z-index: 1000;
    }

    .dropdown-list.active {
        display: block;
    }

    .dropdown-item {
        padding: 8px;
        cursor: pointer;
    }

    .dropdown-item:hover {
        background-color: #f0f0f0;
    }

    [id="dropdown"] {
        width: 94% !important;
    }

    .form-select {
        color: #000;
    }

    .arrow-none {
        background-image: none !important;
    }

    .form-control {
        color: #000;
    }
  
</style>
<!-- @include('customers.partials.filter') -->
<div class="col-sm-9">
    <div class="right_col" role="main">
        <div class="x_panel">
            <div class="x_title">
                <h2>Booking Settings</h2>
            </div>
            <div class="x_content">
                <div class="x_content-container">
                    <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>
                        <div class="row mb-3">
                            <label for="country" class="col-form-label col-md-5 col-sm-5">Operating country:</label>
                            <div class="col-md-7 col-sm-7">
                                <select class="form-select select2" name="country" id="country" style="width: 100%;">
                                    <option value="">Select a country</option>
                                    @foreach($allCountries as $key => $value)
                                        <option 
                                            value="{{ $value['name'] }}"
                                            data-currency="{{ $value['currency'] }}"
                                            data-currency-symbol="{{ $value['currency_symbol'] }}"
                                            data-timezone="{{ $value['zone_name'] }}"
                                        >
                                            {{ $value['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                        <div class="row mb-3">
                            <label for="" class="col-form-label col-md-5 col-sm-5">Timezone</label>
                            <div class="col-md-7 col-sm-7">
                                <input class="form-control " type="text" id="timezone" name="timezone"
                                    placeholder="Enter TimeZone Africa/Abidjan" autocomplete="off" readonly>
                                <div id="dropdowntimezone" class="dropdown-list form-select arrow-none"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>
                            <div class="col-md-7 col-sm-7">
                                <input class="form-control " type="text" name="currency" id="currency"
                                    placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off" readonly>
                                <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-form-label col-md-5 col-sm-5">Distance unit</label>
                            <div class="col-md-7 col-sm-7">
                                <select name="distance_unit" id="distance_unit" class="form-select" required>
                                    <option value="kms">Kms</option>
                                    <option value="miles">Miles</option>
                                </select>
                            </div>
                        </div>


                        <div class="row">
                            <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum</label>
                            <div class="col-md-4 col-sm-4 col-6 mb-3">
                                <select name="advance_booking_minium_type" id="advance_booking_minium_type"
                                    class="form-select" required>
                                    <option value="minutes">Minutes</option>
                                    <option value="hours">Hours</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-3 col-6 mb-3">
                                <select name="advance_booking_minium" id="advance_booking_minium" class="form-select"
                                    required>
                                </select>
                            </div>
                        </div>
                        <div class="row ">
                            <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum</label>
                            <div class="col-md-4 col-sm-4 col-6 mb-3">
                                <select name="advance_booking_maximum_type" id="advance_booking_maximum_type"
                                    class="form-select" required>
                                    <option value="days">Days</option>
                                    <option value="months">Months</option>
                                    <option value="years">Years</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-3 col-6 mb-3">
                                <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select"
                                    required>
                                </select>
                                <input type="hidden" id="bokingsettingid" name="bokingsettingid">
                            </div>
                        </div>
                        <input type="hidden" id="countryCode" name="countryCode">

                        <div class="text-center">
                            <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">

        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab"
            aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking
        </a>
        <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email
        </a>
        <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-plus" style="margin-right: 8px;"></i> Email Template
        </a>
        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options
        </a>
        <!-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date
        </a> -->
        {{-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
            <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar
        </a> --}}
        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/whatsapp-configuration" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;" contenteditable="false">

            <i class="fab fa-whatsapp" style="margin-right: 8px;"></i>WhatsApp Config
      
          </a>
        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab"
            aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-star" style="margin-right: 8px;"></i> Review
        </a>
    </div>
</div>
<style>
    .nav-tabs .nav-link:hover {
        background-color: #747474 !important;
        color: white !important;
    }

    .nav-link.active {
        background-color: #fff !important;
        color: #343a40 !important;
    }

    .nav-link:hover {
        background-color: #6c757d !important;
    }
</style>
@include('bookingsetting.partials.add_customer_modal')
@endsection
@section('custom_scripts')
@include('bookingsetting.partials.customers_js')
@endsection