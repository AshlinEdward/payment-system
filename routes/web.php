<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PayInController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\PaymentLogController;  
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/merchants', [MerchantController::class, 'index'])->name('merchants.index');
Route::get('/pay-ins', [PayInController::class, 'index'])->name('pay-ins.index');
Route::get('/payouts', [PayoutController::class, 'index'])->name('payouts.index');
Route::get('/payment-logs', [PaymentLogController::class, 'index'])->name('payment-logs.index');

Route::get('/pay-ins/create', [PayInController::class, 'create'])->name('pay-ins.create');
Route::get('/payouts/create', [PayoutController::class, 'create'])->name('payouts.create');
Route::post('/pay-ins', [PayInController::class, 'store'])->name('pay-ins.store');
Route::post('/payouts', [PayoutController::class, 'store'])->name('payouts.store');
Route::get('/merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
Route::post('/merchants', [MerchantController::class, 'store'])->name('merchants.store');