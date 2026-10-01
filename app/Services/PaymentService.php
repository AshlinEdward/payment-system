<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Merchant;
use App\Models\PayIn;
use App\Models\Payout;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function createPayIn(array $data): PayIn
    {
        return DB::transaction(function () use ($data) {

            $merchant = Merchant::findOrFail($data['merchant_id']);

            if ($merchant->status !== 'ACTIVE') {
                throw new \Exception('Merchant is inactive.');
            }

            $transactionId = 'PI-' . strtoupper(Str::uuid()->toString());

            $payIn = PayIn::create([
                'merchant_id' => $merchant->id,
                'transaction_id' => $transactionId,
                'amount' => $data['amount'],
                'status' => PaymentStatus::PENDING,
                'customer_reference' => $data['customer_reference'] ?? null,
                'request_payload' => $data,
            ]);

            PaymentLog::create([
                'merchant_id' => $merchant->id,
                'pay_in_id' => $payIn->id,
                'event' => 'PAY_IN_INITIATED',
                'status' => PaymentStatus::PENDING->value,
                'message' => 'Pay-in initiated successfully.',
                'request_payload' => $data,
            ]);

            return $payIn;
        });
    }

    public function createPayout(array $data): Payout
    {
        return DB::transaction(function () use ($data) {

            $merchant = Merchant::findOrFail($data['merchant_id']);

            if ($merchant->status !== 'ACTIVE') {
                throw new \Exception('Merchant is inactive.');
            }

            $transactionId = 'PO-' . strtoupper(Str::uuid()->toString());

            $payout = Payout::create([
                'merchant_id' => $merchant->id,
                'transaction_id' => $transactionId,
                'amount' => $data['amount'],
                'status' => PaymentStatus::PENDING,
                'customer_reference' => $data['customer_reference'] ?? null,
                'request_payload' => $data,
            ]);

            PaymentLog::create([
                'merchant_id' => $merchant->id,
                'payout_id' => $payout->id,
                'event' => 'PAYOUT_INITIATED',
                'status' => PaymentStatus::PENDING->value,
                'message' => 'Payout initiated successfully.',
                'request_payload' => $data,
            ]);

            return $payout;
        });
    }
}