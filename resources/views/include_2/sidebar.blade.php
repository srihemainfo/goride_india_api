<link href="https://assets.calendly.com/assets/external/widget.css" rel="stylesheet">
<style>
        #mobile_menu_close {
        display: none;
    }

    @media screen and (max-width: 576px) {
        .page-wrapper {
            padding: 10px;
        }

        #mobile_menu_close {
            display: block;
            font-size: 24px;
        }
    }
          #sidebarnav1 {
            display: flex;
            flex-direction: column;
            /*gap: 16px;*/
        }
        
        .sidebar-item1 {
            display: flex;
            flex-direction: column;
            align-items: start;
            text-align: start;
        }
        
        .sidebar-item1 i {
            font-size: 20px; /* Smaller YouTube icon */
            color: #FF0000;
            margin-bottom: 6px;
        }
        
        .step-label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }
        
        .yt-link {
            font-size: 12px;
            color: #007bff;
            white-space: nowrap; /* Prevent wrapping */
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            text-decoration: none;
        }
        .yt-link:hover {
            text-decoration: underline;
        }
        
        .youtube-wrapper {
    width: 100%;
    max-width: 360px;
    height: 100px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border-radius: 4px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.custom-play-button {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 26px;         /* reduced from 30px */
    height: 20px;        /* reduced from 24px */
    background-color: #FF0000;
    transform: translate(-50%, -50%);
    border-radius: 4px;  /* slightly smaller roundness */
}

/* Smaller white triangle inside */
.custom-play-button::before {
    content: "";
    position: absolute;
    left: 8px;           /* adjusted for new width */
    top: 4px;            /* adjusted for new height */
    width: 0;
    height: 0;
    border-left: 12px solid white;          /* smaller triangle */
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
}



</style>

<aside class="left-sidebar with-vertical" id="left_sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="/" class="text-nowrap logo-img" contenteditable="false" style="cursor: pointer;">
                <img src="{{ asset('goride/img/logo-dark.png') }}" class="dark-logo" alt="Logo-Dark">
            </a>
            <div class="">
            <i class="fa-solid fa-close" id="mobile_menu_close" style="width: 30px; height: 30px; font-size: 30px;"></i>
            </div>
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
                                        <a class="sidebar-link {{ request()->routeIs('jobs') ? 'active' : '' }}" href="jobs">
                                            <span>
                                                <i class="fa-light fa-briefcase"></i>
                                            </span>
                                            <span class="hide-menu">Jobs</span>
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
                                
                                <br>
                                
                                <div class="card p-2 mb-2">
    <h5 class="mb-3">Follow these steps to set up the CRM</h5>

    <ul id="sidebarnav1" class="list-unstyled mb-0">
        <!-- Step 1 -->
        <li class="sidebar-item1 mb-4">
            <span class="step-label d-block mb-2 fw-semibold">Step 1</span>
            <div class="youtube-wrapper" onclick="loadVideo(this, 'b1_72zXxIRE')">
                <img src="https://img.youtube.com/vi/b1_72zXxIRE/hqdefault.jpg" class="thumbnail" />
                <div class="custom-play-button">
                    <!--<i class="fas fa-play"></i>-->
                </div>
            </div>
        </li>

        <!-- Step 2 -->
        <li class="sidebar-item1 mb-4">
            <span class="step-label d-block mb-2 fw-semibold">Step 2</span>
            <div class="youtube-wrapper" onclick="loadVideo(this, 'LbsafbJds2A')">
                <img src="https://img.youtube.com/vi/LbsafbJds2A/hqdefault.jpg" class="thumbnail" />
                <div class="custom-play-button">
                    <!--<i class="fas fa-play"></i>-->
                </div>
            </div>
        </li>

        <!-- Step 3 -->
        <li class="sidebar-item1">
            <span class="step-label d-block mb-2 fw-semibold">Step 3</span>
            <div class="youtube-wrapper" onclick="loadVideo(this, '6POTdL1TJKQ')">
                <img src="https://img.youtube.com/vi/6POTdL1TJKQ/hqdefault.jpg" class="thumbnail" />
                <div class="custom-play-button">
                    <!--<i class="fas fa-play"></i>-->
                </div>
            </div>
        </li>
    </ul>
</div>


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
<script>
    function loadVideo(element, videoId) {
    element.innerHTML = `
        <iframe width="100%" height="100%" 
            src="https://www.youtube.com/embed/${videoId}?autoplay=1"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen>
        </iframe>`;
}
$(document).ready(function () {
    $('#mobile_menu_close').on('click touchstart', function () {
        $('#left_sidebar').toggle();
        $(this).toggleClass('fa-close fa-close');
    });
    

    // Close left sidebar on touch on the right side of the screen
    $(document).on('touchstart', function (e) {
        if (!$(e.target).closest('#left_sidebar').length && !$(e.target).closest('#mobile_menu_close').length) {
            $('#left_sidebar').hide();
            $('#mobile_menu_close').removeClass('fa-close').addClass('fa-close');
        }
    });
});
</script>

