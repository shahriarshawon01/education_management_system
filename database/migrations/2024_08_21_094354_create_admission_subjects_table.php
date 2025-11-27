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
        Schema::create('admission_subjects', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            // $table->integer('is_seat_fixed')->default(0);
            // $table->integer('seat_allowance')->nullable();
            $table->integer('max_mark')->nullable();
            $table->integer('pass_mark')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('admission_subjects');
    }
};
