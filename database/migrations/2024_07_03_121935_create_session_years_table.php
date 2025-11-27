<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('session_years', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('session_years');
    }
};
