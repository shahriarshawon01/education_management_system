<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('1=Student,2=Teacher,3=Staff');
            $table->unsignedBigInteger('member_id');
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->date('membership_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
};
