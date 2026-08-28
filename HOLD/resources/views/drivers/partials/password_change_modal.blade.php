<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 25%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Change Password</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="changePasswordForm" name="changePasswordForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label for="password" class="col-form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                        <p class="text-danger"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="password_confirmation" class="col-form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password">
                            <p class="text-danger invalid-password"></p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="driver_id" id="driver_id">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Reset Password</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->