<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_group_id');
            $table->unsignedBigInteger('foodics_id')->nullable();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->decimal('price_delta', 12, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['option_group_id', 'foodics_id'], 'uq_options_group_foodics');
            $table->index('option_group_id', 'idx_options_group');
            $table->foreign('option_group_id', 'fk_options_group')->references('id')->on('kims_option_groups');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_options');
    }
};
