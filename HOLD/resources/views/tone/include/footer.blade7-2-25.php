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
<style>
    @media only screen and (max-width: 992px) {
        .navbar-nav {
            justify-content: center !important;
        }
    }
</style>