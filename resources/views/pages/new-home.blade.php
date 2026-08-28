@extends('layouts.app')

@section('content')

@php
    $iii = 1;
    //dd($seoTags);
    
    use App\Helpers\GoRideToken;

    $token = GoRideToken::generate();
@endphp

<style>
@media (max-width: 375px) and (max-height: 608px) {
    .header {
        min-height: 100vh;
        height: auto;
    }
}
.floating-dock{
    position:fixed;
    left:50%;
    bottom:0;
    transform:translateX(-50%);
    width:100%;
    background:#fff;
    display:flex;
    justify-content:space-around;
    align-items:center;
    box-shadow:0 -5px 25px rgba(0,0,0,.12);
    z-index:9999;
    height: 65px;
}

.dock-item{
margin-top: 8px;
    text-decoration:none;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    font-size:12px;
    font-weight:700;
    color:#222;
    transition:.3s;
}
.dock-item:hover{
    display:inline-flex;
}

.dock-item i {
    font-size: 20px;
    /*margin-bottom: 6px;*/
    transition: .3s;
    /*background: #cecaca;*/
    height: 35px;
    width: 35px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.dock-item span{
    font-size:11px;
    color:#222;
    text-transform: uppercase;
}

/* ICON COLORS */

.dock-item:nth-child(1) i{
  color: white;
    background: #2373e8;
    font-size: 15px;
}

.dock-item:nth-child(2) i{
       color: white;
    background: #25D366;
}

.dock-item:nth-child(4) i{
      color: white;
    background: #71a71a;
}

.dock-item:nth-child(5) i{
   color: white;
    background: black;
}

/* CENTER BUTTON */

.center-btn{
    position:relative;
    top:-21px;
}

.center-icon{
  width: 55px;
    height: 55px;
    background: #111;
    border-radius: 50%;
    border: 5px solid #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
    animation: float 2s infinite;
}

.center-icon .driver{
    font-size:21px;
    color:#f8be00;
}

.center-btn span{
    /*margin-top:5px;*/
    color:#111;
    font-weight:700;
}

.center-btn:hover .center-icon{
    transform:scale(1.08);
}

/* HOVER */

.dock-item:nth-child(1):hover{
    color:#111;
}

.dock-item:nth-child(1):hover i{
    animation:ring .6s;
}

.dock-item:nth-child(2):hover i{
    animation:shake .6s;
}

.dock-item:nth-child(4):hover i{
    animation:download .7s;
}

.dock-item:nth-child(5):hover i{
    animation:ride .7s;
}

/* ANIMATIONS */

@keyframes float{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-6px);}
}

@keyframes ring{
    0%,100%{transform:rotate(0);}
    20%{transform:rotate(20deg);}
    40%{transform:rotate(-20deg);}
    60%{transform:rotate(15deg);}
    80%{transform:rotate(-15deg);}
}

@keyframes shake{
    0%,100%{transform:translateX(0);}
    25%{transform:translateX(-4px);}
    50%{transform:translateX(4px);}
    75%{transform:translateX(-4px);}
}

@keyframes download{
    0%{transform:translateY(-5px);}
    50%{transform:translateY(5px);}
    100%{transform:translateY(0);}
}

@keyframes ride{
    0%{transform:translateX(0);}
    50%{transform:translateX(8px);}
    100%{transform:translateX(0);}
}

@media(min-width:992px){
    .floating-dock{
        display:none;
    }
}
/* WHATSAPP ICON */

/*.whatsapp-btn{*/
/*    font-size:28px;*/
/*    color:white;*/
/*    background:#25d366;*/
/*    border-radius:50%;*/
/*    height:38px;*/
/*    width:38px;*/
/*    display:flex;*/
/*    justify-content:center;*/
/*    align-items:center;*/
/*}*/

.whatsapp-btn:hover{
    color:white;
    display:flex;
}

/* MOBILE FIX */

@media screen and (max-width:490px){
    a.blantershow-chat{
        display:none;
        bottom:80px !important;
    }
}

/* HIDE ON DESKTOP */

@media (min-width:992px){
    .mobile-contact-bar{
        display:none !important;
    }
}
.stats-section{
    position:relative;
    background:url("goride/img/car.webp") center/cover no-repeat;
    padding:70px 20px;
}

/* black overlay */

.stats-section::before{
    content:"";
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
}

/* stats box */

.stats-box{
    position:relative;
    max-width:1200px;
    margin:auto;
    background:rgba(20,20,20,0.9);
    border-radius:30px;
    padding:30px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    text-align:center;
    flex-wrap:wrap;
}

/* stat item */

.stat-item{
    flex:1;
    min-width:200px;
    color:#fff;
}

/* icon */

.stat-icon{
     width: 80px;
    height: 80px;
    background: #f6ba02;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    margin: auto;
    border: 4px solid #fff;
    color: black;
}

/* number */

.stat-item h2{
    font-size:42px;
    margin-top:15px;
    font-weight:600;
    color:white;
}

/* text */

.stat-item p{
    font-size:18px;
    color:#ddd;
}

/* responsive */

@media(max-width:768px){

.stats-box{
gap:40px;
}

}


