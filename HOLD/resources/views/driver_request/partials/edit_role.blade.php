<div id="role_form_modal" class="modal fixed-left fade" tabindex="-1" role="dialog" >
    <div class="modal-dialog modal-dialog-aside" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit RoleForm</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="profileForm" name="profileForm">
                <div class="row">
                     <div class="col-sm-12">
                        <div class="form-group">
                            <label for="edit_role_name" class="col-form-label">Role Title<span class="required">&nbsp;</span></label>
                            <input type="text" class="form-control" name="edit_role_name" id="edit_role_name">
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
