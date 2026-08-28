<script>

    @if (session('success_save'))
    
    // swalalertsuccess('{{ session('success_save') }}');
    swalalertsuccess('{{ session("success_save") }}');

        // Swal.fire({
        //     position: 'top-end',
        //     icon: 'success',
        //     title: 'Added',
        //     text: '{{ session('success_save') }}',
        //     showConfirmButton: false,
        //     timer: 2000
        // });    
    @endif

    @if (session('success_update'))
      swalalertsuccess('{{ session('success_update') }}');
        // Swal.fire({
        //     position: 'top-end',
        //     icon: 'success',
        //     title: 'Updated',
        //     text: '{{ session('success_update') }}',
        //     showConfirmButton: false,
        //     timer: 2000
        // });    
    @endif

    @if (session('failed_saveOrUpdate'))
    swalalerterror( '{{ session('failed_saveOrUpdate') }}')
        // Swal.fire(
        //     'Error',
        //     '{{ session('failed_saveOrUpdate') }}',
        //     'error'
        // )
    @endif
    
    $(function(){
        showlist()
    })
    
    function showlist(status=''){
        const url = 'driverlist';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['status']= status;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
        
         if(response['status'] == 200){
             
            var list = '';
            var count = 0;  // Track the number of responses
            for(i=0; i < response['data'].length; i++){
    
            file(response['data'][i], i, function(imageData, index) {
     
        if(response['data'][index]['status'] == 'Active'){
            var sts = 'checked';
        } else {
            var sts = '';
        }

        // Append the HTML to the list variable
        list += '<div class="col-md-6 car-clmn mb-3"class="fleets_card"><div class="who-dr"><div class="as-sec"><label class="switch"><input type="checkbox" '+sts+' id="drstatus'+response['data'][index].id+'" onclick="changedriverstatus('+response['data'][index].id+')"><div class="slider"></div><div class="slider-card"><div class="slider-card-face slider-card-front"></div><div class="slider-card-face slider-card-back"></div></div></label></div><div class="img-sec" >' + imageData + '</div><div class="detail-sec"><h3 style="font-size: 16px;font-weight: 600;color: #003757;">'+response['data'][index].name+'</h3><ul class="mb0 " style="display: grid;padding: 0;margin: 0;"><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-phone me-2"></i>'+response['data'][index].phone+'</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-envelope me-2"></i>'+response['data'][index].email+'</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-signal me-2"></i>assigned</a></li></ul><ul style="display: flex;margin: 0; padding: 0;"><li style="list-style-type: none;margin: 11px 9px 1px 3px;"><a class="ediicon" href="/driver/edit/'+response['data'][index].id+'"><i class="fa-solid fa-pen-to-square"></i></a></li><li style="list-style-type: none;"><button class="delicon" style="list-style-type: none;margin: 3px;padding: 7px 8px 7px 8px;background: #d92550;color: #fff;border-radius: 6px;border: none;" onclick="del_driver('+response['data'][index].id+')"><i class="fa-solid fa-trash"></i></button></li></ul></div></div></div>';

        // Increase the counter to know when all async operations are done
        count++;

        // Once all responses have been processed, update the HTML
        if (count === response['data'].length) {
            $('.listofdriver').html(list);  // Update only after all async calls are done
        }
    });
    
        }

        if(response['data'].length == 0){
             $('.listofdriver').html("<div class='text-center'>Data not found</div>");
        }


}

         if(response['status'] == 400){
            errornotify(response)
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
        }
    
    $('#dfil_sub').on('click', function(){
        const url = 'driverfilter';
        var formdata = $('#driver_filter').serialize();
         var pairs = formdata.split('&');
            var formDataObject  = {};
            
            for (var i = 0; i < pairs.length; i++) {
              var pair = pairs[i].split('=');
              var key = decodeURIComponent(pair[0]);
              var value = decodeURIComponent(pair[1]);
              formDataObject[key] = value;
            }
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
             var list = '';
             for(i=0; i < response['data'].length; i++){
             file(response['data'][i], i, function(imageData, index) {
                 list += '<div class="col-md-6 car-clmn mb-3"><div class="who-dr"><div class="img-sec" >' + imageData + '</div><div class="detail-sec"><h3 style="font-size: 16px;font-weight: 600;color: #003757;">'+response['data'][index].name+'</h3><ul class="mb0 " style="display: grid;padding: 0;margin: 0;"><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-phone me-2"></i>'+response['data'][index].phone+'</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-envelope me-2"></i>'+response['data'][index].email+'</a></li><li class="list-inline-item mb-1"><a href="#" class="car_li"><i class="fa-solid fa-signal me-2"></i>assigned</a></li></ul><ul style="display: flex;margin: 0; padding: 0;"><li style="list-style-type: none;margin: 11px 9px 1px 3px;"><a class="ediicon" href="/driver/edit/'+response['data'][index].id+'"><i class="fa-solid fa-pen-to-square"></i></a></li><li style="list-style-type: none;"><button class="delicon" style="list-style-type: none;margin: 3px;padding: 7px 8px 7px 8px;background: #d92550;color: #fff;border-radius: 6px;border: none;" onclick="del_driver('+response['data'][index].id+')"><i class="fa-solid fa-trash"></i></button></li></ul></div></div></div>';
             $('.listofdriver').html(list);
             });
             }
             }
         if(response['status'] == 400){
            $('.listofdriver').html(response['message'])
            Swal.fire({
                 position: "top-right",
                 icon: "warning",
                 title: response['message'],
                 showConfirmButton: true,
                 timer: 2500
                }).then(function() {
                    showlist()
                });
         }
         if(response['status'] == 500){
            warningClick('Error',response['error'],"danger")
         }
         if(response['status'] == 401){
            unauth()
         }
      });
    })
    
     $('#reset_filter').on('click', function(){
          $("#driver_filter")[0].reset();
      })
      
    function del_driver(id){
        const url = 'deletedriver';
            var formDataObject  = {};
            formDataObject['token'] = getCookie('d_token');
            formDataObject['device_id'] = 0;
            formDataObject['driver_id'] = id;
        var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      Swal.fire({
             title: 'Are you sure?',
             text: 'You won\'t be able to revert this!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes, delete it!',
             cancelButtonText: 'No, cancel!',
           }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax(settings).done(function (response) {
         if(response['status'] == 200){
                   Swal.fire({
                       position: "top-right",
                       icon: "success",
                       title: response['message'],
                       showConfirmButton: true,
                       timer: 2500
                      }).then(function() {
                          showlist()
                      });
                   }
               if(response['status'] == 400){
                  errornotify(response)
               }
               if(response['status'] == 500){
                  warningClick('Error',response['error'],"danger")
               }
               if(response['status'] == 401){
                  unauth()
               }
            });  
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
             }
         });
        
    }
    
    function changedriverstatus(id){
          const url = 'driverstatus';
          var formDataObject  = {};
          formDataObject['token'] = getCookie('d_token');
          formDataObject['device_id'] = 0;
          formDataObject['driver_id'] = id;
          if ($('#drstatus'+id).prop('checked')) {
                formDataObject['isActive'] = 'Active';
            } else {
                formDataObject['isActive'] = 'Inactive';
            }

          var settings = {
         "url": "{{env('API_URL')}}"+url,
         "method": "POST",
         "timeout": 0,
         "headers": {
             "Content-Type": "application/json"
          },
         "data": JSON.stringify(formDataObject),
      };
      Swal.fire({
             title: 'Are you sure?',
             text: 'You want to change the status!',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes',
             cancelButtonText: 'No, cancel!',
           }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax(settings).done(function (response) {
                   if(response['status'] == 200){
                       Swal.fire({ 
                                 position: "top-right",
                                 icon: "success",
                                 title: response['message'],
                                 showConfirmButton: false,
                                 timer: 1500
                             }).then(function() {
                              showlist()
                          });
                       }
                   if(response['status'] == 400){
                       warningClick('Error',response['message'],"danger")
                   }
                   if(response['status'] == 500){
                      warningClick('Error',response['error'],"danger")
                   }
                   if(response['status'] == 401){
                      unauth()
                   }
                  });
               
             } else if (result.dismiss === Swal.DismissReason.cancel) {
               Swal.fire('Cancelled', 'Your data is safe.', 'error');
                showlist()
             }
         });
      }
      
      
