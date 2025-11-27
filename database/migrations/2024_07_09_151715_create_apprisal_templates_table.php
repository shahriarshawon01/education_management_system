<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apprisal_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->string('apprisal_for');
            $table->string('kra');
            $table->string('wheightage');
            $table->float('wheightage_total')->default(100);
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apprisal_templates');
    }
};
