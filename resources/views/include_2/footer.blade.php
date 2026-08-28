@php
use Illuminate\Support\Facades\Http;

$getToken = $_COOKIE['sessionToken'] ?? null;
$apiUrl = $getToken ? 'getChat-message' : 'getChat-con';
$apipostUrl = $getToken ? 'bot-chats' : 'bot-chat';

$getMessUrl = url('/api/' . $apiUrl);
$postMessUrl = url('/api/' . $apipostUrl);


try {
$http = Http::timeout(5); // Optional: avoid long waits

if (!empty($getToken)) {
$response = $http->withHeaders([
'Authorization' => 'Bearer ' . $getToken,
])->post($getMessUrl);


} else {
$response = $http->post($getMessUrl);
}

if ($response->successful()) {
$data = $response->json();
}else{

$data = [];
}


} catch (\Exception $e) {
$data = [];
}
@endphp

<style>
@media screen and (max-width: 490px) {

    a.blantershow-chat {
        bottom: 45px;
        left: 12px;
        padding: 10px 10px 4px 10px;
    }

    a.bot-chat {
        bottom: 100px;
        left: 10px;
        padding: 10px 10px 4px 10px;
    }

    a.blantershow-chat i {
        transform: scale(1.1);
        font-size: 25px;
    }

    a.bot-chat i {
        transform: scale(1.1);
        font-size: 25px;
    }

}

li:hover img.twitter-x {
    filter: invert(0) !important;
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

.cookie-consent-banner__inner {
    margin: 0 auto;
    padding: 20px 20px;
    background: #000;
}

.cookie-consent-banner__description {
    color: #fff;
    font-size: 16px;
    line-height: 24px;
}

.cookie-consent-banner__cta--secondary {
    padding: 9px 13px;
    border: 2px solid #003d4c;
    background-color: #fff;
    color: #000;
    border-radius: 7px;
    border: 0;
    margin-right: 4px;
}

.cookie-consent-banner__cta {
    box-sizing: border-box;
    display: inline-block;
    min-width: 135px;
    padding: 10px 13px;
    margin-top: 15px;
    border-radius: 7px;
    background-color: #f7a20f;
    color: #f7f7f7;
    text-decoration: none;
    text-align: center;
    font-size: 16px;
    line-height: 20px;
    border: 0;
}

.cookie-consent-banner__cta:hover {
    color: #000;
}

@media screen and (max-width: 576px) {
    .cookie-consent-banner__description {
        font-size: 12px;
    }

    .cookie-consent-banner__actions {
        display: flex;
    }

    .cookie-consent-banner__cta {
        min-width: 50%;
        padding: 5px 10px;
        font-size: 13px;
    }
}

/*#chatToggleBtn {*/
/*  position: fixed;*/
/*  bottom: 20px;*/
/*  right: 20px;*/
/*  background-color: #075E54;*/
/*  color: white;*/
/*  border: none;*/
/*  padding: 12px 20px;*/
/*  border-radius: 30px;*/
/*  box-shadow: 0 4px 8px rgba(0,0,0,0.2);*/
/*  z-index: 1000;*/
/*  cursor: pointer;*/
/*}*/

#chatBox {
    position: fixed;
    bottom: 100px;
    left: 20px;
    width: 360px;
    height: 500px;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
}

#chatHeader {
    background-color: #075E54;
    color: white;
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
}

#closeBtn {
    font-size: 24px;
    color: white;
    background: transparent;
    border: none;
    cursor: pointer;
}

#chatMessages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #e5ddd5;
    display: flex;
    flex-direction: column;
}

.chat-msg {
    padding: 10px 14px;
    border-radius: 15px;
    margin: 6px 0;
    max-width: 80%;
    word-wrap: break-word;
}

.user-msg {
    background-color: #dcf8c6;
    align-self: flex-end;
    margin-left: auto;
}

.bot-msg {
    background-color: #fff;
    align-self: flex-start;
}

.suggestion {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 20px;
    padding: 6px 12px;
    margin: 5px 3px;
    font-size: 14px;
    cursor: pointer;
    display: inline-block;
}

#chatForm {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ccc;
    background: white;
}

#chatInput {
    flex: 1;
    padding: 8px 10px;
    border-radius: 20px;
    border: 1px solid #ccc;
}

#sendBtn {
    margin-left: 10px;
    background-color: #25D366;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    color: white;
}

