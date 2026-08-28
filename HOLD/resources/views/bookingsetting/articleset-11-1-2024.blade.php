@extends('dashboard-layout.index')

@section('content')

<style>
    .nav-tabs {

        border: none;

    }
    
    
    tbody th, table.dataTable tbody td {
    /*padding: 3px 3px;*/
    border: 1px solid #ddd !important;
}


.btn-success {
    color: #fff !important;
    background-color: #5cb85c !important;
    border-color: #4cae4c !important;
}

.btn-danger {
    color: #fff !important;
    background-color: #d9534f !important;
    border-color: #d43f3a !important;
}

.btn-default {
    color: #333 !important;
    background-color: #fff !important;
    border-color: #ccc !important;
}


textarea.textareaRead {
    height: 100px;
    padding: 12px 20px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    background-color: #f8f8f8;
    font-size: 16px;
    resize: none;
}

    textarea {
       resize: none;
    }

    .dropdown-list {

        max-height: 200px;

        overflow-y: auto;

        border: 1px solid #ccc;

        display: none;

        position: absolute;

        background-color: white;

        width: 93%;

        z-index: 1000;

    }

    .dropdown-list.active {

        display: block;

    }

    .dropdown-item {

        padding: 8px;

        cursor: pointer;

    }

    .dropdown-item:hover {

        background-color: #f0f0f0;

    }

    [id="dropdown"] {

        width: 94% !important;

    }

    .form-select {



        color: #000;

    }

    .arrow-none {

        background-image: none !important;

    }

    .form-control {

        color: #000;

    }



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

    .card {
        border-radius: 10px;
    }
    
    
   
</style>


<link rel="stylesheet" href="{{ asset('assets/css/rte_theme_default.css') }}" />



<div class="row row-sm ">

    <div class="col-lg-12 mb-3" role="main">
        
        <div class="card ">
            
            <div class="card-header">
                <!--<h3 class="p-2">Article Settings</h3>-->
                   <div class="col-lg-4">
                 <h3 class="card-title" id="fromTitleArt"><strong>Create article</strong></h3>
                 
                 </div> <div class="col-lg-8">
                  </div>
            </div>
            
            <div class="card-body">
               
               
               <div class="new-form-container m-3">
                   
                     <!--<h2 class="">Create article</h2>-->
                
                
                <div class="form-group row">
                    <div class="col-10">
                        <label for="urlType">Select Website</label><span style="color:red;">&nbsp;*</span>
                        <select class="form-control" id="selectType" name="selectType" required>
                            <option value="" disabled selected>Select Website</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="urlType" class="d-block">&nbsp;</label></span>
                        <!--<button class="btn btn-outline-primary" onclick="window.location.href="/general""><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</button>-->
                        <button class="btn btn-outline-primary w-100" onclick="window.location.href='/general'">
    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add
