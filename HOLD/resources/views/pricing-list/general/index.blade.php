@extends('dashboard-layout.index')

@section('content')

@include('pricing-list.general.partials.filter')

<div class="col-sm-10 main-card mb-2 card">
    <div class="card-header mt-3">
        <h4 class="card-title">General Pricing</h4>
       
    </div>
   <form id="formPricingGeneral" class="form-horizontal form-label-left mb-5" method="post" action="" >

<div class="row mb-3 me-2 ms-2">
<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Price Decimals</label>
<div class="col-md-7 col-sm-7 col-xs-12 mt-2">
<input type="text" id="priceDecimal" name="priceDecimal" placeholder="" value="" class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="1" min="0" max="2">
</div>
</div>
<div class="row mb-3 me-2 ms-2">
<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Minimum price for additional drop off</label>
<div class="col-md-7 col-sm-7 col-xs-12">
<div class="input-group">
<input type="text" id="DropOffMinimumPrice" name="DropOffMinimumPrice" placeholder="" value="" class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="1" min="0">

</div>
</div>
</div>
<div class="row mb-3 me-2 ms-2">
<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Child seat price</label>
<div class="col-md-7 col-sm-7 col-xs-12">
<div class="input-group">
<input type="text" id="ChildSeatPrice" name="ChildSeatPrice" placeholder="" value="" class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="0.01" min="0" >

</div>
</div>
</div>
<div class="row mb-3 me-2 ms-2">
<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Card payment price type</label>
<div class="col-md-7 col-sm-7 col-xs-12">
<select id="selCardPaymentPriceType" name="selCardPaymentPriceType" class="form-select" required="">
<option value="amount">Amount</option>
<option value="percentage">Percentage</option>
</select>
</div>
</div>
<div class="row mb-3 me-2 ms-2">
<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Card payment Amount / Percentage</label>
<div class="col-md-7 col-sm-7 col-xs-12">
<div class="input-group">
<input type="text" id="CardPaymentPrice" name="CardPaymentPrice" placeholder="" value="" class="form-control" autocomplete="off" required="required" data-parsley-type="number" step="0.01" min="0">
 <input type="hidden" id="pricingid" name="pricingid">

</div>
</div>
</div>
<div class="text-center">
<button type="button" name="" id="saveBtn" class="btn btn-primary mt-5">UPDATE</button>
</div>
</form>
</div>
<div class="col-sm-2 main-card mb-3 card" style="background-color: #343a40;">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <a class="nav-link active text-light" id="vert-tabs-right-home-tab" href="/generalpricing" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">
      <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/vehiclepricing" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-rupee-sign" style="margin-right: 8px;"></i> Vehicle Pricing
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/distanceslab" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Distance Slab
    </a>
    
    <a class="nav-link   text-light" id="vert-tabs-right-promo-code-tab" href="/hourlypackage" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-clock" style="margin-right: 8px;"></i> Hourly Package
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/locationcategory " role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
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

    @include('pricing-list.general.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('pricing-list.general.partials.fixed_price_js')
@endsection