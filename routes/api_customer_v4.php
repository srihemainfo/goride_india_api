<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Helpers\referralCode;
use App\Http\Controllers\Api\v4\CustomerAppController;
use App\Http\Controllers\Api\v4\CustomerAuthController;
use App\Http\Controllers\Api\v4\CustomerFeedback;
use App\Http\Controllers\Api\v4\WalletController;
use App\Http\Controllers\Api\v4\SchedularController;
use App\Http\Controllers\Api\v4\CarPoolKycController;
use App\Http\Controllers\Api\v4\CarPoolJobController;
use App\Http\Controllers\Api\v4\InvitationController;
use App\Http\Controllers\Api\v4\AgoraController;
use App\Http\Controllers\Api\v4\CustomerCallController;
use App\Http\Controllers\Api\v4\FamousPlaceController;
use App\Http\Controllers\Api\v4\ReferralClaimController;
use App\Http\Controllers\Api\v4\BannerController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


Route::post('/loginOTP', [CustomerAuthController::class, 'loginOTP'])->name('loginOTP');
Route::post('/verify-loginOTP', [CustomerAuthController::class, 'verifyLoginOTP'])->name('verifyLoginOTP');
Route::post('/verify-loginOTP-web', [CustomerAuthController::class, 'verifyLoginOTPWeb'])->name('verifyLoginOTPWeb');
Route::post('/force-register-web', [CustomerAuthController::class, 'forceRegister'])->name('forceRegister');
Route::post('/postJob', [CustomerAppController::class, 'postJob'])->name('postJob');

Route::post("/getlocation-demo", [CustomerAppController::class, 'GoogleLocations_demo'])->name('GoogleLocations_demo');
Route::post("/getdistance-demo", [CustomerAppController::class, 'DistanceAndDuration_demo'])->name('getdistance_demo');

