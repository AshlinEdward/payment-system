<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayInRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PayInController extends Controller
{
    public function store(
        StorePayInRequest $request,
        PaymentService $paymentService
    ): JsonResponse {
        $payIn = $paymentService->createPayIn($request->validated());

        return response()->json([
            'message' => 'Pay-in initiated successfully.',
            'data' => $payIn,
        ], 201);
    }
}