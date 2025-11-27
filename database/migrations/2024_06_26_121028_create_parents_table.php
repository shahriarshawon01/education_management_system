<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->string('phone')->nullable();
            $table->string('profession')->nullable();
            $table->integer('income')->nullable();
            $table->string('image')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parents');
    }
};
