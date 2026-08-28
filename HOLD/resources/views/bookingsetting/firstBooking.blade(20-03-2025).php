@extends('dashboard-layout.index')

@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <style>
.right_col{
    background-color: white;
    padding: 30px;
    border-radius: 10px;
}
  @media (max-width: 767px){

    .wizard .nav-tabs{

        left: 20px;

    }
    .first{
        right: 88% !important;
    }
    .second{
        left: 57% !important;
    }

    .second-round{

        right: 152px;

    position: absolute;

    }

    span.round-tab{

        left: 54px !important;
        
        
    }
    .x_title h2{
        font-size: 23px !important;
        margin-top: -65px;
    }

    }

    .book-id{

        position: absolute;

    top: -17px;

    font-style: normal;

    font-weight: 400;

    white-space: nowrap;

    transform: translate(78%, -50%);

    font-size: 12px;

    font-weight: 700;

    color: #000;

    right: 89%;

    }

    .connecting-line{

            height: 2px;

    background: #e0e0e0;

    position: absolute;

    width: 48%;

    margin: 0 auto;

    left: 71px;

    right: 115px;

    top: 15px;

    z-index: 1;

    }

      

  }

  .modal-body{

      padding:1rem 0;

  }

  .nav-tabs{

      border:none;

  }

        .dropdown-list {

            max-height: 200px;

            overflow-y: auto;

            border: 1px solid #ccc;

            display: none;

            position: absolute;

            background-color: white;

            width: 93%;

            cursor:pointer;

            z-index: 1000;

        }

        .dropdown-list.active {

            display: block;

        }

        .dropdown-item {

            padding: 8px;

            cursor: pointer;

        }

        .dropdown-item:hover {

            background-color: #f0f0f0;

        }

        [id="dropdown"] {

            width: 94% !important;

        }

        .form-select{

          

            color:#000;

        }

        .arrow-none{

              background-image:none !important;

        }

        .form-control{

            color:#000;

        }

      .TOOL{  width: 25px;

    height: 20px;

    border-radius: 50%;

    vertical-align: baseline;

    text-align: center;

    display: inline-flex;

    align-items: center;

    justify-content: center;

}

    </style>

<!-- @include('customers.partials.filter') -->

