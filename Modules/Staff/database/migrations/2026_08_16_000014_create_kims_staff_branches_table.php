<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_staff_branches', function (Blueprint $table) {
            $table->foreignId('staff_id');
            $table->foreignId('branch_id');
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['staff_id', 'branch_id']);
            $table->foreign('staff_id', 'fk_sb_staff')->references('id')->on('kims_staff');
            $table->foreign('branch_id', 'fk_sb_branch')->references('id')->on('kims_branches');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_staff_branches');
    }
};
