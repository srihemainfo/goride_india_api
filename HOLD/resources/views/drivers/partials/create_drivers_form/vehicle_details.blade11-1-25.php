
<div class="card-header">
    <h4 class="card-title">Vehicle & Documents Details</h4>
</div>
<div class="card-body">
    <div class="row">
        <!--<div class="col-sm-6">-->
        <!--    <label for="vehicle_make">Vehicle Make</label>-->
        <!--    <select class="form-control select2 select2-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="vehicle_make" name="vehicle_make" data-control="select2" data-placeholder="Select an option" data-hide-search="true">-->
        <!--        <option value="">select</option>-->
        <!--    </select>-->
        <!--    <span class="error" id="vehicle_make_Error" style="color: red;"></span>-->
        <!--</div>-->
        <!--<div class="col-sm-6 mb-4">-->
        <!--    <label for="vehicle_model">Vehicle Model</label>-->
        <!--    <select class="form-control select2 select2-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="vehicle_model" name="vehicle_model" data-control="select2" data-placeholder="Select an option" data-hide-search="true">-->
        <!--        <option value="">select</option>-->
        <!--    </select>-->
        <!--    <span class="error" id="vehicle_model_Error" style="color: red;"></span>-->
        <!--</div>-->
        <div class="col-sm-6 mb-4">
            <label for="vehicle_type">Vehicle Type <span class="required">*</span></label>
            <select class="form-control select2 select2-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true" id="vehicle_type" name="vehicle_type" data-control="select2" data-placeholder="Select an option" data-hide-search="true">
                <option value="">select</option>
            </select>
            <span class="error" id="vehicle_type_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_reg_no">Vehicle Reg No.</label>
            <input type="text" class="form-control" id="vehicle_reg_no" placeholder="Vehicle Reg No." name="vehicle_reg_no" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="vehicle_reg_no_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_color">Vehicle Color</label>
            <input type="text" class="form-control" id="vehicle_color" placeholder="Vehicle Color" name="vehicle_color" value="" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '').slice(0, 15);">
            <span class="error" id="vehicle_color_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="number_of_seats">Number of Seats</label>
            <input type="number" class="form-control" id="number_of_seats" placeholder="Number of Seats" name="number_of_seats" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2);">
            <span class="error" id="number_of_seats_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_insurance">Vehicle Insurance</label>
            <input type="text" class="form-control" id="vehicle_insurance" placeholder="Vehicle Insurance" name="vehicle_insurance" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="vehicle_insurance_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_insurance_expiry">Insurance Expiry on</label>
            <input type="date" class="form-control future-date" id="vehicle_insurance_expiry"  name="vehicle_insurance_expiry" value="">
            <span class="error" id="vehicle_insurance_expiry_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_license">Vehicle Licence</label>
            <input type="text" class="form-control" id="vehicle_license" placeholder="Vehicle Licence" name="vehicle_license" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="vehicle_license_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="vehicle_license_expiry">Licence Expiry on</label>
            <input type="date" class="form-control future-date" id="vehicle_license_expiry"  name="vehicle_license_expiry" value="">
            <span class="error" id="vehicle_license_expiry_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="pco_license_no">PCO License No.</label>
            <input type="text" class="form-control" id="pco_license_no" placeholder="PCO License No." name="pco_license_no" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="pco_license_no_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="pco_license_no_expiry">PCO Expiry on</label>
            <input type="date" class="form-control future-date" id="pco_license_no_expiry"  name="pco_license_no_expiry" value="">
            <span class="error" id="pco_license_no_expiry_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="driver_license_no">Driver Licence No.</label>
            <input type="text" class="form-control" id="driver_license_no" placeholder="Driver Licence No." name="driver_license_no" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="driver_license_no_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="driver_license_no_expiry">Driver Licence Expiry on</label>
            <input type="date" class="form-control future-date" id="driver_license_no_expiry"  name="driver_license_no_expiry" value="">
            <span class="error" id="driver_license_no_expiry_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="mot_no">MOT No.</label>
            <input type="text" class="form-control" id="mot_no" placeholder="MOT No." name="mot_no" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 25);">
            <span class="error" id="mot_no_Error" style="color: red;"></span>
        </div>
        <div class="col-sm-6 mb-4">
            <label for="mot_no_expiry">MOT Expiry on</label>
            <input type="date" class="form-control future-date" id="mot_no_expiry"  name="mot_no_expiry" value="">
            <span class="error" id="mot_no_expiry_Error" style="color: red;"></span>
        </div>
    </div>
</div>
