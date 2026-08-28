{{-- booking-flow.blade.php --}}
@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />

<style>
/* ===================================================
   BOOKING FLOW  — Glass-dark gold theme (matches original)
=================================================== */
.bf-wrap {
    /*background: #060608;*/
    min-height: 100vh;
    padding: 30px 0 60px;
    font-family: 'Inter', sans-serif;
    color: #fff;
}
.bf-wrap *, .bf-wrap *::before, .bf-wrap *::after { box-sizing: border-box; }

.bf-inner {
    width:100%;
    max-width:1100px;
    margin:auto;
    padding:0 14px;
    margin-top: 100px;
}

/* Material Icons */
.material-icons {
    font-family: 'Material Symbols Rounded', 'Material Icons';
    font-weight: normal;
    font-style: normal;
    font-size: 14px;
    line-height: 1;
    letter-spacing: normal;
    text-transform: none;
    display: inline-block;
    white-space: nowrap;
    direction: ltr;
    -webkit-font-feature-settings: 'liga';
    -webkit-font-smoothing: antialiased;
}

/* ── Route pill (same as original) ──────────────────── */
.route-pill {
    /*background: rgba(255,255,255,0.06);*/
    background:#f5f0f047;
    border-radius: 20px;
    padding: 10px 16px;
    font-size: 13px;
    margin-bottom: 16px;
    display:flex;
       justify-content: center;
       box-shadow: 0 5px 8px rgba(0, 0, 0, .15), 0 8px 19px rgba(0, 0, 0, .25);
}
.route-line-1 {
    display: flex;
    flex-direction:column;
    /*align-items: flex-start;*/
    justify-content: center;
    gap: 10px;
    font-weight: 600;
    line-height: 1.4;
}
.route-text {
    flex: 1;
    min-width: 120px;
    word-break: break-word;
    color: black;
    font-size: 15px;
}
.route-arrow {font-size: 23px;
    margin: 0 4px;
    color: #2cb40a;
    align-self: center;
    transform: rotate(90deg);}
.route-line-2 {
  margin-top: 4px;
    /*display: flex;*/
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 14px;
    /*opacity: .8;*/
    flex-wrap: wrap;
    color: black;
    font-weight: 600;
}
.route-line-2 .material-icons { font-size: 19px; }

