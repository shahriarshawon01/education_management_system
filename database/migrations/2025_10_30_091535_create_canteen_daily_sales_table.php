<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('canteen_daily_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('school_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->date('date');
            $table->tinyInteger('month')->nullable()
                ->comment('1=January ... 12=December');

            $table->unsignedBigInteger('meal_time_id')->nullable()
                ->comment('Meal time ID (1=Breakfast, 2=Lunch, 3=Dinner)');
            $table->json('menu_id')->nullable()
                ->comment('Selected menu item id"[28,4,6]"');

            $table->decimal('amount', 10, 2)->default(0)
                ->comment('Total amount for this meal sale');

            $table->boolean('is_canteen_member')->default(0)
                ->comment('0=Non-member, 1=Member');
            $table->integer('member_id')->nullable();
            $table->tinyInteger('member_type')->nullable()
                ->comment('1=Student, 2=Employee, 3=Guest');

            $table->tinyInteger('sale_type')->default(1)
                ->comment('1=Cash Sale, 2=Invoice Sale');
            $table->boolean('invoice_generate')->default(0)
                ->comment('0=Not Generated, 1=Generated');
            $table->string('voucher_number', 50)->nullable();

            $table->integer('status')->default(1)
                ->comment('1=Active, 0=Inactive');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('date');
            $table->index('meal_time_id');
            $table->index('member_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_daily_sales');
    }
};
