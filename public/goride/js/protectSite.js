function ctrlShiftKey(e, t) {
    try {
        // Check if Ctrl, Shift, and the specified key are pressed simultaneously
        return e.ctrlKey && e.shiftKey && e.keyCode === t.charCodeAt(0);
    } catch (e) {
        console.log('Error: ' + e.message);
    }
}


document.addEventListener("contextmenu", e => e.preventDefault());
document.onkeydown = e => {
    if (123 === e.keyCode || (ctrlShiftKey(e, "I") || ctrlShiftKey(e, "J") || ctrlShiftKey(e, "C") || ctrlShiftKey(e, "S")) || (e.ctrlKey && 85 === e.keyCode) || (e.ctrlKey && 83 === e.keyCode)) {
        return false;
    }
};


function checkDevTools() {
    try {
        // Check for changes in window dimensions
        var widthThreshold = window.outerWidth - window.innerWidth > 160; // Arbitrary threshold


        console.log('Check DevTools');


        if (console.clear) {
            console.clear();
        }


        if (console.timeline) {
            console.timeline();
            console.timelineEnd();

            // If the timeline event is not empty, developer tools may be open
            widthThreshold = widthThreshold || console.timelineEnd.toString().length > 0;
        }

        if (widthThreshold) {
            // $("body").css("filter", "blur(5px)");
            console.log('Developer tools detected. Please close them to continue.');
        } else {
            // $("body").css("filter", "none");
        }

    } catch (e) {
        console.log('Error: ' + e.message);
    }
}

// Attach event listener to the resize event
window.addEventListener('resize', checkDevTools);

const isWebView = () => {
    try {
        var userAgent = navigator.userAgent.toLowerCase();
        return (userAgent.indexOf('wv') > -1) || // Android WebView
            (userAgent.indexOf('ios') > -1 &&
                userAgent.indexOf('applewebkit') > -1 &&
                !/safari/.test(userAgent)); // iOS WebView
    } catch (e) {
        console.log('Error: ' + e.message);
    }
}

$(document).ready(() => {
    if (detectDevice().isWebView) {
        // This is a WebView 
        $(`#signDesktopBtn,#loginDesktopBtn,#signMobileBtn,#loginMobileBtn`).addClass('d-none');
    }
    // document.write(JSON.stringify(detectDevice()));
    if (detectDevice().isWebView) {
        if (detectDevice().isiOS) {
            $(`#logoutBtn`).addClass('d-none');
        }
    }
});