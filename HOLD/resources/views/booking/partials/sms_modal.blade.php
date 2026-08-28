
<div id="sms-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 75%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send SMS</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="smsForm" name="smsForm">
                <div class="row">
                  <div class="col-sm-6">      
                    <div class="form-group">
                      <label for="customer_no" class="col-form-label">Customer No.<span class="required">&nbsp;*</span></label>
                      <input type="text" class="form-control" name="customer_no" id="customer_no" value="">
                      <p class="text-danger invalid_customer_no"></p>
                    </div>
                  </div>

                  <div class="col-sm-6">      
                    <div class="form-group">
                      <label for="driver_no" class="col-form-label">Driver No.<span class="required">&nbsp;*</span></label>
                      <input type="text" class="form-control" name="driver_no" id="driver_no" value="">
                      <p class="text-danger invalid_driver_no"></p>
                    </div>
                  </div>


                  <div class="col-sm-6">      
                      <div class="form-group">
                        <label for="customer_message" class="col-form-label">Customer Message<span class="required">&nbsp;*</span></label>
                        <textarea class="form-control" name="customer_message" id="customer_message" style="height:20em" value=""></textarea>
                        <p class="text-danger invalid_customer_message"></p>
                      </div>
                  </div>

                <div class="col-sm-6">      
                    <div class="form-group">
                      <label for="driver_message" class="col-form-label">Driver Message<span class="required">&nbsp;*</span></label>
                      <textarea class="form-control" name="driver_message" id="driver_message" style="height:20em" value=""></textarea>
                      <p class="text-danger invalid_driver_message"></p>
                    </div>
                </div>

                <div class="col-sm-6">      
                    <div class="form-group">
                      <div class="form-check">
                          <label class="form-check-label">
                            <input type="checkbox" name="sms" id="customer_sms" class="form-check-input" value="SC">SMS Customer
                          </label>
                      </div>
                    </div>
                </div>

                <div class="col-sm-6">      
                  <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                          <input type="checkbox" name="sms" id="driver_sms" class="form-check-input" value="SD" checked>SMS Driver
                        </label>
                    </div>
                  </div>
              </div>

                </div>
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="sms_send_btn"><i class="fa fa-paper-plane"></i>&nbsp; Send SMS</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->


  