<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->string('degree_name');
            $table->string('board_name');
            $table->string('passing_year');
            $table->string('dept_name');
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('qualifications');
    }
};
