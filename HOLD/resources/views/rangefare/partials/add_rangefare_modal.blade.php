{{-- keyword start with add --}}

<div id="add_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Range Fare Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="add_employeeForm" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Vehicle<span class="required">&nbsp;*</span></label>
                    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="veh_id" name="veh_id" data-control="select2" data-placeholder="Select an option" data-hide-search="true" multiple>
                        <option value="">-- Select Vehicle --</option>
                    </select>


              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Start<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="start" id="start" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">End <span class="required">*</span></label>
                <input type="text" class="form-control" name="end" id="end" placeholder="Enter End">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">Fare <span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="fare" id="fare" placeholder="Enter Fare">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="add_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
      </div>
    </div>
  </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->