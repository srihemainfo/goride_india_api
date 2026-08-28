<script>
    $(function() {
        //Trigger client info fields

        var key = window.location.href;
        var segments = key.split('/');
        var lastSegment = segments.pop();
        // var_dump(lastSegment);die;
        // alert(lastSegment);
        alert("This is an alert message!");
        previewBooking(lastSegment)
        
        
        
       
    })


    

    function previewBooking(id) {
        console.log("bookingpreviewclick");

        const url = 'previewbooking';
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 1;
        formDataObject['book_id'] = id;
        var settings = {
            "url": "{{env('API_URL')}}" + url,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Content-Type": "application/json"
            },
            "data": JSON.stringify(formDataObject),
        };
        $.ajax(settings).done(function(response) {
            if (response['status'] == 200) {
                
                $('#one_way_payment_message').html(response['booking_details'].payment_message)
            }
            if (response['status'] == 400) {
                warningClick('Error', response['error'], "danger")
            }
            if (response['status'] == 500) {
                warningClick('Error', response['error'], "danger")
            }
            if (response['status'] == 401) {
                unauth()
            }
        });

    }

    
</script>