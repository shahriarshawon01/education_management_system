<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('invoice_id')->nullable();

            $table->tinyInteger('invoice_category')->comment('1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash');
            $table->tinyInteger('paid_by_type')->comment('1=Student, 2=Employee, 3=Guest');
            $table->integer('paid_by_id')->nullable();

            $table->integer('session_year_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->integer('bank_id')->nullable();
            $table->integer('total_payed');
            $table->integer('payment_type')->comment('1=Cash, 2=Bank');
            $table->date('date');
            $table->string('voucher_number', 50)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};


// add column 
// ALTER TABLE
//   payments
// ADD
//   COLUMN invoice_category TINYINT(1) NULL COMMENT '1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash'
// AFTER
//   invoice_id,
// ADD
//   COLUMN paid_by_type TINYINT(1) NULL COMMENT '1=Student, 2=Employee, 3=Guest'
// AFTER
//   invoice_category,
//   CHANGE COLUMN student_id paid_by_id INT NULL COMMENT 'payer reference (student, employee, or guest)';



// UPDATE payments 
// SET invoice_category = 1,  -- Academic
//     paid_by_type = 1,      -- Student
//     paid_by_id = student_id
// WHERE invoice_category IS NULL;

// UPDATE payment_details 
// SET invoice_category = 1, 
//     paid_by_type = 1 
// WHERE invoice_category IS NULL;