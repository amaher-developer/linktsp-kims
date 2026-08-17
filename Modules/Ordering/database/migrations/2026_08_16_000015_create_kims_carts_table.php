<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->foreignId('branch_id');
            $table->enum('order_type', ['grab_go', 'dine_in']);
            $table->enum('status', ['active', 'checked_out', 'abandoned'])->default('active');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('note')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['customer_id', 'status'], 'idx_carts_customer_status');
            $table->foreign('customer_id', 'fk_carts_customer')->references('id')->on('kims_customers');
            $table->foreign('branch_id', 'fk_carts_branch')->references('id')->on('kims_branches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_carts');
    }
};
