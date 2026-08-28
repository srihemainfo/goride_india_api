<style>
    .modal-content.size{
        position: fixed;
        height: 680px;
        width: 450px;
        bottom: 0;
        right: 0;
    }
</style>

<div class="modal fade" id="unique-model-2" data-bs-backdrop="static" aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-end" role="document" style="width: 30%;">
        <div class="modal-content size">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">File upload</h5>
                <button type="button" class="btn-close" id="dfdfd"></button>
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