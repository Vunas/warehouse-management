<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('location', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('parent_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name', 150);
            $table->enum('type', ['zone','shelf','pallet','bin','rack']);
            $table->boolean('is_store')->default(true);

            $table->integer('picking_priority')->default(0);

            $table->timestamps();
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('batch_id')->constrained('product_batches');

            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);

            $table->timestamps();

            $table->unique(['product_id', 'location_id', 'batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('warehouses');
    }
};
