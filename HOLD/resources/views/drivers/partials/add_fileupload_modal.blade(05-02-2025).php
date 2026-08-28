<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

        <div class="modal-content">

            <div class="modal-header">

            <h5 class="modal-title">File Upload</h5>

            <button type="button" class="close" data-bs-dismiss="modal"  aria-label="Close">

                <span aria-hidden="true">&times;</span>

            </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-sm-12 mb-4">

                        <div class="alert" id="message" style="display: none;"></div>

                        <form method="POST" id="upload_form" enctype="multipart/form-data">

                            @csrf <!-- Add CSRF token for Laravel or other frameworks that require it -->

                            <div class="form-group">

                                <div>

                                    <label for="description" class="col-form-label">Document Description <span class="required">&nbsp;*</span></label>

                                    <input type="text" id="description" name="description" class="form-control"/>

                                    <p class="text-danger invalid-description"></p>

                                </div>

                                <div>

                                    <input type="file" class="form-control-file border" name="select_file" id="select_file" accept=".pdf,.jpeg, .jpg, .png, .doc" />

                                    <p class="text-danger invalid-file"></p>

                                    <input type="hidden" id="driver_id" name="driver_id" value="driver id"/>

                                </div>

                            </div>

                            <!-- Submit Button -->
                            <input type="submit" name="upload" id="file_upload_btn" class=" btn btn-primary" value="Upload">

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->
