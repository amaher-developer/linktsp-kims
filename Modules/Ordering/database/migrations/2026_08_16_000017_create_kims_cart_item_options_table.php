<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_cart_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id');
            $table->foreignId('option_group_id');
            $table->foreignId('option_id');
            $table->decimal('price_delta', 12, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('cart_item_id', 'idx_cio_item');
            $table->foreign('cart_item_id', 'fk_cio_item')->references('id')->on('kims_cart_items');
            $table->foreign('option_group_id', 'fk_cio_group')->references('id')->on('kims_option_groups');
            $table->foreign('option_id', 'fk_cio_option')->references('id')->on('kims_options');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_cart_item_options');
    }
};
