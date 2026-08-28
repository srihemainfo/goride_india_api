@extends('layouts.app')

@section('css')
<style>
 .bot-chat {
        display: none;
    }
.calendly-badge-widget{
    display:none;
}
.plus-icon {
    position: absolute;
    /*bottom: 8px;*/
    /*right: 8px;*/
    background: #007bff;
    color: #fff;
    font-size: 20px;
    font-weight: bold;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    pointer-events: none; /* so clicks still trigger file input */
}
.form-select:focus {

    box-shadow: none !important;
    outline: none !important;
}

input[type="password"], input[type="email"], input[type="text"], input[type="file"], input[type="tel"],textarea {
    max-width: 100%;
    margin-bottom: 15px;
    padding: 10px;
    height: auto;
    background-color: #fff;
    -webkit-box-shadow: none;
    box-shadow: none;
    display: block;
    width: 100%;
    line-height: 1.5em;
    font-family: 'Outfit', sans-serif;
    font-size: 14px;
    font-weight: 300;
    color: #555;
    background-image: none;
    border: none;
    border-radius: 5px;
}
    .form-group label {
        font-weight: 600;
        color: #2c3e50;
        /* Dark blue-gray */
    }

 

    /* On focus */
    .form-group input:focus {
        border-color: #f9bf00;
        background-color: #ffffff;
        box-shadow: 0 0 5px rgb(50 50 50);
    }

    .upload-card {
        border: 2px dashed #aaa;
        border-radius: 12px;
        width: 100%;
        max-width: 350px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f9fdfd;
        cursor: pointer;
        transition: 0.3s;
        overflow: hidden;
        /* keeps image inside */
    }

    .upload-card:hover {
        background: #eefafa;
    }

    .dummy-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .border-dashed {
        border: 2px dashed #ccc;
        border-radius: 10px;
        transition: 0.3s;
    }

    .border-dashed:hover {
        background: #f9f9f9;
        border-color: #999;
    }

    .page-wrapper {

        margin-left: 270px;
        height: 100vh;
        /*overflow-y: auto;*/
        padding: 10px;
    }

    #profile-bar-box .dropdown-item:hover {
        background-color: #f9bf00;
        color: #000;
    }

    #profile-bar-box .dropdown-item i {
        margin-right: 8px;
    }

    .profile-wrapper {

        margin: auto;
        /*background: #fff;*/
        padding: 25px;
        border-radius: 12px;
        /*box-shadow: 0 5px 15px rgba(0,0,0,0.08);*/
    }

    .profile-heading {
        font-size: 26px;
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .section-heading {
        font-size: 20px;
        margin-bottom: 30px;
        color: #34495e;
        border-left: 4px solid #3498db;
        padding-left: 10px;
    }



    .form-action {
        text-align: center;
    }

    .btn-submit {
        background: #3498db;
        color: white;
        padding: 12px 25px;
        font-size: 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-submit:hover {
        background: #2980b9;
    }

    /* Preview Section */
    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }

    .preview-card {
        text-align: center;
        color: black;
        font-weight: normal;
    }

    .preview-card img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }

        .profile-wrapper {
            padding: 15px;
        }
    }

    .edit-icon-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #ff9800;
        color: white;
        padding: 3px;
        font-size: 12px;
        border-radius: 50%;
        border: 1px solid white;
        cursor: pointer;
    }

    #profile-bar img {
        width: 80px;
        object-fit: cover;
    }

    .title {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 30px;
    }


    .section-title {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }



    .form-group input,
    .form-group select {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #aaa;
        border-radius: 4px;
        background-color: #f9f9f9;
        color: black;
        font-weight: 500;
    }

    .form-group input[readonly] {
        background-color: #e9f1ff;
        border-color: #cdd9ef;
    }

    .full-width {
        grid-column: span 3;
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
    input:checked+.slider {
        background-color: #f4ba00;
    }

    input:checked+.slider:before {
        transform: translateX(22px);
    }




    #profile-bar-box {
        top: 85px;
        right: 0px;
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
        font-size: 21px;
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

    #expiryDateV {
        white-space: nowrap;
    }

    #planNameV {
        max-width: 96px;
    }

    #fullDomain {
        /* text-wrap: wrap; */
        /* max-width: 207px; */
        margin-right: 59px;
    }

    #length {
        padding-right: 28px;
        padding-left: 0
    }

    #expiry_date_chart {
        height: 150px;
        width: 150px;
        margin-left: 313px;
    }

    #expiry_date_chart svg {
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
            margin: 0px;
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

    .option-card {
        border: 2px solid #ccc;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .option-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.2);
    }

    .option-card.selected {
        border: 2px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
    }

    .option-btn.active {
        background-color: #007bff;
        color: white;
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
        <div class="mobile_menu_toggler d-flex  justify-content-between justify-content-lg-end">
            <i class="fa-solid fa-bars ms-3" id="mobile_menu_opener" style="font-size: 30px;"></i>

            <div class="fixed-profile p-2 mx-4 mb-1 bg-secondary-subtle rounded">
                <div class="btn-group" id="profile-bar">
                    <!-- Profile picture + edit icon -->
                    <a href="#" class="p-0" onclick="toggleDropdown(event)"
                        style="cursor: pointer; position: relative; display: inline-block;">
                        <img id="profile-pic"
                            src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                            class="profile-pic rounded-circle mt-2" alt="profile-img" style="object-fit: cover;">

                        <!-- Edit icon overlay -->
                        <i class="fa fa-edit edit-icon-overlay" onclick="triggerFileUpload(event)">
                        </i>
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


        <div class="profile-wrapper">
            <h1 class="profile-heading">My Profile</h1>
            <form id="profile_update" enctype="multipart/form-data" onsubmit="profile_update(event)">

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-section">
                            <h2 class="section-heading mt-3">Personal Info</h2>
                            <div class="mb-1">
                                <div class="form-group">
                                    <label>Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="f_name" placeholder="Enter full name">
                                </div>
                                <div class="form-group">
                                    <label>Email<span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="f_email" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" id="f_phone" placeholder="Enter phone number">
                                </div>
                                <div class="form-group mb-3">
                                    <label>ID Proof Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="id_proof_number"
                                        placeholder="Enter ID Number" required>
                                </div>
                                <div class="form-group mb-3">
                                            <label>Profile Image <span class="text-danger">*</span></label>
                                            <div class="upload-card  position-relative" onclick="document.getElementById('profileInput').click();">
                                                <img id="previewProfile" src="https://www.goride.net.in/goride/img/profile.png" class="dummy-img ">
                                                <input type="file" id="profileInput" name="profile_image" accept="image/*" capture="user" class="d-none" onchange="previewImage(this,'previewProfile')">
                                                     <span class="plus-icon">+</span>
                                            </div>
                                        </div>


                            </div>
                        </div>

                    </div>
                    <div class="col-md-6 col-12">
                        <section class="section">
                            <h2 class="section-heading mt-3">Document Upload</h2>


                            <div class="mb-3">

                                <div class="row">
                                    <div class="col-md-12 col-12">
                                        <div class="form-group mb-3">
                                            <label>ID Proof Type <span class="text-danger">*</span></label>
                                            <select class="form-select" name="id_proof_type" required="">
                                                <option value="">Select</option>
                                                <option value="aadhar">Aadhar Card</option>
                                                <option value="pan">PAN Card</option>
                                                <option value="licence">Driving Licence</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group mb-3">
                                            <label>ID Proof Front <span class="text-danger">*</span></label>
                                            <div class="upload-card position-relative"
                                                onclick="document.getElementById('idFrontInput').click();">
                                                <img id="previewFront"
                                                   src="{{ asset('goride/img/id-card.png') }}"
                                                    class="dummy-img">
                                                <input type="file" id="idFrontInput" name="aadhar_image_front"
                                                    accept="image/*" capture="environment" class="d-none"
                                                    onchange="previewImage(this,'previewFront')">
                                                    <span class="plus-icon">+</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="form-group mb-3">
                                            <label>ID Proof Back <span class="text-danger">*</span></label>
                                            <div class="upload-card position-relative"
                                                onclick="document.getElementById('idBackInput').click();">
                                                <img id="previewBack"
                                                          src="{{ asset('goride/img/card_back.png') }}"
                                                    class="dummy-img">
                                                <input type="file" id="idBackInput" name="aadhar_image_back"
                                                    accept="image/*" capture="environment" class="d-none"
                                                    onchange="previewImage(this,'previewBack')">
                                                    <span class="plus-icon">+</span>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <small class="text-muted  d-block">
                                        <i class="fas fa-info-circle me-1"></i> File size must be above than 1MB
                                    </small>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>



                <!-- Submit -->
                <div class="row justify-content-center mt-3">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" id="form-btn">Update</button>
                    </div>
                </div>

            </form>

            <!-- Preview Section -->
            <section class="section" id="section_img" style="display:none;">
                <h2 class="section-heading">Document Preview</h2>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6 text-center">
                        <label>Aadhar Card (Front)</label>
                        <img src="https://airportrides-storage.s3.amazonaws.com/goride-aadhar/11-1/ckBwQ1XaR2aaXGzVzr1CUdzMnu5uMfZhRJJvpGkm.jpg"
                            class="card-img-top mx-auto d-block" alt="aadhar_image_front" id="aadhar_image_front"
                            style="width: 150px; height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    </div>

                    <div class="col-md-3 col-sm-6 text-center">
                        <label>Aadhar Card (Back)</label>
                        <img src="https://airportrides-storage.s3.amazonaws.com/goride-aadhar/11-Screenshot%202024-01-23%20152221/1Sgo6PJxKXRaahrHga0D2nJ93wlzCMd1PUoFxsG9.png"
                            class="card-img-top mx-auto d-block" alt="aadhar_image_back" id="aadhar_image_back"
                            style="width: 150px; height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    </div>

                    <!--<div class="col-md-3 col-sm-6 text-center">-->
                    <!--    <label>Licence</label>-->
                    <!--    <img src="https://airportrides-storage.s3.amazonaws.com/goride-driver/11-Screenshot%202024-01-23%20152221/DXZJONrobOsdemEZyNcqqAJGb2RTMypvB5LEXtVC.png"-->
                    <!--        class="card-img-top mx-auto d-block" alt="licence_image" id="licence_image"-->
                    <!--        style="width: 150px; height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">-->
                    <!--</div>-->

                    <div class="col-md-3 col-sm-6 text-center">
                        <label>Profile Image</label>
                        <img src="https://airportrides-storage.s3.amazonaws.com/goride-driver/11-user-1/Fj3vgIG7Zdh2fLRkU3LeSHJYfPdZfPqj6tLX5fnw.jpg"
                            class="card-img-top mx-auto d-block" alt="profile_image" id="profile_image"
                            style="width: 150px; height: 100px; object-fit: contain; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </section>

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
    document.querySelectorAll('#profile-bar-box .dropdown-item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('#profile-bar-box .dropdown-item').forEach(function (el) {
                el.classList.remove('active-selected');
            });
            this.classList.add('active-selected');
        });
    });

    // document.getElementById("togglePassword").addEventListener("click", function () {
    //     var passwordField = document.getElementById("password");
    //     var icon = this;

    //     // Toggle the type attribute between password and text
    //     if (passwordField.type === "password") {
    //         passwordField.type = "text";
    //         icon.classList.remove("fa-eye");
    //         icon.classList.add("fa-eye-slash");
    //     } else {
    //         passwordField.type = "password";
    //         icon.classList.remove("fa-eye-slash");
    //         icon.classList.add("fa-eye");
    //     }
    // });



    $(document).ready(function () {

        $.ajax({
            url: "{{ env('APP_API') }}get-profile",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    // showToast('success', response.message, 3000);
                    let data = response.data;
                    $('#f_name').val(data.name);
                    $('#f_email').val(data.email);
                    $('#f_phone').val(data.mobile);

                    if (data.profile_img_url) {
                        $('#section_img').show();
                    } else {

                        $('#section_img').hide();
                    }


                    $('#aadhar_image_front').attr('src', data.aadhar_image_front);
                    $('#aadhar_image_back').attr('src', data.aadhar_image_back);
                    $('#licence_image').attr('src', data.licence_image);
                    $('#profile_image').attr('src', data.profile_img_url);
                    $('#profile-pic').attr('src', data.profile_img_url ?? 'https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg');

                    // $('#f_name') = data.name;
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            }
        });



        $('#mobile_menu_opener').click(function () {
            $('#left_sidebar').toggle();
            $(this).toggleClass('fa-close fa-close');
        });

        if (localStorage.getItem('showFareTypeModal') === 'true') {
            $('#FaretypeModal').modal('show');
            localStorage.removeItem('showFareTypeModal'); // prevent showing again on next reload
        }

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
        } else {

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

    function profile_update(event) {
        event.preventDefault();

        let form = event.target;
        let errors = [];
        let firstInvalid = null;

        // Required fields
        const requiredFields = [
            { name: "name", label: "Full Name", type: "text" },
            { name: "email", label: "Email", type: "text" },
            { name: "phone", label: "Phone Number", type: "text" },
            { name: "aadhar_image_front", label: "Aadhar Card (Front)", type: "file" },
            { name: "aadhar_image_back", label: "Aadhar Card (Back)", type: "file" },
            // { name: "licence_image", label: "Licence", type: "file" },
            { name: "profile_image", label: "Profile Image", type: "file" }
        ];

        // Validation loop
        requiredFields.forEach(field => {
            let input = $(form).find(`[name="${field.name}"]`);
            let isEmpty = (field.type === "file")
                ? input.get(0).files.length === 0
                : $.trim(input.val()) === "";

            if (isEmpty) {
                errors.push(`${field.label} is required`);
                if (!firstInvalid) firstInvalid = input;
            }
        });

        // Show errors
        if (errors.length > 0) {

            showToast('error', errors.join("<br>"), 5000);
            if (firstInvalid) firstInvalid.focus();
            return;
        }

        // Prepare FormData
        let formData = new FormData(form);

        // Append dial_code from cookie
        // let dialCode = getCookie("dial_code") || "";
        let dialCode = '91';
        formData.append("dial_code", dialCode);
        formData.append("company_name", 'Your Company');
        formData.append("aadhar_no", '123456789012');

        // Submit via AJAX
        $.ajax({
            url: "{{ env('APP_API') }}update-profile",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#form-btn");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message, 3000);
                    location.reload();
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#form-btn");
                btn.prop('disabled', false).html('Update');
            }
        });
    }

    let hiddencrmid = 'null';

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
                if (response.status == 'success') {

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

                } else {

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

            hiddencrmid = crmID;

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
                            // let redirectURL = origin;
                            // localStorage.setItem('userData', JSON.stringify(response.data));
                            // showToast('success', response.message, 5000);
                            localStorage.setItem('showFareTypeModal', 'true');
                            showToast('success', response.message, 5000);
                            setTimeout(() => location.reload(), 5000);
                            $('#setupCrmModal').modal('hide');
                            // $('#FaretypeModal').modal('show');
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

    let selectedOption = null;

    function selectCard(card) {
        // Remove 'selected' from all cards
        document.querySelectorAll('.option-card').forEach(function (el) {
            el.classList.remove('selected');
        });

        // Add 'selected' to clicked card
        card.classList.add('selected');

        // Enable the Next button
        document.getElementById('nextBtn').disabled = false;

        // Store selected value
        selectedOption = card.getAttribute('data-value');
        console.log("Selected:", selectedOption);
    }

    document.getElementById('nextBtn').addEventListener('click', function () {
        if (selectedOption === 'instance_booking') {
            var modal = new bootstrap.Modal(document.getElementById('instanceBookingModal'));
            modal.show();
        } else if (selectedOption === 'fare_type') {
            var modal = new bootstrap.Modal(document.getElementById('fareTypeOptionsModal'));
            modal.show();
        }
    });

    function showSubmit(sectionId, btn) {
        // Highlight selected
        const buttons = document.querySelectorAll(`#${sectionId} .option-btn`);
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Show submit section
        const submitSection = document.querySelector(`#${sectionId} .submit-section`);
        submitSection.classList.remove('d-none');

        // Store selected name and value using dataset
        const selectedName = btn.innerText.trim();
        let selectedValue = null;

        switch (selectedName.toLowerCase()) {
            case 'automation': selectedValue = 1; break;
            case 'manual': selectedValue = 2; break;
            case 'mileage': selectedValue = 3; break;
            case 'hourly': selectedValue = 4; break;
            case 'tariff': selectedValue = 5; break;
        }

        // Store for submit
        btn.closest(`#${sectionId}`).dataset.selectedName = selectedName;
        btn.closest(`#${sectionId}`).dataset.selectedValue = selectedValue;
    }


    function copyToClipboard(elementId, iconElement) {
        var textToCopy = $('#' + elementId).val();
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

    //Faretype function
    function submitFareChoice(btn) {
        const parentSection = btn.closest('.modal-body');
        const selectedName = parentSection.dataset.selectedName;
        const selectedValue = parentSection.dataset.selectedValue;

        if (!selectedName || !selectedValue) {
            showToast('error', 'Please select an option before submitting', 3000);
            return;
        }

        // Collect other data
        const crmID = $('#crmID').val(); // If exists
        const domainPrefix = $('#domainPrefix').val();
        const userName = $('#username').val();
        const password = $('#password').val();

        let h = new FormData();
        h.append('crmID', hiddencrmid ?? '');
        h.append('domainPrefix', domainPrefix);
        h.append('userName', userName);
        h.append('passWord', password);
        h.append('fareTypeName', selectedName);
        h.append('fareTypeValue', selectedValue);
        // console.log("Selected Fare Type:", selectedName, selectedValue);
        $.ajax({
            url: origin + '/api/createfaretype',
            type: "POST",
            headers: {
                "Accept": "application/json; charset=utf-8",
                // "Content-Type": "application/json; charset=utf-8",
                "Authorization": 'Bearer ' + getCookie("sessionToken"),
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: h,
            contentType: false,
            processData: false,
            success: function (response) {
                showToast('success', 'Fare Type Created Successfully', 4000);
                $('.modal').modal('hide'); // close all modals
                hiddencrmid = 'null';
                setTimeout(() => {
                    location.reload();
                }, 4000);
            },
            error: function (xhr) {
                showToast('error', 'Something went wrong!', 4000);
            }
        });
    }


    const crmSetupPop = (crmID) => {
        try {
            if (crmID == '') {
                showToast(`error`, 'CRM ID has been missing try again', 5000);
                return false;
            }
            $(`#setupCrmModal`).modal('show');
            // $(`#FaretypeModal`).modal('show');
            $(`#createCRM`).attr(`onclick`, `createCRM(${crmID})`);
        } catch (e) {
            console.log(`Error : ${e.message}`);
        }
    }

    const viewCrmPop = (ins) => {
        try {
            if (ins.crmID == '') {
                showToast('error', 'CRM ID has been missing try again', 5000);
                return false;
            }

            let crmidsend = ins.crmID;
            let fullDomainsend = ins.fullDomain;
            let subscription_idsend = ins.subscription_id;

            $.ajax({
                url: origin + '/api/checkfaretype',
                type: "POST",
                headers: {
                    "Accept": "application/json; charset=utf-8",
                    // "Content-Type": "application/json; charset=utf-8",
                    "Authorization": 'Bearer ' + getCookie("sessionToken"),
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    crmid: crmidsend,
                    fullDomain: fullDomainsend,
                    subscription_id: subscription_idsend,
                },
                success: function (response) {
                    // console.log('jana1',response.data.data);
                    if (response.status === 'success' && response.data.data === 0) {
                        $('#FaretypeModal').modal('show');
                        showToast('warning', 'Select Fare Type', 5000);
                        hiddencrmid = ins.crmID;
                    } else {
                        // showToast('error', response.message ?? 'Unexpected error', 5000);
                        // console.log(response.message);
                        $('#planNameV').text(ins.planName);
                        $('#expiryDateV').html(`<i class="fa-solid fa-hourglass-end me-2"></i> ${moment(ins.expiryDate, 'DD/MM/YYYY').format('DD.MM.YYYY')}</span>`);
                        $('#usernameV').text(ins.userName);
                        $('#passwordVV').text(ins.passWord);
                        $('#fullDomain').text(ins.fullDomain).attr('href', `https://${ins.fullDomain}`);

                        if (moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(), 'days') >= 0) {
                            dayCountChart(
                                moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(ins.purchaseDate, 'DD/MM/YYYY'), 'days'),
                                moment(ins.expiryDate, 'DD/MM/YYYY').diff(moment(), 'days')
                            );
                        }

                        // $('#renawalPlanV').attr('onclick', `$('#crm_details_modal').modal('hide');openRenewal(${ins.crmID}, '${ins.planName}');`).show();
                        // if (ins.subCripted === 'YES') {
                        //     $('#renawalPlanV').hide();
                        //     if (moment(ins.expiryDate).isAfter(moment())) {
                        //         $('#renawalPlanV').attr('onclick', `$('#crm_details_modal').modal('hide');cancelSubCription(${ins.crmID}, '${ins.planName}');`).text('Cancel Subscription').show();
                        //     }
                        // }

                        $('#crm_details_modal').modal('show');
                    }
                },
                error: function (xhr) {
                    showToast('error', 'Server error occurred', 5000);
                }
            });

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

    function modifySubFun(ins) {
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


<script id="rendered-js">


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
    const defaultProfilePic = "https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg";

    function triggerFileUpload(e) {
        e.stopPropagation(); // prevent dropdown toggle
        document.getElementById("profile-upload").click();
    }

    function previewProfilePic(event) {
        const file = event.target.files[0];
        const profileImg = document.getElementById("profile-pic");

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profileImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            profileImg.src = defaultProfilePic;
        }
    }

    function toggleDropdown(event) {
        event.preventDefault();
        const dropdown = document.getElementById("profile-bar-box");
        dropdown.style.display = (dropdown.style.display === "none" || dropdown.style.display === "") ? "block" : "none";
    }

    // Hide dropdown on outside click
    document.addEventListener("click", function (event) {
        const dropdown = document.getElementById("profile-bar-box");
        const profileBar = document.getElementById("profile-bar");
        if (!profileBar.contains(event.target)) {
            dropdown.style.display = "none";
        }
    });


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
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.classList.remove("opacity-70"); // optional

                // Hide the plus icon linked to this preview
                const plusIcon = preview.parentElement.querySelector(".plus-icon");
                if (plusIcon) {
                    plusIcon.style.display = "none";
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection