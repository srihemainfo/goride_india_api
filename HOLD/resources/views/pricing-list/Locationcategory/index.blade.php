@extends('dashboard-layout.index')

@section('content')

@include('pricing-list.Locationcategory.partials.filter')
<div class="right_col col-sm-10" role="main"> <div class="x_panel">
<div class="x_title"><h2>Location Category Pricing</h2></div>
<div class="x_content">
<div class="x_content-container">
<form id="formPricing" class="form-horizontal form-label-left" method="post" action="" data-parsley-validate>
<h5>Pick Up</h5>
<div class="row mb-3">
<label class="col-form-label col-md-4 col-sm-4 col-xs-12">Airport</label>
<div class="col-md-4 col-sm-4 col-xs-12">
<div class="input-group">
<input type="text" name="categoryPrice1" id="airportpickup" placeholder value="10" class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="0.01" min="0">

</div>
</div>
</div>
<hr>
<h5>Meet & Greet</h5>
<div class="row mb-3">
<label class="col-form-label col-md-4 col-sm-4 col-xs-12">Airport</label>
<div class="col-md-4 col-sm-4 col-xs-12">
<div class="input-group">
<input type="text" name="categoryPriceMeetGreet1" id="meetpickup"  class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="0.01" min="0">

</div>
</div>
</div>
<div class="text-center">
<button type="submit" name="sbtUpdate" id="update" class="btn btn-primary">UPDATE</button>
</div>
</form>
</div>
</div>
</div>
</div>
<div class="col-sm-2 main-card mb-3 card" style="background-color: #343a40;">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/generalpricing" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
    <!--</a>-->
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/vehiclepricing" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-rupee-sign" style="margin-right: 8px;"></i> Vehicle Pricing
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/distanceslab" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Distance Slab
    </a>
    
    <a class="nav-link   text-light" id="vert-tabs-right-promo-code-tab" href="/hourlypackage" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-clock" style="margin-right: 8px;"></i> Hourly Package
    </a>
    
    <a class="nav-link active  text-light" id="vert-tabs-right-notification-tab" href="/locationcategory " role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-list" style="margin-right: 8px;"></i> Location Category
    </a>
     <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/FixedPrice" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i> fixed Pricing
    </a>
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

    @include('pricing-list.Locationcategory.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('pricing-list.Locationcategory.partials.fixed_price_js')
@endsection