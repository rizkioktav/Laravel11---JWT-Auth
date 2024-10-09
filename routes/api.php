<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SaleController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/customers', [CustomerController::class, 'getAll']);

Route::middleware('auth:api')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Customer Routes
    Route::get('/customer/whoIsLogin', [CustomerController::class, 'whoIsLogin']);
    Route::put('/customer/update', [CustomerController::class, 'update']); 
    Route::delete('/customer/delete', [CustomerController::class, 'delete']);
    // Sale Routes
    Route::post('/customer/sale', [SaleController::class, 'sale']);
});
