<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_account_id');
            $table->foreignId('customer_id');
            $table->enum('type', ['earn', 'redeem', 'refund', 'reversal', 'bonus', 'adjustment', 'expire']);
            $table->integer('points')->comment('signed: positive for earn/bonus, negative for redeem/expire');
            $table->integer('balance_before')->comment('set by trg_loyalty_txn_before_insert — app should pass a placeholder (e.g. 0), not compute this itself');
            $table->integer('balance_after')->comment('set by trg_loyalty_txn_before_insert — app should pass a placeholder (e.g. 0), not compute this itself');
            $table->foreignId('order_id')->nullable()->comment('set for Grab & Go / Dine In earn');
            $table->foreignId('invoice_id')->nullable()->comment('set for Take Away earn (verified invoice)');
            $table->foreignId('reward_redemption_id')->nullable()->comment('set for redeem');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->comment('staff, when awarded via the cashier flow');
            $table->timestamp('created_at')->useCurrent();

            $table->index('loyalty_account_id', 'idx_lt_account');
            $table->index('invoice_id', 'idx_lt_invoice');
            $table->foreign('loyalty_account_id', 'fk_lt_account')->references('id')->on('kims_loyalty_accounts');
            $table->foreign('customer_id', 'fk_lt_customer')->references('id')->on('kims_customers');
            $table->foreign('order_id', 'fk_lt_order')->references('id')->on('kims_orders');
            $table->foreign('invoice_id', 'fk_lt_invoice')->references('id')->on('kims_invoices');
            $table->foreign('reward_redemption_id', 'fk_lt_redemption')->references('id')->on('kims_reward_redemptions');
            $table->foreign('created_by', 'fk_lt_staff')->references('id')->on('kims_staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_loyalty_transactions');
    }
};