/* ── Glass card ──────────────────────────────────────── */
.glass-card {
    background: rgba(0,0,0,.85);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(249,191,0,.2);
    border-radius: 24px;
    overflow: hidden;
    width: 100%;
    margin-bottom: 20px;
}
.card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 22px;
    background: #060608;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.step-circle {
    width: 26px; height: 26px;
    background: #f9bf00; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800; color: #000;
    flex-shrink: 0;
}
.card-header h2 {
    font-size: 20px !important; font-weight: 700;
    margin: 0; color: #fff;
}
.card-header h2 span { color: #f9bf00; }
.card-body { padding: 16px 22px; background: #060608; }

/* ── Cab grid ────────────────────────────────────────── */
#cab-list {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 6px;
}
@media (max-width: 480px) {
    #cab-list { grid-template-columns: 1fr; }
}
.cab-card {
    background: rgba(255,255,255,.04);
    border: 2px solid rgba(255,255,255,.08);
    border-radius: 12px;
    padding: 10px;
    cursor: pointer;
    transition: all .25s;
}
.cab-card:hover, .cab-card.selected {
    border-color: rgba(249,191,0,.75);
    background: rgba(249,191,0,.04);
}
.cab-car-image { width: 100%; height: auto; object-fit: contain; }
.cab-details { color: grey; font-weight: 700; font-size: 13px; }
.cab-details h3 { color: #f9bf00; font-weight: 700; font-size: 12px; margin: 0 0 2px; }
.cab-price-amount {
    color: #fff; font-weight: 800; font-size: 14px;
}
.cab-features {
    display: flex; gap: 10px;
    color: rgba(255,255,255,.85); font-size: 15px;
    justify-content: flex-end; margin-top: 4px;
}
.cab-feature { display: flex; align-items: center; gap: 3px; }
.breakup-header {
    display: flex; align-items: center; justify-content: center;
    background: #f9bf00; height: 20px; width: 20px;
    border-radius: 50%; color: #000; font-weight: 700; font-size: 11px;
    cursor: pointer;
}
.book-now-btn {
    background: #f9bf00; color: #000; border: none;
    border-radius: 8px; padding: 5px 15px;
    font-size: 11px; font-weight: 700; cursor: pointer;
    text-transform: uppercase; 
    /*width: 100%; */
    margin-top: 8px;
    font-family: 'Inter', sans-serif;
    transition: background .2s;
}
.book-now-btn:hover { background: #e6af00; }

/* ── Inclusions panel ────────────────────────────────── */
#inclusions-view {
    background: #fdfdf4;
    border-radius: 12px;
    padding: 14px;
    margin-top: 12px;
    position: relative;
}
#inclusions-view h4, #inclusions-view-2 h4 {
    font-size: 17px; color: #000; text-align: center; margin: 0;
}
.inclusions-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 10px;
}
@media (max-width: 480px) { .inclusions-grid { grid-template-columns: 1fr; } }
.inclusions-col .section-title, .exclusions-col .section-title {
    display: flex; align-items: center; gap: 5px;
    font-size: 14px !important; font-weight: 800 !important;
    margin: 0 0 6px !important; text-transform: uppercase !important;
    letter-spacing: 1px !important; color: #000;
}
.inclusions-col .section-title span, .exclusions-col .section-title span { font-size: 16px !important; }
.inclusion-row {
    display: flex; align-items: flex-start; gap: 5px;
    padding: 4px 0; border-bottom: 1px dashed rgba(0,0,0,.1);
    font-size: 11px;
}
.inclusion-row:last-child { border-bottom: none; }
.inclusion-row p { margin: 0; line-height: 1.4; font-size: 14px !important; color: #000; font-weight: 500; }
.inclusion-row .material-icons { font-size: 18px; min-width: 26px; }
.inclusions-back-btn {
    height: 28px; border-radius: 50%; width: 28px;
    display: flex; justify-content: center; align-items: center;
    background: rgba(0,0,0,.1); border: none; cursor: pointer; color: #333;
}
.inclusions-back-btn:hover{
    color:black !important;
}

/* ── Form inputs ─────────────────────────────────────── */
.form-group { margin-bottom: 10px; position: relative; width: 100%; }
.bf-wrap label {
    display: flex; align-items: center; gap: 6px;
    font-size: 14px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .12em;
    color: #f9bf00; margin-bottom: 4px;
}
.glass-input {
background: rgba(255,255,255,.08) !important;
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 12px; color: #fff;
    font-size: 14px; padding: 8px 14px;
    transition: all .3s; width: 100%;
    font-family: 'Inter', sans-serif;
}
.glass-input:focus {
    background: rgba(255,255,255,.12);
    border-color: #f9bf00; outline: none;
    box-shadow: 0 0 12px rgba(249,191,0,.2);
}
.glass-input::placeholder { color: rgba(255,255,255,.45); }

/* passenger selector */
.passenger-selector {
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px; padding: 5px;
}
.selector-btn {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(249,191,0,.3); color: #f9bf00;
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-family: 'Inter', sans-serif;
    font-size: 16px; transition: background .2s;
}
.selector-btn:hover { background: rgba(249,191,0,.2); }
.count-display { color: #fff; font-weight: 700; font-size: 15px; min-width: 30px; text-align: center; }

/* ── Safety / Inclusions toggle box ─────────────────── */
.safety-box {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(249,191,0,.25);
    border-radius: 10px; padding: 8px 12px;
    display: flex; justify-content: space-between;
    margin-bottom: 14px; flex-wrap: wrap; gap: 6px;
}
.safety-header {
    display: flex; align-items: center; gap: 7px;
    cursor: pointer; font-size: 15px; font-weight: 600; color: #f9bf00;
}

/* fare rows */
.breakup-row {
    display: flex; justify-content: space-between;
    font-size: 15px; padding: 4px 0;
}
.breakup-label { color: #ffc404; }
.breakup-value { font-weight: 500; }

/* ── Glow button ─────────────────────────────────────── */
.glow-button {
    background-color: #f9bf00; color: #000;
    border: none; border-radius: 12px;
    font-weight: 700; font-size: 12px; padding: 10px;
    cursor: pointer; transition: all .3s;
    box-shadow: 0 4px 18px rgba(249,191,0,.3);
    display: flex; align-items: center; justify-content: center;
    gap: 7px; 
    /*width: 100%;*/
    text-transform: uppercase;
    font-family: 'Inter', sans-serif;
}
.glow-button:hover {
    background-color: #e6af00;
    box-shadow: 0 4px 28px rgba(249,191,0,.5);
    transform: translateY(-2px);
}
.glow-button:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* search icon spin */
.search-icon {
    font-size: 16px !important; color: #000 !important;
    animation: searching 1.5s linear infinite; transform-origin: center;
}
@keyframes searching {
    0%   { transform: rotate(0deg)   scale(1);   }
    25%  { transform: rotate(-15deg) scale(1.1); }
    50%  { transform: rotate(0deg)   scale(1.2); }
    75%  { transform: rotate(15deg)  scale(1.1); }
    100% { transform: rotate(0deg)   scale(1);   }
}

/* ── Trip mini card (confirmation) ──────────────────── */
.trip-mini-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px; padding: 12px 14px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px;
    margin-bottom: 14px;
}
@media (max-width: 480px) { .trip-mini-card { grid-template-columns: 1fr; } }
.trip-mini-row {
    display: flex; gap: 7px; padding: 3px 0; align-items: flex-start;
}
.mini-icon { font-size: 19px !important; color: rgba(249,191,0,.85); flex-shrink: 0; }
.mini-text { display: flex; flex-direction: column; gap: 8px; }
.mini-text small {
       font-size: 14px;
    color: rgba(255, 255, 255, .4);
    text-transform: uppercase;
    /* letter-spacing: .08em; */
    line-height: 1;
}
.mini-text p { font-size: 15px !important;
    margin: 0;
    font-weight: 500;
    color: #fff;
    line-height: 1.3;
    word-break: break-word;}
.vehicle-highlight { color: #f9bf00; font-weight: 700; }

/* booking id boxes */
.booking-id-box {
    background: rgba(255,255,255,.05);
    border: 1px dashed rgba(249,191,0,.3);
    border-radius: 8px; padding: 2px 12px;
    text-align: center; font-size: 12px;
}
.booking-id-box p{
    font-size:18px;
}
.booking-id-preview {
    background: rgb(248,190,0);
    border-radius: 8px; padding: 5px 14px;
    cursor: pointer; margin: 4px;
}
.booking-id-preview a {
        color: #000;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    text-transform: uppercase;
}
.confirm-icon-circle {
    width: 48px; height: 48px;
    background: linear-gradient(135deg,rgba(249,191,0,.25),rgba(249,191,0,.05));
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    margin: 0 auto 6px;
    border: 1px solid rgba(249,191,0,.35);
    box-shadow: 0 0 16px rgba(249,191,0,.25);
}
.confirm-icon { font-size: 28px !important; }

/* ── Safety overlay ──────────────────────────────────── */
#safety-view {
    position: absolute; bottom: 0; left: 0; width: 100%;
    padding: 20px; background: #fff; border-radius: 0 0 24px 24px; z-index: 99;
}
#inclusions-view-2 {
    background: #fdfdf4; padding: 14px;
    position: absolute; left: 0; bottom: 0; z-index: 99;
    width: 100%; border-radius: 0 0 24px 24px;
    overflow: auto;
}
.safety-view li {
    margin-bottom: 4px; line-height: 1.4; font-weight: 500;
    font-size: 14px !important; color: #111;
    font-family: 'Inter', sans-serif;
}

/* ── Back button ─────────────────────────────────────── */
.back-btn {
    background: transparent; border: none;
    color: #f9bf00; font-size: 11px; font-weight: 700;
    cursor: pointer; padding: 0; margin: 2px 0 8px;
    display: inline-flex; align-items: center; gap: 5px;
    text-transform: uppercase; letter-spacing: .05em;
    font-family: 'Inter', sans-serif;
}
.back-icon { font-size: 13px !important; }

/* ── Estimated fare row ──────────────────────────────── */
.fare-total-row {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 12px; padding: 0 6px;
}
.fare-total-row .lbl {
    font-size: 15px; color: rgba(255,255,255,.5);
    text-transform: uppercase; letter-spacing: .1em; font-weight: 700;
}
.fare-total-row .amt {
    font-weight: 800; font-size: 22px; color: #fff;
}

/* primary-color helper */
.primary-color { color: #f9bf00; }
.text-warning  { color: #f9bf00 !important; font-size:15px;}
.hidden { display: none !important; }

/* scrollbar */
.bf-wrap ::-webkit-scrollbar { width: 3px; }
.bf-wrap ::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg,#f9bf00,#e6af00);
    border-radius: 10px;
}
.trip-summary-sticky{
     position: sticky;
    top: 129px;
}
@media (max-width: 767px) {
    .route-line-1{
        flex-direction:column;
    }
    .safety-box { flex-direction: column; }
        #inclusions-view-2 {
        height: 70vh;
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 15px;
    }

    #inclusions-view-2::-webkit-scrollbar {
        width: 4px;
    }

    #inclusions-view-2::-webkit-scrollbar-thumb {
        background: #f9bf00;
        border-radius: 10px;
    }
    .route-text{
        font-size:13px;
    }
    .bf-wrap label {
        font-size:13px;
    }
    .fare-total-row .lbl  {
        font-size:13px;
    }
    .fare-total-row .amt {
        font-size:18px;
    }
    #inclusions-view-2{
    position: fixed !important;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 75vh;
    background: #fdfdf4;
    z-index: 999999;
    overflow-y: auto !important;
    overflow-x: hidden;
    touch-action: pan-y;
    -webkit-overflow-scrolling: touch;
    border-radius: 20px 20px 0 0;
}
/*.glass-card{*/
/*    overflow:visible;*/
/*}*/

}
</style>
<section>
    <div class="container">
