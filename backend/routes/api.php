<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Default Laravel Route
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| GoldPlatform Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');

});
use App\Http\Controllers\Api\KimiaController;

Route::prefix('kimia')->group(function () {

    Route::get('/account-groups', [KimiaController::class, 'accountGroups']);

});