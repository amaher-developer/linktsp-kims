<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('cashier, barista, manager, admin');
            $table->json('permissions')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('name', 'uq_roles_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_roles');
    }
};
