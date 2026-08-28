
(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Scrolls to an element with header offset
   */
  const scrollto = (el) => {
    let header = select('#header')
    let offset = header.offsetHeight

    if (!header.classList.contains('header-scrolled')) {
      offset -= 16
    }

    let elementPos = select(el).offsetTop
    window.scrollTo({
      top: elementPos - offset,
      behavior: 'smooth'
    })
  }

  /**
   * Header fixed top on scroll
   */
  // let selectHeader = select('#header')
  // if (selectHeader) {
  //   let headerOffset = selectHeader.offsetTop
  //   let nextElement = selectHeader.nextElementSibling
  //   const headerFixed = () => {
  //     if ((headerOffset - window.scrollY) <= 0) {
  //       selectHeader.classList.add('fixed-top')
  //       nextElement.classList.add('scrolled-offset')
  //     } else {
  //       selectHeader.classList.remove('fixed-top')
  //       nextElement.classList.remove('scrolled-offset')
  //     }
  //   }
  //   window.addEventListener('load', headerFixed)
  //   onscroll(document, headerFixed)
  // }
  
  
  // Get the header element
// let header = document.querySelector('#header');

// // Check if the header element exists
// if (header) {
//   // Get the offset position of the header
//   let headerOffset = header.offsetTop;

//   // Get the next element after the header
//   let nextElement = header.nextElementSibling;

//   // Function to add or remove the fixed-top class based on scroll position
//   const headerFixed = () => {
//     if (window.pageYOffset > headerOffset) {
//       header.classList.add('fixed-top', 'header-animation');
//       nextElement.classList.add('scrolled-offset');
//     } else {
//       header.classList.remove('fixed-top', 'header-animation');
//       nextElement.classList.remove('scrolled-offset');
//     }
//   };

//   // Add event listeners for load and scroll events
//   window.addEventListener('load', headerFixed);
//   window.addEventListener('scroll', headerFixed);
// }


let selectHeader = document.querySelector('#header');
if (selectHeader) {
  const headerFixed = () => {
    if (window.scrollY > 0) {
      selectHeader.classList.add('fixed-top', 'header-animation');
    } else {
      selectHeader.classList.remove('fixed-top', 'header-animation');
    }
  };
  window.addEventListener('load', headerFixed);
  window.addEventListener('scroll', headerFixed);
}


  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Mobile nav toggle
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  /**
   * Mobile nav dropdowns activate
   */
  on('click', '.navbar .dropdown > a', function(e) {
    if (select('#navbar').classList.contains('navbar-mobile')) {
      e.preventDefault()
      this.nextElementSibling.classList.toggle('dropdown-active')
    }
  }, true)

  /**
   * Scrool with ofset on links with a class name .scrollto
   */
  on('click', '.scrollto', function(e) {
    if (select(this.hash)) {
      e.preventDefault()

      let navbar = select('#navbar')
      if (navbar.classList.contains('navbar-mobile')) {
        navbar.classList.remove('navbar-mobile')
        let navbarToggle = select('.mobile-nav-toggle')
        navbarToggle.classList.toggle('bi-list')
        navbarToggle.classList.toggle('bi-x')
      }
      scrollto(this.hash)
    }
  }, true)

  /**
   * Scroll with ofset on page load with hash links in the url
   */
  window.addEventListener('load', () => {
    if (window.location.hash) {
      if (select(window.location.hash)) {
        scrollto(window.location.hash)
      }
    }
  });

  /**

   * Animation on scroll
   */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    })
  });

  /**
   * Initiate Pure Counter 
   */
  // new PureCounter();


   $("#our-winners").owlCarousel({
    items: 4,
    itemsDesktop: [1199, 4],
    itemsDesktopSmall: [980, 2],
    itemsMobile: [600, 1.2],
    navigation: true,
    navigationText: ['<i class="bi bi-caret-left-fill"></i>', '<i class="bi bi-caret-right-fill"></i>'], // Fix side arrow text
    pagination: true,
    dots: true, // Add dot indicators
    autoPlay: true
  });

  if ($(window).width() < 992) { // Check if screen size is less than tablet width
    var clockSection = $(".our-product .col-xl-12");
    clockSection.appendTo($(".our-product")); // Move clock section outside of the slider
  }

  $("#product-slider").owlCarousel({
    items: 4,
    itemsDesktop: [1199, 4],
    itemsDesktopSmall: [980, 2],
    itemsMobile: [600, 1],
    navigation: true,
    pagination: true,
    autoPlay: true
  });

})();









