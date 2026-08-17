<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_external_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id');
            $table->enum('entity_type', ['customer', 'order', 'invoice', 'staff']);
            $table->unsignedBigInteger('entity_id');
            $table->string('external_type', 50)->comment('e.g. foodics_customer, foodics_order, foodics_invoice, foodics_user');
            $table->string('external_id', 100);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['integration_id', 'entity_type', 'entity_id'], 'uq_ext_ref_local');
            $table->unique(['integration_id', 'external_type', 'external_id'], 'uq_ext_ref_external');
            $table->foreign('integration_id', 'fk_ext_ref_integration')->references('id')->on('kims_integrations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_external_references');
    }
};
