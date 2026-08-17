<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_integration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id');
            $table->enum('direction', ['foodics_to_kims', 'kims_to_foodics']);
            $table->string('operation', 50)->comment('sync_products, sync_orders, verify_invoice, get_invoice, ...');
            $table->string('entity_type', 50)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('external_id', 100)->nullable();
            $table->enum('status', ['success', 'failed']);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->string('error_code', 50)->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('attempts')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['integration_id', 'created_at'], 'idx_logs_integration_created');
            $table->index('status', 'idx_logs_status');
            $table->foreign('integration_id', 'fk_logs_integration')->references('id')->on('kims_integrations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_integration_logs');
    }
};
