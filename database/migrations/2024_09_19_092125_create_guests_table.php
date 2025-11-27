<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name', 50);
            $table->string('email', 100);
            $table->integer('guest_id');
            $table->string('phone', 20)->nullable();
            $table->bigInteger('nid')->nullable();
            $table->string('guest_institute', 50);
            $table->string('guest_designation', 50);
            $table->string('guest_department', 50);
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();

            $table->index('guest_name');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('guests');
    }
};
