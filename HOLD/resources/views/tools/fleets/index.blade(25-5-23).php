@extends('dashboard-layout.index')

@section('content')

@include('prices.fixed.partials.filter')

<div class="col-sm-12 main-card mb-2 card">
    <div class="card-header">
        <h4 class="card-title">Fixed Price List</h4>
        <div class="btn-actions-pane-right">
            @if($IS_CREATABLE)
                <button type="button" class="btn btn-success" id="addPrice"><i class="fas fa-plus"></i> Add Fixed Price </button>
            @endif    
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="text-align:center;">Area From</th>
                        <th style="text-align:center;">Area To</th>
                        <th style="width: 7.5%;text-align:center;">Saloon</th>
                        <th style="width: 7.5%;text-align:center;">Estate</th>
                        <th style="width: 7.5%;text-align:center;">MPV</th>
                        <th style="width: 7.5%;text-align:center;">MPV5</th>
                        <th style="width: 7.5%;text-align:center;">MPV6</th>
                        <th style="width: 7.5%;text-align:center;">MPV 8</th>
                        <th style="width: 7.5%;text-align:center;">Executive Saloon</th>
                        <th style="width: 7.5%;text-align:center;">MPV Executive</th>
                        <th style="width:12%;">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

    @include('prices.fixed.partials.add_fixed_price_modal')
@endsection

@section('custom_scripts')
    @include('prices.fixed.partials.fixed_price_js')
@endsection