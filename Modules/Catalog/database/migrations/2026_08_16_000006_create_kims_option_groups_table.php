<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_option_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foodics_id')->nullable();
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->unsignedInteger('min_select')->default(0);
            $table->unsignedInteger('max_select')->default(1);
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('foodics_id', 'uq_option_groups_foodics_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_option_groups');
    }
};
