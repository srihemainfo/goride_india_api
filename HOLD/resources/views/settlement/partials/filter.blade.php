<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">settlement Filter</h4>
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
                
                <!--<div class="col-sm-3">-->
                <!--    <label for="week_filter" class="col-form-label">Week</label>-->
                <!--    <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"-->
                <!--        tabindex="-1" aria-hidden="true" id="week_filter" name="week_filter" data-control="select2"-->
                <!--        data-placeholder="Select Week" data-hide-search="true">-->
                        
                <!--        @foreach ($week_array as $week)-->
                <!--            <option value="{{ $week }}">{{ $week }}</option>-->
                <!--        @endforeach-->
                <!--    </select>-->
                <!--</div>-->
                
                <div class="col-sm-3" id="custom_select">
                    <div class="form-group">
                        <label for="custom_filter">Custom Date Range</label>
                        <input type="text" class="form-control" id="index_custom_filter" placeholder="Select date range" name="index_custom_filter" value="">
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