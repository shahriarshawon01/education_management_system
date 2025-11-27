<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->string('type_name');
            $table->integer('exam_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('file')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examinations');
    }
};
