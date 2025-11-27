<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approve_leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('leave_id')->nullable();
            $table->date('leave_from');
            $table->date('leave_to');
            $table->integer('approve_days');
            $table->string('leave_type')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approve_leaves');
    }
};
