<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('website_setups', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->text('key');
            $table->text('type');
            $table->text('display_name');
            $table->text('value');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_setups');
    }
};
