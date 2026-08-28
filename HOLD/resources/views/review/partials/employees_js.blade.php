<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>

  

  

      function showlist() {

        var formDataObject = { 

        token: getCookie('d_token'),

        device_id: 0

    };


function getStars(count) {
    count = count || 0;
    let stars = '';

    for (let i = 1; i <= 5; i++) {
        if (i <= count) {
            stars += '<i class="fas fa-star text-warning"></i>'; // full star
        } else {
            stars += '<i class="far fa-star text-muted"></i>'; // empty star
        }
    }

    return stars;
}
    // Make an AJAX request

   $.ajax({
    url: '{{env('API_URL')}}reviewlist',
    method: 'POST',
    data: formDataObject,
    success: function(response) {
        console.log(response.reviewdetails);
        var data = response.data;

        AssignValues(data);
        var tbody = $('#reviewTable tbody');
        tbody.empty(); // clear existing rows

        if (response.reviewdetails.length === 0) {
            var row = `
                <tr>
                    <td colspan="7" class="text-center">Data not found</td>
                </tr>
            `;
            tbody.append(row);
        } else {
            $.each(response.reviewdetails, function(index, item) {
                var row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.job_id || '-'}</td>
                        <td>${item.customer_name || '-'}</td>
                        <td>${item.customer_email || '-'}</td>
                        <td>${getStars(item.star_count)}</td>
                        <td>${item.customer_msg || '-'}</td>
                        <td>${item.review_date || '-'}</td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
    },
    error: function(error) {
        console.error('Error fetching data:', error);
    }
});
}

  

    function AssignValues(data){

       console.log(data.review_send_after_pickup_time)

        $('#review_id').val(data.id);

        $('#review_send_setting').val(data.review_send_setting);

        $('#review_send_after_pickup_time').val(data.review_send_after_pickup_time);

        $('#review_subject').val(data.review_subject);

        $('#review_request_template').val(data.review_template);

    }

    

    $(function(){

        showlist()

    })

    

    $('#add_saveBtn').on('click', function(){

          const url = 'reviewstore';

        var formdata = $('#add_employeeForm').serialize();

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

            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0]; // "YYYY-MM-DD"
            formDataObject['current_date'] = formattedDate;


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

            Swal.fire({

                       position: "center",

                       icon: "success",

                       title: response['message'],

                       showConfirmButton: false,

                       timer: 1500

                   }).then(function() {

                    location.reload()

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

      })

      

      

</script>

