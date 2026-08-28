@extends('dashboard-layout.index')
@section('content')
<div class="col-sm-9 mx-4 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-4">Car Fares List</h4>
    </div>
    <div class='row d-none'>
        <div class="col-sm-2 mt-2">
            <input class='form-control' id='start' oninput='validateInputInteger(this)' value='1' disabled>
        </div>
        <div class="col-sm-2 mt-2">
            <input class='form-control' oninput='validateInputInteger(this)' id='end' value>
        </div>
        <div class="col-sm-2 mt-2">
            <button id='addButton' class='btn btn-success'>AddButton</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table" class="table" width="100%">
                    <thead id='header_container'>
                        <!--<tr id="table-header"></tr>-->
                    </thead>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success'>Update</button>
            </div>
        </div>
    </div>



    <!-- <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table" class="table" width="100%">
                    <div class="card-header">
                        <h4 class="card-title mr-4">Car Fares List</h4>
                    </div>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success'>Update</button>
            </div>
        </div>
    </div> -->



</div>
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
        <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
        <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
        <!--</a>-->
        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab"
            aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare
        </a>
        <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Zones
        </a>
        <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-vector-square" style="margin-right: 8px;"></i> Area
        </a>
        <!--<a class="nav-link active text-light" id="vert-tabs-right-promo-code-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-globe" style="margin-right: 8px;"></i> Zone-->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options-->
        <!--</a>-->
        <!--<a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar-->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-star" style="margin-right: 8px;"></i> Review-->
        <!--</a>-->
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
@endsection
@section('custom_scripts')
@include('dynamiccarfare.partials.carfares_js_new')
@endsection