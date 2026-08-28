<script>

        let timeout;    
        var currentvalue = 2;
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
            if (response.data ) {
                      
        
                if(response.data.head){
                    $('#header_container').html(response.data.head)
                }
                 if(response.data.firsttbody){
                    $('#table-body').html(response.data.firsttbody)
                }
                 if(response.data.secondtbody){
                    $('#table-second-body').html(response.data.secondtbody)
                }
                
                currentvalue = response.data.start_value;
                var final_value = response.data.final_value;
                
                $('#start').val(final_value);
                        
            $(document).on('click','#addButton', function(){
        
                var start,end ;
                start = $('#start');
                end = $('#end');
                var startValue = parseFloat(start.val());
                var endvalue = parseFloat(end.val());
                // console.log(startValue,endvalue);
                
                if( (isNaN(startValue) && isNaN(endvalue) ) || isNaN(startValue) ){
                    alert('please fill below selected end fare');
                    return;
                    //  swalalerterror('Please select the end value')
                   
                   
                }else if(isNaN(endvalue)){
                    alert('please fill start and  end fare');
                    return;
                }

            
                if(startValue > endvalue ){
                    alert('start fare not higher than end');
                    // swalalerterror('start value not higher than end')
                    return;
                   
                }else if(startValue ==endvalue ){
                    alert('Start and end Not same value allowed');
                    // swalalerterror('Start and end Not same value allowed')
                    return;
                    
            
                }

                var allowedValue = $('#finalValue').data('finalvalue');


                 if(endvalue >= allowedValue){
                    alert('Limited value already set');
                    return;
                 }


                var  removeButton= '<th></th>' ;
                if(currentvalue != 2){
                     removeButton = `<th><button class='removeButton'>RemoveRow</button> </th>`;
                }
                var appendtbody = '' ;
                 appendtbody += `
                    <tr id='current_${currentvalue}'>
                        <td> <span id="current_text_start_${currentvalue}">${startValue}</span> <input type='hidden' data-start='${startValue}'  id="start_${currentvalue}" name='start[]' value="${startValue}"></th>
                        <td><span id="current_text_end_${currentvalue}" style='display:none;'>${endvalue}</span> <input id="end_${currentvalue}" type='text' name='end[]' value="${endvalue}"></th>`;
                    
                    var carNames = response.data.carName;
                    
                    for (let key in carNames) {
                            if (carNames.hasOwnProperty(key)) {
                               var carName = carNames[key];
                                
                                appendtbody += `<td><input type='text' class='editable-input' name='${carName}[]'  value=''  data-id='".$single_row->id."' data-column='${carName}'></td>` ;
                               
                            }
                    }
                    
                    // console.log(typeof carNames)
                    
                    // carNames.forEach(function(carName, index){
                        
                    //     appendtbody += `<td><input type='text' class='editable-input' name='${carName}[]'  value=''  data-id='".$single_row->id."' data-column='${carName}'></td>` ;
                  
                    // })
                    
                    appendtbody +=  removeButton + '</tr>';
                    
                    $("#table-body").append(appendtbody);
                //     console.log(appendtbody);
                    
                
                //  var test = $("#table-body").append(`
                //     <tr id='current_${currentvalue}'>
                //         <th> <span id="current_text_start_${currentvalue}">${startValue}</span> <input type='hidden' data-start='${startValue}'  id="start_${currentvalue}" name='start[]' value="${startValue}"></th>
                //         <th><span id="current_text_end_${currentvalue}" style='display:none;'>${endvalue}</span> <input id="end_${currentvalue}" type='text' name='end[]' value="${endvalue}"></th>
                //         <th><input type='text' name[]='saloon'> </th>
                //         ${removeButton}
                //     </tr>
                //  `);


             
             $('#last_row_start').html(endvalue);
        

            if(currentvalue != 2 ){
                oldvalue = currentvalue - 1;
                var siblings = $(`#current_${currentvalue}`).siblings('tr[id^=current_]');
                siblings.find("input[name='end[]']").attr("type","hidden"); 
                siblings.find("span[id^='current_text_end_']").show(); 

            }
            start.val(endvalue);
            end.val('');
            currentvalue++;
            
        
    })
    
            $(document).on('click','.removeButton', function(){
                
                // console.log($(this).html());
          
            var start = $('#start');
            var end = $('#end');
            // console.log($(this).closest('tr'));
            
            var startValue = $(this).closest('tr').find("input[name='start[]']").val();
            var endvalue  = $(this).closest('tr').find("input[name='end[]']").val();
            if(startValue == '' ||  endvalue == ''){
                    alert('first fill the End value first')
            }
            // console.log($(this).closest('tr'));
        //   var $position =  $(this).closest('tr').attr('id').slice(-1);
           
           
            // if($position == 1){
            //     start.val(0)
            //     end.val(endvalue)
                
            // }
            
            // else{

                var closestTr = $(this).closest('tr'); 
                // console.log(closestTr);
                var previousRow = closestTr.prev('tr'); 
                // console.log(closestTr.html(),previousRow.html(), previousRow.length);
                // console.log( closestTr.html());
                if (previousRow.length) {
                    var previousStartValue = previousRow.find('input[name="start[]"]');
                    var previousEndValue = previousRow.find('input[name="end[]"]');
                    var previousStartText = previousRow.find('span[id^="current_text_start_"]');
                    var previousEndText = previousRow.find('span[id^="current_text_end_"]');
                    
                    
                    var nextTag = closestTr.next('tr')
                    var nextTagStartValue = nextTag.find('input[name="start[]"]');
                    var nextTagStartText = nextTag.find('span[id^="current_text_start_"]');
                    var nextTagEndValue = nextTag.find('input[name="end[]"]');
                    nextTagStartValue.val(startValue);
                    nextTagStartText.text(startValue);
                    
                    // console.log(previousEndValue.val());
                    
                    // var previousEndValues = previousEndValue.val();

                    // previousEndValue.attr('type','hidden').val(endvalue)
                    // previousEndText.text(endvalue)
                    $(this).closest('tr').remove();  
                }
 

                var rowCount = $('#table-body tr[id^=current_]').length;
                    if(rowCount != 1  ){
                        var lastRow = $('#table-body tr[id^=current_]').last();
                        var lastRowEnd =  lastRow.find("input[name='end[]']") ;
                        lastRowEnd.attr('type','text');
                        var endRowValue = lastRowEnd.val();
                        $('#last_row_start').html(endRowValue);
                        lastRow.find("span[id^='current_text_end_']").hide();

                    }
            // }

        });

            $(document).on('keyup','[id^=end_]',function(){

            var currentId =$(this).attr('id');

            var closestTr = $(this).closest('tr'); 
            var currentEnd = closestTr.find('input[name="end[]"]');
            var previousRow = closestTr.prev('tr');
            var previousStartValue = previousRow.find('input[name="start[]"]');
            var previousEnd = previousRow.find('input[name="end[]"]');
            var previousStartText = previousRow.find('span[id^="current_text_start_"]');
            var previousEndText = previousRow.find('span[id^="current_text_end_"]');
            var allowedValue = $('#finalValue').data('finalvalue');
            
            var addButton = $('#addButton');
            var removeButton = $('.removeButton');

            addButton.attr('disabled', 'disabled');
            removeButton.attr('disabled', 'disabled')
            

            
            $('#end').attr('disabled', 'disabled')
            clearTimeout(timeout);
            timeout =   setTimeout(function() {
                 var currentEndValue = currentEnd.val();
                 var previousEndValue = previousEnd.val();
                 
                //  console.log(parseInt(currentEndValue) >= parseInt(allowedValue), currentEndValue, allowedValue );
        
                if(parseInt(previousEndValue) > parseInt(currentEndValue) ){
                    alert('not allowed previous value equal or low');
                    currentEnd.val('')
                    $('#start').val('')
                    return

                }else if(parseInt(currentEndValue) >= parseInt(allowedValue)){
                    alert('Limited value already set');
                    currentEnd.val('')
                    return;

                }else{
                    if(currentEndValue){
                        $('#start').val(currentEndValue)
                        previousEndText.val(currentEndValue);
                        $('#last_row_start').html(currentEndValue);
                    }else{
                        $('#start').val('')
                    }
                    $('#end').removeAttr('disabled')
                     removeButton.removeAttr('disabled');
                     addButton.removeAttr('disabled');
                }
         }, 6000);


        })

          
                        
                        
                
                
                // // Get the keys (columns) from the first object and filter out unwanted columns
                // var columns = Object.keys(response.data[0]).filter(function(column) {
                //     return !['created_at', 'updated_at', 'deleted_at', 'distance','end', 'rate'].includes(column);
                // });

                // Dynamically create table headers
                // columns.push('Actions'); // Add Actions column for the Update button
                // columns.forEach(function(column) {
                //     $('#table-header').append('<th>' + column.replace(/_/g, ' ') + '</th>');
                // });

                // Dynamically add rows to the table body
                // response.data.forEach(function(row) {
                //     var rowHtml = '<tr>';
                //     columns.forEach(function(column) {
                //         if (column === 'Actions') {
                //             // Add Update button to the last column
                //             rowHtml += '<td><button class="btn btn-primary update-btn" data-id="' + row.id + '">Update</button></td>';
                //         } else if (column === 'start' || column === 'end') {
                //             // Combine 'start' and 'end' into one non-editable field
                //             var start = row.start || 'N/A';
                //             var end = row.end || 'N/A';
                //             rowHtml += '<td>' + start + ' - ' + end + '</td>';
                            
                //         }else if(column === 'id'){ 
                        
                //             rowHtml += '<td>' +row.id +'</td>';
                //         }else {
                //             // Add input field for editable columns
                //             rowHtml += '<td><input type="text" class="editable-input" value="' + (row[column] || '') + '" data-id="' + row.id + '" data-column="' + column + '"></td>';
                //         }
                //     });
                //     rowHtml += '</tr>'; 
                //     console.log(rowHtml);
                //     $('#table-body').append(rowHtml);
                // });

                // Initialize DataTable
                // $('#data-table').DataTable();

                // Attach event handler to Update buttons
                // $('.update-btn').on('click', function() {
                //     var rowId = $(this).data('id');
                //     var updatedData = {};

                //     // Collect values from the input fields
                //     $(this).closest('tr').find('.editable-input').each(function() {
                //         var columnName = $(this).data('column');
                //         updatedData[columnName] = $(this).val();
                //     });

                //     // Perform update operation
                //     $.ajax({
                //         url: '{{env('API_URL')}}updatedynamicfare', // Update URL
                //         method: 'POST',
                //         data: {
                //             id: rowId,
                //             ...updatedData,
                //             token: getCookie('d_token')
                //         },
                //         success: function(response) {
                //             var message = response.message;
                //             if (message === "Update successful") {
                //                 swalalertsuccess(message);
                //                 showlist();
                //             } else {
                //                 swalalertsuccess(message);
                //                 showlist();
                //             }
                //         },
                //         error: function(error) {
                //             console.error('Error updating data:', error);
                //         }
                //     });
                // });
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
    
    
   
 
    
}











         
            
</script>