<?php 


 function redirectToPage($url)
    {
        echo '<script>window.location.href = "' . $url . '";</script>';
    }