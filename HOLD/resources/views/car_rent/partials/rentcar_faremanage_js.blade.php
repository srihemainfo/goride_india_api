<script>
    function showlist() {
        
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;

        $.ajax({
            data: formDataObject,
            url: "{{ env('API_URL') }}rentcar_faremanage_list",
            type: "POST",
            dataType: 'json',
            success: function (response) {
                if (response.status === 200) {
                    let tableBody = '';
        
                    response.data.forEach(function (item, index) {
                        tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.car_name ?? ''}</td>
                                <td>
                                    <input type="number" class="form-control" name="hourly[]" value="${item.hourly ?? ''}" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="rate[]" value="${item.rate ?? ''}" />
                                </td>
                                <td>
                                    <input type="text" name="driver_charge[]" class="form-control" value="${item.driver_charge ?? ''}">
                                    <input type="hidden" name="id[]" value="${item.id}">
                                </td>
                            </tr>
                        `;
                    });
        
                    $('#data-table_new tbody').html(tableBody);
                } else {
                    console.warn('Unexpected response', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });

    }

    // Call showlist on page load
    $(document).ready(function () {
        showlist();
    });
    
    $('#update-all-btn').on('click', function () {
            let formData = {
                token: getCookie('d_token'),
                device_id: 0,
                id: [],
                hourly: [],
                rate: [],
                driver_charge: []
            };
        
            $('#data-table_new tbody tr').each(function () {
                formData.id.push($(this).find('input[name="id[]"]').val());
                formData.hourly.push($(this).find('input[name="hourly[]"]').val());
                formData.rate.push($(this).find('input[name="rate[]"]').val());
                formData.driver_charge.push($(this).find('input[name="driver_charge[]"]').val());
            });
            
            console.log(formData);
        
            $.ajax({
                url: "{{ env('API_URL') }}rentcar_faremanage_update",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function (response) {
                    var message = response.message;
                    if (response.status === 200) {
                        // alert('Updated Successfully!');
                        showToast('success', message, 3000);
                        setTimeout(function () {
                            window.location.reload();
                        }, 3000);
                    } else {
                        // alert('Error: ' + response.error);
                        showToast('error', message, 4000);
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    alert('Update failed');
                }
            });
        });

</script>
