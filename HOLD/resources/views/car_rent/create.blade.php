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
                            <th>6 seater</th>
                            <th>8 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Hour" readonly></td>
                            <td><input type="number" class="form-control" name="first_hr" id="first_hr" value="4" min="0"></td>
                            <td><input type="number" class="form-control" name="second_hr" id="second_hr" value="4" min="0"></td>
                            <td><input type="number" class="form-control" name="third_hr" id="third_hr" value="4" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Km" readonly></td>
                            <td><input type="number" class="form-control" name="first_km" id="first_km" value="40" min="0"></td>
                            <td><input type="number" class="form-control" name="second_km" id="second_km" value="40" min="0"></td>
                            <td><input type="number" class="form-control" name="third_km" id="third_km" value="40" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Amount" readonly></td>
                            <td><input type="number" class="form-control" name="first_amount" id="first_amount" value="1500" min="0"></td>
                            <td><input type="number" class="form-control" name="second_amount" id="second_amount" value="1800" min="0"></td>
                            <td><input type="number" class="form-control" name="third_amount" id="third_amount" value="2000" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Km" readonly></td>
                            <td><input type="number" class="form-control" name="first_additional_km" id="first_additional_km" value="13" min="0"></td>
                            <td><input type="number" class="form-control" name="second_additional_km" id="second_additional_km" value="14" min="0"></td>
                            <td><input type="number" class="form-control" name="third_additional_km" id="third_additional_km" value="15" min="0"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Hours" readonly></td>
                            <td><input type="number" class="form-control" name="first_additional_hr" id="first_additional_hr" value="200" min="0"></td>
                            <td><input type="number" class="form-control" name="second_additional_hr" id="second_additional_hr" value="220" min="0"></td>
                            <td><input type="number" class="form-control" name="third_additional_hr" id="third_additional_hr" value="250" min="0"></td>
                        </tr>
                    </tbody>
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
                <table id="data-table_outstation" class="table" width="100%">
                    <thead>
                        <tr>
                            <th>Types</th>
                            <th>4 seater</th>
                            <th>6 seater</th>
                            <th>8 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control" value="Outstation Above Km" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_above_km" value="250"></td>
                            <td><input type="number" class="form-control" id="second_Outstation_above_km" value="250"></td>
                            <td><input type="number" class="form-control" id="third_Outstation_above_km" value="250"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Minimum Amount" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_min_amount" value="3500"></td>
                            <td><input type="number" class="form-control" id="second_Outstation_min_amount" value="4000"></td>
                            <td><input type="number" class="form-control" id="third_Outstation_min_amount" value="4500"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Additional Km" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_km" value="12"></td>
                            <td><input type="number" class="form-control" id="second_Outstation_km" value="13"></td>
                            <td><input type="number" class="form-control" id="third_Outstation_km" value="14"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control" value="Driver Bata Free For Per Day Limit" style="font-size: 15px;" readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_upto_km" value="400"></td>
                            <td><input type="number" class="form-control" id="second_Outstation_upto_km" value="400"></td>
                            <td><input type="number" class="form-control" id="third_Outstation_upto_km" value="400"></td>
                        </tr>
                         <tr>
                            <td><input type="text" class="form-control" value="Driver Bata " readonly></td>
                            <td><input type="number" class="form-control" id="first_Outstation_day_price" value="600"></td> 
                            <td><input type="number" class="form-control" id="second_Outstation_day_price" value="700"></td>
                            <td><input type="number" class="form-control" id="third_Outstation_day_price" value="800"></td>
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
                            <th>6 seater</th>
                            <th>8 seater</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="input-group"> 
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Km</span>
                                    </div>
                                    <input type="text" class="form-control" id="km_input" value="0" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,5)" />
                                </div>
                                <div class="input-group mt-2 d-none">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Hr </span>
                                    </div>
                                    <input type="text" class="form-control" id="hourly_input" value="0" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,5)"/>
                                </div>
                                <div class="input-group mt-2 d-none">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Day </span>
                                    </div>
                                    <input type="text" class="form-control" id="day_input" value="0" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,2)"/>
                                </div>
                            </td>
                            <td>
                            <div class="d-flex flex-column gap-2">
                                <div class="input-group w-100">
                                    <span class="input-group-text" style="min-width: 70px;">Fare</span>
                                    <input type="text" class="form-control" id="first_total" value="0" readonly>
                                </div>
                            
                                <div class="input-group w-100 d-none">
                                    <span class="input-group-text" style="min-width: 70px;">Out</span>
                                    <input type="text" class="form-control" id="first_outstation_total" value="0" readonly>
                                </div>
                            </div>


                            </td>
                            <td>
                               <div class="d-flex flex-column gap-2">
                                <div class="input-group w-100">
                                        <span class="input-group-text" style="min-width: 70px;">Fare</span>
                                    <input type="text" class="form-control" id="second_total" value="0" readonly />
                                </div>
                            
                                <div class="input-group w-100 d-none">
                                        <span class="input-group-text" id="above_label_2" style="min-width: 70px;">Out</span>
                                    <input type="text" class="form-control" id="second_outstation_total" value="0" readonly />
                                </div>
                            </div>

                            </td>
                            <td>
                               <div class="d-flex flex-column gap-2">
                                <div class="input-group w-100">
                                <span class="input-group-text" style="min-width: 70px;">Fare</span>
                                    <input type="text" class="form-control" id="third_total" value="0" readonly/>
                                </div>
                            
                                <div class="input-group w-100 d-none">
                                    <span class="input-group-text" id="above_label_3" style="min-width: 70px;">Out</span>
                                    <input type="text" class="form-control" id="third_outstation_total" value="0" readonly/>
                                </div>
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
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
        <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
        <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
        <!--</a>-->
        <a class="nav-link active text-dark" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

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
@endsection
@section('custom_scripts') 
<script>
    $(document).ready(function () {
    
        $('#first_Outstation_above_km, #second_Outstation_above_km, #third_Outstation_above_km').on('input', function () {
            let val = $(this).val();
            $('#first_Outstation_above_km, #second_Outstation_above_km, #third_Outstation_above_km').val(val);
        });
    
        $('#hourly_input, #km_input, #day_input').on('input', function () {
            let hr = parseFloat($('#hourly_input').val()) || 0;
            let km = parseFloat($('#km_input').val()) || 0;
            let day = parseFloat($('#day_input').val()) || 1; // default 1
    
            // Validation: if KM is 0 → reset totals
            if (km === 0) {
                $('#first_total, #first_outstation_total, #second_total, #second_outstation_total, #third_total, #third_outstation_total').val(0);
                return;
            }
    
            function calc(normal_hr, normal_km, normal_amt, add_km, add_hr,
                          out_above, out_min_amt, out_km, out_day, out_day_price, upto_km) {
    
                // Normal fare calculation
                let normal_total = ((km - normal_km) * add_km) + normal_amt + ((hr - normal_hr) * add_hr);
    
                // ✅ Outstation fare calculation
                let outstation_total = 0;
    
                if (upto_km > 0) {
                    // --- Day calculation logic ---
                    // First 2 days free (Day 1 = default, Day 2 = upto_km covered)
                    let days = 1; // start with default 1 day
                    let chargeableDays = 0;
    
                    if (km > upto_km) {
                        // Remaining after 2nd free day
                        let extra_km = km - (upto_km * 2);
    
                        if (extra_km > 0) {
                            // Extra days start only after 2nd day free limit
                            chargeableDays = Math.ceil(extra_km / upto_km);
                            days += 1 + chargeableDays; // include 2nd free day in total
                        } else {
                            // Only 2 free days used
                            days = 2;
                        }
                    }
    
                    // Outstation total = min + distance + chargeable days only
                    outstation_total = out_min_amt + ((km - out_above) * out_km) + (chargeableDays * out_day_price);
    
                    // override visible day_input field also
                    $('#day_input').val(days);
    
                } else {
                    // Old formula if upto_km = 0
                    outstation_total = out_min_amt + ((km - out_above) * out_km) + (out_day * out_day_price);
                }
    
                return [normal_total, outstation_total];
            }
    
            // --- 4 Seater ---
            let [f1, o1] = calc(
                parseFloat($('#first_hr').val()), parseFloat($('#first_km').val()), parseFloat($('#first_amount').val()),
                parseFloat($('#first_additional_km').val()), parseFloat($('#first_additional_hr').val()),
                parseFloat($('#first_Outstation_above_km').val()), parseFloat($('#first_Outstation_min_amount').val()),
                parseFloat($('#first_Outstation_km').val()), day, parseFloat($('#first_Outstation_day_price').val()),
                parseFloat($('#first_Outstation_upto_km').val())
            );
    
            // --- 6 Seater ---
            let [f2, o2] = calc(
                parseFloat($('#second_hr').val()), parseFloat($('#second_km').val()), parseFloat($('#second_amount').val()),
                parseFloat($('#second_additional_km').val()), parseFloat($('#second_additional_hr').val()),
                parseFloat($('#second_Outstation_above_km').val()), parseFloat($('#second_Outstation_min_amount').val()),
                parseFloat($('#second_Outstation_km').val()), day, parseFloat($('#second_Outstation_day_price').val()),
                parseFloat($('#second_Outstation_upto_km').val())
            );
    
            // --- 8 Seater ---
            let [f3, o3] = calc(
                parseFloat($('#third_hr').val()), parseFloat($('#third_km').val()), parseFloat($('#third_amount').val()),
                parseFloat($('#third_additional_km').val()), parseFloat($('#third_additional_hr').val()),
                parseFloat($('#third_Outstation_above_km').val()), parseFloat($('#third_Outstation_min_amount').val()),
                parseFloat($('#third_Outstation_km').val()), day, parseFloat($('#third_Outstation_day_price').val()),
                parseFloat($('#third_Outstation_upto_km').val())
            );
    
            // --- Assign values ---
            $('#first_total').val(Math.round(f1));
            $('#first_outstation_total').val(Math.round(o1));
    
            $('#second_total').val(Math.round(f2));
            $('#second_outstation_total').val(Math.round(o2));
    
            $('#third_total').val(Math.round(f3));
            $('#third_outstation_total').val(Math.round(o3));
    
            // Toggle Fare type & Input type
            toggleFareAndInput(km, '#first_Outstation_above_km', '#first_total', '#first_outstation_total');
            toggleFareAndInput(km, '#second_Outstation_above_km', '#second_total', '#second_outstation_total');
            toggleFareAndInput(km, '#third_Outstation_above_km', '#third_total', '#third_outstation_total');
        });
    
        function toggleFareAndInput(km, aboveSelector, fareSelector, outstationSelector) {
            let aboveKm = parseFloat($(aboveSelector).val()) || 0;
    
            if (km >= aboveKm) {
                // Show Outstation Fare & Day Input
                $(outstationSelector).closest('.input-group').removeClass('d-none');
                $(fareSelector).closest('.input-group').addClass('d-none');
                $('#day_input').closest('.input-group').removeClass('d-none');
                $('#hourly_input').closest('.input-group').addClass('d-none');
            } else {
                // Show Normal Fare & Hourly Input
                $(fareSelector).closest('.input-group').removeClass('d-none');
                $(outstationSelector).closest('.input-group').addClass('d-none');
                $('#hourly_input').closest('.input-group').removeClass('d-none');
                $('#day_input').closest('.input-group').addClass('d-none');
            }
        }
    });





</script>

@endsection
