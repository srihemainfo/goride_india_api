@extends('layouts.app')


@section('css')
<style>
    .pricing-section {
        padding: 50px 0;
    }

    a.thm-btn.borderd {
        cursor: pointer;
    }

    .sec-title h2 {
        font-size: 32px;
        color: #170B35;
        font-weight: 600;
    }

    .sec-title p {
        font-size: 20px;
        line-height: 26px;
        color: #656565;
        margin-top: 20px;
    }

    .sec-title {
        margin-bottom: 100px;
    }

    .pricing-section ul.switch-toggler-list {
        margin-bottom: 40px;
    }

    .list-inline li {
        display: inline-block;
    }

    .pricing-section ul.switch-toggler-list li a {
        color: #989898;
    }

    .pricing-section ul.switch-toggler-list li.active a {
        color: #000;
    }

    .pricing-section ul.switch-toggler-list li a {
        font-size: 18px;
        font-weight: 600;
        color: #989898;
        padding-left: 10px;
        padding-right: 10px;
        display: block;
    }

    .pricing-section .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        vertical-align: middle;
    }

    .pricing-section .switch.on .slider {
        background: #d43396;
        background: -webkit-gradient(left top, right top, color-stop(0%, #d43396), color-stop(100%, #6541c1));
        background: -webkit-gradient(linear, left top, right top, from(#d43396), to(#6541c1));
        background: linear-gradient(to right, #d43396 0%, #6541c1 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#d43396', endColorstr='#6541c1', GradientType=1);
    }

    .pricing-section .slider.round {
        border-radius: 34px;
    }

    .pricing-section .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .slider.round:before {
        border-radius: 50%;
    }

    .pricing-section .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .tabed-content #year {
        display: none;
    }

    .pricing-section .pricing-row {
        padding-top: 20px;
    }

    .pricing-section .single-pricing {
        position: relative;
        background: #E8E6E6;
        border-radius: 15px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing .inner {
        position: relative;
        padding-bottom: 45px;
        padding-top: 45px;
    }

    .pricing-section .single-pricing h3.title {
        font-size: 24px;
        color: #170B35;
        font-weight: 600;
    }

    .pricing-section .single-pricing h3,
    .pricing-section .single-pricing p,
    .pricing-section .single-pricing ul,
    .pricing-section .single-pricing li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pricing-section .single-pricing p.price {
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 53px;
        font-weight: 200;
        line-height: 1em;
        margin-bottom: 15px;
        margin-top: 15px;
    }

    .pricing-section .single-pricing p.price-label {
        font-size: 18px;
        font-weight: 600;
        color: #656565;
    }

    .pricing-section .single-pricing ul.list-item {
        margin-top: 45px;
    }

    .pricing-section .single-pricing ul.list-item li {
        font-size: 20px;
        color: #170B35;
        font-weight: 400;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-check {
        color: #12CE32;
    }

    .pricing-section .single-pricing ul.list-item li i {
        vertical-align: middle;
        margin-right: 5px;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-times {
        color: #FF0302;
    }

    .pricing-section .single-pricing a.thm-btn {
        padding: 15px 57px;
        margin-top: 35px;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .thm-btn>span {
        position: relative;
    }

    .pricing-section .single-pricing.popular {
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        margin-top: -10px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing.popular .inner {
        padding-top: 45px;
        padding-bottom: 30px;
    }

    .pricing-section .single-pricing.popular .thm-btn {
        color: #fff;
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .pricing-section .single-pricing a.thm-btn {
        padding: 15px 57px;
        margin-top: 35px;
    }

    .thm-btn {
        display: inline-block;
        border: none;
        outline: none;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 600;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
        border-radius: 28px;
        padding: 15px 29px;
        position: relative;
    }

    .pricing-section .single-pricing.popular .thm-btn:before {
        opacity: 0;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .pricing-section .switch.off .slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .thm-btn.borderd {
        color: #190A32;
    }

    .pricing-section .single-pricing a.thm-btn:hover {
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .thm-btn.borderd:hover:before {
        opacity: 0;
    }

    .thm-btn.borderd:hover {
        color: #fff;
    }

    @media (max-width: 736px) {
        .pricing-section .single-pricing.popular {
            top: 0;
            margin-top: 50px;
        }

        .pricing-section .single-pricing {
            max-width: 370px;
            margin-top: 50px;
            margin-left: auto;
            margin-right: auto;
        }

        .pricing-section ul.switch-toggler-list {
            margin-bottom: 0;
        }
    }

    .single-pricing .tag {
        position: absolute;
        text-transform: uppercase;
        font: 600 14px "Poppins", sans-serif;
        color: #fff;
        padding: 5px 20px;
        top: 5px;
        left: 6px;
        border-radius: 15px;
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
    }
</style>
@endsection

@section('content')
@php
//dd($_COOKIE['sessionToken']);
    $userToken = $_COOKIE['sessionToken'] ?? '';
    // $userDetails = null;
    $planList = null;
    // if (isset($userToken) && $userToken != null) {
    // $apiEndpoint = ;
    if ($userToken != '') {
        $response = Http::withToken($userToken)->post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode'] ?? ''
        ]);
    } else {
        $response = Http::post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode'] ?? ''
        ]);
    }
    if ($response->successful()) {
        // Decode JSON response
        $authUser = $response->json();
        // dd($authUser['data'], 's');
        if (isset($authUser['status']) && $authUser['status'] === 'success') {
            $planList = $authUser['data']['planList'] ?? null;
        }
    }
    // }
    //   dd($planList);
@endphp
<!-- Breadcrumb -->
<section class="page-header">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info">
            <h4>Flexible Pricing Plans</h4>
            <h1>Perfect Plan for Your <span>Business Needs!</span></h1>
            <p>Explore our affordable and customizable pricing options designed to fit businesses of all sizes.</p>
        </div>
    </div>
</section>


<section class="pricing-section" id="pricing">
    <div class="container">
        <div class="sec-title text-center">
            <h2>Get in Reasonable Price</h2>
            <p>Enjoy the convenience of reliable rides at prices that fit your budget with GoRide.</p>
        </div>
        <ul class="list-inline text-center switch-toggler-list" role="tablist" id="switch-toggle-tab">
            <li class="month active"><a href="#">Monthly</a></li>
            <li>
                <label class="switch on">
                    <span class="slider round"></span>
                </label>
            </li>
            <li class="year">
                <a href="#">Yearly
                    <span class="save-notice">save 20% <i>yearly</i></span>
                </a>
            </li>
        </ul>
        <div class="tabed-content mb-2">
            <div id="month">
                <div class="row pricing-row justify-content-center">
                    @foreach ($planList['monthly'] as $plan)
                        <div class="col-md-4 col-sm-6 col-lg-3 col-9">
                            <div class="single-pricing text-center {{ $plan['name'] == 'Enterprise' ? 'popular' : '' }}">
                                <div class="tag {{ $plan['name'] == 'Enterprise' ? '' : 'd-none' }}"><span><i
                                            class="fas fa-star"></i> Popular</span></div>
                                <form action="cart" method="post">
                                    @csrf
                                    <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                    <input type="hidden" name="planType" value="MONTHLY">
                                    <input type="hidden" name="purchaseType" value="NEW">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="subscriptions" value="false">
                                    <div class="inner">
                                        @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                            <div class="plan-tag">
                                                <p class="anu">
                                                    {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                        Day</span>
                                                </p>
                                            </div>
                                        @endif
                                        <h3 class="title">{{ $plan['name'] }}</h3>
                                        <p class="price">
                                            <!-- {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }} -->
                                            {{($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price'])}}
                                        </p>
                                        <p class="price-label">Full access</p>
                                        <ul class="list-item">
                                            <li><i class="fa fa-check"></i>

                                            {{ 'Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '') }}


{{-- $plan['name'] === 'Enterprise' ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) --}}

                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                            </li>
                                            {{-- @if ($plan['productType'] === 'TRAIL' && intval($plan['price']) < 1) --}}
                                                <!-- <li><i class="fa fa-check"></i>
                                                {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                </li> -->
                                                {{-- @endif --}}
                                                <li>
                                                    <i
                                                        class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                    {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                </li>
                                                {{-- <li><i
                                                        class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                    Setup Fee</li> --}}
                                        </ul>



                                        @if ($plan['productType'] != 'TRAIL')
                                            <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();"
                                                class="thm-btn borderd"
                                                href="javascript:void(0);"><span>{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Subscribe')  }}</span></a>

                                        @else

                                            <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();"
                                                class="thm-btn borderd"
                                                href="javascript:void(0);"><span>{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Buy Now')  }}</span></a>


                                            <!-- @if ($plan['productType'] != 'TRAIL')
                                                                        <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();"
                                                                            class="thm-btn borderd"
                                                                            href="javascript:void(0);"><span>{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Subscribe')  }}</span></a>
                                                                    @endif -->


                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div id="year">
                <div class="row pricing-row justify-content-center">
                    @foreach ($planList['yearly'] as $plan)
                        <div class="col-md-4 col-sm-6 overflow col-lg-3 col-9">
                            <div class="single-pricing text-center {{ $plan['name'] == 'Enterprise' ? 'popular' : '' }}">
                                <div class="tag {{ $plan['name'] == 'Enterprise' ? '' : 'd-none' }}"><span><i
                                            class="fas fa-star"></i> Popular</span></div>
                                <form action="cart" method="post">
                                    @csrf
                                    <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                    <input type="hidden" name="planType" value="YEARLY">
                                    <input type="hidden" name="purchaseType" value="NEW">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="subscriptions" value="false">
                                    <div class="inner">
                                        @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                            <div class="plan-tag">
                                                <p class="anu">
                                                    {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                        Day</span>
                                                </p>
                                            </div>
                                        @endif
                                        <h3 class="title">{{ $plan['name'] }}</h3>
                                        <p class="price">
                                            {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }}
                                        </p>
                                        <p class="price-label">Full access</p>
                                        <ul class="list-item">
                                            <li><i class="fa fa-check"></i>
                                            {{ 'Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '') }}


{{-- $plan['name'] === 'Enterprise' ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) --}}
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                            </li>
                                            <li><i class="fa fa-check "></i>
                                                {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                            </li>
                                            {{-- @if ($plan['productType'] === 'TRAIL' && intval($plan['price']) < 1) --}}
                                                <!-- <li><i class="fa fa-check"></i>
                                                {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                </li> -->
                                                {{-- @endif --}}
                                                <li>
                                                    <i
                                                        class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                    {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                </li>
                                                {{-- <li><i
                                                        class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                    Setup Fee</li> --}}
                                        </ul>


                                        @if ($plan['productType'] != 'TRAIL')
                                            <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();"
                                                class="thm-btn borderd"
                                                href="javascript:void(0);"><span>{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Subscribe')  }}</span></a>
                                        @else
                                            <a onclick="$(`input[name='subscriptions']`).val(false);$(this).closest('form').submit();"
                                                class="thm-btn borderd" href="javascript:void(0);"><span>Buy
                                                    Now</span></a>


                                            <!-- @if ($plan['productType'] != 'TRAIL')


                                                        <a onclick="$(`input[name='subscriptions']`).val(true);$(this).closest('form').submit();"
                                                            class="thm-btn borderd"
                                                            href="javascript:void(0);"><span>{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Subscribe')  }}</span></a>
                                                    @endif -->
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-6 col-xs-12">
            <div class="single-pricing text-center">
                <!-- <form action="cart" method="post"> -->
                <!-- <input type="hidden" name="_token" value="eZIUYxcZystg9B65M0VEkHzktkfv6PnFbEDgeAj4"> <input
                        type="hidden" name="productID" value="1">
                    <input type="hidden" name="planType" value="MONTHLY">
                    <input type="hidden" name="purchaseType" value="NEW">
                    <input type="hidden" name="quantity" value="1"> -->
                <div class="inner">
                    <h2 class="title" style="color:gold;">Go + Customize</h2>
                    <h3 class="title">Contact our Sales Team for the most affordable pricing.</h3><a href="custom-plan"
                        class="thm-btn borderd" contenteditable="false" style="cursor: pointer;"><span>Get in
                            Touch</span></a>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
    integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function () {
        $(".switch").click(function () {
            if ($(this).hasClass("on")) {
                $(this).removeClass("on").addClass("off");
                $(".month").removeClass("active");
                $(".year").addClass("active");
                $('#month').hide();
                $('#year').show();
            } else {
                $(this).removeClass("off").addClass("on");
                $(".year").removeClass("active");
                $(".month").addClass("active");
                $('#month').show();
                $('#year').hide();
            }
        });
    });
    var wow = new WOW({
        boxClass: 'wow', // animated element css class (default is wow)
        animateClass: 'animated', // animation css class (default is animated)
        offset: 0, // distance to the element when triggering the animation (default is 0)
        mobile: true, // trigger animations on mobile devices (default is true)
        live: true, // act on asynchronously loaded content (default is true)
        callback: function (box) {
            // the callback is fired every time an animation is started
            // the argument that is passed in is the DOM node being animated
        },
        scrollContainer: null // optional scroll container selector, otherwise use window
    });
    wow.init();
</script>
@endsection