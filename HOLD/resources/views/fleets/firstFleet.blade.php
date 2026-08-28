@extends('dashboard-layout.index')

@section('content')

<style>
   .fleet-box {
    cursor: pointer;
    transition: 0.3s ease;
    border-radius: 10px;
    padding: 10px;
    border: 1px solid #ddd;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.fleet-box:hover {
    background-color: #f9f9f9;
}

.fleet-box.border-primary {
    background-color: #e7f5ff;
    border-color: #0d6efd;
}

.fleet-box label {
    width: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.fleet-box .fleet-img {
    height: 100px;
    object-fit: contain;
    border-radius: 6px;
    margin-bottom: 10px;
}

.fleet-box ul {
    width: 100%;
    padding-left: 0;
}

.fleet-box ul li {
    padding: 6px 0;
    font-size: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.fleet-box input[type="text"] {
    padding: 2px 4px;
    font-size: 13px;
    width: 50px;
    text-align: right;
}

.car-name-input {
    padding: 2px 4px;
    font-size: 13px;
    width: 70px !important;
    text-align: center !important;
    text-align: right;
}

.fleet-box .btn-outline-success {
    margin-top: 10px;
    font-size: 14px;
}

.fleet-box .btn-secondary {
    font-size: 12px;
    padding: 2px 6px;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.modal-content {
    border-radius: 10px;
}

.fleet-box.selected {
    border: 2px solid #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
}

.fleet-box .select-btn {
    background-color: #f8f9fa;
    color: #28a745;
    border-color: #28a745;
}

.fleet-box.selected .select-btn {
    background-color: #28a745 !important;
    color: white !important;
    border-color: #28a745 !important;
}

#nextBtn {
    display: none;
}
</style>

<!-- Hidden Form -->
<form id="fleetForm">
    <input type="hidden" name="passengers" id="selectedPassengers">
    <input type="hidden" name="luggage" id="selectedLuggage">
    <input type="hidden" name="handluggage" id="selectedhandLuggage">
    <input type="hidden" name="child_seat" id="selectedChildSeat">
    <input type="hidden" name="fare" id="selectedFare">
    <input type="hidden" name="car_name" id="selectedCarName">
</form>

<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h4 class="modal-title w-100 text-center fw-bold" id="infoModalLabel">Choose a Fleet / Car Type</h4>
            </div>

            <div class="modal-body">
                <div class="row text-center g-4">

                    <!-- Repeatable Fleet Boxes -->
                    <!-- Loop starts -->
                    <!-- Fleet Box Template -->
                    <!-- Use this to duplicate and replace values/image paths -->
                    <div class="col-md-3 fleet-box" onclick="selectFleet(this)">
                        <label style="cursor:pointer; display:block; position:relative; padding-top: 35px;">
                            <input type="file" accept="image/*" style="display:none;" onchange="handleImagePreview(this)" />
                            <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-1 px-2 py-1"
                                onclick="event.stopPropagation(); this.previousElementSibling.click();">Change Image</button>
                            <img src="assets/ind1.png" class="img-fluid mb-3 fleet-img" style="height:100px;">
                        </label>
                        <h3><input type="text" value="4 Seater" class="fare-input car-name-input" maxlength="20"
                                oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 20)" /></h3>
                        <ul class="list-unstyled">
                            <li><span>Passengers:</span><input type="text" value="4" class="fare-input input-passengers" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Luggage:</span><input type="text" value="2" class="fare-input input-luggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Hand Luggage:</span><input type="text" value="2" class="fare-input input-handluggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Child Seats:</span><input type="text" value="0" class="fare-input input-child" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Fare Per Km:</span><input type="text" value="5" class="fare-input input-fare" maxlength="3"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" /></li>
                        </ul>
                        <button type="button" class="btn btn-outline-success w-100 select-btn">Select</button>
                    </div>

                    <!-- Fleet Box 2 -->
                   <div class="col-md-3 fleet-box" onclick="selectFleet(this)">
                        <label style="cursor:pointer; display:block; position:relative; padding-top: 35px;">
                            <input type="file" accept="image/*" style="display:none;" onchange="handleImagePreview(this)" />
                            <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-1 px-2 py-1"
                                onclick="event.stopPropagation(); this.previousElementSibling.click();">Change Image</button>
                            <img src="assets/ind2.png" class="img-fluid mb-3 fleet-img" style="height:100px;">
                        </label>
                        <h3><input type="text" value="6 Seater" class="fare-input car-name-input" maxlength="20"
                               oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 20)" /></h3>
                        <ul class="list-unstyled">
                            <li><span>Passengers:</span><input type="text" value="6" class="fare-input input-passengers" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Luggage:</span><input type="text" value="3" class="fare-input input-luggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Hand Luggage:</span><input type="text" value="2" class="fare-input input-handluggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Child Seats:</span><input type="text" value="0" class="fare-input input-child" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Fare Per Km:</span><input type="text" value="10" class="fare-input input-fare" maxlength="3"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" /></li>
                        </ul>
                        <button type="button" class="btn btn-outline-success w-100 select-btn">Select</button>
                    </div>

                    <!-- Fleet Box 3 -->
                 <div class="col-md-3 fleet-box" onclick="selectFleet(this)">
                        <label style="cursor:pointer; display:block; position:relative; padding-top: 35px;">
                            <input type="file" accept="image/*" style="display:none;" onchange="handleImagePreview(this)" />
                            <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-1 px-2 py-1"
                                onclick="event.stopPropagation(); this.previousElementSibling.click();">Change Image</button>
                            <img src="assets/uk1.png" class="img-fluid mb-3 fleet-img" style="height:100px;">
                        </label>
                        <h3><input type="text" value="7 Seater" class="fare-input car-name-input" maxlength="20"
                                oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 20)" /></h3>
                        <ul class="list-unstyled">
                            <li><span>Passengers:</span><input type="text" value="4" class="fare-input input-passengers" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Luggage:</span><input type="text" value="2" class="fare-input input-luggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Hand Luggage:</span><input type="text" value="2" class="fare-input input-handluggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Child Seats:</span><input type="text" value="0" class="fare-input input-child" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Fare Per Km:</span><input type="text" value="5" class="fare-input input-fare" maxlength="3"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" /></li>
                        </ul>
                        <button type="button" class="btn btn-outline-success w-100 select-btn">Select</button>
                    </div>

                    <!-- Fleet Box 4 -->
                  <div class="col-md-3 fleet-box" onclick="selectFleet(this)">
                        <label style="cursor:pointer; display:block; position:relative; padding-top: 35px;">
                            <input type="file" accept="image/*" style="display:none;" onchange="handleImagePreview(this)" />
                            <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-1 px-2 py-1"
                                onclick="event.stopPropagation(); this.previousElementSibling.click();">Change Image</button>
                            <img src="assets/uk2.png" class="img-fluid mb-3 fleet-img" style="height:100px;">
                        </label>
                        <h3><input type="text" value="8 Seater" class="fare-input car-name-input" maxlength="20"
                                oninput="this.value = this.value.replace(/[^A-Za-z0-9 ]/g, '').slice(0, 20)" /></h3>
                        <ul class="list-unstyled">
                            <li><span>Passengers:</span><input type="text" value="6" class="fare-input input-passengers" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Luggage:</span><input type="text" value="3" class="fare-input input-luggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Hand Luggage:</span><input type="text" value="2" class="fare-input input-handluggage" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Child Seats:</span><input type="text" value="0" class="fare-input input-child" maxlength="1"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1)" /></li>
                            <li><span>Fare Per Km:</span><input type="text" value="10" class="fare-input input-fare" maxlength="3"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)" /></li>
                        </ul>
                        <button type="button" class="btn btn-outline-success w-100 select-btn">Select</button>
                    </div>

                </div>

                <!-- Next Button -->
                <div class="row mt-4">
                    <div class="col-md-9 text-start">
                        <p style="font-size: 19px; color: #495057; font-weight: 600;">Note: <span style="font-size: 14px">Add at least one fleet to start. Settings can be edited in the dashboard.</span></p>
                    </div>
                    
                    <div class="col-md-3 text-end">
                        <button id="nextBtn" class="btn btn-primary">Next</button>
                    </div>
                </div>
                
        </div>
    </div>
