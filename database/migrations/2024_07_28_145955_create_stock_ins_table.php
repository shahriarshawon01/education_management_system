<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('product_code');
            $table->date('purchase_date');
            $table->date('sale_date')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->integer('purchase_quantity');
            $table->integer('sale_quantity')->nullable();;
            $table->integer('school_id')->nullable();;
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_ins');
    }
};
