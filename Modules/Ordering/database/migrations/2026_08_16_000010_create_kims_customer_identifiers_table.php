<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_customer_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id');
            $table->enum('type', ['qr', 'barcode']);
            $table->string('value', 150);
            $table->boolean('is_primary')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('value', 'uq_identifiers_value');
            $table->index(['customer_id', 'is_active'], 'idx_identifiers_customer');
            $table->foreign('customer_id', 'fk_identifiers_customer')->references('id')->on('kims_customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_customer_identifiers');
    }
};
