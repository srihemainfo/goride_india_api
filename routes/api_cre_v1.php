<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1_cre\LoginController;
use App\Http\Controllers\Api\v1_cre\CreJobsController;

// Public CRE routes
Route::post('/login', [LoginController::class, 'Login']);

// Authenticated CRE routes
Route::middleware(['cre.auth'])->group(function () {
    Route::get('/job-list', [CreJobsController::class, 'getJobList']);
});
