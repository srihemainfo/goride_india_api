{{-- @php
    dd("Test view");
@endphp --}}
@extends('dashboard-layout.index')

@section('content')
<div class="col-sm-12 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title">Currency Form</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('currency.update', $currency->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <div class="row">
                @csrf
                <div class="col-sm-12 row mb-4">
                    <div class="col-sm-4">
                        <label for="pound">Pounds <span class="required">*</span></label>
                        <input type="text" class="form-control" id="pound" placeholder="0.00" name="pound" value="{{ old('pound', optional($currency ?? null)->pound) }}" readonly>
                        @error('pound')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-sm-4">
                        <label for="euro">Euro <span class="required">*</span></label>
                        <input type="text" class="form-control" id="euro" placeholder="0.00" name="euro" value="{{ old('euro', optional($currency ?? null)->euro) }}">
                        @error('euro')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-sm-4">
                        <label for="dollar">Dollar <span class="required">*</span></label>
                        <input type="text" class="form-control" id="dollar" placeholder="0.00" name="dollar" value="{{ old('dollar', optional($currency ?? null)->dollar) }}">
                        @error('dollar')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                @if($IS_UPDATABLE)
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp; Update</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('custom_scripts')
<script>
    @if (session('success'))
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Updated',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000,
        })    
    @endif
</script>
@endsection