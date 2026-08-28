@extends('dashboard-layout.index')



@section('content')



<!-- @include('customers.partials.filter') -->

<style>
.x_panel{
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, .15);

}
    .icheckbox_flat-blue,

.iradio_flat-blue {

  display: inline-block;

  *display: inline;

  vertical-align: middle;

  margin: 0;

  padding: 0;

  width: 20px;

  height: 20px;

  background: url(../images/blue.png) no-repeat;

  border: none;

  cursor: pointer;

}

.icheckbox_flat-blue {

  background-position: 0 0;

}

.icheckbox_flat-blue.checked {

  background-position: -22px 0;

}

.icheckbox_flat-blue.disabled {

  background-position: -44px 0;

  cursor: default;

}

.icheckbox_flat-blue.checked.disabled {

  background-position: -66px 0;

}

.iradio_flat-blue {

  background-position: -88px 0;

}

.iradio_flat-blue.checked {

  background-position: -110px 0;

}

.iradio_flat-blue.disabled {

  background-position: -132px 0;

  cursor: default;

}

.iradio_flat-blue.checked.disabled {

  background-position: -154px 0;

}
@media (min-width: 320px) and (max-width: 776px) {
    .x_title h2{
    font-size: 21px !important;
   }
   .x_panel{
    padding: 14px !important;
   }
   .form-check-label{
    font-size:  17px !important;
   }

}
</style>

<div class="col-md-9">

    <div class="x_panel">

        <div class="x_title">

            <h2>Email Settings</h2>

        </div>

        <div class="x_content">

            <div class="x_content-container">

                <form id="formSettingsEmail" class="form-horizontal form-label-left" method="post" data-parsley-validate novalidate enctype="multipart/form-data">

                    <div class="row mb-3 align-items-center">

                        <label for="mailer_type1" class="form-label col-md-5 col-sm-5">

                            Mailer

                        </label>

                        <div class="col-md-7 col-sm-7 d-flex align-items-center">

                            <div class="form-check me-3">

                                <input 

                                    onchange="checkEmailType(this)"

                                    type="radio" 

                                    name="mailer_type" 

                                    class="form-check-input flat" 

                                    id="mailer_type1" 

                                    value="GoRide">

                                <label class="form-check-label" for="mailer_type1">Go Ride</label>

                            </div>

                            <div class="form-check">

                                <input 

                                    onchange="checkEmailType(this)"

                                    type="radio" 

                                    name="mailer_type" 

                                    class="form-check-input flat" 

                                    id="mailer_type2" 

                                    value="SMTP">

                                <label class="form-check-label" for="mailer_type2">SMTP</label>

                            </div>

                        </div>

                    </div>

                    <div class="row mb-3 email-hide">

                        <label class="form-label col-md-5 col-sm-5">From Email</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="text" id="from_email" name="from_email" class="form-control" maxlength="100" data-parsley-maxlength="100">

                        </div>

                    </div>

                    <div class="row mb-3">

                        <label class="form-label col-md-5 col-sm-5">From Name</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="text" id="from_name" name="from_name" class="form-control" maxlength="100" data-parsley-maxlength="100">

                        </div>

                    </div>

                    <div id="smtpSetup">

                        <div class="row mb-3 email-hide">

                            <label class="form-label col-md-5 col-sm-5">SMTP Host</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_host" name="smtp_host" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                        <div class="row mb-3 email-hide">

                            <label class="form-label col-md-5 col-sm-5">SMTP Port</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_port" name="smtp_port" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                        <div class="row mb-3 email-hide">

                            <label class="form-label col-md-5 col-sm-5">Encryption</label>

                            <div class="col-md-7 col-sm-7">

                                <select name="encryption_type" id="encryption_type" class="form-select">

                                    <option value="">None</option>

                                    <option value="SSL">SSL</option>

                                    <option value="TLS" selected>TLS</option>

                                </select>

                            </div>

                        </div>

                        <div class="row mb-3 email-hide">

                            <label class="form-label col-md-5 col-sm-5">SMTP Username</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_user_name" name="smtp_user_name" class="form-control" maxlength="100" data-parsley-maxlength="100">

                                <input type="hidden" id="emailsettingid" name="emailsettingid">

                            </div>

                        </div>

                        <div class="row mb-3 email-hide">

                            <label class="form-label col-md-5 col-sm-5">SMTP Password</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="password" id="smtp_password" name="smtp_password" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                        <div class="text-center">

                            <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">Update</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <div class="x_title mt-2">

            <h2>Send a test email</h2>

        </div>

        <div class="x_content">

            <div class="x_content-container">

                <form id="emailForm" enctype="multipart/form-data">

                    <div class="row mb-3">

                        <label class="form-label col-md-5 col-sm-5">Send To</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="email" id="txtSendTo" name="txtSendTo" placeholder="Email" class="form-control" maxlength="100" data-parsley-maxlength="100">

                        </div>

                    </div>

                    <div class="col-12 text-center mt-3">

                        <button type="submit" id="sbtSendEmail" class="btn btn-primary">Send Email</button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

       <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position" >

  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

    <!--</a>-->

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

    </a>

    

    <a class="nav-link active text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

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

    @include('emailsetting.partials.customers_js')

@endsection

