<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoRide – Outstation Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
    /* ===== RESET ===== */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{margin:0;padding:0;overflow-x:hidden;}
    body{font-family:'Inter',sans-serif;background:#f5f6f8;color:#1a1a1a;}
    button:focus,button:active,button:focus-visible,.btn:focus,.btn:active,.btn:focus-visible{outline:none!important;box-shadow:none!important;}

    /* ===== LOGO ===== */
    .logo{display:flex;align-items:center;text-decoration:none;}
    .logo-img{height:50px;width:auto;display:block;}

    /* ===== SEARCH STRIP ===== */
    .search-strip{position:fixed;top:0;left:0;right:0;background:#fff;border-bottom:1px solid #e8ecf0;z-index:999;padding:12px 24px;}
    .search-strip-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:0;border:1.5px solid #e0e4ea;border-radius:10px;overflow:visible;background:#fff;height:52px;justify-content:space-between;}

    /* ===== TRIP TABS ===== */
    .trip-tabs{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:8px;margin-bottom:10px;justify-content:space-between;}
    .trip-tab{padding:8px 16px;border:1.5px solid #e0e4ea;background:#fff;border-radius:20px;font-size:12.5px;font-weight:600;color:#6b7280;cursor:pointer;display:flex;align-items:center;gap:5px;font-family:'Inter',sans-serif;transition:all .15s;}
    .trip-tab.active{background:#1a1a1a;color:#fff;border-color:#1a1a1a;}

    /* ===== WELCOME BAR ===== */
    .welcome-user-bar{display:flex;align-items:center;gap:9px;background:#f5f7fa;border-radius:22px;padding:4px 14px 4px 4px;margin-left:auto;}
    .wub-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0A1628,#2a4570);color:#F5A623;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;overflow:hidden;}
    .wub-av img{width:100%;height:100%;object-fit:cover;}
    .wub-name{font-size:13px;font-weight:600;color:#1a1a1a;white-space:nowrap;}
    .wub-name span{font-weight:800;color:#072e69;}
    @media(max-width:680px){.welcome-user-bar{padding:3px 10px 3px 3px;}.wub-av{width:26px;height:26px;font-size:12px;}.wub-name{font-size:11.5px;}}

    /* ===== SEARCH FIELDS ===== */
    .sf{display:flex;flex-direction:column;justify-content:center;padding:0 14px;position:relative;cursor:pointer;min-width:0;}
    .sf:hover{background:#fafbfc;}
    .sf-lbl{font-size:9.5px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.6px;margin-bottom:1px;}
    .sf-val{font-size:13.5px;font-weight:700;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:flex;align-items:center;gap:5px;}
    .sf-val i{font-size:13px;color:#041990;}
    .sf-val .chevdown{font-size:11px;color:#9aa3af;margin-left:2px;}
    .sf-date{flex:0 0 140px;}
    .sf-time{flex:0 0 120px;}
    .sf-return{flex:0 0 155px;}

    /* ===== CITY WRAP ===== */
    .city-wrap{position:relative;flex:0 0 210px;display:flex;flex-direction:column;justify-content:center;padding:0;}
    .city-wrap:hover{background:#fafbfc;}
    .city-inner{display:flex;flex-direction:column;justify-content:center;padding:0 14px;height:100%;}
    .city-text-input{position:absolute;inset:0;width:100%;height:100%;border:none;outline:none;background:transparent;font-family:'Inter',sans-serif;font-size:13.5px;font-weight:700;color:#1a1a1a;padding:18px 14px 4px;cursor:text;opacity:0;}
    .city-text-input.active{opacity:1;z-index:10;}
    .city-text-input::placeholder{color:#b0b8c8;font-weight:500;}
    .city-lbl{font-size:9.5px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.6px;margin-bottom:1px;}
    .city-display{font-size:13.5px;font-weight:700;color:#1a1a1a;display:flex;align-items:center;gap:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .city-display i{font-size:13px;color:#F5A623;}
    .city-display .city-name{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .city-dropdown{position:absolute;top:calc(100% + 6px);left:0;width:240px;background:#fff;border:1.5px solid #ffb800;border-radius:12px;box-shadow:0 8px 30px rgba(22,105,245,.13);z-index:9999;overflow:hidden;display:none;}
    .city-dropdown.open{display:block;}
    .city-dd-item{padding:10px 16px;font-size:13.5px;font-weight:600;color:#1a1a1a;cursor:pointer;display:flex;align-items:center;gap:10px;border-bottom:1px solid #f5f7fa;}
    .city-dd-item:last-child{border-bottom:none;}
    .city-dd-item:hover{background:#fff8e1;color:#ffb800;}
    .city-dd-item i{font-size:13px;color:#ffb800;}

    /* ===== SWAP ===== */
    .swap-circle{width:30px;height:30px;border-radius:50%;border:1.5px solid #e0e4ea;background:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;color:#555;cursor:pointer;flex-shrink:0;align-self:center;margin:0 -15px;z-index:2;transition:all .15s;}
    .swap-circle:hover{border-color:#ffb800;color:#ffb800;}
    .swap-circle.round-trip-icon{display:flex;flex-direction:column;gap:0;font-size:9px;line-height:1;}
    .swap-circle.round-trip-icon i{font-size:9px;line-height:1;}
    .swap-circle.round-trip-icon i:first-child{margin-bottom:1px;color:#1669F5;}
    .swap-circle.round-trip-icon i:last-child{color:#e53e3e;}

    /* ===== SEARCH BUTTON ===== */
    .search-go-btn{height:52px;padding:0 32px;background:#F5A623;color:#fff;border:none;border-radius:0 8px 8px 0;font-size:15px;font-weight:800;cursor:pointer;display:flex;align-items:center;gap:6px;flex-shrink:0;font-family:'Inter',sans-serif;margin-right:-1px;}
    .search-go-btn:hover{background:#e0961a;}

    /* ===== DATE STRIP ===== */
    #datePicker,#datePickerMob{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none;}
    .date-strip-wrap{position:fixed;left:0;right:0;background:#fff;border-bottom:1px solid #e8ecf0;z-index:998;padding:10px 24px;display:none;}
    .date-strip-wrap.show{display:block;}
    .date-strip-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:8px;}
    .date-strip-scroll{display:flex;align-items:center;gap:8px;overflow-x:auto;scrollbar-width:none;flex:1;}
    .date-strip-scroll::-webkit-scrollbar{display:none;}
    .date-chip{text-align:center;padding:8px 12px;border:1.5px solid #e0e4ea;border-radius:10px;cursor:pointer;transition:all .15s;font-family:'Inter',sans-serif;background:#fff;gap:7px;display:flex;flex-direction:column;}
    .date-chip:hover{border-color:#F5A623;}
    .date-chip.active{background:#0A1628!important;border-color:#0A1628;}
    .date-chip .dc-day{font-size:12px;font-weight:700;color:#1a1a1a;}
    .date-chip.active .dc-day{color:#fff;}
    .date-chip .dc-price{font-size:12.5px;font-weight:800;color:#4c525b;margin-top:2px;}
    .date-chip.lowest{background:#ff9f00ed;}
    .date-chip.lowest .dc-day{font-weight:700;}
    .date-chip.lowest .dc-price{color:white;font-weight:800;}
    .date-chip.active .dc-day,.date-chip.active .dc-price{color:#fff!important;}

    /* ===== MAIN WRAP ===== */
    .main-wrap{max-width:1200px;margin:0 auto;padding:20px 24px 35px;}
    .layout-grid{display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start;}
    .sidebar{position:sticky;}

    /* ===== MAP BOX ===== */
    .map-box{border-radius:12px;overflow:hidden;height:200px;background:#cdd9e5;margin-bottom:14px;border:1px solid #dde3eb;position:relative;}
    .map-box svg{width:100%;height:100%;}
    .map-btn{position:absolute;bottom:10px;left:50%;transform:translateX(-50%);background:#1a1a1a;color:#fff;border:none;border-radius:20px;padding:7px 16px;font-size:12.5px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap;font-family:'Inter',sans-serif;}

    /* ===== SORT BOX ===== */
    .sort-box{background:#fff;border-radius:12px;border:1px solid #e8ecf0;padding:14px 16px;}
    .sort-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
    .sort-title{font-size:14px;font-weight:700;color:#1a1a1a;}
    .sort-clear{font-size:12px;font-weight:600;color:#6b7280;cursor:pointer;}
    .sort-item{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f5f7fa;cursor:pointer;}
    .sort-item:last-child{border-bottom:none;}
    .sort-left{display:flex;align-items:center;gap:9px;font-size:13px;font-weight:500;color:#1a1a1a;}
    .sort-radio{width:16px;height:16px;border:2px solid #dde3eb;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .sort-radio.on{border-color:#F5A623;background:#F5A623;}
    .sort-radio.on::after{content:'';width:5px;height:5px;background:#fff;border-radius:50%;}
    .sort-ic{font-size:14px;color:#9aa3af;}
    .divider-line{border-top:1px solid #e8ecf0;margin:10px 0 6px;}
    .trust-row{display:flex;align-items:center;gap:8px;padding:5px 0;font-size:12px;font-weight:500;color:#1a1a1a;}
    .trust-row .trust-icon{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
    .trust-row .trust-text .t1{font-size:12px;font-weight:700;color:#1a1a1a;}
    .trust-row .trust-text .t2{font-size:11px;color:#9aa3af;font-weight:500;}

    /* ===== RESULTS HEADER ===== */
    .results-header{display:flex;align-items:center;margin-bottom:12px;font-size:14px;color:#6b7280;font-weight:500;flex-wrap:wrap;}
    .results-header strong{color:#1a1a1a;font-weight:700;}
    .rides-count{margin-left:auto;font-size:13.5px;font-weight:700;color:#1a1a1a;}

    /* ===== RIDE CARD BASE ===== */
    .ride-card{background:#fff;border:1px solid #e8ecf0;border-radius:14px;padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;gap:18px;transition:all .2s;position:relative;box-shadow:0 1px 3px rgba(16,24,40,.04);justify-content:space-around;}
    .ride-card:hover{border-color:#c5d3f0;box-shadow:0 6px 20px rgba(22,105,245,.1);transform:translateY(-1px);}
    .ride-card.full{opacity:.4;pointer-events:none;}
    .ride-card{padding-top:28px;}

    /* ===== BADGE ===== */
    .ride-type-badge{position:absolute;top:0;left:0;font-size:10.5px;font-weight:800;padding:4px 11px;border-radius:0 0 8px 0;text-transform:uppercase;letter-spacing:.4px;display:flex;align-items:center;gap:4px;z-index:2;}
    .ride-type-badge.private{background:#072e69;color:white;}
    .ride-type-badge.carpool{background:#16a34a;color:#fff;}

    /* ===== PRIVATE CARD – NEW DESIGN ===== */
    .prv-left{display:flex;flex-direction:column;gap:6px;min-width:130px;}
    .prv-route-cities{display:flex;align-items:center;gap:8px;    justify-content: space-between;}
    .prv-city{font-size:18px;font-weight:800;color:#1a1a1a;line-height:1.1;}
    .prv-arrow{font-size:12px;color:#9aa3af;font-weight:700;flex-shrink:0;}
    .prv-city-sub{font-size:11px;color:#6b7280;font-weight:500;}

    /* single pill row: date · time · triptype */
    .prv-meta-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:4px;}
    .prv-meta-chip{display:flex;align-items:center;gap:4px;font-size:11.5px;font-weight:600;color:#444;background:#f5f7fa;border-radius:20px;padding:3px 9px;}
    .prv-meta-chip i{font-size:11px;color:#F5A623;}
    .prv-meta-sep{color:#d0d5de;font-size:10px;}

    /* distance + duration pill */
    .prv-dist-row{display:flex;align-items:center;gap:6px;margin-top:4px;}
    .prv-dist-chip{display:flex;align-items:center;gap:4px;font-size:11.5px;font-weight:600;color:#444;background:#eef3ff;border-radius:20px;padding:3px 9px;}
    .prv-dist-chip i{font-size:11px;color:#1669F5;}
    .prv-dur-chip{display:flex;align-items:center;gap:4px;font-size:11.5px;font-weight:700;color:#c97a00;background:#FFF3D6;border-radius:20px;padding:3px 9px;}
    .prv-dur-chip i{font-size:10px;}

    /* dep → arr times */
    .prv-times{display:flex;flex-direction:column;align-items:center;gap:2px;flex-shrink:0;min-width:80px;}
    .prv-dep-time{font-size:22px;font-weight:800;color:#1a1a1a;line-height:1;}
    .prv-arr-block{display:flex;align-items:center;gap:4px;}
    .prv-arr-time{font-size:16px;font-weight:700;color:#6b7280;}
    .prv-next-day{font-size:10px;font-weight:800;color:#F5A623;background:#FFF3D6;border-radius:10px;padding:1px 5px;}
    .prv-time-lbl{font-size:9.5px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.5px;}

    /* ===== CARPOOL CARD (desktop) ===== */
    .card-dep,.card-arr{flex-shrink:0;}
    .card-dep{min-width:70px;}
    .card-arr{min-width:66px;}
    .dep-time,.arr-time{font-size:20px;font-weight:800;color:#1a1a1a;line-height:1.1;}
    .dep-city,.arr-city{font-size:12px;color:#001536;font-weight:500;margin-top:2px;}
    .card-route{min-width:0;flex:0 0 110px;display:flex;flex-direction:column;align-items:center;gap:4px;}
    .route-dur{font-size:13px;font-weight:600;color:#f79a00;display:flex;align-items:center;gap:4px;white-space:nowrap;}
    .route-dur i{font-size:10px;}
    .route-line{display:flex;align-items:center;width:100%;}
    .route-dot{width:8px;height:8px;border-radius:50%;background:#1a1a1a;flex-shrink:0;}
    .route-track{flex:1;height:1.5px;background:linear-gradient(90deg,#1a1a1a 0%,#b0b8c8 100%);position:relative;}
    .route-track::after{content:'';position:absolute;right:-1px;top:50%;transform:translateY(-50%);border-style:solid;border-width:3.5px 0 3.5px 5px;border-color:transparent transparent transparent #b0b8c8;}
    .cp-stop{min-width:90px;text-align:center;}
    .cp-title{font-size:10px;color:#999;font-weight:700;text-transform:uppercase;}
    .cp-city{font-size:12px;font-weight:700;color:#111;margin-top:2px;}

    /* ===== CAR SECTION ===== */
    .card-car{flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:3px;min-width:86px;}
    .car-icon-row{display:flex;align-items:center;gap:6px;}
    .car-img{width:100%;height:60px;object-fit:cover;cursor:pointer;transition:transform .15s;border-radius:6px;}
    .car-img:hover{transform:scale(1.08);}
    .car-type-lbl{font-size:13px;font-weight:600;color:#1a1a1a;}
    .car-seat-lbl{font-size:11px;color:#5f6368;font-weight:500;}

    /* ===== DRIVER ===== */
    .card-driver{display:flex;align-items:center;gap:9px;justify-content:center;flex-direction:column;flex-shrink:0;min-width:90px;}
    .drv-av{width:40px;height:40px;border-radius:50%;overflow:hidden;cursor:pointer;flex-shrink:0;}
    .drv-av img{width:100%;height:100%;object-fit:cover;display:block;}
    .drv-av:hover{opacity:.88;}
    .drv-name{font-size:13px;font-weight:700;color:#1a1a1a;}
    .drv-rating{font-size:11.5px;font-weight:600;color:#6b7280;display:flex;align-items:center;gap:2px;}
    .drv-rating .star{color:#F5A623;font-size:11px;}

    /* ===== BADGES ===== */
    .cbadge{font-size:11px;font-weight:600;padding:3px 9px;border-radius:16px;display:flex;align-items:center;gap:4px;width:fit-content;}
    .cbadge.instant{background:#eef3ff;color:#1669F5;}
    .cbadge.maxback{background:#f5f7fa;color:#6b7280;}

    /* ===== CARD RIGHT ===== */
    .card-right{text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:5px;min-width:150px;}
    .price{font-size:22px;font-weight:800;color:#1a1a1a;line-height:1.1;}
    .view-btn{border:1.5px solid #F5A623;background:#fff;color:#1a1a1a;border-radius:8px;padding:7px 14px;font-size:12.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;transition:all .18s;white-space:nowrap;font-family:'Inter',sans-serif;justify-content:center;}
    .view-btn:hover{background:#F5A623;color:#fff;}
    .view-btn.checking{background:#6d6ef5;color:#fff;border-color:#6d6ef5;cursor:not-allowed;}
    .view-btn.available{background:#16a34a;color:#fff;border-color:#16a34a;}
    .view-btn.busy{background:#e53e3e;color:#fff;border-color:#e53e3e;}
    .full-lbl{font-size:13px;font-weight:800;color:#9aa3af;}

    /* ===== VEHICLE SUB LABEL ===== */
    .vehicle-sub-lbl{font-size:11.5px;font-weight:700;color:#6b7280;margin-top:2px;display:flex;align-items:center;gap:4px;justify-content:flex-end;}
    .vehicle-sub-lbl i{color:#F5A623;font-size:11px;}

    /* ===== SEAT COUNTER (desktop) ===== */
    .seat-counter{display:flex;align-items:center;gap:8px;justify-content:flex-end;}
    .seat-btn{width:24px;height:24px;border-radius:50%;border:1.5px solid #e0e4ea;background:#fff;color:#1a1a1a;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;}
    .seat-btn:hover{border-color:#F5A623;color:#F5A623;}
    .seat-count-val{font-size:13px;font-weight:800;color:#1a1a1a;min-width:14px;text-align:center;}
    .per-seat-lbl{font-size:11px;font-weight:700;color:#9aa3af;}

    /* ===== DESKTOP FILLED DOTS (carpool) ===== */
    .desk-filled-bar{display:flex;align-items:center;gap:6px;justify-content:flex-end;margin-top:4px;}
    .desk-filled-dots{display:flex;gap:3px;align-items:center;}
    .desk-filled-dot{width:8px;height:8px;border-radius:50%;background:#e0e4ea;}
    .desk-filled-dot.taken{background:#1669F5;}
    .desk-filled-lbl{font-size:10.5px;font-weight:600;color:#9aa3af;white-space:nowrap;}

    /* ===== CARPOOL POINTS ===== */
    .carpool-points{background:#f5f7fa;border-radius:10px;padding:10px 12px;margin-top:10px;width:100%;}
    .cp-row{display:flex;gap:9px;position:relative;padding-bottom:10px;}
    .cp-row:last-child{padding-bottom:0;}
    .cp-dot-wrap{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:14px;}
    .cp-dot{width:9px;height:9px;border-radius:50%;border:2px solid #1669F5;background:#fff;flex-shrink:0;margin-top:3px;}
    .cp-dot.end{border-color:#e53e3e;background:#e53e3e;}
    .cp-dash{flex:1;width:1.5px;background:repeating-linear-gradient(to bottom,#c5cdd6 0,#c5cdd6 3px,transparent 3px,transparent 6px);margin-top:2px;}
    .cp-lbl{font-size:9.5px;font-weight:700;text-transform:uppercase;color:#9aa3af;letter-spacing:.4px;}
    .cp-val{font-size:12px;font-weight:700;color:#1a1a1a;margin-top:1px;}

    /* ===== SPINNER ===== */
    .btn-spinner{display:inline-block;width:11px;height:11px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;margin-right:5px;}
    @keyframes spin{to{transform:rotate(360deg);}}

    /* ===== FOOTER BANNER ===== */
    .footer-banner{background:#fff;border:1px solid #e8ecf0;border-radius:12px;padding:16px 20px;margin-top:4px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
    .footer-banner-car{width:100px;flex-shrink:0;}
    .footer-banner-car img{width:100%;object-fit:contain;}
    .footer-banner-text .t1{font-size:14px;font-weight:700;color:#1a1a1a;}
    .footer-banner-text .t2{font-size:12px;color:#9aa3af;font-weight:500;margin-top:2px;}
    .footer-trusted{margin-left:auto;display:flex;align-items:center;gap:10px;flex-shrink:0;}
    .footer-trusted .trust-label{font-size:12px;font-weight:600;color:#6b7280;}
    .avatar-stack{display:flex;}
    .avatar-stack .av{width:28px;height:28px;border-radius:50%;border:2px solid #fff;margin-left:-8px;background:linear-gradient(135deg,#2a4570,#1a1a2a);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#F5A623;}
    .avatar-stack .av:first-child{margin-left:0;}
    .trust-count{font-size:12px;font-weight:700;color:#1a1a1a;margin-left:6px;}

    /* ===== OVERLAY / MODALS ===== */
    .overlay{display:none;position:fixed;inset:0;background:rgba(5,12,24,.72);z-index:99999;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(4px);}
    .overlay.show{display:flex;}
    .modal{background:#fff;border-radius:16px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 24px 70px rgba(0,0,0,.22);animation:mPop .22s cubic-bezier(.34,1.56,.64,1);max-height:92vh;overflow-y:auto;scrollbar-width:none;}
    .modal::-webkit-scrollbar{display:none;}
    @keyframes mPop{from{transform:scale(.9) translateY(14px);opacity:0;}to{transform:scale(1) translateY(0);opacity:1;}}
    .modal-head{background:#0A1628;padding:13px 18px;display:flex;align-items:center;justify-content:space-between;}
    .modal-head h4{color:#fff;font-size:14px;font-weight:700;margin:0;display:flex;align-items:center;gap:7px;}
    .modal-head h4 i{color:#F5A623;}
    .modal-close{background:rgba(255,255,255,.12);border:none;color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;cursor:pointer;}
    .modal-close:hover{background:rgba(255,255,255,.22);}
    .modal-body{padding:20px 18px;}

    /* ===== DRIVER PREVIEW ===== */
    .driver-preview{display:flex;align-items:center;gap:10px;background:#f5f7fa;border-radius:10px;padding:11px 13px;margin-bottom:16px;}
    .dp-av{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#0A1628,#2a4570);color:#F5A623;font-size:16px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .dp-name{font-size:13.5px;font-weight:700;color:#1a1a1a;}
    .dp-meta{font-size:12px;color:#6b7280;font-weight:500;}
    .dp-price{margin-left:auto;font-size:17px;font-weight:800;color:#1a1a1a;}

    /* ===== FORM ===== */
    .form-group{margin-bottom:13px;}
    .form-label{display:block;font-size:10.5px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;}
    .form-input{width:100%;height:44px;border:1.5px solid #e0e4ea;border-radius:9px;padding:0 13px;font-size:13.5px;font-weight:500;color:#1a1a1a;outline:none;font-family:'Inter',sans-serif;}
    .form-input:focus{border-color:#ffb800;box-shadow:0 0 0 3px rgba(255,184,0,.12);}
    .modal-btn{width:100%;height:46px;border:none;border-radius:10px;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;font-family:'Inter',sans-serif;margin-top:7px;}
    .btn-blue{background:#F5A623;color:black;}
    .btn-navy{background:#f5a623;color:black;}
    .btn-green{background:#16a34a;color:#fff;}
    .btn-red{background:#e53e3e;color:#fff;}
    .btn-ghost{background:transparent;border:1.5px solid #e0e4ea;color:#9aa3af;height:36px;font-size:12px;margin-top:7px;}

    /* ===== NEW INFO-CARD INLINE ROW DESIGN ===== */
    /* replaces old .info-card / .info-row */
    .info-card{background:#f5f7fa;border-radius:9px;padding:10px 14px;margin:12px 0;}
    .info-inline-row{display:flex;align-items:center;gap:0;flex-wrap:wrap;row-gap:0;}
    .info-chip{display:flex;align-items:center;gap:5px;padding:4px 10px;border-right:1px solid #e0e4ea;white-space:nowrap;}
    .info-chip:last-child{border-right:none;}
    .info-chip i{font-size:12px;color:#F5A623;flex-shrink:0;}
    .info-chip-val{font-size:12.5px;font-weight:700;color:#1a1a1a;}

    /* ===== OLD INFO ROW (kept for compatibility but overridden) ===== */
    .info-row{display:flex;align-items:center;gap:9px;padding:4px 0;}
    .info-row i{color:#ffae27;font-size:13px;flex-shrink:0;}
    .info-lbl{font-size:10px;font-weight:700;text-transform:uppercase;color:#9aa3af;margin-bottom:1px;}
    .info-val{font-size:12.5px;font-weight:700;color:#1a1a1a;}

    /* ===== OTP ===== */
    .otp-title{text-align:center;font-size:13px;font-weight:700;margin-bottom:3px;}
    .otp-row{display:flex;gap:10px;justify-content:center;margin:16px 0;}
    .otp-box{width:54px;height:58px;border:2px solid #ffb8007a;border-radius:10px;text-align:center;font-size:24px;font-weight:800;color:#1a1a1a;outline:none;font-family:'Inter',sans-serif;}
    .otp-box:focus{border-color:#ffb8007a;box-shadow:0 0 0 3px rgba(22,105,245,.12);}
    .otp-box.filled{border-color:#ffb8007a;background:#f0f4f8;}

    /* ===== WAIT MODAL ===== */
    .wait-pulse{width:68px;height:68px;border-radius:50%;background:#1669F5;display:flex;align-items:center;justify-content:center;font-size:26px;color:#fff;margin:0 auto 12px;animation:pulse 2s ease-in-out infinite;}
    @keyframes pulse{0%,100%{transform:scale(1);box-shadow:0 0 0 0 rgba(22,105,245,.4);}50%{transform:scale(1.06);box-shadow:0 0 0 16px rgba(22,105,245,0);}}
    .wait-dots{display:flex;gap:5px;justify-content:center;margin-top:8px;}
    .wait-dot{width:7px;height:7px;border-radius:50%;background:#e0e4ea;animation:dot 1.4s infinite;}
    .wait-dot:nth-child(2){animation-delay:.2s;}
    .wait-dot:nth-child(3){animation-delay:.4s;}
    @keyframes dot{0%,80%,100%{background:#e0e4ea;}40%{background:#1669F5;}}

    /* ===== SUCCESS ===== */
    .success-wrap{text-align:center;padding:6px 0;}
    .success-ic{width:68px;height:68px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;font-size:30px;color:#fff;margin:0 auto 12px;animation:sBounce .38s ease;}
    @keyframes sBounce{0%{transform:scale(0);}70%{transform:scale(1.1);}100%{transform:scale(1);}}
    .success-title{font-size:19px;font-weight:800;color:#16a34a;margin-bottom:5px;}
    .success-sub{font-size:14px;color:#6b7280;margin-bottom:14px;}
    .ref-box{background:#f5f7fa;border-radius:9px;padding:11px 14px;margin:10px 0;text-align:left;}
    .ref-lbl{font-size:12px;color:#9aa3af;font-weight:700;text-transform:uppercase;margin-bottom:3px;}
    .ref-id{font-size:14px;font-weight:800;color:#1a1a1a;font-family:monospace;letter-spacing:1px;}

    /* ===== PAY PAGE ===== */
    .pay-page{display:none;min-height:100vh;background:#f5f6f8;padding:270px 16px 60px;}
    .pay-inner{max-width:540px;margin:0 auto;}
    .pay-back{display:inline-flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:#6b7280;cursor:pointer;margin-bottom:18px;border:none;background:none;font-family:'Inter',sans-serif;}
    .pay-back:hover{color:#1a1a1a;}
    .pay-card{background:#fff;border-radius:14px;border:1px solid #e8ecf0;overflow:hidden;margin-bottom:12px;}
    .pay-total{background:#0A1628;padding:22px;text-align:center;}
    .pay-label{font-size:14px;color:white;text-transform:uppercase;letter-spacing:.7px;font-weight:500;margin-bottom:4px;}
    .pay-amt{font-size:38px;font-weight:800;color:#F5A623;}
    .pay-drv{font-size:16px;color:white;margin-top:3px;}
    .pay-body{padding:18px;}
    .pay-secure{display:flex;align-items:center;gap:5px;font-size:12px;color:#6b7280;margin-bottom:14px;}
    .pay-secure i{color:#16a34a;}
    .pay-methods{display:flex;flex-direction:column;gap:9px;}
    .pay-method{display:flex;align-items:center;gap:11px;border:1.5px solid #e0e4ea;border-radius:10px;padding:13px 15px;cursor:pointer;font-size:13.5px;font-weight:600;color:#1a1a1a;transition:all .16s;background:#fff;}
    .pay-method:hover{border-color:#1669F5;background:#f0f5ff;}
    .pay-method .method-ic{font-size:20px;color:#1669F5;}
    .pay-method .chevron{margin-left:auto;font-size:12px;color:#ccc;}

    /* ===== LIGHTBOX ===== */
    .lb{display:none;position:fixed;inset:0;background:rgba(5,12,24,.88);z-index:9999999;align-items:center;justify-content:center;backdrop-filter:blur(8px);}
    .lb.show{display:flex;}
    .lb-box{background:#fff;border-radius:16px;overflow:hidden;max-width:340px;width:calc(100% - 32px);animation:mPop .22s cubic-bezier(.34,1.56,.64,1);}
    .lb-head{background:#0A1628;padding:11px 16px;display:flex;align-items:center;justify-content:space-between;}
    .lb-head h4{color:#fff;font-size:13.5px;font-weight:700;margin:0;display:flex;align-items:center;gap:7px;}
    .lb-head h4 i{color:#F5A623;}
    .lb-close{background:rgba(255,255,255,.1);border:none;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:15px;}
    .lb-photo{width:100%;height:220px;background:#f5f7fa;display:flex;align-items:center;justify-content:center;overflow:hidden;}
    .lb-photo img{width:100%;height:100%;object-fit:cover;}
    .lb-body{padding:16px 18px;}
    .lb-name{font-size:17px;font-weight:800;color:#1a1a1a;}
    .lb-meta{font-size:13px;margin-top:5px;display:flex;align-items:center;gap:9px;flex-wrap:wrap;font-weight:500;}
    .lb-meta span{display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;}
    .car-ic{color:#f5a623;}
    .ride-ic{color:#16a34a;}
    .exp-ic{color:#1669f5;}
    .rating-chip{background:#16a34a;color:#fff;border-radius:5px;padding:2px 9px;font-size:11.5px;font-weight:700;display:flex;align-items:center;gap:3px;}

    /* ===== CAR MODAL ===== */
    .car-modal{display:none;position:fixed;inset:0;background:rgba(5,12,24,.88);z-index:999999999;align-items:center;justify-content:center;backdrop-filter:blur(8px);padding:16px;}
    .car-modal.show{display:flex;}
    .car-box{background:white;border-radius:16px;overflow:hidden;max-width:500px;width:100%;animation:mPop .22s cubic-bezier(.34,1.56,.64,1);}
    .car-head{padding:11px 16px;display:flex;align-items:center;justify-content:space-between;background:black;}
    .car-head h4{color:white;font-size:15px;font-weight:700;margin:0;display:flex;align-items:center;gap:7px;}
    .car-head h4 i{color:#F5A623;}
    .car-head-close{background:rgba(255,255,255,.1);border:none;color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:15px;}
    .car-main{width:100%;height:240px;background:#fff;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
    .car-main img{max-width:100%;max-height:100%;object-fit:contain;}
    .car-arr{position:absolute;top:50%;transform:translateY(-50%);background:rgba(245,166,35,.9);border:none;color:#1a1a1a;width:32px;height:32px;border-radius:50%;font-size:14px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:5;}
    .car-arr.l{left:10px;}
    .car-arr.r{right:10px;}
    .car-ctr{position:absolute;bottom:8px;right:10px;background:rgba(0,0,0,.55);color:#fff;border-radius:18px;padding:2px 9px;font-size:11px;font-weight:700;}
    .car-thumbs{display:flex;gap:7px;padding:9px 13px;background:#fff;overflow-x:auto;scrollbar-width:none;}
    .car-thumbs::-webkit-scrollbar{display:none;}
    .car-thumb{width:56px;height:40px;border-radius:6px;overflow:hidden;cursor:pointer;border:2px solid transparent;flex-shrink:0;}
    .car-thumb.on{border-color:#F5A623;}
    .car-thumb img{width:100%;height:100%;object-fit:cover;}
    .car-body{padding:10px 16px 16px;background:black;}
    .car-name{color:#fff;font-size:14px;font-weight:800;margin-bottom:7px;}
    .car-feats{display:flex;flex-wrap:wrap;gap:5px;}
    .car-feat{background:rgba(255,255,255,.08);color:white;border-radius:5px;padding:3px 9px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:4px;}
    .car-feat i{color:#F5A623;font-size:11px;}

    /* ===== PAYMENT CONFIRM MODAL ===== */
    .booking-radio-opt{display:flex;align-items:center;gap:7px;border:1.5px solid #e0e4ea;border-radius:10px;padding:13px 15px;cursor:pointer;font-size:13.5px;font-weight:600;color:#1a1a1a;transition:all .16s;background:#fff;margin-bottom:9px;}
    .booking-radio-opt:hover{border-color:#F5A623;background:#fff8e1;}
    .booking-radio-opt.sel{border-color:#F5A623;background:#fff8e1;}
    .booking-radio-opt .bro-radio{width:18px;height:18px;border:2px solid #dde3eb;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .booking-radio-opt.sel .bro-radio{border-color:#F5A623;background:#F5A623;}
    .booking-radio-opt.sel .bro-radio::after{content:'';width:6px;height:6px;background:#fff;border-radius:50%;}
    .booking-radio-opt .bro-ic{font-size:14px;color:#1669F5;}
    .confirm-driver-actions{display:flex;gap:12px;margin-top:16px;}
    .confirm-driver-actions .modal-btn{flex:1;}

    /* ===== FLATPICKR ===== */
    .flatpickr-calendar{border-radius:12px!important;box-shadow:0 8px 36px rgba(0,0,0,.14)!important;border:1px solid #e0e4ea!important;font-family:'Inter',sans-serif!important;}
    .flatpickr-day.selected,.flatpickr-day.selected:hover{background:#F5A623!important;border-color:#F5A623!important;}
    .flatpickr-day.today{border-color:#F5A623!important;}

    /* ===== TIME DROPDOWN ===== */
    .time-dropdown{position:absolute;top:calc(100% + 6px);left:0;width:180px;background:#fff;border:1.5px solid #F5A623;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.12);z-index:9999;max-height:220px;overflow-y:auto;display:none;scrollbar-width:thin;}
    .time-dropdown.open{display:block;}
    .time-dd-item{padding:9px 16px;font-size:13px;font-weight:600;color:#1a1a1a;cursor:pointer;border-bottom:1px solid #f5f7fa;}
    .time-dd-item:last-child{border-bottom:none;}
    .time-dd-item:hover{background:#FFF8E1;color:#F5A623;}
    .time-dd-item.on{background:#FFF3D6;color:#F5A623;font-weight:700;}

    /* ===== MOBILE FILTER ===== */
    .mobile-filter-btn{display:none;align-items:center;gap:6px;background:#ffb800;color:black;border:none;border-radius:8px;padding:6px 12px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;flex-shrink:0;}
    .mobile-filter-btn i{font-size:14px;color:black;}
    .filter-panel-overlay{display:none;position:fixed;inset:0;background:rgba(5,12,24,.6);z-index:9997;backdrop-filter:blur(2px);}
    .filter-panel-overlay.show{display:block;}
    .filter-slide-panel{position:fixed;bottom:0;left:0;right:0;background:#fff;border-radius:20px 20px 0 0;z-index:9998;transform:translateY(100%);transition:transform .3s cubic-bezier(.32,.72,0,1);padding:0 0 env(safe-area-inset-bottom,0);}
    .filter-slide-panel.open{transform:translateY(0);}
    .filter-panel-handle{display:flex;justify-content:center;padding:12px 0 0;}
    .filter-panel-handle::before{content:'';width:40px;height:4px;background:#dde3eb;border-radius:2px;}
    .filter-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px 10px;}
    .filter-panel-head .fp-title{font-size:17px;font-weight:800;color:#1a1a1a;}
    .filter-panel-head .fp-clear{font-size:13px;font-weight:600;color:#6b7280;cursor:pointer;border:none;background:none;font-family:'Inter',sans-serif;}
    .filter-panel-body{padding:0 20px 20px;max-height:70vh;overflow-y:auto;}
    .fp-section-title{font-size:11px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.7px;margin:16px 0 10px;}
    .fp-sort-item{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f5f7fa;cursor:pointer;}
    .fp-sort-item:last-child{border-bottom:none;}
    .fp-sort-left{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:500;color:#1a1a1a;}
    .fp-sort-radio{width:18px;height:18px;border:2px solid #dde3eb;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .fp-sort-radio.on{border-color:#F5A623;background:#F5A623;}
    .fp-sort-radio.on::after{content:'';width:6px;height:6px;background:#fff;border-radius:50%;}
    .fp-sort-ic{font-size:15px;color:#9aa3af;}
    .filter-panel-footer{padding:11px 17px;border-top:1px solid #e8ecf0;display:flex;justify-content:center;}
    .fp-apply-btn{background:#F5A623;color:black;border:none;border-radius:10px;font-size:13px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;gap:7px;padding:9px 12px;}

    /* ===== EMPTY STATE ===== */
    .empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:70px 20px;background:#fff;border-radius:16px;border:1px solid #e8ecf0;gap:12px;}
    .empty-state .es-icon{width:84px;height:84px;border-radius:50%;background:#FFF3D6;display:flex;align-items:center;justify-content:center;font-size:36px;color:#F5A623;margin-bottom:18px;position:relative;animation:pulseGlow 2s ease-in-out infinite;}
    .empty-state .es-icon::before{content:"";position:absolute;inset:-8px;border:2px solid rgba(245,166,35,.4);border-radius:50%;animation:ripple 2s linear infinite;}
    .empty-state .es-icon i{animation:floatSearch 1.5s ease-in-out infinite;}
    @keyframes floatSearch{0%,100%{transform:translateY(0);}50%{transform:translateY(-4px);}}
    @keyframes pulseGlow{0%,100%{box-shadow:0 0 10px rgba(245,166,35,.3);}50%{box-shadow:0 0 30px rgba(245,166,35,.8);}}
    @keyframes ripple{0%{transform:scale(.9);opacity:.8;}100%{transform:scale(1.3);opacity:0;}}
    .empty-state .es-title{font-size:19px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
    .empty-state .es-sub{font-size:13.5px;color:#6c737c;font-weight:500;max-width:320px;line-height:1.4;}

    /* ===== FOOTER ===== */
    .site-footer{text-align:center;padding:18px 12px 30px;font-size:12.5px;color:white;font-weight:500;background:black;}
    .site-footer a{color:#6b7280;text-decoration:none;font-weight:700;}
    .site-footer a:hover{color:#F5A623;}

    /* ===== MOBILE COMPACT BAR ===== */
    .mobile-compact-bar{display:none;align-items:center;gap:10px;padding:10px 12px;background:#fff;border-bottom:1px solid #e8ecf0;position:fixed;top:0;left:0;right:0;z-index:1000;cursor:pointer;}
    .mcb-burger{width:36px;height:36px;border-radius:9px;background:#0A1628;color:white;border:none;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;cursor:pointer;}
    .mcb-info{flex:1;min-width:0;}
    .mcb-route{font-size:13px;font-weight:800;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:flex;align-items:center;gap:5px;}
    .mcb-route i{color:#F5A623;font-size:11px;}
    .mcb-meta{font-size:11px;color:#6b7280;font-weight:600;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .mcb-edit-ic{font-size:16px;color:#F5A623;flex-shrink:0;}
    .mcb-close-row{display:none;justify-content:flex-end;padding:8px 12px 0;}
    .mcb-close-btn{background:#f5f7fa;border:none;width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#6b7280;font-size:15px;cursor:pointer;flex-shrink:0;}

    @media(max-width:900px){
        .layout-grid{grid-template-columns:1fr;}
        .sidebar{position:static;display:none;}
        .mobile-filter-btn{display:flex;}
        body.mobile-collapsed #searchStripWrap{display:none;}
        body.mobile-collapsed .mobile-compact-bar{display:flex;}
        #searchStripWrap.mobile-overlay{position:fixed;top:0;left:0;right:0;z-index:10000;box-shadow:0 10px 34px rgba(0,0,0,.2);max-height:100vh;overflow-y:auto;}
        #searchStripWrap.mobile-overlay .mcb-close-row{display:flex;}
    }

    /* ===== MOBILE CARD ===== */
    @media(max-width:680px){
        #daysDropdownMob{left:auto;right:0;width:130px;max-width:calc(100vw - 20px);z-index:99999;}
        #timeDropdownMob{left:auto;right:0;width:130px;max-width:calc(100vw - 20px);z-index:99999;}
        .date-chip{flex:0 0 92px;width:92px;min-width:92px;}
        .date-chip .dc-day{font-size:10px;font-weight:600;white-space:nowrap;}
        .date-chip .dc-price{font-size:11px;white-space:nowrap;}
        .pay-page{padding:375px 16px 60px;}
        .search-strip{padding:10px 12px;}
        .trip-tabs{padding:0;margin-bottom:8px;gap:6px;flex-wrap:wrap;}
        .trip-tab{padding:6px 12px;font-size:11.5px;}
        .logo-img{height:40px;}
        .search-strip-inner{flex-direction:column;height:auto;border-radius:10px;overflow:visible;gap:0;}
        .city-wrap{flex:none;width:100%;height:52px;border-right:none;border-bottom:1px solid #e8ecf0;}
        .mob-arrow-row{display:flex;align-items:center;justify-content:center;padding:0;background:#fff;position:relative;height:0;z-index:3;}
        .mob-arrow-circle{width:28px;height:28px;border-radius:50%;border:1.5px solid #e0e4ea;background:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;color:#F5A623;cursor:pointer;position:absolute;right:14px;top:-14px;z-index:4;box-shadow:0 2px 6px rgba(0,0,0,.08);}
        .mob-arrow-circle.round-trip-icon{flex-direction:column;gap:0;font-size:8px;line-height:1;}
        .mob-arrow-circle.round-trip-icon i{font-size:8px;line-height:1;}
        .mob-arrow-circle.round-trip-icon i:first-child{margin-bottom:1px;color:#1669F5;}
        .mob-arrow-circle.round-trip-icon i:last-child{color:#e53e3e;}
        .mob-datetime-row{display:flex;align-items:stretch;width:100%;border-top:1px solid #e8ecf0;background:#fff;}
        .mob-datetime-row .sf{flex:1;border-right:1px solid #e8ecf0;height:46px;padding:0 10px;border-bottom:none;}
        .mob-datetime-row .sf:last-child{border-right:none;}
        .mob-datetime-row .sf-lbl{font-size:8.5px;}
        .mob-datetime-row .sf-val{font-size:12px;}
        .mob-datetime-row .sf-val i{font-size:11px;}
        .search-go-btn{border-radius:0;width:100%;justify-content:center;height:44px;font-size:14px;}
        .swap-circle{display:none;}
        .city-dropdown{width:calc(100vw - 24px);max-width:320px;}
        .date-strip-wrap{padding:8px 12px;}
        .date-chip{min-width:80px;padding:7px 9px;}
        .main-wrap{padding:16px 12px 50px;}
        .results-header{font-size:13px;flex-wrap:wrap;gap:4px;}
        .rides-count{margin-left:0;flex:1 1 100%;margin-top:2px;font-size:13px;}
        .results-header-row{display:flex;align-items:center;justify-content:space-between;width:100%;margin-bottom:10px;}

        /* MOBILE CARD */
        .ride-card{flex-direction:column;padding:0;gap:0;align-items:stretch;border-radius:14px;overflow:hidden;margin-bottom:12px;}
        .ride-type-badge{position:absolute;top:0;left:0;transform:none;border-radius:0 0 8px 0;font-size:10px;padding:4px 10px;}
        .mob-card-header{display:flex;align-items:flex-start;justify-content:space-between;padding:14px 14px 12px;padding-top:28px;background:#fff;gap:10px;}
        .mob-card-header-left{display:flex;flex-direction:column;gap:4px;flex:1;min-width:0;}
        .mob-price-row{display:flex;align-items:baseline;gap:4px;}
        .mob-price-big{font-size:22px;font-weight:800;color:#1a1a1a;line-height:1;}
        .mob-price-seat{font-size:12px;font-weight:700;color:#6b7280;}
        .mob-time-row{display:flex;align-items:center;gap:6px;margin-top:4px;}
        .mob-dep-time{font-size:15px;font-weight:800;color:#1a1a1a;}
        .mob-dur-pill{background:#FFF3D6;color:#c97a00;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:20px;display:flex;align-items:center;gap:3px;}
        .mob-arr-time{font-size:15px;font-weight:800;color:#1a1a1a;}
        .mob-arr-next{font-size:10px;color:#F5A623;font-weight:700;}
        .mob-seats-left-badge{background:#eef3ff;color:#1669F5;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;white-space:nowrap;display:flex;align-items:center;gap:4px;flex-shrink:0;align-self:flex-start;margin-top:2px;}
        .mob-route-timeline{background:#f9fafc;border-top:1px solid #f0f2f5;border-bottom:1px solid #f0f2f5;padding:12px 14px;}
        .mob-tl-row{display:flex;align-items:flex-start;gap:10px;padding-bottom:8px;position:relative;}
        .mob-tl-row:last-child{padding-bottom:0;}
        .mob-tl-icon-wrap{display:flex;flex-direction:column;align-items:center;flex-shrink:0;width:20px;margin-top:1px;}
        .mob-tl-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
        .mob-tl-dot.driver-start{background:#0A1628;border:2px solid #0A1628;}
        .mob-tl-dot.from-dot{background:#1669F5;border:2px solid #1669F5;}
        .mob-tl-dot.to-dot{background:#8b5cf6;border:2px solid #8b5cf6;}
        .mob-tl-dot.driver-end{background:#e53e3e;border:2px solid #e53e3e;}
        .mob-tl-dot.private-from{background:#1669F5;border:2px solid #1669F5;}
        .mob-tl-dot.private-to{background:#e53e3e;border:2px solid #e53e3e;}
        .mob-tl-line{width:2px;height:16px;background:repeating-linear-gradient(to bottom,#d0d5de 0,#d0d5de 3px,transparent 3px,transparent 6px);margin-top:3px;flex-shrink:0;}
        .mob-tl-text{flex:1;min-width:0;}
        .mob-tl-label{font-size:9.5px;font-weight:700;text-transform:uppercase;color:#9aa3af;letter-spacing:.5px;line-height:1;}
        .mob-tl-val{font-size:13px;font-weight:700;color:#1a1a1a;margin-top:2px;line-height:1.2;}
        .mob-tl-sub{font-size:11px;color:#9aa3af;font-weight:500;margin-top:1px;}
        .mob-card-footer{display:flex;align-items:center;padding:10px 14px;gap:10px;background:#fff;}
        .mob-card-footer .mob-car-thumb{width:56px;height:38px;object-fit:contain;flex-shrink:0;cursor:pointer;border-radius:6px;}
        .mob-driver-info{display:flex;align-items:center;gap:8px;flex:1;min-width:0;cursor:pointer;}
        .mob-driver-av{width:32px;height:32px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid #f0f2f5;}
        .mob-driver-av img{width:100%;height:100%;object-fit:cover;display:block;}
        .mob-driver-name{font-size:12px;font-weight:700;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .mob-driver-rating{font-size:11px;font-weight:600;color:#6b7280;display:flex;align-items:center;gap:2px;margin-top:1px;}
        .mob-driver-rating .star{color:#F5A623;font-size:10px;}
        .mob-seat-counter{display:flex;align-items:center;gap:6px;flex-shrink:0;}
        .mob-seat-btn{width:26px;height:26px;border-radius:50%;border:1.5px solid #e0e4ea;background:#fff;color:#1a1a1a;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;cursor:pointer;}
        .mob-seat-btn:hover{border-color:#F5A623;color:#F5A623;}
        .mob-seat-val{font-size:13px;font-weight:800;color:#1a1a1a;min-width:16px;text-align:center;}
        .mob-book-bar{padding:0 14px 12px;background:#fff;}
        .mob-book-btn{width:100%;height:42px;background:#fff;border:1.5px solid #F5A623;color:#1a1a1a;border-radius:10px;font-size:13px;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-family:'Inter',sans-serif;transition:all .18s;}
        .mob-book-btn:hover{background:#F5A623;color:#fff;}
        .mob-book-btn.checking{background:#6d6ef5;color:#fff;border-color:#6d6ef5;cursor:not-allowed;}
        .mob-book-btn.available{background:#16a34a;color:#fff;border-color:#16a34a;}
        .mob-book-btn.busy{background:#e53e3e;color:#fff;border-color:#e53e3e;}
        .mob-filled-bar{padding:0 14px 10px;background:#fff;}
        .mob-filled-text{font-size:11px;font-weight:600;color:#9aa3af;display:flex;align-items:center;gap:5px;}
        .mob-filled-dots{display:flex;gap:3px;align-items:center;}
        .mob-filled-dot{width:8px;height:8px;border-radius:50%;background:#e0e4ea;}
        .mob-filled-dot.taken{background:#1669F5;}
        .footer-banner{flex-direction:column;text-align:center;}
        .footer-trusted{margin-left:0;}
        /* info-card inline → 2 rows on mobile */
        .info-inline-row{gap:0;}
        .info-chip{border-right:none;border-bottom:1px solid #e8ecf0;padding:6px 10px;}
        .info-chip:last-child{border-bottom:none;}
    }
    @media(max-width:420px){
        .trip-tab{padding:6px 10px;font-size:11px;}
    }
    </style>
</head>
<body>

<div class="search-strip" id="searchStripWrap">
    <div class="mcb-close-row">
        <button class="mcb-close-btn" id="mobileCloseSearchBtn"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="trip-tabs">
        <a href="#" class="logo">
            <img src="/goride/img/logo-dark.png" alt="GoRide Logo" class="logo-img">
        </a>
        <div style="display:flex;gap:8px;">
            <button class="trip-tab active" id="oneWayTab"><i class="bi bi-arrow-right"></i> One Way</button>
            <button class="trip-tab" id="roundTab"><i class="bi bi-arrow-left-right"></i> Round Trip</button>
        </div>
        <div class="welcome-user-bar" id="welcomeUserBar" style="display:none;">
            <div class="wub-av" id="wubAv"><i class="bi bi-person-fill"></i></div>
            <div class="wub-name">Welcome, <span id="wubName">Guest</span></div>
        </div>
    </div>

    <div class="search-strip-inner" id="searchBar">
        <div class="city-wrap" id="fromWrap">
            <div class="city-inner">
                <div class="city-lbl">FROM</div>
                <div class="city-display">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span class="city-name" id="fromDisplay">Chennai, Tamil Nadu, India</span>
                </div>
            </div>
            <input type="text" class="city-text-input" id="fromInput" placeholder="Search city..." autocomplete="off">
            <div class="city-dropdown" id="fromDropdown"></div>
        </div>
        <div class="mob-arrow-row" id="mobArrowRow" style="display:none;">
            <div class="mob-arrow-circle" id="mobSwapBtn"><i class="bi bi-arrow-down-up"></i></div>
        </div>
        <div class="swap-circle" id="swapBtn"><i class="bi bi-arrow-left-right"></i></div>
        <div class="city-wrap" id="toWrap">
            <div class="city-inner">
                <div class="city-lbl">TO</div>
                <div class="city-display">
                    <i class="bi bi-geo-alt-fill" style="color:#e53e3e;"></i>
                    <span class="city-name" id="toDisplay">Madurai, Tamil Nadu, India</span>
                </div>
            </div>
            <input type="text" class="city-text-input" id="toInput" placeholder="Search city..." autocomplete="off">
            <div class="city-dropdown" id="toDropdown"></div>
        </div>

        <!-- MOBILE datetime row -->
        <div class="mob-datetime-row" id="mobDatetimeRow" style="display:none;">
            <div class="sf sf-date" id="dateFieldMob" style="position:relative;">
                <div class="sf-lbl">DATE</div>
                <div class="sf-val">
                    <i class="bi bi-calendar3"></i>
                    <span id="dateDisplayMob">17 Jun</span>
                    <i class="bi bi-chevron-down chevdown"></i>
                </div>
                <input type="text" id="datePickerMob" style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;border:none;outline:none;" readonly>
            </div>
            <div class="sf sf-time" id="timeFieldMob" style="position:relative;cursor:pointer;" tabindex="0">
                <div class="sf-lbl">TIME</div>
                <div class="sf-val">
                    <i class="bi bi-clock"></i>
                    <span id="timeDisplayMob">12:00</span>
                    <i class="bi bi-chevron-down chevdown"></i>
                </div>
                <div class="time-dropdown" id="timeDropdownMob"></div>
            </div>
            <div class="sf sf-return" id="daysFieldMob" style="display:none;position:relative;cursor:pointer;">
                <div class="sf-lbl">DAYS</div>
                <div class="sf-val">
                    <i class="bi bi-moon-stars"></i>
                    <span id="daysDisplayMob">1 Day</span>
                    <i class="bi bi-chevron-down chevdown"></i>
                </div>
                <div class="time-dropdown" id="daysDropdownMob"></div>
            </div>
        </div>

        <!-- DESKTOP date/time fields -->
        <div class="sf sf-date" id="dateField" style="position:relative;">
            <div class="sf-lbl">DATE</div>
            <div class="sf-val">
                <i class="bi bi-calendar3"></i>
                <span id="dateDisplay">17 Jun</span>
                <i class="bi bi-chevron-down chevdown"></i>
            </div>
            <input type="text" id="datePicker" style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;border:none;outline:none;" readonly>
        </div>
        <div class="sf sf-time" id="timeField" style="position:relative;cursor:pointer;" tabindex="0">
            <div class="sf-lbl">TIME</div>
            <div class="sf-val">
                <i class="bi bi-clock"></i>
                <span id="timeDisplay">12:00</span>
                <i class="bi bi-chevron-down chevdown"></i>
            </div>
            <div class="time-dropdown" id="timeDropdown"></div>
        </div>
        <!-- ROUND TRIP: NO. OF DAYS field (desktop only) -->
        <div class="sf sf-return" id="daysField" style="display:none;position:relative;cursor:pointer;" tabindex="0">
            <div class="sf-lbl">NO. OF DAYS</div>
            <div class="sf-val">
                <i class="bi bi-moon-stars"></i>
                <span id="daysDisplay">1 Day</span>
                <i class="bi bi-chevron-down chevdown"></i>
            </div>
            <div class="time-dropdown" id="daysDropdown"></div>
        </div>
        <button class="search-go-btn" id="searchGoBtn"><i class="bi bi-search"></i> Search</button>
    </div>
</div>

<div class="mobile-compact-bar" id="mobileCompactBar">
    <button class="mcb-burger" id="mcbBurgerBtn"><i class="bi bi-list"></i></button>
    <div class="mcb-info">
        <div class="mcb-route"><i class="bi bi-geo-alt-fill"></i><span id="mcbRoute">Chennai → Madurai</span></div>
        <div class="mcb-meta" id="mcbMeta">17 Jun · 12:00</div>
    </div>
    <i class="bi bi-pencil-fill mcb-edit-ic"></i>
</div>

<div class="date-strip-wrap" id="dateStripWrap">
    <div class="date-strip-inner">
        <div class="date-strip-scroll" id="dateStripScroll"></div>
    </div>
</div>

<div class="filter-panel-overlay" id="filterPanelOverlay"></div>
<div class="filter-slide-panel" id="filterSlidePanel">
    <div class="filter-panel-handle"></div>
    <div class="filter-panel-head">
        <span class="fp-title">Filter</span>
        <button class="fp-clear" id="fpClearBtn">Clear all</button>
    </div>
    <div class="filter-panel-body">
        <div class="fp-section-title">Sort by</div>
        <div class="fp-sort-item" data-sort="earliest"><div class="fp-sort-left"><div class="fp-sort-radio on"></div>Earliest departure</div><i class="bi bi-clock fp-sort-ic" style="color:#F5A623;"></i></div>
        <div class="fp-sort-item" data-sort="price"><div class="fp-sort-left"><div class="fp-sort-radio"></div>Lowest price</div><i class="bi bi-currency-rupee fp-sort-ic" style="color:#16a34a;"></i></div>
        <div class="fp-sort-item" data-sort="dep"><div class="fp-sort-left"><div class="fp-sort-radio"></div>Close to departure point</div><i class="bi bi-person-walking fp-sort-ic" style="color:#1669F5;"></i></div>
        <div class="fp-sort-item" data-sort="arr"><div class="fp-sort-left"><div class="fp-sort-radio"></div>Close to arrival point</div><i class="bi bi-person-walking fp-sort-ic" style="color:#8b5cf6;"></i></div>
        <div class="fp-sort-item" data-sort="short"><div class="fp-sort-left"><div class="fp-sort-radio"></div>Shortest ride</div><i class="bi bi-hourglass fp-sort-ic" style="color:#e53e3e;"></i></div>
    </div>
    <div class="filter-panel-footer">
        <button class="fp-apply-btn" id="fpApplyBtn"><i class="bi bi-funnel-fill"></i> Apply Filter</button>
    </div>
</div>

<div class="main-wrap" id="emptyStateWrap">
    <div class="empty-state">
        <div class="es-icon"><i class="bi bi-search"></i></div>
        <div class="es-title">Find your perfect ride</div>
        <div class="es-sub">Enter your pickup &amp; drop cities, pick a date and hit search to see available drivers and carpool rides for your route.</div>
    </div>
</div>

<div class="main-wrap" id="mainWrap" style="display:none;">
    <div class="layout-grid">
        <div class="sidebar">
            <div class="map-box">
                <svg viewBox="0 0 260 200" xmlns="http://www.w3.org/2000/svg">
                    <rect width="260" height="200" fill="#c8dbe8"/>
                    <rect x="0" y="70" width="260" height="7" fill="#b2ccd8" opacity=".6"/>
                    <rect x="0" y="130" width="260" height="5" fill="#b2ccd8" opacity=".5"/>
                    <rect x="80" y="0" width="6" height="200" fill="#b2ccd8" opacity=".55"/>
                    <rect x="180" y="0" width="4" height="200" fill="#b2ccd8" opacity=".4"/>
                    <rect x="24" y="28" width="44" height="26" rx="4" fill="#b8ccd8"/>
                    <rect x="140" y="90" width="54" height="36" rx="4" fill="#b8ccd8"/>
                    <circle cx="88" cy="74" r="8" fill="#1669F5" opacity=".92"/>
                    <circle cx="184" cy="134" r="8" fill="#e53e3e" opacity=".92"/>
                    <line x1="88" y1="74" x2="184" y2="134" stroke="#1669F5" stroke-width="2" stroke-dasharray="6,4" opacity=".65"/>
                </svg>
                <button class="map-btn"><i class="bi bi-geo-alt-fill"></i> Show on map</button>
            </div>
            <div class="sort-box">
                <div class="sort-head">
                    <span class="sort-title">Sort by</span>
                    <span class="sort-clear" id="clearSortBtn">Clear all</span>
                </div>
                <div class="sort-item" data-sort="earliest"><div class="sort-left"><div class="sort-radio on"></div>Earliest departure</div><i class="bi bi-clock sort-ic" style="color:#F5A623;"></i></div>
                <div class="sort-item" data-sort="price"><div class="sort-left"><div class="sort-radio"></div>Lowest price</div><i class="bi bi-currency-rupee sort-ic" style="color:#16a34a;"></i></div>
                <div class="sort-item" data-sort="dep"><div class="sort-left"><div class="sort-radio"></div>Close to departure point</div><i class="bi bi-person-walking sort-ic" style="color:#1669F5;"></i></div>
                <div class="sort-item" data-sort="arr"><div class="sort-left"><div class="sort-radio"></div>Close to arrival point</div><i class="bi bi-person-walking sort-ic" style="color:#8b5cf6;"></i></div>
                <div class="sort-item" data-sort="short"><div class="sort-left"><div class="sort-radio"></div>Shortest ride</div><i class="bi bi-hourglass sort-ic" style="color:#e53e3e;"></i></div>
                <div class="divider-line"></div>
                <div class="trust-row"><div class="trust-icon" style="background:#eaf7ee;"><i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:14px;"></i></div><div class="trust-text"><div class="t1">Verified Drivers</div><div class="t2">Safe &amp; trusted</div></div></div>
                <div class="trust-row"><div class="trust-icon" style="background:#eef3ff;"><i class="bi bi-tag-fill" style="color:#1669F5;font-size:14px;"></i></div><div class="trust-text"><div class="t1">No Hidden Charges</div><div class="t2">Transparent pricing</div></div></div>
                <div class="trust-row"><div class="trust-icon" style="background:#f5f7fa;"><i class="bi bi-headset" style="color:#555;font-size:14px;"></i></div><div class="trust-text"><div class="t1">24/7 Support</div><div class="t2">We are here to help</div></div></div>
                <div class="trust-row"><div class="trust-icon" style="background:#fff0f0;"><i class="bi bi-x-circle-fill" style="color:#e53e3e;font-size:14px;"></i></div><div class="trust-text"><div class="t1">Free Cancellation</div><div class="t2">Cancel anytime</div></div></div>
            </div>
        </div>
        <div id="mainContent">
            <div class="results-header">
                <div class="results-header-row">
                    <div>
                        <strong id="resDateLbl">&nbsp;17 Jun</strong>&nbsp;
                        <strong id="resFromLbl">Chennai</strong> <span style="margin:0 4px;">→</span> <strong id="resToLbl">Madurai</strong>
                        <span class="rides-count" id="rideCount" style="display:inline;margin-left:8px;font-size:13px;">8 rides available</span>
                    </div>
                    <button class="mobile-filter-btn" id="mobileFilterBtn"><i class="bi bi-funnel-fill"></i> Filter</button>
                </div>
            </div>
            <div id="rideList"></div>
        </div>
    </div>
</div>

<div class="pay-page" id="payPage">
    <div class="pay-inner">
        <button class="pay-back" id="payBackBtn"><i class="bi bi-arrow-left"></i> Back to results</button>
        <div class="pay-card">
            <div class="pay-total">
                <div class="pay-label">Total Fare</div>
                <div class="pay-amt" id="payAmt">₹2,800</div>
                <div class="pay-drv" id="payDrv">Driver: –</div>
            </div>
            <div class="pay-body">
                <div class="info-card" style="margin-top:0;margin-bottom:14px;">
                    <div class="info-inline-row" id="payInfoRow"></div>
                </div>
                <div class="pay-secure"><i class="bi bi-lock-fill"></i> Secure payment — toll &amp; parking included</div>
                <div class="pay-methods">
                    <div class="pay-method pay-method-click"><i class="bi bi-credit-card method-ic"></i><span>Credit / Debit Card</span><i class="bi bi-chevron-right chevron"></i></div>
                    <div class="pay-method pay-method-click"><i class="bi bi-phone-fill method-ic" style="color:#4f46e5;"></i><span>UPI / Google Pay / PhonePe</span><i class="bi bi-chevron-right chevron"></i></div>
                    <div class="pay-method pay-method-click"><i class="bi bi-cash-coin method-ic" style="color:#16a34a;"></i><span>Cash to Driver</span><i class="bi bi-chevron-right chevron"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="pay-page" id="confirmedPage">
    <div class="pay-inner">
        <div class="pay-card">
            <div class="pay-body" style="padding:28px 18px;">
                <div class="success-wrap">
                    <div class="success-ic"><i class="bi bi-check-lg"></i></div>
                    <div class="success-title">Booking Confirmed!</div>
                    <div class="success-sub">Your cab is booked. Driver will contact you shortly.</div>
                    <div class="ref-box"><div class="ref-lbl">Booking Reference</div><div class="ref-id" id="confRef">GRC-000000</div></div>
                    <div class="info-card" style="text-align:left;width:100%;">
                        <div class="info-inline-row" id="confInfoRow"></div>
                    </div>
                    <button class="modal-btn btn-navy" style="margin-top:14px;" id="backHomeBtn"><i class="bi bi-house-fill"></i> Back to Home</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CONTACT MODAL -->
<div class="overlay" id="contactModal">
    <div class="modal">
        <div class="modal-head"><h4><i class="bi bi-person-fill"></i> Verify to Continue</h4><button class="modal-close" id="closeContactModal">&times;</button></div>
        <div class="modal-body">
            <div class="driver-preview">
                <div class="dp-av" id="ctAv">R</div>
                <div><div class="dp-name" id="ctName2">–</div><div class="dp-meta" id="ctMeta">–</div></div>
                <div class="dp-price" id="ctPrice">–</div>
            </div>
            <!-- NEW INLINE INFO ROW -->
            <div class="info-card" style="margin-top:0;margin-bottom:14px;">
                <div class="info-inline-row" id="ctInfoRow"></div>
            </div>
            <div class="form-group"><label class="form-label">Full Name</label><input type="text" class="form-input" id="ctName" placeholder="Your full name"></div>
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <div style="display:flex;gap:7px;">
                    <input type="text" class="form-input" value="+91" readonly style="width:60px;flex-shrink:0;text-align:center;background:#f5f7fa;color:#6b7280;">
                    <input type="tel" class="form-input" id="ctPhone" placeholder="98765 43210" style="flex:1;">
                </div>
            </div>
            <button class="modal-btn btn-blue" id="sendOtpBtn"><i class="bi bi-shield-lock-fill"></i> Send OTP to Verify</button>
            <p style="text-align:center;font-size:11.5px;margin-top:9px;color:#9aa3af;font-weight:600;"><i class="bi bi-lock-fill" style="color:#16a34a;"></i> Your details are encrypted &amp; secure</p>
        </div>
    </div>
</div>

<!-- OTP MODAL -->
<div class="overlay" id="otpModal">
    <div class="modal">
        <div class="modal-head"><h4><i class="bi bi-shield-lock-fill"></i> OTP Verification</h4><button class="modal-close" id="closeOtpModal">&times;</button></div>
        <div class="modal-body">
            <p class="otp-title">OTP sent to <strong id="otpPhone">+91 XXXXX</strong></p>
            <p style="text-align:center;font-size:12.5px;color:#9aa3af;font-weight:600;margin-bottom:0;">Enter the 4-digit OTP</p>
            <div class="otp-row">
                <input type="tel" maxlength="1" class="otp-box" id="o1" inputmode="numeric">
                <input type="tel" maxlength="1" class="otp-box" id="o2" inputmode="numeric">
                <input type="tel" maxlength="1" class="otp-box" id="o3" inputmode="numeric">
                <input type="tel" maxlength="1" class="otp-box" id="o4" inputmode="numeric">
            </div>
            <button class="modal-btn btn-blue" id="verifyOtpBtn"><i class="bi bi-check2-shield"></i> Verify OTP</button>
            <p style="text-align:center;font-size:12.5px;margin-top:9px;font-weight:600;">Didn't receive? <a href="#" id="resendLink" style="color:#1a1a1a;font-weight:700;">Resend OTP</a></p>
        </div>
    </div>
</div>

<!-- WAIT MODAL -->
<div class="overlay" id="waitModal">
    <div class="modal">
        <div class="modal-head"><h4><i class="bi bi-hourglass-split"></i> Waiting for Driver</h4><button class="modal-close" id="closeWaitModal">&times;</button></div>
        <div class="modal-body" style="text-align:center;">
            <div class="wait-pulse"><i class="bi bi-car-front-fill"></i></div>
            <div style="font-size:15px;font-weight:800;color:#1a1a1a;margin-bottom:5px;">Checking Availability…</div>
            <div style="font-size:13px;color:#9aa3af;font-weight:500;">Waiting for <strong id="waitDrv">driver</strong> to confirm.</div>
            <div class="wait-dots"><div class="wait-dot"></div><div class="wait-dot"></div><div class="wait-dot"></div></div>
            <div class="info-card" style="text-align:left;margin-top:14px;">
                <div class="info-inline-row" id="waitInfoRow"></div>
            </div>
            <button class="modal-btn btn-green" id="acceptedBtn" style="display:none;"><i class="bi bi-check-circle-fill"></i> Driver Accepted! Make Payment</button>
            <button class="modal-btn btn-red" id="busyBtn" style="display:none;"><i class="bi bi-x-circle-fill"></i> Driver is Busy — Try Another</button>
            <button class="modal-btn btn-ghost" id="cancelWaitBtn"><i class="bi bi-x-circle"></i> Cancel Request</button>
        </div>
    </div>
</div>

<!-- DRIVER LIGHTBOX -->
<div class="lb" id="drvLightbox">
    <div class="lb-box">
        <div class="lb-head"><h4><i class="bi bi-person-fill"></i> <span id="lbTitle">Driver Profile</span></h4><button class="lb-close" id="closeLb">&times;</button></div>
        <div class="lb-photo"><img id="lbPhoto" src="" alt="Driver"></div>
        <div class="lb-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                <div class="lb-name" id="lbName">–</div>
                <span class="rating-chip"><i class="bi bi-star-fill"></i> <span id="lbRating">–</span></span>
            </div>
            <div class="lb-meta">
                <span><i class="bi bi-car-front-fill car-ic"></i><span id="lbVehicle">–</span></span>
                <span><i class="bi bi-person-fill ride-ic"></i><span id="lbRides">–</span> rides</span>
                <span><i class="bi bi-briefcase-fill exp-ic"></i><span id="lbExp">–</span> exp</span>
            </div>
        </div>
    </div>
</div>

<!-- CAR MODAL -->
<div class="car-modal" id="carModal">
    <div class="car-box">
        <div class="car-head"><h4><i class="bi bi-car-front-fill"></i> <span id="carTitle">Vehicle Gallery</span></h4><button class="car-head-close" id="closeCarModal">&times;</button></div>
        <div class="car-main">
            <img id="carBig" src="" alt="Car">
            <button class="car-arr l" id="carArrL"><i class="bi bi-chevron-left"></i></button>
            <button class="car-arr r" id="carArrR"><i class="bi bi-chevron-right"></i></button>
            <div class="car-ctr" id="carCtr">1 / 3</div>
        </div>
        <div class="car-thumbs" id="carThumbs"></div>
        <div class="car-body"><div class="car-name" id="carName">–</div><div class="car-feats" id="carFeats"></div></div>
    </div>
</div>

<!-- PAYMENT CONFIRM MODAL -->
<div class="overlay" id="paymentConfirmModal">
    <div class="modal">
        <div class="modal-head">
            <h4><i class="bi bi-credit-card"></i><span id="bookingModalTitle">Choose Payment</span></h4>
            <button class="modal-close" id="closeBookingModal">&times;</button>
        </div>
        <div class="modal-body" id="bookingModalBody">
            <div class="info-card" style="margin-top:0;">
                <div class="info-inline-row" id="bmInfoRow"></div>
            </div>
            <div id="bookingPaymentChoice">
                <p style="font-size:12px;font-weight:700;color:#9aa3af;text-transform:uppercase;letter-spacing:.5px;margin:14px 0 9px;">Select Payment Method</p>
                <div style="display:flex;justify-content:space-around;">
                    <div style="flex:1;padding:0 4px;"><div class="booking-radio-opt" data-method="Online Payment"><div class="bro-radio"></div><i class="bi bi-credit-card bro-ic"></i><span>Online Payment</span></div></div>
                    <div style="flex:1;padding:0 4px;"><div class="booking-radio-opt" data-method="Cash to Driver"><div class="bro-radio"></div><i class="bi bi-cash-coin bro-ic" style="color:#16a34a;"></i><span>Cash to Driver</span></div></div>
                </div>
                <button class="modal-btn btn-green" id="confirmBookingBtn"><i class="bi bi-check-circle-fill"></i> Confirm Booking</button>
            </div>
            <div id="bookingConfirmedView" style="display:none;text-align:center;">
                <div class="success-ic" style="margin-top:10px;"><i class="bi bi-check-lg"></i></div>
                <div class="success-title">Booking Confirmed!</div>
                <div class="success-sub">Your ride is booked. Driver will contact you shortly.</div>
                <div class="ref-box"><div class="ref-lbl">Booking Reference</div><div class="ref-id" id="bmConfRef">GRC-000000</div></div>
                <div class="info-card" style="text-align:left;">
                    <div class="info-inline-row" id="bmConfInfoRow"></div>
                </div>
                <button class="modal-btn btn-navy" id="bmDoneBtn"><i class="bi bi-house-fill"></i> Done</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(function(){

var S={
    from:'Chennai', fromFull:'Chennai, Tamil Nadu, India',
    to:'Madurai', toFull:'Madurai, Tamil Nadu, India',
    date:'', dateLabel:'', dateObj:null,
    time:'12:00', days:1, tripType:'One Way',
    distance:'345 km',
    selectedDriver:null, selectedFare:0, selectedSeats:1, selectedMethod:null, btnMap:{},
    isVerified:false, userName:'', userPhone:''
};

var MONTHS=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var DOW=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

function fmtDateLabel(d){ return d.getDate()+' '+MONTHS[d.getMonth()]; }
function isoDate(d){ return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
function isMobile(){ return window.innerWidth<=680; }

/* ── DEFAULT DATE ── */
(function initDefaultDate(){
    var today=new Date();
    S.dateObj=today; S.date=isoDate(today); S.dateLabel=fmtDateLabel(today);
    $('#dateDisplay,#dateDisplayMob').text(S.dateLabel);
    $('#datePicker,#datePickerMob').val(S.date);
})();

/* ── MOBILE LAYOUT ── */
function applyMobileLayout(){
    if(isMobile()){
        $('#mobArrowRow').css('display','flex');
        $('#mobDatetimeRow').css('display','flex');
        $('#dateField,#timeField,#daysField').hide();
        $('#swapBtn').hide();
    } else {
        $('#mobArrowRow').hide();
        $('#mobDatetimeRow').hide();
        $('#dateField,#timeField').show();
        if(S.tripType==='Round Trip') $('#daysField').show();
        $('#swapBtn').show();
    }
}
applyMobileLayout();
$(window).on('resize',applyMobileLayout);

/* ── TRIP ICONS ── */
function updateTripIcons(){
    var isRound=S.tripType==='Round Trip';
    if(isRound){
        $('#swapBtn').addClass('round-trip-icon').html('<i class="bi bi-arrow-right"></i><i class="bi bi-arrow-left"></i>');
        $('#mobSwapBtn').addClass('round-trip-icon').html('<i class="bi bi-arrow-right"></i><i class="bi bi-arrow-left"></i>');
    } else {
        $('#swapBtn').removeClass('round-trip-icon').html('<i class="bi bi-arrow-left-right"></i>');
        $('#mobSwapBtn').removeClass('round-trip-icon').html('<i class="bi bi-arrow-down-up"></i>');
    }
}
updateTripIcons();

/* ── CITIES ── */
var CITIES=[
    {name:'Chennai',full:'Chennai, Tamil Nadu, India'},
    {name:'Madurai',full:'Madurai, Tamil Nadu, India'},
    {name:'Coimbatore',full:'Coimbatore, Tamil Nadu, India'},
    {name:'Trichy',full:'Trichy, Tamil Nadu, India'},
    {name:'Salem',full:'Salem, Tamil Nadu, India'},
    {name:'Tirunelveli',full:'Tirunelveli, Tamil Nadu, India'},
    {name:'Vellore',full:'Vellore, Tamil Nadu, India'},
    {name:'Erode',full:'Erode, Tamil Nadu, India'},
    {name:'Ooty',full:'Ooty, Tamil Nadu, India'},
    {name:'Pondicherry',full:'Pondicherry, India'},
    {name:'Bangalore',full:'Bangalore, Karnataka, India'},
    {name:'Hyderabad',full:'Hyderabad, Telangana, India'},
    {name:'Mumbai',full:'Mumbai, Maharashtra, India'},
    {name:'Delhi',full:'Delhi, India'},
    {name:'Kochi',full:'Kochi, Kerala, India'},
    {name:'Mysore',full:'Mysore, Karnataka, India'},
    {name:'Thanjavur',full:'Thanjavur, Tamil Nadu, India'},
    {name:'Kodaikanal',full:'Kodaikanal, Tamil Nadu, India'},
    {name:'Nagercoil',full:'Nagercoil, Tamil Nadu, India'},
    {name:'Dindigul',full:'Dindigul, Tamil Nadu, India'}
];

function filterCities(q){
    if(!q) return CITIES.slice(0,8);
    var ql=q.toLowerCase();
    return CITIES.filter(function(c){return c.name.toLowerCase().indexOf(ql)===0;})
        .concat(CITIES.filter(function(c){return c.name.toLowerCase().indexOf(ql)>0;}))
        .slice(0,8);
}

function buildCityDD(filtered,$dd,onSelect){
    if(!filtered.length){$dd.removeClass('open').empty();return;}
    var html='';
    filtered.forEach(function(c){
        html+='<div class="city-dd-item" data-name="'+c.name+'" data-full="'+c.full+'"><i class="bi bi-geo-alt"></i>'+c.full+'</div>';
    });
    $dd.html(html).addClass('open');
    $dd.find('.city-dd-item').on('click',function(){
        onSelect($(this).data('name'),$(this).data('full'));
        $dd.removeClass('open').empty();
    });
}

var $fromInput=$('#fromInput'),$fromDD=$('#fromDropdown'),$fromDisplay=$('#fromDisplay');
$fromInput.on('focus',function(){$(this).addClass('active');$fromDisplay.closest('.city-inner').hide();buildCityDD(filterCities($(this).val()),$fromDD,function(name,full){S.from=name;S.fromFull=full;$fromDisplay.text(full);$fromDisplay.closest('.city-inner').show();$fromInput.val('').removeClass('active');});}).on('input',function(){buildCityDD(filterCities($(this).val()),$fromDD,function(name,full){S.from=name;S.fromFull=full;$fromDisplay.text(full);$fromDisplay.closest('.city-inner').show();$fromInput.val('').removeClass('active');});}).on('blur',function(){setTimeout(function(){$fromDD.removeClass('open').empty();$fromInput.removeClass('active');$fromDisplay.closest('.city-inner').show();},200);});
$('#fromWrap').on('click',function(){$fromDisplay.closest('.city-inner').hide();$fromInput.addClass('active').focus();buildCityDD(filterCities(''),$fromDD,function(name,full){S.from=name;S.fromFull=full;$fromDisplay.text(full);$fromDisplay.closest('.city-inner').show();$fromInput.val('').removeClass('active');});});

var $toInput=$('#toInput'),$toDD=$('#toDropdown'),$toDisplay=$('#toDisplay');
$toInput.on('focus',function(){$(this).addClass('active');$toDisplay.closest('.city-inner').hide();buildCityDD(filterCities($(this).val()),$toDD,function(name,full){S.to=name;S.toFull=full;$toDisplay.text(full);$toDisplay.closest('.city-inner').show();$toInput.val('').removeClass('active');});}).on('input',function(){buildCityDD(filterCities($(this).val()),$toDD,function(name,full){S.to=name;S.toFull=full;$toDisplay.text(full);$toDisplay.closest('.city-inner').show();$toInput.val('').removeClass('active');});}).on('blur',function(){setTimeout(function(){$toDD.removeClass('open').empty();$toInput.removeClass('active');$toDisplay.closest('.city-inner').show();},200);});
$('#toWrap').on('click',function(){$toDisplay.closest('.city-inner').hide();$toInput.addClass('active').focus();buildCityDD(filterCities(''),$toDD,function(name,full){S.to=name;S.toFull=full;$toDisplay.text(full);$toDisplay.closest('.city-inner').show();$toInput.val('').removeClass('active');});});

function doSwap(){
    var tf=S.from,tff=S.fromFull,tt=S.to,ttf=S.toFull;
    S.from=tt;S.fromFull=ttf;S.to=tf;S.toFull=tff;
    $fromDisplay.text(S.fromFull);$toDisplay.text(S.toFull);
}
$('#swapBtn').on('click',function(e){e.stopPropagation();doSwap();});
$('#mobSwapBtn').on('click',function(e){e.stopPropagation();doSwap();});

/* ── FLATPICKR ── */
var fpDesktop=flatpickr('#datePicker',{minDate:'today',dateFormat:'Y-m-d',disableMobile:true,defaultDate:S.dateObj,onChange:function(selectedDates,dateStr){if(!dateStr)return;var d=selectedDates[0];S.dateObj=d;S.date=dateStr;S.dateLabel=fmtDateLabel(d);$('#dateDisplay,#dateDisplayMob').text(S.dateLabel);if($('#dateStripWrap').hasClass('show'))buildDateStrip();}});
var fpMobile=flatpickr('#datePickerMob',{minDate:'today',dateFormat:'Y-m-d',disableMobile:true,defaultDate:S.dateObj,onChange:function(selectedDates,dateStr){if(!dateStr)return;var d=selectedDates[0];S.dateObj=d;S.date=dateStr;S.dateLabel=fmtDateLabel(d);$('#dateDisplay,#dateDisplayMob').text(S.dateLabel);if($('#dateStripWrap').hasClass('show'))buildDateStrip();}});
$('#dateField').on('click',function(e){e.preventDefault();fpDesktop.open();});
$('#dateFieldMob').on('click',function(e){e.preventDefault();fpMobile.open();});

/* ── TIME DROPDOWN ── */
var TIMES=['00:00','01:00','02:00','03:00','04:00','05:00','06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'];
function buildTimeDD($field,$dd){
    $field.on('click',function(e){
        e.stopPropagation();
        $('#daysDropdown,#daysDropdownMob').removeClass('open').empty();
        if($dd.hasClass('open')){$dd.removeClass('open').empty();return;}
        var html='';TIMES.forEach(function(t){html+='<div class="time-dd-item'+(t===S.time?' on':'')+'" data-t="'+t+'">'+t+'</div>';});
        $dd.html(html).addClass('open');
        $dd.find('.time-dd-item').click(function(e){e.stopPropagation();S.time=$(this).data('t');$('#timeDisplay,#timeDisplayMob').text(S.time);$dd.removeClass('open').empty();});
    });
}
buildTimeDD($('#timeField'),$('#timeDropdown'));
buildTimeDD($('#timeFieldMob'),$('#timeDropdownMob'));

/* ── DAYS DROPDOWN ── */
function buildDaysDD($field,$dd){
    $field.on('click',function(e){
        e.stopPropagation();
        $('#timeDropdown,#timeDropdownMob').removeClass('open').empty();
        if($dd.hasClass('open')){$dd.removeClass('open').empty();return;}
        var html='';
        for(var i=1;i<=15;i++) html+='<div class="time-dd-item'+(i==S.days?' on':'')+'" data-d="'+i+'">'+i+' Day'+(i>1?'s':'')+'</div>';
        $dd.html(html).addClass('open');
        $dd.find('.time-dd-item').click(function(e){e.stopPropagation();S.days=$(this).data('d');$('#daysDisplay,#daysDisplayMob').text(S.days+' Day'+(S.days>1?'s':''));$dd.removeClass('open').empty();});
    });
}
buildDaysDD($('#daysField'),$('#daysDropdown'));
buildDaysDD($('#daysFieldMob'),$('#daysDropdownMob'));

/* ── TRIP TYPE TABS ── */
$('#oneWayTab').click(function(){
    S.tripType='One Way';
    $(this).addClass('active');$('#roundTab').removeClass('active');
    $('#daysField,#daysFieldMob').hide();
    updateTripIcons();
});
$('#roundTab').click(function(){
    S.tripType='Round Trip';
    $(this).addClass('active');$('#oneWayTab').removeClass('active');
    // Show days field in desktop search bar
    if(isMobile()) $('#daysFieldMob').show();
    else $('#daysField').show();
    updateTripIcons();
});

$(document).on('click',function(){$('#timeDropdown,#timeDropdownMob,#daysDropdown,#daysDropdownMob').removeClass('open').empty();});

/* ── FILTER PANEL ── */
$('#mobileFilterBtn').on('click',function(){$('#filterSlidePanel').addClass('open');$('#filterPanelOverlay').addClass('show');$('body').css('overflow','hidden');});
function closeFilterPanel(){$('#filterSlidePanel').removeClass('open');$('#filterPanelOverlay').removeClass('show');$('body').css('overflow','');}
$('#filterPanelOverlay').on('click',closeFilterPanel);
$('#fpApplyBtn').on('click',closeFilterPanel);
$('#fpClearBtn').on('click',function(){$('.fp-sort-radio').removeClass('on');$('.sort-radio').removeClass('on');});
$(document).on('click','.fp-sort-item',function(){
    $('.fp-sort-radio').removeClass('on');$(this).find('.fp-sort-radio').addClass('on');
    var sort=$(this).data('sort');
    $('.sort-item[data-sort="'+sort+'"] .sort-radio').addClass('on');
    $('.sort-item:not([data-sort="'+sort+'"])  .sort-radio').removeClass('on');
});
var touchStartY=0;
$('#filterSlidePanel').on('touchstart',function(e){touchStartY=e.originalEvent.touches[0].clientY;});
$('#filterSlidePanel').on('touchmove',function(e){var diff=e.originalEvent.touches[0].clientY-touchStartY;if(diff>60)closeFilterPanel();});

/* ── DATA ── */
var VEHICLES={
    sedan:{name:'Sedan',seats:'4 Seater',basePrice:930,tags:[{ic:'bi-person-fill',l:'4 Seats'},{ic:'bi-snow',l:'AC'},{ic:'bi-fuel-pump',l:'Diesel'},{ic:'bi-star-fill',l:'Top Rated'}]},
    suv:{name:'SUV',seats:'4 Seater',basePrice:1280,tags:[{ic:'bi-person-fill',l:'4 Seats'},{ic:'bi-snow',l:'AC'},{ic:'bi-fuel-pump',l:'Diesel'},{ic:'bi-shield-check',l:'Insured'}]},
    muv:{name:'MUV',seats:'7 Seater',basePrice:1600,tags:[{ic:'bi-person-fill',l:'7 Seats'},{ic:'bi-snow',l:'AC'},{ic:'bi-gem',l:'Premium'}]}
};
var CAR_IMGS={sedan:['/goride/img/mini(new1).webp','/goride/img/mini(new1).webp','/goride/img/mini(new1).webp']};
var DRIVERS=[
    {id:1,ini:'S',name:'Sanmugam',vid:'sedan',rating:'5.0',rides:1240,dep:'17:00',arr:'23:20',dur:'6h 20m',fareAdj:0,badges:['instant'],exp:'8 yrs',full:false,rideType:'private',distance:'345 km'},
    {id:2,ini:'V',name:'Arul',vid:'sedan',rating:'4.8',rides:980,dep:'18:00',arr:'00:00',dur:'6h 00m',fareAdj:-60,badges:['instant','maxback'],exp:'5 yrs',full:false,nextDay:true,rideType:'carpool',perSeatFare:853,maxSeats:3,distance:'345 km',cpFrom:'Chengalpattu, Tamil Nadu, India',cpTo:'Ulundurpet, Tamil Nadu, India',cpDriverStart:'Tambaram, Chennai, Tamil Nadu, India',cpDriverEnd:'Trichy, Tamil Nadu, India'},
    {id:3,ini:'M',name:'Muruga',vid:'suv',rating:'5.0',rides:1560,dep:'19:00',arr:'01:50',dur:'6h 50m',fareAdj:50,badges:['instant'],exp:'10 yrs',full:false,nextDay:true,rideType:'private',distance:'345 km'},
    {id:4,ini:'A',name:'Arumugam',vid:'sedan',rating:'4.5',rides:430,dep:'20:00',arr:'02:30',dur:'6h 30m',fareAdj:-80,badges:[],exp:'3 yrs',full:false,nextDay:true,rideType:'carpool',perSeatFare:620,maxSeats:4,distance:'345 km',cpFrom:'Tambaram, Chennai, Tamil Nadu, India',cpTo:'Dindigul, Tamil Nadu, India',cpDriverStart:'Chennai Central, Tamil Nadu, India',cpDriverEnd:'Madurai, Tamil Nadu, India'},
    {id:5,ini:'K',name:'Karthikeyan',vid:'muv',rating:'4.6',rides:670,dep:'21:00',arr:'03:20',dur:'6h 20m',fareAdj:200,badges:[],exp:'6 yrs',full:false,nextDay:true,rideType:'private',distance:'345 km'}
];

/* ── INFO-CARD INLINE ROW BUILDER ── */
/* Builds the horizontal pill row: date | time | triptype | upto km | car type | duration */
function buildInfoRow(opts){
    /* opts: { route, vehicle, datetime, fare, driver, method } */
 var chips='';

if(opts.route)    chips+='<span class="info-chip"><i class="bi bi-geo-alt-fill"></i><span class="info-chip-val">'+opts.route+'</span></span>';
if(opts.vehicle)  chips+='<span class="info-chip"><i class="bi bi-car-front-fill"></i><span class="info-chip-val">'+opts.vehicle+'</span></span>';
if(opts.datetime) chips+='<span class="info-chip"><i class="bi bi-calendar3"></i><span class="info-chip-val">'+opts.datetime+'</span></span>';
if(opts.triptype) chips+='<span class="info-chip"><i class="bi bi-arrow-right-circle-fill"></i><span class="info-chip-val">'+opts.triptype+'</span></span>';
if(opts.upto)     chips+='<span class="info-chip"><i class="bi bi-signpost-2-fill"></i><span class="info-chip-val">'+opts.upto+'</span></span>';
if(opts.duration) chips+='<span class="info-chip"><i class="bi bi-clock-fill"></i><span class="info-chip-val">'+opts.duration+'</span></span>';
if(opts.fare)     chips+='<span class="info-chip"><i class="bi bi-currency-rupee"></i><span class="info-chip-val">'+opts.fare+'</span></span>';
if(opts.driver)   chips+='<span class="info-chip"><i class="bi bi-person-fill"></i><span class="info-chip-val">'+opts.driver+'</span></span>';
if(opts.method)   chips+='<span class="info-chip"><i class="bi bi-wallet2"></i><span class="info-chip-val">'+opts.method+'</span></span>';

return chips;
}

/* ── DATE STRIP ── */
function buildDateStrip(){
    var lowestFare=Math.min.apply(null,DRIVERS.map(function(d){return VEHICLES[d.vid].basePrice+d.fareAdj;}));
    var html='';
    var base=S.dateObj?new Date(S.dateObj):new Date();
    for(var i=0;i<7;i++){
        var d=new Date(base);d.setDate(base.getDate()+i);
        var iso=isoDate(d);
        var variance=[0,10,40,20,0,30,50][i%7];
        var price=lowestFare-590+variance;
        var isLowest=(price==lowestFare-590);
        if(price<150) price=150+variance;
        var dayLbl=DOW[d.getDay()]+', '+d.getDate()+' '+MONTHS[d.getMonth()];
        var isActive=iso===S.date;
        html+='<div class="date-chip'+(isActive?' active':'')+(isLowest?' lowest':'')+'" data-iso="'+iso+'" data-label="'+fmtDateLabel(d)+'">'
            +'<div class="dc-day">'+dayLbl+'</div>'
            +'<div class="dc-price">From ₹'+price+'</div>'
            +'</div>';
    }
    $('#dateStripScroll').html(html);
}

$(document).on('click','.date-chip',function(){
    $('.date-chip').removeClass('active');$(this).addClass('active');
    var iso=$(this).data('iso'),label=$(this).data('label');
    S.date=iso;S.dateLabel=label;S.dateObj=new Date(iso+'T00:00:00');
    $('#dateDisplay,#dateDisplayMob').text(label);
    $('#datePicker,#datePickerMob').val(iso);
    $('#resDateLbl').text(' '+label);
    renderRides();
});

$('#searchGoBtn').on('click',doSearch);
$(document).on('keydown',function(e){if(e.key==='Enter'&&!$(e.target).closest('.modal,.overlay').length)doSearch();});

function updateCompactBar(){
    $('#mcbRoute').text(S.from+' → '+S.to);
    $('#mcbMeta').text(S.dateLabel+' · '+S.time+(S.tripType==='Round Trip'?' · '+S.days+' Day'+(S.days>1?'s':''):''));
}

function doSearch(){
    S.from=$fromDisplay.text().split(',')[0].trim()||'Chennai';
    S.to=$toDisplay.text().split(',')[0].trim()||'Madurai';
    $('#resDateLbl').text(' '+S.dateLabel);
    $('#resFromLbl').text(S.from);
    $('#resToLbl').text(S.to);
    buildDateStrip();
    $('#dateStripWrap').addClass('show');
    if(isMobile()){$('#searchStripWrap').removeClass('mobile-overlay');$('body').addClass('mobile-collapsed');updateCompactBar();}
    positionStickyRows();
    renderRides();
    $('#emptyStateWrap').hide();
    $('#mainWrap').show();
    $('#payPage,#confirmedPage').hide();
    $('html,body').animate({scrollTop:0},300);
}

$('#mobileCompactBar').on('click',function(){
    $('#searchStripWrap').addClass('mobile-overlay');
    $('body').removeClass('mobile-collapsed');
    positionStickyRows();
    $('html,body').animate({scrollTop:0},200);
});

$('#mobileCloseSearchBtn').on('click',function(e){
    e.stopPropagation();
    $('#searchStripWrap').removeClass('mobile-overlay');
    if(isMobile()&&$('#dateStripWrap').hasClass('show'))$('body').addClass('mobile-collapsed');
    positionStickyRows();
});

function positionStickyRows(){
    var collapsed=isMobile()&&$('body').hasClass('mobile-collapsed');
    var topH=collapsed?$('#mobileCompactBar').outerHeight():$('#searchStripWrap').outerHeight();
    $('#dateStripWrap').css('top',topH+'px');
    var stripH=$('#dateStripWrap').hasClass('show')?$('#dateStripWrap').outerHeight():0;
    $('.main-wrap').css('padding-top',(topH+stripH+10)+'px');
    $('.sidebar').css('top',(topH+stripH+10)+'px');
}

$(window).on('resize',function(){
    if(!isMobile()){$('body').removeClass('mobile-collapsed');$('#searchStripWrap').removeClass('mobile-overlay');}
    if($('#mainWrap').is(':visible')||$('#emptyStateWrap').is(':visible'))positionStickyRows();
    applyMobileLayout();
});

/* ═══════════════════════════════════════════
   RENDER RIDES
═══════════════════════════════════════════ */
function renderRides(){
    S.btnMap={};
    var avail=DRIVERS.filter(function(d){return !d.full;}).length;
    $('#rideCount').text(avail+' rides available');
    var html='';
    var mobile=isMobile();

    DRIVERS.forEach(function(d){
        var v=VEHICLES[d.vid]||VEHICLES.sedan;
        var isCarpool=d.rideType==='carpool';
        var seats=d.seats||1;
        var fare=isCarpool?(d.perSeatFare*seats):(v.basePrice+d.fareAdj);
        var carImg='/goride/img/mini(new1).webp';
        var moonIcon=d.nextDay?'<i class="bi bi-moon-fill" style="font-size:10px;"></i>':'';
        var badgeHtml='<span class="ride-type-badge '+(isCarpool?'carpool':'private')+'"><i class="bi bi-'+(isCarpool?'people-fill':'shield-lock-fill')+'"></i>'+(isCarpool?'Carpool':'Private Vehicle')+'</span>';

        var actionHtml=d.full
            ?'<span class="full-lbl">Full</span>'
            :'<button class="view-btn avail-btn" id="btn_'+d.id+'" data-id="'+d.id+'" data-fare="'+fare+'"><i class="bi bi-calendar-check-fill"></i> Request to Book</button>';

        if(mobile){
            /* MOBILE CARD */
            var seatsLeft=isCarpool?(d.maxSeats-seats):0;

            var priceHtml=isCarpool
                ?'<div class="mob-price-row"><span class="mob-price-big" id="priceVal_'+d.id+'">₹'+d.perSeatFare.toLocaleString('en-IN')+'</span><span class="mob-price-seat">/seat</span></div>'
                :'<div class="mob-price-row"><span class="mob-price-big" id="priceVal_'+d.id+'">₹'+fare.toLocaleString('en-IN')+'</span></div>';

            var nextDayHtml=d.nextDay?'<span class="mob-arr-next">+1</span>':'';
            var timeHtml='<div class="mob-time-row">'
                +'<span class="mob-dep-time">'+d.dep+'</span>'
                +'<span class="mob-dur-pill">'+(d.nextDay?'<i class="bi bi-moon-fill"></i> ':'')+d.dur+'</span>'
                +'<span class="mob-arr-time">'+d.arr+nextDayHtml+'</span>'
                +'</div>';

            var vehHtml='<div style="font-size:11px;color:#6b7280;font-weight:600;margin-top:4px;display:flex;align-items:center;gap:4px;">'
                +'<i class="bi bi-car-front-fill" style="color:#F5A623;font-size:10px;"></i>'+v.name+' · '+v.seats+'</div>';

            var seatsLeftHtml=isCarpool
                ?'<span class="mob-seats-left-badge"><i class="bi bi-people-fill"></i>'+seatsLeft+' left</span>'
                :'';

            var tlHtml='<div class="mob-route-timeline">';
            if(isCarpool){
                tlHtml+=buildTlRow('driver-start','Driver Start',d.cpDriverStart.split(',')[0],d.cpDriverStart.split(',').slice(1).join(',').trim(),true);
                tlHtml+=buildTlRow('from-dot','From',d.cpFrom.split(',')[0],d.cpFrom.split(',').slice(1).join(',').trim(),true);
                tlHtml+=buildTlRow('to-dot','To',d.cpTo.split(',')[0],d.cpTo.split(',').slice(1).join(',').trim(),true);
                tlHtml+=buildTlRow('driver-end','Driver End',d.cpDriverEnd.split(',')[0],d.cpDriverEnd.split(',').slice(1).join(',').trim(),false);
            } else {
                tlHtml+=buildTlRow('private-from','From',S.from,S.fromFull.split(',').slice(1).join(',').trim(),true);
                tlHtml+=buildTlRow('private-to','To',S.to,S.toFull.split(',').slice(1).join(',').trim(),false);
            }
            tlHtml+='</div>';

            var mobSeatCounterHtml=isCarpool
                ?'<div class="mob-seat-counter">'
                    +'<button class="mob-seat-btn seat-minus" data-did="'+d.id+'"><i class="bi bi-dash"></i></button>'
                    +'<span class="mob-seat-val" id="seatVal_'+d.id+'">'+seats+'</span>'
                    +'<button class="mob-seat-btn seat-plus" data-did="'+d.id+'"><i class="bi bi-plus"></i></button>'
                    +'</div>'
                :'';

            var filledHtml='';
            if(isCarpool){
                var dots='';
                for(var si=0;si<d.maxSeats;si++) dots+='<span class="mob-filled-dot'+(si<seats?' taken':'')+'"></span>';
                filledHtml='<div class="mob-filled-bar"><div class="mob-filled-text"><div class="mob-filled-dots">'+dots+'</div>'+seats+' / '+d.maxSeats+' filled · '+seatsLeft+' left</div></div>';
            }

            var mobBtnHtml=d.full
                ?'<div class="mob-book-bar"><button class="mob-book-btn" disabled style="opacity:.4;">Full</button></div>'
                :'<div class="mob-book-bar"><button class="mob-book-btn avail-btn" id="btn_'+d.id+'" data-id="'+d.id+'" data-fare="'+fare+'"><i class="bi bi-calendar-check-fill"></i> Request to Book</button></div>';

            html+='<div class="ride-card'+(d.full?' full':'')+' mob-card" style="position:relative;">'
                +badgeHtml
                +'<div class="mob-card-header">'
                +  '<div class="mob-card-header-left">'+priceHtml+timeHtml+vehHtml+'</div>'
                +  seatsLeftHtml
                +'</div>'
                +tlHtml
                +'<div class="mob-card-footer">'
                +  '<img class="mob-car-thumb car-open-btn" src="'+carImg+'" data-vid="'+d.vid+'" data-did="'+d.id+'" alt="Car">'
                +  '<div class="mob-driver-info drv-open-btn" data-did="'+d.id+'">'
                +    '<div class="mob-driver-av"><img src="/goride/img/taxi-dri.png" alt="Driver"></div>'
                +    '<div><div class="mob-driver-name">'+d.name+'</div><div class="mob-driver-rating"><i class="bi bi-star-fill star"></i> '+d.rating+'</div></div>'
                +  '</div>'
                +  mobSeatCounterHtml
                +'</div>'
                +filledHtml
                +mobBtnHtml
                +'</div>';

        } else {
            /* ═══ DESKTOP CARD ═══ */

            if(isCarpool){
                /* CARPOOL DESKTOP */
                var subLbl='<div class="per-seat-lbl" style="text-align:right;">₹'+d.perSeatFare+' / seat</div>';
                var rightExtra='<div class="seat-counter" data-did="'+d.id+'">'
                    +'<button class="seat-btn seat-minus" data-did="'+d.id+'"><i class="bi bi-dash"></i></button>'
                    +'<span class="seat-count-val" id="seatVal_'+d.id+'">'+seats+'</span>'
                    +'<button class="seat-btn seat-plus" data-did="'+d.id+'"><i class="bi bi-plus"></i></button>'
                    +'</div>';

                /* DESKTOP FILLED DOTS for carpool */
                var seatsLeft2=d.maxSeats-seats;
                var deskDots='';
                for(var di=0;di<d.maxSeats;di++) deskDots+='<span class="desk-filled-dot'+(di<seats?' taken':'')+'"></span>';
                var deskFilledHtml='<div class="desk-filled-bar" id="deskFilled_'+d.id+'">'
                    +'<div class="desk-filled-dots">'+deskDots+'</div>'
                    +'<span class="desk-filled-lbl" id="deskFilledLbl_'+d.id+'">'+seats+'/'+d.maxSeats+' · '+seatsLeft2+' left</span>'
                    +'</div>';

                html+='<div class="ride-card'+(d.full?' full':'')+'" style="position:relative;">'
                    +badgeHtml
                    +'<div class="card-dep"><div class="dep-time">'+d.dep+'</div><div class="dep-city">'+S.from+'</div></div>'
                    +'<div class="cp-stop"><div class="cp-title">Driver Start</div><div class="cp-city">'+d.cpDriverStart.split(',')[0]+'</div></div>'
                    +'<div class="card-route"><div class="route-dur">'+moonIcon+' '+d.dur+'</div><div class="route-line"><div class="route-dot"></div><div class="route-track"></div></div></div>'
                    +'<div class="cp-stop"><div class="cp-title">Driver End</div><div class="cp-city">'+d.cpDriverEnd.split(',')[0]+'</div></div>'
                    +'<div class="card-arr"><div class="arr-time">'+d.arr+'</div><div class="arr-city">'+S.to+'</div></div>'
                    +'<div class="card-car car-open-btn" data-vid="'+d.vid+'" data-did="'+d.id+'" style="cursor:pointer;">'
                    +'<div class="car-icon-row"><img class="car-img" src="'+carImg+'" alt="Car"></div>'
                    +'<div class="car-type-lbl">'+v.name+'</div><div class="car-seat-lbl">'+v.seats+'</div>'
                    +'</div>'
                    +'<div class="card-driver">'
                    +'<div class="drv-av drv-open-btn" data-did="'+d.id+'"><img src="/goride/img/taxi-dri.png" alt="Driver"></div>'
                    +'<div><div class="drv-name">'+d.name+'</div><div class="drv-rating"><i class="bi bi-star-fill star"></i> '+d.rating+'</div></div>'
                    +'</div>'
                    +'<div class="card-right">'
                    +'<div class="price" id="priceVal_'+d.id+'">₹'+fare.toLocaleString('en-IN')+'</div>'
                    +subLbl
                    +rightExtra
                    +deskFilledHtml
                    +actionHtml
                    +'</div>'
                    +'</div>';

            } else {
                /* PRIVATE VEHICLE DESKTOP – NEW DESIGN */
                var nextDayTag=d.nextDay?'<span class="prv-next-day">+1</span>':'';
                var tripTypeLabel=S.tripType==='Round Trip'?'Round Trip ('+S.days+' Day'+(S.days>1?'s':'')+')':'One Way';

                html+='<div class="ride-card'+(d.full?' full':'')+'" style="position:relative;">'
                    +badgeHtml
                    /* LEFT: route details */
                    +'<div class="prv-left">'
                    +  '<div class="prv-route-cities">'
                    +    '<div><div class="prv-city">'+S.from+'</div><div class="prv-city-sub">Departure</div></div>'
                    +    '<div class="prv-arrow">→</div>'
                    +    '<div><div class="prv-city">'+S.to+'</div><div class="prv-city-sub">Destination</div></div>'
                    +  '</div>'
                    /* date · time · triptype chips */
                    +  '<div class="prv-meta-row">'
                    +    '<span class="prv-meta-chip"><i class="bi bi-calendar3"></i>'+S.dateLabel+'</span>'
                    +    '<span class="prv-meta-sep">·</span>'
                    +    '<span class="prv-meta-chip"><i class="bi bi-clock"></i>'+S.time+'</span>'
                    +    '<span class="prv-meta-sep">·</span>'
                    +    '<span class="prv-meta-chip"><i class="bi bi-arrow-right-circle-fill" style="color:#072e69;"></i>'+tripTypeLabel+'</span>'
                    +  '</div>'
                    /* distance + duration */
                    +  '<div class="prv-dist-row">'
                    +    '<span class="prv-dist-chip"><i class="bi bi-signpost-2-fill"></i>Upto '+d.distance+'</span>'
                    +    '<span class="prv-dur-chip"><i class="bi bi-hourglass-split"></i>'+d.dur+'</span>'
                    +  '</div>'
                    +'</div>'
                    /* CENTER: dep time → arr time */
                    +'<div class="prv-times">'
                    +  '<div class="prv-time-lbl">Departs</div>'
                    +  '<div class="prv-dep-time">'+d.dep+'</div>'
                    +  '<div class="prv-arr-block"><div class="prv-arr-time">→ '+d.arr+'</div>'+nextDayTag+'</div>'
                    +'</div>'
                    /* CAR */
                    +'<div class="card-car car-open-btn" data-vid="'+d.vid+'" data-did="'+d.id+'" style="cursor:pointer;">'
                    +'<div class="car-icon-row"><img class="car-img" src="'+carImg+'" alt="Car"></div>'
                    +'<div class="car-type-lbl">'+v.name+'</div><div class="car-seat-lbl">'+v.seats+'</div>'
                    +'</div>'
                    /* DRIVER */
                    +'<div class="card-driver">'
                    +'<div class="drv-av drv-open-btn" data-did="'+d.id+'"><img src="/goride/img/taxi-dri.png" alt="Driver"></div>'
                    +'<div><div class="drv-name">'+d.name+'</div><div class="drv-rating"><i class="bi bi-star-fill star"></i> '+d.rating+'</div></div>'
                    +'</div>'
                    /* PRICE + ACTION */
                    +'<div class="card-right">'
                    +'<div class="price" id="priceVal_'+d.id+'">₹'+fare.toLocaleString('en-IN')+'</div>'
                    +'<div class="vehicle-sub-lbl"><i class="bi bi-car-front-fill"></i>'+v.name+' · '+v.seats+'</div>'
                    +actionHtml
                    +'</div>'
                    +'</div>';
            }
        }
    });

    $('#rideList').html(html);
    DRIVERS.forEach(function(d){if(!d.full)S.btnMap[d.id]=$('#btn_'+d.id);});

    $(document).off('click.avail').on('click.avail','.avail-btn',function(){
        handleViewDetailsClick(parseInt($(this).data('id')),parseFloat($(this).data('fare')));
    });
    $(document).off('click.drv').on('click.drv','.drv-open-btn',function(){openDriverLb(parseInt($(this).data('did')));});
    $(document).off('click.caropen').on('click.caropen','.car-open-btn',function(){openCarModal($(this).data('vid'),parseInt($(this).data('did')));});

    $(document).off('click.seatplus').on('click.seatplus','.seat-plus,.mob-seat-btn.seat-plus',function(e){
        e.stopPropagation();
        var did=parseInt($(this).data('did'));
        var d=DRIVERS.find(function(x){return x.id===did;});
        if(!d)return;
        d.seats=d.seats||1;
        if(d.seats<d.maxSeats)d.seats++;
        updateCarpoolCard(d);
    });
    $(document).off('click.seatminus').on('click.seatminus','.seat-minus,.mob-seat-btn.seat-minus',function(e){
        e.stopPropagation();
        var did=parseInt($(this).data('did'));
        var d=DRIVERS.find(function(x){return x.id===did;});
        if(!d)return;
        d.seats=d.seats||1;
        if(d.seats>1)d.seats--;
        updateCarpoolCard(d);
    });
}

function buildTlRow(dotClass,label,val,sub,hasLine){
    return '<div class="mob-tl-row">'
        +'<div class="mob-tl-icon-wrap">'
        +'<div class="mob-tl-dot '+dotClass+'"></div>'
        +(hasLine?'<div class="mob-tl-line"></div>':'')
        +'</div>'
        +'<div class="mob-tl-text">'
        +'<div class="mob-tl-label">'+label+'</div>'
        +'<div class="mob-tl-val">'+val+'</div>'
        +(sub?'<div class="mob-tl-sub">'+sub+'</div>':'')
        +'</div>'
        +'</div>';
}

function updateCarpoolCard(d){
    var fare=d.perSeatFare*d.seats;
    var seatsLeft=d.maxSeats-d.seats;
    $('#seatVal_'+d.id).text(d.seats);
    if(isMobile()){
        $('#priceVal_'+d.id).text('₹'+d.perSeatFare.toLocaleString('en-IN'));
    } else {
        $('#priceVal_'+d.id).text('₹'+fare.toLocaleString('en-IN'));
        /* update desktop filled dots */
        var deskDots='';
        for(var di=0;di<d.maxSeats;di++) deskDots+='<span class="desk-filled-dot'+(di<d.seats?' taken':'')+'"></span>';
        $('#deskFilled_'+d.id+' .desk-filled-dots').html(deskDots);
        $('#deskFilledLbl_'+d.id).text(d.seats+'/'+d.maxSeats+' · '+seatsLeft+' left');
    }
    $('#btn_'+d.id).attr('data-fare',fare);
    /* mobile filled bar */
    var $bar=$('#btn_'+d.id).closest('.ride-card').find('.mob-filled-bar');
    if($bar.length){
        var dots='';
        for(var si=0;si<d.maxSeats;si++) dots+='<span class="mob-filled-dot'+(si<d.seats?' taken':'')+'"></span>';
        $bar.find('.mob-filled-dots').html(dots);
        $bar.find('.mob-filled-text').contents().filter(function(){return this.nodeType===3;}).last().replaceWith(d.seats+' / '+d.maxSeats+' filled · '+seatsLeft+' left');
    }
    var $badge=$('#btn_'+d.id).closest('.ride-card').find('.mob-seats-left-badge');
    if($badge.length)$badge.html('<i class="bi bi-people-fill"></i>'+seatsLeft+' left');
}

$(document).on('click','.sort-item',function(){$('.sort-radio').removeClass('on');$(this).find('.sort-radio').addClass('on');});
$('#clearSortBtn').on('click',function(){$('.sort-radio').removeClass('on');});

/* ── DRIVER LIGHTBOX ── */
function openDriverLb(id){
    var d=DRIVERS.find(function(x){return x.id===id;});
    if(!d)return;
    $('#lbTitle,#lbName').text(d.name+' C');
    $('#lbRating').text(d.rating);
    $('#lbVehicle').text(VEHICLES[d.vid].name+' '+VEHICLES[d.vid].seats);
    $('#lbRides').text(d.rides);
    $('#lbExp').text(d.exp);
    $('#lbPhoto').attr('src','/goride/img/taxi-dri.png');
    $('#drvLightbox').addClass('show');
}
$('#closeLb').on('click',function(){$('#drvLightbox').removeClass('show');});
$('#drvLightbox').on('click',function(e){if($(e.target).is(this))$(this).removeClass('show');});

/* ── CAR MODAL ── */
var CS={images:[],idx:0};
function openCarModal(vid,did){
    var d=DRIVERS.find(function(x){return x.id===did;});
    var v=VEHICLES[vid]||VEHICLES.sedan;
    CS.images=CAR_IMGS[vid]||[];CS.idx=0;
    $('#carTitle').text(d?d.name+'\'s Vehicle':'Vehicle');
    $('#carName').text(v.name+' '+v.seats);
    var fh='';v.tags.forEach(function(t){fh+='<div class="car-feat"><i class="bi '+t.ic+'"></i>'+t.l+'</div>';});
    $('#carFeats').html(fh);
    renderCarSlide();
    $('#carModal').addClass('show');
}
function renderCarSlide(){
    var imgs=CS.images,i=CS.idx;
    $('#carBig').attr('src',imgs[i]);
    $('#carCtr').text((i+1)+' / '+imgs.length);
    var th='';imgs.forEach(function(s,idx){th+='<div class="car-thumb'+(idx===i?' on':'')+'" data-idx="'+idx+'"><img src="'+s+'"></div>';});
    $('#carThumbs').html(th);
    $('#carArrL').toggle(i>0);$('#carArrR').toggle(i<imgs.length-1);
    $(document).off('click.cthumb').on('click.cthumb','.car-thumb',function(){CS.idx=parseInt($(this).data('idx'));renderCarSlide();});
}
$('#carArrL').on('click',function(){if(CS.idx>0){CS.idx--;renderCarSlide();}});
$('#carArrR').on('click',function(){if(CS.idx<CS.images.length-1){CS.idx++;renderCarSlide();}});
$('#closeCarModal').on('click',function(){$('#carModal').removeClass('show');});
$('#carModal').on('click',function(e){if($(e.target).is(this))$(this).removeClass('show');});

/* ── BOOKING FLOW ── */
function handleViewDetailsClick(id,fare){
    var d=DRIVERS.find(function(x){return x.id===id;});
    S.selectedDriver=d;S.selectedFare=fare;S.selectedSeats=d.seats||1;
    if(!S.isVerified)openAvailModal(id,fare);
    else startAvailabilityCheck(id);
}

function getInfoChips(d,fare){
    var v=VEHICLES[d.vid];
    var tripStr=S.tripType==='Round Trip'?'Round Trip ('+S.days+'d)':'One Way';
    return buildInfoRow({
        route:S.from+' → '+S.to,
        vehicle:v.name+' '+v.seats+(d.rideType==='carpool'?' · '+(d.seats||1)+' seat(s)':''),
        datetime:S.dateLabel+' · '+S.time,
        triptype:tripStr,
        upto:'Upto '+d.distance,
        duration:d.dur
    });
}

function openAvailModal(id,fare){
    var d=DRIVERS.find(function(x){return x.id===id;});
    var v=VEHICLES[d.vid];
    $('#ctAv').text(d.ini);
    $('#ctName2').text(d.name+' C');
    $('#ctMeta').text(v.name+' '+v.seats+' · ★'+d.rating);
    $('#ctPrice').text('₹'+fare.toLocaleString('en-IN'));
    $('#ctInfoRow').html(getInfoChips(d,fare));
    $('#ctName,#ctPhone').val('');
    openOverlay('contactModal');
}

$('#sendOtpBtn').on('click',function(){
    var name=$('#ctName').val().trim(),phone=$('#ctPhone').val().trim();
    if(!name){alert('Please enter your full name.');return;}
    if(!phone||phone.length<10){alert('Please enter a valid mobile number.');return;}
    S.userName=name;S.userPhone=phone;
    closeOverlay('contactModal');
    $('.otp-box').val('').removeClass('filled');
    $('#otpPhone').text('+91 '+phone);
    openOverlay('otpModal');
    $('#o1').focus();
});

$('.otp-box').on('input',function(){
    var v=$(this).val().replace(/\D/g,'');$(this).val(v);
    if(v.length===1){$(this).addClass('filled');$(this).next('.otp-box').focus();}
    else{$(this).removeClass('filled');}
}).on('keydown',function(e){if(e.key==='Backspace'&&!$(this).val())$(this).prev('.otp-box').focus();});

$('#verifyOtpBtn').on('click',function(){
    var otp=['o1','o2','o3','o4'].map(function(id){return $('#'+id).val();}).join('');
    if(otp.length<4){alert('Please enter the 4-digit OTP.');return;}
    S.isVerified=true;
    closeOverlay('otpModal');
    showWelcomeBar();
    startAvailabilityCheck(S.selectedDriver.id);
});

function showWelcomeBar(){
    var name=S.userName||'Guest';
    var initial=name.trim().charAt(0).toUpperCase()||'G';
    $('#wubAv').html(initial);
    $('#wubName').text(name);
    $('#welcomeUserBar').css('display','flex');
}

function startAvailabilityCheck(id){
    var $btn=S.btnMap[id];
    if($btn){
        $btn.prop('disabled',true)
            .removeClass()
            .addClass(isMobile()?'mob-book-btn checking':'view-btn checking')
            .html('<span class="btn-spinner"></span> Checking...');
    }
    setTimeout(function(){
        if($btn){
            $btn.prop('disabled',false)
                .removeClass()
                .addClass(isMobile()?'mob-book-btn available':'view-btn available')
                .html('<i class="bi bi-car-front-fill"></i> Driver Available');
            $btn.off('click').on('click',function(){openBookingModal();});
        }
    },4000);
}

$('#acceptedBtn').on('click',function(){closeOverlay('waitModal');openBookingModal();});
$('#busyBtn,#cancelWaitBtn').on('click',function(){closeOverlay('waitModal');});

$('#resendLink').on('click',function(e){
    e.preventDefault();
    var $el=$(this);$el.text('Sent!').css('color','#16a34a');
    setTimeout(function(){$el.text('Resend OTP').css('color','#1a1a1a');},3000);
});

function openBookingModal(){
    var d=S.selectedDriver;
    var v=VEHICLES[d.vid];
    S.selectedMethod=null;
    $('.booking-radio-opt').removeClass('sel');
    $('#bookingModalTitle').text('Choose Payment');
    $('#bookingPaymentChoice').show();
    $('#bookingConfirmedView').hide();
    $('#bmInfoRow').html(buildInfoRow({
        route:S.from+' → '+S.to,
        vehicle:v.name+' '+v.seats+(d.rideType==='carpool'?' · '+S.selectedSeats+' seat(s)':''),
        fare:'₹'+S.selectedFare.toLocaleString('en-IN')
    }));
    openOverlay('paymentConfirmModal');
}

$(document).on('click','.booking-radio-opt',function(){
    $('.booking-radio-opt').removeClass('sel');$(this).addClass('sel');
    S.selectedMethod=$(this).data('method');
});

$('#confirmBookingBtn').on('click',function(){
    if(!S.selectedMethod){alert('Please select a payment method.');return;}
    var ref='GRC-'+Date.now().toString().slice(-6)+'-'+Math.random().toString(36).slice(2,5).toUpperCase();
    $('#bmConfRef').text(ref);
    $('#bmConfInfoRow').html(buildInfoRow({
        driver:S.selectedDriver.name+' · '+VEHICLES[S.selectedDriver.vid].name,
        method:S.selectedMethod
    }));
    $('#bookingModalTitle').text('Booking Confirmed');
    $('#bookingPaymentChoice').hide();
    $('#bookingConfirmedView').show();
});

$('#bmDoneBtn,#closeBookingModal').on('click',function(){closeOverlay('paymentConfirmModal');});
$('#payBackBtn').on('click',function(){$('#payPage').hide();$('#mainWrap').show();$('html,body').animate({scrollTop:0},300);});
$('#backHomeBtn').on('click',function(){location.reload();});

function openOverlay(id){$('#'+id).addClass('show');$('body').css('overflow','hidden');}
function closeOverlay(id){$('#'+id).removeClass('show');if(!$('.overlay.show').length)$('body').css('overflow','');}
$('#closeContactModal').on('click',function(){closeOverlay('contactModal');});
$('#closeOtpModal').on('click',function(){closeOverlay('otpModal');});
$('#closeWaitModal').on('click',function(){closeOverlay('waitModal');});
$('.overlay').on('click',function(e){if($(e.target).is(this))closeOverlay($(this).attr('id'));});
$(document).on('keydown',function(e){
    if(e.key==='Escape'){
        $('.overlay.show').removeClass('show');
        $('.lb.show').removeClass('show');
        $('.car-modal.show').removeClass('show');
        closeFilterPanel();
        $('body').css('overflow','');
    }
});

$('#fromDisplay').text(S.fromFull);
$('#toDisplay').text(S.toFull);
positionStickyRows();

});
</script>

<div class="site-footer">©2026 <a href="https://www.goride.run/" target="_blank">GORIDE RUN PRIVATE LIMITED</a>. All rights reserved.</div>
</body>
</html>