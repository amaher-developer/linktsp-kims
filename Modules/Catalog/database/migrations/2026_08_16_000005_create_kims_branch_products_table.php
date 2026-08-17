<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_branch_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('product_id');
            $table->decimal('price_override', 12, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['branch_id', 'product_id'], 'uq_branch_product');
            $table->foreign('branch_id', 'fk_branch_products_branch')->references('id')->on('kims_branches');
            $table->foreign('product_id', 'fk_branch_products_product')->references('id')->on('kims_products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_branch_products');
    }
};
