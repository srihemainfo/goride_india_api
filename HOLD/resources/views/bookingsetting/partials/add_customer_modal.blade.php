<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Customer Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="customerForm" name="customerForm">
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
                            <label for="first_name" class="col-form-label">Company Name<span class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="cmpny_name" id="cmpny_name" placeholder="Enter First Name">
                            <p class="text-danger invalid-first-name"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="email" class="col-form-label">Email</label>
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
                            <label for="address1" class="col-form-label">Address</label>
                            <textarea class="form-control" name="address1" id="address1"></textarea>
                            <p class="text-danger"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="remarks" class="col-form-label">Remarks</label>
                            <textarea class="form-control" name="remarks" id="remarks"></textarea>
                            <p class="text-danger"></p>
                        </div>
                    </div>
                </div>


                <div class="row">
                    
                </div>

                <input type="hidden" name="customer_id" id="customer_id">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->