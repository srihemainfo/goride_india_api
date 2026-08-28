<script>
$(document).ready(function(){
   
 
  TemplateList();

});
// SHOW Wallet settlement histry
$(document).ready(function() {
          const url = 'transactions';
          var formDataObject  = {};
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
    // console.log(response.transaction_summary.total);
           var totalValue=0; 
        if (response.status === 200) {
            var totalValue = response.transaction_summary.total;
            if(totalValue!='' && totalValue !== null){
                var totalValue = totalValue;
                
            }else{
               var totalValue = 0; 
            }
            if(totalValue == undefined){
                var totalValue = 0; 
            }
            
            document.getElementById("totalValue").innerHTML = totalValue;
        } else if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }

});
});
// booking overal ammount
$(document).ready(function() {

          const url = 'indexbookingsammount';
          var formDataObject  = {};
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
    console.log(response.transactions[0].totalNetTotal);
    var bookingsvalue=response.transactions[0].totalNetTotal;
    // alert(bookingsvalue);
    if (bookingsvalue !== '' && bookingsvalue !== null) {
        var bookingsvalue=bookingsvalue;
    }else{
        var bookingsvalue=0;
    }
    
    if (response.status === 200) {
        document.getElementById("totalbookingValue").innerHTML = bookingsvalue;
    } else if (response.status === 400) {
        warningClick('Error', response.message, "danger");
    } else if (response.status === 500) {
        warningClick('Error', response.error, "danger");
    } else if (response.status === 401) {
        unauth();
    } else {
        console.error('Unhandled status code:', response.status);
    }
});
});
// driver ammount 
$(document).ready(function() {
          const url = 'indexdriverammount';
          var formDataObject  = {};
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
// $.ajax(settings).done(function (response) {
//     var bookingsvalues=response.transactions[0].totalNetTotal;
//     console.log(bookingsvalues);
//     if(bookingsvalues!='' && bookingsvalue !== null){
//       var bookingsvalues=bookingsvalues; 
//     }else{  
//       var bookingsvalues=0; 
//     }
//     console.log(bookingsvalues);
//         if (response.status === 200) {
//             document.getElementById("totaldrivervalue").innerHTML = bookingsvalues;
//         } else if (response.status === 400) {
//             warningClick('Error', response.message, "danger");
//         } else if (response.status === 500) {
//             warningClick('Error', response.error, "danger");
//         } else if (response.status === 401) {
//             unauth();
//         } else {
//             console.error('Unhandled status code:', response.status);
//         }

$.ajax(settings).done(function (response) {
      var bookingsvalues = response.transactions[0].totalNetTotal;
    console.log(bookingsvalues);  // This logs the correct value, e.g., 123663
    
    // Check for invalid bookingsvalues and set it to 0 if necessary
    if (bookingsvalues === '' || bookingsvalues === null || typeof bookingsvalues === 'undefined') {
        bookingsvalues = 0;  // Only set to 0 if the value is invalid
    }

    console.log(bookingsvalues);  // This should log the correct or 0 value

    // Append the value to the UI if the status is 200
    if (response.status === 200) {
        document.getElementById("totaldrivervalue").innerHTML = bookingsvalues;
    } else if (response.status === 400) {
        warningClick('Error', response.message, "danger");
    } else if (response.status === 500) {
        warningClick('Error', response.error, "danger");
    } else if (response.status === 401) {
        unauth();
    } else {
        console.error('Unhandled status code:', response.status);
    }
});
});
//auto slider on dashboard with airport data
// $(document).ready(function() {
//           const url = 'rangefareindexunicairportget';
//           var formDataObject  = {};
//           formDataObject['token'] = getCookie('d_token');
//           formDataObject['device_id'] = 0;
//           var settings = {
//          "url": "{{env('API_URL')}}"+url,
//          "method": "POST",
//          "timeout": 0,
//          "headers": {
//              "Content-Type": "application/json"
//           },
//          "data": JSON.stringify(formDataObject),
//       };
//     $.ajax(settings).done(function (response) {
//         var airportname=response.data[0].from_airport;
//         var airportname1=response.data[1].from_airport;
//         var country1 = extractCountry(airportname);
//         var country2 = extractCountry(airportname1);
//         if (response.status === 200) {
//         document.getElementById("totalairportvalue").innerHTML = airportname;
//         document.getElementById("country1").innerHTML = country1;
//         document.getElementById("country2").innerHTML = country2;
//         document.getElementById("totalairportvalue1").innerHTML = airportname1;
    
//             } else if (response.status === 400) {
//                 warningClick('Error', response.message, "danger");
//             } else if (response.status === 500) {
//                 warningClick('Error', response.error, "danger");
//             } else if (response.status === 401) {
//                 unauth();
//             } else {
//                 console.error('Unhandled status code:', response.status);
//             }
//     });
//     });
// function getCurrencySymbol(currencyCode) {
//     const currencySymbols = {
//         "INR": "₹", // Indian Rupee
//         "USD": "$", // US Dollar
//         "GBP": "£", // British Pound
//         "EUR": "€", // Euro
//         "JPY": "¥", // Japanese Yen
//         "AUD": "A$", // Australian Dollar
//         "CAD": "C$", // Canadian Dollar
//         "CHF": "Fr", // Swiss Franc
//         "CNY": "¥", // Chinese Yuan
//         "RUB": "₽", // Russian Ruble
//         // Add more currencies as needed
//     };

//     // Convert the currencyCode to uppercase to make it case-insensitive
//     const upperCurrencyCode = currencyCode.toUpperCase();

//     // Return the symbol, or return the currency code if the symbol is not found
//     return currencySymbols[upperCurrencyCode] || upperCurrencyCode;
// }
//get country 
function extractCountry(airportName) {

    var parts = airportName.split(',');

    var country = parts[parts.length - 1].trim();

    return country;
}
//get currency
// $(document).ready(function() {
//     const url = 'generalsettingcurrency';
//     var formDataObject = {};
//     formDataObject['token'] = getCookie('d_token');
//     formDataObject['device_id'] = 0;

//     var settings = {
//         "url": "{{env('API_URL')}}" + url,
//         "method": "POST",
//         "timeout": 0,
//         "headers": {
//             "Content-Type": "application/json"
//         },
//         "data": JSON.stringify(formDataObject),
//     };

//     $.ajax(settings).done(function(response) {
//     // console.log(response.data.site_currency);
//     var airportname=response.data.currency;
    
//     let symbol = getCurrencySymbol(airportname); 
//         if (response.status === 200) {
//     document.getElementById("site_currency").innerHTML = symbol;
//     document.getElementById("site_currency1").innerHTML = symbol;
//     document.getElementById("site_currency2").innerHTML = symbol;
        
//         } else if (response.status === 400) {
//             warningClick('Error', response.message, "danger");
//         } else if (response.status === 500) {
//             warningClick('Error', response.error, "danger");
//         } else if (response.status === 401) {
//             unauth();
//         } else {
//             console.error('Unhandled status code:', response.status);
//         }
//     }).fail(function(jqXHR, textStatus, errorThrown) {
//         // Handle request failures
//         console.error("Request failed: " + textStatus, errorThrown);
//         warningClick('Error', 'Failed to load data', "danger");
//     });
// });

