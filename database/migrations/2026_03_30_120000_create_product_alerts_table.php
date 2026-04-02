<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->cascadeOnDelete(); 
            $table->integer('stock_threshold')->default(10);
            $table->integer('expiry_threshold_days')->default(90);  
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_stock_alert_at')->nullable();
            $table->timestamp('last_expiry_alert_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_alerts');
    }
};