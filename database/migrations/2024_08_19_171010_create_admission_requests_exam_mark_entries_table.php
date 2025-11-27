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
        Schema::create('admission_requests_exam_mark_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('applicant_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('exam_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('mark')->nullable();
            $table->integer('status')->nullable()->comment('0 default,1 admitted');
            $table->softDeletes();
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
        Schema::dropIfExists('admission_requests_exam_mark_entries');
    }
};