@media (max-width: 767.98px) {
    .two-column-list {
        columns: 2;
    }
}
</style>
<!-- Footer -->
<footer class="footer clearfix">


    <div class="container">
        <!-- first footer -->
        <div class="first-footer">
            @if(array_key_exists('listPages', $seoTags))
            @if (count($seoTags['listPages']) > 0)



            <div class="row  footerbtn mb-2">
                <div class="col-sm-12 col-md-12">
                    <h2 style="color:rgb(255, 253, 253) !important;">Top locations</h2>
                </div>
                <div class="col-sm-12 col-md-12">

                    @foreach ($seoTags['listPages'] as $key)
                    <a style="color:rgb(255, 253, 253) !important;" href="{{ url($key->slug) }}">{{ $key->name }}</a>
                    @if (!$loop->last) |
                    @endif
                    @endforeach


                </div>
            </div>


            @endif

            @endif



            <div class="row">
                <div class="col-md-12">
                    <div class="links dark footer-contact-links">
                        <div class="footer-contact-links-wrapper">
                            <!--<div class="footer-contact-link-wrapper">-->
                            <!--    <div class="image-wrapper footer-contact-link-icon">-->
                            <!--        <div class="icon-footer"> <i class="fa-brands fa-canadian-maple-leaf"></i> </div>-->
                            <!--    </div>-->
                            <!--    <div class="footer-contact-link-content">-->
                            <!--        <h6>Whatsapp Only (Canada*)</h6>-->
                            <!--        <p>-->
                            <!--            <a href="tel:+16473661867">+1 (647) 3661867</a>-->
                            <!--        </p>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="footer-contact-links-divider"></div>-->
                            <div class="footer-contact-link-wrapper">
                                <div class="image-wrapper footer-contact-link-icon">
                                    <div class="icon-footer"> <i class="fa-solid fa-earth-asia"></i> </div>
                                </div>
                                <div class="footer-contact-link-content">
                                    <h6 class="fs-6">Call us</h6>
                                    <p class="fs-6">
                                        <a href="tel:+916369742104
">+91 63697 42104
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="footer-contact-links-divider"></div>
                            <div class="footer-contact-link-wrapper">
                                <div class="image-wrapper footer-contact-link-icon">
                                  <a href="https://whatsapp.com/channel/0029Vb7NmBuKbYMF19jK1u30"  class="icon-footer-whatsapp">
  <i class="fa-brands fa-whatsapp text-white"></i>
