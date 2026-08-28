@php
    // dd(htmlentities(stripslashes(utf8_encode(json_encode($list_drivers))), ENT_QUOTES));
    $driver_id = $queryed_driver_id === 'undefined' || empty($queryed_driver_id) ? null : $queryed_driver_id;
@endphp

@extends('dashboard-layout.index')

@section('content')
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
    background-image: url(/dashboard-assets/assets/images/blue_car.png);
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
} 

.dt-table-14 {
    position: absolute;
    bottom: 10px;
    right: 10px;
}
table{
    width: 100%;
    padding: 14px;
}
.card-book{
    padding: 0px !important;
}

</style>
    <div class="col-sm-12 main-card mb-2 card">
        <div class="card-header">
            <h4 class="card-title">
                Booking List
                <button type="button" class="btn btn-danger d-inline-block d-lg-none" onclick="showlist('All')">
                    <i class="fa fa-undo"></i>
                </button>
            </h4>
            <div class="btn-actions-pane-right d-flex flex-wrap gap-2 justify-content-center justify-content-md-end">
                <button type="button" class="btn btn-danger d-none d-lg-inline-block" onclick="showlist('All')">
                    <i class="fa fa-undo"></i>
                </button>
                <button type="submit" class="btn btn-warning btn-sm" style="background-color: #F5781F !important;" onclick="showlist('Pending')">Pending</button>   
                <button type="submit" class="btn btn-white standard btn-sm" style="background-color: #FFC500!important;" onclick="showlist('Confirmed')">Confirmed</button>
                <button type="submit" class="btn btn-warning btn-sm" style="background-color: #0E73C9 !important;" onclick="showlist('Assigned')">Assigned</button>
                <button type="submit" class="btn btn-success btn-sm" style="background-color: #67138B !important;" onclick="showlist('Dispatched')">Dispatched</button>
                <button type="submit" class="btn btn-info btn-sm" onclick="showlist('Moving')">Moving</button>
                <button type="submit" class="btn btn-success btn-sm" style="background-color: #0ecb02!important;" onclick="showlist('Completed')">Completed</button>
                <button type="submit" class="btn btn-danger btn-sm" style="background-color: #F33434  !important;" onclick="showlist('Canceled')">Canceled</button>
            </div>
        </div>
        
        <div class="card-body card-book">
            <div class="table-responsive">
                <table id="book-table" class="table" width="100%">
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

<div id="email-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Email (Customer)</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="emailForm" name="emailForm">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="customer_email" class="col-form-label">Email<span class="required">&nbsp;*</span></label>
                      <input type="text" class="form-control" name="customer_email" id="customer_email" value="">
                      <p class="text-danger invalid_email"></p>
                    </div>
                    
                  </div>
                  <div class="col-sm-6">
    <label for="templateSelect" class="col-form-label">Select Template<span class="required">&nbsp;*</span></label>
    <select id="templateSelect" name="template_name" class="form-control px-5" required>
        <!-- Options will be populated here -->
    </select>
</div>

                 <div class="col-sm-12">
    <div class="form-group">
      
        <label for="customer_email_send" class="col-form-label">Mail Body (Message)<span class="required">&nbsp;*</span></label>
        <button type="button" class="btn btn-primary mb-2" id="preview_email">Preview</button><br>
        <div class="col-5">
         <label for="customer_emails" class="col-form-label">Customer Name<span class="required">&nbsp;*</span></label>
<input id="customernames" type="text" name="customernames" class="form-control" placeholder="Customer Name">
</div>
        <div id="customer_email_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="description" contenteditable="true">
          
        </div>
        <p class="text-danger invalid_message"></p>
    </div>
</div>

                </div>
              </form>
        </div>
        <div class="modal-footer">
            <div id="load_animation_email" style="display: none;">
                <div class="spinner-grow text-primary" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-secondary" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-success" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-danger" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-info" role="status">
                <span class="sr-only"></span>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="email_send_btn"><i class="fa fa-paper-plane"></i>&nbsp; Send Email</button>
            </div>
        </div>
      </div>
    </div> 
  </div> 
  <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Email Preview</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="email_preview_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

    
</script>

        @include('booking.partials.assign_driver_modal')
        @include('booking.partials.whatsapp_modal')
        @include('booking.partials.sms_modal')
        @include('booking.partials.twilio_sms_modal')
        @include('booking.partials.email_modal')
@endsection

@section('custom_scripts')
    @include('home.partials.danshboard_js')
@endsection
