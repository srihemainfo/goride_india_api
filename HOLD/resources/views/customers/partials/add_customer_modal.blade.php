<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%; left: 12px !important;">

      <div class="modal-content">

        <div class="modal-header">

          <h5 class="modal-title">Customer Form</h5>

          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

            <span aria-hidden="true">&times;</span>

          </button>

        </div>

        <div class="modal-body">

            <form id="customerForm" name="customerForm">

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="first_name" class="col-form-label">Full Name<span class="required">&nbsp;*</span></label>

                            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Customer Name" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '').slice(0, 30);">

                            <p class="text-danger invalid-first-name"></p>

                        </div>

                    </div>

                </div>

                

                <!-- <div class="row">-->

                <!--    <div class="col-sm-12">-->

                <!--        <div class="form-group">-->

                <!--            <label for="first_name" class="col-form-label">Company Name<span class="required">&nbsp;*</span></label>-->

                <!--            <input type="text" class="form-control" name="cmpny_name" id="cmpny_name" placeholder="Enter First Name">-->

                <!--            <p class="text-danger invalid-first-name"></p>-->

                <!--        </div>-->

                <!--    </div>-->

                <!--</div>-->



                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="email" class="col-form-label">Email<span class="required">&nbsp;*</span></label>

                            <input type="text" class="form-control" name="email" id="email" placeholder="Enter Email" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@,._ ]/g, '').slice(0, 50);">

                            <p class="text-danger invalid-email"></p>

                        </div>

                    </div>

                </div>

                <div class="form-group">
                    <label for="phone" class="col-form-label">Phone No.<span class="required">&nbsp;*</span></label>
                    
                    <div class="input-group">
                        <!-- <input type="text" id="country_code" name="country_code" value="+91" disabled 
                            style="width: 50px; text-align: center; padding: 6px;" /> -->
                            <span class="input-group-text" id="country_code">+{{$myDial}}</span>

                            
                            <input type="text" class="form-control" 
                            placeholder="Enter your number" 
                            name="phone" id="phone" required 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                        </div>
                        
                        <p class="text-danger invalid-phone-no"></p>
                    </div>
                    
                    <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+{{$myDial}}" />


                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="address1" class="col-form-label">Address<span class="required">&nbsp;*</span></label>

                            <textarea class="form-control" name="address1" placeholder="Enter your address" id="address1" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@,/. ]/g, '').slice(0, 200);"></textarea>

                            <p class="text-danger"></p>

                        </div>

                    </div>

                </div>

                <div class="row">

                    <div class="col-sm-12">

                        <div class="form-group">

                            <label for="remarks" class="col-form-label">Remarks</label>

                            <textarea class="form-control" name="remarks" id="remarks" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@,/. ]/g, '').slice(0, 100);"></textarea>

                            <p class="text-danger"></p>

                        </div>

                    </div>

                </div>





                <div class="row">

                    

                </div>



                <input type="hidden" name="customer_id" id="customer_id">

              </form>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-primary" id="saveBtn"><i class="fa fa-save"></i>&nbsp; Save</button>

        </div> 

      </div>

    </div> <!-- modal-bialog .// -->

  </div> <!-- modal.// -->