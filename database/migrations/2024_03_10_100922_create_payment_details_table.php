<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->integer('payments_id')->nullable();
            $table->integer('component_id')->nullable();
            $table->tinyInteger('invoice_category')->comment('1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash');
            $table->tinyInteger('paid_by_type')->comment('1=Student, 2=Employee, 3=Guest');
            $table->integer('amount');
            $table->integer('waiver')->default(0);
            $table->integer('payed');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_details');
    }
};

// add column 
// ALTER TABLE
//   payment_details
// ADD
//   COLUMN invoice_category TINYINT(1) NULL COMMENT '1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash'
// AFTER
//   component_id,
// ADD
//   COLUMN paid_by_type TINYINT(1) NULL COMMENT '1=Student, 2=Employee, 3=Guest'
// AFTER
//   invoice_category,





