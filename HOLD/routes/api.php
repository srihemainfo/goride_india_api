<?php



use App\Http\Controllers\crm\Auth\{

    RegisteredUserController,

    AuthenticatedSessionController,

    PasswordResetLinkController,
 
    NewPasswordController

};



use App\Http\Controllers\crm\{

    DriverController,

    FleetController,

    CurrencyController,

    PlaceController,

    CustomerController,

    OfferTimesController,

    PromoCodeController,

    OfferDaysController,

    AreaController,
    
    TwilioWebhook,

    CarFareController,

    FixedPriceController,

    BookingController,

    LocationRangeController,

    MapzoneController,

    InvoiceController,

    DashboardController,

    ReportsController,

    EmployeeController,

    NotificationController,

    SettlementController,

    ModulePermissionController,

    LiveTracking,

    ExtrasController,

    ProfileController,

    AuditLogsController,

    AutoSettlementController,

    SettingsController,

    StripeController,

    PricingController,

    PricingsecondController,

    VehicleController,

    DynamicCarfareController,

    RoleController,

    MyridersController,

    DriverRequestController,

    CronJobController1,

    QueryController,

    Partnerlistshow,

    FaresettingController



};



use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewReminderMail;





// airportrides prasanth 07-09-2023

use App\Http\Controllers\Airportrides\Auth\AuthController;

use App\Http\Controllers\Airportrides\Auth\ForgotpasswordController;

use App\Http\Controllers\Airportrides\{

    RegisterController,

    PartnerController,

    AutoCompleteController,

    CarController,

    BookingsController,

    BookingpaymentController,

    ContactController,

    CarapiController,

    CountriesController,

    UserdashboardController,

    BythehourController,

    NotificationsController

};

/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "api" middleware group. Make something great!

|

*/


// Jana goride admin values show....
 
Route::post('partnerlist', [Partnerlistshow::class, 'partnerlistshow']);

Route::get('starrating', [SettingsController::class, 'starrating']);
Route::post('feedbackstore', [SettingsController::class, 'feedbackstore']);
Route::post('onloadcheck', [SettingsController::class, 'onloadcheck']);

Route::get('/send-review-reminder', function () {
    Artisan::call('email:review-reminder');

    return response()->json([
        'status' => 'success',
        'message' => 'Review reminder command executed.',
    ]);
});

// Route::get('/send-review-email', function () {
//     Artisan::call('email:review-reminder');
// });

// Route::get('/send-review-email', function () {
//     $userEmail = 'mrjana2003@gmail.com';
//     $values1 = DB::connection('mysql')
//     ->table('partnerlists')
//     ->get(['db_key', 'database_user', 'database_password', 'domain_name']);


//     $result = [];
// foreach ($values1 as $value) {
//     try {
//         $bookInfo = DB::connection($value->db_key)
//             ->table('bookinfo')
//             ->get();

//         $normalized = [];
//         foreach ($bookInfo as $row) {
//             if ($row->order_status === 'Completed' && $row->review_manage === null) {
//                 try {
//                     // Attempt to send email
//                     // return $row->fname;
//                     Mail::to($row->email)->send(new ReviewReminderMail(
//                         $row->fname,
//                         $value->domain_name,  
//                         $row->job_no   
//                     ));
                    
//                     // Update review_manage to 'Completed' if mail is sent
//                     DB::connection($value->db_key)
//                         ->table('bookinfo')
//                         ->where('email', $row->email)
//                         ->whereNull('review_manage') // avoid overwriting
//                         ->update(['review_manage' => 'Completed']);

//                     $normalized[] = [
//                         'email' => $row->email,
//                         'status' => 'Mail sent & review_manage updated to Completed',
//                     ];
//                 } catch (\Exception $e) {
//                     // If email sending fails, keep review_manage as null
//                     DB::connection($value->db_key)
//                         ->table('bookinfo')
//                         ->where('email', $row->email)
//                         ->update(['review_manage' => null]);

//                     $normalized[] = [
//                         'email' => $row->email,
//                         'status' => 'Mail failed, review_manage remains null',
//                         'error' => $e->getMessage(),
//                     ];
//                 }
//             }
//         }

//         $result[$value->domain_name] = $normalized;

//     } catch (\Exception $e) {
//         $result[$value->domain_name] = [
//             'error' => 'Connection error or invalid db_key',
//         ];
//     }
// }
// });


// Elavarasan Twilio Webhook....

