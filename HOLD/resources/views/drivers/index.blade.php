

@extends('dashboard-layout.index')



@section('content')



@include('drivers.partials.filter')



<style>

.small_screen .img-sec .img-flx {

    width: 180px !important;

    height: 180px !important;

    padding: 0px 21px 8px 10px !important;

    border-right: 1px solid #cdc3c3;

}
.img-flx{
  width: 215px !important;
}

    .car_li {

    color: #f3ba00;

    font-size: 14px;

    font-weight: 500;

}

.driprofile {

    transform: scaleX(-1);

    border-radius: 50%;

    width: 138px;

    border: 2px solid #d9d3d3;

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

/*    top: 17%;*/

/*    right: 10%;*/

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

overflow:auto;

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

      right: 3%;

    }

}

@media (max-width: 990px) {

      .driprofile {

       margin: 0;

    }

    .detail-sec{

      border:none !important;
      margin-right: 80px;

    }
    .who-dr{
      flex-direction: column;
      
    }
    .car_li{
      font-size: 13px !important;
    }
    .img-flx{
      border: none !important;
    }
    .as-sec{
      right: 7%;
    }

}

</style>



<div class="col-sm-12 main-card mb-2 card">

    <div class="card-header">

        <h4 class="card-title">Driver List</h4>

        <div class="btn-actions-pane-right">                     

                <a href="{{ route('driver.create') }}" class="btn btn-success"><i class="fas fa-plus"></i>  Driver Create</a>          

        </div>

    </div>

    <div class="card-body">

    <div class="row justify-content-center">   



<div class="row listofdriver" id="listofdriver"></div>



                    </div>

    </div>

</div>



<!-- Modal -->

<div class="modal right fade" id="myModal" role="dialog">

  <div class="modal-dialog">

  

    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

       

        <h4 class="modal-title">Edit Profile</h4>

        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>

      <div class="modal-body">

      

          <div class="row">          

          <div class="col-md-12">

					<div class="form-group">

						<label id="name-label" for="name">Name</label>

						<input type="text" name="name" id="name" placeholder="Enter your name" class="form-control" required>

					</div>

				</div>

          <div class="col-md-12">

					<div class="form-group">

						<label id="email-label" for="email">Email</label>

						<input type="email" name="email" id="email" placeholder="Enter your email" class="form-control" required>

					</div>

				</div>



               <div class="col-md-12">

					<div class="form-group">

						<label id="mobile-label" for="mobile">Mobile</label>

						<input type="mobile" name="mobile" id="mobile" placeholder="Enter your Number" class="form-control" required>

					</div>

				</div>

                <div class="col-md-12">

					<div class="form-group">

                    <label for="formFile" class="form-label">Upload Profile Photo</label>

                <input class="form-control" type="file" id="formFile">

					</div>

				</div>



                <div class="col-md-12">

					<div class="form-group">

						<label>Leave Message</label>

						<textarea  id="comments" class="form-control" name="comment" placeholder="Enter your comment here..."></textarea>

					</div>

				</div>

             

              

              

      </div>

        </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-success">Save</button>

        <button type="button" class="btn btn-default close-btn" data-dismiss="modal">Close</button>

      </div>

    </div>

    

  </div>

</div>



    @include('drivers.partials.password_change_modal')

@endsection



@section('custom_scripts')

    @include('drivers.partials.driver_js')

@endsection