</button>

                    </div>
                </div>

                <div class="form-group">
                    <label for="url">Article URL</label><span style="color:red;">&nbsp;*</span>
                    <input type="url" class="form-control" id="url" name="url" placeholder="Enter article URL"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_ ]/g, '');" maxlength="70" required>
                </div>

                <!-- Meta Title Field -->
                <div class="form-group">
                    <label for="meta-title">Meta Title</label><span style="color:red;">&nbsp;*</span>
                    <input type="text" class="form-control" id="meta-title" name="meta-title"
                        placeholder="Enter meta title"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="150" required>
                </div>

                <!-- Meta Description Field -->
                <div class="form-group">
                    <label for="meta-description">Meta Description</label><span style="color:red;">&nbsp;*</span>
                    <textarea class="form-control" id="meta-description" name="meta-description" rows="4"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="500"
                        placeholder="Enter meta description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="meta-keyword">Meta Keyword</label><span style="color:red;">&nbsp;*</span>
                    <textarea class="form-control" id="meta-keyword" name="meta-keyword" rows="4"
                        oninput="this.value = this.value.replace(/[^A-Za-z0-9-_!., ]/g, '');" maxlength="500"
                        placeholder="Enter meta keyword" required></textarea>
                </div>

                <!-- Content Summary Field -->
                <div class="form-group">
                    <label for="content-summary">Content Summary</label><span style="color:red;">&nbsp;*</span>
                    <!-- <textarea class="form-control" id="content-summary" name="content-summary" rows="5"
                            placeholder="Enter content summary" required></textarea> -->


                    <div id="content-summary">

                    </div>

                </div>

                <!-- Submit Button -->
                <button type="submit" id="firstBtnSub" class="btn btn-primary" onclick="saveContent()">Save
                    Content</button>
         
         
         
               </div>
            
         
         
         
         
         
         
         
         
         
         
            </div>
            
        </div>

    </div>
    
    <div class="col-lg-12 mb-3">
        <div class="card ">
            <div class="card-header">
               
               
                <div class="col-lg-4">
                    <h3 class="card-title"><strong>Article List</strong></h3>
                </div>
              
              
                <div class="col-lg-8">
                    
                                        <div class="col-sm-12 col-md-6 col-lg-4 mt-4">
                                            <button type="button" class="btn btn-primary" id="searchBTN"
                                                onclick="unsubscribeList();">GO</button>
                                        </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered text-nowrap border-bottom"
                                                    id="Participation_List" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="column_sort sorting sorting_asc">URL
                                                            </th>
                                                            <th class="column_sort sorting sorting_asc">Meta Title</th>
                                                            <th class="column_sort sorting sorting_asc">Meta Keyword</th>
                                                            <th class="column_sort sorting sorting_asc">Meta Description</th>
                                                            <th class="column_sort sorting sorting_asc">Created At</th>
                                                            
                                                              <th class="column_sort sorting sorting_asc">Action</th>
                                                            <!--<th class="column_sort sorting sorting_asc">Plan Type</th>-->
                                                            <!--<th class="column_sort sorting sorting_asc">Name</th>-->
                                                            <!--<th class="column_sort sorting sorting_asc">Mobile</th>-->
                                                            <!--<th class="column_sort sorting sorting_asc">Email</th>-->
                                                            <!--<th class="column_sort sorting sorting_asc">Createdon</th>-->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <!-- <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot> -->
                                                </table>
                </div>
            </div>
        </div>
    </div>

</div>





<script type="text/javascript" src="{{ asset('assets/js/rte.js') }}"></script>
<!-- <script>RTE_DefaultConfig.url_base = 'richtexteditor'</script> -->
<script type="text/javascript" src='{{ asset('assets/js/all_plugins.js') }}'></script>



<!-- .js -->
<script>
    var editor1 = new RichTextEditor('#content-summary');
    
    let colbalgaData = [];
    
    
    
