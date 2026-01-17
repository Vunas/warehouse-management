<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_blocks', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id')->unsigned();
            $table->foreignId('block_id')->constrained('storage_blocks'); 
            $table->integer('slots_committed')->default(0);
            $table->dateTime('rented_from');
            $table->dateTime('rented_to');
            $table->decimal('rental_price', 15, 2);
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_blocks');
    }
};
