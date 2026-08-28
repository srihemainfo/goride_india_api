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
                <label for="first_name" class="col-form-label">Full Name<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Empolyee Type<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="employee_type" id="employee_type" placeholder="Enter employer type">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">Email <span class="required">*</span></label>
                <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">Phone No.<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone No.">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                <p class="text-danger invalid-password"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="password_confirmation" class="col-form-label">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password">
              </div>
            </div>
          </div>
          
          <div class="col-sm-12">
              <div class="form-group">
                <label for="role_id" class="col-form-label">Roles</label>
                <select class='select2 form-control' id='role_id' name='role_id'>
                <option value=''>Select Role</option>
                @foreach($roles as $id => $value)
                  <option value="{{ $id ?? ''}}"> {{ $value ?? ''}}</option>  
                @endforeach
               </select>
                <p class="text-danger invalid-role_id"></p>
              </div>
            </div>
            
             <div class="col-sm-12">
              <div class="form-group">
                <label for="role_id" class="col-form-label">Roles</label>
                <select class='select2 form-control' id='role_id' name='role_id'>
                    <option value=''>Select Role</option>
                </select>
                <p class="text-danger invalid-role_id"></p>
              </div>
            </div>
      
            <?php 
                /*
                    <div class="row mt-3">
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <input type="checkbox" class="" style="width: 20px;height: 20px; margin-right: 10px;" id="is_admin" name="is_admin" value="1">
                      <label for="is_admin" style="  font-weight: 500; font-size: 1.05em;"> Admin User </label>
                    </div>
                  </div>
                </div>
              </div>
                */ 
            ?>
      
          <input type="hidden" name="add_employee_id" id="add_employee_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="add_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
      </div>
    </div>
  </div> <!-- modal-bialog .// -->
</div> <!-- modal.// -->