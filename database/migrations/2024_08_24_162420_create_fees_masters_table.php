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
        Schema::create('fees_masters', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('amount');
            $table->string('date');
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('school_id')->nullable();
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
        Schema::dropIfExists('fees_masters');
    }
};
