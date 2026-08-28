@php

// dd(count($driver));

@endphp

<div class="card-header">

    <h4 class="card-title">Personal Details</h4>

</div>

<div class="card-body">

    <div class="row">

        <div class="col-sm-6 mb-4">

            <label for="driver_no">Driver No. <span class="required">*</span></label>

            <input type="text" class="form-control" id="driver_no" placeholder="Driver No." name="driver_no" value="DR{{ session('driver_count') }}"  oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 20);">

            <span class="error" id="DriverNoError" style="color: red;"></span>

            

        </div>

        <div class="col-sm-6 mb-4">

            <label for="name">Name <span class="required">*</span></label>

            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="" oninput="this.value = this.value.replace(/[^a-zA-Z ]/g, '').slice(0, 30);">

            <span class="error" id="firstnameError" style="color: red;"></span>

        </div>

        <div class="col-sm-6 mb-4">

            <label for="phone">Phone No. <span class="required">*</span></label>

            <div class="input-group">
                <span class="input-group-text" id="country_code">+{{$myDial}}</span>
                <input type="text" class="form-control" id="phone" placeholder="Mobile Number" name="phone" value=""  aria-describedby="basic-addon1" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);">
                <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+{{$myDial}}" />
                

            </div>

            <span class="error" id="PhoneNoError" style="color: red;"></span>

        </div>

        <div class="col-sm-6 mb-4">

            <label for="address">Address</label>

            <input type="textarea" class="form-control" id="address" placeholder="Address" name="address" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9,. ]/g, '').slice(0, 200);">

            <span class="error" id="AddressError" style="color: red;"></span>

        </div>

        <div class="col-sm-6 mb-4">

            <label for="email">Email <span class="required">*</span></label>

            <input type="text" class="form-control" id="email" placeholder="email" name="email" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9@.,_]/g, '').slice(0, 30);">

            <span class="error" id="EmailError" style="color: red;"></span>

        </div>

        <!--<div class="col-sm-6 mb-4">-->

        <!--    <label for="password">Password <span class="required">*</span></label>-->

        <!--    <input type="password" class="form-control" id="password" placeholder="Password" name="password">-->

        <!--</div>-->

        <div class="col-sm-6 mb-4">

            <label for="dob">Date of Birth <span class="required">*</span></label>

            <input type="date" class="form-control" id="dob" name="dob" 

                onfocus="this.value = new Date(new Date().setFullYear(new Date().getFullYear() - 18)).toISOString().split('T')[0]">

            <span class="error" id="DateofBirthError" style="color: red;"></span>

        </div>

        <div class="col-sm-6 mb-4 mb-4">

            <label for="national_insurance_no">National Insurance No.</label>

            <input type="text" class="form-control" id="national_insurance_no" placeholder="National Insurance No." name="national_insurance_no" value="" oninput="this.value = this.value.replace(/[^a-zA-Z0-9.,]/g, '').slice(0, 20);">

            <span class="error" id="NationalInsuranceNoError" style="color: red;"></span>

        </div>

    </div>

</div>

