<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\SaleController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customer/sale', [SaleController::class, 'index']); 

Route::middleware('auth:api')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Customer Routes
    Route::get('/customer/whoIsLogin', [CustomerController::class, 'show']);
    Route::put('/customer/update', [CustomerController::class, 'update']); 
    Route::delete('/customer/destroy', [CustomerController::class, 'destroy']);
    
    // Sale Routes
    Route::post('/customer/sale/store', [SaleController::class, 'store']); 
    Route::get('/customer/sale/show/{id}', [SaleController::class, 'show']); 
    Route::get('/customer/sale/show', [SaleController::class, 'showSalesByCustomer']);
    Route::put('/customer/sale/update/{id}', [SaleController::class, 'update']); 
    Route::delete('/customer/sale/destroy/{id}', [SaleController::class, 'destroy']);
});

