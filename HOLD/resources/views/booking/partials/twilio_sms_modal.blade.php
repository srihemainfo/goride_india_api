<div id="twilio_sms_modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Sms Message</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button> 
        </div>
        <div class="modal-body">
            <form id="twiliosmsmodel" name="twiliosmsmodel">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="customer_sms_send" class="col-form-label">Customer Message Body<span class="required">&nbsp;*</span></label>
                            <textarea id="customer_sms_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="customer_sms_send"></textarea>
                            <p class="text-danger invalid_message"></p>
                            <button type="button" class="btn btn-primary" id="send_customer_sms_btn">
                              <i class="fa fa-comment"></i>
                              <span class="spinner-border1 spinner-border-sm text-light ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                              Send to Customer
                          </button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="driver_sms_send" class="col-form-label">
                              Driver Message Body <span class="required">&nbsp;*</span>
                          </label>
                          <textarea id="driver_sms_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="driver_sms_send"></textarea>
                          <p class="text-danger invalid_message"></p>

                          <!-- Button Row: Send left, Assign right -->
                          <div class="d-flex justify-content-between mt-2">
                              <div>
                                  <button type="button" class="btn btn-primary" id="send_driver_sms_btn">
                                      <i class="fa fa-comment"></i>
                                      <span class="spinner-border2 spinner-border-sm text-light ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                                      Send to Driver
                                  </button>
                                  <!-- Error under "Send" button only -->
                                  <div>
                                      <span id="driver_sms_error" class="text-danger" style="display: none;">Please assign driver</span>
                                  </div>
                              </div>

                              <button type="button" class="btn btn-primary" style="height: 40px;" id="assign_driver_sms_button">
                                  Assign Driver
                              </button>
                          </div>
                      </div>
                  </div>

                </div>
            </form>
        </div>
      </div>
    </div> 
</div>