</div>



<script>
  $(document).ready(function () {
    // Show modal on load
    $('#infoModal').modal({
      backdrop: 'static', // Prevent closing on outside click
      keyboard: false     // Prevent closing on Esc
    });

    $('#infoModal').modal('show');

    // Hide next button initially
    $('#nextBtn').hide();
  });

  // Fleet selection logic
//   function selectFleet(element) {
//     $('.fleet-box').removeClass('border border-primary shadow');
//     $(element).addClass('border border-primary shadow');

//     const image = $(element).data('image');
//     const passengers = $(element).data('passengers');
//     const luggage = $(element).data('luggage');
//     const child = $(element).data('child');
//     const fare = $(element).find('input[type="text"]').val();

//     $('#selectedImage').val(image);
//     $('#selectedPassengers').val(passengers);
//     $('#selectedLuggage').val(luggage);
//     $('#selectedChildSeat').val(child);
//     $('#selectedFare').val(fare);

//     // Show the Next button after selection
//     $('#nextBtn').fadeIn();
//   }

//   // Click Next button
//   $('#nextBtn').on('click', function (e) {
//     e.preventDefault();
//     // alert('hiii')

//     const formDataObject = new FormData($('#fleetForm')[0]);
 
//     // Add custom fields if needed
//     formDataObject.append('_token', $('input[name="_token"]').val() || '');
//     formDataObject.append('token', getCookie('d_token'));
//     formDataObject.append('device_id', 0);

//     const imageFile = $('#hidden_imageName').val();
//     if (imageFile) {
//       formDataObject.append('file', imageFile);
//     }

//     // Call your custom submit function
//     submitForm(formDataObject, 1);
//   });

</script>


@endsection

@section('custom_scripts')
  @include('fleets.partials.fleet_js')
@endsection
