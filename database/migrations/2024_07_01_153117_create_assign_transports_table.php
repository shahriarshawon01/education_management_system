<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('assign_transports', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->tinyInteger('transport_user_type')
                ->comment('1=Student, 2=Employee, 3=Guest');
            $table->unsignedBigInteger('transport_user_id')->nullable();

            $table->integer('transport_id')->nullable();
            $table->integer('discount')->default(0);
            $table->integer('net_amount')->default(0);
            $table->integer('payable_amount')->default(0)->comment('Net amount after discount');
            $table->string('stoppage')->nullable();
            $table->date('assign_date')->nullable();
            $table->text('comments')->nullable();
            $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assign_transports');
    }
};
