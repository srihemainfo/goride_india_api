<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1_cre\LoginController;
use App\Http\Controllers\Api\v1_cre\CreJobsController;

// Public CRE routes
Route::post('/login', [LoginController::class, 'Login']);

// Authenticated CRE routes
Route::middleware(['cre.auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/job-list', [CreJobsController::class, 'getJobList']);
    Route::match(['get', 'post'], '/assigned-job-list', [CreJobsController::class, 'getAssignedJobList']);
    Route::get('/job-details', [CreJobsController::class, 'getJobDetails']);
    Route::post('/job-details', [CreJobsController::class, 'getJobDetails']);
    Route::post('/cancel-job', [CreJobsController::class, 'cancelJob']);
    Route::get('/district-list', [CreJobsController::class, 'getDistrictList']);
    Route::post('/send-job-notification', [CreJobsController::class, 'sendJobNotification']);
    Route::get('/get-driver-location', [CreJobsController::class, 'getDriverLocation']);
});
