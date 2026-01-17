<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('storage_blocks', function (Blueprint $table) {
            $table->id(); 
            $table->integer('warehouse_id')->unsigned();
            $table->string('block_code'); 
            $table->integer('total_slots');
            $table->enum('status', ['available', 'rented', 'locked'])->default('available');
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('storage_blocks');
    }
};