.app-download{
padding:50px 20px;
background:linear-gradient(135deg,#fff7e6,#ffeaa7);
font-family:'Poppins',sans-serif;
}

.app-container{
max-width:1000px;
margin:auto;
display:flex;
align-items:center;
justify-content:space-between;
gap:60px;
background:#fff;
padding:0px 51px;
border-radius:20px;
box-shadow:0 20px 60px rgba(0,0,0,0.1);
position:relative;
overflow:hidden;
}


.app-container::before{
content:"";
width:300px;
height:300px;
background:#000000db;
position:absolute;
    top: -154px;
    left: -142px;
border-radius:50%;
}



.gift-icon{
    font-size: 42px;
    animation: bounce 2s infinite;
    /* margin: 13px; */
    position: relative;
    top: 55px;
    right: 20px;
}

@keyframes bounce{
0%,100%{transform:translateY(0)}
50%{transform:translateY(-10px)}
}

.app-left h2{
font-size:36px;
font-weight:700;
margin-bottom:10px;
}

.app-left p{
color:#211c1c;
/*margin-bottom:25px;*/
}


.app-input{
display:flex;
border-radius:12px;
overflow:hidden;
border:1px solid #ddd;
max-width:420px;
}

.app-input span{
background:#f3f3f3;
padding:14px;
color:black;
font-weight:600;
}

.app-input input{
flex:1;
border:none;
padding:14px;
outline:none;
margin-bottom:0px;
}

.app-input button{
background:#ffb800;
border:none;
padding:14px 22px;
font-weight:600;
cursor:pointer;
transition:0.3s;
}

.store{
margin:20px;
}

.store img{
width:180px;
transition:.3s;
}

.store img:hover{
transform:scale(1.05);
}

/* right phone */

.app-right img{
width:260px;
animation:float 3s ease-in-out infinite;
}

@keyframes float{
0%{transform:translateY(0)}
50%{transform:translateY(-10px)}
100%{transform:translateY(0)}
}

/* responsive */

/* MOBILE VIEW */
@media (max-width:768px){

.app-download{
padding:40px 15px;
}

.app-container{
flex-direction:column;
padding:25px;
gap:20px;
text-align:center;
}

/* row fix */
.app-left .row{
flex-direction:column;
align-items:center !important;
justify-content:center !important;
}

.app-left .col-7,
.app-left .col-3{
width:100%;
max-width:100%;
flex:0 0 100%;
text-align:center;
}

/* gift icon */
.gift-icon{
font-size:50px;
margin-bottom:30px;
top:0px !important;
right:0px !important;
}

/* heading */
.app-left h2{
font-size:26px;
}

/* paragraph */
.app-left p{
font-size:14px;
}
  .app-container{
    padding:25px 20px;   /* reduce side padding */
  }

  .app-input{
    width:100%;
    max-width:100%;
  }

  .app-input span{
    padding:12px 10px;
    font-size:14px;
  }

  .app-input input{
    padding:12px;
    font-size:14px;
    width:100%;
  }

  .app-input button{
    padding:12px 16px;
    font-size:14px;
    white-space:nowrap;
  }

/* store button */
.store{
display:flex;
justify-content:center;
margin-top:15px;
}

.store img{
width:150px;
}

/* phone image */
.app-right{
margin-top:20px;
}

.app-right img{
width:180px;
margin:auto;
display:block;
}
.app-container::before{
display:none; /* softer look */
}

}
.fare-section{
  padding:30px 0px;
  text-align:center;
  background:#fff;
  font-family:'Poppins',sans-serif;
}

.fare-title{
  font-size:32px;
  font-weight:700;
  margin-bottom:15px;
}

.fare-title span{
  color:#f9bf00;
}



.fare-table-wrapper{
  max-width:900px;
  margin:auto;
}

.fare-table{
  width:100%;
  border-collapse:collapse;
}

.fare-table thead{
  background:#f9bf00;
  color: #353434;
}

.fare-table th{
  padding:16px;
  font-weight:700;
  font-size:15px;
}

.fare-table td{
  padding:12px;
  font-size:15px;
  color:#444;
}

.fare-table tbody tr:nth-child(odd){
  background:#f2f2f2;
}

.fare-table tbody tr:nth-child(even){
  background:#e7e7e7;
}

.fare-note{
  margin-top:20px;
  font-size:14px;
  color:#777;
  font-style:italic;
}
@media (max-width: 768px) {
    .fast-booking-bar{
font-size:13px !important;
    }
    .fare-table th,
.fare-table td{
  padding:12px;
  font-size:13px;
}

.fare-title{
  font-size:24px;
}

}


.phone{
  background:#f9bf00;
  color:#000;
}

.whatsapp{
  background:#25D366;
  color:#fff;
}
.phone-link{
  color:#f9bf00;
  text-decoration:none;
  display:flex;
  align-items:center;
  gap:6px;
}

.icon-circle{
  width:28px;
  height:28px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:16px;
}

.phone-bg{
  background:#f9bf00;
  color:#000;
  font-size:12px;
}

.whatsapp-bg{
  background:#25D366;
  color:#fff;
}
.fast-booking-bar{
 display:flex;
  align-items:center;
  justify-content:center;
  gap:10px;

  padding:8px 16px;

  background:rgba(0,0,0,0.65);
  backdrop-filter:blur(8px);

  border-radius:7px;

  color:#ffffff;
  font-size:15px;
  font-weight:600;

  animation:floatBar 4s ease-in-out infinite;
}
@keyframes floatBar{
  0%{
    transform:translateY(0);
  }
  50%{
    transform:translateY(-4px);
  }
  100%{
    transform:translateY(0);
  }
}

.phone-link{
  color:#f9bf00;
  text-decoration:none;
  display:flex;
  align-items:center;
  gap:4px;
}
.icon-circle:hover{
    display:flex!important;
}
.phone-link:hover{
    display:flex!important;
}
.whatsapp-icon:hover{
    display:flex!important;
}

.whatsapp-icon{
  width:28px;
  height:28px;
  background:#25D366;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-size:14px;
}


.goride-booking .onward{
              color: grey;
    font-weight: 600;
    font-size: 14px !important;
        line-height: 0.9;
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
        .goride-booking .cab-price{
          display: inline-flex !important;
                justify-content: start !important;
        }
        .goride-booking .cab-action{
             display: inline-flex !important;
             margin-left:20px;
        }
        .goride-booking .cab-features{
            gap:6px !important;
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
            flex-direction:column;
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
        
          .cab-card{
        position: relative;
        padding-bottom: 55px;   /* create space so button doesn't overlap */
    }

    /* Keep features normal */
    .cab-features{
        display: flex;
        gap: 12px;
    }

    /* Pull button out of flow and stick it to the end */
       .cab-action{
        position: absolute;
        right: 12px;
        bottom: 5px;
        margin: 0 !important;
        text-align: right;
        width: auto;
    }

    /* Prevent wrapping */
    .book-now-btn{
        white-space: nowrap;

        font-size: 13px;
        border-radius: 8px;
    }
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

        .header {
            height: 100vh !important;
        }

        .feature-img-wrap {
            margin-bottom: 45px;
        }

        .features-grid-section {
            padding: 25px;
        }

        .slider-fade .item .caption {
            top: 54% !important;
        }

        .jobs-heading {
            top: -16px;
            left: 50%;
            transform: translateX(-50%);
        }

        #about {
            padding: 35px !important;
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
    .cs_app_btn_anim {
    position: relative;
    background: #ffc107;
    color: #000;
   padding: 5px 24px;
   border-radius:7px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    transition: 0.3s;
      animation: floatMove 2.5s ease-in-out infinite, pulseGlow 1.8s infinite;
    margin-left:10px;
}
   .cs_app_btn_anim {
            position: relative;
            background: #ffc107;
            color: #000;
           padding: 5px 24px;
           border-radius:7px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            font-size:14px;
            gap: 4px;
            overflow: hidden;
            transition: 0.3s;
              animation: floatMove 2.5s ease-in-out infinite, pulseGlow 1.8s infinite;
            margin-left:10px;
        }
        
        .cs_app_btn_anim::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: rgba(255,255,255,0.4);
            transform: skewX(-20deg);
            animation: shineMove 2.5s infinite;
        }
        
        .cs_app_btn_anim:hover {
            transform: translateY(-3px);
             color: #000 !important;
             display:inline-flex !important;
        }
        
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 rgba(255,193,7,0.5); }
            50% { box-shadow: 0 0 20px rgba(255,193,7,0.8); }
            100% { box-shadow: 0 0 0 rgba(255,193,7,0.5); }
        }
        
        /* UP-DOWN FLOAT */
        @keyframes floatMove {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        @keyframes shineMove {
            0% { left: -100%; }
            100% { left: 120%; }
        }
/* App Store Pills */
/* ===== Realistic App Store Buttons ===== */
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

    @media only screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) {
        .i_phone {
            margin-top: -100px !important;

        }
    }
    
    
    
    .goride-pooling {
  position: relative;
  overflow: hidden;
  padding: 30px;
  background: black;
  color: #fff;
  /*font-family: "Poppins", sans-serif;*/
}

