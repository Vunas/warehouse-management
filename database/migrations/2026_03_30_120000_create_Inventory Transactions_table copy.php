<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng SỔ KHO (Rất quan trọng cho WMS)
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches');
            
            // Kiểu giao dịch: Nhập, Xuất, Chuyển kho, Điều chỉnh
            $table->enum('transaction_type', ['inbound', 'outbound', 'transfer', 'adjustment']);
            
            // Lưu ID của chứng từ liên quan (inbound_id, outbound_id, transfer_id)
            $table->unsignedBigInteger('reference_id')->nullable(); 
            
            // Số lượng thay đổi (dương là nhập, âm là xuất)
            $table->integer('quantity_change'); 
            
            // Số lượng tồn ngay sau khi thay đổi (chốt sổ tại thời điểm đó)
            $table->integer('balance_after'); 

            $table->foreignId('staff_id')->constrained('users');
            $table->text('note')->nullable();

            $table->timestamps();
            // Bảng này là Log chứng từ, không bao giờ được phép sửa hay xóa mềm.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};