$(document).ready(function() {
    const url = 'generalsettingcurrency';
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(formDataObject)
    };

    $.ajax(settings).done(function(response) {
        if (response && response.status === 200) {
            var currencyString = response.data && response.data.currency ? response.data.currency : null;
            if (currencyString) {
                // Extract the currency code (e.g., INR) from the string "Indian rupee (INR)"
                var currencyCode = extractCurrencyCode(currencyString);

                if (currencyCode) {
                    let symbol = getCurrencySymbol(currencyCode);
                    document.getElementById("site_currency").innerHTML = symbol;
                    document.getElementById("site_currency1").innerHTML = symbol;
                    document.getElementById("site_currency2").innerHTML = symbol;
                } else {
                    console.error('Currency code not found in the response');
                }
            } else {
                console.error('Currency string not found in the response');
            }
        } else {
            handleResponseError(response);
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        // Handle request failures
        console.error("Request failed: " + textStatus, errorThrown);
        warningClick('Error', 'Failed to load data', "danger");
    });

    function handleResponseError(response) {
        if (response.status === 400) {
            warningClick('Error', response.message, "danger");
        } else if (response.status === 500) {
            warningClick('Error', response.error, "danger");
        } else if (response.status === 401) {
            unauth();
        } else {
            console.error('Unhandled status code:', response.status);
        }
    }

    function extractCurrencyCode(currencyString) {
        var matches = currencyString.match(/^\s*([A-Za-z]+)\s?\(/);
        return matches ? matches[1] : null;
    }
});

function getCurrencySymbol(currencyCode) {
    const currencySymbols = {
        "INR": "₹", 
        "USD": "$", 
        "GBP": "£", 
        "EUR": "€", 
        "JPY": "¥", 
        "AUD": "A$", 
        "CAD": "C$", 
        "CHF": "Fr", 
        "CNY": "¥", 
        "RUB": "₽", 

    };
    const upperCurrencyCode = currencyCode.toUpperCase();
    return currencySymbols[upperCurrencyCode] || upperCurrencyCode;
}





$(document).ready(function() {
    function sendRequest(url, successCallback) {
        var settings = {
            "url": "{{env('API_URL')}}" + url,
            "method": "POST",
            "headers": { "Content-Type": "application/json" },
            "data": JSON.stringify(formDataObject)
        };

        $.ajax(settings).done(successCallback).fail(function(response) {
            switch(response.status) {
                case 400: warningClick('Error', response.message, "danger"); break;
                case 500: warningClick('Error', response.error, "danger"); break;
                case 401: unauth(); break;
                default: console.error('Unhandled status code:', response.status);
            }
        });
    }

    // Driver amount
    sendRequest('bookingchart', function(response) {
        if (response.status === 200) {
            
            const chartLabels = response.chartLabels; // Days of the month
            const chartData = response.chartData; // Booking counts
            
            // Get the canvas element
            const canvas = document.getElementById('bookingChart');
            const ctx = canvas.getContext('2d'); // Get the context from the canvas element
            
            // Create the chart
            const bookingChart = new Chart(ctx, {
                type: 'bar', // You can change this to 'line', 'pie', etc.
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Total Bookings',
                        data: chartData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Modify the canvas element's display style
            canvas.style.display = 'block';  // You should modify the canvas, not ctx
            
            // Data from your Laravel backend
//         const chartLabels = response.chartLabels; // Days of the month
//         const chartData = response.chartData; // Booking counts
        
//         // console.log(chartLabels);
//         // console.log(chartData);

//         const ctx = document.getElementById('bookingChart').getContext('2d');
//         const bookingChart = new Chart(ctx, {
//                                 type: 'bar', // You can change this to 'line', 'pie', etc.
//                                 data: {
//                                     labels: chartLabels,
//                                     datasets: [{
//                                         label: 'Total Bookings',
//                                         data: chartData,
//                                         backgroundColor: 'rgba(75, 192, 192, 0.2)',
//                                         borderColor: 'rgba(75, 192, 192, 1)',
//                                         borderWidth: 1
//                                     }]
//                                 },
//                                 options: {
//                                     scales: {
//                                         y: {
//                                             beginAtZero: true
//                                         }
//                                     }
//                                 }
// });
//         ctx.style.display = 'block';
        
        // $('#chart_container').show();

           
        }
    });
    

    // Auto slider for airport data
    sendRequest('rangefareindexunicairportget', function(response) {
        if (response.status === 200 && response.data && response.data.length > 0) {
           
            var airport1 = response.data[0].from_airport;
            var airport2 = response.data[1].from_airport;
            $("#totalairportvalue").text(airport1);
            $("#country1").text(extractCountry(airport1));
            $("#country2").text(extractCountry(airport2));
            $("#totalairportvalue1").text(airport2);
        }
    });
});
$(function() {
        
        showlist('all')
        bookingsCount()
        driverlist()
        FleetList()
        $('#driver_name, #driver_name_filter').select2()

        //Date range picker variables
        const start = moment('2015-01-01');
        const end = moment();

        //Date range picker for pickup dates
        function pickup_callback(start, end) {
            $('#pickup_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#pickup_between_filter').daterangepicker({
            locale: {
      firstDay: 1
    }
        }, pickup_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        pickup_callback(start, end);

        //Date range picker for booking dates
        function booking_callback(start, end) {
            $('#booking_between_filter').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        $('#booking_between_filter').daterangepicker({
            locale: {
      firstDay: 1
    }
        }, booking_callback)
            .on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format(
                    'DD/MM/YYYY'));
            })
            .on('cancel.daterangepicker', function(ev, picker) {
                $(this).val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            });

        booking_callback(start, end);

        $("#pickup_between_filter").change(function() {
            let selected_date = $(this).val()
            let date_array = selected_date.split(" ")

            let from_date = moment(date_array[0]).format('YYYY-MM-DD');
            let to_date = moment(date_array[2]).format('YYYY-MM-DD');

            $('#pickup_date_from').val(from_date)
            $('#pickup_date_to').val(to_date)
        });

        //Change the value of date range filter for booking
        $("#booking_between_filter").change(function() {
            let selected_date = $(this).val()
            let date_array = selected_date.split(" ")

            let from_date = moment(date_array[0]).format('YYYY-MM-DD');
            let to_date = moment(date_array[2]).format('YYYY-MM-DD');

            $('#booking_date_from').val(from_date)
            $('#booking_date_to').val(to_date)
        });
        
        $('#dash_book_reset').on('click', function(){
            $('#book_filter_form')[0].reset()
            $('#pickup_date_from').val('')
            $('#pickup_date_to').val('')
            $('#booking_date_from').val('')
            $('#booking_date_to').val('')
            showlist('all')
        })
        
        $('#dash_book_search').on('click', function(){
            const url = 'bookingfilter';
            var key = window.location.href;
            var segments = key.split('/');
            var lastSegment = segments.pop();
                 var formdata = $('#book_filter_form').serialize();
                 var pairs = formdata.split('&');
                 var formDataObject = {};
             
                 for (var i = 0; i < pairs.length; i++) {
                     var pair = pairs[i].split('=');
                     var key = decodeURIComponent(pair[0]);
                     var value = decodeURIComponent(pair[1]);
                     formDataObject[key] = value;
                 }
                 formDataObject['token'] = getCookie('d_token');
                 formDataObject['device_id'] = 0;
                 formDataObject['order_status'] = '';
                 delete formDataObject['pickup_between_filter'];
                 delete formDataObject['booking_between_filter'];
                 // Destroy the existing DataTable before reinitializing
                var existingTable = $('#dash-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#dash-table', {
                processing: true,
                searching: false,
             ajax: {
                 url: '{{env('API_URL')}}bookingfilter',
                 method: 'POST',
                 dataSrc:"data",
                 data: formDataObject,
             },
             createdRow: function (row, data, dataIndex) {
                 // Add a class to the <tr> element based on the o  rder_status value
                 $(row).addClass('col-md-6 mb-2 card db-standard');
             },
             columns: [
                 { data: null,
                 className: 'dt-table-1',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-arrow-up-9-1" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.job_no}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-2',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-plane" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_flight_num}`
                 }
                 },
                 {
                    data: null,
                    className: 'dt-table-3',
                    render: function(data, type, row) {
                        // Convert pickup_date to a Date object
                        let date = new Date(row.pickup_date);
                        
                        // Extract year, month (short format), and day
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
                
                        // Format the date as "YYYY-MMM-DD"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate} / ${row.pickup_time}`;
                    }
                },
                 {
                    data: null,
                    className: 'dt-table-4',
                    render: function(data, type, row) {
                        // Convert booking_date to a Date object
                        let date = new Date(row.booking_date);
                
                        // Extract day, month (short format), and year
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = date.getDate(); // Get day as a number
                
                        // Format as "DD-MMM-YYYY"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate}`;
                    }
                },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-person-walking-luggage" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers}`
                 }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.car_type != null ? row.car_type : '-'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.from}`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-14',
                 render: function(data,type,row){
                     return `<p class="border-dashed"></p>`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-8',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.to}`;
                 }
                 },
                 {
  data: null,
  className: 'dt-table-9',
  render: function(data,type,row){
    return `
      <div>
        <i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-file-invoice" style="font-weight: 00;margin-left: 7px;color: #114462;"></i> ${row.payment_status != null ? row.payment_status : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-building-columns" style="font-weight: 00;margin-left: 5px;color: #114462;"></i> ${row.type ? row.type : 'N/A'}
      </div>
    `;
  }
},

                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                        let orderStatus = row.order_status;
                        if (orderStatus == null || orderStatus == 0 || orderStatus == undefined) {
                            orderStatus = 'Cancelled';  
                        }
                        
                        if (orderStatus == 'Pending') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Confirmed" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Pending</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Confirmed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Assigned" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Confirmed</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Assigned') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Dispatched" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Assigned</button>`;
                            
                         }  
                        if (orderStatus == 'Completed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}"  name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Completed</button>`;
                            
                         }  
                        if (orderStatus == 'Dispatched') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Completed" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Dispatched</button>`;
                            
                         } 
                         if (orderStatus == 'Cancelled') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Cancelled" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }
                        }
                        
                    

                 },
                 {
                     data: null,
                     className: 'dt-table-13',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<ul style="margin-right: -7px;">
                         <li  style="list-style-type: none;"><a href="/booking/edit/${row.id}" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a></li>
                         <li  style="list-style-type: none;"><a href="/booking-status-pdf/${row.id}" target="_blank" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a></li>
                         
                         <li style="list-style-type: none;"><a href="/booking/preview/${row.id}?d_token=${getCookie('d_token')}" target="_blank" title="Preview Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success previewItem"><i class="fa fa-eye"></i></a></li>

                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" title="Assign Driver" onclick="assigndriver(${row.id},'${row.job_no}','${row.total}')" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a></li>` : ``}
                         
                          ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendConfirmationEmail(${row.id})" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                          
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="removedriver(${row.id})" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a></li>
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS"</a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendMail(${row.id})" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         </ul>`;
                     }
                 }
             ],
            //  responsive: {
            //       details: {
            //           type: 'column',
            //           target: 'tr'
            //       }
            //   }
            });
        })

        //Assign driver
        // $('#saveBtn').click(function(e) {
        //     e.preventDefault();
        //     var formdata = $('#assignDriverForm').serialize();
        //     var pairs = formdata.split('&');
        //     var formDataObject  = {};
            
        //     for (var i = 0; i < pairs.length; i++) {
        //       var pair = pairs[i].split('=');
        //       var key = decodeURIComponent(pair[0]);
        //       var value = decodeURIComponent(pair[1]);
        //       formDataObject[key] = value;
        //     }
        //     formDataObject['token'] = getCookie('d_token');
        //     formDataObject['device_id'] = 0;
        //     $.ajax({
        //         data: formDataObject,
        //         url: "{{env('API_URL')}}assigndriver",
        //         type: "POST",
        //         dataType: 'json',
        //         success: function(response) {
        //             if(response['status'] == 200){
        //                 $('#form-modal').modal('hide');
        //              Swal.fire({
        //                         position: "top-right",
        //                         icon: "success",
        //                         title: response['message'],
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     }).then(function() {
        //                      showlist('all')
        //                  });
        //               }
        //           if(response['status'] == 400){
        //             errornotify(response)
        //           }
        //           if(response['status'] == 500){
                   
                   
        //              warningClick('Error',response['error'],"danger")
        //           }
        //           if(response['status'] == 401){
        //              unauth()
        //           }
        //         },
        //         error: function(data) {
        //             console.log('Error:', data);
        //         }
        //     });
        // });
        
    $('#saveBtn').click(function(e) {
    e.preventDefault();
    
    // Get values from form fields
    var bookingTotalAmount = $('#total').val();
    var driverAmount = $('#driver_amount').val();
    
    // Check if the total booking amount is greater than or equal to driver amount
    if (parseFloat(bookingTotalAmount) >= parseFloat(driverAmount)) {
        
        // Serialize form data
        var formData = $('#assignDriverForm').serialize();
        var pairs = formData.split('&');
        var formDataObject = {};
        
        // Convert form data into an object
        for (var i = 0; i < pairs.length; i++) {
            var pair = pairs[i].split('=');
            var key = decodeURIComponent(pair[0]);
            var value = decodeURIComponent(pair[1]);
            formDataObject[key] = value;
        }
        
        // Add token and device_id to the data
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
        
        // AJAX call to assign driver
        $.ajax({
            data: formDataObject,
            url: "{{env('API_URL')}}assigndriver",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                // Handle different response statuses
                if (response.status === 200) {
                    $('#form-modal').modal('hide');
                    Swal.fire({
                        position: "top-right",
                        icon: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        showlist('all');
                    });
                } else if (response.status === 400) {
                    errornotify(response);
                } else if (response.status === 500) {
                    warningClick('Error', response.error, "danger");
                } else if (response.status === 401) {
                    unauth();
                }
            },
            error: function(error) {
                console.log('Error:', error);
            }
        });
        
    } else {
        // Show error if driver amount is not valid
        Swal.fire({
            position: "top-right",
            icon: "error",
            title: "Driver amount must be equal to or less than the actual amount.",
            showConfirmButton: false,
            timer: 1500
        });
    }
});

        
        $('#fleet_create_sub').on('click', function(){
          const url = 'createvehichle';
        var formdata = $('#fleet_create_form').serialize();
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
            var hiddenImageName = $('#hidden_imageName').val();
            if (hiddenImageName) {
                formDataObject['file'] = hiddenImageName;   
            }            
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
         if(response['status'] == 200){
             $("#fleet_create_form")[0].reset();
             $('.modal-title').html('Add Fleet')
             $('#vehichle_form-modal').modal('hide')
            Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: false,
                       timer: 1500
                   }).then(function() {
                    FleetList()
                });
             }
         if(response['status'] == 400){
          //  errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      })
      
      $('#veh_brand_id').on('change', function(){
          var id = $('#veh_brand_id').val();
          if(id != ''){
              models(id,'','veh_model_id')
          }else{
              $('#model_id').html('<option value="">select</option>')
          }
      })


        $('body').on('click', '#preview_email', function() {
            bootprompt.dialog({
                title: 'Mail Preview',
                message: tinymce.get("customer_email_body").getContent(),
                size: 'large'
            })
        })

        $('#email_send_btn').click(function() {
            let message = tinymce.get("customer_email_body").getContent()
            let email = $('#customer_email').val()
           
            let current_url = '{{ url('') }}'
            let formatted_message = message.replace("../..", current_url)
            
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['message'] = formatted_message;
            formDataObject['email'] = email;
            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}bookdetail-mail",
                data: formDataObject,
                beforeSend: function() {
                    $('#load_animation_email').show()
                },
                success: function(response) {
                    $('#load_animation_email').hide()

                    if(response.status == 200){
                        $('#email-modal').modal('hide')
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                    }
                    if(response.status == 400){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000,
                        })
                    }
                    if(response['status'] == 500){
                       warningClick('Error',response['error'],"danger")
                    }
                    if(response['status'] == 401){
                       unauth()
                    }
                },
                error: function(data) {
                    $('#load_animation_email').hide()
                    console.log('Error');
                }
            });
        })

        $('#sms_send_btn').click(function() {
            let customer_number = $('#customer_no').val()
            let driver_number = $('#driver_no').val()
            let customer_message = $('#customer_message').val()
            let driver_message = $('#driver_message').val()
            let sms_customer = $('#customer_sms:checked').val() ? true : false
            let sms_driver = $('#driver_sms:checked').val() ? true : false

            if(sms_customer || sms_driver){
                $.ajax({
                    type: "POST",
                    url: "{{ route('SMSBookingDetails') }}",
                    data: {
                        customer_number: customer_number,
                        driver_number: driver_number,
                        customer_message: customer_message,
                        driver_message: driver_message,
                        sms_customer: sms_customer,
                        sms_driver: sms_driver
                    },
                    beforeSend: function() {
                        $('#load_animation_sms').show()
                    },
                    success: function(response) {
                        $('#load_animation_sms').hide()

                        if(response.status == 200){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'SMS Status',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 5000,
                            })
                        }
                    },
                    error: function(data) {
                        $('#load_animation_sms').hide()
                        console.log('Error');
                    }
                });
            } else {
                Swal.fire({
                    position: 'bottom-start',
                    icon: 'warning',
                    title: 'Recipient not selected',
                    text: 'Please select either driver or customer.',
                    showConfirmButton: false,
                    timer: 3000,
                })
            }
        })

        tinymce.init({
            selector: '#customer_email_body',
            branding: false,
            height: '1000',
            menu: {
                file: {
                    title: '',
                    items: ''
                },
                view: {
                    title: '',
                    items: ''
                },
            },
            relative_urls: false,
            remove_script_host: false
        });

        $('#reset').click(function(){
            $("#driver_name_filter").val(null).trigger("change");;
            $("#customer_name_filter").val('');
            $("#job_no_filter").val('');
            $("#ref_no_filter").val('');
            $("#pickup_between_filter").val('');
            $("#booking_between_filter").val('');
            $("#selected_driver").val(null).trigger("change");
            $("#filter_pickup_from_date").val('');
            $("#filter_pickup_to_date").val('');
            $("#filter_booking_from_date").val('');
            $("#filter_booking_to_date").val('');

            table.draw();
        });

    })
function sendMail(is){
        const url = 'emailtemplate-details';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['book_id'] = is;
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
         if(response['status'] == 200){
             let formatted_customer_email = 'Dear, ' + response['data'][0].fname + '.<br><br>' +
                '\n\n<b>The Vehicle Details</b><br>' +
                '\nReg No: ' + response['data'][0].d_vehreg + '<br/>' +
                '\nV Model: ' + response['data'][0].model + '<br/>' +
                '\nV Make: ' + response['data'][0].brand + '<br/>' +
                '\nV Color: ' + response['data'][0].d_vehcol + '<br/>' +
                '\nV Type: ' + response['data'][0].v_name + '<br/><br/>' +
                '\n\n<b>The Driver Details</b><br>' +
                '\n\n\n\n<img src="" style="width:150px; height=175px; margin: 0px 5px 0px 0px;"> <br><br>' +
                '\n\nDriver Name: ' + response['data'][0].d_name + '<br/>' +
                '\nDriver PCO License No: ' + response['data'][0].d_pco_no + '<br/>' +
                '\nDriver Phone No: ' + response['data'][0].d_phone + '<br/>' +
                '\nPickup Date: ' + response['data'][0].pickup_date + '<br/>' +
                '\n\nPickup Time: ' + response['data'][0].pickup_time.substring(0, 5) + '<br/>' +
                '\n\n<h4>Contact us</h4>' +
                '\n2. Airport Rides is not responsible for lost or damaged luggage or any other items left in the vehicle during the time of service. Please check the vehicle before Exiting. Items left in the vehicle may require extra charges to be returned. <br>' +
                '\n3. Please note that the prices do not include gratuity and are up to the client\'s discretion to tip the Driver, preference in cash, or any currency. <br>' +
                '\n\n<h4>Pickup Instruction</h4>' +
                '<ul>'+
                    '\n<li><strong>Airport Pickup,</strong> The driver will monitor the flight, only go into the terminal 45 minutes after the plane lands, and will meet you with your name on the Board Sign at the arrivals point, located immediately after the customs exit.</li>'+
                    '\n<li><strong>Hotel Pickup,</strong> Please wait at the hotel lobby for collection and just let the concierge or reception desk that you are waiting for a private transfer our driver will aim to arrive 10 minutes early at the hotel and make contact with the concierge or reception desk. </li>'+
                    '\n<li><strong>Private Address,</strong> The driver will make contact by ringing the doorbell and will be waiting as close as possible to the front door at the set pickup time.</li>'+
                    '\n<li><strong>Meet & Greet Service,</strong> Includes 90 minutes of free waiting time from the flight arrival time, an additional charge will apply £8 for every 10 minutes plus any additional car park charges. The train station and cruise terminals are allowed a free waiting time of 30 minutes from the booking time afterward £8 for every 10 minutes plus any additional car park charges. Payable to the driver in cash at the end of the service.</li>'+
                    '\n<li><strong>Our standard cancellation policy,</strong> To make a cancellation for a booking up to 12 hours before the journey pickup time no refund, 24 hours before the journey would be a 50% refund, and 48 hours before the journey 100% refund. For the 16-55 Seater bus, we require a minimum of 14 days notice before the date for a 100% refund, 10 days before the journey would be a 50% refund, and 7 days before the journey pick-up day no refund.</li>'+
                '</ul>'+
                '\n\nBest Regards<br>' +
                '\n<b>Airport Rides</b> <br>';
            $('#emailForm').trigger("reset")
            $('#email-modal').modal('show')

            $('#customer_email').val(response['data'][0].email)
            $('#customernames').val(response['data'][0].fname)
            tinymce.get("customer_email_body").setContent(formatted_customer_email)
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
    }
function sendConfirmationEmail(is){
        Swal.fire({
                title: "Confirmation Email",
                text: "Are you sure want to send the confirmation email to customer?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willUpdate) => {
                if (willUpdate.isConfirmed) {
                    var formDataObject  = {};
                    formDataObject['token'] = getCookie('d_token');
                    formDataObject['device_id'] = 0;
                    formDataObject['book_id'] = is;
                    $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}bookconfirm-mail",
                        data: formDataObject,
                        beforeSend: function() {
                            $('body').append(loading_animation())
                        },
                        success: function(response) {
                            $('.loading-overlay').remove()
                            if (response.status == 200) {
                                showlist('all')
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    text: 'Email sent successfully.',
                                    showConfirmButton: false,
                                    timer: 2000
                                })
                            } 
                            if(response.status == 400){
                                $('.loading-overlay').remove()
                                Swal.fire("Error",
                                    "Unable to send email now.",
                                    "error");
                            }
                            if(response['status'] == 500){
                               warningClick('Error',response['error'],"danger")
                            }
                            if(response['status'] == 401){
                               unauth()
                            }
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                }
            })
    }
function sendSMS(is){
        const url = 'template-details';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['book_id'] = is;
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
         if(response['status'] == 200){
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
    }
    
function showlist(status){

            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['order_status'] = status;
            var existingTable = $('#dash-table').DataTable();
            if (existingTable) {
                existingTable.destroy();
            }
            new DataTable('#dash-table', {
                processing: true,
                searching: false,
             ajax: {
                 url: '{{env('API_URL')}}bookinglist',
                 method: 'POST',
                 dataSrc:"data",
                 data: formDataObject,
             },
             createdRow: function (row, data, dataIndex) {
                 // Add a class to the <tr> element based on the o  rder_status value
                 $(row).addClass(`col-md-6 card db-standard ${data.order_status}`);
             },
             
             columns: [
                 { data: null,
                 className: 'dt-table-1',
                 render: function(data,type,row){
                    return `<i class="fa-solid fa-arrow-up-9-1" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.job_no ? row.job_no : 'N/A'}`;
                }
                 },
                 { data: null,
                 className: 'dt-table-2',
                 render: function(data,type,row){
                     return `<i class="fa-solid fa-plane" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${row.pickup_flight_num ? row.pickup_flight_num : 'N/A'}`
                 }
                 },
                 {
                    data: null,
                    className: 'dt-table-3',
                    render: function(data, type, row) {
                        // Convert pickup_date to a Date object
                        let date = new Date(row.pickup_date);
                        
                        // Extract year, month (short format), and day
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = String(date.getDate()).padStart(2, '0'); // Ensure two-digit day
                
                        // Format the date as "YYYY-MMM-DD"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate} / ${row.pickup_time}`;
                    }
                },
                 {
                    data: null,
                    className: 'dt-table-4',
                    render: function(data, type, row) {
                        // Convert booking_date to a Date object
                        let date = new Date(row.booking_date);
                
                        // Extract day, month (short format), and year
                        let year = date.getFullYear();
                        let month = date.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                        let day = date.getDate(); // Get day as a number
                
                        // Format as "DD-MMM-YYYY"
                        let formattedDate = `${day}-${month}-${year}`;
                
                        return `<i class="fa-solid fa-calendar-days" style="font-weight: 600;margin: 0 9px 2px 4px;color: #114462;"></i>${formattedDate}`;
                    }
                },
                 { data: null,
                 className: 'dt-table-5',
                 render: function(data,type,row){
                    return `<i class="fa-solid fa-person-walking-luggage" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.passengers ? row.passengers : '0'}`;
                }
                 },
                 { data: null,
                 className: 'dt-table-6',
                 render: function(data,type,row){
                     
                     return `<i class="fa-solid fa-car" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.car_type != null ? row.car_type : 'N/A'}`;
                 } 
                 },
                 { data: null,
                 className: 'dt-table-7',
                 render: function(data,type,row){
                    return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 0px 1px;color: #114462;color: green;font-size: 20px;"></i>${row.from ? row.from : 'N/A'}`;
                }
                 },
                 { data: null,
                 className: 'dt-table-14',
                 render: function(data,type,row){
                     return `<p class="border-dashed"></p>`;
                 }
                 },
                 { data: null,
                 className: 'dt-table-8',
                 render: function(data,type,row){
                    return `<i class="fa-solid fa-location-dot" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;color: red;"></i>${row.to ? row.to : 'N/A'}`;
                }
                 },
                 {
  data: null,
  className: 'dt-table-9',
  render: function(data,type,row){
    return `
      <div>
        <i class="fa-solid fa-user" style="font-weight: 00;margin: 0 9px 2px 4px;color: #114462;"></i>${row.driver_name != null ? row.driver_name : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-file-invoice" style="font-weight: 00;margin-left: 7px;color: #114462;"></i> ${row.payment_status != null ? row.payment_status : 'N/A'}
      </div>
      <div style="margin-top: 10px;">
        <i class="fa-solid fa-building-columns" style="font-weight: 00;margin-left: 5px;color: #114462;"></i> ${row.type ? row.type : 'N/A'}
      </div>
    `;
  }
},

                 {
                     data: null,
                     className: 'dt-table-12',
                     render: function(data, type, row) {
                        let orderStatus = row.order_status;
                        if (orderStatus == null || orderStatus == 0 || orderStatus == undefined) {
                            orderStatus = 'Cancelled';  
                        }
                        
                        if (orderStatus == 'Pending') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Confirmed" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Pending</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Confirmed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Assigned" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Confirmed</button>
                            
                            <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Cancelled" name="status"
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }  
                        if (orderStatus == 'Assigned') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Dispatched" name="status" 
                            style="width: 100px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Assigned</button>`;
                            
                         }  
                        if (orderStatus == 'Completed') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}"  name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Completed</button>`;
                            
                         }  
                        if (orderStatus == 'Dispatched') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" onclick="changeBookStatus(${row.id}, this)" id="book_status${row.id}" data-btn_name = "Completed" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Dispatched</button>`;
                            
                         } 
                         if (orderStatus == 'Cancelled') {
                            return `
                    
                             <button class="form-control booking-status me-5 bg-warning" id="book_status${row.id}" data-btn_name = "Cancelled" name="status" 
                            style="width: 110px !important; color: white; margin-right: 0; background-color: #E0A008 !important;">Cancelled</button>`;
                            
                         }
                        }
                        
                    

                 },
                 {
                     data: null,
                     className: 'dt-table-13',
                     render: function(data, type, row) {
                         // Custom rendering logic goes here
                         return `<ul style="margin-right: -7px;">
                         <li  style="list-style-type: none;"><a href="/booking/edit/${row.id}" title="Edit Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-primary editPrice"><i class="fa fa-edit"></i></a></li>
                         <li  style="list-style-type: none;"><a href="/booking-status-pdf/${row.id}" target="_blank" title="Download PDF" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark downloadPDF"><i class="fa fa-download"></i></a></li>
                         
                         <li style="list-style-type: none;"><a href="/booking/preview/${row.id}?d_token=${getCookie('d_token')}" target="_blank" title="Preview Item" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success previewItem"><i class="fa fa-eye"></i></a></li>

                         ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" title="Assign Driver" onclick="assigndriver(${row.id},'${row.job_no}','${row.total}')" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-success assignDriver"><i class="fa fa-user-plus"></i></a></li>` : ``}
                         
                          ${row.order_status == 'Confirmed' ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendConfirmationEmail(${row.id})" title="Confirmation Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                          
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="removedriver(${row.id})" title="Remove Driver" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-danger removeDriver"><i class="fa fa-user-times"></i></a></li>
                         <li  style="list-style-type: none;"><a href="javascript:void(0)" title="Send SMS"</a></li>` : ``}
                         ${(row.order_status == 'Dispatched' || row.order_status == 'Assigned') ? `<li  style="list-style-type: none;"><a href="javascript:void(0)" onclick="sendMail(${row.id})" title="Send Email" class="mb-2 mr-2 btn-sm btn-transition btn btn-outline-dark"><i class="fa fa-envelope"></i></a></li>` : ``}
                         </ul>`;
                     }
                 }
             ],
            //  responsive: {
            //       details: {
            //           type: 'column',
            //           target: 'tr'
            //       }
            //   }
            });
         }
