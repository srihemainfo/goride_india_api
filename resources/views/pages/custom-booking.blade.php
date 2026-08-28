@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('goride/css/custom-plan-style.css') }}">
    <link rel="stylesheet" href="{{ asset('goride/css/custom-plan-flaticon.css') }}">
    <script src="{{ asset('goride/js/custom-plan-main.js') }}"></script>

    <style>
    
/*    .navbar {*/
/*    pointer-events: none;*/
/*}*/

/*.navbar * {*/
/*    pointer-events: auto;*/
/*}*/

            .cs_app_btns {
                display: flex;
                justify-content: center;
                gap: 14px;
                flex-wrap: wrap;
                margin-top: 18px;
            }
            
            .cs_store_pill {
                display: inline-flex;
                align-items: center;
                gap: 11px;
                padding: 8px 15px;
                border-radius: 12px;
                text-decoration: none;
                font-family: inherit;
                transition: all 0.3s ease;
                min-width: 158px;
                position: relative;
                overflow: hidden;
                 animation: downloadBounce 1.2s ease-in-out infinite;
                
            }
            @keyframes downloadBounce {
                0%   { transform: translateY(0);   opacity: 1;   }
                40%  { transform: translateY(4px); opacity: 0.6; }
                70%  { transform: translateY(-2px);opacity: 1;   }
                100% { transform: translateY(0);   opacity: 1;   }
            }
            
            .cs_store_pill::after {
                content: "";
                position: absolute;
                top: 0; left: -75%;
                width: 50%; height: 100%;
                background: rgba(255,255,255,0.15);
                transform: skewX(-20deg);
                transition: left 0.5s ease;
            }
            
            .cs_store_pill:hover::after {
                left: 130%;
            }
            
            .cs_store_pill i {
                font-size: 26px;
                flex-shrink: 0;
                position: relative;
                z-index: 1;
            }
            
            .cs_store_text {
                display: flex;
                flex-direction: column;
                text-align: left;
                line-height: 1.25;
                position: relative;
                z-index: 1;
            }
            
            .cs_store_text small {
                font-size: 14px;
                font-weight: 500;
                letter-spacing: 0.4px;
                opacity: 0.88;
                color:black;
            }
            
            .cs_store_text strong {
                font-size: 15px;
                font-weight: 700;
                letter-spacing: 0.2px;
                display: block;
                color:black;
            }
            
            /* ---- Google Play ---- */
            .cs_store_android {
                background: #f9bf00;
                color: #ffffff;
                border: 1px solid rgb(249 191 0 / 59%);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                 animation: floatAndroid 3s ease-in-out infinite;
            }
            
            .cs_store_android i {
               background: black;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                filter: drop-shadow(0 1px 3px rgba(0, 200, 83, 0.4));
                filter: drop-shadow(0 1px 4px rgba(255, 255, 255, 0.3));
            }
            
            .cs_store_android:hover {
                background: #f9bf00;
                 border-color: rgba(255,255,255,0.35);
                color: #f9bf00ad;
                transform: translateY(-3px) scale(1.02);
                box-shadow: 0 10px 25px rgba(0,0,0,0.5), 0 0 20px rgba(0,200,83,0.2);
                display: inline-flex;
            }
            
            /* ---- App Store ---- */
            .cs_store_ios {
            background: #f9bf00;
                color: #ffffff;
                border: 1px solid rgb(249 191 0 / 59%);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
                 animation: floatIos 3s ease-in-out 0.6s infinite;
            }
            
            @keyframes floatAndroid {
                0%,100% {
                    transform: translate3d(0,0,0);
                }
            
                50% {
                    transform: translate3d(0,-7px,0);
                }
            }
            
            @keyframes floatIos {
                0%,100% {
                    transform: translate3d(0,0,0);
                }
            
                50% {
                    transform: translate3d(0,-7px,0);
                }
            }
            .cs_store_ios i {
                color: black;
                font-size: 28px;
                filter: drop-shadow(0 1px 4px rgba(255,255,255,0.3));
            }
            
            .cs_store_ios:hover {
                background: #f9bf00;
                border-color: rgba(255,255,255,0.35);
                color: #f9bf00ad;
                transform: translateY(-3px) scale(1.02);
                box-shadow: 0 10px 25px rgba(0,0,0,0.5), 0 0 20px rgba(255,255,255,0.1);
                display: inline-flex;
            }
            .cs_btn.cs_style_2{
                background:white;
            }
    
        .book-now-btn {
        background: linear-gradient(135deg, #f8be00, #ffd84d);
            border: none;
            color: #1a1a1a;
            font-weight: 600;
            padding: 1px 9px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(248, 190, 0, 0.6), 0 0 20px rgba(248, 190, 0, 0.4);
        }
        
        /* Hover Glow Effect */
        .book-now-btn:hover {
            color: #1a1a1a;
          transform: translateY(-2px) scale(1.03);
        }
        
        /* Click Effect */
        .book-now-btn:active {
          transform: scale(0.98);
          box-shadow: 0 0 8px rgba(248, 190, 0, 0.5);
        }
    .book-btn {
  background: linear-gradient(135deg, #ffd600, #ffb300);
  color: #000;
  font-weight: 500;
  border: none;
font-size:12px;
  transition: all 0.3s ease-in-out;
  position: relative;
  overflow: hidden;
}

.main-banner-content h1
{
  font-size: 40px !important;
    
}
.goride-booking h2,.goride-booking h1{
    color:white;
}
.page-header{
    height:675px;
}
.page-header-shape{
    height:35px;
}

.book-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 60%;
  height: 100%;
  background: rgba(255, 255, 255, 0.4);
  transform: skewX(-25deg);
  transition: 0.6s;
}

.book-btn:hover::before {
  left: 120%;
}


.book-btn:hover {
  background: linear-gradient(135deg, #ffea00, #ffc107);
  box-shadow: 0 6px 18px rgba(255, 193, 7, 0.6);
  transform: translateY(-2px);
  color: #000;
}


.book-btn i {
  transition: transform 0.3s ease;
}

.book-btn:hover i {
  transform: translateX(4px);
}

        .goride-route-tags .route-tags a:hover {
   
           opacity: 0.7;
            text-decoration: underline;
            text-underline-offset: 6px;
            text-decoration-color: #f9bf00;
        }
        
         .goride-route-tags .container{
                max-width:1200px;
            }
        .goride-route-tags {
          /*background:#f9bf00;*/
          padding: 40px 0;
        }
        
        .goride-route-tags .route-heading {
         color: #f9bf00;
        font-size: 26px;
        margin-bottom: 19px;
        background: white;
        display: inline;
        padding: 6px 13px;
        border-radius: 10px;
        }
        
        .goride-route-tags .route-tags {
          line-height: 1.9;
            display: grid;
    grid-template-columns: repeat(4, 1fr); /* desktop */
    gap: 0px 4px;
        }
        
        .goride-route-tags .route-tags a {
            color: black;
            font-size: 12px;
            text-decoration: none;
            
            position: relative;
          
            font-weight: 500;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            padding-left:18px;
           font-family: 'Outfit', sans-serif;
         
        }
          .goride-route-tags .route-tags a i  {
              font-size: 15px;
            left: 0;
            position: absolute;
            top: 7px;
          } 
        
  
        


        .car-type-section {
            background: rgb(225 221 221);

            border-radius: 15px;
            padding: 30px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .car-type-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            display: block;
        }

        .car-type-label small {
            display: block;
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
            margin-top: 5px;
        }

        .car-tabs {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .car-tab {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 50px;
            padding: 3px 26px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            color: #333;
            min-width: 140px;
            justify-content: center;
        }

        .car-tab:hover {
            border-color: #f9bf00;
            /*background: #f0f8ff;*/
            transform: translateY(-2px);
            color: black;
        }

        .car-tab.active {
            background: #f9bf00;

            border-color: #f9bf00;

        }

        .car-icon {
            font-size: 1.5rem;
        }

        /* District Dropdown */
        .filter-section {
            background: white;
            border-radius: 15px;
            padding: 25px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .filter-label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
            display: block;
        }

        .district-dropdown {
            width: 100%;
            max-width: 400px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            color: #333;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .district-dropdown:hover {
            border-color: #007bff;
        }

        .district-dropdown:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        /* Routes Cards */
        .routes-section {
            margin-bottom: 40px;
        }

        /* .routes-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                gap: 25px;
            } */

        .route-card {
            background: white;
            border-radius: 12px;
            /* overflow: hidden; */
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }


        .route-header {
            border-radius: 12px 12px 0px 0px;
            background: linear-gradient(135deg, #f9bf00 57%, #a3a9ae 36%);
            color: white;
            padding: 12px 12px;
            /* border-bottom: 3px solid #004085; */
        }


        .route-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: black;
        }

        .route-distance {
            font-size: 15px;
            /* opacity: 0.9; */
            font-weight: 700;
            /*color: darkblue;*/
        }
        .route-distance-two {
            font-size: 15px;
            /* opacity: 0.9; */
            font-weight: 700;
            /*color: darkblue;*/
        }

        .route-body {
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* margin-bottom: 15px; */
            /* padding-bottom: 15px; */
            border-bottom: 1px solid #f0f0f0;
            padding: 8px 8px;
        }

        .info-row:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-size: 1rem;
            color: #1a1a1a;
            font-weight: 600;
        }

        .included-kms {
            color: #28a745;
            font-weight: 600;
        }

        .extra-fare {
            color: #ff6b6b;
            font-size: 0.85rem;
        }

        .price-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;

            text-align: center;
            display: flex;
            justify-content: space-evenly;
        }

        .price {
            font-size: 1.8rem;
            font-weight: 700;
            color: darkblue;
        }

        .original-price {
            font-size: 0.9rem;
            color: #999;
            text-decoration: line-through;
            margin-left: 10px;
        }

        .taxes-info {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

        .view-cabs-btn {
            width: 100%;
            padding: 3px 6px;
            background: #f9bf00;
            color: black;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .view-cabs-btn:hover {
            transform: scale(1.02);

        }

        .view-cabs-btn:active {
            transform: scale(0.98);
        }

        .icon-text {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            font-size: 13px;
            color: #555;
        }

        .icon-text i {
            color: #d9534f;
            font-size: 13px;
        }

        /*.cab-card {*/
        /*    background: #fff;*/
        /*    border-radius: 14px;*/
        /*    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);*/
        /*    transition: all 0.3s ease;*/
        /*    display: flex;*/
        /*    align-items: center;*/
        /*    justify-content: space-between;*/

        /*    padding: 14px 18px;*/
        /*    border: 1px solid #eee;*/
        /*    flex-wrap: wrap;*/
        /*    height: 100%;*/
        /*}*/

        .cab-card:hover {
            border-color: #f9bf00;
            transform: translateY(-3px);
        }

        .cab-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex: 1;
            min-width: 240px;
        }

        .cab-left img {
            width: 130px;
            height: 75px;
            border-radius: 10px;
            object-fit: cover;
        }

        .cab-info h5 {
            font-weight: 600;
            color: #111;
            margin-bottom: 3px;
            font-size: 14px;
            line-height: 1.3;
            word-break: break-word;
        }



        .cab-middle {
            text-align: center;
            flex: 1;
            min-width: 140px;
        }

        .cab-middle .km-text {
            /*display: flex;*/
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 13px;
            color: #333;
            flex-wrap: wrap;
            font-weight: 600;
        }

        .cab-middle .km-text i {
            color: #2ecc71;
            font-size: 13px;
        }

        .cab-middle .price {
            font-size: 16px;
            font-weight: 700;
            color: #000;
            margin-top: 3px;
        }

        .cab-right {
            text-align: right;

        }

        .cab-right h4 {
            font-weight: 700;
            color: #000;
            margin-bottom: 0;
        }




        .accordion-box .block .acc-btn:before {
            top: 14px;
        }

        .btn-signin {
            font-family: 'Outfit', sans-serif;
        }

        .accordion-box .block .acc-btn.active {
            padding: 22px 16px;
        }

        .accordion-box .block .acc-btn {
            padding: 22px 16px;
        }

        .accordion-box .block .content {
            padding: 16px 16px;
            font-family: 'Outfit', sans-serif;
            line-height: 1.8;
        }

        .about width: 24px;
        height: 24px;
        }

        .item .curv-butn .br-right-bottom {
            position: absolute;
            bottom: -1px;
            right: -24px;
            -webkit-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            transform: rotate(270deg);
            line-height: 1;
        }

        .item .curv-butn .br-left-top svg {
            width: 24px;
            height: 24px;
        }

        .item .curv-butn .br-left-top {
            position: absolute;
            top: -24px;
            left: -1px;
            -webkit-transform: rotate(270deg);
            -ms-transform: rotate(270deg);
            transform: rotate(270deg);
            line-height: 1;
        }

        .item img {
            width: 100%;
            transform: scale(1);
            transition: transform 500ms ease;
        }

        .item .curv-butn {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 90px;
            height: 90px;
            line-height: 90px;
            text-align: center;
            border-radius: 0 40px 0 0;
        }

        .icon-bg {
            background: #fff !important;
        }

        .item img {
            width: 100%;
            transform: scale(1);
            transition: transform 500ms ease;
        }

        .item {
            position: relative;
            border-radius: 20px 20px 20px 0;
            overflow: hidden;
            margin-bottom: 15px;
            isolation: isolate;
        }



        .theme-btn {
            position: relative;
            overflow: hidden;
            background-color: #f9bf00;
            color: #000;
            font-weight: 600;
            border: none;
            border-radius: 25px;
            padding: 3px 20px;
            font-size: 13px;
            transition: all 0.3s ease;
            z-index: 1;
            flex-shrink: 0;
        }
        .theme-btn:focus,
.theme-btn:active,
.theme-btn:focus-visible {
    color: #fff;
    outline: none;
}

        .theme-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: #000;
            transition: all 0.4s ease;
            z-index: -1;
            border-radius: 25px;
        }

        .theme-btn:hover::before {
            left: 0;
        }

        .theme-btn:hover {
            color: #fff !important;
        }


        .theme-btn i {
            margin-left: 6px;
        }

        .why-choose {
            background: url('{{ asset('goride/img/car.webp') }}') center center/cover no-repeat;
            /*padding: 80px 20px;*/
            color: #fff;
            text-align: center;
            position: relative;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            /* dark overlay for readability */
            padding: 60px 20px;
            border-radius: 10px;
        }

        .why-choose h2 {
            font-size: 2.5rem;
            margin-bottom: 40px;
            font-weight: bold;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .feature-box {
            background-color: rgb(255 255 255 / 90%);
            padding: 20px;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            background-color: rgb(255 251 255);

        }

        .feature-box h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .feature-box p {

            font-weight: bold;
            color: #7a3d0c;
        }


        .fare-options h3 {
            color: #7a3d0c;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .price-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 25px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .price-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #222;
        }

        .card-details small {
            color: #777;
        }

        .card-price {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .card-price span {
            font-size: 20px;
            font-weight: 600;
            color: #e66100;
        }

        .card-price button {
            background: #f9bf00;
            color: #222;
            border: none;
            padding: 2px 14px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.4s ease-in-out;
            font-weight: 600;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        .card-price button:hover {
            background: #e66100;
            color: #fff;
            transform: scale(1.08);
            box-shadow: 0 6px 12px rgba(230, 97, 0, 0.4);
        }

        .default-animation img {
            width: 21px;
            height: 21px;
        }

        @media only screen and (max-width: 767px) {
            
            .choose-content-area h3{
                font-size:22px !important;
            }
            .goride-route-tags .route-tags {
        grid-template-columns: 1fr;
    }
            .price-section {
                display: block;
            }

            .car-tabs {
                gap: 10px;
            }

            .car-tab {
                       padding: 3px 18px;
                min-width: 120px;
                font-size: 0.9rem;
            }

            .routes-grid {
                grid-template-columns: 1fr;
            }

            .car-type-section,
            .filter-section {
                padding: 20px;
            }

            .route-card {
                margin: 0;
            }

            .cab-card {
                flex-direction: row;
                align-items: center;
                text-align: left;
                gap: 10px;
            }

            .accordion-box .block .acc-btn {
                line-height: 1.8;
            }

            .page-header-info h1 {
                font-size: 26px !important;
            }

            .price-card {
                flex-direction: column;
                gap: 15px;
            }

            .boosting-list-tab .tabs li a {
                font-size: 15px;
                padding-right: 10px;
                padding-top: 12px;
                padding-bottom: 12px;
                padding-left: 10px;
            }

            .tab-section {
                padding-bottom: 50px;
            }

            .boosting-list-tab .tabs li {
                flex: 0 0 48%;
                max-width: 46%;
                padding-top: 10px;
            }

            .boosting-list-tab .tabs li a {
                font-size: 15px;
                padding-right: 10px;
                padding-top: 12px;
                padding-bottom: 12px;
                padding-left: 10px;
            }

            .boosting-list-tab .tabs li a span {
                display: block;
                margin-top: 4px;
                font-size: 12px;
            }

            .boosting-list-tab .tabs li a i {
                font-size: 30px;
            }

            .boosting-list-tab .tabs li.bg-eff7e9 {
                background: unset;
            }

            .boosting-list-tab .tabs li.bg-fff8f0 {
                background: unset;
            }

            .boosting-list-tab .tabs li.bg-ecfaf7 {
                background: unset;
            }

            .boosting-list-tab .tabs li.bg-f2f0fb {
                background: unset;
            }

            .boosting-list-tab .tabs li.bg-c5ebf9 {
                background: unset;
            }

            .boosting-list-tab .tab_content .tabs_item .content h2 {
                margin: 30px 0 10px 0;
            }

            .boosting-list-tab .tab_content .tabs_item .tab-text-content {
                margin-top: 25px;
                padding-left: 45px;
            }

            .boosting-list-tab .tab_content .tabs_item .tab-text-content i::before {
                font-size: 30px;
            }

            .boosting-list-tab .tab_content .tabs_item .tab-shape {
                width: 110px;
            }

            .boosting-list-tab .tab_content .tabs_item .tab-btn {
                margin-top: 20px;
            }
        }
     
 @media only screen and (min-width: 380px) and (max-width: 440px) {
    body .goride-booking {
        top: 45px !important;
    }
}
     
    </style>
    


@endsection
@section('content')
    @php
        use Illuminate\Support\Str;
        
        $cacheKey = '/taxi/chennai';
        $cacheFileName = str_replace('/', '_', ltrim($cacheKey, '/')) . '.json';
        
        $cacheDir = storage_path('seoMiddleware');
        
        if (!\File::exists($cacheDir)) {
            \File::makeDirectory($cacheDir, 0755, true);
        }
    
        $cachePath = $cacheDir . '/' . $cacheFileName;
        $seoTags = [];
    
        if (\File::exists($cachePath)) {
            $seoTags = json_decode(\File::get($cachePath), true) ?? [];
            view()->share('seoTags', $seoTags);
        }

        $disCripTion = trim(
            ($seoTags['wikiDes'] ?? '') . ' ' . ($seoTags['shortNote'] ?? '')
        );
    
        if (!empty($seoTags['innerLinks']) && is_array($seoTags['innerLinks'])) {
    
            $innerLinks = $seoTags['innerLinks'];
            shuffle($innerLinks);
    
            $disCripTion = array_reduce($innerLinks, function ($description, $linkData) {
    
                // ✅ ARRAY ACCESS (NOT OBJECT)
                if (
                    is_array($linkData) &&
                    !empty($linkData['slug']) &&
                    !empty($linkData['name']) &&
                    stripos($description, $linkData['name']) !== false
                ) {
                    $anchorTag =
                        '<a href="/' . e($linkData['slug']) . '" ' .
                        'style="color:#467bbe; text-decoration:none;">' .
                        e(ucwords(strtolower($linkData['name']))) .
                        '</a>';
    
                    // Replace first occurrence only
                    $description = preg_replace(
                        '/' . preg_quote($linkData['name'], '/') . '/i',
                        $anchorTag,
                        $description,
                        1
                    );
                }
    
                return $description; // 🔑 MUST always return
            }, $disCripTion);
        }
    
        // Ensure faqData exists
        $seoTags['faqData'] = $seoTags['faqData'] ?? [];
    
        // Slug explode
        if (!empty($seoTags['slug'])) {
            $explode = explode('-', $seoTags['slug']);
        }
    
        // Route time helper
        if (!function_exists('formatRouteTime')) {
            function formatRouteTime($kms) {
                $avgSpeedKmph = 55;
                $totalHours = (float) $kms / $avgSpeedKmph;
                $hours = floor($totalHours);
                $minutes = round(($totalHours - $hours) * 60);
                return "{$hours}h {$minutes}m";
            }
        }
    
        // Slug-based title
        $slug = basename(request()->path());
        //$title = Str::title(str_replace('-', ' ', $slug));
        $title = ucfirst('chennai');
    
        $expl_slg = explode(' ', $title);
    
        $serviceWords = array_slice($expl_slg, 3);
        $serviceTitle = implode(' ', $serviceWords);
    
        $fromCity = ucfirst('chennai');
        $toCity   = ucfirst('chennai');
    
        $combination_slugs = [
            '-drop-taxi',
            '-outstation-taxi',
            '-intercity-taxi',
            '-outstation-cab',
            '-outstation-cab-service',
            '-outstation-cab-booking',
            '-outstation-taxi-service',
            '-outstation-taxi-booking',
            '-outstation-drop-taxi',
            '-taxi-for-outstation',
            '-taxi-outstation',
            '-cab-for-outstation',
            '-car-for-outstation',
            '-one-way-taxi',
            '-one-way-cab',
            '-one-way-cab-service',
            '-one-way-drop-taxi',
            '-one-way-drop-taxi-service',
            '-one-way-trip-taxi',
            '-one-drop-taxi',
            '-single-drop-taxi',
            '-best-one-way-drop-taxi',
            '-drop-taxi-service',
            '-drop-only-taxi',
            '-drop-me-taxi',
            '-drop-call-taxi',
            '-drop-taxi-one-way',
            '-drop-taxi-24x7',
            '-drop-taxi-tariff',
            '-drop-taxi-number',
            '-drop-taxi-contact-number',
            '-oneway-car-rental',
            '-oneway-cabs',
            '-oneway-travels',
            '-book-outstation-taxi',
            '-one-way-taxi-booking',
            '-one-way-cab-booking',
            '-intercity-taxi-booking',
            '-book-intercity-cab',
            '-one-way-taxi-booking',
            '-book-outstation-cab',
            '-intercity-cab-booking'
        ];
    @endphp


    @if($seoTags['page_exist'])

        <!-- Breadcrumb -->
        <section class="page-header"
            style="background-image: url('{{ asset('goride/img/carride.webp') }}');">
            <div class="page-header-shape"></div>
            <div class="container">
                <div class="row d-flex justify-content-center align-items-center">
                    <div class="col-lg-6 col-md-6 mb-30">
                        <div class="page-header-info main-banner-content mt-5 pt-5 d-none d-md-block">
                    <!--<h4>About Us!</h4>-->
                        <h1>
                            {{ $title }} One Way Drop Taxi & Outstation Cab Booking in Tamil Nadu – GoRide
                        </h1>

                    <!-- <h1></h1> -->
                    <!--<p>{{ $seoTags['metaDes'] ?? "Unlock smart mobility by leveraging the power of our next-gen cab booking software with advanced dispatch system" }}-->
                    <!--</p>-->
                    <p>
                      Book reliable taxi service in {{ $title }} with GoRide. Safe local rides, intercity travel, one-way and round-trip cabs at affordable prices with professional drivers.
                    </p>
                    <div class="banner-btn">
                        <!--<a href="#" class="default-btn-one">More About Us</a>-->
                        <!--<a href="https://www.youtube.com/watch?v=_ysd-zHamjk" class="video-btn popup-youtube">Start a Free Trail<i class="fa-solid fa-arrow-right"></i></a>-->
                    </div>
                     <div class="cs_app_btns mt-4">
                        <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer"
                           class="cs_store_pill cs_store_android" target="_blank">
                            <i class="fa-brands fa-google-play"></i>
                            <span class="cs_store_text">
                           <small>
                         Customer App 
                        <i class="fa-solid fa-download ms-1 download-anim" style="font-size:15px;"></i>
                    </small>
                                <strong>Google Play</strong>
                            </span>
                        </a>
                        <a href="https://apps.apple.com/us/app/goride-cab-bike-taxi-pool/id6763038270"
                           class="cs_store_pill cs_store_ios" target="_blank">
                            <i class="fa-brands fa-apple"></i>
                            <span class="cs_store_text">
                              <small>
                         Customer App
                        <i class="fa-solid fa-download ms-1 download-anim" style="font-size:15px;"></i>
                    </small>
                            <strong>App Store</strong>
                        </span>
                    </a>
                </div>
                    
                </div>
                    </div>
                <div class="col-md-6 col-12">
    @include('include.bookingFormStripped')
</div>
                    
                </div>
            </div>
        </section>
        <style>
  .choose-content-area H2 {
                                 
                                 font-size:16px !important;
                             }   
                             
                             .choose-content-area li {
                                 
                           list-style: disc;
    line-height: 20px;
                             }  
                                
        </style>
        
        <section class="choose-content-area px-3 d-block d-md-none">
            <h3> 
{{ $title }} One Way Drop Taxi & Outstation Cab Booking in Tamil Nadu – GoRide</h3>
                    
                    <p>
                      Book reliable taxi service in {{ $title }} with GoRide. Safe local rides, intercity travel, one-way and 
                      round-trip cabs at affordable prices with professional drivers.
                    </p>
        </section>
        <section class="choose-section  pb-5 ">
            <div class="container">
                <div class="row align-items-center mb-5 gap-5 gap-md-0">
                    <div class="col-lg-8 col-md-12 order-1 order-lg-1">
                        <div class="choose-content-area">
                            
                            <h3>Why Visit {{ ucfirst($explode[0]) }}?</h3>
                            <p>
                                {!!$seoTags['wikiDesHtml'] ?? ''!!}
                            </p>



                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 order-1 order-lg-2">
                        <div class="item aos-init aos-animate" data-aos="fade-down" data-aos-duration="1000"> <img
                                src="{{$seoTags['img'] ?? 'https://www.goride.net.in/goride/img/indian-about.jpg'}}"
                                class="img-fluid" alt="about">
                            <div class="curv-butn icon-bg">
                                <img src="https://www.goride.net.in/goride/img/g.png" alt="Play Button"
                                    style="width: 50px; height: 50px;">
                                <div class="br-left-top">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                                <div class="br-right-bottom">
                                    <svg viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-11 h-11">
                                        <path
                                            d="M11 1.54972e-06L0 0L2.38419e-07 11C1.65973e-07 4.92487 4.92487 1.62217e-06 11 1.54972e-06Z"
                                            fill="#ffffff"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row d-none justify-content-center align-items-center">
                    <div class="choose-content-area">
                        <h3>Taxi Fare Options</h3>
                    </div>
                    
                    <div class="row ">
                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car1.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>4 Seater ( Mini/Hatchback )</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i> Upto 4 Seats&nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i>₹{{ $seoTags['mini_four_seater'] ?? 'N/A' }} for up to {{ round($seoTags['mini_four_km']) }} kms</div>
                                    <!--<div class="price"></div>-->
                                </div>

                               <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                                <!--<div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->
                            </div>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car2.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>4 Seater </h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i> Upto 4 Seats&nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i>₹{{ $seoTags['four_seater'] ?? 'N/A' }} for up to {{ round($seoTags['four_km']) }} kms</div>
                                    <!--<div class="price"></div>-->
                                </div>

                             
                                          <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                                <!--<div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->
                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car3.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>6 Seater</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i> Upto 6 Seats &nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i> ₹{{ $seoTags['six_seater'] ?? 'N/A' }} for up to {{ round($seoTags['six_km']) }} kms</div>
                                    <div class="price"></div>
                                </div>
                           <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                 
                                <!-- <div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->

                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car4.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>7 Seater</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i> Upto 7 Seats &nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i>₹{{ $seoTags['seven_seater'] ?? 'N/A' }} for up to {{ round($seoTags['seven_km']) }} kms</div>
                                    <!--<div class="price"></div>-->
                                </div>
                           <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                 
                                <!--      <div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->

                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car5.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>13 Seater</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i>Upto 13 Seats &nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i> ₹{{ $seoTags['onethree_seater'] ?? 'N/A' }} for up to {{ round($seoTags['onethree_km']) }} kms</div>
                                    <!--<div class="price"></div>-->
                                </div>
                           <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                 
                                <!--     <div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->

                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car6.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>21 Seater</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i> Upto 21 Seats &nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i>₹{{ $seoTags['twoone_seater'] ?? 'N/A' }} for up to {{ round($seoTags['twoone_km']) }} kms</div>
                                    <!--<div class="price"></div>-->
                                </div>
                           <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                 
                                <!--      <div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->

                            </div>
                        </div>

                        <div class="col-md-6 col-12 mb-3">
                            <div class="cab-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                <div class="cab-left ">
                                    <img src="{{ asset('goride/img/car7.png') }}" alt="Car">
                                    <div class="cab-info">
                                        <h5>50 Seater</h5>
                                        <div class="icon-text">
                                            <i class="fa-solid fa-user-group text-danger"></i>Upto 50 Seats&nbsp;

                                        </div>
                                    </div>
                                </div>
                                <div class="cab-middle ">
                                    <div class="km-text"><i class="fa-solid fa-check text-success me-2"></i>₹{{ $seoTags['fivezero_seater'] ?? 'N/A' }} for up to {{ round($seoTags['fivezero_km']) }} kms</div>
                                    <!--<div class="price"> </div>-->
                                </div>

                                                      <a href="/booking"
           class="btn btn-warning book-btn py-1"
           target="_blank">
            Book Now <i class="fa-solid fa-car ms-1"></i>
        </a>
                 
                                <!--     <div class="col-12">-->
                                <!--    <div class="d-flex justify-content-end align-items-center gap-2">-->
                                <!--           <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"-->
                                <!--    id="app_link" style="     background: black;     border-radius: 10px; "> <img-->
                                <!--        src="https://www.goride.run/goride/img/paly-store-logo.png" alt="google-play"-->
                                <!--        style="width: 100px;"> </a> <small class="text-muted"-->
                                <!--            style="font-size:11px; font-style:italic; font-weight: 600;">-->
                                <!--            <span class="text-danger">*</span> For Drivers / Travellers / Tourists-->
                                <!--        </small></div>-->
                                <!--</div>-->

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </section>

        <section class="why-choose ">
            <div class="overlay">
                <h2 class="text-white">Why Choose GoRide?</h2>
                <div class="features">
                    <div class="feature-box">
                        <h3>✔ 24×7 Customer Support</h3>
                        <p>Always available to assist you anytime, anywhere.</p>
                    </div>
                    <div class="feature-box">
                        <h3>✔ Verified Drivers</h3>
                        <p>Experienced professionals with background checks.</p>
                    </div>
                    <div class="feature-box">
                        <h3>✔ Clean Vehicles</h3>
                        <p>Well-maintained and sanitized for your comfort.</p>
                    </div>
                    <div class="feature-box">
                        <h3>✔ Safe & Reliable</h3>
                        <p>Timely rides with trusted service standards.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="choose-section  pb-5 ">
            <div class="container">
                <div class="row align-items-center">
                    <div class="choose-content-area">
                        <h3>Top Cab Routes from   {{ ucfirst($explode[0]) }}</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <!-- Car Type Selection -->
                        <div class="car-type-section" data-aos="fade-up">
                            <label class="car-type-label">
                                Select Car Type
                                <small>To update price, included kms & extra fare</small>
                            </label>
                            <div class="row">
                                <div class="car-tabs pb-3">
                                    <button class="car-tab active" data-car="hatchback">
                                        <!-- <span class="car-icon">🚗</span> -->
                                        <span>Go Mini</span>
                                    </button>
                                    <button class="car-tab" data-car="four_seater">
                                        <!-- <span class="car-icon">🚗</span> -->
                                        <span>Go 4Seater</span>
                                    </button>
                                    <button class="car-tab" data-car="sedan">
                                        <!-- <span class="car-icon">🚙</span> -->
                                        <span>Go 6Seater</span>
                                    </button>
                                    <button class="car-tab" data-car="suv">
                                        <!-- <span class="car-icon">🚐</span> -->
                                        <span>Go 7Seater</span>
                                    </button>
                                    <button class="car-tab d-none" data-car="premium">
                                        <!-- <span class="car-icon">✨</span> -->
                                        <span>13 Seater</span>
                                    </button>
                                    <button class="car-tab d-none" data-car="xl">
                                        <!-- <span class="car-icon">🚌</span> -->
                                        <span>21 Seater</span>
                                    </button>
                                    <button class="car-tab d-none" data-car="luxury">
                                        <!-- <span class="car-icon">👑</span> -->
                                        <span>50 Seater</span>
                                    </button>
                                </div>
                            </div>
                            <div class="row gap-3 gap-md-0">
                                <div class="col-md-5 col-12">
                                    <div class="filter-section" data-aos="fade-up">
                                        <label class="filter-label">Select Destination District</label>
                                        <select class="district-dropdown" id="districtSelect">
                                            @if(isset($seoTags['innerLinks']))
                                                @foreach($seoTags['innerLinks'] as $key => $value)
                                                    <option value="{{strtolower($value['to_place'])}}">{{$value['name'] . ' - '.$value['to_place']}}</option>
                                                @endforeach
                                            @else
                                                <option>No more routes</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7 col-12">
                                    <div class="routes-section">
                                        <div class="routes-grid" id="routesContainer">
                                            <!-- Cards will be dynamically inserted here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- Routes Cards -->

                        </div>
                    </div>



                </div>
            </div>

        </section>
        
        
        
        @if(!empty($seoTags['innerLinks']))
            <section class="goride-route-tags">
                <div class="container">
                     <div class="choose-content-area">
                    <h3 style="max-width:100%;">
                        Top Locations Under <span class="text-warning fs-3">Goride's</span> Service Network
                    </h3>
                    </div>
            
                    <div class="route-tags mt-3">
                        @foreach($seoTags['innerLinks'] as $value)
                            @php
                                $words = [
                                    'Taxi',
                                    'One Way Taxi',
                                    'Round Trip Taxi',
                                    'Taxi Cab',
                                    'Cab',
                                    'Round Trip Cab',
                                    'One Way Cab'
                                ];
                                $random = $words[array_rand($words)];
                            @endphp
            
                            <a href="/{{ strtolower($value['slug']) }}"><i class="fa-solid fa-location-dot text-danger me-2"></i>
                                {{ ucwords(strtolower($value['name'])) }} to {{ ucwords(strtolower($value['to_place'])) }} {{ $random }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
        <section class="goride-route-tags">
                <div class="container">
                     <div class="choose-content-area">
                    <h3 style="max-width:100%;">One Way &amp; Return {{ $serviceTitle }} from {{ $fromCity }}</h3>
                    </div>
            
                    <div class="route-tags mt-3">
                        @foreach ($combination_slugs as $slugSuffix)
                            @php
                                $exp = Str::title(str_replace('-', ' ', $slugSuffix));
                            @endphp
                    
                            <a href="https://www.goride.run/taxi/{{ strtolower($fromCity) }}{{ $slugSuffix }}">
                                <i class="fa-solid fa-location-dot text-danger me-2"></i>
                                {{ $fromCity }} {{ $exp }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
            

        @if (count($seoTags['faqData']) > 0)

            <section class="tab-section pt-5 pb-5">
                <div class="container" id="faq">
                    <div class="section-title">

                        <h3>FAQ</h3>
                    </div>
                    <div class="tab boosting-list-tab">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-10">
                                <ul class="accordion-box clearfix">

                                    @foreach ($seoTags['faqData'] as $key)
                                        <li class="accordion block">
                                            <div class="acc-btn"><span class="count">{{ $loop->iteration }}.</span>
                                                {{ $key['question'] ?? ''}}</div>
                                            <div class="acc-content" style="display: none;">
                                                <div class="content">
                                                    <div class="text">{{$key['answer'] ?? ''}} </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        @endif
        
        <section class="goride-route-tags">
            <div class="container">
                 <div class="choose-content-area">
                <h3 style="max-width:100%;">GoRide Taxi Services Across</h3>
                </div>
        
                <div class="mt-3">
                    <div class="service-item">
                        <h5>Outstation Taxi Service</h5>
                        <p>
                            We provide reliable outstation taxi booking for comfortable long-distance 
                            travel between cities across Tamil Nadu. Enjoy smooth highway rides, 
                            experienced drivers, and well-maintained vehicles with transparent pricing 
                            and no hidden charges.
                        </p>
                    </div>
            
                    <div class="service-item">
                        <h5>One Way Cab Service</h5>
                        <p>
                            Book affordable one way taxi rides with GoRide and pay only for the 
                            distance you travel. Our one side cab service eliminates return fare 
                            charges, making intercity travel economical and convenient.
                        </p>
                    </div>
            
                    <div class="service-item">
                        <h5>Intercity Cab Booking</h5>
                        <p>
                            GoRide offers safe and hassle-free intercity cab services connecting 
                            major cities and districts across Tamil Nadu. Whether for business 
                            travel, family trips, or weekend getaways, we ensure comfortable and 
                            punctual transportation.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="tab-section pt-5 pb-5">
                <div class="container" id="faq">
                    <div class="section-title">

                        <h3>FAQ</h3>
                    </div>
                    <div class="tab boosting-list-tab">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-10">
                                <ul class="accordion-box clearfix">

                                    <li class="accordion block">
                                        <div class="acc-btn"><span class="count">1.</span>
                                            What is the best one way drop taxi service in {{ $title }}?</div>
                                        <div class="acc-content" style="display: none;">
                                            <div class="content">
                                                <div class="text">GoRide provides reliable one way drop taxi service in {{ $title }} 
                                                with affordable fixed pricing for intercity travel across Tamil Nadu. 
                                                You can book one way and round trip cabs from {{ $title }} 
                                                to all other major cities with professional drivers and clean sedan vehicles.</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="accordion block">
                                        <div class="acc-btn"><span class="count">2.</span>
                                            How can I book an outstation cab from {{ $title }}?</div>
                                        <div class="acc-content" style="display: none;">
                                            <div class="content">
                                                <div class="text">You can book an outstation cab from {{ $title }} instantly 
                                                through GoRide’s online taxi booking platform. Enter your pickup 
                                                location, destination, travel date, and time to get an instant quote. 
                                                We offer transparent pricing with no hidden charges..</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="accordion block">
                                        <div class="acc-btn"><span class="count">3.</span>
                                            What are the taxi charges from {{ $title }}?</div>
                                        <div class="acc-content" style="display: none;">
                                            <div class="content">
                                                <div class="text">Taxi charges from {{ $title }} depend on the travel distance, 
                                                trip type (one way or round trip), and vehicle selection. 
                                                GoRide offers affordable fixed pricing with no hidden costs, 
                                                ensuring budget-friendly intercity and outstation travel.</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="accordion block">
                                        <div class="acc-btn"><span class="count">4.</span>
                                            Which cities can I travel to from {{ $title }} with GoRide?</div>
                                        <div class="acc-content" style="display: none;">
                                            <div class="content">
                                                <div class="text">GoRide offers taxi service from {{ $title }} to all major 
                                                cities and districts in Tamil Nadu including Chennai, Madurai, 
                                                Coimbatore, Tiruchirappalli, Salem, Vellore, Tirunelveli, 
                                                Thanjavur, Nilgiris, Kanyakumari, and more. 
                                                Both one way drop taxis and round trip cabs are available.</div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="accordion block">
                                        <div class="acc-btn"><span class="count">5.</span>
                                            Why should I choose GoRide {{ $title }} taxi service?</div>
                                        <div class="acc-content" style="display: none;">
                                            <div class="content">
                                                <div class="text">GoRide {{ $title }} taxi service offers one way and round trip options, 
                                                affordable fixed pricing, no hidden charges, professional and verified drivers, 
                                                clean and comfortable sedan cabs, 24/7 booking support, 
                                                and doorstep pickup anywhere in {{ $title }}. 
                                                We ensure safe and reliable travel for local and long-distance journeys.</div>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

    @else
        <div
            style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; text-align: center; padding: 40px; font-family: 'Arial', sans-serif; background-color: #f9f9f9;margin-top:8%;">
            <img src="{{ asset('goride/img/logo-light.png') }}" alt="Page Not Found"
                style="max-width: 350px; width: 100%; margin-bottom: 30px; animation: float 3s ease-in-out infinite;">

            <h1 style="font-size: 48px; font-weight: 700; color: #333; margin-bottom: 15px;">404</h1>
            <h2 style="font-size: 28px; font-weight: 500; color: #555; margin-bottom: 20px;">Oops! Page Not Found</h2>
            <p style="font-size: 18px; color: #777; max-width: 500px; margin-bottom: 30px;">
                The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
            </p>

            <a href="{{ url('/') }}"
                style="display: inline-block; padding: 12px 30px; background: linear-gradient(90deg, #007bff, #00c6ff); color: #fff; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                Go Back Home
            </a>
        </div>

        <style>
            @keyframes float {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            body {
                background: #000000;
            }
            
            
        </style>
    @endif

@php
    $jsRoutesData = [];

    $carTypeMap = [
        'hatchback' => 'mini_four_seater',
        'four_seater' => 'four_seater',
        'sedan'     => 'six_seater',
        'suv'       => 'seven_seater',
        'premium'   => 'onethree_seater',
        'xl'        => 'twoone_seater',
        'luxury'    => 'fivezero_seater'
    ];

    $extraFareMap = [
        'hatchback' => '₹12/km',
        'four_seater' => '₹13/km',
        'sedan'     => '₹17/km',
        'suv'       => '₹19/km',
        'premium'   => '₹26/km',
        'xl'        => '₹28/km',
        'luxury'    => '₹75/km'
    ];

    
    //dd($seoTags['innerLinks']);
    if (isset($seoTags['innerLinks']) && is_array($seoTags['innerLinks'])) {
        foreach ($seoTags['innerLinks'] as $route) {
            // The main key is the lowercase 'to_place'
            $toPlaceKey = strtolower($route['to_place']);
            $jsRoutesData[$toPlaceKey] = [];
            
            $kms = (float) $route['kms'];
            $timeStr = formatRouteTime($kms);

            // Build the data for each car type
            foreach ($carTypeMap as $jsCarType => $phpColumn) {
                if (!isset($route[$phpColumn])) continue; 
                
                $explode1 = explode('_', $phpColumn);

                // Remove the last element ('seater')
                array_pop($explode1);
                
                // Join back to get 'mini_four'
                $bal_arr = implode('_', $explode1) . '_kms';
                
                $price = (int) $route[$phpColumn];
                $inc_kms = (int) $route[$bal_arr];
                
                // Calculate originalPrice and taxes (based on your static JS logic)
                $originalPrice = round($price * 1.15); // ~15% markup
                $taxes = round($price * 0.12);         // 12% tax

                $jsRoutesData[$toPlaceKey][$jsCarType] = [
                    'distance'      => $kms,
                    'time'          => $timeStr,
                    'included'      => $kms,
                    'extraFare'     => $extraFareMap[$jsCarType] . ' after ' . $inc_kms,
                    'price'         => $price,
                    'originalPrice' => $originalPrice,
                    'taxes'         => $taxes,
                    'inc_kms'         => $inc_kms,
                ];
            }
        }
    }
@endphp



@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script>
        
        $(document).ready(function () {

            // const routesData = @json($seoTags['innerLinks']);
            // Initialize AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: true,
                offset: 100
            });

            // Routes Data
            // const routesData = {
            //     dindigul: {
            //         hatchback: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹12/km', price: 1450, originalPrice: 1650, taxes: 174 },
            //         sedan: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹14/km', price: 1750, originalPrice: 1950, taxes: 210 },
            //         suv: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹16/km', price: 2050, originalPrice: 2350, taxes: 246 },
            //         premium: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹18/km', price: 2350, originalPrice: 2650, taxes: 282 },
            //         xl: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹20/km', price: 2650, originalPrice: 2950, taxes: 318 },
            //         luxury: { distance: 145, time: '2h 45m', included: 145, extraFare: '₹25/km', price: 3150, originalPrice: 3550, taxes: 378 }
            //     },
            //     coimbatore: {
            //         hatchback: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹12/km', price: 1950, originalPrice: 2250, taxes: 234 },
            //         sedan: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹14/km', price: 2350, originalPrice: 2650, taxes: 282 },
            //         suv: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹16/km', price: 2750, originalPrice: 3150, taxes: 330 },
            //         premium: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹18/km', price: 3150, originalPrice: 3550, taxes: 378 },
            //         xl: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹20/km', price: 3550, originalPrice: 3950, taxes: 426 },
            //         luxury: { distance: 210, time: '3h 30m', included: 210, extraFare: '₹25/km', price: 4250, originalPrice: 4750, taxes: 510 }
            //     },
            //     perambalur: {
            //         hatchback: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹12/km', price: 950, originalPrice: 1150, taxes: 114 },
            //         sedan: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹14/km', price: 1150, originalPrice: 1350, taxes: 138 },
            //         suv: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹16/km', price: 1350, originalPrice: 1550, taxes: 162 },
            //         premium: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹18/km', price: 1550, originalPrice: 1750, taxes: 186 },
            //         xl: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹20/km', price: 1750, originalPrice: 1950, taxes: 210 },
            //         luxury: { distance: 95, time: '1h 50m', included: 95, extraFare: '₹25/km', price: 2050, originalPrice: 2350, taxes: 246 }
            //     },
            //     kanniyakumari: {
            //         hatchback: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹12/km', price: 5850, originalPrice: 6650, taxes: 702 },
            //         sedan: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹14/km', price: 7050, originalPrice: 7850, taxes: 846 },
            //         suv: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹16/km', price: 8250, originalPrice: 9050, taxes: 990 },
            //         premium: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹18/km', price: 9450, originalPrice: 10250, taxes: 1134 },
            //         xl: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹20/km', price: 10650, originalPrice: 11450, taxes: 1278 },
            //         luxury: { distance: 650, time: '10h 15m', included: 650, extraFare: '₹25/km', price: 12750, originalPrice: 13550, taxes: 1530 }
            //     },
            //     salem: {
            //         hatchback: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹12/km', price: 2050, originalPrice: 2350, taxes: 246 },
            //         sedan: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹14/km', price: 2450, originalPrice: 2750, taxes: 294 },
            //         suv: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹16/km', price: 2850, originalPrice: 3250, taxes: 342 },
            //         premium: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹18/km', price: 3250, originalPrice: 3650, taxes: 390 },
            //         xl: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹20/km', price: 3650, originalPrice: 4050, taxes: 438 },
            //         luxury: { distance: 220, time: '3h 45m', included: 220, extraFare: '₹25/km', price: 4350, originalPrice: 4850, taxes: 522 }
            //     },
            //     cuddalore: {
            //         hatchback: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹12/km', price: 1350, originalPrice: 1550, taxes: 162 },
            //         sedan: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹14/km', price: 1650, originalPrice: 1850, taxes: 198 },
            //         suv: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹16/km', price: 1950, originalPrice: 2250, taxes: 234 },
            //         premium: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹18/km', price: 2250, originalPrice: 2550, taxes: 270 },
            //         xl: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹20/km', price: 2550, originalPrice: 2850, taxes: 306 },
            //         luxury: { distance: 145, time: '2h 30m', included: 145, extraFare: '₹25/km', price: 3050, originalPrice: 3450, taxes: 366 }
            //     },
            //     tirupathur: {
            //         hatchback: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹12/km', price: 1300, originalPrice: 1500, taxes: 156 },
            //         sedan: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹14/km', price: 1600, originalPrice: 1800, taxes: 192 },
            //         suv: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹16/km', price: 1900, originalPrice: 2200, taxes: 228 },
            //         premium: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹18/km', price: 2200, originalPrice: 2500, taxes: 264 },
            //         xl: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹20/km', price: 2500, originalPrice: 2800, taxes: 300 },
            //         luxury: { distance: 140, time: '2h 20m', included: 140, extraFare: '₹25/km', price: 3000, originalPrice: 3400, taxes: 360 }
            //     },
            //     nilgiris: {
            //         hatchback: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹12/km', price: 850, originalPrice: 1050, taxes: 102 },
            //         sedan: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹14/km', price: 1050, originalPrice: 1250, taxes: 126 },
            //         suv: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹16/km', price: 1250, originalPrice: 1450, taxes: 150 },
            //         premium: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹18/km', price: 1450, originalPrice: 1650, taxes: 174 },
            //         xl: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹20/km', price: 1650, originalPrice: 1850, taxes: 198 },
            //         luxury: { distance: 85, time: '1h 45m', included: 85, extraFare: '₹25/km', price: 1950, originalPrice: 2250, taxes: 234 }
            //     },
            //     ariyalur: {
            //         hatchback: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹12/km', price: 1050, originalPrice: 1250, taxes: 126 },
            //         sedan: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹14/km', price: 1300, originalPrice: 1500, taxes: 156 },
            //         suv: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹16/km', price: 1550, originalPrice: 1750, taxes: 186 },
            //         premium: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹18/km', price: 1800, originalPrice: 2000, taxes: 216 },
            //         xl: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹20/km', price: 2050, originalPrice: 2250, taxes: 246 },
            //         luxury: { distance: 110, time: '2h 10m', included: 110, extraFare: '₹25/km', price: 2450, originalPrice: 2750, taxes: 294 }
            //     },
            //     kancheepuram: {
            //         hatchback: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹12/km', price: 650, originalPrice: 850, taxes: 78 },
            //         sedan: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹14/km', price: 800, originalPrice: 1000, taxes: 96 },
            //         suv: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹16/km', price: 950, originalPrice: 1150, taxes: 114 },
            //         premium: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹18/km', price: 1100, originalPrice: 1300, taxes: 132 },
            //         xl: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹20/km', price: 1250, originalPrice: 1450, taxes: 150 },
            //         luxury: { distance: 65, time: '1h 15m', included: 65, extraFare: '₹25/km', price: 1500, originalPrice: 1750, taxes: 180 }
            //     },
            //     nagapattinam: {
            //         hatchback: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹12/km', price: 2450, originalPrice: 2750, taxes: 294 },
            //         sedan: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹14/km', price: 2950, originalPrice: 3250, taxes: 354 },
            //         suv: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹16/km', price: 3450, originalPrice: 3850, taxes: 414 },
            //         premium: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹18/km', price: 3950, originalPrice: 4350, taxes: 474 },
            //         xl: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹20/km', price: 4450, originalPrice: 4850, taxes: 534 },
            //         luxury: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹25/km', price: 5350, originalPrice: 5850, taxes: 642 }
            //     },
            //     erode: {
            //         hatchback: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹12/km', price: 1850, originalPrice: 2150, taxes: 222 },
            //         sedan: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹14/km', price: 2250, originalPrice: 2550, taxes: 270 },
            //         suv: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹16/km', price: 2650, originalPrice: 3050, taxes: 318 },
            //         premium: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹18/km', price: 3050, originalPrice: 3450, taxes: 366 },
            //         xl: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹20/km', price: 3450, originalPrice: 3850, taxes: 414 },
            //         luxury: { distance: 195, time: '3h 15m', included: 195, extraFare: '₹25/km', price: 4150, originalPrice: 4650, taxes: 498 }
            //     },
            //     thiruvallur: {
            //         hatchback: { distance: 45, time: '50m', included: 45, extraFare: '₹12/km', price: 450, originalPrice: 650, taxes: 54 },
            //         sedan: { distance: 45, time: '50m', included: 45, extraFare: '₹14/km', price: 550, originalPrice: 750, taxes: 66 },
            //         suv: { distance: 45, time: '50m', included: 45, extraFare: '₹16/km', price: 650, originalPrice: 850, taxes: 78 },
            //         premium: { distance: 45, time: '50m', included: 45, extraFare: '₹18/km', price: 750, originalPrice: 950, taxes: 90 },
            //         xl: { distance: 45, time: '50m', included: 45, extraFare: '₹20/km', price: 850, originalPrice: 1050, taxes: 102 },
            //         luxury: { distance: 45, time: '50m', included: 45, extraFare: '₹25/km', price: 1050, originalPrice: 1250, taxes: 126 }
            //     },
            //     karur: {
            //         hatchback: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹12/km', price: 2150, originalPrice: 2450, taxes: 258 },
            //         sedan: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹14/km', price: 2600, originalPrice: 2900, taxes: 312 },
            //         suv: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹16/km', price: 3050, originalPrice: 3450, taxes: 366 },
            //         premium: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹18/km', price: 3500, originalPrice: 3900, taxes: 420 },
            //         xl: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹20/km', price: 3950, originalPrice: 4350, taxes: 474 },
            //         luxury: { distance: 240, time: '4h 00m', included: 240, extraFare: '₹25/km', price: 4750, originalPrice: 5250, taxes: 570 }
            //     },
            //     kallakurichi: {
            //         hatchback: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹12/km', price: 1500, originalPrice: 1700, taxes: 180 },
            //         sedan: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹14/km', price: 1800, originalPrice: 2000, taxes: 216 },
            //         suv: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹16/km', price: 2100, originalPrice: 2400, taxes: 252 },
            //         premium: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹18/km', price: 2400, originalPrice: 2700, taxes: 288 },
            //         xl: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹20/km', price: 2700, originalPrice: 3000, taxes: 324 },
            //         luxury: { distance: 155, time: '2h 45m', included: 155, extraFare: '₹25/km', price: 3250, originalPrice: 3650, taxes: 390 }
            //     },
            //     krishnagiri: {
            //         hatchback: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹12/km', price: 1200, originalPrice: 1400, taxes: 144 },
            //         sedan: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹14/km', price: 1450, originalPrice: 1650, taxes: 174 },
            //         suv: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹16/km', price: 1700, originalPrice: 1950, taxes: 204 },
            //         premium: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹18/km', price: 1950, originalPrice: 2250, taxes: 234 },
            //         xl: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹20/km', price: 2200, originalPrice: 2500, taxes: 264 },
            //         luxury: { distance: 125, time: '2h 15m', included: 125, extraFare: '₹25/km', price: 2650, originalPrice: 3000, taxes: 318 }
            //     },
            //     tirunelveli: {
            //         hatchback: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹12/km', price: 5250, originalPrice: 5950, taxes: 630 },
            //         sedan: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹14/km', price: 6350, originalPrice: 7050, taxes: 762 },
            //         suv: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹16/km', price: 7450, originalPrice: 8150, taxes: 894 },
            //         premium: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹18/km', price: 8550, originalPrice: 9250, taxes: 1026 },
            //         xl: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹20/km', price: 9650, originalPrice: 10350, taxes: 1158 },
            //         luxury: { distance: 580, time: '9h 30m', included: 580, extraFare: '₹25/km', price: 11550, originalPrice: 12250, taxes: 1386 }
            //     },
            //     dharmapuri: {
            //         hatchback: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹12/km', price: 1250, originalPrice: 1450, taxes: 150 },
            //         sedan: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹14/km', price: 1550, originalPrice: 1750, taxes: 186 },
            //         suv: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹16/km', price: 1850, originalPrice: 2150, taxes: 222 },
            //         premium: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹18/km', price: 2150, originalPrice: 2450, taxes: 258 },
            //         xl: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹20/km', price: 2450, originalPrice: 2750, taxes: 294 },
            //         luxury: { distance: 135, time: '2h 20m', included: 135, extraFare: '₹25/km', price: 2950, originalPrice: 3350, taxes: 354 }
            //     },
            //     viluppuram: {
            //         hatchback: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹12/km', price: 1300, originalPrice: 1500, taxes: 156 },
            //         sedan: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹14/km', price: 1600, originalPrice: 1800, taxes: 192 },
            //         suv: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹16/km', price: 1900, originalPrice: 2200, taxes: 228 },
            //         premium: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹18/km', price: 2200, originalPrice: 2500, taxes: 264 },
            //         xl: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹20/km', price: 2500, originalPrice: 2800, taxes: 300 },
            //         luxury: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹25/km', price: 3000, originalPrice: 3400, taxes: 360 }
            //     },
            //     thanjavur: {
            //         hatchback: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹12/km', price: 2750, originalPrice: 3050, taxes: 330 },
            //         sedan: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹14/km', price: 3300, originalPrice: 3600, taxes: 396 },
            //         suv: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹16/km', price: 3850, originalPrice: 4250, taxes: 462 },
            //         premium: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹18/km', price: 4400, originalPrice: 4800, taxes: 528 },
            //         xl: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹20/km', price: 4950, originalPrice: 5350, taxes: 594 },
            //         luxury: { distance: 310, time: '5h 00m', included: 310, extraFare: '₹25/km', price: 5950, originalPrice: 6450, taxes: 714 }
            //     },
            //     chengalpattu: {
            //         hatchback: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹12/km', price: 550, originalPrice: 750, taxes: 66 },
            //         sedan: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹14/km', price: 650, originalPrice: 850, taxes: 78 },
            //         suv: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹16/km', price: 750, originalPrice: 950, taxes: 90 },
            //         premium: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹18/km', price: 850, originalPrice: 1050, taxes: 102 },
            //         xl: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹20/km', price: 950, originalPrice: 1150, taxes: 114 },
            //         luxury: { distance: 55, time: '1h 00m', included: 55, extraFare: '₹25/km', price: 1150, originalPrice: 1350, taxes: 138 }
            //     },
            //     sivaganga: {
            //         hatchback: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹12/km', price: 3750, originalPrice: 4150, taxes: 450 },
            //         sedan: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹14/km', price: 4500, originalPrice: 4900, taxes: 540 },
            //         suv: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹16/km', price: 5250, originalPrice: 5750, taxes: 630 },
            //         premium: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹18/km', price: 6000, originalPrice: 6500, taxes: 720 },
            //         xl: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹20/km', price: 6750, originalPrice: 7250, taxes: 810 },
            //         luxury: { distance: 420, time: '6h 45m', included: 420, extraFare: '₹25/km', price: 8100, originalPrice: 8700, taxes: 972 }
            //     },
            //     ramanathapuram: {
            //         hatchback: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹12/km', price: 4300, originalPrice: 4750, taxes: 516 },
            //         sedan: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹14/km', price: 5150, originalPrice: 5650, taxes: 618 },
            //         suv: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹16/km', price: 6000, originalPrice: 6550, taxes: 720 },
            //         premium: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹18/km', price: 6850, originalPrice: 7400, taxes: 822 },
            //         xl: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹20/km', price: 7700, originalPrice: 8250, taxes: 924 },
            //         luxury: { distance: 480, time: '7h 45m', included: 480, extraFare: '₹25/km', price: 9250, originalPrice: 9900, taxes: 1110 }
            //     },
            //     vellore: {
            //         hatchback: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹12/km', price: 1300, originalPrice: 1500, taxes: 156 },
            //         sedan: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹14/km', price: 1600, originalPrice: 1800, taxes: 192 },
            //         suv: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹16/km', price: 1900, originalPrice: 2200, taxes: 228 },
            //         premium: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹18/km', price: 2200, originalPrice: 2500, taxes: 264 },
            //         xl: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹20/km', price: 2500, originalPrice: 2800, taxes: 300 },
            //         luxury: { distance: 140, time: '2h 30m', included: 140, extraFare: '₹25/km', price: 3000, originalPrice: 3400, taxes: 360 }
            //     },
            //     tenkasi: {
            //         hatchback: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹12/km', price: 5050, originalPrice: 5750, taxes: 606 },
            //         sedan: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹14/km', price: 6100, originalPrice: 6800, taxes: 732 },
            //         suv: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹16/km', price: 7150, originalPrice: 7950, taxes: 858 },
            //         premium: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹18/km', price: 8200, originalPrice: 9000, taxes: 984 },
            //         xl: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹20/km', price: 9250, originalPrice: 10050, taxes: 1110 },
            //         luxury: { distance: 560, time: '9h 15m', included: 560, extraFare: '₹25/km', price: 11100, originalPrice: 11950, taxes: 1332 }
            //     },
            //     thiruvarur: {
            //         hatchback: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹12/km', price: 2450, originalPrice: 2750, taxes: 294 },
            //         sedan: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹14/km', price: 2950, originalPrice: 3250, taxes: 354 },
            //         suv: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹16/km', price: 3450, originalPrice: 3850, taxes: 414 },
            //         premium: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹18/km', price: 3950, originalPrice: 4350, taxes: 474 },
            //         xl: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹20/km', price: 4450, originalPrice: 4850, taxes: 534 },
            //         luxury: { distance: 280, time: '4h 30m', included: 280, extraFare: '₹25/km', price: 5350, originalPrice: 5850, taxes: 642 }
            //     },
            //     thoothukudi: {
            //         hatchback: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹12/km', price: 5600, originalPrice: 6300, taxes: 672 },
            //         sedan: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹14/km', price: 6750, originalPrice: 7450, taxes: 810 },
            //         suv: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹16/km', price: 7900, originalPrice: 8650, taxes: 948 },
            //         premium: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹18/km', price: 9050, originalPrice: 9800, taxes: 1086 },
            //         xl: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹20/km', price: 10200, originalPrice: 11000, taxes: 1224 },
            //         luxury: { distance: 620, time: '10h 00m', included: 620, extraFare: '₹25/km', price: 12250, originalPrice: 13100, taxes: 1470 }
            //     },
            //     tiruvannamalai: {
            //         hatchback: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹12/km', price: 1650, originalPrice: 1850, taxes: 198 },
            //         sedan: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹14/km', price: 2000, originalPrice: 2200, taxes: 240 },
            //         suv: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹16/km', price: 2350, originalPrice: 2650, taxes: 282 },
            //         premium: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹18/km', price: 2700, originalPrice: 3000, taxes: 324 },
            //         xl: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹20/km', price: 3050, originalPrice: 3400, taxes: 366 },
            //         luxury: { distance: 175, time: '3h 00m', included: 175, extraFare: '₹25/km', price: 3650, originalPrice: 4050, taxes: 438 }
            //     },
            //     pudukkottai: {
            //         hatchback: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹12/km', price: 1850, originalPrice: 2150, taxes: 222 },
            //         sedan: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹14/km', price: 2250, originalPrice: 2550, taxes: 270 },
            //         suv: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹16/km', price: 2650, originalPrice: 3050, taxes: 318 },
            //         premium: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹18/km', price: 3050, originalPrice: 3450, taxes: 366 },
            //         xl: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹20/km', price: 3450, originalPrice: 3850, taxes: 414 },
            //         luxury: { distance: 200, time: '3h 30m', included: 200, extraFare: '₹25/km', price: 4150, originalPrice: 4650, taxes: 498 }
            //     },
            //     ranipet: {
            //         hatchback: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹12/km', price: 1200, originalPrice: 1400, taxes: 144 },
            //         sedan: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹14/km', price: 1450, originalPrice: 1650, taxes: 174 },
            //         suv: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹16/km', price: 1700, originalPrice: 1950, taxes: 204 },
            //         premium: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹18/km', price: 1950, originalPrice: 2250, taxes: 234 },
            //         xl: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹20/km', price: 2200, originalPrice: 2500, taxes: 264 },
            //         luxury: { distance: 130, time: '2h 15m', included: 130, extraFare: '₹25/km', price: 2650, originalPrice: 3000, taxes: 318 }
            //     },
            //     namakkal: {
            //         hatchback: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹12/km', price: 2350, originalPrice: 2650, taxes: 282 },
            //         sedan: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹14/km', price: 2850, originalPrice: 3150, taxes: 342 },
            //         suv: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹16/km', price: 3350, originalPrice: 3750, taxes: 402 },
            //         premium: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹18/km', price: 3850, originalPrice: 4250, taxes: 462 },
            //         xl: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹20/km', price: 4350, originalPrice: 4750, taxes: 522 },
            //         luxury: { distance: 260, time: '4h 15m', included: 260, extraFare: '₹25/km', price: 5250, originalPrice: 5750, taxes: 630 }
            //     },
            //     theni: {
            //         hatchback: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹12/km', price: 2850, originalPrice: 3150, taxes: 342 },
            //         sedan: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹14/km', price: 3450, originalPrice: 3750, taxes: 414 },
            //         suv: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹16/km', price: 4050, originalPrice: 4450, taxes: 486 },
            //         premium: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹18/km', price: 4650, originalPrice: 5050, taxes: 558 },
            //         xl: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹20/km', price: 5250, originalPrice: 5750, taxes: 630 },
            //         luxury: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹25/km', price: 6300, originalPrice: 6900, taxes: 756 }
            //     },
            //     virudhunagar: {
            //         hatchback: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹12/km', price: 4500, originalPrice: 4950, taxes: 540 },
            //         sedan: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹14/km', price: 5400, originalPrice: 5900, taxes: 648 },
            //         suv: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹16/km', price: 6300, originalPrice: 6900, taxes: 756 },
            //         premium: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹18/km', price: 7200, originalPrice: 7800, taxes: 864 },
            //         xl: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹20/km', price: 8100, originalPrice: 8700, taxes: 972 },
            //         luxury: { distance: 500, time: '8h 00m', included: 500, extraFare: '₹25/km', price: 9750, originalPrice: 10450, taxes: 1170 }
            //     },
            //     tiruppur: {
            //         hatchback: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹12/km', price: 1700, originalPrice: 1950, taxes: 204 },
            //         sedan: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹14/km', price: 2050, originalPrice: 2350, taxes: 246 },
            //         suv: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹16/km', price: 2400, originalPrice: 2750, taxes: 288 },
            //         premium: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹18/km', price: 2750, originalPrice: 3100, taxes: 330 },
            //         xl: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹20/km', price: 3100, originalPrice: 3500, taxes: 372 },
            //         luxury: { distance: 180, time: '3h 00m', included: 180, extraFare: '₹25/km', price: 3750, originalPrice: 4200, taxes: 450 }
            //     },
            //     mayiladuthurai: {
            //         hatchback: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹12/km', price: 2550, originalPrice: 2850, taxes: 306 },
            //         sedan: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹14/km', price: 3100, originalPrice: 3400, taxes: 372 },
            //         suv: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹16/km', price: 3650, originalPrice: 4000, taxes: 438 },
            //         premium: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹18/km', price: 4200, originalPrice: 4600, taxes: 504 },
            //         xl: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹20/km', price: 4750, originalPrice: 5150, taxes: 570 },
            //         luxury: { distance: 290, time: '4h 45m', included: 290, extraFare: '₹25/km', price: 5700, originalPrice: 6150, taxes: 684 }
            //     },
            //     tiruchirappalli: {
            //         hatchback: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹12/km', price: 2850, originalPrice: 3150, taxes: 342 },
            //         sedan: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹14/km', price: 3450, originalPrice: 3750, taxes: 414 },
            //         suv: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹16/km', price: 4050, originalPrice: 4450, taxes: 486 },
            //         premium: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹18/km', price: 4650, originalPrice: 5050, taxes: 558 },
            //         xl: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹20/km', price: 5250, originalPrice: 5750, taxes: 630 },
            //         luxury: { distance: 320, time: '5h 15m', included: 320, extraFare: '₹25/km', price: 6300, originalPrice: 6900, taxes: 756 }
            //     }
            // };

            // let currentCarType = 'hatchback';
            // let currentDistrict = 'dindigul';

            // // Car tab click handler
            // document.querySelectorAll('.car-tab').forEach(tab => {
            //     tab.addEventListener('click', function () {
            //         document.querySelectorAll('.car-tab').forEach(t => t.classList.remove('active'));
            //         this.classList.add('active');
            //         currentCarType = this.dataset.car;
            //         renderRoutes();
            //     });
            // });

            // // District dropdown change handler
            // document.getElementById('districtSelect').addEventListener('change', function () {
            //     currentDistrict = this.value;
            //     renderRoutes();
            // });

            // // Render routes based on selected car type and district
            // function renderRoutes() {
            //     const container = document.getElementById('routesContainer');
            //     container.innerHTML = '';

            //     const districtData = routesData[currentDistrict];
            //     if (!districtData) return;

            //     const carData = districtData[currentCarType];
            //     if (!carData) return;

            //     // Get district name from dropdown
            //     const districtName = document.getElementById('districtSelect').options[document.getElementById('districtSelect').selectedIndex].text;

            //     const card = document.createElement('div');
            //     card.className = 'route-card';
            //     card.setAttribute('data-aos', 'fade-up');
            //     card.innerHTML = `
            //         <div class="route-header">
            //             <div class="route-title">${districtName}</div>
            //             <div class="route-distance">${carData.distance} kms | ${carData.time}</div>
            //         </div>
            //         <div class="route-body">
            //             <div class="info-row">
            //                 <span class="info-label">Included KMs</span>
            //                 <span class="info-value included-kms">✓ ${carData.included} kms included</span>
            //             </div>
            //             <div class="info-row">
            //                 <span class="info-label">Extra Fare</span>
            //                 <span class="info-value extra-fare">${carData.extraFare} after ${carData.included} kms</span>
            //             </div>
            //             <div class="row d-flex justify-content-center align-items-center" >
            //             <div class="price-section">
            //                 <div class="col-md-4 col-12"><div class="price mb-3 mb-md-0">₹${carData.price} <span class="original-price">₹${carData.originalPrice}</span></div></div>
            //                   <div class="col-md-4 col-12"> <div class="taxes-info mb-3 mb-md-0">+₹${carData.taxes} (Taxes & Charges)</div></div>
            //                   <div class="col-md-3 col-12"><button class="theme-btn">VIEW CABS</button></div>
            //             </div>
            //             </div>

            //         </div>
            //     `;

            //     container.appendChild(card);
            //     AOS.refresh();
            // }

            // // Initial render
            // renderRoutes();
            @if (count($seoTags['faqData']) > 0)
                $('ul.accordion-box.clearfix li').first().find('.acc-btn').click();
            @endif

            $('.theme-btn').on('click', function () {
                window.open('/jobs', '_blank'); // opens in new tab
            });
            
            // 1. DYNAMICALLY GENERATE routesData from your PHP array
            const routesData = @json($jsRoutesData);
        
            // 2. YOUR EXISTING JAVASCRIPT LOGIC (Unchanged)
            let currentCarType = 'hatchback';
            let currentDistrict = document.getElementById('districtSelect').value; // Get initial value
        
            // Car tab click handler
            document.querySelectorAll('.car-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.car-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentCarType = this.dataset.car;
                    renderRoutes();
                });
            });
        
            // District dropdown change handler
            document.getElementById('districtSelect').addEventListener('change', function() {
                currentDistrict = this.value;
                renderRoutes();
            });
        
            // Render routes based on selected car type and district
            function renderRoutes() {
                const container = document.getElementById('routesContainer');
                container.innerHTML = '';
        
                const districtData = routesData[currentDistrict];
                // console.log(districtData)
                if (!districtData) {
                    container.innerHTML = '<p>Please select a valid route.</p>';
                    return;
                }
        
                const carData = districtData[currentCarType];
                if (!carData) {
                     container.innerHTML = '<p>Data not available for this car type.</p>';
                    return;
                }
        
                // Get district name from dropdown
                const select = document.getElementById('districtSelect');
                const districtName = select.options[select.selectedIndex].text;
        
                const card = document.createElement('div');
                card.className = 'route-card';
                card.setAttribute('data-aos', 'fade-up');
                card.innerHTML = `
                    <div class="route-header">
                        <div class="route-title">${districtName}</div>
                        <div class="route-distance-two">${carData.distance} kms | ${carData.time}</div>
                    </div>
                    <div class="route-body">
                        <div class="info-row">
                            <span class="info-label">Included KMs</span>
                            <span class="info-value included-kms">✓ Up to ${carData.inc_kms} kms</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Extra Fare</span>
                            <span class="info-value extra-fare">${carData.extraFare} kms</span>
                        </div>
                        <div class="row d-flex justify-content-center align-items-center">
                            <div class="price-section">
                                <div class="col-md-4 col-12"><div class="price mb-3 mb-md-0">₹${carData.price} <span class="original-price">₹${carData.originalPrice}</span></div></div>
                                <div class="col-md-4 col-12"> <div class="taxes-info mb-3 mb-md-0">Included (Taxes, Toll, Parking, Bata & Other Charges)</div></div>
                                <div class="col-md-3 col-12"><button class="theme-btn" onclick="location.href='{{ url()->to('/') }}/jobs'">VIEW CABS</button></div>
                            </div>
                        </div>
                    </div>
                `;
        
                container.appendChild(card);
                
                // Refresh AOS if you are using it
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
            }
        
            // Initial render on page load
            renderRoutes();
        });
    </script>
    <script
  src="https://maps.googleapis.com/maps/api/js?key={{ env('WEBSITE_GOOGLE_KEY') }}"
  async
  defer
></script>

<!--<script src="https://www.google.com/recaptcha/api.js?render={{ env('BOOKING_RECAPTCHA_SITE_KEY') }}"></script>-->


<script>
  $(document).ready(function () {
    // notifyJobs();
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