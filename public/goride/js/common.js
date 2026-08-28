// Set the Cookie 
const setCookie = (name, value, daysToExpire = 1) => {
  try {
    const date = new Date();
    date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    const secureFlag = location.protocol === 'https:' ? '; secure' : '';
    document.cookie = `${name}=${value}; ${expires}; path=/${secureFlag}`;
  } catch (e) {
    console.log('Error: ' + e.message);
  }
}

// Get the value of a cookie by name
const getCookie = (name) => {
  try {
    const cookieName = `${name}=`;
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
      let cookie = cookies[i];
      while (cookie.charAt(0) === ' ') {
        cookie = cookie.substring(1);
      }
      if (cookie.indexOf(cookieName) === 0) {
        return cookie.substring(cookieName.length, cookie.length);
      }
    }
    return null;
  } catch (e) {
    console.log('Error: ' + e.message);
  }
}



// Delete a cookie by name

const deleteCookie = (name) => {
  try {
    setCookie(name, '', -1); // Setting an expired date deletes the cookie
  } catch (e) {
    console.log('Error: ' + e.message);
  }
}



// Return the initialized instance for further use

const inttelInput = (input) => {

  try {

    return window.intlTelInput(input, {

      separateDialCode: true,

      preferredCountries: ["ae", "in", "sa", "qa", "om", "bh", "kw", "ma"],

      excludeCountries: ["AF", "CU", "KP", "IR", "LR", "LY", "MM", "SY", "UA"],

      initialCountry: "auto",

      showSelectedDialCode: true,



      geoIpLookup: function (callback) {

        fetch("https://api.country.is/")

          .then(function (res) { return res.json(); })

          .then(function (data) { callback(((data && data.country) ? data.country : "AE")); })

          .catch(function () { callback(); });

      },

      customPlaceholder: function () {

        return ""

      },

      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.5.3/js/utils.js"

    });

  } catch (e) {

    console.log('Error: ' + e.message);

    return false;

  }

}



const getCountryCodeAndNumber = (Mobile) => {

  try {

    var countryCode = Mobile.getSelectedCountryData().dialCode;

    var number = Mobile.getNumber().replace('+' + countryCode, '');

    return countryCode + number;

  } catch (e) {

    console.log('Error: ' + e.message);

  }

}





const showToast = (icon, title, duration = 5000, onTimerEnd) => {
  try {
    const Toast = Swal.mixin({

      toast: true,

      position: "top-end",

      showConfirmButton: false,

      timer: duration,

      timerProgressBar: false,

      didOpen: (toast) => {

        toast.onmouseenter = Swal.stopTimer;

        toast.onmouseleave = Swal.resumeTimer;

      },

      didClose: () => {

        if (typeof onTimerEnd === 'function') {

          onTimerEnd();

        }

      }

    });



    Toast.fire({

      icon: icon,

      title: title

    });
  } catch (e) {

    console.log('Error: ' + e.message);

  }
}


function detectDevice() {
  try {
    var userAgent = navigator.userAgent;

    // Check if the userAgent contains 'Android' or 'iOS' to detect the device type
    var isAndroid = /Android/.test(userAgent);
    var isiOS = /iPhone|iPad|iPod/.test(userAgent);

    // Check if it's a WebView
    var isWebView = /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(userAgent) ||
      /(Android).*Version\/[\d.]+/.test(userAgent);

    // Detect browser
    var isChrome = /Chrome/.test(userAgent);
    var isFirefox = /Firefox/.test(userAgent);
    var isSafari = /Safari/.test(userAgent) && !isChrome;

    // Return the detected information
    return {
      isAndroid: isAndroid,
      isiOS: isiOS,
      isWebView: isWebView,
      isChrome: isChrome,
      isFirefox: isFirefox,
      isSafari: isSafari
    };

  } catch (e) {

    console.log('Error: ' + e.message);

  }
}