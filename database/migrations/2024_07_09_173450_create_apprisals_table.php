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
        Schema::create('apprisals', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('apprisal_type')->comment('1=student,2=teacher,3=staff');
            $table->integer('class_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('template_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->float('converted');
            $table->float('total_score');
            $table->float('cgpa');
            $table->string('get_mark');
            $table->string('remark');
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
        Schema::dropIfExists('apprisals');
    }
};
