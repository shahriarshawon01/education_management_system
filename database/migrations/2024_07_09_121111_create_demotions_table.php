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
        Schema::create('demotions', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('student_roll');
            $table->integer('session_year_id')->nullable();
            $table->integer('demotion_class_id')->nullable();
            $table->integer('demotion_session_id')->nullable();
            $table->integer('demotion_section_id')->nullable();
            $table->integer('demotion_department_id')->nullable();
            $table->string('demotion_roll');
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
        Schema::dropIfExists('demotions');
    }
};
