
<script> 
    $(function() {
        var formDataObject = {};
        formDataObject['token'] = getCookie('d_token');
        formDataObject['device_id'] = 0;
    
        var existingTable = $('#data-table').DataTable();
        if (existingTable) {
            existingTable.destroy();
        }
    
        $('#data-table').DataTable({
            processing: true,
            searching: false,
            ajax: {
                url: '{{env('API_URL')}}audit_logs',
                method: 'POST',
                dataSrc: "data",
                data: formDataObject
            },
            columns: [
                {data: null, render: function (data, type, row, meta) {
                    return meta.row + 1;
                }, orderable: false, searchable: false},
                {data: 'id', name: 'id', searchable: false, visible: false},
                {data: 'description', name: 'description', className: 'text-center'},
                {data: 'subject_id', name: 'subject_id', className: 'text-center'},
                {data: 'subject_type', name: 'subject_type', className: 'text-center'},
                {data: 'user_id', name: 'user_id', className: 'text-center'},
                {data: 'host', name: 'host', className: 'text-center'},
                {data: 'properties', name: 'properties', className: 'text-center'},
                {data: 'created_at', name: 'created_at', className: 'text-center'}
            ],
            order: [[1, 'desc']]
        });
    });

</script>
