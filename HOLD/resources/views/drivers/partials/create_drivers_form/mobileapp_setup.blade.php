<div class="card-header">

    <h4 class="card-title">Mobile App Setup</h4>

</div>

<div class="card-body">

    <div class="row">

        <div class="col-sm-3">

            <label for="refresh_time">Refresh Time</label>

            <div class="input-group mb-3">

                <!-- Set a default value of '5' -->

                <input type="text" class="form-control" id="refresh_time" placeholder="Refresh Time" name="refresh_time" value="5" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">

                <div class="input-group-append">

                    <span class="input-group-text" id="basic-addon2">Mins</span>

                </div>

            </div>

        </div>

        <div class="col-sm-3">

            <label for="before_reminder_time">Before Reminder Time</label>

            <div class="input-group mb-3">

                <!-- Set a default value of '10' -->

                <input type="text" class="form-control" id="before_reminder_time" placeholder="Before Reminder Time" name="before_reminder_time" value="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">

                <div class="input-group-append">

                    <span class="input-group-text" id="basic-addon2">Mins</span>

                </div>

            </div>

        </div>

        <div class="col-sm-3">

            <label for="start_journey_gaptime">Start Journey Gaptime</label>

            <div class="input-group mb-3">

                <!-- Set a default value of '2' -->

                <input type="text" class="form-control" id="start_journey_gaptime" placeholder="Start Journey Gaptime" name="start_journey_gaptime" value="2" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">

                <div class="input-group-append">

                    <span class="input-group-text" id="basic-addon2">Hrs</span>

                </div>

            </div>

        </div>

        <div class="col-sm-3">

            <label for="customer_call">Customer Care No. (Driver)</label>

            <!-- Set a default value like '1234567890' -->

            <input type="text" class="form-control" id="customer_call" placeholder="Customer Call No." name="customer_call" value="1234567890" maxlength="15" minlength="10" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15); 

                        if (this.value.length < 10) this.setCustomValidity('Minimum 10 digits required');

                        else if (this.value.length > 15) this.setCustomValidity('Maximum 15 digits allowed');

                        else this.setCustomValidity('');">

        </div>

    </div>

</div>

