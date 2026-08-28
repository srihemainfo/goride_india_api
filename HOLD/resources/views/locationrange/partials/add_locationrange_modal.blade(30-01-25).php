<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title">Location Range Form</h5>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </div>

        <div class="modal-body">

            <form id="locationrangeForm" name="locationrangeForm">

                <div class="row">

                    <div class="col-sm-4">

                        <div class="form-group">

                            <label for="name" class="col-form-label">Zone Name<span class="required">&nbsp;*</span></label>

                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Zone Name">

                            <p class="text-danger invalid-name"></p>

                        </div>

                    </div>
                    <div class="col-sm-4">

                    <div class="form-group">

                        <label for="from_charge" class="col-form-label">Pichup Charge<span class="required">&nbsp;</span></label>

                        <input type="number" class="form-control" name="from_charge" id="from_charge" placeholder="0.00">

                        <p class="text-danger invalid-pickup-charge"></p>

                    </div>

                    </div>
                    <div class="col-sm-4">

                <div class="form-group">

                    <label for="to_charge" class="col-form-label">Dropoff Charge<span class="required">&nbsp;</span></label>

                    <input type="number" class="form-control" name="to_charge" id="to_charge" placeholder="0.00">

                    <p class="text-danger invalid-dropoff-charge"></p>

                </div>

                </div>

                    {{--<div class="col-sm-6">

                        <div class="form-group">

                            <label for="type" class="col-form-label">Type<span class="required">&nbsp;*</span></label>

                            <select class="form-control" id="type" name="type">



                                @foreach ($list_places as $place)

                                  <option value="{{ $place->id }}">{{ $place->place }}</option>

                                @endforeach

                            </select>

                            <p class="text-danger invalid-place-type"></p>

                        </div>

                    </div>

                </div>--}}



                <div class="row">

                    



                    



                    <!--<div class="col-sm-4">-->

                    <!--    <div class="form-group">-->

                    <!--        <label for="passing_charge" class="col-form-label">Passing Charge<span class="required">&nbsp;</span></label>-->

                    <!--        <input type="number" class="form-control" name="passing_charge" id="passing_charge" placeholder="0.00">-->

                    <!--        <p class="text-danger invalid-passing_charge"></p>-->

                    <!--    </div>-->

                    <!--</div>-->

                </div>



                <input type="hidden" name="locationrange_id" id="locationrange_id">

              </form>



              <div id="map" style="display:none; height: 250px; width: 100%;">

              </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

        </div>

      </div>

    </div> <!-- modal-bialog .// -->

  </div> <!-- modal.// -->

