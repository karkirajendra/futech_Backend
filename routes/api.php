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
    // Register and login
        Route::post('/register', [AuthController::class, 'register'])
            ->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])
            ->name('auth.login');



         // Email verification routes
         Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
             ->middleware(['signed', 'throttle:6,1'])
             ->name('verification.verify');

         Route::post('/email/resend', [AuthController::class, 'resendVerification'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
    });

// Convenience aliases so you can hit /api/register and /api/login directly
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');




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

    // Create blog
    Route::post('/', [BlogController::class, 'store'])
        ->name('blogs.store');
});



Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found',
    ], 404);
});
Route::post('/test', function() {
    return response()->json(['success' => true, 'message' => 'POST works']);
});

