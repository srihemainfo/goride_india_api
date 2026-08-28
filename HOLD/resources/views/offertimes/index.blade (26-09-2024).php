@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-10 main-card mb-3 card">
   
    
    <div class="card-header row container">
         <div class="col-10">
       <h4 class="card-title mt-0  p-0">Offer Times List</h4>
       </div>
       <div class="col-2">
      <button type="button" class="btn btn-success footable-add float-end " data-bs-toggle="modal" data-bs-target="#add-modal"><i class="fas fa-plus me-2"></i>Add New</button> 
   </div>
   
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                          <th>#</th>
                        <!--<th>ID</th>-->
                        <th>Cost</th>
                        <th>Time From</th>
                        <th>Time To</th>
                        <th>Text</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    <!--Modal Add New-->

<div id="add-modal" class="modal fade fixed-left" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="fleetForm">
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
                                <label for="from" class="col-form-label">Time From<span class="required">&nbsp;*</span></label>
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
                                <label for="content" class="col-form-label">Content<span class="required">&nbsp;*</span></label>
                                <textarea rows="4" cols="50" name="content" class="form-control" id="content"></textarea>
                                <p class="text-danger invalid-content"></p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="editVehicleId" name="id">
                </form>
            </div>
            <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
        </div>
    </div>
</div>






<!--Modal Edit New-->

<!--Modal Edit New-->

<div id="editor-modal" class="modal fixed-left fade" tabindex="-1" aria-labelledby="editorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editorModalLabel">Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                <form id="EditVehicleForm">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="cost" class="col-form-label">Cost<span class="required">&nbsp;*</span></label>
                                <input type="number" class="form-control" name="editcost" id="editcost" placeholder="0.00">
                                <p class="text-danger invalid-cost"></p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="from" class="col-form-label">Time From<span class="required">&nbsp;*</span></label>
                                <input type="number" class="form-control" name="editfrom" id="editfrom" min="0" max="24" placeholder="Enter from time">
                                <p class="text-danger invalid-from"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="to" class="col-form-label">Time To<span class="required">&nbsp;*</span></label>
                                <input type="number" class="form-control" name="editto" id="editto" min="0" max="24" placeholder="Enter to time">
                                <p class="text-danger invalid-to"></p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="content" class="col-form-label">Content<span class="required">&nbsp;*</span></label>
                                <textarea rows="4" cols="50" name="editcontent" class="form-control" id="editcontent"></textarea>
                                <p class="text-danger invalid-content"></p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="editVehicleId" name="id">
                     <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="UpdateprimaryBtn"><i class="fa fa-save"></i>&nbsp; Update</button>
          </div>
                </form>
            </div>
    </div>
  </div>
</div>
</div>
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block" style="background-color: #343a40;">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/fleet" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">
      <i class="fa-solid fa-car" style="margin-right: 8px;"></i> List Fleets
    </a>
    
    <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/offertimes" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Offer Times
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/offerdays" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i> Offer Days
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/promocode" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i> Promo Code
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/notifications" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fa-regular fa-bell" style="margin-right: 8px;"></i> Notification
    </a>
    
  </div>
</div>
</div>
</div>
<style>
.nav-tabs .nav-link:hover  {
    background-color: #747474 !important;
    color: white !important; 
}
.nav-link.active {
  background-color: #fff !important;
  color:#343a40 !important;
}

.nav-link:hover {
  background-color: #6c757d !important; 
}
   

</style>
    @include('offertimes.partials.add_offertimes_modal')
@endsection

@section('custom_scripts')
    @include('offertimes.partials.offertimes_js')
@endsection