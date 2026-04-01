<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->foreignId('staff_id')->constrained('users');

            $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Thêm xóa mềm
        });

        Schema::create('transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('stock_transfers')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');
            $table->foreignId('inventory_id')->constrained('inventory');

            $table->foreignId('from_location_id')->nullable()->constrained('locations'); // Thêm rõ xuất từ đâu
            $table->foreignId('to_location_id')->nullable()->constrained('locations');

            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
