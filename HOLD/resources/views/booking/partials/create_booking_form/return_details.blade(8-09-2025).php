<div class="col-sm-12 main-card mb-3 card" id="return_container">
    <div class="card-header">
        <h4 class="card-title">Return Details</h4>
    </div>

    <div class="card-body">
        <div class="row mb-2">

            <div class="col-sm-4 d-none">
                <label for="return_pick_up">Return Pick-up <span class="required">*</span></label>
                <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1"
                    aria-hidden="true" id="return_pick_up" name="return_pick_up" data-control="select2"
                    data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                    <option value=""></option>
                </select> 
                <p class="text-danger invalid_return_pick_up"></p>
            </div>

            <div class="col-sm-4 d-none">
                <label for="return_drop_off">Return Drop-off <span class="required">*</span></label>
                <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1"
                    aria-hidden="true" id="return_drop_off" name="return_drop_off" data-control="select2"
                    data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                    <option value=""></option>
                </select>
                <p class="text-danger invalid_return_drop_off"></p>
            </div>

            <div class="col-sm-4 d-none">
                <label for="return_ref_no">Reference No. <span class="required">*</span></label>
                <input class="form-control" type="text" name="return_ref_no" id="return_ref_no"
                    placeholder="Return ref no.">
                <p class="text-danger invalid_return_ref_no"></p>
            </div>
        </div>

        <div class="row border-bottom mb-1">
            <div class="col-sm-4">
                <h6>Return Journey Information</h6>
            </div>
        </div>

        <div class="row mb-4">
            <!--<div class="col-sm-2">-->
            <!--    <label for="return_pickup_date">Pickup Date <span class="required">*</span></label>-->
            <!--    <div class="input-group">-->
            <!--        <input class="form-control" type="text" id="return_pickup_date" name="return_pickup_date">-->
            <!--        <button type="button" class="btn btn-outline-secondary"-->
            <!--            onclick="(function(){$('#return_pickup_date').datepicker('show')})()"><i-->
            <!--                class="fa fa-calendar"></i></button>-->
            <!--    </div>-->
            <!--    <p class="text-danger invalid_return_pickup_date"></p>-->
            <!--</div>-->
            <div class=" col-sm-2">
                           <label for="one_way_pickup_date">Pickup Date <span class="required">*</span></label>
                            <div class=" input-group">
                                <input class="form-control"
                                  type="date"
                                  
                                  id="date1"
                                  name="return_pickup_date"
                                  min="{{ date('Y-m-d', strtotime('+0 day')) }}"
                                  required/>
                              </div>
              </div>

            <div class="col-sm-2">
                <label for="return_pickup_time">Pickup Time <span class="required">*</span></label>
                <input class="form-control" type="time" id="return_pickup_time" name="return_pickup_time">
                <p class="text-danger invalid_return_pickup_time"></p>
            </div>

            <div class="col-sm-4">
                <label for="return_pickup_address">Full Pickup Address </label>
                <input class="form-control" type="text" name="return_pickup_address" id="return_pickup_address"
                    placeholder="Full address with postcode" maxlength="150" oninput="this.value = this.value.replace(/[^A-Za-z0-9., ]/g, '')">
                <p class="text-danger invalid_return_pickup_address"></p>
            </div>

            <div class="col-sm-4">
                <label for="return_dropoff_address">Full Dropoff Address </label>
                <input class="form-control" type="text" maxlength="150" name="return_dropoff_address" id="return_dropoff_address"
                    placeholder="Full address with postcode" oninput="this.value = this.value.replace(/[^A-Za-z0-9., ]/g, '')">
                <p class="text-danger invalid_return_dropoff_address"></p>
            </div>
        </div>

        <div class="row border-bottom mb-1 return_flight_ship_details">
            <div class="col-sm-4">
                <h6 id="return_transport_name"></h6>
            </div>
        </div>

        <div class="row mb-4 return_flight_ship_details">
            <!-- <div class="col-sm-2">
                <label for="return_flight_date">Flight Landing Date <span class="required">*</span></label>
                <div class="input-group">
                    <input class="form-control" type="text" id="return_flight_date" name="return_flight_date">
                    <button type="button" class="btn btn-outline-secondary" onclick="(function(){$('#return_flight_date').datepicker('show')})()"><i class="fa fa-calendar"></i></button>
                </div>
                <p class="text-danger invalid_return_flight_date"></p>
            </div>

            <div class="col-sm-2">
                <label for="return_flight_time">Flight Landing Time <span class="required">*</span></label>
                <input class="form-control" type="time" id="return_flight_time" name="return_flight_time">
                <p class="text-danger invalid_return_flight_time"></p>
            </div> -->

            <div class="col-sm-3">
                <label for="return_flight_number">Flight Number</label>
                <input class="form-control" type="text" name="return_flight_number" id="return_flight_number"
                    placeholder="Flight Number" maxlength="10" oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')">
                <p class="text-danger invalid_return_flight_number"></p>
            </div>

            {{-- <div class="col-sm-3">
                <label for="return_flight_from">Flight From</label>
                <input class="form-control" type="text" name="return_flight_from" id="return_flight_from"
                    placeholder="Flight From" oninput="this.value = this.value.replace(/[^0-9a-zA-Z]/g, '').slice(0, 20);">
                <p class="text-danger invalid_return_flight_from"></p>
            </div> --}}

            <div class="col-sm-3">
                <label for="return_flight_pickup_time">Pickup Time After Landing</label>
                <select class="form-control" name="return_flight_pickup_time" id="return_flight_pickup_time">
                    <option value="10 min after">10 min after</option>
                    <option value="15 min after">15 min after</option>
                    <option value="20 min after">20 min after</option>
                    <option value="25 min after">25 min after</option>
                    <option value="30 min after">30 min after</option>
                    <option value="35 min after">35 min after</option>
                    <option value="40 min after">40 min after</option>
                    <option value="45 min after">45 min after</option>
                    <option value="50 min after">50 min after</option>
                    <option value="55 min after">55 min after</option>
                    <option value="60 min after">60 min after</option>
                    <option value="65 min after">65 min after</option>
                    <option value="70 min after">70 min after</option>
                    <option value="75 min after">75 min after</option>
                    <option value="80 min after">80 min after</option>
                    <option value="85 min after">85 min after</option>
                    <option value="90 min after">90 min after</option>
                    <option value="95 min after">95 min after</option>
                    <option value="100 min after">100 min after</option>
                    <option value="105 min after">105 min after</option>
                    <option value="110 min after">110 min after</option>
                    <option value="115 min after">115 min after</option>
                    <option value="120 min after">120 min after</option>
                </select>
                <p class="text-danger invalid_return_flight_pickup_time"></p>
            </div>

            <input type="hidden" name="is_airport_or_ship_return" id="is_airport_or_ship_return" value="0">
        </div>

        <div class="row border-bottom mb-1">
            <div class="col-sm-4">
                <h6>Return Payment & Order Details</h6>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-2">
                <label for="return_payment_method">Payment Method <span class="required">*</span></label>
                <select class="form-control" name="return_payment_method" id="return_payment_method">
                    <option value="Bank">Bank</option>
                    <option value="Card">Card</option>
                    <option value="Card">Cash</option>
                    <option value="Invoiced">Invoiced</option>
                </select>
                <p class="text-danger invalid_return_payment_method"></p>
            </div>

            <div class="col-sm-2">
                <label for="return_payment_status">Payment Status <span class="required">*</span></label>
                <select class="form-control" name="return_payment_status" id="return_payment_status">
                    <option value="Invoiced">Invoiced</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
                <p class="text-danger invalid_return_payment_status"></p>
            </div>

            <div class="col-sm-2">
                <label for="return_order_status">Order Status <span class="required">*</span></label>
                <select class="form-control" name="return_order_status" id="return_payment_status">
                    <option value="Confirmed">Confirmed</option>
                    <option value="Pending">Pending</option>
                </select>
                <p class="text-danger invalid_return_order_status"></p>
            </div>

            <div class="col-sm-3">
                <label for="return_total_cost">Return Total Cost <span class="getcurrencycode"></span> <span class="required">*</span> <a class="" href="/carfares" target="_blank">Check Car Fare</a></label>
                <input class="form-control text-success font-weight-bold" type="text" readonly 
                    id="return_total_cost" onkeyup="extraReturn()" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);">
                <p class="text-danger invalid_return_total_cost"></p>
            </div>

            <div class="col-sm-3 d-none">
                <label for="return_extra_cost">Extra Cost(Parking & Passing Charges) 
                    </label>
                <input class="form-control text-success font-weight-bold" type="text" name="return_extra_cost"
                    id="return_extra_cost" value="0.00" onkeyup="extraReturn()">
                <p class="text-danger invalid_return_extra_cost"></p>
            </div>

            <div class="col-sm-2">
                <label for="return_distance">Distance <span class="distance_unit"></span> <span class="required">*</span></label>
                <input class="form-control text-success font-weight-bold" type="text" name="return_distance"
                    id="return_distance" readonly>
                <p class="text-danger invalid_return_distance"></p>
            </div>

            <div class="col-sm-3">
                <label for="return_travel_time">Approx Travel Time <span class="required">*</span></label>
                <input class="form-control text-success font-weight-bold" type="text" name="return_travel_time"
                    id="return_travel_time" readonly>
                <p class="text-danger invalid_return_travel_time"></p>
            </div>
            <div class="col-sm-3">
                <label for="return_net_total" style="font-weight: bold;">Return Net Total <span class="getcurrencycode"></span></label>
                <input class="form-control text-success font-weight-bold" type="text" name="return_total_cost" style="font-weight: bold;"
                    id="return_net_total" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);" value="0">
            </div>

            <div class="col-sm-3 return_offershowhide">
                <label for="one_way_travel_time" style="margin-top: 38px;color: blue;">Special Offers Includes <span class="return_special_off_total"></span>
                <input type="hidden" name="return_offer_time_price" id="return_offer_time_price">
                <input type="hidden" name="return_offer_date_price" id="return_offer_date_price">
                <input type="hidden" name="return_overall_total" id="return_overall_total">
                </label>
            </div>

                <!--<div class="col-sm-3">-->
                <!--    <div style="position: absolute; bottom: 20%; left: 12%;">-->
                <!--        <input type="checkbox" class="form-check-input" id="is_multi_booking_required"-->
                <!--            name="is_multi_booking_required_return" value="1">-->
                <!--        <label class="form-check-label" for="is_multi_booking_required">Required multi booking</label>-->
                <!--    </div>-->
                <!--</div>-->
            
        </div>

        <div class="row border-bottom mb-1">
            <div class="col-sm-4">
                <h6>Message & Remarks</h6>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <label for="return_message">Client Message</label>
                <textarea class="form-control" name="return_message" id="return_message" row="3"></textarea>
            </div>

            <div class="col-sm-4">
                <label for="return_remarks">Driver Remarks</label>
                <textarea class="form-control" name="return_remarks" id="return_remarks" row="3"></textarea>
            </div>

            <div class="col-sm-4 d-none">
                <label for="return_payment_message">Payment Message</label>
                <textarea class="form-control" name="return_payment_message" id="return_payment_message" row="3"></textarea>
            </div>
        </div>

    </div>
</div>
<script>
    document.getElementById("date1").addEventListener("click", function () {
        this.showPicker(); 
    });
    document.getElementById("return_pickup_time").addEventListener("click", function () {
    this.showPicker();
});
</script>
