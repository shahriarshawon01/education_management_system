<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('assign_dormitories', function (Blueprint $table) {
            $table->id();

            // User info
            $table->unsignedBigInteger('dormitory_user_id')
                ->comment('FK to student or employee depending on type');
            $table->tinyInteger('dormitory_user_type')
                ->comment('1=Student, 2=Employee, 3=Guest');

            // Dormitory info
            $table->unsignedBigInteger('dormitory_id')
                ->nullable()
                ->comment('FK to dormitories table');
            $table->integer('discount')->default(0);
            $table->integer('net_amount')->default(0);
            $table->integer('payable_amount')->default(0);

            $table->string('floor_number')->nullable();
            $table->string('room_number');
            $table->string('seat_number');

            $table->date('arrive_date');

            $table->integer('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('school_id')->nullable()->comment('FK to schools table');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index('dormitory_user_id');
            $table->index('dormitory_user_type');
            $table->index('dormitory_id');
            $table->unique(
                ['dormitory_user_id', 'dormitory_user_type', 'school_id'],
                'unique_user_in_school'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('assign_dormitories');
    }
};
