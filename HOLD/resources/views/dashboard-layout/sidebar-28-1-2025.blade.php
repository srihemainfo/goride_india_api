@php
use Illuminate\Support\Facades\DB;
$get_parrent_menu = DB::connection('mysql1')->table('side_bar_parrent')->get();

//dd($get_parrent_menu);

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
    <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu metismenu">
                @php
                   
                $sessionPermission = session('new_permission');
                //dd( $sessionPermission);
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
                            <li>
                             <a href="{{ url($childItem->route) }}" class="{{ request()->is(strtolower($childItem->route) . '*') ? 'mm-active' : '' }}">
                                {!! $childItem->icon !!} 
                                {{ $childItem->name }}
                            </a>

                            </li>
                     @endif
                     @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach  
        
        
            </ul>
        </div>
    </div>
</div>
