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
        Schema::create('transport_manages', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('route_id')->nullable();
            $table->string('transport_name');
            $table->string('transport_no');
            $table->string('licence_no');
            $table->string('register_date');
            $table->string('running_date')->nullable();
            $table->string('color')->nullable();
            $table->string('total_seat')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('transport_manages');
    }
};
