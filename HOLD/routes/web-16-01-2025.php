<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{
    DriverController,
    FleetController,
    CurrencyController,
    PlaceController,
    CustomerController,
    OfferTimesController,
    PromoCodeController,
    OfferDaysController,
    AreaController,
    CarFareController,
    FixedPriceController,
    BookingController,
    LocationRangeController,
    InvoiceController,
    DashboardController,
    ReportsController,
    EmployeeController,
    NotificationController,
    SettlementController,
    ModulePermissionController,
    RangeFareController,
    RangeFareAirportController,
    LiveTracking,
    SettingsController,
    AuditLogsController,
    PricingController,
    PricingsecondController,
    RoleController,
    MyridersController,
    DriverRequestController
   
};

use Illuminate\Support\Facades\{Route,File};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// dd('Test');
Route::post('/destroy-session/{sessionId}', function ($sessionId) {
    $sessionFile = storage_path("framework/sessions/{$sessionId}");
    
    
        try {
        if (File::exists($sessionFile)) {
            if (File::delete($sessionFile)) {
                
              
                return response()->json(['success' => true, 'message' => 'Session file deleted.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete session file.']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Session file not found.']);
        }
    } catch (\Exception $e) {
        \Log::error('Error deleting session file: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting session file: ' . $e->getMessage()]);
    }

});


Route::post("check", [MyridersController::class, 'index']);

Route::get('/', function () {
    
    return redirect('/login');
});

Route::get('/expiry', function () {
    
    return view('auth.expiry');
});


Route::get('sendSMS', [App\Http\Controllers\SendSMSController::class, 'sendsms']);

// Driver Profile Outside Auth
Route::get('/my-driver/{booking_id}', [DriverController::class, 'MyDriver'])->name('MyDriver');
Route::post('my-check', [AuthenticatedSessionController::class, 'mycheck'])->name('mycheck');

Route::middleware(['check.user'])->group(function () {
    // dd(session()->all());
    // Route::get('/dashboard', function () {
    //     return view('dashboard-layout.index');
    // })->name('dashboard');
    Route::get('/driver/edit/{id}', function () {
        return view('drivers.edit');
    })->name('driver.edit');
    Route::get('/booking/edit/{id}', function () {
        return view('booking.edit');
    })->name('booking.edit');
    Route::get('/myprofile', function () {
        return view('profile.myprofile');
    })->name('profile.myprofile');
    Route::get('/edit-profile/{id}', function () {
        return view('profile.edit');
    })->name('profile.edit');
    
    Route::get('/booking/preview/{sid}', function ($sid) {
    // $postData = Request::input();

    return View::make('booking.userprint', compact('sid'));
        
    })->name('userprint');

    //AJAX Request Routes
    Route::post('/driver-status-update', [DriverController::class, 'DriverStatusUpdate'])->name('DriverStatusUpdate');
    Route::get('/driver-create', [DriverController::class, 'FirstCreate'])->name('FirstCreate');
    Route::post('/driver-password-chnage', [DriverController::class, 'DriverPasswordChange'])->name('DriverPasswordChange');
    Route::post('/place-status-update', [PlaceController::class, 'PlaceStatusUpdate'])->name('PlaceStatusUpdate');
    Route::post('/area-status-update', [AreaController::class, 'AreaStatusUpdate'])->name('AreaStatusUpdate');
    Route::post('/extra-amount-update', [AreaController::class, 'ExtraAmountUpdate'])->name('ExtraAmountUpdate');
    Route::post('/fleet-status-update', [FleetController::class, 'FleetStatusUpdate'])->name('FleetStatusUpdate');
    Route::post('/fixed-price-update', [FixedPriceController::class, 'FixedPriceUpdate'])->name('FixedPriceUpdate');
    Route::get('/get-area-for-place/{place_id?}', [FixedPriceController::class, 'GetAreaForPlace'])->name('GetAreaForPlace');
    Route::post('get-clients', [BookingController::class, 'GetClients'])->name('GetClients');
    Route::post('/get-client-info', [BookingController::class, 'GetClientInfo'])->name('GetClientInfo');
    Route::post('/get-locations', [BookingController::class, 'GetLocations'])->name('GetLocations');
    Route::post('/get-quote', [BookingController::class, 'GetQuote'])->name('GetQuote');
    Route::post('/get-car-deatils', [BookingController::class, 'GetCarDetails'])->name('GetCarDetails');
    Route::post('/booking-status-update', [BookingController::class, 'BookingStatusUpdate'])->name('BookingStatusUpdate');
    Route::post('/assign-remove-driver', [BookingController::class, 'AssignOrRemoveDriver'])->name('AssignOrRemoveDriver');
    Route::any('/check-special-day', [BookingController::class, 'CheckSpecialDay'])->name('CheckSpecialDay');
    Route::get('/get-booking-for-invoice', [InvoiceController::class, 'GetBookingForInvoice'])->name('GetBookingForInvoice');
    Route::post('/generate-invoice', [InvoiceController::class, 'GenerateInvoice'])->name('GenerateInvoice');
    Route::post('/store-invoice', [InvoiceController::class, 'StoreInvoice'])->name('StoreInvoice');
    Route::post('/get-drivers', [BookingController::class, 'GetDrivers'])->name('GetDrivers');
    Route::post('/get-job-nos', [InvoiceController::class, 'GetJobNos'])->name('GetJobNos');
    Route::post('/cancel-invoice', [InvoiceController::class, 'CancelInvoice'])->name('CancelInvoice');
    Route::post('/invoice-status-update', [InvoiceController::class, 'InvoiceStatusUpdate'])->name('InvoiceStatusUpdate');
    Route::post('/get-all-jobs', [BookingController::class, 'GetAllJobs'])->name('GetAllJobs');
    Route::post('/get-job-details', [BookingController::class, 'GetJobDetails'])->name('GetJobDetails');
    Route::post('/store-multi-bookings', [BookingController::class, 'StoreMultiBookings'])->name('StoreMultiBookings');
    Route::post('/file-upload', [DriverController::class, 'FileUpload'])->name('FileUpload');
    Route::post('/file-delete', [DriverController::class, 'FileDelete'])->name('FileDelete');
    Route::post('/get-driver-names', [SettlementController::class, 'GetDriverNames'])->name('GetDriverNames');
    Route::get('/get-transaction-for-settlement', [SettlementController::class, 'GetTransactionForSettlement'])->name('GetTransactionForSettlement');
    Route::post('/employee-status-update', [EmployeeController::class, 'EmployeeStatusUpdate'])->name('EmployeeStatusUpdate');
    Route::post('/store-booking-notification', [NotificationController::class, 'StoreBookingNotification'])->name('StoreBookingNotification');
    Route::post('/notification-status-update', [NotificationController::class, 'NotificationStatusUpdate'])->name('NotificationStatusUpdate');
    Route::post('/employee-details-update', [EmployeeController::class, 'EmployeeDetailsUpdate'])->name('EmployeeDetailsUpdate');
    Route::post('/employee-password-change', [EmployeeController::class, 'EmployeePasswordChange'])->name('EmployeePasswordChange');
    Route::post('/get-client-names', [InvoiceController::class, 'GetClientNames'])->name('GetClientNames');
    Route::post('/calculate-settlemet', [SettlementController::class, 'CalculateSettlemet'])->name('CalculateSettlemet');
    Route::post('/update-permissions', [ModulePermissionController::class, 'UpdatePermissions'])->name('UpdatePermissions');
    Route::post('/get-zones', [LocationRangeController::class, 'GetZones'])->name('GetZones');
    Route::post('/update-coordinates', [LocationRangeController::class, 'UpdateCoordinates'])->name('UpdateCoordinates');
    Route::post('/location-range-status-update', [LocationRangeController::class, 'LocationRangeStatusUpdate'])->name('LocationRangeStatusUpdate');
    Route::post('/get-moving-drivers', [LiveTracking::class, 'GetMovingDrivers'])->name('GetMovingDrivers');
    Route::post('/email-booking-details', [BookingController::class, 'EmailBookingDetails'])->name('EmailBookingDetails');
    Route::post('/sms-booking-details', [BookingController::class, 'SMSBookingDetails'])->name('SMSBookingDetails');
    Route::get('/EmailSettlements', [SettlementController::class, 'EmailSettlements'])->name('EmailSettlements');
    Route::post('/email-invoice', [InvoiceController::class, 'EmailInvoice'])->name('EmailInvoice');
    Route::post('/recalculate-booking', [BookingController::class, 'RecalculateBooking'])->name('RecalculateBooking');
    Route::post('/email-booking-status', [BookingController::class, 'EmailBookingStatus'])->name('EmailBookingStatus');

    //Main Routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('driver', DriverController::class);
    Route::resource('currency', CurrencyController::class)->only(['index', 'update']);
    Route::resource('fleet', FleetController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::get('create-fleet', [FleetController::class, 'firstFleet']);
    Route::resource('place', PlaceController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('area', AreaController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('customer', CustomerController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('offertimes', OfferTimesController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('offerdays', OfferDaysController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('promocode', PromoCodeController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('carfare', CarFareController::class)->only(['index', 'store']);
    Route::resource('fixed-price', FixedPriceController::class)->only(['index', 'store', 'edit', 'destroy']);
    Route::resource('locationrange', LocationRangeController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::resource('invoice', InvoiceController::class)->only(['index', 'create']);
    Route::get('booking/multi-booking', [BookingController::class, 'MultiBooking'])->name('MultiBooking');
    Route::get('booking/list/{order_status?}', [BookingController::class, 'booking_tables']);
    Route::resource('booking', BookingController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('/admin-report', [ReportsController::class, 'ManageAdminReport'])->name('ManageAdminReport');
    //prasanth
    Route::get('/check-driver-vehicle', [SettingsController::class, 'checkDriverVehicle']);

    Route::get('/general', [SettingsController::class, 'index'])->name('general');
    Route::get('/bookingsetting', [SettingsController::class, 'bookingsetting'])->name('bookingsetting');
    // Route::get('/articleset/{id}', [SettingsController::class, 'articleset'])->name('articleset');
    
Route::get('/articleset/{id}', [SettingsController::class, 'articleset'])->name('articleset');

    
    Route::get('/bookingSetting', [SettingsController::class, 'firstBookingSetting'])->name('firstBookingSetting');
    Route::get('/emailsetting', [SettingsController::class, 'emailsetting'])->name('emailsetting');
    Route::get('/emailSetting', [SettingsController::class, 'firstEmailsetting'])->name('firstEmailsetting');
    Route::get('/zonesetting', [SettingsController::class, 'zonesetting'])->name('zonesetting');
    Route::get('/paymentoption', [SettingsController::class, 'paymentoption'])->name('paymentoption');
    Route::get('/paymentOption', [SettingsController::class, 'firstPaymentoption'])->name('firstPaymentoption');
    Route::get('/bookingrestriction', [SettingsController::class, 'bookingrestriction'])->name('bookingrestriction');
    Route::get('/googlecallender', [SettingsController::class, 'googlecallender'])->name('googlecallender');
    Route::get('/review', [SettingsController::class, 'review'])->name('review');
    Route::get('/page', [SettingsController::class, 'page'])->name('page');
    Route::get('/carfares', [RangeFareAirportController::class, 'carfares']);
    Route::get('/create-carfares', [RangeFareAirportController::class, 'Firstcarfares']);
    Route::get('/carfaresss', [RangeFareAirportController::class, 'carfaresss']);
    Route::get('/templaterequest', [SettingsController::class, 'templaterequest'])->name('templaterequest');
    
    //end
    Route::get('/driver-report', [ReportsController::class, 'ManageDriverReport'])->name('ManageDriverReport');
    Route::get('/admin-generate-report', [ReportsController::class, 'AdminGenerateReport'])->name('AdminGenerateReport');
    Route::get('/driver-generate-report', [ReportsController::class, 'DriverGenerateReport'])->name('DriverGenerateReport');
    Route::get('/invoice-generate-report', [InvoiceController::class, 'InvoiceGenerateReport'])->name('InvoiceGenerateReport');
    Route::resource('settlement', SettlementController::class)->only(['index']);
    Route::get('/weekly-driver-settlement-pdf', [SettlementController::class, 'WeeklyDriverSettlementPdf'])->name('WeeklyDriverSettlementPdf');
    Route::get('/driver-settlement-pdf', [SettlementController::class, 'DriverSettlementPdf'])->name('DriverSettlementPdf');
    Route::resource('employee', EmployeeController::class)->only(['index', 'store', 'edit', 'destroy'])->middleware('employee.permission');
    Route::resource('notifications', NotificationController::class)->only(['index']);
    Route::get('/driver-ecxel-export', [DriverController::class, 'DriverExcelExport'])->name('DriverExcelExport');
    Route::get('/customer-ecxel-export', [CustomerController::class, 'CustomerExcelExport'])->name('CustomerExcelExport');
    Route::get('/employee-ecxel-export', [EmployeeController::class, 'EmployeeExcelExport'])->name('EmployeeExcelExport');
    Route::resource('module-permissions', ModulePermissionController::class)->only(['index'])->middleware('employee.permission');
    Route::resource('driver-live-tracking', LiveTracking::class)->only(['index']);
    Route::get('/booking-status-pdf/{booking_id}', [BookingController::class, 'BookingStatusPdf'])->name('BookingStatusPdf');
    Route::resource('rangefare', RangeFareController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::resource('rangefareairport', RangeFareAirportController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::resource('audit-logs', AuditLogsController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
    Route::resource('/generalpricing', PricingController::class)->names([
    'index' => 'generalpricing'
    ]);
    Route::get('/vehiclepricing', [PricingController::class, 'VehiclePricingView'])->name('vehiclepricing');
    Route::get('/distanceslab', [PricingController::class, 'DistanceSlab'])->name('distanceslab');
    Route::get('/ListFleet', [PricingController::class, 'ListFleet'])->name('ListFleet');
    Route::get('/FixedPrice', [PricingController::class, 'FixedPrice'])->name('FixedPrice');
     Route::get('/EmailTemplate', [PricingController::class, 'EmailTemplate'])->name('EmailTemplate');
    Route::get('/hourlypackage', [PricingsecondController::class,'index']);
    Route::get('/locationcategory', [PricingsecondController::class, 'showdata']);
    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::get('/driver-request', [DriverRequestController::class, 'index'])->name('driver-request');
    
});


require __DIR__ . '/auth.php';

use Illuminate\Support\Facades\{DB, Hash};
use App\Events\NotificationEvent;

Route::get('/query', function () {

});
