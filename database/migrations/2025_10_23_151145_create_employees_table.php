<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('sort_order')->default(0);
            $table->string('name', 100);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('father_name', 100);
            $table->string('mother_name', 100);
            $table->date('joining_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->date('terminate_date')->nullable();
            $table->string('employee_id', 20)->nullable();
            $table->unsignedTinyInteger('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->unsignedTinyInteger('marital_status')->default(1)->comment('1=Married, 2=Unmarried');
            $table->unsignedTinyInteger('religion')->default(1)->comment('1=Islam, 2=Hindu, 3=Buddhist, 4=Christian');
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->integer('nid')->nullable();
            $table->unsignedTinyInteger('employee_type')->comment('1=Teacher, 2=Staff, 3=Support Staff');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=Active, 0=Inactive, 2=Resigned, 3=Terminated');
            $table->string('photo')->nullable();
            $table->text('job_comments')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
