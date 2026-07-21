<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KimiaController;
use App\Http\Controllers\Api\Auth\RegistrationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    // OTP
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    // Registration
    Route::post('/register', [RegistrationController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});

/*
|--------------------------------------------------------------------------
| Kimia API
|--------------------------------------------------------------------------
*/

Route::prefix('kimia')->group(function () {

    Route::get('/account-groups', [KimiaController::class, 'accountGroups']);

});

/*
|--------------------------------------------------------------------------
| Additional API Routes
|--------------------------------------------------------------------------
*/

if (file_exists(__DIR__.'/kimia.php')) {
    require __DIR__.'/kimia.php';
}