<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 25%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Place Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="placeForm" name="placeForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label for="place" class="col-form-label">Place Name<span class="required">&nbsp;*</span></label>
                        <input type="text" class="form-control" name="place" id="place" placeholder="Enter Place Name">
                        <p class="text-danger invalid-place-name"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="discount" class="col-form-label">Discount<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="discount" id="discount" placeholder="0.00">
                            <p class="text-danger invalid-discount"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                      <div class="form-group">
                          <label for="discount_type" class="col-form-label">Type<span class="required">&nbsp;*</span></label>
                          <select class="form-control" id="discount_type" name="discount_type">
                            <option value="Flat">Flat</option>
                            <option value="Percent">Percent</option>
                          </select>
                          <p class="text-danger invalid-discount-type"></p>
                      </div>
                  </div>
              </div>

                <input type="hidden" name="place_id" id="place_id">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->