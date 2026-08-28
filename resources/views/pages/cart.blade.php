@extends('layouts.app')
@section('css')
<style>
    .pricing-section {
        padding: 50px 0;
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
        margin-bottom: 45px;
    }

    .pricing-section ul.switch-toggler-list {
        margin-bottom: 40px;
    }

    .list-inline li {
        display: inline-block;
    }

    .pricing-section ul.switch-toggler-list li.active a {
        color: #989898;
    }

    .pricing-section ul.switch-toggler-list li a {
        font-size: 18px;
        font-weight: 600;
        color: #323232;
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
        margin-bottom: 20px;
        margin-top: 20px;
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
        font-size: 14px;
        color: #170B35;
        font-weight: 500;
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
        margin-top: -20px;
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
        padding-top: 65px;
        padding-bottom: 65px;
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
    
    .table tr th:last-child,
    .table tr td:last-child {
        text-align: right;
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

    /*@media screen and (max-width: 576px) {*/

    /*    th, tr, td {*/
    /*        font-size: 10px;*/
    /*    }*/

    /*    .btn-primary {*/
    /*        font-size: 8px !important;*/
    /*    }*/

    /*}*/

    /* Card Design */
    .tabed-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 20px;
        background-color: #f4f4f4;
        border-radius: 10px;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }

    .package-details {
        background-color: white;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .price-label {
        font-size: 16px;
        font-weight: 600;
        color: #171f4f;
        margin-right: 20px;
    }

    .price-value {
        font-size: 16px;
        font-weight: 600;
        color: #000;
        text-align: right;
    }

    .buy-button {
        text-align: center;
    }

    .btn-primary {
        background-color: #f9bf00;
        color: white;
        border: none;
        padding: 5px 10px;
        font-size: 15px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #b00029;
    }

    .text-primary {
        --bs-text-opacity: 1;
        color: #f9bf00 !important;
    }
</style>
@endsection
@section('content')
@php


    //dd($_REQUEST);
    $cartDetails = null;
    $PayBycartdata = isset($_COOKIE['PayBycartdata']) ? json_decode($_COOKIE['PayBycartdata'], true) : null;
    $POSTCart =
        $PayBycartdata != '' && $PayBycartdata != null && $PayBycartdata != 'null' ? $PayBycartdata : $_POST;



    if ($POSTCart == null || $POSTCart == '' || $POSTCart == 'null') {
        // Original
        $POSTCart =
            $PayBycartdata != '' && $PayBycartdata != null && $PayBycartdata != 'null'
            ? $PayBycartdata
            : json_decode($_COOKIE['newFormCartData'], true);

    }



    $productID = $POSTCart['productID'] ?? ($POSTCart['cartData']['productID'] ?? '');
    $crmID = $POSTCart['crmID'] ?? ($POSTCart['cartData']['crmID'] ?? '');
    $planType = $POSTCart['planType'] ?? ($POSTCart['cartData']['planType'] ?? '');
    $purchaseType = $POSTCart['purchaseType'] ?? ($POSTCart['cartData']['purchaseType'] ?? '');
    $quantity = $POSTCart['quantity'] ?? ($POSTCart['cartData']['quantity'] ?? '');


    $subscriptions = $POSTCart['subscriptions'] ?? ($POSTCart['cartData']['subscriptions'] ?? '');




    // dd($_POST);


    $buildCheckout = [];
    if (!isset($productID) || !isset($crmID)) {
        return redirectToPage(url()->previous());
    }
    if (!isset($planType)) {
        return redirectToPage(url()->previous());
    }
    if (!isset($purchaseType)) {
        return redirectToPage(url()->previous());
    }
    if (!isset($quantity)) {
        return redirectToPage(url()->previous());
    }

    // dd($POSTCart);

    //  dd($subscriptions);
    // dd(`sdfs`);
    $buildCheckout['cartDetails']['productID'] = $productID;
    $buildCheckout['cartDetails']['crmID'] = $crmID;
    $buildCheckout['cartDetails']['planType'] = $planType;
    $buildCheckout['purchaseType'] = $purchaseType;
    $buildCheckout['utm_campaign'] = $_COOKIE['utm_campaign']??null;
    $buildCheckout['utm_source'] = $_COOKIE['utm_source']??null;


    if (isset($subscriptions) && $subscriptions) {
        $buildCheckout['subscriptions'] = filter_var($subscriptions, FILTER_VALIDATE_BOOLEAN);
    }


    //dd($buildCheckout, gettype($subscriptions));

    $buildCheckout['cartDetails']['quantity'] = $quantity;


    $response = Http::withToken($_COOKIE['sessionToken'])->post(url('/api/addToCardProduct'), $buildCheckout);
    if ($response->successful()) {
        $authUser = $response->json();
        // dd($authUser);
        if (isset($authUser['status']) && $authUser['status'] === 'success') {
            $cartDetails = $authUser['data'] ?? null;
        } else {

            return redirectToPage(url()->previous());

        }
    }

    setrawcookie('transaction_id', $cartDetails['transaction_id'], time() + 86400 * 30, '/');
    // if (isset($_POST)) {
    // dd(json_encode($cartDetails));


    // setrawcookie('newFormCartData', json_encode($cartDetails), time() + 86400 * 30, '/');
    // dd($POSTCart, $buildCheckout, $cartDetails);

    // dd($cartDetails);

    // }
@endphp
<!-- Breadcrumb -->
<section class="page-header">
    <div class="page-header-shape"></div>
    <div class="container">
        <div class="page-header-info">
            <h4>Flexible Pricing Plans</h4>
            <h2>Perfect Plan for Your <span>Business Needs!</span></h2>
            <p>Explore our affordable and customizable pricing options designed to fit businesses of all sizes.</p>
        </div>
    </div>
</section>
<section class="pricing-section" id="pricing">
    <div class="container">
        <div class="sec-title text-center">
            <h2>Cart Details</h2>
            <p>Enjoy the convenience of reliable rides at prices that fit your budget with GoRide.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover border table-borderless">
                <tr class="border">
                    <th>S.No</th>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <th>Domain name</th>
                    @endif
                    <th>Package Name</th>
                    <th>Plan Type</th>
                    <!--<th>Trial Days</th>-->
                    <!--<th>Validity Days</th>-->
                    <th>Total Days</th>
                    <th>Total</th>
                </tr>
                <tr>
                    <td>1.</td>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <td>{{ $cartDetails['cartData']['crmDetails']['subDomainName'] }}</td>
                    @endif
                    <td>{{ $cartDetails['cartData']['productDetails']['name'] }}</td>
                    <td>{{ ($cartDetails['cartData']['planType'] === 'TRAIL') ? 'Trial' : ucfirst(strtolower($cartDetails['cartData']['planType'])) }}
                    </td>
                    <!--<td>{{ $cartDetails['cartData']['trailsDays'] ?? '-' }} Days</td>-->
                    <!--<td>{{ $cartDetails['cartData']['noOfDays'] ?? '-' }} Days</td>-->
                    <td>{{ $cartDetails['cartData']['totalDays'] ?? '-' }} Days</td>
                    <td>{{ ($cartDetails['cartData']['currency'] === 'INR' ? '₹' : '$') . $cartDetails['cartData']['finalTotal'] }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <td></td>
                    @endif
                    <td>Net Total</td>
                    <td>{{ ($cartDetails['cartData']['currency'] === 'INR' ? '₹' : '$') . $cartDetails['cartData']['finalTotal'] }}

                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <!--<input class="form-control m-0 border" type="text" placeholder="Coupen Code" style="width: 200px;display: inline;" />-->
                        <!--<button class="btn btn-primary ms-2">Apply Coupen</button>-->
                    </td>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <td></td>
                    @endif
                    
                    @if($cartDetails['cartData']['discountAmt']>0) 
                    
                    <td>Discount Total</td>
                    <td class="text-success">- {{ ($cartDetails['cartData']['currency'] === 'INR' ? '₹' : '$') }}
                        {{ $cartDetails['cartData']['discountAmt'] }}
                    </td>
                       @endif
                    
                </tr>
                <tr class="border">
                    <td colspan="3"></td>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <td></td>
                    @endif
                    <th>Grand Total</th>
                    <th>{{ ($cartDetails['cartData']['currency'] === 'INR' ? '₹' : '$') }}
                        {{ $cartDetails['cartData']['grandtotal'] }}
                    </th>
                </tr>
                <tr class="border">
                    <td colspan="4"></td>
                    @if ($cartDetails['cartData']['crmID'] != null)
                        <td></td>
                    @endif
                    <td>


                        @if (isset($cartDetails['cartData']['planType']) && $cartDetails['cartData']['planType'] === 'TRAIL')
                            <button type="button" id="payNow" onclick="buyTrailCRM()" class="btn btn-primary"><i
                                    class="fas fa-share"></i> Checkout</button>
                        @else

                            @if ((isset($cartDetails['cartData']['subscriptions']) && $cartDetails['cartData']['subscriptions']))

                                <button type="button" id="payNow"
                                    onclick="startRazSub('{{$cartDetails['cartData']['currency']}}')" class="btn btn-primary"><i
                                        class="fas fa-share"></i>Subscribe</button>

                                <!-- @else <div id="payment_options" class="btn btn-primary"></div> -->



                            @endif

                        @endif



                        <!-- @if ($cartDetails['cartData']['currency'] === 'INR')



                            <button type="button" id="payNow"
                                onclick="{{ isset($cartDetails['cartData']['planType']) && $cartDetails['cartData']['planType'] === 'TRAIL' ? 'buyTrailCRM()' : ((isset($cartDetails['cartData']['subscriptions']) && $cartDetails['cartData']['subscriptions']) ? 'startRazSub()' : 'startPayment()') }}"
                                class="btn btn-primary"><i class="fas fa-share"></i>
                                {{((isset($cartDetails['cartData']['subscriptions']) && $cartDetails['cartData']['subscriptions']) ? 'Subscribe' : 'Checkout')}}</button>


                        @else

                            @if (isset($cartDetails['cartData']['planType']) && $cartDetails['cartData']['planType'] === 'TRAIL')
                                <button type="button" id="payNow" onclick="buyTrailCRM()" class="btn btn-primary"><i
                                        class="fas fa-share"></i> Checkout</button>
                            @else

                                @if ((isset($cartDetails['cartData']['subscriptions']) && $cartDetails['cartData']['subscriptions']))

                                    <button type="button" id="payNow" onclick="startRazSub('USD')" class="btn btn-primary"><i
                                            class="fas fa-share"></i>Subscribe</button>

                                @else

                                
                                    <div id="payment_options" class="btn btn-primary"></div>
                                @endif

                            @endif

                        @endif -->
                    </td>
                </tr>
            </table>
        </div>


    </div>
</section>
@endsection
@section('script')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
    integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script
    src="https://www.paypal.com/sdk/js?client-id={{config('paypal.client_id')}}&currency={{config('paypal.currency')}}&intent=capture"></script>
<script>
    (() => {
        var cartData = <?php echo json_encode($POSTCart, JSON_PRETTY_PRINT); ?>;

        setCookie('newFormCartData', JSON.stringify(cartData), 1);
        @if ($cartDetails['cartData']['currency'] != 'INR' && !(isset($cartDetails['cartData']['subscriptions']) && $cartDetails['cartData']['subscriptions']))
            paypal.Buttons({
                createOrder: function () {
                    var h = new FormData();
                    h.append('transaction_id', getCookie('transaction_id'));
                    const sessionToken = getCookie("sessionToken");
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    // const btn = $("#yourButtonId"); // Update this with the actual button ID you are using
                    // // Show button loading state
                    // btn.html(
                    //     `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    // ).prop('disabled', true);
                    // Make the fetch request
                    return fetch(origin + "/api/paypalInitiate", {
                        method: 'POST',
                        headers: {
                            "Authorization": 'Bearer ' + sessionToken,
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: h,
                    })
                        .then(response => response.json()) // Parse JSON response
                        .then(response => {
                            if (response !== "") {
                                if (response.status === 'success') {
                                    // if (response.data.redirectPay != null && response.data.redirectPay !== undefined && response.data.redirectPay !== '') {
                                    //     window.location.href = response.data.redirectPay;
                                    // } else {
                                    //     showToast('error', 'Payment initiate process failed. Please Try later', 5000);
                                    // }
                                    setCookie("order_id", response.data.orderDetails.id, 1);
                                    return response.data.orderDetails.id;
                                } else {
                                    // Loading Off 
                                    // btn.html(`Buy Now`).prop('disabled', false);
                                    showToast('error', response.message, 5000);
                                }
                            }
                            // Loading Off
                            // btn.html(`Buy Now`).prop('disabled', false);
                        })
                        .catch((error) => {
                            showToast("error", "Request failed", 5000);
                            // btn.html(`Buy Now`).prop('disabled', false);
                            console.error('Request failed', error);
                        });
                    // return fetch("/create/" + document.getElementById("paypal-amount").value)
                    //     .then((response) => response.text())
                    //     .then((id) => {
                    //         return id;
                    //     });
                },
                onApprove: function () {
                    var h = new FormData();
                    h.append('token', getCookie('order_id'));
                    const sessionToken = getCookie("sessionToken");
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    // const btn = $("#yourButtonId"); // Update this with the actual button ID you are using
                    // // Show button loading state
                    // btn.html(
                    //     `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    // ).prop('disabled', true);
                    // Make the fetch request
                    return fetch(origin + "/api/paypalSuccess", {
                        method: 'POST',
                        headers: {
                            "Authorization": 'Bearer ' + sessionToken,
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: h,
                    })
                        .then(response => response.json()) // Parse JSON response
                        .then(response => {
                            if (response !== "") {
                                if (response.status === 'success') {
                                    // console.log(response);return false;
                                    if (response.status === 'success') {
                                        paymentSuccess('success', response.message, origin + '/dashboard');
                                        deleteCookie('transaction_id');
                                    } else {
                                        showToast('error', response.message, 5000);
                                    }
                                    // if (response.data.redirectPay != null && response.data.redirectPay !== undefined && response.data.redirectPay !== '') {
                                    //     window.location.href = response.data.redirectPay;
                                    // } else {
                                    // showToast('error', 'Payment initiate process failed. Please Try later', 5000);
                                    // }
                                    // setCookie("order_id", response.data.orderDetails.id, 1);
                                    // return response.data.orderDetails.id;
                                } else {
                                    // Loading Off 
                                    // btn.html(`Buy Now`).prop('disabled', false);
                                    showToast('error', response.message, 5000);
                                }
                            }
                            // Loading Off
                            // btn.html(`Buy Now`).prop('disabled', false);
                        })
                        .catch((error) => {
                            showToast("error", "Request failed", 5000);
                            // btn.html(`Buy Now`).prop('disabled', false);
                            console.error('Request failed', error);
                        });
                    // return fetch("/complete", { method: "post", headers: { "X-CSRF-Token": '{{csrf_token()}}' } })
                    //     .then((response) => response.json())
                    //     .then((order_details) => {
                    //         console.log(order_details);
                    //         document.getElementById("paypal-success").style.display = 'block';
                    //         //paypal_buttons.close();
                    //     })
                    //     .catch((error) => {
                    //         console.log(error);
                    //     });
                },
                onCancel: function (data) {
                    //todo
                    location.reload();
                },
                onError: function (err) {
                    //todo
                    console.log(err);
                }
            }).render('#payment_options');
        @endif
    })();
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
        boxClass: 'wow',
        animateClass: 'animated',
        offset: 0,
        mobile: true,
        live: true,
        callback: function (box) {
        },
        scrollContainer: null
    });
    wow.init();
    const startRazSub = (currency) => {
        try {
            const btn = $(`#payNow`);
            var h = new FormData();
            h.append(`transaction_id`, getCookie('transaction_id'));
            $.ajax({
                // url: origin + `/api/${(currency === 'USD' ? 'paypalSubInitiate' : 'razorpaySubInitiate')}`,
                url: origin + `/api/razorpaySubInitiate`,
                type: "POST",
                headers: {
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    // let response = JSON.parse(res);
                    if (response != "") {
                        if (response.status === 'success') {
                            // if  {

                            // }
                            // let redirectPay = (currency === 'USD') ? response?.data?.orderDetails?.links[0]?.href : response?.data?.orderDetails?.short_url;
                          
                            let redirectPay = response?.data?.orderDetails?.short_url;
                            
                            if (redirectPay) {
                                // window.location.href = redirectPay;
                                
                                
                                window.open(
  redirectPay
//   ,          
//   'popUpWindow',         
//   'width=1000' 
);
window.location.href = origin + '/package-history';
                            } else {
                                console.error("Redirect URL not found.");
                                showToast('error', "Redirect URL not found.", 5000);
                            }


                        } else {
                            // Loading Off 
                            btn.html(`Subscribe`).prop('disabled', false);
                            showToast('error', response.message, 5000);
                        }
                    }
                    // Loading Off 
                    btn.html(`Subscribe`).prop('disabled', false);
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`Subscribe`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                }
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }

    const startPayment = () => {
        try {
            const btn = $(`#payNow`);
            var h = new FormData();
            h.append(`transaction_id`, getCookie('transaction_id'));
            $.ajax({
                url: origin + "/api/razorpayInitiate",
                type: "POST",
                headers: {
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    // let response = JSON.parse(res);
                    if (response != "") {
                        if (response.status === 'success') {
                            // showToast('success', response.result, 5000);
                            // const customerID = response.customerDetails.id.toString();
                            var options = {
                                "key": response.data
                                    .razaorKey, // Your Razorpay key ID generated from the Dashboard
                                "amount": response.data.cartDetails
                                    .grandtotal, // Amount in currency subunits (e.g., 50000 paise = 500 INR)
                                "currency": response.data.cartDetails
                                    .currency, // Currency code (e.g., 'INR')
                                "name": "Go Ride", // Your business name
                                "description": "Transaction Description", // Optional description for the transaction
                                "image": origin +
                                    '/goride/img/Go-Ride-fav-icon.webp', // URL of your company’s logo
                                "order_id": response.data.orderDetails
                                    .id, // Order ID created on your server
                                "remember_customer": true, // Whether to remember the customer's details
                                // "callback_url": origin + '/razorpay/razorpayCallBack.php', // URL to handle payment callback
                                "handler": function (response) {
                                    // Handle the payment response here
                                    console.log('Payment successful:', response);
                                    // alert('Payment successful!');
                                    handlePaymentSuccess(response);
                                },
                                "prefill": {
                                    "name": response.data.customerDetails.name, // Customer’s name
                                    "email": response.data.customerDetails
                                        .email, // Customer’s email
                                    "contact": response.data.customerDetails
                                        .mobile // Customer’s contact number
                                },
                                "notes": {
                                    "address": "Razorpay Corporate Office" // Optional notes related to the payment
                                },
                                "theme": {
                                    "color": "#3399cc" // Theme color for the Razorpay payment popup
                                },
                                "modal": {
                                    "backdropclose": true // Whether clicking outside the modal should close it
                                }
                            };
                            console.log(options);
                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            // Loading Off 
                            btn.html(`Buy Now`).prop('disabled', false);
                            showToast('error', response.message, 5000);
                        }
                    }
                    // Loading Off 
                    btn.html(`Buy Now`).prop('disabled', false);
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`Buy Now`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                }
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    const startPayPal = () => {
        try {
            const btn = $(`#payNow`);
            var h = new FormData();
            h.append(`transaction_id`, getCookie('transaction_id'));
            $.ajax({
                url: origin + "/api/paypalInitiate",
                type: "POST",
                headers: {
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    // let response = JSON.parse(res);
                    if (response != "") {
                        if (response.status === 'success') {
                            if (response.data.redirectPay != null && response.data.redirectPay != undefined && response.data.redirectPay != '') {
                                window.location.href = response.data.redirectPay;
                            } else {
                                showToast('error', 'Payment initiate process failed. Please Try later', 5000);
                            }
                        } else {
                            // Loading Off 
                            btn.html(`Buy Now`).prop('disabled', false);
                            showToast('error', response.message, 5000);
                        }
                    }
                    // Loading Off 
                    btn.html(`Buy Now`).prop('disabled', false);
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`Buy Now`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                }
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    const buyTrailCRM = () => {
        try {
            // Prevent the form from submitting
            // event.preventDefault();
            // const btn = $(form).find('button[type="submit"]');
            const btn = $(`#payNow`);
            // if (!required(mobileNo)) {
            //     showToast("error", "Kindly Enter the mobile no!", 5000);
            //     // toast('error', 'Kindly Enter the mobile no!');
            //     return false;
            // }
            // var formArray = $().serializeArray();
            var h = new FormData();
            h.append(`transaction_id`, getCookie('transaction_id'));
            // $.each(formArray, function(i, field) {
            //     h.append(field.name, field.value);
            // });
            $.ajax({
                url: origin + "/api/buyTrailCRM",
                type: "POST",
                headers: {
                    // "Accept": "application/json;",
                    // "Content-Type": "application/json;",
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    let data = response.data;
                    
                    if (response != "") {
                        if (response.status === 'success') {
                            // showToast('success', response.result, 5000);
                            // const customerID = response.customerDetails.id.toString();
                            // var options = {
                            //     "key": response.data
                            //         .razaorKey, // Your Razorpay key ID generated from the Dashboard
                            //     "amount": response.data.cartDetails
                            //         .grandtotal, // Amount in currency subunits (e.g., 50000 paise = 500 INR)
                            //     "currency": response.data.cartDetails
                            //         .currency, // Currency code (e.g., 'INR')
                            //     "name": "Go Ride", // Your business name
                            //     "description": "Transaction Description", // Optional description for the transaction
                            //     "image": origin +
                            //         '/goride/img/Go-Ride-fav-icon.webp', // URL of your company’s logo
                            //     "order_id": response.data.orderDetails
                            //         .id, // Order ID created on your server
                            //     "remember_customer": true, // Whether to remember the customer's details
                            //     // "callback_url": origin + '/razorpay/razorpayCallBack.php', // URL to handle payment callback
                            //     "handler": function(response) {
                            //         // Handle the payment response here
                            //         console.log('Payment successful:', response);
                            //         // alert('Payment successful!');
                            //         handlePaymentSuccess(response);
                            //     },
                            //     "prefill": {
                            //         "name": response.data.customerDetails.name, // Customer’s name
                            //         "email": response.data.customerDetails
                            //             .email, // Customer’s email
                            //         "contact": response.data.customerDetails
                            //             .mobile // Customer’s contact number
                            //     },
                            //     "notes": {
                            //         "address": "Razorpay Corporate Office" // Optional notes related to the payment
                            //     },
                            //     "theme": {
                            //         "color": "#3399cc" // Theme color for the Razorpay payment popup
                            //     },
                            //     "modal": {
                            //         "backdropclose": true // Whether clicking outside the modal should close it
                            //     }
                            // };
                            // console.log(options);
                            // var rzp1 = new Razorpay(options);
                            // rzp1.open();
                            paymentSuccess('success', response.message, origin + '/dashboard');
                            setCookie('SETUP_CRM_POPUP', JSON.stringify(data), 1);
                            deleteCookie('transaction_id');
                            
                        } else {
                            // Loading Off 
                            btn.html(`Buy Now`).prop('disabled', false);
                            showToast('error', response.message, 5000);
                        }
                    }
                    // Loading Off 
                    btn.html(`Buy Now`).prop('disabled', false);
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`Buy Now`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                }
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    const handlePaymentSuccess = (response) => {
        try {
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait a moment',
                allowOutsideClick: false, // Prevent closing by clicking outside
                didOpen: () => {
                    Swal.showLoading(); // Show the loading spinner
                }
            });
            var h = new FormData();
            // h.append('method', 'paymentSuccess');
            // Append the successRes object as flattened key-value pairs
            h.append('successRes[razorpay_payment_id]', response.razorpay_payment_id);
            h.append('successRes[razorpay_order_id]', response.razorpay_order_id);
            h.append('successRes[razorpay_signature]', response.razorpay_signature);
            $.ajax({
                url: origin + "/api/razorpaySuccess",
                type: 'POST',
                contentType: 'application/json',
                data: h,
                headers: {
                    // "Accept": "application/json; charset=utf-8",
                    // "Content-Type": "application/json; charset=utf-8",
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                success: function (response) {
                    // let response = JSON.parse(res);
                    if (response != "") {
                        // if (response.type == '1') {
                        if (response.status === 'success') {
                            paymentSuccess('success', response.message, origin + '/dashboard');
                            deleteCookie('transaction_id');
                        } else {
                            showToast('error', response.message, 5000);
                        }
                    }
                    console.log('Server response:', response);
                },
                error: function (xhr, status, error) {
                    console.error('Error sending payment details:', error);
                }
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    ///////  New ///////
    // function toast(icon, message) {
    //     const Toast = Swal.mixin({
    //         toast: true,
    //         position: 'top-end',
    //         showConfirmButton: false,
    //         timer: 5000,
    //         timerProgressBar: true,
    //         didOpen: (toast) => {
    //             toast.addEventListener('mouseenter', Swal.stopTimer);
    //             toast.addEventListener('mouseleave', Swal.resumeTimer);
    //         }
    //     });
    //     Toast.fire({
    //         icon: icon,
    //         title: message
    //     });
    // }
    function paymentSuccess(icon, titleText, redirectUrl = '') {
        Swal.fire({
            title: titleText,
            icon: icon,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OKAY',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                if (redirectUrl === '') {
                    location.reload();
                } else {
                    window.location.href = redirectUrl;
                }
            }
        });
    }
</script>
@endsection