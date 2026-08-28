@php
    // dd("Bismillah");
@endphp

@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Place List</h4>
        <div class="btn-actions-pane-right">
            @if($IS_CREATABLE)
                <button type="button" class="btn btn-success" id="addPlace"><i class="fas fa-plus"></i> Add Place</button>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>ID</th>
                        <th>Place Name</th>
                        <th style="width:8%;">Discount</th>
                        <th style="width:8%;">Type</th>
                        <th style="width:13%;">Status</th>
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
    @include('places.partials.add_place_modal')
@endsection

@section('custom_scripts')
    @include('places.partials.place_js')
@endsection