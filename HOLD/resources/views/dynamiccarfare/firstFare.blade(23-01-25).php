 @extends('dashboard-layout.index')



 @section('content')

 <style>
     .btn-success {

         font-size: 12px !important;

     }

     .start_41 {

         padding: 1px !important;

     }

     .table-responsive {

         overflow-x: scroll;

     }



     .nav-tabs .nav-link:hover {

         background-color: #747474 !important;

         color: white !important;

     }

     .nav-link.active {

         background-color: #fff !important;

         color: #343a40 !important;

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

         z-index: 1040;
         /* Slightly below the modal's z-index */

         display: none;
         /* Initially hidden */

     }



     /* Show the overlay when the modal is open */

     body.modal-open .modal-overlay {

         display: block;

     }

     /*---------signup-step-------------*/

     .bg-color {

         background-color: #333;

     }

     .signup-step-container {

         padding-top: 60px;



     }



     .wizard .nav-tabs {

         position: relative;

         margin-bottom: 0;

         border-bottom-color: transparent;

     }



     .wizard>div.wizard-inner {

         position: relative;

         margin-bottom: 50px;

         text-align: center;

         left: 7%;

     }



     .connecting-line {

         height: 2px;

         background: #e0e0e0;

         position: absolute;

         width: 21%;

         margin: 0 auto;

         left: 82px;

         right: 182px;

         top: 15px;

         z-index: 1;



     }



     .wizard .nav-tabs>li.active>a,
     .wizard .nav-tabs>li.active>a:hover,
     .wizard .nav-tabs>li.active>a:focus {

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

         left: 49px;

         text-align: center;

         font-size: 16px;

         color: #0e214b;

         font-weight: 500;

         border: 1px solid #ddd;

     }

     span.round-tab i {

         color: #555555;

     }

     .wizard li.active span.round-tab i {

         color: #fff !important;

     }

     .wizard li.active span.round-tab {

         background: #2da150;

         color: #fff;

         border-color: #2da150;

     }

     .wizard li.active span.round-tab i {

         color: #5bc0de;

     }

     .wizard .nav-tabs>li.active>a i {

         color: #2b5d81;

     }



     .wizard .nav-tabs>li {

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







     .wizard .nav-tabs>li a {

         width: 30px;

         height: 30px;

         margin: 20px auto;

         border-radius: 100%;

         padding: 0;

         background-color: transparent;

         position: relative;

         top: 0;

     }

     .book-id {

         position: absolute;

         top: -18px;

         font-style: normal;

         font-weight: 400;

         white-space: nowrap;

         left: 34px;

         transform: translate(78%, -50%);

         font-size: 12px;

         font-weight: 700;

         color: #000;

         right: 42%;

     }

     



     .wizard .nav-tabs>li a:hover {

         background: transparent;

     }



     .wizard .tab-pane {

         position: relative;

         padding-top: 0px;

     }



     .x_title {

         margin-bottom: 20px;

     }

     .wizard h3 {

         margin-top: 0;

     }

     .prev-step {

         background-color: #152e41;

         color: #fff;

     }

     .prev-step,

     .next-step {

         font-size: 13px;

         padding: 8px 24px;

         border: none;

         border-radius: 4px;

         margin-top: 30px;

     }

     .next-step {

         background-color: #152e41;

         color: #fff;

     }

     .skip-btn {

         background-color: #cec12d;

     }

     .step-head {

         font-size: 20px;

         text-align: center;

         font-weight: 500;

         margin-bottom: 20px;

     }

     .term-check {

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

     .footer-link {

         margin-top: 30px;

     }

     .all-info-container {}

     .list-content {

         margin-bottom: 10px;

     }

     .list-content a {

         padding: 10px 15px;

         width: 100%;

         display: inline-block;

         background-color: #f5f5f5;

         position: relative;

         color: #565656;

         font-weight: 400;

         border-radius: 4px;

     }

     .list-content a[aria-expanded="true"] i {

         transform: rotate(180deg);

     }

     .list-content a i {

         text-align: right;

         position: absolute;

         top: 15px;

         right: 10px;

         transition: 0.5s;

     }

     .form-control[disabled],
     .form-control[readonly],
     fieldset[disabled] .form-control {

         background-color: #fdfdfd;

     }

     .list-box {

         padding: 10px;

     }

     .signup-logo-header .logo_area {

         width: 200px;

     }

     .signup-logo-header .nav>li {

         padding: 0;

     }

     .signup-logo-header .header-flex {

         display: flex;

         justify-content: center;

         align-items: center;

     }

     .list-inline li {

         display: inline-block;

     }

     .pull-right {

         float: right;

     }
     .start_20{
        width: 32px;
    padding: 7px 2px;
     }
     /*-----------custom-checkbox-----------*/

     /*----------Custom-Checkbox---------*/

     input[type="checkbox"] {

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

     input[type="checkbox"]::before {

         height: 16px;

         width: 16px;

         border: 1px solid #999;

         left: 0px;

         top: 0px;

         background-color: #fff;

         border-radius: 2px;

     }

     input[type="checkbox"]::after {

         height: 5px;

         width: 9px;

         left: 4px;

         top: 4px;

     }

     input[type="checkbox"]:checked::after {

         content: "";

         border-left: 1px solid #fff;

         border-bottom: 1px solid #fff;

         transform: rotate(-45deg);

     }

     input[type="checkbox"]:checked::before {

         background-color: #18ba60;

         border-color: #18ba60;

     }





     @media (max-width: 767px) {

         .sign-content h3 {

             font-size: 40px;

         }

         .wizard .nav-tabs>li a i {

             display: none;

         }
         span.round-tab,.connecting-line{
            display:none;
         }
         .signup-logo-header .navbar-toggle {

             margin: 0;

             margin-top: 8px;

         }

         .signup-logo-header .logo_area {

             margin-top: 0;

         }

         .signup-logo-header .header-flex {

             display: block;

         }

     }



     .dropdown-currency {

         cursor: pointer;

     }

     .sve-btn {

         background: #2a71a1;

         color: white;

         font-size: 15px !important;



     }
 </style>

 <div class="col-sm-9 mx-4 main-card mb-3 card">

     <div class="modal-overlay"></div>

     <!--<div class="container mt-5">-->

     <!--  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">-->

     <!--    Open Modal-->

     <!--  </button>-->

     <!--</div>-->





     <div class="modal fade" id="car-faremodel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">

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

                                                     <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><span class="round-tab"><i class="fa-solid fa-check"></i> </span> <i class="book-id"> Car Fare</i></a>

                                                 </li>

                                                 <li role="presentation">

                                                     <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id"> Add Drivers</i></a>

                                                 </li>

                                                 <!-- <li role="presentation">

                                                     <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" aria-expanded="false"><span class="round-tab"><i class="fa-solid fa-check"></i></span> <i class="book-id">Complete</i></a>

                                                 </li> -->

                                             </ul>

                                         </div>

                                         <div class="card-header">

                                             <h4 class="card-title mr-4">Car Fares List</h4>

                                             <!--<button id='update-all-btn' class='btn btn-success'>Update</button>-->

                                         </div>



                                         <div class="card-body">

                                             <div class="table-responsive">

                                                 <form id='carFareForm'>

                                                     <table id="data-table" class="table" width="100%">

                                                         <thead id='header_container'>

                                                             <!--<tr id="table-header"></tr>-->

                                                         </thead>

                                                         <tbody id="table-body"></tbody>

                                                         <tbody id="table-second-body"></tbody>



                                                     </table>

                                                     <div class="col-12 text-center mb-4">

                                                         <button id='update-all-btn' class='btn sve-btn' style="background: #146dbb; color: #fff;font-size: 16px;font-weight: 600;">Save and Next </button>

                                                     </div>

                                                 </form>

                                             </div>

                                         </div>

                                     </div>

                                 </div>

                             </div>

                         </div>

                 </div>

                 <style>
                     .nav-tabs .nav-link:hover {

                         background-color: #747474 !important;

                         color: white !important;

                     }

                     .nav-link.active {

                         background-color: #fff !important;

                         color: #343a40 !important;

                     }



                     .nav-link:hover {

                         background-color: #6c757d !important;

                     }
                 </style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.getElementById('table-body');

        // Add Row Functionality
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-row')) {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <span>0</span>
                        <input type="hidden" class="form-control" name="start[]" value="0">
                    </td>
                    <td>
                        <span>0</span>
                        <input type="hidden" class="form-control" name="end[]" value="0">
                    </td>
                    <td>
                        <input type="text" class="form-control editable-input" name="mofilet_s_360_tab[]" value="0">
                    </td>
                    <td>
                        <button type="button" class="add-row btn btn-success">+</button>
                        <button type="button" class="remove-row btn btn-danger">x</button>
                    </td>
                `;
                tableBody.appendChild(newRow);
            }
        });

        // Remove Row Functionality
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                row.parentNode.removeChild(row);
            }
        });
    });
</script>
                 @endsection



                 @section('custom_scripts')

                 @include('dynamiccarfare.partials.carfares_js')

                 @endsection