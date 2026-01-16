<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_rents', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->integer('slot_quantity');
            $table->decimal('price_per_slot', 15, 2);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('warehouse_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_rents');
    }
};