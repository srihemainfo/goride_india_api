<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.2/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.2/dist/sweetalert2.min.js"></script>

<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" id="forget_form">

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

<x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', '')" required autofocus oninput="validateEmailInput(this)" maxlength="254" />
            </div>
            <div id="timeRemaining">
    <span></span>
</div>
            
        </form>
        <div  class="flex items-center justify-end mt-4">
        
                <x-button onclick="verifymail()" id="forgot_btn">
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
    </x-auth-card>
</x-guest-layout>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.5/bootstrap-notify.min.js"></script>
<script>
function validateEmailInput(input) {
     let value = input.value.replace(/[^a-z0-9._()@]/g, ''); 

    let atCount = (value.match(/@/g) || []).length;
    if (atCount > 1) {
        value = value.replace(/@/g, '', atCount - 1);
    }
    input.value = value;
}


function verifymail() {
    var email = $('#email').val();  
    if (!email) {  
        Swal.fire({
            icon: 'error',
            title: 'Email Required',
            text: 'Please enter an email address.',
        });
        return;  
    }
    var emailPattern = /^[a-z0-9._()]+@[a-z0-9.-]+\.[a-z]{2,6}$/;
    if (!emailPattern.test(email)) {  
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
        });
        return;
    }

    var attempts = JSON.parse(localStorage.getItem('resetAttempts')) || { count: 0, timestamp: Date.now() };

    var currentTime = Date.now();
    var oneHour = 60 * 60 * 1000; 


    if (currentTime - attempts.timestamp > oneHour) {
        attempts = { count: 0, timestamp: currentTime };
        localStorage.setItem('resetAttempts', JSON.stringify(attempts));
    }

    if (attempts.count >= 3) {
        var remainingTime = (oneHour - (currentTime - attempts.timestamp)) / 1000;
        
        updateRemainingTime(remainingTime);

        Swal.fire({
            title: 'Request Limit Exceeded',
            text: `You have exceeded the limit of 3 requests. Please try again in ${Math.floor(remainingTime / 60)} minutes and ${Math.floor(remainingTime % 60)} seconds.`,
            icon: 'warning',
            confirmButtonText: 'OK',
            timer: 5000, // Auto-close the alert after 5 seconds
        });
        
        return;
    }

    attempts.count += 1;
    localStorage.setItem('resetAttempts', JSON.stringify(attempts));

    var url = "resetlink"; // Your API endpoint
    var formdata = $('#forget_form').serialize();
    var pairs = formdata.split('&');
    var formDataObject = {};

    // Convert form data to an object
    for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        var key = decodeURIComponent(pair[0]);
        var value = decodeURIComponent(pair[1]);
        formDataObject[key] = value;
    }

    var domain = window.location.hostname; 
    formDataObject['domain'] = domain;

    $('#forgot_btn').html('<i class="fa-solid fa-spinner fa-spin" style="font-size: 19px;"></i>');

    // Call the AJAX function
    ajax_call(url, formDataObject);
}

function updateRemainingTime(remainingTime) {
    var intervalId = setInterval(function() {
        remainingTime--; // Decrease the remaining time by 1 second
        
        var minutes = Math.floor(remainingTime / 60);
        var seconds = Math.floor(remainingTime % 60);
        
        // Display the remaining time in the span
        document.getElementById('timeRemaining').querySelector('span').textContent = `Please try again in ${minutes} minutes and ${seconds} seconds.`;

        // If the remaining time is 0 or less, clear the interval and reset the message
        if (remainingTime <= 0) {
            clearInterval(intervalId);
            document.getElementById('timeRemaining').querySelector('span').textContent = 'You can now request the reset link again.';
        }
    }, 1000); // Update every second
}

function ajax_call(url, data) {
    var settings = {
        "url": "{{env('API_URL')}}" + url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(data),
    };

    $.ajax(settings).done(function (response) {
        if (response['status'] == 200) {
            warningClick("Success", response['message'] + '. Kindly check your email', "success");
            $('#forgot_btn').html('{{ __('Email Password Reset Link') }}');
        }
        if (response['status'] == 400) {
            errornotify(response);
            setTimeout(function () {
                window.location.reload(); 
            }, 1500); // 1.5 seconds delay            
        }
        if (response['status'] == 300) {
            errornotify(response);
            setTimeout(function () {
                window.location.reload(); 
            }, 1500); // 1.5 seconds delay
        }
    });
}
    
    function errornotify(response){
       var title = "Required";
        const obj = response['errors'];
        const arrayOfObjects = [];
        for (const key in obj) {
              if (obj.hasOwnProperty(key)) {
                warningClick(title,response['errors'][key][0],"danger")
             }
       }
       if(response['message']){
           warningClick("Error",response['message'],"warning")
       }
    }
    
    function warningClick(ttl,msg,c_type){
            $.notify({
    	// options
    	title: '<strong>'+ttl+'</strong>',
    	message: "<br>"+msg+"",
      icon: 'glyphicon glyphicon-warning-sign',
    },{
    	// settings
    	element: 'body',
    	position: null,
    	type: c_type,
    	allow_dismiss: true,
    	newest_on_top: false,
    	showProgressbar: false,
    	placement: {
    		from: "top",
    		align: "right"
    	},
    	offset: 20,
    	spacing: 10,
    	z_index: 1031,
    	delay: 3300,
    	timer: 1000,
    	url_target: '_blank',
    	mouse_over: null,
    	animate: {
    		enter: 'animated bounceIn',
    		exit: 'animated bounceOut'
    	},
    	onShow: null,
    	onShown: null,
    	onClose: null,
    	onClosed: null,
    	icon_type: 'class',
      });
    }
    
    function setCookie(name, value, daysToExpire) {
    const date = new Date();
    date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    const secureFlag = location.protocol === 'https:' ? '; secure' : '';
    document.cookie = `${name}=${value}; ${expires}; path=/${secureFlag}`;
  }

  // Get the value of a cookie by name
  function getCookie(name) {
    const cookieName = `${name}=`;
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i];
      while (cookie.charAt(0) === ' ') {
        cookie = cookie.substring(1);
      }
      if (cookie.indexOf(cookieName) === 0) {
        return cookie.substring(cookieName.length, cookie.length);
      }
    }
    return null;
  }

  // Delete a cookie by name
  function deleteCookie(name) {
    this.setCookie(name, '', -1); // Setting an expired date deletes the cookie
  }
    
</script>