<div class="bf-wrap">
    <div class="row">
      <div class="col-md-9"><div class="bf-inner">

  <!-- ════════════════ STEP 2 : SELECT CAB ════════════════ -->
  <div class="glass-card" id="card-2">
    <div class="card-header">
      <div class="step-circle">2</div>
      <h2>Select Your <span>Cab</span></h2>
    </div>
    <div class="card-body">

      <!-- Route pill -->


      <div id="cab-list">
        <!-- populated by JS -->
      </div>

      <!-- Inclusions panel (per-cab) -->
      <div id="inclusions-view" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h4 id="inclusions-view-title">Inclusions &amp; Exclusions</h4>
          <button id="close-inclusions" class="inclusions-back-btn">
            <span class="material-icons">close</span>
          </button>
        </div>
        <div id="inclusions-view-body"></div>
      </div>

    </div>
  </div>

  <!-- ════════════════ STEP 3 : PASSENGER DETAILS ════════════════ -->
  <div class="glass-card" id="card-3">
    <div class="card-header">
      <div class="step-circle">3</div>
      <h2>Passenger <span>Details</span></h2>
    </div>
    <div class="card-body">

      <div class="row mt-2">
        <div class="col-md-3">
          <div class="form-group">
            <label><span class="material-icons">person</span> Full Name</label>
            <input type="text" id="full-name" class="glass-input" placeholder="Enter your full name"
              oninput="this.value=this.value.replace(/[^0-9a-zA-Z ]/g,'').slice(0,30)"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label><span class="material-icons">call</span> Mobile Number</label>
            <input type="tel" id="phone" class="glass-input" placeholder="9876543210"
              oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"/>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label><span class="material-icons">group</span> Passengers</label>
            <div class="passenger-selector">
              <button id="decrease-passengers" class="selector-btn">
                <span class="material-icons">remove</span>
              </button>
              <span id="passenger-count-display" class="count-display">1</span>
              <button id="increase-passengers" class="selector-btn">
                <span class="material-icons">add</span>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label><span class="material-icons">shopping_bag</span> Luggage</label>
            <div class="passenger-selector">
              <button id="decrease-luggage" class="selector-btn">
                <span class="material-icons">remove</span>
              </button>
              <span id="luggage-count-display" class="count-display">0</span>
              <button id="increase-luggage" class="selector-btn">
                <span class="material-icons">add</span>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- ════════════════ STEP 4 : BOOKING SUMMARY ════════════════ -->
  <div class="glass-card position-relative" id="card-4">
    <div class="card-header">
      <div class="step-circle">4</div>
      <h2>Booking <span>Summary</span></h2>
    </div>
    <div class="card-body">

      <!-- Inclusions / Safety toggles -->
      <div class="safety-box">
        <div class="safety-header" id="inclusion-content-toggle" data-id="">
          <span class="material-icons text-warning">shield</span>
          <span>Inclusions &amp; Exclusions</span>
          <span class="material-icons ms-auto">expand_more</span>
        </div>
        <div class="safety-header" id="toggle-safety">
          <span class="material-icons text-warning">shield</span>
          <span>Safety Guidelines</span>
          <span class="material-icons ms-auto">expand_more</span>
        </div>
      </div>

      <!-- Fare breakdown -->
      <div class="form-group">
        <div id="breakup-details">
          <div class="breakup-row">
            <span class="breakup-label">Base Fare</span>
            <span class="breakup-value" id="s4-base">—</span>
          </div>
          <div class="breakup-row">
            <span class="breakup-label">GST (5%)</span>
            <span class="breakup-value" id="s4-tax">—</span>
          </div>
          <div class="breakup-row">
            <span class="breakup-label">Govt. Levy / Toll</span>
            <span class="breakup-value" id="s4-toll">—</span>
          </div>
        </div>
      </div>

      <div class="fare-total-row">
        <span class="lbl">Estimated fare</span>
        <span class="amt estimated_fare">—</span>
      </div>

      <div class="form-group d-flex justify-content-center align-items-center">
        <button class="glow-button" id="proceed-to-payment-btn">
          <span class="material-icons search-icon">search</span>
          FIND DRIVER NOW
        </button>
      </div>

      <!-- Safety overlay -->
      <div id="safety-view" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 style="color:#000;font-size:17px;margin:0;">Safety Guidelines</h4>
          <button id="close-safety" class="inclusions-back-btn">
            <span class="material-icons">close</span>
          </button>
        </div>
        <div class="safety-view">
          <h6 style="margin:6px 0 4px;font-size:15px;color:#f9bf00;">Before Starting the Ride</h6>
          <ul style="padding-left:14px;margin-bottom:8px;">
            <li>Verify the driver's photo and name</li>
            <li>Check vehicle details (number plate &amp; model)</li>
            <li>Cross check ride charges &amp; Kms</li>
            <li>Take odometer photo before trip starts</li>
            <li>Share trip details with trusted contact</li>
          </ul>
          <h6 style="margin:6px 0 4px;font-size:12px;color:#f9bf00;">After Completing the Ride</h6>
          <ul style="padding-left:14px;margin-bottom:0;">
            <li>Take final odometer photo</li>
            <li>Cross-check Govt. levy with receipts</li>
            <li>Collect all your belongings</li>
            <li>Confirm payment after verifying charges</li>
          </ul>
        </div>
      </div>

      <!-- Inclusions overlay (step 4) -->
      <div id="inclusions-view-2" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h4 id="inclusions-view-title-2" style="font-size:15px;color:#000;margin:0;">Inclusions &amp; Exclusions</h4>
          <button id="close-inclusions-2" class="inclusions-back-btn">
            <span class="material-icons">close</span>
          </button>
        </div>
        <div id="inclusions-view-body-2"></div>
      </div>

    </div>
  </div>

  <!-- ════════════════ STEP 5 : CONFIRMATION ════════════════ -->
  <div class="glass-card" id="card-5" style="display:none;">
    <div class="card-body">

      <div class="d-flex flex-column align-items-center mb-3">
        <div class="confirm-icon-circle">
          <span class="material-icons primary-color confirm-icon">check_circle</span>
        </div>
        <h2 style="font-size:18px !important;margin:0 0 6px;color:white;">Booking Confirmed!</h2>
        <div class="booking-id-box">
          <p class="mb-0 text-white">
            Booking ID: <span class="primary-color fw-bold" id="job_id">######</span>
          </p>
        </div>
      </div>

      <div class="trip-mini-card">
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">my_location</span>
          <div class="mini-text"><small>Pickup</small><p id="confirm-pickup"></p></div>
        </div>
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">location_on</span>
          <div class="mini-text"><small>Destination</small><p id="confirm-destination"></p></div>
        </div>
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">schedule</span>
          <div class="mini-text"><small>Date &amp; Time</small><p id="confirm-datetime"></p></div>
        </div>
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">directions_car</span>
          <div class="mini-text"><small>Vehicle</small><p id="confirm-vehicle" class="vehicle-highlight"></p></div>
        </div>
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">route</span>
          <div class="mini-text"><small>Distance</small><p class="vehicle-highlight" id="confirm-dist"></p></div>
        </div>
        <div class="trip-mini-row">
          <span class="material-icons mini-icon">currency_rupee</span>
          <div class="mini-text"><small>Estimated Fare</small><p class="vehicle-highlight estimated_fare"></p></div>
        </div>
      </div>

      <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
        <div class="booking-id-preview">
          <p style="margin:0;">
            <a href="#" id="b-pre-link" target="_blank">
              <span class="material-icons" style="font-size:16px;">visibility</span>
              Booking Information
            </a>
          </p>
        </div>
        <div class="booking-id-preview">
          <p style="margin:0;">
            <a href="#" id="fd-pre-link" target="_blank">
              <span class="material-icons" style="font-size:16px;">visibility</span>
              PICK YOUR DRIVER
            </a>
          </p>
        </div>
      </div>

      <div class="text-center">
        <p style="color:rgba(255,255,255,.6);font-size:17px;line-height:1.5;margin:0 0 6px;">
          Your booking has been successfully confirmed. A driver will be assigned soon.
        </p>
        <p style="font-size:15px;color:rgba(255,255,255,.5);margin:0;">
          Need help?
          <a href="mailto:support@goride.run" style="color:#fff;">support@goride.run</a> |
          <a href="tel:+916369742104" style="color:#fff;">+91 63697 42104</a>
        </p>
      </div>
      <div class="text-center mt-3">
    <button class="glow-button mx-auto" id="book-again-btn">
        <span class="material-icons">refresh</span>
        Book Another Ride
    </button>
