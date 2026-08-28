@extends('dashboard-layout.index')

@section('content')
    <div class="col-sm-4 main-card card mx-auto ">
        <div class="card-header">
            <h4 class="card-title">Generate Admin Settled Job's Report</h4>
        </div>
        <div class="card-body">
           
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="report_type" class="col-form-label">Report Type</label>
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="report_type"
                        name="report_type">
                        <option>-- Select Report Type --</option>
                        <option value="Daily">Daily</option>
                        <option value="Weekly">Weekly</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Custom">Custom Range</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12" id="job_select" style="display: none;">
                <div class="form-group">
                    <label for="job_filter" class="col-form-label">Job Type</label>
                    <select class="form-control" style="width: 100%;" tabindex="-1" aria-hidden="true" id="job_type"
                        name="report_type">
                        <!--<option value="settled">-- Select Job Type --</option>-->
                        <!--<option value="all">All Jobs</option>-->
                        <option value="settled">Settled Jobs</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12" id="month_select" style="display: none;">
                <div class="form-group">
                    <label for="month_filter" class="col-form-label">Month</label>
                    <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                        tabindex="-1" aria-hidden="true" id="month_filter" name="month_filter" data-control="select2"
                        data-placeholder="Select Month" data-hide-search="true">
                        <option value="">-- select month --</option>
                        @foreach ($month_array as $month)
                            <option value="{{ $month }}">{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12" id="week_select" style="display: none;">
                <div class="form-group">
                    <label for="week_filter" class="col-form-label">Week</label>
                    <select class="form-control" class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                        tabindex="-1" aria-hidden="true" id="week_filter" name="week_filter" data-control="select2"
                        data-placeholder="Select Week" data-hide-search="true">
                        <option value="">-- select week --</option>
                        @foreach ($week_array as $week)
                            <option value="{{ $week }}">{{ $week }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-12" id="day_select" style="display: none;">
                <div class="form-group">
                    <label for="date_filter" class="col-form-label">Date</label>
                    <div class="input-group">
                        <input class="form-control" type="text" id="date_filter" name="date_filter">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="(function(){$('#date_filter').datepicker('show')})()">
                            <i class="fa fa-calendar"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12" id="custom_select" style="display: none;">
                <div class="form-group">
                    <label for="custom_filter">Custom Date Range</label>
                    <input type="text" class="form-control" id="custom_filter" placeholder="Select date range" name="custom_filter" value="">
                </div>
            </div>
             

            <div class="col-sm-12 mx-auto" id="generate-btn" style="display: none;">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <a href="" target="_blank" id="generate-link" class="btn btn-danger">
                            Generate Report (PDF)</a>
                    </div>
                    <div class="col-sm-6">
                        <a href="" target="_blank" id="generate-excel" class="btn btn-success">
                            Generate Report (Excel)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="col-sm-2 main-card mb-3 card  d-none d-lg-block position" >
  <div class="nav flex-column nav-tabs nav-tabs-right " id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">
    
    <a class="nav-link  active text-light" id="vert-tabs-right-home-tab" href="/admin-report" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">
      <i class="fas fa-file-alt" style="margin-right: 8px;"></i> Admin Report
    </a>
    
    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/driver-report" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
     <i class="fas fa-car" style="margin-right: 8px;"></i>Driver Report
    </a>
    

    
  </div>
</div>
<style>
.nav-tabs .nav-link:hover  {
    background-color: #747474 !important;
    color: white !important; 
}
.nav-link.active {
  background-color: #fff !important;
  color:#343a40 !important;
}

.nav-link:hover {
  background-color: #6c757d !important; 
}
</style>
@endsection

@section('custom_scripts')
    @include('reports.partials.admin_report_js')
@endsection
