@extends('dashboard-layout.index')

@section('content')
<!--<div class="col-sm-10">-->
@include('offerdays.locationrange.partials.filter')

{{-- <div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Map</h4>
    </div>
    <div class="card-body">
        <div id="map" style="height: 400px; width: 100%;">
        </div>
    </div>
</div> --}}

<div class="col-sm-9 mx-4 main-card mb-2 card">
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
                        <!--<th style="width:7%;">ID</th>-->
                        <th style="width:10%;">Zone Name</th>
                        <!--<th style="width:12%;">Type</th>-->
                        <th style="width:12%;">Pickup</th>
                        <th style="width:12%;">DropOff</th>
                        <!--<th style="width:10%;">Passing</th>-->
                       <!--<th style="width:10%;">Status</th>-->
                        <th style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
</div>
 <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
    <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare
    </a>
    
    <a class="nav-link active text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
     <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Zones
    </a>
     <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
   <i class="fas fa-vector-square" style="margin-right: 8px;"></i> Area
    </a>
    
    <!--<a class="nav-link active text-light" id="vert-tabs-right-promo-code-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
    <!--  <i class="fas fa-globe" style="margin-right: 8px;"></i> Zone-->
    <!--</a>-->
    
    <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
    <!--  <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options-->
    <!--</a>-->
    <!--<a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
    <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->
    <!--</a>-->
    <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
    <!--  <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar-->
    <!--</a>-->
    <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
    <!--  <i class="fas fa-star" style="margin-right: 8px;"></i> Review-->
    <!--</a>-->
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
    @include('offerdays.locationrange.partials.add_locationrange_modal')
@endsection

@section('custom_scripts')
    @include('offerdays.locationrange.partials.locationrange_js')
@endsection