</div>

    </div>
  </div>

</div></div>  

   <div class="col-md-3">  
   <div class="trip-summary-sticky">
   <div class="route-pill row">

    <!-- Edit Button -->
    <div class="col-7 order-1 order-md-2 text-center  mt-2 mb-md-0">
        <button type="button" class="glow-button w-100 py-2" id="edit-trip-btn">
            <span class="material-icons">edit</span>
            Edit Trip
        </button>
    </div>

    <!-- Route Details -->
    <div class="col-12 order-2 order-md-1">

        <div class="route-line-1">
            <div class="d-flex align-items-center align-items-md-start gap-1">
                <span class="material-icons" style="font-size:19px;color:red;">location_on</span>
                <span class="route-text" id="s2-from"></span>
            </div>

              <div class="text-center">
        <span class="material-icons route-arrow"  id="route-arrow">arrow_forward</span>
    </div>

            <div class="d-flex align-items-center align-items-md-start gap-1">
                <span class="material-icons" style="font-size:19px;color:red;">location_on</span>
                <span class="route-text" id="s2-to"></span>
            </div>
        </div>

        <div class="route-line-2">

    <div class="d-flex align-items-center mb-2">
        <span class="material-icons me-2" style="color:blue;">event</span>
        <span id="s2-date"></span>
    </div>

    <div class="d-flex align-items-center mb-2">
        <span class="material-icons me-2" style="color:crimson;">schedule</span>
        <span id="s2-time"></span>
    </div>

    <div class="d-flex align-items-center mb-2">
        <span class="material-icons me-2" style="color:darkred;" id="s2-type-icon">trending_flat</span>
        <span id="s2-type">One Way</span>
    </div>

    <div class="d-flex align-items-center mb-2" id="s2-days-wrap" style="display:none;">
        <span class="material-icons me-2" style="color:darkgreen;">calendar_today</span>
        <span><span id="s2-days"></span> Days</span>
    </div>

    <div class="d-flex align-items-center mb-2">
        <span class="material-icons me-2" style="color:goldenrod;">route</span>
        <span id="s2-dist">0 kms</span>
    </div>

    <div class="d-flex align-items-center">
        <span class="material-icons me-2" style="color:crimson;">schedule</span>
        <span id="s2-dur">0 hrs</span>
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

