<div class="col-sm-12 main-card mb-3 card" id="outward_container">
    <div class="card-header">
        <h4 class="card-title">Outward Details</h4>
    </div>

    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-4">
                <label for="one_way_pick_up">Pick-up <span class="required">*</span></label>
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1"
                        aria-hidden="true" id="one_way_pick_up" name="one_way_pick_up" data-control="select2"
                        data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                        <option value=""></option>
                    </select>
                
                <p class="text-danger invalid_one_way_pick_up"></p>
                <input type="hidden" name="from_place_id" id="from_place_id" value="">
            </div>

            <div class="col-sm-4">
                <label for="one_way_drop_off">Drop-off <span class="required">*</span></label>
                
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1"
                        aria-hidden="true" id="one_way_drop_off" name="one_way_drop_off" data-control="select2"
                        data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                        <option value=""></option>
                    </select>
                
                <p class="text-danger invalid_one_way_drop_off"></p>
                <input type="hidden" name="to_place_id" id="to_place_id" value="">
            </div>

            <div class="col-sm-4">
                <label for="one_way_ref_no">Reference No. </label>
                <input class="form-control" type="text" name="one_way_ref_no" id="one_way_ref_no"
                    placeholder="Outward ref no." value="">
                <p class="text-danger invalid_one_way_ref_no"></p>
            </div>

            <div class="col-sm-3 ml-2 mt-4 mb-3">
                <div id="pickup_points_container" style="position: absolute; bottom: 20%; left: 12%;">
                    <input type="checkbox" class="form-check-input" id="pickup_points"
                        name="pickup_points_one_way" value="1">
                    <label class="form-check-label" for="pickup_points"><b>Via Points</b></label>
                </div>
            </div>

            <div class="col-sm-12" id="pickup_point_container" style="display: none">
                <div class="row mb-3" id="pickup_point_div">
                    <div class="col-sm-4 mb-1">
                        <label for="pick_up_point_select">Add Pickup Points</label>
                        <select class="form-control select2 select2-hidden-accessible pick_up_point_select" style="width: 100%;" tabindex="-1"
                            aria-hidden="true" name="pick_up_point_select" data-control="select2"
                            data-placeholder="Enter Airport, Seaport, Postcode" data-hide-search="true">
                        </select>
                    </div>
                    <div class="col-sm-2 mb-1">
                        <button type="button" class="btn btn-primary add_pickup_point" title="Add New Pickup Point"
                            style="position: absolute; bottom: 0;">
                            <i class="fa fa-plus" aria-hidden="true"></i> &nbsp;Add Point
                        </button>
                    </div>
                </div>
                <div class="row mb-3" id="points_values">
                </div>
                <div class="row mb-3">
                    <div class="ml-4">
                        <button type="button" class="btn btn-success calc_new_amount" title="Newly Calculated Amount">
                            <i class="fa fa-redo" aria-hidden="true"></i> &nbsp;&nbsp; Recalculate Amount
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row border-bottom mb-1">
            <div class="col-sm-4">
                <h6>Journey Information</h6>
            </div>
        </div>

        <div class="row mb-4">
            <!--<div class="col-sm-2">-->
            <!--    <label for="one_way_pickup_date">Pickup Date <span class="required">*</span></label>-->
            <!--    <div class="input-group">-->
            <!--        <input class="form-control" type="text" id="one_way_pickup_date" name="one_way_pickup_date">-->
            <!--        <button type="button" class="btn btn-outline-secondary"-->
            <!--            onclick="(function(){$('#one_way_pickup_date').datepicker('show')})()"><i-->
            <!--                class="fa fa-calendar"></i></button>-->
            <!--    </div>-->
            <!--    <p class="text-danger invalid_one_way_pickup_date"></p>-->
            <!--</div>-->
             <div class=" col-sm-2">
                           <label for="one_way_pickup_date">Pickup Date <span class="required">*</span></label>
                            <div class=" input-group">
                                <input class="form-control"
                                  type="date"
                                  
                                  id="date"
                                  name="one_way_pickup_date"
                                  min="{{ date('Y-m-d', strtotime('+0 day')) }}"
                                  required/>
                              </div>
              </div>

            <div class="col-sm-2">
                <label for="one_way_pickup_time">Pickup Time <span class="required">*</span></label>
                <input class="form-control" type="time" id="one_way_pickup_time" name="one_way_pickup_time"
                    value="">
                <p class="text-danger invalid_one_way_pickup_time"></p>
            </div>

            <div class="col-sm-4">
                <label for="one_way_pickup_address">Full Pickup Address </label>
                <input class="form-control" type="text" name="one_way_pickup_address" id="one_way_pickup_address"
                    placeholder="Full address with postcode"
                    value="">
                <p class="text-danger invalid_one_way_pickup_address"></p>
            </div>

            <div class="col-sm-4">
                <label for="one_way_dropoff_address">Full Dropoff Address </label>
                <input class="form-control" type="text" name="one_way_dropoff_address" id="one_way_dropoff_address"
                    placeholder="Full address with postcode"
                    value="">
                <p class="text-danger invalid_one_way_dropoff_address"></p>
            </div>

        </div>

        <div class="row border-bottom mb-1 one_way_arrival_flight_ship_details">
            <div class="col-sm-4">
                <h6 id="one_way_transport_name">Arrival Details</h6>
            </div>
        </div>

        <div class="row mb-4 one_way_arrival_flight_ship_details">

            <!--<div class="col-sm-2">
                <label for="one_way_flight_date">Flight Landing Date <span class="required">*</span></label>
                <div class="input-group">
                    <input class="form-control" type="text" id="one_way_flight_date" name="one_way_flight_date">
                    <button type="button" class="btn btn-outline-secondary" onclick="(function(){$('#one_way_flight_date').datepicker('show')})()"><i class="fa fa-calendar"></i></button>
                </div>
                <p class="text-danger invalid_one_way_flight_date"></p>
            </div>

            <div class="col-sm-2">
                <label for="one_way_flight_time">Flight Landing Time <span class="required">*</span></label>
                <input class="form-control" type="time" id="one_way_flight_time" name="one_way_flight_time">
                <p class="text-danger invalid_one_way_flight_time"></p>
            </div>-->

            <div class="col-sm-3">
                <label for="one_way_flight_number"> <span class="required">*</span></label>
                
                    <input class="form-control" type="text" name="one_way_flight_number"
                        id="one_way_flight_number" placeholder="Flight Number"
                        value="">
                
                <p class="text-danger invalid_one_way_flight_number"></p>
            </div>

            {{-- <div class="col-sm-3">
                <label for="one_way_flight_from"> <span class="required">*</span></label>
                <input class="form-control" type="text" name="one_way_flight_from" id="one_way_flight_from"
                    placeholder="Flight From"
                    value="{{ $isEditable && $booking_details->pickup_flight_from ? $booking_details->pickup_flight_from : '' }}">
                <p class="text-danger invalid_one_way_flight_from"></p>
            </div> --}}

            <div class="col-sm-3" id="pickup_time_container">
                <label for="one_way_flight_pickup_time">Pickup Time After Landing? <span
                        class="required">*</span></label>
                <select class="form-control" name="one_way_flight_pickup_time" id="one_way_flight_pickup_time">
                    <option value="10 min after">10 min after</option>
                    <option value="15 min after">15 min after</option>
                    <option value="20 min after">20 min after</option>
                    <option value="25 min after">25 min after</option>
                    <option value="30 min after">30 min after</option>
                    <option value="35 min after">35 min after</option>
                    <option value="40 min after">40 min after</option>
                    <option value="45 min after" selected>45 min after</option>
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
                <p class="text-danger invalid_one_way_flight_pickup_time"></p>
            </div>

            <input type="hidden" name="is_airport_or_ship_one_way" id="is_airport_or_ship_one_way" value="0">
        </div>

        <div class="row border-bottom mb-1 one_way_departure_flight_ship_details">
            <div class="col-sm-4">
                <h6 id="one_way_transport_name">Departure Details</h6>
            </div>
        </div>

        <div class="row mb-4 one_way_departure_flight_ship_details">
            <div class="col-sm-3">
                <label for="one_way_dest_ship_name">Departure Cruise Name </label>
                <input class="form-control" type="text" name="one_way_dest_ship_name"
                    id="one_way_dest_ship_name" placeholder="Departure Cruise Name"
                    value="">
                <p class="text-danger invalid_one_way_dest_ship_name"></p>
            </div>
        </div>

        <div class="row border-bottom mb-1">
            <div class="col-sm-4">
                <h6>Payment & Order Details</h6>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-sm-2">
                <label for="one_way_payment_method">Payment Method <span class="required">*</span></label>
                <select class="form-control" name="one_way_payment_method" id="one_way_payment_method">
                    <option value="Bank">Bank</option>
                    <option value="Card">Card</option>
                    <option value="Cash">Cash</option>
                    <option value="Invoiced">Invoiced</option>
                </select>
                <p class="text-danger invalid_one_way_payment_method"></p>
            </div>

            <div class="col-sm-2">
                <label for="one_way_payment_status">Payment Status <span class="required">*</span></label>
                <select class="form-control" name="one_way_payment_status" id="one_way_payment_status">
                    <option value="Invoiced">Invoiced</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
                <p class="text-danger invalid_one_way_payment_status"></p>
            </div>

            <div class="col-sm-2">
                <label for="one_way_order_status">Order Status <span class="required">*</span></label>
                <select class="form-control" name="one_way_order_status" id="one_way_order_status">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Pending">Pending</option>
                </select>
                <p class="text-danger invalid_one_way_order_status"></p>
            </div>

            <div class="col-sm-3">
                <label for="one_way_total_cost">Total Cost <span class="required">*</span></label>
                <input class="form-control text-success font-weight-bold" type="text" name="one_way_total_cost"
                    id="one_way_total_cost" value="">
                <p class="text-danger invalid_one_way_total_cost"></p>
            </div>

            <div class="col-sm-3">
                <label for="one_way_extra_cost">Extra Cost(Parking & Passing Charges) <span
                        class="required">*</span></label>
                <input class="form-control text-success font-weight-bold" type="text" name="one_way_extra_cost"
                    id="one_way_extra_cost"
                    value="">
                <p class="text-danger invalid_one_way_extra_cost"></p>
            </div>

            <div class="col-sm-2">
                <label for="one_way_distance">Distance (In miles)
                        <span class="required">*</span>
                </label>
                <input class="form-control text-success font-weight-bold" type="text" name="one_way_distance"
                    id="one_way_distance" readonly
                    value="">
                <p class="text-danger invalid_one_way_distance"></p>
            </div>

            <div class="col-sm-2">
                <label for="one_way_travel_time">Approx Travel Time
                        <span class="required">*</span>
                </label>
                <input class="form-control text-success font-weight-bold" type="text" name="one_way_travel_time"
                    id="one_way_travel_time" readonly
                    value="">
                <p class="text-danger invalid_one_way_travel_time"></p>
            </div>

                <!--<div class="col-sm-3">-->
                <!--    <div style="position: absolute; bottom: 20%; left: 12%;">-->
                <!--        <input type="checkbox" class="form-check-input" id="is_multi_booking_required"-->
                <!--            name="is_multi_booking_required_one_way" value="1">-->
                <!--        <label class="form-check-label" for="is_multi_booking_required"><b>Required multi booking</b></label>-->
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
                <label for="one_way_message">Client Message</label>
                <textarea class="form-control" name="one_way_message" id="one_way_message" row="3"></textarea>
            </div>

            <div class="col-sm-4">
                <label for="one_way_remarks">Driver Remarks</label>
                <textarea class="form-control" name="one_way_remarks" id="one_way_remarks" row="3"></textarea>
            </div>

            <div class="col-sm-4">
                <label for="one_way_payment_message">Payment Message</label>
                <textarea class="form-control" name="one_way_payment_message" id="one_way_payment_message" row="3"></textarea>
            </div>
        </div>
    </div>
</div>
