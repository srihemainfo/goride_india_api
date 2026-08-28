<script>

    $(document).ready(function(){
    
  showlist();  
  $('#paymentModal').modal('show')
});    
    // show list
    function showlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}paymentoption',
        method: 'POST',
        data: formDataObject,
        success: function(response) {
             console.log(response.data);
         var data= response.data;  
         AssignValues(data);
          //console.log(response);
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}
    // assign the out put values
    function AssignValues(data){
            
        $('#paymentsetting_id').val(data.id);
        $('#cash_title').val('pay_by_cash');
        $('input[name="paypal_live_check"]').each(function() {
            $(this).prop('checked', $(this).val() == data.paypal_live_check);
        });
        $('#paypal_title').val('paypal');
        $('#paypal_id').val(data.paypal_id);
        $('#paypal_identify_token').val(data.paypal_identify_token);
        $('input[name="stripe_live_check"]').each(function() {
            $(this).prop('checked', $(this).val() == data.stripe_live_check);
        });
        $('#stripe_title').val('stripe');
        $('#stripePublishableKey').val(data.stripePublishableKey);
        $('#stripeSecretKey').val(data.stripeSecretKey);
        $('#stripeWebhookUrl').val(data.stripeWebhookUrl);
        $('#stripeWebhookEvent').val(data.stripeWebhookEvent);
        $('#stripeWebhookSecretKey').val(data.stripeWebhookSecretKey);
        // Handle the square_live_check radio button
        $('input[name="square_live_check"]').each(function() {
            $(this).prop('checked', $(this).val() == data.square_live_check);
        });
        $('#square_title').val('square');
        if (data.paypal_live_check === 'on') {
            $('#paypalPaymentFor').prop('checked', true);
        }
        if (data.stripe_live_check === 'on') {
            $('#stripePaymentFor').prop('checked', true);
        }
        if (data.square_live_check === 'on') {
            $('#squarePaymentFor').prop('checked', true);
        }
        if (data.cash_check === 'on') {
            $('#cashPaymentFor').prop('checked', true);
        }

        $('#txtsquare_accessToken').val(data.txtsquare_accessToken);
        $('#txt_square_appId').val(data.txt_square_appId);
        $('#txt_square_locationId').val(data.txt_square_locationId);
        
        
        
    }
    // save the payment setting
  	$('#saveBtn').click(function (e) {
    e.preventDefault();
    const url = 'paymentstore';
    var formdata = new FormData($('#formSettingsPayment')[0]);
    // Append additional fields
    formdata.append('token', getCookie('d_token'));
    formdata.append('device_id', 0);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}" + url,
        type: "POST",
        processData: false, 
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.status == 400) {
                errornotify(response);
            } else if (response.status == 500) {
                warningClick('Error', response['error'], "danger");
            } else if (response.status == 401) {
                unauth();
            } else if (response.status == 200) {

                if (response.message =="Data has been update successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been updated successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        let curr_url  = window.location.pathname;
                        
                        if(curr_url == '/paymentOption'){
                            window.location.href = '/emailSetting';
                        }else{
                            window.location.reload();
                        }

                    });
                } else if(response.message =="Data has been inserted successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Added',
                        text: 'Data has been inserted successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        let curr_url  = window.location.pathname;
                        
                        if(curr_url == '/paymentOption'){
                            window.location.href = '/emailSetting';
                        }else{
                            window.location.reload();
                        }

                    });
                }
            }
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

        
</script>