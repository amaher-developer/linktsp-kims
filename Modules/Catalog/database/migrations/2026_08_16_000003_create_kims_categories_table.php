<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foodics_id');
            $table->foreignId('parent_id')->nullable();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->string('image_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('foodics_id', 'uq_categories_foodics_id');
            $table->index('parent_id', 'idx_categories_parent');
            $table->foreign('parent_id', 'fk_categories_parent')->references('id')->on('kims_categories');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_categories');
    }
};
