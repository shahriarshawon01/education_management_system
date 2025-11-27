<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('staff_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('type')->comment('1 == present, 2 == permanent');
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('union')->nullable();
            $table->integer('upazila')->nullable();
            $table->integer('district')->nullable();
            $table->integer('division')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
