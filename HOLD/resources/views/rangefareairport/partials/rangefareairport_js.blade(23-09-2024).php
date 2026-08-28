<script>

function edit_employee(id){
        //alert('editfare');
        const url = 'editairportRangeFare';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fare_id'] = id;
          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
          console.log(response.data);
         if(response['status'] == 200){ 
            AssignValues(response.data);
            $('#edit_cus_form-modal').modal('show')
             }
         if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
    }
    
function AssignValues(data){
        // console.log(data[0].end);
        $('#edit_fare_id').val(data[0].id);
        $('#edit_user_id').val(data[0].partner_id);
        $('#edit_vehicle_id').val(data[0].veh_id);
        $('#veh_id12').val(data[0].veh_id);
        $('#edit_start').val(data[0].start);
        $('#edit_end').val(data[0].end); 
        $('#edit_fare').val(data[0].fare);
        $('#edit_airportfare').val(data[0].from_airport);
        $('#edit_airportfareto').val(data[0].to_airport);
        $('#edit_airporthour_fare').val(data[0].hour_fare);
       
    }    
    
function showlist() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;

    var existingTable = $('#emp-table').DataTable();
    if (existingTable) {
        existingTable.destroy();
    }

    new DataTable('#emp-table', {
        ajax: {
            url: '{{env('API_URL')}}airportfare',
            method: 'POST',
            dataSrc: "data",
            data: formDataObject,
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'vehicle_name' },
            { data: 'from_airport', 
            render: function (data, type, row) {
                if (data === null || data.trim() === '') {
                    return ' - ';
                } else {
                    return data;
                }
            }    
            },
            { data: 'to_airport',
                        render: function (data, type, row) {
                if (data === null || data.trim() === '') {
                    return ' - ';
                } else {
                    return data;
                }
            } 
            },
            { data: 'by_hour',
            
                render: function (data, type, row) {
                if (data === null || data.trim() === '') {
                    return ' - ';
                } else {
                    return data;
                }
            } 
            
            },
            { data: 'hour_fare',
            render: function (data, type, row) {
                if (data === null || data.trim() === '') {
                    return ' - ';
                } else {
                    return data;
                }
            } 
            },
            { data: 'start',
            render: function (data, type, row) {
                if (data === null) {
                    return ' - ';
                } else {
                    return data;
                }
            } 
            },
            { data: 'end',
            render: function (data, type, row) {
                if (data === null) {
                    return ' - ';
                } else {
                    return data;
                }
            } 
            },
            { data: 'fare',
                        render: function (data, type, row) {
                if (data === null || data.trim() === '') {
                    return ' - ';
                } else {
                    return data;
                }
            }
            
            },
            // { data: 'veh_id' },
            {
                data: 'upload_photo',
                render: function (data, type, row) {
                    var list = '';
                    file(row, null, function(imageData, index) {
                        list += `<div class="img-sec"><img class="img-flx" src="data:image/png;base64,${imageData}" alt="Displayed Image" style="max-width: 80px; max-height: 200px;"></div>`;
                        $('#imageContainer_' + row.id).html(list);
                    });
                    return `<div id="imageContainer_${row.id}" class="listoffleets"></div>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<span style="padding: 8px;"><i class="fa-regular fa-pen-to-square" style="background: green;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;margin: 0px 0px 6px 0;" onclick="edit_employee(${row.id})"></i></span><span style="padding: 8px;"><i class="fa-solid fa-trash" style="background: red;color: #fff;padding: 6px 7px 6px 7px;border-radius: 6px;" onclick="delete_employee(${row.id})"></i></span>`;
                }
            }
        ],
    });
}

function file(data, index, callback) {
    var settings = {
        "url": "{{env('API_URL')}}showfile",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify({
            "image": data.upload_photo
        }),
    };

    $.ajax(settings).done(function (response) {
        if (callback && typeof callback === "function") {
            callback(response, index);
        }
    });
}

$(function(){
        showlist()
    })
    
$('#add_saveBtn').on('click', function(){
          const url = 'airporthourstore';
        var formdata = $('#add_employeeFormhour').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
          console.log(response);
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      })
    
$('#add_saveBtnairport').on('click', function(){
  //  alert("from_airport");
          const url = 'airportfareupdate';
        var formdata = $('#add_employeeFormairport').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
          console.log(response.data);
        //   alert(response);
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      })
      
$('#add_saveBtnairport_to').on('click', function(){
          const url = 'airportfareupdateto';
        var formdata = $('#add_employeeFormairportto').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
          console.log(response);
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      })
      
