<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('foodics_id');
            $table->string('name_en', 150);
            $table->string('name_ar', 150);
            $table->string('code', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone', 50)->nullable();
            $table->boolean('accepts_grab_go')->default(true);
            $table->boolean('accepts_dine_in')->default(true);
            $table->boolean('is_active')->default(true);
            $table->dateTime('synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('foodics_id', 'uq_branches_foodics_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_branches');
    }
};
