@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-9 mx-4 main-card mb-3 card">
      <div class="card-header row container">
         <div class="col-9">
       <h4 class="card-title  mb-0  mt-1 p-0">Promo Code List</h4>
       </div>
       <div class="col-3">
      <button type="button" class="btn btn-success footable-add float-end" data-bs-toggle="modal" data-bs-target="#add-modal"><i class="me-2 fas fa-plus"></i>Add New</button> 
   </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                       <th>#</th>
                        <th>Code</th>
                        <th>Min Value</th>
                        <th>Max Value</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    </div>
    <div class="col-sm-2 main-card mb-3 card position">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/fleet" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">
      <i class="fa-solid fa-car" style="margin-right: 8px;"></i> List Fleets
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/offertimes" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Offer Times
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/offerdays" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i> Offer Days
    </a>
    
    <a class="nav-link active text-light" id="vert-tabs-right-promo-code-tab" href="/promocode" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
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
    
    <!--Modal Add New-->

<div id="add-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
         <div class="modal-body">
              <form id="VehicleForm">
            <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <label for="code" class="col-form-label">Code<span class="required">&nbsp;*</span></label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code">
                        <p class="text-danger invalid-code"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="min_value" class="col-form-label">Min Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="min_value" id="min_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-minvalue"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="max_value" class="col-form-label">Max Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="max_value" id="max_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-maxvalue"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="from_date" id="from_date" placeholder="Enter From Date">
                            <p class="text-danger invalid-fromdate"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="to_date" id="to_date" placeholder="Enter To Date">
                            <p class="text-danger invalid-todate"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type" class="col-form-label">Type<span class="required">&nbsp;*</span></label>
                            <select class="form-control" id="type" name="type">
                                 <option value="Flat">Select Type</option>
                                <option value="Flat">Flat</option>
                                <option value="Percent">Percent</option>
                            </select>
                            <p class="text-danger invalid-type"></p>
                        </div>
                   </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="value" class="col-form-label">Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="values" id="values" min="0" placeholder="0.00">
                            <p class="text-danger invalid-value"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
        </form>
                </div>
         </div>
    </div> <!-- modal-bialog .// -->
  </div> <!-- modal.// -->

</div>

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
                        <label for="code" class="col-form-label">Code<span class="required">&nbsp;*</span></label>
                        <input type="text" class="form-control" name="editcode" id="editcode" placeholder="Enter Code">
                        <p class="text-danger invalid-code"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="min_value" class="col-form-label">Min Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editmin_value" id="editmin_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-minvalue"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="max_value" class="col-form-label">Max Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editmax_value" id="editmax_value" min="0" placeholder="0.00">
                            <p class="text-danger invalid-maxvalue"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="from_date" class="col-form-label">From Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="editfrom_date" id="editfrom_date" placeholder="Enter From Date">
                            <p class="text-danger invalid-fromdate"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to_date" class="col-form-label">To Date<span class="required">&nbsp;*</span></label>
                            <input type="date" class="form-control" name="editto_date" id="editto_date" placeholder="Enter To Date">
                            <p class="text-danger invalid-todate"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type" class="col-form-label">Type<span class="required">&nbsp;*</span></label>
                            <select class="form-control" id="edittype" name="edittype">
                                 <option value="Flat">Select Type</option>
                                <option value="Flat">Flat</option>
                                <option value="Percent">Percent</option>
                            </select>
                            <p class="text-danger invalid-type"></p>
                        </div>
                   </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="value" class="col-form-label">Value<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editvalues" id="editvalues" min="0" placeholder="0.00">
                            <p class="text-danger invalid-value"></p>
                        </div>
                         <input type="hidden" id="editVehicleId" name="id">
                    </div>
                    <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="UpdateprimaryBtn"><i class="fa fa-save"></i>&nbsp; Update</button>
          </div>
        </form>
                </div>
    </div>
  </div>
</div>

    @include('promocode.partials.add_promocode_modal')
@endsection

@section('custom_scripts')
    @include('promocode.partials.promocode_js')
@endsection