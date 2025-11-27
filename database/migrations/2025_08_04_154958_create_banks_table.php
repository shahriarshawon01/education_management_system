<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('branch');
            $table->integer('routing_number');
            $table->string('swift_code')->nullable();
            $table->string('address')->nullable();
            $table->integer('status')->default(1)->comment('1=Active,0=Inactive');
            $table->integer('school_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('banks');
    }
};

// CREATE TABLE `banks` (
//     `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     `name` VARCHAR(255) NOT NULL,
//     `account_name` VARCHAR(255) NOT NULL,
//     `account_number` VARCHAR(255) NOT NULL,
//     `branch` VARCHAR(255) NOT NULL,
//     `routing_number` INT NOT NULL,
//     `swift_code` VARCHAR(255) DEFAULT NULL,
//     `address` VARCHAR(255) DEFAULT NULL,
//     `status` INT DEFAULT 1 COMMENT '1=Active,0=Inactive',
//     `school_id` INT DEFAULT NULL,
//     `created_by` INT DEFAULT NULL,
//     `updated_by` INT DEFAULT NULL,
//     `created_at` TIMESTAMP NULL DEFAULT NULL,
//     `updated_at` TIMESTAMP NULL DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// ALTER TABLE `payments` ADD `bank_id` INT(11) NULL DEFAULT NULL AFTER `class_id`;


