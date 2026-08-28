@extends('layouts.app')

@section('css')
<style>
    .under-line {
        color: #f9bf00;
        margin-left: 20px;
        text-decoration-color: aliceblue;
        text-decoration: underline;

    }

    .login-container {
        max-width: 800px;
        margin: 140px 0 0 160px;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 600px) {
        .login-container {
            max-width: 90%;
            margin: 80px auto;
            padding: 15px;
        }
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #f9bf00;
        font-size: 27px;

    }

    .form-control {
        margin-bottom: 15px;
        border: 1px solid #ced4da !important;
    }

    .btn-login {
        background-color: #f9bf00;
        border: none;
        text-align: center;
        padding: 8px 24px;
    }

    .btn-save {
        background-color: #f9bf00;
        border: none;
    }

    .btm-save:hover {
        background-color: #0056b3;
    }

    .btn-login:hover {
        background-color: #0056b3;
    }

    .change-password-link {
        text-align: center;
        margin-top: 10px;
    }

    .modal-footer {
        justify-content: center !important;
        border-top: none !important;
        padding: 0 0 20px 0 !important;
    }

    #profile-bar-box {
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
        width: 80px;
        margin-top: -13px;
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

    .modal-content {
        background-color: #f2f2f2;
        margin: 7% auto;
        padding: 12px;
        border: 1px solid #888;
        width: 80%;
        max-width: 700px;
        position: relative;
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
            font-size: 16px;
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

    .sidebar-nav ul .sidebar-item.selected>.sidebar-link,
    .sidebar-nav ul .sidebar-item.selected>.sidebar-link.active,
    .sidebar-nav ul .sidebar-item>.sidebar-link.active {
        background-color: #f8be00;
        color: #fff;
    }

    body.swal2-no-backdrop .swal2-container .swal2-modal {
        display: flex !important;
        align-items: center;
        justify-content: space-around;
    }

    div:where(.swal2-container) div:where(.swal2-popup) {
        width: 25em !important;
        padding: .8em !important;
        height: 80px !important;
    }
    div:where(.swal2-container) {
        background: rgba(0, 0, 0, 0.3); 
}

div:where(.swal2-container) div:where(.swal2-popup) {
    width: 25em !important;
    padding: .8em !important;
    height: 80px !important;
}


@media (max-width: 600px) {
    div:where(.swal2-container) {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 100px !important;
        /* margin-top: 90%; */
        margin-right: 12px;
        background: rgba(0, 0, 0, 0.5) !important;
    }

    div:where(.swal2-container) div:where(.swal2-popup) {
        width: 90% !important;
        max-width: 300px !important;
        height: auto !important;
        height: auto !important;
        /* box-shadow: 0 8px 20px rgba(0, 0, 0, 0.8) !important; */
    }
}
    .my_sweet_title {
        font-size: 15px !important;
        font-weight: 400;
        letter-spacing: 1;
        padding: 0 !important;
    }

    .swal2-icon.my_sweet_icon {
        width: 40px;
        height: 40px;
        margin: 10px;
    }

    .swal2-icon.my_sweet_icon svg {
        width: 100% !important;
        height: 100% !important;
    }

    .swal2-icon.my_sweet_icon {
        grid-column: 1;
        grid-row: 1 / 99;
        align-self: center;
        width: 2em;
        min-width: 2em;
        height: 2em;
        margin: 0 .5em 0 0;
    }

    div:where(.swal2-icon).swal2-success [class^=swal2-success-circular-line][class$=left] {
        top: -0.8em !important;
        left: -0.5em !important;
        transform: rotate(-45deg) !important;
        transform-origin: 2em 2em !important;
        border-radius: 4em 0 0 4em !important;
        width: 1.6em !important;
        height: 3em !important;
    }

    div:where(.swal2-icon).swal2-success [class^=swal2-success-circular-line][class$=left] {
        top: -0.8em !important;
        left: -0.5em !important;
        transform: rotate(-45deg) !important;
        transform-origin: 2em 2em !important;
        border-radius: 4em 0 0 4em !important;
        width: 1.6em !important;
        height: 3em !important;
    }

    div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-line-tip {
        top: 1.125em !important;
        left: .1875em !important;
        width: .75em !important;
        height: .3125em !important;
    }

    div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-line-long {
        top: .9375em !important;
        right: .1875em !important;
        width: 1.375em !important;
        height: .3125em !important;
    }

    div:where(.swal2-icon).swal2-success .swal2-success-fix {
        top: 0 !important;
        left: .4375em !important;
        width: .4375em !important;
        height: 2.6875em !important;
    }

    div:where(.swal2-icon).swal2-success .swal2-success-ring {
        width: 2em !important;
        height: 2em !important;
    }

    div:where(.swal2-icon).swal2-success.swal2-icon-show .swal2-success-circular-line-right {
        top: -0.25em !important;
        left: .9375em !important;
        transform-origin: 0 1.5em !important;
        border-radius: 0 4em 4em 0 !important;
        width: 1.6em !important;
        height: 3em !important;
    }

    div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=left] {
        top: .875em !important;
        width: 1.375em !important;
        left: .3125em !important;
    }

    div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=right] {
        top: .875em !important;
        width: 1.375em !important;
        right: .3125em !important;
    }

    @media screen and (max-width: 476px) {
        .my_sweet_title {
            font-size: 12px;
        }
    }
</style>
@endsection

@section('content')
<section class="login_dashboard_section" id="main-dashboard-wrapper">
    @include('include.sidebar')



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
            <div class="container">
                <div class="row justify-content-center">
                    <!-- ////////////////////////////////////////////////////////////// -->
                    <div class="login-container">
                        <h2>My Profile</h2>
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" placeholder="Enter your username" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="whatsapp" class="form-label">WhatsApp Number</label>
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" style="height: 38px;" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="country-code">+91</span>
                                        </button>
                                        <ul class="dropdown-menu">

                                            <li><a class="dropdown-item" href="#" data-code="+91">India (+91)</a></li>

                                        </ul>
                                        <input type="tel" class="form-control" placeholder="WhatsApp number" aria-label="Phone number">
                                    </div>
                                </div>
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////// -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="lb-text form-label">Email</label>
                                        <input type="email" class="form-control" placeholder="Enter your Email" value="" name="email" id="email" required="" oninput="email_validation($(this).val(), 'email')" maxlength="70" autocomplete="off">
                                        <span id="email_err" class="spanClass" style="color: red;"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="d-flex">
                                        <div class="input-group">
                                            <input type="password" id="password" class="form-control" placeholder="Enter your password">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <a href="#" data-bs-toggle="modal" class="under-line" data-bs-target="#changePasswordModal">Change Password</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /////////////////////////////////////////////////////////////////////////////// -->
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-login" onclick="showSwalfire()">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
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
            </div>
        </div>
    </div>

    </div>
</section>

@endsection

@section('script')
<script>
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const code = e.target.getAttribute('data-code');
            document.querySelector('.country-code').textContent = code;
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
    document.getElementById('togglePasswordmodel1').addEventListener('click', function() {
        const passwordField = document.getElementById('password-field1');
        const icon = this;

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    document.getElementById('togglePasswordmodel2').addEventListener('click', function() {
        const passwordField = document.getElementById('password-field2');
        const icon = this;

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>


@endsection