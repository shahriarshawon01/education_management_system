<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->tinyInteger('component_type')->comment('1=Academic, 2=Canteen, 3=Dormitory, 4=Transport');
            $table->string('name');
            $table->integer('value')->default(0);
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('components');
    }
};
