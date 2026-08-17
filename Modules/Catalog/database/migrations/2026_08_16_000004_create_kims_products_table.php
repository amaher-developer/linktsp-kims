<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foodics_id');
            $table->foreignId('category_id')->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->boolean('is_available')->default(true)
                ->comment('global kill switch, overridden per branch by kims_branch_products');
            $table->boolean('is_active')->default(true);
            $table->dateTime('synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('foodics_id', 'uq_products_foodics_id');
            $table->index('category_id', 'idx_products_category');
            $table->foreign('category_id', 'fk_products_category')->references('id')->on('kims_categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_products');
    }
};
