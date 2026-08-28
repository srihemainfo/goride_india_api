@extends('layouts.app')

@section('content')

    <style>
      
.outer-wrapper{
    background: #bfc2c4;
    padding: 20px;
}
        /* --- NEW TRIP TYPE TABS STYLES --- */
        .trip-type-tabs {
            display: flex;
            margin-bottom: 10px;
            max-width: 280px;
            background-color: #f5f5f5;
            border-radius: 6px;
            padding: 3px;
        }
        .trip-type-tab {
            flex: 1;
            padding: 8px 15px;
            text-align: center;
            font-weight: 600;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .trip-type-tab:not(.selected-tab) {
            color: #666666;
            background-color: transparent;
        }
        .trip-type-tab.selected-tab {
            color: #ffffff;
            background-color: #f9bf00;
        }

        /* --- SEARCH FORM STYLES --- */
        .search-form-container {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-field-group {
            margin-bottom: 12px;
        }

        .field-label {
            display: block;
            font-size: 12px;
            color: #333333;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-control-custom, .form-select-custom {
            border: 1px solid #e0e0e0;
            background-color: #ffffff;
            border-radius: 4px;
            color: #333333;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 10px;
            height: 40px;
            box-shadow: none;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control-custom:focus, .form-select-custom:focus {
            border-color: #f9bf00;
            box-shadow: 0 0 0 0.2rem rgba(249, 191, 0, 0.25);
            outline: none;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666666;
            z-index: 5;
            font-size: 14px;
        }

        .input-with-icon .form-control-custom {
            padding-left: 35px;
                border: 2px solid #f9bf00;
        }

 .datepicker-dropdown .active,
            .datepicker-dropdown .active:hover {
                background-color: #f8be00 !important;
                border-color: #f8be00 !important;
                color: #fff !important;
            }

            .datepicker-dropdown {
                padding: 4px !important;
                font-size: 0.9rem;
            }

            .datepicker-dropdown table {
                margin: 0;
            }

            .datepicker-dropdown td,
            .datepicker-dropdown th {
                padding: 2px 4px !important;
                font-size: 0.85rem;
            }

            .datepicker-dropdown .datepicker-days {
                padding: 2px;
            }
               .datepicker table tr td.active,
            .datepicker table tr td.active.highlighted,
            .datepicker table tr td.active.focused,
            .datepicker table tr td.active:hover,
            .datepicker table tr td.active:focus,
            .datepicker table tr td.active:active {
                background-color: #f9bf00 !important;
                background-image: none !important;
                color: #1a1a1a;
                border-color: #f9bf00 !important;
            }

            .datepicker table tr th.prev,
            .datepicker table tr th.next,
            .datepicker table tr th.datepicker-switch {
                color: #cc9900;
            }
        .btn-search {
            background-color: #f9bf00;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 15px;
            width: 100%;
            transition: background-color 0.3s;
            color: #333333;
            margin-top: 17px;
        }

        .btn-search:hover {
            background-color: #e6ac00;
            color: #333333;
        }

        /* --- RESULTS HEADER STYLES --- */
        .results-header-bar {
            background-color: #f9bf00;
            color: #333333;
            padding: 10px 0;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .results-summary {
            font-size: 14px;
            font-weight: 600;
        }

        .date-navigation {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .date-nav-btn {
            background-color: #333333;
            color: white;
            border: none;
            border-radius: 3px;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            font-size: 12px;
        }

        .date-range {
            font-weight: 600;
            margin: 0 8px;
            font-size: 13px;
        }

        /* --- DATE TABS STYLES --- */
        .date-tabs-container {
            display: flex;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 15px;
            gap: 8px;
        }

        .date-tab {
            min-width: 130px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background-color: #ffffff;
            font-size: 12px;
        }

        .date-tab:hover {
            border-color: #f9bf00;
        }

        .date-tab.active {
            background-color: #172a3a;
            border-color: #172a3a;
            color: white;
        }

        .date-tab.cheapest {
            border-color: #e3001e;
        }

        .date-tab.cheapest .price {
            color: #e3001e;
            font-weight: 700;
        }

        .date-tab.active .price {
            color: white;
        }

        .date-range-text {
            font-size: 12px;
            color: #666666;
            margin-bottom: 3px;
        }

        .date-tab.active .date-range-text {
            color: #cccccc;
        }

        .price {
            font-size: 14px;
            font-weight: 600;
            color: #333333;
        }

        .flexible-dates-btn {
            min-width: 130px;
            padding: 10px;
            border-radius: 6px;
            border: 1px dashed #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .flexible-dates-btn:hover {
            border-color: #f9bf00;
            background-color: #fff9e6;
        }

        /* --- CAR CARD STYLES --- */
        .car-card {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            background-color: #ffffff;
            font-size: 13px;
        }

        .car-card:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        .car-image-section {
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f7f7f7;
            height: 100px;
        }

        .car-image {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .car-info-section {
            padding: 12px;
        }

        .car-title {
            font-size: 15px;
            font-weight: 700;
            color: #333333;
            margin-bottom: 3px;
        }

        .car-subtitle {
            font-size: 12px;
            color: #666666;
            margin-bottom: 8px;
        }

        .car-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 8px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 3px;
            font-size: 12px;
            color: #666666;
        }

        .feature-icon {
            color: #666666;
            font-size: 12px;
        }

        .car-details {
            margin: 8px 0;
            font-size: 12px;
        }

        .detail-item {
            margin-bottom: 3px;
        }

        .inclusions-toggle {
            color: #f9bf00;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            margin: 5px 0;
        }

        .inclusions-details {
            background-color: #f9f9f9;
            padding: 8px;
            border-radius: 4px;
            margin-top: 5px;
            font-size: 11px;
            display: none;
        }

        .inclusions-details.show {
            display: block;
        }

        .car-provider {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
            margin-top: 8px;
        }

        .provider-logo {
            height: 16px;
            object-fit: contain;
        }

        .rating {
            background-color: #008714;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }

        .car-price-section {
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-left: 1px solid #f0f0f0;
            height: 100%;
        }

        .price-container {
            text-align: right;
        }

        .total-price {
            font-size: 18px;
            font-weight: 700;
            color: #333333;
            margin-bottom: 3px;
        }

        .price-note {
            font-size: 11px;
            color: #008714;
            font-weight: 600;
        }

        .btn-select {
            background-color: #172a3a;
            color: white;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 10px;
            font-size: 13px;
        }

        .btn-select:hover {
            background-color: #273d52;
            color: white;
        }

        /* --- ROUTE BOX STYLES --- */
        .route-box {
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin-bottom: 20px;
        }

        .route-box-header {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .route-details {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .btn-view-route {
            background-color: #f9bf00;
            color: #333333;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            font-weight: 600;
            font-size: 13px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn-view-route:hover {
            background-color: #e6ac00;
            color: #333333;
        }

        /* --- CLOCKPICKER STYLES --- */
        .clockpicker-popover {
                z-index: 1060;
            }

            .clockpicker-canvas line,
            .clockpicker-canvas span,
            .clockpicker-canvas-bg {
                stroke: #f9bf00 !important;
                fill: #f9bf00 !important;
            }

            .clockpicker-canvas-bg {
                opacity: 0.2;
            }

            .clockpicker-canvas center {
                background-color: #f9bf00 !important;
                color: #1a1a1a;
            }

            .clockpicker-popover .btn-primary {
                background-color: #f9bf00;
                border-color: #f9bf00;
                color: #1a1a1a;
            }

            .clockpicker-popover .btn-default {
                color: #333;
            }

        /* --- MODAL STYLES --- */
        .modal-header {
            background-color: #f9f9f9;
            border-bottom: 1px solid #e0e0e0;
            padding: 12px 15px;
        }

        .modal-title {
            font-weight: 700;
            color: #333333;
            font-size: 16px;
        }

        .price-grid {
            margin-top: 15px;
        }

        .price-row {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
            font-size: 13px;
        }

        .price-row-header {
            font-weight: 700;
            background-color: #f9f9f9;
        }

        .price-cell {
            flex: 1;
            padding: 8px 5px;
            text-align: center;
            border-right: 1px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .price-cell:last-child {
            border-right: none;
        }

        .price-cell:hover {
            background-color: #fff9e6;
        }

        .price-cell.active {
            background-color: #0d6efd;
            color: white;
            border-radius: 3px;
        }

        .price-cell.cheapest {
            color: #e3001e;
            font-weight: 700;
        }

        .price-cell.na {
            color: #999999;
            cursor: not-allowed;
        }

        .date-header {
            flex: 1;
            padding: 8px 5px;
            text-align: center;
            font-weight: 600;
            background-color: #f9f9f9;
        }

        .row-header {
            flex: 0 0 120px;
            padding: 8px 5px;
            font-weight: 600;
            background-color: #f9f9f9;
        }

        .modal-footer {
            background-color: #f9f9f9;
            border-top: 1px solid #e0e0e0;
            padding: 10px 15px;
        }

        .lowest-price-note {
            font-size: 14px;
        }

        .lowest-price {
            color: #e3001e;
            font-weight: 700;
        }

        /* --- RESPONSIVE STYLES --- */
        @media (max-width: 768px) {
            .trip-type-tabs {
                max-width: 100%;
            }
            
            .search-form-container {
                padding: 10px;
            }
            
            .car-card {
                flex-direction: column;
            }
            
            .car-price-section {
                border-left: none;
                border-top: 1px solid #f0f0f0;
                align-items: center;
                text-align: center;
            }
            
            .date-navigation {
                justify-content: center;
                margin-top: 10px;
            }
        }
    </style>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <section class="page-header"
        style="background-image: url('{{ request()->is('agency') ? asset('goride/img/handshake.webp') : asset('goride/img/breadcrump_banner.webp') }}'); height: 200px; background-size: cover; background-position: center center; padding-top: 30px;">

    </section>

    <section class="mt-4 mb-4 outer-wrapper">
        <div class="container">
            <div class="trip-type-tabs">
                <div class="trip-type-tab selected-tab" data-trip-type="One Way" id="oneWayTab">
                    <i class="bi bi-arrow-right"></i>
                    ONE WAY
                </div>
                <div class="trip-type-tab" data-trip-type="Round Trip" id="returnTab">
                    <i class="bi bi-arrow-left-right"></i>
                    ROUND TRIP
                </div>
            </div>
            
            <div class="search-form-container">
                <form id="carHireForm">
                    <input type="hidden" id="tripType" value="One Way">

                    <div class="row g-2">
                        <div class="col-md-2 form-field-group">
                            <label for="from" class="field-label">Pickup Location</label>
                            <div class="input-with-icon">
                                <i class="bi bi-geo-alt input-icon"></i>
                                <input type="text" id="from" class="form-control-custom" placeholder="City, Airport" value="Chennai">
                            </div>
                        </div>

                        <div class="col-md-2 form-field-group">
                            <label for="to" class="field-label">Drop Location</label>
                            <div class="input-with-icon">
                                <i class="bi bi-geo-alt-fill input-icon"></i>
                                <input type="text" id="to" class="form-control-custom" placeholder="City, Airport" value="Madurai">
                            </div>
                        </div>

                        <div class="col-md-2 form-field-group">
                            <label for="pickupDate" class="field-label">Pickup Date</label>
                            <div class="input-with-icon">
                                <i class="bi bi-calendar input-icon"></i>
                                <input type="text" id="pickupDate" class="form-control-custom date-picker" placeholder="DD/MM/YYYY" value="30/10/2025">
                            </div>
                        </div>

                        <div class="col-md-2 form-field-group">
                            <label for="pickupTime" class="field-label">Pickup Time</label>
                            <div class=" clockpicker input-with-icon" data-placement="bottom" data-align="right" data-autoclose="true">
                               <i class="bi bi-clock input-icon"></i> <input type="text" id="pickupTime" class="form-control-custom" value="05:05" placeholder="HH:MM">
                            </div>
                        </div>

                        <div class="col-md-2 form-field-group" id="returnDateContainer" style="display: none;">
                            <label for="returnDate" class="field-label">Return Date</label>
                            <div class="input-with-icon">
                                <i class="bi bi-calendar input-icon"></i>
                                <input type="text" id="returnDate" class="form-control-custom date-picker" placeholder="DD/MM/YYYY" value="06/11/2025">
                            </div>
                        </div>

                        <div class="col-md-2 form-field-group d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn btn-search">
                                <i class="bi bi-search me-1"></i> SEARCH
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <div class="container-fluid bg-light pt-3 d-none" id="resultsPageContainer">
        <div class="container">
            <div class="results-header-bar rounded mb-3">
                <div class="row align-items-center px-5">
                    <div class="col-md-8">
                        <div class="results-summary" id="resultsSearchSummary">
                            One Way • Chennai → Madurai • 30/10/2025
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="date-navigation">
                            <button class="date-nav-btn"><i class="bi bi-chevron-left"></i></button>
                            <span class="date-range">Fri, 31 Oct</span>
                            <button class="date-nav-btn"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="date-tabs-container">
                <!-- Date tabs will be dynamically generated here -->
            </div>

            <div class="row">
                <div class="col-lg-8" id="mainCarResults">
                    <!-- Car cards will be dynamically generated here -->
                </div>
                
                <div class="col-lg-4">
                    <div class="route-box">
                        <div class="route-box-header">
                            Route Overview: Chennai to Madurai
                        </div>
                        <div class="route-details">
                            <div><strong>Distance:</strong> 450 km</div>
                            <div><strong>Duration:</strong> 8-9 hours</div>
                            <div><strong>Route:</strong> NH38</div>
                        </div>
                        <button class="btn-view-route" onclick="window.open('https://maps.google.com/maps?saddr=Chennai&daddr=Madurai', '_blank')">
                            <i class="bi bi-map me-1"></i> VIEW ROUTE ON MAP
                        </button>
                    </div>
                    
                    <div class="route-box">
                        <div class="route-box-header">
                            Need Help?
                        </div>
                        <div class="route-details">
                            <div>Call us: <strong>+91 98765 43210</strong></div>
                            <div>Email: <strong>support@carrental.com</strong></div>
                        </div>
                        <button class="btn-view-route">
                            <i class="bi bi-headset me-1"></i> CONTACT SUPPORT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flexible Dates Modal -->
    <div class="modal fade" id="flexibleDatesModal" tabindex="-1" aria-labelledby="flexibleDatesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Flexible Dates & Car Prices Grid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="text-center mb-3">
                        <p class="text-muted">Prices shown are for <strong id="modalCarType">5 Seater</strong> cars.</p>
                    </div>

                    <div class="price-grid">
                        <div class="price-row price-row-header">
                            <div class="row-header">Drop Date</div>
                            <div class="date-header">Tue</div>
                            <div class="date-header">Wed</div>
                            <div class="date-header">Thu</div>
                            <div class="date-header">Fri</div>
                            <div class="date-header">Sat</div>
                            <div class="date-header">Sun</div>
                            <div class="date-header">Mon</div>
                        </div>
                        
                        <div class="price-row">
                            <div class="row-header">27 Oct</div>
                            <div class="price-cell na">N/A</div>
                            <div class="price-cell">₹3,200</div>
                            <div class="price-cell">₹3,450</div>
                            <div class="price-cell">₹3,700</div>
                            <div class="price-cell">₹4,100</div>
                            <div class="price-cell active cheapest">₹2,999</div>
                            <div class="price-cell">₹3,500</div>
                        </div>
                        
                        <div class="price-row">
                            <div class="row-header">28 Oct</div>
                            <div class="price-cell">₹3,150</div>
                            <div class="price-cell na">N/A</div>
                            <div class="price-cell">₹3,300</div>
                            <div class="price-cell">₹3,650</div>
                            <div class="price-cell">₹3,800</div>
                            <div class="price-cell">₹3,100</div>
                            <div class="price-cell">₹3,400</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <p class="lowest-price-note mb-0"><span class="lowest-price">₹2,999</span> is the lowest price found.</p>
                    <button class="btn btn-primary" data-bs-dismiss="modal" id="closeModalButton">Select & Search Again</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"
        integrity="sha512-Eak/29OTpb36LLo2r47IpVzPBLXnAMPAVypbSZiZ4Qkf8p/7S/XRG5xp7OKWPPYfJT6metI+IORkR5G8F900+g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        (function () {
            // ---------- DOM references ----------
            const tripTypeInput = document.getElementById('tripType');
            const oneWayTab = document.getElementById('oneWayTab');
            const returnTab = document.getElementById('returnTab');
            const returnDateContainer = document.getElementById('returnDateContainer');
            const carHireForm = document.getElementById('carHireForm');
            const resultsContainer = document.getElementById('resultsPageContainer');
            const resultsSummary = document.getElementById('resultsSearchSummary');
            const dateTabsContainer = document.querySelector('.date-tabs-container');
            const mainCarResults = document.getElementById('mainCarResults');

            // ---------- Initialize UI Components ----------
           document.addEventListener('DOMContentLoaded', function () {
    try {
        // Initialize Datepicker
        $('.date-picker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });
    } catch (e) {
        console.error('Datepicker initialization failed:', e);
    }

    try {
        // Initialize Clockpicker
        $('.clockpicker').each(function () {
            $(this).clockpicker({
                placement: 'bottom',
                align: 'right',
                autoclose: true,
                donetext: 'Done',
                twelvehour: false
            }).find('input').on('focus', function () {
                const popover = $('.clockpicker-popover');
                const inputOffset = $(this).offset();
                const inputHeight = $(this).outerHeight();

                // Dynamically position popover below input
                popover.css({
                    top: inputOffset.top + inputHeight + 5 + 'px',
                    left: inputOffset.left + 'px',
                    position: 'absolute'
                });
            });
        });
    } catch (e) {
        console.error('Clockpicker initialization failed:', e);
    }

    // Initialize trip type or any other UI logic
    try {
        updateTripFields();
    } catch (e) {
        console.warn('updateTripFields not found or failed:', e);
    }
});

            // ---------- Trip Type Handling ----------
            function updateTripFields() {
                const tripType = tripTypeInput.value;

                if (tripType === 'Round Trip') {
                    returnDateContainer.style.display = 'block';
                } else {
                    returnDateContainer.style.display = 'none';
                }
            }

            oneWayTab.addEventListener('click', function() {
                oneWayTab.classList.add('selected-tab');
                returnTab.classList.remove('selected-tab');
                tripTypeInput.value = 'One Way';
                updateTripFields();
            });

            returnTab.addEventListener('click', function() {
                returnTab.classList.add('selected-tab');
                oneWayTab.classList.remove('selected-tab');
                tripTypeInput.value = 'Round Trip';
                updateTripFields();
            });

            // ---------- Date Tabs Generation ----------
            function generateDateTabs() {
                const tabsData = [
                    { range: '2 Nov – 9 Nov', price: 7937, dropDate: '9 Nov', isCheapest: false, isActive: false },
                    { range: '3 Nov – 10 Nov', price: 6519, dropDate: '10 Nov', isCheapest: false, isActive: false },
                    { range: '4 Nov – 11 Nov', price: 7701, dropDate: '11 Nov', isCheapest: false, isActive: false },
                    { range: '5 Nov – 12 Nov', price: 6826, dropDate: '12 Nov', isCheapest: false, isActive: false },
                    { range: '6 Nov – 13 Nov', price: 6285, dropDate: '13 Nov', isCheapest: true, isActive: false },
                    { range: '7 Nov – 14 Nov', price: 6280, dropDate: '14 Nov', isCheapest: true, isActive: true },
                    { range: '8 Nov – 15 Nov', price: 6284, dropDate: '15 Nov', isCheapest: false, isActive: false },
                ];

                // Find the minimum price for cheapest highlighting
                const minPrice = Math.min(...tabsData.map(item => item.price));

                dateTabsContainer.innerHTML = '';

                tabsData.forEach(item => {
                    const isCheapest = item.price === minPrice;
                    const isActive = item.isActive;
                    
                    const tab = document.createElement('div');
                    tab.className = `date-tab ${isActive ? 'active' : ''} ${isCheapest ? 'cheapest' : ''}`;
                    tab.innerHTML = `
                        <div class="date-range-text">${item.range}</div>
                        <div class="price">₹${item.price.toLocaleString('en-IN')}</div>
                    `;
                    
                    tab.addEventListener('click', function() {
                        document.querySelectorAll('.date-tab').forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        // In a real app, you would update search results based on selected date
                    });
                    
                    dateTabsContainer.appendChild(tab);
                });

                // Add flexible dates button
                const flexibleBtn = document.createElement('div');
                flexibleBtn.className = 'flexible-dates-btn';
                flexibleBtn.innerHTML = `
                    <i class="bi bi-calendar-week" style="font-size: 18px; margin-bottom: 3px;"></i>
                    <div>Flexible Dates</div>
                `;
                flexibleBtn.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('flexibleDatesModal'));
                    modal.show();
                });
                
                dateTabsContainer.appendChild(flexibleBtn);
            }

            // ---------- Toggle Inclusions Details ----------
            function setupInclusionsToggle() {
                document.querySelectorAll('.inclusions-toggle').forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const details = this.nextElementSibling;
                        details.classList.toggle('show');
                        this.textContent = details.classList.contains('show') ? 
                            'Hide Inclusions & Exclusions ▲' : 'Inclusions & Exclusions ▼';
                    });
                });
            }

            // ---------- Car Results Generation ----------
            function renderCarResults() {
                const carData = [
                    { 
                        id: 1, 
                        title: 'MG ZS (NEW)', 
                        subtitle: 'Electric | 4 Seats • AC', 
                        image: "{{ asset('goride/img/car.webp') }}", 
                        features: [
                            { icon: 'bi-person', text: '4 Seats' },
                            { icon: 'bi-snow', text: 'AC' },
                            { icon: 'bi-lightning-charge', text: 'Electric' },
                            { icon: 'bi-fuel-pump', text: 'CNG' }
                        ],
                        details: [
                            '15+ years experienced driver',
                            'Exact model as shown',
                            'Free cancellation till 1 hr before ride'
                        ],
                        inclusions: [
                            'Night charges, Parking Charges, State Tax, Toll Charges & Driver Allowance included',
                            'Only One Pickup and Drop',
                            '148 Kms included. ₹25/Km will be charged beyond that',
                            'Waiting time upto 15 mins included. ₹3.00/min after that',
                            'Free Cancellation till 1 hr before ride'
                        ],
                        provider: { name: 'Zoomcar', logo: '' },
                        rating: '8.5',
                        reviews: '95,000+ reviews',
                        price: 3200,
                        note: 'Free cancellation'
                    },
                    { 
                        id: 2, 
                        title: 'Kia Seltos', 
                        subtitle: 'SUV | 5 Seats • AC', 
                        image: "{{ asset('goride/img/car.webp') }}", 
                        features: [
                            { icon: 'bi-person', text: '5 Seats' },
                            { icon: 'bi-snow', text: 'AC' },
                            { icon: 'bi-fuel-pump', text: 'Petrol' },
                            { icon: 'bi-gear', text: 'Automatic' }
                        ],
                        details: [
                            '10+ years experienced driver',
                            'Well-maintained vehicle',
                            'Free cancellation till 2 hrs before ride'
                        ],
                        inclusions: [
                            'All tolls and parking included',
                            'Multiple pickup points available',
                            '200 Kms included. ₹20/Km will be charged beyond that',
                            'Waiting time upto 30 mins included. ₹2.50/min after that',
                            'Free Cancellation till 2 hrs before ride'
                        ],
                        provider: { name: 'MyChoize', logo: '' },
                        rating: '9.0',
                        reviews: '1,13,953 reviews',
                        price: 3550,
                        note: 'Free cancellation'
                    },
                    { 
                        id: 3, 
                        title: 'Toyota Innova Crysta', 
                        subtitle: 'MUV | 7 Seats • AC', 
                        image: "{{ asset('goride/img/car.webp') }}", 
                        features: [
                            { icon: 'bi-person', text: '7 Seats' },
                            { icon: 'bi-snow', text: 'AC' },
                            { icon: 'bi-fuel-pump', text: 'Diesel' },
                            { icon: 'bi-gear', text: 'Manual' }
                        ],
                        details: [
                            '12+ years experienced driver',
                            'Spacious and comfortable',
                            'Free cancellation till 3 hrs before ride'
                        ],
                        inclusions: [
                            'Driver charges and tolls included',
                            'Flexible pickup locations',
                            '250 Kms included. ₹18/Km will be charged beyond that',
                            'Waiting time upto 45 mins included. ₹2.00/min after that',
                            'Free Cancellation till 3 hrs before ride'
                        ],
                        provider: { name: 'Revv', logo: '' },
                        rating: '7.9',
                        reviews: '75,000+ reviews',
                        price: 5200,
                        note: 'Free cancellation'
                    }
                ];

                mainCarResults.innerHTML = '';

                carData.forEach(car => {
                    const featuresHtml = car.features.map(feature => `
                        <div class="feature-item">
                            <i class="bi ${feature.icon} feature-icon"></i>
                            <span>${feature.text}</span>
                        </div>
                    `).join('');

                    const detailsHtml = car.details.map(detail => `
                        <div class="detail-item">• ${detail}</div>
                    `).join('');

                    const inclusionsHtml = car.inclusions.map(inclusion => `
                        <div class="detail-item">• ${inclusion}</div>
                    `).join('');

                    const cardHtml = `
                        <div class="car-card">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <div class="car-image-section">
                                        <img src="${car.image}" alt="${car.title}" class="car-image">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="car-info-section">
                                        <h5 class="car-title">${car.title}</h5>
                                        <p class="car-subtitle">${car.subtitle}</p>
                                        <div class="car-features">
                                            ${featuresHtml}
                                        </div>
                                        <div class="car-details">
                                            ${detailsHtml}
                                        </div>
                                        <div class="inclusions-toggle">Inclusions & Exclusions ▼</div>
                                        <div class="inclusions-details">
                                            ${inclusionsHtml}
                                        </div>
                                        <div class="car-provider">
                                            <span class="rating">${car.rating}</span>
                                            <span>${car.provider.name}</span>
                                            <span class="text-muted">• ${car.reviews}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="car-price-section">
                                        <div class="price-container">
                                            <div class="total-price">₹${car.price.toLocaleString('en-IN')}</div>
                                            <div class="price-note">${car.note}</div>
                                        </div>
                                        <button class="btn-select">SELECT CAB</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    mainCarResults.insertAdjacentHTML('beforeend', cardHtml);
                });

                // Setup inclusions toggle after rendering
                setTimeout(setupInclusionsToggle, 100);
            }

            // ---------- Form Submission ----------
            carHireForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const from = document.getElementById('from').value.trim();
                const to = document.getElementById('to').value.trim();
                const tripType = tripTypeInput.value;
                const pickupDate = document.getElementById('pickupDate').value;
                const returnDate = tripType === 'Round Trip' ? document.getElementById('returnDate').value : '';

                if (!from || !to) {
                    alert('Please enter both Pickup and Drop locations.');
                    return;
                }

                // Update results summary
                let summary = `${tripType} • ${from} → ${to} • ${pickupDate}`;
                if (tripType === 'Round Trip') {
                    summary += ` to ${returnDate}`;
                }
                resultsSummary.textContent = summary;

                // Show results and generate content
                resultsContainer.classList.remove('d-none');
                window.scrollTo({ top: resultsContainer.offsetTop - 100, behavior: 'smooth' });

                generateDateTabs();
                renderCarResults();
            });

            // ---------- Initialize the UI ----------
            document.addEventListener('DOMContentLoaded', function() {
                initializeUI();
            });
        })();
    </script>
@endsection