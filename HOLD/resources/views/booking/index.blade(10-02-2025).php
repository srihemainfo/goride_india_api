

<style>
    .dt-table-11{
    color: #494949 !important;
    font-weight: 500 !important;
    margin: -27px 0 0 427px !important;
}
.dt-table-7, .dt-table-8 {
    width: 60% !important;
}
.db-standard {
    position: relative !important;
    z-index: 1 !important;
}

</style>



@php
    // dd(htmlentities(stripslashes(utf8_encode(json_encode($list_drivers))), ENT_QUOTES));
    $driver_id = $queryed_driver_id === 'undefined' || empty($queryed_driver_id) ? null : $queryed_driver_id;
@endphp

@extends('dashboard-layout.index')

@section('content')
        @include('booking.partials.filter')


    <div class="col-sm-12 main-card mb-2 card">
       <div class="card-header">
        <h4 class="card-title"> Booking List</h4>
        <div class="btn-actions-pane-right">
            <button type="button" class="btn btn-danger" onclick="showlist('All')"><i class="fa fa-undo"></i></button>
            <button type="submit" class="btn btn-danger" onclick="showlist('Pending')"> Pending </button>   
            <button type="submit" class="btn btn-white standard" onclick="showlist('Confirmed')"> Confirmed </button>
            <button type="submit" class="btn btn-warning" onclick="showlist('Assigned')"> Assigned </button>
            <button type="submit" class="btn btn-success" onclick="showlist('Dispatched')"> Dispatched </button>
            <button type="submit" class="btn btn-info" onclick="showlist('Moving')"> Moving </button>
            <button type="submit" class="btn btn-success1" onclick="showlist('Dispatched')"> Completed </button>
            <button type="submit" class="btn btn-warning1" onclick="showlist('settled')"> Settled </button>
            <button type="submit"class="btn btn-info1" onclick="showlist('Canceled')"> Cancelled </button>
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

        @include('booking.partials.sms_modal')
        @include('booking.partials.email_modal')
@endsection

@section('custom_scripts')
    @include('booking.partials.booking_datatable_js')
@endsection
