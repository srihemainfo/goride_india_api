<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 35%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advance Payment Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="generateSettlementForm" name="generateSettlementForm">
                    <div class="row">
                        
                        
                        <div class="col-sm-12" id="day_select">
                            <div class="form-group">
                                <label for="date_filter" class="col-form-label">Date <span class="required">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" type="hidden" id="advance_id" name="advance_id" readonly value="">
                                    <input class="form-control" type="date" id="date_filter" name="date_filter">
                                    <!--<button type="button" class="btn btn-outline-secondary"-->
                                    <!--    onclick="(function(){$('#date_filter').datepicker('show')})()">-->
                                    <!--    <i class="fa fa-calendar"></i>-->
                                    <!--</button>-->
                                </div>
                            </div>
                        </div>
                         <div class="col-sm-12">
                            <label for="driver_name_filter">Driver Name <span class="required">*</span></label>
                            <div class="input-group">
                                <select class="form-control select2" style="width: 100%;" tabindex="-1" id="driver_name_create" name="driver_name_create"  data-placeholder="Search">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="driver_name_filter">Amount <span class="required">*</span></label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="driver_amt" name="driver_amt" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 7);">
                            </div>
                        </div>
                        <!--<div class="col-sm-12" id="load_animation" style="display: none;">-->
                        <!--    <img src="{{ asset('dashboard-assets/assets/images/loading.gif') }}">-->
                        <!--</div>-->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="payBtn"><i class="fa fa-save"></i>&nbsp;
                    Save</button>
            </div>
        </div>
    </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->
