<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id')->unsigned();
            $table->dateTime('expected_date');
            $table->enum('status', ['pending', 'approved', 'received', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_tickets');
    }
};