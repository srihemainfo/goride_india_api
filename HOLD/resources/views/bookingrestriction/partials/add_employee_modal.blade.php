{{-- keyword start with add --}}

<div id="add_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Employee Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="add_employeeForm" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Caption<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="caption" id="caption" placeholder="Enter caption">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Recurring<span class="required">&nbsp;*</span></label>
                <select  class="form-control" name="recurring" id="recurring">
                  <option value="No" selected="">No</option>
                  <option value="Yearly">Yearly</option>
                  <option value="Daily">Daily</option>
                  </select>  
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">From <span class="required">*</span></label>
                <input type="datetime-local" class="form-control" name="txtDateFrom" id="txtDateFrom" placeholder="Enter Email">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">To<span class="required">&nbsp;*</span></label>
                <input type="datetime-local" class="form-control" name="txtDateTo" id="txtDateTo" placeholder="Enter Phone No.">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
          </div>
          <!--<input type="hidden" name="add_employee_id" id="add_employee_id">-->
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="add_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
      </div>
    </div>
  </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->