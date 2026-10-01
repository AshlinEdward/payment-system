<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->foreignId('pay_in_id')
                ->nullable()
                ->constrained('pay_ins')
                ->cascadeOnDelete();

            $table->foreignId('payout_id')
                ->nullable()
                ->constrained('payouts')
                ->cascadeOnDelete();

            $table->string('event');

            $table->string('status')->nullable();

            $table->text('message')->nullable();

            $table->json('request_payload')->nullable();

            $table->json('response_payload')->nullable();

            $table->timestamps();

            $table->index('event');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
