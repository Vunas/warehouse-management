<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('address', 255)->nullable();
            $table->boolean('is_active')->default(true); // Thêm trạng thái hoạt động
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier_products', function (Blueprint $table) {
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->primary(['supplier_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('suppliers');
    }
};