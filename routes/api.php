<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\QRCodeController;
use App\Http\Controllers\Api\SalesController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['tenant'])->group(function () {
    Route::get('/retrieve-token', [AuthController::class, 'retrieve']);
});

// Tenant-specific routes (must use a subdomain)
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('items', ItemController::class);
    
    Route::post('/qr-code/generate', [QRCodeController::class, 'generate']);
    
    Route::post('/upload-csv', [SalesController::class, 'upload']);
});

