<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->string('username',100);
            $table->string('email', 150);
            $table->string('password');
            $table->integer('role_id')->nullable();
            $table->integer('type')->comment('1=Admin,2=Teacher 3=Staff, 4=Parents, 5=Students, 6=Websites');
            $table->integer('school_id')->nullable();
            $table->string('image')->nullable();
            $table->integer('created_by')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('nid')->nullable();
            $table->integer('status')->default(1)->comment('1=Active, 0=InActive');
            $table->string('layout', 100)->default('vertical');
            $table->timestamps();

            $table->index('username');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::drop('users');
    }
}
