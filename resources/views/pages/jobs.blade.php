@extends('layouts.app')

@section('css')
<style>

    .bot-chat {
        display: none;
    }
.calendly-badge-widget{
    display:none;
}
  #profile-bar-box .dropdown-item:hover {
        background-color: #f9bf00;
        color: #000;
    }

    #profile-bar-box .dropdown-item i {
        margin-right: 8px;
    }


    #profile-bar img {
        width: 80px;
        height: 80px;
        object-fit: cover;
    }

    .dropdown-item.active-selected {
        background-color: #f8be00 !important;
        color: #000 !important;
    }

/* Toggle switch wrapper */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
}

/* Hide default checkbox */
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

/* Slider */
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

/* Circle */
.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

/* Checked state */
input:checked + .slider {
    background-color: #f4ba00;
}

input:checked + .slider:before {
    transform: translateX(22px);
}




    #profile-bar-box{
        left: -41px;
    top: 65px;

    }
     
.calendly-badge-widget:before {
    content: "\f590";
    font-family: "Font Awesome 6 Pro"; 
    font-weight: 700; 
        position: absolute;
    top: 13px;
    color: white;
    left: 7px !important;
    display: inline-block;
    margin-right: 8px; 
}
 .calendly-badge-widget {
    right: unset;
    left: 20px;
  
}
    .pricing-section {
        padding: 10px 0;
    }

    a.thm-btn.borderd {
        cursor: pointer;
    }

    .sec-title h2 {
        font-size: 32px;
        color: #170B35;
        font-weight: 600;
    }

    .sec-title p {
        font-size: 20px;
        line-height: 26px;
        color: #656565;
        margin-top: 20px;
    }

    .sec-title {
        margin-bottom: 100px;
    }

    .pricing-section ul.switch-toggler-list {
        margin-bottom: 18px;
    }

    .list-inline li {
        display: inline-block;
    }

    .pricing-section ul.switch-toggler-list li.active a {
        color: #000;
    }

    .pricing-section ul.switch-toggler-list li a {
        font-size: 18px;
        font-weight: 600;
        color: #989898;
        padding-left: 10px;
        padding-right: 10px;
        display: block;
    }

    .pricing-section .switch,
    .pricing-section .switch1 {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        vertical-align: middle;
    }

    .pricing-section .switch.on .slider,
    .pricing-section .switch1.on .slider {
        background: #d43396;
        background: -webkit-gradient(left top, right top, color-stop(0%, #d43396), color-stop(100%, #6541c1));
        background: -webkit-gradient(linear, left top, right top, from(#d43396), to(#6541c1));
        background: linear-gradient(to right, #d43396 0%, #6541c1 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#d43396', endColorstr='#6541c1', GradientType=1);
    }

    .pricing-section .slider.round {
        border-radius: 34px;
    }

    .pricing-section .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .slider.round:before {
        border-radius: 50%;
    }

    .pricing-section .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .pricing-section .tabed-content #year {
        display: none;
    }

    .pricing-section .pricing-row {
        padding-top: 20px;
    }

    .pricing-section .single-pricing {
        position: relative;
        background: #E8E6E6;
        border-radius: 15px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing .inner {
        position: relative;
        padding-bottom: 45px;
        padding-top: 45px;
    }

    .pricing-section .single-pricing h3.title {
        font-size: 24px;
        color: #170B35;
        font-weight: 600;
    }

    .plan-tag p span {
        font-size: 12px;
        color: #000;
        font-weight: 700;
        display: inline-block;
        width: 70%;
        border-top: 2px solid #000;
        line-height: 20px;
        margin: 0;
    }

    .plan-tag {
        width: 85px;
        height: 95px;
        position: absolute;
        right: 16px;
        top: -12px;
        background-image: url(/goride/img/Tag1.png);
        background-repeat: no-repeat;
        padding: 0;
        box-sizing: border-box;
        text-align: center;
        background-size: contain;
    }

    .plan-tag p.anu {
        font-size: 17px;
        line-height: 20px;
        position: absolute;
        top: 10px;
        right: -8px;
    }

    .modal {
        padding: 0;
    }

    .pricing-section .single-pricing h3,
    .pricing-section .single-pricing p,
    .pricing-section .single-pricing ul,
    .pricing-section .single-pricing li {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pricing-section .single-pricing p.price {
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 53px;
        font-weight: 200;
        line-height: 1em;
        margin-bottom: 20px;
        margin-top: 20px;
    }

    .pricing-section .single-pricing p.price-label {
        font-size: 18px;
        font-weight: 600;
        color: #656565;
    }

    .pricing-section .single-pricing ul.list-item {
        margin-top: 45px;
    }

    .pricing-section .single-pricing ul.list-item li {
        font-size: 14px;
        color: #170B35;
        font-weight: 500;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-check {
        color: #12CE32;
    }

    .pricing-section .single-pricing ul.list-item li i {
        vertical-align: middle;
        margin-right: 5px;
    }

    .pricing-section .single-pricing ul.list-item li i.fa-times {
        color: #FF0302;
    }

    .pricing-section .single-pricing a.thm-btn {
        padding: 10px 30px;
        margin-top: 20px;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .thm-btn>span {
        position: relative;
    }

    .pricing-section .single-pricing.popular {
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        margin-top: -20px;
    }

    .pricing-section .single-pricing:before {
        content: '';
        background: #fff;
        position: absolute;
        top: 4px;
        left: 4px;
        right: 4px;
        bottom: 4px;
        border-radius: 15px;
    }

    .pricing-section .single-pricing.popular .inner {
        padding-top: 65px;
        padding-bottom: 65px;
    }

    .pricing-section .single-pricing.popular .thm-btn {
        color: #fff;
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .thm-btn {
        display: inline-block;
        border: none;
        outline: none;
        background: #6541c1;
        background: -webkit-gradient(left top, right top, color-stop(0%, #6541c1), color-stop(98%, #d43396), color-stop(100%, #d43396));
        background: -webkit-gradient(linear, left top, right top, from(#6541c1), color-stop(98%, #d43396), to(#d43396));
        background: linear-gradient(to right, #6541c1 0%, #d43396 98%, #d43396 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#6541c1', endColorstr='#d43396', GradientType=1);
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 600;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
        border-radius: 28px;
        padding: 15px 29px;
        position: relative;
    }

    .pricing-section .single-pricing.popular .thm-btn:before {
        opacity: 0;
    }

    .thm-btn.borderd:before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        bottom: 2px;
        border-radius: 28px;
        background: #fff;
        opacity: 1;
        -webkit-transition: all .4s ease;
        transition: all .4s ease;
    }

    .pricing-section .switch.off .slider:before,
    .pricing-section .switch1.off .slider:before {
        -webkit-transform: translateX(26px);
        transform: translateX(26px);
    }

    .thm-btn.borderd {
        color: #190A32;
    }

    .pricing-section .single-pricing a.thm-btn:hover {
        -webkit-box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
        box-shadow: 0px 15px 30px rgba(212, 50, 151, 0.27);
    }

    .thm-btn.borderd:hover:before {
        opacity: 0;
    }

    .thm-btn.borderd:hover {
        color: #fff;
    }

    @media (max-width: 736px) {
        .pricing-section .single-pricing.popular {
            top: 0;
            margin-top: 50px;
        }

        .pricing-section .single-pricing {
            max-width: 370px;
            margin-top: 50px;
            margin-left: auto;
            margin-right: auto;
        }

        .pricing-section ul.switch-toggler-list {
            margin-bottom: 0;
        }
    }

    ::selection {
        background-color: #f8be00;
        color: #ffffff;
    }

    .blantershow-chat,
    footer,
    .navbar.navbar-expand-lg {
        display: none;
    }

    /*.login_dashboard_section {*/
    /*    margin-top: 100px;*/
    /*}*/
    #main-wrapper {
        min-height: 100vh;
    }

    .brand-logo {
        min-height: 70px;
        padding: 0 24px;
    }

    .left-sidebar {
        width: 270px;
        border-right: 1px solid #dee2e6;
        flex-shrink: 0;
        background: #fff;
        z-index: 99;
        transition: .2s ease-in;
        position: fixed;
        left: 0;
        right: 0;
        height: 100%;
    }

    .with-vertical {
        display: block;
    }

    .left-sidebar .scroll-sidebar {
        /*overflow-y: auto;*/
        padding: 0 24px;
        height: calc(100vh - 150px);
        border-radius: 7px;
    }

    [data-simplebar] {
        position: relative;
        flex-direction: column;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-content: flex-start;
        align-items: flex-start;
    }

    .simplebar-mask {
        direction: inherit;
        position: absolute;
        overflow: hidden;
        padding: 0;
        margin: 0;
        left: 0;
        top: 0;
        bottom: 0;
        right: 0;
        width: auto !important;
        height: auto !important;
        z-index: 0;
    }

    .simplebar-offset {
        direction: inherit !important;
        box-sizing: inherit !important;
        resize: none !important;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 0;
        margin: 0;
        -webkit-overflow-scrolling: touch;
    }

    .simplebar-content-wrapper {
        direction: inherit;
        box-sizing: border-box !important;
        position: relative;
        display: block;
        height: 100%;
        width: auto;
        max-width: 100%;
        max-height: 100%;
        overflow: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .nav-small-cap {
        margin-top: 24px;
        color: #f8be00;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 0;
        line-height: 26px;
        text-transform: uppercase;
    }

    .nav-small-cap .nav-small-cap-icon {
        display: none;
    }

    .sidebar-nav ul .sidebar-item.selected>.sidebar-link,
    .sidebar-nav ul .sidebar-item.selected>.sidebar-link.active,
    .sidebar-nav ul .sidebar-item>.sidebar-link.active {
        background-color: #f8be00;
        color: #fff;
    }

    .sidebar-nav ul .sidebar-item .sidebar-link:hover {
        background: #fff;
        color: #f8be00;
    }

    .sidebar-nav ul .sidebar-item .sidebar-link {
        display: flex;
        font-size: 14px;
        white-space: nowrap;
        align-items: center;
        line-height: 25px;
        position: relative;
        margin: 0 0 2px;
        padding: 10px;
        border-radius: 7px;
        gap: 15px;
        text-decoration: none;
        font-weight: 400;
    }

    .sidebar-nav ul .sidebar-item .sidebar-link span:first-child {
        display: flex;
    }

    .sidebar-nav ul .sidebar-item .sidebar-link {
        display: flex;
        font-size: 14px;
        white-space: nowrap;
        align-items: center;
        line-height: 25px;
        position: relative;
        margin: 0 0 2px;
        padding: 10px;
        border-radius: 7px;
        gap: 15px;
        text-decoration: none;
        font-weight: 400;
    }

    #sidebarnav {
        padding: 0;
        list-style: none;
    }

    @media (min-width: 1300px) {
        .page-wrapper {
            margin-left: 270px;
            padding: 30px;
        }
    }

    .rounded {
        width:80px;
        margin-top:-13px;
        border-radius: 7px !important;
    }

    /*.bg-secondary-subtle {*/
    /*    background: linear-gradient(to right, rgb(249 189 26) 0, #fff79b 100%);*/
    /*}*/

    .hstack {
        display: flex;
        flex-direction: row;
        align-items: center;
        align-self: stretch;
    }

    .text-primary {
        --bs-text-opacity: 1;
        color: rgba(93, 135, 255, var(--bs-text-opacity)) !important;
    }

    .fs-2 {
        font-size: 1.5rem !important;
    }

    /* Redesign */
    .cs_btn.cs_style_2 {
        padding: 10px 15px;
    }

    .myNewCard {
        --bs-card-spacer-y: 30px;
        --bs-card-spacer-x: 30px;
        --bs-card-title-spacer-y: 0.5rem;
        --bs-card-title-color: #2a3547;
        --bs-card-subtitle-color: var(--bs-body-color);
        --bs-card-border-width: 0px;
        --bs-card-border-color: #ebf1f6;
        --bs-card-border-radius: 7px;
        --bs-card-box-shadow: rgba(145, 158, 171, 0.2) 0px 0px 2px 0px, rgba(145, 158, 171, 0.12) 0px 12px 24px -4px;
        --bs-card-inner-border-radius: 7px;
        --bs-card-cap-padding-y: 15px;
        --bs-card-cap-padding-x: 30px;
        --bs-card-cap-bg: rgba(var(--bs-body-color-rgb), 0.03);
        --bs-card-bg: var(--bs-body-bg);
        --bs-card-img-overlay-padding: 1rem;
        --bs-card-group-margin: 12px;
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        height: var(--bs-card-height);
        color: var(--bs-body-color);
        word-wrap: break-word;
        background-color: var(--bs-card-bg);
        background-clip: border-box;
        border: var(--bs-card-border-width) solid var(--bs-card-border-color);
        border-radius: var(--bs-card-border-radius);
        box-shadow: var(--bs-card-box-shadow);
    }

    .card-title {
        font-size: 24px;
        margin-bottom: 8px;
        color: var(--bs-card-title-color);
    }

    .card-subtitle {
        color: #2a3547;
    }

    .card-body .fs-2 {
        font-size: .75rem !important;
    }

    .text-dark {
        color: rgba(42, 53, 71, 1) !important;
    }

    .btn-main-theme {
        background-color: #f8be00;
        color: #fff;
    }

    .btn-main-theme:hover {
        border: 1px solid #f8be00;
        color: #000;
    }

    .myNewCard {
        border: 1px solid #ddd;
    }

    .form-label,
    .form-check-label {
        color: #000;
    }

    .pricing-section .single-pricing ul.list-item {
        margin-top: 15px;
    }

    .pricing-section .single-pricing p.price {
        margin-bottom: 0;
        margin-top: 15px;
    }

    #crmRenewal .modal-content,
    #crmUpgrade .modal-content {
        width: 150%;
        margin: 0;
    }

    #crmUpgrade .modal,
    #crmRenewal .modal {
        padding-top: 0;
    }

    #crmUpgrade .modal-dialog,
    #crmRenewal .modal-dialog {
        max-width: fit-content;
    }

    [onclick="logoutFUN()"]:hover {
        color: #000;
    }

    #crmUpgrade .price span,
    #crmRenewal .price span {
        font-size: 1.5rem;
    }

    .crm_details_card a {
        color: #0d6efd;
    }

    .crm_details_card {
        border: 1px solid #ddd;
        /*width: 300px;*/
        font-family: Arial, sans-serif;
        border-radius: 10px;
    }

    .crm_details_card p {
        display: flex;
        justify-content: space-between;
        margin: 10px 0;
    }
    

    .label {
        font-weight: bold;
        flex: 1;
        text-align: left;
    }
   
    .value {
        flex: auto; 
        white-space: nowrap;
    }
    #expiryDateV{
        white-space: nowrap;
    }
    #planNameV{
        max-width: 96px;
    }
    #fullDomain{
        /* text-wrap: wrap; */
        /* max-width: 207px; */
        margin-right: 59px;
    }
    #length{
        padding-right: 28px;
        padding-left: 0
    }
    #expiry_date_chart{
            height: 150px;
            width: 150px;
            margin-left: 313px;
    }
    #expiry_date_chart svg{
        margin-left: -10px;
        height: 187px;
        margin-top: -193px;
    }
   
   
    .copy-icon {
        margin-left: 8px;
        cursor: pointer;
        color: #007bff;
        transition: color 0.3s ease;
    }

    .copy-icon:hover {
        color: #0056b3;
    }

    .copied {
        color: green;
    }

    .crm_details_card i {
        cursor: pointer;
    }

    .profile-greeting {
        position: relative;
        background-color: #6362e7;
        color: #fff;
        /*height: 205px;*/
        border: none;
    }

    .media {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
    }

    .media .media-body {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
    }

    .profile-greeting .greeting-user h1 {
        margin-bottom: 12px;
        font-size: 24px;
        color: #fff;
    }

    .profile-greeting .greeting-user p {
        font-weight: 400;
        color: rgba(255, 255, 255, .67);
        margin-bottom: 0;
        max-width: 56%;
        width: -webkit-fit-content;
        width: -moz-fit-content;
        width: fit-content;
        font-size: 16px;
    }

    .profile-greeting .greeting-user .btn {
       background: white;
    color: #6362e7;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        width: -webkit-fit-content;
        width: -moz-fit-content;
        width: fit-content;
        margin-top: 35px;
        line-height: 1;
        padding: 13px 17px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 5px;
        margin-top: 15px;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
    }

    .btn-outline-white_color {
        border-color: #fff;
        color: #fff;
        background-color: rgba(0, 0, 0, 0);
    }

    .profile-greeting .greeting-user .btn i {
        margin-left: 10px;
        font-weight: 700;
    }

    .profile-greeting .cartoon-img {
        position: absolute;
        bottom: 0;
        right: 0;
        overflow: hidden;
        margin-right: 20px;
        width: 40%;
    }

    .profile-greeting .greeting-user .btn:hover {
        background-color: #f8be00 !important;
    color: black;
    border:none;
    }

    #crm_details_modal .modal-content {
        width: 100%;
        border: none;
    }

    #crm_details_modal .crm_details_card {
        border: none;
    }

    .admin_name img {
        width: 50px;
    }

    .ad_section img {
        border-radius: 20px;
    }

    #setupCrmModal .modal-content {
        width: 100%;
        max-width: 100%;
    }

    #setupCrmModal .modal-dialog {
        max-width: 900px;
    }

    #setupCrmModal .btn-close {
        position: relative;
        top: -25px;
    }

    .card:hover {
        border-color: #c3c6ce;
        box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.25);
    }

    .btn-light:hover {
        border: 1px solid #d3d4d5;
    }

    #mobile_menu_opener {
        display: none;
    }

    @media screen and (max-width: 576px) {
        .page-wrapper {
            padding: 10px;
        }

        #mobile_menu_opener {
            display: block;
            font-size: 24px;
        }

        #left_sidebar {
            display: none;
            height: 100vh;
        }

        .left-sidebar .scroll-sidebar {
            height: calc(100vh - 210px);
        }

        .mobile_menu_toggler {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .profile-greeting .cartoon-img {
            display: none;
        }

        .profile-greeting .greeting-user p {
            max-width: 100%;
        }
    }

    @media screen and (max-width: 576px) {

        table {
            text-wrap-mode: nowrap;
        }

        .mobile_menu_toggler h1 {
            font-size: 18px !important;
        }

        .mobile_menu_toggler {
            font-size: 16px;
            margin-bottom: 15px;
        }

        #crmRenewal .modal-content,
        #crmUpgrade .modal-content {
            width: 100%;
        }

        .pricing-section .switch,
        .pricing-section .switch1 {
            width: 48px;
            height: 30px;
        }

    }
    @media (max-width: 767px) {
    .mobile_menu_toggler .admin_name {
        margin-top: 20px;
    }
}
/* Default position (for mobile) */
#profile-bar img {
    position: relative;
    margin-left: 10px;
}

