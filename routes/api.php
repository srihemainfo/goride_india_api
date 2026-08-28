<?php

use App\Http\Controllers\Api\AddcreditController;
use App\Http\Controllers\Api\authController;
// use App\Http\Controllers\Api\NewauthController2;
use App\Http\Controllers\Api\billingController;
use App\Http\Controllers\Api\cashTicket;
use App\Http\Controllers\Api\CCAvenueGateway;
// use App\Http\Controllers\Api\couponCode;
use App\Http\Controllers\Api\DeleteuserController;
use App\Http\Controllers\Api\DrawController;
use App\Http\Controllers\Api\drawsController;
use App\Http\Controllers\Api\FaqControllers;
use App\Http\Controllers\Api\getThanks;
use App\Http\Controllers\Api\HomeCoutroller;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\WhatsappController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MyprofileController;
use App\Http\Controllers\Api\networkGateway;
use App\Http\Controllers\Api\onlineTicket;
use App\Http\Controllers\Api\PlayController;
use App\Http\Controllers\Api\TermsController;
use App\Http\Controllers\Api\TicktviewController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WithdrawController;
use App\Http\Controllers\Api\ContactController;
// use App\Http\Controllers\Api\myTicketsController;
// use App\Http\Controllers\Api\cartonBilling;
use App\Http\Controller\Api\NewauthController;
use App\Http\Controllers\Api\pastDraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\twilioController;
use App\Http\Controllers\Api\dtSendTemplate;
use App\Http\Controllers\Api\OpenJobsController;
use App\Http\Controllers\Api\DigioKyc;
use App\Http\Controllers\Api\JobStreamController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\OpenJobCustomer;
use App\Http\Controllers\Api\WebsiteBookingController;
// use App\Http\Controllers\Api\ConsentController;
use App\Http\Controllers\Api\ManualKycUpload;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CustomerAppController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\MultipleVehicleController;
use App\Http\Controllers\Api\DriverScoreController;
use App\Http\Controllers\Api\CustomerAuthController;
// use App\Http\Controllers\Api\ReferralClaimController;
use App\Http\Controllers\Api\GlobalAuthController;

//website booking routes 30-06-2026
                
                

