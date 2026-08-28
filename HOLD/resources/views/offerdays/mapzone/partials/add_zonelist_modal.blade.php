<div id="form-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title">Create Zone</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="mapzoneForm" name="mapzoneForm">

                    <!-- Zone 1 Area -->
                    <div class="form-group">
                        <label for="fromarea">Zone 1 Area <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="fromarea" name="fromarea" data-placeholder="Select Area">
                            <option value=""></option>
                        </select>
                        <p class="text-danger invalid_edit_fromarea mb-0"></p>
                    </div>

                    <!-- Zone 1 Name -->
                    <div class="form-group">
                        <label for="name">Zone 1 Name <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control"
                                   id="name" name="name"
                                   maxlength="30"
                                   placeholder="Enter Zone 1 Name"
                                   oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                            <button type="button" class="btn btn-success btn-sm ml-2" id="addZoneBtn">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <p class="text-danger invalid-name mb-0"></p>
                    </div>

                    <!-- Zone 1 Map -->
                    <div class="form-group">
                        <label for="map1">Draw Zone 1 on Map</label>
                        <div id="map1" style="display: none; height: 300px; width: 100%; border: 1px solid #ccc;"></div>
                    </div>

                    <!-- Zone 2 Section -->
                    <div id="zone2Section" class="mt-4" style="display: none;">

                        <!-- Zone 2 Area -->
                        <div class="form-group position-relative">
                            <label for="zone1fromarea">Zone 2 Area <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="zone1fromarea" name="zone1fromarea" data-placeholder="Select Area">
                                <option value=""></option>
                            </select>
                            <p class="text-danger invalid_edit_zone1fromarea mb-0"></p>
                        </div>

                        <!-- Zone 2 Name -->
                        <div class="form-group">
                            <label for="name1">Zone 2 Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control"
                                   id="name1" name="name1"
                                   maxlength="30"
                                   placeholder="Enter Zone 2 Name"
                                   oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                            <p class="text-danger invalid-name mb-0"></p>
                        </div>

                        <!-- Zone 2 Map -->
                        <div class="form-group">
                            <label for="map2">Draw Zone 2 on Map</label>
                            <div id="map2" style="display: none; height: 300px; width: 100%; border: 1px solid #ccc;"></div>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="mapzonesubmit">
                    <i class="fa fa-save"></i>&nbsp; Save
                </button>
            </div>

        </div>
    </div>
</div>
