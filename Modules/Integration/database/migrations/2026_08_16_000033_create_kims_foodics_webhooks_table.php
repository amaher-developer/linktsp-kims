<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_foodics_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id');
            $table->string('event_id', 150);
            $table->string('event_type', 100);
            $table->json('payload');
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->dateTime('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('event_id', 'uq_webhooks_event_id');
            $table->index('integration_id', 'idx_webhooks_integration');
            $table->foreign('integration_id', 'fk_webhooks_integration')->references('id')->on('kims_integrations');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_foodics_webhooks');
    }
};
