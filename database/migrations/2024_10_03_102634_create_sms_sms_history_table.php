<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sms_history', function (Blueprint $table) {
            $table->id();
            $table->text('sms')->nullable();
            $table->string('mobile')->nullable();
            $table->string('request_type')->nullable();
            $table->string('message_type')->nullable();
            $table->integer('server_code')->nullable();
            $table->string('server_response')->nullable();
            $table->string('campaign_title')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_history');
    }
};