// function file(data, index, callback) {

//     var settings = {
//   "url": "{{env('API_URL')}}showfile",
//   "method": "POST",
//   "timeout": 0,
//   "headers": {
//     "Content-Type": "application/json"
//   },
//   "data": JSON.stringify({
//     "image": data.upload_photo
//   }),
// };

//     $.ajax(settings).done(function(response) {
//         var imageData = `<img class="img-flx" src="data:image/png;base64,${response}" alt="Displayed Image" style="width: 211px; height:187px;">`;
//         if (callback && typeof callback === "function") {
//             callback(imageData, index);
//         }
//     });
// }

function file(data, index, callback) {
 
    var settings = {
  "url": "{{env('API_URL')}}showfile",
  "method": "POST",
  "timeout": 0,
  "headers": {
    "Content-Type": "application/json"
  },
  "data": JSON.stringify({
    "image": data.upload_photo
  }),
};

    $.ajax(settings).done(function(response) {
        console.log(response);
        if(typeof response != 'object'){
        var imageData = `<img class="img-flx" src="data:image/png;base64,${response}" alt="Displayed Image" style="width: 220px;height: 200px;padding: 9px 30px 9px 0px;border-right: 1px solid #cdc3c3;">`;
            
        }else{
           var imageData = `<img class="img-flx" src="{{asset('unknown.png')}}" alt="Displayed Image" style="width: 220px;height: 200px;padding: 9px 30px 9px 0px;border-right: 1px solid #cdc3c3;">`;
          
        }
        if (callback && typeof callback === "function") {
            callback(imageData, index);
        }
        
        
    });
}
</script>
<script>
    
$(document).ready(function(){
    $('.close-sidebar-btn').click(function(){
        $('.fleets_card').toggleClass('small_screen');
    });
    
    $(document).on('change','#driver_status',function(){
        var value = $(this).val();
        return showlist(value)
    })
});




    
</script>