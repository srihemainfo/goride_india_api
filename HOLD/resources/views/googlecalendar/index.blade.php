@extends('dashboard-layout.index')



@section('content')



<div class="col-sm-9 mx-4 main-card mb-2 card">

    <!--<div class="card-header">-->

    <!--    <h4 class="card-title">Employee List</h4>-->

    <!--    <div class="btn-actions-pane-right">-->

                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->

    <!--        <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add Employee </button>-->

    <!--    </div>-->

    <!--</div>-->

   <div class="x_panel">

<div class="x_title">

<h2 class="ms-3 mt-3">Google Calendar</h2>

</div>

<div class="x_content"><grammarly-extension data-grammarly-shadow-root="true" style="position: absolute; top: 0px; left: 0px; pointer-events: none;" class="dnXmp"></grammarly-extension><grammarly-extension data-grammarly-shadow-root="true" style="position: absolute; top: 0px; left: 0px; pointer-events: none;" class="dnXmp"></grammarly-extension>

<div class="x_content-container2">

<form id="add_employeeForm" class="form-horizontal form-label-left" method="post" data-parsley-validate="" novalidate="">

<div class="row mb-3 mt-3 ms-3 me-3">

<label class="form-label col-md-2 col-sm-6">Google Calendar</label>

<div class="col-md-10 col-sm-6">

<select name="googlecallender_check" id="googlecallender_check" class="form-select">

<option value="Yes">Yes</option>

<option value="No" selected="">No</option>

</select>

</div>

</div>

<div class="row mb-3  mt-3 ms-3 me-3">

<label class="form-label col-md-2 col-sm-6">Google Calendar ID</label>

<div class="col-md-10 col-sm-6">

<input type="email" id="googlecallender_id" name="googlecallender_id"  class="form-control required" maxlength="100" data-parsley-maxlength="100">

<input type="hidden" name="google_data_id" id="google_data_id">

</div>

</div>

<div class="row mb-3  mt-3 ms-3 me-3">

<label class="form-label col-md-2 col-sm-6">Google Calendar JSON </label>

<div class="col-md-10 col-sm-6">

<textarea name="googlecallender_json" id="googlecallender_json" class="form-control required" rows="17" spellcheck="false" data-gramm="false" wt-ignore-input="true"></textarea>

</div>

</div>

<div class="text-center mb-5 mt-2">

<button type="button" name="sbtUpdate" class="btn btn-primary" id="add_saveBtn">UPDATE</button>

</div>

</form>

</div>

</div>

</div>

</div>

 <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

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

    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-star" style="margin-right: 8px;"></i> Review

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

    @include('googlecalendar.partials.employees_js')

@endsection

