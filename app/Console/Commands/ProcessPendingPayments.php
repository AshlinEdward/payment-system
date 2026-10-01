<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\PayIn;
use App\Models\Payout;
use App\Services\PaymentProcessingService;
use Illuminate\Console\Command;

class ProcessPendingPayments extends Command
{
    protected $signature = 'payments:process';

    protected $description = 'Process pending pay-ins and payouts';

    public function handle(PaymentProcessingService $processingService): int
    {
        PayIn::where('status', PaymentStatus::PENDING)
            ->get()
            ->each(function (PayIn $payIn) use ($processingService) {

                 $status = $this->randomStatus();

                $processingService->processPayIn($payIn, $status);
            });

        Payout::where('status', PaymentStatus::PENDING)
            ->get()
            ->each(function (Payout $payout) use ($processingService) {

               $status = $this->randomStatus();

                $processingService->processPayout($payout, $status);
            });

        $this->info('Pending payments processed.');

        return self::SUCCESS;
    }

    private function randomStatus(): PaymentStatus
    {
        return PaymentStatus::from(
            collect([
                PaymentStatus::SUCCESS->value,
                PaymentStatus::FAILED->value,
                PaymentStatus::PENDING->value,
            ])->random()
        );
    }
}