Route::middleware(['customer.auth'])->group(function () {
    
    Route::post('/fcm-token', function (Request $request) {
        return response()->json(['message' => 'Not found'], 404);
    });
    
    Route::post('/carpool/push-job-chat-message', function (Request $request) {
        return response()->json(['message' => 'Not found'], 404);
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
    
    Route::get('/profile', [CustomerAuthController::class, 'profile'])->name('profile');
    Route::get('/users-profile', [CustomerAuthController::class, 'userProfile'])->name('userProfile');
    Route::post('/initial-profile', [CustomerAuthController::class, 'initiateProfile'])->name('initiateProfile');
    Route::post('/update-profile', [CustomerAuthController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/boot-status', [CustomerAuthController::class, 'bootStatus'])->name('bootStatus');
    Route::post('/delete-account', [CustomerAuthController::class, 'deleteAccount'])->name('deleteAccount');
    
    // Locations APIs
    Route::post("/getlocation", [CustomerAppController::class, 'GoogleLocations'])->name('GoogleLocations');
    Route::post("/getdistance", [CustomerAppController::class, 'DistanceAndDuration'])->name('getdistance');
    Route::post("/get-district", [CustomerAppController::class, 'GoogleDistrict'])->name('get-district');
    
    Route::post("/fetch-address", [CustomerAppController::class, 'fetchAddress'])->name('fetchAddress');
    
    Route::post('/saved-place/store', [CustomerAppController::class, 'savedPlaceStore']);
    Route::get('/saved-place/list', [CustomerAppController::class, 'savedPlaceList']);
    Route::post('/saved-place/update/{id}', [CustomerAppController::class, 'savedPlaceEdit']);
    Route::post('/saved-place/delete/{id}', [CustomerAppController::class, 'savedPlaceDelete']);
    
    Route::get("/get-seaters", [CustomerAppController::class, 'getSeaters'])->name('getSeaters');
    Route::post('/fetch-pre-driver', [CustomerAppController::class, 's_fetch_driver']);
    Route::post('/create-journey', [CustomerAppController::class, 'create_job']);
    Route::post("/get-vehicle", [CustomerAppController::class, 'getVehicles'])->name('getVehicles');
    Route::post("/payment-break-down", [CustomerAppController::class, 'paymentBreakDown'])->name('paymentBreakDown');
    Route::post("/payment-initiate", [CustomerAppController::class, 'createOrder'])->name('createOrder');
    Route::post("/cash-payment", [CustomerAppController::class, 'cashOrder'])->name('cashOrder');
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
    
    // Car-Pool API's
    Route::post('/s3/presigned-url-carpool', [CarPoolKycController::class, 'getPresignedUrl']);
    
    Route::post('/kyc/selfie', [CarPoolKycController::class, 'selfieUpdate']);
    Route::post("/kyc/aadhar-ocr", [CarPoolKycController::class, 'docVerify'])->name('docVerify');
    Route::post("/kyc/digilocker-request", [CarPoolKycController::class, 'digioRequest'])->name('digioRequest');
    Route::post("/kyc/rc", [CarPoolKycController::class, 'rcVerify'])->name('rcVerify');
    Route::post("/kyc/dl-ocr", [CarPoolKycController::class, 'docVerifyDL'])->name('docVerifyDL');
    Route::post("/kyc/vehicle-profile", [CarPoolKycController::class, 'vehicleUpload'])->name('vehicleUpload');
    
    // Car-Pool Invitation
    Route::post('/carpool/contacts/sync', [InvitationController::class, 'sync']);
    Route::get('/carpool/contacts/app-users', [InvitationController::class, 'appUsers']);
    Route::post('/carpool/invitations/send', [InvitationController::class, 'send']);
    Route::post('/carpool/invitations/accept', [InvitationController::class, 'accept']);
    Route::get('/carpool/invitations', [InvitationController::class, 'inviteList']);
    Route::get('/carpool/friends', [InvitationController::class, 'friendsList']);
    
    // Car-Pool Jobs
    Route::post('carpool/distance', [CarPoolJobController::class, 'DistanceAndDuration'])->name('DistanceAndDuration');
    Route::post("/carpool/create-job", [CarPoolJobController::class, 'createJob'])->name('createJob');
    Route::get('/carpool/my-jobs', [CarPoolJobController::class, 'myJobs'])->name('myJobs');
    Route::post('/carpool/open-post', [CarPoolJobController::class, 'postOpen'])->name('postOpen');
    Route::post('/carpool/cancel-job', [CarPoolJobController::class, 'cancelJob'])->name('cancelJob');
    
    // Car-Pool Search
    Route::get('/carpool/home-jobs', [CarPoolJobController::class, 'homeJobs']);
    Route::get('/carpool/job-info', [CarPoolJobController::class, 'jobInfo']);
    Route::post('/carpool/job-lock', [CarPoolJobController::class, 'jobLock']);
    
    Route::post('/carpool/search', [CarPoolJobController::class, 'carPoolSearch']);
    Route::post('/carpool/join-request', [InvitationController::class, 'joinRequest']);
    Route::post('/carpool/join-action', [InvitationController::class, 'acceptReject']);
    Route::post('/carpool/remove-invite', [InvitationController::class, 'removeInvite']);
    
    Route::get('/carpool/job-history', [CarPoolJobController::class, 'jobHistory']);
    Route::get('/carpool/past-job', [CarPoolJobController::class, 'pastJob']);
    
    
    // IN APP CALLING AGORA
    Route::post('/agora/token', [AgoraController::class, 'generateToken']);
    Route::post('/agora/refresh-token', [AgoraController::class, 'refreshToken']);
    Route::post('/call/start', [CustomerCallController::class, 'startCall']);
    Route::post('/call/accept', [CustomerCallController::class, 'acceptCall']);
    Route::post('/call/end', [CustomerCallController::class, 'endCall']);
    Route::get('/call/history', [CustomerCallController::class, 'callHistory']);
    Route::get('/call/status', [CustomerCallController::class, 'callStatus']);
    
    // Vehicle Updated V4
    
    Route::get('/get-vehicle', [CustomerAuthController::class, 'getVehicles']);
    Route::post('/upload-vehicle', [CustomerAuthController::class, 'uploadVehicle']);
    Route::post('/edit-vehicle', [CustomerAuthController::class, 'editVehicle']);
    
    Route::get('/notification-list', [InvitationController::class, 'notificationList']);
    
    // Manage Schedule Carpool
    Route::get('/carpool/schedule-list', [CarPoolJobController::class, 'scheduleList']);
    Route::get('/carpool/schedule-delete', [CarPoolJobController::class, 'deleteSchedule']);
    
    Route::get('/famous-spots/categories', [FamousPlaceController::class, 'categories']);
    Route::get('/famous-spots/places', [FamousPlaceController::class, 'places']);
    
    // Referral clain
    Route::get('referral-claim-eligibility', [ReferralClaimController::class, 'checkEligibility']);
    Route::post('referral-claim', [ReferralClaimController::class, 'claimThresholdReward']);
    
    //App Banners Statewise
    Route::get('/banners', [BannerController::class, 'getBanners']);

});