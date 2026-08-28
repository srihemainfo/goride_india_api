{{-- keyword start with edit --}}



<div id="edit_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">Employee Edit Form</h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form id="edit_employeeForm" name="employeeForm">

          <div class="row">

            <div class="col-sm-12">

              <div class="form-group">

                <label for="edit_first_name" class="col-form-label">Full Name<span class="required">&nbsp;*</span></label>

                <input type="text" class="form-control" name="edit_first_name" id="edit_first_name" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').slice(0, 40)" placeholder="Enter First Name">

                <p class="text-danger invalid-first-name"></p>

              </div>

            </div>

          

          <?Php /*

            <div class="col-sm-12">

              <div class="form-group">

                <label for="first_name" class="col-form-label">Empolyee Type<span class="required">&nbsp;*</span></label>

                <input type="text" class="form-control" name="edit_employee_type" id="edit_employee_type" placeholder="Enter employer type">

                <p class="text-danger invalid-first-name"></p>

              </div>

            </div> */ ?>

          

            <div class="col-sm-12">

              <div class="form-group">

                <label for="edit_email" class="col-form-label">Email <span class="required">*</span></label>

                <input type="text" class="form-control" oninput="this.value = this.value.slice(0, 35)" name="edit_email" id="edit_email" placeholder="Enter Email">

                <p class="text-danger invalid-email"></p>

              </div>

            </div>

        

            <!-- <div class="col-sm-12">

              <div class="form-group">

                <label for="edit_phone" class="col-form-label">Phone No.<span class="required">&nbsp;*</span></label>

  

                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)" placeholder="Enter Phone No." name="edit_phone" id="edit_phone" required="" oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                <p class="text-danger invalid-phone-no"></p>

              </div>

            </div> -->

            <div class="form-group">
              <label for="edit_phone" class="col-form-label">
                Phone No. <span class="required">*</span>
              </label>

              <div class="input-group">
                <span class="input-group-text" id="country_code_edit">+{{$myDial}}</span>
                
                <input type="text"
                      class="form-control"
                      name="edit_phone"
                      id="edit_phone"
                      placeholder="Enter your number"
                      required
                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">

              </div>

              <p class="text-danger invalid-phone-no"></p>
            </div>
            <input type="hidden" id="edit_hidden_phoneCode" name="edit_hidden_phoneCode" value="+{{$myDial}}" />

            

            <div class="col-sm-12">

              <div class="form-group">

                <label for="password_confirmation" class="col-form-label">Roles</label>

                <select class='select2 form-control role_get' id='edit_role_id' name='edit_role_id'>

                <option value=''>Select Role</option>

               </select>

                <p class="text-danger invalid-role_id"></p>

              </div>

            </div>

         <?php /*

            <div class="col-sm-6">

              <div class="form-group">

                <div class="input-group">

                  <input type="checkbox" class="" style="width: 20px;height: 20px; margin-right: 10px;" id="edit_is_admin" name="edit_is_admin" value="1">

                  <label for="edit_is_admin" style="  font-weight: 500; font-size: 1.05em;"> Admin User </label>

                </div>

              </div>

            </div>

            */ ?>

          </div>

          <input type="hidden" name="edit_employee_id" id="edit_employee_id">

          <input type="hidden" name="edit_user_id" id="edit_user_id">

        </form>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="edit_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

      </div>

    </div>

  </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->