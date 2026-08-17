<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('mobile', 30);
            $table->string('email', 150)->nullable();
            $table->string('password')->comment('hashed at application layer (bcrypt/argon2)');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('mobile', 'uq_customers_mobile');
            $table->unique('email', 'uq_customers_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_customers');
    }
};
