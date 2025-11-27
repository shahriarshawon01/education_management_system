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
        Schema::create('book_accessions', function (Blueprint $table) {
            $table->id();
            $table->string('book_title',100);
            $table->integer('school_id')->nullable();
            $table->integer('author')->nullable();
            $table->integer('publisher')->nullable();
            $table->integer('edition')->nullable();
            $table->integer('language')->nullable();
            $table->string('cell_number')->nullable();
            $table->string('phone',20)->nullable();
            $table->string('isbn',50)->nullable();
            $table->string('soft_copy',150)->nullable();
            $table->integer('status')->default(1)->comment("0=inactive,1=active");
            $table->integer('book_type')->default(1)->comment("1=Academic,2=Non Academic");
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
        Schema::dropIfExists('book_accessions');
    }
};
