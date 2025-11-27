<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up()
    {
        Schema::create('book_issues_details', function (Blueprint $table) {
            $table->id();
            $table->integer('issue_id')->nullable();
            $table->integer('book_accession_id')->nullable();
            $table->date('issue_date');
            $table->date('expected_return')->nullable();
            $table->integer('status')->default(1)->comment('1=Issued,2=Returned');
            $table->date('return_date')->nullable();
            $table->integer('school_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_issues_details');
    }
};
