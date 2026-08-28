@extends('layouts.app')

@section('css')
<style> 
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
    .container {
    max-width: 800px;
    width: 90%;
    margin: 130px auto 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

@media (max-width: 767px) {
    .container {
        margin: 100px auto;
        padding: 15px;
        width: 95%;
        max-width: 100%;
    }
}

.under-line {
    color: #f9bf00;
    margin-left: 10px;
    text-decoration: underline;
    text-decoration-color: aliceblue;
}

.container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #f9bf00;
    font-size: 27px;
}

.form-control {
    margin-bottom: 15px;
    border: 1px solid #ced4da !important;
}

.btn-login, .btn-save {
    background-color: #f9bf00;
    border: none;
    padding: 8px 24px;
}

.btn-login:hover, .btn-save:hover {
    background-color: #0056b3;
    color: #fff;
}

.change-password-link {
    text-align: center;
    margin-top: 10px;
}

.modal-footer {
    justify-content: center !important;
    border-top: none !important;
    padding-bottom: 20px !important;
}

.modal-content {
    background-color: #fefefe;
    margin: 7% auto;
    padding: 10px;
    border: 1px solid #888;
    width: 90%;
    max-width: 700px;
    position: relative;
}

.mobile_menu_toggler {
    position: fixed;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
}


.dropdown-menu {
    position: absolute;
    right: -39px;
    top: 72px;
    display: none;
}

.fixed-profile {
    position: absolute;
    top: 10px;
    right: 10px;
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
    .input-group .btn {
    position: relative;
    z-index: 2;
    height: 38px;
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
        padding: 15px;
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
        flex: 1;
        text-align: right;
        white-space: nowrap;
    }

    .copy-icon {
        margin-left: 10px;
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
            font-size: 30px;
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
  /* Small success toast container */
.toast-success {
  position: fixed;
  background-color: #4CAF50; /* Green for success */
  color: white;
  border-radius: 30px; /* Rounded edges for small pop-up */
  display: flex;
  align-items: center;
  font-family: Arial, sans-serif;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transform: translateX(100%);
  transition: opacity 0.3s, transform 0.3s;
  font-size: 14px;
}

/* Show toast (when active) */
.toast-success.show {
  opacity: 1;
  transform: translateX(0);
}

/* Checkmark icon */
.toast-success .icon {
  margin-right: 8px;
  font-size: 16px; /* Smaller icon for compact design */
}

/* Optional: Toast message style */
.toast-success .message {
  font-size: 14px;
  white-space: nowrap;
}

</style>
@endsection


@section('content')
@php
    $planList = null;
    $userToken = $_COOKIE['sessionToken'] ?? '';
    if ($userToken != '') {
        $response = Http::withToken($userToken)->post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode']
        ]);
    } else {
        $response = Http::post(url('/api/planList'), [
            'countryCode' => $_COOKIE['countryCode']
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
            <i class="fa-solid fa-bars ms-5 mt-3" id="mobile_menu_opener" style="font-size: 30px;"></i>
            <div class="fixed-profile p-2 mx-4 mb-1 bg-secondary-subtle rounded">
            <div class="btn-group" id="profile-bar">
        <a href="#" class="p-0" onclick="toggleDropdown(event)" style="cursor: pointer;">
        <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
        class="rounded-circle mt-2" width="30" height="30" alt="profile-img" style="cursor: pointer;">
        </a>
        <div tabindex="-1" role="menu" class="dropdown-menu dropdown-menu-right" id="profile-bar-box">
            <a href="profile" type="button" class="dropdown-item" style="cursor: pointer;">
                <i class="fas fa-user"></i> Profile 
            </a>
            <a href="#" style="cursor: pointer;" onclick="logoutFUN()" contenteditable="false" type="button" class="dropdown-item" style="cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
        </div>
    
</section>
                    <!-- ////////////////////////////////////////////////////////////// -->
                    <div class="container">
    <h2 class="text-center mb-4">My Profile</h2>
    <form>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" placeholder="Enter your username" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="whatsapp" class="form-label">WhatsApp Number</label>
                <div class="input-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="country-code">+91</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-code="+91">India (+91)</a></li>
                    </ul>
                    <input type="tel" class="form-control" placeholder="WhatsApp number" aria-label="Phone number">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" placeholder="Enter your Email" name="email" id="email" required oninput="email_validation($(this).val(), 'email')" maxlength="70" autocomplete="off">
                <span id="email_err" class="text-danger"></span>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" id="password" class="form-control" placeholder="Enter your password">
                </div>
                <div class="mt-2">
                    <a href="#" data-bs-toggle="modal" class="text-decoration-none" data-bs-target="#changePasswordModal">Change Password</a>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" onclick="showSwalfire()">Update</button>
        </div>
    </form>
</div>


            <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="changePasswordForm" novalidate>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <!-- <input type="password" id="password" class="form-control" placeholder="Enter your password"> -->
                                        <input id="password-field1" type="password" placeholder="Enter New Password" class="form-control" name="password-field" maxlength="15">
                                        <span class="input-group-text" style="height: 43px;">
                                            <i class="fa fa-eye" id="togglePasswordmodel1" style="cursor: pointer;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <!-- <input type="password" id="password" class="form-control" placeholder="Enter your password"> -->
                                        <input id="password-field2" type="password" placeholder="Confirm  Password" class="form-control" name="password-field" maxlength="15">
                                        <span class="input-group-text" style="height: 43px;">
                                            <i class="fa fa-eye" id="togglePasswordmodel2" style="cursor: pointer;"></i>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                            <button type="submit" form="changePasswordForm" class="btn btn-primary btn-save">Save changes</button>
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
        $(document).ready(function () {
            $('#mobile_menu_opener').click(function () {
                $('#left_sidebar').toggle();
                $(this).toggleClass('fa-close fa-close');
            });

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
    });
</script>
<script>
function showSwalfire()
    {
        Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Updated Successfully.!",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        backdrop: false,
        customClass: {
            title: 'my_sweet_title',
            icon: 'my_sweet_icon'
        }
    });

    }
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