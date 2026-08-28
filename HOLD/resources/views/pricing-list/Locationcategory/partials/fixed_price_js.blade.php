<script>
$(document).ready(function(){
   
  showlist();  
});    

function showlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0,
        id:1
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}locationcategoryshow',
        method: 'POST', // Consider using GET if you're only fetching data
        data: formDataObject,
        success: function(response) {
            if (response.status === 200) {
                var data = response.data;
                
                data.forEach(function(item) {
                   
                $('#airportpickup').val(item.Airportpickup);
    $('#meetpickup').val(item.meetpickup);
                });
            } else {
                console.error('Error:', response.message);
            }
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}



    
  $('#update').click(function (e) {
    e.preventDefault();
   var airportpickup=$('#airportpickup').val();
    var meetpickup=$('#meetpickup').val();

    // Check if form exists before using it
    var form = $('#formPricing')[0];
    if (!form) {
        console.log('Form not found!');
        return;
    }

    // Initialize FormData after confirming the form exists
    var formdata = new FormData(form);

    // Append additional data to FormData after initialization
    formdata.append('token', getCookie('d_token')); // Assuming getCookie is a function that gets the cookie value
    formdata.append('device_id', 0);
    formdata.append('id', 1);
      formdata.append('Airportpickup',airportpickup);
        formdata.append('meetpickup',  meetpickup);

    $.ajax({
        data: formdata,
        url: "{{env('API_URL')}}locationcategoryupdate",
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

                if (response.message =="Data has been created successfully") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been created successfully',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(function () {
                        window.location.reload();

                    });
                } else if(response.message =="Package updated successfully!") {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Updated',
                        text: 'Data has been updated successfully',
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