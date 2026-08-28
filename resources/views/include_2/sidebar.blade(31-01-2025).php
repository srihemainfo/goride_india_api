<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">

<aside class="left-sidebar with-vertical" id="left_sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="/" class="text-nowrap logo-img" contenteditable="false" style="cursor: pointer;">
                <img src="{{ asset('goride/img/logo-dark.png') }}" class="dark-logo" alt="Logo-Dark">
            </a>
        </div>
        <nav class="sidebar-nav scroll-sidebar simplebar-scrollable-y" data-simplebar="init">
            <div class="simplebar-wrapper" style="margin: 0px -24px;">

                <div class="simplebar-mask">
                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                        <div class="simplebar-content-wrapper" tabindex="0" role="region"
                            aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                            <div class="simplebar-content" style="padding: 24px;">
                                <ul id="sidebarnav">
                                    <!--<li class="nav-small-cap">-->
                                    <!--    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>-->
                                    <!--    <span class="hide-menu">Home</span>-->
                                    <!--</li>-->
                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="dashboard">
                                            <span>
                                                <i class="fa-light fa-house"></i>
                                            </span>
                                            <span class="hide-menu">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('package-history') ? 'active' : '' }}" href="package-history">
                                            <span>
                                                <i class="fa-light fa-clock-rotate-left"></i>
                                            </span>
                                            <span class="hide-menu">Package History</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link {{ request()->routeIs('support') ? 'active' : '' }}" href="contact">
                                            <span>
                                                <i class="fa-light fa-headset"></i>
                                            </span>
                                            <span class="hide-menu">Support</span>
                                        </a>
                                    </li>
                                    <!-- <li class="sidebar-item">-->
                                    <!--    <a class="sidebar-link {{ request()->routeIs('support') ? 'active' : '' }}" href="{{ env('CALENDLY_URL') }}" >-->
                                    <!--        <span>-->
                                    <!--            <i class="fa-light fa-headset"></i>-->
                                    <!--        </span>-->
                                    <!--        <span class="hide-menu">Request to Demo</span>-->
                                    <!--    </a>-->
                                    <!--</li>-->
                                    <!--<li class="sidebar-item">-->
                                    <!--    <a class="sidebar-link" href="#">-->
                                    <!--        <span>-->
                                    <!--            <i class="fa-light fa-trash"></i>-->
                                    <!--        </span>-->
                                    <!--        <span class="hide-menu">Delete Account</span>-->
                                    <!--    </a>-->
                                    <!--</li>-->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar"
                    style="width: 0px; display: none; transform: translate3d(0px, 0px, 0px);"></div>
            </div>
            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                <div class="simplebar-scrollbar"
                    style="height: 25px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
            </div>
        </nav>
        
    </div>
</aside>


