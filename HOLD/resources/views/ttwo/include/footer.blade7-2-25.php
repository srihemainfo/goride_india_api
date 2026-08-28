<footer class="footer" data-background="assets/img/bg/footer-noise.png">









	<div class="container mxw_1350">

		<div class="row pt-25 pb-65">



			<div class="col-lg-12 col-md-12 footer__col mt-30">
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
										<li class="">
											<a class="" href="{{ url('/' . $page->url) }}">{{ $page->title }}</a>
										</li>
									@endforeach
								</ul>

				@endif


			</div>



			<div class="col-lg-4 col-md-6 footer__col mt-30">

				<div class="footer__widget">

					<h3>About Us</h3>

					<p>We treat our customers like family and we're known for offering

						comfortable transportation services.

						<a href="about">Readmore</a>

					</p>

					

				</div>

			</div>

			<div class="col-lg-4 col-md-6 footer__col mt-30">

				<div class="footer__widget">

					<h3>Contact details</h3>

					<ul class="footer__links list-unstyled">

						<li class="d-none">Toronto, Canada.</li>

						<li><a
								href="tel:{{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? $seoData['partnerWeb']->whatsapp_number : '0000000000' }}">{{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? '+' . $seoData['partnerWeb']->whatsapp_number : '0000000000' }}</a>
						</li>

						<li><a
								href="mailto:{{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' ? $seoData['partnerWeb']->email : 'support@goride.run' }}">

								<span>{{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' ?
									$seoData['partnerWeb']->email : 'support@goride.run' }}</span>

							</a>

						</li>

					</ul>

				</div>

			</div>





			<!--<div class="col-lg-3 col-md-6 footer__col mt-30">-->

			<!--	<div class="footer__widget">-->

			<!--		<h3>Important Links</h3>-->

			<!--		<ul class="footer__links list-unstyled">-->

			<!--			<li><a href="terms-condition">Terms and Conditions</a></li>-->

			<!--			<li><a href="privacy-policy">Privacy Policy</a></li>-->

			<!--			<li><a href="faq">FAQ</a></li>-->

			<!--			<li><a href="https://www.Demo Themepro.ca/blog">Blog</a></li>-->

			<!--		</ul>-->

			<!--	</div>-->

			<!--</div>-->

			<div class="col-lg-4 col-md-6 footer__col mt-30">

			<div>

<a href="https://maps.app.goo.gl/YBDttBW6Apwpevc6A" target="_blank"><img
		src="{{ asset('ttwo/images/google-review.png') }}" width="184" height="106"></a>

</div>

			</div>

		</div>

	</div>

</footer>







<style>
	.header__main-wrap {
    position: relative;
    z-index: 1;
    padding: 10px 0!important;
    background: #e3edf6;
}
	.pt-25 {
    padding-top: 10px!important;
}
	.pb-65 {
    padding-bottom: 27px!important;
}
	.btn-whatsapp-pulse {

		background: #25d366;

		color: white;

		position: fixed;

		bottom: 20px;

		right: 20px;

		font-size: 40px;

		display: flex;

		justify-content: center;

		align-items: center;

		width: 0;

		height: 0;

		padding: 35px;

		text-decoration: none;

		border-radius: 50%;

		animation-name: pulse;

		animation-duration: 1.5s;

		animation-timing-function: ease-out;

		animation-iteration-count: infinite;

		z-index: 999;

	}



	@keyframes pulse {

		0% {

			box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.5);

		}



		80% {

			box-shadow: 0 0 0 14px rgba(37, 211, 102, 0);

		}

	}



	.btn-whatsapp-pulse-border {

		bottom: 120px;

		right: 20px;

		animation-play-state: paused;

	}



	.btn-whatsapp-pulse-border::before {

		content: "";

		position: absolute;

		border-radius: 50%;

		padding: 25px;

		border: 5px solid #25d366;

		opacity: 0.75;

		animation-name: pulse-border;

		animation-duration: 1.5s;

		animation-timing-function: ease-out;

		animation-iteration-count: infinite;

	}



	@keyframes pulse-border {

		0% {

			padding: 25px;

			opacity: 0.75;

		}



		75% {

			padding: 50px;

			opacity: 0;

		}



		100% {

			opacity: 0;

		}

	}



	.desk-hd {

		display: none;

	}



	.desk-vw {

		background: #00000000 !important;

	}



	@media (max-width: 992px) {

		.desk-hd {

			display: block;

		}



		.desk-vw {

			display: none;

		}

	}



	.header__logo {

		width: 34%;

	}
</style>