<script>

$(document).ready(function () {

  // ── 1. Load session data ──────────────────────────────────────────────
  const raw = sessionStorage.getItem('gorideBookingData');
  if (!raw) {
    showToast('error', 'No booking data found. Redirecting...', 3000);
    setTimeout(function() { window.location.href = '/'; }, 2200);
    return;
  }

  const bd   = JSON.parse(raw);        // booking data
  const cabs = bd.cabs || [];
  const isRoundTrip = (bd.tripType === 'roundtrip');

  if (!cabs.length) {
    showToast('error', 'No cabs available. Please try again.', 3000);
    setTimeout(function() { window.location.href = '/'; }, 2200);
    return;
  }

  // ── 2. State ──────────────────────────────────────────────────────────
  let selectedCab   = null;
  let passengerCount = 1;
  let luggageCount   = 0;
  let journeyPayload = {};

  // ── 3. Helpers ────────────────────────────────────────────────────────
  function formatCurrency(amount) {
    return '₹' + Number(amount || 0).toLocaleString('en-IN');
  }

  function getCabById(id) {
    return cabs.find(function(c) { return c.id == id; });
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    var d = new Date(dateStr);
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  function convert24hours(dateStr, timeStr) {
    var parts = (timeStr || '').trim().match(/(\d{1,2}):(\d{2})\s?(AM|PM)/i);
    if (!parts) return dateStr + ' 00:00:00';
    var h = parseInt(parts[1], 10);
    var m = parts[2];
    var p = parts[3].toUpperCase();
    if (p === 'PM' && h !== 12) h += 12;
    if (p === 'AM' && h === 12) h = 0;
    return dateStr + ' ' + String(h).padStart(2,'0') + ':' + m + ':00';
  }

  // ── 4. Populate route pills ───────────────────────────────────────────
  function populateRoutePills() {
    var from  = (bd.pickup      || '').slice(0, 60);
    var to    = (bd.destination || '').slice(0, 60);
    var date  = formatDate(bd.travelDate);
    var time  = bd.pickupTime || '';
    var type  = isRoundTrip ? 'Round Trip' : 'One Way';
    var ticon = isRoundTrip ? 'sync_alt' : 'trending_flat';
    var dist  = cabs[0] ? ('Upto ' + cabs[0].distance + ' kms') : '0 kms';
    var dur   = cabs[0] ? cabs[0].day : '0 hrs';

    // Step 2
    $('#s2-from').text(from); $('#s2-to').text(to);
    $('#s2-date').text(date); $('#s2-time').text(time);
    $('#s2-type').text(type); $('#s2-type-icon').text(ticon);
    if (isRoundTrip) {
    $('#route-arrow')
        .text('sync_alt')
        .css('transform', 'rotate(90deg)');
} else {
    $('#route-arrow')
        .text('arrow_forward')
        .css('transform', 'rotate(90deg)');
}
    $('#s2-dist').text(dist); $('#s2-dur').text(dur);
    if (isRoundTrip && bd.returnDays) {
      $('#s2-days-wrap').show(); $('#s2-days').text(bd.returnDays);
    }

    // Step 3
    $('#s3-from').text(from); $('#s3-to').text(to);
    $('#s3-date').text(date); $('#s3-time').text(time);
    $('#s3-type').text(type); $('#s3-type-icon').text(ticon);

    // Step 4
    $('#s4-from').text(from); $('#s4-to').text(to);
    $('#s4-date').text(date); $('#s4-time').text(time);
    $('#s4-type').text(type); $('#s4-type-icon').text(ticon);
    $('#s4-dist').text(dist); $('#s4-dur').text(dur);
  }

  populateRoutePills();

  // ── 5. Build inclusions HTML ──────────────────────────────────────────
  function buildIncHTML(cab) {
    var inc = cab.inc_km || 'Standard';
    var pkm = cab.per_km || 12;

    var inc_html =
      '<div class="inclusions-grid">' +
        '<div class="inclusions-col">' +
          '<h5 class="section-title text-warning"><span class="material-icons">check_circle</span> Inclusions</h5>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">speed</span><p>' + inc + ' kms included</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">person_pin</span><p>Driver allowance included</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">restaurant</span><p>Driver food &amp; accommodation (stay) included</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">schedule</span><p>Waiting up to 30 min free (₹100 per 60 min after)</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">map</span><p>Sightseeing included</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">local_gas_station</span><p>Fuel charges included</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">receipt_long</span><p>Govt. levy extra included (based on actual value)</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">local_parking</span><p>Parking charges included</p></div>' +
        '</div>' +
        '<div class="exclusions-col">' +
          '<h5 class="section-title text-warning"><span class="material-icons">cancel</span> Exclusions</h5>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">location_city</span><p>State permit / entry charges</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">terrain</span><p>Hill station charges (extra)</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">toll</span><p>Govt. levy extra &amp; parking charges</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">add_road</span><p>Additional km: ₹' + pkm + '/km</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">schedule</span><p>Extra hours/days: ₹100/hr &amp; ₹1200/day</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">hourglass_bottom</span><p>Waiting beyond 30 min: extra charges apply</p></div>' +
          '<div class="inclusion-row"><span class="material-icons text-warning">receipt_long</span><p>Any government taxes or local charges, if applicable</p></div>' +
        '</div>' +
      '</div>';

    return inc_html;
  }

  // ── 6. Update fare summary ────────────────────────────────────────────
  function updateFareSummary() {
    if (!selectedCab) return;
    var cab   = getCabById(selectedCab);
    var base  = Number(cab.fareNum  || 0);
    var toll  = Number(cab.toll_fare || 0);
    var tax   = Number(cab.tax      || 0);
    var total = base + toll + tax;

    $('#s4-base').text(formatCurrency(base));
    $('#s4-tax').text(formatCurrency(tax));
    $('#s4-toll').text(formatCurrency(toll));
    $('.estimated_fare').text(formatCurrency(total));
    $('#confirm-dist').text('Upto ' + (cab.distance || '0') + ' kms');

    // Update inclusions toggle data-id
    $('#inclusion-content-toggle').attr('data-id', cab.id);
    // Update selected vehicle label in pills
    $('.selected-vehicle-lbl').text(cab.name);
  }

  // ── 7. Select cab ─────────────────────────────────────────────────────
  function selectCab(id) {
    selectedCab = id;
    $('.cab-card').removeClass('selected');
    $('.cab-card[data-cab-id="' + id + '"]').addClass('selected');

    var cab = getCabById(id);
    if (!cab) return;

    luggageCount   = (id == 1) ? 0 : (cab.luggage || 2);
    passengerCount = 1;

    $('#passenger-count-display').text(passengerCount);
    $('#luggage-count-display').text(luggageCount);

    updateFareSummary();
  }

  // ── 8. Build cab grid ─────────────────────────────────────────────────
  function buildCabGrid() {
    var $grid = $('#cab-list');
    $grid.empty();

    cabs.forEach(function (cab) {
      var lugDisplay = (cab.id == 1) ? 0 : (cab.luggage || 2);

      var $card = $(
        '<div class="cab-card" data-cab-id="' + cab.id + '">' +
          '<div class="d-flex flex-md-column">' +
            '<div class="cab-icon">' +
              '<img src="' + cab.image + '" alt="' + cab.name + '" class="cab-car-image"/>' +
            '</div>' +
            '<div class="cab-details">' +
              '<div class="d-flex flex-column flex-md-row justify-content-between align-items-center">' +
                '<div class="d-flex justify-content-center align-items-center gap-2">' +
                  '<div class="fare-breakdown">' +
                    '<div class="breakup-header hover-inclusions-trigger" data-cab-id="' + cab.id + '">' +
                      '<span style="font-weight:600;color:#000;">i</span>' +
                    '</div>' +
                  '</div>' +
                  '<span class="cab-price-amount">' + (cab.price || '—') + '</span>' +
                '</div>' +
                '<p class="onward mb-0 text-white">Onwards</p>' +
              '</div>' +
            '</div>' +
          '</div>' +
          '<div class="d-flex align-items-center justify-content-between mt-1">' +
            '<h3 style="font-size:16px;color:#fff;margin:0;">' + cab.name + '</h3>' +
            '<div class="cab-features">' +
              '<span class="cab-feature"><span class="material-icons">person</span>' + (cab.capacity || 4) + '</span>' +
              '<span class="cab-feature"><span class="material-icons">luggage</span>' + lugDisplay + '</span>' +
            '</div>' +
          '</div>' +
         '<div class="d-flex justify-content-end justify-content-md-center mt-2">' +
    '<button class="book-now-btn" data-cab-id="' + cab.id + '">Book Now</button>' +
'</div>' +
        '</div>'
      );

      $grid.append($card);
    });

    // Click card → select
    $grid.on('click', '.cab-card', function (e) {
      if ($(e.target).closest('.fare-breakdown').length) return;
      selectCab($(this).data('cab-id'));
    });


    // $grid.on('click', '.book-now-btn', function (e) {
    //   e.stopPropagation();
    //   selectCab($(this).data('cab-id'));
 
    //   setTimeout(function() {
    //     document.getElementById('card-3').scrollIntoView({ behavior: 'smooth', block: 'start' });
    //   }, 150);
    // });

    // "i" inclusions button
    $grid.on('click', '.hover-inclusions-trigger', function (e) {
      e.stopPropagation();
      var cabId = $(this).data('cab-id');
      var cab   = getCabById(cabId);
      if (!cab) return;
      selectedCab = cabId;
      $('.cab-card').removeClass('selected');
      $('.cab-card[data-cab-id="' + cabId + '"]').addClass('selected');
      updateFareSummary();
      $('#inclusions-view-title').text(cab.name + ' — Inclusions & Exclusions');
      $('#inclusions-view-body').html(buildIncHTML(cab));
      $('#inclusions-view').show();
    });

    // Auto-select first cab
    if (cabs.length) selectCab(cabs[0].id);
  }

  buildCabGrid();

  // ── 9. Close inclusions ───────────────────────────────────────────────
  $(document).on('click', '#close-inclusions', function (e) {
    e.stopPropagation();
    $('#inclusions-view').hide();
  });
  $(document).on('click', '#close-inclusions-2', function (e) {
    e.stopPropagation();
    $('#inclusions-view-2').hide();
  });

  // ── 10. Inclusions toggle (step 4) ───────────────────────────────────
  $(document).on('click', '#inclusion-content-toggle', function (e) {
    e.stopPropagation();
    var cabId = $(this).data('id') || selectedCab;
    var cab   = getCabById(cabId);
    if (!cab) { showToast('error', 'Please select a cab first', 2000); return; }
    $('#inclusions-view-title-2').text(cab.name + ' — Inclusions & Exclusions');
    $('#inclusions-view-body-2').html(buildIncHTML(cab));
    $('#inclusions-view-2').show();
  });

  // ── 11. Safety toggle ────────────────────────────────────────────────
  $(document).on('click', '#toggle-safety', function (e) {
    e.stopPropagation();
    $('#safety-view').show();
  });
  $(document).on('click', '#close-safety', function (e) {
    e.stopPropagation();
    $('#safety-view').hide();
  });

  // ── 12. Passenger counters ────────────────────────────────────────────
  $('#increase-passengers').on('click', function () {
    var cab = getCabById(selectedCab);
    var max = cab ? (cab.capacity || 4) : 4;
    if (passengerCount < max) { passengerCount++; $('#passenger-count-display').text(passengerCount); }
  });
  $('#decrease-passengers').on('click', function () {
    if (passengerCount > 1) { passengerCount--; $('#passenger-count-display').text(passengerCount); }
  });

  $('#increase-luggage').on('click', function () {
    var cab    = getCabById(selectedCab);
    var maxLug = (selectedCab == 1) ? 0 : (cab ? (cab.luggage || 2) : 2);
    if (luggageCount < maxLug) { luggageCount++; $('#luggage-count-display').text(luggageCount); }
  });
  $('#decrease-luggage').on('click', function () {
    if (luggageCount > 0) { luggageCount--; $('#luggage-count-display').text(luggageCount); }
  });

  // ── 13. Name / Phone keypress guards ─────────────────────────────────
  $('#full-name').on('keypress', function (e) {
    var c = e.which;
    if (c >= 48 && c <= 57) {
      e.preventDefault();
      showToast('error', 'Numbers are not allowed in the name field', 2000);
    }
  });
  $('#phone').on('keypress', function (e) {
    var c = e.which;
    if (c < 48 || c > 57) { e.preventDefault(); return; }
    if (this.value.length === 0 && c === 48) {
      e.preventDefault();
      showToast('error', 'Mobile number cannot start with 0', 2000);
    }
  });

  // ── 14. FIND DRIVER NOW ───────────────────────────────────────────────
  $('#proceed-to-payment-btn').on('click', function () {
    var $btn = $(this);

    // Validate name
    var name = $('#full-name').val().trim();
    if (!name) { showToast('error', 'Fill the passenger name', 2500); $('#full-name').focus(); return; }
    if (!/^[a-zA-Z\s]+$/.test(name)) { showToast('error', 'Name should only contain letters', 2500); $('#full-name').focus(); return; }

    // Validate phone
    var phone = $('#phone').val().trim();
    if (!phone) { showToast('error', 'Fill the passenger mobile number', 2500); $('#phone').focus(); return; }
    if (phone.startsWith('0')) { showToast('error', 'Mobile number should not start with 0', 2500); $('#phone').focus(); return; }
    if (phone.length < 10) { showToast('error', 'Please enter a valid 10-digit mobile number', 2500); $('#phone').focus(); return; }

    if (!selectedCab) { showToast('error', 'Please select a cab first', 2500); return; }

    var cab = getCabById(selectedCab);
    if (!cab) return;

    var pickupDateTime = convert24hours(bd.travelDate, bd.pickupTime);

    journeyPayload = {
      job_type:    isRoundTrip ? 'roundtrip' : 'oneway',
      from_place:  bd.pickup,
      to_place:    bd.destination,
      from_place_id: bd.fromPlaceId || '',
      to_place_id:   bd.toPlaceId   || '',
      pickup_date:   pickupDateTime,
      dropoff_date:  isRoundTrip ? (bd.returnDays || '') : '',
      pass_count:    passengerCount,
      lugg_count:    luggageCount,
      fare:          Number(cab.fareNum  || 0),
      distance:      cab.distance || '',
      duration:      cab.duration || '',
      day:           cab.day      || '',
      toll:          Number(cab.toll_fare || 0),
      tax:           Number(cab.tax       || 0),
      cab_type:      cab.cab_type || '',
      add_fare_details: { bata: 'Excluded', parking: 'Excluded', toll: 'Excluded' },
      type:         'customer',
      c_name:        name,
      c_email:       '',
      c_mobile:      phone,
      pick_address:  '',
      drop_address:  '',
      pick_lat:      bd.fromLat || '',
      pick_lan:      bd.fromLng || '',
      drop_lat:      bd.toLat   || '',
      drop_lan:      bd.toLng   || '',
      isDriver:      'no',
      recaptcha_token: ''
    };

    $btn.prop('disabled', true)
        .data('orig', $btn.html())
        .html('Processing...');

    $.ajax({
      url: "{{ env('APP_API') }}web-book-journey",
      type: "POST",
      data: journeyPayload,
      dataType: "json",
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

      success: function (res) {
        if (res.status === true) {
          showToast('success', res.message || 'Booking confirmed!', 3000);

          // Fill confirmation card
          $('#job_id').text('#' + res.data);
          $('#confirm-pickup').text(bd.pickup);
          $('#confirm-destination').text(bd.destination);
          $('#confirm-datetime').text(formatDate(bd.travelDate) + ' | ' + (bd.pickupTime || ''));
          $('#confirm-vehicle').text(cab.name);

          if (res.preview) $('#b-pre-link').attr('href', res.preview).attr('target','_blank');
          if (res.fd)      $('#fd-pre-link').attr('href', res.fd).attr('target','_blank');

          // Show confirmation
          $('#card-5').show();

          setTimeout(function () {
            document.getElementById('card-5').scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 200);

        } else {
          showToast('error', res.message || 'Booking failed. Please try again.', 3000);
          $btn.prop('disabled', false).html($btn.data('orig'));
        }
      },

      error: function () {
        showToast('error', 'Something went wrong. Please try again.', 3000);
        $btn.prop('disabled', false).html($btn.data('orig'));
      }
    });
  });

  // ── Dismiss overlays on outside click ────────────────────────────────
  $(document).on('click', function (e) {
    if (!$(e.target).closest('#inclusions-view, .hover-inclusions-trigger').length) {
      $('#inclusions-view').hide();
    }
    if (!$(e.target).closest('#inclusions-view-2, #inclusion-content-toggle').length) {
      $('#inclusions-view-2').hide();
    }
    if (!$(e.target).closest('#safety-view, #toggle-safety').length) {
      $('#safety-view').hide();
    }
  });
  
  $(document).on("click", "#edit-trip-btn", function () {
    window.location.href = "/booking";
});

$(document).on("click", "#book-again-btn", function () {

    sessionStorage.removeItem("gorideBookingData");

    window.location.href = "/booking";

});

}); // end document.ready
</script>
@endsection