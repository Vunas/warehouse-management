<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('contract_code')->unique();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('penalty_markup', 5, 2)->default(0.00);
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};