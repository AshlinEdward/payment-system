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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('merchant_id')
                ->constrained('merchants')
                ->cascadeOnDelete();

            $table->string('transaction_id')->unique();

            $table->decimal('amount', 15, 2);

            $table->enum('status', [
                'PENDING',
                'SUCCESS',
                'FAILED'
            ])->default('PENDING');

            $table->string('customer_reference')->nullable();

            $table->json('request_payload')->nullable();

            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index(['merchant_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