.goride-pooling__bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 10%, rgba(255, 196, 0, 0.14), transparent 18%),
    radial-gradient(circle at 90% 85%, rgba(255, 196, 0, 0.12), transparent 20%),
    linear-gradient(180deg, rgba(0,0,0,0.15), rgba(0,0,0,0.3));
  pointer-events: none;
}

.goride-pooling__container {
  position: relative;
  z-index: 2;
  width: min(1320px, calc(100% - 40px));
  margin: 0 auto;
}


.goride-pooling__content {
  padding-top: 10px;
}

.goride-pooling__kicker {
  font-size: 30px;
  font-weight: 600;
  color: #ffc400;
  margin-bottom: 12px;
  text-align: center;
}

.goride-pooling__title {
  margin: 0 0 14px;
  font-size: 42px;
  line-height: 1.02;
  font-weight: 800;
  letter-spacing: -0.04em;
  text-align: center;
  color:white;
}

.goride-pooling__title span {
  color: #ffc400;
}

.goride-pooling__subline {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 16px;
  font-size: 16px;
  color: white;
}

.goride-pooling__subline span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.goride-pooling__subline .desc {
  color: white;
}

.goride-pooling__subline i {
  color: #ffc400;
}

.goride-pooling__text {
text-align: center;
    font-size: 17px;
    line-height: 1.9;
    color: white;
}

.goride-pooling__visual {
position: relative;
    border-radius: 28px;
    overflow: hidden;
 height: 340px;
    width: 100%;
}

.goride-pooling__visual img {
 width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    object-position: top;
}

.goride-pooling__cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  margin-top: 22px;
}

.goride-card {
  display: grid;
  grid-template-columns: 72px 1fr 170px;
  gap: 18px;
  align-items: center;
  padding: 18px 20px;
  border-radius: 22px;
 background: rgb(242 242 242);
}

.goride-card__icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 28px;
  color: #111;
  flex: 0 0 auto;
}

.goride-card__icon--yellow {
  background: #ffc400;
}

.goride-card__icon--green {
  background: #40cc4b;
}

.goride-card__body h3 {
  margin: 0 0 8px;
  font-size: 24px;
  line-height: 1.1;
  font-weight: 700;
  color: #ffc400;
}

.goride-card:nth-child(2) .goride-card__body h3 {
  color: #40cc4b;
}

.goride-card__body p {
  margin: 0;
  font-size: 16px;
  line-height: 1.7;
  /*color: white;*/
}

.goride-card__image {
  width: 100%;
  height: 120px;
  border-radius: 18px;
  overflow: hidden;
}

.goride-card__image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}



.goride-benefits__label {
  display: inline-block;
  background: #ffc400;
  color: #111;
  font-weight: 800;
  font-size: 16px;
  padding: 8px 18px;
  border-radius: 6px;
  margin: 0 0 16px 12px;
  transform: translateY(-10px);
}



.benefit {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  border-right: 1px dashed rgba(255, 255, 255, 0.14);
}

.benefit:last-child {
  border-right: none;
}

.benefit__icon {
     width: 45px;
    height: 45px;
    border-radius: 50%;
    background:#ffc400;
    color: #111;
    display: grid;
    place-items: center;
    font-size: 17px;
    flex: 0 0 auto;
}

.benefit p {
margin: 0;
    font-size: 16px;
    line-height: 1.45;
}

.goride-download {
  text-align: center;
}

.goride-download h3 {
  margin: 0 0 18px;
  font-size: clamp(22px, 2.4vw, 34px);
  line-height: 1.25;
  font-weight: 800;
  /*color:white;*/
}

.goride-download h3 span {
  color: #ffc400;
}

.goride-download__buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
  flex-wrap: wrap;
      margin-top: 20px;
}

.store-btn{
    display:inline-flex;
    align-items:center;
    gap:9px;
    min-width:160px;
    padding:8px 20px;
    border-radius:14px;
    background:#ffc400;
    color:#111;
    text-decoration:none;
    box-shadow:0 12px 30px rgba(255,196,0,.22);
    transition:.3s ease;
    position:relative;
    overflow:hidden;

    animation:floatBtn 2.5s ease-in-out infinite;
}

/* Shine Effect */
.store-btn::before{
    content:"";
    position:absolute;
    top:0;
    left:-100%;
    width:50%;
    height:100%;
    background:linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,.5),
        transparent
    );
    transform:skewX(-25deg);
    animation:shine 3s infinite;
}

.store-btn:hover{
    transform:translateY(-5px) scale(1.05);
    box-shadow:0 16px 34px rgba(255,196,0,.35);
    color:#111;
}

/* Icon animation */
.store-btn i{
    font-size:24px;
    animation:bounceIcon 1.5s infinite;
}

/* Floating */
@keyframes floatBtn{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-4px);
    }
}

/* Shining light */
@keyframes shine{
    0%{
        left:-100%;
    }
    100%{
        left:150%;
    }
}

/* Icon bounce */
@keyframes bounceIcon{
    0%,100%{
        transform:scale(1);
    }
    50%{
        transform:scale(1.15);
    }
}
.store-btn i {
  font-size: 24px;
}

.store-btn small {
  display: block;
  font-size: 12px;
  line-height: 1;
}

.store-btn strong {
    display: block;
    font-size: 14px;
    line-height: 1.1;
    font-weight: 700;
}
.goride-benefits__list{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:0px 30px;
}

.benefit{
    display:flex;
    align-items:center;
    gap:14px;
}

@media (max-width: 1200px) {
  .goride-pooling__top {
    grid-template-columns: 1fr;
  }

  .goride-pooling__visual {
    min-height: 260px;
  }

  .goride-pooling__cards {
    grid-template-columns: 1fr;
  }

  
  .goride-benefits__list{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px 30px;
}

.benefit{
    display:flex;
    align-items:center;
    gap:14px;
} {
    grid-template-columns: repeat(2, 1fr);
  }

  .benefit {
    border-right: none;
    border-bottom: 1px dashed rgba(255, 255, 255, 0.14);
  }

  .benefit:nth-child(5) {
    border-bottom: none;
  }
}

@media (max-width: 768px) {
    .goride-download__buttons{
            flex-wrap: nowrap;
            
    }
  .goride-pooling {
    padding: 44px 0 36px;
  }

  .goride-pooling__container {
    width: min(100% - 20px, 1320px);
  }

  .goride-pooling__title {
    font-size: clamp(34px, 12vw, 50px);
  }

  .goride-pooling__subline {
    font-size: 13px;
  }

  .goride-pooling__text {
    font-size: 13px;
    line-height: 1.7;
  }

  .goride-card {
    grid-template-columns: 1fr;
    text-align: left;
  }

  .goride-card__image {
    height: 200px;
  }

  .goride-benefits__list {
    grid-template-columns: 1fr;
  }

  .benefit {
    padding: 12px 0;
    border-right: none;
  }

  .store-btn {
    width: 100%;
    min-width: 120px;
    justify-content: center;
    padding: 11px 12px;
  }
}

.app-strip-card{
      padding: 25px;
    border-radius: 20px;
    background: #eee;
    border: 1px solid #eee;
}