<div class="col-sm-9 mx-4">

    <div class="modal-overlay"></div>

    <!--<div class="container mt-5">-->

    <!--  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">-->

    <!--    Open Modal-->

    <!--  </button>-->

    <!--</div>-->

  



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

                            <div class="row d-flex align-items-center justify-content-center">

                            <ul class="nav nav-tabs" role="tablist">

                                <div class="col-md-3"></div>

                                 <div class="col-md-3">

                                <li role="presentation" class="{{ request()->routeIs('firstBookingSetting') ? 'active' : 'disabled'}}">

                                    <a href="bookingSetting" ><span class="round-tab"><i class="fa-solid fa-check"></i> </span> <i class="book-id first">Booking Settings</i></a>

                                </li>

                                </div>

                                 <div class="col-md-3">

                                <li role="presentation" class="{{ request()->routeIs('first-fleet') ? 'active' : 'disabled'}}">

                                    <a href="create-fleet" ><span class="round-tab" style="position: relative; left: 17% !important;"><i class="fa-solid fa-check"></i></span> <i class="book-id second">Fleet Creation</i></a>

                                </li>

                                </div>

                                <div class="col-md-3"></div>

                                <!--<li role="presentation">-->

                                <!--    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Complete</i></a>-->

                                <!--</li>-->

                            </ul>

                        </div>

                        </div>

        

                        <!--<form role="form" action="index.html" class="login-box">-->

                            <div class="tab-content" id="main_form">

                                <div class="tab-pane active" role="tabpanel" id="step1">

                                   <div class="right_col" role="main">

                    <div class="x_panel">

                        <div class="x_title">

                            <h2 class="text-center">Booking Settings</h2>

                        </div>

                        <div class="x_content">

                            <div class="x_content-container">

                                <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>

                                    <div class="row mb-3">

                                        <label for="" class="col-form-label col-md-5 col-sm-5">Company Name: <span class="required">*</span></label>

                                        <div class="col-md-7 col-sm-7">

                                            <input class="form-control " type="text" name="company_name" id="company_name" placeholder="Enter Company Name" autocomplete="off" value="Your Company" oninput="this.value = this.value.replace(/[^0-9a-zA-z ]/g, '').slice(0, 20);">

                                            <div id="dropdown" class="dropdown-list form-select arrow-none"></div>

                                        </div>

                                    </div>
                                    
                                    <div class="row mb-3">

                                        <label for="" class="col-form-label col-md-5 col-sm-5">Operating country:</label>

                                        <div class="col-md-7 col-sm-7">

                                            <input class="form-control " type="text" name="country" id="country" placeholder="Enter Country (India)" autocomplete="off">

                                            <div id="dropdown" class="dropdown-list form-select arrow-none"></div>

                                        </div>

                                    </div>

                                    



                                    <div class="row mb-3">

                                            <label for="" class="col-form-label col-md-5 col-sm-5">Timezone</label>

                                            <div class="col-md-7 col-sm-7">

                                                <input class="form-control " type="text" id="timezone" name="timezone" placeholder="Enter TimeZone Africa/Abidjan" autocomplete="off" readonly>

                                                <div id="dropdowntimezone" class="dropdown-list form-select arrow-none"></div>

                                            </div>

                                        </div>



                                        <div class="row mb-3">

                                        <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>

                                        <div class="col-md-7 col-sm-7">

                                            <input class="form-control " type="text" name="currency" id="currency" placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off" readonly>

                                            <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>

                                        </div>

                                    </div>

                                    

                                    <div class="row mb-3">

                                        <label class="col-form-label col-md-5 col-sm-5">Distance Calculated By</label>

                                        <div class="col-md-7 col-sm-7">

                                            <select name="distance_unit" id="distance_unit" class="form-select" required>

                                                <option value="kms">Kms</option>

                                                <option value="miles">Miles</option>

                                            </select>

                                        </div>

                                    </div>

                                    

                        <div class="row">

                            <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum <button type="button" class="TOOL btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Please set sufficient minimum time, before booking for your customer">i</button></label></label>

                            

                                        <div class="col-md-4 col-sm-4 col-6 mb-3">

                                            <select name="advance_booking_minium_type" id="advance_booking_minium_type" class="form-select" required>

                                                <option value="">Select</option>

                                                <option value="minutes">Minutes</option>

                                                <option value="hours">Hours</option>

                                            </select>

                                        </div>

                                        <div class="col-md-3 col-sm-3 col-6 mb-3">

                                             <select name="advance_booking_minium" id="advance_booking_minium" class="form-select" required>

                                            </select>

                                            <!--<input type="text" id="advance_booking_minium" name="advance_booking_minium"  placeholder="Enter Number"class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                        </div>

                        </div>



                                    <div class="row ">

                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum

                                        <button type="button" class="TOOL btn btn-secondary" data-toggle="tooltip" data-placement="top" title="Please set sufficient maximum time, before booking for your customer">i</button></label>

                                        <div class="col-md-4 col-sm-4 col-6 mb-3">

                                            <select name="advance_booking_maximum_type" id="advance_booking_maximum_type" class="form-select" required>

                                                 <option value="">Select</option>

                                                <option value="days">Days</option>

                                                <option value="months">Months</option>

                                                <option value="years">Years</option>

                                            </select>

                                        </div>

                                        <div class="col-md-3 col-sm-3 col-6 mb-3">

                                             <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select" required>

                                            </select>

                                            <!--<input type="text" id="advance_booking_maximum" name="advance_booking_maximum"  placeholder="Enter Number" class="form-control" maxlength="2" data-parsley-maxlength="2" required data-parsley-type="digits">-->

                                            

                                        <input type="hidden" id="bokingsettingid" name="bokingsettingid" >   

                                        </div>

                                    </div>

                                    

                                    <div class="text-center">

                                        <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">Save and Next</button>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

                                    <!--<ul class="list-inline pull-right">-->

                                    <!--    <li><button type="button" class="default-btn btn btn-primary">Save and Next</button></li>-->

                                    <!--</ul>-->

                                </div>

                                <!--<div class="tab-pane" role="tabpanel" id="step2">-->

                                <!--    <h4 class="text-center">Step 2</h4>-->

                                <!--    <div class="row">-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Address 1 *</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                    

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>City / Town *</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Country *</label> -->

                                <!--            <select name="country" class="form-control" id="country">-->

                                <!--                <option value="NG" selected="selected">Nigeria</option>-->

                                <!--                <option value="NU">Niue</option>-->

                                <!--                <option value="NF">Norfolk Island</option>-->

                                <!--                <option value="KP">North Korea</option>-->

                                <!--                <option value="MP">Northern Mariana Islands</option>-->

                                <!--                <option value="NO">Norway</option>-->

                                <!--            </select>-->

                                <!--        </div>-->

                                <!--    </div>-->

                                    

                                    

                                    

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Registration No.</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--   </div>-->

                                    

                                    

                                <!--    <ul class="list-inline pull-right">-->

                                <!--        <li>-->

                                <!--            <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true">Save</a>-->

                                <!--        </li>-->

                                <!--        <li><button type="button" class="default-btn next-step skip-btn">Skip</button></li>-->

                                <!--        <li><button type="button" class="btn btn-primary default-btn">Submit</button></li>-->

                                <!--    </ul>-->

                                <!--</div>-->

                                <!--<div class="tab-pane" role="tabpanel" id="step3">-->

                                <!--    <h4 class="text-center">Step 3</h4>-->

                                <!--     <div class="row">-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Account Name *</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Demo</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Inout</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Information</label> -->

                                <!--            <div class="custom-file">-->

                                <!--              <input type="file" class="custom-file-input" id="customFile">-->

                                <!--              <label class="custom-file-label" for="customFile">Select file</label>-->

                                <!--            </div>-->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Number *</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--    <div class="col-md-6">-->

                                <!--        <div class="form-group">-->

                                <!--            <label>Input Number</label> -->

                                <!--            <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--        </div>-->

                                <!--    </div>-->

                                <!--       </div>-->

                                <!--    <ul class="list-inline pull-right">-->

                                <!--        <li><button type="button" class="default-btn prev-step">Back</button></li>-->

                                <!--        <li><button type="button" class="default-btn next-step skip-btn">Skip</button></li>-->

                                <!--        <li><button type="button" class="default-btn next-step">Continue</button></li>-->

                                <!--    </ul>-->

                                <!--</div>-->

                                <!--<div class="tab-pane" role="tabpanel" id="step4">-->

                                <!--    <h4 class="text-center">Step 4</h4>-->

                                <!--    <div class="all-info-container">-->

                                <!--        <div class="list-content">-->

                                <!--            <a href="#listone" data-toggle="collapse" aria-expanded="false" aria-controls="listone">Collapse 1 <i class="fa fa-chevron-down"></i></a>-->

                                <!--            <div class="collapse" id="listone">-->

                                <!--                <div class="list-box">-->

                                <!--                    <div class="row">-->

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>First and Last Name *</label> -->

                                <!--                                <input class="form-control" type="text"  name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Phone Number *</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                <!--                    </div>-->

                                <!--                </div>-->

                                <!--            </div>-->

                                <!--        </div>-->

                                <!--        <div class="list-content">-->

                                <!--            <a href="#listtwo" data-toggle="collapse" aria-expanded="false" aria-controls="listtwo">Collapse 2 <i class="fa fa-chevron-down"></i></a>-->

                                <!--            <div class="collapse" id="listtwo">-->

                                <!--                <div class="list-box">-->

                                <!--                    <div class="row">-->

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Address 1 *</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>City / Town *</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Country *</label> -->

                                <!--                                <select name="country2" class="form-control" id="country2" disabled="disabled">-->

                                <!--                                    <option value="NG" selected="selected">Nigeria</option>-->

                                <!--                                    <option value="NU">Niue</option>-->

                                <!--                                    <option value="NF">Norfolk Island</option>-->

                                <!--                                    <option value="KP">North Korea</option>-->

                                <!--                                    <option value="MP">Northern Mariana Islands</option>-->

                                <!--                                    <option value="NO">Norway</option>-->

                                <!--                                </select>-->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                                        

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Legal Form</label> -->

                                <!--                                <select name="legalform2" class="form-control" id="legalform2" disabled="disabled">-->

                                <!--                                    <option value="" selected="selected">-Select an Answer-</option>-->

                                <!--                                    <option value="AG">Limited liability company</option>-->

                                <!--                                    <option value="GmbH">Public Company</option>-->

                                <!--                                    <option value="GbR">No minimum capital, unlimited liability of partners, non-busines</option>-->

                                <!--                                </select> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Business Registration No.</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Registered</label> -->

                                <!--                                <select name="vat2" class="form-control" id="vat2" disabled="disabled">-->

                                <!--                                    <option value="" selected="selected">-Select an Answer-</option>-->

                                <!--                                    <option value="yes">Yes</option>-->

                                <!--                                    <option value="no">No</option>-->

                                <!--                                </select> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Seller</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                        <div class="col-md-12">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Company Name *</label> -->

                                <!--                                <input class="form-control" type="password" name="name" placeholder="" disabled="disabled"> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                <!--                    </div>-->

                                <!--                </div>-->

                                <!--            </div>-->

                                <!--        </div>-->

                                <!--        <div class="list-content">-->

                                <!--            <a href="#listthree" data-toggle="collapse" aria-expanded="false" aria-controls="listthree">Collapse 3 <i class="fa fa-chevron-down"></i></a>-->

                                <!--            <div class="collapse" id="listthree">-->

                                <!--                <div class="list-box">-->

                                <!--                    <div class="row">-->

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Name *</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                                        

                                <!--                        <div class="col-md-6">-->

                                <!--                            <div class="form-group">-->

                                <!--                                <label>Number *</label> -->

                                <!--                                <input class="form-control" type="text" name="name" placeholder=""> -->

                                <!--                            </div>-->

                                <!--                        </div>-->

                                                        

                                <!--                    </div>-->

                                <!--                </div>-->

                                <!--            </div>-->

                                <!--        </div>-->

                                <!--    </div>-->

                                    

                                <!--    <ul class="list-inline pull-right">-->

                                <!--        <li><button type="button" class="default-btn prev-step">Back</button></li>-->

                                <!--        <li><button type="button" class="default-btn next-step">Finish</button></li>-->

                                <!--    </ul>-->

                                <!--</div>-->

                                <div class="clearfix"></div>

                            </div>

                            

                        <!--</form>-->

             



                    </div>

                </div>

            </div>

        </div>

    </section>

        </div>

      </div>

    </div>

  </div>

