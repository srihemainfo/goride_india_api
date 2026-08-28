@extends('dashboard-layout.index')
@section('content')





<div class="col-sm-9  main-card mb-3 card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0" id="farename">Fare Management</h4>

        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#kmTab" id="km_mile" data-toggle="tab">KM / Mile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tariffTab" id="tariff" data-toggle="tab">Tariff</a>
            </li>
        </ul>
             <h6 class="card-title">Default Fare</h6>
         <div>
            <select class="form-control" id="fareSelect" style="width:134px;">
                <option value="Km_mile">KM / Mile</option>
                <option value="Tariff">Tariff</option>
            </select>
        </div>
    </div>
    <div class="tab-content p-3">
        <!-- KM / Mile Content -->
        <div class="tab-pane fade show active" id="kmTab">
       
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Km/Mile Fare Management</h4>
                </div>
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

        <!-- Tariff Content -->
        <div class="tab-pane fade" id="tariffTab">
            <div class="card-header">
                <h4 class="card-title mr-4" id="farename">Tariff Fare Management</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form id="carFareForm">
                        <table id="tariff-table" class="table table-bordered" width="100%">
                           
                        </table>
                    </form>
                </div>
            </div>
            
            <div class="card-header">
                <h4 class="card-title mr-4" id="farename">Outstation above KM</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form id="carFareFormOutstation">
                        <table id="tariff-out-table" class="table" width="100%">
                          
                        </table>
                    </form>
                    <div class='text-center mt-4'>
                        <button id='update-tariff-btn' class='btn btn-success mb-3'>
                            <span id="btn-spinner" class="spinner-border spinner-border-sm text-light me-2" style="display: none;" role="status" aria-hidden="true"></span>
                            Update
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-header">
                <h4 class="card-title mr-4" id="farename">Check Car Fares Calculation</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <form id="carFareCalculationForm">
                        <table id="tariff-calculation-table" class="table table-bordered">
                          
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
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
    
    .card-header>.nav {
        margin-left: -0.625rem;
        height: 100%;
        width: 50%;
    }
</style>



@endsection
@section('custom_scripts')
@include('dynamiccarfare.partials.carfares_js_new')
@endsection