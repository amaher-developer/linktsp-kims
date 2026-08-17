<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_product_option_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('option_group_id');
            $table->integer('sort_order')->default(0);

            $table->unique(['product_id', 'option_group_id'], 'uq_product_option_group');
            $table->foreign('product_id', 'fk_pog_product')->references('id')->on('kims_products');
            $table->foreign('option_group_id', 'fk_pog_group')->references('id')->on('kims_option_groups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_product_option_groups');
    }
};
