<footer>
    <div class="container-xl">
        <div class="footer-inner">
            
        @if(isset($seoData['getAllPages']) &&  collect($seoData['getAllPages'])->count() > 5)
    @php
        // Skip the first 5 items and get the rest
        $getAfterFive =  collect($seoData['getAllPages'])->skip(5);
    @endphp
    <ul class="navbar-nav">
        @foreach ($getAfterFive as $page)
            <li class="nav-item">
                <a class="nav-link" href="{{ url( '/' . $page->url) }}">{{ $page->title }}</a>
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
                        <li class="list-inline-item"><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="list-inline-item"><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li class="list-inline-item"><a href="#"><i class="fab fa-instagram"></i></a></li>
                        <li class="list-inline-item"><a href="#"><i class="fab fa-linkedin"></i></a></li>
                    </ul>
                </div>

                <!-- go to top button -->
                <div class="col-md-4">
                    <a href="#" id="return-to-top" class="float-md-end"><i class="icon-arrow-up"></i>Back to Top</a>
                </div>
            </div>
        </div>
    </div>
</footer>
