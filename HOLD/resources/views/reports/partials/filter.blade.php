<div class="col-sm-12 main-card mb-3 card">

    <div class="card-header mt-3">

        <h4 class="card-title">Job Details Filter</h4>

    </div>

    <div class="card-body mt-2">

        <div class="row">

            <form method="post" id="job_detail_filter">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <label for="name_filter">Name/Phone No</label>
                        <input type="text" class="form-control" id="name_filter" maxlength="70" placeholder="Search by Name/Phone No" name="name_filter" value="">
                    </div>

                    <div class="col-sm-3">
                        <label for="job_date_filter">Date Search</label>
                        <input type="text" class="form-control" id="job_date_filter" maxlength="70" placeholder="Search Date" name="job_date_filter" value="">
                    </div>

                    <div class="col-sm-3 d-flex align-items-end mt-2">
                        <button type="button" class="btn btn-primary mr-2" id="job_search">
                            <i class="fa fa-filter"></i>&nbsp; Filter
                        </button>
                        <button type="button" class="btn btn-danger" id="reset_job_filter">
                            <i class="fa fa-undo"></i>&nbsp; Reset
                        </button>
                    </div>
                </div>
            </form>




        </div>

    </div>

</div>