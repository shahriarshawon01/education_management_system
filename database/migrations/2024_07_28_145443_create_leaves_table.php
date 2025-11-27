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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('type');
            $table->integer('category_id');
            $table->date('apply_date');
            $table->string('apply_to');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('no_of_days');
            $table->string('note');
            $table->string('file')->nullable();
            $table->integer('approve_days')->nullable();
            $table->integer('status')->default(1)->comment('1=pending,2=approve,3=cancel');
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
        Schema::dropIfExists('leaves');
    }
};
