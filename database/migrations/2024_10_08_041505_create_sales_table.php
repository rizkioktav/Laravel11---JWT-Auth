<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_no');
            $table->string('customer_no');
            $table->string('product');
            $table->double('qty');
            $table->double('price');
            $table->double('discount')->nullable();;
            $table->double('tax')->nullable();;
            $table->double('total');
            $table->timestamps();
            
            $table->foreign('customer_no')->references('customer_no')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
