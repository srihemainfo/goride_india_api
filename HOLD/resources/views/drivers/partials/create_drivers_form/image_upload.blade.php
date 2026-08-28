<div class="card-header">
    <h4 class="card-title">Driver Image Upload</h4>
</div>
<div class="card-body"> 
    <div class="row">
        
        <div class="col-sm-12 mb-4">
            <label for="upload_photo">Upload Photo</label>
            <input type="file" class="form-control-file border" id="upload_photo" name="upload_photo"  accept=".png,.jpg,jpeg">
           
            <p style="color: #002b91">Note:Only PNG, JPG, or JPEG file formats are accepted,File size must not exceed 2MB</p>
            <input type="hidden" name="show_image_data" id="show_image_data">
            @error('upload_photo')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-sm-12">
            <input type="hidden" class="form-control-file border" id="uploadPreview" name="uploadPreview" value="">

            <div class="ml-3 align-items-start gap-3" id="deleteImageBtn_remove" style="display: none;">
                <img id="uploadPreview123" name="uploadPreview123" style="width: 100px;" src="">
                <button type="button" id="deleteImageBtn" class="btn btn-danger btn-sm">Delete</button>
            </div>
        </div>
        
    </div>
</div>
