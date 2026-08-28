    


@extends('dashboard-layout.index')

@section('content')
<style>

.prev-step{
        background-color: #152e41;
        color: #fff;
}
    .car_li {
    color: #f3ba00;
    font-size: 14px;
    font-weight: 500;
}
.driprofile {
    transform: scaleX(-1);
    width: 234px;
    padding: 7px;
    margin: 1px 3px 3px 51px;
}
.ediicon{
    list-style-type: none; 
    margin: 3px;
    padding: 7px 8px 7px 8px;
    background: #254bd9;
    color: #fff;
     border-radius: 6px;
     border: none;
}
.btn-position{
  top:50%;
  left:50%;
  transform:translate(-50%, -50%);
  position:absolute;
}
/*.detail-sec{*/
/*  float: right;*/
/*    position: absolute;*/
/*    top: 20%;*/
/*    right: 17%;*/
    /*border-left: 1px solid #cdc3c3;*/
/*    padding: 1px 1px 0px 17px;*/
/*}*/

.as-sec {
    position: absolute;
    right: 5%;
    top: 3%;
}
.switch {
    --circle-dim: 1.4em;
    font-size: 17px;
    position: relative;
    display: inline-block;
    width: 48px;
    height: 22px;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #f5aeae;
  transition: .4s;
  border-radius: 30px;
}

.slider-card {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  border-radius: 20px;
  left: 3px;
  bottom: 3px;
  transition: .4s;
  pointer-events: none;
}
.modal-body{
            overflow-y: visible !important;
}
.slider-card-face {
  position: absolute;
  inset: 0;
  backface-visibility: hidden;
  perspective: 1000px;
  border-radius: 50%;
  transition: .4s transform;
}

.slider-card-front {
  background-color: #DC3535;
}

.slider-card-back {
  background-color: #379237;
  transform: rotateY(180deg);
}

input:checked ~ .slider-card .slider-card-back {
  transform: rotateY(0);
}

input:checked ~ .slider-card .slider-card-front {
  transform: rotateY(-180deg);
}

input:checked ~ .slider-card {
  transform: translateX(24px);
}

