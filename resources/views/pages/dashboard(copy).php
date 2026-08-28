@extends('layouts.app')

@section('css')
<style>


.btn-url {
    background-color: #007bff;
    padding: 4px 8px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: background-color 0.3s;
}
.btn-url:hover {
  background-color: #0056b3; /* Darker shade on hover */
  transform: scale(1.05);    /* Slight zoom effect */
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
    z-index: 999 !important;
  
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
        right: 10px;
        overflow: hidden;
        margin-right: 20px;
    }

    .profile-greeting .greeting-user .btn:hover {
        background-color: #fff !important;
        color: #6362e7;
    }

    #crm_details_modal .modal-content {
        width: 100%;
        border: none;
        height: 277px;
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
            font-size: 25px;
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
    #expiry_date_chart svg

 {   display: none;
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

    .fixed-profile {
        position: absolute;
        top: 10px;
        right: 10px;
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
            'countryCode' => $_COOKIE['countryCode']?? 'IN'
        ]);
    } else {
        $response = Http::post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode']?? 'IN'
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
            <div class="fixed-profile p-2 mx-4 mb-1 bg-secondary-subtle rounded">
            <div class="btn-group" id="profile-bar">
        <a href="#" class="p-0" onclick="toggleDropdown(event)" style="cursor: pointer;">
        <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
        class="rounded-circle mt-2" width="30" height="30" alt="profile-img" style="cursor: pointer;">
        </a>
        <div tabindex="-1" role="menu" class="dropdown-menu dropdown-menu-right" id="profile-bar-box">
            <!--<a href="profile" type="button" class="dropdown-item" style="cursor: pointer;">-->
            <!--    <i class="fas fa-user"></i> Profile -->
            <!--</a>-->
            <!-- <div class="dropdown-divider"></div> -->
            <a href="#" style="cursor: pointer;" onclick="logoutFUN()" contenteditable="false" type="button" class="dropdown-item" style="cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>


            <!--<div class="hstack row">-->
                
                <!--<div class="john-title col-6 p-0">-->
                    <!--<h6 class="mb-0 fs-4 fw-semibold">Mathew</h6>-->
                <!--    <span class="fs-6 text-dark">{{ $userDetails['userDetails']['name'] }}</span>-->
                <!--</div>-->
                <!--<button class="border-0 bg-transparent ms-auto col-2" onclick="logoutFUN()" title="Logout"-->
                <!--    tabindex="0" type="button" aria-label="logout" data-bs-toggle="tooltip"-->
                <!--    data-bs-placement="top" data-bs-title="logout">-->
                <!--    <i class="fa-light fa-power-off"></i>-->
                <!--</button>-->
            <!--</div>-->
        </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card profile-greeting">
                    <div class="card-body">
                        <div class="media">
                            <div class="media-body">
                                <div class="greeting-user">
                                    <h1>Welcome back! Your dashboard is ready and waiting.</h1>
                                    <p>You’re just a few steps away from optimizing your rides with our exclusive
                                        packages.
                                        Unlock special rates and enhanced features to take your business to the next
                                        level.</p>
                                    <a class="btn btn-outline-white_color" href="/pricing">Get Started<i
                                            class="fa-light fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="cartoon-img"><img class="img-fluid"
                                src="{{ asset('goride/img/dashboard-welcome.svg') }}" alt=""></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>List</h4>
                    </div>
                    @if (
                            isset($userDetails['userDetails']) &&
                            isset($userDetails['userDetails']['crmList']) &&
                            count($userDetails['userDetails']['crmList']) > 0
                        )
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="align-middle mb-0 table table-hover">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>S.No</th>
                                                                 <th>Subscription ID</th>
                                                                <th>Package</th>
                                                                <th>Purchase Date</th>
                                                                <th>Expiry Date</th>
                                                                <th>CRM Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="text-center">
                                                            @foreach ($userDetails['userDetails']['crmList'] as $key => $value)

                                                                <tr>
                                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                                    <td>{{ $value['subscrID'] ?? '' }}</td>
                                                                    <td>{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}</td>
                                                                    <td>{{ isset($value['createdon']) ? date('d/m/Y', strtotime($value['createdon'])) : '' }}
                                                                    </td>
                                                                    <td>{{ isset($value['expiryDate']) ? date('d/m/Y', strtotime($value['expiryDate'])) : '' }}
                                                                    </td>
                                                                    <td>
                                                                        <label class="switch" title="{{ $value['active_status'] == 1 ? 'Active' : 'Inactive' }}">
                                                                            <input type="checkbox" class="status-toggle" data-id="{{ $value['id'] }}" data-subid="{{ $value['subscription_id'] }}" {{ $value['active_status'] == 1 ? 'checked' : '' }}>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </td>


                                                                    <td>
                                                                        @if ($value['crmStatus'] === 'pending')
                                                                            <button type="button" class="btn btn-light btn-sm"
                                                                                onclick="crmSetupPop({{ $value['id'] }})">Setup CRM</button>

                                                                        @elseif($value['subscription_id'] == null && $value['transactionID'] != null)

                                                                            @if ($value['currentPlanBenefits']['productDetails']['name'] !== 'Enterprise')
                                                                                <!-- <button type="button" class="btn btn-light btn-sm"
                                                                                                                                                                                                                                                                                                                                                                                                                        onclick="openUpgrade({{ $value['id'] }}, '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}')">Upgrade</button> -->
                                                                            @endif


                                                                            @if ($value['currentPlanBenefits']['productDetails']['name'] !== 'Free Plan')
                                                                                <!-- <button type="button" class="btn btn-light btn-sm"
                                                                                                                                                                                                                                                                                                                                                                                                                        onclick="openRenewal({{ $value['id'] }}, '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}')">Renewal</button> -->
                                                                            @endif


                                                                        @endif

                                                                        @if ($value['crmStatus'] === 'generated')
                                                                            <button type="button" class="btn btn-sm" style="background-color: #f4ba00;" id="view-crm"
                                                                                onclick="viewCrmPop({
                                                                                                                                                                                                                                                                                                                                                                                          'crmID': {{ $value['id'] }},
                                                                                                                                                                                                                                                                                                                                                                                          'purchaseDate' : '{{ isset($value['createdon']) ? date('d/m/Y', strtotime($value['createdon'])) : '' }}',
                                                                                                                                                                                                                                                                                                                                                                                          'planName': '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                         'expiryDate': '{{ isset($value['expiryDate']) ? date('d/m/Y', strtotime($value['expiryDate'])) : '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                     'userName': '{{ $value['crmReq']['userName'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                        'passWord': '{{ $value['crmReq']['passWord'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                        'subCripted': '{{$value['transactionID'] != null ? 'NO' : 'YES'}}',
                                                                                                                                                                                                                                                                                                                                                                                                                                        'subscription_id': '{{  $value['subscription_id'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                           'fullDomain': '{{ $value['crmReq']['fullDomain'] ?? '' }}'
                                                                                                                                                                                                                                                                                                                                                                                          })">View
                                                                                CRM</button>
                                                                                
                                                                                
                                                                                
                                                                                
                                                                                
                                                                            
                                                                        @endif
                                                                        
                                                                        @if($value['manual_sub_access'])
                                                                            <button type="button" class="btn btn-sm" style="background-color: #f4ba00;"
                                                                            onclick="changeSubFun({
                                                                                                                                                                                                                                                                                                                                                                                      'crmID': {{ $value['id'] }},
                                                                                                                                                                                                                                                                                                                                                                                      'purchaseDate' : '{{ isset($value['createdon']) ? date('d/m/Y', strtotime($value['createdon'])) : '' }}',
                                                                                                                                                                                                                                                                                                                                                                                      'planName': '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                     'expiryDate': '{{ isset($value['expiryDate']) ? date('d/m/Y', strtotime($value['expiryDate'])) : '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                 'userName': '{{ $value['crmReq']['userName'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                    'passWord': '{{ $value['crmReq']['passWord'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                    'subCripted': '{{$value['transactionID'] != null ? 'NO' : 'YES'}}',
                                                                                                                                                                                                                                                                                                                                                                                                                                    'subscription_id': '{{  $value['subscription_id'] ?? '' }}',
                                                                                                                                                                                                                                                                                                                                                                                                                                       'fullDomain': '{{ $value['crmReq']['fullDomain'] ?? '' }}'
                                                                                                                                                                                                                                                                                                                                                                                      })">Change Subscription
                                                                            </button>
                                                                            
                                                                        @endif


                                                                        @if (($value['transactionID'] == null && isset($value['subStatus']) && $value['subStatus'] === 'cancelled') || (isset($value['currentPlanBenefits']['planType']) && $value['currentPlanBenefits']['planType'] === 'TRAIL'))
                                                                            <button type="button" class="btn btn-light btn-sm"
                                                                                onclick="openUpgrade({{ $value['id'] }}, '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}')">Upgrade</button>
                                                                        @endif



                                                                        @if (
                                                                            is_null($value['transactionID']) &&
                                                                            isset($value['subStatus']) && $value['subStatus'] === 'pending' &&
                                                                            !empty($value['isActive']) &&
                                                                            isset($value['currentPlanBenefits']['planType']) &&
                                                                            $value['currentPlanBenefits']['planType'] !== 'TRAIL'
                                                                        )
                                                                            <button type="button" class="btn btn-light btn-sm"
                                                                                onclick="window.open('{{ $value['subRes']['short_url'] }}', '_blank')">
                                                                                Manual Payment
                                                                            </button>
                                                                        @endif
                                                                        @if ($value['transactionID'] == null && isset($value['subStatus']) && $value['subStatus'] === 'active' && $value['isActive'] && (isset($value['currentPlanBenefits']['planType']) && $value['currentPlanBenefits']['planType'] != 'TRAIL'))
                                                                            <button type="button" class="btn btn-light btn-sm"
                                                                                onclick="cancelSubCription({{ $value['id'] }}, '{{ $value['currentPlanBenefits']['productDetails']['name'] ?? '' }}')">Cancel
                                                                                Subscription</button>
                                                                        @endif




                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                    @else
                        <div class="card-body text-center">
                            <p>No Purchases have done <a href="/pricing" style="color:#0d6efd;">clike here</a> to buy now.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="ad_section card">
            <a href="/custom-plan">
                <img src="{{ asset('goride/img/goride-footer.png') }}" alt="goride_ads">
            </a>
        </div>
    </div>
</section>


<!-- Modal -->
<div class="modal fade" id="setupCrmModal" tabindex="-1" aria-labelledby="setupCrmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- <form> -->
            <div class="modal-header">
                <div>
                    <h1 class="modal-title fs-5">Setup CRM</h1>
                    <p>Easily manage customer relationships and driver interactions through our CRM-enabled dashboard.
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-3 row no-gutters">
                <div class="col-lg-6 d-flex">
                    <img src="{{ asset('goride/img/setup_image.svg') }}" alt="setup_image">
                </div>
                <div class="col-lg-6">
                    <div class="mb-2">
                        <label for="exampleInputEmail1" class="form-label">URL</label>
                        <div class="row">
                            <div class="col-7">
                                <input type="text" oninput="this.value = this.value.replace(/[^a-z0-9-]/g, '');" style="background-color: #e9ecef;"
                                    class="form-control" id="domainPrefix" 
                                    placeholder="Domain prefix (CRM)"
                                    aria-describedby="emailHelp" value="<?=  'DS' . str_pad(mt_rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT) . date('d'); ?>" readonly>
                            </div>
                            <div class="col-5">
                                <input type="text" class="form-control" value=".goride.run" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Username</label>
                        <input type="text" id="username"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9._@]/g, '');" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control m-0" name="password"
                                maxlength="15">
                            <span class="input-group-text border-0">
                                <i class="fa fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" class="form-check-input" id="crmCheckBox">
                        <label class="form-check-label" for="crmCheckBox">I Accept <a href="/terms" style="color: blue;"
                                target="_blank">Terms & Conditions.</a></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="createCRM" class="btn btn-main-theme mx-auto">Create</button>
            </div>
            <!-- </form> -->
        </div>
    </div>
</div>


<div class="modal fade" id="crmUpgrade" tabindex="-1" aria-labelledby="setupCrmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Upgrade CRM Package</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row pricing-section justify-content-center m-0">


                <ul class="list-inline text-center switch-toggler-list" role="tablist" id="switch-toggle-tab">
                    <li class="month active"><a href="#">Monthly</a></li>
                    <li>
                        <label class="switch on">
                            <span class="slider round"></span>
                        </label>
                    </li>
                    <li class="year">
                        <a href="#">Yearly
                            <span class="save-notice">save 20% <i>yearly</i></span>
                        </a>
                    </li>
                </ul>
                <div class="tabed-content mb-2">
                    <div id="month">
                        <div class="row pricing-row justify-content-center">
                            @foreach ($planList['monthly'] as $plan)
                                @if ($plan['name'] != 'Free Plan')
                                    <div class="col-md-6 col-sm-6 col-xs-12  offerPlanL {{$plan['name']}}Offer">
                                        <div
                                            class="single-pricing text-center {{ $plan['name'] == 'Professional' ? 'popular' : '' }}">
                                            <form action="cart" method="post">
                                                @csrf
                                                <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                                <input type="hidden" name="planType" value="MONTHLY">
                                                <!-- <input type="hidden" name="purchaseType" value="UPGRADE"> -->

                                                <input type="hidden" name="purchaseType" value="NEW">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="crmID" value="">
                                                <input type="hidden" name="subscriptions" value="true">
                                                <div class="inner">
                                                    @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                                        <div class="plan-tag">
                                                            <p class="anu">
                                                                {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                                    Day</span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <h3 class="title">{{ $plan['name'] }}</h3>
                                                    <p class="price">
                                                        <!-- {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }} -->
                                                        {{($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price'])}}
                                                    </p>
                                                    <p class="price-label">Full access</p>
                                                    <ul class="list-item">
                                                        <li><i class="fa fa-check"></i>
                                                            {{  ($plan['name'] === 'Enterprise') ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) }}
                                                        </li>
                                                        <li><i class="fa fa-check "></i>
                                                            {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                        </li>
                                                        <li><i class="fa fa-check "></i>
                                                            {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                        </li>
                                                        {{-- @if ($plan['productType'] === 'TRAIL' && intval($plan['price']) <
                                                            1) --}} <!-- <li><i class="fa fa-check"></i>
                                                            {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                            </li> -->
                                                            {{-- @endif --}}
                                                            <li>
                                                                <i
                                                                    class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                            </li>
                                                            {{-- <li><i
                                                                    class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                Setup Fee</li> --}}
                                                    </ul>
                                                    {{-- <button type="submit" class="thm-btn borderd">Start Trial</button> --}}
                                                    <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span
                                                            class="upgratePlanBTN">{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Buy Now')  }}</span></a>
                                                    <!-- <p class="trial_text mt-2">Or <a href="#">Start Trial</a></p> -->
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div id="year">
                        <div class="row pricing-row justify-content-center">
                            @foreach ($planList['yearly'] as $plan)
                                <div class="col-md-6 col-sm-6 col-xs-12 offerPlanL {{$plan['name']}}Offer">
                                    <div
                                        class="single-pricing text-center {{ $plan['name'] == 'Professional' ? 'popular' : '' }}">
                                        <form action="cart" method="post">
                                            @csrf
                                            <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                            <input type="hidden" name="planType" value="YEARLY">
                                            <!-- <input type="hidden" name="purchaseType" value="UPGRADE"> -->
                                            <input type="hidden" name="purchaseType" value="NEW">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="crmID" value="">
                                            <input type="hidden" name="subscriptions" value="true">
                                            <div class="inner">
                                                @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                                    <div class="plan-tag">
                                                        <p class="anu">
                                                            {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                                Day</span>
                                                        </p>
                                                    </div>
                                                @endif
                                                <h3 class="title">{{ $plan['name'] }}</h3>
                                                <p class="price">
                                                    {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }}
                                                </p>
                                                <p class="price-label">Full access</p>
                                                <ul class="list-item">
                                                    <li><i class="fa fa-check"></i>
                                                        {{  ($plan['name'] === 'Enterprise') ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) }}
                                                    </li>
                                                    <li><i class="fa fa-check "></i>
                                                        {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                    </li>
                                                    <li><i class="fa fa-check "></i>
                                                        {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                    </li>

                                                    <li>
                                                        <i
                                                            class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                        {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                    </li>

                                                </ul>

                                                <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span
                                                        class="upgratePlanBTN">Buy
                                                        Now</span></a>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>



                @foreach ($planList['renewal'] as $plan)
                    <!-- <div class="col-6 mb-3" id="{{$plan['name']}}Offer">
                                                                                                                                                                                    <div class="single-pricing text-center">
                                                                                                                                                                                        <form action="cart" method="post">
                                                                                                                                                                                            @csrf
                                                                                                                                                                                            <input type="hidden" name="crmID" value="{{ $plan['id'] }}">
                                                                                                                                                                                            <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                                                                                                                                                                            <input type="hidden" name="planType"
                                                                                                                                                                                                value="{{ $plan['id'] === '2' ? 'MONTHLY' : 'YEARLY' }}">
                                                                                                                                                                                            <input type="hidden" name="purchaseType" value="RENEWAL">
                                                                                                                                                                                            <input type="hidden" name="quantity" value="1">
                                                                                                                                                                                            <div class="inner">
                                                                                                                                                                                                <h3 class="title">{{ $plan['name'] }}</h3>
                                                                                                                                                                                                <p class="price">
                                                                                                                                                                                                    {{ intval($plan['price']) > 0 ? '₹' . intval($plan['price']) : 'FREE' }}
                                                                                                                                                                                                    <span>/month</span>
                                                                                                                                                                                                </p>
                                                                                                                                                                                                <p class="price-label">Full access</p>
                                                                                                                                                                                                <ul class="list-item">
                                                                                                                                                                                                    <li><i class="fa fa-check"></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '') }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                    <li><i class="fa fa-check "></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                    <li><i class="fa fa-check "></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_website']) . ' page' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                   <li>
                                                                                                                                                                                                        <i class="fa fa-check"></i>
                                                                                                                                                                                                        {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                                                                                                                                                                        </li>

                                                                                                                                                                                                        <li>
                                                                                                                                                                                                            <i
                                                                                                                                                                                                                class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                                                                                                                                                            {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                                                                                                                                                                        </li>
                                                                                                                                                                                                        <li><i
                                                                                                                                                                                                                class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                                                                                                                                                            Setup Fee</li>
                                                                                                                                                                                                </ul>

                                                                                                                                                                                                <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span>Upgrade
                                                                                                                                                                                                        Now</span></a>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </form>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div> -->
                @endforeach
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="crmRenewal" tabindex="-1" aria-labelledby="setupCrmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Renewal CRM Package</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row pricing-section justify-content-center m-0">


                <ul class="list-inline text-center switch-toggler-list" role="tablist" id="switch-toggle-tab">
                    <li class="month1 active"><a href="#">Monthly</a></li>
                    <li>
                        <label class="switch1 on">
                            <span class="slider round"></span>
                        </label>
                    </li>
                    <li class="year1">
                        <a href="#">Yearly
                            <span class="save-notice">save 20% <i>yearly</i></span>
                        </a>
                    </li>
                </ul>
                <div class="tabed-content mb-2">
                    <div id="month1">
                        <div class="row pricing-row justify-content-center">
                            @foreach ($planList['monthly'] as $plan)
                                @if ($plan['name'] != 'Free Plan')
                                    <div class="col-md-6 col-sm-6  offerPlanL {{$plan['name']}}Offer">
                                        <div
                                            class="single-pricing text-center {{ $plan['name'] == 'Professional' ? 'popular' : '' }}">
                                            <form action="cart" method="post">
                                                @csrf
                                                <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                                <input type="hidden" name="planType" value="MONTHLY">
                                                <input type="hidden" name="purchaseType" value="RENEWAL">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="crmID" value="">
                                                <div class="inner">
                                                    @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                                        <div class="plan-tag">
                                                            <p class="anu">
                                                                {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                                    Day</span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                    <h3 class="title">{{ $plan['name'] }}</h3>
                                                    <p class="price">
                                                        <!-- {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }} -->
                                                        {{($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price'])}}
                                                    </p>
                                                    <p class="price-label">Full access</p>
                                                    <ul class="list-item">
                                                        <li><i class="fa fa-check"></i>
                                                            {{  ($plan['name'] === 'Enterprise') ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) }}
                                                        </li>
                                                        <li><i class="fa fa-check "></i>
                                                            {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                        </li>
                                                        <li><i class="fa fa-check "></i>
                                                            {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                        </li>

                                                        <li>
                                                            <i
                                                                class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                            {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                        </li>

                                                    </ul>
                                                    {{-- <button type="submit" class="thm-btn borderd">Start Trial</button> --}}
                                                    <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span
                                                            class="upgratePlanBTN">{{ ($plan['productType'] === 'TRAIL' ? 'Go with free' : 'Buy Now')  }}</span></a>
                                                    <!-- <p class="trial_text mt-2">Or <a href="#">Start Trial</a></p> -->
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div id="year1" style="display: none;">
                        <div class="row pricing-row justify-content-center">
                            @foreach ($planList['yearly'] as $plan)
                                <div class="col-md-6 col-sm-6 offerPlanL {{$plan['name']}}Offer">
                                    <div
                                        class="single-pricing text-center {{ $plan['name'] == 'Professional' ? 'popular' : '' }}">
                                        <form action="cart" method="post">
                                            @csrf
                                            <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                            <input type="hidden" name="planType" value="YEARLY">
                                            <input type="hidden" name="purchaseType" value="RENEWAL">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="crmID" value="">

                                            <div class="inner">
                                                @if ($plan['productType'] != 'TRAIL' && $plan['perDay'] > 0)
                                                    <div class="plan-tag">
                                                        <p class="anu">
                                                            {{($plan['currency'] === 'INR' ? '₹' : '$') . ($plan['perDay'] > 0 ? number_format($plan['perDay'], 1) : 0) }}<span>Per
                                                                Day</span>
                                                        </p>
                                                    </div>
                                                @endif
                                                <h3 class="title">{{ $plan['name'] }}</h3>
                                                <p class="price">
                                                    {{ intval($plan['price']) > 0 ? ($plan['currency'] === 'INR' ? '₹' : '$') . intval($plan['price']) : 'FREE' }}
                                                </p>
                                                <p class="price-label">Full access</p>
                                                <ul class="list-item">
                                                    <li><i class="fa fa-check"></i>
                                                        {{  ($plan['name'] === 'Enterprise') ? 'Unlimited drivers' : ('Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '')) }}
                                                    </li>
                                                    <li><i class="fa fa-check "></i>
                                                        {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                    </li>
                                                    <li><i class="fa fa-check "></i>
                                                        {{ 'Upto ' . intval($plan['no_of_website']) . ' website' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                    </li>
                                                    {{-- @if ($plan['productType'] === 'TRAIL' && intval($plan['price']) <
                                                        1) --}} <!-- <li><i class="fa fa-check"></i>
                                                        {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                        </li> -->
                                                        {{-- @endif --}}
                                                        <li>
                                                            <i
                                                                class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                            {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                        </li>
                                                        {{-- <li><i
                                                                class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                            Setup Fee</li> --}}
                                                </ul>
                                                {{-- <button type="submit" class="thm-btn borderd">Start Trial</button> --}}
                                                <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span
                                                        class="upgratePlanBTN">Buy
                                                        Now</span></a>
                                                <!-- <p class="trial_text mt-2">Or <a href="#">Start Trial</a></p> -->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>



                @foreach ($planList['renewal'] as $plan)
                    <!-- <div class="col-6 mb-3" id="{{$plan['name']}}Offer">
                                                                                                                                                                                    <div class="single-pricing text-center">
                                                                                                                                                                                        <form action="cart" method="post">
                                                                                                                                                                                            @csrf
                                                                                                                                                                                            <input type="hidden" name="crmID" value="{{ $plan['id'] }}">
                                                                                                                                                                                            <input type="hidden" name="productID" value="{{ $plan['id'] }}">
                                                                                                                                                                                            <input type="hidden" name="planType"
                                                                                                                                                                                                value="{{ $plan['id'] === '2' ? 'MONTHLY' : 'YEARLY' }}">
                                                                                                                                                                                            <input type="hidden" name="purchaseType" value="RENEWAL">
                                                                                                                                                                                            <input type="hidden" name="quantity" value="1">
                                                                                                                                                                                            <div class="inner">
                                                                                                                                                                                                <h3 class="title">{{ $plan['name'] }}</h3>
                                                                                                                                                                                                <p class="price">
                                                                                                                                                                                                    {{ intval($plan['price']) > 0 ? '₹' . intval($plan['price']) : 'FREE' }}
                                                                                                                                                                                                    <span>/month</span>
                                                                                                                                                                                                </p>
                                                                                                                                                                                                <p class="price-label">Full access</p>
                                                                                                                                                                                                <ul class="list-item">
                                                                                                                                                                                                    <li><i class="fa fa-check"></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_Vehicle']) . ' driver' . (intval($plan['no_of_Vehicle']) > 1 ? 's' : '') }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                    <li><i class="fa fa-check "></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_bookings']) . ' bookings' }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                    <li><i class="fa fa-check "></i>
                                                                                                                                                                                                        {{ 'Upto ' . intval($plan['no_of_website']) . ' page' . (intval($plan['no_of_website']) > 1 ? 's' : '') }}
                                                                                                                                                                                                    </li>
                                                                                                                                                                                                   <li>
                                                                                                                                                                                                        <i class="fa fa-check"></i>
                                                                                                                                                                                                        {{ $plan['trailsDays'] . ' Day FREE Trial' }}
                                                                                                                                                                                                        </li>

                                                                                                                                                                                                        <li>
                                                                                                                                                                                                            <i
                                                                                                                                                                                                                class="{{ intval($plan['validityDays']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                                                                                                                                                            {{ intval($plan['validityDays']) > 0 ? round(intval($plan['validityDays']) / 30) . ' Month' . (round(intval($plan['validityDays']) / 30) > 1 ? 's' : '') : 'No License' }}
                                                                                                                                                                                                        </li>
                                                                                                                                                                                                        <li><i
                                                                                                                                                                                                                class="{{ intval($plan['setupFees']) > 0 ? 'fa fa-check' : 'fas fa-times' }}"></i>
                                                                                                                                                                                                            Setup Fee</li>
                                                                                                                                                                                                </ul>

                                                                                                                                                                                                <a onclick="$(this).closest('form').submit();" class="thm-btn borderd"><span>Upgrade
                                                                                                                                                                                                        Now</span></a>
                                                                                                                                                                                            </div>
                                                                                                                                                                                        </form>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div> -->
                @endforeach
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="crm_details_modal" tabindex="-1" aria-labelledby="crm_details_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">CRM Details</h1>
            </div>
            <div class="modal-body">
                <div id="crm_details_section">
                    <div class="row crm_details_card no-gutters" style="height: 180px;">
                        <div class="col-lg-8 ms-2">
                            
                            <!--<p>Ex.Dt : <span>23.10.2027</span></p>-->
                            <p>
                                <span class="label">URL</span>
                                <span class="value ms-3">
                                    <span id="justTxt"></span> <br>
                                    <a id="fullDomain"
                                        target="_blank" class="btn btn-url text-white">mynewcrm.goride.run</a>
                                    </span>
                                </p>

                            <p><span class="label">Username</span>
                                <span class="value ms-3">
                                    <span id="usernameV">gorideuser</span>
                                    <i class="fas fa-copy copy-icon" onclick="copyToClipboard('usernameV', this)"
                                        title="Click to Copy"></i>
                                </span>
                            </p>
                            <p><span class="label">Password</span>
                                <span class="value">
                                    <span id="passwordV">********</span>
                                    <input type="hidden" value="" id="passwordVV">
                                    <i class="fas fa-copy copy-icon" onclick="copyToClipboard('passwordVV', this)"
                                        title="Click to Copy"></i>
                                </span>
                            </p>
                            <p><span class="label" id="planNameV">Personal</span><span class="value" id="expiryDateV"></span>
                            </p>
                        </div>
                        <div class="col-lg-6" id="length">
                            <div id="expiry_date_chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-none">
                <button id="renawalPlanV" class="btn btn-main-theme py-1 px-2 mx-auto">Renewal My Plan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change_sub_modal" tabindex="-1" aria-labelledby="change_sub_modalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Subscription Change</h1>
            </div>
            <div class="modal-body">
                <div id="crm_details_section">
                    <div class="row crm_details_card no-gutters" style="height: 180px;">
                        <div class="col-12 text-center">
                            <p class="d-block">This Process will cancel the previous subscription and create a new subscription. Then starts with new payment method.</br>
                                <strong class="fw-bold">Note*: But It continue with the same CRM Account.</strong>
                            </p>
                        </div>
                        <!--<div class="col-lg-8 ms-2">-->
                        <!--    <p><span class="label" id="planNameV">Personal</span><span class="value" id="expiryDateV"></span>-->
                        <!--    </p>-->
                            <!--<p>Ex.Dt : <span>23.10.2027</span></p>-->
                        <!--    <p><span class="label">URL</span><span class="value ms-3"><a id="fullDomain"-->
                        <!--                target="_blank">mynewcrm.goride.run</a></span></p>-->
                        <!--    <p><span class="label">Username</span>-->
                        <!--        <span class="value ms-3">-->
                        <!--            <span id="usernameV">gorideuser</span>-->
                        <!--            <i class="fas fa-copy copy-icon" onclick="copyToClipboard('usernameV', this)"-->
                        <!--                title="Click to Copy"></i>-->
                        <!--        </span>-->
                        <!--    </p>-->
                        <!--    <p><span class="label">Password</span>-->
                        <!--        <span class="value">-->
                        <!--            <span id="passwordV">********</span>-->
                        <!--            <input type="hidden" value="" id="passwordVV">-->
                        <!--            <i class="fas fa-copy copy-icon" onclick="copyToClipboard('passwordVV', this)"-->
                        <!--                title="Click to Copy"></i>-->
                        <!--        </span>-->
                        <!--    </p>-->
                        <!--</div>-->
                        <!--<div class="col-lg-6" id="length">-->
                        <!--    <div id="expiry_date_chart"></div>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-header">
                <button id="changeSubBtn" class="btn btn-main-theme py-1 px-2 mx-auto">Change My Subscription</button>
            </div>
        </div>
    </div>
</div>
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
  const cookies = document.cookie.split(';').reduce((acc, cookie) => {
    const [name, value] = cookie.trim().split('=');
    acc[name] = value;
    return acc;
  }, {});

  if (cookies.crm === 'true') {
    $('#view-crm').trigger('click');
    document.cookie = "crm=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
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
        var status = $(this).is(':checked') ? 1 : 0;
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
                
                let message = status ? 'CRM Now Activated' : 'CRM Now DeActivated';
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
    
    const dayCountChart = (totalDays, daysLeft) => {
        try {
            // var totalDays = 30;
            // var daysLeft = 25;
            var options = {
                series: [(daysLeft / totalDays) * 100],
                chart: {
                    height: 350,
                    type: 'radialBar',
                    offsetY: -10
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        dataLabels: {
                            name: {
                                fontSize: '16px',
                                color: undefined,
                                offsetY: 100
                            },
                            value: {
                                offsetY: 56,
                                fontSize: '22px',
                                color: undefined,
                                formatter: function () {
                                    return daysLeft;
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        shadeIntensity: 0.15,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 50, 65, 91]
                    }
                },
                stroke: {
                    dashArray: 4
                },
                labels: ['Days Left'],
            };
            var chart = new ApexCharts(document.querySelector("#expiry_date_chart"), options);
            chart.render();
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    }
    const validatePassword = (password) => {
        try {
            let res = {
                status: false,
                error: ''
            };
            // Minimum length of 8 characters
            if (password.length < 8) {
                res.error = 'Minimum length of 8 characters';
                return res;
            }
            // At least 1 uppercase letter
            if (!/[A-Z]/.test(password)) {
                res.error = 'At least 1 uppercase letter';
                return res;
            }
            // At least 1 digit
            if (!/[0-9]/.test(password)) {
                res.error = 'At least 1 digit';
                return res;
            }
            // At least 1 special character (non-alphanumeric)
            if (!/[!@#$%^&*()\-_=+{}[\]|;:'",<.>/?]/.test(password)) {
                res.error = 'At least 1 special character (non-alphanumeric)';
                return res;
            }
            // If all criteria are met
            res.status = true;
            return res;
        } catch (e) {
            console.log('Error: ' + e.message);
        }
    };
    const createCRM = (crmID) => {
        

        try {
            if (crmID == '') {
                showToast(`error`, 'CRM ID has been missing try again', 5000);
                return false;
            }
            // let cookieValue = getCookie('SETUP_CRM_POPUP');

            // if (cookieValue) { 
            //     deleteCookie('SETUP_CRM_POPUP');
            // }
            
            let domainPrefix = $(`#domainPrefix`).val();
            let userName = $(`#username`).val();
            let password = $(`#password`).val();
            const btn = $(`#createCRM`);
            if (domainPrefix == '' || domainPrefix == null || domainPrefix == undefined) {
                showToast(`error`, 'Kindly fill the domain prefix!', 5000);
                return false;
            }
            if (userName == '' || userName == null || userName == undefined) {
                showToast(`error`, 'Kindly fill the username!', 5000);
                return false;
            }
            if (password == '' || password == null || password == undefined) {
                showToast(`error`, 'Kindly fill the password!', 5000);
                return false;
            }
            if (password == '' || password == null || password == undefined) {
                showToast(`error`, 'Kindly fill the password!', 5000);
                return false;
            }
            let pssVal = validatePassword(password);
            if (!pssVal.status) {
                // $('#password_err').html('Kindly enter strong password');
                // $('#password').addClass('warningError');
                // // notifyToast('Kindly enter strong password', "error", "#password", "bottom");
                // return false;
                showToast(`error`, 'Password - ' + pssVal.error, 5000);
                return false;
            }
            if (!$(`#crmCheckBox`).prop('checked')) {
                showToast(`error`, 'Kindly accept the terms and conditions!', 5000);
                return false;
            }
            // crmCheckBox
            // return false;
            //             crmID
            // domainPrefix
            // userName
            // passWord
            // generateCRM
            var h = new FormData();
            h.append('crmID', crmID);
            h.append('domainPrefix', domainPrefix);
            h.append('userName', userName);
            h.append('passWord', password);
            $.ajax({
                url: origin + '/api/generateCRM',
                type: "POST",
                headers: {
                    "Accept": "application/json; charset=utf-8",
                    // "Content-Type": "application/json; charset=utf-8",
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: h,
                beforeSend: function () {
                    // Button Loading
                    btn.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    if (response != "") {
                        if (response.status == 'success') {
                            let redirectURL = origin;
                            document.cookie = "crm=true; path=/; max-age=" + 60 * 60 * 24 * 7;
                            // localStorage.setItem('userData', JSON.stringify(response.data));
                            showToast('success', response.message, 5000, location.reload());

                        } else {
                            // Loading Off 
                            btn.html(`Create`).prop('disabled', false);
                            showToast('error', response.message, 5000);
                            //  document.querySelector('.col-6.text-end.pl-0 a[href="loginwithotp"]').classList.add('blink-hard');
                            document.getElementById('signup-link').style.display = 'block';
                        }
                    } else {
                        btn.html(`Create`).prop('disabled', false);
                    }
                    // Loading Off 
                    // btn.html(`Create`).prop('disabled', false);
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                    btn.html(`Create`).prop('disabled', false);
                    console.error('Request failed');
                    console.error(xhr, status, error);
                }
            });
            // $(`#setupCrmModal`).modal('show');
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }
    
    function copyToClipboard(elementId, iconElement) {
        var textToCopy = $('#'+ elementId).val();
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
    const crmSetupPop = (crmID) => {
        try {
            if (crmID == '') {
                showToast(`error`, 'CRM ID has been missing try again', 5000);
                return false;
            }
            $(`#setupCrmModal`).modal('show');
            $(`#createCRM`).attr(`onclick`, `createCRM(${crmID})`);
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }
    
    const viewCrmPop = (ins) => {
        try {

            // console.log(ins);

            if (ins.crmID == '') {
                showToast(`error`, 'CRM ID has been missing try again', 5000);
                return false;
            }
            $(`#planNameV`).text(ins.planName);

            // console.log(ins.expiryDate, moment(ins.expiryDate).format('DD.MM.YYYY'));
            $(`#expiryDateV`).html(`<i class="fa-solid fa-hourglass-end me-2"></i> ${moment(ins.expiryDate, 'DD/MM/YYYY').format('DD.MM.YYYY')}</span>`);
            $(`#usernameV`).text(ins.userName);
            $(`#passwordVV`).text(ins.passWord);
            $(`#fullDomain`).text('Click here').attr(`href`, `https://${ins.fullDomain}`);
            $('#justTxt').text(ins.fullDomain);


            // console.log( moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(), 'days'),ins.purchaseDate,  ins.expiryDate, moment(ins.expiryDate).diff(moment(ins.purchaseDate), 'days'));
            if (moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(), 'days') >= 0) {
                dayCountChart(moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(ins.purchaseDate, 'DD/MM/YYYY'), 'days'), moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(), 'days'));
            }

            // $(`#renawalPlanV`).attr(`onclick`, `$('#crm_details_modal').modal('hide');openRenewal(${ins.crmID}, '${ins.planName}');`).show();
            // if (ins.subCripted === 'YES') {
            //     $(`#renawalPlanV`).hide();
            //     if (moment(ins.expiryDate).isAfter(moment())) {
            //         $(`#renawalPlanV`).attr(`onclick`, `$('#crm_details_modal').modal('hide');cancelSubCription(${ins.crmID}, '${ins.planName}');`).text('Cancel Subscription').show();
            //     }
            // }

            $(`#crm_details_modal`).modal('show');
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }


    const openUpgrade = (crmID, planName) => {
        try {
            if (crmID == '' || planName == '') {
                return false;
            }
            $(`.offerPlanL`).show();


            // if (planName == 'Professional') {
            //     $(`.ProfessionalOffer`).hide();
            // }



            // if (planName == 'Enterprise') {
            //     return false;
            // }



            $('input[name="crmID"]').val(crmID);
            $(`.upgratePlanBTN`).text('Upgrade Now');
            $(`#crmUpgrade`).modal('show');
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }
    
    function changeSubFun(ins) {
        $('#change_sub_modal').modal('show');
       $('#changeSubBtn').attr('onclick', `modifySubFun(${JSON.stringify(ins)})`);
    }
    
    function modifySubFun(ins){
        // $('#changeSubBtn')
        // .off('click') // remove any previous click handlers
        // .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
        // .prop('disabled', true);
        
        // var h = new FormData();
        // h.append('crmID', crmID);
        // h.append('domainPrefix', domainPrefix);
        // h.append('userName', userName);
        // h.append('passWord', password);
        
            let formData = new FormData();
            for (const key in ins) {
                if (ins.hasOwnProperty(key)) {
                    formData.append(key, ins[key]);
                }
            }

        
            $.ajax({
                url: origin + '/api/changeSubscription',
                type: "POST",
                headers: {
                    "Accept": "application/json; charset=utf-8",
                    // "Content-Type": "application/json; charset=utf-8",
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                beforeSend: function () {
                    // Button Loading
                    $('#changeSubBtn').html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
                    ).prop('disabled', true);
                },
                success: function (response) {
                    if (response != "") {
                        
                        if (response.status == 'success') {
                            let redirectPay = response?.data?.data?.orderDetails?.short_url;
                            console.log(redirectPay)
                            console.log(response)
                            if (redirectPay) {
                                $('#change_sub_modal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Order Status',
                                    text: response.message,
                                    allowOutsideClick: false,
                                }).then(() => {
                                    window.open(redirectPay, '_blank');
                                    window.location.href = `${origin}/package-history`;
                                });
                            }
                            
                            // showToast('success', response.message, 5000);
                        } else {
                            showToast('error', response.message, 5000);
                        }
                        
                    } else {
                        btn.html(`Create`).prop('disabled', false);
                    }
                },
                processData: false,
                contentType: false,
                error: function (xhr, status, error) {
                    showToast("error", "Request failed", 5000);
                }
            });
        
        // console.log(ins)
    }



    const cancelSubCription = (crmID) => {
        try {
            if (crmID == '') {
                showToast(`error`, 'CRM ID has been missing try again', 5000);
                return false;
            }

            Swal.fire({
                title: 'The date is still active!',
                text: 'Do you want to proceed?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Close'
            }).then((result) => {
                if (result.isConfirmed) {
                    // const btn = $(`#createCRM`);

                    var h = new FormData();
                    h.append('crmID', crmID);
                    // h.append('domainPrefix', domainPrefix);
                    // h.append('userName', userName);
                    // h.append('passWord', password);
                    $.ajax({
                        url: origin + '/api/cancelSubCRM',
                        type: "POST",
                        headers: {
                            "Accept": "application/json; charset=utf-8",
                            // "Content-Type": "application/json; charset=utf-8",
                            "Authorization": 'Bearer ' + getCookie("sessionToken"),
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: h,
                        beforeSend: function () {
                            // Button Loading
                            // btn.html(
                            //     `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`
                            // ).prop('disabled', true);

                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we proceed.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                        },
                        success: function (response) {
                            Swal.close();
                            if (response != "") {
                                if (response.status == 'success') {
                                    let redirectURL = origin;
                                    // localStorage.setItem('userData', JSON.stringify(response.data));
                                    showToast('success', response.message, 5000, location.reload());
                                } else {
                                    // Loading Off 
                                    // btn.html(`Create`).prop('disabled', false);
                                    showToast('error', response.message, 5000);
                                    //  document.querySelector('.col-6.text-end.pl-0 a[href="loginwithotp"]').classList.add('blink-hard');
                                    document.getElementById('signup-link').style.display = 'block';
                                }
                            } else {
                                // btn.html(`Create`).prop('disabled', false);
                            }
                            // Loading Off 
                            // btn.html(`Create`).prop('disabled', false);
                        },
                        processData: false,
                        contentType: false,
                        error: function (xhr, status, error) {
                            showToast("error", "Request failed", 5000);
                            // btn.html(`Create`).prop('disabled', false);
                            console.error('Request failed');
                            console.error(xhr, status, error);
                        }
                    });
                    // $(`#setupCrmModal`).modal('show');

                } else {

                    console.log('User canceled the action');
                }
            });

        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }

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
    const openRenewal = (crmID, planName) => {
        try {
            if (crmID == '' || planName == '') {
                return false;
            }
            $(`.offerPlanL`).hide();


            // if(planName == 'Professional') {
            $(`.${planName}Offer`).show();
            // }



            if (planName == 'Free Plan') {
                return false;
            }



            $('input[name="crmID"]').val(crmID);
            $(`.upgratePlanBTN`).text('Renewal Now');
            $(`#crmRenewal`).modal('show');
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
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
    
<script>
    
window.addEventListener('DOMContentLoaded', function () {
    if (sessionStorage.getItem('triggerCalendlyClick') === 'true') {
        const calendlyButton = document.querySelector('.calendly-badge-content');
        if (calendlyButton) calendlyButton.click();
        sessionStorage.removeItem('triggerCalendlyClick');
    }
});
    
</script>
    
@endsection