@extends('dashboard-layout.index')



@section('content')

<div class="col-sm-9 mx-4 main-card mb-3 card">

   

    

    <div class="card-header row container">

         <div class="col-9">

       <h4 class="card-title mt-0 p-0">Email Templates List</h4>

       </div>

       <div class="col-3">

      <button type="button" class="btn btn-success footable-add float-end " data-bs-toggle="modal" data-bs-target="#add-modal"><i class="fas fa-plus me-2"></i>Add New</button> 

   </div>

   

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="data-table" class="table" width="100%">

                <thead class="table-light">

                    <tr>

                          <th>#</th>

                        <!--<th>ID</th>-->

                        <th>Template Name</th>

                        <!--<th>Description</th>-->

                        <th style="width:10%;">Action</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>

    

    <!--Modal Add New-->



<div id="add-modal" class="modal fade fixed-left" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 43%;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Add New</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <form id="fleetForm">

                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="cost" class="col-form-label">Template Name<span class="required">&nbsp;*</span></label>

                                <input type="text" class="form-control" name="template_name" id="template_name" placeholder="Enter Template Name">

                                <p class="text-danger invalid-cost"></p>

                            </div>

                        </div>

                        <!--<div class="col-sm-12">-->

                        <!--    <div class="form-group">-->

                        <!--        <label for="from" class="col-form-label">Email<span class="required">&nbsp;*</span></label>-->

                        <!--        <input type="text" class="form-control" name="email" id="email"  placeholder="Enter from time">-->

                        <!--        <p class="text-danger invalid-from"></p>-->

                        <!--    </div>-->

                        <!--</div>-->

                    </div>



                    <div class="row">

                        <div class="col-sm-12">

                        <div class="form-group">

                            <label for="to" class="col-form-label">Description<span class="required">&nbsp;*</span></label>

                            <textarea class="form-control summernote" name="description" id="description" placeholder="Enter description"></textarea>

                            <p class="text-danger invalid-to"></p>

                        </div>

                    </div>



                       

                    </div>



                    <input type="hidden" id="editVehicleId" name="id">

                </form>

            </div>

            <div class="modal-footer">

          <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

        </div>

        </div>

    </div>

</div>













<!--Modal Edit New-->



<!--Modal Edit New-->



<div id="editor-modal" class="modal fixed-left fade" tabindex="-1" aria-labelledby="editorModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 45%;">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title" id="editorModalLabel">Edit</h5>

        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

      </div>

      <div class="modal-body">

                <form id="EditVehicleForm">

                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="cost" class="col-form-label">Template Name<span class="required">&nbsp;*</span></label>

                                <input type="text" class="form-control" name="edittemplate_name" id="edittemplate_name" placeholder="0.00">

                                <p class="text-danger invalid-cost"></p>

                            </div>

                        </div>

                        <!--<div class="col-sm-12">-->

                        <!--    <div class="form-group">-->

                        <!--        <label for="from" class="col-form-label">Email<span class="required">&nbsp;*</span></label>-->

                        <!--        <input type="text" class="form-control" name="editemail" id="editemail" min="0" max="24" placeholder="Enter from time">-->

                        <!--        <p class="text-danger invalid-from"></p>-->

                        <!--    </div>-->

                        <!--</div>-->

                    </div>



                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <label for="to" class="col-form-label">Description<span class="required">&nbsp;*</span></label>

                                <input type="text" class="form-control" name="editdiscription" id="editdiscription" min="0" max="24" placeholder="Enter to time">

                                <p class="text-danger invalid-to"></p>

                            </div>

                        </div>

                       

                    </div>



                    <input type="hidden" id="editVehicleId" name="id">

                     <div class="modal-footer">

            <button type="button" class="btn btn-primary" id="UpdateprimaryBtn"><i class="fa fa-save"></i>&nbsp; Update</button>

          </div>

                </form>

            </div>

    </div>

  </div>

</div>

</div>

 <div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->

    <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->

    <!--</a>-->

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/bookingsetting" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-ticket-alt" style="margin-right: 8px;"></i>Booking

    </a>

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/emailsetting" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-envelope" style="margin-right: 8px;"></i> Email

    </a>

    

    <a class="nav-link active text-light" id="vert-tabs-right-promo-code-tab" href="/EmailTemplate" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

    <i class="fas fa-plus"style="margin-right: 8px;"></i> Email Template

    </a>

    

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options

    </a>

    <!-- <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date 

    </a> -->

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar

    </a>

    <a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">

      <i class="fas fa-star" style="margin-right: 8px;"></i> Review

    </a>

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

    @include('emailTemplate.partials.add_offertimes_modal')

@endsection



@section('custom_scripts')

    @include('paymentsetting.partials.customers_js')

@endsection