function assigndriver(id,job,ttl){
            $('#assignDriverForm').trigger("reset");
            $("#driver_name").val('').trigger('change');
            $('#driver_name').empty()
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          var settings = {
         "url": "{{env('API_URL')}}driverlist",
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             let driver_options = '<option value="">-- select driver --</option>'
             response['data'].forEach(function(item) {
                 if(item.status == 'Active' && (item.order_status == null || item.order_status == 'Pending' || item.order_status == 'Confirmed' || item.order_status == 'Completed')){
                    driver_options +=
                     `<option value="${item.id}">${item.name}</option>`
                     
                 }
             })
             $('#driver_name').html(driver_options)
             $('#booking_id').val(id);
             $('#job_no').val(job);
             $('#total').val(ttl);
             $('#form-modal').modal('show');
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
function removedriver(id){
            Swal.fire({
                title: "Driver Removal",
                text: "Are you sure want to remove the driver?.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willUpdate) => {
                if (willUpdate.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}assigndriver",
                        data: {
                            booking_id: id,
                            status: 'Confirmed',
                            token: getCookie('d_token'),
                            device_id: 0
                        },
                        success: function(response) {
                            if(response['status'] == 200){
                                Swal.fire({
                                  position: "top-right",
                                  icon: "success",
                                  title: response['message'],
                                  showConfirmButton: false,
                                  timer: 1500
                                }).then(function() {
                                 showlist('all')
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
                        },
                        error: function(data) {
                            console.log('Error:', data);
                        }
                    });
                }
            })
        }
function changeBookStatus(id, element){
            if ($(element).data('btn_name') == "Confirmed") {
                window.open('/booking/edit/' + id, '_blank');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to change status!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectstat = $('#book_status' + id).val();
                    var optionToRemoveSelected = $('#book_status' + id+' option[value="'+selectstat+'"]');
                    optionToRemoveSelected.removeAttr('selected');
                    var formDataObject  = {};
                     formDataObject['token'] = getCookie('d_token');
                     formDataObject['device_id'] = 0;
                     formDataObject['booking_id'] = id;
                    //  formDataObject['status'] = $('#book_status' + id).text();
                     formDataObject['status'] = $(element).data('btn_name');
                     
                     $.ajax({
                        type: "POST",
                        url: "{{env('API_URL')}}bookingstatus",
                        data: formDataObject,
                        success: function(response) {
                            if(response['status'] == 200){
                              Swal.fire({
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: true,
                                 timer: 2500
                                }).then(function() {
                                    showlist('all')
                                });
                            }
                            if(response['status'] == 400){
                              warningClick('Error',response['error'],"danger")
                            }
                            if(response['status'] == 500){
                              warningClick('Error',response['error'],"danger")
                            }
                            if(response['status'] == 401){
                              unauth()
                            }
                        },
                        error: function(data) {
                            ('[data-id="' + booking_id + '"]').val(order_status)
                            console.log('Error:', data);
                        }
                    });
                    
                } else {
                    $('[data-id="' + booking_id + '"]').val(order_status)
                }
            })
        }
