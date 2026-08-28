<style>
    .driver-license{
         text-transform: math-auto !important;
    }
</style>

<div class="card-header">

    <div><h4 class="card-title">File Upload</h4>
    <p class="driver-license">E.g:Driver License</p> 
</div>

    <div class="btn-actions-pane-right">

        <input type="hidden" id="driver_id_file" name="driver_id_file" value="driver id"/>

        <button type="button" class="btn btn-success" id="addFileupload" /><i class="fa fa-upload" style="padding-right: 5px "> </i>  Add Document </button>

    </div>

</div>

<div class="card-body">

  

        <table class="table table-striped" style="margin-bottom: 0px;">

            <thead>

                <tr>

                    <th style="width: 60%;border-top: 0px;">Document Description</th>

                    <th style="width: 40%;border-top: 0px;">Action</th>

                </tr>

            </thead>

            <tbody id="uploaded_documents_view">

                <?php /*

                    <tr>

                    <td>

                        <label for="booking_email">document description</label>

                    </td>

                    <td>

                   

                        <a href="file path" target="_blank" id="document_view" title="View Document" class="mb-2 mr-2 btn-sm btn-transition btn " style="color: #266444; border-color:#266444;"><i class="fa fa-eye"></i></a>

                        <button type="button" class="mb-2 mr-2 btn-sm btn  btn-outline-danger document_delete" data-id="doc id" title="Delete Document" />  <i class="fa fa-trash"></i> </button>

                  

                    </td>

                </tr>

                */ ?>

            </tbody>

        </table>

</div>



