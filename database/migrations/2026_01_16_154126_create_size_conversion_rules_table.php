<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('size_conversion_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rule_name');
            $table->float('max_length');
            $table->float('max_width');
            $table->float('max_height');
            $table->integer('slot_cost');
            $table->integer('priority_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('size_conversion_rules');
    }
};