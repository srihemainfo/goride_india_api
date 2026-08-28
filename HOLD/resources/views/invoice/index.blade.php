@extends('dashboard-layout.index')



@section('content')

<div class="col-sm-10">

    @include('invoice.partials.filter')

    <style>
        @media screen and (min-width:320px) and (max-width:776px) {
            .hidden-card {
                display: none;
            }
        }
    </style>


    <div class="col-sm-12 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Invoice List</h4>

            <div class="btn-actions-pane-right">

                @if($IS_CREATABLE)

                <a href="{{ route('invoice.create') }}">

                    <button type="button" class="btn btn-success" id="addInvoice"><i class="fas fa-plus"></i> Create Invoice </button>

                </a>

                @endif

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="data-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Invoice No</th>

                            <th>Invoice Date</th>

                            <th>Client Info</th>

                            <th>Jobs No</th>

                            <th>Total</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>



                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<div class="col-sm-2 main-card mb-3 card payments" style="background-color: #343a40;">

    <div class="nav flex-column nav-tabs nav-tabs-right " id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">



        <a class="nav-link   text-light" id="vert-tabs-right-home-tab" href="/invoice" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

            <i class="fas fa-file-alt" style="margin-right: 8px;"></i> Generate Invoice

        </a>



        <a class="nav-link text-light" id="vert-tabs-right-offer-times-tab" href="/settlement" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-car" style="margin-right: 8px;"></i> Settlement

        </a>

        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/settlement" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

            <i class="fas fa-solid fa-sack-dollar" style="margin-right: 8px;"></i> Driver Payment

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

@include('invoice.partials.invoices_datatable_js')

@endsection