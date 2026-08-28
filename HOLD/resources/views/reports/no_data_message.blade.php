@extends('dashboard-layout.index')

@section('content')
    <div class="col-12 jumbotron">
        <h1 class="text-danger">No data found.</h1>
        <p class="lead">There is no settled job's data available to generate proper report.</p>
        <a class="btn btn-lg btn-primary"
            href="{{ $report_type === 'admin' ? route('ManageAdminReport') : route('ManageDriverReport') }}"
            role="button">Back to {{ $report_type }} reports »
        </a>
    </div>
@endsection
