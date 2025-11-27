<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('subject_mark');
            $table->integer('subject_group_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('subject_code');
            $table->integer('is_optional')->default(1)->comment('1=Compulsory, 2=Optional');         
            $table->integer('subject_type')->default(1)->comment('1=Tech, 2=Non-Tech');
            $table->integer('session_year_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subjects');
    }
};
