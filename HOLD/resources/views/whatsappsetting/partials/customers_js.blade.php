<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

        url: '{{env('API_URL')}}get-whatsappsetting',

        method: 'POST',

        data: formDataObject,

        success: function(response) {

         var data= response.data;


         if(data){

            AssignValues(data);

            

         }

          //console.log(response);

        },

        error: function(error) {

            console.error('Error fetching data:', error);

        }

    });

}

    // assign the out put values

    function AssignValues(data){

            

        $('#whatsappsetting_id').val(data.id);

        

        // $('input[name="paypal_live_check"]').each(function() {

        //     $(this).prop('checked', $(this).val() == data.status);

        // });

        

        $('#session_id').val(data.session_id);

        $('#whats_key').val(data.apikey);

        

        // if (data.paypal_live_check === 'on') {

        //     $('#paypalPaymentFor').prop('checked', true);

        // }

        // if (data.stripe_live_check === 'on') {

        //     $('#stripePaymentFor').prop('checked', true);

        // }

        // if (data.square_live_check === 'on') {

        //     $('#squarePaymentFor').prop('checked', true);

        // }

        

        // if (data.cash_check === 'on') {

        //     $('#cashPaymentFor').prop('checked', true);

        // }



        // $('#txtsquare_accessToken').val(data.txtsquare_accessToken);

        // $('#txt_square_appId').val(data.txt_square_appId);

        // $('#txt_square_locationId').val(data.txt_square_locationId);

        

        $('.test-div').show();

        

    }

    

    // save the payment setting

  	$('#saveBtn').click(function (e) {

        e.preventDefault();

        const url = 'whatsappsetting';

        var formdata = new FormData($('#formSettingsPayment')[0]); 

        // Append additional fields

        formdata.append('token', getCookie('d_token'));

        formdata.append('device_id', 0);

        let sessionId = document.getElementById("session_id").value;
        let apiKey = document.getElementById("whats_key").value;

        if (sessionId != '' && apiKey != '') {
            
            $.ajax({

                data: formdata,

                url: '{{ env('API_URL') }}' + url,

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

                        Swal.fire({

                            position: 'top-end',

                            icon: 'success',

                            title: 'Updated',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 2000,

                        })

                        showlist();

                    }

                },

                error: function (data) {

                    console.log('Error:', data);

                }

                });

        } else {
            Swal.fire({
                title: "Error",
                text: "Invalid API Key or Session ID. Please enter the correct values.",
                icon: "error",
                confirmButtonText: "Try Again"
            });
        }

    });

    

  	$('#testMessBtn').click(function (e) {

        e.preventDefault();

        const url = 'testwhats-message';

        let whats_id = $('#whatsappsetting_id').val();

        // let test_number = $('#test_number').val();

        let test_message = $('#test_message').val();

        let isValid = true;

        $('.error-message').remove();


        // Validate WhatsApp Setting ID

        if (whats_id === '') {

            $('#whatsappsetting_id').after('<span class="error-message text-danger">WhatsApp Setting ID is required.</span>');

            isValid = false;

        }



        // Validate Test Number (should be numeric and not empty)

        // if (test_number === '' || !/^\d+$/.test(test_number)) {

        //     $('#test_number').after('<span class="error-message text-danger">Enter a valid phone number.</span>');

        //     isValid = false;

        // }



        // Validate Test Message

        if (test_message === '') {

            $('#test_message').after('<span class="error-message text-danger">Message cannot be empty.</span>');

            isValid = false;

        }

        

        if(isValid){

            var formdata = new FormData(); 

            // Append additional fields

            formdata.append('token', getCookie('d_token'));

            formdata.append('device_id', 0);

            formdata.append('whats_id', whats_id);

            // formdata.append('whats_no', test_number);

            formdata.append('whats_message', test_message);

        

            $.ajax({

                data: formdata,

                url: '{{ env('API_URL') }}' + url,

                type: "POST",

                processData: false, 

                contentType: false,

                dataType: 'json',

                beforeSend: function () {

                    // Button Loading

                    $('#testMessBtn').html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);

                 },

                success: function (response) {

                    $('#testMessBtn').html('Send Message').prop('disabled', false);

                    if (response.status == 400) {

                        errornotify(response);

                    } else if (response.status == 500) {

                        warningClick('Error', response['error'], "danger");

                    } else if (response.status == 401) {

                        unauth();

                    } else if (response.status == 200) {

                        Swal.fire({

                            position: 'top-end',

                            icon: 'success',

                            title: 'Status',

                            text: response.message,

                            showConfirmButton: false,

                            timer: 2000,

                        })

                        // showlist();

                        $('.test-div').show();

                    }

                },

                error: function (data) {

                    $('#testMessBtn').html('Send Message').prop('disabled', false);

                    console.log('Error:', data);

                }

            });

            

        }

    });



        

</script>