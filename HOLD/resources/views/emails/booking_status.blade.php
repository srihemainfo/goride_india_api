<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Booking Status Email</title>
    </head>
    <body>
        <p>Dear {{ $customer }},</p>

        <p><strong>{{ $company }} Booking Information</strong></p>

        <p>Your Booking Ref: {{ $job_no }}</p>

        <p>Your booking has been received, We will confirm your booking within 12 hours.</p>

        <p>If your booking less than 12 hours please call us on  0208 111 1104 to Confirmed.</p>

        <p>To view and print the Booking details please click here <a href="{{ $booking_link }}">Current Booking Status</a></p>

        <p><strong>Best Regards</strong></p>
        <p>www.{{ $website }}</p>
        <p>0208 111 1104.</p>
    </body>
</html>
