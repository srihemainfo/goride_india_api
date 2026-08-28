@extends('dashboard-layout.index')

@section('content')
<!--<div class="col-sm-9 mx-4 ">-->
@include('bookingrestriction.partials.filter')

<div class="col-sm-9 mx-4 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Restriction List</h4>
        <div class="btn-actions-pane-right">
                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->
            <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add New </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="emp-table" class="table" width="100%">
                <thead class="table-light">
                  <tr>
                        <th>#</th>
                        <th>Caption</th>
                        <th>Recurring</th>
                        <th>From</th>
                        <th>To</th>
                        <!--<th>Status</th>-->
                        <th>Action</th>
                    </tr>
                </thead>
              </table>
        </div>
    </div>
</div>

    @include('bookingrestriction.partials.add_employee_modal')
    @include('bookingrestriction.partials.edit_employee_modal')
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
    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date 
    </a>
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
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
    @include('bookingrestriction.partials.employees_js')
@endsection
