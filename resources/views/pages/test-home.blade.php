@extends('layouts.app') @section('content') @php $iii = 1;
@endphp
<style>

  .goride-booking  .inclusions-close-btn {

    border: none;
    color: #fff;
    cursor: pointer;
    padding: 4px;
    border-radius: 6px;
  }

  .goride-booking  .inclusions-close-btn:hover {
    background: rgba(255,255,255,0.1);
  }

  .goride-booking .form-group {
      position: relative;
      width: 100%;
  }

  .select2-container{
      height: 50px !important;
  }

  .goride-booking .select2-container {
      width: 100% !important;
      min-width: 100% !important;
  }

  .goride-booking .select2-selection--single {
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
  }


  .goride-booking .select2-selection__rendered {
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      display: block !important;
      padding-right: 40px !important;
      white-space: nowrap !important;
  }

  .goride-booking span.select2.select2-container {
      width: 100% !important;
      min-width: 100% !important;
  }

  .goride-booking .select2-dropdown {
      min-width: 400px !important;
      width: auto !important;

  }


  .goride-booking .select2-results__option {
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      max-width: 100% !important;
  }


  .goride-booking .select2-selection__rendered:hover::after {
      content: attr(title);
      position: absolute;
      bottom: 100%;
      left: 0;
      background: rgba(0, 0, 0, 0.9);
      color: white;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 14px;
      white-space: normal;
      max-width: 400px;
      min-width: 300px;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      pointer-events: none;
  }


  .goride-booking .select2-container {
      width: 100% !important;
      margin-bottom: 0 !important;
  }

  .goride-booking .select2-container--default .select2-selection--single {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15) !important;
      border-radius: 12px !important;
      color: white;
      font-size: 15px;
      padding: 0;
      height: auto;
      min-height: 45px;
      transition: all 0.3s ease;
  }

  .goride-booking .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: white !important;
      font-size: 15px;
      line-height: normal;
      padding: 12px 16px !important;
      padding-right: 30px !important;
  }

  .goride-booking .select2-container--default .select2-selection--single .select2-selection__placeholder {
      color: rgba(255, 255, 255, 0.7) !important;
  }

  .goride-booking .select2-selection__arrow {
      display: block !important;
      height: 100% !important;
      top: 0 !important;
      right: 10px !important;
  }

  .goride-booking .select2-selection__arrow b {
      border-color: white transparent transparent transparent !important;
      border-width: 6px 5px 0 5px !important;
      margin-top: -3px !important;
  }

  .goride-booking .select2-container--default.select2-container--open .select2-selection__arrow b {
      border-color: transparent transparent white transparent !important;
      border-width: 0 5px 6px 5px !important;
      margin-top: -3px !important;
  }

  /* Dropdown styles */
  .goride-booking .select2-container--default .select2-dropdown {
      background: rgba(30, 30, 40, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 12px;
      backdrop-filter: blur(10px);
  }

  .goride-booking .select2-container--default .select2-results__option {
      color: white;
      padding: 12px 16px;
      font-size: 14px;
  }

  .goride-booking .select2-container--default .select2-results__option--highlighted[aria-selected] {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
  }

  .goride-booking .select2-container--default .select2-results__option[aria-selected=true] {
      background-color: rgba(255, 255, 255, 0.15);
  }

  /* Remove conflicting styles */
  .goride-booking .select2 {
      margin-bottom: 0;
      border: none;
      border-radius: 0;
      background: transparent;
  }

  /* ===== FINAL FORCE FIX FOR LONG SELECT2 TEXT ===== */

  .goride-booking .select2-container--default .select2-selection--single {
      position: relative !important;
      overflow: hidden !important;
      box-sizing: border-box !important;
  }

  /* This is the REAL text container in Select2 */
  .goride-booking .select2-container--default
  .select2-selection--single
  .select2-selection__rendered {
      display: block !important;
      width: 100% !important;
      max-width: 100% !important;
      padding-right: 45px !important; /* space for arrow */
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
      box-sizing: border-box !important;
  }

  /* Some Select2 versions wrap text in a span */
  .goride-booking .select2-selection__rendered > span {
      display: block !important;
      max-width: 100% !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
  }

  /* Lock arrow so it never pushes text */
  .goride-booking .select2-selection__arrow {
      position: absolute !important;
      right: 12px !important;
      width: 30px !important;
      pointer-events: none;
  }

  /* Prevent any internal element from breaking width */
  .goride-booking .select2-container *,
  .goride-booking .select2-selection *,
  .goride-booking .select2-selection__rendered * {
      max-width: 100% !important;
      box-sizing: border-box !important;
      width:100%;

  }
  .goride-booking
  .select2-container--default
  .select2-selection--single
  .select2-selection__clear {
             cursor: pointer;
      float: right;
      font-weight: bold;
      height: 2px;
      margin-right: -197px;
      padding-right: 0px;
  }

  .goride-booking .mini-text {
    display: flex;
    flex-direction: column;   /* label on top, value below */
    align-items: flex-start; /* LEFT ALIGN */
    gap: 2px;
    width: 100%;
  }
  .safety-box {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(249,191,0,0.25);
    border-radius: 10px;
    padding: 8px 10px;
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
  }

  .safety-header {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #f9bf00;
  }

  .safety-view {
    margin-top: 8px;
    font-size: 12px;
    color: rgba(255,255,255,0.75);
  }

  .safety-view h6 {
    margin: 6px 0 4px;
    font-size: 12px;
    color: #f9bf00;
  }

  .safety-view ul {
    padding-left:3px;
    margin-bottom: 6px;
  }

  .safety-view li {
      margin-bottom: 4px;
      line-height: 1.4;
      font-weight: 500;
      font-size: 12px !important;
      line-height: 24px;
      margin: 0;
      color: black;
      font-family: 'Outfit';
  }

  .goride-booking .cab-car-image {
    width:105px;
    height: auto;
    object-fit: contain;
  }

  /*#inclusions-view-title,*/
  /*#inclusions-view-title-2 {*/
  /*    font-size:17px;*/
  /*    color:black;*/
  /*    width: 100%;*/
  /*    text-align: center;*/
  /*}*/

  .inclusions-header h4 {
      font-size:17px;
      color:black;
      width: 100%;
      text-align: center;
  }

  .inclusions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  .exclusions-col .section-title,.inclusions-col .section-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px !important;
    font-weight: 700 !important;
    margin: 0px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
  }


  .inclusion-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(255,255,255,0.1);
    font-size: 14px;
  }

  .inclusion-row:last-child {
    border-bottom: none;
  }

  .inclusion-row p {
    margin: 0;
    line-height: 1.4;
  }

  .inclusion-row .material-icons {
    font-size: 20px;
    min-width: 22px;
  }

  /* Mobile: stack columns */
  @media (max-width: 576px) {
    .inclusions-grid {
      grid-template-columns: 1fr;
    }
  }

  .inclusions-back-btn{
          height: 30px;
      border-radius: 50%;
      width: 30px;
      display: flex;
              justify-content: center;
                          align-items: center;

  }
  .hover-inclusions-tooltip {
    display: none !important;
  }

  #inclusions-view {
    background: #fdfdf4;
    border-radius: 16px;
    padding: 16px;
    margin-top: 15px;
    position: absolute;
    height: 100%;
    left: 0;
    bottom: 0;
    z-index: 999;
    overflow: auto;
  }

  #inclusions-view-2 {
      background: #fdfdf4;
      padding: 16px;
      margin-top: 15px;
      position: absolute;
      left: 0;
      bottom: 0;
      z-index: 999;
  }

  #safety-view {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 20px;
      background: #ffffff;
  }

  .inclusions-content .inclusion-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    font-size: 14px;
  }

  /* Hide old inline hover box */
  .hover-inclusions-tooltip {
    display: none !important;
  }

  /* Always hide inline inclusion box */
  .hover-inclusions-tooltip {
    display: none !important;
  }

  /* Small modal */
  .custom-small-modal {
    max-width: 380px;
  }

  .small-modal-content {
    border-radius: 12px;
    background: #111;
    color: #fff;
  }

  .small-modal-body .inclusion-row {
    display: flex;
    gap: 8px;
  }

  /*.header .caption h2{*/
  /*    font-size: 35px !important;*/
  /*}*/
  /*.v-middle p{*/
  /*    font-size: 15px !important;*/
  /*}*/
  .cs_btn.cs_style_2{
          padding: 5px 24px !important;
          font-size: 14px !important;
  }
         .goride-booking,
          .goride-booking * {
              box-sizing: border-box;
          }

          .goride-booking {
              font-family: 'Inter', sans-serif;
              color: white;
              position: relative;
              min-height: 100vh;
              display: flex;
              align-items: center;
              justify-content: center;
              padding: 20px;
              top: 50px;
              margin-bottom: 7rem;
          }

          .goride-booking .bg-gradient {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%);
          }

          .goride-booking .main-container {
              width: 100%;
              max-width: 480px;
              height: max-content;
              max-height: 560px;
              overflow: auto;
              position: relative;
              z-index: 10;
              border-radius: 30px;
          }

          .goride-booking .glass-card {
              background: rgba(0, 0, 0, 0.85);
              backdrop-filter: blur(25px);
              -webkit-backdrop-filter: blur(25px);
              border: 1px solid rgba(249, 191, 0, 0.2);
              border-radius: 30px;
              box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
              overflow: hidden;
              width: 100%;
              height: 100%;
          }



          .goride-booking .primary-color {
              color: #f9bf00;
          }

          .goride-booking h1 {
              font-size: 28px !important;
              margin: 0px;
                  display: flex !important;
      justify-content: center !important;
          }

          .goride-booking h2 {
              font-size: 22px !important;
              margin: 0px;
          }

         .goride-booking #main-container .modal-content{
              padding: 11px 30px;
              height: 100%;
              overflow-y: auto;
                  margin: 0px !important;
      background: #060608;
      border:none;
          }

          .goride-booking .modal-header {
              display: flex;
              align-items: center;
              justify-content: center;
              margin:0px;
                  background: #060608 !important;
                  padding:0px;
          }

          .goride-booking .route-pill {
     background: rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 10px 16px;
    text-align: center;
    font-size: 13px;
    margin-top: 10px;
  }
  .goride-booking .route-line-1 {
      display: flex;
      align-items: flex-start; /* Changed from center to flex-start */
      justify-content: center;
      gap: 6px;
      font-weight: 600;
      /*flex-wrap: wrap;*/
      text-align: center;
      line-height: 1.4;
  }

  .goride-booking .route-text {
      flex: 1; /* Allow text to grow */
      min-width: 150px; /* Minimum width for readability */
      max-width: calc(50% - 30px); /* Limit width for each location */
      word-break: break-word; /* Break long words */
      overflow-wrap: break-word; /* Handle long URLs/words */
      text-align: start;
      color:grey;
  }

  .goride-booking .route-arrow {
      flex-shrink: 0; /* Don't shrink the arrow */
      margin: 0 8px;
  }

  .goride-booking .route-icon {
      flex-shrink: 0; /* Don't shrink icons */
  }
          .goride-booking .route-line-2 {
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            opacity: 0.8;
            flex-wrap: wrap;
          }

          @media screen and (max-width: 400px) {

          }

          .goride-booking .route-icon {
    font-size: 16px;
  }

          .goride-booking .route-arrow {
    font-size: 18px;
    margin: 0 4px;
  }

          .goride-booking .info-icon {
    font-size: 14px;
  }

          .goride-booking .divider {
    margin: 0 6px;
    opacity: 0.5;
  }
          .goride-booking .hidden {
              display: none !important;
          }

          .goride-booking .glass-input {
              background: rgba(255, 255, 255, 0.08);
              border: 1px solid rgba(255, 255, 255, 0.15);
              border-radius: 12px;
              color: white;
              font-size: 15px;
              padding: 7px 16px;
              transition: all 0.3s ease;
              width: 100%;
              margin-bottom:0px;
          }

          .goride-booking .glass-input#drop-address,
          .goride-booking .glass-input#pickup-address {
              padding: 7px 30px 7px 16px !important;
          }

          .goride-booking .glass-input:focus {
              background: rgba(255, 255, 255, 0.12);
              border-color: #f9bf00;
              outline: none;
              box-shadow: 0 0 15px rgba(249, 191, 0, 0.2);
          }

          .goride-booking .glass-input::placeholder {
              color: rgba(255, 255, 255, 0.5);
          }

          .goride-booking .glow-button {
              background-color: #f9bf00;
              color: black;
              border: none;
              border-radius: 12px;
              font-weight: 700;
              font-size: 13px;
              padding: 7px;
              cursor: pointer;
              transition: all 0.3s ease;
              box-shadow: 0 4px 20px rgba(249, 191, 0, 0.3);
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 8px;
              width: 100%;
              text-transform: uppercase;
              font-family: 'Outfit', sans-serif;
          }

          .glow-button.location-choose-btn {
              position: absolute;
              bottom: 10px;
              left: 10px;
              width: max-content;
              font-weight: 600;
          }

          .glow-button.location-close-btn {
              position: absolute;
              top: 13px;
              right: 13px;
              width: max-content;
              font-size: 20px !important;
          }

          .goride-booking .glow-button:hover {
              background-color: #e6af00;
              box-shadow: 0 4px 30px rgba(249, 191, 0, 0.5);
              transform: translateY(-2px);
          }

          .goride-booking .form-group {
              margin-bottom: 8px;
          }

          .goride-booking label {
              display: flex;
              align-items: center;
              gap: 8px;
              font-size: 11px;
              font-weight: 700;
              text-transform: uppercase;
              letter-spacing: 0.15em;
              color: #f9bf00;
          }

          .goride-booking label[for="travel-date"] {
              width: max-content;
          }

          .goride-booking label {
              display: flex;
              align-items: center;
              gap: 8px;
              font-size: 11px;
              font-weight: 700;
              text-transform: uppercase;
              letter-spacing: 0.15em;
              color: #f9bf00;
          }

          .goride-booking .tab-container {
              display: flex;
              background: rgba(255, 255, 255, 0.1);
              padding: 4px;
              border-radius: 10px;
              border: 1px solid rgba(255, 255, 255, 0.1);
              backdrop-filter: blur(10px);
              width: fit-content;
              margin: 0 auto 10px;
          }

          .goride-booking .tab-button {
              padding: 5px 12px;
              font-size: 11px;
              font-weight: 900;
              text-transform: uppercase;
              letter-spacing: 0.1em;
              border-radius: 8px;
              border: none;
              cursor: pointer;
              transition: all 0.3s ease;
          }

          .goride-booking .tab-button.active {
              background-color: #f9bf00;
              color: black;
              box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
          }

          .goride-booking .tab-button:not(.active) {
              background-color: transparent;
              color: white;
          }

          .goride-booking .tab-button:not(.active):hover {
              color: #f9bf00;
          }

          .goride-booking .material-icons {
              font-family: 'Material Symbols Rounded';
              font-weight: normal;
              font-style: normal;
              font-size: 14px;
              line-height: 1;
              letter-spacing: normal;
              text-transform: none;
              display: inline-block;
              white-space: nowrap;
              word-wrap: normal;
              direction: ltr;
              -webkit-font-feature-settings: 'liga';
              -webkit-font-smoothing: antialiased;
          }

          .goride-booking .grid-2 {
                display: grid;
      grid-template-columns: 2fr 2fr;
      gap: 5px;
          }

          .goride-booking .footer-note {
              text-align: center;
              font-size: 11px;
              font-weight: 500;
              color: rgba(255, 255, 255, 0.5);
              margin-top: 15px;
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 6px;
          }
  .goride-booking #cab-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 15px;
  }
          .goride-booking .cab-card {
              background: rgba(255, 255, 255, 0.04);
              border: 2px solid rgba(255, 255, 255, 0.08);
              border-radius: 12px;
              padding: 7px;
              /*display: flex;*/
              align-items: center;
              gap: 15px;
              margin-bottom: 10px;
              transition: all 0.3s ease;
              cursor: pointer;
          }

          .goride-booking .cab-card:hover {
              background: rgba(255, 255, 255, 0);
              border-color: rgba(249, 191, 0, 0.75);
          }

          .goride-booking .cab-card.selected {
              background: rgba(255, 255, 255, 0);
              border-color: rgba(249, 191, 0, 0.75);
          }

          /*.goride-booking .cab-icon {*/
          /*    width: 45px;*/
          /*    height: 45px;*/
          /*    background: rgba(255, 255, 255, 0.05);*/
          /*    border-radius: 10px;*/
          /*    flex-shrink: 0;*/
          /*    display: flex;*/
          /*    align-items: center;*/
          /*    justify-content: center;*/
          /*}*/

          .goride-booking .cab-icon .material-icons {
              font-size: 28px;
              color: #f9bf00;
          }

          .goride-booking .cab-details {
              flex: 1;
          }

          .goride-booking .cab-details h3 {
              color: #f9bf00;
              /*font-family: 'Outfit', sans-serif;*/
              font-weight: 700;
              font-size: 12px;
              margin: 0px;
          }

          .goride-booking .cab-features .material-icons {
              font-size: 16px;
          }

          .goride-booking .cab-features {
              display: flex;
              gap: 12px;
              color: rgba(255, 255, 255, 0.9);
              font-size: 15px;
              justify-content:center;
              justify-content: end;
          }

          .goride-booking .cab-feature {
              display: flex;
              align-items: center;
              gap: 4px;
          }

          .goride-booking .cab-price {
              display: flex;
              /*flex-direction: column;*/
              align-items: flex-end;
              gap: 8px;
              /*flex-shrink: 0;*/
                  justify-content: space-between;
          }

   .goride-booking .cab-price h3{
           font-size: 15px;
      margin-bottom: 0px;
   }
          .goride-booking .cab-price-amount {
           padding: 0px 11px;
      background: #f9bf00;
      color: black;
      border: none;
      border-radius: 6px;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 16px;
      /* letter-spacing: 0.05em; */
      cursor: pointer;
      transition: all 0.3s ease;
          }

          .goride-booking .select-cab-btn {
         display: block;
      color: #f9bf00;
      font-size: 13px;
      font-weight: 700;
      background: none;
          }

          .goride-booking .select-cab-btn:hover {
            text-decoration: underline;
          }

  .goride-booking .inclusion-row{
        display: flex;
      align-items: baseline;
      gap: 0px;
      margin:0px;
      padding:3px;
  }
          .goride-booking .selector-btn {
              background: rgba(255, 255, 255, 0.1);
              border: 1px solid rgba(249, 191, 0, 0.3);
              color: #f9bf00;
              width: 36px;
              height: 36px;
              border-radius: 8px;
              display: flex;
              align-items: center;
              justify-content: center;
              cursor: pointer;
              transition: all 0.2s ease;
          }

          .goride-booking .selector-btn:hover {
              background: rgba(249, 191, 0, 0.2);
          }

          .goride-booking .passenger-selector {
              display: flex;
              align-items: center;
              justify-content: space-between;
              background: rgba(255, 255, 255, 0.05);
              border: 1px solid rgba(255, 255, 255, 0.1);
              border-radius: 10px;
              padding: 5px;
          }

          .goride-booking .count-display {
              color: white;
              font-weight: 700;
              font-size: 16px;
              min-width: 30px;
              text-align: center;
          }

          .goride-booking .payment-option {
              margin-bottom: 10px;
          }

          .goride-booking .payment-option input[type="radio"] {
              display: none;
          }

          .goride-booking .payment-option label {
              display: flex;
              justify-content: space-between;
              align-items: center;
              width: 100%;
              padding:8px;
              border-radius: 10px;
              background: rgba(255, 255, 255, 0.05);
              border: 1px solid rgba(255, 255, 255, 0.1);
              cursor: pointer;
              margin-bottom: 0;
          }

          .goride-booking .payment-option label:hover {
              background: rgba(255, 255, 255, 0.1);
          }

          .goride-booking .payment-option .radio-indicator {
              width: 14px;
              height: 14px;
              border-radius: 50%;
              border: 2px solid rgba(255, 255, 255, 0.3);
              display: flex;
              align-items: center;
              justify-content: center;
              margin-right: 10px;
              flex-shrink: 0;
          }

          .goride-booking .payment-option input[type="radio"]:checked+label .radio-indicator {
              border-color: #f9bf00;
          }

          .goride-booking .payment-option input[type="radio"]:checked+label .radio-indicator::after {
              content: '';
              width: 8px;
              height: 8px;
              border-radius: 50%;
              background: #f9bf00;
          }

          .goride-booking .payment-option-details {
              display: flex;
              align-items: center;
              gap: 4px;
              flex: 1;
          }

          .goride-booking .payment-option-title {
              font-weight: 600;
              font-size: 11px;
              color: white;
          }

          .goride-booking .payment-option-subtitle {
              font-size: 11px;
              color: rgba(255, 255, 255, 0.3);
          }

          .goride-booking .payment-option-price {
              color: #f9bf00;
              font-weight: 700;
              font-size: 15px;
          }




  .goride-booking .inclusion-row:last-child {
    border-bottom: none;
  }

  .goride-booking .inclusion-row p {
    font-weight: 500;
    font-size: 12px !important;
    margin: 0;
    color:black;
  }

  .goride-booking .inclusion-row small {
    font-size: 11px;
    color: rgba(255,255,255,0.3);
  }

  .goride-booking .confirm-icon-circle {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, rgba(249,191,0,0.25), rgba(249,191,0,0.05));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
    border: 1px solid rgba(249, 191, 0, 0.35);
    box-shadow: 0 0 18px rgba(249,191,0,0.25);
  }

  .goride-booking .confirm-icon {
    font-size: 30px;
  }


          /*.goride-booking .fare-breakdown {*/
          /*    background: rgba(255, 255, 255, 0.05);*/
          /*    border-radius: 10px;*/
          /*    padding: 0px 10px;*/

          /*}*/

          .goride-booking .breakup-header {
          display: flex;
      justify-content: center;
      font-size: 11px;
      cursor: pointer;
      align-items: center;
          }

          /*.goride-booking .breakup-details {*/
          /*    display: none;*/
          /*}*/

          .goride-booking .breakup-row {
              display: flex;
              justify-content: space-between;
              /*margin-bottom: 8px;*/
              font-size: 13px;
          }

          .goride-booking .breakup-label {
              color: #ffc404;
          }

          .goride-booking .breakup-value {
              font-weight: 500;
          }

          .goride-booking .breakup-total {
              display: flex;
              justify-content: space-between;
              padding-top: 12px;
              border-top: 1px solid rgba(255, 255, 255, 0.1);
              font-weight: 700;
          }

          .goride-booking .secondary-button {
              width: 100%;
              background: rgba(255, 255, 255, 0.05);
              border: 1px solid rgba(255, 255, 255, 0.1);
              color: white;
              border-radius: 12px;
              font-weight: 700;
              font-size: 14px;
              padding: 14px;
              cursor: pointer;
              transition: all 0.3s ease;
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 8px;
          }

          .goride-booking .secondary-button:hover {
              background: rgba(255, 255, 255, 0.1);
          }

          .goride-booking .return-date-container {
              display: none;
          }

          .goride-booking .ui-datepicker {
              background: rgba(0, 0, 0, 0.9) !important;
              border: 1px solid rgba(249, 191, 0, 0.2) !important;
              border-radius: 12px !important;
              color: white !important;
              padding: 12px !important;
              backdrop-filter: blur(25px) !important;
          }

          .goride-booking .ui-datepicker-header {
              background: rgba(249, 191, 0, 0.1) !important;
              border: none !important;
              border-radius: 6px !important;
              color: white !important;
              padding: 8px !important;
          }

          .goride-booking .ui-datepicker-title {
              font-weight: 600 !important;
              color: white !important;
              font-size: 14px !important;
          }

          .goride-booking .ui-datepicker-calendar th {
              color: rgba(255, 255, 255, 0.7) !important;
              font-weight: 600 !important;
              padding: 6px !important;
              font-size: 12px !important;
          }

          .goride-booking .ui-datepicker-calendar td {
              border: none !important;
          }

          .goride-booking .ui-datepicker-calendar td a {
              background: rgba(255, 255, 255, 0.05) !important;
              border: none !important;
              color: white !important;
              text-align: center !important;
              padding: 6px !important;
              border-radius: 6px !important;
              font-size: 12px !important;
          }

          .goride-booking .ui-datepicker-calendar td a:hover {
              background: rgba(249, 191, 0, 0.2) !important;
              color: white !important;
          }

          .goride-booking .ui-datepicker-calendar td .ui-state-active {
              background: #f9bf00 !important;
              color: black !important;
              font-weight: 700 !important;
          }

          .goride-booking .ui-datepicker-calendar td .ui-state-highlight {
              background: rgba(249, 191, 0, 0.3) !important;
              color: white !important;
          }

          .goride-booking .ui-datepicker-prev,
          .ui-datepicker-next {
              cursor: pointer !important;
              color: white !important;
          }

          .goride-booking .ui-datepicker-prev:hover,
          .ui-datepicker-next:hover {
              background: rgba(255, 255, 255, 0.1) !important;
              border-radius: 4px !important;
          }
          .goride-booking .custom-time-picker {
    position: relative;
  }

  .goride-booking .time-dropdown {
    position: absolute;
    bottom: 110%;
    left: 0;
    right: 0;
    background: #0b0b0f;
    border: 1px solid rgba(249,191,0,0.25);
    border-radius: 12px;
    max-height: 180px;   /* SCROLL HEIGHT */
    overflow-y: auto;
    display: none;
    z-index: 9999;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
  }

  .goride-booking .time-option {
    padding: 3px 14px;
    font-size: 13px;
    color: #fff;
    cursor: pointer;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: all 0.2s ease;
  }

  .goride-booking .time-option:hover {
    background: rgba(249,191,0,0.15);
    color: #f9bf00;
  }

  /* Nice scrollbar */
  .goride-booking .time-dropdown::-webkit-scrollbar {
    width: 6px;
  }

  .goride-booking .time-dropdown::-webkit-scrollbar-thumb {
    background: rgba(249,191,0,0.5);
    border-radius: 10px;
  }

  .goride-booking .time-dropdown::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.05);
  }

  .goride-booking .booking-id-box {
    background: rgba(255,255,255,0.05);
    border: 1px dashed rgba(249,191,0,0.3);
    border-radius: 8px;
    padding: 4px 10px;
    margin: 6px;
    text-align: center;
  }
  .goride-booking .booking-id-preview {
    background: rgb(248 190 0);
    border: 1px dashed rgba(249,191,0,0.3);
    border-radius: 8px;
    padding: 4px 40px;
    margin: 6px;
    text-align: center;
    cursor:pointer;
  }
  .goride-booking .booking-id-preview a{
      color:black;
      font-weight:500;
  }
  .goride-booking .booking-id {
    font-weight: 700;
  }

  .goride-booking .back-btn {
    background: transparent;
    border: none;
    color: #f9bf00;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
    margin: 4px 0 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .goride-booking .back-icon {
    font-size: 14px;
  }

  .goride-booking ::-webkit-scrollbar {
    width: 3px;
    height: 6px;
  }

  .goride-booking ::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
  }

  .goride-booking ::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #f9bf00, #e6af00);
    border-radius: 10px;
    box-shadow: 0 0 6px rgba(249,191,0,0.5);
  }

  .goride-booking ::-webkit-scrollbar-thumb:hover {
    background: #ffd84d;
  }
  .goride-booking .trip-mini-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 12px 14px;
    display: grid;
    grid-template-columns: 1fr 1fr;  /* equal clean columns */
    gap: 12px 18px;
  }

  .goride-booking .trip-mini-row {
    display: flex;
    gap: 8px;
    padding: 4px 0;
    align-items: flex-start;  /* align top for long text */
  }

  .goride-booking .mini-icon,
  .goride-booking .trip-mini-row .material-icons {
    font-size: 14px;
    color: rgba(249, 191, 0, 0.8);
    margin-top: 2px;
    flex-shrink: 0;
  }


  .goride-booking .mini-text small {
    font-size: 9px;
    color: rgba(255, 255, 255, 0.45);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    line-height: 1;
  }

  /* Value */
  .goride-booking .mini-text p {
    font-size: 12px !important;
    margin: 0;
    font-weight: 500;
    color: #fff;
    line-height: 1.3;
    word-break: break-word;   /* wrap long address */
  }

  /* Full width rows if needed (for long address) */
  .goride-booking .trip-mini-row.full-width {
    grid-column: 1 / -1;   /* span both columns */
  }

  .goride-booking .vehicle-highlight {
    color: #f9bf00;
    font-weight: 700;
  }
  /* Make modal-content a flex column */
  .goride-booking #modal-1.modal-content {
    display: flex;
    flex-direction: column;
  }

  /* Push last form-group (button) to bottom */
  .goride-booking #modal-1 .form-group:last-of-type {
    margin-top: auto;
  }

  .goride-booking .hover-fare-box {
    position: relative;
  }

  .goride-booking .hover-inclusions-tooltip {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #1a1a1a;
    color: #fff;
    padding: 12px;
    border-radius: 8px;
    width: 280px;
    z-index: 9999;
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  }
  .goride-booking .btn-whatsapp {
    background: #f8be00;
    color: black;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(100%);
        cursor: pointer;
    }


          @media (max-width: 480px) {
              .goride-booking .booking-id-box {
    font-size:13px !important;
  }
  .goride-booking .booking-id-preview {
    font-size:13px !important;
  }

              .goride-booking
    .select2-container--default
    .select2-selection--single
    .select2-selection__clear {
       position: absolute;
                 left: 135px ;
          top: 20% ;
          transform: translateY(-50%) ;
          float: none ;
          height: auto ;
          margin: 0 ;
          padding: 0 ;
          font-size: 16px ;
          line-height: 1 ;
          z-index: 5 ;
    }

               .goride-booking .route-line-1 {
          flex-direction: column; /* Stack vertically */
          align-items: flex-start;
          gap: 12px;
      }

      .goride-booking .route-text {
          max-width: 100%; /* Take full width */
          min-width: 0; /* Remove minimum width */
          text-align: left; /* Left align text */
          font-size: 14px; /* Smaller font for mobile */
      }

      .goride-booking .route-arrow {
          transform: rotate(90deg); /* Point downward */
          margin: 0 auto; /* Center the arrow */
          align-self: center; /* Center arrow */
      }

      .goride-booking .route-icon {
          margin-top: 3px; /* Align icon with text */
      }
              .goride-booking{
                  display:block;
                  padding:0px;
              }

              .goride-booking .route-pill {
                  padding: 10px 12px 1px 12px;
                  font-size: 12px !important;
              }

              .goride-booking #cab-list{
                  grid-template-columns: repeat(1, 1fr);
              }

              .goride-booking .main-container {
                  max-height: max-content;
                  overflow: auto;
                  scrollbar-width: none;
                  -ms-overflow-style: none;
              }
              
              .safety-box {
                  flex-direction: column;
              }
              
              #inclusions-view-2 {
                  height: 100%;
                  overflow: auto;
              }

              .goride-booking .main-container::-webkit-scrollbar {
                  display: none;
              }

              .goride-booking #main-container .modal-content {
                  padding: 20px;
              }

              .goride-booking h1 {
                  font-size: 24px;
              }

              .goride-booking h2 {
                  font-size: 20px;
              }

              .goride-booking .grid-2 {

                  gap: 15px;
              }
          }

  .btn-agent-super {
     position: relative;
      display: inline-block;
         padding: 14px 30px;
      font-size: 21px;Manage Smarter
      font-weight: bold;
      color: #000;
      background: #f9bf00;
      border-radius: 15px;
      /*text-transform: uppercase;*/
      text-decoration: none;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(249, 191, 0, 0.5), 0 0 40px rgba(249, 191, 0, 0.3) inset;
      transition: all 0.3s
  ease;
      animation: bounce 2s infinite;
      font-weight:600;
  }

  /* Shimmer effect */
  .btn-agent-super::before {
      content: "";
      position: absolute;
      top: 0;
      left: -75%;
      width: 50%;
      height: 100%;
      background: rgba(255,255,255,0.4);
      transform: skewX(-25deg);
      transition: all 0.7s ease;
  }
  .btn-agent-super:hover::before {
      left: 125%;
  }

  /* Hover glow and scale */
  .btn-agent-super:hover {
      transform: scale(1.15);
      box-shadow: 0 0 30px rgba(249,191,0,1), 0 0 60px rgba(249,191,0,0.7) inset;
      color:black !important;
  }



  /* Keyframes */
  @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
  }

  @keyframes sparks {
      0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
      50% { opacity: 1; transform: translate(calc(-50% + 12px), calc(-50% - 24px)) scale(1); }
      100% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
  }

  @keyframes gradientBG {
      0%{background-position:0% 50%}
      50%{background-position:100% 50%}
      100%{background-position:0% 50%}
  }
  .agency-carousel .item {
      width: 100%;
      height: 100% !important;
      display: block;

  }

  .agency-carousel .item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius:6px !important;

  }

  .agency-btn-wraap{
       display: flex;
      justify-content: space-evenly;
      align-items: center;
  }

  .agency-section{
      padding: 30px 0px;

    color: #fff;
    text-align: center;
    position: relative;
  }

  .overlay{

    padding: 60px 20px;
    border-radius: 10px;
  }

  .agency-section .section-title{
          font-size: 33px;
          margin-bottom:0px;
  }
  /* From Uiverse.io by augustin_4687 */
  .agency-button1 {
    --stone-50: #fafaf9;
    --stone-800: #292524;
    --yellow-400: #facc15;

    font-size: 25px;
    cursor: pointer;
    position: relative;
    font-family: "Rubik", sans-serif;
    font-weight: bold;
    line-height: 1;
    padding: 1px;

    transform: translate(-4px, -4px);
    outline: 2px solid transparent;
    outline-offset: 5px;

    background-color: var(--stone-800);
    color: var(--stone-800);
    transition:
      transform 150ms ease,
      box-shadow 150ms ease;
    text-align: center;
    box-shadow:
      0.5px 0.5px 0 0 var(--stone-800),
      1px 1px 0 0 var(--stone-800),
      1.5px 1.5px 0 0 var(--stone-800),
      2px 2px 0 0 var(--stone-800),
      2.5px 2.5px 0 0 var(--stone-800),
      3px 3px 0 0 var(--stone-800),
      0 0 0 2px var(--stone-50),
      0.5px 0.5px 0 2px var(--stone-50),
      1px 1px 0 2px var(--stone-50),
      1.5px 1.5px 0 2px var(--stone-50),
      2px 2px 0 2px var(--stone-50),
      2.5px 2.5px 0 2px var(--stone-50),
      3px 3px 0 2px var(--stone-50),
      3.5px 3.5px 0 2px var(--stone-50),
      4px 4px 0 2px var(--stone-50);

    &:hover {
      transform: translate(0, 0);
      box-shadow: 0 0 0 2px var(--stone-50);
    }


    &:active,
    &:focus-visible {
      outline-color: var(--yellow-400);
    }

    &:focus-visible {
      outline-style: dashed;
    }
    &:hover {
    transform: translate(0, 0);
    box-shadow: 0 0 0 2px var(--stone-50);

    color: black; // fallback

    & > div > span {
      color: black;
    }
  }


    & > div {
      position: relative;
      pointer-events: none;
      background-color: var(--yellow-400);
      border: 2px solid rgba(255, 255, 255, 0.3);


      &::before {
        content: "";
        position: absolute;
        inset: 0;

        opacity: 0.5;
        background-image: radial-gradient(
            rgb(255 255 255 / 80%) 20%,
            transparent 20%
          ),
          radial-gradient(rgb(255 255 255 / 100%) 20%, transparent 20%);
        background-position:
          0 0,
          4px 4px;
        background-size: 8px 8px;
        mix-blend-mode: hard-light;
        animation: dots 0.5s infinite linear;
      }

      & > span {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 21px 12px 0px;
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



  /* Mobile */
  @media (max-width: 600px) {
    .goride-capsule {
      border-radius: 20px;
      flex-direction: column;
      padding: 20px;
    }
    .capsule-info {
      border-right: none;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      padding-bottom: 15px;
      margin-bottom: 15px;
      text-align: center;
    }
  }

      .goride-routes .tagline {
          color: #f9bf00;
          font-weight: 600;
          font-size: 1.1rem;
          letter-spacing: 1px;
          margin-bottom: 10px;
          text-transform: uppercase;
      }

      .goride-routes {
          padding: 30px 0;
          background: white;
      }


      .goride-routes .accordion-item {
          border: none;
          border-radius: 14px;
          box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
          overflow: hidden;
          background: #fff;
      }


      .goride-routes .accordion-button {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 24px;
          padding: 9px;
          font-size: 15px;
          font-weight: 600;
          line-height: 21px;
          line-height: 1.4;
          background: #fff8e1;
          color: #111;
          transition: all 0.3s ease;
      }


      .goride-routes .accordion-button:hover {
          background: #fff;
      }



      .goride-routes .count {
          width: 42px;
          height: 42px;
          background: #f7b500;
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
          transition: all 0.4s ease;
      }




      .goride-routes .count i {
          color: #fff;
          font-size: 18px;
      }


      .goride-routes .accordion-body {
          padding: 16px 10px;
          border-top: 1px solid #eee;
      }


      .goride-routes .routes-list ul {
          list-style: none;
          padding: 0;
          margin: 0;
      }

      .goride-routes .routes-list li a {
          display: flex;
          align-items: baseline;
          gap: 10px;
          padding: 0px 8px;
          font-size: 15px;
          color: #222;
          text-decoration: none;
          border-radius: 6px;
          transition: all 0.25s ease;
      }


      .goride-routes .routes-list li a:hover {
          background: rgba(247, 181, 0, 0.12);
          color: #000;
          padding-left: 12px;
      }

      .goride-routes .route-icon {
          color: #f82525;
          font-size: 14px;
          flex-shrink: 0;
      }


      .description {
          color: #1d2b53;
          font-weight: 500;
          font-size: 17px;
      }

      .upgrade {
          padding: 60px 20px;
          text-align: center;
          background: linear-gradient(135deg, #f4f7ff, #eef2ff);
      }

      .agency-tag {
          display: inline-block;
          background: rgba(248, 190, 0, 0.1);
          color: #f8be00;
          padding: 8px 16px;
          border-radius: 50px;
          font-size: 0.8rem;
          font-weight: 700;
          text-transform: uppercase;
          letter-spacing: 1.5px;
          margin-bottom: 24px;
      }

      .agency-desc {
          font-size: 21px;
          font-weight: 700;

          line-height: 1.2;
          margin-bottom: 30px;
          max-width: 500px;
      }

      /* The Yellow Button */
      .agency-button {
              display: inline-flex;
      align-items: center;
      gap: 12px;
      background: #f9bf00;
      color: #1a1a1a;
      text-decoration: none;
      padding: 5px 12px;
      border-radius: 16px;
      font-weight: 600;
      font-size: 17px;
      }

      .agency-btn:hover {
          color: white;
          transform: translateY(-3px);

      }

      .agency-btn i {
          transition: transform 0.3s ease;
      }

      .agency-btn:hover i {
          transform: translateX(5px);
      }


      .agency-image img {
          max-width: 360px;
          height: auto;
          border-radius: 20px;
          box-shadow: none;
      }



      .driver-section {
          position: relative;
          padding: 50px 0;
          background: #f2f2f2;
          background-size: cover;
          background-position: center
      }


      .driver-section .tagline {
          color: #f9bf00;
          font-weight: 600;
          font-size:16px;
          letter-spacing: 1px;
          margin-bottom: 10px;
          text-transform: uppercase;
      }

      .driver-section .highlight {
          color: #f39c12;
          font-weight: 700;
      }

      .driver-section .driver-section .description {
          font-size: 1.1rem;
          color: #555;
          line-height: 1.7;

      }

      .driver-section .steps-section {
          margin: 10px 0;
      }

      .driver-section .steps-title {
          font-size: 1.8rem;
          color: #2c3e50;
          margin-bottom: 30px;
          position: relative;
          padding-bottom: 10px;
      }

      .driver-section .steps-title:after {
          content: '';
          position: absolute;
          bottom: 0;
          left: 0;
          width: 60px;
          height: 4px;
          background: #f39c12;
          border-radius: 2px;
      }

      .driver-section .step {
          display: flex;
          align-items: center;
          justify-content: start;
          margin-bottom: 20px;
          padding: 13px;
          border-radius: 15px;
          background: #f8f9fa;
          transition: transform 0.3s ease;
          box-shadow: 0 6px 4px rgba(0, 0, 0, 0.4);
      }

      .driver-section .step:hover {
          display: flex;
          align-items: center;
          justify-content: start;
          margin-bottom: 20px;
          padding: 13px;
          border-radius: 15px;
          background: #f8f9fa;
          transition: transform 0.3s ease;
          box-shadow: 0 6px 4px rgba(0, 0, 0, 0.4);
      }

      .driver-section .step-number {
          background: #f9bf00;
          color: white;
          width: 30px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          font-size: 16px;
          margin-right: 20px;
          flex-shrink: 0;
      }

      .driver-section .step-content {
          font-size: 1.1rem;
          color: #333;
          font-weight: 500;
      }

      /* Image Column */
      /*   .driver-section  .image-col {*/
      /*        background: linear-gradient(135deg, #edece9 0%, #f3ba00 100%);*/
      /*position: relative;*/
      /*overflow: hidden;*/
      /*display: flex;*/
      /*align-items: center;*/
      /*justify-content: center;*/
      /*padding: 30px;*/
      /*height: 100%;*/
      /*    }*/

      .driver-section .image-container {
          position: relative;
          width: 100%;

      }

      .driver-section .driver-image {
          width: 100%;

          object-fit: cover;
          border-radius: 20px;

          max-height: 700px;
      }

      .driver-section .image-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(44, 62, 80, 0.1);
          border-radius: 20px;
          display: flex;
          flex-direction: column;
          justify-content: flex-end;
          padding: 40px;
          color: white;
      }



      .driver-section .stats {
          display: flex;
          /*justify-content: space-around;*/
          gap: 10px !important;

          text-align: center;
      }

      .driver-section .stat-item {
          padding: 9px;
      }

      .driver-section .stat-number {
          font-size: 21px;
          font-weight: 700;
          color: #f39c12;
          display: block;
      }

      .driver-section .stat-label {
          font-size: 20px;
          color: #1d2b53;
          margin-top: 5px;
          font-weight: 500;
      }

      .driver-section .cta-section {
          margin-top: 20px;
          text-align: center;
      }

      .driver-section .cta-text {
          font-size: 1.3rem;
          color: #2c3e50;
          margin-bottom: 25px;
          font-weight: 600;
          line-height: 1.5;
      }

      .driver-section .cta-button {
          display: inline-block;
          background: black;
          color: white;
          padding: 5px 14px;
          border-radius: 7px;
          font-size: 13px;
          font-weight: 700;
          text-decoration: none;
          border: none;
          cursor: pointer;
          transition: all 0.3s ease;
          letter-spacing: 0.5px;
      }

      .driver-section .cta-button:hover {
          transform: translateY(-5px);
          background: linear-gradient(to right, #f9bf00, #f9bf00);
          color: black;
      }



      .mycard p {
          font-size: 15px;
      }

      .mycard {
          padding: 0px 10px;
      }

      .step .content {
          padding: 10px;
      }

      .mycard .overlay {
          width: 85px;
          height: 85px;
          border-radius: 50%;
          background: var(--bg-color);
          position: absolute;
          top: 9px;
          left: 0;
          right: 0;
          margin: 0 auto;
          /* Center horizontally */
          z-index: 0;
          transition: transform 0.3s ease-out;
          padding:0px !important;
      }

      .section-subtitle {
          font-weight: 700;
          color: #040303;
      }

      /*.slider-fade .item .caption{*/
      /*    left:50%;*/
      /*}*/
      .content.blue {
          border-left: 5px solid #1E90FF;
          border-top: none;
      }

      .content.blue .step-icon {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 13px;
          font-size: 22px;
          color: white;
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
          background: #1E90FF;
      }

      .content.yellow {
          border-left: 5px solid #FFD700;
          border-top: none;
      }

      .content.yellow .step-icon {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 13px;
          font-size: 22px;
          color: white;
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
          background: #FFD700;
      }

      .content.orange {
          border-left: 5px solid #FF4500;
          border-top: none;
      }

      .content.orange .step-icon {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 13px;
          font-size: 22px;
          color: white;
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
          background: #FF4500;
      }

      .content.red {
          border-left: 5px solid #a200ff;
          border-top: none;
      }


      .content.red .step-icon {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 13px;
          font-size: 22px;
          color: white;
          box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
          background: #a200ff;
      }

      .features-grid-section {
          padding: 50px;
          background: #fff;
      }

      .features-grid {
          display: flex;
      }

      .feature-col {
          display: flex;
      }

      /* CARD */
      .feature-card {
          text-align: center;
          padding: 35px 25px;
          border-radius: 22px;
          transition: all 0.35s ease;
          background: #ffffff;
          height: 100%;
          display: flex;
          flex-direction: column;
          border: 2px solid #ebebeb;

      }

      .feature-card:hover {
          transform: translateY(-8px);
          border-color: #ffc107;
          /* brand highlight */
          box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12),
              0 6px 15px rgba(255, 193, 7, 0.25);
      }

      /* IMAGE WRAPPER */
      .feature-img-wrap {
          position: relative;
          display: inline-block;
          margin-bottom: 50px;
      }

      /* BEFORE – soft yellow blob */
      .feature-img-wrap::before {
          content: "";
          position: absolute;
          width: 130px;
          height: 130px;
          background: #f2f2f2;
          border-radius: 50%;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          z-index: 0;
      }

      .feature-img-wrap::after {
          display: none;
      }

      /* AFTER – subtle ring */
      /*.feature-img-wrap::after {*/
      /*    content: "";*/
      /*    position: absolute;*/
      /*    width: 130px;*/
      /*    height: 130px;*/
      /*    border: 2px dashed #ea0a04;*/
      /*    border-radius: 50%;*/
      /*    top: 50%;*/
      /*    left: 50%;*/
      /*    transform: translate(-50%, -50%);*/
      /*    z-index: 0;*/
      /*    opacity: 0.6;*/
      /*}*/

      /* IMAGE */
      .feature-img-wrap img {
          width: 77px;
          position: relative;
          z-index: 1;
          filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.15));
      }

      /* TEXT */
      .feature-card h3 {
          font-size: 20px;
          font-weight: 700;

      }

      .feature-card p {
          font-size: 16px;
          color: #444;
          line-height: 1.7;
          max-width: 420px;
          font-weight: 500;

      }


      .fleet-card {
          background: #fff;
          border-radius: 20px;
          overflow: hidden;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
          transition: transform 0.4s ease;
          height: 100%;
      }

      .fleet-card:hover {
          transform: translateY(-10px);
      }

      /* IMAGE WRAPPER */
      .fleet-image {
          position: relative;
          height: 260px;
          overflow: hidden;
      }

      /* IMAGE */
      .fleet-image img {
          width: 100%;
          height: 100%;
          object-fit: cover;
      }

      /* OVERLAY */
      .fleet-image::before {
          content: "";
          position: absolute;
          inset: 0;
          background: linear-gradient(to top, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.1));
          z-index: 1;
      }

      /* ACCENT STRIPE */
      .fleet-image::after {
          content: "";
          position: absolute;
          width: 150%;
          height: 55px;
          background: #facc15;
          top: -40px;
          left: -120%;
          transform: rotate(-12deg);
          transition: all 0.5s ease;
          z-index: 2;
      }

      /* Hover accent */
      .fleet-card:hover .fleet-image::after {
          left: -20%;
      }

      /* CONTENT BELOW IMAGE */
      .fleet-content {
          padding: 8px 14px 10px;
      }

      .fleet-content h3 {
          margin: 0 0 6px;
          color: #1d2b53;
          font-size: 20px;
      }

      .fleet-models {
          font-style: italic;
          font-size: 15px;
          color: #6b7280;
          font-weight: 600;
      }

      .fleet-desc {

          font-size: 15px;
          color: #444;
          line-height: 1.6;
          font-weight: 500;
      }


      .payment-cards {
          max-width: 1000px;
          margin: auto;
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
          gap: 30px;
      }

      .payment-card {
          background: #fff;
          border-radius: 20px;
          padding: 35px 25px;
          box-shadow: none;
          transition: all 0.35s ease;
          position: relative;
      }

      .payment-card:hover {
          transform: translateY(-10px);
          box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
          border: 1px solid #f3ba00;
      }

      /* Small round image */
      .payment-img {
          width: 70px;
          height: 70px;
          border-radius: 50%;
          background: white;
          margin: auto;
          box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
          border: 1px solid #f9bf00;
      }

      .payment-card:hover .payment-img {
          background: #f9bf0087;

      }


      .payment-card:hover .payment-img img {
          transform: scale(1.1);
      }


      .payment-img img {
          width: 40px;
          height: 40px;
          object-fit: contain;
      }

      .payment-card h3 {
          margin: 20px 0 15px;
          color: #1d2b53;
      }

      .payment-card ul {
          list-style: none;
          padding: 0;
          margin: 0;
          text-align: left;
      }

      .payment-card ul li {
          margin: 10px 0;
          padding-left: 22px;
          position: relative;
          color: #444;
          font-weight: 500;
          font-size: 15px;
      }

      .payment-card ul li::before {
          content: "✔";
          position: absolute;
          left: 0;
          color: #4f46e5;
          font-size: 14px;
      }

      .item.bg-img:before {
          background-color: rgba(0, 0, 0, 0.6);
          padding: 60px 20px;


      }

      .theme-btn:focus,
      .theme-btn:active,
      .theme-btn3:focus,
      .theme-btn3:active {
          background-color: #ffc107 !important;
          color: #000 !important;
          outline: none !important;
          box-shadow: none !important;
      }

      #india-content2 {
          position: absolute;
          bottom: 75px;
          right: 275px;
      }

      .theme-btn3 {
          background: #FFC107;
          color: #000;
          padding: 7px 10px;
          border-radius: 7px;
          font-weight: 600;
          margin: 10px;
          display: inline-block;
          text-decoration: none;
          transition: 0.3s;
          position: relative;
          z-index: 1;

      }

      .theme-btn3:hover {
          background: #e0a800;
          color: #000;
      }

      .theme-btn {
          background: #FFC107;
          color: #000;
          padding: 7px 10px;
          border-radius: 7px;
          font-weight: 600;
          margin: 10px;
          display: inline-block;
          text-decoration: none;
          transition: 0.3s;
          position: relative;
          z-index: 1;
      }

      .theme-btn:hover {
          background: #e0a800;
          transform: translateY(-2px);
          color: #000;
      }

      /* Fixed square wave effect with yellow only */
      .theme-btn::before {
          content: '';
          position: absolute;
          top: -4px;
          left: -4px;
          right: -4px;
          bottom: -4px;
          border-radius: 9px;
          z-index: -1;
          background: transparent;
          animation: squareWave 1.5s linear infinite;
      }

      @keyframes squareWave {
          0% {
              box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.8),
                  0 0 0 0 rgba(255, 193, 7, 0.5);
          }

          50% {
              box-shadow: 0 0 0 5px rgba(255, 193, 7, 0.8),
                  0 0 0 10px rgba(255, 193, 7, 0.3);
          }

          100% {
              box-shadow: 0 0 0 10px rgba(255, 193, 7, 0),
                  0 0 0 15px rgba(255, 193, 7, 0);
          }
      }

      .header-jobs.download-app {
          right: 5% !important;
      }

      .header-jobs {
          right: 20% !important;
          bottom: 100px;
      }

      @media screen and (max-width: 767px) {
          .booking-wrapper-form{
              margin-bottom: 70px;
          }
          .agency-button1{
              font-size:16px;
          }
          .payment-card{
                  padding: 17px 14px;
          }
          .upgrade {
              padding: 38px 10px;
          }
          .agency-btn-wraap{
          flex-direction: column;
          gap:12px;
          }
          .capsule-cta{
                display: inline-flex;
          }

          .driver-section .stat-label {
              font-size: 16px;
          }

          .driver-section .stats {
              gap: 50px;
          }

          .driver-section .step-content {
              font-size: 16px;
          }

          .driver-section .step {
              padding: 10px;
          }

          .description {
              font-size: 16px;
          }

          .payment-section {
              padding: 30px 0px;
          }

          .agency-desc {
              font-size: 17px;
              margin-bottom: 12px;
          }

          .agency-btn {
              padding: 6px 19px;
              font-size: 15px;
          }

          .agency-content {
              max-width: 100%;
          }

          .carousel-inner .step {
              display: block;
          }

          .mycard {
              padding: 4px 17px;
              width: 90% !important;
          }

          .feature-col {
              margin-bottom: 20px;
          }

          .slider-fade .item .caption {
              left: 0%;
          }

          .fleet-grid {
              display: block;
          }


          .features-grid {
              display: block;
          }

          .theme-btn {
              padding: 6px 8px !important;
              font-size: 12px !important;
          }

          .theme-btn3 {
              padding: 6px 8px !important;
              font-size: 12px !important;
          }

          .header-jobs:hover {
              transform: translateX(-50%) !important;
          }

          .header-jobs {
              top: 225px;
          }

          .download-app.header-jobs {
              top: 270px;
          }
      }

      @media (max-width: 1400px) {

          .header-jobs {
              right: 25% !important;
          }

          .header-jobs {
              padding: 15px 10px !important;
          }

      }

      .jobs-heading {
          background: #f9bf00;
          position: absolute;
          padding: 18px;
          height: 32px;
          width: fit-content;
          z-index: 1;
          top: -10px;
          left: 222px;
          display: flex;
          justify-content: center;
          align-items: center;
          border-radius: 6px;
      }

      .job-search {
          max-width: 526px;
      }

      .see-jobs-btn {
          background: #f9bf00;
          color: black;
          font-weight: 500;
          border-radius: 14px;
      }

      .badge {
          background: #f9bf00;
          color: black;
          #f2f2f2
      }

      .phone-mockup {
          width: 100%;
          max-width: 471px;
          background: #f2f2f2;
          border-radius: 40px;
          position: relative;
          height: 483px;
          overflow: scroll;
          max-height: 483px;
          /* or whatever height you want */
          overflow-y: scroll;
          /* enable vertical scroll */

          /* hide scrollbar for all browsers */
          scrollbar-width: none;
          /* Firefox */
          -ms-overflow-style: none;
          /* IE 10+ */
          padding: 20px;
      }


      .phone-mockup::-webkit-scrollbar {
          display: none;
          /* Chrome, Safari, Opera */
      }


      .notify-card {
          display: flex;
          align-items: center;
          gap: 15px;
          color: #fff;
          padding: 10px;
          border-radius: 12px;
          margin: 25px auto;
          width: 90%;
          box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
      }

      .notify-card i {
          font-size: 20px;
      }

      .notify-card.blue {
          border-right: 8px solid #f9bf00;
          background: white;
      }

      .notify-card h6 {
          color: #5c5861;
          font-size: 14px;
          margin-bottom: 4px;
      }

      .notify-card p {
          font-size: 12px;
      }



      .modal-header {
          background: #fff !important;
          margin: 12px 0 0 0;
      }

      .modal-body {
          background: #fff !important;
      }

      .frm-sec {
          padding: 21px;
      }

      input#exampleInputEmail1 {
          border: 1px solid #bbbbbb;
          box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
      }

      .close1 {
          background: none;
          position: absolute;
          right: 20px;
          font-size: 26px;
      }

      .close1:hover {
          color: orange;
      }

      .modal-content {
          background-color: #fefefe;
          margin: 83px 0 0 0;
          padding: 5px;
          border: 1px solid #888;
          width: 100%;
          max-width: 700px;
          position: relative;
      }

      .modal-body h1 {
          font-weight: 900;
          font-size: 2.3em;
          text-transform: uppercase;
      }

      .modal-body a.pre-order-btn {
          color: #000;
          background-color: gold;
          border-radius: 1em;
          padding: 1em;
          display: block;
          margin: 2em auto;
          width: 50%;
          font-size: 1.25em;
          font-weight: 6600;
      }

      .modal-body a.pre-order-btn:hover {
          background-color: #000;
          text-decoration: none;
          color: gold;
      }

      @media (max-width: 768px) {
          .slider-fade .item {
              background-position: right;
          }

          .feature-img-wrap img {
              width: 50px;

          }

          .feature-img-wrap::before {
              width: 90px;
              height: 90px;
          }

          .feature-img-wrap::after {
              width: 110px;
              height: 110px;
          }

          /*.header {*/
          /*    height: 80vh !important;*/
          /*}*/

          .feature-img-wrap {
              margin-bottom: 45px;
          }

          .features-grid-section {
              padding: 25px;
          }

          .slider-fade .item .caption {
              top: 60% !important;
          }

          .jobs-heading {
              top: -16px;
              left: 50%;
              transform: translateX(-50%);
          }

          #about {
              padding: 0px !important;
          }

          .cs_cta.cs_style_1 .cs_section_title {
              font-size: 24px;
          }

          section .how {
              padding: 0px !important;
          }

          .job-box .title-box {
              width: 100% !important;
          }

          .job-search {
              max-width: 265px !important;

          }

          .phone-mockup {
              height: auto;
              max-height: 600px;
          }

          .notify-card {
              margin: 25px 0 !important;
              width: 100% !important;
              padding: 10px 10px 20px 10px !important;
          }

      }

      .notify-card {
          position: relative;
      }

      .phone-mockup .badge {
          position: absolute;
          bottom: 5px;
          right: 5px;
      }

      #mapModal.open-mapModal:not(.hidden) {
          position: absolute;
          left: 0;
          top: 0;
          height: 100%;
          width: 100%;
          border-radius: 30px;
      }

      .ui-autocomplete {
          background: #000;
          border-radius: 10px;
          box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
          border: 1px solid #ddd;
          padding: 5px 0;
          max-height: 250px;
          overflow-y: auto;
          z-index: 9999 !important;

          scrollbar-width: thin;
          scrollbar-color: #f9bf00 transparent;
      }

      .ui-autocomplete::-webkit-scrollbar {
          width: 8px;
      }

      .ui-autocomplete::-webkit-scrollbar-track {
          background: transparent;
          border-radius: 10px;
      }

      .ui-autocomplete::-webkit-scrollbar-thumb {
          background: #f9bf00;
          border-radius: 10px;
          transition: background 0.3s;
      }

      .ui-autocomplete::-webkit-scrollbar-thumb:hover {
          background: #f9bf00;
      }

      .ui-widget-content {
          color: #fff !important;
      }

      .ui-menu-item {
          padding: 5px 10px;
          font-size: 14px;
          cursor: pointer;
          display: flex;
          align-items: center;
          gap: 10px;
      }

      .ui-menu-item:hover {
          background: #f9bf00 !important;
          color: #ffffff !important;
          border: none !important;
      }

      .ui-datepicker-calendar .ui-state-active {
          color: #333333 !important;
      }

      .ui-state-active {
          margin: 0 !important;
          color: unset !important;
          background: transparent !important;
      }

      .ui-menu-item::before {
          font-family: "Font Awesome 6 Pro";
          content: "\f3c5";
          font-size: 16px;
          opacity: 0.7;
      }

      .ui-menu-item-wrapper {
          border: none !important;
          padding: 0 !important;
      }

      .glass-input {
          width: 100%;
          padding: 14px 15px 14px 45px;
          border-radius: 10px;
          border: 1px solid #ccc;
          font-size: 15px;
          outline: none;
      }

      .ui-datepicker .ui-datepicker-next:before,
      .ui-datepicker .ui-datepicker-prev:after {
          content: none !important;
      }

      .input-with-icon {
        position: relative;
      }

      .location-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
      }

      .location-icon:hover {
        color: #fff;
        cursor: pointer;
      }

      .flatpickr-calendar {
          border-radius: 15px;
          overflow: hidden;
      }

      .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month {
          background: #000;
          font-size: 0.8rem;
      }

      .flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month:hover {
          background: #f9bf00;
          color: #000;
      }

      .flatpickr-calendar,
      .flatpickr-months .flatpickr-month,
      .flatpickr-current-month .flatpickr-monthDropdown-months,
      span.flatpickr-weekday {
          background: #000;
      }

      .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
          background: #f9bf00;
          color: #000;
      }

      @media only screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) {
          .i_phone {
              margin-top: -100px !important;

          }

      }
