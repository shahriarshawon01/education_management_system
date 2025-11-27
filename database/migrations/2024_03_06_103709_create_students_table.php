<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('student_name_bn', 100)->nullable();
            $table->string('student_name_en', 100);
            $table->integer('merit_roll');
            $table->string('student_roll', 100);
            $table->integer('birth_certificate_no')->nullable();
            $table->integer('reg_number')->nullable();           
            $table->string('father_name_bn', 100)->nullable();
            $table->string('father_name_en', 100)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->integer('father_nid')->nullable();
            $table->string('father_profession', 20)->nullable();
            $table->string('mother_name_bn', 100)->nullable();
            $table->string('mother_name_en', 100)->nullable();
            $table->string('mother_phone', 20)->nullable();
            $table->integer('mother_nid')->nullable();
            $table->string('student_phone', 20)->nullable();
            $table->string('mother_profession', 20)->nullable();
            $table->integer('school_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('responsible_teacher_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('session_year_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('nationality', 50)->nullable();
            $table->integer('religion')->default(1)->comment('1=Islam, 2=Hindu, 3=Buddhist, 4=Christian');
            $table->date('admission_date')->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->integer('gender')->default(1)->comment('1=Male, 2=Female, 3=Other');
            $table->string('reference_name', 150)->nullable()->comment('Reference Name');
            $table->string('reference_mobile', 20)->nullable()->comment('Reference Mobile Number');
            $table->string('reference_address', 255)->nullable()->comment('Reference Address');
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('relation', 50)->nullable();
            $table->decimal('yearly_income', 10, 2)->nullable();
            $table->string('previous_institute')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive, 2=Dropout');
            $table->integer('print_status')->default(0)->comment('0=no_print,1=font_print, 2=back_print');
            $table->date('date_of_birth')->nullable();
            $table->string('photo')->nullable();
            $table->date('dropout_date')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->index('student_name_en');
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
