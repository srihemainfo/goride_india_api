<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
.relative {
    position: relative;
}

#togglePassword, #toggleConfirmPassword {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

input[type="password"] {
    padding-right: 35px; /* Add space on the right for the eye icon */
}

  </style>
<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" id="change_passform">

            <!-- Password Reset Token -->
            <input type="hidden" name="remember_token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" id="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">

                <x-label for="password" :value="__('Password')" />

                <div class="relative">

                <x-input id="password" class="block mt-1 w-full" type="password" id="password" name="password" required />
            
                <span id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" style="display:none;">

                  <i class="fa fa-eye"></i>  <!-- Eye icon -->

                  </span>

              </div>

            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />

                <div class="relative">

                <x-input id="password_confirmation" class="block mt-1 w-full"
                                    type="password"
                                    name="password_confirmation" required />

                  <span id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" style="display:none;">

                    <i class="fa fa-eye"></i>  <!-- Eye icon -->

                </div>
             </div>
        </form>
        <div class="flex items-center justify-end mt-4">
                <x-button onclick="reserpassword()">
                    {{ __('Reset Password') }}
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
    
    function reserpassword(){
        
        if($('#password').val() != $('#password_confirmation').val()){
            warningClick("Warning","Please enter the correct confirmation password...!","warning")
            return false;
        }
        var url = "cn-pass";
            var formdata = $('#change_passform').serialize();
            var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['domain'] = window.location.host;
            $('#forgot_btn').html('<i class="fa-solid fa-spinner fa-spin" style="font-size: 19px;"></i>')
            ajax_call(url,formDataObject)
        }
        
        function ajax_call(url,data){
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(data),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             warningClick("Success",response['message'],"success")
             var url = "login";
            var formDataObject  = {};
            formDataObject['username'] = $('#email').val();
            formDataObject['password'] = $('#password').val();
            formDataObject['domain'] = window.location.host;
            formDataObject['device_id'] = 0;
            ajax_call1(url,formDataObject)
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 300){
            errornotify(response)
         }
      });
    }
    
    function ajax_call1(url,data){
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(data),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             warningClick("Success","Successfully Login","success")
             setCookie('d_token',response['token'],1)
             setCookie('user_name',response['user_name'],1);
             setCookie('user_email',response['user_email'],1);
             setCookie('domainName', window.location.host, 1);
             localStorage.removeItem('invalidAttempts');
             window.location.href="/dashboard";
             }
         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 300){
            errornotify(response)
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
    

  $(document).ready(function() {
  const $passwordField = $("#password");
  const $confirmPasswordField = $("#password_confirmation");
  const $emailField = $("#email");
  const $togglePassword = $("#togglePassword");
  const $toggleConfirmPassword = $("#toggleConfirmPassword");

  $passwordField.on("focus", () => $togglePassword.show());
  $passwordField.on("blur", () => $togglePassword.toggle($passwordField.val().length > 0));

  $emailField.on("focus", () => $togglePassword.hide());

  $togglePassword.on("click", () => {
    $passwordField.attr("type", $passwordField.attr("type") === "password" ? "text" : "password");
    $togglePassword.find("i").toggleClass("fa-eye fa-eye-slash");
  });

  $confirmPasswordField.on("focus", () => $toggleConfirmPassword.show());
  $confirmPasswordField.on("blur", () => $toggleConfirmPassword.toggle($confirmPasswordField.val().length > 0));

  $toggleConfirmPassword.on("click", () => {
    $confirmPasswordField.attr("type", $confirmPasswordField.attr("type") === "password" ? "text" : "password");
    $toggleConfirmPassword.find("i").toggleClass("fa-eye fa-eye-slash");
  });
});

$(document).ready(function() {
    const url = '404';
    var formDataObject = {};
    formDataObject['token'] = getCookie('d_token');
    formDataObject['device_id'] = 0;
    formDataObject['domain'] = window.location.host;

    var settings = {
        "url": "{{env('API_URL')}}"+url,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(formDataObject),
        "success": function(response) {
            console.log(response);
        },
        "error": function(xhr, status, error) {
            console.error("Error: ", error);
            window.location.href = "/404"; 
        }
    };

    $.ajax(settings);
});
</script>
