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
        Schema::create('admission_selection_mappings', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->integer('admission_exam_id')->nullable();
            $table->time('starting_time')->nullable();
            $table->time('ending_time')->nullable();
            $table->integer('total_seat')->nullable();
            $table->integer('status')->nullable()->comment('fail=2,pass=1,exam expel=3');
            $table->softDeletes();
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
        Schema::dropIfExists('admission_selection_mappings');
    }
};
