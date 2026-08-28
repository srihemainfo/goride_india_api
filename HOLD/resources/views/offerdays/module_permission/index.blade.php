

@extends('dashboard-layout.index')



@section('content')



<style>
  @media screen (min-width:320px) and (max-width:776px){
    .employee{
      display: none;
    }
  }
</style>

    <!-- Left Content - sm-10 -->

    <div class="col-sm-9 ">

        {{-- 

        <div class="col-sm-12 alert alert-danger">

            <strong>Note: </strong> Update permissions on each row individually, removing 'READ' permission will remove all other permissions in the module.

        </div> 

        --}}

        

        <div class="main-card mb-3 card">

            <div class="card-header">

                <h4 class="card-title">Choose the role Permission</h4>

               

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-sm-3 col-xl-3 col-md-3 col-12">

                        <label for="role_id">Role Type <span class="required">*</span></label>

                        <select class="form-control select2" style="width: 100%;" tabindex="-1" id="role_id" name="role_id" data-placeholder="Select an option" data-hide-search="true">

                            <option value="">Select Role</option>

                            @foreach($roles as $id => $value)

                            <option value={{$id}}>{{ ucwords($value) }}</option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-sm-12 main-card mb-3 card">

    <div class="card-header">

        <h4 class="card-title">Employee Module Permissions</h4>

        

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table id="data-table" class="table" width="100%">

                <thead class="table-light">

                    <tr>

                        <th style="width:5%;">#</th>

                        <th>ID</th>

                        <th>Module Name</th>

                        <th>READ</th>

                        <th>CREATE</th>

                        <th>UPDATE</th>

                        <th>DELETE</th>

                        <th style="width:10%;">Action</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

          <div class="text-center">

         <button class="btn btn-primary mt-2" id='update-all-btn'>Update All</button>

         </div>

    </div>

</div>

    </div>



    <!-- Right Sidebar - sm-2 -->

   

        <div class="col-sm-2 main-card mb-3 card position employee">

            <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist" aria-orientation="vertical">

                <a class="nav-link text-light" id="vert-tabs-right-home-tab" href="/employee" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">

                    <i class="fa-solid fa-user" style="margin-right: 8px;"></i> Employees

                </a>

                <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/module-permissions" role="tab" aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">

                    <i class="fas fa-cogs" style="margin-right: 8px;"></i> Module Permission

                </a>

            </div>

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

    @include('offerdays.module_permission.partials.module_permission_js')

@endsection

