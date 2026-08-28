/* -----------------------------------------------------------

[ Custom Settings ]

01. ScrollIt
02. Preloader
03. Navbar scrolling background
04. Close navbar-collapse when a clicked
05. Sections background image from data background 
06. Slider-Fade owlCarousel
07. Services owlCarousel
08. Services 2 owlCarousel
09. Team owlCarousel
10. Services 1 owlCarousel
11. Services 2 owlCarousel
12. Car Type 1 owlCarousel
13. Car Type 2 owlCarousel
14. Car Type 3 owlCarousel
15. Car Type 4 owlCarousel
16. Testimonials owlCarousel
17. Cars 1 owlCarousel
18. Cars 2 owlCarousel
19. Cars 3 owlCarousel
20. Blog 1 owlCarousel
21. Blog 2 owlCarousel
22. Clients owlCarousel
23. MagnificPopup (Image, Youtube, Vimeo & Custom PopUp)
24. Accordion
25. Isotope Active Masonry Gallery
26. Animations
27. YouTubePopUp
28. Select2
29. Datepicker
30. Scroll back to top
31. Contact Form
32. Custom Cursor

----------------------------------------------------------- */
$(function () {
"use strict";
var wind = $(window);

wind.on("scroll", function () {

    var bodyScroll = wind.scrollTop(),
        navbar = $(".navbar"),
        logo = $(".navbar .logo> img"),
        bookingBar = $(".fast-booking-bar");

    if (bodyScroll > 100) {
        navbar.addClass("nav-scroll");
        logo.attr('src', '/goride/img/logo-dark.png');
        bookingBar.addClass("move-right");
    } 
    else {
        navbar.removeClass("nav-scroll");
        logo.attr('src', '/goride/img/logo-light.png');
        bookingBar.removeClass("move-right");
    }

});
   
    // Close navbar-collapse when a clicked
    $(".navbar-nav .dropdown-item a").on('click', function () {
        $(".navbar-collapse").removeClass("show");
    });
    
    // Close mobile menu "on click"
    $(function () {
        var navMain = $(".scroll-init");
        navMain.on("click", "a", null, function () {
            navMain.collapse('hide');
        });
    });
    
    // Sections background image from data background
    var pageSection = $(".bg-img, section");
    pageSection.each(function (indx) {
        if ($(this).attr("data-background")) {
            $(this).css("background-image", "url(" + $(this).data("background") + ")");
        }
    });
    
    // Slider  
    $(document).ready(function () {
        var owl = $('.header .owl-carousel');
        // Slider owlCarousel - (Inner Page Slider)
        $('.slider .owl-carousel').owlCarousel({
            items: 1,
            loop: true,
            dots: true,
            margin: 0,
            autoplay: false,
            autoplayTimeout: 5000,
            nav: false,
            navText: ['<i class="ti-angle-left" aria-hidden="true"></i>', '<i class="ti-angle-right" aria-hidden="true"></i>'],
            responsiveClass: true,
            responsive: {
                0: {
                    dots: true,
                },
                600: {
                    dots: true,
                },
                1000: {
                    dots: true,
                }
            }
        });
        $('.slider-fade .owl-carousel').owlCarousel({
            items: 1,
            loop: true,
            dots: true,
            margin: 0,
            autoplay: false,
            autoplayTimeout: 5000,
            animateOut: 'fadeOut',
            nav: false,
            navText: ['<i class="ti-angle-left" aria-hidden="true"></i>', '<i class="ti-angle-right" aria-hidden="true"></i>']
        });
        owl.on('changed.owl.carousel', function (event) {
            var item = event.item.index - 2; // Position of the current item
            $('h6').removeClass('animated fadeInUp');
            $('h1').removeClass('animated fadeInUp');
            $('h2').removeClass('animated fadeInUp');
            $('h5').removeClass('animated fadeInUp');
            $('p').removeClass('animated fadeInUp');
            $('.car-features').removeClass('animated fadeInRight');
            $('.button-1').removeClass('animated fadeInUp');
            $('.button-2').removeClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('h6').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('h1').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('h2').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('h5').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('p').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('.car-features').addClass('animated fadeInRight');
            $('.owl-item').not('.cloned').eq(item).find('.button-1').addClass('animated fadeInUp');
            $('.owl-item').not('.cloned').eq(item).find('.button-2').addClass('animated fadeInUp');
        });
    });
    
    // Team owlCarousel
    $('.team .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 5000,
        dots: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    //  Services 1 owlCarousel 
    $('.services1 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    //  Services 2 owlCarousel 
    $('.services2 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Car Type 1 owlCarousel
    $('.car-types1 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 5000,
        dots: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Car Type 2 owlCarousel
    $('.car-types2 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 5000,
        dots: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 2,
            },
            600: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    });
    
    // Car Type 3 owlCarousel
    $('.car-types3 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 5000,
        dots: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Car Type 4 owlCarousel
    $('.car-types4 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        autoplayTimeout: 5000,
        dots: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Testimonials owlCarousel
    $('.testimonials .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: true,
        autoplayTimeout: 3000,
        dots: false,
        nav: false,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    $('.myClients .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: true,
        autoplayTimeout: 5000,
        dots: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 3
            },
            600: {
                items: 3
            },
            1000: {
                items: 8
            }
        },
        onInitialized: function(event) {
            const stage = $(event.target).find('.owl-stage');
            const stageWidth = stage.width();
            const itemWidth = $(event.target).find('.owl-item').width();
            const itemsCount = stage.children().length;

            // Duplicate items to ensure the infinite scroll effect
            stage.append(stage.html());
            stage.width(stageWidth * 2);
        }
    });
    
    // Cars 1 owlCarousel
    $(".cars1-carousel").owlCarousel({
        loop: true,
        margin: 30,
        autoHeight: false,
        autoplayTimeout: 5000,
        dots: false,
        nav: true,
        navText: ['<i class="ti-angle-left" aria-hidden="true"></i>', '<i class="ti-angle-right" aria-hidden="true"></i>'],
        responsiveClass: true,
        responsive: {
            0: {
                dots: false,
                items: 1,
            },
            600: {
                dots: false,
                items: 1,
            },
            1000: {
                dots: false,
                items: 1,
            }
        }
    });
    
    //  Cars 2 owlCarousel 
    $('.cars2 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    //  Cars 3 owlCarousel 
    $('.cars3 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    //  Blog 1 owlCarousel 
    $('.blog1 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    //  Blog 2 owlCarousel 
    $('.blog2 .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: false,
        dots: true,
        autoplayHoverPause: true,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });
    
    // Clients owlCarousel
    $('.clients .owl-carousel').owlCarousel({
        loop: true,
        margin: 30,
        mouseDrag: true,
        autoplay: true,
        autoplayTimeout: 5000,
        dots: false,
        nav: false,
        navText: ["<span class='lnr ti-angle-left'></span>", "<span class='lnr ti-angle-right'></span>"],
        responsiveClass: true,
        responsive: {
            0: {
                margin: 15,
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 6
            }
        }
    });
    
    // Accordion
    if ($(".accordion-box").length) {
        $(".accordion-box").on("click", ".acc-btn", function () {
            var outerBox = $(this).parents(".accordion-box");
            var target = $(this).parents(".accordion");
            if ($(this).next(".acc-content").is(":visible")) {
                //return false;
                $(this).removeClass("active");
                $(this).next(".acc-content").slideUp(300);
                $(outerBox).children(".accordion").removeClass("active-block");
            } else {
                $(outerBox).find(".accordion .acc-btn").removeClass("active");
                $(this).addClass("active");
                $(outerBox).children(".accordion").removeClass("active-block");
                $(outerBox).find(".accordion").children(".acc-content").slideUp(300);
                target.addClass("active-block");
                $(this).next(".acc-content").slideDown(300);
            }
        });
    }
    
    // Scroll back to top
    var progressPath = document.querySelector('.progress-wrap path');
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    var updateProgress = function () {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var progress = pathLength - (scroll * pathLength / height);
        progressPath.style.strokeDashoffset = progress;
    }
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 150;
    var duration = 550;
    jQuery(window).on('scroll', function () {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.progress-wrap').addClass('active-progress');
        } else {
            jQuery('.progress-wrap').removeClass('active-progress');
        }
    });
    jQuery('.progress-wrap').on('click', function (event) {
        event.preventDefault();
        jQuery('html, body').animate({
            scrollTop: 0
        }, duration);
        return false;
    })
    
    // Contact Form
    var form = $('.contact__form'),
        message = $('.contact__msg'),
        form_data;
    // success function
    function done_func(response) {
        message.fadeIn().removeClass('alert-danger').addClass('alert-success');
        message.text(response);
        setTimeout(function () {
            message.fadeOut();
        }, 2000);
        form.find('input:not([type="submit"]), textarea').val('');
    }
    // fail function
    function fail_func(data) {
        message.fadeIn().removeClass('alert-success').addClass('alert-success');
        message.text(data.responseText);
        setTimeout(function () {
            message.fadeOut();
        }, 2000);
    }
    form.submit(function (e) {
        e.preventDefault();
        form_data = $(this).serialize();
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form_data
        }).done(done_func).fail(fail_func);
    });
});

   // Slider Grid Background
    (function () {
            var imageElements = document.querySelectorAll('.grid-img');
            var itemElements = document.querySelectorAll('.grid-con');
            if (itemElements.length) {
              itemElements.forEach(function (item, index) {
                item.addEventListener('mouseenter', function () {
                  imageElements.forEach(function (image) {
                    image.classList.remove('grid-img-active');
                  });
                  itemElements.forEach(function (card) {
                    card.classList.remove('grid-con-active');
                  });
                  item.classList.add('grid-con-active');
                  imageElements[index].classList.add('grid-img-active');
                });
              });
            }
          })();
          
          
          
    // Customr Cursor
    
//   const cursor = document.querySelector('.custom-cursor');
//   let mouseX = 0, mouseY = 0;
//   let cursorX = 0, cursorY = 0;
//   const speed = 0.3; // Adjust speed for smoother or faster movement

//   function animateCursor() {
//     cursorX += (mouseX - cursorX) * speed;
//     cursorY += (mouseY - cursorY) * speed;
//     cursor.style.left = `${cursorX}px`;
//     cursor.style.top = `${cursorY}px`;
//     requestAnimationFrame(animateCursor);
//   }

//   function updateMousePosition(e) {
//     mouseX = e.pageX;
//     mouseY = e.pageY;
//     cursor.style.opacity = 0.5;
//   }

//   document.addEventListener('mousemove', updateMousePosition);
//   document.addEventListener('mouseleave', function() {
//     cursor.style.opacity = 0;
//   });
//   document.addEventListener('mouseenter', function() {
//     cursor.style.opacity = 0.5;
//   });
//   window.addEventListener('load', function() {
//     cursor.style.opacity = 0;
//   });

  // Start the animation loop
//   animateCursor();
  
    // const signUpButton = document.getElementById('signUp');
    // const signInButton = document.getElementById('signIn');
    // const container = document.getElementById('container');
    
    // signUpButton.addEventListener('click', () => {
    // 	container.classList.add("right-panel-active");
    // });
    
    // signInButton.addEventListener('click', () => {
    // 	container.classList.remove("right-panel-active");
    // });
    

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))