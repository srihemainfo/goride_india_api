@extends('dashboard-layout.index')



@section('content')



<style>

.select2 {

    width: 100% !important ;

}

    



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
@media screen and (min-width: 320px) and (max-width: 776px) {
    .employee {
        display: none !important;
    }
}




   </style>



<!--<div class="col-sm-10  ">-->





@include('employees.partials.filter')

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
.modal-dialog-aside {
    left: 12px !important;
}

</style>

<div class="col-sm-9  main-card mb-2 card">

    <div class="card-header">

        <h4 class="card-title">Employee List</h4>

        <div class="btn-actions-pane-right">

                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->

            <button type="button" class="btn btn-success" id="addEmployee" data-toggle="modal" data-target="#add_cus_form-modal"><i class="fas fa-plus"></i> Add Employee </button>

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="emp-table" class="table" width="100%">

                <thead class="table-light">

                  <tr>

                        <th>#</th>

                        <th>Full Name</th>

                        <th>Phone No</th>

                        <th>Email</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>

              </table>

        </div>

    </div>

</div>

</div>







   <div class="col-sm-2 main-card mb-3 card position employee">

  <div class="nav flex-column nav-tabs nav-tabs-right " id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

    

    <a class="nav-link  active text-light" id="vert-tabs-right-home-tab" href="/employee" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

      <i class="fa-solid fa-user" style="margin-right: 8px;"></i> Employees

    </a>

    

    <a class="nav-link  text-light" id="vert-tabs-right-offer-times-tab" href="/module-permissions" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

     <i class="fas fa-cogs"style="margin-right: 8px;"></i> Module Permission

    </a>

    



    

  </div>

</div>



    @include('employees.partials.add_employee_modal')

    @include('employees.partials.edit_employee_modal')

    @include('employees.partials.password_change_modal')

@endsection



@section('custom_scripts')

    @include('employees.partials.employees_js')

@endsection

