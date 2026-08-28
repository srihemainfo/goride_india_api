@extends('dashboard-layout.index')
@section('content')
<div class="col-sm-9  main-card mb-3 card d-none">
    <div class="card-header">
        <h4 class="card-title mr-4">Car Fares List</h4>
    </div>
    <div class='row d-none'>
        <div class="col-sm-2 mt-2">
            <input class='form-control' id='start' oninput='validateInputInteger(this)' value='1' disabled>
        </div>
        <div class="col-sm-2 mt-2">
            <input class='form-control' oninput='validateInputInteger(this)' id='end' value>
        </div>
        <div class="col-sm-2 mt-2">
            <button id='addButton' class='btn btn-success'>AddButton</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm_old'>
                <table id="data-table" class="table" width="100%">
                    <thead id='header_container'>
                        <!--<tr id="table-header"></tr>-->
                    </thead>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <!--<button id='update-all-btn' class='btn btn-success'>Update</button>-->
            </div>
        </div>
    </div>




    <!-- <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table" class="table" width="100%">
                    <div class="card-header">
                        <h4 class="card-title mr-4">Car Fares List</h4>
                    </div>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success'>Update</button>
            </div>
        </div>
    </div> -->



</div>




<div class="col-sm-9  main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-4" id="farename">Fare Management</h4>
    </div>
    <!--<div class='row d-none'>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <input class='form-control' id='start' oninput='validateInputInteger(this)' value='1' disabled>-->
    <!--    </div>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <input class='form-control' oninput='validateInputInteger(this)' id='end' value>-->
    <!--    </div>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <button id='addButton' class='btn btn-success'>AddButton</button>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table_new" class="table" width="100%">
                   
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success mb-3'>Update</button>
            </div>
        </div>
    </div>




</div>

<div class="col-sm-9  main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-1">Check Car Fares Calculation </h4>
        <button type="button" style="border-radius: 50%; font-size: 11px !important; width:16px; height: 21px; padding: 3px; " class="btn btn-secondary mb-3" data-toggle="tooltip" data-placement="top" title="Please Update the Car's Fares List Before Calculating!."><i class="fa-solid fa-info"></i></button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id='priceCal'>
                <table id="data-table_one" class="table" width="100%">
                   
                </table>
            </form>
        </div>
    </div>




</div>



<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
        <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
        <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
        <!--</a>-->
        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

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
    
        
    
        <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/mapzone" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
    
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
</style>



@endsection
@section('custom_scripts')
@include('dynamiccarfare.partials.carfares_js_new')
@endsection