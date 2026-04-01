<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Phiếu kiểm kê kho
        Schema::create('stock_takes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('staff_id')->constrained('users'); // Người tạo phiếu
            
            $table->string('code')->unique(); // Mã phiếu kiểm kê (VD: KK-202310-001)
            $table->enum('status', ['draft', 'counting', 'reviewing', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // Chi tiết từng mặt hàng kiểm kê
        Schema::create('stock_take_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_take_id')->constrained('stock_takes')->cascadeOnDelete();
            
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');
            
            $table->integer('expected_quantity'); // Số lượng tồn trên hệ thống lúc bắt đầu kiểm
            $table->integer('counted_quantity')->nullable(); // Số lượng đếm thực tế bằng tay/máy quét
            $table->integer('variance')->nullable(); // Độ lệch (counted - expected)
            
            $table->text('reason')->nullable(); // Lý do lệch (nếu có)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_take_items');
        Schema::dropIfExists('stock_takes');
    }
};