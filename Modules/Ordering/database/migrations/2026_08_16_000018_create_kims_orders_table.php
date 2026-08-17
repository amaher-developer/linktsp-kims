<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50);
            $table->foreignId('cart_id');
            $table->foreignId('customer_id');
            $table->foreignId('branch_id');
            $table->enum('order_type', ['grab_go', 'dine_in']);
            $table->enum('status', ['confirmed', 'preparing', 'ready', 'collected', 'cancelled'])->default('confirmed');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('customer_note')->nullable();
            $table->dateTime('placed_at');
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('preparing_at')->nullable();
            $table->dateTime('ready_at')->nullable();
            $table->dateTime('collected_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('order_number', 'uq_orders_number');
            $table->index(['branch_id', 'status'], 'idx_orders_branch_status');
            $table->index('customer_id', 'idx_orders_customer');
            $table->foreign('cart_id', 'fk_orders_cart')->references('id')->on('kims_carts');
            $table->foreign('customer_id', 'fk_orders_customer')->references('id')->on('kims_customers');
            $table->foreign('branch_id', 'fk_orders_branch')->references('id')->on('kims_branches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_orders');
    }
};
