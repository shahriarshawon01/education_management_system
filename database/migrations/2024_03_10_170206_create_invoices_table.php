<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->tinyInteger('invoice_type')->comment('1=Student, 2=Employee, 3=Guest');
            $table->tinyInteger('invoice_category')->comment('1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash');
            $table->integer('invoice_type_id')->nullable();
            $table->tinyInteger('is_advance')->comment('0=regular, 1=advance');
            $table->string('invoice_code');
            $table->integer('total_amount');
            $table->integer('total_waiver')->default(0);
            $table->integer('total_payable');
            $table->date('date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('month')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};

// modify 

// ALTER TABLE invoices 
// MODIFY COLUMN invoice_type TINYINT(1) COMMENT '1=Student, 2=Employee, 3=Guest' NOT NULL;

// ALTER TABLE invoices 
// MODIFY COLUMN is_advance TINYINT(1) DEFAULT 0 COMMENT '0=Regular, 1=Advance' NOT NULL;


// ALTER TABLE invoices 
// ADD COLUMN invoice_category TINYINT(1) NULL COMMENT '1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash' 
// AFTER invoice_type;


// ALTER TABLE invoices 
// ADD INDEX idx_invoice_type (invoice_type, invoice_type_id),
// ADD INDEX idx_invoice_category (invoice_category),
// ADD INDEX idx_school_id (school_id);


// UPDATE invoices 
// SET invoice_type = CASE 
//     WHEN invoice_type = 'student' THEN 1
//     WHEN invoice_type IN ('teacher', 'staff') THEN 2
//     WHEN invoice_type = 'guest' THEN 3
//     ELSE 0
// END;

// UPDATE invoices 
// SET is_advance = CASE 
//     WHEN is_advance = 'advance' THEN 1
//     ELSE 0
// END;

// UPDATE invoices 
// SET invoice_category = 1 
// WHERE invoice_category IS NULL;




