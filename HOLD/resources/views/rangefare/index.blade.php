@extends('dashboard-layout.index')

@section('content')

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Range Fare List</h4>
        <div class="btn-actions-pane-right">
                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->
            <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add Range Fare </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="emp-table1" class="table" width="100%">
                <thead class="table-light">
                  <tr>
                        <th>#</th>
                        <th>Vehicle Name</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Fare</th>
                        <th>Vehicle</th>
                        <th>Vehicle Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
              </table>
        </div>
    </div>
</div>

    @include('rangefare.partials.add_rangefare_modal')
    @include('rangefare.partials.edit_rangefare_modal')
    @include('rangefare.partials.password_change_modal')
@endsection

@section('custom_scripts')
    @include('rangefare.partials.rangefare_js')
@endsection
