<div id="vehichle_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 40%;">

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title">Fleet Form</h5>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </div>

        <div class="modal-body">

            <form id="fleetForm" name="fleetForm" enctype="multipart/form-data">

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="name" class="col-form-label">Fleet Name<span class="required">&nbsp;*</span></label>

                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter First Name" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '').slice(0, 30);">

                            <p class="text-danger invalid-fleet-name"></p>

                        </div>

                    </div>

                </div>

                

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="name" class="col-form-label">Fleet Image<span class="required">&nbsp;*</span></label>

                            <input type="file" class="form-control" name="file" id="file" placeholder="Enter First Name">

                            <p class="text-danger invalid-fleet-file"></p>

                        </div>

                    </div>

                </div>



                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="passenger" class="col-form-label">Passengers<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="passenger" id="passenger" placeholder="Enter passenger count" oninput="this.value = this.value.replace(/[^0-9 ]/g, '').slice(0, 2);">

                            <p class="text-danger invalid-passenger"></p>

                        </div>

                    </div>

                    

                    

                    <!--<div class="col-sm-6">-->

                    <!--    <div class="form-group">-->

                    <!--        <label for="no_of_seats" class="col-form-label">Number of seats<span class="required">&nbsp;*</span></label>-->

                    <!--        <input type="number" class="form-control" name="no_of_seats" id="no_of_seats" placeholder="Enter seats count">-->

                    <!--        <p class="text-danger invalid-no-seats"></p>-->

                    <!--    </div>-->

                    <!--</div>-->

                </div>



                <div class="row">

                   

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="max" class="col-form-label">Max<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="max" id="max" placeholder="Enter max count">

                            <p class="text-danger invalid-max"></p>

                        </div>

                    </div>

                </div>



                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="luggage" class="col-form-label">luggage<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="luggage" id="luggage" placeholder="Enter luggage count">

                            <p class="text-danger invalid-luggage"></p>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="hand_luggage" class="col-form-label">Hand Luggage<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="hand_luggage" id="hand_luggage" placeholder="Enter hand luggage count">

                            <p class="text-danger invalid-hand-luggage"></p>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="child" class="col-form-label">Child Seat<span class="required">&nbsp;*</span></label>

                            <input type="number" class="form-control" name="child" id="child" placeholder="Enter child seats count">

                            <p class="text-danger invalid-child"></p>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            <label for="order" class="col-form-label">Order</label>

                            <input type="number" class="form-control" name="order" id="order" placeholder="Enter order" value="0">

                            <p class="text-danger invalid-order"></p>

                        </div>

                    </div>

                </div>





                <div class="row">

                    

                </div>



                <input type="hidden" name="fleet_id" id="fleet_id">

              </form>

        </div>

       

      </div>

    </div> <!-- modal-bialog .// -->

  </div> <!-- modal.// -->