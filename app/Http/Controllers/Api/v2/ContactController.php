<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Template\mailController;
use App\Rules\CustomRule;
// use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    public function getData($url, $dataArray)
    {
        $response = Http::get($url, $dataArray);
        // dd($response);

        if ($response->failed()) {
            return 'Request Error: ' . $response->body();
        } else {
            return $response->json();
        }
    }

    // public function contact(Request $request)
    // {
    //     // Validate request data
    //     $validator = Validator::make($request->all(), [
    //         'subject' => ['required', 'max:100'],
    //         'name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/'],
    //         'email' => ['required', 'email', 'max:70'],
    //         'mobile' => ['required'],
    //         'message' => ['required'],
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             "error" => 'validation_error',
    //             "message" => $validator->errors(),
    //         ], 422);
    //     }
    //     // Verify Google reCAPTCHA
    //     $urlGoogleCaptcha = 'https://www.google.com/recaptcha/api/siteverify';
    //     $recaptchaSecretKey = env('RECAPTCHA_SECRET');
    //     $verficationResponse = $request->input('google_response');
    //     $response = file_get_contents($urlGoogleCaptcha, false, stream_context_create([
    //         'http' => [
    //             'method' => 'POST',
    //             'header' => 'Content-Type: application/x-www-form-urlencoded',
    //             'content' => http_build_query([
    //                 'secret' => $recaptchaSecretKey,
    //                 'response' => $verficationResponse,
    //             ]),
    //         ],
    //     ]));
    //     $recaptchaResponse = json_decode($response, true);
    //     if (!$recaptchaResponse || !isset($recaptchaResponse['success']) || !$recaptchaResponse['success']) {
    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Google reCaptcha error',
    //             'error' => 'Google reCaptcha error',
    //         ]);
    //     }
    //     // Proceed with form submission logic
    //     $dubaidate_time = date('Y-m-d H:i:s');
    //     // Insert data into the database
    //     $data = DB::table('contact_us')->insert([
    //         'details' => '',
    //         'subject' => $request->subject,
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //         'message' => $request->message,
    //         'status' => '1',
    //         'ip' => $request->ip(),
    //         'datetime' => $dubaidate_time,
    //         'recapchae_req' => '',
    //         'recapchae_res' => '',
    //     ]);
    //     // Send email
    //     $subject = $request->subject;
    //     $email = 'noreply@goride.run';
    //     $data1 = [
    //         'subject' => $request->subject,
    //         'name' => $request->name,
    //         'message' => $request->message,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //     ];
    //     $message = mailController::contactus($data1);
    //     $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $message);
    //     // Return success response
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Thank you, we will get back to you soon',
    //         'data' => 'Thank you, we will get back to you soon',
    //     ]);
    // }



    public function contact(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'max:100'],
            'name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:70'],
            'mobile' => ['required'],
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => 'validation_error',
                "message" => $validator->errors(),
            ], 422);
        }

        // Verify Google reCAPTCHA
        $urlGoogleCaptcha = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptchaSecretKey = env('RECAPTCHA_SECRET');
        $verficationResponse = $request->input('google_response');

        // Make the HTTP request to Google reCAPTCHA API using Http::post
        $response = Http::asForm()->post($urlGoogleCaptcha, [
            'secret' => $recaptchaSecretKey,
            'response' => $verficationResponse,
        ]);

        // Parse the JSON response from the reCAPTCHA API
        $recaptchaResponse = $response->json();

        if (!$recaptchaResponse || !$recaptchaResponse['success']) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Google reCaptcha error',
                'error' => 'Google reCaptcha error',
            ]);
        }

        // Proceed with form submission logic
        $dubaidate_time = date('Y-m-d H:i:s');

        // Insert data into the database
        $data = DB::table('contact_us')->insert([
            'details' => '',
            'subject' => $request->subject,
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'message' => $request->message,
            'status' => '1',
            'ip' => $request->ip(),
            'datetime' => $dubaidate_time,
            'recapchae_req' => '',
            'recapchae_res' => '',
        ]);

        // Send email
        $subject = $request->subject;
        $email = env('SUPPORT_EMAIL');
        $data1 = [
            'subject' => $request->subject,
            'name' => $request->name,
            'message' => $request->message,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ];
        $message = mailController::contactus($data1);
        $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $message);

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Thank you, we will get back to you soon',
            'data' => 'Thank you, we will get back to you soon',
        ]);
    }

    // public function contact(Request $request)
    // {
    //     // Validate request data
    //     $validator = Validator::make($request->all(), [
    //         'subject' => ['required', 'max:100'],
    //         'name' => ['required', 'max:70', 'regex:/^[a-zA-Z\s]+$/'],
    //         'email' => ['required', 'email', 'max:70'],
    //         'mobile' => ['required'],
    //         'message' => ['required'],
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             "error" => 'validation_error',
    //             "message" => $validator->errors(),
    //         ], 422);
    //     }

    //     // Verify Google reCAPTCHA
    //     $urlGoogleCaptcha = 'https://www.google.com/recaptcha/api/siteverify';
    //     $recaptchaSecretKey = '6Ld4whMlAAAAAAL7wilDxst7lBZ-KMkI2yxfjNoo';
    //     $verficationResponse = $request->input('google_response');

    //     $response = file_get_contents($urlGoogleCaptcha, false, stream_context_create([
    //         'http' => [
    //             'method' => 'POST',
    //             'header' => 'Content-Type: application/x-www-form-urlencoded',
    //             'content' => http_build_query([
    //                 'secret' => $recaptchaSecretKey,
    //                 'response' => $verficationResponse,
    //             ]),
    //         ],
    //     ]));

    //     $recaptchaResponse = json_decode($response, true);

    //     if (!$recaptchaResponse || !isset($recaptchaResponse['success']) || !$recaptchaResponse['success']) {
    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Google reCaptcha error',
    //             'error' => 'Google reCaptcha error',
    //         ]);
    //     }

    //     // Proceed with form submission logic
    //     $dubaidate_time = date('Y-m-d H:i:s');
    //     // Insert data into the database
    //     $data = DB::table('contact_us')->insert([
    //         'details' => '',
    //         'subject' => $request->subject,
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //         'message' => $request->message,
    //         'status' => '1',
    //         'ip' => $request->ip(),
    //         'datetime' => $dubaidate_time,
    //         'recapchae_req' => '',
    //         'recapchae_res' => '',
    //     ]);

    //     // Send email
    //     $subject = $request->subject;
    //     $email = 'care@nationaldrawuae.com';
    //     $data1 = [
    //         'subject' => $request->subject,
    //         'name' => $request->name,
    //         'message' => $request->message,
    //         'email' => $request->email,
    //         'mobile' => $request->mobile,
    //     ];

    //     $message = mailController::contactus($data1);
    //     $sendEmail = Controller::composeEmail($request->ip(), $email, $subject, $message);

    //     // Return success response
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Thank you, we will get back to you soon',
    //         'data' => 'Thank you, we will get back to you soon',
    //     ]);
    // }
}
