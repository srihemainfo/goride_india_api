
@extends('dashboard-layout.index')

@section('content')

@include('home.partials.stats')

@include('home.partials.filter')

<style>

table.dataTable.no-footer {
    font-size: .88rem;
}
table{
    width: 100%;
    padding: 14px;
}
.card-home{
    padding: 0px !important;
}

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

.db-standard {
    position: relative;
}

.dt-table-15 {
    position: absolute;
    width: 130px;
    top: 22px;
    right: -26px;
    transform: rotate(45deg);
}

.driver-img {
    width: 23px;
    margin: 0 3px 0 0;
}

.removeDriver {
    padding: 3px !important;
}

.removeDriver:hover img {
    filter: brightness(100);
}

.btn-outline-danger.removeDriver {
    color: #114462 !important;
    border-color: #114462 !important;
}

.btn-outline-danger.removeDriver:hover {
    color: #fff !important;
    border-color: transparent !important;
}

@media (min-width:320px) and (max-width:776px){
    .db-standard .dt-table-13{
        position: absolute;
    }

    .dt-table-7, .dt-table-8 {
        width: 85%;
    }
    .dt-table-12 button{
        font-size: 12px !important;
    }
    .dt-table-14{
        font-size: 12px !important;
    }
    .app-main .app-main__inner{
        padding: 5px !important;
        margin: 4px !important;
    }
} 

.dt-table-14 {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

/*.closed-sidebar .dt-table-15 {*/
/*    right: -585px;*/
/*}*/

</style>

<!--<audio id="notif-sound" src="/assets/noti_sud.mp3" preload="auto"></audio>-->

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">
            Booking List
            <button type="button" class="btn btn-danger ms-1 d-inline-block d-lg-none" onclick="showlist('All')">
                <i class="fa fa-undo"></i>
            </button>
        </h4>
        <div class="btn-actions-pane-right d-flex flex-wrap gap-2 justify-content-center justify-content-md-end">
            <button type="button" class="btn btn-danger d-none d-lg-inline-block" onclick="showlist('All')">
                <i class="fa fa-undo"></i>
            </button>
            <button type="submit" class="btn btn-warning btn-sm" style="background:#F5781F !important; border:2px solid white;" onclick="showlist('Pending')">Pending</button>
            <button type="submit" class="btn btn-white standard btn-sm" style="background:#FFC500!important; border:2px solid white;" onclick="showlist('Confirmed')">Confirmed</button>
            <button type="submit" class="btn btn-warning btn-sm" style="background:#0e73c9; border:2px solid white;" onclick="showlist('Assigned')">Assigned</button>
            <button type="submit" class="btn btn-success btn-sm" style="background:#67138B; border:2px solid white;" onclick="showlist('Dispatched')">Dispatched</button>
            <button type="submit" class="btn btn-success btn-sm" style="background:#0ecb02; border:2px solid white;" onclick="showlist('Completed')">Completed</button>
            <button type="submit" class="btn btn-danger btn-sm" style="background:#f33434; border:2px solid white;" onclick="showlist('Canceled')">Canceled</button>
        </div>
    </div>    
    <div class="card-body card-home">
        <div class="table-responsive">
            <table id="dash-table" class="table" width="100%">
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

<div class="modal fade" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="callModalLabel">Call to Customer</h5>
      </div>
      <div class="modal-body text-center">
        <!-- For outgoing calls -->
        <div id="outgoing-controls">
            <div class="cus-details text-white">
                <p style="font-size: 1.1rem">Name: <span id="cus-name"></span></p>
                <p style="font-size: 1.1rem">Job ID: <span id="cus-jobId"></span></p>
            </div>
          <input type="hidden" id="phone-number" class="form-control mb-3" placeholder="Enter phone +919876543210">
          <button id="call-button" class="btn btn-success">Call</button>
        </div>

        <!-- For incoming calls -->
        <div id="incoming-controls" class="d-none">
          <p id="incoming-info" class="mb-3 text-white" style="font-size: 1.1rem">Incoming call...</p>
          <button id="acceptButton" class="btn btn-success">Accept</button>
          <button id="rejectButton" class="btn btn-danger">Reject</button>
        </div>

        <!-- Shared hangup -->
        <button id="hangup-button" class="btn btn-warning mt-3 d-none">Hangup</button>

        <!-- Log output -->
        <div class="mt-3">
          <textarea id="log" class="form-control" rows="5" readonly style="font-family: monospace;"></textarea>
        </div>
      </div>
    </div>
  </div>
</div>




    @include('booking.partials.sms_modal')
    @include('booking.partials.email_modal')
    @include('booking.partials.whatsapp_modal')
    @include('booking.partials.twilio_sms_modal')
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
