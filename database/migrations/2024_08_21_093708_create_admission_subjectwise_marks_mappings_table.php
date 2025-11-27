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
        Schema::create('admission_subjectwise_marks_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('marks_entry_id')->nullable();
            $table->integer('applicant_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('subject_id')->nullable();
            $table->integer('subject_marks')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('admission_subjectwise_marks_mappings');
    }
};
