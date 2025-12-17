<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\EnsureTokenIsValid;


Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('auth')->group(function () {
    // Register and login (with rate limiting)
        Route::post('/register', [AuthController::class, 'register'])
            ->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('auth.login');


    // Protected authentication routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])
            ->name('auth.logout-all');
        Route::get('/user', [AuthController::class, 'user'])
            ->name('auth.user');

        // Email verification routes
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('/email/resend', [AuthController::class, 'resendVerification'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
    });
});



// Public blog routes (viewable by anyone)
Route::prefix('blogs')->group(function () {
    Route::get('/', [BlogController::class, 'index'])
        ->name('blogs.index');
    Route::get('/{id}', [BlogController::class, 'show'])
        ->name('blogs.show')
        ->where('id', '[0-9]+');
    Route::get('/user/{userId}', [BlogController::class, 'userBlogs'])
        ->name('blogs.user')
        ->where('userId', '[0-9]+');
});



Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found',
    ], 404);
});
