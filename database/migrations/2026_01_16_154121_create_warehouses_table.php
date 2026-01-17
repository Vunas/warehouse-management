<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned();
            $table->string('name');
            $table->integer('total_blocks')->default(0);
            $table->integer('total_slots')->default(0);
            $table->enum('status', ['active', 'maintenance', 'locked'])->default('active');
            $table->integer('paired_warehouse_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('warehouse_types');
            $table->foreign('paired_warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};