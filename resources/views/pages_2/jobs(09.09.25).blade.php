@extends('layouts.app')
@section('css')
<link href="{{ asset('/goride/css/packageHis.css') }}" rel="stylesheet">
<style>

.neo-toggle-container {
    --toggle-width: 70px;
    --toggle-height: 26px;
    --toggle-bg: #181c20;
    --toggle-off-color: #ffffff;
    --toggle-on-color: #f8be00;
    --toggle-transition: 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    position: relative;
    display: inline-flex;
    flex-direction: column;
    font-family: "Segoe UI", Tahoma, sans-serif;
    user-select: none;
}

.neo-toggle-input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.neo-toggle {
  position: relative;
  width: var(--toggle-width);
  height: var(--toggle-height);
  display: block;
  cursor: pointer;
  transform: translateZ(0);
  perspective: 500px;
}

/* Track styles */
.neo-track {
  position: absolute;
  inset: 0;
  border-radius: calc(var(--toggle-height) / 2);
  overflow: hidden;
  transform-style: preserve-3d;
  transform: translateZ(-1px);
  transition: transform var(--toggle-transition);
  box-shadow:
    0 2px 10px rgba(0, 0, 0, 0.5),
    inset 0 0 0 1px rgba(255, 255, 255, 0.1);
}

.neo-background-layer {
  position: absolute;
  inset: 0;
  background: var(--toggle-bg);
  background-image: linear-gradient(
    -45deg,
    rgba(20, 20, 20, 0.8) 0%,
    rgba(30, 30, 30, 0.3) 50%,
    rgba(20, 20, 20, 0.8) 100%
  );
  opacity: 1;
  transition: all var(--toggle-transition);
}

.neo-grid-layer {
  position: absolute;
  inset: 0;
  background-image: linear-gradient(
      to right,
      rgba(71, 80, 87, 0.05) 1px,
      transparent 1px
    ),
    linear-gradient(to bottom, rgba(71, 80, 87, 0.05) 1px, transparent 1px);
  background-size: 5px 5px;
  opacity: 0;
  transition: opacity var(--toggle-transition);
}

.neo-track-highlight {
  position: absolute;
  inset: 1px;
  border-radius: calc(var(--toggle-height) / 2);
  background: linear-gradient(90deg, transparent, rgba(54, 249, 199, 0));
  opacity: 0;
  transition: all var(--toggle-transition);
}

/* Spectrum analyzer */
.neo-spectrum-analyzer {
  position: absolute;
  bottom: 6px;
  right: 10px;
  height: 10px;
  display: flex;
  align-items: flex-end;
  gap: 2px;
  opacity: 0;
  transition: opacity var(--toggle-transition);
}

.neo-spectrum-bar {
  width: 2px;
  height: 3px;
  background-color: var(--toggle-on-color);
  opacity: 0.8;
}

/* Thumb styles */
.neo-thumb {
    position: absolute;
   top: 3px;
    left: 4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    transform-style: preserve-3d;
    transition: transform var(--toggle-transition);
    z-index: 1;
}

.neo-thumb-ring {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: var(--toggle-off-color);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  transition: all var(--toggle-transition);
}

.neo-thumb-core {
  position: absolute;
  inset: 5px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
  transition: all var(--toggle-transition);
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.neo-thumb-icon {
  position: relative;
  width: 10px;
  height: 10px;
  transition: all var(--toggle-transition);
}

.neo-thumb-wave {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 10px;
  height: 2px;
  background: var(--toggle-off-color);
  transform: translate(-50%, -50%);
  transition: all var(--toggle-transition);
}

.neo-thumb-pulse {
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 1px solid var(--toggle-off-color);
  transform: scale(0);
  opacity: 0;
  transition: all var(--toggle-transition);
}

/* Gesture area */
.neo-gesture-area {
  position: absolute;
  inset: -10px;
  z-index: 0;
}

/* Interaction feedback */
.neo-interaction-feedback {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 0;
}

.neo-ripple {
  position: absolute;
  top: 50%;
  left: 30%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: radial-gradient(
    circle,
    var(--toggle-on-color) 0%,
    transparent 70%
  );
  transform: translate(-50%, -50%);
  opacity: 0;
  transition: all 0.4s ease-out;
}

.neo-progress-arc {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 2px solid transparent;
  border-top-color: var(--toggle-on-color);
  transform: translate(-50%, -50%) scale(0) rotate(0deg);
  opacity: 0;
  transition:
    opacity 0.3s ease,
    transform 0.5s ease;
}

/* Status indicator */
.neo-status {
  position: absolute;
  bottom: -30px;
  left: 0;
  width: 200%;
  display: flex;
  justify-content: start;
}

.neo-status-indicator {
  display: flex;
  align-items: center;
  gap: 4px;
}

.neo-status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #000;
  transition: all var(--toggle-transition);
}

.neo-status-text {
  font-family: 'Outfit', sans-serif;
  font-size: 12px;
  font-weight: 600;
  color: #000;
  letter-spacing: 1px;
  transition: all var(--toggle-transition);
}

/* Value display */
.neo-value-display {
  position: absolute;
  top: -22px;
  right: 0;
  font-size: 12px;
  font-weight: 500;
  color: var(--toggle-off-color);
  opacity: 0;
  transform: translateY(5px);
  transition: all var(--toggle-transition);
}

.neo-value-text {
  transition: all var(--toggle-transition);
}

/* Active states */

/* ON state */
.neo-toggle-input:checked + .neo-toggle .neo-thumb {
  transform: translateX(calc(var(--toggle-width) - 38px));
}

.neo-toggle-input:checked + .neo-toggle .neo-thumb-ring {
  background-color: var(--toggle-on-color);
}

.neo-toggle-input:checked + .neo-toggle .neo-thumb-wave {
  height: 8px;
  width: 8px;
  border-radius: 50%;
  background: transparent;
  border: 1px solid #fff;
}

