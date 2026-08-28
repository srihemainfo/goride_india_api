<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 25%;">

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title">Offer Times Form</h5>

          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </div>

       

        <div class="modal-body">

            <form id="offertimesForm" name="offertimesForm">

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                        <label for="cost" class="col-form-label">Cost<span class="required">&nbsp;*</span></label>

                        <input type="number" class="form-control" name="cost" id="cost" placeholder="0.00">

                        <p class="text-danger invalid-cost"></p>

                        </div>

                    </div>

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="from" class="col-form-label">Time From (24 hour format & Integer value Only)<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="from" id="from" min="0" max="24" placeholder="Enter from time">

                            <p class="text-danger invalid-from"></p>

                        </div>

                    </div>

                </div>



                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="to" class="col-form-label">Time To<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="to" id="to" min="0" max="24" placeholder="Enter to time">

                            <p class="text-danger invalid-to"></p>

                        </div>

                    </div>

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="content" class="col-form-label">content<span class="required">&nbsp;*</span></label>

                            <textarea rows="4" cols="50" name="content" class="form-control" id="content"></textarea>

                            <p class="text-danger invalid-content"></p>

                        </div>

                    </div>

                </div>



                <input type="hidden" name="offertime_id" id="offertime_id">

              </form>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

        </div>

      </div>

    </div> <!-- modal-bialog .// -->

  </div> <!-- modal.// -->