@extends('dashboard-layout.index')



@section('content')

<div class="col-sm-9 main-card mb-3 card">



  <div class="card-header row container">

    <div class="col-9">

      <h4 class="card-title text-start mb-0  mt-1 p-0">Offer Days List</h4>

    </div>

    <div class="col-3 add">

      <button type="button" class="btn btn-success footable-add float-end" data-bs-toggle="modal" data-bs-target="#add-modal"><i class="me-2 fas fa-plus"></i>Add New</button>

    </div>

  </div>

  <div class="card-body">

    <div class="table-responsive">

      <table id="data-table" class="table" width="100%">

        <thead class="table-light">

          <tr>



            <th>#</th>

            <th>Cost</th>

            <th>Date</th>

            <th>Text</th>

            <th style="width:10%;">Action</th>

          </tr>

        </thead>

        <tbody></tbody>

      </table>

    </div>

  </div>



</div>

<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">

  <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">



    <a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/fleet" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

      <i class="fa-solid fa-car" style="margin-right: 8px;"></i> List Fleets

    </a>



    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/offertimes" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

      <i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Offer Times

    </a>



    <a class="nav-link active text-light" id="vert-tabs-right-offer-days-tab" href="/offerdays" role="tab" aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">

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



<style>
  .nav-tabs .nav-link:hover {

    background-color: #747474 !important;

    color: white !important;

  }

  .nav-link.active {

    background-color: #fff !important;

    color: #343a40 !important;

  }

  @media (max-width: 768px) {
    .modal-dialog-aside {
        width: 100% !important;
        height: 100% !important;
        margin: 0;
        max-width: none;
        left: 12px;
    }

    .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .add{
      max-width: 54%;
    }
    .add  button{
      margin-top: 10px;
      width: 111px;
    }

}
.modal-dialog-aside{
  left: 12px !important;
}

  .nav-link:hover {

    background-color: #6c757d !important;

  }
</style>

<!--Modal Add New-->



<div id="add-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title">Add New</h5>

        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form id="VehicleForm">

          <div class="row">

            <div class="col-sm-12">

              <div class="form-group">

                <label for="cost" class="col-form-label">
                  Cost in % <span class="required">&nbsp;*</span>
                  <i class="fas fa-info-circle text-primary"
                    data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Amount is increased for booking."
                    style="cursor: pointer;"></i>
                </label>

                <input type="number" class="form-control" name="cost" id="cost" placeholder="Enter the Cost" min="0" max="99999999" oninput="if(this.value.length > 8) this.value = this.value.slice(0, 8);">

                <p class="text-danger invalid-cost"></p>

              </div>

            </div>

            <div class="col-sm-12">

              <div class="form-group">

                <label for="dates" class="col-form-label">Date<span class="required">&nbsp;*</span></label>

                <input type="date" class="form-control" name="dates" id="dates" placeholder="Enter the Date">

                <p class="text-danger invalid-dates"></p>

              </div>

            </div>

            <div class="col-sm-12">

              <div class="form-group">

                <label for="content" class="col-form-label">Text<span class="required">&nbsp;*</span></label>

                <textarea rows="4" cols="50" name="content" class="form-control" id="content"></textarea>

                <p class="text-danger invalid-content"></p>

              </div>

            </div>

          </div>









      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

      </div>

      </form>

    </div>

  </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->



</div>

<!--Modal Edit New-->



<div id="editor-modal" class="modal fixed-left fade" tabindex="-1" aria-labelledby="editorModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

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

                <label for="editcost" class="col-form-label">Cost<span class="required">&nbsp;*</span></label>

                <input type="number" class="form-control" name="editcost" id="editcost" placeholder="Enter the Cost">

                <p class="text-danger invalid-cost"></p>

              </div>

            </div>

            <div class="col-sm-12">

              <div class="form-group">

                <label for="editdates" class="col-form-label">Date<span class="required">&nbsp;*</span></label>

                <input type="date" class="form-control" name="editdates" id="editdates" placeholder="Enter the Date">

                <p class="text-danger invalid-dates"></p>

              </div>

            </div>

            <div class="col-sm-12">

              <div class="form-group">

                <label for="editcontent" class="col-form-label">Text<span class="required">&nbsp;*</span></label>

                <textarea rows="4" cols="50" name="editcontent" class="form-control" id="editcontent"></textarea>

                <p class="text-danger invalid-content"></p>

                <input type="hidden" id="editVehicleId" name="id">

              </div>

            </div>

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-primary" id="UpdateprimaryBtn"><i class="fa fa-save"></i>&nbsp; Update</button>

          </div>

        </form>

      </div>

    </div>

  </div>

</div>







@endsection



@section('custom_scripts')

@include('offerdays.partials.offerdays_js')

@endsection