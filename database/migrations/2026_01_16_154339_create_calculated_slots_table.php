<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculated_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbound_detail_id')->constrained('inbound_details')->onDelete('cascade');
            $table->integer('rule_id')->unsigned();
            $table->float('final_length');
            $table->float('final_width');
            $table->float('final_height');
            $table->integer('final_slot_cost');
            $table->boolean('is_violation')->default(false);
            $table->timestamps();

            $table->foreign('rule_id')->references('id')->on('size_conversion_rules');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculated_slots');
    }
};