<!--<div class="right_col" role="main">-->

<!--                    <div class="x_panel">-->

<!--                        <div class="x_title">-->

<!--                            <h2>Booking Settings</h2>-->

<!--                        </div>-->

<!--                        <div class="x_content">-->

<!--                            <div class="x_content-container">-->

<!--                                <form id="formSettingsSocialMedia" class="form-horizontal" method="post" data-parsley-validate>-->

<!--                                    <div class="row mb-3">-->

<!--                                        <label for="" class="col-form-label col-md-5 col-sm-5">Operating country:</label>-->

<!--                                        <div class="col-md-7 col-sm-7">-->

<!--                                            <input class="form-control " type="text" name="country" id="country" placeholder="Enter Country (India)" autocomplete="off">-->

<!--                                            <div id="dropdown" class="dropdown-list form-select arrow-none"></div>-->

<!--                                        </div>-->

<!--                                    </div>-->

                                    



<!--                                    <div class="row mb-3">-->

<!--    <label for="" class="col-form-label col-md-5 col-sm-5">Timezone</label>-->

<!--    <div class="col-md-7 col-sm-7">-->

<!--        <input class="form-control " type="text" id="timezone" name="timezone" placeholder="Enter TimeZone Africa/Abidjan" autocomplete="off">-->

