<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rating Modal</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .star-rating {
      font-size: 30px;
      color: lightgray;
      cursor: pointer;
    }

    .star-rating .hover,
    .star-rating .selected {
      color: gold;
    }

    .modal-content {
      border-radius: 20px;
    }

    .rate-btn {
      background-color: #007bff;
      color: #fff;
      padding: 10px 20px;
      border-radius: 25px;
      border: none;
      font-weight: 500;
    }

    .rate-btn:hover {
      background-color: #0056b3;
    }

    .close {
      font-size: 1.5rem;
    }
  </style>
</head>

<body>

  <div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <!-- <button type="button" class="close ml-auto mr-2" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->

        <div class="mb-3">
          <img src="{{ asset('tone/star_rating.jpg') }}" alt="Feedback" style="width: 300px; height: auto;">
        </div>
        <h5>Hi <strong>{{ $name }}</strong>,</h5>
        <p>Give Your Star Rating</p>
        <div class="star-rating mb-2" id="stars">
          <span data-value="1">&#9733;</span>
          <span data-value="2">&#9733;</span>
          <span data-value="3">&#9733;</span>
          <span data-value="4">&#9733;</span>
          <span data-value="5">&#9733;</span>
        </div>
        <p>Thanks for loving us!</p>

        <form id="feedbackForm">
          <input type="hidden" name="customer_name" id="customer_name" value="{{ $name }}">
          <input type="hidden" name="web_url" id="web_url" value="{{ $weburl }}">
          <input type="hidden" name="token" id="token" value="{{ $token }}">
          <input type="hidden" name="job_no" id="job_no" value="{{ $jobNo }}">
          <input type="hidden" name="customer_email" id="customer_email" value="{{ $email }}">
          <input type="hidden" name="rating" id="ratingValue">
          <input type="text" name="feedback" class="form-control mb-3" maxlength="190" placeholder="Write your feedback...">
          <button type="submit" class="rate-btn">Send</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  $(document).ready(function () {
    onloadcheck();
    
    function onloadcheck() {
      const data = {
        web_url: $('#web_url').val(),
        token: $('#token').val(),
        job_no: $('#job_no').val(),
      };

      $.ajax({
        url: '{{env('API_URL')}}onloadcheck',
        method: 'POST',
        data: data,
        success: function (response) {
          if (response.success == 400) {
            Swal.fire({
              icon: 'info',
              title: 'Thanks for your review!',
              showConfirmButton: false,
              timer: 4000
            });

            setTimeout(() => {
              window.close(); // Close only if opened by JS
            }, 4000);
          } else {
                $('#ratingModal').modal({
                backdrop: 'static',
                keyboard: false
              });
          }
        },
        error: function (xhr) {
          console.error('Error:', xhr.responseText);
          Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        }
      });
    }

    let selectedRating = 0;

    // Hover effect
    $('#stars span').on('mouseenter', function () {
      let val = $(this).data('value');
      $('#stars span').each(function () {
        $(this).toggleClass('hover', $(this).data('value') <= val);
      });
    }).on('mouseleave', function () {
      $('#stars span').removeClass('hover');
    });

    // Click to select rating
    $('#stars span').on('click', function () {
      selectedRating = $(this).data('value');
      $('#ratingValue').val(selectedRating);

      $('#stars span').each(function () {
        $(this).toggleClass('selected', $(this).data('value') <= selectedRating);
      });
    });

    // Form submission
    $('#feedbackForm').on('submit', function (e) {
      e.preventDefault();

      if (!selectedRating) {
        Swal.fire('Required', 'Please give a star rating before submitting!', 'warning');
        return;
      }

      const data = {
        customer_name: $('#customer_name').val(),
        web_url: $('#web_url').val(),
        token: $('#token').val(),
        job_no: $('#job_no').val(),
        customer_email: $('#customer_email').val(),
        rating: $('#ratingValue').val(),
        feedback: $('input[name="feedback"]').val(),
      };

      $.ajax({
          url: '{{env('API_URL')}}feedbackstore', 
          method: 'POST',
          data: data,
          success: function (response, textStatus, xhr) {
              console.log('Success:', response);

              if(response.status == 200){
                  Swal.fire({
                      icon: 'success',
                      title: 'Thank you for your feedback!',
                      showConfirmButton: false,
                      timer: 3000
                  });

                  setTimeout(() => {
                      window.close();
                  }, 3000);
              } else if (response.status == 409){
                  Swal.fire({
                      icon: 'info',
                      title: 'Thank you',
                      text: response.message,
                      showConfirmButton: false,
                      timer: 3000
                  });

                  setTimeout(() => {
                      window.close();
                  }, 3000);
              }
          },
          error: function (xhr) {
              console.error('Error:', xhr.responseText);
              Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          }
      });

    });
  });
</script>



</body>

</html>