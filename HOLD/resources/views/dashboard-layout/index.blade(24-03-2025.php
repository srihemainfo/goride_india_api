@php
use Illuminate\Support\Facades\DB;
$get_topbar = DB::connection('mysql1')->table('top_bar')->where('name','!=','Multi Booking')->get();
@endphp

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Go Ride - CRM Admin Dashboard</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src=" https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('dashboard-assets/css/main.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css"
        rel="stylesheet" />
        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
   
   <link rel="stylesheet" href="{{ asset('dashboard-assets/css/easy-autocomplete.min.css') }}">
   <link rel="stylesheet" href="{{ asset('dashboard-assets/css/easy-autocomplete.themes.min.css') }}">
   
   
   <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
    <script src="{{ asset('dashboard-assets/scripts/jquery.easy-autocomplete.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1476.0.min.js"></script>
<!-- Include Summernote's CSS and JS -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
   <style>
   
   .notificate .col {
       margin-left: -35px;
   }
   
   /*.notificate .col:nth-last-child(1) {*/
   /*    margin-left: 0;*/
   /*}*/
   
   .img-cd{
       padding: 16px 0px 0px 19px;
   }
      .img-flx{
              width: 270px !important;

         
      }
        .card {
            padding: 0px;
        }

        .card-header,
        .card-body {
            padding: .6rem;
        }

        tbody th,
        table.dataTable tbody td {
            padding: 3px 3px;
        }

        .select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important
        }

        .select2-container--default .select2-selection--single,
        .select2-selection .select2-selection--single {
            border: 1px solid #d2d6de;
            border-radius: 0;
            padding: 6px 12px;
            height: 34px
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #aaa;
            border-radius: 4px
        }

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 28px;
            user-select: none;
            -webkit-user-select: none
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-right: 10px
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            padding-right: 0;
            height: auto;
            margin-top: -3px
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 28px
        }

        .select2-container--default .select2-selection--single,
        .select2-selection .select2-selection--single {
            border: 1px solid #d2d6de;
            border-radius: 4px !important;
            padding: 6px 12px;
            height: 40px !important
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 24px;
            position: absolute;
            top: 6px !important;
            right: 1px;
            width: 20px
        }

        .tox-promotion {
            display: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
        }
        table.dataTable tbody tr {
    background-color: transparent;
    padding: 10px;
}
tbody th, table.dataTable tbody td {
    padding: 3px 0px;
    border: none;
}
.dt-table-1{
    color: #494949;
    font-weight: 500;
}
.dt-table-2{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 258px;
}
.dt-table-3{
    color: #494949;
    font-weight: 500;
}
.dt-table-4{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 258px;
}
.dt-table-5{
    color: #494949;
    font-weight: 500;
}
.dt-table-6{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 258px !important;
}
.dt-table-7{
    color: #494949;
    font-weight: 500;
}
.dt-table-8{
    color: #494949;
    font-weight: 500;
    /*margin: 0 0px 6px 0;*/
}
.dt-table-9{
    color: #494949;
    font-weight: 500;
}
.dt-table-10{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 258px;
}
.dt-table-11{
    color: #494949;
    font-weight: 500;
    margin: -25px 0 0 500px;
}
.dt-table-12{
    color: #494949;
    font-weight: 500;
    display: flex;
    justify-content: center;
    /* margin: -25px 0 0 258px; */
    /* width: 19%; */
}

.dt-table-12 select {
    width: max-content;
}