Route::post('/twilio/forward-call', [TwilioWebhook::class, 'index']);

Route::post('/twilio-voice', [TwilioWebhook::class, 'voice']);


//prasanth airportrides 07-09-2023



//User login
Route::post('/verifyCode', [SettingsController::class, 'verifyCode']);




Route::post('userlogin', [AuthController::class, 'login']);

Route::post('gate_get', [AuthController::class, 'gate_get']);

Route::post('condition_check', [AuthController::class, 'condition_check']);

Route::post('alterTable', [QueryController::class, 'alterTable']);

Route::post('NewTable', [QueryController::class, 'NewTable']);







Route::post('forgot', [ForgotpasswordController::class, 'index']);

Route::post('register', [RegisterController::class, 'store']);

// Route::post('check', [MyridersController::class, 'index']);

Route::post('removetoken', [MyridersController::class, 'removetoken']);

Route::post("checktoken", [MyridersController::class, 'checktoken']);



// Route::prefix('api')->group(function () {

//  Route::post('/check', [RegisterController::class, 'index']);

// });





Route::post('sidebarpage', [BookingController::class, 'sidebarpage']);

Route::post('child_items', [BookingController::class, 'child_items']);





//Partner



Route::post('partner', [PartnerController::class, 'index']);

Route::post('partner_name', [PartnerController::class, 'partner_name']);



//Location autocomplete



Route::get('autocomplete', [AutoCompleteController::class, 'autoComplete']);

Route::get('airport_autocomplete', [AutoCompleteController::class, 'autoComplete_airport']);



//Carlist



Route::post('carlist', [CarController::class, 'index'])->name('form.carlist');

Route::post('getprice', [CarController::class, 'getprice'])->name('form.getprice');

Route::post('carlistapi', [CarapiController::class, 'index']);



//oneway and return booking



Route::post('booking', [BookingsController::class, 'index']);

Route::post('bookings', [BookingpaymentController::class, 'index']);

Route::post('cancelupdate', [BookingsController::class, 'update']);



// By The Hour Booking



Route::post('byhourbooking', [BythehourController::class, 'index']);



//contact form



Route::post('contactform', [ContactController::class, 'index']);

Route::post('countries_search', [CountriesController::class, 'search']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    return $request->user();

});



//Dashboard

Route::Post('dashboardindex', [DashboardController::class, 'index'])->name('dashboard');



//24-12-2024-start

#Website settings and atleast one driver create or not check for website and crm usage

Route::Post('websetting/driver/check', [AuthenticatedSessionController::class, 'condition_check']);



//24-12-2024 -end



Route::post('dashboard', [UserdashboardController::class, 'index']);

Route::post('profile_submit', [UserdashboardController::class, 'profile_submit'])->name('profile_submit');

Route::post('profile_app', [UserdashboardController::class, 'profile_app'])->name('profile_app');

Route::post('upload-image', [UserdashboardController::class, 'upload'])->name('upload-image');

Route::post('appupload', [UserdashboardController::class, 'appupload'])->name('appupload');

Route::post('changePassword', [UserdashboardController::class, 'changePassword'])->name('changePassword');

Route::post('appchangePassword', [UserdashboardController::class, 'appchangePassword'])->name('appchangePassword');

Route::post('dashboarddata', [UserdashboardController::class, 'dashboard'])->name('dashboarddata');

Route::post('profileappdata', [UserdashboardController::class, 'profileappdata'])->name('profileappdata');

//stripe

Route::get('/payment', [StripeController::class, 'index'])->name('payment.index');