</a>

                                </div>
                                <div class="footer-contact-link-content">
                                    <h6 class="fs-6">WhatsApp Channel</h6>
                                    <p class="fs-6">
                                        <a href="https://whatsapp.com/channel/0029Vb7NmBuKbYMF19jK1u30">
                                            Follow for Latest Updates
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div class="footer-contact-links-divider"></div>
                            <div class="footer-contact-link-wrapper">
                                <div class="image-wrapper footer-contact-link-icon">
                                    <div class="icon-footer"> <i class="omfi-envelope"></i> </div>
                                </div>
                                <div class="footer-contact-link-content">
                                    <h6 class="fs-6">Write to us</h6>
                                    <p class="fs-6">
                                        <a href="mailto:support@goride.run">support@goride.run</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- second footer -->
        <div class="second-footer pt-3" id="footer">
            <div class="row">
                <!-- about & social icons -->
                <div class="col-md-4 widget-area">
                    <div class="widget clearfix">
                        <div class="footer-logo"><img src="{{ asset('goride/img/logo-light.png') }}" alt=""></div>
                        <!-- <div class="footer-logo"><h2>CARE<span>X</span></h2></div> -->
                        <div class="widget-text">
                            <p>Have questions or need support? Reach out to our team at </p>
                            <div class="social-icons">
                                <ul class="list-inline text-center">
                                    <li><a href="https://api.whatsapp.com/send/?phone=916369742104&text=Hi%2C%20Need%20Taxi%20%2F%20Cab%20Dispatch%20Software%20System%2C%20connect%20me.&type=phone_number&app_absent=0"
                                            target="_blank"><i class="fa-brands fa-whatsapp"></i></a></li>
                                    <li><a href="https://x.com/go_rides8499" target="_blank">
                                            <img src="https://cdn-icons-png.flaticon.com/16/5968/5968958.png"
                                                class="img-fluid twitter-x"
                                                style=" height: 12px;width: 13px;filter: invert(1);">
                                        </a></li>
                                    <li><a href="https://www.instagram.com/go_ride.run/" target="_blank"><i
                                                class="fa-brands fa-instagram"></i></a></li>
                                    <li><a href="https://www.facebook.com/goride25" target="_blank"><i
                                                class="fa-brands fa-facebook-f"></i></a></li>
                                    <li><a href="https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow"
                                            target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
                                    <li><a href="https://www.linkedin.com/in/go-rides" target="_blank"><i
                                                class="fa-brands fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- quick links -->
                <div class="col-md-3 offset-md-1 widget-area">
                    <div class="widget clearfix usful-links">
                        <h3 class="widget-title">Quick Links</h3>
                        <ul class="two-column-list">
                            <li><a href="/about">About</a></li>
                            <li><a href="/contact">Contact</a></li>
                            <li><a href="/faq">FAQ</a></li>
                            <li><a href="/our-clients">Our Clients</a></li>
                            <li><a href="/terms">Terms & Conditions</a></li>
                            <li><a href="/privacy-policy">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <!-- subscribe -->
                <div class="col-md-3 offset-md-1 widget-area service_offerings_footer">
                    <div class="widget clearfix usful-links">
                        <h3 class="widget-title">Service Offerings</h3>
                        <ul>
                            <li><a href="/crm-with-dispatch">CRM with Dispatch System</a></li>
                            <li><a href="/driver-app">My Riders App</a></li>
                            <li><a href="/passenger-app">Passenger App</a></li>
                        </ul>
                        <div class="text-center text-md-start">
                            <h3 class="widget-title">Go Ride Partner App</h3>
                            <a href="https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&pcampaignid=web_share"
                                target="_blank" id="app_link">
                                <img src="{{ asset('goride/img/google-play.png') }}" alt="google-play"
                                    style="width:160px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- bottom footer -->
        <div class="bottom-footer-text">
            <div class="row copyright">
                <div class="col-md-12">
                    <p class="mb-0 text-center">&copy;<?php echo date('Y'); ?> <a
                            href="https://www.goride.net.in/">GORIDE RUN PRIVATE LIMITED</a>. All
                        rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="cookie-consent-banner " id="cookiecontent">
    <div class="cookie-consent-banner__inner ">
        <div class="row">
            <div class="col-md-9">
                <div class="cookie-consent-banner__copy">
                    <div class="cookie-consent-banner__description">This website stores cookies on your browser. These
                        cookies are used to improve your experience and provide more personalized service on our website
                        and related media. To learn more about the cookies and data we use, please review our Privacy
                        Policy.
                    </div>
                </div>
            </div>
            <div class="col-md-3 ">
                <div class="cookie-consent-banner__actions">
                    <a target="_blank" href="/privacy-policy"
                        class="cookie-consent-banner__cta cookie-consent-banner__cta--secondary">
                        PRIVACY POLICY
                    </a>
                    <button class="cookie-consent-banner__cta" aria-label="Close"
                        onclick="setCookie('acceptCookie', 'Accepted', 30 );$(`#cookiecontent`).hide();">ACCEPT</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Demo Popup -->
<div class="modal fade" id="demoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Get start
                    <br> <span>Begin Your Journey with Seamless Dispatch System</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="booking-box">
                    <div class="booking-inner clearfix">
                        <form method="post" action="#0" class="form1 contact__form clearfix">
                            <!-- form message -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-success contact__msg" style="display: none" role="alert">
                                        Your message was sent successfully. </div>
                                </div>
                            </div>
                            <!-- form elements -->
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <input name="name" type="text" placeholder="First Name *" required>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <input name="name" type="text" placeholder="Last Name *" required>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <input name="email" type="email" placeholder="Email *" required>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <input name="phone" type="text" placeholder="Phone *" required>
                                </div>
                                <div class="col-lg-12 col-md-12 form-group">
                                    <textarea name="message" id="message" cols="30" rows="4"
                                        placeholder="Additional Note"></textarea>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <button type="submit" class="booking-button mt-15">Get Start</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="videoModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <iframe id="videoIframe" width="620" height="315" src="" frameborder="0" allow="autoplay; encrypted-media"
            allowfullscreen></iframe>
    </div>
</div>

