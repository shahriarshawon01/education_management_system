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
        Schema::create('applicant_present_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_requests_id');
            $table->integer('user_id');
            $table->integer('division')->nullable();
            $table->integer('district')->nullable();
            $table->integer('upazila')->nullable();
            $table->integer('union')->nullable();
            $table->string('post_office');
            $table->string('village');
            $table->foreign('admission_requests_id')->references('id')->on('admission_requests')->onDelete('cascade');
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
        Schema::dropIfExists('applicant_present_addresses');
    }
};