.app-icon{
    width:110px;
    border-radius:15px;
}

.app-strip-card h3{
    font-size:20px;
    font-weight:700;
    margin:0;
}

.app-strip-card p{
    color:#666;
    font-size:15px;
        font-weight:500;
}

.free-credit{
      display: inline-flex;
    align-items: center;
    /* padding: 6px 20px; */
    border-radius: 50px;
    /* background: linear-gradient(135deg, #ffd54f, #f9bf00); */
    color: #222;
    font-weight: 700;
    font-size: 13px;
    /* box-shadow: 0 8px 25px rgba(249, 191, 0, .3); */
    animation: pulseOffer 2s infinite;
}

.free-credit span{
font-size: 22px;
    font-weight: 900;
    margin-right: 6px;
    color: #ecb113;
}

.store-buttons{
     display: flex;
    gap: 12px;
    margin-top: 15px;
    justify-content: center;
    align-items: center;
}

.store-buttons a {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
    padding: 15px;
    border-radius: 15px;
    transition: all 0.3s ease;
    animation: floatIcon 3s ease-in-out infinite;
    box-shadow: 0 5px 15px rgba(0,0,0,.08);
}

.store-buttons a:hover{
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
}

.store-buttons a:nth-child(2){
    animation-delay: 1.5s;
}

@keyframes floatIcon{
    0%,100%{
        transform: translateY(0);
    }
    50%{
        transform: translateY(-10px);
    }
}

.store-buttons img{
       width: 100%;
    max-width: 50px;
    height: 44px;
    display: block;
    transition: 0.3s;
}

.store-buttons img:hover{
    transform: scale(1.05);
}

@media(max-width:576px){
    .store-buttons{
        gap: 8px;
    }

    .store-buttons img{
        max-width: 150px;
    }
}
@keyframes pulseOffer{
    0%{
        transform:scale(1);
    }
    50%{
        transform:scale(1.05);
    }
    100%{
        transform:scale(1);
    }
}
.feature-pill{
    background:#f7f7f7;
    padding:4px 14px;
    border-radius:20px;
        font-weight: 500;
}

.feature-pill i,

.trust i{
    color:#f9bf00;
}
.free-credit i{
    color:black;
}

.app-icon{
    width:110px;
    border-radius:15px;
    animation: floatApp 3s ease-in-out infinite;
}

@keyframes floatApp{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-8px);
    }
}

.trust{
     color: #666;
    font-weight: 700;
}

@media(max-width:991px){

    .app-strip-card{
        text-align:center;
    }

    .col-auto,
    .col{
        width:100%;
    }

    .app-icon{
        margin:auto;
        display:block;
    }

}
</style>

<header class="header slider-fade" id="slider-image">
      <div class="item bg-img" data-overlay-dark="1">
        <div class="v-middle caption">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-6 col-md-6 mb-30 d-none d-md-block">
    <div class="v-middle text-center">
        <h2>Go!</h2>
        <h2>Anywhere & Anytime</h2>
        <h2>With <span class="text-warning">GoRide</span></h2>

        <p>Whether you're heading to work, the airport,</p>
        <p>or a weekend getaway — we've got you covered.</p>
        <p>On-time pickups and drop-offs, always reliable.</p>

        <div class="d-none d-md-block">
            @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                <a href="/booking" class="cs_btn cs_style_2 mt-4 me-3">
                    Book now Your Cab Soon&nbsp;<i class="fa-regular fa-car-side-bolt"></i>
                </a>
            @else
                <div style="cursor: pointer;" onclick="window.location.href='/booking'"
                    class="cs_btn cs_style_2 mt-4 me-3">
                    Book now Your Cab Soon&nbsp;<i class="fa-regular fa-car-side-bolt"></i>
                </div>
            @endif

            <!-- App Buttons Row -->
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

        <div class="d-block d-md-none">
            @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                <a href="/booking" class="cs_btn cs_style_2 mt-4 me-3">
                    Book now Your Cab Soon&nbsp;<i class="fa-regular fa-car-side-bolt"></i>
                </a>
            @else
                <div style="cursor: pointer;" onclick="window.location.href='/booking'"
                    class="cs_btn cs_style_2 mt-4">
                    Book now Your Cab Soon&nbsp;<i class="fa-regular fa-car-side-bolt"></i>
                </div>
            @endif

            <!-- App Buttons Row (mobile) -->
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
</div>
              
              <div class="col-md-6 col-12">
                @include('include.bookingForm')
          </div>
            </div>
          </div>
        </div>
       
      </div>
    </header>
    
    
<!--    <section class="goride-app-strip mt-3">-->
<!--    <div class="container">-->
<!--        <div class="app-strip-card">-->

<!--            <div class="row d-flex justify-content-center align-items-center g-3">-->

<!--                <div class="col-md-3 col-12 order-2 order-md-1 text-center">-->


<!--                    <img src="goride/img/cust3.jpg"-->
<!--                         class="app-icon">-->
<!--                                <small class="d-block mt-2 trust">-->

<!--                        <i class="fas fa-star"></i>-->

<!--                        Trusted by 50,000+-->

<!--                    </small>-->
                    
<!--                    <div class="free-credit mt-2">-->
<!--        <i class="fas fa-gift me-2"></i>-->
<!--        <span>₹1000</span> FREE Credits-->
<!--    </div>-->

<!--                </div>-->

<!--                <div class="col-md-4 col-12 text-center order-1 order-md-2">-->

<!--                    <h3 class="section-title">-->
<!--                      Ride Faster With Our <span> App</span>  -->
<!--                    </h3>-->

<!--                    <p class="mb-3">-->
<!--                     Download now and enjoy safe, affordable rides with exclusive offers!-->
<!--                    </p>-->

<!--                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">-->
<!--           <span class="feature-pill">-->
<!--                            <i class="fas fa-shield-alt"></i>-->
<!--                            Safe-->
<!--                        </span>-->

<!--                        <span class="feature-pill">-->
<!--                            <i class="fas fa-bolt"></i>-->
<!--                            Fast-->
<!--                        </span>-->

<!--                        <span class="feature-pill">-->
<!--                            <i class="fas fa-wallet"></i>-->
<!--                            Transparent-->
<!--                        </span>-->

<!--                    </div>-->

<!--                </div>-->

<!--              <div class="col-md-3 col-12 text-center order-3 order-md-3">-->

<!--<h3 class="section-title">-->
<!--                          Available on  <span> Play </span>Store  & <span>App </span>Store-->
<!--                    </h3>-->

<!--  <div class="store-buttons">-->
    
<!--    <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer"-->
<!--       target="_blank">-->
<!--        <img src="/goride/img/google-play-badge.png" alt="Google Play">-->
<!--    </a>-->

<!--    <a href="https://apps.apple.com/us/app/goride-cab-bike-taxi-pool/id6763038270"-->
<!--       target="_blank">-->
<!--        <img src="/goride/img/app-store-badge.png" alt="App Store">-->
<!--    </a>-->

<!--</div>    -->

<!--</div>-->
<!--            </div>-->

