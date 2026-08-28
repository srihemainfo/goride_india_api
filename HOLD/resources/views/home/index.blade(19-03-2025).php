
@extends('dashboard-layout.index')

@section('content')

@include('home.partials.stats')

@include('home.partials.filter')

<style>

.dt-table-7, .dt-table-8 {
    width: 60%;
}
.dt-table-11{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 420px;
}

.border-dashed{
    background-size: 7px 8px, 100% 20px !important;
}
    .d-arrow {
    color: #fff;
    background: #198306;
    padding: 4px 7px 4px 8px;
    font-size: 10px;
    border-radius: 19px;
    cursor: pointer;
}
.d-arrow:hover{
    background: #061583;
}
.small-screen{
        margin-top: 30px;
}

.db-standard.Canceled {
    background: #fad6d6 !important;
}

.db-standard.Confirmed {
    background:#f8db97 !important;
}

.db-standard.Assigned {
    background: #bdd4ff !important;
}

.db-standard.Dispatched {
    background: #d0bdff !important;
}

.db-standard.Moving {
    background: #bdd9ff !important;
}
.db-standard.Completed{
    background: #d1ffbd !important;
}
.db-standard {
    position: relative;
    z-index: 1;
}


.db-standard:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url(./dashboard-assets/assets/images/blue_car.png);
    background-size: 450px;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.1;
    z-index: -1;
    backdrop-filter: grayscale(1);
    filter: grayscale(1);
}

</style>

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title"> Booking List </h4>
        <div class="btn-actions-pane-right">
            <button type="button" class="btn btn-danger" onclick="showlist('All')"><i class="fa fa-undo"></i></button>
            <button type="submit" class="btn btn-warning"  style="background-color: #F5781F !important" onclick="showlist('Pending')"> Pending </button>
            <button type="submit" class="btn btn-white standard" style="background-color: #FFC500!important;" onclick="showlist('Confirmed')"> Confirmed </button>
            <button type="submit" class="btn btn-warning" onclick="showlist('Assigned')" style="background:#0e73c9;border:2px solid white;"> Assigned </button>
            <button type="submit" class="btn btn-success" onclick="showlist('Dispatched')" style="background: green;border:2px solid white;"> Dispatched </button>
            <button type="submit" class="btn btn-info" onclick="showlist('Moving')"> Moving </button>
            <button type="submit" class="btn btn-success" onclick="showlist('Completed')"> Completed </button>
            <button type="submit" class="btn btn-danger" onclick="showlist('Canceled')" style="background:#f33434;border:2px solid white;"> Canceled </button>
            
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dash-table" class="table row" width="100%">
                <thead class="table-light" style="display:none;">
                    <tr>
                        <!--<th></th>-->
                        <!--<th>#</th>-->
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
                <tbody class="row justify-content-around"></tbody>
            </table>
        </div>
    </div>
</div>

    @include('booking.partials.sms_modal')
    @include('booking.partials.email_modal')
    @include('booking.partials.assign_driver_modal')
    @include('home.partials.add_fleet_modal')
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@section('custom_scripts')
    @include('home.partials.danshboard_js')
   <script>
$(document).ready(function(){
    $('.close-sidebar-btn').click(function(){
        $('#site_currency2').toggleClass('small_screen');
    });
});
    
</script>
@endsection
