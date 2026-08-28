@extends('dashboard-layout.index')



@section('content')

<div class="col-sm-10">

    @include('settlement.partials.filter')


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
  
    .settlement{
            display: none;
        }

}
.modal-dialog-aside{
  left: 12px !important;
}
</style>
    <div class="col-sm-12 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Settlement List</h4>

            <div class="btn-actions-pane-right">

                <!--<a href="" target="_blank" id="settlement_report" class="btn btn-primary"><i class="fas fa-Print"></i> Print </a>-->

                <a href="">

                    <button type="button" class="btn btn-success" id="calcSettlement"><i class="fas fa-calculator"></i>

                        Calculate Settlement

                    </button>

                </a>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="data-table" class="table table-bordered" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th>Driver No.</th>

                            <th style="text-align: center;">Total</th>

                            <th style="text-align: center;">Commission</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody id="settle-table">

                    </tbody>

                    <tbody id="settlement_view">



                    </tbody>

                </table>

            </div>

        </div>

    </div>

    </div>

     <div class="col-sm-2 main-card mb-3 card  settlement" style="background-color: #343a40;">

  <div class="nav flex-column nav-tabs nav-tabs-right " id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <a class="nav-link   text-light" id="vert-tabs-right-home-tab" href="/invoice" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

      <i class="fas fa-file-alt" style="margin-right: 8px;"></i> Generate Invoice

    </a>

    

    <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/settlement" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

     <i class="fas fa-car" style="margin-right: 8px;"></i> Settlement

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



@include('settlement.partials.generate_settlement_modal')

@section('custom_scripts')

    @include('settlement.partials.settlement_js')

@endsection

