@extends('layouts.app')

@section('content')

<style>

.team-thumb {
    /*clip-path: polygon(0 0, 90% 0%, 100% 10%, 100% 100%, 0 100%);*/
    border: 2px solid #ddd;
    overflow: hidden;
}

.team-content h3 a {
    background-color: #ff9900;
    clip-path: polygon(0 0, 100% 0, 100% 70%, 90% 100%, 0 100%);
    background-image: repeating-linear-gradient(45deg, #f7a20f 0, #f7a20f 2px, transparent 0, transparent 50%);
    background-size: 8px 8px;
    width: 70%;
    height: 50px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 500;
    line-height: 1;
    margin: 0 auto;
    margin-top: -30px;
}
    
</style>

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>Our Clients!</h4>
                <h1>Our Esteemed <span>Clients</span></h1>
                <p>Trusted by businesses worldwide, <br>our clients are the foundation of our success!</p>
            </div>
        </div>
    </section>
    
    <section class="team-section section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="section-title text-center">Our &nbsp;<span> Clients</span></div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_airportcarsuk_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Airport Cars UK</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_airportlimocanada_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Airport Limo Canada</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_airportrides_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Airport Rides</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_britishexpresscars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">British Express Cars</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_cheamairportcars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Cheam Airport Cars</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_cutlasscars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Cutlass Cars</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_epsomcars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Epsom Cars</a></h3>
                        </div>
                    </div>
                </div> 
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_essexairportcars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Essex Airport Cars</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_londonairportriders_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">London Airport Riders</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_pristageairportcars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Prestige Airport Cabs</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_prestige_airport_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Prestige Airport Cars</a></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="team-item">
                        <div class="team-thumb">
                            <img src="{{ asset('goride/img/client/client_surbitonairportcars_hp.webp') }}" alt="thumb">
                        </div>
                        <div class="team-content">
                            <h3><a href="javascript:void(0);">Surbiton Airport Cars</a></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection

@section('script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js" integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        var wow = new WOW( {
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       0,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
            callback:     function(box) {
              // the callback is fired every time an animation is started
              // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null // optional scroll container selector, otherwise use window
          }
        );
        wow.init();
    </script>
    
@endsection
