@extends('dashboard-layout.index')



@section('content')

    <div class="col-sm-9 mx-4 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Notification List</h4>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="data-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th style="width:5%;">#</th>

                            <th>ID</th>

                            <th>Driver Name</th>

                            <th>Booking ID</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/fleet" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

      <i class="fa-solid fa-car" style="margin-right: 8px;"></i> List Fleets

    </a>

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/offertimes" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Offer Times

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/offerdays" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i> Offer Days

    </a>

    

    <!-- <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/promocode" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i> Promo Code

    </a>

    

    <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/notifications" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fa-regular fa-bell" style="margin-right: 8px;"></i> Notification

    </a> -->

    

  </div>

</div>

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

    @include('offerdays.notification.partials.notification_js')

@endsection

