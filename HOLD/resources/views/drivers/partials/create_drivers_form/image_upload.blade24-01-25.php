<div class="card-header">
    <h4 class="card-title">Image Upload</h4>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-sm-12 mb-4" style="text-align: center;">
            <img src="{{ optional($driver[0] ?? null)->photo }}" id="uploadPreview" alt="your image" onerror="" style="width: 156px; height: 156px; display: {{ optional($driver[0] ?? null)->photo == null ? 'none': 'block' }}">
        </div>
        <div class="col-sm-12 mb-4">
            <label for="upload_photo">Upload Photo</label>
            <input type="file" class="form-control-file border" id="upload_photo" name="upload_photo"  accept="image/*" onchange="PreviewImage();">
            <input type="hidden" name="show_image_data" id="show_image_data">
            @error('upload_photo')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