input:checked ~ .slider {
  background-color: #9ed99c;
}
.modal.right.fade.in .modal-dialog {
right:0 !important;
transform: translateX(-50%);
}
.modal.right .modal-content {
height:90%;
border-radius:0;
}
.modal.right .modal-dialog {
position: fixed;
margin: auto;
height: 100%;
-webkit-transform: translate3d(0%, 0, 0);
-ms-transform: translate3d(0%, 0, 0);
-o-transform: translate3d(0%, 0, 0);
transform: translate3d(0%, 0, 0);
width: 100%;
}
.modal.right.fade.in .modal-dialog {
transform: translateX(0%);
}
.modal.right.fade .modal-dialog {
right: 1%;
-webkit-transition: opacity 0.3s linear, right 0.3s ease-out;
-moz-transition: opacity 0.3s linear, right 0.3s ease-out;
-o-transition: opacity 0.3s linear, right 0.3s ease-out;
transition: opacity 0.3s linear, right 0.3s ease-out;
}
.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 7px 10px 11px 10px;
    border-bottom: 1px solid #e9ecef;
    border-top-left-radius: 0.3rem;
    border-top-right-radius: 0.3rem;
}    
    
   .modal.right .modal-header {background-color:#0f4260; color:#fff}
    .modal.right .modal-header::after {content:""; display:inline-block;}
    .modal.right .close {text-shadow:none; opacity:1; color:#ff4d4d; font-size:26px}

    .form-group{
	margin-bottom: 5px;
}
.form-group > label{
	display: block;
	font-size: 14px;	
	color: #000;
}
.custom-control-label{
	color: #000;
	font-size: 16px;
}
.form-control{
	height: 35px;
	background: #ecf0f4;
	border-color: transparent;
	padding: 0 15px;
	font-size: 13px;
	-webkit-transition: all 0.3s ease-in-out;
	-moz-transition: all 0.3s ease-in-out;
	-o-transition: all 0.3s ease-in-out;
	transition: all 0.3s ease-in-out;
}
.form-control:focus{
	border-color: #00bcd9;
	-webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
	-moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
	box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
}
textarea.form-control{
	height: 160px;
	padding-top: 15px;
	resize: none;
}
.who-dr{
  border: 1px solid #cdc3c3;
  border-radius: 10px;
      display: flex;
    gap: 10px;
    align-items: center;
  box-shadow: 0 0 15px rgb(0 0 0 / 27%);
  padding: 14px 0 12px 16px;
}
    .btn {border-radius:0}

    @media (max-width: 600px) {
      .driprofile {
       margin: 0;
    }
    .detail-sec{
      right:12%;
    }
    .as-sec {
    position: absolute;
    /*left: 26%;*/
    /*top: 77%;*/
}
}
@media (max-width: 740px) {
      .driprofile {
       margin: 0;
       width: 187px;
    }
    .detail-sec{
      border:none !important;
      top:27px;
    }
    .who-dr {
    padding: 22px 0 21px 18px;
}
.img-flx{
        width: 180px !important;
    height: 175px !important;
}
}
@media (min-width: 320px) and (max-width: 411px) {
    .img-flx {
              width: 143px !important;
        height: 143px !important;
    }
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <style>
        img {
            max-width: 100%;
        }
    </style>
</head>
<style>
.nav-tabs .nav-link:hover  {
    background-color: #747474 !important;
    color: white !important; 
}
.nav-link.active {
  background-color: #fff !important;
  color:#343a40 !important;
}

.nav-link:hover {
  background-color: #6c757d !important; 
}

.small_screen .img-sec .img-flx {
    width: 180px !important;
    height: 180px !important;
    padding: 0px 21px 8px 10px !important;
    border-right: 1px solid #cdc3c3;
}

</style>
<style>
.nav-tabs .nav-link:hover  {
    background-color: #747474 !important;
    color: white !important; 
}
.nav-link.active {
  background-color: #fff !important;
  color:#343a40 !important;
}

.nav-link:hover {
  background-color: #6c757d !important; 
}
 .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: black;
      opacity: 0.5;
      z-index: 1040; /* Slightly below the modal's z-index */
      display: none; /* Initially hidden */
    }

    /* Show the overlay when the modal is open */
    body.modal-open .modal-overlay {
      display: block;
    }
    /*---------signup-step-------------*/
.bg-color{
	background-color: #333;
}
.signup-step-container{
	padding-top: 60px ;

}

    .wizard .nav-tabs {
        position: relative;
        margin-bottom: 0;
        border-bottom-color: transparent;
    }

    .wizard > div.wizard-inner {
            position: relative;
    margin-bottom: 50px;
    text-align: center;
    left:7%;
    }

.connecting-line {
   height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 47%;
    margin: 0 auto;
    left: 82px;
    right: 182px;
    top: 15px;
    z-index: 1;

}

.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
    color: #555555;
    cursor: default;
    border: 0;
    border-bottom-color: transparent;
}

span.round-tab {
    width: 30px;
    height: 30px;
    line-height: 30px;
    display: inline-block;
    border-radius: 50%;
    background: #fff;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 16px;
    color: #0e214b;
    font-weight: 500;
    border: 1px solid #ddd;
}
span.round-tab i{
    color:#555555;
}
.wizard li.active span.round-tab i{
 color: #fff !important;
}
.wizard li.active span.round-tab {
        background: #2da150 !important;
    color: #fff;
    border-color: #2da150;
}
.wizard li.active span.round-tab i{
    color: #5bc0de;
}
.wizard .nav-tabs > li.active > a i{
	color: #2b5d81;
}

.wizard .nav-tabs > li {
    width: 25%;
}

.wizard li:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 0;
    margin: 0 auto;
    bottom: 0px;
    border: 5px solid transparent;
    border-bottom-color: red;
    transition: 0.1s ease-in-out;
}



