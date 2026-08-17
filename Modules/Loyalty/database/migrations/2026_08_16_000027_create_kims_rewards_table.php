<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->unsignedBigInteger('foodics_product_id')->nullable();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->unsignedInteger('points_cost');
            $table->enum('reward_type', ['product', 'discount']);
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('product_id', 'fk_rewards_product')->references('id')->on('kims_products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_rewards');
    }
};