<!--        <div id="dropdowntimezone" class="dropdown-list form-select arrow-none"></div>-->

<!--    </div>-->

<!--</div>-->



<!--                                        <div class="row mb-3">-->

<!--                                        <label for="" class="col-form-label col-md-5 col-sm-5">Currency</label>-->

<!--                                        <div class="col-md-7 col-sm-7">-->

<!--                                            <input class="form-control " type="text" name="currency" id="currency" placeholder="Enter Currency Afghan afghani (AFN)" autocomplete="off">-->

<!--                                            <div id="dropdowncurrency" class="dropdown-list form-select arrow-none"></div>-->

<!--                                        </div>-->

<!--                                    </div>-->

                                    

<!--                                    <div class="row mb-3">-->

<!--                                        <label class="col-form-label col-md-5 col-sm-5">Distance unit</label>-->

<!--                                        <div class="col-md-7 col-sm-7">-->

<!--                                            <select name="distance_unit" id="distance_unit" class="form-select" required>-->

<!--                                                <option value="miles">Miles</option>-->

<!--                                                <option value="kms">Kms</option>-->

<!--                                            </select>-->

<!--                                        </div>-->

<!--                                    </div>-->

                                    

<!--                                    <div class="row">-->

<!--                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking minimum</label>-->

<!--                                        <div class="col-md-4 col-sm-4 col-6 mb-3">-->

<!--                                            <select name="advance_booking_minium_type" id="advance_booking_minium_type" class="form-select" required>-->

<!--                                                <option value="minutes">Minutes</option>-->

<!--                                                <option value="hours">Hours</option>-->

<!--                                            </select>-->

<!--                                        </div>-->

<!--                                        <div class="col-md-3 col-sm-3 col-6 mb-3">-->

