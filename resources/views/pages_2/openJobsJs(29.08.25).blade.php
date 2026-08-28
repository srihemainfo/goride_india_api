<script>
    $(document).ready(function () {
        openJobs()
        currentJobs();
        bidStatus();
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
    
    function convertISO(txt){
        let dateObj = new Date(txt.replace(" ", "T")); // Convert to valid ISO
        
        let options = { 
            day: "2-digit", 
            month: "short", 
            hour: "2-digit", 
            minute: "2-digit", 
            hour12: true 
        };
        
        let formatted = dateObj.toLocaleString("en-GB", options).replace(",", "");
        
        return formatted;
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
    
    function maskName(name) {
        let parts = name.trim().split(" ");
        return parts.map(part => {
            
            if (part.length > 1) {
                return part[0] + "*".repeat(Math.max(3, part.length));
            } else {
                return part;
            }
        }).join(" ");
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
            // beforeSend: function () {
            //     let btn = $("#con_create");
            //     btn.prop('disabled', true)
            //         .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            // },
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
                            
                            // if(value.reject_bid == 'yes'){
                            //     return true;
                            // }

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
                                                                    <span class="icon-report me-2 d-none d-md-inline" onclick="toggleLike(this)" style="cursor:pointer;">
                                                                        <i class="fa fa-heart"></i>
                                                                    </span>
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
                                                        <span class="price mb-1">₹ ${value.fare}</span>
                                                            <span class="bids-c ">
                                                                <i class="fas fa-gavel"></i> ${value.bids_count??0} Bids
                                                            </span>
                                                                                                     
                                                        </div>
                 
                                                        
                                                    </div>
                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12 py-1 px-0 col-12">
                                                       
                                            <div class="trip-info">
                                                            <div class="route-section m-2">
                                                                <div class="location-time">
                                                                
                                                                    <div class="location" onclick="toggleAddress(this)">${value.from_place}</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                
                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">${value.job_type != 'oneway' ? 'ROUND TRIP' : 'ONE WAY'}</span>
                                                                        <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        ${value.job_type != 'oneway' ? '<div class="long-arrow bottom-arrow"></div>' : ''} 
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                    <div class="date-time d-none"></div>
                                                                    <div class="location" onclick="toggleAddress(this)">${value.to_place}</div>
                                                                </div>
                  
                                                            </div>
                                                             <div class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                            <!-- Pickup with icon -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                             <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold" 
                                                                        >
                                                                    ${value.pickup_date}
                                                                </strong>
                                                            </span>
                                                        
                                                            <!-- Dropoff only if date exists -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                                ${value.dropoff_date
                                                                ? `<i class="fa-solid fa-plane-arrival text-danger"></i>
                                                                                                   <strong class="fw-bold">
                                                                                                       ${value.dropoff_date}
                                                                                                   </strong>`
                                                                : ''}
                                                            </span>
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
                                                                
                                                                
                                                                <div class=" small text-muted mt-2 position-relative d-md-none" style="border-top: 2px solid #ddd;">
                                                                 <span class="oneway-label">${value.job_type != 'oneway' ? 'ROUND TRIP' : 'ONE WAY'}</span>
                                                              <div class="d-flex justify-content-between py-3">
                                                            <!-- Pickup with icon -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                             <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold" 
                                                                       >
                                                                    ${value.pickup_date}
                                                                </strong>
                                                            </span>
                                                        
                                                            <!-- Dropoff only if date exists -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                                ${value.dropoff_date
                                    ? `<i class="fa-solid fa-plane-arrival text-danger"></i>
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
                                    `<button class="btn btn-sm btn-warning ms-auto agreeBtn ${value.job_status != 'bidding' ? 'd-none' : ''}" onclick="bidManage('${value.id}', this)">
                                                                            <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                        </button>`
                                    :
                                    `<button class="btn btn-sm btn-success ms-auto agreeBtn" onclick="agreeBid('${value.id}', this, '${JSON.parse(value.add_fare_details)}', '${value.fare}')">
                                                                            <i class="fas fa-thumbs-up me-1"></i> Agree
                                                                        </button>`
                                }   
                                                                                            </span>
                                                                    </span>
                                                                    <span class="text-form">
                                                                        <i class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                                        ${expiryTime}
                                                                    </span>
                                                                     ${value.user_bid == 'yes' ? `` :
                                    `<div class="small text-muted mt-2 place-bid-section d-flex justify-content-between align-items-center">
                                                                                                 <span class="icon-report d-md-none" onclick="toggleLike(this)" style="cursor:pointer;">
                                                                                    <i class="fa fa-heart"></i>
                                                                                </span>
                                                                                <div>
                                                                            Need to increase amount?
                                                                            <a href="#"
                                                                                class="text-primary fw-semibold text-decoration-none" onclick="bidCreate('${value.id}', this)" style="font-size: 16px;">
                                                                                Place bid
                                                                            </a>
                                                                        </div>`
                                }
                                                                                            </div>
                                                                                                 <div class="text-end d-none d-md-block" id="actionSection">
                                                                    ${value.user_bid == 'yes' ?
                                    `<button class="btn btn-sm btn-warning ms-auto agreeBtn ${value.job_status != 'bidding' ? 'd-none' : ''}" onclick="bidManage('${value.id}', this)">
                                                                            <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                        </button>`
                                    :
                                    `<button class="btn btn-sm btn-success ms-auto agreeBtn" onclick="agreeBid('${value.id}', this, '${JSON.parse(value.add_fare_details)}', '${value.fare}')">
                                                                            <i class="fas fa-thumbs-up me-1"></i> Agree
                                                                        </button>`
                                }
                                                            
                                                                    ${value.user_bid == 'yes' ? `` :
                                    `<div class="small text-muted mt-2 place-bid-section">
                                                                            Need to increase amount?
                                                                            <a href="#"
                                                                                class="text-primary fw-semibold text-decoration-none" onclick="bidCreate('${value.id}', this)">
                                                                                Place bid
                                                                            </a>
                                                                        </div>`
                                }
                                                                </div>
                                                                </div>
                                                            
                                                                <!-- Right: Action Buttons -->
                                                         
                                                            
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
                    // // console.log(response.data);
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            // complete: function () {
            //     let btn = $("#con_create");
            //     btn.html('Update');
            // }
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
            // beforeSend: function () {
            //     let btn = $("#con_create");
            //     btn.prop('disabled', true)
            //         .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            // },
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
                            
                            let biddersData = '';
                            let after_accept = '';
                            // // console.log('hiiiiiiiiiiiiiiiiii')
                            if(value.bidders.length > 0){
                                
                                $.each(value.bidders, function(ind, bid){
                                let bidder_status = value.bidders.some(bid => bid.status == 'accept');
                                
                                let isDis = !bidder_status ? '' : 'disabled-bid';
                                
                                // let isDis = !bidder_status ? '' : 'disabled-bid';
                                
                                let isAc = !bidder_status ? '' : 'd-none';
                                
                                let b_name = bid.status != 'accept' ? maskName(bid.bidder_name) : bid.bidder_name;
                                
                                let b_email = bid.bidder_name;
                                let b_mobile = bid.bidder_mobile;
                                let b_amount = bid.amount;
                                
                                
                                
                                biddersData += `
                                
                                    <div class="row bid-card-wrapper ${ bid.status == 'accept' ? '' : isDis}" >
                                                          <div class="bid-card col-12" data-card-id="${ind}">
                                                            <div class="bid-card-row redesigned-bid-card">
                                                              <div class="col-12">
                                                                <div class="row driver-list">
                                                                  <div class="d-flex justify-content-center gap-3">
                                                                    <div class="driver-info">
                                                                      <div
                                                                        class="driver-name"
                                                                        style="cursor: pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#userModal"
                                                                      >
                                                                        <div class="masked">${b_name}</div>
                                                                      </div>
                                                                      <div class="driver-info">
                                                                        <div class="amount-bid">₹${b_amount}</div>
                                                                      </div>
                                                        
                                                                      <div class="bid-actions ${isAc}" id="actions-3">
                                                                        <button
                                                                          class="btn btn-success p-2 btn-sm accept-btn"
                                                                          onclick="showAcceptConfirmation('${bid.bidder_id}', '${value.id}', this)"
                                                                          title="Accept"
                                                                        >
                                                                          <i class="fa-solid fa-check"></i>
                                                                        </button>
                                                                      </div>`;
                                                                      
                                                                      if(bid.status == 'accept'){
                                    
                                                                            biddersData += `<div class="bid-post-accept" id="post-accept-3">
                                                                                            <!-- Call icon -->
                                                                                            <div
                                                                                              href="tel:+${b_mobile}"
                                                                                              class="icon-circle bg-primary text-white me-2"
                                                                                              title="Call"
                                                                                            >
                                                                                              <i class="fas fa-phone fa-lg"></i>
                                                                                            </div>
                                                                            
                                                                                            <div
                                                                                              href="https://wa.me/${b_mobile}"
                                                                                              target="_blank"
                                                                                              class="icon-circle bg-success text-white me-2"
                                                                                              title="WhatsApp"
                                                                                            >
                                                                                              <i class="fab fa-whatsapp fa-lg"></i>
                                                                                            </div>
                                                                                            <!-- Reject button -->
                                                                                            <button
                                                                                              class="btn  p-0 btn-sm-danger accept-btn" style="
    font-size: 31px;"
                                                                                              onclick="showRejectModal('${value.id}', '${bid.bidder_id}', this)"
                                                                                              title="Reject"
                                                                                            >
                                                                                              <i class="fas fa-times-circle text-danger"></i>
                                                                                            </button>
                                                                                          </div>`;
                                                                        }
                                                    
                                                                      
                                                          biddersData +=  `</div>
                                                                  </div>
                                                                </div>
                                                              </div>
                                                                </div>
                                                              <!-- Remarks section -->
                                                              <div class="glassy-remarks mt-2" style="display: none">
                                                                <p>Available for immediate dispatch Prefers bypass route</p>
                                                              </div>
                                                        
                                                              <!-- Flag options -->
                                                              <div class="flag-options stylish-flags mt-2" style="display: none">
                                                                <button onclick="reportUser('Ramesh Kumar')">
                                                                  <i class="fas fa-exclamation-circle"></i> Block
                                                                </button>
                                                                <button onclick="markAsSpam('Ramesh Kumar')">
                                                                  <i class="fas fa-ban"></i> Block and Report with Spam
                                                                </button>
                                                              </div>
                                                            </div>
                                                          </div>
                                
                                `;
                            })
                            
                            }else{
                                biddersData = `
                                    
                                    <div class="row bid-card-wrapper">
                                          <div class="col-12">
                                            <div class="bid-card d-flex flex-column justify-content-center align-items-center text-center p-4">
                                              <i class="fas fa-user-slash text-muted mb-2" style="font-size: 2rem;"></i>
                                              <p class="mb-0 text-muted fw-bold">No Bids Available</p>
                                            </div>
                                          </div>
                                    </div>
                                
                                `;
                            }
                            
                            


                            jobs_content +=

                                `<div class="compact-car-card ">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row d-flex justify-content-start align-items-start">
                                                 <div class="car-specs mb-3">
                                                               <div class="row">
                                                                    <!-- Left: Passenger & Distance + Heart at end -->
                                                                    <div class="col-md-6 col-6 d-flex align-items-center gap-1">
                                                                    <span class="icon-report me-2 d-none d-md-inline" onclick="toggleLike(this)" style="cursor:pointer;">
                                                                        <i class="fa fa-heart"></i>
                                                                    </span>
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
                                                        <span class="price mb-1">₹ ${value.fare}</span>
                                                            <span class="bids-c ">
                                                                <i class="fas fa-gavel"></i> ${value.bidders.length} Bids
                                                            </span>
                                                                                                     
                                                        </div>
                 
                                                        
                                                    </div>
                                                    </div>
                                                            </div>
                                                    <div class="col-md-7 col-12 px-0">
                                                        <div class="company-info">
                                                           
                                                        </div>
                                                        <div class="trip-info">
                                                           <div class="route-section m-2">
                                                                <div class="location-time">
                                                                
                                                                    <div class="location" onclick="toggleAddress(this)">${value.from_place}</div>
                                                                </div>
                                                                <div class="route-arrow">
                                                                
                                                                    <div class="arrow-line">
                                                                        <span class="oneway-label d-none d-md-block">${value.job_type != 'oneway' ? 'ROUND TRIP' : 'ONE WAY'}</span>
                                                                        <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        ${value.job_type != 'oneway' ? '<div class="long-arrow bottom-arrow"></div>' : ''} 
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                                    </div>
                                                                </div>
                                                                <div class="location-time">
                                                                   
                                                                    <div class="location" onclick="toggleAddress(this)">${value.to_place}</div>
                                                                </div>
                  
                                                            </div>
                                                            <div class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                            <!-- Pickup with icon -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                             <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold" 
                                                                        >
                                                                    ${value.pickup_date}
                                                                </strong>
                                                            </span>
                                                        
                                                            <!-- Dropoff only if date exists -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                                ${value.dropoff_date
                                    ? `<i class="fa-solid fa-plane-arrival text-danger"></i>
                                                                       <strong class="fw-bold">
                                                                           ${value.dropoff_date}
                                                                       </strong>`
                                    : ''}
                                                            </span>
                                                        </div>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                         <div class=" small text-muted mt-3 position-relative d-md-none" style="border-top: 2px solid #ddd;">
                                                                 <span class="oneway-label">${value.job_type != 'oneway' ? 'ROUND TRIP' : 'ONE WAY'}</span>
                                                              <div class="d-flex justify-content-between mt-3">
                                                            <!-- Pickup with icon -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                             <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold"
                                                                       >
                                                                    ${value.pickup_date}
                                                                </strong>
                                                            </span>
                                                        
                                                            <!-- Dropoff only if date exists -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                                ${value.dropoff_date
                                    ? `<i class="fa-solid fa-plane-arrival text-danger"></i>
                                                                       <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" 
                                                                               data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                           ${value.dropoff_date}
                                                                       </strong>`
                                    : ''}
                                                            </span>
                                                        </div>
                                                        </div>

                                                        </div>
                                                    </div>
                                                   <div class="col-md-4 col-10 position-relative">
                                                                
                                                      
                                                        <!--<div class="d-flex justify-content-center align-items-center" style="font-weight:500">-->
                                                        <!--    <strong>Driver Bids</strong>-->
                                                        <!--    </div>-->
                                                        <div class="scrollable-bid-list" style="">
                                                            
                                                        ${biddersData}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-1 col-2 position-relative">
                                                        <div class="dropdown sort-dropdown  text-end ">
                                                            <button class="btn btn-light dropdown-toggle" type="button" id="sortMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-filter " id="selectedSort"></i>
                                                            </button>
                                                    
                                                            <ul class="dropdown-menu" aria-labelledby="sortMenuButton">
                                                                <li><a class="dropdown-item" href="#" onclick="setActiveSort(this)">Price - Low to High</a></li>
                                                                <li><a class="dropdown-item" href="#" onclick="setActiveSort(this)">Price - High to Low</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                </div>

                                            </div>
                                        </div>
                                    </div>`

                                ;
                                
                                // console.log(jobs_content);
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
                    
                    // console.log(jobs_content)

                    $('#current').html(jobs_content);
                    // // console.log(response.data);
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            // complete: function () {
            //     let btn = $("#con_create");
            //     btn.html('Update');
            // }
        });
    }
    
    function submitJob() {

        let storedData = localStorage.getItem('formData');
        let formData = new FormData();

        if (storedData) {
            let parsedData = JSON.parse(storedData);
            for (let key in parsedData) {
                formData.append(key, parsedData[key]);
            }
        }

        $.ajax({
            url: "{{ env('APP_API') }}create-job",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#con_create");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
            },
            success: function (response) {
                if (response.status) {
                    // $('#jobPreviewModal').modal('hide');
                    showToast('success', response.message, 3000);
                    document.getElementById('journeyForm').reset();
                    localStorage.removeItem('formData');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#con_create");
                btn.html('<i class="fas fa-check-circle me-1"></i> Confirm & Create');
            }
        });



        // Close the preview modal

        // Optional: Reset the form
    }
    
    function bidCreate(id, btn) {
        
        if(id != ''){
            
            let formData = new FormData();
            formData.append('job_id', id);
            formData.append('method', 'create');
            
            $.ajax({
                url: "{{ env('APP_API') }}get-jobinfo",
                type: 'POST',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(btn).prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Get Bid...');
                },
                success: function (response) {
                    if (response.status) {
                        let data = response.data;
                        
                        $('#j_name').text(data.poster_name??'');
                        $('#j_company').text(data.poster_company??data.poster_name + ' Travels');
                        $('#j_rate').text((data.poster_ratings != null && data.poster_ratings != '') ? data.poster_ratings : '5');
                        $('.j_complete').text(data.poster_complete_jobs + ' Jobs Completed');
                        
                        $('#pre_from4').text(data.from_place);
                        $('#pre_to4').text(data.to_place);
                        let converti = convertISO(data.pickup_date)
                        $('#m_pickup').text(converti);
                
                        let job_time = jobPostTime(data.created_at)
                        $('.j_time').text(`${job_time}`);
                        
    
                        $('#placeBid_btn').attr('onclick', 'placeBid("' + data.id + '")');
                        
                        $('#carModal2').modal('show');
    
                    } else {
                        showToast('error', response.message, 3000);
                    }
                },
                error: function () {
                    showToast('error', 'Something went wrong!', 3000);
                },
                complete: function () {
                    $(btn).prop('disabled', false).html('Place Bid');
                }
            });
            
        }
    }
    
    function bidManage(id, btn) {
        
        // $('#managebid').modal('show')
        if(id != ''){
            
            let formData = new FormData();
            formData.append('job_id', id);
            formData.append('method', 'manage');
            
            $.ajax({
                url: "{{ env('APP_API') }}get-jobinfo",
                type: 'POST',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(btn).prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Get Bid...');
                },
                success: function (response) {
                    if (response.status) {
                        let data = response.data;
                        $('#m_name').text(data.poster_name??'');
                        $('#m_company').text(data.poster_company??data.poster_name + ' Travels');
                        $('#m_rate').text((data.poster_ratings != null && data.poster_ratings != '') ? data.poster_ratings : '5');
                        $('#m_complete').text(data.poster_complete_jobs + ' Jobs Completed');
                        
                        $('#pre_from3').text(data.from_place);
                        $('#pre_to3').text(data.to_place);
                        let converti = convertISO(data.pickup_date)
                        $('#m_pickup').text(converti);
                
                        let job_time = jobPostTime(data.created_at);
                        // // console.log(job_time)
                        $('#m_time').text(`${job_time}`);
                        
                        $('#manageBidAmount').val(data.bidders? data.bidders[0].amount : '');
                        $('#manageBidRemarks').val(data.bidders? data.bidders[0].remark : '');
    
                        $('#manageBit_btn').attr('onclick', 'confirmBidPlacement("' + data.id + '")');
                        
                        $('#managebid').modal('show');
    
                    } else {
                        showToast('error', response.message, 3000);
                    }
                },
                error: function () {
                    showToast('error', 'Something went wrong!', 3000);
                },
                complete: function () {
                    $(btn).prop('disabled', false).html('<i class="fas fa-gavel me-2"></i> Manage Bid');
                }
            });
            
        }
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
    
    function confirmBidPlacement(id) {

        const bidAmount = $('#manageBidAmount').val();
        const bidRemarks = $('#manageBidRemarks').val();
        
        // console.log(bidAmount, bidRemarks);

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
                let btn = $("#manageBit_btn");
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
                let btn = $("#manageBit_btn");
                btn.prop('disabled', false).html('<i class="fas fa-gavel me-2"></i>Update Bid');
            }
        });

        // Close the modal (assuming Bootstrap 5)


    }
    
    function agreeBid(id, btn, details, fare){
        
        $('#bidAmountDisplay').text('₹ ' + fare);
        
        $('.successBidAmount').text('₹ ' + fare);
        
        let bata = details.bata == 'Included' ? 'Included' : 'Passenger To Pay';
        let toll = details.toll == 'Included' ? 'Included' : 'Passenger To Pay';
        let parking = details.parking == 'Included' ? 'Included' : 'Passenger To Pay';

        let add_details = 'Toll ' + toll + ', Parking ' + parking + ', Bata ' + bata;
        $('#bidAmountDetails').text(add_details);
        
        $('#confirmBidBtn').attr('onclick', 'confirmAgree("' + id + '")');
        
        $('#agreedModal').modal('show')
    }
    
    function confirmAgree(id) {

        if (id == '') {
            showToast('error', 'Job Not Available.', 3000);
            return;

        }

        let formData = new FormData();
        formData.append('job_id', id);
        
        // Here you would typically send the data to your server
        $.ajax({
            url: "{{ env('APP_API') }}agree_job",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#confirmBidBtn");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message, 3000);
                    $('#agreedModal').modal('hide');

                    $('#confirmedBidModal').modal('show');
                    
                    setInterval(function () {
                        location.reload();
                    }, 2000);


                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#confirmBidBtn");
                btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Confirm');
            }
        });



    }
    
    function showAcceptConfirmation(id, job_id, btn) {
        
        let formData = new FormData();
        formData.append('job_id', job_id);
        formData.append('method', 'bidder');
        formData.append('bidder_id', id);
        
        $.ajax({
                url: "{{ env('APP_API') }}get-jobinfo",
                type: 'POST',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(btn).prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                },
                success: function (response) {
                    if (response.status) {
                        let data = response.data;
                        
                        $('#confirmAcceptBtn').attr('onclick', 'acceptJob("' + data.id + '", "'+ id +'")');
                        
                        data = response.data.bidders.length > 0 ? response.data.bidders[0] : response.data;
                        
                        $('#b_name').text(data.bidder_name??'');
                        $('#b_company').text(data.bidder_company??data.bidder_name + ' Travels');
                        $('#b_rate').text((data.bidder_ratings != null && data.bidder_ratings != '') ? data.bidder_ratings : '5');
                        $('#b_complete').text(data.bidder_complete_jobs + ' Jobs Completed');
                        
                        $('#b_amount').val(data.amount??'');
                        $('#b_remark').val(data.remark??'');
                        
                        $('#acceptBidModal').modal('show');
    
                    } else {
                        showToast('error', response.message, 3000);
                    }
                },
                error: function () {
                    showToast('error', 'Something went wrong!', 3000);
                },
                complete: function () {
                    $(btn).prop('disabled', false).html('<i class="fa-solid fa-check"></i>');
                }
            });
    }
    
    function showRejectModal(job_id, id, btn) {
        
        let formData = new FormData();
        formData.append('job_id', job_id);
        formData.append('method', 'reject');
        formData.append('bidder_id', id);
        
        $.ajax({
                url: "{{ env('APP_API') }}get-jobinfo",
                type: 'POST',
                headers: {
                    "Authorization": "Bearer " + getCookie('sessionToken')
                },
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $(btn).prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                },
                success: function (response) {
                    if (response.status) {
                        let data = response.data;
                        
                        $('#confirmRejectBtn').attr('onclick', 'rejectJob("' + data.id + '", "'+ id +'")');
                        
                        data = response.data.bidders.length > 0 ? response.data.bidders[0] : response.data;
                        
                        $('#r_name').text(data.bidder_name??'');
                        $('#r_company').text(data.bidder_company??data.bidder_name + ' Travels');
                        $('#r_rate').text((data.bidder_ratings != null && data.bidder_ratings != '') ? data.bidder_ratings : '5');
                        $('#r_complete').text(data.bidder_complete_jobs + ' Jobs Completed');
                        
                        $('#r_amount').val(data.amount??'');
                        $('#r_remark').val(data.remark??'');
                        
                        $('#rejectConfirmModal').modal('show');
    
                    } else {
                        showToast('error', response.message, 3000);
                    }
                },
                error: function () {
                    showToast('error', 'Something went wrong!', 3000);
                },
                complete: function () {
                    $(btn).prop('disabled', false).html('<i class="fas fa-times-circle"></i>');
                }
            });
    }
    
    function acceptJob(id, bidder){
        
        if (id == '') {
            showToast('error', 'Job Not Available.', 3000);
            return;

        }
        if (bidder == '') {
            showToast('error', 'Bidder Not Available.', 3000);
            return;

        }

        let formData = new FormData();
        formData.append('job_id', id);
        formData.append('bidder_id', bidder);
        
        $.ajax({
            url: "{{ env('APP_API') }}accept-job",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#confirmAcceptBtn");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message, 3000);
                    
                    setInterval(function () {
                        location.reload();
                    }, 2000);


                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#confirmAcceptBtn");
                btn.prop('disabled', false).html('Yes, Accept');
            }
        });
        
    }
    
    function rejectJob(id, bidder){
        
        if (id == '') {
            showToast('error', 'Job Not Available.', 3000);
            return;

        }
        if (bidder == '') {
            showToast('error', 'Bidder Not Available.', 3000);
            return;

        }

        let formData = new FormData();
        formData.append('job_id', id);
        formData.append('bidder_id', bidder);
        
        $.ajax({
            url: "{{ env('APP_API') }}reject-job",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                let btn = $("#confirmRejectBtn");
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message, 3000);
                    $('#rejectConfirmModal').modal('hide');
                    setInterval(function () {
                        location.reload();
                    }, 2000);


                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            },
            complete: function () {
                let btn = $("#confirmRejectBtn");
                btn.prop('disabled', false).html('Yes, Reject');
            }
        });
        
    }
    
    function bidStatus(){
        
        $.ajax({
            url: "{{ env('APP_API') }}bidding-status",
            type: 'POST',
            headers: {
                "Authorization": "Bearer " + getCookie('sessionToken')
            },
            data: [],
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status) {
                    let data = response.data;
                    
                    $('#bid-status').empty();
                    let bid_status = '';
                    $.each(data, function(ind, value){
                        
                        let bid_length = value.bids_details ? Object.keys(JSON.parse(value.bids_details)).length : 0;

                        let ribbonHtml = '';
                                        
                        if (value.user_bid_status === 'accept') {
                            ribbonHtml = '<div class="ribbon"><span class="completed">Won</span></div>';
                        } else if (value.user_bid_status === 'reject') {
                            ribbonHtml = '<div class="ribbon"><span class="cancelled">Lose</span></div>';
                        } else if (value.user_bid_status === 'direct' || value.user_bid_status === 'inreview') {
                            ribbonHtml = '<div class="ribbon"><span class="no-response">In Review</span></div>';
                        }
                        
                        value.pickup_date = convertDateFormat(value.pickup_date);
                        value.dropoff_date = value.dropoff_date ? '<i class="fa-solid fa-plane-arrival text-danger"></i>'+convertDateFormat(value.dropoff_date) : '';
                        
                        bid_status += `
                        
                        <div class="job-card mb-3">
                                    ${ ribbonHtml }

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="company-info">
                                                    <div class="car-specs">
                                                        <div class="row mt-3">
                                                            <!-- Left: Passenger & Distance + Heart at end -->
                                                            <div class="col-md-6 col-7 d-flex align-items-center gap-1">

                                                                <span
                                                                    class="passenger-count d-flex align-items-center gap-1 ms-5">
                                                                    <i class="fas fa-users"></i> ${value.pass_count}
                                                                </span>
                                                                <span class="distance d-flex align-items-center gap-1">
                                                                    <i class="fas fa-route"></i> ${value.distance} km
                                                                </span>

                                                            </div>

                                                            <!-- Right: Amount & Description -->
                                                            <div
                                                                class="col-md-6 col-5 d-flex justify-content-end align-items-end">
                                                                <div
                                                                    class="amount d-flex flex-column justify-content-end align-items-end">
                                                                    <span class="price mb-1">₹ ${value.user_bid_amount}</span>
                                                                    <span class="bids-c ">
                                                                        <i class="fas fa-gavel"></i> ${bid_length} Bids
                                                                    </span>

                                                                </div>


                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 py-1 col-12">

                                                <div class="trip-info">
                                                    <div class="route-section m-3 p-1">
                                                        <div class="location-time">

                                                            <div class="location" onclick="toggleAddress(this)">${value.from_place}
                                                            </div>
                                                        </div>
                                                        <div class="route-arrow">

                                                            <div class="arrow-line">
                                                                <span class="oneway-label d-none d-md-block">${value.job_type != 'oneway' ? 'ROUND TRIP' : 'ONE WAY'}</span>
                                                                <div class="arrow-container">
                                                                    <!-- For double arrows (stacked version) -->
                                                                    <div class="arrow-stack">
                                                                        <div class="long-arrow top-arrow"></div>
                                                                        ${value.job_type != 'oneway' ? '<div class="long-arrow bottom-arrow"></div>' : ''} 
                                                                    </div>
                                                                    <i class="fas fa-car car-icon"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="location-time">
                                                            <div class="date-time"></div>
                                                            <div class="location " onclick="toggleAddress(this)">${value.to_place}
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex justify-content-between mt-3 d-none d-md-flex">
                                                        <!-- Pickup with icon -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">
                                                            <i class="fa-solid fa-plane-departure text-success"></i>
                                                            <strong class="fw-bold">
                                                                ${value.pickup_date}
                                                            </strong>
                                                        </span>

                                                        <!-- Dropoff only if date exists -->
                                                        <span class="d-inline-flex align-items-center gap-1 date-time">
                                                            ${value.dropoff_date}
                                                        </span>
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
                                                                            Kavi
                                                                        </strong>
                                                                    </span>
                                                                </div>
                                                            
                                                                <!-- Center: Expiry + Icons (Desktop Only) -->
                                                                <div class="d-md-flex d-inline-block align-items-center justify-content-center gap-3 mb-2 d-none d-md-block">
                                                                    <span class="text-form">
                                                                        <i class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                                        Expiry in 1 days and 0 hours
                                                                    </span>
                                                                </div>
                                                            
                                                                <!-- Mobile View: Posted + Expiry in one row -->
                                                                
                                                                
                                                                <div class=" small text-muted mt-2 position-relative d-md-none" style="border-top: 2px solid #ddd;">
                                                                 <span class="oneway-label">ROUND TRIP</span>
                                                              <div class="d-flex justify-content-between py-3">
                                                            <!-- Pickup with icon -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                             <i class="fa-solid fa-plane-departure text-success"></i>
                                                                <strong class="fw-bold">
                                                                    27 Aug 04:15 PM
                                                                </strong>
                                                            </span>
                                                        
                                                            <!-- Dropoff only if date exists -->
                                                            <span class="d-inline-flex align-items-center gap-1 date-time">
                                                                <i class="fa-solid fa-plane-arrival text-danger"></i>
                                                                       <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                           29 Aug 02:15 PM
                                                                       </strong>
                                                            </span>
                                                        </div>


                                                                    <span class="d-flex align-items-center gap-1">
                                                                        <i class="fa-solid fa-badge-check text-success"></i>
                                                                        Posted by
                                                                        <strong class="fw-bold" style="cursor:pointer; text-decoration: underline;" data-bs-toggle="modal" data-bs-target="#postedModal">
                                                                            Kavi
                                                                        </strong>
                                                                        <span class="ms-auto" id="actionSection">
                                                                              <button class="btn btn-sm btn-warning ms-auto agreeBtn " onclick="bidManage('23', this)">
                                                                            <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                        </button>   
                                                                                            </span>
                                                                    </span>
                                                                    <span class="text-form">
                                                                        <i class="fas fa-hourglass-end text-danger hourglass-rotate"></i>
                                                                        Expiry in 1 days and 0 hours
                                                                    </span>
                                                                     
                                                                                            </div>
                                                                                                 <div class="text-end d-none d-md-block" id="actionSection">
                                                                    <button class="btn btn-sm btn-warning ms-auto agreeBtn " onclick="bidManage('23', this)">
                                                                            <i class="fas fa-gavel me-2"></i> Manage Bids
                                                                        </button>
                                                            
                                                                    
                                                                </div>
                                                                </div>

                                        </div>
                                        
                                    </div>
                                </div>
                        
                        `;
                    })
                    
                    $('#bid-status').html(bid_status)
    
                } else {
                    showToast('error', response.message, 3000);
                }
            },
            error: function () {
                showToast('error', 'Something went wrong!', 3000);
            }
        });
    }

</script>