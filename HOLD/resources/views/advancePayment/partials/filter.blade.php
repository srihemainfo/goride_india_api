<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Advance Payment Filter</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12 row mb-2">
                <div class="col-sm-3">
                    <label for="driver_name_filter">Driver Name</label>
                    <div class="input-group">
                        <select class="form-control select2" style="width: 100%;" tabindex="-1" id="driver_name_filter" name="driver_name_filter"  data-placeholder="Search">
                        </select>
                    </div>
                </div>
                
                <div class="col-sm-3" id="custom_select">
                    <div class="form-group">
                        <label for="custom_filter">Date</label>
                        <!--<input type="text" class="form-control" id="index_custom_filter" placeholder="Select date" name="index_custom_filter" value="">-->
                        <input class="form-control" type="text" id="index_custom_filter" name="index_custom_filter" value="" placeholder="YYYY-MM-DD" autocomplete="off" />
                    </div>
                </div>
            </div>
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-6">
                    <input type="hidden" name="name_filter" id="name_filter" value="">
                    <button type="button" class="btn btn-primary" id="search_filter"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>