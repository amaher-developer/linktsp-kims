<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('priority')->default(0)->comment('higher wins when more than one rule is active for the same date');
            $table->decimal('earn_points_rate', 12, 4)->comment('points earned per earn_amount_unit spent');
            $table->decimal('earn_amount_unit', 12, 4)->default(1);
            $table->decimal('redeem_points_unit', 12, 4)->comment('points required per redeem_value');
            $table->decimal('redeem_value', 12, 4);
            $table->unsignedInteger('minimum_redeem_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['is_active', 'priority'], 'idx_loyalty_rules_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_loyalty_rules');
    }
};