.wizard .nav-tabs > li a {
    width: 30px;
    height: 30px;
    margin: 20px auto;
    border-radius: 100%;
    padding: 0;
    background-color: transparent;
    position: relative;
    top: 0;
}
.book-id{
	    position: absolute;
    top: -18px;
    font-style: normal;
    font-weight: 400;
    white-space: nowrap;
    /* left: 83%; */
    transform: translate(78%, -50%);
    font-size: 12px;
    font-weight: 700;
    color: #000;
    right: 42%;
}
}

    .wizard .nav-tabs > li a:hover {
        background: transparent;
    }

.wizard .tab-pane {
    position: relative;
    padding-top: 0px;
}

.x_title{
    margin-bottom:20px;
}
.wizard h3 {
    margin-top: 0;
}
.next-step{
	background-color: #152e41;
    color: #fff;
}
.skip-btn{
	background-color: #cec12d;
}
.step-head{
    font-size: 20px;
    text-align: center;
    font-weight: 500;
    margin-bottom: 20px;
}
.term-check{
	font-size: 14px;
	font-weight: 400;
}
.custom-file {
    position: relative;
    display: inline-block;
    width: 100%;
    height: 40px;
    margin-bottom: 0;
}
.custom-file-input {
    position: relative;
    z-index: 2;
    width: 100%;
    height: 40px;
    margin: 0;
    opacity: 0;
}
.custom-file-label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1;
    height: 40px;
    padding: .375rem .75rem;
    font-weight: 400;
    line-height: 2;
    color: #495057;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: .25rem;
}
.custom-file-label::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    display: block;
    height: 38px;
    padding: .375rem .75rem;
    line-height: 2;
    color: #495057;
    content: "Browse";
    background-color: #e9ecef;
    border-left: inherit;
    border-radius: 0 .25rem .25rem 0;
}
.footer-link{
	margin-top: 30px;
}
.all-info-container{

}
.list-content{
	margin-bottom: 10px;
}
.list-content a{
	padding: 10px 15px;
    width: 100%;
    display: inline-block;
    background-color: #f5f5f5;
    position: relative;
    color: #565656;
    font-weight: 400;
    border-radius: 4px;
}
.list-content a[aria-expanded="true"] i{
	transform: rotate(180deg);
}
.list-content a i{
	text-align: right;
    position: absolute;
    top: 15px;
    right: 10px;
    transition: 0.5s;
}
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: #fdfdfd;
}
.list-box{
	padding: 10px;
}
.signup-logo-header .logo_area{
	width: 200px;
}
.signup-logo-header .nav > li{
	padding: 0;
}
.signup-logo-header .header-flex{
	display: flex;
	justify-content: center;
	align-items: center;
}
.list-inline li{
    display: inline-block;
}
.pull-right{
    float: right;
}
/*-----------custom-checkbox-----------*/
/*----------Custom-Checkbox---------*/
input[type="checkbox"]{
    position: relative;
    display: inline-block;
    margin-right: 5px;
}
input[type="checkbox"]::before,
input[type="checkbox"]::after {
    position: absolute;
    content: "";
    display: inline-block;   
}
input[type="checkbox"]::before{
    height: 16px;
    width: 16px;
    border: 1px solid #999;
    left: 0px;
    top: 0px;
    background-color: #fff;
    border-radius: 2px;
}
input[type="checkbox"]::after{
    height: 5px;
    width: 9px;
    left: 4px;
    top: 4px;
}
input[type="checkbox"]:checked::after{
    content: "";
    border-left: 1px solid #fff;
    border-bottom: 1px solid #fff;
    transform: rotate(-45deg);
}
input[type="checkbox"]:checked::before{
    background-color: #18ba60;
    border-color: #18ba60;
}


@media (max-width: 767px){
	.sign-content h3{
		font-size: 40px;
	}
	.wizard .nav-tabs > li a i{
		display: none;
	}
	.signup-logo-header .navbar-toggle{
		margin: 0;
		margin-top: 8px;
	}
	.signup-logo-header .logo_area{
		margin-top: 0;
	}
	.signup-logo-header .header-flex{
		display: block;
	}
}

