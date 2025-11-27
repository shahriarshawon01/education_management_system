<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->nullable();
            $table->timestamp('check_in');
            $table->timestamp('checkout')->nullable();
            $table->integer('tpsc_generate_user_id');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_attendance');
    }
};
