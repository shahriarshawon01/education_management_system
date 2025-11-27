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
        Schema::create('admission_selection_subject_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('admission_subject_id')->nullable();
            // $table->integer('admission_exam_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->string('marks')->nullable();
            $table->integer('pass_marks')->nullable();
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
        Schema::dropIfExists('admission_selection_subject_mappings');
    }
};
