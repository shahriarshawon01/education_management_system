<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Rename old table first to keep the data safe
        if (Schema::hasTable('addresses')) {
            Schema::rename('addresses', 'addresses_old');
        }

        // 2️⃣ Create new 'addresses' table (polymorphic structure)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addressable_id');
            $table->integer('addressable_type')->comment('1=Student, 2=Employee, 3=Parent');
            $table->integer('school_id')->nullable();
            $table->integer('type')->comment('1=present, 2=permanent');
            $table->string('village')->nullable();
            $table->string('post_office')->nullable();
            $table->string('union')->nullable();
            $table->integer('upazila')->nullable();
            $table->integer('district')->nullable();
            $table->integer('division')->nullable();
            $table->timestamps();

            $table->index(['addressable_id', 'addressable_type']);
        });

        // 3️⃣ Copy and transform data from the old table
        if (Schema::hasTable('addresses_old')) {
            DB::table('addresses_old')->orderBy('id')->chunkById(500, function ($rows) {
                $insertData = [];

                foreach ($rows as $row) {
                    $addressableType = null;
                    $addressableId = null;

                    if (!empty($row->student_id)) {
                        $addressableType = 1;
                        $addressableId = $row->student_id;
                    } elseif (!empty($row->teacher_id)) {
                        $addressableType = 2;
                        $addressableId = $row->teacher_id;
                    } elseif (!empty($row->staff_id)) {
                        $addressableType = 3;
                        $addressableId = $row->staff_id;
                    } elseif (!empty($row->parent_id)) {
                        $addressableType = 5;
                        $addressableId = $row->parent_id;
                    }

                    if ($addressableType && $addressableId) {
                        $insertData[] = [
                            'addressable_id' => $addressableId,
                            'addressable_type' => $addressableType,
                            'school_id' => $row->school_id,
                            'type' => $row->type,
                            'village' => $row->village,
                            'post_office' => $row->post_office,
                            'union' => $row->union,
                            'upazila' => $row->upazila,
                            'district' => $row->district,
                            'division' => $row->division,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ];
                    }
                }

                if (!empty($insertData)) {
                    DB::table('addresses')->insert($insertData);
                }
            });
        }
    }

    public function down(): void
    {
        // Rollback safely: drop new table and restore old one
        if (Schema::hasTable('addresses')) {
            Schema::dropIfExists('addresses');
        }

        if (Schema::hasTable('addresses_old')) {
            Schema::rename('addresses_old', 'addresses');
        }
    }
};


// 1. php artisan make:migration create_addresses_new_table
// 2. run this cmd => RENAME TABLE addresses TO addresses_old;
// for department and designation 
// RENAME TABLE work_departments TO employee_departments
// RENAME TABLE designations TO employee_designations