Route::post('/payment/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->name('payment.createSession');

Route::get('/payment/success', [StripeController::class, 'success'])->name('payment.success');

Route::get('/payment/cancel', [StripeController::class, 'cancel'])->name('payment.cancel');





// reviews

Route::post('reviews', [ContactController::class, 'reviews']);



Route::post('notifications', [NotificationsController::class, 'index']);



// Routes for crm - Start

Route::post('login', [AuthenticatedSessionController::class, 'login']);

Route::post('resetlink', [PasswordResetLinkController::class, 'store']);




Route::post('cn-pass', [NewPasswordController::class, 'store']);

Route::post('overwrite', [ExtrasController::class, 'overwrite']);

Route::post('user_insert', [ExtrasController::class, 'user_insert']);

Route::post('importfile', [ExtrasController::class, 'ImportfFile']);

Route::post('uploadfile', [ExtrasController::class, 'uploadfile']);

Route::post('showfile', [ExtrasController::class, 'showfile']);





Route::post('/createuser', [RegisteredUserController::class, 'store']);

Route::post('/query_change', [SettingsController::class, 'query_change']);

Route::post('previewBooking', [BookingController::class, 'preview']);



Route::middleware(['user.check'])->group(function () {
    
    // Twilio Outgoing Call 
    Route::post('/twilio-token', [TwilioWebhook::class, 'getToken']);
    
    Route::post('destroy-auth', [AuthenticatedSessionController::class, 'Logout']);



    Route::post('audit_logs', [AuditLogsController::class, 'index']);



    // Extras

    Route::post('brands', [ExtrasController::class, 'brands']);

    Route::post('models', [ExtrasController::class, 'models']);

    Route::post('distance', [ExtrasController::class, 'DistanceAndDuration']);

    Route::post('getlocation', [ExtrasController::class, 'GoogleLocations']);

    Route::post('getarealocation', [ExtrasController::class, 'GetAreaLocation']);

    Route::post('airports', [ExtrasController::class, 'airports']);

    Route::post('template-details', [ExtrasController::class, 'TemplateDetails']);

    Route::post('emailtemplate-details', [ExtrasController::class, 'EmailTemplateDetails']);
    
    Route::post('whatsapptemplate-details', [ExtrasController::class, 'WhatsappTemplateDetails']);

    Route::post('twiliosms-details', [ExtrasController::class, 'twiliosmsdetails']);



    // For Profile

    Route::post('myprofile', [ProfileController::class, 'MyProfile']);

    Route::post('createprofile', [ProfileController::class, 'createprofile']);

    Route::post('profilepasswordchange', [ProfileController::class, 'profilepasswordchange']);



    // For Vehichle

    Route::post('createvehichle', [FleetController::class, 'store']);

    Route::post('updatevehichle', [FleetController::class, 'store']);

    Route::post('deletevehichle', [FleetController::class, 'destroy']);

    Route::post('vehichlestatus', [FleetController::class, 'FleetStatusUpdate']);

    Route::post('vehichlelist', [FleetController::class, 'index']);

    Route::post('editvehichle', [FleetController::class, 'edit']);

    Route::post('getfleet', [FleetController::class, 'getfleet']);

    Route::post('get-distance-unit', [ExtrasController::class, 'GetDistanceUnit']);



    // For Driver

    Route::post('createdriver', [DriverController::class, 'store']);

    Route::post('updatedriver', [DriverController::class, 'update']);

    Route::post('editdriver', [DriverController::class, 'edit']);

    Route::post('driverimgdelete', [DriverController::class, 'driverimgdelete']);

    Route::post('deletedriver', [DriverController::class, 'destroy']);

    Route::post('driverlist', [DriverController::class, 'index']);

    Route::post('driverstatus', [DriverController::class, 'DriverStatusUpdate']);

    Route::post('driverpass', [DriverController::class, 'DriverPasswordChange']);

    Route::post('driverfilter', [DriverController::class, 'DriverFilter']);



    // For Employer

    Route::post('createemployer', [EmployeeController::class, 'store']);

    Route::post('updateemployer', [EmployeeController::class, 'EmployeeDetailsUpdate']);

    Route::post('deleteemployer', [EmployeeController::class, 'destroy']);

    Route::post('employerstatus', [EmployeeController::class, 'EmployeeStatusUpdate']);

    Route::post('employerpass', [EmployeeController::class, 'EmployeePasswordChange']);

    Route::post('employerlist', [EmployeeController::class, 'index']);

    Route::post('editemployer', [EmployeeController::class, 'edit']);

    Route::post('filterepmloyer', [EmployeeController::class, 'EmployerFilter']);

    Route::post('passwordchange', [EmployeeController::class, 'passwordChange']);

    Route::post('passwordShow', [EmployeeController::class, 'passwordShow']);



    // For Customer

    Route::post('createcustomer', [CustomerController::class, 'store']);

    Route::post('updatecustomer', [CustomerController::class, 'store']);

    Route::post('editcustomer', [CustomerController::class, 'edit']);

    Route::post('deletecustomer', [CustomerController::class, 'destroy']);

    Route::post('customerlist', [CustomerController::class, 'index']);

    Route::post('filteremployer', [CustomerController::class, 'CustomerFilter']);

    Route::post('TemplateList', [CustomerController::class, 'TemplateList']);

    Route::post('Template', [CustomerController::class, 'Template']);

    Route::post('/get-emails', [CustomerController::class, 'getEmails']);

    Route::post('/customer_list_show', [CustomerController::class, 'customer_list_show']);

    Route::post('/delete_group_list', [CustomerController::class, 'delete_group_list']);
    
    Route::post('/getGroups', [CustomerController::class, 'getGroups']);
    
    Route::post('/create-groups', [CustomerController::class, 'create_groups']);

    Route::post('getDashboardEmails', [CustomerController::class, 'getDashboardEmails']);

    Route::post('CustomerEmailBooking', [CustomerController::class, 'CustomerEmailBooking']);

    Route::post('CustomerDashboardBooking', [CustomerController::class, 'CustomerDashboardBooking']);

    Route::post('get-clients', [CustomerController::class, 'GetClients'])->name('GetClients');

    //Fare Setting

    Route::post('faresetting', [FaresettingController::class, 'faresetting']);

    Route::post('fareprice_store', [FaresettingController::class, 'fareprice_store']);

    Route::post('fareprice_delete', [FaresettingController::class, 'fareprice_delete']);

    Route::post('farepriceedit', [FaresettingController::class, 'farepriceedit']);

    Route::post('fixedpriceupdate', [FaresettingController::class, 'fixedpriceupdate']);


    //for area

    Route::post('arealist', [AreaController::class, 'index']);

    Route::post('editarea', [AreaController::class, 'edit']);

    Route::post('areastore', [AreaController::class, 'store']);

    Route::post('areaupdate', [AreaController::class, 'update']);

    Route::post('deletearea', [AreaController::class, 'destroy']);

    Route::post('/filterarea', [AreaController::class, 'areafilter']);

    // For Booking

    Route::post('createbooking', [BookingController::class, 'store']);

    Route::post('updatebooking', [BookingController::class, 'update']);

    Route::post('bookingstatus', [BookingController::class, 'BookingStatusUpdate']);

    Route::post('deletebooking', [BookingController::class, 'destroy']);

    Route::post('assigndriver', [BookingController::class, 'AssignOrRemoveDriver']);

    Route::post('bookinglist', [BookingController::class, 'index']);

    Route::post('bookingfilter', [BookingController::class, 'BookingFilter']);

    Route::post('multibooking', [BookingController::class, 'StoreMultiBookings']);

    Route::post('editbooking', [BookingController::class, 'edit']);

    Route::post('bookconfirm-mail', [BookingController::class, 'EmailBookingStatus']);
    
    Route::post('bookconfirm-whatsapp', [BookingController::class, 'WhatsappBookingStatus']);

    Route::post('twiliosmssend', [BookingController::class, 'twiliosmssend']);

    Route::post('bookdetail-mail', [BookingController::class, 'EmailBookingDetails']);

    Route::post('previewbooking', [BookingController::class, 'preview']);

    Route::post('recalculate', [BookingController::class, 'RecalculateBooking']);

    Route::post('bookingchart', [BookingController::class, 'bookingChart']);

    Route::post('checkSub-limits', [BookingController::class, 'checkSub_limits']);




    // For Range Fare

    Route::post('create_rfare', [CarFareController::class, 'store']);

    Route::post('update_rfare', [CarFareController::class, 'UpdateRangeFare']);

    Route::post('edit_rfare', [CarFareController::class, 'editRangeFare']);

    Route::post('delete_rfare', [CarFareController::class, 'destroyRangeFare']);

    Route::post('rfare_list', [CarFareController::class, 'index']);

    //range fare airport

    Route::post('destroyairportRangeFare', [CarFareController::class, 'destroyairportRangeFare']);

    Route::post('editairportRangeFare', [CarFareController::class, 'editairportRangeFare']);

    Route::post('airportfare', [CarFareController::class, 'rangefareindex']);

    Route::post('airportfareupdate', [CarFareController::class, 'airportstore']);

    Route::post('airportfareupdateto', [CarFareController::class, 'airportstoreto']);

    Route::post('update_rfareairport', [CarFareController::class, 'update_rfareairport']);

    Route::post('airporthourstore', [CarFareController::class, 'airporthourstore']);

    Route::post('rangefareindexunicairportget', [CarFareController::class, 'rangefareindexunicairportget']);



    // For Radius Fare

    Route::post('create_rsfare', [LocationRangeController::class, 'store']);

    Route::post('update_rsfare', [LocationRangeController::class, 'updateRadiusFare']);

    Route::post('edit_rsfare', [LocationRangeController::class, 'editRadiusFare']);

    Route::post('destroy_rsfare', [LocationRangeController::class, 'destroyRadiusFare']);

    Route::post('rsfare_list', [LocationRangeController::class, 'index']);

    Route::resource('roles', RoleController::class);

    Route::post('roles', [RoleController::class, 'index']);

    Route::post('roles/store', [RoleController::class, 'store']);

    Route::post('roles/get_data', [RoleController::class, 'get_data']);

    Route::post('roles/update', [RoleController::class, 'update']);

    Route::post('roles/delete', [RoleController::class, 'delete']);

    Route::post('roles/role_get', [RoleController::class, 'roleGet']);



    Route::post('driver_request', [DriverRequestController::class, 'index']);





    //MAp zone routes

    Route::post('mapzonelist', [MapzoneController::class, 'mapzonelist']);







    //For location range

    Route::post('GetZones', [LocationRangeController::class, 'GetZones']);

    Route::post('UpdateCoordinates', [LocationRangeController::class, 'UpdateCoordinates']);

    Route::post('locationrangeFilter', [LocationRangeController::class, 'locationrangeFilter']);

    Route::post('cordinatestore', [LocationRangeController::class, 'cordinatestore']);

    Route::post('fardetailsedit', [LocationRangeController::class, 'fardetailsedit']);

    Route::post('destroyfaredetail', [LocationRangeController::class, 'destroyfaredetail']);





    // For Settlement

    Route::post('settlement', [SettlementController::class, 'CalculateSettlemet']);

    Route::post('transactions', [SettlementController::class, 'index']);

    Route::post('indexbookingsammount', [SettlementController::class, 'indexbookingsammount']);

    Route::post('indexdriverammount', [SettlementController::class, 'indexdriverammount']);

    Route::post('transfilter', [SettlementController::class, 'TransFilter']);

    Route::post('WeeklyDriverSettlemepdfdata', [SettlementController::class, 'WeeklyDriverSettlemepdfdata']);

    Route::post('EmailSettlements', [SettlementController::class, 'EmailSettlements']);

    Route::post('DriverSettlementPdf', [SettlementController::class, 'DriverSettlementPdf']);


    
    // Advance Payment for Driver
    Route::post('advance-paymentStore', [SettlementController::class, 'advance_paymentStore']);
    
    Route::post('advance-paymentIndex', [SettlementController::class, 'advance_paymentIndex']);
    
    Route::post('advance-filter', [SettlementController::class, 'advance_paymentFilter']);
    
    Route::post('advance-paymentEdit', [SettlementController::class, 'advance_paymentEdit']);
    
    Route::post('advance-paymentDelete', [SettlementController::class, 'advance_paymentDelete']);

    //ModulePermission

    Route::post('module-permissions', [ModulePermissionController::class, 'index']);

    Route::post('UpdatePermissions', [ModulePermissionController::class, 'UpdatePermissions']);

    Route::post('updateAllPermissions', [ModulePermissionController::class, 'updateAllPermissions']);



    //invoice gentrade

    Route::post('GetClientNames', [InvoiceController::class, 'GetClientNames']);

    Route::post('GetJobNos', [InvoiceController::class, 'GetJobNos']);

    Route::post('GetBookingForInvoice', [InvoiceController::class, 'GetBookingForInvoice']);

    Route::post('GenerateInvoice', [InvoiceController::class, 'GenerateInvoice']);

    Route::post('StoreInvoice', [InvoiceController::class, 'StoreInvoice']);

    Route::post('invoice', [InvoiceController::class, 'index']);

    Route::post('invoice_filter', [InvoiceController::class, 'invoice_filter']);
    
    Route::post('temp-invoice', [InvoiceController::class, 'temp_invoice']);
    
    Route::post('view-temp-invoice', [InvoiceController::class, 'view_temp_invoice']);

    Route::post('CancelInvoice', [InvoiceController::class, 'CancelInvoice']);

    Route::post('EmailInvoice', [InvoiceController::class, 'EmailInvoice']);

    Route::post('InvoiceGenerateReport', [InvoiceController::class, 'InvoiceGenerateReport']);



    //For Reports

    Route::post('admin-job-filter', [ReportsController::class, 'job_Filter']);

    Route::post('admin-job-report', [ReportsController::class, 'job_report']);

    Route::post('admin-generate-report', [ReportsController::class, 'AdminGenerateReport']);

    Route::post('admin-generate-report-weekly', [ReportsController::class, 'AdminGenerateReportweekly']);

    Route::post('admin-generate-report-excel', [ReportsController::class, 'AdminGenerateReportExcel']);

    Route::post('AdminGenerateReportExcelaccount', [ReportsController::class, 'AdminGenerateReportExcelaccount']);

    Route::post('AdminGenerateReportExceldriver', [ReportsController::class, 'AdminGenerateReportExceldriver']);

    Route::post('AdminGenerateReportExcelwekly_monthly', [ReportsController::class, 'AdminGenerateReportExcelwekly_monthly']);

    Route::post('driverlistall', [ReportsController::class, 'driverlistall']);

    Route::post('driverreportmonthly', [ReportsController::class, 'driverreportmonthly']);

    Route::post('driverreportsettlehistry', [ReportsController::class, 'driverreportsettlehistry']);

    Route::post('driverreportbokking', [ReportsController::class, 'driverreportbokking']);

    Route::post('driverreportbokkingweekly', [ReportsController::class, 'driverreportbokkingweekly']);

    Route::post('driverreportbokkingdetails', [ReportsController::class, 'driverreportbokkingdetails']);

    Route::post('driverreportbokkingweeklybookinks', [ReportsController::class, 'driverreportbokkingweeklybookinks']);

    Route::post('driverreportbokkingdaily', [ReportsController::class, 'driverreportbokkingdaily']);

    Route::post('driverreportbokkingdailysummary', [ReportsController::class, 'driverreportbokkingdailysummary']);

    Route::post('getpartnerlogo', [ReportsController::class, 'getpartnerlogo']);

    //ecxel driver report

    Route::post('driverreportexceltransaction', [ReportsController::class, 'driverreportexceltransaction']);

    Route::post('driverreportexcelsettle_history', [ReportsController::class, 'driverreportexcelsettle_history']);

    Route::post('driverreportexcelsummary_details', [ReportsController::class, 'driverreportexcelsummary_details']);

    Route::post('driverreportexcelbookinfo', [ReportsController::class, 'driverreportexcelbookinfo']);

    Route::post('driverreportexcelsummary_detailsexcel', [ReportsController::class, 'driverreportexcelsummary_detailsexcel']);

    Route::post('driverreportexcelbookinfoexcel', [ReportsController::class, 'driverreportexcelbookinfoexcel']);

    Route::post('driverreportexceltransactiondaily', [ReportsController::class, 'driverreportexceltransactiondaily']);



    Route::get('/run-autosettlement', [AutoSettlementController::class, 'runAutoSettlement']);

    //general setting

    Route::post('generalsetting', [SettingsController::class, 'generalsetting']);

    Route::post('generalstore', [SettingsController::class, 'generalstore']);
    
    

    Route::post('generalupdate', [SettingsController::class, 'generalupdate']);

    Route::post('generalsettingedit', [SettingsController::class, 'generalsettingedit']);

    Route::post('generaldelete', [SettingsController::class, 'generaldelete']);

    Route::post('countrylists', [SettingsController::class, 'countrylists']);

    Route::post('phoneCode', [SettingsController::class, 'phoneCode']);

    Route::post('generalsettingcurrency', [SettingsController::class, 'generalsettingcurrency']);



    Route::post('save-article', [SettingsController::class, 'saveArticle']);





    Route::post('getArticleList', [SettingsController::class, 'getArticleList']);



    //bookingsetting

    Route::post('bookingsetting', [SettingsController::class, 'bookingsetting']);

    Route::post('bookingstore', [SettingsController::class, 'bookingstore']);

    //email setting

    Route::post('emailsetting', [SettingsController::class, 'emailsetting']);

    Route::post('emailsettingstore', [SettingsController::class, 'emailsettingstore']);

    Route::post('emailTest', [SettingsController::class, 'emailTest']);

    //zone setting

    Route::post('zonesetting', [SettingsController::class, 'zonesetting']);

    //   Pricing

    Route::post('PricingShow', [PricingController::class, 'PricingShow']);

    Route::post('GeneralPricingstore', [PricingController::class, 'GeneralPricingstore']);

    // payment setting     

    Route::post('paymentoption', [SettingsController::class, 'paymentoption']);

    
    Route::post('paymentstore', [SettingsController::class, 'paymentstore']);
    
    // Whatsapp Configuration
    Route::post('/get-whatsappsetting', [SettingsController::class, 'get_whatsappsetting'])->name('get_whatsappsetting');
    
    Route::post('/whatsappsetting', [SettingsController::class, 'whatsappsetting'])->name('whatsappsetting');
    
    Route::post('/testwhats-message', [SettingsController::class, 'testwhats_message'])->name('testwhats-message');
    
    // Call Configuration
    Route::post('/get-callsetting', [SettingsController::class, 'get_callsetting'])->name('get_callsetting');
    
    Route::post('/callsetting', [SettingsController::class, 'callsetting'])->name('callsetting');

    // bookingrestriction setting

    Route::post('bookingrestriction', [SettingsController::class, 'bookingrestriction']);

    Route::post('bookingrestrictionfilter', [SettingsController::class, 'bookingrestrictionfilter']);

    Route::post('bookingrestrictionstore', [SettingsController::class, 'bookingrestrictionstore']);

    Route::post('bookingrestrictionedit', [SettingsController::class, 'bookingrestrictionedit']);

    Route::post('bookingrestrictionupdate', [SettingsController::class, 'bookingrestrictionupdate']);

    Route::post('bookingrestrictiondelete', [SettingsController::class, 'bookingrestrictiondelete']);

    //google callender setting

    Route::post('googlecallender', [SettingsController::class, 'googlecallender']);

    Route::post('googlecallenderstore', [SettingsController::class, 'googlecallenderstore']);

    //review setting

    Route::post('reviewstore', [SettingsController::class, 'reviewstore']);

    Route::post('reviewlist', [SettingsController::class, 'reviewlist']);

    Route::post('feedbackshow', [SettingsController::class, 'feedbackshow']);


    Route::post('country_websites', [SettingsController::class, 'countryWebsites']);





    // create page

    Route::post('articlestore', [ArticleController::class, 'articlestore']);

    //dynamic carfare 

    Route::post('dynamiccarfare', [DynamicCarfareController::class, 'dynamiccarfare']);



    Route::post('getCarFare', [DynamicCarfareController::class, 'getCarFare']);





    Route::post('updatedynamicfare', [DynamicCarfareController::class, 'updatedynamicfare']);

    Route::post('updatedynamicfareoverall', [DynamicCarfareController::class, 'updatedynamicfareoverall']);







    // vehicle

    Route::post('VehicleStore', [PricingController::class, 'VehicleStore']);

    Route::post('Vehiclelist', [PricingController::class, 'Vehiclelist']);

    Route::post('Vehiclelist', [DriverController::class, 'Vehiclelist']);
    
    Route::post('FileUpload', [DriverController::class, 'FileUpload']);
    
    Route::post('/file-delete', [DriverController::class, 'FileDelete'])->name('FileDelete');

    Route::post('EditVehiclelist', [PricingController::class, 'EditVehiclelist']);

    Route::post('/vehiclepricing/edit/{id}', [PricingController::class, 'VehicleEdit'])->name('vehiclepricing.edit');

    Route::post('/vehiclepricing/update/{id}', [PricingController::class, 'VehicleUpdate'])->name('vehiclepricing.update');

    Route::post('/vehiclepricing/delete/{id}', [PricingController::class, 'DeleteVehicle']);

    Route::post('/VehiclePricingView', [PricingController::class, 'VehiclePricingView'])->name('vehiclepricing');

    //   distance slab

    Route::post('DistanceStore', [PricingController::class, 'DistanceStore']);

    Route::post('/distanceslab/update/{id}', [PricingController::class, 'DistanceUpdate'])->name('distanceslab.update');

    Route::post('/distanceslab/edit/{id}', [PricingController::class, 'DistanceEdit'])->name('distanceslab.edit');

    Route::post('/distanceslab/delete/{id}', [PricingController::class, 'Distancedelete']);

    Route::post('/distanceview', [PricingController::class, 'DistanceView'])->name('distanceview');



    // list fleets

    Route::post('/Fleetlist', [PricingController::class, 'Fleetlist'])->name('Fleetlist');

    Route::post('FleetStore', [PricingController::class, 'FleetStore']);

    Route::post('/FleetEdit/edit/{id}', [PricingController::class, 'FleetEdit'])->name('FleetEdit.edit');

    Route::post('/fleet/update/{id}', [PricingController::class, 'FleetUpdate'])->name('fleet.update');

    Route::post('/fleet/delete/{id}', [PricingController::class, 'DeleteFleet']);



    // Auditlogs

    // Route::post('audit_logs', [AuditLogsController::class, 'audit_logs']);

    // Route::post('audit_logs', AuditLogsController::class, 'index');



    // hourly

    //  offer times

    Route::post('/OffertimesView', [PricingController::class, 'OffertimesView'])->name('OffertimesView');

    Route::post('OffertimeStore', [PricingController::class, 'OffertimeStore']);

    Route::post('/OfferTimeEdit/edit/{id}', [PricingController::class, 'OfferTimeEdit'])->name('OfferTimeEdit.edit');

    Route::post('/OfferTimeUpdate/update/{id}', [PricingController::class, 'OfferTimeUpdate'])->name('OfferTimeUpdate.update');

    Route::post('/OfferTimedelete/delete/{id}', [PricingController::class, 'OfferTimedelete']);



    // offer price

    Route::post('PriceView', [PricingController::class, 'PriceView'])->name('PriceView');

    Route::post('OfferDaysStore', [PricingController::class, 'OfferDaysStore']);

    Route::post('/OfferDaysEdit/edit/{id}', [PricingController::class, 'OfferDaysEdit'])->name('OfferDaysEdit.edit');

    Route::post('/OfferdaysUpdate/update/{id}', [PricingController::class, 'OfferdaysUpdate'])->name('OfferdaysUpdate.update');

    Route::post('/OfferDaysdelete/delete/{id}', [PricingController::class, 'OfferDaysdelete']);





    // fixed price

    Route::post('FixedPriceView', [PricingController::class, 'FixedPriceView'])->name('FixedPriceView');

    Route::post('FixedPriceStore', [PricingController::class, 'FixedPriceStore']);

    Route::post('/FixedPriceEdit/edit/{id}', [PricingController::class, 'FixedPriceEdit'])->name('FixedPriceEdit.edit');

    Route::post('/FixedPriceUpdate/update/{id}', [PricingController::class, 'FixedPriceUpdate'])->name('FixedPriceUpdate.update');

    Route::post('/FixedPriceDelete/delete/{id}', [PricingController::class, 'FixedPriceDelete']);





    // promo code

    Route::post('PromoCodeView', [PricingController::class, 'PromoCodeView'])->name('PromoCodeView');

    Route::post('PromoCodeStore', [PricingController::class, 'PromoCodeStore']);

    Route::post('/PromoCodeEdit/edit/{id}', [PricingController::class, 'PromoCodeEdit'])->name('PromoCodeEdit.edit');

    Route::post('/PromoCodeUpdate/update/{id}', [PricingController::class, 'PromoCodeUpdate'])->name('PromoCodeUpdate.update');

    Route::post('/PromoCodeDelete/delete/{id}', [PricingController::class, 'PromoCodeDelete']);

    // email template

    Route::post('EmailTemplateView', [PricingController::class, 'EmailTemplateView'])->name('EmailTemplateView');

    Route::post('EmailTemplateStore', [PricingController::class, 'EmailTemplateStore']);



    Route::post('/EmailTemplateEdit/edit/{id}', [PricingController::class, 'EmailTemplateEdit'])->name('EmailTemplateEdit.edit');

    Route::post('/EmailTemplateUpdate/update/{id}', [PricingController::class, 'EmailTemplateUpdate'])->name('EmailTemplateUpdate.update');

    Route::post('/EmailTemplateDelete/delete/{id}', [PricingController::class, 'EmailTemplateDelete']);

    //   



    Route::post('/hourlypackage', [PricingsecondController::class, 'showdata']);

    Route::post('/hourlypackagestore', [PricingsecondController::class, 'stores']);

    Route::post('/hourlypackagedit/{id}', [PricingsecondController::class, 'edits']);

    Route::post('/HourlyPackageUpdate/{id}', [PricingsecondController::class, 'HourlyPackageUpdate']);

    Route::post('/hourlypackagedelete/{id}', [PricingsecondController::class, 'cleardata']);

    // location category

    Route::post('/locationcategoryupdate', [PricingsecondController::class, 'locationcategoryupdate']);



    Route::post('/locationcategoryshow', [PricingsecondController::class, 'locationcategoryshow']);

});


// routes/api.php
Route::post('/404', function (Request $request) {
    return response()->json(['status' => 'error', 'message' => 'Link expired']);
});



// Routes for crm - End

