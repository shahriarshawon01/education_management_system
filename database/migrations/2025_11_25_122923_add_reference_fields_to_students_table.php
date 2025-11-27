<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('reference_name', 150)->nullable()->comment('Reference Name');
            $table->string('reference_mobile', 20)->nullable()->comment('Reference Mobile Number');
            $table->string('reference_address', 255)->nullable()->comment('Reference Address');
            $table->tinyInteger('is_hem_member')->default(0)->comment('1=yes, 0=no');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'reference_name',
                'reference_mobile',
                'reference_address',
                'is_hem_member'
            ]);
        });
    }
};
