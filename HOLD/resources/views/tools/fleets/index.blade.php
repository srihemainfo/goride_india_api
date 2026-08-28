@extends('dashboard-layout.index')

@section('content')

@include('tools.fleets.partials.filter')

<div class="col-sm-12 main-card mb-3 card">

    <div class="card-header">
        <h4 class="card-title">Fleet List</h4>
        <div class="col-12">
      <button type="button" class="btn btn-primary footable-add float-end" data-bs-toggle="modal" data-bs-target="#add-modal">Add New</button> 
   </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Passengers</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Luggage</th>
                        <th>Hand Luggage</th>
                        <th>Booster</th>
                        <th>Child Seats</th>
                        <th>Status</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    
    
    
    <!--Modal Add New-->

<div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="editor-title" aria-modal="true" role="dialog">
   <style scoped="">
      .mb-3.required .control-label:after { content: "*"; color: red; margin-left: 4px }
   </style>
   <div class="modal-dialog">
      <form class="modal-content form-horizontal" data-parsley-validate="" id="fleetForm" novalidate="">
         <input type="hidden" id="txtVehicleID" name="txtVehicleID" value="">
         <div class="modal-header">
            <h5 class="modal-title" id="editor-title">Add New</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="fleetForm" name="fleetForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Fleet Name<span class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter First Name">
                            <p class="text-danger invalid-fleet-name"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="passenger" class="col-form-label">Passengers<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="passenger" id="passenger" placeholder="Enter passenger count">
                            <p class="text-danger invalid-passenger"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="no_of_seats" class="col-form-label">Booster Seat<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="booster" id="booster" placeholder="Enter seats count">
                            <p class="text-danger invalid-no-seats"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="min" class="col-form-label">Min<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="min" id="min" placeholder="Enter min count">
                            <p class="text-danger invalid-min"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="max" class="col-form-label">Max<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="max" id="max" placeholder="Enter max count">
                            <p class="text-danger invalid-max"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="luggage" class="col-form-label">luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="luggage" id="luggage" placeholder="Enter luggage count">
                            <p class="text-danger invalid-luggage"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="hand_luggage" class="col-form-label">Hand Luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="hand_luggage" id="hand_luggage" placeholder="Enter hand luggage count">
                            <p class="text-danger invalid-hand-luggage"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="child" class="col-form-label">Child Seats<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="child" id="child" placeholder="Enter child seats count">
                            <p class="text-danger invalid-child"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="order" class="col-form-label">Order</label>
                            <input type="number" class="form-control" name="order" id="order" placeholder="Enter order" value="0">
                            <p class="text-danger invalid-order"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>

                <!--<input type="hidden" name="fleet_id" id="fleet_id">-->
             
        </div>
         <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="primaryBtn">Add</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
         </div>
      </form>
   </div>
</div>





<!--Modal Edit New-->

<div class="modal fade" id="editor-modal" tabindex="-1" aria-labelledby="editor-title" aria-modal="true" role="dialog">
   <style scoped="">
      .mb-3.required .control-label:after { content: "*"; color: red; margin-left: 4px }
   </style>
   <div class="modal-dialog">
      <form class="modal-content form-horizontal" data-parsley-validate="" id="EditVehicleForm" novalidate="">
         <input type="hidden" id="txtVehicleID" name="txtVehicleID" value="">
         <div class="modal-header">
            <h5 class="modal-title" id="editor-title">Edit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
        <div class="modal-body">
           
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Fleet Name<span class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="editname" id="editname" placeholder="Enter First Name">
                            <p class="text-danger invalid-fleet-name"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="passenger" class="col-form-label">Passengers<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editpassenger" id="editpassenger" placeholder="Enter passenger count">
                            <p class="text-danger invalid-passenger"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="no_of_seats" class="col-form-label">Booster Seat<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editbooster" id="editbooster" placeholder="Enter seats count">
                            <p class="text-danger invalid-no-seats"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="min" class="col-form-label">Min<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editmin" id="editmin" placeholder="Enter min count">
                            <p class="text-danger invalid-min"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="max" class="col-form-label">Max<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editmax" id="editmax" placeholder="Enter max count">
                            <p class="text-danger invalid-max"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="luggage" class="col-form-label">luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editluggage" id="editluggage" placeholder="Enter luggage count">
                            <p class="text-danger invalid-luggage"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="hand_luggage" class="col-form-label">Hand Luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editthand_luggage" id="editthand_luggage" placeholder="Enter hand luggage count">
                            <p class="text-danger invalid-hand-luggage"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="child" class="col-form-label">Child Seats<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="editchild" id="editchild" placeholder="Enter child seats count">
                            <p class="text-danger invalid-child"></p>
                             <input type="hidden" id="editVehicleId" name="id">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="order" class="col-form-label">Order</label>
                            <input type="number" class="form-control" name="editorder" id="editorder" placeholder="Enter order" value="0">
                            <p class="text-danger invalid-order"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    
                </div>

                
              
        </div>
         
         <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="UpdateprimaryBtn">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
         </div>
      </form>
   </div>
</div>
</div>

    @include('tools.fleets.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('tools.fleets.partials.fixed_price_js')
@endsection