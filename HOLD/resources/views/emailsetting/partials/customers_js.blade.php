<script>

$(document).ready(function(){

    $("#from_email").on("input", function () {
        let emailValue = $(this).val();
        $("#smtp_user_name").val(emailValue); // Copy value to second field
    });

  showlist(); 
  
//   $('#mailer_type1').click(function(){
//       $('.email-hide').hide();
//   })
  
//   $('#mailer_type2').click(function(){
//       $('.email-hide').show();
//   })

  $('#EmailModal').modal('show')

});



function showlist() {

    var formDataObject = {

        token: getCookie('d_token'),

        device_id: 0

    };



    // Make an AJAX request

    $.ajax({

        url: '{{env('API_URL')}}emailsetting',

        method: 'POST',

        data: formDataObject,

        success: function(response) {

            //  // console.log(response.data);

         var data= response.data;
         var company_name= response.company_name;

        //  AssignValues(data, company_name);
         AssignValues1(data, company_name);

        //   console.log(company_name.company_name);

        },

        error: function(error) {

            // console.error('Error fetching data:', error);

        }

    });

}

        function AssignValues1(data, company_name){

                $('#from_name').val(company_name.company_name);

                $('#emailsettingid').val(company_name.id);
                
                // $('input[name="mailer_type"]').filter(`[value="${emailType}"]`).prop('checked', true);
    
    
        }



function AssignValues(data, company_name){
        $('.email-hide').hide();

        if(data && data != undefined){

            let emailType = data.mailer_type;

            $('input[name="mailer_type"]').filter(`[value="${emailType}"]`).prop('checked', true);

            if(emailType == 'GoRide'){

                if (data.from_name !== '') {

                    $('#comp-path').addClass('active');

                }

                $('#from_name').val(company_name.company_name);

                $('#emailsettingid').val(company_name.id);
        
                $('.email-hide').hide();
            
                

            }else if(emailType == 'SMTP'){
                
                $('.email-hide').show();

                if (data.from_name !== '') { // Strict comparison for better type checking

                    $('#comp-path').addClass('active'); // Use jQuery's addClass method to add a class

                }

                $('.email-hide').show();

                $('#emailsettingid').val(data.id);

                $('#from_email').val(data.from_email);

                $('#from_name').val(data.from_name);

                $('#mailer_type2').val(data.mailer_type);
                
                // $('#mailer_type1').val('GoRide');

                $('#smtp_host').val(data.smtp_host);

                $('#smtp_port').val(data.smtp_port);

                $('#encryption_type').val(data.encryption_type);

                $('#smtp_user_name').val(data.smtp_user_name);

                $('#smtp_password').val(data.smtp_password);

            }

            

        }



    }
    
$('#encryption_type').change(function(){
    
    if($('#encryption_type').val() == 'TLS'){
        
        $('#smtp_port').val('587');
        
    }else if($('#encryption_type').val() == 'SSL'){
        
        $('#smtp_port').val('465');
        
    }
    
});

    

$('#saveBtn').click(function (e) {

    submitForm(e, 0)

    $('#saveBtn').attr('disabled', true).html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">

                    <span class="visually-hidden">Loading...</span>

                </div>`);

        });

$('#prev-btn').click(function (e) {

    submitForm(e, 1)

    $('#prev-btn').attr('disabled', true).html(`<div class="spinner-border" role="status" style="width: 1rem !important; height: 1rem !important;">

                    <span class="visually-hidden">Loading...</span>

                </div>`);

});



function submitForm(e, formNumber){

    e.preventDefault();



    const url = 'emailsettingstore';

    var formdata = new FormData($('#formSettingsEmail')[0]); // FormData object



    // Append additional fields

    formdata.append('token', getCookie('d_token'));

    formdata.append('device_id', 0);



    $.ajax({

        data: formdata,

        url: "{{env('API_URL')}}" + url,

        type: "POST",

        processData: false, // Important for FormData

        contentType: false, // Important for FormData

        dataType: 'json',

        success: function (response) {
             $('#prev-btn').html('Previous').attr('disabled', false);
             $('#saveBtn').html('Previous').attr('disabled', false);

            if (formNumber) {

                $('#prev-btn').html(`Save and Previous`);

            } else {

                $('#saveBtn').html(`Submit`);

            }

            // // console.log(response)

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                warningClick('Error', response['error'], "danger");

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {



                    if (response.message =="Email Configured Successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Success',

                        text: 'Email Configured Successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        // window.location.href = '/emailsetting';

                        if (formNumber) {

                            window.location.href = '/paymentOption';

                        } else {

                            let curr_url  = window.location.pathname;

                            if(curr_url == '/emailSetting'){

                                window.location.href = '/booking/create';

                            }else{

                                window.location.reload();

                            }

                        }

                        

                    });

                } else if(response.message =="Email Configuration Updated successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Success',

                        text: 'Email Configuration Updated successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        if (formNumber) {

                            window.location.href = '/paymentOption';

                        } else {

                            let curr_url  = window.location.pathname;

                

                            if(curr_url == '/emailSetting'){

                                window.location.href = '/general';

                            }else{

                                window.location.reload();

                            }

                        }



                    });

                }

            }

            

            

        },

        error: function (data) {
            $('#prev-btn').html('Previous').attr('disabled', false);
            $('#saveBtn').html('Previous').attr('disabled', false);
            // // console.log('Error:', data);

            if (formNumber) {

                $('#prev-btn').html(`Save and Previous`);

            } else {

                $('#saveBtn').html(`Submit`);

            }

        }

    });

}



$('#sbtSendEmail').click(function (e) {

    e.preventDefault();



    const url = 'emailTest';

    var formdata = new FormData($('#emailForm')[0]);



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

            // // console.log(response.status)

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                warningClick('Error', response['error'], "danger");

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {



                if (response.message) {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: '',

                        text: response.message,

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        let curr_url  = window.location.pathname;

                        

                        if(curr_url == '/bookingSetting'){

                            window.location.href = '/create-fleet';

                        }else{

                            window.location.reload();

                        }

                    });

                }

            }

        },

        error: function (data) {

            // // console.log('Error:', data);

        }

    });

});

        
function checkEmailType(element){

    // let emailType = 'GoRide';

    var emailType = $(element).val();

    console.log(emailType)

    if(emailType == 'GoRide'){

        

        $('.email-hide').hide();

        

    }else if(emailType == 'SMTP'){

        

        $('.email-hide').show();

        // $('#emailsettingid').val('');

        // $('#from_email').val('');

        // $('#from_name').val('');

        // // $('input[name="mailer_type"]').prop('checked', false);

        // $('#smtp_host').val('');

        // $('#smtp_port').val('');

        // $('#encryption_type').val('');

        // $('#smtp_user_name').val('');

        // $('#smtp_password').val('');



        

    }

}



        

</script>