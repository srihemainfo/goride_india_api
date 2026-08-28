@extends('dashboard-layout.index')
@section('content')

<div class="col-sm-12 main-card mb-3 card">

    <div class="card-header">
        <h4 class="card-title">Role Create</h4>
    </div>
    <div class="card-body">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-3 ">
                <label for="role_name">Customer Name</label>
                <input type="text" class="form-control" id="role_name" placeholder="Enter Role Name" name="role_name" value="">
            </div>
            <div class="col-sm-2 d-flex justify-content-start">
                <button type="button" class="btn btn-success" id="Role_create">ADD ROLE</button>
            </div>
        </div>

  </div>
    </div>

</div>



<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Roles</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>ID</th>
                        <th>Title</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('role.partials.edit_role')




@endsection

@section('custom_scripts')
    @include('role.partials.role_js')
@endsection