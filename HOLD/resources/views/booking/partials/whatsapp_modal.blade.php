<div id="whatsapp-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Whatsapp Message</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="whatsappForm" name="whatsappForm">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="customer_whatsapp_send" class="col-form-label">Customer Message Body<span class="required">&nbsp;*</span></label>
                            <textarea id="customer_whatsapp_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="customer_whatsapp_send"></textarea>
                            <p class="text-danger invalid_message"></p>
                            <button type="button" class="btn btn-primary" id="send_customer_whatsapp_btn">
                              <i class="fab fa-whatsapp"></i>
                              <span class="spinner-border1 spinner-border-sm text-light ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                              Send to Customer
                          </button>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="driver_whatsapp_send" class="col-form-label">
                              Driver Message Body <span class="required">&nbsp;*</span>
                          </label>
                          <textarea id="driver_whatsapp_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="driver_whatsapp_send"></textarea>
                          <p class="text-danger invalid_message"></p>

                          <!-- Button Row: Send left, Assign right -->
                          <div class="d-flex justify-content-between mt-2">
                              <div>
                                  <button type="button" class="btn btn-primary" id="send_driver_whatsapp_btn">
                                      <i class="fab fa-whatsapp"></i>
                                      <span class="spinner-border2 spinner-border-sm text-light ms-2" style="display: none;" role="status" aria-hidden="true"></span>
                                      Send to Driver
                                  </button>
                                  <!-- Error under "Send" button only -->
                                  <div>
                                      <span id="driver_phone_error" class="text-danger" style="display: none;">Please assign driver</span>
                                  </div>
                              </div>

                              <button type="button" class="btn btn-primary" style="height: 40px;" id="assign_driver_button">
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


<!-- Driver Location show -->

<div id="driver-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 1060px; width: 1060px; height: 580px;">
        <div class="modal-content" style="height: 100%;">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">Driver Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0" style="height: calc(100% - 56px);"> <!-- 56px header height -->
                <div id="map1" style="height: 100%; width: 100%; display: block;"></div>
            </div>

        </div>
    </div>
</div>
