{{-- <div class="col-sm-12 main-card mb-3 card" id="journey_container">
    <div class="card-header">
        <h4 class="card-title">Journey Details</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <label>Journey Type <span class="required">*</span></label>
                    <div class="form-check">
                        <div class="mr-5">
                            <input value="One Way" class="form-check-input" type="radio" name="journey_type" id="one_way" checked>
                            <label class="form-check-label" for="one_way"> One Way </label>
                        </div>
                        <div>
                            <input value="Return" class="form-check-input" type="radio" name="journey_type" id="return_journey">
                            <label class="form-check-label" for="return_journey"> Return </label>
                        </div>
                    </div>
                <p class="text-danger invalid-journey-type"></p>
            </div> --}}
            <div class="col-sm-3 d-none">
                <label>Booking Date <span class="required">*</span></label>
                <div class="input-group">
                        <input class="form-control" type="text" name="booking_date" value="{{ date('d-m-Y') }}" readonly>
                        <button type="button" class="btn btn-outline-secondary"><i class="fa fa-calendar"></i></button>
                    <p class="text-danger invalid-booking-date"></p>
                </div>
            </div>
        {{-- </div>
    </div>
</div> --}}
