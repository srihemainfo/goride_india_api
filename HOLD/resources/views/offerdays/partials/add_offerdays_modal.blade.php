<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 25%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Offer Days Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="offerdaysForm" name="offerdaysForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label for="cost" class="col-form-label">Cost<span class="required">&nbsp;*</span></label>
                        <input type="number" class="form-control" name="cost" id="cost" placeholder="Enter the Cost">
                        <p class="text-danger invalid-cost"></p>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="dates" class="col-form-label">Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="dates" id="dates" placeholder="Enter the Date">
                            <p class="text-danger invalid-dates"></p>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="content" class="col-form-label">Text<span class="required">&nbsp;*</span></label>
                            <textarea rows="4" cols="50" name="content" class="form-control" id="content"></textarea>
                            <p class="text-danger invalid-content"></p>
                        </div>
                    </div>
                </div>

                

                <input type="hidden" name="offerdays_id" id="offerdays_id">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->