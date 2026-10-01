<?php
namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class MerchantSeeder extends Seeder
{
    public function run(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'test@merchant.com',
            'api_key' => Str::random(32),
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);
    }
}