<div class="col-sm-9 mx-4 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Restriction Filter</h4>
    </div>
    <div class="card-body">
        <div class="row">
        <form method="post" id="emp_filter">
            <div class="col-sm-12 row mb-2">
                <div class="col-sm-3">
                    <label for="name_filter">Caption</label>
                    <input type="text" class="form-control" id="caption_filter" placeholder="Search by name" name="caption_filter">
                </div>
                <div class="col-sm-3">
                    <label for="phone_filter">Recurring</label>
                <select  class="form-control" name="recurring_filter" id="recurring_filter">
                  <option value="" selected="">Select</option>
                  <option value="No" selected="">No</option>
                  <option value="Yearly">Yearly</option>
                  <option value="Daily">Daily</option>
                  </select>  
                </div>
                <!--<div class="col-sm-3">-->
                <!--    <label for="email_filter">Email</label>-->
                <!--    <input type="text" class="form-control" id="email_filter" placeholder="Search by email" name="email_filter" value="">-->
                <!--</div>-->
                <!--<div class="col-sm-3">-->
                <!--    <label for="user_status">Status</label>-->
                <!--    <select class="form-control" id="user_status" name="user_status">-->
                <!--        <option value="" selected>Show All</option>-->
                <!--        <option value="Active">Active</option>-->
                <!--        <option value="Inactive">Inactive</option>-->
                <!--    </select>-->
                <!--    <input type="hidden" name="active_status" id="active_status" value="">-->
                <!--</div>-->
            </div>
            </form>
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-primary" id="emp_search"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="reset_emp_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>