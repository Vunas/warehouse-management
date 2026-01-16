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
            $table->integer('warehouse_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->foreignId('calc_id')->nullable()->constrained('calculated_slots');
            $table->dateTime('imported_at');
            $table->integer('current_quantity')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};