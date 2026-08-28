<script>
    $(function () {
            $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

    });
    
    
$(document).ready(function() {
    showlist(); // Call the showlist function when the document is fully loaded
    
    
     $('#update-all-btn').on('click', function() {
        let updates = [];

        // Iterate over each row in the table
        $('#table-body tr').each(function() {
            let row = $(this);
            let id = row.find('.update-btn').data('id'); // Get the ID from the button
            let data = { id: id }; // Initialize the data object with the ID

            // Iterate over each input in the row
            row.find('input.editable-input').each(function() {
                let column = $(this).data('column'); // Get the column name
                let value = $(this).val(); // Get the value

                // Only add to data if the value is not empty
                if (value !== '') {
                    data[column] = value;
                }
            });

            updates.push(data);
             formDataObject['data'] = updates;
            // Add this row's data to the updates array
        });

        // Make AJAX call to update all data
        $.ajax({
            url: '{{env('API_URL')}}updatedynamicfareoverall', // Replace with your actual update endpoint
            method: 'POST',
            data: formDataObject,
            success: function(response) {
                
                var message = response.message;
                var status = response.status;
                if (status == 200) {
                    swalalertsuccess(message);
                    showlist();
                } else {
                    swalalertsuccess(message);
                    showlist();
                }
                
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error('Error updating data:', error);
                alert('Failed to update data.');
            }
        });
    });
});
// Ajax for Save and Update
$('.updateFare').click(function (e) {
    e.preventDefault();
    let id = $(this).data("id");

    $.ajax({
        data: $('#car_fare_'+id).serialize(),
        url: "{{ route('carfare.store') }}",
        type: "POST",
        dataType: 'json',
        success: function (response) {
            if(response.isUpdated){
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Car Fare',
                    text: 'Car fares updated successfully',
                    showConfirmButton: false,
                    timer: 2000,
                }).then((willUpdate) =>{
                    if(willUpdate.isConfirmed){
                        location.reload();
                    }
                })
            } else {
                Swal.fire("Error", "Carfare not updated", "error");
            }				

        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
    
});


            
//showlist
// function showlist() {
//     var formDataObject = {};
//     formDataObject['token'] = getCookie('d_token');
//     formDataObject['device_id'] = 0;

//     // AJAX call to get data
//     $.ajax({
//         url: '{{env('API_URL')}}dynamiccarfare',
//         method: 'POST',
//         data: formDataObject,
//         success: function(response) {
//             // Clear the existing table header and body
//             $('#table-header').empty();
//             $('#table-body').empty();

//             // Assuming response.data contains an array of objects with dynamic keys
//             if (response.data && response.data.length > 0) {
//                 // Get the keys (columns) from the first object and filter out unwanted columns
//                 var columns = Object.keys(response.data[0]).filter(function(column) {
//                     return !['created_at', 'updated_at', 'deleted_at', 'distance','id','end','rate'].includes(column);
//                 });

//                 // Dynamically create table headers
//                 columns.push('Actions'); // Add Actions column for the Update button
//                 columns.forEach(function(column) {
//                     $('#table-header').append('<th>' + column.replace(/_/g, ' ') + '</th>');
//                 });

//                 // Dynamically add rows to the table body
//                 response.data.forEach(function(row) {
//                     var rowHtml = '<tr>';
//                     columns.forEach(function(column) {
//                         if (column === 'Actions') {
//                             // Add Update button to the last column
//                             rowHtml += '<td><button class="update-btn" data-id="' + row.id + '">Update</button></td>';
//                         } else if (column === 'start' || column === 'end') {
//                             // Combine 'start' and 'end' into one non-editable field
//                             if (row.start && row.end) {
//                                 rowHtml += '<td>' + row.start + ' - ' + row.end + '</td>';
//                             } else {
//                                 rowHtml += '<td>N/A</td>';
//                             }
//                         } else {
//                             // Add input field for editable columns
//                             rowHtml += '<td><input type="text" class="editable-input" value="' + row[column] + '" data-id="' + row.id + '" data-column="' + column + '"></td>';
//                         }
//                     });
//                     rowHtml += '</tr>';
//                     $('#table-body').append(rowHtml);
//                 });

//                 // Initialize DataTable
//                 $('#data-table').DataTable();

//                 // Attach event handler to Update buttons
//                 $('.update-btn').on('click', function() {
//                     var rowId = $(this).data('id');
//                     var updatedData = {};

//                     // Collect values from the input fields
//                     $(this).closest('tr').find('.editable-input').each(function() {
//                         var columnName = $(this).data('column');
//                         updatedData[columnName] = $(this).val();
//                     });

//                     // Perform update operation
//                     $.ajax({
//                         url: '{{env('API_URL')}}updatedynamicfare', // Update URL
//                         method: 'POST',
//                         data: {
//                             id: rowId,
//                             ...updatedData,
//                             token: getCookie('d_token')
//                         },
//                         success: function(response) {
//                         var message =response.message;   
//                         if(message="Update successful"){
//                           swalalertsuccess(message);  
//                             showlist();
//                         }else{
//                           swalalertsuccess(message);
//                           showlist();
//                         }
//                         //console.log(response.message);
                         
//                         },
//                         error: function(error) {
//                             console.error('Error updating data:', error);
//                         }
//                     });
//                 });
//             }
//         },
//         error: function(error) {
//             console.error('Error fetching data:', error);
//         }
//     });
// }


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
            // Clear the existing table header and body
            $('#table-header').empty();
            $('#table-body').empty();

            // Check if response data is present
            if (response.data && response.data.length > 0) {
                // Get the keys (columns) from the first object and filter out unwanted columns
                var columns = Object.keys(response.data[0]).filter(function(column) {
                    return !['created_at', 'updated_at', 'deleted_at', 'distance','end', 'rate'].includes(column);
                });

                // Dynamically create table headers
                columns.push('Actions'); // Add Actions column for the Update button
                columns.forEach(function(column) {
                    $('#table-header').append('<th>' + column.replace(/_/g, ' ') + '</th>');
                });

                // Dynamically add rows to the table body
                response.data.forEach(function(row) {
                    var rowHtml = '<tr>';
                    columns.forEach(function(column) {
                        if (column === 'Actions') {
                            // Add Update button to the last column
                            rowHtml += '<td><button class="btn btn-primary update-btn" data-id="' + row.id + '">Update</button></td>';
                        } else if (column === 'start' || column === 'end') {
                            // Combine 'start' and 'end' into one non-editable field
                            var start = row.start || 'N/A';
                            var end = row.end || 'N/A';
                            rowHtml += '<td>' + start + ' - ' + end + '</td>';
                            
                        }else if(column === 'id'){ 
                        
                            rowHtml += '<td>' +row.id +'</td>';
                        }else {
                            // Add input field for editable columns
                            rowHtml += '<td><input type="text" class="editable-input" value="' + (row[column] || '') + '" data-id="' + row.id + '" data-column="' + column + '"></td>';
                        }
                    });
                    rowHtml += '</tr>'; 
                    console.log(rowHtml);
                    $('#table-body').append(rowHtml);
                });

                // Initialize DataTable
                $('#data-table').DataTable();

                // Attach event handler to Update buttons
                $('.update-btn').on('click', function() {
                    var rowId = $(this).data('id');
                    var updatedData = {};

                    // Collect values from the input fields
                    $(this).closest('tr').find('.editable-input').each(function() {
                        var columnName = $(this).data('column');
                        updatedData[columnName] = $(this).val();
                    });

                    // Perform update operation
                    $.ajax({
                        url: '{{env('API_URL')}}updatedynamicfare', // Update URL
                        method: 'POST',
                        data: {
                            id: rowId,
                            ...updatedData,
                            token: getCookie('d_token')
                        },
                        success: function(response) {
                            var message = response.message;
                            if (message === "Update successful") {
                                swalalertsuccess(message);
                                showlist();
                            } else {
                                swalalertsuccess(message);
                                showlist();
                            }
                        },
                        error: function(error) {
                            console.error('Error updating data:', error);
                        }
                    });
                });
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
    
    
   
 
    
}











         
            
</script>