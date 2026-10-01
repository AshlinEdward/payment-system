<?php

namespace Tests\Feature;

use App\Models\Merchant;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_payout_can_be_initiated(): void
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

        $response = $this->postJson('/api/payouts', [
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'ORDER-1002',
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'PENDING');

        $this->assertDatabaseHas('payouts', [
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'status' => 'PENDING',
            'customer_reference' => 'ORDER-1002',
        ]);
    }

    public function test_payout_requires_valid_amount(): void
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

        $response = $this->postJson('/api/payouts', [
            'merchant_id' => $merchant->id,
            'amount' => 0,
            'customer_reference' => 'INVALID-PAYOUT',
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'amount',
        ]);
    }

    public function test_successful_payout_debits_wallet(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'payout@example.com',
            'api_key' => 'payout-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payout = app(\App\Services\PaymentService::class)->createPayout([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'PAYOUT-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayout($payout, PaymentStatus::SUCCESS);

        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'SUCCESS',
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 9500.00,
        ]);
    }

    public function test_payout_fails_when_wallet_balance_is_insufficient(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'insufficient@example.com',
            'api_key' => 'insufficient-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 1000.00,
        ]);

        $payout = app(\App\Services\PaymentService::class)->createPayout([
            'merchant_id' => $merchant->id,
            'amount' => 2000,
            'customer_reference' => 'INSUFFICIENT-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayout($payout, PaymentStatus::SUCCESS);

        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'FAILED',
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 1000.00,
        ]);
    }

    public function test_completed_payout_cannot_be_processed_again(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'duplicate@example.com',
            'api_key' => 'duplicate-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payout = app(\App\Services\PaymentService::class)->createPayout([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'DUPLICATE-TEST',
        ]);

        $processingService = app(\App\Services\PaymentProcessingService::class);

        // First processing
        $processingService->processPayout(
            $payout,
            PaymentStatus::SUCCESS
        );

        // Try processing the same payout again
        $processingService->processPayout(
            $payout->fresh(),
            PaymentStatus::SUCCESS
        );

        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'SUCCESS',
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 9500.00,
        ]);
    }

    public function test_payout_creates_initiation_log(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'payout-log@example.com',
            'api_key' => 'payout-log-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $response = $this->postJson('/api/payouts', [
            'merchant_id' => $merchant->id,
            'amount' => 300,
            'customer_reference' => 'PAYOUT-LOG-TEST',
        ]);

        $response->assertStatus(201);

        $payoutId = $response->json('data.id');

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'payout_id' => $payoutId,
            'event' => 'PAYOUT_INITIATED',
            'status' => 'PENDING',
        ]);
    }

    public function test_successful_payout_creates_processing_log(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'payout-log@example.com',
            'api_key' => 'payout-log-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payout = app(\App\Services\PaymentService::class)->createPayout([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'PAYOUT-LOG-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayout($payout, PaymentStatus::SUCCESS);

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'payout_id' => $payout->id,
            'event' => 'PAYOUT_SUCCESS',
            'status' => 'SUCCESS',
        ]);
    }

    public function test_pending_payout_can_be_retried(): void
    {
        $merchant = Merchant::create([
            'name' => 'Test Merchant',
            'email' => 'payout-retry@example.com',
            'api_key' => 'payout-retry-api-key',
            'status' => 'ACTIVE',
        ]);

        Wallet::create([
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $payout = app(\App\Services\PaymentService::class)->createPayout([
            'merchant_id' => $merchant->id,
            'amount' => 500,
            'customer_reference' => 'PAYOUT-RETRY-TEST',
        ]);

        app(\App\Services\PaymentProcessingService::class)
            ->processPayout($payout, PaymentStatus::PENDING);

        $this->assertDatabaseHas('payouts', [
            'id' => $payout->id,
            'status' => 'PENDING',
            'processed_at' => null,
        ]);

        $this->assertDatabaseHas('wallets', [
            'merchant_id' => $merchant->id,
            'balance' => 10000.00,
        ]);

        $this->assertDatabaseHas('payment_logs', [
            'merchant_id' => $merchant->id,
            'payout_id' => $payout->id,
            'event' => 'PAYOUT_RETRY',
            'status' => 'PENDING',
        ]);
    }
}