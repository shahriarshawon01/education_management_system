<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('school_id')->nullable();
            $table->string('department_name');
            $table->string('department_code');
            $table->integer('class_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('departments');
    }
};
