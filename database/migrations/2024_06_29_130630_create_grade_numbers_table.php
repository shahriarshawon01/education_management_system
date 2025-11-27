<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grade_numbers', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('subject_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('exam_id')->nullable();
            $table->integer('exam_type_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->float('grade_number');
            $table->string('mark_component');
            $table->string('exam_mark')->nullable();
            $table->string('convert_num')->nullable();
            $table->string('total_mark')->nullable();
            $table->string('pass_mark');
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grade_numbers');
    }
};
