<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->integer('user_id')->nullable();
            $table->integer('teacher_bio_id');
            $table->string('father_name',100);
            $table->string('mother_name',100);
            $table->date('joining_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->date('terminate_date')->nullable();
            $table->string('employee_id',20)->nullable();
            $table->integer('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->integer('marital_status')->default(1)->comment('1=Married, 2=Unmarried');
            $table->integer('religion')->default(1)->comment('1=Islam, 2=Hindu, 3=Buddhist, 4=Christian');
            $table->integer('designation_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('nid')->nullable();
            $table->integer('job_category')->default(1)->comment('1=Tech, 2=Non-Tech');
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive, 2=Resigned, 3=Terminated');
            $table->string('photo')->nullable();
            $table->text('job_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teachers');
    }
};
