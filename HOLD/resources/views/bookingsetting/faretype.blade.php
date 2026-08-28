@extends('dashboard-layout.index')

@section('content')
<style>
    .x_panel {
        background-color: white;
        padding: 30px;
        border-radius: 10px;
    }

    @media (max-width: 776px) {
        .x_panel {
            padding: 13px !important;
        }

        .x_title h2 {
            font-size: x-large !important;
        }
    }

    .form-select,
    .form-control {
        color: #000;
    }

    .form-check-label {
        margin-left: 10px;
    }

    #updateButton {
        display: none;
    }
</style>

<div class="col-sm-9">
    <div class="right_col" role="main">
        <div class="x_panel">
            <div class="x_title">
                <h2>Fare Type</h2>
            </div>
            <div class="x_content">
                <div class="x_content-container">
                    <form id="formSettingsFareType" class="form-horizontal" method="post">
                        @csrf

                        <!-- Km Based -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Km Based</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input fare-switch" type="checkbox" id="kmBased" name="fare_type" value="1">
                                    <label class="form-check-label" for="kmBased">On/Off</label>
                                </div>
                            </div>
                        </div>

                        <!-- Hourly Based -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Hourly Based</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input fare-switch" type="checkbox" id="hourlyBased" name="fare_type" value="2">
                                    <label class="form-check-label" for="hourlyBased">On/Off</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tariff Based -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Tariff Based</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch">
                                    <input class="form-check-input fare-switch" type="checkbox" id="tariffBased" name="fare_type" value="3">
                                    <label class="form-check-label" for="tariffBased">On/Off</label> 
                                </div>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <div class="form-group row mt-4">
                            <div class="col-sm-12 text-center">
                                <button type="submit" id="updateButton" class="btn btn-success">Update</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_scripts')

@include('bookingsetting.partials.faretype_js')
@endsection
