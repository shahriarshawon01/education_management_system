<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('assign_canteens', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->tinyInteger('consumer_type')
                ->comment('1=Student, 2=Employee, 3=Guest');
            $table->integer('consumer_id')->nullable();
            $table->date('assign_date')->nullable();
            $table->text('comments')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assign_canteens');
    }
};
