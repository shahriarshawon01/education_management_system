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
        Schema::create('admission_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('applicant_id');
            $table->string('applicant_name_en');
            $table->string('applicant_name_bn')->nullable();
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('blood_group');
            $table->string('religion');
            $table->string('nationality')->nullable();
            $table->string('applicant_email')->nullable();
            $table->string('applicant_phone');
            $table->decimal('weight',5,2)->nullable();
            $table->decimal('height',5,2)->nullable();
            $table->integer('class_id');
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('session_id');
            $table->string('father_name_en');
            $table->string('father_name_bn')->nullable();
            $table->string('father_phone');
            $table->string('father_profession')->nullable();
            $table->decimal('yearly_income', 10,2)->nullable();
            $table->string('mother_name_en');
            $table->string('mother_name_bn')->nullable();
            $table->string('mother_phone');
            $table->string('mother_profession')->nullable();
            $table->string('quota')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('birth_nid_certificate')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->integer('short_list')->default(0);
            $table->integer('final_enroll_status')->nullable();
            $table->integer('merit_waiting_status')->default(0)->comment('0=pending; 1=merit; 2=waiting');
            $table->integer('admission_payment_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void+
     */
    public function down()
    {
        Schema::dropIfExists('admission_requests');
    }
};
