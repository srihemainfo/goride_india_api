{{-- keyword start with add --}}

<div id="add_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 40%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Airport Range Fare Form</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
               <div class="container-fluid mt-4">
  <div class="row justify-content-center">
    <div class="col-md-12 main">
      <ul class="nav nav-pills" id="myTab" role="tablist">
        <li class="nav-item pf">
          <a class="nav-link pf-ac active" id="intro-tab" data-toggle="tab" href="#intro" role="tab" aria-controls="intro" aria-selected="true">Add From Airport Fare </a>
        </li>
        <li class="nav-item pf">
          <a class="nav-link pf-ac" id="intro-tab-to" data-toggle="tab" href="#introto" role="tab" aria-controls="introto" aria-selected="true">Add To Airport Fare </a>
        </li>
        <li class="nav-item pf">
          <a class="nav-link pf-ac" id="sites-tab" data-toggle="tab" href="#sites" role="tab" aria-controls="sites" aria-selected="false">Add Hour Fare</a>
        </li>
        
       
      </ul>
<div class="tab-content mb-4" id="myTabContent">
  <div class="tab-pane fade show active" id="intro" role="tabpanel" aria-labelledby="intro-tab">
      <form id="add_employeeFormairport" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Vehicle<span class="required">&nbsp;*</span></label>
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="veh_id" name="veh_id"  data-placeholder="Select an option">
                        <!--<option value="">-- Select Vehicle --</option>-->
                    </select>


              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Airport<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="from_airport" id="from_airport" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
        <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Start<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="start" id="start" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">End <span class="required">*</span></label>
                <input type="text" class="form-control" name="end" id="end" placeholder="Enter End">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">Fare <span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="fare" id="fare" placeholder="Enter Fare">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
            <div class="row justify-content-center">
             <div class="col-md-4 text-center">
                 <button type="button" class="btn btn-primary" id="add_saveBtnairport"><i class="fa fa-save"></i>&nbsp; Save</button>
            </div>
          </div>
          </div>
        </form>
  </div>
  <div class="tab-pane fade" id="introto" role="tabpanel" aria-labelledby="intro-tab-to">
      <form id="add_employeeFormairportto" name="employeeForm"> 
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Vehicle<span class="required">&nbsp;*</span></label>
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="veh_id_to" name="veh_id"  data-placeholder="Select an option">
                        <!--<option value="">-- Select Vehicle --</option>-->
                    </select>


              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Airport<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="to_airport" id="to_airport" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
        <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Start<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="start" id="start" placeholder="Enter Start">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="email" class="col-form-label">End <span class="required">*</span></label>
                <input type="text" class="form-control" name="end" id="end" placeholder="Enter End">
                <p class="text-danger invalid-email"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="phone" class="col-form-label">Fare <span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control" name="fare" id="fare" placeholder="Enter Fare">
                <p class="text-danger invalid-phone-no"></p>
              </div>
            </div>
            <div class="row justify-content-center">
             <div class="col-md-4 text-center">
                 <button type="button" class="btn btn-primary" id="add_saveBtnairport_to"><i class="fa fa-save"></i>&nbsp; Save</button>
            </div>
          </div>
          </div>
        </form>
  </div>
  <div class="tab-pane fade" id="sites" role="tabpanel" aria-labelledby="sites-tab">
  <div class="edit-pf">
    <div class="col-md-12">
 <form id="add_employeeFormhour" name="employeeForm">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Vehicle<span class="required">&nbsp;*</span></label>
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="veh" name="veh_id"  data-placeholder="Select an option" data-hide-search="true" >
                        <!--<option value="">-- Select Vehicle --</option>-->
                    </select>


              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group">
                <label for="first_name" class="col-form-label">Hour Fare<span class="required">&nbsp;*</span></label>
                <input type="text" class="form-control TEXTAIRPORT" name="hour_fare" id="hour_fare" placeholder="Enter Your Hour Fare">
                <p class="text-danger invalid-first-name"></p>
              </div>
            </div>
          </div>
        <!--<div class="row">-->
        <!--    <div class="col-sm-12">-->
        <!--      <div class="form-group">-->
        <!--        <label for="first_name" class="col-form-label">Start<span class="required">&nbsp;*</span></label>-->
        <!--        <input type="text" class="form-control" name="start" id="start" placeholder="Enter Start">-->
        <!--        <p class="text-danger invalid-first-name"></p>-->
        <!--      </div>-->
        <!--    </div>-->
        <!--  </div>-->
          <!--<div class="row">-->
          <!--  <div class="col-sm-12">-->
          <!--    <div class="form-group">-->
          <!--      <label for="email" class="col-form-label">End <span class="required">*</span></label>-->
          <!--      <input type="text" class="form-control" name="end" id="end" placeholder="Enter End">-->
          <!--      <p class="text-danger invalid-email"></p>-->
          <!--    </div>-->
          <!--  </div>-->
          <!--</div>-->
          <div class="row">
            <!--<div class="col-sm-12">-->
            <!--  <div class="form-group">-->
            <!--    <label for="phone" class="col-form-label">Fare <span class="required">&nbsp;*</span></label>-->
            <!--    <input type="text" class="form-control" name="fare" id="fare" placeholder="Enter Fare">-->
            <!--    <p class="text-danger invalid-phone-no"></p>-->
            <!--  </div>-->
            <!--</div>-->
            <div class="row justify-content-center">
        <div class="col-md-4 text-center">
                 <button type="button" class="btn btn-primary" id="add_saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
            </div>
          </div>
          </div>
        </form>
  </div>
  </div>

</div>
    </div>
  </div>
</div>
</div>
       
      </div>
      <div class="modal-footer">
       
      </div>
    </div>
  </div> 
</div>