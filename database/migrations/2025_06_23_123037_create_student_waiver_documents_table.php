<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('student_waiver_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->tinyInteger('reason')->comment('1 = Debtor Member, 2 = Staff, 3 = HEM Staff, 4 = Merit Position, 5 = Child Merit, 6 = General');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID from related table based on reason');

            // Only for debtor Member & HEM Staff (reason = 1,3)
            $table->string('reference_code')->nullable();
            $table->string('reference_name')->nullable();
            $table->integer('reference_nid')->nullable();
            $table->string('reference_phone', 14)->nullable();

            $table->string('file');
            $table->string('remarks');
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_waiver_documents');
    }
};
