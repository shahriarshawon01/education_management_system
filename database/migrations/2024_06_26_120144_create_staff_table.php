<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('user_id')->nullable();
            $table->integer('staff_bio_id');
            $table->string('father_name');
            $table->string('mother_name');
            $table->date('joining_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->date('terminate_date')->nullable();
            $table->string('employee_id')->nullable();
            $table->integer('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->integer('staff_type')->comment('1=Staff, 2=Support Staff');
            $table->integer('marital_status')->default(1)->comment('1=Married, 2=Unmarried');
            $table->integer('religion')->default(1)->comment('1=Islam, 2=Hindu, 3=Buddhist, 4=Christian');
            $table->integer('designation_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('nid')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=InActive, 2=Resigned, 3=Terminated');
            $table->string('photo')->nullable();
            $table->integer('school_id')->nullable();
            $table->text('job_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
};
