@extends('dashboard-layout.index')
@section('content')
<div class="col-sm-9 mx-4 main-card mb-3 card d-none">
    <div class="card-header">
        <h4 class="card-title mr-4">Car Fares List</h4>
    </div>
    <div class='row d-none'>
        <div class="col-sm-2 mt-2">
            <input class='form-control' id='start' oninput='validateInputInteger(this)' value='1' disabled>
        </div>
        <div class="col-sm-2 mt-2">
            <input class='form-control' oninput='validateInputInteger(this)' id='end' value>
        </div>
        <div class="col-sm-2 mt-2">
            <button id='addButton' class='btn btn-success'>AddButton</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm_old'>
                <table id="data-table" class="table" width="100%">
                    <thead id='header_container'>
                        <!--<tr id="table-header"></tr>-->
                    </thead>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <!--<button id='update-all-btn' class='btn btn-success'>Update</button>-->
            </div>
        </div>
    </div>



    <!-- <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table" class="table" width="100%">
                    <div class="card-header">
                        <h4 class="card-title mr-4">Car Fares List</h4>
                    </div>
                    <tbody id="table-body"></tbody>
                    <tbody id="table-second-body"></tbody>
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success'>Update</button>
            </div>
        </div>
    </div> -->



</div>




<div class="col-sm-9 mx-4 main-card mb-3 card">
    <div class="card-header">
        <h4 class="card-title mr-4">Car Fares List</h4>
    </div>
    <!--<div class='row d-none'>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <input class='form-control' id='start' oninput='validateInputInteger(this)' value='1' disabled>-->
    <!--    </div>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <input class='form-control' oninput='validateInputInteger(this)' id='end' value>-->
    <!--    </div>-->
    <!--    <div class="col-sm-2 mt-2">-->
    <!--        <button id='addButton' class='btn btn-success'>AddButton</button>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="card-body">
        <div class="table-responsive">
            <form id='carFareForm'>
                <table id="data-table_new" class="table" width="100%">
                   
                </table>
            </form>
            <div class='text-center'>
                <button id='update-all-btn' class='btn btn-success'>Update</button>
            </div>
        </div>
    </div>




</div>



<div class="col-sm-2 main-card mb-3 card d-none d-lg-block position">
    <div class="nav flex-column nav-tabs nav-tabs-right h-100" id="vert-tabs-right-tab" role="tablist"
        aria-orientation="vertical">
        <!--<a class="nav-link  text-light" id="vert-tabs-right-home-tab" href="/general" role="tab" aria-controls="vert-tabs-right-home" aria-selected="true" style="cursor: pointer; background-color: #343a40;">-->
        <!--  <i class="fas fa-info-circle" style="margin-right: 8px;"></i> General-->
        <!--</a>-->
        <a class="nav-link active text-light" id="vert-tabs-right-offer-times-tab" href="/carfares" role="tab"
            aria-controls="vert-tabs-right-profile" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-indian-rupee-sign" style="margin-right: 8px;"></i>Fare
        </a>
        <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/locationrange" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Zones
        </a>
        <a class="nav-link  text-light" id="vert-tabs-right-offer-days-tab" href="/area" role="tab"
            aria-controls="vert-tabs-right-messages" aria-selected="false" style="cursor: pointer;">
            <i class="fas fa-vector-square" style="margin-right: 8px;"></i> Area
        </a>
        <!--<a class="nav-link active text-light" id="vert-tabs-right-promo-code-tab" href="/locationrange" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-globe" style="margin-right: 8px;"></i> Zone-->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/paymentoption" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-wallet" style="margin-right: 8px;"></i> Payment Options-->
        <!--</a>-->
        <!--<a class="nav-link  text-light" id="vert-tabs-right-notification-tab" href="/bookingrestriction" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> Booking Restriction Date -->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/googlecallender" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fab fa-google" style="margin-right: 8px;"></i> Google Calendar-->
        <!--</a>-->
        <!--<a class="nav-link text-light" id="vert-tabs-right-notification-tab" href="/review" role="tab" aria-controls="vert-tabs-right-settings" aria-selected="false" style="cursor: pointer;">-->
        <!--  <i class="fas fa-star" style="margin-right: 8px;"></i> Review-->
        <!--</a>-->
    </div>
</div>
<style>
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


