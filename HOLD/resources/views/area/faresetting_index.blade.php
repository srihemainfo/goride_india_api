@extends('dashboard-layout.index')



@section('content')





    <style>
        @media (max-width: 768px) {
            .modal-dialog-aside {
                width: 100% !important;
                height: 100% !important;
                margin: 0;
                max-width: none;
                left: 12px;
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

        }

        .modal-dialog-aside {
            left: 12px !important;
        }
    </style>

    <!--<div class="col-sm-9 main-card mb-2 card">-->
    <!--    <div class="card-header">-->
    <!--        <h4 class="card-title mb-0">Fixed Price/ Zone/ Mileage</h4>-->
    <!--    </div>-->

    <!--    <div class="p-3">-->
    <!--        <div class="form-group w-50 position-relative">-->
    <!--            <label for="areaSelect">Fare Settings/ Options</label>-->
    <!--            <select class="form-control" id="areaSelect" name="area" onchange="handleFareSetting(this)">-->
    <!--                <option value="">-- Select Fare Setting --</option>-->
    <!--                <option value="/faresetting">Fixed Price</option>-->
    <!--                <option value="/locationrange">Zone</option>-->
    <!--                <option value="/carfares">Mileage</option>-->
    <!--                <option value="/mapzone">Map Zone</option>-->
    <!--            </select>-->
                <!-- Icon overlay -->
    <!--            <i class="fas fa-chevron-down position-absolute"-->
    <!--                style="top: 38px; right: 15px; pointer-events: none; line-height: 2;"></i>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->





    <div class="col-sm-9  main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Fixed Area Price</h4>

            <div class="btn-actions-pane-right">

                <button type="button" class="btn btn-success" id="add_area"><i class="fas fa-plus"></i> Add Area </button>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="fare-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <!--<th style="width:5%;">#</th>-->

                            <th>ID</th>

                            <th>From </th>

                            <th>To </th>

                            <th>Fare </th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

        <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
            aria-orientation="vertical">



            <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

            <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

            <!--</a>-->



            <a class="nav-link text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare Management
        
            </a>
        
            
        
            <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
        
             <i class="fas fa-map-marked-alt" style="margin-right: 8px;"></i>Pricing By Zone
        
            </a>
        
             <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
        
           <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Pricing By Area
        
            </a>
        
            
        
            <a class="nav-link active text-dark" id="vert-tabs-right-promo-code-tab" href="/faresetting" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
        
              <i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i>Fixed Area Price
        
            </a>
        
            
        
            <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/mapzone" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
        
              <i class="fas fa-tags" style="margin-right: 8px;"></i>Fixed Zone Price
            </a>



            <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

            <!--  <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options-->

            <!--</a>-->

            <!--<a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

            <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->

            <!--</a>-->

            <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

            <!--  <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar-->

            <!--</a>-->

            <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->

            <!--  <i class="fas fa-star" style="margin-right: 8px;"></i> Review-->

            <!--</a>-->

        </div>

    </div>

   <div id="fixedprice-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Fixed Price Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="fixedPriceForm">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="fromarea">From Area Name <span class="required">*</span></label>
                            <select class="form-control select2" id="fromarea" name="fromarea"
                                data-placeholder="Enter Airport, Seaport, Postcode">
                                <option value=""></option>
                            </select>
                            <p class="text-danger invalid_fromarea"></p>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label for="toarea">To Area Name <span class="required">*</span></label>
                            <select class="form-control select2" id="toarea" name="toarea"
                                data-placeholder="Enter Airport, Seaport, Postcode">
                                <option value=""></option>
                            </select>
                            <p class="text-danger invalid_toarea"></p>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label for="fixed_price">Price <span class="required">*</span></label>
                        <input type="text" class="form-control" name="fixed_price" id="fixed_price" required
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5)"
                            placeholder="Enter the Amount">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="addsaveBtn">
                        <i class="fa fa-save"></i>&nbsp; Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


     <div id="fixedprice-editmodal" class="modal fixed-left fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Fixed Price Edit Form</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="fixedPriceeditForm">
                    <div class="modal-body">

                        <!-- <div class="form-group">
                            <label for="address" class="col-form-label">From Area Name<span
                                    class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="editfromarea" id="editfromarea"
                                placeholder="Enter From Address" maxlength="150">
                        </div> -->

                        <div class="col-sm-6 form-group">
                            <label for="editfromarea">From Area Name <span class="required">*</span></label>
                            <select class="form-control select2" id="editfromarea" name="editfromarea"
                                data-placeholder="Enter Airport, Seaport, Postcode">
                                <option value=""></option>
                            </select>
                            <p class="text-danger invalid_edit_fromarea"></p>
                        </div>

                        <!-- <div class="form-group">
                            <label for="address" class="col-form-label">To Area Name<span
                                    class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="edittoarea" id="edittoarea"
                                placeholder="Enter To Address" maxlength="150">
                        </div> -->

                         <div class="col-sm-6 form-group">
                            <label for="edittoarea">To Area Name <span class="required">*</span></label>
                            <select class="form-control select2" id="edittoarea" name="edittoarea"
                                data-placeholder="Enter Airport, Seaport, Postcode">
                                <option value=""></option>
                            </select>
                            <p class="text-danger invalid_edittoarea"></p>
                        </div>

                        <div class="form-group">
                            <label for="add_pickup_extra" class="col-form-label">Price<span
                                    class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="editfixed_price" id="editfixed_price" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5)"
                                placeholder="Enter the Amount">
                        </div>

                        <input type="hidden" name="edit_id" id="edit_id">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="editsaveBtn">
                            <i class="fa fa-save"></i>&nbsp; Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection



@section('custom_scripts')

    @include('area.partials.faresetting_js')

@endsection