<!--        </div>-->
<!--    </div>-->
<!--</section>-->

    <section>
        <div class="container">
            <div class="row mt-3">
              <div class="col-lg-6 col-md-6 d-block d-md-none">
                <div class="text-center">
                     <h2 style="font-size:22px;margin-bottom:0px;"> Go! Anywhere & Anytime</h2>
                  <h2 style="font-size:22px;margin-bottom:0px;">With <span class="text-warning">GoRide</span> </h2>
                 
                  <p style="margin-bottom:0px;">Whether you’re heading to work, the airport,or a weekend getaway — we’ve got you covered.On-time pickups and drop-offs, always reliable.</p>
                 
                  <div class="d-none d-md-block">
                    @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                    <a href="/booking" class="cs_btn cs_style_2 mt-4 me-3">Book now Your Cab Soon&nbsp;<i
                        class="fa-regular fa-car-side-bolt"></i></a>
                    <!--<a style="cursor: pointer;" onclick="triggerCalendly();" class="cs_btn cs_style_2 mt-4">Book a-->
                    <!--  demo&nbsp;<i class="fa-regular fa-headset"></i></a>-->
                    @else
                    <div style="cursor: pointer;" href="/booking" onclick="window.location.href='/booking'" class="cs_btn cs_style_2  mt-4">Book now Your Cab Soon&nbsp;<i
                        class="fa-regular fa-car-side-bolt"></i></div>
                    @endif
                  </div>
                  <div class="d-block d-md-none">
                    @if (isset($_COOKIE['cusid']) && $_COOKIE['cusid'] != null)
                    <a href="/booking" class="cs_btn cs_style_2  mt-4 me-3">Book now Your Cab Soon&nbsp;<i
                        class="fa-regular fa-car-side-bolt"></i></a>
                    <!--<a style="cursor: pointer;" onclick="triggerCalendly();" class="cs_btn cs_style_2 mt-4">Book a-->
                    <!--  demo&nbsp;<i class="fa-regular fa-headset"></i></a>-->
                    @else
                    <div style="cursor: pointer;" href="/booking" onclick="window.location.href='/booking'" class="cs_btn cs_style_2  mt-4">Book now Your Cab Soon&nbsp;<i
                        class="fa-regular fa-car-side-bolt"></i></div>
                    @endif
                  </div>
                </div>
                
              </div>
             
            </div>
        
        </div>
        
    </section>
    
    

    
     <section class="fare-section">
  <div class="container">

    <h2 class="section-title">
      <span>Ride Smarter with
GoRide Pooling</span> - Car Pool & Bike Pool - Ride for Free, Affordable, Convenient, and Eco-Friendly Travel
    </h2>

    <p class="fare-desc">
      Whether you prefer the comfort of a car or the speed of a bike, GoRide Pooling connects you with riders traveling in the same direction. Share your journey, reduce travel costs, avoid traffic stress, and contribute to a greener environment.
    </p>
    
       <div class="goride-pooling__cards  mb-3">
      <article class="goride-card">
        <div class="goride-card__icon goride-card__icon--yellow">
          <i class="fa-solid fa-car-side"></i>
        </div>

        <div class="goride-card__body">
          <h3>Car Pool</h3>
          <p>
            Perfect for comfortable daily commutes, office travel, and long-distance city rides. Share your ride, split expenses, and travel smarter.
          </p>
        </div>

        <div class="goride-card__image">
          <img src="goride/img/car-pool.webp" alt="Car Pool">
        </div>
      </article>

      <article class="goride-card">
        <div class="goride-card__icon goride-card__icon--green">
          <i class="fa-solid fa-bicycle"></i>
        </div>

        <div class="goride-card__body">
          <h3>Bike Pool</h3>
          <p>
            Ideal for quick city travel and short-distance commutes. Skip traffic, save time, and reach your destination faster.
          </p>
        </div>

        <div class="goride-card__image">
          <img src="goride/img/bike-pool.webp" alt="Bike Pool">
        </div>
      </article>
    </div>
    
       <div class="goride-benefits py-4">
<h2 class="section-title">
      Benefits of <span>GoRide Pooling </span>
    </h2>

  <div class="goride-benefits__list">


    <div class="benefit">
      <div class="benefit__icon">
        <i class="fa-solid fa-piggy-bank"></i>
      </div>
      <p>Save money on every trip</p>
    </div>

    <div class="benefit">
      <div class="benefit__icon">
      <i class="fa fa-shield"></i>
      </div>
      <p>Verified riders and secure travel</p>
    </div>

    <div class="benefit">
      <div class="benefit__icon">
        <i class="fa-solid fa-location-dot"></i>
      </div>
      <p>Real-time tracking and easy booking</p>
    </div>

    <div class="benefit">
      <div class="benefit__icon">
        <i class="fa-solid fa-leaf"></i>
      </div>
      <p>Reduce traffic congestion and pollution</p>
    </div>

    <div class="benefit">
      <div class="benefit__icon">
        <i class="fa-solid fa-map-location-dot"></i>
      </div>
      <p>Convenient pickup and drop points</p>
    </div>
  <div class="benefit">
  <div class="benefit__icon">
    <i class="fa-solid fa-earth-asia"></i>
  </div>
  <p>Eco-friendly shared rides for a greener future</p>
</div>


  </div>
</div>
<div class="row d-flex justify-content-center align-items-center mt-3">
    <div class="col-md-7 col-12">
            <div class="goride-download">
      <h3>Start your journey with <span>GoRide Pooling</span> today!</h3>

    
    </div>
    <div class="fare-table-wrapper">
      <table class="fare-table">
        <thead>
          <tr>
            <th>VEHICLE TYPE</th>
            <th>ONE WAY TAXI FARE</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>Car</td>
            <td>₹4 / Km</td>
          </tr>

          <tr>
            <td>Bike</td>
            <td>₹3 / Km</td>
            
          </tr>

       
        </tbody>
      </table>
    
    </div>
      <div class="goride-download__buttons">
        <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer" class="store-btn">
          <i class="fa-brands fa-google-play"></i>
          <div>
            <small>Download on the</small>
            <strong>Google Play</strong>
          </div>
        </a>

        <a href="https://apps.apple.com/us/app/goride-cab-bike-taxi-pool/id6763038270" class="store-btn">
          <i class="fa-brands fa-apple"></i>
          <div>
            <small>Download on the</small>
            <strong>App Store</strong>
          </div>
        </a>
      </div>
    </div>

   <div class="col-md-5 col-12"> 
   <div class="goride-pooling__top">

      <div class="goride-pooling__visual">
        <img src="goride/img/common.webp" alt="GoRide car and bike pooling">
      </div>
    </div>
    </div>
    
    
    
    </div>
    
  </div>
