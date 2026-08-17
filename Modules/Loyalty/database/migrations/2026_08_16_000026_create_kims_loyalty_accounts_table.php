<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_loyalty_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->integer('balance')->default(0)
                ->comment('cached projection of SUM(points) in kims_loyalty_transactions — kept honest by trg_loyalty_txn_after_insert, never written directly');
            $table->integer('lifetime_earned')->default(0)->comment('cached projection, same rule as balance');
            $table->integer('lifetime_redeemed')->default(0)->comment('cached projection, same rule as balance');
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('customer_id', 'uq_loyalty_accounts_customer');
            $table->foreign('customer_id', 'fk_loyalty_accounts_customer')->references('id')->on('kims_customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_loyalty_accounts');
    }
};
