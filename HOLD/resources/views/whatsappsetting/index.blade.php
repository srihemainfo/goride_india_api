@extends('dashboard-layout.index')



@section('content')



{{-- <div class="col-sm-12 main-card mb-2 card">

    <div class="card-header">

        <h4 class="card-title">Map</h4>

    </div>

    <div class="card-body">

        <div id="map" style="height: 400px; width: 100%;">

        </div>

    </div>

</div> --}}

<div class="col-sm-9">

<div class="right_col" role="main"> <div class="x_panel">

    <div class="x_title d-flex justify-content-between align-items-center flex-wrap mb-3">

        <h2>WhatsApp Configuration</h2>

        <button class="btn btn-purchase" style="border: 3px solid #2A668F;" onclick="window.open('https://goconnect.goride.run/', '_blank')">
            <b>Get Config Key</b> <i class="fas fa-key icon"></i>
        </button>

    </div>
    

<div class="x_content">

<form id="formSettingsPayment" class="form-horizontal" method="post"  data-parsley-validate>

    <input type="hidden" name="whatsappsetting_id" id="whatsappsetting_id"  class="form-control" value="" readonly>

    <div class="card border border-secondary mb-3">

        <!--<div class="card-header bg-secondary text-white">-->

        <!--</div>-->

        <div class="card-body">

            <label class="form-label">Session ID</label>

            <input type="text" name="session_id" id="session_id" 
            class="form-control" maxlength="20" 
            data-parsley-maxlength="20"  value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">
     
        

            <label class="form-label mt-3">API Key</label>

            <input type="text" name="whats_key" id="whats_key" class="form-control" maxlength="50" data-parsley-maxlength="50" value=""
            oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')">

            <button type="button" name="saveBtn" id="saveBtn" class="btn btn-primary mt-3">Update</button>

        </div>

    </div>


</form>

<!--<div class="card border border-secondary test-div">-->

    <div class="card-header row test-div mt-4">

        <div class="col-12 mb-2">
            Test Message
        </div>
    
        <div class="row g-3">
            
            <div class="col-md-5 col-12">
                <input type="text" name="test_message" id="test_message" class="form-control"
                    oninput="this.value = this.value.slice(0, 50);" 
                    placeholder="Send Test Message" maxlength="50" data-parsley-maxlength="50" value="">
            </div>
        </div>
    
        <div class="mt-3">
            <button type="button" name="testMessBtn" id="testMessBtn" class="btn btn-primary">Send Message</button>
        </div>
    
    </div>
<!--</div>-->

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

    

    <a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

    </a>


    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/whatsapp-configuration" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

        <i class="fab fa-whatsapp" style="margin-right: 8px;"></i>WhatsApp Config
  
      </a>

    <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

    <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->

    <!--</a>-->

    {{-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a> --}}

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
.btn-purchase {
            color: black;
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            width: 150px;
            font-size: large;
            border-radius: 50px;
            font-size: 13px;
        }
        .btn-purchase .icon {
            position: absolute;
            right: -210px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.2s ease;
        }
        .btn-purchase:hover {
            padding-right: 40px;
            background: linear-gradient(310deg, #2980b9, #2c3e50);
            color: white;
        }
        .btn-purchase:hover .icon {
            right: 18px;
        } 
        @media (max-width:776px) {
            .x_title h2{
                font-size: 25px;
            }
            .btn-purchase{
                margin-left: 128px;
                margin-top: 10px;
            }
            
        }
    
</style>

@endsection



@section('custom_scripts')

    @include('whatsappsetting.partials.customers_js')

@endsection

