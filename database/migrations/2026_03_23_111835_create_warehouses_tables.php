<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Warehouses
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('location', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Locations (thay thế zone/shelf)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('parent_id')->nullable()->constrained('locations');
            $table->string('name', 150);
            $table->enum('type', ['zone','shelf','pallet','bin','rack']);
            $table->boolean('is_store')->default(true);
            $table->timestamps();
        });

        // Inventory
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('location_id')->constrained('locations');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'location_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('warehouses');
    }
};
