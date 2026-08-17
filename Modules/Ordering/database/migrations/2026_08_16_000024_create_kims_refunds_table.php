<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id');
            $table->foreignId('order_id');
            $table->decimal('amount', 12, 2)->comment('independent of payments.amount to allow partial refunds');
            $table->string('reason')->nullable();
            $table->enum('initiated_by_type', ['customer', 'staff', 'system']);
            $table->unsignedBigInteger('initiated_by_id')->nullable()
                ->comment('polymorphic: customers.id or staff.id depending on initiated_by_type — not FK-enforced, same reasoning as kims_order_status_history.changed_by_id');
            $table->foreignId('approved_by')->nullable()->comment('staff.id — NULL if auto-approved or still pending review');
            $table->enum('status', ['requested', 'approved', 'rejected', 'processing', 'completed', 'failed'])->default('requested');
            $table->string('provider_reference', 150)->nullable()->comment('Paymob refund transaction id');
            $table->dateTime('requested_at')->useCurrent();
            $table->dateTime('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index('payment_id', 'idx_refunds_payment');
            $table->index('order_id', 'idx_refunds_order');
            $table->foreign('payment_id', 'fk_refunds_payment')->references('id')->on('kims_payments');
            $table->foreign('order_id', 'fk_refunds_order')->references('id')->on('kims_orders');
            $table->foreign('approved_by', 'fk_refunds_approver')->references('id')->on('kims_staff');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_refunds');
    }
};
