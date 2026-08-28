@extends('dashboard-layout.index')
@section('content')
<div class="col-sm-9 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-4" id="farename">Fare Management</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id="carFareForm">
                <table id="data-table_new" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>Types</th>
                            <th>4 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Hour" readonly></td>
                            <td><input type="number" class="form-control" name="first_hr" id="first_hr" value="4" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Km" readonly></td>
                            <td><input type="number" class="form-control" name="first_km" id="first_km" value="40" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Amount" readonly></td>
                            <td><input type="number" class="form-control" name="first_amount" id="first_amount" value="1500" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Km" readonly></td>
                            <td><input type="number" class="form-control" name="first_additional_km" id="first_additional_km" value="13" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Hours" readonly></td>
                            <td><input type="number" class="form-control" name="first_additional_hr" id="first_additional_hr" value="200" min="0"></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    
    <div class="card-header">
        <h4 class="card-title mr-4" id="farename">Outstation above KM > 250</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id="carFareFormOutstation">
                <table id="data-table_outstation" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>Types</th>
                            <th>4 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" value="Outstation Above Km" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_above_km" value="250"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Amount" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_min_amount" value="3500"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Km" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_km" value="12"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Day" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_day" value="600"></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    
    <div class="card-header">
        <h4 class="card-title mr-4" id="farename">Check Car Fares Calculation</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id="carFareCalculationForm">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Check Your Fare Calculation Kms/Hrs</th>
                            <th>4 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Hr </span>
                                    </div>
                                    <input type="text" 
                                        class="form-control" 
                                        id="hourly_input" 
                                        value="0"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Km</span>
                                    </div>
                                    <input type="text" 
                                        class="form-control" 
                                        id="km_input" 
                                        value="0"/>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Fare</span>
                                    </div>
                                    <input type="text" 
                                        class="form-control" 
                                        id="first_total"
                                        value="0" 
                                        readonly/>
                                
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="above_label">Above 250</span>
                                    </div>
                                    <input type="text" 
                                        class="form-control" 
                                        id="outstation_total"
                                        value="0" 
                                        readonly/>
                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100">
        <a class="nav-link active text-light" href="/carfares">
            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare Management
        </a>
        <a class="nav-link text-light" href="/locationrange">
            <i class="fas fa-map-marked-alt" style="margin-right: 8px;"></i>Pricing By Zone
        </a>
        <a class="nav-link text-light" href="/area">
            <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i>Pricing By Area
        </a>
        <a class="nav-link text-light" href="/faresetting">
            <i class="fas fa-money-bill-wave" style="margin-right: 8px;"></i>Fixed Area Price
        </a>
        <a class="nav-link text-light" href="/mapzone">
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
</style>
@endsection

@section('custom_scripts')
<script>
$(document).ready(function(){
    function updateAboveLabel() {
        let aboveKm = parseFloat($('#first_Outstation_above_km').val()) || 0;
        $('#above_label').text('Above ' + aboveKm);
    }

    // Update on page load
    updateAboveLabel();

    // Update if "first_Outstation_above_km" changes
    $('#first_Outstation_above_km').on('input', function(){
        updateAboveLabel();
    });

    // Your fare calculation logic
    $('#hourly_input, #km_input').on('input', function(){
        let first_hr = parseFloat($('#first_hr').val()) || 0;
        let first_km = parseFloat($('#first_km').val()) || 0;
        let first_amount = parseFloat($('#first_amount').val()) || 0;
        let first_additional_km = parseFloat($('#first_additional_km').val()) || 0;
        let first_additional_hr = parseFloat($('#first_additional_hr').val()) || 0;
        
        let first_Outstation_above_km = parseFloat($('#first_Outstation_above_km').val()) || 0;
        let first_Outstation_min_amount = parseFloat($('#first_Outstation_min_amount').val()) || 0;
        let first_Outstation_km = parseFloat($('#first_Outstation_km').val()) || 0;
        let first_Outstation_day = parseFloat($('#first_Outstation_day').val()) || 0;

        let hourly_input = parseFloat($('#hourly_input').val()) || 0;
        let km_input = parseFloat($('#km_input').val()) || 0;

        let normal_total = (((km_input - first_km) * first_additional_km) + first_amount) 
                         + ((hourly_input - first_hr) * first_additional_hr);

        let outstation_total = first_Outstation_min_amount 
                             + ((km_input - first_Outstation_above_km) * first_Outstation_km) 
                             + first_Outstation_day;

        $('#first_total').val(normal_total);
        $('#outstation_total').val(outstation_total);
    });
});


</script>
@endsection
