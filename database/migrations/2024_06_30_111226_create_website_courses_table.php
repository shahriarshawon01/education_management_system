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
        Schema::create('website_courses', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('student_name');
            $table->string('roll_no');
            $table->string('reg_no');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('religion');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image')->nullable();
            $table->integer('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
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
        Schema::dropIfExists('website_courses');
    }
};
