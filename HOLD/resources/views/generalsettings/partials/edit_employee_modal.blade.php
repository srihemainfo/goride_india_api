{{-- keyword start with edit --}}



<div id="edit_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">General Setting Form</h5>

                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <form id="edit_employeeForm" class="form-horizontal" method="post" action="" data-parsley-validate=""
                    enctype="multipart/form-data" novalidate="">





                    <div class="mb-3 row">
                        <label class="form-label col-md-5 col-sm-5">Contact number<span
                                style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">
                            <div class="input-group">
                                <!-- Country Code Input (Disabled) -->
                                <input type="text" id="edit_country_code" name="edit_country_code" value="+{{$myDial}}"
                                    disabled="disabled" style="width: 50px; text-align: center;padding:3px;" />
                                    <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+{{$myDial}}" />

                                <!-- Contact Number Input -->
                                <input type="tel" id="edit_contact_number" name="edit_contact_number"
                                    oninput="validateContactNumber(this)" maxlength="15" class="form-control"
                                    required="required" data-parsley-maxlength="15"
                                    data-parsley-pattern="/^([+]|(00))[ ()0-9]{9,18}$/"
                                    data-parsley-error-message="Enter valid contact number with country code, e.g., +91 7358543391"
                                    placeholder="Enter your phone number" />

                            </div>

                            <span id="edit_contact_error" style="color: red;"></span> <!-- Error message -->

                        </div>

                    </div>
 


                    <div class="mb-3 row">

                        <!-- <label class="form-label col-md-5 col-sm-5">Whatsapp Number<span
                                style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <div class="input-group"> -->
                                <!-- Country Code Input (Disabled) -->
                                <!-- <input type="text" id="edit_country_code_whatsapp" name="edit_code_whatsapp" value="+91"
                                    disabled="disabled" style="width: 50px; text-align: center;padding:3px;" />

                                <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+91" />

                                <input type="tel" id="edit_whatsapp_number" name="edit_whatsapp_number"
                                    class="form-control" maxlength="15" data-parsley-maxlength="15"
                                    oninput="validateContactNumber(this)"
                                    data-parsley-pattern="/^([+]|(00))[ ()0-9]{9,18}$/"
                                    data-parsley-error-message="Enter valid contact number with country code, e.g., +91 7358543391">

                                <span id="edit_whatsapp_error" style="color: red;"></span>

                            </div>

                        </div> -->



                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5 mt-3">Email address<span
                                    style="color: red;">*</span></label>

                            <div class="col-md-7 col-sm-7">

                                <input type="email" id="edit_email" name="edit_email" class="form-control mt-3"
                                    required="required" maxlength="100" data-parsley-maxlength="100">

                                <span id="edit_email_error" style="color: red;"></span>

                            </div>

                        </div>

                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5">Website Prefix<span
                                    style="color: red;">*</span></label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="edit_website_prefix" name="edit_website_prefix"
                                    class="form-control" required="required" maxlength="3" rows="5" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '');"
                                    data-parsley-maxlength="20">

                                <span id="edit_prefix_error" style="color: red;"></span>

                            </div>

                        </div>





                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5">Contact address</label>

                            <div class="col-md-7 col-sm-7">

                                <textarea id="edit_company_address" name="edit_company_address" class="form-control"
                                    rows="5" required="required" maxlength="1000"
                                    data-parsley-maxlength="1000"></textarea>

                                <span id="edit_contact_address_error" style="color: red;"></span>

                            </div>

                        </div>

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Google Translate</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <select name="edit_google_translate" id="edit_google_translate" class="form-select">-->

                        <!--            <option value="No">No</option>-->

                        <!--            <option value="Yes">Yes</option>-->

                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Currency</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <select name="edit_site_currencies" id="edit_site_currencies" class="form-select">-->

                        <!--            <option value="INR">INR</option>-->

                        <!--            <option value="GBP">GBP</option>-->

                        <!--            <option value="USD">USD</option>-->

                        <!--            <option value="CAD">CAD</option>-->

                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Country</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <select name="edit_site_country" id="edit_site_country" class="form-select">-->



                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Cookie Consent</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <select name="edit_cookieConsent" id="edit_cookieConsent" class="form-select">-->

                        <!--            <option value="No">No</option>-->

                        <!--            <option value="Yes" selected="">Yes</option>-->

                        <!--        </select>-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Domain name</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="edit_domain_name" name="edit_domain_name"  class="form-control" required="required" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">Company name<span
                                    style="color: red;">*</span></label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="edit_company_name" name="edit_company_name" class="form-control"
                                    required="required" maxlength="100" data-parsley-maxlength="100">

                                <span id="edit_company_name_error" style="color: red;"></span>

                            </div>

                        </div>


                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5">Theme </label>

                            <div class="col-md-7 col-sm-7">

                                <select class="form-select" id="theme" name="theme" aria-label="Default select example">
                                    <!-- <option selected>Open this select menu</option> -->
                                    <option value="tone">Theme 1</option>
                                    <option value="ttwo">Theme 2</option>
                                    <!-- <option value="tthree">Theme 3</option> -->
                                </select>

                            </div>

                        </div>



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Trading name</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="edit_trading_name" name="edit_trading_name"  class="form-control" required="required" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5">Logo </label>

                            <div class="col-md-7 col-sm-7">

                                <input type="file" id="edit_txtLogo" class="form-control" name="edit_txtLogo"
                                    accept="image/*" alt="Logo"
                                    onchange="if (!window.__cfRLUnblockHandlers) return false; preview_image(event)">

                                <span id="edit_logo_error" style="color: red;"></span>

                            </div>

                        </div>



                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5"></label>

                            <div class="col-md-7 col-sm-7">

                                <img id="edit_imagePreview" name="edit_imagePreview" src="" class="img-thumbnail"
                                    style="max-width: 200px">

                                <!--<div class="form-check mt-2">-->

                                <!--    <input class="form-check-input" type="checkbox" name="chkLogoDelete" id="chkLogoDeleteId" data-parsley-multiple="chkLogoDelete" value="Yes">-->

                                <!--    <label class="form-check-label" for="chkLogoDeleteId">Delete Logo</label>-->

                                <!--</div>-->

                            </div>

                        </div>



                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5">Favicon</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="file" id="edit_favicon" class="form-control" name="favicon"
                                    accept="image/*" alt="favicon"
                                    onchange="if (!window.__cfRLUnblockHandlers) return false; preview_image_favicon(event)">

                            </div>

                        </div>



                        <div class="mb-3 row">

                            <label class="form-label col-md-5 col-sm-5"></label>

                            <div class="col-md-7 col-sm-7">

                                <img id="edit_imagePreviewFavicon" src="" class="img-thumbnail"
                                    style="max-width: 200px">

                                <!--<div class="form-check mt-2">-->

                                <!--    <input class="form-check-input" type="checkbox" name="chkFaviconDelete" id="chkFaviconDeleteId" data-parsley-multiple="chkFaviconDelete" value="Yes">-->

                                <!--    <label class="form-check-label" for="chkFaviconDeleteId">Delete Favicon</label>-->

                                <!--</div>-->

                            </div>

                        </div>



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5" for="bgColorTopFooter">Top Bar & Footer Background Color</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="color" class="form-control form-control-color" id="bgColorTopFooter" name="bgColorTopFooter" value="" title="Choose your color">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5" for="textColorTopFooter">Top Bar & Footer Text Color</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="color" class="form-control form-control-color" id="textColorTopFooter" name="textColorTopFooter" value="" title="Choose your color">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5" for="bgColorMenu">Menu Background Color</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="color" class="form-control form-control-color" id="bgColorMenu" name="bgColorMenu" value="" title="Choose your color">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5" for="textColorMenu">Menu Text Color</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="color" class="form-control form-control-color" id="textColorMenu" name="textColorMenu" value="" title="Choose your color">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Meta Keywords</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="txtMetaKeywords" name="txtMetaKeywords" value="website, cabookie, crm, business management, software" class="form-control" required="required" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">License number</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="edit_licencenumber" name="edit_licencenumber"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Licensed by</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input id="edit_lincenceedby" name="edit_lincenceedby" class="form-control" rows="5" required="required" maxlength="255" data-parsley-maxlength="255">-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">License referrer link</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="edit_licencenumber_refrence" name="edit_licencenumber_refrence"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->

                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Google Maps API Key</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="edit_google_api_key" name="edit_google_api_key"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                        <input type="hidden" id="edit_model_id" name="edit_model_id">

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Facebook Pixel ID</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <input type="text" id="txtFacebookPixelID" name="txtFacebookPixelID" value="123456789012345" class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5">Additional Script</label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <textarea id="txtAdditionalScript" name="txtAdditionalScript" class="form-control" rows="5" maxlength="1000" data-parsley-maxlength="1000"></textarea>-->

                        <!--    </div>-->

                        <!--</div>-->



                        <!--<div class="mb-3 row">-->

                        <!--    <label class="form-label col-md-5 col-sm-5"></label>-->

                        <!--    <div class="col-md-7 col-sm-7">-->

                        <!--        <button type="submit" class="btn btn-primary" id="edit_button_save">Save Changes</button>-->

                        <!--    </div>-->

                        <!--</div>-->




                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">X


                            </label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="social_x" name="social_x" class="form-control"
                                    required="required" maxlength="100" data-parsley-maxlength="100">

                                <span id="edit_company_name_error" style="color: red;"></span>

                            </div>

                        </div>




                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">Facebook


                            </label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="social_fb" name="social_fb" class="form-control"
                                    required="required" maxlength="100" data-parsley-maxlength="100">

                                <span id="edit_company_name_error" style="color: red;"></span>

                            </div>

                        </div>




                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">Instagram</label>

                            <div class="col-md-7 col-sm-7">

                                <input type="text" id="social_insta" name="social_insta" class="form-control"
                                    required="required" maxlength="100" data-parsley-maxlength="100">

                                <span id="edit_company_name_error" style="color: red;"></span>

                            </div>

                        </div>



                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">YouTube</label>
                            <div class="col-md-7 col-sm-7">
                                <input type="text" id="social_yt" name="social_yt" class="form-control"
                                    required="required" maxlength="100" data-parsley-maxlength="100">
                                <span id="edit_company_name_error" style="color: red;"></span>
                            </div>

                        </div>

                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5">Meet & Greet</label>
                            <div class="col-md-7 col-sm-7">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="radio" onclick="yesnoCheck();" name="flexRadioDefault" id="yesCheck">
                                &nbsp;<label class="form-label" for="yesCheck">Yes</label>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input class="form-check-input" onclick="noCheck3();" type="radio" name="flexRadioDefault" id="noCheck" checked>
                                &nbsp;<label class="form-label" for="noCheck">No</label>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="form-label col-md-5 col-sm-5" id="costLabel" style="display: none;">Cost</label>
                            <div class="col-md-7 col-sm-7">
                                <input type="hidden" id="mg_cost" name="mg_cost" class="form-control" required="required" maxlength="5" pattern="^[0-9]{1,5}$" oninput="this.value = this.value.replace(/[^0-9]/g, '')" data-parsley-maxlength="5">
                                <span id="mg_cost_error" style="color: red;"></span>
                            </div>
                        </div>

                </form>

            </div>
 
            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="edit_saveBtn"><i class="fa fa-save"></i>&nbsp;
                    Save</button>

            </div>

        </div>

    </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->