<div id='whatsapp-chat' class='hide'>
    <div class='home-chat'>
        <div class='header-chat'>
            <div class='head-home'>
                <h3>Hello!</h3>
                <h2>How can I help you?</h2>
                <p>Click one of our representatives below to chat on WhatsApp or send us an email to support@goride.run
                </p>
            </div>
            <div class='get-new hide'>
                <div id='get-label'></div>
                <div id='get-nama'></div>
            </div>
        </div>
        <!-- Info Contact Start -->
        <a class='informasi' href='javascript:void' title='Chat Whatsapp'>
            <div class='info-avatar'>
                <img
                    src='https://2.bp.blogspot.com/-y6xNA_8TpFo/XXWzkdYk0MI/AAAAAAAAA5s/RCzTBJ_FbMwVt5AEZKekwQqiDNqdNQJjgCLcBGAs/s70/supportmale.png' />
            </div>
            <div class='info-chat'>
                <span class='chat-label'>Support</span>
                <span class='chat-nama'>For Canada Only</span>
            </div>
            <span class='my-number'>+16473661867</span>
        </a>
        <!-- Info Contact End -->
        <!-- Info Contact Start -->
        <a class='informasi' href='javascript:void' title='Chat Whatsapp'>
            <div class='info-avatar'>
                <img
                    src='https://4.bp.blogspot.com/-X1Xs2iRKabY/XXWzkqQ-iDI/AAAAAAAAA5w/HSyhR0gIXvUzlAx5XgaZzmlrCJkTgrOFQCLcBGAs/s70/supportfemale.png' />
            </div>
            <div class='info-chat'>
                <span class='chat-label'>Support</span>
                <span class='chat-nama'>International</span>
            </div>
            <span class='my-number'>+916369742104</span>
        </a>
        <!-- Info Contact End -->
        <div class='blanter-msg'>Call us to <b> <a href="+916369742104"> +91 6369742104</a></b></div>
    </div>
    <div class='start-chat hide'>
        <div class='first-msg'><span>Hello! What can I do for you?</span></div>
        <div class='blanter-msg'>
            <textarea id='chat-input' placeholder='Write a response' maxlength='120' row='1'></textarea>
            <a href='javascript:void;' id='send-it'>Send</a>
        </div>
    </div>
    <div id='get-number'></div>
    <a class='close-chat' href='javascript:void'>×</a>
</div>
<a class='blantershow-chat'
    href="https://api.whatsapp.com/send/?phone=916369742104&text=Hi%2C%20Need%20Taxi%20%2F%20Cab%20Dispatch%20Software%20System%2C%20connect%20me.&type=phone_number&app_absent=0"
    target="_blank" title='Show Chat'>
    <i class='fab fa-whatsapp'></i>
    <!--How can I help you?-->

</a>

<div id="chatBox" class="flex-column">
    <div id="chatHeader">
        GoRide AI Assistant
        <button id="closeBtn" title="Close">&times;</button>
    </div>

    <div id="chatMessages" class="d-flex flex-column"></div>

    <form id="chatForm">
        <input type="text" id="chatInput" placeholder="Ask GoRide anything..." required />
        <button id="sendBtn" type="submit">Ask</button>
    </form>
</div>

<a class='bot-chat d-none' id="chatToggleBtn" href="#" target="_blank" title='Show Chat'>
    <i class="fas fa-robot"></i>
    <!--How can I help you?-->
</a>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "GoRide",
    "image": "https://www.goride.run/goride/img/logo-dark.png",
    "@id": "https://www.goride.run/",
    "url": "https://www.goride.run/",
    "telephone": "+916369742104",
    "priceRange": "0-9999",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "IN"
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
        ],
        "opens": "00:00",
        "closes": "23:59"
    },
    "sameAs": [
        "https://www.facebook.com/profile.php?id=61564856917550",
        "https://twitter.com/go_rides8499",
        "https://www.instagram.com/goride.run/",
        "https://www.youtube.com/channel/UCK60VSKjbjLDhNlGzDCYDow",
        "https://www.linkedin.com/in/go-rides/",
        "https://www.goride.run/"
    ]
}
</script>

<script>
let countryC = getCookie('countryCode') ?? '';

