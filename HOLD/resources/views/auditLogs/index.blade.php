@extends('dashboard-layout.index')

@section('content')

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Audit Logs List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                  <tr>
                        <th style="width:5%;">#</th>
                        <th>ID</th>
                        <th>Description</th>
                        <th>Subject_id</th>
                        <th>Subject Type</th>
                        <th>user id</th>
                        <th>Host</th>
                        <th>Properties</th>
                        <th>created_at</th>
                    </tr>
                </thead>
                
              </table>
        </div>
    </div>
    
</div>

   
@endsection

@section('custom_scripts')
    @include('auditLogs.audit_logs_js')
@endsection
