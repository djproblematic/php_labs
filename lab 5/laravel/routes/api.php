<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ClientAreaController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/products/{id}', [ProductController::class, 'getProductItem']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::middleware('role:Admin')->get('/admin', [AdminController::class, 'index']);
    Route::middleware('role:Manager')->get('/manager', [ManagerController::class, 'index']);
    Route::middleware('role:Client')->get('/client', [ClientAreaController::class, 'index']);

    Route::middleware('role:Admin,Manager')->apiResource('room-types', RoomTypeController::class);
    Route::middleware('role:Admin')->apiResource('rooms', RoomController::class);
    Route::middleware('role:Manager')->apiResource('clients', ClientController::class);
    Route::middleware('role:Manager, Admin')->apiResource('bookings', BookingController::class);
    Route::middleware('role:Admin')->apiResource('payments', PaymentController::class);
});
