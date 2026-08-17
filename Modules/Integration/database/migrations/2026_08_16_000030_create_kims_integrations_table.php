<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('foodics');
            $table->string('name', 100);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('credentials')->comment('application-layer encrypted (e.g. Laravel encrypted cast) — never plain JSON');
            $table->json('settings')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_integrations');
    }
};
