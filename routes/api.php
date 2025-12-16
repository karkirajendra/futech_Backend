<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\EnsureTokenIsValid;

Route::apiResource('blogs', BlogController::class);


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/email/verify/{id}/{hash}', [AuthController::class,'verifyEmail'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class,'resendVerification']);
});