</style>

<header class="header slider-fade" id="slider-image">
  <div class="item bg-img" data-overlay-dark="1">
    <div class="v-middle caption">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6 col-md-6 mb-30">
            <div class="v-middle text-center d-none d-md-block">
              <h2>Go!</h2>
              <h2>Anywhere & Anytime</h2>
              <h2>With <span class="text-warning">GoRide</span></h2>

              <p>Whether you’re heading to work, the airport,</p>
              <p>or a weekend getaway — we’ve got you covered.</p>
              <p>On-time pickups and drop-offs, always reliable.</p>

              <div class="d-none d-md-block">
                @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                <a href="pricing" class="cs_btn cs_style_2 mt-4 me-3"
                  >Go Start ther Cab Search&nbsp;<i
                    class="fa-regular fa-car-side-bolt"
                  ></i
                ></a>
                <a
                  style="cursor: pointer"
                  onclick="triggerCalendly()"
                  class="cs_btn cs_style_2 mt-4"
                  >Book a demo&nbsp;<i class="fa-regular fa-headset"></i
                ></a>
                @else
                <a href="/booking" class="cs_btn cs_style_2 mt-4"
                  >Go Start the Cab Search&nbsp;<i
                    class="fa-regular fa-car-side-bolt"
                  ></i
                ></a>
                @endif
              </div>
              <div class="d-block d-md-none">
                @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                <a href="pricing" class="cs_btn cs_style_2 mt-4 me-3"
                  >Go Start the Cab Search&nbsp;<i
                    class="fa-regular fa-car-side-bolt"
                  ></i
                ></a>
                <a
                  style="cursor: pointer"
                  onclick="triggerCalendly()"
                  class="cs_btn cs_style_2 mt-4"
                  >Book a demo&nbsp;<i class="fa-regular fa-headset"></i
                ></a>
                @else
                <a href="/booking" class="cs_btn cs_style_2 mt-4"
                  >Go Start the Cab Search&nbsp;<i
                    class="fa-regular fa-car-side-bolt"
                  ></i
                ></a>
                @endif
              </div>
            </div>

            <!--<div class="v-middle text-center two">-->
            <!--  <img src="{{ asset('goride/img/banner-1-mob.webp') }}" alt="mobile_mockup" id="mobile_mockup">-->
            <!--  <div id="india-content">-->
            <!--      @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)-->
            <!--      <a href="/jobs" class="theme-btn btn">-->
            <!--        Get Your Jobs <i class="fa-solid fa-print-magnifying-glass ms-2"></i>-->
            <!--      </a>-->
            <!--      <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank" class="theme-btn3 download-app btn">-->
            <!--        Go Ride Partner App <i class="fa-brands fa-google-play ms-2"></i>-->
            <!--      </a>-->
            <!--      @else-->
            <!--      <a href="/login" class="theme-btn btn">-->
            <!--        Get Your Jobs <i class="fa-solid fa-print-magnifying-glass ms-2"></i>-->
            <!--      </a>-->
            <!--      @endif-->
            <!--  </div>-->
            <!--</div>-->
          </div>
          <div class="col-md-6 col-12">
            <div class="goride-booking">
              <div class="main-container position-relative">
                <div class="glass-card" id="main-container">
                  <!-- Modal 1: Trip Details -->
                  <div id="modal-1" class="modal-content">
                    <div class="modal-header mb-3">
                      <h1 class="font-display">
                        Book Your <span class="primary-color">Journey</span>
                      </h1>
                    </div>

                    <div class="tab-container">
                      <button class="tab-button active" id="one-way-btn">
                        One Way
                      </button>
                      <button class="tab-button" id="round-trip-btn">
                        Round Trip
                      </button>
                    </div>

                    <!--<div class="form-group">-->
                    <!--    <label for="pickup-location">-->
                    <!--        <span class="material-icons">my_location</span>-->
                    <!--        Pickup Location-->
                    <!--    </label>-->
                    <!--    <select id="pickup-location" id="pickup-location" class="glass-input" placeholder="Enter departure point"></select>-->
                    <!--</div>-->

                    <div class="form-group">
                      <label for="pickup-location">
                        <span class="material-icons">my_location</span>
                        Pickup Location
                      </label>

                      <input
                        type="text"
                        id="pickup-location"
                        class="glass-input"
                        placeholder="Enter pickup address"
                      />
                    </div>

                    <div class="form-group">
                      <label for="destination">
                        <span class="material-icons">location_on</span>
                        Destination
                      </label>
                      <input
                        type="text"
                        id="destination"
                        class="glass-input"
                        placeholder="Enter arrival point"
                      />
                      <!--<select id="destination" id="destination" class="glass-input" placeholder="Enter arrival point"></select>-->
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="travel-date">
                            <span class="material-icons">calendar_today</span>
                            Travel Date
                          </label>
                          <input
                            type="text"
                            id="travel-date"
                            class="glass-input datepicker"
                            placeholder="Select date"
                          />
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="pickup-time">
                            <span class="material-icons">schedule</span>
                            Pickup Time
                          </label>
                          <div class="custom-time-picker">
                            <input
                              type="text"
                              id="pickup-time"
                              class="glass-input"
                              placeholder="Select time"
                              readonly
                            />
                            <div class="time-dropdown" id="time-dropdown"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div
                      class="form-group return-date-container"
                      id="return-date-container"
                    >
                      <label for="return-date">
                        <span class="material-icons">calendar_today</span>
                        No. Of Days
                      </label>

                      <select id="return-date" class="glass-input">
                        <option value="" style="color: black">
                          Select No. Of Days
                        </option>
                        <option value="1" style="color: black">
                          1 Day (upto 24 hrs)
                        </option>
                        <option value="2" style="color: black">2 Days</option>
                        <option value="3" style="color: black">3 Days</option>
                        <option value="4" style="color: black">4 Days</option>
                        <option value="5" style="color: black">5 Days</option>
                        <option value="6" style="color: black">6 Days</option>
                        <option value="7" style="color: black">7 Days</option>
                        <option value="8" style="color: black">8 Days</option>
                        <option value="9" style="color: black">9 Days</option>
                        <option value="10" style="color: black">10 Days</option>
                      </select>
                    </div>

                    <!--<div class="row">-->
                    <!--    <div class="col-6">-->
                    <!--        <div class="form-group">-->
                    <!--            <label for="travel-date">-->
                    <!--                <span class="material-icons">calendar_today</span>-->
                    <!--                Distance-->
                    <!--            </label>-->
                    <!--            <input type="text" id="travel-date" class="glass-input datepicker" placeholder="Select date">-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-6">-->
                    <!--        <div class="form-group">-->
                    <!--            <label for="pickup-time">-->
                    <!--                <span class="material-icons">schedule</span>-->
                    <!--                Duration-->
                    <!--            </label>-->
                    <!--            <div class="custom-time-picker">-->
                    <!--              <input type="text" id="pickup-time" class="glass-input" placeholder="Select time" readonly>-->
                    <!--              <div class="time-dropdown" id="time-dropdown"></div>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="form-group mt-3">
                      <button class="glow-button" id="get-quote-btn">
                        Get Instant Quote
                        <span class="material-icons">arrow_forward</span>
                      </button>
                    </div>

                    <!--<p class="footer-note">-->
                    <!--    <span class="material-icons">verified_user</span>-->
                    <!--    No payment required upfront. Cancel anytime up to 24h before.-->
                    <!--</p>-->
                  </div>

                  <!-- Modal 2: Select Cab -->
                  <div id="modal-2" class="modal-content hidden">
                    <div class="modal-header">
                      <div class="row w-100 align-items-center">
                        <!-- Left: Back Button -->
                        <div class="col-3 text-start">
                          <button class="back-btn">
                            <span class="material-icons back-icon"
                              >arrow_back</span
                            >
                            Back
                          </button>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-9 text-start">
                          <h2 class="font-display mb-0">
                            Select Your <span class="primary-color">Cab</span>
                          </h2>
                        </div>
                      </div>
                    </div>

                    <div class="w-100">
                      <div class="route-pill" id="route-summary">
                        <div class="route-line-1">
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text from-location-3"
                              id="from-location-3"
                            ></span>
                          </div>
                          <span class="material-icons route-arrow"
                            >arrow_forward</span
                          >
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text to-location-3"
                              id="to-location-3"
                              >Pune</span
                            >
                          </div>
                        </div>

                        <div class="route-line-2">
                          <span class="material-icons info-icon text-warning"
                            >event</span
                          >
                          <span id="route-date" class="route-date"
                            >23 Jan, 2026</span
                          >

                          <span
                            class="material-icons info-icon ms-2 text-warning"
                            >schedule</span
                          >
                          <span id="route-time" class="route-time"
                            >10:30 AM</span
                          >

                          <span class="d-flex align-items-center gap-1">
                            <span
                              class="material-icons info-icon text-warning trip-type-icon"
                            >
                              trending_flat
                            </span>
                            <span id="trip-type-text" class="trip-type-text"
                              >One Way</span
                            >
                          </span>

                          <span
                            id="route-return-wrapper"
                            class="route-return-wrapper"
                            style="display: none"
                            class="ms-2"
                          >
                            <div class="d-flex align-items-center gap-1">
                              <span
                                class="material-icons info-icon text-warning"
                                >event</span
                              >
                              <span
                                id="route-return-date"
                                class="route-return-date"
                              ></span>
                            </div>
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >route</span
                            >
                            <span id="route-distance" class="route-distance"
                              >0 kms</span
                            >
                          </span>

                          <!-- Duration -->
                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >schedule</span
                            >
                            <span id="route-duration" class="route-duration"
                              >0 hrs</span
                            >
                          </span>
                        </div>
                      </div>
                    </div>

                    <!--                    <button class="back-btn">-->
                    <!--  <span class="material-icons  back-icon" >arrow_back</span>-->
                    <!--  Back-->
                    <!--</button>-->

                    <div id="cab-list">
                      <!-- Cab cards will be inserted here -->
                    </div>

                    <div id="inclusions-view" style="display: none">
                      <div
                        class="inclusions-header d-flex justify-content-between align-items-center mb-3"
                      >
                        <h4 class="mb-0" id="inclusions-view-title">
                          Inclusions & Exclusions
                        </h4>
                        <button
                          id="close-inclusions"
                          class="inclusions-back-btn"
                        >
                          <span class="material-icons">close</span>
                        </button>
                      </div>
                      <!-- NEW CLOSE BUTTON -->

                      <div class="inclusions-content" id="inclusions-view-body">
                        <!-- Dynamic inclusions will be injected here -->
                      </div>
                    </div>
                    <!-- Inclusions & Exclusions Toggle -->

                    <!--<p class="footer-note" style="margin-top: 20px;">-->
                    <!--    <span class="material-icons">verified_user</span>-->
                    <!--    Sanitized vehicles • Professional chauffeurs-->
                    <!--</p>-->
                  </div>

                  <!-- Modal 3: Passenger Details -->
                  <div id="modal-3" class="modal-content hidden">
                    <!-- HEADER -->
                    <div class="modal-header">
                      <div class="row w-100 align-items-center">
                        <!-- Back Button -->
                        <div class="col-3 text-start">
                          <button type="button" class="back-btn">
                            <span class="material-icons back-icon"
                              >arrow_back</span
                            >
                            Back
                          </button>
                        </div>

                        <!-- Title -->
                        <div class="col-9 text-start">
                          <h2 class="font-display mb-0">
                            Passenger <span class="primary-color">Details</span>
                          </h2>
                        </div>
                      </div>
                    </div>
                    <div class="w-100">
                      <div class="route-pill" id="route-summary">
                        <div class="route-line-1">
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text from-location-3"
                              id="from-location-3"
                            ></span>
                          </div>
                          <span class="material-icons route-arrow"
                            >arrow_forward</span
                          >
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text to-location-3"
                              id="to-location-3"
                              >Pune</span
                            >
                          </div>
                        </div>

                        <div class="route-line-2">
                          <span class="material-icons info-icon text-warning"
                            >event</span
                          >
                          <span id="route-date" class="route-date"
                            >23 Jan, 2026</span
                          >

                          <span
                            class="material-icons info-icon ms-2 text-warning"
                            >schedule</span
                          >
                          <span id="route-time" class="route-time"
                            >10:30 AM</span
                          >

                          <span class="d-flex align-items-center gap-1">
                            <span
                              class="material-icons info-icon text-warning trip-type-icon"
                            >
                              trending_flat
                            </span>
                            <span id="trip-type-text" class="trip-type-text"
                              >One Way</span
                            >
                          </span>

                          <span
                            id="route-return-wrapper"
                            class="route-return-wrapper"
                            style="display: none"
                            class="ms-2"
                          >
                            <div class="d-flex align-items-center gap-1">
                              <span
                                class="material-icons info-icon text-warning"
                                >event</span
                              >
                              <span
                                id="route-return-date"
                                class="route-return-date"
                              ></span>
                            </div>
                          </span>

                          <!-- Distance -->
                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >route</span
                            >
                            <span id="route-distance" class="route-distance"
                              >0 kms</span
                            >
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >schedule</span
                            >
                            <span id="route-duration" class="route-duration"
                              >0 hrs</span
                            >
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >local_taxi</span
                            >
                            <span class="selected-vehicle">Mini</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div class="container">
                      <div class="row mt-2">
                        <div class="d-none">
                          <div class="col-md-12">
                            <div class="form-group address-group">
                              <label>
                                <span class="material-icons">my_location</span>
                                Pickup Address
                              </label>

                              <div class="input-with-icon">
                                <input
                                  type="text"
                                  id="pickup-address"
                                  class="glass-input"
                                  placeholder="Enter pickup address"
                                  autocomplete="off"
                                />
                                <span
                                  class="material-icons location-icon"
                                  id="pickup-map-btn"
                                  role="button"
                                  tabindex="0"
                                  title="Open map to choose pickup address"
                                  aria-label="Open map to choose pickup address"
                                  onclick="openPickupMap()"
                                >
                                  my_location
                                </span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-12">
                            <div class="form-group address-group">
                              <label>
                                <span class="material-icons">location_on</span>
                                Drop Address
                              </label>

                              <div class="input-with-icon">
                                <input
                                  type="text"
                                  id="drop-address"
                                  class="glass-input"
                                  placeholder="Enter drop address"
                                  autocomplete="off"
                                />
                                <span
                                  class="material-icons location-icon"
                                  id="drop-map-btn"
                                  role="button"
                                  tabindex="0"
                                  title="Open map to choose dropff address"
                                  aria-label="Open map to choose dropoff address"
                                  onclick="openDropMap()"
                                >
                                  location_on
                                </span>

                                <!--<span class="material-icons location-icon" id="drop-map-btn" onclick="openDropMap()">-->
                                <!--  location_on-->
                                <!--</span>-->
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>
                              <span class="material-icons">person</span>
                              Full Name
                            </label>
                            <input
                              type="text"
                              id="full-name"
                              class="glass-input"
                              placeholder="Enter your full name"
                              oninput="
                                this.value = this.value
                                  .replace(/[^0-9a-zA-Z ]/g, '')
                                  .slice(0, 30)
                              "
                            />
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label>
                              <span class="material-icons">call</span>
                              Mobile Number
                            </label>
                            <input
                              type="tel"
                              id="phone"
                              class="glass-input"
                              placeholder="9876543210"
                              oninput="
                                this.value = this.value
                                  .replace(/[^0-9]/g, '')
                                  .slice(0, 10)
                              "
                            />
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-group">
                            <label>
                              <span class="material-icons">mail</span>
                              Email Address
                            </label>
                            <input
                              type="email"
                              id="email"
                              class="glass-input"
                              placeholder="example@gmail.com"
                              oninput="
                                this.value = this.value
                                  .replace(/[^0-9a-zA-Z@#.-%&]/g, '')
                                  .slice(0, 40)
                              "
                            />
                          </div>
                        </div>

                        <div class="col-6">
                          <div class="form-group">
                            <label>
                              <span class="material-icons">group</span>
                              Passengers
                            </label>
                            <div class="passenger-selector">
                              <button
                                id="decrease-passengers"
                                class="selector-btn"
                              >
                                <span class="material-icons">remove</span>
                              </button>
                              <span
                                id="passenger-count-display"
                                class="count-display"
                                >1</span
                              >
                              <button
                                id="increase-passengers"
                                class="selector-btn"
                              >
                                <span class="material-icons">add</span>
                              </button>
                            </div>
                          </div>
                        </div>

                        <div class="col-6">
                          <div class="form-group">
                            <label>
                              <span class="material-icons">shopping_bag</span>
                              Luggage
                            </label>
                            <div class="passenger-selector">
                              <button
                                id="decrease-luggage"
                                class="selector-btn"
                              >
                                <span class="material-icons">remove</span>
                              </button>
                              <span
                                id="luggage-count-display"
                                class="count-display"
                                >0</span
                              >
                              <button
                                id="increase-luggage"
                                class="selector-btn"
                              >
                                <span class="material-icons">add</span>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="text-center mt-3">
                        <button class="glow-button" id="next-to-payment-btn">
                          Next
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Modal 4: Payment -->
                  <div id="modal-4" class="modal-content hidden">
                    <div class="modal-header">
                      <div class="row w-100 align-items-center">
                        <!-- Left: Back Button -->
                        <div class="col-3 text-start">
                          <button class="back-btn">
                            <span class="material-icons back-icon"
                              >arrow_back</span
                            >
                            Back
                          </button>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-9 text-start">
                          <h2 class="font-display mb-0">
                            Booking <span class="primary-color">Summary</span>
                          </h2>
                        </div>
                      </div>
                    </div>

                    <div class="w-100">
                      <div class="route-pill" id="route-summary">
                        <div class="route-line-1">
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text from-location-3"
                              id="from-location-3"
                            ></span>
                          </div>
                          <span class="material-icons route-arrow"
                            >arrow_forward</span
                          >
                          <div class="d-flex gap-1">
                            <span class="material-icons route-icon text-warning"
                              >location_on</span
                            >
                            <span
                              class="route-text to-location-3"
                              id="to-location-3"
                              >Pune</span
                            >
                          </div>
                        </div>

                        <div class="route-line-2">
                          <span class="material-icons info-icon text-warning"
                            >event</span
                          >
                          <span id="route-date" class="route-date"
                            >23 Jan, 2026</span
                          >

                          <span
                            class="material-icons info-icon ms-2 text-warning"
                            >schedule</span
                          >
                          <span id="route-time" class="route-time"
                            >10:30 AM</span
                          >

                          <span class="d-flex align-items-center gap-1">
                            <span
                              class="material-icons info-icon text-warning trip-type-icon"
                            >
                              trending_flat
                            </span>
                            <span id="trip-type-text" class="trip-type-text"
                              >One Way</span
                            >
                          </span>

                          <span
                            id="route-return-wrapper"
                            class="route-return-wrapper"
                            style="display: none"
                            class="ms-2"
                          >
                            <div class="d-flex align-items-center gap-1">
                              <span
                                class="material-icons info-icon text-warning"
                                >event</span
                              >
                              <span
                                id="route-return-date"
                                class="route-return-date"
                              ></span>
                            </div>
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >route</span
                            >
                            <span id="route-distance" class="route-distance"
                              >0 kms</span
                            >
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >schedule</span
                            >
                            <span id="route-duration" class="route-duration"
                              >0 hrs</span
                            >
                          </span>

                          <span class="d-flex align-items-center gap-1">
                            <span class="material-icons info-icon text-warning"
                              >local_taxi</span
                            >
                            <span class="selected-vehicle">Mini</span>
                          </span>
                        </div>
                      </div>
                    </div>

                    <div class="form-group mt-2">
                      <div class="fare-breakdown d-none">
                        <!--<div class="breakup-row">-->
                        <!--  <span class="breakup-label">Pickup</span>-->
                        <!--  <span class="breakup-value" id="preview-pickup">-</span>-->
                        <!--</div>-->

                        <!--<div class="breakup-row">-->
                        <!--  <span class="breakup-label">Destination</span>-->
                        <!--  <span class="breakup-value" id="preview-destination">-</span>-->
                        <!--</div>-->

                        <!--<div class="breakup-row">-->
                        <!--  <span class="breakup-label">Date & Time</span>-->
                        <!--  <span class="breakup-value" id="preview-datetime">-</span>-->
                        <!--</div>-->

                        <!--<div class="breakup-row">-->
                        <!--  <span class="breakup-label">Trip Type</span>-->
                        <!--  <span class="breakup-value" id="preview-triptype">One Way</span>-->
                        <!--</div>-->

                        <div class="breakup-row flex-column">
                          <span class="breakup-label">Pickup Address</span>
                          <span id="preview-pickup-address">--</span>
                        </div>

                        <div class="breakup-row flex-column">
                          <span class="breakup-label">Drop Address</span>
                          <span id="preview-drop-address">--</span>
                        </div>

                        <!--<div class="breakup-row">-->
                        <!--    <span class="breakup-label">Vehicle</span>-->
                        <!--    <span class="breakup-value" id="preview-vehicle">-</span>-->
                        <!--</div>-->
                      </div>
                    </div>

                    <div class="safety-box">
                      <div class="safety-header" id="inclusion-content-toggle">
                        <span class="material-icons text-warning">shield</span>
                        <span>Inclusions & Exclusions</span>
                        <span class="material-icons ms-auto">expand_more</span>
                      </div>

                      <div class="safety-header" id="toggle-safety">
                        <span class="material-icons text-warning">shield</span>
                        <span>Safety Guidelines</span>
                        <span class="material-icons ms-auto">expand_more</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="fare-breakdown">
                        <!--<div class="breakup-header" id="toggle-breakup">-->
                        <!--    <span style="font-weight: 600;">View Fare Breakup</span>-->
                        <!--    <span class="material-icons" id="breakup-chevron">expand_more</span>-->
                        <!--</div>-->
                        <div id="breakup-details" class="breakup-details">
                          <div class="breakup-row">
                            <span class="breakup-label">Base Fare</span>
                            <span class="breakup-value">₹2,873</span>
                          </div>
                          <div class="breakup-row">
                            <span class="breakup-label">Govt. levy extra</span>
                            <span class="breakup-value">₹380</span>
                          </div>
                          <!--<div class="breakup-row">-->
                          <!--    <span class="breakup-label">Taxes & Fees</span>-->
                          <!--    <span class="breakup-value">₹258</span>-->
                          <!--</div>-->
                          <!--<div class="breakup-total">-->
                          <!--    <span>Total</span>-->
                          <!--    <span class="primary-color" style="font-size: 16px;">₹3,511</span>-->
                          <!--</div>-->
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <div
                        style="
                          display: flex;
                          justify-content: space-between;
                          align-items: center;
                          margin-bottom: 15px;
                          padding: 0 8px;
                        "
                      >
                        <span
                          style="
                            font-size: 11px;
                            color: rgba(255, 255, 255, 0.5);
                            text-transform: uppercase;
                            letter-spacing: 0.1em;
                            font-weight: 700;
                          "
                        >
                          Estimated fare</span
                        >
                        <span
                          style="font-weight: 700; font-size: 22px"
                          class="estimated_fare"
                          >₹3,511</span
                        >
                      </div>
                      <button class="glow-button" id="proceed-to-payment-btn">
                        Confirm
                      </button>
                    </div>

                    <div
                      id="safety-view"
                      class="safety-view"
                      style="display: none"
                    >
                      <div
                        class="inclusions-header d-flex justify-content-between align-items-center mb-3"
                      >
                        <h4 class="mb-0" id="safety-view-title">
                          Safety Guidelines
                        </h4>

                        <button id="close-safety" class="inclusions-back-btn">
                          <span class="material-icons">close</span>
                        </button>
                      </div>

                      <div
                        class="inclusions-content inclusions-col"
                        id="safety-view-body"
                      >
                        <h6 class="section-title">Before Starting the Ride</h6>
                        <ul class="safety-list">
                          <li>
                            <i class="fa fa-check-circle"></i> Verify the
                            driver’s photo and name
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Check vehicle
                            details (number plate & model)
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Cross check ride charges & Kms
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Take odometer
                            photo before trip starts
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Share trip
                            details with trusted contact
                          </li>
                        </ul>

                        <h6 class="section-title">After Completing the Ride</h6>
                        <ul class="safety-list">
                          <li>
                            <i class="fa fa-check-circle"></i> Take final
                            odometer photo
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Cross-check Govt.
                            levy with receipts
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Collect all your
                            belongings
                          </li>
                          <li>
                            <i class="fa fa-check-circle"></i> Confirm payment
                            after verifying charges
                          </li>
                        </ul>
                      </div>
                    </div>

                    <div id="inclusions-view-2" style="display: none">
                      <div
                        class="inclusions-header d-flex justify-content-between align-items-center mb-3"
                      >
                        <h4 class="mb-0" id="inclusions-view-title-2">
                          Inclusions & Exclusions
                        </h4>
                        <button
                          id="close-inclusions-2"
                          class="inclusions-back-btn"
                        >
                          <span class="material-icons">close</span>
                        </button>
                      </div>

                      <div
                        class="inclusions-content"
                        id="inclusions-view-body-2"
                      ></div>
                    </div>
                  </div>

                  <!-- Modal 5: Payment Screen -->
                  <div id="modal-5" class="modal-content hidden">
                    <div class="modal-header">
                      <div class="row w-100 align-items-center">
                        <!-- Left: Back Button -->
                        <div class="col-3 text-start">
                          <button class="back-btn">
                            <span class="material-icons back-icon"
                              >arrow_back</span
                            >
                            Back
                          </button>
                        </div>

                        <!-- Center: Title -->
                        <div class="col-9 text-start">
                          <h2 class="font-display mb-0">
                            Pay via <span class="primary-color">UPI</span>
                          </h2>
                        </div>

                        <!-- Right: Total Amount -->
                        <div class="col-3 text-end">
                          <p
                            class="mb-0"
                            style="
                              font-size: 11px;
                              color: rgba(255, 255, 255, 0.5);
                              text-transform: uppercase;
                              letter-spacing: 0.1em;
                              font-weight: 700;
                            "
                          >
                            Total Amount
                          </p>
                          <p
                            class="primary-color mb-0"
                            style="font-weight: 700; font-size: 22px"
                          >
                            ₹3,511
                          </p>
                        </div>
                      </div>
                    </div>

                    <div class="form-group" style="text-align: center">
                      <div
                        style="
                          background: white;
                          padding: 15px;
                          border-radius: 15px;
                          display: inline-block;
                          box-shadow: 0 0 20px rgba(249, 191, 0, 0.15);
                          margin-bottom: 12px;
                        "
                      >
                        <div
                          style="
                            width: 160px;
                            height: 160px;
                            background: white;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                          "
                        >
                          <div
                            style="
                              width: 140px;
                              height: 140px;
                              background: #f0f0f0;
                              display: flex;
                              align-items: center;
                              justify-content: center;
                              border: 2px dashed #ccc;
                            "
                          >
                            <span
                              class="material-icons"
                              style="color: #666; font-size: 48px"
                              >qr_code_scanner</span
                            >
                          </div>
                        </div>
                      </div>
                      <p
                        style="
                          font-size: 11px;
                          color: rgba(255, 255, 255, 0.5);
                          text-transform: uppercase;
                          letter-spacing: 0.1em;
                          font-weight: 700;
                        "
                      >
                        Scan QR Code to pay
                      </p>
                    </div>

                    <div class="form-group">
                      <h3
                        class="primary-color"
                        style="
                          font-size: 14px;
                          margin-bottom: 12px;
                          text-transform: uppercase;
                          letter-spacing: 0.1em;
                        "
                      >
                        Popular UPI Apps
                      </h3>
                      <div class="grid-2">
                        <button
                          class="secondary-button"
                          style="flex-direction: column; padding: 12px"
                          id="google-pay-btn"
                        >
                          <span class="material-icons" style="font-size: 28px"
                            >account_balance_wallet</span
                          >
                          <span style="font-size: 11px; margin-top: 4px"
                            >Google Pay</span
                          >
                        </button>
                        <button
                          class="secondary-button"
                          style="flex-direction: column; padding: 12px"
                          id="phonepe-btn"
                        >
                          <span class="material-icons" style="font-size: 28px"
                            >payments</span
                          >
                          <span style="font-size: 11px; margin-top: 4px"
                            >PhonePe</span
                          >
                        </button>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="upi-id">
                        <span class="material-icons">send_to_mobile</span>
                        Enter UPI ID
                      </label>
                      <div style="position: relative">
                        <input
                          type="text"
                          id="upi-id"
                          class="glass-input"
                          placeholder="example@upi"
                        />
                        <button
                          id="verify-upi"
                          style="
                            position: absolute;
                            right: 8px;
                            top: 50%;
                            transform: translateY(-50%);
                            background: #f9bf00;
                            color: black;
                            border: none;
                            padding: 6px 12px;
                            border-radius: 6px;
                            font-weight: 700;
                            cursor: pointer;
                            font-size: 11px;
                          "
                        >
                          Verify
                        </button>
                      </div>
                    </div>

                    <div class="form-group">
                      <button class="glow-button" id="complete-payment-btn">
                        Complete Payment
                      </button>
                      <p class="footer-note" style="margin-top: 15px">
                        <span class="material-icons">shield</span>
                        SECURE 256-BIT ENCRYPTED PAYMENT
                      </p>
                    </div>
                  </div>

                  <!-- Modal 6: Confirmation -->
                  <div id="modal-6" class="modal-content hidden">
                    <div
                      class="modal-header position-relative"
                      style="flex-direction: column; gap: 8px"
                    >
                      <!-- Back Button (Top Left) -->
                      <!--<button class="back-btn position-absolute" style="top: 10px; left: 10px;">-->
                      <!--  <span class="material-icons back-icon">arrow_back</span>-->
                      <!--  Back-->
                      <!--</button>-->

                      <!-- Success Icon -->
                      <div class="confirm-icon-circle">
                        <span class="material-icons primary-color confirm-icon">
                          check_circle
                        </span>
                      </div>

                      <!-- Title -->
                      <h2
                        class="font-display"
                        style="margin: 0; font-size: 20px !important"
                      >
                        Booking Confirmed!
                      </h2>

                      <!-- Booking ID -->
                      <div class="booking-id-box">
                        <p class="modal-subtitle" style="font-size: 13px">
                          Booking ID:
                          <span class="primary-color booking-id" id="job_id"
                            >######</span
                          >
                        </p>
                      </div>
                    </div>

                    <div class="form-group trip-mini-card">
                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon"
                          >my_location</span
                        >
                        <div class="mini-text">
                          <small>Pickup</small>
                          <p id="confirm-pickup"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon"
                          >location_on</span
                        >
                        <div class="mini-text">
                          <small>Destination</small>
                          <p id="confirm-destination"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row d-none">
                        <span class="material-icons text-warning"
                          >my_location</span
                        >
                        <div class="mini-text">
                          <small>Pickup Address</small>
                          <p id="confirm-pickup-address"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row d-none">
                        <span class="material-icons text-warning"
                          >location_on</span
                        >
                        <div class="mini-text">
                          <small>Drop Address</small>
                          <p id="confirm-drop-address"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon">schedule</span>
                        <div class="mini-text">
                          <small>Date & Time</small>
                          <p id="confirm-datetime"></p>
                        </div>
                      </div>

                      <div
                        class="trip-mini-row return-row"
                        id="confirm-return-row"
                        style="display: none"
                      >
                        <span class="material-icons mini-icon"
                          >keyboard_return</span
                        >
                        <div class="mini-text">
                          <small>Return Date</small>
                          <p id="confirm-return-date"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon"
                          >directions_car</span
                        >
                        <div class="mini-text">
                          <small>Vehicle</small>
                          <p id="confirm-vehicle" class="vehicle-highlight"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon">route</span>
                        <div class="mini-text">
                          <small>Distance</small>
                          <p class="vehicle-highlight route-distance"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon">schedule</span>
                        <div class="mini-text">
                          <small>Duration</small>
                          <p class="vehicle-highlight route-duration"></p>
                        </div>
                      </div>

                      <div class="trip-mini-row">
                        <span class="material-icons mini-icon"
                          >currency_rupee</span
                        >
                        <div class="mini-text">
                          <small>Estimated Fare</small>
                          <p class="vehicle-highlight estimated_fare">5,305</p>
                        </div>
                      </div>
                    </div>

                    <div style="display: inline-flex; justify-content: center">
                      <div class="booking-id-preview">
                        <p class="modal-subtitle" style="text-decoration: none;display: flex;align-items: center;gap: 12px;x">
                          <a
                            href=""
                            style="text-decoration: none;display: flex;
    align-items: center;
    gap: 8px;"
                            id="b-pre-link"
                          >
                               <span class="material-icons text-dark" style="font-size: 18px;">
          visibility
        </span>
                            Booking Information
                          </a>
                        </p>
                      </div>
                    </div>

                    <div class="form-group" style="text-align: center">
                              <div>
                                <p
                                  style="
                                    color: rgba(255, 255, 255, 0.65);
                                    line-height: 1.5;
                                    font-size: 12px !important;
                                    margin: 0;
                                  "
                                >
                                  Your booking has been successfully confirmed. A driver will be assigned
                                  soon, and you will be contacted with the trip details.
                                </p>
                            
                                <!-- Support Info -->
                                <p
                                  style="
                                    margin-top: 6px;
                                    font-size: 11px;
                                    color: rgba(255, 255, 255, 0.55);
                                  "
                                >
                                  Need help?
                                  <a
                                    href="mailto:support@goride.run"
                                    style="color: #ffffff; text-decoration: none;" class="ms-1"
                                  >
                                    support@goride.run
                                  </a>
                                  |
                                  <a
                                    href="tel:+916369742104"
                                    style="color: #ffffff; text-decoration: none;"
                                  >
                                    +91 63697 42104
                                  </a>
                                </p>
                              </div>
                            </div>
                    <!-- WhatsApp Share & Support -->
                    <div
                      class="form-group d-none"
                      style="text-align: center; margin-top: 12px"
                    >
                      <div
                        style="
                          display: flex;
                          gap: 14px;
                          justify-content: center;
                          align-items: center;
                          flex-wrap: wrap;
                        "
                      >
                        <a
                          class="btn-whatsapp"
                          id="share-whatsapp-btn"
                          href="javascript:void(0)"
                          onclick="shareWhatsapp()"
                        >
                          <span class="material-icons">share</span>
                          <span class="btn-text">Share on WhatsApp</span>
                        </a>

                        <!-- Return to Dashboard -->
                        <a
                          href="#"
                          id="return-to-dashboard"
                          style="
                            color: rgba(255, 255, 255, 0.6);
                            text-transform: uppercase;
                            letter-spacing: 0.1em;
                            font-weight: 700;
                            font-size: 11px;
                            text-decoration: none;
                            border: 1px solid rgba(249, 191, 0, 0.3);
                            padding: 6px 12px;
                            border-radius: 8px;
                          "
                        >
                          New Booking
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <!--<div id="mapModal" class="hidden open-mapModal">-->
                <!--    <div id="osm-map" style="height:400px;"></div>-->
                <!--    <button id="confirm-location" class="glow-button">Use this location</button>-->
                <!--</div>-->

                <div id="mapModal" class="hidden open-mapModal">
                  <div id="map" style="height: 100%; border-radius: 30px"></div>
                  <button
                    id="confirm-location"
                    class="glow-button location-choose-btn"
                  >
                    Use this location
                  </button>
                  <button
                    onclick="closeMap()"
                    class="glow-button location-close-btn material-icons"
                  >
                    close
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--<div id="india-content2">-->
    <!--    @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)-->
    <!--    <div class="d-none d-md-block">-->
    <!--      <a href="/jobs" class="theme-btn btn">-->
    <!--        Get Your Jobs <i class="fa-solid fa-print-magnifying-glass ms-2"></i>-->
    <!--      </a>-->
    <!--      <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank" class="theme-btn3 download-app btn">-->
    <!--        Go Ride Partner App <i class="fa-brands fa-google-play ms-2"></i>-->
    <!--      </a>-->
    <!--    </div>-->
    <!--    @else-->
    <!--    <div class="d-none d-md-block">-->
    <!--      <a href="/login" class="theme-btn btn">-->
    <!--        Get Your Jobs <i class="fa-solid fa-print-magnifying-glass ms-2"></i>-->
    <!--      </a>-->
    <!--    </div>-->
    <!--    @endif-->
    <!--</div>-->
  </div>
</header>

<section class="features-grid-section">
  <div class="row mb-3 mb-md-5">
    <div class="section-title text-center">
      Why Choose&nbsp;<span> GoRide</span>
    </div>
  </div>
  <div class="row justify-content-center align-items-stretch">
    <div class="col-12 col-md-3 feature-col">
      <div class="feature-card">
        <div class="feature-img-wrap mt-3">
          <img
            src="https://www.goride.run/goride/img/c2.webp"
            alt="Book Ride"
          />
        </div>
        <h3 class="section-title">
          Book a <span> ride </span> in seconds. Reach safely, every time.
        </h3>
        <p>
          From daily commutes to long-distance travel, we make every ride
          smooth, affordable, and reliable.
        </p>
      </div>
    </div>

    <div class="col-12 col-md-3 feature-col">
      <div class="feature-card">
        <div class="feature-img-wrap mt-3">
          <img
            src="https://www.goride.run/goride/img/c1.png"
            alt="Anytime Anywhere"
          />
        </div>
        <h3 class="section-title">
          Book a <span> cab </span> anytime, anywhere with just a few taps.
        </h3>
        <p>
          Fast pickups, professional drivers, and real-time tracking — all in
          one app.
        </p>
      </div>
    </div>

    <div class="col-12 col-md-3 feature-col">
      <div class="feature-card">
        <div class="feature-img-wrap mt-3">
          <img
            src="https://www.goride.run/goride/img/c3.png"
            alt="Endless Journeys"
          />
        </div>
        <h3 class="section-title">One <span> App.</span> Endless Journeys.</h3>
        <p>
          Quick and affordable rides within your city, Airport Transfers, and
          Outstation Trips.
        </p>
      </div>
    </div>

    <div class="col-12 col-md-3 feature-col">
      <div class="feature-card">
        <div class="feature-img-wrap mt-3">
          <img
            src="https://www.goride.run/goride/img/carevery.webp"
            alt="For Everyone"
          />
        </div>
        <h3 class="section-title">
          <span> GoRide </span> Is There For Everyone!
        </h3>
        <p>For Every Budget. For Every Distance. For Every Duration.</p>
      </div>
    </div>
  </div>
</section>

<!--<section class="about section-padding pt-5" id="about">-->
<!--    <div class="container">-->
<!--        <div class="row">-->
<!--            <div class="col-lg-6 col-md-12  i_phone mb-md-30">-->
<!--                <div class="content" data-aos="fade-down" data-aos-duration="1000">-->
<!--                    <div class="text-center text-md-start">-->
<!--                        <h1 class="section-subtitle">The Heartbeat of Everyday Travel</h1>-->
<!--                    </div>-->
<!--                    <div class="section-title text-center text-md-start"><span>Smarter Rides. Seamless Journeys.</span>-->
<!--                    </div>-->
<!--                    <p class="fw-normal" style="text-align: justify;">Welcome to <strong>GoRide</strong> — your trusted-->
<!--                        cab booking platform designed to make every journey-->
<!--                        smooth, safe, and reliable. Whether it’s a short city ride or a long-distance trip, GoRide-->
<!--                        connects you with the right ride at the right time.</p>-->
<!--                    <p class=" fw-normal" style="text-align: justify;">With intelligent ride matching, real-time-->
<!--                        tracking, and dependable drivers, we make getting-->
<!--                        rides effortless.</p>-->
<!--                    <div class="text-center text-md-start">-->
<!--                        <a href="about" class=" button-4 mb-3 mb-md-0">Learn More About Go Ride's Services <span-->
<!--                                class="ti-arrow-top-right"></span></a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="col-lg-5 offset-lg-1 col-md-12">-->
<!--                <div class="item " data-aos="fade-down" data-aos-duration="1000"> <img-->
<!--                        src="{{ asset('goride/img/abt-1.webp') }}" class="img-fluid" alt="about">-->
<!--                    <div class="curv-butn icon-bg">-->
<!--                        <img src="{{ asset('goride/img/g.png') }}" alt="Play Button" style="width: 50px; height: 50px;">-->
<!--                        <div class="br-left-top">-->
<!--                            <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">-->
<!--                                <path-->
<!--                                    d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                    fill="#ffffff"></path>-->
<!--                            </svg>-->
<!--                        </div>-->
<!--                        <div class="br-right-bottom">-->
<!--                            <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">-->
<!--                                <path-->
<!--                                    d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                    fill="#ffffff"></path>-->
<!--                            </svg>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<!--<section class="features section-padding mt-5 mb-3 mb-md-5" id="features">-->
<!--    <div class="container">-->
<!--        <div class="row">-->
<!--            <div class="col-md-12 text-center">-->
<!--                <div class="section-title text-center"><span> GoRide</span> – The Smart Way to Move</div>-->

<!--                <p class="text-center">Explore the Power Behind Every Ride.GoRide adapts to your travel needs with-->
<!--                    flexible options designed for individuals, families,-->
<!--                    and businesses alike.</p>-->

<!--            </div>-->
<!--        </div>-->
<!--        <div class="row justify-content-around">-->

<!--            <div class="col-md-2 text-center mb-3">-->
<!--                <div class="mycard wallet mx-auto" data-aos="fade-up" data-aos-duration="1000">-->
<!--                    <img src="{{ asset('goride/img/movee1.webp') }}" style="width:100px;" alt="settings">-->
<!--                    <div class="overlay"></div>-->
<!--                    <h3 class="big-para">Smart Ride Matching</h3>-->
<!--                    <p class="text-center">Our intelligent system instantly connects riders with nearby drivers,-->
<!--                        ensuring faster pickups-->
<!--                        and smoother journeys every time.</p>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="col-md-2 text-center mb-3">-->
<!--                <div class="mycard wallet mx-auto" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">-->
<!--                    <img src="{{ asset('goride/img/movee2.webp') }}" style="width:100px;" alt="scalability">-->
<!--                    <div class="overlay"></div>-->
<!--                    <h3 class="big-para">Live Ride & Fleet Tracking</h3>-->
<!--                    <p class="text-center">Track your ride in real time with accurate location updates. Stay informed-->
<!--                        about your driver,-->
<!--                        route, and arrival time—all in one place.</p>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="col-md-2 text-center mb-3">-->
<!--                <div class="mycard wallet mx-auto" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">-->
<!--                    <img src="{{ asset('goride/img/movee3.webp') }}" style="width:100px;" alt="globe">-->
<!--                    <div class="overlay"></div>-->
<!--                    <h3 class="big-para">Smart Pricing for Every Ride</h3>-->
<!--                    <p class="text-center">Enjoy fair and transparent pricing powered by smart demand insights. Get the-->
<!--                        best value-->
<!--                        whether you're commuting daily or traveling far. </p>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="col-md-2 text-center mb-3">-->
<!--                <div class="mycard wallet mx-auto" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">-->
<!--                    <img src="{{ asset('goride/img/movee4.webp') }}" style="width:100px;" alt="productivity">-->
<!--                    <div class="overlay"></div>-->
<!--                    <h3 class="big-para">Insights That Keep You Moving</h3>-->
<!--                    <p class="text-center">Get clear ride insights and performance updates to help improve efficiency-->
<!--                        and ensure a-->
<!--                        better travel experience.</p>-->
<!--                </div>-->
<!--            </div>-->

<!--            <div class="col-md-2 text-center mb-3">-->
<!--                <div class="mycard wallet mx-auto" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">-->
<!--                    <img src="{{ asset('goride/img/movee5.webp') }}" style="width:100px;" alt="trade">-->
<!--                    <div class="overlay"></div>-->
<!--                    <h3 class="big-para">Built to Fit Your Needs</h3>-->
<!--                    <p class="text-center">GoRide adapts to your travel needs with flexible options designed for-->
<!--                        individuals, families,-->
<!--                        and businesses alike.</p>-->
<!--                </div>-->
<!--            </div>-->

<!--        </div>-->
<!--    </div>-->

<!--</section>-->

<!--<section class="how">-->
<!--    <div class="container">-->
<!--        <div class="row">-->
<!--            <div class="col-md-12">-->
<!--                <div class="section-title text-center">-->
<!--                    How <span>GoRide</span> Works-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="timeline d-none d-md-block">-->
<!--            <div class="roadmap row">-->
<!--                <div class="step col-md-3" data-aos="fade-up" data-aos-duration="2000">-->
<!--                    <div class="content yellow">-->
<!--                        <div class="steps d-flex align-items-center">-->
<!--                            <div class="step-icon">-->
<!--                                <i class="fa-solid fa-user-plus"></i>-->
<!--                            </div>-->
<!--                            <h3 class="m-0">1. Get Started</h3>-->
<!--                        </div>-->
<!--                        <p>-->
<!--                            Sign up in minutes and get ready to ride. We'll help you get set up quickly and smoothly.-->
<!--                        </p>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="step col-md-3" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="100">-->
<!--                    <div class="content orange">-->
<!--                        <div class="steps d-flex align-items-center">-->
<!--                            <div class="step-icon">-->
<!--                                <i class="fas fa-user-friends"></i>-->
<!--                            </div>-->
<!--                            <h3 class="m-0">2. Get Matched</h3>-->
<!--                        </div>-->
<!--                        <p>-->
<!--                            Our smart system connects you with the nearest driver for faster pickups and smoother-->
<!--                            journeys.-->
<!--                        </p>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="step col-md-3" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="200">-->
<!--                    <div class="content red">-->
<!--                        <div class="steps d-flex align-items-center">-->
<!--                            <div class="step-icon">-->
<!--                                <i class="fas fa-map-marked-alt"></i>-->
<!--                            </div>-->
<!--                            <h3 class="m-0">3. Track Your Ride</h3>-->
<!--                        </div>-->
<!--                        <p>-->
<!--                            Follow your ride in real time with live location updates and accurate ETAs.-->
<!--                        </p>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="step col-md-3" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="300">-->
<!--                    <div class="content blue">-->
<!--                        <div class="steps d-flex align-items-center">-->
<!--                            <div class="step-icon">-->
<!--                                <i class="fa-solid fa-car-side"></i>-->
<!--                            </div>-->
<!--                            <h3 class="m-0">4. Ride Smarter</h3>-->
<!--                        </div>-->
<!--                        <p>-->
<!--                            View trip details, track performance, and make smarter decisions with easy insights.-->
<!--                        </p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

<!--        <div class="d-md-none">-->
<!--            <div id="stepsCarousel" class="carousel slide" data-bs-ride="carousel">-->
<!--                <div class="carousel-inner">-->

<!--                    <div class="step">-->
<!--                        <div class="carousel-item active">-->
<!--                            <div class="content yellow text-center p-4 mx-3">-->
<!--                                <div class="steps d-flex justify-content-start justify-content-md-center align-items-center mb-3">-->
<!--                                    <div class="step-icon">-->
<!--                                        <i class="fa-solid fa-user-plus"></i>-->
<!--                                    </div>-->
<!--                                    <h3 class="m-0 ">1. Get Started</h3>-->
<!--                                </div>-->
<!--                                <p class="mb-0">-->
<!--                                    Sign up in minutes and get ready to ride. We'll help you get set up quickly and-->
<!--                                    smoothly.-->
<!--                                </p>-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="step">-->
<!--                        <div class="carousel-item">-->
<!--                            <div class="content orange text-center p-4 mx-3">-->
<!--                                <div class="steps d-flex justify-content-start justify-content-md-center align-items-center mb-3">-->
<!--                                    <div class="step-icon">-->
<!--                                        <i class="fas fa-user-friends"></i>-->
<!--                                    </div>-->
<!--                                    <h3 class="m-0 ">2. Get Matched</h3>-->
<!--                                </div>-->
<!--                                <p class="mb-0">-->
<!--                                    Our smart system connects you with the nearest driver for faster pickups and-->
<!--                                    smoother journeys.-->
<!--                                </p>-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="step">-->
<!--                        <div class="carousel-item">-->
<!--                            <div class="content red text-center p-4 mx-3">-->
<!--                                <div class="steps d-flex justify-content-start justify-content-md-center align-items-center mb-3">-->
<!--                                    <div class="step-icon">-->
<!--                                        <i class="fas fa-map-marked-alt"></i>-->
<!--                                    </div>-->
<!--                                    <h3 class="m-0 ">3. Track Your Ride</h3>-->
<!--                                </div>-->
<!--                                <p class="mb-0">-->
<!--                                    Follow your ride in real time with live location updates and accurate ETAs.-->
<!--                                </p>-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="step">-->
<!--                        <div class="carousel-item">-->
<!--                            <div class="content blue text-center p-4 mx-3">-->
<!--                                <div class="steps d-flex justify-content-start justify-content-md-center align-items-center mb-3">-->
<!--                                    <div class="step-icon">-->
<!--                                        <i class="fa-solid fa-car-side"></i>-->
<!--                                    </div>-->
<!--                                    <h3 class="m-0 ">4. Ride Smarter</h3>-->
<!--                                </div>-->
<!--                                <p class="mb-0">-->
<!--                                    View trip details, track performance, and make smarter decisions with easy insights.-->
<!--                                </p>-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

<!--    <div class="running-taxi">-->
<!--        <div class="taxi"></div>-->
<!--        <div class="taxi taxi-2"></div>-->
<!--        <div class="taxi taxi-3"></div>-->
<!--    </div>-->
<!--</section>-->

<section class="myClients section-padding mt-5">
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center">
      <div class="section-title text-center mb-5">
        Available &nbsp;<span> Fleets&nbsp;</span>
      </div>
      <div class="col-md-12 text-center mb-30">
        <div class="row g-4 justify-content-center">
          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/mini(new1).webp') }}"
                  alt="Mini"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go Mini</h3>
                <div class="fleet-models">Indica, Micra, Ritz</div>
                <div class="fleet-desc">
                  Affordable AC rides for everyday travel.
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/primesedan(new1).webp') }}"
                  alt="Prime Sedan"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go Sedan</h3>
                <div class="fleet-models">Dzire, Etios, Sunny</div>
                <div class="fleet-desc">
                  Comfortable sedans with extra legroom.
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/primesuv(new1).webp') }}"
                  alt="Prime SUV"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go SUV</h3>
                <div class="fleet-models">Ertiga, Enjoy</div>
                <div class="fleet-desc">
                  Spacious SUVs ideal for group travel.
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/Primesuv+(new1).png') }}"
                  alt="Prime SUV+"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go SUV+</h3>
                <div class="fleet-models">Innova, Crysta</div>
                <div class="fleet-desc">Premium SUVs for smooth journeys.</div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/audi1.webp') }}"
                  alt="Prime Plus"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go Executive</h3>
                <div class="fleet-models">Audi, Benz</div>
                <div class="fleet-desc">
                  Luxury rides with top comfort & style.
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="fleet-card">
              <div class="fleet-image">
                <img
                  src="{{ asset('goride/img/xl1.webp') }}"
                  alt="XL Intercity"
                />
              </div>
              <div class="fleet-content">
                <h3 class="m-0">Go Tourister</h3>
                <div class="fleet-models">
                  Toyota, Force Motors, Tempo Traveller
                </div>
                <div class="fleet-desc">Comfortable rides for long trips.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!--<section class="testimonials section-padding mt-15 mb-5">-->
<!--        <div class="container">-->
<!--            <div class="row">-->
<!--                <div class="col-md-12 text-center mb-30">-->
<!--                    <div class="section-subtitle fw-sm-bold fs-sm-5">Testimonials</div>-->
<!--                     <div class="section-title text-center">What&nbsp;<span> Clients </span> Say</div>-->

<!--                </div>-->
<!--                <div class="col-md-12">-->
<!--                    <div class="owl-carousel owl-theme client-say">-->
<!--                        <div class="item">-->
<!--                            <div class="stars"> <span class="rate">-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                </span>-->

<!--                                <div class="shap-right-bottom">-->
<!--                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                        class="w-11 h-11">-->
<!--                                        <path-->
<!--                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                            fill="#ffffff"></path>-->
<!--                                    </svg>-->
<!--                                </div>-->
<!--                            </div> <i class="fa-solid fa-quote-left"></i>-->
<!--                            <div class="text ">-->
<!--                                <p class="fw-normal">I’ve been using GoRide for over a year now. The drivers are always punctual, the rides are-->
<!--                                    comfortable, and the real-time tracking makes commuting stress-free!</p>-->
<!--                            </div>-->
<!--                            <div class="info mt-30">-->
<!--                                <div class="img-curv">-->
<!--                                    <div class="img"> <img src="{{ asset('goride/img/usernew1.png') }}" alt="team">-->
<!--                                    </div>-->

<!--                                    <div class="shap-right-bottom">-->
<!--                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                            class="w-11 h-11">-->
<!--                                            <path-->
<!--                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                                fill="#ffffff"></path>-->
<!--                                        </svg>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="ml-30">-->
<!--                                    <h6>Ravi S</h6>-->
<!--                                    <p>Chennai</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="item">-->
<!--                            <div class="stars"> <span class="rate">-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                </span>-->

<!--                                <div class="shap-right-bottom">-->
<!--                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                        class="w-11 h-11">-->
<!--                                        <path-->
<!--                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                            fill="#ffffff"></path>-->
<!--                                    </svg>-->
<!--                                </div>-->
<!--                            </div> <i class="fa-solid fa-quote-left"></i>-->
<!--                            <div class="text">-->
<!--                                <p class="fw-normal">Booking a cab through GoRide is so easy! The app is user-friendly, multiple payment options-->
<!--                                    are a bonus, and I always feel safe sharing my ride details with my family.</p>-->
<!--                            </div>-->
<!--                            <div class="info mt-30">-->
<!--                                <div class="img-curv">-->
<!--                                    <div class="img"> <img src="{{ asset('goride/img/usernew1.png') }}" alt="team">-->
<!--                                    </div>-->

<!--                                    <div class="shap-right-bottom">-->
<!--                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                            class="w-11 h-11">-->
<!--                                            <path-->
<!--                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                                fill="#ffffff"></path>-->
<!--                                        </svg>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="ml-30">-->
<!--                                    <h6>Priya M</h6>-->
<!--                                    <p>Coimbatore</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="item">-->
<!--                            <div class="stars"> <span class="rate">-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                </span>-->

<!--                                <div class="shap-right-bottom">-->
<!--                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                        class="w-11 h-11">-->
<!--                                        <path-->
<!--                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                            fill="#ffffff"></path>-->
<!--                                    </svg>-->
<!--                                </div>-->
<!--                            </div> <i class="fa-solid fa-quote-left"></i>-->
<!--                            <div class="text">-->
<!--                                <p class="fw-normal">GoRide is my go-to app for daily commutes. The cabs arrive on time, fares are transparent,-->
<!--                                    and the support team is very responsive whenever I have questions.</p>-->
<!--                            </div>-->
<!--                            <div class="info mt-30">-->
<!--                                <div class="img-curv">-->
<!--                                    <div class="img"> <img src="{{ asset('goride/img/usernew1.png') }}" alt="team">-->
<!--                                    </div>-->

<!--                                    <div class="shap-right-bottom">-->
<!--                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                            class="w-11 h-11">-->
<!--                                            <path-->
<!--                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                                fill="#ffffff"></path>-->
<!--                                        </svg>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="ml-30">-->
<!--                                    <h6>Arjun K</h6>-->
<!--                                    <p>Madurai</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="item">-->
<!--                            <div class="stars"> <span class="rate">-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                </span>-->

<!--                                <div class="shap-right-bottom">-->
<!--                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                        class="w-11 h-11">-->
<!--                                        <path-->
<!--                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                            fill="#ffffff"></path>-->
<!--                                    </svg>-->
<!--                                </div>-->
<!--                            </div> <i class="fa-solid fa-quote-left"></i>-->
<!--                            <div class="text">-->
<!--                                <p class="fw-normal">I love the variety of cabs GoRide offers. Whether I need a quick Micro ride or a spacious SUV-->
<!--                                    for family trips, I can book it in seconds. Truly convenient!</p>-->
<!--                            </div>-->
<!--                            <div class="info mt-30">-->
<!--                                <div class="img-curv">-->
<!--                                    <div class="img"> <img src="{{ asset('goride/img/usernew1.png') }}" alt="team">-->
<!--                                    </div>-->

<!--                                    <div class="shap-right-bottom">-->
<!--                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                            class="w-11 h-11">-->
<!--                                            <path-->
<!--                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                                fill="#ffffff"></path>-->
<!--                                        </svg>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="ml-30">-->
<!--                                    <h6>Sneha R</h6>-->
<!--                                    <p>Tiruchirappalli</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="item">-->
<!--                            <div class="stars"> <span class="rate">-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                    <i class="fa-solid fa-star"></i>-->
<!--                                </span>-->

<!--                                <div class="shap-right-bottom">-->
<!--                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                        class="w-11 h-11">-->
<!--                                        <path-->
<!--                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                            fill="#ffffff"></path>-->
<!--                                    </svg>-->
<!--                                </div>-->
<!--                            </div> <i class="fa-solid fa-quote-left"></i>-->
<!--                            <div class="text">-->
<!--                                <p class="fw-normal">GoRide makes traveling across the city effortless. The drivers are professional, the rides are-->
<!--                                    clean, and I especially appreciate the safety features. Highly recommend it!</p>-->
<!--                            </div>-->
<!--                            <div class="info mt-30">-->
<!--                                <div class="img-curv">-->
<!--                                    <div class="img"> <img src="{{ asset('goride/img/usernew1.png') }}" alt="team">-->
<!--                                    </div>-->

<!--                                    <div class="shap-right-bottom">-->
<!--                                        <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg"-->
<!--                                            class="w-11 h-11">-->
<!--                                            <path-->
<!--                                                d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"-->
<!--                                                fill="#ffffff"></path>-->
<!--                                        </svg>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="ml-30">-->
<!--                                    <h6>Karthik V</h6>-->
<!--                                    <p>Salem</p>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->

<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </section>-->

<!--<section class="driver-section ">-->
<!--    <div class="container">-->
<!--        <div class="row d-flex justify-content-center align-items-center">-->

<!--            <div class="col-md-6 col-12 content-col">-->
<!--                <div data-aos="fade-up">-->
<!--                    <div class="tagline">Drive. Earn. Grow with GoRide.</div>-->
<!--                    <div class="section-title">Become a&nbsp;<span> GoRide</span> Partner!</div>-->

<!--                </div>-->

<!--                <div class="description" data-aos="fade-up" data-aos-delay="100">-->
<!--                    Join thousands of drivers earning steady income with flexible working hours. Whether you want to-->
<!--                    drive full-time or part-time, GoRide gives you the freedom to earn on your own schedule.-->
<!--                </div>-->

<!--                <div class="steps-section">-->
<!--                    <h2 class="section-title" data-aos="fade-up">Start <span> Driving</span> in 3 Easy Steps</h2>-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="100">-->
<!--                        <div class="step-number">1</div>-->
<!--                        <div class="step-content">Download the GoRide Driver App</div>-->
<!--                    </div>-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="200">-->
<!--                        <div class="step-number">2</div>-->
<!--                        <div class="step-content">Register & Upload Documents</div>-->
<!--                    </div>-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="300">-->
<!--                        <div class="step-number">3</div>-->
<!--                        <div class="step-content">Get Approved and Start Earning</div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="stats">-->
<!--                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="100">-->
<!--                        <span class="stat-number">10K+</span>-->
<!--                        <span class="stat-label">Active Drivers</span>-->
<!--                    </div>-->
<!--                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="200">-->
<!--                        <span class="stat-number">4.8★</span>-->
<!--                        <span class="stat-label">Driver Rating</span>-->
<!--                    </div>-->
<!--                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="300">-->
<!--                        <span class="stat-number">24/7</span>-->
<!--                        <span class="stat-label">Support</span>-->
<!--                    </div>-->
<!--                </div>-->

<!--            </div>-->

<!--            <div class="col-md-6 col-12 image-col">-->
<!--                <div class="image-container">-->

<!--                    <img src="{{ asset('goride/img/banner-1-mob.webp') }}" class="driver-image"  alt="driver-image "data-aos="zoom-in">-->
<!--                    <div class="cta-section" data-aos="zoom-in" data-aos-delay="400">-->
<!--                        <div class="cta-text">Join today and turn every drive into an earning opportunity.</div>-->
<!--                        <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
<!--                            class="text-decoration-none">-->
<!--                            <button class="cta-button">-->
<!--                                <svg class="kOqhQd" aria-hidden="true" viewBox="0 0 40 40"-->
<!--                                    xmlns="http://www.w3.org/2000/svg" style=" height: 20px;margin: 4px;">-->
<!--                                    <path fill="none" d="M0,0h40v40H0V0z"></path>-->
<!--                                    <g>-->
<!--                                        <path-->
<!--                                            d="M19.7,19.2L4.3,35.3c0,0,0,0,0,0c0.5,1.7,2.1,3,4,3c0.8,0,1.5-0.2,2.1-0.6l0,0l17.4-9.9L19.7,19.2z"-->
<!--                                            fill="#EA4335"></path>-->
<!--                                        <path-->
<!--                                            d="M35.3,16.4L35.3,16.4l-7.5-4.3l-8.4,7.4l8.5,8.3l7.5-4.2c1.3-0.7,2.2-2.1,2.2-3.6C37.5,18.5,36.6,17.1,35.3,16.4z"-->
<!--                                            fill="#FBBC04"></path>-->
<!--                                        <path-->
<!--                                            d="M4.3,4.7C4.2,5,4.2,5.4,4.2,5.8v28.5c0,0.4,0,0.7,0.1,1.1l16-15.7L4.3,4.7z"-->
<!--                                            fill="#4285F4"></path>-->
<!--                                        <path-->
<!--                                            d="M19.8,20l8-7.9L10.5,2.3C9.9,1.9,9.1,1.7,8.3,1.7c-1.9,0-3.6,1.3-4,3c0,0,0,0,0,0L19.8,20z"-->
<!--                                            fill="#34A853"></path>-->
<!--                                    </g>-->
<!--                                </svg>Download App-->
<!--                            </button>-->
<!--                        </a>-->

<!--                    </div>-->

<!--                </div>-->
<!--            </div>-->

<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<section class="goride-routes d-none">
  <div class="container">
    <div class="tagline">
      Top Locations Under <span class="text-dark">GoRide's </span> Service
      Network
    </div>
    <div class="section-title mb-3">
      Most Popular <span>Travel Routes</span> among Our Cities
    </div>
    <div class="row accordion" id="routesAccordion">
      @foreach($seoTags['innerLinks'] as $kj => $value)

      <div class="col-md-6 col-12 mb-3">
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading{{ $iii }}">
            <button
              class="accordion-button collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse{{ $iii }}"
              aria-expanded="false"
              aria-controls="collapse{{ $iii }}"
            >
              <span class="count"><i class="fa fa-route"></i></span>
              {{ ucwords(strtolower($kj)) }} Call Taxis / Cab / Pick Up and Drop
              Taxi
            </button>
          </h2>

          <div
            id="collapse{{ $iii }}"
            class="accordion-collapse collapse"
            aria-labelledby="heading{{ $iii }}"
            data-bs-parent="#routesAccordion"
          >
            <div class="accordion-body routes-list">
              <ul>
                @foreach($value as $val) @php $words = [ 'Taxi', 'One Way Taxi',
                'Round Trip Taxi', 'Taxi Cab', 'Cab', 'Round Trip Cab', 'One Way
                Cab' ]; $random = $words[array_rand($words)]; @endphp

                <li>
                  <a href="/{{ $val['slug'] }}" target="_blank">
                    <i class="fa-solid fa-location-dot route-icon"></i>
                    {{ ucwords(strtolower($val['name'])) }} to {{
                    ucwords(strtolower($val['to_place'])) }} {{ $random }}
                  </a>
                </li>

                @endforeach
              </ul>
            </div>
          </div>
        </div>
      </div>

      @php $iii++; @endphp @endforeach
    </div>
  </div>
</section>

<!--<section class="upgrade">-->
<!--    <div class="container">-->
<!--        <div class="row justify-content-center">-->
<!--            <div class="col-12">-->
<!--                <div class="cs_cta cs_style_1 text-center position-relative p-0" data-aos="fade-up"-->
<!--                    data-aos-duration="1000">-->
<!--                    <div class="section-title text-center">-->
<!--                        Payment&nbsp;<span> Options </span> Made Easy-->
<!--                    </div>-->
<!--                    <p class="cs_section_subtitle mb-4">-->
<!--                        Pay Your Way – Simple, Secure, and Flexible-->
<!--                    </p>-->

<!--                    <div class="payment-section">-->
<!--                        <div class="row justify-content-center gy-4">-->

<!--                            <div class="col-12 col-md-6 col-lg-4">-->
<!--                                <div class="payment-card h-100">-->
<!--                                    <div class="payment-img">-->
<!--                                        <img src="{{ asset('goride/img/postpaid.png') }}" alt="Online Payment">-->
<!--                                    </div>-->
<!--                                    <h3>Online Payment</h3>-->
<!--                                    <ul>-->
<!--                                        <li>Pay via <strong>UPI, Credit/Debit Cards</strong>, or-->
<!--                                            <strong>Wallets</strong></li>-->
<!--                                        <li>Quick and contactless – no need for cash</li>-->
<!--                                        <li>Instant payment confirmation</li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->

<!--                            <div class="col-12 col-md-6 col-lg-4">-->
<!--                                <div class="payment-card h-100">-->
<!--                                    <div class="payment-img">-->
<!--                                        <img src="{{ asset('goride/img/cash.png') }}" alt="Cash Payment">-->
<!--                                    </div>-->
<!--                                    <h3>Cash Payment</h3>-->
<!--                                    <ul>-->
<!--                                        <li>Pay your driver <strong>directly</strong> in cash</li>-->
<!--                                        <li>Convenient for traditional payment users</li>-->
<!--                                        <li>Safe and hassle-free</li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->

<!--                            <div class="col-12 col-md-6 col-lg-4">-->
<!--                                <div class="payment-card h-100">-->
<!--                                    <div class="payment-img">-->
<!--                                        <img src="{{ asset('goride/img/online.png') }}" alt="Postpaid Payment">-->
<!--                                    </div>-->
<!--                                    <h3>GoRide Postpaid+</h3>-->
<!--                                    <ul>-->
<!--                                        <li>Buy now, pay later at the <strong>end of the month</strong></li>-->
<!--                                        <li>Flexible credit limit up to <strong>₹1,00,000</strong></li>-->
<!--                                        <li>Accepted across all GoRide rides</li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->

<!--                        </div>-->
<!--                    </div>-->

<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->

<!--<section class="driver-section ">-->
<!--    <div class="container">-->
<!--        <div class="row d-flex justify-content-center align-items-center">-->

<!--             <div class="col-md-6 col-12 image-col">-->

<!--    <div class="owl-carousel agency-carousel">-->

<!--        <div class="item">-->
<!--            <img src="{{ asset('goride/img/ag1.webp') }}"-->
<!--                 class="driver-image"-->
<!--                 alt="driver-image"-->
<!--                 data-aos="zoom-in">-->
<!--        </div>-->

<!--        <div class="item">-->
<!--            <img src="{{ asset('goride/img/ag2.webp') }}"-->
<!--                 class="driver-image"-->
<!--                 alt="driver-image">-->
<!--        </div>-->

<!--    </div>-->

<!--</div>-->

<!--            <div class="col-md-6 col-12 content-col">-->
<!--                <div data-aos="fade-up">-->
<!--                    <div class="tagline">Manage Smarter.Earn Better.Grow with GoRide Agency</div>-->
<!--                    <div class="section-title">Become a&nbsp;<span> GoRide</span> Agency!</div>-->

<!--                </div>-->

<!--                <div class="description" data-aos="fade-up" data-aos-delay="100">-->
<!--                   Minimum Requirements-->
<!--                </div>-->

<!--                <div class="steps-section">-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="100">-->
<!--                        <div class="step-number">1</div>-->
<!--                        <div class="step-content">100Sqft Commercial Space</div>-->
<!--                    </div>-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="200">-->
<!--                        <div class="step-number">2</div>-->
<!--                        <div class="step-content">Computer, Mobile, Internet, CCTV</div>-->
<!--                    </div>-->

<!--                    <div class="step" data-aos="fade-right" data-aos-delay="300">-->
<!--                        <div class="step-number">3</div>-->
<!--                        <div class="step-content">Minimum Educational Qualifications</div>-->
<!--                    </div>-->
<!--                </div>-->

<!--            </div>-->

<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<!--<section class="agency-section">-->
<!--     <div class="-->
<!--     text-center">-->
<!--    <div class="container-fluid">-->
<!--        <div class="agency-btn-wraap" ><div class="section-title text-center -->
<!--        ">-->
<!--                       Manage Smarter & Earn Better with &nbsp;<span> GoRide </span> Agency-->
<!--                    </div>-->
<!--      <a href="/agency" class="btn-agent-super">-->
<!--     Become an Agency<i class="fas fa-hand-point-up ms-2"></i>-->
<!--</a>-->

<!--    </div>-->
<!--</div>-->
<!--</div>-->
<!--</section>-->

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <div class="row w-100 align-items-center">
          <!-- Left: Back Button -->
          <div class="col-6 text-start">
            <button class="back-btn">
              <span class="material-icons back-icon">arrow_back</span>
              Back
            </button>
          </div>

          <!-- Right: Close Button -->
          <div class="col-6 text-end">
            <button type="button" class="close1" data-bs-dismiss="modal">
              <i
                class="fa fa-close"
                style="
                  width: 30px;
                  height: 30px;
                  border-radius: 50%;
                  border: 1px solid #000;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              ></i>
            </button>
          </div>
        </div>
      </div>

      <div class="modal-body text-center mt-4">
        <img
          src="https://goride.run/goride/img/logo-dark.png"
          class="logo-img"
          alt=""
          style="width: 200px"
        />
        <form class="frm-sec">
          <div class="mb-3">
            <input
              type="email"
              class="form-control"
              id="exampleInputEmail1"
              aria-describedby="emailHelp"
              placeholder="Login with Mobile Number"
            />
          </div>
          <button
            type="submit"
            class="btn btn-primary mb-3"
            style="width: 100%"
          >
            Get OTP
          </button>
          <span class="by-click-text"
            >Already have an account?
            <a
              class="by-click-text under-line text-danger"
              href="login"
              contenteditable="false"
              style="cursor: pointer"
            >
              Sign In
            </a>
          </span>
        </form>
      </div>
      <div class="modal-footer">
        <!--         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
      </div>
    </div>
  </div>
</div>
<!-- Dynamic Inclusions Modal -->
<div class="modal fade" id="inclusionsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm custom-small-modal">
    <div class="modal-content small-modal-content">
      <div class="modal-header py-2">
        <h6 class="modal-title mb-0" id="inclusionsModalTitle">
          Inclusions & Exclusions
        </h6>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
        ></button>
      </div>

      <div class="modal-body small-modal-body" id="inclusionsModalBody">
        <!-- Dynamic content -->
      </div>
    </div>
  </div>
</div>
<!--<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />-->
<!--<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>-->

<!--<script src="https://maps.googleapis.com/maps/api/js?key={{ env('WEBSITE_GOOGLE_KEY') }}"></script>-->
<script
  src="https://maps.googleapis.com/maps/api/js?key={{ env('WEBSITE_GOOGLE_KEY') }}"
  async
  defer
></script>

<script src="https://www.google.com/recaptcha/api.js?render={{ env('BOOKING_RECAPTCHA_SITE_KEY') }}"></script>
<script>

  let osmMap, osmMarker;
  let activeInput = null;
  let selectedAddress = '';
  let selectedLatLng = null;

  function executeRecaptcha(action, callback) {
      grecaptcha.ready(function () {
          grecaptcha.execute('{{ env('BOOKING_RECAPTCHA_SITE_KEY') }}', { action: action })
              .then(function (token) {
                  callback(token);
              });
      });
  }

  function initPickupAddressAutocomplete(selector, contextLat, contextLng) {

      const $input = $(selector);

      $input.autocomplete({

          minLength: 3,

          source: function (request, response) {

              if (request.term.length > 20) {
                  response([]);
                  return;
              }

              response([
                  { label: 'Loading addresses...', value: '' }
              ]);

              executeRecaptcha('address_autocomplete', function (recaptchaToken) {

                  $.ajax({
                      url: "{{ env('APP_API') }}address-autocomplete",
                      type: "POST",
                      dataType: "json",
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      data: {
                          query: request.term,
                          lat: contextLat,
                          lng: contextLng,
                          recaptcha_token: recaptchaToken
                      },

                      success: function (res) {

                          if (!res.status || !res.data.length) {
                              response([
                                  { label: 'No addresses found', value: '' }
                              ]);
                              return;
                          }

                          response(res.data.map(item => ({
                              label: item.address,
                              value: item.address,
                              latitude: item.lat,
                              longitude: item.lng
                          })));
                      },

                      error: function () {
                          response([
                              { label: 'Error loading addresses', value: '' }
                          ]);
                      }
                  });

              });
          },

          select: function (event, ui) {

              if (!ui.item.latitude) {
                  event.preventDefault();
                  return false;
              }

              $input
                  .val(ui.item.value)
                  .data('lat', ui.item.latitude)
                  .data('lng', ui.item.longitude);
          }

      });
  }


  // function openMapPicker(inputId, lat, lng) {

  //     console.log(lat)
  //     console.log(lng)

  //     activeInput = inputId;
  //     $('#mapModal').removeClass('hidden');

  //     const centerLat = lat ?? 13.0827;   // fallback Chennai
  //     const centerLng = lng ?? 80.2707;

  //     setTimeout(() => initOrUpdateMap(centerLat, centerLng), 200);
  // }

  // function closeMapPicker() {
  //     $('#mapModal').addClass('hidden');
  // }

  // function initOrUpdateMap(lat, lng) {

  //     if (!osmMap) {

  //         osmMap = L.map('osm-map').setView([lat, lng], 14);

  //         L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  //             attribution: '&copy; OpenStreetMap'
  //         }).addTo(osmMap);

  //         osmMarker = L.marker([lat, lng], { draggable: true }).addTo(osmMap);

  //         osmMap.on('click', onMapClick);
  //         osmMarker.on('dragend', onMarkerDrag);

  //     } else {
  //         osmMap.setView([lat, lng], 14);
  //         osmMarker.setLatLng([lat, lng]);
  //     }

  //     reverseGeocode(lat, lng);
  // }

  // function onMapClick(e) {
  //     osmMarker.setLatLng(e.latlng);
  //     reverseGeocode(e.latlng.lat, e.latlng.lng);
  // }

  // function onMarkerDrag(e) {
  //     const pos = e.target.getLatLng();
  //     reverseGeocode(pos.lat, pos.lng);
  // }

  // const geocoder = new google.maps.Geocoder();

  // function reverseGeocode(lat, lng) {
  //     geocoder.geocode(
  //         { location: { lat, lng } },
  //         function (results, status) {
  //             if (status === 'OK' && results[0]) {
  //                 selectedAddress = data.display_name;
  //                 // callback(results[0].formatted_address);
  //             } else {
  //                 selectedAddress = '';
  //                 // callback(null);
  //             }
  //         }
  //     );
  // }


  function reverseGeocode(lat, lng) {

      selectedLatLng = { lat, lng };

      fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=en`)
          .then(res => res.json())
          .then(data => {
              if (data.display_name) {
                  selectedAddress = data.display_name;
              }
          });
  }

  // Google map

  function openPickupMap() {

      if (!navigator.geolocation) {
          alert('Location not supported');
          return;
      }

      navigator.geolocation.getCurrentPosition(
          function (position) {
              const lat = position.coords.latitude;
              const lng = position.coords.longitude;
              activeInput = 'pickup-address';

              $('#pickup')
              .data('lat', lat)
              .data('lng', lng);

              reverseGeocode(lat, lng);

              openMap(lat, lng, 'pickup');
          },
          function () {
              alert('Location permission denied');
          }
      );
  }

  // function openDropMap(lat, lng) {
  //     activeInput = 'drop-address';
  //     openMap(lat, lng, 'drop');
  // }

  function openDropMap() {

      var lat = $('#destination').data("latitude");
      var lng = $('#destination').data("longitude");

      // Convert strings to floats
      var numericLat = parseFloat(lat);
      var numericLng = parseFloat(lng);

      $('#drop')
          .data('lat', numericLat)
          .data('lng', numericLng);

      selectedAddress = $('#destination').val()

      activeInput = 'drop-address';
      openMap(numericLat, numericLng, 'drop');
  }


  let map, marker;

  function openMap(lat, lng, type) {

      $('#mapModal').removeClass('hidden');

      const center = { lat: lat, lng: lng };

      map = new google.maps.Map(document.getElementById("map"), {
          center: center,
          zoom: 15
      });

      marker = new google.maps.Marker({
          position: center,
          map: map,
          draggable: true
      });

      google.maps.event.addListener(marker, 'dragend', function () {
          const pos = marker.getPosition();
          saveLatLng(pos.lat(), pos.lng(), type);
      });
  }

  function closeMap() {
      $('#mapModal').addClass('hidden');
  }

  $('#mapModal').on('click', function (e) {
      if (e.target.id === 'mapModal') {
          closeMap();
      }
  });

  function saveLatLng(lat, lng, type) {

      if (type == 'pickup') {

          reverseGeocode(lat, lng);

          $('#pickup-address')
              .data('lat', lat)
              .data('lng', lng);
      } else {
          reverseGeocode(lat, lng);

          $('#drop-address')
              .data('lat', lat)
              .data('lng', lng);
      }
  }




  $(document).on("mousedown", function (e) {
      if (!$('#mapModal').hasClass("hidden")) {
          if ($(e.target).is("#mapModal")) {
              closeMapPicker();
          }
      }
  });

  $(document).on("keydown", function (e) {
      if (e.key === "Escape") {
          closeMapPicker();
      }
  });

  $('#pickup-address').on('focus', function () {

      let lat = $('#pickup-location').data("latitude");
      let lng = $('#pickup-location').data("longitude");

      if (!lat || !lng) {
          showToast("error", "Please select Pickup Location first", 3000);
          return;
      }

      // openMapPicker(
      //     'pickup-address',
      //     lat,
      //     lng
      // );

      initPickupAddressAutocomplete(
          '#pickup-address',
          lat,
          lng
      );

      // openPickupMap()
  });

  $('#drop-address').on('focus', function () {

      var lat = $('#destination').data("latitude");
      var lng = $('#destination').data("longitude");

      if (!lat || !lng) {
          showToast("error", "Please select Destination first", 3000);
          return;
      }

      // openMapPicker(
      //     'drop-address',
      //     lat,
      //     lng
      // );

      var numericLat = parseFloat(lat);
      var numericLng = parseFloat(lng);



      initPickupAddressAutocomplete(
          '#drop-address',
          numericLat,
          numericLng
      );


  });

  // $('#pickup-address').on('focus', function () {
  //     var pl = $('#pickup-location').select2('data')[0] ?? null;

  //     openMapPicker(
  //         'pickup-address',
  //         pl.latitude,
  //         pl.longitude
  //     );
  // });

  // $('#drop-address').on('focus', function () {
  //     var dl = $('#destination').select2('data')[0] ?? null;
  //     openMapPicker(
  //         'drop-address',
  //         dl.latitude,
  //         dl.longitude
  //     );
  // });

  $('#confirm-location').on('click', function () {

      if (!activeInput || !selectedAddress) return;

      $('#' + activeInput).val(selectedAddress);

      // Optional: store lat/lng on input
      $('#' + activeInput)
          .data('lat', selectedLatLng.lat)
          .data('lng', selectedLatLng.lng);

      $('#mapModal').addClass('hidden');
  });

  $(document).on('select2:open', () => {
      document.querySelector('.select2-container--open .select2-search__field')?.focus();
  });


  function shareWhatsapp() {

      const $btn = $('#share-whatsapp-btn');
      const $btnText = $btn.find('.btn-text');

      // values from booking API response (must already exist)
      if (!window.bookingData || !bookingData.job_no || !bookingData.mobile) {
          showToast('error', 'Booking data not available', 3000);
          return;
      }

      // prevent double click
      if ($btn.data('loading')) return;

      executeRecaptcha('share_whatsapp', function (recaptchaToken) {

          // before send UI
          $btn.data('loading', true);
          $btn.addClass('disabled');
          $btnText.text('Sharing...');

          $.ajax({
              url: "{{ env('APP_API') }}web-send-bookinfo",
              type: "POST",
              dataType: "json",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                  job_no: bookingData.job_no,
                  mob: bookingData.mobile,
                  recaptcha_token: recaptchaToken
              },

              success: function (res) {

                  if (res.status === 'success') {
                      showToast('success', res.message || 'Shared successfully', 3000);
                  } else {
                      showToast('error', res.message || 'Unable to share', 3000);
                  }
              },

              error: function () {
                  showToast('error', 'Something went wrong. Try again.', 3000);
              },

              complete: function () {
                  // restore UI
                  $btn.data('loading', false);
                  $btn.removeClass('disabled');
                  $btnText.text('Share on WhatsApp');
              }
          });

      });
  }




  $(document).ready(function () {

      // Safety Toggle
  $(document).on("click", "#toggle-safety", function () {
    $("#safety-content").slideToggle(200);

    const icon = $("#safety-chevron");
    icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
  });

  // ---------- Fare Breakup Toggle ----------
  // $(document).on("click", "#toggle-breakup", function () {
  //   $("#breakup-details").slideToggle(300);

  //   const icon = $("#breakup-chevron");
  //   if (icon.text() === "expand_more") {
  //     icon.text("expand_less");
  //   } else {
  //     icon.text("expand_more");
  //   }
  // });

  $('#pickup-date').on('change', generateTimeOptions);

  // ---------- Inclusions Toggle (like Fare Breakup) ----------
  $("#toggle-inclusions").click(function () {
    $("#inclusions-details").slideToggle(300);

    const icon = $("#inclusions-chevron");
    if (icon.text() === "expand_more") {
      icon.text("expand_less");
    } else {
      icon.text("expand_more");
    }
  });

    // ---------- Custom Time Picker ----------
  //   function generateTimeOptions(selectedDate) {

  //     const $dropdown = $("#time-dropdown");
  //     $dropdown.empty();

  //     const now = new Date();
  //     const todayStr = now.toISOString().split('T')[0];

  //     let currentMinutes = now.getHours() * 60 + now.getMinutes();

  //     for (let h = 0; h < 24; h++) {
  //         for (let m = 0; m < 60; m += 30) {

  //             let slotMinutes = h * 60 + m;

  //             // ⛔ Skip past times if selected date is today
  //             if (selectedDate === todayStr && slotMinutes <= currentMinutes) {
  //                 continue;
  //             }

  //             let hour12 = h % 12 || 12;
  //             let period = h < 12 ? "AM" : "PM";
  //             let min = m.toString().padStart(2, "0");
  //             let label = `${hour12}:${min} ${period}`;

  //             $dropdown.append(`
  //                 <div class="time-option" data-time="${label}">
  //                     ${label}
  //                 </div>
  //             `);
  //         }
  //     }

  //     // Edge case: no slots left today
  //     if (!$dropdown.children().length) {
  //         $dropdown.append(`<div class="time-option disabled">No slots available</div>`);
  //     }
  // }

  function hasSlotsToday() {
      const now = new Date();
      let bufferMinutes = now.getHours() * 60 + now.getMinutes() + 120;
      bufferMinutes = Math.ceil(bufferMinutes / 30) * 30;
      return bufferMinutes <= 1410;
  }

  function generateTimeOptions() {

      const $dropdown = $("#time-dropdown");
      $dropdown.empty();

      const selectedDate = $('#travel-date').val();
      if (!selectedDate) return;

      const now = new Date();

      const today =
          now.getFullYear() + '-' +
          String(now.getMonth() + 1).padStart(2, '0') + '-' +
          String(now.getDate()).padStart(2, '0');

      if (selectedDate === today && !hasSlotsToday()) {

          const tomorrow = new Date();
          tomorrow.setDate(tomorrow.getDate() + 1);

          travelPicker.setDate(tomorrow, true);
          return;
      }

      let currentMinutes = now.getHours() * 60 + now.getMinutes() + 120;
      currentMinutes = Math.ceil(currentMinutes / 30) * 30;

      for (let h = 0; h < 24; h++) {
          for (let m = 0; m < 60; m += 30) {

              const slotMinutes = h * 60 + m;

              if (selectedDate === today && slotMinutes < currentMinutes) {
                  continue;
              }

              const hour12 = h % 12 || 12;
              const period = h < 12 ? "AM" : "PM";
              const min = m.toString().padStart(2, "0");

              const label = `${hour12}:${min} ${period}`;

              $dropdown.append(`
                  <div class="time-option" data-time="${label}">
                      ${label}
                  </div>
              `);
          }
      }
  }

    $("#pickup-time").on("click", function () {
      $("#time-dropdown").toggle();
      generateTimeOptions()
    });

    $(document).on("click", ".time-option", function () {
      const time = $(this).data("time");
      $("#pickup-time").val(time);
      $("#time-dropdown").hide();
    });

    $(document).on("click", function (e) {
      if (!$(e.target).closest(".custom-time-picker").length) {
        $("#time-dropdown").hide();
      }
    });

    generateTimeOptions();

    // ---------- Datepicker ----------
  //   $("#travel-date").datepicker({
  //       dateFormat: "yy-mm-dd",
  //       minDate: 0,
  //       showAnim: "fadeIn",
  //       onSelect: function (selectedDate) {
  //         var travelDate = $(this).datepicker("getDate");
  //         travelDate.setDate(travelDate.getDate() + 1);

  //         $("#return-date").datepicker("option", "minDate", travelDate);
  //       }
  //     });

  //     $("#return-date").datepicker({
  //       dateFormat: "yy-mm-dd",
  //       minDate: 1,
  //       showAnim: "fadeIn"
  //     });

  let minDateValue = "today";

  if (!hasSlotsToday()) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      minDateValue = tomorrow;
  }

  const travelPicker = flatpickr("#travel-date", {
      dateFormat: "Y-m-d",
      minDate: minDateValue,
      defaultDate: minDateValue,

      onReady: function () {
          generateTimeOptions();
      },

      onChange: function () {
          generateTimeOptions();
      }
  });

  const returnPicker = flatpickr("#return-date", {
    dateFormat: "Y-m-d",
    minDate: minDateValue,
  defaultDate: minDateValue
  });

    // ---------- State ----------
    let currentModal = 1;
    let modalHistory = [];
    let selectedCab = null;
    let passengerCount = 1;
    let luggageCount = 0;
    let isRoundTrip = false;

  var cabs = [];

  function convert24hours(dateTimeStr) {
      const parts = dateTimeStr.trim().match(/(.*)\s(\d{1,2}):(\d{2})\s?(AM|PM)/i);

      if (!parts) return null;

      let datePart = parts[1];
      let hours = parseInt(parts[2], 10);
      let minutes = parts[3];
      let period = parts[4].toUpperCase();

      if (period === 'PM' && hours !== 12) {
          hours += 12;
      }

      if (period === 'AM' && hours === 12) {
          hours = 0;
      }

      hours = hours.toString().padStart(2, '0');

      return `${datePart} ${hours}:${minutes}:00`;
  }


  function showModal(modalNumber, isBack = false) {

    if (!isBack) {
      modalHistory.push(currentModal);
    }

    $(".modal-content").addClass("hidden");
    $("#modal-" + modalNumber).removeClass("hidden");
    currentModal = modalNumber;

    // ---------- Get All Values ----------
    const pickup = $("#pickup-location").val() || "";
    const destination = $("#destination").val() || "";
    const travelDate = $("#travel-date").val() || "";
    const returnDate = $("#return-date").val() || "";
    const pickupTime = $("#pickup-time").val() || "";
    // ---------- Pickup & Drop Address ----------
  const pickupAddress = $("#pickup-address").val() || "";
  const dropAddress = $("#drop-address").val() || "";

  let pk_date = '';
  let dp_date = '';

    // ---------- Build Full Date + Time Text ----------
    let dateTimeText = travelDate;

      let dateObj23 = new Date(travelDate);

      let dateTimeTextToDisplay = dateObj23.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });

    if (pickupTime) {
        pk_date = convert24hours(dateTimeText + ' ' + pickupTime);
      dateTimeTextToDisplay += " | " + pickupTime;
    }

    if (isRoundTrip && returnDate) {
        dp_date = returnDate;
      // dateTimeText += " → " + returnDate;
    }

    let fullRouteText = `${pickup} → ${destination} | ${dateTimeText}`;

  $("#preview-pickup").text(pickup);
  $("#from-location").text(pickup);
  $("#from-location-2").text(pickup);
  $(".from-location-3").text(
      pickup.length > 55 ? pickup.slice(0, 55) + "..." : pickup
  ).attr("title", pickup);
  $("#preview-destination").text(destination);
  $("#to-location").text(destination);
  $("#to-location-2").text(destination);
  $(".to-location-3").text(
      destination.length > 55 ? pickup.slice(0, 55) + "..." : destination
  ).attr("title", destination);
  // $(".to-location-3").text(destination);
  $("#preview-datetime").text(dateTimeTextToDisplay);
  $("#preview-triptype").text(isRoundTrip ? "Round Trip" : "One Way");
  $("#preview-pickup-address").text(pickupAddress || "Not provided");
  $("#preview-drop-address").text(dropAddress || "Not provided");

  $('.route-distance').text(
      cabs[0]?.distance ? `${cabs[0].distance} kms` : '0 kms'
  );

  $('.route-duration').text(
      cabs[0]?.day ? cabs[0].day : '0 hrs'
  );

  if (selectedCab) {
    const cabObj = cabs.find(c => c.id == selectedCab);
    if (cabObj) {
      // $("#preview-vehicle").text(cabObj.name);
      $('.selected-vehicle').text(cabObj.name);
    }
  }

  if (modalNumber >= 2) {
    $("#route-summary").show();

    // From / To
    $("#from-location").text(pickup);
    $("#to-location").text(destination);

    const date = new Date(travelDate);

  const formattedDate = date.toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  });

  $(".route-date").text(formattedDate);

    // Outbound Date
  //   $("#route-date").text(travelDate);

    // Time
    $(".route-time").text(pickupTime || "");

    // Trip Type + Return Date
    if (isRoundTrip) {
      $(".trip-type-icon").text("sync_alt");   // Round Trip
      $(".trip-type-text").text("Round Trip");

      // Show Return Date
      if (returnDate) {

          const dateReturn = new Date(returnDate);

          const formattedDateReturn = dateReturn.toLocaleDateString("en-GB", {
            day: "2-digit",
            month: "short",
            year: "numeric"
          });

        $(".route-return-date").text(formattedDateReturn).show();
      //   $(".route-return-wrapper").show();
      }

    } else {
      $(".trip-type-icon").text("trending_flat"); // One Way
      $(".trip-type-text").text("One Way");

      // Hide Return Date UI
      $(".route-return-wrapper").hide();
    }

    if(modalNumber == 4){
          let s_cab = cabs.find(c => c.id === selectedCab);

          journeyPayload = {
              job_type: getWayType(),
              from_place: $('#pickup-location').val(),
              to_place: $('#destination').val(),
              from_place_id: $('#pickup-location').data("place-id"),
              to_place_id: $('#destination').data("place-id"),
              pickup_date: pk_date,
              dropoff_date: dp_date,
              pass_count: $('#passenger-count-display').text(),
              lugg_count: $('#luggage-count-display').text(),
              fare: Number((s_cab.price || '0').replace('₹','').replace(/,/g,'')) || 0,
              distance: s_cab.distance,
              duration: s_cab.duration,
              day: s_cab.day,
              toll: s_cab.toll_fare,
              tax: s_cab.tax,
              cab_type: s_cab.cab_type,

              add_fare_details: {
                  bata: 'Excluded',
                  parking: 'Excluded',
                  toll: 'Excluded'
              },

              type: 'customer',
              c_name: $('#full-name').val(),
              c_email: $('#email').val(),
              c_mobile: $('#phone').val(),
              pick_address: $('#pickup-address').val(),
              drop_address: $('#drop-address').val(),
              pick_lat: $('#pickup-address').data("lat"),
              pick_lan: $('#pickup-address').data("lng"),
              drop_lat: $('#drop-address').data("lat"),
              drop_lan: $('#drop-address').data("lng"),
              isDriver: 'no',
          };

          populateBookingPreview(journeyPayload, selectedCab);
    }

  }



    // ---------- Confirmation Screen (Modal 6) ----------
    $("#confirm-pickup").text(pickup);
    $("#confirm-destination").text(destination);
    $("#confirm-datetime").text(dateTimeTextToDisplay);
    $("#confirm-pickup-address").text(pickupAddress || "Not provided");
  $("#confirm-drop-address").text(dropAddress || "Not provided");

  // ---------- Return Date in Confirmation ----------
  if (isRoundTrip && returnDate) {
    $("#confirm-return-date").text(returnDate);
    $("#confirm-return-row").show();
  } else {
    $("#confirm-return-row").hide();
  }

    if (selectedCab) {
      const cabObj = cabs.find(c => c.id == selectedCab);
      if (cabObj) {
        $("#confirm-vehicle").text(cabObj.name);
        $("#inclusion-content-toggle").attr("data-id", cabObj.id);
      }
    }

    // ---------- Load Cab List ----------
  //   if (modalNumber === 2) {
      // populateCabList();
  //   }
  }

    $(document).on("click", ".back-btn", function (e) {
      e.preventDefault();
      if (modalHistory.length > 0) {
        const prev = modalHistory.pop();
        showModal(prev, true);
      }
    });

      function populateCabList() {
    const cabList = $("#cab-list");
    cabList.empty();

    cabs.forEach(cab => {
      cabList.append(`
        <div class="cab-card" data-cab-id="${cab.id}">

          <div class="d-flex justify-content-evenly">
            <div class="cab-icon">
                <img
                  src="${cab.image}"
                  alt="${cab.name}"
                  class="cab-car-image"
                />
              </div>

            <div class="cab-details">
             <div class="d-flex justify-content-end"> <span class="cab-price-amount">${cab.price}</span></div>
            </div>
          </div>

          <div class="cab-price">
            <h3>${cab.name}</h3>
              <div class="cab-features">
                <span class="cab-feature">
                  <span class="material-icons">person</span>${cab.capacity}
                </span>
                <span class="cab-feature">
                  <span class="material-icons">luggage</span>${cab.luggage}
                </span>
              </div>
          </div>

          <div class="fare-breakdown hover-fare-box mt-2">

            <div class="breakup-header hover-inclusions-trigger" data-cab-id="${cab.id}">
              <span style="font-weight:600;">Inclusions & Exclusions</span>
            </div>

            <div class="breakup-details hover-inclusions-tooltip">

              <div class="inclusion-item inclusion-combined">

                <div class="inclusion-row">
                  <span class="material-icons primary-color">speed</span>
                  <div>
                    <p>157 kms included. Extra ₹12.6/km</p>
                  </div>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons primary-color">receipt_long</span>
                  <div>
                    <p>Tolls & Taxes</p>
                  </div>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons primary-color">person_pin</span>
                  <div>
                    <p>Driver allowance</p>
                  </div>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons primary-color">schedule</span>
                  <div>
                    <p>Waiting time</p>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      `);
    });

    var selectCar = '';

    // ================================
    // CAB CARD CLICK (GO NEXT)
    // ================================
    $(".cab-card").off("click").on("click", function (e) {

      if ($(e.target).closest(".fare-breakdown").length) {
        return;
      }

      selectedCab = $(this).data("cab-id");

      $(".cab-card").removeClass("selected");
      $(this).addClass("selected");

      showModal(3);
    });

    // ================================
    // SELECT CAB BUTTON (GO NEXT)
    // ================================
    $(".select-cab-btn").off("click").on("click", function (e) {
      e.stopPropagation();

      selectedCab = $(this).data("cab-id");

      $(".cab-card").removeClass("selected");
      $(`.cab-card[data-cab-id="${selectedCab}"]`).addClass("selected");

      showModal(3);
    });
  }

      // API Integration

      const cabImageMap = {
          mini_four_seater: "{{ asset('goride/img/outline12.webp') }}",
          four_seater: "{{ asset('goride/img/outline22.webp') }}",
          seven_seater: "{{ asset('goride/img/outline32.webp') }}",
          onethree_seater: "{{ asset('goride/img/outline42.webp') }}"
      };
      const cabNameMap = {
          mini_four_seater: "Go Mini",
          four_seater: "Go Sedan",
          seven_seater: "Go SUV",
          onethree_seater: "GO SUV+"
      };

      function getSelectedCabCapacity() {

          if (!selectedCab) return 1;

          // if selectedCab is an object
          if (typeof selectedCab === 'object') {
              return selectedCab.capacity ?? 1;
          }

          // if selectedCab is an ID
          var cab = cabs.find(c => c.id === selectedCab);
          return cab ? cab.capacity : 1;
      }



      function getWayType() {
          if ($('#round-trip-btn').hasClass('active')) {
              return 'roundtrip';
          }
          return 'oneway';
      }

      function validatePassengerCountOnCabChange() {

          const maxCapacity = getSelectedCabCapacity();

          if (passengerCount > maxCapacity) {
              passengerCount = maxCapacity;
              $('#passenger-count-display').text(passengerCount);
          }
      }

      function getSelectedCabLuggageLimit() {

          if (!selectedCab) return 0;

          // if selectedCab is an object
          if (typeof selectedCab === 'object') {
              return selectedCab.luggage ?? 0;
          }

          // if selectedCab is an ID
          var cab = cabs.find(c => c.id === selectedCab);
          return cab ? cab.luggage : 0;
      }


      function initPickupAutocomplete(selector) {

          const $input = $(selector);

          $input.autocomplete({

              minLength: 3,

              source: function (request, response) {

                  response([
                      { label: 'Loading locations...', value: '' }
                  ]);

                  executeRecaptcha('location_fetch', function (recaptchaToken) {

                      $.ajax({
                          url: "{{env('APP_API')}}web-getlocation",
                          type: "POST",
                          dataType: "json",
                          headers: {
                              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          },
                          data: {
                              search: request.term,
                              recaptcha_token: recaptchaToken
                          },

                          success: function (res) {

                              if (!res.status || !res.data.length) {
                                  response([
                                      { label: 'No locations found', value: '' }
                                  ]);
                                  return;
                              }

                              response(res.data.map(function (item) {
                                  return {
                                      id: item.place_id,
                                      label: item.name,
                                      value: item.name,
                                      latitude: item.latitude,
                                      longitude: item.longitude
                                  };
                              }));
                          },

                          error: function () {
                              response([
                                  { label: 'Error loading locations', value: '' }
                              ]);
                          }
                      });

                  });
              },

              select: function (event, ui) {

                  if (!ui.item.id) {
                      event.preventDefault();
                      return false;
                  }

                  $input.data("latitude", ui.item.latitude);
                  $input.data("longitude", ui.item.longitude);
                  $input.data("place-id", ui.item.id);

                  // console.log("Selected Location:", ui.item.value);
                  // console.log("Lat:", ui.item.latitude);
                  // console.log("Lng:", ui.item.longitude);
              }

          });
      }

      // function initLocationSelect(selector) {
      //     $(selector).select2({
      //         placeholder: "Type location...",
      //         minimumInputLength: 2,
      //         allowClear: true,
      //             ajax: {
      //             url: "{{env('APP_API')}}web-getlocation",
      //             type: "POST",
      //             dataType: "json",
      //             delay: 300,
      //             headers: {
      //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      //             },
      //             transport: function (params, success, failure) {

      //                 executeRecaptcha('location_fetch', function (recaptchaToken) {

      //                     params.data = params.data || {};
      //                     params.data.recaptcha_token = recaptchaToken;

      //                     const request = $.ajax(params);
      //                     request.then(success);
      //                     request.fail(failure);
      //                 });
      //             },
      //             data: function (params) {
      //                 return {
      //                     search: params.term
      //                 };
      //             },
      //             processResults: function (response) {
      //                 if (response.status == false) {
      //                     return { results: [] };
      //                 }

      //                 return {
      //                     results: response.data.map(function (item) {
      //                         return {
      //                             id: item.name,
      //                             text: item.name,
      //                             latitude: item.latitude,
      //                             longitude: item.longitude
      //                         };
      //                     })
      //                 };
      //             },
      //             cache: true
      //         }
      //     });
      // }

      function populateBookingPreview(payload, selectedCab) {

          var cab = cabs.find(c => c.id === selectedCab);

          $('#preview-pickup').text(payload.from_place.text ?? payload.from_place);
          $('#preview-destination').text(payload.to_place.text ?? payload.to_place);
          $('#preview-datetime').text(payload.pickup_date);
          $('#preview-triptype').text(formatTripType(payload.job_type));
          // $('#preview-vehicle').text(cab ? cab.name : '-');

          $('#preview-pickup-address').text(payload.pick_address || '--');
          $('#preview-drop-address').text(payload.drop_address || '--');

          /* Fare breakup */
          $('#breakup-details').html(`
              <div class="breakup-row">
                  <span class="breakup-label">Base Fare</span>
                  <span class="breakup-value">${formatCurrency(payload.fare)}</span>
              </div>
              <div class="breakup-row">
                  <span class="breakup-label">Govt. levy extra</span>
                  <span class="breakup-value">${formatCurrency(payload.toll)}</span>
              </div>
          `);

          // <div class="breakup-row">
          //     <span class="breakup-label">Taxes & Fees</span>
          //     <span class="breakup-value">${formatCurrency(payload.tax)}</span>
          // </div>
          // <div class="breakup-total">
          //     <span>Total</span>
          //     <span class="primary-color" style="font-size:16px;">
          //         ${formatCurrency(
          //             Number(payload.fare) + Number(payload.toll) + Number(payload.tax)
          //         )}
          //     </span>
          // </div>

          /* Total payable */
          $('.estimated_fare').text(
              formatCurrency(
                  Number(payload.fare) + Number(payload.toll)
              )
          );


      }

      function formatTripType(type) {
          return type == 'roundtrip' ? 'Round Trip' : 'One Way';
      }

      function formatCurrency(amount) {
          return '₹' + Number(amount).toLocaleString('en-IN');
      }

      initPickupAutocomplete('#pickup-location');
      initPickupAutocomplete('#destination');



      $('#get-quote-btn').on('click', function () {

          const $btn = $(this);

          // const pickup_2 = $('#pickup-location').select2('data')[0] ?? null;
          // const destination_2 = $('#destination').select2('data')[0] ?? null;


          const pickup_2 = $('#pickup-location').data("place-id").trim();
          const destination_2 = $('#destination').data("place-id").trim();

          const pk_d = $('#travel-date').val().trim();
          const pk_t = $('#pickup-time').val().trim();
          const rt_d = $('#return-date').val().trim();

          if (!pickup_2 || !destination_2) {
              showToast('error', 'Please select both Pickup and Destination', 3000);
              return;
          }

          if (pickup_2.toLowerCase() === destination_2.toLowerCase()) {
              showToast('error', 'Pickup and Destination cannot be the same', 3000);
              return;
          }

          if (!pk_d || !pk_t) {
              showToast('error', 'Please select both Pickup date and time', 3000);
              return;
          }

          const pickupDateTime = new Date(`${pk_d} ${pk_t}`);
          const now = new Date();

          if (pickupDateTime <= now) {
              showToast('error', 'Pickup date and time cannot be in the past', 3000);
              return;
          }

          if (getWayType() == 'roundtrip') {

              if (!rt_d) {
                  showToast('error', "Please select no. of days", 3000);
                  return;
              }

              // const pickupDate = new Date(pk_d);
              // const returnDate = rt_d;

              // if (returnDate.getTime() === pickupDate.getTime()) {
              //     showToast('error', 'Return date cannot be the same as Pickup date', 3000);
              //     return;
              // }


              // return must be after pickup
              // if (returnDate < pickupDate) {
              //     showToast('error', 'Return date must be after Pickup date', 3000);
              //     return;
              // }
          }


          let pk_date = '';
          pk_date = convert24hours(pk_d + ' ' + pk_t);

          executeRecaptcha('distance_fetch', function (recaptchaToken) {

              $.ajax({
              url: "{{env('APP_API')}}web-getdistance",
              type: "POST",
              dataType: "json",
              data: {
                  from_place_id: pickup_2,
                  to_place_id: destination_2,
                  pickup_date: pk_date,
                  dropoff_date: rt_d,
                  way_type: getWayType(),
                  recaptcha_token: recaptchaToken
              },
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },

              beforeSend: function () {
                  $btn
                      .prop('disabled', true)
                      .data('original-text', $btn.html())
                      .html('Your ride is on the way...');
              },

              success: function (response) {

                  if (!response || !response.data) {
                      return;
                  }

                  if(response.status != true){
                      showToast(`error`, response.message, 3000);
                      return;
                  }

                  var apiData = response.data;

                  var c_t = getWayType();

                  cabs = [];



                  Object.keys(cabImageMap).forEach(function (key, index) {

                      if (!apiData[key]) return;

                      var cab = apiData[key];

                      $('#pickup-location').data("latitude", cab.from_lat);
                      $('#pickup-location').data("longitude", cab.from_lng);
                      $('#destination').data("latitude", cab.to_lat);
                      $('#destination').data("longitude", cab.to_lng);

                      cabs.push({
                          id: index + 1,
                          name: cabNameMap[key]
                              .replace(/_/g, ' ')
                              .replace(/\b\w/g, l => l.toUpperCase()),

                          image: cabImageMap[key],
                          capacity: key.includes('seven') ? 7 : key.includes('onethree') ? 9 : 4,
                          luggage: key.includes('mini') ? 2 : 3,
                          price: '₹' + cab.fare.toLocaleString('en-IN'),

                          distance: cab.distance,
                          duration: cab.duration,
                          inc_km: cab.inc_km,
                          day: cab.day,
                          tax: cab.tax,
                          cab_type: key,
                          toll_fare: parseInt(cab.toll_fare, 10),
                          per_km: cab.per_km
                      });
                  });

                  populateCabList();

                  showModal(2);
              },


              error: function (xhr) {
                  showToast(`error`, 'Something went wrong. Please try again.', 3000);
              },

              complete: function () {
                  $btn
                      .prop('disabled', false)
                      .html($btn.data('original-text'));
              }
          });

          });

      });

      $('#increase-passengers').on('click', function () {

          const maxCapacity = getSelectedCabCapacity();

          if (passengerCount >= maxCapacity) {
              // alert('Maximum ' + maxCapacity + ' passengers allowed for this cab');
              return;
          }

          passengerCount++;
          $('#passenger-count-display').text(passengerCount);
      });

      $('#decrease-passengers').on('click', function () {

          if (passengerCount <= 1) {
              return;
          }

          passengerCount--;
          $('#passenger-count-display').text(passengerCount);
      });

      $('#increase-luggage').on('click', function () {

          const maxLuggage = getSelectedCabLuggageLimit();

          if (luggageCount >= maxLuggage) {
              // alert('Maximum ' + maxLuggage + ' luggage allowed for this cab');
              return;
          }

          luggageCount++;
          $('#luggage-count-display').text(luggageCount);
      });

      $('#decrease-luggage').on('click', function () {

          if (luggageCount <= 0) {
              return;
          }

          luggageCount--;
          $('#luggage-count-display').text(luggageCount);
      });

      var journeyPayload = {};

      $("#next-to-payment-btn").click(function () {

          if (!$("#full-name").val()) {
              // alert("");
              showToast(`error`, 'Fill the passenger name', 3000);
              $("#full-name").focus();
              return;
          }
          if (!$("#email").val()) {
              // alert("");
              showToast(`error`, 'Fill the passenger email', 3000);
              $("#full-name").focus();
              return;
          }
          if (!$("#phone").val()) {
              // alert("");
              showToast(`error`, 'Fill the passenger mobile number', 3000);
              $("#full-name").focus();
              return;
          }

          if (!selectedCab) {
              // alert("");
              showToast(`error`, 'Select a cab', 3000);
              return;
          }
          showModal(4);
      });

      $("#proceed-to-payment-btn").on('click', function () {

          const $btn = $(this);
          
          $btn
            .prop('disabled', true)
            .data('original-text', $btn.html())
            .html('Processing...');

          if (!journeyPayload) {
              showToast(`error`, 'Booking data missing', 3000);
              $btn
                .prop('disabled', false)
                .html($btn.data('original-text'));
              return;
          }

          executeRecaptcha('booking_create', function (recaptchaToken) {

              journeyPayload.recaptcha_token = recaptchaToken;

              $.ajax({
              url: "{{env('APP_API')}}web-book-journey",
              type: "POST",
              data: journeyPayload,
              // contentType: "application/json",
              dataType: "json",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },

              beforeSend: function () {
                  $btn
                      .prop('disabled', true)
                      .data('original-text', $btn.html())
                      .html('Processing...');
              },

              success: function (response) {
                  // console.log('Booking Created:', response);
                  // skip payment screen (as requested)
                  if(response.status == true){
                      showToast(`success`, response.message, 3000);
                      window.bookingData = {
                          job_no: response.data,
                          mobile: journeyPayload.c_mobile
                      };
                      $('#b-pre-link')
                      .attr('href', '/booking-information/' + response.preview)
                      .attr('target', '_blank');
                      showModal(6);
                      $('#job_id').text('#'+ response.data)
                  }else{
                      showToast(`error`, response.message, 3000);
                  }


              },

              error: function (xhr) {
                  // console.error(xhr.responseText);
                  // alert('');
                  // if(response.status == 'true'){
                      // showToast(`success`, response.message, 3000);
                  // }else{
                      showToast(`error`, xhr.responseText, 3000);
                  // }
              },

              complete: function () {
                  $btn
                    // .prop('disabled', false)
                    .html($btn.data('original-text'));
              }
          });

          });

      });

  // ================================
  // INCLUSIONS TOGGLE (ONLY TOGGLE)
  // ================================
  function isMobile() {
    return window.matchMedia("(hover: none)").matches;
  }

    $("#one-way-btn").click(function () {
      isRoundTrip = false;
      $("#one-way-btn").addClass("active");
      $("#round-trip-btn").removeClass("active");
      $("#return-date-container").slideUp();

      $('#return-date').val('')
    });

    $("#round-trip-btn").click(function () {
      isRoundTrip = true;
      $("#round-trip-btn").addClass("active");
      $("#one-way-btn").removeClass("active");
      $("#return-date-container").slideDown();
    });

      $("#complete-payment-btn").click(function () {
          showModal(6);
      });

      $(document).on("click", ".hover-inclusions-trigger", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $cabCard = $(this).closest(".cab-card");
        const cabId = $cabCard.data("cab-id");

        selectedCab = cabId;
        $(".cab-card").removeClass("selected");
        $cabCard.addClass("selected");

        let s_cab = cabs.find(c => c.id === selectedCab);

        // $("#cab-list").hide();
        $("#inclusions-view").show();

        const cabName = $cabCard.find("h3").text();
        $("#inclusions-view-title").text(`${cabName} - Inclusions & Exclusions`);

          const inclusionsHtml = `
            <div class="inclusions-grid">

              <div class="inclusions-col">
                <h5 class="section-title text-warning">
                  <span class="material-icons">check_circle</span>
                  Inclusions
                </h5>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">speed</span>
                  <p id="inc_km">${s_cab.inc_km} kms included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">person_pin</span>
                  <p>Driver allowance included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">restaurant</span>
                  <p>Driver food and accommodation (stay) charges included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">schedule</span>
                  <p>Waiting time up to 30 minutes for pickup included (₹100 per 60 minutes after 30 minutes)</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">map</span>
                  <p>Sightseeing included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">local_gas_station</span>
                  <p>Fuel charges included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">receipt_long</span>
                  <p>Govt. levy extra included (based on actual value)</p>
                </div>

                <div class="inclusion-row d-none">
                  <span class="material-icons text-warning">access_time</span>
                  <p>Return trips close by 9:00 PM</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">local_parking</span>
                  <p>Parking charges included</p>
                </div>

                <div class="inclusion-row d-none">
                  <span class="material-icons text-warning">account_balance</span>
                  <p>Taxes included</p>
                </div>
              </div>

              <div class="exclusions-col">
                <h5 class="section-title text-warning">
                  <span class="material-icons">cancel</span>
                  Exclusions
                </h5>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">location_city</span>
                  <p>State permit / entry charges</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">terrain</span>
                  <p>Hill station charges (extra)</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">toll</span>
                  <p>Govt. levy extra & parking charges</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">add_road</span>
                  <p>Additional kilometers: ₹${s_cab.per_km} per km</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">schedule</span>
                  <p>Additional hours / days: ₹100 per hour & ₹1200 per day</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">hourglass_bottom</span>
                  <p>Waiting: First 30 minutes free; additional waiting charges apply</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">receipt_long</span>
                  <p>Any government taxes or local charges, if applicable</p>
                </div>
              </div>

            </div>
          `;

        $("#inclusions-view-body").html(inclusionsHtml);
      });

      $(document).on("click", "#inclusion-content-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const cabId = $(this).data("id");

        selectedCab = cabId;
      //   $(".cab-card").removeClass("selected");
      //   $cabCard.addClass("selected");

        let s_cab = cabs.find(c => c.id === selectedCab);

      //   $("#cab-list").hide();
        $("#inclusions-view-2").show();

      //   const cabName = $cabCard.find("h3").text();
        const cabName = s_cab.name;
        $("#inclusions-view-title-2").text(`${cabName} - Inclusions & Exclusions`);

          const inclusionsHtml = `
            <div class="inclusions-grid">

              <div class="inclusions-col">
                <h5 class="section-title text-warning">
                  <span class="material-icons">check_circle</span>
                  Inclusions
                </h5>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">speed</span>
                  <p id="inc_km">${s_cab.inc_km} kms included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">person_pin</span>
                  <p>Driver allowance included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">restaurant</span>
                  <p>Driver food and accommodation (stay) charges included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">schedule</span>
                  <p>Waiting time up to 30 minutes for pickup included (₹100 per 60 minutes after 30 minutes)</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">map</span>
                  <p>Sightseeing included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">local_gas_station</span>
                  <p>Fuel charges included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">receipt_long</span>
                  <p>Govt. levy extra included (based on actual value)</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">access_time</span>
                  <p>Return trips close by 9:00 PM</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">local_parking</span>
                  <p>Parking charges included</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">account_balance</span>
                  <p>Taxes included</p>
                </div>
              </div>

              <div class="exclusions-col">
                <h5 class="section-title text-warning">
                  <span class="material-icons">cancel</span>
                  Exclusions
                </h5>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">location_city</span>
                  <p>State permit / entry charges</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">terrain</span>
                  <p>Hill station charges (extra)</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">toll</span>
                  <p>Govt. levy extra & parking charges</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">add_road</span>
                  <p>Additional kilometers: ₹${s_cab.per_km} per kms</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">schedule</span>
                  <p>Additional hours / days: ₹100 per hour & ₹1200 per day</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">hourglass_bottom</span>
                  <p>Waiting: First 30 minutes free; additional waiting charges apply</p>
                </div>

                <div class="inclusion-row">
                  <span class="material-icons text-warning">receipt_long</span>
                  <p>Any government taxes or local charges, if applicable</p>
                </div>
              </div>

            </div>
          `;

        $("#inclusions-view-body-2").html(inclusionsHtml);
      });

       $(document).on("click", "#close-inclusions-2", function (e) {
        e.preventDefault();
        e.stopPropagation();

        $("#inclusions-view-2").hide();
      //   $("#cab-list").show();
      });

      $(document).on("click", "#toggle-safety", function (e) {
        e.preventDefault();
        e.stopPropagation();

        $("#safety-view").show();
      });

      $(document).on("click", "#close-safety", function (e) {
        e.preventDefault();
        e.stopPropagation();

        $("#safety-view").hide();
      });

   $(document).on("click", "#back-to-cabs, #close-inclusions", function (e) {
    e.preventDefault();
    e.stopPropagation();

    $("#inclusions-view").hide();
    // $("#cab-list").show();
  });

      showModal(1);

      $(document).on("click", "#return-to-dashboard", function (e) {
    e.preventDefault();

    // Reset form fields
    $("#pickup-location").val("");
    $("#destination").val("");
    $("#travel-date").val("");
    $("#return-date").val("");
    $("#pickup-time").val("");

    $("#full-name").val("");
    $("#email").val("");
    $("#phone").val("");
    $("#pickup-address").val("");
    $("#drop-address").val("");

    // Reset counters
    passengerCount = 1;
    luggageCount = 0;
    $("#passenger-count-display").text("1");
    $("#luggage-count-display").text("0");

    // Reset trip type
    isRoundTrip = false;
    $("#one-way-btn").addClass("active");
    $("#round-trip-btn").removeClass("active");
    $("#return-date-container").hide();

    // Reset selected cab
    selectedCab = null;
    $(".cab-card").removeClass("selected");

    // Clear previews
    $("#preview-pickup, #preview-destination, #preview-datetime, #preview-triptype, #preview-vehicle").text("");
    $("#preview-pickup-address, #preview-drop-address").text("");

    $("#confirm-pickup, #confirm-destination, #confirm-datetime, #confirm-vehicle").text("");
    $("#confirm-pickup-address, #confirm-drop-address").text("");

    // Reset modal history
    modalHistory = [];

    // Hide inclusions view if open
    $("#inclusions-view").hide();
    // $("#cab-list").show();

    // Go back to first screen
    showModal(1);
  });

  });


  let countryC = getCookie('countryCode') || 'IN';
  let isMobile = window.innerWidth <= 768;

  if (countryC !== 'IN') {
      document.querySelector('#slider-image .item').style.backgroundImage = "url('/goride/img/slider/goride_main_banner.webp')";
      document.querySelector('#mobile_mockup').src = '/goride/img/slider/mobile_mockup_two.webp';
      document.querySelector('#india-content').style.display = "none";
      document.querySelector('#india-content2').style.display = "none";
  } else {
      document.querySelector('#slider-image .item').style.backgroundImage = "url('/goride/img/new-home-banner3.webp')";
      document.querySelector('#india-content').style.display = "block";
      document.querySelector('#india-content2').style.display = "block";
      document.querySelector('#mobile_mockup').src = '/goride/img/banner-1-mob.webp';
  }

  if (isMobile) {
  document.querySelector('#slider-image .item').style.backgroundImage = "url('/goride/img/slider/go_ride_background.png')";
  }

  triggerCalendly = () => {
      sessionStorage.setItem('triggerCalendlyClick', 'true');
      window.location.href = '/dashboard';
  }

  //     $(document).ready(function(){
  //   $('#myModal').modal('show');
  //     });
