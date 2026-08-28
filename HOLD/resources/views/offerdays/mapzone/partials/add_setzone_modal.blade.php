<div id="map-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 50%;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title">Map Zone Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form id="setzoneForm" name="setzoneForm">
                    <div class="form-group">
                        <label for="zone1" class="col-form-label">Zone 1 <span class="required text-danger">*</span></label>
                        <select class="form-control" id="zone1" name="zone1">
                            <option value="">-- Select Zone 1 --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="zone2" class="col-form-label">Zone 2 <span class="required text-danger">*</span></label>
                        <select class="form-control" id="zone2" name="zone2">
                            <option value="">-- Select Zone 2 --</option>
                        </select>
                    </div>
                  
                   <div class="form-group">
                        <label for="price" class="col-form-label">Set Price <span class="required text-danger">*</span></label>
                        <input type="text" class="form-control"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            name="price"
                            maxlength="5"
                            id="price"
                            placeholder="Enter Price">
                        <p class="text-danger invalid-name"></p>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="setmapsubmit">
                    <i class="fa fa-save"></i>&nbsp; Save
                </button>
            </div>

        </div>
    </div>
</div>
