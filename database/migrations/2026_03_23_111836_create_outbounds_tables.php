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

            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('staff_id')->constrained('users');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses');

            $table->enum('type', ['sales', 'internal', 'adjustment', 'return_to_supplier'])->default('sales');
            $table->text('reason')->nullable();

            $table->enum('status', ['pending', 'picking', 'completed', 'cancelled'])->default('pending'); // Thêm trạng thái picking

            $table->timestamps();
            $table->softDeletes(); // Thêm xóa mềm chứng từ
        });

        Schema::create('outbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_id')->constrained('outbound_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');
            $table->foreignId('location_id')->nullable()->constrained('locations');
            
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