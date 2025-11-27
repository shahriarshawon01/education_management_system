<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notice_boards', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->string('title',255);
            $table->string('slug',50)->nullable();
            $table->string('subject');
            $table->string('author',20);
            $table->string('notice_to',20);
            $table->text('notice');
            $table->string('file')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('notice_boards');
    }
};
