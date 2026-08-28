<footer>

    <div class="container-xl">

        <div class="footer-inner">


            @if(isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)

                        @php

                            $bottomItems = collect($seoData['getAllPages'])->filter(function ($page) {
                                return $page->position == 'bottom';
                            });

                            $getAfterFive = collect($bottomItems);

                            // $getAfterFive = collect($seoData['getAllPages'])->skip(5);
                        @endphp

                        <ul class="navbar-nav">

                            @foreach ($getAfterFive as $page)

                                <li class="nav-item">

                                    <a class="nav-link" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>

                                </li>

                            @endforeach

                        </ul>

            @endif





            <div class="row d-flex align-items-center gy-4">

                <!-- copyright text -->

                <div class="col-md-4">

                    <span class="copyright">© {{ date('Y') }} Go Ride Run</span>

                </div>



                <!-- social icons -->

                <div class="col-md-4 text-center">

                    <ul class="social-icons list-unstyled list-inline mb-0">



                        @if (isset($seoData['partnerWeb']->fb) && $seoData['partnerWeb']->fb != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->fb}}"><i
                                        class="fab fa-facebook-f"></i></a></li>
                        @endif

                        @if (isset($seoData['partnerWeb']->x) && $seoData['partnerWeb']->x != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->x}}"><i
                                        class="fab fa-twitter"></i></a></li>
                        @endif

                        @if (isset($seoData['partnerWeb']->insta) && $seoData['partnerWeb']->insta != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->insta}}"><i
                                        class="fab fa-instagram"></i></a></li>
                        @endif

                        @if (isset($seoData['partnerWeb']->yt) && $seoData['partnerWeb']->yt != '')
                            <li class="list-inline-item"><a target="_blank" href="{{$seoData['partnerWeb']->yt}}"><i
                                        class="fab fa-youtube"></i></a></li>
                        @endif


                    </ul>

                </div>



                <!-- go to top button -->

                <div class="col-md-4">

                    <a href="#" id="return-to-top" class="float-md-end"><i class="fa-solid fa-arrow-up"></i>Back to
                        Top</a>

                </div>

            </div>

        </div>

    </div>

</footer>

<div class="cookie-consent-banner" id="cookiecontent" style="display: block;">
    <div class="cookie-consent-banner__inner">
        <div class="row">
            <div class="col-md-9">
                <div class="cookie-consent-banner__copy">
                    <div class="cookie-consent-banner__description">This website stores cookies on your browser. These
                        cookies are used to improve your experience and provide more personalized service on our website
                        and related media. To learn more about the cookies and data we use, please review our Privacy
                        Policy.
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="cookie-consent-banner__actions">
                    <a target="_blank" href="#" class="cookie-consent-banner__cta cookie-consent-banner__cta--secondary">
                        PRIVACY POLICY
                    </a>
                    <button class="cookie-consent-banner__cta" aria-label="Close" onclick="setCookie('acceptCookie', 'Accepted', 30 );$(`#cookiecontent`).hide();">ACCEPT</button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .cookie-consent-banner__cta {
    box-sizing: border-box;
    display: inline-block;
    min-width: 135px;
    padding: 10px 13px;
    margin-top: 15px;
    border-radius: 7px;
    background-color: #f7a20f;
    color: #f7f7f7!important;
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
    @media only screen and (max-width: 992px) {
        .navbar-nav {
            justify-content: center !important;
        }
    }
</style>