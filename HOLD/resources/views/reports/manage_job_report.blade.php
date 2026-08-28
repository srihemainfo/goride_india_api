@extends('dashboard-layout.index')



@section('content')
 


<style>
    .select2 { 

        width: 100% !important;

    }





    .nav-tabs .nav-link:hover {

        background-color: #747474 !important;

        color: white !important;

    }

    .nav-link.active {

        background-color: #fff !important;

        color: #343a40 !important;

    }



    .nav-link:hover {

        background-color: #6c757d !important;

    }
</style>



<!--<div class="col-sm-10  ">-->





@include('reports.partials.filter')



<div class="col-sm-12 main-card mb-3 card">

        

    <div class="card-body mt-2">

        <div class="table-responsive"> 

            <table id="job-table" class="table" width="100%">

                <thead class="table-light">

                    <tr>

                        <th>S.No</th>
                        <th>Person Accepting Booking</th>
                        <th>Date of Booking</th>
                        <th>Date of Journey</th>
                        <th>Customer Name</th>
                        <th>Mobile No</th>
                        <th>Place of Collection</th>
                        <th>Main Destination</th>
                        <th>Fare Quoted</th>
                        <th>Person Dispatching</th>
                        <th>Driver Details</th>
                        <th>Vehicle Reg</th>
                        <th>Job Status</th>

                    </tr>

                </thead>

            </table>

        </div>

    </div>

</div>

</div>











 
@endsection



@section('custom_scripts')

@include('reports.partials.job_report_js')

@endsection