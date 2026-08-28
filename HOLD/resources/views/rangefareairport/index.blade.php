@extends('dashboard-layout.index')

@section('content')

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Range Fare Airport List</h4>
        <div class="btn-actions-pane-right">
                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->
            <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add Range Fare Airport</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="emp-table" class="table" width="100%">
                <thead class="table-light">
                  <tr>
                        <th>#</th>
                        <th>Vehicle Name</th>
                        <th>From Airport</th>
                        <th>To Airport</th>
                        <th>By Hour</th>
                        <th>Hour Fare</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Fare</th>
                        <!--<th>Vehicle</th>-->
                        <th>Vehicle Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
              </table>
        </div>
    </div>
</div>

    @include('rangefareairport.partials.add_rangefareairport_modal')
    @include('rangefareairport.partials.edit_rangefareairport_modal')
    @include('rangefareairport.partials.password_change_modal')
@endsection

@section('custom_scripts')
    @include('rangefareairport.partials.rangefareairport_js')
@endsection
