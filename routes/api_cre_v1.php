<?php

use App\Http\Controllers\Api\v1_cre\LoginController;

Route::middleware(['admin.apis'])->group(function () {
    
Route::post('/login', [LoginController::class, 'Login']);

});
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World']);
});