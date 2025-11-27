<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->string('year');
            $table->timestamp('check_in');
            $table->timestamp('checkout')->nullable();
            $table->integer('tpsc_gen_user_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_attendance');
    }
};
