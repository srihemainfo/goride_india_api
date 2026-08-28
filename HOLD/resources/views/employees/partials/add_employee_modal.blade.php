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

     <form id="add_employeeForm" name="employeeForm" autocomplete="off">

          <div class="row">

              <!-- Hidden dummy fields to prevent browser from autofilling the real fields -->

        <input type="text" name="prevent_autofill" style="display:none">

        <input type="password" name="fake_password" style="display:none">

            <div class="col-sm-12">

              <div class="form-group">

                <label for="first_name" class="col-form-label">Full Name<span class="required">&nbsp;*</span></label>

                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').slice(0, 40)">

                <p class="text-danger invalid-first-name"></p>

              </div>

            </div>

    

         <?php /*

            <div class="col-sm-12">

              <div class="form-group">

                <label for="first_name" class="col-form-label">Empolyee Type<span class="required">&nbsp;*</span></label>

                <input type="text" class="form-control" name="employee_type" id="employee_type" placeholder="Enter employer type">

                <p class="text-danger invalid-first-name"></p>

              </div>

            </div> */ ?>

       

            <div class="col-sm-12">

              <div class="form-group">

                <label for="email" class="col-form-label">Email <span class="required">*</span></label>

                <input type="email" class="form-control" name="email" id="email" oninput="this.value = this.value.slice(0, 35)" autocomplete="off" placeholder="Enter Email">

                <p class="text-danger invalid-email"></p>

              </div>

            </div>

        

            <div class="form-group">
              <label for="phone" class="col-form-label">
                Phone No. <span class="required">*</span>
              </label>

              <div class="input-group">
                <span class="input-group-text" id="country_code">+{{$myDial}}</span>
                
                <input type="text"
                      class="form-control"
                      name="phone"
                      id="phone"
                      placeholder="Enter your number"
                      required
                      oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">

              </div>

              <p class="text-danger invalid-phone-no"></p>
            </div>


            <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+{{$myDial}}" />

            <div class="col-sm-12">

              <div class="form-group">

                <label for="password">Password <span class="required">*</span></label>

                 <input type="password" class="form-control" oninput="this.value = this.value.slice(0, 20)" id="password" autocomplete="new-password" placeholder="Password" name="password">

                <p class="text-danger invalid-password"></p>

              </div>

            </div>

         

            <div class="col-sm-12">

              <div class="form-group">

                <label for="password_confirmation" class="col-form-label">Confirm Password</label>

                <input type="password" class="form-control" oninput="this.value = this.value.slice(0, 20)" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password">

              </div>

            </div>

         

          

           <div class="col-sm-12">

              <div class="form-group">

                <label for="role_id" class="col-form-label">Roles</label>

                <select class='select2 form-control role_get' id='role_id' name='role_id'>

                <option value=''>Select Role</option>

               </select>

                <p class="text-danger invalid-role_id"></p>

              </div>

            </div>

            <?php /*

            <div class="col-sm-6">

              <div class="form-group">

                <div class="input-group">

                  <input type="checkbox" class="" style="width: 20px;height: 20px; margin-right: 10px;" id="is_admin" name="is_admin" value="1">

                  <label for="is_admin" style="  font-weight: 500; font-size: 1.05em;"> Admin User </label>

                </div>

              </div>

            </div>

            */ ?>

         </div>

         

          <input type="hidden" name="add_employee_id" id="add_employee_id">

        </form>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="add_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

      </div>

    </div>

  </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->