</section>
    
    <section class="fare-section">
  <div class="container">

    <h2 class="section-title">
      <span>Outstation Taxi Fare</span> - One Way & Round Trip Pricing Chart
    </h2>

    <p class="fare-desc">
      Book low-cost outstation taxi services with transparent per km pricing.
      Enjoy affordable, reliable, and comfortable city-to-city travel with GoRide.
    </p>

    <div class="fare-table-wrapper">
      <table class="fare-table">
        <thead>
          <tr>
            <th>VEHICLE TYPE</th>
            <th>ONE WAY TAXI FARE</th>
            <th>ROUND TRIP TAXI FARE</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>Go Mini</td>
            <td>₹14 / Km</td>
            <td>₹13 / Km</td>
          </tr>

          <tr>
            <td>Go 4Seaters</td>
            <td>₹19 / Km</td>
            <td>₹18 / Km</td>
          </tr>

          <tr>
            <td>Go 6Seaters</td>
            <td>₹20 / Km</td>
            <td>₹19 / Km</td>
          </tr>

          <tr>
            <td>Go 7Seaters</td>
            <td>₹23 / Km</td>
            <td>₹22 / Km</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="fare-note">
      * Minimum distance: 50Kms. Toll, parking, hill station, waiting & state permit charges are extra, only if applicable.
    </p>

  </div>
</section>

  <section class="app-download">
  <div class="app-container">

    <div class="app-left">

      <div class="gift-icon">🎁</div>
<div class="row d-flex justify-content-end align-items-center">
    <div class="col-7">
      <h2 class="section-title mb-0">Ride Faster with <span>Our App</span></h2>
      <p class="mb-2">Download the app now, sign up, and  get  <span style="font-size: 20px;color: #f9bf00;font-weight: 600;">  1000 </span>  credits - absolutely free!</p>
      <div class=" align-items-center">
            <div class="app-input">
        <span>+91</span>
        <!--<input type="text" placeholder="Enter Mobile Number">-->
        <input type="tel" id="mobile" placeholder="Enter Mobile Number" maxlength="10" inputmode="numeric">
        <button id="sendCusBtn">Send Link</button>
      </div>

      <div class="store">
          <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer" target="_blank">
        <img src="/goride/img/paly-store-logo.png"></a>
      </div>
      </div>
</div>
   

   <div class="col-3">
  <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer" target="_blank">
    <div class="app-right">
      <img src="goride/img/cust.png" alt="phone">
    </div>
    </a>
    </div>
     </div>
 </div>
  </div>
</section>
    
    <section class="about section-padding mt-4" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12  i_phone mb-md-30">
                <div class="content">
                    <div class="text-center text-md-start">
                        <h1 class="section-subtitle">The Heartbeat of Everyday Travel</h1>
                    </div>
                    <div class="section-title text-center text-md-start"><span>Smarter Rides. Seamless Journeys.</span>
                    </div>
                    <p class="fw-normal" style="text-align: justify;">Welcome to <strong>GoRide</strong> — your trusted
                        cab booking platform designed to make every journey
                        smooth, safe, and reliable. Whether it’s a short city ride or a long-distance trip, GoRide
                        connects you with the right ride at the right time.</p>
                    <p class=" fw-normal" style="text-align: justify;">With intelligent ride matching, real-time
                        tracking, and dependable drivers, we make getting
                        rides effortless.</p>
                    <div class="text-center text-md-start">
                        <a href="about" class=" button-4 mb-3 mb-md-0">Learn More About Go Ride's Services <span
                                class="ti-arrow-top-right"></span></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1 col-md-12">
                <div class="item " > <img
                        src="{{ asset('goride/img/abt-1.webp') }}" class="img-fluid" alt="about">
                    <div class="curv-butn icon-bg">
                        <img src="{{ asset('goride/img/g.png') }}" alt="Play Button" style="width: 50px; height: 50px;">
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
    </div>
</section>

<section class="stats-section mt-3">

<div class="stats-box">

<div class="stat-item">
<div class="stat-icon">
<i class="fa-solid fa-taxi"></i>
</div>
<h2 class="counter" data-count="150">0+</h2>
<p> Available Cabs</p>
</div>

<div class="stat-item">
<div class="stat-icon">
<i class="fa-solid fa-users"></i>
</div>
<h2 class="counter" data-count="100">0+</h2>
<p> Happy Clients</p>
</div>

<div class="stat-item">
<div class="stat-icon">
<i class="fa-solid fa-user"></i>
</div>
<h2 class="counter" data-count="70">0+</h2>
<p> Our Drivers</p>
</div>

<div class="stat-item">
<div class="stat-icon">
<i class="fa-solid fa-route"></i>
</div>
<h2 class="counter" data-count="180">0+</h2>
<p>Road Trip Done</p>
</div>

</div>

</section>

 
<section class="myClients section-padding mt-5">
                    <div class="container">
                        <div class="row d-flex justify-content-center align-items-center">
                          
                  
                            <div class="section-title text-center mb-5"> Available &nbsp;<span> Fleets&nbsp;</span>
                            </div>
                            <div class="col-md-12 text-center mb-30">
     
                                <div class="row g-4 justify-content-center">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="fleet-card">
                                            <div class="fleet-image">
                                                <img src="{{ asset('goride/img/mini(new1).webp') }}" alt="Mini">
                                            </div>
                                            <div class="fleet-content">
                                                <h3 class="m-0">Go Mini</h3>
                                                <div class="fleet-models">Indica, Micra, Ritz</div>
                                                <div class="fleet-desc">Affordable AC rides for everyday travel.</div>
                                            </div>
                                        </div>
                                
                                    </div>
                            
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="fleet-card">
                                            <div class="fleet-image">
                                                <img src="{{ asset('goride/img/primesedan(new1).webp') }}" alt="Prime Sedan">
                                            </div>
                                            <div class="fleet-content">
                                                <h3 class="m-0">Go Sedan</h3>
                                                <div class="fleet-models">Dzire, Etios, Sunny</div>
                                                <div class="fleet-desc">Comfortable sedans with extra legroom.</div>
                                            </div>
                                        </div>
         
                                    </div>
                            
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="fleet-card">
                                            <div class="fleet-image">
                                                <img src="{{ asset('goride/img/primesuv(new1).webp') }}" alt="Prime SUV">
                                            </div>
                                            <div class="fleet-content">
                                                <h3 class="m-0">Go SUV</h3>
                                                <div class="fleet-models">Ertiga, Enjoy</div>
                                                <div class="fleet-desc">Spacious SUVs ideal for group travel.</div>
                                            </div>
                                        </div>
          
                                    </div>
                            
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="fleet-card">
                                            <div class="fleet-image">
                                                <img src="{{ asset('goride/img/Primesuv+(new1).png') }}" alt="Prime SUV+">
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
                                                <img src="{{ asset('goride/img/audi1.webp') }}" alt="Prime Plus">
                                            </div>
                                            <div class="fleet-content">
                                                <h3 class="m-0">Go Executive</h3>
                                                <div class="fleet-models">Audi, Benz</div>
                                                <div class="fleet-desc">Luxury rides with top comfort & style.</div>
                                            </div>
                                        </div>
          
                                    </div>
                            
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="fleet-card">
                                            <div class="fleet-image">
                                                <img src="{{ asset('goride/img/xl1.webp') }}" alt="XL Intercity">
                                            </div>
                                            <div class="fleet-content">
                                                <h3 class="m-0">Go Tourister</h3>
                                                <div class="fleet-models">Toyota,  Force Motors, Tempo Traveller</div>
                                                <div class="fleet-desc">Comfortable rides for long trips.</div>
                                            </div>
                                        </div>
                                    
                                    </div>
             
                                </div>
                            </div>
                              
                            </div>
                        </div>
                    </section>

