 @extends('dashboard-layout.index')

@section('content')

@include('pricing-list.Hourly_package.partials.filter')

<div class="col-sm-10 main-card mb-2 card">
    <div class="card-header  row container">
         <div class="col-10">
       <h4 class="card-title  mb-0  mt-1 p-0">Hourly Package</h4>
       </div>
       <div class="col-2">
      <button type="button" class="btn btn-success footable-add float-end" data-bs-toggle="modal" data-bs-target="#add-modal"><i class="me-2 fas fa-plus"></i>Add New</button> 
   </div>
    </div>

<div class="x_content">
<div class="row justify-content-between">
    
   <div class="col-12">
        <div class="card-body">
       <div class="table-responsive">
       <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                       <th>#</th>
                        <th>Distance</th>
                        <th>Hours</th>
                        <th>Saloon</th>
                        <th>Executive</th>
                        <th>MPV</th>
                        <th>Seater</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
          </table>
   </div>
   </div>
   </div>
</div>
</div>
</div>

<!--desktop tab-->
<div class="col-sm-2 main-card mb-3 card " style="background-color: #343a40;">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/generalpricing" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/vehiclepricing" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-rupee-sign" style="margin-right: 8px;"></i> Vehicle Pricing
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/distanceslab" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Distance Slab
    </a>
    
    <a class="nav-link active  text-light" id="vert-tabs-right-promo-code-tab" href="/hourlypackage" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-clock" style="margin-right: 8px;"></i> Hourly Package
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/locationcategory " role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-list" style="margin-right: 8px;"></i> Location Category
    </a>
     <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/FixedPrice" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i> fixed Pricing
    </a>
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
<!-- Add Price Modal -->
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
                    <div class="form-group">
                        <label for="Distance">Distance</label>
                        <input type="number" class="form-control" name="Distance" id="distancePackage" required>
                    </div>
                    <div class="form-group">
                        <label for="Hours">Hours</label>
                        <input type="number" class="form-control" name="Hours" id="hoursPackage" required>
                    </div>
                    <div class="form-group">
                        <label for="Saloon">Saloon</label>
                        <input type="number" class="form-control" name="Saloon" id="vehicle1" required>
                    </div>
                    <div class="form-group">
                        <label for="Executive">Executive</label>
                        <input type="number" class="form-control" name="Executive" id="vehicle2" required>
                    </div>
                    <div class="form-group">
                        <label for="MPV">MPV</label>
                        <input type="number" class="form-control" name="MPV" id="vehicle3" required>
                    </div>
                    <div class="form-group">
                        <label for="Seater">8 Seater</label>
                        <input type="number" class="form-control" name="Seater" id="vehicle4" required>
                    </div>
                    <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-save"></i>&nbsp; Save</button>
        </div>
        </form>
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
                    <div class="form-group">
                        <label for="Distance">Distance</label>
                        <input type="number" class="form-control" name="editDistance" id="editdistancePackage" required>
                    </div>
                    <div class="form-group">
                        <label for="Hours">Hours</label>
                        <input type="number" class="form-control" name="editHours" id="edithoursPackage" required>
                    </div>
                    <div class="form-group">
                        <label for="Saloon">Saloon</label>
                        <input type="number" class="form-control" name="editSaloon" id="editvehicle1" required>
                    </div>
                    <div class="form-group">
                        <label for="Executive">Executive</label>
                        <input type="number" class="form-control" name="editExecutive" id="editvehicle2" required>
                    </div>
                    <div class="form-group">
                        <label for="MPV">MPV</label>
                        <input type="number" class="form-control" name="editMPV" id="editvehicle3" required>
                    </div>
                    <div class="form-group">
                        <label for="Seater">8 Seater</label>
                        <input type="number" class="form-control" name="editSeater" id="editvehicle4" required>
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
    @include('pricing-list.Hourly_package.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('pricing-list.Hourly_package.partials.fixed_price_js')
@endsection