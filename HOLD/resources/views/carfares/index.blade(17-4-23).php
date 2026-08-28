@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Car Fares List</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="data-table" class="table" width="100%">
                <thead class="table-light">
                    <tr>
                        <th>Range</th>
                        <th>Saloon</th>
                        <th>Executive Saloon</th>
                        <th>Estate</th>
                        <th>MPV</th>
                        <th>MPV5</th>
                        <th>MPV6</th>
                        <th>MPV8</th>
                        <th>MPV Executive</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list_car_fares as $car_fare)
                        <tr>
                            <form id="car_fare_{{ $car_fare->id }}">
                                <td>{{ $car_fare->start .' - '. $car_fare->end }}</td>
                                <td><input style="width:60px;text-align:right;" type="number" name="saloon" value="{{ $car_fare->saloon }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="executive" value="{{ $car_fare->executive }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="estate" value="{{ $car_fare->estate }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv" value="{{ $car_fare->mpv }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv5" value="{{ $car_fare->mpv5 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv6" value="{{ $car_fare->mpv6 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv8" value="{{ $car_fare->mpv8 }}"></td>
                                <td><input style="width:60px;text-align:right;" type="number" name="mpv_executive" value="{{ $car_fare->mpv_executive }}"></td>
                                @if($IS_UPDATABLE)
                                <td><input type="hidden" name="fare_id" value="{{ $car_fare->id }}"><button class="btn btn-success updateFare" data-id="{{ $car_fare->id }}"><i class="fa fa-check"></i> &nbsp; update</button></td>
                                @endif
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-danger">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('custom_scripts')
    @include('carfares.partials.carfares_js')
@endsection