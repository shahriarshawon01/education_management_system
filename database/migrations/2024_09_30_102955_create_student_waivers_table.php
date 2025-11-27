<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_waivers', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('component_id')->nullable();
            $table->tinyInteger('type')->comment('1=percentage, 2=fixed');
            $table->integer('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_waivers');
    }
};
