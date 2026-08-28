{{-- keyword start with edit --}}

<div id="edit_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Range Fare Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit_employeeForm" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Vehicle<span class="required">&nbsp;*</span></label>
                    <select class="form-control" id="veh_id1" name="veh_id1">
                        <!--<option value="">-- Select Vehicle --</option>-->
                    </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Start<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="start" id="edit_start" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">End <span class="required">*</span></label>
                <input type="text" class="form-control" name="end" id="edit_end" placeholder="Enter End">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">Fare <span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="fare" id="edit_fare" placeholder="Enter Fare">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
          </div>
         
          <input type="hidden" name="fare_id" id="edit_fare_id">
          <input type="hidden" name="edit_user_id" id="edit_user_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="edit_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
      </div>
    </div>
  </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->