$('#edit_saveBtn').on('click', function(){
   // alert('king');
          const url = 'update_rfareairport';
        var formdata = $('#edit_employeeFormairport').serialize();
        console.log(formdata);
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
        //   console.log(response);
         if(response['status'] == 200){
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    location.reload()
                });
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      })
      
//edit range fare    
function delete_employee(id){
            const url = 'destroyairportRangeFare';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fare_id'] = id;
          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
        Swal.fire({
             title: 'Are you sure?',
             text: 'You won\'t be able to revert this!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes, delete it!',
             cancelButtonText: 'No, cancel!',
           }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax(settings).done(function (response) {
                     
                   if(response['status'] == 200){
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              location.reload()
                          });
                       }
                   if(response['status'] == 400){
                       warningClick('Error',response['message'],"danger")
                   }
                   if(response['status'] == 500){
                      warningClick('Error',response['error'],"danger")
                   }
                   if(response['status'] == 401){
                      unauth()
                   }
                  });
               
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
             }
         });
        }
        
$('#reset_emp_filter').on('click', function(){
          $("#emp_filter")[0].reset();
          showlist()
      })

function FleetList() {
    const url = 'vehichlelist';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        url: '{{env('API_URL')}}' + url,
        method: 'POST',
        timeout: 0,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(formDataObject),
    };

    // Append the default option first
    $('#veh_id').append('<option value="">Select Vehicle</option>');

    $.ajax(settings)
        .done(function(response) {
            if (response && response.data.length > 0) {
                response.data.forEach(function(vehicle) {
                    var option = new Option(vehicle.name, vehicle.id);
                    $(option).attr('id', 'option_' + vehicle.id); // Setting the id of the option
                    $('#veh_id').append(option);
                });
            } else {
                console.error('No data received from the server.');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed: ' + textStatus, errorThrown);
        });
}
$(document).ready(function() {
    FleetList();
});

function FleetList1() {
    const url = 'vehichlelist';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        url: '{{env('API_URL')}}' + url,
        method: 'POST',
        timeout: 0,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(formDataObject),
    };

    // Append the default option first
    $('#veh').append('<option value="">Select Vehicle</option>');

    $.ajax(settings)
        .done(function(response) {
            if (response && response.data.length > 0) {
                response.data.forEach(function(vehicle) {
                    var option = new Option(vehicle.name, vehicle.id);
                    $(option).attr('id', 'option_' + vehicle.id); // Setting the id of the option
                    $('#veh').append(option);
                });
            } else {
                console.error('No data received from the server.');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed: ' + textStatus, errorThrown);
        });
}

$(document).ready(function() {
    FleetList1();
});

$(document).ready(function () {
    var options = {
      url: function (phrase) {
        // Use the Laravel route() function to generate the URL
        return (
          "{{env('API_URL')}}airport_autocomplete?phrase=" +
          phrase
        );
      },
      getValue: "text",
    };

    // Initialize EasyAutocomplete for the second input field
    $("#from_airport").easyAutocomplete(options);
    $("#to_airport").easyAutocomplete(options);
    $("#edit_airportfare").easyAutocomplete(options);
    $("#edit_airportfareto").easyAutocomplete(options);
  });
 
function FleetList12() {
    const url = 'vehichlelist';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        url: '{{env('API_URL')}}' + url,
        method: 'POST',
        timeout: 0,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(formDataObject),
    };

    // Append the default option first
    $('#veh_id12').append('<option value="">Select Vehicle</option>');

    $.ajax(settings)
        .done(function(response) {
            if (response && response.data.length > 0) {
                response.data.forEach(function(vehicle) {
                    var option = new Option(vehicle.name, vehicle.id);
                    $(option).attr('id', 'option_' + vehicle.id); // Setting the id of the option
                    $('#veh_id12').append(option);
                });
            } else {
                console.error('No data received from the server.');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed: ' + textStatus, errorThrown);
        });
}
$(document).ready(function() {
    FleetList12();
});
  
function FleetList123() {
    const url = 'vehichlelist';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        url: '{{env('API_URL')}}' + url,
        method: 'POST',
        timeout: 0,
        headers: {
            'Content-Type': 'application/json'
        },
        data: JSON.stringify(formDataObject),
    };

    // Append the default option first
    $('#veh_id_to').append('<option value="">Select Vehicle</option>');

    $.ajax(settings)
        .done(function(response) {
            if (response && response.data.length > 0) {
                response.data.forEach(function(vehicle) {
                    var option = new Option(vehicle.name, vehicle.id);
                    $(option).attr('id', 'option_' + vehicle.id); // Setting the id of the option
                    $('#veh_id_to').append(option);
                });
            } else {
                console.error('No data received from the server.');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX request failed: ' + textStatus, errorThrown);
        });
}
$(document).ready(function() {
    FleetList123();
});

</script>
