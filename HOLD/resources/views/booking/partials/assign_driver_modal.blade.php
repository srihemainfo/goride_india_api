<style>

    span.select2-dropdown.select2-dropdown--below {

    width: 220px !important;

}

span.select2-dropdown.select2-dropdown--above {

    width: 220px !important;

}
.fixed-left{
  left: 12px !important;
}
@media (max-width: 768px) {
    .modal-dialog-aside {
        width: 100% !important;
        height: 100% !important;
        margin: 0;
        max-width: none;
    }

    .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
  }

</style>

<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title">Assign Driver</h5>

          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </div>

        <div class="modal-body">

            <form id="assignDriverForm" name="areaForm">

                <div class="row">

                  <div class="col-sm-12">

                      <div class="form-group">

                          <label for="job_no" class="col-form-label">Job No.<span class="required">&nbsp;*</span></label>

                          <input type="text" class="form-control" name="job_no" id="job_no" value="" readonly>

                          <p class="text-danger invalid-job_no"></p>

                      </div>

                  </div>



                  <div class="col-sm-12">

                    <label for="driver_name" class="col-form-label">Driver Name<span class="required">&nbsp;*</span></label>

                    <div class="input-group">



                        <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 80%;" tabindex="-1" aria-hidden="true" id="driver_name" name="driver_id" data-control="select2" data-placeholder="Select driver for booking" data-hide-search="true">

                              <option value="">-- select driver --</option>

                        </select>



                        <button type="button" class="btn btn-success" title="Add Driver"  onclick="window.location.href='/driver/create'"><i class="fas fa-plus"></i></button>



                        <!--<p class="invalid_client_id text-danger"></p>-->

                        <p class="text-danger invalid-driver_name"></p>



                    </div>

                    

                  </div>



                  <div class="col-sm-12">

                    <div class="form-group">

                        <label for="total" class="col-form-label">Total Amount<span class="required">&nbsp;*</span>(<span id="currency_show"></span>)</label>

                        <input type="text" class="form-control" name="total" id="total" value="" readonly>

                        <p class="text-danger invalid-total"></p>

                    </div>

                  </div>



                  <div class="col-sm-12">

                    <div class="form-group">

                        <label for="driver_amount" class="col-form-label">Driver Amount<span class="required">&nbsp;*</span>(<span id="currency_show2"></span>)</label>

                        <input type="text" class="form-control" name="driver_amount" id="driver_amount" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" placeholder="Enter Driver Amount">

                        <p class="text-danger invalid-driver_amount"></p>

                    </div>

                  </div>

                </div>



                <input type="hidden" name="status" id="status" value="Assigned">

                <input type="hidden" name="booking_id" id="booking_id" value="">

              </form>

        </div>

        <div class="modal-footer">

          <!-- <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button> -->
          <button class="btn btn-primary" 
                            id="saveBtn">
                            <i class="fa fa-save"></i>&nbsp; Save
                            <span class="spinner-border spinner-border-sm text-light" style="display: none;" role="status" aria-hidden="true"></span>
                        </button>
        </div>

      </div>

    </div> <!-- modal-bialog .// -->

  </div> <!-- modal.// -->





