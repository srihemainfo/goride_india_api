
@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 alert alert-danger">
    <strong>Note: </strong> Update permissions on each row individually, removing 'READ' permission will remove all other permissions in the module.
</div>
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Employee Module Permissions</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>ID</th>
                        <th>Module Name</th>
                        <th>READ</th>
                        <th>CREATE</th>
                        <th>UPDATE</th>
                        <th>DELETE</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('offerdays.module_permission.partials.module_permission_js')
@endsection
