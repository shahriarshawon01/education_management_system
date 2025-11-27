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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('title');
			$table->string('email');
			$table->string('phone');
			$table->string('steb_number');
			$table->integer('status')->default(1);
            $table->integer('reg_number');
            $table->integer('institute_code');
            $table->string('address');
            $table->integer('emis_code');
            $table->string('logo')->nullable();
            $table->text('map_link')->nullable();
            $table->text('founder_info')->nullable();
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
        Schema::dropIfExists('schools');
    }
};
