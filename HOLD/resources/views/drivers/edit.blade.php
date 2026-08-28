





@extends('dashboard-layout.index')



@section('content')


<form method="post" id="driver_up_form" enctype="multipart/form-data">

    <input type="hidden" name="driver_id" id="driver_id">

    <!--<input type="hidden" name="ExistingImagePath" value="{{ optional($driver[0] ?? null)->photo }}">-->

    @include('drivers.partials.create_drivers_form.breadcrumb')

    @include('drivers.form')

</form>

<div class="col-sm-12 main-card mb-3">

        <div>

     
     

<div class="row justify-content-center">

    <div class="col-3">

<button type="submit" class="btn btn-primary" id="driver_sub_up"><i class="fa fa-save"></i>&nbsp; Update</button>

</div></div>

                

            

        </div>

    </div>
    <style>
        @media screen and (min-width:320px)and(max-width:776px){
           .btn-primary{
           width: 102px !important;
           left: -26px !important;
       }
           }
       </style>  

@include('drivers.partials.add_fileupload_modal')

@endsection



@section('custom_scripts')

    @include('drivers.partials.form-js')

@endsection