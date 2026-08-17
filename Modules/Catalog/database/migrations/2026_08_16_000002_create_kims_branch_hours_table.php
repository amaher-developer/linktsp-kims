<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_branch_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->unsignedTinyInteger('day_of_week')->comment('0=Sunday .. 6=Saturday');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['branch_id', 'day_of_week'], 'uq_branch_hours_day');
            $table->foreign('branch_id', 'fk_branch_hours_branch')->references('id')->on('kims_branches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_branch_hours');
    }
};
