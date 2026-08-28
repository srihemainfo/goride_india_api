<style>
    .driver-license{
         text-transform: math-auto !important;
    }
</style>
@php
    use Illuminate\Support\Str;
    $currentUrl = request()->path();
@endphp
<div class="card-header">

    <div><h4 class="card-title">File Upload</h4>
        <p class="driver-license">E.g:Driver License</p> 
    </div>

    <div class="btn-actions-pane-right">

        <input type="hidden" id="driver_id_file" name="driver_id_file" value=""/>
        @if (Str::startsWith($currentUrl, 'driver/edit'))
            <button type="button" class="btn btn-success" onclick="showModal()">
                Add Document
            </button>
        @else
        @endif
 
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

            </tbody>

        </table>

</div>