<script>
    window.onload = function () {
    let mgCostInput = document.getElementById('mg_cost');
    let costLabel = document.getElementById('costLabel');
    let errorSpan = document.getElementById('mg_cost_error');

    if (mgCostInput.value !== '') {
        document.getElementById('yesCheck').checked = true;
        mgCostInput.type = 'text';
        costLabel.style.display = 'block';
        mgCostInput.required = true;
        errorSpan.textContent = '';
    } else {
        document.getElementById('noCheck').checked = true;
        mgCostInput.type = 'hidden';
        costLabel.style.display = 'none';
        mgCostInput.required = false;
        errorSpan.textContent = '';
    }
};

function yesnoCheck() {
    let yes1 = document.getElementById('mg_cost');
    let costLabel = document.getElementById('costLabel');
    let errorSpan = document.getElementById('mg_cost_error'); 

    if (document.getElementById('yesCheck').checked) {
        yes1.type = 'text';
        costLabel.style.display = 'block';
        yes1.required = true; 
        errorSpan.textContent = '';
    }
}

function noCheck3() {
    let yes1 = document.getElementById('mg_cost');
    let costLabel = document.getElementById('costLabel');
    let errorSpan = document.getElementById('mg_cost_error');

    if (document.getElementById('noCheck').checked) {
        yes1.required = false;
        errorSpan.textContent = ''; 
        yes1.type = 'hidden';
        costLabel.style.display = 'none';
        yes1.value = '';  
        
    }
}
</script>