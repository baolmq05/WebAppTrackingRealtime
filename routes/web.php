<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
Route::post('/orders/{order}/start-delivery', [OrderController::class, 'startDelivery'])->name('orders.start-delivery');
Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
Route::get('/orders/{order}/map', [OrderController::class, 'showMap'])->name('orders.map');
Route::post('/orders/{order}/update-location', [OrderController::class, 'updateLocation'])->name('orders.update-location');
Route::get('/api/orders/{order}', [OrderController::class, 'getOrderData'])->name('api.orders.show');