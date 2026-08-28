<div id="email-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Send Email (Customer)</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="emailForm" name="emailForm">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="customer_email" class="col-form-label">Email<span class="required">&nbsp;*</span></label>
                      <input type="text" class="form-control" name="customer_email" id="customer_email" value="">
                      <p class="text-danger invalid_email"></p>
                    </div>
                    
                  </div>
                  <div class="col-sm-6">
    <label for="templateSelect" class="col-form-label">Select Template<span class="required">&nbsp;*</span></label>
    <select id="templateSelect" name="template_name" class="form-control px-5" required>
        <!-- Options will be populated here -->
    </select>
</div>

                 <div class="col-sm-12">
    <div class="form-group">
      
        <label for="customer_email_send" class="col-form-label">Mail Body (Message)<span class="required">&nbsp;*</span></label>
        <button type="button" class="btn btn-primary mb-2" id="preview_email">Preview</button><br>
        <div class="col-5">
         <label for="customer_emails" class="col-form-label">Customer Name<span class="required">&nbsp;*</span></label>
<input id="customernames" type="text" name="customernames" class="form-control" placeholder="Customer Name">
</div>
        <div id="customer_email_send" class="form-control mt-2" style="height:20em; overflow-y: auto;" name="description" contenteditable="true">
          
        </div>
        <p class="text-danger invalid_message"></p>
    </div>
</div>

                </div>
              </form>
        </div>
        <div class="modal-footer">
            <div id="load_animation_email" style="display: none;">
                <div class="spinner-grow text-primary" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-secondary" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-success" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-danger" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                <span class="sr-only"></span>
                </div>
                <div class="spinner-grow text-info" role="status">
                <span class="sr-only"></span>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-paper-plane"></i>&nbsp; Send Email</button>
            </div>
        </div>
      </div>
    </div> 
  </div> 
  <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Email Preview</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="email_preview_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>