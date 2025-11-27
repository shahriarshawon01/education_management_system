<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->integer('product_code')->nullable();
            $table->integer('stock_id')->nullable();
            $table->enum('customer_type', ['student', 'teacher', 'staff']);
            $table->string('customer_id')->nullable();
            $table->date('sale_date');
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->integer('quantity');
            $table->integer('school_id');
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('stock_outs');
    }
};
