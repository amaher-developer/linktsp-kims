<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable();
            $table->foreignId('branch_id');
            $table->string('invoice_number', 100);
            $table->enum('source', ['customer_app', 'pos']);
            $table->decimal('total_amount', 12, 2);
            $table->dateTime('issued_at');
            $table->dateTime('verified_at')->nullable()
                ->comment('set when a cashier verifies this invoice against Foodics for Take Away loyalty');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('invoice_number', 'uq_invoices_number');
            $table->unique('order_id', 'uq_invoices_order');
            $table->foreign('order_id', 'fk_invoices_order')->references('id')->on('kims_orders');
            $table->foreign('branch_id', 'fk_invoices_branch')->references('id')->on('kims_branches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_invoices');
    }
};
