<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form id="log_form">

            <div>
                <x-label for="username" :value="__('Username')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="username" :value="old('email')" autofocus />
            </div>

            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                
            </div>
        </form>
        <x-button id="log_sub" class="ml-3">
                    {{ __('Log in') }}
                </x-button>
    </x-auth-card>
</x-guest-layout>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.5/bootstrap-notify.min.js"></script>
<script>
    $(function(){
        if(getCookie('d_token')){
            window.location.href="/dashboard";
        }
        $('#log_sub').click(function(){
            // alert('test')
            var url = "login";
            var formdata = $('#log_form').serialize();
            var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['domain'] = window.location.host;
            formDataObject['device_id'] = 0;
            ajax_call(url,formDataObject)
        })
        
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
             warningClick("Success","Successfully Login","success")
             setCookie('d_token',response['token'],1)
             setCookie('user_name',response['user_name'],1);
             setCookie('user_email',response['user_email'],1);
             setCookie('domainName',window.location.host,1);
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
        
    })
    
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
