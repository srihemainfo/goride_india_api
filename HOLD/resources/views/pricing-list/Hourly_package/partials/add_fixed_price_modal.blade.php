<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Fixed Price Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="fixedPriceForm" name="fixedPriceForm">
                <div class="row">
                    <div class="col-sm-12">
                        <label for="place_from" class="col-form-label">From Place<span class="required">&nbsp;*</span></label>
                        <select class="form-control select2 select2-hidden-accessible" id="place_from" name="place_from" style="width: 100%;" tabindex="-1" aria-hidden="true" data-control="select2" data-placeholder="-- Select From Place --" data-hide-search="true">
                                <option value=""></option>
                           
                                <option value=""></option>
                           
                          </select>
                        <p class="text-danger invalid-place_from"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <label for="area_from" class="col-form-label">From Area<span class="required">&nbsp;*</span></label>
                        <select class="form-control select2 select2-hidden-accessible" id="area_from" name="area_from" style="width: 100%;" tabindex="-1" aria-hidden="true" data-control="select2" data-placeholder="-- Select From Area --" data-hide-search="true" disabled>
                                <option value=""></option>
                          </select>
                        <p class="text-danger invalid-area_from"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <label for="place_to" class="col-form-label">To Place<span class="required">&nbsp;*</span></label>
                        <select class="form-control select2 select2-hidden-accessible" id="place_to" name="place_to" style="width: 100%;" tabindex="-1" aria-hidden="true" data-control="select2" data-placeholder="-- Select To Place --" data-hide-search="true">
                                <option value=""></option>
                          
                                <option value=""></option>
                           
                          </select>
                        <p class="text-danger invalid-place_to"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <label for="area_to" class="col-form-label">To Area<span class="required">&nbsp;*</span></label>
                        <select class="form-control select2 select2-hidden-accessible" id="area_to" name="area_to" style="width: 100%;" tabindex="-1" aria-hidden="true" data-control="select2" data-placeholder="-- Select To Area --" data-hide-search="true" disabled>
                                <option value=""></option>
                          </select>
                        <p class="text-danger invalid-area_to"></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="saloon" class="col-form-label">Sedan</label>
                            <input type="number" class="form-control" name="sedan" id="sedan" placeholder="0">
                            <p class="text-danger invalid-saloon"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="estate" class="col-form-label">Minivan</label>
                            <input type="number" class="form-control" name="minivan" id="minivan" placeholder="0">
                            <p class="text-danger invalid-estate"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv" class="col-form-label">7 Seater Van</label>
                            <input type="number" class="form-control" name="seater7" id="seater7" placeholder="0">
                            <p class="text-danger invalid-hand-luggage"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv5" class="col-form-label">8 Seater Van</label>
                                <input type="number" class="form-control" name="seater8" id="seater8" placeholder="0">
                            <p class="text-danger invalid-mpv5"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv6" class="col-form-label">Executive Sedan</label>
                            <input type="number" class="form-control" name="executive" id="executive" placeholder="0">
                            <p class="text-danger invalid-mpv6"></p>
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv8" class="col-form-label">Executive Minivan</label>
                            <input type="number" class="form-control" name="mpv_executive" id="mpv_executive" placeholder="0">
                            <p class="text-danger invalid-mpv8"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="executive" class="col-form-label">Shared Ride</label>
                            <input type="number" class="form-control" name="sharedride" id="sharedride" placeholder="0">
                            <p class="text-danger invalid-executive"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv_executive" class="col-form-label">16 Seater Bus</label>
                            <input type="number" class="form-control" name="sixteenseater" id="sixteenseater" placeholder="0">
                            <p class="text-danger invalid-no-seats"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="executive" class="col-form-label">22 Seater Bus</label>
                            <input type="number" class="form-control" name="twotwoseater" id="twotwoseater" placeholder="0">
                            <p class="text-danger invalid-executive"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv_executive" class="col-form-label">32 Seater Bus</label>
                            <input type="number" class="form-control" name="seater32" id="seater32" placeholder="0">
                            <p class="text-danger invalid-no-seats"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="executive" class="col-form-label">44 Seater Bus</label>
                            <input type="number" class="form-control" name="seater44" id="seater44" placeholder="0">
                            <p class="text-danger invalid-executive"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="mpv_executive" class="col-form-label">55 Seater Bus</label>
                            <input type="number" class="form-control" name="seater55" id="seater55" placeholder="0">
                            <p class="text-danger invalid-no-seats"></p>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="price_id" id="price_id">
                <input type="hidden" name="area_from_name" id="area_from_name">
                <input type="hidden" name="area_to_name" id="area_to_name">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->