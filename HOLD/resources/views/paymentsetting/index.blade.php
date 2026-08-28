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

    <div class="right_col" role="main">
    <div class="x_panel">

      <div class="x_title">

      <h2>Payment Option</h2>

      </div>

      <div class="x_content">

      <form id="formSettingsPayment" class="form-horizontal" method="post" data-parsley-validate>

        <div class="card border border-secondary mb-3">

        <div class="card-header bg-secondary text-white">

          <div class="form-check">

          <input class="form-check-input" type="checkbox" name="cashPayment" id="cashPaymentFor">

          <label class="form-check-label" for="cashPaymentFor" style="font-size:18px;">Cash</label>

          </div>

        </div>

        <div class="card-body">

          <label class="form-label">Title</label>

          <input type="text" name="cash_title" id="cash_title" class="form-control" maxlength="100"
          data-parsley-maxlength="100" value="pay_by_cash" readonly>

        </div>

        </div>

        <div class="row row-cols-1 g-4">



        <!-- <div class="col">

          <div class="card border border-secondary">

          <div class="card-header bg-secondary text-white">

            <div class="d-flex align-items-center gap-3">

            <div class="form-check mb-0">

              <input class="form-check-input" type="checkbox" name="paypalPayment" id="paypalPaymentFor">

              <label class="form-check-label" for="paypalPaymentFor" style="font-size:18px;">PayPal</label>

            </div>

            <div class="form-check form-switch mb-0">

              <input class="form-check-input" type="checkbox" name="paypal_live_check"
              style="margin-left: -12px;" id="paypal_live_check" value="live">

              <label class="form-check-label" for="paypalEnvironmentFor"
              style="font-size:18px;margin-left: 22px;margin-top: 1px;">Live mode</label>

            </div>

            </div>

          </div>

          <div class="card-body">

            <div class="row g-3">

            <div class="col-12 col-md-6 col-lg-4">

              <div class="input-group">

              <label class="input-group-text">Title</label>

              <input type="text" name="paypal_title" id="paypal_title" class="form-control" maxlength="100"
                data-parsley-maxlength="100" value="paypal" readonly>

              </div>

            </div>

            <div class="col-12 col-md-6 col-lg-4">

              <div class="input-group">

              <label class="input-group-text">ID</label>

              <input type="text" id="paypal_id" name="paypal_id" class="form-control" maxlength="100"
                data-parsley-maxlength="100">

              </div>

            </div>

            <div class="col-12 col-md-12 col-lg-4">

              <div class="input-group">

              <label class="input-group-text">Identity Token</label>

              <input type="text" id="paypal_identify_token" name="paypal_identify_token" class="form-control"
                maxlength="100" data-parsley-maxlength="100">

              </div>

            </div>

            </div>

          </div>

          </div>

        </div> -->



        <div class="col-md-12">

          <div class="card border border-secondary">

          <div class="card-header bg-secondary text-white">

            <div class="d-flex align-items-center gap-3">

            <div class="form-check mb-0">

              <input class="form-check-input" type="checkbox" name="stripePayment" id="stripePaymentFor">

              <label class="form-check-label" for="stripePayment" style="font-size:18px;">Stripe</label>

            </div>

            <div class="form-check form-switch mb-0">

              <input class="form-check-input" type="checkbox" name="stripe_live_check"
              style="margin-left: -12px;" id="stripe_live_check" value="live">

              <label class="form-check-label" for="stripe_live_check"
              style="font-size:18px;margin-left: 22px;margin-top: 1px;">Live mode</label>

            </div>

            </div>

          </div>

          <div class="card-body">

            <div class="row g-3">

            <div class="col-12 col-sm-4 col-md-3 col-lg-3">

              <div class="input-group">

              <label class="input-group-text">Title</label>

              <input type="text" name="stripe_title" id="stripe_title" class="form-control" maxlength="100"
                data-parsley-maxlength="100" value="stripe" readonly>

              </div>

            </div>

            <div class="col-12 col-sm-8 col-md-5 col-lg-5">

              <div class="input-group">

              <label class="input-group-text">Publishable Key</label>

              <input type="text" id="stripePublishableKey" name="stripePublishableKey" class="form-control"
                maxlength="200" data-parsley-maxlength="200">

              </div>

            </div>

            <div class="col-12 col-sm col-md-4 col-lg-4">

              <div class="input-group">

              <label class="input-group-text">Secret Key</label>

              <input type="text" id="stripeSecretKey" name="stripeSecretKey" class="form-control"
                maxlength="200" data-parsley-maxlength="200">

              </div>

            </div>

            <div class="col-12 col-md-6 col-lg-4">

              <label class="form-label">Webhook Endpoint URL</label>

              <div class="input-group">

              <input type="text" id="stripeWebhookUrl" name="stripeWebhookUrl" class="form-control">

              <span id="endpointTooltip" class="btn btn-primary copy" data-copy-elem="stripeWebhookUrl"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Copied">Copy</span>

              </div>

            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-4">

              <label class="form-label">Webhook Events</label>

              <div class="input-group">

              <input type="text" id="stripeWebhookEvent" name="stripeWebhookEvent" class="form-control">

              <span id="eventsTooltip" class="btn btn-primary copy" data-copy-elem="stripeWebhookEvent"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Copied">Copy</span>

              </div>

            </div>

            <div class="col-12 col-sm-6 col-md-6 col-lg-4">

              <label class="form-label">Webhook Signing Secret</label>

              <input type="text" id="stripeWebhookSecretKey" name="stripeWebhookSecretKey" class="form-control"
              maxlength="100" data-parsley-maxlength="150">

            </div>

            </div>

          </div>

          </div>

        </div>



        <!-- <div class="col-md-12">

          <div class="card border border-secondary">

          <div class="card-header bg-secondary text-white">

            <div class="d-flex align-items-center gap-3">

            <div class="form-check mb-0">

              <input class="form-check-input" type="checkbox" name="squarePayment" value id="squarePaymentFor">

              <label class="form-check-label" for="squarePaymentFor" style="font-size:18px;">Square</label>

            </div>

            <div class="form-check form-switch mb-0">

              <input class="form-check-input" type="checkbox" name="square_live_check"
              style="margin-left: -12px;" id="square_live_check" value="live">

              <label class="form-check-label" for="square_live_check"
              style="font-size:18px;margin-left: 22px;margin-top: 1px;">Live mode</label>

            </div>

            </div>

          </div>

          <div class="card-body">

            <div class="row g-3">

            <div class="col-12 col-sm-4 col-md-3 col-lg-3">

              <div class="input-group">

              <label class="input-group-text">Title</label>

              <input type="text" name="square_title" id="square_title" class="form-control" maxlength="100"
                data-parsley-maxlength="100" value="square" readonly>

              </div>

            </div>

            <div class="col-12 col-sm-8 col-md-9 col-lg-9">

              <div class="input-group">

              <label class="input-group-text">Access Token</label>

              <input type="text" id="txtsquare_accessToken" name="txtsquare_accessToken" class="form-control"
                maxlength="100" data-parsley-maxlength="100">

              </div>

            </div>

            <div class="col-12 col-sm-7">

              <div class="input-group">

              <label class="input-group-text">Application Id</label>

              <input type="text" id="txt_square_appId" name="txt_square_appId" class="form-control"
                maxlength="100" data-parsley-maxlength="100">

              </div>

            </div>

            <div class="col-12 col-sm-5">

              <div class="input-group">

              <label class="input-group-text">Location Id</label>

              <input type="text" id="txt_square_locationId" name="txt_square_locationId" class="form-control"
                maxlength="100" data-parsley-maxlength="100">

              <input type="hidden" name="paymentsetting_id" id="paymentsetting_id">

              </div>

            </div>

            </div>

          </div>

          </div>

        </div> -->

        </div>

        <div class="text-center mt-3">

        <button type="button" name="saveBtn" id="saveBtn" class="btn btn-primary">UPDATE</button>

        </div>

      </form>

      </div>

    </div>

    </div>

  </div>

  <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
    aria-orientation="vertical">



    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

    <!--</a>-->



    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab"
      aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

    </a>



    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab"
      aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

    </a>



    <a class="nav-link  text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab"
      aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-plus" style="margin-right: 8px;"></i> Email Template

    </a>



    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab"
      aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

    </a>

    <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

    <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->

    <!--</a>-->

    {{-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab"
      aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a> --}}
    <a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/whatsapp-configuration" role="tab"
      aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;" contenteditable="false">

      <i class="fab fa-whatsapp" style="margin-right: 8px;"></i>WhatsApp Config

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab"
      aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-star" style="margin-right: 8px;"></i> Review

    </a>

    </div>

  </div>



  <style>
    .nav-tabs .nav-link:hover {

    background-color: #747474 !important;

    color: white !important;

    }

    .nav-link.active {

    background-color: #fff !important;

    color: #343a40 !important;

    }

    @media (max-width:776px) {
    .x_title h2 {
      font-size: x-large;
    }

    .form-check-label {
      font-size: 15px !important;
    }
    }



    .nav-link:hover {

    background-color: #6c757d !important;

    }
  </style>

@endsection



@section('custom_scripts')

  @include('paymentsetting.partials.customers_js')

@endsection