<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('staff_id')->constrained('users');
            $table->enum('type', ['sales', 'internal', 'adjustment', 'return_to_supplier'])->default('sales');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // Thêm ghi chú
            
            $table->timestamps();
            $table->softDeletes(); // Xóa mềm chứng từ
        });

        Schema::create('inbound_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inbound_id')->constrained('inbound_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');            
            $table->foreignId('location_id')->nullable()->constrained('locations');

            $table->integer('quantity');
            $table->decimal('price', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_items');
        Schema::dropIfExists('inbound_orders');
    }
};