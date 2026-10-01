<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\PayIn;
use App\Models\Payout;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\DB;

class PaymentProcessingService
{
    public function __construct(
        private WalletService $walletService
    ) {
    }

    public function processPayIn(PayIn $payIn, PaymentStatus $status): void
    {
        DB::transaction(function () use ($payIn, $status) {

            $lockedPayIn = PayIn::where('id', $payIn->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedPayIn || $lockedPayIn->status !== PaymentStatus::PENDING) {
                return;
            }

            if ($status === PaymentStatus::SUCCESS) {
                $wallet = $this->walletService->findForUpdate(
                    $lockedPayIn->merchant_id
                );

                $this->walletService->credit(
                    $wallet,
                    (float) $lockedPayIn->amount
                );
            }

            $lockedPayIn->update([
                'status' => $status,
                'processed_at' => $status !== PaymentStatus::PENDING
                    ? now()
                    : null,
            ]);

            PaymentLog::create([
                'merchant_id' => $lockedPayIn->merchant_id,
                'pay_in_id' => $lockedPayIn->id,
                'event' => match ($status) {
                    PaymentStatus::SUCCESS => 'PAY_IN_SUCCESS',
                    PaymentStatus::FAILED => 'PAY_IN_FAILED',
                    PaymentStatus::PENDING => 'PAY_IN_RETRY',
                },
                'status' => $status->value,
                'message' => match ($status) {
                    PaymentStatus::SUCCESS => 'Pay-in processed successfully.',
                    PaymentStatus::FAILED => 'Pay-in processing failed.',
                    PaymentStatus::PENDING => 'Pay-in remains pending and will be retried.',
                },
            ]);
        });
    }

    public function processPayout(Payout $payout, PaymentStatus $status): void
    {
        DB::transaction(function () use ($payout, $status) {

            $lockedPayout = Payout::where('id', $payout->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedPayout || $lockedPayout->status !== PaymentStatus::PENDING) {
                return;
            }

            if ($status === PaymentStatus::SUCCESS) {
                $wallet = $this->walletService->findForUpdate(
                    $lockedPayout->merchant_id
                );

                if ($wallet->balance < $lockedPayout->amount) {
                    $status = PaymentStatus::FAILED;
                } else {
                    $this->walletService->debit(
                        $wallet,
                        (float) $lockedPayout->amount
                    );
                }
            }

            $lockedPayout->update([
                'status' => $status,
                'processed_at' => $status !== PaymentStatus::PENDING
                    ? now()
                    : null,
            ]);

            PaymentLog::create([
                'merchant_id' => $lockedPayout->merchant_id,
                'payout_id' => $lockedPayout->id,
                'event' => match ($status) {
                    PaymentStatus::SUCCESS => 'PAYOUT_SUCCESS',
                    PaymentStatus::FAILED => 'PAYOUT_FAILED',
                    PaymentStatus::PENDING => 'PAYOUT_RETRY',
                },
                'status' => $status->value,
                'message' => match ($status) {
                    PaymentStatus::SUCCESS => 'Payout processed successfully.',
                    PaymentStatus::FAILED => 'Payout processing failed.',
                    PaymentStatus::PENDING => 'Payout remains pending and will be retried.',
                },
            ]);
        });
    }
}