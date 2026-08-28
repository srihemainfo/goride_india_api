<script>

 $(document).ready(function(){

  vehicletable();

});    

//  function vehicletable() {

//     var formDataObject = {

//         token: getCookie('d_token'),

//         device_id: 0

//     };



//     $.ajax({

//         url: '{{env('API_URL')}}PriceView',

//         method: 'POST',

//         data: formDataObject,

//         success: function(response) {

//             console.log("API Response:", response);



//             if (response.status === 200 && response.data && Array.isArray(response.data)) {

//                 var listCarFares = response.data;

//                 console.log(listCarFares);



//                 $('table tbody').empty(); 



//                 listCarFares.forEach(function(item) {

//                     let row = `

//                         <tr data-id="${item.id}">

//                             <td>${item.cost}</td>

//                             <td>${item.dates}</td>

//                             <td>${item.content}</td>

//                             <td class="footable-editing footable-last-visible">

//                                 <div class="btn-group btn-group-sm" role="group">

//                                     <!-- Edit Button -->

//                                     <button type="button" class="btn btn-outline-secondary footable-edit" 

//                                         data-bs-toggle="modal" 

//                                         data-bs-target="#editor-modal" 

//                                         data-id="${item.id}">

//                                         <span class="fa-solid fa-edit" aria-hidden="true"></span>

//                                     </button>



//                                     <!-- Delete Button -->

//                                     <button id="delete" class="btn btn-outline-danger footable-delete" 

//                                         data-id="${item.id}">

//                                         <i class="fa fa-trash"></i>

//                                     </button>

//                                 </div>

//                             </td>

//                         </tr>

//                     `;



//                     $('table tbody').append(row);

//                 });



//                 // Handle permissions if they exist

//                 if (response.permissions) {

//                     if (!response.permissions.IS_CREATABLE) {

//                         $('.create-button').hide();

//                     }

//                     if (!response.permissions.IS_UPDATABLE) {

//                         $('.footable-edit').hide();

//                     }

//                     if (!response.permissions.IS_DELETABLE) {

//                         $('.footable-delete').hide();

//                     }

//                 }

//             } else {

//                 console.error('Unexpected response or data not found.');

//             }

//         },

//         error: function(error) {

//             console.error('Error fetching data', error);

//         }

//     });

// }









function vehicletable() {

    var formDataObject = {};

    formDataObject['token'] = getCookie('d_token');

    formDataObject['device_id'] = 0;

    

    if ($.fn.DataTable.isDataTable('#data-table')) {

        $('#data-table').DataTable().destroy();

    }



    $('#data-table').DataTable({

        ajax: {

            url: '{{env('API_URL')}}PriceView',

            method: 'POST',

            dataSrc: 'data',  

            data: function(d) {

                return $.extend({}, d, formDataObject);  

            }

        },

        columns: [

            { 

                data: null,

                render: function(data, type, row, meta) {

                    return meta.row + 1;  

                }

            },

            { data: 'cost' },

            { data: 'dates' },

            { data: 'content' },



            {

                data: null,

                render: function(data, type, row) {

                    return `

                       

                            <!-- Edit Button -->

                        <button type="button" class="btn btn-outline-secondary footable-edit "  data-id="${row.id}"

                        data-bs-toggle="modal" 

                        data-bs-target="#editor-modal" 

                        data-id="123">

                    <span class="fa-solid fa-edit" aria-hidden="true"></span>

                </button>





                            <!-- Delete Button -->

                            <button class="btn btn-outline-danger footable-delete" 

                                data-id="${row.id}">

                                <i class="fa fa-trash"></i>

                            </button>

                       

                    `;

                }

            }

        ],

        destroy: true,  

        responsive: true,  

        autoWidth: false   

    });

}















  $('#primaryBtn').click(function (e) {

    e.preventDefault();





    var form = $('#VehicleForm')[0];

    if (!form) {

        console.log('Form not found!');

        return;

    }

    var costValue = $('#cost').val();

    if (costValue.length > 8) {

        Swal.fire({

            title: '',

            text: 'The cost length must be up to 8 digits only.',

            icon: 'error'

        });



        return false;

    }



    var formdata = new FormData(form);





    formdata.append('token', getCookie('d_token'));

    formdata.append('device_id', 0);



    $.ajax({

        data: formdata,

        url: "{{env('API_URL')}}OfferDaysStore",

        type: "POST",

        processData: false,  

        contentType: false,  

        dataType: 'json',

       success: function (response) {

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                Swal.fire({

                        position: 'center',

                        icon: 'error',

                        title: 'Error',

                        text: 'Offer Day Already Exists',

                        showConfirmButton: false,

                        timer: 2000,

                        }).then(function () {

                        window.location.reload();



                    });

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {



                if (response.message =="Data has been updated successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Offer Day Updated successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });

                } else if(response.message =="Data has been inserted successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Added',

                        text: 'Offer Day Added successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });

                }

            }

        },

        else: function (xhr, status, error) {

            // Better error logging

            console.log('Error:', error);

            Swal.fire({

                icon: 'error',

                title: 'Oops...',

                text: 'Something went wrong! ' + xhr.responseText,

            });

        }

    });

});





