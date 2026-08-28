@php

$get_parrent_menu = DB::connection('mysql1')->table('side_bar_parrent')->get();
$apiUrl = env('API_URL') . 'getfaretype_data';

// Get token from cookie
$token = $_COOKIE['d_token'];

// Optional: fallback for testing
// $token = $token ?? 'your-test-token-value';

// Prepare data
$postData = [
    'token' => $token,
    'device_id' => 0,
    'jana' => 0,
];

//dd($token); 

// Initialize cURL
$ch = curl_init($apiUrl);

// Set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
]);

// Execute request
$response = curl_exec($ch);

// Handle errors
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    dd("Curl error: " . $error_msg);
}

// Close connection
curl_close($ch);

// Decode JSON
$data = json_decode($response, true);

// Extract your needed value
$fare_data_count = $data['data']['fare_type'] ?? null;

// Output or use it
//dd($fare_data_count);
@endphp



<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
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
                style="background-color:#3f6ad8;"class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner position-relative">
                <ul class="vertical-nav-menu metismenu">
                    @php
                       
                    $sessionPermission = session('new_permission');
                     //dd(Request::url());
                    @endphp
                   
                     @foreach($get_parrent_menu as $getparrentmenu)  
                @php
            
                    $childItems = DB::connection('mysql1')->table('side_bar_chiled_menu')->where('parent_id', $getparrentmenu->id)->get();
                    $isActiveParent = false;
                    
                   foreach ($childItems as $childItem) {
                    if (Request::url() === url($childItem->route)) {
                        $isActiveParent = true;
                        break; // Stop checking further if a match is found
                    }
                }
                    
                    $permissionmenu = $getparrentmenu->menu_model;
                    
                    $hasActiveChild = $childItems->contains(function ($childItem) {
                        return strtolower(Request::segment(3)) === strtolower($childItem->route);
                    });
                    
                @endphp
                @if($getparrentmenu->menu_model === $permissionmenu)
                 @if(isset($sessionPermission["{$permissionmenu}_read"]) && $sessionPermission["{$permissionmenu}_read"])
                <li class="{{ $isActiveParent || $hasActiveChild ? 'mm-active' : '' }}">
                    <a href="{{ $getparrentmenu->route === 'javascript:void(0)' ? 'javascript:void(0)' : route($getparrentmenu->route) }}" 
                       class="{{ $isActiveParent || $hasActiveChild ? 'mm-active' : '' }}">
                        {!! $getparrentmenu->icon !!}
                        {{ $getparrentmenu->name }}
                        {!! $getparrentmenu->icon2 !!}
                    </a>
                 @endif
                @endif
                    @if($childItems->isNotEmpty())
                        <ul class="mm-collapse">
                            @foreach($childItems as $childItem)
                            @php
                            $permissionmenuget=$childItem->menu_model;
                            @endphp
                            @if($childItem->menu_model === $permissionmenuget)
                             @if(isset($sessionPermission["{$permissionmenuget}_read"]) && $sessionPermission["{$permissionmenuget}_read"])
                                
                                @if(isset($book_sets) && $childItem->name != 'TFL UK Report')
                                
                                    <li>
                                         <a href="{{ url($childItem->route) }}" class="{{ request()->is(strtolower($childItem->route) . '*') ? 'mm-active' : '' }}">
                                            {!! $childItem->icon !!} 
                                            {{ $childItem->name }}
                                        </a>
                                    </li>
                                @elseif(isset($book_sets) && $book_sets['country'] == 'United Kingdom' && $childItem->name == 'TFL UK Report')
                                
                                    <li>
                                         <a href="{{ url($childItem->route) }}" class="{{ request()->is(strtolower($childItem->route) . '*') ? 'mm-active' : '' }}">
                                            {!! $childItem->icon !!} 
                                            {{ $childItem->name }}
                                        </a>
                                    </li>
                                @endif
                         @endif
                         @endif
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach  
            
            
                </ul>
        </div>
        <style>
           .playstore li a{
               text-decoration: none;
               color: white;
           }
           .playstore {
                display: flex;
                gap: 23px;
                list-style: none;
                padding: 0;
                position: absolute;
                left: 20px;
                bottom: 10px;
            }

            .app-sidebar .app-sidebar__inner {
                height: 75% !important;
            } 
            /*.scrollbar-sidebar, .scrollbar-container {*/
            /*    height: 100dvh;*/
            /*}*/
        
            .playstore li {
                text-align: center;
            }
            @media (max-width: 776px){
                .playstore{
                    bottom: -10px !important;
                }
            }
  
        </style>
       
            <ul class="playstore">
                <li>
                    <a href="#" onclick="shareLink('https://play.google.com/store/apps/details?id=com.shi.my_rider_driver', 'Driver'); return false;">
                        <p style="margin: 0;">Driver App</p>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/512px-Google_Play_Store_badge_EN.svg.png" alt="Download Driver App" width="100">
                    </a>
                </li>
                <li>
                    <a href="#" onclick="shareLink('https://play.google.com/store/apps/details?id=com.shi.myPassenger&pcampaignid=web_share', 'Passenger'); return false;">
                        <p style="margin: 0;">Passenger App</p>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/512px-Google_Play_Store_badge_EN.svg.png" 
                             alt="Download Passenger App" width="100">
                    </a>
                </li>
            </ul>
       
    </div>
</div>


<script>
    
    function adjustSidebarHeight() {
        const sidebar = document.querySelector(".scrollbar-sidebar");
        const viewportHeight = window.innerHeight;
        sidebar.style.height = `${viewportHeight}px`;
    }
    
    window.addEventListener("load", adjustSidebarHeight);
    window.addEventListener("resize", adjustSidebarHeight);
    
</script>

