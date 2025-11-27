<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_history', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->timestamp('time_history');
            $table->tinyInteger('user_type')->comment('1=teacher, 2=staff, 3=student');
            $table->integer('tpsc_user_id');
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_history');
    }
};
