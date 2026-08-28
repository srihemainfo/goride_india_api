@extends('dashboard-layout.index')



@section('content')

<style>
   .submit-btnnn {

      display: none;

   }

   .myprofile-head {

      font-size: 24px;

      font-weight: 500;

   }

   .nav-pills .pf-ac.active,
   .nav-pills .show>.pf-ac {

      background-color: crimson;

   }



   a {

      color: crimson;

   }



   a:hover {

      color: pink;

   }

   h1 {



      color: pink;

   }

   #myTabContent {

      box-shadow: 0px 4px 16px 0px rgba(0, 0, 0, 0.16);

      padding: 10px;

      background: #f3f3f3;

      border-radius: 10px;

   }

   .pf-name {

      font-size: 18px;

      font-weight: 500;

      color: #0e4260;

   }

   .pf-company {

      font-weight: 500;

      color: #0e4260;

      font-size: 18px;

   }

   .pf-email {

      font-weight: 500;

      color: #0e4260;

      font-size: 18px;

   }

   .pf-number {

      font-weight: 500;

      color: #0e4260;

      font-size: 18px;

   }

   .pf-plan {

      font-weight: 500;

      color: #0e4260;

      font-size: 18px;

   }

   .pf-plan {

      font-weight: 500;

      color: #0e4260;

      font-size: 18px;

   }

   .pf-form {

      color: #003757;

   }

   .icon-border {

      /* border: 1px solid; */

      padding: 11px 16px;

      border-radius: 30px;

      background: #d7d9d9;

   }



   .profile_name {

      font-size: 20px;

      color: #0d8545;

      font-family: -webkit-body;

      font-weight: 600;

      letter-spacing: 2px;

   }

   .img-one {

      padding: 16px 0px;

   }

   /* .nav-pills {

      margin-bottom: 0 !important;

   } */

   .nav-pills .nav-link:hover {

      color: #fff !important;

      background: #f3ba00 !important;

   }

   .nav-item.pf {

      padding: 0px 2px;

   }

   /*profile*/

   .profile-cover__img .profile-img-1 {

      position: relative;

      display: inline-block;

   }

   .profile-cover__img .profile-img-1 img {

      border: 2px solid #017e90;

      border-radius: 50%;

      width: 7rem;

      height: 7rem;

   }

   .fw-semibold {

      font-weight: bold;

   }

   .table-responsive:before {

      content: "";

      position: absolute;

      width: 100%;

      height: 100%;

      background-color: rgb(49 35 81 / 52%);

      inset-inline-start: 0;

      inset-inline-end: 0;

      inset-block-start: 0;

      inset-block-end: 0;

   }

   .table-responsive {

      background-image: url(https://img.freepik.com/free-photo/white-sport-car-with-black-autotuning-driving-with-high-speed-road_114579-4072.jpg?w=740&t=st=1699016678~exp=1699017278~hmac=5bac6654a5ea763dfbd24f921c2ad577c1dae4cd1896dffcf2f4dcad9a3b699b);

      background-size: cover;

      background-position: center;

      background-repeat: no-repeat;

      position: relative;

      z-index: 9;

      height: 216px;

   }

   .table-responsive h5 {

      color: #fff;

      z-index: 10;

      letter-spacing: 1px;

      position: relative;

      font-weight: 600;

      background: #F9BF00;

      width: fit-content;

      padding: 4px 10px;

      border-radius: 7px;

   }

   .fw-semibold {

      font-weight: bold;

      z-index: 10;

      color: #fcfcfc;

      position: relative;

   }

   .same {

      color: #f3efef;

      font-weight: 500;

   }

   .profile-pic {
      width: 200px;
      max-height: 200px;
      display: inline-block;
   }

   .file-upload {
      display: none;
   }

   .circle {
      border-radius: 100% !important;
      overflow: hidden;
      width: 128px;
      height: 128px;
      border: 2px solid rgba(255, 255, 255, 0.2);
      position: absolute;
      top: 11px;
      right: -65px;
   }

   img {
      max-width: 100%;
      height: auto;
   }

   .p-image {
      position: absolute;
      top: 100px;
      color: #666666;
      transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
   }

   .upload-button {
      font-size: 20px;
      color: #017e90;
      margin-left: 41px;
      font-weight: 700;
   }
   .toastr.options{
      background-color: white;
      color: black;
      font-weight: 600;
      }
</style>



<div class="col-sm-12 main-card mb-3 card">

   <div class="container-fluid my-4">

      <div class="row justify-content-center">

         <div class="col-md-9 main">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">

               <li class="nav-item pf" role="presentation">

                  <a class="nav-link pf-ac active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">My Profile</a>

               </li>

               {{-- <li class="nav-item pf" role="presentation">

                  <a class="nav-link pf-ac" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Edit Profile</a>

               </li> --}}

               <li class="nav-item pf" role="presentation">

                  <a class="nav-link pf-ac" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Change Password</a>

               </li>

            </ul>

            <div class="tab-content" id="pills-tabContent">

               <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                  <div class="row pf-sec">

                     <div class="col-xxl-3 col-xl-4 col-lg-5 col-md-5">

                        <div class="card text-center shadow-none border profile-cover__img">

                           <div class="card-body" style="height: 215px;">

                              <div class="profile-img-1 pf-img" style="margin-top: 20px;">

                                 <div class="circle">
                                    <img class="profile-pic" src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg">

                                 </div>
                                 <div class="p-image">
                                    <i class="fa-regular fa-pen-to-square upload-button"></i>
                                    <input class="file-upload" type="file" accept="image/*" />
                                 </div>
                              </div>
                              <div>
                                 <br>
                                 <br>
                              </div>

                              <!-- <div>

                                 <div class="text-warning mb-0"> <i class="fa fa-star fs-20"></i> <i class="fa fa-star fs-20"></i> <i

                                       class="fa fa-star fs-20"></i> <i class="fa fa-star fs-20"></i> <i class="fa fa-star-half-o fs-20"></i>

                                 </div>

                              </div>

                              <p class="mb-2">(3145 Reviews)</p> -->

                           </div>

                        </div>

                     </div>

                     <div class="col-xxl-9 col-xl-8 col-lg-7 col-md-7">

                        <div class="card">

                           <div class="card-body p-0">

                              <div>

                                 <div>

                                    <div class="border-top"></div>

                                    <div class="border-top"></div>

                                    <div class="table-responsive p-3">

                                       <h5 class="mb-0"> Profile Info</h5>

                                       <div class="row">

                                          <div class="col-xl-12">

                                             {{-- <div class="row row-sm"> --}}

                                             {{-- <div class="col-md-4"> <span class="fw-semibold fs-14">Company Name : </span> </div> --}}

                                             {{-- <div class="col-md-8"> <span class="fs-15 same" id="companyname"></span> </div> --}}

                                             {{-- </div> --}}

                                             <div class="row row-sm mt-3">

                                                <div class="col-md-2"> <span class="fw-semibold fs-14">Name : </span> </div>

                                                <div class="col-md-10"> <span class="fs-15 same" id="profile_name"></span> </div>

                                             </div>

                                             <div class="row row-sm mt-3">

                                                <div class="col-md-2"> <span class="fw-semibold fs-14">Email : </span> </div>

                                                <div class="col-md-10"> <span class="fs-15 same" id="email"></span> </div>

                                             </div>

                                             <div class="row row-sm mt-3">

                                                <div class="col-md-2"> <span class="fw-semibold fs-14">Phone : </span> </div>

                                                <div class="col-md-10"> <span class="fs-15 same" id="phone"></span> </div>

                                             </div>

                                             <!--<div class="row row-sm mt-3">-->

                                             <!--  <div class="col-md-4"> <span class="fw-semibold fs-14">Country : </span> </div>-->

                                             <!--  <div class="col-md-8"> <span class="fs-15 same" id="country" ></span> </div>-->

                                             <!--</div>-->

                                             {{-- <div class="row row-sm mt-3"> --}}

                                             {{-- <div class="col-md-4"> <span class="fw-semibold fs-14">Currency Symbol : </span> </div> --}}

                                             {{-- <div class="col-md-8"> <span class="fs-15 same" id="currency"></span> </div> --}}

                                             {{-- </div> --}}

                                          </div>

                                       </div>

                                    </div>

                                    <div class="border-top"></div>

                                 </div>

                              </div>

                           </div>

                        </div>

                     </div>

                  </div>

               </div>

               {{-- <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                  <div class="edit-pf">

                     <div class="col-md-12">

                        <form id="edit_profileForm" name="profileForm">

                           <div class="row">

                              <div class="mb-3 col-md-6">

                                 <label for="name" class="form-label pf-form">Name</label>

                                 <input type="text" class="form-control" id="edit_profile_name" name="profile_name" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')" maxlength="30">

                                 <span class="text-danger" id="edit_profile_name_span"></span>

                              </div>

                              <div class="mb-3 col-md-6">

                                 <label for="name" class="form-label pf-form">Currency Symbol</label>

                                 <select class="form-control" id="edit_currency" name="currency_symbol">

                                    <option value="$">$ (United States Dollar)</option>

                                    <option value="₹">₹ (Indian Rupee)</option>

                                    <option value="€">€ (Euro)</option>

                                    <option value="£">£ (British Pound Sterling)</option>

                                    <option value="¥">¥ (Japanese Yen)</option>

                                    <option value="C$">C$ (Canadian Dollar)</option>

                                    >

                                 </select>

                                 <span class="text-danger" id="edit_currency_span"></span>

                              </div>

                              <div class="mb-3 col-md-6">

                                 <label for="name" class="form-label pf-form">Company Name</label>

                                 <input type="text" class="form-control" id="edit_companyname" name="company_name" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '')" maxlength="30">

                                 <span class="text-danger" id="edit_companyname_span"></span>

                              </div>

                              <div class="mb-3 col-md-6">

                                 <label for="name" class="form-label pf-form">Phone</label>

                                 <input type="text" class="form-control" id="edit_phone" name="phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15">

                                 <span class="text-danger" id="edit_phone_span"></span>

                              </div>

                              <div class="mb-3 col-md-6">

                                 <label for="formFile" class="form-label pf-form">Choose Company

                                    Logo</label>

                                 <input class="form-control" type="file" id="formFile" name="company_logo">

                                 <img id="profileimage1" alt="img" style="width: 200px; height: 200px;">

                              </div>

                              <div class="col-12 text-center">

                                 <button type="button" class="btn btn-primary" id="edit_profile"><i class="fa fa-save"></i> Update</button>

                              </div>

                           </div>

                        </form>

                     </div>

                  </div>

               </div> --}}

               <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

                  <div class="edit-pf">

                     <div class="col-md-12">

                        <form id="password_profileForm" name="password_profileForm">

                           <div class="row">

                              <div class="mb-3 col-md-6">

                                 <label for="name" class="form-label pf-form">User Email Id</label>

                                 <input type="text" class="form-control " id="edit_email" name="email" style="cursor: no-drop;" readonly>

                              </div>

                              <div class="mb-3 col-md-6" style="display:none;" id="otpshow">

                                 <label for="name" class="form-label pf-form">OTP <span style="color: red;">*</span></label>

                                 <input type="text" class="form-control" id="otp" name="otp" pattern="^[0-9]{4}$" maxlength="4" required oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                              </div>

                              <div class="mb-3 col-md-6" style="display:none;" id="passwordshow">

                                 <label for="name" class="form-label pf-form">Password <span style="color: red;">*</span></label>

                                 <!-- <input type="text" class="form-control" id="password" name="password"> -->
                                 <!-- <input type="text" class="form-control" id="password" name="password" pattern="^[A-Za-z0-9]{8}$" maxlength="8" required oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')"> -->
                                 <input type="text" class="form-control" id="password" name="password"
                                    minlength="8" maxlength="20"
                                    pattern="^[A-Za-z0-9@#$%&*()%?]+$"
                                    oninput="this.value = this.value.replace(/[\s ]/g, '')" required>

                              </div>

                              <div class="mb-3 col-md-6" style="display:none;" id="conformpasswordshow">

                                 <label for="name" class="form-label pf-form">Confirm Password <span style="color: red;">*</span></label>

                                 <!-- <input type="text" class="form-control" id="con_password" name="con_password"> -->
                                 <!-- <input type="text" class="form-control" id="con_password" name="con_password" pattern="^[A-Za-z0-9]{8}$" maxlength="8" required oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')"> -->
                                 <input type="text" class="form-control" id="con_password" name="con_password"
                                    minlength="8" maxlength="20"
                                    pattern="^[A-Za-z0-9@#$%&*()%?]+$"
                                    oninput="this.value = this.value.replace(/[\s ]/g, '')" required>

                              </div>

                              <div class="col-12 text-start">
                                 <button type="button" class="btn btn-primary" id="password_change_butn" onclick="disableButton()">Send OTP</button>
                              </div>

                              <!--</div> -->

                           </div>

                        </form>

                     </div>

                  </div>

               </div>

            </div>
 
         </div>

      </div>

   </div>

</div>

<script>
   function disableButton() {
      let button = document.getElementById("password_change_butn");
      button.disabled = true;
   }

   
</script>

@endsection

@section('custom_scripts')

@include('profile.profile_js')

@endsection