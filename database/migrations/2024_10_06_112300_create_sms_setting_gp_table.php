<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_setting_gp', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->string('password');
            $table->integer('apicode');
            $table->string('countrycode', 10);
            $table->string('cli', 30);
            $table->integer('messagetype');
            $table->integer('messageid');
            $table->integer('api_status');
            $table->string('test_number', 30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_setting_gp');
    }
};
