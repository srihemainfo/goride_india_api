<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 35%;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Generate Settlement Form</h5>

                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <form id="generateSettlementForm" name="generateSettlementForm">

                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="report_type" class="col-form-label">Settlement Type</label><span style="color: red;">&nbsp;*</span>

                                <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true"
                                    id="settlement_type" name="settlement_type">

                                    <!-- <option value="">-- Select Settlement Type --</option> -->

                                    <option value="Daily" selected>Daily</option>

                                    <!-- <option value="Weekly">Weekly</option> -->

                                    <!-- <option value="Custom">Custom Range</option> -->

                                </select>

                            </div>

                            <div class="form-group">

                                <label for="date_filter" class="col-form-label">Date(Select the PickUp Dates)</label><span style="color: red;">&nbsp;*</span>

                                <div class="input-group">

                                    <input class="form-control" type="text" id="date_filter" name="date_filter">

                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="(function(){$('#date_filter').datepicker('show')})()">

                                        <i class="fa fa-calendar"></i>

                                    </button>

                                </div>

                            </div>

                        </div>

                        <div class="col-sm-12" id="week_select" style="display: none;">

                            <div class="form-group">

                                <label for="week_filter_form" class="col-form-label">Week</label><span style="color: red;">&nbsp;*</span>

                                <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                                    tabindex="-1" aria-hidden="true" id="week_filter_form" name="week_filter_form"
                                    data-control="select2" data-placeholder="Select Week" data-hide-search="true">



                                    @foreach ($week_array as $week)

                                    <option value="{{ $week }}">{{ $week }}</option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <!--<div class="col-sm-12" id="month_select" style="display: none;">-->

                        <!--    <div class="form-group">-->

                        <!--        <label for="month_filter" class="col-form-label">Month</label>-->

                        <!--        <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;"-->

                        <!--            tabindex="-1" aria-hidden="true" id="month_filter" name="month_filter" data-control="select2"-->

                        <!--            data-placeholder="Select Month" data-hide-search="true">-->

                        <!--            <option value="">-- select month --</option>-->

                        <!--            @foreach ($month_array as $month)-->

                        <!--                <option value="{{ $month }}">{{ $month }}</option>-->

                        <!--            @endforeach-->

                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="col-sm-12" id="week_select" style="display: none;">-->

                        <!--    <div class="form-group">-->

                        <!--        <label for="week_filter" class="col-form-label">Week</label>-->

                        <!--        <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;"-->

                        <!--            tabindex="-1" aria-hidden="true" id="week_filter" name="week_filter" data-control="select2"-->

                        <!--            data-placeholder="Select Week" data-hide-search="true">-->

                        <!--            <option value="">-- select week --</option>-->

                        <!--            @foreach ($week_array as $week)-->

                        <!--                <option value="{{ $week }}">{{ $week }}</option>-->

                        <!--            @endforeach-->

                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!-- <div class="col-sm-12" id="day_select" style="display: none;">

                            <div class="form-group">

                                <label for="date_filter" class="col-form-label">Date(Select the PickUp Dates)</label><span style="color: red;">&nbsp;*</span>

                                <div class="input-group">

                                    <input class="form-control" type="text" id="date_filter" name="date_filter">

                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="(function(){$('#date_filter').datepicker('show')})()">

                                        <i class="fa fa-calendar"></i>

                                    </button>

                                </div>

                            </div>

                        </div> -->

                        <div class="col-sm-12" id="custom_select" style="display: none;">

                            <div class="form-group">

                                <label for="custom_filter">Custom Date Range</label><span style="color: red;">&nbsp;*</span>

                                <input type="text" class="form-control" id="custom_filter"
                                    placeholder="Select date range" name="custom_filter" value="">

                            </div>

                        </div>

                        <!-- <div class="col-sm-12">

                            <label for="driver_name_filter">Driver Name</label><span style="color: red;">&nbsp;*</span>

                            <div class="input-group">

                                <select class="form-control select2" style="width: 100%;" tabindex="-1"
                                    id="driver_name_create" name="driver_name_create" data-placeholder="Search">

                                </select>

                            </div>

                        </div> -->

                        <div class="col-sm-12">
                            <label for="driver_name_create">Driver Name</label><span style="color: red;">&nbsp;*</span>
                            <div class="input-group">
                                <select class="form-control" id="driver_name_create" name="driver_name_create">
                                    <option value="">-- Select Driver --</option>
                                    <!-- Add options dynamically here -->
                                </select>
                            </div>
                        </div>

                        <!-- <div class="col-sm-12" id="load_animation" style="display: none;">

                            <img src="{{ asset('dashboard-assets/images/loading.gif') }}">

                        </div> -->

                    </div>

                </form>

            </div>

            <!-- <div class="modal-footer"> -->

            <!-- <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp;

                    Save</button> -->
            <!-- <button class="btn btn-primary" 
                            id="saveBtn">
                            <i class="fa fa-save"></i>&nbsp; Save
                            <span class="spinner-border spinner-border-sm text-light" style="display: none;" role="status" aria-hidden="true"></span>
                        </button>

            </div> -->


            <div class="modal-footer">
                <button class="btn btn-primary" id="saveBtn">
                    <i class="fa fa-save"></i>&nbsp; Save
                    <span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                </button>
            </div>

        </div>

    </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->