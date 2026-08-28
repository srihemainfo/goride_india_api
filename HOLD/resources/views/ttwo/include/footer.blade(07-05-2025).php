
<footer class="footer" data-background="assets/img/bg/footer-noise.png">
  <div class="container mxw_1350">
    <div class="row pt-2  text-center">
      <div class="col-lg-12 col-md-12 footer__col mt-30">
        @if(isset($seoData['getAllPages']) &&
        collect($seoData['getAllPages'])->count() > 0) @php $bottomItems =
        collect($seoData['getAllPages'])->filter(function ($page) { return
        $page->position == 'bottom'; }); $getAfterFive = collect($bottomItems);
        // $getAfterFive = collect($seoData['getAllPages'])->skip(5); @endphp

        <ul class="navbar-nav d-flex flex-row justify-content-center align-items-center">
          @foreach ($getAfterFive as $page)
          <li class="mx-2">
            <a
              class="mx-2"
              href="{{ url('/' . $page->url) }}"
              >{{ $page->title }}</a
            >
          </li>
          @endforeach
        </ul>

        @endif
      </div>

      <!-- <div class="col-lg-4 col-md-6 footer__col mt-30">
        <div class="footer__widget">
          <h3>About Us</h3>

          <p>
            We treat our customers like family and we're known for offering
            comfortable transportation services.

            <a href="about">Readmore</a>
          </p>
        </div>
      </div> -->

      <!-- <div class="col-lg-4 col-md-6 footer__col mt-30">
        <div class="footer__widget">
          <h3>Contact details</h3>

          <ul class="footer__links list-unstyled">
            <li class="d-none">Toronto, Canada.</li>

            <li>
              <a
                href="tel:{{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? $seoData['partnerWeb']->whatsapp_number : '0000000000' }}"
                >{{ isset($seoData['partnerWeb']->whatsapp_number) && $seoData['partnerWeb']->whatsapp_number != '' ? '+' . $seoData['partnerWeb']->whatsapp_number : '0000000000' }}</a
              >
            </li>

            <li>
              <a
                href="mailto:{{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' ? $seoData['partnerWeb']->email : 'support@goride.run' }}"
              >
                <span
                  >{{ isset($seoData['partnerWeb']->email) && $seoData['partnerWeb']->email != '' ?
									$seoData['partnerWeb']->email : 'support@goride.run' }}</span
                >
              </a>
            </li>
          </ul>
        </div>
      </div> -->

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

      <!-- <div class="col-lg-4 col-md-6 footer__col mt-30">
        <div>
          <a href="https://maps.app.goo.gl/YBDttBW6Apwpevc6A" target="_blank"
            ><img
              src="{{ asset('ttwo/images/google-review.png') }}"
              width="184"
              height="106"
          /></a>
        </div>
      </div> -->
    </div>
	<div class="row d-flex align-items-center justify-content-end mt-2" style="text-align: end;">
		<div class="col-lg-12 col-md-12 footer__col">
				<span class="copyright" style="font-size: 15px; color: #2b2b2b;">© 2025 Go Ride Run</span>
		</div>
	</div>
  </div>
</footer>

<!--<div class="cookie-consent-banner" id="cookiecontent" style="display: block;">-->
<!--	<div class="cookie-consent-banner__inner">-->
<!--		<div class="row">-->
<!--			<div class="col-md-9">-->
<!--				<div class="cookie-consent-banner__copy">-->
<!--					<div class="cookie-consent-banner__description">-->
<!--						This website stores cookies on your browser. These cookies are used to improve your experience-->
<!--						and provide more personalized service on our website and related media. To learn more about-->
<!--						the cookies and data we use, please review our Privacy Policy.-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--			<div class="col-md-3">-->
<!--				<div class="cookie-consent-banner__actions">-->
<!--					<a target="_blank" href="#" class="cookie-consent-banner__cta cookie-consent-banner__cta--secondary">-->
<!--						PRIVACY POLICY-->
<!--					</a>-->
<!--					<button class="cookie-consent-banner__cta" aria-label="Close"-->
<!--                        onclick="acceptCookies()">-->
<!--                        ACCEPT-->
<!--                    </button>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->

<script>
  window.onload = checkPopup;

  function setCookie(name, value, days) {
    let date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    let expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + "; " + expires + "; path=/";
  }

  function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function checkPopup() {
    let lastPopupTime = getCookie("Cookie_con");

    if (!lastPopupTime) {
      document.getElementById("cookiecontent").style.display = "block";
    } else {
      let currentTime = new Date().getTime();
      let lastTime = parseInt(lastPopupTime);
      let oneDay = 24 * 60 * 60 * 1000;

      if (currentTime - lastTime > oneDay) {
        document.getElementById("cookiecontent").style.display = "block";
      } else {
        document.getElementById("cookiecontent").style.display = "none";
      }
    }
  }

  function acceptCookies() {
    let currentTime = new Date().getTime();
    setCookie("Cookie_con", currentTime, 1);
    document.getElementById("cookiecontent").style.display = "none";
  }
</script>

<style>
  .cookie-consent-banner__cta {
    box-sizing: border-box;
    display: inline-block;
    min-width: 135px;
    padding: 10px 13px;
    margin-top: 15px;
    border-radius: 7px;
    background-color: #f7a20f;
    color: #f7f7f7 !important;
    text-decoration: none;
    text-align: center;
    font-size: 16px;
    line-height: 20px;
    border: 0;
  }
  .navbar-nav li a:hover{
           color: #0789FF;
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

  .header__main-wrap {
    position: relative;
    z-index: 1;
    padding: 10px 0 !important;
    background: #e3edf6;
  }

  .pt-25 {
    padding-top: 10px !important;
  }

  .pb-65 {
    padding-bottom: 27px !important;
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
