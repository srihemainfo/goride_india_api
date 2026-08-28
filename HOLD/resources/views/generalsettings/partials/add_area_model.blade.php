
<div id="form-modal1" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Area Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form id="customerForm1" name="customerForm1">
                <div class="row">
                  <!--<div class="col-sm-12">-->
                  <!--  <label for="place_name" class="col-form-label">Place Name<span class="required">&nbsp;*</span></label>-->
                  <!--  <select class="form-control" id="place_name" name="place_name">-->
                  <!--          <option value="">-- select place --</option>-->
                  <!--      @foreach ($list_places as $place)-->
                  <!--          <option value="{{ $place->id }}">{{ $place->place }}</option>-->
                  <!--      @endforeach-->
                  <!--    </select>-->
                  <!--  <p class="text-danger invalid-place_name"></p>-->
                  <!--</div>-->
                  
                  <div class="col-sm-12">      
                      <div class="form-group">
                          <label for="area_name" class="col-form-label">Area Name<span class="required">&nbsp;*</span></label>
                          <input type="char" class="form-control" name="add_area_name" id="add_area_name" placeholder="Enter the Area">
                          <p class="text-danger invalid-area_name"></p>
                      </div>
                  </div>
              
                  <!--  <div class="col-sm-12">-->
                  <!--    <div class="form-group">-->
                  <!--        <label for="address" class="col-form-label">Address<span class="required">&nbsp;</span></label>-->
                  <!--        <input type="text" class="form-control" name="address" id="address" placeholder="Enter the Address">-->
                  <!--    </div>-->
                  <!--</div>-->

                  <!--<div class="col-sm-12">-->
                  <!--    <div class="form-group">-->
                  <!--        <label for="city" class="col-form-label">City<span class="required">&nbsp;</span></label>-->
                  <!--        <input type="text" class="form-control" name="city" id="city" placeholder="Enter the City">-->
                  <!--    </div>-->
                  <!--</div>-->
                <div class="col-sm-12">
                    <label class="col-form-label">Status</label>
                    <div class="form-group">
                        <select name="add_area_status" id="add_area_status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="InActive">InActive</option>
                        </select>
                    </div>
                </div>
                  

                  <div class="col-sm-12">
                      <div class="form-group">
                          <label for="post_code" class="col-form-label">Postal Code<span class="required">&nbsp;</span></label>
                          <input type="text" class="form-control" name="add_post_code" id="add_post_code" placeholder="Enter the Code">
                      </div>
                  </div>
                <div class="col-sm-12">
                      <div class="form-group">
                          <label for="pickup_extra" class="col-form-label">Pickup Extra<span class="required">&nbsp;</span></label>
                          <input type="text" class="form-control" name="add_pickup_extra" id="add_pickup_extra" placeholder="Enter the Code">
                      </div>
                  </div>
              <div class="col-sm-12">
                      <div class="form-group">
                          <label for="drop_extra" class="col-form-label">Drop Extra<span class="required">&nbsp;</span></label>
                          <input type="text" class="form-control" name="add_drop_extra" id="add_drop_extra" placeholder="Enter the Code">
                      </div>
                  </div>
                </div>
                <input type="hidden" name="id" id="id">
              </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="addsaveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
      </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->


  