.neo-toggle-input:checked + .neo-toggle .neo-thumb-pulse {
  transform: scale(1.2);
  opacity: 0.3;
  animation: neo-pulse 1.5s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-track-highlight {
  background: linear-gradient(90deg, transparent, rgba(54, 249, 199, 0.2));
  opacity: 1;
}

.neo-toggle-input:checked + .neo-toggle .neo-grid-layer {
  opacity: 1;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-analyzer {
  opacity: 1;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-bar:nth-child(1) {
  animation: neo-spectrum 0.9s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-bar:nth-child(2) {
  animation: neo-spectrum 0.8s 0.1s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-bar:nth-child(3) {
  animation: neo-spectrum 1.1s 0.2s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-bar:nth-child(4) {
  animation: neo-spectrum 0.7s 0.1s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-spectrum-bar:nth-child(5) {
  animation: neo-spectrum 0.9s 0.15s infinite;
}

.neo-toggle-input:checked + .neo-toggle .neo-status-dot {
  background-color: var(--toggle-on-color);
  box-shadow: 0 0 8px var(--toggle-on-color);
}

.neo-toggle-input:checked + .neo-toggle .neo-status-text {
     color: #3a65de;;
  content: "ACTIVE";
}

.neo-toggle-input:checked + .neo-toggle + .neo-value-display {
  opacity: 1;
  transform: translateY(0);
}

.neo-toggle-input:checked + .neo-toggle + .neo-value-display .neo-value-text {
  color: var(--toggle-on-color);
}

/* Hover effects */
.neo-toggle:hover .neo-thumb-ring {
  transform: scale(1.05);
}

.neo-toggle-input:not(:checked) + .neo-toggle:hover .neo-thumb-wave::before,
.neo-toggle-input:not(:checked) + .neo-toggle:hover .neo-thumb-wave::after {
  opacity: 1;
}

/* Drag gesture handling */
.neo-toggle.neo-dragging .neo-track {
  transform: translateZ(-1px) scale(1.02);
}

.neo-toggle.neo-dragging .neo-thumb {
  transition: none;
}

/* Animations */
@keyframes neo-pulse {
  0% {
    transform: scale(1);
    opacity: 0.5;
  }
  50% {
    transform: scale(1.5);
    opacity: 0.2;
  }
  100% {
    transform: scale(1);
    opacity: 0.5;
  }
}

@keyframes neo-spectrum {
  0% {
    height: 3px;
  }
  50% {
    height: 8px;
  }
  100% {
    height: 3px;
  }
}

/* Custom script to enable advance features */
.neo-toggle.neo-activated .neo-ripple {
  width: 100px;
  height: 100px;
  opacity: 0.5;
  transition: all 0.6s ease-out;
}

.neo-toggle.neo-progress .neo-progress-arc {
  opacity: 0.8;
  transform: translate(-50%, -50%) scale(1) rotate(270deg);
  transition:
    opacity 0.3s ease,
    transform 1s ease;
}

/* Status text change */
.neo-toggle-input:checked + .neo-toggle .neo-status-text::before {
  content: "P2P";
}

.neo-toggle-input:not(:checked) + .neo-toggle .neo-status-text::before {
  content: "INCLUDED";
}

    .daterangepicker .drp-buttons {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 6px 10px !important;
        border-top: 1px solid #eee !important;
    }

    .daterangepicker .drp-buttons .drp-selected {
        font-size: 13px !important;
        color: #333 !important;
    }

    .daterangepicker .drp-buttons .btn {
        margin-left: 6px !important;
        font-size: 13px !important;
        padding: 4px 12px !important;
        border-radius: 4px !important;
    }


    .drp-buttons .btn {
        margin-left: 6px;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 4px;
    }

    .daterangepicker .ranges li {
        list-style: none;
        padding: 6px 8px;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 4px;
        font-weight: 500;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .daterangepicker .ranges li:hover {
        background: #ffe680;
        /* softer yellow hover */
        color: #000;
    }

    .daterangepicker .ranges li.active {
        background: #f8be00 !important;
        /* active stays solid yellow */
        color: #000 !important;
    }

    /* Calendar hover + selection */
    .daterangepicker .calendar-table td {
        transition: background 0.2s ease, color 0.2s ease;
    }

    .daterangepicker .calendar-table td.available:hover {
        background: #ffe680 !important;
        /* stable hover */
        color: #000 !important;
    }

    .daterangepicker .calendar-table td.in-range {
        background: #f8be00 !important;
        /* full range stays visible */
        color: #000 !important;
    }

    .daterangepicker .calendar-table td.start-date,
    .daterangepicker .calendar-table td.end-date {
        background: #000 !important;
        /* black for edges */
        color: #fff !important;
    }

    .cancel-link {
        background: none;
        border: none;
        color: #dc3545;
        /* Bootstrap danger red */
        font-size: 12px;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
    }

    .cancel-link:hover {
        color: #a71d2a;
        /* Darker red on hover */
    }

    .custom-date {
        background-color: #f8be00;
        color: #000;
        border: none;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 6px;
        outline: none;
        /* Removes blue outline */
        transition: all 0.2s ease-in-out;
    }

    .custom-date:focus {
        background-color: white;
        color: black;
        border: 2px solid #f8be00;
    }

    .custom-date::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }


    .custom-btn {
        background-color: #f8be00 !important;
        /* Yellow */
        color: #000 !important;
        /* Black text */
        border: none !important;
        border-radius: 6px;
    }

    .custom-dropdown {
        background-color: #d7d7d7;
        border: none;
    }

    .custom-dropdown .dropdown-item {
        color: #695205;
        font-weight: 500;
    }

    .custom-dropdown .dropdown-item:hover {
        background-color: white;
        color: #000;
    }

    .dropdown-toggle::after {
        display: none;
    }

    .custom-dropdown .dropdown-header {
        color: black;
        font-weight: bold;
        border-bottom: 1px solid #f8be00;
    }

    .image-slider .item {
        text-align: center;
        height: 227px;
        width: 365px;
        /* Fixed width */
        margin: auto;
    }

    .image-slider img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        border-radius: 12px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        /* Light grey */
        border-top: 4px solid #f9bf00;
        /* Blue */
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .tooltip-inner {
        max-width: 420px !important;
        background-color: white !important;
        color: #333 !important;
        text-align: left;
        padding: 15px;
        border-radius: 10px;
        font-size: 14px;
        /* same everywhere */
        line-height: 1.6;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.35);
        white-space: normal;
        border: 1px solid #ddd;
        z-index: 9999 !important;
        opacity: 1 !important;
    }

    .tooltip.show {
        opacity: 1 !important;
    }

    .bs-tooltip-top .tooltip-arrow::before,
    .bs-tooltip-bottom .tooltip-arrow::before {
        border-top-color: #ffffff !important;
        border-bottom-color: #ffffff !important;
    }

    /* Tooltip content */
    .tooltip-box {
        font-size: 14px;
        /* uniform */
        color: #333;
    }

    .tooltip-box ul {
        list-style: none;
        padding-left: 0;
        margin: 8px 0;
    }

    .tooltip-box ul li {
        margin-bottom: 8px;
        font-size: 14px;
        line-height: 1.5;
        position: relative;
        padding-left: 20px;
        /* spacing for arrow */
    }

    .tooltip-box ul li::before {
        content: "➜";
        /* simple right arrow */
        position: absolute;
        left: 0;
        color: #333;
        /* same as text */
        font-size: 14px;
    }

    .create-btn {
        --stone-50: #fafaf9;
        --stone-800: #292524;
        --yellow-400: #f8be00;

        font-size: 14px;
        cursor: pointer;
        font-weight: bold;
        line-height: 1;
        padding: 0.6rem 1.2rem;
        border-radius: 9999px;
        background-color: var(--yellow-400);
        color: var(--stone-800);
        border: 2px solid rgba(255, 255, 255, 0.3);
        position: relative;
        overflow: hidden;
        transition: transform 150ms ease, box-shadow 150ms ease, background-color 200ms ease;

        display: inline-flex;
        align-items: center;
        gap: 6px;

        box-shadow:
            2px 2px 0 var(--stone-800),
            4px 4px 0 var(--stone-50);
    }


    /* active = stronger pressed */
    .create-btn:focus,
    .create-btn:active {
        transform: translate(3px, 3px);
        background-color: #e6ad00;
        /* deeper yellow when clicked */
        box-shadow:
            0 0 0 var(--stone-800),
            0 0 0 var(--stone-50);
    }

    /* icon animation */
    .create-btn i {
        transition: transform 0.3s ease;
    }

    .create-btn:hover i {
        transform: rotate(90deg);
    }

    .create-btn:active i {
        transform: rotate(180deg);
    }

    .create-btn:active {
        transform: translateY(2px);
        background-color: #e6ad00;
        /* slightly darker yellow when clicked */
    }

    /* icon rotation */
    .create-btn i {
        transition: transform 0.4s ease;
    }

    .create-btn:hover i {
        transform: rotate(90deg);
    }

    .create-btn:active i {
        transform: rotate(180deg);
    }

    @keyframes dots {
        0% {
            background-position: 0 0, 4px 4px;
        }

        100% {
            background-position: 8px 0, 12px 4px;
        }
    }



    .create-btn:hover {
        transform: translate(-2px, -2px);
        box-shadow: 0 0 0 2px #f8be00;
        transform: translate(-2px, -2px);
        color: black;
        background: #f8be00;
    }



    .create-btn:active {
        transform: translateY(2px);
    }

    /* icon rotation */
    .create-btn i {
        transition: transform 0.4s ease;
    }

    .create-btn:hover i {
        transform: rotate(90deg);
    }

    .create-btn:active i {
        transform: rotate(180deg);
    }

    @keyframes dots {
        0% {
            background-position: 0 0, 4px 4px;
        }

        100% {
            background-position: 8px 0, 12px 4px;
        }
    }



    .create-btn:active {
        transform: translateY(2px);
    }


    &:active,
    &:focus-visible {
        outline-color: var(--yellow-400);
    }

    &:focus-visible {
        outline-style: dashed;
    }

    &>div {
        position: relative;
        pointer-events: none;
        background-color: var(--yellow-400);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 9999px;

        &::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            opacity: 0.5;
            background-image: radial-gradient(rgb(255 255 255 / 80%) 20%,
                    transparent 20%),
                radial-gradient(rgb(255 255 255 / 100%) 20%, transparent 20%);
            background-position:
                0 0,
                4px 4px;
            background-size: 8px 8px;
            mix-blend-mode: hard-light;
            animation: dots 0.5s infinite linear;
        }

        &>span {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.25rem;
            gap: 0.25rem;
            filter: drop-shadow(0 -1px 0 rgba(255, 255, 255, 0.25));

            &:active {
                transform: translateY(2px);
            }
        }
    }
    }

    @keyframes dots {
        0% {
            background-position:
                0 0,
                4px 4px;
        }

        100% {
            background-position:
                8px 0,
                12px 4px;
        }
    }

    .driver-list {

        background: white;
        padding: 3px;
    }

    .estimated_fare {
        border: 1px dashed black;
        width: auto;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #f8be00;
        padding: 10px;
        border-radius: 5px;
    }

    .estimated_fare p {
        color: black;
    }

    button:focus,
    button:active {
        outline: none !important;
        box-shadow: none !important;
    }

    #pre_from4 {
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 264px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #pre_from4.expanded {
        max-width: 264px;
        max-height: none;
        -webkit-line-clamp: unset;
        /* Remove line limit */
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    #pre_to4 {
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 264px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #pre_to4.expanded {
        max-width: 264px;
        max-height: none;
        -webkit-line-clamp: unset;
        /* Remove line limit */
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    #pre_from3 {
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 264px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #pre_from3.expanded {
        max-width: 264px;
        max-height: none;
        -webkit-line-clamp: unset;
        /* Remove line limit */
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    #pre_to3 {
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 264px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #pre_to3.expanded {
        max-width: 264px;
        max-height: none;
        -webkit-line-clamp: unset;
        /* Remove line limit */
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    .arrow-stack {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }


    .arrow-container.double-arrows .car-icon {
        position: static;
        transform: none;
        margin: 0 10px;
    }

    .arrow-container.double-arrows .arrow-stack {
        display: flex;
    }

    .bottom-arrow {
        height: 2px;
        background: linear-gradient(to right, #007bff, #0056b3);
        width: 195px;
        position: relative;

    }

    .bottom-arrow::after {
        content: "";
        position: absolute;
        left: -6px;
        top: -3px;
        width: 0;
        height: 0;
        border-right: 8px solid #0056b3;
        border-top: 4px solid transparent;
        border-bottom: 4px solid transparent;
    }

    .top-arrow {
        height: 2px;
        background: linear-gradient(to right, #007bff, #0056b3);
        width: 195px;
        position: relative;
    }

    .top-arrow::after {
        content: "";
        position: absolute;
        right: -6px;
        top: -3px;
        width: 0;
        height: 0;
        border-left: 8px solid #0056b3;
        border-top: 4px solid transparent;
        border-bottom: 4px solid transparent;
    }

    #current .oneway-label {
        position: absolute;
        top: 55px;
        left: 31%;
        transform: translateX(-50%);
        /* background: #fff; */
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #007bff;
        /* border-radius: 12px; */
        /* border: 1px solid #ddd; */
    }

    #openJobs_cards .oneway-label {
        position: absolute;
        top: 40px;
        left: 51%;
        transform: translateX(-50%);
        /* background: #fff; */
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #007bff;
        /* border-radius: 12px; */
        /* border: 1px solid #ddd; */
    }

    .past-job .oneway-label {
        position: absolute;
        top: 75px;
        left: 51%;
        transform: translateX(-50%);
        /* background: #fff; */
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #007bff;
        /* border-radius: 12px; */
        /* border: 1px solid #ddd; */
    }

    .liked-jobs .oneway-label {
        position: absolute;
        top: 59px;
        left: 52%;
        transform: translateX(-50%);
        /* background: #fff; */
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #007bff;
        /* border-radius: 12px; */
        /* border: 1px solid #ddd; */
    }

    .bidding-jobs .oneway-label {
        position: absolute;
        top: 81px;
        left: 51%;
        transform: translateX(-50%);
        /* background: #fff; */
        padding: 0 10px;
        font-size: 12px;
        font-weight: 600;
        color: #007bff;
        /* border-radius: 12px; */
        /* border: 1px solid #ddd; */
    }

    .bot-chat {
        display: none;
    }

    .calendly-badge-widget {
        display: none;
    }

    .price {
        font-size: 21px;
    }

    .page-wrapper {

        margin-left: 270px;
        /*height: 100vh;*/
        overflow-y: auto;
        padding: 10px;
    }

    .job-empty-body button {
        min-width: 160px;
        /* keeps it neat even as small btn */
    }

    .job-list-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* Empty job card */
    .job-empty-card {
        background: #f9fafb;
        border: 1px dashed #d6d8db;
        border-radius: 10px;
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .job-empty-card:hover {
        background: #f1f3f5;
    }

    .job-empty-body i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #adb5bd;
    }

    .job-empty-body p {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .select2-container {
        border-radius: 5px;
        line-height: 1.5em;
        border: 1px solid #ced4da;
    }

    .view-btn:focus {
        background: #f8be00;
        ;
        color: black;
    }

    .view-btn:hover {
        background: #f8be00;
        ;
        color: black;
    }

    input,
    textarea,
    select {
        border: 1px solid #ced4da !important;

    }

    #mainLocationInput {
        border: 1px solid #ffc107;
        /* Bootstrap warning yellow */
        border-radius: 4px;
        /* Optional rounded corners */
    }

    #profile-bar-box .dropdown-item:hover {
        background-color: #f9bf00;
        color: #000;
    }

    #profile-bar-box .dropdown-item i {
        margin-right: 8px;
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

    @media screen and (max-width: 576px) {
        #profile-bar img{
            width: 70px;
    height: 70px;
        }
        .neo-toggle-container{
            --toggle-width: 70px;
            --toggle-height: 24px;
        }
        .neo-thumb{
             top: 4px;
        left: 4px;
        width: 17px;
        height: 16px;
        }
        #profile-bar-box {
            left: -65px !important;
            top: 91px !important;
        }

        .close-button {
            font-size: 12px;
        }

        #openJobs_cards .oneway-label {
            position: absolute;
            top: -12px;
            /* sits on the border */
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            /* same background as card */
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            color: #007bff;
            /* primary color */
            border-radius: 12px;
            border: 1px solid #ddd;
            /* optional border */
        }

        #current .oneway-label {
            position: absolute;
            top: -12px;
            /* sits on the border */
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            /* same background as card */
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            color: #007bff;
            /* primary color */
            border-radius: 12px;
            border: 1px solid #ddd;
            /* optional border */
        }

        .past-job .oneway-label {
            position: absolute;
            top: -12px;
            /* sits on the border */
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            /* same background as card */
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            color: #007bff;
            /* primary color */
            border-radius: 12px;
            border: 1px solid #ddd;
            /* optional border */
        }

        .liked-jobs .oneway-label {
            position: absolute;
            top: -12px;
            /* sits on the border */
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            /* same background as card */
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            color: #007bff;
            /* primary color */
            border-radius: 12px;
            border: 1px solid #ddd;
            /* optional border */
        }

        .bidding-jobs .oneway-label {
            position: absolute;
            top: -12px;
            /* sits on the border */
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            /* same background as card */
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            color: #007bff;
            /* primary color */
            border-radius: 12px;
            border: 1px solid #ddd;
            /* optional border */
        }

        .driver-info {
            font-size: 13px;
        }

        .amount-bid {
            font-size: 13px;
            padding: 0px 6px !important;
        }

        .bid-actions .accept-btn {
            font-size: 13px;
            padding: 7px 8px !important;
        }

        .mobile_menu_toggler h1 {
            font-size: 18px !important;
        }
    }

    .dropdown-item.active {
        background-color: #f0ad4e;
        /* Bootstrap warning color */
        color: white;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        display: block !important;
        padding-left: 8px !important;
        padding-right: 20px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;


    }

    .select2-selection__clear {
        display: none !important;
    }

    .select2-container .select2-selection--single {
        display: flex !important;
        height: 35px !important;
        align-items: center;

    }

    .selection {
        width: 100%;
        display: inline-block;
    }

    .select2-container .select2-search__field {
        width: 100% !important;
        min-width: 100px;
        box-sizing: border-box;
    }

    select.form-control {
        white-space: normal;
        /* allows text to wrap */
        line-height: 1.2em;
        /* tighter line spacing if wrapping */
    }



    #profile-bar img {
        position: relative;
        margin-left: 10px;
    }

    .rounded {
        width: 80px;
        margin-top: -13px;
        border-radius: 7px !important;
    }


    #profile-bar-box {
        left: -41px;
        top: 65px;

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

    @media (max-width: 767.98px) {
        #actionSection .btn {
            font-size: 14px;
            padding: 6px 10px;
        }

        .view-btn {
            font-size: 13px !important;
            padding: 5px !important;
        }

        #createJobModal .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        #jobPreviewModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #carModal2 .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #acceptBidModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        #rejectConfirmModal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .card-header-tabs.nav-tabs .nav-link {
            font-size: 13px !important;
            padding: 11px;
        }

        .ribbon span {
            padding: 0px;
            font-size: 9px;
        }

        .tab-content.card-body {
            padding: 0px !important;
        }

        .past-job .job-card .card-body {
            padding: 0px !important;
        }

        .bidding-jobs .job-card .card-body {
            padding: 0px !important;
        }

        .offcanvas-start {
            width: 70% !important;
        }

        .offcanvas-body {
            padding: 1rem;
            overflow-y: hidden;
            max-height: calc(100vh - 56px);
            /* Adjust if header height differs */
        }

        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .form-select,
        .form-control,
        .form-range {
            font-size: 0.9rem;
        }

        /*.btn-sm {*/
        /*    font-size: 0.8rem;*/
        /*    padding: 0.4rem 0.75rem;*/
        /*}*/

        .input-group-text {
            font-size: 0.8rem;
        }

        /*.card-body {*/
        /*    padding: 1rem !important;*/
        /*}*/
        .amount-bid {
            padding: 2px;
        }
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
        padding: 0 8px;
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


    /*--------------*/

    .card {
        position: relative;
        overflow: visible;
        z-index: 1;
    }

    .custom-flag-popup {
        position: absolute;
        top: 100%;
        /* Show below the flag */
        left: 0;
        z-index: 1000;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: max-content;
        white-space: nowrap;
    }

    .custom-flag-popup button {
        display: block;
        width: 100%;
        margin-bottom: 5px;
        padding: 8px 12px;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
        font-size: 14px;
    }

    .custom-flag-popup button:hover {
        background-color: #f5f5f5;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-in {
        animation: slideIn 0.2s ease-out;
    }


    /*-----------*/
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
        border-radius: 7px !important;
    }

    .bg-secondary-subtle {
        background: linear-gradient(to right, rgb(249 189 26) 0, #fff79b 100%);
    }

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
        font-weight: normal;
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
        right: 10px;
        overflow: hidden;
        margin-right: 20px;
    }

    .profile-greeting .greeting-user .btn:hover {
        background-color: #f8be00 !important;
        color: black;
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

    .compact-car-card {
        margin-bottom: 12px;
    }

    .compact-car-card .card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 0px;
    }

    .compact-car-card .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .compact-car-card .card-body {
        padding: 10px !important;
        border-left: 4px solid #f8be00;
        border-radius: 6px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        /* margin-bottom: 15px; */
        transition: transform 0.2s ease-in-out;
    }


    /* Company Info */
    .company-info {
        text-align: left;
    }

    .company-name {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 2px;
    }

    .car-model {
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
    }

    /*.car-specs {*/
    /*  display: flex;*/
    /*  flex-direction: column;*/
    /*  gap: 3px;*/
    /*}*/

    .passenger-count,
    .distance {
        font-size: 15px;
        color: #555;
        font-weight: 500;
        /*display: flex;*/
        /*align-items: center;*/
        gap: 4px;
    }

    /*.passenger-count i {*/
    /*  color: #28a745;*/
    /*  font-size: 9px;*/
    /*}*/

    .distance i {
        color: #0058f8;
        font-size: 9px;
    }

    /* Trip Info */
    .route-section {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        width: 100%;
    }

    .location-time {
        text-align: center;
        flex: 0 0 auto;
    }

    .date-time {
        font-size: 15px;
        font-weight: 600;
        color: #704400;
        margin-bottom: 2px;
    }

    .date-time span {
        font-size: 14px;
        color: #2d64c2 !important;
    }

    #openJobs_cards .location {
        font-size: 16px;
        color: #484141;
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 240px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #openJobs_cards .location.expanded {
        max-width: 249px;
        max-height: none;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    .past-job .location {
        font-size: 16px;
        color: #484141;
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 249px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    .past-job .location.expanded {
        max-width: 249px;
        max-height: none;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    .liked-jobs .location {
        font-size: 16px;
        color: #484141;
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 249px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    .liked-jobs .location.expanded {
        max-width: 249px;
        max-height: none;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    .bidding-jobs .location {
        font-size: 16px;
        color: #484141;
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 2 lines */
        -webkit-box-orient: vertical;
        /* Required for multi-line clamp */
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 249px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    .bidding-jobs .location.expanded {
        max-width: 249px;
        max-height: none;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    #current .location {
        font-size: 16px;
        color: #484141;
        max-height: 54px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        cursor: pointer;
        max-width: 172px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        text-align: left;
    }

    #current .location.expanded {
        max-width: 172px;
        max-height: none;
        -webkit-line-clamp: unset;
        -webkit-box-orient: unset;
        white-space: normal;
        text-overflow: clip;
    }

    /* Route Arrow */
    .route-arrow {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 20px;
    }

    .trip-type {
        font-size: 10px;
        color: #007bff;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
        background: #e3f2fd;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .arrow-line {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .duration {
        font-size: 15px;
        color: #666;
        margin-bottom: 15px;
        font-weight: 500;
        max-width: 114px;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
    }

    .arrow-container {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        justify-content: center;
    }

    /*.long-arrow {*/
    /*    height: 2px;*/
    /*    background: linear-gradient(to right, #007bff, #0056b3);*/
    /*    width: 195px;*/
    /*    position: relative;*/
    /*}*/

    /*.long-arrow::after {*/
    /*    content: "";*/
    /*    position: absolute;*/
    /*    right: -6px;*/
    /*    top: -3px;*/
    /*    width: 0;*/
    /*    height: 0;*/
    /*    border-left: 8px solid #0056b3;*/
    /*    border-top: 4px solid transparent;*/
    /*    border-bottom: 4px solid transparent;*/
    /*}*/

    .car-icon {
        position: absolute;
        /* right: 29px; */
        color: #704a0f !important;
        font-size: 25px;
        background: white;
        padding: 7px;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* Round Trip Arrow */
    .arrow-container.round-trip {
        flex-direction: column;
        gap: 2px;
    }

    .text-form {
        font-size: 13px !important;
    }

    .return-arrow {
        height: 2px;
        background: linear-gradient(to left, #28a745, #1e7e34);
        width: 80px;
        position: relative;
    }

    .return-arrow::after {
        content: "";
        position: absolute;
        left: -6px;
        top: -3px;
        width: 0;
        height: 0;
        border-right: 8px solid #1e7e34;
        border-top: 4px solid transparent;
        border-bottom: 4px solid transparent;
    }

    .round-trip .car-icon {
        position: static;
        margin: 2px 0;
        color: #28a745;
    }

    /* Price and Actions */
    .price-action-section {
        text-align: center;
        /*display: flex;*/
        /*flex-direction: column;*/
        /*gap: 8px;*/
    }

    .bid-count {
        font-size: 15px;
        color: #ff6b35;
        font-weight: 600;
    }

    .bids-c i {
        color: #ff6b35;
    }

    .bid-count i {
        margin-right: 4px;
        font-size: 10px;
    }

    .amount {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .charges {
        display: flex;
        /*justify-content: flex-end;*/
        gap: 8px;
        font-size: 13px;
        color: #666;
    }

    .charges span {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .charges i {
        font-size: 9px;
    }

    .view-details-btn {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
        width: 95px;
        background: #608ecfbd;
    }




    .trip-stats {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 6px;
        font-size: 14px;
    }

    .stat-item i {
        font-size: 16px;
    }

    .view-btn {
        background: #f8be00;
    }

    .go-ride-search .search-input {
        border: 2px solid #e9ecef;
        border-radius: 12px 0 0 12px;
        padding: 10px 20px;
        font-size: 15px;
        border-right: none;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        transition: all 0.3s ease;
    }

    .go-ride-search .search-input:focus {
        border-color: #FFC107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        background: #ffffff;
        outline: none;
    }

    .go-ride-search .search-btn {
        border: 2px solid #e9ecef;
        border-left: none;
        border-radius: 0 12px 12px 0;
        background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
        color: #ffffff;
        padding: 14px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
        height: 100%;
    }

    .go-ride-search .search-btn:hover {
        background: linear-gradient(135deg, #FFB300 0%, #FF8F00 100%);
        border-color: #FFC107;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .search-dropdown {
        border: none;
        border-radius: 0 0 12px 12px;
        max-height: 280px;
        overflow-y: auto;
        /*border-top: 2px solid #FFC107;*/
    }

    .popular-searches-title {
        color: #6c757d;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .go-ride-tag {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #495057;
        border: 1px solid #dee2e6;
        padding: 8px 14px;
        font-size: 13px;
        cursor: pointer;
        border-radius: 20px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .go-ride-tag:hover {
        background: linear-gradient(135deg, #FFC107 0%, #FFB300 100%);
        color: #ffffff;
        border-color: #FFC107;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .go-ride-tag i {
        color: #6c757d;
        transition: color 0.3s ease;
    }

    .go-ride-tag:hover i {
        color: #ffffff;
    }

    /* Scrollbar styling for dropdown */
    .search-dropdown::-webkit-scrollbar {
        width: 6px;
    }

    .search-dropdown::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb {
        background: #FFC107;
        border-radius: 3px;
    }

    .search-dropdown::-webkit-scrollbar-thumb:hover {
        background: #FFB300;
    }

    .placebid-btn {
        background: linear-gradient(135deg, #f7971e, #ffd200);
        position: relative;
        bottom: -10px;
        left: 22px;
        border-radius: 29px 2px 2px;
        padding: 9px;
        width: 104px;
        text-decoration: underline;
    }

    .placebid-btn:hover {
        color: black;
        background: linear-gradient(135deg, #c96b00, #cc9a00);
    }

    .icon-color {
        color: goldenrod;
    }
    }


    @media (max-width: 768px) {
        /*.route-section {*/
        /*  flex-direction: column;*/
        /*  gap: 10px;*/
        /*}*/



        .route-arrow {
            margin: 10px 0;
        }

        .long-arrow {
            width: 60px;
        }

        .return-arrow {
            width: 60px;
        }

        .arrow-container.round-trip .car-icon {
            transform: rotate(90deg);
        }

        /*.charges {*/
        /*  flex-direction: column;*/
        /*  gap: 4px;*/
        /*}*/

        .price-action-section {
            align-items: center;
            margin-top: 10px;
        }

        /*.car-specs {*/
        /*  flex-direction: row;*/
        /*  gap: 8px;*/
        /*  justify-content: space-between;*/
        /*}*/
    }

    /* Animation */
    .compact-car-card .card {
        animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Hover effects */
    /*.view-details-btn:hover {*/
    /*  transform: translateY(-1px);*/
    /*  box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);*/
    /*}*/

    .bid-count {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }

        100% {
            opacity: 1;
        }
    }

    /* Compact spacing */
    .row {
        margin: 0;
    }

    .col-2,
    .col-4,
    .col-6 {
        padding-left: 8px;
        padding-right: 8px;
    }

    /* Clean borders */
    .card {
        border-width: 1px;
        border-style: solid;
        border-color: #dee2e6;
    }

    /* Typography improvements */
    .company-name,
    .car-model,
    .date-time,
    .location,
    .amount {
        line-height: 1.2;
    }

    /* Icon improvements */
    .charges i {
        color: #d42f21;
        font-size: 13px;
    }

    .bid-count i {
        color: #ff6b35;
    }

    /* Car specs styling */
    /*.car-specs {*/
    /*  border-top: 1px solid #f0f0f0;*/
    /*  padding-top: 6px;*/
    /*  margin-top: 4px;*/
    /*}*/

    /* Enhanced hover effects for car specs */
    /*.passenger-count:hover,*/
    /*.distance:hover {*/
    /*  background: #f8f9fa;*/
    /*  padding: 2px 4px;*/
    /*  border-radius: 4px;*/
    /*  transition: all 0.2s ease;*/
    /*}*/

    /* Improved modal stats */
    .trip-stats .stat-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
        transition: all 0.3s ease;
    }


    .passenger-count i {
        color: #1e7e34;
        font-size: 15px;
    }

    .distance i {
        color: #0058f8;
        font-size: 15px;
    }

    /* Special styling for 7+ seater vehicles */
    .compact-car-card:has(.passenger-count:contains("7")) .card {
        border-left: 4px solid #ffc107;
    }

    /* Distance-based color coding */
    .distance:has(i):contains("450km") i {
        color: #dc3545;
        /* Red for long distances */
    }

    .distance:has(i):contains("280km") i {
        color: #fd7e14;
        /* Orange for medium distances */
    }

    .distance:has(i):contains("120km") i,
    .distance:has(i):contains("180km") i {
        color: #28a745;
        /* Green for short distances */
    }

    @media screen and (max-width: 576px) {
        #left_sidebar {
            display: none;
            height: 100vh;
        }

        .past-job {
            width: 100% !important;
        }

        #agreedModal .modal-dialog {
            margin: 0.5rem;
            max-width: 100%;
        }

        #agreedModal .modal-content {
            padding: 1rem !important;
        }

        #agreedModal h3 {
            font-size: 1.4rem;
        }

        #agreedModal .modal-footer .btn {
            /*width: 100%;*/
            margin-bottom: 0.5rem;
        }

        #agreedModal .modal-footer {
            flex-direction: row;
            gap: 0.5rem;
        }

        .ribbon span {
            padding: 0px !important;
            font-size: 10px !important;
            top: 16px !important;
        }

        .bidding-jobs .job-card .card-body {
            padding: 0px !important;
        }

        .bidding-jobs {
            width: 100% !important;
        }

        .bid-actions btn {
            font-size: 12px;
        }

        /*.amount-bid{*/
        /*    padding:2px !important;*/
        /*}*/
        .title-mobile {
            font-size: 18px;
        }

        .location-time {
            flex: none;
        }

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

        .right-info {
            max-width: 300px;
        }

        .return-info {
            display: block;
            padding: 0px 26px;
        }

        .job-details {
            display: block;
        }

        .details-row {}

        #bidAmountInput {
            max-width: 150px;
        }

        .compact-car-card {
            margin-bottom: 12px;
            /* width: 310px; */
            /*margin-left: -23px;*/
        }






        .charges {
            gap: 30px;
        }

        .passenger-count,
        .distance,
        .bids-c,
        .amount-des,
        .amount {
            font-size: 14px;
        }

        .date-time {
            font-size: 11px;
        }

        .location {
            font-size: 10px;
            max-width: 94px;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            text-align: left;
        }

        .route-arrow {
            margin: 0px;
        }

        .long-arrow {
            width: 41px;
        }

        .car-icon {
            font-size: 13px;
            padding: 4px;
        }

        .text-form {
            font-size: 12px !important;
            /*max-width: 92px;*/
            line-height: 2.0;
            /* display: flex
; */
            align-items: center;
            justify-content: cente
        }

        #openJobs_cards .location {
            font-size: 14px;
            max-width: 120px;
            max-height: 54px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            /* Required for multi-line clamp */
            text-overflow: ellipsis;
            cursor: pointer;
        }

        #openJobs_cards .location.expanded {
            max-width: 120px;
            max-height: none;
            -webkit-line-clamp: unset;
            /* Remove line limit */
            -webkit-box-orient: unset;
            white-space: normal;
            text-overflow: clip;
        }

        .past-job .location {
            font-size: 14px;
            max-width: 120px;
            max-height: 54px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            /* Required for multi-line clamp */
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .past-job .location.expanded {
            max-width: 120px;
            max-height: none;
            -webkit-line-clamp: unset;
            /* Remove line limit */
            -webkit-box-orient: unset;
            white-space: normal;
            text-overflow: clip;
        }

        .liked-jobs .location {
            font-size: 14px;
            max-width: 120px;
            max-height: 54px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            /* Required for multi-line clamp */
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .liked-jobs .location.expanded {
            max-width: 120px;
            max-height: none;
            -webkit-line-clamp: unset;
            /* Remove line limit */
            -webkit-box-orient: unset;
            white-space: normal;
            text-overflow: clip;
        }

        .bidding-jobs .location.expanded {
            max-width: 120px;
            max-height: none;
            -webkit-line-clamp: unset;
            /* Remove line limit */
            -webkit-box-orient: unset;
            white-space: normal;
            text-overflow: clip;
        }

        .bidding-jobs .location {
            font-size: 14px;
            max-width: 120px;
            max-height: 54px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            /* Required for multi-line clamp */
            text-overflow: ellipsis;
            cursor: pointer;
        }

        #current .location {
            font-size: 14px;
            max-width: 130px;
            max-height: 54px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Limit to 2 lines */
            -webkit-box-orient: vertical;
            /* Required for multi-line clamp */
            text-overflow: ellipsis;
        }

        #current .location.expanded {
            max-width: 130px;
            max-height: none;
            -webkit-line-clamp: unset;
            /* Remove line limit */
            -webkit-box-orient: unset;
            white-space: normal;
            text-overflow: clip;
        }

        .placebid-btn {
            bottom: -10px;
            left: 209px;
            border-radius: 29px 2px 2px;
            padding: 6px;
            width: 77px;
            font-size: 11px;
        }

        .icon-report i {
            width: 22px !important;
            height: 22px !important;
            padding: 5px !important;
            font-size: 10px !important;
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

    td {
        font-size: 13px;
    }

    /* Default (unliked) style */
    .icon-report i {
        font-size: 16px;
        /*border: 1px solid #FF4B2B;*/
        border-radius: 50%;
        padding: 8px;
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        animation: beat 1.2s infinite ease-in-out;
        /*box-shadow: 0 4px 12px rgba(255, 75, 43, 0.4);*/
        color: #817777;
        background: transparent;
        transition: all 0.4s ease;
    }

    @keyframes rotateHourglass {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .hourglass-rotate {
        display: inline-block;
        animation: rotateHourglass 2s linear infinite;
    }

    .report-flag i {
        font-size: 13px;
        border: 1px solid #FF4B2B;
        border-radius: 50%;
        padding: 8px;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        animation: beat 1.2s infinite ease-in-out;
        box-shadow: 0 4px 12px rgba(255, 75, 43, 0.4);
        color: #FF4B2B;
        background: transparent;
        transition: all 0.4s ease;
    }

    /* After liked */
    .icon-report.liked i {
        color: #FF4B2B;
        /*background: linear-gradient(135deg, #FF416C, #FF4B2B);*/
        transform: scale(1.2);
    }

    /* Heart beat animation */
    @keyframes beat {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    /* Custom styles for range input and checkboxes to match theme */
    .custom-range-warning {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        background: #ddd;
        border-radius: 5px;
        outline: none;
        opacity: 0.7;
        transition: opacity .2s;
    }

    .custom-range-warning:hover {
        opacity: 1;
    }

    .custom-range-warning::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: #ffc107;
        /* Warning color */
        border: 2px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 0 2px #ffc107;
        /* Outer ring */
    }

    .custom-range-warning::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: #ffc107;
        /* Warning color */
        border: 2px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 0 0 2px #ffc107;
        /* Outer ring */
    }

    .custom-checkbox-warning:checked {
        background-color: #ffc107;
        /* Warning color */
        border-color: #ffc107;
        /* Warning color */
    }

    .custom-checkbox-warning:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        /* Warning color with transparency */
    }

    .filter {
        position: sticky;
        top: 0;
        max-height: 90vh;
        /* Ensures it doesn't exceed viewport */
        overflow-y: auto;
        /* Enables scrolling inside the filter */
        background-color: #fff;
        /* Important to prevent content from showing underneath */
        z-index: 10;
        /* Make sure it stays above other content */
        padding: 1rem;
    }

    /*.driver-card {*/
    /*    background: #ffffff;*/
    /*    border-radius: 10px;*/
    /*    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);*/
    /*    padding: 20px;*/
    /*    margin-bottom: 20px;*/
    /*    position: relative;*/
    /*}*/

    /*.driver-header {*/
    /*    font-weight: 600;*/
    /*    font-size: 18px;*/
    /*    color: #111;*/
    /*    margin-bottom: 10px;*/
    /*}*/

    /*.bid-section {*/
    /*    display: flex;*/
    /*    justify-content: space-between;*/
    /*    align-items: center;*/
    /*    margin-bottom: 10px;*/
    /*    gap: 10px;*/
    /*}*/

    /*input[type="number"] {*/
    /*    padding: 10px;*/
    /*    font-size: 14px;*/
    /*    border: 1px solid #ccc;*/
    /*    border-radius: 6px;*/
    /*    width: 120px;*/
    /*}*/

    /*.dropdown {*/
    /*    position: relative;*/
    /*    display: inline-block;*/
    /*}*/

    /*.dots-btn {*/
    /*    background: #F1F3F6;*/
    /*    border: none;*/
    /*    border-radius: 6px;*/
    /*    padding: 10px;*/
    /*    font-size: 20px;*/
    /*    cursor: pointer;*/
    /*    color: #333;*/
    /*}*/

    /*.dropdown-menu {*/
    /*    display: none;*/
    /*    position: absolute;*/
    /*    right: 0;*/
    /*    background-color: white;*/
    /*    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);*/
    /*    border-radius: 8px;*/
    /*    overflow: hidden;*/
    /*    z-index: 999;*/
    /*    min-width: 170px;*/
    /*}*/

    /*.dropdown-menu button {*/
    /*    width: 100%;*/
    /*    padding: 12px 15px;*/
    /*    border: none;*/
    /*    background: none;*/
    /*    text-align: left;*/
    /*    font-size: 14px;*/
    /*    cursor: pointer;*/
    /*    transition: background 0.2s;*/
    /*    font-weight: 500;*/
    /*}*/

    /*.dropdown-menu button:hover {*/
    /*    background-color: #f5f5f5;*/
    /*}*/

    /*.dropdown-menu .accept {*/
    /*    color: #0aaf4b;*/
    /*}*/

    /*.dropdown-menu .reject {*/
    /*    color: #e63946;*/
    /*}*/

    /*.dropdown-menu .spam {*/
    /*    color: #f9a825;*/
    /*}*/

    /*.remarks-container {*/
    /*    margin-top: 15px;*/
    /*}*/

    /*.remarks-input {*/
    /*    width: 100%;*/
    /*    padding: 10px;*/
    /*    border-radius: 6px;*/
    /*    font-size: 14px;*/
    /*    border: 1px solid #ccc;*/
    /*    resize: vertical;*/
    /*    min-height: 60px;*/
    /*    max-height: 150px;*/
    /*    overflow-y: auto;*/
    /*    display: none;*/
    /*}*/

    /*.remarks-preview {*/
    /*    font-size: 14px;*/
    /*    color: #444;*/
    /*    max-height: 50px;*/
    /*    overflow: hidden;*/
    /*    transition: max-height 0.3s ease;*/
    /*}*/

    /*.remarks-preview.expanded {*/
    /*    max-height: 200px;*/
    /*}*/

    .read-toggle {
        color: #f9a825;
        cursor: pointer;
        font-weight: 500;
        margin-top: 5px;
        display: inline-block;
    }

    .edit-toggle {
        color: #007bff;
        cursor: pointer;
        margin-left: 15px;
        font-size: 13px;
    }

    .status-ribbon {
        position: absolute;
        top: 21px;
        right: -38px;
        width: 133px;
        padding: 0px 0;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transform: rotate(45deg);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        z-index: 10;
        background: green;
        color: white;
    }

    .job-card {
        /*background: #ffffff;*/
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        padding: 0px;
        border-left: 5px solid #f1c40f;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .ribbon {
        position: absolute;
        top: 0;
        left: 0;
        width: 100px;
        height: 100px;
        overflow: hidden;
        z-index: 2;
    }


    .ribbon::before,
    .ribbon::after {
        position: absolute;
        content: '';
        display: block;
        5px solid #f8be00 border-top-color: transparent;
        border-left-color: transparent;
        /* Changed from right to left */
    }


    .ribbon::before {
        top: 0;
        right: 0;
        /* Flipped corner for top-right */
    }

    .ribbon::after {
        bottom: 0;
        left: 0;
        /* Flipped corner for bottom-left */
    }

    .ribbon span {
        position: absolute;
        display: block;
        width: 140px;
        padding: 8px 0;
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        transform: rotate(-45deg);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        top: 11px;
        left: -36px;
    }

    .ribbon span.completed {
        background-color: #27ae60;
        /* Green */
    }

    .ribbon span.expired {
        background-color: #7f8c8d;
        /* Gray */
    }

    .ribbon span.no-response {
        background-color: #f38c00;
        /* Red */
    }

    .ribbon span.cancelled {
        background-color: #c0392b;
        /* Dark Red */
    }

    .rejected-badge {
        background-color: #e74c3c;
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        margin-left: auto;
    }

    .rejected-badge i {
        font-size: 0.9rem;
    }

    .job-stats {
        display: flex;
        align-items: center;
        gap: 25px;
        font-size: 1rem;
        font-weight: 500;
        color: #2c3e50;
    }

    .job-stats .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .job-stats .stat-item i {
        color: #3498db;
        font-size: 1.2rem;
    }

    .bid-card {
        position: relative;
        transition: all 0.3s ease;
        /* background: linear-gradient(135deg, #f9fafb, #f3f4f6); */
        border-radius: 16px;
        /*margin-bottom: 14px;*/
        /* box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05); */
        /* transition: transform 0.3s ease; */
    }

    /*    .bid-card:hover {*/
    /*  transform: scale(1.02);*/
    /*}*/
    .bid-card-row {
        display: flex;
        justify-content: center;
        /*align-items: center;*/
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .bid-post-accept {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .icon-circle {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        text-decoration: none;
        /*transition: 0.3s;*/
        font-size: 10px;
    }

    .icon-circle:hover {
        opacity: 0.85;
        text-decoration: none;
        display: inline-flex;
    }

    .icon-circle i {}


    /*.agreeBtn {*/
    /*    background: linear-gradient(to right, #16a34a, #22c55e);*/
    /*    border: none;*/
    /*    color: white;*/
    /*    padding: 8px 18px;*/
    /*    font-weight: 600;*/
    /*    border-radius: 24px;*/
    /*    transition: background 0.3s ease;*/
    /*}*/

    /*.agreeBtn:hover {*/
    /*    background: linear-gradient(to right, #15803d, #16a34a);*/
    /*}*/

    .bid-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .bid-actions i {
        /*font-size: 1.25rem;*/

        cursor: pointer;
        transition: color 0.3s ease;
    }

    /*  .bid-actions i:hover {*/
    /*    color: #f8be00;*/
    /*}*/
    .bid-actions .info-icon {
        color: #344ce5;
    }

    .glassy-remarks {
        left: 41px;
        position: relative;
        background: rgba(173, 216, 230, 0.6);
        color: #333;
        font-size: 14px;
        line-height: 1.4;
        padding: 3px 15px;
        border-radius: 25px;
        max-width: 280px;
        /* adjust to fit within box */
        width: fit-content;
        margin-top: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
    }


    /*.glassy-remarks::after {*/
    /*  content: "";*/
    /*  position: absolute;*/
    /*  bottom: -6px;*/
    /*  left: 50%;*/
    /*  transform: translateX(-50%);*/
    /*  border-width: 6px;*/
    /*  border-style: solid;*/
    /*  border-color: #e6f3fb transparent transparent transparent;*/
    /*}*/

    .stylish-flags {
        position: relative;

        animation: fadeIn 0.2s ease;
    }

    .stylish-flags button {
        background: none;
        border: none;
        font-size: 0.9rem;
        color: #475569;
        padding: 4px 0;
        display: block;
        transition: color 0.2s ease;
    }

    .stylish-flags button:hover {
        color: #ef4444;
    }

    .driver-info {
        display: flex;
        flex-direction: row;
        justify-content: center;
        gap: 6px;
    }

    .bid-card.disabled {
        opacity: 0.6;
        pointer-events: none;
    }

    .driver-header {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 15px;
    }

    .driver-name {

        font-weight: 700;
        color: #1e293b;
    }

    .flag-icon {
        color: #dc0000;
        cursor: pointer;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        padding: 0.6rem;
        border-radius: 50%;
        width: 2.2rem;
        height: 2.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .flag-icon:hover {
        /*background-color: rgba(255, 71, 87, 0.1);*/
        color: #ff4757;
        transform: rotate(360deg);
    }

    .scrollable-bid-list {
        max-height: 142px;
        overflow-y: auto;
        padding-right: 8px;
        /*background: white;*/
        /* padding: 0px; */
        border-radius: 8px;
        padding-top: 7px;
    }

    .amount-bid {
        font-weight: bold;
        color: #333;
        /*background: #fef8e8;*/
        padding: 0px 6px;
        /*border-radius: 8px;*/
        /*border: 1px solid #f8be00;*/
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .remarks-container {
        /*display: flex;*/
        /*justify-content: space-between;*/
        align-items: flex-start;
        /*gap: 15px;*/
        margin-bottom: 5px;
    }

    .remarks {
        display: flex;
        gap: 10px;
        background-color: #f9f9f9;
        padding: 12px;
        border-radius: 8px;
        color: #555;
        /*font-size: 0.95rem;*/
        line-height: 1.4;
        flex: 1;
    }

    .remark-icon {
        color: #f8be00;
        margin-top: 5px;
    }

    .remark-text {
        flex: 1;
    }

    .action-btns {
        display: flex;
        justify-content: center;
        gap: 10px;
        align-items: center;
    }

    .btn {
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    /*.custom-accept-btn {*/
    /*    background-color: #f8be00;*/
    /*    color: #333;*/
    /*}*/

    /*.custom-accept-btn:hover {*/
    /*    background-color: #e6b000;*/
    /*    transform: translateY(-1px);*/
    /*    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);*/
    /*}*/

    .custom-reject-btn {
        background-color: #f0f0f0;
        color: #666;
    }

    .custom-reject-btn:hover {
        background-color: #e0e0e0;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Communication Icons Animation */
    .communication-icons {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        align-items: center;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #e0e0e0;
    }

    .communication-icons.show-communication {
        opacity: 1;
        transform: translateY(0);
    }

    .icon-buttons {
        display: flex;
        gap: 5px;
    }

    .icon-btn {
        background: white;
        border: 1px solid #e0e0e0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #555;
        transition: all 0.2s;
        cursor: pointer;
    }

    .icon-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .icon-btn:nth-child(1) {
        color: #25D366;
        /* WhatsApp green */
        border-color: #c8e6c9;
    }

    .icon-btn:nth-child(2) {
        color: #2196F3;
        /* Phone blue */
        border-color: #bbdefb;
    }

    .btn-reject {
        background-color: white;
        border: 1px solid #ffcdd2;
        color: #f44336;
        padding: 8px 20px;
        border-radius: 20px;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-reject:hover {
        background-color: #f44336;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3);
    }

    /* Disabled Cards */
    .disabled-card {
        filter: grayscale(80%);
        opacity: 0.6;
        pointer-events: none;
        transition: all 0.3s ease;
    }



    /* Main Buttons Transition */
    .main-buttons {
        transition: all 0.3s ease;
    }
    }

    .flag-options {
        display: none;
        position: absolute;
        right: 1.2rem;
        top: 4rem;
        background: white;
        border: 1px solid #dfe4ea;
        border-radius: 12px;
        padding: 0.6rem 0;
        z-index: 100;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 200px;
        overflow: hidden;
        animation: fadeIn 0.2s ease-out;
    }

    .flag-options button {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        width: 100%;
        padding: 0.8rem 1.2rem;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
        color: #2f3542;
    }

    .flag-options button:hover {
        background-color: #f1f2f6;
        color: #ff4757;
    }

    .flag-options button i {
        width: 1.2rem;
        text-align: center;
        transition: transform 0.2s;
    }

    .flag-options button:hover i {
        transform: scale(1.2);
    }


    .flag-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-weight: 600;
        color: #333;
    }

    .close-flag {
        cursor: pointer;
        color: #888;
    }

    .close-flag:hover {
        color: #f8be00;
    }

    .flag-options label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        margin-bottom: 10px;
        padding: 8px;
        border-radius: 5px;
        cursor: pointer;
    }

    .flag-options label:hover {
        background-color: #fef8e8;
    }

    .custom-report-btn {
        width: 100%;
        padding: 8px;
        background-color: #f8be00;
        color: #333;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        margin-top: 5px;
        cursor: pointer;
    }

    .custom-report-btn:hover {
        background-color: #e6b000;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .chosen-driver {
        border: 2px solid #28a745;
        box-shadow: 0 0 12px rgba(40, 167, 69, 0.3);
    }

    /* Small non-overlapping badge under name */
    .chosen-badge {
        display: inline-block;
        margin-top: 4px;
        background: #28a745;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }

    .chosen-indicator {
        font-size: 0.8rem;
        color: #28a745;
    }

    .remarks-toggle {
        display: flex;
        align-items: center;
        color: #747d8c;
        font-size: 0.9rem;
        cursor: pointer;
        margin: 1rem 0;
        transition: color 0.2s;
        font-weight: 500;
    }

    .remarks-toggle:hover {
        color: #f8be00;
    }

    /*.remarks-content {*/
    /*    position: absolute;*/
    /*margin: 0px;*/
    /*top: 20px;*/
    /*    display: none;*/
    /*    font-size: 0.9rem;*/
    /*    color: #57606f;*/
    /*    padding: 1rem;*/
    /*    background: #f8f9fa;*/
    /*    border-radius: 12px;*/
    /*    margin-top: 0.8rem;*/
    /*    line-height: 1.6;*/
    /*    border-left: 4px solid #f8be00;*/
    /*    animation: fadeIn 0.3s ease;*/
    /*}*/

    /*.remarks-content strong {*/
    /*    color: #2f3542;*/
    /*}*/

    /* Disabled Card Style */
    .disabled-card {
        opacity: 0.6 !important;
        pointer-events: none !important;
        filter: grayscale(70%);
        background-color: #f9f9f9 !important;
        border-color: #eee !important;
    }

    /* Accepted Card Style (modified) */
    /*.bid-card.accepted {*/
    /*    background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);*/
    /*    border: 1px solid #4CAF50;*/
    /*    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);*/
    /*    transform: translateY(-3px);*/
    /*    padding: 20px;*/
    /*    z-index: 10;*/
    /*}*/

    /* Remove chosen badge styles */
    .chosen-badge,
    .chosen-indicator {
        display: none !important;
    }

    /* Reject Button Animation */
    .btn-reject {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-reject:hover::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.2);
        animation: ripple 0.6s linear;
        border-radius: 20px;
    }

    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }

        100% {
            transform: scale(2);
            opacity: 0;
        }
    }


    /* Flag Options Positioning */
    .flag-options {
        position: absolute;
        right: 12px;
        top: 20px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        display: none;
    }

    .flag-options button {
        display: block;
        width: 100%;
        padding: 8px 15px;
        text-align: left;
        background: none;
        border: none;
        color: #333;
        white-space: nowrap;
    }

    .flag-options button:hover {
        background-color: #f5f5f5;
    }

    .flag-options button i {
        margin-right: 8px;
        width: 18px;
        text-align: center;
    }

    .sort-dropdown .dropdown-toggle {
        border-radius: 10px;
        border: 1px solid #ccc;
        background: #f8be00;
        padding: 2px 8px;
        font-weight: 500;
    }

    .sort-dropdown .dropdown-menu {
        min-width: 180px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    .sort-dropdown .dropdown-item {
        padding: 4px 11px;
        transition: background-color 0.2s ease;
        color: #000;
    }

    /* Hover */
    .sort-dropdown .dropdown-item:hover {
        background-color: #fbd84a;
        /* lighter yellow */
    }

    /* Active */
    .sort-dropdown .dropdown-item.active {
        background-color: #f8be00;
        font-weight: 600;
    }

    /* Initially hide all bid-card containers (JS will show first few) */
    .bid-card-container {
        display: none;
        transition: all 0.3s ease;
    }

    /* Optional: Add some spacing between cards if not already handled */
    .bid-card-container {
        margin-bottom: 20px;
    }

    .disabled-bid {
        opacity: 0.5;
        pointer-events: none;
    }



    /* Style the Load More button */
    #loadMoreBtn {
        background-color: #f8be00;
        color: #000;
        font-weight: 600;
        border-radius: 8px;
        /*padding: 10px 30px;*/
        border: none;
        transition: background-color 0.2s ease;
    }

    #loadMoreBtn:hover {
        background-color: #fbd84a;
    }


    /* Animate in from the right */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Slide-in with blur + fade */
    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
            filter: blur(4px);
        }

        to {
            transform: translateX(0);
            opacity: 1;
            filter: blur(0);
        }
    }

    /* Glassy toast style */
    .glassy-toast {
        background: rgba(40, 167, 69, 0.75);
        /* Semi-transparent success green */
        backdrop-filter: blur(8px);
        border-radius: 16px;
        animation: toastSlideIn 0.5s ease-out;
        font-size: 0.95rem;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Icon animation */
    .animate-icon {
        animation: iconPop 0.6s ease-in-out;
    }

    /* Pulse animation for icon */
    @keyframes iconPop {
        0% {
            transform: scale(0.6);
            opacity: 0;
        }

        60% {
            transform: scale(1.2);
            opacity: 1;
        }

        100% {
            transform: scale(1);
        }
    }

    /* Optional: glowing effect */
    .glassy-toast .toast-body i {
        color: #fff;
        text-shadow: 0 0 6px rgba(255, 255, 255, 0.8);
    }


    .custom-report-dropdown {
        min-width: 180px;
    }

    .custom-report-dropdown .dropdown-item {
        font-size: 14px;
        padding: 8px 15px;
    }

    .custom-report-dropdown .dropdown-item:hover {
        background-color: #ffe6e6;
    }

    /*#current .route-section {*/
    /*    border-right: 2px solid #888;*/
    /*}*/

    #bidAmountDetails ul {
        text-align: start;
        list-style: disc;
        margin: 0;
    }

    #bidAmountDetails ul li {
        list-style: disc;
    }

    #agreedModal .form-check-input {
        width: 1.5em;
        height: 1.5em;
        border: 1px solid #5d87ff !important
    }
