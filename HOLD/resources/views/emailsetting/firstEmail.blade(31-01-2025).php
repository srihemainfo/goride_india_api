@extends('dashboard-layout.index')



@section('content')

<style>
.x_panel{
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 1px 12px rgba(0, 0, 0, .15);

}
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

#paymentModalLabel{  

    margin-top: 101px !important;}

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

    width: 24%;

    margin: 0 auto;

    left: 82px;

    right: 182px;

    top: 73px;

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

    left: 66px;

    top:57px;

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

        background: #2da150;

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

    top: 38px;

    font-style: normal;

    font-weight: 400;

    white-space: nowrap;

    left: 51px;
    transform: translate(78%, -50%);

    font-size: 12px;

    font-weight: 700;

    color: #000;

    right: 42%;

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

.prev-step{

        background-color: #152e41;

        color: #fff;

}

.prev-step,

.next-step{

    font-size: 13px;

    padding: 8px 24px;

    border: none;

    border-radius: 4px;

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
    span.round-tab{
        display: none;
    }
    .connecting-line{
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

</style>

<!-- @include('customers.partials.filter') -->

<style>

    .icheckbox_flat-blue,

.iradio_flat-blue {

  display: inline-block;

  vertical-align: middle;

  margin: 0;

  padding: 0;

  width: 20px;

  height: 20px;

  background: url(../images/blue.png) no-repeat;

  border: none;

  cursor: pointer;

}

.icheckbox_flat-blue {

  background-position: 0 0;

}

.icheckbox_flat-blue.checked {

  background-position: -22px 0;

}

.icheckbox_flat-blue.disabled {

  background-position: -44px 0;

  cursor: default;

}

.icheckbox_flat-blue.checked.disabled {

  background-position: -66px 0;

}

.iradio_flat-blue {

  background-position: -88px 0;

}

.iradio_flat-blue.checked {

  background-position: -110px 0;

}

.iradio_flat-blue.disabled {

  background-position: -132px 0;

  cursor: default;

}

.iradio_flat-blue.checked.disabled {

  background-position: -154px 0;

}

</style>

 <div class="modal-overlay"></div>

<div class="modal fade" id="EmailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-modal="true" role="dialog" >

  <div class="modal-dialog">

    <div class="modal-content">

         <div class="wizard">

                        <div class="wizard-inner">

                            <div class="connecting-line"></div>

                            <ul class="nav nav-tabs" role="tablist">

                                <li role="presentation" class="active">

                                    <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><span class="round-tab"><i class="fa-solid fa-check"></i> </span> <i class="book-id">Payment Options</i></a>

                                </li>

                                <li role="presentation" class="active">

                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Email Settings</i></a>

                                </li>

                                <!-- <li role="presentation" id="comp-path">

                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Complete</i></a>

                                </li> -->

                            </ul>

                        </div>

            

    

      

    <div class="x_panel">

        <div class="x_title">

                            <h2 class="text-center" id="paymentModalLabel">Email Settings</h2>

                        </div>

        <div class="x_content"style="padding: 0 20px;margin-top: -33px;">

            <div class="x_content-container">

                <form id="formSettingsEmail" class="form-horizontal form-label-left" method="post" data-parsley-validate novalidate enctype="multipart/form-data">

                    <div class="row mb-3 mt-5 align-items-center">

                        <label for="mailer_type1" class="form-label col-md-5 col-sm-5">

                            Email Services

                        </label>

                        <div class="col-md-7 col-sm-7 d-flex align-items-center">

                            <div class="form-check me-3">

                                <input 

                                    onchange="checkEmailType(this)"

                                    type="radio" 

                                    name="mailer_type" 

                                    class="form-check-input flat " 
                                    checked
                                    id="mailer_type1" 

                                    value="GoRide">

                                <label class="form-check-label"  for="mailer_type1">Default Email</label>

                            </div>

                            <div class="form-check">

                                <input 

                                    onchange="checkEmailType(this)"

                                    type="radio" 

                                    name="mailer_type" 

                                    class="form-check-input flat" 

                                    id="mailer_type2" 

                                    value="SMTP">

                                <label class="form-check-label" for="mailer_type2">SMTP</label>

                            </div>

                        </div>

                    </div>

                    <div class="row mb-3 email-hide" style="display:none;">

                        <label class="form-label col-md-5 col-sm-5">From Email</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="text" id="from_email" name="from_email" class="form-control" maxlength="100" data-parsley-maxlength="100">

                        </div>

                    </div>

                    <div class="row mb-3">

                        <label class="form-label col-md-5 col-sm-5">From Name</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="text" id="from_name" name="from_name" class="form-control" maxlength="100" data-parsley-maxlength="100">

                        </div>

                    </div>

                    <div id="smtpSetup" class="email-hide" style="display:none;">

                        <div class="row mb-3">

                            <label class="form-label col-md-5 col-sm-5">SMTP Host</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_host" name="smtp_host" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                        <div class="row mb-3">

                            <label class="form-label col-md-5 col-sm-5">SMTP Port</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_port" name="smtp_port" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                        <div class="row mb-3">

                            <label class="form-label col-md-5 col-sm-5">Encryption</label>

                            <div class="col-md-7 col-sm-7">

                                <select name="encryption_type" id="encryption_type" class="form-select">

                                    <option value="">None</option>

                                    <option value="SSL">SSL</option>

                                    <option value="TLS" selected>TLS</option>

                                </select>

                            </div>

                        </div>

                        <div class="row mb-3">

                            <label class="form-label col-md-5 col-sm-5">SMTP Username</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="smtp_user_name" name="smtp_user_name" class="form-control" maxlength="100" data-parsley-maxlength="100">

                                <input type="hidden" id="emailsettingid" name="emailsettingid">

                            </div>

                        </div>

                        <div class="row mb-3">

                            <label class="form-label col-md-5 col-sm-5">SMTP Password</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="password" id="smtp_password" name="smtp_password" class="form-control" maxlength="100" data-parsley-maxlength="100">

                            </div>

                        </div>

                       

                    </div>

                </form>

            </div>

        </div>

        <!--<div class="x_title">-->

        <!--    <h4 class="text-center">Send a test email</h4>-->

        <!--</div>-->

        <!--<div class="x_content" style="padding:0 20px">-->

        <!--    <div class="x_content-container">-->

        <!--        <form id="emailForm" enctype="multipart/form-data">-->

        <!--            <div class="row mb-3">-->

        <!--                <label class="form-label col-md-5 col-sm-5">Send To</label>-->

        <!--                <div class="col-md-7 col-sm-7">-->

        <!--                    <input type="email" id="txtSendTo" name="txtSendTo" class="form-control" maxlength="100" data-parsley-maxlength="100">-->

        <!--                </div>-->

        <!--            </div>-->

        <!--            <div class="col-12 text-center mt-3 mb-4">-->

        <!--                <button type="submit" id="sbtSendEmail" class="btn btn-primary">Send Email</button>-->

                          

        <!--            </div>-->

                     

        <!--        </form>-->

        <!--    </div>-->

        <!--</div>-->

        <div class="text-center mt-1 mb-4">

            <!--<button type="submit" class="btn" style="background: #146dbb; color: #fff;font-size: 16px;font-weight: 600;" id="prev-btn">Save and Previous</button>-->

            <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">Submit</button>

        </div>

    </div>

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

   



</style>

@endsection



@section('custom_scripts')



    @include('emailsetting.partials.customers_js')

@endsection

