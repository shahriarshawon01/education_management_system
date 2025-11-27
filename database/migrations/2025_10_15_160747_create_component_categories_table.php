<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up()
    {
        Schema::create('component_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('school_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('component_categories');
    }
};
