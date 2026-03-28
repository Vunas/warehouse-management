<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('staff_id')->constrained('users');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('outbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_id')->constrained('outbound_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_items');
        Schema::dropIfExists('outbound_orders');
    }
};