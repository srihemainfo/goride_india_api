

<div class="col-sm-12 main-card mb-3 card">

    <div class="card-header">

        <h4 class="card-title">Invoice Filter</h4>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-sm-12 row mb-2">

                <div class="col-sm-3">

                <label for="invoice_no_filter">Invoice No</label>

                <input type="text" class="form-control" id="invoice_no_filter" placeholder="Search by no" name="invoice_no_filter" value="">

                </div>

                

                <div class="col-sm-3" id="custom_select">

                    <div class="form-group">

                        <label for="invoice_between_filter">Invoice Date</label>

                        <!--<input type="text" class="form-control" id="index_custom_filter" placeholder="Select date" name="index_custom_filter" value="">-->

                        <input class="form-control" type="text" id="invoice_between_filter" name="invoice_between_filter" value="" placeholder="YYYY-MM-DD" autocomplete="off" />

                    </div>

                </div>

            </div>

            <div class="col-sm-12 row mb-3"> 

                <div class="col-sm-6">

                <input type="hidden" name="filter_from_date" id="filter_from_date" value="">

                <input type="hidden" name="filter_to_date" id="filter_to_date" value="">

                    <button type="button" class="btn btn-primary" id="search"><i class="fa fa-filter"></i>&nbsp; Filter</button>

                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>

                </div>

            </div>

        </div>

    </div>

</div>