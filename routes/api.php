<?php

use App\Http\Controllers\Api\PayInController;
use App\Http\Controllers\Api\PayoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/pay-ins', [PayInController::class, 'store']);
Route::post('/payouts', [PayoutController::class, 'store']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
