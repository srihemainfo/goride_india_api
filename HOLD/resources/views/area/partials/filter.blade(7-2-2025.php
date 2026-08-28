<div class="col-sm-9 mx-4 main-card mb-3 card">

    <div class="card-header">
        <h4 class="card-title">Area Filter</h4>
    </div>
    <div class="card-body">
        <div class="row">   
        <form id="cus_filter_form" name="cus_filter_form">
            <div class="col-sm-12 row mb-2">
                <!--<div class="col-sm-3">-->
                <!--    <label for="place_filter">Place</label>-->
                <!--    <select class="form-control" id="place_filter" name="place_filter">-->
                <!--            <option value="">-- select place --</option>-->
                <!--        @foreach ($list_places as $place)-->
                <!--            <option value="{{ $place->id }}">{{ $place->place }}</option>-->
                <!--        @endforeach-->
                <!--    </select>-->
                                        
                <!--    <input type="hidden" name="selected_place" id="selected_place" value="">-->
                <!--</div>-->

                <div class="col-sm-3">
                    <label for="area_name_filter">Area</label>
                    <input type="text" class="form-control" id="area_name_filter" placeholder="Search by area name" name="area_name_filter">
                </div>
            </div>

            <div class="col-sm-12 row mb-3">
                <div class="col-sm-3">
                    <button type="button" class="btn btn-primary" id="search"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
            </form>
        </div>
    </div>

</div>
<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position" >
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
    <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
     <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Zones
    </a>
     <a class="nav-link active text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
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