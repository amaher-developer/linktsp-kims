<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->enum('changed_by_type', ['staff', 'system', 'customer']);
            $table->unsignedBigInteger('changed_by_id')->nullable()
                ->comment('polymorphic: staff.id or customers.id depending on changed_by_type, NULL when system — not FK-enforced, a single hard FK to one table would silently mismatch rows from the other');
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id', 'idx_osh_order');
            $table->foreign('order_id', 'fk_osh_order')->references('id')->on('kims_orders');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_order_status_history');
    }
};
