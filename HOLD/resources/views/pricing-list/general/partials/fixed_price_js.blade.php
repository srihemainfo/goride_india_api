<script>
$(document).ready(function(){
   
  showlist();  
});    

function showlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}PricingShow',
        method: 'POST', // Consider using GET if you're only fetching data
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                console.log(response);
                AssignValues(data);
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

function AssignValues(data) {
   $('#pricingid').val(data.id);
    $('#priceDecimal').val(data.price_decimal);
    $('#DropOffMinimumPrice').val(data.minprice_dropoff);
    $('#ChildSeatPrice').val(data.childseat_price);
    $('#selCardPaymentPriceType').val(data.cardpayment_pricetype).trigger('change').trigger('select');
    $('#CardPaymentPrice').val(data.cardpayment_percentage);
}



    
  $('#saveBtn').click(function (e) {
    e.preventDefault();

    // Check if form exists before using it
    var form = $('#formPricingGeneral')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    // Initialize FormData after confirming the form exists
    var formdata = new FormData(form);

    // Append additional data to FormData after initialization
    formdata.append('token', getCookie('d_token')); // Assuming getCookie is a function that gets the cookie value
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}GeneralPricingstore",
        type: "POST",
        processData: false,  // Important for FormData
        contentType: false,  // Important for FormData
        dataType: 'json',
         success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Data has been updated successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                }
            }
        },
        error: function (xhr, status, error) {
            // Better error logging
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