<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Helpers\referralCode;
use App\Http\Controllers\Api\CustomerAppController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerFeedback;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\SchedularController;
use App\Http\Controllers\Api\CarPoolKycController;
use App\Http\Controllers\Api\CarPoolJobController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\WebsiteBookingController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Route::get('/test', function () {
//     return response()->json([
//         'status' => true,
//         'message' => 'Customer API working'
//     ]);
// });


Route::post('loginOTP', [CustomerAuthController::class, 'loginOTP'])->name('loginOTP');
Route::post('verify-loginOTP', [CustomerAuthController::class, 'verifyLoginOTP'])->name('verifyLoginOTP');
Route::post('verify-loginOTP-web', [CustomerAuthController::class, 'verifyLoginOTPWeb'])->name('verifyLoginOTPWeb');
Route::post('force-register-web', [CustomerAuthController::class, 'forceRegister'])->name('forceRegister');
Route::post('postJob', [CustomerAppController::class, 'postJob'])->name('postJob');

Route::post('/s3-upload-image', [CustomerAppController::class, 'uploadImage']);
Route::post('/admin-autocomplete', [CustomerAppController::class, 'adminAutoCompleted']);

// Schedule
// Route::post('/schedule/available-dates', [CustomerAppController::class, 'available_dates']);
// Route::post('/schedule/get-dates', [CustomerAppController::class, 'get_dates']);
// Route::post('/open-cus/fetch-driver', [CustomerAppController::class, 's_fetch_driver']);

Route::post("getlocation-demo", [CustomerAppController::class, 'GoogleLocations_demo'])->name('GoogleLocations_demo');
Route::post("getdistance-demo", [CustomerAppController::class, 'DistanceAndDuration_demo'])->name('getdistance_demo');

Route::post("/bidder-vehicle", [CustomerAppController::class, 'getVehiclesBidder'])->name('getVehiclesBidder');
Route::post('/w-cancel-job', [CustomerAppController::class, 'webCancelJob']);

Route::post("/w-p-break-down", [CustomerAppController::class, 'webPaymentBreakDown'])->name('webPaymentBreakDown');
Route::post("/w-ctd-payment", [CustomerAppController::class, 'webCashOrder'])->name('webCashOrder');

Route::post('bookingOTP', [CustomerAuthController::class, 'bookingOTP'])->name('bookingOTP');
Route::post('bookingOTPverify', [CustomerAuthController::class, 'bookingOTPverify'])->name('bookingOTPverify');

Route::middleware(['customer.auth'])->group(function () {
    
    Route::get('/profile', function (Request $request) {

        $customer = $request->get('customer');
    
        // Check existing referral code
        $existing = DB::table('referral_codes')
            ->where('user_id', $customer->id)
            ->where('app_name', 'customer')
            ->first();
    
        if (!$existing) {
    
            $referral_code = referralCode::generateReferralCode();
    
            DB::table('referral_codes')->insert([
                'user_id'    => $customer->id,
                'app_name'   => 'customer',
                'code'       => $referral_code,
                'created_at' => now()
            ]);
            
            $existing = DB::table('referral_codes')
                ->where('user_id', $customer->id)
                ->where('app_name', 'customer')
                ->first();
    
        } else {
            $referral_code = $existing->code;
        }
    
        return response()->json([
            'status' => 'success',
            'data'   => $customer,
            'referral_code' => $referral_code,
'referral_content' => '🚖 Ride with Goride Today!

Sign up using my referral code and get ₹1000 wallet credits 💰
🔹 Referral Code: 
*'. $referral_code .'*
🔹 Fast, affordable & comfortable rides anytime
👉 Download and Book Now:',
            'app_link' => 'https://www.goride.run/customer-app',
            'total_invites' => $existing ? $existing->total_invites : null,
            'total_rewards' => $existing ? $existing->total_rewards : null
        ]);
    });
    
    Route::get('/socket-auth', function (Request $request) {

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $request->user()->id,
                'user_type' => 'customer',
                'name' => $request->user()->name,
            ]
        ]);

    });
    
    Route::post('initial-profile', [CustomerAuthController::class, 'initiateProfile'])->name('initiateProfile');
    
    Route::post('boot-status', [CustomerAuthController::class, 'bootStatus'])->name('bootStatus');
    
    // Locations APIs
    Route::post("getlocation", [CustomerAppController::class, 'GoogleLocations'])->name('GoogleLocations');
    Route::post("getdistance", [CustomerAppController::class, 'DistanceAndDuration'])->name('getdistance');
    Route::post("get-district", [CustomerAppController::class, 'GoogleDistrict'])->name('get-district');
    
    Route::get("get-seaters", [CustomerAppController::class, 'getSeaters'])->name('getSeaters');
    
    Route::post('fetch-pre-driver', [CustomerAppController::class, 's_fetch_driver']);
    
    Route::post('create-journey', [CustomerAppController::class, 'create_job']);
    
    Route::post("get-vehicle", [CustomerAppController::class, 'getVehicles'])->name('getVehicles');
    
    Route::post("payment-break-down", [CustomerAppController::class, 'paymentBreakDown'])->name('paymentBreakDown');
    
    Route::post("payment-initiate", [CustomerAppController::class, 'createOrder'])->name('createOrder');
    
    Route::post("cash-payment", [CustomerAppController::class, 'cashOrder'])->name('cashOrder');
    
    Route::post('/payment-verify', [CustomerAppController::class, 'paymentVerify']);
    
    Route::post('/booking-history', [CustomerAppController::class, 'bookingHistory']);
    
    Route::post('/view-contact', [CustomerAppController::class, 'viewContact']);
    
    Route::post('/cancel-job', [CustomerAppController::class, 'cancel_job']);
    
    // Feedback
    Route::post('ride/feedback', [CustomerFeedback::class, 'submitFeedback']);
    
    //Withdraw
    Route::post('/wallet/withdraw-request', [WalletController::class, 'withdrawRequestCustomer']);
    Route::post('/wallet/transaction', [WalletController::class, 'walletTransactionCustomer']);
    
    
    // Schedule
    Route::post('/date-wise-cheapest', [SchedularController::class, 'getDateWiseCheapest']);
    Route::post('/by-date', [SchedularController::class, 'getDriversByDate']);
    Route::post('/check-availability', [SchedularController::class, 'checkAvailability']);
    
    // S3 URL
    Route::post('/s3/presigned-url', [CustomerAppController::class, 'getPresignedUrl']);
    
    Route::post('/validate-referral-code', [CustomerAppController::class, 'validateCode']);
    
    Route::post('/request-book', [WebsiteBookingController::class, 'requestBook']);
    Route::post('/poll-status', [WebsiteBookingController::class, 'pollStatus']);
    Route::post('/active-request', [WebsiteBookingController::class, 'activeRequest']);
    Route::post('/cancel-request', [WebsiteBookingController::class, 'cancelRequest']);
    Route::post('/get-all-jobs-after-login', [WebsiteBookingController::class, 'get_all_jobs_after_login']);
    Route::get('/carpool-status', [WebsiteBookingController::class, 'carpoolStatus']);
    Route::post('/get-job-details', [WebsiteBookingController::class, 'getJobDetails']);
    Route::post("/w-ctd-payment-web", [WebsiteBookingController::class, 'webCashOrder']);
    
    // Car-Pool API's
    Route::post('/s3/presigned-url-carpool', [CarPoolKycController::class, 'getPresignedUrl']);
    
    Route::post('/kyc/selfie', [CarPoolKycController::class, 'selfieUpdate']);
    Route::post("/kyc/aadhar-ocr", [CarPoolKycController::class, 'docVerify'])->name('docVerify');
    Route::post("/kyc/digilocker-request", [CarPoolKycController::class, 'digioRequest'])->name('digioRequest');
    Route::post("/kyc/rc", [CarPoolKycController::class, 'rcVerify'])->name('rcVerify');
    Route::post("/kyc/dl-ocr", [CarPoolKycController::class, 'docVerifyDL'])->name('docVerifyDL');
    Route::post("/kyc/vehicle-upload", [CarPoolKycController::class, 'vehicleUpload'])->name('vehicleUpload');
    
    // Car-Pool Jobs
    Route::post('carpool/distance', [CarPoolJobController::class, 'DistanceAndDuration'])->name('DistanceAndDuration');
    
    Route::post("/carpool/create-job", [CarPoolJobController::class, 'createJob'])->name('createJob');
    Route::get('/carpool/my-jobs', [CarPoolJobController::class, 'myJobs'])->name('myJobs');
    Route::post('/carpool/open-post', [CarPoolJobController::class, 'postOpen'])->name('postOpen');
    
    // Car-Pool Invitation
    Route::post('/carpool/contacts/sync', [InvitationController::class, 'sync']);
    Route::get('/carpool/contacts/app-users', [InvitationController::class, 'appUsers']);

    Route::post('/carpool/invitations/send', [InvitationController::class, 'send']);
    Route::post('/carpool/invitations/accept', [InvitationController::class, 'accept']);
    Route::get('/carpool/invitations', [InvitationController::class, 'inviteList']);

    Route::get('/carpool/friends', [InvitationController::class, 'friendsList']);
    


});