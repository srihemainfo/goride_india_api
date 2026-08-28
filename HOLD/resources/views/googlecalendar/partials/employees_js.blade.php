<script>
  

    
    function showlist() {
    var formDataObject = {
        token: getCookie('d_token'),
        device_id: 0
    };

    // Make an AJAX request
    $.ajax({
        url: '{{env('API_URL')}}googlecallender',
        method: 'POST',
        data: formDataObject,
        success: function(response) {
            
         var data= response.data;  
         AssignValues(data);
          //console.log(response);
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}
        
    $(function(){
        showlist()
    })
    
    
       function AssignValues(data){
         //  alert(data);
        //console.log(data)
        $('#google_data_id').val(data.id);
        $('#googlecallender_check').val(data.google_callender_check);
        $('#googlecallender_id').val(data.google_callender_id);
        $('#googlecallender_json').val(data.google_callender_json);
    } 
    
    
    $('#add_saveBtn').on('click', function(){
          const url = 'googlecallenderstore';
        var formdata = $('#add_employeeForm').serialize();
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
      

</script>
