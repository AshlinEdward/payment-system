<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Merchant;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PayInTest extends TestCase
{
    use RefreshDatabase;

    public function test_pay_in_can_be_initiated(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'test@example.com',
            'api_key' => 'test-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $response = $this->postJson('/api/pay-ins', [
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'ORDER-1001',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'PENDING');

        $this->assertDatabaseHas('pay_ins', [
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'status' => 'PENDING',
            'customer_reference' => 'ORDER-1001',
        ]);
    }

    public function test_pay_in_requires_valid_amount(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'validation@example.com',
            'api_key' => 'validation-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $response = $this->postJson('/api/pay-ins', [
            'merchant_id' => $merchant->id,
            'amount' => 0,
            'customer_reference' => 'INVALID-ORDER',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'amount',
        ]);
    }

    public function test_successful_pay_in_credits_wallet(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'credit@example.com',
            'api_key' => 'credit-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payIn = app(\App\Services\PaymentService::class)->createPayIn([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'CREDIT-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayIn($payIn, PaymentStatus::SUCCESS);

        $this->assertDatabaseHas('pay_ins', [
            'id' => $payIn->id,
            'status' => 'SUCCESS',
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 10500.00,
        ]);
    }
    public function test_pay_in_creates_initiation_log(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'log@example.com',
            'api_key' => 'log-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $response = $this->postJson('/api/pay-ins', [
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'LOG-TEST',
        ]);

        $response->assertStatus(201);

        $payInId = $response->json('data.id');

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'pay_in_id' => $payInId,
            'event' => 'PAY_IN_INITIATED',
            'status' => 'PENDING',
        ]);
    }

    public function test_successful_pay_in_creates_processing_log(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'processing-log@example.com',
            'api_key' => 'processing-log-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payIn = app(\App\Services\PaymentService::class)->createPayIn([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'PROCESSING-LOG-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayIn($payIn, PaymentStatus::SUCCESS);

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'pay_in_id' => $payIn->id,
            'event' => 'PAY_IN_SUCCESS',
            'status' => 'SUCCESS',
        ]);
    }

    public function test_pending_pay_in_can_be_retried(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'retry@example.com',
            'api_key' => 'retry-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payIn = app(\App\Services\PaymentService::class)->createPayIn([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'RETRY-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayIn($payIn, PaymentStatus::PENDING);

        $this->assertDatabaseHas('pay_ins', [
            'id' => $payIn->id,
            'status' => 'PENDING',
            'processed_at' => null,
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'pay_in_id' => $payIn->id,
            'event' => 'PAY_IN_RETRY',
            'status' => 'PENDING',
        ]);
    }
}