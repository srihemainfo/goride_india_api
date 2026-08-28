@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-9 mx-4 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Car Fares List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                         <th>#</th>
                        <th>Range</th>
                        <th>Sedan</th>
                        <th>Minivan</th>
                        <th>7 Seater Van</th>
                        <th>8 Seater Van</th>
                        <th>Executive Sedan</th>
                        <th>Executive Minivan</th>
                        <th>Shared Ride</th>
                        <th>16 Seater Bus</th>
                        <th>22 Seater Bus</th>
                        <th>32 Seater Bus</th>
                        <th>44 Seater Bus</th>
                        <th>55 Seater Bus</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list_car_fares as $car_fare)
                        <tr>
                            <form id="car_fare_{{ $car_fare->id }}">
                                <td>{{ $car_fare->start .' - '. $car_fare->end }}</td>
                                <td><input style="width:60px;text-align:right;" type="number" name="saloon" value="{{ $car_fare->sedan }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="estate" value="{{ $car_fare->minivan }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv" value="{{ $car_fare->seater7 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv5" value="{{ $car_fare->seater8 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="executive" value="{{ $car_fare->executive }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv_executive" value="{{ $car_fare->mpv_executive }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="sharedride" value="{{ $car_fare->sharedride }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv6" value="{{ $car_fare->mpv6 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv8" value="{{ $car_fare->mpv8 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="seater32" value="{{ $car_fare->seater32 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="seater44" value="{{ $car_fare->seater44 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="seater55" value="{{ $car_fare->seater55 }}"></td>
                                
                                
                                
                                @if($IS_UPDATABLE)
                                <td><input type="hidden" name="fare_id" value="{{ $car_fare->id }}"><button class="btn btn-success updateFare" data-id="{{ $car_fare->id }}"><i class="fa fa-check"></i> &nbsp; update</button></td>
                                @endif
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-danger">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position" >
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
    <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>fare
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/location" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
     <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Zones
    </a>
    
    
    <a class="nav-link  text-light" id="vert-tabs-right-promo-code-tab" href="/area" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-globe" style="margin-right: 8px;"></i> Area
    </a>
    
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

@endsection

@section('custom_scripts')
    @include('carfares.partials.carfares_js')
@endsection