</script>

{{--
<div class="container">
  <h1>Welcome to Our Website</h1>
  <p>
    This is the home page of our Laravel application. Here you can add content
    that is specific to the home page.
  </p>

  <div class="row">
    <div class="col-md-6">
      <h2>About Us</h2>
      <p>Learn more about our mission, values, and team.</p>
    </div>
    <div class="col-md-6">
      <h2>Contact Us</h2>
      <p>Get in touch with us for any inquiries or support.</p>
    </div>
  </div>
</div>
--}} @endsection @section('script')

<script>
  $(document).ready(function () {
    notifyJobs();
  });

  document.addEventListener("DOMContentLoaded", function () {
    // Initialize carousel with auto rotation on mobile
    if (window.innerWidth < 768) {
      const myCarousel = document.getElementById("stepsCarousel");
      if (myCarousel) {
        const carousel = new bootstrap.Carousel(myCarousel, {
          interval: 4000, // Rotate every 4 seconds
          wrap: true,
          touch: true,
        });
      }
    }
  });
  function convertDateFormat(txt, type = "full") {
    let dateString = txt;

    // Create Date object (replace space with T so it's ISO compatible)
    let dateObj = new Date(dateString.replace(" ", "T"));

    // Extract day and month
    let day = String(dateObj.getDate()).padStart(2, "0");
    let month = dateObj.toLocaleString("en-US", { month: "short" });

    if (type === "date") {
      // Return only date format (e.g., "05 Sep")
      return `${day} ${month}`;
    }

    // Extract time components
    let hours = dateObj.getHours();
    let minutes = String(dateObj.getMinutes()).padStart(2, "0");
    let ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12; // Convert 24h to 12h format

    // Return full format (e.g., "05 Sep 02:15 PM")
    let formattedDate = `${day} ${month} ${String(hours).padStart(2, "0")}:${minutes} ${ampm}`;
    return formattedDate;
  }

  $(".agency-carousel").owlCarousel({
    items: 1,
    loop: true,
    margin: 10,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 4000,
    smartSpeed: 700,
    touchDrag: true,
    mouseDrag: true,
  });
  function notifyJobs() {
    if (true) {
      $.ajax({
        url: "{{ env('APP_API') }}notify-jobs",
        type: "POST",
        // headers: {
        //     "Authorization": "Bearer " + getCookie('sessionToken')
        // },
        data: [],
        contentType: false,
        processData: false,
        // beforeSend: function () {
        //     let btn = $("#con_create");
        //     btn.prop('disabled', true)
        //         .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
        // },
        success: function (response) {
          if (response.status) {
            let data = response.data.jobs ? response.data.jobs : [];
            $("#notify_jobs").empty();
            let jobs_content = "";
            if (data.length > 0) {
              $.each(data, function (index, value) {
                // let expiryTime = expiryCals(value.pickup_date);
                value.pickup_date = convertDateFormat(value.pickup_date);
                value.dropoff_date = value.dropoff_date
                  ? convertDateFormat(value.dropoff_date, "date")
                  : "";

                let j_type =
                  value.job_type == "oneway" ? "One Way" : "Round Trip";

                jobs_content += `<div class="notify-card blue aos-init aos-animate" >
                                    <img  src="https://www.goride.net.in/goride/img/bell.gif"  style="height: 42px; width: 42px"/>
                                    <div>
                                      <h6>${value.from_place} → ${value.to_place} <span class="badge ms-3">${j_type}</span></h6>
                                      <div class="d-flex gap-2">
                                        <p class="m-0 text-dark fw-bold">
                                          <strong class="text-danger fw-bold me-1">Pickup:</strong> ${value.pickup_date}
                                        </p>
                                        <p class="m-0 text-dark fw-bold ${value.dropoff_date ? "" : "d-none"}">
                                          <strong class="text-success fw-bold me-1">Return:</strong> ${value.dropoff_date}
                                        </p>
                                      </div>
                                    </div>
                                  </div>`;
              });

              jobs_content += `
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="{{ env('APP_URL') }}jobs" class="see-jobs-btn px-3">View Jobs</a>
                            </div>
                            
                        `;
            } else {
              jobs_content = `
                                        
                                        <div class="notify-card blue aos-init aos-animate d-flex justify-content-center" >
                                                <i class="fa-solid fa-briefcase text-danger"></i>
                                              <h6>No More Jobs</h6>
                                              
                                          </div>
                                    `;

              // hasMore = false;
            }
            $("#notify_jobs").html(jobs_content);
            // // // console.log(response.data);

            // page = response.data.next_page;
            // hasMore = !!response.data.next_page;
          } else {
            showToast("error", response.message, 3000);
          }
        },
        error: function () {
          showToast("error", "Something went wrong!", 3000);
        },
        // complete: function () {
        //     loading = false;
        //     $("#loader").hide();
        // }
      });
    }
  }
</script>

@endsection