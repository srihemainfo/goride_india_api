<div id="role_form_modal" class="modal fixed-left fade" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-aside" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Role Form</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="profileForm" name="profileForm">
                <div class="row">
                     <div class="col-sm-12">
                        <div class="form-group">
                            <label for="edit_role_name" class="col-form-label">Role Title<span class="required">&nbsp;</span></label>
                            <input type="text" class="form-control" name="edit_role_name" id="edit_role_name" maxlength="30">
                            <input type="hidden" class="form-control" name="edit_role_id" id="edit_role_id" value=''>
                            <p class="text-danger invalid-profile_name"></p>
                        </div>
                    </div>
                </div>
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="roleBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div>
  </div>
