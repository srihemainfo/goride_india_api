@extends('dashboard-layout.index')

@section('content')

@include('pricing-list.vehicle.partials.filter')

<style>
    .table-bordered th, .table-bordered td {
        border: 1px solid #b8b8b8;
    }
    .table-light th, .table-light td, .table-light thead th, .table-light tbody+tbody {
        border-color: #b8b8b8;
    }
    .table-bordered thead th, .table-bordered thead td {
        border-bottom-width: 0px;
    }
</style>
<div class="col-sm-10 main-card  card">
 <div class="card-header  row container">
         <div class="col-10">
       <h4 class="card-title  mb-0  mt-1 p-0">Vehicle List</h4>
       </div>
       <div class="col-2">
      <button type="button" class="btn btn-success footable-add float-end" data-bs-toggle="modal" data-bs-target="#add-modal"><i class="me-2 fas fa-plus"></i>Add New</button> 
   </div>
    </div>

  
  
   <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                       
                         <th>#</th>
                        <th>Priority</th>
                        <th>Vehicle Name</th>
                        <th>Passengers</th>
                        <th>Small Luggage</th>
                        <th>Large Luggage</th>
                        <th>Child Seat</th>
                        <th>Price</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
 </div>
 <div class="col-sm-2 main-card mb-3 card " style="background-color: #343a40;">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/generalpricing" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/vehiclepricing" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-rupee-sign" style="margin-right: 8px;"></i> Vehicle Pricing
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/distanceslab" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Distance Slab
    </a>
    
    <a class="nav-link   text-light" id="vert-tabs-right-promo-code-tab" href="/hourlypackage" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-clock" style="margin-right: 8px;"></i> Hourly Package
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/locationcategory " role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-list" style="margin-right: 8px;"></i> Location Category
    </a>
     <a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/FixedPrice" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
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
            <div class="row mb-3">
               <label for="txtPriority" class="col-md-6 col-sm-12 col-form-label">Priority</label>
               <div class="col-md-6 col-sm-12">
                  <input type="text" id="Priority" name="txtPriority" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
               </div>
            </div>
            <div class="row mb-3">
               <label for="selectVehicle" class="col-md-6 col-sm-12 col-form-label">Vehicle Name</label>
               <div class="col-md-6 col-sm-12">
                     <select id="selectVehicle" name="selectVehicle" class="form-control" required>
                     <option value="">Select Vehicle</option>
                  </select>
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtVehicleImage" class="col-md-6 col-sm-12 col-form-label">Image</label>
               <div class="col-md-6 col-sm-12">
                  <div class="input-group">
                     <input type="file" id="VehicleImage" name="txtVehicleImage" class="form-control" required="">
                     <!--<button type="button" class="btn btn-outline-secondary galleryBtn"><i class="fa-solid fa-image"></i></button>-->
                  </div>
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtPassengerCapacity" class="col-md-6 col-sm-12 col-form-label">Passenger Capacity</label>
               <div class="col-md-6 col-sm-12">
                  <input type="text" id="PassengerCapacity" name="txtPassengerCapacity" class="form-control" autocomplete="off" required=""  data-parsley-type="digits">
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtSmallLuggageCapacity" class="col-md-6 col-sm-12 col-form-label">Small Luggage Capacity</label>
               <div class="col-md-6 col-sm-12">
                  <input type="text" id="SmallLuggageCapacity" name="txtSmallLuggageCapacity" class="form-control" autocomplete="off" required=""  data-parsley-type="digits">
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtLargeLuggageCapacity" class="col-md-6 col-sm-12 col-form-label">Large Luggage Capacity</label>
               <div class="col-md-6 col-sm-12">
                  <input type="text" id="LargeLuggageCapacity" name="txtLargeLuggageCapacity" class="form-control" autocomplete="off" required=""  data-parsley-type="digits">
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtChildSeatCapacity" class="col-md-6 col-sm-12 col-form-label">Child Seats Capacity</label>
               <div class="col-md-6 col-sm-12">
                  <input type="text" id="ChildSeatCapacity" name="txtChildSeatCapacity" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
               </div>
            </div>
            <div class="row mb-3">
               <label for="selPriceType" class="col-md-6 col-sm-12 col-form-label">Price Type</label>
               <div class="col-md-6 col-sm-12">
                  <select id="selPriceType" name="selPriceType" class="form-select" required="">
                     <option value="Amount" >Amount</option>
                     <option value="Percentage">Percentage</option>
                  </select>
               </div>
            </div>
            <div class="row mb-3">
               <label for="txtPrice" class="col-md-6 col-sm-12 col-form-label">Amount / Percentage</label>
               <div class="col-md-6 col-sm-12">
                  <input type="number" id="Price" name="txtPrice" class="form-control" autocomplete="off" min="0" step="0.01" required="" data-parsley-type="number">
               </div>
               <input type="hidden" id="editVehicleId" name="id">
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
    <div class="row mb-3">
        <label for="editPriority" class="col-md-6 col-sm-12 col-form-label">Priority</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editPriority" name="EditPriority" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
        </div>
    </div>
    <div class="row mb-3">
        <label for="editselectVehicle" class="col-md-6 col-sm-12 col-form-label">Vehicle Name</label>
        <div class="col-md-6 col-sm-12">
            <select id="editselectVehicle" name="EditselectVehicle" class="form-control" required>
                <option value="">Select Vehicle</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <label for="editVehicleImage" class="col-md-6 col-sm-12 col-form-label">Image</label>
        <div class="col-md-6 col-sm-12">
            <div class="input-group">
                <input type="file" id="editVehicleImage" name="editVehicleImage" class="form-control">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <label for="editPassengerCapacity" class="col-md-6 col-sm-12 col-form-label">Passenger Capacity</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editPassengerCapacity" name="editPassengerCapacity" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
        </div>
    </div>
    <div class="row mb-3">
        <label for="editSmallLuggageCapacity" class="col-md-6 col-sm-12 col-form-label">Small Luggage Capacity</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editSmallLuggageCapacity" name="editSmallLuggageCapacity" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
        </div>
    </div>
    <div class="row mb-3">
        <label for="editLargeLuggageCapacity" class="col-md-6 col-sm-12 col-form-label">Large Luggage Capacity</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editLargeLuggageCapacity" name="editLargeLuggageCapacity" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
        </div>
    </div>
    <div class="row mb-3">
        <label for="editChildSeatCapacity" class="col-md-6 col-sm-12 col-form-label">Child Seats Capacity</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editChildSeatCapacity" name="editChildSeatCapacity" class="form-control" autocomplete="off" required="" data-parsley-type="digits">
        </div>
    </div>
    <div class="row mb-3">
        <label for="editselPriceType" class="col-md-6 col-sm-12 col-form-label">Price Type</label>
        <div class="col-md-6 col-sm-12">
            <select id="editselPriceType" name="editselPriceType" class="form-select" required="">
                <option value="Amount">Amount</option>
                <option value="Percentage">Percentage</option>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <label for="editPrice" class="col-md-6 col-sm-12 col-form-label">Amount / Percentage</label>
        <div class="col-md-6 col-sm-12">
            <input type="number" id="editPrice" name="EditPrice" class="form-control" autocomplete="off" min="0" step="0.01" required="" data-parsley-type="number">
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
    @include('pricing-list.vehicle.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('pricing-list.vehicle.partials.fixed_price_js')
@endsection