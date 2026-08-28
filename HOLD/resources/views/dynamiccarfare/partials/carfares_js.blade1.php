<script>
    $(document).ready(function() {

        showlist();
        $('#car-faremodel').modal('show')

    });

    $(document).on('input', '.editable-input', function() {

        validateInputInteger(this);

    });

    function validateInputInteger(input) {

        input.value = input.value

            .replace(/[^0-9.]/g, '')

            .replace(/(\..*)\./g, '$1');

    }
</script>

<script>
    let timeout;

    var currentvalue = 2;

    $(document).ready(function() {

        $.ajaxSetup({

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function formData(formrow) {
            var allRowData = [];

            $('#' + formrow + ' tr').each(function() {
                var rowData = {};
                $(this).find('input').each(function() {

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
        showlist();
        $('#update-all-btn').on('click', function(e) {
            e.preventDefault();
            var allRowData = finalRowData = [];
            allRowData = formData('table-body');
            finalRowData = formData('table-second-body')
            var combinedData = allRowData.concat(finalRowData);
            let updates = [];
            formDataObject['data'] = combinedData;
            $.ajax({
                url: '{{env('API_URL')}}updatedynamicfareoverall',
                method: 'POST',
                data: formDataObject,
                success: function(response) {
                    var message = response.message;
                    var status = response.status;
                    if (status == 200) {
                        swalalertsuccess(message);
                        showlist();
                    } else {
                        swalalerterror(message);

                    }
                    let curr_url = window.location.pathname;
                    if (curr_url == '/create-carfares') {
                        window.location.href = '/booking/create';
                    } else {
                        window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert('Failed to update data.');
                }
            });
        });
    });
    // Ajax for Save and Update
    $('.updateFare').click(function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        $.ajax({
            data: $('#car_fare_' + id).serialize(),
            url: "{{ route('carfare.store') }}",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                if (response.isUpdated) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Car Fare',
                        text: 'Car fares updated successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then((willUpdate) => {
                        if (willUpdate.isConfirmed) {
                            location.reload();
                        }
                    })
                } else {
                    Swal.fire("Error", "Carfare not updated", "error");
                }
            },
            error: function(data) {}

        });

    });

    function showlist() {
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;

        // AJAX call to get data
        $.ajax({
            url: '{{env('API_URL')}}dynamiccarfare',
            method: 'POST',
            data: formDataObject,
            success: function(response) {
                $('#header_container, #table-body').empty();

                if (response.data) {
                    // Update table header and body
                    if (response.data.head) {
                        $('#header_container').html(response.data.head);
                    }

                    if (response.data.firsttbody) {
                        $('#table-body').html(response.data.firsttbody);
                    }

                    var currentvalue = response.data.start_value;
                    var final_value = response.data.final_value;

                    // Set initial start value
                    $('#start').val(final_value);

                    // Event handler for adding a new row
                    $(document).on('click', '#addButton', function() {
                        var start = $('#start');
                        var end = $('#end');
                        var startValue = parseFloat(start.val());
                        var endValue = parseFloat(end.val());

                        // // Make sure that if values are not valid, they are set to 0
                        // if (isNaN(startValue) || isNaN(endValue)) {
                        //     startValue = 10; // Default value for start
                        //     endValue = 20; // Default value for end
                        // }

                        // Validating the inputs
                        if (startValue >= endValue) {
                            swalalerterror(
                            'Start fare cannot be greater than or equal to end fare');
                            return;
                        }

                        // Remove button logic (changed to '-' symbol)
                        var removeButton =
                            `<td><button class='removeButton btn btn-danger'>-</button></td>`;

                        // Append new row
                        var appendTbody =
                            `
                        <tr id='current_${currentvalue}'>
                            <td><span id="current_text_start_${currentvalue}">${startValue}</span><input type='hidden' name='start[]' value="${startValue}" class='form-control' id="start_${currentvalue}"></td>
                            <td><span id="current_text_end_${currentvalue}" style='display:none;'>${endValue}</span><input id="end_${currentvalue}" type='text' class='form-control' name='end[]' value="${endValue}" oninput='validateInputInteger(this)'></td>`;

                        // Append car name inputs
                        var carNames = response.data.carName;
                        for (let key in carNames) {
                            if (carNames.hasOwnProperty(key)) {
                                var carName = carNames[key];
                                appendTbody +=
                                    `<td><input type='text' class='editable-input form-control' name='${carName}[]' value='' oninput='validateInputInteger(this)'></td>`;
                            }
                        }

                        appendTbody += removeButton + '</tr>';

                        // Append the new row to the table
                        $('#table-body').append(appendTbody);

                        // Update start value for the next row based on the first row value (10)
                        start.val(10); // Keep the start value fixed (10) for subsequent rows
                        end.val(''); // Reset end field for next input

                        currentvalue++; // Increment the row id for next row
                    });

                    // Event handler for removing a row (button is now '-')
                    $(document).on('click', '.removeButton', function() {
                        var closestTr = $(this).closest('tr');
                        var previousRow = closestTr.prev('tr');

                        // Update the start value of the next row
                        if (previousRow.length) {
                            var previousStartValue = previousRow.find('input[name="start[]"]')
                        .val();
                            var nextTag = closestTr.next('tr');
                            nextTag.find('input[name="start[]"]').val(previousStartValue);
                            nextTag.find('span[id^="current_text_start_"]').text(
                            previousStartValue);
                        }

                        closestTr.remove(); // Remove the current row

                        // Update last row values and buttons
                        updateLastRow();
                    });

                    // Function to handle last row behavior
                    function updateLastRow() {
                        var lastRow = $('#table-body tr[id^=current_]').last();
                        if (lastRow.length) {
                            var lastRowEnd = lastRow.find("input[name='end[]']");
                            lastRowEnd.attr('type', 'text');
                            var lastRowEndValue = lastRowEnd.val();
                            $('#last_row_start').html(lastRowEndValue);
                        }
                    }

                    // Event handler for end value input changes
                    $(document).on('keyup', '[id^=end_]', function() {
                        var currentEnd = $(this).val();
                        var previousRow = $(this).closest('tr').prev('tr');

                        if (previousRow.length) {
                            var previousEnd = previousRow.find('input[name="end[]"]').val();
                            if (parseInt(previousEnd) > parseInt(currentEnd)) {
                                alert('End value cannot be less than previous row end value');
                                $(this).val(''); // Reset the current input
                            }
                        }

                        // Update last row values
                        updateLastRow();
                    });
                }
            },
            error: function(error) {
                //console.error('Error fetching data:', error);
            }
        });
    }
</script>