</style>

@endsection
@section('content')
@php

$planList = null;
$userToken = $_COOKIE['sessionToken'] ?? '';
if ($userToken != '') {
$response = Http::withToken($userToken)->post(url('/api/packageHistory'), [
'countryCode' => $_COOKIE['countryCode']
]);
} else {
$response = Http::post(url('/api/packageHistory'), [
'countryCode' => $_COOKIE['countryCode']
]);
}
if ($response->successful()) {
$authUser = $response->json();
if (isset($authUser['status']) && $authUser['status'] === 'success') {
$planList = $authUser['data']['packageHis'] ?? null;
}
}

$i = 1;
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
        <div class="row mb-3 d-none">
            <div class="col-12 p-0">
                <div class="card ">
                    <div class="card-body">
                        <div class="owl-carousel image-slider">

                            <!-- Slide 1 -->
                            <div class="item">
                                <img src="https://www.goride.net.in/goride/img/dashboard-welcome.svg" alt="Slide 1">
                            </div>

                            <!-- Slide 2 -->
                            <div class="item">
                                <img src="https://www.goride.net.in/goride/img/dashboard-welcome.svg" alt="Slide 1">
                            </div>

                            <!-- Slide 3 -->
                            <div class="item">
                                <img src="https://www.goride.net.in/goride/img/dashboard-welcome.svg" alt="Slide 1">
                            </div>
                            <div class="item">
                                <img src="https://www.goride.net.in/goride/img/dashboard-welcome.svg" alt="Slide 1">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 px-0">
                <div class="card px-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <ul class="nav nav-tabs card-header-tabs w-100 flex-wrap flex-sm-nowrap" id="jobTabs"
                            role="tablist">

                            <!-- Mobile: Create Job button on top -->
                            <li class="nav-item w-100 d-sm-none mb-2" role="presentation">
                                <button
                                    class="btn btn-sm view-btn w-100 d-flex justify-content-center align-items-center gap-1"
                                    style="font-size: 14px;" data-bs-toggle="modal" data-bs-target="#createJobModal">
                                    <i class="fa-solid fa-square-plus"></i> Create Job
                                </button>
                            </li>

                            <!-- Mobile: Pills style tabs -->
                            <li class="nav-item d-sm-none flex-fill text-center" role="presentation">
                                <button
                                    class="nav-link rounded-pill text-center py-2 d-flex align-items-center justify-content-center gap-2"
                                    id="available-tab" data-bs-toggle="tab" data-bs-target="#available" type="button"
                                    role="tab" aria-selected="false">
                                    <img src="{{ asset('goride/img/flag.png') }}" alt="goride_ads"
                                        style="height: 20px;width: 20px;">
                                    Open Jobs
                                </button>
                            </li>

                            <li class="nav-item d-sm-none flex-fill" role="presentation">
                                <button class="nav-link rounded-pill text-center py-2" id="created-tab"
                                    data-bs-toggle="tab" data-bs-target="#created" type="button" role="tab"
                                    aria-selected="false">
                                    My Jobs
                                </button>
                            </li>
                            <li class="nav-item d-sm-none flex-fill" role="presentation">
                                <button class="nav-link rounded-pill text-center py-2" id="bidding-tab"
                                    data-bs-toggle="tab" data-bs-target="#bidding" type="button" role="tab"
                                    aria-selected="false">
                                    Bidding Jobs
                                </button>
                            </li>

                            <li class="nav-item d-sm-none flex-fill" role="presentation">
                                <button class="nav-link rounded-pill text-center py-2" id="liked-tab"
                                    data-bs-toggle="tab" data-bs-target="#liked" type="button" role="tab"
                                    aria-selected="false">
                                    Liked Jobs <i class="fa fa-heart text-danger"></i>
                                </button>
                            </li>
                            <li class="nav-item d-sm-none d-flex align-items-center" role="presentation">

                                <button class="btn btn-warning py-1" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#mobileFilterOffcanvas">
                                    <i class="fas fa-filter me-2"></i>Filters
                                </button>

                            </li>

                            <!-- Desktop view (unchanged) -->
                            <li class="nav-item flex-shrink-0 d-none d-sm-block" role="presentation">
                                <button class="nav-link d-flex align-items-center gap-2" id="available-tab-desktop"
                                    data-bs-toggle="tab" data-bs-target="#available" type="button" role="tab"
                                    aria-selected="false">
                                    <img src="{{ asset('goride/img/flag.png') }}" alt="goride_ads"
                                        style="height:20px;width:20px;">
                                    Open Jobs
                                </button>
                            </li>

                            <li class="nav-item flex-shrink-0 d-none d-sm-block" role="presentation">
                                <button class="nav-link" id="created-tab-desktop" data-bs-toggle="tab"
                                    data-bs-target="#created" type="button" role="tab" aria-selected="false">
                                    My Jobs
                                </button>
                            </li>
                            <li class="nav-item flex-shrink-0 d-none d-sm-block" role="presentation">
                                <button class="nav-link" id="bidding-tab-desktop" data-bs-toggle="tab"
                                    data-bs-target="#bidding" type="button" role="tab" aria-selected="false">
                                    Bidding Jobs
                                </button>
                            </li>

                            <li class="nav-item ms-auto d-none d-sm-block me-3" role="presentation">
                                <button class="nav-link p-2 border-0 bg-transparent icon-report" id="liked-tab-desktop"
                                    data-bs-toggle="tab" data-bs-target="#liked" type="button" role="tab"
                                    aria-selected="false">
                                    <i class="fa fa-heart text-danger"></i> Liked Jobs
                                </button>
                            </li>
                            <li class="nav-item d-none d-sm-block" role="presentation">
                                <button class="btn btn-sm create-btn d-flex align-items-center gap-1"
                                    style="font-size: 14px;" data-bs-toggle="modal" data-bs-target="#createJobModal">
                                    <i class="fa-solid fa-square-plus"></i> Create Job
                                </button>
                            </li>
                        </ul>
                    </div>




                    <div class="card-body tab-content" id="jobTabsContent">
                        <div class="tab-pane fade show active" id="available" role="tabpanel"
                            aria-labelledby="#available-tab">
                            <!-- Search Bar -->
                            <!--                        <div class="row mb-4 mt-3">-->
                            <!--                            <div class="col-12">-->
                            <!--                                <div class="search-container position-relative">-->
                            <!--                                    <div class="input-group go-ride-search d-none">-->
                            <!--                                        <input type="text" class="form-control search-input"-->
                            <!--                                            placeholder="Search jobs by route, distance, passengers..."-->
                            <!--                                            id="jobSearchInput" autocomplete="off">-->
                            <!--                                        <button class="btn search-btn" type="button">-->
                            <!--                                            <i class="fas fa-search"></i>-->
                            <!--                                        </button>-->
                            <!--                                    </div>-->
                            <!-- Search Suggestions Dropdown -->
                            <!--                                    <div class="search-dropdown position-absolute w-100 bg-white shadow-lg"-->
                            <!--                                        id="searchDropdown" style="display: none; top: 100%; z-index: 1000;">-->
                            <!--                                        <div class="p-3">-->
                            <!--                                            <h6 class="popular-searches-title mb-3">Popular Searches</h6>-->
                            <!--                                            <div class="trending-tags">-->
                            <!--                                                <span class="badge go-ride-tag me-2 mb-2">-->
                            <!--                                                    <i class="fas fa-users me-1"></i>Passengers Count-->
                            <!--                                                </span>-->
                            <!--                                                <span class="badge go-ride-tag me-2 mb-2">-->
                            <!--                                                    <i class="fas fa-route me-1"></i>Distance-->
                            <!--                                                </span>-->
                            <!--                                                <span class="badge go-ride-tag me-2 mb-2">-->
                            <!--                                                    <i class="fas fa-map-marker-alt me-1"></i> Location-->
                            <!--                                                </span>-->
                            <!--                                                <span class="badge go-ride-tag me-2 mb-2">-->
                            <!--                                                   <i class="fa-solid fa-person-seat me-1 -->
                            <!--"></i> Seater-->
                            <!--                                                </span>-->

                            <!--                                                <span class="badge go-ride-tag me-2 mb-2">-->
                            <!--                                                    <i class="fas  fa-indian-rupee-sign  me-1"></i> Amount-->
                            <!--                                                </span>-->

                            <!--                                            </div>-->
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                            <!--                        </div>-->
                            <!-- Wrap with flex to push to the right -->
                            <div class="d-flex align-items-center justify-content-end gap-3 mb-3 mt-2 mt-md-0">

                                <!-- Departure Date Filter -->
                                <div class="d-flex align-items-center gap-2 flex-wrap">

                                    <input type="text" class="form-control form-control-sm mb-0 p-0"
                                        id="dateRangePicker" placeholder="  Select date range">
                                </div>


                                <!-- Sort Dropdown -->
                                <div class="dropdown">
                                    <button
                                        class="btn dropdown-toggle dropdown-toggle-no-caret fw-bold py-1 px-3 custom-btn"
                                        type="button" id="sortMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort By
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end custom-dropdown"
                                        aria-labelledby="sortMenu">

                                        <li>
                                            <h6 class="dropdown-header">Distance</h6>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Distance: Low → High', 'distance-asc', this)">
                                                Lowest to Highest
                                            </label>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Distance: High → Low', 'distance-desc', this)">
                                                Highest to Lowest
                                            </label>
                                        </li>

                                        <li>
                                            <h6 class="dropdown-header">Amount</h6>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Amount: Low → High', 'amount-asc', this)">
                                                Lowest to Highest
                                            </label>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Amount: High → Low', 'amount-desc', this)">
                                                Highest to Lowest
                                            </label>
                                        </li>

                                        <li>
                                            <h6 class="dropdown-header">Passengers</h6>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Passengers: Low → High', 'passenger-asc', this)">
                                                Lowest to Highest
                                            </label>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input me-2"
                                                    onclick="setSort('Passengers: High → Low', 'passenger-desc', this)">
                                                Highest to Lowest
                                            </label>
                                        </li>

                                    </ul>
                                </div>


                            </div>






                            <div class="row">
                                <!-- Filter Sidebar -->
                                <div class="col-md-3 col-12 mb-4 mb-md-0 d-none d-md-block">
                                    <div class="card filter shadow-sm border-0 rounded-3">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h5 class="card-title fw-bold mb-2 text-dark text-center">
                                                <i class="fas fa-filter text-warning me-2"></i>Filters
                                            </h5>
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-sm btn-link text-muted p-0 me-3"
                                                    onclick="resetFilter()" id="resetFiltersBtn">
                                                    <i class="fas fa-sync-alt me-1"></i>Reset
                                                </button>
                                                <!--<button class="btn btn-sm btn-link text-muted p-0" id="saveFiltersBtn">-->
                                                <!--    <i class="fas fa-save me-1"></i>Save-->
                                                <!--</button>-->
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <!-- Location Filter -->
                                            <div class="mb-4 position-relative">
                                                <label for="filterLocation"
                                                    class="form-label fw-bold small mb-2 text-dark">
                                                    <i class="fas fa-map-marker-alt text-warning me-2"></i>Location
                                                </label>

                                                <!-- Selected locations container -->
                                                <div id="selectedLocations" class="mb-2 d-flex flex-wrap gap-2"></div>

                                                <!-- Input + Dropdown -->
                                                <div class="position-relative">
                                                    <i class="fas fa-search position-absolute text-warning"
                                                        style="left: 10px; top: 50%; transform: translateY(-50%);"></i>
                                                    <!--<input type="text"-->
                                                    <!--    class="form-control form-control-sm border-warning location-input text-center mb-2 mainLocationInput"-->
                                                    <!--    placeholder="Search" autocomplete="off" id="mainLocationInput">-->
                                                    <div id="loc_d">
                                                        <!--<select id="location_input" -->
                                                        <!--        name="location_input" -->
                                                        <!--        class="form-select"-->
                                                        <!--        style="width: 100%;" -->
                                                        <!--        data-placeholder="Select District">-->
                                                        <!--    <option value=""></option>-->
                                                        <!--</select>-->
                                                    </div>

                                                    <!-- Dropdown -->
                                                    <div class="search-dropdown position-absolute w-100 bg-white shadow-lg"
                                                        id="locationDropdown"
                                                        style="display: none; top: 100%; z-index: 1000;">
                                                        <div class="p-3">
                                                            <h6 class="popular-searches-title mb-3">Popular Locations
                                                            </h6>
                                                            <div class="trending-tags">
                                                                <span
                                                                    class="badge go-ride-tag me-2 mb-2 location-option">
                                                                    <i class="fa-solid fa-circle-plus me-2"></i>Chennai
                                                                </span>
                                                                <span
                                                                    class="badge go-ride-tag me-2 mb-2 location-option">
                                                                    <i
                                                                        class="fa-solid fa-circle-plus me-2"></i>Bangalore
                                                                </span>
                                                                <span
                                                                    class="badge go-ride-tag me-2 mb-2 location-option">
                                                                    <i
                                                                        class="fa-solid fa-circle-plus me-2"></i>Hyderabad
                                                                </span>
                                                                <span
                                                                    class="badge go-ride-tag me-2 mb-2 location-option">
                                                                    <i class="fa-solid fa-circle-plus me-2"></i>Mumbai
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div id="filter_d">

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Mobile Filter Toggle Button -->


                                <!-- Mobile Filter Offcanvas -->



                                <!-- Job Cards Column -->
                                <div class="col-md-9 col-12 px-0" id="openJobs_cards">

                                </div>
                                <div id="loader" style="display:none; text-align:center; padding:20px;">
                                    <div class="spinner"></div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade " id="created" role="tabpanel">
                            <div class="row">
                                <ul class="nav nav-tabs card-header-tabs justify-content-center text-center mb-5"
                                    id="MyjobTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="current-tab" data-bs-toggle="tab"
                                            data-bs-target="#current" type="button" role="tab" aria-selected="true">
                                            Current Jobs </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="past-tab" data-bs-toggle="tab"
                                            data-bs-target="#past" type="button" role="tab" aria-selected="false"> Past
                                            Jobs</button>
                                    </li>
                                </ul>


                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="current" role="tabpanel"
                                    aria-labelledby="#current-tab">
                                    <div class="compact-car-card ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-center align-items-center">
                                                    <div class="col-md-7 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex align-items-center gap-3">
                                                                        <span class="passenger-count">
                                                                            <i class="fas fa-users"></i> 5
                                                                        </span>
                                                                        <span class="distance">
                                                                            <i class="fas fa-route"></i> 280km
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex justify-content-center align-items-end flex-column">
                                                                        <div class="amount"><span class="bids-c me-3 ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>₹3,800</div>
                                                                        <div class="amount-des">
                                                                            <i class="fas fa-bullhorn"></i> <small>
                                                                                Toll, parking, and bata included</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="trip-info mb-2">
                                                            <div class="route-section">
                                                                <div class="location-time">
                                                                    <div class="date-time">26 Jul <br> <span> 09:00
                                                                            AM</span> </div>
                                                                    <div class="location">Perambur railway station,
                                                                        Chennai</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                    <div class="arrow-line">
                                                                        <div class="duration d-none d-sm-block">2 days
                                                                            and 15 hours</div>
                                                                        <div class="arrow-container">
                                                                            <div class="long-arrow"></div>
                                                                            <i class="fas fa-car car-icon"
                                                                                style="transform: scale(1); color: rgb(0, 123, 255);"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time">29 Jul <br> <span> 05:42
                                                                            AM</span> </div>
                                                                    <div class="location">Kilambakkam bus terminus
                                                                        chennai</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 col-12 position-relative">
                                                        <div class="vertical-divider d-none d-md-block" style="
                                                                border-left: 2px solid #888f00;
                                                                height: 100%;
                                                                position: absolute;
                                                                top: 0;
                                                            
                                                            "></div>
                                                        <div
                                                            style="display: flex;justify-content: end;align-items: end;">
                                                            <div class="dropdown sort-dropdown  text-end mb-2">
                                                                <button class="btn btn-light dropdown-toggle"
                                                                    type="button" id="sortMenuButton"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="fas fa-filter " id="selectedSort"></i>
                                                                </button>

                                                                <ul class="dropdown-menu"
                                                                    aria-labelledby="sortMenuButton">
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="setActiveSort(this)">Price - Low to
                                                                            High</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="setActiveSort(this)">Price - High
                                                                            to Low</a></li>
                                                                </ul>
                                                            </div>


                                                        </div>
                                                        <!--<div class="d-flex justify-content-center align-items-center" style="font-weight:500">-->
                                                        <!--    <strong>Driver Bids</strong>-->
                                                        <!--    </div>-->
                                                        <div class="scrollable-bid-list" style="">

                                                            <div class="row bid-card-wrapper">
                                                                <div class="col-12">
                                                                    <div
                                                                        class="bid-card d-flex flex-column justify-content-center align-items-center text-center p-4">
                                                                        <i class="fas fa-user-slash text-muted mb-2"
                                                                            style="font-size: 2rem;"></i>
                                                                        <p class="mb-0 text-muted fw-bold">No bidders
                                                                            available</p>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="compact-car-card ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-center align-items-center">
                                                    <div class="col-md-7 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex align-items-center gap-3">
                                                                        <span class="passenger-count">
                                                                            <i class="fas fa-users"></i> 5
                                                                        </span>
                                                                        <span class="distance">
                                                                            <i class="fas fa-route"></i> 280km
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex justify-content-center align-items-end flex-column">
                                                                        <div class="amount"><span class="bids-c me-3 ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>₹3,800</div>
                                                                        <div class="amount-des">
                                                                            <i class="fas fa-bullhorn"></i> <small>
                                                                                Toll, parking, and bata included</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="trip-info">
                                                            <div class="route-section">
                                                                <div class="location-time">
                                                                    <div class="date-time">26 Jul <br> <span> 09:00
                                                                            AM</span> </div>
                                                                    <div class="location">Perambur railway station,
                                                                        Chennai</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                    <div class="arrow-line">
                                                                        <div class="duration d-none d-sm-block">2 days
                                                                            and 15 hours</div>
                                                                        <div class="arrow-container">
                                                                            <div class="long-arrow"></div>
                                                                            <i class="fas fa-car car-icon"
                                                                                style="transform: scale(1); color: rgb(0, 123, 255);"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time">29 Jul <br> <span> 05:42
                                                                            AM</span> </div>
                                                                    <div class="location">Kilambakkam bus terminus
                                                                        chennai</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 col-12 position-relative">
                                                        <div class="vertical-divider d-none d-md-block" style="
    border-left: 2px solid #888f00;
    height: 100%;
    position: absolute;
    top: 0;"></div>
                                                        <div style="
    display: flex;
    justify-content: end;
    align-items: end;
">
                                                            <div class="dropdown sort-dropdown  text-end">
                                                                <button class="btn btn-light dropdown-toggle"
                                                                    type="button" id="sortMenuButton"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="fas fa-filter " id="selectedSort"></i>
                                                                </button>

                                                                <ul class="dropdown-menu"
                                                                    aria-labelledby="sortMenuButton">

                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="sortCards('priceLow', this)"
                                                                            data-label="Price - Low to High">Price - Low
                                                                            to High</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="sortCards('priceHigh', this)"
                                                                            data-label="Price - High to Low">Price -
                                                                            High to Low</a></li>


                                                                </ul>
                                                            </div>

                                                        </div>
                                                        <!--<div class="d-flex justify-content-center align-items-center" style="font-weight:500">-->
                                                        <!--    <strong>Driver Bids</strong>-->
                                                        <!--    </div>-->
                                                        <div class="scrollable-bid-list" style="">
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="1">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-1">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(1)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-1">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(1)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>



                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div id="toreport"
                                                                        class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser()"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam()"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="2">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-2">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(2)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-2">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(2)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="3">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="bid-actions"
                                                                                        id="actions-3">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(3)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>

                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-3">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(3)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="4">

                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Right: Initial Actions -->
                                                                                    <div class="bid-actions"
                                                                                        id="actions-4">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(4)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-4">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(4)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="5">

                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-5">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(5)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-5">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(5)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>



                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="compact-car-card ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-center align-items-center">
                                                    <div class="col-md-7 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex align-items-center gap-3">
                                                                        <span class="passenger-count">
                                                                            <i class="fas fa-users"></i> 5
                                                                        </span>
                                                                        <span class="distance">
                                                                            <i class="fas fa-route"></i> 280km
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex justify-content-center align-items-end flex-column">
                                                                        <div class="amount"><span class="bids-c me-3 ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>₹3,800</div>
                                                                        <div class="amount-des">
                                                                            <i class="fas fa-bullhorn"></i> <small>
                                                                                Toll, parking, and bata included</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="trip-info">
                                                            <div class="route-section">
                                                                <div class="location-time">
                                                                    <div class="date-time">26 Jul <br> <span> 09:00
                                                                            AM</span> </div>
                                                                    <div class="location">Perambur railway station,
                                                                        Chennai</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                    <div class="arrow-line">
                                                                        <div class="duration d-none d-sm-block">2 days
                                                                            and 15 hours</div>
                                                                        <div class="arrow-container">
                                                                            <div class="long-arrow"></div>
                                                                            <i class="fas fa-car car-icon"
                                                                                style="transform: scale(1); color: rgb(0, 123, 255);"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time">29 Jul <br> <span> 05:42
                                                                            AM</span> </div>
                                                                    <div class="location">Kilambakkam bus terminus
                                                                        chennai</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 col-12 position-relative">
                                                        <div class="vertical-divider d-none d-md-block" style="
    border-left: 2px solid #888f00;
    height: 100%;
    position: absolute;
    top: 0;"></div>
                                                        <div style="
    display: flex;
    justify-content: end;
    align-items: end;
