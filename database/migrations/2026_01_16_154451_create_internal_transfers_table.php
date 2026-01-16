<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_transfers', function (Blueprint $table) {
            $table->id();
            $table->integer('from_warehouse_id')->unsigned();
            $table->integer('to_warehouse_id')->unsigned();
            $table->string('trigger_reason')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'completed'])->default('pending');
            $table->timestamps();

            $table->foreign('from_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_transfers');
    }
};