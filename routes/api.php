<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\EnsureTokenIsValid;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/blogs', [BlogController::class,'index']);
    Route::post('/blogs', [BlogController::class,'store']);
    Route::get('/blogs/{id}', [BlogController::class,'show']);
    Route::put('/blogs/{id}', [BlogController::class,'update']);
    Route::delete('/blogs/{id}', [BlogController::class,'destroy']);
});



Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);



