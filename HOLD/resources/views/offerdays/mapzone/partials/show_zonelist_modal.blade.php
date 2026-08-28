<div id="show-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%; margin-left: auto;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Show Zone</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="mapzoneshow" name="mapzoneshow">

                    <!-- Zone Name -->
                    <div class="form-group">
                        <label for="showname" class="col-form-label">
                            Zone Name <span class="required text-danger">*</span>
                        </label>
                        <input type="text" class="form-control"
                               id="showname"
                               name="showname"
                               readonly
                               oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                               placeholder="Zone Name">
                        <p class="text-danger invalid-name mb-0"></p>
                    </div>

                    <!-- Zone Map -->
                    <div class="form-group">
                        <label for="showmap" class="col-form-label">Drawn Zone on Map</label>
                        <div id="showmap" style="display:none; height: 300px; width: 100%; border: 1px solid #ccc;"></div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
