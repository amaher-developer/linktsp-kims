<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id');
            $table->string('name', 150);
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('role_id', 'idx_staff_role');
            $table->foreign('role_id', 'fk_staff_role')->references('id')->on('kims_roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_staff');
    }
};
