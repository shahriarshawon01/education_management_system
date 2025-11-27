<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_qualifications', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('reg_number')->nullable();
            $table->integer('roll_number')->nullable();
            $table->string('exam_name')->nullable();
            $table->string('board_name')->nullable();
            $table->string('group')->nullable();
            $table->string('passing_year')->nullable();
            $table->decimal('gpa', 5, 2)->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('student_qualifications');
    }
};
