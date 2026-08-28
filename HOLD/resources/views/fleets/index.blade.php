    


@extends('dashboard-layout.index')

@section('content')
<style>
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
	height: 50px;
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
    left: 77%;
    /* top: 77%; */
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
    .car_li{
      font-size: 12px !important;
    }
    .who-dr {
    padding: 22px 0 21px 0px;
    /* width: 277px; */
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
<div class="col-sm-9  main-card mb-3 card" id="fleet_card">
    
    <div class="card-header">
        <h4 class="card-title">Fleet List</h4>
        <div class="btn-actions-pane-right">
                <button type="button" class="btn btn-success" id="addFleet" data-toggle="modal" data-target="#vehichle_modal"><i class="fas fa-plus"></i> Add Fleet </button>
        </div>
    </div>
    <div class="card-body">

    <div class="row justify-content-center">   

    <div class="row listoffleets"></div>




                    </div>
    </div>
</div>
<div class="col-sm-2 main-card mb-3 card  d-none d-lg-block position">
  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <a class="nav-link active text-light" id="vert-tabs-right-home-tab" href="/fleet" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">
      <i class="fa-solid fa-car" style="margin-right: 8px;"></i> List Fleets
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/offertimes" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Offer Times
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-offer-days-tab" href="/offerdays" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
      <i class="fa-solid fa-calendar-days" style="margin-right: 8px;"></i> Offer Days
    </a>
    
    <!-- <a class="nav-link text-light" id="vert-tabs-right-promo-code-tab" href="/promocode" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i> Promo Code
    </a>
    
    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/notifications" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">
      <i class="fa-regular fa-bell" style="margin-right: 8px;"></i> Notification
    </a> -->
    
  </div>
</div>
</div>
</div>
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

<!-- Modal -->
<div class="modal right fade" id="vehichle_modal" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
       
        <h4 class="modal-title">Add Fleet</h4>
        <button type="button" class="close" data-dismiss="modal" onclick="reset()" id="flfrm_dis">&times;</button>
      </div>
<div class="modal-body">
       <form method="post" id="fleet_create_form"  enctype="multipart/form-data">
       
          <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="name" class="col-form-label">Fleet Name<span class="required">&nbsp;*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Fleet Name" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 20);">
                            <p class="text-danger invalid-fleet-name"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="passenger" class="col-form-label">Passengers<span class="required">&nbsp;*</span></label>
                            <input type="number" class="form-control" name="passenger" id="passenger" placeholder="Enter passenger count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);">
                            <p class="text-danger invalid-passenger"></p>
                        </div>
                    </div>
                </div>
<div class="row">
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="luggage" class="col-form-label">luggage<span class="required">&nbsp;</span></label>
                            <input type="number" class="form-control" name="luggage" id="luggage" placeholder="Enter luggage count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-luggage"></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="hand_luggage" class="col-form-label">Hand Luggage<span class="required">&nbsp;</span></label>
                            <input type="number" class="form-control" name="hand_luggage" id="hand_luggage" placeholder="Enter hand luggage count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-hand-luggage"></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="child" class="col-form-label">Child Seats/Booster<span class="required">&nbsp;</span></label>
                            <input type="number" class="form-control" name="child" id="child" placeholder="Enter child seats count" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
                            <p class="text-danger invalid-child"></p>
                        </div>
                    </div>
                    
    <div class="col-sm-6">
      <div class="form-group">
          <label for="name" class="col-form-label">Fleet Image<span class="required">&nbsp;*</span></label>
          <input type="file" class="" name="file" id="fileInput" placeholder="Enter Fleet Image" accept=".jpg, .jpeg, .webp, .png">
          <p class="text-danger invalid-image"></p>
         <img id="edit_imagePreview" name="edit_imagePreview" src="">
      </div>
      
      <input type="hidden" name="fleet_id" id="fleet_id">

      <div class="mt-3">
          <img id="croppedImage" alt="Cropped Fleet Image" style="display:none; max-width:100%;">
      </div>
  </div>
                </div>
<input type="hidden" id="hidden_imageName" name="upload_photo">


                </form>
        </div>
      <div class="modal-footer">
        <button type="button" id="fleet_create_sub" class="btn btn-success">Save</button>
        <button type="button" class="btn btn-default close-btn" data-dismiss="modal"  onclick="reset()">Close</button>
      </div>
    </div>
    
  </div>
</div>

<script> 
    
$(document).ready(function(){
    $('.close-sidebar-btn').click(function(){
        $('#fleet_card').toggleClass('small_screen');
    });
});
    
</script>

    @include('fleets.partials.add_fleet_modal')
@endsection

@section('custom_scripts')
    @include('fleets.partials.fleet_js')
@endsection