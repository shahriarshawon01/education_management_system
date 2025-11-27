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
        Schema::create('admission_exams', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('building_id')->nullable();
            $table->integer('floor_id')->nullable();
            $table->integer('room_id')->nullable();
            $table->string('venue')->nullable();
            $table->string('requirements')->nullable();
            $table->date('exam_date')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
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
        Schema::dropIfExists('admission_exams');
    }
};