<script>
    
    const getFareList = () => {
    try {
        $.ajax({
            url: '{{ env('API_URL') }}getCarFare',
            method: 'POST',
            data: {
                token: getCookie('d_token')
            },
            success: function(response) {
                if (response.status === 200) {
                    
                    localStorage.setItem('careNameList', JSON.stringify(response.data.careNameList));
                    localStorage.setItem('fareList', JSON.stringify(response.data.fareList));

                
                    buildHTM();
                } else {
                    showToast('error', 'Fare details collection process failed!', 5000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
}

const buildHTM = () => {
    try {
      
        const careNameList = JSON.parse(localStorage.getItem('careNameList'));
        const fareList = JSON.parse(localStorage.getItem('fareList'));

        if (!careNameList || !fareList) {
            // console.error('Data not found in localStorage');
            
             showToast('error', 'Data not found!', 5000);
            return;
        }

        $(`#data-table_new`).html('');

        let BuildHtm = `<thead>
            <tr>
                <th scope="col">Start</th>
                <th scope="col">End</th>`;

        careNameList.forEach(careName => {
            BuildHtm += `<th scope="col">${_.capitalize(careName.replace('_', ' '))}</th>`;
        });

        BuildHtm += `<th scope="col">Action</th>`;
        BuildHtm += `</tr></thead><tbody>`;


const nonReadonlyRowIndex  = fareList.length - 2;

         fareList.forEach((e, i) => {
             
             
             
            BuildHtm += `<tr>
        <td>
    <input type="text" 
           class="form-control" 
           name="start_${i + 1}" 
           id="start[]" 
           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
           maxlength="10" 
           value="${fareList[i - 1] && fareList[i - 1].end ? fareList[i - 1].end : e.start}" 
           readonly />
</td>

        <td>
    <input type="text" 
           class="form-control" 
           id="end_${i + 1}" 
           name="end[]" 
           ${i !== nonReadonlyRowIndex ? 'readonly' : 'onblur="_changeVal(${i}, this.value)"'}
           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
           maxlength="10" 
           value="${e.end}" 
            />
</td>

`;

            careNameList.forEach((careName, index) => {
                BuildHtm += `<td>
                    <input type="text" 
                           class="form-control" 
                           id="${careName}_${i + 1}" 
                           name="${careName}[]"  
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                           maxlength="10" 
                           value="${e[careName]}" />
                </td>`;
            });

           BuildHtm += `<td>`;

if (i !== 0 && i !== fareList.length - 1) {
    BuildHtm += i === fareList.length - 2 
        ? `<a class="btn btn-primary btn-sm" onclick="_incVal(${i}, true)">Add</a>` 
        : `<a class="btn btn-danger btn-sm" onclick="_incVal(${i})">Remove</a>`;
}

BuildHtm += `</td>`;
            
          

            BuildHtm += `</tr>`;
        });

        BuildHtm += `</tbody>`;

        $(`#data-table_new`).html(BuildHtm);
    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
}

const _changeVal = (i, newValue) => {
    try {
        let fareList = JSON.parse(localStorage.getItem('fareList')) || [];
        
      
        const parsedValue = parseInt(newValue, 10);

        if (isNaN(parsedValue) || parsedValue <= 0) {
          
            showToast('error', 'Please enter a valid number greater than 0', 5000);
            return;
        }

        if (fareList[i]) {
            fareList[i].end = parsedValue;  

           
            localStorage.setItem('fareList', JSON.stringify(fareList));
        }

    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
};



const _incVal = (i, isIncrement = false) => {
    try {
        let fareList = JSON.parse(localStorage.getItem('fareList')) || [];
        let careNameList = JSON.parse(localStorage.getItem('careNameList')) || [];

        if (isIncrement) {
            const previousRow = fareList[i] || { start: "", end: "" };

            const newRow = {
                start: previousRow.end || "",
                end: (previousRow.end + 10) || "",
            };

            careNameList.forEach(careName => {
                newRow[careName] = previousRow[careName] || "";
            });

            fareList.splice(i + 1, 0, newRow); // Insert at next index

            localStorage.setItem('fareList', JSON.stringify(fareList));
            localStorage.setItem('careNameList', JSON.stringify(careNameList));

            i = i + 1;  // Set index to the new row's index
        } else {
            fareList.splice(i, 1); // Remove the row at index `i`
            localStorage.setItem('fareList', JSON.stringify(fareList));
            localStorage.setItem('careNameList', JSON.stringify(careNameList));
        }

        // console.log(fareList, i);
        buildHTM();
    } catch (e) {
        console.error(`Error: ${e.message}`);
    }
};







    (() => {
    getFareList();
})();

    
    
    
</script>

@endsection
@section('custom_scripts')
@include('dynamiccarfare.partials.carfares_js_new')
@endsection