$(document).on('click', '.footable-edit', function() {

    var id = $(this).data('id'); 



    $.ajax({

        url: '{{env('API_URL')}}OfferDaysEdit/edit/' + id, 

        method: 'POST', 

        data: {

            id: id,   

            token: getCookie('d_token'), 

            device_id: 0 

        },

        success: function(response) {

            if (response.status === 200) {

                var data = response.data; 

                console.log('API Data: ', data);

                $('#editcost').val(data.cost);

                $('#editdates').val(data.dates);

                $('#editcontent').val(data.content);

            } else {

                alert('Failed to fetch the data for editing.');

            }

        },

        error: function(error) {

            console.error('Error:', error);

            alert('An error occurred while fetching data.');

        }

    });

});









$(document).on('click', '.footable-edit', function() {

    editVehicleId = $(this).data('id'); 

    $('#editVehicleId').val(editVehicleId);  

});





$('#UpdateprimaryBtn').click(function (e) {

    e.preventDefault();



    var form = $('#EditVehicleForm')[0];

    if (!form) {

        console.log('Form not found!');

        return;

    }



    var formdata = new FormData(form);

    formdata.append('token', getCookie('d_token')); 

    formdata.append('device_id', 0);





    var vehicleId = $('#editVehicleId').val(); 



    if (!vehicleId) {

        console.log('Vehicle ID not found!');

        return;

    }



   var url = `{{env('API_URL')}}OfferdaysUpdate/update/` + vehicleId;







    $.ajax({

        data: formdata,

        url: url,

        type: "POST",

        processData: false, 

        contentType: false,  

        dataType: 'json',

        success: function (response) {

            if (response.status == 400) {

                errornotify(response);

            } else if (response.status == 500) {

                warningClick('Error', response['error'], "danger");

            } else if (response.status == 401) {

                unauth();

            } else if (response.status == 200) {



                if (response.message =="Data has been updated successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Updated',

                        text: 'Offer day updated successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });

                } else if(response.message =="Data has been inserted successfully") {

                    Swal.fire({

                        position: 'center',

                        icon: 'success',

                        title: 'Added',

                        text: 'Offer day Inserted successfully',

                        showConfirmButton: false,

                        timer: 2000,

                    }).then(function () {

                        window.location.reload();



                    });

                }

            }

        },

        error: function (xhr, status, error) {

            console.log('Error:', error);

            Swal.fire({

                icon: 'error',

                title: 'Oops...',

                text: 'Something went wrong! ' + xhr.responseText,

            });

        }

    });

});





  $(document).on('click', '.footable-delete', function() {

    var id = $(this).data('id');

    console.log("Delete button clicked with ID:", id);



    Swal.fire({

        title: 'Are you sure?',

        text: "You won't be able to revert this!",

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#3085d6',

        cancelButtonColor: '#d33',

        confirmButtonText: 'Yes, delete it!'

    }).then((result) => { // Corrected: this is where the block should start

        if (result.isConfirmed) {

            $.ajax({

                url: '{{env('API_URL')}}OfferDaysdelete/delete/' + id,

                type: 'POST', 

                data: {

                    token: getCookie('d_token'),

                    device_id: 0

                },

                success: function(response) {

                    if (response.status === 200) {

                        $('tr[data-id="' + id + '"]').remove();

                        Swal.fire({

                            position: 'center',

                            icon: 'success',

                            title: 'Deleted',

                            text: 'Offer Day Deleted successfully.',

                            showConfirmButton: false,

                            timer: 1500

                        });

                        window.location.reload(); // Correct place for page reload

                    } else {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: 'Failed to delete item.',

                        });

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Error:', xhr.responseText); 

                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text: 'An error occurred while trying to delete the item.',

                    });

                }

            });

        }

    });

});





</script>