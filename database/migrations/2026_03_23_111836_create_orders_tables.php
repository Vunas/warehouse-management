<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('address_id')->nullable()->constrained('addresses'); // Có thể null nếu đến lấy tại kho
            
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['pending', 'paid', 'processing', 'shipping', 'completed', 'cancelled'])->default('pending'); // Thêm processing
            $table->text('customer_note')->nullable();
            
            $table->timestamp('order_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // ĐÃ BỎ UNIQUE(): Một đơn hàng có thể có nhiều lần thử thanh toán nếu lần trước thất bại
            $table->foreignId('order_id')->constrained('orders'); 
            
            $table->enum('payment_method', ['cash', 'vnpay', 'bank_transfer', 'momo']);
            $table->string('transaction_id')->nullable(); // Thêm mã giao dịch từ cổng thanh toán
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};