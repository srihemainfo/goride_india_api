@extends($theme . '.layouts.app')

@section('css')
<style>
    #about{
        color: black;
        font-size: 14px!important;
    }
    @media (min-width: 1200px) {
    .h4, h4 {
        font-size: 1rem!important;
    }
}
</style>
@endsection


@section('content')

<section class="main-content">

    <div class="container-fluid mt-5">

        <div class="row gy-4">
            <div class="col-lg-4">
                @php
                    $order = request()->order ?? '';
                    $rorder = request()->rorder ?? '';
                    $subis = request()->subis ?? '';
                @endphp

                @if($order != '')
                
                    <iframe src="{{ env('iframeURL') . request()->getHost().'&order='.$order.'&rorder='.$rorder.'&subis='.$subis }}" width="100%" height="500" frameborder="0" style="overflow: hidden;" frameborder="0"></iframe>
                @else
                    <iframe src="{{ env('iframeURL') . request()->getHost() }}" width="100%" height="500" frameborder="0" style="overflow: hidden;" frameborder="0"></iframe>
                
                @endif


            </div>

            <div class="col-lg-8 right-content my-5">

                @if( isset($seoData['getAllPages']) &&
                collect($seoData['getAllPages'])->count() > 0 && 
                isset($seoData['activePagesContent']->description)   &&
                (isset($slug) || isset($seoData['activePagesContent'])))
                
             
                    <!-- widget1 -->
                    <div class="widget rounded page-content bordered rounded  {{ isset($seoData['activePagesContent']) ? '' : 'd-none' }}"
                        id="about">
                        {!! $seoData['activePagesContent']->description ?? '' !!}

                    </div>
                    <!-- widget2 post carousel -->
                    <!--<div class="widget rounded page-content d-none" id="fleet">-->
                    <!--    <div class="widget-header">-->
                    <!--        <h3 class="widget-title">OEC Fleet</h3>-->
                    <!--    </div>-->
                    <!--    <div class="widget-content">-->
                    <!--        <div class="main-fleet" data-content="1545" id="right_2">-->
                    <!--            <p>We provide access to a large fleet of vehicle sizes and types. Regardless of your party-->
                    <!--                size, luggage or special-->
                    <!--                requirements, we can usually provide the perfect vehicle.</p>-->
                    <!--            <p>If you have any queries about vehicle suitability, please check our vehicle calculator.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/saloon.png" alt="Airport taxi transfers"-->
                    <!--                    class="vehicle vehicle-1" title="saloon car">-->
                    <!--                <strong>Saloon Car</strong>: Ford Mondeo, VW Passat or similar. These can accommodate up-->
                    <!--                to 3 passengers plus 3 standard suitcases (23kg max), or 4 passengers plus hand luggage.-->
                    <!--                Ford Mondeo or VW Passat or similar.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/estate.png" alt="Birmingham airport taxi"-->
                    <!--                    class="vehicle vehicle-3" title="estate car">-->
                    <!--                <strong>Estate Car</strong>: Volvo Estate, VW Passat or similar. These can accommodate-->
                    <!--                up to 4 passengers plus 4 standard suitcases (23kg max). Volvo Estate, VW Passat or-->
                    <!--                similar.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/executive.png" alt="Gatwick airport taxi"-->
                    <!--                    class="vehicle vehicle-2" title="executive car">-->
                    <!--                <strong>Executive Car</strong>: E Class Mercedes or similar. These can accommodate up to-->
                    <!--                3 passengers plus 3 standard suitcases (23kg max), or 4 passengers plus hand luggage.-->
                    <!--                E-Class Mercedes or similar.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/mpv-5.png" alt="London airport transfer"-->
                    <!--                    class="vehicle vehicle-4" title="people car">-->
                    <!--                <strong>People Carrier</strong>: VW Sharan, Ford Galaxy or similar. These can-->
                    <!--                accommodate up to 5 passengers plus 5 standard suitcases (23kg max), or 6 passengers-->
                    <!--                plus hand luggage. VW Sharan, Ford Galaxy or similar.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/mpv-7.png" alt="Luton airport transfers"-->
                    <!--                    class="vehicle vehicle-5" title="mpv car">-->
                    <!--                <strong>Executive People Carrier</strong>: Mercedes Viano or similar. These can-->
                    <!--                accommodate up to 5 passengers plus 5 standard suitcases (23kg max), or 6 passengers-->
                    <!--                plus hand luggage. Mercedes Viano or similar.-->
                    <!--            </p>-->

                    <!--            <p>-->
                    <!--                <img src="images/fleet/mpv-8.png" alt="Birmingham airport taxi"-->
                    <!--                    class="vehicle vehicle-7" title="8 seater car">-->
                    <!--                <strong>8 Seater Minibus</strong>: VW Transporter or similar. These can accommodate 8-->
                    <!--                passengers plus up to 8 standard suitcases (23kg max). VW Transporter or similar.-->
                    <!--            </p>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <!-- widget2 -->
                    <!--<div class="widget rounded page-content clearfix d-none" id="service">-->
                    <!--    <img src="images/single.jpg" class="rounded alignleft" alt="Stansted airport taxi"-->
                    <!--        title="Stansted airport taxi">-->
                    <!--    <h1 style="font-size:24px;">AIRPORT TAXI SERVICE</h1>-->
                    <!--    <p>A 'Meet and Greet' service can be arranged when booking whereby the driver will be in arrivals-->
                    <!--        with the passenger's name or company name, whichever you prefer. A comforting thought,-->
                    <!--        especially for first time visitors in a foreign country.</p>-->

                    <!--    <p>If your flight is early or subject to delays, we will track your flight's progress and send your-->
                    <!--        driver at the new expected time of arrival.</p>-->

                    <!--    <p>A lot of our work is corporate based so if you are booking a taxi for other people you need not-->
                    <!--        worry, we will give them the professional quality service they would expect. We accept most-->
                    <!--        major credit cards and debit cards and send receipts via email.</p>-->
                    <!--    <p>We provide access to a large fleet of vehicle sizes and types. Regardless of your party size,-->
                    <!--        luggage or special requirements, we can usually provide the perfect vehicle.</p>-->

                    <!--    <ul>-->
                    <!--        <li><strong>Online Booking:</strong> to our user-friendly website at Oxford cars Our intuitive-->
                    <!--            design ensures a hassle-free booking experience.</li>-->
                    <!--        <li><strong>City Transport:</strong> Our city transport services are designed to be efficient-->
                    <!--            and punctual. Whether you're heading to a business meeting, a hotel, or a popular city-->
                    <!--            attraction.</li>-->
                    <!--        <li><strong>Airport Transport:</strong> Our experienced drivers are not just skilled behind the-->
                    <!--            wheel; they are also committed to providing a courteous and professional service the moment-->
                    <!--            you arrive.</li>-->
                    <!--        <li><strong>Business Transport:</strong> We understand that business plans can change. Our-->
                    <!--            services are flexible and customizable, allowing you to modify your reservation.</li>-->
                    <!--        <li><strong>Regular Transport:</strong> Set up your regular commuting routes by entering your-->
                    <!--            daily pick-up and drop-off locations, preferred timings, and any specific preferences you-->
                    <!--            may have.</li>-->
                    <!--        <li><strong>Tour Transport:</strong> Provide details about your tour, including the destination,-->
                    <!--            date, and any specific stops or attractions you plan to visit for perfect vehicle selection.-->
                    <!--        </li>-->
                    <!--    </ul>-->

                    <!--    <p>OEC also welcomes corporate account customers and, subject to credit references, we’ll be happy-->
                    <!--        to offer you a monthly invoicing facility or direct debit on our airport taxi services.</p>-->
                    <!--</div>-->

                    <!--<div class="widget rounded page-content clearfix d-none" id="service">-->
                    <!--    <h4>POPULAR LOCATIONS</h4>-->
                    <!--    <p>We cover the whole of the UK, including many popular locations such as-->
                    <!--        <a href="stansted-car-service">Stansted Car Service</a>,-->
                    <!--        <a href="luton-airport-transportation">London airport taxi</a>,-->
                    <!--        <a href="heathrow-airport-transfer">Heathrow Airport Transfer</a>,-->
                    <!--        <a href="birmingham-airport">Birmingham airport taxi</a>,-->
                    <!--        <a href="manchester-airport-taxi-transfer-service">Manchester Airport Taxi Transfer Service</a>,-->
                    <!--        <a href="liverpool-airport-taxi-transfer-service">Liverpool Airport Taxi Transfer Service</a>-->
                    <!--    </p>-->
                    <!--</div>-->
                @endif




            </div>


        </div>

    </div>
</section>
@endsection
@section('script')
<script>
if (window.location.search.includes('order')) {
    // Update the URL in the address bar without reloading the page
    window.history.replaceState({}, document.title, window.location.pathname);
}

</script>
@endsection