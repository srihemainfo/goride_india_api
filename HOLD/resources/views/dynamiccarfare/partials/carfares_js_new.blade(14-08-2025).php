
<script>
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
                rowData[inputName.slice(0, -2)] = inputValue;
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
            var combinedData = formData('carFareForm');
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
                        // swalalertsuccess(message);
                        showToast('success', message, 5000);
                        getFareList();
                        let curr_url = window.location.pathname;
                        if (curr_url == '/create-carfares') {
                            window.location.href = '/general';
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // swalalerterror(message);
                        showToast('error', message, 5000);
                        // showlist();
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.error('Error updating data:', error);
                    // alert('Failed to update data.');
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
            let lastEndValue = fareList[fareList.length - 1]['end']; // Get the 'end' value of the last item
        
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