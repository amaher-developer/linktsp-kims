<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id');
            $table->foreignId('product_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('cart_id', 'idx_cart_items_cart');
            $table->foreign('cart_id', 'fk_cart_items_cart')->references('id')->on('kims_carts');
            $table->foreign('product_id', 'fk_cart_items_product')->references('id')->on('kims_products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_cart_items');
    }
};
