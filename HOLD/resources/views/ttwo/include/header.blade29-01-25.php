<header class="header">

	<div class="header__top-wrap">

		<div class="container mob-nav">

			<div class="header__top ul_li_between mt-none-15">

				<div class="header__social mt-15">

					<a
						href="index.html">{{ isset($seoData['partnerWeb']->company_name) && $seoData['partnerWeb']->company_name != '' ? $seoData['partnerWeb']->company_name : 'Go Ride Run' }}</a>

				</div>

				<ul class="header__info ul_li mt-15">

					<li><i class="far fa-phone"></i><a class="text-dark"
							href="javacript:void(0);">{{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? '+' . $seoData['partnerWeb']->whatsapp_number : '0000000000' }}</a>
					</li>

					<li>

						<i class="far fa-envelope"></i><a class="text-dark" href="javacript:void(0);"><span class="">{{
								isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' ?
								$seoData['partnerWeb']->email : 'support@goride.run' }}</span></a>
					</li>

				</ul>

			</div>

		</div>

	</div>

	<div class="header__main-wrap desk-hd" data-uk-sticky="top: 250; animation: uk-animation-slide-top;">

		<div class="container-fluid">

			<nav class="navbar navbar-expand-lg navbar-light">

				<div class="container-fluid">

					<div class="header__main ul_li_between">

						<div class="header__main-left ul_li flex-1">

							<div class="header__bar hamburger_menu">

								<a href="javacript:void(0);"><i class="fa fa-bars"></i></a>

							</div>

							<div class="header__logo ml-50">

								<a href="home">

									<!--<img src="{{ asset('ttwo/images/logo-white1.png') }}" alt="logo">-->

									<h1><img src="{{ asset(isset($seoData['partnerWeb']->logo) && $seoData['partnerWeb']->logo != '' ? $seoData['partnerWeb']->logo : 'tone/images/demoimage.png') }}"
											alt="logo" /></h1>

								</a>

							</div>

						</div>

						<div class="main-menu navbar navbar-expand-lg">

							<nav class="main-menu__nav collapse navbar-collapse">








								@if(isset($seoData['getAllPages']) && collect($seoData['getAllPages'])->count() > 0)
																@php

																	$topItems = collect($seoData['getAllPages'])->filter(function ($page) {
																		return $page->position == 'top';
																	});

																	$getFirstFive = collect($topItems)->take(5);
																@endphp
																<ul class="navbar-nav">
																	@foreach ($getFirstFive as $page)
																		<li class="">
																			<a class="" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
																		</li>
																	@endforeach
																</ul>
								@endif

								<!--<ul>-->

								<!--	<li><a href="Demo Themeusine-service-toronto">Demo Themeusine-service</a></li>-->

								<!--	<li><a href="airport-Demo Theme-services">Airport Transfers</a></li>-->

								<!--	<li><a href="fleet">Our Fleet</a></li>-->

								<!--	<li><a href="contact">Contact us</a></li>-->

								<!--</ul>-->

							</nav>

						</div>

						<div class="header__main-right ul_li">

						</div>

					</div>

				</div>

			</nav>

		</div>

	</div>

</header>