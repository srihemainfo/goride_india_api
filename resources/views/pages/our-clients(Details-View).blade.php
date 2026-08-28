@extends('layouts.app')

@section('content').

<style>
    
.owner-thumb {
    width: 200px;
}

.driver-details img {
    border: 1px solid #ddd;
    border-radius: 10px;
}
    
</style>

    <!-- Breadcrumb -->

    <section class="page-header">
        <div class="page-header-shape"></div>
        <div class="container">
            <div class="page-header-info">
                <h4>About Us!</h4>
                <h2>Feel your journey <br> with <span>GoRide!</span></h2>
                <p>Everything your taxi business <br>needs is already here! </p>
            </div>
        </div>
    </section>
    
    <section class="driver-details-section bd-bottom padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-heading mb-3">
                        <h4><span></span>Our Client!</h4>
                        <h2>Lonadon Airport Riders</h2>
                        <p>London Airport Riders offers the most reliable airport transfer services to passengers arriving or departing from London Airports. We specialize in pre-booked airports pick-ups from all major UK airports including Heathrow Airport, Gatwick Airport, Stansted Airport, Luton Airport, and London City Airport.</p>
                    </div>
                    <ul class="about-info m-0">
                        <li>
                            <img class="owner-thumb" src="{{ asset('goride/img/client/london_airport.png') }}" alt="thumb">
                        </li>
                        <!--<li>-->
                        <!--    <h2><span>Call For Taxi</span><a href="tel:5267214392">5267-214-392</a></h2>-->
                        <!--</li>-->
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="driver-details">
                        <img src="{{ asset('goride/img/client/client_londonairportriders_hp.webp') }}" alt="client_hp">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="driver-details-section bd-bottom padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="driver-details">
                        <img src="{{ asset('goride/img/client/client_prestige_airport_hp.webp') }}" alt="client_hp">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-heading mb-3">
                        <h4><span></span>Our Client!</h4>
                        <h2>Prestige Airport Cars</h2>
                        <p>If you are going on a trip, the details might be filling you with dread. Have you packed the right things? Do you have enough foreign currency? Have your hotel room reservations been confirmed? And that question that can put a wrench into the plans in any major city with traffic congestion: how are you getting to the airport?</p>
                    </div>
                    <ul class="about-info m-0">
                        <li>
                            <img class="owner-thumb" src="{{ asset('goride/img/client/prestige_airport.png') }}" alt="thumb">
                        </li>
                    </ul>
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
