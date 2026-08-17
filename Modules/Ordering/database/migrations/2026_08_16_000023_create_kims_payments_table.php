<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->string('provider', 50)->comment('payment gateway name');
            $table->enum('method', ['card', 'wallet']);
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('EGP');
            $table->string('transaction_id', 150)->nullable();
            $table->string('provider_reference', 150)->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending')
                ->comment('refunded = at least one kims_refunds row against this payment has reached completed; app/webhook handler keeps this in sync');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('order_id', 'idx_payments_order');
            $table->index('status', 'idx_payments_status');
            $table->foreign('order_id', 'fk_payments_order')->references('id')->on('kims_orders');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_payments');
    }
};