const createDatePicker = (id) => {
        try {
            const selector = `#${id}`;
            const today = moment();
            const sevenDaysAgo = moment().subtract(0, 'days');
            $(selector).daterangepicker({
                autoUpdateInput: true,
                locale: {
                    cancelLabel: 'Clear'
                },
                // minDate: sevenDaysAgo, 
                maxDate: today,
                opens: 'left',
                startDate: sevenDaysAgo,
                endDate: today,
                // maxSpan: {
                //     days: 30
                // },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            });
            $(selector).on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                // $(`#drawID`).val('');
                // $(`#drawID`).val('').selectpicker('refresh');
            });
            $(selector).on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    
    
    
    
    const editForm = (art) => {
        try {
            
            if(colbalgaData.length < 1){
              showToast('error', 'Details not found. Please refresh and try again!', 5000);
             return false;   
            }
            
            const fi = colbalgaData.find(item => item.id === art);

if (fi) {
    
    $(`#selectType`).val(fi.gentralID);
    
    $(`#url`).val(fi.url);
    
        $(`#meta-title`).val(fi.meta_title);
    
    
         $(`#meta-description`).val(fi.meta_desp);
         
         
                  $(`#meta-keyword`).val(fi.meta_keyword);
    
    editor1.setHTMLCode(fi.description);
    
    
    $(`#fromTitleArt`).html(`<strong>Edit article</strong>`);
    
    
        $(`#firstBtnSub`).attr(`onclick`, `saveContent(${art})`).text(`Update content`);
 
 
 
  $("html, body").animate({ scrollTop: 0 }, 500);
} else {
    // console.log('Item with ID not found');
    
    showToast('error', 'Details not found. kindly refersh and try again!', 5000);
    
    return false;
    
}
            
            
            
            
            // console.log(art);
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    } 
    
    
    
    const handleToggleChange = (id, checkbox) => {
        try {
            const status = checkbox.checked ? 'active' : 'inactive';
          if (!id) {
              
                showToast('error', 'Details not found. kindly refersh and try again!', 5000);
              return false;
                
            }
            
            
            var h = new FormData();
            // var h = formDataObject;

            // Append the data to the FormData object
            // h.append('_token', '{{ csrf_token() }}');
            
            
            
             h.append('status', status);
            // h.append('content_summary', content);
            // h.append('url', url);
            // h.append('title', title);
            // h.append('description', description);
            // h.append('keyword', keyword);
            h.append('token', getCookie('d_token'));
            
            h.append('id', id)
            
            
            // h.append('device_id', device_id ?? 0);
            // formDataObject[''] = token;
            // formDataObject['device_id'] = device_id;



            $.ajax({
                // url: url,
                url:  '{{ env('API_URL') }}save-article',
                type: 'POST',
                data: h,
                beforeSend: function () {

                    // Button Loading

                    // btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);

                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 200) {
                            
                            
                            // if()

                                        showToast('success', response.message, 5000);
                                        

                                    setTimeout(function() {
                                        location.reload();
                                    }, 5000);

                        

                        } else {

                            // $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 

                    // btn.html(`Save Content`).prop('disabled', false);
                },
                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    // btn.html(`Save Content`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },

                processData: false,
                contentType: false
            });
            
            // console.log(status);
            
            
     
    } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
    
    
    const unsubscribeList = () => {
    try {
        let startDate = null;
        let endDate = null;
        const btn = $('#searchBTN');
        const dateFilter = $('#datefilterLogin').val();
        
        if (dateFilter && dateFilter !== '') {
            startDate = moment($('#datefilterLogin').data('daterangepicker').startDate).format("YYYY-MM-DD");
            endDate = moment($('#datefilterLogin').data('daterangepicker').endDate).format("YYYY-MM-DD");
        }

        const table = $('#Participation_List').DataTable({
            destroy: true,
            pageLength: 10,
            order: [],
            paging: true,
            searching: true,
            info: true,
            ajax: {
                url: '{{ env('API_URL') }}getArticleList',
                method: "POST",
                dataSrc: function (response) {
                    if (response.status === 200) {
                        colbalgaData = response.data;
                    } 
                    
                    
                    return response.data;
                }
                
                ,
                data: {
                    dateFilter,
                    startDate,
                    endDate,
                    searchTxt: $('#searchTxt').val(),
                    statusVal: $('#statusVal').val(),
                    statusPlan: $('#statusPlan').val(),
                    token: getCookie('d_token')
                },
                // success: function (response) {
                //     if (response.status === 200) {
                //         colbalgaData = response.data;
                //     } else {
                //         console.error("Error fetching data:", response.message);
                //     }
                // },
                beforeSend: function () {
                    btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);
                },
            },
            order: [
                [4, 'desc']
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: [4]
                }
            ],
            columns: [
                {
                    data: null,
                    render: function (data) {
                        return data?.url || '';
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return data?.meta_title || '';
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return `<textarea class="textareaRead">${data?.meta_keyword || ''}</textarea>`;
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        return `<textarea class="textareaRead">${data?.meta_desp || ''}</textarea>`;
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        const dateTi = data?.created_at || '';
                        return dateTi ? moment(dateTi).format("DD MMM YYYY hh:mm A") : '';
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        let btn = '';
                       btn +=  `<input type="checkbox" ${data.status === 'active' ? 'checked' : ''} data-on="Active" data-off="Inactive" data-onstyle="success" data-offstyle="danger" data-toggle="toggle" class="toggle-one" id="toggle-${data?.id || ''}"  onchange="handleToggleChange(${data?.id || ''}, this)">`;
                        //   btn += `&nbsp;<span>${data.status === 'active' ? 'Active' : 'Inactive'}</span>`;
                        
                         btn +=  `&nbsp;<a class="btn text-danger btn-sm" style="cursor: pointer; font-size: 16px;" onclick="editForm(${data?.id || ''})">
                                    <span class="fa fa-pencil-square-o" style="color: #001e1e;" >&nbsp;Edit</span>
                                </a>`;
                        
                        return btn;
                    }
                },
            ],
            initComplete: function () {
                btn.html(`GO`).prop('disabled', false);
                
                $('.toggle-one').bootstrapToggle(
                    
    //                 {
    //                  on: 'Active',
    //   off: 'Inactive'
    //             }
                
                );
            }
        });

    } catch (e) {
        console.log(`Error: ${e.message}`);
    }
};

            
//   const unsubscribeList = () => {
//         try {
//             let startDate = null;
//             let endDate = null;
//             var btn = $(`#searchBTN`);
//             var dateFilter = $('#datefilterLogin').val() ?? null;
//             if (dateFilter != '' && dateFilter != null && dateFilter != undefined) {
//                 startDate = moment($('#datefilterLogin').data('daterangepicker').startDate).format("YYYY-MM-DD");
//                 endDate = moment($('#datefilterLogin').data('daterangepicker').endDate).format("YYYY-MM-DD");
//             }
//             var table = $('#Participation_List').DataTable({
//                 destroy: true,
//                 pageLength: 10,
//                 order: [],
//                 paging: true,
//                 searching: true,
//                 info: true,
//                 ajax: {
//                     url: '{{ env('API_URL') }}getArticleList',
//                     method: "POST",
//                     dataSrc: "data",
//                     data: {
//                         // method: 'CRMLsitReprot',
//                         dateFilter: dateFilter,
//                         startDate: startDate,
//                         endDate: endDate,
//                         searchTxt: $(`#searchTxt`).val(),
//                         statusVal: $(`#statusVal`).val(),
//                         statusPlan: $(`#statusPlan`).val(),
//                       token: getCookie('d_token')
                        
//                         // currencyID: $(`#currencyID`).val()
//                     },
//                      success: function (response) {
//     if(response.status === 200){
        

//         colbalgaData = response.data; 
//     }
       
//         // console.log(colbalgaData); 
//     },
//                     beforeSend: function () {
//                         btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);
//                     },
//                 },
//                 order: [
//                     [4, 'desc']
//                 ],
//                 columnDefs: [
                    
//                 //     {
//                 //     type: 'num',
//                 //     targets: [2]
//                 // }
                
//                 {
//                     type: 'date',
//                     targets: [4]
//                 }
                
//                 ],
//                 // dom: 'Bfrtip',
//                 // buttons: [
//                 //     'pageLength',
//                 //     'copy',
//                 //     {
//                 //         extend: 'excelHtml5',
//                 //         title: 'CRM List'
//                 //     },
//                 // ],
//                 columns: [
//                     {
//                         data: null,
//                         render: function (data, type, row, meta) {
//                             return data?.url || ''
//                         }
//                     },
                    
//                         {
//                         data: null,
//                         render: function (data, type, row, meta) {
//                             return data?.meta_title || ''
//                         }
//                     },
                    
                    
//                           {
//                         data: null,
//                         render: function (data, type, row, meta) {
//                             return `<textarea class="textareaRead">${data?.meta_keyword || ''}</textarea>`
//                         }
//                     },
                    
                    
//                           {
//                         data: null,
//                         render: function (data, type, row, meta) {
//                             return `<textarea class="textareaRead">${data?.meta_desp || ''}</textarea>`
//                         }
//                     },
                    
                    
                    
//                     //     {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return `<textarea class="textareaRead">${data?.meta_desp || ''}</textarea>`
//                     //     }
//                     // },
                    
                    
                    
                    
                    
                    
                    
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         let cart = data.checkout_response ? JSON.parse(data.checkout_response) : [];
//                     //         return cart?.productDetails?.name || '';
//                     //     }
//                     // },
//                     // {
//                     //     data: 'grandtotal',
//                     //     render: function (data, type, row, meta) {
//                     //         return parseFloat(data)
//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         let dom = data?.subDomainName || '';
//                     //         return dom ? `<a target="_blank" style="color: green;font-weight: 900;" href="https://${dom + '.' + mainDomain}">${dom}</a>` : '';

//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return data.Mobile
//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return _.capitalize(data?.subStatus || '')
//                     //     }
//                     // },
//                     {
//                         data: null,
//                         render: function (data, type, row, meta) {
//                             let dateTi = data?.created_at || '';
//                             return dateTi != '' ? moment(dateTi).format("DD MMM YYYY hh:mm A") : '';
//                         }
//                     },
                    
//                       {
//                         data: null,
//                         render: function (data, type, row, meta) {
                            
//                           let btn = `<a class="btn text-danger btn-sm" style="cursor: pointer; font-size: 16px;">
//               <span class="fa fa-pencil-square-o" style="color: #001e1e;" 
//                     onclick="editForm('${data?.id || ''}')">&nbsp;Edit</span>
//             </a>`;

//                             return btn;
//                         }
//                     },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return _.capitalize(data?.gateway || '')
//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return _.capitalize(data?.planType === 'TRIAL' ? 'TRAIL' : data?.planType || '');
//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {


//                     //         return data?.name ? `<a target="_blank" style="color: #525704; font-weight: 900;" href="profile/edit/${data?.userID}">${data?.name}</a>` : '';
//                     //     }
//                     // },
//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return data?.mobile ? `<a target="_blank" style="color: #525704; font-weight: 900;" href="tel:${data?.mobile}">${data?.mobile}</a>` : '';

//                     //     }
//                     // },

//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         return data?.email ? `<a target="_blank" style="color: #525704; font-weight: 900;" href="mailto:${data?.email}">${data?.email}</a>` : '';
//                     //     }
//                     // },

//                     // {
//                     //     data: null,
//                     //     render: function (data, type, row, meta) {
//                     //         let dateTi = data?.createdon || '';
//                     //         return dateTi != '' ? moment(dateTi).format("DD MMM YYYY hh:mm A") : '';
//                     //     }
//                     // },
//                     // {
//                     //     data: 'TotalSpending',
//                     //     render: function (data, type, row, meta) {
//                     //         return parseFloat(data)
//                     //     }
//                     // },
//                     // {
//                     //     data: 'TotalTapWinning',
//                     //     render: function (data, type, row, meta) {
//                     //         return parseFloat(data)
//                     //     }
//                     // },
//                     // {
//                     //     data: 'TotalWinning',
//                     //     render: function (data, type, row, meta) {
//                     //         return parseFloat(data)
//                     //     }
//                     // },
//                     // {
//                     //     data: 'TotalWinningCombined',
//                     //     render: function (data, type, row, meta) {
//                     //         return parseFloat(data)
//                     //     }
//                     // },
//                 ],
//                 initComplete: function (settings, json) {
//                     btn.html(`GO`).prop('disabled', false);
//                 },
//                 // footerCallback: function (row, data, start, end, display) {
//                 //     var api = this.api();
//                 //     var intVal = function (i) {
//                 //         if (typeof i === 'string') {
//                 //             const trimmed = i.trim();
//                 //             if (trimmed === 'NA' || trimmed === '') {
//                 //                 return 0; // Treat 'NA' as 0
//                 //             }
//                 //             return parseFloat(trimmed.replace(/[\$,]/g, '')) || 0;
//                 //         }
//                 //         return typeof i === 'number' ? i : 0;
//                 //     };
//                 //     var calculateTotals = function (columnIndex) {
//                 //         let s = {
//                 //             total: 0,
//                 //             pageTotal: 0
//                 //         };
//                 //         // Total for all pages
//                 //         s.total = api.column(columnIndex).data().reduce((a, b) => {
//                 //             const value = intVal(b);
//                 //             console.log(`Column ${columnIndex} Total Value:`, value); // Log total value for each item
//                 //             return a + value;
//                 //         }, 0);
//                 //         // Total for current page
//                 //         s.pageTotal = api.column(columnIndex, {
//                 //             page: 'current'
//                 //         }).data().reduce((a, b) => {
//                 //             const value = intVal(b);
//                 //             console.log(`Column ${columnIndex} Page Value:`, value); // Log page value for each item
//                 //             return a + value;
//                 //         }, 0);
//                 //         return s;
//                 //     };
//                 //     [2].forEach(function (i) {
//                 //         api.column(i).data().each(function (value, index) {
//                 //             console.log(`Row ${index} Column ${i} Value:`, value);
//                 //         });
//                 //         var totals = calculateTotals(i);
//                 //         $(api.column(i).footer()).html(totals.pageTotal.toFixed(2) + ' ( ' + totals.total.toFixed(2) + ' total)');
//                 //     });
//                 // }
//             });
//         } catch (e) {
//             console.log(`Error: ${e.message}`);
//         }
//     }
    
    
const getOptionList = () => {
    try {
     
            var h = new FormData();
  
            
            h.append('token', getCookie('d_token'));
         


            $.ajax({
                // url: url,
                url:  '{{ env('API_URL') }}generalsetting',
                type: 'POST',
                data: h,
                beforeSend: function () {

                    // Button Loading

                    // btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);

                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 200) {
                      
                      if(response.data.length > 0){
                          
                          
                          
                          
                          $.each(response.data, function(index, item) {
   
    const option = $('<option></option>').val(item.id).text(item.company_name);

   
    $('#selectType').append(option);
});
                          
                          
                          
                      } else {
                        //   $()
                        
                        console.log(`Create Website`);
                      }
                      
                      
                      
                        } else {

                            // $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 

                    // btn.html(`Save Content`).prop('disabled', false);
                },
                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    // btn.html(`Save Content`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },

                processData: false,
                contentType: false
            });
    } catch(e) {
        console.log(`Error: ${e.message}`);
    }
}

    // Save content to the server
    const saveContent = (id = '') => {
        try {

            var url = $(`#url`).val();
            var title = $(`#meta-title`).val();
            var description = $(`#meta-description`).val();
            var keyword = $(`#meta-keyword`).val();
            var content = editor1.getHTMLCode();
            var btn = $('#firstBtnSub');
var selectWeb = $('#selectType').val();

 if ( selectWeb == null || selectWeb == undefined || selectWeb == 'null' || selectWeb.trim() == '') {
                showToast('error', 'Select the Website!', 5000);
                return false;
            }

            if (url.length < 1 || url == null || url == undefined || url == 'null' || url.trim() == '') {
                showToast('error', 'URL is required!', 5000);
                return false;
            }

            if (title.length < 1 || title == null || title == undefined || title == 'null' || title.trim() == '') {
                showToast('error', 'Meta Title is required!', 5000);
                return false;
            }



            if (description.length < 1 || description == null || description == undefined || description == 'null' || description.trim() == '') {
                showToast('error', 'Meta description is required!', 5000);
                return false;
            }



            if (keyword.length < 1 || keyword == null || keyword == undefined || keyword == 'null' || keyword.trim() == '') {
                showToast('error', 'Meta keyword is required!', 5000);
                return false;
            }




            if (content.length < 1 || content == null || content == undefined || content == 'null' || content.trim() == '') {
                showToast('error', 'Content is required!', 5000);
                return false;
            }






            // var keyword = $(`#meta-keyword`).val();


            // console.log(content);
            var h = new FormData();
            // var h = formDataObject;

            // Append the data to the FormData object
            // h.append('_token', '{{ csrf_token() }}');
            
            
            
             h.append('gentralID', selectWeb);
            h.append('content_summary', content);
            h.append('url', url);
            h.append('title', title);
            h.append('description', description);
            h.append('keyword', keyword);
            h.append('token', getCookie('d_token'));
            
            h.append('id', id)
            
            
            // h.append('device_id', device_id ?? 0);
            // formDataObject[''] = token;
            // formDataObject['device_id'] = device_id;



            $.ajax({
                // url: url,
                url:  '{{ env('API_URL') }}save-article',
                type: 'POST',
                data: h,
                beforeSend: function () {

                    // Button Loading

                    btn.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;Loading...`).prop('disabled', true);

                },
                success: function (response) {
                    // var response = JSON.parse(data);
                    if (response != "") {
                        if (response.status == 200) {
                            
                            
                            // if()

                                        showToast('success', response.message, 5000);
                                        

setTimeout(function() {
    location.reload();
}, 5000);

                        

                        } else {

                            // $(`#resendPbtn`).removeAttr('disabled', 'disabled');
                            showToast('error', response.message, 5000);
                        }
                    }

                    // Loading Off 

                    btn.html(`Save Content`).prop('disabled', false);
                },
                error: function (xhr, status, error) {

                    showToast("error", "Request failed", 5000);

                    btn.html(`Save Content`).prop('disabled', false);

                    console.error('Request failed');

                    console.error(xhr, status, error);

                },

                processData: false,
                contentType: false
            });
            // }


            //     $.ajax({
            //     url: '/save-article',
            //     method: 'POST',
            //     data: {
            //         _token: '{{ csrf_token() }}',
            //         content_summary: content,
            //         url: url,
            //         title: title,
            //         description: description,
            //         keyword: keyword
            //     },
            //     success: function (response) {
            //         alert('Content saved successfully!');
            //     },
            //     error: function (err) {
            //         console.error(err);
            //         alert('Error saving content');
            //     }
            // });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }



$(document).ready(function() {
 
    
     try {
            // createDatePicker('datefilterLogin');

            // $('#datefilterLogin').trigger('cancel.daterangepicker');
               getOptionList();
               
            unsubscribeList();
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
});


</script>

@include('bookingsetting.partials.add_customer_modal')

@endsection

@section('custom_scripts')

@include('bookingsetting.partials.customers_js')

@endsection



<!--<script type="text/javascript" src='{{ asset('assets/js/common.js') }}'></script> -->