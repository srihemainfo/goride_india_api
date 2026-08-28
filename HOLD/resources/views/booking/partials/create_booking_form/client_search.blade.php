<style>
    @media (max-width: 768px) {
    .modal-dialog-aside {
        width: 100% !important;
        height: 100% !important;
        margin: 0;
        max-width: none;
    }

    .modal-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
}
</style>

<div class="col-sm-12 main-card mb-3 card">

    <div class="card-body">

        

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>

                    <li class="breadcrumb-item"><a href="{{ url('booking/list/Confirmed') }}">List Bookings</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Create Booking</li>

                </ol>

            </nav>

        

        <div class="card-header">

            <h4 class="card-title">Booking Form</h4>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-sm-4">

                    <label for="search_clients">Search Clients <span class="required">*</span></label>

                    <div class="input-group">

                        <select class="form-control select2 select2-hidden-accessible" style="width: 80%;" tabindex="-1" aria-hidden="true" id="search_clients" name="client_id" data-control="select2" data-placeholder="Search Clients" data-hide-search="true">

                            <option value=""></option>

                        </select>

                        <button type="button" class="btn btn-success" title="Add Client" id="addCustomer"><i class="fas fa-plus"></i></button>

                        <p class="invalid_client_id text-danger"></p>

                    </div>

                </div>

            </div>

            <div class="row" id="client_info"></div>

        </div>

    </div>

</div>