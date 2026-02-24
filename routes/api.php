<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantPortalController;
use App\Http\Controllers\Api\AuthController;

// PUBLIC ROUTE (No token required to try and log in)
Route::post('/login', [AuthController::class, 'login']);

// SECURE ROUTES (Must have a valid token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tenant/dashboard', [TenantPortalController::class, 'dashboard']);
});