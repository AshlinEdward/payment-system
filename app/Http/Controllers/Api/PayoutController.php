<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayoutRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PayoutController extends Controller
{
    public function store(
        StorePayoutRequest $request,
        PaymentService $paymentService
    ): JsonResponse {
        $payout = $paymentService->createPayout($request->validated());

        return response()->json([
            'message' => 'Payout initiated successfully.',
            'data' => $payout,
        ], 201);
    }
}