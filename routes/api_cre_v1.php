<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1_cre\LoginController;

// Public CRE routes
Route::post('/login', [LoginController::class, 'Login']);

// Authenticated CRE routes
Route::middleware(['cre.auth'])->group(function () {
    Route::get('/hello', function () {
        return response()->json([
            'status'  => true,
            'message' => 'hello world'
        ]);
    });
});
