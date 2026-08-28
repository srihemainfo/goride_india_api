<style>
    .modal-content.size{
        position: fixed;
        height: 540px;
        width: 453px;
        bottom: 0;
        right: 0;

    }
    #img_doc {
        border: 2px solid #000;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    @media (max-width: 576px) {
    .modal-content.size{
        height: 509px !important;
        width: 265px !important;
        top: 58px !important;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    input[type="text"],
    input[type="file"],
    input[type="submit"] {
        font-size: 14px;
    }
}


</style>
@php
    use Illuminate\Support\Str;
    $currentUrl = request()->path();
@endphp
<div class="modal fade" id="unique-model-2" data-bs-backdrop="static" aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-end" role="document" style="width: 30%;">
        <div class="modal-content size">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">File upload</h5>
                @if (Str::startsWith(request()->path(), 'driver/edit'))
                    <button type="button" class="btn-close" id="dfdfd"></button>
                @else
                    <button type="button" class="btn-close" id="dfdfd" onclick="window.location.href='/driver/create'"></button>
                @endif
             </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-sm-12 mb-4">

                        <div class="alert" id="message" style="display: none;"></div>

                        <form method="POST" id="upload_d_form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">

                                <div>

                                    <label for="description" class="col-form-label">Document Description <span class="required">&nbsp;*</span></label>

                                    <input type="text" id="description" name="description" class="form-control" oninput="this.value = this.value.replace(/[^a-z0-9-A-Z ]/g, '').slice(0, 20);"/>

                                    <p class="text-danger invalid-description"></p>

                                </div>

                                <div>

                                    <input type="file" class="form-control-file border" name="select_d_file" id="select_d_file" accept=".pdf,.jpeg, .jpg, .png, .doc" />

                                    <p class="text-danger invalid-file"></p>
                                    <p style="color: #002b91">Note:Only PNG, JPG, or JPEG file formats are accepted,File size must not exceed 2MB</p>
                                    <input type="hidden" id="driver_n_id" name="driver_n_id" value=""/>

                                </div>

                            </div>
                            <input type="submit" name="upload" id="file_upload_btn" class=" btn btn-primary" value="Upload">

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div> <!-- modal-bialog .// -->

</div>

<div class="modal fade" id="unique-model-3" data-bs-backdrop="static" aria-labelledby="exampleModalToggleLabel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-end" role="document" style="width: 30%;">
        <div class="modal-content size">
            <div class="modal-header">
                <button type="button" class="btn-close" id="dfdfdsss" onclick="$('#unique-model-3').modal('hide');"></button>
             </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-12 mb-4">
                        <img src="" alt="Alternate Image" width="400" height="500" id="img_doc" style="border: 2px solid #000; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    </div>

                </div>

            </div>
        </div>

    </div> <!-- modal-bialog .// -->

</div>