<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->string('guardian_name',100)->nullable();
            $table->string('guardian_phone',20)->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('student_id')->nullable();
            $table->string('relation',50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('guardians');
    }
};
