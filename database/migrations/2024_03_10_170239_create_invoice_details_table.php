<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id')->nullable();
            $table->integer('components_id')->nullable();
            $table->tinyInteger('invoice_category')->comment('1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash');
            $table->string('invoice_amount');
            $table->string('waiver_amount')->default(0);
            $table->string('payable_amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
};

// add column 

// ALTER TABLE invoice_details 
// ADD COLUMN invoice_category TINYINT(1) NULL COMMENT '1=Academic, 2=Canteen, 3=Dormitory, 4=Transport, 5=Canteen Cash' 
// AFTER components_id;

// update 
// UPDATE invoice_details 
// SET invoice_category = 1 
// WHERE invoice_category IS NULL;
