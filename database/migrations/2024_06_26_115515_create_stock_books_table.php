<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_books', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('book_accession_id');
            $table->date('stock_date');
            $table->string('net_price')->nullable();
            $table->string('purchase_price')->nullable();
            $table->string('discount')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('available_quantity')->nullable();
            $table->integer('status')->default(1)->comment("0=inactive,1=active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_books');
    }
};