<!--                                             <select name="advance_booking_minium" id="advance_booking_minium" class="form-select" required>-->

<!--                                            </select>-->

                                            

<!--                                        </div>-->

<!--                                    </div>-->

<!--                                    <div class="row ">-->

<!--                                        <label class="col-form-label col-md-5 col-sm-5">Advance booking maximum</label>-->

<!--                                        <div class="col-md-4 col-sm-4 col-6 mb-3">-->

<!--                                            <select name="advance_booking_maximum_type" id="advance_booking_maximum_type" class="form-select" required>-->

<!--                                                <option value="days">Days</option>-->

<!--                                                <option value="months">Months</option>-->

<!--                                                <option value="years">Years</option>-->

<!--                                            </select>-->

<!--                                        </div>-->

<!--                                        <div class="col-md-3 col-sm-3 col-6 mb-3">-->

<!--                                             <select name="advance_booking_maximum" id="advance_booking_maximum" class="form-select" required>-->

<!--                                            </select>-->

                                           

                                            

<!--                                        <input type="hidden" id="bokingsettingid" name="bokingsettingid" >   -->

<!--                                        </div>-->

<!--                                    </div>-->

                                    

<!--                                    <div class="text-center">-->

<!--                                        <button type="button" name="sbtUpdate" class="btn btn-primary" id="saveBtn">UPDATE</button>-->

<!--                                    </div>-->

<!--                                </form>-->

<!--                            </div>-->

<!--                        </div>-->

<!--                    </div>-->

<!--                </div>-->





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

    }



.connecting-line {

   height: 2px;

    background: #e0e0e0;

    position: absolute;

    width: 40%;

    margin: 0 auto;

    left: 72px;

    right: 90px;

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

    left: 37px;

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
    left: 17% !important;

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

      top: -17px;

    font-style: normal;

    font-weight: 400;

    white-space: nowrap;

    /* left: 83%; */

    transform: translate(78%, -50%);

    font-size: 12px;

    font-weight: 700;

    color: #000;

   right: 77%;

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

    margin-top: 30px;

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

</style>

<script>

var currentTab = 0; // Current tab is set to be the first tab (0)

showTab(currentTab); // Display the current tab



function showTab(n) {

  // This function will display the specified tab of the form...

  var x = document.getElementsByClassName("tab");

  x[n].style.display = "block";

  //... and fix the Previous/Next buttons:

  if (n == 0) {

    document.getElementById("prevBtn").style.display = "none";

  } else {

    document.getElementById("prevBtn").style.display = "inline";

  }

  if (n == (x.length - 1)) {

    document.getElementById("nextBtn").innerHTML = "Submit";

  } else {

    document.getElementById("nextBtn").innerHTML = "Next";

  }

  //... and run a function that will display the correct step indicator:

  fixStepIndicator(n)

}



function nextPrev(n) {

  // This function will figure out which tab to display

  var x = document.getElementsByClassName("tab");

  // Exit the function if any field in the current tab is invalid:

  if (n == 1 && !validateForm()) return false;

  // Hide the current tab:

  x[currentTab].style.display = "none";

  // Increase or decrease the current tab by 1:

  currentTab = currentTab + n;

  // if you have reached the end of the form...

  if (currentTab >= x.length) {

    // ... the form gets submitted:

    document.getElementById("regForm").submit();

    return false;

  }

  // Otherwise, display the correct tab:

  showTab(currentTab);

}



function validateForm() {

  // This function deals with validation of the form fields

  var x, y, i, valid = true;

  x = document.getElementsByClassName("tab");

  y = x[currentTab].getElementsByTagName("input");

  // A loop that checks every input field in the current tab:

  for (i = 0; i < y.length; i++) {

    // If a field is empty...

    if (y[i].value == "") {

      // add an "invalid" class to the field:

      y[i].className += " invalid";

      // and set the current valid status to false

      valid = false;

    }

  }

  // If the valid status is true, mark the step as finished and valid:

  if (valid) {

    document.getElementsByClassName("step")[currentTab].className += " finish";

  }

  return valid; // return the valid status

}



function fixStepIndicator(n) {

  // This function removes the "active" class of all steps...

  var i, x = document.getElementsByClassName("step");

  for (i = 0; i < x.length; i++) {

    x[i].className = x[i].className.replace(" active", "");

  }

  //... and adds the "active" class on the current step:

  x[n].className += " active";

}

</script>



@include('bookingsetting.partials.add_customer_modal')

@endsection

@section('custom_scripts')

@include('bookingsetting.partials.customers_js')

@endsection