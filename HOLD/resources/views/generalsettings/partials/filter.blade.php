<div class="col-sm-12 main-card mb-3 card">

    <div class="card-header">
        <h4 class="card-title">Customer Filter</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <form method="post" id="cus_filter_form">
            <div class="col-sm-12 row mb-2">
                <div class="col-sm-3">
                    <label for="cus_name_filter">Name</label>
                    <input type="text" class="form-control" id="cus_name_filter" placeholder="Search by name" name="cus_name_filter" value="">
                </div>
                <div class="col-sm-3">
                    <label for="cus_cmpny_filter">Company Name</label>
                    <input type="text" class="form-control" id="cus_cmpny_filter" placeholder="Search by company name" name="cus_cmpny_filter" value="">
                </div>
                <div class="col-sm-3">
                    <label for="cus_email_filter">Email</label>
                    <input type="text" class="form-control" id="cus_email_filter" placeholder="Search by email" name="cus_email_filter" value="">
                </div>
                <div class="col-sm-3">
                    <label for="cus_phone_filter">Phone No</label>
                    <input type="text" class="form-control" id="cus_phone_filter" placeholder="Search by phone" name="cus_phone_filter" value="">
                </div>
            </div>
            </form>
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-3">
                    <button type="button" class="btn btn-primary" id="cus_search"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
        </div>
    </div>

</div>