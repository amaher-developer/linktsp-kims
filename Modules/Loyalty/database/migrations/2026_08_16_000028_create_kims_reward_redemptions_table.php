<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('loyalty_account_id');
            $table->foreignId('reward_id');
            $table->foreignId('order_id')->nullable();
            $table->unsignedInteger('points_cost');
            $table->enum('status', ['pending', 'redeemed', 'cancelled', 'expired'])->default('pending');
            $table->string('redemption_code', 50);
            $table->dateTime('redeemed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->foreignId('created_by')->nullable()->comment('staff who fulfilled the redemption, if in-branch');
            $table->timestamp('created_at')->useCurrent();

            $table->unique('redemption_code', 'uq_reward_redemptions_code');
            $table->foreign('customer_id', 'fk_rr_customer')->references('id')->on('kims_customers');
            $table->foreign('loyalty_account_id', 'fk_rr_account')->references('id')->on('kims_loyalty_accounts');
            $table->foreign('reward_id', 'fk_rr_reward')->references('id')->on('kims_rewards');
            $table->foreign('order_id', 'fk_rr_order')->references('id')->on('kims_orders');
            $table->foreign('created_by', 'fk_rr_staff')->references('id')->on('kims_staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_reward_redemptions');
    }
};
