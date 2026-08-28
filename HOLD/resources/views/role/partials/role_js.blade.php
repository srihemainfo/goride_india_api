<script> 
$(function () { 
        
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    let table = $('#data-table').DataTable({
        pageLength: 100,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{env('API_URL')}}roles",
            type: "post",
            dataType: 'json',
            delay: 400,
            data: function(d) {
                d.role_id = $('#role_id').val();
                d.token = token;
                d.device_id = device_id;
            },
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'id', name: 'id', searchable: false, visible: false},
            {data: 'title', name: 'module_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        "order": [[1, 'asc']]
    });

    // Handle Role creation
    $(document).on('click', '#Role_create', function(e) {
        e.preventDefault();
        var $role_name = $('#role_name').val();
          formDataObject['role_name'] = $role_name;

        if ($role_name) {
            loader.show();

            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}roles/store",
                data:formDataObject,
                success: function(response) {
                    if (response.status == true) {
                        
                        // swalalertsuccess(response.message)
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Added',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        
                        $('#role_name').val('')
                        
                        
                    } else {
                        
                        swalalerterror(response.message)
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Error',
                        //     text: response.message,
                        // });
                    }

                    loader.hide();
                    table.ajax.reload(); // Reload DataTable to show the new role
                },
                error: function(data) {
                    swalalerterror('Request Failed')
                    console.log('Error:', data);
                    loader.hide();
                }
            });
        }else{
            
            swalalerterror('Fill the Role Field')
            
            // Swal.fire({
            //     icon: 'error',
            //     title: 'Error',
            //     text: 'Fill the Role Field',
            // });
            
        }
    });
    
    $(document).on('click', '.editRole', function(){
        
        var $id = $(this).data('role-id');
        $('#edit_role_name').val('');
        $('#edit_role_id').val('')
        
        if($id ){
            
             loader.show();
             formDataObject['id'] = $id;

            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}roles/get_data",
                data: formDataObject,
                success: function(response) {
                    if (response.status == true) {
                    var  data  = response.data  ;
                        $('#edit_role_name').val(data.title );
                        $('#edit_role_id').val($id);
                        
                        $('#role_form_modal').modal('show');
                    } else {
                        
                        swalalerterror(response.message)
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Error',
                        //     text: response.message,
                        // });
                    }

                    loader.hide();
                    // Reload DataTable to show the new role
                },
                error: function(data) {
                    console.log('Error:', data);
                    loader.hide();
                }
            });
            
        
            
        }else{
            Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'data Not found',
                });
        }
        
    })
    
    $(document).on('click', '#roleBtn', function(){
        
        var $role_name =  $('#edit_role_name').val();
        var $id =  $('#edit_role_id').val()
        formDataObject['id'] = $id;
        formDataObject['role_name'] = $role_name;
        
        if($id && $role_name){
            
             loader.show();

            $.ajax({
                type: "POST",
                url: "{{env('API_URL')}}roles/update",
                data: formDataObject,
                success: function(response) {
                    if (response.status == true) {
                        
                        // swalalertsuccess(response.message)
                        
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Updated',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                         $('#role_form_modal').modal('hide');
                         table.draw();
                        
                    } else {
                        
                        swalalerterror(response.message)
                        // Swal.fire({
                        //     icon: 'error',
                        //     title: 'Error',
                        //     text: response.message,
                        // });
                    }

                    loader.hide();
                    
                },
                error: function(data) {
                    console.log('Error:', data);
                    loader.hide();
                }
            });
            
        
            
        }else{
            
            swalalerterror('title Required')
            // Swal.fire({
            //     icon: 'error',
            //     title: 'Error',
            //     text: 'title Required',
            // });
        }
        
    })
    
    $(document).on('click', '.deleteRole', function(e) {
    e.preventDefault();

    var id = $(this).data('role-id');
    formDataObject['id'] = id;

    if (id) {
        // Show Swal confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you really want to delete this role?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, proceed with AJAX delete request
                loader.show();
                $.ajax({
                    type: 'POST',
                    url: "{{env('API_URL')}}roles/delete",
                    data: formDataObject,
                    success: function(response) {
                        if (response.status) {
                            // swalalertsuccess(response.message);
                            Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Roles Deleted Successfully',
                            showConfirmButton: false,
                            timer: 2000,
                        });
                            table.draw();
                        } else {
                            // swalalerterror(response.message);
                            Swal.fire({
                            position: 'center',
                            icon: 'warning',
                            title: 'Failed',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                        });
                        }
                        loader.hide();
                    },
                    error: function(data) {
                        swalalerterror('Request Failed');
                        console.log('Error:', data);
                        loader.hide();
                    }
                });
            } else {
                // If cancelled, show cancellation message
                Swal.fire(
                    'Cancelled',
                    'Your role is safe :)',
                    'error'
                );
            }
        });
    } else {
        swalalerterror('Data Not Found');
    }
});

    
    // $(document).on('click','.deleteRole',function(e){
    //     e.preventDefault();
        
    //         var id =   $(this).data('role-id')
    //      formDataObject['id'] = id;
    //     if(id){
    //         loader.show();
    //         $.ajax ({
    //         type:'post',
    //         url: "{{env('API_URL')}}roles/delete",
    //         data:formDataObject,
    //         success: function(response){
                
    //             if(response.status){
                    
    //             swalalertsuccess(response.message)
    //                 // Swal.fire({
    //                 //     position: 'top-end',
    //                 //     icon: 'success',
    //                 //     title: 'Added',
    //                 //     text: response.message,
    //                 //     showConfirmButton: false,
    //                 //     timer: 2000,
    //                 // });
                    
    //                     table.draw();
                       
                
    //             }else{
    //                 swalalerterror(response.message)
    //                 // Swal.fire({
    //                 //     icon: 'error',
    //                 //     title: 'Error',
    //                 //     text: 'Data Not Found ',
    //                 // }); 
                    
    //             }
                
    //              loader.hide();
                
    //         }, error: function(data){
    //              swalalerterror('Request Failed')
    //             console.log('Error:', data);
    //             loader.hide();
    //         }
            
    //     });
        
    //     }else{
            
    //         swalalerterror('Data Not Found' );
    //         // Swal.fire({
    //         //     icon: 'error',
    //         //     title: 'Error',
    //         //     text: 'Data Not Found ',
    //         // });
    //     }
    // }) 
    
});

</script> 