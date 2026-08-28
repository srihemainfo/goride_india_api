  @extends('dashboard-layout.index')



@section('content')
<style>
  .x_panel{
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, .15);

}
</style>

<div class="col-sm-9 mx-4">

<div class="x_panel">

<div class="x_title">

<h2>Review Settings</h2>

</div>

<div class="x_content">

<form id="add_employeeForm" class="form-horizontal form-label-left" method="post"  data-parsley-validate="" enctype="multipart/form-data" novalidate="">

<div class="row mb-3">

<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Review request automatically</label>

<div class="col-md-7 col-sm-7 col-xs-12 mb-3">

<select name="review_send_setting" id="review_send_setting" class="form-select">

<option value="No">No</option>

<option value="Yes">Yes</option>

</select>

</div>

</div>

<div class="row mb-3">

<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Review request after Dropoff time</label>

<div class="col-md-7 col-sm-7 col-xs-12 mb-3">

<select name="review_send_after_pickup_time"  id="review_send_after_pickup_time" class="form-select">

<option value="15" selected>15 Minutes </option>

<option value="30">30 Minutes </option>

<option value="60">1 Hour</option>
<option value="120">2 Hours</option>
<option value="240">4 Hours</option>
<option value="360">6 Hours</option>
<option value="480">8 Hours</option>
<option value="600">10 Hours</option>
<option value="720">12 Hours</option>
<option value="840">14 Hours</option>
<option value="960">16 Hours</option>
<option value="1080">18 Hours</option>
<option value="1200">20 Hours</option>
<option value="1320">22 Hours</option>
<option value="1440">24 Hours</option>

</select>

</div>

</div>

<!-- <div class="row mb-3">

<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Review subject</label>

<div class="col-md-7 col-sm-7 col-xs-12 mb-3">

<input type="text" name="review_subject" id="review_subject" class="form-control" required>


</div>

</div> -->
<input type="hidden" name="review_id" id="review_id" >

<div class="row mb-3">

<label class="col-form-label col-md-5 col-sm-5 col-xs-12">Review template</label>

<div class="col-md-7 col-sm-7 col-xs-12 mb-3">

<textarea name="review_request_template" id="review_request_template" class="form-control" required="" style="width: 100%; height: 400px;">

    

</textarea>

</div>

</div>

<div class="text-center">

<button type="button" name="sbtUpdate" id="add_saveBtn" class="btn btn-primary">UPDATE</button>

</div>

</form>

</div>

</div>

</div>

 <div class="col-sm-2 main-card mb-3 card  d-none d-lg-block position">

  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

    <!--</a>-->

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

    </a>

    

    <a class="nav-link  text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">    

      <i class="fas fa-plus"style="margin-right: 8px;"></i> Email Template   

      </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

    </a>

    <!-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date 

    </a> -->

    {{-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a> --}}

    <a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/whatsapp-configuration" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;" contenteditable="false">

      <i class="fab fa-whatsapp" style="margin-right: 8px;"></i>WhatsApp Config

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/call-configuration" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-phone-volume" style="margin-right: 8px;"></i> Call/Sms Config

    </a>

    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-star" style="margin-right: 8px;"></i> Review

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/reviewdetails" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i> Review Details

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

@endsection



@section('custom_scripts')

    @include('review.partials.employees_js')

@endsection

