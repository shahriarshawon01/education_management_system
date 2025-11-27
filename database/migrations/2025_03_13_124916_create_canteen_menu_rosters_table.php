<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('canteen_menu_rosters', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->integer('month');
            $table->string('day', 10);
            $table->date('menu_date');
            $table->date('menu_start_date');
            $table->integer('week_number');
            $table->integer('menu_item_id')->nullable();
            $table->integer('meal_time_id')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();

            // Indexes
            $table->index('menu_date');
            $table->index('menu_item_id');
            $table->index('meal_time_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_menu_rosters');
    }
};
