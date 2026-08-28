<div class="card-header">
    <h4 class="card-title">Business Details</h4>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-sm-3 mb-4">
            <label for="driver_booking_percentage">Booking Percentage</label>
            <input type="number" class="form-control" id="driver_booking_percentage" placeholder="0.00" name="driver_booking_percentage" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
            <span class="error" id="book_per_value_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-3 mb-4">
            <label for="commision_value">Commision Value</label>
            <input type="number" class="form-control" id="commision_value" placeholder="0.00" name="commision_value" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);">
            <span class="error" id="commision_value_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="booking_email">Booking Email</label>
            <input type="text" class="form-control" id="booking_email" placeholder="Booking Email" name="booking_email" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.,_]/g, '').slice(0, 30);">
            <span class="error" id="booking_value_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-3 mb-4">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date"  name="start_date" value="">
            <span class="error" id="start_value_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-3 mb-4">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date"  name="end_date" value="">
            <span class="error" id="end_value_Error" style="color: red;"></span>
        </div>
    </div>
</div>
