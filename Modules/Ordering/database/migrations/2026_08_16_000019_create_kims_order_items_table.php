<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('product_id')->nullable();
            $table->unsignedBigInteger('foodics_product_id')->nullable();
            $table->string('product_name_en', 150)->comment('snapshot at order time');
            $table->string('product_name_ar', 150)->comment('snapshot at order time');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('order_id', 'idx_order_items_order');
            $table->foreign('order_id', 'fk_order_items_order')->references('id')->on('kims_orders');
            $table->foreign('product_id', 'fk_order_items_product')->references('id')->on('kims_products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_order_items');
    }
};
