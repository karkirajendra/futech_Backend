<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\EnsureTokenIsValid;

Route::apiResource('blogs', BlogController::class);


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);


