@extends('dashboard-layout.index')

@section('content')
@include('locationrange.partials.breadcrumb')
<div class="col-sm-8 main-card card mx-auto">
    <div class="card-header">
        <h4 class="card-title">Draw borders for zone</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="zone_name">Zone Name</label>
                    <div class="input-group">
                        <select class="form-control select2" style="width: 100%;" tabindex="-1" id="zone_name" name="zone_name"  data-placeholder="Search Zones">

                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="map" style="display:none; height: 300px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_scripts')
    @include('locationrange.partials.draw_map_js')
@endsection
