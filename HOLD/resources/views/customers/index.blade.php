@extends('dashboard-layout.index')



@section('content')



    @include('customers.partials.filter') 

    <style>
        #Sendemail.modal .modal-dialog-aside {

            width: 600px !important;

        }

        @media (max-width: 768px) {
            .modal-dialog-aside {
                width: 100% !important;
                height: 100% !important;
                margin: 0;
                max-width: none;
                left: 12px;
            }

            .modal-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
            }

            .customer-name {
                max-width: 100% !important;
                padding-left: 6px !important;
            }
        }
    </style>

    <div class="col-sm-12 main-card mb-2 card">

        <div class="card-header">

            <h4 class="card-title">Customer List</h4>

            <div class="btn-actions-pane-right">

                <!--<a href="" target="_blank" id="generate-excel" class="btn btn-primary"><i class="fas fa-upload"></i> Export </a>-->

                @if($IS_CREATABLE)

                    <button type="button" class="btn btn-success" id="addCustomer"><i class="fas fa-plus"></i> Add Customer
                    </button>

                @endif

                <button type="button" id="Emailsend" class="btn btn-warning text-white">

                    Send Mail

                </button>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table id="cus-table" class="table" width="100%">

                    <thead class="table-light">

                        <tr>

                            <th style="width:7%;">#</th>

                            <th style="width:10%;">Checkbox</th>

                            <th style="width:10%;">Member ID</th>

                            <th>Full Name</th>



                            <th style="width:12%;">Phone No.</th>

                            <th>Email</th>

                            <th style="width:10%;">Action</th>

                        </tr>

                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>

    <div id="Sendemail" class="modal fixed-left fade" tabindex="-1" role="dialog">

        <div class="modal-dialog modal-dialog-aside" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">Send Emails (Customer)</h5>

                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <form id="emailForm" name="emailForm">

                        <div class="row">

                            <div class="col-sm-6 d-flex">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="message_type" id="singleRadio"
                                        value="single" checked onchange="changeField('single')">
                                    <label class="form-check-label" for="singleRadio">Single</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="message_type" id="groupRadio"
                                        value="group" onchange="changeField('group')">
                                    <label class="form-check-label" for="groupRadio">Group</label>
                                </div>
                            </div>

                            <div class="col-sm-6 hideGroup" style="display: none;">

                                <label for="templateSelect" class="col-form-label">Groups<span
                                        class="required">&nbsp;*</span></label>
        
                                <!--<select id="groupSelect" name="groupSelect" class="form-control px-5" required>-->

                                <!-- Options will be populated here -->

                                <!--</select>-->

                                <!-- <div class="input-group">

                                    <select class="form-select" id="groupSelect"
                                        aria-label="Example select with button addon" name="group_id">
                                        <option selected>Choose...</option>
                                    </select>

                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="$('#groupModal').modal('show');">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>


                                </div> -->

                                <div class="input-group">

                                    <select class="form-select" id="groupSelect"
                                        aria-label="Example select with button addon" name="group_id">
                                        <option selected>Choose...</option>
                                    </select>

                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="$('#groupModal').modal('show');">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                        <button class="btn btn-danger" type="button" onclick="openDeleteModal();">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <div class="col-sm-6">

                                <label for="templateSelect" class="col-form-label">Select Template<span
                                        class="required">&nbsp;</span></label>

                                        <div class="input-group">
                                        <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='/EmailTemplate'">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                        <select id="templateSelect" name="template_name" class="form-select" required>
                                            <option selected>Select Template</option>
                                        </select>
                                    </div>

                            </div>

                            <div class="col-sm-6 hideSingle">

                                <div class="form-group">

                                    <label for="customer_emails" class="col-form-label">Email<span
                                            class="required">&nbsp;*</span></label>

                                    <input type="email" class="form-control" name="customer_email" id="customer_emails"
                                        placeholder="Enter recipient's email" required>

                                    <p class="text-danger invalid_email"></p>

                                </div>

                            </div>


                            <div class="col-sm-6 ">

                                <div class="form-group">

                                    <label class="col-form-label">Mail Body (Message)<span
                                            class="required">&nbsp;*</span></label><br>

                                    <button type="button" class="btn btn-primary mb-2"
                                        id="preview_email">Preview</button><br>
                                </div>
 
                            </div>

                            <div class="col-sm-12 ">

                                <div class="form-group">

                                    <div class="col-5 px-0 mb-3 customer-name hideSingle">

                                        <label for="customer_emails" class="col-form-label">Customer Name<span
                                                class="required">&nbsp;*</span></label>

                                        <input id="customernames" type="text" name="customernames" class="form-control"
                                            placeholder="Customer Name">

                                    </div>

                                    <div class="col-12 px-0 mb-3 customer-name">

                                        <label for="customer_emails" class="col-form-label">Email Subject<span
                                                class="required">&nbsp;*</span></label>

                                        <input id="email_subject" type="text" name="email_subject" class="form-control"
                                            placeholder="Subject" maxlength="75"
                                            oninput="this.value = this.value.replace(/[^A-Za-z0-9. ]/g, '')">

                                    </div>
                                    <label for="customer_emails" class="col-form-label">Email Description<span
                                                class="required">&nbsp;*</span></label>
                                    <div id="customer_email_send" class="form-control mt-2"
                                        style="height:20em; overflow-y: auto;" name="description" contenteditable="true">



                                    </div>
                                    <!-- <textarea 
                                        id="customer_email_send" 
                                        class="form-control mt-2"
                                        style="height: 20em; overflow-y: auto;" 
                                        name="description"
                                        placeholder="Put your email content"></textarea> -->
                                    

                                    <p class="text-danger invalid_message"></p>

                                </div>

                            </div>



                        </div>

                    </form>

                </div>

                <div class="modal-footer">

                    <div id="load_animation_emails" style="display: none;">

                        <div class="spinner-grow text-primary" role="status">

                            <span class="sr-only"></span>

                        </div>

                        <div class="spinner-grow text-secondary" role="status">

                            <span class="sr-only"></span>

                        </div>

                        <div class="spinner-grow text-success" role="status">

                            <span class="sr-only"></span>

                        </div>

                        <div class="spinner-grow text-danger" role="status">

                            <span class="sr-only"></span>

                        </div>

                        <div class="spinner-grow text-warning" role="status">

                            <span class="sr-only"></span>

                        </div>

                        <div class="spinner-grow text-info" role="status">

                            <span class="sr-only"></span>

                        </div>

                    </div>

                    <!-- <button type="button" class="btn btn-primary" id="primaryBtn"><i class="fa fa-paper-plane"></i>&nbsp;
                        Send Email</button> -->
                        <button class="btn btn-primary" id="primaryBtn">
                            <span class="spinner-border spinner-border-sm text-light" style="display: none;" role="status" aria-hidden="true"></span>
                            <span class="button-text"><i class="fa fa-paper-plane"></i></span>&nbsp;Send Email
                        </button>

                </div>

            </div>

        </div>

    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="previewModalLabel">Email Preview </h5>

                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div id="email_preview_content"></div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                </div>

            </div>

        </div>

    </div>

    <div class="modal fade" id="groupModal" tabindex="-1" role="dialog" aria-labelledby="groupModalLabel"
        aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="previewModalLabel">Group Create</h5>

                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <form id="groupForm">
                    <div class="modal-body">
                        <!-- Group Name Input -->
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Group Name</label>
                            <input type="text" maxlength="50" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                                class="form-control" id="groupName" name="group_name" placeholder="Enter group name">
                        </div>

                        <!-- Search Input -->
                        <!-- <div class="mb-3" style="position: relative;">
                        <input type="text" class="form-control" id="customerSearch" placeholder="Search by name or email" style="width: 50%; position: absolute; right: 0;">
                    </div> -->

                        <!-- Scrollable Customer Table -->
                        <div style="max-height: 300px;overflow-y: auto;margin-top: 70px;">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">Select</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody id="customer_list">
                                    <!-- Dynamically loaded rows -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Group</button>
                    </div>
                </form>


            </div>

        </div>

    </div>

    <div class="modal fade" id="groupModalDelete" tabindex="-1" role="dialog" aria-labelledby="groupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Group Delete</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="group_Formdelete">
                <div class="modal-body">
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">S.No</th>
                                    <th>Group Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="customer_list_delete">
                                <!-- Dynamically loaded rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




    @include('customers.partials.add_customer_modal')

@endsection 



@section('custom_scripts')

    @include('customers.partials.customers_js')

@endsection