$(document).ready(function() {

    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const chatBox = document.getElementById('chatBox');
    const closeBtn = document.getElementById('closeBtn');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    const modelQuestions = [
        "What is GoRide?",
        "How much does GoRide cost?",
        "What apps does GoRide provide?",
        "Is GoRide available globally?",
        "How do I sign up for GoRide?"
    ];

    // Show welcome message and suggestions
    function showWelcome() {
        addMessage("🤝 Hi there! I'm GoBot, your GoRide assistant. How can I help you today?", 'bot-msg');

        const suggestionContainer = document.createElement('div');
        modelQuestions.forEach(q => {
            const btn = document.createElement('span');
            btn.className = 'suggestion';
            btn.innerText = q;
            btn.addEventListener('click', () => {
                chatInput.value = q;
                chatForm.dispatchEvent(new Event('submit'));
            });
            suggestionContainer.appendChild(btn);
        });
        chatMessages.appendChild(suggestionContainer);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }


    // Toggle chat open
    chatToggleBtn.addEventListener('click', () => {
        chatBox.style.display = 'flex';
        chatToggleBtn.style.display = 'none';
        chatInput.focus();
        showWelcome();
    });

    // Close chat
    closeBtn.addEventListener('click', () => {
        chatBox.style.display = 'none';
        chatToggleBtn.style.display = 'inline-block';
    });

    // Submit message
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userText = chatInput.value.trim();
        if (!userText) return;

        addMessage(userText, 'user-msg');
        chatInput.value = '';
        addMessage('Typing...', 'bot-msg', true);

        let apiURL = {
            !!json_encode($postMessUrl) !!
        };
        let getToken = getCookie('sessionToken');


        try {
            const res = await fetch(apiURL, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': 'Bearer ' + getToken
                },
                body: 'message=' + encodeURIComponent(userText)
            });
            const botText = await res.text();
            removeTyping();
            addMessage(botText, 'bot-msg');
        } catch {
            removeTyping();
            addMessage('Something went wrong. Try again later.', 'bot-msg');
        }

    });



    if (getCookie('acceptCookie')) {
        document.getElementById('cookiecontent').style.display = 'none';
    } else {
        document.getElementById('cookiecontent').style.display = 'block';
    }


    var words = [
        "TAXI DISPATCH", "CAB DISPATCH", "LIMO DISPATCH",
        "TAXI BUSINESS", "CAB BUSINESS", "LIMO BUSINESS"
    ];
    var currentIndex = 0;
    var $changingWord = $(".changing-word");
    var intervalId;

    function styleWord(text) {
        var parts = text.split(" ");
        if (parts.length === 2) {
            return '<span style="color: #f8be00;">' + parts[0] + '</span> ' +
                '<span style="color: white;">' + parts[1] + '</span>';
        }
        return text;
    }

    function changeWord() {
        $changingWord.fadeOut(500, function() {
            currentIndex = (currentIndex + 1) % words.length;
            $changingWord.html(styleWord(words[currentIndex]));
            $changingWord.fadeIn(500);
        });
    }

    // Initialize first word with styling
    $(document).ready(function() {
        $changingWord.html(styleWord(words[currentIndex]));
        intervalId = setInterval(changeWord, 2000);
    });

    function addMessage(text, className, isTyping = false) {
        const div = document.createElement('div');
        div.className = 'chat-msg ' + className;
        if (isTyping) div.id = 'typing';
        div.innerText = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTyping() {
        const typing = document.getElementById('typing');
        if (typing) typing.remove();
    }

    function startInterval() {
        intervalId = setInterval(changeWord, 2000);
    }

    function stopInterval() {
        clearInterval(intervalId);
    }

    document.addEventListener("visibilitychange", function() {
        if (document.hidden) {
            stopInterval();
        } else {
            startInterval();
        }
    });

    startInterval();
});

var videoModal = $('#videoModal');
var videoIframe = $('#videoIframe');
var closeBtn = $('.close');

// When the user clicks the button, open the modal and play the video
$('#videoButton').click(function() {
    videoModal.show();
    videoIframe.attr('src', 'https://www.youtube.com/embed/1LxcTt1adfY?autoplay=1');
});

// When the user clicks on close button, close the modal
closeBtn.click(function() {
    videoModal.hide();
    videoIframe.attr('src', '');
});

// When the user clicks anywhere outside of the modal, close it
$(window).click(function(event) {
    if ($(event.target).is(videoModal)) {
        videoModal.hide();
        videoIframe.attr('src', '');
    }
});

$('a[href*="#"]').on('click', function(e) {
    e.preventDefault();

    $('html, body').animate({
        scrollTop: $($(this).attr('href')).offset().top
    }, 500, 'linear');
});

/* Whatsapp Chat Widget by www.idblanter.com */
// $(document).on("click", ".blantershow-chat", function () {
//     $("#whatsapp-chat").removeClass("hide").addClass("home-chat");
// });

$(document).on("click", ".informasi", function() {
    var phoneNumber = $(this).find(".my-number").text();
    var message = "Hello, I need assistance.";
    var whatsappURL = "https://wa.me/+" + phoneNumber + "?text=" + encodeURIComponent(message);

    window.open(whatsappURL, '_blank');
});

$(document).on("click", ".close-chat", function() {
    $("#whatsapp-chat").addClass("hide").removeClass("home-chat");
});

if (countryC == 'IN') {
    $('#app_link').attr('href', 'https://play.google.com/store/apps/details?id=com.shi.goride.customer');
} else {
    $('#app_link').attr('href',
        'https://play.google.com/store/apps/details?id=com.shi.my_rider_driver&pcampaignid=web_share');
}

// document.addEventListener('keydown', function (event) {
//     if (event.ctrlKey) {
//         event.preventDefault();
//         // console.log('Ctrl key is disabled!');
//     }
// });
</script>