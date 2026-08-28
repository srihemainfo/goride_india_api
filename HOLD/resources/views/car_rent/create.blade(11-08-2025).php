@extends('dashboard-layout.index')

@section('content')

<div class="container mt-4">
  <form action="#" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Car Details -->
    <div class="card card-box">
      <div class="card-body">
        <h5 class="card-title">Car Details</h5>

        <div class="text-center mb-4">
          <img src="https://www.carbike360.com/_next/image?url=https%3A%2F%2Fdelen.s3.ap-southeast-1.amazonaws.com%2Fsmall_PUNCH_074ded8c69.jpg&w=3840&q=75" 
               class="img-fluid" style="width: 300px; height: 180px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
        </div>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Car Image *</label>
            <input type="file" name="car_image" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Car Name / Title *</label>
            <input type="text" name="car_name" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Brand *</label>
            <input type="text" name="brand" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Model *</label>
            <input type="text" name="model" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Car Number *</label>
            <input type="text" name="car_number" class="form-control" placeholder="TN-01-AB-1234" required>
          </div>
          <div class="form-group col-md-4">
            <label>Fuel Type *</label>
            <select name="fuel_type" class="form-control" required>
              <option value="">Select</option>
              <option>Petrol</option>
              <option>Diesel</option>
              <option>Electric</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>Transmission *</label>
            <select name="transmission" class="form-control" required>
              <option value="">Select</option>
              <option>Automatic</option>
              <option>Manual</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>Seating Capacity *</label>
            <select name="seating_capacity" class="form-control" required>
              <option value="">Select</option>
              @for($i = 1; $i <= 15; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Rental Pricing -->
    <div class="card card-box mt-4">
      <div class="card-body">
        <h5 class="card-title">Rental Pricing</h5>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>Hourly Rate (₹) *</label>
            <input type="number" name="hourly_rate" class="form-control" required>
          </div>
          <div class="form-group col-md-4">
            <label>Driver Charges (₹)</label>
            <input type="number" name="driver_charges" class="form-control">
          </div>
          <div class="form-group col-md-4">
            <label>Total (₹)</label>
            <input type="number" name="total" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <!-- Optional Services -->
    <div class="card card-box mt-4">
      <div class="card-body">
        <h5 class="card-title">Optional Services</h5>

        <div class="form-row">
          <div class="form-group col-md-4">
            <label>GPS Available?</label>
            <select name="gps" class="form-control">
              <option value="">Select</option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>Baby Seat Available?</label>
            <select name="baby_seat" class="form-control">
              <option value="">Select</option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>AC / Non-AC</label>
            <select name="ac" class="form-control">
              <option value="">Select</option>
              <option value="AC">AC</option>
              <option value="Non-AC">Non-AC</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit -->
    <div class="text-center mt-4 mb-5">
      <button type="submit" class="btn btn-primary  px-5 py-2">Create</button>
    </div>

  </form>
</div>

@endsection
