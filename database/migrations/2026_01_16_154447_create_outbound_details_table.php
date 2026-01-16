<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outbound_id')->constrained('outbound_tickets')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_details');
    }
};