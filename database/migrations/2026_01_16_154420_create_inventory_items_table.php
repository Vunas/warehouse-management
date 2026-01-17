<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('storage_blocks');
            $table->integer('product_id')->unsigned();
            $table->foreignId('calc_id')->nullable()->constrained('calculated_slots');
            $table->integer('slot_used');
            $table->dateTime('imported_at');
            $table->integer('current_quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
