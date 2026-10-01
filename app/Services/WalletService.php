<?php

namespace App\Services;

use App\Models\Wallet;

class WalletService
{
    public function credit(Wallet $wallet, float $amount): void
    {
        $wallet->balance += $amount;
        $wallet->save();
    }

    public function debit(Wallet $wallet, float $amount): void
    {
        if ($wallet->balance < $amount) {
            throw new \Exception('Insufficient wallet balance.');
        }

        $wallet->balance -= $amount;
        $wallet->save();
    }

    public function findForUpdate(int $merchantId): Wallet
    {
        return Wallet::where('merchant_id', $merchantId)
            ->lockForUpdate()
            ->firstOrFail();
    }
}