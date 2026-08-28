<div class="col-sm-9 mx-4  main-card mb-3 card">

    <div class="card-header">
        <h4 class="card-title">Location Range Filter</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <form method="post" id="emp_filter">
            <div class="col-sm-12 row mb-2">
                <div class="col-sm-3">
                    <label for="name_filter">Name</label>
                    <input type="text" class="form-control" id="name_filter" placeholder="Search by name" name="name_filter" value="">
                </div>
                {{--<div class="col-sm-3">
                    <label for="type_filter">Type</label>
                    <select class="form-control" id="type_filter" name="type_filter">
                            <option value="">-- select Place --</option>
                            @foreach ($list_places as $place)
                                <option value="{{ $place->id }}">{{ $place->place }}</option>
                            @endforeach
                    </select>
                    <input type="hidden" name="place_filter" id="place_filter" value="">
                </div>--}}
                <div class="col-sm-3">
                    <label for="pickup_filter">Pickup Charge</label>
                    <input type="number" class="form-control" id="pickup_filter" placeholder="Search by Pickup Charge" name="pickup_filter" value="">
                </div>
                <div class="col-sm-3">
                    <label for="dropoff_filter">Dropoff Charge</label>
                    <input type="number" class="form-control" id="dropoff_filter" placeholder="Search by Dropoff Charge" name="dropoff_filter" value="">
                </div>
                <!--<div class="col-sm-2">-->
                <!--    <label for="passing_filter">Passing Charge</label>-->
                <!--    <input type="number" class="form-control" id="passing_filter" placeholder="Search by Passing Charge" name="passing_filter" value="">-->
                <!--</div>-->
              
            </div>
            <div class="col-sm-12 row mb-3">
                <div class="col-sm-6">
                    <button type="button" class="btn btn-primary" id="search"><i class="fa fa-filter"></i>&nbsp; Filter</button>
                    <button type="button" class="btn btn-danger" id="reset_filter"><i class="fa fa-undo"></i>&nbsp; Reset</button>
                </div>
            </div>
            </form>   
        </div>
    </div>

</div>