<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('manage_dormitories', function (Blueprint $table) {
            $table->id();
            $table->string('dormitory_name');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->integer('dormitory_no')->nullable();
            $table->integer('total_floor');
            $table->integer('total_room');
            $table->integer('total_seat');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('manage_dormitories');
    }
};
