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
        Schema::create('applicant_guardians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admission_requests_id');
            $table->integer('user_id');
            $table->string('guardian_name');
            $table->string('guardian_relation');
            $table->string('guardian_phone');
            $table->string('guardian_address')->nullable();
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
        Schema::dropIfExists('applicant_guardians');
    }
};
