<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 25%;">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">Change Password</h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form id="changePasswordForm" name="changePasswordForm">

          <div class="row">

              <div class="col-sm-12">

                  <div class="form-group">

                  <label for="password" class="col-form-label">Password<span class="required">&nbsp;*</span></label>

                  <input type="password" oninput="this.value = this.value.replace(/\s/g, '')" maxlength="20" class="form-control" name="change_password" id="change_password" placeholder="Enter Password">

                  <p class="text-danger invalid-change_password"></p>

                  </div>

              </div>

        

            <div class="col-sm-12">

              <div class="form-group">

                  <label for="password_confirmation" class="col-form-label">Confirm Password<span class="required">&nbsp;*</span></label>

                  <input type="password" oninput="this.value = this.value.replace(/\s/g, '')" maxlength="20" class="form-control" name="change_password_confirmation" id="change_password_confirmation" placeholder="Enter Confirm Password">

                  

              </div>

            </div>

          </div>



          <input type="hidden" name="password_user_id" id="password_user_id">

        </form>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="paswordsaveBtn"><i class="fa fa-save"></i>&nbsp; Reset Password</button>

      </div>

    </div>

  </div> 

</div> 