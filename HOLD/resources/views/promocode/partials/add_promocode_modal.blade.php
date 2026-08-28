<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 35%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Promo Code Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="promocodeForm" name="promocodeForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label for="code" class="col-form-label">Code<span class="required">&nbsp;*</span></label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code">
                        <p class="text-danger invalid-code"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="min_value" class="col-form-label">Min Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="min_value" id="min_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-minvalue"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="max_value" class="col-form-label">Max Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="max_value" id="max_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-maxvalue"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="from_date" id="from_date" placeholder="Enter From Date">
                            <p class="text-danger invalid-fromdate"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="to_date" id="to_date" placeholder="Enter To Date">
                            <p class="text-danger invalid-todate"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type" class="col-form-label">Type<span class="required">&nbsp;*</span></label>
                            <select class="form-control" id="type" name="type">
                                <option value="Flat">Flat</option>
                                <option value="Percent">Percent</option>
                            </select>
                            <p class="text-danger invalid-type"></p>
                        </div>
                   </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="value" class="col-form-label">Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="values" id="value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-value"></p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="promocode_id" id="promocode_id">
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->