">
                                                            <div class="dropdown sort-dropdown  text-end">
                                                                <button class="btn btn-light dropdown-toggle"
                                                                    type="button" id="sortMenuButton"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="fas fa-filter " id="selectedSort"></i>
                                                                </button>

                                                                <ul class="dropdown-menu"
                                                                    aria-labelledby="sortMenuButton">

                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="sortCards('priceLow', this)"
                                                                            data-label="Price - Low to High">Price - Low
                                                                            to High</a></li>
                                                                    <li><a class="dropdown-item" href="#"
                                                                            onclick="sortCards('priceHigh', this)"
                                                                            data-label="Price - High to Low">Price -
                                                                            High to Low</a></li>


                                                                </ul>
                                                            </div>

                                                        </div>
                                                        <!--<div class="d-flex justify-content-center align-items-center" style="font-weight:500">-->
                                                        <!--    <strong>Driver Bids</strong>-->
                                                        <!--    </div>-->
                                                        <div class="scrollable-bid-list" style="">
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="1">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-1">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(1)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-1">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>
                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(1)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>



                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div id="toreport"
                                                                        class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser()"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam()"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="2">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-2">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(2)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-2">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(2)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="3">
                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="bid-actions"
                                                                                        id="actions-3">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(3)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>

                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-3">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(3)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="4">

                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- Right: Initial Actions -->
                                                                                    <div class="bid-actions"
                                                                                        id="actions-4">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(4)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-4">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(4)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>


                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row bid-card-wrapper">
                                                                <div class="bid-card" data-card-id="5">

                                                                    <div class="bid-card-row redesigned-bid-card ">

                                                                        <div class="col-md-12 col-12">
                                                                            <div class="row">
                                                                                <div
                                                                                    class="d-flex justify-content-center gap-3">
                                                                                    <div class="driver-info">
                                                                                        <div class="driver-name"
                                                                                            style="cursor:pointer; text-decoration: underline;"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target="#userModal">
                                                                                            Ramesh Kumar</div>
                                                                                    </div>
                                                                                    <div class="driver-info">
                                                                                        <div class="amount-bid">₹100
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="bid-actions"
                                                                                        id="actions-5">
                                                                                        <button
                                                                                            class="btn btn-success p-2 btn-sm accept-btn"
                                                                                            onclick="showAcceptConfirmation(5)"
                                                                                            title="Accept">
                                                                                            <i
                                                                                                class="fa-solid fa-check"></i>
                                                                                        </button>


                                                                                    </div>
                                                                                    <!-- Right: After Accepted -->
                                                                                    <div class="bid-post-accept d-none"
                                                                                        id="post-accept-5">
                                                                                        <!-- Call icon -->
                                                                                        <div href="tel:+919999999999"
                                                                                            class="icon-circle bg-primary text-white me-2"
                                                                                            title="Call">
                                                                                            <i
                                                                                                class="fas fa-phone fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- WhatsApp icon -->
                                                                                        <div href="https://wa.me/919999999999"
                                                                                            target="_blank"
                                                                                            class="icon-circle bg-success text-white me-2"
                                                                                            title="WhatsApp">
                                                                                            <i
                                                                                                class="fab fa-whatsapp fa-lg"></i>
                                                                                        </div>

                                                                                        <!-- Reject button -->
                                                                                        <button
                                                                                            class="btn btn-danger p-2 btn-sm accept-btn"
                                                                                            onclick="showRejectModal(5)"
                                                                                            title="Reject">
                                                                                            <i
                                                                                                class="fas fa-times-circle"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>



                                                                    </div>

                                                                    <!-- Remarks section -->
                                                                    <div class=" glassy-remarks mt-2"
                                                                        style="display: none;">
                                                                        <p>Available for immediate dispatch
                                                                            Prefers bypass route</p>
                                                                    </div>

                                                                    <!-- Flag options -->
                                                                    <div class="flag-options stylish-flags mt-2"
                                                                        style="display: none;">
                                                                        <button onclick="reportUser('Ramesh Kumar')"><i
                                                                                class="fas fa-exclamation-circle"></i>
                                                                            Block</button>
                                                                        <button onclick="markAsSpam('Ramesh Kumar')"><i
                                                                                class="fas fa-ban"></i> Block and Report
                                                                            with Spam</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="#past-tab">

                                    <div class="past-job" id="past_list">

                                        <div class="job-card mb-3">
                                            <div class="ribbon"><span class="cancelled">Cancelled</span></div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row mt-3">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div
                                                                        class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                        <span
                                                                            class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                            <i class="fas fa-users"></i> 23
                                                                        </span>
                                                                        <span
                                                                            class="distance d-flex align-items-center gap-1">
                                                                            <i class="fas fa-route"></i> 255 km
                                                                        </span>

                                                                    </div>

                                                                    <!-- Right: Amount & Description -->
                                                                    <div
                                                                        class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                        <div
                                                                            class="amount d-flex flex-column justify-content-end align-items-end">
                                                                            <span class="price mb-1">₹ 2000</span>
                                                                            <span class="bids-c ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>

                                                                        </div>


                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 py-1 col-12">

                                                        <div class="trip-info">
                                                            <div class="route-section m-3 p-1">
                                                                <div class="location-time">

                                                                    <div class="location" onclick="toggleAddress(this)">
                                                                        Kallakurichi New bus stand, Tamil Nadu, India -
                                                                        606213</div>
                                                                </div>
                                                                <div class="route-arrow">

                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">ONE
                                                                            WAY</span>
                                                                        <div class="arrow-container">
                                                                            <!-- For double arrows (stacked version) -->
                                                                            <div class="arrow-stack">
                                                                                <div class="long-arrow top-arrow"></div>
                                                                                <div class="long-arrow bottom-arrow">
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-car car-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time"></div>
                                                                    <div class="location "
                                                                        onclick="toggleAddress(this)">Perambur Bus
                                                                        Terminus, Perambur High Road, Chinnaiyan Colony,
                                                                        Perambur, Chennai, Tamil Nadu, India - 600012
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                                <!-- Pickup with icon -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">
                                                                    <i
                                                                        class="fa-solid fa-plane-departure text-success"></i>
                                                                    <strong class="fw-bold">
                                                                        20 Aug 11:46 AM
                                                                    </strong>
                                                                </span>

                                                                <!-- Dropoff only if date exists -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="job-card mb-3">
                                            <div class="ribbon"><span class="completed">Completed</span></div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row mt-3">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div
                                                                        class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                        <span
                                                                            class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                            <i class="fas fa-users"></i> 23
                                                                        </span>
                                                                        <span
                                                                            class="distance d-flex align-items-center gap-1">
                                                                            <i class="fas fa-route"></i> 255 km
                                                                        </span>

                                                                    </div>

                                                                    <!-- Right: Amount & Description -->
                                                                    <div
                                                                        class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                        <div
                                                                            class="amount d-flex flex-column justify-content-end align-items-end">
                                                                            <span class="price mb-1">₹ 2000</span>
                                                                            <span class="bids-c ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>

                                                                        </div>


                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 py-1  col-12">

                                                        <div class="trip-info">
                                                            <div class="route-section m-3 p-1">
                                                                <div class="location-time">

                                                                    <div class="location" onclick="toggleAddress(this)">
                                                                        Kallakurichi New bus stand, Tamil Nadu, India -
                                                                        606213</div>
                                                                </div>
                                                                <div class="route-arrow">

                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">ONE
                                                                            WAY</span>
                                                                        <div class="arrow-container">
                                                                            <!-- For double arrows (stacked version) -->
                                                                            <div class="arrow-stack">
                                                                                <div class="long-arrow top-arrow"></div>
                                                                                <div class="long-arrow bottom-arrow">
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-car car-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time"></div>
                                                                    <div class="location "
                                                                        onclick="toggleAddress(this)">Perambur Bus
                                                                        Terminus, Perambur High Road, Chinnaiyan Colony,
                                                                        Perambur, Chennai, Tamil Nadu, India - 600012
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                                <!-- Pickup with icon -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">
                                                                    <i
                                                                        class="fa-solid fa-plane-departure text-success"></i>
                                                                    <strong class="fw-bold">
                                                                        20 Aug 11:46 AM
                                                                    </strong>
                                                                </span>

                                                                <!-- Dropoff only if date exists -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="job-card mb-3">
                                            <div class="ribbon"><span class="expired">Expired</span></div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row mt-3">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div
                                                                        class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                        <span
                                                                            class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                            <i class="fas fa-users"></i> 23
                                                                        </span>
                                                                        <span
                                                                            class="distance d-flex align-items-center gap-1">
                                                                            <i class="fas fa-route"></i> 255 km
                                                                        </span>

                                                                    </div>

                                                                    <!-- Right: Amount & Description -->
                                                                    <div
                                                                        class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                        <div
                                                                            class="amount d-flex flex-column justify-content-end align-items-end">
                                                                            <span class="price mb-1">₹ 2000</span>
                                                                            <span class="bids-c ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>

                                                                        </div>


                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 py-1  col-12">

                                                        <div class="trip-info">
                                                            <div class="route-section m-3 p-1">
                                                                <div class="location-time">

                                                                    <div class="location" onclick="toggleAddress(this)">
                                                                        Kallakurichi New bus stand, Tamil Nadu, India -
                                                                        606213</div>
                                                                </div>
                                                                <div class="route-arrow">

                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">ONE
                                                                            WAY</span>
                                                                        <div class="arrow-container">
                                                                            <!-- For double arrows (stacked version) -->
                                                                            <div class="arrow-stack">
                                                                                <div class="long-arrow top-arrow"></div>
                                                                                <div class="long-arrow bottom-arrow">
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-car car-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time"></div>
                                                                    <div class="location "
                                                                        onclick="toggleAddress(this)">Perambur Bus
                                                                        Terminus, Perambur High Road, Chinnaiyan Colony,
                                                                        Perambur, Chennai, Tamil Nadu, India - 600012
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                                <!-- Pickup with icon -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">
                                                                    <i
                                                                        class="fa-solid fa-plane-departure text-success"></i>
                                                                    <strong class="fw-bold">
                                                                        20 Aug 11:46 AM
                                                                    </strong>
                                                                </span>

                                                                <!-- Dropoff only if date exists -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="job-card mb-3">
                                            <div class="ribbon"><span class="no-response ">No Response</span></div>

                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row mt-3">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div
                                                                        class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                        <span
                                                                            class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                            <i class="fas fa-users"></i> 23
                                                                        </span>
                                                                        <span
                                                                            class="distance d-flex align-items-center gap-1">
                                                                            <i class="fas fa-route"></i> 255 km
                                                                        </span>

                                                                    </div>

                                                                    <!-- Right: Amount & Description -->
                                                                    <div
                                                                        class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                        <div
                                                                            class="amount d-flex flex-column justify-content-end align-items-end">
                                                                            <span class="price mb-1">₹ 2000</span>
                                                                            <span class="bids-c ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>

                                                                        </div>


                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 py-1 col-12">

                                                        <div class="trip-info">
                                                            <div class="route-section m-3 p-1">
                                                                <div class="location-time">

                                                                    <div class="location" onclick="toggleAddress(this)">
                                                                        Kallakurichi New bus stand, Tamil Nadu, India -
                                                                        606213</div>
                                                                </div>
                                                                <div class="route-arrow">

                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">ONE
                                                                            WAY</span>
                                                                        <div class="arrow-container">
                                                                            <!-- For double arrows (stacked version) -->
                                                                            <div class="arrow-stack">
                                                                                <div class="long-arrow top-arrow"></div>
                                                                                <div class="long-arrow bottom-arrow">
                                                                                </div>
                                                                            </div>
                                                                            <i class="fas fa-car car-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time"></div>
                                                                    <div class="location "
                                                                        onclick="toggleAddress(this)">Perambur Bus
                                                                        Terminus, Perambur High Road, Chinnaiyan Colony,
                                                                        Perambur, Chennai, Tamil Nadu, India - 600012
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div
                                                                class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                                <!-- Pickup with icon -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">
                                                                    <i
                                                                        class="fa-solid fa-plane-departure text-success"></i>
                                                                    <strong class="fw-bold">
                                                                        20 Aug 11:46 AM
                                                                    </strong>
                                                                </span>

                                                                <!-- Dropoff only if date exists -->
                                                                <span
                                                                    class="d-inline-flex align-items-center gap-1 date-time">

                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>


                        <div class="tab-pane fade " id="bidding" role="tabpanel">
                            <div class="bidding-jobs mt-5" id="bid-status">
                                <div class="job-card mb-3">
                                    <div class="ribbon"><span class="completed">Won</span></div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="company-info">
                                                    <div class="car-specs">
                                                        <div class="row mt-3">
                                                            <!-- Left: Passenger & Distance + Heart at end -->
                                                            <div class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                <span
                                                                    class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                    <i class="fas fa-users"></i> 23
                                                                </span>
                                                                <span class="distance d-flex align-items-center gap-1">
                                                                    <i class="fas fa-route"></i> 255 km
                                                                </span>

                                                            </div>

                                                            <!-- Right: Amount & Description -->
                                                            <div
                                                                class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                <div
                                                                    class="amount d-flex flex-column justify-content-end align-items-end">
                                                                    <span class="price mb-1">₹ 2000</span>
                                                                    <span class="bids-c ">
                                                                        <i class="fas fa-gavel"></i> 15 Bids
                                                                    </span>

                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 py-1 col-12">

                                                <div class="trip-info">
                                                    <div class="route-section m-3 p-1">
                                                        <div class="location-time">

                                                            <div class="location" onclick="toggleAddress(this)">
                                                                Kallakurichi New bus stand, Tamil Nadu, India - 606213
                                                            </div>
                                                        </div>
                                                        <div class="route-arrow">

                                                            <div class="arrow-line">
                                                                <span class="oneway-label d-none d-md-block">ONE
                                                                    WAY</span>
                                                                <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        <div class="long-arrow bottom-arrow"></div>
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="location-time">
                                                            <div class="date-time"></div>
                                                            <div class="location " onclick="toggleAddress(this)">
                                                                Perambur Bus Terminus, Perambur High Road, Chinnaiyan
                                                                Colony, Perambur, Chennai, Tamil Nadu, India - 600012
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex justify-content-between mt-3  ">
                                                        <!-- Pickup with icon -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">
                                                            <i class="fa-solid fa-plane-departure text-success"></i>
                                                            <strong class="fw-bold">
                                                                20 Aug 11:46 AM
                                                            </strong>
                                                        </span>

                                                        <!-- Dropoff only if date exists -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="job-card mb-3">
                                    <div class="ribbon"><span class="cancelled">Lose</span></div>

                                    <div class="card-body bidding-jobs">
                                        <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="company-info">
                                                    <div class="car-specs">
                                                        <div class="row mt-3">
                                                            <!-- Left: Passenger & Distance + Heart at end -->
                                                            <div class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                <span
                                                                    class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                    <i class="fas fa-users"></i> 23
                                                                </span>
                                                                <span class="distance d-flex align-items-center gap-1">
                                                                    <i class="fas fa-route"></i> 255 km
                                                                </span>

                                                            </div>

                                                            <!-- Right: Amount & Description -->
                                                            <div
                                                                class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                <div
                                                                    class="amount d-flex flex-column justify-content-end align-items-end">
                                                                    <span class="price mb-1">₹ 2000</span>
                                                                    <span class="bids-c ">
                                                                        <i class="fas fa-gavel"></i> 15 Bids
                                                                    </span>

                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 py-1  col-12">

                                                <div class="trip-info">
                                                    <div class="route-section m-3 p-1">
                                                        <div class="location-time">

                                                            <div class="location" onclick="toggleAddress(this)">
                                                                Kallakurichi New bus stand, Tamil Nadu, India - 606213
                                                            </div>
                                                        </div>
                                                        <div class="route-arrow">

                                                            <div class="arrow-line">
                                                                <span class="oneway-label d-none d-md-block">ONE
                                                                    WAY</span>
                                                                <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        <div class="long-arrow bottom-arrow"></div>
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="location-time">
                                                            <div class="date-time"></div>
                                                            <div class="location " onclick="toggleAddress(this)">
                                                                Perambur Bus Terminus, Perambur High Road, Chinnaiyan
                                                                Colony, Perambur, Chennai, Tamil Nadu, India - 600012
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                        <!-- Pickup with icon -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">
                                                            <i class="fa-solid fa-plane-departure text-success"></i>
                                                            <strong class="fw-bold">
                                                                20 Aug 11:46 AM
                                                            </strong>
                                                        </span>

                                                        <!-- Dropoff only if date exists -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="job-card mb-3">
                                    <div class="ribbon"><span class="no-response">In Review</span></div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="company-info">
                                                    <div class="car-specs">
                                                        <div class="row mt-3">
                                                            <!-- Left: Passenger & Distance + Heart at end -->
                                                            <div class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                <span
                                                                    class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                    <i class="fas fa-users"></i> 23
                                                                </span>
                                                                <span class="distance d-flex align-items-center gap-1">
                                                                    <i class="fas fa-route"></i> 255 km
                                                                </span>

                                                            </div>

                                                            <!-- Right: Amount & Description -->
                                                            <div
                                                                class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                <div
                                                                    class="amount d-flex flex-column justify-content-end align-items-end">
                                                                    <span class="price mb-1">₹ 2000</span>
                                                                    <span class="bids-c ">
                                                                        <i class="fas fa-gavel"></i> 15 Bids
                                                                    </span>

                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 py-1  col-12">

                                                <div class="trip-info">
                                                    <div class="route-section m-3 p-1">
                                                        <div class="location-time">

                                                            <div class="location" onclick="toggleAddress(this)">
                                                                Kallakurichi New bus stand, Tamil Nadu, India - 606213
                                                            </div>
                                                        </div>
                                                        <div class="route-arrow">

                                                            <div class="arrow-line">
                                                                <span class="oneway-label d-none d-md-block">ONE
                                                                    WAY</span>
                                                                <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        <div class="long-arrow bottom-arrow"></div>
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="location-time">
                                                            <div class="date-time"></div>
                                                            <div class="location " onclick="toggleAddress(this)">
                                                                Perambur Bus Terminus, Perambur High Road, Chinnaiyan
                                                                Colony, Perambur, Chennai, Tamil Nadu, India - 600012
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                        <!-- Pickup with icon -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">
                                                            <i class="fa-solid fa-plane-departure text-success"></i>
                                                            <strong class="fw-bold">
                                                                20 Aug 11:46 AM
                                                            </strong>
                                                        </span>

                                                        <!-- Dropoff only if date exists -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="tab-pane fade mt-3 " id="liked" role="tabpanel">
                            <div class="liked-jobs" id="liked_job_list">

                                <div class="compact-car-card">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 col-12">
                                                    <div class="company-info">
                                                        <div class="car-specs">
                                                            <div class="row">
                                                                <!-- Left: Passenger & Distance + Heart at end -->
                                                                <div
                                                                    class="col-md-6 col-6 d-flex align-items-center gap-1">
                                                                    <span class="icon-report me-2 d-none d-md-inline"
                                                                        onclick="toggleLike(this)"
                                                                        style="cursor:pointer;">
                                                                        <i class="fa fa-heart"></i>
                                                                    </span>
                                                                    <span
                                                                        class="passenger-count d-flex align-items-center gap-1">
                                                                        <i class="fas fa-users"></i> 23
                                                                    </span>
                                                                    <span
                                                                        class="distance d-flex align-items-center gap-1">
                                                                        <i class="fas fa-route"></i> 255 km
                                                                    </span>

                                                                </div>

                                                                <!-- Right: Amount & Description -->
                                                                <div
                                                                    class="col-md-6 col-6 d-flex justify-content-end align-items-end">
                                                                    <div
                                                                        class="amount d-flex flex-column justify-content-end align-items-end">
                                                                        <span class="price mb-1">₹ 2000</span>
                                                                        <span class="bids-c ">
                                                                            <i class="fas fa-gavel"></i> 15 Bids
                                                                        </span>

                                                                    </div>


                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 py-1 px-0 col-12">

                                                    <div class="trip-info">
                                                        <div class="route-section m-3 p-1">
                                                            <div class="location-time">

                                                                <div class="location" onclick="toggleAddress(this)">
                                                                    Kallakurichi New bus stand, Tamil Nadu, India -
                                                                    606213</div>
                                                            </div>
                                                            <div class="route-arrow">

                                                                <div class="arrow-line">
                                                                    <span class="oneway-label d-none d-md-block">ONE
                                                                        WAY</span>
                                                                    <div class="arrow-container">
                                                                        <!-- For double arrows (stacked version) -->
                                                                        <div class="arrow-stack">
                                                                            <div class="long-arrow top-arrow"></div>
                                                                            <div class="long-arrow bottom-arrow"></div>
                                                                        </div>
                                                                        <i class="fas fa-car car-icon"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="location-time">
                                                                <div class="date-time"></div>
                                                                <div class="location" onclick="toggleAddress(this)">
                                                                    Perambur Bus Terminus, Perambur High Road,
                                                                    Chinnaiyan Colony, Perambur, Chennai, Tamil Nadu,
                                                                    India - 600012</div>
                                                            </div>

                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                            <!-- Pickup with icon -->
                                                            <span
                                                                class="d-inline-flex align-items-center gap-1 date-time">
                                                                <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold">
                                                                    20 Aug 11:46 AM
                                                                </strong>
                                                            </span>

                                                            <!-- Dropoff only if date exists -->
                                                            <span
                                                                class="d-inline-flex align-items-center gap-1 date-time">

                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div
                                                    class="col-12 d-md-flex justify-content-md-between align-items-center flex-md-wrap align-items-end">

                                                    <!-- Left: Posted Info (Desktop Only) -->
                                                    <div
                                                        class="d-md-flex d-inline-block align-items-center justify-content-center small text-muted gap-4 mb-2 d-none d-md-block">
                                                        <span class="d-flex align-items-center gap-1">
                                                            <i class="fa-solid fa-badge-check text-success"></i>
                                                            Posted by
                                                            <strong class="fw-bold"
                                                                style="cursor:pointer; text-decoration: underline;"
                                                                data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                Kavi
                                                            </strong>
                                                        </span>
                                                    </div>

                                                    <!-- Center: Expiry + Icons (Desktop Only) -->
                                                    <div
                                                        class="d-md-flex d-inline-block align-items-center justify-content-center gap-3 mb-2 d-none d-md-block">
                                                        <span class="text-form">
                                                            <i
                                                                class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                            Expiry in 1 days and 0 hours
                                                        </span>
                                                    </div>

                                                    <!-- Mobile View: Posted + Expiry in one row -->


                                                    <div class=" small text-muted mt-2 position-relative d-md-none"
                                                        style="border-top: 2px solid #ddd;">
                                                        <span class="oneway-label">ROUND TRIP</span>
                                                        <div class="d-flex justify-content-between py-3">
                                                            <!-- Pickup with icon -->
                                                            <span
                                                                class="d-inline-flex align-items-center gap-1 date-time">
                                                                <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold">
                                                                    27 Aug 04:15 PM
                                                                </strong>
                                                            </span>

                                                            <!-- Dropoff only if date exists -->
                                                            <span
                                                                class="d-inline-flex align-items-center gap-1 date-time">
                                                                <i class="fa-solid fa-plane-arrival text-danger"></i>
                                                                <strong class="fw-bold"
                                                                    style="cursor:pointer; text-decoration: underline;"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#postedModal">
                                                                    29 Aug 02:15 PM
                                                                </strong>
                                                            </span>
                                                        </div>


                                                        <span class="d-flex align-items-center gap-1">
                                                            <i class="fa-solid fa-badge-check text-success"></i>
                                                            Posted by
                                                            <strong class="fw-bold"
                                                                style="cursor:pointer; text-decoration: underline;"
                                                                data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                Kavi
                                                            </strong>
                                                            <span class="ms-auto" id="actionSection">
                                                                <button class="btn btn-sm btn-warning ms-auto agreeBtn "
                                                                    onclick="bidManage('23', this)">
                                                                    <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                </button>
                                                            </span>
                                                        </span>
                                                        <span class="text-form">
                                                            <i
                                                                class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                            Expiry in 1 days and 0 hours
                                                        </span>

                                                    </div>
                                                    <div class="text-end d-none d-md-block" id="actionSection">
                                                        <button class="btn btn-sm btn-warning ms-auto agreeBtn "
                                                            onclick="bidManage('23', this)"><i
                                                                class="fas fa-gavel me-2"></i> Manage Bid</button>


                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<!-- Remarks Modal for Driver ID 1 -->