.dt-table-13 {
    position: absolute;
    top: 9px;
    right: 1%;
}
.border-dashed{
    width: 2px;
    height: 25px;
    background-image: linear-gradient(1800deg, transparent, transparent 50%, #fff 50%, #fff 100%), linear-gradient(180deg, black, black, black, black, black);
    background-size: 3px 20px, 100% 20px;
    border: none;
    margin: 0 0 0 7px;
}

@media screen and (max-width: 600px) {
  .dt-table-2 {
    margin: 0 !important;
  }
  .dt-table-4 {
    margin: 0 !important;
  }
  .dt-table-6 {
    margin: 0 !important;
  }
  .dt-table-8 {
    margin: 0 !important;
  }
  .dt-table-10 {
    margin: 0 !important;
  }
  .dt-table-12 {
    margin: 0 !important;
    width:100% !important;
  }
  .dt-table-13 {
    position: unset;
    /*top: 9px;*/
    /*right: 1%;*/
}
}
@media screen and (min-width: 320px) and (max-width: 740px) {
  .dropdown-menu {
    margin-top: -67px !important;
  }
}

.db-standard{
    border: 1px solid #ffefef;
    box-shadow: inset 0px 4px 4px 0px rgba(0, 0, 0, 0.24);
    background: #e1e1e1 !important;
}
table.dataTable.no-footer {
   border:none;
}

/*AUTO COMPLETE STYLE*/
.easy-autocomplete-container ul {
    position: absolute;
    background: white;
    color: black;
    list-style: none;
    width: 100%;
    /* padding: 0px 5px; */
    z-index: 999;
}
.easy-autocomplete{
    width: 100% !important;
}
.easy-autocomplete-container ul li, .easy-autocomplete-container ul .eac-category {
    background: inherit;
    border-color: #ccc;
    border-image: none;
    border-style: solid;
    border-width: 0 1px;
    display: block;
    font-size: 13px;
    font-weight: normal;
    padding: 4px 12px;
    cursor: pointer;
    border: 1px solid #e6e6e6 !important;
}
.easy-autocomplete-container .fa-plane-departure {
    width: 15px;
    height: 20px;
    float: left;
    margin-right: 7px;
    margin-top: 6px;
    color: #f3ba00;
}
.eac-item {
    color: #000;
}

.note-editable{
    height: 426.254px;
}


 /*Loading CSS*/

    .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.2);
            z-index: 9999;
        }
    .loader {
        position: fixed;
        z-index: 99999;
        height: 1em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        font-weight: bold;
        font-family: monospace;
        font-size: 30px;
        line-height: 1.2em;
        display: inline-grid;
        pointer-events: none
    }
    .loader:before,
    .loader:after {
        content: "Loading...";
        grid-area: 1/1;
        -webkit-mask: linear-gradient(90deg, #000 50%, #0000 0) 0 50%/2ch 100%;
        color: #0000;
        text-shadow: 0 0 0 #000, 0 calc(var(--s, 1)*1.2em) 0 #000;
        animation: l15 1s infinite;
    }
    .loader:after {
        -webkit-mask-position: 1ch 50%;
        --s: -1;
    }
    @keyframes l15 {

            80%,
            100% {
                text-shadow: 0 calc(var(--s, 1)*-1.2em) 0 #000, 0 0 0 #000
            }
        }


    </style>
    <script type="text/javascript" src='{{ asset('assets/js/common.js') }}'></script> 


<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js" integrity="sha512-a+SUDuwNzXDvz4XrIcXHuCf089/iJAoN4lmrXJg18XnduKK6YlDHNRalv4yd1N40OKI80tFidF+rqTFKGPoWFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
    
        const loader =  $('.loading-overlay');
        const token = getCookie('d_token');
        const device_id = 0;
        var formDataObject  = {};
        formDataObject['token'] = token;
        formDataObject['device_id'] = device_id;
        
    //   function getCookie(name) {
    //         const cookieName = `${name}=`;
    //         const cookies = document.cookie.split(';');
    //         for (let i = 0; i < cookies.length; i++) {
    //           let cookie = cookies[i];
    //           while (cookie.charAt(0) === ' ') {
    //             cookie = cookie.substring(1);
    //           }
    //           if (cookie.indexOf(cookieName) === 0) {
    //             return cookie.substring(cookieName.length, cookie.length);
    //           }
    //         }
    //         return null;
    //     }
        
        
    </script>
    
</head>

<body>
    
    <div class="loading-overlay" style='display:none;' >
        <div class="loader"></div>
    </div>
    
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div class="logo-src"></div>
                <div class="header__pane ml-auto" id="sidebar_menu_opener">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                            data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="row notificate">
                                        @foreach($get_topbar as $gettopbar)  
                                        
                                        <div class="col">
                                                
                                                <ul class="vertical-nav-menu metismenu">
                                                    <li style="margin-top: -5px;" title="Unread Notifications">
                                                        <a href="{{ route($gettopbar->route) }}">
                                                {!! $gettopbar->icon !!}
                                                {{ $gettopbar->name ? $gettopbar->name : get_notification_counts() }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endforeach 
       
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="btn-group" id="profile-bar">
                                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            class="p-0 btn">
                                            <i class="fa fa-angle-down ml-2"></i>
                                        </a>
                                        <div tabindex="-1" role="menu" aria-hidden="true"
                                            class="dropdown-menu dropdown-menu-right" id="profile-bar-box">
                                            <a href="{{ route('profile.myprofile') }}" type="button" tabindex="0" class="dropdown-item"><i
                                                    class="fas fa-user"></i> Profile</a>
                                            <div tabindex="-1" class="dropdown-divider"></div>
                                            <a type="button" href="#" onclick="LogoutScript(event)"
                                                tabindex="0" class="dropdown-item"><i
                                                    class="fas fa-sign-out-alt"></i>Logout</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading">
                                        {{ $_COOKIE['user_name'] }}
                                    </div>
                                    <div class="widget-subheading">
                                        {{ $_COOKIE['user_email'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-main">

            {{-- Sidebar --}}
            @include('dashboard-layout.sidebar')

            {{-- Main --}}
            <div class="app-main__outer">
                <div class="app-main__inner">
                    <div class="row justify-content-start">
                        @yield('content')
                    </div>
                </div>
                <div class="app-wrapper-footer">
                    <div class="app-footer">
                        <div class="app-footer__inner">
                            <div class="app-footer-left">

                            </div>
                            <div class="app-footer-right">
                                <strong>Copyright © {{ date('Y') }} <a href="{{ url('') }}">
                                    Go Ride.</a>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end footer-->
            </div>

        </div>
    </div>
    <script src="{{ asset('dashboard-assets/scripts/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://unpkg.com/bootprompt"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.2.0/tinymce.min.js"
        integrity="sha512-tofxIFo8lTkPN/ggZgV89daDZkgh1DunsMYBq41usfs3HbxMRVHWFAjSi/MXrT+Vw5XElng9vAfMmOWdLg0YbA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('dashboard-assets/scripts/notify.min.js') }}"></script>

    <script>
    
   
    
    
    window.addEventListener("load", function() {
        $('.app-container').addClass('closed-sidebar');
    });
        
        function unauth(){
            Swal.fire({
             title: 'Error..!',
             text: 'Kindly login again',
             icon: 'warning',
             confirmButtonText: 'Ok, Thank you!',
          }).then(function() {
             deleteCookie('d_token');
             location.reload();
            });
        }
        
        function deleteCookies() {
            const cookiesName = ["d_token", "user_name", "user_email"];
        
            cookiesName.forEach(function(cookieName) {
                // Set the cookie's expiration date to a time in the past
                document.cookie = cookieName + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
            });
        }
     
        

        function LogoutScript(event) {
            var url = 'destroy-auth';
            var data = {
                'token': getCookie('d_token'),
                'device_id': 0
            };
            console.log(data);
            var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(data),
         };
             console.log(settings);
        $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             
             var message = response['message'] ;
             
             $.ajax({
            url: `/destroy-session/{{session()->getId()?? 'N/A'}}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                // console.log(data.message);
                
                deleteCookies();
            
                warningClick("Success",message,"success")
                deleteCookie('d_token');
                 window.location.href = '/login';
                // location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                // console.log('Response:', xhr.responseText);
                // alert('An error occurred. Please try again.');
            }
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
        }

        $('.inputDate').datepicker({
            format: "dd-mm-yyyy"
        });

        $(document).ready(function() {
            //Datatable
            $('#example').DataTable();

            //Select2 Dropdown
            $('.select2').select2();
        });

        // $("#profile-bar").on('click', function() {
        //     $("#profile-bar-box").toggleClass("show");
        // });
        
        $("#profile-bar").on('click', function(e) {
            e.stopPropagation(); // Prevent the click event from propagating
            $("#profile-bar-box").toggleClass("show");
        });

        // Hide the profile bar box if clicked outside of the box
        $(document).on('click', function() {
            $("#profile-bar-box").removeClass("show");
        });
    
        // Prevent hiding the box if the click is inside the box
        $("#profile-bar-box").on('click', function(e) {
            e.stopPropagation();
        });

        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
        });

        const channel = pusher.subscribe('booking-notification');

        channel.bind('web-notification-event', function(data) {
            console.log('From Pusher: ' + data.booking_id)

            $.ajax({
                data: {
                    booking_id: data.booking_id,
                    driver_id: data.driver_id,
                    status: data.status
                },
                url: "{{ route('StoreBookingNotification') }}",
                type: "POST",
                dataType: 'json',
                success: function(response) {
                    $('#notification_count').text(response.notification_count)

                    if (response.status == 200 && response.status_id == 1) {
                        $.notify(`Booking ID: ${response.booking_id} was accepted by driver.`, {
                            autoHide: true,
                            autoHideDelay: 3000,
                            className: 'success',
                            globalPosition: 'bottom right',
                        });
                    } else if (response.status == 200 && response.status_id == 0) {
                        $.notify(`Booking ID: ${response.booking_id} was rejected by driver.`, {
                            autoHide: true,
                            autoHideDelay: 3000,
                            className: 'error',
                            globalPosition: 'bottom right',
                        });
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
    </script>

    @yield('custom_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.5/bootstrap-notify.min.js"></script>
    <script>
        $(function(){
            $.ajax({
                  type: "POST",
                  url: "{{ route('mycheck') }}",
                  cache: false,
                  success: function(data){
                     if(data == "2"){
                         location.reload();
                     }
                  }
                });
            if(getCookie('swal') != null){
                    Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: getCookie('swal'),
                       showConfirmButton: false,
                       timer: 1500
                   });
                  deleteCookie('swal')
            }
           // brands('')
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
      console.log(settings);
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             warningClick("Success",response['message'],"success")
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
        
//         function setCookie(name, value, daysToExpire) {
//     const date = new Date();
//     date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
//     const expires = `expires=${date.toUTCString()}`;
//     const secureFlag = location.protocol === 'https:' ? '; secure' : '';
//     document.cookie = `${name}=${value}; ${expires}; path=/${secureFlag}`;
//   }

      // Get the value of a cookie by name
    //   function getCookie(name) {
    //     const cookieName = `${name}=`;
    //     const cookies = document.cookie.split(';');
    //     for (let i = 0; i < cookies.length; i++) {
    //       let cookie = cookies[i];
    //       while (cookie.charAt(0) === ' ') {
    //         cookie = cookie.substring(1);
    //       }
    //       if (cookie.indexOf(cookieName) === 0) {
    //         return cookie.substring(cookieName.length, cookie.length);
    //       }
    //     }
    //     return null;
    //   }

  // Delete a cookie by name
//   function deleteCookie(name) {
//     this.setCookie(name, '', -1); // Setting an expired date deletes the cookie
//   }
  
      function brands(id,ref_id){
          const url = 'brands';
          var formDataObject  = {};
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
             var sel = '<option value="">select</option>';
             for(i=0; i < response['data'].length; i++){
                 sel += `<option value="${response['data'][i].id}" ${response["data"][i].id == id ? "selected" : "" }>${response['data'][i].brand}</option>`;
             }
            $('#'+ref_id).html(sel)
             }
         if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      }
      
      function models(id,sel_id,ref_id){
          const url = 'models';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['brand'] = id;
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
             var sel = '<option value="">select</option>';
             for(i=0; i < response['data'].length; i++){
                 sel += `<option value="${response['data'][i].id}" ${response["data"][i].id == sel_id ? "selected" : "" }>${response['data'][i].model_name}</option>`;
             }
            $('#'+ref_id).html(sel)
             }
         if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      }
      
      function veh_types(br_id,md_id,sel_id,ref_id,status){
          const url = 'vehichlelist';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['brand'] = br_id;
          formDataObject['model'] = md_id;
          formDataObject['status'] = status;
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
             var sel = '<option value="">select</option>';
             for(i=0; i < response['data'].length; i++){
                 sel += `<option value="${response['data'][i].name}" ${response["data"][i].name == sel_id ? "selected" : "" }>${response['data'][i].name}</option>`;
             }
            $('#'+ref_id).html(sel)
             }
         if(response['status'] == 400){
             warningClick('Error',response['message'],"danger")
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
      }
      
      
      function swalalerterror(message){
       return   Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Failed',
            text: message,
            showConfirmButton: false,
            timer: 3000,
        })
    
    }

    function swalalertsuccess(message){
       return   Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Success',
            text: message,
            showConfirmButton: false,
            timer: 3000,
        })
        
    }
    
      function validateInput(input) {
            input.value = input.value
                .replace(/[^0-9]/g, '')  // Remove any non-numeric and non-period characters
                .replace(/(\..*)\./g, '$1');  // Allow only one period
        }
        
        
        setTimeout(function() {
          try{
                // $('a[href="https://richtexteditor.com/?go=RTE"]').css('visibility', 'hidden');
                
                $('a[href="https://richtexteditor.com/?go=RTE"]').css('display', 'none');

          } catch(e){
              console.log(`Error: ${e.message}`);
          }
          
        }, 2000);
    </script>

</body>

</html>
