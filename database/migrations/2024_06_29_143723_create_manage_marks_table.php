<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manage_marks', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('subject_id')->nullable();
            $table->integer('grade_number_id')->nullable();
            $table->integer('exam_id')->nullable();
            $table->integer('exam_type_id')->nullable();
            $table->string('exam_mark_data');

            $table->string('component_name');
            $table->string('subject_total_mark');
            $table->string('result_mark');
            $table->string('cgpa')->nullable();
            $table->string('grade')->nullable();
            $table->string('result_status');
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manage_marks');
    }
};
