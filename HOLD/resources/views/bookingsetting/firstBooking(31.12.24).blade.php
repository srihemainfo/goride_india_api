@extends('dashboard-layout.index')
@section('content')
  <style>
  .nav-tabs{
      border:none;
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
        .form-select{
          
            color:#000;
        }
        .arrow-none{
              background-image:none !important;
        }
        .form-control{
            color:#000;
        }
    </style>
<!-- @include('customers.partials.filter') -->
<div class="col-sm-9 mx-4">
<div class="right_col" role="main">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Booking Settings</h2>
                        </div>
                        <div class="x_content">
                            <div class="x_content-container">
                                <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>
                                    <div class="row mb-3">
                                        <label for="" class="col-form-label col-md-5 col-sm-5">Operating country:</label>
                                        <div class="col-md-7 col-sm-7">
                                            <input class="form-control " type="text" name="country" id="country" placeholder="Enter Country (India)" autocomplete="off">
                                            <div id="dropdown" class="dropdown-list form-select arrow-none"></div>
                                        </div>
                                    </div>
                                    

                                    <div class="row mb-3">
    <label for="" class="col-form-label col-md-5 col-sm-5">Timezone</label>
    <div class="col-md-7 col-sm-7">
        <input class="form-control " type="text" id="timezone" name="timezone" placeholder="Enter TimeZone Africa/Abidjan" autocomplete="off">
        <div id="dropdowntimezone" class="dropdown-list form-select arrow-none"></div>
    </div>
</div>

                                        <div class="row mb-3">
                                        <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>
                                        <div class="col-md-7 col-sm-7">
                                            <input class="form-control " type="text" name="currency" id="currency" placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off">
                                            <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <label class="col-form-label col-md-5 col-sm-5">Distance unit</label>
                                        <div class="col-md-7 col-sm-7">
                                            <select name="distance_unit" id="distance_unit" class="form-select" required>
                                                <option value="miles">Miles</option>
                                                <option value="kms">Kms</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum</label>
                                        <div class="col-md-4 col-sm-4 col-6 mb-3">
                                            <select name="advance_booking_minium_type" id="advance_booking_minium_type" class="form-select" required>
                                                <option value="minutes">Minutes</option>
                                                <option value="hours">Hours</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-6 mb-3">
                                             <select name="advance_booking_minium" id="advance_booking_minium" class="form-select" required>
                                            </select>
                                            <!--<input type="text" id="advance_booking_minium" name="advance_booking_minium"  placeholder="Enter Number"class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum</label>
                                        <div class="col-md-4 col-sm-4 col-6 mb-3">
                                            <select name="advance_booking_maximum_type" id="advance_booking_maximum_type" class="form-select" required>
                                                <option value="days">Days</option>
                                                <option value="months">Months</option>
                                                <option value="years">Years</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-6 mb-3">
                                             <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select" required>
                                            </select>
                                            <!--<input type="text" id="advance_booking_maximum" name="advance_booking_maximum"  placeholder="Enter Number" class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->
                                            
                                        <input type="hidden" id="bokingsettingid" name="bokingsettingid" >   
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">UPDATE</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

<style>
.nav-tabs .nav-link:hover  {
    background-color: #747474 !important;
    color: white !important; 
}
.nav-link.active {
  background-color: #fff !important;
  color:#343a40 !important;
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