// Route::
//         namespace('API')->middleware('log.api.requests')->group(function () {
            // dd('hiiii');
            Route::get('/testxyz', function (Request $request) {
    
                return response()->json([
                    'success' => true,
                    'user' => 'hii'
                ]);
        
            });
            
            Route::get('api-booking-information/{key}', [CustomerAppController::class, 'apiBookingPreview']);
            Route::get('api-fetch-bidding/{key}', [CustomerAppController::class, 'apiFetchBidding'])->name('apiFetchBidding');

            Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
                return $request->user();
            });
            
            // Route::post('bookingOTPverify', [CustomerAuthController::class, 'bookingOTPverify'])->name('bookingOTPverify');
            
            Route::post('bookingOTP', [CustomerAuthController::class, 'bookingOTP'])->name('bookingOTP');
            Route::post('bookingOTPverify', [CustomerAuthController::class, 'bookingOTPverify'])->name('bookingOTPverify');
            
            //Newregister
            Route::post('bot-chat', [authController::class, 'bot_chat'])->name('bot_chat');
            
            Route::any('digio-kyc/callback', [DigioKyc::class, 'digio_callback'])->name('digio_callback');
            
            Route::post('newuserRegister', [NewauthController::class, 'newuserRegister'])->name('newuserRegister');
            
            Route::post('create-crm-whatsapp', [WhatsappController::class, 'create'])->name('crm_whatsapp_create');

            Route::post("showWinners", [PlayController::class, 'showWinners'])->name('showWinners');

            // SignUp First API
            // Route::post('userRegisters', [NewauthController2::class, 'userRegisters'])->name('userRegisters');
            // Route::post('login_new', [NewauthController2::class, 'login_new'])->name('login_new');

            Route::post('register', [authController::class, 'register'])->name('register');

            Route::post('signup', [authController::class, 'signup'])->name('signup');

            // GetWorld First API
            Route::get('getWorld', [authController::class, 'getWorld'])->name('getWorld');

            // GetWorld First API
            Route::get('getCountry', [authController::class, 'getCountry'])->name('getCountry');

            // GetWorld First API
            Route::post('getCity', [authController::class, 'getCity'])->name('getCity');

            //login
            Route::post('login', [LoginController::class, 'login'])->name('login');

            //login
            Route::post('loginWithPassword', [LoginController::class, 'loginWithPassword'])->name('loginWithPassword');

            // The Mobile/Email OTP Verification API
            Route::post('signupOTPVerify', [authController::class, 'signupOTPVerify'])->name('signupOTPVerify');
            Route::post('singupOTPResend', [authController::class, 'singupOTPResend'])->name('singupOTPResend');



            Route::post('verifyOTPsign', [authController::class, 'verifyOTPsign'])->name('verifyOTPsign');

            //email & mobile otp login
        
            Route::post('signin_otp', [LoginController::class, 'signin_otp'])->name('signin_otp');
            //resend otp
            Route::post('resend_otp', [LoginController::class, 'resend_otp'])->name('resend_otp');
            //otp verify
            Route::post('otp_verify', [LoginController::class, 'otp_verify'])->name('otp_verify');



            Route::post('loginOTP', [LoginController::class, 'loginOTP'])->name('loginOTP');
            Route::post('loginOTPverify', [LoginController::class, 'loginOTPverify'])->name('loginOTPverify');

            
            
            //forgot
            Route::post('forgot', [LoginController::class, 'forgot'])->name('forgot');


            Route::post('forgotRequest', [LoginController::class, 'forgotRequest'])->name('forgotRequest');



            Route::post('forgot_resendotp', [LoginController::class, 'forgot_resendotp'])->name('forgot_resendotp');

            Route::post('forgot_otpverify', [LoginController::class, 'forgot_otpverify'])->name('forgot_otpverify');

            Route::post('forgotOTPverify', [LoginController::class, 'forgotOTPverify'])->name('forgotOTPverify');


            Route::post('forgot_password_update', [LoginController::class, 'forgot_password_update'])->name('forgot_password_update');


            Route::post('forgotPasswordUpdate', [LoginController::class, 'forgotPasswordUpdate'])->name('forgotPasswordUpdate');

            Route::any("logout_message", [LoginController::class, 'logout_message'])->name('logout_message');

            Route::any("logError", [LoginController::class, 'logError'])->name('logError');


            //   product list
            Route::post("productList", [PlayController::class, 'productList'])->name('productList');
            //   faq list
            Route::post("faq", [FaqControllers::class, 'faq'])->name('faq');

            //   terms & conditions
            Route::post("terms", [TermsController::class, 'terms'])->name('terms');
            // home page
            Route::post("home", [HomeCoutroller::class, 'home'])->name('home');
            Route::post("lastedraw", [HomeCoutroller::class, 'lastedraw'])->name('lastedraw');

            // Route::post("transaction_list", [TransactionController::class, 'transaction_list'])->name('transaction_list');
        
            Route::post("winner_count", [HomeCoutroller::class, 'winner_count'])->name('winner_count');

            //Previous Draw
            Route::post("previous_draw", [DrawController::class, 'previous_draw'])->name('previous_draw');
            //previous Draw view
        
            Route::post("previous_drawview", [DrawController::class, 'previous_drawview'])->name('previous_drawview');

            //previous Draw Video view
        
            Route::post("previous_youtubevideo", [DrawController::class, 'previous_youtubevideo'])->name('previous_youtubevideo');
            //winner past draw
            Route::post("winner_pastdraw", [DrawController::class, 'winner_pastdraw'])->name('winner_pastdraw');
            //winner pastdraw individual view
            Route::post("winner_pastdrawview", [DrawController::class, 'winner_pastdrawview'])->name('winner_pastdrawview');
            Route::post("ticketView", [TicktviewController::class, 'ticketView'])->name('ticketView');
            Route::post("invoiceView", [InvoiceController::class, 'invoiceView'])->name('invoiceView');

            // Route::post("contact_us", [ContactusControllers::class, 'contact_us'])->name('contact_us');
        
            Route::post("country", [MyprofileController::class, 'country'])->name('country');
            Route::post("state", [MyprofileController::class, 'state'])->name('state');
            Route::post("city", [MyprofileController::class, 'city'])->name('city');
            Route::post("contact", [ContactController::class, 'contact'])->name('contact');

            Route::post("tempEmail", [getThanks::class, 'tempEmail'])->name('tempEmail');


            Route::post("BluckdoS3upload", [getThanks::class, 'BluckdoS3upload'])->name('BluckdoS3upload');


            Route::post("getPastDrawVideos", [getThanks::class, 'getPastDrawVideos'])->name('getPastDrawVideos');

            Route::post("getFindKisok", [getThanks::class, 'getFindKisok'])->name('getFindKisok');

            Route::post("storeUserJourney", [getThanks::class, 'storeUserJourney'])->name('storeUserJourney');

            //   Get Active Draw API - 16-9-2023   PRAKASH
            Route::match(['get', 'post'], "getActiveDraw", [drawsController::class, 'getActiveDraw'])->name('getActiveDraw');


            Route::post("dynamicLink", [getThanks::class, 'dynamicLink'])->name('dynamicLink');





            Route::match(['get', 'post'], "getSettings", [drawsController::class, 'getSettings'])->name('getSettings');


            // pastDrawList Phase 2
            Route::get("pastDrawList", [pastDraw::class, 'pastDrawList'])->name('pastDrawList');
            Route::post("pastDrawDetails", [pastDraw::class, 'pastDrawDetails'])->name('pastDrawDetails');



            //   WORK WITH US API - 12-12-2023  SURYA
            // Route::post('/retailer', [WorkwithusController::class, 'index']);
            // Route::post('/agent', [WorkwithusController::class, 'Agent']);
        


            Route::post("twilioSMS", [twilioController::class, 'twilioSMS'])->name('twilioSMS');



            // Double Ticket Send Template
            Route::post("dtSendTemplate", [dtSendTemplate::class, 'dtSendTemplate'])->name('dtSendTemplate');
            Route::match(['get', 'post'], "dtCallBack", [dtSendTemplate::class, 'dtCallBack'])->name('dtCallBack');

            Route::match(['get', 'post'], "bmsmscallback", [dtSendTemplate::class, 'bmsmscallback'])->name('bmsmscallback');

            // ================ DEVANATHAN K ==================================================
        
            // Route::post('deleterequest', [authController::class, 'deleterequest'])->name('deleterequest');
        
            Route::post('Upcoming_Prizes', [authController::class, 'Upcoming_Prizes'])->name('Upcoming_Prizes');

            // Route::post('Download_pdf_whatsapp', [authController::class, 'Download_pdf_whatsapp'])->name('Download_pdf_whatsapp');
        
            //   plan list
            Route::post("planList", [PlayController::class, 'planList'])->name('planList');

            Route::post("checkDomainStatus", [PlayController::class, 'checkDomainStatus'])->name('checkDomainStatus');

            Route::post("cancel-subPlan", [CCAvenueGateway::class, 'cancelSubPlan'])->name('cancelSubPlan');
            

            Route::middleware(['driver.auth'])->group(function () {
                //   Dashboard
                Route::post("dashboard", [PlayController::class, 'dashboard'])->name('dashboard');
                
                Route::post("crm-activate", [PlayController::class, 'crmActivation'])->name('crmActivation');

                Route::post("generateCRM", [PlayController::class, 'generateCRM'])->name('generateCRM');
                
                Route::post("generateCRM-mobile", [PlayController::class, 'generateCRM_mobile'])->name('generateCRM_mobile');
                
                Route::post("createfaretype", [PlayController::class, 'createfaretype'])->name('createfaretype');
                
                Route::post("checkfaretype", [PlayController::class, 'checkfaretype'])->name('checkfaretype');

                Route::post("deleteIdProof", [PlayController::class, 'deleteIdProof'])->name('deleteIdProof');

                // Route::post('deleterequest', [authController::class, 'deleterequest'])->name('deleterequest');
        
                Route::post('deleteRequest', [PlayController::class, 'deleteRequest'])->name('deleteRequest');

                Route::post('updateDetails', [PlayController::class, 'updateDetails'])->name('updateDetails');

                //   Amount transfer to wallet
                Route::post("transfer_towallet", [PlayController::class, 'transfer_towallet'])->name('transfer_towallet');

                Route::post("product_addto_cart", [PlayController::class, 'product_addto_cart'])->name('product_addto_cart');

                Route::post("pay_bylink", [PlayController::class, 'pay_bylink'])->name('pay_bylink');

                //  Product add to cart
        
                Route::post("delete_image", [MyprofileController::class, 'delete_image'])->name('delete_image');

                // ================ DEVANATHAN K 23-04-2024 image upload==================================================
                Route::post("user_image_upload1", [MyprofileController::class, 'user_image_upload1'])->name('user_image_upload1');

                Route::post('Download_pdf_whatsapp', [authController::class, 'Download_pdf_whatsapp'])->name('Download_pdf_whatsapp');


                Route::post("customer", [MyprofileController::class, 'customer'])->name('customer');


                Route::post("myprofile", [MyprofileController::class, 'myprofile'])->name('myprofile');
                Route::post("emailupdate_otp", [MyprofileController::class, 'emailupdate_otp'])->name('emailupdate_otp');
                Route::post("emailupdate_resendotp", [MyprofileController::class, 'emailupdate_resendotp'])->name('emailupdate_resendotp');
                Route::post("emailupdate", [MyprofileController::class, 'emailupdate'])->name('emailupdate');
                Route::post("updateMyprofile", [getThanks::class, 'updateMyprofile'])->name('updateMyprofile');
                Route::post("img_delete", [MyprofileController::class, 'img_delete'])->name('img_delete');
                Route::post("profile_update", [MyprofileController::class, 'MyprofileUpdate']);
                Route::post("change_password", [MyprofileController::class, 'changePassword']);
                Route::post("showTransaction", [PlayController::class, 'showTransaction'])->name('showTransaction');
                Route::post("Withdrawalhistory", [PlayController::class, 'Withdrawalhistory'])->name('Withdrawalhistory');
                Route::post("Wallethistory", [PlayController::class, 'Wallethistory'])->name('Wallethistory');


                Route::post("packageHistory", [PlayController::class, 'packageHistory'])->name('packageHistory');

                Route::post("withdraw_details", [WithdrawController::class, 'withdraw_details'])->name('withdraw_details');
                Route::post("withdraw", [WithdrawController::class, 'withdraw'])->name('withdraw');
                Route::post("drop_nation", [WithdrawController::class, 'drop_nation'])->name('drop_nation');
                Route::post("drop_live", [WithdrawController::class, 'drop_live'])->name('drop_live');

                // Route::post("myticket", [TicketshowController::class, 'myticket'])->name('myticket');
        
                Route::post("myTicket", [billingController::class, 'myTicket'])->name('myTicket');


                Route::post("requestGold", [billingController::class, 'requestGold'])->name('requestGold');


                // Get Invoice List
                Route::post("getInvoiceList", [billingController::class, 'getInvoiceList'])->name('getInvoiceList');


                Route::post("password_change", [MyprofileController::class, 'password_change'])->name('password_change');


                //tranction list
                Route::post("transaction_list", [TransactionController::class, 'transaction_list'])->name('transaction_list');
                //withdraw Details
                Route::post("withdraw_data", [WithdrawController::class, 'withdraw_data'])->name('withdraw_data');

                Route::post("delete_account", [DeleteuserController::class, 'delete_account'])->name('delete_account');


                //add credit
                Route::post("card_details", [AddcreditController::class, 'card_details'])->name('card_details');
                Route::post("cartons_stored", [AddcreditController::class, 'cartons_stored'])->name('cartons_stored');

                Route::post("payment_status", [AddcreditController::class, 'payment_status'])->name('payment_status');
                Route::post("thanks_status", [AddcreditController::class, 'thanks_status'])->name('thanks_status');


                Route::any("logout", [LoginController::class, 'logout'])->name('logout');

                //   Add To Cart API - 17-9-2023   PRAKASH
                Route::post("VerifyDetails", [billingController::class, 'VerifyDetails'])->name('VerifyDetails');

                //   Add To Cart API - 17-9-2023   PRAKASH
                Route::post("addToCardProduct", [billingController::class, 'addToCardProduct'])->name('addToCardProduct');
                
                Route::post("changeSubscription", [billingController::class, 'changeSubscription'])->name('changeSubscription');


                // Update Address Book
                Route::post("updateAddressBook", [PlayController::class, 'updateAddressBook'])->name('updateAddressBook');



                Route::post("getSuggestion", [billingController::class, 'getSuggestion'])->name('getSuggestion');

                Route::post("myWinnings", [billingController::class, 'myWinnings'])->name('myWinnings');


                Route::post("transferToWallet", [billingController::class, 'transferToWallet'])->name('transferToWallet');



                Route::post("AdTransferToWallet", [billingController::class, 'AdTransferToWallet'])->name('AdTransferToWallet');




                //  get User Product Cart API - 17-9-2023   PRAKASH
                Route::post("getUserProductCart", [billingController::class, 'getUserProductCart'])->name('getUserProductCart');

                //  Net Work Payment Gate Way Initiate API - 17-9-2023   PRAKASH
                Route::post("networkInitiate", [networkGateway::class, 'networkInitiate'])->name('networkInitiate');

                //  Net Work Payment Gate Way Success API - 17-9-2023   PRAKASH
                Route::post("networkSuccess", [networkGateway::class, 'networkSuccess'])->name('networkSuccess');

                //  The Online Ticket Generation API - 17-9-2023   PRAKASH
                Route::post("buyCRM", [onlineTicket::class, 'onlineTicketGeneration'])->name('onlineTicketGeneration');

                //  The Online Ticket Generation API - 17-9-2023   PRAKASH
                Route::post("buyTrailCRM", [onlineTicket::class, 'buyTrailCRM'])->name('buyTrailCRM');
                
                Route::post("buyTrailCRM-mobile", [onlineTicket::class, 'buyTrailCRM_mobile'])->name('buyTrailCRM');




                Route::post("walletTicketGeneration", [onlineTicket::class, 'walletTicketGeneration'])->name('walletTicketGeneration');

                //  The Online Ticket Generation API - 17-9-2023   PRAKASH
                Route::post("getThanks", [getThanks::class, 'getThanks'])->name('getThanks');


                Route::post("razorpayInitiate", [CCAvenueGateway::class, 'ccavenueInitiate'])->name('ccavenueInitiate');

                Route::post("razorpaySubInitiate", [CCAvenueGateway::class, 'razorpaySubInitiate'])->name('razorpaySubInitiate');




                Route::post("cancelSubCRM", [CCAvenueGateway::class, 'cancelSubCRM'])->name('cancelSubCRM');




                Route::post("razorpaySuccess", [CCAvenueGateway::class, 'ccavenueSuccess'])->name('ccavenueSuccess');

                Route::post("paypalInitiate", [CCAvenueGateway::class, 'paypalInitiate'])->name('paypalInitiate');


                Route::post("paypalSubInitiate", [CCAvenueGateway::class, 'paypalSubInitiate'])->name('paypalSubInitiate');



                Route::post("paypalSuccess", [CCAvenueGateway::class, 'paypalSuccess'])->name('paypalSuccess');


                Route::post("couponCode", [onlineTicket::class, 'couponCode'])->name('couponCode');

                //  coupon code Success API - 23-9-2023   PRAKASH
                Route::post("cbTicketCheck", [cashTicket::class, 'cashTicketCheck'])->name('cbTicketCheck');

                //  coupon code Success API - 23-9-2023   PRAKASH
                // Route::post("cashTicketGenerate", [cashTicket::class, 'cashTicketGenerate'])->name('cashTicketGenerate');
        
                //  coupon code Success API - 23-9-2023   PRAKASH
                // Route::post("bonusTicketGenerate", [cashTicket::class, 'bonusTicketGenerate'])->name('bonusTicketGenerate');
        


                // Route::post("cartonBilling", [cartonBilling::class, 'addToCardCarton'])->name('cartonBilling');
        
                // Route::post("cartonPurchase", [cartonBilling::class, 'cartonPurchase'])->name('cartonPurchase');
        

                // Route::post("getPurchaseSEO", [getThanks::class, 'getPurchaseSEO'])->name('getPurchaseSEO');
        
                Route::post("getMobileOTP", [getThanks::class, 'getMobileOTP'])->name('getMobileOTP');


                Route::post("mobileUpdate", [getThanks::class, 'mobileUpdate'])->name('mobileUpdate');

                Route::post("getWithDrawDetails", [getThanks::class, 'getWithDrawDetails'])->name('getWithDrawDetails');

                Route::post("deleteImage", [getThanks::class, 'deleteImage'])->name('deleteImage');

                Route::post("doS3upload", [getThanks::class, 'doS3upload'])->name('doS3upload');


                Route::post("withdrawRequest", [billingController::class, 'withdrawRequest'])->name('withdrawRequest');


                Route::post("checkValidity", [getThanks::class, 'checkValidity'])->name('checkValidity');



                Route::post("deleteCart", [billingController::class, 'deleteCart'])->name('deleteCart');

                Route::post("fmRegister", [drawsController::class, 'fmRegister'])->name('fmRegister');
                
                
                
                
                
                // OPEN JOBS APIS DEVELOPED BY ELAVARASAN
                
                
                Route::post("getlocation", [OpenJobsController::class, 'GoogleLocations'])->name('GoogleLocations');
                Route::post("getdistance", [OpenJobsController::class, 'DistanceAndDuration'])->name('getdistance');
                Route::post("get-district", [OpenJobsController::class, 'GoogleDistrict'])->name('get-district');
                
                Route::post("update-profile", [OpenJobsController::class, 'update_profile'])->name('update-profile');
                
                Route::post("pro-update", [OpenJobsController::class, 'pro_update'])->name('pro_update');
                
                Route::post("get-profile", [OpenJobsController::class, 'get_profile'])->name('get-profile');
                
                Route::post("create-job", [OpenJobsController::class, 'create_job'])->name('create-job');
                
                Route::post("v2/create-job", [OpenJobsController::class, 'create_job_v2'])->name('create-job-v2');
                
                Route::post("get-jobs", [OpenJobsController::class, 'get_all_jobs'])->name('get-all-jobs');
                Route::post("my-current-jobs", [OpenJobsController::class, 'my_current_jobs'])->name('my-current-jobs');
                Route::post("my-past-jobs", [OpenJobsController::class, 'my_past_jobs'])->name('my-past-jobs');
                
                Route::post("create-bid", [OpenJobsController::class, 'create_bid'])->name('create-bid');
                
                Route::post("get-jobinfo", [OpenJobsController::class, 'get_jobinfo'])->name('get-jobinfo');
                
                Route::post("agree_job", [OpenJobsController::class, 'agree_job'])->name('agree_job');
                
                // Route::post("get-bid", [OpenJobsController::class, 'get_bid'])->name('get-bid');
                
                Route::post("accept-job", [OpenJobsController::class, 'accept_job'])->name('accept-job');
                
                Route::post("reject-job", [OpenJobsController::class, 'reject_bid'])->name('reject-job');
                
                Route::post("bidding-status", [OpenJobsController::class, 'bidding_status'])->name('bidding-status');
                
                Route::post("job-owner", [OpenJobsController::class, 'job_owner'])->name('job-owner');
                
                Route::post("update-like", [OpenJobsController::class, 'update_like'])->name('update-like');
                
                Route::post("liked-jobs", [OpenJobsController::class, 'liked_jobs'])->name('liked-jobs');
                
                Route::post("prefered-loc-store", [OpenJobsController::class, 'preferedLoc_store'])->name('prefered-loc-store');
                
                Route::post("get-prefered-loc", [OpenJobsController::class, 'preferedLoc_get'])->name('prefered-loc-get');
                
                Route::post("cancel-job", [OpenJobsController::class, 'cancel_job'])->name('cancel_job');
                
                Route::post("cancel-bid", [OpenJobsController::class, 'cancel_bid'])->name('cancel_bid');
                
                Route::post("report-user", [OpenJobsController::class, 'report_user'])->name('report-user');
                
                Route::post("notify-status", [OpenJobsController::class, 'notify_status'])->name('notify-status');
                
                Route::post("assigned-job", [OpenJobsController::class, 'assigned_job'])->name('assigned-job');
                
                // Browser FCM Token
                
                Route::post('/save-fcm-token', function (Illuminate\Http\Request $request) {
                    $request->validate(['token' => 'required|string']);
                    
                    if (auth()->check()) {
                        auth()->user()->update(['browser_fcm_token' => $request->token]);
                    }
                    
                    return response()->json(['success' => true]);
                });
                
                Route::post('/s3/presigned-url', [DigioKyc::class, 'getPresignedUrl']);
                
                
                // KYC Start -------
                
                Route::post("kyc/selfie", [DigioKyc::class, 'selfie_update'])->name('selfie_update');
                Route::post("boot/status", [DigioKyc::class, 'boot_status'])->name('boot_status');
                Route::post("kyc/request", [DigioKyc::class, 'new_request'])->name('new_request');
                Route::post("kyc/ocr", [DigioKyc::class, 'doc_verify'])->name('doc_verify');
                Route::post("kyc/ocr/dl", [DigioKyc::class, 'doc_verify_dl'])->name('doc_verify_dl');
                Route::post("kyc/owner-proof", [DigioKyc::class, 'owner_proof'])->name('owner_proof');
                Route::post("kyc/rc", [DigioKyc::class, 'rc_verify'])->name('rc_verify');
                
                
                // KYC End -------
                
                
                // Driver Binding with Owner Start ----------
                
                Route::post('send-otp-owner', [LoginController::class, 'driverBindOTP'])->name('driverBindOTP');
                Route::post('verify-otp-owner', [LoginController::class, 'driverBindOTPverify'])->name('driverBindOTPverify');
                
                Route::post('owner-pro', [OpenJobsController::class, 'ownerPro'])->name('ownerPro');
                
                Route::post('drivers-list', [OpenJobsController::class, 'driversList'])->name('driversList');
                
                Route::post('driver-access-modify', [OpenJobsController::class, 'driverAccessModify'])->name('driverAccessModify');
                
                Route::post('assign-driver', [OpenJobsController::class, 'assignDriver'])->name('assignDriver');
                
                Route::post('re-assign-driver', [OpenJobsController::class, 're_assignDriver'])->name('re_assignDriver');
                
                Route::post('remove-owner', [OpenJobsController::class, 'removeOwner'])->name('removeOwner');
                // Driver Binding with Owner End ----------
                
                Route::post("user-vehicle", [OpenJobsController::class, 'user_vehicle'])->name('user_vehicle');
                
                // Wallet Top Up Start -----------
                
                Route::post('/wallet/create-order', [WalletController::class, 'createOrder']);

                Route::post('/wallet/verify-payment', [WalletController::class, 'verifyPayment']);

                Route::post('/wallet/withdraw-request', [WalletController::class, 'withdrawRequest']);

                Route::post('/wallet/transfer', [WalletController::class, 'transferToWallet']);
                
                Route::post('/wallet/transaction', [WalletController::class, 'walletTransaction']);
                
                // Wallet Top Up End -------------
                
                
                // Bank Details Start -----------
                Route::post('/update-bank-details', [authController::class, 'update_bank']);
                // Bank Details End -------------
                
                // Schedule Job Start -----------
                // OpenJobCustomer
                
                Route::post('/schedule/create', [OpenJobsController::class, 'schedule_create']);
                Route::post('/schedule/check-dates', [OpenJobsController::class, 'schedule_check_dates']);
                Route::post('/schedule/available-dates', [OpenJobsController::class, 'available_dates']);
                Route::post('/schedule/get-dates', [OpenJobsController::class, 'get_dates']);
                
                Route::post('/schedule/edit-loc', [OpenJobsController::class, 'schedule_edit']);
                Route::post('/schedule/extend-date', [OpenJobsController::class, 'extend_date']);
                Route::post('/schedule/past-list', [OpenJobsController::class, 'get_past_dates']);
                Route::post('/schedule/delete-loc', [OpenJobsController::class, 'schedule_delete']);
                
                // Schedule Job End -----------
                
                // Open Job Customer Start -----------
                Route::post('/open-cus/fetch-driver', [OpenJobsController::class, 's_fetch_driver']);
                
                Route::post('/open-cus/create-job', [OpenJobsController::class, 'c_create_job']);
                
                Route::post('/open-cus/fetch-journey', [OpenJobCustomer::class, 'fetch_journey']);
                
                Route::post('/confirm-availability', [OpenJobsController::class, 'confirmAvailability']);
                
                
                // Open Job Customer End -----------
                
                // // Route::post('/consent/create', [ConsentController::class, 'create']);
                
                
                // Multiple Vehicle
                Route::post('/store-vehicle', [MultipleVehicleController::class, 'store']);
                
                Route::post('/ride-otp-verify', [OpenJobsController::class, 'optVerify']);
                
                Route::post('/complete-ride', [OpenJobsController::class, 'completeRide']);
                
                Route::post('/cancel-ride', [OpenJobsController::class, 'cancelRide']);
                
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
                
            });
            
            // User form Generate
            // Route::post('/consent/create', [ConsentController::class, 'create']);
            
            // Realtime Bidding Apis Start -----------
            
            Route::get('/jobs/{job}/stream', [JobStreamController::class, 'stream']);
            
            Route::post('job/action', [OpenJobsController::class, 'job_action'])->name('job_action');
              Route::post('/get-route-polyline', [AdminApiController::class, 'getRoutePolyline']);
    Route::post('/admin-withdraw-transfer', [AdminApiController::class, 'adminWithdrawTransfer']);
    Route::post('/admin-withdraw-decline-form', [AdminApiController::class, 'adminWithdrawDeclineForm']);
    Route::post('/admin-withdraw-reject', [AdminApiController::class, 'adminWithdrawReject']);
            // Realtime Bidding Apis End -----------
            Route::post("get-vehicles", [OpenJobsController::class, 'get_vehicles'])->name('get_vehicles');
            Route::post("admin-kyc/ocr", [DigioKyc::class, 'admin_doc_verify'])->name('admin_doc_verify');
    Route::post("admin-kyc/ocr/dl", [DigioKyc::class, 'admin_doc_verify_dl'])->name('admin_doc_verify_dl');
    Route::post("admin-kyc/rc", [DigioKyc::class, 'admin_rc_verify'])->name('admin_rc_verify');
            
            //new routes 26-05-2026
            Route::post('/trigger-selfie-push', [AdminApiController::class, 'triggerSelfieVerify']);
            Route::post('/trigger-dl-push', [AdminApiController::class, 'triggerDlVerify']);
            Route::post('/trigger-vehicle-push', [AdminApiController::class, 'triggerVehicleVerify']);
            
            Route::get('/whatsapp/fetch-missing-templates', [AdminApiController::class, 'fetchMissingTemplates']);
    Route::post('/whatsapp/sync-selected-templates', [AdminApiController::class, 'syncSelectedTemplates']);
            Route::post('/admin-send-mobile-push', [AdminApiController::class, 'sendMobilePushNotification']);
            Route::post("send-push-notify", [OpenJobsController::class, 'admin_pushNotify'])->name('admin_pushNotify');
            Route::post('/admin-resend-push-driver', [AdminApiController::class, 'resendPushNotificationDriver']);
            Route::post('/admin-send-specific-push-driver', [AdminApiController::class, 'sendSpecificUserPushNotificationDrivers']);
            Route::post('/admin-cancel-scheduled-job', [AdminApiController::class, 'adminCancelScheduledJob']);
            Route::post('/admin-search-map-drivers', [AdminApiController::class, 'searchMapDrivers']);
            Route::post("send-template", [OpenJobsController::class, 'send_template'])->name('send_template');
            Route::post('/whatsapp/delete-template', [AdminApiController::class, 'deleteFacebookTemplate']);
            Route::post('/whatsapp/request-approval', [AdminApiController::class, 'requestTemplateApproval']);
            Route::post('/whatsapp/sync-approval', [AdminApiController::class, 'syncTemplateStatus']);
            Route::post('/admin-expired-jobs', [AdminApiController::class, 'getExpiredJobs']);
            Route::post("admin-cancel-job", [AdminApiController::class, 'cancel_job'])->name('cancel_job');
            Route::post("admin-cancel-job-assigned", [AdminApiController::class, 'cancel_assigned_job'])->name('cancel_assigned_job');
            Route::post("admin-delete-job", [OpenJobsController::class, 'delete_job'])->name('delete_job');
            Route::post('/admin-get-driver-location', [AdminApiController::class, 'getDriverCurrentLocation']);
            Route::post("check-app-version", [OpenJobsController::class, 'check_appVersion'])->name('check_appVersion');
            
            Route::post("admin-block-user", [OpenJobsController::class, 'block_user'])->name('block_user');
            
            Route::post("adminUserCancelJobs", [OpenJobsController::class, 'cancel_all_jobs_of_user'])->name('cancel_all_jobs_of_user');
            
            Route::post("notify-jobs", [OpenJobsController::class, 'notify_jobs'])->name('notify-jobs');
            
            // Admin Api Datas
            Route::post("get-location", [OpenJobsController::class, 'GoogleLocations_admin'])->name('GoogleLocations_admin');
            Route::post("get-distance", [OpenJobsController::class, 'DistanceAndDuration_admin'])->name('getdistance_admin');
            
            // Admin Manual KYC
            Route::post("manual-kyc/presigned-url", [ManualKycUpload::class, 'getPresignedUrl'])->name('getPresignedUrl');
            Route::post("manual-kyc/delete-s3-object", [ManualKycUpload::class, 'deleteS3Object'])->name('deleteS3Object');
            
            Route::post("manual-kyc/selfie-update", [ManualKycUpload::class, 'selfie_update'])->name('selfie_update');
            Route::post("manual-kyc/aadhar-update", [ManualKycUpload::class, 'doc_verify'])->name('doc_verify');
            Route::post("manual-kyc/dl-update", [ManualKycUpload::class, 'doc_verify_dl'])->name('doc_verify_dl');
            
            
            Route::post("web-getlocation", [OpenJobsController::class, 'GoogleLocationsAll'])->name('GoogleLocations');
            Route::post("admin-web-getlocation", [OpenJobsController::class, 'AdminGoogleLocationsAll'])->name('AdminGoogleLocations');
            
            
            Route::post("web-getdistance", [CustomerAppController::class, 'DistanceAndDurationAll'])->name('getdistanceall');
            Route::post("admin-get-fare", [AdminApiController::class, 'Admin_get_fare']);
            Route::post("admin-web-getdistance", [CustomerAppController::class, 'AdminDistanceAndDurationAll'])->name('admingetdistanceall');
            Route::post("web-book-journey", [CustomerAppController::class, 'bookJourney'])->name('bookJourney');
            Route::post("admin-web-book-journey", [CustomerAppController::class, 'AdminbookJourney'])->name('AdminbookJourney');
            Route::post("web-send-bookinfo", [CustomerAppController::class, 'sendWhatsappBook'])->name('sendWhatsappBook');
            // Route::post("admin-web-send-bookinfo", [CustomerAppController::class, 'AdminsendWhatsappBook'])->name('AdminsendWhatsappBook');
            
            Route::post("address-autocomplete", [CustomerAppController::class, 'addressAutocomplete'])->name('addressAutocomplete');
            Route::post("admin-address-autocomplete", [CustomerAppController::class, 'adminaddressAutocomplete'])->name('adminaddressAutocomplete');
            
            Route::any("whatsapp/noreply-webhook", [CustomerAppController::class, 'whNoreplyWebhook'])->name('whNoreplyWebhook');
            
            Route::middleware(['admin.apis'])->group(function () {
                
                Route::post("admin-create-bid", [OpenJobsController::class, 'admin_create_bid'])->name('admin-create-bid');

                Route::post('/rm-req-list', [AdminApiController::class, 'rmReqeuestList']);
                Route::post('/admin-withdraw-list', [AdminApiController::class, 'adminWithdrawList']);
                Route::post('/admin-job-list', [AdminApiController::class, 'adminJobList']);
                Route::post('/admin-accept-bidder', [AdminApiController::class, 'admin_accept_job'])->name('paymentBreakDownConfirm');
                Route::post('/admin-job-edit', [AdminApiController::class, 'adminJobEdit'])->name('adminJobEdit');
                
                Route::post('/admin-get-passenger-details', [AdminApiController::class, 'adminGetPassengerDetails']);
                Route::post('/admin-get-driver-details', [AdminApiController::class, 'adminGetDriverDetails']);
                Route::post('/admin-get-driver-list', [AdminApiController::class, 'adminGetDriverList']);
                Route::post('/admin-update-job', [AdminApiController::class, 'adminUpdateJob']);
                // Route::post("admin-reject-job", [OpenJobsController::class, 'reject_bid_admin'])->name('reject_bid_admin');
                Route::post('/admin-driver-profile-update', [DriverScoreController::class, 'updateDriverScore']);
                Route::post("admin-reject-bid", [OpenJobsController::class, 'reject_bid_admin'])->name('reject_bid_admin');
                // Route::post('/admin-cancel-scheduled-job', [AdminApiController::class, 'adminCancelScheduledJob']);
                Route::post('/admin-scheduled-job-list', [AdminApiController::class, 'adminScheduledJobList']);
                 Route::post('/admin-send-specific-push-job', [AdminApiController::class, 'sendSpecificDriverPushNotificationJob']);
                Route::post('/admin-send-push', [AdminApiController::class, 'sendAdminPushNotification']);
                Route::post('/admin-resend-job-push', [AdminApiController::class, 'admin_resend_job_push']);
                Route::post('/admin-resend-push', [AdminApiController::class, 'resendPushNotification']);
                Route::post('/admin-send-specific-push', [AdminApiController::class, 'sendSpecificUserPushNotification']);
                Route::post('/send-customer-push', [AdminApiController::class, 'sendCustomerPushNotification']);
                Route::post('/get-fare', [AdminApiController::class, 'getFareAutomation']);
                
                Route::get('/google-locations', [WebsiteBookingController::class, 'GoogleLocations']);
                Route::post('/get-all-jobs-web', [WebsiteBookingController::class, 'get_all_jobs']);
                Route::post('/send-otp', [WebsiteBookingController::class, 'sendWebOtp']);
                Route::post('/verify-otp', [WebsiteBookingController::class, 'verifyOtp']);
                
            });
            //push notification api
            
            Route::middleware(['goride.token'])->group(function () {
            
                Route::any("send-fbw-template", [Controller::class, 'fbwTemplateSend'])->name('fbwTemplateSend');
            
            });
            
            
            
            // GLOBAL AUTH APIS
            Route::post("/global-user-check", [GlobalAuthController::class, 'checkUser'])->name('checkUser');
            Route::post("/global-verify-otp", [GlobalAuthController::class, 'verifyOtp'])->name('verifyOtp');
            Route::post("/global-send-otp", [GlobalAuthController::class, 'sendOTP'])->name('sendOTP');
            
            Route::post("/global-google-auth", [GlobalAuthController::class, 'googleAuth'])->name('googleAuth');
            
            Route::middleware('auth.global_db')->post('/auth-check', [GlobalAuthController::class, 'verifyToken']);
            
            Route::middleware('auth.global_db')->post('/profile-update', [GlobalAuthController::class, 'updateProfile']);
            Route::middleware('auth.global_db')->get('/customer/me', [GlobalAuthController::class, 'me']);
            // Route::get('/customer/me', [CustomerController::class, 'me']);
            
            
        // });