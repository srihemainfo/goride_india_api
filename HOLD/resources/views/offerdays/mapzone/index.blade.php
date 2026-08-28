@extends('dashboard-layout.index')



@section('content')

    <!--<div class="col-sm-10">-->

    @include('offerdays.locationrange.partials.filter')



    {{-- <div class="col-sm-12 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Map</h4>

        </div>

        <div class="card-body">

            <div id="map" style="height: 400px; width: 100%;">

            </div>

        </div>

    </div> --}}



    <div class="col-sm-9 main-card mb-5 card">

        <div class="card-header">

            <h4 class="card-title">Zone List</h4>

            <div class="btn-actions-pane-right">



                <!-- <a href="{{ route('locationrange.create') }}" class="btn btn-primary"><i class="fas fa-map"></i> Draw
                        Zone</a> -->



 

                <button type="button" class="btn btn-success" id="addLocationrange"><i class="fas fa-plus"></i> Add Location
                    Range</button>



            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="zone-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th style="width:7%;">#</th>
                            <th style="width:10%;">Zone Name</th>
                            <th style="width:10%;">Action</th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="col-sm-9 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Fixed Zone Price</h4>

            <div class="btn-actions-pane-right">



                <!-- <a href="{{ route('locationrange.create') }}" class="btn btn-primary"><i class="fas fa-map"></i> Draw
                        Zone</a> -->





                <button type="button" class="btn btn-success" id="setmap"><i class="fas fa-plus"></i> Add Map Zone </button>



            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="maplist-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Zone 1 Name</th>

                            <th>Zone 2 Name</th>

                            <th>Price</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>


    </div>

    <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

        <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
            aria-orientation="vertical">

           <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare Management
        
            </a>
        
            
        
            <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
        
             <i class="fas fa-map-marked-alt" style="margin-right: 8px;"></i>Pricing By Zone
        
            </a>
        
             <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
        
           <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Pricing By Area
        
            </a>
        
            
        
            <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/faresetting" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
        
              <i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i>Fixed Area Price
        
            </a>
        
            
        
            <a class="nav-link active text-light" id="vert-tabs-right-notification-tab" href="/mapzone" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
        
              <i class="fas fa-tags" style="margin-right: 8px;"></i>Fixed Zone Price
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



        .nav-link:hover {

            background-color: #6c757d !important;

        }

        @media (max-width: 768px) {
            .modal-dialog-aside {
                width: 100% !important;
                height: 100% !important;
                margin: 0;
                max-width: none;
            }

            .modal-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .btn-success {
                margin-top: 4px;
            }
        }
    </style>

    @include('offerdays.mapzone.partials.add_zonelist_modal')
    @include('offerdays.mapzone.partials.add_setzone_modal')
    @include('offerdays.mapzone.partials.show_zonelist_modal')

@endsection



@section('custom_scripts')

    @include('offerdays.mapzone.partials.mapzone_js')

@endsection