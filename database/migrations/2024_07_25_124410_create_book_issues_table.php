<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('type')->comment('1=Student,2=Teacher,2=Staff,4=Library Member');
            $table->integer('member_id')->nullable();
            $table->integer('total_books');
            $table->date('issue_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('book_issues');
    }
};