<section class="driver-section ">
    <div class="container">
        <div class="row d-flex justify-content-center align-items-center">
            <!-- Left Column: Content -->
            <div class="col-md-6 col-12 content-col">
                <div data-aos="fade-up">
                    <div class="tagline">Drive. Earn. Grow with GoRide.</div>
                    <div class="section-title">Become a&nbsp;<span> GoRide</span> Partner!</div>

                </div>

                <div class="description" data-aos="fade-up" data-aos-delay="100">
                    Join thousands of drivers earning steady income with flexible working hours. Whether you want to
                    drive full-time or part-time, GoRide gives you the freedom to earn on your own schedule.
                </div>

                <div class="steps-section">
                    <h2 class="section-title" data-aos="fade-up">Start <span> Driving</span> in 3 Easy Steps</h2>

                    <div class="step" data-aos="fade-right" data-aos-delay="100">
                        <div class="step-number">1</div>
                        <div class="step-content">Download the GoRide Driver App</div>
                    </div>

                    <div class="step" data-aos="fade-right" data-aos-delay="200">
                        <div class="step-number">2</div>
                        <div class="step-content">Register & Upload Documents</div>
                    </div>

                    <div class="step" data-aos="fade-right" data-aos-delay="300">
                        <div class="step-number">3</div>
                        <div class="step-content">Get Approved and Start Earning</div>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="100">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Active Drivers</span>
                    </div>
                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="200">
                        <span class="stat-number">4.8★</span>
                        <span class="stat-label">Driver Rating</span>
                    </div>
                    <div class="stat-item" data-aos="zoom-in" data-aos-delay="300">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>


            </div>

            <!-- Right Column: Image -->
            <div class="col-md-6 col-12 image-col">
                <div class="image-container">

                    <img src="{{ asset('goride/img/banner-1-mob.webp') }}" class="driver-image"  alt="driver-image "data-aos="zoom-in">
                    <div class="cta-section" data-aos="zoom-in" data-aos-delay="400">
                        <div class="cta-text">Join today and turn every drive into an earning opportunity.</div>
                        <a href="https://play.google.com/store/apps/details?id=com.shi.goride.customer" target="_blank"
                            class="text-decoration-none">
                            <button class="cta-button">
                                <svg class="kOqhQd" aria-hidden="true" viewBox="0 0 40 40"
                                    xmlns="http://www.w3.org/2000/svg" style=" height: 20px;margin: 4px;">
                                    <path fill="none" d="M0,0h40v40H0V0z"></path>
                                    <g>
                                        <path
                                            d="M19.7,19.2L4.3,35.3c0,0,0,0,0,0c0.5,1.7,2.1,3,4,3c0.8,0,1.5-0.2,2.1-0.6l0,0l17.4-9.9L19.7,19.2z"
                                            fill="#EA4335"></path>
                                        <path
                                            d="M35.3,16.4L35.3,16.4l-7.5-4.3l-8.4,7.4l8.5,8.3l7.5-4.2c1.3-0.7,2.2-2.1,2.2-3.6C37.5,18.5,36.6,17.1,35.3,16.4z"
                                            fill="#FBBC04"></path>
                                        <path
                                            d="M4.3,4.7C4.2,5,4.2,5.4,4.2,5.8v28.5c0,0.4,0,0.7,0.1,1.1l16-15.7L4.3,4.7z"
                                            fill="#4285F4"></path>
                                        <path
                                            d="M19.8,20l8-7.9L10.5,2.3C9.9,1.9,9.1,1.7,8.3,1.7c-1.9,0-3.6,1.3-4,3c0,0,0,0,0,0L19.8,20z"
                                            fill="#34A853"></path>
                                    </g>
                                </svg>Download App
                            </button>
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
    
<section class="goride-routes ">
    <div class="container">
        <div class="tagline">Top Locations Under <span class="text-dark">GoRide's </span> Service Network</div>
        <div class="section-title mb-3">
            Most Popular <span>Travel Routes</span> among Our Cities
        </div>
     <div class="row accordion" id="routesAccordion">
         
        @foreach($seoTags['innerLinks'] as $kj => $value)

            <div class="col-md-6 col-12 mb-3">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $iii }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $iii }}"
                                aria-expanded="false"
                                aria-controls="collapse{{ $iii }}">
        
                            <span class="count"><i class="fa fa-route"></i></span>
                            {{ ucwords(strtolower($kj)) }} Call Taxis / Cab / Pick Up and Drop Taxi
                        </button>
                    </h2>
        
                    <div id="collapse{{ $iii }}"
                         class="accordion-collapse collapse"
                         aria-labelledby="heading{{ $iii }}"
                         data-bs-parent="#routesAccordion">
        
                        <div class="accordion-body routes-list">
                            <ul>
        
                                @foreach($value as $val)
        
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
        
                                    <li>
                                        <a href="/{{ $val['slug'] }}">
                                            <i class="fa-solid fa-location-dot route-icon"></i>
                                            {{ ucwords(strtolower($val['name'])) }}
                                            to
                                            {{ ucwords(strtolower($val['to_place'])) }}
                                            {{ $random }}
                                        </a>
                                    </li>
        
                                @endforeach
        
                            </ul>
                        </div>
        
                    </div>
                </div>
            </div>
        
            @php $iii++; @endphp
        
        @endforeach

     </div>

    </div>
</section>

<section class="agency-section">
     <div class="
     text-center">
    <div class="container-fluid">
        <div class="agency-btn-wraap" ><div class="section-title text-center 
        ">
                       Manage Smarter & Earn Better with &nbsp;<span> GoRide </span> Agency
                    </div>
      <a href="/agency" class="btn-agent-super">
     Become an Agency<i class="fas fa-hand-point-up ms-2"></i>
</a>
        
    </div>
</div>
</div>
</section>


<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close1" data-bs-dismiss="modal"><i class="fa fa-close"
                        style="width: 30px; height: 30px;border-radius: 50%; border: 1px solid #000;"></i></button>
                <!--         <h4 class="modal-title">Modal Header</h4> -->
            </div>
            <div class="modal-body text-center mt-4">
                <img src="https://goride.run/goride/img/logo-dark.png" class="logo-img" alt="" style="width: 200px;">
                <form class="frm-sec">
                    <div class="mb-3">
                        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                            placeholder="Login with Mobile Number">

                    </div>
                    <button type="submit" class="btn btn-primary mb-3" style="width: 100%;">Get OTP</button>
                    <span class="by-click-text ">Already have an account? <a
                            class="by-click-text under-line text-danger " href="login" contenteditable="false"
                            style="cursor: pointer;"> Sign In
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

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<div class="floating-dock">

    <!-- Call -->
    <a href="tel:+916369742104" class="dock-item">
        <i class="fa-solid fa-phone"></i>
        <span>Call</span>
    </a>

    <!-- WhatsApp -->
    <a href="https://api.whatsapp.com/send/?phone=916369742104&text=Hi%21%20Need%20a%20cab.%20Please%20connect.&type=phone_number&app_absent=0" class="dock-item">
        <i class="fa-brands fa-whatsapp"></i>
        <span>Whatsapp</span>
    </a>

    <!-- Book A Cab -->