<div class="modal fade" id="remarksModal-1" tabindex="-1" aria-labelledby="remarksModalLabel-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-primary" id="remarksModalLabel-1">
                    <i class="fas fa-comment-dots me-2"></i>Driver Remarks
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3 text-secondary" style="line-height: 1.6;">
                Available immediately, prefers routes with less traffic, comfortable with night drives, and has
                experience in long-distance delivery.
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createJobModal" tabindex="-1" aria-labelledby="createJobLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg  modal-dialog-centered">
        <div class="modal-content w-100 p-3">
            <div class="modal-header d-flex justify-content-center position-relative p-0">
                <h5 class="modal-title" id="createJobLabel">Create Job</h5>
                <button type="button" class="btn-close close-button position-absolute end-0 me-3"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-2">
                <form id="journeyForm" data-gtm-form-interact-id="0" enctype="multipart/form-data"
                    onsubmit="journeyForm(event)">

                    <!-- Journey Type -->
                    <div class="d-flex align-items-center flex-nowrap overflow-auto gap-2 ms-2">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input mt-2" type="radio" name="job_type" id="oneWay" value="oneway"
                                checked>
                            <label class="form-check-label fw-bold" for="oneWay">One Way</label>
                        </div>
                        <div class="form-check mb-0">
                            <input class="form-check-input mt-2" type="radio" name="job_type" id="roundTrip"
                                value="roundtrip">
                            <label class="form-check-label fw-bold" for="roundTrip">Round Trip</label>
                        </div>
                    </div>

                    <!-- From, To, Distance -->
                    <div class="row g-2 mb-3 ">
                        <!--<div class="col-md-6 col-12">-->
                        <!--    <input type="text" id="fromPlace" class="form-control p-1" placeholder="From"-->
                        <!--        data-gtm-form-interact-field-id="3">-->
                        <!--</div>-->
                        <!--<div class="col-md-6 col-12">-->
                        <!--    <input type="text" id="toPlace" class="form-control p-1" placeholder="To">-->
                        <!--</div>-->
                        <div class="col-md-6 col-12">
                            <select id="fromPlace" name="from_place" class="form-control" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>
                        <div class="col-md-6 col-12">
                            <select id="toPlace" name="to_place" class="form-control" style="width: 100%;">
                                <option></option>
                            </select>
                        </div>




                    </div>

                    <!-- Pickup, Drop, Passenger -->
                    <div class="row mb-3">
                        <div class="col-md-4 col-12">
                            <label class="fw-bold">Pickup Date &amp; Time</label>
                            <input type="datetime-local" id="pickupDate" name="pickup_date" class="form-control p-1">
                        </div>
                        <div class="col-md-4 col-12 d-none" id="dropSection">
                            <label class="fw-bold">Drop Date &amp; Time</label>
                            <input type="datetime-local" id="dropDate" name="dropoff_date" class="form-control p-1">
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="fw-bold" for="passengerCount">Passenger Count</label>
                            <input type="number" id="passengerCount" name="pass_count" class="form-control p-1" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,3)"
                                placeholder="Count" min="1" max="500">
                        </div>
                    </div>


                    <!-- Expiry and Amounts -->
                    <div class="row ">
                        <!-- Estimated Fare -->
                        <div class="col-md-6 col-12">
                            <label class="fw-bold">Estimated Fare (₹)</label>
                            <input type="number" id="marketedPrice" name="fare" class="form-control p-1 mb-0" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,7)"
                                placeholder="Enter the estimated fare">
                            <small class="form-text text-muted mt-1 d-none">The actual marketed price is <span
                                    id="approx_fare" class="fw-bold">₹1500</span>.</small>
                        </div>

                        <!-- Distance -->
                        <div class="col-md-4 col-12">
                            <label class="fw-bold">Distance (KM)</label>
                            <input type="number" id="distance" name="distance" class="form-control p-1" oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,7)"
                                placeholder="Enter Distance">
                        </div>
                    </div>
                    <!-- Additional Charges -->
                    <div class="mb-2">
                        <label class="form-label fw-bold">Additional Charges (₹) (Included / Passenger to Pay(P2P))</label>
                        <div class="row">
                            <!-- Bata -->
                            <div class="col-md-3 col-4">
                                <label class="fw-bold mb-1 d-block">Bata</label>
                                <!--<div class="mt-2 d-flex flex-column">-->
                                <!--    <div class="form-check">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="bataIncluded"-->
                                <!--            name="bataRadio" value="Included" checked>-->
                                <!--        <label class="form-check-label" for="bataIncluded">Included</label>-->
                                <!--    </div>-->
                                <!--    <div class="form-check">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="bataClient"-->
                                <!--            name="bataRadio" value="Passenger to pay">-->
                                <!--        <label class="form-check-label" for="bataClient">Passenger to pay</label>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="neo-toggle-container">
                                  <input class="neo-toggle-input" id="bataIncluded" type="checkbox" />
                                  <label class="neo-toggle" for="bataIncluded">
                                    <div class="neo-track">
                                      <div class="neo-background-layer"></div>
                                      <div class="neo-grid-layer"></div>
                                      <div class="neo-spectrum-analyzer">
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                      </div>
                                      <div class="neo-track-highlight"></div>
                                    </div>
                                
                                    <div class="neo-thumb">
                                      <div class="neo-thumb-ring"></div>
                                      <div class="neo-thumb-core">
                                        <div class="neo-thumb-icon">
                                          <div class="neo-thumb-wave"></div>
                                          <div class="neo-thumb-pulse"></div>
                                        </div>
                                      </div>
                                    </div>
                                
                                    <div class="neo-gesture-area"></div>
                                
                                    <div class="neo-interaction-feedback">
                                      <div class="neo-ripple"></div>
                                      <div class="neo-progress-arc"></div>
                                    </div>
                                
                                    <div class="neo-status">
                                      <div class="neo-status-indicator">
                                        <div class="neo-status-text"></div>
                                      </div>
                                    </div>
                                  </label>
                                </div>
                            </div>


                            <!-- Toll -->
                            <div class="col-md-3 col-4">
                                <label class="fw-bold mb-1 d-block">Toll</label>
                                <!--<div class="mt-2 d-flex flex-column">-->
                                <!--    <div class="form-check">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="tollIncluded"-->
                                <!--            name="tollRadio" value="Included" checked>-->
                                <!--        <label class="form-check-label" for="tollIncluded">Included</label>-->
                                <!--    </div>-->
                                <!--    <div class="form-check">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="tollClient"-->
                                <!--            name="tollRadio" value="Passenger to pay">-->
                                <!--        <label class="form-check-label" for="tollClient">Passenger to pay</label>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="neo-toggle-container">
                                  <input class="neo-toggle-input" id="tollIncluded" type="checkbox" />
                                  <label class="neo-toggle" for="tollIncluded">
                                    <div class="neo-track">
                                      <div class="neo-background-layer"></div>
                                      <div class="neo-grid-layer"></div>
                                      <div class="neo-spectrum-analyzer">
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                      </div>
                                      <div class="neo-track-highlight"></div>
                                    </div>
                                
                                    <div class="neo-thumb">
                                      <div class="neo-thumb-ring"></div>
                                      <div class="neo-thumb-core">
                                        <div class="neo-thumb-icon">
                                          <div class="neo-thumb-wave"></div>
                                          <div class="neo-thumb-pulse"></div>
                                        </div>
                                      </div>
                                    </div>
                                
                                    <div class="neo-gesture-area"></div>
                                
                                    <div class="neo-interaction-feedback">
                                      <div class="neo-ripple"></div>
                                      <div class="neo-progress-arc"></div>
                                    </div>
                                
                                    <div class="neo-status">
                                      <div class="neo-status-indicator">
                                        <div class="neo-status-text"></div>
                                      </div>
                                    </div>
                                  </label>
                                </div>
                            </div>


                            <!-- Parking -->
                            <div class="col-md-3 col-4">
                                <label class="fw-bold mb-1 d-block">Parking</label>
                                <!--<div class="mt-2 d-flex flex-column">-->
                                <!--    <div class="form-check me-2">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="parkingIncluded"-->
                                <!--            name="parkingRadio" value="Included" checked>-->
                                <!--        <label class="form-check-label" for="parkingIncluded">Included</label>-->
                                <!--    </div>-->
                                <!--    <div class="form-check">-->
                                <!--        <input class="form-check-input mt-2" type="radio" id="parkingClient"-->
                                <!--            name="parkingRadio" value="Passenger to pay">-->
                                <!--        <label class="form-check-label" for="parkingClient">Passenger to pay</label>-->
                                <!--    </div>-->
                                <!--</div>-->
                                <div class="neo-toggle-container">
                                  <input class="neo-toggle-input" id="parkingIncluded" type="checkbox" />
                                  <label class="neo-toggle" for="parkingIncluded">
                                    <div class="neo-track">
                                      <div class="neo-background-layer"></div>
                                      <div class="neo-grid-layer"></div>
                                      <div class="neo-spectrum-analyzer">
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                        <div class="neo-spectrum-bar"></div>
                                      </div>
                                      <div class="neo-track-highlight"></div>
                                    </div>
                                
                                    <div class="neo-thumb">
                                      <div class="neo-thumb-ring"></div>
                                      <div class="neo-thumb-core">
                                        <div class="neo-thumb-icon">
                                          <div class="neo-thumb-wave"></div>
                                          <div class="neo-thumb-pulse"></div>
                                        </div>
                                      </div>
                                    </div>
                                
                                    <div class="neo-gesture-area"></div>
                                
                                    <div class="neo-interaction-feedback">
                                      <div class="neo-ripple"></div>
                                      <div class="neo-progress-arc"></div>
                                    </div>
                                
                                    <div class="neo-status">
                                      <div class="neo-status-indicator">
                                        <div class="neo-status-text"></div>
                                      </div>
                                    </div>
                                  </label>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- Remarks -->
                    <div class="mt-3">
                        <label class="fw-bold">Remarks</label>
                        <textarea class="form-control p-1" rows="2" placeholder="Enter remarks..." oninput="this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'').slice(0,150)"
                            name="job_remark"></textarea>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input p-1 mt-2 " type="checkbox" id="termsCheck" required>
                      <label class="form-check-label " style ="font-size:14px;" for="termsCheck">
                        I agree to the User Agreement – 
                        <span class="terms-tooltip text-primary fw-bold" data-bs-toggle="tooltip">
                          Disclaimer
                        </span>
                      </label>
                    </div>

                    <!-- Submit -->
                    <div class="text-center d-flex justify-content-center">
                        <!--<button type="button" class="btn view-btn p-1" data-bs-toggle="modal"-->
                        <!--    data-bs-target="#jobPreviewModal" data-bs-dismiss="modal">Create Job</button>-->
                        <button type="submit" class="btn view-btn " id="job_submit-btn" style="padding:4px 16px" ;>Post
                            Job</button>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="jobPreviewModal" tabindex="-1" aria-labelledby="jobPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content w-100 p-3">
            <div class="modal-header">
                <h5 class="modal-title" id="jobPreviewLabel">Job Preview</h5>
                <button type="button" class="btn-close close-button" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="previewContent">

                <!-- Summary Section -->
                <div class="summary-section p-3 bg-light rounded" style="width: auto !important;">
                    <h6 class="fw-bold mb-3"><i class="fas fa-file-alt me-2 text-warning"></i>Job Summary</h6>
                    <div class="row d-none d-md-flex">
                        <div class="col-md-6 col-12">
                            <p><i class="fas fa-calendar-day me-2 text-primary"></i><strong
                                    class="fw-bold">Pickup:</strong> <span id="pre_pick">15 May 2023, 10:00 AM</span>
                            </p>
                            <p><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong
                                    class="fw-bold">From:</strong> <span id="pre_from2"
                                    onclick="toggleAddress(this)">Mumbai, Maharashtra</span></p>
                            <p><i class="fas fa-user-friends me-2 text-success"></i><strong class="fw-bold">Passenger
                                    Count:</strong> <span id="pre_pass">12</span></p>
                            <p><i class="fas fa-ruler-horizontal me-2 text-info"></i><strong class="fw-bold">Distance
                                    (KM):</strong> <span id="pre_dis">391</span></p>
                        </div>
                        <div class="col-md-6 col-12">
                            <p><i class="fas fa-calendar-day me-2 text-danger pre_drop_hide"></i><strong
                                    class="fw-bold pre_drop_hide">Drop:</strong> <span id="pre_drop"
                                    class="pre_drop_hide">15 May 2023, 01:30 PM</span></p>
                            <p><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong
                                    class="fw-bold">To:</strong> <span id="pre_to2" onclick="toggleAddress(this)">Pune,
                                    Maharashtra</span></p>
                            <p class="my-0">
                                <i class="fas fa-check-circle me-2 text-primary"></i>
                                <strong class="fw-bold">Additional Charges:</strong>
                            </p>
                            <ul class="mb-0 ps-4">
                                <li id="b_pre">Bata</li>
                                <li id="t_pre">Toll</li>
                                <li id="p_pre">Parking</li>
                            </ul>



                        </div>
                        <div class="text-left estimated_fare py-0">
                            <p class="mb-0">
                                <strong>Estimated Fare
                                    (₹):</strong> <span id="pre_fare" class="fw-bold ms-2">1343</span>
                            </p>
                        </div>
                    </div>

                    <div class="row d-block d-md-none">
                        <div class="col-md-6 col-12 ">

                            <p><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong
                                    class="fw-bold">From:</strong> <span id="pre_from5"
                                    onclick="toggleAddress(this)">Mumbai, Maharashtra</span></p>
                            <p><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong
                                    class="fw-bold">To:</strong> <span id="pre_to5" onclick="toggleAddress(this)">Pune,
                                    Maharashtra</span></p>
                            <p><i class="fas fa-calendar-day me-2 text-primary"></i><strong class="fw-bold">Pickup
                                    Date/Time:</strong> <span id="pre_pick5">15 May 2023, 10:00 AM</span>
                            </p>
                            <p><i class="fas fa-calendar-day me-2 text-danger pre_drop_hide"></i><strong
                                    class="fw-bold pre_drop_hide">Drop Date/Time:</strong> <span id="pre_drop5"
                                    class="pre_drop_hide">15 May 2023, 01:30 PM</span></p>


                        </div>
                        <div class="col-md-6 col-12">

                            <p><i class="fas fa-user-friends me-2 text-success"></i><strong class="fw-bold">Passenger
                                    Count:</strong> <span id="pre_pass5">12</span></p>
                            <p><i class="fas fa-ruler-horizontal me-2 text-info"></i><strong class="fw-bold">Distance
                                    (KM):</strong> <span id="pre_dis5">391</span></p>
                            <p class="my-0">
                                <i class="fas fa-check-circle me-2 text-primary"></i>
                                <strong class="fw-bold">Additional Charges:</strong>
                            </p>
                            <ul class="mb-0 ps-4">
                                <li id="b_pre5"></li>
                                <li id="t_pre5"></li>
                                <li id="p_pre5"></li>
                            </ul>



                        </div>
                        <div class="text-left estimated_fare py-0">
                            <p class="mb-0">
                                <strong>Estimated Fare
                                    (₹):</strong> <span id="pre_fare5" class="fw-bold ms-2">1343</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                <button class="btn btn-success" onclick="submitJob()" id="con_create">
                    <i class="fas fa-check-circle me-1"></i> Confirm & Create
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="agreedModal" tabindex="-1" aria-labelledby="agreedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content w-100 p-4 rounded-4 text-center position-relative">

            <!-- Close Button at top-right -->
            <button type="button" class="btn-close close-button position-absolute top-0 end-0 p-2 m-1"
                data-bs-dismiss="modal" aria-label="Close"></button>

            <!-- Header -->
            <div class="modal-header border-0 pb-0 mt-3 d-flex justify-content-center">
                <h5 class="modal-title fw-bold text-center m-0" id="agreedModalLabel">
                    Confirm Bid
                </h5>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <p class="fw-semibold fs-6 mb-2">Do you want to confirm the bid amount of</p>
                <h3 class="text-primary fw-bold" id="bidAmountDisplay">₹0</h3>

                <div class="mt-3 border-top pt-3">
                    <p
                        class="text-danger mb-1 bg-light fw-normal border w-100 rounded p-2 d-flex flex-column align-items-start">
                        <strong class="text-dark">Additional Charges:</strong> <span id="bidAmountDetails"></span>
                    </p>
                </div>
            </div>
            <div class="form-check mt-3 d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" id="termsCheck" required>
                <label class="form-check-label m-0" for="termsCheck">
                    I agree to the User Agreement –
                    <span class="terms-tooltip text-primary fw-bold" data-bs-toggle="tooltip">
                        Disclaimer &amp; Terms
                    </span>
                </label>
            </div>



            <!-- Footer -->
            <div class="modal-footer justify-content-center border-0 pt-3 d-flex gap-2 flex-wrap">
                <button class="btn btn-success fw-bold" id="confirmBidBtn">
                    <i class="fas fa-check me-1"></i> Yes, Agree
                </button>
                <!--<button class="btn btn-danger fw-bold" data-bs-dismiss="modal">-->
                <!--    <i class="fas fa-times me-1"></i> Cancel-->
                <!--</button>-->
            </div>

            <p class="text-muted small">
                Once placed, the bid cannot be edited. Make sure everything looks good before confirming.
            </p>
        </div>

    </div>
</div>


<!-- Temporary Confirmed Modal -->
<div class="modal fade" id="confirmedBidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content w-100 border-0 shadow-lg position-relative">
            <div class="modal-body text-center p-4 bg-light">

                <!-- ✅ Close Button at Top-Right Corner -->
                <button type="button" class="btn-close close-button position-absolute" style="top: 15px; right: 15px;"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>


                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h5 class="fw-bold text-dark mb-2">Bid Placed Successfully!</h5>
                <p class="text-muted mb-3">
                    Your bid of <span class="fw-bold text-dark p-2 fs-4 successBidAmount">₹</span>
                    has been submitted successfully.
                </p>

                <!-- Success Info -->
                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning mb-3">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    <small class="text-dark">You will be notified once the bid is accepted.</small>
                </div>

                <!-- <div class="d-flex justify-content-center">-->
                <!--    <div class="btn btn-success px-4 fw-bold" style="pointer-events: none; cursor: default;">-->
                <!--        <i class="fas fa-thumbs-up me-2"></i>Placed!-->
                <!--    </div>-->
                <!--</div>-->



            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="JobcreatedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            <div class="modal-header p-3  text-white">

                <button type="button" class="btn-close close-button position-absolute" style="top: 15px; right: 15px;"
                    data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body text-center  ">



                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 43px; height: 43px;">
                        <i class="fas fa-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h5 class="fw-bold text-dark mb-2"> Job Created Successfully!</h5>


                <!-- Success Info -->
                <!--<div class="alert alert-warning bg-warning bg-opacity-10 border-warning mb-3">-->
                <!--    <i class="fas fa-info-circle text-warning me-2"></i>-->
                <!--    <small class="text-dark">You will be notified once the bid is accepted through E-mail or Whatsapp.</small>-->
                <!--</div>-->

                <!--               <div class="d-flex justify-content-center mb-3">-->
                <!--  <div class="updated-message animate-updated">-->
                <!--    <i class="fas fa-thumbs-up me-2"></i>Updated!-->
                <!--  </div>-->
                <!--</div>-->


            </div>

        </div>
    </div>
</div>
<!-- User Info Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered"
        style="animation: 0.3s ease-out 0s 1 normal none running modalSlideIn;">
        <div class="modal-content p-3">


            <!-- Body -->
            <div class="modal-body bg-light p-2">
                <!-- Author Card -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3 border border-warning border-opacity-25">
                    <button type="button" class="btn-close close-button position-absolute btn-sm top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                            style="width: 61px; height: 94px; object-fit: cover;" class="rounded" alt="User Image">
                    </div>

                    <div class="pt-3">
                        <div class="d-flex align-items-center justify-content-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0 fw-bold text-dark">Rajesh Kumar</h6>
                            <span class="badge rounded-pill bg-success small">
                                <i class="fas fa-check-circle me-1"></i> Verified
                            </span>
                            <span>
                                <i class="fas fa-star text-warning me-1"></i>
                                <span class="fw-semibold">4.7</span>
                            </span>
                        </div>

                        <div class="d-flex flex-column text-dark small text-center gap-1">
                            <span class="fw-semibold">
                                <i class="fas fa-building text-warning me-1"></i> Sri Hematech Info Solutions
                            </span>
                            <span class="fw-semibold">
                                <i class="fas fa-map-marker-alt text-warning me-1"></i> Perambur, Chennai
                            </span>
                            <span class="fw-semibold">
                                <i class="fa fa-briefcase text-warning me-1"></i> 15 jobs completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <!-- Modal Footer -->
            <div class="modal-footer d-flex justify-content-center py-0 bg-white  flex-nowrap">
                <div class="dropdown flex-fill">
                    <a class="text-danger text-decoration-underline d-flex align-items-center justify-content-center w-100"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-flag me-2"></i> Report User
                    </a>

                    <ul class="dropdown-menu shadow-sm custom-report-dropdown w-100" style="">
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-flag me-2"></i> Report
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-ban me-2"></i> Block &amp; Report
                            </a>
                        </li>
                    </ul>

                    <ul class="dropdown-menu shadow-sm custom-report-dropdown w-100">
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-flag me-2"></i> Report
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-ban me-2"></i> Block &amp; Report
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="modal fade" id="postedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content w-100 p-3 position-relative">

            <!-- Outside Close Button -->
            <button type="button" class="btn-close ms-auto custom-close" data-bs-dismiss="modal"
                aria-label="Close"></button>

            <!-- Body -->
            <div class="modal-body bg-light">
                <!-- Author Card -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3 border border-warning border-opacity-25 text-center">

                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                        style="    width: 88px; height: 92px;object-fit: cover;" class="rounded" alt="User Image"
                        id="post_img">

                    <div class="pt-3">
                        <div class="d-flex align-items-center justify-content-center mb-2 flex-wrap gap-2">
                            <h6 class="mb-0 fw-bold text-dark" id="post_name">Rajesh Kumar</h6>
                            <span class="badge rounded-pill bg-success small">
                                <i class="fas fa-check-circle me-1"></i> Verified
                            </span>
                        </div>

                        <div class="d-flex flex-column text-dark small text-start gap-1">
                            <span class="fw-semibold">
                                <i class="fas fa-building text-warning me-1"></i> <span id="post_comp"></span>
                            </span>
                            <span class="fw-semibold d-none">
                                <i class="fas fa-map-marker-alt text-warning me-1"></i>
                            </span>
                            <span class="fw-semibold">
                                <i class="fa fa-briefcase text-warning me-1"></i><span id="post_complete"></span>
                                <span class="ms-3">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <span class="fw-semibold" id="post_rate"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer d-flex justify-content-center py-0 bg-white  flex-nowrap">
                <div class="dropdown flex-fill">
                    <a class="text-danger text-decoration-underline d-flex align-items-center justify-content-center w-100"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-flag me-2"></i> Report User
                    </a>

                    <ul class="dropdown-menu shadow-sm custom-report-dropdown w-100" style="">
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-flag me-2"></i> Report
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-ban me-2"></i> Block &amp; Report
                            </a>
                        </li>
                    </ul>

                    <ul class="dropdown-menu shadow-sm custom-report-dropdown w-100">
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-flag me-2"></i> Report
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-dark" href="#">
                                <i class="fas fa-ban me-2"></i> Block &amp; Report
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade " id="carModal2" tabindex="-1" style="display: none; padding-left: 0px;" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content w-100 p-3 position-relative">
            <button type="button" class="btn-close close-button position-absolute top-0 end-0 p-2 m-1"
                data-bs-dismiss="modal" aria-label="Close"></button>


            <!-- Body with light background -->
            <div class="modal-body bg-light p-2 mt-3">
                <!-- Author Card Design -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3  border border-warning border-opacity-25">
                    <!-- Inside modal-content just after opening -->



                    <div class="row align-items-center">
                        <!-- Profile Section - col-8 -->
                        <div class="col-lg-8  col-12 d-flex align-items-start">
                            <div class="d-flex align-items-start flex-grow-1  flex-md-row gap-3">
                                <!-- Profile Image - Full width on mobile, inline on desktop -->
                                <div class="position-relative me-md-3 mb-3 mb-md-0 text-center text-lg-start">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                                        class="img-fluid" style="width: 100%; height: 94px; object-fit: cover;">
                                    <span
                                        class="position-absolute d-none d-md-block bottom-0 end-0 bg-success rounded-circle border-2 border-white"
                                        style="width: 14px; height: 14px;" title="Active now"></span>
                                </div>

                                <!-- Profile Info - Full width on both -->
                                <div class="flex-grow-1 pt-md-1 w-100">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark me-2" id="j_name"></h6>
                                        <span class="badge rounded-pill bg-success small">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </span>
                                    </div>

                                    <!-- First row of details - stacked on mobile -->
                                    <div class="d-flex flex-wrap gap-2 text-dark small mb-0 mb-md-2 mb-sm-0">
                                        <span class="fw-semibold d-block d-md-inline-block mb-1 mb-md-0">
                                            <i class="fas fa-building text-warning me-1"></i><span
                                                id="j_company"></span>
                                        </span>
                                        <span class="fw-semibold d-none mb-1 mb-md-0">
                                            <i class="fas fa-map-marker-alt text-warning me-1"></i><span>Perambur,
                                                Chennai</span>
                                        </span>
                                        <span class="d-block d-md-inline-block">
                                            <i class="fas fa-star text-warning me-1"></i>
                                            <span class="fw-semibold" id="j_rate"></span>
                                        </span>
                                    </div>

                                    <!-- Second row of details - stacked on mobile -->
                                    <div class="d-flex flex-wrap gap-0 gap-md-3 text-dark small d-none d-md-block">
                                        <span class="fw-semibold d-block d-md-inline-block mb-1 mb-md-0">
                                            <i class="fa fa-briefcase text-warning me-1"></i><span class="j_complete">15
                                                jobs completed</span>
                                        </span>
                                        <span class="fw-semibold d-block d-md-inline-block">
                                            <i class="fas fa-calendar text-warning me-1"></i><span class="j_time">Posted
                                                3
                                                hours ago</span>
                                        </span>
                                    </div>


                                    <div
                                        class="d-flex flex-column align-items-center justify-content-center  mt-lg-0 d-block d-md-none">
                                        <div class="d-flex gap-2 mb-0 mb-md-2">
                                            <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1"
                                                disabled="">
                                                <i class="fas fa-comments me-1"></i>
                                            </button>
                                            <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1"
                                                disabled="">
                                                <i class="fas fa-phone me-1"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted text-center">You can contact only if the bid is
                                            accepted</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons - col-4 -->

                        <div
                            class="col-lg-4 col-12 d-flex flex-column align-items-center justify-content-center  mt-lg-0 d-block d-md-none">
                            <div class="d-flex flex-wrap gap-3  text-dark small ">
                                <span class="fw-semibold d-block d-md-inline-block mb-1 mb-md-0">
                                    <i class="fa fa-briefcase text-warning me-1"></i><span class="j_complete">0
                                        Completed</span>
                                </span>
                                <span class="fw-semibold d-block d-md-inline-block">
                                    <i class="fas fa-calendar text-warning me-1"></i><span class="j_time"></span>
                                </span>
                            </div>
                        </div>

                        <div
                            class="col-lg-4 d-flex flex-column align-items-center justify-content-center mt-3 mt-lg-0 d-none d-md-block">
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-comments me-1"></i>
                                </button>
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-phone me-1"></i>
                                </button>
                            </div>
                            <small class="text-muted text-center">You can contact only if the bid is accepted</small>
                        </div>


                    </div>



                </div>

                <!-- Bid Amount Input -->
                <div class="mb-2">
                    <div class="row">
                        <div class="col-6">
                            <p class="my-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong
                                    class="fw-bold">From:</strong> <span id="pre_from4"
                                    onclick="toggleAddress(this)">Perungalathur Bus Stand, Tambaram Corporation, Grand
                                    Southern Trunk Road, Otteri Extension, Kamaraj Nagar, New Perungalathur, Chennai,
                                    Tamil Nadu, India - 600063</span></p>
                        </div>
                        <div class="col-6">
                            <p class="my-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong
                                    class="fw-bold">To:</strong> <span id="pre_to4"
                                    onclick="toggleAddress(this)">Perambur Bus Terminus, Perambur High Road, Chinnaiyan
                                    Colony, Perambur, Chennai, Tamil Nadu, India - 600012</span></p>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-3 mb-3  ">
                                <!-- Pickup with icon -->
                                <span class="d-inline-flex align-items-center gap-1 date-time">
                                    <i class="fa-solid fa-plane-departure text-success"></i>
                                    <strong class="fw-bold">
                                        20 Aug 11:46 AM
                                    </strong>
                                </span>

                                <!-- Dropoff only if date exists -->
                                <span class="d-inline-flex align-items-center gap-1 date-time">

                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="title-mobile d-flex justify-content-center align-items-center fw-bold">
                        <i class="fas fa-gavel me-2 text-warning"></i>
                        Place Your Bid
                    </p>
                    <!-- Label + Buttons Row -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="bidAmount" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-indian-rupee-sign text-warning me-1"></i>
                            Enter Your Bid Amount
                        </label>
                        <!--<div>-->

                        <!--    <button id="saveAmountBtn" class="btn btn-sm btn-warning me-1" onclick="saveField('bidAmount', 'editAmountBtn', 'saveAmountBtn', 'Bid amount')">-->
                        <!--        <i class="fas fa-save"></i>-->
                        <!--    </button>-->

                        <!--    <button id="editAmountBtn" class="btn btn-sm btn-outline-warning d-none" onclick="enableEditing('bidAmount', 'editAmountBtn', 'saveAmountBtn')">-->
                        <!--        <i class="fas fa-edit"></i>-->
                        <!--    </button>-->
                        <!--</div>-->
                    </div>

                    <!-- Input Field -->
                    <div class="input-group">
                        <span class="input-group-text bg-warning text-dark fw-bold border-warning">₹</span>
                        <input type="number" class="form-control border-warning fw-semibold" id="bidAmount"
                            placeholder="e.g., 4200" min="0" style="border-left: none;">
                    </div>

                    <!-- Helper Text -->
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Enter your competitive bid amount
                    </small>
                </div>

                <!-- Remarks Input -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="bidRemarks" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-comment-alt text-warning me-1"></i>
                            Remarks (Optional)
                        </label>
                        <!--<div>-->
                        <!--    <button id="saveRemarksBtn" class="btn btn-sm btn-warning me-1" onclick="saveField('bidRemarks', 'editRemarksBtn', 'saveRemarksBtn', 'Remarks')">-->
                        <!--        <i class="fas fa-save"></i>-->
                        <!--    </button>-->


                        <!--    <button id="editRemarksBtn" class="btn btn-sm btn-outline-warning d-none" onclick="enableEditing('bidRemarks', 'editRemarksBtn', 'saveRemarksBtn')">-->
                        <!--        <i class="fas fa-edit"></i>-->
                        <!--    </button>-->

                        <!--</div>-->
                    </div>
                    <textarea class="form-control border-warning fw-semibold" id="bidRemarks" rows="2"
                        oninput="this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'').slice(0,150)"
                        placeholder="Any special notes or conditions..."></textarea>
                </div>
            </div>

            <!-- Footer with action buttons -->
            <div
                class="modal-footer bg-white border-0 pt-0 d-flex flex-row gap-2 justify-content-center justify-content-sm-end align-items-center">
                <button type="button" class="btn btn-warning text-dark fw-bold px-4 shadow-sm" id="placeBid_btn"
                    onclick="placeBid('10')">
                    <i class="fas fa-gavel me-2"></i>Place Bid
                </button>
                <!--<button type="button" class="btn btn-outline-secondary text-dark px-4" data-bs-dismiss="modal">-->
                <!--    <i class="fas fa-times me-1"></i>Cancel-->
                <!--</button>-->
            </div>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="managebid" tabindex="-1" aria-labelledby="agreedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content  w-100 p-3">
            <div class="modal-body bg-light p-2">
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3  border border-warning border-opacity-25">
                    <button type="button" class="btn-close close-button position-absolute btn-sm top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="row align-items-center">
                        <div class="col-lg-8  col-12 d-flex align-items-start">
                            <div class="d-flex align-items-start flex-grow-1  flex-md-row gap-3">
                                <!-- Profile Image - Full width on mobile, inline on desktop -->
                                <div class="position-relative me-md-3 mb-3 mb-md-0 text-center text-lg-start">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                                        class="img-fluid" style="width: 61px; height: 94px; object-fit: cover;">
                                    <span
                                        class="position-absolute d-none d-md-block bottom-0 end-0 bg-success rounded-circle border-2 border-white"
                                        style="width: 14px; height: 14px;" title="Active now"></span>
                                </div>

                                <!-- Profile Info - Full width on both -->
                                <div class="flex-grow-1 pt-md-1 w-100">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark me-2" id="m_name"></h6>
                                        <span class="badge rounded-pill bg-success small">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </span>
                                    </div>

                                    <!-- First row of details - stacked on mobile -->
                                    <div class="d-flex flex-wrap gap-0 gap-md-2 text-dark small mb-0 mb-md-2">
                                        <span class="fw-semibold d-block d-md-inline-block mb-1 mb-md-0">
                                            <i class="fas fa-building text-warning me-1"></i><span
                                                id="m_company"></span>
                                        </span>
                                        <span class="fw-semibold d-none mb-1 mb-md-0">
                                            <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                        </span>
                                        <span class="d-block d-md-inline-block">
                                            <i class="fas fa-star text-warning me-1"></i>
                                            <span class="fw-semibold" id="m_rate">4.7</span>
                                        </span>
                                    </div>

                                    <!-- Second row of details - stacked on mobile -->
                                    <div class="d-flex flex-wrap gap-0 gap-md-3 text-dark small">
                                        <span class="fw-semibold d-block d-md-inline-block mb-1 mb-md-0">
                                            <i class="fa fa-briefcase text-warning me-1"></i><span
                                                id="m_complete"></span>
                                        </span>
                                        <span class="fw-semibold d-block d-md-inline-block">
                                            <i class="fas fa-calendar text-warning me-1"></i><span id="m_time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - col-4 -->
                        <div
                            class="col-lg-4 col-12 d-flex flex-column align-items-center justify-content-center mt-3 mt-lg-0 ">
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-comments me-1"></i>
                                </button>
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-phone me-1"></i>
                                </button>
                            </div>
                            <small class="text-muted text-center">You can contact only if the bid is accepted</small>
                        </div>
                    </div>



                </div>

                <div class="mb-2">
                    <div class="row">
                        <div class="col-6">
                            <p class="my-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong
                                    class="fw-bold">From:</strong> <span id="pre_from3"
                                    onclick="toggleAddress(this)"></span></p>
                        </div>
                        <div class="col-6">
                            <p class="my-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i><strong
                                    class="fw-bold">To:</strong> <span id="pre_to3"
                                    onclick="toggleAddress(this)"></span></p>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-3 ">
                                <!-- Pickup with icon -->
                                <span class="d-inline-flex align-items-center gap-1 date-time">
                                    <i class="fa-solid fa-plane-departure text-success"></i>
                                    <strong class="fw-bold" id="m_pickup">
                                    </strong>
                                </span>

                                <!-- Dropoff only if date exists -->
                                <span class="d-inline-flex align-items-center gap-1 date-time">

                                </span>
                            </div>
                        </div>
                    </div>
                    <p class="title-mobile d-flex justify-content-center align-items-center fw-bold">
                        <i class="fas fa-gavel me-2 text-warning"></i>
                        Manage Your Bid
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidAmount" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-indian-rupee-sign text-warning me-1"></i> Modify Your Bid Amount
                        </label>
                        <div>
                            <!--<button id="saveManageBidAmountBtn" class="btn btn-sm btn-warning me-1 d-none"-->
                            <!--    onclick="saveBidField('manageBidAmount', 'editManageBidAmountBtn', 'saveManageBidAmountBtn', 'Bid amount')">-->
                            <!--    <i class="fas fa-save"></i>-->
                            <!--</button>-->
                            <button id="editManageBidAmountBtn" class="btn btn-sm btn-outline-warning"
                                onclick="enableBidEdit('manageBidAmount', 'editManageBidAmountBtn', 'saveManageBidAmountBtn')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text bg-warning text-dark fw-bold border-warning">₹</span>
                        <input type="number" class="form-control border-warning fw-semibold" id="manageBidAmount"
                            value="0" min="0" style="border-left: none;" disabled="">
                    </div>

                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i> You can edit your bid amount only once. Make sure it's
                        final.
                    </small>
                </div>

                <!-- Remarks Input -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidRemarks" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-comment-alt text-warning me-1"></i> Remarks (Optional)
                        </label>
                        <div>
                            <button id="saveManageBidRemarksBtn" class="btn btn-sm btn-warning me-1 d-none"
                                onclick="saveBidField('manageBidRemarks', 'editManageBidRemarksBtn', 'saveManageBidRemarksBtn', 'Bid remarks')">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="editManageBidRemarksBtn" class="btn btn-sm btn-outline-warning"
                                onclick="enableBidEdit('manageBidRemarks', 'editManageBidRemarksBtn', 'saveManageBidRemarksBtn')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    <textarea class="form-control border-warning fw-semibold" id="manageBidRemarks" rows="2"
                        placeholder="Enter You Remarks"
                        oninput="this.value = this.value.replace(/[^0-9a-zA-Z ]/g,'').slice(0,150)"
                        disabled=""></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="modal-footer bg-white border-0 pt-0 d-flex flex-row gap-2 justify-content-center justify-content-sm-end align-items-center flex-wrap">
                <button type="button" class="btn btn-warning text-dark fw-bold px-4 shadow-sm"
                    onclick="confirmBidPlacement()" id="manageBit_btn">
                    <i class="fas fa-gavel me-2"></i>Update Bid
                </button>
                <button type="button" class="btn btn-outline-secondary text-dark px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Success Alert Modal -->
