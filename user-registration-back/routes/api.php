<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


    Route::prefix('auth')->group(function () {
        Route::get('google/url', [AuthController::class, 'getGoogleAuthUrl']);
        Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/complete-registration', [UserController::class, 'completeRegistration']);
        Route::post('/{userId}/send-email', [UserController::class, 'sendEmail']);
    });
