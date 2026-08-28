@extends('dashboard-layout.index')

@section('content')

<form id="driver_form" enctype="multipart/form-data">
    @include('drivers.partials.create_drivers_form.breadcrumb')
    @include('drivers.form')
</form>
<div class="col-sm-12 main-card mb-3">
        <div>
            <div class="row justify-content-center">
                <div class="col-3">
                    <button type="submit" id="driver_sub" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp; Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_scripts')
    @include('drivers.partials.form-js')
@endsection