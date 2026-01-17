<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type_code')->unique(); 
            $table->boolean('rentable')->default(true);
            $table->boolean('single_contract')->default(false); 
            $table->integer('priority_rule')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_types');
    }
};