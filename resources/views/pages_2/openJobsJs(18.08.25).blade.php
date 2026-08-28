<script>
    $(document).ready(function () {
        openJobs()
        currentJobs()
    });

    function convertDateFormat(txt) {
        let dateString = txt;

        // Create Date object (replace space with T so it's ISO compatible)
        let dateObj = new Date(dateString.replace(" ", "T"));

        // Format
        let day = String(dateObj.getDate()).padStart(2, '0');
        let month = dateObj.toLocaleString('en-US', { month: 'short' });
        let hours = dateObj.getHours();
        let minutes = String(dateObj.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12; // Convert 24h to 12h format

        let formattedDate = `${day} ${month} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
        return formattedDate;

    }

    function expiryCals(txt) {
        let pickupDateStr = txt;

        // Convert to Date object
        let pickupDate = new Date(pickupDateStr.replace(" ", "T"));
        let now = new Date();

        // Calculate the difference in milliseconds
        let diffMs = pickupDate - now;

        // If the date is in the future
        if (diffMs > 0) {
            let diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            let diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            return `Expiry in ${diffDays} days and ${diffHours} hours`;
        } else {
            return "Expired";
        }

    }

    function jobPostTime(txt) {

        let postedTimeStr = txt;

        // Convert string to Date object (make it ISO format)
        let postedDate = new Date(postedTimeStr.replace(" ", "T"));
        let now = new Date();

        let diffMs = now - postedDate; // milliseconds difference
        let diffSeconds = Math.floor(diffMs / 1000);
        let diffMinutes = Math.floor(diffSeconds / 60);
        let diffHours = Math.floor(diffMinutes / 60);
        let diffDays = Math.floor(diffHours / 24);

        let displayText = "";

        if (diffDays > 0) {
            displayText = `Posted ${diffDays} day${diffDays > 1 ? "s" : ""} ago`;
        } else if (diffHours > 0) {
            displayText = `Posted ${diffHours} hour${diffHours > 1 ? "s" : ""} ago`;
        } else if (diffMinutes > 0) {
            displayText = `Posted ${diffMinutes} minute${diffMinutes > 1 ? "s" : ""} ago`;
        } else {
            displayText = `Posted just now`;
        }

        return displayText;

    }

    function openJobs() {

        $.ajax({
            url: "{{ env('APP_API') }}get-jobs",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: [],
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#con_create");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            },
            success: function (response) {
                if (response.status) {
                    let data = response.data.jobs ? response.data.jobs : [];
                    $('#openJobs_cards').empty();
                    let jobs_content = '';
                    if (data.length > 0) {

                        $.each(data, function (index, value) {

                            let expiryTime = expiryCals(value.pickup_date);
                            value.pickup_date = convertDateFormat(value.pickup_date);
                            value.dropoff_date = value.dropoff_date ? convertDateFormat(value.dropoff_date) : '';

                            let decode_details = JSON.parse(value.add_fare_details);

                            decode_details.bata = decode_details.bata == 'Included' ? 'Inc.' : 'PTP';
                            decode_details.toll = decode_details.toll == 'Included' ? 'Inc.' : 'PTP';
                            decode_details.parking = decode_details.parking == 'Included' ? 'Inc.' : 'PTP';

                            let add_details = 'Toll ' + decode_details.toll + ', Parking ' + decode_details.parking + ', Bata ' + decode_details.bata;

                            jobs_content +=

                                `<div class="compact-car-card">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                  <div class="col-md-12 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div class="col-md-6 col-6 d-flex align-items-center gap-1">
                                                                        <span class="passenger-count d-flex align-items-center gap-1">
                                                                            <i class="fas fa-users"></i> ${value.pass_count}
                                                                        </span>
                                                                        <span class="distance d-flex align-items-center gap-1">
                                                                            <i class="fas fa-route"></i> ${value.distance} km
                                                                        </span>
                                                    
                                                                    </div>
                                                    
                                                                    <!-- Right: Amount & Description -->
                                                                   <div class="col-md-6 col-6 d-flex justify-content-end align-items-end">
                                                        <div class="amount d-flex flex-column justify-content-end align-items-end">
                                                        <span class="price mb-1"
">₹ ${value.fare}</span>
                                                            <span class="bids-c ">
                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                            </span>
                                                                                                     
                                                        </div>
                 
                                                        
                                                    </div>
                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12 py-1 px-0 col-12">
                                                        <div class="trip-info">
                                                            <div class="route-section">
                                                                <div class="location-time">
                                                                 <div class="date-time">${value.pickup_date}</div>
                                                                    <div class="location">${value.from_place}</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                    <div class="arrow-line">
                                                                        <div class="duration d-none d-sm-block">${value.duration}</div>
                                                                        <div class="arrow-container">
                                                                            <div class="long-arrow"></div>
                                                                            <i class="fas fa-car car-icon"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time">${value.dropoff_date ?? ''}</div>
                                                                    <div class="location">${value.to_place}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                            <div class="col-12 d-md-flex justify-content-md-between align-items-center flex-md-wrap align-items-end">
                                                            
                                                                <!-- Left: Posted Info (Desktop Only) -->
                                                                <div class="d-md-flex d-inline-block align-items-center justify-content-center small text-muted gap-4 mb-2 d-none d-md-block">
                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="fa-solid fa-badge-check text-success"></i>
                                                                        Posted by
                                                                        <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                            ${value.poster_name}
                                                                        </strong>
                                                                    </span>
                                                                </div>
                                                            
                                                                <!-- Center: Expiry + Icons (Desktop Only) -->
                                                                <div class="d-md-flex d-inline-block align-items-center justify-content-center gap-3 mb-2 d-none d-md-block">
                                                                    <span class="text-form">
                                                                        <i class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                                        ${expiryTime}
                                                                    </span>
                                                                </div>
                                                            
                                                                <!-- Mobile View: Posted + Expiry in one row -->
                                                                <div class=" small text-muted mt-2 d-md-none" style="
    border-top: 2px solid #ddd;
">
                                                                <div class="d-flex justify-content-between mt-2">
    <!-- Pickup with icon -->
    <span class="d-inline-flex align-items-center gap-1 date-time">
        <i class="fa-solid fa-badge-check text-success"></i>
        <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" 
                data-bs-toggle="modal" data-bs-target="#postedModal">
            ${value.pickup_date}
        </strong>
    </span>

    <!-- Dropoff only if date exists -->
    <span class="d-inline-flex align-items-center gap-1 date-time">
        ${value.dropoff_date 
            ? `<i class="fa-solid fa-badge-check text-success"></i>
               <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" 
                       data-bs-toggle="modal" data-bs-target="#postedModal">
                   ${value.dropoff_date}
               </strong>` 
            : ''}
    </span>
</div>


                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="fa-solid fa-badge-check text-success"></i>
                                                                        Posted by
                                                                        <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                            ${value.poster_name}
                                                                        </strong>
                                                                        <span class="ms-auto" id="actionSection">
           ${value.user_bid == 'yes' ?
                                                                                                `<button class="btn btn-sm btn-warning ms-auto agreeBtn ${value.job_status != 'created' ? 'd-none' : ''}" onclick="bidManage('${value.id}')">
                                                                            <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                        </button>`
                                                                                                :
                                                                                                `<button class="btn btn-sm btn-success ms-auto agreeBtn"
                                                                            data-bs-toggle="modal" data-bs-target="#agreedModal">
                                                                            <i class="fas fa-thumbs-up me-1"></i> Agree
                                                                        </button>`
                                                                                            }                             </span>
                                                                    </span>
                                                                    <span class="text-form">
                                                                        <i class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                                        ${expiryTime}
                                                                    </span>
                                                                </div>
                                                            
                                                                <!-- Right: Action Buttons -->
                                                                <div class="text-end" id="actionSection">
                                                                   
                                                            
                                                                    ${value.user_bid == 'yes' ? `` :
                                                                                                `<div class="small text-muted mt-2 place-bid-section">
                                                                            Need to increase amount?
                                                                            <a href="#"
                                                                                class="text-primary fw-semibold text-decoration-none" onclick="bidCreate('${value.poster_name}', '${value.poster_company}', '${value.poster_mobile}', '${value.poster_ratings}', '${value.poster_complete_jobs}','${value.id}', '${value.created_at}')">
                                                                                Place bid
                                                                            </a>
                                                                        </div>`
                                                                                            }
                                                                </div>
                                                            
                                                            </div>



                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`

                                ;
                        })
                    }
                    else {
                        jobs_content = `
                                        <div class="job-empty-card">
                                            <div class="job-empty-body">
                                                <i class="fa-solid fa-briefcase text-danger"></i>
                                                <p>No jobs available</p> 
                                            </div>
                                        </div>
                                    `;
                    }
                    $('#openJobs_cards').html(jobs_content);
                    // console.log(response.data);
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#con_create");
                btn.html('Update');
            }
        });
    }

    function currentJobs() {

        $.ajax({
            url: "{{ env('APP_API') }}my-current-jobs",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: [],
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#con_create");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            },
            success: function (response) {
                if (response.status) {
                    let data = response.data.jobs ? response.data.jobs : [];
                    $('#current').empty();
                    let jobs_content = '';
                    if (data.length > 0) {
                        $.each(data, function (index, value) {

                            let expiryTime = expiryCals(value.pickup_date);
                            value.pickup_date = convertDateFormat(value.pickup_date);
                            value.dropoff_date = value.dropoff_date ? convertDateFormat(value.dropoff_date) : '';

                            let decode_details = JSON.parse(value.add_fare_details);

                            decode_details.bata = decode_details.bata == 'Included' ? 'Inc.' : 'PTP';
                            decode_details.toll = decode_details.toll == 'Included' ? 'Inc.' : 'PTP';
                            decode_details.parking = decode_details.parking == 'Included' ? 'Inc.' : 'PTP';

                            let add_details = 'Toll ' + decode_details.toll + ', Parking ' + decode_details.parking + ', Bata ' + decode_details.bata;


                            jobs_content +=

                                `<div class="compact-car-card ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-center align-items-center">
                                                    <div class="col-md-7 col-12">
                                                        <div class="company-info">
                                                            <div class="car-specs">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex align-items-center gap-3">
                                                                        <span class="passenger-count">
                                                                            <i class="fas fa-users"></i> ${value.pass_count}
                                                                        </span>
                                                                        <span class="distance">
                                                                            <i class="fas fa-route"></i> ${value.distance} km
                                                                        </span>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-6 col-12 d-flex justify-content-center align-items-end flex-column">
                                                                        <div class="amount"><span class="bids-c me-3 ">
                                                                                <i class="fas fa-gavel"></i> 15 Bids
                                                                            </span>₹ ${value.fare}</div>
                                                                        <div class="amount-des">
                                                                            <i class="fas fa-bullhorn"></i> <small>${add_details}</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="trip-info">
                                                            <div class="route-section">
                                                                <div class="location-time">
                                                                    <div class="date-time">${value.pickup_date}</div>
                                                                    <div class="location">${value.from_place}</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                    <div class="arrow-line">
                                                                        <div class="duration d-none d-sm-block">${value.duration}</div>
                                                                        <div class="arrow-container">
                                                                            <div class="long-arrow"></div>
                                                                            <i class="fas fa-car car-icon"
                                                                                style="transform: scale(1); color: rgb(0, 123, 255);"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time">${value.dropoff_date ?? ''}</div>
                                                                    <div class="location">${value.to_place}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                   <div class="col-md-5 col-12 position-relative">
                                                                <div class="vertical-divider d-none d-md-block" style="
                                                                border-left: 2px solid #888f00;
                                                                height: 100%;
                                                                position: absolute;
                                                                top: 0;
                                                            
                                                            "></div>
                                                        <div style="display: flex;justify-content: end;align-items: end;">
                                                        <div class="dropdown sort-dropdown  text-end">
                                                          <button class="btn btn-light dropdown-toggle" type="button" id="sortMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-filter me-2" id="selectedSort"></i>
                                                          </button>
                                                        
                                                          <ul class="dropdown-menu" aria-labelledby="sortMenuButton">
                                                            <li><a class="dropdown-item" href="#" onclick="setActiveSort(this)">Price - Low to High</a></li>
                                                            <li><a class="dropdown-item" href="#" onclick="setActiveSort(this)">Price - High to Low</a></li>
                                                          </ul>
                                                        </div>


                                                        </div>
                                                        <!--<div class="d-flex justify-content-center align-items-center" style="font-weight:500">-->
                                                        <!--    <strong>Driver Bids</strong>-->
                                                        <!--    </div>-->
                                                        <div class="scrollable-bid-list" style="">
                                                            
                                                <div class="row bid-card-wrapper">
                                                            <div class="bid-card" data-card-id="1">
                                                              <div class="bid-card-row redesigned-bid-card ">
                                                                
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <div class="d-flex justify-content-center gap-3">
                                                                  <div class="driver-info">
                                                                  <div class="driver-name" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#userModal">Ramesh Kumar</div>
                                                                  </div>
                                                              
                                                                    <div class="amount-bid">₹100
                                                                  </div>
                                                                   <div class="bid-actions" id="actions-1" style="display: flex; align-items: center; gap: 0.5rem;">
                                                          <button class="btn btn-success p-2 btn-sm accept-btn" onclick="showAcceptConfirmation(1)" title="Accept">
                                                            <i class="fa-solid fa-check"></i>
                                                          </button>
                                                        
                                                        </div>

                                                                      <!-- Right: After Accepted -->
                                                                <div class="bid-post-accept d-none" id="post-accept-1">
                                                        <!-- Call icon -->
                                                        <div href="tel:+919999999999" class="icon-circle bg-primary text-white me-2" title="Call">
                                                            <i class="fas fa-phone fa-lg"></i>
                                                        </div>
                                                    
                                                        <!-- WhatsApp icon -->
                                                        <div href="https://wa.me/919999999999" target="_blank" class="icon-circle bg-success text-white me-2" title="WhatsApp">
                                                            <i class="fab fa-whatsapp fa-lg"></i>
                                                        </div>
                                                    
                                                        <!-- Reject button -->
                                                        <button class="btn btn-danger p-2 btn-sm accept-btn" onclick="showRejectModal(1)" title="Reject">
                                                            <i class="fas fa-times-circle"></i> 
                                                        </button>
                                                    </div>
                                                            </div>
                                                            </div>
                                                          
                                                          </div>


                                                                  
                                                                </div>

                                                                <!-- Remarks section -->
                                                                <div class=" glassy-remarks mt-2" style="display: none;">
                                                                      <p>Available for immediate dispatch
                                                                        Prefers bypass route</p>
                                                                </div>

                                                                <!-- Flag options -->
                                                                <div id="toreport" class="flag-options stylish-flags mt-2" style="display: none;">
                                                                    <button onclick="reportUser()"><i class="fas fa-exclamation-circle"></i>
                                                                        Block</button>
                                                                    <button onclick="markAsSpam()"><i class="fas fa-ban"></i> Block and Report
                                                                        with Spam</button>
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <div class="row bid-card-wrapper">
                                                            <div class="bid-card" data-card-id="2">
                                                              <div class="bid-card-row redesigned-bid-card ">
                                                                                                                                
                                                                <div class="col-12">
                                                                    <div class="row">
                                                                        <div class="d-flex justify-content-center gap-3">
                                                                          <div class="driver-info">
                                                                             <div class="driver-name" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#userModal">Ramesh Kumar</div>
                                                                          </div>
                                                                          <div class="driver-info">
                                                                            <div class="amount-bid">₹100</div>
                                                                          </div>
                                                                            <div class="bid-actions" id="actions-2">
                                                                                                                                       <button class="btn btn-success p-2 btn-sm accept-btn" onclick="showAcceptConfirmation(2)" title="Accept">
                                                                    <i class="fa-solid fa-check"></i> </button>
                                                                        
                                                                   
                                                                    </div>
                                                                      <!-- Right: After Accepted -->
                                                                   <div class="bid-post-accept d-none" id="post-accept-2">
                                                                <!-- Call icon -->
                                                               <div href="tel:+919999999999" class="icon-circle bg-primary text-white me-2" title="Call">
                                                                    <i class="fas fa-phone fa-lg"></i>
                                                                </div>
                                                            
                                                                <!-- WhatsApp icon -->
                                                                  <div href="https://wa.me/919999999999" target="_blank" class="icon-circle bg-success text-white me-2" title="WhatsApp">
                                                                    <i class="fab fa-whatsapp fa-lg"></i>
                                                                </div>
                                                            
                                                                <!-- Reject button -->
                                                                <button class="btn btn-danger p-2 btn-sm accept-btn" onclick="showRejectModal(2)" title="Reject">
                                                                    <i class="fas fa-times-circle"></i> 
                                                                </button>
                                                            </div>
                                                                    </div>
                                                                    </div>
                                                                
                                                                  </div>

                                                                  
                                                                </div>

                                                                <!-- Remarks section -->
                                                                <div class=" glassy-remarks mt-2" style="display: none;">
                                                                      <p>Available for immediate dispatch
                                                                        Prefers bypass route</p>
                                                                </div>

                                                                <!-- Flag options -->
                                                                <div class="flag-options stylish-flags mt-2" style="display: none;">
                                                                    <button onclick="reportUser('Ramesh Kumar')"><i class="fas fa-exclamation-circle"></i>
                                                                        Block</button>
                                                                    <button onclick="markAsSpam('Ramesh Kumar')"><i class="fas fa-ban"></i> Block and Report
                                                                        with Spam</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row bid-card-wrapper">
                                                            <div class="bid-card" data-card-id="3">
                                                                <div class="bid-card-row redesigned-bid-card ">
                                                                                                                  
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="d-flex justify-content-center gap-3">
                                                          <div class="driver-info">
                                                             <div class="driver-name" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#userModal">Ramesh Kumar</div>
                                                          </div>
                                                          <div class="driver-info">
                                                            <div class="amount-bid">₹100</div>
                                                          </div>
                                                          
                                                                    <div class="bid-actions" id="actions-3">
                                                                       <button class="btn btn-success p-2 btn-sm accept-btn" onclick="showAcceptConfirmation(3)" title="Accept">
                                                                            <i class="fa-solid fa-check"></i> </button>
                                                                       
                                                                       
                                                                    </div>
                                                                    
                                                                    <!-- Right: After Accepted -->
                                                                  <div class="bid-post-accept d-none" id="post-accept-3">
                                                        <!-- Call icon -->
                                                       <div href="tel:+919999999999" class="icon-circle bg-primary text-white me-2" title="Call">
                                                            <i class="fas fa-phone fa-lg"></i>
                                                        </div>
                                                    
                                                         <div href="https://wa.me/919999999999" target="_blank" class="icon-circle bg-success text-white me-2" title="WhatsApp">
                                                            <i class="fab fa-whatsapp fa-lg"></i>
                                                        </div>
                                                        <!-- Reject button -->
                                                        <button class="btn btn-danger p-2 btn-sm accept-btn" onclick="showRejectModal(3)" title="Reject">
                                                            <i class="fas fa-times-circle"></i> 
                                                        </button>
                                                    </div>
                                                            </div>
                                                            </div>
                                                           
                                                          </div>


                                                                </div>

                                                                <!-- Remarks section -->
                                                                <div class=" glassy-remarks mt-2" style="display: none;">
                                                                     <p>Available for immediate dispatch
                                                                        Prefers bypass route</p>
                                                                </div>

                                                                <!-- Flag options -->
                                                                <div class="flag-options stylish-flags mt-2" style="display: none;">
                                                                    <button onclick="reportUser('Ramesh Kumar')"><i class="fas fa-exclamation-circle"></i>
                                                                        Block</button>
                                                                    <button onclick="markAsSpam('Ramesh Kumar')"><i class="fas fa-ban"></i> Block and Report
                                                                        with Spam</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                   <div class="row bid-card-wrapper">
                                                            <div class="bid-card" data-card-id="4">
                                                            
                                                                    <div class="bid-card-row redesigned-bid-card ">
                                                                       
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="d-flex justify-content-center gap-3">
                                                      <div class="driver-info">
                                                       <div class="driver-name" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#userModal">Ramesh Kumar</div>
                                                      </div>
                                                      <div class="driver-info">
                                                        <div class="amount-bid">₹100</div>
                                                      </div>
                                                         <!-- Right: Initial Actions -->
                                                            <div class="bid-actions" id="actions-4">
                                                                        <button class="btn btn-success p-2 btn-sm accept-btn" onclick="showAcceptConfirmation(4)" title="Accept">
                                                         <i class="fa-solid fa-check"></i> </button>
                                                                    
                                                                        
                                                                    </div>
                                                                     <!-- Right: After Accepted -->
                                                                  <div class="bid-post-accept d-none" id="post-accept-4">
                                                        <!-- Call icon -->
                                                       <div href="tel:+919999999999" class="icon-circle bg-primary text-white me-2" title="Call">
                                                            <i class="fas fa-phone fa-lg"></i>
                                                        </div>
                                                    
                                                        <!-- WhatsApp icon -->
                                                         <div href="https://wa.me/919999999999" target="_blank" class="icon-circle bg-success text-white me-2" title="WhatsApp">
                                                            <i class="fab fa-whatsapp fa-lg"></i>
                                                        </div>
                                                    
                                                        <!-- Reject button -->
                                                        <button class="btn btn-danger p-2 btn-sm accept-btn" onclick="showRejectModal(4)" title="Reject">
                                                            <i class="fas fa-times-circle"></i> 
                                                        </button>
                                                    </div>
                                                            </div>
                                                            </div>
                                                        
                                                          </div>
                                                                
                                                                   
                                                                </div>

                                                                <!-- Remarks section -->
                                                                <div class=" glassy-remarks mt-2" style="display: none;">
                                                                     <p>Available for immediate dispatch
                                                                        Prefers bypass route</p>
                                                                </div>

                                                                <!-- Flag options -->
                                                                <div class="flag-options stylish-flags mt-2" style="display: none;">
                                                                    <button onclick="reportUser('Ramesh Kumar')"><i class="fas fa-exclamation-circle"></i>
                                                                        Block</button>
                                                                    <button onclick="markAsSpam('Ramesh Kumar')"><i class="fas fa-ban"></i> Block and Report
                                                                        with Spam</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                         <div class="row bid-card-wrapper">
                                                            <div class="bid-card" data-card-id="5">
                                                            
                                                                    <div class="bid-card-row redesigned-bid-card ">
                                                                       
                                                                <div class="col-12">
                                                                    <div class="row">
                                                                        <div class="d-flex justify-content-center gap-3">
                                                                          <div class="driver-info">
                                                                            <div class="driver-name" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#userModal">Ramesh Kumar</div>
                                                                          </div>
                                                                          <div class="driver-info">
                                                                            <div class="amount-bid">₹100</div>
                                                                          </div>
                                                                            <div class="bid-actions" id="actions-5">
                                                                       <button class="btn btn-success p-2 btn-sm accept-btn" onclick="showAcceptConfirmation(5)" title="Accept">
                                                                <i class="fa-solid fa-check"></i> </button>
                                                                      
                                                                        
                                                                    </div>
                                                                     <!-- Right: After Accepted -->
                                                                 <div class="bid-post-accept d-none" id="post-accept-5">
                                                                <!-- Call icon -->
                                                                <div href="tel:+919999999999" class="icon-circle bg-primary text-white me-2" title="Call">
                                                                    <i class="fas fa-phone fa-lg"></i>
                                                                </div>
                                                            
                                                                <!-- WhatsApp icon -->
                                                                 <div href="https://wa.me/919999999999" target="_blank" class="icon-circle bg-success text-white me-2" title="WhatsApp">
                                                                    <i class="fab fa-whatsapp fa-lg"></i>
                                                                </div>
                                                            
                                                                <!-- Reject button -->
                                                                <button class="btn btn-danger p-2 btn-sm accept-btn" onclick="showRejectModal(5)" title="Reject">
                                                                    <i class="fas fa-times-circle"></i> 
                                                                </button>
                                                            </div>
                                                                    </div>
                                                                    </div>
                                                                  
                                                                  </div>
                                                                    

                                                                   
                                                                </div>

                                                                <!-- Remarks section -->
                                                                <div class=" glassy-remarks mt-2" style="display: none;">
                                                                     <p>Available for immediate dispatch
                                                                        Prefers bypass route</p>
                                                                </div>

                                                                <!-- Flag options -->
                                                                <div class="flag-options stylish-flags mt-2" style="display: none;">
                                                                    <button onclick="reportUser('Ramesh Kumar')"><i class="fas fa-exclamation-circle"></i>
                                                                        Block</button>
                                                                    <button onclick="markAsSpam('Ramesh Kumar')"><i class="fas fa-ban"></i> Block and Report
                                                                        with Spam</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`

                                ;
                        })
                    } else {
                        jobs_content = `
                                            <div class="job-empty-card">
              <div class="job-empty-body">
                <i class="fa-solid fa-briefcase text-danger"></i>
                <p>No jobs available</p>
                <div class="d-flex justify-content-center">
                  <button class="btn btn-sm btn-success mt-3" 
                          data-bs-toggle="modal" 
                          data-bs-target="#createJobModal">
                    Create Your Job
                  </button>
                </div>
              </div>
            </div>

                            `;
                    }

                    $('#current').html(jobs_content);
                    // console.log(response.data);
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#con_create");
                btn.html('Update');
            }
        });
    }

    function bidCreate(pname, pcompany, pmobile, prate, pjob, id, pcreate) {
        $('#j_name').text(pname);
        $('#j_company').text(pcompany);
        // console.log
        $('#j_rate').text((prate != 'null' && prate != '') ? prate : '5');
        $('#j_complete').text(pjob + ' Completed');

        let job_time = jobPostTime(pcreate)
        // console.log(job_time)
        $('#j_time').text(`${job_time}`);
        $('#placeBid_btn').attr('onclick', 'placeBid("' + id + '")');
        $('#managebid').modal('show')
    }
    function bidManage(id) {
        // $('#j_name').text(pname);
        // $('#j_company').text(pcompany);
        // // console.log
        // $('#j_rate').text((prate != 'null' && prate != '') ? prate : '5');
        // $('#j_complete').text(pjob + ' Completed');

        // let job_time = jobPostTime(pcreate)
        // // console.log(job_time)
        // $('#j_time').text(`${job_time}`);
        // $('#placeBid_btn').attr('onclick', 'placeBid("' + id + '")');
        $('#managebid').modal('show')
        
        $.ajax({
            url: "{{ env('APP_API') }}get-bid",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $(this);
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Get Bid...');
            },
            success: function (response) {
                if (response.status) {
                    console.log(response.data);
                    
                    $('#managebid').modal('show')

                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $(this);
                btn.prop('disabled', false).html('<i class="fas fa-gavel me-2"></i>Get Bid');
            }
        });
    }

    function placeBid(id) {

        const bidAmount = $('#bidAmount').val();
        const bidRemarks = $('#bidRemarks').val();

        if (!bidAmount || isNaN(bidAmount)) {
            showToast('error', 'Please enter a bid amount before placing your bid.', 3000);
            return;
        }


        if (id == '') {
            showToast('error', 'Job Not Available.', 3000);
            return;

        }

        let formData = new FormData();
        formData.append('job_id', id);
        formData.append('amount', bidAmount);
        formData.append('remark', bidRemarks);
        // Here you would typically send the data to your server
        $.ajax({
            url: "{{ env('APP_API') }}create-bid",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#placeBid_btn");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Biding...');
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message, 3000);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('carModal2'));
                    if (modal) modal.hide();
                    $('.successBidAmount').text('₹' + bidAmount);
                    $('#bidSuccessModal').modal('show');
                    setInterval(function () {
                        location.reload();
                    }, 3000);


                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#placeBid_btn");
                btn.prop('disabled', false).html('<i class="fas fa-gavel me-2"></i>Place Bid');
            }
        });

        // Close the modal (assuming Bootstrap 5)


    }

</script>