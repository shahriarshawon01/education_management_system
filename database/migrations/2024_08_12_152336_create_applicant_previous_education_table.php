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
        Schema::create('applicant_previous_education', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_requests_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('board_name')->nullable();
            $table->string('exam_name')->nullable();
            $table->integer('reg_no')->nullable();
            $table->integer('roll_no')->nullable();
            $table->string('group')->nullable();
            $table->string('passing_year')->nullable();
            $table->decimal('gpa',5,2)->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->foreign('admission_requests_id')->references('id')->on('admission_requests')->onDelete('cascade');
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
        Schema::dropIfExists('applicant_previous_education');
    }
};