/* For larger screens (Desktop) */
@media (min-width: 992px) {
    .mobile_menu_toggler {
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    #profile-bar {
        display: flex;
        align-items: center;
    }

    #profile-bar img {
        margin: 0;
    }
}
.mobile_menu_toggler .admin_name {
    text-align: center;
    margin-left: 5%;
    margin-top: 3rem;
}

/* For desktop screen sizes */
@media (min-width: 768px) {
    .mobile_menu_toggler .admin_name {
        text-align: right;
        margin-left: 0;
        margin-right: 5%;
    }
}
</style>
@endsection


@section('content')
@php
    $planList = null;
    $userToken = $_COOKIE['sessionToken'] ?? '';
    if ($userToken != '') {
        $response = Http::withToken($userToken)->post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode']??'IN'
        ]);
    } else {
        $response = Http::post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode']??'IN'
        ]);
    }
    if ($response->successful()) {
        $authUser = $response->json();
        if (isset($authUser['status']) && $authUser['status'] === 'success') {
            $planList = $authUser['data']['planList'] ?? null;
        }
    }
    //dd($userDetails);
@endphp

<section class="login_dashboard_section" id="main-dashboard-wrapper">
    @include('include.sidebar')
    <div class="page-wrapper">
           <div class="mobile_menu_toggler d-flex justify-content-between">
            <i class="fa-solid fa-bars ms-3" id="mobile_menu_opener" style="font-size: 30px;"></i>
            <h1 class="admin_name text-center mt-3 ms-5">Hello {{ $userDetails['userDetails']['name'] ?? '' }}</h1>
            <div class="fixed-profile p-2 mx-4 mb-1  rounded">
                <div class="btn-group" id="profile-bar">
                    <!-- Profile picture + edit icon -->
                    <a href="#" class="p-0" onclick="toggleDropdown(event)"
                        style="cursor: pointer; position: relative; display: inline-block;">
                        <img id="profile-pic"
                            src="{{ $userDetails['userDetails']['profile_img_url'] ?? 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg' }}"
                            class="profile-pic rounded-circle mt-2" alt="profile-img" style="object-fit: cover;">

                     
                    </a>

                    <!-- Hidden file input -->
                    <input type="file" id="profile-upload" accept="image/*" style="display:none;"
                        onchange="previewProfilePic(event)">

                    <!-- Dropdown -->
                    <div tabindex="-1" role="menu" class="dropdown-menu dropdown-menu-right" id="profile-bar-box"
                        style="display:none;">
                        <a href="/profile" type="button" class="dropdown-item">
                            <i class="fas fa-user"></i> View Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" onclick="logoutFUN()" type="button" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>

            </div>

        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card profile-greeting">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <div class="greeting-user">
        
                                    <h1>Download the GoRide Partner App</h1>
                                    <p>
                                        Manage your rides, track earnings, accept jobs instantly, and navigate smarter —
                                        all from one powerful app. Start using the GoRide Partner App today to boost your 
                                        productivity and streamline your workflow.
                                    </p>
        
                                    <div class="d-flex gap-4 mt-3">
                                        
                                        <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer&pli=1" target="_blank" style="margin-top: 15px;">
                                            <img src="{{asset('/goride/img/en_badge_web_generic.png')}}"
                                                 alt="Get it on Google Play"
                                                 style="height: 45px;">
                                        </a>
        
                                        <a class="btn btn-outline-white_color d-none" href="/docs/partner-app-guide.pdf">
                                            User Guide <i class="fa-solid fa-file-arrow-down"></i>
                                        </a>
                                    </div>
        
                                </div>
                            </div>
                            <div class="cartoon-img">
                                <img class="img-fluid"
                                src="{{ asset('goride/img/job-portal2.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