function bookingsCount(){
            const url = 'bookinglist';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['order_status'] = "all"
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
         if(response['status'] == 200){
             var dataArray = response['data'];
             var pendingOrders = dataArray.filter(function(item) {
                    return item.order_status === "Pending";
                 });
             var confirmedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Confirmed";
                 });
             var assignedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Assigned";
                 });
             var cancelledOrders = dataArray.filter(function(item) {
                    return item.order_status === "Canceled";
                 });
             var dispatchedOrders = dataArray.filter(function(item) {
                    return item.order_status === "Dispatched";
                 });
             $('#ttl_book').html(animateCount(response['data'].length,'ttl_book'))
             $('#pending_book').html(animateCount(pendingOrders.length,'pending_book'))
             $('#confirmed_book').html(animateCount(confirmedOrders.length,'confirmed_book'))
             $('#assigned_book').html(animateCount(assignedOrders.length,'assigned_book'))
             $('#dispatched_book').html(animateCount(dispatchedOrders.length,'dispatched_book'))
             $('#cancelled_book').html(animateCount(cancelledOrders.length,'cancelled_book'))
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
        }
function FleetList() {
    const url = 'vehichlelist';
    var formDataObject  = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(formDataObject),
    };

    $.ajax(settings).done(function (response) {
       
        if (response['status'] == 200) {
            // Display the fleet list if there are vehicles
            var list = `<h3 class="hd-name">Car Fleets</h3>
                    <div class="owl-carousel owl-theme" id="fleet_corousel">`;

            var promises = [];
            for(let i = 0; i < response['data'].length; i++) {
                let promise = new Promise((resolve, reject) => {
                    file(response['data'][i], i, function(imageData, index) {
                        // console.log('hiiii'); 
                        let item = `<div class="item card">
                                <div class="tent">
                                    <ul class="ed-ul">
                                        <button class="ed-icon" onclick="editvehichle(${response['data'][index].id})">
                                            <i class="fa-solid fa-pen-to-square" style="color: #fff;"></i> 
                                        </button>
                                        <button class="del-icon" onclick="deletefleet(${response['data'][index].id})">
                                            <i class="fa-solid fa-trash" style="color: #fff;"></i>
                                        </button>
                                    </ul>
                                </div>
                                <div class="img-cd">
                                    ${imageData}
                                    <h3 class="car-name text-center">${response['data'][index]['name'].toUpperCase()}</h3>
                                </div>
                            </div>`;
                        resolve(item);
                    });
                });
                promises.push(promise);
            }

            Promise.all(promises).then((values) => {
                list += values.join('');
                list += `</div>`;
                $('#fleet_container').html(list);

                var owl = $(".owl-carousel");
                owl.owlCarousel({
                    items: 3,
                    margin: 10,
                    loop: false,
                    nav: true,
                    dots: true,
                    center: true,
                    autoplay: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        748: {
                            items: 2
                        },
                        1280: {
                            items: 2
                        }
                    }
                });
                $('.owl-dots').css('display','block');
            });
        } else if (response['status'] == 400 && response['message'] === "Vehichle Data Not Found") {
            // Display 'Add Fleet' button if no vehicles found
            $('#fleet_container').html(`
                <div class="no-fleet text-center mt-5">
                    <h3>No Fleets Available</h3>
                    <a href="/fleet" class="btn btn-primary">Add Fleet</a>
                </div>
            `);
        } else if (response['status'] == 400) {
            errornotify(response);
        } else if (response['status'] == 500) {
            warningClick('Error', response['error'], "danger");
        } else if (response['status'] == 401) {
            unauth();
        }
    });
}
function driverlist(){
        var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          var settings = {
         "url": "{{env('API_URL')}}driverlist",
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             let driver_options = '<option value="">-- select driver --</option>'
             response['data'].forEach(function(item) {
                 driver_options +=
                     `<option value="${item.id}">${item.name}</option>`
             })
             $('#driver_name_filter').html(driver_options)
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
    function editvehichle(id){
            const url = 'editvehichle';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['fleet_id'] = id;
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
            if(response['status'] == 200){
                $('.modal-title').html('Edit Fleet')
                AssignValues(response)
                $('#vehichle_form-modal').modal('show')
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
function deletefleet(id){
            const url = 'deletevehichle';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
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
                       $("#fleet_create_form")[0].reset();
                       $('.modal-title').html('Add Fleet')
                       $('#flfrm_dis').click()
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              FleetList()
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
        
        function AssignValues(response) {
    $('#edit_imagePreview').attr('src', '');  // Reset image preview

    // Assign other fields
    let imagePath = response.data.upload_photo;
    const imageName = imagePath ? imagePath.split('/').pop() : ''; // Get file name from path if imagePath exists
    const hiddenFileNameInput = document.getElementById('hidden_imageName');
    console.log(hiddenFileNameInput);
    if (hiddenFileNameInput) {
        hiddenFileNameInput.value = imageName;  // Set the value to the image file name
    }
    let curr_url = window.location.pathname;
    let fleetData = curr_url.includes('create-fleet') ? response : response.data; // Check for "create-fleet" instead of exact match

    // Assign values to form fields
    $('#fleet_id').val(fleetData.id);
    $('#name').val(fleetData.name);
    $('#passenger').val(fleetData.passenger);
    $('#no_of_seats').val(fleetData.no_of_seats);
    $('#min').val(fleetData.min);
    $('#max').val(fleetData.max);
    $('#luggage').val(fleetData.luggage);
    $('#hand_luggage').val(fleetData.hand_luggage);
    $('#child').val(fleetData.child);

    // If the image is present in the response, display the image preview
    if (imagePath) {
        var imageUrl = 'https://airportrides-storage.s3.amazonaws.com/' + imagePath;
        $('#edit_imagePreview').attr('src', imageUrl);
    }

    // Set the image file name in the hidden input field
    

    // Logging the hidden input value to check if it's correctly set
    console.log('Hidden Input Value:', hiddenFileNameInput ? hiddenFileNameInput.value : 'Not Found');
}
function reset(){
           $("#fleet_create_form")[0].reset();
           $('#vehichle_form-modal').modal('hide')
          brands('','brand_id')
          $('#fleet_id').val('')
          $('#model_id').html('<option value="">Select</option>')
          $('.modal-title').html('Add Fleet')
      }
function animateCount(targetCount,ref_id) {
    var currentCount = 0;
    var countDisplay = $("#"+ref_id);

    function updateCount() {
        countDisplay.text(currentCount);
    }

    $({ count: currentCount }).animate({ count: targetCount }, {
        duration: 1000, // Animation duration in milliseconds
        step: function() {
            currentCount = Math.ceil(this.count);
            updateCount();
        },
        complete: function() {
            currentCount = targetCount;
            updateCount();
        }
    });
}
//prasanth show file
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

    $.ajax(settings).done(function(response) {
        // var imageData = `<img class="w-100" src="data:image/png;base64,${response.image}" alt="Displayed Image" style="height:201px;">`;
        var imageData = `<img class="w-100" src="https://airportrides-storage.s3.amazonaws.com/${response.image}" alt="Displayed Image" style="height:201px;">`;

        if (callback && typeof callback === "function") {
            callback(imageData, index);
        }
    });
}
function AssignModal_ShowErrors(errors) {
        if (errors.charges) {
            $('.invalid-charges').text(errors.charges);
        }
        if (errors.driver_amount) {
            $('.invalid-driver_amount').text(errors.driver_amount);
        }
        if (errors.driver_id) {
            $('.invalid-driver_name').text(errors.driver_id);
        }
    }
function AssignModal_ResetErrors() {
        $('.invalid-charges, .invalid-driver_name, .invalid-driver_amount').text('')
    }
function loading_animation(){
        return `<div class="loading-overlay d-flex align-items-center justify-content-center">
                    <div class="spinner-grow text-primary" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-secondary" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-success" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-danger" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-warning" role="status">
                        <span class="sr-only"></span>
                        </div>
                        <div class="spinner-grow text-info" role="status">
                        <span class="sr-only"></span>
                    </div>
                </div>`
    }
    @if (session('booking_details_update'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('booking_details_update') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('booking_details_update');
        @endphp
    @endif

    @if (session('booking_status_update'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('booking_status_update') }}',
            showConfirmButton: false,
            timer: 2000,
        })

        @php
            Illuminate\Support\Facades\Session::forget('booking_status_update');
        @endphp
    @endif
    
    
        function TemplateList() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    $.ajax({
        url: '{{env('API_URL')}}TemplateList',
        method: 'POST', 
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var templates = response.data;
                PopulateSelect(templates); 
                console.log(response);
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

function PopulateSelect(templates) {
    var select = $('#templateSelect');
    select.empty(); 

    select.append('<option value="">Select Template</option>');

    if (templates.length > 0) {
        templates.forEach(function(template) {
            var option = $('<option></option>');
            option.val(template.description); 
            option.text(template.template_name); 
            select.append(option);
        });
    } else {
        select.append('<option value="">No templates found.</option>');
    }
}

function changefleetstatus(id){
          const url = 'vehichlestatus';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['fleet_id'] = id;
          if ($('#flstatus'+id).prop('checked')) {
                formDataObject['isActive'] = 'Active';
            } else {
                formDataObject['isActive'] = 'Inactive';
            }
        //   console.log(formDataObject);
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
             text: 'You want to change the status!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes',
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
                              showlist()
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
                showlist()
             }
         });
      }

$('#templateSelect').on('change', function() {
    var selectedTemplateDescription = $(this).val(); 
    if (selectedTemplateDescription) {
        $('#customer_email_send').html(selectedTemplateDescription); 
    } else {
        $('#customer_email_send').html(''); 
    }
});

 $(document).ready(function() {
    $('#preview_email').click(function() {
         var name = $('#customernames').val();
        var emailBody = $('#customer_email_send').html();
        var recipientEmail = $('#customer_email').val();
      

        var previewContent = `
         <p>Dear ${name}</p>
            <h6>To: ${recipientEmail}</h6>
            
            <hr>
            <div>${emailBody}</div>
        `;
        $('#email_preview_content').html(previewContent);
        $('#previewModal').modal('show');
    });
    
    //11-01-2025
    
     const fileInput = document.getElementById('fileInput');
    const image = document.getElementById('edit_imagePreview'); // Ensure this element exists in your HTML
    let cropper;
    let isImageCropped = false; 
    let imageType = 'image/png'; 

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        imageType = file.type || 'image/png';

        reader.onload = function (e) {
            image.src = e.target.result;
            image.style.display = 'block'; // Ensure the image is visible
            if (cropper) {
                cropper.destroy(); 
            }
            cropper = new Cropper(image, {
                viewMode: 1,
                autoCropArea: 1, // Ensure full area is used
            });
            isImageCropped = true; 
        };
        reader.readAsDataURL(file);
    });

    $('#fleet_create_sub').on('click', function (e) {
        e.preventDefault(); 
        const formDataObject = new FormData($('#fleet_create_form')[0]);
        formDataObject.append('_token', $('input[name="_token"]').val());
        formDataObject.append('token', getCookie('d_token'));
        formDataObject.append('device_id', 0);
        formDataObject.append('file', $('#hidden_imageName').val());

        if (isImageCropped && cropper) {
            const croppedCanvas = cropper.getCroppedCanvas();
            const uniqueFileName = `croppedFleetImage_${Date.now()}.${imageType.split('/')[1]}`;

            croppedCanvas.toBlob(function (blob) {
                formDataObject.append('file', blob, uniqueFileName); 
                submitForm(formDataObject, 0); 
            }, imageType);
        } else {
            submitForm(formDataObject, 0); 
        }
    });
    $('#sbtUpdate').on('click', function (e) {
        e.preventDefault(); 
        const formDataObject = new FormData($('#fleet_create_form')[0]);
        formDataObject.append('_token', $('input[name="_token"]').val());
        formDataObject.append('token', getCookie('d_token'));
        formDataObject.append('device_id', 0);
        
        formDataObject.append('file', $('#hidden_imageName').val());

        if (isImageCropped && cropper) {
            const croppedCanvas = cropper.getCroppedCanvas();
            const uniqueFileName = `croppedFleetImage_${Date.now()}.${imageType.split('/')[1]}`;

            croppedCanvas.toBlob(function (blob) {
                formDataObject.append('file', blob, uniqueFileName); 
                submitForm(formDataObject, 1); 
            }, imageType);
        } else {
            submitForm(formDataObject, 1); 
        }
    });
      function submitForm(formData, forNumber) {
        let text_btn = $('#fleet_create_sub').html();

        

        document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

        const fleetName = document.getElementById('name').value.trim();
        let isValid = true;

        // Validate Fleet Name
        if (fleetName === '') {
            document.querySelector('.invalid-fleet-name').textContent = 'Fleet Name is required.';
            isValid = false;
        } else if (fleetName.length > 30) {
            document.querySelector('.invalid-fleet-name').textContent = 'Fleet Name cannot exceed 30 characters.';
            isValid = false;
        }

        // Validate Passengers
        const passengers = document.getElementById('passenger').value.trim();
        if (passengers === '') {
            document.querySelector('.invalid-passenger').textContent = 'Passengers count is required.';
            isValid = false;
        } else if (isNaN(passengers) || passengers < 1 || passengers > 99) {
            document.querySelector('.invalid-passenger').textContent = 'Passengers count must be between 1 and 99.';
            isValid = false;
        }
        
        const child = document.getElementById('child').value.trim();
        if (child === '') {
            document.querySelector('.invalid-child').textContent = 'Child count is required.';
            isValid = false;
        } else if (isNaN(child) || child < 0 || child > 99) {
            document.querySelector('.invalid-child').textContent = 'Child count must be between 1 and 99.';
            isValid = false;
        }
        
        // const booster = document.getElementById('booster').value.trim();
        // if (booster === '') {
        //     document.querySelector('.invalid-booster').textContent = 'Booster count is required.';
        //     isValid = false;
        // } else if (isNaN(booster) || booster < 0 || booster > 99) {
        //     document.querySelector('.invalid-booster').textContent = 'Booster count must be between 0 and 99.';
        //     isValid = false;
        // }
        const luggage = document.getElementById('luggage').value.trim();
        if (luggage === '') {
            document.querySelector('.invalid-luggage').textContent = 'Luggage count is required.';
            isValid = false;
        } else if (isNaN(luggage) || luggage < 0 || luggage > 99) {
            document.querySelector('.invalid-luggage').textContent = 'Luggage count must be between 0 and 99.';
            isValid = false;
        }
        const hand_luggage = document.getElementById('hand_luggage').value.trim();
        if (hand_luggage === '') {
            document.querySelector('.invalid-hand-luggage').textContent = 'Hand luggage count is required.';
            isValid = false;
        } else if (isNaN(hand_luggage) || hand_luggage < 0 || hand_luggage > 99) {
            document.querySelector('.invalid-hand-luggage').textContent = 'Hand luggage count must be between 0 and 99.';
            isValid = false;
        }
        
        // const order = document.getElementById('order').value.trim();
        // if (order === '') {
        //     document.querySelector('.invalid-order').textContent = 'Order count is required.';
        //     isValid = false;
        // } else if (isNaN(order) || order < 0 || order > 99) {
        //     document.querySelector('.invalid-order').textContent = 'Order count must be between 0 and 99.';
        //     isValid = false;
        // }
        
        const fileInputs = document.getElementById('fileInput');
        const filePreviousInputs = $('#edit_imagePreview').attr('src');;
        
        // console.log(filePreviousInputs,filePreviousInputs === '', !fileInputs.files.length);
        
        if (filePreviousInputs === '') { // Check if image preview source is empty
            if (!fileInputs.files.length) { // Check if no file is selected in the input
                document.querySelector('.invalid-image').textContent = 'Fleet Image is required.';
                isValid = false;
            }
        }



        // Validate other fields (Luggage, Hand Luggage, Child Seats, Booster, Order)
        // Add your other validation logic here...

        // If all fields are valid, submit the form
        if (isValid) {
            if (forNumber) {
                $('#sbtUpdate').html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
                    <span class="visually-hidden">Loading...</span>
                </div>`);
            } else {
                $('#fleet_create_sub').html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">
                    <span class="visually-hidden">Loading...</span>
                </div>`);
            }
            $.ajax({
                url: "{{env('API_URL')}}createvehichle",
                method: "POST",
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response['status'] == 200) {
                        $("#fleet_create_form")[0].reset();
                        $('.modal-title').html('Add Fleet');
                        $('#flfrm_dis').click();
                        Swal.fire({
                            position: "top-right",
                            icon: "success",
                            title: response['message'],
                            showConfirmButton: false,
                            text: 'Fleet has been created successfully',
                            timer: 1500
                        }).then(function () {
                            showlist(); 
                        });
                    } else if (response['status'] == 400) {
                        errornotify(response); 
                    } else if (response['status'] == 500) {
                        warningClick('Error', response['error'], "danger"); 
                    } else if (response['status'] == 401) {
                        unauth(); 
                    }

                    if (forNumber) {
                        $('#sbtUpdate').html("Save and Previous");
                        window.location.href = '/bookingSetting';
                    } else {
                        $('#fleet_create_sub').html("Save");
                        window.location.href = '/dashboard';
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error(textStatus, errorThrown);
                }
            });
        } else {
            $('#fleet_create_sub').html("Save");
        }
    }
    
    //end  11-01-2025
    
    
});

$('#primaryBtn').click(function (e) {
    e.preventDefault();
    var form = $('#emailForm')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    var formdata = new FormData(form);
    formdata.append('token', getCookie('d_token'));
    formdata.append('device_id', 0);

 
    var emailBody = $('#customer_email_send').html();
    formdata.append('description', emailBody);

    var selectedTemplateValue = $('#templateSelect').val();
    var selectedTemplateName = $('#templateSelect').find('option:selected').text();

    console.log('Selected Template Value:', selectedTemplateValue);
    console.log('Selected Template Name:', selectedTemplateName);

    if (!selectedTemplateValue) {
        console.log('No template selected.');
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select a template.',
        });
        return; 
    }

    formdata.append('template_name', selectedTemplateName);

    $.ajax({
        url: "{{env('API_URL')}}CustomerDashboardBooking",
        type: "POST",
        data: formdata,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if (response.status === 200) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Added',
                    text: 'Data has been inserted successfully',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function () {
                    // window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: response.message || 'Unexpected response received',
                });
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong! ' + xhr.responseText,
            });
        }
    });
});
</script>
