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
        Schema::create('admission_payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('applicant_id');
            $table->integer('school_id');
            $table->integer('class_id');
            $table->integer('session_year_id');
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('fees_type_id')->nullable();
            $table->integer('fees_master_id');
            $table->integer('payment_method');
            $table->integer('total_amount');
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('admission_payment_statuses');
    }
};
