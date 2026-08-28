(function($){
	"use strict";
    
    // Header Sticky
    $(window).on('scroll',function() {
        if ($(this).scrollTop() > 120){  
            $('.navbar-area').addClass("is-sticky");
        }
        else{
            $('.navbar-area').removeClass("is-sticky");
        }
    });

    // Header Sticky
    $(window).on('scroll',function() {
        if ($(this).scrollTop() > 120){  
            $('.navbar-area').addClass("is-sticky");
        }
        else{
            $('.navbar-area').removeClass("is-sticky");
        }
    });

    // Sidebar Modal
    $(".burger-menu").on('click',  function() {
        $('.sidebar-modal').toggleClass('active');
    });
    $(".sidebar-modal-close-btn").on('click',  function() {
        $('.sidebar-modal').removeClass('active');
    });
    
    // Search Popup JS
    $('.close-btn').on('click',function() {
        $('.search-overlay').fadeOut();
        $('.search-btn').show();
        $('.close-btn').removeClass('active');
    });
    $('.search-btn').on('click',function() {
        $(this).hide();
        $('.search-overlay').fadeIn();
        $('.close-btn').addClass('active');
    });
    
    // Mean Menu
    jQuery('.mean-menu').meanmenu({
        meanScreenWidth: "991"
    });

    // Popup Video
    $('.popup-youtube').magnificPopup({
        disableOn: 320,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });

    // Odometer JS
    $('.odometer').appear(function(e) {
        var odo = $(".odometer");
        odo.each(function() {
            var countNumber = $(this).attr("data-count");
            $(this).html(countNumber);
        });
    });

    // Tabs
    (function ($) {
        $('.tab ul.tabs').addClass('active').find('> li:eq(0)').addClass('current');
        $('.tab ul.tabs li a').on('click', function (g) {
            var tab = $(this).closest('.tab'), 
            index = $(this).closest('li').index();
            tab.find('ul.tabs > li').removeClass('current');
            $(this).closest('li').addClass('current');
            tab.find('.tab_content').find('div.tabs_item').not('div.tabs_item:eq(' + index + ')').slideUp();
            tab.find('.tab_content').find('div.tabs_item:eq(' + index + ')').slideDown();
            g.preventDefault();
        });
    })(jQuery);
    
    // MixItUp JS
    try {
        var mixer = mixitup('#Container', {
            controls: {
                toggleDefault: 'none'
            }
        });
    } catch (err) {}

    // Testimonial-slides
    $('.testimonial-slides').owlCarousel({
        loop: true,
        nav: true,
        dots: true,
        smartSpeed: 2000,
        margin: 30,
        active: true,
        autoplayHoverPause: true,
        autoplay: true,
        navText: [
            "<i class='flaticon-left-arrow-1'></i>",
            "<i class='flaticon-right-arrow-1'></i>"
        ],
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            1200: {
                items: 2
            }
        }
    });

    // Partner-slider
    $('.partner-slider').owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        smartSpeed: 2000,
        margin: 30,
        autoplayHoverPause: true,
        autoplay: false,

        responsive: {
            0: {
                items: 2
            },
            576: {
                items: 3
            },
            768: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });

    // Go to Top
    $(function(){
        // Scroll Event
        $(window).on('scroll', function(){
            var scrolled = $(window).scrollTop();
            if (scrolled > 600) $('.go-top').addClass('active');
            if (scrolled < 600) $('.go-top').removeClass('active');
        });  
        // Click Event
        $('.go-top').on('click', function() {
            $("html, body").animate({ scrollTop: "0" },  0);
        });
    });
    
    // Pricing Tabs
    (function ($) {
        $('.tab ul.tabs').addClass('active').find('> li:eq(0)').addClass('current');
        $('.tab ul.tabs li a').on('click', function (g) {
            var tab = $(this).closest('.tab'), 
            index = $(this).closest('li').index();
            tab.find('ul.tabs > li').removeClass('current');
            $(this).closest('li').addClass('current');
            tab.find('.tab-content').find('div.tabs-item').not('div.tabs-item:eq(' + index + ')').slideUp();
            tab.find('.tab-content').find('div.tabs-item:eq(' + index + ')').slideDown();
            g.preventDefault();
        });
    })(jQuery);

    // Client-slides
    $('.client-slides').owlCarousel({
        loop: true,
        nav: true,
        dots: true,
        smartSpeed: 2000,
        margin: 30,
        autoplayHoverPause: true,
        autoplay: true,
        navText: [
            "<i class='flaticon-left-arrow-1'></i>",
            "<i class='flaticon-right-arrow-1'></i>"
        ],
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            1200: {
                items: 1
            }
        }
    });

    // FAQ Accordion
    $(function() {
        $('.accordion').find('.accordion-title').on('click', function() {
            // Adds Active Class
            $(this).toggleClass('active');
            // Expand or Collapse This Panel
            $(this).next().slideToggle('fast');
            // Hide The Other Panels
            $('.accordion-content').not($(this).next()).slideUp('fast');
            // Removes Active Class From Other Titles
            $('.accordion-title').not($(this)).removeClass('active');
        });
    });

    // Nice Select JS
    $('select').niceSelect();
    
    // Input Plus & Minus Number JS
    $('.input-counter').each(function() {
        var spinner = jQuery(this),
        input = spinner.find('input[type="text"]'),
        btnUp = spinner.find('.plus-btn'),
        btnDown = spinner.find('.minus-btn'),
        min = input.attr('min'),
        max = input.attr('max');
        
        btnUp.on('click', function() {
            var oldValue = parseFloat(input.val());
            if (oldValue >= max) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue + 1;
            }
            spinner.find("input").val(newVal);
            spinner.find("input").trigger("change");
        });
        btnDown.on('click', function() {
            var oldValue = parseFloat(input.val());
            if (oldValue <= min) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue - 1;
            }
            spinner.find("input").val(newVal);
            spinner.find("input").trigger("change");
        });
    });
    
    // Count Time 
    function makeTimer() {
        var endTime = new Date("September 30, 2025 17:00:00 PDT");
        var endTime = (Date.parse(endTime)) / 1000;
        var now = new Date();
        var now = (Date.parse(now) / 1000);
        var timeLeft = endTime - now;
        var days = Math.floor(timeLeft / 86400); 
        var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
        var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600 )) / 60);
        var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));
        if (hours < "10") { hours = "0" + hours; }
        if (minutes < "10") { minutes = "0" + minutes; }
        if (seconds < "10") { seconds = "0" + seconds; }
        $("#days").html(days + "<span>Days</span>");
        $("#hours").html(hours + "<span>Hours</span>");
        $("#minutes").html(minutes + "<span>Minutes</span>");
        $("#seconds").html(seconds + "<span>Seconds</span>");
    }
    setInterval(function() { makeTimer(); }, 1000);

    // WOW JS
    $(window).on ('load', function (){
        if ($(".wow").length) { 
            var wow = new WOW({
            boxClass:     'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset:       20,          // distance to the element when triggering the animation (default is 0)
            mobile:       true,       // trigger animations on mobile devices (default is true)
            live:         true,       // act on asynchronously loaded content (default is true)
          });
          wow.init();
        }
    });
	
	// Subscribe form
	$(".newsletter-form").validator().on("submit", function (event) {
		if (event.isDefaultPrevented()) {
		// handle the invalid form...
			formErrorSub();
			submitMSGSub(false, "Please enter your email correctly.");
		} else {
			// everything looks good!
			event.preventDefault();
		}
	});
	function callbackFunction (resp) {
		if (resp.result === "success") {
			formSuccessSub();
		}
		else {
			formErrorSub();
		}
	}
	function formSuccessSub(){
		$(".newsletter-form")[0].reset();
		submitMSGSub(true, "Thank you for subscribing!");
		setTimeout(function() {
			$("#validator-newsletter").addClass('hide');
		}, 4000)
	}
	function formErrorSub(){
		$(".newsletter-form").addClass("animated shake");
		setTimeout(function() {
			$(".newsletter-form").removeClass("animated shake");
		}, 1000)
	}
	function submitMSGSub(valid, msg){
		if(valid){
			var msgClasses = "validation-success";
		} else {
			var msgClasses = "validation-danger";
		}
		$("#validator-newsletter").removeClass().addClass(msgClasses).text(msg);
	}
	
	// AJAX MailChimp
	$(".newsletter-form").ajaxChimp({
		url: "https://envytheme.us20.list-manage.com/subscribe/post?u=60e1ffe2e8a68ce1204cd39a5&amp;id=42d6d188d9", // Your url MailChimp
		callback: callbackFunction
	});
    
    // Preloader
	jQuery(window).on('load', function() {
		$('.preloader').fadeOut();
    });
    
    // Home Slides
    $('.home-slides').owlCarousel({
        loop: true,
        nav: true,
        dots: false,
        autoplayHoverPause: true,
        items: 1,
        smartSpeed: 750,
        autoplay: true,
        navText: [
            "<i class='fa fa-chevron-left'></i>",
            "<i class='fa fa-chevron-right'></i>"
        ],
    });

    // Projects-slider
    $('.projects-slider').owlCarousel({
        loop: true,
        nav: false,
        dots: false,
        smartSpeed: 2000,
        margin: 30,
        autoplayHoverPause: true,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            1024: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });

    // Buy Now Btn
	$('body').append("<a href='https://1.envato.market/ao4Nq' target='_blank' class='buy-now-btn'>Buy Now</a>");

	// Switch Btn
	$('body').append("<div class='switch-box'><label id='switch' class='switch'><input type='checkbox' onchange='toggleTheme()' id='slider'><span class='slider round'></span></label></div>");
    
}(jQuery));

// function to set a given theme/color-scheme
function setTheme(themeName) {
    localStorage.setItem('crimso_theme', themeName);
    document.documentElement.className = themeName;
}

// function to toggle between light and dark theme
function toggleTheme() {
    if (localStorage.getItem('crimso_theme') === 'theme-dark') {
        setTheme('theme-light');
    } else {
        setTheme('theme-dark');
    }
}

// Immediately invoked function to set the theme on initial load
(function () {
    if (localStorage.getItem('crimso_theme') === 'theme-dark') {
        setTheme('theme-dark');
        document.getElementById('slider').checked = false;
    } else {
        setTheme('theme-light');
      document.getElementById('slider').checked = true;
    }
})();