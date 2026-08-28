<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
function showlist() {
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    
    $.ajax({
        url: '{{env('API_URL')}}myprofile',
        type: 'POST',
        data: formDataObject,
        success: function(data) {
            let response =data;
             console.log(response.data.currency);
            console.log(response.data);
            if(response.data!=null){
            $('#profile_name').html(response.data.name);
            $('#companyname').html(response.data.cmpny_name);
            $('#email').html(response.data.email);
            $('#phone').html(response.data.phone);
            // $('#country').html(response.data.country);
            $('#currency').html(response.data.currency);
            $('#profileimage').attr('src',response.data.cmpny_logo);
            $('#profileimage1').attr('src',response.data.cmpny_logo);
            
            $('#edit_profile_name').val(response.data.name);
            $('#edit_companyname').val(response.data.cmpny_name);
            $('#edit_email').val(response.data.email);
             $('#editemailpass').val(response.data.email);
            $('#edit_phone').val(response.data.phone);
            $('#edit_currency').val(response.data.currency); 
            }
            else{
                alert("please update the profile")
            }
            $("#edit_profile_name_span").text("");
            $("#edit_currency_span").text("");
            $("#edit_companyname_span").text("");
            $("#edit_phone_span").text("");
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
    
    
    
    
}

$(document).ready(function() {
    showlist();
    // console.log('hiii');
});


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

$('#edit_profile').on('click', function(){ 
        console.log('hiiiiiiiiiiii')
        const url = 'createprofile';
        var formData = new FormData(document.getElementById('edit_profileForm'));
        formData.append('company_logo', document.getElementById('formFile').files[0]);
        var serializedData = $('#edit_profileForm').serializeArray();
        $.each(serializedData, function (key, input) {
            formData.append(input.name, input.value);
        });
        
        formData.append('token', getCookie('d_token'));
        formData.append('device_id', 0);
        
        let edit_profile_name = $("#edit_profile_name").val();
        let edit_currency = $("#edit_currency").val();
        let edit_companyname = $("#edit_companyname").val();
        let edit_phone = $("#edit_phone").val();
        
        let Validateverify = true;
        
        // console.log(edit_currency);
        if (edit_profile_name == '') {
            $("#edit_profile_name_span").text("This Field Is Required");
            Validateverify = false;
        }else if(edit_profile_name.length > 30){
            $("#edit_profile_name_span").text("Max Length 30 Characters");
            Validateverify = false;
        }else{
            $("#edit_profile_name_span").text("");
        }
        
        if (edit_currency == '' || edit_currency == null) {
            $("#edit_currency_span").text("This Field Is Required");
            Validateverify = false;
        } else {
            $("#edit_currency_span").text("");
        }
        
        if (edit_companyname == '') {
            $("#edit_companyname_span").text("This Field Is Required");
            Validateverify = false;
        } else if(edit_companyname.length > 30){
            $("#edit_companyname_span").text("Max Length 30 Characters");
            Validateverify = false;
        }else {
            $("#edit_companyname_span").text("");
        }
        if (edit_phone == '') {
            $("#edit_phone_span").text("This Field Is Required");
            Validateverify = false;
        } else {
            if(isNaN(edit_phone)){
                $("#edit_phone_span").text("This Field Is Required");
                Validateverify = false;
            }else{
                $("#edit_phone_span").text("");
                // Validateverify = true;
            }
        }
        // console.log(Validateverify)
        if(Validateverify){
            $('#edit_profile').html('<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;"><span class="visually-hidden">Loading...</span></div>');
            $.ajax({
                 "url": "{{env('API_URL')}}" + url,
                "method": "POST",
                "timeout": 0,
                "processData": false,
                "contentType": false,
                "mimeType": "multipart/form-data",
                "data": formData,
                "success":function (response) {
                    console.log(response);
                var jsonResponse = JSON.parse(response);
                var status=jsonResponse.status;
                var message=jsonResponse.message;
                console.log(status, 'hiii');
                if(status ==200){
                    Swal.fire({
                        position: "top-right",
                        icon: "success",
                        title: 'Profile Updated Successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                }
                if(status == 400){
                     Swal.fire({
                        position: "top-right",
                        icon: "danger",
                        title: 'No Changes Can Be Made.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        location.reload();
                    });
                }
                if(status == 500){
                    warningClick('Error', response['error'], "danger");
                }
                if(status == 401){
                    unauth();
                }
                $('#edit_profile').html(`<i class="fa fa-save"></i> Update`);
            }  
        })
        
            
        }else{
            $('#edit_profile').html(`<i class="fa fa-save"></i> Update`);
            
        }
        

});

//change password 

   $('#password_change_butn').on('click', function(){ 
        const url = 'profilepasswordchange';
        var serializedData = $('#password_profileForm').serializeArray();
        console.log(serializedData);
        
        var formData = new FormData();
        
        $.each(serializedData, function (key, input) {
            formData.append(input.name, input.value);
        });
        
        formData.append('token', getCookie('d_token'));
        formData.append('device_id', 0);
        
        var settings = {
            
        };
    $.ajax({
         "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "contentType": false,
        "mimeType": "multipart/form-data",
        "data": formData,
        "success":function (response) {
        var jsonResponse = JSON.parse(response);
        var status=jsonResponse.status;
        var message=jsonResponse.message;
        if(message =='Mail send successfully'){
         $("#otpshow").show();
         $("#password_change_butn").text(`Submit`);
         toastr.warning('Please fill the OTP');
        }else if (message == 'Enter your Password') {
          $("#passwordshow").show();
          $("#conformpasswordshow").show();
          toastr.warning('Please fill Password');
        }else if(message == 'Password Mismatch'){
          $('#password').val('');  
          $('#con_password').val(''); 
          toastr.warning('Please Fill same password');
        }else if(message =="Invalid Otp"){
         $('#otp').val(''); 
         toastr.warning('Please fill Valid otp');
        }
        if(message =="Password Changed successfully"){
              Swal.fire({
                // position: "top-right",
                icon: "success",
                title: message,
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                 location.reload();
            });   
            
            
        }
        if(status == 400){
             Swal.fire({
                // position: "top-right",
                icon: "danger",
                title: message,
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                 location.reload();
            });
        }
        if(status == 500){
            warningClick('Error', response['error'], "danger");
        }
        if(status == 401){
            unauth();
        }
    }  
    })
    
});
  



  
</script>