<div class="modal fade" id="bidSuccessModal" tabindex="-1" style="display: none;">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content w-100 p-3 position-relative">
            <div class="modal-body text-center p-4 bg-light">

                <!-- ✅ Close Button at Top-Right Corner -->
                <button type="button" class="btn-close close-button position-absolute" style="top: 15px; right: 15px;"
                    onclick="closeModal();"></button>

                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h5 class="fw-bold text-dark mb-2">Bid Placed Successfully!</h5>
                <p class="text-muted mb-3">
                    Your bid of <span class="fw-bold text-dark p-2 fs-4 successBidAmount" id="bid_fare"></span>
                    has been submitted successfully.
                </p>

                <!-- Success Info -->
                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning mb-3">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    <small class="text-dark">Once your bid is accepted, you will get notified by WhatsApp or
                        email.</small>
                </div>

                <!-- Great Button -->
                <!--<button type="button" class="btn btn-success px-4 fw-bold" data-bs-dismiss="modal">-->
                <!--    <i class="fas fa-thumbs-up me-2"></i>Placed!-->
                <!--</button>-->

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="carModal3" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-car me-2"></i>
                    Swift Dzire - Car Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Vehicle Information</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-car me-2 text-primary"></i><strong>Model:</strong> Swift Dzire</li>
                            <li><i class="fas fa-users me-2 text-primary"></i><strong>Seating:</strong> 4 Passengers
                            </li>
                            <li><i class="fas fa-gas-pump me-2 text-primary"></i><strong>Fuel:</strong> Petrol</li>
                            <li><i class="fas fa-cogs me-2 text-primary"></i><strong>Transmission:</strong> Manual</li>
                            <li><i class="fas fa-route me-2 text-primary"></i><strong>Distance:</strong> 120 km covered
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Trip Details</h6>
                        <ul class="list-unstyled">
                            <li><strong>Pickup:</strong> 24 Jul, 10:00 AM</li>
                            <li><strong>Drop-off:</strong> 25 Jul, 10:00 AM</li>
                            <li><strong>Location:</strong> Mumbai</li>
                            <li><strong>Duration:</strong> 24 hours</li>
                            <li><strong>Trip Type:</strong> One Way</li>
                        </ul>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Price Breakdown</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Base Rental:</span>
                            <span>₹2,096</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-road me-1"></i>Toll Charges:</span>
                            <span>₹150</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-wallet me-1"></i>Batta Charges:</span>
                            <span>₹200</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fas fa-parking me-1"></i>Parking Charges:</span>
                            <span>₹100</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total Amount:</span>
                            <span class="text-primary">₹2,546</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-gavel me-2"></i>
                            <strong>12 active bids</strong> for this vehicle.
                        </div>
                        <div class="trip-stats">
                            <div class="stat-item">
                                <i class="fas fa-users text-success"></i>
                                <span>4 Passengers</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-route text-info"></i>
                                <span>120 km Distance</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-clock text-warning"></i>
                                <span>24 Hours Duration</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="bookCar(event)">Book Now</button>
            </div>
        </div>
    </div>
</div>

<div id="saveToast"
    class="toast glassy-toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3 shadow"
    role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999;">
    <div class="d-flex">
        <div class="toast-body">
            <i class="fas fa-check-circle me-1"></i>
            <span id="toastMessage">Bid saved successfully!</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
</div>

<!-- Bid Confirmation Modal -->
<div class="modal fade" id="bidConfirmModal" tabindex="-1" aria-labelledby="bidConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg position-relative">
            <div class="modal-header p-3  text-white">

                <button type="button" class="btn-close close-button position-absolute" style="top: 15px; right: 15px;"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center  ">



                <!-- Success Icon -->
                <div class="mb-3">
                    <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check text-white" style="font-size: 24px;"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h5 class="fw-bold text-dark mb-2"> Updated Bid Placed Successfully!</h5>
                <p class="text-muted mb-3">
                    Your bid of <span class="fw-bold text-dark p-2 fs-4 successBidAmount">₹12</span>
                    has been updated successfully.
                </p>

                <!-- Success Info -->
                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning mb-3">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    <small class="text-dark">You will be notified once the bid is accepted through E-mail or
                        Whatsapp.</small>
                </div>

                <div class="d-flex justify-content-center mb-3">
                    <div class="updated-message animate-updated">
                        <i class="fas fa-thumbs-up me-2"></i>Updated!
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="acceptBidModal" tabindex="-1" aria-labelledby="acceptBidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl "
        style="animation: 0.3s ease-out 0s 1 normal none running modalSlideIn;">
        <div class="modal-content w-100">
            <div class="modal-header">
                <!--<h5 class="modal-title" id="acceptBidModalLabel">Confirm Bid Acceptance</h5>-->
                <button type="button" class="btn-close close-button" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-2">
                <!-- Author Card -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3  border border-warning border-opacity-25">
                    <!-- Inside modal-content just after opening -->



                    <div class="row align-items-center">
                        <!-- Profile Section - col-8 -->
                        <div class="col-lg-8 d-flex align-items-start">
                            <div class="d-flex align-items-start flex-grow-1 flex-column flex-lg-row ">
                                <!-- Profile Image -->
                                <div class="position-relative me-3 text-center text-lg-start">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                                        style="width: 61px; height: 94px; object-fit: cover;">
                                    <span
                                        class="position-absolute d-none d-md-block bottom-0 end-0 bg-success rounded-circle border-2 border-white"
                                        style="width: 14px; height: 14px;" title="Active now"></span>
                                </div>

                                <!-- Profile Info -->
                                <div class="flex-grow-1 pt-1">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark me-2" id="b_name"></h6>
                                        <span class="badge rounded-pill bg-success small">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 text-dark small mb-2">
                                        <span class="fw-semibold">
                                            <i class="fas fa-building text-warning me-1"></i><span
                                                id="b_company"></span>
                                        </span>
                                        <span class="fw-semibold ">
                                            &nbsp;
                                        </span>
                                        <span>
                                            <i class="fas fa-star text-warning me-1"></i>
                                            <span class="fw-semibold" id="b_rate"></span>
                                        </span>
                                        <span class="fw-semibold">
                                            <i class="fa fa-briefcase text-warning me-1"></i><span
                                                id="b_complete"></span>
                                        </span>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - col-4 -->
                        <div
                            class="col-lg-4 d-flex flex-column align-items-center justify-content-center mt-3 mt-lg-0 ">
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-comments me-1"></i>
                                </button>
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-phone me-1"></i>
                                </button>
                            </div>
                            <small class="text-muted text-center">You can contact only if the bid is accepted</small>
                        </div>
                    </div>



                </div>

                <div class="mb-2">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidAmount" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-indian-rupee-sign text-warning me-1"></i> Bid Amount
                        </label>
                        <div>
                            <!--<button id="saveManageBidAmountBtn" class="btn btn-sm btn-warning me-1 d-none"-->
                            <!--    onclick="saveBidField('manageBidAmount', 'editManageBidAmountBtn', 'saveManageBidAmountBtn', 'Bid amount')">-->
                            <!--    <i class="fas fa-save"></i>-->
                            <!--</button>-->

                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text bg-warning text-dark fw-bold border-warning">₹</span>
                        <input type="number" class="form-control border-warning fw-semibold" id="b_amount" value=""
                            min="0" style="border-left: none;" disabled="">
                    </div>


                </div>

                <!-- Remarks Input -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidRemarks" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-comment-alt text-warning me-1"></i> Remarks
                        </label>
                        <div>
                            <button id="saveManageBidRemarksBtn" class="btn btn-sm btn-warning me-1 d-none"
                                onclick="saveBidField('manageBidRemarks', 'editManageBidRemarksBtn', 'saveManageBidRemarksBtn', 'Bid remarks')">
                                <i class="fas fa-save"></i>
                            </button>

                        </div>
                    </div>
                    <textarea class="form-control border-warning fw-semibold" id="b_remark" rows="2"
                        disabled="">This is a default remark.</textarea>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-3">
                <button type="button" class="btn btn-danger px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4 fw-bold" id="confirmAcceptBtn">Yes, Accept </button>
            </div>


        </div>
    </div>
</div>


<div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-labelledby="rejectConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl ">
        <div class="modal-content w-100">
            <div class="modal-header">
                <!--<h5 class="modal-title" id="acceptBidModalLabel">Confirm Bid Acceptance</h5>-->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light p-2">
                <!-- Author Card -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3  border border-warning border-opacity-25">
                    <!-- Inside modal-content just after opening -->



                    <div class="row align-items-center">
                        <!-- Profile Section - col-8 -->
                        <div class="col-lg-8 d-flex align-items-start">
                            <div class="d-flex align-items-start flex-grow-1 flex-column flex-lg-row ">
                                <!-- Profile Image -->
                                <div class="position-relative me-3 text-center text-lg-start">
                                    <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg"
                                        style="width: 61px; height: 94px; object-fit: cover;">
                                    <span
                                        class="position-absolute d-none d-md-block bottom-0 end-0 bg-success rounded-circle border-2 border-white"
                                        style="width: 14px; height: 14px;" title="Active now"></span>
                                </div>

                                <!-- Profile Info -->
                                <div class="flex-grow-1 pt-1">
                                    <div class="d-flex align-items-center mb-1 flex-wrap">
                                        <h6 class="mb-0 fw-bold text-dark me-2" id="r_name"></h6>
                                        <span class="badge rounded-pill bg-success small">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 text-dark small mb-2">
                                        <span class="fw-semibold">
                                            <i class="fas fa-building text-warning me-1"></i><span
                                                id="r_company"></span>
                                        </span>
                                        <span class="fw-semibold d-none">
                                            <i class="fas fa-map-marker-alt text-warning me-1"></i>Perambur, Chennai
                                        </span>
                                        <span>
                                            <i class="fas fa-star text-warning me-1"></i>
                                            <span class="fw-semibold" id="r_rate"></span>
                                        </span>
                                        <span class="fw-semibold">
                                            <i class="fa fa-briefcase text-warning me-1"></i> <span
                                                id="r_complete"></span>
                                        </span>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons - col-4 -->
                        <div
                            class="col-lg-4 d-flex flex-column align-items-center justify-content-center mt-3 mt-lg-0 ">
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-comments me-1"></i>
                                </button>
                                <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1" disabled="">
                                    <i class="fas fa-phone me-1"></i>
                                </button>
                            </div>
                            <small class="text-muted text-center">You can contact only if the bid is accepted</small>
                        </div>
                    </div>



                </div>

                <div class="mb-2">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidAmount" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-indian-rupee-sign text-warning me-1"></i> Bid Amount
                        </label>
                        <div>
                            <!--<button id="saveManageBidAmountBtn" class="btn btn-sm btn-warning me-1 d-none"-->
                            <!--    onclick="saveBidField('manageBidAmount', 'editManageBidAmountBtn', 'saveManageBidAmountBtn', 'Bid amount')">-->
                            <!--    <i class="fas fa-save"></i>-->
                            <!--</button>-->

                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text bg-warning text-dark fw-bold border-warning">₹</span>
                        <input type="number" class="form-control border-warning fw-semibold" id="r_amount" value="4200"
                            min="0" style="border-left: none;" disabled="">
                    </div>


                </div>

                <!-- Remarks Input -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="manageBidRemarks" class="form-label fw-bold text-dark mb-0">
                            <i class="fas fa-comment-alt text-warning me-1"></i> Remarks
                        </label>
                        <div>
                            <button id="saveManageBidRemarksBtn" class="btn btn-sm btn-warning me-1 d-none"
                                onclick="saveBidField('manageBidRemarks', 'editManageBidRemarksBtn', 'saveManageBidRemarksBtn', 'Bid remarks')">
                                <i class="fas fa-save"></i>
                            </button>

                        </div>
                    </div>
                    <textarea class="form-control border-warning fw-semibold" id="r_remark" rows="2"
                        disabled=""></textarea>
                </div>
                <div class="alert alert-warning py-2 px-3 small mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    You can reject <strong class="fw-bold">only one bid</strong>. If you try to reject another bid, you
                    can <strong class="fw-bold">only cancel the job.</strong>.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-button" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn" onclick="">Yes, Reject</button>
            </div>
        </div>
    </div>
</div>


<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileFilterOffcanvas">
    <div class="card-header bg-white border-bottom py-2 position-relative">
        <h5 class="card-title fw-bold mb-2 text-dark text-center">
            <i class="fas fa-filter text-warning me-2"></i>Filters
        </h5>

        <!-- Close Button (Top Right) -->
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>

        <div class="d-flex justify-content-center">
            <button class="btn btn-sm btn-link text-muted p-0 me-3" onclick="resetFilter()" id="resetFiltersBtn">
                <i class="fas fa-sync-alt me-1"></i>Reset
            </button>
            <!--<button class="btn btn-sm btn-link text-muted p-0" id="saveFiltersBtn">-->
            <!--    <i class="fas fa-save me-1"></i>Save-->
            <!--</button>-->
        </div>
    </div>

    <div class="offcanvas-body">
        <div class="card filter shadow-sm border-0 rounded-3">

            <div class="card-body p-3">
                <!-- Location Filter -->
                <div class="mb-4 position-relative">
                    <label for="filterLocation" class="form-label fw-bold small mb-2 text-dark">
                        <i class="fas fa-map-marker-alt text-warning me-2"></i>Location
                        <!--<button type="button" class="btn btn-sm btn-outline-warning ms-2 p-0 px-2" id="addLocationBtn" title="Add location">-->
                        <!--    <i class="fas fa-plus"></i>-->
                        <!--</button>-->
                    </label>
                    <div id="locationFields">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute text-warning"
                                style="left: 10px; top: 50%; transform: translateY(-50%); "></i>
                            <!--<input type="text"-->
                            <!--    class="form-control form-control-sm border-warning location-input text-center mb-2 mainLocationInput"-->
                            <!--    placeholder="search" autocomplete="off">-->

                            <div class="col-md-6 col-12" id="loc_m">
                                <!--<select id="location_input_m" name="location_input_m" class="form-control" style="width: 100%;">-->
                                <!--    <option></option>-->
                                <!--</select>-->
                            </div>
                            <!-- Location Suggestions Dropdown -->
                            <div class="search-dropdown position-absolute w-100 bg-white shadow-lg"
                                id="locationDropdown" style="display: none; top: 100%; z-index: 1000;">
                                <div class="p-3">
                                    <h6 class="popular-searches-title mb-3">Popular
                                        Locations</h6>
                                    <div class="trending-tags">
                                        <span class="badge go-ride-tag me-2 mb-2">
                                            <i class="fa-solid fa-circle-plus me-2"></i>Chennai
                                        </span>
                                        <span class="badge go-ride-tag me-2 mb-2">
                                            <i class="fa-solid fa-circle-plus me-2"></i>Bangalore
                                        </span>
                                        <span class="badge go-ride-tag me-2 mb-2">
                                            <i class="fa-solid fa-circle-plus me-2"></i>Hyderabad
                                        </span>
                                        <span class="badge go-ride-tag me-2 mb-2">
                                            <i class="fa-solid fa-circle-plus me-2"></i>Mumbai
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="filter_m">


                </div>



            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<!-- JavaScript -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>
