<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ItemController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Tenant-specific routes (must use a subdomain)
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('items', ItemController::class);
});


Route::middleware(['auth:sanctum'])->group(function () {
    // Route::post('/logout', [AuthController::class, 'logout']);
});