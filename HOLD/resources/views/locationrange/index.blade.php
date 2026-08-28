@extends('dashboard-layout.index')



@section('content')



@include('locationrange.partials.filter')



{{-- <div class="col-sm-12 main-card mb-2 card">

    <div class="card-header">

        <h4 class="card-title">Map</h4>

    </div>

    <div class="card-body">

        <div id="map" style="height: 400px; width: 100%;">

        </div>

    </div>

</div> --}}
<style>
    @media screen and (min-width:320px) and(max-width:776px){
        .btn-success{
        margin-top: 10px;
    }
    }
</style>
<div class="col-sm-10">

<div class="col-sm-12 main-card mb-2 card">

    <div class="card-header">

        <h4 class="card-title">Zone List</h4>

        <div class="btn-actions-pane-right">

            @if($IS_UPDATABLE)

                <a href="{{ route('locationrange.create') }}" class="btn btn-primary"><i class="fas fa-map"></i> Draw Zone</a>

            @endif

            @if($IS_CREATABLE)

                <button type="button" class="btn btn-success" id="addLocationrange"><i class="fas fa-plus"></i> Add Location Range </button>

            @endif

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="data-table" class="table" width="100%">

                <thead class="table-light">

                    <tr>

                        <th style="width:7%;">#</th>

                        <th style="width:7%;">ID</th>

                        <th style="width:10%;">Zone Name</th>

                        <th style="width:12%;">Type</th>

                        <th style="width:12%;">Pickup</th>

                        <th style="width:12%;">Dropoff</th>

                        <th style="width:10%;">Passing</th>

                        <th style="width:10%;">Status</th>

                        <th style="width:10%;">Action</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>

</div>

</div>

           

    @include('locationrange.partials.add_locationrange_modal')

@endsection



@section('custom_scripts')

    @include('locationrange.partials.locationrange_js')

@endsection