.dropdown-currency{
    cursor:pointer;
}
.round-tab  {
        width: 30px;
    height: 30px;
    line-height: 30px;
    display: inline-block;
    border-radius: 50%;
    background: #fff;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 16px;
    color: #0e214b;
    font-weight: 500;
    border: 1px solid #ddd;

}
.prev-step:hover{
   color:#fff; 
}
</style>
<!-- Modal -->
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!--<div class="modal-header">-->
        <!--  <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>-->
          <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
        <!--</div>-->
        <div class="modal-body">
             <section class="signup-step-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="wizard">
             <div class="wizard-inner">
                            <div class="connecting-line"></div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><span class="round-tab"><i class="fa-solid fa-check"></i> </span> <i class="book-id">Booking Settings</i></a>
                                </li>
                                <li role="presentation"  class="active">
                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Fleet Creation</i></a>
                                </li>
                                <li role="presentation" id="complete-path" class="">
                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Complete</i></a>
                                </li>
                            </ul>
                        </div>
                        
       <form method="post" id="fleet_create_form"  enctype="multipart/form-data">
       
          <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Fleet Name<span class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Fleet Name" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 30);">
                            <p class="text-danger invalid-fleet-name"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="passenger" class="col-form-label">Passengers<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="passenger" id="passenger" placeholder="Enter passenger count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-passenger"></p>
                        </div>
                    </div>
                </div>
<div class="row">
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="luggage" class="col-form-label">luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="luggage" id="luggage" placeholder="Enter luggage count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-luggage"></p>
                        </div>
                    </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                            <label for="hand_luggage" class="col-form-label">Hand Luggage<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="hand_luggage" id="hand_luggage" placeholder="Enter hand luggage count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-hand-luggage"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                   
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="child" class="col-form-label">Child Seats<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="child" id="child" placeholder="Enter child seats count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-child"></p>
                        </div>
                    </div>
                     <div class="col-sm-6">
                        <div class="form-group">
                            <label for="order" class="col-form-label">Booster</label>
                            <input type="number" class="form-control" name="booster" id="booster" placeholder="Enter booster count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-booster"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                   
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="order" class="col-form-label">Order</label>
                            <input type="number" class="form-control" name="order" id="order" placeholder="Enter order" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-order"></p>
                        </div>
                    </div>
                
    <div class="col-sm-6">
        <div class="form-group">
            <label for="name" class="col-form-label">Fleet Image<span class="required">&nbsp;*</span></label>
            <input type="file" class="" name="file" id="fileInput" placeholder="Enter Fleet Image">
            <p class="text-danger invalid-image"></p>
           <img id="edit_imagePreview" name="edit_imagePreview" src="">
        </div>
        
        <input type="hidden" name="fleet_id" id="fleet_id">

        <div class="mt-3">
            <img id="croppedImage" alt="Cropped Fleet Image" style="display:none; max-width:100%;">
        </div>
    </div>
</div>


                </form>
        
<div class="col-12 mt-0 text-center">
        
           <!--<button type="button" name="sbtUpdate" class="btn" style="background: #146dbb; color: #fff;font-size: 16px;font-weight: 600;" id="sbtUpdate">-->
           <!--     Save and Previous-->
           <!-- </button>-->

           <button type="button" id="fleet_create_sub" class="text-center btn btn-primary">Submit</button>
 </div>
    </div>
      </div>
    </div>
    </div>
    </section>
  </div>
    </div> 
    </div>
      </div>


<script>
    
$(document).ready(function(){
    $('#staticBackdrop').modal('show');
    $('.close-sidebar-btn').click(function(){
        $('#fleet_card').toggleClass('small_screen');
    });
});
    
</script>
@endsection

@section('custom_scripts')
    @include('fleets.partials.fleet_js')
@endsection