</section>

@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
    integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    document.getElementById("togglePassword").addEventListener("click", function () {
        var passwordField = document.getElementById("password");
        var icon = this;

        // Toggle the type attribute between password and text
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
    
    

        $(document).ready(function () {
            
            $('#mobile_menu_opener').click(function () {
                $('#left_sidebar').toggle();
                $(this).toggleClass('fa-close fa-close');
            });
            
            let get_setupCRM = getCookie('SETUP_CRM_POPUP');
            deleteCookie('SETUP_CRM_POPUP');
            if (get_setupCRM) { 
                try {
                    let decode_value = JSON.parse(get_setupCRM);
                    
                    // console.log(decode_value.crmIDs);
                    if (decode_value.crmIDs) {
                        crmSetupPop(decode_value.crmIDs);
                    }
                } catch (e) {
                    console.error("Error parsing cookie data:", e);
                }
            }else{
                
            }


            $(".switch").click(function () {
                if ($(this).hasClass("on")) {
                    $(this).removeClass("on").addClass("off");
                    $(".month").removeClass("active");
                    $(".year").addClass("active");
                    $('#month').hide();
                    $('#year').show();
                } else {
                    $(this).removeClass("off").addClass("on");
                    $(".year").removeClass("active");
                    $(".month").addClass("active");
                    $('#month').show();
                    $('#year').hide();
                }
            });



        $(".switch1").click(function () {
            if ($(this).hasClass("on")) {
                $(this).removeClass("on").addClass("off");
                $(".month1").removeClass("active");
                $(".year1").addClass("active");
                $('#month1').hide();
                $('#year1').show();
            } else {
                $(this).removeClass("off").addClass("on");
                $(".year1").removeClass("active");
                $(".month1").addClass("active");
                $('#month1').show();
                $('#year1').hide();
            }
        });


        var wow = new WOW({
            boxClass: 'wow', // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset: 0, // distance to the element when triggering the animation (default is 0)
            mobile: true, // trigger animations on mobile devices (default is true)
            live: true, // act on asynchronously loaded content (default is true)
            callback: function (box) {
                // the callback is fired every time an animation is started
                // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null // optional scroll container selector, otherwise use window
        });
        wow.init();
    });
    
    $(document).on('change', '.status-toggle', function () {
        var id = $(this).data('id');
        var subId = $(this).data('subid');
        var status = $(this).is(':checked') ? 0 : 1;
        var h = new FormData();
        h.append('id', id);
        h.append('subId', subId);
        h.append('status', status);
        
        $.ajax({
            url: origin + '/api/crm-activate',
            type: "POST",
            processData: false,
            contentType: false,
            headers: {
                "Accept": "application/json; charset=utf-8",
                // "Content-Type": "application/json; charset=utf-8",
                "Authorization": 'Bearer ' + getCookie("sessionToken"),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: h,
            success: function (response) {
                
                let message = status == 0 ? 'CRM Now Activated' : 'CRM Now DeActivated';
                if(response.status == 'success'){
                    
                    Swal.fire({
                        title: 'Success!',
                        text: message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Refresh the page
                        }
                    });
                    
                }else{
                    
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                    
                }
            },
            error: function (xhr) {
                alert('Error updating status');
            }
        });
    });

    function copyToClipboard(elementId, iconElement) {
        var textToCopy = document.getElementById(elementId).textContent;
        navigator.clipboard.writeText(textToCopy).then(function () {
            iconElement.classList.remove('fa-copy');
            iconElement.classList.add('fa-check', 'copied');
            //   alert(elementId.charAt(0).toUpperCase() + elementId.slice(1) + " copied to clipboard!");
            setTimeout(function () {
                iconElement.classList.remove('fa-check', 'copied');
                iconElement.classList.add('fa-copy');
            }, 2000);
        }, function (err) {
            console.error("Failed to copy: ", err);
        });
    }
</script>



<script src="https://assets.calendly.com/assets/external/widget.js" type="text/javascript"></script>


 <script id="rendered-js" >


const tracking = {
  utmSource: 'goRide',
  utmMedium: 'web',
  utmCampaign: 'request_demo',
};


const customer = {
  fname: _.capitalize("{{ $userDetails['userDetails']['name'] ?? '' }}"),
  lname: "{{ $userDetails['userDetails']['lname'] ?? '' }}",
  email: "{{ $userDetails['userDetails']['email'] ?? '' }}",
  a1: '' 
};


const dynamicPath = '{{ env('CALENDLY_URL') }}';


const prefillData = {
  name: customer.fname,
  email: customer.email,
  customAnswers: {
    a1: customer.a1
  },
  utm: { ...tracking } 
};


Calendly.initBadgeWidget({
  url: dynamicPath,
  prefill: prefillData,
  text: `Request to Demo.`,
  color: '#000000',
  textColor: '#ffffff',
  branding: false
});

    </script>
      <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = document.getElementById("profile-bar-box");
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block"; // Show dropdown
            } else {
                dropdown.style.display = "none"; // Hide dropdown
            }
        }

        // Optional: Hide dropdown when clicking outside
        document.addEventListener("click", function (event) {
            const dropdown = document.getElementById("profile-bar-box");
            const profileBar = document.getElementById("profile-bar");
            if (!profileBar.contains(event.target)) {
                dropdown.style.display = "none"; // Hide dropdown
            }
        });
    </script>
@endsection