<a href="javascript:void(0)" class="dock-item center-btn" id="bookCabBtn">
    <div class="center-icon">
        <i class="fa-solid fa-taxi driver"></i>
    </div>
    <span>Book A Cab</span>
</a>
    <!-- Android -->
    <a href="https://play.google.com/store/apps/details?id=com.shi.goride_customer" class="dock-item">
        <i class="fa-brands fa-android"></i>
        <span>Android App</span>
    </a>

    <!-- iOS -->
    <a href="https://apps.apple.com/us/app/goride-cab-bike-taxi-pool/id6763038270" class="dock-item">
        <i class="fa-brands fa-apple"></i>
        <span>iOS App</span>
    </a>

</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('WEBSITE_GOOGLE_KEY') }}" async defer></script>

<!--<script src="https://www.google.com/recaptcha/api.js?render={{ env('BOOKING_RECAPTCHA_SITE_KEY') }}"></script>-->


@endsection

@section('script')
<script>
var gorideToken = "{{ $token }}";
</script>
<script>
$("#bookCabBtn").click(function () {
    $('html, body').animate({
        scrollTop: $("#pickup-location").offset().top - 200
    }, 600, function () {
        $("#pickup-location").focus().click();
    });
});



    $(document).ready(function(){       
    //   notifyJobs()
    

    
        $("#mobile").on("input", function(){

            this.value = this.value.replace(/[^0-9]/g, '');
            if(this.value.length > 10){
                this.value = this.value.slice(0,10);
            }
        });
        
        $("#sendCusBtn").click(function(){

            let mobile = $("#mobile").val().trim();
            let btn = $(this);
    
            if(mobile.length !== 10){
                // alert("Please enter valid mobile number");
                showToast('error', 'Please enter valid mobile number', 3000);
                return;
            }
    
            btn.prop("disabled", true);
            btn.text("Sending...");
    
            $.ajax({
                url: "https://www.goride.net.in/api/send-fbw-template",
                type: "POST",
                headers: {
                    "X-Goride-Token": gorideToken
                },
                data: {
                    mobile: mobile,
                    type: "{{ encrypt('customer_app_link') }}"
                },
    
                success: function(res){
    
                    if(res.status){
                        // alert("Message sent successfully");
                        showToast('success', res.message, 3000);
                    }else{
                        showToast('error', res.message, 3000);
                        // alert(res.message);
                    }
    
                },
    
                error: function(xhr){
                    alert("Something went wrong");
                },
    
                complete: function(){
                    btn.prop("disabled", false);
                    btn.text("Send Link");
                }
    
            });
    
        });
        
    }); 
    
    document.addEventListener('DOMContentLoaded', function() {
  // Initialize carousel with auto rotation on mobile
  if (window.innerWidth < 768) {
    const myCarousel = document.getElementById('stepsCarousel');
    if (myCarousel) {
      const carousel = new bootstrap.Carousel(myCarousel, {
        interval: 4000, // Rotate every 4 seconds
        wrap: true,
        touch: true
      });
    }
  }
});
    function convertDateFormat(txt, type = 'full') {
        let dateString = txt;
    
        // Create Date object (replace space with T so it's ISO compatible)
        let dateObj = new Date(dateString.replace(" ", "T"));
    
        // Extract day and month
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = dateObj.toLocaleString('en-US', { month: 'short' });
    
        if (type === 'date') {
            // Return only date format (e.g., "05 Sep")
            return `${day} ${month}`;
        }
    
        // Extract time components
        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24h to 12h format
    
        // Return full format (e.g., "05 Sep 02:15 PM")
        let formattedDate = `${day} ${month} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
        return formattedDate;
    }

    
    $('.agency-carousel').owlCarousel({
        items: 1,
        loop: true,
        margin: 10,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 4000,
        smartSpeed: 700,
        touchDrag: true,
        mouseDrag: true
    });
    function notifyJobs() {
        
        if(true){
            $.ajax({
            url: "{{ env('APP_API') }}notify-jobs",
            type: 'POST',
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
                    $('#notify_jobs').empty();
                    let jobs_content = '';
                    if (data.length > 0) {

                        $.each(data, function (index, value) {

                            // let expiryTime = expiryCals(value.pickup_date);
                            value.pickup_date = convertDateFormat(value.pickup_date);
                            value.dropoff_date = value.dropoff_date ? convertDateFormat(value.dropoff_date, 'date') : '';
                            
                            let j_type = value.job_type == 'oneway' ? 'One Way' : 'Round Trip';

                            jobs_content +=

                                `<div class="notify-card blue aos-init aos-animate" >
                                    <img  src="https://www.goride.net.in/goride/img/bell.gif"  style="height: 42px; width: 42px"/>
                                    <div>
                                      <h6>${value.from_place} → ${value.to_place} <span class="badge ms-3">${j_type}</span></h6>
                                      <div class="d-flex gap-2">
                                        <p class="m-0 text-dark fw-bold">
                                          <strong class="text-danger fw-bold me-1">Pickup:</strong> ${value.pickup_date}
                                        </p>
                                        <p class="m-0 text-dark fw-bold ${value.dropoff_date ? '' : 'd-none'}">
                                          <strong class="text-success fw-bold me-1">Return:</strong> ${value.dropoff_date}
                                        </p>
                                      </div>
                                    </div>
                                  </div>`

                                ;
                        })
                        
                        jobs_content += `
                            <div class="d-flex justify-content-center align-items-center">
                                <a href="{{ env('APP_URL') }}jobs" class="see-jobs-btn px-3">View Jobs</a>
                            </div>
                            
                        `;

                    }
                    else {
                        jobs_content = `
                                        
                                        <div class="notify-card blue aos-init aos-animate d-flex justify-content-center" >
                                                <i class="fa-solid fa-briefcase text-danger"></i>
                                              <h6>No More Jobs</h6>
                                              
                                          </div>
                                    `;
                                    
                        // hasMore = false;
                    }
                    $('#notify_jobs').html(jobs_content);
                    // // console.log(response.data);
                    
                    // page = response.data.next_page;
                    // hasMore = !!response.data.next_page;
                } else {
                    showToast('error', response.message, 3000);
                }
                
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            // complete: function () {
            //     loading = false;
            //     $("#loader").hide();
            // }
    });
        }
        
    
    }
 
 
 
// Stats Count start
function startCounter(){

const counters = document.querySelectorAll(".counter");

counters.forEach(counter => {

const target = +counter.getAttribute("data-count");
let count = 0;
const speed = target / 100;

function update(){

if(count < target){
count += speed;
counter.innerText = Math.ceil(count) + "+";
requestAnimationFrame(update);
}else{
counter.innerText = target + "+";
}

}

update();

});

}


/* detect section on scroll */

const observer = new IntersectionObserver(entries => {

entries.forEach(entry => {

if(entry.isIntersecting){
startCounter();
}

});

},{ threshold:0.5 });

observer.observe(document.querySelector(".stats-section"));

// Stats Count end

</script>

@endsection