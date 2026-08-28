<!-- Include the CSS for intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">

<!-- Include the JavaScript for intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<div id="add_cus_form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"> New Website Setting </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div> 
 
            <div class="modal-body"> 

                <form id="formSettingsGeneral" class="form-horizontal" method="post" action="" data-parsley-validate="" enctype="multipart/form-data" novalidate="">

 

                    <div class="mb-3 row">
                        <label class="form-label col-md-5 col-sm-5">Contact number<span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">
                            <div class="input-group">
                                <!-- Country Code Input (Disabled) -->
                                <input type="text" id="country_code" name="country_code" value="+{{$myDial}}" disabled="disabled" style="width: 50px; text-align: center;padding:3px;" />
                        
                                <!-- Contact Number Input -->
                                <input type="tel" id="add_contact_number" name="add_contact_number" oninput="validateContactNumber(this)" maxlength="15" class="form-control" required="required"
                                    data-parsley-maxlength="15"
                                    data-parsley-pattern="/^([+]|(00))[ ()0-9]{9,18}$/"
                                    data-parsley-error-message="Enter valid contact number with country code, e.g., +91 7358543391" placeholder="Enter your phone number" />
                            </div>
                            <span id="contact_error" style="color: red;"></span> <!-- Error message -->

                        </div>
                    </div>




                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">URL<span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <div class="input-group">

                                <input type="text" class="form-control" name="domain_Prefix" required="required" id="domain_Prefix" oninput="this.value = this.value.replace(/[^a-z0-9-]/g, '');" aria-label="Domain prefix" maxlength="20">

                                <span class="input-group-text" id="domain_dynamic"></span>

                            </div>

                            <span id="url_error" style="color: red;"></span> <!-- Error message -->


                        </div>

                    </div>


 
                    <!-- <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Whatsapp Number<span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <div class="input-group"> -->

                                <!-- Country Code Input (Disabled) -->
                                <!-- <input type="text" id="country_code_whatsapp" name="country_code_whatsapp" value="+91" disabled="disabled" style="width: 50px; text-align: center; padding: 3px;" />

                                <input type="hidden" id="hidden_phoneCode" name="hidden_phoneCode" value="+91" />

                                <input type="tel" id="add_whatsapp_number" name="add_whatsapp_number" class="form-control" maxlength="15" data-parsley-maxlength="15" oninput="validateContactNumber(this)" data-parsley-pattern="/^([+]|(00))[ ()0-9]{9,18}$/" data-parsley-error-message="Enter valid contact number with country code, e.g., +91 7358543391">

                            </div>

                            <span id="whatsapp_error" style="color: red;"></span>

                        </div>

                    </div> -->

                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Email address<span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <input type="email" id="add_email" name="add_email" class="form-control" required="required" maxlength="30" data-parsley-maxlength="30" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 .,@]/g, '');">

                            <span id="email_error" style="color: red;"></span>

                        </div>

                    </div>


                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Website Prefix<span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <input id="website_prefix" name="website_prefix" class="form-control" rows="5" required="required" maxlength="3" data-parsley-maxlength="20"  oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '');">

                            <span id="prefix_error" style="color: red;"></span>

                        </div>

                    </div>

                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Contact address</label>

                        <div class="col-md-7 col-sm-7">

                            <textarea id="add_company_address" name="add_company_address" class="form-control" rows="5" maxlength="150" data-parsley-maxlength="150" oninput="this.value = this.value.replace(/[^a-zA-Z0-9 .,]/g, '');"></textarea>

                            <span id="address_error" style="color: red;"></span>

                        </div>

                    </div>

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Google Translate</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <select name="add_google_translate" id="add_google_translate" class="form-select">-->

                    <!--            <option value="No">No</option>-->

                    <!--            <option value="Yes">Yes</option>-->

                    <!--        </select>-->

                    <!--    </div>-->

                    <!--</div>-->

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Currency</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <select name="add_site_currencies" id="add_site_currencies" class="form-select">-->

                    <!--            <option value="INR">INR</option>-->

                    <!--            <option value="GBP">GBP</option>-->

                    <!--            <option value="USD">USD</option>-->

                    <!--            <option value="CAD">CAD</option>-->

                    <!--        </select>-->

                    <!--    </div>-->

                    <!--</div>-->



                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Cookie Consent</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <select name="add_cookieConsent" id="add_cookieConsent" class="form-select">-->

                    <!--            <option value="No">No</option>-->

                    <!--            <option value="Yes" selected="">Yes</option>-->

                    <!--        </select>-->

                    <!--    </div>-->

                    <!--</div>-->



                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Domain name</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <input type="text" id="add_domain_name" name="add_domain_name"  class="form-control" required="required" maxlength="100" data-parsley-maxlength="100">-->

                    <!--    </div>-->

                    <!--</div>-->



                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Company name <span style="color: red;">*</span></label>

                        <div class="col-md-7 col-sm-7">

                            <input type="text" id="add_company_name" name="add_company_name" class="form-control" required="required" maxlength="50" data-parsley-maxlength="50">

                            <span id="company_name_error" style="color: red;"></span>

                        </div>

                    </div>



                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Trading name</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <input type="text" id="add_trading_name" name="add_trading_name"  class="form-control" required="required" maxlength="100" data-parsley-maxlength="100">-->

                    <!--    </div>-->

                    <!--</div>-->



                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Logo</span></label>

                        <div class="col-md-7 col-sm-7">

                            <input type="file" id="add_txtLogo" class="form-control" name="add_txtLogo" accept="image/*" alt="Logo" onchange="if (!window.__cfRLUnblockHandlers) return false; preview_image(event)">

                            <span id="logo_error" style="color: red;"></span>

                        </div>

                    </div>



                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5"></label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <img id="add_imagePreview" src="" class="img-thumbnail" style="max-width: 200px">-->

                    <!--<div class="form-check mt-2">-->

                    <!--    <input class="form-check-input" type="checkbox" name="chkLogoDelete" id="chkLogoDeleteId" data-parsley-multiple="chkLogoDelete" value="Yes">-->

                    <!--    <label class="form-check-label" for="chkLogoDeleteId">Delete Logo</label>-->

                    <!--</div>-->

                    <!--    </div>-->

                    <!--</div>-->



                    <div class="mb-3 row">

                        <label class="form-label col-md-5 col-sm-5">Favicon</label>

                        <div class="col-md-7 col-sm-7">

                            <input type="file" id="add_favicon" class="form-control" name="add_favicon" accept="image/*" alt="favicon" onchange="if (!window.__cfRLUnblockHandlers) return false; preview_image_favicon(event)">

                        </div>

                    </div>

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Country</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <select name="add_site_country" id="add_site_country" class="form-select">-->

                    <!--        </select>-->

                    <!--    </div>-->

                    <!--</div> -->



                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5"></label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <img id="imagePreviewFavicon" src="" class="img-thumbnail" style="max-width: 200px">-->

                    <!--<div class="form-check mt-2">-->

                    <!--    <input class="form-check-input" type="checkbox" name="chkFaviconDelete" id="chkFaviconDeleteId" data-parsley-multiple="chkFaviconDelete" value="Yes">-->

                    <!--    <label class="form-check-label" for="chkFaviconDeleteId">Delete Favicon</label>-->

                    <!--</div>-->

                    <!--    </div>-->

                    <!--</div>-->



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

                    <!--        <input type="text" id="add_licencenumber" name="add_licencenumber"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                    <!--    </div>-->

                    <!--</div>-->

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Licensed by</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <input id="add_lincenceedby" name="add_lincenceedby" class="form-control" rows="5" required="required" maxlength="255" data-parsley-maxlength="255">-->

                    <!--    </div>-->

                    <!--</div>-->

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">License referrer link</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <input type="text" id="add_licencenumber_refrence" name="add_licencenumber_refrence"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                    <!--    </div>-->

                    <!--</div>-->

                    <!--<div class="mb-3 row">-->

                    <!--    <label class="form-label col-md-5 col-sm-5">Google Maps API Key</label>-->

                    <!--    <div class="col-md-7 col-sm-7">-->

                    <!--        <input type="text" id="add_google_api_key" name="add_google_api_key"  class="form-control" maxlength="100" data-parsley-maxlength="100">-->

                    <!--<input type="hidden" id="generalid" name="generalid">-->

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

                    <!--        <button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>-->

                    <!--    </div>-->

                    <!--</div>-->

                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-primary" id="saveBtn" onclick="validateForm()"><i class="fa fa-save"></i>&nbsp; Save</button>

            </div>

        </div>

    </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->