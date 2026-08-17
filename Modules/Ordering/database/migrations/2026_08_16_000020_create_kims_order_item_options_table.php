<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kims_order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id');
            $table->foreignId('option_group_id')->nullable();
            $table->foreignId('option_id')->nullable();
            $table->unsignedBigInteger('foodics_option_id')->nullable();
            $table->string('option_group_name_en', 150);
            $table->string('option_group_name_ar', 150);
            $table->string('option_name_en', 150);
            $table->string('option_name_ar', 150);
            $table->decimal('price_delta', 12, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_item_id', 'idx_oio_item');
            $table->foreign('order_item_id', 'fk_oio_item')->references('id')->on('kims_order_items');
            $table->foreign('option_group_id', 'fk_oio_option_group')->references('id')->on('kims_option_groups')->nullOnDelete();
            $table->foreign('option_id', 'fk_oio_option')->references('id')->on('kims_options')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kims_order_item_options');
    }
};