<script>
    $(function () {
        
        $('#dateRangePicker').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: { cancelLabel: 'Clear' },
            ranges: {
                'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                'Next 7 Days': [moment().add(1, 'days'), moment().add(7, 'days')],
                'Next 15 Days': [moment().add(1, 'days'), moment().add(15, 'days')]
            }
        });

        $('#dateRangePicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' → ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#dateRangePicker').on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    });

    function setSort(label, type, checkbox) {
        // Uncheck all other checkboxes (single selection)
        document.querySelectorAll('.dropdown-menu input[type="checkbox"]').forEach(cb => {
            if (cb !== checkbox) cb.checked = false;
        });

        // Update dropdown button text
        document.getElementById("sortMenu").innerHTML = label;

        // Trigger sorting logic
        sortJobs(type);
    }

    function sortJobs(type) {
        console.log("Sorting by:", type);

        // Add your job sorting logic here
        switch (type) {
            case 'distance-asc':
                // sort by distance low → high
                break;
            case 'distance-desc':
                // sort by distance high → low
                break;
            case 'amount-asc':
                // sort by amount low → high
                break;
            case 'amount-desc':
                // sort by amount high → low
                break;
            case 'passenger-asc':
                // sort by passengers low → high
                break;
            case 'passenger-desc':
                // sort by passengers high → low
                break;
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const tooltipTriggers = document.querySelectorAll('.terms-tooltip');

        const tooltipContent = `
    <div class="tooltip-box">
      <p>This platform is only a job portal designed to connect drivers and customers.</p>
      <p>The company does not take any responsibility for:</p>
      <ul>
        <li>Personal information shared between users</li>
        <li> Any payments, transactions, or money exchanges</li>
        <li> Accuracy of details provided by drivers or customers</li>
        <li> Safety, reliability, or quality of rides and services</li>
      </ul>
      <p>Note: All activities on this platform are done at your own choice and risk. Users are advised to verify details independently and exercise caution at all times.</p>
    </div>
  `;

        tooltipTriggers.forEach(el => {
            new bootstrap.Tooltip(el, {
                html: true,
                title: tooltipContent,
                placement: 'top',
                sanitize: false,
                trigger: 'hover'
            });
        });
    });

</script>
<script>
  
    $(document).ready(function () {
        $(".image-slider").owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            smartSpeed: 800,
            nav: true,
            dots: true
        });
    });

    $(document).ready(function () {


        renderFilters()
        loc_select()

        let debounceTimer;

        $("#filterDistance, #filterAmount, #filterPassengerCount, #location_input").on("input change", function () {
            $("#distanceValue").text("0 - " + $("#filterDistance").val() + " km+");
            $("#amountValue").text("₹0 - ₹" + $("#filterAmount").val() + "+");
            $("#PassengerCount").text("0 - " + $("#filterPassengerCount").val() + "+");



            clearTimeout(debounceTimer); // cancel previous scheduled call
            debounceTimer = setTimeout(function () {
                // console.log('hiiiiiiii')
                myFilterFunction($('#location_input').val(), $("#filterDistance").val(), $("#filterAmount").val(), $("#filterPassengerCount").val());
            }, 1000); // wait 1 second after last change
        });

    });

    // $(window).on("resize", renderFilters);

    function renderFilters() {

        if ($(window).width() < 576) {
            // MOBILE
            $('#filter_d').empty();
            $('#loc_d').empty();

            $('#loc_m').html(
                `
                <select id="location_input" 
                        name="location_input" 
                        class="form-select"
                        style="width: 100%;" 
                        data-placeholder="Select District">
                    <option value=""></option>
                </select>
                `

            );

            $('#filter_m').html(`
                <div class="mb-4">
                    <label for="filterDistance"
                        class="form-label fw-bold small mb-2 text-dark">
                        <i class="fas fa-route text-warning me-2"></i>Distance Range:
                        <span id="distanceValue">0 - 1000 km+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterDistance" min="0" max="1000" step="10" value="0">
                </div>
    
                <div class="mb-4">
                    <label for="filterAmount" class="form-label fw-bold small mb-2 text-dark">
                        Amount Range: <span id="amountValue">₹0 - ₹10000+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterAmount" min="0" max="10000" step="10" value="0">
                </div>
    
                <div class="mb-4">
                    <label for="filterPassengerCount" class="form-label fw-bold small mb-2 text-dark">
                        PassengerCount: <span id="PassengerCount">0 - 100+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterPassengerCount" min="0" max="100" step="10" value="0">
                </div>
    
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm text-white bg-primary py-1" id="saveFiltersBtn" onclick="storeFilter()">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            `);

        } else {
            // DESKTOP
            $('#filter_m').empty();

            $('#loc_m').empty();

            $('#loc_d').html(
                `
                <select id="location_input" 
                        name="location_input" 
                        class="form-select"
                        style="width: 100%;" 
                        data-placeholder="Select District">
                    <option value=""></option>
                </select>
                `

            );

            $('#filter_d').html(`
                <div class="mb-4">
                    <label for="filterDistance" class="form-label fw-bold small mb-2 text-dark">
                        <i class="fas fa-route text-warning me-2"></i>Distance Range:
                        <span id="distanceValue">0 - 1000 km+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterDistance" min="0" max="1000" step="10" value="0">
                </div>
    
                <div class="mb-4">
                    <label for="filterAmount" class="form-label fw-bold small mb-2 text-dark">
                        Amount Range: <span id="amountValue">₹0 - ₹10000+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterAmount" min="0" max="10000" step="10" value="0">
                </div>
    
                <div class="mb-4">
                    <label for="filterPassengerCount" class="form-label fw-bold small mb-2 text-dark">
                        PassengerCount: <span id="PassengerCount">0 - 100+</span>
                    </label>
                    <input type="range" class="form-range custom-range-warning"
                        id="filterPassengerCount" min="0" max="100" step="10" value="0">
                </div>
    
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn btn-sm text-white bg-primary py-1" id="saveFiltersBtn" onclick="storeFilter()">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            `);
        }

        loc_select()
    }



    function loc_select() {
        // From Location 
        $('#fromPlace').select2({

            dropdownParent: $('#createJobModal'),
            theme: 'bootstrap-5',
            placeholder: 'Select From',
            allowClear: true,
            ajax: {
                url: "{{ env('APP_API') }}getlocation",
                method: 'POST',
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                delay: 250,
                data: function (params) {
                    return { search: params.term || '', countryCode: getCookie('countryCode') };
                },
                processResults: function (data) {
                    data = data.data;
                    return {
                        results: data.map(function (item) {
                            return { id: item.text, text: item.text };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 4
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        const isMobile = window.matchMedia("(max-width: 767.98px)").matches;

        $('#location_input').select2({
            // dropdownParent: $('body'),
            theme: 'bootstrap-5',
            placeholder: 'Select District',
            allowClear: true,
            ...(isMobile && { dropdownParent: $('#mobileFilterOffcanvas') }),
            ajax: {
                url: "{{ env('APP_API') }}get-district",
                method: 'POST',
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                delay: 250,
                data: function (params) {
                    return { search: params.term || '', countryCode: getCookie('countryCode') };
                },
                processResults: function (data) {
                    data = data.data;
                    return {
                        results: data.map(function (item) {
                            return { id: item, text: item };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 4
        }).on('select2:open', function () {
            setTimeout(function () {
                const input = document.querySelector('.select2-container--open .select2-search__field');
                if (input) {
                    input.focus();
                    input.click(); // <-- important for mobile
                }
            }, 100);
        });

        // $('#location_input_m').select2({

        //     // dropdownParent: $('#createJobModal'),
        //     theme: 'bootstrap-5',
        //     placeholder: 'Select District',
        //     allowClear: true,
        //     ajax: {
        //         url: "{{ env('APP_API') }}get-district",
        //         method: 'POST',
        //         dataType: 'json',
        //         headers: {
        //             "Authorization": "Bearer " + getCookie('sessionToken')
        //         },
        //         delay: 250,
        //         data: function (params) {
        //             return { search: params.term || '', countryCode: getCookie('countryCode') };
        //         },
        //         processResults: function (data) {
        //             data = data.data;
        //             return {
        //                 results: data.map(function (item) {
        //                     return { id: item, text: item };
        //                 })
        //             };
        //         },
        //         cache: true
        //     },
        //     minimumInputLength: 4
        // }).on('select2:open', function () {
        //     document.querySelector('.select2-search__field').focus();
        // });


        //  Location 
        $('#toPlace').select2({
            dropdownParent: $('#createJobModal'),
            theme: 'bootstrap-5',
            placeholder: 'Select To',
            allowClear: true,
            ajax: {
                url: "{{ env('APP_API') }}getlocation",
                method: 'POST',
                dataType: 'json',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                delay: 250,
                data: function (params) {
                    return { search: params.term || '', countryCode: getCookie('countryCode') };
                },
                processResults: function (data) {
                    data = data.data;
                    return {
                        results: data.map(function (item) {
                            return { id: item.text, text: item.text };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 4
        }).on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('#fromPlace, #toPlace, input[name="job_type"]').change(function () {
            let fromPlace = $('#fromPlace').val();
            let toPlace = $('#toPlace').val();

            if (fromPlace != '' && toPlace != '' && $('input[name="job_type"]:checked').val() != '') {

                if ($('input[name="job_type"]:checked').val() == 'oneway') {
                    $('#dropDate').val('');
                }

                let formData = new FormData();
                formData.append("from", fromPlace);
                formData.append("to", toPlace);
                formData.append("way_type", $('input[name="job_type"]:checked').val());

                $.ajax({
                    url: "{{ env('APP_API') }}getdistance",
                    type: 'POST',
                    headers: {
                        "Authorization": "Bearer " + getCookie('sessionToken')
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status) {
                            $('#approx_fare').text(response.data.fare);
                            $('#distance').val(response.data.distance);
                            setCookie('duration', response.data.duration)
                        } else {
                            showToast('error', response.message, 3000);
                        }
                    },
                    error: function () {
                        showToast('error', 'Something went wrong!', 3000);
                    }
                });
            }
        });



    };

    document.addEventListener("DOMContentLoaded", function () {
        if (window.innerWidth < 576) {
            // Mobile → make mobile Open Jobs active
            document.getElementById("available-tab").classList.add("active");
            document.getElementById("available-tab").setAttribute("aria-selected", "true");
        } else {
            // Desktop → make desktop Open Jobs active
            document.getElementById("available-tab-desktop").classList.add("active");
            document.getElementById("available-tab-desktop").setAttribute("aria-selected", "true");
        }

        // Ensure the tab pane itself is active
        document.getElementById("available").classList.add("show", "active");
    });

    function toggleAddress(el) {
        el.classList.toggle("expanded");
    }
    const selectedContainer = document.getElementById("selectedLocations");
    const locationOptions = document.querySelectorAll(".location-option");
    const locationDropdown = document.getElementById("locationDropdown");
    // const input = document.getElementById("mainLocationInput");

    // // Show dropdown when typing
    // input.addEventListener("focus", () => {
    //     locationDropdown.style.display = "block";
    // });

    // // Add selected location
    // locationOptions.forEach(option => {
    //     option.addEventListener("click", () => {
    //         const locName = option.textContent.trim();

    //         // Create pill with remove option
    //         const pill = document.createElement("span");
    //         pill.className = "badge bg-warning text-dark d-flex align-items-center";
    //         pill.innerHTML = `
    //     ${locName}
    //     <i class="fa-solid fa-times ms-2 cursor-pointer"></i>
    //   `;

    //         // Remove on X click
    //         pill.querySelector("i").addEventListener("click", () => {
    //             pill.remove();
    //         });

    //         selectedContainer.appendChild(pill);
    //         input.value = ""; // clear input after selection
    //     });
    // });

    // // Hide dropdown if click outside
    // document.addEventListener("click", (e) => {
    //     if (!e.target.closest(".position-relative")) {
    //         locationDropdown.style.display = "none";
    //     }
    // });
    // $(document).on('click', '#liked-tab', function () {
    //   likedJob();
    // });

    // By Elavarasan --- Start
    $(document).ready(function () {
        $('.agreeBtn').on('click', function () {
            const enteredAmount = parseInt($('#bidInput').val()) || 0;
            $('#bidAmountDisplay').text('₹' + enteredAmount);

        });


        // $('#available-tab').on('click', function () {
        //     openJobs();
        // });
        // $('#created-tab').on('click', function () {
        //     currentJobs();
        // });
        // $('#bidding-tab').on('click', function () {
        //     bidStatus();
        // });
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            // console.log($(e.target).attr('id'))
            // Check if the shown tab is the one you want
            if ($(e.target).attr('id') === 'liked-tab-desktop' || $(e.target).attr('id') === 'liked-tab') {
                likedJob();
            }

            if ($(e.target).attr('id') === 'bidding-tab-desktop' || $(e.target).attr('id') === 'bidding-tab') {
                bidStatus();
            }

            if ($(e.target).attr('id') === 'current-tab' || $(e.target).attr('id') === 'created-tab-desktop') {
                currentJobs();
            }

            if ($(e.target).attr('id') === 'created-tab') {
                currentJobs();
                pastJobs();
            }

            if ($(e.target).attr('id') === 'past-tab') {
                pastJobs();
            }

            if ($(e.target).attr('id') === 'available-tab' || $(e.target).attr('id') === 'available-tab-desktop') {
                openJobs(null, null, null, null, 'yes');
            }
        });

        $('#confirmBidBtn').on('click', function () {

            $('#agreedModal').modal('hide');

            $('#confirmedBidModal').modal('show');
            // Show confirmation modal for 1 second


            setTimeout(function () {
                $('#confirmedBidModal').modal('hide');
            }, 5000); // 60,000 milliseconds = 1 minute

            // Update only the clicked button
            clickedButton
                .removeClass('btn-success')
                .addClass('btn-warning')
                .html('<i class="fas fa-cogs me-1"></i> Manage Bid')
                .attr('data-bs-toggle', 'modal')
                .attr('data-bs-target', '#managebid');

            // Hide the "Place bid" section near the clicked button
            clickedButton.closest('#actionSection').find('.place-bid-section').hide();
        });

    });

    function journeyForm(event) {
        event.preventDefault();

        let form = event.target;
        let errors = [];
        let firstInvalid = null;

        // Required fields
        const requiredFields = [
            { name: "job_type", label: "One Way Or Round Trip", type: "radio" },
            { name: "from_place", label: "From Place", type: "text" },
            { name: "to_place", label: "To Place", type: "text" },
            { name: "pickup_date", label: "PickyUp Date", type: "text" },
            { name: "dropoff_date", label: "DropOff Date", type: "text" },
            { name: "pass_count", label: "Passenger Count", type: "text" },
            { name: "fare", label: "Fare", type: "text" },
            { name: "distance", label: "Distance", type: "text" },
            { name: "bataRadio", label: "Bata", type: "radio" },
            { name: "tollRadio", label: "Toll", type: "radio" },
            { name: "parkingRadio", label: "Parking", type: "radio" },
            { name: "job_remark", label: "Remarks", type: "text" }
        ];
        let job_remark = '';
        // Validation loop
        let isRound = 0;
        requiredFields.forEach(field => {
            let input = $(form).find(`[name="${field.name}"]`);
            if (field.name != 'job_remark') {
                let isEmpty = (field.type === "radio")
                    ? $('input[name="' + field.name + '"]:checked').val() == ''
                    : $.trim(input.val()) == "";

                if (isEmpty) {

                    if (isRound && field.name == 'dropoff_date') {
                        errors.push(`${field.label} is required`);
                        if (!firstInvalid) firstInvalid = input;
                    } else if (field.name != 'dropoff_date') {
                        errors.push(`${field.label} is required`);
                        if (!firstInvalid) firstInvalid = input;
                    }
                } else {

                    if (field.name == 'job_type' && $('input[name="' + field.name + '"]:checked').val() == 'roundtrip') {
                        isRound = 1;
                    }

                    if (field.name === 'pickup_date' || field.name === 'dropoff_date') {
                        let selectedDate = new Date(input.val());
                        let now = new Date();

                        if (selectedDate < now) {
                            errors.push(`Invalid ${field.label} `);
                            // input.val("");
                            // return false;
                        }
                    }


                }

            } else {
                job_remark = $.trim(input.val());
            }
        });

        // Show errors
        if (errors.length > 0) {

            showToast('error', errors.join("<br>"), 5000);
            if (firstInvalid) firstInvalid.focus();

            return;
            $('#jobPreviewModal').modal('hide')
        }

        // Prepare FormData
        let formData = new FormData(form);
        formData.append("job_remark", job_remark);
        formData.append("duration", getCookie('duration'));

        // Convert FormData to plain object
        let formObject = {};
        formData.forEach((value, key) => {
            formObject[key] = value;
        });


        $('#pre_pass').text(formObject.pass_count);
        $('#pre_pass5').text(formObject.pass_count);

        $('#pre_dis').text(formObject.distance);
        $('#pre_dis5').text(formObject.distance);

        $('#pre_fare').text(formObject.fare);
        $('#pre_fare5').text(formObject.fare);

        $('#pre_from').text(formObject.from_place);
        $('#pre_to').text(formObject.to_place);
        $('#pre_duration').text(formObject.duration);
        $('#pre_duration5').text(formObject.duration);

        $('#pre_from2').text(formObject.from_place);
        $('#pre_from5').text(formObject.from_place);

        $('#pre_to2').text(formObject.to_place);
        $('#pre_to5').text(formObject.to_place);

        $('#pre_pick').text(formObject.pickup_date != '' ? formatDate(formObject.pickup_date) : '');
        $('#pre_pick2').text(formObject.pickup_date != '' ? formatDate(formObject.pickup_date) : '');
        $('#pre_pick5').text(formObject.pickup_date != '' ? formatDate(formObject.pickup_date) : '');

        if (formObject.dropoff_date != '') {
            $('.pre_drop_hide').show();
        } else {
            $('.pre_drop_hide').hide();
        }

        $('#pre_drop').text(formObject.dropoff_date != '' ? formatDate(formObject.dropoff_date) : 'NA');
        $('#pre_drop5').text(formObject.dropoff_date != '' ? formatDate(formObject.dropoff_date) : 'NA');

        $('#b_pre').text(formObject.bataRadio == 'Included' ? 'Bata Included' : 'Bata Passenger to Pay');
        $('#t_pre').text(formObject.tollRadio == 'Included' ? 'Toll Included' : 'Toll Passenger to Pay');

        $('#p_pre').text(formObject.parkingRadio == 'Included' ? 'Parking Included' : 'Parking Passenger to Pay');
        $('#b_pre5').text(formObject.bataRadio == 'Included' ? 'Bata Included' : 'Bata Passenger to Pay');

        $('#t_pre5').text(formObject.tollRadio == 'Included' ? 'Toll Included' : 'Toll Passenger to Pay');
        $('#p_pre5').text(formObject.parkingRadio == 'Included' ? 'Parking Included' : 'Parking Passenger to Pay');

        // if(formObject.dropoff_date == ''){
        //     $('.pre_drop_hide').hide();
        // }else{
        //     $('.pre_drop_hide').show();

        // }

        // Save to localStorage
        localStorage.setItem('formData', JSON.stringify(formObject));

        $('#jobPreviewModal').modal('show')


        // Submit via AJAX
        // $.ajax({
        //     url: "{{ env('APP_API') }}create-job",
        //     type: 'POST',
        //     headers: {
        //         "Authorization": "Bearer "+ getCookie('sessionToken')
        //     },
        //     data: formData,
        //     contentType: false,
        //     processData: false,
        //     beforeSend: function () {
        //         let btn = $("#job_submit-btn");
        //         btn.prop('disabled', true)
        //           .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
        //     },
        //     success: function (response) {
        //         if(response.status){
        //             showToast('success', response.message, 3000);
        //             location.reload();
        //         }else{
        //             showToast('error', response.message, 3000);
        //         }
        //     },
        //     error: function () {
        //         showToast('error', 'Something went wrong!', 3000);
        //     },
        //     complete: function () {
        //         let btn = $("#job_submit-btn");
        //         btn.prop('disabled', false).html('Update');
        //     }
        // });
    }

    function formatDate(dateString) {
        let date = new Date(dateString);
        let options = {
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        return date.toLocaleString('en-GB', options).replace(',', '');
    }

    // function toggleLike(el) {
    //     el.classList.toggle('liked');
    // }
    let pendingRejectId = null;

    // Step 1: Open modal and store ID
    function showRejectModal(id) {
        pendingRejectId = id;
        const modal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
        modal.show();
    }

    // Step 2: When "Yes, Reject" is clicked, call rejectBid(id)
    document.addEventListener('DOMContentLoaded', function () {
        const confirmBtn = document.getElementById('confirmRejectBtn');
        confirmBtn.addEventListener('click', function () {
            if (pendingRejectId !== null) {
                rejectBid(pendingRejectId);

                // Close the modal
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('rejectConfirmModal'));
                modalInstance.hide();

                pendingRejectId = null;
            }
        });
    });


    //   function loadPlaces(selectId, apiEndpoint, selectTxt) {
    //         let formData = new formData();
    //         formData.append('search', selectTxt);
    //         formData.append('countryCode', getCookie('countryCode'));
    //         $.ajax({
    //             url: apiEndpoint,
    //             type: 'GET',
    //             headers: {
    //                 "Authorization": "Bearer " + getCookie('sessionToken')
    //             },
    //             data: formData,
    //             beforeSend: function () {
    //                 $("#" + selectId).html('<option>Loading...</option>');
    //             },
    //             success: function (data) {
    //                 let select = $("#" + selectId);
    //                 select.empty().append('<option value="">Select</option>');
    //                 $.each(data, function (index, place) {
    //                     select.append('<option value="' + place.id + '">' + place.name + '</option>');
    //                 });
    //             },
    //             error: function () {
    //                 $("#" + selectId).html('<option>Error loading data</option>');
    //             }
    //         });
    //     }


    // Step 3: Your original rejectBid function
    function rejectBid(id) {
        const cardToRemove = document.querySelector(`.bid-card[data-card-id="${id}"]`);

        if (cardToRemove) {
            cardToRemove.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardToRemove.style.opacity = '0';
            cardToRemove.style.transform = 'scale(0.9)';

            setTimeout(() => {
                cardToRemove.remove();

                // Re-enable all other cards
                const allBids = document.querySelectorAll('.bid-card');
                allBids.forEach(card => {
                    card.classList.remove('disabled-bid');
                    const elements = card.querySelectorAll('button, .info-icon, .flag-icon');
                    elements.forEach(el => {
                        if (el.tagName === 'BUTTON') el.disabled = false;
                        el.classList.remove('disabled-bid');
                    });
                });
            }, 500);
        }
    }
    // Global variable to store the current bid ID
    let currentBidId = null;

    // Modified accept button to show modal first


    // Original function remains unchanged
    function acceptBid(id) {
        console.log(id)
        const selectedCard = document.querySelector(`.bid-card[data-card-id="${id}"]`);
        const parentCompactCard = selectedCard.closest('.compact-car-card');

        const bidActions = parentCompactCard.querySelector(`#actions-${id}`);
        const postAccept = parentCompactCard.querySelector(`#post-accept-${id}`);
        if (bidActions && postAccept) {
            bidActions.classList.add('d-none');
            postAccept.classList.remove('d-none');
        }

        // Disable only sibling bids within the same .compact-car-card
        const allBids = parentCompactCard.querySelectorAll('.bid-card');
        allBids.forEach(card => {
            const cardId = card.getAttribute('data-card-id');
            if (parseInt(cardId) !== id) {
                card.classList.add('disabled-bid');
                const elements = card.querySelectorAll('button, .info-icon, .flag-icon');
                elements.forEach(el => {
                    if (el.tagName === 'BUTTON') el.disabled = true;
                    el.classList.add('disabled-bid');
                });
            }
        });
    }

    // Event listener for the confirm button in modal
    document.getElementById('confirmAcceptBtn').addEventListener('click', function () {
        if (currentBidId) {
            acceptBid(currentBidId);
            const modal = bootstrap.Modal.getInstance(document.getElementById('acceptBidModal'));
            modal.hide();
        }
    });


    function rejectBid(id) {
        const cardToRemove = document.querySelector(`.bid-card[data-card-id="${id}"]`);

        if (cardToRemove) {
            // Apply inline animation styles
            cardToRemove.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            cardToRemove.style.opacity = '0';
            cardToRemove.style.transform = 'scale(0.9)';

            // Wait for animation to complete
            setTimeout(() => {
                cardToRemove.remove();

                // Re-enable all other cards
                const allBids = document.querySelectorAll('.driver-list');
                allBids.forEach(card => {
                    card.classList.remove('disabled-bid');
                    const elements = card.querySelectorAll('button, .info-icon, .flag-icon');
                    elements.forEach(el => {
                        if (el.tagName === 'BUTTON') el.disabled = false;
                        el.classList.remove('disabled-bid');
                    });
                });
            }, 500); // Same as transition duration
        }
    }




    function toggleRemarks(elem) {
        const remarks = elem.closest('.bid-card').querySelector('.glassy-remarks');
        remarks.style.display = remarks.style.display === 'none' ? 'block' : 'none';
    }





    function toggleRemarks(iconEl) {
        const remarks = iconEl.closest('.bid-card').querySelector('.glassy-remarks');
        const isVisible = remarks.style.display === 'block';
        document.querySelectorAll('.glassy-remarks').forEach(el => el.style.display = 'none');
        if (!isVisible) {
            remarks.style.display = 'block';
        }
    }

    // function toggleFlagOptions(iconEl) {
    //     const flagBox = iconEl.closest('.bid-card').querySelector('.stylish-flags');
    //     const isVisible = flagBox.style.display === 'block';
    //     document.querySelectorAll('.stylish-flags').forEach(el => el.style.display = 'none');
    //     if (!isVisible) {
    //         flagBox.style.display = 'block';
    //     }
    // }

    function setActiveSort(element) {
        // Remove active class from all items
        document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to clicked item
        element.classList.add('active');
    }
    // function sortCards(type, element) {
    //     const label = element.getAttribute('data-label');
    //     document.getElementById('selectedSort').textContent = label;

    //     // Remove 'active' from all dropdown items
    //     document.querySelectorAll('.sort-dropdown .dropdown-item').forEach(item => {
    //         item.classList.remove('active');
    //     });

    //     // Add 'active' to selected item
    //     element.classList.add('active');

    //     // TODO: Call your sorting logic here
    //     console.log("Sorting by:", type);
    // }

    // let currentlyAcceptedCard = null;
    // function acceptBid(cardId) {

    //     document.querySelectorAll('.bid-card').forEach(card => {
    //         if (card.dataset.cardId !== cardId.toString()) {
    //             card.classList.add('disabled-card');
    //             card.style.opacity = '0.6';
    //             card.style.pointerEvents = 'none';
    //         }
    //     });

    //     const card = document.querySelector(`.bid-card[data-card-id="${cardId}"]`);


    //     card.classList.add('accepted');


    //     const mainButtons = card.querySelector('.main-buttons');
    //     mainButtons.style.opacity = '0';
    //     mainButtons.style.transform = 'translateY(10px)';


    //     const commIcons = card.querySelector('.communication-icons');
    //     commIcons.style.display = 'flex';

    //     setTimeout(() => {
    //         mainButtons.style.display = 'none';
    //         commIcons.style.opacity = '1';
    //         commIcons.style.transform = 'translateY(0)';
    //     }, 50);


    //     const remarksContent = card.querySelector('.remarks-content');
    //     const flagOptions = card.querySelector('.flag-options');
    //     if (remarksContent) remarksContent.style.display = 'none';
    //     if (flagOptions) flagOptions.style.display = 'none';
    // }
    // function rejectBid(cardId) {
    //     const card = document.querySelector(`.bid-card[data-card-id="${cardId}"]`);


    //     card.style.transform = 'translateX(100%)';
    //     card.style.opacity = '0';
    //     card.style.transition = 'all 0.3s ease';


    //     setTimeout(() => {
    //         card.remove();


    //         if (card.classList.contains('accepted')) {
    //             document.querySelectorAll('.bid-card.disabled-card').forEach(card => {
    //                 card.classList.remove('disabled-card');
    //                 card.style.opacity = '1';
    //                 card.style.pointerEvents = 'auto';
    //             });
    //         }
    //     }, 300);
    // }

    function resetCard(cardId) {
        const card = document.querySelector(`.bid-card[data-card-id="${cardId}"]`);
        if (!card) return;

        const mainButtons = card.querySelector('.main-buttons');
        const commIcons = card.querySelector('.communication-icons');

        card.classList.remove('accepted');
        commIcons.style.display = 'none';
        commIcons.style.opacity = '0';
        mainButtons.style.display = 'block';
        mainButtons.style.opacity = '1';
        mainButtons.style.transform = 'translateY(0)';
    }


    // function toggleFlagOptions(element) {

    //     document.querySelectorAll('.flag-options').forEach(flag => {
    //         if (flag !== element.nextElementSibling) {
    //             flag.style.display = 'none';
    //         }
    //     });

    //     const flagOptions = element.nextElementSibling;
    //     flagOptions.style.display = flagOptions.style.display === 'block' ? 'none' : 'block';
    // }
    // function toggleRemarks(header) {
    //     const content = header.nextElementSibling;
    //     const icon = header.querySelector('.expand-icon');

    //     if (content.style.display === 'block') {
    //         content.style.display = 'none';
    //         icon.style.transform = 'rotate(0deg)';
    //     } else {
    //         content.style.display = 'block';
    //         icon.style.transform = 'rotate(180deg)';
    //     }
    // }

    // function toggleFlagOptions(flagIcon) {
    //     event.stopPropagation();


    //     const bidCard = flagIcon.closest('.bid-card');


    //     const options = bidCard.querySelector('.flag-options');

    //     if (!options) {
    //         console.warn('No .flag-options found inside this bid card');
    //         return;
    //     }


    //     document.querySelectorAll('.flag-options').forEach(opt => {
    //         if (opt !== options) opt.style.display = 'none';
    //     });


    //     options.style.display = options.style.display === 'block' ? 'none' : 'block';
    // }



    // Add event listener for the confirm button
    document.querySelector('#previewJobModal .btn-success')?.addEventListener('click', function () {
        // Add your job confirmation logic here
        console.log('Job confirmed!');
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('previewJobModal'));
        modal?.hide();
    });
    // Setup charge radio toggle
    function setupChargeField(inputId, includedId, clientId) {
        const input = document.getElementById(inputId);
        const included = document.getElementById(includedId);
        const client = document.getElementById(clientId);

        if (!input || !included || !client) return;

        function updateInputState() {
            if (included.checked) {
                client.checked = false;
                input.disabled = true;
            } else if (client.checked) {
                included.checked = false;
                input.disabled = false;
            } else {
                input.disabled = false;
            }
        }

        included.addEventListener("change", updateInputState);
        client.addEventListener("change", updateInputState);
    }

    // Trip type toggle functionality
    function initializeTripTypeToggle() {
        const tripTypeRadios = document.querySelectorAll('input[name^="job_type"]');
        tripTypeRadios.forEach((radio) => {
            radio.addEventListener("change", function () {
                updateTripDisplay(this);
            });
        });
    }

    function setupChargeField(amountId, includedId, clientId) {
        const amountField = document.getElementById(amountId);
        const includedRadio = document.getElementById(includedId);
        const clientRadio = document.getElementById(clientId);

        if (!amountField || !includedRadio || !clientRadio) return;

        includedRadio.addEventListener("change", () => {
            if (includedRadio.checked) {
                amountField.disabled = false;
            }
        });

        clientRadio.addEventListener("change", () => {
            if (clientRadio.checked) {
                amountField.disabled = true;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Auto-fill pickup/dropoff times
        const now = new Date();
        const pickupDateTime = document.getElementById("pickupDateTime");
        const dropoffDateTime = document.getElementById("dropoffDateTime");

        if (pickupDateTime && dropoffDateTime) {
            pickupDateTime.value = now.toISOString().slice(0, 16);
            const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            dropoffDateTime.value = tomorrow.toISOString().slice(0, 16);
        }

        // Setup charge fields
        setupChargeField("bata", "bataIncluded", "bataClient");
        setupChargeField("toll", "tollIncluded", "tollClient");
        setupChargeField("parking", "parkingIncluded", "parkingClient");

        // Your custom functions
        initializeTripTypeToggle?.();
        initializeBidAnimation?.();
        setupModalEvents?.();
    });


    // Trip type toggle functionality
    function initializeTripTypeToggle() {
        const tripTypeRadios = document.querySelectorAll('input[name^="job_type"]')

        tripTypeRadios.forEach((radio) => {
            radio.addEventListener("change", function () {
                // updateTripDisplay(this)
            })
        })
    }

    function updateTripDisplay(radio) {
        const card = radio.closest(".car-rental-card")
        const routeMiddle = card.querySelector(".route-middle")
        const tripTypeText = routeMiddle.querySelector(".trip-type")
        const routeIcon = routeMiddle.querySelector(".route-line i")

        if (radio.id.includes("roundTrip")) {
            tripTypeText.textContent = "Round Trip"
            routeIcon.className = "fas fa-exchange-alt"

            // Update the second location to show return
            const locationTimes = card.querySelectorAll(".location-time")
            if (locationTimes.length >= 2) {
                const firstLocation = locationTimes[0].querySelector(".location").textContent
                locationTimes[1].querySelector(".location").textContent = firstLocation
            }
        } else {
            tripTypeText.textContent = "Self Drive"
            routeIcon.className = "fas fa-arrow-right"
        }

        // Add animation effect
        routeMiddle.style.transform = "scale(1.1)"
        setTimeout(() => {
            routeMiddle.style.transform = "scale(1)"
        }, 200)
    }

    // Search cars function
    function searchCars() {
        const pickupLocation = document.getElementById("pickupLocation").value
        const dropoffLocation = document.getElementById("dropoffLocation").value
        const pickupDateTime = document.getElementById("pickupDateTime").value
        const dropoffDateTime = document.getElementById("dropoffDateTime").value

        if (!pickupLocation || !dropoffLocation) {
            alert("Please enter both pickup and drop-off locations")
            return
        }

        if (!pickupDateTime || !dropoffDateTime) {
            alert("Please select pickup and drop-off dates/times")
            return
        }

        // Simulate search loading
        const searchBtn = document.querySelector(".search-bar button")
        const originalText = searchBtn.innerHTML
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...'
        searchBtn.disabled = true

        setTimeout(() => {
            searchBtn.innerHTML = originalText
            searchBtn.disabled = false

            // Update results count
            document.querySelector(".results-header span").textContent =
                Math.floor(Math.random() * 50 + 20) + " of " + Math.floor(Math.random() * 200 + 100) + " results"

            // Show success message
            showNotification("Cars found! Showing results for " + pickupLocation + " to " + dropoffLocation)
        }, 2000)
    }

    // Select car function
    function selectCar(carId) {
        const carModels = ["Swift Dzire", "Creta", "Nexon"]
        const selectedCar = carModels[carId - 1]

        showNotification("Selected " + selectedCar + "! Redirecting to booking page...")

        setTimeout(() => {
            // Simulate redirect to booking page
            console.log("Redirecting to booking page for car ID:", carId)
        }, 1500)
    }

    // Show notification function
    function showNotification(message) {
        // Create notification element
        const notification = document.createElement("div")
        notification.className = "alert alert-success alert-dismissible fade show position-fixed"
        notification.style.cssText = "top: 20px; right: 20px; z-index: 9999; min-width: 300px;"
        notification.innerHTML = `<div class="modal-dialog modal-md">
        <div class="modal-content p-3">
            <!-- Header with purple gradient background -->
            <div class="modal-header text-white border-0">
                <h5 class="modal-title title-mobile fw-bold">
                    <i class="fas fa-gavel me-2 text-warning"></i>
                    Place Your Bid
                </h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal"></button>
            </div>

            <!-- Body with light background -->
            <div class="modal-body bg-light p-2">
                <!-- Author Card Design -->
                <div class="mb-3 bg-white rounded-3 shadow-sm p-3 border border-warning border-opacity-25">
                    <div>
                        <!-- Profile Section -->
                        <div>
                            <div class="position-relative me-lg-3 mb-3 mb-lg-0 flex-shrink-0 text-center">
                                <img src="https://bootstrapdemos.adminmart.com/modernize/dist/assets/images/profile/user-1.jpg" style="width: 61px;height: 94px;object-fit: cover;">
                                
                            </div>

                            <div class="flex-grow-1 pt-3">
                                <div class="d-flex align-items-center mb-1 flex-wrap">
                                    <h6 class="mb-0 fw-bold text-dark me-2">Rajesh Kumar</h6>
                                    <span class="badge rounded-pill bg-success small">
                                        <i class="fas fa-check-circle me-1"></i>Verified
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 text-dark small mb-2">
                                    <span class="fw-semibold">
                                        <i class="fas fa-building text-warning me-1"></i>Sri Hematech Info Solutions
                                    </span>
                                    <span class="fw-semibold">
                                        <i class="fas fa-map-marker-alt text-warning me-1"></i>Perambur, Chennai
                                    </span>
                                    <span>
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="fw-semibold">4.7</span>
                                    </span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-dark small">
                                    <span class="fw-semibold">
                                        <i class="fa fa-briefcase text-warning me-1"></i>15 jobs completed
                                    </span>
                                    <span class="fw-semibold">
                                        <i class="fas fa-calendar text-warning me-1"></i>Posted 3 hours ago
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class=" align-items-center mt-3  text-center">
                            <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1">
                                <i class="fas fa-comments me-1"></i>Chat
                            </button>
                            <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-1">
                                <i class="fas fa-phone me-1"></i>Call
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Bid Amount Input -->
                <div class="mb-2">
                    <!-- Label + Buttons Row -->
                    

                    <!-- Input Field -->
                    

                    <!-- Helper Text -->
                    
                </div>

                <!-- Remarks Input -->
                
            </div>

            <!-- Footer with action buttons -->
            
        </div>
    </div>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

        document.body.appendChild(notification)

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove()
            }
        }, 5000)
    }

    // Filter functionality
    document.addEventListener("change", (e) => {
        if (e.target.type === "checkbox" || e.target.type === "range") {
            filterCars()
        }
    })

    function filterCars(criteria) {
        const cards = document.querySelectorAll(".car-rental-card")

        cards.forEach((card) => {
            const shouldShow = true

            // Add filtering logic based on criteria
            // This is a placeholder for future enhancement

            card.style.display = shouldShow ? "block" : "none"
        })
    }

    // Price tab switching
    document.addEventListener("click", (e) => {
        if (e.target.closest(".price-tab")) {
            // Remove active class from all tabs
            document.querySelectorAll(".price-tab").forEach((tab) => {
                tab.classList.remove("active")
            })

            // Add active class to clicked tab
            e.target.closest(".price-tab").classList.add("active")

            // Update car prices based on selected tab
            const tabLabel = e.target.closest(".price-tab").querySelector(".tab-label").textContent
            updateCarPrices(tabLabel)
        }
    })

    function updateCarPrices(tabType) {
        const priceElements = document.querySelectorAll(".price-section .price")

        priceElements.forEach((priceEl) => {
            const currentPrice = Number.parseInt(priceEl.textContent.replace("₹", "").replace(",", ""))
            let newPrice

            switch (tabType) {
                case "Cheapest":
                    newPrice = Math.floor(currentPrice * 0.8)
                    break
                case "Premium":
                    newPrice = Math.floor(currentPrice * 1.5)
                    break
                default: // Best
                    newPrice = currentPrice
            }

            priceEl.textContent = "₹" + newPrice.toLocaleString()
        })
    }

    // Bid count animation
    function initializeBidAnimation() {
        const bidCounts = document.querySelectorAll(".bid-count span")

        bidCounts.forEach((bidElement) => {
            // Random bid count updates
            setInterval(
                () => {
                    updateBidCount(bidElement)
                },
                Math.random() * 30000 + 20000,
            ) // Random interval between 20-50 seconds
        })
    }

    function updateBidCount(bidElement) {
        const currentText = bidElement.textContent
        const currentCount = Number.parseInt(currentText.split(" ")[0])
        const change = Math.floor(Math.random() * 3) - 1 // -1, 0, or 1
        const newCount = Math.max(1, currentCount + change)

        if (newCount !== currentCount) {
            // Add animation
            bidElement.style.transform = "scale(1.2)"
            bidElement.style.color = "#ff4757"

            setTimeout(() => {
                bidElement.textContent = newCount + " bids"
                bidElement.style.transform = "scale(1)"
                bidElement.style.color = "#ff6b35"
            }, 300)

            // Show notification
            if (newCount > currentCount) {
                showBidNotification("increased", newCount)
            }
        }
    }

    function showBidNotification(type, count) {
        const notification = document.createElement("div")
        notification.className = "alert alert-info position-fixed"
        notification.style.cssText = `
        top: 20px; 
        right: 20px; 
        z-index: 9999; 
        min-width: 250px;
        animation: slideInRight 0.5s ease-out;
    `

        notification.innerHTML = `
        <i class="fas fa-gavel me-2"></i>
        New bid placed! Total: ${count} bids
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `

        document.body.appendChild(notification)

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove()
            }
        }, 4000)
    }

    // Modal events
    function setupModalEvents() {
        const modals = document.querySelectorAll(".modal")

        modals.forEach((modal) => {
            modal.addEventListener("show.bs.modal", function () {
                // Add entrance animation
                this.querySelector(".modal-dialog").style.animation = "modalSlideIn 0.3s ease-out"
            })

            modal.addEventListener("hide.bs.modal", function () {
                // Add exit animation
                this.querySelector(".modal-dialog").style.animation = "modalSlideOut 0.3s ease-in"
            })
        })
    }

    // Book car function
    function bookCar(event) {
        // Show loading state
        const bookBtn = event.target
        const originalText = bookBtn.innerHTML
        bookBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...'
        bookBtn.disabled = true

        // Simulate booking process
        setTimeout(() => {
            // Close modal
            const modalElement = bookBtn.closest(".modal")
            const modal = bootstrap.Modal.getInstance(modalElement)
            modal.hide()

            // Show success message
            showBookingSuccess()

            // Reset button
            setTimeout(() => {
                bookBtn.innerHTML = originalText
                bookBtn.disabled = false
            }, 1000)
        }, 2000)
    }

    function showBookingSuccess() {
        const successAlert = document.createElement("div")
        successAlert.className = "alert alert-success alert-dismissible fade show position-fixed"
        successAlert.style.cssText = `
        top: 50%; 
        left: 50%; 
        transform: translate(-50%, -50%); 
        z-index: 10000; 
        min-width: 400px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    `

        successAlert.innerHTML = `
        <div class="mb-3">
            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
        </div>
        <h5>Booking Confirmed!</h5>
        <p class="mb-0">Your car rental has been successfully booked.</p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `

        document.body.appendChild(successAlert)

        setTimeout(() => {
            if (successAlert.parentNode) {
                successAlert.remove()
            }
        }, 5000)
    }

    // Add CSS animations
    const style = document.createElement("style")
    style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes modalSlideOut {
        from {
            transform: translateY(0);
            opacity: 1;
        }
        to {
            transform: translateY(-50px);
            opacity: 0;
        }
    }
`
    document.head.appendChild(style)

    // Card hover effects
    document.querySelectorAll(".compact-car-card").forEach((card) => {
        card.addEventListener("mouseenter", function () {
            const carIcon = this.querySelector(".car-icon");
            if (carIcon) {
                carIcon.style.transform = "scale(1.2)";
                carIcon.style.color = "#28a745";
            }
        });
    });

    document.querySelectorAll(".compact-car-card").forEach((card) => {
        card.addEventListener("mouseleave", function () {
            const carIcon = this.querySelector(".car-icon");
            if (carIcon) {
                carIcon.style.transform = "scale(1)";
                carIcon.style.color = "#007bff";
            }
        });
    });

    // Utility functions
    function formatCurrency(amount) {
        return new Intl.NumberFormat("en-IN", {
            style: "currency",
            currency: "INR",
            minimumFractionDigits: 0,
        }).format(amount)
    }

    function formatDate(date) {
        return new Intl.DateTimeFormat("en-IN", {
            day: "numeric",
            month: "short",
            hour: "2-digit",
            minute: "2-digit",
        }).format(new Date(date))
    }

    // Price comparison functionality
    // function comparePrices() {
    //     const priceElements = document.querySelectorAll(".amount-value");

    //     const prices = Array.from(priceElements).map((el) => {
    //         return {
    //             element: el,
    //             value: Number.parseInt(el.textContent.replace("₹", "").replace(",", ""))
    //         };
    //     }).filter(item => !isNaN(item.value)); // Filter out invalid numbers

    //     if (prices.length === 0) {
    //         console.warn("No valid price elements found.");
    //         return;
    //     }

    //     prices.sort((a, b) => a.value - b.value);

    //     // Highlight the best deal
    //     if (prices[0] && prices[0].element) {
    //         const card = prices[0].element.closest(".car-rental-card");
    //         if (card) {
    //             card.style.border = "2px solid #28a745";
    //         }
    //     }

    //     return prices;
    // }


    // Initialize price comparison on load
    // setTimeout(comparePrices, 1000)

    // Location autocomplete simulation
    function setupLocationAutocomplete() {
        const locations = [
            "Mumbai Airport",
            "Mumbai Central",
            "Bandra",
            "Andheri",
            "Powai",
            "Delhi Airport",
            "Connaught Place",
            "Gurgaon",
            "Noida",
            "Bangalore Airport",
            "Koramangala",
            "Whitefield",
            "Electronic City",
            "Chennai Airport",
            "T. Nagar",
            "Velachery",
            "OMR",
        ]

        const locationInputs = document.querySelectorAll("#pickupLocation, #dropoffLocation")

        locationInputs.forEach((input) => {
            input.addEventListener("input", function () {
                const value = this.value.toLowerCase()
                if (value.length > 2) {
                    const matches = locations.filter((loc) => loc.toLowerCase().includes(value))

                    // Create dropdown (simplified)
                    console.log("Location suggestions:", matches)
                }
            })
        })
    }

    // Initialize location autocomplete
    setupLocationAutocomplete()

    // Declare bootstrap variable
    const bootstrap = window.bootstrap
</script>

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
    function enableBidEdit(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.disabled = false;
        }
    }


    function saveBidField(inputId, editBtnId, saveBtnId, label) {
        const input = document.getElementById(inputId);
        const editBtn = document.getElementById(editBtnId);
        const saveBtn = document.getElementById(saveBtnId);

        if (input && editBtn && saveBtn) {
            input.disabled = true;
            editBtn.classList.remove('d-none');
            saveBtn.classList.add('d-none');
            showSaveToast(`${label} saved: ${input.value}`);
        }
    }

    // function confirmBidPlacement() {
    //     const amount = document.getElementById("manageBidAmount").value.trim();
    //     const remarks = document.getElementById("manageBidRemarks").value.trim() || "None";

    //     if (amount === "") {
    //         // Optionally show a validation modal or inline message
    //         return;
    //     }

    //     // Set bid amount in confirmation modal
    //     document.getElementByClass("successBidAmount").textContent = `₹${amount}`;

    //     // First hide the "Manage Bid" modal
    //     const manageBidModalEl = document.getElementById("managebid");
    //     const manageBidModal = bootstrap.Modal.getInstance(manageBidModalEl) || new bootstrap.Modal(manageBidModalEl);
    //     manageBidModal.hide();

    //     // After a short delay (to allow closing animation), show the confirmation modal
    //     setTimeout(() => {
    //         const confirmModal = new bootstrap.Modal(document.getElementById("bidConfirmModal"));
    //         confirmModal.show();
    //     }, 300); 
    // }





    function showSaveToast(message) {
        const toastEl = document.getElementById('saveToast');
        const toastBody = document.getElementById('toastMessage');
        toastBody.textContent = message;

        const bsToast = new bootstrap.Toast(toastEl);
        bsToast.show();

        // Automatically hide after 3 seconds
        setTimeout(() => {
            bsToast.hide();
        }, 3000); // 3000ms = 3 seconds
    }



    function saveField(fieldId, editBtnId, saveBtnId, fieldName) {
        const field = document.getElementById(fieldId);
        const editBtn = document.getElementById(editBtnId);
        const saveBtn = document.getElementById(saveBtnId);

        // if (field.value.trim() === "") {
        //     alert(`Please enter ${fieldName.toLowerCase()} before saving.`);
        //     field.focus();
        //     return;
        // }

        field.disabled = true;
        saveBtn.classList.add('d-none');
        editBtn.classList.remove('d-none');

        // Update toast message
        const toastMsg = document.getElementById("toastMessage");
        toastMsg.textContent = `${fieldName} saved successfully!`;

        // Show the toast
        const toastElement = new bootstrap.Toast(document.getElementById('saveToast'));
        toastElement.show();
    }


    function enableEditing(fieldId, editBtnId, saveBtnId) {
        const field = document.getElementById(fieldId);
        const editBtn = document.getElementById(editBtnId);
        const saveBtn = document.getElementById(saveBtnId);

        field.disabled = false;
        field.focus();
        saveBtn.classList.remove('d-none');
        editBtn.classList.add('d-none');
    }



    document.addEventListener('DOMContentLoaded', () => {
        const mainLocationInput = document.getElementById('mainLocationInput');
        const locationDropdown = document.getElementById('locationDropdown');
        const addLocationBtn = document.getElementById('addLocationBtn');
        const locationFields = document.getElementById('locationFields');

        // Show dropdown when typing in main location input
        if (mainLocationInput && locationDropdown) {
            mainLocationInput.addEventListener('focus', () => {
                locationDropdown.style.display = 'block';
            });

            mainLocationInput.addEventListener('input', () => {
                if (mainLocationInput.value.length > 0) {
                    locationDropdown.style.display = 'block';
                } else {
                    locationDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', event => {
                if (!event.target.closest('.position-relative') &&
                    !event.target.closest('#locationDropdown')) {
                    locationDropdown.style.display = 'none';
                }
            });

            document.querySelectorAll('#locationDropdown .go-ride-tag').forEach(tag => {
                tag.addEventListener('click', () => {
                    mainLocationInput.value = tag.textContent.trim();
                    locationDropdown.style.display = 'none';
                });
            });
        }

        // Add new location field
        if (addLocationBtn && locationFields) {
            addLocationBtn.addEventListener('click', () => {
                const newInputDiv = document.createElement('div');
                newInputDiv.className = 'position-relative';
                newInputDiv.innerHTML = `
                <input type="text" class="form-control form-control-sm border-warning location-input mb-2" 
                       placeholder="Add another location" autocomplete="off">
                <button class="btn btn-sm btn-outline-danger remove-location" 
                        type="button" style="position: absolute; right: 5px; top: 5px;">
                    <i class="fas fa-times"></i>
                </button>
            `;
                locationFields.appendChild(newInputDiv);

                // Add event listener to remove button
                newInputDiv.querySelector('.remove-location').addEventListener('click', function () {
                    locationFields.removeChild(newInputDiv);
                });
            });
        }
    });
    function showPlaceBid() {
        document.getElementById('agreeBtn').classList.add('d-none');
        document.getElementById('infoText').classList.remove('d-none');
        document.getElementById('placeBidBtn').classList.remove('d-none');
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('jobSearchInput');
        const dropdown = document.getElementById('searchDropdown');

        if (searchInput && dropdown) {
            searchInput.addEventListener('focus', () => {
                dropdown.style.display = 'block';
            });

            document.addEventListener('click', event => {
                if (!event.target.closest('.search-container')) {
                    dropdown.style.display = 'none';
                }
            });

            document.querySelectorAll('.go-ride-tag').forEach(tag => {
                tag.addEventListener('click', () => {
                    searchInput.value = tag.textContent.trim();
                    dropdown.style.display = 'none';
                });
            });
        }
    });


    // Dropdown toggle
    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        const isVisible = menu.style.display === 'block';
        document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
        if (!isVisible) menu.style.display = 'block';

        // Close on outside click
        document.addEventListener('click', function closeDropdown(e) {
            if (!btn.parentElement.contains(e.target)) {
                menu.style.display = 'none';
                document.removeEventListener('click', closeDropdown);
            }
        });
    }

    // Handle action clicks
    function handleAction(action) {
        alert("You selected: " + action);
    }

    // Read More Toggle
    function toggleRead() {
        const preview = document.getElementById("remarksPreview");
        const toggle = document.getElementById("toggleRemarks");
        preview.classList.toggle("expanded");
        toggle.textContent = preview.classList.contains("expanded") ? "Show less" : "Read more";
    }

    // Edit Toggle
    function toggleEdit() {
        const input = document.getElementById("remarksInput");
        const preview = document.getElementById("remarksPreview");

        if (input.style.display === "none") {
            input.style.display = "block";
            preview.style.display = "none";
            document.getElementById("editRemarks").textContent = "Save";
        } else {
            input.style.display = "none";
            preview.style.display = "block";
            preview.textContent = input.value;
            document.getElementById("editRemarks").textContent = "Edit";
        }
    }

    // Initialize (hide input, show preview)
    document.addEventListener("DOMContentLoaded", () => {
        const remarksInput = document.getElementById("remarksInput");
        if (remarksInput) {
            remarksInput.style.display = "none";
        }

        // ... rest of your initialization logic here
    });


    document.addEventListener("DOMContentLoaded", () => {
        // Get elements
        // const filterDistance = document.getElementById("filterDistance")
        // const distanceValue = document.getElementById("distanceValue")
        // const filterAmount = document.getElementById("filterAmount")
        // const amountValue = document.getElementById("amountValue")
        // const resetFiltersBtn = document.getElementById("resetFiltersBtn")
        // const filterLocation = document.getElementById("filterLocation")
        // const seaterCheckboxes = document.querySelectorAll('.form-check-input[id^="seater"]')
        // const vehicleCheckboxes = document.querySelectorAll('.form-check-input[id^="vehicle"]')

        // Initialize range values
        // if (filterDistance && distanceValue) {
        //     distanceValue.textContent = `0 - ${filterDistance.value} km+`
        //     filterDistance.addEventListener("input", function () {
        //         distanceValue.textContent = `0 - ${this.value} km+`
        //     })
        // }

        // if (filterAmount && amountValue) {
        //     amountValue.textContent = `₹0 - ₹${filterAmount.value}+`
        //     filterAmount.addEventListener("input", function () {
        //         amountValue.textContent = `₹0 - ₹${this.value}+`
        //     })
        // }

        // Reset Filters Functionality
        // if (resetFiltersBtn) {
        //     resetFiltersBtn.addEventListener("click", () => {
        //         // Reset Location
        //         if (filterLocation) filterLocation.value = ""

        //         // Reset Distance Range
        //         if (filterDistance && distanceValue) {
        //             filterDistance.value = filterDistance.max // Set to max for "0 - 1000km+"
        //             distanceValue.textContent = `0 - ${filterDistance.max} km+`
        //         }

        //         // Reset Amount Range
        //         if (filterAmount && amountValue) {
        //             filterAmount.value = filterAmount.max // Set to max for "₹0 - ₹10000+"
        //             amountValue.textContent = `₹0 - ₹${filterAmount.max}+`
        //         }

        //         // Reset Seater Type Checkboxes
        //         seaterCheckboxes.forEach((checkbox) => {
        //             checkbox.checked = false
        //         })

        //         // Reset Vehicle Type Checkboxes
        //         vehicleCheckboxes.forEach((checkbox) => {
        //             checkbox.checked = false
        //         })

        //         // You would typically trigger a filter update here
        //         console.log("Filters reset!")
        //         // Example: triggerFilterUpdate();
        //     })
        // }

        // Example of how you might apply filters (this would need backend integration)
        // function applyFilters() {
        //     const currentFilters = {
        //         location: filterLocation ? filterLocation.value : "",
        //         distance: filterDistance ? filterDistance.value : "",
        //         amount: filterAmount ? filterAmount.value : "",
        //         seaterType: Array.from(seaterCheckboxes)
        //             .filter((cb) => cb.checked)
        //             .map((cb) => cb.value),
        //         vehicleType: Array.from(vehicleCheckboxes)
        //             .filter((cb) => cb.checked)
        //             .map((cb) => cb.value),
        //     }
        //     console.log("Applying filters:", currentFilters)
        //     // Here you would typically send these filters to your backend or update your UI
        // }

        // Add event listeners to trigger applyFilters on change (optional, for real-time filtering)
        // if (filterLocation) filterLocation.addEventListener("input", applyFilters)
        // if (filterDistance) filterDistance.addEventListener("change", applyFilters) // Use 'change' for range to avoid too many updates
        // if (filterAmount) filterAmount.addEventListener("change", applyFilters)
        // seaterCheckboxes.forEach((checkbox) => checkbox.addEventListener("change", applyFilters))
        // vehicleCheckboxes.forEach((checkbox) => checkbox.addEventListener("change", applyFilters))
    })

    // function placeBid() {
    //     const amount = document.getElementById('bidAmount').value.trim();
    //     const remarks = document.getElementById('bidRemarks').value.trim();

    //     if (!amount || isNaN(amount) || amount <= 0) {
    //         alert("Please enter a valid bid amount.");
    //         return;
    //     }

    //     // Inject bid amount into success modal
    //     document.getElementByClass('successBidAmount').textContent = `₹${amount}`;

    //     // Hide bid modal manually
    //     document.getElementById('carModal2').classList.add('show');
    //     document.getElementById('carModal2').style.display = 'block';
    //     document.body.classList.add('modal-open');
    //     document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());

    //     // Show success modal manually
    //     const successModal = document.getElementById('bidSuccessModal');
    //     successModal.classList.add('show');
    //     successModal.style.display = 'block';
    //     document.body.classList.add('modal-open');

    //     // Add backdrop manually
    //     const backdrop = document.createElement('div');
    //     backdrop.className = 'modal-backdrop fade show';
    //     backdrop.id = 'myBackdrop';
    //     document.body.appendChild(backdrop);

    //     // console.log("Bid Amount:", amount);
    //     // console.log("Remarks:", remarks);
    // }

    // function closeModal() {
    //     const successModal = document.getElementById('bidSuccessModal');
    //     successModal.classList.remove('show');
    //     successModal.style.display = 'none';
    //     document.body.classList.remove('modal-open');
    //     document.body.style.overflow = 'auto';

    //     const myBackdrop = document.getElementById('myBackdrop');

    //     if (myBackdrop) {
    //         myBackdrop.remove();
    //     }
    // }


    const tripRadios = document.querySelectorAll('input[name="job_type"]');
    const dropSection = document.getElementById("dropSection");
    const from = document.getElementById("fromPlace");
    const to = document.getElementById("toPlace");
    const distance = document.getElementById("distance");
    const passengerCount = document.getElementById("passengerCount");
    const expectedAmount = document.getElementById("expectedAmount");
    const marketedPrice = document.getElementById("marketedPrice");
    const pickupDate = document.getElementById("pickupDate");
    const expiryDate = document.getElementById("expiryDate");
    const bata = document.getElementById("bata");
    const toll = document.getElementById("toll");
    const parking = document.getElementById("parking");

    const ratePerKmPerPerson = 10;

    function calculateDistance() {
        if (from.value && to.value) {
            let dummyDistance = Math.floor(Math.random() * 100) + 50; // Simulate distance
            distance.value = dummyDistance;
            updateAmount();
        }
    }

    function updateAmount() {
        let dist = parseInt(distance.value) || 0;
        let count = parseInt(passengerCount.value) || 0;

        let totalExtra = (parseInt(bata.value) || 0) +
            (parseInt(toll.value) || 0) +
            (parseInt(parking.value) || 0);

        let total = dist * count * ratePerKmPerPerson + totalExtra;
        expectedAmount.value = isNaN(total) ? '' : total.toFixed(2);
    }

    tripRadios.forEach(radio => {
        radio.addEventListener("change", () => {
            if (radio.value === "roundtrip" && radio.checked) {
                dropSection.classList.remove("d-none");
            } else {
                dropSection.classList.add("d-none");
            }
        });
    });

    // [from, to].forEach(el => el.addEventListener("input", calculateDistance));
    // [passengerCount, distance, bata, toll, parking].forEach(el => el.addEventListener("input", updateAmount));

    pickupDate.addEventListener("change", () => {
        const pickup = pickupDate.value;
        if (pickup) {
            expiryDate.max = pickup;

            // Optional: clear expiry if it's after pickup
            if (expiryDate.value && expiryDate.value > pickup) {
                expiryDate.value = '';
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const createBtn = document.getElementById("createJobBtn");
        if (createBtn) {
            createBtn.addEventListener("click", function () {
                alert("Created successfully!");
            });
        }
    });

    $(document).ready(function () {



        // setInterval(function() {
        //     location.reload();
        // }, 60000);





        var wow = new WOW({
            boxClass: 'wow',
            animateClass: 'animated',
            offset: 0,
            mobile: true,
            live: true,
            callback: function (box) {
            },
            scrollContainer: null
        });
        wow.init();
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


@include('pages.openJobsJs')

@endsection