{{-- keyword start with edit --}}

<div id="edit_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Employee Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit_employeeForm" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="edit_first_name" class="col-form-label">Caption<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="edit_caption" id="edit_caption" placeholder="Enter First Name">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Recurring<span class="required">&nbsp;*</span></label>
                <select  class="form-control" name="edit_recurring" id="edit_recurring">
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
                <label for="edit_email" class="col-form-label">From <span class="required">*</span></label>
                <input type="datetime-local" class="form-control" name="edit_from" id="edit_from" placeholder="Enter Email">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="edit_phone" class="col-form-label">To<span class="required">&nbsp;*</span></label>
                <input type="datetime-local" class="form-control" name="edit_to" id="edit_to" placeholder="Enter Phone No.">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
          </div>
          <!--<div class="row mt-3">-->
          <!--  <div class="col-sm-6">-->
          <!--    <div class="form-group">-->
          <!--      <div class="input-group">-->
          <!--        <input type="checkbox" class="" style="width: 20px;height: 20px; margin-right: 10px;" id="edit_is_admin" name="edit_is_admin" value="1">-->
          <!--        <label for="edit_is_admin" style="  font-weight: 500; font-size: 1.05em;"> Admin User </label>-->
          <!--      </div>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div>-->
          <input type="hidden" name="edit_employee_id" id="edit_employee_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="edit_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
      </div>
    </div>
  </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->