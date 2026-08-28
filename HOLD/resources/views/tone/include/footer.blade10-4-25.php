<style>
    #myPassengerApp {
        text-decoration: none;
        color:rgb(255, 255, 255);
    }

    #myPassengerApp:hover {
        color: #ffca09;
    }
    #return-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: black;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 999;
    border: none;
}

#return-to-top i {
    font-size: 20px;
}
#return-to-top:hover{
    color: white;
    background-color: #FAC608;
}

/* Smooth scroll behavior for the whole page */
html {
    scroll-behavior: smooth;
}
.navbar-nav .nav-item a{
    font-size: 13px !important;
    margin-top: 0px !important;
    }
</style>

<footer>
    <div class="container">
        <div class="footer-inner">
            @if(isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)
                @php
                    $subdomain = explode('.', request()->getHost())[0];
                    $bottomItems = collect($seoData['getAllPages'])->filter(fn($page) => $page->position == 'bottom');
                @endphp

                <ul class="navbar-nav d-flex flex-wrap justify-content-center">
                    @foreach ($bottomItems as $page)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="row align-items-center">
                <!-- Copyright - Left aligned -->
                <div class="col-md-4 text-start">
                    <a href="https://play.google.com/store/apps/details?id=com.shi.myPassenger&pcampaignid=web_share&subdomain={{ $subdomain }}" 
                       id="myPassengerApp" target="_blank" 
                       class="d-inline-flex align-items-center">
                        <span style="font-weight: bold; font-size: 12px;">Get My Passenger App Now!</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/512px-Google_Play_Store_badge_EN.svg.png" 
                             alt="Download Driver App" width="100" class="ms-2">
                    </a>
                </div>

                <!-- Social Icons - Center aligned -->
                <div class="col-md-4 text-center">
                    <ul class="social-icons list-unstyled list-inline mb-0">
                        @if(isset($seoData['partnerWeb']->fb) && $seoData['partnerWeb']->fb != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->fb}}"><i class="fab fa-facebook-f"></i></a></li>
                        @endif
                        @if(isset($seoData['partnerWeb']->x) && $seoData['partnerWeb']->x != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->x}}"><i class="fab fa-twitter"></i></a></li>
                        @endif
                        @if(isset($seoData['partnerWeb']->insta) && $seoData['partnerWeb']->insta != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->insta}}"><i class="fab fa-instagram"></i></a></li>
                        @endif
                        @if(isset($seoData['partnerWeb']->yt) && $seoData['partnerWeb']->yt != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->yt}}"><i class="fab fa-youtube"></i></a></li>
                        @endif
                    </ul>
                </div>

                <!-- App Download - Right aligned -->
                <div class="col-md-4 text-end">
                    <span class="copyright" style="font-size: 12px;">© 2025 Go Ride Run</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<a href="#" id="return-to-top"><i class="fa-solid fa-arrow-up ms-2"></i></a>

<!--<div class="cookie-consent-banner" id="cookiecontent" style="display: none;">-->
<!--    <div class="cookie-consent-banner__inner">-->
<!--        <div class="row">-->
<!--            <div class="col-md-9">-->
<!--                <div class="cookie-consent-banner__copy">-->
<!--                    <div class="cookie-consent-banner__description">-->
<!--                        This website stores cookies on your browser. These cookies are used to improve your experience-->
<!--                        and provide more personalized service on our website and related media. To learn more about-->
<!--                        the cookies and data we use, please review our Privacy Policy.-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-md-3">-->
<!--                <div class="cookie-consent-banner__actions">-->
<!--                    <a target="_blank" href="#" class="cookie-consent-banner__cta cookie-consent-banner__cta--secondary">-->
<!--                        PRIVACY POLICY-->
<!--                    </a>-->
                    <!-- <button class="cookie-consent-banner__cta" aria-label="Close" 
<!--                        onclick="$('#cookiecontent').hide();">-->
<!--                        ACCEPT-->
<!--                    </button> -->-->
<!--                    <button class="cookie-consent-banner__cta" aria-label="Close"-->
<!--                        onclick="acceptCookies()">-->
<!--                        ACCEPT-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<script>
    // function acceptcookies() {
    //     // // document.getElementById("name").value = "jana";
    //     // // const name = document.getElementById("name").value; 

    //     // // document.getElementById("idnamew").value = "accept";
    //     // // const values = document.getElementById("idnamew").value;

    //     // let nameInput = document.getElementById("name");
    //     // let valueInput = document.getElementById("idnamew");

    //     // nameInput.value = "jana";
    //     // const name = nameInput.value; 

    //     // valueInput.value = "accept";
    //     // const values = valueInput.value;

    //     let date = new Date();
    //     date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    //     let expires = "expires=" + date.toUTCString();


    //     document.cookie = name + "=" + values + "; " + expires + "; path=/";

    //     document.getElementById("cookiecontent").style.display = "none";
    // }
    window.onload = checkPopup;


    function setCookie(name, value, days) {
        let date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        let expires = "expires=" + date.toUTCString();
        document.cookie = name + "=" + value + "; " + expires + "; path=/";
    }

    function getCookie(name) {
        let nameEQ = name + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function checkPopup() {
        let lastPopupTime = getCookie("Cookie_con");

        if (!lastPopupTime) {
            document.getElementById("cookiecontent").style.display = "block";

        } else {
            let currentTime = new Date().getTime();
            let lastTime = parseInt(lastPopupTime);
            let oneDay = 24 * 60 * 60 * 1000;

            if (currentTime - lastTime > oneDay) {
                document.getElementById("cookiecontent").style.display = "block";
            } else {
                document.getElementById("cookiecontent").style.display = "none";

            }
        }
    }

    function acceptCookies() {
        let currentTime = new Date().getTime();
        setCookie("Cookie_con", currentTime, 1);
        document.getElementById("cookiecontent").style.display = "none";
    }
</script>

<style>
    .cookie-consent-banner__cta {
        box-sizing: border-box;
        display: inline-block;
        min-width: 135px;
        padding: 10px 13px;
        margin-top: 15px;
        border-radius: 7px;
        background-color: #f7a20f;
        color: #f7f7f7 !important;
        text-decoration: none;
        text-align: center;
        font-size: 16px;
        line-height: 20px;
        border: 0;
    }

    .cookie-consent-banner__description {
        color: #fff;
        font-size: 16px;
        line-height: 24px;
    }

    .cookie-consent-banner__inner {
        margin: 0 auto;
        padding: 20px 20px;
        background: #2b2b2b;
    }

    .cookie-consent-banner {
        position: fixed;
        bottom: 0px;
        left: 0;
        z-index: 2147483645;
        box-sizing: border-box;
        width: 100%;
        background-color: #f3ba00;
    }

    @media only screen and (max-width: 990px) {
        .navbar-nav {
            justify-content: center !important;
        }
        
    }
     @media (max-width:767px) {
        .col-md-4.text-start{
            text-align: center !important;
        }
     }

    /* @media (max-width: 767px) {
        #myPassengerApp {
            display: none;
        }
    } */
</style>