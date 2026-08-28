
<script>

$(document).ready(function () {

    function getfaretype() {
        try {
            $.ajax({
                url: '{{ env("API_URL") }}faretype',
                method: 'POST',
                data: {
                    token: getCookie('d_token')
                },
                success: function (response) {
                    // console.log('return f', response);
    
                    if (response.status == 200 && response.data && response.data.fare_type) {
                        if (response.data.fare_type == 1) {
                            $('select.form-control').val('Km_mile');
                            $('#km_mile').tab('show');
                        } else if (response.data.fare_type == 2) {
                            $('select.form-control').val('Tariff');
                            $('#tariff').tab('show');
                        }
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }       

    getfaretype();
    gettariff_FareList();

});

    $('#fareSelect').on('change', function () {
        var selectedValue = $(this).val();
        $.ajax({
            url: '{{ env("API_URL") }}faretype_update',
            method: 'POST',
            data: {
                token: getCookie('d_token'),
                fare_type: (selectedValue === 'Km_mile' ? 1 : 2)
            },
            success: function (response) {
                if (response.status === 200) {
                    showToast('success', response.message, 2000);
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    showToast('Error', response.message || 'Something went wrong', 5000);
                }
            },
            error: function (xhr, status, error) {
                showToast('Error', xhr.responseJSON?.message || error, 5000);
            }
        });
    });
    
    const _changeVal = (i, newValue) => {
        try {
            let fareList = JSON.parse(localStorage.getItem('fareList')) || [];
            const parsedValue = parseInt(newValue, 10);
            const updateButton = $('#update-all-btn');
            updateButton.prop('disabled', true);
            if (isNaN(parsedValue) || parsedValue <= 0) {
                showToast('error', 'Please enter a valid number greater than 0', 5000);
                return;
            }
            if (fareList[i]) {
                // const startValue = fareList[i].start;

                const startValue = $(`#start_${i+1}`).val();
                if (parsedValue <= startValue) {
                    showToast('error', 'End value must be greater than Start value', 5000);
                    return;
                }
                fareList[i].end = parsedValue;
                localStorage.setItem('fareList', JSON.stringify(fareList));
            }
            buildHTM();
            updateButton.prop('disabled', false);
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }
    
    //Tariff 
    const gettariff_FareList = () => {
        try {
            $.ajax({
                url: '{{ env('API_URL') }}getTarif_Fare',
                method: 'POST',
                data: {
                    token: getCookie('d_token')
                },
                success: function (response) {
                    // alert('hii');
                    if (response.status === 200) {
                       buildTariff(response.data);
                       buildTariffOutstation(response.data);
                       buildTariffCalculation(response.data);
                    } else {
                        showToast('error', 'Fare details collection process failed!', 5000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }
    
    // Tariff Km and hr
    const buildTariff = (data) => {
    try {
        if (!data || !Array.isArray(data) || data.length === 0) {
            showToast('error', 'No tariff data found!', 5000);
            return;
        }

        let BuildHtm = `
            <thead>
                <tr>
                    <th>Types</th>
        `;

        data.forEach(car => {
            BuildHtm += `<th>${_.capitalize(car.car_name)}</th>`;
        });

        BuildHtm += `</tr></thead><tbody>`;

        const addRow = (label, key, prefix) => {
            BuildHtm += `<tr><td><input type="text" class="form-control" value="${label}" readonly></td>`;
            data.forEach((car, i) => {
                const name = `${prefix}_${car.car_id}`;
                const id   = `${prefix}_${car.car_id}`;
                BuildHtm += `
                    <td>
                        <input type="hidden" name="car_id_${car.car_id}" value="${car.car_id}">
                        <input type="hidden" name="car_name_${car.car_id}" value="${car.car_name}">
                        <input type="number" class="form-control" name="${name}" id="${id}" value="${car[key] ?? 0}" min="0">
                    </td>`;
            });
            BuildHtm += `</tr>`;
        };

        addRow("Minimum Hour", "min_hr", "min_hr");
        addRow("Minimum Km", "min_km", "min_km");
        addRow("Minimum Amount", "min_amount", "min_amount");
        addRow("Additional Km", "additional_km", "additional_km");
        addRow("Additional Hours", "additional_hours", "additional_hours");

        BuildHtm += `</tbody>`;

        $(`#tariff-table`).html(BuildHtm);

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
};

    // Tariff Outstation
    const buildTariffOutstation = (data) => {
    try {
        if (!data || !Array.isArray(data) || data.length === 0) {
            showToast('error', 'No tariff data found!', 5000);
            return;
        }

        let BuildHtm = `
            <thead>
                <tr>
                    <th>Types</th>
        `;

        data.forEach(car => {
            BuildHtm += `<th>${_.capitalize(car.car_name)}</th>`;
        });

        BuildHtm += `</tr></thead><tbody>`;

        const addRow = (label, key, prefix) => {
            BuildHtm += `<tr><td><input type="text" class="form-control" value="${label}" readonly></td>`;
            data.forEach((car) => {
                const name = `${prefix}_${car.car_id}`;
                const id   = `${prefix}_${car.car_id}`;
                BuildHtm += `<td><input type="number" class="form-control" name="${name}" id="${id}" value="${car[key] ?? 0}" min="0"></td>`;
            });
            BuildHtm += `</tr>`;
        };

        addRow("Outstation Above Km", "outstation_above_km", "out_above_km");
        addRow("Minimum Amount", "out_amount", "out_amount");
        addRow("Additional Km", "out_additional_km", "out_add_km");
        addRow("Driver Bata Free For Per Day Limit", "out_additional_day", "out_add_upto_km");
        addRow("Driver Bata ", "out_additional_day_price", "out_add_day_price");

        BuildHtm += `</tbody>`;

        $(`#tariff-out-table`).html(BuildHtm);

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
};

    const buildTariffCalculation = (data) => {
        try {
            if (!data || !Array.isArray(data) || data.length === 0) {
                showToast('error', 'No tariff calculation data found!', 5000);
                return;
            }
    
            let BuildHtm = `
                <form id="carFareCalculationForm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Check Your Fare Calculation Kms/Hrs</th>
            `;
    
            data.forEach(car => {
                BuildHtm += `<th>${_.capitalize(car.car_name)}</th>`;
            });
    
            BuildHtm += `</tr></thead><tbody><tr><td>`;
    
            BuildHtm += `
                <div class="input-group"> 
                    <div class="input-group-prepend"><span class="input-group-text">Km</span></div>
                    <input type="text" class="form-control" id="km_input" value="0" maxlength="5" 
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,5)" />
                </div>
                <div class="input-group mt-2 d-none">
                    <div class="input-group-prepend"><span class="input-group-text">Hr</span></div>
                    <input type="text" class="form-control" id="hourly_input" value="0" maxlength="5" 
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,5)" />
                </div>
                <div class="input-group mt-2 d-none">
                    <div class="input-group-prepend"><span class="input-group-text">Day</span></div>
                    <input type="text" class="form-control" id="day_input" value="1" maxlength="2" 
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,2)" />
                </div>
            `;
            BuildHtm += `</td>`;
    
            data.forEach(car => {
                let cid = _.camelCase(car.car_name);
                BuildHtm += `
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <div class="input-group w-100">
                                <span class="input-group-text" style="min-width:70px;">Fare</span>
                                <input type="text" class="form-control" id="${cid}_total" value="0" readonly />
                            </div>
                            <div class="input-group w-100 d-none">
                                <span class="input-group-text" style="min-width:70px;">Out</span>
                                <input type="text" class="form-control" id="${cid}_outstation_total" value="0" readonly />
                            </div>
                        </div>
                    </td>
                `;
            });
    
            BuildHtm += `</tr></tbody></table></form>`;
    
            $('#tariff-calculation-table').html(BuildHtm);
    
            attachCalculationHandler(data);
    
        } catch (e) {
            console.error(`Error in buildTariffCalculation: ${e.message}`);
        }
    };
    
    const attachCalculationHandler = (data) => {
        $('#hourly_input, #km_input, #day_input').on('input', function () {
            let hr = parseFloat($('#hourly_input').val()) || 0;
            let km = parseFloat($('#km_input').val()) || 0;
            let day = parseFloat($('#day_input').val()) || 0;
    
            if (km === 0) {
                data.forEach(car => {
                    let cid = _.camelCase(car.car_name);
                    $(`#${cid}_total, #${cid}_outstation_total`).val(0);
                });
                return;
            }
    
            function calc(car) {
                let id = car.car_id;
                let min_hr = parseFloat($(`#min_hr_${id}`).val()) || 0;
                let min_km = parseFloat($(`#min_km_${id}`).val()) || 0;
                let min_amount = parseFloat($(`#min_amount_${id}`).val()) || 0;
                let add_km = parseFloat($(`#additional_km_${id}`).val()) || 0;
                let add_hr = parseFloat($(`#additional_hours_${id}`).val()) || 0;
    
                // Normal total
                let normal_total = ((km - min_km) * add_km) + min_amount + ((hr - min_hr) * add_hr);
    
                // Outstation total with free day logic
                let extraKm = km - (parseFloat(car.outstation_above_km) || 0);
                if (extraKm < 0) extraKm = 0;
    
                let baseAmount = (parseFloat(car.out_amount) || 0) + (extraKm * (parseFloat(car.out_additional_km) || 0));
    
                // Fee days logic
                let uptoKm = parseFloat(car.out_additional_day) || 0;
                let freeDays = 1;
                if (uptoKm > 0) {
                    freeDays = Math.round(km / uptoKm) + 1;
                }
    
                let extraDayCharges = 0;
                if (day > freeDays) {
                    extraDayCharges = (day - freeDays) * (parseFloat(car.out_additional_day_price) || 0);
                }
    
                let outstation_total = baseAmount + extraDayCharges;
    
                return [normal_total, outstation_total];
            }
    
            function applyMinimumLogic(km, hr, min_km, min_hr, min_amount, add_km_amt, add_hr_amt) {
                let total = min_amount;
                if (km < min_km && hr > min_hr) total += (hr - min_hr) * add_hr_amt;
                if (hr < min_hr && km > min_km) total += (km - min_km) * add_km_amt;
                if (km >= min_km && hr >= min_hr) return false;
                return total;
            }
    
            data.forEach(car => {
                let cid = _.camelCase(car.car_name);
                let [fare, outFare] = calc(car);
    
                let customFare = applyMinimumLogic(
                    km, hr,
                    parseFloat($(`#min_km_${car.car_id}`).val()),
                    parseFloat($(`#min_hr_${car.car_id}`).val()),
                    parseFloat($(`#min_amount_${car.car_id}`).val()),
                    parseFloat($(`#additional_km_${car.car_id}`).val()),
                    parseFloat($(`#additional_hours_${car.car_id}`).val())
                );
                if (customFare !== false) fare = customFare;
    
                $(`#${cid}_total`).val(Math.round(fare));
                $(`#${cid}_outstation_total`).val(Math.round(outFare));
    
                toggleFareAndInputDynamic(km, car.outstation_above_km, `#${cid}_total`, `#${cid}_outstation_total`);
            });
        });
    };
    
    function toggleFareAndInputDynamic(km, aboveKm, fareSelector, outstationSelector) {
        if (km >= aboveKm) {
            $(outstationSelector).closest('.input-group').removeClass('d-none');
            $(fareSelector).closest('.input-group').addClass('d-none');
            $('#day_input').closest('.input-group').removeClass('d-none');
            $('#hourly_input').closest('.input-group').addClass('d-none');
        } else {
            $(fareSelector).closest('.input-group').removeClass('d-none');
            $(outstationSelector).closest('.input-group').addClass('d-none');
            $('#hourly_input').closest('.input-group').removeClass('d-none');
            $('#day_input').closest('.input-group').addClass('d-none');
        }
    }

    const collectTariffData = (tableId) => {
        return $(`#${tableId} tr`).map(function () {
            let rowData = {};
            $(this).find('input').each(function () {
                let inputName = $(this).attr('name');
                let inputValue = $(this).val() || '0';
                if (inputName) {
                    rowData[inputName] = inputValue;
                }
            });
            return Object.keys(rowData).length > 0 ? rowData : null;
        }).get();
    };
    
    $('#update-tariff-btn').on('click', function () {
        let $btn = $(this);
        let $spinner = $('#btn-spinner');
        
        let isValid = true;
        $(`#tariff-out-table input[name^="out_above_km_"]`).each(function () {
            let value = parseFloat($(this).val()) || 0;
            if (value <= 0) {
                showToast('error', 'Please enter a valid Outstation Above Km value!', 5000);
                isValid = false;
                return false; // stop looping
            }
        });
    
        if (!isValid) {
            return; // prevent further execution
        }
        
        // Validate Outstation Above Km vs Minimum Km
        $(`#tariff-out-table input[name^="out_above_km_"]`).each(function () {
            let $outInput = $(this);
            let valueOut = parseFloat($outInput.val()) || 0;
        
            let nameParts = $outInput.attr('name').split('_');
            let carId = nameParts[nameParts.length - 1];
        
            let $minKmInput = $(`#tariff-table input[name="min_km_${carId}"]`);
            if ($minKmInput.length > 0) {
                let valueMinKm = parseFloat($minKmInput.val()) || 0;
        
                if (valueOut <= valueMinKm) {
                    // Find the index of this input's column
                    let colIndex = $outInput.closest('td').index();
                    // Find the header text at this index
                    let carName = $(`#tariff-out-table th`).eq(colIndex).text().trim() || `ID ${carId}`;
        
                    showToast('error', `Outstation Above Km must be greater than Minimum Km for ${carName}!`, 5000);
                    isValid = false;
                    return false;
                }
            }
        });

    
        if (!isValid) {
            return; // stop if invalid found
        }
    
        $spinner.show();
        $btn.prop('disabled', true);
    
        let localTariff    = collectTariffData('tariff-table');
        let outstationFare = collectTariffData('tariff-out-table');
    
        $.ajax({
            url: '{{ env("API_URL") }}tariff_update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                token: getCookie('d_token'),
                tariff: localTariff,
                outstation: outstationFare
            }),
            success: function (response) {
                if (response.status === 200) {
                    showToast('success', response.message, 2000);
                    setTimeout(() => location.reload(), 700);
                } else {
                    showToast('error', response.message || 'Something went wrong', 5000);
                }
            },
            error: function (xhr, status, error) {
                showToast('error', xhr.responseJSON?.message || error, 3000);
            },
            complete: function () {
                $spinner.hide();
                $btn.prop('disabled', false);
            }
        });
    });

    const getFareList = () => {
        try {
            $.ajax({
                url: '{{ env('API_URL') }}getCarFare',
                method: 'POST',
                data: {
                    token: getCookie('d_token')
                },
                success: function (response) {
                    if (response.status === 200) {
                        localStorage.setItem('careNameList', JSON.stringify(response.data.careNameList));
                        localStorage.setItem('fareList', JSON.stringify(response.data.fareList));
                        buildHTM();
                        buildFareCalHTM();
                    } else {
                        showToast('error', 'Fare details collection process failed!', 5000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }
    
    const buildHTM = () => {
        try {
            const careNameList = JSON.parse(localStorage.getItem('careNameList'));
            const fareList = JSON.parse(localStorage.getItem('fareList'));
            if (!careNameList || !fareList) {
                // console.error('Data not found in localStorage');
                showToast('error', 'Data not found!', 5000);
                return;
            }
            $(`#data-table_new`).html('');
            let BuildHtm = `<thead>
            <tr>
                <th scope="col">Start (KM/Miles)</th>
                <th scope="col">End (KM/Miles)</th>`;
            careNameList.forEach(careName => {
                BuildHtm += `<th scope="col">${_.capitalize(careName.replace('_', ' '))}</th>`;
            });
            BuildHtm += `<th scope="col">Action</th>`;
            BuildHtm += `</tr></thead><tbody>`;
            const nonReadonlyRowIndex = fareList.length - 2;
            fareList.forEach((e, i) => {
                BuildHtm += `<tr>
                                        <td>
                                    <input type="text" 
                                        class="form-control" 
                                        id="start_${i + 1}" 
                                        name="start[]" 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                        maxlength="10" 
                                        value="${fareList[i - 1] && fareList[i - 1].end ? fareList[i - 1].end : e.start}" 
                                        readonly />
                                </td>
                                        <td>
                                    <input type="text" 
                                        class="form-control" 
                                        id="end_${i + 1}" 
                                        name="end[]" 
                                        ${i !== nonReadonlyRowIndex ? 'readonly' : 'onfocusout="_changeVal(' + i + ', $(this).val())"'}
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                        maxlength="4" 
                                        value="${e.end}" 
                                            />
                                </td>
                                `;
                careNameList.forEach((careName, index) => {
                    BuildHtm += `<td>
                    <input type="text" 
                           class="form-control" 
                           id="${careName}_${i + 1}" 
                           name="${careName}[]"  
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           maxlength="10" 
                           value="${(e[careName] != 0) ? e[careName] : 5}" />
                </td>`;
                });
                BuildHtm += `<td>`;
                if (i !== fareList.length - 1) {
                    BuildHtm += i === fareList.length - 2
                        ? `<a class="link-offset-2 link-underline link-underline-opacity-0" style="cursor: pointer;" onclick="_incVal(${i}, true)"><i class="fa fa-plus-circle" style="font-size:20px;color: green;" aria-hidden="true"></i></a>`
                        : ((i !== 0 && i !== fareList.length - 1)) ? `<a class="link-offset-2 link-underline link-underline-opacity-0" style="cursor: pointer;" onclick="_incVal(${i})"><i class="fa fa-minus-circle" style="font-size:20px;color: red;" aria-hidden="true"></i></a>` : ``;
                }
                BuildHtm += `</td>`;
                BuildHtm += `</tr>`;
            });
            BuildHtm += `</tbody>`;
            $(`#data-table_new`).html(BuildHtm);
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }
    
    const buildFareCalHTM = () => {
        try {
            
            const careNameList = JSON.parse(localStorage.getItem('careNameList'));
            const fareList = JSON.parse(localStorage.getItem('fareList'));
            if (!careNameList || !fareList) {
                // console.error('Data not found in localStorage');
                showToast('error', 'Data not found!', 5000);
                return;
            }
            $(`#data-table_one`).html('');
            let BuildHtm = `<thead>
            <tr>
                <th >Check Your Fare Calculation Kms/Miles</th>
                <th scope="col"></th>`;
            careNameList.forEach(careName => {
                BuildHtm += `<th scope="col">${_.capitalize(careName.replace('_', ' '))}</th>`;
            });
            // BuildHtm += `<th scope="col">Action</th>`;
            BuildHtm += `</tr></thead><tbody><tr>
                <td>
                <div class="input-group">
                   <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">Km/Miles</span>
                  </div>
                  <input type="text" 
                       class="form-control" 
                       id="given_fare" 
                       name="given_fare"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice('', 8); calculateFare(this.value);" 
                       maxlength="10"
                       value="0" aria-describedby="basic-addon2"/>
                </div>
                
                   </td><td></td>
            `;
            
            careNameList.forEach(careName => {
                BuildHtm += `<td>
                
                
                    <div class="input-group">
                          <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon3">Fare</span>
                          </div>
 
                           <input type="text" 
                               class="form-control" 
                               id="${careName}_fare" 
                               name="${careName}_fare"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                               maxlength="10" 
                               value="0" disabled readonly aria-describedby="basic-addon3"/>
                               
                    </div>
                
                    
                    
                </td>`;
            });
            
            // const nonReadonlyRowIndex = fareList.length - 2;
            // fareList.forEach((e, i) => {
            //     BuildHtm += `<tr>
            //                             <td>
            //                         <input type="text" 
            //                             class="form-control" 
            //                             id="start_${i + 1}" 
            //                             name="start[]" 
            //                             oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
            //                             maxlength="10" 
            //                             value="${fareList[i - 1] && fareList[i - 1].end ? fareList[i - 1].end : e.start}" 
            //                             readonly />
            //                     </td>
            //                             <td>
            //                         <input type="text" 
            //                             class="form-control" 
            //                             id="end_${i + 1}" 
            //                             name="end[]" 
            //                             ${i !== nonReadonlyRowIndex ? 'readonly' : 'onfocusout="_changeVal(' + i + ', $(this).val())"'}
            //                             oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
            //                             maxlength="4" 
            //                             value="${e.end}" 
            //                                 />
            //                     </td>
            //                     `;
            //     careNameList.forEach((careName, index) => {
            //         BuildHtm += `<td>
            //         <input type="text" 
            //               class="form-control" 
            //               id="${careName}_${i + 1}" 
            //               name="${careName}[]"  
            //               oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
            //               maxlength="10" 
            //               value="${(e[careName] != 0) ? e[careName] : 5}" />
            //     </td>`;
            //     });
            //     BuildHtm += `<td>`;
            //     if (i !== fareList.length - 1) {
            //         BuildHtm += i === fareList.length - 2
            //             ? `<a class="link-offset-2 link-underline link-underline-opacity-0" style="cursor: pointer;" onclick="_incVal(${i}, true)"><i class="fa fa-plus-circle" style="font-size:20px;color: green;" aria-hidden="true"></i></a>`
            //             : ((i !== 0 && i !== fareList.length - 1)) ? `<a class="link-offset-2 link-underline link-underline-opacity-0" style="cursor: pointer;" onclick="_incVal(${i})"><i class="fa fa-minus-circle" style="font-size:20px;color: red;" aria-hidden="true"></i></a>` : ``;
            //     }
            //     BuildHtm += `</td>`;
            //     BuildHtm += `</tr>`;
            // });
            BuildHtm += `</tr></tbody>`;
            $(`#data-table_one`).html(BuildHtm);
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    }
    
    const _incVal = (i, isIncrement = false) => {
        try {
            let fareList = JSON.parse(localStorage.getItem('fareList')) || [];
            let careNameList = JSON.parse(localStorage.getItem('careNameList')) || [];
            // Maximum value for 'end'
            const MAX_END_VALUE = 10000;
            if (isIncrement) {
                const previousRow = fareList[i] || { start: "", end: "" };
                let newEndValue = (previousRow.end + 10) || 0;
                if (newEndValue >= MAX_END_VALUE) {
                    showToast('error', `End value cannot exceed ${MAX_END_VALUE}`, 5000);
                    return;
                }
                const newRow = {
                    start: previousRow.end || "",
                    end: newEndValue,
                };
                careNameList.forEach(careName => {
                    newRow[careName] = previousRow[careName] || "";
                });
                fareList.splice(i + 1, 0, newRow);
                localStorage.setItem('fareList', JSON.stringify(fareList));
                localStorage.setItem('careNameList', JSON.stringify(careNameList));
                i = i + 1;
            } else {
                fareList.splice(i, 1);
                localStorage.setItem('fareList', JSON.stringify(fareList));
                localStorage.setItem('careNameList', JSON.stringify(careNameList));
            }
            buildHTM();
        } catch (e) {
            console.error(`Error: ${e.message}`);
        }
    };
    
    const formData = (formrow) => {
        var allRowData = [];
        $('#' + formrow + ' tr').each(function () {
            var rowData = {};
            $(this).find('input').each(function () {
                var inputName = $(this).attr('name');
                var inputValue = $(this).val();
                if (inputValue === '') {
                    inputValue = '0';
                }
                if (inputName) { // ensure inputName exists
                    rowData[inputName.slice(0, -2)] = inputValue;
                }
            });
            allRowData.push(rowData);
        });
        return allRowData;
    }
    
    (() => {
        $('#update-all-btn').on('click', function (e) {
            e.preventDefault();
            let isValid = true;
            const careNameList = JSON.parse(localStorage.getItem('careNameList')) || [];
            if (!careNameList.includes('end')) {
                careNameList.push('end');
            }
            careNameList.forEach(careName => {
                $(`input[name="${careName}[]"]`).each(function () {
                    const value = $(this).val();
                    if (value === '0') {
                        $(this).addClass('border-danger');
                        isValid = false;
                    } else {
                        $(this).removeClass('border-danger');
                    }
                });
            });
            if (!isValid) {
                showToast('error', 'Please make sure all values are valid and not zero!', 5000);
                return;
            }
    
            // collect data
            var combinedData = formData('carFareForm');
    
            // filter out tariff rows (min_hr, min_km, min_amount, additional_km, additional_hours)
            combinedData = combinedData.filter(row => {
                return !(
                    row.hasOwnProperty('min_hr') ||
                    row.hasOwnProperty('min_km') ||
                    row.hasOwnProperty('min_amount') ||
                    row.hasOwnProperty('additional_km') ||
                    row.hasOwnProperty('additional_hours')
                );
            });
    
            let updates = [];
            formDataObject['data'] = combinedData;
    
            $.ajax({
                url: '{{ env('API_URL') }}updatedynamicfareoverall',
                method: 'POST',
                data: formDataObject,
                success: function (response) {
                    var message = response.message;
                    var status = response.status;
                    if (status == 200) {
                        showToast('success', message, 5000);
                        getFareList();
                        let curr_url = window.location.pathname;
                        if (curr_url == '/create-carfares') {
                            window.location.href = '/general';
                        } else {
                            window.location.reload();
                        }
                    } else {
                        showToast('error', message, 5000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error updating data:', error);
                    showToast('error', 'Failed to update data!', 5000);
                }
            });
        });
    
        let curr_url = window.location.pathname;
        if (curr_url == '/create-carfares') {
            showModal();
        }
        getFareList();
    })();

    function showModal(){
        // $('#car-faremodel').modal('show')
        var myModal = new bootstrap.Modal(document.getElementById('car-faremodel'));
        myModal.show();

    }
    
    function calculateFare(distance){
        let total_dis = distance;
        let careNameList = JSON.parse(localStorage.getItem('careNameList'));
        const fareList = JSON.parse(localStorage.getItem('fareList'));
        
        if (fareList && fareList.length > 0) {
            let lastEndValue = fareList[fareList.length - 1]['end']; 
        
            if (total_dis <= lastEndValue) {
                
                careNameList = Object.values(careNameList).reduce((acc, value) => {
                    acc[value] = 0;
                    return acc;
                }, {});
                
                let total=0;
        
                let netto =0;
            
                let total_cur_val =0;
            
                let nettotal_car_name = 0;
                
                
                $.each(fareList, function(index, value){
                    
                    let start = value.start;
                    let end = value.end;
                    
                    let cur_val =  end - start;
                    
                    if(end >= total_dis){
                        cur_val = total_dis - total_cur_val;
                        
                        $.each(careNameList, function (ind) {
                            careNameList[ind] += cur_val * value[ind];
                        });
                        
                        return false;
                        
                    }else if(end <= total_dis){
                        
                        total_cur_val += end - start;
                        
                        $.each(careNameList, function (ind) {
                            careNameList[ind] += cur_val * value[ind];
                        });
                        
                    }
                })
                
                $.each(careNameList, function(index, item){
                    $('#' + index + '_fare').val(item);
                })
            }else{
                $.each(careNameList, function(index, item){
                    $('#' + item + '_fare').val(0);
                })
                showToast('warning', 'Total Distance Reached!', 2000);
            }
        }
        
    }
    
</script>