<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_setting', function (Blueprint $table) {
            $table->id();
            $table->string('api_key');
            $table->string('api_secret');
            $table->string('request_type');
            $table->string('message_type');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_settings');
    }
};
