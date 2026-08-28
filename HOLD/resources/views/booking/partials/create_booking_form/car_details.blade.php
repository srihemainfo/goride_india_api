<div class="col-sm-12 main-card mb-3 card" id="car_container">
    <div class="card-header">
        <h4 class="card-title">Car Details</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-3">
                <label for="car_type">Car Type <span class="required">*</span></label>
                <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="car_type" name="car_type" data-control="select2" data-placeholder="Select an option" data-hide-search="true">
                    
                </select>
            </div>

            <div class="col-sm-2">
                <label for="passenger_count">No of Passengers <span class="required">*</span></label>
                <select class="form-control" id="passenger_count" name="passenger_count"></select>
            </div>

                       <div class="col-sm-2">
                <label for="luggage_count">Luggage</label>
                <select class="form-control" id="luggage_count" name="luggage_count"></select>
            </div>

            <div class="col-sm-2">
                <label for="hand_luggage_count">Hand Luggage</label>
                <select class="form-control" id="hand_luggage_count" name="hand_luggage_count"></select>
            </div>

            <div class="col-sm-3">
                <label for="child_seat_count">Child Seat Required ?</label>
                <select class="form-control" id="child_seat_count" name="child_seat_count"></select></select>
            </div>

        </div>
        <div class="row mt-2" id="child_seat_container"></div>
    </div>
</div>
