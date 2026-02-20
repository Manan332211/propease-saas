<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantPortalController;

// All routes inside this block require a valid Sanctum API Token (or session)
Route::middleware('auth:sanctum')->group(function () {
    
    // Returns the logged-in user's basic details
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Our new Tenant Dashboard endpoint
    Route::get('/tenant/dashboard', [TenantPortalController::class, 'dashboard']);
    
});