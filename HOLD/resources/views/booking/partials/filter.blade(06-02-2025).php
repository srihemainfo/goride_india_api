
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Booking Filter</h4>
    </div>
    <div class="card-body">
        <form id="book_filter_form">
        <div class="row">
            <div class="col-sm-12 row mb-2">
                    <div class="col-sm-3">
                        <label for="driver_name_filter" class="col-form-label">Driver Name</label>
                        <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="driver_name_filter" name="driver_name_filter" data-control="select2" data-placeholder="Select driver for booking" data-hide-search="true">
                        </select>
                    </div>

                <div class="col-sm-3">
                    <label for="job_no_filter">Job No.</label>
                    <input type="text" class="form-control" id="job_no_filter" placeholder="Search by Job No." name="job_no_filter" value="">
                </div>

                <div class="col-sm-3">
                    <label for="pickup_between_filter">Pickup Dates Between</label>
                    <input type="text" class="form-control" id="pickup_between_filter" placeholder="Select date range" name="pickup_between_filter" value="">
                </div>

                <div class="col-sm-3">
                    <label for="booking_between_filter">Booking Dates Between</label>
                    <input type="text" class="form-control" id="booking_between_filter" placeholder="Select date range" name="booking_between_filter" value="">
                </div>
            </div>
            <input type="hidden" name="pickup_date_from" id="pickup_date_from" value="">
            <input type="hidden" name="pickup_date_to" id="pickup_date_to" value="">
            <input type="hidden" name="booking_date_from" id="booking_date_from" value="">
            <input type="hidden" name="booking_date_to" id="booking_date_to" value="">
        </div>
        </form>

        <div class="row">
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-3">
                    <button type="button" class="btn btn-primary" id="book_search"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="book_reset"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
      
  </div>
    </div>

</div>

