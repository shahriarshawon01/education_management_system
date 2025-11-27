<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->date('date')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('duration');
            $table->integer('teacher_id')->nullable();
            $table->integer('meeting_id')->nullable();
            $table->string('meeting_link')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('school_id')->nullable();
            $table->string('file')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('live_classes');
    }
};
